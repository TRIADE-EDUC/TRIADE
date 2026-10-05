<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: in.inc.php,v 1.35.4.1 2025/04/16 12:16:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $base_path, $class_path, $msg;
global $act, $source_id, $id, $go;
global $timeout, $retry, $ttl, $repository, $rep_upload, $upload_doc_num;
global $name, $comment, $opac_allowed, $enrichment, $opac_affiliate_search, $opac_selected, $gestion_selected, $type_enrichment_allowed, $ico_notice;

if (empty($act)) {
    $act = '';
}
if (empty($source_id)) {
    $source_id = 0;
}
$source_id = intval($source_id);

require_once ($class_path . "/connecteurs.class.php");

// Affichage de la liste des connecteurs disponibles
function show_connectors()
{
    print list_configuration_connecteurs_in_ui::get_instance()->get_display_list();
}

switch ($act) {
    case "modif":
        $contrs = new connecteurs();
        print $contrs->show_connector_form($id);
        break;

    case "update":
        if ($id) {
            $contrs = new connecteurs();
            require_once ($base_path . "/admin/connecteurs/in/" . $contrs->catalog[$id]["PATH"] . "/" . $contrs->catalog[$id]["NAME"] . ".class.php");

            $classname = $contrs->catalog[$id]["NAME"];
            $conn = new $classname("{$base_path}/admin/connecteurs/in/{$contrs->catalog[$id]["PATH"]}");

            if ($conn) {
                $conn->timeout = $timeout;
                $conn->retry = $retry;
                $conn->ttl = $ttl;
                $conn->repository = $repository;
                $conn->rep_upload = $rep_upload;
                $conn->upload_doc_num = $upload_doc_num;
                $conn->save_property_form();
            }
        }
        show_connectors();
        break;

    case "cancel_sync":
        $sql = "DELETE FROM source_sync WHERE source_id = $source_id AND cancel > 0";
        pmb_mysql_query($sql);
        show_connectors();
        break;

    case "abort_sync":
        $sql = "DELETE FROM source_sync WHERE source_id = $source_id ";
        pmb_mysql_query($sql);
        show_connectors();
        break;

    case "add_source":
        $contrs = new connecteurs();
        print $contrs->show_source_form($id, $source_id);
        break;

    case "update_source":
        if ($id) {
            $contrs = new connecteurs();
            require_once ($base_path . "/admin/connecteurs/in/" . $contrs->catalog[$id]["PATH"] . "/" . $contrs->catalog[$id]["NAME"] . ".class.php");

            $classname = $contrs->catalog[$id]["NAME"];
            $conn = new $classname("{$base_path}/admin/connecteurs/in/{$contrs->catalog[$id]["PATH"]}");

            if ($conn) {
                $conn->sources[$source_id]["TIMEOUT"] = $timeout;
                $conn->sources[$source_id]["RETRY"] = $retry;
                $conn->sources[$source_id]["TTL"] = $ttl;
                $conn->sources[$source_id]["REPOSITORY"] = $repository;
                $conn->sources[$source_id]["NAME"] = stripslashes($name);
                $conn->sources[$source_id]["COMMENT"] = stripslashes($comment);
                $conn->sources[$source_id]["OPAC_ALLOWED"] = (isset($opac_allowed) ? $opac_allowed * 1 : 0);
                $conn->sources[$source_id]["REP_UPLOAD"] = stripslashes($rep_upload);
                $conn->sources[$source_id]["ENRICHMENT"] = (isset($enrichment) ? $enrichment * 1 : 0);
                $conn->sources[$source_id]["UPLOAD_DOC_NUM"] = stripslashes($upload_doc_num);
                $conn->sources[$source_id]["OPAC_AFFILIATE_SEARCH"] = (isset($opac_affiliate_search) ? $opac_affiliate_search * 1 : 0);
                $conn->sources[$source_id]["OPAC_SELECTED"] = (isset($opac_selected) ? $opac_selected * 1 : 0);
                $conn->sources[$source_id]["GESTION_SELECTED"] = (isset($gestion_selected) ? $gestion_selected * 1 : 0);
                $conn->sources[$source_id]["TYPE_ENRICHMENT_ALLOWED"] = (isset($type_enrichment_allowed) ? $type_enrichment_allowed : array());
                $conn->sources[$source_id]["UPLOAD_DOC_NUM"] = (isset($upload_doc_num) ? $upload_doc_num * 1 : 0);
                $conn->sources[$source_id]["ICO_NOTICE"] = stripslashes($ico_notice);
                // Vérification du nom
                $requete = "select count(*) from connectors_sources where name='" . $name . "' and source_id!=$source_id and id_connector='" . addslashes($contrs->catalog[$id]["NAME"]) . "'";
                $resultat = pmb_mysql_query($requete);
                if (pmb_mysql_result($resultat, 0, 0) == 0) {
                    $conn->source_save_property_form($source_id);
                    show_connectors();
                } else {
                    error_form_message($msg["connecteurs_name_exists"]);
                }
            }
        }
        break;

    case "delete_source":
        if ($id) {
            $contrs = new connecteurs();
            require_once ($base_path . "/admin/connecteurs/in/" . $contrs->catalog[$id]["PATH"] . "/" . $contrs->catalog[$id]["NAME"] . ".class.php");

            $classname = $contrs->catalog[$id]["NAME"];
            $conn = new $classname("{$base_path}/admin/connecteurs/in/{$contrs->catalog[$id]["PATH"]}");

            if (($source_id) && ($conn)) {
                if (! $conn->del_source($source_id)) {
                    print "<div class='erreur'>$conn->error_message</div>";
                }
            }
            show_connectors();
        }
        break;

    case "sync":
        if ($id) {

            $contrs = new connecteurs();
            require_once ($base_path . "/admin/connecteurs/in/" . $contrs->catalog[$id]["PATH"] . "/" . $contrs->catalog[$id]["NAME"] . ".class.php");

            $classname = $contrs->catalog[$id]["NAME"];
            $conn = new $classname("{$base_path}/admin/connecteurs/in/{$contrs->catalog[$id]["PATH"]}");

            // Si on doit afficher un formulaire de synchronisation
            $syncr_form = $conn->getSynchroForm($source_id);
            if (empty($go) && $syncr_form) {
                print '<form name="sync_form" action="' . "admin.php?categ=connecteurs&sub=in&act=sync&source_id=" . $source_id . "&go=1&id=$id" . '" method="POST"  enctype="multipart/form-data">';
                print $syncr_form;
                print "<input type='submit' class='bouton_small' value='" . $msg["connecteurs_sync"] . "'/>";
                print '</form>';
            } else {
                if (($source_id) && ($conn)) {
                    require_once ($base_path . "/admin/connecteurs/in/sync.inc.php");
                }
            }
        } else
            show_connectors();
        break;

    case "sync_custom_page":
        if ($id) {

            $contrs = new connecteurs();
            require_once ($base_path . "/admin/connecteurs/in/" . $contrs->catalog[$id]["PATH"] . "/" . $contrs->catalog[$id]["NAME"] . ".class.php");

            $classname = $contrs->catalog[$id]["NAME"];
            $conn = new $classname("{$base_path}/admin/connecteurs/in/{$contrs->catalog[$id]["PATH"]}");

            print $conn->sync_custom_page($source_id);
        }
        break;

    case "empty":
        if ($id) {
            $contrs = new connecteurs();
            require_once ($base_path . "/admin/connecteurs/in/" . $contrs->catalog[$id]["PATH"] . "/" . $contrs->catalog[$id]["NAME"] . ".class.php");

            $classname = $contrs->catalog[$id]["NAME"];
            $conn = new $classname("{$base_path}/admin/connecteurs/in/{$contrs->catalog[$id]["PATH"]}");

            if (($source_id) && ($conn)) {
                if ($conn->del_notices($source_id)) {
                    $sql = "UPDATE connectors_sources SET last_sync_date = '0000-00-00 00:00:00' WHERE source_id = $source_id ";
                    pmb_mysql_query($sql);
                } else {
                    print "<div class='erreur'>$conn->error_message</div>";
                }
            }
		}
		show_connectors();
		break;

    default:
        show_connectors();
        break;
}

