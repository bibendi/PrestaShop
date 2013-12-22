<?php
error_reporting(E_ALL ^ E_NOTICE);ini_set('error_reporting', E_ALL ^ E_NOTICE);
//include_once( _PS_MODULE_DIR_.'/../classes/AdminTab.php');
include_once(_PS_MODULE_DIR_.'/smsprestashop/Utils.php');
include_once(_PS_MODULE_DIR_.'/smsprestashop/lang.php');

class SmsAnswers extends AdminTab
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




	if($this->user5 && $this->passwd5):

			$vysl .= '  
				<h2>'.$this->l(v_smsanswers_smsanswers).'</h2><p>'.v_smsanswers_description.'</p>';

			$mesic2=date("m");
			$rok2=date("Y");
			
			if(!$this->rok) $this->rok = $rok2;
			$rok_pred = $rok2-1;
			if(!$this->mesic) $this->mesic = $mesic2;
			
			$vysl .= "<br /><fieldset>
					<legend><img src=\"../img/t/AdminTools.gif\" alt=\"\" /> ".$this->l(v_smsanswers_smsanswers)."</legend><form action=\"". $currentIndex ."&submitSmsAnswers=1&token=".$this->token."\" method=\"post\">";

			$vysl .= "<select name=\"rok\">
        			<option value=\"0\">".v_smsanswers_year."</option>";
			$sel="";
			if($this->rok==$rok2) $sel="selected";
			$vysl .= "<option value=\"{$rok2}\"{$sel}>{$rok2}";
			$sel="";
			if($this->rok==$rok_pred) $sel="selected";
			$vysl .= "<option value=\"{$rok_pred}\"{$sel}>{$rok_pred}";		
			$sel="";
			$vysl .= "</select>&nbsp;<select name=\"mesic\">";
			$vysl .= "<option value=\"-1\">".v_smsanswers_month."</option>";
			
			for($i=1;$i<13;$i++)
			{
			if($i==$this->mesic) $sel="selected";
			$vysl .= "<option value=\"{$i}\"{$sel}>{$i}";
			$sel="";
			}
			$vysl .="</select>&nbsp;";



		


			$vysl .="<input type=\"submit\" value=\"".$this->l(v_smsanswers_show)."\" name=\"submitSmsAnswers\" class=\"button\" /><input type=\"hidden\" name=\"odesl\" value=\"1\" /></form></fieldset><br /><br />
						";
			if($this->mesic==-1) $mes_pre = "%";
			elseif(strlen($this->mesic)==1) $mes_pre = "0".$this->mesic;
			else  $mes_pre = $this->mesic;



			$date_pre = $this->rok."-".$mes_pre."-%";
			$sql = "SELECT count(*) as pocet FROM "._DB_PREFIX_."sp_answers where cas like '".$date_pre."'".$dot1."".$dot2."";
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
				<legend><img src="../img/t/AdminCatalog.gif" alt="" /> '.$this->l(v_smsanswers_results.$od." - ".$doo).v_smsanswers_of.$pocet.' '.v_smsanswers_sms.'</legend>';

				$addt = " style=\"padding-top:5px; padding-bottom:5px; border-bottom: 1px solid white; background-color:#ffffff\";";
				$vysl .= "<div".$addt."><div style=\"float:right;width:50px;\">&nbsp;</div><div style=\"float:right;margin-right:120px;width:120px;text-align:left;\">&nbsp;".v_smsanswers_smscenter."</div><div style=\"float:right;margin-right:170px;width:140px;text-align:left;\">".v_smsanswers_date."</div>&nbsp;".v_smsanswers_number."</div>";

				$sql = "SELECT * FROM "._DB_PREFIX_."sp_answers where cas like '".$date_pre."'".$dot1."".$dot2." order by ID desc limit $start,$konec";
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
								
										$nap_smsc = ""; $prohlednuto_text = "";
										if(strlen($row['smsc']>0)) $nap_smsc = "+".trim($row['smsc']);
										if($row['prohlednuto']==0) $prohlednuto_text = "<span style=\"text-decoration: blink;color:red;\">".v_smsanswers_new."</span> ";
										
										$vysl .= "<div".$addt."><div style=\"float:right;width:50px;\">&nbsp;".$prohlednuto_text."</div><div style=\"float:right;margin-right:120px;width:120px;\">".$nap_smsc."</div><div style=\"float:right;margin-right:170px;width:140px;\">".$row['cas']."</div><a id=\"displayText".$row['ID']."\" href=\"javascript:toggle".$row['ID']."();\"><img src='../modules/smsprestashop/i_plus.gif' style='border:0px;vertical-align:middle;' /> ".$row['from']."</a><br />
										<div id=\"toggleText".$row['ID']."\" style=\"display: none; padding:8px;\"><b>".v_smsanswers_text."</b><br />".$row['text']."<br /></div>";
										$vysl .= "<script language=\"javascript\">\nfunction toggle".$row['ID']."() {	
										var ele = document.getElementById(\"toggleText".$row['ID']."\"); var text = document.getElementById(\"displayText".$row['ID']."\");
										if(ele.style.display == \"block\") { 
										ele.style.display = \"none\"; 
										text.innerHTML = \"<img src='../modules/smsprestashop/i_plus.gif' style='border:0px;vertical-align:middle;' /> ".$row['from']."\";
										}
										else { 
										ele.style.display = \"block\"; text.innerHTML = \"<img src='../modules/smsprestashop/i_minus.gif' style='border:0px;vertical-align:middle;' /> ".$row['from']."\";
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
									$vysl .="<div style=\"float:left;\"><form action=\"". $currentIndex ."&submitSmsAnswersPrevious=1&token=".$this->token."\" method=\"post\"><input type=\"submit\" value=\"".$this->l(v_smsanswers_previous)."\" name=\"submitSmsAnswersPrevious\" class=\"button\" />
									<input type=\"hidden\" name=\"odesl\" value=\"1\" />
									<input type=\"hidden\" name=\"mesic\" value=\"".$this->mesic."\" />
									<input type=\"hidden\" name=\"rok\" value=\"".$this->rok."\" />
									<input type=\"hidden\" name=\"d\" value=\"".$dalsi2."\" />
									</form></div>";
								endif;
								if($pocet>$dalsi1):
									$vysl .="<div style=\"float:right;\"><form action=\"". $currentIndex ."&submitSmsAnswersNext=1&token=".$this->token."\" method=\"post\"><input type=\"submit\" value=\"".$this->l(v_smsanswers_next)."\" name=\"submitSmsAnswersNext\" class=\"button\" />
									<input type=\"hidden\" name=\"odesl\" value=\"1\" />
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
									elseif($sa==0) $asdvde[] =  "<a style=\"text-decoration:underline;\" href=\"". $currentIndex ."&submitSmsAnswersPage=1&token=".$this->token."&mesic=".$this->mesic."&rok=".$this->rok."&odesl=".$this->odesl."\">". ($sa+1)."</a>";
									else $asdvde[] =  "<a style=\"text-decoration:underline;\" href=\"". $currentIndex ."&submitSmsAnswersPage=1&token=".$this->token."&d=".($konec*$sa)."&mesic=".$this->mesic."&rok=".$this->rok."&odesl=".$this->odesl."\">". ($sa+1)."</a>";
									}
									if(count($asdvde)>1) $vysl .=  v_smsanswers_page.@implode(" | ",$asdvde);

								endif;

							$vysl .="</div><div class=\"clear\"></div>";


							endif;


				$sql = "update "._DB_PREFIX_."sp_answers set prohlednuto=1";
				$result = Db::getInstance()->Execute($sql);


			else:
				$vysl .= '
				<div style="padding-left:10px;">
				<b>'.v_smsanswers_nosms.'</b></div><br />';
			endif;






			echo $vysl;

	else:

			echo '  
			<h2>'.$this->l(v_smsanswers_smsnotactive).'</h2>
			<div>'.v_smsanswers_usernotactive.'</div>';
	endif;


   }




	


	public function postProcess()
	{
		


		if (Tools::isSubmit('submitSmsAnswers') )
		{			

			$this->rok = htmlentities( Tools::getValue( 'rok' ), ENT_COMPAT, 'UTF-8' );
			$this->mesic = htmlentities( Tools::getValue( 'mesic' ), ENT_COMPAT, 'UTF-8' );
			$this->odesl = htmlentities( Tools::getValue( 'odesl' ), ENT_COMPAT, 'UTF-8' );


			
		}

		elseif (Tools::isSubmit('submitSmsAnswersNext') )
		{			

			$this->rok = htmlentities( Tools::getValue( 'rok' ), ENT_COMPAT, 'UTF-8' );
			$this->mesic = htmlentities( Tools::getValue( 'mesic' ), ENT_COMPAT, 'UTF-8' );
			$this->odesl = htmlentities( Tools::getValue( 'odesl' ), ENT_COMPAT, 'UTF-8' );
			$this->d = htmlentities( Tools::getValue( 'd' ), ENT_COMPAT, 'UTF-8' );		
			
		}
		elseif (Tools::isSubmit('submitSmsAnswersPrevious') )
		{			

			$this->rok = htmlentities( Tools::getValue( 'rok' ), ENT_COMPAT, 'UTF-8' );
			$this->mesic = htmlentities( Tools::getValue( 'mesic' ), ENT_COMPAT, 'UTF-8' );
			$this->odesl = htmlentities( Tools::getValue( 'odesl' ), ENT_COMPAT, 'UTF-8' );	
			$this->d = htmlentities( Tools::getValue( 'd' ), ENT_COMPAT, 'UTF-8' );		
			
		}
		elseif (Tools::isSubmit('submitSmsAnswersPage') )
		{			

			$this->rok = htmlentities( Tools::getValue( 'rok' ), ENT_COMPAT, 'UTF-8' );
			$this->mesic = htmlentities( Tools::getValue( 'mesic' ), ENT_COMPAT, 'UTF-8' );
			$this->odesl = htmlentities( Tools::getValue( 'odesl' ), ENT_COMPAT, 'UTF-8' );	
			$this->d = htmlentities( Tools::getValue( 'd' ), ENT_COMPAT, 'UTF-8' );		

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