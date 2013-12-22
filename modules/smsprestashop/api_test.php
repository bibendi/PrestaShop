<?php



		$url_address = "test.sms-prestashop.com"; //write url of your eshop, without http://

		$to = "447971796595"; // number, in international format, no leading zeros or a “+” sign, e.g., 447971796595
		$text = "hello world"; // SMS text
		$unicode=0; // unicode yes=1, no=0
		$type = "customer"; // admin x customer - senderID - from SMS settings TAB
		$sender = "";	// optional, default from SMS settings TAB, not applicable with SYSTEM NUMBER
		

		$query = "to=".$to."&text=".$text."&unicode=".$unicode."&sender=".$sender."&type=".$type;
		
		@$fp = fsockopen ($url_address, 80, $errno, $errstr, 30);
		if (!$fp) {
			    
			    echo "not connected";
		
		} 
		
		else
		{
		
				fwrite($fp, "POST /modules/smsprestashop/api.php HTTP/1.0\r\n");
   				fwrite($fp, "User-Agent: Mozilla/4.0\r\n");
        			fwrite($fp, "Host: ".$url_address."\r\n");
        			fwrite($fp, "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n");
        			fwrite($fp, "Content-length: ".strlen ($query)."\r\n");
        			fwrite($fp, "\r\n".$query."\r\n");
        			
					while (!feof($fp)) {
					$res= fgets ($fp, 30000);
						if(eregi("SMSSTATUS:OK",$res,$regs)) $status = "OK";
						if(eregi("SMSSTATUS:ERROR",$res,$regs)) $status = "ERROR";

					}
					
				fclose ($fp);
		}

		
		
		echo "status: 	".$status; //print status of SMS sending




?>