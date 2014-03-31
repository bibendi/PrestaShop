<?php
/*
 * Prestashop interface utility for eCommerce Manager app
 * KIS Software - www.kis-ecommerce.com
 * Version: 3.0
 */
	function smart_resize_image($file,
		                          $width              = 0, 
		                          $height             = 0, 
		                          $proportional       = false, 
		                          $output             = 'file', 
		                          $delete_original    = true, 
		                          $use_linux_commands = false ) { // http://github.com/maxim/smart_resize_image
		  
		if ( $height <= 0 && $width <= 0 ) return false;

		# Setting defaults and meta
		$info                         = getimagesize($file);
		$image                        = '';
		$final_width                  = 0;
		$final_height                 = 0;
		list($width_old, $height_old) = $info;

		# Calculating proportionality
		if ($proportional) {
		  if      ($width  == 0)  $factor = $height/$height_old;
		  elseif  ($height == 0)  $factor = $width/$width_old;
		  else                    $factor = min( $width / $width_old, $height / $height_old );

		  $final_width  = round( $width_old * $factor );
		  $final_height = round( $height_old * $factor );
		}
		else {
		  $final_width = ( $width <= 0 ) ? $width_old : $width;
		  $final_height = ( $height <= 0 ) ? $height_old : $height;
		}

		# Loading image to memory according to type
		switch ( $info[2] ) {
		  case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
		  case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
		  case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
		  default: return false;
		}
		
		
		# This is the resizing/resampling/transparency-preserving magic
		$image_resized = imagecreatetruecolor( $final_width, $final_height );
		if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
		  $transparency = imagecolortransparent($image);
		  if ($transparency >= 0) {
		    $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
		    $transparency       = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
		    imagefill($image_resized, 0, 0, $transparency);
		    imagecolortransparent($image_resized, $transparency);
		    imagetruecolortopalette($image_resized, true, imagecolorstotal($image));
		  }
		  elseif ($info[2] == IMAGETYPE_PNG) {
		    imagealphablending($image_resized, false);
		    $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
		    imagefill($image_resized, 0, 0, $color);
		    imagesavealpha($image_resized, true);
		  }
		}
		imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
		
		# Taking care of original, if needed
		if ( $delete_original ) {
		  if ( $use_linux_commands ) exec('rm '.$file);
		  else @unlink($file);
		}

		# Preparing a method of providing result
		switch ( strtolower($output) ) {
		  case 'browser':
		    $mime = image_type_to_mime_type($info[2]);
		    header("Content-type: $mime");
		    $output = NULL;
		  break;
		  case 'file':
		    $output = $file;
		  break;
		  case 'return':
		    return $image_resized;
		  break;
		  default:
		  break;
		}
		
		# Writing image according to type to the output destination
		switch ( $info[2] ) {
		  case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
		  case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output);   break;
		  case IMAGETYPE_PNG:   imagepng($image_resized, $output);    break;
		  default: return false;
		}

		return true;
	}
	
	function init_byte_map() {
		global $byte_map;
	  	for( $x = 128; $x < 256; ++$x ) {
			$byte_map[chr($x)] = utf8_encode(chr($x));
	  	}
	  	$cp1252_map = array(
			"\x80"=>"\xE2\x82\xAC",    // EURO SIGN
			"\x82" => "\xE2\x80\x9A",  // SINGLE LOW-9 QUOTATION MARK
			"\x83" => "\xC6\x92",      // LATIN SMALL LETTER F WITH HOOK
			"\x84" => "\xE2\x80\x9E",  // DOUBLE LOW-9 QUOTATION MARK
			"\x85" => "\xE2\x80\xA6",  // HORIZONTAL ELLIPSIS
			"\x86" => "\xE2\x80\xA0",  // DAGGER
			"\x87" => "\xE2\x80\xA1",  // DOUBLE DAGGER
			"\x88" => "\xCB\x86",      // MODIFIER LETTER CIRCUMFLEX ACCENT
			"\x89" => "\xE2\x80\xB0",  // PER MILLE SIGN
			"\x8A" => "\xC5\xA0",      // LATIN CAPITAL LETTER S WITH CARON
			"\x8B" => "\xE2\x80\xB9",  // SINGLE LEFT-POINTING ANGLE QUOTATION MARK
			"\x8C" => "\xC5\x92",      // LATIN CAPITAL LIGATURE OE
			"\x8E" => "\xC5\xBD",      // LATIN CAPITAL LETTER Z WITH CARON
			"\x91" => "\xE2\x80\x98",  // LEFT SINGLE QUOTATION MARK
			"\x92" => "\xE2\x80\x99",  // RIGHT SINGLE QUOTATION MARK
			"\x93" => "\xE2\x80\x9C",  // LEFT DOUBLE QUOTATION MARK
			"\x94" => "\xE2\x80\x9D",  // RIGHT DOUBLE QUOTATION MARK
			"\x95" => "\xE2\x80\xA2",  // BULLET
			"\x96" => "\xE2\x80\x93",  // EN DASH
			"\x97" => "\xE2\x80\x94",  // EM DASH
			"\x98" => "\xCB\x9C",      // SMALL TILDE
			"\x99" => "\xE2\x84\xA2",  // TRADE MARK SIGN
			"\x9A" => "\xC5\xA1",      // LATIN SMALL LETTER S WITH CARON
			"\x9B" => "\xE2\x80\xBA",  // SINGLE RIGHT-POINTING ANGLE QUOTATION MARK
			"\x9C" => "\xC5\x93",      // LATIN SMALL LIGATURE OE
			"\x9E" => "\xC5\xBE",      // LATIN SMALL LETTER Z WITH CARON
			"\x9F" => "\xC5\xB8"       // LATIN CAPITAL LETTER Y WITH DIAERESIS
	  	);
	  	foreach ( $cp1252_map as $k => $v ){
			$byte_map[$k] = $v;
	  	}
	}

	function get_ip_address() {
		global $HTTP_SERVER_VARS;

		if (isset($HTTP_SERVER_VARS)) {
			if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
		  	} elseif (isset($HTTP_SERVER_VARS['HTTP_CLIENT_IP'])) {
		    	$ip = $HTTP_SERVER_VARS['HTTP_CLIENT_IP'];
		  	} else {
		    	$ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
		  	}
		} else {
		  	if (getenv('HTTP_X_FORWARDED_FOR')) {
		   		$ip = getenv('HTTP_X_FORWARDED_FOR');
		  	} elseif (getenv('HTTP_CLIENT_IP')) {
		    	$ip = getenv('HTTP_CLIENT_IP');
		  	} else {
		    	$ip = getenv('REMOTE_ADDR');
			}
		}
		return $ip;
	}

	function not_null($value) {
		if (is_array($value)) {
		  	if (sizeof($value) > 0) {
		    	return true;
		  	} else {
		    	return false;
		  	}
		} else {
		  	if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
		    	return true;
		  	} else {
		    	return false;
		  	}
		}
  	}

	function fix_latin($instr) {
		if (mb_check_encoding($instr, 'UTF-8')) return $instr; // no need for the rest if it's all valid UTF-8 already
	  	global $nibble_good_chars, $byte_map;
		$outstr='';
		$char='';
		$rest='';
		while( (strlen($instr)) > 0 ) {
			if (1 == preg_match($nibble_good_chars,$instr,$match)) {
		  		$char=$match[1];
		  		$rest=$match[2];
		  		$outstr.=$char;
			} elseif (1 == preg_match('@^(.)(.*)$@s',$instr,$match)) {
		  		$char=$match[1];
		  		$rest=$match[2];
		  		$outstr.=$byte_map[$char];
			}
			$instr=$rest;
	  	}
	  return $outstr;
	}

	// Init globals for fix_latin
	$byte_map = array();
	init_byte_map();
	$ascii_char = '[\x00-\x7F]';
	$cont_byte = '[\x80-\xBF]';
	$utf8_2 = '[\xC0-\xDF]'.$cont_byte;
	$utf8_3 = '[\xE0-\xEF]'.$cont_byte.'{2}';
	$utf8_4 = '[\xF0-\xF7]'.$cont_byte.'{3}';
	$utf8_5 = '[\xF8-\xFB]'.$cont_byte.'{4}';
	$nibble_good_chars = "@^($ascii_char+|$utf8_2|$utf8_3|$utf8_4|$utf8_5)(.*)$@s";
	// EOF Init globals for fix_latin
	
	function fix_strings($input) {
		if (is_array($input)) {
			$result = array();
			foreach ( $input as $k => $v) {
				$result[$k] = fix_strings($v);			
			}
		} else {
			if (not_null($input)) {
				$result = fix_latin(html_entity_decode($input, ENT_QUOTES, 'UTF-8'));
			} else {
				$result = $input;			
			}		
		}	
		return $result;
	}

	function t_generate_image_name_resized($image_name, $dim, $postfix = '', $append_res_details = true) {
		$result = substr($image_name, 0, strrpos($image_name, '.')) . $postfix;
		if ($append_res_details) {
 			$result .= '_resized_' . $dim . '_' . $dim;
		}
		$result .= substr($image_name, strrpos($image_name, '.'));
		return $result;
	}

	function generate_image_name_resized($image_name) {
		return t_generate_image_name_resized($image_name, PRODUCT_IMAGE_DIM);
	}

	function t_resize_image($image_name, $image_name_resized, $dim)
	{
		if ((!file_exists(_PS_PROD_IMG_DIR_ . IMAGES_RES_DIR . $image_name_resized)) || 
			 (file_exists(_PS_PROD_IMG_DIR_ . IMAGES_RES_DIR . $image_name_resized) &&
				(filemtime(_PS_PROD_IMG_DIR_ . $image_name) > filemtime(_PS_PROD_IMG_DIR_ . IMAGES_RES_DIR . $image_name_resized))) ) {
					smart_resize_image(_PS_PROD_IMG_DIR_ . $image_name, $dim, $dim, true, 
						_PS_PROD_IMG_DIR_ . IMAGES_RES_DIR . $image_name_resized, false, false);
		}
		return $image_name_resized;	
	}

	function resize_image($image_name) {
		$image_name_resized = generate_image_name_resized($image_name);
		return t_resize_image($image_name, $image_name_resized, PRODUCT_IMAGE_DIM);
	}
	
	function format_price($price) {
		return Tools::displayPrice($price, Currency::getCurrency(get_currency_id()));
	}

	function check_db_error() { 
		$errno = Db::getInstance()->getNumberError();
		if ($errno) {
			$error = Db::getInstance()->getMsgError();
    		die('<font color="#000000"><b>' . $errno . ' - ' . $error . '<br><br>');
		}
  	}
  	
  	/* compatibility for 1.5+ */
  	if (!function_exists('pSQL')) {
  		function pSQL($string, $htmlOK = false) {
  			return Db::getInstance()->escape($string, $htmlOK);
		}
  	}
  	
	function json_encode_wrapper($arg) {
		if (function_exists('json_encode')) {
			return json_encode($arg);
		} else {
			require_once('ecommerceapp_json.php');
			global $services_json;
			if (!isset($services_json)) {
				$services_json = new Services_JSON();
			}
			return $services_json->encode($arg);
		}
	}

	function print_output($output) {
		print_r(encryptString(json_encode_wrapper(fix_strings($output))));
	}

	function get_configuration($key) {
		$sql = "SELECT value " .
			   " FROM " . _DB_PREFIX_ . "configuration c " .
			   " WHERE name  = '" . $key . "' ";
				
		$config = Db::getInstance()->getRow($sql);
		check_db_error();
		
		if ($config) {
			return $config['value'];
		} else {
			return null;
		}
	}
	
	function get_store_time_zone() {
		$timezone = get_configuration('PS_TIMEZONE');
		if ($timezone) {
			if (is_int($timezone)) {
				$sql = "SELECT name " .
					   " FROM " . _DB_PREFIX_ . "timezone tz " .
					   " WHERE (id_timezone = " . $timezone .") ";
				
				$timezone_name = Db::getInstance()->getRow($sql);
				check_db_error();
				
				$timezone = $timezone_name['name'];
			}			
			return $timezone;
		} else {
			return null;	
		}		
	}
	
	function set_time_zone() {
		$timezone = get_store_time_zone();
		if ($timezone) {
			date_default_timezone_set($timezone); 
			Db::getInstance()->Execute("SET SESSION time_zone = '" . date("P") . "'");
			check_db_error();
		}
	}
	
	function create_ids_hash($ids) {
		$ids_string = "";
		$count_ids = count($ids);
		for ($i = 0; $i < $count_ids; $i++) {
			$ids_string .= $ids[$i] . ",";		
		}
		$hash = md5($ids_string);
		return $hash;
	}

	function compress_seq_ids($ids) {
		$count_ids = count($ids);
		$coded_ids = array();
		$range_open = 0;
		$range_open_coded_id = -1;
		$last_coded_id = -1;

		$i = 0;
		while ($i < $count_ids || $range_open) {
			if ($range_open) {
				if (($i < $count_ids) && ($ids[$i] == $last_coded_id + 1)) {
				    $last_coded_id = $ids[$i];
				    $i++;
				} else {
				    $coded_ids[] = ($last_coded_id - $range_open_coded_id) + 1;
				    $range_open = 0;
				}
			} else {
				$range_open_coded_id = $ids[$i];
				$last_coded_id = $range_open_coded_id;
				$coded_ids[] = $last_coded_id;        
				$range_open = 1;
				$i++;
			}   
		}

		$single_ids = array();
		$range_ids = array();

		for ($i = 1; $i < count($coded_ids); $i = $i + 2) {
			 if ($coded_ids[$i] == 1) {
				 $single_ids[] = $coded_ids[$i - 1];
			 } else {
				 $range_ids[] = $coded_ids[$i - 1];
				 $range_ids[] = $coded_ids[$i];
			 }
		}

		$compressed_ids["single_ids"] = $single_ids;
		$compressed_ids["range_ids"] = $range_ids;

		return $compressed_ids;
	}

	function construct_extra_limit_sql($extra) {
		if (not_null($extra)) {
			list($start, $total) = explode(";#", $extra, 2);
			$start = pSQL($start);		
			$total = pSQL($total);
			return " LIMIT " . $start . ", " . $total;
		} else {
			return "";
		}
	}
	
	function get_language_id() {
		$lang_check = Db::getInstance()->getRow("SELECT COUNT(*) total FROM " . _DB_PREFIX_ . "lang l WHERE l.id_lang = " . MAIN_LANGUAGE_ID);
		if (intval($lang_check['total']) == 1) {
			return MAIN_LANGUAGE_ID;
		} else {
			$lang = Db::getInstance()->getRow("SELECT MIN(id_lang) id_lang FROM " . _DB_PREFIX_ . "lang l"); 
			return $lang['id_lang'];
		}
	}
	
	function get_currency_id() {
		$currency_check = Db::getInstance()->getRow("SELECT COUNT(*) total FROM " . _DB_PREFIX_ . "currency c WHERE c.id_currency = " . MAIN_CURRENCY_ID);
		if (intval($currency_check['total']) == 1) {
			return MAIN_CURRENCY_ID;
		} else {
			$currency = Db::getInstance()->getRow("SELECT MIN(id_currency) id_currency FROM " . _DB_PREFIX_ . "currency c");
			return $currency['id_currency'];
		}
	}
	
	function get_employee_id() {
		$employee_check = Db::getInstance()->getRow("SELECT COUNT(*) total FROM " . _DB_PREFIX_ . "employee e WHERE e.id_employee = " . MAIN_EMPLOYEE_ID);
		if (intval($employee_check['total']) == 1) {
			return MAIN_EMPLOYEE_ID;
		} else {
			$employee = Db::getInstance()->getRow("SELECT MIN(id_employee) id_employee FROM " . _DB_PREFIX_ . "employee e");
			return $employee['id_employee'];
		}
	}
	
	function get_image_id_dir($image_id) {
		$dir_id = '';
		foreach (str_split($image_id) as $c) {
			$dir_id .= $c . '/';
		}
		return $dir_id;
	}
	
	function get_image_name_dir($image_name) {
		$dir_id = '';
		if (strstr($image_name, '/')) {
			$end = strrpos($image_name, '/');
			$dir_id = substr($image_name, 0, $end);
		}
		return $dir_id;
	}
	
	function get_product_image_name($product_id, $image_id) {
		$dir_id = get_image_id_dir($image_id);
		$new_fs_image_name_jpg = $dir_id . $image_id . '.jpg';
		$new_fs_image_name_png = $dir_id . $image_id . '.png';
		$old_fs_image_name_jpg = $product_id. '-' . $image_id . '.jpg';
		$old_fs_image_name_png = $product_id . '-' . $image_id . '.png';					
		$image_name = null;
		if (file_exists(_PS_PROD_IMG_DIR_ . $new_fs_image_name_jpg)) {
			$image_name = $new_fs_image_name_jpg;
		} else if (file_exists(_PS_PROD_IMG_DIR_ . $new_fs_image_name_png)) {
			$image_name = $new_fs_image_name_png;
		} else if (file_exists(_PS_PROD_IMG_DIR_ . $old_fs_image_name_jpg)) {
			$image_name = $old_fs_image_name_jpg;
		} else if (file_exists(_PS_PROD_IMG_DIR_ . $old_fs_image_name_png)) {
			$image_name = $old_fs_image_name_png;
		} 
		return $image_name;
	}
	
	function print_notifications($attr) {
		list($orders_last_sync_timestamp, $products_last_sync_timestamp, $customers_last_sync_timestamp) = explode("*", $attr, 3);
		$orders_last_sync_timestamp = pSQL($orders_last_sync_timestamp);
		$products_last_sync_timestamp = pSQL($products_last_sync_timestamp);
		$customers_last_sync_timestamp = pSQL($customers_last_sync_timestamp);
		
		$num_new_orders_sql = "SELECT COUNT(*) total " .
							  " FROM " . _DB_PREFIX_ . "orders o " .
						      " WHERE (o.date_add IS NOT NULL) AND (o.date_add > FROM_UNIXTIME(" . $orders_last_sync_timestamp . ")) ";

		$num_new_orders = Db::getInstance()->getRow($num_new_orders_sql);
		check_db_error();

		if ($num_new_orders) {
			$notification['num_new_orders'] = $num_new_orders['total'];	
		}
		
		if (not_null(PRODUCTS_QUANTITY_LOW_ALERTS_VALUES)) {
			list($quantity_lowest, $quantity_very_low, $quantity_low) = explode(",", PRODUCTS_QUANTITY_LOW_ALERTS_VALUES, 3);
			$quantity_lowest = intval($quantity_lowest);
			$quantity_very_low = intval($quantity_very_low);
			$quantity_low = intval($quantity_low);
			
			$products_sql = "SELECT p.id_product products_id, p.quantity products_quantity " . 
			     " FROM " . _DB_PREFIX_ . "product p, " . _DB_PREFIX_ . "product_lang pl " .
			     " WHERE ((p.id_product = pl.id_product) AND (pl.id_lang = " . get_language_id() . " )) AND " .
			     " (((p.date_add IS NOT NULL) AND (p.date_add > FROM_UNIXTIME(" . $products_last_sync_timestamp . "))) OR " . 
			     " ((p.date_upd IS NOT NULL) AND (p.date_upd > FROM_UNIXTIME(" . $products_last_sync_timestamp . ")))) " .
			     " ORDER BY pl.name ";
			
			$products = Db::getInstance()->ExecuteS($products_sql);
			check_db_error();
			
			if ($products) {
				foreach ($products as &$product) {				
					// Products quantity low alert		
					$products_quantity = intval($product['products_quantity']);		
					if ($products_quantity <= $quantity_lowest) {
						$product['products_quantity_low_alert'] = "0";
					} else if ($products_quantity <= $quantity_very_low) {
						$product['products_quantity_low_alert'] = "1";
					} else if ($products_quantity <= $quantity_low) {
						$product['products_quantity_low_alert'] = "2";
					}					
					// EOF Products quantity low alert
					unset($product['products_quantity']);
					if (isset($product['products_quantity_low_alert'])) {	
						$notification['products_quantity_alerts'][] = $product;
					}
				}
			}
		}
		
		$new_customers_sql = "SELECT c.id_customer customers_id, CONCAT(c.firstname, ' ', c.lastname) AS customers_name " .
			   " FROM " . _DB_PREFIX_ . "customer c " .
			   " WHERE ((c.date_add IS NOT NULL) AND (c.date_add > FROM_UNIXTIME(" . $customers_last_sync_timestamp . "))) " . 
			   " ORDER BY c.id_customer ";

		$new_customers = Db::getInstance()->ExecuteS($new_customers_sql);
		check_db_error();
				
		if ($new_customers) {
			$notification['new_customers'] = $new_customers;
		}
		
		print_output($notification);
	}	
	
	function print_orders_meta($last_sync_timestamp) {
		$sql = "SELECT COUNT(o.id_order) total " .
	           " FROM " . _DB_PREFIX_ . "orders o " . 
	           " WHERE (((o.date_add IS NOT NULL) AND (o.date_add > FROM_UNIXTIME(" . $last_sync_timestamp . "))) OR " . 
			   " ((o.date_upd IS NOT NULL) AND (o.date_upd > FROM_UNIXTIME(" . $last_sync_timestamp . ")))) ";
		
		$orders_meta = Db::getInstance()->getRow($sql);
		check_db_error();
		
		if ($orders_meta) {
			$orders_total = $orders_meta['total'];
		} else {
			throw new Exception("print_orders_meta, orders meta query sql result error!");		
		}
		
		print_r(encryptString($orders_total));		
	}	
	
	function print_orders($last_sync_timestamp, $extra) {
		$order_messages_ids_sql = "SELECT DISTINCT m.id_order " .
							  	  " FROM " . _DB_PREFIX_ . "message m " .
							  	  " WHERE (m.date_add > FROM_UNIXTIME(" . $last_sync_timestamp . "))";
		
		$order_messages_ids = Db::getInstance()->ExecuteS($order_messages_ids_sql);
		check_db_error();
		
		if ($order_messages_ids) {
			$extra_ids_query = " OR (o.id_order IN (";
			for ($i = 0; $i < count($order_messages_ids); $i++) {
				$id = $order_messages_ids[$i]['id_order'];
				if ($i != 0) {
					$extra_ids_query .= ",";
				} 
				$extra_ids_query .= $id;
			}
			$extra_ids_query .= "))";
		} else {
			$extra_ids_query = "";
		}
				
		$language_id = get_language_id();
				
		$orders_sql = "SELECT o.id_order orders_id, o.total_discounts, o.total_paid, o.total_paid_real, o.total_products, " .
					  " o.total_products_wt, o.total_shipping, o.total_wrapping, o.invoice_number, o.payment payment_method, " .
					  " o.delivery_date, o.date_add, o.date_upd, " .
					  " c.firstname customers_firstname, c.lastname customers_lastname, c.email customers_email, " .
					  " ba.company billing_company, ba.firstname billing_firstname, ba.lastname billing_lastname, " .
					  " ba.address1 billing_address1, ba.address2 billing_address2, ba.postcode billing_postcode, " .
					  " ba.city billing_city, bs.name billing_state, bcl.name billing_country, " .
					  " ba.phone billing_telephone, ba.phone_mobile billing_mobilephone, " .
					  " da.company delivery_company, da.firstname delivery_firstname, da.lastname delivery_lastname, " .
					  " da.address1 delivery_address1, da.address2 delivery_address2, da.postcode delivery_postcode, " .
					  " da.city delivery_city, ds.name delivery_state, dcl.name delivery_country, " .
					  " da.phone delivery_telephone, da.phone_mobile delivery_mobilephone " .
					  " FROM " . _DB_PREFIX_ . "orders o " .
					  " LEFT JOIN " . _DB_PREFIX_. "customer c " .
					  " ON (o.id_customer = c.id_customer) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "address ba " .
					  " ON (o.id_address_invoice = ba.id_address) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "state bs " .
					  " ON (ba.id_state = bs.id_state) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "country_lang bcl " .
					  " ON ((ba.id_country = bcl.id_country) AND (bcl.id_lang = " . $language_id . ")) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "address da " .
					  " ON (o.id_address_delivery = da.id_address) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "state ds " .
					  " ON (da.id_state = ds.id_state) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "country_lang dcl " .
					  " ON ((da.id_country = dcl.id_country) AND (dcl.id_lang = " . $language_id . ")) " .
					  " WHERE (((o.date_add IS NOT NULL) AND (o.date_add > FROM_UNIXTIME(" . $last_sync_timestamp . "))) OR " . 
			   		  " ((o.date_upd IS NOT NULL) AND (o.date_upd > FROM_UNIXTIME(" . $last_sync_timestamp . "))) " . 
					    $extra_ids_query . ") " .
	           		  " ORDER BY o.date_add DESC " .
					    construct_extra_limit_sql($extra);

		$orders = Db::getInstance()->ExecuteS($orders_sql);
		check_db_error();
		
		if ($orders) {
			foreach ($orders as &$order) {
				// totals
				$totals_names = array('total_discounts', 'total_paid', 'total_paid_real', 'total_products', 'total_products_wt', 
									  'total_shipping', 'total_wrapping');
				$totals_titles = array('Total discounts:', 'Total paid:', 'Total paid real:', 'Total products:', 'Total products WT:', 
									   'Total shipping:', 'Total wrapping:');
				for ($i = 0; $i < count($totals_names); $i++) {
					$total['title'] = $totals_titles[$i];
					$total['text'] = format_price($order[$totals_names[$i]]);
					$order['totals'][] = $total;
					unset($order[$totals_names[$i]]);
				}				
				// EOF totals
				
				// order status
				$order_status_sql = "SELECT osl.name " .
								    " FROM " . _DB_PREFIX_ . "order_history oh " .
									" LEFT JOIN " . _DB_PREFIX_ . "order_state_lang osl " .
									" ON (oh.id_order_state = osl.id_order_state) " .
									" WHERE (oh.id_order = " . $order['orders_id'] . ")" .
									" ORDER BY oh.date_add DESC " . 
									" LIMIT 1";
				
				$order_status = Db::getInstance()->ExecuteS($order_status_sql);			
				check_db_error();
				
				if ($order_status) {
					$order['orders_status_name'] = $order_status[0]['name'];
				} else {
					$order['orders_status_name'] = null;
				}
				
				// order products
				$products_sql = "SELECT od.id_order_detail orders_products_id, " .
								" od.product_quantity products_quantity, od.product_price products_price, " .
								" pl.name products_name, " .  
								" agl.name products_options, al.name products_options_values, " .
								" i.id_product, i.id_image " .
								" FROM " . _DB_PREFIX_ . "order_detail od " .
								" LEFT JOIN " . _DB_PREFIX_ . "product_lang pl " .
								" ON ((od.product_id = pl.id_product) AND (pl.id_lang = " . $language_id . ")) " .
								" LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination pac " .
								" ON (od.product_attribute_id = pac.id_product_attribute) " .
								" LEFT JOIN " . _DB_PREFIX_ . "attribute a " .
								" ON (pac.id_attribute = a.id_attribute) " .
								" LEFT JOIN " . _DB_PREFIX_ . "attribute_lang al " .
								" ON ((a.id_attribute = al.id_attribute) AND (al.id_lang = " . $language_id . ")) " .
								" LEFT JOIN " . _DB_PREFIX_ . "attribute_group_lang agl " .
								" ON ((a.id_attribute_group = agl.id_attribute_group) AND (agl.id_lang = " . $language_id . "))" .
								" LEFT JOIN " . _DB_PREFIX_ . "image i " .
								" ON ((od.product_id = i.id_product) AND (i.cover = 1)) " .
								" WHERE od.id_order = " . $order['orders_id'];

				$products_values = Db::getInstance()->ExecuteS($products_sql);
				check_db_error();
				
				if ($products_values) {
					$last_orders_products_id = '';
					foreach ($products_values as &$product) {						
						if (strcmp($last_orders_products_id, $product['orders_products_id']) != 0) {	// new order product
							// create image thumb
							$product['products_image'] = '';
							if (not_null($product['id_image'])) {
								$image_name = get_product_image_name($product['id_product'], $product['id_image']);																
								if ($image_name) {
									$image_name_resized = resize_image($image_name);
									$product['products_image'] = IMAGES_DIR . IMAGES_RES_DIR . $image_name_resized;
								}
							}
							// EOF create image thumb
							// format price
							$product['final_price'] = format_price($product['products_price']);
							// EOF format price
							// product attributes
							if (not_null($product['products_options']) && not_null($product['products_options_values'])) {
								$attribute['products_options'] = $product['products_options'];
								$attribute['products_options_values'] = $product['products_options_values'];						
								$product['options'][] = $attribute;
							}
							unset($product['products_options']);
							unset($product['products_options_values']);
							// EOF product attributes						
							$last_orders_products_id = $product['orders_products_id'];
							unset($product['orders_products_id']);
							$products[] = $product;
						} else { // just append product attributes
							$attribute['products_options'] = $product['products_options']; 						
							$attribute['products_options_values'] = $product['products_options_values'];						
							unset($result);
							$products[count($products) - 1]['options'][] = $attribute;
						}
					}
					$order['products'] = $products;
					unset($products);
				}				
				
				// order history
				$history_sql = "SELECT sl.name orders_status_name, h.date_add date_added, s.send_email customer_notified " . 
							   " FROM " . _DB_PREFIX_ . "order_history h " .
							   " LEFT JOIN " . _DB_PREFIX_ . "order_state s " .
							   " ON (h.id_order_state = s.id_order_state) " .
							   " LEFT JOIN " . _DB_PREFIX_ . "order_state_lang sl " .
							   " ON (h.id_order_state = sl.id_order_state) " .
							   " WHERE ((h.id_order = " . $order['orders_id'] .") AND (sl.id_lang = " . $language_id . ")) ";
				
				$history = Db::getInstance()->ExecuteS($history_sql);
				check_db_error();
				
				if ($history) {
					$order['status_history'] = $history;
					unset($history);
				}
				
				// order messages
				$messages_sql = "SELECT m.message, m.private, m.date_add, " .
								" CONCAT(c.firstname, ' ', c.lastname) customer, CONCAT(e.firstname, ' ', e.lastname) employee " .
								" FROM " . _DB_PREFIX_ . "message m " .
								" LEFT JOIN " . _DB_PREFIX_ . "customer c " .
								" ON (m.id_customer = c.id_customer) " . 
								" LEFT JOIN " . _DB_PREFIX_ . "employee e " .
								" ON (m.id_employee = e.id_employee) " .
								" WHERE (m.id_order = " . $order['orders_id'] . ") " .
							    " ORDER BY m.date_add ";

				$messages = Db::getInstance()->ExecuteS($messages_sql);
				check_db_error();
				
				if ($messages) {
					foreach ($messages as &$message) {
						if (not_null($message['customer'])) {
							$message['sender'] = $message['customer'];							
						} else if (not_null($message['employee'])) {
							$message['sender'] = $message['employee'];
						} else {
							$message['sender'] = "Unknown";
						}					
						unset($message['customer']);
						unset($message['employee']);
					}
					$order['status_messages'] = $messages;
					unset($messages);
				}				
			}
			unset($order);
			print_output($orders);
		} else {
			print_output(null);
		}		
	}

	function print_order_ids($attr) {
		list($oldest_id_timestamp, $ids_hash) = explode("*", $attr, 2);
		$oldest_id_timestamp = pSQL($oldest_id_timestamp);
		$ids_hash = pSQL($ids_hash);
				
		$sql = "SELECT o.id_order orders_id " .			   
	           " FROM " . _DB_PREFIX_ . "orders o " .
			   " WHERE (((o.date_add IS NOT NULL) AND (o.date_add > FROM_UNIXTIME(" . $oldest_id_timestamp . "))) OR " . 
			   " ((o.date_upd IS NOT NULL) AND (o.date_upd > FROM_UNIXTIME(" . $oldest_id_timestamp . ")))) " . 
	           " ORDER BY o.id_order ";

		$results = Db::getInstance()->ExecuteS($sql);
		check_db_error();
		
		$ids = array();
		
		if ($results) {
			foreach ($results as &$result) {
				$ids[] = $result["orders_id"];
			}	
		}

		$current_ids_hash = create_ids_hash($ids);

		if (strcmp($current_ids_hash, $ids_hash) == 0) {
			print_output(null);
		} else {
			print_output(compress_seq_ids($ids));
		}
	}

	function print_single_order($orders_id) {
		$language_id = get_language_id();
	
		$orders_sql = "SELECT o.id_order orders_id, o.total_discounts, o.total_paid, o.total_paid_real, o.total_products, " .
					  " o.total_products_wt, o.total_shipping, o.total_wrapping, o.invoice_number, o.payment payment_method, " .
					  " o.delivery_date, o.date_add, o.date_upd, " .
					  " c.firstname customers_firstname, c.lastname customers_lastname, c.email customers_email, " .
					  " ba.company billing_company, ba.firstname billing_firstname, ba.lastname billing_lastname, " .
					  " ba.address1 billing_address1, ba.address2 billing_address2, ba.postcode billing_postcode, " .
					  " ba.city billing_city, '' as billing_state, bcl.name billing_country, " .
					  " ba.phone billing_telephone, ba.phone_mobile billing_mobilephone, " .
					  " da.company delivery_company, da.firstname delivery_firstname, da.lastname delivery_lastname, " .
					  " da.address1 delivery_address1, da.address2 delivery_address2, da.postcode delivery_postcode, " .
					  " da.city delivery_city, '' as delivery_state, dcl.name delivery_country, " .
					  " da.phone delivery_telephone, da.phone_mobile delivery_mobilephone " .
					  " FROM " . _DB_PREFIX_ . "orders o " .
					  " LEFT JOIN " . _DB_PREFIX_. "customer c " .
					  " ON (o.id_customer = c.id_customer) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "address ba " .
					  " ON (o.id_address_invoice = ba.id_address) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "state bs " .
					  " ON (ba.id_state = bs.id_state) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "country_lang bcl " .
					  " ON ((ba.id_country = bcl.id_country) AND (bcl.id_lang = " . $language_id . ")) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "address da " .
					  " ON (o.id_address_delivery = da.id_address) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "state ds " .
					  " ON (da.id_state = ds.id_state) " .
					  " LEFT JOIN " . _DB_PREFIX_ . "country_lang dcl " .
					  " ON ((da.id_country = dcl.id_country) AND (dcl.id_lang = " . $language_id . ")) " .
					  " WHERE (o.id_order = " . $orders_id . ") " .
	           		  " ORDER BY o.date_add DESC "; 

		$order = Db::getInstance()->getRow($orders_sql);
		check_db_error();
		
		if ($order) {
			// totals
			$totals_names = array('total_discounts', 'total_paid', 'total_paid_real', 'total_products', 'total_products_wt', 
								  'total_shipping', 'total_wrapping');
			$totals_titles = array('Total discounts:', 'Total paid:', 'Total paid real:', 'Total products:', 'Total products WT:', 
								   'Total shipping:', 'Total wrapping:');
			for ($i = 0; $i < count($totals_names); $i++) {
				$total['title'] = $totals_titles[$i];
				$total['text'] = format_price($order[$totals_names[$i]]);
				$order['totals'][] = $total;
				unset($order[$totals_names[$i]]);
			}
			// EOF totals
						
			// order status
			$order_status_sql = "SELECT osl.name " .
							    " FROM " . _DB_PREFIX_ . "order_history oh " .
								" LEFT JOIN " . _DB_PREFIX_ . "order_state_lang osl " .
								" ON (oh.id_order_state = osl.id_order_state) " .
								" WHERE (oh.id_order = " . $order['orders_id'] . ")" .
								" ORDER BY oh.date_add DESC " . 
								" LIMIT 1";
			
			$order_status = Db::getInstance()->ExecuteS($order_status_sql);			
			check_db_error();
			
			if ($order_status) {
				$order['orders_status_name'] = $order_status[0]['name'];
			} else {
				$order['orders_status_name'] = null;
			}
			
			// order products
			$products_sql = "SELECT od.id_order_detail orders_products_id, " .
							" od.product_quantity products_quantity, od.product_price products_price, " .
							" pl.name products_name, " .  
							" agl.name products_options, al.name products_options_values, " .
							" i.id_product, i.id_image " .
							" FROM " . _DB_PREFIX_ . "order_detail od " .
							" LEFT JOIN " . _DB_PREFIX_ . "product_lang pl " .
							" ON ((od.product_id = pl.id_product) AND (pl.id_lang = " . $language_id . ")) " .
							" LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination pac " .
							" ON (od.product_attribute_id = pac.id_product_attribute) " .
							" LEFT JOIN " . _DB_PREFIX_ . "attribute a " .
							" ON (pac.id_attribute = a.id_attribute) " .
							" LEFT JOIN " . _DB_PREFIX_ . "attribute_lang al " .
							" ON ((a.id_attribute = al.id_attribute) AND (al.id_lang = " . $language_id . ")) " .
							" LEFT JOIN " . _DB_PREFIX_ . "attribute_group_lang agl " .
							" ON ((a.id_attribute_group = agl.id_attribute_group) AND (agl.id_lang = " . $language_id . "))" .
							" LEFT JOIN " . _DB_PREFIX_ . "image i " .
							" ON ((od.product_id = i.id_product) AND (i.cover = 1)) " .
							" WHERE od.id_order = " . $order['orders_id'];

			$products_values = Db::getInstance()->ExecuteS($products_sql);
			check_db_error();
			
			if ($products_values) {
				$last_orders_products_id = '';
				foreach ($products_values as &$product) {						
					if (strcmp($last_orders_products_id, $product['orders_products_id']) != 0) {	// new order product
						// create image thumb
						$product['products_image'] = '';
						if (not_null($product['id_image'])) {
							$image_name = get_product_image_name($product['id_product'], $product['id_image']);								
							if ($image_name) {
								$image_name_resized = resize_image($image_name);
								$product['products_image'] = IMAGES_DIR . IMAGES_RES_DIR . $image_name_resized;
							}
						}
						// EOF create image thumb
						// format price
						$product['final_price'] = format_price($product['products_price']);
						// EOF format price
						// product attributes
						if (not_null($product['products_options']) && not_null($product['products_options_values'])) {
							$attribute['products_options'] = $product['products_options'];
							$attribute['products_options_values'] = $product['products_options_values'];						
							$product['options'][] = $attribute;
						}
						unset($product['products_options']);
						unset($product['products_options_values']);
						// EOF product attributes						
						$last_orders_products_id = $product['orders_products_id'];
						unset($product['orders_products_id']);
						$products[] = $product;
					} else { // just append product attributes
						$attribute['products_options'] = $product['products_options']; 						
						$attribute['products_options_values'] = $product['products_options_values'];						
						unset($result);
						$products[count($products) - 1]['options'][] = $attribute;
					}
				}
				$order['products'] = $products;
				unset($products);
			}				
			
			// order history
			$history_sql = "SELECT sl.name orders_status_name, h.date_add date_added, s.send_email customer_notified " . 
						   " FROM " . _DB_PREFIX_ . "order_history h " .
						   " LEFT JOIN " . _DB_PREFIX_ . "order_state s " .
						   " ON (h.id_order_state = s.id_order_state) " .
						   " LEFT JOIN " . _DB_PREFIX_ . "order_state_lang sl " .
						   " ON (h.id_order_state = sl.id_order_state) " .
						   " WHERE ((h.id_order = " . $order['orders_id'] .") AND (sl.id_lang = " . $language_id . ")) ";
			
			$history = Db::getInstance()->ExecuteS($history_sql);
			check_db_error();
			
			if ($history) {
				$order['status_history'] = $history;
				unset($history);
			}
		
			// order messages
			$messages_sql = "SELECT m.message, m.private, m.date_add, " .
							" CONCAT(c.firstname, ' ', c.lastname) customer, CONCAT(e.firstname, ' ', e.lastname) employee " .
							" FROM " . _DB_PREFIX_ . "message m " .
							" LEFT JOIN " . _DB_PREFIX_ . "customer c " .
							" ON (m.id_customer = c.id_customer) " . 
							" LEFT JOIN " . _DB_PREFIX_ . "employee e " .
							" ON (m.id_employee = e.id_employee) " .
							" WHERE (m.id_order = " . $order['orders_id'] . ") " .
						    " ORDER BY m.date_add ";

			$messages = Db::getInstance()->ExecuteS($messages_sql);
			check_db_error();
			
			if ($messages) {
				foreach ($messages as &$message) {
					if (not_null($message['customer'])) {
						$message['sender'] = $message['customer'];							
					} else if (not_null($message['employee'])) {
						$message['sender'] = $message['employee'];
					} else {
						$message['sender'] = "Unknown";
					}					
					unset($message['customer']);
					unset($message['employee']);
				}
				$order['status_messages'] = $messages;
				unset($messages);
			}
			
			print_output($order);
		} else {
			throw new Exception("print_single_order, order fetch error!");
		}	
	}

	function print_products_meta($last_sync_timestamp) {
		$sql = "SELECT COUNT(p.id_product) total " .
			   " FROM " . _DB_PREFIX_ . "product p " .
			   " WHERE (((p.date_add IS NOT NULL) AND (p.date_add > FROM_UNIXTIME(" . $last_sync_timestamp . "))) OR " . 
			   " ((p.date_upd IS NOT NULL) AND (p.date_upd > FROM_UNIXTIME(" . $last_sync_timestamp . ")))) ";
		
		$products_meta = Db::getInstance()->getRow($sql);
		check_db_error();
		
		if ($products_meta) {
			$products_total = $products_meta['total'];
		} else {
			throw new Exception("print_products_meta, products meta query sql result error!");		
		}
		
		print_r(encryptString($products_total));
	}
	
	function print_products($last_sync_timestamp, $extra) {		
		$c_sql = "SELECT id_product products_id, id_category categories_id " .
				 " FROM " . _DB_PREFIX_ . "category_product ";

		$categories_products = Db::getInstance()->ExecuteS($c_sql);
		check_db_error();

		if ($categories_products) {
			foreach ($categories_products as &$result) {
				$products_categories[$result['products_id']][] = $result['categories_id'];  
			}
		}
		
		if (not_null(PRODUCTS_QUANTITY_LOW_ALERTS_VALUES)) {
			list($quantity_lowest, $quantity_very_low, $quantity_low) = explode(",", PRODUCTS_QUANTITY_LOW_ALERTS_VALUES, 3);
			$quantity_lowest = intval($quantity_lowest);
			$quantity_very_low = intval($quantity_very_low);
			$quantity_low = intval($quantity_low);
		}
				
		$language_id = get_language_id();
				
		$products_sql = "SELECT p.id_product products_id, p.date_add products_date_added, p.date_upd products_last_modified, " .
					    " p.price products_price, p.active products_status, p.quantity products_quantity, p.weight products_weight, p.ean13, " . 
						" pl.name products_name, m.name manufacturers_name, s.name supplier_name, i.id_image images_id " . 
						" FROM " . _DB_PREFIX_ . "product p " .
						" LEFT JOIN " . _DB_PREFIX_ . "product_lang pl " . 
						" ON (p.id_product = pl.id_product) " .
						" LEFT JOIN " . _DB_PREFIX_ . "manufacturer m " .
						" ON (p.id_manufacturer = m.id_manufacturer) " .
						" LEFT JOIN " . _DB_PREFIX_ . "supplier s " .
						" ON (p.id_supplier = s.id_supplier) " . 
						" LEFT JOIN " . _DB_PREFIX_ . "image i " . 
						" ON ((p.id_product = i.id_product) AND (i.cover = 1)) " .
						" WHERE (pl.id_lang = " . $language_id . ") " .
						  construct_extra_limit_sql($extra);

		$products = Db::getInstance()->ExecuteS($products_sql);
		check_db_error();
		
		if ($products) {
			foreach ($products as &$product) {
				$product['products_image_http_dir'] = IMAGES_DIR;
				// create image thumb
				$product['products_image'] = '';
				$product['products_image_resized'] = '';
				if (not_null($product['images_id'])) {
					$image_name = get_product_image_name($product['products_id'], $product['images_id']);					
					if ($image_name) {
						$product['products_image'] = $image_name;
						$image_name_resized = resize_image($image_name);
						$product['products_image_resized'] = $image_name_resized;
					}
				}
				unset($product['images_id']);
				// EOF create image thumb
				// format price
				$product['products_price'] = format_price($product['products_price']);
				// EOF format price	
				/* Categories */
				$product['categories'] = $products_categories[$product['products_id']];
				/* EOF Categories */
				/* Products quantity low alert */
				if (not_null(PRODUCTS_QUANTITY_LOW_ALERTS_VALUES)) { 	
					$products_quantity = intval($product['products_quantity']);		
					if ($products_quantity <= $quantity_lowest) {
						$product['products_quantity_low_alert'] = "0";
					} else if ($products_quantity <= $quantity_very_low) {
						$product['products_quantity_low_alert'] = "1";
					} else if ($products_quantity <= $quantity_low) {
						$product['products_quantity_low_alert'] = "2";
					}
				}
				/* EOF Products quantity low alert */
				// Extra Images
				if (strcmp(EXTRA_IMAGES, "unlimited") == 0) {
					$ei_sql = "SELECT i.id_image " .
						      " FROM " . _DB_PREFIX_ . "image i " .
						      " WHERE ((i.cover = 0) AND (i.id_product = " . $product['products_id'] . ")) " . 
							  " ORDER BY i.position ";
	
					$eis = Db::getInstance()->ExecuteS($ei_sql);
					check_db_error();
					
					if ($eis) {
						foreach ($eis as &$ei) {
							$extra_images[] = get_product_image_name($product['products_id'], $ei['id_image']);
						}	
						$product['images'] = $extra_images;
						unset($extra_images);					
					}					
				}
				// EOF Extra Images
				
				$attributes_sql = "SELECT COUNT(pa.id_product_attribute)" .
								  " FROM " . _DB_PREFIX_ . "product_attribute pa " .
								  " WHERE (pa.id_product = " . $product['products_id'] . ") "; 
				
				$attributes = Db::getInstance()->ExecuteS($attributes_sql);
				check_db_error();
				
				if ($attributes) {
					$product['products_attributes'] = 'true';
				}
			}
			print_output($products);
		} else {
			print_output(null);
		}
	}

	function print_product_ids($ids_hash) {		
		$sql = "SELECT p.id_product products_id " .			   
	           " FROM " . _DB_PREFIX_ . "product p " . 
	           " ORDER BY p.id_product ";

		$results = Db::getInstance()->ExecuteS($sql);
		check_db_error();
		
		$ids = array();
		
		if ($results) {
			foreach ($results as &$result) {
				$ids[] = $result["products_id"];
			}	
		}

		$current_ids_hash = create_ids_hash($ids);

		if (strcmp($current_ids_hash, $ids_hash) == 0) {
			print_output(null);
		} else {
			print_output(compress_seq_ids($ids));
		}
	}

	function print_single_product($products_id) {
		$c_sql = "SELECT id_product products_id, id_category categories_id " .
				 " FROM " . _DB_PREFIX_ . "category_product ";

		$categories_products = Db::getInstance()->ExecuteS($c_sql);
		check_db_error();

		if ($categories_products) {
			foreach ($categories_products as &$result) {
				$products_categories[$result['products_id']][] = $result['categories_id'];  
			}
		}
		
		if (not_null(PRODUCTS_QUANTITY_LOW_ALERTS_VALUES)) {
			list($quantity_lowest, $quantity_very_low, $quantity_low) = explode(",", PRODUCTS_QUANTITY_LOW_ALERTS_VALUES, 3);
			$quantity_lowest = intval($quantity_lowest);
			$quantity_very_low = intval($quantity_very_low);
			$quantity_low = intval($quantity_low);
		}
		
		$language_id = get_language_id();
		
		$products_sql = "SELECT p.id_product products_id, p.date_add products_date_added, p.date_upd products_last_modified, " .
					    " p.price products_price, p.active products_status, p.quantity products_quantity, p.weight products_weight, p.ean13, " . 
						" pl.name products_name, m.name manufacturers_name, s.name supplier_name, i.id_image images_id " . 
						" FROM " . _DB_PREFIX_ . "product p " .
						" LEFT JOIN " . _DB_PREFIX_ . "product_lang pl " . 
						" ON (p.id_product = pl.id_product) " .
						" LEFT JOIN " . _DB_PREFIX_ . "manufacturer m " .
						" ON (p.id_manufacturer = m.id_manufacturer) " .
						" LEFT JOIN " . _DB_PREFIX_ . "supplier s " .
						" ON (p.id_supplier = s.id_supplier) " . 
						" LEFT JOIN " . _DB_PREFIX_ . "image i " . 
						" ON ((p.id_product = i.id_product) AND (i.cover = 1)) " .
						" WHERE ((p.id_product = " . $products_id . ") AND (pl.id_lang = " . $language_id . ")) ";

		$product = Db::getInstance()->getRow($products_sql);
		check_db_error();
		
		if ($product) {
			$product['products_image_http_dir'] = IMAGES_DIR;
			// create image thumb
			$product['products_image'] = '';
			$product['products_image_resized'] = '';
			if (not_null($product['images_id'])) {
				$image_name = get_product_image_name($product['products_id'], $product['images_id']);
				if ($image_name) {
					$product['products_image'] = $image_name;
					$image_name_resized = resize_image($image_name);
					$product['products_image_resized'] = $image_name_resized;
				}
			}
			unset($product['images_id']);
			// EOF create image thumb
			// format price
			$product['products_price'] = format_price($product['products_price']);
			// EOF format price	
			/* Categories */
			$product['categories'] = $products_categories[$product['products_id']];
			/* EOF Categories */
			/* Products quantity low alert */
			if (not_null(PRODUCTS_QUANTITY_LOW_ALERTS_VALUES)) { 	
				$products_quantity = intval($product['products_quantity']);		
				if ($products_quantity <= $quantity_lowest) {
					$product['products_quantity_low_alert'] = "0";
				} else if ($products_quantity <= $quantity_very_low) {
					$product['products_quantity_low_alert'] = "1";
				} else if ($products_quantity <= $quantity_low) {
					$product['products_quantity_low_alert'] = "2";
				}
			}
			/* EOF Products quantity low alert */
			// Extra Images
			if (strcmp(EXTRA_IMAGES, "unlimited") == 0) {
				$ei_sql = "SELECT i.id_image " .
					      " FROM " . _DB_PREFIX_ . "image i " .
					      " WHERE ((i.cover = 0) AND (i.id_product = " . $product['products_id'] . ")) " . 
						  " ORDER BY i.position ";

				$eis = Db::getInstance()->ExecuteS($ei_sql);
				check_db_error();
				
				if ($eis) {
					foreach ($eis as &$ei) {
						$extra_images[] = get_product_image_name($product['products_id'], $ei['id_image']);
					}	
					$product['images'] = $extra_images;
					unset($extra_images);					
				}					
			}
			// EOF Extra Images

			$attributes_sql = "SELECT COUNT(pa.id_product_attribute)" .
							  " FROM " . _DB_PREFIX_ . "product_attribute pa " .
							  " WHERE (pa.id_product = " . $product['products_id'] . ") "; 
			
			$attributes = Db::getInstance()->ExecuteS($attributes_sql);
			check_db_error();
			
			if ($attributes) {
				$product['products_attributes'] = 'true';
			}
		}
		print_output($product);
	}
	
	function print_customers_meta($last_sync_timestamp) {
		$sql = "SELECT COUNT(c.id_customer) total " .
			   " FROM " . _DB_PREFIX_ . "customer c " .
			   " WHERE ((c.deleted = 0) AND (" . 
			   " ((c.date_add IS NOT NULL) AND (c.date_add > FROM_UNIXTIME(" . $last_sync_timestamp . "))) OR " . 
			   " ((c.date_upd IS NOT NULL) AND (c.date_upd > FROM_UNIXTIME(" . $last_sync_timestamp . "))))) ";
			   
		$customers_meta = Db::getInstance()->getRow($sql);
		check_db_error();
		
		if ($customers_meta) {
			$customers_total = $customers_meta['total'];
		} else {
			throw new Exception("print_customers_meta, customers meta query sql result error!");		
		}
		
		print_r(encryptString($customers_total));
	}
	
	function print_customers($last_sync_timestamp, $extra) {
		$customers_sql = "SELECT c.id_customer customers_id, c.id_gender customers_gender, " . 
						 " c.firstname customers_firstname, c.lastname customers_lastname, c.birthday customers_dob, " .
			   			 " c.email customers_email, c.newsletter customers_newsletter, c.active status, c.date_add, c.date_upd " .
			   			 " FROM " . _DB_PREFIX_ . "customer c " .
						 " WHERE ((c.deleted = 0) AND (" . 
						 " ((c.date_add IS NOT NULL) AND (c.date_add > FROM_UNIXTIME(" . $last_sync_timestamp . "))) OR " . 
			   			 " ((c.date_upd IS NOT NULL) AND (c.date_upd > FROM_UNIXTIME(" . $last_sync_timestamp . "))))) " .			   
			   			 " ORDER BY c.firstname, c.lastname " .
						   construct_extra_limit_sql($extra);
		
		$customers = Db::getInstance()->ExecuteS($customers_sql);
		check_db_error();
		
		if ($customers) {
			$language_id = get_language_id();
			
			foreach ($customers as &$customer) {
				// Convert gender 1, 2 to 'm', 'f'
				$customer['customers_gender'] = (strcmp($customer['customers_gender'], "1") == 0) ? "m" : "f";
				// EOF Conver gender
				// Assign first customer address
				$address_sql = "SELECT a.address1, a.address2, a.city, a.postcode, a.phone, a.phone_mobile, " .
							   " s.name state, cl.name country " .
							   " FROM " . _DB_PREFIX_ . "address a " .
							   " LEFT JOIN " . _DB_PREFIX_ . "state s " .
					  		   " ON (a.id_state = s.id_state) " .
					  		   " LEFT JOIN " . _DB_PREFIX_ . "country_lang cl " .
					  		   " ON ((a.id_country = cl.id_country) AND (cl.id_lang = " . $language_id . ")) " .
							   " WHERE ((a.active = 1) AND (a.deleted = 0) AND (a.id_customer = " . $customer['customers_id'] . ")) " .
							   " LIMIT 1 ";
				
				$address = Db::getInstance()->ExecuteS($address_sql);
				check_db_error();
				
				if ($address) {
					$address = $address[0];
					foreach ($address as $key => $value) {
						$customer[$key] = $value;
					}				
				} else {
					$customer['address1'] = '';
					$customer['address2'] = '';
					$customer['postcode'] = '';
					$customer['city'] = '';
					$customer['phone'] = '';
					$customer['phone_mobile'] = '';
					$customer['state'] = '';
					$customer['country'] = '';
				}
				// EOF Assign first customer address
			}
			print_output($customers);		
		} else {
			print_output(null);
		}
	}
	
	function print_customer_ids($ids_hash) {		
		$sql = "SELECT c.id_customer customers_id " .			   
	           " FROM " . _DB_PREFIX_ . "customer c " .
			   " WHERE (c.deleted = 0) " .
	           " ORDER BY c.id_customer ";

		$results = Db::getInstance()->ExecuteS($sql);
		check_db_error();
		
		$ids = array();
		
		if ($results) {
			foreach ($results as &$result) {
				$ids[] = $result["customers_id"];
			}	
		}

		$current_ids_hash = create_ids_hash($ids);

		if (strcmp($current_ids_hash, $ids_hash) == 0) {
			print_output(null);
		} else {
			print_output(compress_seq_ids($ids));
		}
	}
	
	function print_single_customer($customers_id) {
		$customers_sql = "SELECT c.id_customer customers_id, c.id_gender customers_gender, " . 
						 " c.firstname customers_firstname, c.lastname customers_lastname, c.birthday customers_dob, " .
			   			 " c.email customers_email, c.newsletter customers_newsletter, c.active status, c.date_add, c.date_upd " .
			   			 " FROM " . _DB_PREFIX_ . "customer c " .
						 " WHERE (c.id_customer = " . $customers_id . ")";
		
		$customers = Db::getInstance()->ExecuteS($customers_sql);
		check_db_error();
		
		if ($customers) {
			$language_id = get_language_id();
			
			$customer = $customers[0];			
			// Convert gender 1, 2 to 'm', 'f'
			$customer['customers_gender'] = (strcmp($customer['customers_gender'], "1") == 0) ? "m" : "f";
			// EOF Conver gender
			// Assign first customer address
			$address_sql = "SELECT a.address1, a.address2, a.city, a.postcode, a.phone, a.phone_mobile, " .
						   " s.name state, cl.name country " .
						   " FROM " . _DB_PREFIX_ . "address a " .
						   " LEFT JOIN " . _DB_PREFIX_ . "state s " .
				  		   " ON (a.id_state = s.id_state) " .
				  		   " LEFT JOIN " . _DB_PREFIX_ . "country_lang cl " .
				  		   " ON ((a.id_country = cl.id_country) AND (cl.id_lang = " . $language_id . ")) " .
						   " WHERE ((a.active = 1) AND (a.deleted = 0) AND (a.id_customer = " . $customer['customers_id'] . ")) " .
						   " LIMIT 1 ";
			
			$address = Db::getInstance()->ExecuteS($address_sql);
			check_db_error();
			
			if ($address) {
				$address = $address[0];
				foreach ($address as $key => $value) {
					$customer[$key] = $value;
				}				
			} else {
				$customer['address1'] = '';
				$customer['address2'] = '';
				$customer['postcode'] = '';
				$customer['city'] = '';
				$customer['phone'] = '';
				$customer['phone_mobile'] = '';
				$customer['state'] = '';
				$customer['country'] = '';
			}
			// EOF Assign first customer address
		}
		print_output($customer);				
	}

	function print_online_customers() {
		$sql = "SELECT u.id_customer customers_id " .
			   " FROM " ._DB_PREFIX_ . "connections c " .
			   " LEFT JOIN " . _DB_PREFIX_ . "connections_page cp " . 
			   " ON (c.id_connections = cp.id_connections) " .
			   " LEFT JOIN " . _DB_PREFIX_ . "page p " . 
			   " ON (p.id_page = cp.id_page) " .
			   " LEFT JOIN " . _DB_PREFIX_ . "page_type pt " . 
			   " ON (p.id_page_type = pt.id_page_type) " .
			   " INNER JOIN " . _DB_PREFIX_ . "guest g " . 
			   " ON (c.id_guest = g.id_guest) " .
			   " INNER JOIN " . _DB_PREFIX_ . "customer u " . 
			   " ON (u.id_customer = g.id_customer) " .
			   " WHERE ((cp.time_end IS NULL) AND (TIME_TO_SEC(TIMEDIFF(NOW(), cp.time_start)) < 900)) " .
			   " GROUP BY c.id_connections " .
			   " ORDER BY u.id_customer ";

		$ids = array();
		
		$online_customers = DB::getInstance()->ExecuteS($sql);
		check_db_error();
		
		if ($online_customers) {
			foreach ($online_customers as $customer) {
				$ids[] = $customer['customers_id'];
			}
		}
		
		print_output(compress_seq_ids($ids));
	}
	
	function print_categories() {
		$sql = "SELECT MIN(id_category) min_level, MAX(id_category) max_level" .
			   " FROM " . _DB_PREFIX_ . "category";

		$result_levels = DB::getInstance()->getRow($sql);
		check_db_error();

		if ($result_levels) {
			$language_id = get_language_id();
			
			$min_level = $result_levels['min_level'];
			$max_level = $result_levels['max_level'];

			$sql = "SELECT c.id_category categories_id, c.id_parent parent_id, cl.name categories_name" .
				   " FROM " . _DB_PREFIX_ . "category c, " . _DB_PREFIX_ . "category_lang cl " .
				   " WHERE (c.id_category = cl.id_category) && (cl.id_lang = " . $language_id . ")";

			$categories_results = DB::getInstance()->ExecuteS($sql);
			check_db_error();
			
			if ($categories_results) {
				foreach ($categories_results as &$result) {
					$categories[$result['categories_id']] = $result; 			
				}
			}

			for ($level = $min_level; $level < $max_level; $level++) {
				foreach ($categories as &$category) {
					if ($category['parent_id'] == $level) {
						$category['categories_name'] = 
							$categories[$category['parent_id']]['categories_name'] . 
							" - " . $category['categories_name'];							
					}
				}					
			}

			foreach ($categories as &$category) {
				unset($category['parent_id']);
				$categories_array[] = $category;
			}
		}
		
		if (isset($categories_array)) {
			print_output($categories_array);
		} else {
			print_output(null);
		}
	}
	
	function print_order_statuses() {
		$language_id = get_language_id();
		
		$sql = "SELECT sl.id_order_state orders_status_id, sl.name orders_status_name " .
			   " FROM " . _DB_PREFIX_ . "order_state_lang sl " .
			   " WHERE sl.id_lang = " . $language_id;
		
		$statuses = Db::getInstance()->ExecuteS($sql);
		check_db_error();
		
		print_output($statuses);
	}
	
	function change_order_status($attr, $extra) {
		list($orders_id, $status_id) = explode("*", $attr, 2);
		$orders_id = pSQL($orders_id);
		$status_id = pSQL($status_id);
		list($notify_customer, $comment) = explode(";#", $extra, 2);
		$notify_customer = pSQL($notify_customer);		
		$comment = pSQL($comment); // Not used
		
		if (!not_null($orders_id)) {
			print_r(encryptString("false"));
			throw new Exception("change_order_status, No such order id!");
		}
		
		if ((strcmp($notify_customer, "1") != 0) && (strcmp($notify_customer, "0") != 0)) {
			print_r(encryptString("false"));
			throw new Exception("change_order_status, notify_customer data error!");		
		}
		
		if (!not_null($status_id)) {
			print_r(encryptString("false"));
			throw new Exception("change_order_status, No such status id!");
		}
		
		$sql = "INSERT INTO " . _DB_PREFIX_ . "order_history (id_employee, id_order, id_order_state, date_add) " .
			   " VALUES (" . get_employee_id() . ", " . $orders_id . ", " . $status_id . ", NOW() )";
		
		$result = Db::getInstance()->Execute($sql);
		check_db_error();
		
		if ($result) {
			$sql = "UPDATE " . _DB_PREFIX_ . "orders " . 
			   	   " SET date_upd = NOW() " .
		       	   " WHERE (id_order = " . $orders_id . ")";
			
			$result = Db::getInstance()->Execute($sql);
			check_db_error();
			
			if ($result) {
				if (strcmp($notify_customer, "1") == 0) {
					$language_id = get_language_id();
					
					$email_data_sql = "SELECT o.total_paid, " . 
									  " c.firstname, c.lastname, c.email, " .
								  	  " osl.name osname, osl.template " . 
								  	  " FROM " . _DB_PREFIX_ . "orders o " .
								      " LEFT JOIN " . _DB_PREFIX_ . "customer c " .
								      " ON (o.id_customer = c.id_customer) " .
								      " LEFT JOIN " . _DB_PREFIX_ . "order_state_lang osl " . 
								      " ON ((osl.name = '" . $status_name . "') AND (osl.id_lang = " . $language_id . ")) " .
								      " WHERE (o.id_order = " . $orders_id . ")";
					
					$result = Db::getInstance()->ExecuteS($email_data_sql);
					check_db_error();
					
					if ($result) {
						$result = $result[0];
						if (isset($result['template']) AND Validate::isEmail($result['email']))
						{
							$topic = $result['osname'];
							$data = array(
								'{lastname}' => $result['lastname'], 
								'{firstname}' => $result['firstname'], 
								'{id_order}' => intval($orders_id));
							$data['{total_paid}'] = format_price($result['total_paid']);
							$data['{order_name}'] = sprintf("#%06d", intval($orders_id));
							
							Mail::Send(intval($orders_idg), 
								$result['template'], $topic, $data, $result['email'], 
								$result['firstname'] . ' ' . $result['lastname']);
						}
					}
				}
				print_r(encryptString("true"));
			} else {
				print_r(encryptString("false")); // Orders Error
			}	
		} else {
			print_r(encryptString("false")); // Order History Error
		}	
	}
		
	function add_order_status_message($attr, $message) {
		list($orders_id, $private_flag) = explode("*", $attr, 2);
		$orders_id = pSQL($orders_id);
		$private_flag = pSQL($private_flag);
		
		$sql = "INSERT INTO " . _DB_PREFIX_ . "message (id_cart, id_customer, id_employee, id_order, message, private, date_add) " .
			   " VALUES ( 0, 0, " . get_employee_id(). ", " . $orders_id . ", '" . $message . "', " . $private_flag . ", NOW() ) ";
		
		$result = Db::getInstance()->Execute($sql);
		check_db_error();
		
		if ($result) {
			if ($private_flag) { // Send e-mail to customer				
				$customer_sql = "SELECT c.firstname, c.lastname, c.email " .
								" FROM " . _DB_PREFIX_ . "orders o " .
								" LEFT JOIN " . _DB_PREFIX_ . "customer c " .
								" ON (o.id_customer = c.id_customer) " .
								" WHERE (o.id_order = " . $orders_id . ")";   
				
				$customer = Db::getInstance()->ExecuteS($customer_sql);
				check_db_error();
				
				if ($customer) {
					$customer = $customer[0];								
					$title = html_entity_decode('New message regarding your order' . ' ' . $orders_id, ENT_NOQUOTES, 'UTF-8');
					$varsTpl = array(
						'{lastname}' => $customer['lastname'], 
						'{firstname}' => $customer['firstname'], 
						'{id_order}' => $orders_id, 
						'{message}' => (Configuration::get('PS_MAIL_TYPE') == 2 ? $message : nl2br2($message)));
					
					Mail::Send(intval($orders_id), 'order_merchant_comment', $title, $varsTpl, 
						$customer['email'], $customer['firstname'] . ' ' . $customer['lastname']);						
				}
			}
			print_r(encryptString("true"));	
		} else {
			print_r(encryptString("false"));
		}		
	}
	
	function print_update_sql_result($sql) {
		$result = Db::getInstance()->Execute($sql);
		check_db_error();
		
		if ($result) {
			print_r(encryptString("true"));
		} else {
			print_r(encryptString("false"));
		}
	}
	
	function change_product_price($attr) {
		list($products_id, $price) = explode("*", $attr);
		$products_id = pSQL($products_id);
		$price = pSQL($price);			

		$sql = "UPDATE " . _DB_PREFIX_ . "product " .
			   " SET price = " . $price . ", date_upd = now() " .
			   " WHERE id_product = " . $products_id;

		print_update_sql_result($sql);
	}
	
	function change_product_image($attr) {
		list($products_id, $new_image_name, $old_image_name) = explode("*", $attr, 3);
		$products_id = pSQL($products_id);
		$new_image_name = pSQL($new_image_name);
		$old_image_name = pSQL($old_image_name);
		
		if (strcmp($old_image_name, "new") == 0) {
			// new extra image			
			$image_desc_sql = "SELECT MAX(i.position) pos " .
							  " FROM " . _DB_PREFIX_ . "image i " .
						 	  " WHERE ((i.id_product = " . $products_id . ") AND (i.cover = 0)) "; 
			
			$image_desc_values = Db::getInstance()->getRow($image_desc_sql);
			check_db_error();
			
			if (!$image_desc_values) {
				print_r(encryptString("false"));	
				return;
			}
			
			$new_pos = (int)$image_desc_values['pos'] + 1;		
			
			$insert_image_sql = "INSERT INTO " . _DB_PREFIX_ . "image " .
								" (id_product, position, cover) VALUES (" . $products_id . ", " . $new_pos . ", 0)";
		
			$result = Db::getInstance()->Execute($insert_image_sql);
			check_db_error();
			
			if (!$result) {
				print_r(encryptString("false"));	
				return;
			}
			
			$image_sql = "SELECT MAX(id_image) id_image " .
			 	 	 	 " FROM " . _DB_PREFIX_ . "image i " . 
			 	 	 	 " WHERE ((i.id_product = " . $products_id . ") AND (i.position = " . $new_pos . ") AND (i.cover = 0))";
	
			$image_values = Db::getInstance()->getRow($image_sql);
			check_db_error();
			
			if (!$image_values) {				
				print_r(encryptString("false"));	
				return;
			}
			
			$image_id = $image_values['id_image'];
			
			mkdir(_PS_PROD_IMG_DIR_ . get_image_id_dir($image_id), 0755, true);
			
		} else if (strcmp($old_image_name, "default_new") == 0) {
			// new default image			
			$image_desc_sql = "SELECT MIN(i.position) min_pos, MAX(i.position) max_pos " .
							  " FROM " . _DB_PREFIX_ . "image i " .
						 	  " WHERE (i.id_product = " . $products_id . ") ";
			
			$image_desc_values = Db::getInstance()->getRow($image_desc_sql);
			check_db_error();
			
			if ($image_desc_values) {
				$min_pos = (int)$image_desc_values['min_pos'];
				$max_pos = (int)$image_desc_values['max_pos'];
				if ($min_pos > 1) {
					$pos = 1;
				} else {
					$pos = $max_pos + 1;
				}
			} else {
				$pos = 1;
			}
			
			$insert_image_sql = "INSERT INTO " . _DB_PREFIX_ . "image " .
								" (id_product, position, cover) VALUES (" . $products_id . ", " . $pos . ", 1)";
			
			$result = Db::getInstance()->Execute($insert_image_sql);
			check_db_error();
			
			if (!$result) {
				print_r(encryptString("false"));	
				return;
			}
			
			$image_sql = "SELECT id_image " .
				 	 	 " FROM " . _DB_PREFIX_ . "image i " . 
				 	 	 " WHERE ((i.id_product = " . $products_id . ") AND (i.cover = 1))";
		
			$image_values = Db::getInstance()->getRow($image_sql);
			check_db_error();
			
			if (!$image_values) {
				print_r(encryptString("false"));	
				return;
			}	
			
			$image_id = $image_values['id_image'];
			
			mkdir(_PS_PROD_IMG_DIR_ . get_image_id_dir($image_id), 0755, true);
			
		} else {
			// update existing entry (just get image_id below common code will do the rest)
			$image_id = get_image_id_from_image_filename($old_image_name);
		
			if ($image_id === false) {
				print_r(encryptString("false"));
				return;
			}
		}	

		// create images based on image types
		$image_types_sql = "SELECT it.name, it.width, it.height " .
						   " FROM " . _DB_PREFIX_ . "image_type it" .
						   " WHERE (it.products = 1) ";
		
		$image_types = Db::getInstance()->ExecuteS($image_types_sql);
		check_db_error();
		
		$ps_version = explode('.', _PS_VERSION_);
		$ps_version_ge_1_4_3 = (($ps_version[0] > 1) || 
								(($ps_version[0] == 1) && ($ps_version[1] > 4)) ||
								(($ps_version[0] == 1) && ($ps_version[1] == 4) && ($ps_version[2] >= 3)));
		
		if ($ps_version_ge_1_4_3) {
			foreach ($image_types as &$it) {
				if (!imageResize(_PS_PROD_IMG_DIR_ . $new_image_name, 
								 _PS_PROD_IMG_DIR_ . get_image_id_dir($image_id) . $image_id . '-' . $it['name'] . '.jpg', 
								 $it['width'], $it['height'])) {
					print_r(encryptString("false"));
					return;
				}
			}			
			imageResize(_PS_PROD_IMG_DIR_ . $new_image_name, 
						_PS_PROD_IMG_DIR_ . get_image_id_dir($image_id) . $image_id . '.jpg');
		} else {
			foreach ($image_types as &$it) {
				if (!imageResize(_PS_PROD_IMG_DIR_ . $new_image_name, 
								 _PS_PROD_IMG_DIR_ . $products_id . '-' . $image_id . '-' . $it['name'] . '.jpg', 
								 $it['width'], $it['height'])) {
					print_r(encryptString("false"));
					return;
				}
			}			
			imageResize(_PS_PROD_IMG_DIR_ . $new_image_name, 
						_PS_PROD_IMG_DIR_ . $products_id . '-' . $image_id . '.jpg');
		}
		
		@unlink(_PS_PROD_IMG_DIR_ . $new_image_name);
		
		// update product
		$sql = "UPDATE " . _DB_PREFIX_ . "product " .
			   " SET date_upd = now() " .
			   " WHERE id_product = " . $products_id;

		print_update_sql_result($sql);
	}
	
	function unlink_product_image($attr) {
		list($products_id, $old_image_name) = explode("*", $attr, 2);
		$products_id = pSQL($products_id);		
		$old_image_name = pSQL($old_image_name);		

		$image_id = get_image_id_from_image_filename($old_image_name);

		if ($image_id === false) {
			print_r(encryptString("false"));
		} else {
			$sql = "DELETE FROM " . _DB_PREFIX_ . "image " .
				   " WHERE (id_image = " . $image_id . ")";			
	
			print_update_sql_result($sql);

			@unlink(_PS_PROD_IMG_DIR_ . $old_image_name);
			
			// remove other images based on image type
			$image_types_sql = "SELECT it.name " .
						   	   " FROM " . _DB_PREFIX_ . "image_type it" .
						   	   " WHERE (it.products = 1) ";
		
			$image_types = Db::getInstance()->ExecuteS($image_types_sql);
			check_db_error();
			
			// old fs
			$name_prefix = str_replace(".jpg", "", $old_image_name);
			foreach ($image_types as &$it) {
				$file = _PS_PROD_IMG_DIR_ . $name_prefix . '-' . $it['name'] . '.jpg';
				if (file_exists($file)) {
					@unlink($file);
				}
			}
			// new fs
			$image_dir_id = get_image_name_dir($old_image_name);
			if ($image_dir_id && file_exists(_PS_PROD_IMG_DIR_ . $image_dir_id)) {
				r_rmdir(_PS_PROD_IMG_DIR_ . $image_dir_id);
			}
		}			
	}
	
	function get_image_id_from_image_filename($image_filename) {
		if (strstr($image_filename, '-')) {  // old image fs
			$start = strpos($image_filename, '-');			
		} else { // new image fs
			$start = strrpos($image_filename, '/');
		}
		$end = strrpos($image_filename, '.');		
		if ($start === false || $end === false) {
			return false;
		} else {
			return substr($image_filename, $start + 1, $end - $start - 1);
		}
	}
	
	function r_rmdir($dirname) {
		if (is_dir($dirname)) {
			$dir_handle = opendir($dirname);
		}
		if (!$dir_handle) {
			return false;
		}
		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($dirname."/".$file)) {
					unlink($dirname."/".$file);
				} else {
					r_rmdir($dirname.'/'.$file);
				}
			}
		}
		closedir($dir_handle);
		rmdir($dirname);
		return true;
	}
	
	function change_product_availability($attr) {
		list($products_id, $status) = explode("*", $attr, 2);	
		$products_id = pSQL($products_id);
		$status = pSQL($status);

		$sql = "UPDATE " . _DB_PREFIX_ . "product " .
			   " SET active = " . $status . ", date_upd = now() " .
			   " WHERE id_product = " . $products_id;
		
		print_update_sql_result($sql);
	}
	
	function change_product_quantity($attr) {
		list($products_id, $quantity) = explode("*", $attr, 2);
		$products_id = pSQL($products_id);
		$quantity = pSQL($quantity);	

		$sql = "UPDATE " . _DB_PREFIX_ . "product " .
			   " SET quantity = " . $quantity . ", date_upd = now() " .
			   " WHERE id_product = " . $products_id;

		print_update_sql_result($sql);
	}
	
	function change_product_weight($attr) {
		list($products_id, $weight) = explode("*", $attr, 2);
		$products_id = pSQL($products_id);
		$weight = pSQL($weight);	

		$sql = "UPDATE " . _DB_PREFIX_ . "product " .
			   " SET weight = " . $weight . ", date_upd = now() " .
			   " WHERE id_product = " . $products_id;

		print_update_sql_result($sql);
	}
	
	function change_product_barcode($attr) {
		list($products_id, $barcode) = explode("*", $attr, 2);
		$products_id = pSQL($products_id);
		$barcode = pSQL($barcode);	

		$sql = "UPDATE " . _DB_PREFIX_ . "product " .
			   " SET ean13 = " . $barcode . ", date_upd = now() " .
			   " WHERE id_product = " . $products_id;

		print_update_sql_result($sql);
	}
	
	function unlink_product_barcode($products_id) {
		$sql = "UPDATE " . _DB_PREFIX_ . "product " .
			   " SET ean13 = NULL, date_upd = now() " .
			   " WHERE id_product = " . $products_id;

		print_update_sql_result($sql);
	}
	
	function change_customer_status($attr) {
		list($customers_id, $status) = explode("*", $attr, 2);
		$customers_id = pSQL($customers_id);
		$status = pSQL($status);			

		$sql = "UPDATE " . _DB_PREFIX_ . "customer " .
			   " SET active = " . $status . ", date_upd = NOW() " .
			   " WHERE (id_customer = " . $customers_id . ")";
		
		$result = Db::getInstance()->Execute($sql);
		check_db_error();
			
		if ($result) {
			print_r(encryptString("true"));
		} else {
			print_r(encryptString("false"));
		}
	}
	
	function print_images_fs_dir() {
		print_r(encryptString(FTP_IMAGES_UPLOAD_DIR));	
	}
	
	function print_orders_year_statistics() {
		if (not_null(STATISTICS_ORDER_STATUSES)) {
			$status_filter = " WHERE (oh.id_order_state IN (" . STATISTICS_ORDER_STATUSES . "))"; 		
		} else {
			$status_filter = "";
		}

		$sql = "SELECT SUM(o.total_paid) value, COUNT(DISTINCT o.id_order) orders_count, YEAR(o.date_add) date " .
			   " FROM " . _DB_PREFIX_ . "orders o " . 
			   " LEFT JOIN " . _DB_PREFIX_ . "order_history oh " .
			   " ON (o.id_order = oh.id_order) AND (oh.id_order_state = ( " .
			   "	SELECT MAX(sub_oh.id_order_state) " . 
			   "	FROM " . _DB_PREFIX_ . "order_history sub_oh " .
			   "	WHERE (sub_oh.id_order = o.id_order) " .
			   " )) " .
			   $status_filter .
			   " GROUP BY YEAR(o.date_add) " .
			   " ORDER BY o.date_add DESC ";
		
		$year_statistics = Db::getInstance()->ExecuteS($sql);
		check_db_error();
		
		if ($year_statistics) {
			foreach ($year_statistics as &$year_stat) {
				$year_stat['total'] = format_price($year_stat['value']); 
			}
			print_output($year_statistics);
		} else {
			print_output(null);
		}
	}
	
	function print_orders_month_statistics($year) {
		if (not_null(STATISTICS_ORDER_STATUSES)) {
			$status_filter = " AND (oh.id_order_state IN (" . STATISTICS_ORDER_STATUSES . "))"; 		
		} else {
			$status_filter = "";
		}

		$sql = "SELECT SUM(o.total_paid) value, COUNT(DISTINCT o.id_order) orders_count, MONTH(o.date_add) date " .
			   " FROM " . _DB_PREFIX_ . "orders o " . 
			   " LEFT JOIN " . _DB_PREFIX_ . "order_history oh " .
			   " ON (o.id_order = oh.id_order) AND (oh.id_order_state = ( " .
			   "	SELECT MAX(sub_oh.id_order_state) " . 
			   "	FROM " . _DB_PREFIX_ . "order_history sub_oh " .
			   "	WHERE (sub_oh.id_order = o.id_order) " .
			   " )) " .
			   " WHERE ((YEAR(o.date_add) = " . $year . " ) " . $status_filter . ") " .
			   " GROUP BY MONTH(o.date_add) " .
			   " ORDER BY o.date_add ";
		
		$month_statistics_values = Db::getInstance()->ExecuteS($sql);
		check_db_error();
		
		$month_count = 12;
		$month_index = 1;
		if ($month_statistics_values) {
			foreach ($month_statistics_values as &$month_stat_value) {
				$month_stat_value['total'] = format_price($month_stat_value['value']);
				while ( $month_index < $month_stat_value['date'] ) {
					$zero_result['value'] = 0;
					$zero_result['total'] = format_price(0);
					$zero_result['orders_count'] = 0;
					$zero_result['date'] = $month_index;
					$month_statistics[] = $zero_result;					
					$month_index++;				
				}				
				$month_statistics[] = $month_stat_value;
				$month_index++; 
			}
		}
		while ( $month_index <= $month_count ) {
			$zero_result['value'] = 0;
			$zero_result['total'] = format_price(0);
			$zero_result['orders_count'] = 0;
			$zero_result['date'] = $month_index;
			$month_statistics[] = $zero_result;					
			$month_index++;				
		}

		print_output($month_statistics);	
	}
	
	function print_orders_day_statistics($attr) {
		list($year, $month) = explode("*", $attr, 2);
		$year = pSQL($year);
		$month = pSQL($month);

		if (not_null(STATISTICS_ORDER_STATUSES)) {
			$status_filter = " AND (oh.id_order_state IN (" . STATISTICS_ORDER_STATUSES . "))"; 		
		} else {
			$status_filter = "";
		}

		$sql = "SELECT SUM(o.total_paid) value, COUNT(DISTINCT o.id_order) orders_count, DAY(o.date_add) date " .
			   " FROM " . _DB_PREFIX_ . "orders o " . 
			   " LEFT JOIN " . _DB_PREFIX_ . "order_history oh " .
			   " ON (o.id_order = oh.id_order) AND (oh.id_order_state = ( " .
			   "	SELECT MAX(sub_oh.id_order_state) " . 
			   "	FROM " . _DB_PREFIX_ . "order_history sub_oh " .
			   "	WHERE (sub_oh.id_order = o.id_order) " .
			   " )) " .
			   " WHERE ((YEAR(o.date_add) = " . $year . " ) AND (MONTH(o.date_add) = " . $month . " ) " . $status_filter . ") " .
			   " GROUP BY DAY(o.date_add) " .
			   " ORDER BY o.date_add ";
		
		$day_statistics_values = Db::getInstance()->ExecuteS($sql);
		check_db_error();
		
		$days_count = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$day_index = 1;
		if ($day_statistics_values) {
			foreach ($day_statistics_values as &$day_stat_value) {
				$day_stat_value['total'] = format_price($day_stat_value['value']);
				while ( $day_index < $day_stat_value['date'] ) {
					$zero_result['value'] = 0;
					$zero_result['total'] = format_price(0);
					$zero_result['orders_count'] = 0;
					$zero_result['date'] = $day_index;
					$day_statistics[] = $zero_result;					
					$day_index++;				
				}				
				$day_statistics[] = $day_stat_value;
				$day_index++; 
			}
		}
		while ( $day_index <= $days_count ) {
			$zero_result['value'] = 0;
			$zero_result['total'] = format_price(0);
			$zero_result['orders_count'] = 0;
			$zero_result['date'] = $day_index;
			$day_statistics[] = $zero_result;					
			$day_index++;				
		}	

		print_output($day_statistics);		
	}
	
	function print_orders_hour_statistics($attr) {	
		list($year, $month, $day) = explode("*", $attr, 3);
		$year = pSQL($year);
		$month = pSQL($month);
		$day = pSQL($day);

		if (not_null(STATISTICS_ORDER_STATUSES)) {
			$status_filter = " AND (oh.id_order_state IN (" . STATISTICS_ORDER_STATUSES . "))"; 		
		} else {
			$status_filter = "";
		}

		$sql = "SELECT SUM(o.total_paid) value, COUNT(DISTINCT o.id_order) orders_count, HOUR(o.date_add) date " .
			   " FROM " . _DB_PREFIX_ . "orders o " . 
			   " LEFT JOIN " . _DB_PREFIX_ . "order_history oh " .
			   " ON (o.id_order = oh.id_order) AND (oh.id_order_state = ( " .
			   "	SELECT MAX(sub_oh.id_order_state) " . 
			   "	FROM " . _DB_PREFIX_ . "order_history sub_oh " .
			   "	WHERE (sub_oh.id_order = o.id_order) " .
			   " )) " .
			   " WHERE ((YEAR(o.date_add) = " . $year . " ) AND (MONTH(o.date_add) = " . $month . " ) AND (DAY(o.date_add) = " . $day . " ) " . $status_filter . ") " .
			   " GROUP BY HOUR(o.date_add) " .
			   " ORDER BY o.date_add ";

		$hour_statistics_values = Db::getInstance()->ExecuteS($sql);
		check_db_error();
		
		$hours_count = 23;
		$hour_index = 0;
		if ($hour_statistics_values) {
			foreach ($hour_statistics_values as &$hour_stat_value) {
				$hour_stat_value['total'] = format_price($hour_stat_value['value']);
				while ( $hour_index < $hour_stat_value['date'] ) {
					$zero_result['value'] = 0;
					$zero_result['total'] = format_price(0);
					$zero_result['orders_count'] = 0;
					$zero_result['date'] = $hour_index;
					$hour_statistics[] = $zero_result;					
					$hour_index++;				
				}				
				$hour_statistics[] = $hour_stat_value;
				$hour_index++; 
			}
		}
		while ( $hour_index <= $hours_count ) {
			$zero_result['value'] = 0;
			$zero_result['total'] = format_price(0);
			$zero_result['orders_count'] = 0;
			$zero_result['date'] = $hour_index;
			$hour_statistics[] = $zero_result;					
			$hour_index++;				
		}	

		print_output($hour_statistics);	
	}
	
	function print_customers_year_statistics() {
		$sql = "SELECT COUNT(DISTINCT c.id_customer) new, YEAR(c.date_add) date " .
			   " FROM " . _DB_PREFIX_ . "customer c " .
			   " GROUP BY YEAR(c.date_add) " .
			   " ORDER BY c.date_add ";

		$year_statistics_values = Db::getInstance()->ExecuteS($sql);
		check_db_error();
		
		if ($year_statistics_values) {
			$total_customers_count = 0;
			foreach ($year_statistics_values as &$result) {
				$total_customers_count += $result['new'];
				$result['total'] = $total_customers_count;	
				$year_statistics[] = $result;
			}	
		}
		
		if (isset($year_statistics)) {
			$year_statistics = array_reverse($year_statistics);
			print_output($year_statistics);
		} else {
			print_output(null);
		}
	}
	
	function print_customers_month_statistics($year) {
		$start_total_sql = "SELECT COUNT(DISTINCT c.id_customer) total " .
						   " FROM " . _DB_PREFIX_ . "customer c " .
			   			   " WHERE (YEAR(c.date_add) < " . $year .  " )";
		
		$start_total_values = Db::getInstance()->ExecuteS($start_total_sql);
		check_db_error();
		
		if ($start_total_values) {
			$result = $start_total_values[0];
			$total_customers_count = $result['total']; 
		} else {
			throw new Exception("print_customers_month_statistics, total fetch error!");
		}
				
		$sql = "SELECT COUNT(DISTINCT c.id_customer) new, MONTH(c.date_add) date " .
			   " FROM " . _DB_PREFIX_ . "customer c " .
			   " WHERE (YEAR(c.date_add) = " . $year .  " )" .
			   " GROUP BY MONTH(c.date_add) " .
			   " ORDER BY c.date_add ";

		$month_statistics_values = Db::getInstance()->ExecuteS($sql);
		check_db_error();

		$month_count = 12;
		$month_index = 1;
		if ($month_statistics_values) {
			foreach ($month_statistics_values as &$result) {	
				while ($month_index < $result['date']) {
					$zero_result['new'] = 0;					
					$zero_result['total'] = $total_customers_count;
					$zero_result['date'] = $month_index;
					$month_statistics[] = $zero_result;					
					$month_index++;				
				}
				$total_customers_count += $result['new'];
				$result['total'] = $total_customers_count;				
				$month_statistics[] = $result;
				$month_index++;
			}	
		} 
		while ($month_index <= $month_count) {
			$zero_result['new'] = 0;					
			$zero_result['total'] = $total_customers_count;
			$zero_result['date'] = $month_index;
			$month_statistics[] = $zero_result;					
			$month_index++;				
		}
		
		print_output($month_statistics);
	}
	
	function print_customers_day_statistics($attr) {
		list($year, $month) = explode("*", $attr, 2);
		$year = pSQL($year);
		$month = pSQL($month);

		$start_total_sql = "SELECT COUNT(DISTINCT c.id_customer) total " .
						   " FROM " . _DB_PREFIX_ . "customer c " .
			   			   " WHERE (((YEAR(c.date_add) < " . $year .  " ) " .
						   " OR ((YEAR(c.date_add) = " . $year .  " ) AND (MONTH(c.date_add) < " . $month . "))))";
		
		$start_total_values = Db::getInstance()->ExecuteS($start_total_sql);
		check_db_error();
		
		if ($start_total_values) {
			$result = $start_total_values[0];
			$total_customers_count = $result['total']; 
		} else {
			throw new Exception("print_orders_day_statistics, total fetch error!");
		}

		$sql = "SELECT COUNT(DISTINCT c.id_customer) new, DAY(c.date_add) date " .
			   " FROM " . _DB_PREFIX_ . "customer c " .
			   " WHERE ((YEAR(c.date_add) = " . $year .  " ) " . 
			   " AND (MONTH(c.date_add) = " . $month . ")) " .
			   " GROUP BY DAY(c.date_add) " .
			   " ORDER BY c.date_add ";
		
		$day_statistics_values = Db::getInstance()->ExecuteS($sql);
		check_db_error();

		$days_count = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$day_index = 1;
		if ($day_statistics_values) {
			foreach ($day_statistics_values as &$result) {	
				while ($day_index < $result['date']) {
					$zero_result['new'] = 0;					
					$zero_result['total'] = $total_customers_count;
					$zero_result['date'] = $day_index;
					$day_statistics[] = $zero_result;					
					$day_index++;				
				}				
				$total_customers_count += $result['new'];
				$result['total'] = $total_customers_count;					
				$day_statistics[] = $result;
				$day_index++;
			}	
		}
		while ($day_index <= $days_count) {
			$zero_result['new'] = 0;					
			$zero_result['total'] = $total_customers_count;
			$zero_result['date'] = $day_index;
			$day_statistics[] = $zero_result;					
			$day_index++;				
		}	

		print_output($day_statistics);	
	}
	
	function print_customers_hour_statistics($attr) {
		list($year, $month, $day) = explode("*", $attr, 3);
		$year = pSQL($year);
		$month = pSQL($month);
		$day = pSQL($day);

		$start_total_sql = "SELECT COUNT(DISTINCT c.id_customer) total " .
						   " FROM " . _DB_PREFIX_ . "customer c " .
			   			   " WHERE (((c.date_add) < " . $year .  " ) " .
						   " OR ((YEAR(c.date_add) = " . $year .  " ) AND (MONTH(c.date_add) < " . $month . ")) " . 
						   " OR ((YEAR(c.date_add) = " . $year .  " ) AND (MONTH(c.date_add) = " . $month . ") AND (DAY(c.date_add) < " . $day . ")))";
		
		$start_total_values = Db::getInstance()->ExecuteS($start_total_sql);
		check_db_error();
		
		if ($start_total_values) {
			$result = $start_total_values[0];
			$total_customers_count = $result['total']; 
		} else {
			throw new Exception("print_customers_hour_statistics, total fetch error!");
		}
		
		$sql = "SELECT COUNT(DISTINCT c.id_customer) new, HOUR(c.date_add) date " .
			   " FROM " . _DB_PREFIX_ . "customer c " .
			   " WHERE ((YEAR(c.date_add) = " . $year .  " ) " . 
			   " AND (MONTH(c.date_add) = " . $month . ") " . 
			   " AND (DAY(c.date_add) = " . $day . ")) " .
			   " GROUP BY HOUR(c.date_add) " .
			   " ORDER BY c.date_add ";
		
		$hour_statistics_values = Db::getInstance()->ExecuteS($sql);
		check_db_error();

		$hours_count = 23;
		$hour_index = 0;
		if ($hour_statistics_values) {
			foreach ($hour_statistics_values as &$result) {
				while ($hour_index < $result['date']) {
					$zero_result['new'] = 0;					
					$zero_result['total'] = $total_customers_count;
					$zero_result['date'] = $hour_index;
					$hour_statistics[] = $zero_result;					
					$hour_index++;				
				}				
				$total_customers_count += $result['new'];
				$result['total'] = $total_customers_count;				
				$hour_statistics[] = $result;
				$hour_index++;
			}	
		}
		while ($hour_index <= $hours_count) {
			$zero_result['new'] = 0;					
			$zero_result['total'] = $total_customers_count;
			$zero_result['date'] = $hour_index;
			$hour_statistics[] = $zero_result;					
			$hour_index++;				
		}	

		print_output($hour_statistics);
	}
	
	function check_aes_key_definition() {
		$length = strlen(AES_KEY); 
		if (($length != 16) && ($length != 24) && ($length != 32)) {
			die('DEF_ERR: AES Key must be of length 16, 24 or 32. Currency length is: ' . $length);
		}
		return 0;
	}
	
	function check_main_language_definition() {
		$sql = "SELECT COUNT(*) c " .
			   " FROM " . _DB_PREFIX_ . "lang " .
			   " WHERE id_lang = " . MAIN_LANGUAGE_ID;
	
		$result = Db::getInstance()->getRow($sql);
		check_db_error();
		if ($result['c'] == 0) {
			echo 'DEF_ERR: MAIN_LANGUAGE_ID does not define any language! Defaulting to language with ID: ' . get_language_id() . '...';
		}
		return 0;
	}
	
	function check_main_currency_definition() {
		$sql = "SELECT COUNT(*) c " .
			   " FROM " . _DB_PREFIX_ . "currency " .
			   " WHERE id_currency = " . MAIN_CURRENCY_ID;
	
		$result = Db::getInstance()->getRow($sql);
		check_db_error();
		if ($result['c'] == 0) {
			echo 'DEF_ERR: MAIN_CURRENCY_ID does not define any currency! Defaulting to currency with ID: ' . get_currency_id() . '...';
		}
		return 0;
	}
	
	function check_main_employee_definition() {
		$sql = "SELECT COUNT(*) c " .
			   " FROM " . _DB_PREFIX_ . "employee " .
			   " WHERE id_employee = " . MAIN_EMPLOYEE_ID;
	
		$result = Db::getInstance()->getRow($sql);
		check_db_error();
		if ($result['c'] == 0) {
			echo 'DEF_ERR: MAIN_EMPLOYEE_ID does not define any employee! Defaulting to employee with ID: ' . get_employee_id() . '...';
		}
		return 0;
	}
	
