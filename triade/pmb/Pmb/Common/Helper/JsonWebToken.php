<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: JsonWebToken.php,v 1.1.2.1 2025/02/07 13:49:03 qvarin Exp $

namespace Pmb\Common\Helper;

use password;

class JsonWebToken
{
    /**
     * Returns the salt
     *
     * @return string
     */
    private static function salt(): string
    {
        global $opac_empr_password_salt;
        if ('' == $opac_empr_password_salt) {
            password::gen_salt_base();
        }

        return $opac_empr_password_salt;
    }

    /**
     * Generates a JSON Web Token
     *
     * @param array $data
     * @return string
     */
    public static function encode(array $data): string
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $header = base64_encode($header);

        $payload = json_encode($data);
        $payload = base64_encode($payload);

        $signature = hash_hmac("sha256", $header . "." . $payload, static::salt(), true);
        $signature = base64_encode($signature);

        return $header . "." . $payload . "." . $signature;
    }

    /**
     * Decodes a JSON Web Token
     *
     * @param string $token
     * @return false|array
     */
    public static function decode(string $token)
    {
        $tokens = explode(".", $token, 3);
        if (count($tokens) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $tokens;
        if (empty($header) || empty($payload) || empty($signature)) {
            return false;
        }

        $rebuildSignature = hash_hmac("sha256", $header . "." . $payload, static::salt(), true);
        $signature = base64_decode($signature);

        if (!hash_equals($rebuildSignature, $signature)) {
            return false;
        }

        $data = json_decode(base64_decode($payload), true);
        return is_array($data) ? $data : false;
    }
}