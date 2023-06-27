<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_types.class.php,v 1.5 2018-01-23 13:35:42 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/templates/demandes_type.tpl.php");
require_once($class_path."/liste_simple.class.php");
require_once($class_path."/workflow.class.php");
/*
 * Classe des types de demandes
 */
class demandes_types extends liste_simple{
	public $id = 0;
	public $allowed_actions = array();
	
	public function __construct($table,$col_id_name,$col_lib_name,$id_liste=0){
		global $dbh;
		$this->table = $table;
		$this->colonne_id_nom = $col_id_name;
		$this->colonne_lib_nom = $col_lib_name;
	
		$this->id_liste = $id_liste;
	
		if(!$this->id_liste){
			$this->lib_liste ='';
			$workflow = new workflow('ACTIONS');
			$this->allowed_actions = $workflow->getTypeList();
		} else {
			$req = "select $this->colonne_lib_nom as lib,allowed_actions from $this->table where $this->colonne_id_nom ='".$this->id_liste."'";
			$res = pmb_mysql_query($req,$dbh);
			$list = pmb_mysql_fetch_object($res);
			$this->lib_liste = $list->lib;
			$this->allowed_actions = unserialize($list->allowed_actions);
			if(!is_array($this->allowed_actions) || !count($this->allowed_actions)){
				$workflow = new workflow('ACTIONS');
				$this->allowed_actions = $workflow->getTypeList();
			}
		}
		$this->setParametres();
	}
	
	
	public function setParametres(){
		$this->setMessages('demandes_ajout_type','demandes_modif_type','demandes_del_type','demandes_add_type','demandes_no_type_available','demandes_used_type');
		$this->setActions('admin.php?categ=demandes&sub=type','admin.php?categ=demandes&sub=type');
	}	
	
	public function hasElements(){
		
		global $dbh;
		
		$q = "select count(1) from demandes where type_demande = '".$this->id_liste."' ";
		$r = pmb_mysql_query($q, $dbh); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	public static function get_qty() {
		
		global $dbh;
		$q = "select count(1) from demandes_type";
		$r = pmb_mysql_query($q, $dbh); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	/*
	 * Formulaire d'ajout/modification
	*/
	public function show_edit_form(){
		global $demandes_type_form, $msg, $charset;
	
		if(!$this->id_liste){
			$form = str_replace('!!form_title!!',$msg[$this->messages['ajout_titre']],$demandes_type_form);
			$form = str_replace('!!libelle!!','',$form);
			$form = str_replace('!!bouton_sup!!','',$form);
			$form = str_replace('!!id_liste!!','',$form);
		} else {
			$demandes_type_form .= "<script type='text/javascript'>
				function confirm_del(){
					result = confirm(\"".$msg[$this->messages['confirm_del']]."\");
        			return result;
        		}
        		</script>";
			$form = str_replace('!!id_liste!!',$this->id_liste,$demandes_type_form);
			$form = str_replace('!!form_title!!',$msg[$this->messages['modif_titre']],$form);
			$form = str_replace('!!libelle!!',htmlentities($this->lib_liste, ENT_QUOTES, $charset),$form);
			$btn_sup = "<input class='bouton' type='submit' name='del' id='del' value='$msg[63]' onclick='this.form.act.value=\"del\"; return confirm_del();'";
			$form = str_replace('!!bouton_sup!!',$btn_sup,$form);
		}
	
		$form = str_replace("!!actions!!",$this->get_actions_form(),$form);
		$form = str_replace('!!list_simple_action!!',$this->actions['form'],$form);
		print $form;
	}
		
	public function get_actions_form(){
		global $msg,$charset;
		
		$form = "
		<table>
			<tr>
				<th>".$msg['demandes_action_type']."</th>
				<th>".$msg['demandes_action_type_allow']."</th>
				<th>".$msg['demandes_action_type_default']."</th>
			</tr>";
		foreach($this->allowed_actions as $allowed_action){
			$form.="
			<tr>
				<td>".htmlentities($allowed_action['comment'],ENT_QUOTES,$charset)."</td>
				<td>".$msg['connecteurs_yes']."&nbsp;<input type='radio' name='action_".$allowed_action['id']."' value='1'".($allowed_action['active']==1 ? " checked='checked'": "")."/>&nbsp;&nbsp;
					".$msg['connecteurs_no']."&nbsp;<input type='radio' name='action_".$allowed_action['id']."' value='0'".($allowed_action['active']==0 ? " checked='checked'": "")."/></td>
				<td><input type='radio' name='default_action' value='".$allowed_action['id']."'".($allowed_action['default']? " checked='checked'": "")."/></td>
			</tr>";
		}
		$form.= "
		</table>";
		return $form;
	}
	
	/*
	 * Création/Modification
	*/
	public function save(){
	
		global $dbh, $libelle, $default_action;
		$allowed_actions = array();
		foreach($this->allowed_actions as $allowed_action_form){
			$val = "action_".$allowed_action_form['id'];
			global ${$val};
			$allowed_action_form['active'] = ${$val};
			if($allowed_action_form['id'] == $default_action){
				$allowed_action_form['default'] = 1;
			}else{
				$allowed_action_form['default'] = 0;
			}
			$allowed_actions[] = $allowed_action_form;
		
		}
		$this->allowed_actions = $allowed_actions;
	
		if(!$this->id_liste){
			$req = "insert into $this->table set $this->colonne_lib_nom='".$libelle."', allowed_actions = \"".addslashes(serialize($this->allowed_actions))."\"";
		} else {
			$req="update $this->table set $this->colonne_lib_nom='".$libelle."', allowed_actions = \"".addslashes(serialize($this->allowed_actions))."\" where $this->colonne_id_nom='".$this->id_liste."'";
		}
		pmb_mysql_query($req,$dbh);
	}
	
	/*
	 * Formulaire de présentation
	*/
	public function show_form(){
		global $dbh;
		global $msg;
		global $charset;
		
		
		$tab_list =array();
		$req = "select * from $this->table order by $this->colonne_lib_nom";
		$res=pmb_mysql_query($req,$dbh);
		while ($row = pmb_mysql_fetch_object($res)){
			$nom = $this->colonne_lib_nom;
			$id = $this->colonne_id_nom;
			$tab_list[$row->$id] = array(
					'name' => $row->$nom,
					'allowed_actions' => unserialize($row->allowed_actions)
			);
			$colspan = count($tab_list[$row->$id]['allowed_actions']);
		}
		
		$display='';
		$display= "
		<table>
			<tr>
				<th rowspan='2'>".htmlentities($msg[103], ENT_QUOTES, $charset)."</th>
				<th colspan='".$colspan."' >".htmlentities($msg['demande_type_allowed_actions'], ENT_QUOTES, $charset)."</th>";
		$display.= "
			</tr>
			<tr>";
		
		foreach($this->allowed_actions as $action){
			$display.="
				<th>".htmlentities($action['comment'],ENT_QUOTES,$charset)."</th>";
		}
		$display.= "
			</tr>";
		if(count($tab_list) == 0){
			$display .= "<tr><td>".$msg[$this->messages['no_list']]."</td></tr>";
		}
		$parity=1;
		foreach($tab_list as $id_list=>$val) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='".$this->actions['base']."&act=modif&id_liste=$id_list';\" ";
			$display .= "
			<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
				<td><i>".htmlentities($val['name'], ENT_QUOTES, $charset)."</i></td>";
			foreach($this->allowed_actions as $action){
				if(isset($val['allowed_actions']) && is_array($val['allowed_actions'])){
					foreach($val['allowed_actions'] as $allowed_action){
						if($action['id'] == $allowed_action['id']){
							$display .= "
							<td style='text-align:center;".($allowed_action['default'] ? "font-weight:bold;" : "")."'>".($allowed_action['active'] ? "X" : "")."</td>";
							break;
						}else{
							continue;
						}
					}
				}
			}
			$display .= "
			</tr>";
		}
		$display .= "
		</table>
		<input class='bouton' type='button' value=' ".$msg[$this->messages['add_btn']]." ' onClick=\"document.location='".$this->actions['base']."&act=add'\" />";
		print $display;
	}
}
?>