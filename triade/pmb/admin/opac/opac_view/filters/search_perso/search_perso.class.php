<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_perso.class.php,v 1.9.10.1 2025/03/14 13:27:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/admin/opac/opac_view/filters/opac_view_filters.class.php");

class search_perso extends opac_view_filters {
	
	protected function _init_path() {
		$this->path="search_perso";
	}
    
    public function fetch_data() {
		parent::fetch_data();
						
		$myQuery = pmb_mysql_query("SELECT * FROM search_persopac order by search_name ");
		$this->liste_item=array();
		$i=0;
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->liste_item[$i]=new stdClass();
				$this->liste_item[$i]->limitsearch=$r->search_limitsearch;
				$this->liste_item[$i]->id=$r->search_id;
				$this->liste_item[$i]->name=$r->search_name;
				$this->liste_item[$i]->shortname=$r->search_shortname;
				$this->liste_item[$i]->query=$r->search_query;
				$this->liste_item[$i]->human=$r->search_human;
				$this->liste_item[$i]->directlink=$r->search_directlink;	
				$this->liste_item[$i]->limitsearch=$r->search_limitsearch;	
				if(in_array($r->search_id,$this->selected_list)) {
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
 	    global $msg;
 	    return array(
 	        'selection' => $this->msg["selection_opac"],
 	        'directlink' => $msg['search_persopac_table_preflink'],
 	        'name' => $msg['search_persopac_table_name'],
 	        'shortname' => $msg['search_persopac_table_shortname'],
 	        'human' => $msg['search_persopac_table_humanquery']
 	    );
 	}
 	
 	protected function get_item_form($item, $class=''){
 	    $line = parent::get_item_form($item, $class);
 	    $line = str_replace('!!human!!', $item->human, $line);
 	    $line = str_replace('!!shortname!!', $item->shortname, $line);
 	    if($item->directlink) {
 	        $directlink="<img src='".get_url_icon('tick.gif')."' border='0'  hspace='0' class='align_middle'  class='bouton-nav' value='=' />";
 	    } else {
 	        $directlink="";
 	    }
        $line = str_replace('!!directlink!!', $directlink, $line);
 	    return $line;
 	}
 	
	public function save_form(){
		$req="delete FROM opac_filters where opac_filter_view_num=".$this->id_vue." and  opac_filter_path='".$this->path."' ";
		pmb_mysql_query($req);
		
		$param=array();
		$selected_list=array();
		for($i=0;$i<count($this->liste_item);$i++) {
			eval("global \$search_perso_selected_".$this->liste_item[$i]->id.";
			\$selected= \$search_perso_selected_".$this->liste_item[$i]->id.";");
			if($selected){
				$selected_list[]=$this->liste_item[$i]->id;
			}
		}
		$param["selected"]=$selected_list;
		$param=addslashes(serialize($param));		
		$req="insert into opac_filters set opac_filter_view_num=".$this->id_vue." ,  opac_filter_path='".$this->path."', opac_filter_param='$param' ";
		pmb_mysql_query($req);
		
		//Sauvegarde dans les recherches prédéfinies
		$req = "select search_id, search_opac_views_num from search_persopac";
		$res = pmb_mysql_query($req);
		if ($res) {
		    while($row = pmb_mysql_fetch_object($res)) {
		        $views_num = array();
		        //la recherche prédéfinie est sélectionnée..
		        if (in_array($row->search_id,$selected_list)) {
		            if ($row->search_opac_views_num != "") {
		                $views_num = explode(",", $row->search_opac_views_num);
		                if (count($views_num)) {
		                    if (!in_array($this->id_vue, $views_num)) {
		                        $views_num[] = $this->id_vue;
		                        $requete = "update search_persopac set search_opac_views_num='".implode(",", $views_num)."' where search_id=".$row->search_id;
		                        pmb_mysql_query($requete);
		                    }
		                }
		            }
		        } else {
		            if ($row->search_opac_views_num != "") {
		                $views_num = explode(",", $row->search_opac_views_num);
		                if (count($views_num)) {
		                    $key_exists = array_search($this->id_vue, $views_num);
		                    if ($key_exists !== false) {
		                        //la recherche prédéfinie ne doit plus être affichée dans la vue
		                        array_splice($views_num,$key_exists,1);
		                        $requete = "update search_persopac set search_opac_views_num='".implode(",", $views_num)."' where search_id=".$row->search_id;
		                        pmb_mysql_query($requete);
		                    }
		                }
		            } else {
		                //la recherche prédéfinie doit être affichée dans les autres vues sauf celle-ci..
		                $requete = "select opac_view_id from opac_views where opac_view_id <> ".$this->id_vue;
		                $resultat = pmb_mysql_query($requete);
		                $views_num[] = 0; // OPAC classique
		                while ($view = pmb_mysql_fetch_object($resultat)) {
		                    $views_num[] = $view->opac_view_id;
		                }
		                $requete = "update search_persopac set search_opac_views_num='".implode(",", $views_num)."' where search_id=".$row->search_id;
		                pmb_mysql_query($requete);
		            }
		        }
		    }
		}
	}	
	
}