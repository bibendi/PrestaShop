<?php

/**
* robokassa module redirect controller.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*/

class robokassaredirectModuleFrontController extends ModuleFrontController
{
	public $display_header = false;
	public $display_column_left = false;
	public $display_column_right = false;
	public $display_footer = false;
	public $ssl = true;

	public function initContent()
	{
		if($id_cart=Tools::getValue('id_cart'))
		{
			$myCart=new Cart($id_cart);
			if(!Validate::isLoadedObject($myCart))
				$myCart=$this->context->cart;
		}else
			$myCart=$this->context->cart;
		$currency = new Currency($myCart->id_currency);
		$total_to_pay = number_format(Tools::convertPrice($myCart->getOrderTotal(true, Cart::BOTH), $currency, false), 2, '.', '');
		if ($postvalidate=Configuration::get('robokassa_postvalidate'))
			$order_number=$myCart->id;
		else
		{
			if(!($order_number=Order::getOrderByCartId($myCart->id)))
			{
				$this->module->validateOrder((int)$myCart->id, Configuration::get('PL_OS_WAITPAYMENT'), $myCart->getOrderTotal(true, Cart::BOTH), $this->module->displayName, NULL, array(), NULL, false, $myCart->secure_key);
				$order_number=$this->module->currentOrder;
			}
		}

        $customer=new Customer($myCart->id_customer);
        $signature=md5(Configuration::get('robokassa_login').':'.$total_to_pay.':'.$order_number.':'.Configuration::get('robokassa_password1'));

		$this->context->smarty->assign(array(
            'robokassa_login' => Configuration::get('robokassa_login'),
            'robokassa_demo' => Configuration::get('robokassa_demo'),
            'signature' => strtoupper($signature),
            'email' => $customer->email,
			'postvalidate' => $postvalidate,
			'order_number' => $order_number,
			'total_to_pay' => $total_to_pay,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		return $this->setTemplate('redirect.tpl');
	}
}