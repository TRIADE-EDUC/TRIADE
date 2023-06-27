<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classements.class.php,v 1.8 2017-11-13 10:24:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des classements de la DSI

class classement {
	// propriétés
	public $id_classement ;
	public $nom_classement = '';
	public $nom_classement_opac = '';
	public $type_classement = 'BAN';
	public $order = 0;
	
	// ---------------------------------------------------------------
	//	constructeur
	// ---------------------------------------------------------------
	public function __construct($id_classement=0) {
		$this->id_classement = $id_classement+0;
		$this->getData();
	}

	// ---------------------------------------------------------------
	public function getData() {
		$this->type_classement	= 'BAN';
		$this->nom_classement	= '';	
		$this->nom_classemen_opac = '';	
		$this->order = 0;
		if($this->id_classement) {
			$requete = "SELECT type_classement, nom_classement, classement_opac_name, classement_order FROM classements WHERE id_classement='$this->id_classement' ";
			$result = @pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				$this->type_classement = $temp->type_classement;
				$this->nom_classement = $temp->nom_classement;
				$this->nom_classement_opac = $temp->classement_opac_name;
				$this->order = $temp->classement_order;
			}
		}
	}

	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form($type="pro") {
		global $msg, $charset;
		global $dsi_classement_form;
	
		if ($this->id_classement) {
			$action = "./dsi.php?categ=options&sub=classements&id_classement=$this->id_classement&suite=update";
			$button_delete = "<input type='button' class='bouton' value='$msg[63]' ";
			$button_delete .= "onClick=\"confirm_delete();\">";
			$libelle = $msg['dsi_clas_form_modif'];
			$type_classement = $msg['dsi_clas_type_class_'.$this->type_classement] ;
		} else {
			$action = "./dsi.php?categ=options&sub=classements&id_classement=0&suite=update";
			$libelle = $msg['dsi_clas_form_creat'];
			$button_delete ='';
			$type_classement = "<select id='type_classement' name='type_classement'><option value='BAN'>".$msg['dsi_clas_type_class_BAN']."</option><option value='EQU'>".$msg['dsi_clas_type_class_EQU']."</OPTION></select>";
		}
		$dsi_classement_form = str_replace('!!libelle!!', $libelle, $dsi_classement_form);
		$dsi_classement_form = str_replace('!!id_classement!!', $this->id_classement, $dsi_classement_form);
		$dsi_classement_form = str_replace('!!action!!', $action, $dsi_classement_form);
		$dsi_classement_form = str_replace('!!nom_classement!!', htmlentities($this->nom_classement,ENT_QUOTES, $charset), $dsi_classement_form);
		$dsi_classement_form = str_replace('!!nom_classement_opac!!', htmlentities($this->nom_classement_opac,ENT_QUOTES, $charset), $dsi_classement_form);
		$dsi_classement_form = str_replace('!!type_classement!!', $type_classement, $dsi_classement_form);
	
		if ($this->id_classement==1) $button_delete="";
		$dsi_classement_form = str_replace('!!delete!!', $button_delete,  $dsi_classement_form);
		print $dsi_classement_form;
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
			$result = pmb_mysql_query($query);
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
			$result = @pmb_mysql_query($query);
			$this->id_classement = pmb_mysql_insert_id() ;
		}
	}
	
	// ---------------------------------------------------------------
	public function delete() {
		if ($this->id_classement==1) return ;
		$requete = "delete FROM classements where id_classement='".$this->id_classement."' ";
		$result = @pmb_mysql_query($requete);
	}
	
	// ---------------------------------------------------------------
	public function set_order($sens) {
		global $dbh;
	
		switch ($sens){
			case "up":
				$requete="select classement_order from classements where id_classement=".$this->id_classement;
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select max(classement_order) as ordre from classements where (type_classement='BAN' or type_classement='') and classement_order<$ordre";
				$resultat=pmb_mysql_query($requete);
				$ordre_max=@pmb_mysql_result($resultat,0,0);
				if ($ordre_max) {
					$requete="select id_classement from classements where (type_classement='BAN' or type_classement='') and classement_order=$ordre_max limit 1";
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
				$requete="select min(classement_order) as ordre from classements where (type_classement='BAN' or type_classement='') and classement_order>$ordre";
				$resultat=pmb_mysql_query($requete);
				$ordre_min=@pmb_mysql_result($resultat,0,0);
				if ($ordre_min) {
					$requete="select id_classement from classements where (type_classement='BAN' or type_classement='') and classement_order=$ordre_min limit 1";
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
	
} // fin de déclaration de la classe classement
  
