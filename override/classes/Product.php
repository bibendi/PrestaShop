<?php
class Product extends ProductCore
{
    public static function getFrontFeaturesStatic($id_lang, $id_product)
    {
        $process = (!array_key_exists($id_product.'-'.$id_lang, parent::$_frontFeaturesCache));
        $features = parent::getFrontFeaturesStatic($id_lang, $id_product);

        //PWeb: join multiple values of one feature into one value
        if ($process AND count($features)) {
            $features_key = array();
            foreach ($features as $key => $feature) {
                if (!array_key_exists($feature['name'], $features_key)) {
                    $features_key[$feature['name']] = $key;
                } else {
                    $features[ (int)$features_key[$feature['name']] ]['value'] .= ', ' . $feature['value'] ;
                    unset($features[$key]);
                }
            }
            parent::$_frontFeaturesCache[$id_product.'-'.$id_lang] = $features;
        }

        return $features;
    }
}
?>