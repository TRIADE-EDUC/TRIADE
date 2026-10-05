<?php
/*
 * Génération des clés RSA pour le module OIDC Triade
 * À exécuter UNE SEULE FOIS en ligne de commande ou via navigateur (en dev uniquement).
 * Les fichiers générés : private.pem, public.pem, kid.txt
 *
 * Usage CLI : php generate.php
 * Usage web : http://localhost/sso/keys/generate.php (DÉSACTIVER en prod !)
 */

$dir = __DIR__;

if (file_exists("$dir/private.pem") && !in_array('--force', $argv ?? [])) {
    die("Clés déjà générées. Utilisez --force pour régénérer.\n");
}

$config = [
    'digest_alg'       => 'sha256',
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
];

$res = openssl_pkey_new($config);
if (!$res) {
    die("Erreur OpenSSL : " . openssl_error_string() . "\n");
}

// Clé privée
openssl_pkey_export($res, $privateKey);
file_put_contents("$dir/private.pem", $privateKey);

// Clé publique
$details   = openssl_pkey_get_details($res);
$publicKey = $details['key'];
file_put_contents("$dir/public.pem", $publicKey);

// kid = empreinte SHA-1 de la clé publique (format OpenSSH standard)
$kid = substr(hash('sha256', $publicKey), 0, 16);
file_put_contents("$dir/kid.txt", $kid);

echo "Clés RSA générées avec succès.\n";
echo "kid : $kid\n";
echo "\nAjoutez dans sso/config.php :\n";
echo "define('OIDC_KEY_ID', '$kid');\n";
