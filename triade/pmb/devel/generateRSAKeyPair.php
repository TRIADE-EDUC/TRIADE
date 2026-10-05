<?php

require_once __DIR__.'/../vendor/autoload.php';

$crypto = new Pmb\Common\Library\Crypto\Crypto();

try {

    $RSA_key = $crypto->generateRSAKeyPair();
    echo 'public key :'.PHP_EOL.$RSA_key['public'].PHP_EOL;
    echo 'private key :'.PHP_EOL.$RSA_key['private'].PHP_EOL;
    
    //$current_time = date('YmdHis');
    $current_time = '';
    $public_filename = "public_RSA_key".(($current_time) ? $current_time :'').".pem";
    $private_filename = "private_RSA_key".(($current_time) ? $current_time :'').".pem";
    
    file_put_contents(__DIR__."/$public_filename", $RSA_key['public']);
    file_put_contents(__DIR__."/$private_filename", $RSA_key['private']);
    
    echo "Keys saved in files : '".$public_filename."' and '".$private_filename."' ".PHP_EOL;
    
    echo PHP_EOL;
    echo 'Loading public key :'.PHP_EOL;
    $crypto->loadPublicRSAKey('file://'.__DIR__."/$public_filename");
    echo '>> OK'.PHP_EOL;
    
    echo PHP_EOL;
    echo 'Loading private key :'.PHP_EOL;
    $crypto->loadPrivateRSAKey('file://'.__DIR__."/$private_filename");
    echo '>> OK'.PHP_EOL;
    
    $data = 'A little encryption/decryption test';
    
    echo PHP_EOL;
    echo 'Data to encrypt = '.$data.PHP_EOL;
    
    echo PHP_EOL;
    echo 'Checking private key encryption :'.PHP_EOL;
    $encrypted = $crypto->encryptWithPrivateRSAKey($data);
    echo '>> crypted data = '.bin2hex($encrypted).PHP_EOL;
    
    echo PHP_EOL;
    echo 'Checking public key decryption :'.PHP_EOL;
    $decrypted = $crypto->decryptWithPublicRSAKey($encrypted);
    echo '>> decrypted data = '.$decrypted.PHP_EOL;
    
    echo PHP_EOL;
    echo 'Checking public key encryption :'.PHP_EOL;
    $encrypted = $crypto->encryptWithPublicRSAKey($data);
    echo '>> crypted data = '.bin2hex($encrypted).PHP_EOL;
    
    echo PHP_EOL;
    echo 'Checking private key decryption :'.PHP_EOL;
    $decrypted = $crypto->decryptWithPrivateRSAKey($encrypted);
    echo '>> decrypted data = '.$decrypted.PHP_EOL;
    
    echo PHP_EOL;
    echo 'Congratulations ...'.PHP_EOL;
    
    echo PHP_EOL;
    echo 'Fill in "pmb_public_rsa_key" parameter with public Key as "file://path/to/file.pem" or PEM string '; 
    echo 'ex : "file://'.__DIR__."/$public_filename";
    echo PHP_EOL;
    echo 'Fill in "pmb_private_rsa_key" parameter with private Key as "file://path/to/file.pem" or PEM string'; 
    echo 'ex : "file://'.__DIR__."/$private_filename";
    echo PHP_EOL;
    
    
} catch(\Exception $e) {
    echo $e->getMessage();
}