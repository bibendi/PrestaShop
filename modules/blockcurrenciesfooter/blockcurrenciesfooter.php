<?php


if (!defined('_PS_VERSION_'))
	exit;
	
class blockcurrenciesfooter extends Module
{
	public function __construct()
	{
		$this->name = 'blockcurrenciesfooter';
		$this->tab = 'custom';
		$this->version = 0.1;
		$this->author = 'Codespot';
		$this->need_instance = 0;

		parent::__construct();
		
		$this->displayName = $this->l('Currency block on footer');
		$this->description = $this->l('Adds a block for selecting a currency.');
	}

	public function install()
	{
		return (parent::install() AND $this->registerHook('footer') AND $this->registerHook('header'));
	}

	/**
	* Returns module content for header
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookFooter($params)
	{
	//	echo "footer fafg";
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
	
		global $smarty;
		$currencies = Currency::getCurrencies();
		if (!sizeof($currencies))
			return '';
		$smarty->assign('currencies', $currencies);
		return $this->display(__FILE__, 'blockcurrenciesfooter.tpl');
	}
	
	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
		$this->context->controller->addCss($this->_path.'blockcurrenciesfooter.css', 'all');
	}
}


