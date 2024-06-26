<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd6aa985415b057d85bc5a1a99863499e
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PaymentPlugins\\WooCommerce\\PPCP\\Tests\\' => 38,
            'PaymentPlugins\\WooCommerce\\PPCP\\' => 32,
            'PaymentPlugins\\PayPalSDK\\' => 25,
            'PaymentPlugins\\PPCP\\WooCommerceShipStation\\' => 43,
            'PaymentPlugins\\PPCP\\WooCommerceProductAddons\\' => 45,
            'PaymentPlugins\\PPCP\\WooCommerceGermanized\\' => 42,
            'PaymentPlugins\\PPCP\\WooCommerceExtraProductOptions\\' => 51,
            'PaymentPlugins\\PPCP\\Stripe\\' => 27,
            'PaymentPlugins\\PPCP\\MondialRelay\\' => 33,
            'PaymentPlugins\\PPCP\\FunnelKit\\' => 30,
            'PaymentPlugins\\PPCP\\Elementor\\' => 30,
            'PaymentPlugins\\PPCP\\CheckoutWC\\' => 31,
            'PaymentPlugins\\PPCP\\Blocks\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PaymentPlugins\\WooCommerce\\PPCP\\Tests\\' => 
        array (
            0 => __DIR__ . '/../..' . '/tests',
        ),
        'PaymentPlugins\\WooCommerce\\PPCP\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'PaymentPlugins\\PayPalSDK\\' => 
        array (
            0 => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src',
        ),
        'PaymentPlugins\\PPCP\\WooCommerceShipStation\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/woocommerce-shipstation/src',
        ),
        'PaymentPlugins\\PPCP\\WooCommerceProductAddons\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/woocommerce-product-addons/src',
        ),
        'PaymentPlugins\\PPCP\\WooCommerceGermanized\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/woocommerce-germanized/src',
        ),
        'PaymentPlugins\\PPCP\\WooCommerceExtraProductOptions\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/woocommerce-tm-extra-product-options/src',
        ),
        'PaymentPlugins\\PPCP\\Stripe\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/stripe/src',
        ),
        'PaymentPlugins\\PPCP\\MondialRelay\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/mondial-relay/src',
        ),
        'PaymentPlugins\\PPCP\\FunnelKit\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/funnelkit/src',
        ),
        'PaymentPlugins\\PPCP\\Elementor\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/elementor/src',
        ),
        'PaymentPlugins\\PPCP\\CheckoutWC\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/checkoutwc/src',
        ),
        'PaymentPlugins\\PPCP\\Blocks\\' => 
        array (
            0 => __DIR__ . '/../..' . '/packages/blocks/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'PaymentPlugins\\PayPalSDK\\AbstractObject' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/AbstractObject.php',
        'PaymentPlugins\\PayPalSDK\\Address' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Address.php',
        'PaymentPlugins\\PayPalSDK\\AgreementDetails' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/AgreementDetails.php',
        'PaymentPlugins\\PayPalSDK\\Amount' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Amount.php',
        'PaymentPlugins\\PayPalSDK\\Authorization' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Authorization.php',
        'PaymentPlugins\\PayPalSDK\\BillingAgreement' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/BillingAgreement.php',
        'PaymentPlugins\\PayPalSDK\\BillingAgreementToken' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/BillingAgreementToken.php',
        'PaymentPlugins\\PayPalSDK\\Breakdown' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Breakdown.php',
        'PaymentPlugins\\PayPalSDK\\Capture' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Capture.php',
        'PaymentPlugins\\PayPalSDK\\Client\\AbstractClient' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Client/AbstractClient.php',
        'PaymentPlugins\\PayPalSDK\\Client\\BaseHttpClient' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Client/BaseHttpClient.php',
        'PaymentPlugins\\PayPalSDK\\Client\\ClientInterface' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Client/ClientInterface.php',
        'PaymentPlugins\\PayPalSDK\\Collection' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Collection.php',
        'PaymentPlugins\\PayPalSDK\\Exception\\AccessTokenExpiredException' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Exception/AccessTokenExpiredException.php',
        'PaymentPlugins\\PayPalSDK\\Exception\\ApiException' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Exception/ApiException.php',
        'PaymentPlugins\\PayPalSDK\\Exception\\AuthenticationException' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Exception/AuthenticationException.php',
        'PaymentPlugins\\PayPalSDK\\Exception\\AuthorizationException' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Exception/AuthorizationException.php',
        'PaymentPlugins\\PayPalSDK\\Exception\\BadRequestException' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Exception/BadRequestException.php',
        'PaymentPlugins\\PayPalSDK\\Exception\\InternalServerException' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Exception/InternalServerException.php',
        'PaymentPlugins\\PayPalSDK\\Exception\\NotFoundException' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Exception/NotFoundException.php',
        'PaymentPlugins\\PayPalSDK\\Exception\\UnprocessableEntity' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Exception/UnprocessableEntity.php',
        'PaymentPlugins\\PayPalSDK\\ExchangeRate' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/ExchangeRate.php',
        'PaymentPlugins\\PayPalSDK\\Item' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Item.php',
        'PaymentPlugins\\PayPalSDK\\Link' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Link.php',
        'PaymentPlugins\\PayPalSDK\\Money' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Money.php',
        'PaymentPlugins\\PayPalSDK\\Name' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Name.php',
        'PaymentPlugins\\PayPalSDK\\OAuthSignUp' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/OAuthSignUp.php',
        'PaymentPlugins\\PayPalSDK\\OAuthToken' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/OAuthToken.php',
        'PaymentPlugins\\PayPalSDK\\Order' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Order.php',
        'PaymentPlugins\\PayPalSDK\\OrderAmount' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/OrderAmount.php',
        'PaymentPlugins\\PayPalSDK\\OrderApplicationContext' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/OrderApplicationContext.php',
        'PaymentPlugins\\PayPalSDK\\PatchRequest' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/PatchRequest.php',
        'PaymentPlugins\\PayPalSDK\\PayPalClient' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/PayPalClient.php',
        'PaymentPlugins\\PayPalSDK\\Payee' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Payee.php',
        'PaymentPlugins\\PayPalSDK\\Payer' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Payer.php',
        'PaymentPlugins\\PayPalSDK\\PayerInfo' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/PayerInfo.php',
        'PaymentPlugins\\PayPalSDK\\Payment' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Payment.php',
        'PaymentPlugins\\PayPalSDK\\PaymentMethod' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/PaymentMethod.php',
        'PaymentPlugins\\PayPalSDK\\PaymentSource' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/PaymentSource.php',
        'PaymentPlugins\\PayPalSDK\\Payments' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Payments.php',
        'PaymentPlugins\\PayPalSDK\\Phone' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Phone.php',
        'PaymentPlugins\\PayPalSDK\\PhoneNumber' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/PhoneNumber.php',
        'PaymentPlugins\\PayPalSDK\\PlatformFee' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/PlatformFee.php',
        'PaymentPlugins\\PayPalSDK\\ProcessorResponse' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/ProcessorResponse.php',
        'PaymentPlugins\\PayPalSDK\\PurchaseUnit' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/PurchaseUnit.php',
        'PaymentPlugins\\PayPalSDK\\Refund' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Refund.php',
        'PaymentPlugins\\PayPalSDK\\SellerPayableBreakdown' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/SellerPayableBreakdown.php',
        'PaymentPlugins\\PayPalSDK\\SellerProtection' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/SellerProtection.php',
        'PaymentPlugins\\PayPalSDK\\SellerReceivableBreakdown' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/SellerReceivableBreakdown.php',
        'PaymentPlugins\\PayPalSDK\\Service\\AbstractServiceFactory' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/AbstractServiceFactory.php',
        'PaymentPlugins\\PayPalSDK\\Service\\BaseService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/BaseService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\BaseServiceFactory' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/BaseServiceFactory.php',
        'PaymentPlugins\\PayPalSDK\\Service\\BillingAgreementService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/BillingAgreementService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\BillingAgreementTokenService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/BillingAgreementTokenService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\OAuthTokenService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/OAuthTokenService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\OrderService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/OrderService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\PartnerService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/PartnerService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\PaymentAuthorizationService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/PaymentAuthorizationService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\PaymentCaptureService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/PaymentCaptureService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\PaymentService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/PaymentService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\PaymentTokenService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/PaymentTokenService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\TrackingService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/TrackingService.php',
        'PaymentPlugins\\PayPalSDK\\Service\\WebhookService' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Service/WebhookService.php',
        'PaymentPlugins\\PayPalSDK\\Shipping' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Shipping.php',
        'PaymentPlugins\\PayPalSDK\\ShippingOption' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/ShippingOption.php',
        'PaymentPlugins\\PayPalSDK\\StatusDetails' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/StatusDetails.php',
        'PaymentPlugins\\PayPalSDK\\Token' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Token.php',
        'PaymentPlugins\\PayPalSDK\\Utils' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Utils.php',
        'PaymentPlugins\\PayPalSDK\\V1\\Address' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/V1/Address.php',
        'PaymentPlugins\\PayPalSDK\\V1\\Tracker' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/V1/Tracker.php',
        'PaymentPlugins\\PayPalSDK\\Webhook' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/Webhook.php',
        'PaymentPlugins\\PayPalSDK\\WebhookEvent' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/WebhookEvent.php',
        'PaymentPlugins\\PayPalSDK\\WebhookEventType' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/WebhookEventType.php',
        'PaymentPlugins\\PayPalSDK\\WebhookResourceVersion' => __DIR__ . '/..' . '/paymentplugins/paypal-php-sdk/src/WebhookResourceVersion.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd6aa985415b057d85bc5a1a99863499e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd6aa985415b057d85bc5a1a99863499e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd6aa985415b057d85bc5a1a99863499e::$classMap;

        }, null, ClassLoader::class);
    }
}
