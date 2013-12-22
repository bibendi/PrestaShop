<?php
error_reporting(E_ALL ^ E_NOTICE);ini_set('error_reporting', E_ALL ^ E_NOTICE);
//include_once( _PS_MODULE_DIR_.'/../classes/AdminTab.php');
include_once(_PS_MODULE_DIR_.'/smsprestashop/Utils.php');
include_once(_PS_MODULE_DIR_.'/smsprestashop/lang.php');


class SmsHistory extends AdminTab
  {

  public $rok, $mesic,$eshopsms,$bulksms,$odesl,$status, $d;

  public function display()
    {

	global $currentIndex;

	if(!strlen($currentIndex)>0):
	  $currentIndex = Utils::get_currentIndex();
	endif;


	$utils2 = new Utils();
	$utils2->loadSmsProfile();
	$this->user5 =  $utils2->user5;
	$this->passwd5 =  $utils2->passwd5;

	$status_array = Array(1=>v_smshistory_sent,2=>v_smshistory_error,3=>v_smshistory_delivered,4=>v_smshistory_buffered);
	$status_icon = Array(1=>"i_sent.png",2=>"i_canceled.gif",3=>"i_accepted.gif",4=>"i_buffered.png");


	if($this->user5 && $this->passwd5):

			$vysl .= '  
				<h2>'.$this->l(v_smshistory_smshistory).'</h2><p>'.v_smshistory_description.'</p>';

			$mesic2=date("m");
			$rok2=date("Y");
			
			if(!$this->rok) $this->rok = $rok2;
			$rok_pred = $rok2-1;
			if(!$this->mesic) $this->mesic = $mesic2;
			
			$vysl .= "<br /><fieldset>
					<legend><img src=\"../img/t/AdminTools.gif\" alt=\"\" /> ".$this->l(v_smshistory_smshistory)."</legend><form action=\"". $currentIndex ."&submitSmsHistory=1&token=".$this->token."\" method=\"post\">";

			$vysl .= "<select name=\"rok\">
        			<option value=\"0\">".v_smshistory_year."</option>";
			$sel="";
			if($this->rok==$rok2) $sel="selected";
			$vysl .= "<option value=\"{$rok2}\"{$sel}>{$rok2}";
			$sel="";
			if($this->rok==$rok_pred) $sel="selected";
			$vysl .= "<option value=\"{$rok_pred}\"{$sel}>{$rok_pred}";		
			$sel="";
			$vysl .= "</select>&nbsp;<select name=\"mesic\">";
			$vysl .= "<option value=\"-1\">".v_smshistory_month."</option>";
			
			for($i=1;$i<13;$i++)
			{
			if($i==$this->mesic) $sel="selected";
			$vysl .= "<option value=\"{$i}\"{$sel}>{$i}";
			$sel="";
			}
			$vysl .="</select>&nbsp;";



			$vysl .= "<select name=\"status\">";
			$vysl .= "<option value=\"0\">".v_smshistory_status."</option>";
			
			while(list($key, $value) = @each($status_array)) 
			{ 
			if($key==$this->status) $sel="selected";
			$vysl .= "<option value=\"".$key."\"".$sel.">".$value."</option>";
			$sel="";
			}
			$vysl .="</select>&nbsp;&nbsp;&nbsp;&nbsp;";


			if($this->eshopsms1==1) $sell31 = " checked=\"checked\"";
			if($this->eshopsms==1) $sell3 = " checked=\"checked\"";
			if($this->bulksms==1) $sell4 = " checked=\"checked\"";
			if($this->bulksms2==1) $sell41 = " checked=\"checked\"";

			if(!$this->eshopsms && !$this->odesl): 
			 $sell3 = " checked=\"checked\"";
			 $this->eshopsms = 1;
			endif;

			if(!$this->eshopsms1 && !$this->odesl): 
			 $sell31 = " checked=\"checked\"";
			 $this->eshopsms1 = 1;
			endif;

			if(!$this->bulksms && !$this->odesl):
			  $sell4 = " checked=\"checked\"";
			  $this->bulksms = 1;
			endif;

			if(!$this->bulksms2 && !$this->odesl):
			  $sell41 = " checked=\"checked\"";
			  $this->bulksms2 = 1;
			endif;

			$vysl .="<input type=\"checkbox\" name=\"eshopsms1\" value=\"1\"".$sell31." />".v_smshistory_adminsms."&nbsp;&nbsp;&nbsp;";
			$vysl .="<input type=\"checkbox\" name=\"eshopsms\" value=\"1\"".$sell3." />".v_smshistory_customersms."&nbsp;&nbsp;&nbsp;";
			$vysl .="<input type=\"checkbox\" name=\"bulksms\" value=\"1\"".$sell4." />".v_smshistory_marketingsms."&nbsp;&nbsp;&nbsp;";
			$vysl .="<input type=\"checkbox\" name=\"bulksms2\" value=\"1\"".$sell41." />".v_smshistory_simplesms."&nbsp;&nbsp;&nbsp;";

			$vysl .="<input type=\"submit\" value=\"".$this->l(v_smshistory_show)."\" name=\"submitSmsHistory\" class=\"button\" /><input type=\"hidden\" name=\"odesl\" value=\"1\" /></form></fieldset><br /><br />
						";
			if($this->mesic==-1) $mes_pre = "%";
			elseif(strlen($this->mesic)==1) $mes_pre = "0".$this->mesic;
			else  $mes_pre = $this->mesic;

			$dot1_pre = null;
			if($this->eshopsms==1)	$dot1_pre[] = 2;
			if($this->eshopsms1==1)	$dot1_pre[] = 1;
			if($this->bulksms==1) $dot1_pre[] = 3;
			if($this->bulksms2==1) $dot1_pre[] = 4;

			if(count($dot1_pre)>0) 	$dot1 = " and type IN (".implode(",",$dot1_pre).")";

			$dot2 = "";
			if($this->status>0):
				$dot2 = " and status=".$this->status;
			endif;

			$date_pre = $this->rok."-".$mes_pre."-%";
			$sql = "SELECT count(*) as pocet FROM "._DB_PREFIX_."sp_sms_history where date like '".$date_pre."'".$dot1."".$dot2."";
			$result = Db::getInstance()->ExecuteS($sql);
			
			if (is_array($result)):
				foreach ($result AS $row)
				{
					$pocet = $row['pocet'];
				}
			endif;

			

			$konec=50;			
			
			if($this->d<0):
			$this->d=0;
			elseif($this->d>$pocet):
			$this->d=0;
			endif;
			$start=$this->d+0;            



			if($pocet>0):

				$od=$this->d+1;
				if($pocet<$this->d+$konec):
					$doo=$pocet;
				else:
					$doo=$this->d+$konec;
				endif;
				if(!$pocet>0) $od=0;
				$vysl .='<div>
				<fieldset>
				<legend><img src="../img/t/AdminCatalog.gif" alt="" /> '.$this->l(v_smshistory_results.$od." - ".$doo).v_smshistory_of.$pocet.' '.v_smshistory_sms.'</legend>';

				$addt = " style=\"padding-top:5px; padding-bottom:5px; border-bottom: 1px solid white; background-color:#ffffff\";";
				$vysl .= "<div".$addt."><div style=\"float:right;width:40px;\">".v_smshistory_status."</div><div style=\"float:right;margin-right:50px;width:120px;\">".v_smshistory_type."</div><div style=\"float:right;margin-right:70px;width:120px;\">".v_smshistory_date."</div><div style=\"float:right;margin-right:40px;width:200px;\">&nbsp;".v_smshistory_subject."</div><div style=\"float:right;margin-right:20px;width:200px;\">&nbsp;".v_smshistory_recipient."</div>&nbsp;".v_smshistory_nubmer."</div>";

				$sql = "SELECT * FROM "._DB_PREFIX_."sp_sms_history where date like '".$date_pre."'".$dot1."".$dot2." order by ID desc limit $start,$konec";
							$result = Db::getInstance()->ExecuteS($sql);

							if (is_array($result)):
								foreach ($result AS $row)
								{
									
										$gg++;
										$addt = '';
										if($gg%2==0):
										 $addt = " style=\"padding-top:5px; padding-bottom:5px; border-bottom: 1px solid white; background-color:#ffffff\";";
										else:
										 $addt = " style=\"padding-top:5px; padding-bottom:5px; border-bottom: 1px solid white;\"";
										endif;  

										switch($row['type']){
										case 1: $typ = v_smshistory_adminsms; break;
										case 2: $typ = v_smshistory_customersms; break;
										case 3: $typ = v_smshistory_marketingsms; break;
										case 4: $typ = v_smshistory_simplesms; break;
										}

										switch($row['unicode']){
										case 1: $un = v_smshistory_yes; break;
										case 0: $un = v_smshistory_no; break;
										}

										if(strlen($row['sender'])>0 && preg_match("/^([0-9])*$/", $row['sender'], $matches)) $senderID = "+".$row['sender'];
										elseif(strlen($row['sender'])>0) $senderID = $row['sender'];
										else  $senderID = v_smshistory_sysnumber;

										$addt33 = '';
										if( ($row['status']==2 || $row['status']==4) && strlen($row['note'])>0 ):
										$addt33 = " - ".$row['note'];
										endif;
										
										$addt3 = '';
										$addt3 = "<span style=\"cursor:help;\" title=\"".$status_array[$row['status']].$addt33."\"><img src='../modules/smsprestashop/".$status_icon[$row['status']]."' style='border:0px;vertical-align:middle;' alt=\"".$status_array[$row['status']].$addt33."\" /></span>"; 

										$nap_smsid = ""; $napis_zmekr = "";
										if($row['price']>0) $napis_zmekr = "<b>".v_smshistory_balance."</b> ".$row['credit']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										
										$nap_cust = "";						
										if($row['customer_ID']>0):
										$nap_cust = "<span style=\"cursor:help;\" title=\"".v_smshistory_customerdetail."\"><a href=\"". $currentIndex ."&submitSmsCustomer=1&id_customer=".$row['customer_ID']."&token=".$this->token."\" onclick=\"window.open(this.href);return false;\" style=\"text-decoration: underline;\">".Utils::more_text($row['recipient'], 16, 18)."</a></span>";
										else:
										$nap_cust = Utils::more_text($row['recipient'], 16, 25);
										endif;
										
										if(strlen($row['smsID'])>0) $nap_smsid = "<br /><b>smsID:</b> ".$row['smsID'];
										$vysl .= "<div".$addt."><div style=\"float:right;width:40px;\">".$addt3."</div><div style=\"float:right;margin-right:50px;width:120px;\">".$typ."</div><div style=\"float:right;margin-right:70px;width:120px;\">".$row['date']."</div><div style=\"float:right;margin-right:40px;width:200px;\">&nbsp;".Utils::more_text($row['subject'], 16, 25)."</div><div style=\"float:right;margin-right:20px;width:200px;\">&nbsp;".$nap_cust."</div><a id=\"displayText".$row['ID']."\" href=\"javascript:toggle".$row['ID']."();\"><img src='../modules/smsprestashop/i_plus.gif' style='border:0px;vertical-align:middle;' /> ".$row['number']."</a><br />
										<div id=\"toggleText".$row['ID']."\" style=\"display: none; padding:8px;\"><b>".v_smshistory_smstext."</b><br />".$row['text']."<br /><br /><b>".v_smshistory_price."</b> ".$row['price']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$napis_zmekr."<b>".v_smshistory_totalsms."</b> ".$row['total']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>".v_smshistory_unicode."</b> ".$un."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>".v_smshistory_senderid."</b> ".$senderID.$nap_smsid."</div>";
										$vysl .= "<script language=\"javascript\">\nfunction toggle".$row['ID']."() {	
										var ele = document.getElementById(\"toggleText".$row['ID']."\"); var text = document.getElementById(\"displayText".$row['ID']."\");
										if(ele.style.display == \"block\") { 
										ele.style.display = \"none\"; 
										text.innerHTML = \"<img src='../modules/smsprestashop/i_plus.gif' style='border:0px;vertical-align:middle;' /> ".$row['number']."\";
										}
										else { 
										ele.style.display = \"block\"; text.innerHTML = \"<img src='../modules/smsprestashop/i_minus.gif' style='border:0px;vertical-align:middle;' /> ".$row['number']."\";
										}
										} </script></div>";  




								}

							$vysl .="<div style=\"text-align:center; padding-top:8px;\">";

									
								$dalsi1=$start+$konec;
								$dalsi2=$start-$konec;
								if($pocet>$dalsi2 && !$this->d<=0):
									if($dalsi2<1):
									$dalsi2=0;
									endif;
									$vysl .="<div style=\"float:left;\"><form action=\"". $currentIndex ."&submitSmsHistoryPrevious=1&token=".$this->token."\" method=\"post\"><input type=\"submit\" value=\"".$this->l(v_smshistory_previous)."\" name=\"submitSmsHistoryPrevious\" class=\"button\" />
									<input type=\"hidden\" name=\"odesl\" value=\"1\" />
									<input type=\"hidden\" name=\"status\" value=\"".$this->status."\" />
									<input type=\"hidden\" name=\"bulksms\" value=\"".$this->bulksms."\" />
									<input type=\"hidden\" name=\"bulksms2\" value=\"".$this->bulksms2."\" />
									<input type=\"hidden\" name=\"eshopsms\" value=\"".$this->eshopsms."\" /><input type=\"hidden\" name=\"eshopsms1\" value=\"".$this->eshopsms1."\" />
									<input type=\"hidden\" name=\"mesic\" value=\"".$this->mesic."\" />
									<input type=\"hidden\" name=\"rok\" value=\"".$this->rok."\" />
									<input type=\"hidden\" name=\"d\" value=\"".$dalsi2."\" />
									</form></div>";
								endif;
								if($pocet>$dalsi1):
									$vysl .="<div style=\"float:right;\"><form action=\"". $currentIndex ."&submitSmsHistoryNext=1&token=".$this->token."\" method=\"post\"><input type=\"submit\" value=\"".$this->l(v_smshistory_next)."\" name=\"submitSmsHistoryNext\" class=\"button\" />
									<input type=\"hidden\" name=\"odesl\" value=\"1\" />
									<input type=\"hidden\" name=\"status\" value=\"".$this->status."\" />
									<input type=\"hidden\" name=\"bulksms2\" value=\"".$this->bulksms2."\" />
									<input type=\"hidden\" name=\"bulksms\" value=\"".$this->bulksms."\" />
									<input type=\"hidden\" name=\"eshopsms\" value=\"".$this->eshopsms."\" /><input type=\"hidden\" name=\"eshopsms1\" value=\"".$this->eshopsms1."\" />
									<input type=\"hidden\" name=\"mesic\" value=\"".$this->mesic."\" />
									<input type=\"hidden\" name=\"rok\" value=\"".$this->rok."\" />
									<input type=\"hidden\" name=\"d\" value=\"".$dalsi1."\" />
									</form></div>";
								endif;        
								if($pocet>0):
								$pocet_stranek = ceil($pocet/$konec);
								$vysl .=  "<br />";		
								for($sa=0;$sa<$pocet_stranek;$sa++)
												{
									//$pocet_stranek
									if(($konec*$sa)==$start) $asdvde[] =   ($sa+1); 
									elseif($sa==0) $asdvde[] =  "<a style=\"text-decoration:underline;\" href=\"". $currentIndex ."&submitSmsHistoryPage=1&token=".$this->token."&mesic=".$this->mesic."&rok=".$this->rok."&odesl=".$this->odesl."&status=".$this->status."&bulksms=".$this->bulksms."&bulksms2=".$this->bulksms2."&eshopsms=".$this->eshopsms."&eshopsms1=".$this->eshopsms1."\">". ($sa+1)."</a>";
									else $asdvde[] =  "<a style=\"text-decoration:underline;\" href=\"". $currentIndex ."&submitSmsHistoryPage=1&token=".$this->token."&d=".($konec*$sa)."&mesic=".$this->mesic."&rok=".$this->rok."&odesl=".$this->odesl."&status=".$this->status."&bulksms2=".$this->bulksms2."&bulksms=".$this->bulksms."&eshopsms=".$this->eshopsms."&eshopsms1=".$this->eshopsms1."\">". ($sa+1)."</a>";
									}
									if(count($asdvde)>1) $vysl .=  v_smshistory_page.@implode(" | ",$asdvde);

								endif;

							$vysl .="</div><div class=\"clear\"></div>";


							endif;





			else:
				$vysl .= '
				<div style="padding-left:10px;">
				<b>'.v_smshistory_nosms.'</b></div><br />';
			endif;






			echo $vysl;

	else:

			echo '  
			<h2>'.$this->l(v_smshistory_smsnotactive).'</h2>
			<div>'.v_smshistory_usernotactive.'</div>';
	endif;


   }




	


	public function postProcess()
	{
		


		


		if (Tools::isSubmit('submitSmsHistory') )
		{			

			$this->rok = htmlentities( Tools::getValue( 'rok' ), ENT_COMPAT, 'UTF-8' );
			$this->mesic = htmlentities( Tools::getValue( 'mesic' ), ENT_COMPAT, 'UTF-8' );
			$this->eshopsms = htmlentities( Tools::getValue( 'eshopsms' ), ENT_COMPAT, 'UTF-8' );
			$this->eshopsms1 = htmlentities( Tools::getValue( 'eshopsms1' ), ENT_COMPAT, 'UTF-8' );
			$this->bulksms = htmlentities( Tools::getValue( 'bulksms' ), ENT_COMPAT, 'UTF-8' );
			$this->bulksms2 = htmlentities( Tools::getValue( 'bulksms2' ), ENT_COMPAT, 'UTF-8' );
			$this->odesl = htmlentities( Tools::getValue( 'odesl' ), ENT_COMPAT, 'UTF-8' );
			$this->status = htmlentities( Tools::getValue( 'status' ), ENT_COMPAT, 'UTF-8' );		

			
		}

		elseif (Tools::isSubmit('submitSmsHistoryNext') )
		{			

			$this->rok = htmlentities( Tools::getValue( 'rok' ), ENT_COMPAT, 'UTF-8' );
			$this->mesic = htmlentities( Tools::getValue( 'mesic' ), ENT_COMPAT, 'UTF-8' );
			$this->eshopsms = htmlentities( Tools::getValue( 'eshopsms' ), ENT_COMPAT, 'UTF-8' );
			$this->eshopsms1 = htmlentities( Tools::getValue( 'eshopsms1' ), ENT_COMPAT, 'UTF-8' );
			$this->bulksms = htmlentities( Tools::getValue( 'bulksms' ), ENT_COMPAT, 'UTF-8' );
			$this->bulksms2 = htmlentities( Tools::getValue( 'bulksms2' ), ENT_COMPAT, 'UTF-8' );
			$this->odesl = htmlentities( Tools::getValue( 'odesl' ), ENT_COMPAT, 'UTF-8' );
			$this->status = htmlentities( Tools::getValue( 'status' ), ENT_COMPAT, 'UTF-8' );		
			$this->d = htmlentities( Tools::getValue( 'd' ), ENT_COMPAT, 'UTF-8' );		
			
		}
		elseif (Tools::isSubmit('submitSmsHistoryPrevious') )
		{			

			$this->rok = htmlentities( Tools::getValue( 'rok' ), ENT_COMPAT, 'UTF-8' );
			$this->mesic = htmlentities( Tools::getValue( 'mesic' ), ENT_COMPAT, 'UTF-8' );
			$this->eshopsms = htmlentities( Tools::getValue( 'eshopsms' ), ENT_COMPAT, 'UTF-8' );
			$this->eshopsms1 = htmlentities( Tools::getValue( 'eshopsms1' ), ENT_COMPAT, 'UTF-8' );
			$this->bulksms = htmlentities( Tools::getValue( 'bulksms' ), ENT_COMPAT, 'UTF-8' );
			$this->bulksms2 = htmlentities( Tools::getValue( 'bulksms2' ), ENT_COMPAT, 'UTF-8' );
			$this->odesl = htmlentities( Tools::getValue( 'odesl' ), ENT_COMPAT, 'UTF-8' );
			$this->status = htmlentities( Tools::getValue( 'status' ), ENT_COMPAT, 'UTF-8' );		
			$this->d = htmlentities( Tools::getValue( 'd' ), ENT_COMPAT, 'UTF-8' );		
			
		}
		elseif (Tools::isSubmit('submitSmsHistoryPage') )
		{			

			$this->rok = htmlentities( Tools::getValue( 'rok' ), ENT_COMPAT, 'UTF-8' );
			$this->mesic = htmlentities( Tools::getValue( 'mesic' ), ENT_COMPAT, 'UTF-8' );
			$this->eshopsms = htmlentities( Tools::getValue( 'eshopsms' ), ENT_COMPAT, 'UTF-8' );
			$this->eshopsms1 = htmlentities( Tools::getValue( 'eshopsms1' ), ENT_COMPAT, 'UTF-8' );
			$this->bulksms = htmlentities( Tools::getValue( 'bulksms' ), ENT_COMPAT, 'UTF-8' );
			$this->bulksms2 = htmlentities( Tools::getValue( 'bulksms2' ), ENT_COMPAT, 'UTF-8' );
			$this->odesl = htmlentities( Tools::getValue( 'odesl' ), ENT_COMPAT, 'UTF-8' );
			$this->status = htmlentities( Tools::getValue( 'status' ), ENT_COMPAT, 'UTF-8' );		
			$this->d = htmlentities( Tools::getValue( 'd' ), ENT_COMPAT, 'UTF-8' );		

		}

		elseif (Tools::isSubmit('submitSmsCustomer') )
		{			
			$id_customer = htmlentities( Tools::getValue( 'id_customer' ), ENT_COMPAT, 'UTF-8' );	
			Tools::redirectAdmin("index.php?tab=AdminCustomers&id_customer=".$id_customer."&viewcustomer&token=".Utils::getAdminTokenLite1('AdminCustomers'));
		}


		return parent::postProcess();
	}





	public function displayConfirmation($string)
	{
	 	$output = '
		<div class="module_confirmation conf confirm">
			<img src="'._PS_IMG_.'admin/ok.gif" alt="" title="" /> '.$string.'
		</div>';
		return $output;
	}


}