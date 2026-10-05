<?php
use PhpOffice\PhpSpreadsheet\Style\Fill;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: all.inc.php,v 1.94.2.1 2024/12/30 15:15:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $include_path, $msg, $lvl, $action, $dest, $id_empr, $id_groupe;
global $opac_pret_prolongation;
global $prolonge_id, $opac_show_group_checkout;

require_once($class_path."/exemplaire.class.php");
require_once($include_path."/notice_authors.inc.php");

$prolonge_id = intval($prolonge_id);
$id_groupe = intval($id_groupe);
if(!isset($action)) $action = '';
//Récupération des variables postées, on en aura besoin pour les liens
$page=$_SERVER['SCRIPT_NAME'];


if ($dest=="TABLEAU") {
	//Export excel
	require_once ($class_path."/spreadsheetPMB.class.php");
	$worksheet = new spreadsheetPMB();
	//formats
	$heading_blue = array(
		'fill' => array(
			'type' => Fill::FILL_SOLID,
            'color' => array('rgb' => '00CCFF')
		)
	);
} else {
    switch($action) {
        case 'group_prolonge_pret':
            if ($id_groupe) {
                $group = new group($id_groupe);
                $group->pret_prolonge_members();
            }
            break;
    }
	// Si click bouton de prolongation, et prolongation autorisée 
	if($prolonge_id>0 && $opac_pret_prolongation==1){
		//Il faut prolonger un livre
	    $instance_pret = new pret($id_empr, $prolonge_id);
	    if($instance_pret->is_extendable()) {
	        $nouvelle_date = extraitdate($prolongation);//la variable GET prolongation contient la date
	        //Assurons-nous que le prêt peut être prolongé et que nous ne sommes pas sur une actualisation F5
	        if($nouvelle_date != $instance_pret->pret_retour) {
	           $prolongation = $instance_pret->prolongation($nouvelle_date);
	        } else {
	            $prolongation = FALSE;
	        }
	    } else {
	        $prolongation = FALSE;
	        echo $instance_pret->no_prolong_explanation . "<br />";
	    }	
	}
}
	
// REQUETE SQL

$sql = "SELECT notices_m.notice_id as num_notice_mono, bulletin_id, IF(pret_retour>sysdate(),0,1) as retard, expl_id," ;
$sql.= "date_format(pret_retour, '".$msg["format_date_sql"]."') as aff_pret_retour, pret_retour, "; 
$sql.= "date_format(pret_date, '".$msg["format_date_sql"]."') as aff_pret_date, " ;
$sql.= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '".$msg["format_date_sql"]."'),')') ,'')))) as tit, "; 
$sql.= "if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id, if(notices_m.tparent_id, notices_m.tparent_id, notices_s.tparent_id) as tparent_id, ifnull(notices_m.tnvol , notices_s.tnvol) as tnvol, ";
$sql.= "tdoc_libelle, empr_location, location_libelle ";
$sql.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
$sql.= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
$sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
$sql.= "        docs_type, docs_location , pret left join pnb_orders_expl on pnb_orders_expl.pnb_order_expl_num=pret.pret_idexpl, empr ";
$sql.= "WHERE expl_typdoc = idtyp_doc and pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr and expl_location = idlocation and pnb_orders_expl.pnb_order_expl_num is null";
$sql.= $critere_requete;

$req = pmb_mysql_query($sql) or die("Erreur SQL !<br />".$sql."<br />".pmb_mysql_error()); 
$nb_elements = pmb_mysql_num_rows($req) ;

if (!$dest) {
    global $opac_cart_allow, $opac_empr_export_loans;
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
	    print list_opac_loans_reader_ui::get_instance(array('empr_login' => $login))->get_display_list();
	} elseif ($dest=="TABLEAU") {
	    list_opac_loans_reader_ui::get_instance(array('empr_login' => $login))->get_display_spreadsheet_list();
	} 
	
} else { // fin du if nb_elements
	switch($lvl) {
		case 'all':	
			if(!$dest){
				print '<br><p class="noLoan">'.$msg["empr_no_loan"].'</p>' ;
			}elseif ($dest=="TABLEAU") {
				$worksheet->write(0,0,$msg["empr_no_loan"],$heading_blue);
			}
			break;
		case 'late':
			if(!$dest){
				print '<br>'.$msg["empr_no_late"] ;
			}elseif ($dest=="TABLEAU") {
				$worksheet->write(0,0,$msg["empr_no_late"],$heading_blue);
			}
			break;
	}
}
if($opac_show_group_checkout) {
    aff_pret_groupes();
}

if(file_exists($base_path."/empr/all_extended.inc.php")) {
    require_once($base_path."/empr/all_extended.inc.php");
}

if ($dest=="TABLEAU") {
	$worksheet->download('empr.xls');
	die();
}

function aff_pret_groupes(){
	global $msg,$id_empr,$lvl;
	global $dest,$worksheet;
	global $heading_blue;
	
	$req_groupes="SELECT * from groupe where resp_groupe=$id_empr order by libelle_groupe";
	$res = pmb_mysql_query($req_groupes);		

	while ($r_goupe = pmb_mysql_fetch_object($res)) {
		if ($lvl=="late"){
			$critere_requete=" AND pret_retour < '".date('Y-m-d')."' ORDER BY location_libelle, empr_nom, empr_prenom, pret_retour";
		}else{
			$critere_requete=" ORDER BY location_libelle, empr_nom, empr_prenom, pret_retour";
		}
		
		$sql = "SELECT notices_m.notice_id as num_notice_mono, bulletin_id, IF(pret_retour>sysdate(),0,1) as retard, expl_id, empr.id_empr as emprunteur, " ;
		$sql.= "date_format(pret_retour, '".$msg["format_date_sql"]."') as aff_pret_retour, pret_retour, "; 
		$sql.= "date_format(pret_date, '".$msg["format_date_sql"]."') as aff_pret_date, " ;
		$sql.= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '".$msg["format_date_sql"]."'),')') ,'')))) as tit, ";
		$sql.= "if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id, if(notices_m.tparent_id, notices_m.tparent_id, notices_s.tparent_id) as tparent_id, ifnull(notices_m.tnvol , notices_s.tnvol) as tnvol, ";
		$sql.= "tdoc_libelle, location_libelle ";
		$sql.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) ";
		$sql.= "        LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ";
		$sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), ";
		$sql.= "        docs_type, docs_location , pret, empr,empr_groupe  ";
		$sql.= "WHERE expl_typdoc = idtyp_doc and pret_idexpl = expl_id  and empr.id_empr = pret.pret_idempr and empr_groupe.empr_id = empr.id_empr and expl_location = idlocation and groupe_id=". $r_goupe->id_groupe;
		$sql.= $critere_requete;
	
		$req = pmb_mysql_query($sql) or die("Erreur SQL !<br />".$sql."<br />".pmb_mysql_error()); 
		$nb_elements = pmb_mysql_num_rows($req) ;
		
		if ($nb_elements) {
		    list_opac_loans_groups_reader_ui::set_id_group($r_goupe->id_groupe);
		    $list_opac_loans_groups_reader_ui = list_opac_loans_groups_reader_ui::get_instance(array('groups' => array($r_goupe->id_groupe)));
			if (!$dest) {
				echo "<br>";
				if(count($list_opac_loans_groups_reader_ui->get_objects())) {
				    echo $list_opac_loans_groups_reader_ui->get_display_list();
				}
			} else {
			    $list_opac_loans_groups_reader_ui->set_spreadsheet($worksheet);
			    $list_opac_loans_groups_reader_ui->add_spreadsheet_line(2);
			    $list_opac_loans_groups_reader_ui->get_display_spreadsheet_list();
			}
		}
	}
}

function get_info_empr($id){
    $id = intval($id);
	$req="SELECT * FROM empr, docs_location 
	where id_empr=$id and empr_location=idlocation ";
	
	$info_eleve=array();
	$resultat=pmb_mysql_query($req);
	if($r=pmb_mysql_fetch_object($resultat)) {
		$info_eleve['id']=$id;
		$info_eleve['nom']=$r->empr_nom;
		$info_eleve['prenom']=$r->empr_prenom;
		$info_eleve['location_libelle']=translation::get_translated_text($r->idlocation, "docs_location", "location_libelle",$r->location_libelle);
		
	}
	return $info_eleve;
}

function sql_value($rqt) {
	$result=pmb_mysql_query($rqt);
	$row = pmb_mysql_fetch_row($result);
	return $row[0];
}
