<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesDSI.class.php,v 1.12.2.1.2.1 2025/05/26 12:13:32 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\Product;

global $class_path;
require_once "{$class_path}/external_services.class.php";
require_once "{$class_path}/bannette.class.php";

class pmbesDSI extends external_services_api_class {

	/**
	 * Liste des bannettes automatiques
	 *
	 * @param string $filtre_search
	 * @param integer $id_classement
	 * @return array
	 */
    public function listBannettesAuto($filtre_search="", $id_classement=0) {
        global $dsi_active;

        if ((SESSrights & DSI_AUTH) && $dsi_active == 1) {
            $id_classement = intval($id_classement);

			$result = array();

            //auto = 1 : bannettes automatiques sans contrôle de date
            $auto=1;

            $filtre_search = str_replace("*", "%", $filtre_search) ;

            if ($filtre_search) {
                $clause = "WHERE nom_bannette like '".addslashes($filtre_search)."%' and bannette_auto='$auto' " ;
            } else {
                $clause = "WHERE bannette_auto='$auto' " ;
            }
            //			if ($id_classement!=0) $clause.= " and num_classement=0 ";
            if ($id_classement>0) {
                $clause.= " and num_classement='$id_classement' " ;
            }

            $requete = "SELECT COUNT(1) FROM bannettes $clause ";
            $res = pmb_mysql_query($requete);
            $nbr_lignes = pmb_mysql_result($res, 0, 0);
            if ($nbr_lignes) {
                $requete = "SELECT id_bannette, nom_bannette, date_last_remplissage, date_last_envoi, proprio_bannette, bannette_auto, nb_notices_diff FROM bannettes $clause ORDER BY nom_bannette, id_bannette ";
                $res = pmb_mysql_query($requete);

                while ($row = pmb_mysql_fetch_assoc($res)) {
					$result[] = array(
                        "id_bannette" => $row["id_bannette"],
                        "nom_bannette" => encoding_normalize::utf8_normalize($row["nom_bannette"]),
                        "date_last_remplissage" => $row["date_last_remplissage"],
                        "date_last_envoi" => $row["date_last_envoi"],
                        "proprio_bannette" => $row["proprio_bannette"],
                        "bannette_auto" => $row["bannette_auto"],
                        "nb_notices_diff" => $row["nb_notices_diff"],
					);
                }
            }
            return $result;
        } else {
			return array();
        }
    }

	/**
	 * Vidage/Remplissage/Diffusion de la liste des bannettes automatiques
	 *
	 * @param array $lst_bannettes
	 * @return string
	 */
    public function diffuseBannettesFullAuto($lst_bannettes) {
        global $msg, $dsi_auto, $PMBusername, $pmb_bdd_version, $database, $dsi_active;

        if ((SESSrights & DSI_AUTH) && $dsi_active == 1) {
            if (!$dsi_auto) {
                throw new Exception("DSI Auto pas activée sur base $database (user=$PMBusername) Version noyau: $pmb_bdd_version ");
            }
            if (!$lst_bannettes) {
                throw new Exception("Missing parameter: lst_bannettes");
            }

            if (!$lst_bannettes) {
                $lst_bannettes = [] ;
            }
            $action_diff_aff="";
            $nb_bannettes = count($lst_bannettes);
            for ($iba = 0; $iba < $nb_bannettes; $iba++) {
                $lst_bannettes[$iba] = intval($lst_bannettes[$iba]);
                if ($lst_bannettes[$iba]) {
                    $bannette = new bannette($lst_bannettes[$iba]) ;
                    $action_diff_aff .= $msg['dsi_dif_vidage'].": ".$bannette->nom_bannette."<br />" ;
                    if (!$bannette->limite_type) {
                        $action_diff_aff .= $bannette->vider();
                    }
                    $action_diff_aff .= $msg['dsi_dif_remplissage'].": ".$bannette->nom_bannette ;
                    $action_diff_aff .= $bannette->remplir();
                    $action_diff_aff .= $bannette->purger();
                    $action_diff_aff .= "<strong>".$msg['dsi_dif_diffusion'].": ".$bannette->nom_bannette."</strong><br />" ;
                    $action_diff_aff .= $bannette->diffuser();
                }
            }
            return $action_diff_aff;
        } else {
            return sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
        }
    }

	/**
	 * Vidage/Remplissage/Diffusion d'une bannette automatique
	 *
	 * @param integer $id_bannette
	 * @return string
	 */
    public function diffuseBannetteFullAuto($id_bannette) {
        global $msg, $dsi_auto, $PMBusername, $pmb_bdd_version, $database, $dsi_active;

        $id_bannette = intval($id_bannette);
        if ((SESSrights & DSI_AUTH) && $dsi_active == 1) {
            $action_diff_aff="";
            if (!$dsi_auto) {
                $action_diff_aff .="DSI Auto pas activée sur base $database (user=$PMBusername) Version noyau: $pmb_bdd_version ";
                //			throw new Exception("DSI Auto pas activée sur base $database (user=$PMBusername) Version noyau: $pmb_bdd_version ");
                return $action_diff_aff;
            }
            if (!$id_bannette) {
                $action_diff_aff .="Missing parameter: id_bannette";
                //			throw new Exception("Missing parameter: id_bannette");
                return $action_diff_aff;
            }

            $bannette = new bannette($id_bannette) ;

            $action_diff_aff .= $msg['dsi_dif_vidage'].": ".$bannette->nom_bannette."<br />" ;
            if (!$bannette->limite_type) {
                $action_diff_aff .= $bannette->vider();
            }
            $action_diff_aff .= $msg['dsi_dif_remplissage'].": ".$bannette->nom_bannette ;
            $action_diff_aff .= $bannette->remplir();
            $action_diff_aff .= $bannette->purger();
            $action_diff_aff .= "<strong>".$msg['dsi_dif_diffusion'].": ".$bannette->nom_bannette."</strong><br />" ;
            $action_diff_aff .= $bannette->diffuser();

            return $action_diff_aff;
        } else {
            return sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
        }
    }

	/**
	 * Vidage d'une bannette automatique
	 *
	 * @param integer $id_bannette
	 * @return string
	 */
    public function flushBannette($id_bannette) {
        global $msg, $PMBusername, $dsi_active;

        $id_bannette = intval($id_bannette);
        if ((SESSrights & DSI_AUTH) && $dsi_active == 1) {
            if (!$id_bannette) {
                throw new Exception("Missing parameter: id_bannette");
            }

            $bannette = new bannette($id_bannette) ;
            $action_diff_aff = $msg['dsi_dif_vidage'].": ".$bannette->nom_bannette."<br />" ;
            $action_diff_aff .= $bannette->vider();

            return $action_diff_aff;
        } else {
            return sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
        }
    }

	/**
	 * Remplissage d'une bannette automatique
	 *
	 * @param integer $id_bannette
	 * @return string
	 */
    public function fillBannette($id_bannette) {
        global $msg, $PMBusername, $dsi_active;

        $id_bannette = intval($id_bannette);
        if ((SESSrights & DSI_AUTH) && $dsi_active == 1) {
            if (!$id_bannette) {
                throw new Exception("Missing parameter: id_bannette");
            }

            $bannette = new bannette($id_bannette) ;
            $action_diff_aff = $msg['dsi_dif_remplissage'].": ".$bannette->nom_bannette ;
            $action_diff_aff .= $bannette->remplir();

            return $action_diff_aff;
        } else {
            return sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
        }
    }

	/**
	 * Diffusion d'une bannette automatique
	 *
	 * @param integer $id_bannette Identifiant de la bannette
	 * @return string
	 */
    public function diffuseBannette($id_bannette) {
        global $msg, $PMBusername, $dsi_active;

        $id_bannette = intval($id_bannette);
        if ((SESSrights & DSI_AUTH) && $dsi_active == 1) {
            if (!$id_bannette) {
                throw new Exception("Missing parameter: id_bannette");
            }

            $bannette = new bannette($id_bannette) ;
            $result = sprintf("<strong>%s: %s</strong><br />", $msg['dsi_dif_diffusion'], $bannette->nom_bannette);
            $result .= $bannette->diffuser();

            return $result;
        } else {
            return sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
        }
    }

	/**
	 * Export d'une bannette automatique
	 *
	 * @param string $id_bannette
	 * @return string
	 */
    public function exportBannette($id_bannette) {
        global $msg, $PMBusername;
        global $ourPDF;

        $id_bannette = intval($id_bannette);
        if (SESSrights & DSI_AUTH) {
            if (!$id_bannette) {
                throw new Exception("Missing parameter: id_bannette");
            }

            $bannette = new bannette($id_bannette) ;
            $resultat_html = $bannette->get_display_export();
            $ourPDF = new PDF_HTML();
            $ourPDF->AddPage();
            $ourPDF->SetFont('Arial');
            $ourPDF->WriteHTML($resultat_html);

            return $ourPDF;
        } else {
            return sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
        }
    }

    /**
	 * Envoi d'une diffusion automatique
	 *
	 * @param integer $id_diffusion Identifiant de la diffusion
	 * @return string
	 */
    public function sentDiffusion($id_diffusion) {
        global $msg, $PMBusername, $dsi_active, $dsi_send_automatically;

        $id_diffusion = intval($id_diffusion);
        if ((SESSrights & DSI_AUTH) && $dsi_active == 2) {
            if (!$id_diffusion) {
                throw new \InvalidArgumentException("Missing parameter: id_diffusion");
            }

            try {
                $diffusion = new Diffusion($id_diffusion);

                $dsi_send_automatically = 1;

                $result = sprintf("%s: %s<br />", $msg['dsi_send_diffusion'], $diffusion->name);
                $result .= $diffusion->trigger();
            } catch (\Exception $e) {
                $result = $e->getMessage();
            }

            return $result;
        } else {
            return sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
        }
    }

    /**
	 * Envoi d'une diffusion automatique
	 *
	 * @param integer $id_diffusion Identifiant de la diffusion
	 * @return string
	 */
    public function sentProduct($id_product) {
        global $msg, $PMBusername, $dsi_active, $dsi_send_automatically;

        $id_product = intval($id_product);
        if ((SESSrights & DSI_AUTH) && $dsi_active == 2) {
            if (!$id_product) {
                throw new \InvalidArgumentException("Missing parameter: id_product");
            }

            try {
                $product = new Product($id_product);


                $result = sprintf("%s: %s<br />", $msg['dsi_send_diffusion'], $product->name);
                $result .= $product->trigger();
            } catch (\Exception $e) {
                $result = $e->getMessage();
            }

            return $result;
        } else {
            return sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
        }
    }

    /**
	 * Liste des diffusion automatiques
	 *
	 * @param string $filtre_search
	 * @param integer $id_classement
	 * @return array
	 */
    public function listDiffusionAuto($filtre_search = "") {
        global $dsi_active;

        if ((SESSrights & DSI_AUTH) && $dsi_active == 2) {

            $listDiffusionAuto = [];

            $search = "";
            $filtre_search = str_replace("*", "%", $filtre_search) ;
            if ($filtre_search) {
                $search = "AND name LIKE '".addslashes($filtre_search)."%'" ;
            }


            $query = "SELECT * FROM dsi_diffusion WHERE automatic=1 {$search} ORDER BY name, id_diffusion";
            $result = pmb_mysql_query($query);

            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $listDiffusionAuto[] = [
                        "id_diffusion" => $row["id_diffusion"],
                        "name" => encoding_normalize::utf8_normalize($row["name"])
                    ];
                }
                pmb_mysql_free_result($result);
            }

            return $listDiffusionAuto;
        } else {
            return [];
        }
    }

    /**
	 * Envoi d'une liste de diffusion automatique
	 *
	 * @param array $list_diffusion
	 * @return string
	 */
    public function sentDiffusionAuto($list_diffusion) {
        global $msg, $PMBusername, $dsi_active, $dsi_send_automatically;

        if ((SESSrights & DSI_AUTH) && $dsi_active == 2) {
            if (empty($list_diffusion) || !is_array($list_diffusion)) {
                throw new \InvalidArgumentException("Missing or invalide parameter: list_diffusion");
            }

            $list_diffusion = array_map("intval", $list_diffusion);
            $count = count($list_diffusion);

            $result = "";
            for ($i = 0; $i < $count; $i++) {
                try {
                    $diffusion = new Diffusion($list_diffusion[$i]) ;
                    if (!$diffusion->automatic) {
                        continue;
                    }

                    $dsi_send_automatically = 1;

                    $result = sprintf("%s: %s<br />", $msg['dsi_send_diffusion'], $diffusion->name);
                    $result .= $diffusion->trigger();
                } catch (\Exception $e) {
                    $result = sprintf("%s: %s<br />", $msg['dsi_send_diffusion'], $diffusion->name);
                    $result .= $e->getMessage();
                }
            }
            return $result;
        } else {
            return sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
        }
    }
}
