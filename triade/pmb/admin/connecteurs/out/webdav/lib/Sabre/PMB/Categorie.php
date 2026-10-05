<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Categorie.php,v 1.11 2019/07/05 13:25:14 btafforeau Exp $
namespace Sabre\PMB;

class Categorie extends Collection {
	protected $categ;
	public $config;
	public $type;
	
	public function __construct($name,$config) {
		$this->config = $config;
		$this->type = "categorie";
		$code = $this->get_code_from_name($name);
		$id_noeud = substr($code,1);
		if($id_noeud){
			$this->categ = new \category($id_noeud);
		}
	}
	
	public function getChildren() {
		//les enfants attendus par le paramétrage du connecteur
		//sauf pour le noeud racine d'un thésaurus...
		$current_children=array();
		if($this->categ->id != $this->categ->thes->num_noeud_racine){
			$children = parent::getChildren();
		}else{
			$children = array();
		}
		//les enfants de la catégorie (navigation thésaurus)
		$query = "select id_noeud from noeuds where num_parent=".$this->categ->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($this->need_to_display($row->id_noeud)){
					$current_children[] = $this->getChild("(C".$row->id_noeud.")");
				}
			}
		}
		usort($current_children,"sortChildren");
		return array_merge($children,$current_children);
	}

	public function getName() {
		return $this->format_name($this->categ->libelle." (C".$this->categ->id.")");
	}
	
	public function need_to_display($categ_id){
		if($categ_id){
			//on va cherché le libellé...
			$categ = new \category($categ_id);
			if(substr($categ->libelle,0,1) == "~"){
				return false;
			}
			if($this->config['only_with_notices']){
				if($this->restricted_objects != ""){
					$clause = " and notice_id in (".$this->restricted_objects.")";
				}else $clause = "";
				//notices ou notices de bulletins...
				$query = "select sum(nb) from (select count(1) as nb from notices_categories join noeuds on id_noeud = notices_categories.num_noeud join notices on notice_id = notcateg_notice join explnum on explnum_notice = notice_id and explnum_notice != 0 where explnum_mimetype != 'URL' and path like (select concat(path,'%') from noeuds where id_noeud = ".$categ_id.")".$clause." union select count(1) as nb from notices_categories join noeuds on id_noeud = notices_categories.num_noeud join notices on notice_id = notcateg_notice and niveau_biblio = 'b ' join bulletins on num_notice = notice_id join explnum on explnum_bulletin = bulletin_id and explnum_notice=0 where explnum_mimetype != 'URL' and path like (select concat(path,'%') from noeuds where id_noeud = ".$categ_id.")".$clause.") as uni ";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					if(pmb_mysql_result($result,0,0)>0){
						return true;
					}
				}
			}else{
				return true;
			}
		}
		return false;
	}
	
	public function getNotices(){
		
		$this->notices = array();		
		if($this->categ->id){
			$clause ="";
			$query = "select notcateg_notice as notice_id from notices_categories join noeuds on id_noeud = notices_categories.num_noeud join notices on notice_id = notcateg_notice join explnum on explnum_notice = notice_id and explnum_notice != 0 where explnum_mimetype != 'URL' and (path like concat((select path from noeuds where id_noeud=".$this->categ->id."),'%')) union select notcateg_notice as notice_id from notices_categories join noeuds on id_noeud = notices_categories.num_noeud join notices on notice_id = notcateg_notice and niveau_biblio = 'b ' join bulletins on num_notice = notice_id join explnum on explnum_bulletin = bulletin_id and explnum_notice=0 where explnum_mimetype != 'URL' and (path like concat((select path from noeuds where id_noeud=".$this->categ->id."),'%'))";
			$this->filterNotices($query);		
		}
		return $this->notices;
	}
    
	public function update_notice_infos($notice_id){
		if($notice_id*1 >0){
			$query = "select * from notices_categories where notcateg_notice = ".$notice_id." and num_noeud = ".$this->categ->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result) == 0){
				$query = "insert into notices_categories set notcateg_notice = ".$notice_id.",num_noeud = ".$this->categ->id;
				pmb_mysql_query($query);				
			} 
		}
	}
}