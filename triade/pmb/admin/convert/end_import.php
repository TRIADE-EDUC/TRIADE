<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: end_import.php,v 1.20.14.1.2.1 2025/04/16 12:16:50 dbellamy Exp $

//Fin de la conversion
$base_path = "../..";
$base_auth = "ADMINISTRATION_AUTH|CATALOGAGE_AUTH";
$base_title = "\$msg[ie_import_running]";
require ($base_path."/includes/init.inc.php");

global $class_path, $include_path, $msg, $charset, $n_errors;
global $admin_convert_end, $pmb_import_modele;

require_once($class_path."/import/import_entities.class.php");
require_once ($class_path."/import/import_records.class.php");
require_once ($class_path."/import/import_expl.class.php");

$percent=100;
echo "<h3>$msg[admin_conversion_end1] $import_type_l $msg[admin_conversion_end2].</h3><br />\n";

print "
<table class='center' width=100%>
   <tr>
      <td style='border-width:1px;border-style:solid;border-color:#000;'>
	  	<div class='jauge'><img src='".get_url_icon('jauge.png')."' width='".$percent."%' height='16'></div>
      </td></tr>
   <tr>
      <td class='center'>".round($percent)."%</td>
      </tr>
</table>";

print $n_current." ".$msg["admin_conversion_end3"];

$n_errors = intval($n_errors);
if ($n_errors!=0) {
    print ", ".$n_errors." ".$msg["admin_conversion_end4"];
    $requete="select error_text from error_log where error_origin='convert.log ".$origine."'";
    $resultat=pmb_mysql_query($requete);
    $errors_msg = '';
	while (list($err_)=pmb_mysql_fetch_row($resultat)) {
        $errors_msg.=$err_;
    }
}

require ($include_path."/templates/admin.tpl.php");

if (preg_match("#<!--select_func_import-->#",$admin_convert_end)) {
	//Ajout du selecteur de choix de fonction d'import
	if(file_exists($base_path."/admin/import/func_import_subst.xml")){
    	$table_list_func_import=_parser_text_no_function_(file_get_contents($base_path."/admin/import/func_import_subst.xml"),"CATALOG");
    }elseif(file_exists($base_path."/admin/import/func_import.xml")){
    	$table_list_func_import=_parser_text_no_function_(file_get_contents($base_path."/admin/import/func_import.xml"),"CATALOG");
	}
    $code_js="";
   	if(is_array($table_list_func_import["ITEM"]) && count($table_list_func_import["ITEM"])){
   		if(!empty($_SESSION["func_import_model"])){
			$func_import_model=$_SESSION["func_import_model"];
		}elseif(!$pmb_import_modele) {
			$func_import_model="func_bdp.inc.php";
		} else {
			$func_import_model=$pmb_import_modele;
		}
   		$incr=0;
   		$text_desc_func_import="";
   		$name_func="func_import";
   		$code_js.="<script type=\"text/javascript\">\n";
   		$code_js.="var func_import_desc= new Array(); var func_import_value= new Array();\n";
   		$selecteur_fic="<label class=\"etiquette\" for=\"".$name_func."\">".htmlentities($msg["admin_import_notice_convert_choice"],ENT_QUOTES,$charset)."</label>\n";
   		$selecteur_fic.="<select name=\"".$name_func."\" id=\"".$name_func."\" onChange=\"affiche_description();\" >\n";
   		$selected_trouve="";
   		foreach ( $table_list_func_import["ITEM"] as $value ) {
   			$code_js.="func_import_desc[$incr] = \"".htmlentities($value["DESCRIPTION"],ENT_QUOTES,$charset)."\";\n";
   			$code_js.="func_import_value[$incr] = \"".htmlentities(substr($value["FUNCTION"],0,-4),ENT_QUOTES,$charset)."\";\n";

   			$selecteur_fic.="<option value=\"".htmlentities(substr($value["FUNCTION"],0,-4),ENT_QUOTES,$charset)."\" ";
   			if($func_import_model == $value["FUNCTION"]){
   				$selecteur_fic.="selected=\"selected\" ";
   				$selected_trouve=$value["FUNCTION"];
   				$text_desc_func_import=$value["DESCRIPTION"];
   			}
   			$selecteur_fic.=">".htmlentities($value["NAME"],ENT_QUOTES,$charset)."</option>\n";
   			$incr++;
		}
		if(!$selected_trouve || ($pmb_import_modele && $selected_trouve != $pmb_import_modele)){
			$code_js.="func_import_desc[$incr] = \"\";\n";
   			$code_js.="func_import_value[$incr] = \"".htmlentities(substr($pmb_import_modele,0,-4),ENT_QUOTES,$charset)."\";\n";
			$selecteur_fic.="<option value=\"".htmlentities(substr($pmb_import_modele,0,-4),ENT_QUOTES,$charset)."\" ";
			if(!$selected_trouve){
				$selecteur_fic.="selected=\"selected\" ";
			}

			$selecteur_fic.=">".htmlentities($msg["admin_import_notice_defaut"],ENT_QUOTES,$charset)."</option>\n";
		}
   		$selecteur_fic.="</select>&nbsp;&nbsp;&nbsp;\n";

   		$code_js.="function affiche_description(){
        	var func_import=document.destfic.".$name_func.";
			var mon_select=false;
			var index_select = func_import.options[func_import.selectedIndex].value;
			//console.log(document.getElementById('text_desc_func_import').innerHTML);
			for (var i=0; i<func_import_value.length;i++){
				if(func_import_value[i] == index_select){
					document.getElementById('text_desc_func_import').innerHTML =func_import_desc[i];
				}
			}
        }";
        $code_js.="</script>";
        $formulaire=$selecteur_fic;
   		$formulaire.="<label class=\"etiquette\" for=\"".$name_func."\" id=\"text_desc_func_import\" name=\"text_desc_func_import\">".htmlentities($text_desc_func_import,ENT_QUOTES,$charset)."</label>\n";
    	$formulaire.=$code_js;
		$admin_convert_end=str_replace("<!--select_func_import-->",$formulaire,$admin_convert_end);
   	}

}

print $admin_convert_end;


