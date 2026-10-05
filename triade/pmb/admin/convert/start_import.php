<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: start_import.php,v 1.33.4.1 2025/03/13 16:27:12 qvarin Exp $

use Pmb\Common\Library\Upload\UploadImport;

global $base_path, $base_auth, $base_title, $include_path, $n_current, $encodage_fic_source, $step, $input_type, $output_type, $first, $msg;
global $output_params, $input_params, $redirect, $import_type_l, $n_per_pass, $message_convert, $import_type, $noimport, $output, $redirect_string;
global $charset;

//Execution de l'import
$base_path = "../..";
$base_auth = "ADMINISTRATION_AUTH|CATALOGAGE_AUTH";
$base_title = "\$msg[ie_import_running]";

require_once "{$base_path}/includes/init.inc.php";
require_once "{$include_path}/parser.inc.php";
require_once "{$base_path}/admin/convert/start_import.class.php";
require_once "{$base_path}/admin/convert/start_export.class.php";

if (!isset($n_current)) {
    $n_current = 0;
}

//Gestion de l'encodage du fichier d'import
if (isset($encodage_fic_source)) {
    $_SESSION["encodage_fic_source"] = $encodage_fic_source;
} elseif (isset($_SESSION["encodage_fic_source"])) {
    $encodage_fic_source = $_SESSION["encodage_fic_source"];
}

/**
 *
 * @param string $origine
 * @return string
 */
function fetch_file_in($origine)
{
    global $base_path, $msg, $file_in;

    if (!empty($file_in) && file_exists($base_path."/temp/".$file_in)) {
        return $file_in;
    }

    if (!empty($_FILES['import_file']['name'])) {
    	try {
    		$uploadedFile = new UploadImport('import_file');
    		if ($uploadedFile->copy("{$base_path}/temp", $origine)) {
    			$file_in = $uploadedFile->filename;
    		} else {
    			throw new Exception($msg['ie_transfert_error_detail']);
    		}
    	} catch (Exception|InvalidArgumentException $e) {
    		error_message_history($msg["ie_tranfert_error"], $msg["ie_transfert_error_detail"], 1);
    		exit;
    	}
        return $file_in;
    }

    if (!$file_in) {
        $file_in = "convert".(defined("LOCATION") ? "_".constant("LOCATION") : "").".fic";
    }
    if (!file_exists($base_path."/temp/convert".(defined("LOCATION") ? "_".constant("LOCATION") : "").".fic")) {
    	error_message_history($msg["ie_file_not_found"], sprintf($msg["ie_file_not_found_detail"], $file_in), 1);
    	exit;
    }

    return $file_in;
}

/**
 * Récupération du chemin du fichier de paramétrage de l'import
 *
 * @param array $param
 * @return void
 */
function _item_($param)
{
    global $import_type;
    global $i;
    global $param_path;
    global $import_type_l;

    if (($i == $import_type) || ($import_type == $param['PATH'])) {
        $param_path = $param['PATH'];
        $import_type_l = $param['NAME'];
    }
    $i ++;
}

/**
 * Récupération du nom de l'import
 *
 * @param array $param
 * @return void
 */
function _import_name_($param)
{
    global $import_name;

    $import_name = $param['value'];
}

/**
 * Récupération du nombre de notices à traiter par passe
 *
 * @param array $param
 * @return void
 */
function _n_per_pass_($param)
{
    global $n_per_pass;

    $n_per_pass = $param['value'];
}

/**
 * Récupération du type d'entrée
 *
 * @param array $param
 * @return void
 */
function _input_($param)
{
    global $input_type;
    global $input_params;

    $input_type = $param['TYPE'];
    $input_params = $param;
}

/**
 * Récupération des étapes de conversion
 *
 * @param array $param
 * @return void
 */
function _step_($param)
{
    global $step;

    $step[] = $param;
}

/**
 * Récupération du paramètre d'import
 *
 * @param array $param
 * @return void
 */
function _output_($param)
{
    global $output;
    global $output_type;
    global $output_params;

    $output = $param['IMPORTABLE'];
    $output_type = $param['TYPE'];
    $output_params=$param;
}

//Lecture des paramètres d'import

//Récupération du répertoire
$i = 0;
$param_path = "";

if (file_exists("imports/catalog_subst.xml")) {
    $fic_catal = "imports/catalog_subst.xml";
} else {
    $fic_catal = "imports/catalog.xml";
}

_parser_($fic_catal, array("ITEM" => "_item_"), "CATALOG");

//Lecture des paramètres
_parser_(
    "imports/".$param_path."/params.xml",
    array(
        "IMPORTNAME" => "_import_name_",
        "NPERPASS" => "_n_per_pass_",
        "INPUT" => "_input_",
        "STEP" => "_step_",
        "OUTPUT" => "_output_"
    ),
    "PARAMS"
);

//Inclusion des librairies éventuelles
if (is_array($step)) {
    for ($i = 0; $i < count($step); $i ++) {
        if ($step[$i]['TYPE'] == "custom") {
            require_once "imports/".$param_path."/".$step[$i]['SCRIPT'][0]['value'];
        }
    }
}

require_once "xmltransform.php";

//En fonction du type de fichier d'entrée, inclusion du script de gestion des entrées
$input_instance = start_import::get_instance_from_input_type($input_type);

//En fonction du type de fichier de sortie, inclusion du script de gestion des sorties
$output_instance = start_export::get_instance_from_output_type($output_type);

//Si premier accès
if (!isset($first)) {
    $first = '';
}
if (!$first) {
    $origine=str_replace(" ", "", microtime());
    $origine=str_replace("0.", "", $origine);

    //Copie du fichier dans le répertoire temporaire
    try {
        $file_in = fetch_file_in($origine);
    } catch (Exception|InvalidArgumentException $e) {
    	error_message_history($msg["ie_tranfert_error"], $msg["ie_transfert_error_detail"], 1);
        exit;
    }

    //Première notice = 0
    $n_current = 0;

    //Nombre d'erreurs = 0;
    $n_errors = 0;

    //Création du fichier de sortie
    $f = explode(".", $file_in);
    if (count($f) > 1) {
        unset($f[count($f) - 1]);
    }
    $file_out = implode(".", $f).".".$output_params['SUFFIX']."~";

    $fo = fopen("$base_path/temp/".$file_out, "w+");

    //Ouverture du fichier d'origine
    $fi = fopen("$base_path/temp/".$file_in, "r");

    //Récupération du nombre de notices et enregistrement dans la base de données des notices
    if (is_object($input_instance)) {
        $index = $input_instance->_get_n_notices_($fi, "$base_path/temp/".$file_in, $input_params, $origine);
    } elseif (function_exists('_get_n_notices_')) {
        $index = _get_n_notices_($fi, "$base_path/temp/".$file_in, $input_params, $origine);
    }

    if (count($index) == 0) {
        error_message_history($msg["ie_empty_file"], sprintf($msg["ie_empty_file_detail"], $import_type_l), 1);
        exit;
    }

    //Entête
    if (isset($output_params['SCRIPT'])) {
        $class_name = str_replace('.class.php', '', $output_params['SCRIPT']);
    }

    if (is_object($output_instance)) {
        fwrite($fo, $output_instance->_get_header_($output_params));
    } elseif (isset($class_name) && class_exists($class_name)) {
        $import_instance = new $class_name();
        fwrite($fo, $import_instance->_get_header_($output_params));
    } elseif (function_exists('_get_header_')) {
        fwrite($fo, _get_header_($output_params));
    }
    fclose($fo);

    //Vidage de la table de log
    //pmb_mysql_query("delete from error_log where error_origin='convert.log'");
}

function convert_notice($notice, $encoding)
{
    global $step;
    global $param_path;
    global $n_errors;
    global $message_convert;
    global $n_current;
    global $z;

    if (is_array($step)) {
        for ($i = 0; $i < count($step); $i ++) {
            $s = $step[$i];
            //si on a un encodage sur la notice, on le rajoute aux parametres
            if ($encoding) {
                $s['ENCODING'] = $encoding;
            }
            $islast=($i==count($step)-1);
            $isfirst=($i==0);
            switch ($s['TYPE']) {
                case "xmltransform":
                    $r = perform_xslt($notice, $s, $islast, $isfirst, $param_path);
                    break;
                case "toiso":
                    $r = toiso($notice, $s, $islast, $isfirst, $param_path);
                    break;
                case "isotoxml":
                    $r = isotoxml($notice, $s, $islast, $isfirst, $param_path);
                    break;
                case "texttoxml":
                    $r = texttoxml($notice, $s, $islast, $isfirst, $param_path);
                    break;
                case "custom":
                    $static = explode('.', $s['SCRIPT'][0]['value']);
                    if (class_exists($static[0])) {
                        eval("\$r=".$static[0]."::".$s['CALLBACK'][0]['value']."(\$notice, \$s, \$islast, \$isfirst, \$param_path);");
                    } else {
                        eval("\$r=".$s['CALLBACK'][0]['value']."(\$notice, \$s, \$islast, \$isfirst, \$param_path);");
                    }
                    break;
            }
            if (!$r['VALID']) {
                $n_errors ++;
                $message_convert.= "<b>Notice ". ($n_current + $z)." : </b>".$r['ERROR']."<br />\n";
                $notice = "";
                break;
            } else {
                $notice = $r['DATA'];
                if (isset($r['WARNING']) && $r['WARNING']) {
                    $n_errors ++;
                    $message_convert.= "<b>Notice ". ($n_current + $z)." : </b>".$r['WARNING']."<br />\n";
                }
            }
        }
    }
    return $notice;
}

$requete="select count(1) from import_marc where origine='$origine'";
$resultat=pmb_mysql_query($requete);
$n_notices=pmb_mysql_result($resultat, 0, 0);

$percent = @ round(($n_current / $n_notices) * 100);
if ($percent == 0) {
    $percent = 1;
}

echo "<h3>".htmlentities($msg["conversion_en_cours"], ENT_QUOTES, $charset)."</h3><br />";
echo "<table class='' width=100%>
<tr>
    <td style='border-width:1px;border-style:solid;border-color:#FFFFFF;' width=100%>
        <div class='jauge'>
            <img src='".get_url_icon('jauge.png')."' width='".$percent."%' height='16'>
        </div>
    </td>
</tr>
<tr>
    <td>".round($percent)."%</td>
</tr>
</table>";

$processedNotices = sprintf($msg["ie_processed_notices"], $n_current, $n_notices, ($n_notices - $n_current));
echo "<span class='center'>".htmlentities($processedNotices, ENT_QUOTES, $charset)."</span>";

$z = 0;

//Ouverture du fichier final
$f = explode(".", $file_in);
if (count($f) > 1) {
    unset($f[count($f) - 1]);
}
if (empty($output_params['SUFFIX'])) {
    $output_params['SUFFIX'] = '';
}
$file_out = implode(".", $f).".".$output_params['SUFFIX']."~";

$fo = fopen("$base_path/temp/".$file_out, "r+");

//Positionnement a la fin du fichier
fseek($fo, filesize("$base_path/temp/".$file_out));

for ($i = $n_current; $i < $n_current + $n_per_pass; $i ++) {
    $requete = "SELECT notice, encoding
        FROM import_marc
        WHERE no_notice=".($i+1)."
            AND origine='". addslashes($origine) ."'";
    $resultat = pmb_mysql_query($requete);

    if (pmb_mysql_num_rows($resultat) != 0) {
        //Si la notice existe début de conversion
        $obj=pmb_mysql_fetch_object($resultat);
        $notice_ = convert_notice($obj->notice, $obj->encoding);
        @fwrite($fo, $notice_);
        $z ++;
    } else {
        break;
    }
}

//Y-a-t-il eu des erreurs ?
if ($message_convert != "") {
    $requete="INSERT INTO error_log (error_date, error_origin, error_text)
        VALUES(now(), 'convert.log ".addslashes($origine)."', '".addslashes($message_convert)."')";
    pmb_mysql_query($requete);
    echo pmb_mysql_error();
}

//Fin du fichier de notice ?
if ($z < $n_per_pass) {
    $n_current = $n_current + $z;
    if (isset($output_params['SCRIPT'])) {
        $class_name = str_replace('.class.php', '', $output_params['SCRIPT']);
    }

    if (is_object($output_instance)) {
        fwrite($fo, $output_instance->_get_footer_($output_params));
    } elseif (isset($class_name) && class_exists($class_name)) {
        $export_instance = new $class_name();
        fwrite($fo, $export_instance->_get_footer_($output_params));
    } elseif (function_exists('_get_footer_')) {
        fwrite($fo, _get_footer_($output_params));
    }
    fclose($fo);

    $requete="DELETE FROM import_marc WHERE origine='". addslashes($origine) ."'";
    pmb_mysql_query($requete);
    if ($redirect) {
        $redirect .= "&file_in=".rawurlencode($file_in)."&suffix=".$output_params['SUFFIX']."&origine=".$origine."&import_type=$import_type&outputtype=".$output_params["TYPE"]."&converted=1";
        $location = "parent.document.location='".$redirect."'";
    } else {
        if (!isset($output_params['MIMETYPE'])) {
            $output_params['MIMETYPE'] = '';
        }
        if (!isset($index)) {
            $index = array();
        }
        $location = "document.location='end_import.php?file_in=".rawurlencode($file_in)."&first=1&n_current=".$n_current."&import_type=".$import_type."&n_total=".count($index)."&n_errors=$n_errors&output=$output&suffix=".$output_params['SUFFIX']."&import_type_l=".rawurlencode($import_type_l)."&noimport=$noimport".$redirect_string."&mimetype=".rawurlencode($output_params['MIMETYPE'])."&origine=".$origine."'";
    }
    echo "<script>setTimeout(\"$location\",1000);</script>";
} else {
    //Si pas fin, recharegement de la page pour les $n_per_pass_suivants
    $n_current = $n_current + $n_per_pass;
    fclose($fo);

    if ($redirect) {
        $redirect_string = "&redirect=".urlencode($redirect);
    } else {
        $redirect_string = "";
    }

    echo "<script>setTimeout(\"document.location='start_import.php?file_in=".rawurlencode($file_in)."&first=1&n_current=".$n_current."&import_type=".$import_type."&n_errors=$n_errors&noimport=$noimport".$redirect_string."&origine=".$origine."'\",1000);</script>";
}
