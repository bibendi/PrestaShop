<?php

/**
* robokassa module validation controller.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*/

class robokassavalidationModuleFrontController extends ModuleFrontController
{
	public $display_header = false;
	public $display_column_left = false;
	public $display_column_right = false;
	public $display_footer = false;
	public $ssl = true;

	public function postProcess()
	{
		parent::postProcess();

		//ИД заказа
		$ordernumber = Tools::getValue('InvId');
		//Сумма заказа
		$amount = Tools::getValue('OutSum');

        $signature=md5($amount.':'.$ordernumber.':'.Configuration::get('robokassa_password2'));
		//Проверка подписи
		if (strtoupper($signature) != Tools::getValue('SignatureValue'))
			robokassa::validateAnsver($this->module->l('Invalid signature'));

		if (Configuration::get('robokassa_postvalidate'))
		{
			$cart = new Cart((int)$ordernumber);
			//Проверка существования заказа
			if (!Validate::isLoadedObject($cart))
				robokassa::validateAnsver($this->module->l('Cart does not exist'));

			$currency = new Currency($cart->id_currency);

			$total_to_pay = number_format(Tools::convertPrice($cart->getOrderTotal(true, Cart::BOTH), $currency, false), 2, '.', '');
			//Проверка суммы заказа
			if ($amount != $total_to_pay)
				robokassa::validateAnsver($this->module->l('Incorrect payment summ'));

			$this->module->validateOrder((int)$cart->id, Configuration::get('PS_OS_PAYMENT'), $cart->getOrderTotal(true, Cart::BOTH), $this->module->displayName, NULL, array(), NULL, false, $cart->secure_key);
		}
		else
		{
			$order = new Order((int)$ordernumber);
			//Проверка существования заказа
			if (!Validate::isLoadedObject($order))
				robokassa::validateAnsver($this->module->l('Order does not exist'));

			$currency = new Currency($order->id_currency);

			$total_to_pay = number_format(Tools::convertPrice($order->total_paid, $currency, false), 2, '.', '');
			//Проверка суммы заказа
			if ($amount != $total_to_pay)
				robokassa::validateAnsver($this->module->l('Incorrect payment summ'));

			//Меняем статус заказа
			$history = new OrderHistory();
			$history->id_order = $ordernumber;
			$history->changeIdOrderState(Configuration::get('PS_OS_PAYMENT'), $ordernumber);
			$history->addWithemail(true);
		}
        die('OK'.$ordernumber);
	}
}