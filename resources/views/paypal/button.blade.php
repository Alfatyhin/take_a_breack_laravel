
@extends('layouts.payment-paypal')

@section('title', ' ')

@section('head')
    @parent

@stop
@section('sidebar')
    @parent

@stop

@section('content')
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="paypal_button">
            <p> Total price {{ $orderData['option']['total_price'] }}</p>
            <!-- Set up a container element for the button -->
            <div id="paypal-button-container"></div>
        </div>
        <div class="hide pay_success">
            <h1>Thank You</h1>
        </div>
        <script src="https://www.paypal.com/sdk/js?client-id={{ $client_id }}&currency=ILS"></script>



        <script>

            paypal.Buttons({
                // Sets up the transaction when a payment button is clicked

                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '{{ $orderData['option']['total_price'] }}'
                            },
                            'custom_id': '{{ $order_id }}'
                        }]
                    });
                },

                // Finalize the transaction after payer approval
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(orderData) {
                        // Successful capture! For dev/demo purposes:
                        var transaction = orderData.purchase_units[0].payments.captures[0];

                        var settings = {
                            "url": "https://takeabreak.website/api/paypal/order/capture",
                            "method": "POST",
                            "timeout": 0,
                            "data": {
                                'data': orderData
                            }
                        };
                        $.ajax(settings).done(function (response) {
                            var data = JSON.parse(response);
                            console.log(data);

                        });

                        $('.paypal_button').hide();
                        $('.pay_success').show();
                    });
                }
            }).render('#paypal-button-container');
            </script>

    </div>
@stop

