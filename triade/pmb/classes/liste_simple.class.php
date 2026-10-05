<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_simple.class.php,v 1.12.4.1 2025/04/25 09:37:43 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/liste_simple.tpl.php");

/*
 * Classe générique qui permet la création d'un liste simple id/libellé
 */
class liste_simple{

	public $table ='';
	public $colonne_id_nom = '';
	public $colonne_lib_nom = '';
	public $id_liste = 0;
	public $lib_liste = '';
	public $messages = array();
	public $actions = array();

	public function __construct($table,$col_id_name,$col_lib_name,$id_liste=0){
		$this->table = $table;
		$this->colonne_id_nom = $col_id_name;
		$this->colonne_lib_nom = $col_lib_name;

		$this->id_liste = intval($id_liste);

		if(!$this->id_liste){
			$this->lib_liste ='';
		} else {
			$req = "select $this->colonne_lib_nom as lib from $this->table where $this->colonne_id_nom ='".$this->id_liste."'";
			$res = pmb_mysql_query($req);
			$list = pmb_mysql_fetch_object($res);
			$this->lib_liste = $list->lib;
		}

		$this->setParametres();
	}

	/*
	 * Fonction qui affecte tous les paramètres de la classe
	 */
	public function setParametres(){
		$this->setMessages();
		$this->setActions();
	}

	/*
	 * Affectation des messages
	 */
	public function setMessages($ajout_titre="",$modif_titre="",$confirm_del="",$add_btn="", $no_list="", $used="", $selector_all=""){
		$ajout_titre ? $this->messages['ajout_titre'] = $ajout_titre : $this->messages['ajout_titre'] = 'list_simple_ajout';
		$modif_titre ? $this->messages['modif_titre'] = $modif_titre : $this->messages['modif_titre'] = 'list_simple_modif';
		$confirm_del ? $this->messages['confirm_del'] = $confirm_del : $this->messages['confirm_del'] = 'list_simple_del';
		$add_btn ? $this->messages['add_btn'] = $add_btn : $this->messages['add_btn'] = 'list_simple_add_btn';
		$no_list ? $this->messages['no_list'] = $no_list : $this->messages['no_list'] = 'list_simple_no_list';
		$used ? $this->messages['object_used'] = $used : $this->messages['object_used'] = 'list_simple_used';
		$selector_all ? $this->messages['selector_all'] = $selector_all : $this->messages['selector_all'] = 'list_simple_all';
	}

	/*
	 * Définition des actions
	 */
	public function setActions($base='',$form_act=''){
		$this->actions['base'] = $base;
		$this->actions['form'] = $form_act;
	}

	/*
	 * Formulaire d'ajout/modification
	 */
	public function show_edit_form(){
		global $liste_simple_content_form, $msg, $charset;

		$content_form = $liste_simple_content_form;
		$content_form = str_replace('!!id!!', $this->id_liste, $content_form);

		$interface_form = new interface_admin_form('simple_list_form');
		if(!$this->id_liste){
			$interface_form->set_label($msg[$this->messages['ajout_titre']]);
		}else{
			$interface_form->set_label($msg[$this->messages['modif_titre']]);
		}
		$content_form = str_replace('!!libelle!!', htmlentities($this->lib_liste, ENT_QUOTES, $charset), $content_form);

		$interface_form->set_object_id($this->id_liste)
		->set_confirm_delete_msg($msg[$this->messages['confirm_del']])
		->set_content_form($content_form)
		->set_field_focus('libelle');
		print $interface_form->get_display();
	}

	public function getLabel($id){
		$query='SELECT '.$this->colonne_lib_nom.' FROM '.$this->table.' WHERE '.$this->colonne_id_nom.'='.$id;
		$result=pmb_mysql_query($query);
		if(!pmb_mysql_error() && pmb_mysql_num_rows($result)){
			return pmb_mysql_result($result, 0,0);
		}
	}

	/*
	 * Retourne un sélecteur correspondant à la liste
	 */
	public function getListSelector($idliste=0,$action='',$default=false){
		global $charset,$msg;

		$req = "select * from $this->table order by $this->colonne_lib_nom";
		$res = pmb_mysql_query($req);
		$select = "";
		$selector = "<select id='$this->colonne_id_nom' name='$this->colonne_id_nom' $action >";
		if($default) $selector .= "<option value='0'>".htmlentities($msg[$this->messages['selector_all']],ENT_QUOTES,$charset)."</option>";
		while(($list=pmb_mysql_fetch_object($res))){
			$id = $this->colonne_id_nom;
			$nom = $this->colonne_lib_nom;
			if($idliste == $list->$id) $select="selected";
			$selector .= "<option value='".$list->$id."' $select>".htmlentities($list->$nom,ENT_QUOTES,$charset)."</option>";
			$select = "";
		}
		$selector .= "</select>";

		return $selector;
	}

	//Vérifie si le thème de demande est utilisé dans les demandes
	public function hasElements(){
	}
}

/*
 * Classe des thèmes de demandes
 */
class demandes_themes extends liste_simple {

	/*
	 * Définition des paramètres
	 */
	public function setParametres(){
		$this->setMessages('demandes_ajout_theme','demandes_modif_theme','demandes_del_theme','demandes_add_theme','demandes_no_theme_available','demandes_used_theme');
		$this->setActions('admin.php?categ=demandes&sub=theme','admin.php?categ=demandes&sub=theme');
	}
	/*
	 * Vérifie si le thème de demande est utilisé dans les demandes
	 */
	public function hasElements(){
		$q = "select count(1) from demandes where theme_demande = '".$this->id_liste."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	public static function get_qty() {
		$q = "select count(1) from demandes_theme";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

}
?>