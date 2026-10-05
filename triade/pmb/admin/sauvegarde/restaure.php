<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: restaure.php,v 1.12 2023/03/02 08:45:38 dbellamy Exp $

@set_time_limit(1200);

//Restauration d'un jeu de sauvegarde

global $msg;

$base_path = "../..";
$base_auth = "SAUV_AUTH|ADMINISTRATION_AUTH";
$current_module = 'restaure';

//Initialisation variables
$critical = !empty($_GET["critical"]) ? 1 : 0;
$logid = 0;
$filename = '';
$displayed_filename = '';
$charset = 'iso-8859-1';

//Est-ce une restauration critique (dans ce cas, pas de vérification d'utilisateur)?
if ($critical) {
    
    if ( is_readable("../backup/backups/critical_upload.php") ) {
        $fc = file_get_contents("../backup/backups/critical_upload.php");
        if(false !== $fc) {
            $filename = trim(explode('//', $fc)[1]);
        }
    }
    include "emergency/messages_env_r.inc.php";
    $displayed_filename = isset($_GET['filename']) ? $_GET['filename'] : '';
    
} else {
    require $base_path."/includes/init.inc.php";
    $critical = 0;
    $displayed_filename = $filename;
}

require_once "lib/api.inc.php";

$tpl = '';
if($critical) {
    $tpl = '<!DOCTYPE html><html><head><meta charset="'.$charset.'" ></head><body>';
}
$tpl.= '
<div id="contenu-frame">
    <h1>'.sprintf($msg['sauv_misc_restaure'], $displayed_filename).'</h1>
    <form class="form-'.$current_module.'" name="infos" action="restaure_act.php" method="post" >
        <input type="hidden" name="critical" value="'.$critical.'" />
        <input type="hidden" name="logid" value="'.$logid.'" />
        <input type="hidden" name="filename" value="'.$displayed_filename.'" />
        <!-- file_error -->
        <!-- content -->
        <input type="submit" value="'.$msg["sauv_misc_restaure_launch"].'" onClick="return confirm(\''.$msg["sauv_misc_restaure_confirm"].'\');" class="bouton" >
        <input type="button" value="'.$msg["sauv_annuler"].'" class="bouton" onClick="self.close();" >
    </form>
</div>';
if($critical) {
    $tpl.= '</body></html>';
}

//Verification du fichier
$infos = read_infos("../backup/backups/".$filename);
if ( !empty($infos['error']) ) {
    @unlink ($filename);
    $tpl = str_replace('<!-- file_error -->',  '<h2>'.htmlentities($msg['sauv_misc_restaure_bad_sauv_file'], ENT_QUOTES, $charset).'</h2>', $tpl);
    print $tpl;
    exit();
}

$content =
'<table class="center">
    <tr>
        <td style="border-width:1px;border-style:solid"><b>'.htmlentities($msg["sauv_misc_restaure_set_name"], ENT_QUOTES, $charset).'</b></td>
        <td style="border-width:1px;border-style:solid">'.$infos["Name"].'</td>
    </tr>
    <tr>
        <td style="border-width:1px;border-style:solid"><b>'.htmlentities($msg["sauv_misc_restaure_date_sauv"], ENT_QUOTES, $charset).'</b></td>
        <td style="border-width:1px;border-style:solid">'.$infos["Date"].'</td>
    </tr>
    <tr>
        <td style="border-width:1px;border-style:solid"><b>'.htmlentities($msg["sauv_misc_restaure_hour_sauv"], ENT_QUOTES, $charset).'</b></td>
        <td style="border-width:1px;border-style:solid">'.$infos["Start time"].'</td>
    </tr>
    <tr>
        <td style="border-width:1px;border-style:solid"><b>'.htmlentities($msg["sauv_misc_restaure_tables_sauv"], ENT_QUOTES, $charset).'</b></td>
        <td style="border-width:1px;border-style:solid">
            <table width=100%>';

//Liste des tables
$tTables = explode(",", $infos["Tables"]);
$n=0;
for ($i=0; $i<count($tTables); $i++) {
    if ($n==0) {
        $content.= '<tr>';
    }
    $content.='<td style="border-width:1px;border-style:solid"><input type="checkbox" value="'.$tTables[$i].'" name="tables[]" checked />&nbsp;'.$tTables[$i].'</td>';
    $n++;
    if ($n==4) {
        $n=0;
        $content.= '</tr>';
    }
}
if ($n<4) {
    for ($i=$n; $i<4; $i++) {
        $content.= '<td style="border-width:1px;border-style:solid">&nbsp;</td>';
    }
    $content.= '</tr>';
}
$content.= '</table>
        </td>
    </tr>
</table>
<br /><br />';

/*
 // Compression
 if ($infos["Compress"]==1) {
 $content.= '<input type="hidden" name="compress" value="1" /><b>'.$msg["sauv_misc_restaure_compressed"].' ';
 
 $tCompressCommand = explode(":",$infos["Compress commands"]);
 $content.= '<input type="hidden" name="decompress_type" value="'.$tCompressCommand[0].'" />';
 if ($tCompressCommand[0]=="internal") {
 $content.= $msg["sauv_misc_restaure_bz2"].'</b><br />';
 } else {
 $content.= $msg["sauv_misc_restaure_external"].' '.$tCompressCommand[1].'</b><br />';
 $content.=  '<table>
 <tr>
 <td>'.$msg["sauv_misc_restaure_dec_command"].'</td>
 <td><input name="decompress" type="text" value="'.$tCompressCommand[2].'"></td>
 </tr>
 <tr>
 <td>'.$msg["sauv_misc_restaure_dec_ext"].'</td>
 <td><input name="decompress_ext" type="text" value="'.$tCompressCommand[3].'"></td>
 </tr>
 </table>';
 }
 }
 $content.= '<br />';
 *//*
 //Chiffrement
 if ($infos["Crypt"]==1) {
 $content.= '<input type="hidden" name="crypt" value="1" />
 <b>'.$msg["sauv_misc_restaure_crypted"].'</b><br />
 <table>
 <tr>
 <td>'.$msg["sauv_misc_restaure_ph1"].'</td>
 <td><input type="password" value="" name="phrase1"></td>
 </tr>
 <tr>
 <td>'.$msg["sauv_misc_restaure_ph2"].'</td>
 <td><input type="password" value="" name="phrase2"></td>
 </tr>
 </table>';
 }
 $content.= '<br />';
 */

//Critical
if ($critical) {
    $content.= '<b>'.htmlentities($msg["sauv_misc_restaure_connect_infos"], ENT_QUOTES, $charset).'</b>
        <br />
        <table>
            <tr>
                <td>'.htmlentities($msg["sauv_misc_restaure_host_addr"], ENT_QUOTES, $charset).'</td>
                <td><input name="host" type="text" ></td>
            </tr>
            <tr>
                <td>'.htmlentities($msg["sauv_misc_restaure_user"], ENT_QUOTES, $charset).'</td>
                <td><input name="db_user" type="text" ></td>
            </tr>
            <tr>
                <td>'.htmlentities($msg["sauv_misc_restaure_passwd"], ENT_QUOTES, $charset).'</td>
                <td><input name="db_password" type="password" ></td>
            </tr>
            <tr>
                <td>'.htmlentities($msg["sauv_misc_restaure_db"], ENT_QUOTES, $charset).'</td>
                <td><input name="db" type="text" ></td>
            </tr>
        </table>';
}

$tpl = str_replace('<!-- content -->', $content, $tpl);
print $tpl;

