<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Crypto.php,v 1.5 2023/10/11 10:44:03 dbellamy Exp $

namespace Pmb\Common\Library\Crypto;

use Exception;

class Crypto{
    
    
    const DEFAULT_PRIVATE_KEY_RSA_BITS = 4096;
    const RSA_CHUNK_SIZE = 256;
    const CHUNK_SEPARATOR = ":::";
    const INDICATOR = "{crypto}";
    
    private $public_RSA_key = null;
    private $private_RSA_key = null;
    
    protected $charset;
    
    /**
     * Initialisation du contexte à partir des paramètres définis dans PMB,
     * ou dans le tableau "$overload_global_parameters" (fichier "config_local.inc.php")
     * 
     * @throws \Exception
     */
    public function loadPMBRSAContext()
    {
        global $pmb_public_rsa_key, $pmb_private_rsa_key;
        global $charset;
        
        $this->charset = $charset;
        
        if(!empty($pmb_public_rsa_key) && !empty($pmb_private_rsa_key)) {
            try {
                $this->loadPublicRSAKey($pmb_public_rsa_key);
                $this->loadPrivateRSAKey($pmb_private_rsa_key);
            } catch (\Exception $e) {
                throw $e;
            }
            return;
        }
        throw new \Exception('Error loading RSA context');
    }
    
    
    /**
     * Génère une paire de clés RSA et les retourne au format PEM
     * 
     * @param int $private_key_bits : : Longueur clé privée (bits)
     * 
     * @throws \Exception
     * 
     * @return string[] : ['public' => '?', 'private' => '?']
     */
    public function generateRSAKeyPair(int $private_key_bits = Crypto::DEFAULT_PRIVATE_KEY_RSA_BITS)
    {
        $private_key_bits = intval($private_key_bits);
        if (!$private_key_bits) {
            $private_key_bits = Crypto::DEFAULT_PRIVATE_KEY_RSA_BITS;
        }
        $options = [
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
            "private_key_bits" => $private_key_bits,
        ];
        
        $RSA_key = openssl_pkey_new($options);
        
        if (false == $RSA_key) {
            throw new \Exception('Error during key generation');
        }
        
        $public_key_pem = openssl_pkey_get_details($RSA_key)['key'];
        $private_key_pem = '';
        openssl_pkey_export($RSA_key, $private_key_pem);
        return [
            'public' => $public_key_pem, 
            'private' => $private_key_pem,
        ];
    }
    
    /**
     * Lecture clé publique RSA 
     * 
     * @param mixed $data : Chaine PEM | Chemin vers fichier PEM 'file://path/to/file.pem' | Instance OpenSSLAsymmetricKey
     * @throws Exception
     */
    public function loadPublicRSAKey($data)
    {
        $public_RSA_key = openssl_pkey_get_public($data);
        if (false == $public_RSA_key) {
            throw New \Exception('Error loading public key ');
        }
        $this->public_RSA_key = $public_RSA_key;
    }
    
    
    /**
     * Lecture clé privée RSA
     *
     * @param mixed $data : Chaine PEM | Chemin vers fichier PEM 'file://path/to/file.pem' | Instance OpenSSLAsymmetricKey
     * @throws Exception
     */
    public function loadPrivateRSAKey($data)
    {
        $private_RSA_key = openssl_pkey_get_private($data);
        if (false == $private_RSA_key) {
            throw New \Exception('Error loading private key ');
        }
        $this->private_RSA_key = $private_RSA_key;
    }
    
    
    /**
     * Chiffrement de données avec la clé RSA privée
     * Les données sont traitées par tronçons
     * 
     * @param string $data
     * @throws \Exception
     * 
     * @return array
     */
    public function encryptWithPrivateRSAKey(string $data)
    {
        if( is_null($this->private_RSA_key) ) {
            throw new \Exception ('Private key not loaded');
        }
        
        $encrypted_data = [];
        $chunks = $this->splitData($data);
        for($i=0; $i < count($chunks); $i++) {
            //$encryted_data[$i] = '';
            $res = openssl_private_encrypt($chunks[$i], $encrypted_data[$i], $this->private_RSA_key);
            if( false == $res) {
                throw new \Exception('Error encrypting data');
            }
        }
        return $encrypted_data;
    }
    
    
    /**
     * Tronçonnage
     * 
     * @param string $data
     * @return array
     */
    protected function splitData(string $data)
    {
        return mb_str_split($data, Crypto::RSA_CHUNK_SIZE, $this->charset); 
    }


    /**
     * Déchiffrement de données avec la clé RSA privée
     *
     * @param array|string $data
     * @throws \Exception
     *
     * @return string
     */
    public function decryptWithPrivateRSAKey($data)
    {
        if( is_null($this->private_RSA_key) ) {
            throw new \Exception ('Private key not loaded');
        }
        if(is_string($data)) {
            $data = [$data];
        }
        if( !is_array($data) ) {
            throw new \Exception('data must be of type string or array');
        }
        $decrypted_data = '';
        $decrypted_chunks = [];
        for($i=0; $i < count($data); $i++) {
            $decrypted_chunks[$i] = '';
            $res = openssl_private_decrypt($data[$i], $decrypted_chunks[$i], $this->private_RSA_key);
            if( false == $res) {
                throw new \Exception('Error decrypting data');
            }
        }
        $decrypted_data = implode('', $decrypted_chunks);
        return $decrypted_data;
    }
    
    
    /**
     * Chiffrement de données avec la clé RSA publique
     * Les données sont traitées par tronçons
     *
     * @param array|string $data
     * @throws \Exception
     *
     * @return array
     */
    public function encryptWithPublicRSAKey($data)
    {
        if( is_null($this->public_RSA_key) ) {
            throw new \Exception ('Public key not loaded');
        }
        
        $encrypted_data = [];
        $chunks = $this->splitData($data);
        for($i=0; $i < count($chunks); $i++) {
            $res = openssl_public_encrypt($chunks[$i], $encrypted_data[$i], $this->public_RSA_key);
            if( false == $res) {
                throw new \Exception('Error encrypting data');
            }
        }
        return $encrypted_data;
    }
    
    
    /**
     * Déchiffrement de données avec la clé RSA publique
     *
     * @param array|string $data
     * @throws \Exception
     *
     * @return string
     */
    public function decryptWithPublicRSAKey($data)
    {
        if( is_null($this->public_RSA_key) ) {
            throw new \Exception ('Public key not loaded');
        }
        if(is_string($data)) {
            $data = [$data];
        }
        if( !is_array($data) ) {
            throw new \Exception('data must be of type string or array');
        }
        $decrypted_data = '';
        $decrypted_chunks = [];
        for($i=0; $i < count($data); $i++) {
            $decrypted_chunks[$i] = '';
            $res = openssl_public_decrypt($data[$i], $decrypted_chunks[$i], $this->public_RSA_key);
            if( false == $res) {
                throw new \Exception('Error decrypting data');
            }
        }
        $decrypted_data = implode('', $decrypted_chunks);
        return $decrypted_data;
    }

    /**
     * Déchiffrement chaîne de données hexa-décimale avec prefixe {crypto}
     *
     * @param string $hex_encrypted_string
     * @throws \Exception
     *
     * @return string
     */
    public function decryptFromHexa(string $hex_encrypted_string)
    {
        $l = strlen(Crypto::INDICATOR);
        if(Crypto::INDICATOR !== substr($hex_encrypted_string, 0, $l)) {
            return $hex_encrypted_string;
        }
        $decrypted_data = '';
        try {
            $data_wo_indicator = str_replace(Crypto::INDICATOR, '', $hex_encrypted_string);
            $hex_encrypted_chunks = explode(Crypto::CHUNK_SEPARATOR, $data_wo_indicator);
            $bin_encrypted_chunks = [];
            for($i=0 ; $i<count($hex_encrypted_chunks); $i++) {
                $bin_encrypted_chunks[$i] = @hex2bin($hex_encrypted_chunks[$i]);
                if(false === $bin_encrypted_chunks[$i]) {
                    throw new \Exception('Invalid data');
				}
            }
            $decrypted_data = $this->decryptWithPublicRSAKey($bin_encrypted_chunks);
        } catch(\Exception $e) {
            throw new \Exception('Invalid data');
        }
        return $decrypted_data;
    }


    /**
     * Chiffrement chaîne de données en hexa avec prefixe {crypto}
     *
     * @param string $data
     * @throws \Exception
     *
     * @return string
     */
    public function encryptToHexa(string $data)
    {
        try {
            $bin_encrypted_chunks = $this->encryptWithPrivateRSAKey($data);
        } catch (Exception $e) {
            throw $e;
        }
        for($i = 0; $i < count($bin_encrypted_chunks); $i++) {
            $hex_encrypted_chunks[$i] = bin2hex($bin_encrypted_chunks[$i]);
        }
        $hex_encrypted_data = Crypto::INDICATOR.implode(Crypto::CHUNK_SEPARATOR, $hex_encrypted_chunks);
        return $hex_encrypted_data;
    }
}
