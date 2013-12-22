<?php
/**
 * Класс товарного предложения
 * @author 0RS <admin@prestalab.ru>
 * @version 0.1
 * @package
 * @link http://prestalab.ru/
 */

class ymlOffer extends ymlElement
{
	public static $collectionName = 'offers';
	protected $element = 'offer';
	protected $generalAttributes = array('id'=>'', 'type'=>'', 'available'=>'true', 'bid'=>'');
	protected $generalProperties = array('url'=>'','price'=>'','currencyId'=>'RUB','categoryId'=>'1','picture'=>array(),'store'=>'false','pickup'=>'false','delivery'=>'false','local_delivery_cost'=>'','name'=>'','vendor'=>'','vendorCode'=>'','description'=>'','sales_notes'=>'','country_of_origin'=>'','adult'=>'','barcode'=>'','param'=>array());

	function __construct($id, $type, $available='true', $bid=false)
	{
		$this->id = $id;
		$this->type = $type;
		$this->available = $available;
		$this->bid = $bid;
	}
}