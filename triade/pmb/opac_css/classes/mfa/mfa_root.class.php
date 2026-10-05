<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mfa_root.class.php,v 1.1.4.1 2025/03/11 09:44:39 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class mfa_root
{

    /**
     * Genere un code secret
     *
     * @param int $length
     * @return string
     */
    public function generate_secret_code(int $length = 7): string
    {
        $secret_code = "";
        $digits = "0123456789";

        for ($i = 0; $i < $length; $i++) {
            $random_digit = $digits[rand(0, strlen($digits) - 1)];
            $secret_code .= $random_digit;
        }

        return $secret_code;
    }

    /**
     * Genere l'URI du type otpauth://
     * 
     * @param string $type
     * @param string $label
     * @param string $secret_code
     * @param array $options
     * @return string
     */
    protected function get_key_uri($type, $label, $secret_code, $options = array())
    {
        $label = trim($label);

        $otpauth = 'otpauth://' . $type . '/' . rawurlencode($label) . '?secret=' . rawurlencode($secret_code);

        // Defaults to SHA1
        if (array_key_exists('algorithm', $options)) {
            $otpauth .= '&algorithm=' . rawurlencode($options['algorithm']);
        }

        // Defaults to 6
        if (array_key_exists('digits', $options)) {
            $otpauth .= '&digits=' . intval($options['digits']);
        }

        // Defaults to 30
        if (array_key_exists('period', $options)) {
            $otpauth .= '&period=' . rawurlencode($options['period']);
        }

        if (array_key_exists('issuer', $options)) {
            $otpauth .= '&issuer=' . rawurlencode($options['issuer']);
        }

        return $otpauth;
    }

    /**
     * Retourne l'URL d'un QR code en base64
     * 
     * @param string $type
     * @param string $label
     * @param string $secret_code
     * @param array $options
     * @return string
     */
    public function get_qr_code_url($type, $label, $secret_code, $options = array())
    {
        // Créer l'URI
        $otpauth = $this->get_key_uri($type, $label, $secret_code, $options);

        // Créer l'image du QR code
        $qrCode = new TCPDF2DBarcode($otpauth, 'QRCODE,L');
        $qrCodeArray = $qrCode->getBarcodeArray();

        // Taille de l'image du QR code en pixels
        $size = 128;

        // Taille d'un block du QR code en pixels
        $sizePerModule = $size / $qrCodeArray['num_cols'];

        // convert to base64
        $base64 = base64_encode($qrCode->getBarcodePngData($sizePerModule, $sizePerModule));

        return 'data:image/png;base64,' . $base64;
    }
}