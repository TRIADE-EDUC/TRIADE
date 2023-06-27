<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_relations.class.php,v 1.25 2019-03-28 21:47:25 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/notice_relation.class.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path."/encoding_normalize.class.php");
require_once($include_path."/templates/notice_relations.tpl.php");

class notice_relations {
	
	/**
	 * Identifiant de la notice
	 * @var integer
	 */
	protected $notice_id;
	
	/**
	 * Tableau de relations associées
	 * @var notice_relation
	 */
	protected $links;
	
	public static $liste_type_relation;
	public static $corresp_relation_up_down;
	
	public static $rank_by_type;
	
	public function __construct($notice_id=0) {
		$this->notice_id = $notice_id+0;
		static::parse();
		$this->links = array();
		if($this->notice_id) {
			$this->fetch_data();
		}
	}
	
	protected static function parse() {
		if (!isset(static::$liste_type_relation)) {
			static::$liste_type_relation['up'] = new marc_list("relationtypeup");
			static::$liste_type_relation['down'] = new marc_list("relationtypedown");
			static::$liste_type_relation['both'] = new marc_list("relationtypeup");
			static::$corresp_relation_up_down=array();
			foreach(static::$liste_type_relation['up']->table as $key_up=>$val_up){
				$horizontal = false;
				foreach(static::$liste_type_relation['down']->table as $key_down=>$val_down){
					if($val_up==$val_down){
						static::$corresp_relation_up_down[$key_up]=$key_down;
						unset(static::$liste_type_relation['down']->table[$key_down]);
						unset(static::$liste_type_relation['down']->attributes[$key_down]);
						unset(static::$liste_type_relation['up']->table[$key_up]);
						unset(static::$liste_type_relation['up']->attributes[$key_up]);
						$horizontal = true;
					}
				}
				if(!$horizontal) {
					unset(static::$liste_type_relation['both']->table[$key_up]);
					unset(static::$liste_type_relation['both']->attributes[$key_up]);
					static::$liste_type_relation['up']->attributes[$key_up]['REVERSE_DIRECTION'] = 'down';
					if(isset(static::$liste_type_relation['up']->attributes[$key_up]['REVERSE_CODE'])) {
						static::$liste_type_relation['up']->attributes[$key_up]['REVERSE_CODE'] = strtolower(static::$liste_type_relation['up']->attributes[$key_up]['REVERSE_CODE']);
					} else {
						static::$liste_type_relation['up']->attributes[$key_up]['REVERSE_CODE'] = $key_up;
					}
					static::$liste_type_relation['down']->attributes[$key_up]['REVERSE_DIRECTION'] = 'up';
					if(isset(static::$liste_type_relation['down']->attributes[$key_up]['REVERSE_CODE'])) {
						static::$liste_type_relation['down']->attributes[$key_up]['REVERSE_CODE'] = strtolower(static::$liste_type_relation['down']->attributes[$key_up]['REVERSE_CODE']);
					} else {
						static::$liste_type_relation['down']->attributes[$key_up]['REVERSE_CODE'] = $key_up;
					}
				} else {
					static::$liste_type_relation['both']->attributes[$key_up]['REVERSE_DIRECTION'] = 'both';
					if(isset(static::$liste_type_relation['both']->attributes[$key_up]['REVERSE_CODE'])) {
						static::$liste_type_relation['both']->attributes[$key_up]['REVERSE_CODE'] = strtolower(static::$liste_type_relation['both']->attributes[$key_up]['REVERSE_CODE']);
					} else {
						static::$liste_type_relation['both']->attributes[$key_up]['REVERSE_CODE'] = static::$corresp_relation_up_down[$key_up];
					}
				}
			}
			foreach (static::$liste_type_relation['both']->attributes as $key=>$reverse) {
				if(isset($reverse['REVERSE_CODE']) && !isset(static::$corresp_relation_up_down[$reverse['REVERSE_CODE']])) {
					static::$liste_type_relation['both']->attributes[$key]['REVERSE_DIRECTION'] = 'up';
				}
			}
		}
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {
		$query = "select id_notices_relations, num_reverse_link, notices.niveau_biblio, notices.niveau_hierar 
				from notices_relations 
				join notices on notice_id=linked_notice 
				join notices n2 on n2.notice_id=num_notice 
				where num_notice = ".$this->notice_id." order by relation_type, rank, notices.create_date";
		$result = pmb_mysql_query($query);
		$i = 0;
		while ($row = pmb_mysql_fetch_object($result)) {
			$this->links[$i] = new notice_relation($row->id_notices_relations);
			$this->links[$i]->set_parent_niveau_biblio($row->niveau_biblio);
			$this->links[$i]->set_parent_niveau_hierar($row->niveau_hierar);
			$i++;
		}
	}
	
	public static function get_default_relation_type($niveau_biblio='m') {
		switch ($niveau_biblio) {
			case 's':
				global $value_deflt_relation_serial;
				$default_relation_type = $value_deflt_relation_serial;
				break;
			case 'b':
				global $value_deflt_relation_bulletin;
				$default_relation_type = $value_deflt_relation_bulletin;
				break;
			case 'a':
				global $value_deflt_relation_analysis;
				$default_relation_type = $value_deflt_relation_analysis;
				break;
			case 'm':
				global $value_deflt_relation;
				$default_relation_type = $value_deflt_relation;
				break;
		}
		return $default_relation_type;
	}
	
	public static function get_default_reverse_relation_type($niveau_biblio='m') {
		$default_relation_type = static::get_default_relation_type($niveau_biblio);
		$relation_type=explode('-', $default_relation_type)[0];
		$direction=explode('-', $default_relation_type)[1];
		$default_reverse_relation_type = static::$liste_type_relation[$direction]->attributes[$relation_type]['REVERSE_CODE'].'-'.static::$liste_type_relation[$direction]->attributes[$relation_type]['REVERSE_DIRECTION'];
		return $default_reverse_relation_type;
	}
	
	public function get_form($notice_links=array(), $niveau_biblio='m', $from_duplicate_form = false) {
		global $charset;
		global $notice_relations_links_tpl;
		
		$form = $notice_relations_links_tpl;
		
		$string_relations = '';
		$n_rel=0;
		foreach($notice_links as $direction=>$relations){
			$last_rel = count($relations) - 1;
			foreach($relations as $relation){
				if(!((is_object($relation)) && ($relation->get_serial_id() == $relation->get_linked_notice()) && ($relation->get_relation_type() == 'b'))) {
					$button_add_field = "";
					if ($n_rel === $last_rel) {
						$button_add_field = "<input id='add_field_linked_record' type='button' class='bouton' value='+' onClick=\"add_rel();\"/>";
					}
					$string_relations .= str_replace('!!button_add_field!!', $button_add_field, $relation->get_form($n_rel, $niveau_biblio, $from_duplicate_form));
					$n_rel++;
				}
			}
		}
		if(!$n_rel) {
			$button_add_field = "<input id='add_field_linked_record' type='button' class='bouton' value='+' onClick=\"add_rel();\"/>";
			$this->links[0] = new notice_relation();
			$string_relations .= str_replace('!!button_add_field!!', $button_add_field, $this->links[0]->get_form($n_rel, $niveau_biblio, $from_duplicate_form));
			$n_rel++;
		}
		
		$form=str_replace("!!value_deflt_relation!!",static::get_default_relation_type($niveau_biblio),$form);
		$form=str_replace("!!value_deflt_reverse_relation!!",static::get_default_reverse_relation_type($niveau_biblio),$form);
		$form=str_replace("!!get_json_reverse_attributes!!",static::get_json_reverse_attributes(),$form);
		
		//Nombre de relations
		$form=str_replace("!!max_rel!!",$n_rel,$form);
			
		//Liens multiples
		$form=str_replace("!!notice_relations!!",$string_relations,$form);
		
		$form=str_replace("!!notice_id_no_replace!!",($from_duplicate_form ? 0 : $this->notice_id),$form);
		return $form;
	}
	
	protected static function get_selector_options($direction='', $selected='') {
		$options = '';
		foreach(static::$liste_type_relation[$direction]->table as $key=>$val){
			$reverse_code = static::$liste_type_relation[$direction]->attributes[$key]['REVERSE_CODE'];
			$reverse_direction = static::$liste_type_relation[$direction]->attributes[$key]['REVERSE_DIRECTION'];
			if((is_array($selected) && in_array($key.'-'.$direction, $selected)) || ($key.'-'.$direction == $selected)) {
				$options .='<option  style="color:#000000" value="'.$key.'-'.$direction.'" selected="selected" data-reverse-code="'.$reverse_code.'-'.$reverse_direction.'">'.$val.'</option>';
			}else{
				$options .='<option  style="color:#000000" value="'.$key.'-'.$direction.'" data-reverse-code="'.$reverse_code.'-'.$reverse_direction.'">'.$val.'</option>';
			}
		}
		return $options;
	}
	
	public static function get_selector($name='', $selected='', $on_change='', $multiple = false) {
		global $msg;
	
		static::parse();
		$tmp = explode("_",$name);
		unset($tmp[count($tmp)-1]);
		
		
		$select = "
			<select onchange='".$on_change."' id='".$name."' name='".$name."' data-form-name='".implode('_',$tmp)."_' ".($multiple ? "multiple='multiple'" : "").">
				<optgroup class='erreur' label='".$msg['notice_lien_montant']."'>";
		$select .= static::get_selector_options('up', $selected);
		$select .= "
				</optgroup>
				<optgroup class='erreur' label='".$msg['notice_lien_descendant']."'>";
		$select .= static::get_selector_options('down', $selected);
		$select .= "
				</optgroup>
				<optgroup class='erreur' label='".$msg['notice_lien_symetrique']."'>";
		$select .= static::get_selector_options('both', $selected);
		$select .= "
				</optgroup>
			</select>";
		return $select;
	}
	
	public static function get_next_rank($notice_id=0, $direction='') {
		$query = "select max(rank) as max_rank from notices_relations where num_notice=".$notice_id." and direction='".$direction."'";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		if ($row->max_rank !== null) {
			$rank = $row->max_rank + 1;
		} else {
			$rank = 0;
		}
	
		return $rank;
	}
	
	public function set_properties_from_form() {
		global $max_rel;

		static::$rank_by_type = array(
				'up' => 0,
				'down' => 0,
				'both' => 0
		);
		for ($i=0; $i<$max_rel; $i++) {
			if(isset($this->links[$i]) && is_object($this->links[$i])) {
				if(($this->links[$i]->get_serial_id() == $this->links[$i]->get_linked_notice() && ($this->links[$i]->get_relation_type() == 'b'))) {
					unset($this->links[$i]);
					$this->links = array_values($this->links);
				}
			}
			$f_rel_delete_link="f_rel_delete_link_".$i;
			$f_rel_id="f_rel_id_".$i;
			global ${$f_rel_delete_link};
			global ${$f_rel_id};
			
			if(${$f_rel_id}) {
				if(!is_object($this->links[$i])) {
					$this->links[$i] = new notice_relation();
					$this->links[$i]->set_num_notice($this->notice_id);
				}
				if(!($this->links[$i]->get_serial_id() == $this->links[$i]->get_linked_notice() && ($this->links[$i]->get_relation_type() == 'b'))) {
					switch (${$f_rel_delete_link}) {
						case 1:
							$this->links[$i]->set_to_delete(true);
							break;
						case 2:
							$this->links[$i]->set_to_delete(true);
							$this->links[$i]->get_reverse_notice_relation()->set_to_delete(true);
							break;
						default:
							$this->links[$i]->set_properties_from_form($i);
							break;
					}
				}
			} else {
				if(isset($this->links[$i]) && is_object($this->links[$i])) {
					$this->links[$i]->set_to_delete(true);
				}
			}
		}
	}
	
	public function save() {	
		foreach ($this->links as $i=>$link) {
			if($link->get_to_delete()) {
				$link->delete();
				unset($this->links[$i]);
			} else {
				$link->save();
			}
		}
	}
	
	public function get_display_links($type_links, $print_mode, $show_explnum, $show_statut, $show_opac_hidden_fields, $anti_loop=array()) {
		global $base_path;
	
		//On définit le tableau à utiliser
		switch ($type_links) {
			case 'parents':
				$direction = 'up';
				$links = $this->get_parents();
				$nb_links = $this->get_nb_parents();
				break;
			case 'childs':
				$direction = 'down';
				$links = $this->get_childs();
				$nb_links = $this->get_nb_childs();
				break;
			case 'pairs':
				$direction = 'both';
				$links = $this->get_pairs();
				$nb_links = $this->get_nb_pairs();
				break;
					
		}
		$display_links = '';
		$nb_displayed_links = 0;
		foreach ($links as $rel_type=>$links_relations) {
			if($nb_displayed_links>=100) break;
			$relations = static::$liste_type_relation[$direction];
			$display_links .= "\n<br /><b>".$relations->table[$rel_type]."</b>";
			if($direction != 'up') $display_links .= "<blockquote>";
			$nb_links = count($links_relations);
			foreach ($links_relations as $i=>$link) {
				if($nb_displayed_links>=100) break;
				$as=array_search($link->get_linked_notice(),$anti_loop);
				$display_link = $link->get_display_link($print_mode, $show_explnum, $show_statut, $show_opac_hidden_fields, $anti_loop, $as);
				if ($nb_links==1) {
					$display_links.=$display_link;
				} else {
					if($i == 0) {
						$display_links.="\n<ul class='notice_rel li_draggable'>\n";
					}
					$display_links.="\n<li>".$display_link."</li>\n";
					if($i == ($nb_links-1)) {
						$display_links.="\n</ul>\n";
					}
				}
				$nb_displayed_links++;
			}
			if($direction != 'up') $display_links.="</blockquote>";
		}
		return $display_links;
	}
	
	public static function insert_from_import($num_notice, $linked_notice, $relation_type, $rank=0, $direction='up') {
		//Le XML définit si on crée la relation inverse ou non
		static::parse();
		if (static::$liste_type_relation[$direction]->attributes[$relation_type]['REVERSE_CODE_DEFAULT_CHECKED']=='YES') {
			$reverse_create = true;
		} else {
			$reverse_create = false;
		}
		static::insert($num_notice, $linked_notice, $relation_type, $rank, $direction, $reverse_create);
	}
	
	public static function insert($num_notice, $linked_notice, $relation_type, $rank=0, $direction='up', $add_reverse_link=true) {
		$id_notices_relations = static::insert_link($num_notice, $linked_notice, $relation_type, $rank, $direction, 0);
		$reverse_id_notices_relations = 0;
		if ($add_reverse_link) {
			static::parse();
			$reverse_relation_type = static::$liste_type_relation[$direction]->attributes[$relation_type]['REVERSE_CODE'];
			$reverse_direction = static::$liste_type_relation[$direction]->attributes[$relation_type]['REVERSE_DIRECTION'];
			$reverse_id_notices_relations = static::insert_link($linked_notice, $num_notice, $reverse_relation_type, $rank, $reverse_direction, $id_notices_relations);
			
			pmb_mysql_query("update notices_relations 
				set num_reverse_link=".$reverse_id_notices_relations." 
				where id_notices_relations=".$id_notices_relations);
		}
		return array(
				'id_notices_relations' => $id_notices_relations,
				'num_reverse_link' => $reverse_id_notices_relations,
				'reverse_id_notices_relations' => $reverse_id_notices_relations,
				'reverse_num_reverse_link' => $id_notices_relations
		);
	}
	
	public static function insert_link($num_notice, $linked_notice, $relation_type, $rank=0, $direction='', $num_reverse_link=0) {
		$query = "insert into notices_relations set
			num_notice = '".$num_notice."',
			linked_notice = '".$linked_notice."',
			relation_type = '".addslashes($relation_type)."',
			rank = '".$rank."',
			direction = '".addslashes($direction)."',
			num_reverse_link = ".$num_reverse_link;
		pmb_mysql_query($query);

		return pmb_mysql_insert_id();
	}
	
	public static function replace($num_notice, $linked_notice, $relation_type, $rank=0) {
		$query = "replace into notices_relations set
				num_notice = '".$num_notice."',
				linked_notice = '".$linked_notice."',
				relation_type = '".addslashes($relation_type)."',
				rank = '".$rank."',
				direction = 'up',
				num_reverse_link = 0";
		pmb_mysql_query($query);
	}
	
	public static function update_nomenclature_rank($num_notice, $linked_notice, $relation_type, $rank=0) {
		$query = "update notices_relations set
				rank = '".$rank."' 
				where
				num_notice = '".$num_notice."' and
				linked_notice = '".$linked_notice."' and
				relation_type = '".addslashes($relation_type)."'";
		pmb_mysql_query($query);
		$query = "select num_reverse_link from notices_relations where
				num_notice = '".$num_notice."' and
				linked_notice = '".$linked_notice."' and
				relation_type = '".addslashes($relation_type)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$res = pmb_mysql_result($result,0,0);
			self::update_rank($res,$rank);
		}
	}
	
	public static function update_rank($id, $rank=0) {
		$query = "update notices_relations set
				rank = '".$rank."'
				where
				id_notices_relations = '".$id."'";
		pmb_mysql_query($query);
	}
	
	public static function update_num_notice($num_notice, $old_num_notice) {
		$query="update notices_relations set num_notice='".$num_notice."' where num_notice='".$old_num_notice."'";
		pmb_mysql_query($query);
	}
	
	public static function update_linked_notice($linked_notice, $old_linked_notice) {
		$query="update notices_relations set linked_notice='".$linked_notice."' where linked_notice='".$old_linked_notice."'";
		pmb_mysql_query($query);
	}
	
	public static function delete($notice_id=0) {
		$query='DELETE FROM notices_relations WHERE num_notice="'.$notice_id.'" OR linked_notice="'.$notice_id.'"';
		pmb_mysql_query($query);
	}

	public static function delete_unilateral_links($notice_id=0) {
		$query='DELETE FROM notices_relations WHERE num_notice="'.$notice_id.'" AND num_reverse_link = 0';
		pmb_mysql_query($query);
	}
	
	public static function delete_mutual_links($notice_id=0) {
		$query='DELETE FROM notices_relations WHERE (num_notice="'.$notice_id.'" OR linked_notice="'.$notice_id.'") AND num_reverse_link != 0';
		pmb_mysql_query($query);
	}
	
	public static function get_notice_links($num_notice=0, $niveau_biblio='m', $num_serial=0) {
		$notice_links = array();
		
		$notice_relations = new notice_relations($num_notice);
		/**
		 * @var notice_relation
		 */
		foreach ($notice_relations->links as $link) {
			$notice_links[$link->get_direction()][] = $link;
		}
		return $notice_links;
	}
	
	public function get_parents() {
		$parents = array();
		foreach ($this->links as $link) {
			if($link->get_direction()=='up') {
				$parents[$link->get_relation_type()][] = $link;
			}
		}
		return $parents;
	}
	
	public function get_nb_parents() {
		$nb_parents = 0;
		foreach ($this->links as $link) {
			if($link->get_direction()=='up') {
				$nb_parents++;
			}
		}
		return $nb_parents;
	}
	
	public function get_first_parent() {
		foreach ($this->links as $link) {
			if($link->get_direction()=='up') {
				return $link;
			}
		}
		return;
	}
	
	public function get_childs() {
		$childs = array();
		foreach ($this->links as $link) {
			if($link->get_direction()=='down') {
				$childs[$link->get_relation_type()][] = $link;
			}
		}
		return $childs;
	}
	
	public function get_nb_childs() {
		$nb_childs = 0;
		foreach ($this->links as $link) {
			if($link->get_direction()=='down') {
				$nb_childs++;
			}
		}
		return $nb_childs;
	}
	
	/**
	 * Méthode temporaire pour récupérer les horizontales filles
	 */
	public function get_pairs() {
		$pairs = array();
		foreach ($this->links as $link) {
			if($link->get_direction()=='both') {
				$pairs[$link->get_relation_type()][] = $link;
			}
		}
		return $pairs;
	}
	
	public function get_nb_pairs() {
		$nb_pairs = 0;
		foreach ($this->links as $link) {
			if($link->get_direction()=='both') {
				$nb_pairs++;
			}
		}
		return $nb_pairs;
	}
	
	public function get_nb_links() {

		return count($this->links);
	}
	
	public static function clean_lost_links() {
		$affected = 0;
		$query = pmb_mysql_query("delete notices_relations from notices_relations left join notices on num_notice=notice_id where notice_id is null ");
		$affected += pmb_mysql_affected_rows();
		$query = pmb_mysql_query("delete notices_relations from notices_relations left join notices on linked_notice=notice_id where notice_id is null ");
		$affected += pmb_mysql_affected_rows();
		return $affected;
	}
	
	public static function upgrade_notices_relations_table() {
		$affected = 0;
		static::parse();
	
		$query = "show columns from notices_relations like 'id_notices_relations'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)==0) {
			pmb_mysql_query("ALTER TABLE notices_relations DROP primary key");
			pmb_mysql_query("ALTER TABLE notices_relations ADD id_notices_relations  int unsigned not null auto_increment primary key FIRST");
			pmb_mysql_query("ALTER TABLE notices_relations ADD direction varchar (4) not null default '', ADD num_reverse_link int (10) not null default 0, ADD index num_notice(num_notice), ADD index direction(direction)");
		}
	
		$result = pmb_mysql_query("select * from notices_relations where direction=''");
		if (pmb_mysql_num_rows($result)){
			while ($row = pmb_mysql_fetch_object($result)) {
				$direction = 'up';
				$reverse_id_notices_relations = 0;
				//Cas spécifique des notices de bulletin
				$query_bull = "select count(1) from bulletins where num_notice =".$row->num_notice." and bulletin_notice=".$row->linked_notice;
				$result_bull = pmb_mysql_query($query_bull);
				if (!((pmb_mysql_result($result_bull, 0, 0)) && ($row->relation_type == 'b'))) {
					$reverse_relation_type = $row->relation_type;
					$reverse_direction = 'down';
					if(isset(static::$corresp_relation_up_down[$row->relation_type])){
						$reverse_relation_type = static::$corresp_relation_up_down[$row->relation_type];
						$reverse_direction = 'both';
						$direction = 'both';
					}
					$reverse_id_notices_relations = static::insert_link($row->linked_notice, $row->num_notice, $reverse_relation_type, $row->rank, $reverse_direction, $row->id_notices_relations);
				}	
				if(isset(static::$corresp_relation_up_down[$row->relation_type])){
					$reverse_relation_type = static::$corresp_relation_up_down[$row->relation_type];
					$direction = 'both';
				}
// 				print "<br />update notices_relations
// 					set direction='".$direction."',
// 					num_reverse_link=".$reverse_id_notices_relations."
// 					where id_notices_relations=".$row->id_notices_relations;
				pmb_mysql_query("update notices_relations
					set direction='".$direction."',
					num_reverse_link=".$reverse_id_notices_relations."
					where id_notices_relations=".$row->id_notices_relations);
				$affected++;
			}
		}
	
		return $affected;
	}
	
	public static function get_json_reverse_attributes() {
		$datas = array();
		
		foreach (static::$liste_type_relation as $direction=>$relations) {
			foreach ($relations->attributes as $relation=>$attributes) {
				$datas[$relation.'-'.$direction] = $attributes['REVERSE_CODE_DEFAULT_CHECKED'];
			}
		}
		
		return encoding_normalize::json_encode($datas);
	}
	
	public static function relation_exists($num_notice, $linked_notice, $relation_type, $direction='up', $num_reverse_link = -1) {
		$query = "select id_notices_relations from notices_relations
				where num_notice = '".$num_notice."'
				and linked_notice = '".$linked_notice."'
				and relation_type = '".$relation_type."'
				and direction = '".$direction."'";
		
		if ($num_reverse_link != -1) {
			$query .= " and num_reverse_link = ".$num_reverse_link;
		}
	
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function replace_links($num_notice, $by_num_notice, $notice_replace_links = 0) {
		
		switch ($notice_replace_links) {
			case "2" : //Conserver les liens de la notice remplacée (on supprime donc ceux de la notice qui remplace)
				static::delete($by_num_notice);
				static::update_num_notice($by_num_notice, $num_notice);
				static::update_linked_notice($by_num_notice, $num_notice);
				break;
			case "1" : //Conserver les liens de la notice qui remplace (on supprime donc ceux de la notice remplacée : réciproques et non-réciproques uniquement partants de la notice)
				static::delete_mutual_links($num_notice);
				static::delete_unilateral_links($num_notice);
				//remplacer les liens restants
				static::update_num_notice($by_num_notice, $num_notice);
				static::update_linked_notice($by_num_notice, $num_notice);
				break;
			case "0" : //On conserve tous les liens en évitant les doublons
			default :
				//Etape 1 : on supprime les liens en doublon
				$notice_relations = new notice_relations($num_notice);
				$notice_relations_by = new notice_relations($by_num_notice);
				
				$is_modified = false;
				foreach ($notice_relations->links as $i=>$link) {
					$link_found = false;
					foreach ($notice_relations_by->links as $i_by=>$link_by) {
						if (($link->get_relation_type() == $link_by->get_relation_type()) && ($link->get_direction() == $link_by->get_direction())) {
							//Deux cas de figure : les deux relations n'ont pas de relation associée / les deux relations ont une relation associée identique
							if (is_object($link->get_reverse_notice_relation()) && is_object($link_by->get_reverse_notice_relation())) {
								if (($link->get_reverse_notice_relation()->get_relation_type() == $link_by->get_reverse_notice_relation()->get_relation_type()) && ($link->get_reverse_notice_relation()->get_direction() == $link_by->get_reverse_notice_relation()->get_direction())) {
									$link_found = true;
									break;
								}
							} elseif (!is_object($link->get_reverse_notice_relation()) && !is_object($link_by->get_reverse_notice_relation())) {
								$link_found = true;
								break;
							}
						}
					}
					if ($link_found) {
						$notice_relations->links[$i]->set_to_delete(true);
						if (is_object($notice_relations->links[$i]->get_reverse_notice_relation())) {
							$notice_relations->links[$i]->get_reverse_notice_relation()->set_to_delete(true);
						}
						$is_modified = true;
					}
				}
				if ($is_modified) {
					$notice_relations->save();
				}
				//Etape 2 : on vérifie les liens dans l'autre sens éventuellement restants : relations pointant vers num_notice mais sans réciproque
				$query = "select * from notices_relations where linked_notice = '".$num_notice."' and num_reverse_link = 0";
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						if (static::relation_exists($by_num_notice, $row->linked_notice, $row->relation_type, $row->direction, 0)) {
							$query = "delete from notices_relations where id_notices_relations = ".$row->id_notices_relations;
							pmb_mysql_query($query);
						}
					}
				}
				//Etape 3 : on update ce qui reste	
				static::update_num_notice($by_num_notice, $num_notice);
				static::update_linked_notice($by_num_notice, $num_notice);
				break;
		}
		
		return;
	}
	
	public static function get_liste_type_relation() {
		if (!isset(static::$liste_type_relation)) {
			static::parse();
		}
		return static::$liste_type_relation;
	}
	
	public static function get_liste_type_relation_by_direction($direction) {
		if (!isset(static::$liste_type_relation)) {
			static::parse();
		}
		if (isset(static::$liste_type_relation[$direction])) {
			return static::$liste_type_relation[$direction];
		}
		return null;
	}
}