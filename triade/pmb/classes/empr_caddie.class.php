<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_caddie.class.php,v 1.42 2019-06-10 08:57:11 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des paniers

require_once ($class_path."/caddie_root.class.php");
require_once ($class_path."/classementGen.class.php");
require_once ($include_path."/templates/empr_cart.tpl.php");
require_once ($include_path."/templates/cart.tpl.php");

require_once ($class_path."/emprunteur.class.php");
require_once ($class_path."/list/caddie/list_empr_caddie_ui.class.php");

class empr_caddie extends caddie_root {
	// propriétés
	public $idemprcaddie ;
	public $type = '';
	public static $table_name = 'empr_caddie';
	public static $field_name = 'idemprcaddie';
	public static $table_content_name = 'empr_caddie_content';
	public static $field_content_name = 'empr_caddie_id';
	
	// ---------------------------------------------------------------
	//		empr_caddie($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($empr_caddie_id=0) {
		$this->idemprcaddie = $empr_caddie_id+0;
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos caddie
	// ---------------------------------------------------------------
	protected function getData() {
		global $dbh;
		
		parent::getData();
		if($this->idemprcaddie) {
			$requete = "SELECT * FROM empr_caddie WHERE idemprcaddie='$this->idemprcaddie' ";
			$result = @pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				$this->idemprcaddie = $temp->idemprcaddie;
				$this->name = $temp->name;
				$this->comment = $temp->comment;
				$this->autorisations = $temp->autorisations;
				$this->autorisations_all = $temp->autorisations_all;
				$this->classementGen = $temp->empr_caddie_classement;
				$this->acces_rapide = $temp->acces_rapide;
				$this->favorite_color = $temp->favorite_color;
				$this->creation_user_name = $temp->creation_user_name;
				$this->creation_date = $temp->creation_date;
			
				//liaisons
				$req="SELECT id_planificateur, num_type_tache, libelle_tache FROM planificateur WHERE num_type_tache=8 AND param REGEXP 's:11:\"empr_caddie\";s:[0-9]+:\"".$this->idemprcaddie."\";'";
				$res=pmb_mysql_query($req,$dbh);
				if($res && pmb_mysql_num_rows($res)){
					while ($ligne=pmb_mysql_fetch_object($res)){
						$this->liaisons["mailing"][]=array("id"=>$ligne->id_planificateur,"id_bis"=>$ligne->num_type_tache,"lib"=>$ligne->libelle_tache);
					}
				}
				$this->type = 'EMPR';
			}
			$this->compte_items();
		}
	}

	protected function get_template_form() {
		global $empr_cart_form;
		return $empr_cart_form;
	}
	
	protected function get_warning_delete() {
		global $msg;
		
		$message_delete_warning = $msg["caddie_used_in_warning"];
		foreach ($this->liaisons as $type => $values){
			if(count($values)){
				switch ($type){
					case "mailing":
						$message_delete_warning .= "\\n- ".$msg["planificateur_task"];
						break;
					default://On ne doit pas passer par là
						break;//On sort aussi du foreach
				}
			}
		}
		$message_delete_warning .= "\\n";
		return $message_delete_warning;
	}
	
	// formulaire
	public function get_form($form_action="", $form_cancel="") {
		global $msg, $charset;
		global $liaison_tpl;
		
		$form = parent::get_form($form_action, $form_cancel);
		if($this->get_idcaddie()) {
			$info_liaisons = $this->get_links_form();
			$message_delete_warning = "";
			if($info_liaisons){
				$liaison_tpl=str_replace("<!-- info_liaisons -->",$info_liaisons,$liaison_tpl);
				$form = str_replace('<!-- liaisons -->', $liaison_tpl, $form);
				$message_delete_warning = $this->get_warning_delete();
				$button_delete = "<input type='button' class='bouton' value=' ".$msg['supprimer']." ' onClick=\"javascript:alert('".$message_delete_warning."\\n".$msg["empr_caddie_used_cant_delete"]."')\" />";
				$form = str_replace('!!button_delete!!', $button_delete, $form);
			
			} else {
				$button_delete = "<input type='button' class='bouton' value=' ".$msg['supprimer']." ' onClick=\"javascript:confirmation_delete(".$this->get_idcaddie().",'".htmlentities(addslashes($this->name),ENT_QUOTES, $charset)."')\" />";
				$form = str_replace('!!button_delete!!', $button_delete, $form);
				$form .= confirmation_delete("./circ.php?categ=caddie&action=del_cart&idemprcaddie=");
			}
		} else {
			$form = str_replace('!!button_delete!!', '', $form);
		}
		return $form;
	}
	
	// Liaisons pour le panier
	protected function get_links_form() {
		global $msg, $charset;
			
		$links_form = "";
		$end = false;
		foreach ( $this->liaisons as $type => $values ) {
			if (count ( $values )) {
				$links_form .= "<br>";
				switch ($type){
					case "mailing":
						$links_form.="<div class='row'>
                                           <label for='' class='etiquette'>".$msg["planificateur_task"]."</label>
                                       </div>
                                       <div class='row'>";
						if (SESSrights & ADMINISTRATION_AUTH) {
							$link="<a href='./admin.php?categ=planificateur&sub=manager&act=task&type_task_id=!!id_bis!!&planificateur_id=!!id!!'>!!name!!</a>";
						} else {
							$link="!!name!!";
						}
						break;
					default://On ne doit pas passer par là
						$links_form="";
						//break 2;//On sort aussi du foreach
						$end = true;
						break;
				}
				if($end) break;
				foreach ( $values as $infos ) {
					$links_form .= str_replace ( array (
							"!!id!!",
							"!!name!!"
					), array (
							$infos ["id"],
							htmlentities ( $infos ["lib"], ENT_QUOTES, $charset )
					), $link );
				}
				$links_form .= "</div>";
			}
		}
		return $links_form;
	}
	
	public function set_properties_from_form() {
		global $classementGen_empr_caddie;
		
		parent::set_properties_from_form();
		$this->classementGen = stripslashes($classementGen_empr_caddie);
	}

	static public function get_cart_data($temp) {
		global $dbh;
	
		$nb_item = 0 ;
		$nb_item_pointe = 0 ;
		$rqt_nb_item="select count(1) from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' ";
		$nb_item = pmb_mysql_result(pmb_mysql_query($rqt_nb_item, $dbh), 0, 0);
		$rqt_nb_item_pointe = "select count(1) from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' and (flag is not null and flag!='') ";
		$nb_item_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_pointe, $dbh), 0, 0);
	
		return array( 
			'idemprcaddie' => $temp->idemprcaddie,
			'idcaddie' => $temp->idemprcaddie,
			'type' => 'EMPR',
			'name' => $temp->name,
			'comment' => $temp->comment,
			'autorisations' => $temp->autorisations,
			'autorisations_all' => $temp->autorisations_all,
			'empr_caddie_classement' => $temp->empr_caddie_classement,
			'caddie_classement' => $temp->empr_caddie_classement,
			'acces_rapide' => $temp->acces_rapide,
			'favorite_color' => $temp->favorite_color,
			'nb_item' => $nb_item,
			'nb_item_pointe' => $nb_item_pointe
		);
	}
	
	// création d'un panier vide
	public function create_cart() {
		$requete = "insert into empr_caddie set name='".addslashes($this->name)."', comment='".addslashes($this->comment)."', autorisations='".$this->autorisations."', autorisations_all='".$this->autorisations_all."', empr_caddie_classement='".addslashes($this->classementGen)."', acces_rapide='".$this->acces_rapide."', favorite_color='".addslashes($this->favorite_color)."' ";
		$user = $this->get_info_user();
		if(is_object($user) && count($user)) {
			$requete .= ", creation_user_name='".addslashes($user->name)."', creation_date='".date("Y-m-d H:i:s")."'";
		}
		pmb_mysql_query($requete);
		$this->idemprcaddie = pmb_mysql_insert_id();
		$this->compte_items();
		return $this->idemprcaddie;
	}
	
	// sauvegarde du panier
	public function save_cart() {
		$query = "update empr_caddie set name='".addslashes($this->name)."', comment='".addslashes($this->comment)."', autorisations='".$this->autorisations."', autorisations_all='".$this->autorisations_all."', empr_caddie_classement='".addslashes($this->classementGen)."', acces_rapide='".$this->acces_rapide."', favorite_color='".addslashes($this->favorite_color)."' where ".static::get_field_name()."='".$this->get_idcaddie()."'";
		$result = pmb_mysql_query($query);
		return true;
	}

	// ajout d'un item
	public function add_item($item=0) {
		global $dbh;
		
		if (!$item) return CADDIE_ITEM_NULL ;
		
		$requete = "replace into empr_caddie_content set empr_caddie_id='".$this->idemprcaddie."', object_id='".$item."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		return CADDIE_ITEM_OK ;
	}

	public function del_item_base($item=0) {
		global $dbh;
		
		if (!$item) return CADDIE_ITEM_NULL ;
		
		$verif_empr_item = $this->verif_empr_item($item); 
		if (!$verif_empr_item) {
			emprunteur::del_empr($item);
			return CADDIE_ITEM_SUPPR_BASE_OK ;
		} elseif ($verif_empr_item == 1) {
			return CADDIE_ITEM_EXPL_PRET ;
		} else {
			return CADDIE_ITEM_RESA ;
		}
					
	}

	// suppression d'un item de tous les caddies du même type le contenant
	public function del_item_all_caddies($item) {
		global $dbh;
		$requete = "select idemprcaddie FROM empr_caddie ";
		$result = pmb_mysql_query($requete, $dbh);
		for($i=0;$i<pmb_mysql_num_rows($result);$i++) {
			$temp=pmb_mysql_fetch_object($result);
			$requete_suppr = "delete from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' and object_id='".$item."' ";
			$result_suppr = pmb_mysql_query($requete_suppr, $dbh);
		}
	}

	public function del_item_flag() {
		global $dbh;
		$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is not null and flag!='') ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
	}
	
	public function del_item_no_flag() {
		global $dbh;
		$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is null or flag='') ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
	}

	

	public function pointe_item($item=0) {
		global $dbh;
		$requete = "update empr_caddie_content set flag='1' where empr_caddie_id='".$this->idemprcaddie."' and object_id='".$item."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
		return CADDIE_ITEM_OK ;
	}

	// suppression d'un panier
	public function delete() {
		parent::delete();
	}

	// get_cart() : ouvre un panier et récupère le contenu
	public function get_cart($flag="") {
		global $dbh;
		$cart_list=array();
		switch ($flag) {
			case "FLAG" :
				$requete = "SELECT * FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is not null and flag!='') ";
				break ;
			case "NOFLAG" :
				$requete = "SELECT * FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is null or flag='') ";
				break ;
			case "ALL" :
			default :
				$requete = "SELECT * FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' ";
				break ;
			}
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			while ($temp = pmb_mysql_fetch_object($result)) {
				$cart_list[] = $temp->object_id;
			}
		} 
		return $cart_list;
	}

	// compte_items 
	public function compte_items() {
		parent::compte_items();
	}

	public function verif_empr_item($id) {
	
		global $dbh;
		if ($id) {
			//Prêts en cours
			$query = "select count(1) from pret where pret_idempr=".$id." limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_result($result, 0, 0)){
				return 1 ;
			} else {
				//Réservations validées
				$query = "select count(1) from resa where resa_idempr=".$id." and resa_confirmee=1 limit 1 ";
				$result = pmb_mysql_query($query, $dbh);
				if(pmb_mysql_result($result, 0, 0)){
					return 2 ;
				} else {
					return 0 ;
				}
			}		
		} else return 0 ;
	}
	
	static public function show_actions($id_caddie = 0) {
		global $msg,$cart_action_selector,$cart_action_selector_line;
	
		//Le tableau des actions possibles
		$array_actions = array();
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_edit_panier"], 'location' => './circ.php?categ=caddie&sub=gestion&quoi=panier&action=edit_cart&idemprcaddie='.$id_caddie.'&item=0');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_suppr_panier"], 'location' => './circ.php?categ=caddie&sub=action&quelle=supprpanier&action=choix_quoi&idemprcaddie='.$id_caddie.'&item=');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_transfert"], 'location' => './circ.php?categ=caddie&sub=action&quelle=transfert&action=transfert&idemprcaddie='.$id_caddie.'&item=');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_edition"], 'location' => './circ.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&idemprcaddie='.$id_caddie.'&item='.$id_caddie.'&item=0');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_mailing"], 'location' => './circ.php?categ=caddie&sub=action&quelle=mailing&action=envoi&idemprcaddie='.$id_caddie.'&item='.$id_caddie.'&item=0');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_selection"], 'location' => './circ.php?categ=caddie&sub=action&quelle=selection&action=&idemprcaddie='.$id_caddie.'&item='.$id_caddie.'&item=0');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_suppr_base"], 'location' => './circ.php?categ=caddie&sub=action&quelle=supprbase&action=choix_quoi&idemprcaddie='.$id_caddie.'&item=');
		
		//On crée les lignes du menu
		$lines = '';
		foreach($array_actions as $item_action){
			$tmp_line = str_replace('!!cart_action_selector_line_location!!',$item_action['location'],$cart_action_selector_line);
			$tmp_line = str_replace('!!cart_action_selector_line_msg!!',$item_action['msg'],$tmp_line);
			$lines.= $tmp_line;
		}
		
		//On récupère le template
		$to_show = str_replace('!!cart_action_selector_lines!!',$lines,$cart_action_selector);
	
		return $to_show;
	}
	
	protected function replace_in_action_query($query, $by) {
		$final_query=str_replace("CADDIE(EMPR)",$by,$query);
		return $final_query;
	}
	
	protected function get_edition_template_form() {
		global $empr_cart_choix_quoi_edition;
		return $empr_cart_choix_quoi_edition;
	}
	
	public function get_list_caddie_ui() {
		global $show_list;
		
		list_empr_caddie_ui::set_id_caddie($this->idemprcaddie);
		list_empr_caddie_ui::set_object_type('EMPR');
		if($show_list) {
			list_empr_caddie_ui::set_show_list(true);
		}
		return new list_empr_caddie_ui();
	}
	
	public function get_edition_form($action="", $action_cancel="") {
		global $msg;
		
		if(!$action) $action = "./circ/caddie/action/edit.php?idemprcaddie=".$this->get_idcaddie();
		if(!$action_cancel) $action_cancel = "./circ.php?categ=caddie&sub=action&quelle=edition&action=&idemprcaddie=0" ;
		$form = parent::get_edition_form($action, $action_cancel);
		$form = str_replace('<!-- !!boutons_supp!! -->', '', $form);
		return $form;
	}
	
	public function get_export_form($action="", $action_cancel="") {
		return "";
	}
	
	public function aff_cart_objects ($url_base="./circ.php?categ=caddie&sub=gestion&quoi=panier&idemprcaddie=0", $no_del=false,$rec_history=0, $no_point=false ) {
		global $msg, $begin_result_liste;
		global $dbh;
		global $nbr_lignes, $page, $nb_per_page_search ;
		global $url_base_suppr_empr_cart ;
	
		$url_base_suppr_empr_cart = $url_base ;
	
		// nombre de références par pages
		if ($nb_per_page_search != "") $nb_per_page = $nb_per_page_search ;
		else $nb_per_page = 10;
	
		// on récupére le nombre de lignes
		if(!$nbr_lignes) {
			$requete = "SELECT count(1) FROM empr_caddie_content where empr_caddie_id='".$this->get_idcaddie()."' ".static::get_query_filters();
			$res = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
		}
	
		if(!$page) $page=1;
		$debut =($page-1)*$nb_per_page;
	
		//Calcul des variables pour la suppression d'items
		$modulo = $nbr_lignes%$nb_per_page;
		if($modulo == 1){
			$page_suppr = (!$page ? 1 : $page-1);
		} else {
			$page_suppr = $page;
		}
		$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);
	
	
		if($nbr_lignes) {
			// on lance la vraie requête
			$from = " empr_caddie_content left join empr on id_empr = object_id ";
			$order_by = " empr_nom, empr_prenom " ;
			$requete = "SELECT object_id, flag FROM $from where empr_caddie_id='".$this->get_idcaddie()."' ".static::get_query_filters();
			$requete .= " order by ".$order_by;
			$requete.= " LIMIT $debut,$nb_per_page ";
				
	
			$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
			// l'affichage du résultat est fait après le else
		} else {
			print $msg[399];
			return;
		}
	
		$liste=array();
		$result = @pmb_mysql_query($requete, $dbh);
		if ($result) {
			if(pmb_mysql_num_rows($result)) {
				while ($temp = pmb_mysql_fetch_object($result)) {
					$liste[] = array('object_id' => $temp->object_id, 'flag' => $temp->flag ) ;
				}
			}
		}
		if(!sizeof($liste) || !is_array($liste)) {
			print $msg[399];
			return;
		} else {
			print $this->get_js_script_cart_objects('circ');
			print $begin_result_liste;
			print empr_caddie::show_actions($this->get_idcaddie());
			foreach ($liste as $cle => $object) {
				// affichage de la liste des emprunteurs
				$requete = "SELECT * FROM empr WHERE id_empr=".$object['object_id']." LIMIT 1";
				$fetch = pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($fetch)) {
					$empr = pmb_mysql_fetch_object($fetch);
					// emprunteur
					$link = './circ.php?categ=pret&form_cb='.rawurlencode($empr->empr_cb);
					if (!$no_point) {
						if ($object['flag']) $marque_flag ="<img src='".get_url_icon('depointer.png')."' id='caddie_".$this->get_idcaddie()."_item_".$empr->id_empr."' title=\"".$msg['caddie_item_depointer']."\" onClick='del_pointage_item(".$this->get_idcaddie().",".$empr->id_empr.");' style='cursor: pointer'/>" ;
						else $marque_flag ="<img src='".get_url_icon('pointer.png')."' id='caddie_".$this->get_idcaddie()."_item_".$empr->id_empr."' title=\"".$msg['caddie_item_pointer']."\" onClick='add_pointage_item(".$this->get_idcaddie().",".$empr->id_empr.");' style='cursor: pointer'/>" ;
					} else {
						if ($object['flag']) $marque_flag ="<img src='".get_url_icon('tick.gif')."'/>" ;
						else $marque_flag ="" ;
					}
					if (!$no_del) $lien_suppr_cart = "<a href='$url_base&action=del_item&item=$empr->id_empr&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a> $marque_flag";
					else $lien_suppr_cart = $marque_flag ;
					$empr = new emprunteur($empr->id_empr, "", FALSE, 3);
					$empr->fiche_consultation = str_replace('!!image_suppr_caddie_empr!!'    , $lien_suppr_cart    , $empr->fiche_consultation);
					$empr->fiche_consultation = str_replace('!!lien_vers_empr!!'    , $link    , $empr->fiche_consultation);
					print $empr->fiche_consultation;
				}
			} // fin de liste
	
		}
		print "<br />".$nav_bar ;
		return;
	}
	
	public function aff_cart_titre() {
		global $msg;
		
		$link = "./circ.php?categ=caddie&sub=gestion&quoi=panier&action=&idemprcaddie=".$this->get_idcaddie();
		return "
			<div class='titre-panier'>
				<h3>
					<a href='".$link."'>".$this->name.($this->comment ? " - ".$this->comment : "")."</a>
				</h3>
			</div>";
	}
	
	protected function get_choix_quoi_template_form() {
		global $empr_cart_choix_quoi;
		return $empr_cart_choix_quoi;
	}
	
	public function get_choix_quoi_form($action="", $action_cancel="", $titre_form="", $bouton_valider="",$onclick="", $aff_choix_dep = false) {
		global $msg;
	
		$form = parent::get_choix_quoi_form($action, $action_cancel, $titre_form, $bouton_valider, $onclick, $aff_choix_dep);
		return $form;
	}
	
	public function del_items_base_from_list($liste=array()) {	
		global $url_base;
		
		$res_aff_suppr_base = "" ;
		foreach ($liste as $cle => $object) {
			if ($this->del_item_base($object)==CADDIE_ITEM_SUPPR_BASE_OK) $this->del_item_all_caddies ($object) ;
			else  {
				$res_aff_suppr_base .= aff_cart_unique_object ($object, $this->type, $url_base="./circ.php?categ=caddie&sub=gestion&quoi=panier&idemprcaddie=".$this->idemprcaddie);
			}
		}
		return $res_aff_suppr_base;
	}
	
	protected function write_content_tableau($worksheet) {
		global $elt_flag, $elt_no_flag;
	
		afftab_empr_cart_objects ($this->idemprcaddie, $elt_flag , $elt_no_flag) ;
	}
	
	protected function get_display_content_tableauhtml() {
		global $elt_flag, $elt_no_flag;
	
		afftab_empr_cart_objects ($this->idemprcaddie, $elt_flag , $elt_no_flag) ;
	}
	
	public function get_idcaddie() {
		return $this->idemprcaddie;
	}
} // fin de déclaration de la classe
  
