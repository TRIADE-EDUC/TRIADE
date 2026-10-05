<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CryptoPlugin.php,v 1.4 2023/10/11 10:44:03 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], '.class.php')) {
    die('no access');
}

use Pmb\Common\Library\Crypto\Crypto;

class CryptoPlugin {

    static protected $templates = [];
    
    static protected $crypto_class = null;
    static protected $keys_already_defined = false;
    
    private function __construct()
    {
    }

    /**
     * Chargement des templates d'affichage
     */
    protected static function loadTemplates()
    {
        if(!empty(static::$templates)) {
            return;
        }        
        require_once __DIR__.'/../includes/templates/crypto_plugin.tpl.php';
        if(!empty($crypto_plugin_templates)) {
            static::$templates = $crypto_plugin_templates;
        }
    }
    
    
    /** 
     * Chargement de la classe crypto et vérification de la présence des clés RSA
     */
    protected static function loadPMBRSAContext()
    {
        if( is_null(static::$crypto_class) ) {
            $crypto = static::$crypto_class = new Crypto();
            
            try {
                $crypto->loadPMBRSAContext();
                static::$keys_already_defined = true;
            } catch(Exception $e) {
            }
        }
    }

    
    public static function getNoAccessForm()
    {
        static::loadTemplates();
        $tpl = static::$templates['no_access'];
        return $tpl;
    }
    
    
    public static function getKeyGenerationForm()
    {
        static::loadTemplates();
        $tpl = static::$templates['key_generation'];
        
        static::loadPMBRSAContext();
        $crypto = static::$crypto_class;
        
        $RSA_keys = ['public' => '', 'private' => ''];
        
        try {
            $RSA_keys = $crypto->generateRSAKeyPair();
        } catch(Exception $e) {
        }
        
        $tpl = str_replace('<!-- crypto_private_key_value -->', $RSA_keys['private'], $tpl);
        $tpl = str_replace('<!-- crypto_public_key_value -->',  $RSA_keys['public'], $tpl);
        
        if(static::$keys_already_defined) {
            $tpl = str_replace('<!-- crypto_keys_already_defined -->', static::$templates['keys_already_defined'], $tpl);
        }
        return $tpl;
    }

    
    public static function getDataEncryptionForm(string $data = '', string $action = '')
    {
        static::loadTemplates();
        $tpl = static::$templates['data_encryption'];
        
        static::loadPMBRSAContext();
        $crypto = static::$crypto_class;
        
        if(!static::$keys_already_defined) {
            $tpl = str_replace('<!-- crypto_keys_not_defined -->', static::$templates['keys_not_defined'], $tpl);
            $action = '';
        }
        
        $data= stripslashes($data);
        $decrypted_data = '';
        $encrypted_data = '';

        switch($action) {
            
            case 'encrypt' : 
                
                $tpl = str_replace('<!-- crypto_data_to_encrypt -->', $data, $tpl);
                $tpl = str_replace('<!-- crypto_data_to_decrypt -->', '', $tpl);
                
                // Chiffrement avec la clé privée
                try {

                    $encrypted_data = $crypto->encryptToHexa($data);

                } catch (Exception $e) {

                    $error_msg = plugins::get_message('crypto', 'crypto_encrypt_error');
                    $tpl = str_replace('<!-- crypto_encrypted_data -->', $error_msg, $tpl);
                    $tpl = str_replace('<!-- crypto_decrypted_data -->', '', $tpl);
                    return $tpl;
                }
                $tpl = str_replace('<!-- crypto_encrypted_data -->', $encrypted_data, $tpl);
                
                // Vérification par déchiffrement avec la clé publique
                try {

                    $decrypted_data = $crypto->decryptFromHexa($encrypted_data);

                } catch (Exception $e) {

                    $error_msg = plugins::get_message('crypto', 'crypto_decrypt_error');
                    $tpl = str_replace('<!-- crypto_decrypted_data -->', $error_msg, $tpl);
                    return $tpl;

                }
                $tpl = str_replace('<!-- crypto_decrypted_data -->', $decrypted_data, $tpl);

                break;

            case 'decrypt' : 
                
                        $tpl = str_replace('<!-- crypto_data_to_encrypt -->', '', $tpl);
                        $tpl = str_replace('<!-- crypto_data_to_decrypt -->', $data, $tpl);      

                // Déchiffrement avec la clé publique
                try {

                    $decrypted_data = $crypto->decryptFromHexa($data);

                } catch (\Exception $e) {

                    $error_msg = plugins::get_message('crypto', 'crypto_decrypt_error');
                        $tpl = str_replace('<!-- crypto_decrypted_data -->', $error_msg, $tpl);
                    $tpl = str_replace('<!-- crypto_encrypted_data -->', '', $tpl);
                        return $tpl;
                    }
                $tpl = str_replace('<!-- crypto_decrypted_data -->', $decrypted_data, $tpl);
                
                //Vérification par chiffrement avec la clé privée
                try {

                    $encrypted_data = $crypto->encryptToHexa($decrypted_data);

                } catch(\Exception $e) {

                    $error_msg = plugins::get_message('crypto', 'crypto_encrypt_error');
                    $tpl = str_replace('<!-- crypto_encrypted_data -->', $error_msg, $tpl);
                }
                $tpl = str_replace('<!-- crypto_encrypted_data -->', $encrypted_data, $tpl);

                break;
            
            default :
                $tpl = str_replace('<!-- crypto_data_to_encrypt -->', '', $tpl);
                $tpl = str_replace('<!-- crypto_encrypted_data -->', '', $tpl);
                $tpl = str_replace('<!-- crypto_data_to_decrypt -->', '', $tpl);
                $tpl = str_replace('<!-- crypto_decrypted_data -->', '', $tpl);
                break;
        }

        return $tpl;
    }
}
