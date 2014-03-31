<?php
/*
 * Prestashop interface encryption for eCommerce Manager app
 * KIS Software - www.kis-ecommerce.com
 * Version: 3.0
 */

function encryptString($RAWDATA)
{
    $key = AES_KEY;
    // encrypt string
    $td = mcrypt_module_open('rijndael-128','','ecb','');
    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $encrypted_string = mcrypt_generic($td, pkcs5_pad($RAWDATA, 16));
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    // base-64 encode
    return base64_encode($encrypted_string);
}

function decryptString($ENCRYPTEDDATA)
{
    $key = AES_KEY;
    // base-64 decode
    $encrypted_string = base64_decode($ENCRYPTEDDATA);
    // decrypt string
    $td = mcrypt_module_open('rijndael-128', '', 'ecb', '');
    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $returned_string = mdecrypt_generic($td, $encrypted_string);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);    
    unset($encrypted_string);
    return pkcs5_unpad($returned_string);
}

function pkcs5_pad($text, $blocksize) 
{ 
    $pad = $blocksize - (strlen($text) % $blocksize); 
    return $text . str_repeat(chr($pad), $pad); 
} 

function pkcs5_unpad($text) 
{ 
    $pad = ord($text{strlen($text)-1}); 
    if ($pad > strlen($text)) return $text; 
    if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return $text; 
    return substr($text, 0, -1 * $pad); 
} 