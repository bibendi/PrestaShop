<?php
/* version 1.5.3.1 */
class AdminProductsController extends AdminProductsControllerCore
{
	public function processFeatures()
	{
		if (!Feature::isFeatureActive())
			return;

		if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product'))))
		{
			// delete all objects
			$product->deleteFeatures();

			// add new objects
			$languages = Language::getLanguages(false);
			foreach ($_POST as $key => $val)
			{
				if (preg_match('/^feature_([0-9]+)_value/i', $key, $match))
				{
					// Mellow => Mutiple feature values modification
                    if ($val && $val[0] != 0)
						foreach ($val AS $feature_val) $product->addFeaturesToDB($match[1], $feature_val);
					// Mellow => End of modification
					else
					{
						if ($default_value = $this->checkFeatures($languages, $match[1]))
						{
							$id_value = $product->addFeaturesToDB($match[1], 0, 1);
							foreach ($languages as $language)
							{
								if ($cust = Tools::getValue('custom_'.$match[1].'_'.(int)$language['id_lang']))
									$product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $cust);
								else
									$product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $default_value);
							}
						}
					}
				}
			}
		}
		else
			$this->errors[] = Tools::displayError('Product must be created before adding features.');
	}

	public function initFormFeatures($obj)
	{
		$data = $this->createTemplate($this->tpl_form);
		if (!Feature::isFeatureActive())
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
		else
		{
			if ($obj->id)
			{
				if ($this->product_exists_in_shop)
				{
					$features = Feature::getFeatures($this->context->language->id, (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP));

					// Mellow => Mutiple feature values modification
                    foreach ($features as $k => $tab_features)
					{
						$features[$k]['current_item'] = false;
						$features[$k]['val'] = array();

						$features[$k]['custom'] = true;
						foreach ($obj->getFeatures() as $tab_products)
							if ($tab_products['id_feature'] == $tab_features['id_feature'])
                                $features[$k]['current_item'][] = $tab_products['id_feature_value'];

                        if (!$features[$k]['current_item']) $features[$k]['current_item'][0] = null;
                            $features[$k]['featureValues'] = FeatureValue::getFeatureValuesWithLang($this->context->language->id, (int)$tab_features['id_feature']);
						
						if (count($features[$k]['featureValues']))
							foreach ($features[$k]['featureValues'] as $value)
								if (in_array($value['id_feature_value'], $features[$k]['current_item']))
									$features[$k]['custom'] = false;

						if ($features[$k]['custom'])
                            $features[$k]['val'] = FeatureValue::getFeatureValueLang($features[$k]['current_item'][0]);
					}
					// Mellow => End of modification

					$data->assign('available_features', $features);

					$data->assign('product', $obj);
					$data->assign('link', $this->context->link);
					$data->assign('languages', $this->_languages);
					$data->assign('default_form_language', $this->default_form_language);
				}
				else
					$this->displayWarning($this->l('You must save this product in this shop before adding features.'));
			}
			else
				$this->displayWarning($this->l('You must save this product before adding features.'));
		}
		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}
}