<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_tpl_print_fields.class.php,v 1.7 2023/05/04 09:22:14 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/serialcirc_print_fields.class.php");

class serialcirc_tpl_print_fields extends serialcirc_print_fields {
	
	public $p_perso;

	public function __construct($id_tpl_serialcirc=0) {
		$this->id=intval($id_tpl_serialcirc);
		$this->fetch_data();
	}
	
	public function fetch_data() {
		$this->p_perso = new parametres_perso("empr");
		$this->circ_tpl=array();
		$requete="select * from serialcirc_tpl where serialcirctpl_id=".$this->id ;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			if($r->serialcirctpl_tpl) {
				$this->circ_tpl=stripslashes_array(unserialize($r->serialcirctpl_tpl));
			}
		}	
	}

	public function save_form(){
		$this->get_fields();
		$req="update serialcirc_tpl set serialcirctpl_tpl='".addslashes(serialize($this->circ_tpl))."' where serialcirctpl_id=".$this->id ;
		pmb_mysql_query($req);
	}
	
	public function up_order($tablo){	
		$liste = explode(",",$tablo);
		$new_circ_tpl=array();
		$this->get_fields();
		for($i=0;$i<count($liste);$i++){			
			$new_circ_tpl[]=$this->circ_tpl[$liste[$i]];
		}
		$this->circ_tpl = $new_circ_tpl;
	}
	
	public function add_field(){
		global $select_field;
		
		$this->get_fields();
		$cpt=count($this->circ_tpl);
		$data=explode('_',$select_field);
		$this->circ_tpl[$cpt]['type']=$data[0];
		$this->circ_tpl[$cpt]['id']=(isset($data[1]) ? $data[1] : '');
	}	
	
	public function del_field(){
		global $index;
		global $order_tpl;
		
		$liste = explode(",",$order_tpl);
		$this->get_fields();
		for($i=0;$i<count($liste);$i++){
			if ($liste[$i] == $index) array_splice($this->circ_tpl,$i,1);			
		}
	}
	
	public function get_fields(){
		global $field_list;
	
		$this->circ_tpl=array();
		$cpt=0;
		if(!$field_list)$field_list=array();
		foreach($field_list as $field){
			$data=explode('_',$field);
			$this->circ_tpl[$cpt]['type']=$data[0];
			$this->circ_tpl[$cpt]['id']=$data[2];
			$val_label=$field."_label";
			global ${$val_label};
			$this->circ_tpl[$cpt]['label']=  ${$val_label};
			$cpt++;
		}
	}
	
} //serialcirc class end