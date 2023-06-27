<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docs_section.class.php,v 1.10 2017-07-11 15:10:04 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'docs_section'

if ( ! defined( 'DOCSSECTION_CLASS' ) ) {
  define( 'DOCSSECTION_CLASS', 1 );

class docs_section {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */
	public $id=0;
	public $libelle='';
	public $sdoc_codage_import="";
	public $sdoc_owner=0;

	/* ---------------------------------------------------------------
		docs_section($id) : constructeur
   --------------------------------------------------------------- */
	public function __construct($id=0) {
		$this->id = $id+0;
		$this->getData();
	}

	/* ---------------------------------------------------------------
		getData() : récupération des propriétés
   --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
	
		/* récupération des informations de la catégorie */
	
		$requete = "SELECT * FROM docs_section WHERE idsection='".$this->id."' ";
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) return;
			
		$data = pmb_mysql_fetch_object($result);
		$this->id = $data->idsection;		
		$this->libelle = $data->section_libelle;
		$this->sdoc_codage_import = $data->sdoc_codage_import;
		$this->sdoc_owner = $data->sdoc_owner;
	}

	// ---------------------------------------------------------------
	//		import() : import d'une section de document
	// ---------------------------------------------------------------
	public static function import($data) {
	
		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['section_libelle'] 	
		//	$data['sdoc_codage_import']
		//	$data['sdoc_owner']
	
		global $dbh;
	
		// check sur le type de  la variable passée en paramètre
		if(!sizeof($data) || !is_array($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
			}
		// check sur les éléments du tableau
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT section_libelle FROM docs_section limit 1"),0);
		$data['section_libelle'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['section_libelle']))),0,$long_maxi));
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT sdoc_codage_import FROM docs_section limit 1"),0);
		$data['sdoc_codage_import'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['sdoc_codage_import']))),0,$long_maxi));
	
		if($data['sdoc_owner']=="") $data['sdoc_owner'] = 0;
		if($data['section_libelle']=="") return 0;
		/* sdoc_codage_import est obligatoire si sdoc_owner != 0 */
		// if(($data['sdoc_owner']!=0) && ($data['sdoc_codage_import']=="")) return 0;
		
		// préparation de la requête
		$key0 = addslashes($data['section_libelle']);
		$key1 = addslashes($data['sdoc_codage_import']);
		$key2 = $data['sdoc_owner'];
		
		/* vérification que la section existe */
		$query = "SELECT idsection FROM docs_section WHERE sdoc_codage_import='${key1}' and sdoc_owner = '${key2}' LIMIT 1 ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't SELECT docs_section ".$query);
		$docs_section  = pmb_mysql_fetch_object($result);
	
		/* le type de doc existe, on retourne l'ID */
		if($docs_section->idsection) return $docs_section->idsection;
	
		// id non-récupérée, il faut créer la forme.
		$query  = "INSERT INTO docs_section SET ";
		$query .= "section_libelle='".$key0."', ";
		$query .= "sdoc_codage_import='".$key1."', ";
		$query .= "sdoc_owner='".$key2."' ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't INSERT into docs_section ".$query);
		$id_section_cree = pmb_mysql_insert_id($dbh);
		$query = "insert into docsloc_section (num_section,num_location) (SELECT $id_section_cree, idlocation FROM docs_location) ";
		$result = @pmb_mysql_query($query, $dbh);
	
		return $id_section_cree ;

	} /* fin méthode import */

	/* une fonction pour générer des combo Box 
   paramêtres :
	$selected : l'élément sélectioné le cas échéant
   retourne une chaine de caractères contenant l'objet complet */
	public static function gen_combo_box ( $selected ) {
		global $msg;
		$requete="select idsection, section_libelle from docs_section order by section_libelle ";
		$champ_code="idsection";
		$champ_info="section_libelle";
		$nom="book_section_id";
		$on_change="";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_section'];
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

} /* fin de définition de la classe */

} /* fin de délaration */


