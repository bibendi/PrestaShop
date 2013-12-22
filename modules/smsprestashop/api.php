<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/Utils.php');
include_once(dirname(__FILE__).'/lang.php');



if(strlen($_POST["to"])):
$to = $_POST["to"]; // number, in international format, no leading zeros or a “+” sign, e.g., 447971796595
elseif(strlen($_GET["to"])):
$to = $_GET["to"]; //  number, in international format, no leading zeros or a “+” sign, e.g., 447971796595
endif;

if(strlen($_POST["text"])):
$text = $_POST["text"]; // sms text
elseif(strlen($_GET["text"])):
$text = $_GET["text"]; //  sms text
endif;

if(strlen($_POST["unicode"])):
$unicode = $_POST["unicode"]; // no=0, yes=1
elseif(strlen($_GET["unicode"])):
$unicode = $_GET["unicode"]; // no=0, yes=1
endif;

if(strlen($_POST["type"])):
$type = $_POST["type"]; // admin x customer
elseif(strlen($_GET["type"])):
$type = $_GET["type"]; // admin x customer
endif;

if(strlen($_POST["sender"])):
$sender = $_POST["sender"]; // optional, default from SMS settings, not applicable with SYSTEM NUMBER
elseif(strlen($_GET["sender"])):
$sender = $_GET["sender"]; // optional, default from SMS settings, not applicable with SYSTEM NUMBER
endif;





if(strlen($to)>4 && strlen($text)>0):
  $utils = new Utils();
  $sms = $utils->SendSimpleSms($to, $text, $unicode, $type, $sender, $utils);
  if($sms):
    echo "SMSSTATUS:OK";
  else:
    echo "SMSSTATUS:ERROR";
  endif;
else:
    echo "SMSSTATUS:ERROR";
endif;
  
?>