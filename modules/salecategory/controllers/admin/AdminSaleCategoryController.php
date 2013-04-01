<?php
/**
* 
*
* @author BTConsulting <BTConsulting.dev@gmail.com>
* @copyright BTConsulting
* @license http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
* @version 2.0
*
*/

class AdminSaleCategoryController extends ModuleAdminController {

    private $_nbProcessedCategories;
    private $_nbProcessedProducts;
    
    private $_categoryTree;
    private $_confmsg;
    
    public function __construct()
    {
        $this->_nbProcessedCategories=0;
        $this->_nbProcessedProducts=0;
        
        $this->_categoryTree='';
        $this->_confmsg='';
    
        parent::__construct();
    }    
    
    public function setMedia()
    {
        parent::setMedia();
        
        $this->addJqueryUI(array(
            'ui.core',
            'ui.widget',
            'ui.slider',
            'ui.datepicker'
        ));
    
        $this->addJS(array(
            _PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js'
        ));

        $this->addCSS(array(
            _PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css',
        ));        
    }    
   
    public function display()
    {
        $categories = Category::getCategories($this->context->language->id,false);
        
        $this->_recurseCategory($categories, $categories[1][2], 2);
        $this->context->smarty->assign('categories', $this->_categoryTree);
        
        if ($this->_confmsg)
            $this->context->smarty->assign('conf', $this->_confmsg);
        
        parent::display();
    }  
    
    
    public function initProcess()
    {
        if (Tools::isSubmit('submitSetReductionSale'))
            $this->action = 'apply_sale_category';
        elseif (Tools::isSubmit('submitUnsetReductionSale'))
            $this->action = 'remove_sale_category';
    }
    
    public function processApplySaleCategory()
    {
        $this->_processSetCategoryRecurse(Tools::getValue('categories'),Tools::getValue('recurse')=="on"?true:false);
        
        if(empty($this->_errors))
            $this->_confmsg=$this->_nbProcessedCategories.' '.$this->l('Categories and').' '.$this->_nbProcessedProducts.' '.$this->l('Products successfully updated.');
    }
    
    public function processRemoveSaleCategory()
    {
        $this->_processUnsetCategoryRecurse(Tools::getValue('categories'),Tools::getValue('recurse')=="on"?true:false);
        
        if(empty($this->_errors))
            $this->_confmsg=$this->_nbProcessedCategories.' '.$this->l('Categories and').' '.$this->_nbProcessedProducts.' '.$this->l('Products successfully updated.');
    }    

    private function _processSetCategoryRecurse($id_category, $recurse)
    {
        $this->_nbProcessedCategories+=1;
        
        $reduction = (float)(Tools::getValue('sp_reduction'));
        $reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
        $from = Tools::getValue('date_from');
        $to = Tools::getValue('date_to');
        
        $sql='SELECT cp.`id_product`
              FROM `'._DB_PREFIX_.'category_product` cp
              WHERE cp.`id_category`='.$id_category;
        $query_result = Db::getInstance()->ExecuteS($sql);
    
        foreach($query_result as $row)
        {
            $this->_nbProcessedProducts+=1;
            
            $id_product = $row['id_product'];
      
            $id_shop = $this->context->cookie->shopContext?str_replace('s-', '', $this->context->cookie->shopContext):0;

            if(Tools::getValue('on_reduction')=="on")
            {
                
                $id_currency = Tools::getValue('sp_id_currency');
                $id_country = Tools::getValue('sp_id_country');
                $id_group = Tools::getValue('sp_id_group');
                $from_quantity = (int)(Tools::getValue('sp_from_quantity'))<1?1:Tools::getValue('sp_from_quantity');
                
                $from_date = !$from ? '0000-00-00 00:00:00' : $from;
                $to_date = !$to ? '0000-00-00 00:00:00' : $to;
                $sql = "DELETE FROM  `"._DB_PREFIX_."specific_price` WHERE `id_product` = ".$id_product."
                                                          AND  `id_shop` = 0
                                                          AND  `id_currency` = 0
                                                          AND  `id_country` = 0
                                                          AND  `id_group` = 0
                                                          AND  `from_quantity` = ".$from_quantity."
                                                          AND  `from` = '".$from_date."'
                                                          AND  `to` = '".$to_date."'";


                Db::getInstance()->Execute($sql);
                
                $specificPrice = new SpecificPrice();
                $specificPrice->id_product = $id_product;
                $specificPrice->id_shop = $id_shop;
                $specificPrice->id_currency = (int)($id_currency);
                $specificPrice->id_country = (int)($id_country);
                $specificPrice->id_group = (int)($id_group);
                $specificPrice->id_customer = 0;
                $specificPrice->price = (float)(-1);
                $specificPrice->from_quantity = (int)($from_quantity);
                $specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
                $specificPrice->reduction_type = $reduction_type;
                $specificPrice->from = !$from ? '0000-00-00 00:00:00' : $from;
                $specificPrice->to = !$to ? '0000-00-00 00:00:00' : $to;
                if (!$specificPrice->add())
                    $this->_errors[] = Tools::displayError('An error occurred while updating the specific price.');
            }
            if(Tools::getValue('on_sale')=="on")
            {
                $product = new Product($id_product, false, null, $id_shop);
                $product->on_sale = true;
                if(!$product->update())
                    $this->_errors[] = Tools::displayError('An error occurred while updating product '.$id_product.'.');
            }
        }
        if($recurse)
        {
            $sql='SELECT c.`id_category`
                  FROM `'._DB_PREFIX_.'category` c
                  WHERE c.`id_parent`='.$id_category.
                  ' AND c.`active`=1';
            $query_result = Db::getInstance()->ExecuteS($sql);
        
            foreach($query_result as $row)
            {
                $this->_processSetCategoryRecurse($row['id_category'], $recurse);
            }
        }
    }
    
    
    private function _processUnsetCategoryRecurse($id_category, $recurse)
    {
        global $cookie, $currentIndex;

        $this->_nbProcessedCategories+=1;

        $sql='SELECT cp.`id_product`
              FROM `'._DB_PREFIX_.'category_product` cp
              WHERE cp.`id_category`='.$id_category;
        $query_result = Db::getInstance()->ExecuteS($sql);

        foreach($query_result as $row)
        {
            $this->_nbProcessedProducts+=1;

            $id_product = $row['id_product'];

            if(Tools::getValue('on_reduction')=="on")
                if(!SpecificPrice::deleteByProductId($id_product))
                    $this->_errors[] = Tools::displayError('An error occurred while deleting the specific price.');
            
            if(Tools::getValue('on_sale')=="on")
            {
                $product = new Product($id_product);
                $product->on_sale = false;
                if(!$product->update())
                    $this->_errors[] = Tools::displayError('An error occurred while updating product '.$id_product.'.');
            }
        }
        
        if($recurse)
        {
            $sql='SELECT c.`id_category`
                  FROM `'._DB_PREFIX_.'category` c
                  WHERE c.`id_parent`='.$id_category.
                  ' AND c.`active`=1';
            $query_result = Db::getInstance()->ExecuteS($sql);
        
            foreach($query_result as $row)
            {
                $this->_processUnsetCategoryRecurse($row['id_category'], $recurse);
            }
        }
    }

    private function _recurseCategory($categories, $current, $id_category = 1, $id_selected = 1)
    {
        $this->_categoryTree.='<option value="'.$id_category.'"'.(($id_selected == $id_category) ? ' selected="selected"' : '').'>'.str_repeat('&nbsp;', ($current['infos']['level_depth']-1) * 5).stripslashes($current['infos']['name']).'</option>';
        if (isset($categories[$id_category]))
            foreach (array_keys($categories[$id_category]) as $key)
                $this->_recurseCategory($categories, $categories[$id_category][$key], $key, $id_selected);
    }

}