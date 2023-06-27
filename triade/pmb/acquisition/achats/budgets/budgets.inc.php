<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: budgets.inc.php,v 1.16 2019-06-05 06:41:21 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $acquisition_no_html, $include_path, $msg, $charset, $action, $id_bibli, $id_bud;

// gestion des budgets
require_once("$class_path/entites.class.php");
require_once("$class_path/exercices.class.php");
require_once("$class_path/budgets.class.php");
require_once("$class_path/rubriques.class.php");
if (!$acquisition_no_html) {
	require_once("$include_path/templates/budgets.tpl.php");
}

//globalisé
$line=0;

//Affiche la liste des budgets
function show_list_bud($id_bibli) {
	global $msg, $charset;
	
	//Affichage du formulaire de recherche
	show_search_form($id_bibli);
	
	//Affichage de la liste des budgets
	$form = "<table>
	<tr>
		<th>".htmlentities($msg[103],ENT_QUOTES,$charset)."</th>
		<th>".htmlentities($msg['acquisition_statut'],ENT_QUOTES,$charset)."</th>
		<th>".htmlentities($msg['acquisition_budg_exer'],ENT_QUOTES,$charset)."</th>
	</tr>";

	$q = budgets::listByEntite($id_bibli);
	$r = pmb_mysql_query($q);
	$nb = pmb_mysql_num_rows($r);

	$parity=1;
	for($i=0;$i<$nb;$i++) {
		$row=pmb_mysql_fetch_object($r);
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./acquisition.php?categ=ach&sub=bud&action=show&id_bibli=$row->num_entite&id_bud=$row->id_budget';\" ";
	        $form.="<tr class='$pair_impair' $tr_javascript style='cursor: pointer'><td><i>".htmlentities($row->libelle, ENT_QUOTES, $charset)."</i></td>";
	        $form.='<td>';
	        switch ($row->statut) {
	        	case STA_BUD_VAL :
	        		$form.=htmlentities($msg['acquisition_statut_actif'],ENT_QUOTES,$charset) ;
	        		break;
	        	case  STA_BUD_CLO :
	        		$form.=htmlentities($msg['acquisition_statut_clot'],ENT_QUOTES,$charset) ;
	        		break;
	        	default:
	        		$form.=htmlentities($msg['acquisition_budg_pre'],ENT_QUOTES,$charset) ;
	        		break;
	        }
			$form.="</td>";
			
			$exer = new exercices($row->num_exercice);
			$form.='<td>'.htmlentities($exer->libelle, ENT_QUOTES, $charset)."</td></tr>";
	}
	$form.="</table>";
	
	print $form;
}

//Affiche le formulaire de recherche
function show_search_form($id_bibli) {
	global $msg, $charset;
	global $search_form;	
	global $tab_bib;
	
	$form = $search_form;
	$titre = htmlentities($msg['acquisition_voir_bud'], ENT_QUOTES, $charset);
	
	//Creation selecteur etablissement
	$sel_bibli ="<select class='saisie-50em' id='id_bibli' name='id_bibli' onchange=\"document.forms['search'].setAttribute('action', './acquisition.php?categ=ach&sub=bud&action=list');document.forms['search'].submit(); \" >";
	foreach($tab_bib[0] as $k=>$v) {
		$sel_bibli.="<option value='".$v."' ";
		if($v==$id_bibli) $sel_bibli.="selected='selected' ";
		$sel_bibli.=">".htmlentities($tab_bib[1][$k], ENT_QUOTES, $charset)."</option>";
	}
	$sel_bibli.="</select>";

	$form=str_replace('!!form_title!!', $titre , $form);
	$form=str_replace('<!-- sel_bibli -->', $sel_bibli, $form);
	print $form;
}

//Affiche le formulaire d'un budget
function show_bud($id_bibli=0, $id_bud=0) {
	global $msg, $charset;
	global $view_bud_form;
	global $view_lig_rub_form, $lig_rub_img, $view_tot_rub_form;
	global $pmb_gestion_devise;
	global $acquisition_gestion_tva;
	
	if (!$id_bibli || !$id_bud) return;

	show_search_form($id_bibli);
	
	//Recuperation budget
	$bud= new budgets($id_bud);
	switch ($acquisition_gestion_tva) {
		case '0' :
		case '2' :
			$htttc=htmlentities($msg['acquisition_ttc'], ENT_QUOTES, $charset);
			$k_htttc='ttc';
			$k_htttc_autre='ht';
			break;
		default:
			$htttc=htmlentities($msg['acquisition_ht'], ENT_QUOTES, $charset);
			$k_htttc='ht';
			$k_htttc_autre='ttc';
			break;
	}
	
	//montant total pour budget par rubriques
	if ($bud->type_budget == TYP_BUD_GLO) {
		$mnt['tot'][$k_htttc] = $bud->montant_global;
		$totaux = array('tot'=>$mnt['tot'][$k_htttc], 'ava'=>0, 'eng'=>0, 'fac'=>0, 'pay'=>0, 'sol'=>0);
		$totaux_autre = array('tot'=>0, 'ava'=>0, 'eng'=>0, 'fac'=>0, 'pay'=>0, 'sol'=>0);
	} else {
		$totaux = array('tot'=>0, 'ava'=>0, 'eng'=>0, 'fac'=>0, 'pay'=>0, 'sol'=>0);
		$totaux_autre = array('tot'=>0, 'ava'=>0, 'eng'=>0, 'fac'=>0, 'pay'=>0, 'sol'=>0);
	}

	switch ($bud->statut) {
		case STA_BUD_VAL :
			$sta_bud = htmlentities($msg['acquisition_statut_actif'],ENT_QUOTES,$charset);
			break;
		case STA_BUD_CLO :
			$sta_bud = htmlentities($msg['acquisition_statut_clot'],ENT_QUOTES,$charset);
			break;
		case STA_BUD_PRE :
		default :
			$sta_bud = htmlentities($msg['acquisition_budg_pre'],ENT_QUOTES,$charset);
			break;	
	}
	
	//Recuperation exercice
	$exer = new exercices($bud->num_exercice);

	$form = $view_bud_form;
	
	$form = str_replace('!!lib_bud!!', htmlentities($bud->libelle, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!lib_exer!!', htmlentities($exer->libelle, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!mnt_bud!!', number_format($bud->montant_global,'2','.',' '), $form);
	$form = str_replace('!!devise!!', $pmb_gestion_devise, $form);
	$form = str_replace('!!htttc!!', $htttc, $form);
	if(!$bud->type_budget) {
		$form = str_replace('!!typ_bud!!', htmlentities($msg['acquisition_budg_aff_rub'], ENT_QUOTES, $charset), $form);
	} else {
		$form = str_replace('!!typ_bud!!', htmlentities($msg['acquisition_budg_aff_glo'], ENT_QUOTES, $charset), $form);
	}
	
	$form = str_replace('!!sta_bud!!', $sta_bud, $form);
	$form = str_replace('!!seu_bud!!', $bud->seuil_alerte, $form);
	
	//recuperation de la liste complete des rubriques
	$q = budgets::listRubriques($id_bud, 0);	
	$list_n1 = pmb_mysql_query($q); 
	while(($row=pmb_mysql_fetch_object($list_n1))) {
		$form = str_replace('<!-- rubriques -->', $view_lig_rub_form.'<!-- rubriques -->', $form);
		$form = str_replace('<!-- marge -->', '', $form);
		$nb_sr = rubriques::countChilds($row->id_rubrique);
		if ($nb_sr) {
			$form = str_replace('<!-- img_plus -->', $lig_rub_img, $form);
		} else {
			$form = str_replace('<!-- img_plus -->', '', $form);
		}
		$form = str_replace('!!id_rub!!', $row->id_rubrique, $form);
		$form = str_replace('!!id_parent!!', $row->num_parent, $form);			
		$form = str_replace('!!lib_rub!!', htmlentities($row->libelle, ENT_QUOTES, $charset), $form);
		
		//montant total pour budget par rubriques
		$mnt['tot'][$k_htttc] = $row->montant;
		//montant a valider
		$mnt['ava'] = rubriques::calcAValider($row->id_rubrique);
		//montant engage
		$mnt['eng'] = rubriques::calcEngage($row->id_rubrique);
		//montant facture
		$mnt['fac'] = rubriques::calcFacture($row->id_rubrique);
		//montant paye
		$mnt['pay'] = rubriques::calcPaye($row->id_rubrique);
		//solde
		$mnt['sol'][$k_htttc]=$mnt['tot'][$k_htttc]-$mnt['eng'][$k_htttc];  
			
		foreach($totaux as $k=>$v) {
			$totaux[$k]=$v+$mnt[$k][$k_htttc];
		}
	
		foreach($totaux_autre as $k=>$v) {
			if(!isset($mnt[$k][$k_htttc_autre])) $mnt[$k][$k_htttc_autre] = 0;
			$totaux_autre[$k]=$v+$mnt[$k][$k_htttc_autre];
		}
		
		$lib_mnt=array();
		foreach($mnt as $k=>$v) {
			$lib_mnt[$k]=number_format($mnt[$k][$k_htttc],2,'.',' ');
			if($acquisition_gestion_tva && $k!="tot" && $k!="sol") {
				$lib_mnt_autre[$k]=number_format($mnt[$k][$k_htttc_autre],2,'.',' ');
			}
		}
		if ($bud->type_budget == TYP_BUD_GLO ) {
			$lib_mnt['tot']='&nbsp;';
			$lib_mnt['sol']='&nbsp;';			
		}
		foreach($lib_mnt as $k=>$v) {
			if(!$acquisition_gestion_tva || !$lib_mnt_autre[$k]){
				$form = str_replace('!!mnt_'.$k.'!!', $lib_mnt[$k], $form);
			} elseif($acquisition_gestion_tva) {
				$form = str_replace('!!mnt_'.$k.'!!', $lib_mnt[$k]."<br />".$lib_mnt_autre[$k], $form);
			}
			
		}
		
		if($nb_sr) {
			$form = str_replace('<!-- sous_rub -->', '<!-- sous_rub'.$row->id_rubrique.' -->', $form);
			afficheSousRubriques($bud, $row->id_rubrique, $form, 1);
		} else {
			$form = str_replace('<!-- sous_rub -->', '', $form);
		}
	}
	$form = str_replace('<!-- totaux -->', $view_tot_rub_form, $form);
	if($bud->type_budget==TYP_BUD_GLO){
		$totaux['tot']=$bud->montant_global;
		$totaux['sol']=$totaux['tot']-$totaux['eng'];
	}
	foreach($totaux as $k=>$v) {
		if(is_numeric($v)) {
			$totaux[$k]=number_format($v,2,'.',' ');			
		} else {
			$totaux[$k]='&nbsp;';
		}
	}
	foreach($totaux_autre as $k=>$v) {
		if(is_numeric($v) && $k!='tot' && $k!='sol') {
			$totaux_autre[$k]=number_format($v,2,'.',' ');
		} else {
			$totaux_autre[$k]='&nbsp;';
		}
	}
	foreach($totaux as $k=>$v) {
		$form = str_replace('!!mnt_'.$k.'!!', $totaux[$k].(($acquisition_gestion_tva)?'<br />'.$totaux_autre[$k]:''), $form);
	}
	
	$form = str_replace('!!id_bibli!!', $id_bibli, $form);
	$form = str_replace('!!id_bud!!', $id_bud, $form);
	
	print $form;	
}

function print_bud($id_bibli=0, $id_bud=0) {
	global $msg, $charset;
	global $pmb_gestion_devise;
	global $acquisition_gestion_tva;
	global $base_path,$class_path;
	global $line;

	if (!$id_bibli || !$id_bud) return;

	//Export excel
	require_once ($class_path."/spreadsheetPMB.class.php");
	$worksheet = new spreadsheetPMB();
	$bold = array(
		'font' => array(
			'bold' => true
		)
	);

	//Recuperation budget
	$bud= new budgets($id_bud);
	
	switch ($acquisition_gestion_tva) {
		case '0' :
		case '2' :
			$htttc=$msg['acquisition_ttc'];
			$k_htttc='ttc';
			$k_htttc_autre='ht';
			break;
		default:
			$htttc=$msg['acquisition_ht'];
			$k_htttc='ht';
			$k_htttc_autre='ttc';
			break;
	}
	
	//montant total pour budget par rubriques
	if ($bud->type_budget == TYP_BUD_GLO) {
		$mnt['tot'][$k_htttc] = $bud->montant_global;
		$totaux = array('tot'=>$mnt['tot'][$k_htttc], 'ava'=>0, 'eng'=>0, 'fac'=>0, 'pay'=>0, 'sol'=>0);
		$totaux_autre = array('tot'=>0, 'ava'=>0, 'eng'=>0, 'fac'=>0, 'pay'=>0, 'sol'=>0);
	} else {
		$totaux = array('tot'=>0, 'ava'=>0, 'eng'=>0, 'fac'=>0, 'pay'=>0, 'sol'=>0);
		$totaux_autre = array('tot'=>0, 'ava'=>0, 'eng'=>0, 'fac'=>0, 'pay'=>0, 'sol'=>0);
	}
	
	switch ($bud->statut) {
		case STA_BUD_VAL :
			$sta_bud = $msg['acquisition_statut_actif'];
			break;
		case STA_BUD_CLO :
			$sta_bud = $msg['acquisition_statut_clot'];
			break;
		case STA_BUD_PRE :
		default :
			$sta_bud = $msg['acquisition_budg_pre'];
			break;
	}
	$seu_bud = $bud->seuil_alerte;
	
	//Recuperation exercice
	$exer = new exercices($bud->num_exercice);
	
	$worksheet->write($line,0,$msg['acquisition_bud'],$bold);
	$worksheet->write($line,1,$bud->libelle);
	$worksheet->write($line,2,$msg['acquisition_budg_montant'],$bold);
	//problème du symbole euro à faire passer en encodage iso...
	$worksheet->write($line,3,number_format($bud->montant_global,'2','.','')." ".($charset=="utf-8"?html_entity_decode(stripslashes($pmb_gestion_devise)):mb_convert_encoding(html_entity_decode(stripslashes($pmb_gestion_devise)),"windows-1252","utf-8"))." ".$htttc);
	
	$line++;
	
	$worksheet->write($line,0,$msg['acquisition_budg_exer'],$bold);
	$worksheet->write($line,1,$exer->libelle);
	$worksheet->write($line,2,$msg['acquisition_budg_aff_lib'],$bold);
	if(!$bud->type_budget) {
		$worksheet->write($line,3,$msg['acquisition_budg_aff_rub']);
	} else {
		$worksheet->write($line,3,$msg['acquisition_budg_aff_glo']);
	}
	
	$line++;
	
	$worksheet->write($line,0,$msg['acquisition_statut'],$bold);
	$worksheet->write($line,1,$sta_bud);
	$worksheet->write($line,2,$msg['acquisition_budg_seuil'],$bold);
	$worksheet->write($line,3,$seu_bud." %");
	
	$line+=2;
	
	if ($acquisition_gestion_tva==1) {
		$worksheet->write($line,0,$msg['acquisition_rub'],$bold);
		$worksheet->write($line,1,$msg['acquisition_rub_mnt_tot'],$bold);
		$worksheet->write($line,2,$msg['acquisition_rub_mnt_ava_ht'],$bold);
		$worksheet->write($line,3,$msg['acquisition_rub_mnt_eng_ht'],$bold);
		$worksheet->write($line,4,$msg['acquisition_rub_mnt_fac_ht'],$bold);
		$worksheet->write($line,5,$msg['acquisition_rub_mnt_pay_ht'],$bold);
		$worksheet->write($line,6,$msg['acquisition_rub_mnt_sol'],$bold);
	} elseif ($acquisition_gestion_tva==2) {
		$worksheet->write($line,0,$msg['acquisition_rub'],$bold);
		$worksheet->write($line,1,$msg['acquisition_rub_mnt_tot'],$bold);
		$worksheet->write($line,2,$msg['acquisition_rub_mnt_ava_ttc'],$bold);
		$worksheet->write($line,3,$msg['acquisition_rub_mnt_eng_ttc'],$bold);
		$worksheet->write($line,4,$msg['acquisition_rub_mnt_fac_ttc'],$bold);
		$worksheet->write($line,5,$msg['acquisition_rub_mnt_pay_ttc'],$bold);
		$worksheet->write($line,6,$msg['acquisition_rub_mnt_sol'],$bold);
	} else {
		$worksheet->write($line,0,$msg['acquisition_rub'],$bold);
		$worksheet->write($line,1,$msg['acquisition_rub_mnt_tot'],$bold);
		$worksheet->write($line,2,$msg['acquisition_rub_mnt_ava'],$bold);
		$worksheet->write($line,3,$msg['acquisition_rub_mnt_eng'],$bold);
		$worksheet->write($line,4,$msg['acquisition_rub_mnt_fac'],$bold);
		$worksheet->write($line,5,$msg['acquisition_rub_mnt_pay'],$bold);
		$worksheet->write($line,6,$msg['acquisition_rub_mnt_sol'],$bold);
	}
	
	$q = budgets::listRubriques($id_bud, 0);
	$list_n1 = pmb_mysql_query($q);
	while(($row=pmb_mysql_fetch_object($list_n1))) {
		//montant total pour budget par rubriques
		$mnt['tot'][$k_htttc] = $row->montant;
		//montant a valider
		$mnt['ava'] = rubriques::calcAValider($row->id_rubrique);
		//montant engage
		$mnt['eng'] = rubriques::calcEngage($row->id_rubrique);
		//montant facture
		$mnt['fac'] = rubriques::calcFacture($row->id_rubrique);
		//montant paye
		$mnt['pay'] = rubriques::calcPaye($row->id_rubrique);
		//solde
		$mnt['sol'][$k_htttc]=$mnt['tot'][$k_htttc]-$mnt['eng'][$k_htttc];
		
		foreach($totaux as $k=>$v) {
			$totaux[$k]=$v+$mnt[$k][$k_htttc];
		}
		
		foreach($totaux_autre as $k=>$v) {
			if(!isset($mnt[$k][$k_htttc_autre])) $mnt[$k][$k_htttc_autre] = 0;				
			$totaux_autre[$k]=$v+$mnt[$k][$k_htttc_autre];
		}
		
		$lib_mnt=array();
		foreach($mnt as $k=>$v) {
			$lib_mnt[$k]=number_format($mnt[$k][$k_htttc],2,'.','');
			if($acquisition_gestion_tva && $k!="tot" && $k!="sol") {
				$lib_mnt_autre[$k]=number_format($mnt[$k][$k_htttc_autre],2,'.','');
			}
		}

		if ($bud->type_budget == TYP_BUD_GLO ) {
			$lib_mnt['tot']='';
			$lib_mnt['sol']='';
		}
		
		$line++;
		$worksheet->write($line,0,$row->libelle);
		$worksheet->write($line,1,$lib_mnt["tot"]);
		$worksheet->write($line,2,$lib_mnt["ava"]);
		$worksheet->write($line,3,$lib_mnt["eng"]);
		$worksheet->write($line,4,$lib_mnt["fac"]);
		$worksheet->write($line,5,$lib_mnt["pay"]);
		$worksheet->write($line,6,$lib_mnt["sol"]);
		
		if($acquisition_gestion_tva) {
			$line++;
			if ($lib_mnt_autre["tot"]) {
				$worksheet->write($line,1,$lib_mnt_autre["tot"]);
			}
			if ($lib_mnt_autre["ava"]) {
				$worksheet->write($line,2,$lib_mnt_autre["ava"]);
			}
			if ($lib_mnt_autre["eng"]) {
				$worksheet->write($line,3,$lib_mnt_autre["eng"]);
			}
			if ($lib_mnt_autre["fac"]) {
				$worksheet->write($line,4,$lib_mnt_autre["fac"]);
			}
			if ($lib_mnt_autre["pay"]) {
				$worksheet->write($line,5,$lib_mnt_autre["pay"]);
			}
			if ($lib_mnt_autre["sol"]) {
				$worksheet->write($line,6,$lib_mnt_autre["sol"]);
			}
		}
				
		//Sous-rubriques
		$nb_sr = rubriques::countChilds($row->id_rubrique);
		if ($nb_sr) {
			printSousRubriques($bud, $row->id_rubrique, $worksheet, 1);
		}
	}

	//recuperation de la liste complete des rubriques
	if($bud->type_budget==TYP_BUD_GLO){
		$totaux['tot']=$bud->montant_global;
		$totaux['sol']=$totaux['tot']-$totaux['eng'];
	}
	foreach($totaux as $k=>$v) {
		if(is_numeric($v)) {
			$totaux[$k]=number_format($v,2,'.','');
		} else {
			$totaux[$k]=' ';
		}
	}
	
	foreach($totaux_autre as $k=>$v) {
		if(is_numeric($v) && $k!='tot' && $k!='sol') {
			$totaux_autre[$k]=number_format($v,2,'.','');
		} else {
			$totaux_autre[$k]=' ';
		}
	}
	
	$line+=2;
	
	$worksheet->write($line,0,$msg["acquisition_budg_montant"],$bold);
	$worksheet->write($line,1,$totaux["tot"],$bold);
	$worksheet->write($line,2,$totaux["ava"],$bold);
	$worksheet->write($line,3,$totaux["eng"],$bold);
	$worksheet->write($line,4,$totaux["fac"],$bold);
	$worksheet->write($line,5,$totaux["pay"],$bold);
	$worksheet->write($line,6,$totaux["sol"],$bold);
	
	if ($acquisition_gestion_tva) {
		$line++;
		$worksheet->write($line,1,$totaux_autre["tot"],$bold);
		$worksheet->write($line,2,$totaux_autre["ava"],$bold);
		$worksheet->write($line,3,$totaux_autre["eng"],$bold);
		$worksheet->write($line,4,$totaux_autre["fac"],$bold);
		$worksheet->write($line,5,$totaux_autre["pay"],$bold);
		$worksheet->write($line,6,$totaux_autre["sol"],$bold);
	}
	
	//Final
	$worksheet->download('Budget.xls');
	die();
}

//Export excel des sous-rubriques d'une rubrique
function printSousRubriques($bud, $id_rub, &$worksheet, $indent=0) {
	global $msg, $charset;
	global $acquisition_gestion_tva,$line;

	switch ($acquisition_gestion_tva) {
		case '0' :;
		case '2' :
			$htttc=$msg['acquisition_ttc'];
			$k_htttc='ttc';
			$k_htttc_autre='ht';
			break;
		default:
			$htttc=$msg['acquisition_ht'];
			$k_htttc='ht';
			$k_htttc_autre='ttc';
			break;
	}
	$id_bud = $bud->id_budget;
	$q = budgets::listRubriques($id_bud, $id_rub);
	$list_n = pmb_mysql_query($q);
	while(($row=pmb_mysql_fetch_object($list_n))){
				
		$marge = '';
		for($i=0;$i<$indent;$i++){
			$marge.= "      ";
		}
		
		//montant total
		$mnt['tot'][$k_htttc]=$row->montant;
		//montant a valider
		$mnt['ava'] = rubriques::calcAValider($row->id_rubrique);
		//montant engage
		$mnt['eng'] = rubriques::calcEngage($row->id_rubrique);
		//montant facture
		$mnt['fac'] = rubriques::calcFacture($row->id_rubrique);
		//montant paye
		$mnt['pay'] = rubriques::calcPaye($row->id_rubrique);
		//solde
		$mnt['sol'][$k_htttc]=$mnt['tot'][$k_htttc]-$mnt['eng'][$k_htttc];
		$lib_mnt=array();
		foreach($mnt as $k=>$v) {
			$lib_mnt[$k]=number_format($mnt[$k][$k_htttc],2,'.','');
			if($acquisition_gestion_tva && $k!="tot" && $k!="sol") {
				$lib_mnt_autre[$k]=number_format($mnt[$k][$k_htttc_autre],2,'.','');
			}
		}
		if ($bud->type_budget == TYP_BUD_GLO ) {
			$lib_mnt['tot']='';
			$lib_mnt['sol']='';
		}
		
		$line++;
		$worksheet->write($line,0,$marge.$row->libelle);
		$worksheet->write($line,1,$lib_mnt["tot"]);
		$worksheet->write($line,2,$lib_mnt["ava"]);
		$worksheet->write($line,3,$lib_mnt["eng"]);
		$worksheet->write($line,4,$lib_mnt["fac"]);
		$worksheet->write($line,5,$lib_mnt["pay"]);
		$worksheet->write($line,6,$lib_mnt["sol"]);
		
		if($acquisition_gestion_tva) {
			$line++;
			if ($lib_mnt_autre["tot"]) {
				$worksheet->write($line,1,$lib_mnt_autre["tot"]);
			}
			if ($lib_mnt_autre["ava"]) {
				$worksheet->write($line,2,$lib_mnt_autre["ava"]);
			}
			if ($lib_mnt_autre["eng"]) {
				$worksheet->write($line,3,$lib_mnt_autre["eng"]);
			}
			if ($lib_mnt_autre["fac"]) {
				$worksheet->write($line,4,$lib_mnt_autre["fac"]);
			}
			if ($lib_mnt_autre["pay"]) {
				$worksheet->write($line,5,$lib_mnt_autre["pay"]);
			}
			if ($lib_mnt_autre["sol"]) {
				$worksheet->write($line,6,$lib_mnt_autre["sol"]);
			}
		}
		
		$nb_sr = rubriques::countChilds($row->id_rubrique);
		if ($nb_sr) {
			printSousRubriques($bud, $row->id_rubrique, $worksheet, $indent+1);
		}
	}
}

//Affiche les sous-rubriques d'une rubrique
function afficheSousRubriques($bud, $id_rub, &$form, $indent=0) {
	global $charset;
	global $view_lig_rub_form, $lig_rub_img, $lig_indent;
	global $acquisition_gestion_tva;

	switch ($acquisition_gestion_tva) {
		case '0' :;
		case '2' :
			$htttc=htmlentities($msg['acquisition_ttc'], ENT_QUOTES, $charset);
			$k_htttc='ttc';
			$k_htttc_autre='ht';
			break;
		default:
			$htttc=htmlentities($msg['acquisition_ht'], ENT_QUOTES, $charset);
			$k_htttc='ht';
			$k_htttc_autre='ttc';
			break;
	}
	$id_bud = $bud->id_budget;
	$q = budgets::listRubriques($id_bud, $id_rub);
	$list_n = pmb_mysql_query($q); 
	while(($row=pmb_mysql_fetch_object($list_n))){
		$form = str_replace('<!-- sous_rub'.$id_rub.' -->', $view_lig_rub_form.'<!-- sous_rub'.$id_rub.' -->', $form);
		$marge = '';
		for($i=0;$i<$indent;$i++){
			$marge.= $lig_indent;
		}
		$form = str_replace('<!-- marge -->', $marge, $form);
		
		$nb_sr = rubriques::countChilds($row->id_rubrique);
		if ($nb_sr) {
			$form = str_replace('<!-- img_plus -->', $lig_rub_img, $form);
		} else {
			$form = str_replace('<!-- img_plus -->', '', $form);
		}
		$form = str_replace('<!-- sous_rub -->', '<!-- sous_rub'.$row->id_rubrique.' -->', $form);
		$form = str_replace('!!id_rub!!', $row->id_rubrique, $form);
		$form = str_replace('!!id_parent!!', $row->num_parent, $form);
		$libelle = htmlentities($row->libelle, ENT_QUOTES, $charset);
		$form = str_replace('!!lib_rub!!', $libelle, $form);

		//montant total
		$mnt['tot'][$k_htttc]=$row->montant;
		//montant a valider
		$mnt['ava'] = rubriques::calcAValider($row->id_rubrique);
		//montant engage
		$mnt['eng'] = rubriques::calcEngage($row->id_rubrique);
		//montant facture
		$mnt['fac'] = rubriques::calcFacture($row->id_rubrique);
		//montant paye
		$mnt['pay'] = rubriques::calcPaye($row->id_rubrique);
		//solde 
		$mnt['sol'][$k_htttc]=$mnt['tot'][$k_htttc]-$mnt['eng'][$k_htttc];
		$lib_mnt=array();		
		foreach($mnt as $k=>$v) {
			$lib_mnt[$k]=number_format($mnt[$k][$k_htttc],2,'.',' ');
			if($acquisition_gestion_tva && $k!="tot" && $k!="sol") {
				$lib_mnt_autre[$k]=number_format($mnt[$k][$k_htttc_autre],2,'.',' ');
			}
		}
		if ($bud->type_budget == TYP_BUD_GLO ) {
			$lib_mnt['tot']='&nbsp;';
			$lib_mnt['sol']='&nbsp;';			
		}
		foreach($lib_mnt as $k=>$v) {
			if(!$acquisition_gestion_tva || !$lib_mnt_autre[$k]) {
				$form = str_replace('!!mnt_'.$k.'!!', $lib_mnt[$k], $form);
			} elseif($acquisition_gestion_tva) {
				$form = str_replace('!!mnt_'.$k.'!!', $lib_mnt[$k]."<br />".$lib_mnt_autre[$k], $form);
			}
			
		}							
		if ($nb_sr) {
			afficheSousRubriques($bud, $row->id_rubrique, $form, $indent+1);
		}
	}
}

//Traitement des actions
if (!$acquisition_no_html) {
	print "<h1>".htmlentities($msg['acquisition_ach_ges'],ENT_QUOTES, $charset)."&nbsp;:&nbsp;".htmlentities($msg['acquisition_menu_ref_budget'],ENT_QUOTES, $charset)."</h1>";
}

switch($action) {
	case 'list':
		entites::setSessionBibliId($id_bibli);
		show_list_bud($id_bibli);
		break;
	case 'show':
		show_bud($id_bibli, $id_bud);
		break;
	case 'print_budget':
		print_bud($id_bibli, $id_bud);
		break;
	default:
		print entites::show_list_biblio('show_list_bud');	
		break;
}
?>
