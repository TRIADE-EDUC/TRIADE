<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: procs.inc.php,v 1.64 2019-06-05 06:41:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($sort)) $sort = 0;
if(!isset($force_exec)) $force_exec = '';
if(!isset($form_type)) $form_type = '';

include("$class_path/parameters.class.php");
require_once("$class_path/notice_tpl_gen.class.php");

switch($dest) {
	case "TABLEAU":
	    $worksheet = new spreadsheetPMB();
		break;
	case "TABLEAUHTML":
		echo "<h1>".$msg[1130]."&nbsp;:&nbsp;".$msg[1131]."</h1>";  
		break;
	case "TABLEAUCSV":
		break;
	case "EXPORT_NOTI":
		$fichier_temp_nom=str_replace(" ","",microtime());
		$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);
		$fname = tempnam("./temp", $fichier_temp_nom.".doc");		
		break;
	default:
		echo "<h1>".$msg[1130]."&nbsp;:&nbsp;".$msg[1131]."</h1>";  
		break;
	}

if(!isset($id_proc)) $id_proc = 0;
if (!$id_proc) {
	procs::$module = 'edit';
	print procs::get_display_list();
} else {
	@set_time_limit ($pmb_set_time_limit);
	//Récupération des variables postées, on en aura besoin pour les liens
	$page="./edit.php";
	$requete = "SELECT idproc, name, requete, comment, proc_notice_tpl, proc_notice_tpl_field FROM procs where idproc='".$id_proc."' ";
	$res = pmb_mysql_query($requete, $dbh);
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
		
		//REINITIALISATION DE LA REQUETE SQL
		switch($dest) {
			case "TABLEAU":
			case "TABLEAUHTML":
			case "TABLEAUCSV":
			case "EXPORT_NOTI":
				if(!$req_nombre_lignes){
					$res = @pmb_mysql_query($sql, $dbh) or die($sql."<br /><br />".pmb_mysql_error()); 
				}else{
					$res = $req_nombre_lignes;
				}
				break;
			default:
				echo "<h1>".htmlentities($row[1], ENT_QUOTES, $charset)."</h1><h2>".htmlentities($row[3], ENT_QUOTES, $charset)."</h2>";
				//tri défini ?
				if($sort>0){
// 					preg_match('`^(.+)( order by .+)$`i',$sql,$arraySql);
					preg_match("/(.+)(order by.+)$/isU", $sql,$arraySql);
					if(count($arraySql)) {
						$sql=$arraySql[1]." order by ".($sort>0?$sort:(-$sort)." DESC");
					} else {
						$sql .= " order by ".($sort>0?$sort:(-$sort)." DESC");
					}
				}
				//Si aucune limite_page n'a été passée, valeur par défaut : 10
				if (!isset($limite_page) || !$limite_page) $limite_page = 10;
				$nbpages= $nombre_lignes_total / $limite_page;
				
				// on arondi le nombre de page pour ne pas avoir de virgules, ici au chiffre supérieur
				$nbpages_arrondi = ceil($nbpages);
				
				// on enlève 1 au nombre de pages, car la 1ere page affichée ne fait pas partie des pages suivantes
				$nbpages_arrondi = $nbpages_arrondi - 1;
				
				if (!isset($numero_page) || !$numero_page) $numero_page=0;
				
				$limite_mysql = $limite_page * $numero_page;
				
				//on définit les limites
				if(stripos($sql, ' LIMIT ') !== false) {
					$sql = substr($sql, 0, stripos($sql, 'LIMIT'));
				}
				$sql = $sql." LIMIT ".$limite_mysql.", ".$limite_page;
				// on execute la requete avec les bonnes limites
				$res = @pmb_mysql_query($sql, $dbh) or die($sql."<br /><br />".pmb_mysql_error()); 
				echo "<p>";	
				break;
		}
		
		$nbr_lignes = @pmb_mysql_num_rows($res);
		$nbr_champs = @pmb_mysql_num_fields($res);

		if ($nbr_lignes) {
			switch($dest) {
				case "TABLEAU":
					$worksheet->write_string(0,0,$row[1]);
					$worksheet->write_string(0,1,$row[3]);
					for($i=0; $i < $nbr_champs; $i++) {
						// entête de colonnes
						$fieldname = pmb_mysql_field_name($res, $i);
						$worksheet->write_string(1,$i,$fieldname);
					}
              		        		
					for($i=0; $i < $nbr_lignes; $i++) {
						$row = pmb_mysql_fetch_row($res);
						$j=0;
						foreach($row as $dummykey=>$col) {
							if(trim($col)=='') $col=" ";
							$worksheet->write(($i+2),$j,$col);
							$j++;
						}
					}
					
					$worksheet->download('Procedure_'.$id_proc.'.xls');
					break;
				case "TABLEAUHTML":
					echo "<h1>$row[1]</h1><h2>$row[3]</h2>$sql<br />";						
					echo "<table>";
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						print("<th class='align_left'>".$fieldname."</th>");
					}
       		        for($i=0; $i < $nbr_lignes; $i++) {
						$row = pmb_mysql_fetch_row($res);
						echo "<tr>";
						foreach($row as $dummykey=>$col) {
							if (is_numeric($col)){
								$col = "'".$col ;
							}
							if(trim($col)=='') $col="&nbsp;";
							print '<td>'.$col.'</td>';
						}
						echo "</tr>";
					}
					echo "</table>";
					break;
				case "TABLEAUCSV":
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						print $fieldname."\t";
					}
					for($i=0; $i < $nbr_lignes; $i++) {
						$row = pmb_mysql_fetch_row($res);
						echo "\n";
						foreach($row as $dummykey=>$col) {
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
					echo "<script type='text/javascript'>
					function survol(obj){
						obj.style.cursor = 'pointer';
					}
					function sort_by_col(type){
						document.forms['navbar'].sort.value = type;
						document.forms['navbar'].submit();					
					}
					</script>";
					echo "<table>";
					ini_set("display_errors",1);
					error_reporting(E_ALL);
					//
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						print "<th class='align_left' onMouseOver ='survol(this);' onClick='sort_by_col(".($sort==($i+1)?(-($i+1)):($i+1)).");'>".$fieldname;
						if($sort==($i+1)){
							print "&nbsp;&#x25B4;";
						}elseif((-$sort)==($i+1)){
							print "&nbsp;&#x25BE;";
						}
						print "</th>";
					}
       		        $odd_even=0;
					for($i=0; $i < $nbr_lignes; $i++) {
						$row = pmb_mysql_fetch_row($res);
						if ($odd_even==0) {
							echo "	<tr class='odd'>";
							$odd_even=1;
						} elseif ($odd_even==1) {
							echo "	<tr class='even'>";
							$odd_even=0;
						}
						foreach($row as $dummykey=>$col) {
							if(trim($col)=='') $col="&nbsp;";
							print '<td>'.$col.'</td>';
						}
						echo "</tr>";
					}
					echo "</table><hr />";
					
					echo "<p class='align_left pn-normal' size='-3'>
					<form name='navbar' class='form-$current_module' action='$page' method='post'>";
					echo "
					<input type='hidden' name='numero_page'  value='$numero_page' />
					<input type='hidden' name='id_proc'  value='$id_proc' />
					<input type='hidden' name='categ'  value='$categ' />
					<input type='hidden' name='sub' value='$sub' />
					<input type='hidden' id='sort' name='sort' value='$sort' />";
					print $param_hidden;
					
					// LIENS PAGE SUIVANTE et PAGE PRECEDENTE
					// si le nombre de page n'est pas 0 et si la variable numero_page n'est pas définie
					// dans cette condition, la variable numero_page est incrémenté et est inférieure à $nombre 
					
					// constitution des liens
					$nav_bar = '';
					$suivante = $numero_page+1;
					$precedente = $numero_page-1;
					// affichage du lien précédent si nécéssaire
					if ($precedente >= 0)
						$nav_bar .= "<img src='".get_url_icon('left.gif')."' style='border:0px; margin:3px 3px' title='$msg[48]' alt='[$msg[48]]' class='align_bottom' onClick=\"document.navbar.dest.value='';document.navbar.numero_page.value='$precedente'; document.navbar.limite_page.value='$limite_page'; document.navbar.submit(); \"/>" ;
					for ($i = 0; $i <=$nbpages_arrondi; $i++) {
						if($i==$numero_page) $nav_bar .= "<strong>".($i+1)."/".($nbpages_arrondi+1)."</strong>";
					}
					if ($suivante<=$nbpages_arrondi) $nav_bar .= "<img src='".get_url_icon('right.gif')."' style='border:0px; margin:3px 3px' title='$msg[49]' alt='[$msg[49]]' class='align_bottom' onClick=\"document.navbar.dest.value='';document.navbar.numero_page.value='$suivante'; document.navbar.limite_page.value='$limite_page'; document.navbar.submit(); \" />";
					echo $nav_bar ;

					echo "
					<input type='hidden' name='dest' value='' />
					$msg[edit_cbgen_mep_afficher] <input type='text' name='limite_page' value='$limite_page' class='saisie-5em' /> $msg[1905]
					<input type='submit' class='bouton' value='".$msg['actualiser']."' onclick=\"this.form.dest.value='';document.navbar.numero_page.value=0;\" />&nbsp;&nbsp;&nbsp;&nbsp;
					<input type='image' src='".get_url_icon('tableur.gif')."' style='border:0px' onClick=\"this.form.dest.value='TABLEAU';\" alt='".$msg['export_tableur']."' title='".$msg['export_tableur']."' />&nbsp;&nbsp;&nbsp;&nbsp;
					<input type='image' src='".get_url_icon('tableur_html.gif')."' style='border:0px' onClick=\"this.form.dest.value='TABLEAUHTML';\" alt='".$msg['export_tableau_html']."' title='".$msg['export_tableau_html']."' />";
 
					if($proc_notice_tpl_field) {
						echo "&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='submit' class='bouton' value='".$msg['etatperso_export_notice']."' onclick=\"this.form.dest.value='EXPORT_NOTI';\" />&nbsp;";
						echo notice_tpl_gen::gen_tpl_select("form_notice_tpl",$proc_notice_tpl,'',0,1);
					}
					echo "</form></p>";
					break;
				}
			} else {
				echo $msg["etatperso_aucuneligne"];
			}
			
			pmb_mysql_free_result($res); 
		} // fin if else proc paramétrée
	}