<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Oeuvre.php,v 1.2 2020/11/25 11:07:50 arenou Exp $
namespace Sabre\PMB;

class Oeuvre extends Collection {
    protected $titre_uniforme;
	public $config;
	public $type;
	
	public function __construct($name,$config) {
		$this->config = $config;
		$this->type = "oeuvre";
		$code = $this->get_code_from_name($name);
		$tu_id = substr($code,1);
		if($tu_id){
		    $this->titre_uniforme = new \titre_uniforme($tu_id);
		}
	}
	
	public function getChildren() {
		//les enfants attendus par le paramétrage du connecteur
		//sauf pour le noeud racine d'un thésaurus...
		$current_children=array();

		$children = parent::getChildren();

		if(!empty($this->titre_uniforme)){
            $links_definition = \marc_list_collection::get_instance('oeuvre_link');
            $this->titre_uniforme->get_oeuvre_links();
            for($i=0 ; $i<count($this->titre_uniforme->oeuvre_expressions_from) ; $i++){
                $children[] = $this->getChild("(O".$this->titre_uniforme->oeuvre_expressions_from[$i]['to_id'].")");
            }
            for($i=0 ; $i<count($this->titre_uniforme->other_links) ; $i++){
                if(in_array($this->titre_uniforme->other_links[$i]['type'],array_keys($links_definition->table['descendant']))){
                    $children[] = $this->getChild("(O".$this->titre_uniforme->other_links[$i]['to_id'].")");
                }
            }
		}
		usort($current_children,"sortChildren");
		return array_merge($children,$current_children);
	}

	public function getName() {
		return $this->format_name($this->titre_uniforme->get_isbd_simple()." (O".$this->titre_uniforme->id.")");
	}
	
	public function need_to_display($categ_id){
        return true;
	}
	
	public function getNotices(){
		
		$this->notices = array();		
		if($this->titre_uniforme->id){
			$query = "select ntu_num_notice as notice_id 
            from notices_titres_uniformes 
            join explnum on explnum_notice = ntu_num_notice and explnum_notice != 0 
            where explnum_mimetype != 'URL' and ntu_num_tu = ".$this->titre_uniforme->id;
			$this->filterNotices($query);		
		}
		return $this->notices;
	}
    
	public function update_notice_infos($notice_id){
		if($notice_id*1 >0){
			$query = "select * from notices_notices_titres_uniformes where ntu_num_notice= ".$notice_id." and ntu_num_tu = ".$this->titre_uniforme->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result) == 0){
			    $query = "insert into notices_notices_titres_uniformes set ntu_num_notice = ".$notice_id.",ntu_num_tu = ".$this->titre_uniforme->id;
				pmb_mysql_query($query);				
			} 
		}
	}
}