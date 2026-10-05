<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_external_filter.class.php,v 1.5.10.1 2025/03/14 13:27:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/admin/opac/opac_view/filters/opac_view_filters.class.php");

class facettes_external_filter extends opac_view_filters {    
    
	protected function _init_path() {
		$this->path="facettes_external_filter";
	}
    
    public function fetch_data() {
		$this->selected_list=array();
		$req="SELECT * FROM opac_filters where opac_filter_view_num=".$this->id_vue." and  opac_filter_path='facettes_external' ";
		$myQuery = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($myQuery)){
			$r=pmb_mysql_fetch_object($myQuery);
			$param=unserialize($r->opac_filter_param);
			$this->selected_list=$param["selected"];
		}				
		$myQuery = pmb_mysql_query("SELECT * FROM facettes_external WHERE num_facettes_set = 0 ORDER BY facette_name ");
		$this->liste_item=array();
		$i=0;
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->liste_item[$i]=new stdClass();
				$this->liste_item[$i]->id=$r->id_facette ;
				$this->liste_item[$i]->name=$r->facette_name ;
				if(in_array($r->id_facette ,$this->selected_list)) {
				    $this->liste_item[$i]->selected=1;
				} else {
				    $this->liste_item[$i]->selected=0;				
				}
				$i++;			
			}	
		}
		return true;
 	}	
	
 	public function get_available_columns() {
 	    return array(
 	        'selection' => $this->msg["selection_opac"],
 	        'name' => $this->msg["title"],
 	    );
 	}
 	
	public function save_form(){
		$req="delete FROM opac_filters where opac_filter_view_num=".$this->id_vue." and  opac_filter_path='facettes_external' ";
		pmb_mysql_query($req);
		
		$param=array();
		$selected_list=array();
		for($i=0;$i<count($this->liste_item);$i++) {
			eval("global \$".$this->path."_selected_".$this->liste_item[$i]->id.";
			\$selected= \$".$this->path."_selected_".$this->liste_item[$i]->id.";");
			if($selected){
				$selected_list[]=$this->liste_item[$i]->id;
			}
		}
		$param["selected"]=$selected_list;
		$param=addslashes(serialize($param));		
		$req="insert into opac_filters set opac_filter_view_num=".$this->id_vue." ,  opac_filter_path='facettes_external', opac_filter_param='$param' ";
		pmb_mysql_query($req);
		
		//sauvegarde dans les facettes externes..
		$req = "SELECT id_facette, facette_opac_views_num FROM facettes_external WHERE num_facettes_set = 0";
		$res = pmb_mysql_query($req);
		if ($res) {
			while($row = pmb_mysql_fetch_object($res)) {
				$views_num = array();
				//la facette est sélectionnée..
				if (in_array($row->id_facette,$selected_list)) {
					if ($row->facette_opac_views_num != "") {
						$views_num = explode(",", $row->facette_opac_views_num);
						if (count($views_num)) {
							if (!in_array($this->id_vue, $views_num)) {
								$views_num[] = $this->id_vue;
								$requete = "update facettes_external set facette_opac_views_num='".implode(",", $views_num)."' where id_facette=".$row->id_facette;
								pmb_mysql_query($requete);
							}
						}
					}
				} else {
					if ($row->facette_opac_views_num != "") {
						$views_num = explode(",", $row->facette_opac_views_num);
						if (count($views_num)) {
							$key_exists = array_search($this->id_vue, $views_num);
							if ($key_exists !== false) {
								//la facette ne doit plus être affichée dans la vue
								array_splice($views_num,$key_exists,1);
								$requete = "update facettes_external set facette_opac_views_num='".implode(",", $views_num)."' where id_facette=".$row->id_facette;
								pmb_mysql_query($requete);
							}
						}
					} else {
						//la facette doit être affichée dans les autres vues sauf celle-ci..
						$requete = "select opac_view_id from opac_views where opac_view_id <> ".$this->id_vue;
						$resultat = pmb_mysql_query($requete);
						$views_num[] = 0; // OPAC classique
						while ($view = pmb_mysql_fetch_object($resultat)) {
							$views_num[] = $view->opac_view_id;
						}
						$requete = "update facettes_external set facette_opac_views_num='".implode(",", $views_num)."' where id_facette=".$row->id_facette;
						pmb_mysql_query($requete);
					}
				}
			}
		}
	}	
}