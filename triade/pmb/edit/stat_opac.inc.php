<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: stat_opac.inc.php,v 1.25 2019-06-05 06:41:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

include("$class_path/parameters.class.php");

function show_stats($dbh) {
	
	global $msg;
 	global $charset;
 	global $PMBuserid, $javascript_path;
	
 	print "
		<script type=\"text/javascript\" src=\"".$javascript_path."/tablist.js\"></script>
		<span class='item-expand'>
			<a href=\"javascript:expandAll()\"><img src='".get_url_icon('expand_all.gif')."' style='border:0px' id=\"expandall\"></a>
			<a href=\"javascript:collapseAll()\"><img src='".get_url_icon('collapse_all.gif')."' style='border:0px' id=\"collapseall\"></a>
		</span>
		";
	
 	$requete_vue = "select * from statopac_vues order by nom_vue";
 	$res = pmb_mysql_query($requete_vue,$dbh);
 	$vue_affichage="";	
	if(pmb_mysql_num_rows($res) == 0){
		$vue_affichage="<br>".$msg["stat_no_view_created"]."<br>";
		return $vue_affichage;
	} else {		
		$vue_affichage="";
		$parity=1;
		while(($vue = pmb_mysql_fetch_object($res))){			
			$rqt="select * from statopac_request where num_vue='".addslashes($vue->id_vue)."' order by name";
			$result = pmb_mysql_query($rqt);
			$liste_requete ="";
			while(($request = pmb_mysql_fetch_object($result))){
				if ($parity % 2) {
				$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity++;
				$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./edit.php?categ=stat_opac&sub=&action=execute&id_proc=$request->idproc';\" ";
				$liste_requete.="\n<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
						<td><strong>".htmlentities($request->name,ENT_QUOTES,$charset)."</strong><br>
							<small>".htmlentities($request->comment,ENT_QUOTES,$charset)."</small></td>
					</tr>";	
			}
			if($liste_requete){
				$tab_list="<table><tr><th colspan=4>".stripslashes(htmlentities($vue->nom_vue,ENT_QUOTES,$charset))."</th></tr>".$liste_requete."</table>";
				$vue_affichage .= "<div id='vue".$vue->id_vue."Parent' class='notice-parent'>";
				$lien = stripslashes(htmlentities($vue->nom_vue,ENT_QUOTES,$charset));
				$space = "<small><span style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span></small>";
				$vue_affichage .= "<img id='vue".$vue->id_vue."Img' class='img_plus' style='border:0px; margin:3px 3px' onClick=\"expandBase('vue".$vue->id_vue."',true);return false;\" title='requete' name='imEx' src='".get_url_icon('plus.gif')."' >";
				$vue_affichage .= "$space<span class='notice-heada'>$lien</span>";
				$vue_affichage .= "</div>";
				$vue_affichage .= "<div id='vue".$vue->id_vue."Child' class='notice-child' style='margin-bottom: 6px; display: none;'>$tab_list</div>";
			}				
		}
 	}
 	
	return $vue_affichage;
}

function show_results_stats($id_proc=0){

	global $msg, $dbh, $form_type, $categ, $numero_page,$limite_page, $sub,$charset;
	global $dest,$pmb_set_time_limit, $force_exec,$erreur_explain_rqt,$nombre_lignes_total;
	@set_time_limit ($pmb_set_time_limit);
	//Récupération des variables postées, on en aura besoin pour les liens
	$page="./edit.php";
	$requete = "SELECT idproc, name, requete, comment, num_vue FROM statopac_request where idproc='".$id_proc."' ";
	$res = pmb_mysql_query($requete, $dbh);
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
		
		
		//Si aucune limite_page n'a été passée, valeur par défaut : 10
		if (!isset($limite_page) || !$limite_page) $limite_page = 10;
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
					$res = @pmb_mysql_query($sql, $dbh) or die($sql."<br /><br />".pmb_mysql_error()); 
				}else{
					$res = $req_nombre_lignes;
				}
				break;
			default:
				echo "<h1>".htmlentities($msg["opac_admin_menu"], ENT_QUOTES, $charset)."&nbsp;:&nbsp;".htmlentities($msg["stat_opac_menu"], ENT_QUOTES, $charset)."</h1>";
				echo "<h1>".htmlentities($row[1], ENT_QUOTES, $charset)."</h1><h2>".htmlentities($row[3], ENT_QUOTES, $charset)."</h2>";
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
							$worksheet->write_string(($i+3),$j,$col);
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
	print show_stats($dbh);
} else {
	print show_results_stats($id_proc);
}
?>