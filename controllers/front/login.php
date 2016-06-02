<?php

/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*         DISCLAIMER   *
* *************************************** */

/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* *****************************************************
* @category   Belvg
* @package    login.php
* @author     Dzmitry Urbanovich (urbanovich.mslo@gmail.com)
* @site       http://module-presta.com
* @copyright  Copyright (c) 2007 - 2016 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/

class customer_loginLoginModuleFrontController extends ModuleFrontControllerCore
{

    public function init()
    {
        parent::init();

        //checking if user is admin
        $url = parse_url($_SERVER['HTTP_REFERER']);

        //get $token with GET request
        parse_str($url['query']);

        $id_tab = Tab::getIdFromClassName('AdminCustomers');
        $admin = false;
        foreach(Employee::getEmployees() as $employee)
        {
            if(Tools::getAdminToken('AdminCustomers' . $id_tab . $employee['id_employee']) == $token)
                $admin = true;
        }

        if(Tools::isSubmit('id_customer') && $admin)
        {
            $this->context = Context::getContext();
            Hook::exec('actionBeforeAuthentication');
            $customer = new Customer(Tools::getValue('id_customer'));

            $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
            $this->context->cookie->id_customer = (int)($customer->id);
            $this->context->cookie->customer_lastname = $customer->lastname;
            $this->context->cookie->customer_firstname = $customer->firstname;
            $this->context->cookie->logged = 1;
            $customer->logged = 1;
            $this->context->cookie->is_guest = $customer->isGuest();
            $this->context->cookie->passwd = $customer->passwd;
            $this->context->cookie->email = $customer->email;

            // Add customer to the context
            $this->context->customer = $customer;

            if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
                $this->context->cart = new Cart($id_cart);
            } else {
                $id_carrier = (int)$this->context->cart->id_carrier;

                $this->context->cart->id_carrier = 0;
                $this->context->cart->setDeliveryOption(null);
                $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
            }
            $this->context->cart->id_customer = (int)$customer->id;
            $this->context->cart->secure_key = $customer->secure_key;

            if ($this->ajax && isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
                $this->context->cart->setDeliveryOption($delivery_option);
            }

            $this->context->cart->save();
            $this->context->cookie->id_cart = (int)$this->context->cart->id;
            $this->context->cookie->write();
            $this->context->cart->autosetProductAddress();

            Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

            // Login information have changed, so we check if the cart rules still apply
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);

            Tools::redirect('index.php?controller=my-account');

        }
    }
}