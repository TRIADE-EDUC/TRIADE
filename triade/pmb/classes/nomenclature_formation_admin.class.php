<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_formation_admin.class.php,v 1.6 2016-03-30 14:34:53 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/nomenclature_formation_admin.tpl.php");

class nomenclature_formation_admin {
	protected $id=0;
	public $info=array();
	
	
	public function __construct($id=0) {
		$this->id=$id+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $dbh;
		
		$this->info=array();
		$this->info['types']=array();
		if(!$this->id) return;
		$req="select * from nomenclature_formations where id_formation=". $this->id;	
		$resultat=pmb_mysql_query($req,$dbh);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_formation;	
			$this->info['name']= $r->formation_name;	
			$this->info['nature']= $r->formation_nature;
			$this->info['display']=  "<a href='./admin.php?categ=formation&sub=formation&action=form&id=".$r->id_formation."'>".$r->formation_name."</a>";$r->formation_name;
			$j=0;
			$req="select * from nomenclature_formations,nomenclature_types where type_formation_num=id_formation and id_formation=".$this->id." order by type_order";
			$res_types=pmb_mysql_query($req,$dbh);
			if (pmb_mysql_num_rows($res_types)) {
				while($r_type=pmb_mysql_fetch_object($res_types)){
					$this->info['types'][$r_type->id_type]['id']=$r_type->id_type;
					$this->info['types'][$r_type->id_type]['name']=$r_type->type_name;
					$this->info['types'][$r_type->id_type]['division']=$r_type->type_division;
					
					if($this->info['types_display'])$this->info['types_display'].="<br>";
					$this->info['types_display'].="<a href='./admin.php?categ=formation&sub=formation&action=type_form&id=".$this->id."&id_type=".$r_type->id_type."'>".$r_type->type_name."</a>";
					
					$j++;
				}
			}			
		}
	}
 
	public function get_form() {
		global $nomenclature_formation_form_tpl,$msg,$charset;		
		
		$tpl=$nomenclature_formation_form_tpl;
		if($this->id){
			$tpl=str_replace('!!msg_title!!',$msg['admin_nomenclature_formation_form_edit'],$tpl);
			$tpl=str_replace('!!delete!!',"<input type='button' class='bouton' value='".$msg['admin_nomenclature_formation_form_del']."'  onclick=\"document.getElementById('action').value='delete';this.form.submit();\"  />", $tpl);
			$name=$this->info['name'];
			$nature=$this->info['nature'];
		}else{ 
			$tpl=str_replace('!!msg_title!!',$msg['admin_nomenclature_formation_form_add'],$tpl);
			$tpl_objet="";
			$tpl=str_replace('!!delete!!',"",$tpl);
			$name="";
			$nature=0;
		}
		$tpl=str_replace('!!name!!',htmlentities($name, ENT_QUOTES, $charset),$tpl);
		if($nature){// voix
			$tpl=str_replace('!!nature_checked_0!!',"",$tpl);
			$tpl=str_replace('!!nature_checked_1!!',"checked",$tpl);			
		}else{// instruments
			$tpl=str_replace('!!nature_checked_0!!',"checked",$tpl);
			$tpl=str_replace('!!nature_checked_1!!',"",$tpl);			
		}
		$tpl=str_replace('!!id!!',$this->id,$tpl);

		$tpl_types="				
		<script type='text/javascript' src='./javascript/sorttable.js'></script>	
		<table class='sortable'>
			<tr>		
				<th>".$msg["admin_nomenclature_formation_type_form_name"]."
				</th> 
			</tr>				
		";	
		$flag_checked=0;
		foreach($this->info['types'] as $type){
			$tpl_type="
			<tr>
				<td><a href='./admin.php?categ=formation&sub=formation&action=type_form&id=".$this->id."&id_type=".$type['id']."'>".$type['name']."</a>
				</td>	
			</tr>					
			";
			$tpl_types.=$tpl_type;
		}
		$tpl_types.="
		</table>"; 			
		
		$tpl=str_replace('!!types!!',$tpl_types,$tpl);
		 
		return $tpl;
	}

	public function save() {
		global $dbh, $msg;
		global $name;
		global $nature;
		$fields="
			formation_name='".$name."',
			formation_nature='".$nature."'
		";		
		if(!$this->id){ // Ajout
			$requete="select max(formation_order) as ordre from nomenclature_formations";
			$resultat=pmb_mysql_query($requete, $dbh);
			$ordre_max=@pmb_mysql_result($resultat,0,0);
			$req="INSERT INTO nomenclature_formations SET $fields, formation_order=".($ordre_max+1);	
			pmb_mysql_query($req, $dbh);
			$this->id = pmb_mysql_insert_id($dbh);
		} else {
			$req="UPDATE nomenclature_formations SET $fields where id_formation=".$this->id;	
			pmb_mysql_query($req, $dbh);				
		}	
		$this->fetch_data();
		print display_notification($msg['account_types_success_saved']);
	}	
	
	public function delete() {
		global $dbh;
		$req="DELETE from nomenclature_types WHERE type_formation_num=".$this->id;
		pmb_mysql_query($req, $dbh);
		$req="DELETE from nomenclature_formations WHERE id_formation=".$this->id;
		pmb_mysql_query($req, $dbh);	
		$this->id=0;		
		$this->fetch_data();	
	}	

	public function get_type_form($id_type) {
		global $nomenclature_formation_type_form_tpl,$msg,$charset;
	
		$tpl=$nomenclature_formation_type_form_tpl;
		if($id_type){			
			$tpl=str_replace('!!msg_title!!',str_replace('!!formation_name!!',$this->info['display'],$msg['admin_nomenclature_formation_type_form_edit']),$tpl);
			$tpl=str_replace('!!delete!!',"<input type='button' class='bouton' value='".$msg['admin_nomenclature_formation_type_form_del']."'  onclick=\"document.getElementById('action').value='type_delete';this.form.submit();\"  />", $tpl);
			$name=$this->info['types'][$id_type]['name'];
			
			if($this->info['types'][$id_type]['division'])$checked="checked"; else $checked="";
			$tpl=str_replace('!!checked!!',$checked, $tpl);
		}else{
			$tpl=str_replace('!!msg_title!!',str_replace('!!formation_name!!',$this->info['display'],$msg['admin_nomenclature_formation_type_form_add']),$tpl);
			$tpl_objet="";
			$tpl=str_replace('!!delete!!',"",$tpl);
			$tpl=str_replace('!!checked!!',"", $tpl);
			$name="";
		}
		$tpl=str_replace('!!name!!',htmlentities($name, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!id!!',$this->id,$tpl);
		$tpl=str_replace('!!id_type!!',$id_type,$tpl);
					
		return $tpl;
	}
	
	public function type_save($id_type) {
		global $dbh;
		global $name;
		global $standard;
		
		$fields="
		type_formation_num='".$this->id."',
		type_name='".$name."'
		";
		if(!$id_type){ // Ajout
			
			$requete="select max(type_order) as ordre from nomenclature_types where type_formation_num=".$this->id;
			$resultat=pmb_mysql_query($requete, $dbh);
			$ordre_max=@pmb_mysql_result($resultat,0,0);			
			$req="INSERT INTO nomenclature_types SET $fields, type_order=".($ordre_max+1);
			pmb_mysql_query($req, $dbh);
			$id_type = pmb_mysql_insert_id($dbh);
		} else {
		$req="UPDATE nomenclature_types SET $fields where id_type=".$id_type;		
		pmb_mysql_query($req, $dbh);
		}
		$this->fetch_data();
	}
	
	public function type_delete($id_type) {
		global $dbh;
				
		$req="DELETE from nomenclature_types WHERE id_type=".$id_type;
		pmb_mysql_query($req, $dbh);
		$this->fetch_data();
	}
	
	
} //nomenclature_formation_type class end



class nomenclature_formation_admins {	
	public $info=array();
	
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $dbh;
		$this->info=array();
		$i=0;
		$req="select * from nomenclature_formations order by formation_order";
		$resultat=pmb_mysql_query($req,$dbh);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info[$i]= $formation=new nomenclature_formation_admin($r->id_formation);					
				$i++;
			}
		}
	}
				
	public function get_list() {
		global $nomenclature_formation_list_tpl,$nomenclature_formation_list_line_tpl,$msg;
		
		$tpl=$nomenclature_formation_list_tpl;
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info as $elt){
			$tpl_elt=$nomenclature_formation_list_line_tpl;
			if($odd_even=='odd')$odd_even="even"; else $odd_even="odd";		
			
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);	
			$tpl_elt=str_replace('!!name!!',$elt->info['name'], $tpl_elt);
			if($elt->info['nature']) // voix
				$tpl_elt=str_replace('!!nature!!',$msg['admin_nomenclature_formation_form_nature_voice'], $tpl_elt);
			else// instruments
				$tpl_elt=str_replace('!!nature!!',$msg['admin_nomenclature_formation_form_nature_instrument'], $tpl_elt);
			
			$tpl_elt=str_replace('!!types_display!!',$elt->info['types_display'], $tpl_elt);
			$tpl_elt=str_replace('!!id!!',$elt->info['id'], $tpl_elt);	
			$tpl_list.=$tpl_elt;	
		}
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		return $tpl;
	}	
	
	function order_up($id){
		global $dbh;	
	
		$requete="select formation_order from nomenclature_formations where id_formation=$id";
		$resultat=pmb_mysql_query($requete,$dbh);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select max(formation_order) as ordre from nomenclature_formations where formation_order<$ordre";
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre_max=@pmb_mysql_result($resultat,0,0);
		if ($ordre_max != '') {
			$requete="select id_formation from nomenclature_formations where formation_order=$ordre_max limit 1";
			$resultat=pmb_mysql_query($requete);
			$id_max=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_formations set formation_order='".$ordre_max."' where id_formation=$id";
			pmb_mysql_query($requete,$dbh);
			$requete="update nomenclature_formations set formation_order='".$ordre."' where id_formation=".$id_max;
			pmb_mysql_query($requete,$dbh);			
			$this->fetch_data();
		}
	}
	
	function order_down($id){
		global $dbh;
		$requete="select formation_order from nomenclature_formations where id_formation=$id";
		$resultat=pmb_mysql_query($requete);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select min(formation_order) as ordre from nomenclature_formations where formation_order>$ordre";
		$resultat=pmb_mysql_query($requete);
		$ordre_min=@pmb_mysql_result($resultat,0,0);
		if ($ordre_min) {
			$requete="select id_formation from nomenclature_formations where formation_order=$ordre_min limit 1";
			$resultat=pmb_mysql_query($requete);
			$id_min=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_formations set formation_order='".$ordre_min."' where id_formation=$id";
			pmb_mysql_query($requete);
			$requete="update nomenclature_formations set formation_order='".$ordre."' where id_formation=".$id_min;
			pmb_mysql_query($requete);			
			$this->fetch_data();
		}
	}

} // nomenclature_formation_admins class end
	


class nomenclature_formation_type_admins {
	protected $id=0; // id de la formation ou est rataché les types
	public $info=array();

	public function __construct($id) {
		$this->id=$id+0;
		$this->fetch_data();
	}

	protected function fetch_data() {
		global $dbh;
		$this->info=array();
		$this->info[0]=new nomenclature_formation_admin($this->id);
	}

	public function get_list() {
		global $nomenclature_formation_type_list_tpl,$nomenclature_formation_type_list_line_tpl,$msg;

		$tpl=$nomenclature_formation_type_list_tpl;
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info[0]->info['types'] as $elt){
			$tpl_elt=$nomenclature_formation_type_list_line_tpl;
			if($odd_even=='odd')$odd_even="even"; else $odd_even="odd";
			
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);
			$tpl_elt=str_replace('!!name!!',$elt['name'], $tpl_elt);
			$tpl_elt=str_replace('!!id_type!!',$elt['id'], $tpl_elt);
			$tpl_list.=$tpl_elt;
		}
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		$tpl=str_replace('!!id!!',$this->id, $tpl); // id formation
		$tpl=str_replace('!!formation_name!!',$this->info[0]->info['name'], $tpl); // id formation
		return $tpl;
	}
	
	function order_up($id){
		global $dbh;
	
		$requete="select type_order from nomenclature_types where id_type=$id";
		$resultat=pmb_mysql_query($requete,$dbh);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select max(type_order) as ordre from nomenclature_types where type_order<$ordre and type_formation_num=".$this->id;
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre_max=@pmb_mysql_result($resultat,0,0);
		if ($ordre_max != '') {
			$requete="select id_type from nomenclature_types where type_order=$ordre_max  and type_formation_num=".$this->id." limit 1";
			$resultat=pmb_mysql_query($requete, $dbh);
			$id_max=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_types set type_order='".$ordre_max."' where id_type=$id";
			pmb_mysql_query($requete,$dbh);
			$requete="update nomenclature_types set type_order='".$ordre."' where id_type=".$id_max;
			pmb_mysql_query($requete,$dbh);
			$this->fetch_data();
		}
	}
	
	function order_down($id){
		global $dbh;
		$requete="select type_order from nomenclature_types where id_type=$id";
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select min(type_order) as ordre from nomenclature_types where type_order>$ordre and type_formation_num=".$this->id;
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre_min=@pmb_mysql_result($resultat,0,0);
		if ($ordre_min) {
			$requete="select id_type from nomenclature_types where type_order=$ordre_min  and type_formation_num=".$this->id." limit 1";
			$resultat=pmb_mysql_query($requete, $dbh);
			$id_min=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_types set type_order='".$ordre_min."' where id_type=$id";
			pmb_mysql_query($requete, $dbh);
			$requete="update nomenclature_types set type_order='".$ordre."' where id_type=".$id_min;
			pmb_mysql_query($requete, $dbh);
			$this->fetch_data();
		}
	}
	
	 
} // nomenclature_formation_type_admins class end
