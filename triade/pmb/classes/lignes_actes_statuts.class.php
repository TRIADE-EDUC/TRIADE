<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lignes_actes_statuts.class.php,v 1.14 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class lgstat{
	
	 
	public $id_statut = 0;					//Identifiant de statut de ligne d'acte	
	public $libelle  = '';					//Libelle
	public $relance = 0;					//0=non, 1=oui
	 
	//Constructeur.	 
	public function __construct($id_statut=0) {
		$this->id_statut = intval($id_statut);
		if ($this->id_statut) {
			$this->load();	
		}
	}	
	
	
	// charge un statut de ligne d'acte à partir de la base.
	public function load(){
		$q = "select * from lignes_actes_statuts where id_statut = '".$this->id_statut."' ";
		$r = pmb_mysql_query($q) ;
		if(!pmb_mysql_num_rows($r)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->relance = $obj->relance;

	}

	public function get_content_form() {
		global $msg;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('libelle', '103')
		->add_input_node('text', $this->libelle);
		$interface_content_form->add_element('relance', 'acquisition_lgstat_arelancer')
		->add_select_node(array(1 => $msg[40], 0 => $msg[39]), $this->relance);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_form('lgstatform');
		if(!$this->id_statut){
			$interface_form->set_label($msg['acquisition_lgstat_add']);
		}else{
			$interface_form->set_label($msg['acquisition_lgstat_mod']);
		}
		$interface_form->set_object_id($this->id_statut)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->libelle." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('lignes_actes_statuts')
		->set_field_focus('libelle');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $libelle, $relance;
		
		$this->libelle = stripslashes($libelle);
		$this->relance = stripslashes($relance);
	}
	
	public function get_query_if_exists() {
		$query = "select count(1) from lignes_actes_statuts where libelle = '".addslashes($this->libelle)."' ";
		if ($this->id_statut) $query.= "and id_statut != '".$this->id_statut."' ";
		return $query;
	}
	
	// enregistre un statut de ligne d'acte en base.
	public function save(){
		if( $this->libelle == '' ) die("Erreur de création statut de ligne d'acte");
	
		if ($this->id_statut) {
			
			$q = "update lignes_actes_statuts set  
					libelle = '".addslashes($this->libelle)."',
					relance = '".addslashes($this->relance)."'
					where id_statut = '".$this->id_statut."' ";
			pmb_mysql_query($q);
		} else {
			$q = "insert into lignes_actes_statuts set 
					libelle = '".addslashes($this->libelle)."',
					relance = '".addslashes($this->relance)."' ";
			pmb_mysql_query($q);
			$this->id_statut = pmb_mysql_insert_id();
		}
	}

	//Retourne une liste des statuts de lignes d'actes (tableau)
	public static function getList($x='ARRAY_ALL') {
		$res = array();
		
		$q = "select * from lignes_actes_statuts order by libelle ";
		
		switch ($x) {
			case 'QUERY' :
				return $q;
			case 'ARRAY_VALUES' :
				$r = pmb_mysql_query($q);
				$res = array();
				while ($row = pmb_mysql_fetch_object($r)){
					$res[] = $row->id_statut;
				}
				break;
			case 'ARRAY_ALL':
			default :
				$r = pmb_mysql_query($q);
				$res = array();
				while ($row = pmb_mysql_fetch_object($r)){
					$res[$row->id_statut] = array();
					$res[$row->id_statut][0] = $row->libelle;
					$res[$row->id_statut][1] = $row->relance;
				}
				break;
		}
		return $res;
	}

	//Retourne un selecteur html avec la liste des statuts de lignes d'actes
	public static function getHtmlSelect($selected=array(), $sel_all='', $sel_attr=array()) {
		global $msg,$charset;

		$sel='';
		$q = "select id_statut,libelle from lignes_actes_statuts order by libelle ";
		$r = pmb_mysql_query($q);
		$res = array();
		if ($sel_all) {
			$res[0]=htmlentities($sel_all,ENT_QUOTES,$charset);
		}
		
		while ($row = pmb_mysql_fetch_object($r)){
			$res[$row->id_statut] = $row->libelle;
		}
		
		$size=count($res);
		if (isset($sel_attr['size']) && $sel_attr['size']>$size) $sel_attr['size']=$size;
		
		if ($size) {
			$sel="<select ";
			if (count($sel_attr)) {
				foreach($sel_attr as $attr=>$val) {
					$sel.="$attr='".$val."' ";
				}
			}
			$sel.=">";
			$sel.="<option value='0' ".(!count($selected) || in_array(0,$selected) ? "selected='selected'" : "").">".htmlentities($msg['acquisition_lgstat_all'],ENT_QUOTES,$charset)."</option>";
			foreach($res as $id=>$val){
				$sel.="<option value='".$id."'";
				if(in_array($id,$selected)) $sel.=" selected='selected'";
				$sel.=" >";
				$sel.=htmlentities($val,ENT_QUOTES,$charset);
				$sel.="</option>";
			}
			$sel.='</select>';
		}
		return $sel;
	}
	
	//Vérifie si un statut de ligne d'acte existe
	public static function exists($id) {
		$id = intval($id);
		$q = "select count(1) from lignes_actes_statuts where id_statut = '".$id."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
		
	}
		
	//Vérifie si le libelle d'un statut de ligne d'acte existe déjà en base
	public static function existsLibelle($libelle,$id) {
		$id = intval($id);
		$q = "select count(1) from lignes_actes_statuts where libelle = '".$libelle."' ";
		if ($id) $q.= "and id_statut != '".$id."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}
	
	public static function getLabelFromId($id) {
		return lgstat::getList()[$id][0];
	}

	//supprime un statut de ligne d'acte de la base
	public static function delete($id= 0) {
		global $msg;
		
		$id = intval($id);
		if($id) {
			if ($id=='1') {	//statut de ligne d'acte avec id=1 non supprimable
				$msg_suppr_err = $msg['acquisition_lgstat_used'] ;
				pmb_error::get_instance(static::class)->add_message('321', $msg_suppr_err);
				return false;
			} else {
				$total1 = static::isUsed($id);
				if ($total1==0) {
					$q = "delete from lignes_actes_statuts where id_statut = '".$id."' ";
					pmb_mysql_query($q);
					return true;
				} else {
					$msg_suppr_err = $msg['acquisition_lgstat_used'] ;
					if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_lgstat_used_lgact'] ;
					pmb_error::get_instance(static::class)->add_message('321', $msg_suppr_err);
					return false;
				}
			}
		}
		return true;
	}


	//Vérifie si un statut de ligne d'acte est utilise dans les lignes d'actes	
	public static function isUsed($id){
		$id = intval($id);
		if (!$id) return 0;
		$total=0;
		$q = "select count(1) from lignes_actes where num_statut = '".$id."' ";
		$r = pmb_mysql_query($q); 
		$total+=pmb_mysql_result($r, 0, 0);
		$q = "select count(1) from lignes_actes_relances where num_statut = '".$id."' ";
		$r = pmb_mysql_query($q); 
		$total+=pmb_mysql_result($r, 0, 0);
		$q = "select count(1) from users where deflt3lgstatdev='".$id."' or deflt3lgstatcde='".$id." '";
		$r = pmb_mysql_query($q);
		pmb_mysql_result($r, 0, 0);
		$total+=pmb_mysql_result($r, 0, 0);
		return $total;
	}


	//optimization de la table lignes_actes_statuts
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE lignes_actes_statuts');
		return $opt;
				
	}
				
}