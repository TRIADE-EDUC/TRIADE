<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_link.class.php,v 1.58.4.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
// gestion des liens entre autorités

global $class_path, $include_path;
require_once("$class_path/marc_table.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/editor.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/indexint.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/category.class.php");
require_once("$class_path/titre_uniforme.class.php");
require_once("$class_path/authperso.class.php");
require_once("$class_path/indexation_authority.class.php");
require_once("$class_path/indexation_authperso.class.php");
require_once("$class_path/onto/onto_index.class.php");
require_once("$class_path/authorities_collection.class.php");
require_once($include_path."/templates/aut_link.tpl.php");
require_once($class_path."/cache_factory.class.php");
require_once $class_path."/indexation_stack.class.php";

define('AUT_TABLE_AUTHORS',1);
define('AUT_TABLE_CATEG',2);
define('AUT_TABLE_PUBLISHERS',3);
define('AUT_TABLE_COLLECTIONS',4);
define('AUT_TABLE_SUB_COLLECTIONS',5);
define('AUT_TABLE_SERIES',6);
define('AUT_TABLE_TITRES_UNIFORMES',7);
define('AUT_TABLE_INDEXINT',8);
define('AUT_TABLE_AUTHPERSO',9);
define('AUT_TABLE_CONCEPT',10);
define('AUT_TABLE_INDEX_CONCEPT',11);
// Pour la classe authorities_collection
define('AUT_TABLE_CATEGORIES',12);
define('AUT_TABLE_AUTHORITY',13);
define('AUT_TABLE_FAQ',15);
// authperso >1000
define('AUT_TABLE_ANIMATION',14);

$aut_table_name_list=array(
	AUT_TABLE_AUTHORS => 'authors',
	AUT_TABLE_CATEG => 'categ',
	AUT_TABLE_PUBLISHERS=> 'publishers',
	AUT_TABLE_COLLECTIONS => 'collection',
	AUT_TABLE_SUB_COLLECTIONS => 'sub_collections',
	AUT_TABLE_SERIES => 'series',
	AUT_TABLE_TITRES_UNIFORMES => 'titres_uniformes',
	AUT_TABLE_INDEXINT => 'indexint',
	AUT_TABLE_CONCEPT => 'concept',
	AUT_TABLE_INDEX_CONCEPT => 'concept',
	AUT_TABLE_AUTHPERSO => 'authperso',
    AUT_TABLE_ANIMATION => 'anim_animations'
);

// définition de la classe de gestion des liens entre autorités
class aut_link {

	protected $aut_link_xml;
	public $aut_table;
	public $aut_table_name;
	public $id;
	protected $js_aut_link_table_list = ''; // nécesaire pour les aut perso..
	private static $onto_index;
	public $aut_list = array();

	public function __construct($aut_table,$id) {
		$this->aut_table = $aut_table;
		$this->id = intval($id);
		$this->getdata();
	}

	public function getdata() {
		global $msg;
		global $aut_table_name_list;
		global $pmb_opac_url;
		$this->parse_file();
		if($this->aut_table > 1000) {
			$this->aut_table_name = $aut_table_name_list[AUT_TABLE_AUTHPERSO];
		} else {
			$this->aut_table_name = $aut_table_name_list[$this->aut_table];
		}

		$rqt="select * from aut_link where (aut_link_from='".$this->aut_table."' and aut_link_from_num='".$this->id."')
		order by aut_link_type, aut_link_string_start_date, aut_link_string_end_date, aut_link_rank";
		$aut_res=pmb_mysql_query($rqt);
		$i=0;
		while ($row = pmb_mysql_fetch_object($aut_res)) {
			$this->aut_list[$i]['to'] = $row->aut_link_to;
			$this->aut_list[$i]['to_num'] = $row->aut_link_to_num;
			$this->aut_list[$i]['type'] = $row->aut_link_type;
			$this->aut_list[$i]['comment'] = $row->aut_link_comment;
			$this->aut_list[$i]['string_start_date'] = $row->aut_link_string_start_date;
			$this->aut_list[$i]['string_end_date'] = $row->aut_link_string_end_date;
			$this->aut_list[$i]['start_date'] = $row->aut_link_start_date;
			$this->aut_list[$i]['end_date'] = $row->aut_link_end_date;
			$this->aut_list[$i]['rank'] = $row->aut_link_rank;
			$this->aut_list[$i]['direction'] = $row->aut_link_direction;
			$this->aut_list[$i]['reverse_link_num'] = $row->aut_link_reverse_link_num;

			if($this->aut_list[$i]['reverse_link_num']) {
				$this->aut_list[$i]['flag_reciproc'] = 1;
			} else {
				$this->aut_list[$i]['flag_reciproc'] = 0;
			}

			switch($this->aut_list[$i]['to']){
				case AUT_TABLE_AUTHORS :
					$auteur = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $auteur->get_isbd();
					$this->aut_list[$i]['libelle'] = '['.$msg[133].'] '.$auteur->get_isbd();
				break;
				case AUT_TABLE_CATEG :
					$categ = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $categ->libelle;
					$this->aut_list[$i]['libelle'] = '['.$msg[134].'] '.$categ->libelle;
				break;
				case AUT_TABLE_PUBLISHERS :
					$ed = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $ed->get_isbd();
					$this->aut_list[$i]['libelle'] = '['.$msg[135].'] '.$ed->get_isbd();
				break;
				case AUT_TABLE_COLLECTIONS :
					$collection = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $collection->get_isbd();
					$this->aut_list[$i]['libelle'] = '['.$msg[136].'] '.$collection->get_isbd();
				break;
				case AUT_TABLE_SUB_COLLECTIONS :
					$subcollection = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $subcollection->get_isbd();
					$this->aut_list[$i]['libelle'] = '['.$msg[137].'] '.$subcollection->get_isbd();
				break;
				case AUT_TABLE_SERIES :
					$serie = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $serie->get_isbd();
					$this->aut_list[$i]['libelle'] = '['.$msg[333].'] '.$serie->get_isbd();
				break;
				case AUT_TABLE_TITRES_UNIFORMES :
					$tu = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry']=$tu->get_isbd();
					$this->aut_list[$i]['libelle']='['.$msg['aut_menu_titre_uniforme'].'] '.$tu->get_isbd();
				break;
				case AUT_TABLE_INDEXINT :
					$indexint = authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry']=$indexint->get_isbd();
					$this->aut_list[$i]['libelle']='['.$msg['indexint_menu'].'] '.$indexint->get_isbd();
				break;
				case AUT_TABLE_CONCEPT :
					$concept= authorities_collection::get_authority($this->aut_list[$i]['to'], $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry']=$concept->get_display_label();
					$this->aut_list[$i]['libelle']='['.$msg['concept_menu'].'] '.$concept->get_display_label();
				break;
				default:
					if($this->aut_list[$i]['to']>1000){
						// authperso
						$authperso = new authperso($this->aut_list[$i]['to']-1000);
						$isbd = authperso::get_isbd($this->aut_list[$i]['to_num']);
						$this->aut_list[$i]['isbd_entry']=$isbd;
						$this->aut_list[$i]['libelle']='['.$authperso->info['name'].'] '.$isbd;
						$this->aut_list[$i]['url_to_gestion']='./autorites.php?categ=authperso&sub=authperso_form&id_authperso=&id='.$this->aut_list[$i]['to_num'];
						$this->aut_list[$i]['url_to_opac']=$pmb_opac_url.'index.php?lvl=authperso_see&id='.$this->aut_list[$i]['to_num'];
					}

				break;
			}
			$relation = new marc_select("aut_link","f_aut_link_type$i", $this->aut_list[$i]['type']);
			$this->aut_list[$i]['relation_libelle'] = $relation->libelle;
			$i++;
		}
	}

	public function get_completion_table_name($table) {

	    switch ($table) {
	        case '1' :
	            $table_name = 'authors';
	            break;
	        case '2' :
	            $table_name =  'categories_mul';
	            break;
	        case '3' :
	            $table_name = 'publishers';
	            break;
	        case '4' :
	            $table_name = 'collections';
	            break;
	        case '5' :
	            $table_name = 'subcollections';
	            break;
	        case '6' :
	            $table_name = 'serie';
	            break;
	        case '7' :
	            $table_name = 'titre_uniforme';
	            break;
	        case '8' :
	            $table_name = 'indexint';
	            break;
	        case '10' :
	            $table_name = 'onto';
	            break;
	        default :
	            if ($table > 1000) {
	                $table_name = 'authperso_' . (intval($table) - 1000);
	            }
	            break;
	    }
	    return $table_name;
	}

	public function get_form($caller = "categ_form") {
	    global $msg,$add_aut_link,$aut_link0,$aut_link1,$form_aut_link;
		global $charset, $pmb_aut_link_autocompletion;

		$form = $add_aut_link;
		$this->js_aut_link_table_list = "
		var aut_link_table_select=Array();
		aut_link_table_select[".AUT_TABLE_AUTHORS."]='./select.php?what=auteur&caller=$caller&dyn=2&param1=';
		aut_link_table_select[".AUT_TABLE_CATEG."]='./select.php?what=categorie&caller=$caller&dyn=2&parent=1&p1=';
		aut_link_table_select[".AUT_TABLE_PUBLISHERS."]='./select.php?what=editeur&caller=$caller&dyn=2&p1=';
		aut_link_table_select[".AUT_TABLE_COLLECTIONS."]='./select.php?what=collection&caller=$caller&dyn=2&p1=';
		aut_link_table_select[".AUT_TABLE_SUB_COLLECTIONS."]='./select.php?what=subcollection&caller=$caller&dyn=2&p1=';
		aut_link_table_select[".AUT_TABLE_SERIES."]='./select.php?what=serie&caller=$caller&dyn=2&param1=';
		aut_link_table_select[".AUT_TABLE_TITRES_UNIFORMES."]='./select.php?what=titre_uniforme&caller=$caller&dyn=2&param1=';
		aut_link_table_select[".AUT_TABLE_INDEXINT."]='./select.php?what=indexint&caller=$caller&dyn=2&param1=';
		aut_link_table_select[".AUT_TABLE_CONCEPT."]='./select.php?what=ontology&caller=$caller&element=concept&dyn=2&param1=';
		";

        $aut_table_list = $this->generate_aut_type_selector($caller);

		$i = 0;
		if (!count($this->aut_list)) {
			// pas d'enregistrement
			$form.=$aut_link0;

			$relation = new marc_select("aut_link", "f_aut_link_type$i", "", "", "", "", array(array('name'=>'data-form-name','value'=>'f_aut_link_type')));

			$form = str_replace("!!aut_link_type!!", $relation->display, $form);
			$form = str_replace("!!aut_link_reciproc!!", "checked='checked'", $form);
			$form = str_replace("!!aut_link!!", $i, $form);
			$form = str_replace("!!aut_link_libelle!!", "", $form);
			$form = str_replace("!!aut_link_table!!", 1, $form);
			$form = str_replace("!!aut_link_id!!", "", $form);
			$form = str_replace("!!aut_link_comment!!", "", $form);
			$form = str_replace("!!aut_link_string_start_date!!", "", $form);
			$form = str_replace("!!aut_link_string_end_date!!", "", $form);
			$button_add = "<input id='button_add_f_aut_link' type='button' class='bouton_small' value='+' onClick='add_aut_link();'/>";
			if ($pmb_aut_link_autocompletion) {
			    $form = str_replace("!!aut_table_list!!", $this->generate_aut_type_selector($caller, 0, 0), $form);
			    $completion = " autfield = 'f_aut_link_id0' completion = '" . self::get_completion_table_name(1) . "' ";
			    $form = str_replace("!!completion!!", $completion, $form);
			} else {
			    $form = str_replace("!!aut_table_list!!", '', $form);
			    $form = str_replace("!!completion!!",  '',  $form);
			}
			$form = str_replace('!!button_add_aut_link!!', $button_add, $form);
			$i++;
		} else{
			foreach ($this->aut_list as $aut) {
				$button_add = '';
				// Construction de chaque ligne du formulaire
				if($i) {
					$form_suivant=$aut_link1;
				} else {
					$form_suivant=$aut_link0;
				}
				if ($aut['to_num'] == end($this->aut_list)['to_num']) {
					$button_add = "<input id='button_add_f_aut_link' type='button' class='bouton_small' value='+' onClick='add_aut_link();'/>";
				}
				$form_suivant=str_replace('!!button_add_aut_link!!', $button_add, $form_suivant);
				$relation = new marc_select("aut_link","f_aut_link_type$i", $aut["type"],"","","",array(array('name'=>'data-form-name','value'=>'f_aut_link_type')));

				$form_suivant=str_replace("!!aut_link_type!!",$relation->display,$form_suivant);
				if($aut["reverse_link_num"]) $check="checked='checked'"; else $check="";
				$form_suivant=str_replace("!!aut_link_reciproc!!",$check,$form_suivant);
				$form_suivant=str_replace("!!aut_link!!",$i,$form_suivant);
				$form_suivant=str_replace("!!aut_link_libelle!!",htmlentities($aut["libelle"],ENT_QUOTES, $charset,false),$form_suivant);
				$form_suivant=str_replace("!!aut_link_table!!",$aut["to"],$form_suivant);
				$form_suivant=str_replace("!!aut_link_id!!",$aut["to_num"],$form_suivant);
				$form_suivant=str_replace("!!aut_link_comment!!",$aut["comment"],$form_suivant);
				$form_suivant=str_replace("!!aut_link_string_start_date!!", $aut["string_start_date"], $form_suivant);
				$form_suivant=str_replace("!!aut_link_string_end_date!!", $aut["string_end_date"], $form_suivant);
				if ($pmb_aut_link_autocompletion) {
				    $form_suivant = str_replace("!!aut_table_list!!", $this->generate_aut_type_selector($caller, $aut["to"], $i), $form_suivant);
				    $completion = " autfield = 'f_aut_link_id" . $i . "' completion = '" . self::get_completion_table_name($aut["to"]) . "' ";
				    if ($aut["type"] == 10) {
				        $completion.= " att_id_filter = 'http://www.w3.org/2004/02/skos/core#Concept' ";
				    }
				    $form_suivant = str_replace("!!completion!!", $completion, $form_suivant);
				} else {
				    $form_suivant = str_replace("!!aut_table_list!!", '', $form_suivant);
				    $form_suivant = str_replace("!!completion!!", '', $form_suivant);
				}
				$form.= $form_suivant;
				$i++;
			}
		}
		$form = str_replace("!!max_aut_link!!", $i, $form);
		$form = str_replace("!!js_aut_link_table_list!!", $this->js_aut_link_table_list, $form);
		$form = str_replace("!!aut_table_list!!", $aut_table_list, $form);
		if (!$aut_table_list && !count($this->aut_list)) {
		    $form_aut_link = str_replace("!!aut_table_list!!", '', $form_aut_link);
			return str_replace("!!aut_link_contens!!", $msg['no_aut_link'], $form_aut_link);
		}
		if (!$pmb_aut_link_autocompletion) {
		    $form_aut_link = str_replace("!!aut_table_list!!", $aut_table_list , $form_aut_link);
		} else {
		    $form_aut_link = str_replace("!!aut_table_list!!", '', $form_aut_link);
		}
		return str_replace("!!aut_link_contens!!", $form , $form_aut_link);
	}

	public function save_form() {

		global $max_aut_link;
		if(!$this->aut_table && !$this->id) return;
		$relations = new marc_list("aut_link");
		$direction = '';
		$this->delete_link();
		for($i=0;$i<$max_aut_link;$i++){
			global ${"f_aut_link_table".$i};
			global ${"f_aut_link_id".$i};
			global ${"f_aut_link_type".$i};
			global ${"f_aut_link_reciproc".$i};
			global ${"f_aut_link_comment".$i};
			global ${"f_aut_link_string_start_date".$i};
			global ${"f_aut_link_string_end_date".$i};

			$f_aut_link_table = ${"f_aut_link_table".$i};
			$f_aut_link_id = ${"f_aut_link_id".$i};
			$f_aut_link_type = ${"f_aut_link_type".$i};
			$f_aut_link_reciproc = ${"f_aut_link_reciproc".$i};
			$f_aut_link_comment = ${"f_aut_link_comment".$i};
			$f_aut_link_string_start_date = ${"f_aut_link_string_start_date".$i};
			$f_aut_link_string_end_date = ${"f_aut_link_string_end_date".$i};

			$f_aut_link_start_date = detectFormatDate($f_aut_link_string_start_date);
			$f_aut_link_end_date = detectFormatDate($f_aut_link_string_end_date, "max");

			// Les selecteurs de concept retourne l'uri et non id
			if($f_aut_link_table==AUT_TABLE_CONCEPT && !is_numeric($f_aut_link_id)){
				$f_aut_link_id=onto_common_uri::get_id($f_aut_link_id);
			}
			if($f_aut_link_reciproc)$f_aut_link_reciproc=1;

			$direction = 'up';
			if (array_key_exists($f_aut_link_type, $relations->table['ascendant'])) {
			    $direction = 'up';
			} elseif (array_key_exists($f_aut_link_type, $relations->table['descendant'])) {
			    $direction = 'down';
			}
			if($f_aut_link_id && $f_aut_link_table && $f_aut_link_type && !(($this->aut_table == $f_aut_link_table) && ($this->id == $f_aut_link_id))) {
	 			$requete="INSERT INTO aut_link SET
                    aut_link_from='".$this->aut_table."',
                    aut_link_from_num='".$this->id."',
                    aut_link_to='".$f_aut_link_table."',
                    aut_link_to_num='".$f_aut_link_id."',
                    aut_link_type='".$f_aut_link_type."',
                    aut_link_comment='".$f_aut_link_comment."',
                    aut_link_string_start_date='" . $f_aut_link_string_start_date . "',
                    aut_link_string_end_date='" . $f_aut_link_string_end_date . "',
                    aut_link_start_date='" . $f_aut_link_start_date . "',
                    aut_link_end_date='" . $f_aut_link_end_date . "',
                    aut_link_rank='" . $i . "',
                    aut_link_direction='" . $direction . "'
                ";
				pmb_mysql_query($requete);
				$last_id = pmb_mysql_insert_id();
				if ($f_aut_link_reciproc) {
				    $type = $relations->inverse_of[$f_aut_link_type];
				    if ($direction === "up") {
				        $direction = "down";
				    } else {
				        $direction = "up";
				    }
				    $requete="INSERT INTO aut_link SET
                        aut_link_from='" . $f_aut_link_table . "',
                        aut_link_from_num='" . $f_aut_link_id . "',
                        aut_link_to='" . $this->aut_table . "',
                        aut_link_to_num='" . $this->id . "',
                        aut_link_type='" . $type . "',
                        aut_link_comment='" . $f_aut_link_comment . "',
                        aut_link_string_start_date='" . $f_aut_link_string_start_date . "',
                        aut_link_string_end_date='" . $f_aut_link_string_end_date . "',
                        aut_link_start_date='" . $f_aut_link_start_date . "',
                        aut_link_end_date='" . $f_aut_link_end_date . "',
                        aut_link_rank='" . $i . "',
                        aut_link_direction='" . $direction . "',
                        aut_link_reverse_link_num='" . $last_id . "'
                    ";
				    pmb_mysql_query($requete);
				    $reciproc_id = pmb_mysql_insert_id();
				    $requete = "UPDATE aut_link SET aut_link_reverse_link_num=" . $reciproc_id . " WHERE id_aut_link=" . $last_id;
				    pmb_mysql_query($requete);
				}
			}
		}
	}

	// delete tous les liens (from vers to) de cette autorité
	public function delete_link() {
		if(!$this->aut_table && !$this->id) return;
		$query = "SELECT aut_link_to_num, aut_link_to FROM aut_link WHERE aut_link_from_num='".$this->id."' and aut_link_from='".$this->aut_table."'";
		$result = pmb_mysql_query($query);
		pmb_mysql_query("DELETE FROM aut_link WHERE aut_link_from='".$this->aut_table."' and aut_link_from_num='".$this->id."' ");
		if(pmb_mysql_num_rows($result)) {
		    while($row = pmb_mysql_fetch_object($result)) {
		        $entity_type = authority::aut_const_to_type_const(($row->aut_link_to > 1000 ? 9 : $row->aut_link_to));
		        indexation_stack::push($row->aut_link_to_num, $entity_type, "aut_link");
			}
		}
		$query = "SELECT aut_link_from_num, aut_link_from FROM aut_link WHERE aut_link_to_num='".$this->id."' and aut_link_to='".$this->aut_table."' ";
		$result = pmb_mysql_query($query);
		pmb_mysql_query("DELETE FROM aut_link WHERE aut_link_to='".$this->aut_table."' and aut_link_to_num='".$this->id."' ");
		if(pmb_mysql_num_rows($result)) {
		    while($row = pmb_mysql_fetch_object($result)) {
		        $entity_type = authority::aut_const_to_type_const(($row->aut_link_from > 1000 ? 9 : $row->aut_link_from));
		        indexation_stack::push($row->aut_link_from_num, $entity_type, "aut_link");
			}
		}
	}

	// delete tous les liens (from et to) de cette autorité
	public function delete($indexation_active = true) {
		if(!$this->aut_table && !$this->id) return;
		if ($indexation_active) {
    		$query = "SELECT aut_link_to_num, aut_link_to FROM aut_link WHERE aut_link_from_num='".$this->id."' and aut_link_from='".$this->aut_table."'";
    		$result = pmb_mysql_query($query);
		}
		pmb_mysql_query("DELETE FROM aut_link WHERE aut_link_from='".$this->aut_table."' and aut_link_from_num='".$this->id."' ");

		if(pmb_mysql_num_rows($result) && $indexation_active) {
		    while($row = pmb_mysql_fetch_object($result)) {
		        $entity_type = authority::aut_const_to_type_const(($row->aut_link_to > 1000 ? 9 : $row->aut_link_to));
		        indexation_stack::push($row->aut_link_to_num, $entity_type, "aut_link");
			}
		}

		if ($indexation_active) {
    		$query = "SELECT aut_link_from_num, aut_link_from FROM aut_link WHERE aut_link_to_num='".$this->id."' and aut_link_to='".$this->aut_table."'";
    		$result = pmb_mysql_query($query);
		}
		pmb_mysql_query("DELETE FROM aut_link WHERE aut_link_to='".$this->aut_table."' and aut_link_to_num='".$this->id."' ");

		if(pmb_mysql_num_rows($result) && $indexation_active) {
		    while($row = pmb_mysql_fetch_object($result)) {
		        $entity_type = authority::aut_const_to_type_const(($row->aut_link_from > 1000 ? 9 : $row->aut_link_from));
		        indexation_stack::push($row->aut_link_from_num, $entity_type, "aut_link");
			}
		}
	}

	// copie les liens from et to par une autre autorité
	public function add_link_to($copy_table,$copy_num) {
	    if(!$this->aut_table && !$this->id && !$copy_table && !$copy_num) return;
		$i = 0;
		$relations = new marc_list("aut_link");
		foreach ($this->aut_list as $aut) {
		    //on verifie l'existence du lien pour éviter les doublons (notament créés lors de la duplication).
    		$requete = "SELECT * FROM aut_link
                        WHERE
                        aut_link_from='".$copy_table."',
                        aut_link_to='".$aut["to"]."'";
    		$result = pmb_mysql_query($requete);
    		$nb_link = pmb_mysql_num_rows($result);
    		if ($nb_link) {
    		    continue; //il y a déja ce lien, on passe au suivant
    		}

		    $requete = "INSERT INTO aut_link SET
                aut_link_from='".$copy_table."',
                aut_link_from_num='".$copy_num."',
                aut_link_to='".$aut["to"]."',
                aut_link_to_num='".$aut["to_num"]."',
                aut_link_type='".$aut["type"]."',
                aut_link_comment='".$aut["comment"]."',
                aut_link_string_start_date='" . $aut["string_start_date"] . "',
                aut_link_string_end_date='" . $aut["string_end_date"] . "',
                aut_link_start_date='" . $aut["start_date"] . "',
                aut_link_end_date='" . $aut["end_date"] . "',
                aut_link_rank='" . $i . "',
                aut_link_direction='" . $aut["direction"] . "'
            ";
		    pmb_mysql_query($requete);
		    $last_id = pmb_mysql_insert_id();
		    if ($aut["flag_reciproc"]) {
		        $type = $relations->inverse_of[$aut["type"]];
		        if ($aut["direction"] === "up") {
		            $direction = "down";
		        } else {
		            $direction = "up";
		        }
		        $requete = "INSERT INTO aut_link SET
                    aut_link_from='" . $aut["to"] . "',
                    aut_link_from_num='" . $aut["to_num"] . "',
                    aut_link_to='" . $copy_table . "',
                    aut_link_to_num='" . $copy_num . "',
                    aut_link_type='" . $type . "',
                    aut_link_comment='".$aut["comment"]."',
                    aut_link_string_start_date='" . $aut["string_start_date"] . "',
                    aut_link_string_end_date='" . $aut["string_end_date"] . "',
                    aut_link_start_date='" . $aut["start_date"] . "',
                    aut_link_end_date='" . $aut["end_date"] . "',
                    aut_link_rank='" . $i . "',
                    aut_link_direction='" . $direction . "',
                    aut_link_reverse_link_num='" . $last_id . "'
                ";
		        pmb_mysql_query($requete);
		        $reciproc_id = pmb_mysql_insert_id();
		        $requete = "UPDATE aut_link SET aut_link_reverse_link_num=" . $reciproc_id . " WHERE id_aut_link=" . $last_id;
		        pmb_mysql_query($requete);
		    }
		    $i++;
		}
	}

	public function get_display($caller="categ_form") {
		if(!count($this->aut_list)) return"";

		$aut_see_link = "./autorites.php?categ=see&sub=!!type!!&id=!!to_num!!";

		$marc = marc_list_collection::get_instance("aut_link");
		$liste_type_relation = $marc->table;

		$aff="<ul>";
		foreach ($this->aut_list as $aut) {
			$type = $this->get_type_from_const(($aut['to']>1000?9:$aut['to']));
			$aff.="<li>";
			if($aut['direction'] == 'up') {
			    $aff.= (!empty($liste_type_relation['ascendant'][$aut['type']]) ? $liste_type_relation['ascendant'][$aut['type']] : "")." : ";
			} else	{
			    $aff.= ($liste_type_relation['descendant'][$aut['type']] ?? "")." : ";
			}
			$link =str_replace("!!to_num!!",$aut['to_num'],$aut_see_link);
			$link = str_replace("!!type!!",$type,$link);
			$aff.=" <a href='".$link."'>".$aut['libelle']."</a>";
			$aff_dates = '';
			if ($aut['string_start_date']) {
			    $aff_dates.= $aut['string_start_date'];
			}
			if ($aff_dates && $aut['string_end_date']) {
			    $aff_dates.= ' - ';
			}
			if ($aut['string_end_date']) {
			    $aff_dates.= $aut['string_end_date'];
			}
			if ($aff_dates && !$aut['comment']) {
			    $aff.= " (" . $aff_dates . ")";
			}
			if($aut['comment']) {
			    $aff.= " (" . $aff_dates . ' ' . $aut['comment'] . ")";
			}
			$aff.="</li>";
		}
		$aff.="</ul>";
		return $aff;
	}

	/**
	 * Parse le fichier xml
	 */
	private function parse_file() {
		global $base_path, $include_path, $charset;
		global $KEY_CACHE_FILE_XML;

		$filepath = $include_path."/authorities/aut_links_subst.xml";
		if (!file_exists($filepath)) {
			$filepath = $include_path."/authorities/aut_links.xml";
		}

		$fileInfo = pathinfo($filepath);
		$fileName = preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset);
		$tempFile = $base_path."/temp/XML".$fileName.".tmp";
		$dejaParse = false;

		$cache_php=cache_factory::getCache();
		$key_file="";
		if ($cache_php) {
			$key_file=getcwd().$fileName.filemtime($filepath);
			$key_file=$KEY_CACHE_FILE_XML.md5($key_file);
			if($tmp_key = $cache_php->getFromCache($key_file)){
				if($cache = $cache_php->getFromCache($tmp_key)){
					if(count($cache) == 1){
						$this->aut_link_xml = $cache[0];
						$dejaParse = true;
					}
				}
			}

		}else{
		if (file_exists($tempFile) ) {
			//Le fichier XML original a-t-il été modifié ultérieurement ?
			if (filemtime($filepath) > filemtime($tempFile)) {
				//on va re-générer le pseudo-cache
				if($tempFile && file_exists($tempFile)){
					unlink($tempFile);
				}
			} else {
				$dejaParse = true;
			}
		}
		if ($dejaParse) {
			$tmp = fopen($tempFile, "r");
			$cache = unserialize(fread($tmp,filesize($tempFile)));
			fclose($tmp);
			if(count($cache) == 1){
				$this->aut_link_xml = $cache[0];
			}else{
				//SOUCIS de cache...
				if($tempFile && file_exists($tempFile)){
					unlink($tempFile);
				}
					$dejaParse = false;
				}
			}
			}

		if(!$dejaParse){
			$fp=fopen($filepath,"r") or die("Can't find XML file");
			$size=filesize($filepath);

			$xml=fread($fp,$size);
			fclose($fp);
			$aut_links = _parser_text_no_function_($xml, "AUT_LINKS", $filepath);

			$this->aut_link_xml = array();
			$aut_def = array();
			foreach($aut_links['DEFINITION'][0]['ENTRY'] as $xml_aut_definition){
				$aut_def[$xml_aut_definition['CODE']] = $xml_aut_definition['value'];
			}

			/**
			 * Le résultat du parse du fichier xml est stocké en temps que tableau sérialisé dans le fichier tempo
			 */
			//Lecture des liens
			foreach ($aut_links['LINKS'][0]['AUTHORITY'] as $main_authority) {
				$aut_allowed = array();
				if($main_authority['AUTHORITY_ALLOWED']){
					foreach($main_authority['AUTHORITY_ALLOWED'] as $sub_aut_allowed){
						if(isset($aut_def[$sub_aut_allowed['value']])){
							$aut_allowed[] = $aut_def[$sub_aut_allowed['value']];
						}

					}
				}
				if(isset($aut_def[$main_authority['CODE']])){
					$this->aut_link_xml[$aut_def[$main_authority['CODE']]]['aut_to_display'] = $aut_allowed;
				}
			}

			if ($key_file) {
				$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($this->aut_link_xml)));
				$cache_php->setInCache($key_file_content, array($this->aut_link_xml));
				$cache_php->setInCache($key_file,$key_file_content);
			}else{
				$tmp = fopen($tempFile, "wb");
				fwrite($tmp,serialize(array($this->aut_link_xml)));
				fclose($tmp);
			}
		}
	}

	public static function get_type_from_const($const){
		switch($const){
			case "1" :
				return "author";
			case "2" :
				return "category";
			case "3" :
				return "publisher";
			case "4" :
				return "collection";
			case "5" :
				return "subcollection";
			case "6" :
				return "serie";
			case "7" :
				return "titre_uniforme";
			case "8" :
				return "indexint";
			case "9" :
				return "authperso";
			case "10" :
				return "concept";
		}
	}

	public function generate_aut_type_selector($caller="categ_form", $aut_sel=0, $index=0){
		global $msg;
		global $thesaurus_concepts_active;
		global $form_aut_link_buttons, $pmb_aut_link_autocompletion;

		if ($pmb_aut_link_autocompletion) {
		    $aut_table_list="<select class='aut_link_authorities_selector' id='f_aut_link_table_list_" . $index . "' name='f_aut_link_table_list_" . $index . "' onchange = 'onchange_aut_link_selector($index)'>";
		} else {
		    $aut_table_list="<select class='aut_link_authorities_selector' id='f_aut_link_table_list' name='f_aut_link_table_list'>";
		}
		$options = '';
		//Cas à gérer pour les autorités persos
		$auth_type = ($this->aut_table <= 1000 ? $this->aut_table : 9);
		$first = 0;
		foreach($this->aut_link_xml[$auth_type]['aut_to_display'] as $aut_to_display){
		    $selected = '';
		    if ((!$aut_sel && !$first) || ($aut_to_display == $aut_sel)) {
		        $selected = ' selected="selected" ';
		    }
		    $first = 1;
			switch($aut_to_display){
				case '1':
					$options.= '<option value="'.AUT_TABLE_AUTHORS.'" ' . $selected. '>'.$msg["133"].'</option>';
					break;
				case '2':
					$options.= '<option value="'.AUT_TABLE_CATEG.'" ' . $selected. '>'.$msg['134'].'</option>';
					break;
				case '3':
					$options.= '<option value="'.AUT_TABLE_PUBLISHERS.'" ' . $selected. '>'.$msg['135'].'</option>';
					break;
				case '4':
					$options.= '<option value="'.AUT_TABLE_COLLECTIONS.'">'.$msg['136'].'</option>';
					break;
				case '5':
					$options.= '<option value="'.AUT_TABLE_SUB_COLLECTIONS.'" ' . $selected. '>'.$msg['137'].'</option>';
					break;
				case '6':
					$options.= '<option value="'.AUT_TABLE_SERIES.'" ' . $selected. '>'.$msg['333'].'</option>';
					break;
				case '7':
					$options.= '<option value="'.AUT_TABLE_TITRES_UNIFORMES.'" ' . $selected. '>'.$msg['aut_menu_titre_uniforme'].'</option>';
					break;
				case '8':
					$options.= '<option value="'.AUT_TABLE_INDEXINT.'" ' . $selected. '>'.$msg['indexint_menu'].'</option>';
					break;
				case '9':
					$authpersos = authpersos::get_instance();
					$info=$authpersos->get_data();
					foreach($info as $elt){
					    $selected = '';
					    if ($pmb_aut_link_autocompletion) {
					        if(($elt['id'] + 1000) == $aut_sel) {
					            $selected = ' selected="selected" ';
					        }
					    }
						$tpl_elt="<option value='!!id_authperso!!' " . $selected. ">!!name!!</option>";
						$tpl_elt=str_replace('!!name!!',$elt['name'], $tpl_elt);
						$tpl_elt=str_replace('!!id_authperso!!',$elt['id'] + 1000, $tpl_elt);
						$this->js_aut_link_table_list.="aut_link_table_select[".($elt['id'] + 1000)."]='./select.php?what=authperso&authperso_id=".$elt['id']."&caller=$caller&dyn=2&param1=';";
						$options.= $tpl_elt;
					}
					break;
				case '10':
					if($thesaurus_concepts_active){
						$options.= '<option value="'.AUT_TABLE_CONCEPT.'" ' . $selected. '>'.$msg['ontology_skos_menu'].'</option>';
					}
					break;
			}
		}
		if($options){
		    $add_button = $form_aut_link_buttons;
		    $add_button = str_replace("!!index!!", $index, $add_button);
		    return $aut_table_list.$options.'</select>' . $add_button;
		}
		return '';
	}
	public function get_hidden_values_already_exist()
	{
	    global $max_aut_link, $charset;
	    $html = "";
	    if (!empty($max_aut_link)) {
            $html .= "<input type='hidden' name='max_aut_link' value='$max_aut_link' />";
    	    for($i=0;$i<$max_aut_link;$i++){
    	        global ${"f_aut_link_table$i"};
    	        $f_aut_link_table= ${"f_aut_link_table$i"};
    	        $html .= "<input type='hidden' name='f_aut_link_table$i' value='$f_aut_link_table' />";

    	        global ${"f_aut_link_id$i"};
    	        $f_aut_link_id= ${"f_aut_link_id$i"};
    	        $html .= "<input type='hidden' name='f_aut_link_id$i' value='$f_aut_link_id' />";

    	        global ${"f_aut_link_type$i"};
    	        $f_aut_link_type = ${"f_aut_link_type$i"};
    	        $html .= "<input type='hidden' name='f_aut_link_type$i' value='$f_aut_link_type' />";

    	        global ${"f_aut_link_reciproc$i"};
    	        $f_aut_link_reciproc = ${"f_aut_link_reciproc$i"};
    	        $html .= "<input type='hidden' name='f_aut_link_reciproc$i' value='$f_aut_link_reciproc' />";

    	        global ${"f_aut_link_comment$i"};
    	        $f_aut_link_comment = ${"f_aut_link_comment$i"};
    	        $html .= "<input type='hidden' name='f_aut_link_comment$i' value='".htmlentities(stripslashes($f_aut_link_comment), ENT_QUOTES, $charset)."' />";

    	        global ${"f_aut_link_string_start_date$i"};
    	        $f_aut_link_string_start_date = ${"f_aut_link_string_start_date$i"};
    	        $html .= "<input type='hidden' name='f_aut_link_string_start_date$i' value='".htmlentities(stripslashes($f_aut_link_string_start_date), ENT_QUOTES, $charset)."' />";

    	        global ${"f_aut_link_string_end_date$i"};
    	        $f_aut_link_string_end_date = ${"f_aut_link_string_end_date$i"};
    	        $html .= "<input type='hidden' name='f_aut_link_string_end_date$i' value='".htmlentities(stripslashes($f_aut_link_string_end_date), ENT_QUOTES, $charset)."' />";
    	    }
	    }
	    return $html;
	}

	public function get_aut_list() {
	    if (!isset($this->aut_list)) {
	        return $this->getdata();
	    }
	    return $this->aut_list;
	}
// fin class
}