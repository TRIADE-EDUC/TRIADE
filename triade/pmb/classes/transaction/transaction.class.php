<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction.class.php,v 1.4 2019-06-07 12:23:10 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/transaction/transaction.tpl.php");


class transactype  {
	public $id = 0;				// identifiant de la transactype
	public $name = "";				// Libellé de la transactype
	public $unit_price = 0;		// prix unitaire
	public $quick_allowed = 0;		// Autorisation de l'encaissement rapide
	
	public function __construct($id=0){		
		$this->id = intval($id);		
		$this->fetch_data();		
	}
	
	protected function fetch_data(){	
		$this->name="";
		$this->unit_price = 0;
		$this->quick_allowed = 0;		
		if(!$this->id)	return false;
		
		// les infos générales...	
		$rqt = "select * from transactype where transactype_id ='".$this->id."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$row = pmb_mysql_fetch_object($res);
			$this->id = $row->transactype_id;
			$this->name = $row->transactype_name;
			$this->unit_price = $row->transactype_unit_price;
			$this->quick_allowed = $row->transactype_quick_allowed;			
		}
	}
	
	public function get_form(){
		global $msg, $charset;
		global $pmb_droits_explr_localises;
		global $transactype_form,$deflt_docs_location;
		
		$form=$transactype_form;
		
		if($this->id){
			$titre=$msg["transactype_form_titre_edit"];
			$form = str_replace('!!supprimer!!', "<input type='button' class='bouton' value=' ".$msg["transactype_form_delete"]." ' onClick=\"if(confirm('".$msg["transactype_form_delete_question"]."'))
					document.location = './admin.php?categ=finance&sub=transactype&action=delete&id=!!id!!'\" />", $form);
		}else{
			$titre=$msg["transactype_form_titre_add"];
			$form = str_replace('!!supprimer!!', "", $form);
		}
		
		$form = str_replace('!!titre!!', $titre, $form);
		$form = str_replace('!!name!!', htmlentities($this->name,ENT_QUOTES, $charset), $form);
		$form = str_replace('!!unit_price!!', htmlentities($this->unit_price,ENT_QUOTES, $charset), $form);
		
		if($this->quick_allowed) $quick_allowed_checked=" checked='checked' ";
		else $quick_allowed_checked = '';		
		$form = str_replace('!!quick_allowed_checked!!', $quick_allowed_checked, $form);
		
		$form = str_replace('!!action!!', "./admin.php?categ=finance&sub=transactype&action=save&id=!!id!!", $form);
		$form = str_replace('!!id!!', $this->id, $form);
		return $form; 
	}
	
	public function get_from_form(){		
		global $f_name;
		global $f_locations;
		global $id;
		global $f_unit_price;
		global $f_quick_allowed;
		
		$this->id=$id+0;
		$this->name=stripslashes($f_name);
		$this->unit_price=$f_unit_price+0;
		$this->quick_allowed=$f_quick_allowed+0;
	}
	
	public function check_delete(){
		
		return 1;
	}
	
	public function delete(){
		global $dbh;
		
		$rqt = "delete FROM transactype WHERE transactype_id ='".$this->id."'";
		pmb_mysql_query($rqt, $dbh);
		
		$this->id=0;
	}
	
	public function save(){
		global $dbh;
		
		if($this->id){
			
			$save = "update ";
			$clause = "where transactype_id = '".$this->id."'";
		}else{
			$save = "insert into ";
			$clause = "";
		}
		$save.=" transactype set transactype_name='". addslashes( $this->name). "', transactype_unit_price='".$this->unit_price. "' ,transactype_quick_allowed='". $this->quick_allowed. "'   $clause";
		pmb_mysql_query($save,$dbh);		
		if(!$this->id){
			$this->id=pmb_mysql_insert_id();
		}			
		$this->fetch_data();
	}
	
	public function proceed(){
		global $action,$msg;
		
		switch($action) {
			case 'edit':
					print $this->get_form();
				break;
			case 'save':
					$this->get_from_form();
					$this->save();
					print "<script type='text/javascript'>window.location='./admin.php?categ=finance&sub=transactype'</script>";
				break;
			case 'delete':
					if($this->check_delete()){
						$this->delete();
						print "<script type='text/javascript'>window.location='./admin.php?categ=finance&sub=transactype'</script>";
					}else{
						print "<script type='text/javascript'>alert(".$msg["transactype_form_delete_no"].");window.location='./admin.php?categ=finance&sub=transactype'</script>";
					}
				break;	
			default:
				break;
		}
	}
}