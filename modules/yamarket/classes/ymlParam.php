<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 0rs
 * Date: 25.05.12
 * Time: 15:21
 * To change this template use File | Settings | File Templates.
 */
class ymlParam
{
	protected $element = 'currency';
	protected $generalAttributes = array('name'=>'');

	function __construct($name, $value)
	{
		$this->name = self::PrepareString($name);
		$this->tagContent = self::PrepareString($value);
	}
}
