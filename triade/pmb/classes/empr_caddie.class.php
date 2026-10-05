<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_caddie.class.php,v 1.59.4.2 2025/04/15 13:14:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Animations\Models\AnimationModel;

// définition de la classe de gestion des paniers

global $class_path, $include_path;
require_once ($class_path."/caddie_root.class.php");
require_once ($class_path."/classementGen.class.php");
require_once ($include_path."/templates/empr_cart.tpl.php");
require_once ($include_path."/templates/cart.tpl.php");

require_once ($class_path."/emprunteur.class.php");

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
		$this->idemprcaddie = intval($empr_caddie_id);
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos caddie
	// ---------------------------------------------------------------
	protected function getData() {
		parent::getData();
		if($this->idemprcaddie) {
			$requete = "SELECT * FROM empr_caddie WHERE idemprcaddie='$this->idemprcaddie' ";
			$result = pmb_mysql_query($requete);
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
				$res=pmb_mysql_query($req);
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
							$link="<a href='./admin.php?categ=planificateur&sub=manager&action=edit&type_id=!!id_bis!!&id=!!id!!'>!!name!!</a>";
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
		$nb_item = 0 ;
		$nb_item_pointe = 0 ;
		$rqt_nb_item="select count(1) from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' ";
		$nb_item = pmb_mysql_result(pmb_mysql_query($rqt_nb_item), 0, 0);
		$rqt_nb_item_pointe = "select count(1) from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' and (flag is not null and flag!='') ";
		$nb_item_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_pointe), 0, 0);
	
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
		if (is_object($user) && !empty($user)) {
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
		pmb_mysql_query($query);
		return true;
	}

	// ajout d'un item
	public function add_item($item=0) {
		if (!$item) return CADDIE_ITEM_NULL ;
		
		$requete = "replace into empr_caddie_content set empr_caddie_id='".$this->idemprcaddie."', object_id='".$item."' ";
		pmb_mysql_query($requete);
		return CADDIE_ITEM_OK ;
	}

	public function del_item_base($item=0) {
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
		$requete = "select idemprcaddie FROM empr_caddie ";
		$result = pmb_mysql_query($requete);
		for($i=0;$i<pmb_mysql_num_rows($result);$i++) {
			$temp=pmb_mysql_fetch_object($result);
			$requete_suppr = "delete from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' and object_id='".$item."' ";
			pmb_mysql_query($requete_suppr);
		}
	}

	public function del_item_flag() {
		$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is not null and flag!='') ";
		pmb_mysql_query($requete);
		$this->compte_items();
	}
	
	public function del_item_no_flag() {
		$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is null or flag='') ";
		pmb_mysql_query($requete);
		$this->compte_items();
	}

	

	public function pointe_item($item=0, $object_type="", $blob="", $blob_type="") {
		$requete = "update empr_caddie_content set flag='1' where empr_caddie_id='".$this->idemprcaddie."' and object_id='".$item."' ";
		pmb_mysql_query($requete);
		$this->compte_items();
		return CADDIE_ITEM_OK ;
	}

	// suppression d'un panier
	public function delete() {
	    //Suppression dans la table animation du num_cart
	    AnimationModel::deleteAnimationCartNum($this->idemprcaddie);
		parent::delete();
	}

	// get_cart() : ouvre un panier et récupère le contenu
	public function get_cart($flag="") {
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
		$result = pmb_mysql_query($requete);
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
		if ($id) {
			//Prêts en cours
			$query = "select count(1) from pret where pret_idempr=".$id." limit 1 ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_result($result, 0, 0)){
				return 1 ;
			} else {
				//Réservations validées
				$query = "select count(1) from resa where resa_idempr=".$id." and resa_confirmee=1 limit 1 ";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_result($result, 0, 0)){
					return 2 ;
				} else {
					return 0 ;
				}
			}		
		} else return 0 ;
	}
	
	public static function get_array_actions($id_caddie = 0, $type_caddie = 'NOTI', $actions_to_remove = array()) {
		global $msg;
		
		$array_actions = array();
		if (empty($actions_to_remove['edit_cart'])) {
			$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_edit_panier"], 'location' => static::get_constructed_link('gestion', 'panier', 'edit_cart', $id_caddie, '&item=0'));
		}
		if (empty($actions_to_remove['pointage_raz'])) {
			$array_actions[] = array('msg' => $msg["empr_caddie_menu_pointage_raz"], 'location' => static::get_constructed_link('gestion', 'razpointage', '', $id_caddie, '&moyen=raz'));
		}
		if (empty($actions_to_remove['supprpanier'])) {
			$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_suppr_panier"], 'location' => static::get_constructed_link('action', 'supprpanier', 'choix_quoi', $id_caddie));
		}
		if (empty($actions_to_remove['transfert'])) {
			$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_transfert"], 'location' => static::get_constructed_link('action', 'transfert', 'transfert', $id_caddie));
		}
		if (empty($actions_to_remove['edition'])) {
			$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_edition"], 'location' => static::get_constructed_link('action', 'edition', 'choix_quoi', $id_caddie, '&item=0'));
		}
		if (empty($actions_to_remove['mailing'])) {
			$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_mailing"], 'location' => static::get_constructed_link('action', 'mailing', 'envoi', $id_caddie, '&item=0'));
		}
		if (empty($actions_to_remove['carte'])) {
			$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_carte"], 'location' => static::get_constructed_link('action', 'carte', 'choix_quoi', $id_caddie, '&item=0'));
		}
		if (empty($actions_to_remove['selection'])) {
			$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_selection"], 'location' => static::get_constructed_link('action', 'selection', '', $id_caddie, '&item=0'));
		}
		if (empty($actions_to_remove['suppr_base'])) {
			$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_suppr_base"], 'location' => static::get_constructed_link('action', 'supprbase', 'choix_quoi', $id_caddie));
		}
		return $array_actions;
	}
	
	protected function replace_in_action_query($query, $by) {
		$final_query=str_replace("CADDIE(EMPR)",$by,$query);
		return $final_query;
	}
	
	protected function get_edition_template_form() {
		global $empr_cart_choix_quoi_edition;
		return $empr_cart_choix_quoi_edition;
	}
	
	public function get_list_caddie_content_ui() {
		global $show_list;
		
		list_empr_caddie_content_ui::set_id_caddie($this->idemprcaddie);
		list_empr_caddie_content_ui::set_object_type('EMPR');
		if($show_list) {
			list_empr_caddie_content_ui::set_show_list(true);
		}
		return new list_empr_caddie_content_ui();
	}
	
	public function get_edition_form($action="", $action_cancel="") {
		if(!$action) $action = "./circ/caddie/action/edit.php?idemprcaddie=".$this->get_idcaddie();
		if(!$action_cancel) $action_cancel = static::get_constructed_link('action', 'edition');
		$form = parent::get_edition_form($action, $action_cancel);
		$form = str_replace('<!-- !!boutons_supp!! -->', '', $form);
		return $form;
	}
	
	public function get_export_form($action="", $action_cancel="") {
		return "";
	}
	
	public function aff_cart_objects ($url_base="./circ.php?categ=caddie&sub=gestion&quoi=panier&idemprcaddie=0", $no_del=false,$rec_history=0, $no_point=false ) {
		global $msg, $begin_result_liste;
		global $nbr_lignes, $page, $nb_per_page_search ;
		global $url_base_suppr_empr_cart ;
	
		$url_base_suppr_empr_cart = $url_base ;
	
		// nombre de références par pages
		if ($nb_per_page_search != "") $nb_per_page = $nb_per_page_search ;
		else $nb_per_page = 10;
	
		// on récupére le nombre de lignes
		if(!$nbr_lignes) {
			$requete = "SELECT count(1) FROM empr_caddie_content where empr_caddie_id='".$this->get_idcaddie()."' ".static::get_query_filters();
			$res = pmb_mysql_query($requete);
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
		$result = pmb_mysql_query($requete);
		if ($result) {
			if(pmb_mysql_num_rows($result)) {
				while ($temp = pmb_mysql_fetch_object($result)) {
					$liste[] = array('object_id' => $temp->object_id, 'flag' => $temp->flag ) ;
				}
			}
		}
		if ((empty($liste) && !is_array($liste)) || !is_array($liste)) {
			print $msg[399];
			return;
		} else {
			print $this->get_js_script_cart_objects('circ');
			print $begin_result_liste;
			print static::show_actions($this->get_idcaddie(), $this->type);
			foreach ($liste as $object) {
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
		$link = static::get_constructed_link('gestion', 'panier', '', $this->get_idcaddie());
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
		$form = parent::get_choix_quoi_form($action, $action_cancel, $titre_form, $bouton_valider, $onclick, $aff_choix_dep);
		return $form;
	}
	
	public function del_items_base_from_list($liste=array()) {	
		global $url_base;
		
		$res_aff_suppr_base = array();
		foreach ($liste as $object) {
			$del_item_base = $this->del_item_base($object);
			if ($del_item_base == CADDIE_ITEM_SUPPR_BASE_OK) {
				$this->del_item_all_caddies ($object);
			} else  {
				if(empty($res_aff_suppr_base[$del_item_base])) {
					$res_aff_suppr_base[$del_item_base] = array();
				}
				$res_aff_suppr_base[$del_item_base][] = aff_cart_unique_object ($object, $this->type, $url_base="./circ.php?categ=caddie&sub=gestion&quoi=panier&idemprcaddie=".$this->idemprcaddie);
			}
		}
		return $res_aff_suppr_base;
	}
	
	protected function write_content_tableau() {
		global $elt_flag, $elt_no_flag;
	
		afftab_empr_cart_objects ($this->idemprcaddie, $elt_flag , $elt_no_flag);
	}
	
	protected function get_display_content_tableauhtml() {
		global $elt_flag, $elt_no_flag;
	
		afftab_empr_cart_objects ($this->idemprcaddie, $elt_flag , $elt_no_flag) ;
	}
	
	public function get_idcaddie() {
		return $this->idemprcaddie;
	}
	
	public function get_id() {
		return $this->idemprcaddie;
	}
	
	public function set_idcaddie($idcaddie) {
	    $this->idemprcaddie = intval($idcaddie);
	}
	
	public static function get_constructed_link($sub='', $sub_categ='', $action='', $idcaddie=0, $args_others='') {
		global $base_path;
		global $quoi;
		
		$link = $base_path."/circ.php?categ=caddie&sub=".$sub;
		if($sub_categ) {
			switch ($sub) {
				case 'gestion':
					switch ($quoi) {
						case 'selection':
							$link .= "&quoi=selection&moyen=".$sub_categ;
							break;
						case 'pointage':
							$link .= "&quoi=pointage&moyen=".$sub_categ;
							break;
						default :
							$link .= "&quoi=".$sub_categ;
							break;
					}
					break;
				case 'action':
					$link .= "&quelle=".$sub_categ;
					break;
			}
		}
		if($action) $link .= "&action=".$action;
		if($args_others) $link .= $args_others;
		if($idcaddie) $link .= "&idemprcaddie=".$idcaddie;
		return $link;
	}
	
	public function has_flag_not_sended() {
	    $result = pmb_mysql_query("SELECT count(*) as nb FROM empr_caddie_content WHERE flag='2' AND empr_caddie_id=".$this->idemprcaddie);
	    return pmb_mysql_result($result, 0, 'nb');
	}
	
	public function reset_flag_not_sended() {
	    pmb_mysql_query("UPDATE empr_caddie_content SET flag='' WHERE flag='2' AND empr_caddie_id=".$this->idemprcaddie);
	}
} // fin de déclaration de la classe