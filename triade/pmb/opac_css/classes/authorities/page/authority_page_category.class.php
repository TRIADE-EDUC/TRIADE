<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_category.class.php,v 1.5 2021/06/14 07:38:34 dgoron Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" )) die ( "no access" );

global $class_path;
require_once ($class_path."/authorities/page/authority_page.class.php");

/**
 * class authority_page_category
 * Controler d'une page d'une autorité catégorie
 */
class authority_page_category extends authority_page {
	/**
	 * Constructeur
	 * @param int $id Identifiant de la catégorie
	 */
	public function __construct($id) {
		$this->id = intval($id);
		$query = "select id_noeud from noeuds where id_noeud = " . $this->id;
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			//$this->authority = new authority (0, $this->id, AUT_TABLE_CATEG);
			$this->authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_CATEG]);
		}
	}
	
	protected function get_title_recordslist() {
		global $msg, $charset;
		return htmlentities($msg['doc_category_title'], ENT_QUOTES, $charset);
	}
	
	protected function get_join_recordslist() {
		global $opac_auto_postage_etendre_recherche;
		global $opac_auto_postage_descendant, $opac_auto_postage_montant;
		global $nb_level_montant, $nb_level_descendant;
		
		if(!isset($this->acces_j)) {
			$this->calculate_restrict_access_rights();
		}
		$q = "select path from noeuds where id_noeud = '".$this->id."' ";
		$r = pmb_mysql_query($q);
		if($r && pmb_mysql_num_rows($r)){
			$path=pmb_mysql_result($r, 0, 0);
			$nb_pere=substr_count($path,'/');
		}else{
			$path="";
			$nb_pere=0;
		}
		// Si un path est renseigné et le paramètrage activé
		if ($path && ($opac_auto_postage_descendant || $opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){
// 			$this->join_recordslist = " FROM noeuds STRAIGHT_JOIN notices_categories on id_noeud=num_noeud join notices on notcateg_notice=notice_id ".$this->acces_j." ".$this->statut_j." ";
			$join_recordslist = " JOIN notices_categories on notcateg_notice=notice_id JOIN noeuds on id_noeud=num_noeud ";
		} else {
			$join_recordslist = " JOIN notices_categories on notcateg_notice=notice_id ";
		}
		return $join_recordslist;
	}
	
	protected function get_clause_authority_id_recordslist() {
		global $opac_auto_postage_descendant, $opac_auto_postage_montant, $opac_auto_postage_etendre_recherche;
		global $nb_level_montant, $nb_level_descendant;
		
		$q = "select path from noeuds where id_noeud = '".$this->id."' ";
		$r = pmb_mysql_query($q);
		if($r && pmb_mysql_num_rows($r)){
			$path=pmb_mysql_result($r, 0, 0);
			$nb_pere=substr_count($path,'/');
		}else{
			$path="";
			$nb_pere=0;
		}
		
		// Si un path est renseigné et le paramètrage activé
		if ($path && ($opac_auto_postage_descendant || $opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){
				
			//Recherche des fils
			$liste_fils = "";
			if(($opac_auto_postage_descendant || $opac_auto_postage_etendre_recherche)&& $nb_level_descendant) {
				if($nb_level_descendant != '*' && is_numeric($nb_level_descendant))
					$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$nb_level_descendant}$' ";
					else
						//$liste_fils=" path regexp '^$path(\\/[0-9]*)*' ";
						$liste_fils=" path like '$path/%' or  path = '$path' ";
			} else {
				$liste_fils=" id_noeud='".$id."' ";
			}
				
			// recherche des pères
			$liste_pere = "";
			if(($opac_auto_postage_montant || $opac_auto_postage_etendre_recherche) && $nb_level_montant ) {
					
				$id_list_pere=explode('/',$path);
				$stop_pere=0;
				if($nb_level_montant != '*' && is_numeric($nb_level_montant)) $stop_pere=$nb_pere-$nb_level_montant;
				if($stop_pere<0) $stop_pere=0;
				for($i=$nb_pere;$i>=$stop_pere; $i--) {
					$liste_pere.= " or id_noeud='".$id_list_pere[$i]."' ";
				}
			}
			return "(".$liste_fils." ".$liste_pere.")";
		} else {
			return "num_noeud=".$this->id;
		}
	}
	
	protected function get_mode_recordslist() {
		return "categ_see";
	}
}