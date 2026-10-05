<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_order.class.php,v 1.4 2017/07/12 15:15:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class editions_state_order {
	public $elem;
	public $sort;
	
	public function __construct($elem,$params=""){
		$this->elem = $elem;
		if(!$params){
			$this->get_from_form();
		}else $this->set_params($params);
	}
	
	public function get_from_form(){
		$sort = $this->elem['id']."_sort";
		global ${$sort};
		$this->sort = stripslashes(${$sort});
	}
	
	public function get_form(){
		global $msg,$charset;
		$order_form= "
			<div class='row'>&nbsp;</div>
			<div class='row' >
				<div class='colonne2' id='orders_".$this->elem['id']."_drag' draggable='yes' dragtype='editionsstateorderslist'>
					<label for='".$this->elem['id']."_sort' style='cursor: pointer;'>".htmlentities($this->elem['label'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='colonne2'>
					<select name='".$this->elem['id']."_sort'>
						<option value='asc'".($this->sort == "asc" ? " selected='selected'" : "").">".htmlentities($msg['editions_state_order_'.$this->elem['type'].'_asc'],ENT_QUOTES,$charset)."</option>
						<option value='desc'".($this->sort == "desc" ? " selected='selected'" : "").">".htmlentities($msg['editions_state_order_'.$this->elem['type'].'_desc'],ENT_QUOTES,$charset)."</option>
					</select>
				</div>
			</div>";
		return $order_form;
	}	
	
	public function get_params(){
		return $this->sort;
	}
	
	protected function set_params($params){
		$this->sort = $params;
	}
	
	public function get_sql_filter(){
		if($this->sort){
			if($this->elem['field_alias']){
				$champ=$this->elem['field_alias'];
			}else{
				$champ=$this->elem['field'];
			}
			
			
			/*if($this->elem['field_join']){
				$champ=$this->elem['field_join'];
			}else{
				$champ=$this->elem['field'];
			}*/
			$sql_filter = $champ." ".$this->sort;	
		}
		return $sql_filter;
	} 
}