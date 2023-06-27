<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: origine_notice.class.php,v 1.9 2019-05-31 14:08:55 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'origine_notice'

if ( ! defined( 'ORINOT_CLASS' ) ) {
  define( 'ORINOT_CLASS', 1 );

class origine_notice {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $orinot_id=0;
	public $orinot_nom='';
	public $orinot_pays='FR';
	public $orinot_diffusion=1;
	protected static $long_maxi_nom;
	protected static $long_maxi_pays;
	
	/* ---------------------------------------------------------------
			origine_notice($id) : constructeur
	   --------------------------------------------------------------- */
	public function __construct($id=0) {
		$this->orinot_id = $id+0;
		$this->getData();
	}

	/* ---------------------------------------------------------------
			getData() : récupération des propriétés
	   --------------------------------------------------------------- */
	public function getData() {
		if(!$this->orinot_id) return;
	
		/* récupération des informations du statut */
	
		$requete = 'SELECT orinot_id, orinot_nom, orinot_pays, orinot_diffusion FROM origine_notice WHERE orinot_id='.$this->orinot_id.' ';
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) return;
			
		$data = pmb_mysql_fetch_object($result);
		$this->orinot_nom = $data->orinot_nom;
		$this->orinot_pays = $data->orinot_pays;
		$this->orinot_diffusion = $data->orinot_diffusion;
	}

	/**
	 * Initialisation du tableau de valeurs pour update et import
	 */
	protected static function get_default_data() {
		return array(
				'nom' => '',
				'pays' => '',
				'diffusion' => '',
		);
	}
	
	// ---------------------------------------------------------------
	//		import() : import d'un statut de document
	// ---------------------------------------------------------------
	public static function import($data) {
	
		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['nom'] 	
		//	$data['pays']
		//	$data['diffusion']
	
		global $dbh;
	
		// check sur le type de  la variable passée en paramètre
		if(!sizeof($data) || !is_array($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		
		$data = array_merge(static::get_default_data(), $data);
		
		// check sur les éléments du tableau
		if(!isset(static::$long_maxi_nom)) {
			static::$long_maxi_nom = pmb_mysql_field_len(pmb_mysql_query("SELECT orinot_nom FROM origine_notice "),0);
		}
		$data['nom'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['nom']))),0,static::$long_maxi_nom));
		if(!isset(static::$long_maxi_pays)) {
			static::$long_maxi_pays = pmb_mysql_field_len(pmb_mysql_query("SELECT orinot_pays FROM origine_notice "),0);
		}
		$data['pays'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['pays']))),0,static::$long_maxi_pays));
	
		if($data['diffusion']=="") $data['diffusion'] = 1;
		if($data['nom']=="") return 0;
		
		// préparation de la requête
		$key0 = addslashes($data['nom']);
		$key1 = addslashes($data['pays']);
		$key2 = $data['diffusion'];
		
		/* vérification que le statut existe */
		$query = "SELECT orinot_id FROM origine_notice WHERE orinot_nom='${key0}' and orinot_pays = '${key1}' LIMIT 1 ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't SELECT origine_notice ".$query);
		$origine_notice  = pmb_mysql_fetch_object($result);
	
		/* le statut de doc existe, on retourne l'ID */
		if(!empty($origine_notice->orinot_id)) return $origine_notice->orinot_id;
	
		// id non-récupérée, il faut créer la forme.
		
		$query  = "INSERT INTO origine_notice SET ";
		$query .= "orinot_nom='".$key0."', ";
		$query .= "orinot_pays='".$key1."', ";
		$query .= "orinot_diffusion='".$key2."' ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't INSERT into origine_notice ".$query);
	
		return pmb_mysql_insert_id($dbh);

	} /* fin méthode import */

	/* une fonction pour générer des combo Box 
   paramêtres :
	$selected : l'élément sélectioné le cas échéant
   retourne une chaine de caractères contenant l'objet complet */

	public static function gen_combo_box ( $selected ) {
		$requete="select orinot_id, orinot_nom, orinot_pays from origine_notice order by orinot_nom, orinot_pays ";
		$champ_code="orinot_id";
		$champ_info="orinot_nom";
		$nom="orinot_id";
		$on_change="";
		$liste_vide_code="";
		$liste_vide_info="";
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


