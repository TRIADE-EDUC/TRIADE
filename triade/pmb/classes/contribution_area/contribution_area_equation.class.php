<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_equation.class.php,v 1.10.4.1 2025/03/17 08:15:42 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once "$class_path/contribution_area/contribution_area.class.php";
require_once "$include_path/templates/contribution_area/contribution_area_equation.tpl.php";
require_once "$include_path/misc.inc.php";

class contribution_area_equation{

	protected $id;
	protected $type;
	protected $name;
	protected $query;
	protected $human_query;
	protected $xml_file_name;
	protected $search_class_name;
	protected static $equations = array();
	private static $equations_fetched = false;

	public function __construct($id = 0) {
		$this->id = intval($id);
		$this->fetch_data();
	}


	protected function fetch_data () {
		if ($this->id) {
			$query = "	SELECT contribution_area_equation_id, contribution_area_equation_name, contribution_area_equation_type, contribution_area_equation_query, contribution_area_equation_human_query
						FROM contribution_area_equations
						WHERE contribution_area_equation_id = '". $this->id ."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				if ($row = pmb_mysql_fetch_object($result)) {
					$this->name = $row->contribution_area_equation_name;
					$this->type = $row->contribution_area_equation_type;
					$this->query = $row->contribution_area_equation_query;
					$this->human_query = $row->contribution_area_equation_human_query;
				}
			}
		}
	}

	public static function show_list(){
		global $msg;
		global $charset;

		static::get_list();

		print "
		<table>
			<tr>
				<th>".$msg['noti_statut_libelle']."</th>
				<th>".$msg['admin_contribution_area_equation_type']."</th>
			</tr>";
		$i=0;
		foreach(static::$equations as $id => $equation){
			if ($i % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			print "
			<tr  class='".$pair_impair."' style='cursor: pointer' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\">
				<td onclick='document.location=\"./modelling.php?categ=contribution_area&sub=equation&action=edit&id=".$id."\"'>".htmlentities($equation['name'], ENT_QUOTES, $charset)."</td>
				<td onclick='document.location=\"./modelling.php?categ=contribution_area&sub=equation&action=edit&id=".$id."\"'>".htmlentities($equation['type_name'], ENT_QUOTES, $charset)."</td>
			</tr>";
			$i++;
		}
		print "
		</table>
		<div class='row'>
			<input type='button' class='bouton' value='".$msg['admin_contribution_area_add_equation']."' onclick='document.location=\"./modelling.php?categ=contribution_area&sub=equation&action=add\"'/>
		</div>";
	}

	public static function get_list(){
		if(!static::$equations_fetched){
			static::$equations = array();
			$query = "	SELECT contribution_area_equation_id, contribution_area_equation_name, contribution_area_equation_type, contribution_area_equation_query, contribution_area_equation_human_query
						FROM contribution_area_equations
						ORDER BY contribution_area_equation_name";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){

				$pmb_entities = static::get_pmb_entities();

				while($row = pmb_mysql_fetch_object($result)){
					static::$equations[$row->contribution_area_equation_id] = array(
						'name' => $row->contribution_area_equation_name,
						'type' => $row->contribution_area_equation_type,
						'type_name' => $pmb_entities[$row->contribution_area_equation_type],
						'query' => unserialize($row->contribution_area_equation_query),
						'human_query' => $row->contribution_area_equation_human_query
					);
				}
			}
			static::$equations_fetched = true;
		}
	}

	public static function show_form($id){
		global $msg,$charset;
		global $admin_contribution_area_status_form;

		static::get_list();
		$id = intval($id);
		$form = $admin_contribution_area_status_form;

		if(isset(static::$equations[$id])){
			$form_title = $msg['118'];
			$statut = static::$equations[$id];
		}else{
			$form_title = $msg['115'];
			$statut = array(
				'label' =>	"",
				'class_html' => "statutnot1",
				'available_for' => array()
			);
		}

		$form = str_replace("!!form_title!!", $form_title, $form);
		$couleur=array();
		for ($i=1;$i<=20; $i++) {
			if ($statut['class_html'] == "statutnot".$i){
			    $checked = "checked";
			}
			else {
			    $checked = "";
			}
			$couleur[$i]="<span for='statutnot".$i."' class='statutnot".$i."' style='margin: 7px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' />
					<input id='statutnot".$i."' type=radio name='form_class_html' value='statutnot".$i."' $checked class='checkbox' /></span>";
			if ($i==10) $couleur[10].="<br />";
			elseif ($i!=20) $couleur[$i].="<b>|</b>";
		}

		$couleurs=implode("",$couleur);
		$form = str_replace("!!class_html!!", $couleurs, $form);

		$form = str_replace("!!gestion_libelle!!", htmlentities($statut['label'],ENT_QUOTES,$charset),$form);
		if($id == 1 || !isset(static::$equations[$id])){
			$form = str_replace("!!bouton_supprimer!!","",$form);
		}else{
			$form = str_replace("!!bouton_supprimer!!","<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />",$form); ;
		}

		$entities_list = static::get_pmb_entities();
		$i=0;
		foreach($entities_list as $value => $name){
		    if($i!= 0 && $i % 5 == 0){
				$pmb_entities.= "<br>";
			}
			$pmb_entities.= "<span style='margin-right:5px;'><input".($id==1 ? " disabled='disabled'" : "")." type='checkbox'".( (in_array($value,$statut['available_for']) || $id == 1) ? " checked='checked'" : "")." name='form_available_for[]' value='".$value."'/> $name</span>";
			$i++;
		}

		$form = str_replace("!!list_entities!!", $pmb_entities, $form);

		$form.=confirmation_delete("./modelling.php?categ=contribution_area&sub=status&action=del&id=");
		$form = str_replace('!!libelle_suppr!!', addslashes($statut['label']), $form);
		$form = str_replace("!!id!!",$id,$form);
		print $form;
	}


	public function get_from_from(){
		global $id,$contribution_area_equation_name,$contribution_area_equation_type;
		global $contribution_area_equation_query, $contribution_area_equation_human_query;

		return array(
			'id' => intval($id),
			'name' => stripslashes($contribution_area_equation_name),
			'type' => stripslashes($contribution_area_equation_type),
			'query' => stripslashes($contribution_area_equation_query),
			'human_query' => stripslashes($contribution_area_equation_human_query)
		);

	}

	public function save($equation){
		$equation['id'] = intval($equation['id']);
		if($equation['name'] != ""){
			if($equation['id'] != 0){
				$query = " update contribution_area_equations set ";
				$where = "where contribution_area_equation_id = '".$equation['id']."'";
			}else{
				$query = " insert into contribution_area_equations set ";
				$where = "";
			}
			$query.="
				contribution_area_equation_name = '".addslashes($equation['name'])."',
				contribution_area_equation_type = '".addslashes($equation['type'])."',
				contribution_area_equation_query = '".addslashes($equation['query'])."',
				contribution_area_equation_human_query = '".addslashes($equation['human_query'])."' ";
			$result = pmb_mysql_query($query.$where);
			if($result){
				static::$equations_fetched = false;
			}else{
				return false;
			}
		}
		return true;
	}

	public static function delete($id) {
		$id = intval($id);
		if(!count(static::check_used($id))){
			$query = "delete from contribution_area_equations where contribution_area_equation_id = ".$id;
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}

	/**
	 * Fonction qui controle si le status de contribution est utilisé
	 * @param integer $id
	 * @return array:
	 */
	public static function check_used($id){

		$id = intval($id);
		$used = array();
		return $used;
	}

	private static function get_pmb_entities(){
		return contribution_area::get_pmb_entities();
	}

	/**
	 * Fonction permettant de générer le selecteur des equations définis pour un type d'autorité
	 * @param integer $auth_type Constante type d'autorité (ou 1000+id authperso)
	 * @param integer $contribution_area_id Identifiant du statut enregistré pour l'autorité courante
	 * @param boolean $selector_search Sélécteur affiché dans la page de recherche
	 * @return string
	 */
	public static function get_form_for($pmb_entity, $contribution_area_id, $search=false){
	    global $msg;
        $equations_defined = static::get_status_for($pmb_entity);
        $on_change='';
        if($search){
        	$on_change='onchange="if(this.form) this.form.submit();"';
        }
        $selector = '<select name="contribution_area_status" '.$on_change.' >';
        if($search){
            $selector.='<option value="0">'.$msg['contribution_area_status_selector_all'].'</option>';
        }
        foreach($equations_defined as $id_statut => $statut){
            $selector.='<option '.(($id_statut == $contribution_area_id)?'selected="selected"':'').' value="'.$id_statut.'">'.$statut['label'].'</option>';
        }
        $selector.= '</select>';
        return $selector;
	}


	public function add(){
		global $msg,$equation_type ;


		//type par defaut
		$type = 'record';

		if ($equation_type) {
			$type = $equation_type;
		}elseif ($this->type) {
			$type = $this->type;
		}

		$this->set_properties_form_type($type);

		$my_search=new $this->search_class_name(false,$this->xml_file_name);

		$form = "<h3>".$msg['admin_contribution_area_equation_type']."</h3>";

		$form .= $this->get_pmb_entities_selector();

		$form.= $my_search->show_form("./modelling.php?categ=contribution_area&sub=equation&action=build&equation_type=".$type."&id=".$this->id,
				"","","./modelling.php?categ=contribution_area&sub=equation&action=form&equation_type=".$type."&id=".$this->id);
		print $form;

	}

	public function get_pmb_entities_selector() {
		global $equation_type;

		$selected_type = '';

		if($equation_type) {
			$selected_type = $equation_type;
		}elseif ($this->type) {
			$selected_type = $this->type;
		}

		$on_change='onchange="document.location=\'./modelling.php?categ=contribution_area&sub=equation&section=liste&action=build&equation_type=\'+this.value+\'&id='.$this->id.'\'"';

		$selector = '<select name="contribution_area_equation_type" '.$on_change.' >';

		$pmb_entities = static::get_pmb_entities();
		if(count($pmb_entities)){
			foreach ($pmb_entities as $pmb_name => $name) {
				$selector.='<option '.(($selected_type == $pmb_name)?'selected="selected"':'').' value="'.$pmb_name.'">'.$name.'</option>';
			}
		}
		$selector.= '</select>';

		return $selector;
	}

	protected function load_xml($file_name) {
		global $pmb_opac_url,$lang,$base_path;

		// Recherche du fichier lang de l'opac
		$url=$pmb_opac_url."includes/messages/$lang.xml";
		$fichier_xml=$base_path."/temp/opac_lang.xml";
		curl_load_opac_file($url,$fichier_xml);

		$url=$pmb_opac_url."includes/search_queries/".$file_name.".xml";
		$fichier_xml="$base_path/temp/".$file_name."_opac.xml";
		curl_load_opac_file($url,$fichier_xml);
	}

	protected function set_properties_form_type($type) {
		//recherche sur le type d'equation
		switch ($type) {
			case 'record' :
				$this->set_xml_file_name("search_fields");
				$this->set_search_class_name("search");
				break;
			default:
				$this->set_xml_file_name("search_fields_authorities");
				$this->set_search_class_name("search_authorities");
				break;
		}
		return $this;
	}

	public function set_xml_file_name($file_name) {
		$this->xml_file_name = $file_name;
		return $this;
	}

	public function set_search_class_name($search_class_name) {
		$this->search_class_name = $search_class_name;
		return $this;
	}

	public function get_xml_file_name() {
		return $this->xml_file_name;
	}

	protected function get_search_class_name() {
		return $this->search_class_name;
	}

	public function do_form () {
		global $msg,$tpl_contribution_area_equation_form,$charset, $equation_type;
		global $id_equation;

		$type = $equation_type ?? $this->type;
		if (!empty($type)) {
		    $this->set_properties_form_type($type);
		}else {
			$this->set_properties_form_type("record");
		}

		// titre formulaire
		$my_search=new $this->search_class_name(false,$this->xml_file_name);

		if($this->id) {
			$libelle = $msg["admin_contribution_area_equation_edit"];
			$link_delete="<input type='button' class='bouton' value='".$msg[63]."' onClick=\"confirm_delete();\" />";
			$button_modif_requete = "<input type='button' class='bouton' value=\"".$msg["search_perso_modif_requete"]."\" onClick=\"document.modif_requete_form_".$this->id.".submit();\">";

			//Mémorisation de recherche prédéfinie en édition
			if ($id_equation) {
				$this->query=$my_search->serialize_search();
				$my_search->unserialize_search($this->query);
			} else {
				$my_search->unserialize_search($this->query);
				$this->query=$my_search->serialize_search();
			}
			$form_modif_requete = $this->make_hidden_search_form();
		} else {
			$libelle=$msg["admin_contribution_area_equation_add"];
			$link_delete="";
			$button_modif_requete = "";
			$form_modif_requete = "";

			$this->query=$my_search->serialize_search();
		}

		$this->human_query = $my_search->make_human_query();

		$type_equation = "record";
		if ($equation_type) {
			$type_equation = $equation_type;
		} elseif ($this->type) {
			$type_equation = $this->type;
		}

		$pmb_entities = static::get_pmb_entities();

		$tpl_contribution_area_equation_form = str_replace('!!id!!', htmlentities($this->id,ENT_QUOTES,$charset), $tpl_contribution_area_equation_form);
		$tpl_contribution_area_equation_form = str_replace('!!name!!', htmlentities($this->name,ENT_QUOTES,$charset), $tpl_contribution_area_equation_form);
		$tpl_contribution_area_equation_form = str_replace('!!type_label!!', $pmb_entities[$type_equation], $tpl_contribution_area_equation_form);
		$tpl_contribution_area_equation_form = str_replace('!!type!!', $type_equation, $tpl_contribution_area_equation_form);

		$action="./modelling.php?categ=contribution_area&sub=equation&action=save&id=".$this->id;
		$tpl_contribution_area_equation_form = str_replace('!!action!!', $action, $tpl_contribution_area_equation_form);
		$tpl_contribution_area_equation_form = str_replace('!!delete!!', $link_delete, $tpl_contribution_area_equation_form);
		$tpl_contribution_area_equation_form = str_replace('!!libelle!!',htmlentities($libelle,ENT_QUOTES,$charset) , $tpl_contribution_area_equation_form);

		$link_annul = "onClick=\"unload_off();history.go(-1);\"";
		$tpl_contribution_area_equation_form = str_replace('!!annul!!', $link_annul, $tpl_contribution_area_equation_form);

		$tpl_contribution_area_equation_form = str_replace('!!query!!', htmlentities($this->query,ENT_QUOTES,$charset), $tpl_contribution_area_equation_form);
		$tpl_contribution_area_equation_form = str_replace('!!requete_human!!', $this->human_query, $tpl_contribution_area_equation_form);
		$tpl_contribution_area_equation_form = str_replace('!!human_query!!', htmlentities($this->human_query,ENT_QUOTES,$charset), $tpl_contribution_area_equation_form);

		$tpl_contribution_area_equation_form = str_replace('!!bouton_modif_requete!!', $button_modif_requete,  $tpl_contribution_area_equation_form);
		$tpl_contribution_area_equation_form = str_replace('!!form_modif_requete!!', $form_modif_requete,  $tpl_contribution_area_equation_form);

		return $tpl_contribution_area_equation_form;
	}


	// pour maj de requete de recherche prédéfinie
	public function make_hidden_search_form() {
		global $search;
		global $charset;

		$url = "./modelling.php?categ=contribution_area&sub=equation&action=add&id=".$this->id ;

		$r="<form name='modif_requete_form_".$this->id."' action='$url' style='display:none' method='post'>";

		for ($i=0; $i<count($search); $i++) {
			$inter="inter_".$i."_".$search[$i];
			global ${$inter};
			$op="op_".$i."_".$search[$i];
			global ${$op};
			$field_="field_".$i."_".$search[$i];
			global ${$field_};
			$field=${$field_};
			//Récupération des variables auxiliaires
			$fieldvar_="fieldvar_".$i."_".$search[$i];
			global ${$fieldvar_};
			$fieldvar=${$fieldvar_};
			if (!is_array($fieldvar)) $fieldvar=array();

			$r.="<input type='hidden' name='search[]' value='".htmlentities($search[$i],ENT_QUOTES,$charset)."'/>";
			$r.="<input type='hidden' name='".$inter."' value='".htmlentities(${$inter},ENT_QUOTES,$charset)."'/>";
			$r.="<input type='hidden' name='".$op."' value='".htmlentities(${$op},ENT_QUOTES,$charset)."'/>";
			for ($j=0; $j<count($field); $j++) {
				$r.="<input type='hidden' name='".$field_."[]' value='".htmlentities($field[$j],ENT_QUOTES,$charset)."'/>";
			}
			reset($fieldvar);
			foreach ($fieldvar as $var_name => $var_value) {
				for ($j=0; $j<count($var_value); $j++) {
					$r.="<input type='hidden' name='".$fieldvar_."[".$var_name."][]' value='".htmlentities($var_value[$j],ENT_QUOTES,$charset)."'/>";
				}
			}
		}
		$r.="<input type='hidden' name='id_equation' value='$this->id'/>";
		$r.="</form>";
		return $r;
	}

	public static function get_list_by_type($type){
		$equations = array();
		$query = "	SELECT contribution_area_equation_id, contribution_area_equation_name, contribution_area_equation_type, contribution_area_equation_query, contribution_area_equation_human_query
					FROM contribution_area_equations ";
		if ($type) {
		    if ("bulletin" == $type) {//cas particulier pour le type bulletin
		        $type = "record";
		    }
			$query .="WHERE contribution_area_equation_type = '". $type ."' ";
		}
		$query .="	ORDER BY contribution_area_equation_name";

		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$equations[$row->contribution_area_equation_id] = array(
					'name' => $row->contribution_area_equation_name,
					'type' => $row->contribution_area_equation_type,
					'query' => unserialize($row->contribution_area_equation_query),
					'human_query' => $row->contribution_area_equation_human_query
				);
			}
		}
		return $equations;
	}

	private static function get_status_for($pmb_entity)
	{
		return [];
	}
}