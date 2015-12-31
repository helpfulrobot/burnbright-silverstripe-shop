<?php

class CheckoutStep_PaymentMethod extends CheckoutStep
{

    private static $allowed_actions = array(
        'paymentmethod',
        'PaymentMethodForm',
    );

    protected function checkoutconfig()
    {
        $config = new CheckoutComponentConfig(ShoppingCart::curr(), false);
        $config->addComponent(new PaymentCheckoutComponent());

        return $config;
    }

    public function paymentmethod()
    {
        $gateways = GatewayInfo::get_supported_gateways();
        if (count($gateways) == 1) {
            return $this->owner->redirect($this->NextStepLink());
        }
        return array(
            'OrderForm' => $this->PaymentMethodForm()
        );
    }

    public function PaymentMethodForm()
    {
        $form = new CheckoutForm($this->owner, "PaymentMethodForm", $this->checkoutconfig());
        $form->setActions(new FieldList(
            FormAction::create("setpaymentmethod", "Continue")
        ));
        $this->owner->extend('updatePaymentMethodForm', $form);

        return $form;
    }

    public function setpaymentmethod($data, $form)
    {
        $this->checkoutconfig()->setData($form->getData());
        return $this->owner->redirect($this->NextStepLink());
    }

    public function SelectedPaymentMethod()
    {
        return Checkout::get($this->owner->Cart())->getSelectedPaymentMethod(true);
    }
}
