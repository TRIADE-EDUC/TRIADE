<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_external_filter.class.php,v 1.4 2017-10-27 13:39:11 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path,$include_path;

class facettes_external_filter {    
    public function __construct($id_vue,$local_msg) {
    	$this->id_vue=$id_vue;
    	$this->path="facettes_external";
    	$this->msg=$local_msg;
    	$this->fetch_data();    	   	
    }
    
    public function fetch_data() {
		global $dbh;
			
		$this->selected_list=array();
		$req="SELECT * FROM opac_filters where opac_filter_view_num=".$this->id_vue." and  opac_filter_path='".$this->path."' ";
		$myQuery = pmb_mysql_query($req, $dbh);
		if(pmb_mysql_num_rows($myQuery)){
			$r=pmb_mysql_fetch_object($myQuery);
			$param=unserialize($r->opac_filter_param);
			$this->selected_list=$param["selected"];
		}				
		$myQuery = pmb_mysql_query("SELECT * FROM facettes_external order by facette_name ", $dbh);
		$this->liste_item=array();
		$link="";
		$i=0;
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->liste_item[$i]=new stdClass();
				$this->liste_item[$i]->id=$r->id_facette ;
				$this->liste_item[$i]->name=$r->facette_name ;
				if(in_array($r->id_facette ,$this->selected_list))	$this->liste_item[$i]->selected=1;
				else $this->liste_item[$i]->selected=0;				
				$i++;			
			}	
		}
		return true;
 	}
       
	public function get_all_elements(){	
		return $this->ids;
	}
    	
	public function get_elements(){		
		return $this->all_ids;
	}		
	
	public function get_form(){
		global $msg;
		global $tpl_liste_item_tableau,$tpl_liste_item_tableau_ligne;
		
		global $class_path,$base_path,$include_path;

		require_once($base_path."/admin/opac/opac_view/filters/facettes_external_filter/facettes_external_filter.tpl.php");
		
		// liste des lien de recherche directe
		$liste="";
		// pour toute les recherche de l'utilisateur
		$liste_id = array();
		
		for($i=0;$i<count($this->liste_item);$i++) {
			$liste_id[] = 'facettes_external_selected_'.$this->liste_item[$i]->id;
			if ($i % 2) $pair_impair = "even"; else $pair_impair = "odd";			
	        $td_javascript=" ";
	        $tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
	
	        $line = str_replace('!!td_javascript!!',$td_javascript , $tpl_liste_item_tableau_ligne);
	        $line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
	        $line = str_replace('!!pair_impair!!',$pair_impair , $line);
	
			$line =str_replace('!!id!!', $this->liste_item[$i]->id, $line);
			if($this->liste_item[$i]->selected) $checked="checked";else $checked="";			
			$line =str_replace('!!selected!!', $checked, $line);
			$line = str_replace('!!name!!', $this->liste_item[$i]->name, $line);
			
			$liste.=$line;
		}
		$tpl_liste_item_tableau = str_replace('!!lignes_tableau!!',$liste , $tpl_liste_item_tableau);
		
		if (count($liste_id)) {
			$tpl_liste_item_tableau .= "<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(\"".implode("|",$liste_id)."\",1);'>";
			$tpl_liste_item_tableau .= "<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(\"".implode("|",$liste_id)."\",0);'>";
		}
		
		return $tpl_liste_item_tableau;
	}	
	
	public function save_form(){
		global $dbh;

		$req="delete FROM opac_filters where opac_filter_view_num=".$this->id_vue." and  opac_filter_path='".$this->path."' ";
		$myQuery = pmb_mysql_query($req, $dbh);
		
		$param=array();
		$selected_list=array();
		for($i=0;$i<count($this->liste_item);$i++) {
			eval("global \$facettes_external_selected_".$this->liste_item[$i]->id.";
			\$selected= \$facettes_external_selected_".$this->liste_item[$i]->id.";");
			if($selected){
				$selected_list[]=$this->liste_item[$i]->id;
			}
		}
		$param["selected"]=$selected_list;
		$param=addslashes(serialize($param));		
		$req="insert into opac_filters set opac_filter_view_num=".$this->id_vue." ,  opac_filter_path='".$this->path."', opac_filter_param='$param' ";
		$myQuery = pmb_mysql_query($req, $dbh);
		
		//sauvegarde dans les facettes externes..
		$req = "select id_facette, facette_opac_views_num from facettes_external";
		$res = pmb_mysql_query($req,$dbh);
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
								pmb_mysql_query($requete,$dbh);
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
								pmb_mysql_query($requete,$dbh);
							}
						}
					} else {
						//la facette doit être affichée dans les autres vues sauf celle-ci..
						$requete = "select opac_view_id from opac_views where opac_view_id <> ".$this->id_vue;
						$resultat = pmb_mysql_query($requete,$dbh);
						$views_num[] = 0; // OPAC classique
						while ($view = pmb_mysql_fetch_object($resultat)) {
							$views_num[] = $view->opac_view_id;
						}
						$requete = "update facettes_external set facette_opac_views_num='".implode(",", $views_num)."' where id_facette=".$row->id_facette;
						pmb_mysql_query($requete,$dbh);
					}
				}
			}
		}
	}	
	
}