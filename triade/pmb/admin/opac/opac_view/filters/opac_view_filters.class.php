<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_view_filters.class.php,v 1.2.10.2 2025/03/14 13:27:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class opac_view_filters {

	public $id_vue;
	public $path;
	public $msg;
	public $liste_item;
	public $selected_list;
	public $ids;
	public $all_ids;

   public function __construct($id_vue,$local_msg) {
    	$this->id_vue=$id_vue;
    	$this->_init_path();
    	$this->msg=$local_msg;
    	$this->fetch_data();
    }

    protected function _init_path() {
    	$this->path = '';
    }

    public function fetch_data() {
		$this->selected_list=array();
		$req="SELECT * FROM opac_filters where opac_filter_view_num=".$this->id_vue." and  opac_filter_path='".$this->path."' ";
		$myQuery = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($myQuery)){
			$r=pmb_mysql_fetch_object($myQuery);
			$param=unserialize($r->opac_filter_param);
			$this->selected_list=$param["selected"];
		}
    }

	public function get_all_elements(){
		return $this->ids;
	}

	public function get_elements(){
		return $this->all_ids;
	}

	protected function get_filename_template() {
	    global $base_path;
	    return $base_path."/admin/opac/opac_view/filters/".$this->path."/".$this->path.".tpl.php";
	}
	
	public function get_available_columns() {
	   return array();
	}
	
	protected function get_tpl_liste_item_tableau() {
	    global $tpl_liste_item_tableau;
	    
	    $tpl_liste_item_tableau = '';
	    require($this->get_filename_template());
	    if (empty($tpl_liste_item_tableau)) {
	        $tpl_liste_item_tableau = "
            <table>
                <tr>";
	        $available_columns = $this->get_available_columns();
	        foreach ($available_columns as $name) {
	            $tpl_liste_item_tableau .= "<th>".$name."</th>";
	        }
	        $tpl_liste_item_tableau .= "
                </tr>
                !!lignes_tableau!!
            </table>";
	    }
	    return $tpl_liste_item_tableau;
	}
	
	protected function get_prefix_selection() {
	    return $this->path;
	}
	
	protected function get_tpl_liste_item_ligne($property) {
	    switch ($property) {
	        case 'selection':
	            return "<td><input value='1' id='".$this->get_prefix_selection()."_selected_!!id!!' name='".$this->get_prefix_selection()."_selected_!!id!!' !!selected!! type='checkbox' aria-labelledby='".$this->get_prefix_selection()."_selected_accessibility_label_!!id!!'></td>";
	        case 'name':
	            return "<td !!td_javascript!! id='".$this->get_prefix_selection()."_selected_accessibility_label_!!id!!'>!!".$property."!!</td>";
	        default:
	            return "<td !!td_javascript!! >!!".$property."!!</td>";
	    }
	}
	
	protected function get_tpl_liste_item_tableau_ligne() {
	    global $tpl_liste_item_tableau_ligne;
	    
	    $tpl_liste_item_tableau_ligne = '';
	    require($this->get_filename_template());
	    if (empty($tpl_liste_item_tableau_ligne)) {
	        $tpl_liste_item_tableau_ligne = "<tr class='!!pair_impair!!' '!!tr_surbrillance!!' >";
	        $available_columns = $this->get_available_columns();
	        foreach ($available_columns as $property=>$name) {
	            $tpl_liste_item_tableau_ligne .= $this->get_tpl_liste_item_ligne($property);
	        }
            $tpl_liste_item_tableau_ligne .= "</tr>"; 
	    }
	    return $tpl_liste_item_tableau_ligne;
	}
	
	protected function get_item_form($item, $class=''){
	    $td_javascript=" ";
	    $tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$class."'\" ";
	    
	    $tpl_liste_item_tableau_ligne = $this->get_tpl_liste_item_tableau_ligne();
	    $line = str_replace('!!td_javascript!!',$td_javascript , $tpl_liste_item_tableau_ligne);
	    $line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
	    $line = str_replace('!!pair_impair!!',$class , $line);
	    
	    $line =str_replace('!!id!!', $item->id, $line);
	    $line =str_replace('!!selected!!', ($item->selected ? "checked" : ""), $line);
	    $line = str_replace('!!name!!', $item->name, $line);
	    if(isset($item->comment)) {
	        $line = str_replace('!!comment!!', $item->comment, $line);
	    }
	    return $line;
	}
	
	public function get_form(){
		global $msg;
		
		$tpl_liste_item_tableau = $this->get_tpl_liste_item_tableau();
		
		// liste des lien de recherche directe
		$liste="";
		// pour toute les recherche de l'utilisateur
		$liste_id = array();

		for($i=0;$i<count($this->liste_item);$i++) {
			$liste_id[] = $this->path.'_selected_'.$this->liste_item[$i]->id;
			if ($i % 2) {
			    $pair_impair = "even";
			} else {
			    $pair_impair = "odd";
			}
			$liste.= $this->get_item_form($this->liste_item[$i], $pair_impair);
		}
		$tableau = str_replace('!!lignes_tableau!!',$liste , $tpl_liste_item_tableau);

		if (count($liste_id)) {
		    $tableau .= "<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(\"".implode("|",$liste_id)."\",1);'>";
		    $tableau .= "<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(\"".implode("|",$liste_id)."\",0);'>";
		}
		return $tableau;
	}

	public function save_form(){
		$req="delete FROM opac_filters where opac_filter_view_num=".$this->id_vue." and  opac_filter_path='".$this->path."' ";
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
		$req="insert into opac_filters set opac_filter_view_num=".$this->id_vue." ,  opac_filter_path='".$this->path."', opac_filter_param='$param' ";
		pmb_mysql_query($req);
	}
}