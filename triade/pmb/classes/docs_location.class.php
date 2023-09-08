<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docs_location.class.php,v 1.20 2017-06-22 10:19:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'docs_location'

if ( ! defined( 'DOCSLOCATION_CLASS' ) ) {
  define( 'DOCSLOCATION_CLASS', 1 );
	
class docs_location {
	
	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */
	
	public $id=0;
	public $libelle='';
	public $pret_flag='';
	public $locdoc_codage_import="";
	public $locdoc_owner=0;
	public $num_infopage=0;
	public $email='';
	
	/* ---------------------------------------------------------------
		docs_location($id) : constructeur
   --------------------------------------------------------------- */
	
	public function __construct($id=0) {
		$this->id = $id+0;
		$this->getData();
	}
	
	/* ---------------------------------------------------------------
		getData() : récupération des propriétés
   --------------------------------------------------------------- */
	public function getData() {
		global $dbh;

		if(!$this->id) return;
		
		/* récupération des informations du statut */
	
		$requete = 'SELECT * FROM docs_location WHERE idlocation='.$this->id.' LIMIT 1;';
		$result = @pmb_mysql_query($requete, $dbh);
		if(!pmb_mysql_num_rows($result)) return;
			
		$data = pmb_mysql_fetch_object($result);
		$this->id = $data->idlocation;		
		$this->libelle = $data->location_libelle;		
		$this->locdoc_codage_import = $data->locdoc_codage_import;
		$this->locdoc_owner = $data->locdoc_owner;
		$this->num_infopage = $data->num_infopage;
		$this->email = $data->email;
	}
		
	// ---------------------------------------------------------------
	//		import() : import d'un lieu de document
	// ---------------------------------------------------------------
	public static function import($data) {

		// cette méthode prend en entrée un tableau constitué des informations suivantes :
		//	$data['location_libelle'] 	
		//	$data['locdoc_codage_import']
		//	$data['locdoc_owner']

		global $dbh;

		// check sur le type de  la variable passée en paramètre
		if(!sizeof($data) || !is_array($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		// check sur les éléments du tableau
	
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT location_libelle FROM docs_location limit 1"),0);
		$data['location_libelle'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['location_libelle']))),0,$long_maxi));
		$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT locdoc_codage_import FROM docs_location limit 1"),0);
		$data['locdoc_codage_import'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['locdoc_codage_import']))),0,$long_maxi));
	
		if($data['locdoc_owner']=="") $data['locdoc_owner'] = 0;
		if($data['location_libelle']=="") return 0;
		/* locdoc_codage_import est obligatoire si locdoc_owner != 0 */
		//if(($data['locdoc_owner']!=0) && ($data['locdoc_codage_import']=="")) return 0;
		
		// préparation de la requête
		$key0 = addslashes($data['location_libelle']);
		$key1 = addslashes($data['locdoc_codage_import']);
		$key2 = $data['locdoc_owner'];
		
		/* vérification que le lieu existe */
		$query = "SELECT idlocation FROM docs_location WHERE locdoc_codage_import='${key1}' and locdoc_owner = '${key2}' LIMIT 1 ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't SELECT docs_location ".$query);
		$docs_location  = pmb_mysql_fetch_object($result);
	
		/* le lieu de doc existe, on retourne l'ID */
		if($docs_location->idlocation) return $docs_location->idlocation;
	
		// id non-récupérée, il faut créer la forme.
		
		$query  = "INSERT INTO docs_location SET ";
		$query .= "location_libelle='".$key0."', ";
		$query .= "locdoc_codage_import='".$key1."', ";
		$query .= "locdoc_owner='".$key2."' ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't INSERT into docs_location ".$query);
	
		return pmb_mysql_insert_id($dbh);
	} /* fin méthode import */
	
			
	/* une fonction pour générer des combo Box 
	   paramêtres :
		$selected : l'élément sélectioné le cas échéant
	   retourne une chaine de caractères contenant l'objet complet */
	public static function gen_combo_box ( $selected ) {
		global $msg;
		$requete="select idlocation, location_libelle from docs_location order by location_libelle ";
		$champ_code="idlocation";
		$champ_info="location_libelle";
		$nom="book_location_id";
		$on_change="";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_location'];
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
	
	public static function gen_combo_box_empr ( $selected, $afficher_premier=1, $on_change="" ) {
		global $msg;
		$requete="select idlocation, location_libelle from docs_location order by location_libelle ";
		$champ_code="idlocation";
		$champ_info="location_libelle";
		$nom="empr_location_id";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_location'];
		$option_premier_code="0";
		if ($afficher_premier) $option_premier_info=$msg['all_location'];
		else $option_premier_info='';
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\" >\n";
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			if ($option_premier_info!="") {	
				$gen_liste_str.="<option value=\"".$option_premier_code."\" ";
				if ($selected==$option_premier_code) $gen_liste_str.="selected" ;
				$gen_liste_str.=">".$option_premier_info."</option>\n";
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
	} /* fin gen_combo_box_empr */
		
	public static function gen_combo_box_docs ( $selected, $afficher_premier=1, $on_change="" ) {
		global $msg;
		$requete="select idlocation, location_libelle from docs_location order by location_libelle ";
		$champ_code="idlocation";
		$champ_info="location_libelle";
		$nom="docs_location_id";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_location'];
		$option_premier_code="0";
		if ($afficher_premier) $option_premier_info=$msg['all_location'];
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\" >\n";
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			if ($option_premier_info!="") {
				$gen_liste_str.="<option value=\"".$option_premier_code."\" ";
				if ($selected==$option_premier_code) $gen_liste_str.="selected" ;
				$gen_liste_str.=">".$option_premier_info."</option>\n";
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
	} /* fin gen_combo_box_docs */
		
		
	public function gen_combo_box_sugg ( $selected, $afficher_premier=1, $on_change="" ) {
		global $msg;
		$requete="select idlocation, location_libelle from docs_location order by location_libelle ";
		$champ_code="idlocation";
		$champ_info="location_libelle";
		$nom="sugg_location_id";
		$liste_vide_code="0";
		$liste_vide_info=$msg['class_location'];
		$option_premier_code="0";
		if ($afficher_premier) $option_premier_info=$msg['all_location'];
		else $option_premier_info='';
		$gen_liste_str="";
		$resultat_liste=pmb_mysql_query($requete);
		$gen_liste_str = "<select name=\"$nom\" onChange=\"$on_change\" >\n";
		$nb_liste=pmb_mysql_num_rows($resultat_liste);
		if ($nb_liste==0) {
			$gen_liste_str.="<option value=\"$liste_vide_code\">$liste_vide_info</option>\n" ;
		} else {
			if ($option_premier_info!="") {	
				$gen_liste_str.="<option value=\"".$option_premier_code."\" ";
				if ($selected==$option_premier_code) $gen_liste_str.="selected" ;
				$gen_liste_str.=">".$option_premier_info."</option>\n";
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
	} /* fin gen_combo_box_sugg */
		
		
	public function gen_multiple_combo($liste_id=array()){
		global $dbh, $msg,$charset;
		
		if(!$liste_id) return;
		
		$req = "select count(1) from docs_location";
		$res = pmb_mysql_query($req,$dbh);
		$nb_loc = pmb_mysql_result($res,0,0);
		$req= "select idlocation, location_libelle from docs_location";
		$res = pmb_mysql_query($req,$dbh);
		$selector_location="";
		if(pmb_mysql_num_rows($res)){				
			$selector_location = "<select id='loc_selector' name='loc_selector[]' multiple>";
			$selector_location .= "<option value='-1' ".((count($liste_id) == $nb_loc) ? 'selected' : '').">".htmlentities($msg['all_location'],ENT_QUOTES,$charset)."</option>";
			while($loc = pmb_mysql_fetch_object($res)){
				if((array_search($loc->idlocation,$liste_id) !== false) && (count($liste_id) != $nb_loc))
					$selected = 'selected';
				else $selected = '';
				$selector_location .= "<option value='".$loc->idlocation."' $selected>".htmlentities($loc->location_libelle,ENT_QUOTES,$charset)."</option>";
			}
			$selector_location .= "</select>";
		}	
		return $selector_location;	
	}
		

	public static function get_html_select($selected=array(),$sel_all=array('id'=>0,'msg'=>''),$sel_attr=array()) {
		global $dbh,$charset;

		$sel='';
		$q = "select idlocation, location_libelle from docs_location order by location_libelle";
		$r = pmb_mysql_query($q, $dbh);
		$res = array();
		if (count($sel_all)) {
			$res[$sel_all['id']]=htmlentities($sel_all['msg'],ENT_QUOTES,$charset);
		}
		if (pmb_mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)){
				$res[$row->idlocation] = $row->location_libelle;
			}
		}
		$size=count($res);
		if (isset($sel_attr['size']) && $sel_attr['size']>$size) $sel_attr['size']=$size;
		if ($size) {
			$sel="<select ";
			if (count($sel_attr)) {
				foreach($sel_attr as $attr=>$val) {
					$sel.="$attr='".$val."' ";
				}
			}
			$sel.=">";
			foreach($res as $id=>$val){
				$sel.="<option value='".$id."'";
				if(in_array($id,$selected)) $sel.=" selected='selected'";
				$sel.=" >";
				$sel.=htmlentities($val,ENT_QUOTES,$charset);
				$sel.="</option>";
			}
			$sel.='</select>';
		}
		return $sel;
	}
} /* fin de définition de la classe */

} /* fin de délaration */


