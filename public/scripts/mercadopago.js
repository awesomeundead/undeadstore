const mp = new MercadoPago(MP_PUBLIC_KEY, { locale: 'pt-BR' });

const bricksBuilder = mp.bricks();

const renderPaymentBrick = async (bricksBuilder) =>
{
    const settings =
    {
        initialization:
        {
            amount: MP_AMOUNT,
            payer: MP_PAYER
        },
        customization:
        {
            paymentMethods: MP_PAYMENT_METHODS
        },
        callbacks: 
        {
            onReady: () =>
            {
                
            },
            onSubmit: ({ selectedPaymentMethod, formData }) =>
            {
                return new Promise((resolve, reject) =>
                {
                    fetch('/payment/process?id=' + PURCHASE_ID,
                    {
                        method: 'POST',
                        headers:
                        {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(formData)
                    })
                    .then((response) => response.json())
                    .then((response) =>
                    {
                        if (!response.id)
                        {
                            throw new Error(response.message);
                        } 

                        renderStatusScreenBrick(bricksBuilder, response);
                        resolve();
                    })
                    .catch((error) =>
                    {
                        if (error.message == 'internal_error')
                        {
                            document.getElementById('mercadopago_alert').hidden = false;
                        }

                        reject();
                    });
                });
            },
            onError: (error) =>
            {
                console.error(error);
            }
        }
    };

    window.paymentBrickController = await bricksBuilder.create('payment', 'paymentBrick_container', settings);
};

const renderStatusScreenBrick = async (bricksBuilder, response) =>
{
    const settings =
    {
        initialization:
        {
            paymentId: response.id
        },
        callbacks:
        {
            onReady: () =>
            {
                document.getElementById('paymentBrick_container').hidden = true;
            },
            onError: (error) =>
            {
                console.error(error);
            },
        },
        customization:
        {
            backUrls: MP_BACK_URLS,
            visual:
            {
                showExternalReference: true
            }
        }
    };
    window.statusScreenBrickController = await bricksBuilder.create( 'statusScreen', 'statusScreenBrick_container', settings);  
};

if (MP_PAYMENT_ID != undefined)
{
    renderStatusScreenBrick(bricksBuilder, {id: MP_PAYMENT_ID});
}
else
{
    renderPaymentBrick(bricksBuilder);
}