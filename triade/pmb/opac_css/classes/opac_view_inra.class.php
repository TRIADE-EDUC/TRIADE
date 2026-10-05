<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_view_inra.class.php,v 1.5 2021/08/12 13:34:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/opac_view.class.php");

class opac_view_inra extends opac_view {
	public $corresp_centres=array(
		28=>5,
		34=>1,
		23=>4,
		35=>9,
		12=>11,
		44=>15,
		24=>10,
		21=>13,
		11=>19,
		30=>16,
		29=>7,
		22=>3,
		31=>17,
		13=>8,
		42=>6,
		32=>18,
		41=>2,
		33=>12,
		1=>14
	);
	
	// constructeur
	public function __construct($id=0,$id_empr=0) {	
		parent::__construct($id,$id_empr);
	}

    public function list_views(){
    	//A l'INRA, 2 vues de base par utilisateur...
    	//	- celle de son centre
    	//  - la vue nationale
       	
    	//Vue nationale par défaut
    	$this->opac_views_list[]=21;
    	$this->view_list_empr_default = 21;
    	
    	
		//Récupération du centre de l'emprunteur
		if($this->id_empr){
			$myQuery=pmb_mysql_query("select empr_custom_integer from empr_custom_values where empr_custom_origine=".$this->id_empr." and empr_custom_champ=15");
			if(pmb_mysql_num_rows($myQuery)){		
			  	$sql = "select opac_view_visible from opac_views where opac_view_id = ".$this->corresp_centres[pmb_mysql_result($myQuery,0,0)];
			  	$res = pmb_mysql_query($sql);
			  	if(pmb_mysql_num_rows($res)){
			  		if(pmb_mysql_result($res,0,0)>0){	
			  			$this->opac_views_list[]=$this->corresp_centres[pmb_mysql_result($myQuery,0,0)];
			  		}
			  	}
			}
		}
		
		//+ les vues publiques
		$myQuery = pmb_mysql_query("SELECT * FROM opac_views where opac_view_visible=1");
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->opac_views_list[]=$r->opac_view_id;
			}
		}	
		$this->opac_views_list = array_unique($this->opac_views_list);
    }
    
	public function get_list($name='', $value_selected=0) {
		global $charset;
		global $opac_url_base;

		if ($this->id_empr) $myQuery = pmb_mysql_query("SELECT * FROM opac_views left join opac_views_empr on (emprview_view_num=opac_view_id and emprview_empr_num=$this->id_empr) where opac_view_visible!=0 and opac_view_id in (".implode(",",$this->opac_views_list).") order by opac_view_name ");
		else $myQuery = pmb_mysql_query("SELECT * FROM opac_views where opac_view_visible=1 order by opac_view_name ");
		
		$selector = "
		<select name='$name' id='$name' onchange='document.location=\"".$opac_url_base."?opac_view=\"+this.value;'>";
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_object($myQuery))) {		
				$selector .= "
				<option value='".$r->opac_view_id."'";
				$r->opac_view_id == $value_selected ? $selector .= " selected='selected'>" : $selector .= ">";
		 		$selector .= htmlentities($r->opac_view_name,ENT_QUOTES, $charset)."</option>";
			}	
		}
		$selector .= "
		</select>";
		$this->selector=$selector;

		return $selector;
	}
} // fin définition classe