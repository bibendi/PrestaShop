<?php
/*
 * Prestashop interface for eCommerce Manager app
 * KIS Software - www.kis-ecommerce.com
 * Version: 3.0
 */

require_once('config/settings.inc.php');

function __autoload($className)
{
	if (!class_exists($className, false))
	{
		$ps_version = explode('.', _PS_VERSION_);
		$ps_version_ge_1_4 = (($ps_version[0] > 1) || (($ps_version[0] == 1) && ($ps_version[1] >= 4)));
		$ps_version_ge_1_5 = (($ps_version[0] > 1) || (($ps_version[0] == 1) && ($ps_version[1] >= 5)));
		
		if ($ps_version_ge_1_5) {
		    require_once('config/autoload.php');
		    Autoload::getInstance()->load($className);
		} else if (file_exists('classes/'.$className.'.php')) { 
			require_once('classes/'.str_replace(chr(0), '', $className).'.php');			
			if ($ps_version_ge_1_4) {
				if (file_exists('override/classes/'.$className.'.php')) {
					require_once('override/classes/'.$className.'.php');
				} else {
					$coreClass = new ReflectionClass($className.'Core');
					if ($coreClass->isAbstract())
						eval('abstract class '.$className.' extends '.$className.'Core {}');
					else
						eval('class '.$className.' extends '.$className.'Core {}');
				}
			}
		} else {
			return;
		}
	}
}

require_once('config/defines.inc.php');
require_once('images.inc.php');

define('_PS_MAGIC_QUOTES_GPC_', get_magic_quotes_gpc()); // do not configure this
define('_PS_MODULE_DIR_', _PS_ROOT_DIR_ . '/modules/'); // do not configure this
define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string')); // do not configure this

Configuration::loadConfiguration();

define('AES_KEY', '0234567890123450'); // configure this, length must be 16, 24 or 32

define('MAIN_LANGUAGE_ID', '1'); // optional to configure (if not found, defaults to first in database table)
define('MAIN_CURRENCY_ID', '1'); // optional to configure (if not found, defaults to first in database table)
define('MAIN_EMPLOYEE_ID', '1'); // optional to configure (if not found, defaults to first in database table)

define('STATISTICS_ORDER_STATUSES', '5'); // optional to configure
define('PRODUCTS_QUANTITY_LOW_ALERTS_VALUES', '0,3,5'); // optional to configure

define('IMAGES_DIR', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER["SCRIPT_NAME"]) . '/img/p/'); // optional to configure
define('FTP_IMAGES_UPLOAD_DIR', '/public_html/' . dirname($_SERVER["SCRIPT_NAME"]) . '/img/p/'); // optional to configure

define('EXTRA_IMAGES', 'unlimited'); // do not configure this
define('IMAGES_RES_DIR', ''); // do not configure this
define('PRODUCT_IMAGE_DIM', '64'); // do not configure this
define('MAX_INPUT_LENGTH', '100'); // do not cofigure this
define('VERSION', 'presta_v7'); // do not configure this
define('TIMEOUT', '30'); // do not configure this
define('AUTH_IP', false); // do not configure this

require_once('ecommerceapp_aes.php');
require_once('ecommerceapp_utils.php');

set_time_zone();

error_reporting(0);

//require_once('ecommerceapp_test.php'); // uncomment to run tests

	try {
		if (!isset($_REQUEST["ip"])) {
			print(time() . '?' . get_ip_address());
			exit();	
		}

		// Authenticate client
		$time_ip = decryptString($_REQUEST['ip']);
		list($time, $ip_address) = explode("?", $time_ip, 2);		
		if (time() > $time + TIMEOUT) {
			throw new Exception("Authentication timeout, delta = " . (time() - $time) . " sec");		
		}
		if (AUTH_IP) {
			if (strcmp($ip_address, get_ip_address()) != 0) {
				throw new Exception("Client ip address does not match! posted = " . 
					$ip_address . ", client = " . get_ip_address());	
			}
		}
		
		// Process action
		$time_action = decryptString($_REQUEST['action']);
		list($time, $action) = explode("?", $time_action, 2);
		if (time() > $time + TIMEOUT) {
			return new Exception("Action timeout, delta = " . (time() - $time) . " sec");			
		}
		if (isset($_REQUEST["attr"])) {
			$time_attr = decryptString($_REQUEST['attr']);
			list($time, $attr) = explode("?", $time_attr, 2);
			$attr = pSQL($attr);
			if (time() > $time + TIMEOUT) {
				return new Exception("Attribute timeout, delta = " . (time() - $time) . " sec");			
			}		
		}
		if (isset($_REQUEST["extra"])) {
			$time_extra = decryptString($_REQUEST['extra']);
			list($time, $extra) = explode(";#", $time_extra, 2);
			$extra = pSQL($extra);
			if (time() > $time + TIMEOUT) {
				return new Exception("Extra info timeout, delta = " . (time() - $time) . " sec");			
			}			
		} 

		if (strcmp($action, "notifications") == 0) {
			if (not_null($attr)) { print_notifications($attr); } else { throw new Exception("Notifications, Attr error!"); }
		} else if (strcmp($action, "ordersmeta") == 0) {
			if (not_null($attr)) { print_orders_meta($attr); } else { throw new Exception("OrdersMeta, No last sync timestamp!"); }
		} else if (strcmp($action, "orders") == 0) {
			if (not_null($attr)) { print_orders($attr, $extra); } else { throw new Exception("Orders, No last sync timestamp!"); }
		} else if (strcmp($action, "orderids") == 0) {
			if (not_null($attr)) { print_order_ids($attr); } else { throw new Exception("OrderIds, Attr error!"); }
		} else if (strcmp($action, "singleorder") == 0) {
			if (not_null($attr)) { print_single_order($attr); } else { throw new Exception("SingleOrder, No order id!"); }
		} else if (strcmp($action, "productsmeta") == 0) {
			if (not_null($attr)) { print_products_meta($attr); } else { throw new Exception("ProductsMeta, No last sync timestamp!"); }
		} else if (strcmp($action, "products") == 0) {
			if (not_null($attr)) { print_products($attr, $extra); } else { throw new Exception("Products, No last sync timestamp!"); }
		} else if (strcmp($action, "productids") == 0) {
			if (not_null($attr)) { print_product_ids($attr); } else { throw new Exception("ProductIds, Attr error!"); }
		} else if (strcmp($action, "singleproduct") == 0) {
			if (not_null($attr)) { print_single_product($attr); } else { throw new Exception("SingleProduct, No product id!"); }
		} else if (strcmp($action, "customersmeta") == 0) {
			if (not_null($attr)) { print_customers_meta($attr); } else { throw new Exception("CustomersMeta, No last sync timestamp!"); }
		} else if (strcmp($action, "customers") == 0) {
			if (not_null($attr)) { print_customers($attr, $extra); } else { throw new Exception("Customers, No last sync timestamp!"); }
		} else if (strcmp($action, "customerids") == 0) {
			if (not_null($attr)) { print_customer_ids($attr); } else { throw new Exception("CustomerIds, Attr error!"); }
		} else if (strcmp($action, "singlecustomer") == 0) {
			if (not_null($attr)) { print_single_customer($attr); } else { throw new Exception("SingleCustomer, No customer id!"); }
		} else if (strcmp($action, "onlinecustomersids") == 0) {
			print_online_customers(); 
		} else if (strcmp($action, "orderstatuses") == 0) {			
			print_order_statuses();
		} else if (strcmp($action, "categories") == 0) {
			print_categories();
		} else if (strcmp($action, "changeorderstatus") == 0) {
			if (not_null($attr) || not_null($extra)) { change_order_status($attr, $extra); } else { throw new Exception("ChangeOrderStatus, Attr Extra error!"); }
		} else if (strcmp($action, "addorderstatusmessage") == 0) { 
			if (not_null($attr)) { add_order_status_message($attr, $extra); } else { throw new Exception("AddOrderMessage, Attr error!"); }
		} else if (strcmp($action, "changeproductprice") == 0) {
			if (not_null($attr)) { change_product_price($attr); } else { throw new Exception("ChangeProductPrice, Attr error!"); }		
		} else if (strcmp($action, "changeproductimage") == 0) {
 			if (not_null($attr)) { change_product_image($attr); } else { throw new Exception("ChangeProductImage, Attr error!"); }
		} else if (strcmp($action, "unlinkproductimage") == 0) {
			if (not_null($attr)) { unlink_product_image($attr); } else { throw new Exception("UnlinkProductImage, Attr error!"); }
		} else if (strcmp($action, "changeproductavailability") == 0) { 
			if (not_null($attr)) { change_product_availability($attr); } else { throw new Exception("ChangeProductAvailability, Attr error!"); }		
		} else if (strcmp($action, "changeproductquantity") == 0) {
			if (not_null($attr)) { change_product_quantity($attr); } else { throw new Exception("ChangeProductQuantity, Attr error!"); }		
		} else if (strcmp($action, "changeproductweight") == 0) { 
			if (not_null($attr)) { change_product_weight($attr); } else { throw new Exception("ChangeProductWeight, Attr error!"); }
		} else if (strcmp($action, "changeproductbarcode") == 0) {
			if (not_null($attr)) { change_product_barcode($attr); } else { throw new Exception("ChangeProductBarcode, Attr error!"); }
		} else if (strcmp($action, "unlinkproductbarcode") == 0) {
			if (not_null($attr)) { unlink_product_barcode($attr); } else { throw new Exception("UnlinkProductBarcode, Attr error!"); }
		} else if (strcmp($action, "changecustomerstatus") == 0) { 
			if (not_null($attr)) { change_customer_status($attr); } else { throw new Exception("ChangeCustomerStatus, Attr error!"); }
		} else if (strcmp($action, "imagesfsdir") == 0) {
			print_images_fs_dir();
		} else if (strcmp($action, "ordersyearstatistics") == 0) {
			print_orders_year_statistics();		
		} else if (strcmp($action, "ordersmonthstatistics") == 0) {
			if (not_null($attr)) { print_orders_month_statistics($attr); } else { throw new Exception("OrdersMonthStatistics, Attr error!"); }		
		} else if (strcmp($action, "ordersdaystatistics") == 0) {
			if (not_null($attr)) { print_orders_day_statistics($attr); } else { throw new Exception("OrdersDayStatistics, Attr error!"); }		
		} else if (strcmp($action, "ordershourstatistics") == 0) { 
			if (not_null($attr)) { print_orders_hour_statistics($attr); } else { throw new Exception("OrdersHourStatistics, Attr error!"); }
		} else if (strcmp($action, "customersyearstatistics") == 0) {
			print_customers_year_statistics();		
		} else if (strcmp($action, "customersmonthstatistics") == 0) {
			if (not_null($attr)) { print_customers_month_statistics($attr); } else { throw new Exception("CustomersMonthStatistics, Attr error!"); }		
		} else if (strcmp($action, "customersdaystatistics") == 0) {
			if (not_null($attr)) { print_customers_day_statistics($attr); } else { throw new Exception("CustomersDayStatistics, Attr error!"); }		
		} else if (strcmp($action, "customershourstatistics") == 0) { 
			if (not_null($attr)) { print_customers_hour_statistics($attr); } else { throw new Exception("CustomersHourStatistics, Attr error!"); }
		} else if (strcmp($action, "ver") == 0) {
			print(encryptString(VERSION));
		} else if (strcmp($action, "key") == 0) {
			print(encryptString("true"));
		} else if (strcmp($action, "extraimages") == 0) {
			print(encryptString(EXTRA_IMAGES));
		} else if (strcmp($action, "barcodesupport") == 0) {
			if (not_null(PRODUCTS_BARCODE_FIELD)) { print(encryptString("true")); } else { print(encryptString("false")); }		
		} else if (strcmp($action, "productsquantitylowalertsvalues") == 0) {
			print_output(explode(',', PRODUCTS_QUANTITY_LOW_ALERTS_VALUES));
		} else {
			throw new Exception("Unknown action.");
		}
	} catch (Exception $e) {
		// log
		$f = fopen("ecommerceapp.log", 'a+');
		fwrite($f, date('c') . " client ip = " . get_ip_address() . ", message = " . $e->getMessage() . "\n");
		fclose($f);	
	}
