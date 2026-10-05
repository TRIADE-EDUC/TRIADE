<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: procs.inc.php,v 1.71.2.4 2024/09/14 08:09:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $categ, $sub, $pmb_set_time_limit;
global $dest, $force_exec, $form_type, $nombre_lignes_total, $id_proc, $sort;
global $query_parameters, $form_notice_tpl, $proc_notice_tpl;

if(!isset($sort)) $sort = 0;

include("$class_path/parameters.class.php");
require_once("$class_path/notice_tpl_gen.class.php");
require_once ($class_path."/procs/procs_edition_controller.class.php");

$id_proc = intval($id_proc);
if (!$id_proc) {
	procs_edition_controller::proceed($id_proc);
} else {
	switch($dest) {
		case "TABLEAU":
			break;
		case "TABLEAUHTML":
			break;
		case "TABLEAUCSV":
			break;
		case "EXPORT_NOTI":
			$fichier_temp_nom=str_replace(" ","",microtime());
			$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);
			$fname = tempnam("./temp", $fichier_temp_nom.".doc");
			break;
		default:
			break;
	}
	
	@set_time_limit ($pmb_set_time_limit);
	//Récupération des variables postées, on en aura besoin pour les liens
	$page="./edit.php";
	$requete = "SELECT idproc, name, requete, comment, proc_notice_tpl, proc_notice_tpl_field FROM procs where idproc='".$id_proc."' ";
	$res = pmb_mysql_query($requete);
	$row=pmb_mysql_fetch_row($res);
	
	//Requete et calcul du nombre de pages à afficher selon la taille de la base 'pret'
	//********************************************************************************/
	
	// récupérer ici la procédure à lancer
	$sql = $row[2];
	//$proc_notice_tpl=$row[4];
	$proc_notice_tpl_field=$row[5];
	if (preg_match_all("|!!(.*)!!|U",$sql,$query_parameters) && $form_type=="") {
		$hp=new parameters($id_proc,"procs");
		$hp->gen_form("edit.php?categ=procs&sub=&action=execute&id_proc=".$id_proc."&force_exec=".$force_exec);
	} else {
	    list_query_proc_edition_ui::set_id_proc($id_proc);
	    
		$param_hidden="";
		if($force_exec){
			$param_hidden.="<input type='hidden' name='force_exec'  value='".$force_exec."' />";//On a forcé la requete
		}
		if (preg_match_all("|!!(.*)!!|U",$sql,$query_parameters)) {
			$hp=new parameters($id_proc,"procs");
			$hp->get_final_query();
			$sql=$hp->final_query;
			$param_hidden.=$hp->get_hidden_values();//Je mets les paramêtres en champ caché en cas de forçage
			$param_hidden.="<input type='hidden' name='form_type'  value='gen_form' />";//Je mets le marqueur des paramêtres en champ caché en cas de forçage
		}
		
		if($dest != "TABLEAU" && $dest != "TABLEAUHTML" && $dest != "TABLEAUCSV"){
			print "<form class=\"form-edit\" id=\"formulaire\" name=\"formulaire\" action='./edit.php?categ=procs&sub=&action=execute&id_proc=".$id_proc."&force_exec=".$force_exec."' method=\"post\">";
			
			print "<input type='button' class='bouton' value='".htmlentities($msg[654], ENT_QUOTES, $charset)."'  onClick='this.form.action=\"./edit.php?categ=procs\";this.form.submit();'/>";
			if (!explain_requete($sql) && (SESSrights & EDIT_FORCING_AUTH) && !$force_exec) {
				print $param_hidden;
				print "<input type='button' id='procs_button_exec' class='bouton' value='".htmlentities($msg["procs_force_exec"], ENT_QUOTES, $charset)."' onClick='this.form.action=\"./edit.php?categ=procs&sub=&action=execute&id_proc=".$id_proc."&force_exec=1\";this.form.submit();' />";
			} else{
				print "<input type='submit' id='procs_button_exec' class='bouton' value='".htmlentities($msg[708], ENT_QUOTES, $charset)."'/>";
			}
			print "<br />";
			print "</form>";
		}
		
		if (!explain_requete($sql) && !((SESSrights & EDIT_FORCING_AUTH) && $force_exec)){
			die("<br /><br />".$sql."<br /><br />".htmlentities($msg["proc_param_explain_failed"], ENT_QUOTES, $charset)."<br /><br />".$erreur_explain_rqt);
		}
		
		$req_nombre_lignes="";
		if(!isset($nombre_lignes_total) || !$nombre_lignes_total){
			$req_nombre_lignes = pmb_mysql_query($sql);
			if(!$req_nombre_lignes){
				 die($sql."<br /><br />".pmb_mysql_error());
			}
			$nombre_lignes_total = pmb_mysql_num_rows($req_nombre_lignes);
		}
		$param_hidden.="<input type='hidden' name='nombre_lignes_total'  value='".$nombre_lignes_total."' />";//Je garde le nombre de ligne total pour le pas refaire la requête à la page suivante
		
		$nbr_lignes = 0;
		$nbr_champs = 0;
		switch($dest) {
		    case "TABLEAU":
		        list_query_proc_edition_ui::get_instance()->get_display_spreadsheet_list();
		        break;
		    case "TABLEAUHTML":
		        print list_query_proc_edition_ui::get_instance()->get_display_html_list();
		        break;
		    case "TABLEAUCSV":
		    case "EXPORT_NOTI":
		        if(!$req_nombre_lignes){
		            $res = pmb_mysql_query($sql) or die($sql."<br /><br />".pmb_mysql_error());
		        }else{
		            $res = $req_nombre_lignes;
		        }
		        $nbr_lignes = @pmb_mysql_num_rows($res);
		        $nbr_champs = @pmb_mysql_num_fields($res);
		        break;
		    default:
		        print list_query_proc_edition_ui::get_instance()->get_display_list();
		        break;
		}
		if ($nbr_lignes) {
			switch($dest) {
				case "TABLEAU":

					break;
				case "TABLEAUHTML":
					break;
				case "TABLEAUCSV":
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						print $fieldname."\t";
					}
					for($i=0; $i < $nbr_lignes; $i++) {
						$row = pmb_mysql_fetch_row($res);
						echo "\n";
						foreach($row as $col) {
							/* if (is_numeric($col)) {
								$col = "\"'".(string)$col."\"" ;
							} */
							print "$col\t";
						}
					}
					break;				
				case "EXPORT_NOTI":					
					$noti_tpl=new notice_tpl_gen($form_notice_tpl);					
       		        for($i=0; $i < $nbr_lignes; $i++) {
						$row = pmb_mysql_fetch_object($res);
						$contents.=$noti_tpl->build_notice($row->$proc_notice_tpl_field)."<hr />";									
					}
					header("Content-Disposition: attachment; filename='bibliographie.doc';");
					header('Content-type: application/msword'); 
					header("Expires: 0");
				    header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
				    header("Pragma: public");
					echo "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>".$contents."</body></html>";
					break;
				default:
					break;
				}
				pmb_mysql_free_result($res);
			}
		} // fin if else proc paramétrée
	}