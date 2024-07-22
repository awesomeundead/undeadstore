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
            paymentMethods:
            {
                bankTransfer: "all",
                creditCard: "all",
                debitCard: "all",
                mercadoPago: "all",
                maxInstallments: 12
            }
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
                    fetch('/payment/process',
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

                        const renderStatusScreenBrick = async (bricksBuilder) =>
                        {
                            const settings =
                            {
                                initialization: { paymentId: response.id },
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
                            };
                            window.statusScreenBrickController = await bricksBuilder.create( 'statusScreen', 'statusScreenBrick_container', settings);  
                        };

                        renderStatusScreenBrick(bricksBuilder);
                        resolve();
                    })
                    .catch((error) =>
                    {
                        console.error(error);
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

renderPaymentBrick(bricksBuilder);