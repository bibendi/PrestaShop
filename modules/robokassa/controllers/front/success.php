<?php

/**
* robokassa module success payment script.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*/

class robokassasuccessModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	public function initContent()
	{
		parent::initContent();

		$ordernumber = Tools::getValue('InvId');
		$this->context->smarty->assign('ordernumber', $ordernumber);

		if (Configuration::get('robokassa_postvalidate'))
		{
			if (!$ordernumber)
				robokassa::validateAnsver($this->module->l('Cart number is not set'));

			$cart = new Cart((int)$ordernumber);
			if (!Validate::isLoadedObject($cart))
				robokassa::validateAnsver($this->module->l('Cart does not exist'));

			if(!($ordernumber=Order::getOrderByCartId($cart->id)))
				$this->setTemplate('waitingPayment.tpl');
		}

		if (!$ordernumber)
			robokassa::validateAnsver($this->module->l('Order number is not set'));

		$order = new Order((int)$ordernumber);
		if (!Validate::isLoadedObject($order))
			robokassa::validateAnsver($this->module->l('Order does not exist'));

		$customer = new Customer((int)$order->id_customer);

		if ($customer->id != $this->context->cookie->id_customer)
			robokassa::validateAnsver($this->module->l('You are not logged in'));

		if ($order->hasBeenPaid())
			Tools::redirectLink(__PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int)($order->id_cart) . '&id_module=' . (int)$this->module->id . '&id_order=' . (int)$order->id);
		else
			$this->setTemplate('waitingPayment.tpl');
	}
}