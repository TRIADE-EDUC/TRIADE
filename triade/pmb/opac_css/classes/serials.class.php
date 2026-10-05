<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials.class.php,v 1.6 2023/10/24 09:57:08 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . "/notice.class.php");

/*
 * ------------------------------------------------------------------------------------
 * classe serial : classe de gestion des notices chapeau
 * ---------------------------------------------------------------------------------------
 */
class serial extends notice
{

    // classe de la notice chapeau des périodiques
    public $serial_id = 0;

    // id de ce périodique

    // constructeur
    public function __construct($id = 0)
    {
        $this->id = intval($id); // Propriété dans la classe notice
        $this->serial_id = intval($id);
    }
}

// fin définition classe

/*
 * ------------------------------------------------------------------------------------
 * classe bulletinage : classe de gestion des bulletinages
 * ---------------------------------------------------------------------------------------
 */
class bulletinage extends notice
{

    public $bulletin_id = 0;

    // id de ce bulletinage
    public $bulletin_notice = 0;

    // id notice parent = id du périodique relié
    public $serial_id = 0;

    // id notice parent = id du périodique relié

    // constructeur
    public function __construct($bulletin_id, $serial_id = 0, $link_explnum = '', $localisation = 0, $make_display = true)
    {
        $this->bulletin_id = intval($bulletin_id);

        if ($serial_id) {
            $this->bulletin_notice = $serial_id;
            $this->serial_id = $serial_id;
        }
        return $this->bulletin_id;
    }

    /**
     *
     * @param int $notice_id
     * @return string
     */
    public static function get_permalink($notice_id)
    {
        global $opac_url_base;

        $issue_id = intval($notice_id);
        $record_id = static::get_issue_record_id_from_id($issue_id);
        if (! empty($record_id)) {
            return parent::get_permalink($record_id);
        }
        return $opac_url_base . "index.php?lvl=bulletin_display&id=" . $issue_id;
    }

    public static function get_notice_id_from_id($bulletin_id)
    {
        $bulletin_id = intval($bulletin_id);
        $query = "SELECT num_notice, bulletin_notice FROM bulletins WHERE bulletin_id = " . $bulletin_id;
        $result = pmb_mysql_query($query);
        $row = pmb_mysql_fetch_object($result);
        if ($row->num_notice) {
            return $row->num_notice; // Notice de bulletin
        } else {
            return $row->bulletin_notice; // Notice de périodique
        }
    }

    public static function get_issue_record_id_from_id($bulletin_id)
    {
        $bulletin_id = intval($bulletin_id);
        $query = "SELECT num_notice FROM bulletins WHERE bulletin_id = " . $bulletin_id;
        $result = pmb_mysql_query($query);
        return pmb_mysql_result($result, 0, 0) ?? 0;
    }
}

// fin définition classe

// mark dep

/*
 * ------------------------------------------------------------------------------------
 * classe analysis : classe de gestion des dépouillements
 * ---------------------------------------------------------------------------------------
 */
class analysis extends notice
{

    public $id_bulletinage = 0;

    // id du bulletinage contenant ce dépouillement

    // constructeur
    public function __construct($analysis_id, $bul_id = 0)
    {
        $this->id = intval($analysis_id);
        if ($bul_id) {
            $this->id_bulletinage = intval($bul_id);
        }

        return $this->id;
    }
} // fin définition classe