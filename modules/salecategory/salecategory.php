<?php
/**
* 
*
* @author BTConsulting <BTConsulting.dev@gmail.com>
* @copyright BTConsulting
* @license http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
* @version 1.5
*
*/
if (!defined('_CAN_LOAD_FILES_'))
exit;

class salecategory extends Module
{


	public function __construct()
	{
		global $cookie;

		$this->name = 'salecategory';
		$this->tab = 'pricing_promotion';
		$this->version = 1.5;

		$this->displayName = $this->l('Sale Category');
		$this->description = $this->l('Set products in category in sale with reduction');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall the module ').$this->name;

		parent::__construct();
	}

	public function install()
	{
		return (parent::install() 
		AND $this->installModuleTab('AdminSaleCategory', array(1=>'Sale Category', 2=>'Solde Categorie'), 'AdminCatalog')
		);
	}
	
	public function uninstall()
	{
		return (parent::uninstall() 
		AND $this->uninstallModuleTab('AdminSaleCategory')
		);
	}


	private function installModuleTab($tabClass, $tabName, $nameTabParent)
	{
      $idTabParent = Db::getInstance()->getValue("SELECT `id_tab` FROM `"._DB_PREFIX_."tab` WHERE `class_name`='".$nameTabParent."'");
      
  		$tab = new Tab();
  		$languages=Language::getLanguages(false);
  		$tabNameAllLanguages=array();
  		foreach($languages as $language)
  		{
  		      if(isset($tabName[$language['id_lang']]))
  			       $tabNameAllLanguages[$language['id_lang']]=$tabName[$language['id_lang']];
  		      else
  			       $tabNameAllLanguages[$language['id_lang']]=$tabName[1];
  		}
  		$tab->name = $tabNameAllLanguages;
  		$tab->class_name = $tabClass;
  		$tab->module = $this->name;
  		$tab->id_parent = $idTabParent;
  		return ($tab->save());
	} 

	private function uninstallModuleTab($tabClass)
	{
		$idTab = Tab::getIdFromClassName($tabClass);
		if($idTab != 0)
		{
			$tab = new Tab($idTab);
			$tab->delete();
			return true;
		}
		return false;
	}    

}
?>