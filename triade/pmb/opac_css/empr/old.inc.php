<?php
use PhpOffice\PhpSpreadsheet\Style\Fill;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id$

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($include_path."/notice_authors.inc.php");

if (!$allow_loan_hist) die();

//Récupération des variables postées, on en aura besoin pour les liens
$page=$_SERVER['SCRIPT_NAME'];

if ($dest=="TABLEAU") {
	//Export excel
	require_once ($class_path."/spreadsheet.class.php");
	$worksheet = new spreadsheetPMB();
	//formats
	$heading_blue = array(
		'fill' => array(
			'type' => Fill::FILL_SOLID,
            'color' => array('rgb' => '00CCFF')
		)
	);
	$heading_10 = array(
		'font' => array(
			'bold' => true,
			'size' => 10
		)
	);
	$heading_12 = array(
		'font' => array(
			'bold' => true,
			'size' => 12
		)
	);
}

// REQUETE SQL
if ($opac_empr_hist_nb_max) $limit=" LIMIT 0, $opac_empr_hist_nb_max ";
else $limit = '';
if ($opac_empr_hist_nb_jour_max) $restrict_date=" date_add(pret_archive.arc_fin, INTERVAL $opac_empr_hist_nb_jour_max day)>=sysdate() AND ";
else $restrict_date="";

$sql = "SELECT arc_expl_notice, arc_expl_bulletin, " ;
$sql.= "group_concat(distinct date_format(arc_debut, '".$msg["format_date_sql"]."') separator '<br />') as aff_pret_debut, "; 
$sql.= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '".$msg["format_date_sql"]."'),')') ,'')))) as tit, ";
$sql.= "if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id, if(notices_m.tparent_id, notices_m.tparent_id, notices_s.tparent_id) as tparent_id, if(notices_m.tnvol, notices_m.tnvol, notices_s.tnvol) as tnvol ";
$sql.= "FROM (((pret_archive LEFT JOIN notices AS notices_m ON arc_expl_notice = notices_m.notice_id ) ";
$sql.= "        LEFT JOIN bulletins ON arc_expl_bulletin = bulletins.bulletin_id) ";
$sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
$sql.= "        empr ";
$sql.= "WHERE $restrict_date empr.id_empr = arc_id_empr and arc_id_empr='$id_empr' ";
$sql.= " and arc_fin < '".date("Y-m-d H:i:s")."'";
$sql.= "group by arc_expl_notice, arc_expl_bulletin, tit, not_id ";
$sql.= "having tit is not null and tit <> '' ";
$sql.= "order by arc_debut desc $limit ";

$req = pmb_mysql_query($sql) or die("Erreur SQL !<br />".$sql."<br />".pmb_mysql_error()); 
$nb_elements = pmb_mysql_num_rows($req) ;

if(!$dest && $nb_elements) {
	print "<script type='text/javascript'>
		if(document.getElementById('empr_loans_old_number')) {
			document.getElementById('empr_loans_old_number').innerHTML = ' (".$nb_elements.")';
		}
	</script>";
}

if (!$dest){
	global $opac_cart_allow;
	
	if ($opac_empr_export_loans) {
		echo "<input class=\"bouton\" type=\"button\" value=\"".$msg["print_loans_bt"]."\" name=\"print_loans_bt\" id=\"print_loans_bt\" onClick=\"location.href='empr.php?tab=".$tab."&lvl=".$lvl."&dest=TABLEAU'\">";
	}
	if ($opac_empr_export_loans && $nb_elements) {
		echo "&nbsp;";
	}
	if ($nb_elements && $opac_cart_allow) {
		echo "<span class='addCart'><input type=\"button\" class=\"bouton\" id=\"add_cart_loans_bt\" value=\"".$msg["add_cart_loans_bt"]."\" onClick=\"javascript:document.add_cart_loans.submit();\"></span>";
		echo "<form name='add_cart_loans' method='post' action='cart_info.php?lvl=loans_".$lvl."' target='cart_info' style='display:none'></form>";
	}
}

if ($nb_elements) {
	if (!$dest) {
		echo "<script type='text/javascript' src='./includes/javascript/sorttable.js'></script>";
		echo "<table class='sortable' style='width:100%'>";
		echo "<thead>";
		echo "<tr>" ;
		echo "<th>".$msg["title"]."</th>
			<th>".$msg["author"]."</th>
			<th class='center'>".$msg["date_loan"]."</th>";
		echo "</tr>" ;
		echo "</thead>";
		$odd_even=1;
		while ($data = pmb_mysql_fetch_array($req)) { 
			$titre = $data['tit'];
			
			// récupération du titre de série
			$titre_serie="";
			if ($data['tparent_id']) {
				$parent = new serie($data['tparent_id']);
				$titre_serie = $parent->name;
				if($data['tnvol'])
					$titre_serie .= ', '.$data['tnvol'];
			}
			if($titre_serie) {
				$titre = $titre_serie.'. '.$titre;
			}
			
			// **********
			$responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
			$responsab = get_notice_authors($data['not_id']) ;
			
			//$this->responsabilites
			$as = array_search ("0", $responsab["responsabilites"]) ;
			if ($as!== FALSE && $as!== NULL) {
				$auteur_0 = $responsab["auteurs"][$as] ;
				$auteur = new auteur($auteur_0["id"]);
				$mention_resp = $auteur->get_isbd();
			} else {
				$aut1_libelle = array();
				$as = array_keys ($responsab["responsabilites"], "1" ) ;
				for ($i = 0 ; $i < count($as) ; $i++) {
					$indice = $as[$i] ;
					$auteur_1 = $responsab["auteurs"][$indice] ;
					$auteur = new auteur($auteur_1["id"]);
					$aut1_libelle[]= $auteur->get_isbd();
				}
				$mention_resp = implode (", ",$aut1_libelle) ;
			}
			
			$mention_resp ? $auteur = $mention_resp : $auteur="";
				
			// on affiche les résultats 
			if ($odd_even==0) {
				$pair_impair="odd";
				$odd_even=1;
				} else if ($odd_even==1) {
					$pair_impair="even";
					$odd_even=0;
					}
			if ($data['arc_expl_notice']) $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=notice_display&id=".$data['not_id']."&seule=1';\" style='cursor: pointer' ";
				else $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=bulletin_display&id=".$data['arc_expl_bulletin']."';\" style='cursor: pointer' ";
			$deb_ligne = "<tr $tr_javascript>";
			echo $deb_ligne ;
			echo "<td column_name='".htmlentities($msg["title"],ENT_QUOTES,$charset)."'>".$titre."</td>";
			echo "<td column_name='".htmlentities($msg["author"],ENT_QUOTES,$charset)."'>".$auteur."</td>";
			echo "<td column_name='".htmlentities($msg["date_loan"],ENT_QUOTES,$charset)."' class='center'>".$data['aff_pret_debut']."</td>"; 
				
			echo "</tr>\n";
		}
		
		echo "</table>";
	} elseif ($dest=="TABLEAU") {
		//Titre
		$worksheet->write(0,0,$msg["empr_loans_old"],$heading_blue);
		$worksheet->merge_cells(0,0,0,6);
		//Entêtes
		$line = 2;
		$x=0;
		$worksheet->write($line,$x,$msg["title"],$heading_10);
		$worksheet->write($line,$x+1,$msg["authors"],$heading_10);
		$worksheet->write($line,$x+2,$msg["date_loan"],$heading_10);
		//Valeurs
		$loc_cours="";
		while ($data = pmb_mysql_fetch_array($req)) {
			$line++;
			$x=0;
			
			$titre = $data['tit'];
			$responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
			$responsab = get_notice_authors($data['not_id']) ;
			$as = array_search ("0", $responsab["responsabilites"]) ;
			if ($as!== FALSE && $as!== NULL) {
				$auteur_0 = $responsab["auteurs"][$as] ;
				$auteur = new auteur($auteur_0["id"]);
				$mention_resp = $auteur->get_isbd();
			} else {
				$aut1_libelle = array();
				$as = array_keys ($responsab["responsabilites"], "1" ) ;
				for ($i = 0 ; $i < count($as) ; $i++) {
					$indice = $as[$i] ;
					$auteur_1 = $responsab["auteurs"][$indice] ;
					$auteur = new auteur($auteur_1["id"]);
					$aut1_libelle[]= $auteur->get_isbd();
				}
				$mention_resp = implode (", ",$aut1_libelle) ;
			}
			$mention_resp ? $auteur = $mention_resp : $auteur="";
			
			$worksheet->write($line,$x,$titre);
			$worksheet->write($line,$x+1,$auteur);
			$worksheet->write($line,$x+2,$data['aff_pret_debut']);	
		}
		$worksheet->download('empr.xls');
		die();
	}
}