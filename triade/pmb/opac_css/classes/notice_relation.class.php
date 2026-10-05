<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_relation.class.php,v 1.14.2.2 2024/09/05 07:43:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/notice_relations.class.php");

class notice_relation {


	protected $id;

	protected $reverse_instance;

	protected $num_notice;

	protected $linked_notice;

	protected $relation_type;

	protected $ranking;

	protected $direction;

	protected $num_reverse_link;

	protected $niveau_biblio;

	protected $niveau_hierar;

	/**
	 *
	 * @var notice_relation
	 */
	protected $reverse_notice_relation;

	protected $parent_niveau_biblio;

	protected $parent_niveau_hierar;

	protected $to_delete;

	protected $serial_id;
	
	protected static $childs_records = [];

	public function __construct($id=0, $reverse_instance=true) {
		$this->id = intval($id);
		$this->reverse_instance = $reverse_instance;
		$this->fetch_data();
	}

	/**
	 * Data
	 */
	protected function fetch_data() {
		$this->num_notice = 0;
		$this->linked_notice = 0;
		$this->relation_type = '';
		$this->ranking = 0;
		$this->direction = '';
		$this->num_reverse_link = 0;
		$this->niveau_biblio = '';
		$this->niveau_hierar = '';
		$this->to_delete = false;
		$this->serial_id = 0;
		if($this->id) {
			$query = "select num_notice, linked_notice, relation_type, ranking, direction, num_reverse_link, niveau_biblio, niveau_hierar
					from notices_relations join notices on notice_id=num_notice
					where id_notices_relations = ".$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->num_notice = $row->num_notice;
			$this->linked_notice = $row->linked_notice;
			$this->relation_type = $row->relation_type;
			$this->ranking = $row->ranking;
			$this->direction = $row->direction;
			$this->num_reverse_link = $row->num_reverse_link;
			$this->niveau_biblio = $row->niveau_biblio;
			$this->niveau_hierar = $row->niveau_hierar;

			if($this->num_reverse_link && $this->reverse_instance) {
				$this->reverse_notice_relation = new notice_relation($this->num_reverse_link, false);
			}
			if($this->niveau_biblio == 'b') {
				$query = 'select bulletin_notice from bulletins where num_notice ='.$this->num_notice;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_object($result);
					$this->serial_id = $row->bulletin_notice;
				}
			}
		}
	}

	public function get_form($n_rel, $niveau_biblio='m') {
		global $charset;
		global $notice_relations_link_tpl;

		if($this->linked_notice) {
			$query = "select niveau_biblio from notices where notice_id=".$this->linked_notice;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			switch ($row->niveau_biblio) {
				case 's':
					$called_class = 'serial';
					break;
				case 'b':
					$called_class = 'bulletinage';
					break;
				case 'a':
					$called_class = 'analysis';
					break;
				case 'm':
					$called_class = 'notice';
					break;
			}
		}

		//Selection du template
		$form=$notice_relations_link_tpl;

		//Construction du textbox
		$form=str_replace("!!notice_relations_id!!",$this->linked_notice,$form);
		$form=str_replace("!!notice_relations_libelle!!",($this->linked_notice ? htmlentities($called_class::get_notice_title($this->linked_notice),ENT_QUOTES,$charset) : ''),$form);
		$form=str_replace("!!n_rel!!",$n_rel,$form);
		$form=str_replace("!!linked_notice_is_disabled!!",($this->id ? "disabled='disabled'" : ""),$form);
		$form=str_replace("!!linked_notice_button_is_hidden!!",($this->id ? "style='display:none'" : ""),$form);

		//Construction du combobox de type de lien
		$add_reverse_link_checked = '';
		if($this->id) {
			$selector = $this->get_selector_from_relation('f_rel_type_'.$n_rel, 'update_rel_reverse_type(this, '.$n_rel.');');
			if ($this->num_reverse_link) {
				$add_reverse_link_checked = "checked='checked'";
			}
		} else {
			$deflt_relation = notice_relations::get_default_relation_type($niveau_biblio);
			$selector = notice_relations::get_selector('f_rel_type_'.$n_rel, $deflt_relation, 'update_rel_reverse_checked(this, '.$n_rel.'); update_rel_reverse_type(this, '.$n_rel.');');
			$deflt_relation_type=explode('-', $deflt_relation)[0];
			$deflt_direction=explode('-', $deflt_relation)[1];
			if(notice_relations::$liste_type_relation[$deflt_direction]->attributes[$deflt_relation_type]['REVERSE_CODE_DEFAULT_CHECKED']=='YES') {
				$add_reverse_link_checked = "checked='checked'";
			}
		}
		$form=str_replace("!!relations_links_selector!!", $selector, $form);

		$form=str_replace("!!add_reverse_link!!", $add_reverse_link_checked, $form);
		if ($add_reverse_link_checked) {
			$form=str_replace("!!checked_dflt_reverse_link!!", "checkbox_f_rel0_add_reverse_link.setAttribute('checked','checked');", $form);
		} else {
			$form=str_replace("!!checked_dflt_reverse_link!!", "", $form);
		}

		//Construction du combobox de type de lien associé
		if(isset($this->reverse_notice_relation)) {
			$reverse_selector = $this->reverse_notice_relation->get_selector_from_relation('f_rel_reverse_type_'.$n_rel);
			$form = str_replace('!!f_rel_add_reverse_link_action!!',' onChange = \'update_add_reverse_link_action('.$n_rel.', this.checked);\'',$form);
 		} else {
 			if ($this->id) {
				$reverse_selector = notice_relations::get_selector('f_rel_reverse_type_'.$n_rel, notice_relations::$liste_type_relation[$this->direction]->attributes[$this->relation_type]['REVERSE_CODE'].'-'.notice_relations::$liste_type_relation[$this->direction]->attributes[$this->relation_type]['REVERSE_DIRECTION']);
 			} else {
				$reverse_selector = notice_relations::get_selector('f_rel_reverse_type_'.$n_rel, notice_relations::get_default_reverse_relation_type($niveau_biblio));
 			}
			$form = str_replace('!!f_rel_add_reverse_link_action!!','',$form);
		}
		$form = str_replace('!!del_action!!','raz_existing_rel('.$n_rel.');',$form);
		$form=str_replace("!!relations_reverse_links_selector!!", $reverse_selector, $form);

		//Champs cachés
		$form=str_replace("!!id_notices_relations!!", $this->id, $form);
		$form=str_replace("!!num_reverse_link!!", $this->num_reverse_link, $form);

		return $form;
	}

	protected function get_selector_options($direction='') {
		$options = '';
		if ($this->num_notice) {
			foreach(notice_relations::$liste_type_relation[$direction]->table as $key=>$val){
				$reverse_code = notice_relations::$liste_type_relation[$direction]->attributes[$key]['REVERSE_CODE'];
				$reverse_direction = notice_relations::$liste_type_relation[$direction]->attributes[$key]['REVERSE_DIRECTION'];
				if(preg_match('/^'.$key.'/', $this->relation_type) && $this->direction==$direction){
					$options.='<option  style="color:#000000" value="'.$key.'-'.$direction.'" selected="selected" data-reverse-code="'.$reverse_code.'-'.$reverse_direction.'">'.$val.'</option>';
				}else{
					$options.='<option  style="color:#000000" value="'.$key.'-'.$direction.'" data-reverse-code="'.$reverse_code.'-'.$reverse_direction.'">'.$val.'</option>';
				}
			}
		} else {
			$default_relation_type = notice_relations::get_default_relation_type($this->parent_niveau_biblio);
			foreach(notice_relations::$liste_type_relation[$direction]->table as $key=>$val){
				$reverse_code = notice_relations::$liste_type_relation[$direction]->attributes[$key]['REVERSE_CODE'];
				$reverse_direction = notice_relations::$liste_type_relation[$direction]->attributes[$key]['REVERSE_DIRECTION'];
				if($key.'-'.$direction == $default_relation_type){
					$options.='<option  style="color:#000000" value="'.$key.'-'.$direction.'" selected="selected" data-reverse-code="'.$reverse_code.'-'.$reverse_direction.'">'.$val.'</option>';
				}else{
					$options.='<option  style="color:#000000" value="'.$key.'-'.$direction.'" data-reverse-code="'.$reverse_code.'-'.$reverse_direction.'">'.$val.'</option>';
				}
			}
		}
		return $options;
	}

	public function get_selector_from_relation($name='', $on_change='') {
		global $msg;

		$select = "
			<select onchange='".$on_change."' id='".$name."' name='".$name."' size='1'>
				<optgroup class='erreur' label='".$msg['notice_lien_montant']."'>";

		$select .= $this->get_selector_options('up');
		$select .= "
				</optgroup>
				<optgroup class='erreur' label='".$msg['notice_lien_descendant']."'>";
		$select .= $this->get_selector_options('down');
		$select .= "
				</optgroup>
				<optgroup class='erreur' label='".$msg['notice_lien_symetrique']."'>";
		$select .= $this->get_selector_options('both');
		$select .= "
				</optgroup>
			</select>";
		return $select;
	}

	public function set_properties_from_form($i) {
		
		global $is_duplication;
		
		$f_rel_id="f_rel_id_".$i;
		$f_rel="f_rel_type_".$i;
		$f_rel_add_reverse_link="f_rel_add_reverse_link_".$i;
		$f_rel_reverse="f_rel_reverse_type_".$i;
		$f_rel_id_notices_relations="f_rel_id_notices_relations_".$i;
		$f_rel_num_reverse_link="f_rel_num_reverse_link_".$i;
		$f_rel_delete_link="f_rel_delete_link_".$i;

		global ${$f_rel_id};
		global ${$f_rel};
		global ${$f_rel_add_reverse_link};
		global ${$f_rel_reverse};
		global ${$f_rel_id_notices_relations};
		global ${$f_rel_num_reverse_link};
		global ${$f_rel_delete_link};

		$relation_type=explode('-', ${$f_rel})[0];
		$direction=explode('-', ${$f_rel})[1];

		$relation_type_reverse=explode('-', ${$f_rel_reverse})[0];
		$direction_reverse=explode('-', ${$f_rel_reverse})[1];

		$this->linked_notice = ${$f_rel_id};
		$this->relation_type = $relation_type;
		$this->direction = $direction;
		$this->num_reverse_link = ${$f_rel_num_reverse_link};
		$this->ranking = notice_relations::$ranking_by_type[$this->direction]++;

		if(${$f_rel_add_reverse_link}) {
			if(!isset($this->reverse_notice_relation)) {
				$this->reverse_notice_relation = new notice_relation($this->num_reverse_link, false);
				$this->reverse_notice_relation->set_ranking(notice_relations::get_next_ranking($this->linked_notice, $direction_reverse));
			}
			//Si on est sur une duplication, on duplique le lien réciproque aussi
			if($is_duplication) {
				$this->reverse_notice_relation->set_id(0);
			}
			$this->reverse_notice_relation->set_num_notice($this->linked_notice);
			$this->reverse_notice_relation->set_linked_notice($this->num_notice);
			$this->reverse_notice_relation->set_relation_type($relation_type_reverse);
			$this->reverse_notice_relation->set_direction($direction_reverse);
			if ($this->id) {
				$this->reverse_notice_relation->set_num_reverse_link($this->id);
			}

		} else {
			if(isset($this->reverse_notice_relation)) {
				$this->num_reverse_link = 0;
				$this->reverse_notice_relation->set_num_reverse_link(0);
			}
		}
	}

	public function save() {
		if(isset($this->reverse_notice_relation)) {
			if($this->reverse_notice_relation->get_to_delete()) {
				$this->reverse_notice_relation->delete();
				$this->set_num_reverse_link(0);
			} else {
				$on_create = false;
				if (!$this->reverse_notice_relation->get_id()) {
					$on_create = true;
				}
				$this->reverse_notice_relation->save();
				if ($on_create) {
					$this->set_num_reverse_link($this->reverse_notice_relation->get_id());
				}
			}
		}
		if($this->id) {
			$query = "update notices_relations ";
			$where = "where id_notices_relations=".$this->id;
		} else {
			$query = "insert into notices_relations ";
			$where = "";
		}
		$query .= "set
			num_notice = '".$this->num_notice."',
			linked_notice = '".$this->linked_notice."',
			relation_type = '".addslashes($this->relation_type)."',
			ranking = '".$this->ranking."',
			direction = '".addslashes($this->direction)."',
			num_reverse_link = ".$this->num_reverse_link." ".$where;
		pmb_mysql_query($query);

		if(!$this->id) {
			$this->id = pmb_mysql_insert_id();
			if($this->num_reverse_link) {
				pmb_mysql_query("update notices_relations
					set num_reverse_link=".$this->id."
					where id_notices_relations=".$this->num_reverse_link);
			}
		}
	}

	public function delete() {
		if (isset($this->reverse_notice_relation) && $this->reverse_notice_relation->get_to_delete()) {
			$this->reverse_notice_relation->delete();
		}

		$query = "delete from notices_relations where id_notices_relations = ".$this->id;
		pmb_mysql_query($query);

		pmb_mysql_query("update notices_relations
 				set num_reverse_link=0
 				where num_reverse_link=".$this->id);
	}

	protected function get_drag_template($id_elt, $tit1='') {
		global $base_path, $charset;

		switch ($this->direction) {
			case 'up':
				$dragtype = 'parents';
				$recepttype = 'parents';
				break;
			case 'down':
				$dragtype = 'childs';
				$recepttype = 'childs';
				break;
			case 'both':
				$dragtype = 'pairs';
				$recepttype = 'pairs';
				break;

		}
		$drag_link = "<div id=\"drag_".$id_elt."\" handler=\"handle_".$id_elt."\" dragtype='".$dragtype."' draggable='yes' recepttype='".$recepttype."' recept='yes'
									dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities($tit1,ENT_QUOTES,$charset)."\" callback_before=\"is_expandable\"
													callback_after=\"\" downlight=\"noti_downlight\" highlight=\"noti_highlight\" notice_relation_id='".$this->id."' pere='".$this->num_notice."' order='".$this->ranking."' type_rel=\"".$this->relation_type."\" >";
		$drag_link .= "<span id=\"handle_".$id_elt."\" style=\"float:left; padding-right : 7px\"><img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' /></span>";
		return $drag_link;
	}

	public function get_child_record($notice) {
	    global $opac_notice_affichage_class;
	    global $seule;
	    
	    if ($opac_notice_affichage_class) {
	        $notice_affichage=$opac_notice_affichage_class;
	    } else {
	        $notice_affichage="notice_affichage";
	    }
	    //On stocke l'instance uniquement dans le contexte de l'affichage d'une notice etendue &seule=1 
	    if(empty($seule) || !isset(static::$childs_records[$this->num_notice][$this->linked_notice])) {
	        $linked_notice=new $notice_affichage($this->linked_notice,$notice->liens,$notice->cart_allowed,$notice->to_print,0);
	        $linked_notice->antiloop=$notice->antiloop;
	        $linked_notice->do_header();
	        $linked_notice->do_isbd();
	        $linked_notice->do_public();
	        if ($notice->double_ou_simple == 2 ) $linked_notice->genere_double(1, $notice->premier);
	        $linked_notice->genere_simple(1, $notice->premier);
	        static::$childs_records[$this->num_notice][$this->linked_notice] = $linked_notice;
	    }
	    return static::$childs_records[$this->num_notice][$this->linked_notice];
	}
	    
	public function get_display_link($notice, $header_only=1) {
		global $opac_notice_affichage_class;
		global $memo_notice;

		$display_link = '';

		if ($opac_notice_affichage_class) $notice_affichage=$opac_notice_affichage_class; else $notice_affichage="notice_affichage";

		if($header_only) {
			if(!isset($memo_notice[$this->linked_notice]["header_without_doclink"])) {
				$linked_notice=new $notice_affichage($this->linked_notice,$notice->liens,1,$notice->to_print,1);
				$linked_notice->antiloop=$notice->antiloop;
				$linked_notice->visu_expl = 0 ;
 				//$linked_notice->visu_explnum = 0 ;
				if ($notice->get_parents_header_without_html()) {
					$linked_notice->do_header_without_html();
				} else {
					$linked_notice->do_header();
				}
			}
			if ($notice->lien_rech_notice) $display_link .= "<a href='".str_replace("!!id!!",$this->linked_notice,$notice->lien_rech_notice)."&seule=1'>";
			if ($notice->get_parents_header_without_html()) {
				$display_link .= $memo_notice[$this->linked_notice]["header_without_html"];
			} else {
				$display_link .= $memo_notice[$this->linked_notice]["header"];
			}
			if ($notice->lien_rech_notice) $display_link .= "</a>";
		} else {
		    $linked_notice = $this->get_child_record($notice);
		    $display_link .= $linked_notice->result;
		}
		$notice_header_doclink = '';
		if (isset($linked_notice->notice_header_doclink)) {
		    $notice_header_doclink = $linked_notice->notice_header_doclink;
		}
		return array(
				'display' => $display_link,
				'header_only' => $header_only,
				'linked_notice' => $this->linked_notice,
		        'header_doclink' => $notice_header_doclink
		);
	}

	public function get_id() {
		return $this->id;
	}

	public function get_num_notice() {
		return $this->num_notice;
	}

	public function set_num_notice($num_notice) {
		$this->num_notice = $num_notice;
	}

	public function get_linked_notice() {
		return $this->linked_notice;
	}

	public function set_linked_notice($linked_notice) {
		$this->linked_notice = $linked_notice;
	}

	public function get_relation_type() {
		return $this->relation_type;
	}

	public function set_relation_type($relation_type) {
		$this->relation_type = $relation_type;
	}

	public function get_ranking() {
		return $this->ranking;
	}

	public function set_ranking($ranking) {
		$this->ranking = $ranking;
	}

	public function get_direction() {
		return $this->direction;
	}

	public function set_direction($direction) {
		$this->direction = $direction;
	}

	public function get_num_reverse_link() {
		return $this->num_reverse_link;
	}

	public function set_num_reverse_link($num_reverse_link) {
		$this->num_reverse_link = $num_reverse_link;
	}

	public function get_niveau_biblio() {
		return $this->niveau_biblio;
	}

	public function get_niveau_hierar() {
		return $this->niveau_hierar;
	}

	public function get_reverse_notice_relation() {
		return $this->reverse_notice_relation;
	}

	public function set_parent_niveau_biblio($parent_niveau_biblio='m') {
		$this->parent_niveau_biblio = $parent_niveau_biblio;
	}

	public function set_parent_niveau_hierar($parent_niveau_hierar='0') {
		$this->parent_niveau_hierar = $parent_niveau_hierar;
	}
	
	public function set_id($id=0) {
		$this->id = $id;
	}

	public function get_parent_niveau_biblio() {
		return $this->parent_niveau_biblio;
	}

	public function get_parent_niveau_hierar() {
		return $this->parent_niveau_hierar;
	}

	public function get_to_delete() {
		return $this->to_delete;
	}

	public function set_to_delete($to_delete=false) {
		$this->to_delete = $to_delete;
	}

	public function get_serial_id() {
		return $this->serial_id;
	}
}