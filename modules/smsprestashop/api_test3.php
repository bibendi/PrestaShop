<?php



		$url_address = "test.sms-prestashop.com"; //write url of your eshop, without http://

		$to = "447971796595"; // number, in international format, no leading zeros or a “+” sign, e.g., 447971796595
		$text = "hello world"; // SMS text
		$unicode=0; // unicode yes=1, no=0
		$type = "customer"; // admin x customer - senderID - from SMS settings TAB
		$sender = "";	// optional, default from SMS settings TAB, not applicable with SYSTEM NUMBER
		

		$query = "to=".urlencode($to)."&text=".urlencode($text)."&unicode=".$unicode."&sender=".urlencode($sender)."&type=".$type;
		
		
		
		function URLopen($url)
			{                                                                                                                 
				return file_get_contents("$url");
			} 


		
		
		@$data = URLopen("http://".$url_address."/modules/smsprestashop/api.php?".$query);
		if (!$data) {
			    
			    echo "not connected";
		
		} 
		
		else
		{
		
				$res = $data;
        			if(eregi("SMSSTATUS:OK",$res,$regs)) $status = "OK";
				if(eregi("SMSSTATUS:ERROR",$res,$regs)) $status = "ERROR";

					
		}

		
		
		echo "status: 	".$status; //print status of SMS sending




?>