<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Naan.php,v 1.2 2022/03/01 08:26:35 tsamson Exp $
namespace Pmb\Ark;

class Naan
{

    /**
     * liste des naan
     * @var array
     */
    private $naan;

    /**
     * 
     */
    public function __construct()
    {
        $this->getData();
    }
    
    private function getData()
    {
        $this->naan = ["99999"];
        $query = "SELECT valeur_param FROM parametres WHERE type_param = 'pmb' AND sstype_param = 'ark_naan'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);
            if ($row["valeur_param"]) {
                $this->naan = \encoding_normalize::json_decode(stripslashes($row["valeur_param"]), true);
            }
        }
        
    }
    /**
     *
     * @return number
     */
    public function save()
    {
        $query = "SELECT id_param FROM parametres WHERE type_param = 'pmb' AND sstype_param = 'ark_naan'";
        $result = pmb_mysql_query($query);
        $naanVal = addslashes(\encoding_normalize::json_encode($this->naan));
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_assoc($result);
            
            $query = "UPDATE parametres SET valeur_param = '$naanVal' WHERE id_param = ".$row["id_param"];
        } else {
            $query = "INSERT INTO parametres (type_param, sstype_param, valeur_param, section_param, gestion)
                    VALUES ('pmb', 'ark_naan', '$naanVal', 'ark', 1)";
        }
        if (pmb_mysql_query($query)) {
            return true;
        }
        return false;
    }
    
    public function getNaan()
    {
        if (isset($this->naan)) {
            return $this->naan;
        }
        $this->naan = ["99999"];
        return $this->naan;
    }
    
    public function setNaan($naan) {
        $this->naan = $naan;
    }
}