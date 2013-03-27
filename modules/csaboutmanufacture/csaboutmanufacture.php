<?php
if (!defined('_PS_VERSION_'))
	exit;
class CsAboutManufacture extends Module
{
	function __construct()
    {
        $this->name = 'csaboutmanufacture';
        $this->tab = 'mymodule';
        $this->version = 1.0;
		$this->author = 'Codespot';

        parent::__construct();

		$this->displayName = $this->l('Tab manufacturer');
        $this->description = $this->l('Displays a tab about of manufacturers on the product page');
    }
	function install()
    {
        return (parent::install() AND $this->registerHook('productTab') AND $this->registerHook('productTabContent'));
    }
	
	public function hookProductTab()
	{
		return $this->display(__FILE__, 'producttab.tpl');
	}
	
	public function hookProductTabContent()
	{
		if (isset($_GET['id_product']))
		{
			$pro = new Product($_GET['id_product']);
			$manu = new Manufacturer ($pro->id_manufacturer);
			$manu_des = $manu->description[(int)Context::getContext()->language->id];
			$this->smarty->assign(array('manu_des' => $manu_des));
			return $this->display(__FILE__, 'producttabcontent.tpl');
		}
		
	}
	
}
?>