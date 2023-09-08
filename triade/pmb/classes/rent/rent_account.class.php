<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_account.class.php,v 1.38 2017-11-21 12:01:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path."/editor.class.php");
require_once($class_path."/author.class.php");
require_once($class_path."/rent/rent_pricing_system.class.php");
require_once($class_path."/rent/rent_invoices.class.php");
require_once($class_path."/marc_table.class.php");
require_once($include_path."/templates/rent/rent_account.tpl.php");
require_once($class_path."/actes.class.php");
require_once($class_path."/lignes_actes.class.php");

class rent_account {
	
	/**
	 * Identifiant du décompte
	 * @var integer
	 */
	protected $id;
	
	/**
	 * Utilisateur associé
	 * @var integer
	 */
	protected $num_user;
	
	/**
	 * Exercice associé
	 * @var exercices
	 */
	protected $exercice;
	
	/**
	 * Type de demande
	 * @var string
	 */
	protected $request_type;
	
	/**
	 * label du type de demande
	 * @var string
	 */
	protected $request_type_name;
	
	/**
	 * Type
	 * @var string
	 */
	protected $type;

	/**
	 * label du Type 
	 * @var string
	 */
	protected $type_name;
	
	/**
	 * Description
	 * @var string
	 */
	
	protected $desc;
	
	/**
	 * Date
	 * @var datetime
	 */
	protected $date;
	
	/**
	 * Date formatée
	 * @var string
	 */
	protected $formatted_date;
	
	/**
	 * Date limite pour la réception
	 * @var datetime
	 */
	protected $receipt_limit_date;

	/**
	 * Date limite pour la réception formatée
	 * @var string
	 */
	protected $formatted_receipt_limit_date;
	
	/**
	 * Date effective de réception
	 * @var datetime
	 */
	protected $receipt_effective_date;
	
	/**
	 * Date effective de réception formatée
	 * @var string
	 */
	protected $formatted_receipt_effective_date;
		
	/**
	 * Date de retour
	 * @var datetime
	 */
	
	protected $return_date;
	
	/**
	 * Date de retour formatée
	 * @var string
	 */
	protected $formatted_return_date;		
	
	/**
	 * Instance de l'exécution (optionnel)
	 * @var titre_uniforme
	 */
	protected $uniform_title;
	
	/**
	 * Titre
	 * @var string
	 */
	protected $title;
	
	/**
	 * Date de l'évènement ( concert ou communication)
	 * @var datetime
	 */
	protected $event_date;
	
	/**
	 * Date de l'évènement formatée
	 * @var string
	 */
	protected $formatted_event_date;
	
	/**
	 * Formation
	 * @var string
	 */
	protected $event_formation;
	
	/**
	 * Chef d'orchestre
	 * @var string
	 */
	protected $event_orchestra;
	
	/**
	 * Lieu de l'évènement
	 * @var string
	 */
	protected $event_place;
	
	/**
	 * éditeur
	 * @var publisher
	 */
	protected $publisher;
	
	/**
	 * fournisseur
	 * @var entites
	 */	
	protected $supplier;
	
	/**
	 * compositeur
	 * @var auteur
	 */
	protected $author;

	/**
	 * Système de tarification associé
	 * @var rent_pricing_system
	 */
	protected $pricing_system;
	
	/**
	 * Minutage
	 */
	protected $time;
	
	/**
	 * Pourcentage
	 */
	protected $percent;
	
	/**
	 * Prix calculé ou prix saisie
	 */
	protected $price;
	
	/**
	 * Web case à cocher
	 */
	protected $web;
	
	/**
	 * Pourcentage web
	 */
	protected $web_percent;
	
	/**
	 * Prix web calculé ou prix web saisie
	 */
	protected $web_price;
	
	/**
	 * Commentaire en retour
	 * @var string
	 */
	protected $comment;
	
	/**
	 * Statut (commandé / non commandé)
	 * @var integer
	 */
	protected $request_status;
	
	/**
	 * Identifiant de la facture (s'il en a une)
	 */
	protected $num_invoice;
	
	/**
	 * Identifiant de l'acte budgétaire associé
	 */
	protected $num_acte;
	
	/**
	 * Flag modifiable / non modifiable (Facture associée)
	 * @var boolean
	 */
	protected $editable;
	
	protected $object_type;
	
	/**
	 * Entité liée
	 * @var entites
	 */
	protected $entity;
	
	public function __construct($id) {
		$this->id = $id*1;
		$this->fetch_data();
		$this->object_type = 'account';
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {

		$this->num_user = 0;
		$this->request_type = '';
		$this->request_type_name = '';
		$this->type = '';
		$this->type_name = '';
		$this->desc = '';
		$this->date = date('Y-m-d H:i:s');
		$this->formatted_date = formatdate($this->date);
		$this->receipt_limit_date = date('Y-m-d H:i:s');
		$this->formatted_receipt_limit_date = '';
		$this->receipt_effective_date = '';
		$this->formatted_receipt_effective_date = '';
		$this->return_date = '';
		$this->formatted_return_date = '';
		$this->uniform_title = null;
		$this->title = '';
		$this->event_date = '';
		$this->formatted_event_date = '';
		$this->event_formation = '';
		$this->event_orchestra = '';
		$this->event_place = '';
		$this->publisher = null;
		$this->supplier = null;
		$this->author = null;
		$this->time = 0;
		$this->percent = '100';
		$this->price = '0';
		$this->web = 0;
		$this->web_percent = '0';
		$this->web_price = '0';
		$this->comment = '';
		$this->request_status = 1;
		$this->num_acte = 0;
		$this->num_invoice = 0;
		$this->editable = true;
		if ($this->id) {
			$query = 'select * from rent_accounts where id_account = '.$this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->num_user = $row->account_num_user;
				$this->exercice = new exercices($row->account_num_exercice);
				$this->request_type = $row->account_request_type;
				$account_request_types = new marc_list('rent_request_type');
				$this->request_type_name=$account_request_types->table[$this->request_type];				
				$this->type = $row->account_type;			
				$account_types = new marc_list('rent_account_type');
				$this->type_name=$account_types->table[$this->type];
				$this->desc = $row->account_desc;
				$this->date = $row->account_date;
				$this->formatted_date = format_date($row->account_date);
				if($row->account_receipt_limit_date != '0000-00-00 00:00:00'){
					$this->receipt_limit_date = $row->account_receipt_limit_date;
					$this->formatted_receipt_limit_date = format_date($row->account_receipt_limit_date);
				}
				if($row->account_receipt_effective_date != '0000-00-00 00:00:00'){
					$this->receipt_effective_date = $row->account_receipt_effective_date;
					$this->formatted_receipt_effective_date = format_date($row->account_receipt_effective_date);
				}
				if($row->account_return_date != '0000-00-00 00:00:00'){
					$this->return_date = $row->account_return_date;
					$this->formatted_return_date = format_date($row->account_return_date);
				}
				if($row->account_num_uniform_title) {
					$this->uniform_title = new titre_uniforme($row->account_num_uniform_title);
				}
				$this->title = $row->account_title;
				if($row->account_event_date != '0000-00-00 00:00:00'){
					$this->event_date = $row->account_event_date;
					$this->formatted_event_date = format_date($row->account_event_date);
				}
				$this->event_formation = $row->account_event_formation;
				$this->event_orchestra = $row->account_event_orchestra;
				$this->event_place = $row->account_event_place;
				if($row->account_num_publisher) {
					$this->publisher = new editeur($row->account_num_publisher);
				}
				if($row->account_num_supplier) {
					$this->supplier = new entites($row->account_num_supplier);
				}
				if($row->account_num_author) {
					$this->author = new auteur($row->account_num_author);
				}
				$this->pricing_system = new rent_pricing_system($row->account_num_pricing_system);
				$this->time = $row->account_time;
				$this->percent = round($row->account_percent, 2);
				$this->price = $row->account_price;
				$this->web = $row->account_web;
				$this->web_percent = round($row->account_web_percent, 2);
				$this->web_price = $row->account_web_price;
				$this->comment = $row->account_comment;
				$this->request_status = $row->account_request_status;
				$this->num_acte = $row->account_num_acte;
				$query = 'select account_invoice_num_invoice, invoice_status from rent_accounts_invoices
						left join rent_invoices on id_invoice = account_invoice_num_invoice  
						where account_invoice_num_account = '.$this->id;
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_object($result);
					$this->num_invoice = $row->account_invoice_num_invoice;
					$this->editable = false;
				} else {
					$this->num_invoice = 0;
					$this->editable = true;
				}
			}
		}
	}
	
	/**
	 * Retourne la fonction JS d'initialisation du formulaire (display)
	 */
	protected function get_function_form_hide_fields() {
		return 'account_form_hide_fields();';
	}
	
	/**
	 * Formulaire
	 */
	public function get_form(){
		global $msg,$charset;
		global $rent_account_form_tpl;
		
		if(!$this->editable) {
			return;
		}
		
		$form = $rent_account_form_tpl;
		$sub_categ = $this->object_type.'s';
		if($this->id) {
			$form = str_replace("!!form_title!!",htmlentities($msg['acquisition_'.$this->object_type.'_form_edit'], ENT_QUOTES, $charset),$form);
			$button_delete = "<input type='button' class='bouton' value='".htmlentities($msg['acquisition_'.$this->object_type.'_delete'], ENT_QUOTES, $charset)."' 
				onclick=\"if(confirm('".htmlentities(addslashes($msg['acquisition_'.$this->object_type.'_confirm_delete']), ENT_QUOTES, $charset)."')) { document.location='./acquisition.php?categ=rent&sub=".$sub_categ."&action=delete&id=".$this->id."';} return false;\"/>";
			$form = str_replace("!!button_delete!!",$button_delete,$form);
		} else {			
			$form = str_replace("!!form_title!!",htmlentities($msg['acquisition_'.$this->object_type.'_form_add'], ENT_QUOTES, $charset),$form);
			$form = str_replace("!!button_delete!!",'',$form);
		}
		$form = str_replace("!!sub!!",$sub_categ,$form);
		$form = str_replace("!!entity_id!!",$this->get_entity()->id_entite,$form);
		$form = str_replace("!!entity_label!!",$this->get_entity()->raison_sociale,$form);
		$form = str_replace("!!exercices!!",$this->gen_selector_exercices(),$form);
		$account_request_types = new marc_select('rent_request_type', 'account_request_types', $this->request_type, '');
		$form = str_replace("!!request_types!!",$account_request_types->display,$form);
		$account_types = new marc_select('rent_account_type', 'account_types', $this->type, '');
		$form = str_replace("!!types!!",$account_types->display,$form);		
		$form = str_replace("!!desc!!",htmlentities($this->desc, ENT_QUOTES, $charset),$form);
		$form = str_replace("!!receipt_limit_date!!",explode(' ', $this->receipt_limit_date)[0],$form);
		$form = str_replace("!!receipt_effective_date!!",explode(' ', $this->receipt_effective_date)[0],$form);
		$form = str_replace("!!return_date!!",explode(' ', $this->return_date)[0],$form);
		
		if(is_object($this->uniform_title)) {
			$form = str_replace("!!uniform_title!!",htmlentities($this->uniform_title->get_isbd(), ENT_QUOTES, $charset),$form);
			$form = str_replace("!!num_uniform_title!!",$this->uniform_title->id,$form);
		} else {
			$form = str_replace("!!uniform_title!!","",$form);
			$form = str_replace("!!num_uniform_title!!",0,$form);
		}
		
		$form = str_replace("!!title!!",htmlentities($this->title, ENT_QUOTES, $charset),$form);
		if(is_object($this->publisher)) {
			$form = str_replace("!!publisher!!",htmlentities($this->publisher->display, ENT_QUOTES, $charset),$form);
			$form = str_replace("!!num_publisher!!",$this->publisher->id,$form);
		} else {
			$form = str_replace("!!publisher!!","",$form);
			$form = str_replace("!!num_publisher!!",0,$form);
		}
		if(is_object($this->supplier)) {
			$form = str_replace("!!supplier!!",htmlentities($this->supplier->raison_sociale, ENT_QUOTES, $charset),$form);
			$form = str_replace("!!num_supplier!!",$this->supplier->id_entite,$form);
		} else {
			$form = str_replace("!!supplier!!","",$form);
			$form = str_replace("!!num_supplier!!",0,$form);
		}
		if(is_object($this->author)) {
			$form = str_replace("!!author!!",htmlentities($this->author->display, ENT_QUOTES, $charset),$form);
			$form = str_replace("!!num_author!!",$this->author->id,$form);
		} else {
			$form = str_replace("!!author!!","",$form);
			$form = str_replace("!!num_author!!",0,$form);
		}
		$form = str_replace("!!event_formation!!",htmlentities($this->event_formation, ENT_QUOTES, $charset),$form);
		$form = str_replace("!!event_orchestra!!",htmlentities($this->event_orchestra, ENT_QUOTES, $charset),$form);
		$form = str_replace("!!event_date!!",explode(' ', $this->event_date)[0],$form);
		$form = str_replace("!!event_place!!",htmlentities($this->event_place, ENT_QUOTES, $charset),$form);
		
		if(is_object($this->pricing_system)) {
			$selected = $this->pricing_system->get_id();
		} else {
			$selected = 0;
		}
		if ($this->exercice) {
			$num_exercice = $this->exercice->id_exercice;
		} else {
			$num_exercice = $this->get_default_exercice_num();
		}
		if($num_exercice){
			$selector_pricing_systems = gen_liste("select id_pricing_system, pricing_system_label from rent_pricing_systems where pricing_system_num_exercice = ".$num_exercice,"id_pricing_system","pricing_system_label","account_num_pricing_system","account_selected_grid(this);",$selected, 0, $msg['acquisition_account_pricing_system_except'], 0, $msg['acquisition_account_pricing_system_except']);
		}else{
			$selector_pricing_systems = "";
		}
		
		$form = str_replace("!!pricing_systems!!",$selector_pricing_systems,$form);
		$grid_see_link = '<a style="cursor:pointer;" onclick=\'show_layer(); show_grid_in_account(document.forms["account_form"].elements["account_num_pricing_system"].value);\'>'.htmlentities($msg['acquisition_account_grid_see'], ENT_QUOTES, $charset).'</a>';
		$form = str_replace("!!pricing_system_grid_see!!",$grid_see_link,$form);
		$form = str_replace("!!pricing_system_grid_see_visible!!",($selected ? "" : "style='display : none;'"),$form);
		
		$form = str_replace("!!time!!",$this->time,$form);
		$form = str_replace("!!percent!!",$this->percent,$form);
		$form = str_replace("!!percent_enabled!!",($selected ? "" : "disabled='disabled'"),$form);
		$form = str_replace("!!price!!",$this->price,$form);
		
		$form = str_replace("!!web_checked!!",($this->web ? "checked='checked'" : ""),$form);
		$form = str_replace("!!web_percent!!",$this->web_percent,$form);
		$form = str_replace("!!web_price!!",$this->web_price,$form);
		$form = str_replace("!!web_enabled!!",($this->web ? "" : "disabled='disabled'"),$form);
		
		$form = str_replace("!!comment!!",htmlentities($this->comment, ENT_QUOTES, $charset),$form);
		$form = str_replace("!!request_status!!",$this->get_selector_request_status(),$form);
		
		$form = str_replace("!!id!!",$this->id,$form);
		$form = str_replace("!!js_function_form_hide_fields!!",$this->get_function_form_hide_fields(),$form);
		return $form;
	}

	/**
	 * Provenance du formulaire
	 */
	public function set_properties_from_form(){
		global $account_exercices;
		global $account_request_types;
		global $account_types;
		global $account_desc;
		global $account_receipt_limit_date;
		global $account_receipt_effective_date;
		global $account_return_date;
		global $account_num_uniform_title;
		global $account_title;
		global $account_event_date;
		global $account_event_formation;
		global $account_event_orchestra;
		global $account_event_place;
		global $account_num_publisher;
		global $account_num_supplier;
		global $account_num_author;
		global $account_num_pricing_system;
		global $account_time;
		global $account_percent;
		global $account_price;
		global $account_web;
		global $account_web_percent;
		global $account_web_price;
		global $account_comment;
		global $account_request_status;
		
		$this->exercice = new exercices($account_exercices);
		$this->request_type = stripslashes($account_request_types);
		$this->type = stripslashes($account_types);
		if(!$this->type) $this->type=rent_account_types::get_request_type_pref_account($this->request_type);	
		$this->desc = stripslashes($account_desc);
		$this->receipt_limit_date = $account_receipt_limit_date;
		$this->formatted_receipt_limit_date = format_date($this->receipt_limit_date);
		$this->receipt_effective_date = $account_receipt_effective_date;
		$this->formatted_receipt_effective_date = format_date($account_receipt_effective_date);
		$this->return_date = $account_return_date;
		$this->formatted_return_date = format_date($account_return_date);
		$account_num_uniform_title += 0;
		$this->uniform_title = new titre_uniforme($account_num_uniform_title);
		$this->title = stripslashes($account_title);
		$this->event_date = $account_event_date;
		$this->formatted_event_date = format_date($account_event_date);
		$this->event_formation = stripslashes($account_event_formation);
		$this->event_orchestra = stripslashes($account_event_orchestra);
		$this->event_place = stripslashes($account_event_place);
		$account_num_publisher += 0;
		$this->publisher = new editeur($account_num_publisher);
		$account_num_supplier += 0;
		$this->supplier = new entites($account_num_supplier);
		$account_num_author += 0;
		$this->author = new auteur($account_num_author);
		$account_num_pricing_system += 0;
		$this->pricing_system = new rent_pricing_system($account_num_pricing_system);
		$this->time = $account_time;
		$this->percent = ($account_percent ? stripslashes($account_percent) : '100');
		$this->price = stripslashes($account_price);
		$this->web = $account_web;
		$this->web_percent = ($account_web_percent ? stripslashes($account_web_percent) : '');
		$this->web_price = ($account_web_price ? stripslashes($account_web_price) : '');
		$this->comment = stripslashes($account_comment);
		$this->request_status = $account_request_status;
	}

	/**
	 * Sauvegarde de l'acte associé
	 */
	protected function save_acte() {
	
		$acte=new actes($this->num_acte);
		$acte->type_acte=TYP_ACT_RENT_ACC;
		if($this->num_invoice) {
			$acte->statut = STA_ACT_FAC;
		} else {
			switch($this->request_status){
				case 1 :
					$acte->statut=STA_ACT_AVA;
					break;
				case 2 :
					$acte->statut=STA_ACT_ENC;
					break;
				case 3 :
					$acte->statut=STA_ACT_ENC;
					break;
			}
		}
		$acte->num_entite=$this->get_entity()->id_entite;
		$acte->num_fournisseur=$this->get_supplier()->id_entite;
		$acte->num_exercice=$this->get_exercice()->id_exercice;
		$acte->save();
		$this->num_acte=$acte->id_acte;
		if($this->num_acte){
			$id_ligne=0;
			$res_lignes_acte=actes::getLignes($this->num_acte);
			if (pmb_mysql_num_rows($res_lignes_acte)) {
				$row = pmb_mysql_fetch_object($res_lignes_acte);
				$id_ligne=$row->id_ligne;
			}
			$ligne_acte=new lignes_actes($id_ligne);
			$ligne_acte->type_ligne=TYP_ACT_RENT_ACC;
			$ligne_acte->statut=$acte->statut;
			$ligne_acte->num_acte=$acte->id_acte;
			$ligne_acte->libelle=$this->get_title();
			$ligne_acte->num_rubrique=$this->get_num_section();
			$ligne_acte->prix=$this->get_total_price();
			$ligne_acte->nb=1;
			$ligne_acte->commentaires_gestion=$this->get_desc();
			$ligne_acte->save();
		}
	}	
	
	/**
	 * Sauvegarde
	 */
	public function save(){
		
		// Sauvegarde de l'acte / Peu importe si une facture est déjà associée
		$this->save_acte();
		
		if($this->num_invoice) {
			return false;
		}
		if($this->id) {
			$query = 'update rent_accounts set ';
			$fields_in_create = '';
			$where = 'where id_account= '.$this->id;
		} else {
			$this->num_user = SESSuserid;
			$this->date = date('Y-m-d H:i:s');
			$this->formatted_date = format_date($this->date);
			$query = 'insert into rent_accounts set ';
			$fields_in_create = '
					account_num_user = "'.$this->num_user.'",
					account_date = "'.$this->date.'",
			';
			$where = '';
		}
		$query .= $fields_in_create;
		$query .= '
				account_num_exercice = "'.$this->exercice->id_exercice.'",
				account_request_type = "'.addslashes($this->request_type).'",
				account_type = "'.addslashes($this->type).'",
				account_desc = "'.addslashes($this->desc).'",
				account_receipt_limit_date = "'.$this->receipt_limit_date.'",
				account_receipt_effective_date = "'.$this->receipt_effective_date.'",
				account_return_date = "'.$this->return_date.'",
				account_num_uniform_title = "'.$this->uniform_title->id.'",
				account_title = "'.addslashes($this->title).'",
				account_event_date = "'.$this->event_date.'",
				account_event_formation = "'.addslashes($this->event_formation).'",
				account_event_orchestra = "'.addslashes($this->event_orchestra).'",
				account_event_place = "'.addslashes($this->event_place).'",
				account_num_publisher = "'.$this->publisher->id.'",
				account_num_supplier = "'.$this->supplier->id_entite.'",
				account_num_author = "'.$this->author->id.'",
				account_num_pricing_system = "'.$this->pricing_system->get_id().'",
				account_time = "'.$this->time.'",
				account_percent = "'.addslashes($this->percent).'",
				account_price = "'.addslashes($this->price).'",
				account_web = "'.$this->web.'",
				account_web_percent = "'.addslashes($this->web_percent).'",
				account_web_price = "'.addslashes($this->web_price).'",
				account_comment = "'.addslashes($this->comment).'",
				account_request_status = "'.$this->request_status.'",	
				account_num_acte = "'.$this->num_acte.'"		
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			return true;
		} else {
			return false;
		}
	}

	public function get_num_section(){
		$query = 'select account_type_num_section from rent_account_types_sections where account_type_num_exercice='.$this->get_exercice()->id_exercice.' and account_type_marclist="'.$this->get_type().'"';
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 'account_type_num_section');
		} else {
			return 0;
		}
	}
	
	/**
	 * Suppression de l'acte associé
	 */
	protected function delete_acte() {
	
		$acte=new actes($this->num_acte);
		$acte->delete();
	}
			
	/**
	 * Suppression
	 */
	public function delete(){
		global $msg;
		
		if($this->id) {
			if($this->num_invoice) {
				return array(
						'msg_to_display' => $msg['acquisition_'.$this->object_type.'_cant_delete'].'<br /><br />',
						'state' => false
				);
			} else {
				$this->delete_acte();
				$query = "delete from rent_accounts where id_account = ".$this->id;
				$result = pmb_mysql_query($query);
				return array(
						'msg_to_display' => '',
						'state' => true
				);
			}
		}
		return array(
				'msg_to_display' => '',
				'state' => false
		);
	}

	/**
	 * Sélecteur des exercices comptables en cours
	 */
	protected function gen_selector_exercices() {
		global $msg;
	
		$display = '';
		$query = exercices::listByEntite($this->get_entity()->id_entite,1);
		$display=gen_liste($query,'id_exercice','libelle', 'account_exercices', 'update_pricing_systems();', (isset($this->exercice) ? $this->exercice->id_exercice : ''), 0,$msg['pricing_system_exercices_empty'],0,'');
			
		return $display;
	}
	
	public function get_entity(){
		if (!isset($this->entity)) {
			$this->entity = new entites(entites::getSessionBibliId()*1);
		}
		return $this->entity;
	}
	
	public function get_invoice_address_entity(){
		$query_result = entites::get_coordonnees(entites::getSessionBibliId()*1, '1');
		return pmb_mysql_fetch_object($query_result);
	}
	
	public function get_user() {
		$query ='select * from users where userid='.$this->num_user;
		$result = pmb_mysql_query($query);
		return pmb_mysql_fetch_object($result);
	}
	
	public function get_total_price() {
		return number_format($this->get_price() + $this->get_web_price(), 2, '.', '');
	}
	
	public function get_state_invoice() {
		global $base_path;
		global $msg;
		
		if($this->num_invoice) {
			$link_edit_invoice = "onclick=\"document.location='".$base_path."/acquisition.php?categ=rent&sub=invoices&action=edit&id_bibli=&id=".$this->num_invoice."';\" style=\"cursor:pointer;\"";
			$invoice=new rent_invoice($this->num_invoice);
			if($invoice->get_status() == 1) {
				return "<img src='".get_url_icon('new.gif')."' alt='".$msg['acquisition_account_state_unvalidated']."' title='".$msg['acquisition_account_state_unvalidated']."' ".$link_edit_invoice." />";	
			} elseif($invoice->get_status() == 2) {
				return "<img src='".get_url_icon('notice.gif')."' alt='".$msg['acquisition_account_state_validated']."' title='".$msg['acquisition_account_state_validated']."' ".$link_edit_invoice." />";
			} else {
				return "";
			}
		} else {
			$link_invoices_selector = "onclick=\"account_show_invoices_selector('".$this->id."');\"";
			return "<img src='".get_url_icon('req_get.gif')."' alt='".$msg['account_show_invoices_selector_title']."' title='".$msg['account_show_invoices_selector_title']."' ".$link_invoices_selector." />";
		}
	}	

	protected function get_selector_request_status(){
		global $msg;
	
		return '<select name="account_request_status">
			<option value="1" '.($this->request_status == 1 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_not_ordered'].'</option>
			<option value="2" '.($this->request_status == 2 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_ordered'].'</option>
			<option value="3" '.($this->request_status == 3 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_account'].'</option>
		</select>';
	}
	
	public function get_supplier_coords() {
		if(is_object($this->supplier)) {
			$result = entites::get_coordonnees($this->supplier->id_entite,1);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				return $row;
			}
		}
	}
	
	public function get_id() {
		return $this->id;
	}

	public function get_num_user() {
		return $this->num_user;
	}

	public function get_exercice() {
		return $this->exercice;
	}
	
	public function get_request_type() {
		return $this->request_type;
	}
	
	public function get_request_type_name() {
		return $this->request_type_name;
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function get_type_name() {
		return $this->type_name;
	}
	
	public function get_desc() {
		return $this->desc;
	}
	
	public function get_date() {
		return $this->date;
	}

	public function get_formatted_date() {
		return $this->formatted_date;
	}
	
	public function get_short_year_date() {
		return substr($this->date, 2, 2);
	}
	
	public function get_receipt_limit_date() {
		return $this->receipt_limit_date;
	}
	
	public function get_formatted_receipt_limit_date() {
		return $this->formatted_receipt_limit_date;
	}
	
	public function get_receipt_effective_date() {
		return $this->receipt_effective_date;
	}

	public function get_formatted_receipt_effective_date() {
		return $this->formatted_receipt_effective_date;
	}
	
	public function get_return_date() {
		return $this->return_date;
	}

	public function get_formatted_return_date() {
		return $this->formatted_return_date;
	}
	
	public function get_uniform_title() {
		return $this->uniform_title;
	}
	
	public function get_title() {
		return $this->title;
	}
	
	public function get_event_date() {
		return $this->event_date;
	}
	
	public function get_formatted_event_date() {
		return $this->formatted_event_date;
	}

	public function get_event_formation() {
		return $this->event_formation;
	}
	
	public function get_event_orchestra() {
		return $this->event_orchestra;
	}
	
	public function get_event_place() {
		return $this->event_place;
	}
	
	public function get_publisher() {
		return $this->publisher;
	}
	
	public function get_supplier() {
		return $this->supplier;
	}
	
	public function get_author() {
		return $this->author;
	}
	
	public function get_pricing_system() {
		return $this->pricing_system;
	}
	
	public function get_time() {
		return $this->time;
	}

	public function get_formatted_time() {
		return sprintf('%02d',floor($this->time/60)).':'.sprintf('%02d',$this->time % 60);
	}
	
	public function get_percent() {
		return $this->percent;
	}
	
	public function get_price() {
		return $this->price;
	}
	
	public function is_web() {
		return $this->web;
	}
	
	public function get_web_percent() {
		return $this->web_percent;
	}
	
	public function get_web_price() {
		return $this->web_price;
	}
	
	public function get_comment() {
		return $this->comment;
	}
	
	public function get_request_status() {
		return $this->request_status;
	}
	
	public function get_request_status_label() {
		global $msg;
		switch ($this->request_status) {
			case 2 :
				return $msg['acquisition_account_request_status_ordered'];
				break;
			case 3 :
				return $msg['acquisition_account_request_status_account'];
				break;
			case 1:
				return $msg['acquisition_account_request_status_not_ordered'];
				break;
			default :
				return '';
				break;
		}
	}

	public function get_num_acte() {
		return $this->num_acte;
	}
		
	public function get_num_invoice() {
		return $this->num_invoice;
	}
	
	public function is_editable() {
		return $this->editable;
	}
	
	public function set_id($id) {
		$this->id = $id*1;
	}
	
	public function set_num_user($num_user) {
		$this->num_user = $num_user*1;
	}
	
	public function set_exercice($exercice) {
		$this->exercice = $exercice;
	}
	
	public function set_request_type($request_type) {
		$this->request_type = $request_type;
	}
	
	public function set_type($type) {
		$this->type = $type;
	}

	public function set_desc($desc) {
		$this->desc = $desc;
	}
	
	public function set_date($date) {
		$this->date = $date;
	}
	
	public function set_receipt_limit_date($receipt_limit_date) {
		$this->receipt_limit_date = $receipt_limit_date;
	}
	
	public function set_receipt_effective_date($receipt_effective_date) {
		$this->receipt_effective_date = $receipt_effective_date;
	}
	
	public function set_return_date($return_date) {
		$this->return_date = $return_date;
	}
	
	public function set_uniform_title($uniform_title) {
		$this->uniform_title = $uniform_title;
	}
	
	public function set_title($title) {
		$this->title = $title;
	}
	
	public function set_event_date($event_date) {
		$this->event_date = $event_date;
	}
	
	public function set_event_formation($event_formation) {
		$this->event_formation = $event_formation;
	}
	
	public function set_event_orchestra($event_orchestra) {
		$this->event_orchestra = $event_orchestra;
	}
	
	public function set_event_place($event_place) {
		$this->event_place = $event_place;
	}
	
	public function set_publisher($publisher) {
		$this->publisher = $publisher;
	}
	
	public function set_supplier($supplier) {
		$this->supplier = $supplier;
	}
	
	public function set_author($author) {
		$this->author = $author;
	}
	
	public function set_pricing_system($pricing_system) {
		$this->pricing_system = $pricing_system;
	}
	
	public function set_time($time) {
		$this->time = $time*1;
	}
	
	public function set_percent($percent) {
		$this->percent = $percent;
	}
	
	public function set_price($price) {
		$this->price = $price;
	}
	
	public function set_web($web) {
		$this->web = $web;
	}
	
	public function set_web_percent($web_percent) {
		$this->web_percent = $web_percent;
	}
	
	public function set_web_price($web_price) {
		$this->web_price = $web_price;
	}
	
	public function set_comment($comment) {
		$this->comment = $comment;
	}
	
	public function set_request_status($request_status) {
		$this->request_status = $request_status;
	}

	public function set_num_acte($num_acte) {
		$this->num_acte = $num_acte;
	}
	
	public function set_num_invoice($num_invoice) {
		$num_invoice += 0;
		if($num_invoice) {
			$this->editable = false;
		} else {
			$this->editable = true;
		}
		$this->num_invoice = $num_invoice;
	}
		
	static function get_uniform_title_fields($uniform_title_id) {
		$tu= new titre_uniforme($uniform_title_id);
		return $tu;
	}
	
	public function get_invoices_to_select(){
		
		if($this->num_invoice) return "";
		$filters = array(
			'id_type' => $this->get_type(),
			'status' => 1,
			'num_pricing_system' => $this->pricing_system->get_id(),
		);
		$invoices=new rent_invoices($filters);
		return $invoices->get_display_selector_list($this->id);
	}	
	
	public function add_account_in_invoice($invoice_id){
		global $msg, $charset;
		
		$invoice=new rent_invoice($invoice_id);
		$invoice->add_account($this);		
		$invoice->save();
		$this->num_invoice = $invoice_id;		
		return array(
			'id' => $this->id,
			'invoice_id' => $invoice_id,
			'icon' => "<img onclick=\"document.location='./acquisition.php?categ=rent&sub=invoices&action=edit&id_bibli=&id=".$invoice_id."';\" title='".htmlentities($msg['acquisition_invoice_status_new'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['acquisition_invoice_status_new'], ENT_QUOTES, $charset)."' src='".get_url_icon('new.gif')."'>",
		);
	}
	
	protected function get_default_exercice_num() {
		$query = exercices::listByEntite($this->get_entity()->id_entite,1).' limit 1';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 0);
		}
	}
}