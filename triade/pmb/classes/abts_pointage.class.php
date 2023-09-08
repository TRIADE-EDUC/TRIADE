<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_pointage.class.php,v 1.87 2019-06-07 06:59:23 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

require_once ($include_path . "/templates/abts_pointage.tpl.php");
require_once ($class_path . "/serial_display.class.php");
require_once ($include_path . "/abts_func.inc.php");
require_once ($include_path . "/misc.inc.php");
require_once ($class_path . "/parse_format.class.php");
require_once ($class_path.'/entites.class.php');
require_once($base_path."/classes/rtf/Rtf.php");
require_once($base_path."/classes/fpdf.class.php");
require_once($base_path."/classes/ufpdf.class.php");
require_once("$class_path/coordonnees.class.php");
require_once($class_path."/cache_factory.class.php");
require_once($class_path."/abts_status.class.php");
	
class abts_pointage {
	public $num_notice; //notice id
	public $print_mode = 0; //0 : rtf, 1 : pdf
	public $error; //Erreur
	public $error_message; //Message d'erreur
	public $liste_rel=array(); 
	
	public function __construct($notice_id = "") {
		global $msg,$dbh;
		
		//Verif de l'id de la notice 
		if ($notice_id) {
			$this->num_notice = 0;
			$requete = "select niveau_biblio from notices where notice_id=" . $notice_id;
			$resultat = pmb_mysql_query($requete,$dbh);
			if (pmb_mysql_result($resultat, 0, 0) == "s")
				$this->num_notice = $notice_id;

			else {
				$this->error = true;
				$this->error_message = $msg["pointage_message_no_serial"];
			}

		}
	}

	public function getData() {

	}

	public function get_bulletinage($clause_filter="",$order=" date_parution,tit1,ordre,abt_name ") {
		global $msg;
		global $dbh;
		global $pointage_form, $pointage_list;
		global $location_view, $deflt_bulletinage_location;
		
		$this->fiche_bulletin=array();
		$print_format=new parse_format();

		if ($location_view == "") $location_view = $deflt_bulletinage_location;
		if($this->num_notice) $and_rqt_notice=" and notice_id =". $this->num_notice ;
		else $and_rqt_notice="";
		$cpt_a_recevoir = $cpt_en_retard = $cpt_en_alerte = 0;
		$numero_modele = array();

		$abts_status_where = '';
		$abts_status_ids = abts_status::get_ids_bulletinage_active();
		if(count($abts_status_ids)) {
			$abts_status_where = " and abt_status in(".implode(',', $abts_status_ids).") ";
		}
		
		$requete = "			
		SELECT id_bull,num_abt,abts_grille_abt.date_parution,modele_id,type,numero,nombre,ordre,state,fournisseur,abt_name,num_notice,location_id,tit1,index_sew,date_debut, date_fin,cote
		FROM abts_grille_abt ,abts_abts, notices
		WHERE abt_id=num_abt and notice_id= num_notice ".$abts_status_where;
		if ($location_view) $requete .= " and location_id='$location_view' ";
		$requete .= " $and_rqt_notice $clause_filter
		order by $order";

		$memo_prochain=array();
		$memo_abt_modele=array();
		
		$resultat = pmb_mysql_query($requete,$dbh);
		if ($resultat) {
			while ($r = pmb_mysql_fetch_object($resultat)) {
				$numero = $r->numero;
				$libelle_numero = $numero;
				$volume = "";
				$tome = "";
	
				if (!isset($numero_modele[$r->modele_id]) || !$numero_modele[$r->modele_id]) {
					$requete = "SELECT modele_name,num_cycle,num_combien,num_increment,num_date_unite,num_increment_date,num_depart,vol_actif,vol_increment,vol_date_unite,vol_increment_numero,vol_increment_date,vol_cycle,vol_combien,vol_depart,tom_actif,tom_increment,tom_date_unite,tom_increment_numero,tom_increment_date,tom_cycle,tom_combien,tom_depart, format_aff 
								FROM abts_modeles WHERE modele_id=$r->modele_id";
					$resultat_n = pmb_mysql_query($requete,$dbh);
					if ($r_n = pmb_mysql_fetch_object($resultat_n)) {
						$numero_modele[$r->modele_id]['modele_name'] = $r_n->modele_name;
						$numero_modele[$r->modele_id]['num_cycle'] = $r_n->num_cycle;
						$numero_modele[$r->modele_id]['num_combien'] = $r_n->num_combien;
						$numero_modele[$r->modele_id]['num_increment'] = $r_n->num_increment;
						$numero_modele[$r->modele_id]['num_date_unite'] = $r_n->num_date_unite;
						$numero_modele[$r->modele_id]['num_increment_date'] = $r_n->num_increment_date;
						$numero_modele[$r->modele_id]['num_depart'] = $r_n->num_depart;
						$numero_modele[$r->modele_id]['vol_actif'] = $r_n->vol_actif;
						$numero_modele[$r->modele_id]['vol_increment'] = $r_n->vol_increment;
						$numero_modele[$r->modele_id]['vol_date_unite'] = $r_n->vol_date_unite;
						$numero_modele[$r->modele_id]['vol_increment_numero'] = $r_n->vol_increment_numero;
						$numero_modele[$r->modele_id]['vol_increment_date'] = $r_n->vol_increment_date;
						$numero_modele[$r->modele_id]['vol_cycle'] = $r_n->vol_cycle;
						$numero_modele[$r->modele_id]['vol_combien'] = $r_n->vol_combien;
						$numero_modele[$r->modele_id]['vol_depart'] = $r_n->vol_depart;
						$numero_modele[$r->modele_id]['tom_actif'] = $r_n->tom_actif;
						$numero_modele[$r->modele_id]['tom_increment'] = $r_n->tom_increment;
						$numero_modele[$r->modele_id]['tom_date_unite'] = $r_n->tom_date_unite;
						$numero_modele[$r->modele_id]['tom_increment_numero'] = $r_n->tom_increment_numero;
						$numero_modele[$r->modele_id]['tom_increment_date'] = $r_n->tom_increment_date;
						$numero_modele[$r->modele_id]['tom_cycle'] = $r_n->tom_cycle;
						$numero_modele[$r->modele_id]['tom_combien'] = $r_n->tom_combien;
						$numero_modele[$r->modele_id]['tom_depart'] = $r_n->tom_depart;
						$numero_modele[$r->modele_id]['format_aff'] = $r_n->format_aff;
					}
					$numero_modele[$r->modele_id]['date_debut'] = $r->date_debut;
					//confection de la requette sql pour les num cyclique date
					$requette = $numero_modele[$r->modele_id]['num_increment_date'];
					if ($numero_modele[$r->modele_id]['num_date_unite'] == 1)	$requette .= " month ";
					elseif ($numero_modele[$r->modele_id]['num_date_unite'] == 2) $requette .= " year ";
					else $requette .= " day ";
					$numero_modele[$r->modele_id]['num_date_sql'] = $requette;
					$numero_modele[$r->modele_id]['num_date_fin_cycle'] = pmb_sql_value("SELECT DATE_ADD('" . $numero_modele[$r->modele_id]['date_debut'] . "', INTERVAL " . $numero_modele[$r->modele_id]['num_date_sql'] . ")");
	
					//confection de la requette sql pour les vol cyclique date
					$requette = $numero_modele[$r->modele_id]['vol_increment_date'];
					if ($numero_modele[$r->modele_id]['vol_date_unite'] == 1) $requette .= " month ";
					elseif ($numero_modele[$r->modele_id]['vol_date_unite'] == 2) $requette .= " year ";
					else $requette .= " day ";
					$numero_modele[$r->modele_id]['vol_date_sql'] = $requette;
					$numero_modele[$r->modele_id]['vol_date_fin_cycle'] = pmb_sql_value("SELECT DATE_ADD('" . $numero_modele[$r->modele_id]['date_debut'] . "', INTERVAL " . $numero_modele[$r->modele_id]['vol_date_sql'] . ")");
	
					//confection de la requette sql pour les tom cyclique date
					$requette = $numero_modele[$r->modele_id]['tom_increment_date'];
					if ($numero_modele[$r->modele_id]['tom_date_unite'] == 1) $requette .= " month ";
					elseif ($numero_modele[$r->modele_id]['tom_date_unite'] == 2) $requette .= " year ";
					else $requette .= " day ";
					$numero_modele[$r->modele_id]['tom_date_sql'] = $requette;
					$numero_modele[$r->modele_id]['tom_date_fin_cycle'] = pmb_sql_value("SELECT DATE_ADD('" . $numero_modele[$r->modele_id]['date_debut'] . "', INTERVAL " . $numero_modele[$r->modele_id]['tom_date_sql'] . ")");
					
				}		
				$obj = $r->id_bull; //ce n'est pas un id de bulletin, mais l'id dans abts_grille_abt
				
				$diff = pmb_sql_value("SELECT DATEDIFF(CURDATE(),'$r->date_parution')");
				$libelle_numero=$libelle_abonnement="";
				if($diff<0) {
					$retard=3;
				}else{		
					if( $r->type != 2){
						if (!isset($numero_modele[$r->modele_id][$r->num_abt]) || !$numero_modele[$r->modele_id][$r->num_abt]) {
							$requete = "SELECT num,vol, tome, delais,	critique FROM abts_abts_modeles WHERE modele_id=$r->modele_id and abt_id=$r->num_abt";
							$resultat_n = pmb_mysql_query($requete,$dbh);
							if ($r_abt = pmb_mysql_fetch_object($resultat_n)) {
								$numero_modele[$r->modele_id][$r->num_abt]['num'] = $r_abt->num;
								$numero_modele[$r->modele_id][$r->num_abt]['vol'] = $r_abt->vol;
								$numero_modele[$r->modele_id][$r->num_abt]['tom'] = $r_abt->tome;
								$numero_modele[$r->modele_id][$r->num_abt]['delais'] = $r_abt->delais;
								$numero_modele[$r->modele_id][$r->num_abt]['critique'] = $r_abt->critique;
								$numero_modele[$r->modele_id][$r->num_abt]['start_num'] = $r_abt->num;
								$numero_modele[$r->modele_id][$r->num_abt]['start_vol'] = $r_abt->vol;
								$numero_modele[$r->modele_id][$r->num_abt]['start_tom'] = $r_abt->tome;
								$numero_modele[$r->modele_id][$r->num_abt]['num_date_fin_cycle'] = $numero_modele[$r->modele_id]['num_date_fin_cycle'];
								$numero_modele[$r->modele_id][$r->num_abt]['vol_date_fin_cycle'] = $numero_modele[$r->modele_id]['vol_date_fin_cycle'];
								$numero_modele[$r->modele_id][$r->num_abt]['tom_date_fin_cycle'] = $numero_modele[$r->modele_id]['tom_date_fin_cycle'];
							}							
							
							$numero_modele[$r->modele_id][$r->num_abt]['date_parution'] = $r->date_parution;
							$numero_modele[$r->modele_id][$r->num_abt]['num']--;
							increment_bulletin($r->modele_id, $numero_modele[$r->modele_id],$r->num_abt);							
							$numero_modele[$r->modele_id][$r->num_abt]['ordre'] = $r->ordre;
							
						} elseif (($numero_modele[$r->modele_id][$r->num_abt]['date_parution'] != $r->date_parution) || ($numero_modele[$r->modele_id][$r->num_abt]['ordre'] != $r->ordre)) {
							$numero_modele[$r->modele_id][$r->num_abt]['date_parution'] = $r->date_parution;
							$numero_modele[$r->modele_id][$r->num_abt]['ordre'] = $r->ordre;
							increment_bulletin($r->modele_id, $numero_modele[$r->modele_id],$r->num_abt);			
						}
					}
					
					if ($r->type == 1) {				
						$numero_modele[$r->modele_id]['abt_name'] = $r->abt_name;
						$libelle_abonnement = $numero_modele[$r->modele_id]['modele_name'] . " / " . $numero_modele[$r->modele_id]['abt_name'];			
						
						$numero = $numero_modele[$r->modele_id][$r->num_abt]['num'];
						
						$volume = $numero_modele[$r->modele_id][$r->num_abt]['vol'];
						$tome = $numero_modele[$r->modele_id][$r->num_abt]['tom'];
						$format_aff = $numero_modele[$r->modele_id]['format_aff'];
						if($format_aff){
							$print_format->var_format['DATE'] = $r->date_parution;
							$print_format->var_format['TOM'] = $tome;
							$print_format->var_format['VOL'] = $volume;
							$print_format->var_format['NUM'] = $numero;
							$print_format->var_format['START_NUM'] = $numero_modele[$r->modele_id][$r->num_abt]['start_num'];
							$print_format->var_format['START_VOL'] = $numero_modele[$r->modele_id][$r->num_abt]['start_vol'];
							$print_format->var_format['START_TOM'] = $numero_modele[$r->modele_id][$r->num_abt]['start_tom'];
							$print_format->var_format['START_DATE'] = $r->date_debut;
							$print_format->var_format['END_DATE'] = $r->date_fin;
												
							$print_format->cmd = $format_aff;
							$libelle_numero=$print_format->exec_cmd();
						}	
						else {
							$libelle_numero="";
							if($tome)$libelle_numero.= sprintf($msg['abts_tome'],$tome).' ';
							if($volume)$libelle_numero.= sprintf($msg['abts_vol'],$volume).' ';
							if($numero)$libelle_numero.= sprintf($msg['abts_no'],$numero);					
						}
					}
					else if ($r->type == 2) {				
						$numero_modele[$r->modele_id]['abt_name'] = $r->abt_name;
						$libelle_abonnement = $numero_modele[$r->modele_id]['modele_name'] . " / " . $numero_modele[$r->modele_id]['abt_name'];
						
						$volume = $numero_modele[$r->modele_id][$r->num_abt]['vol'];
						$tome = $numero_modele[$r->modele_id][$r->num_abt]['tom'];
						$format_aff = $numero_modele[$r->modele_id]['format_aff'];
						if($format_aff){
							$print_format->var_format['DATE'] = $r->date_parution;
							$print_format->var_format['TOM'] = $tome;
							$print_format->var_format['VOL'] = $volume;
							$print_format->var_format['NUM'] = "HS".$numero;
							$print_format->var_format['START_NUM'] = $numero_modele[$r->modele_id][$r->num_abt]['start_num'];
							$print_format->var_format['START_VOL'] = $numero_modele[$r->modele_id][$r->num_abt]['start_vol'];
							$print_format->var_format['START_TOM'] = $numero_modele[$r->modele_id][$r->num_abt]['start_tom'];
							$print_format->var_format['START_DATE'] = $r->date_debut;
							$print_format->var_format['END_DATE'] = $r->date_fin;
												
							$print_format->cmd = $format_aff;
							$libelle_numero=$print_format->exec_cmd();
						}	
						else {
							$libelle_numero="";
							if($tome)$libelle_numero.= sprintf($msg['abts_tome'],$tome).' ';
							if($volume)$libelle_numero.= sprintf($msg['abts_vol'],$volume).' ';
							if($numero)$libelle_numero.= sprintf($msg['abts_hsno'],$numero);					
						}
					}
					
					if ($r->state == 0) {			
						$obj = $r->id_bull;
						$fiche['date_parution']=$r->date_parution;
						$fiche['periodique']="<a href=\"./catalog.php?categ=serials&sub=view&serial_id=" . $r->num_notice . "\">$r->tit1</a>";
						$fiche['libelle_notice']=$r->tit1;
						$fiche['libelle_numero']=$libelle_numero;
						$fiche['libelle_abonnement']=$libelle_abonnement;
						$fiche['link_recu']="onClick='bulletine(\"$obj\",event);'";
						$fiche['link_non_recevable']="onClick='nonrecevable(\"$obj\",event);'";
						$fiche['fournisseur_id']=$r->fournisseur;
						$fiche['location_id']=$r->location_id;
						$fiche['TOM']=$tome;
						$fiche['VOL']=$volume;
						$fiche['NUM']=$numero;
						$fiche['cote'] = $r->cote;
						$fiche['perio_id'] = $r->num_notice;
						$fiche['abt_id'] = $r->num_abt;
						
						//Test des retards
						$diff = pmb_sql_value("SELECT DATEDIFF(CURDATE(),'$r->date_parution')");
						if($diff<0) {
							$retard=3;
						}else{
							if ($diff <= $numero_modele[$r->modele_id][$r->num_abt]["delais"])	$retard=0;
							elseif ($diff <= $numero_modele[$r->modele_id][$r->num_abt]["critique"]) $retard=1;
							else $retard=2;
							$this->fiche_bulletin[$retard][$obj]=$fiche;
						}		
					}
				}
				$abtModele=$r->num_abt.$r->modele_id;
				if(!in_array($abtModele,$memo_abt_modele)){
					// pour chaque modèle d'abonnement on va chercher le prochain
					// le libellé de numéro sera calculé à la fin de toute la boucle			
					$req_prochain="SELECT *	FROM abts_grille_abt
						WHERE date_parution > CURDATE() and num_abt = ".$r->num_abt." AND modele_id=".$r->modele_id."
						ORDER BY date_parution ";
					$res_prochain = pmb_mysql_query($req_prochain,$dbh);
					$deja_bulletine=0;
					while ($r_prochain = pmb_mysql_fetch_object($res_prochain)) {
						$prochain_id_bull=$r_prochain->id_bull;					
						$fiche_prochain['date_parution']=$r_prochain->date_parution;
						$fiche_prochain['periodique']="<a href=\"./catalog.php?categ=serials&sub=view&serial_id=" . $r->num_notice . "\">$r->tit1</a>";
						$fiche_prochain['libelle_notice']=$r->tit1;
						$fiche_prochain['libelle_numero']=$libelle_numero;
						$fiche_prochain['libelle_abonnement']=$libelle_abonnement;
						$fiche_prochain['link_recu']="onClick='bulletine(\"$prochain_id_bull\",event);'";
						$fiche_prochain['link_non_recevable']="onClick='nonrecevable(\"$prochain_id_bull\",event);'";
						$fiche_prochain['fournisseur_id']=$r->fournisseur;
						$fiche_prochain['location_id']=$r->location_id;
						$fiche_prochain['TOM']=$tome;
						$fiche_prochain['VOL']=$volume;
						if($r_prochain->numero){
							$fiche_prochain['NUM']=$r_prochain->numero;
						}else{
							$fiche_prochain['NUM']=$numero;
						}
						$fiche_prochain['cote'] = $r->cote;
						$fiche_prochain['perio_id'] = $r->num_notice;
						$fiche_prochain['abt_id'] = $r->num_abt;
						$fiche_prochain['modele_id'] = $r->modele_id;
						$fiche_prochain['ordre'] = $r->ordre;
						$fiche_prochain['type'] = $r_prochain->type;
						$fiche_prochain['abt_name'] = $r->abt_name;
						
						$memo_abt_modele[$abtModele]=$abtModele;
						if($r_prochain->state== 0){
							$fiche_prochain['deja_bulletine'] = $deja_bulletine;
							$memo_prochain[$r_prochain->date_parution."_".$r->tit1."_".$prochain_id_bull]=array($prochain_id_bull,$fiche_prochain);
							break;
						}elseif($r_prochain->type != 2){//Si c'est un hors série alors on ne le prend pas en compte dans le calcule du prochain numéro
							$deja_bulletine++;
						}
					}								
				}				
			}
			
			// Traitement de prochains numéros
			ksort($memo_prochain);//Pour trier les prochain numéros par date puis titre
			foreach($memo_prochain as $table){
				$obj=$table[0];
				$this->fiche_bulletin[3][$obj]=$table[1];
				$modele_id=$this->fiche_bulletin[3][$obj]['modele_id'];
				$num_abt=$this->fiche_bulletin[3][$obj]['abt_id'];
				$date_parution=$this->fiche_bulletin[3][$obj]['date_parution'];
				$ordre=$this->fiche_bulletin[3][$obj]['ordre'];
				$type=$this->fiche_bulletin[3][$obj]['type'];
				$abt_name=$this->fiche_bulletin[3][$obj]['abt_name'];
				
				for($i=0; $i<$this->fiche_bulletin[3][$obj]['deja_bulletine']; $i++ ){									
					increment_bulletin($modele_id, $numero_modele[$modele_id],$num_abt);
				}
							
				if (!$numero_modele[$modele_id][$num_abt]['ordre']) {
					$requete = "SELECT num,vol, tome, delais,	critique FROM abts_abts_modeles WHERE modele_id=".$modele_id." and abt_id=".$num_abt;
					$resultat_n = pmb_mysql_query($requete,$dbh);
					if ($r_abt = pmb_mysql_fetch_object($resultat_n)) {
						$numero_modele[$modele_id][$num_abt]['num'] = $r_abt->num;
						$numero_modele[$modele_id][$num_abt]['vol'] = $r_abt->vol;
						$numero_modele[$modele_id][$num_abt]['tom'] = $r_abt->tome;
						$numero_modele[$modele_id][$num_abt]['delais'] = $r_abt->delais;
						$numero_modele[$modele_id][$num_abt]['critique'] = $r_abt->critique;
						$numero_modele[$modele_id][$num_abt]['start_num'] = $r_abt->num;
						$numero_modele[$modele_id][$num_abt]['start_vol'] = $r_abt->vol;
						$numero_modele[$modele_id][$num_abt]['start_tom'] = $r_abt->tome;
					}
					
					$numero_modele[$modele_id][$num_abt]['num']--;
					increment_bulletin($modele_id, $numero_modele[$modele_id],$num_abt);
					
				} elseif (($numero_modele[$modele_id][$num_abt]['date_parution'] != $date_parution) || ($numero_modele[$modele_id][$num_abt]['ordre'] != $ordre)) {
					increment_bulletin($modele_id, $numero_modele[$modele_id],$num_abt);
				}
				
				if ($type == 1) {
					$numero_modele[$modele_id]['abt_name'] = $abt_name;
					$libelle_abonnement = $numero_modele[$modele_id]['modele_name'] . " / " . $numero_modele[$modele_id]['abt_name'];
						
					$numero = $numero_modele[$modele_id][$num_abt]['num'];
						
					$volume = $numero_modele[$modele_id][$num_abt]['vol'];
					$tome = $numero_modele[$modele_id][$num_abt]['tom'];
					$format_aff = $numero_modele[$modele_id]['format_aff'];
					if($format_aff){
						$print_format->var_format['DATE'] = $date_parution;
						$print_format->var_format['TOM'] = $tome;
						$print_format->var_format['VOL'] = $volume;
						$print_format->var_format['NUM'] = $numero;
						$print_format->var_format['START_NUM'] = $numero_modele[$modele_id][$num_abt]['start_num'];
						$print_format->var_format['START_VOL'] = $numero_modele[$modele_id][$num_abt]['start_vol'];
						$print_format->var_format['START_TOM'] = $numero_modele[$modele_id][$num_abt]['start_tom'];
							
						$print_format->cmd = $format_aff;
						$libelle_numero=$print_format->exec_cmd();
					}
					else {
						$libelle_numero="";
						if($tome)$libelle_numero.= sprintf($msg['abts_tome'],$tome).' ';
						if($volume)$libelle_numero.= sprintf($msg['abts_vol'],$volume).' ';
						if($numero)$libelle_numero.= sprintf($msg['abts_no'],$numero);
					}
				}
				else if ($type == 2) {
					$numero_modele[$modele_id]['abt_name'] = $abt_name;
					$libelle_abonnement = $numero_modele[$modele_id]['modele_name'] . " / " . $numero_modele[$modele_id]['abt_name'];
					
					$numero=$this->fiche_bulletin[3][$obj]['NUM'];//Dans le cas où l'on a forcé un numéro pour le hors série
					
					$volume = $numero_modele[$modele_id][$num_abt]['vol'];
					$tome = $numero_modele[$modele_id][$num_abt]['tom'];
					$format_aff = $numero_modele[$modele_id]['format_aff'];
					if($format_aff){
						$print_format->var_format['DATE'] = $date_parution;
						$print_format->var_format['TOM'] = $tome;
						$print_format->var_format['VOL'] = $volume;
						$print_format->var_format['NUM'] = "HS".$numero;
						$print_format->var_format['START_NUM'] = $numero_modele[$modele_id][$num_abt]['start_num'];
						$print_format->var_format['START_VOL'] = $numero_modele[$modele_id][$num_abt]['start_vol'];
						$print_format->var_format['START_TOM'] = $numero_modele[$modele_id][$num_abt]['start_tom'];
							
						$print_format->cmd = $format_aff;
						$libelle_numero=$print_format->exec_cmd();
					}
					else {
						$libelle_numero="";
						if($tome)$libelle_numero.= sprintf($msg['abts_tome'],$tome).' ';
						if($volume)$libelle_numero.= sprintf($msg['abts_vol'],$volume).' ';
						if($numero)$libelle_numero.= sprintf($msg['abts_hsno'],$numero);
					}
				}
				$this->fiche_bulletin[3][$obj]['libelle_numero']=$libelle_numero;
				$this->fiche_bulletin[3][$obj]['libelle_abonnement']=$libelle_abonnement;
			}			
		}	
		return $this->fiche_bulletin;
	}	

	public static function get_dashboard_info($location_view="") {
		global $msg;
		global $dbh;
		global $deflt_bulletinage_location;	
		
		
		$cache_php=cache_factory::getCache();
		if ($cache_php) {
			$key = SQL_SERVER.DATA_BASE."_dashboard_".$location_view;
			$key_datetime = SQL_SERVER.DATA_BASE."_dashboard_datetime_".$location_view;
			$tmp_key_datetime = $cache_php->getFromCache($key_datetime);
			if($tmp_key_datetime){
				$req = "select if('".$tmp_key_datetime."' > greatest(curdate(),(SELECT max(IF(UPDATE_TIME IS NULL,'3000-01-01 01:01:01',UPDATE_TIME)) from information_schema.tables where table_schema='".DATA_BASE."' and (table_name='abts_grille_abt' or table_name='abts_abts' or table_name='abts_abts_modeles'))),1,0)";
				if ( pmb_sql_value($req) ) {
					return $cache_php->getFromCache($key);
				}
			}
		}
		
		$cpt_a_recevoir = $cpt_en_retard = $cpt_en_alerte = $prochain_numero = 0;
		
		$requete = "
		select * from (
			SELECT id_bull,num_abt,abts_grille_abt.date_parution,modele_id,type,numero,nombre,ordre,state
			FROM abts_grille_abt ,abts_abts
			WHERE abts_grille_abt.date_parution <= CURDATE() and abt_id=num_abt  and state=0";
			if ($location_view) $requete .= " and location_id='$location_view'";
			$requete .= " 
			union
			select id_bull,num_abt,prochain.date_parution,modele_id,type,numero,nombre,ordre,state
			from (
				SELECT id_bull,num_abt,abts_grille_abt.date_parution,modele_id,type,numero,nombre,ordre,state
				FROM abts_grille_abt ,abts_abts
				WHERE abts_grille_abt.date_parution > CURDATE()  and abt_id=num_abt  and state=0";
				if ($location_view) $requete .= " and location_id='$location_view'";
				$requete .= " 
				ORDER BY abts_grille_abt.date_parution
			) as prochain group by type,ordre,num_abt,modele_id
		) as liste_bull order by date_parution
		";
				
		$resultat = pmb_mysql_query($requete,$dbh);
		if ($resultat) {
			while ($r = pmb_mysql_fetch_object($resultat)) {
				// recheche des délais de retart 
				if (!isset($numero_modele[$r->modele_id][$r->num_abt])) {
					$requete = "SELECT delais,	critique FROM abts_abts_modeles WHERE modele_id=$r->modele_id and abt_id=$r->num_abt";
					$resultat_n = pmb_mysql_query($requete,$dbh);
					if ($r_abt = pmb_mysql_fetch_object($resultat_n)) {
						$numero_modele[$r->modele_id][$r->num_abt]['delais'] = $r_abt->delais;
						$numero_modele[$r->modele_id][$r->num_abt]['critique'] = $r_abt->critique;
					}							
				}
				if ($numero_modele[$r->modele_id][$r->num_abt]) {		
					$diff = pmb_sql_value("SELECT DATEDIFF(CURDATE(),'$r->date_parution')");
					if($diff<0 ) $prochain_numero++;					
					elseif ($diff <= $numero_modele[$r->modele_id][$r->num_abt]["delais"])	$cpt_a_recevoir++;
					elseif ($diff <= $numero_modele[$r->modele_id][$r->num_abt]["critique"]) $cpt_en_retard++;
					else $cpt_en_alerte++;
				}
			}
		}
		
		$ret = array(
			'a_recevoir'=>$cpt_a_recevoir,
			'en_retard'=>$cpt_en_retard,
			'en_alerte'=>$cpt_en_alerte,
			'prochain_numero'=>$prochain_numero
		);
		
		if ($cache_php) {
			$cache_php->setInCache($key,$ret);
			$cache_php->setInCache($key_datetime,pmb_sql_value("select now()"));
		}
		return $ret;
	}	

	
	
	public function show_form() {
		global $msg, $charset;
		global $dbh;
		global $pointage_form, $pointage_list;
		global $location_view, $deflt_bulletinage_location,$serial_id,$pmb_abt_end_delay;
		global $pmb_serialcirc_subst;
		
		if ($location_view == "") $location_view = $deflt_bulletinage_location;
		$form = $pointage_form;

		$form .=<<<ENDOFTEXT
		<script type="text/javascript" src='./javascript/select.js'></script>
		<script type="text/javascript" src='./javascript/ajax.js'></script>
		<script type='text/javascript' src='./javascript/serialcirc.js'></script>
		<script type="text/javascript">
		function bulletine(obj,e) {
				
			var bull_layer=document.getElementById('bull_layer');
			if (undefined==bull_layer) {
				
				bull_layer=document.createElement("div");
				bull_layer.setAttribute('id','bull_layer');
				bull_layer.setAttribute('style','position:absolute;left:0;z-index:1001;');
				bull_layer.setAttribute('onclick','kill_frame_periodique();');
				bull_layer.style.width=getWindowWidth()+'px';
				bull_layer.style.height=getWindowHeight()+'px';
				bull_layer.style.top=getWindowScrollY()+'px';
				document.getElementsByTagName('body')[0].appendChild(bull_layer);
		
			}
			bull_frame=document.createElement("iframe");		
			bull_frame.setAttribute('id','bull_frame');
			bull_frame.setAttribute('name','bull_frame');
			
			var obj_2=obj+"_2";
			var id_obj=document.getElementById(obj_2);
			var num=id_obj.getAttribute('num');	
			var nume=id_obj.getAttribute('nume');	
			var vol=id_obj.getAttribute('vol');	
			var tom=id_obj.getAttribute('tom');
			
			var url="./pointage_exemplarise.php?id_bull="+obj+"&numero="+num+"&nume="+nume+"&vol="+vol+"&tom="+tom+"";
			bull_frame.src=url;
			bull_resizeFrame(obj);
			bull_frame.style.visibility="visible";	
			bull_frame.style.display='block';	
			bull_layer.appendChild(bull_frame);		
			bull_layer.parentNode.style.overflow = "hidden";
		}
				
		//position verticale curseur
		function getWindowScrollY(){
			if(window.scrollY)
				return window.scrollY;
			else return document.documentElement.scrollTop;
		}
				
		//hauteur fenetre
		function getWindowHeight(){
			if(window.innerHeight) 
				return window.innerHeight;
			else return document.body.clientHeight;
		}
		
		
		//largeur fenetre
		function getWindowWidth(){
			if(window.innerWidth) 
				return window.innerWidth;
			else return document.body.clientWidth;
		}
				
		//redimensionnement frame
		function bull_resizeFrame() {
			
			var bull_layer = document.getElementById('bull_layer');
			if (bull_layer) {
				bull_layer.style.width=(getWindowWidth()-200)+'px';
				bull_layer.style.height=getWindowHeight()+'px';
				bull_layer.style.top=getWindowScrollY()+'px';
				bull_layer.style.left='200px';
				
				bull_frame.style.top='5%';
				bull_frame.style.width='95%';
				bull_frame.style.height='95%';
				bull_frame.style.left='5%';
				
			}
		}
		
		function nonrecevable(obj,e) {	
				
			var bull_layer=document.getElementById('bull_layer');
			if (undefined==bull_layer) {
				
				bull_layer=document.createElement("div");
				bull_layer.setAttribute('id','bull_layer');
				bull_layer.setAttribute('style','position:absolute;left:0;z-index:1001;');
				bull_layer.setAttribute('onclick','kill_frame_periodique();');
				bull_layer.style.width=getWindowWidth()+'px';
				bull_layer.style.height=getWindowHeight()+'px';
				bull_layer.style.top=getWindowScrollY()+'px';
				document.getElementsByTagName('body')[0].appendChild(bull_layer);
		
			}
			bull_frame=document.createElement("iframe");		
			bull_frame.setAttribute('id','bull_frame');
			bull_frame.setAttribute('name','bull_frame');
			
			var obj_2=obj+"_2";
			var id_obj=document.getElementById(obj_2);
			var num=id_obj.getAttribute('num');
			
			var url="./pointage_exemplarise.php?nonrecevable=1&id_bull="+obj+"&numero="+num+"";
			bull_frame.src=url;
			bull_resizeFrame(obj);
			bull_frame.style.visibility="visible";	
			bull_frame.style.display='block';	
			bull_layer.appendChild(bull_frame);		
			bull_layer.parentNode.style.overflow = "hidden";
		}
		
		function kill_frame_periodique() {
			var bull_layer = document.getElementById('bull_layer');
			bull_layer.parentNode.style.overflow = "auto";
			bull_layer.parentNode.removeChild(bull_layer);
		}


		function imprime() {
			var selectBox=document.getElementById("location_id");
			value=selectBox.options[selectBox.selectedIndex].value;
			document.location="./pdf.php?pdfdoc=liste_bulletinage&act=print&location_view="+value;
		}		
		
		function imprime_abts_depasse() {
			var selectBox=document.getElementById("location_id");
			value=selectBox.options[selectBox.selectedIndex].value;
			document.location="./pdf.php?pdfdoc=abts_depasse&act=print&location_view="+value;
		}		
		function imprime_cote(expl_id) {
			openPopUp("./ajax.php?module=circ&categ=periocirc&sub=print_cote&expl_id="+expl_id, "circulation");
		}		
		function imprime_all_cote() {
			openPopUp("./ajax.php?module=circ&categ=periocirc&sub=print_cote", "circulation");
		}	
ENDOFTEXT;
		$link_bulletinage="";
		if ($serial_id) {
			$link_bulletinage = "&serial_id=$serial_id"; 
		}
				
		$form.= "
			function localisation_change(selectBox) {			
			id=selectBox.options[selectBox.selectedIndex].value;
			document.location='./catalog.php?categ=serials&sub=pointage".$link_bulletinage."&location_view='+id;
		}
		</script>	
		";


		// select "localisation"
		$form_localisation = gen_liste("select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'location_id', "localisation_change(this);", $location_view, "", "", "0", $msg["all_location"], 0);
		$link_bulletinage="";
		if ($serial_id) {
			$requete = "SELECT tit1 from notices WHERE notice_id= $serial_id";
			$resultat = pmb_mysql_query($requete,$dbh);
			if ($r = pmb_mysql_fetch_object($resultat)) {
				
				$link_bulletinage = "<a href='./catalog.php?categ=serials&sub=view&serial_id=$serial_id&location=$location_view'>"
					.$r->tit1."</a>"; 
			}	
			$form_localisation.=$link_bulletinage;
		}
		
		$form = str_replace('!!localisation!!',$form_localisation , $form);
		$header_table = "<table class='sortable'>
					<tr>			
						<th>" .	$msg['pointage_label_date'] . "</th>
						<th>" . $msg['pointage_label_notice'] . "</th>
						<th>" . $msg['pointage_label_numero'] . "</th>
						<th>" . $msg['pointage_label_abonnement'] . "</th>
						<th>" . $msg['pointage_label_a_recevoir'] . "</th>
						<th>" . $msg['pointage_label_recu'] . "</th>
						<th>" . $msg['pointage_label_supprimer_et_conserver'] . "</th>
						<th></th>
					</tr>";													
		$liste_bulletin=$this->get_bulletinage();
		$a_recevoir = $en_retard = $en_alerte = "";
		$cpt_a_recevoir = $cpt_en_retard = $cpt_en_alerte = 0;					
		$prochain_numero = 0;
		
		if($liste_bulletin){
			//Tri par type de retard
			asort($liste_bulletin);
			foreach($liste_bulletin as $retard => $bulletin_retard){
				$cpt=0;
				$contenu='';
				foreach($bulletin_retard as $id_bull => $fiche){
					if (++$cpt % 2) $pair_impair = "even"; else $pair_impair = "odd";
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
					$contenu_tmp = "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
					$contenu_tmp .= "<td><strong>" . formatdate($fiche['date_parution']) . "</strong></td>";
					$contenu_tmp .= "<td>".$fiche['periodique']."</td>";
					$contenu_tmp .= "<td>".$fiche['libelle_numero']."</td>";
					$contenu_tmp .= "<td>".$fiche['libelle_abonnement']."</td>";
					$contenu_tmp .= "<td><input name='".$id_bull."' id='".$id_bull."_1' checked='checked'  value='1' type='radio'></td>";
					$contenu_tmp .= "<td><input name='".$id_bull."' id='".$id_bull."_2' value='2' nume='". $fiche['NUM']."' vol='". $fiche['VOL']."'	tom='". $fiche['TOM']."' num='". htmlentities($fiche['libelle_numero'],ENT_QUOTES, $charset)."'  type='radio' ".$fiche['link_recu']." ></td>";
					$contenu_tmp .= "<td><input name='".$id_bull."' id='".$id_bull."_3' value='3' type='radio' ".$fiche['link_non_recevable']." ></td>";
					$contenu_tmp .= "<td id='". $id_bull."_bul'>&nbsp;</td>";
					$contenu_tmp .= "</tr>";	
					$contenu=$contenu_tmp.$contenu;
								
				}
				$contenu = $header_table . $contenu . "</table>";
				if($cpt && $retard==3){
					$prochain_numero = $this->gen_plus_form("prochain_numero", $msg["pointage_label_prochain_numero"] . " ($cpt)", $contenu);
					$cpt_prochain_numero= $cpt;				
				}				
				if($cpt && $retard==0){
					$a_recevoir = $this->gen_plus_form("a_recevoir", $msg["pointage_label_a_recevoir"] . " ($cpt)", $contenu);
					$cpt_a_recevoir= $cpt;				
				}	
				if($cpt && $retard==1){
					$en_retard = $this->gen_plus_form("en_retard", $msg["pointage_label_en_retard"] . " ($cpt)", $contenu);	
					$cpt_en_retard=	$cpt;		
				}			
				if($cpt && $retard==2){
					$en_alerte = $this->gen_plus_form("en_alerte", $msg["pointage_label_depasse"] . " ($cpt)", $contenu);	
					$cpt_en_alerte=	$cpt;	
				}				
			}	
		}	
		$pointage_list = str_replace('!!prochain_numero!!', $prochain_numero, $pointage_list);
		$pointage_list = str_replace('!!a_recevoir!!', $a_recevoir, $pointage_list);
		$pointage_list = str_replace('!!en_retard!!', $en_retard, $pointage_list);
		$pointage_list = str_replace('!!en_alerte!!', $en_alerte, $pointage_list);
		// Gestion des abonnements qui arrive a terme
		if(!$pmb_abt_end_delay || !is_numeric($pmb_abt_end_delay)) $pmb_abt_end_delay=30;
		$header_table = "<table class='sortable'>	
				<tr>		
					<th>" .	$msg['pointage_label_date_fin'] . "</th>		
					<th>" . $msg['pointage_label_abonnement'] . "</th>
				</tr>";
		
		$abts_status_where = '';
		$abts_status_ids = abts_status::get_ids_bulletinage_active();
		if(count($abts_status_ids)) {
			$abts_status_where = " and abt_status in(".implode(',', $abts_status_ids).") ";
		}		
		
		$requete = "SELECT abt_id,abt_name,tit1,num_notice, date_fin
					FROM abts_abts,notices
					WHERE date_fin BETWEEN CURDATE() AND  DATE_ADD(CURDATE(), INTERVAL $pmb_abt_end_delay DAY)
					and notice_id= num_notice ".$abts_status_where;
		if ($location_view) $requete .= " and location_id='$location_view'";
		$requete .= " ORDER BY date_fin,abt_name";
		$resultat = pmb_mysql_query($requete,$dbh);	
		$cpt=0;
		$contenu='';
		while ($r = pmb_mysql_fetch_object($resultat)) {
			if (++$cpt % 2) $pair_impair = "even"; else $pair_impair = "odd";
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
			$contenu .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
			$contenu .= "<td><strong>" . formatdate($r->date_fin) . "</strong></td>";
			$contenu .= "<td><a href=\"./catalog.php?categ=serials&sub=abon&serial_id=" . $r->num_notice . "&abt_id=" . $r->abt_id . "\">".$r->tit1." / ".$r->abt_name."</a></td>";		
			$contenu .= "</tr>";				
		}
		$contenu = $header_table . $contenu . "</table>";
		$fin_abonnement='';
		if($cpt){
			$fin_abonnement = $this->gen_plus_form("fin_abonnement", $msg["pointage_alerte_fin_abonnement"] . " ($cpt)", $contenu);			
		}	
		// Gestion des abonnements dont la date est dépassée
		$requete = "SELECT abt_id,abt_name,tit1,num_notice, date_fin
					FROM abts_abts,notices
					WHERE date_fin < CURDATE()
					and notice_id= num_notice ".$abts_status_where;
		if ($location_view) $requete .= " and location_id='$location_view'";
		$requete .= " ORDER BY date_fin,abt_name";	
		$resultat = pmb_mysql_query($requete,$dbh);	
		$cpt=0;
		$contenu='';
		$flag_imprime_abts_depasse=0;
		while ($r = pmb_mysql_fetch_object($resultat)) {
			if (++$cpt % 2) $pair_impair = "even"; else $pair_impair = "odd";
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
			$contenu .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
			$contenu .= "<td><strong>" . formatdate($r->date_fin) . "</strong></td>";
			$contenu .= "<td><a href=\"./catalog.php?categ=serials&sub=abon&serial_id=" . $r->num_notice . "&abt_id=" . $r->abt_id . "\">".$r->tit1." / ".$r->abt_name."</a></td>";	
			$contenu .= "</tr>";	
			$flag_imprime_abts_depasse=1;			
		}
		$contenu = $header_table . $contenu . "</table>";
		$abonnement_depasse='';
		if($cpt){
			$abonnement_depasse = $this->gen_plus_form("depasse_abonnement", $msg["pointage_alerte_abonnement_depasse"] . " ($cpt)", $contenu);			
		}				
				
		$pointage_list = str_replace('!!alerte_fin_abonnement!!', $fin_abonnement, $pointage_list);
		$pointage_list = str_replace('!!alerte_abonnement_depasse!!', $abonnement_depasse, $pointage_list);
		
		$form = str_replace('!!bultinage!!', $pointage_list, $form);
		if ($cpt_en_retard || $cpt_en_alerte)
			$form = str_replace("!!imprimer!!", "<input type=\"button\" class='bouton' value='" .
			$msg["abonnements_imprimer_lettres"] . "' onClick=\"imprime();\"/>", $form);			
		else $form = str_replace("!!imprimer!!", "", $form);
		if ($flag_imprime_abts_depasse)
			$form = str_replace("!!imprime_abts_depasse!!", "<input type=\"button\" class='bouton' value='" .
			$msg["abts_print_depasse_bt"] . "' onClick=\"imprime_abts_depasse();\"/>", $form);			
		else $form = str_replace("!!imprime_abts_depasse!!", "", $form);
		
		$bt_gestion_retard="";
		if ($cpt_en_alerte){
			$bt_gestion_retard="<input type=\"button\" class='bouton' value='" .$msg["abts_gestion_retard_bt"] . "' onClick=\"document.location='./catalog.php?categ=serials&sub=abts_retard&location_view=".$location_view."'\"/>";		
		}
		$bt_impression_etiquette_cote="";
		if ($pmb_serialcirc_subst){
			$bt_impression_etiquette_cote="<input type=\"button\" class='bouton' value='" .$msg["abts_print_cote_bt"] . "' onClick=\"imprime_all_cote();return false;\"/>";		
		}		
		
		$form = str_replace("!!gestion_retard!!", $bt_gestion_retard.$bt_impression_etiquette_cote, $form);
		
		$form = str_replace("!!action!!", "./catalog.php?categ=serials&sub=pointage&serial_id=" . "$serial_id&location_view=$location_view", $form);
		return $form;
	}


	public function imprimer() {
		global $dbh;
		global $msg;
		global $include_path;
	}

	public function proceed() {
		global $act;
		global $serial_id, $msg, $num_notice;
		
		switch ($act) {
			case 'print' :
				$liste_bulletin=$this->get_bulletinage();
				return $liste_bulletin;
				break;
			default :
				print $this->show_form();
				break;
		}
	}
	
	
	
	public function get_form_retard(){
		global $abts_gestion_retard_form_filter,$charset,$dbh,$msg;
		global $location_view,$filter,$deflt_bulletinage_location;
		global $abts_gestion_retard_fournisseur_first,$abts_gestion_retard_fournisseur_suite;
		global $max_fourn,$abts_gestion_retard_form,$abts_gestion_retard_perio,$abts_gestion_retard_bulletin;
		
		$form=$abts_gestion_retard_form_filter;
		if($location_view == "") $location_view=$deflt_bulletinage_location;
		$select_location = gen_liste("select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'location_view', "", $location_view, "", "", "0", $msg['all_location'], 0);
		$form = str_replace("!!location_filter!!", $select_location, $form);
		$form = str_replace("!!abts_state_selected_".$filter."!!", "selected='selected' ", $form);
		$clause_filter="";
		if($filter==1){ // abts actifs
			$clause_filter=" and date_debut <= CURDATE() and date_fin >= CURDATE() ";
		}elseif($filter==2){ // abts dépassés
			$clause_filter=" and date_fin < CURDATE() ";
		} 
		$fournisseurs=array();
		$nb=0;
		for($i=0;$i<$max_fourn; $i++){		
			eval ("global \$f_fourn_id$i; \$id=  \$f_fourn_id$i;"); 									
			$q = "select * from entites where id_entite = '".$id."' ";
			$res = pmb_mysql_query($q, $dbh);
			if (pmb_mysql_num_rows($res) != 0) {
				$coord = pmb_mysql_fetch_object($res);
				$fournisseurs[$nb]["libelle"]=$coord->raison_sociale;
				$fournisseurs[$nb]["id"]=$id;				
				$nb++;
			}			
		}
		
		$clause_fournisseur="";		
		if(count($fournisseurs)){		
			foreach($fournisseurs as $fournisseur){
				if($clause_fournisseur){
					$clause_fournisseur.=" or "; 
				}
				$clause_fournisseur.="  fournisseur= '".$fournisseur["id"]."' ";
			}	
			$clause_fournisseur=" and( $clause_fournisseur ) ";
		}
		
		$fourn_repetables = '';
		if (sizeof($fournisseurs)==0) $max_fourn = 1 ;
		else $max_fourn = sizeof($fournisseurs) ; 
		for ($i = 0 ; $i < $max_fourn ; $i++) {
			if(isset($fournisseurs[$i]["id"])) {
				$fourn_id = $fournisseurs[$i]["id"] ;
			} else {
				$fourn_id = 0;
			}
			
			if ($i==0) $tmp_fourn = str_replace('!!ifourn!!', $i, $abts_gestion_retard_fournisseur_first) ;
			else $tmp_fourn = str_replace('!!ifourn!!', $i, $abts_gestion_retard_fournisseur_suite) ;
				
			$tmp_fourn = str_replace('!!fourn_id!!',			$fourn_id, $tmp_fourn);
			if ( sizeof($fournisseurs)==0 ) { 
				$tmp_fourn = str_replace('!!fourn_libelle!!', '', $tmp_fourn);		
			} else {
				$tmp_fourn = str_replace('!!fourn_libelle!!',	htmlentities($fournisseurs[$i]["libelle"],ENT_QUOTES, $charset), $tmp_fourn);
			}
			$fourn_repetables .= $tmp_fourn ;
		}
		$form = str_replace('!!max_fourn!!', $max_fourn, $form);
		$form = str_replace('!!fournisseurs_repetables!!', $fourn_repetables, $form);
			
		$this->get_bulletinage($clause_filter.$clause_fournisseur," index_sew,abt_name,date_parution,ordre ");
		$perio_id_old=0;
		$form_perio="";
		if($this->fiche_bulletin){
			$i=0;
		}
		$js_tab_perio_bulletin=$form_bulletin_liste=$form_perio_liste="";
		$tab_bulletins_to_post=array();
		$js_perio_bulletin=$js_perio_bulletin_start=0;
		$i_perio=0;
		if($this->fiche_bulletin[2]) {
			foreach($this->fiche_bulletin[2] as $fiche){
				$i++;
				if($perio_id_old != $fiche['perio_id']){
					$form_perio = str_replace("!!liste_retard!!", $form_bulletin_liste, $form_perio);
					$form_perio_liste.=$form_perio;
					$form_perio=$abts_gestion_retard_perio;
					$form_perio = str_replace("!!perio_header!!", $fiche['libelle_notice'], $form_perio);
					$form_perio=str_replace("!!num_perio!!", $fiche['perio_id'], $form_perio);
					$form_perio=str_replace("!!i_perio!!", $i_perio++, $form_perio);
					if ($perio_id_old) {
						$js_tab_perio_bulletin.="tab_perio_bulletins[".$perio_id_old."]=new Array($js_perio_bulletin_start,$js_perio_bulletin);\n";
					}
					$js_perio_bulletin_start=$i;
					$js_perio_bulletin=0;
					$form_bulletin_liste="";
					$perio_id_old = $fiche['perio_id'];
					$class_tr="";
				}
				if($class_tr=='odd')$class_tr='even';else $class_tr='odd';
				$data_relance=$this->get_comment_form($fiche['abt_id'],$fiche['date_parution'],$fiche['libelle_numero'],$class_tr);
				$form_bulletin=$abts_gestion_retard_bulletin.$data_relance["suite"];
				
				$form_bulletin=str_replace("!!tr_class!!", $class_tr, $form_bulletin);
				$form_bulletin=str_replace("!!date!!",formatdate( $fiche['date_parution']), $form_bulletin);
				$form_bulletin=str_replace("!!numero!!", $fiche['libelle_numero'], $form_bulletin);
				$form_bulletin=str_replace("!!abonnement!!", $fiche['libelle_abonnement'], $form_bulletin);
				$form_bulletin=str_replace("!!num_perio!!", $fiche['perio_id'], $form_bulletin);
				
				$tab_bulletins_to_post['abt_id']=$fiche['abt_id'];
				$tab_bulletins_to_post['date_parution']=$fiche['date_parution'];
				$tab_bulletins_to_post['libelle_numero']=$fiche['libelle_numero'];
				$form_bulletin=str_replace("!!bulletin_serialise!!", htmlentities(serialize($tab_bulletins_to_post), ENT_QUOTES,$charset), $form_bulletin);
				
				$form_bulletin=str_replace("!!bulletin_number!!", $i, $form_bulletin);			
				$form_bulletin=str_replace("!!comment_gestion!!", isset($data_relance["first_line"]['comment_gestion']) ? $data_relance["first_line"]['comment_gestion'] : '', $form_bulletin);
				$form_bulletin=str_replace("!!comment_opac!!",  isset($data_relance["first_line"]['comment_opac']) ? $data_relance["first_line"]['comment_opac'] : '', $form_bulletin);
				if($data_relance["first_line"]['nb_relance']) {
					$form_bulletin=str_replace("!!nb_relance!!", "<a href='#'  onClick=\"gestion_retard_view_histo(!!rel_id!!,".$data_relance["first_line"]['nb_relance'].");return false;\">".$data_relance["first_line"]['nb_relance']."</a>", $form_bulletin);
				} else {
					$form_bulletin=str_replace("!!nb_relance!!", $data_relance["first_line"]['nb_relance'], $form_bulletin);
				}
				$form_bulletin=str_replace("!!date_relance!!", $data_relance["first_line"]['date_relance'], $form_bulletin);
				$form_bulletin=str_replace("!!rel_id!!", $data_relance["first_line"]['rel_id'], $form_bulletin);	
	
				
				$form_bulletin=str_replace("!!relnew_num!!", $data_relance["first_line"]['rel_id'], $form_bulletin);
						
				$form_bulletin.$data_relance["suite"];
				$js_perio_bulletin++;
				
				$form_bulletin_liste.=$form_bulletin;
			}
		}
		$js_tab_perio_bulletin.="tab_perio_bulletins[".$fiche['perio_id']."]=new Array($js_perio_bulletin_start,$js_perio_bulletin);\n";
		$form_perio = str_replace("!!liste_retard!!", $form_bulletin_liste, $form_perio);
		$form_perio=str_replace("!!num_perio!!", $fiche['perio_id'], $form_perio);
		$form_perio=str_replace("!!i_perio!!", $i_perio, $form_perio);
		$form_perio_liste.=$form_perio;
		$form.=$abts_gestion_retard_form;
		$form = str_replace("!!perio_list!!", $form_perio_liste, $form);
		$form = str_replace("!!nb_perios!!", $i_perio, $form);
		$form = str_replace("!!nb_bulletins!!", $i, $form);
		$form = str_replace("!!tab_perio!!", $js_tab_perio_bulletin, $form);
		return $form;
	}

	
	public function get_comment_form($abt_id,$date_parution,$libelle_numero,$class_tr){
		global $dbh,$abts_gestion_retard_bulletin_relance,$charset;
		
		$rel_max=0;
		$i=0;
		$form_list="";
		$first_line = array();
		$req="SELECT * from perio_relance where rel_abt_num='".$abt_id."' and rel_date_parution='".$date_parution."' and  rel_libelle_numero='".addslashes($libelle_numero)."' order by rel_nb desc";		
		$result = pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($result)){
			while($r = pmb_mysql_fetch_object($result)) {				
				if($i==0){
					$rel_max=$r->rel_nb;
					$rel_date_max=$r->rel_date;
				}
				if(!$r->rel_nb){
					// Commentaire non relancé présent
					$first_line["comment_gestion"]=htmlentities( $r->rel_comment_gestion, ENT_QUOTES,$charset);
					$first_line["comment_opac"]=htmlentities( $r->rel_comment_opac, ENT_QUOTES,$charset);				
					if($rel_max){
						$first_line["nb_relance"]=$rel_max;
						$first_line["date_relance"]=formatdate($rel_date_max);
					}
					else{
						$first_line["nb_relance"]="";
						$first_line["date_relance"]="";
					}					 
					$first_line["rel_id"]=$r->rel_id;
				}else{					
					// c'est une relance effectuée
					$form=$abts_gestion_retard_bulletin_relance;					
					$form=str_replace("!!comment_gestion!!",htmlentities( $r->rel_comment_gestion, ENT_QUOTES,$charset), $form);
					$form=str_replace("!!comment_opac!!",htmlentities( $r->rel_comment_opac, ENT_QUOTES,$charset), $form);
					$form=str_replace("!!nb_relance!!", $r->rel_nb, $form);
					$form=str_replace("!!date_relance!!",formatdate($r->rel_date), $form);
					$form=str_replace("!!rel_id!!",$r->rel_id, $form);
					$form_list.=$form;
				}
				$i++;
			}				
		} else {
			// aucune relance et aucun commentaire			
		}	
		if(!$first_line) {
			$req="insert into perio_relance set rel_abt_num='".$abt_id."', rel_date_parution='".$date_parution."',  rel_libelle_numero='".addslashes($libelle_numero)."'  ";		
			pmb_mysql_query($req,$dbh);	
			$first_line["rel_id"]=pmb_mysql_insert_id($dbh);
			if($rel_max){
				$first_line["nb_relance"]=$rel_max;
				$first_line["date_relance"]=formatdate($rel_date_max);
			}
			else{
				$first_line["nb_relance"]="";
				$first_line["date_relance"]="";
			}					 
		}				
		//$first_line["nb_relance"]=$rel_max+1;
		$return_data["first_line"]=$first_line;		
		$return_data["suite"]=$form_list;
		return $return_data;				
	}
	
	public function set_comment_retard($type=0){
		global $dbh;
		global $bulletin, $comment;
		if(!$comment || !$bulletin) return;
		foreach($bulletin as $data){
			$bulletin_info=unserialize(stripslashes($data));
			if($type==1)	$type_comment ="rel_comment_gestion";
			else 			$type_comment ="rel_comment_opac";
			$req="SELECT rel_id from perio_relance where rel_abt_num='".$bulletin_info['abt_id']."' and rel_date_parution='".$bulletin_info['date_parution']."' and  rel_libelle_numero='".addslashes($bulletin_info['libelle_numero'])."' and rel_nb=0";
			$result = pmb_mysql_query($req,$dbh);	
			if(pmb_mysql_num_rows($result)){
				$r = pmb_mysql_fetch_object($result);
				$req= "update perio_relance set $type_comment='$comment' where rel_id=".$r->rel_id."  ";
			} else {
				$req="insert into perio_relance set rel_abt_num='".$bulletin_info['abt_id']."', rel_date_parution='".$bulletin_info['date_parution']."',  rel_libelle_numero='".addslashes($bulletin_info['libelle_numero'])."', $type_comment='$comment'  ";		
			}
			pmb_mysql_query($req,$dbh);	
		}		
	}
	
	
	public function relance_retard(){
		global $dbh;
		global $sel_relance;	
			
		if(!$sel_relance) return;		
		$rel_id_list=explode(",",$sel_relance);
		$this->liste_rel=array();
		foreach($rel_id_list as $rel_id){
			
			if(!$rel_id) continue;
			$nb=0;
			$req="SELECT * from perio_relance where rel_id=$rel_id ";
			$result = pmb_mysql_query($req,$dbh);	
			if(pmb_mysql_num_rows($result)){
				$r = pmb_mysql_fetch_object($result);
				$nb=$r->nb;
				$bulletin_info['abt_id']=$r->rel_abt_num;
				$bulletin_info['date_parution']=$r->rel_date_parution;
				$bulletin_info['libelle_numero']=$r->rel_libelle_numero;
			} else continue;
			if($nb) continue;
			// recherche de la plus grande relance
			$req="SELECT max(rel_nb)as nb from perio_relance where rel_abt_num='".$bulletin_info['abt_id']."' and rel_date_parution='".$bulletin_info['date_parution']."' and  rel_libelle_numero='".addslashes($bulletin_info['libelle_numero'])."' ";
			$result = pmb_mysql_query($req,$dbh);	
			if(pmb_mysql_num_rows($result)){
				$r = pmb_mysql_fetch_object($result);
				$nb=$r->nb;
			}			
			$nb++;	
			
			$req="SELECT * from perio_relance,abts_abts where abt_id=".$bulletin_info['abt_id']." and rel_id=$rel_id";
			$result = pmb_mysql_query($req,$dbh);
			if(pmb_mysql_num_rows($result)){
				$r = pmb_mysql_fetch_object($result);
				//if($r->rel_comment_gestion)	{	
					$this->liste_rel[$r->fournisseur][$r->num_notice][$r->rel_abt_num][$r->rel_id]["rel_date_parution"]=$r->rel_date_parution;
					$this->liste_rel[$r->fournisseur][$r->num_notice][$r->rel_abt_num][$r->rel_id]["rel_libelle_numero"]=$r->rel_libelle_numero;
					$this->liste_rel[$r->fournisseur][$r->num_notice][$r->rel_abt_num][$r->rel_id]["rel_comment_gestion"]=$r->rel_comment_gestion;
					$this->liste_rel[$r->fournisseur][$r->num_notice][$r->rel_abt_num][$r->rel_id]["rel_nb"]=$r->rel_nb;
					$req= "update perio_relance set rel_nb=$nb, rel_date=now() where rel_id=".$r->rel_id."  ";				
					pmb_mysql_query($req,$dbh);		
				//}		
			}
		}
		if($this->print_mode==1){
			$this->generate_PDF();
		}else{
			$this->generate_RTF();
		}
		return;			
	}
	
	public static function delete_retard($abt_id,$date_parution='',$libelle_numero=''){
		global $dbh;		
		$req="DELETE from perio_relance where rel_abt_num='".$abt_id."' ";
		if($date_parution)	$req.=" and rel_date_parution='".$date_parution."'  ";
		if($libelle_numero)	$req.=" and rel_libelle_numero='".addslashes($libelle_numero)."' ";
		@pmb_mysql_query($req,$dbh);
	}
	
	public function generate_PDF(){
		global $base_path,$charset, $msg, $biblio_logo;
		global $biblio_name, $biblio_logo, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email, $biblio_website ;
		global $madame_monsieur;
		global $pmb_pdf_font,$fpdf;
		
		//Document
		$largeur_page = '210';
		$hauteur_page = '297';
		$orient_page = 'P';
		$marge_haut = '10';
		$marge_bas = '20';
		$marge_gauche = '10';
		$marge_droite = '10';
		
		$retraitFournisseur=100;
		
		$taille_doc=array($largeur_page,$hauteur_page);
		$ourPDF = new $fpdf($orient_page, 'mm', $taille_doc);
		$ourPDF->Open();
		$ourPDF->SetMargins($marge_gauche, $marge_haut, $marge_droite);
		$ourPDF->setFont($pmb_pdf_font);
		
		foreach($this->liste_rel as $id_fournisseur =>$info_fournisseur ){
			//Nouvelle page
			$ourPDF->addPage();
			
			//Affichage Bibli / date édition
			$ourPDF->setFontSize(12);
			$ourPDF->Cell(150,4,$this->to_utf8($biblio_name),0);
			$ourPDF->Cell(0,4,$this->to_utf8($msg['fpdf_edite']." ".formatdate(date("Y-m-d",time()))),0);
			$ourPDF->Ln();
			$ourPDF->setFontSize(10);
			if($biblio_adr1){
				$ourPDF->Cell(0,4,$this->to_utf8($biblio_adr1),0);
				$ourPDF->Ln();
			}			
			if($biblio_adr2){
				$ourPDF->Cell(0,4,$this->to_utf8($biblio_adr2),0);
				$ourPDF->Ln();
			}
			if($biblio_cp || $biblio_town){
				$ourPDF->Cell(0,4,$this->to_utf8(trim($biblio_cp." ".$biblio_town)),0);
				$ourPDF->Ln();
			}
			if($biblio_phone){
				$ourPDF->Cell(0,4,$this->to_utf8($biblio_phone),0);
				$ourPDF->Ln();
			}
			if($biblio_email){
				$ourPDF->Cell(0,4,$this->to_utf8($biblio_email),0);
				$ourPDF->Ln();
			}
			//Fournisseur
			if($id_fournisseur){
				$fou = new entites($id_fournisseur);
				$coord_fou = entites::get_coordonnees($id_fournisseur,1);
				$coord_fou = pmb_mysql_fetch_object($coord_fou);
				if($fou->raison_sociale != '') {
					$libelleFou = $fou->raison_sociale;
				} else {
					$libelleFou = $coord_fou->libelle;
				}
				
				//Affichage fournisseur
				$ourPDF->Ln();
				$ourPDF->Ln();
				$ourPDF->setFontSize(12);
				$ourPDF->Cell($retraitFournisseur,4,"",0);
				$ourPDF->Cell(0,4,$this->to_utf8($libelleFou),0);
				$ourPDF->Ln();
				$ourPDF->setFontSize(10);
				if($coord_fou->adr1){
					$ourPDF->Cell($retraitFournisseur,4,"",0);
					$ourPDF->Cell(0,4,$this->to_utf8($coord_fou->adr1),0);
					$ourPDF->Ln();
				}
				if($coord_fou->adr2){
					$ourPDF->Cell($retraitFournisseur,4,"",0);
					$ourPDF->Cell(0,4,$this->to_utf8($coord_fou->adr2),0);
					$ourPDF->Ln();
				}
				if($coord_fou->cp){
					$ourPDF->Cell($retraitFournisseur,4,"",0);
					$ourPDF->Cell(0,4,$this->to_utf8($coord_fou->cp),0);
					$ourPDF->Ln();
				}
				if($coord_fou->ville){
					$ourPDF->Cell($retraitFournisseur,4,"",0);
					$ourPDF->Cell(0,4,$this->to_utf8($coord_fou->ville),0);
					$ourPDF->Ln();
				}
				if($coord_fou->contact!=''){
					$ourPDF->Cell($retraitFournisseur,4,"",0);
					$ourPDF->Cell(0,4,$this->to_utf8($msg['acquisition_act_formule']." ".$coord_fou->contact),0);
					$ourPDF->Ln();
				}
			}
			
			$ourPDF->Ln();
			$ourPDF->Ln();
			$ourPDF->Ln();
			$ourPDF->Ln();
			$ourPDF->Ln();
			$ourPDF->setFontSize(10);
			$ourPDF->Cell(0,4,$this->to_utf8($msg["abts_gestion_retard_lettre_monsieur"]),0);
			$ourPDF->Ln();
			$ourPDF->Ln();
			$ourPDF->Ln();
			
			foreach($info_fournisseur as $num_notice =>$info_notice ){
				$perio= new serial_display($num_notice);
				$ourPDF->Cell(0,4,$this->to_utf8($perio->notice->tit1),0);
				$ourPDF->Ln();
				$ourPDF->Cell(0,4,$this->to_utf8("________________________________________"),0);
				$ourPDF->Ln();
				$ourPDF->Ln();
				foreach($info_notice as $abt_num => $info_abt){
					foreach($info_abt as $rel_id => $rel_info){
						$ourPDF->SetFont('','U');
						$ourPDF->Cell(20,4,$this->to_utf8($rel_info["rel_libelle_numero"]." :"),0,0);
						$ourPDF->SetFont('','');
						$ourPDF->Cell(0,4,$this->to_utf8(formatdate($rel_info["rel_date_parution"])),0);
						$ourPDF->Ln();
						$ourPDF->Cell(0,4,$this->to_utf8($rel_info["rel_comment_gestion"]),0);
						$ourPDF->Ln();
					}
				}
			}
		}
		$ourPDF->OutPut();
	}
	
	public function generate_RTF(){
		
		global  $base_path,$charset, $msg, $biblio_logo;
		global $biblio_name, $biblio_logo, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email, $biblio_website ;
		global $madame_monsieur;
		//Format des fonts		
		$fontHead = new Font(12, 'Arial','#0E298A');
		$fontHead->setBold();
		$fontSmall = new Font(1);
		$fontComment = new Font(10,'Arial');
		$fontComment->setItalic();
		$fontChapter = new Font(10,'Arial');
		$fontSubChapter = new Font(10,'Arial');
		$fontSubChapter->setUnderline();		
		
		//Format des paragraphes
		$parPmb = new ParFormat();
		$parPmb->setIndentRight(12.5);
		$parPmb->setBackColor('#0E298A');
		$parPmb->setSpaceAfter(8);			
		$parHead = new ParFormat();
		$parHead->setSpaceBefore(5);		
		$parChapter = new ParFormat();
		$parChapter->setSpaceBefore(2);
		$parChapter->setSpaceAfter(1);			
		$parComment = new ParFormat();
		$parComment->setIndentLeft(1);
		$parComment->setIndentRight(0.5);			
		$parContenu = new ParFormat('justify');
		$parContenu->setIndentLeft(1);				
		$parSubChapter = new ParFormat();
		$parSubChapter->setIndentLeft(0.5);		
		$parInfo = new ParFormat();
		$parInfo->setIndentLeft(0,5);
		$parInfo->setSpaceAfter(1.5);
			
		$parInfoBib = new ParFormat();
		$parInfoBib->setIndentLeft(0);
		$parInfoBib->setSpaceAfter(1.5);		
		
		//Document
		$rtf = new Rtf();
		$rtf->setMargins(1, 1, 1 ,1);
		
		foreach($this->liste_rel as $id_fournisseur =>$info_fournisseur ){	
		
		$rtf->setMargins(1, 1, 1 ,1);
		
			$sect = &$rtf->addSection();
			$table = &$sect->addTable();
			$table->addRows(1, 2);
			$table->addColumnsList(array(15,4));
			//$table->addImageToCell(1,1,$base_path."/images/".$biblio_logo,new ParFormat('center'),0,0);
			
			// Info biblio
			$cell = &$table->getCell(1,1);	
			$cell->writeText($this->to_utf8($biblio_name), new Font(14,'Arial','#0E298A'), new ParFormat('left'));
			if($biblio_adr1)$cell->writeText($this->to_utf8($biblio_adr1), new Font(12,'Arial','#0E298A'), new ParFormat('left'));
			if($biblio_adr2)$cell->writeText($this->to_utf8($biblio_adr2), new Font(12,'Arial','#0E298A'), new ParFormat('left'));
			if($biblio_cp || $biblio_town)$cell->writeText($this->to_utf8($biblio_cp." ".$biblio_town), new Font(12,'Arial','#0E298A'), new ParFormat('left'));
			if($biblio_phone)$cell->writeText($this->to_utf8($biblio_phone), new Font(12,'Arial','#0E298A'), new ParFormat('left'));
			if($biblio_email)$cell->writeText($this->to_utf8($biblio_email), new Font(12,'Arial','#0E298A'), new ParFormat('left'));
	
			// Info date de génération 		
			$cell = &$table->getCell(1,2);
			if($biblio_email)$cell->writeText($this->to_utf8("\n".$msg['fpdf_edite']." ".formatdate(date("Y-m-d",time())),ENT_QUOTES,$charset), new Font(12,'Arial','#0E298A'), new ParFormat('right'));
	
			if($id_fournisseur){		
				$fou = new entites($id_fournisseur);
				$coord_fou = entites::get_coordonnees($id_fournisseur,1);
				$coord_fou = pmb_mysql_fetch_object($coord_fou);
				if($fou->raison_sociale != '') {
					$libelle = $fou->raison_sociale;
				} else { 
					$libelle = $coord_fou->libelle;
				}			
				$table = &$sect->addTable();
				$table->addRows(2, 2);
				$table->addColumnsList(array(9, 10));
				$cell = &$table->getCell(1,2);
				$cell->writeText($this->to_utf8($libelle), new Font(14,'Arial','#0E298A'), new ParFormat('left'));
				if($coord_fou->adr1) $cell->writeText($this->to_utf8($coord_fou->adr1), new Font(12,'Arial','#0E298A'), new ParFormat('left'));
				if($coord_fou->adr2) $cell->writeText($this->to_utf8($coord_fou->adr2), new Font(12,'Arial','#0E298A'), new ParFormat('left'));			
				if($coord_fou->cp) $cell->writeText($this->to_utf8($coord_fou->cp), new Font(12,'Arial','#0E298A'), new ParFormat('left'));
				if($coord_fou->ville)$cell->writeText($this->to_utf8($coord_fou->ville), new Font(12,'Arial','#0E298A'), new ParFormat('left'));
				if ($coord_fou->contact != ''){
					$cell = &$table->getCell(2,2);
					$cell->writeText($this->to_utf8($msg['acquisition_act_formule']." ".$coord_fou->contact), new Font(12,'Arial','#0E298A'), new ParFormat('left'));
				}
			}		
			
			$sect->writeText($this->to_utf8($msg["abts_gestion_retard_lettre_monsieur"]."<br />"), new Font(12,'Arial','#0E298A'), new ParFormat('left'));			
			foreach($info_fournisseur as $num_notice =>$info_notice ){			
				//print $num_notice; print_r($info_notice) ;exit;
				$perio= new serial_display($num_notice);
				$sect->writeText($this->to_utf8($perio->notice->tit1), $fontHead, $parHead);
				$sect->emptyParagraph($fontSmall, $parPmb);
				foreach($info_notice as $abt_num => $info_abt){
					//$sect->writeText($this->to_utf8($doc), new Font(10,'Arial'), $parInfo);
					foreach($info_abt as $rel_id => $rel_info){			
						$date = "<u>".$rel_info["rel_libelle_numero"]."</u> : ".formatdate($rel_info["rel_date_parution"]);			
						$sect->writeText($this->to_utf8($date), new Font(10,'Arial'), $parInfo);	
						$sect->writeText($this->to_utf8($rel_info["rel_comment_gestion"]), new Font(10,'Arial'), $parSubChapter);
					}				
				}	
			}	
			$sect->insertPageBreak();
		}
		$rtf->sendRtf("rapport");
	}
		
	
	
	public function to_utf8($string){
		global $charset;		
		if($charset != 'utf-8'){
			return utf8_encode($string);
		}		
		return $string;
	}
	
	public function gen_plus_form($id, $titre, $contenu) {
		global $msg;
		return "	
			<div class='row'></div>
			<div id='$id' class='notice-parent'>
				<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='$id" . "Img' title='".addslashes($msg['plus_detail'])."' style='border:0px; margin:3px 3px' onClick=\"expandBase('$id', true); return false;\">
				<span class='notice-heada'>
					$titre
				</span>
			</div>
			<div id='$id" . "Child' class='notice-child' style='margin-bottom:6px;display:none;width:94%'>
				$contenu
			</div>
			";
	}
	
	public function calc_alert() {
		global $dbh;
		global $location_view, $deflt_bulletinage_location,$pmb_abt_end_delay;
		if ($location_view == "") $location_view = $deflt_bulletinage_location;
	
		$liste_bulletin=$this->get_bulletinage();
		$cpt_a_recevoir = $cpt_en_retard = $cpt_en_alerte = 0;
		$state=array();
		if($liste_bulletin){
			//Tri par type de retard
			asort($liste_bulletin);
			foreach($liste_bulletin as $retard => $bulletin_retard){
				$cpt=0;
				$contenu='';
				foreach($bulletin_retard as $id_bull => $fiche){
					if (++$cpt % 2) $pair_impair = "even"; else $pair_impair = "odd";
				}
				if($cpt && $retard==3){
					$state["prochain_numero"]= $cpt;
				}
				if($cpt && $retard==0){
					$state["a_recevoir"]= $cpt;
				}
				if($cpt && $retard==1){
					$state["en_retard"]=	$cpt;
				}
				if($cpt && $retard==2){
					$state["en_alerte"]=	$cpt;
				}
			}
		}
		return($state);
	}
	
}// Fin de la Classe

function increment_bulletin($modele_id, &$num,$num_abt) {
	// num_cycle 	num_combien 	num_increment 	num_date_unite 	num_increment_date 	num_depart 	
	// vol_actif 	vol_increment 	vol_date_unite 	vol_increment_numero 	vol_increment_date 	vol_cycle 	vol_combien 	vol_depart 	
	// tom_actif 	tom_increment 	tom_date_unite 	tom_increment_numero 	tom_increment_date 	tom_cycle 	tom_combien 	tom_depart 	
	// format_aff			
	$num[$num_abt]['num']++;

	if ($num['num_cycle']) {
		if (!$num['num_increment']) { //numero cyclique selon un nombre de bulletin
			if ($num[$num_abt]['num'] > $num['num_combien']) {
				$num[$num_abt]['num'] = $num['num_depart'];
			}
		} elseif($num[$num_abt]['num_date_fin_cycle'] && $num[$num_abt]['date_parution']){ // numero cyclique selon la date
			while (pmb_sql_value("SELECT DATEDIFF('" . $num[$num_abt]['num_date_fin_cycle'] . "','" . $num[$num_abt]['date_parution'] . "')") <= 0) {
				$num[$num_abt]['num'] = $num['num_depart'];
				$tmp_num_date_fin_cycle = pmb_sql_value("SELECT DATE_ADD('" . $num[$num_abt]['num_date_fin_cycle'] . "', INTERVAL " . $num['num_date_sql'] . ")");
				if(($num[$num_abt]['num_date_fin_cycle'] == $tmp_num_date_fin_cycle) && (preg_match("/^0 /",trim($num['num_date_sql'])))){
				      break;
				}else{
					$num[$num_abt]['num_date_fin_cycle'] = $tmp_num_date_fin_cycle;
				}			
			}
		}
	}

	if ($num['vol_actif']) {
		if ($num['inc_vol'] == 1) {
			$num[$num_abt]['vol']++;
			$num['inc_vol'] = 0;
		}
		if (!$num['vol_increment']) { //volume s'incrémente selon un nombre de bulletin
			$modulo = ($num[$num_abt]['num']) % ($num['vol_increment_numero']);
			if ($modulo == 0) {
				$num['inc_vol'] = 1;
			}
		} elseif($num[$num_abt]['vol_date_fin_cycle'] && $num[$num_abt]['date_parution']){ // volume s'incrémente selon la date 			
			while (pmb_sql_value("SELECT DATEDIFF('" . $num[$num_abt]['vol_date_fin_cycle'] . "','" . $num[$num_abt]['date_parution'] . "')") <= 0) {
				$num[$num_abt]['vol']++;
				$tmp_vol_date_fin_cycle = pmb_sql_value("SELECT DATE_ADD('" . $num[$num_abt]['vol_date_fin_cycle'] . "', INTERVAL " . $num['vol_date_sql'] . ")");
				if(($num[$num_abt]['vol_date_fin_cycle'] == $tmp_vol_date_fin_cycle) && (preg_match("/^0 /",trim($num['vol_date_sql'])))){
					break;
				}else{
					$num[$num_abt]['vol_date_fin_cycle'] = $tmp_vol_date_fin_cycle;
				}
			}
		}
		// Si volume est cyclique
		if ($num['vol_cycle']) {
			if ($num[$num_abt]['vol'] > $num['vol_combien']) {
				$num[$num_abt]['vol'] = $num['vol_depart'];
			}
		}
	}

	if ($num['tom_actif']) {
		if (($num['inc_tom'] == 1) && ($num['val_vol'] != $num[$num_abt]['vol'])) {
			$num[$num_abt]['tom']++;
			$num['inc_tom'] = 0;
		}
		if (!$num['tom_increment']) { //tome s'incrémente selon un nombre de volume
			if ($num['val_vol'] != $num[$num_abt]['vol']) {
				$num['val_vol'] = $num[$num_abt]['vol'];
				$modulo = ($num[$num_abt]['vol']) % ($num['tom_increment_numero']);
				if ($modulo == 0) {
					$num['inc_tom'] = 1;
				}
			}
		} elseif($num[$num_abt]['tom_date_fin_cycle'] && $num[$num_abt]['date_parution']){ // tome s'incrémente selon la date
			while (pmb_sql_value("SELECT DATEDIFF('" . $num[$num_abt]['tom_date_fin_cycle'] . "','" . $num[$num_abt]['date_parution'] . "')") <= 0) {
				$num[$num_abt]['tom']++;
				$tmp_tom_date_fin_cycle = pmb_sql_value("SELECT DATE_ADD('" . $num[$num_abt]['tom_date_fin_cycle'] . "', INTERVAL " . $num['tom_date_sql'] . ")");
				if(($num[$num_abt]['tom_date_fin_cycle'] == $tmp_tom_date_fin_cycle) && (preg_match("/^0 /",trim($num['tom_date_sql'])))){
					break;
				}else{
					$num[$num_abt]['tom_date_fin_cycle'] = $tmp_tom_date_fin_cycle;
				}
			}
		}
		// Si tome est cyclique
		if ($num['tom_cycle']) {
			if ($num[$num_abt]['tom'] > $num['tom_combien']) {
				$num[$num_abt]['tom'] = $num['tom_depart'];
			}
		}
	}
}
