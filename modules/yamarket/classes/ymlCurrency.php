<?php
/**
 * Класс валюты
 * @author 0RS <admin@prestalab.ru>
 * @version 0.1
 * @package
 * @link http://prestalab.ru/
 */

class ymlCurrency extends ymlElement
{
	public static $collectionName = 'currencies';
	protected $element = 'currency';
	protected $generalAttributes = array('id'=>'RUB', 'rate'=>'1', 'plus'=>'');

	function __construct($id='RUB', $rate='1')
	{
		if (!in_array($id, array('RUR', 'RUB', 'USD', 'BYR', 'KZT', 'EUR', 'UAH')))
			return false;
		$this->id = $id;
		$this->rate = $rate;
	}
}