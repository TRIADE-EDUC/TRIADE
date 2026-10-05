<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alter.php,v 1.25.2.1.2.1 2025/06/09 07:44:13 dbellamy Exp $

// définition du minimum nécéssaire
$base_path = "../..";
$base_auth = "";
$base_title = "";

require_once "$base_path/includes/init.inc.php";

global $action, $msg, $pmb_version_database_as_it_should_be;

function form_relance($maj_suivante = "lancement")
{
    global $msg;
    global $current_module;

    $dummy = "<form class='form-$current_module' NAME=\"majbase\" METHOD=\"post\" ACTION=\"alter.php\">";
    $dummy .= "<INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"alter\">";
    $dummy .= "<INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"\">";
    $dummy .= "<INPUT NAME=\"action\" TYPE=\"hidden\" value=\"" . $maj_suivante . "\">";
    $dummy .= "<br /><br /><a href=\"alter.php?categ=alter&sub=&action=" . $maj_suivante . "\">" . $msg[1802] . "</a><br />";
    $dummy .= "</FORM>";
    return $dummy;
}

function traite_rqt($requete = "", $message = "")
{
    global $charset;
    global $db_update_log_version;

    $retour = "Successful";
    if($charset == "utf-8") {
    	// Contrairement au addon ce n'est pas à faire car dans les fichiers alter_vX.inc.php on fait un set names latin1
    	// $requete=encoding_normalize::utf8_normalize($requete);
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
                $retour = "<font color=\"#FF0000\">Error may be fatal : <i>" . $erreur_msg . "<i></font>";
                break;
        }
    }
    $result = pmb_mysql_query("SHOW TABLES LIKE 'db_update_logs'");
    if (pmb_mysql_num_rows($result)) {
        $query_log = " INSERT INTO db_update_logs (db_update_log_type, db_update_log_version, db_update_log_query, db_update_log_message, db_update_log_error, db_update_log_result)
        VALUES ('alter', '" . $db_update_log_version . "', '" . addslashes($requete) . "', '" . addslashes($message) . "', $erreur_no, '" . addslashes(strip_tags($retour)) . "')";
        pmb_mysql_query($query_log);
    }

    return "<tr><td><font size='1'><span data-alter='message'>" . $message . "</span></font></td><td><font size='1'><span data-alter='result'>" . $retour . "</span></font></td></tr>";
}

settype($action, "string");

/* vérification de l'existence de la table paramètres */
$query = "select count(1) from parametres ";
$req = pmb_mysql_query($query);
if (! $req) { /* la table parametres n'existe pas... */
    $rqt = "CREATE TABLE if not exists parametres (
            id_param INT( 6 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            type_param VARCHAR( 20 ) ,
            sstype_param VARCHAR( 20 ) ,
            valeur_param VARCHAR( 255 ) ,
            PRIMARY KEY ( id_param ) ,
            INDEX ( type_param , sstype_param )
		) ";
    pmb_mysql_query($rqt);
}

$query = "select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version' ";
$req = pmb_mysql_query($query);
if (pmb_mysql_num_rows($req) == 0) { /* la version de la base n'existe pas... */
    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param) VALUES (0, 'pmb', 'bdd_version', 'v1.0')";
    pmb_mysql_query($rqt);
    $query = "select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version' ";
    $req = pmb_mysql_query($query);
}

$data = pmb_mysql_fetch_array($req);
$pmb_bdd_version = $data['valeur_param'];

echo "<div id='contenu-frame'>";
echo "<h1>" . $msg[1803] . "<span class='bdd_version'>" . $pmb_bdd_version . "</span></h1>";
echo "<h2>" . $msg['pmb_v_db_as_it_should_be'] . "<span class='bdd_version'>" . $pmb_version_database_as_it_should_be . "</span></h2>";

if ($action == "lancement" || ! $action) {
    $deb_pmb_bdd_version = substr($pmb_bdd_version, 0, 2);
} else {
    $deb_pmb_bdd_version = substr($action, 0, 2);
}

switch ($deb_pmb_bdd_version) {
    case "v1":
        include ("./alter_v1.inc.php");
        break;
    case "v2":
        include ("./alter_v2.inc.php");
        break;
    case "v3":
        include ("./alter_v3.inc.php");
        break;
    case "v4":
        include ("./alter_v4.inc.php");
        break;
    case "v5":
        include ("./alter_v5.inc.php");
        break;
    case "v6":
        include ("./alter_v6.inc.php");
        break;
}

echo "</div>";
print "</body></html>";
