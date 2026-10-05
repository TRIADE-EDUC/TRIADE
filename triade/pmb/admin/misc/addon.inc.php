<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: addon.inc.php,v 1.8.2.32.2.10 2025/06/09 07:44:13 dbellamy Exp $

use Pmb\CMS\Orm\VersionOrm;
use Pmb\CMS\Models\PortalModel;
use Pmb\CMS\Models\PageModel;
use Pmb\CMS\Models\LayoutModel;
use Pmb\CMS\Models\ConditionModel;
use Pmb\CMS\Models\LayoutElementModel;
use Pmb\CMS\Models\LayoutContainerModel;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $pmb_bdd_subversion, $pmb_bdd_version, $pmb_version_brut, $pmb_version_patch;
global $pmb_subversion_database_as_it_shouldbe;
global $pmb_bdd_subversion_error;

function traite_addon_rqt($requete = "", $message = "")
{
    global $charset, $pmb_bdd_subversion;
    global $db_update_log_version, $db_update_step, $db_update_error;

    $retour = "Successful";
    if ($charset == "utf-8") {
        $requete = encoding_normalize::utf8_normalize($requete);
        $message = encoding_normalize::utf8_normalize($message);
    }
    pmb_mysql_query($requete);
    $erreur_no = pmb_mysql_errno();
    $erreur_msg = pmb_mysql_error();

    if ($erreur_no) {
        switch ($erreur_no) {
            case "1060":
                $retour = "Field already exists, no problem.";
                break;
            case "1061":
                $retour = "Key already exists, no problem.";
                break;
            case "1091":
                $retour = "Object already deleted, no problem.";
                break;
            default:
                if (! $db_update_error) {
                    $db_update_error = $db_update_step;
                    $query_error = "UPDATE parametres SET valeur_param='" . $db_update_error . "' WHERE type_param='pmb' AND sstype_param='bdd_subversion_error' AND valeur_param=0";
                    pmb_mysql_query($query_error);
                }
                $retour = "<font color=\"#FF0000\">Error may be fatal : <i>" . $erreur_msg . "<i></font>";
                break;
        }
    }
    $result = pmb_mysql_query("SHOW TABLES LIKE 'db_update_logs'");
    if (pmb_mysql_num_rows($result)) {
        $query_log = " INSERT INTO db_update_logs (db_update_log_type, db_update_log_version, db_update_log_query, db_update_log_message, db_update_log_error, db_update_log_result)
        VALUES ('addon', '" . $db_update_log_version . $db_update_step . "', '" . addslashes($requete) . "', '" . addslashes($message) . "', $erreur_no, '" . addslashes(strip_tags($retour)) . "')";
        pmb_mysql_query($query_log);
    }
    return "<tr><td><font size='1'><span data-alter='action'>" . $message ."</span></font></td><td><font size='1'><span data-alter='result'>" . $retour . "</span></font></td></tr>";
}
echo "<table>";

/**
 * ****************** AJOUTER ICI LES MODIFICATIONS ******************************
 */

// Formate et verifie que $pmb_bdd_subversion est <= à $pmb_subversion_database_as_it_shouldbe
$pmb_bdd_subversion = intval($pmb_bdd_subversion);
$pmb_subversion_database_as_it_shouldbe = intval($pmb_subversion_database_as_it_shouldbe);
if($pmb_bdd_subversion > $pmb_subversion_database_as_it_shouldbe ) {
    $pmb_bdd_subversion =  $pmb_subversion_database_as_it_shouldbe;
}
$pmb_bdd_subversion_error = intval($pmb_bdd_subversion_error);

$db_update_log_version = "PMB : " . $pmb_version_brut . "." . $pmb_version_patch . "; BDD : " . $pmb_bdd_version . "_";
$db_update_step = $pmb_bdd_subversion;

//Raz erreur $pmb_bdd_subversion_error
if($pmb_bdd_subversion <= $pmb_bdd_subversion_error) {
    $query_raz = "UPDATE parametres SET valeur_param=0 WHERE type_param='pmb' AND sstype_param='bdd_subversion_error'";
    pmb_mysql_query($query_raz);
}
while ($db_update_step < $pmb_subversion_database_as_it_shouldbe) {

    switch ($db_update_step) {

        case 0:
            // GN - Ajout d'un message qui indique si l'emprunteur possède l'exemplaire
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='already_loaned' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
                VALUES (0, 'selfservice', 'already_loaned', 'l\'emprunteur possède l\'exemplaire', '1', 'Ajout d\'un message qui indique si l\'emprunteur possède l\'exemplaire') ";
                echo traite_addon_rqt($rqt, "INSERT selfservice_already_loaned INTO parametres");
            }

            // GN - Ajout d'un message pour la gestion du statut de la réservations de l'exemplaire
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='expl_status' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
                VALUES (0, 'selfservice', 'expl_status', '', '1', 'Ajout d\'un message pour la gestion des statuts de l\'exemplaire') ";
                echo traite_addon_rqt($rqt, "INSERT selfservice_expl_status INTO parametres");
            }
            break;

        case 1:
            // DG - Ajout index sur num_object et type_object de la table vedette_link
            $add_index = true;
            $req = "SHOW INDEX FROM vedette_link WHERE Key_name='i_object' ";
            $res = pmb_mysql_query($req);
            if ($res && pmb_mysql_num_rows($res)) {
                $add_index = false;
            }
            if ($add_index) {
                $rqt = "ALTER TABLE vedette_link ADD INDEX i_object(num_object, type_object)";
                echo traite_addon_rqt($rqt, "alter table vedette_link add index i_object");
            }
            break;

        case 2:
            // JP - Ajout d'un paramètre pour la taille maximum d'un logo dans le contenu éditorial
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'cms' AND sstype_param='img_pics_max_size'")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES (0, 'cms', 'img_pics_max_size', '640', 'Taille maximale des logos du contenu éditorial, en largeur ou en hauteur', '', 0)";
                echo traite_addon_rqt($rqt, "INSERT cms_img_pics_max_size = 640 INTO parameters");
            }
            break;

        case 3:
            // QV - [Refonte Portail] Correction des types et sous-types des pages
            $query = "SELECT id FROM portal_version ORDER BY id DESC LIMIT 50";
            $res = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($res) > 0) {
                while ($row = pmb_mysql_fetch_assoc($res)) {
                    PortalModel::$instances = [];
                    PortalModel::$nbInstance = 0;

                    PageModel::$instances = [];
                    PageModel::$nbInstance = 0;

                    LayoutModel::$instances = [];
                    LayoutModel::$nbInstance = 0;

                    ConditionModel::$instances = [];
                    ConditionModel::$nbInstance = 0;

                    LayoutElementModel::$instances = [];
                    LayoutElementModel::$nbInstance = 0;

                    LayoutContainerModel::$instances = [];
                    LayoutContainerModel::$nbInstance = 0;

                    $portal = PortalModel::getPortal($row["id"]);
                    $pages = $portal->getPages();
                    if (empty($pages)) {
                        // Aucune page
                        continue;
                    }

                    $isModified = false;
                    array_walk($pages, function ($page) use (&$isModified) {

                        if ($page->type == '39' && intval($page->subType / 100) == 40) {
                            // Cas specifique pour les animations (correction du type de page)
                            $page->type = '40';
                            $isModified = true;
                        }

                        if ($page->type == '35' && $page->subType == '3401') {
                            // Cas specifique pour les segments (correction du sous-type de page)
                            $page->subType = '3501';
                            $isModified = true;
                        }

                        if ($page->type == '34' && $page->subType == '3301') {
                            // Cas specifique pour les univers (correction du sous-type de page)
                            $page->subType = '3401';
                            $isModified = true;
                        }

                        return $page;
                    });

                    if (! $isModified) {
                        // Aucune modification
                        continue;
                    }

                    $portal->pages = $pages;
                    $properties_serialised = \encoding_normalize::json_encode($portal->serialize());
                    if (! empty($properties_serialised)) {
                        $version = new VersionOrm($row["id"]);
                        $version->properties = gzcompress($properties_serialised);
                        $version->save();
                    }
                }
                echo traite_addon_rqt("SELECT 1", "[Refonte Portail] Correction des types et sous-types des pages");
            }
            break;

        case 4:

            // GN - Ajout d'une table pour stocker les paramètres des listes de lecture pour l'IA
            $rqt = "CREATE TABLE IF NOT EXISTS ai_shared_list (
                        id_ai_shared_list int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        settings_ai_shared_list mediumblob NOT NULL)";
            echo traite_addon_rqt($rqt, "CREATE TABLE ai_shared_list");

            // GN - JP - Ajout d'une colonne FLAG pour l'indexation des notices de la liste de lecture dans le module IA
            $rqt = "ALTER TABLE opac_liste_lecture_notices ADD opac_liste_lecture_flag_ia int(1) UNSIGNED NOT NULL DEFAULT 0";
            echo traite_addon_rqt($rqt, "ALTER TABLE opac_liste_lecture_notices ADD opac_liste_lecture_flag_ia");

            // GN - JP - Ajout d'un paramètre pour le nombre d'éléments à indexer par passe dans le module IA
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='ai' AND sstype_param='index_nb_elements'")) == 0) {
                $rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                            VALUES ('ai', 'index_nb_elements', '5', 'Nombre d\'éléments traités par passe d\'indexation', '', 0)";
                echo traite_addon_rqt($rqt, 'INSERT index_nb_elements INTO parametres artificial_intelligence');
            }

            // GN - JP - Ajout d'un paramètre pour la taille maximun d'un fichier upload dans le module IA
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='ai' AND sstype_param='upload_max_size'")) == 0) {
                $rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                            VALUES ('ai', 'upload_max_size', '100', 'Poids maximun d\'un fichier en Mo lors de l\'upload', '', 0)";
                echo traite_addon_rqt($rqt, 'INSERT upload_max_size INTO parametres artificial_intelligence');
            }

            // GN - JP - Création de la table des documents associés à la liste de lecture dans le module IA
            $rqt = "CREATE TABLE IF NOT EXISTS ai_shared_list_docnum (
                        id_ai_shared_list_docnum INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        name_ai_shared_list_docnum VARCHAR(255) NOT NULL DEFAULT '',
                        content_ai_shared_list_docnum MEDIUMTEXT NOT NULL DEFAULT '',
                        mimetype_ai_shared_list_docnum VARCHAR(255) NOT NULL DEFAULT '',
                        extfile_ai_shared_list_docnum VARCHAR(20) NOT NULL DEFAULT '',
                        path_ai_shared_list_docnum VARCHAR(255) NOT NULL DEFAULT '',
                        hash_name_ai_shared_list_docnum MEDIUMTEXT NOT NULL DEFAULT '',
                        hash_binary_ai_shared_list_docnum MEDIUMTEXT NOT NULL DEFAULT '',
                        num_list_ai_shared_list_docnum INT(11) UNSIGNED NOT NULL DEFAULT 0,
                        flag_ai_shared_list_docnum INT(1) UNSIGNED NOT NULL DEFAULT 0
                )";
            echo traite_addon_rqt($rqt, "CREATE TABLE ai_shared_list_docnum");
            break;

        case 5:
            // JP & TS & QV - Ajout de la colonne num_list_ai_session_semantique
            $rqt = "ALTER TABLE ai_session_semantique ADD ai_session_semantique_type INT(11) UNSIGNED NOT NULL DEFAULT 0";
            echo traite_addon_rqt($rqt, "ALTER TABLE ai_session_semantique ADD ai_session_semantique_type");

            // JP & TS & QV - Création de la table des listes de lecture partagées dans le module IA
            $rqt = "CREATE TABLE IF NOT EXISTS ai_session_shared_list (
                num_ai_session_semantique INT(11) UNSIGNED NOT NULL,
                num_empr INT(11) UNSIGNED NOT NULL,
                num_shared_list INT(11) UNSIGNED NOT NULL,
                UNIQUE KEY (num_ai_session_semantique, num_empr, num_shared_list)
            )";
            echo traite_addon_rqt($rqt, "CREATE TABLE ai_session_shared_list");
            break;

        case 6:
            // JP - Ajout d'un paramètre pour le remplacement du champ identifiant par le champ mail dans le formulaire du lecteur à l'OPAC
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='empr' AND sstype_param='username_with_mail'")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
                        VALUES (0, 'empr', 'username_with_mail', '0', '1', 'Activer le remplacement du champ identifiant par le champ mail dans le formulaire de changement de profil à l\'OPAC\n 0: Non \n 1: Oui') ";
                echo traite_addon_rqt($rqt, 'INSERT username_with_mail INTO parametres');
            }
            break;

        case 7:
            // JP - Nombre de notices max diffusées dans une bannette par mail
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'dsi' AND sstype_param = 'bannette_max_nb_notices_per_mail'")) == 0) {
                $rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                        VALUES ('dsi', 'bannette_max_nb_notices_per_mail', '100', 'Nombre maximum de notices diffusées dans une bannette par mail.', '', 0)";
                echo traite_addon_rqt($rqt, "INSERT dsi_bannette_max_nb_notices_per_mail INTO parametres");
            }
            break;

        case 8:
            // JP - Ajout d'une table pour gérer la diffusion manuelle
            $rqt = "CREATE TABLE IF NOT EXISTS dsi_send_queue (
                id_send_queue INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                channel_type VARCHAR(255) NOT NULL DEFAULT '',
                settings mediumblob NOT NULL,
                num_subscriber_diffusion INT(11) UNSIGNED NOT NULL,
                num_diffusion_history INT(11) UNSIGNED NOT NULL,
                flag INT(1) UNSIGNED NOT NULL DEFAULT 0
            )";
            echo traite_addon_rqt($rqt, "CREATE TABLE dsi_send_queue");
            break;

        case 9:
            // JP - QV - Activer la mise en cache des images dans les animations
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'animations' AND sstype_param ='active_image_cache' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                        VALUES (0, 'animations', 'active_image_cache', '0', 'Activer la mise en cache des images dans les animations\n 0: Non \n 1: Oui', '', 0)";
                echo traite_addon_rqt($rqt, "INSERT animations_active_image_cache INTO parametres");
            }
            break;

        case 10:
            // TS - Ajout d'une nouvelle option du parametre resa_alert_localized pour les notifications aux utilisateurs du site de retrait
            $rqt = "update parametres set comment_param='Mode de notification par email des nouvelles réservations aux utilisateurs ? \n0 : Recevoir toutes les notifications \n1 : Notification des utilisateurs du site de gestion du lecteur \n2 : Notification des utilisateurs associés à la localisation par défaut en création d\'exemplaire \n3 : Notification des utilisateurs du site de gestion et de la localisation d\'exemplaire \n4 : Notification des utilisateurs du site de retrait' where type_param= 'pmb' and sstype_param='resa_alert_localized' ";
            echo traite_addon_rqt($rqt, "update pmb_resa_alert_localized into parametres");
            break;

        case 11:
            // RT - Ajout d'un paramètre utilisateur permettant de définir un propriétaire par défaut en import d'exemplaires UNIMARC
            $rqt = "ALTER TABLE users ADD deflt_import_lenders TINYINT UNSIGNED DEFAULT 1 NOT NULL ";
            echo traite_addon_rqt($rqt, "ALTER TABLE users ADD deflt_import_lenders");
            break;

        case 12:
            // JP - Ajout d'un paramètre pour le préremplissage de la date de parution avec la date du jour lors de la création d'un bulletin
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' AND sstype_param='bulletin_date_parution' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES(0, 'pmb', 'bulletin_date_parution', '1', 'Préremplissage de la date de parution avec la date du jour lors de la création d\'un bulletin.\n0 : Non\n1 : Oui', '', 0)";
                echo traite_addon_rqt($rqt, "INSERT pmb_bulletin_date_parution INTO parametres");
            }
            break;

        case 13:
            // DB - Ajout de parametres d'indexation
            // pmb_clean_mode : mode d'indexation a utiliser (0 : par entite / 1 : par champ)
            // pmb_clean_nb_elements_by_field : nb d'elements a traiter par passe en indexation par champ
            // pmb_clean_nb_elements_by_callable : nb d'elements a traiter par passe en indexation par callable
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='clean_mode' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES(0, 'pmb', 'clean_mode', '0', 'Mode d\'indexation ( 0 : par entité, 1 : par champ)', '', 1)";
                echo traite_addon_rqt($rqt, "INSERT clean_mode INTO parametres");
            }
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='clean_nb_elements_by_field' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES(0, 'pmb', 'clean_nb_elements_by_field', '50000', 'Nombre d\'éléments traités par passe en indexation par champ', '', 0)";
                echo traite_addon_rqt($rqt, "INSERT pmb_clean_nb_elements_by_field INTO parametres");
            }
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='clean_nb_elements_by_callable' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                    VALUES(0, 'pmb', 'clean_nb_elements_by_callable', '5000', 'Nombre d\'éléments traités par passe en indexation par callable', '', 0)";
                echo traite_addon_rqt($rqt, "INSERT pmb_clean_nb_elements_by_callable INTO parametres");
            }
            break;

        case 14:
            // GN : Ajout d'un paramètre pour activer la recherche sémantique pour les utilisateurs connectés
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='ai' AND sstype_param='allow_semantic_search'")) == 0) {
                $rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('ai', 'allow_semantic_search', '1', 'Activer la recherche sémantique uniquement pour les lecteurs connectés.\n 0 : Non.\n 1 : Oui.', '', 0)";
                echo traite_addon_rqt($rqt, 'INSERT INTO parametres allow_semantic_search');
            }
            break;

        case 15:
            // TS - Ajout d'un paramètre pour le nombre de versions de portail à conserver
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='cms' AND sstype_param='portal_version_history'")) == 0) {
                $rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                            VALUES ('cms', 'portal_version_history', '50', 'Nombre de versions de portail à conserver', '', 0)";
                echo traite_addon_rqt($rqt, 'INSERT cms_portal_version_history INTO parametres');
            }
            break;

        case 16:
            // DG - Lettres de retard (niveau 1) - titre avant la liste des documents en retard
            if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1title_list' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
    					VALUES(0,'pdflettreretard','1title_list','','Titre apparaissant avant la liste des documents en retard de niveau 1','relance_1',0)";
                echo traite_addon_rqt($rqt, "insert pdflettreretard_1title_list into parametres");
            }

            // DG - Lettres de retard (niveau 2) - titre avant la liste des documents en retard
            if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='2title_list' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
    					VALUES(0,'pdflettreretard','2title_list','','Titre apparaissant avant la liste des documents en retard de niveau 2','relance_2',0)";
                echo traite_addon_rqt($rqt, "insert pdflettreretard_2title_list into parametres");
            }

            // DG - Lettres de retard (niveau 3) - titre avant la liste des documents en retard
            if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='3title_list' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
    					VALUES(0,'pdflettreretard','3title_list','','Titre apparaissant avant la liste des documents en retard de niveau 3','relance_3',0)";
                echo traite_addon_rqt($rqt, "insert pdflettreretard_3title_list into parametres");
            }

            // DG - Mails de retard (niveau 1) - titre avant la liste des documents en retard
            if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='1title_list' ")) == 0) {
                $rqt = "INSERT INTO parametres VALUES (0,'mailretard','1title_list','','Titre apparaissant avant la liste des documents en retard de niveau 1','relance_1',0)";
                echo traite_addon_rqt($rqt, "insert mailretard_1title_list into parametres");
            }

            // DG - Mails de retard (niveau 2) - titre avant la liste des documents en retard
            if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='2title_list' ")) == 0) {
                $rqt = "INSERT INTO parametres VALUES (0,'mailretard','2title_list','','Titre apparaissant avant la liste des documents en retard de niveau 2','relance_2',0)";
                echo traite_addon_rqt($rqt, "insert mailretard_2title_list into parametres");
            }

            // DG - Mails de retard (niveau 3) - titre avant la liste des documents en retard
            if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='3title_list' ")) == 0) {
                $rqt = "INSERT INTO parametres VALUES (0,'mailretard','3title_list','','Titre apparaissant avant la liste des documents en retard de niveau 3','relance_3',0)";
                echo traite_addon_rqt($rqt, "insert mailretard_3title_list into parametres");
            }

            // DG - Lettres de retard (niveau 3) - ordonnancement des niveaux
            if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='3level_order' ")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
    					VALUES(0,'pdflettreretard','3level_order','0','Ordre d\'affichage des niveaux de relance : \n 0 : 1, 2 puis 3 \n 1 : 3, 2 puis 1','relance_3',0)";
                echo traite_addon_rqt($rqt, "insert pdflettreretard_3level_order into parametres");
            }

            // DG - Mails de retard (niveau 3) - ordonnancement des niveaux
            if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='3level_order' ")) == 0) {
                $rqt = "INSERT INTO parametres VALUES (0,'mailretard','3level_order','0','Ordre d\'affichage des niveaux de relance : \n 0 : 1, 2 puis 3 \n 1 : 3, 2 puis 1','relance_3',0)";
                echo traite_addon_rqt($rqt, "insert mailretard_3level_order into parametres");
            }
            break;

        case 17:
            // JP - Paramètre d'activation du nouveau tableau de bord
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'pmb' and sstype_param='dashboard_active'")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
            			VALUES (0, 'pmb', 'dashboard_active', '1', '0', 'Activer le nouveau module de tableau de bord.\r\n 0 : Non.\r\n 1 : Oui.')";
                echo traite_addon_rqt($rqt, "INSERT dashboard_active INTO parametres");
            }
            break;

        case 18:
            // RT - Ajout d'une colonne dans les catégories d'emprunteurs pour personnaliser l'activation du piège en prêt
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM empr_categ LIKE 'pret_already_loaned_active'")) == 0) {
                $rqt = "ALTER TABLE empr_categ ADD pret_already_loaned_active TINYINT UNSIGNED DEFAULT 1 NOT NULL";
                echo traite_addon_rqt($rqt, "ALTER TABLE empr_categ ADD pret_already_loaned_active TINYINT UNSIGNED DEFAULT 1 NOT NULL");
            }
            break;

        case 19:
            // DB - Correction de la traduction des descriptions d'univers et de segments
            $rqt = "UPDATE translation SET trans_text = trans_small_text, trans_small_text=null where trans_table='search_universes' and trans_field='universe_description' and trans_small_text is not null and (trans_text is null or trans_text='')";
            echo traite_addon_rqt($rqt, "UPDATE universe_description translation ");
            $rqt = "UPDATE translation SET trans_text = trans_small_text, trans_small_text=null where trans_table='search_segments' and trans_field='segment_description' and trans_small_text is not null and (trans_text is null or trans_text='')";
            echo traite_addon_rqt($rqt, "UPDATE segment_description translation ");
            break;

        case 20:
            // JP - TS - Paramètre pour la gestion du calcul des amendes par période
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'pmb' and sstype_param='gestion_financiere_periode'")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
                        VALUES (0, 'pmb', 'gestion_financiere_periode', '0', '0', 'Détermine la période de calcul des amendes (en jours).\r\n 0 : Aucune période.\r\n 1 : Amende journalière.\r\n 2 : Amende tous les 2 jours.\r\n ...\r\n 7 : Amende par semaine.')";
                echo traite_addon_rqt($rqt, "INSERT pmb_gestion_financiere_periode INTO parametres");
            }
            // JP - TS - Paramètre pour la gestion des amendes par période
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param = 'pmb' and sstype_param='gestion_financiere_periode_amende'")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
                        VALUES (0, 'pmb', 'gestion_financiere_periode_amende', '0', '0', 'Gestion des amendes par période.\r\n 0 : Amende par période complète.\r\n 1 : Amende par période entamée.\r\n')";
                echo traite_addon_rqt($rqt, "INSERT pmb_gestion_financiere_periode_amende INTO parametres");
            }
            break;

        case 21:
            // TS & DG - Gestion des rôles utilisateurs/groupes
            $rqt = "CREATE TABLE IF NOT EXISTS users_roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL DEFAULT '',
                comment TEXT NOT NULL
            )";
            echo traite_addon_rqt($rqt, "CREATE TABLE users_roles");

            // TS & DG - Gestion des droits sur les rôles utilisateurs/groupes
            // type = module / tab / subtab / action
            $rqt = "CREATE TABLE IF NOT EXISTS users_roles_rights (
                id INT AUTO_INCREMENT PRIMARY KEY,
                component VARCHAR(50) NOT NULL DEFAULT '',
                module VARCHAR(50) NOT NULL DEFAULT '',
                categ VARCHAR(50) NOT NULL DEFAULT '',
                sub VARCHAR(50) NOT NULL DEFAULT '',
                url_extra VARCHAR(255) NOT NULL DEFAULT '',
                action VARCHAR(255) NOT NULL DEFAULT '',
                visible INT(1) NOT NULL DEFAULT 1,
                privilege INT(1) NOT NULL DEFAULT 0,
                log INT(1) NOT NULL DEFAULT 0,
                num_role INT NOT NULL DEFAULT 0
            )";
            echo traite_addon_rqt($rqt, "CREATE TABLE users_roles_rights");

            // TS & DG - Gestion des utilisateurs/groupes sur les rôles
            $rqt = "CREATE TABLE IF NOT EXISTS users_roles_members (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type_member VARCHAR(50) NOT NULL DEFAULT '',
                num_member INT NOT NULL DEFAULT 0,
                num_role INT NOT NULL DEFAULT 0
            )";
            echo traite_addon_rqt($rqt, "CREATE TABLE users_roles_members");
            break;

        case 22:
            // JP - Modification de la colonne montant des rubriques pour passer de FLOAT(8,2) à FLOAT(12,2)
            $rqt = "ALTER TABLE rubriques MODIFY COLUMN montant FLOAT(12,2) UNSIGNED NOT NULL DEFAULT '0.00'";
            echo traite_addon_rqt($rqt, "ALTER TABLE rubriques MODIFY COLUMN montant");
            break;

        case 23:
            // TS & DB - Stockage des informations de mise a jour de base
            $rqt = "CREATE TABLE IF NOT EXISTS db_update_logs (
                id_db_update_log INT AUTO_INCREMENT PRIMARY KEY,
                db_update_log_type VARCHAR(10) NOT NULL DEFAULT '',
                db_update_log_version VARCHAR(50) NOT NULL DEFAULT '',
                db_update_log_query TEXT NOT NULL DEFAULT '',
                db_update_log_message VARCHAR(1000) NOT NULL DEFAULT '',
                db_update_log_error INT(10) NOT NULL DEFAULT 0,
                db_update_log_result VARCHAR(1000) NOT NULL DEFAULT '',
                db_update_log_date DATETIME NOT NULL DEFAULT NOW()
            )";
            echo traite_addon_rqt($rqt, "CREATE TABLE db_update_logs");

            // TS & DB - Parametre $pmb_bdd_subversion_error = Etape d'echec de mise a jour de la sous-version de base de donnees
            if ( pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param = 'pmb' and sstype_param='bdd_subversion_error' ")) == 0 ){
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion)
                    VALUES (0, 'pmb', 'bdd_subversion_error', '0', 'Etape d\'échec de mise à jour de la sous-version de base de données', 1)";
                echo traite_addon_rqt($rqt,"insert pmb_bdd_subversion_error=0 into parametres");
            }
            break;

        case 24:
            // JP - Ajout d'une colonne pour les emprunteurs dans la file d'attente des envois manuels de la refonte DSI
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM dsi_send_queue LIKE 'num_subscriber_empr'")) == 0) {
                $rqt = "ALTER TABLE dsi_send_queue ADD num_subscriber_empr INT(11) UNSIGNED DEFAULT 0 NOT NULL";
                echo traite_addon_rqt($rqt, "ALTER TABLE dsi_send_queue ADD num_subscriber_empr INT(11) UNSIGNED DEFAULT 0 NOT NULL");
            }
            break;

        case 25:
        	// QV - Ajout d'une colonne pour les logs
        	if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM logopac LIKE 'validated'")) == 0) {
        		$rqt = "ALTER TABLE logopac ADD validated TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL";
        		echo traite_addon_rqt($rqt, "ALTER TABLE logopac ADD validated TINYINT(1) UNSIGNED DEFAULT 0 NOT NULL");

        		$rqt = "UPDATE logopac set validated=1";
        		echo traite_addon_rqt($rqt, $rqt);
        	}
            break;
        case 26:
            // GN - Suppression des colonnes "num_tag" qui ne sont plus utilisées dans la refonte DSI.
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM dsi_channel LIKE 'num_tag'")) != 0) {
                $rqt = "ALTER TABLE dsi_channel DROP COLUMN num_tag";
                echo traite_addon_rqt($rqt, "ALTER TABLE dsi_channel DROP COLUMN num_tag");
            }
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM dsi_diffusion LIKE 'num_tag'")) != 0) {
                $rqt = "ALTER TABLE dsi_diffusion DROP COLUMN num_tag";
                echo traite_addon_rqt($rqt, "ALTER TABLE dsi_diffusion DROP COLUMN num_tag");
            }
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM dsi_item LIKE 'num_tag'")) != 0) {
                $rqt = "ALTER TABLE dsi_item DROP COLUMN num_tag";
                echo traite_addon_rqt($rqt, "ALTER TABLE dsi_item DROP COLUMN num_tag");
            }
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM dsi_event LIKE 'num_tag'")) != 0) {
                $rqt = "ALTER TABLE dsi_event DROP COLUMN num_tag";
                echo traite_addon_rqt($rqt, "ALTER TABLE dsi_event DROP COLUMN num_tag");
            }
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM dsi_product LIKE 'num_tag'")) != 0) {
                $rqt = "ALTER TABLE dsi_product DROP COLUMN num_tag";
                echo traite_addon_rqt($rqt, "ALTER TABLE dsi_product DROP COLUMN num_tag");
            }
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM dsi_subscribers LIKE 'num_tag'")) != 0) {
                $rqt = "ALTER TABLE dsi_subscribers DROP COLUMN num_tag";
                echo traite_addon_rqt($rqt, "ALTER TABLE dsi_subscribers DROP COLUMN num_tag");
            }
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM dsi_subscriber_list LIKE 'num_tag'")) != 0) {
                $rqt = "ALTER TABLE dsi_subscriber_list DROP COLUMN num_tag";
                echo traite_addon_rqt($rqt, "ALTER TABLE dsi_subscriber_list DROP COLUMN num_tag");
            }
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM dsi_view LIKE 'num_tag'")) != 0) {
                $rqt = "ALTER TABLE dsi_view DROP COLUMN num_tag";
                echo traite_addon_rqt($rqt, "ALTER TABLE dsi_view DROP COLUMN num_tag");
            }
            break;
        case 27:
            //DB - Ajout champ import dans la table notices_custom (champs perso de notices)
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM notices_custom LIKE 'import'")) == 0) {
                $rqt = "ALTER TABLE notices_custom ADD import INT(1) UNSIGNED DEFAULT 1 NOT NULL";
                echo traite_addon_rqt($rqt, "ALTER TABLE notices_custom ADD field import");
            }
            break;
        case 28:
            //RT - Ajout d'un champ pour paramétrer les champs perso visibles par type de demande
            if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM demandes_type LIKE 'allowed_pperso'")) == 0) {
                $rqt = "ALTER TABLE demandes_type ADD allowed_pperso TEXT NOT NULL DEFAULT ''";
                echo traite_addon_rqt($rqt, "ALTER TABLE demandes_type ADD allowed_pperso TEXT NOT NULL DEFAULT ''");
            }
            break;
        case 29:
            // GN - Présence d'une authentification externe
            if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='security' AND sstype_param='authentification_external'")) == 0) {
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
                    VALUES (0, 'security', 'authentification_external', '0', '0', 'Présence d\'une authentification externe\n 0 : Non\n 1 : Oui')";
                echo traite_addon_rqt($rqt, 'INSERT security_authentification_external INTO parametres');
            }
            break;
        case 30:
            // TS - ajout d'un paramètre de tri par défaut
            if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='default_sort' "))==0){
                $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','default_sort','d_num_6,c_text_1','Tri par défaut des recherches en gestion.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))','',0)" ;
                echo traite_addon_rqt($rqt,"insert pmb_default_sort into parametres") ;
            }
            break;
        case 31:
        	// QV & DB - Limitation des tentatives de connexion
        	$rqt = "ALTER TABLE users ADD param_notify_login_failed TINYINT UNSIGNED DEFAULT 0 NOT NULL ";
        	echo traite_addon_rqt($rqt, "ALTER TABLE users ADD param_notify_login_failed=0");

        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='opac' AND sstype_param='active_log_login_attempts'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('opac', 'active_log_login_attempts', '0', 'Activer le journal des tentatives de connexion\n 0 : Non\n 1 : Oui', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT opac_active_log_login_attempts=1 INTO parametres');
        	}
        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='opac' AND sstype_param='log_retention'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('opac', 'log_retention', '1', 'Durée de conservation des logs de connexion (en mois)', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT opac_log_retention=1 INTO parametres');
        	}
        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='opac' AND sstype_param='notify_after_failures'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('opac', 'notify_after_failures', '5', 'Nombre de tentatives de connexion avant notification des utilisateurs', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT opac_notify_after_failures=5 INTO parametres');
        	}
        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='opac' AND sstype_param='block_after_failures'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('opac', 'block_after_failures', '5', 'Nombre de tentatives de connexion avant blocage', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT opac_block_after_failures=5 INTO parametres');
        	}
        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='opac' AND sstype_param='block_duration'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('opac', 'block_duration', '180', 'Durée de blocage (en secondes)', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT opac_block_duration=180 INTO parametres');
        	}

        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='active_log_login_attempts'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('pmb', 'active_log_login_attempts', '0', 'Activer le journal des tentatives de connexion\n 0 : Non\n 1 : Oui', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT pmb_active_log_login_attempts=1 INTO parametres');
        	}
        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='log_retention'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('pmb', 'log_retention', '1', 'Durée de conservation des logs de connexion (en mois)', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT pmb_log_retention=1 INTO parametres');
        	}
        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='notify_after_failures'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('pmb', 'notify_after_failures', '5', 'Nombre de tentatives de connexion avant notification des utilisateurs', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT pmb_notify_after_failures=5 INTO parametres');
        	}
        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='block_after_failures'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('pmb', 'block_after_failures', '5', 'Nombre de tentatives de connexion avant blocage', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT pmb_block_after_failures=5 INTO parametres');
        	}
        	if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param='pmb' AND sstype_param='block_duration'")) == 0) {
        		$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
                VALUES ('pmb', 'block_duration', '180', 'Durée de blocage (en secondes)', 'security', 0)";
        		echo traite_addon_rqt($rqt, 'INSERT pmb_block_duration=180 INTO parametres');
        	}

        	$rqt = "CREATE TABLE IF NOT EXISTS gestion_login_attempt (
			    id_gestion_login_attempt INT AUTO_INCREMENT PRIMARY KEY,
			    gestion_login_attempt_ip VARCHAR(45) NOT NULL,
			    gestion_login_attempt_login VARCHAR(255) NOT NULL,
			    gestion_login_attempt_time DATETIME NOT NULL DEFAULT now(),
			    gestion_login_attempt_success TINYINT NOT NULL,
			    UNIQUE KEY u_ip_login_time (gestion_login_attempt_ip,gestion_login_attempt_login,gestion_login_attempt_time),
			    INDEX i_ip (gestion_login_attempt_ip),
			    INDEX i_login (gestion_login_attempt_login)
			)";
        	echo traite_addon_rqt($rqt, "CREATE TABLE gestion_login_attempt");

        	$rqt = "CREATE TABLE IF NOT EXISTS opac_login_attempt (
			    id_opac_login_attempt INT AUTO_INCREMENT PRIMARY KEY,
			    opac_login_attempt_ip VARCHAR(45) NOT NULL,
			    opac_login_attempt_login VARCHAR(255) NOT NULL,
			    opac_login_attempt_time DATETIME NOT NULL DEFAULT now(),
			    opac_login_attempt_success TINYINT NOT NULL,
			    UNIQUE KEY u_ip_login_time (opac_login_attempt_ip,opac_login_attempt_login,opac_login_attempt_time),
			    INDEX i_ip (opac_login_attempt_ip),
			    INDEX i_login (opac_login_attempt_login)
			);";
        	echo traite_addon_rqt($rqt, "CREATE TABLE opac_login_attempt");

        	$rqt = "CREATE TABLE IF NOT EXISTS ip_whitelist (
			    id_ip_whitelist INT AUTO_INCREMENT PRIMARY KEY,
			    ip_whitelist_time DATETIME NOT NULL DEFAULT now(),
			    ip_whitelist_ip VARCHAR(45) NOT NULL UNIQUE
			);";
        	echo traite_addon_rqt($rqt, "CREATE TABLE ip_whitelist");

        	$rqt = "CREATE TABLE IF NOT EXISTS ip_blacklist (
			    id_ip_blacklist INT AUTO_INCREMENT PRIMARY KEY,
			    ip_blacklist_time DATETIME NOT NULL DEFAULT now(),
			    ip_blacklist_ip VARCHAR(45) NOT NULL UNIQUE
			);";
        	echo traite_addon_rqt($rqt, "CREATE TABLE ip_blacklist");
            break;
    }
    $db_update_step ++;
}

/**
 * ****************** JUSQU'ICI *************************************************
 */
/* PENSER à faire +1 au paramètre $pmb_subversion_database_as_it_shouldbe dans includes/config.inc.php */
/* COMMITER les deux fichiers addon.inc.php ET config.inc.php en même temps */
echo traite_addon_rqt("update parametres set valeur_param='" . $db_update_step . "' where type_param='pmb' and sstype_param='bdd_subversion'", "Update to $db_update_step database subversion.");
echo "<table>";
