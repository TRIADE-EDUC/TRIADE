<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rubriques.class.php,v 1.31 2022/07/07 13:45:40 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $base_path, $line;

require_once("$class_path/actes.class.php");
require_once("$base_path/acquisition/achats/func_achats.inc.php");


class rubriques{
	
	public $id_rubrique = 0;						//Identifiant de rubrique	
	public $num_budget = 0;						//Identifiant du budget auquel appartient la rubrique
	public $num_parent = 0;						//Identifiant de la rubrique parent (0 si rubrique de tête)
	public $libelle = '';							//Libellé de rubrique
	public $commentaires = '';						//Commentaires sur la rubrique
	public $montant = '000000.00';					//Montant affecté à la rubrique
	public $num_cp_compta = '';					//Numéro de compte comptable pour affectation
	public $autorisations = '';					//Autorisations d'accès à la rubrique

	 
	//Constructeur.	 
	public function __construct($id_rubrique= 0) {
		$this->id_rubrique = intval($id_rubrique);
		if ($this->id_rubrique) {
			$this->load();	
		}
	}
	
	
	// charge une rubrique à partir de la base.
	public function load(){
		$q = "select * from rubriques where id_rubrique = '".$this->id_rubrique."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->num_budget = $obj->num_budget;
		$this->num_parent = $obj->num_parent;
		$this->libelle = $obj->libelle;
		$this->commentaires = $obj->commentaires;
		$this->montant = $obj->montant;
		$this->num_cp_compta = $obj->num_cp_compta;
		$this->autorisations = $obj->autorisations;
	}
	
	protected function autorisations() {
		global $charset;
		global $autorisations_selection_content_form, $autorisations_user_content_form;
		
		//affichage entete
		$content_form = $autorisations_selection_content_form.'<!-- autorisations -->';
		
		$bud = new budgets($this->num_budget);
		
		//récupération des autorisations de l'entité
		$bibli = new entites($bud->num_entite);
		$aut_entite = $bibli->autorisations;
		
		$aut = '';
		//récupération autorisations rubrique
		if ($this->id_rubrique) {
			$aut = $this->autorisations;
		} else {
			//récupération autorisations rubrique parent
			if ($this->num_parent) {
				$rub_par = new rubriques($this->num_parent);
				$aut = $rub_par->autorisations;
			}
			if ($aut=='') $aut = $aut_entite;
		}
		
		$aut = explode(' ',$aut);
		$aut_entite = explode(' ', $aut_entite);
		
		//récupération liste des noms d'utilisateurs pmb
		$q = "SELECT userid, username FROM users order by username ";
		$r = pmb_mysql_query($q);
		
		$id_check_list = '';
		while($row = pmb_mysql_fetch_object($r)){
			if(in_array($row->userid, $aut_entite)) {
				$content_form = str_replace('<!-- autorisations -->', $autorisations_user_content_form.'<!-- autorisations -->', $content_form);
				
				$content_form = str_replace('!!user_name!!', htmlentities($row->username,ENT_QUOTES, $charset), $content_form);
				$content_form = str_replace('!!user_id!!', $row->userid, $content_form);
				if (in_array($row->userid, $aut)) {
					$chk = 'checked=\'checked\'';
				} else {
					$chk = '';
				}
				$content_form = str_replace('!!checked!!', $chk, $content_form);
				
				if($id_check_list)$id_check_list.='|';
				$id_check_list.="user_aut[".$row->userid."]";
			}
		}
		$content_form = str_replace('!!auto_id_list!!', $id_check_list, $content_form);
		return $content_form;
	}
	
	public function get_form() {
		global $msg;
		global $charset;
		global $rub_content_form, $rub_js_form;
		global /*$ptab, */$bt_add_lig;
		global $lig_rub, $lig_rub_img;
		global $mnt_rub_form;
		
		//Récuperation du budget
		if ($this->num_budget) $bud= new budgets($this->num_budget);
		else die();
		
		$content_form = $rub_content_form;
		
		$interface_form = new interface_admin_acquisition_form('rubform');
		if(!$this->id_rubrique){
			$interface_form->set_label($msg['acquisition_ajout_rub']);
		}else{
			$interface_form->set_label($msg['acquisition_modif_rub']);
		}
		
		if(!$this->id_rubrique) { //création de rubrique
			//Affichage barre de navigation
			$nav_form = "<a href=\"./admin.php?categ=acquisition&sub=budget&action=modif&id_bibli=".$bud->num_entite."&id_bud=".$this->num_budget."\" >".$bud->libelle."</a>";
			if ($this->num_parent) {
				$list_bar = rubriques::listAncetres($this->num_parent, TRUE);
				foreach ($list_bar as $value) {
					$nav_form.= "&nbsp;&gt;&nbsp;<a href=\"./admin.php?categ=acquisition&sub=budget&action=modif_rub&id_bud=".$this->num_budget."&id_rub=".$value[0]."&id_parent=".$value[2]."\" >".htmlentities($value[1], ENT_QUOTES, $charset)."</a>";
				}
			}
			$content_form = str_replace('<!-- nav_form -->', $nav_form, $content_form);
			$content_form = str_replace('!!libelle!!', '', $content_form);
			
			if ($bud->type_budget == TYP_BUD_RUB ) {
				$content_form = str_replace('<!-- lib_mnt -->', $mnt_rub_form[0], $content_form);
				$mnt_rub = str_replace('!!mnt_rub!!', '0.00',$mnt_rub_form[1]);
				$content_form = str_replace('<!-- montant -->', $mnt_rub, $content_form);
				$content_form = str_replace('!!lib_mnt!!', htmlentities($msg['acquisition_rub_mnt'], ENT_QUOTES, $charset), $content_form);
			} else {
				$content_form = str_replace('!!lib_mnt!!', '&nbsp;', $content_form);
			}
			
			$label_ncp ="<label class='etiquette' for='ncp'>".htmlentities($msg['acquisition_num_cp_compta'],ENT_QUOTES,$charset)."</label>";
			$content_form = str_replace('<!-- label_ncp -->', $label_ncp, $content_form);
			
			$ncp = "<input type='text' id='ncp' name='ncp' class='saisie-30em' style='text-align:right' value='' />";
			$content_form = str_replace('!!ncp!!', $ncp, $content_form);
			$content_form = str_replace('!!comment!!', '', $content_form);
			
			//complément du formulaire
			$content_form = str_replace('!!id_bibli!!', $bud->num_entite, $content_form);
			$content_form = str_replace('!!id_bud!!', $this->num_budget, $content_form);
			$content_form = str_replace('!!id_rub!!', $this->id_rubrique, $content_form);
			$content_form = str_replace('!!id_parent!!', $this->num_parent, $content_form);
			
			//Affichage des autorisations
			$content_form = str_replace('<!-- autorisations -->', $this->autorisations(), $content_form);
		} else { //modification de rubrique
			//Affichage barre de navigation
			$nav_form = "<a href=\"./admin.php?categ=acquisition&sub=budget&action=modif&id_bibli=".$bud->num_entite."&id_bud=".$this->num_budget."\" >".$bud->libelle."</a>";
			$list_bar = rubriques::listAncetres($this->id_rubrique, FALSE);
			foreach ($list_bar as $value) {
				$nav_form.= "&nbsp;&gt;&nbsp;<a href=\"./admin.php?categ=acquisition&sub=budget&action=modif_rub&id_bud=".$this->num_budget."&id_rub=".$value[0]."&id_parent=".$value[2]."\" >".htmlentities($value[1], ENT_QUOTES, $charset)."</a>";
				
			}
			$content_form = str_replace('<!-- nav_form -->', $nav_form, $content_form);
			$content_form = str_replace('!!libelle!!', htmlentities($this->libelle,ENT_QUOTES,$charset), $content_form);
			
			if (!$bud->type_budget) {
				
				$content_form = str_replace('!!lib_mnt!!', htmlentities($msg['acquisition_rub_mnt'], ENT_QUOTES, $charset), $content_form);
				
				if(rubriques::countChilds($this->id_rubrique)) {
					$ncp = '&nbsp;';
					$aut = FALSE;
				} else {
					$content_form = str_replace('<!-- lib_mnt -->', $mnt_rub_form[0], $content_form);
					$mnt_rub = str_replace('!!mnt_rub!!', $this->montant, $mnt_rub_form[1]);
					$content_form = str_replace('<!-- montant -->', $mnt_rub, $content_form);
					$label_ncp ="<label class='etiquette' for='ncp'>".htmlentities($msg['acquisition_num_cp_compta'],ENT_QUOTES,$charset)."</label>";
					$ncp = "<input type='text' id='ncp' name='ncp' class='saisie-30em' style='text-align:right' value='".$this->num_cp_compta."' />";
					$aut = TRUE;
				}
			} else {
				$content_form = str_replace('!!lib_mnt!!', '&nbsp;', $content_form);
				
				if(rubriques::countChilds($this->id_rubrique)) {
					$ncp = '&nbsp;';
					$aut = FALSE;
				} else {
					$label_ncp ="<label class='etiquette' for='ncp'>".htmlentities($msg['acquisition_num_cp_compta'],ENT_QUOTES,$charset)."</label>";
					$ncp = "<input type='text' id='ncp' name='ncp' class='saisie-30em' style='text-align:right' value='".$this->num_cp_compta."' />";
					$aut = TRUE;
				}
			}
			$content_form = str_replace('<!-- label_ncp -->', $label_ncp, $content_form);
			$content_form = str_replace('!!ncp!!', $ncp, $content_form);
			
			$content_form = str_replace('!!comment!!', htmlentities($this->commentaires,ENT_QUOTES,$charset), $content_form);
			
			//complément du formulaire
			$content_form = str_replace('!!id_rub!!', $this->id_rubrique, $content_form);
			$content_form = str_replace('!!id_parent!!', $this->num_parent, $content_form);
			
			//affichage du bouton ajout rubrique si budget non clôturé
			if ($bud->statut != STA_BUD_CLO ) {
				$bt_add_lig = str_replace('!!id_rub!!', '0', $bt_add_lig);
				$bt_add_lig = str_replace('!!id_parent!!', $this->id_rubrique, $bt_add_lig);
				$content_form = str_replace('<!-- bouton_lig -->', $bt_add_lig, $content_form);
			}
			
			$content_form = str_replace('!!id!!', $this->id_rubrique, $content_form);
			
			//Affichage rubriques budgetaires
			$q = budgets::listRubriques($this->num_budget, $this->id_rubrique);
			$list_n1 = pmb_mysql_query($q);
			while($row=pmb_mysql_fetch_object($list_n1)){
				$content_form = str_replace('<!-- rubriques -->', $lig_rub[0].'<!-- rubriques -->', $content_form);
				$content_form = str_replace('<!-- marge -->', '', $content_form);
				if (rubriques::countChilds($row->id_rubrique)) {
					$content_form = str_replace('<!-- img_plus -->', $lig_rub_img, $content_form);
				} else {
					$content_form = str_replace('<!-- img_plus -->', '', $content_form);
				}
				$content_form = str_replace('!!id_rub!!', $row->id_rubrique, $content_form);
				$content_form = str_replace('!!id_parent!!', $row->num_parent, $content_form);
				$content_form = str_replace('!!lib_rub!!', $row->libelle, $content_form);
				if (!$bud->type_budget) {
					$content_form = str_replace('!!mnt!!', $row->montant, $content_form);
				} else {
					$content_form = str_replace('!!mnt!!', '&nbsp;', $content_form);
				}
				$content_form = str_replace('!!ncp!!', $row->num_cp_compta, $content_form);
				$content_form = str_replace('<!-- sous_rub -->', '<!-- sous_rub'.$row->id_rubrique.' -->', $content_form);
				
				afficheSousRubriques($this->num_budget, $row->id_rubrique, $content_form, 1);
			}
			//complément du formulaire
			$content_form = str_replace('!!id_bibli!!', $bud->num_entite, $content_form);
			$content_form = str_replace('!!id_bud!!', $this->num_budget, $content_form);
			
			//Affichage des autorisations
			if ($aut) {
				$content_form = str_replace('<!-- autorisations -->', $this->autorisations(), $content_form);
			}
		}
		
		$biblio = new entites($bud->num_entite);
		$form = "<div class='row'><label class='etiquette'>".htmlentities($biblio->raison_sociale,ENT_QUOTES,$charset)."</label></div>";
		
		$interface_form->set_object_id($this->id_rubrique)
		->set_id_entity($bud->num_entite)
		->set_id_budget($this->num_budget)
		->set_id_parent($this->num_parent)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($content_form)
		->set_table_name('rubriques')
		->set_field_focus('libelle');
		$form .= $interface_form->get_display();
		
		$form .= $rub_js_form;
		return $form;
	}
	
	public function set_properties_from_form() {
		global $id_parent, $libelle, $comment, $mnt, $ncp, $user_aut;
		
		$this->num_parent = intval($id_parent);
		$this->libelle = stripslashes($libelle);
		$this->commentaires = stripslashes($comment);
		if(isset($mnt))$this->montant = $mnt;
		$this->num_cp_compta = stripslashes($ncp);
		if (is_array($user_aut)) $this->autorisations = ' '.implode(' ',$user_aut).' ';
		else $this->autorisations = '';
	}
	
	// enregistre une rubrique en base.
	public function save(){
		if ($this->libelle == '' || !$this->num_budget) die("Erreur de création rubriques");
	
		if ($this->id_rubrique) {
			
			$q = "update rubriques set num_budget = '".$this->num_budget."', num_parent = '".$this->num_parent."', libelle = '".addslashes($this->libelle)."', ";
			$q.= "commentaires = '".addslashes($this->commentaires)."', montant = '".addslashes($this->montant)."', num_cp_compta = '".addslashes($this->num_cp_compta)."', autorisations = '".$this->autorisations."' ";
			$q.= "where id_rubrique = '".$this->id_rubrique."' ";
			pmb_mysql_query($q);
			
		} else {
			
			$q = "insert into rubriques set num_budget = '".$this->num_budget."', num_parent = '".$this->num_parent."', libelle = '".addslashes($this->libelle)."', ";
			$q.= "commentaires = '".addslashes($this->commentaires)."', montant = '".addslashes($this->montant)."', num_cp_compta = '".addslashes($this->num_cp_compta)."', autorisations = '".$this->autorisations."' ";
			pmb_mysql_query($q);
			$this->id_rubrique = pmb_mysql_insert_id();
			
		}

	}

	//supprime un rubrique de la base
	public static function delete($id_rubrique= 0) {
		if(!$id_rubrique) return; 	

		$q = "delete from rubriques where id_rubrique = '".$id_rubrique."' ";
		pmb_mysql_query($q);
	}
	
	//calcule le montant engagé pour une rubrique budgétaire
	public static function calcEngagement($id_rubrique= 0) {
		$id_rubrique = intval($id_rubrique);
		//	Montant Total engagé pour une rubrique =
		//	Somme des Montants engagés non facturés pour une rubrique par ligne de commande		(nb_commandé-nb_facturé)*prix_commande*(1-remise_commande)
		//+ Somme des Montants engagés pour une rubrique par ligne de facture					(nb_facturé)*prix_facture*(1-remise_facture)

		$q1 = "select ";
		$q1.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, lignes_actes.remise as rem, lignes_actes.debit_tva ";
		$q1.= "from actes, lignes_actes ";
		$q1.= "where ";
		$q1.= "lignes_actes.num_rubrique = '".$id_rubrique."' ";
		$q1.= "and (actes.type_acte = '".TYP_ACT_CDE."' or actes.type_acte = '".TYP_ACT_RENT_ACC."') ";
		$q1.= "and actes.statut > '".STA_ACT_AVA."' and ( (actes.statut & ".STA_ACT_FAC.") != ".STA_ACT_FAC.") ";
		$q1.= "and actes.id_acte = lignes_actes.num_acte ";
		$r1 = pmb_mysql_query($q1);

		$tab_cde = array();
		while($row1 = pmb_mysql_fetch_object($r1)) {
			
			$tab_cde[$row1->id_ligne]['nb']=$row1->nb;
			$tab_cde[$row1->id_ligne]['prix']=$row1->prix;				
			$tab_cde[$row1->id_ligne]['rem']=$row1->rem;				
			$tab_cde[$row1->id_ligne]['debit_tva']=$row1->debit_tva;
		
		}			
		
		$q2 = "select ";
		$q2.= "lignes_actes.lig_ref, sum(nb) as nb, lignes_actes.debit_tva ";
		$q2.= "from actes, lignes_actes ";
		$q2.= "where ";
		$q2.= "lignes_actes.num_rubrique = '".$id_rubrique."' ";
		$q2.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."') ";
		$q2.= "and actes.id_acte = lignes_actes.num_acte ";
		$q2.= "group by lignes_actes.lig_ref ";
		$r2 = pmb_mysql_query($q2);	

		while($row2 = pmb_mysql_fetch_object($r2)) {
			if(array_key_exists($row2->lig_ref,$tab_cde)) {
				$tab_cde[$row2->lig_ref]['nb'] = $tab_cde[$row2->lig_ref]['nb'] - $row2->nb; 
			}
		}

		$q3 = "select ";
		$q3.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, lignes_actes.remise as rem, lignes_actes.debit_tva ";
		$q3.= "from actes, lignes_actes ";
		$q3.= "where ";
		$q3.= "lignes_actes.num_rubrique = '".$id_rubrique."' ";
		$q3.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."') ";
		$q3.= "and actes.id_acte = lignes_actes.num_acte ";
		$r3 = pmb_mysql_query($q3);

		$tab_fac = array();
		while($row3 = pmb_mysql_fetch_object($r3)) {
			
			$tab_fac[$row3->id_ligne]['nb']=$row3->nb;
			$tab_fac[$row3->id_ligne]['prix']=$row3->prix;				
			$tab_fac[$row3->id_ligne]['rem']=$row3->rem;
		
		}			

		$tot_rub = 0;
		$tab = array_merge($tab_cde, $tab_fac);
		
		foreach($tab as $key=>$value) {
			$tot_lig = $tab[$key]['nb']*$tab[$key]['prix'];
			if($tab[$key]['rem'] != 0) $tot_lig = $tot_lig * (1- ($tab[$key]['rem']/100));
			$tot_rub = $tot_rub + $tot_lig;
		}
		return $tot_rub;
	}

	
	//calcule le montant engagé pour une rubrique budgétaire
	//$ws=avec rubriques filles
	//et retourne un tableau
	//['ht']=montant ht
	//['ttc']=montant ttc
	//['tva']=montant tva
	public static function calcEngage($id_rubrique= 0, $wc=TRUE) {
		$id_rubrique = intval($id_rubrique);
		//	Montant Total engagé pour une rubrique =
		//	Somme des Montants engagés non facturés pour une rubrique par ligne de commande		(nb_commandé-nb_facturé)*prix_commande*(1-remise_commande)
		//+ Somme des Montants engagés pour une rubrique par ligne de facture					(nb_facturé)*prix_facture*(1-remise_facture)
		if($wc) {
			$tab_r[$id_rubrique]='1';
			$tab_r=$tab_r + rubriques::getChilds($id_rubrique);
			$id_rubrique=implode("','", array_keys($tab_r));
		}
		
		$q1 = "select ";
		$q1.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q1.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva ";
		$q1.= "from actes, lignes_actes ";
		$q1.= "where ";
		$q1.= "lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q1.= "and (actes.type_acte = '".TYP_ACT_CDE."' or actes.type_acte = '".TYP_ACT_RENT_ACC."') ";
		$q1.= "and actes.statut > '".STA_ACT_AVA."' and ( (actes.statut & ".STA_ACT_FAC.") != ".STA_ACT_FAC.") ";
		$q1.= "and actes.id_acte = lignes_actes.num_acte ";
		$r1 = pmb_mysql_query($q1);

		$tab_cde = array();
		while($row1 = pmb_mysql_fetch_object($r1)) {
			
			$tab_cde[$row1->id_ligne]['q']=$row1->nb;
			$tab_cde[$row1->id_ligne]['p']=$row1->prix;
			$tab_cde[$row1->id_ligne]['t']=$row1->tva;				
			$tab_cde[$row1->id_ligne]['r']=$row1->rem;
			$tab_cde[$row1->id_ligne]['debit_tva']=$row1->debit_tva;	
		
		}			
		
		$q2 = "select ";
		$q2.= "lignes_actes.lig_ref, sum(nb) as nb ";
		$q2.= "from actes, lignes_actes ";
		$q2.= "where ";
		$q2.= "lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q2.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."' )";
		$q2.= "and actes.id_acte = lignes_actes.num_acte ";
		$q2.= "group by lignes_actes.lig_ref ";
		$r2 = pmb_mysql_query($q2);	

		while($row2 = pmb_mysql_fetch_object($r2)) {
			if(array_key_exists($row2->lig_ref,$tab_cde)) {
				$tab_cde[$row2->lig_ref]['q'] = $tab_cde[$row2->lig_ref]['q'] - $row2->nb; 
			}
		}

		$q3 = "select ";
		$q3.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q3.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva ";
		$q3.= "from actes, lignes_actes ";
		$q3.= "where ";
		$q3.= "lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q3.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."' )";
		$q3.= "and actes.id_acte = lignes_actes.num_acte ";
		$r3 = pmb_mysql_query($q3);

		$tab_fac = array();
		while($row3 = pmb_mysql_fetch_object($r3)) {
			
			$tab_fac[$row3->id_ligne]['q']=$row3->nb;
			$tab_fac[$row3->id_ligne]['p']=$row3->prix;				
			$tab_fac[$row3->id_ligne]['t']=$row3->tva;				
			$tab_fac[$row3->id_ligne]['r']=$row3->rem;
			$tab_fac[$row3->id_ligne]['debit_tva']=$row3->debit_tva;		
		}			

		$lg = array_merge($tab_cde, $tab_fac);
		
		$tot_rub = calc($lg,2);
		return $tot_rub;
		
	}
	
	//calcule le montant a valider pour une rubrique budgétaire
	//$ws=avec rubriques filles
	//et retourne un tableau
	//['ht']=montant ht
	//['ttc']=montant ttc
	//['tva']=montant tva
	public static function calcAValider($id_rubrique= 0,$wc=TRUE) {
		$id_rubrique = intval($id_rubrique);
		//	Montant A valider pour une rubrique =
		//	Somme des Montants pour les commandes non encore validees 
		
		if($wc) {
			$tab_r[$id_rubrique]='1';
			$tab_r=$tab_r + rubriques::getChilds($id_rubrique);
			$id_rubrique=implode("','", array_keys($tab_r));
		}
		if (!$id_rubrique) {
			return array('ht'=>0,'tva'=>0,'ttc'=>0);
		}
		$q = "select ";
		$q.= "lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva  ";
		$q.= "from actes, lignes_actes ";
		$q.= "where 1 ";
		$q.= "and (actes.type_acte = '".TYP_ACT_CDE."' or actes.type_acte = '".TYP_ACT_RENT_ACC."') ";
		$q.= "and ((actes.statut & '".STA_ACT_AVA."')= '".STA_ACT_AVA."') ";
		$q.= "and lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q.= "and actes.id_acte = lignes_actes.num_acte ";
		$r = pmb_mysql_query($q);
		$i=0;
		$lg=array();
		while($row = pmb_mysql_fetch_object($r)) {
			$lg[$i]['q']=$row->nb;
			$lg[$i]['p']=$row->prix;				
			$lg[$i]['t']=$row->tva;
			$lg[$i]['r']=$row->rem;
			$lg[$i]['debit_tva']=$row->debit_tva;
			$i++;			
		}
		
		$tot_rub = calc($lg,2);
		return $tot_rub;
	}	
	
	//calcule le montant facture pour une rubrique budgétaire
	//$ws=avec rubriques filles
	//et retourne un tableau
	//['ht']=montant ht
	//['ttc']=montant ttc
	//['tva']=montant tva
	public static function calcFacture($id_rubrique= 0,$wc=TRUE) {
		$id_rubrique = intval($id_rubrique);
		//	Montant A valider pour une rubrique =
		//	Somme des Montants pour les factures 
		
		if($wc) {
			$tab_r[$id_rubrique]='1';
			$tab_r=$tab_r + rubriques::getChilds($id_rubrique);
			$id_rubrique=implode("','", array_keys($tab_r));
		}
		if (!$id_rubrique) {
			return array('ht'=>0,'tva'=>0,'ttc'=>0);
		}
		$q = "select ";
		$q.= "lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva  ";
		$q.= "from actes, lignes_actes ";
		$q.= "where 1 ";		
		$q.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."' )";
		$q.= "and lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q.= "and actes.id_acte = lignes_actes.num_acte ";
		$r = pmb_mysql_query($q);
		$i=0;
		$lg=array();
		while($row = pmb_mysql_fetch_object($r)) {
			$lg[$i]['q']=$row->nb;
			$lg[$i]['p']=$row->prix;				
			$lg[$i]['t']=$row->tva;
			$lg[$i]['r']=$row->rem;
			$lg[$i]['debit_tva']=$row->debit_tva;
			$i++;			
		}
		
		$tot_rub = calc($lg,2);
		return $tot_rub;
	}	
	

	//calcule le montant facture/paye pour une rubrique budgétaire
	//$ws=avec rubriques filles
	//et retourne un tableau
	//['ht']=montant ht
	//['ttc']=montant ttc
	//['tva']=montant tva
	public static function calcPaye($id_rubrique= 0,$wc=TRUE) {
		$id_rubrique = intval($id_rubrique);
		//	Montant A valider pour une rubrique =
		//	Somme des Montants pour les factures 
		
		if($wc) {
			$tab_r[$id_rubrique]='1';
			$tab_r=$tab_r + rubriques::getChilds($id_rubrique);
			$id_rubrique=implode("','", array_keys($tab_r));
		}
		if (!$id_rubrique) {
			return array('ht'=>0,'tva'=>0,'ttc'=>0);
		}
		$q = "select ";
		$q.= "lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva  ";
		$q.= "from actes, lignes_actes ";
		$q.= "where 1 ";
		$q.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."' )";
		$q.= "and ((actes.statut & '".STA_ACT_PAY."') = '".STA_ACT_PAY."') ";
		$q.= "and lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q.= "and actes.id_acte = lignes_actes.num_acte ";
		$r = pmb_mysql_query($q);
		$i=0;
		$lg=array();
		while($row = pmb_mysql_fetch_object($r)) {
			$lg[$i]['q']=$row->nb;
			$lg[$i]['p']=$row->prix;				
			$lg[$i]['t']=$row->tva;
			$lg[$i]['r']=$row->rem;
			$lg[$i]['debit_tva']=$row->debit_tva;
			$i++;			
		}
		$tot_rub = calc($lg,2);
		return $tot_rub;
	}	

	//compte le nb d'enfants directs d'une rubrique
	public static function countChilds($id_rubrique=0) {
		$id_rubrique = intval($id_rubrique);
		$q = "select count(1) from rubriques where num_parent ='".$id_rubrique."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}		

	
	//retourne la liste des descendants d'une rubrique sous forme de tableau
	//[id_rubrique]=1
	public static function getChilds($id_rubrique=0) {
		$id_rubrique = intval($id_rubrique);
		$tab_childs=array();
		
		$q="select id_rubrique from rubriques where num_parent='".$id_rubrique."' ";
		$r=pmb_mysql_query($q);
		while($row=pmb_mysql_fetch_object($r)){
			if (!array_key_exists($row->id_rubrique, $tab_childs)) {
				$tab_childs=$tab_childs + rubriques::getChilds($row->id_rubrique);
			}
			$tab_childs[$row->id_rubrique]=1;
		}
		return $tab_childs;
	}
	
	//Liste les ancetres d'une rubrique et les retourne sous forme d'un tableau 
	//[index][niveau][0]=id_rubrique 
	//[index][niveau][1]=libelle
	//[index][niveau][2]=num_parent
	public static function listAncetres($id_rub=0, $inclus=FALSE) {
		$id_rub = intval($id_rub);
		$q = "select id_rubrique, libelle, num_parent from rubriques where id_rubrique = '".$id_rub."' limit 1";
		$r = pmb_mysql_query($q);
		$row = pmb_mysql_fetch_object($r);
		$rub_list = array();

		$i=0;
		if ($inclus) {
			$rub_list[$i][0] = $row->id_rubrique;
			$rub_list[$i][1] = $row->libelle;
			$rub_list[$i][2] = $row->num_parent;
			$i++;
		}
		while ($row->num_parent){
			$q = "select id_rubrique, libelle, num_parent from rubriques where id_rubrique = '".$row->num_parent."' limit 1";
			$r = pmb_mysql_query($q);
			$row = pmb_mysql_fetch_object($r);
			$rub_list[$i][0] = $row->id_rubrique;
			$rub_list[$i][1] = $row->libelle;
			$rub_list[$i][2] = $row->num_parent;
			$i++;
		}
		$rub_list = array_reverse($rub_list);
		return $rub_list;		
	}	

	//Compte le nb de lignes d'actes affectées à une rubrique budgetaire			
	public static function hasLignes($id_rubrique=0){
		$id_rubrique = intval($id_rubrique);
		if (!$id_rubrique) return 0;

		$q = "select count(1) from lignes_actes where num_rubrique = '".$id_rubrique."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}	

	//Recalcul des montants des rubriques parent et raz des numéros de compte comptable et autorisations
	public static function maj($num_parent=0, $calcul=TRUE ) {
		if ($calcul) {
			if($num_parent) {
				$q = "select sum(montant) from rubriques where num_parent = '".$num_parent."' ";
				$r = pmb_mysql_query($q);
				$total = pmb_mysql_result($r,0,0);
			
				$parent = new rubriques($num_parent);	
				$parent->montant = $total;
				$parent->num_cp_compta = '';
				$parent->autorisations = '';
				$parent->save();
			
				rubriques::maj($parent->num_parent);
			}
		} else {
			if($num_parent) {
				$parent = new rubriques($num_parent);	
				$parent->num_cp_compta = '';
				$parent->autorisations = '';
				$parent->save();				
				rubriques::maj($parent->num_parent, FALSE);
			}
			
		}
	}
	
	//
	public static function getAutorisations($id_rubrique, $id_user) {
		$id_rubrique = intval($id_rubrique);
		$q = "select count(1) from rubriques where id_rubrique = '".$id_rubrique."' and autorisations like('% ".$id_user." %') ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}
	
	//optimization de la table rubriques
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE rubriques');
		return $opt;
	}
	
	//Retourne un tableau (id_rubrique=>libelle) a partir d'un tableau d'id 
	//si id_bibli et id_exer, userid sont précisés, limite les resultats aux rubriques par bibliotheque, exercice, utilisateur
	public static function getLibelle($tab=array(),$id_bibli=0,$id_exer=0,$userid=0) {
		$res=array();
		if(is_array($tab) && count($tab)) {
			$q = "select id_rubrique, rubriques.libelle from rubriques ";
			if ($id_exer || $id_bibli) {
				$q.= "join budgets on num_budget=id_budget " ;
			}
			$q.= "where 1 ";
			if($id_bibli) $q.= " and num_entite='".$id_bibli."' ";
			if($id_exer) $q.= " and num_exercice='".$id_exer."' ";
			if($userid) $q.= " and autorisations like '% ".$userid." %' ";
			$q.= "and id_rubrique in ('".implode("','", $tab)."') ";
			$r = pmb_mysql_query($q);
			while($row=pmb_mysql_fetch_object($r)) {
				$res[$row->id_rubrique]=$row->libelle;
			}
		}
		return $res;
	}
	
	
	//Affiche les sous-rubriques d'une rubrique
	public static function afficheSousRubriques($bud, $id_rub, &$form, $indent=0) {
	    
	    global $msg, $charset;
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
	    $mnt = array();
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
	        
	        $lib_mnt = array();
	        $lib_mnt_autre = array();
	        foreach($mnt as $k=>$v) {
	            $lib_mnt[$k]=number_format($v[$k_htttc],2,'.',' ');
	            if($acquisition_gestion_tva && $k!="tot" && $k!="sol") {
	                $lib_mnt_autre[$k]=number_format($v[$k_htttc_autre],2,'.',' ');
	            }
	        }
	        if ($bud->type_budget == TYP_BUD_GLO ) {
	            $lib_mnt['tot']='&nbsp;';
	            $lib_mnt['sol']='&nbsp;';
	        }
	        foreach ($lib_mnt as $k => $v) {
	            if (!$acquisition_gestion_tva || empty($lib_mnt_autre[$k])) {
	                $form = str_replace('!!mnt_'.$k.'!!', $v, $form);
	            } elseif ($acquisition_gestion_tva) {
	                $form = str_replace('!!mnt_'.$k.'!!', $v."<br />".$lib_mnt_autre[$k], $form);
	            }
	            
	        }
	        if ($nb_sr) {
	            static::afficheSousRubriques($bud, $row->id_rubrique, $form, $indent+1);
	        }
	    }
	}
	
	
	//Export excel des sous-rubriques d'une rubrique
	static public function printSousRubriques($bud, $id_rub, &$worksheet, $indent=0) {
	    
	    global $msg;
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
	    $mnt = array();
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
	        
	        $lib_mnt = array();
	        $lib_mnt_autre = array();
	        foreach($mnt as $k=>$v) {
	            $lib_mnt[$k]=number_format($v[$k_htttc],2,'.','');
	            if($acquisition_gestion_tva && $k!="tot" && $k!="sol") {
	                $lib_mnt_autre[$k]=number_format($v[$k_htttc_autre],2,'.','');
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
	            if (!empty($lib_mnt_autre["tot"])) {
	                $worksheet->write($line,1,$lib_mnt_autre["tot"]);
	            }
	            if (!empty($lib_mnt_autre["ava"])) {
	                $worksheet->write($line,2,$lib_mnt_autre["ava"]);
	            }
	            if (!empty($lib_mnt_autre["eng"])) {
	                $worksheet->write($line,3,$lib_mnt_autre["eng"]);
	            }
	            if (!empty($lib_mnt_autre["fac"])) {
	                $worksheet->write($line,4,$lib_mnt_autre["fac"]);
	            }
	            if (!empty($lib_mnt_autre["pay"])) {
	                $worksheet->write($line,5,$lib_mnt_autre["pay"]);
	            }
	            if (!empty($lib_mnt_autre["sol"])) {
	                $worksheet->write($line,6,$lib_mnt_autre["sol"]);
	            }
	        }
	        
	        $nb_sr = static::countChilds($row->id_rubrique);
	        if ($nb_sr) {
	            static::printSousRubriques($bud, $row->id_rubrique, $worksheet, $indent+1);
	        }
	    }
	}
	
	
}

