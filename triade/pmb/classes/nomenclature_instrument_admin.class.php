<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instrument_admin.class.php,v 1.14 2017-11-21 12:00:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/nomenclature_instrument_admin.tpl.php");

class nomenclature_instrument_admin {
	protected $id=0;
	public $info=array();
	
	
	public function __construct($id=0) {
		$this->id=$id+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $dbh;
		
		$this->info=array();
		if(!$this->id) return;
		
		$req="select * from nomenclature_instruments where id_instrument=". $this->id;	
		$resultat=pmb_mysql_query($req,$dbh);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_instrument;		
			$this->info['code']= $r->instrument_code;
			$this->info['name']= $r->instrument_name;
			$this->info['standard']= $r->instrument_standard;
			$this->info['musicstand_num']= $r->instrument_musicstand_num;
			$this->info['musicstand']=array();
			
			$req="select * from nomenclature_families,nomenclature_musicstands where musicstand_famille_num=id_family and id_musicstand=".$this->info['musicstand_num'];
			$res_musicstands=pmb_mysql_query($req,$dbh);
			if (pmb_mysql_num_rows($res_musicstands)) {
				if($r_musicstand=pmb_mysql_fetch_object($res_musicstands)){
					$this->info['musicstand']['id']=$r_musicstand->id_musicstand;
					$this->info['musicstand']['name']=$r_musicstand->musicstand_name;
					$this->info['musicstand']['division']=$r_musicstand->musicstand_division;	
					$this->info['musicstand']['display']="<a href='./admin.php?categ=family&sub=family&action=musicstand_form&id=".$r_musicstand->id_family."&id_musicstand=".$r_musicstand->id_musicstand."'>".$r_musicstand->musicstand_name."</a>";
													
					$req="select * from nomenclature_families where id_family=". $r_musicstand->id_family;
					$res_family=pmb_mysql_query($req,$dbh);
					if (pmb_mysql_num_rows($res_family)) {
						$r_family=pmb_mysql_fetch_object($res_family);
						$this->info['musicstand']['family']['id']= $r_family->id_family;
						$this->info['musicstand']['family']['name']= $r_family->family_name;
						$this->info['musicstand']['family']['display']= "<a href='./admin.php?categ=family&sub=family&action=form&id=".$r_musicstand->id_family."'>".$r_family->family_name."</a>";	
					}					
				}
			}
		}
	}
 
	public function get_form() {
		global $nomenclature_instrument_form_tpl,$msg,$charset;		
		global $msg;
		
		$tpl=$nomenclature_instrument_form_tpl;
		if($this->id){
			$tpl=str_replace('!!msg_title!!',$msg['admin_nomenclature_instrument_form_edit'],$tpl);
			$tpl=str_replace('!!delete!!',"<input type='button' class='bouton' value='".$msg['admin_nomenclature_instrument_form_del']."'  onclick=\"document.getElementById('action').value='delete';this.form.submit();\"  />", $tpl);
			$name=$this->info['name'];
			$code=$this->info['code'];	
			if($this->info['standard'])$checked="checked"; else $checked="";
		}else{ 
			$tpl=str_replace('!!msg_title!!',$msg['admin_nomenclature_instrument_form_add'],$tpl);
			$tpl_objet="";
			$tpl=str_replace('!!delete!!',"",$tpl);
			$name="";
			$code="";
			$checked="";
		}
		$tpl=str_replace('!!name!!',htmlentities($name, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!code!!',htmlentities($code, ENT_QUOTES, $charset),$tpl);		
		$tpl=str_replace('!!checked!!',$checked, $tpl);

		$req="select id_musicstand, concat(musicstand_name,' ( ',family_name,' )')as label from nomenclature_musicstands,nomenclature_families where musicstand_famille_num=id_family order by musicstand_name";
		$musicstand=gen_liste($req, "id_musicstand", "label", "id_musicstand", "", $this->info['musicstand']['id'], 
				0,$msg["admin_nomenclature_instrument_form_musicstand_no"], 0, $msg["admin_nomenclature_instrument_form_musicstand_no_sel"]);		
		$tpl=str_replace('!!musicstand!!',$musicstand,$tpl);
		$tpl=str_replace('!!id!!',$this->id,$tpl);
		 
		return $tpl;
	}
	
	public function save_form() {
		
	}
	
	public function save() {
		global $dbh;
		global $msg;
		global $name;
		global $code;
		global $id_musicstand;
		global $standard;
		global $force;
		
		if($id_musicstand && $standard){
			if($this->id) $restrict=" and id_instrument!=".$this->id;
			$req="select * from nomenclature_instruments where instrument_musicstand_num=$id_musicstand and instrument_standard=1 $restrict ";
			$res_instruments=pmb_mysql_query($req,$dbh);
			$count_instrument=0;
			if (pmb_mysql_num_rows($res_instruments)) {
				if($r_instrument=pmb_mysql_fetch_object($res_instruments)){		
					if($force){
						$req="UPDATE nomenclature_instruments SET instrument_standard=0 where id_instrument=".$r_instrument->id_instrument;
						pmb_mysql_query($req, $dbh);
					}else return "
					<br />
					<div class='erreur'>$msg[540]</div>
					<div class='row'>
						<div class='colonne10'>
							<img src='".get_url_icon('error.gif')."' class='align_left'>
						</div>
						<div class='colonne80'>
							<strong>".$msg["admin_nomenclature_instrument_form_musicstand_standard_error"].$r_instrument->instrument_code." ( ".$r_instrument->instrument_name ." )</strong>
						</div>
					</div>					
					<div class='row'>
						<form class='form-$current_module' name='dummy' method=\"post\" action='./admin.php?categ=instrument&sub=instrument&action=save&force=1'>				
							<input type='hidden' name='code' value='$code'/>
							<input type='hidden' name='name' value='$name'/>
							<input type='hidden' name='standard' value='$standard'/>
							<input type='hidden' name='id_musicstand' value='$id_musicstand'/>	
							<input type='hidden' name='id' value='".$this->id."'/>		
							<input type='submit' name='ok' class='bouton' value='". $msg["admin_nomenclature_instrument_form_musicstand_standard_force"] ."' >
							<input type='button' name='retour' class='bouton' value=' $msg[76] ' onClick=\"history.go(-1); return false;\"'>
						</form>								
						<script type='text/javascript'>
							document.forms['dummy'].elements['ok'].focus();
						</script>
					</div>
					";	
				}
			}
		}
		$fields="
			instrument_code='".$code."',
			instrument_name='".$name."',
			instrument_musicstand_num='".$id_musicstand."',
			instrument_standard='".$standard."'
		";		
		if(!$this->id){ // Ajout
			$req="INSERT INTO nomenclature_instruments SET $fields ";	
			pmb_mysql_query($req, $dbh);
			$this->id = pmb_mysql_insert_id($dbh);
			print display_notification($msg['account_types_success_saved']);
		} else {
			$req="UPDATE nomenclature_instruments SET $fields where id_instrument=".$this->id;
			pmb_mysql_query($req, $dbh);
			print display_notification($msg['account_types_success_saved']);
		}	
		$this->fetch_data();
	}	
	
	public function delete() {
		global $dbh;
		$req="DELETE from nomenclature_instruments WHERE id_instrument=".$this->id;
		pmb_mysql_query($req, $dbh);	
		$this->id=0;		
		$this->fetch_data();	
	}	

} //nomenclature_instrument_admin class end



class nomenclature_instrument_admins {	
	public $info=array();
	
	public function __construct() {
		$this->fetch_data();
	}
	
	function fetch_data() {
		global $dbh;
		$this->info=array();
		$i=0;
		$req="select * from nomenclature_instruments order by instrument_code";
		$resultat=pmb_mysql_query($req,$dbh);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info[$i]= $instrument=new nomenclature_instrument_admin($r->id_instrument);					
				$i++;
			}
		}
	}
				
	public function get_list() {
		global $nomenclature_instrument_list_tpl,$nomenclature_instrument_list_line_tpl,$msg;
		
		$tpl=$nomenclature_instrument_list_tpl;
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info as $elt){
			$tpl_elt=$nomenclature_instrument_list_line_tpl;
			if($odd_even=='odd')$odd_even="even"; else $odd_even="odd";		
			
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);	
			$tpl_elt=str_replace('!!name!!',$elt->info['name'], $tpl_elt);
			$tpl_elt=str_replace('!!code!!',$elt->info['code'], $tpl_elt);
			$musicstand="";		
			$family="";
			if($elt->info['musicstand']['display']){
				$musicstand=$elt->info['musicstand']['display'];
				$family=$elt->info['musicstand']['family']['display'];
			}
			$tpl_elt=str_replace('!!musicstand!!',$musicstand, $tpl_elt);	
			$tpl_elt=str_replace('!!family!!',$family, $tpl_elt);			
			if($elt->info['standard'])$standard="x"; else $standard="";
			$tpl_elt=str_replace('!!standard!!',$standard, $tpl_elt);
			$tpl_elt=str_replace('!!id!!',$elt->info['id'], $tpl_elt);	
			$tpl_list.=$tpl_elt;	
		}
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		return $tpl;
	}	

    	
} // nomenclature_instrument_admins class end
	
