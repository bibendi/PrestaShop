<?php
/*
* 2007-2011 PrestaShop 
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8783 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
	
class buddietopseller extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'buddietopseller';
		$this->tab = 'mymodule';
		$this->version = '0.1';
		$this->author = 'Codespot';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Top seller on home page');
		$this->description = $this->l('Displaying the shop\'s top sellers on home page.');
	}

	/**
	 * @see ModuleCore::install()
	 */
	public function install()
	{
		if (!Configuration::updateValue('PRODUCT_BESTSELLER_NBR', 9) OR !parent::install() OR
				!$this->registerHook('home') OR
				!$this->registerHook('header') OR
				!$this->registerHook('updateOrderStatus') OR
				!ProductSale::fillProductSales())
			return false;
		return true;
	}

	/**
	 * Called in administration -> module -> configure
	 */
	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBestSellers'))
		{
			Configuration::updateValue('PRODUCT_BESTSELLER_NBR', $_POST['nbrPrSeller']);
			Configuration::updateValue('PS_BLOCK_BESTSELLERS_DISPLAY', (int)(Tools::getValue('always_display')));
			$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		return '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Always display block').'</label>
				<div class="margin-form">
					<input type="radio" name="always_display" id="display_on" value="1" '.(Tools::getValue('always_display', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="always_display" id="display_off" value="0" '.(!Tools::getValue('always_display', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p class="clear">'.$this->l('Show the block even if no product is available.').'</p>
				</div>
				<label>'.$this->l('Number products display').'</label>
				<input type="text" name="nbrPrSeller" value="'.Configuration::get('PRODUCT_BESTSELLER_NBR').'" />
				<br/>
				<center><input type="submit" name="submitBestSellers" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}
	
	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
		$this->context->controller->addJs($this->_path.'jquery.multipleelements.cycle.js');
	}
	public function hookHome($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;

		global $smarty;
		$currency = new Currency((int)($params['cookie']->id_currency));
		$np = Configuration::get('PRODUCT_BESTSELLER_NBR');
		$bestsellers = ProductSale::getBestSales((int)($params['cookie']->id_lang), 0, $np);
		if (!$bestsellers AND !Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY'))
			return;
		$best_sellers = array();
		
		if($bestsellers)
			foreach ($bestsellers AS $bestseller)
			{
				$bestseller['price'] = Tools::displayPrice(Product::getPriceStatic((int)($bestseller['id_product'])), $currency);
				$best_sellers[] = $bestseller;
			}
			
		$smarty->assign(array(
			'best_sellers' => $best_sellers	));
		return $this->display(__FILE__, 'buddietopseller.tpl');
	}
}


