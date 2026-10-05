<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: receptions.class.php,v 1.13 2021/12/28 08:46:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once("$class_path/analyse_query.class.php");
require_once("$include_path/user_error.inc.php");


if(!defined('TYP_ACT_CDE')) define('TYP_ACT_CDE', 0);	//				0 = Commande
if(!defined('TYP_ACT_DEV')) define('TYP_ACT_DEV', 1);	//				1 = Demande de devis
if(!defined('TYP_ACT_LIV')) define('TYP_ACT_LIV', 2);	//				2 = Bon de Livraison
if(!defined('TYP_ACT_FAC')) define('TYP_ACT_FAC', 3);	//				3 = Facture
if(!defined('TYP_ACT_RENT_ACC')) define('TYP_ACT_RENT_ACC', 4);	//		4 = Demande/Décompte de location
if(!defined('TYP_ACT_RENT_INV')) define('TYP_ACT_RENT_INV', 5);	//		5 = Facture de Location

if(!defined('STA_ACT_ALL')) define('STA_ACT_ALL', -1);	//Statut acte	-1 = Tous
if(!defined('STA_ACT_AVA')) define('STA_ACT_AVA', 1);	//				1 = A valider
if(!defined('STA_ACT_ENC')) define('STA_ACT_ENC', 2);	//				2 = En cours
if(!defined('STA_ACT_REC')) define('STA_ACT_REC', 4);	//				4 = Reçu/Livré
if(!defined('STA_ACT_FAC')) define('STA_ACT_FAC', 8);	//				8 = Facturé
if(!defined('STA_ACT_PAY')) define('STA_ACT_PAY', 16);	//				16 = Payé
if(!defined('STA_ACT_ARC')) define('STA_ACT_ARC', 32);	//				32 = Archivé

class receptions {
	
	public $id_bibli=0;
	public $id_exer=0;
	public $type_acte=TYP_ACT_CDE;
	public $filtre_actes='';
	public $filtre_lignes='';
	public $filtre_origines='';
	public $nb_lignes=0;
	public $t_list=array();
	public $error='';
	
	//Constructeur
	public function __construct($id_bibli,$id_exer) {
		$this->id_bibli=intval($id_bibli);
		$this->id_exer=intval($id_exer);
	}
	
	
	//cree les filtres des requetes de selection
	public function setFiltres($tab_fou=array(),$tab_empr=array(),$tab_user=array(), $tab_rub=array(), $chk_dev='0',$cde_query='', $lgstat_filter=array(), $date_inf='', $date_sup='') {
		$this->filtre_actes="actes.num_entite='".$this->id_bibli."' and actes.num_exercice='".$this->id_exer."' and (actes.statut & ".STA_ACT_ENC." = ".STA_ACT_ENC.") ";
		
		$type_acte=TYP_ACT_CDE;
		if ($chk_dev) {
			$type_acte=TYP_ACT_DEV;
			$this->type_acte = $type_acte;
		}
		
		$this->filtre_actes.=  "and actes.type_acte = '".$type_acte."' ";
		
		if ($cde_query) {
			$cde_query = trim($cde_query);
			$this->filtre_actes.=  "and actes.numero like '%".$cde_query."%' ";
		}

		if (is_array($tab_fou) && count($tab_fou)) {		
			$this->filtre_actes.= "and actes.num_fournisseur in ('".implode("','",$tab_fou)."') ";
		}
		
		if ($date_inf) {
			$this->filtre_actes.= "and actes.date_acte >= '".$date_inf."' ";
		} 
		if ($date_sup) {
			$this->filtre_actes.= "and actes.date_acte <= '".$date_sup."' ";
		}		
		
		$this->filtre_lignes = "and lignes_actes.type_ligne != '3' ";	//pas de non recevable
		
		if (is_array($tab_rub) && count($tab_rub)) {		
			$this->filtre_lignes.= "and lignes_actes.num_rubrique in ('".implode("','",$tab_rub)."') ";
		}
		
		if (is_array($lgstat_filter) && count($lgstat_filter) && $lgstat_filter[0]) {
			$this->filtre_lignes.= "and lignes_actes.statut in ('".implode("','",$lgstat_filter)."') ";
		}
		
		$filtre_empr='';
		$filtre_user='';
		if (is_array($tab_empr) && count($tab_empr)) {
			$filtre_empr = "suggestions_origine.origine in ('".implode("','",$tab_empr)."') and type_origine='1' ";
		}
		if (is_array($tab_user) && count($tab_user)) {		
			$filtre_user = "suggestions_origine.origine in ('".implode("','",$tab_user)."') and type_origine='0' ";
		}
		if ($filtre_empr && $filtre_user) {
			$this->filtre_origines= "and ( (".$filtre_empr.") or (".$filtre_user.") ) ";
		} elseif($filtre_empr) {
			$this->filtre_origines= "and (".$filtre_empr.") ";
		} elseif ($filtre_user){
			$this->filtre_origines= "and (".$filtre_user.") ";
		}
	}
	
	//Compte le nb de lignes d'acte en reception
	public function calcNbLignes($all_query='') {
		global $msg;
		
		//analyse_query 
		switch ($this->type_acte) {
			
			case TYP_ACT_CDE :
				
				if ($all_query=='') {
					
					$q_cde = "create temporary table tmp_cde as ";
					$q_cde.= "select distinct id_ligne, id_acte, numero, actes.num_fournisseur as num_fournisseur, raison_sociale, type_ligne, date_acte, lignes_actes.nb as nb_cde, ";
					$q_cde.= "lignes_actes.code as code, libelle, lignes_actes.statut, lignes_actes.commentaires_gestion, lignes_actes.commentaires_opac, lignes_actes.num_produit as num_produit, num_acquisition ";
					$q_cde.= "from lignes_actes join actes on num_acte=id_acte join entites on actes.num_fournisseur=id_entite ";
					$q_cde.= "left join suggestions on num_acquisition=id_suggestion left join suggestions_origine on num_suggestion=id_suggestion ";
					$q_cde.= "where ";
					$q_cde.= $this->filtre_actes.$this->filtre_lignes.$this->filtre_origines;
					pmb_mysql_query($q_cde);
					//echo $q_cde.'<br />';
					
					$q_liv = "create temporary table tmp_liv as ";
					$q_liv.= "select lig_ref, sum(nb) as nb_liv from lignes_actes "; 
					$q_liv.= "join actes on num_acte=id_acte and type_acte='".TYP_ACT_LIV."' ";
					$q_liv.= "where lig_ref in (select id_ligne from tmp_cde)";
					$q_liv.= "group by lig_ref ";
					pmb_mysql_query($q_liv);
					//echo $q_liv.'<br />';
					
					$q_sol = "select distinct id_ligne, id_acte, numero, num_fournisseur, raison_sociale, type_ligne, date_acte, nb_cde ,if(nb_liv is null,0,nb_liv) as nb_liv, if(nb_liv is null,nb_cde,((nb_cde*1)-(nb_liv*1))) as nb_sol, ";
					$q_sol.= "code, libelle, statut, commentaires_gestion, commentaires_opac, num_produit, num_acquisition ";
					$q_sol.= "from tmp_cde left join tmp_liv on id_ligne=lig_ref ";
					$q_sol.= "where ((nb_cde*1)-(nb_liv*1)) > 0 or nb_liv is null ";
					$q_sol.= "order by raison_sociale, numero ";
					$r_sol = pmb_mysql_query($q_sol);
					//echo $q_sol.'<br />';
					
					$r = pmb_mysql_num_rows($r_sol);
					if ($r) {
						while ($row=pmb_mysql_fetch_object($r_sol)) {
							$i=$row->id_acte;
							$j=$row->id_ligne;
							$this->t_list[$i][$j]=array();
							$this->t_list[$i][$j]['numero'] = $row->numero;  
							$this->t_list[$i][$j]['date_acte'] = $row->date_acte;
							$this->t_list[$i][$j]['num_fournisseur'] = $row->num_fournisseur;
							$this->t_list[$i][$j]['raison_sociale'] = $row->raison_sociale;
							$this->t_list[$i][$j]['type_ligne'] = $row->type_ligne;
							$this->t_list[$i][$j]['nb_cde']  =  $row->nb_cde;
							$this->t_list[$i][$j]['nb_liv']  =  $row->nb_liv;	
							$this->t_list[$i][$j]['nb_sol']  =  $row->nb_sol;
							$this->t_list[$i][$j]['code'] = $row->code;
							$this->t_list[$i][$j]['libelle'] = $row->libelle;
							$this->t_list[$i][$j]['statut'] = $row->statut;
							$this->t_list[$i][$j]['commentaires_gestion'] = $row->commentaires_gestion;
							$this->t_list[$i][$j]['commentaires_opac'] = $row->commentaires_opac;
							$this->t_list[$i][$j]['num_produit'] = $row->num_produit;
							$this->t_list[$i][$j]['num_acquisition'] = $row->num_acquisition;
						}
					}

				} else {

					$aq=new analyse_query(stripslashes($all_query),0,0,0,0);
					
					if ($aq->error) {
						$this->error=return_error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
					} else {
										
						//$members_actes = $aq->get_query_members("actes","numero","index_acte", "id_acte");
						$members_actes = array('where' => '0');
						
						$members_lignes = $aq->get_query_members("lignes_actes","code","index_ligne", "id_ligne");
						$members_global = $aq->get_query_members("notices_global_index","infos_global","index_infos_global","num_notice");
		
						$q_cde = "create temporary table tmp_cde as ( ";
						$q_cde.= "select distinct id_ligne,id_acte, numero, actes.num_fournisseur as num_fournisseur, raison_sociale, type_ligne, date_acte, lignes_actes.nb as nb_cde,  ";
						$q_cde.= "lignes_actes.code as code, libelle, lignes_actes.statut, lignes_actes.commentaires_gestion, lignes_actes.commentaires_opac, lignes_actes.num_produit as num_produit, num_acquisition ";
						$q_cde.= "from lignes_actes join actes on num_acte=id_acte join entites on actes.num_fournisseur=id_entite ";
						$q_cde.= "left join notices_global_index on num_produit=notices_global_index.num_notice and type_ligne in ('1','5') ";
						$q_cde.="left join suggestions on num_acquisition=id_suggestion left join suggestions_origine on num_suggestion=id_suggestion ";
						$q_cde.= "where ";
						$q_cde.= $this->filtre_actes.$this->filtre_lignes.$this->filtre_origines;
						$q_cde.= "and (".$members_actes['where']." ";
						$q_cde.= "or ".$members_lignes['where']." ";
						$q_cde.= "or ".$members_global['where'].") ";
						
						$q_cde.= ") union (";
						
						$q_cde.= "select distinct id_ligne,id_acte, numero, actes.num_fournisseur as num_fournisseur, raison_sociale, type_ligne, date_acte, lignes_actes.nb as nb_cde, ";
						$q_cde.= "lignes_actes.code as code, libelle, lignes_actes.statut, lignes_actes.commentaires_gestion, lignes_actes.commentaires_opac, lignes_actes.num_produit as num_produit, num_acquisition ";
						$q_cde.= "from lignes_actes join actes on num_acte=id_acte join entites on actes.num_fournisseur=id_entite ";
						$q_cde.= "left join bulletins on num_produit=bulletins.num_notice and type_ligne='2' ";
						$q_cde.= "left join notices_global_index on bulletins.num_notice=notices_global_index.num_notice ";
						$q_cde.="left join suggestions on num_acquisition=id_suggestion left join suggestions_origine on num_suggestion=id_suggestion ";
						$q_cde.= "where ";
						$q_cde.= $this->filtre_actes.$this->filtre_lignes.$this->filtre_origines;
						$q_cde.= "and (".$members_actes['where']." ";
						$q_cde.= "or ".$members_lignes['where']." ";
						$q_cde.= "or ".$members_global['where'].") ";
						$q_cde.= ")  ";
						
						pmb_mysql_query($q_cde);
						//echo $q_cde.'<br />';
						
						$q_liv = "create temporary table tmp_liv as ";
						$q_liv.= "select lig_ref,sum(nb) as nb_liv from lignes_actes "; 
						$q_liv.= "join actes on lignes_actes.num_acte=actes.id_acte and actes.type_acte='".TYP_ACT_LIV."' ";
						$q_liv.= "where lig_ref in (select id_ligne from tmp_cde) ";
						$q_liv.= "group by lig_ref ";
						pmb_mysql_query($q_liv);
						//echo $q_liv.'<br />';
						
						$q_sol = "select distinct id_ligne, id_acte, numero, num_fournisseur, raison_sociale, type_ligne, date_acte, nb_cde, if(nb_liv is null,0,nb_liv) as nb_liv, if(nb_liv is null,nb_cde, ((nb_cde*1)-(nb_liv*1))) as nb_sol, ";
						$q_sol.= "code, libelle, statut, commentaires_gestion, commentaires_opac, num_produit, num_acquisition ";
						$q_sol.= "from tmp_cde left join tmp_liv on id_ligne=lig_ref ";
						$q_sol.= "where ((nb_cde*1)-(nb_liv*1)) > 0 or nb_liv is null ";
						$q_sol.= "order by raison_sociale, numero ";
						$r_sol = pmb_mysql_query($q_sol);
						//echo $q_sol.'<br />';
						
						$r = pmb_mysql_num_rows($r_sol);
						if ($r) {
							while ($row=pmb_mysql_fetch_object($r_sol)) {
								$i=$row->id_acte;
								$j=$row->id_ligne;
								$this->t_list[$i][$j]=array();
								$this->t_list[$i][$j]['numero'] = $row->numero;  
								$this->t_list[$i][$j]['date_acte'] = $row->date_acte;
								$this->t_list[$i][$j]['num_fournisseur'] = $row->num_fournisseur;
								$this->t_list[$i][$j]['raison_sociale'] = $row->raison_sociale;
								$this->t_list[$i][$j]['type_ligne'] = $row->type_ligne;
								$this->t_list[$i][$j]['nb_cde']  =  $row->nb_cde;
								$this->t_list[$i][$j]['nb_liv']  =  $row->nb_liv;	
								$this->t_list[$i][$j]['nb_sol']  =  $row->nb_sol;
								$this->t_list[$i][$j]['code'] = $row->code;
								$this->t_list[$i][$j]['libelle'] = $row->libelle;
								$this->t_list[$i][$j]['statut'] = $row->statut;
								$this->t_list[$i][$j]['commentaires_gestion'] = $row->commentaires_gestion;
								$this->t_list[$i][$j]['commentaires_opac'] = $row->commentaires_opac;
								$this->t_list[$i][$j]['num_produit'] = $row->num_produit;
								$this->t_list[$i][$j]['num_acquisition'] = $row->num_acquisition;
							}
						}
					}
				}
				break;
			
			case TYP_ACT_DEV :
				
				if ($all_query=='') {
				/*			
					$this->q_count = "select count(distinct id_ligne) from lignes_actes ";
					$this->q_count.= "join actes on lignes_actes.num_acte=actes.id_acte ";
					if ($this->filtre_origines) $this->q_count.="join suggestions on lignes_actes.num_acquisition=suggestions.id_suggestion join suggestions_origine on num_suggestion=id_suggestion ";
					$this->q_count.= "where ";
					$this->q_count.= $this->filtre_actes.$this->filtre_lignes.$this->filtre_origines;
					
					$this->q_list = "select lignes_actes.*, actes.numero, nom_acte from lignes_actes ";
					$this->q_list.= "join actes on lignes_actes.num_acte=actes.id_acte ";
					if ($this->filtre_origines) $this->q_list.="join suggestions on lignes_actes.num_acquisition=suggestions.id_suggestion join suggestions_origine on num_suggestion=id_suggestion ";
					$this->q_list.= "where ";
					$this->q_list.= $this->filtre_actes.$this->filtre_lignes.$this->filtre_origines;
					$this->q_list.= "group by id_ligne order by date_acte";	
				*/			
					
				} else {
					
				}
				break;	
				
		}			
		
		if ($this->error) {
			return 0;
		} else {
			return $r;
		}
		 
	}
	
	public function getError() {
		return $this->error;
	}
	
	//Retourne les lignes d'acte en reception
	public function getLignes() {
		return $this->t_list;
	}
}

?>