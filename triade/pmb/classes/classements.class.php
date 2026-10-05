<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classements.class.php,v 1.13.2.1 2024/06/03 11:24:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des classements de la DSI

class classement {
	// propriétés
	public $id_classement ;
	public $nom_classement = '';
	public $nom_classement_opac = '';
	public $type_classement = 'BAN';
	public $order = 0;
	
	protected static $instances = [];
	
	// ---------------------------------------------------------------
	//	constructeur
	// ---------------------------------------------------------------
	public function __construct($id_classement=0) {
		$this->id_classement = intval($id_classement);
		$this->getData();
	}

	// ---------------------------------------------------------------
	public function getData() {
		$this->type_classement	= 'BAN';
		$this->nom_classement	= '';	
		$this->nom_classement_opac = '';	
		$this->order = 0;
		if($this->id_classement) {
			$requete = "SELECT type_classement, nom_classement, classement_opac_name, classement_order FROM classements WHERE id_classement='$this->id_classement' ";
			$result = pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				if($temp->type_classement) {
					$this->type_classement = $temp->type_classement;
				}
				$this->nom_classement = $temp->nom_classement;
				$this->nom_classement_opac = $temp->classement_opac_name;
				$this->order = $temp->classement_order;
			}
		}
	}

	public function get_content_form() {
	    global $msg;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('nom_classement', 'dsi_clas_form_nom')
	    ->set_class('colonne2')
	    ->add_input_node('text', $this->nom_classement);
	    
	    if ($this->id_classement) {
	        $type_classement = $msg['dsi_clas_type_class_'.$this->type_classement] ;
	    } else {
	        $type_classement = "<select id='type_classement' name='type_classement'><option value='BAN'>".$msg['dsi_clas_type_class_BAN']."</option><option value='EQU'>".$msg['dsi_clas_type_class_EQU']."</OPTION></select>";
	    }
	    $interface_content_form->add_element('type_classement', 'dsi_clas_form_type')
	    ->set_class('colonne_suite')
	    ->add_html_node($type_classement);
	    
	    $interface_content_form->add_element('nom_classement_opac', 'dsi_clas_form_nom_opac')
	    ->add_input_node('text', $this->nom_classement_opac);
	    return $interface_content_form->get_display();
	}
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form($type="pro") {
		global $msg;
		
		$interface_form = new interface_dsi_form('saisie_classement');
		if(!$this->id_classement){
			$interface_form->set_label($msg['dsi_clas_form_creat']);
		}else{
			$interface_form->set_label($msg['dsi_clas_form_modif']);
		}
		$interface_form->set_object_id($this->id_classement)
		->set_confirm_delete_msg($msg['confirm_suppr'])
		->set_content_form($this->get_content_form())
		->set_table_name('classements')
		->set_field_focus('nom_classement');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $nom_classement;
		global $nom_classement_opac;
		global $type_classement;
		
		$this->nom_classement = stripslashes($nom_classement);
		$this->nom_classement_opac = stripslashes($nom_classement_opac);
		$this->type_classement = stripslashes($type_classement);
	}
	
	// ---------------------------------------------------------------
	public function save() {
		if ($this->id_classement) {
			$query = "update classements set nom_classement='".addslashes($this->nom_classement)."', classement_opac_name='".addslashes($this->nom_classement_opac)."' where id_classement='".$this->id_classement."'";
			pmb_mysql_query($query);
		} else {
			$set_order='';
			if($this->type_classement == 'BAN'){
				$requete="select max(classement_order) as ordre from classements where (type_classement='BAN' or type_classement='') ";
				$resultat=pmb_mysql_query($requete);
				$ordre_max=@pmb_mysql_result($resultat,0,0);
				$this->order = ($ordre_max+1);
				$set_order= ', classement_order= '.$this->order.' ';
			}
			$query = "insert into classements set nom_classement='".addslashes($this->nom_classement)."', classement_opac_name='".addslashes($this->nom_classement_opac)."', type_classement='".addslashes($this->type_classement)."' ".$set_order;
			pmb_mysql_query($query);
			$this->id_classement = pmb_mysql_insert_id() ;
		}
	}
	
	// ---------------------------------------------------------------
	public function delete() {
		if ($this->id_classement==1) return ;
		$requete = "delete FROM classements where id_classement='".$this->id_classement."' ";
		pmb_mysql_query($requete);
	}
	
	protected function init_order() {
		if($this->type_classement == 'BAN'){
			$query_where = "(type_classement='BAN' or type_classement='')";
		} else {
			$query_where = "(type_classement='EQU')";
		}
		$query = "SELECT id_classement, classement_order 
				FROM classements 
				WHERE ".$query_where."
				ORDER BY classement_order, nom_classement";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$order = 1;
			while ($row = pmb_mysql_fetch_object($result)) {
				pmb_mysql_query("UPDATE classements SET classement_order = '".$order."' WHERE id_classement = ".$row->id_classement);
				$order++;
			}
		}
	}
	
	// ---------------------------------------------------------------
	public function set_order($sens) {
		$this->init_order();
		if($this->type_classement == 'BAN'){
			$query_where = "(type_classement='BAN' or type_classement='')";
		} else {
			$query_where = "(type_classement='EQU')";
		}
		switch ($sens){
			case "up":
				$requete="select classement_order from classements where id_classement=".$this->id_classement;
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select max(classement_order) as ordre from classements where ".$query_where." and classement_order<$ordre";
				$resultat=pmb_mysql_query($requete);
				$ordre_max=pmb_mysql_result($resultat,0,0);
				if ($ordre_max != '') {
					$requete="select id_classement from classements where ".$query_where." and classement_order=$ordre_max limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_max=pmb_mysql_result($resultat,0,0);
					$requete="update classements set classement_order='".$ordre_max."' where id_classement=".$this->id_classement;
					pmb_mysql_query($requete);
					$requete="update classements set classement_order='".$ordre."' where id_classement=".$idchamp_max;
					pmb_mysql_query($requete);
				}
				break;
			case "down":
				$requete="select classement_order from classements where id_classement=".$this->id_classement;
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select min(classement_order) as ordre from classements where ".$query_where." and classement_order>$ordre";
				$resultat=pmb_mysql_query($requete);
				$ordre_min=pmb_mysql_result($resultat,0,0);
				if ($ordre_min != '') {
					$requete="select id_classement from classements where ".$query_where." and classement_order=$ordre_min limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_min=pmb_mysql_result($resultat,0,0);
					$requete="update classements set classement_order='".$ordre_min."' where id_classement=".$this->id_classement;
					pmb_mysql_query($requete);
					$requete="update classements set classement_order='".$ordre."' where id_classement=".$idchamp_min;
					pmb_mysql_query($requete);
				}
				break;		
		}
	}
	
	public static function get_instance($id) {
	    $id = intval($id);
	    if(!isset(static::$instances[$id])) {
	        static::$instances[$id] = new classement($id);
	    }
	    return static::$instances[$id];
	}
} // fin de déclaration de la classe classement
  
