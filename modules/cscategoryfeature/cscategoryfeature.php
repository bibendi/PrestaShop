<?php

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class CsCategoryFeature extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'cscategoryfeature';
		$this->tab = 'MyBlock';
		$this->version = '1.0';
		$this->author = 'Codespot';
		parent::__construct();

		$this->displayName = $this->l('Feature Product by Category');
		$this->description = $this->l('Add a block displaying Feature product by category in category page.');
	}

	public function install()
	{
		if (!Hook::get("categoryFeature"))
		{
			DB::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."hook`
                            SET `name`= 'categoryFeature',
                                `title`= 'Category feature product'
                           ");
		}
		if (!parent::install() OR
				!$this->registerHook('header') OR
				!$this->registerHook('categoryFeature'))
			return false;
		return true;
	}
	
	public function getCategoryFeatureProducts($id_lang, $idcategory, $n)
	{
		global $cookie;
		$id_supplier = (int)(Tools::getValue('id_supplier'));
		$id_cate_home = Configuration::get('PS_HOME_CATEGORY');
		$sql = '
		SELECT p.*, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new,
			(p.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
		FROM `'._DB_PREFIX_.'category_product` cp
		LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
		                                           AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
	                                           	   AND tr.`id_state` = 0)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
		WHERE cp.`id_category` = '.(int)($idcategory).' AND p.`active` = 1' .'
		'.($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '').' AND cp.`id_product` in (select id_product from `'._DB_PREFIX_.'category_product` where id_category = '.$id_cate_home.' )';

		$sql .= ' ORDER BY RAND()';
		$sql .= ' LIMIT 0, '.(int)($n);

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

		if (!$result)
			return false;

		/* Modify SQL result */
		return Product::getProductsProperties($id_lang, $result);
	}
	
	function hookHeader($params)
	{
		global $smarty;
		$smarty->assign(array(
			'HOOK_CATEGORY_FEATURE' => Hook::Exec('categoryFeature')
		));
	}
	
	function hookCategoryFeature($params)
	{
		global $smarty;
		if (isset($_GET['id_category']))
		{
			$feature_products =$this->getCategoryFeatureProducts((int)Context::getContext()->language->id, $_GET['id_category'], 4);
			$smarty->assign(array(
				'feature_products' => $feature_products,
				'ps_manu_img_dir' => _PS_MANU_IMG_DIR_));
			return $this->display(__FILE__, 'cscategoryfeature.tpl');
		}
		
	}
}


