<?php
/**
 * Класс каталога
 * @author 0RS <admin@prestalab.ru>
 * @version 0.1
 * @package
 * @link http://prestalab.ru/
 */
require_once 'ymlElement.php';
require_once 'ymlShop.php';
require_once 'ymlCurrency.php';
require_once 'ymlCategory.php';
require_once 'ymlOffer.php';

class ymlCatalog extends ymlElement
{
	protected $element = 'yml_catalog';
	protected $generalAttributes = array('date'=>'');
	public $gzip=false;

	function __construct()
	{
		$this->date = date("Y-m-d H:i");
	}

	public function generate()
	{
		$tmp='<?xml version="1.0" encoding="windows-1251"?>
		<!DOCTYPE yml_catalog SYSTEM "shops.dtd">';
		$tmp.=parent::generate();
		if($this->gzip&&function_exists('gzencode')){
			$tmp = gzencode($tmp, 9);
		}
		return $tmp;
	}
}
