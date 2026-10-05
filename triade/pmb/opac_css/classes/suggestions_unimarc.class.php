<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_unimarc.class.php,v 1.5 2021/12/24 13:21:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/classes/iso2709.class.php");

class suggestions_unimarc{
	
	public $sugg_uni_id=0;
	public $sugg_uni_notice='';
	public $sugg_uni_origine='';
	public $sugg_uni_num_notice=0;
	
	/*
	 * Constructeur
	 */
	public function __construct($id=0){
		$this->sugg_uni_id = intval($id);
		$this->sugg_uni_notice = "";
		$this->sugg_uni_origine = "";
		$this->sugg_uni_num_notice = 0;
		if($this->sugg_uni_id){
			$req = "select * from import_marc where id_import='".$this->sugg_uni_id."'";
			$res = pmb_mysql_query($req);
			if($res){
				$uni = pmb_mysql_fetch_object($res);
				$this->sugg_uni_notice = $uni->notice;
				$this->sugg_uni_origine = $uni->origine;
				$this->sugg_uni_num_notice = $uni->no_notice;
			}			
		}
	}
	
	/*
	 * Enregistrement
	 */
	public function save(){
		$req = "insert into import_marc set notice='".addslashes($this->sugg_uni_notice)."', 
			origine='".addslashes($this->sugg_uni_origine)."',
			no_notice='".addslashes($this->sugg_uni_num_notice)."'";
		pmb_mysql_query($req); 
		
		$this->sugg_uni_id = pmb_mysql_insert_id();
	}
	
	/*
	 * Suppression
	 */
	public function delete(){
		$req = "delete from import_marc where origine='".$this->sugg_uni_origine."'";
		pmb_mysql_query($req);
	}
	
	/*
	 * Récupération de la notice unimarc par l'entrepot
	 */
	public function entrepot_to_unimarc($recid) {
		$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($recid).";";
		$myQuery = pmb_mysql_query($requete);
		$source_id = pmb_mysql_result($myQuery, 0, 0);
		
		$requete="select * from entrepot_source_$source_id where recid='".addslashes($recid)."' group by ufield,usubfield,field_order,subfield_order,value order by field_order,subfield_order";
		$resultat = pmb_mysql_query($requete);
		
		$unimarc=new iso2709_record("",USER_UPDATE);
		
		$field_order=-1;
		$field='';
		$sfields=array();
		
		while ($r=pmb_mysql_fetch_object($resultat)) {
			switch ($r->ufield) {
				case "rs":
					$unimarc->set_rs($r->value);
					break;
				case "dt":
					$unimarc->set_dt($r->value);
					break;
				case "bl":
					$unimarc->set_bl($r->value);
					break;
				case "hl":
					$unimarc->set_hl($r->value);
					break;
				case "el":
					$unimarc->set_el($r->value);
					break;
				case "ru":
					$unimarc->set_ru($r->value);
					break;
				case "001":
					$unimarc->add_field("001",'  ',$r->value);
				default:
					if ($field_order!=$r->field_order) {
						if (count($sfields)) {
							$unimarc->add_field($field,'  ',$sfields);
						}
						$field=$r->ufield;
						$sfields=array();
						$field_order=$r->field_order;
					}
					if (!$r->usubfield) 
						$unimarc->add_field($r->ufield,'',$r->value);
					else {
						$sfields[][0]=$r->usubfield;
						$sfields[count($sfields)-1][1]=$r->value;
					}
					break;
			}
		}
		if (count($sfields)) {
			$unimarc->add_field($field,'  ',$sfields);
		}
		$unimarc->update();
		$this->sugg_uni_notice = $unimarc->full_record;
	}
}
?>