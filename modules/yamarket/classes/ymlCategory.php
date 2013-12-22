<?php
/**
 * Класс категорий товара
 * @author 0RS <admin@prestalab.ru>
 * @version 0.1
 * @package
 * @link http://prestalab.ru/
 */

class ymlCategory extends ymlElement
{
	public static $collectionName = 'categories';
	protected $element = 'category';
	protected $generalAttributes = array('id'=>'1', 'parentId'=>'');

	function __construct($id='1', $name='', $parentId='')
	{
		$this->id = $id;
		$this->tagContent = self::PrepareString($name);
		$this->parentId = $parentId;
	}
}