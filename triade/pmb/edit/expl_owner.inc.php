<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_owner.inc.php,v 1.22 2019-06-05 06:41:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Récupération des variables postées, on en aura besoin pour les liens
$page="./edit.php";

//Requete et calcul du nombre de pages à afficher selon la taille de la base 'pret'
//********************************************************************************
$sql = "";
$sql = "SELECT notices_m.notice_id as m_id, notices_s.notice_id s_id, section_libelle, expl_cote, expl_cb, idlender, lender_libelle , trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit ";
$sql.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
$sql.= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
$sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
$sql.= "        docs_type, docs_section, lenders ";
$sql.= "WHERE expl_typdoc = idtyp_doc and idsection = expl_section and expl_owner = idlender ";
$sql = $sql.$critere_requete;


$req_nombre_lignes_pret = pmb_mysql_query($sql);

$nombre_lignes_pret = pmb_mysql_num_rows($req_nombre_lignes_pret);

//Si aucune limite_page n'a été passée, valeur par défaut : 10
if ($limite_page=="") {$limite_page = 10; }
$nbpages= $nombre_lignes_pret / $limite_page; 

// on arondi le nombre de page pour ne pas avoir de virgules, ici au chiffre supérieur 
$nbpages_arrondi = ceil($nbpages); 

// on enlève 1 au nombre de pages, car la 1ere page affichée ne fait pas partie des pages suivantes
$nbpages_arrondi = $nbpages_arrondi - 1; 

// si par un quelconque hasard, on se retrouve après le dernier enregistrement, rechargement de la liste au premier ouvrage
if ($numero_page > $nbpages_arrondi) {
	switch($dest) {
		case "TABLEAU":
			break;
		case "TABLEAUHTML":
			break;
		default:
			echo "<script language=\"javascript\">document.location.replace(\"".$page."?categ=".$categ."&sub=".$sub."&limite_page=".$limite_page."\");</script>";
			break;
		}
	}

// si la variable numero de page a une valeur ou est différente de 0,
// on multiplie la limite par le numero de la page passée par l'url
// sinon, pas de variable numero_page
if(isset($numero_page) || $numero_page != 0 ) { 
	$limite_mysql = $limite_page * $numero_page; 
	} else { 
		$limite_mysql = 0; // la limite est de 0
		} 

//REINITIALISATION DE LA REQUETE SQL
$sql = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, section_libelle, expl_cote, expl_cb, idlender, lender_libelle , trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit ";
$sql.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
$sql.= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
$sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
$sql.= "        docs_type, docs_section, lenders ";
$sql.= "WHERE expl_typdoc = idtyp_doc and idsection = expl_section and expl_owner = idlender ";
$sql = $sql.$critere_requete;
switch($dest) {
	case "TABLEAU":
	    $worksheet = new spreadsheetPMB();
		$row=0;
		$col=0;
		break;
	case "TABLEAUHTML":
		echo "<h1>".$msg[1110]."&nbsp;:&nbsp;".$msg[1113]."</h1>";  
		echo "<table class='fiche-lecteur' width=100%>";
 		echo "<tr><td class='jauge' colspan='2'>".$msg['circ_preteur']."</td><td class='jauge'>".$msg[296]."</td><td class='jauge'>".$msg['circ_CB']."</td><td class='jauge'>".$msg[233]."</td><td class='jauge'>".$msg[234]."</td></tr>"; 
		break;
	default:
		$sql = $sql." LIMIT ".$limite_mysql.", ".$limite_page; 
		echo "<h1>".$msg[1110]."&nbsp;:&nbsp;".$msg[1113]."</h1>";  
		echo "<table class='fiche-lecteur' width=100%>";
 		echo "<tr><td class='jauge' colspan='2'>".$msg['circ_preteur']."</td><td class='jauge'>".$msg[296]."</td><td class='jauge'>".$msg['circ_CB']."</td><td class='jauge'>".$msg[233]."</td><td class='jauge'>".$msg[234]."</td></tr>"; 
		break;
	}
	
// on lance la requête (mysql_query) et on impose un message d'erreur si la requête ne se passe pas bien (or die) 
$req = pmb_mysql_query($sql) or die("Erreur SQL !<br />".$sql."<br />".pmb_mysql_error()); 

// on va scanner tous les tuples un par un 
$odd_even=0;
while ($data = pmb_mysql_fetch_array($req)) { 
	
	$responsabilites = get_notice_authors(($data['m_id']+$data['s_id'])) ;
	$header_aut = gen_authors_header($responsabilites);
	
	$header_aut ? $auteur=$header_aut : $auteur="";
		
	// on affiche les résultats 
	switch($dest) {
		case "TABLEAU":
			$row++;
			$worksheet->write_string($row,1,$data['idlender']);
			$worksheet->write_string($row,2,$data['lender_libelle']);
			$worksheet->write_string($row,3,$data['expl_cote']);
			$worksheet->write_string($row,4,$data['expl_cb']);
			$worksheet->write_string($row,5,$data['tit']);
			$worksheet->write_string($row,6,$auteur);
			break;
		case "TABLEAUHTML":
			if ($odd_even==0) {
				echo "	<tr class='odd'>";
				$odd_even=1;
				} else if ($odd_even==1) {
					echo "	<tr class='even'>";
					$odd_even=0;
					}
			echo "<td>".$data['idlender']."</td>"; 
			echo "<td>".$data['lender_libelle']."</td>"; 
			echo "<td>".$data['expl_cote']."</td>"; 
			echo "<td>'".$data['expl_cb']."</td>";    
			echo "<td><b>".$data['tit']."</b></td>";    
			echo "<td>".$auteur."</td>";
			echo "</tr>";
			break;
		default:
			if ($odd_even==0) {
				echo "	<tr class='odd'>";
				$odd_even=1;
				} else if ($odd_even==1) {
					echo "	<tr class='even'>";
					$odd_even=0;
					}
			echo "<td>".$data['idlender']."</td>"; 
			echo "<td>".$data['lender_libelle']."</td>"; 
			echo "<td>".$data['expl_cote']."</td>"; 
			echo "<td><a href='catalog.php?categ=edit_expl&id=".$data['expl_id']."&cb=".$data['expl_cb']."'>".$data['expl_cb']."</a></td>";    
			if ($data['m_id']) echo "<td><b><a href='catalog.php?categ=isbd&id=".$data['m_id']."'>".$data['tit']."</a></b></td>";
				else echo "<td><b><a href='catalog.php?categ=serials&sub=serial_form&id=".$data['s_id']."'>".$data['tit']."</a></b></td>";
			echo "<td>".$auteur."</td>";
			echo "</tr>";
			break;
		}
	} // fin while


switch($dest) {
	case "TABLEAU":
		$worksheet->download('expl_owner.xls');
		break;
	case "TABLEAUHTML":
		echo "</table>";
		break;
	default:
		echo "</table>";
		//LIENS PAGE SUIVANTE et PAGE PRECEDENTE
		// si le nombre de page n'est pas 0 et si la variable numero_page n'est pas définie
		// dans cette condition, la variable numero_page est incrémenté et est inférieure à $nombre 
		
		if( $nbpages_arrondi != 0 && empty($numero_page)) {
 			print '< '.$msg[48].' <a href="'.$page.'?categ='.$categ.'&sub='.$sub.'&limite_page='.$limite_page;
 			print '&numero_page=1">'.$msg[49].' ></a>'; // on passe la variable numero page à 1
			} elseif ($nbpages_arrondi !='0' && isset($numero_page) && $numero_page < $nbpages_arrondi) {
				$suivant = $numero_page + 1; // on ajoute 1 au numero de page en cours 
				$precedent = $numero_page - 1;
				print '<a href="'.$page.'?categ='.$categ.'&sub='.$sub.'&limite_page='.$limite_page.'&numero_page='.$precedent;
 				print '">< '.$msg[48].'</a>'; // retour page précédente
				print '<a href="'.$page.'?categ='.$categ.'&sub='.$sub.'&limite_page='.$limite_page.'&numero_page='.$suivant;
 				print '">'.$msg[49].' ></a>'; //le lien pour les pages suivantes
				} // dans cette condition, le lien qui sera affiché lorsque le nombre de page a été atteint
				  elseif ( $nbpages_arrondi !='0' && isset($numero_page) && $numero_page >= $nbpages_arrondi ) { 
					$precedent = $numero_page - 1;
					print '<a href="'.$page.'?categ='.$categ.'&sub='.$sub.'&limite_page='.$limite_page.'&numero_page='.$precedent;
 					print '">< '.$msg[48].'</a>'; // retour page précédente
					}
		
		echo "<p class='align_left pn-normal' size='-3'>
		<form class='form-$current_module' action='$page' method='post'>
		<input type='hidden' name='limite_page'  value='$limite_page' />
		<input type='hidden' name='numero_page'  value='$numero_page' />
		<input type='hidden' name='id_proc'  value='$id_proc' />
		<input type='hidden' name='categ'  value='$categ' />
		<input type='hidden' name='sub' value='$sub' />
		<input type='hidden' name='dest' value='' />
 		<input type='text' name='limite_page_saisie' size='4' value='$limite_page' class='petit' /> ".$msg[1905]."
		<input type='submit' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.limite_page.value=this.form.limite_page_saisie.value;\" />&nbsp;&nbsp;|&nbsp;&nbsp;
		<input type='image' src='".get_url_icon('tableur.gif')."' style='border:0px' onClick=\"this.form.dest.value='TABLEAU';\" alt='".$msg['export_tableur']."' title='".$msg['export_tableur']."' />&nbsp;&nbsp;|&nbsp;&nbsp;
		<input type='image' src='".get_url_icon('tableur_html.gif')."' style='border:0px' onClick=\"this.form.dest.value='TABLEAUHTML';\" alt='".$msg['export_tableau_html']."' title='".$msg['export_tableau_html']."' />
		</form></p>";
		break;
	}

pmb_mysql_free_result($req);
?>