<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: origin_authorities.class.php,v 1.7.4.1 2025/03/04 11:29:31 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'origin_authorities'

if ( ! defined( 'ORIAUTH_CLASS' ) ) {
  define( 'ORIAUTH_CLASS', 1 );

class origin_authorities {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */
	public $oriauth_id=0;
	public $oriauth_nom='';
	public $oriauth_pays='FR';
	public $oriauth_diffusion=1;

	/* ---------------------------------------------------------------
		origin_authorities($id) : constructeur
   --------------------------------------------------------------- */
	public function __construct($id=0) {
		$this->oriauth_id = intval($id);
		$this->getData();
	}

	/* ---------------------------------------------------------------
		getData() : récupération des propriétés
   --------------------------------------------------------------- */
	public function getData() {
		if(!$this->oriauth_id) return;

		/* récupération des informations de l'origine de l'autorité */

		$requete = 'SELECT id_origin_authorities, origin_authorities_name, origin_authorities_country, origin_authorities_diffusible FROM origin_authorities WHERE id_origin_authorities='.$this->oriauth_id.' ';
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) return;

		$data = pmb_mysql_fetch_object($result);
		$this->oriauth_nom = $data->origin_authorities_name;
		$this->oriauth_pays = $data->origin_authorities_country;
		$this->oriauth_diffusion = $data->origin_authorities_diffusible;
	}

	// ---------------------------------------------------------------
	//		import() : import d'une origine d'autorité
	// ---------------------------------------------------------------
	public static function import($data) {
		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['nom']
		//	$data['pays']
		//	$data['diffusion']

		// check sur le type de  la variable passée en paramètre
		if (!is_array($data) || empty($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		// check sur les éléments du tableau
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT origin_authorities_name FROM origin_authorities "),0);
		$data['nom'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['nom']))),0,$long_maxi));
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT origin_authorities_country FROM origin_authorities "),0);
		$data['pays'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['pays']))),0,$long_maxi));

		if(empty($data['diffusion'])) $data['diffusion'] = 1;
		if($data['nom']=="") return 0;

		// préparation de la requête
		$key0 = addslashes($data['nom']);
		$key1 = addslashes($data['pays']);
		$key2 = $data['diffusion'];

		/* vérification que l'origine de l'autorité existe */
		$query = "SELECT id_origin_authorities FROM origin_authorities WHERE origin_authorities_name='{$key0}' and origin_authorities_country = '{$key1}' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT origin_authorities ".$query);
		$origin_authorities  = pmb_mysql_fetch_object($result);

		/* l'origine de l'autorité existe, on retourne l'ID */
		if (is_object($origin_authorities) && $origin_authorities->id_origin_authorities) {
			return $origin_authorities->id_origin_authorities;
		}

		// id non-récupérée, il faut créer la forme.

		$query  = "INSERT INTO origin_authorities SET ";
		$query .= "origin_authorities_name='".$key0."', ";
		$query .= "origin_authorities_country='".$key1."', ";
		$query .= "origin_authorities_diffusible='".$key2."' ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't INSERT into origin_authorities ".$query);

		return pmb_mysql_insert_id();

	} /* fin méthode import */

	/* une fonction pour générer des combo Box
   paramètres :
	$selected : l'élément sélectionné le cas échéant
   retourne une chaine de caractères contenant l'objet complet */

	public static function gen_combo_box ( $selected ) {
		$requete="select id_origin_authorities, origin_authorities_name, origin_authorities_country from origin_authorities order by origin_authorities_name, origin_authorities_country ";
		$champ_code="id_origin_authorities";
		$champ_info="origin_authorities_name";
		$nom="id_origin_authorities";
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


