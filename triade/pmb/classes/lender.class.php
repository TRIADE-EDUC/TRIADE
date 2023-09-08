<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lender.class.php,v 1.13 2017-01-03 11:14:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'prêteurs d'ouvrage'

if ( ! defined( 'LENDER_CLASS' ) ) {
  define( 'LENDER_CLASS', 1 );

class lender {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $idlender=0;			// MySQL idlender in table 'lenders'
	public $lender_libelle='';		// nom du prêteur

	// ---------------------------------------------------------------
	//		lender($id) : constructeur
	// ---------------------------------------------------------------
	
	public function __construct($id=0) {
		$this->idlender = $id+0;
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos du lender
	// ---------------------------------------------------------------
	
	public function getData() {
		if($this->idlender) {
			$requete = "SELECT idlender, lender_libelle FROM lenders WHERE idlender='".$this->idlender."' LIMIT 1 " or die (pmb_mysql_error());
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				$this->idlender		= $temp->idlender;
				$this->lender_libelle		= $temp->lender_libelle;
			}
		}
	}

	/* une fonction pour générer des combo Box 
   paramêtres :
	$selected : l'élément sélectioné le cas échéant
   retourne une chaine de caractères contenant l'objet complet */

	public static function gen_combo_box ( $selected ) {
		global $msg;
		$requete="select idlender, lender_libelle from lenders order by lender_libelle ";
		$champ_code="idlender";
		$champ_info="lender_libelle";
		$nom="book_lender_id";
		$on_change="";
		$liste_vide_code="0";
		$liste_vide_info= $msg['class_lender'];
		$option_premier_code="";
		$option_premier_info="";
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\">\n" ;
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			if ($option_premier_info!="") {	
				$gen_liste_str.="<option value=\"".$option_premier_code."\" ";
				if ($selected==$option_premier_code) $gen_liste_str.="selected" ;
				$gen_liste_str.=">".$option_premier_info."\n";
			}
			$i=0;
			while ($i<$nb_liste) {
				$gen_liste_str.="<option value=\"".pmb_mysql_result($resultat_liste,$i,$champ_code)."\" " ;
				if ($selected==pmb_mysql_result($resultat_liste,$i,$champ_code)) {
					$gen_liste_str.="selected" ;
				}
				$gen_liste_str.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n" ;
				$i++;
			}
		}
		$gen_liste_str.="</select>\n" ;
		return $gen_liste_str ;
	} /* fin gen_combo_box */


	public static function gen_multiple_combo_box($liste_id=array()){
		global $msg,$charset;
	
		$requete="select idlender, lender_libelle from lenders order by lender_libelle ";
		$champ_code="idlender";
		$champ_info="lender_libelle";
		$nom="book_lender_id[]";
		$on_change="";
		$liste_vide_code="0";
		$liste_vide_info= $msg['class_lender'];
		$option_premier_code="";
		$option_premier_info="";
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\" multiple >\n" ;
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			$i=0;
			while ($i<$nb_liste) {
				$id=pmb_mysql_result($resultat_liste,$i,$champ_code);
				$gen_liste_str.="<option value=\"".$id."\" " ;
				if(in_array($id, $liste_id)) {
					$gen_liste_str.=" selected " ;
				}
				$gen_liste_str.=">".pmb_mysql_result($resultat_liste,$i,$champ_info)."</option>\n" ;
				$i++;
			}
		}
		$gen_liste_str.="</select>\n" ;
		return $gen_liste_str ;
	
	}

	public static function import($data) {
	
		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['lender_libelle'] 	
	
		global $dbh;
	
		// check sur le type de la variable passée en paramètre
		if(!sizeof($data) || !is_array($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		// check sur les éléments du tableau
		
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT lender_libelle FROM lenders limit 1"),0);
		$data['lender_libelle'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['lender_libelle']))),0,$long_maxi));
	
		if($data['lender_libelle']=="") return 0;
	
		// préparation de la requête
		$key0 = addslashes($data['lender_libelle']);
		
		/* vérification que le lender existe */
		$query = "SELECT idlender FROM lenders WHERE lender_libelle='${key0}' LIMIT 1 ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't SELECT lenders ".$query);
		$lenders  = pmb_mysql_fetch_object($result);
	
		/* le lender existe, on retourne l'ID */
		if($lenders->idlender) return $lenders->idlender;
	
		// id non-récupérée, il faut créer la forme.
		
		$query  = "INSERT INTO lenders SET lender_libelle='".$key0."' ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't INSERT into lenders ".$query);
	
		return pmb_mysql_insert_id($dbh);
	
	} /* fin méthode import */

} # fin de définition de la classe serie

} # fin de délaration

