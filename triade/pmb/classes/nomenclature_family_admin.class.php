<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_family_admin.class.php,v 1.10 2017-04-26 10:20:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/nomenclature_family_admin.tpl.php");

class nomenclature_family_admin {
	protected $id=0;
	public $info=array();
	
	
	public function __construct($id=0) {
		$this->id=$id+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $dbh;
		
		$this->info=array();
		$this->info['musicstands']=array();
		if(!$this->id) return;
		$req="select * from nomenclature_families where id_family=". $this->id;	
		$resultat=pmb_mysql_query($req,$dbh);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_family;	
			$this->info['name']= $r->family_name;
			$this->info['display']=  "<a href='./admin.php?categ=family&sub=family&action=form&id=".$r->id_family."'>".$r->family_name."</a>";$r->family_name;
			$this->info['musicstands_display']= "";
			$j=0;
			$req="select * from nomenclature_families,nomenclature_musicstands where musicstand_famille_num=id_family and id_family=".$this->id." order by musicstand_order";
			$res_musicstands=pmb_mysql_query($req,$dbh);
			if (pmb_mysql_num_rows($res_musicstands)) {
				while($r_musicstand=pmb_mysql_fetch_object($res_musicstands)){
					$this->info['musicstands'][$r_musicstand->id_musicstand]['id']=$r_musicstand->id_musicstand;
					$this->info['musicstands'][$r_musicstand->id_musicstand]['name']=$r_musicstand->musicstand_name;
					$this->info['musicstands'][$r_musicstand->id_musicstand]['division']=$r_musicstand->musicstand_division;
					$this->info['musicstands'][$r_musicstand->id_musicstand]['workshop']=$r_musicstand->musicstand_workshop;
					$this->info['musicstands'][$r_musicstand->id_musicstand]['instruments']=array();
					
					if($this->info['musicstands_display'])$this->info['musicstands_display'].="<br>";
					$this->info['musicstands_display'].="<a href='./admin.php?categ=family&sub=family&action=musicstand_form&id=".$this->id."&id_musicstand=".$r_musicstand->id_musicstand."'>".$r_musicstand->musicstand_name."</a>";
					
					$this->info['musicstands'][$r_musicstand->id_musicstand]['instruments_display'] = '';
					$req="select * from nomenclature_instruments where instrument_musicstand_num=". $r_musicstand->id_musicstand." order by instrument_code";	
					$res_instruments=pmb_mysql_query($req,$dbh);	
					$count_instrument=0;
					if (pmb_mysql_num_rows($res_instruments)) {
						while($r_instrument=pmb_mysql_fetch_object($res_instruments)){
							$this->info['musicstands'][$r_musicstand->id_musicstand]['instruments'][$count_instrument]['id']= $r_instrument->id_instrument;
							$this->info['musicstands'][$r_musicstand->id_musicstand]['instruments'][$count_instrument]['code']= $r_instrument->instrument_code;
							$this->info['musicstands'][$r_musicstand->id_musicstand]['instruments'][$count_instrument]['name']= $r_instrument->instrument_name;
							$this->info['musicstands'][$r_musicstand->id_musicstand]['instruments'][$count_instrument]['standard']= $r_instrument->instrument_standard;
							
							if($r_instrument->instrument_standard)$standard="*";else $standard="";
							if($this->info['musicstands'][$r_musicstand->id_musicstand]['instruments_display'])$this->info['musicstands'][$r_musicstand->id_musicstand]['instruments_display'].="<br>";
							$this->info['musicstands'][$r_musicstand->id_musicstand]['instruments_display'].="<a href='./admin.php?categ=instrument&sub=instrument&action=form&id=".$r_instrument->id_instrument."'>".$r_instrument->instrument_code." ( ".$r_instrument->instrument_name." )</a> $standard";
															
							$count_instrument++;
						}
					}
					$j++;
				}
			}			
		}
	}
 
	public function get_form() {
		global $nomenclature_family_form_tpl,$msg,$charset;		
		
		$tpl=$nomenclature_family_form_tpl;
		if($this->id){
			$tpl=str_replace('!!msg_title!!',$msg['admin_nomenclature_family_form_edit'],$tpl);
			$tpl=str_replace('!!delete!!',"<input type='button' class='bouton' value='".$msg['admin_nomenclature_family_form_del']."'  onclick=\"document.getElementById('action').value='delete';this.form.submit();\"  />", $tpl);
			$name=$this->info['name'];
		}else{ 
			$tpl=str_replace('!!msg_title!!',$msg['admin_nomenclature_family_form_add'],$tpl);
			$tpl_objet="";
			$tpl=str_replace('!!delete!!',"",$tpl);
			$name="";
		}
		$tpl=str_replace('!!name!!',htmlentities($name, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!id!!',$this->id,$tpl);

		$tpl_musicstands="				
		<script type='text/javascript' src='./javascript/sorttable.js'></script>	
		<table class='sortable'>
			<tr>		
				<th>".$msg["admin_nomenclature_family_musicstand_form_name"]."
				</th> 	
				<th>".$msg["admin_nomenclature_family_musicstand_form_instruments"]."
				</th> 
			</tr>				
		";	
		$flag_checked=0;
		foreach($this->info['musicstands'] as $musicstand){
			$tpl_musicstand="
			<tr>
				<td style=\"cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_form&id=".$this->id."&id_musicstand=".$musicstand['id']."';\">
					<a href='./admin.php?categ=family&sub=family&action=musicstand_form&id=".$this->id."&id_musicstand=".$musicstand['id']."'>".$musicstand['name']."</a>
				</td>	
				<td style=\"cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_form&id=".$this->id."&id_musicstand=".$musicstand['id']."';\">
					".$musicstand['instruments_display']."
				</td>	
			</tr>					
			";
			$tpl_musicstands.=$tpl_musicstand;
		}
		$tpl_musicstands.="
		</table>"; 	
		
		
		
		$tpl=str_replace('!!musicstands!!',$tpl_musicstands,$tpl);
		 
		return $tpl;
	}

	public function save() {
		global $dbh, $msg;
		global $name;
		
		$notice_onglet+=0;		
		$fields="
			family_name='".$name."'
		";		
		if(!$this->id){ // Ajout
			$requete="select max(family_order) as ordre from nomenclature_families";
			$resultat=pmb_mysql_query($requete, $dbh);
			$ordre_max=@pmb_mysql_result($resultat,0,0);
			$req="INSERT INTO nomenclature_families SET $fields, family_order=".($ordre_max+1);	
			pmb_mysql_query($req, $dbh);
			$this->id = pmb_mysql_insert_id($dbh);
		} else {
			$req="UPDATE nomenclature_families SET $fields where id_family=".$this->id;	
			pmb_mysql_query($req, $dbh);				
		}	
		$this->fetch_data();
		print display_notification($msg['account_types_success_saved']);
	}	
	
	public function delete() {
		global $dbh;
		$req="DELETE from nomenclature_musicstands WHERE musicstand_famille_num=".$this->id;
		pmb_mysql_query($req, $dbh);
		$req="DELETE from nomenclature_families WHERE id_family=".$this->id;
		pmb_mysql_query($req, $dbh);	
		$this->id=0;		
		$this->fetch_data();	
	}	

	public function get_musicstand_form($id_musicstand) {
		global $nomenclature_family_musicstand_form_tpl,$msg,$charset;
	
		$tpl=$nomenclature_family_musicstand_form_tpl;
		if($id_musicstand){			
			$tpl=str_replace('!!msg_title!!',str_replace('!!famille_name!!',$this->info['display'],$msg['admin_nomenclature_family_musicstand_form_edit']),$tpl);
			$tpl=str_replace('!!delete!!',"<input type='button' class='bouton' value='".$msg['admin_nomenclature_family_musicstand_form_del']."'  onclick=\"document.getElementById('action').value='musicstand_delete';this.form.submit();\"  />", $tpl);
			$name=$this->info['musicstands'][$id_musicstand]['name'];
			
			if($this->info['musicstands'][$id_musicstand]['division'])$checked="checked"; else $checked="";
			if($this->info['musicstands'][$id_musicstand]['workshop'])$workshop_checked="checked"; else $workshop_checked="";
			$tpl=str_replace('!!checked!!',$checked, $tpl);
			$tpl=str_replace('!!workshop_checked!!',$workshop_checked, $tpl);
		}else{
			$tpl=str_replace('!!msg_title!!',str_replace('!!famille_name!!',$this->info['display'],$msg['admin_nomenclature_family_musicstand_form_add']),$tpl);
			$tpl_objet="";
			$tpl=str_replace('!!delete!!',"",$tpl);
			$tpl=str_replace('!!checked!!',"", $tpl);
			$tpl=str_replace('!!workshop_checked!!',"", $tpl);
			$name="";
		}
		$tpl=str_replace('!!name!!',htmlentities($name, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!id!!',$this->id,$tpl);
		$tpl=str_replace('!!id_musicstand!!',$id_musicstand,$tpl);
		
		$tpl_instruments="";
		if($id_musicstand){
			$tpl_instruments="				
			<script type='text/javascript' src='./javascript/sorttable.js'></script>	
			<table class='sortable'>
				<tr>		
					<th>".$msg["admin_nomenclature_instrument_code"]."
					</th> 	
					<th>".$msg["admin_nomenclature_instrument_name"]."
					</th> 		
					<th>".$msg["admin_nomenclature_instrument_standard"]."
					  (<input type='radio' name='standard' value='0' !!checked!! /> ".$msg["admin_nomenclature_instrument_standard_no"]." )
					</th> 
				</tr>				
			";	
			$flag_checked=0;
			foreach($this->info['musicstands'][$id_musicstand]['instruments'] as $instrument){
				if($instrument['standard']){
					$checked="checked"; 
					$flag_checked=1;
				}else $checked="";		
				
				$standard="<input type='radio' name='standard' value='".$instrument['id']."' $checked />";			
				$tpl_instrument="
				<tr>
					<td style=\"cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form&id=".$instrument['id']."';\">
					".$instrument['code']."
					</td>	
					<td style=\"cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form&id=".$instrument['id']."';\">
					".$instrument['name']."
					</td>	
					<td>
					".$standard."
					</td>	
				</tr>					
				";
				$tpl_instruments.=$tpl_instrument;
			}
			$tpl_instruments.="
			</table>"; 
			if(!$flag_checked)	$checked="checked";else $checked="";
			$tpl_instruments=str_replace('!!checked!!',$checked,$tpl_instruments);
		}	
		$tpl=str_replace('!!instruments!!',$tpl_instruments,$tpl);
			
		return $tpl;
	}
	
	public function musicstand_save($id_musicstand) {
		global $dbh;
		global $name;
		global $division;
		global $workshop;
		global $standard;
	
		$fields="
		musicstand_famille_num='".$this->id."',
		musicstand_name='".$name."',
		musicstand_division='".$division."',
		musicstand_workshop='".$workshop."'
		";
		if(!$id_musicstand){ // Ajout
			
			$requete="select max(musicstand_order) as ordre from nomenclature_musicstands where musicstand_famille_num=".$this->id;
			$resultat=pmb_mysql_query($requete, $dbh);
			$ordre_max=@pmb_mysql_result($resultat,0,0);			
			$req="INSERT INTO nomenclature_musicstands SET $fields, musicstand_order=".($ordre_max+1);
			pmb_mysql_query($req, $dbh);
			$id_musicstand = pmb_mysql_insert_id($dbh);
		} else {
		$req="UPDATE nomenclature_musicstands SET $fields where id_musicstand=".$id_musicstand;
		pmb_mysql_query($req, $dbh);
		}
		
		$standard+=0; // id de l'instrument standard
		$req="UPDATE nomenclature_instruments SET instrument_standard=0 where instrument_musicstand_num=".$id_musicstand;
		pmb_mysql_query($req, $dbh);
		if($standard){			
			$req="UPDATE nomenclature_instruments SET instrument_standard=1 where id_instrument=".$standard;
			pmb_mysql_query($req, $dbh);		
		}
					
		$this->fetch_data();
	}
	
	public function musicstand_delete($id_musicstand) {
		global $dbh;
		
		$req="UPDATE nomenclature_instruments SET instrument_musicstand_num=0 where instrument_musicstand_num=".$id_musicstand;
		pmb_mysql_query($req, $dbh);
		
		$req="DELETE from nomenclature_musicstands WHERE id_musicstand=".$id_musicstand;
		pmb_mysql_query($req, $dbh);
		$this->fetch_data();
	}
	
	
} //nomenclature_family_musicstand class end



class nomenclature_family_admins {	
	public $info=array();
	
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $dbh;
		$this->info=array();
		$i=0;
		$req="select * from nomenclature_families order by family_order";
		$resultat=pmb_mysql_query($req,$dbh);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info[$i]= $family=new nomenclature_family_admin($r->id_family);					
				$i++;
			}
		}
	}
				
	public function get_list() {
		global $nomenclature_family_list_tpl,$nomenclature_family_list_line_tpl,$msg;
		
		$tpl=$nomenclature_family_list_tpl;
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info as $elt){
			$tpl_elt=$nomenclature_family_list_line_tpl;
			if($odd_even=='odd')$odd_even="even"; else $odd_even="odd";		
			
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);	
			$tpl_elt=str_replace('!!name!!',$elt->info['name'], $tpl_elt);
			$tpl_elt=str_replace('!!musicstands_display!!',$elt->info['musicstands_display'], $tpl_elt);
			$tpl_elt=str_replace('!!id!!',$elt->info['id'], $tpl_elt);	
			$tpl_list.=$tpl_elt;	
		}
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		return $tpl;
	}	
	
	function order_up($id){
		global $dbh;	
	
		$requete="select family_order from nomenclature_families where id_family=$id";
		$resultat=pmb_mysql_query($requete,$dbh);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select max(family_order) as ordre from nomenclature_families where family_order<$ordre";
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre_max=@pmb_mysql_result($resultat,0,0);
		if ($ordre_max != '') {
			$requete="select id_family from nomenclature_families where family_order=$ordre_max limit 1";
			$resultat=pmb_mysql_query($requete);
			$id_max=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_families set family_order='".$ordre_max."' where id_family=$id";
			pmb_mysql_query($requete,$dbh);
			$requete="update nomenclature_families set family_order='".$ordre."' where id_family=".$id_max;
			pmb_mysql_query($requete,$dbh);			
			$this->fetch_data();
		}
	}
	
	function order_down($id){
		global $dbh;
		$requete="select family_order from nomenclature_families where id_family=$id";
		$resultat=pmb_mysql_query($requete);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select min(family_order) as ordre from nomenclature_families where family_order>$ordre";
		$resultat=pmb_mysql_query($requete);
		$ordre_min=@pmb_mysql_result($resultat,0,0);
		if ($ordre_min) {
			$requete="select id_family from nomenclature_families where family_order=$ordre_min limit 1";
			$resultat=pmb_mysql_query($requete);
			$id_min=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_families set family_order='".$ordre_min."' where id_family=$id";
			pmb_mysql_query($requete);
			$requete="update nomenclature_families set family_order='".$ordre."' where id_family=".$id_min;
			pmb_mysql_query($requete);			
			$this->fetch_data();
		}
	}

} // nomenclature_family_admins class end
	


class nomenclature_family_musicstand_admins {
	protected $id=0; // id de la famille ou est rataché les pupitres
	public $info=array();

	public function __construct($id) {
		$this->id=$id+0;
		$this->fetch_data();
	}

	protected function fetch_data() {
		global $dbh;
		$this->info=array();
		$this->info[0]=new nomenclature_family_admin($this->id);
	}

	public function get_list() {
		global $nomenclature_family_musicstand_list_tpl,$nomenclature_family_musicstand_list_line_tpl,$msg;

		$tpl=$nomenclature_family_musicstand_list_tpl;
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info[0]->info['musicstands'] as $elt){
			$tpl_elt=$nomenclature_family_musicstand_list_line_tpl;
			if($odd_even=='odd')$odd_even="even"; else $odd_even="odd";
			
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);
			$tpl_elt=str_replace('!!name!!',$elt['name'], $tpl_elt);
			if($elt['division'])$division="x"; else $division="";
			if($elt['workshop'])$workshop="x"; else $workshop="";
			$tpl_elt=str_replace('!!division!!',$division, $tpl_elt);
			$tpl_elt=str_replace('!!workshop!!',$workshop, $tpl_elt);
			$tpl_elt=str_replace('!!instruments!!',$elt['instruments_display'], $tpl_elt);
			$tpl_elt=str_replace('!!id_musicstand!!',$elt['id'], $tpl_elt);
			$tpl_list.=$tpl_elt;
		}
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		$tpl=str_replace('!!id!!',$this->id, $tpl); // id family
		$tpl=str_replace('!!famille_name!!',$this->info[0]->info['name'], $tpl); // id family
		return $tpl;
	}
	
	function order_up($id){
		global $dbh;
	
		$requete="select musicstand_order from nomenclature_musicstands where id_musicstand=$id";
		$resultat=pmb_mysql_query($requete,$dbh);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select max(musicstand_order) as ordre from nomenclature_musicstands where musicstand_order<$ordre and musicstand_famille_num=".$this->id;
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre_max=@pmb_mysql_result($resultat,0,0);
		if ($ordre_max != '') {
			$requete="select id_musicstand from nomenclature_musicstands where musicstand_order=$ordre_max  and musicstand_famille_num=".$this->id." limit 1";
			$resultat=pmb_mysql_query($requete, $dbh);
			$id_max=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_musicstands set musicstand_order='".$ordre_max."' where id_musicstand=$id";
			pmb_mysql_query($requete,$dbh);
			$requete="update nomenclature_musicstands set musicstand_order='".$ordre."' where id_musicstand=".$id_max;
			pmb_mysql_query($requete,$dbh);
			$this->fetch_data();
		}
	}
	
	function order_down($id){
		global $dbh;
		$requete="select musicstand_order from nomenclature_musicstands where id_musicstand=$id";
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select min(musicstand_order) as ordre from nomenclature_musicstands where musicstand_order>$ordre and musicstand_famille_num=".$this->id;
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre_min=@pmb_mysql_result($resultat,0,0);
		if ($ordre_min) {
			$requete="select id_musicstand from nomenclature_musicstands where musicstand_order=$ordre_min  and musicstand_famille_num=".$this->id." limit 1";
			$resultat=pmb_mysql_query($requete, $dbh);
			$id_min=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_musicstands set musicstand_order='".$ordre_min."' where id_musicstand=$id";
			pmb_mysql_query($requete, $dbh);
			$requete="update nomenclature_musicstands set musicstand_order='".$ordre."' where id_musicstand=".$id_min;
			pmb_mysql_query($requete, $dbh);
			$this->fetch_data();
		}
	}
	
	 
} // nomenclature_family_musicstand_admins class end
