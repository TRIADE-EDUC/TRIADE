<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: stat_opac.inc.php,v 1.29 2022/04/22 11:50:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

include("$class_path/parameters.class.php");

function show_stats() {
	return list_statopac_edition_ui::get_instance()->get_display_list();
}

function show_results_stats($id_proc=0){
	global $msg, $form_type, $categ, $numero_page,$limite_page, $sub,$charset;
	global $dest,$pmb_set_time_limit, $force_exec,$erreur_explain_rqt,$nombre_lignes_total;
	@set_time_limit ($pmb_set_time_limit);
	//Récupération des variables postées, on en aura besoin pour les liens
	$page="./edit.php";
	$requete = "SELECT idproc, name, requete, comment, num_vue FROM statopac_request where idproc='".$id_proc."' ";
	$res = pmb_mysql_query($requete);
	$row=pmb_mysql_fetch_row($res);
	
	//Requete et calcul du nombre de pages à afficher selon la taille de la base 'pret'
	//********************************************************************************/
	
	// récupérer ici la procédure à lancer
	$sql = $row[2];
	$sql = str_replace("VUE()","statopac_vue_$row[4]",$sql);
	if (preg_match_all("|!!(.*)!!|U",$sql,$query_parameters) && $form_type=="") {
		$hp=new parameters($id_proc,"statopac_request");
		$hp->gen_form("edit.php?categ=stat_opac&sub=&action=execute&id_proc=".$id_proc."&force_exec=".$force_exec);
	} else {
		
		$param_hidden="";
		if($force_exec){
			$param_hidden.="<input type='hidden' name='force_exec'  value='".$force_exec."' />";//On a forcé la requete
		}
		if (preg_match_all("|!!(.*)!!|U",$sql,$query_parameters)) {
			$hp=new parameters($id_proc,"statopac_request");
			$hp->get_final_query();
			$sql=$hp->final_query;
			$param_hidden.=$hp->get_hidden_values();//Je mets les paramêtres en champ caché en cas de forçage
			$param_hidden.="<input type='hidden' name='form_type'  value='gen_form' />";//Je mets le marqueur des paramêtres en champ caché en cas de forçage
		}
		$sql = str_replace("VUE()","statopac_vue_$row[4]",$sql);
		
		if($dest != "TABLEAU" && $dest != "TABLEAUHTML" && $dest != "TABLEAUCSV"){
			print "<form class=\"form-edit\" id=\"formulaire\" name=\"formulaire\" action='./edit.php?categ=stat_opac&sub=&action=execute&id_proc=".$id_proc."&force_exec=".$force_exec."' method=\"post\">";
			
			print "<input type='button' class='bouton' value='".htmlentities($msg[654], ENT_QUOTES, $charset)."'  onClick='this.form.action=\"./edit.php?categ=stat_opac\";this.form.submit();' />";
			if (!explain_requete($sql) && (SESSrights & EDIT_FORCING_AUTH) && !$force_exec) {
				print $param_hidden;
				print "<input type='button' id='procs_button_exec' class='bouton' value='".htmlentities($msg["procs_force_exec"], ENT_QUOTES, $charset)."' onClick='this.form.action=\"./edit.php?categ=stat_opac&sub=&action=execute&id_proc=".$id_proc."&force_exec=1\";this.form.submit();' />";
			} else {
				print "<input type='submit' id='procs_button_exec' class='bouton' value='".htmlentities($msg[708], ENT_QUOTES, $charset)."'/>";
			}
			print "<br />";
			print "</form>";
			// la procédure n'a pas de parm ou les paramètres ont été reçus
			if (!explain_requete($sql) && !((SESSrights & EDIT_FORCING_AUTH) && $force_exec)) {
				die("<br /><br />".$sql."<br /><br />".htmlentities($msg["proc_param_explain_failed"], ENT_QUOTES, $charset)."<br /><br />".$erreur_explain_rqt); 
			}
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
		
		
		//Si aucune limite_page n'a été passée, valeur par défaut : 25
		if (!isset($limite_page) || !$limite_page) $limite_page = 25;
		$nbpages= $nombre_lignes_total / $limite_page; 
		
		// on arondi le nombre de page pour ne pas avoir de virgules, ici au chiffre supérieur 
		$nbpages_arrondi = ceil($nbpages); 
		
		// on enlève 1 au nombre de pages, car la 1ere page affichée ne fait pas partie des pages suivantes
		$nbpages_arrondi = $nbpages_arrondi - 1; 
		
		if (!isset($numero_page) || !$numero_page) $numero_page=0;
		
		$limite_mysql = $limite_page * $numero_page; 
		
		//REINITIALISATION DE LA REQUETE SQL
		switch($dest) {
			case "TABLEAU":
			case "TABLEAUHTML":
			case "TABLEAUCSV":
				if(!$req_nombre_lignes){
					$res = pmb_mysql_query($sql) or die($sql."<br /><br />".pmb_mysql_error()); 
				}else{
					$res = $req_nombre_lignes;
				}
				break;
			default:
				echo "<h1>".htmlentities($row[1], ENT_QUOTES, $charset)."</h1><h2>".htmlentities($row[3], ENT_QUOTES, $charset)."</h2>";
				$sql = $sql." LIMIT ".$limite_mysql.", ".$limite_page; 
				// on execute la requete avec les bonnes limites
				$res = pmb_mysql_query($sql) or die($sql."<br /><br />".pmb_mysql_error()); 
				echo "<p>";	
				break;
		}
		
		$nbr_lignes = @pmb_mysql_num_rows($res);
		$nbr_champs = @pmb_mysql_num_fields($res);

		if ($nbr_lignes) {
			switch($dest) {
				case "TABLEAU":
				    $worksheet = new spreadsheetPMB();
					
					$worksheet->write_string(0,0,$row[1]);
					$worksheet->write_string(0,1,$row[3]);
					for($i=0; $i < $nbr_champs; $i++) {
						// entête de colonnes
						$fieldname = pmb_mysql_field_name($res, $i);
						$worksheet->write_string(2,$i,$fieldname);
					}
              		        		
					for($i=0; $i < $nbr_lignes; $i++) {
						$row = pmb_mysql_fetch_row($res);
						$j=0;
						foreach($row as $dummykey=>$col) {
							if (is_numeric($col) && preg_match("/^0/",$col)){
								$col = "'".$col ;
							}
							if(trim($col)=='') $col=" ";
							if (is_numeric($col)) {
							    $worksheet->write(($i+3),$j,$col);
							} else {
							    $worksheet->write_string(($i+3),$j,$col);
							}
							$j++;
						}
					}
					
					$worksheet->download('edition.xls');
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
							/*if (is_numeric($col)){
								$col = "'".$col ;
							}*/
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
				default:
					echo "<table>";
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						print "<th class='align_left'>".$fieldname."</th>";
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
					<form name='navbar' class='form-edit' action='$page' method='post'>";
					echo "
					<input type='hidden' name='numero_page'  value='$numero_page' />
					<input type='hidden' name='id_proc'  value='$id_proc' />
					<input type='hidden' name='categ'  value='$categ' />
					<input type='hidden' name='sub' value='$sub' />";
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
					<input type='image' src='".get_url_icon('tableur_html.gif')."' style='border:0px' onClick=\"this.form.dest.value='TABLEAUHTML';\" alt='".$msg['export_tableau_html']."' title='".$msg['export_tableau_html']."' />
					</form></p>";
					break;
				}
			} else {
				echo $msg["etatperso_aucuneligne"];
			}
			pmb_mysql_free_result($res); 
		}
	
}

if(!isset($id_proc)){
	print show_stats();
} else {
	print show_results_stats($id_proc);
}
?>