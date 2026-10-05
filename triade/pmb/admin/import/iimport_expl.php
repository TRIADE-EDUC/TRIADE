<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: iimport_expl.php,v 1.87.2.3 2024/12/17 13:50:17 dgoron Exp $

global $base_auth, $base_title, $include_path, $class_path, $pmb_indexation_lang, $file_submit, $isbn_mandatory;

// définition du minimum necessaire
$base_path="../..";
$base_auth = "ADMINISTRATION_AUTH";
$base_title = "";
require_once ("$base_path/includes/init.inc.php");

// les requis par iimport_expl.php ou ses sous modules
require_once ("$include_path/isbn.inc.php");
require_once ("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
require_once ("$class_path/iso2709.class.php");

require_once($class_path."/import/import_entities.class.php");
require_once ($class_path."/import/import_records.class.php");
require_once ($class_path."/import/import_expl.class.php");

require_once ("$class_path/author.class.php");
require_once ("$class_path/serie.class.php");
require_once ("$class_path/editor.class.php");
require_once ("$class_path/collection.class.php");
require_once ("$class_path/subcollection.class.php");
require_once ("$class_path/expl.class.php");
require_once ("$class_path/lender.class.php");
require_once ("$class_path/docs_type.class.php");
require_once ("$class_path/docs_section.class.php");
require_once ("$class_path/docs_statut.class.php");
require_once ("$class_path/docs_location.class.php");
require_once ("$class_path/docs_codestat.class.php");
require_once ("$class_path/indexint.class.php");
require_once ("$class_path/origine_notice.class.php");
require_once ("$class_path/marc_table.class.php");
require_once ("$class_path/lender.class.php");
require_once ("$class_path/notice.class.php");
require_once ("$class_path/titre_uniforme.class.php");
require_once($class_path."/origin.class.php");
require_once("$include_path/parser.inc.php");

require_once("import_func.inc.php");

global $msg, $charset, $sub, $action, $lang, $pmb_import_modele, $encodage_fic_source;
global $current_module;
global $deflt_docs_location, $book_lender_id;
global $nb_expl_ignores, $notice_deja_presente, $notice_remplacee, $isbn_dedoublonnage;
global $tit, $tit_200a, $isbn, $EAN, $issn_011, $collection_225, $collection_410;
global $import_notice_existing_replace, $import_explajtNOTI, $import_recordsajtNOTI, $add_explnum;
global $import_expl_caddie_NOTI, $import_records_caddie_NOTI;

global $import_without_index;
$import_without_index = filter_var($import_without_index, FILTER_VALIDATE_BOOLEAN);

$name_func="func_import";

if(isset(${$name_func}) && ${$name_func}){
	${$name_func}.=".php";
}

//J'efface la fonction d'import de la session au début de l'import
if(((!isset($file_submit) || !$file_submit) && $action=="preload" && !${$name_func}) || ($action=="beforeupload" && !${$name_func})){
	$_SESSION["func_import_model"]="";
}

//Controle que la fonction d'import existe
$trouve=false;
$table_list_func_import=array();
if(isset(${$name_func}) && ${$name_func}){
	if(${$name_func} == $pmb_import_modele){
		$trouve=true;
	}else{
		if(file_exists("func_import_subst.xml")){
        	$table_list_func_import=_parser_text_no_function_(file_get_contents("func_import_subst.xml"),"CATALOG");
        }elseif(file_exists("func_import.xml")){
        	$table_list_func_import=_parser_text_no_function_(file_get_contents("func_import.xml"),"CATALOG");
        }
       	if(is_array($table_list_func_import["ITEM"]) && count($table_list_func_import["ITEM"])){
       		foreach ( $table_list_func_import["ITEM"] as $value ) {
       			if($value["FUNCTION"] == ${$name_func}){
       				$trouve=true;
       				break;
       			}
       		}
       	}
	}
}

$func_import_model="";
if(!$trouve){
	if(isset($_SESSION["func_import_model"]) && $_SESSION["func_import_model"]){
		$func_import_model=$_SESSION["func_import_model"];
	}elseif(!$pmb_import_modele) {
		$func_import_model="func_bdp.inc.php";
	} else {
		$func_import_model=$pmb_import_modele;
	}
}else{
	$func_import_model=${$name_func};
	$_SESSION["func_import_model"]=$func_import_model;//Je garde la fonction d'import sélectionnée jusqu'à la fin
}
if (file_exists($func_import_model)) {
	require_once($func_import_model);
} else {
	error_message("", sprintf($msg["admin_error_file_import_modele"],$func_import_model), 1, "./admin.php?categ=param");
	exit;
}

//Gestion de l'encodage du fichier d'import
if(isset($encodage_fic_source)){
	$_SESSION["encodage_fic_source"]=$encodage_fic_source;
}elseif(isset($_SESSION["encodage_fic_source"])){
	$encodage_fic_source=$_SESSION["encodage_fic_source"];
}

print "<div id='contenu-frame'>" ;

$nom_fichier_transfert_ftp = "unimarc".(defined("LOCATION")?"_".constant("LOCATION"):"").".fic";

$tmp_file = (isset($_FILES['userfile']['tmp_name']) ? $_FILES['userfile']['tmp_name'] : '');
if (!isset($from_file)) $from_file = (isset($_FILES['userfile']['name']) ? $_FILES['userfile']['name'] : '');
$to_file = $base_path.'/temp/'.basename($tmp_file);

if ($sub == "import_expl") {
	echo window_title($msg[520].$msg[1003].$msg[1001]);
	/* the name of the lender is read in the table */
	if ($book_lender_id!="") {
		$sql_rech="select lender_libelle from lenders where idlender = '".$book_lender_id."' ";
		$sql_result_rech = pmb_mysql_query($sql_rech) or die ("Couldn't select lenders ! = ".$sql_rech);
		if (pmb_mysql_num_rows($sql_result_rech)==0) {
			$book_lender_name = $msg[561];
		} else {
			$book_lender_name = pmb_mysql_result($sql_result_rech,0,"lender_libelle");
		}
	} else {
		$book_lender_name = $msg[561];
	}
} else {
	echo window_title($msg[500].$msg[1003].$msg[1001]);
}

switch ($action) {
	case 'beforeupload':
		if ($sub == "import_expl") {
			$book_lender_id = "";
			$book_statut_id = "";
			print $tpl_beforeupload_expl ;
		} else {
			// import de notice
			 print $tpl_beforeupload_notices ;
		}
	break;
	case 'afterupload':
		if (!$statutnot) $statutnot = 1 ;
		if(!isset($que_faire)) $que_faire = '';
		if(!isset($isbn_only)) $isbn_only = '';
		if ($sub == "import_expl") {
			if ($book_lender_id==0 || $book_lender_id=="") {
				print $msg[561];
				break;
			}
			if ($book_statut_id==0 || $book_statut_id=="") {
				print $msg[561];
				break;
			}
			if ($tmp_file=="") {
				printf ($msg[503],$from_file); /* wrong permissions to copy the file %s ... Contact your admin... */
				break;
			}
			if (!move_uploaded_file($tmp_file,$to_file)) {
				printf ($msg[504],$from_file); /* Fail to copy %s, Contact your admin... */
			} else {
				printf ($msg[505],$from_file); /* File transfered, Loading is about to go on */
				print import_expl::get_hidden_form('afterupload', 'preload');
				print "<SCRIPT>setTimeout(\"document.afterupload.submit()\",2000);</SCRIPT>";
			}
		} else {
			// import de notice
			if ($to_file=="") {
			    printf ($msg[503],$from_file); /* wrong permissions to copy the file %s ... Contact your admin... */
			    break;
			}
 			if (!move_uploaded_file($tmp_file,$to_file)) {
			    printf ($msg[504],$from_file); /* Fail to copy %s, Contact your admin... */
			} else {
				printf ($msg[505],$from_file); /* File transfered, Loading is about to go on */
				print import_records::get_hidden_form('afterupload', 'preload');
				print "<SCRIPT>setTimeout(\"document.afterupload.submit()\",2000);</SCRIPT>";
			}
		}
		break;
	case 'preload':
		if ($sub == "import_expl") {
			/* Does the file exist ? */
			if ($file_submit=="") {
				/* l'utilisateur n'est pas passé par le téléchargement du fichier */
				$filename = $base_path."/admin/import/".$nom_fichier_transfert_ftp;
				$from_file = "unimarc".(defined("LOCATION")?"_".constant("LOCATION"):"").".fic";
			} else {
				$filename=$file_submit;
			}
			if ($book_lender_id=="" || $book_statut_id=="" || $isbn_mandatory=="" || $cote_mandatory=="" || $book_location_id=="" || $statutnot=="") {
				/* l'utilisateur n'est pas passé par le téléchargement du fichier, il faut qu'il choisisse un prêteur s'il n'en a pas communiqué auparavant */
				print "
					<form class='form-$current_module' NAME=\"preload\" METHOD=\"post\" ACTION=\"iimport_expl.php\">
						<h3>".$msg['import_expl_form_titre']."</h3>
						<div class='form-contenu'>
						<div class='row'>
							<div class='colonne2'>
								<label class='etiquette' for='isbn_obligatoire'>$msg[564]</label><br />
								<INPUT TYPE='radio' NAME='isbn_mandatory' id='io1' VALUE='1' CLASS='radio' /><label for='io1'> $msg[40] </label>
								<INPUT TYPE='radio' NAME='isbn_mandatory' id='io0' VALUE='0' CLASS='radio' checked='checked' /><label for='io0'> $msg[39] </label>
							</div>
							<div class='colonne-suite'>
								<label class='etiquette' for='isbn_dedoublonnage'>$msg[568]</label><br />
								<INPUT TYPE='radio' NAME='isbn_dedoublonnage' id='di1' VALUE='1' CLASS='radio' checked='checked' /><label for='di1'> $msg[40] </label>
								<INPUT TYPE='radio' NAME='isbn_dedoublonnage' id='di0' VALUE='0' CLASS='radio' /><label for='di0'> $msg[39] </label>
								<input type='checkbox' name='isbn_only' id='ionly' value='1'/><label for='ionly'> ".$msg["ignore_issn"]." </label>
							</div>
						</div>
						<div class='row'>&nbsp;</div>
						<div class='row'>
	                        <div class='colonne2'>
	                    		<label class='etiquette' for='statutnot'>$msg[import_statutnot]</label>
	                    		<div>
	                    		".gen_liste_multiple ("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "statutnot", "", 1, "", "","","",0)."
	                    		</div>
	                    	</div>
	                    	<div class='colonne-suite'>
	                    		<label class='etiquette' for='generer_lien'>".$msg['import_genere_liens']."</label><br />
	                    		<INPUT TYPE='radio' NAME='link_generate' id='link1' VALUE=' 1' CLASS='radio' onclick='param_links_display();' /><label for='link1'> $msg[40] </label>
	                            <INPUT TYPE='radio' NAME='link_generate' id='link0' VALUE='0' CLASS='radio' onclick='param_links_display();' checked='checked' /><label for='link0'> $msg[39] </label>
	                            <span id='list_param_links' style='display: none;'>
	                            	<div style='clear: both; margin-left: 50%;'>
	                            		<label class='etiquette' for='notice_replace_links'>".$msg['notice_replace_links_option_keep_title']."</label>
	                            		<br /><input type='radio' name='notice_replace_links' value='0' ".($deflt_notice_replace_links==0?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_all']."
										<br /><input type='radio' name='notice_replace_links' value='1' ".($deflt_notice_replace_links==1?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replacing']."
										<br /><input type='radio' name='notice_replace_links' value='2' ".($deflt_notice_replace_links==2?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replaced']."
									</div>
	                            </span>
	                    	</div>
                    	</div>
                    	<div class='row'>&nbsp;</div>
	                   	<div class='row'>
	                   		<div class='colonne2'>
	                   			<label class='etiquette' for='import_force_notice_is_new'>".$msg['import_force_notice_is_new']."</label>
	                    		<div>
	                    			<input type='radio' name='import_force_notice_is_new' id='import_force_notice_is_new' value='0' checked='checked'> ".$msg['39']."
	                    			<input type='radio' name='import_force_notice_is_new' id='import_force_notice_is_new' value='1'> ".$msg['40']."
	                    		</div>
	                   		</div>
	                   		<div class='colonne-suite'>
								<label class='etiquette' for='authorities_notices'>".htmlentities($msg['import_with_authorities_notices'],ENT_QUOTES,$charset)."</label><br />
								<input type='radio' name='authorities_notices' id='authorities_notices1' value='1' class='radio' /><label for='authorities_notices1'>".htmlentities($msg[40],ENT_QUOTES,$charset)."</label>
								<input type='radio' name='authorities_notices' id='authorities_notices0' value='0' class='radio' checked='checked'/><label for='authorities_notices0'>".htmlentities($msg[39],ENT_QUOTES,$charset)."</label>
	                    	</div>
	                   	</div>
	                   	<div class='row'>&nbsp;</div>
                   		<div class='row'>
                   			<div class='colonne2'>
	                   			<label class='etiquette' for='import_notice_existing_replace'>".$msg['import_notice_existing_replace']."</label>
	                    		<div>
	                    			<input type='radio' name='import_notice_existing_replace' id='import_notice_existing_replace0' value='0' checked='checked' onclick='param_existing_replace_display();'> <label for='import_notice_existing_replace0'>".$msg['39']."</label>
	                    			<input type='radio' name='import_notice_existing_replace' id='import_notice_existing_replace1' value='1' onclick='param_existing_replace_display();'> <label for='import_notice_existing_replace1'>".$msg['40']."</label>
	                    		</div>
	                    		<div id='import_notice_existing_replace_message' class='warning' style='display: none;'>
	                    			".htmlentities($msg['import_notice_existing_replace_message'], ENT_QUOTES, $charset)."
	                    		</div>
	                   		</div>
                   			<div class='colonne-suite'>
                   				<label class='etiquette' for='authorities_default_origin'>".htmlentities($msg['import_authorities_origin_default_value'],ENT_QUOTES,$charset)."</label><br />
								".origin::gen_combo_box("authorities","authorities_default_origin")."
                   			</div>
                   		</div>
                   		<div class='row'>&nbsp;</div>
						<div class='row'><hr /></div>
						<div class='row'>
                            <label class='etiquette' for='prêteur statut'>$msg[560]</label>
                            </div>
                        <div class='row'>".
                            lender::gen_combo_box($book_lender_id)."&nbsp;&nbsp;".
                            docs_statut::gen_combo_box($book_statut_id)."
                            </div>
	                    <div class='row'>
    	                    <label class='etiquette' for='localisation'>$msg[import_localisation]</label>
        	                </div>
            	        <div class='row'>".
                	        docs_location::gen_combo_box($deflt_docs_location)."
                    	    </div>
                        <div class='row'><hr /></div>
                    	<div class='row'>
                            <label class='etiquette' for='cote_obligatoire'>$msg[566]</label>
                            </div>
                        <div class='row'>
                            <INPUT TYPE='radio' NAME='cote_mandatory' VALUE='1' CLASS='radio' /> $msg[40]
                            <INPUT TYPE='radio' NAME='cote_mandatory' VALUE='0' CLASS='radio' checked='checked' /> $msg[39]
                            </div>
                        <div class='row'><hr /></div>
                        <div class='row'>
                            <label class='etiquette'>$msg[17]</label>
                            </div>
                        <div class='row'>
                            <INPUT TYPE='radio' NAME='tdoc_codage' VALUE='1' CLASS='radio' /> ".$msg["import_expl_codage_proprio"]."
                            <INPUT TYPE='radio' NAME='tdoc_codage' VALUE='0' CLASS='radio' checked='checked' /> ".$msg["import_expl_codage_generique"]."
                            </div>
                        <div class='row'>
                            <label class='etiquette'>$msg[24]</label>
                            </div>
                        <div class='row'>
                            <INPUT TYPE='radio' NAME='statisdoc_codage' VALUE='1' CLASS='radio' /> ".$msg["import_expl_codage_proprio"]."
                            <INPUT TYPE='radio' NAME='statisdoc_codage' VALUE='0' CLASS='radio' checked='checked' /> ".$msg["import_expl_codage_generique"]."
                            </div>
                        <div class='row'>
                            <label class='etiquette'>$msg[19]</label>
                            </div>
                        <div class='row'>
                            <INPUT TYPE='radio' NAME='sdoc_codage' VALUE='1' CLASS='radio' /> ".$msg["import_expl_codage_proprio"]."
                            <INPUT TYPE='radio' NAME='sdoc_codage' VALUE='0' CLASS='radio' checked='checked' /> ".$msg["import_expl_codage_generique"]."
                            </div>
                        <div class='row'>
                            <label class='etiquette'>$msg[21]</label>
                            </div>
                        </div>
                        <hr />
                        <div class='row'>
                            <input type='checkbox' name='import_without_index' id='import_without_index' value='1' /><label for='import_without_index' class='etiquette' >".htmlentities($msg['import_without_index'],ENT_QUOTES,$charset)."</label> 
                        </div>
					".import_expl::get_advanced_form()."
                    <INPUT TYPE=\"SUBMIT\"  class='bouton' NAME=\"upload\" VALUE=\"".$msg[502]."\" />
                    <INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"import\" />
                    <INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import_expl\" />
                    <INPUT NAME=\"action\" TYPE=\"hidden\" value=\"preload\" />
                    <INPUT NAME=\"".$name_func."\" TYPE=\"hidden\" value=\"".${$name_func}."\"/>
                    </FORM>
                    <script type='text/javascript'>
						function param_links_display(){
							if(document.getElementById('link1').checked){
								document.getElementById('list_param_links').style.display='';
							} else {
								document.getElementById('list_param_links').style.display='none';
							}
						}
                    	function param_existing_replace_display(){
							if(document.getElementById('import_notice_existing_replace1').checked){
								document.getElementById('import_notice_existing_replace_message').style.display='';
							} else {
								document.getElementById('import_notice_existing_replace_message').style.display='none';
							}
						}
					</script>";
                break;
			}
            loadfile_in_table() ;
            if ($pb_fini=="EOF") {
				$formulaire = import_expl::get_hidden_form('load', 'load');
                printf ($msg[512], $from_file); /* File %s... . End of preload... */

                $fo = fopen("$base_path/temp/liste_id".SESSid.".txt","w");
				fwrite($fo,"");
				fclose($fo);
				//file_put_contents("$base_path/temp/liste_id".SESSid.".txt","");
                $script = "<script>setTimeout(\"document.load.submit()\",2000);</script>";
			} else {
				$formulaire = import_expl::get_hidden_form('preload', 'preload');
				$script = "<script>setTimeout(\"document.preload.submit()\",2000);</script>";
			}
            print $formulaire;
            print $script;
		} else {
		    // import de notice
		    /* Does the file exist ? */
		    if ($file_submit=="") {
		    	$filename = $base_path."/admin/import/".$nom_fichier_transfert_ftp;
		    	$from_file = "unimarc".(defined("LOCATION")?"_".constant("LOCATION"):"").".fic";
		    } else {
		    	$filename=$file_submit;
		    }

		    if ($isbn_mandatory=="") {
		        /* l'utilisateur n'est pas passé par le téléchargement du fichier, il faut qu'il nous dise si l'ISBN est obligatoire */
		        print "
		            <form class='form-$current_module' NAME=\"preload\" METHOD=\"post\" ACTION=\"iimport_expl.php\">
		            <h3>".$msg['import_noti_form_titre']."</h3>
		            <div class='form-contenu'>
		        	<div class='row'>
		        	    <div class='colonne2'>
		    	            <label class='etiquette' for='isbn_obligatoire'>$msg[564]</label><br />
			                <INPUT TYPE='radio' NAME='isbn_mandatory' id='io1' VALUE='1' CLASS='radio' /><label for='io1'> $msg[40] </label>
		                    <INPUT TYPE='radio' NAME='isbn_mandatory' id='io0' VALUE='0' CLASS='radio' checked='checked' /><label for='io0'> $msg[39] </label>
		                </div>
		                <div class='colonne-suite'>
		            	    <label class='etiquette' for='isbn_dedoublonnage'>$msg[568]</label><br />
		        	        <INPUT TYPE='radio' NAME='isbn_dedoublonnage' id='di1' VALUE='1' CLASS='radio' checked='checked' /><label for='di1'> $msg[40] </label>
		    	            <INPUT TYPE='radio' NAME='isbn_dedoublonnage' id='di0' VALUE='0' CLASS='radio' /><label for='di0'> $msg[39] </label>
			                <input type='checkbox' name='isbn_only' id='ionly' value='1' /><label for='ionly'> ".$msg["ignore_issn"]." </label>
		                </div>
		             </div>
					<div class='row'>&nbsp;</div>
		            <div class='row'>
                        <div class='colonne2'>
                    		<label class='etiquette' for='statutnot'>$msg[import_statutnot]</label>
                    		<div>
                    		".gen_liste_multiple ("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "statutnot", "", 1, "", "","","",0)."
                    		</div>
                    	</div>
                    	<div class='colonne-suite'>
	                    	<label class='etiquette' for='generer_lien'>".$msg['import_genere_liens']."</label><br />
	                    	<INPUT TYPE='radio' NAME='link_generate' id='link1' VALUE=' 1' CLASS='radio' onclick='param_links_display();' /><label for='link1'> $msg[40] </label>
	                        <INPUT TYPE='radio' NAME='link_generate' id='link0' VALUE='0' CLASS='radio' onclick='param_links_display();' checked='checked' /><label for='link0'> $msg[39] </label>
                            <span id='list_param_links' style='display: none;'>
                            	<div style='clear: both; margin-left: 50%;'>
	                            	<label class='etiquette' for='notice_replace_links'>".$msg['notice_replace_links_option_keep_title']."</label>
                            		<br /><input type='radio' name='notice_replace_links' value='0' ".($deflt_notice_replace_links==0?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_all']."
									<br /><input type='radio' name='notice_replace_links' value='1' ".($deflt_notice_replace_links==1?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replacing']."
									<br /><input type='radio' name='notice_replace_links' value='2' ".($deflt_notice_replace_links==2?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replaced']."
								</div>
                            </span>
                    	</div>
                    </div>
                    <div class='row'>&nbsp;</div>
                   	<div class='row'>
                   		<div class='colonne2'>
                   			<label class='etiquette' for='import_force_notice_is_new'>".$msg['import_force_notice_is_new']."</label>
                    		<div>
                    			<input type='radio' name='import_force_notice_is_new' id='import_force_notice_is_new' value='0' checked='checked'> ".$msg['39']." <input type='radio' name='import_force_notice_is_new' id='import_force_notice_is_new' value='1'> ".$msg['40']."
                    		</div>
                   		</div>
                   		<div class='colonne-suite'>
							<label class='etiquette' for='authorities_notices'>".htmlentities($msg['import_with_authorities_notices'],ENT_QUOTES,$charset)."</label><br />
							<input type='radio' name='authorities_notices' id='authorities_notices1' value='1' class='radio' /><label for='authorities_notices1'>".htmlentities($msg[40],ENT_QUOTES,$charset)."</label>
							<input type='radio' name='authorities_notices' id='authorities_notices0' value='0' class='radio' checked='checked'/><label for='authorities_notices0'>".htmlentities($msg[39],ENT_QUOTES,$charset)."</label>
                    	</div>
                   	</div>
                   	<div class='row'>&nbsp;</div>
                   	<div class='row'>
                   		<div class='colonne2'>
                   			<label class='etiquette' for='import_notice_existing_replace'>".$msg['import_notice_existing_replace']."</label>
                    		<div>
                    			<input type='radio' name='import_notice_existing_replace' id='import_notice_existing_replace0' value='0' checked='checked' onclick='param_existing_replace_display();'> <label for='import_notice_existing_replace0'>".$msg['39']."</label>
                    			<input type='radio' name='import_notice_existing_replace' id='import_notice_existing_replace1' value='1' onclick='param_existing_replace_display();'> <label for='import_notice_existing_replace0'>".$msg['40']."</label>
                    		</div>
                    		<div id='import_notice_existing_replace_message' class='warning' style='display: none;'>
                    			".htmlentities($msg['import_notice_existing_replace_message'], ENT_QUOTES, $charset)."
                    		</div>
                   		</div>
                   		<div class='colonne-suite'>
                   			<label class='etiquette' for='authorities_default_origin'>".htmlentities($msg['import_authorities_origin_default_value'],ENT_QUOTES,$charset)."</label><br />
							".origin::gen_combo_box("authorities","authorities_default_origin")."
                   		</div>
                   	</div>
                   	<div class='row'>&nbsp;</div>
                    <div class='row'>
		            	<INPUT TYPE=\"SUBMIT\"  class='bouton' NAME=\"upload\" VALUE=\"".$msg[502]."\" />
		            </div>
		            <INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"import\" />
		            <INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import\" />
		            <INPUT NAME=\"action\" TYPE=\"hidden\" value=\"preload\" />
		            <INPUT NAME=\"".$name_func."\" TYPE=\"hidden\" value=\"".${$name_func}."\"/>
		            </FORM>
                    <script type='text/javascript'>
						function param_links_display(){
							if(document.getElementById('link1').checked){
								document.getElementById('list_param_links').style.display='';
							} else {
								document.getElementById('list_param_links').style.display='none';
							}
						}
		            	function param_existing_replace_display(){
							if(document.getElementById('import_notice_existing_replace1').checked){
								document.getElementById('import_notice_existing_replace_message').style.display='';
							} else {
								document.getElementById('import_notice_existing_replace_message').style.display='none';
							}
						}
					</script>";
		        break;
		    }
		    loadfile_in_table() ;
			if ($pb_fini=="EOF") {
				$formulaire = import_records::get_hidden_form('load', 'load');
				printf ($msg[509].$msg[512], $from_file, $from_file); /* File %s... . End of preload... */

				$fo = fopen("$base_path/temp/liste_id".SESSid.".txt","w");
				fwrite($fo,"");
				fclose($fo);
				//file_put_contents("$base_path/temp/liste_id".SESSid.".txt","");
				$script = "<script>setTimeout(\"document.load.submit()\",2000);</script>";
			} else {
				$formulaire = import_records::get_hidden_form('preload', 'preload');
				$script = "<script>setTimeout(\"document.preload.submit()\",2000);</script>";
			}
		    print $formulaire;
		    print $script;
		}
        break;
    case 'load':
		if (!$statutnot) $statutnot=1;
		printf ($msg[509], $from_file);
		if (!isset($nbtot_notice) || $nbtot_notice=="") {
			$sql = "select count(1) from import_marc where origine='".addslashes(SESSid)."' ";
			$sql_result = pmb_mysql_query($sql) or die ("Couldn't select count(1) from import table !");
			$nbtot_notice=pmb_mysql_result($sql_result, 0, 0);
       	}
		if ($sub == "import_expl") {
			$section_995_=new marc_list("section_995");
			$section_995=$section_995_->table;
			$typdoc_995_=new marc_list("typdoc_995");
			$typdoc_995=$typdoc_995_->table;
			$codstatdoc_995_=new marc_list("codstatdoc_995");
			$codstatdoc_995=$codstatdoc_995_->table;
			/* let's initialize the counters if necessary */
			if ($nb_expl_ignores == "") $nb_expl_ignores=0;
			printf ($msg[511], "\"".$book_lender_name."\"") ;
		}

        $sql = "select notice, id_import from import_marc where origine='".addslashes(SESSid)."' ORDER BY id_import limit $pmb_import_limit_record_load ";
        $sql_result_import = pmb_mysql_query($sql) or die ("Couldn't select import table !");
        $n_notice=pmb_mysql_num_rows($sql_result_import);
        if (!isset($notice_deja_presente) || $notice_deja_presente=="") {
        	$notice_deja_presente=0;
        }
        if (!isset($notice_remplacee) || $notice_remplacee=="") {
        	$notice_remplacee=0;
        }
        $inotice=0;
        $notice_rejetee=0;
        $indexation_stack_number=0;
        $txt="";

		while (($notobj = pmb_mysql_fetch_object($sql_result_import))) {
            $notice=$notobj->notice ;
            $idnotice_import=$notobj->id_import ;
            $inotice++;

            $res_lecture = recup_noticeunimarc($notice) ;
            if($link_generate) $res_link = recup_noticeunimarc_link($notice);
			if (!$res_lecture || empty($tit_200a[0])) {
                $res_lecture = 0;
                // ".$inotice."
                $fp = fopen ("../../temp/err_import.unimarc","a+");
                fwrite ($fp, $notice);
                fclose ($fp);
                $notice_rejetee++;
			}

			if ($res_lecture) {
                recup_noticeunimarc_suite($notice) ;

                /* We've got everything, let's have a look if ISBN already exists in notices table */
                if(!isset($isbn[0])) $isbn[0] = '';
                if($isbn[0]=="NULL") $isbn[0]="";
                // si isbn vide, on va tenter de prendre l'EAN stocké en 345$b
                if ($isbn[0]=="") $isbn[0] = (isset($EAN[0]) ? $EAN[0] : '');
                // si isbn vide, on va tenter de prendre le serial en 011
                if ($isbn[0]=="") $isbn[0] = (isset($issn_011[0]) ? $issn_011[0] : '');
                // si ISBN obligatoire et isbn toujours vide :
                if ($isbn_mandatory == 1 && $isbn[0]=="") {
                    // on va tenter de prendre l'ISSN stocké en 225$x
                    $isbn[0]=$collection_225[0]['x'] ;
                    // si isbn toujours vide, on va tenter de prendre l'ISSN stocké en 410$x
                    if ($isbn[0]=="") $isbn[0]=$collection_410[0]['x'] ;
                }

				// on commence par voir ce que le code est (basé sur la recherche par code du module catalogage
				$ex_query = clean_string($isbn[0]);

				$EAN = '';
				$isbn = '';
				$code = '';
				$code10 = '' ;

				if(isEAN($ex_query)) {
					// la saisie est un EAN -> on tente de le formater en ISBN
					$EAN=$ex_query;
					$isbn = EANtoISBN($ex_query);
					// si échec, on prend l'EAN comme il vient
					if(!$isbn)
						$code = str_replace("*","%",$ex_query);
					else {
						$code=$isbn;
						$code10=formatISBN($code,10);
					}
				} else {
					if(isISBN($ex_query)) {
						// si la saisie est un ISBN
						$isbn = formatISBN($ex_query);
						// si échec, ISBN erroné on le prend sous cette forme
						if(!$isbn)
							$code = str_replace("*","%",$ex_query);
						else {
							$code10=$isbn ;
							$code=formatISBN($code10,13);
						}
					} else {
						// ce n'est rien de tout ça, on prend la saisie telle quelle
						$code = str_replace("*","%",$ex_query);
					}
				}

				$isbn_OK=$code;
                $new_notice = 0;
                $notice_id = 0 ;
				// le paramétrage est-il : dédoublonnage sur code ? / Ne dédoublonner que sur code ISBN (ignorer les ISSN) ?
                if ((($isbn_dedoublonnage)&&(!$isbn_only))||(($isbn_dedoublonnage)&&($isbn_only)&&(isISBN($isbn)))) {
					$trouvees=0;
					if ($EAN && $isbn) {
						// cas des EAN purs : constitution de la requête
						$requete = "SELECT distinct notice_id FROM notices ";
						$requete.= " WHERE notices.code in ('$code','$EAN'".($code10?",'$code10'":"").") limit 1";
						$myQuery = pmb_mysql_query($requete);
						$trouvees=pmb_mysql_num_rows($myQuery);
					} elseif ($isbn) {
						// recherche d'un isbn
						$requete = "SELECT distinct notice_id FROM notices ";
						$requete.= " WHERE notices.code in ('$code'".($code10?",'$code10'":"").") limit 1";
						$myQuery = pmb_mysql_query($requete);
						$trouvees=pmb_mysql_num_rows($myQuery);
					} elseif ($code) {
						// note : le code est recherché dans le champ code des notices
						// (cas des code-barres disques qui échappent à l'EAN)
						//
						$requete = "SELECT notice_id FROM notices ";
						$requete.= " WHERE notices.code like '$code' limit 10";
						$myQuery = pmb_mysql_query($requete);
						$trouvees=pmb_mysql_num_rows($myQuery);
					}

                    // dédoublonnage sur isbn
                    if ($EAN  || $isbn || $code) {
						if ($trouvees==0) {
                            $new_notice=1;
                        } else {
                        	if(isset($import_notice_existing_replace) && $import_notice_existing_replace == 1) {
                        		$new_notice=1;
                        		pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_expl_".addslashes(SESSid).".inc', '".$msg['import_notice_replaced']." $EAN  || $isbn || $code ".addslashes($tit[0]['a'])."') ") ;
                        	} else {
                            	$new_notice=0;
                            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_expl_".addslashes(SESSid).".inc', '".$msg[542]." $EAN  || $isbn || $code ".addslashes($tit[0]['a'])."') ") ;
                            }
                            $notice_id = pmb_mysql_result($myQuery,0,"notice_id");
                        }
                    } else {
                        if ($isbn_mandatory == 1) {
                            pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[543]."') ") ;
                        } else {
                            $new_notice = 1;
							pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[565]."') ") ;
                        }
                    }
                } else {
                    // pas de dédoublonnage
                    if ($isbn_mandatory == 1 && $isbn_OK=="") {
                       pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[543]."') ") ;
                    }elseif($isbn_OK){
                        $new_notice = 1;
                    }else{
                    	 $new_notice = 1;
                         pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_".addslashes(SESSid).".inc', '".$msg[565]."') ") ;
                    }
                }

                /* the notice is new, we are going to import it... */
                if ($new_notice==1) {
                	if(isset($import_notice_existing_replace) && $import_notice_existing_replace == 1 && $notice_id) {
                		$notice_remplacee++;
                	}
                    import_new_notice($notice_id) ;
                    if($link_generate) import_notice_link();
    				import_new_notice_suite() ;
    				//Indexation
    				if(!$import_without_index && ($notice_id != 0)) {
						indexation_stack::push($notice_id, TYPE_NOTICE, "all");
						$indexation_stack_number++;
					}
    				if ($sub == "import_expl") {
    					if(!empty($import_explajtNOTI) && !empty($import_expl_caddie_NOTI)) {
    						import_expl::add_object_caddie($notice_id, 'NOTI', $import_expl_caddie_NOTI);
    					}
    				} else {
    					if(!empty($import_recordsajtNOTI) && !empty($import_records_caddie_NOTI)) {
    						import_records::add_object_caddie($notice_id, 'NOTI', $import_records_caddie_NOTI);
    					}
    				}
                } else {
                	$notice_deja_presente++;

					//TRAITEMENT DES DOCS NUMERIQUES SUR NOTICE EXISTANTE
					if ($add_explnum===TRUE) ajoute_explnum();
				}

                // TRAITEMENT DES EXEMPLAIRES ICI
                if ($sub == "import_expl") {
                    traite_exemplaires () ;
                } // fin if $sub=import_expl
    		}
            
            /* this has been succesfuly read, it can be deleted */
	        $sql_del = "delete from import_marc where id_import = '".$idnotice_import."' ";
            $sql_result_del = pmb_mysql_query($sql_del) or die ("Couldn't delete import_marc $idnotice_import !");
            
            if(!$import_without_index && ($indexation_stack_number % 100 == 0)) {
                //On lance directement l'indexation par lot de 100 pour ne pas encombrer la pile
                indexation_stack::live_indexation();
            }
        } /* end while records in import table */
        
        $sql = "select count(1) as reste from import_marc where origine='".addslashes(SESSid)."'";
        $sql_result = pmb_mysql_query($sql) or die ("Couldn't select count import table !");
        $reste=pmb_mysql_result($sql_result,0,"reste");

        //On termine avec un nouvel appel à l'indexation directe
        if(!$import_without_index) {
            indexation_stack::live_indexation();
        }
        
        if ($sub == "import_expl") {
            if ($reste > 0 ) {
				$formulaire = import_expl::get_hidden_form('load', 'load');

                 //On enregistre les ids utilisés avant le rechargement
                global $notices_crees, $notices_a_creer,$bulletins_crees,$bulletins_a_creer;
                $tabimport_id=array();
                $tabimport_id['notices_existantes'] = $notices_crees;
                $tabimport_id['notices_a_creer'] = $notices_a_creer;
                $tabimport_id['bulletins_crees'] = $bulletins_crees;
                $tabimport_id['bulletins_a_creer'] = $bulletins_a_creer;
                $fo = fopen("$base_path/temp/liste_id".SESSid.".txt","w");
				fwrite($fo,serialize($tabimport_id));
                $formulaire.="<script> setTimeout(\"document.load.submit()\",2000); </script>\n";
                printf ($msg[513], $reste, $nbtot_notice); /* File %s ... . Still %s notices to load (total = %s) ... */
                if ($notice_deja_presente==1) {
                	printf ($msg[514],$notice_deja_presente) ; /* <br />($notice_deja_presente notice already in the database has been ignored since the begining) */
                } elseif ($notice_deja_presente>1) {
                	printf ($msg[515],$notice_deja_presente) ; /* <br />($notice_deja_presente notices already in the database have been ignored since the begining) */
                }
                if ($notice_remplacee==1) {
                	printf ($msg['import_notice_replace_only_one'],$notice_remplacee) ; /* <br />($notice_remplacee notice already in the database has been replaced since the begining) */
                } elseif ($notice_remplacee>1) {
                	printf ($msg['import_notice_replace_many'],$notice_remplacee) ; /* <br />($notice_remplacee notices already in the database have been replaced since the begining) */
                }
                if ($notice_rejetee>0) {
                	print "<br /> ".$notice_rejetee." ".$msg['notices_invalides'];
                }
                printf ($msg[521], $nb_expl_ignores); /* ## exemplaire(s) ignoré(s) */
            } else {
                $formulaire="";
                $script="";
                printf ($msg[516], $nbtot_notice); /* There were <b>$nbtot_notice</b> notice(s) to load, everything is OK...</b> */
                if ($notice_deja_presente==1) {
                	printf ($msg[517], $notice_deja_presente); /*  <b>$notice_deja_presente</b> notice already in the database has been ignored... */
                } elseif ($notice_deja_presente>1) {
                	printf ($msg[518], $notice_deja_presente); /*  dont <b>$notice_deja_presente</b> notices already in the database have been ignored... */
                }
                if ($notice_remplacee==1) {
                	printf ($msg['import_notice_replace_only_one'],$notice_remplacee) ; /* <br />($notice_remplacee notice already in the database has been replaced since the begining) */
                } elseif ($notice_remplacee>1) {
                	printf ($msg['import_notice_replace_many'],$notice_remplacee) ; /* <br />($notice_remplacee notices already in the database have been replaced since the begining) */
                }
                if ($notice_rejetee>0) {
                	print "<br /> ".$notice_rejetee." ".$msg['notices_invalides'];
                }
                printf ($msg[521], $nb_expl_ignores); /* ## exemplaire(s) ignoré(s) */
                /* ajouter ici SELECT error_origin, error_text, count(*) FROM error_log group by error_origin, error_text */
                $gen_liste_log="";
                $array_isbn_doublons = array();
                $datetime_import='';
                $resultat_liste=pmb_mysql_query("SELECT error_origin, error_text, error_date, count(*) as nb_error FROM error_log where error_origin in ('expl_".addslashes(SESSid).".class','import_expl_".addslashes(SESSid).".inc','iimport_expl_".addslashes(SESSid).".inc','import_".addslashes(SESSid).".inc.php','import_".addslashes(SESSid).".inc','import_func_".addslashes(SESSid).".inc.php') group by error_origin, error_text" );
                $nb_liste=pmb_mysql_num_rows($resultat_liste);
                if ($nb_liste>0) {
                    $gen_liste_log = "<br /><br /><b>".$msg[538]."</b><br />!!dbls_isbn!!<table border='1'>" ;
                    $gen_liste_log.="<tr><th>".$msg[539]."</th><th>".$msg[540]."</th><th>".$msg[541]."</th></tr>";
                    $i_log=0;
                    while ($i_log<$nb_liste) {
                    	if (preg_match('`^'.$msg['542'].'(.*)\|\|(.*)\|\|(.*)$`',pmb_mysql_result($resultat_liste,$i_log,"error_text"),$out)) {
                    		for ($j=1;$j<=3;$j++) {
                    			if ($tmp_isbn = trim($out[$j])) {
                    				$tmp_isbn = "'".$tmp_isbn."'";
                    				if ((!count($array_isbn_doublons))||(!in_array($tmp_isbn,$array_isbn_doublons))) {
                    					$array_isbn_doublons[] = $tmp_isbn;
                    				}
                    			}
                    		}
                    	}
                        $gen_liste_log.="<tr>";
                        $gen_liste_log.="<td>".pmb_mysql_result($resultat_liste,$i_log,"error_origin")."</td>" ;
                        $gen_liste_log.="<td><b>".pmb_mysql_result($resultat_liste,$i_log,"error_text")."</b></td>" ;
                        $gen_liste_log.="<td>".pmb_mysql_result($resultat_liste,$i_log,"nb_error")."</td>" ;
                        $gen_liste_log.="</tr>" ;
                        $datetime_import = pmb_mysql_result($resultat_liste,$i_log,"error_date");
                        $i_log++;
                    }
                }
                $gen_liste_log.="</table>\n" ;
                if (count($array_isbn_doublons)) {
                	$gen_liste_log = str_replace('!!dbls_isbn!!','<div class="hmenu">'.$msg['last_import_isbn_doublons_found'].'<span><a href="javascript:parent.location=\'../../catalog.php?categ=search&mode=8&option_show_notice_fille=&option_show_expl=\';">'.$msg['search_expl'].'</a></span></div>',$gen_liste_log);
                } else {
                	$gen_liste_log = str_replace('!!dbls_isbn!!','',$gen_liste_log);
                }
                print $gen_liste_log;
                $_SESSION["last_import_isbn_doublons"] = json_encode(implode(',',$array_isbn_doublons));
                $_SESSION["last_import_isbn_doublons_datetime"] = $datetime_import;
                @unlink("$base_path/temp/liste_id".SESSid.".txt");
            }
            print $formulaire;
            print $script;

            //Options avancées
            print "<br /><br />".import_expl::get_links_caddies();

        } else {
            // import de notices
            if ($reste > 0 ) {
				$formulaire = import_records::get_hidden_form('load', 'load');

                //On enregistre les ids utilisés avant le rechargement
                global $notices_crees, $notices_a_creer,$bulletins_crees,$bulletins_a_creer;
                $tabimport_id=array();
                $tabimport_id['notices_existantes'] = $notices_crees;
                $tabimport_id['notices_a_creer'] = $notices_a_creer;
                $tabimport_id['bulletins_crees'] = $bulletins_crees;
                $tabimport_id['bulletins_a_creer'] = $bulletins_a_creer;
                $fo = fopen("$base_path/temp/liste_id".SESSid.".txt","w");
				fwrite($fo,serialize($tabimport_id));
                $formulaire.="<script> setTimeout(\"document.load.submit()\",2000); </script>\n";
                printf ($msg[509].$msg[513],$from_file, $reste, $nbtot_notice); /* File %s ... . Still %s notices to load (total = %s) ... */
                if ($notice_deja_presente==1) {
                	printf ($msg[514],$notice_deja_presente) ; /* <br />($notice_deja_presente notice already in the database has been ignored since the begining) */
                } elseif ($notice_deja_presente>1) {
                	printf ($msg[515],$notice_deja_presente) ; /* <br />($notice_deja_presente notices already in the database have been ignored since the begining) */
                }
                if ($notice_remplacee==1) {
                	printf ($msg['import_notice_replace_only_one'],$notice_remplacee) ; /* <br />($notice_remplacee notice already in the database has been replaced since the begining) */
                } elseif ($notice_remplacee>1) {
                	printf ($msg['import_notice_replace_many'],$notice_remplacee) ; /* <br />($notice_remplacee notices already in the database have been replaced since the begining) */
                }
                if ($notice_rejetee>0) {
                	print "<br /> ".$notice_rejetee." ".$msg['notices_invalides'];
                }
            } else {
                $formulaire="";
                $script="";
                printf ($msg[509].$msg[516], $from_file, $nbtot_notice); /* There were <b>$nbtot_notice</b> notice(s) to load, everything is OK...</b> */
                if ($notice_deja_presente==1) {
                	printf ($msg[517], $notice_deja_presente); /*  <b>$notice_deja_presente</b> notice already in the database has been ignored... */
                } elseif ($notice_deja_presente>1) {
                	printf ($msg[518], $notice_deja_presente); /*  dont <b>$notice_deja_presente</b> notices already in the database have been ignored... */
                }
                if ($notice_remplacee==1) {
                	printf ($msg['import_notice_replace_only_one'],$notice_remplacee) ; /* <br />($notice_remplacee notice already in the database has been replaced since the begining) */
                } elseif ($notice_remplacee>1) {
                	printf ($msg['import_notice_replace_many'],$notice_remplacee) ; /* <br />($notice_remplacee notices already in the database have been replaced since the begining) */
                }
                if ($notice_rejetee>0) {
                	print "<br /> ".$notice_rejetee." ".$msg['notices_invalides'] ;
                }
                /* ajouter ici SELECT error_origin, error_text, count(*) FROM error_log group by error_origin, error_text */
                $gen_liste_log="";
                $array_isbn_doublons = array();
                $datetime_import='';
                $resultat_liste=pmb_mysql_query("SELECT error_origin, error_text, error_date, count(*) as nb_error FROM error_log where error_origin in ('expl_".addslashes(SESSid).".class','import_expl_".addslashes(SESSid).".inc','iimport_expl_".addslashes(SESSid).".inc','import_".addslashes(SESSid).".inc.php', 'import_".addslashes(SESSid).".inc','import_func_".addslashes(SESSid).".inc.php') group by error_origin, error_text" );
                $nb_liste=pmb_mysql_num_rows($resultat_liste);
                if ($nb_liste>0) {
                    $gen_liste_log = "<br /><br /><b>".$msg[538]."</b><br />!!dbls_isbn!!<table width=\"100%\"  border='1'>" ;
                    $gen_liste_log.="<tr><td>".$msg[539]."</td><td>".$msg[540]."</td><td>".$msg[541]."</td></tr>";
                    $i_log=0;
                    while ($i_log<$nb_liste) {
                    	if (preg_match('`^'.$msg['542'].'(.*)\|\|(.*)\|\|(.*)$`',pmb_mysql_result($resultat_liste,$i_log,"error_text"),$out)) {
                    		for ($j=1;$j<=3;$j++) {
                    			if ($tmp_isbn = trim($out[$j])) {
                    				$tmp_isbn = "'".$tmp_isbn."'";
                    				if ((!count($array_isbn_doublons))||(!in_array($tmp_isbn,$array_isbn_doublons))) {
                    					$array_isbn_doublons[] = $tmp_isbn;
                    				}
                    			}
                    		}
                    	}
                        $gen_liste_log.="<tr>";
                        $gen_liste_log.="<td>".pmb_mysql_result($resultat_liste,$i_log,"error_origin")."</td>" ;
                        $gen_liste_log.="<td><b>".pmb_mysql_result($resultat_liste,$i_log,"error_text")."</b></td>" ;
                        $gen_liste_log.="<td>".pmb_mysql_result($resultat_liste,$i_log,"nb_error")."</td>" ;
                        $gen_liste_log.="</tr>" ;
                        $datetime_import = pmb_mysql_result($resultat_liste,$i_log,"error_date");
                        $i_log++;
                    }
                }
                $gen_liste_log.="</table>\n" ;
                if (count($array_isbn_doublons)) {
                	$gen_liste_log = str_replace('!!dbls_isbn!!','<div class="hmenu">'.$msg['last_import_isbn_doublons_found'].'<span><a href="javascript:parent.location=\'../../catalog.php?categ=search&mode=6\';">'.$msg['search_extended'].'</a></span></div>',$gen_liste_log);
                } else {
                	$gen_liste_log = str_replace('!!dbls_isbn!!','',$gen_liste_log);
                }
                print $gen_liste_log;
                $_SESSION["last_import_isbn_doublons"] = json_encode(implode(',',$array_isbn_doublons));
                $_SESSION["last_import_isbn_doublons_datetime"] = $datetime_import;
                @unlink("$base_path/temp/liste_id".SESSid.".txt");
            }

	        print $formulaire;
	        print $script;

	        //Options avancées
	        print "<br /><br />".import_records::get_links_caddies();
        }
        break;
    default:
    	$formulaire="<form class='form-$current_module' name=\"beforeupload\" method=\"post\" action=\"iimport_expl.php\" onsubmit=\"return false;\">";
    	$formulaire.="<INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"import\" />";
        $formulaire.="<INPUT NAME=\"action\" id=\"action\" TYPE=\"hidden\" value=\"beforeupload\" />";
        $form_text="";
        if ($sub == "import_expl"){
        	$formulaire.="<INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import_expl\" />";
        	$form_text=file_get_contents("$include_path/messages/help/$lang/import_expl.txt");
        }else{
        	$formulaire.="<INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"import\" />";
        	$form_text=file_get_contents("$include_path/messages/help/$lang/import.txt");
        }

        $formulaire.=str_replace(array("!!nom_fic!!","!!nom_bouton!!"),array(htmlentities($nom_fichier_transfert_ftp,ENT_QUOTES,$charset),htmlentities($msg["admin_import_notice_prechargement"],ENT_QUOTES,$charset)),$form_text);

        if(!$table_list_func_import){
        	if(file_exists("func_import_subst.xml")){
	        	$table_list_func_import=_parser_text_no_function_(file_get_contents("func_import_subst.xml"),"CATALOG");
	        }elseif(file_exists("func_import.xml")){
	        	$table_list_func_import=_parser_text_no_function_(file_get_contents("func_import.xml"),"CATALOG");
        	}
        }

       	$code_js=$selecteur_fic="";
       	if(is_array($table_list_func_import["ITEM"]) && count($table_list_func_import["ITEM"])){
       		$incr=0;
       		$text_desc_func_import="";
       		$code_js.="<script type=\"text/javascript\">\n";
       		$code_js.="var func_import_desc= new Array(); var func_import_value= new Array();\n";
       		$selecteur_fic="<label class=\"etiquette\" for=\"".$name_func."\">".htmlentities($msg["admin_import_notice_choice"],ENT_QUOTES,$charset)."</label>\n";
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
	        	var func_import=document.beforeupload.".$name_func.";
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
       	}

       	$formulaire.=$selecteur_fic;
       	$formulaire.="<label class=\"etiquette\" for=\"".$name_func."\" id=\"text_desc_func_import\" name=\"text_desc_func_import\">".htmlentities($text_desc_func_import,ENT_QUOTES,$charset)."</label></br>\n";
       	$formulaire.="<label class=\"etiquette\" for=\"encodage_fic_source\" id=\"text_desc_encodage_fic_source\" name=\"text_desc_encodage_fic_source\">".htmlentities($msg["admin_import_encodage_fic_source"],ENT_QUOTES,$charset)."</label>";
       	$formulaire.=import_entities::get_encoding_selector();
       	$formulaire.="</br></br>";
       	$formulaire.="<INPUT type=\"button\" value=\"".htmlentities($msg["admin_import_notice_telechargement"],ENT_QUOTES,$charset)."\" class=\"bouton\" onclick=\"document.getElementById('action').value ='beforeupload';document.beforeupload.submit();\" />";
        $formulaire.="<INPUT type=\"button\" value=\"".htmlentities($msg["admin_import_notice_prechargement"],ENT_QUOTES,$charset)."\" class=\"bouton\" onclick=\"document.getElementById('action').value ='preload'; document.beforeupload.submit();\"/>";
        $formulaire.="</form>\n";

        $formulaire.=$code_js;

        print $formulaire;
        break;
    }

print "</div></body></html>";

