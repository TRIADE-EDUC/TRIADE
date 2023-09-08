<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: origin.class.php,v 1.4 2017-06-30 14:08:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/*
 * Classe de gestion d'une origine...
 */
class origin {
	public $id;			// Identifiant de l'origine
	public $type;			// Type associé à l'origine
	public $name;			// Nom de l'origine
	public $country;		// Pays d'orgine
	public $diffusible;	// Booléen pour définir si les éléments de l'origine sont exportables...
	
	
	public function __construct($id=0,$type="authorities"){
		$this->type = $type;
		$this->id = $id+0;
		if($this->id){
			$this->_fetch_data();
		}else{
			$this->name = "";
			$this->country = "";
			$this->diffusible = true;
		}
	}
	
	private function _fetch_data(){
		$query = "select * from origin_".$this->type." where id_origin_".$this->type." = ".$this->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_assoc($result);
			$this->name = $row['origin_'.$this->type."_name"];
			$this->country = $row['origin_'.$this->type."_country"];
			$this->diffusible = ($row['origin_'.$this->type."_diffusible"]==1 ? true : false);
		}
	}
	
	public function is_diffusible(){
		return $this->diffusible;
	}
	
	public function save(){
		if($this->name != ""){
			if($this->id){
				$query = "update origin_".$this->type ." set ";
				$where = "where id_origin_".$this->type." = ".$this->id;	
			}else{
				$query = "insert into origin_".$this->type ." set ";
				$where = "";
			}
			$query .= "origin_".$this->type."_name = '".addslashes($this->name)."',";
			$query .= "origin_".$this->type."_country = '".addslashes($this->country)."',";
			$query .= "origin_".$this->type."_diffusible = '".($this->is_diffusible() ? 1:0)."' ";
			$result = pmb_mysql_query($query.$where);
			if($result) return true;
			else return false;
		}
		return false;
	}
	
	public function delete(){
		if($this->id < 2){
			// le catalogue interne et la BnF, c'est pas négociable !
			return false;
		}else{
			//TODO check utilisation
			$query = "delete from origin_".$this->type." where id_origin_".$this->type." = ".$this->id;
			$result = pmb_mysql_query($query);
			if($result) return true;
		}
		return false;
	} 
	
	public function show_form(){
		global $msg,$charset;
		global $origin_form,$current_module;
		
		$form = str_replace("!!type!!",$this->type,$origin_form);
		$form = str_replace("!!id!!",$this->id,$form);
		$title = $this->id!= 0 ? $msg['authorities_origin_add']:$msg['authorities_origin_modif'];
		$form = str_replace("!!title!!",htmlentities($title,ENT_QUOTES,$charset),$form);
		$form = str_replace("!!origin_name!!",$this->name,$form);
		$form = str_replace("!!origin_country!!",$this->country,$form);
		$form = str_replace("!!checked!!",$this->diffusible ? "checked='checked'" : "",$form);
		print $form;
	}
	
	public function show_tab_row(){
		global $msg,$charset;
		
		$row = "<tr style='cursor:pointer;' onmouseover='this.className=\"surbrillance\"' onmouseout=\"this.className='even'\" onmousedown=\"document.location='./admin.php?categ=authorities&sub=origins&action=modif&id=".$this->id."';\">
					<td>".htmlentities($this->name,ENT_QUOTES,$charset)."</td>
					<td>".htmlentities($this->country,ENT_QUOTES,$charset)."</td>
					<td>".htmlentities(($this->diffusible ? $msg['orinot_diffusable_oui'] : $msg['orinot_diffusable_non']),ENT_QUOTES,$charset)."</td>
				</tr>";
		return $row;
	}
	
	public static function get_list($type="authorities"){
		$list = array();
		$query = "select id_origin_".$type." from origin_".$type;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_assoc($result)){
				$list[]=$row['id_origin_'.$type];
			}
		}
		return $list;
	}
	
	public static function gen_combo_box($type="authorities",$name="authorities_origin"){
		global $msg,$charset;
		
		$query = "select id_origin_".$type.",origin_".$type."_name from origin_".$type;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$selector = "
			<select name='".$name."'>";	
			while ($row = pmb_mysql_fetch_assoc($result)){
				$selector.= " 
				<option value='".$row['id_origin_'.$type]."'>".htmlentities($row['origin_'.$type.'_name'],ENT_QUOTES,$charset)."</option>";
			}
			$selector .= "
			</select>";
		}
		return $selector;
	}
	
	public static function import($type="authorities",$origin){
		if($origin!=""){
			$query = "select id_origin_".$type." from origin_".$type." where  origin_".$type."_name = '".$origin['origin']."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				return pmb_mysql_result($result,0,0);
			}else{
				$query = "insert into origin_".$type." set 
					origin_".$type."_name = '".$origin['origin']."',
					origin_".$type."_country = '".$origin['country']."'";
				$result = pmb_mysql_query($query);
				if($result) return pmb_mysql_insert_id();
			}
		}
		return false;
		
	}
}