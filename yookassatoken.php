<?php
/**
 * WHMCS Sample Tokenisation Gateway Module
 *
 * This sample module demonstrates how to create a merchant gateway module
 * that accepts input of pay method data locally and then exchanges it for
 * a token that is stored locally for future billing attempts.
 *
 * As with all modules, within the module itself, all functions must be
 * prefixed with the module filename, followed by an underscore, and then
 * the function name. For this example file, the filename is "tokengateway"
 * and therefore all functions begin "yookassatoken_".
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/
 *
 * @copyright Copyright (c) WHMCS Limited 2019
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */
use WHMCS\Billing\Payment\Transaction\Information;
use WHMCS\Carbon;
use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

#require_once ROOTDIR . '/modules/addons/whscore/lib/Autoload.php';

if (!class_exists('\YooKassa\Client')) {
	require ROOTDIR . '/modules/gateways/whs_yookassa/vendor/autoload.php';
}

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function yookassatoken_MetaData()
{
    return [
        'DisplayName' => 'Sample Tokenisation Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
    ];
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/configuration/
 *
 * @return array
 */
function yookassatoken_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'YooKassa-token',
        ),
        'shopID' => array(
            'FriendlyName' => 'Shop ID',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your shop ID here',
        ),
        'secretKey' => array(
            'FriendlyName' => 'Secret Key',
            'Type' => 'password',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Enter secret key here',
		)
    );
}

/**
 * Store payment details.
 *
 * Called when a new pay method is added or an existing pay method is
 * requested to be updated or deleted.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/tokenised-remote-storage/
 *
 * @return array
 */
function yookassatoken_nolocalcc()
{
}
// function yookassatoken_storeremote($params)
// {

// 	$params['systemurl'] = ($params['systemurl'][strlen($params['systemurl'])-1] != '/') ? $params['systemurl'] . '/' : $params['systemurl'];
// 	$client = new \YooKassa\Client();
// 	$client->setAuth($params['shopID'], $params['secretKey']);
//     // Gateway Configuration Parameters
//     $apiUsername = $params['apiUsername'];
//     $apiPassword = $params['apiPassword'];
//     $testMode = $params['testMode'];

//     // Store Remote Parameters
//     $action = $params['action']; // One of either 'create', 'update' or 'delete'
//     $remoteGatewayToken = $params['gatewayid'];
//     $cardType = $params['cardtype']; // Card Type
//     $cardNumber = $params['cardnum']; // Credit Card Number
//     $cardExpiry = $params['cardexp']; // Card Expiry Date (format: mmyy)
//     $cardStart = $params['cardstart']; // Card Start Date (format: mmyy)
//     $cardIssueNum = $params['cardissuenum']; // Card Issue Number
//     $cardCvv = $params['cccvv']; // Card Verification Value

//     // Client Parameters
//     $firstname = $params['clientdetails']['firstname'];
//     $lastname = $params['clientdetails']['lastname'];
//     $email = $params['clientdetails']['email'];
//     $address1 = $params['clientdetails']['address1'];
//     $address2 = $params['clientdetails']['address2'];
//     $city = $params['clientdetails']['city'];
//     $state = $params['clientdetails']['state'];
//     $postcode = $params['clientdetails']['postcode'];
//     $country = $params['clientdetails']['country'];
//     $phone = $params['clientdetails']['phonenumber'];

//     switch ($action) {
//         case 'create':
//             // Invoked when a new card is added.
            
//             $postfields = [
//                 'card_type' => $cardType,
//                 'card_number' => $cardNumber,
//                 'card_expiry_month' => substr($cardExpiry, 0, 2),
//                 'card_expiry_year' => substr($cardExpiry, 2, 2),
//                 'card_cvv' => $cardCvv,
//                 'card_holder_name' => $firstname . ' ' . $lastname,
//                 'card_holder_address1' => $address1,
//                 'card_holder_address2' => $address2,
//                 'card_holder_city' => $city,
//                 'card_holder_state' => $state,
//                 'card_holder_zip' => $postcode,
//                 'card_holder_country' => $country,
//             ];

//             // Perform API call to store the provided card details and generate a token.
//             // Sample response data:
//             $response = [
//                 'success' => true,
//                 'token' => 'abc1111111111',
//             ];
//             $payment = $client->createPayment(
//                 array(
//                         'payment_token' => $description,
//                     'amount' => array(
//                         'value' => $params['amount'],
//                         'currency' => $params['currency'],
//                     ),
//                     'confirmation' => array(
//                         'type' => 'redirect',
//                         // 'return_url' => $params['systemurl'] . '/modules/gateways/callback/whs_yookassa.php?id=' . $params['invoiceid'],
//                         'return_url' => $params['systemurl'] . '/viewinvoice.php?id=' . $params['invoiceid'],
//                     ),
//                     'capture' => true,
//                     'description' => 'Invoice #' . $params['invoiceid'],
//                     'metadata' => array(
//                         'invoiceid' => $params['invoiceid'],
//                     ),
//                     'save_payment_method' => true
//                 ),
//                 uniqid('', true)
//             );
//             $response = json_decode(json_encode($payment), true);

//             if ($response['success']) {
//                 return [
//                     // 'success' if successful, otherwise 'error' for failure
//                     'status' => 'success',
//                     // Data to be recorded in the gateway log - can be a string or array
//                     'rawdata' => $response,
//                     // The token that should be stored in WHMCS for recurring payments
//                     'gatewayid' => $response['token'],
//                 ];
//             }

//             return [
//                 // 'success' if successful, otherwise 'error' for failure
//                 'status' => 'error',
//                 // Data to be recorded in the gateway log - can be a string or array
//                 'rawdata' => $response,
//             ];

//             break;
//         case 'update':
//             // Invoked when an existing card is updated.
//             $postfields = [
//                 'token' => $remoteGatewayToken,
//                 'card_type' => $cardType,
//                 'card_number' => $cardNumber,
//                 'card_expiry_month' => substr($cardExpiry, 0, 2),
//                 'card_expiry_year' => substr($cardExpiry, 2, 2),
//                 'card_cvv' => $cardCvv,
//                 'card_holder_name' => $firstname . ' ' . $lastname,
//                 'card_holder_address1' => $address1,
//                 'card_holder_address2' => $address2,
//                 'card_holder_city' => $city,
//                 'card_holder_state' => $state,
//                 'card_holder_zip' => $postcode,
//                 'card_holder_country' => $country,
//             ];

//             // Perform API call to update the requested token.
//             // Sample response data:
//             $response = [
//                 'success' => true,
//                 'token' => 'abc2222222222',
//             ];

//             if ($response['success']) {
//                 return [
//                     // 'success' if successful, otherwise 'error' for failure
//                     'status' => 'success',
//                     // Data to be recorded in the gateway log - can be a string or array
//                     'rawdata' => $response,
//                     // The token to be stored if it has changed
//                     'gatewayid' => $response['token'],
//                 ];
//             }

//             return [
//                 // 'success' if successful, otherwise 'error' for failure
//                 'status' => 'error',
//                 // Data to be recorded in the gateway log - can be a string or array
//                 'rawdata' => $response,
//             ];

//             break;
//         case 'delete':
//             // Invoked when an existing card is requested to be deleted.
//             $postfields = [
//                 'token' => $remoteGatewayToken,
//             ];

//             // Perform API call to delete the requested token.
//             // Sample response data:
//             $response = [
//                 'success' => true,
//             ];

//             if ($response['success']) {
//                 return [
//                     // 'success' if successful, otherwise 'error' for failure
//                     'status' => 'success',
//                     // Data to be recorded in the gateway log - can be a string or array
//                     'rawdata' => $response,
//                 ];
//             }

//             return [
//                 // 'success' if successful, otherwise 'declined', 'error' for failure
//                 'status' => 'error',
//                 // Data to be recorded in the gateway log - can be a string or array
//                 'rawdata' => $response,
//             ];

//             break;
//     }
// }

/**
 * Capture payment.
 *
 * Called when a payment is requested to be processed and captured.
 *
 * This function may receive pay method data instead of a token when a
 * payment is attempted using a pay method that was originally created and
 * stored locally within WHMCS using something other than this token
 * module, and therefore it should be able to accomodate captures based
 * both on a token as well as a pay method data.
 *
 * The CVV number parameter will only be present for card holder present
 * transactions. Automated recurring capture attempts will not provide it.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/merchant-gateway/
 *
 * @return array
 */
function yookassatoken_capture($params)
{
    $params['systemurl'] = ($params['systemurl'][strlen($params['systemurl'])-1] != '/') ? $params['systemurl'] . '/' : $params['systemurl'];
	$client = new \YooKassa\Client();
	$client->setAuth($params['shopID'], $params['secretKey']);
    // Gateway Configuration Parameters
    $apiUsername = $params['apiUsername'];
    $apiPassword = $params['apiPassword'];
    $testMode = $params['testMode'];

    // Capture Parameters
    $remoteGatewayToken = $params['gatewayid'];
    $cardType = $params['cardtype']; // Card Type
    $cardNumber = $params['cardnum']; // Credit Card Number
    $cardExpiry = $params['cardexp']; // Card Expiry Date (format: mmyy)
    $cardStart = $params['cardstart']; // Card Start Date (format: mmyy)
    $cardIssueNum = $params['cardissuenum']; // Card Issue Number
    $cardCvv = $params['cccvv']; // Card Verification Value

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params['description'];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];
    $clientId = $params['clientdetails']['id'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // if (!$remoteGatewayToken) {
    //     // If there is no token yet, it indicates this capture is being
    //     // attempted using an existing locally stored card. Create a new
    //     // token and then attempt capture.
    //     $postfields = [
    //         'card_type' => $cardType,
    //         'card_number' => $cardNumber,
    //         'card_expiry_month' => substr($cardExpiry, 0, 2),
    //         'card_expiry_year' => substr($cardExpiry, 2, 2),
    //         'card_cvv' => $cardCvv,
    //         'card_holder_name' => $firstname . ' ' . $lastname,
    //         'card_holder_address1' => $address1,
    //         'card_holder_address2' => $address2,
    //         'card_holder_city' => $city,
    //         'card_holder_state' => $state,
    //         'card_holder_zip' => $postcode,
    //         'card_holder_country' => $country,
    //     ];

    //     // Perform API call to store the provided card details and generate a token.
    //     // Sample response data:

    //     $payment = $client->createPayment(
    //                         array(
    //                             'amount' => array(
    //                                 'value' => $params['amount'],
    //                                 'currency' => $params['currency'],
    //                             ),
    //                             'confirmation' => array(
    //                                 'type' => 'embedded'
    //                             ),
    //                             'capture' => true,
    //                             'description' => 'Invoice #' . $params['invoiceid'],
    //                             'metadata' => array(
    //                                 'invoiceid' => $params['invoiceid'],
    //                             ),
    //                             // 'save_payment_method' => true
    //                         ),
    //                         uniqid('', true)
    //                     );
    //     $response = json_decode(json_encode($payment), true);
            
    //     if ($response['status']) {
    //         $remoteGatewayToken = $response['payment_method']['id'];
    //         return [
    //             'status' => 'success',
    //             'rawdata' => $response,
    //             'gatewayid' => $remoteGatewayToken,
    //         ];
    //     } else {
    //         return [
    //             // 'success' if successful, otherwise 'error' for failure
    //             'status' => 'error',
    //             // Data to be recorded in the gateway log - can be a string or array
    //             'rawdata' => $response,
    //         ];
    //     }
    // }

    // $postfields = [
    //     'token' => $remoteGatewayToken,
    //     'cvv' => $cardCvv,
    //     'invoice_number' => $invoiceId,
    //     'amount' => $amount,
    //     'currency' => $currencyCode,
    // ];

    // Perform API call to initiate capture.
    // Sample response data:
    $payment = $client->createPayment(
        array(
            'payment_method_id' => $params['gatewayid'],
            'amount' => array(
                'value' => $params['amount'],
                'currency' => $params['currency'],
            ),
            // 'confirmation' => array(
            //     'type' => 'embedded'
            // ),
            'capture' => true,
            'description' => 'Invoice #' . $params['invoiceid'],
            'metadata' => array(
                'invoiceid' => $invoiceId,
                'userid' => $clientId,
            ),
            // 'merchant_customer_id' => $clientId,
            'save_payment_method' => true,
            "receipt" => array(
                "customer" => array(
                    "full_name" => "Иванов Иван Иванович",
                    "phone" => "79000000000",
                ),
                "items" => array(
                    array(
                        "description" => "Наименование товара 1",
                        "quantity" => "2.00",
                        "amount" => array(
                            "value" => "250.00",
                            "currency" => "RUB"
                        ),
                        "vat_code" => "2",
                        "payment_mode" => "full_prepayment",
                        "payment_subject" => "commodity"
                    ),
                    array(
                        "description" => "Наименование товара 2",
                        "quantity" => "1.00",
                        "amount" => array(
                            "value" => "100.00",
                            "currency" => "RUB"
                        ),
                        "vat_code" => "2",
                        "payment_mode" => "full_prepayment",
                        "payment_subject" => "commodity"
                    )
                )
            )
        ),
        uniqid('', true)
    );
    $response = json_decode(json_encode($payment), true);
    logModuleCall("Yookassa", "payment", $response, $response['status'], '');
    if ($response['status']=='succeeded') {
        return [
            // 'success' if successful, otherwise 'declined', 'error' for failure
            'status' => 'success',
            // The unique transaction id for the payment
            'transid' => $response['id'],
            // Optional fee amount for the transaction
            'fee' => $response['amount']['value'] - $response['income_amount']['value'],
            // Return only if the token has updated or changed
            'gatewayid' => $response['payment_method']['id'],
            // Data to be recorded in the gateway log - can be a string or array
            'rawdata' => $response,
        ];
    }elseif ($response['status']=='pending') {
        return [
            // 'success' if successful, otherwise 'declined', 'error' for failure
            'status' => 'pending',
            // The unique transaction id for the payment
            'transid' => $response['id'],
            // Optional fee amount for the transaction
            'fee' => $response['amount']['value'] - $response['income_amount']['value'],
            // Return only if the token has updated or changed
            'gatewayid' => $response['payment_method']['id'],
            // Data to be recorded in the gateway log - can be a string or array
            'rawdata' => $response,
        ];
    }



    return [
        // 'success' if successful, otherwise 'declined', 'error' for failure
        'status' => 'declined',
        // For declines, a decline reason can optionally be returned
        'declinereason' => $response['status'],
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $response,
    ];
}

/**
 * Refund transaction.
 *
 * Called when a refund is requested for a previously successful transaction.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/refunds/
 *
 * @return array
 */
function yookassatoken_remoteinput($params)
{
    	$params['systemurl'] = ($params['systemurl'][strlen($params['systemurl'])-1] != '/') ? $params['systemurl'] . '/' : $params['systemurl'];
	$client = new \YooKassa\Client();
	$client->setAuth($params['shopID'], $params['secretKey']);
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params['description'];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $clientId = $params['clientdetails']['id'];
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // Build a form which can be submitted to an iframe target to render
    // the payment form.

    $action = '';
    if ($amount > 0) {
        $action = 'payment';
    } else {
        $action = 'create';
        $params['amount'] = 1;
        $params['currency'] = 'RUB';
    }
    $payment = $client->createPayment(
        array(
            'payment_method' => $remoteGatewayToken,
            'amount' => array(
                'value' => $params['amount'],
                'currency' => $params['currency']?$params['currency']:"RUB",
            ),
            'confirmation' => array(
                'type' => 'embedded'
            ),
            // "payment_method_data" => array(
            //     "type" => "bank_card"
            // ),
            'capture' => true,
            'description' => 'Invoice #' . $params['invoiceid'],
            'metadata' => array(
                'invoiceid' =>  $params['invoiceid'],
                'userid' => $clientId,
            ),
            'save_payment_method' => true,
            // 'merchant_customer_id' => $clientId,
        ),
        uniqid('', true)
    );
    $response = json_decode(json_encode($payment), true);


    $confirmation = $response['confirmation']['confirmation_token'];
    $return_url = $params['systemurl'] . '/index.php/account/paymentmethods/';
    // This is a working example which posts to the file: demo/remote-iframe-demo.php
    return <<<HTML


<div id="payment-form"></div>

<script>
        console.log("yookassatoken_remoteinput");

// Инициализация виджета. Все параметры обязательные.
$(function() {
    const checkout = new window.YooMoneyCheckoutWidget({
confirmation_token: "$confirmation", //Токен, который перед проведением оплаты нужно получить от ЮKassa
return_url: "$return_url", 
error_callback: function(error) {
        //Обработка ошибок инициализации
        console.log(error);
    }
});

//Отображение платежной формы в контейнере
checkout.render('payment-form')
//Метод возвращает Promise, исполнение которого говорит о полной загрузке платежной формы (можно не использовать).
  .then(() => {
     //Код, который нужно выполнить после отображения платежной формы.
  });
// checkout.destroy();

// //Инициализация нового виджета. Все параметры обязательные.
// const checkoutNew = new window.YooMoneyCheckoutWidget({
//     confirmation_token: "$confirmation", //Токен, который перед проведением оплаты нужно получить от ЮKassa
//     return_url: "$return_url",
//     error_callback: function(error) {
//         //Обработка ошибок инициализации
//         console.log(error);

//     }
// });

// //Отображение платежной формы в контейнере
// checkoutNew.render('payment-form')
// //Метод возвращает Promise, исполнение которого говорит о полной загрузке платежной формы (можно не использовать).
//     .then(() => {
//        //Код, который нужно выполнить после отображения платежной формы.
//   });




})


// Удаление платежной формы из контейнера


</script>
HTML;
}

/**
 * Remote update.
 *
 * Called when a pay method is requested to be updated.
 *
 * The expected return of this function is direct HTML output. It provides
 * more flexibility than the remote input function by not restricting the
 * return to a form that is posted into an iframe. We still recommend using
 * an iframe where possible and this sample demonstrates use of an iframe,
 * but the update can sometimes be handled by way of a modal, popup or
 * other such facility.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @return string
 * @see https://developers.whmcs.com/payment-gateways/remote-input-gateway/
 *
 */
// function yookassatoken_remoteupdate($params)
// {
//     $params['systemurl'] = ($params['systemurl'][strlen($params['systemurl'])-1] != '/') ? $params['systemurl'] . '/' : $params['systemurl'];
// 	$client = new \YooKassa\Client();
// 	$client->setAuth($params['shopID'], $params['secretKey']);
//     // Gateway Configuration Parameters
//     $accountId = $params['accountID'];
//     $secretKey = $params['secretKey'];
//     $remoteStorageToken = $params['gatewayid'];
//     $testMode = $params['testMode'];
//     $dropdownField = $params['dropdownField'];
//     $radioField = $params['radioField'];
//     $textareaField = $params['textareaField'];

//     // Invoice Parameters
//     $invoiceId = $params['invoiceid'];
//     $description = $params['description'];
//     $amount = $params['amount'];
//     $currencyCode = $params['currency'];

//     // Client Parameters
//     $clientId = $params['clientdetails']['id'];
//     $firstname = $params['clientdetails']['firstname'];
//     $lastname = $params['clientdetails']['lastname'];
//     $email = $params['clientdetails']['email'];
//     $address1 = $params['clientdetails']['address1'];
//     $address2 = $params['clientdetails']['address2'];
//     $city = $params['clientdetails']['city'];
//     $state = $params['clientdetails']['state'];
//     $postcode = $params['clientdetails']['postcode'];
//     $country = $params['clientdetails']['country'];
//     $phone = $params['clientdetails']['phonenumber'];

//     // System Parameters
//     $companyName = $params['companyname'];
//     $systemUrl = $params['systemurl'];
//     $returnUrl = $params['returnurl'];
//     $langPayNow = $params['langpaynow'];
//     $moduleDisplayName = $params['name'];
//     $moduleName = $params['paymentmethod'];
//     $whmcsVersion = $params['whmcsVersion'];

//     // Build a form which can be submitted to an iframe target to render
//     // the payment form.

//     $action = '';
//     if ($amount > 0) {
//         $action = 'payment';
//     } else {
//         $action = 'create';
//     }
//     $payment = $client->createPayment(
//         array(
//             'payment_method' => $remoteGatewayToken,
//             'amount' => array(
//                 'value' => 10,
//                 'currency' => "RUB",
//             ),
//             'confirmation' => array(
//                 'type' => 'embedded'
//             ),
//             'capture' => true,
//             'description' => 'Invoice #' . $invoiceId,
//             'metadata' => array(
//                 'invoiceid' => $invoiceId,
//                 'userid' => $clientId,
//             ),
//             'save_payment_method' => true,
//             // 'merchant_customer_id' => $clientId,
//         ),
//         uniqid('', true)
//     );
//     $response = json_decode(json_encode($payment), true);

//     $confirmation = $response['confirmation']['confirmation_token'];
//     $return_url = $params['systemurl'] . '/account/paymentmethods';

//     // This is a working example which posts to the file: demo/remote-iframe-demo.php
//     return <<<HTML

// <script src="https://yookassa.ru/checkout-widget/v1/checkout-widget.js"></script>
// <div id="payment-form"></div>

// <script>
//     console.log("yookassatoken_remoteupdate");
// //Инициализация виджета. Все параметры обязательные.
// const checkout = new window.YooMoneyCheckoutWidget({
//     confirmation_token: $confirmation, //Токен, который перед проведением оплаты нужно получить от ЮKassa
//     return_url: $return_url, 
//     error_callback: function(error) {
//         //Обработка ошибок инициализации
//         console.log(error);
//     }
// });

// //Отображение платежной формы в контейнере
// checkout.render('payment-form')
// //Метод возвращает Promise, исполнение которого говорит о полной загрузке платежной формы (можно не использовать).
//   .then(() => {
//      //Код, который нужно выполнить после отображения платежной формы.
//   });

// //Удаление платежной формы из контейнера
// // checkout.destroy();

// //Инициализация нового виджета. Все параметры обязательные.
// const checkoutNew = new window.YooMoneyCheckoutWidget({
//     confirmation_token: $confirmation, //Токен, который перед проведением оплаты нужно получить от ЮKassa
//     return_url: $return_url, 
//     error_callback: function(error) {
//         //Обработка ошибок инициализации
//         console.log(error);

//     }
// });

// //Отображение платежной формы в контейнере
// // checkoutNew.render('payment-form')
// // //Метод возвращает Promise, исполнение которого говорит о полной загрузке платежной формы (можно не использовать).
// //     .then(() => {
// //        //Код, который нужно выполнить после отображения платежной формы.
// //   });
// // </script>
// HTML;
// }

/**
 * Admin Status Message.
 *
 * Called when an invoice is viewed in the admin area.
 *
 * @param array $params Payment Gateway Module Parameters.
 *
 * @return array
 */
function yookassatoken_adminstatusmsg($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $remoteStorageToken = $params['gatewayid'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];

    // Invoice Parameters
    $remoteGatewayToken = $params['gatewayid'];
    // The id of the invoice being viewed
    $invoiceId = $params['id'];
    // The id of the user the invoice belongs to
    $userId = $params['userid'];
    // The creation date of the invoice
    $date = $params['date'];
    // The due date of the invoice
    $dueDate = $params['duedate'];
    // The status of the invoice
    $status = $params['status'];

    if ($remoteGatewayToken) {
        return [
            'type' => 'info',
            'title' => 'Token Gateway Profile',
            'msg' => 'This customer has a Remote Token storing their bank'
                . ' details for automated recurring billing with ID ' . $remoteGatewayToken,
        ];
    }
}
