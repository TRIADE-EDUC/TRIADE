<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: budgets.class.php,v 1.38.4.1 2025/02/17 15:41:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path, $line;

require_once("$class_path/actes.class.php");
require_once("$class_path/interface/admin/interface_admin_acquisition_form.class.php");
require_once("$include_path/templates/budgets.tpl.php");

if(!defined('TYP_BUD_RUB')) define('TYP_BUD_RUB', 0);	//Type de budget	0 = Affectation par rubrique
if(!defined('TYP_BUD_GLO')) define('TYP_BUD_GLO', 1);	//					1 = Affectation globale

if(!defined('STA_BUD_PRE')) define('STA_BUD_PRE', 0);	//Statut		0 = En préparation
if(!defined('STA_BUD_VAL')) define('STA_BUD_VAL', 1);	//				1 = Valide
if(!defined('STA_BUD_CLO')) define('STA_BUD_CLO', 2);	//				2 = Cloturé

class budgets{
	
	
	public $id_budget = 0;							//Identifiant de budget	
	public $num_entite = 0;						//Identifiant de l'entité propriétaire du budget
	public $num_exercice = 0;						//Numéro de l'exercice sur lequel le budget est affecté
	public $libelle = '';							//Libellé du budget
	public $commentaires = '';						//Commentaires sur le budget
	public $montant_global = '000000.00';			//Montant global du budget
	public $seuil_alerte = '000';					//Niveau d'alerte en % du montant global
	public $statut = '0';							//Statut du budget (0=En préparation, 1=valide, 2=Cloturé)
	public $type_budget = '0';						//Type de budget 0=Affectation par rubriques, 1=Affectation globale
	 
	
	//Constructeur.	 
	public function __construct($id_budget= 0){ 
		$this->id_budget = intval($id_budget);
		if ($this->id_budget) {
			$this->load();	
		}
	}	
	
	// charge un budget à partir de la base.
	public function load(){
		$q = "select * from budgets where id_budget = '".$this->id_budget."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->num_entite = $obj->num_entite;
		$this->num_exercice = $obj->num_exercice;
		$this->libelle = $obj->libelle;
		$this->commentaires = $obj->commentaires;
		$this->montant_global = $obj->montant_global;
		$this->seuil_alerte = $obj->seuil_alerte;
		$this->statut = $obj->statut;
		$this->type_budget = $obj->type_budget;
	}

	public function get_form($id_entite) {
		global $msg;
		global $charset;
		global $budg_content_form, $budg_js_form;
		global /*$ptab, */$bt_add_lig;
		global $lig_rub, $lig_rub_img;
		global $mnt_form, $sel_typ_form;
		
		$content_form = $budg_content_form;

		//Affichage exercices actifs
		$q = exercices::listByEntite($id_entite, STA_EXE_ACT, 'statut desc, date_debut desc');
		$res = pmb_mysql_query($q);
		
		if (!$this->id_budget) {	//Nouveau budget ->choix exercice possible & choix type possible (affectation globale ou par lignes)
			
			$form_exer = "<select id='exer' name ='exer' >";
			while ($row=pmb_mysql_fetch_object($res)) {
				$form_exer.="<option value='".$row->id_exercice."' >".$row->libelle."</option>";
			}
			$form_exer.= "</select>";
			
			$mnt = $mnt_form[0];
			$sel_typ = $sel_typ_form;
		} else {			//Modification
			if (($this->statut == STA_BUD_PRE) || ($this->statut == STA_BUD_VAL && (!static::hasLignes($this->id_budget)) ) ) {		//Exercice modifiable si budget non activé ou pas de lignes d'actes affectées
				$form_exer = "<select id='exer' name ='exer' >";
				while ($row=pmb_mysql_fetch_object($res)) {
					$form_exer.="<option value='".$row->id_exercice."' ";
					if($this->num_exercice == $row->id_exercice) $form_exer.= "selected='selected' ";
					$form_exer.=">".$row->libelle."</option>";
				}
				$form_exer.= "</select>";
			} else {	// Exercice non modifiable si budget activé et non vide ou cloturé
				$exer = new exercices($this->num_exercice);
				$form_exer = "<input type='hidden' id='exer' name='exer' value='".$exer->id_exercice."' />".htmlentities($exer->libelle, ENT_QUOTES, $charset);
			}
			
			if ($this->type_budget == TYP_BUD_RUB) {
				$mnt = $this->montant_global;
			} else {
				$mnt = str_replace('!!mnt_bud!!', $this->montant_global, $mnt_form[1]);
			}
			
			if(!$this->type_budget) {
				$sel_typ = htmlentities($msg['acquisition_budg_aff_rub'], ENT_QUOTES, $charset);
			} else {
				$sel_typ = htmlentities($msg['acquisition_budg_aff_glo'], ENT_QUOTES, $charset);
			}
		}
		
		$interface_form = new interface_admin_acquisition_form('budgform');
		if(!$this->id_budget){
			$interface_form->set_label($msg['acquisition_ajout_budg']);
		}else{
			$interface_form->set_label($msg['acquisition_modif_budg']);
		}
		
		$content_form = str_replace('!!libelle!!', htmlentities($this->libelle,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!comment!!', htmlentities($this->commentaires,ENT_QUOTES,$charset), $content_form);
		if(!$this->id_budget) {
			$content_form = str_replace('!!seuil!!', '100', $content_form);
			$content_form = str_replace('!!statut!!', htmlentities($msg['acquisition_budg_pre'], ENT_QUOTES, $charset), $content_form);
			$content_form = str_replace('!!val_statut!!', '0', $content_form);
		} else {
			$content_form = str_replace('!!seuil!!', $this->seuil_alerte, $content_form);
			switch ($this->statut) {
				case STA_BUD_PRE :
					$content_form = str_replace('!!statut!!', htmlentities($msg['acquisition_budg_pre'],ENT_QUOTES,$charset), $content_form);
					break;
				case STA_BUD_VAL :
					$content_form = str_replace('!!statut!!', htmlentities($msg['acquisition_statut_actif'],ENT_QUOTES,$charset), $content_form);
					break;
				case STA_BUD_CLO :
					$content_form = str_replace('!!statut!!', htmlentities($msg['acquisition_statut_clot'],ENT_QUOTES,$charset), $content_form);
					break;
				default :
					$content_form = str_replace('!!statut!!', htmlentities($msg['acquisition_budg_pre'],ENT_QUOTES,$charset), $content_form);
					break;
			}
			$content_form = str_replace('!!val_statut!!', $this->statut, $content_form);
		}
		$content_form = str_replace('!!montant!!', $mnt, $content_form);
		$content_form = str_replace('!!sel_typ!!', $sel_typ, $content_form);
		
		$content_form = str_replace('!!id!!', $this->id_budget, $content_form);
		
		$content_form = str_replace('!!id_parent!!', 0, $content_form);
		
		//Affichage rubriques budgetaires
		if (!$this->id_budget) {
			$content_form = str_replace('!!lib_mnt!!', htmlentities($msg['acquisition_rub_mnt'], ENT_QUOTES, $charset), $content_form);
			$content_form = str_replace('<!-- rubriques -->', '', $content_form);
		} else {
			if ($this->type_budget == TYP_BUD_RUB ) {
				$content_form = str_replace('!!lib_mnt!!', htmlentities($msg['acquisition_rub_mnt'], ENT_QUOTES, $charset), $content_form);
			} else {
				$content_form = str_replace('!!lib_mnt!!', '&nbsp;', $content_form);
			}
			$q = static::listRubriques($this->id_budget);
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
				if ($this->type_budget == TYP_BUD_RUB ) {
					$content_form = str_replace('!!lib_mnt!!', htmlentities($msg['acquisition_rub_mnt'], ENT_QUOTES, $charset), $content_form);
					$content_form = str_replace('!!mnt!!', $row->montant, $content_form);
				} else {
					$content_form = str_replace('!!lib_mnt!!', '&nbsp;', $content_form);
					$content_form = str_replace('!!mnt!!', '&nbsp', $content_form);
				}
				$content_form = str_replace('!!ncp!!', $row->num_cp_compta, $content_form);
				$content_form = str_replace('<!-- sous_rub -->', '<!-- sous_rub'.$row->id_rubrique.' -->', $content_form);
				
				afficheSousRubriques($this->id_budget, $row->id_rubrique, $content_form, 1);
			}
			if ($this->statut != STA_BUD_CLO ) {
				$content_form = str_replace('<!-- bouton_lig -->', $bt_add_lig, $content_form);
			}
		}
		$content_form = str_replace('!!exer!!', $form_exer, $content_form);
		$content_form = str_replace('!!id_bibli!!', $id_entite, $content_form);
		$content_form = str_replace('!!id_bud!!', $this->id_budget, $content_form);
		$content_form = str_replace('!!id_rub!!', 0, $content_form);
		$content_form = str_replace('!!id_parent!!', 0, $content_form);
		
		$biblio = new entites($id_entite);
		$form = "<div class='row'><label class='etiquette'>".htmlentities($biblio->raison_sociale,ENT_QUOTES,$charset)."</label></div>";
		
		$interface_form->set_object_id($this->id_budget)
		->set_id_entity($id_entite)
		->set_statut($this->statut)
		->set_confirm_cloture_msg($msg['acquisition_budg_confirm_clot']." ".$this->libelle." ?")
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($content_form)
		->set_table_name('budgets')
		->set_field_focus('libelle')
		->set_duplicable(true);
		$form .= $interface_form->get_display();
		
		$form .= $budg_js_form;
		return $form;
	}
	
	public function set_properties_from_form() {
		global $id_bibli, $exer, $libelle, $comment, $seuil, $sel_typ, $mnt_bud;
		
		$this->num_entite = intval($id_bibli);
		$this->num_exercice = intval($exer);
		$this->libelle = stripslashes($libelle);
		$this->commentaires = stripslashes($comment);
		$this->seuil_alerte = $seuil;
		if (!$this->id_budget) {
			if (!$sel_typ) {
				$this->type_budget = TYP_BUD_RUB ; //Affectation par rubriques
				$this->montant_global = 0;
			} else {
				$this->type_budget = TYP_BUD_GLO ; //Affectation globale
				$this->montant_global = $mnt_bud;
			}
		} else {
			if ($this->type_budget == TYP_BUD_GLO ) $this->montant_global = $mnt_bud;
		}
	}
	
	// enregistre un budget en base.
	public function save(){
		if( $this->libelle == '' || !$this->num_entite || !$this->num_exercice ) die("Erreur de création budgets");
		if($this->id_budget) {
				$q = "update budgets set num_entite = '".$this->num_entite."', num_exercice = '".$this->num_exercice."', libelle = '".addslashes($this->libelle)."', ";
				$q.= "commentaires = '".addslashes($this->commentaires)."', montant_global = '".$this->montant_global."', seuil_alerte = '".$this->seuil_alerte."', ";
				$q.= "statut = '".$this->statut."', type_budget = '".$this->type_budget."' "; 
				$q.= "where id_budget = '".$this->id_budget."' ";
				pmb_mysql_query($q);
		} else {
			$q = "insert into budgets set num_entite = '".$this->num_entite."', num_exercice = '".$this->num_exercice."', libelle = '".addslashes($this->libelle)."', ";
			$q.= "commentaires = '".addslashes($this->commentaires)."', montant_global = '".$this->montant_global."', seuil_alerte = '".$this->seuil_alerte."', ";
			$q.= "statut = '".$this->statut."', type_budget = '".$this->type_budget."' "; 
			pmb_mysql_query($q);
			$this->id_budget = pmb_mysql_insert_id();
		}
	}

	// duplique un budget et l'enregistre en base.
	public static function duplicate($id_budget=0){
	    $id_budget = intval($id_budget);
		$new_bud = new budgets($id_budget);
		$new_bud->id_budget = 0;

		$lib = $new_bud->libelle.'_';
		$l_lib = strlen($lib);
		$q = "select if(max(substring(libelle, ".$l_lib."+1)) is null, 1, max(substring(libelle, ".$l_lib."+1))+1)  from budgets ";
		$q.= "where substring(libelle, 1, ".$l_lib.") = '".addslashes($lib)."' ";
		$q.= "and substring(libelle, ".$l_lib."+1) regexp '^[0-9]+\$' ";
		$r = pmb_mysql_query($q);
		$n=pmb_mysql_result($r, 0, 0);
		$new_bud->libelle = $lib.$n;
		
		$new_bud->statut = STA_BUD_PRE;
		$new_bud->save();
		$id_new_bud = $new_bud->id_budget;
		
		$q = static::listAllRubriques($id_budget);
		$r = pmb_mysql_query($q);
		$tab_p = array();
		while (($obj=pmb_mysql_fetch_object($r))) {
			
			$new_rub = new rubriques($obj->id_rubrique);
			$new_rub->num_budget = $id_new_bud;
			$new_rub->id_rubrique = 0;
			if ($obj->num_parent) $new_rub->num_parent = $tab_p[$obj->num_parent];
			$new_rub->save();
			$id_new_rub = $new_rub->id_rubrique;
			$tab_p[$obj->id_rubrique]= $id_new_rub;
			
		}
		return $id_new_bud;
	}

	//supprime un budget de la base
	public static function delete($id_budget=0) {
	    $id_budget = intval($id_budget);
	    if(!$id_budget) {
	        return false; 	
	    }
		$q = "delete from budgets where id_budget = '".$id_budget."' ";
		pmb_mysql_query($q);
		
		//supprime les rubriques associées
		$q = "delete from rubriques where num_budget = '".$id_budget."' ";
		pmb_mysql_query($q);
	}

	//retourne une requete pour liste des budgets de l'entité
	public static function listByEntite($id_entite) {
		$id_entite = intval($id_entite);
		$q = "select * from budgets where num_entite = '".$id_entite."' order by statut, libelle  ";
		return $q;
	}

	//retourne la liste des budgets d'un exercice
	public static function listByExercice($num_exercice) {
		$num_exercice = intval($num_exercice);
		$q = "select id_budget, libelle from budgets where num_exercice = '".$num_exercice."' ";
		$r = pmb_mysql_query($q);
		return $r;
	}

	//Vérifie si un budget existe			
	public static function exists($id_budget){
	    $id_budget = intval($id_budget);
		$q = "select count(1) from budgets where id_budget = '".$id_budget."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
		
	//Vérifie si le libellé d'un budget existe déjà pour une entité	et un même exercice		
	public static function existsLibelle($id_entite, $libelle, $id_exer, $id_budget=0){
		$id_entite = intval($id_entite);
		$id_exer = intval($id_exer);
		$id_budget = intval($id_budget);
		$q = "select count(1) from budgets where libelle = '".addslashes($libelle)."' and num_entite = '".$id_entite."' ";
		$q.= "and num_exercice = '".$id_exer."' ";
		if ($id_budget) $q.= "and id_budget != '".$id_budget."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//compte le nb de budgets activés pour une entité			
	public static function countActifs($id_entite, $id_budget=0){
		$id_entite = intval($id_entite);
		$id_budget = intval($id_budget);
		$q = "select count(1) from budgets where num_entite = '".$id_entite."' and statut = '1' ";
		if ($id_budget) $q.= "and id_budget != '".$id_budget."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	//Compte le nb de lignes d'actes affectées à un budget			
	public static function hasLignes($id_budget=0){
	    $id_budget = intval($id_budget);
		$q = "select id_rubrique from rubriques where num_budget = '".$id_budget."' ";
		$r = pmb_mysql_query($q);
		$nb = intval(pmb_mysql_num_rows($r));
		
		if ($nb) {			
			$liste= '';
			for ($i=0; $i<$nb; $i++) { 
				$row =pmb_mysql_fetch_row($r);
				$liste.= $row[0];
				if ($i<$nb-1) $liste.= ', ';
			}
			
			$q = "select count(1) from lignes_actes where num_rubrique in (".$liste.") ";
			$r = pmb_mysql_query($q); 
			return pmb_mysql_result($r, 0, 0);
		} else return '0';
	}	

	//Retourne une requete pour les rubriques d'un budget ayant pour parent la rubrique mentionnée
	public static function listRubriques($id_budget=0, $num_parent=0){
	    $id_budget = intval($id_budget);
	    $num_parent = intval($num_parent);
		$q = "select * from rubriques where num_budget = '".$id_budget."' ";
		$q.= "and num_parent = '".$num_parent."' ";
		$q.= "order by libelle ";
		return $q;
	}

	//Retourne une requete pour l'ensemble des rubriques d'un budget 	
	public static function listAllRubriques($id_budget=0){
	    $id_budget = intval($id_budget);
		$q = "select * from rubriques where num_budget = '".$id_budget."' order by num_parent asc ";
		return $q;
	}

	//Retourne le nombre de rubriques d'un budget	
	public static function countRubriques($id_budget=0){
	    $id_budget = intval($id_budget);
		$q = "select count(1) from rubriques where num_budget = '".$id_budget."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0); 
	}
	
	//calcule le montant engagé pour un budget 
	public static function calcEngagement($id_budget=0) {
		//	Montant Total engagé pour un budget =
		//	Somme des Montants engagés non facturés pour les rubriques du budget par ligne de commande		(nb_commandé-nb_facturé)*prix_commande*(1-remise_commande)
		//+ Somme des Montants engagés pour les rubriques du budget par ligne de facture					(nb_facturé)*prix_facture*(1-remise_facture)
	    $id_budget = intval($id_budget);
		$q1 = "select ";
		$q1.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, lignes_actes.remise as rem ";
		$q1.= "from actes, lignes_actes, rubriques ";
		$q1.= "where ";
		$q1.= "actes.type_acte = '".TYP_ACT_CDE."' ";
		$q1.= "and actes.statut > '".STA_ACT_AVA."' and ( (actes.statut & ".STA_ACT_FAC.") != ".STA_ACT_FAC.") ";
		$q1.= "and rubriques.num_budget = '".$id_budget."' ";
		$q1.= "and actes.id_acte = lignes_actes.num_acte ";
		$q1.= "and lignes_actes.num_rubrique = rubriques.id_rubrique ";
		$r1 = pmb_mysql_query($q1);

		$tab_cde = array();
		while (($row1 = pmb_mysql_fetch_object($r1))) {
			
			$tab_cde[$row1->id_ligne]['nb']=$row1->nb;
			$tab_cde[$row1->id_ligne]['prix']=$row1->prix;				
			$tab_cde[$row1->id_ligne]['rem']=$row1->rem;
		
		}			
		
		$q2 = "select ";
		$q2.= "lignes_actes.lig_ref, sum(nb) as nb ";
		$q2.= "from actes, lignes_actes ";
		$q2.= "where ";
		$q2.= "actes.type_acte = '".TYP_ACT_FAC."' ";
		$q2.= "and actes.id_acte = lignes_actes.num_acte ";
		$q2.= "group by lignes_actes.lig_ref ";
		$r2 = pmb_mysql_query($q2);	

		while(($row2 = pmb_mysql_fetch_object($r2))) {
			if(array_key_exists($row2->lig_ref,$tab_cde)) {
				$tab_cde[$row2->lig_ref]['nb'] = $tab_cde[$row2->lig_ref]['nb'] - $row2->nb; 
			}
		}

		$q3 = "select ";
		$q3.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, lignes_actes.remise as rem ";
		$q3.= "from actes, lignes_actes, rubriques ";
		$q3.= "where ";
		$q3.= "actes.type_acte = '".TYP_ACT_FAC."' ";
		$q3.= "and rubriques.num_budget = '".$id_budget."' ";
		$q3.= "and actes.id_acte = lignes_actes.num_acte ";
		$q3.= "and lignes_actes.num_rubrique = rubriques.id_rubrique ";
		$r3 = pmb_mysql_query($q3);
		$tab_fac = array();
		while (($row3 = pmb_mysql_fetch_object($r3))) {
			
			$tab_fac[$row3->id_ligne]['nb']=$row3->nb;
			$tab_fac[$row3->id_ligne]['prix']=$row3->prix;				
			$tab_fac[$row3->id_ligne]['rem']=$row3->rem;
		
		}			

		$tot_bud = 0;
		$tab = array_merge($tab_cde, $tab_fac);
		
		foreach($tab as $row) {
			$tot_lig = $row['nb']*$row['prix'];
			if($row['rem'] != 0) $tot_lig = $tot_lig * (1- ($row['rem']/100));
			$tot_bud = $tot_bud + $tot_lig;
		}
		return $tot_bud;
	}

	//Recalcul du montant global du budget
	public static function calcMontant($id_budget=0) {
		$id_budget = intval($id_budget);
		if($id_budget) {
			$q = "select sum(montant) from rubriques where num_budget = '".$id_budget."' and num_parent = '0' ";
			$r = pmb_mysql_query($q);
			$total = pmb_mysql_result($r,0,0);
			$budget = new budgets($id_budget);
			$budget->montant_global = $total;
			$budget->save();
		}	
	}	

	//optimization de la table budgets
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE budgets');
		return $opt;
	}

	
	//Affiche la liste des budgets
	public static function show_list_bud($id_bibli) {
	    //Affichage du formulaire de recherche
	    $form = static::show_search_form($id_bibli);
	    
	    //Affichage de la liste des budgets
	    $form .= list_accounting_budgets_ui::get_instance(array('entite' => $id_bibli, 'status' => 0, 'exercice' => 0))->get_display_list();	    
	    return $form;
	}
	
	//Affiche le formulaire de recherche
	protected static function show_search_form ($id_bibli) {
	    global $msg, $charset;
	    global $budg_search_form;
	    global $tab_bib;
	    
	    $form = $budg_search_form;
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
	    return $form;
	}
	
	
	//Affiche le formulaire d'un budget
	public static function show_bud($id_bibli=0, $id_bud=0) {
	    global $msg, $charset;
	    global $view_bud_form;
	    global $view_lig_rub_form, $lig_rub_img, $view_tot_rub_form;
	    global $pmb_gestion_devise;
	    global $acquisition_gestion_tva;
	    
	    if (!$id_bibli || !$id_bud) return;
	    
	    $form = static::show_search_form($id_bibli);
	    
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
	    $mnt = array();
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
	    
	    $form.= $view_bud_form;
	    
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
	    $q = static::listRubriques($id_bud, 0);
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
	            if (empty($acquisition_gestion_tva) || empty($lib_mnt_autre[$k])) {
	                $form = str_replace('!!mnt_'.$k.'!!', $v, $form);
	            } elseif (!empty($acquisition_gestion_tva)) {
	                $form = str_replace('!!mnt_'.$k.'!!', $v."<br />".$lib_mnt_autre[$k], $form);
	            }
	        }
	        
	        if($nb_sr) {
	            $form = str_replace('<!-- sous_rub -->', '<!-- sous_rub'.$row->id_rubrique.' -->', $form);
	            rubriques::afficheSousRubriques($bud, $row->id_rubrique, $form, 1);
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
	        $form = str_replace('!!mnt_'.$k.'!!', $v.(($acquisition_gestion_tva)?'<br />'.$totaux_autre[$k]:''), $form);
	    }
	    
	    $form = str_replace('!!id_bibli!!', $id_bibli, $form);
	    $form = str_replace('!!id_bud!!', $id_bud, $form);
	    
	    return $form;
	}
	
	public static function print_bud($id_bibli=0, $id_bud=0) {
	    global $msg, $charset;
	    global $pmb_gestion_devise;
	    global $acquisition_gestion_tva;
	    global $class_path;
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
	    $mnt = array();
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
	    
	    $q = static::listRubriques($id_bud, 0);
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
	        $worksheet->write($line,0,$row->libelle);
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
	        
	        //Sous-rubriques
	        $nb_sr = rubriques::countChilds($row->id_rubrique);
	        if ($nb_sr) {
	            rubriques::printSousRubriques($bud, $row->id_rubrique, $worksheet, 1);
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
 
}


