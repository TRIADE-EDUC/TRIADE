<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2023/01/03 09:51:33 dbellamy Exp $

if (stristr($_SERVER ['REQUEST_URI'], '.inc.php')) {
    die('no access');
}

global $base_path;
global $action, $crypto_data_to_encrypt, $crypto_data_to_decrypt;
global $PMBuserid;

require_once "$base_path/plugins/crypto/classes/CryptoPlugin.php";

//Limitation accès à l'utilisateur d'ID = 1
if(1 != $PMBuserid) {
    $sub = "no_access";
}

switch ($sub) {
    
    case "no_access" :
        echo CryptoPlugin::getNoAccessForm();
        break;
        
	case "key_generation" :
	    echo CryptoPlugin::getKeyGenerationForm();
	    break;
	    
	case "data_encryption" :
	    
	    switch($action) {
	        
	        case 'encrypt' :
	            
	            if(empty($crypto_data_to_encrypt) || !is_string($crypto_data_to_encrypt) ) {
	                $crypto_data_to_encrypt = '';
	            }
	            
        	    echo CryptoPlugin::getDataEncryptionForm($crypto_data_to_encrypt, 'encrypt');
	            break;
	            
	        case 'decrypt' :
	            if(empty($crypto_data_to_decrypt) || !is_string($crypto_data_to_decrypt) ) {
	                $crypto_data_to_decrypt = '';
	            }
	            echo CryptoPlugin::getDataEncryptionForm($crypto_data_to_decrypt, 'decrypt');
	            break;
	        default :
	            echo CryptoPlugin::getDataEncryptionForm();
	            break;
	    }
	    break;
}
 
