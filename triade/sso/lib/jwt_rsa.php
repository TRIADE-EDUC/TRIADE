<?php
/*
 * JWT RS256 — signature asymétrique RSA pour OIDC id_token
 * Utilisé par le module OIDC de Triade (authorize.php / token.php)
 */

class JwtRsa {

    // Signe un payload avec la clé privée RSA → JWT RS256
    public static function encode(array $payload, string $privateKeyPem, string $kid = ''): string {
        $header = self::b64u(json_encode(array_filter([
            'typ' => 'JWT',
            'alg' => 'RS256',
            'kid' => $kid ?: null,
        ])));
        $body = self::b64u(json_encode($payload));
        $data = "$header.$body";

        $key = openssl_pkey_get_private($privateKeyPem);
        if (!$key) {
            throw new RuntimeException('Clé privée RSA invalide');
        }
        openssl_sign($data, $sig, $key, OPENSSL_ALGO_SHA256);

        return "$data." . self::b64u($sig);
    }

    // Vérifie et décode un JWT RS256 avec la clé publique
    public static function decode(string $token, string $publicKeyPem): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        [$header, $body, $sig] = $parts;

        $key   = openssl_pkey_get_public($publicKeyPem);
        $valid = openssl_verify("$header.$body", self::b64u_decode($sig), $key, OPENSSL_ALGO_SHA256);
        if ($valid !== 1) return null;

        $data = json_decode(self::b64u_decode($body), true);
        if (!$data) return null;

        if (isset($data['exp']) && $data['exp'] < time()) return null;

        return $data;
    }

    // Construit le JWKS (JSON Web Key Set) à partir de la clé publique
    public static function jwks(string $publicKeyPem, string $kid): array {
        $key     = openssl_pkey_get_public($publicKeyPem);
        $details = openssl_pkey_get_details($key);
        $rsa     = $details['rsa'];

        return [
            'keys' => [[
                'kty' => 'RSA',
                'alg' => 'RS256',
                'use' => 'sig',
                'kid' => $kid,
                'n'   => self::b64u($rsa['n']),
                'e'   => self::b64u($rsa['e']),
            ]]
        ];
    }

    // base64url encode
    public static function b64u(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // base64url decode
    public static function b64u_decode(string $data): string {
        $pad = (4 - strlen($data) % 4) % 4;
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', $pad));
    }
}
