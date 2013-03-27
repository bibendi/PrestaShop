<?php
	require_once(dirname(__FILE__).'/../../config/config.inc.php');
	require_once(dirname(__FILE__).'/../../init.php');

	$color = "default";
	if(isset($_GET['c']))
		$color = $_GET['c'];

	//Configuration::updateValue('COLOR_THEME', $color);
	global $cookie;
	$cookie->__set('buddie_color',$color);
	header( 'Location: '.$_SERVER['HTTP_REFERER'] ) ;
?>