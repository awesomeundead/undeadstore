<?php $this->layout('layout', ['title' => 'Pagamento | Undead Store', 'session' => $session]) ?>

<div>
    <div id="statusScreenBrick_container"></div>
    <div id="cardPaymentBrick_container"></div>
</div>

<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>

const MP_PUBLIC_KEY = '<?= $mercadopago['public_key'] ?>';
const MP_AMOUNT = 100;
const MP_PAYER = {email: 'undead.gamer@outlook.com'};

const mp = new MercadoPago(MP_PUBLIC_KEY, { locale: 'pt-BR' });

const bricksBuilder = mp.bricks();

const renderCardPaymentBrick = async (bricksBuilder) =>
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
                maxInstallments: 12
            }
        },
        callbacks:
        {
            onReady: () =>
            {

            },
            onSubmit: (formData) =>
            {
                return new Promise((resolve, reject) =>
                {
                    fetch('/payment/process',
                    {
                        method: 'POST',
                        headers:
                        {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(response =>
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
                                        document.getElementById('cardPaymentBrick_container').hidden = true;
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
                        console.log('ERRO ' + error);
                        reject();
                    });
                });
            },
            onError: (error) =>
            {
                console.log(error);
            }
        }
    };

    window.cardPaymentBrickController = await bricksBuilder.create('cardPayment', 'cardPaymentBrick_container', settings);
};

renderCardPaymentBrick(bricksBuilder);

</script>