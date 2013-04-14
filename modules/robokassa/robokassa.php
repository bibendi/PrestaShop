<?php

/**
* robokassa module main file.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.2
*/

if (!defined('_PS_VERSION_'))
	exit;

class robokassa extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'robokassa';
		$this->tab = 'payments_gateways';
		$this->version = '0.2';
		$this->author = 'PrestaLab.Ru';
		$this->need_instance = 0;
		//Ключик из addons.prestashop.com
		$this->module_key='81c2ab22efca5e64a3b02ee819c9fcb2';

		//Привязвать к валюте
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		parent::__construct();

		$this->displayName = $this->l('RoboKassa');
		$this->description = $this->l('Service to receive payments by plastic cards, in every e-currency, using mobile commerce');
	}

	public function install()
	{
		return (parent::install()
			&& $this->registerHook('payment')
			&& $this->registerHook('paymentReturn')
			&&$this->_addPLStatuses()
		);
	}

	public function uninstall()
	{
		return (parent::uninstall()
			&& Configuration::deleteByName('robokassa_login')
			&& Configuration::deleteByName('robokassa_password1')
			&& Configuration::deleteByName('robokassa_password2')
			&& Configuration::deleteByName('robokassa_postvalidate')
		);
	}

	private function initToolbar()
	{
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);
		return $this->toolbar_btn;
	}

	protected function _displayForm()
	{

		$this->_display = 'index';


		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Merchant login'),
					'desc' => $this->l('Merchant login in RoboKassa system'),
					'name' => 'robokassa_login',
					'size' => 33,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Password #1'),
					'desc' => $this->l('Password used during payment initialization procedure'),
					'name' => 'robokassa_password1',
					'size' => 33,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Password #2'),
					'desc' => $this->l('Password used by payment notification interface'),
					'name' => 'robokassa_password2',
					'size' => 33,
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Demo mode') ,
					'name' => 'robokassa_demo',
					'desc' => $this->l('Set this mode to disabled for switch to production mode'),
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'robokassa_demo_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'robokassa_demo_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Order after payment'),
					'name' => 'robokassa_postvalidate',
					'desc' => $this->l('Create order after receive payment notification'),
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'robokassa_postvalidate_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'robokassa_postvalidate_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),

			),

			'submit' => array(
				'name' => 'submitrobokassa',
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		$this->fields_form[1]['form'] = array(
			'legend' => array(
				'title' => $this->l('Merchant configuration information') ,
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Result URL'),
					'desc' => $this->l('Used for payment notification.'),
					'name' => 'url1',
					'size' => 120,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Success URL'),
					'desc' => $this->l('URL to be used for query in case of successful payment.'),
					'name' => 'url2',
					'size' => 120,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Fail URL'),
					'desc' => $this->l('URL to be used for query in case of failed payment.'),
					'name' => 'url3',
					'size' => 120,
				)
			)
		);

		$this->fields_value['robokassa_login'] = Configuration::get('robokassa_login');
		$this->fields_value['robokassa_password1'] = Configuration::get('robokassa_password1');
		$this->fields_value['robokassa_password2'] = Configuration::get('robokassa_password2');
		$this->fields_value['robokassa_demo'] = Configuration::get('robokassa_demo');
		$this->fields_value['robokassa_postvalidate'] = Configuration::get('robokassa_postvalidate');

		$this->fields_value['url1'] = $this->context->link->getModuleLink('robokassa', 'validation', array(), true) ;
		$this->fields_value['url2'] = $this->context->link->getModuleLink('robokassa', 'success', array(), true);
		$this->fields_value['url3'] = $this->context->link->getPageLink('order.php', true, null, 'step=3');

		$helper = $this->initForm();
		$helper->submit_action = '';

		$helper->title = $this->displayName;

		$helper->fields_value = $this->fields_value;
		$this->_html .= $helper->generateForm($this->fields_form);
		return;
	}

	private function initForm()
	{
		$helper = new HelperForm();

		$helper->module = $this;
		$helper->name_controller = 'robokassa';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->toolbar_scroll = true;
		$helper->tpl_vars['version'] = $this->version;
		$helper->tpl_vars['author'] = $this->author;
		$helper->tpl_vars['this_path'] = $this->_path;
		$helper->toolbar_btn = $this->initToolbar();

		return $helper;
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitrobokassa'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= $this->displayError($err);;
		}
		$this->_displayForm();
		return $this->_html;
	}

	private function _postValidation()
	{
		if(Tools::getValue('robokassa_login')&&(!Validate::isString(Tools::getValue('robokassa_login'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Merchant login');
		if(Tools::getValue('robokassa_password1')&&(!Validate::isString(Tools::getValue('robokassa_password1'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Password #1');
		if(Tools::getValue('robokassa_password2')&&(!Validate::isString(Tools::getValue('robokassa_password2'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Password #2');
	}

	private function _postProcess()
	{

		Configuration::updateValue('robokassa_login', Tools::getValue('robokassa_login'));
		Configuration::updateValue('robokassa_password1', Tools::getValue('robokassa_password1'));
		Configuration::updateValue('robokassa_password2', Tools::getValue('robokassa_password2'));
        Configuration::updateValue('robokassa_demo', (int)Tools::getValue('robokassa_demo'));
        Configuration::updateValue('robokassa_postvalidate', (int)Tools::getValue('robokassa_postvalidate'));
		$this->_html .= $this->displayConfirmation($this->l('Settings updated.'));
	}

	public function hookpayment($params)
	{
		if (!$this->active)
			return ;

		if (!$this->_checkCurrency($params['cart']))
			return ;


		$this->smarty->assign(array(
			'id_cart' => $params['cart']->id,
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		return $this->display(__FILE__, 'payment.tpl');

	}

	public function hookpaymentReturn($params)
	{
		if (!$this->active)
			return ;

		if(!$order=$params['objOrder'])
			return;

		if ($this->context->cookie->id_customer!=$order->id_customer)
			return;
		if (!$order->hasBeenPaid())
			return;
		$this->smarty->assign(array(
			'products' =>$order->getProducts()
		));
		return $this->display(__FILE__, 'paymentReturn.tpl');

	}


	/**Отображение ответа валидации уведомления
	* @return html
	*/
	static public function validateAnsver($message)
	{
		Logger::addLog('robokassa: ' . $message);
		die($message);
	}

	/*Устанавливает статусы заказа для модулей PrestaLab
	* @return bool
	*/
	private function _addPLStatuses()
	{
		return ($this->_addStatus('PL_OS_WAITPAYMENT', $this->l('Waiting paymetn'))
				//&& $this->_addStatus('PL_OS_APPROVED', $this->l('Approved for payment'))
				//&& $this->_addStatus('PL_OS_JUSTADDED', $this->l('Order added'))
		);
	}

	/*Устанавливает статусы заказа для модулей PrestaLab
	* @return bool
	*/
	private function _addStatus($setting_name, $name, $template=false)
	{
		if (Configuration::get($setting_name))
			return true;

		$status= new OrderState();
		$status->send_email = ($template?1:0);
		$status->invoice = 0;
		$status->logable = 0;
		$status->delivery = 0;
		$status->hidden = 0;

		$lngs = Language::getLanguages();
		foreach ($lngs as $lng) {
			$status->name[$lng['id_lang']] =$name ;
			if($template)
				$status->template[$lng['id_lang']] =$template ;
		}
		if($status->add()){
			Configuration::updateValue($setting_name, $status->id);
			return true;
		}
		return false;
	}

	/*Проверка валюты
	* @return bool
	*/
	private function _checkCurrency($cart)
	{
		$currency_order = new Currency((int)($cart->id_currency));
		$currencies_module = $this->getCurrency((int)$cart->id_currency);
		$currency_default = Configuration::get('PS_CURRENCY_DEFAULT');

		if (is_array($currencies_module))
			foreach ($currencies_module AS $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

}