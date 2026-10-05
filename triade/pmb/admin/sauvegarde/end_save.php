<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: end_save.php,v 1.14.4.1 2025/02/27 13:45:22 gneveu Exp $
global $base_auth, $base_path, $base_title, $current_module;
global $filename, $temp_file, $currentSauv, $logid, $msg, $csrf_token;
global $sauvegardes, $sauv_sauvegarde_nom;

// Création du fichier final et transfert vers les lieux puis passage au jeu suivant
$base_path = "../..";
$base_auth = "SAUV_AUTH|ADMINISTRATION_AUTH";
$base_title = "\$msg[sauv_misc_running]";

require ($base_path . "/includes/init.inc.php");

require_once ("lib/api.inc.php");

$end_save_ko = "end_save KO";

// Répertoire autorisé
$allowed_directory = "../../admin/backup/backups/";

// Extensions autorisées
$allowed_extensions = [
    "sql",
    "sav"
];

// Vérification du nom de fichier
$basename_filename = basename($filename);
$basename_temp_file = basename($temp_file);

// Vérification de l'extension
$file_extension_filename = pathinfo($basename_filename, PATHINFO_EXTENSION);
$file_extension_temp_file = pathinfo($basename_temp_file, PATHINFO_EXTENSION);
if (! in_array($file_extension_filename, $allowed_extensions)) {
    write_log($end_save_ko, $logid);
    echo $msg["admin_files_gestion_error_not_valid"] . $file_extension_temp_file;
    unset($_SESSION["csrf_token"]);
    exit();
}
if (! in_array($file_extension_temp_file, $allowed_extensions)) {
    write_log($end_save_ko, $logid);
    echo ($msg["admin_files_gestion_error_not_valid"] . $file_extension_temp_file);
    unset($_SESSION["csrf_token"]);
    exit();
}

// Chemin complet du fichier
$file_path_filename = $allowed_directory . $basename_filename;
$file_path_temp_file = $allowed_directory . $basename_filename;
// Vérification que le fichier existe dans le répertoire autorisé
if (! file_exists($file_path_filename)) {
    write_log($end_save_ko, $logid);
    echo $msg["end_save_no_check_acces_file"];
    unset($_SESSION["csrf_token"]);
    exit();
}
if (! file_exists($file_path_temp_file)) {
    write_log($end_save_ko, $logid);
    echo $msg["end_save_no_check_acces_file"];
    unset($_SESSION["csrf_token"]);
    exit();
}

// Vérification que le fichier est bien dans le répertoire autorisé
$real_path_filename = realpath($file_path_filename);
$real_path_temp_file = realpath($file_path_temp_file);
if (strpos($real_path_filename, realpath($allowed_directory)) !== 0) {
    write_log($end_save_ko, $logid);
    echo $msg["end_save_no_check_rep_file"];
    unset($_SESSION["csrf_token"]);
    exit();
}
if (strpos($real_path_temp_file, realpath($allowed_directory)) !== 0) {
    write_log($end_save_ko, $logid);
    echo $msg["end_save_no_check_rep_file"];
    unset($_SESSION["csrf_token"]);
    exit();
}

if (!verify_csrf("", false)) {
    write_log($end_save_ko, $logid);
    echo $msg["end_save_no_check_csrf"];
    unset($_SESSION["csrf_token"]);
    exit();
}
unset($_SESSION["csrf_token"]);

$currentSauv = intval($currentSauv);
$logid = intval($logid);

// Entête
print "<div id=\"contenu-frame\">\n";
echo "<h1>" . $msg["sauv_misc_export_running"] . "</h1>\n";
echo "<form class='form-$current_module' name=\"sauv\" action=\"\" method=\"post\">\n";
echo "<br /><br />";
echo "<input type=\"button\" value=\"" . $msg["sauv_annuler"] . "\" onClick=\"document.location='launch.php';\" class=bouton>\n";

// Jeux à suivre
if (! empty($sauvegardes) && is_array($sauvegardes)) {
    for ($i = 0; $i < count($sauvegardes); $i ++) {
        echo "<input type=\"hidden\" name=\"sauvegardes[]\" value=\"" . $sauvegardes[$i] . "\">\n";
    }
}
// Sauvegarde courante
echo "<input type=\"hidden\" name=\"currentSauv\" value=\"" . $currentSauv . "\">\n";

// Fusion des deux fichiers en un seul

// print "<h1>FILENAME=$filename TEMP_FILE=$temp_file</h1>";

$fe = @fopen($filename, "a");
$fsql = @fopen($temp_file, "rb");

if ((! $fe) || (! $fsql))
    abort("Could not create final file", $logid);

// $to_happend=fread($fsql,filesize($temp_file));
// fwrite($fe,"#data-section\r\n".$to_happend);

// MaxMan: modified because this error:
// Fatal error: Allowed memory size of 8388608 bytes exhausted
// (tried to allocate 6495315 bytes) in
// /var/www/pmb/admin/sauvegarde/end_save.php on line 52

fwrite($fe, "#data-section\r\n");
do {
    $to_append = fread($fsql, 8192);
    if (strlen($to_append) == 0) {
        break;
    }
    fwrite($fe, $to_append);
} while (true);

fclose($fsql);
fclose($fe);
unlink($temp_file);

// Log : Backup complet
write_log("Backup complete", $logid);

// Récupération de la taille du fichier
if ($tmp_size = filesize($filename)) {
    if ($tmp_size < 1000) {
        write_log("Backup size : " . round($tmp_size, 3) . " bytes", $logid);
    } else {
        $tmp_size = $tmp_size / 1024;
        if ($tmp_size < 1000) {
            write_log("Backup size : " . round($tmp_size, 3) . " Ko", $logid);
        } else {
            $tmp_size = $tmp_size / 1024;
            write_log("Backup size : " . round($tmp_size, 3) . " Mo", $logid);
        }
    }
}

// Succeed
$requete = "update sauv_log set sauv_log_succeed=1 where sauv_log_id=" . $logid;
@pmb_mysql_query($requete);

// Paramètres
echo "<input type=\"hidden\" name=\"logid\" value=\"" . $logid . "\">\n";
echo "<h2>" . sprintf($msg["sauv_misc_merging"], $sauv_sauvegarde_nom) . "</h2>";
echo "<input type=\"hidden\" name=\"filename\" value=\"$filename\">";

// Récupération des lieux
$requete = "select sauv_sauvegarde_lieux from sauv_sauvegardes where sauv_sauvegarde_id=" . $currentSauv;
$resultat = @pmb_mysql_query($requete);
$lieux = pmb_mysql_result($resultat, 0, 0);

$tLieux = explode(",", $lieux);
echo "<script>";
// Pour chaque lieu, ouvrir une fenêtre de transfert
for ($i = 0; $i < count($tLieux); $i ++) {
    echo "openPopUp(\"copy_lieux.php?filename=$filename&logid=$logid&sauv_lieu_id=" . $tLieux[$i] . "\",\"copy_lieux_$i\", 400, 200, -2, -2, \"menubar=no,resizable=1,scrollbars=yes\");\n";
}
echo "</script>";
echo "</form></body></html>";

// Passer au jeu suivant
echo "<script>document.sauv.action=\"run.php\"; document.sauv.submit();</script>";
print "</div>";
