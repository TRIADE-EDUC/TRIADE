<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: equation.class.php,v 1.31 2024/01/26 14:45:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'équations de recherche'
global $class_path;
require_once($class_path."/search.class.php");

class equation {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $id_equation=0;	
	public $num_classement=1; 
	public $nom_equation="";
	public $comment_equation="";
	public $requete="";
	public $proprio_equation=0;
	public $search_class;
	public $human_query = "" ;

	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id_equation = intval($id);
		//Instantiation d'une classe recherche
		$this->search_class=new search(false);
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos
	// ---------------------------------------------------------------
	public function getData() {
		$this->num_classement = 1 ;
		$this->nom_equation="";
		$this->comment_equation="";
		$this->requete="";
		$this->proprio_equation=0;
		$this->human_query = "" ;
		if ($this->id_equation) {
			$query = "SELECT num_classement, nom_equation,comment_equation,requete, proprio_equation FROM equations WHERE id_equation='".$this->id_equation."' " ;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
			 	$this->num_classement	= $temp->num_classement ;
				$this->nom_equation		= $temp->nom_equation ;
				$this->comment_equation	= $temp->comment_equation ;	
				$this->requete			= $temp->requete ;
				$this->proprio_equation	= $temp->proprio_equation ;	
				$this->human_query = $this->search_class->make_serialized_human_query($this->requete) ;
			}
		}
	}
	
	public function get_content_form() {
	    global $msg, $charset;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('nom_equation', 'dsi_equ_form_nom')
	    ->set_class('colonne2')
	    ->add_input_node('text', $this->nom_equation);
	    $interface_content_form->add_element('num_classement', 'dsi_equ_form_classement')
	    ->set_class('colonne_suite')
	    ->add_html_node(show_classement_utilise ('EQU', $this->num_classement, 0));
	    $interface_content_form->add_element('comment_equation', 'dsi_ban_form_com_gestion')
	    ->add_textarea_node($this->comment_equation, 62, 2);
	    
	    $requete_html_node = $this->search_class->make_serialized_human_query($this->requete);
	    $requete_html_node .= "<input type='hidden' name='requete' value=\"".htmlentities($this->requete, ENT_QUOTES, $charset)."\" />";
	    if($this->id_equation) {
	        $requete_html_node .= "<input type='button' class='bouton' id='button_requete' value=\"".htmlentities($msg['dsi_equ_modif_requete'], ENT_QUOTES, $charset)."\" onClick=\"document.modif_requete_form_$this->id_equation.submit();\">";
	    }
	    $interface_content_form->add_element('button_requete', 'dsi_equ_form_requete')
	    ->add_html_node($requete_html_node);
	    
// 	    $interface_content_form->add_element('proprio_equation', 'dsi_ban_proprio_bannette')
// 	    ->add_html_node(($this->proprio_equation==0 ? $msg['dsi_equ_no_proprio'] : "Choix de proprio à faire"));
	    
	    return $interface_content_form->get_display();
	}
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form() {
		global $msg;
		
		$interface_form = new interface_dsi_form('saisie_equation');
		if(!$this->id_equation){
			$interface_form->set_label($msg['dsi_equ_form_creat']);
		}else{
			$interface_form->set_label($msg['dsi_equ_form_modif']);
		}
		$interface_form->set_object_id($this->id_equation)
		->set_confirm_delete_msg($msg['confirm_suppr'])
		->set_content_form($this->get_content_form())
		->set_table_name('equations')
		->set_field_focus('nom_equation')
		->set_duplicable(true);
		$display = $interface_form->get_display();
		//formulaire caché intégré hors formulaire de l'équation
		if($this->id_equation) {
		    $display .= $this->make_hidden_search_form();
		}
		return $display;
	}
	
	public function set_properties_from_form() {
		global $num_classement;
		global $nom_equation;
		global $comment_equation;
		global $requete;
		global $proprio_equation;
		
		$this->num_classement = intval($num_classement);
		$this->nom_equation = trim(stripslashes($nom_equation));
		$this->comment_equation = trim(stripslashes($comment_equation));
		$this->requete = stripslashes($requete);
		$this->proprio_equation = intval($proprio_equation);
	}
	
	// ---------------------------------------------------------------
	//		save
	// ---------------------------------------------------------------
	public function save() {
		if ($this->id_equation) {
			// update
			$query = "UPDATE equations set ";
			$clause = " WHERE id_equation='".$this->id_equation."'";
		} else {
			$query = "insert into equations set ";
			$clause = "";
		}
		$query.="num_classement='$this->num_classement',";
		$query.="nom_equation='".addslashes($this->nom_equation)."',";
		$query.="comment_equation='".addslashes($this->comment_equation)."',";
		$query.="requete='".addslashes($this->requete)."',";
		$query.="proprio_equation='".$this->proprio_equation."'";
		$query.=$clause ;
		pmb_mysql_query($query);
		if (!$this->id_equation) $this->id_equation = pmb_mysql_insert_id() ;
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression
	// ---------------------------------------------------------------
	public function delete() {
		global $msg;
		
		if (!$this->id_equation)
			// impossible d'accéder à cette équation
			return $msg[409];
	
		$query = "delete from bannette_equation WHERE num_equation='$this->id_equation'";
		pmb_mysql_query($query);
		$query = "delete from equations WHERE id_equation='$this->id_equation'";
		pmb_mysql_query($query);
	}

	// pour maj de requete d'équation
	public function make_hidden_search_form($url="", $priv_pro="PUB", $id_empr=0) {
	    global $search;
	    global $charset;
	    
	    $url = "./catalog.php?categ=search&mode=6" ;
	    // remplir $search
	    $this->search_class->unserialize_search($this->requete);
	    
	    $r="<form name='modif_requete_form_$this->id_equation' action='$url' style='display:none' method='post'>";
	    
	    for ($i=0; $i<count($search); $i++) {
	    	$inter="inter_".$i."_".$search[$i];
	    	global ${$inter};
	    	$op="op_".$i."_".$search[$i];
	    	global ${$op};
	    	$field_="field_".$i."_".$search[$i];
	    	global ${$field_};
	    	$field=${$field_};
	    	//Récupération des variables auxiliaires
	    	$fieldvar_="fieldvar_".$i."_".$search[$i];
	    	global ${$fieldvar_};
	    	$fieldvar=${$fieldvar_};
	    	if (!is_array($fieldvar)) $fieldvar=array();
	
	    	$r.="<input type='hidden' name='search[]' value='".htmlentities($search[$i],ENT_QUOTES,$charset)."'/>";
	    	$r.="<input type='hidden' name='".$inter."' value='".htmlentities(${$inter},ENT_QUOTES,$charset)."'/>";
	    	$r.="<input type='hidden' name='".$op."' value='".htmlentities(${$op},ENT_QUOTES,$charset)."'/>";
	    	if (is_array($field)) {
                $r.= $this->get_recursif_field($field, $field_);
	    	}
	    	reset($fieldvar);
	    	foreach ($fieldvar as $var_name => $var_value) {
	    		for ($j=0; $j<count($var_value); $j++) {
	    			$r.="<input type='hidden' name='".$fieldvar_."[".$var_name."][]' value='".htmlentities($var_value[$j],ENT_QUOTES,$charset)."'/>";
	    		}
	    	}
	    }
	    $r.="<input type='hidden' name='id_equation' value='$this->id_equation'/>";
	    $r.="<input type='hidden' name='priv_pro' value='$priv_pro'/>";
	    $r.="<input type='hidden' name='id_empr' value='$id_empr'/>";
	    $r.="</form>";
	    return $r;
    }

    public function get_id() {
    	return $this->id_equation;
    }
    
    public function get_recursif_field(array $elements, $field_) {

        global $charset;
        $html = "";
        
        $index = count($elements);
        for ($i = 0; $i < $index; $i++) {
            if (is_array($elements[$i])) {
                $html .= $this->get_recursif_field($elements[$i], $field_."[$i]");
            } else {
                $html .= "
                    <input type='hidden' 
                    name='".$field_."[$i]' 
                    value='".htmlentities($elements[$i],ENT_QUOTES,$charset)."'/>";
            }
        }
        return $html;
    }
} # fin de définition de la classe equation
