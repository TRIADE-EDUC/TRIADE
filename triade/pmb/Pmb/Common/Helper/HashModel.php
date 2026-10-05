<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HashModel.php,v 1.2 2021/03/22 09:13:34 qvarin Exp $
namespace Pmb\Common\Helper;

class HashModel
{

    /**
     *
     * @var string
     */
    private const PHRASE_SECRET = "";

    /**
     * Hash générer pour chaque client
     *
     * @var string
     */
    private $key_secret;

    public function __construct()
    {
        $query = "SELECT valeur_param FROM parametres WHERE type_param='pmb' AND sstype_param='hash_key_secret'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);
            $this->key_secret = $row['valeur_param'];
        } else {
            throw new \Exception("No secret key found");
        }
    }

    /**
     *
     * @param string $param
     * @return string
     */
    public function generateHash(string $param)
    {
        return sha1($param . self::PHRASE_SECRET . $this->key_secret);
    }

    /**
     *
     * @param string $hash
     * @param string $barcode
     * @return boolean
     */
    public function verifeHash(string $hash, string $barcode)
    {
        $newHash = $this->generateHash($barcode);
        if ($newHash === $hash) {
            return true;
        }
        return false;
    }
}