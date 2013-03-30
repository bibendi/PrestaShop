<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CategoryControllerCore extends FrontController
{
	public $php_self = 'category';
	protected $category;
	public $customer_access = true;

	/**
	 * Set default medias for this controller
	 */
	public function setMedia()
	{
		parent::setMedia();

		if ($this->context->getMobileDevice() == false)
		{
			//TODO : check why cluetip css is include without js file
			$this->addCSS(array(
				_THEME_CSS_DIR_.'scenes.css' => 'all',
				_THEME_CSS_DIR_.'category.css' => 'all',
				_THEME_CSS_DIR_.'product_list.css' => 'all',
			));

			if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0)
				$this->addJS(_THEME_JS_DIR_.'products-comparison.js');
		}
	}

	public function canonicalRedirection($canonicalURL = '')
	{
		if (!Validate::isLoadedObject($this->category) || !$this->category->inShop() || !$this->category->isAssociatedToShop())
		{
			$this->redirect_after = '404';
			$this->redirect();
		}
		if (!Tools::getValue('noredirect') && Validate::isLoadedObject($this->category))
			parent::canonicalRedirection($this->context->link->getCategoryLink($this->category));
	}

	/**
	 * Initialize category controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		// Get category ID
		$id_category = (int)Tools::getValue('id_category');
		if (!$id_category || !Validate::isUnsignedId($id_category))
			$this->errors[] = Tools::displayError('Missing category ID');

		// Instantiate category
		$this->category = new Category($id_category, $this->context->language->id);

		parent::init();
		//check if the category is active and return 404 error if is disable.
		if (!$this->category->active)
		{
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
		}
		//check if category can be accessible by current customer and return 403 if not
		if (!$this->category->checkAccess($this->context->customer->id))
		{
			header('HTTP/1.1 403 Forbidden');
			header('Status: 403 Forbidden');
			$this->errors[] = Tools::displayError('You do not have access to this category.');
			$this->customer_access = false;
		}
	}
	
	public function initContent()
	{
		parent::initContent();
		
		$this->setTemplate(_PS_THEME_DIR_.'category.tpl');
		
		if (!$this->customer_access)
			return;

		if (isset($this->context->cookie->id_compare))
			$this->context->smarty->assign('compareProducts', CompareProduct::getCompareProducts((int)$this->context->cookie->id_compare));

		$this->productSort(); // Product sort must be called before assignProductList()
		
		$this->assignScenes();
		$this->assignSubcategories();
		if ($this->category->id != 1)
			$this->assignProductList();

        $this->assignProductsAttributesGroups();
        $this->assignProductsAttributesCombinations();

		$this->context->smarty->assign(array(
			'category' => $this->category,
			'products' => (isset($this->cat_products) && $this->cat_products) ? $this->cat_products : null,
			'id_category' => (int)$this->category->id,
			'id_category_parent' => (int)$this->category->id_parent,
			'return_category_name' => Tools::safeOutput($this->category->name),
			'path' => Tools::getPath($this->category->id),
			'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'categorySize' => Image::getSize(ImageType::getFormatedName('category')),
			'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			'thumbSceneSize' => Image::getSize(ImageType::getFormatedName('m_scene')),
			'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
			'allow_oosp' => (int)Configuration::get('PS_ORDER_OUT_OF_STOCK'),
			'comparator_max_item' => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
			'suppliers' => Supplier::getSuppliers()
		));
	}

	/**
	 * Assign scenes template vars
	 */
	protected function assignScenes()
	{
		// Scenes (could be externalised to another controler if you need them)
		$scenes = Scene::getScenes($this->category->id, $this->context->language->id, true, false);
		$this->context->smarty->assign('scenes', $scenes);

		// Scenes images formats
		if ($scenes && ($sceneImageTypes = ImageType::getImagesTypes('scenes')))
		{
			foreach ($sceneImageTypes as $sceneImageType)
			{
				if ($sceneImageType['name'] == ImageType::getFormatedName('m_scene'))
					$thumbSceneImageType = $sceneImageType;
				elseif ($sceneImageType['name'] == ImageType::getFormatedName('scene'))
					$largeSceneImageType = $sceneImageType;
			}

			$this->context->smarty->assign(array(
				'thumbSceneImageType' => isset($thumbSceneImageType) ? $thumbSceneImageType : null,
				'largeSceneImageType' => isset($largeSceneImageType) ? $largeSceneImageType : null,
			));
		}
	}

	/**
	 * Assign sub categories templates vars
	 */
	protected function assignSubcategories()
	{
		if ($subCategories = $this->category->getSubCategories($this->context->language->id))
		{
			$this->context->smarty->assign(array(
				'subcategories' => $subCategories,
				'subcategories_nb_total' => count($subCategories),
				'subcategories_nb_half' => ceil(count($subCategories) / 2)
			));
		}
	}

	/**
	 * Assign list of products template vars
	 */
	public function assignProductList()
	{
		$hookExecuted = false;
		Hook::exec('actionProductListOverride', array(
			'nbProducts' => &$this->nbProducts,
			'catProducts' => &$this->cat_products,
			'hookExecuted' => &$hookExecuted,
		));

		// The hook was not executed, standard working
		if (!$hookExecuted)
		{
			$this->context->smarty->assign('categoryNameComplement', '');
			$this->nbProducts = $this->category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true);
			$this->pagination((int)$this->nbProducts); // Pagination must be call after "getProducts"
			$this->cat_products = $this->category->getProducts($this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);
		}
		// Hook executed, use the override
		else
			// Pagination must be call after "getProducts"
			$this->pagination($this->nbProducts);

		foreach ($this->cat_products as &$product)
		{
			if ($product['id_product_attribute'] && isset($product['product_attribute_minimal_quantity']))
				$product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
		}

		$this->context->smarty->assign('nb_products', $this->nbProducts);
	}

    protected function assignProductsAttributesGroups(){
        $products_groups = array();
        $products_combinations = array();
        $products_colors = array();
        $products_combination_images = array();

        foreach ($this->cat_products as $cat_product)
        {
            $product = new Product (intval($cat_product['id_product']), true , $this->context->language->id);
            $colors = array();
            $groups = array();
            $combinations = array();
            $attributes_groups = $product->getAttributesGroups($this->context->language->id);

            if (is_array($attributes_groups) && $attributes_groups)
            {
                $combination_images = $product->getCombinationImages($this->context->language->id);
                $combination_prices_set = array();

                foreach ($attributes_groups as $k => $row)
                {
                    // Color management
                    if ((isset($row['attribute_color']) && $row['attribute_color']) || (file_exists(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg')))
                    {
                        $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                        $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                        if (!isset($colors[$row['id_attribute']]['attributes_quantity']))
                            $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                        $colors[$row['id_attribute']]['attributes_quantity'] += (int)$row['quantity'];
                    }
                    if (!isset($groups[$row['id_attribute_group']]))
                        $groups[$row['id_attribute_group']] = array(
                            'name' => $row['public_group_name'],
                            'group_type' => $row['group_type'],
                            'default' => -1,
                        );

                    $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                    if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1)
                        $groups[$row['id_attribute_group']]['default'] = (int)$row['id_attribute'];
                    if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']]))
                        $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];

                    $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                    $combinations[$row['id_product_attribute']]['attributes'][] = (int)$row['id_attribute'];
                    $combinations[$row['id_product_attribute']]['price'] = (float)$row['price'];

                    // Call getPriceStatic in order to set $combination_specific_price
                    if (!isset($combination_prices_set[(int)$row['id_product_attribute']]))
                    {
                        Product::getPriceStatic((int)$product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                        $combination_prices_set[(int)$row['id_product_attribute']] = true;
                        $combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                    }
                    $combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
                    $combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
                    $combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
                    $combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                    $combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                    $combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                    if ($row['available_date'] != '0000-00-00')
                        $combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    else
                        $combinations[$row['id_product_attribute']]['available_date'] = '';

                    if (isset($combination_images[$row['id_product_attribute']][0]['id_image']))
                        $combinations[$row['id_product_attribute']]['id_image'] = $combination_images[$row['id_product_attribute']][0]['id_image'];
                    else
                        $combinations[$row['id_product_attribute']]['id_image'] = -1;
                }
                // wash attributes list (if some attributes are unavailables and if allowed to wash it)
                if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0)
                {
                    foreach ($groups as &$group)
                        foreach ($group['attributes_quantity'] as $key => &$quantity)
                            if (!$quantity)
                                unset($group['attributes'][$key]);

                    foreach ($colors as $key => $color)
                        if (!$color['attributes_quantity'])
                            unset($colors[$key]);
                }
                foreach ($combinations as $id_product_attribute => $comb)
                {
                    $attribute_list = '';
                    foreach ($comb['attributes'] as $id_attribute)
                        $attribute_list .= '\''.(int)$id_attribute.'\',';
                    $attribute_list = rtrim($attribute_list, ',');
                    $combinations[$id_product_attribute]['list'] = $attribute_list;
                }

                $products_groups[$product->id] = $groups;
                $products_combinations[$product->id] = $combinations;
                $products_colors[$product->id] = (count($colors)) ? $colors : false;
                $products_combination_images[$product->id] = $combination_images;
            }
        }

        $this->context->smarty->assign(array(
            'productsGroups' => $products_groups,
            'productsCombinations' => $products_combinations,
            'productsColors' => $products_colors,
            'productsCombinationImages' => $products_combination_images));
        //die(var_dump(array($products_groups, $products_combinations)));
    }

    protected function assignProductsAttributesCombinations()
    {
        $products_attributes_combinations = array();

        foreach ($this->cat_products as $cat_product)
        {
            $product_id = intval($cat_product['id_product']);
            $attributes_combinations = Product::getAttributesInformationsByProduct($product_id);
            foreach ($attributes_combinations as &$ac)
                foreach ($ac as &$val)
                    $val = str_replace('-', '_', Tools::link_rewrite(str_replace(array(',', '.'), '-', $val)));
            $products_attributes_combinations[$product_id] = $attributes_combinations;
        }
        $this->context->smarty->assign('productsAttributesCombinations', $products_attributes_combinations);
        //die(var_dump($products_attributes_combinations));
    }
}

