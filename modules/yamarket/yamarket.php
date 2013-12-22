<?php

/**
* yamarket module main file.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 0.5
*/

if (!defined('_PS_VERSION_'))
	exit;

class yamarket extends Module
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'yamarket';
		$this->tab = 'export';
		$this->version = '1.1';
		$this->author = 'PrestaLab.Ru';
		$this->need_instance = 0;
		//Ключик из addons.prestashop.com
		$this->module_key='';

		parent::__construct();

		$this->displayName = $this->l('Yandex Market export');
		$this->description = $this->l('Yandex Market export file generator');
	}

	public function install()
	{
		return (parent::install()
			&&Configuration::updateValue('yamarket_categories', serialize(array()))
			&&Configuration::updateValue('yamarket_shipping', serialize(array()))
			&&Configuration::updateValue('yamarket_shop', Configuration::get('PS_SHOP_NAME'))
			&&Configuration::updateValue('yamarket_company', Configuration::get('PS_SHOP_NAME'))
		);
	}

	public function uninstall()
	{
		return (parent::uninstall()
			&& Configuration::deleteByName('yamarket_shop')
			&& Configuration::deleteByName('yamarket_company')
			&& Configuration::deleteByName('yamarket_shipping')
			&& Configuration::deleteByName('yamarket_info')
			&& Configuration::deleteByName('yamarket_gzip')
			&& Configuration::deleteByName('yamarket_combinations')
			&& Configuration::deleteByName('yamarket_shipping')
			&& Configuration::deleteByName('yamarket_currencies')
			&& Configuration::deleteByName('yamarket_availability')
			&& Configuration::deleteByName('yamarket_categories')
		);
	}

	public function getContent()
	{
		if (Tools::isSubmit('submityamarket'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= $this->displayError($err);
		}
		elseif (Tools::isSubmit('generate'))
		{
			$this->generate(true);
			$this->_html .= $this->displayConfirmation($this->l('Generating completed.'));
		}
		$this->_displayForm();
		return $this->_html;
	}

	private function initToolbar()
	{
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);
	        $this->toolbar_btn['refresh-cache'] = array(
	            'href' => 'index.php?controller=AdminModules&generate&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($this->context->cookie->id_employee)),
	            'desc' => $this->l('Generate')
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
					'label' => $this->l('Shop Name'),
					'desc' => $this->l('Shop name in yandex market'),
					'name' => 'yamarket_shop',
					'size' => 33,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Сompany name'),
					'desc' => $this->l('Your company name'),
					'name' => 'yamarket_company',
					'size' => 33,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Shipping cost'),
					'desc' => $this->l('Shipping cost in shop region'),
					'name' => 'yamarket_shippingcost',
					'size' => 33,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Information'),
					'desc' => $this->l('Information about minimal order cost, minimal product quantity or prepayment'),
					'name' => 'yamarket_info',
					'size' => 33,
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Gzip compression'),
					'desc' => $this->l('Compress export file'),
					'name' => 'yamarket_gzip',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'yamarket_gzip_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'yamarket_gzip_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Combinations'),
					'desc' => $this->l('Export combinations'),
					'name' => 'yamarket_combinations',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'yamarket_combinations_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'yamarket_combinations_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Shipping'),
					'desc' => $this->l('Delivery, pickup and store'),
					'name' => 'yamarket_shipping',
					'class' => 't',
					'is_bool' => false,
					'values' => array(
						'id'=>'id',
						'name'=>'label',
						'query'=>array(
							array(
								'id' => '[1]',
								'val' => 1,
								'label' => $this->l('Delivery availability')
							),
							array(
								'id' => '[2]',
								'val' => 1,
								'label' => $this->l('Pickup in store availability')
							),
							array(
								'id' => '[3]',
								'val' => 1,
								'label' => $this->l('Can buy in Store')
							)
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Currencies'),
					'desc' => $this->l('If not checked will be used default currency'),
					'name' => 'yamarket_currencies',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'yamarket_currencies_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'yamarket_currencies_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Availability'),
					'desc' => $this->l('Product availability'),
					'name' => 'yamarket_availability',
					'class' => 't',
					'is_bool' => false,
					'values' => array(
						array(
							'id' => 'yamarket_availability_0',
							'value' => 0,
							'label' => $this->l('All avaible')
						),
						array(
							'id' => 'yamarket_availability_1',
							'value' => 1,
							'label' => $this->l('If quantity >0, then avaible, else on request')
						),
						array(
							'id' => 'yamarket_availability_2',
							'value' => 2,
							'label' => $this->l('If quantity = 0, product not exported')
						),
						array(
							'id' => 'yamarket_availability_3',
							'value' => 3,
							'label' => $this->l('All on request')
						)
					)
				),

				array(
					'type' => 'categories',
					'label' => $this->l('Categories'),
					'desc' => $this->l('Categories to export. If necessary, subcategories must be checked too.'),
					'name' => 'yamarket_categories',
					'values' => array(
						'input_name' => 'yamarket_categories[]',
						'use_radio' => false,
						'use_search' => true,
						'selected_cat' => unserialize(Configuration::get('yamarket_categories')),
						'use_context' =>true,
						'disabled_categories' => array(1),
						'top_category'=>new Category(1),
						'trads'=>array(
							'selected'=>$this->l('selected'),
							'Collapse All' => $this->l('Collapse All'),
							'Expand All' => $this->l('Expand All'),
							'Check All' => $this->l('Check All'),
							'Uncheck All' => $this->l('Uncheck All'),
							'search' => $this->l('search'),
							'use_search' => true,
							'Root' => array(
								'name' => 'Root',
								'id_category' => 1
							)
						)
					)
				),
			),
			
			'submit' => array(
				'name' => 'submityamarket',
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		$this->fields_form[1]['form'] = array(
			'legend' => array(
				'title' => $this->l('Yandex Market configuration information') ,
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Static url'),
					'desc' => $this->l('URL to download file generated by cron or Export button.'),
					'name' => 'url1',
					'size' => 120,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Dinamic url'),
					'desc' => $this->l('URL to download dinamicaly generated export file.'),
					'name' => 'url2',
					'size' => 120,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Cron url'),
					'desc' => $this->l('URL to regenerate export file by cron.'),
					'name' => 'url3',
					'size' => 120,
				)
			)
		);

		$this->fields_value['yamarket_shop'] = Configuration::get('yamarket_shop');
		$this->fields_value['yamarket_company'] = Configuration::get('yamarket_company');
		$this->fields_value['yamarket_shippingcost'] = Configuration::get('yamarket_shippingcost');
		$this->fields_value['yamarket_info'] = Configuration::get('yamarket_info');
		$this->fields_value['yamarket_gzip'] = Configuration::get('yamarket_gzip');
		$this->fields_value['yamarket_combinations'] = Configuration::get('yamarket_combinations');
		$yamarket_shipping=unserialize(Configuration::get('yamarket_shipping'));
		if($yamarket_shipping)
			foreach($yamarket_shipping as $key=>$val)
				$this->fields_value['yamarket_shipping_['.$key.']'] = $val;
		$this->fields_value['yamarket_currencies'] = Configuration::get('yamarket_currencies');
		$this->fields_value['yamarket_availability'] = Configuration::get('yamarket_availability');

		$this->fields_value['url1'] = 'http://'.Tools::getHttpHost(false, true)._THEME_PROD_PIC_DIR_.'yml.xml'.(Configuration::get('yamarket_gzip') ? '.gz' : '');
		$this->fields_value['url2'] = $this->context->link->getModuleLink('yamarket', 'generate', array(), true);
		$this->fields_value['url3'] = $this->context->link->getModuleLink('yamarket', 'generate', array('cron'=>'1'), true);

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
		$helper->name_controller = 'yamarket';
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

	private function _postValidation()
	{
		if(Tools::getValue('yamarket_shop')&&(!Validate::isString(Tools::getValue('yamarket_shop'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Shop Name');
		if(Tools::getValue('yamarket_company')&&(!Validate::isString(Tools::getValue('yamarket_company'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Сompany name');
		if(Tools::getValue('yamarket_shippingcost')&&(!Validate::isPrice(Tools::getValue('yamarket_shippingcost'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Shipping cost');
		if(Tools::getValue('yamarket_info')&&(!Validate::isString(Tools::getValue('yamarket_info'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Information');
		if(Tools::getValue('yamarket_gzip')&&(!Validate::isBool(Tools::getValue('yamarket_gzip'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Gzip compression');
		if(Tools::getValue('yamarket_combinations')&&(!Validate::isBool(Tools::getValue('yamarket_combinations'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Combinations');
		if(Tools::getValue('yamarket_currencies')&&(!Validate::isBool(Tools::getValue('yamarket_currencies'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Currencies');
		if(Tools::getValue('yamarket_availability')&&(!Validate::isInt(Tools::getValue('yamarket_availability'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Availability');
		if(Tools::getValue('yamarket_categories')&&(!is_array(Tools::getValue('yamarket_categories'))))
			$this->_postErrors[] = $this->l('Invalid').' '.$this->l('Categories');
	}

	private function _postProcess()
	{
		Configuration::updateValue('yamarket_categories', serialize(Tools::getValue('yamarket_categories')));
		Configuration::updateValue('yamarket_shop', Tools::getValue('yamarket_shop'));
		Configuration::updateValue('yamarket_company', Tools::getValue('yamarket_company'));
		Configuration::updateValue('yamarket_shippingcost', (float)Tools::getValue('yamarket_shippingcost'));
		Configuration::updateValue('yamarket_info', Tools::getValue('yamarket_info'));
		Configuration::updateValue('yamarket_gzip', (int)Tools::getValue('yamarket_gzip'));
		Configuration::updateValue('yamarket_combinations', (int)Tools::getValue('yamarket_combinations'));
		Configuration::updateValue('yamarket_shipping', serialize(Tools::getValue('yamarket_shipping_')));
		Configuration::updateValue('yamarket_currencies', (int)Tools::getValue('yamarket_currencies'));
		Configuration::updateValue('yamarket_availability', (int)Tools::getValue('yamarket_availability'));
		$this->_html .= $this->displayConfirmation($this->l('Settings updated.'));
	}

	public function generate($cron=false)
	{
		include 'classes/ymlCatalog.php';
		//Язык по умолчанию
		$id_lang=(int)Configuration::get('PS_LANG_DEFAULT');
		//Валюта по умолчанию
		$currency_default=new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
		$this->currency_iso=$currency_default->iso_code;
		//Адрес магазина
		$shop_url='http://'.Tools::getHttpHost(false, true).__PS_BASE_URI__;
		//Категории для экспорта
		$yamarket_categories = unserialize(Configuration::get('yamarket_categories'));

		$yamarket_combinations=Configuration::get('yamarket_combinations');

		$this->yamarket_availability = Configuration::get('yamarket_availability');
		$this->yamarket_shipping = unserialize(Configuration::get('yamarket_shipping'));

		//создаем новый магазин
		$catalog = new ymlCatalog();
		$catalog->gzip = Configuration::get('yamarket_gzip');
		$shop = new ymlShop();
		$shop->name = Configuration::get('yamarket_shop');
		$shop->company = Configuration::get('yamarket_company');
		$shop->url = $shop_url;
		$shop->platform = 'PrestaShop';
		$shop->version = _PS_VERSION_;
		$shop->agency = 'PrestaLab';
		$shop->email = 'admin@prestalab.ru';

		//Валюты
		$shop->startTag(ymlCurrency::$collectionName);
		if(Configuration::get('yamarket_currencies'))
		{
			$currencies = Currency::getCurrencies();
			foreach ($currencies as $currency)
				$shop->add(new ymlCurrency(($currency['iso_code']), (float)$currency['conversion_rate']));
			unset($currencies);
		}
		else
			$shop->add(new ymlCurrency($currency_default->iso_code, (float)$currency_default->conversion_rate));
		$shop->endTag(ymlCurrency::$collectionName);

		//Категории
		$categories=Category::getCategories($id_lang, false, false);
		$shop->startTag(ymlCategory::$collectionName);
		foreach($categories as $category)
		{
			$shop->add(new ymlCategory($category['id_category'], $category['name'], $category['id_parent']));
		}
		$shop->endTag(ymlCategory::$collectionName);

		//Стоимость доставки
		$shop->addString('<local_delivery_cost>'.Configuration::get('yamarket_shippingcost').'</local_delivery_cost>');
		//Товары
		$shop->startTag(ymlOffer::$collectionName);
		foreach($categories as $category)
		{
			if(in_array($category['id_category'], $yamarket_categories))
			{
				$category_object=new Category ($category['id_category']);
				$products = $category_object->getProducts($id_lang, 1, 10000);
				if($products)
					foreach($products as $product)
					{
						if($product['id_category_default']!=$category['id_category'])
							continue;
						//Для комбинаций
						if($yamarket_combinations)
						{
							$product_object = new Product($product['id_product'], false, $id_lang);
							$combinations = $product_object->getAttributeCombinations($id_lang);
						}
						else
							$combinations = false;
						if (is_array($combinations) && count($combinations) > 0)
						{
							$combArray = array();
							foreach ($combinations AS $combination)
							{
								$combArray[$combination['id_product_attribute']]['id_product_attribute'] = $combination['id_product_attribute'];
								$combArray[$combination['id_product_attribute']]['price'] = Product::getPriceStatic($product['id_product'], true, $combination['id_product_attribute']);
								$combArray[$combination['id_product_attribute']]['reference'] = $combination['reference'];
								$combArray[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
								$combArray[$combination['id_product_attribute']]['quantity'] = $combination['quantity'];
								$combArray[$combination['id_product_attribute']]['attributes'][$combination['group_name']] = $combination['attribute_name'];
							}
							foreach($combArray as $combination)
							{
								self::_addProduct($shop, $product, $combination);
							}
						}
						else
							self::_addProduct($shop, $product);
					}
				unset($product);
			}
		}
		unset($categories);
		$shop->endTag(ymlOffer::$collectionName);
		$catalog->add($shop);

		if ($cron)
		{
			if($fp = fopen(dirname(__FILE__) . '/../../upload/yml.xml' . ($catalog->gzip ? '.gz' : ''), 'w'))
			{
				fwrite($fp, $catalog->generate());
				fclose($fp);
			}
		}
		else
		{
			if($catalog->gzip)
			{
				header('Content-type:application/x-gzip');
				header('Content-Disposition: attachment; filename=yml.xml.gz');
			}
			else
				header('Content-type:application/xml;  charset=windows-1251');
			echo $catalog->generate();
		}
	}

	private function _addProduct($shop, $product, $combination=false)
	{
		$quantity=(int)($combination?$combination['quantity']:$product['quantity']);

		//В наличии или под заказ
		$available='false';
		if($this->yamarket_availability==0){
			$available='true';
		}
		elseif($this->yamarket_availability==1){
			if($quantity>0)
				$available='true';
		}
		elseif($this->yamarket_availability==2){
			if($quantity==0)
				return;
			$available='true';
		}

		$offer = new ymlOffer($product['id_product'].($combination?'c'.$combination['id_product_attribute']:''),
			'',
			$available
		);
		//$offer->url = $product['link'].($combination?'#'.$combination['id_product_attribute']:'');
                $offer->url = 'http://www.zoo-cafe.ru/' . $product['category'] . '/' . $product['id_product'] . '-' . $product['link_rewrite'] . '.html' . ($combination ? '#/' .  Tools::link_rewrite(str_replace(array(',', '.'), '-', implode(' ', array_keys($combination['attributes'])))) . '-' . str_replace('-', '_', Tools::link_rewrite(str_replace(array(',', '.'), '-', implode(' ', $combination['attributes'])))) : '');
		$offer->price = Tools::ps_round(($combination?$combination['price']:$product['price']), 2);
		$offer->currencyId = $this->currency_iso;
		$offer->categoryId = $product['id_category_default'];
		$offer->picture = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image']);
		$offer->name = $product['name'] . ($combination ? ' ' . implode(', ', $combination['attributes']) : '');
		$offer->vendor = $product['manufacturer_name'];
		$offer->vendorCode = ($combination?$combination['reference']:$combination['reference']);
		$offer->description = $product['description'];
		$offer->sales_notes = Configuration::get('yamarket_info');
		$offer->barcode = ($combination?$combination['ean13']:$combination['ean13']);
		if(isset($this->yamarket_shipping[1])&&$this->yamarket_shipping[1])
			$offer->delivery='true';
		if(isset($this->yamarket_shipping[2])&&$this->yamarket_shipping[2])
			$offer->pickup='true';
		if(isset($this->yamarket_shipping[3])&&$this->yamarket_shipping[3])
			$offer->store='true';
		$params=array();
		if($product['features'])
			foreach($product['features'] as $feature)
				$params[$feature['name']]=$feature['value'];
		if($combination)
			$params=array_merge($params, $combination['attributes']);
		$offer->param = $params;

		$shop->add($offer);
	}

}
