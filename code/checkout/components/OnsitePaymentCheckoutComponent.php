<?php

use Omnipay\Common\Helper;

/**
 *
 * This component should only ever be used on SSL encrypted pages!
 */
class OnsitePaymentCheckoutComponent extends CheckoutComponent
{

    public function getFormFields(Order $order)
    {
        $gateway = Checkout::get($order)->getSelectedPaymentMethod();
        $gatewayfieldsfactory = new GatewayFieldsFactory($gateway, array('Card'));
        $fields = $gatewayfieldsfactory->getCardFields();
        if ($gateway === "Dummy") {
            $fields->unshift(new LiteralField("dummypaymentmessage",
                "<p class=\"message good\">Dummy data has been added to the form for testing convenience.</p>"
            ));
        }

        return $fields;
    }

    public function getRequiredFields(Order $order)
    {
        return GatewayInfo::required_fields(Checkout::get($order)->getSelectedPaymentMethod());
    }

    public function validateData(Order $order, array $data)
    {
        $result = new ValidationResult();
        //TODO: validate credit card data
        if (!Helper::validateLuhn($data['number'])) {
            $result->error('Credit card is invalid');
            throw new ValidationException($result);
        }
    }

    public function getData(Order $order)
    {
        $data = array();
        $gateway = Checkout::get($order)->getSelectedPaymentMethod();
        //provide valid dummy credit card data
        if ($gateway === "Dummy") {
            $data = array_merge(array(
                'name' => 'Joe Bloggs',
                'number' => '4242424242424242',
                'cvv' => 123
            ), $data);
        }
        return $data;
    }

    public function setData(Order $order, array $data)
    {
        //create payment?
    }
}
