<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_account.class.php,v 1.43.2.2.2.3 2025/03/19 11:33:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;

require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path."/editor.class.php");
require_once($class_path."/author.class.php");
require_once($class_path."/rent/rent_pricing_system.class.php");
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
	 * Date de diffusion
	 */
	protected $diffusion_date;
	
	/**
	 * Date formatée
	 * @var string
	 */
	protected $formatted_diffusion_date;
	
	/**
	 * Date de fin de droits
	 */
	protected $rights_date;
	
	/**
	 * Date formatée
	 * @var string
	 */
	protected $formatted_rights_date;

	/**
	 * Droits illimités ?
	 */
	protected $unlimited_rights;
	
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
	
	protected const EMPTY_TEXT = "--";
	
	protected const EMPTY_DATE = "--";
	
	public function __construct($id) {
		$this->id = intval($id);
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
		$this->diffusion_date = '';
		$this->formatted_diffusion_date = '';
		$this->rights_date = '';
		$this->formatted_rights_date = '';
		$this->unlimited_rights = 0;
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
				$this->type_name=(!empty($account_types->table[$this->type]) ? $account_types->table[$this->type] : '');
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
				if($row->account_diffusion_date != '0000-00-00 00:00:00'){
				    $this->diffusion_date = $row->account_diffusion_date;
				    $this->formatted_diffusion_date = format_date($row->account_diffusion_date);
				}
				if($row->account_rights_date != '0000-00-00 00:00:00'){
				    $this->rights_date = $row->account_rights_date;
				    $this->formatted_rights_date = format_date($row->account_rights_date);
				}
				$this->unlimited_rights = $row->account_unlimited_rights;
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
	
	public function get_coords_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->set_grid_model('flat_column_25');
	    $interface_content_form->add_element('account_coords', 'acquisition_coord_lib')
	    ->set_class('row el_account_coords')
	    ->add_html_node($this->get_entity()->raison_sociale);
	    return $interface_content_form->get_display();
	}
	
	public function get_exercices_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $element_account_exercices = $interface_content_form->add_element('account_exercices', 'acquisition_account_exercice');
	    if ($this->editable) {
	        $element_account_exercices->add_html_node($this->gen_selector_exercices());
	    } else {
	        $element_account_exercices->add_html_node($this->get_exercice()->libelle);
	    }
	    return $interface_content_form->get_display();
	}
	
	/**
	 * Contenu du formulaire du type de demande
	 */
	public function get_request_types_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $account_request_types = new marc_select('rent_request_type', 'account_request_types', $this->request_type, '');
	    $element_account_request_types = $interface_content_form->add_element('account_request_types', 'acquisition_account_request_type_name');
	    if ($this->editable) {
	        $element_account_request_types->add_html_node($account_request_types->display);
	    } else {
	        $element_account_request_types->add_html_node($account_request_types->libelle);
	    }
	    return $interface_content_form->get_display();
	}
	
	/**
	 * Contenu du formulaire du type
	 */
	public function get_types_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $account_types = new marc_select('rent_account_type', 'account_types', $this->type, '');
	    $element_account_types = $interface_content_form->add_element('account_types', 'acquisition_account_type_name');
	    if ($this->editable) {
	        $element_account_types->add_html_node($account_types->display);
	    } else {
	        $element_account_types->add_html_node($account_types->libelle);
	    }
	    return $interface_content_form->get_display();
	}
	
	/**
	 * Contenu du formulaire de la description
	 */
	public function get_desc_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $element_account_desc = $interface_content_form->add_element('account_desc', 'acquisition_account_desc');
	    if ($this->editable) {
	        $element_account_desc->add_textarea_node($this->desc, 62, 6)
	        ->set_attributes(array('wrap' => 'virtual'));
	    } else {
	        $desc = (!empty($this->desc) ? $this->desc : static::EMPTY_TEXT);
	        $element_account_desc->add_html_node($desc);
	    }
	    return $interface_content_form->get_display();
	}
	
	public function get_dates_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    
	    // Date limite pour la réception
	    $element_account_receipt_limit_date = $interface_content_form->add_element('account_receipt_limit_date', 'acquisition_account_receipt_limit_date')
	    ->set_class('colonne3 el_account_receipt_limit_date');
	    if ($this->editable) {
	        $element_account_receipt_limit_date->add_input_node('date', explode(' ', $this->receipt_limit_date)[0]);
	        //required
	    } else {
	        $receipt_limit_date = (!empty($this->receipt_limit_date) ? $this->formatted_receipt_limit_date : static::EMPTY_DATE);
	        $element_account_receipt_limit_date->add_html_node($receipt_limit_date);
	    }
	    
	    // Date effective de réception
	    $element_account_receipt_effective_date = $interface_content_form->add_element('account_receipt_effective_date', 'acquisition_account_receipt_effective_date')
	    ->set_class('colonne3 el_account_receipt_effective_date');
	    if ($this->editable) {
	        $element_account_receipt_effective_date->add_input_node('date', explode(' ', $this->receipt_effective_date)[0]);
	    } else {
	        $receipt_effective_date = (!empty($this->receipt_effective_date) ? $this->formatted_receipt_effective_date : static::EMPTY_DATE);
	        $element_account_receipt_effective_date->add_html_node($receipt_effective_date);
	    }
	    
	    // Date de retour
	    $element_account_return_date = $interface_content_form->add_element('account_return_date', 'acquisition_account_return_date')
	    ->set_class('colonne3 el_account_return_date');
	    if ($this->editable) {
	        $element_account_return_date->add_input_node('date', explode(' ', $this->return_date)[0]);
	    } else {
	        $return_date = (!empty($this->return_date) ? $this->formatted_return_date : static::EMPTY_DATE);
	        $element_account_return_date->add_html_node($return_date);
	    }
	    return $interface_content_form->get_display();
	}
	
	public function get_uniform_title_informations_content_form() {
	    global $msg;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    //TODO : uniform title refresh
	    
	    // Titre
	    $element_account_title = $interface_content_form->add_element('account_title', 'acquisition_account_title')
	    ->set_class('row el_account_title');
	    if ($this->editable) {
	        $element_account_title->add_input_node('text', $this->title)
	        ->set_class('saisie-80em')
	        ->set_attributes(array('data-form-name' => 'account_title'));
	    } else {
	        $title = (!empty($this->title) ? $this->title : static::EMPTY_TEXT);
	        $element_account_title->add_html_node($title);
	    }
	    
	    // Editeur
	    if(is_object($this->publisher)) {
	        $publisher_display = $this->publisher->display;
	        $publisher_id = $this->publisher->id;
	    } else {
	        $publisher_display = '';
	        $publisher_id = 0;
	    }
	    $element_account_publisher = $interface_content_form->add_element('account_publisher', 'acquisition_account_num_publisher')
	    ->set_class('row el_account_publisher');
	    if ($this->editable) {
	        $element_account_publisher->add_authority_node($publisher_display, 'publishers')
	        ->set_hidden_name('account_num_publisher')
	        ->set_hidden_value($publisher_id)
	        ->set_openPopUpUrl("./select.php?what=editeur&caller=account_form&p1=account_num_publisher&p2=account_publisher&callback=account_maj_supplier_field");
	    } else {
	        if(empty($publisher_display)) {
	            $publisher_display = static::EMPTY_TEXT;
	        }
	        $element_account_publisher->add_html_node($publisher_display);
	    }
	    
	    // Fournisseur
	    if(is_object($this->supplier)) {
	        $supplier_display = $this->supplier->raison_sociale;
	        $supplier_id = $this->supplier->id_entite;
	    } else {
	        $supplier_display = '';
	        $supplier_id = 0;
	    }
	    $element_account_supplier = $interface_content_form->add_element('account_supplier', 'acquisition_account_num_supplier')
	    ->set_class('row el_account_supplier');
	    if ($this->editable) {
	        $element_account_supplier->add_authority_node($supplier_display, 'fournisseurs')
	        ->set_hidden_name('account_num_supplier')
	        ->set_hidden_value($supplier_id)
	        ->set_param1($this->get_entity()->id_entite)
	        ->set_openPopUpUrl("./select.php?what=fournisseur&caller=account_form&param1=account_num_supplier&param2=account_supplier&id_bibli=".$this->get_entity()->id_entite);
	    } else {
	        if(empty($supplier_display)) {
	            $supplier_display = static::EMPTY_TEXT;
	        }
	        $element_account_supplier->add_html_node($supplier_display);
	    }
	    
	    // Compositeur
	    if(is_object($this->author)) {
	        $author_display = $this->author->display;
	        $author_id = $this->author->id;
	    } else {
	        $author_display = '';
	        $author_id = 0;
	    }
	    $element_account_author = $interface_content_form->add_element('account_author', 'acquisition_account_num_author')
	    ->set_class('row el_account_author');
	    if ($this->editable) {
	        $element_account_author->add_authority_node($author_display, 'authors')
	        ->set_hidden_name('account_num_author')
	        ->set_hidden_value($author_id)
	        ->set_openPopUpUrl("./select.php?what=auteur&caller=account_form&param1=account_num_author&param2=account_author");
	    } else {
	        if(empty($author_display)) {
	            $author_display = static::EMPTY_TEXT;
	        }
	        $element_account_author->add_html_node($author_display);
	    }
	    
	    // Formation
	    $element_account_event_formation = $interface_content_form->add_element('account_event_formation', 'acquisition_account_event_formation')
	    ->set_class('row el_account_event_formation');
	    if ($this->editable) {
	        $element_account_event_formation->add_input_node('text', $this->event_formation)
	        ->set_class('saisie-80em')
	        ->set_attributes(array('data-form-name' => 'account_event_formation'));
	    } else {
	        $event_formation = (!empty($this->event_formation) ? $this->event_formation : static::EMPTY_TEXT);
	        $element_account_event_formation->add_html_node($event_formation);
	    }
	    
	    // Chef d'orchestre
	    $element_account_event_orchestra = $interface_content_form->add_element('account_event_orchestra', 'acquisition_account_event_orchestra')
	    ->set_class('row el_account_event_orchestra');
	    if ($this->editable) {
	        $element_account_event_orchestra->add_input_node('text', $this->event_orchestra)
	        ->set_class('saisie-80em')
	        ->set_attributes(array('data-form-name' => 'account_event_orchestra'));
	    } else {
	        $event_orchestra = (!empty($this->event_orchestra) ? $this->event_orchestra : static::EMPTY_TEXT);
	        $element_account_event_orchestra->add_html_node($event_orchestra);
	    }
	    
	    // Date de l'événement
	    $element_account_event_date = $interface_content_form->add_element('account_event_date', 'acquisition_account_event_date')
	    ->set_class('row el_account_event_date');
	    if ($this->editable) {
	        $element_account_event_date->add_input_node('date', explode(' ', $this->event_date)[0])
	        ->set_attributes(array('data-form-name' => 'account_event_date'));
	    } else {
	        $event_date = (!empty($this->event_date) ? $this->formatted_event_date : static::EMPTY_DATE);
	        $element_account_event_date->add_html_node($event_date);
	    }
	    
	    // Lieu de l'événement
	    $element_account_event_place = $interface_content_form->add_element('account_event_place', 'acquisition_account_event_place')
	    ->set_class('row el_account_event_place');
	    if ($this->editable) {
	        $element_account_event_place->add_input_node('text', $this->event_place)
	        ->set_class('saisie-80em')
	        ->set_attributes(array('data-form-name' => 'account_event_place'));
	    } else {
	        $event_place = (!empty($this->event_place) ? $this->event_place : static::EMPTY_TEXT);
	        $element_account_event_place->add_html_node($event_place);
	    }
	    
	    // Date de diffusion
	    $element_account_diffusion_date = $interface_content_form->add_element('account_diffusion_date', 'acquisition_account_diffusion_date')
	    ->set_class('row el_account_diffusion_date');
	    if ($this->editable) {
	        $element_account_diffusion_date->add_input_node('date', explode(' ', $this->diffusion_date)[0])
	        ->set_attributes(array('data-form-name' => 'account_diffusion_date'));
	    } else {
	        $diffusion_date = (!empty($this->diffusion_date) ? $this->formatted_diffusion_date : static::EMPTY_DATE);
	        $element_account_diffusion_date->add_html_node($diffusion_date);
	    }
	    
	    // Date de fin de droits
	    $element_account_rights_date = $interface_content_form->add_element('account_rights_date', 'acquisition_account_rights_date')
	    ->set_class('row el_account_rights_date');
	    if ($this->editable) {
	        $element_account_rights_date->add_input_node('date', explode(' ', $this->rights_date)[0])
	        ->set_attributes(array('data-form-name' => 'account_rights_date'));
	    } else {
	        $rights_date = (!empty($this->rights_date) ? $this->formatted_rights_date : static::EMPTY_DATE);
	        $element_account_rights_date->add_html_node($rights_date);
	    }
	    
	    // Droits illimités ?
	    $element_account_unlimited_rights = $interface_content_form->add_element('account_unlimited_rights', 'acquisition_account_unlimited_rights')
	    ->set_class('row el_account_unlimited_rights');
	    if ($this->editable) {
	        $element_account_unlimited_rights->add_input_node('boolean', $this->unlimited_rights)
	        ->set_attributes(array('data-form-name' => 'account_unlimited_rights'));
	    } else {
	        $element_account_unlimited_rights->add_html_node(($this->unlimited_rights ? $msg['40'] : $msg['39']));
	    }
	    
	    return $interface_content_form->get_display();
	}
	
	protected function get_selected_pricing_system() {
	    if(is_object($this->pricing_system)) {
	        return $this->pricing_system->get_id();
	    } else {
	        return 0;
	    }
	}
	
	protected function get_selector_pricing_systems() {
	    global $msg;
	    
	    if ($this->exercice) {
	        $num_exercice = $this->exercice->id_exercice;
	    } else {
	        $num_exercice = $this->get_default_exercice_num();
	    }
	    if($num_exercice){
	        $selected = $this->get_selected_pricing_system();
	        return gen_liste("select id_pricing_system, pricing_system_label from rent_pricing_systems where pricing_system_num_exercice = ".$num_exercice,"id_pricing_system","pricing_system_label","account_num_pricing_system","account_selected_grid(this);",$selected, 0, $msg['acquisition_account_pricing_system_except'], 0, $msg['acquisition_account_pricing_system_except']);
	    }else{
	        return '';
	    }
	}
	
	protected function get_html_node_pricing_systems() {
	    global $msg, $charset;
	    
	    $html_node = $this->get_selector_pricing_systems();
	    $html_node .= "<span id='account_grid_see' ".($this->get_selected_pricing_system() ? "" : "style='display : none;'").">";
	    $html_node .= '<a style="cursor:pointer;" onclick=\'show_layer(); show_grid_in_account(document.forms["account_form"].elements["account_num_pricing_system"].value);\'>'.htmlentities($msg['acquisition_account_grid_see'], ENT_QUOTES, $charset).'</a>';
	    $html_node .= "</span>";
	    return $html_node;
	}
	
	public function get_pricing_system_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $element_account_pricing_system = $interface_content_form->add_element('account_num_pricing_system', 'acquisition_account_num_pricing_system');
	    if ($this->editable) {
	        $element_account_pricing_system->add_html_node($this->get_html_node_pricing_systems());
	    } else {
	        $label = static::EMPTY_TEXT;
	        if(is_object($this->pricing_system)) {
	            $label = $this->pricing_system->get_label();
	        }
	        $element_account_pricing_system->add_html_node($label);
	    }
	    return $interface_content_form->get_display();
	}
	
	public function get_minutage_content_form() {
	    global $msg, $charset;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    
	    // Minutage
	    $element_account_time = $interface_content_form->add_element('account_time', 'acquisition_account_time')
	    ->set_class('colonne10');
	    if ($this->editable) {
	        $element_account_time->add_input_node('number', $this->time)
	        ->set_attributes(array('data-form-name' => 'account_time', 'min' => 0, 'onchange' => 'account_update_price_from_time(this.value);'));
	    } else {
	        $element_account_time->add_html_node($this->time);
	    }
	    
	    // Pourcentage
	    $element_account_percent = $interface_content_form->add_element('account_percent', 'acquisition_account_percent')
	    ->set_class('colonne10');
	    if ($this->editable) {
	        $element_account_percent->add_input_node('text', $this->percent)
	        ->set_class('saisie-5em')
	        ->set_disabled($this->get_selected_pricing_system() ? false : true)
	        ->set_attributes(array('onchange' => 'account_update_price_from_percent(this.value);'));
	    } else {
	        $element_account_percent->add_html_node($this->percent);
	    }
	    
	    // Prix
	    $element_account_price = $interface_content_form->add_element('account_price', 'acquisition_account_price')
	    ->set_class('colonne10');
	    if ($this->editable) {
	        $html_node = "
    		<a onclick=\"account_update_price_from_time(document.getElementById('account_time').value); \" title=\"".htmlentities($msg['refresh'], ENT_QUOTES, $charset)."\" alt=\"".htmlentities($msg['refresh'], ENT_QUOTES, $charset)."\" style='cursor:pointer;font-size:1.5em;vertical-align:middle;' />
    			&nbsp;<i class='fa fa-refresh'></i>&nbsp;
    		</a>";
	        $element_account_price->add_input_node('text', $this->price)
	        ->set_class('saisie-5em');
	        $element_account_price->add_html_node($html_node);
	    } else {
	        $element_account_price->add_html_node($this->price);
	    }
	    
	    return $interface_content_form->get_display();
	}
	
	public function get_web_minutage_content_form() {
	    global $msg, $charset;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    
	    // Diffusion web ?
	    $element_account_web = $interface_content_form->add_element('account_web', 'acquisition_account_web');
	    if ($this->editable) {
	        $element_account_web->add_input_node('boolean', $this->web)
	        ->set_attributes(array('onchange' => 'account_change_checkbox_web(this.checked);'));
	    } else {
	        $element_account_web->add_html_node(($this->web ? $msg['40'] : $msg['39']));
	    }
	    
	    // Pourcentage
	    $element_account_web_percent = $interface_content_form->add_element('account_web_percent', 'acquisition_account_web_percent')
	    ->set_class('colonne10');
	    if ($this->editable) {
	        $element_account_web_percent->add_input_node('text', $this->web_percent)
	        ->set_class('saisie-5em')
	        ->set_disabled($this->web ? false : true)
	        ->set_attributes(array('onchange' => 'account_update_web_price_from_web_percent(this.value);'));
	    } else {
	        $element_account_web_percent->add_html_node($this->web_percent);
	    }
	    
	    // Prix
	    $element_account_web_price = $interface_content_form->add_element('account_web_price', 'acquisition_account_web_price')
	    ->set_class('colonne10');
	    if ($this->editable) {
	        $html_node = "
    		<a onclick=\"account_update_web_price_from_web_percent(document.getElementById('account_web_percent').value); \" title=\"".htmlentities($msg['refresh'], ENT_QUOTES, $charset)."\" alt=\"".htmlentities($msg['refresh'], ENT_QUOTES, $charset)."\" style='cursor:pointer;font-size:1.5em;vertical-align:middle;' />
    			&nbsp;<i class='fa fa-refresh'></i>&nbsp;
    		</a>";
	        $element_account_web_price->add_input_node('text', $this->web_price)
	        ->set_class('saisie-5em')
	        ->set_disabled($this->web ? false : true);
	        $element_account_web_price->add_html_node($html_node);
	    } else {
	        $element_account_web_price->add_html_node($this->web_price);
	    }
	    
	    return $interface_content_form->get_display();
	}
	
	public function get_comment_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $element_account_comment = $interface_content_form->add_element('account_comment', 'acquisition_account_comment')
	    ->set_class('row el_account_comment');
	    if ($this->editable) {
	        $element_account_comment->add_textarea_node($this->comment, 62, 6)
	        ->set_attributes(array('wrap' => 'virtual'));
	    } else {
	        $comment = (!empty($this->comment) ? $this->comment : static::EMPTY_TEXT);
	        $element_account_comment->add_html_node($comment);
	    }
	    return $interface_content_form->get_display();
	}
	
	public function get_request_status_content_form() {
	    $interface_content_form = new interface_content_form(static::class);
	    $element_account_request_status = $interface_content_form->add_element('account_request_status', 'acquisition_account_request_status')
	    ->set_class('row el_account_event_request_status');
	    if ($this->editable) {
	        $element_account_request_status->add_html_node($this->get_selector_request_status());
	    } else {
	        $element_account_request_status->add_html_node($this->get_label_request_status());
	    }
	    return $interface_content_form->get_display();
	}
	
	public function get_content_form() {
	    global $charset;
	    global $rent_account_content_form_tpl;
	    
	    $content_form = $rent_account_content_form_tpl;
	    $content_form = str_replace("!!coords_content_form!!", $this->get_coords_content_form(),$content_form);
	    $content_form = str_replace("!!exercices_content_form!!", $this->get_exercices_content_form(),$content_form);
	    $content_form = str_replace("!!request_types_content_form!!", $this->get_request_types_content_form(),$content_form);
	    $content_form = str_replace("!!types_content_form!!", $this->get_types_content_form(),$content_form);
	    $content_form = str_replace("!!desc_content_form!!", $this->get_desc_content_form(),$content_form);
	    $content_form = str_replace("!!dates_content_form!!", $this->get_dates_content_form(),$content_form);
	    $content_form = str_replace("!!uniform_title_informations_content_form!!", $this->get_uniform_title_informations_content_form(),$content_form);
	    $content_form = str_replace("!!pricing_system_content_form!!", $this->get_pricing_system_content_form(),$content_form);
	    
	    $content_form = str_replace("!!minutage_content_form!!", $this->get_minutage_content_form(),$content_form);
	    $content_form = str_replace("!!web_minutage_content_form!!", $this->get_web_minutage_content_form(),$content_form);
	    
	    
	    $content_form = str_replace("!!comment_content_form!!", $this->get_comment_content_form(),$content_form);
	    $content_form = str_replace("!!request_status_content_form!!", $this->get_request_status_content_form(),$content_form);
	    
	    if(is_object($this->uniform_title)) {
	        $content_form = str_replace("!!uniform_title!!", htmlentities($this->uniform_title->get_isbd(), ENT_QUOTES, $charset), $content_form);
	        $content_form = str_replace("!!num_uniform_title!!", $this->uniform_title->id, $content_form);
	    } else {
	        $content_form = str_replace("!!uniform_title!!", "", $content_form);
	        $content_form = str_replace("!!num_uniform_title!!", 0, $content_form);
	    }
	    
	    return $content_form."<div class='row'>&nbsp;</div>";
	}
	
	/**
	 * Formulaire
	 */
	public function get_form(){
		global $msg;
		
		if(!$this->editable) {
		    return "
            <div class='form-contenu'>
                ".$this->get_content_form()."
            </div>
            <script src='javascript/pricing_systems.js'></script>
            <script>
                account_not_editable_form_hide_fields();
            </script>";
		}
		
		$interface_form = new interface_acquisition_rent_form('account_form');
		if($this->id) {
		    $interface_form->set_label($msg['acquisition_'.$this->object_type.'_form_edit']);
		}else{
		    $interface_form->set_label($msg['acquisition_'.$this->object_type.'_form_add']);
		}
		$interface_form->set_object_id($this->id)
		->set_num_entity($this->get_entity()->id_entite)
		->set_confirm_delete_msg($msg['acquisition_'.$this->object_type.'_confirm_delete'])
		->set_content_form($this->get_content_form())
		->set_table_name('rent_accounts');
		$form = $interface_form->get_display();
		
		$form = str_replace("!!js_function_form_hide_fields!!", $this->get_function_form_hide_fields(), $form);
		$form = str_replace("!!id!!",$this->id,$form);
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
		global $account_diffusion_date;
		global $account_rights_date;
        global $account_unlimited_rights;
		
		$this->exercice = new exercices($account_exercices);
		$this->request_type = stripslashes($account_request_types);
		$this->type = (isset($account_types) ? stripslashes($account_types) : '');
		if(!$this->type) {
		    $this->type=rent_account_types::get_request_type_pref_account($this->request_type);	
		}
		$this->desc = stripslashes($account_desc);
		$this->receipt_limit_date = $account_receipt_limit_date;
		$this->formatted_receipt_limit_date = format_date($this->receipt_limit_date);
		$this->receipt_effective_date = $account_receipt_effective_date;
		$this->formatted_receipt_effective_date = format_date($account_receipt_effective_date);
		$this->return_date = $account_return_date;
		$this->formatted_return_date = format_date($account_return_date);
		$this->uniform_title = new titre_uniforme($account_num_uniform_title);
		$this->title = stripslashes($account_title);
		$this->event_date = $account_event_date;
		$this->formatted_event_date = format_date($account_event_date);
		$this->event_formation = stripslashes($account_event_formation);
		$this->event_orchestra = stripslashes($account_event_orchestra);
		$this->event_place = stripslashes($account_event_place);
		$this->publisher = new editeur($account_num_publisher);
		$this->supplier = new entites($account_num_supplier);
		$this->author = new auteur($account_num_author);
		$this->pricing_system = new rent_pricing_system($account_num_pricing_system);
		$this->time = $account_time;
		$this->percent = ($account_percent ? stripslashes($account_percent) : '100');
		$this->price = stripslashes($account_price);
		$this->web = $account_web;
		$this->web_percent = ($account_web_percent ? stripslashes($account_web_percent) : '');
		$this->web_price = ($account_web_price ? stripslashes($account_web_price) : '');
		$this->comment = stripslashes($account_comment);
		$this->request_status = $account_request_status;
		$this->diffusion_date = $account_diffusion_date;
		$this->formatted_diffusion_date = format_date($this->diffusion_date);
		$this->rights_date = $account_rights_date;
		$this->formatted_rights_date = format_date($this->rights_date);
		$this->unlimited_rights = $account_unlimited_rights;
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
				account_num_acte = "'.$this->num_acte.'",
                account_diffusion_date = "'.$this->diffusion_date.'",
                account_rights_date = "'.$this->rights_date.'",
                account_unlimited_rights = "'.$this->unlimited_rights.'"		
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
		actes::delete($this->num_acte);
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
				pmb_mysql_query($query);
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
			$this->entity = new entites(entites::getSessionBibliId());
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
	    $price = $this->get_price();
	    if (empty($price)) {
	        $price = 0; 
	    }
	    $web_price = $this->get_web_price();
	    if (empty($web_price)) {
	        $web_price = 0;
	    }
	    return number_format($price + $web_price, 2, '.', '');
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
	
		return '<select id="account_request_status" name="account_request_status">
			<option value="1" '.($this->request_status == 1 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_not_ordered'].'</option>
			<option value="2" '.($this->request_status == 2 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_ordered'].'</option>
			<option value="3" '.($this->request_status == 3 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_account'].'</option>
		</select>';
	}
	
	protected function get_label_request_status(){
	    global $msg;
	    
	    switch ($this->request_status) {
	        case 1:
	            return $msg['acquisition_account_request_status_not_ordered'];
	        case 2:
	            return $msg['acquisition_account_request_status_ordered'];
	        case 3:
	            return $msg['acquisition_account_request_status_account'];
	    }
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
	
	public function get_prestation_date() {
	    switch($this->type) {
	        case 'd': // Rediffusion
	        case 't': // Retransmission
	            if (!empty($this->diffusion_date)) {
	                return $this->diffusion_date;
	            }
	            break;
	    }
	    return $this->event_date;
	}
	
	public function get_formatted_prestation_date() {
	    return format_date($this->get_prestation_date());
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
		
	public function get_diffusion_date() {
	    return $this->diffusion_date;
	}
	
	public function get_formatted_diffusion_date() {
	    return $this->formatted_diffusion_date;
	}
	
	public function get_rights_date() {
	    return $this->rights_date;
	}
	
	public function get_formatted_rights_date() {
	    return $this->formatted_rights_date;
	}
	
	public function get_unlimited_rights() {
	    return $this->unlimited_rights;
	}
	
	public function get_num_invoice() {
		return $this->num_invoice;
	}
	
	public function is_editable() {
		return $this->editable;
	}
	
	public function set_id($id) {
		$this->id = intval($id);
	}
	
	public function set_num_user($num_user) {
		$this->num_user = intval($num_user);
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
		$this->time = intval($time);
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
	
	public function set_diffusion_date($diffusion_date) {
	    $this->diffusion_date = $diffusion_date;
	}
	
	public function set_rights_date($rights_date) {
	    $this->rights_date = $rights_date;
	}
	
	public function set_unlimited_rights($unlimited_rights) {
	    $this->unlimited_rights = intval($unlimited_rights);
	}
	
	public function set_num_invoice($num_invoice) {
		$num_invoice = intval($num_invoice);
		if($num_invoice) {
			$this->editable = false;
		} else {
			$this->editable = true;
		}
		$this->num_invoice = $num_invoice;
	}
		
	public static function get_uniform_title_fields($uniform_title_id) {
		$tu= new titre_uniforme($uniform_title_id);
		return $tu;
	}
	
	public function get_invoices_to_select(){
		
		if($this->num_invoice) return "";
		$filters = array(
			'type' => $this->get_type(),
			'status' => 1,
			'num_pricing_system' => $this->pricing_system->get_id(),
		);
		$list_rent_invoices_selector_ui=new list_rent_invoices_selector_ui($filters);
		$list_rent_invoices_selector_ui->set_num_account($this->id);
		return $list_rent_invoices_selector_ui->get_display_list();
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