<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_voice_admin.class.php,v 1.5 2016-03-30 14:34:53 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/nomenclature_voice_admin.tpl.php");

class nomenclature_voice_admin {
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
		$req="select * from nomenclature_voices where id_voice=". $this->id;	
		$resultat=pmb_mysql_query($req,$dbh);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_voice;	
			$this->info['name']= $r->voice_name;
			$this->info['code']= $r->voice_code;
			$this->info['display']=  "<a href='./admin.php?categ=voice&sub=voice&action=form&id=".$r->id_voice."'>".$r->voice_name."</a>";$r->voice_name;	
		}
	}
 
	public function get_form() {
		global $nomenclature_voice_form_tpl,$msg,$charset;		
		
		$tpl=$nomenclature_voice_form_tpl;
		if($this->id){
			$tpl=str_replace('!!msg_title!!',$msg['admin_nomenclature_voice_form_edit'],$tpl);
			$tpl=str_replace('!!delete!!',"<input type='button' class='bouton' value='".$msg['admin_nomenclature_voice_form_del']."'  onclick=\"document.getElementById('action').value='delete';this.form.submit();\"  />", $tpl);
			$name=$this->info['name'];
			$code=$this->info['code'];
		}else{ 
			$tpl=str_replace('!!msg_title!!',$msg['admin_nomenclature_voice_form_add'],$tpl);
			$tpl_objet="";
			$tpl=str_replace('!!delete!!',"",$tpl);
			$name="";
			$code="";
		}
		$tpl=str_replace('!!name!!',htmlentities($name, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!code!!',htmlentities($code, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!id!!',$this->id,$tpl);
	 
		return $tpl;
	}

	public function save() {
		global $dbh, $msg;
		global $name;
		global $code;
			
		$fields="
			voice_name='".$name."', voice_code='".$code."'
		";		
		if(!$this->id){ // Ajout
			$requete="select max(voice_order) as ordre from nomenclature_voices";
			$resultat=pmb_mysql_query($requete, $dbh);
			$ordre_max=@pmb_mysql_result($resultat,0,0);
			$req="INSERT INTO nomenclature_voices SET $fields, voice_order=".($ordre_max+1);	
			pmb_mysql_query($req, $dbh);
			$this->id = pmb_mysql_insert_id($dbh);
		} else {
			$req="UPDATE nomenclature_voices SET $fields where id_voice=".$this->id;	
			pmb_mysql_query($req, $dbh);				
		}	
		$this->fetch_data();
		print display_notification($msg['account_types_success_saved']);
	}	
	
	public function delete() {
		global $dbh;
		$req="DELETE from nomenclature_voices WHERE id_voice=".$this->id;
		pmb_mysql_query($req, $dbh);	
		$this->id=0;		
		$this->fetch_data();	
	}	
	
} //nomenclature_voice_admin class end



class nomenclature_voice_admins {	
	public $info=array();
	
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $dbh;
		$this->info=array();
		$i=0;
		$req="select * from nomenclature_voices order by voice_order";
		$resultat=pmb_mysql_query($req,$dbh);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info[$i]= $voice=new nomenclature_voice_admin($r->id_voice);					
				$i++;
			}
		}
	}
				
	public function get_list() {
		global $nomenclature_voice_list_tpl,$nomenclature_voice_list_line_tpl,$msg;
		
		$tpl=$nomenclature_voice_list_tpl;
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info as $elt){
			$tpl_elt=$nomenclature_voice_list_line_tpl;
			if($odd_even=='odd')$odd_even="even"; else $odd_even="odd";		
			
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);	
			$tpl_elt=str_replace('!!name!!',$elt->info['name'], $tpl_elt);
			$tpl_elt=str_replace('!!code!!',$elt->info['code'], $tpl_elt);
			$tpl_elt=str_replace('!!id!!',$elt->info['id'], $tpl_elt);	
			$tpl_list.=$tpl_elt;	
		}
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		return $tpl;
	}	
	
	function order_up($id){
		global $dbh;	
	
		$requete="select voice_order from nomenclature_voices where id_voice=$id";
		$resultat=pmb_mysql_query($requete,$dbh);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select max(voice_order) as ordre from nomenclature_voices where voice_order<$ordre";
		$resultat=pmb_mysql_query($requete, $dbh);
		$ordre_max=@pmb_mysql_result($resultat,0,0);
		if ($ordre_max != '') {
			$requete="select id_voice from nomenclature_voices where voice_order=$ordre_max limit 1";
			$resultat=pmb_mysql_query($requete);
			$id_max=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_voices set voice_order='".$ordre_max."' where id_voice=$id";
			pmb_mysql_query($requete,$dbh);
			$requete="update nomenclature_voices set voice_order='".$ordre."' where id_voice=".$id_max;
			pmb_mysql_query($requete,$dbh);			
			$this->fetch_data();
		}
	}
	
	function order_down($id){
		global $dbh;
		$requete="select voice_order from nomenclature_voices where id_voice=$id";
		$resultat=pmb_mysql_query($requete);
		$ordre=pmb_mysql_result($resultat,0,0);
		$requete="select min(voice_order) as ordre from nomenclature_voices where voice_order>$ordre";
		$resultat=pmb_mysql_query($requete);
		$ordre_min=@pmb_mysql_result($resultat,0,0);
		if ($ordre_min) {
			$requete="select id_voice from nomenclature_voices where voice_order=$ordre_min limit 1";
			$resultat=pmb_mysql_query($requete);
			$id_min=pmb_mysql_result($resultat,0,0);
			$requete="update nomenclature_voices set voice_order='".$ordre_min."' where id_voice=$id";
			pmb_mysql_query($requete);
			$requete="update nomenclature_voices set voice_order='".$ordre."' where id_voice=".$id_min;
			pmb_mysql_query($requete);			
			$this->fetch_data();
		}
	}

} // nomenclature_voice_admins class end
	
