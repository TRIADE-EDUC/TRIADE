<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_relations.class.php,v 1.22.4.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
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

	public static $ranking_by_type;

	protected static $access_rights;

	public function __construct($notice_id=0) {
	    $this->notice_id = intval($notice_id);
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
		$access_rights = static::get_access_rights();
		$query = "select id_notices_relations, num_reverse_link, notices.niveau_biblio, notices.niveau_hierar
				from notices_relations
				join notices on notice_id=linked_notice
				join notices n2 on n2.notice_id=num_notice ".$access_rights['acces_j']." ".$access_rights['statut_j']."
				where num_notice = ".$this->notice_id." ".$access_rights['statut_r']."
				order by relation_type, ranking, notices.create_date";
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

	public function get_form($notice_links=array(), $niveau_biblio='m') {
		global $charset;
		global $notice_relations_links_tpl;

		$form = $notice_relations_links_tpl;

		$string_relations = '';
		$n_rel=0;
		foreach($notice_links as $direction=>$relations){
			foreach($relations as $relation){
				if(!((is_object($relation)) && ($relation->get_serial_id() == $relation->get_linked_notice()) && ($relation->get_relation_type() == 'b'))) {
					$string_relations .= $relation->get_form($n_rel, $niveau_biblio);
					$n_rel++;
				}
			}
		}
		if(!$n_rel) {
			$this->links[0] = new notice_relation();
			$string_relations .= $this->links[0]->get_form($n_rel, $niveau_biblio);
			$n_rel++;
		}

		$form=str_replace("!!value_deflt_relation!!",static::get_default_relation_type($niveau_biblio),$form);
		$form=str_replace("!!value_deflt_reverse_relation!!",static::get_default_reverse_relation_type($niveau_biblio),$form);
		$form=str_replace("!!get_json_reverse_attributes!!",static::get_json_reverse_attributes(),$form);

		//Nombre de relations
		$form=str_replace("!!max_rel!!",$n_rel,$form);

		//Liens multiples
		$form=str_replace("!!notice_relations!!",$string_relations,$form);

		$form=str_replace("!!notice_id_no_replace!!",$this->notice_id,$form);
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

		$select = "
			<select onchange='".$on_change."' id='".$name."' name='".$name."' ".($multiple ? "multiple='multiple'" : "").">
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

	public static function get_next_ranking($notice_id=0, $direction='') {
		$query = "select max(ranking) as max_ranking from notices_relations where num_notice=".$notice_id." and direction='".$direction."'";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		if ($row->max_ranking !== null) {
			$ranking = $row->max_ranking + 1;
		} else {
			$ranking = 0;
		}

		return $ranking;
	}

	public function set_properties_from_form() {
		global $max_rel;

		static::$ranking_by_type = array(
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
			    if(!isset($this->links[$i]) || !is_object($this->links[$i])) {
					$this->links[$i] = new notice_relation();
					$this->links[$i]->set_num_notice($this->notice_id);
				}
				if(isset($this->links[$i]) && !($this->links[$i]->get_serial_id() == $this->links[$i]->get_linked_notice() && ($this->links[$i]->get_relation_type() == 'b'))) {
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

	public function get_display_links($type_links, $notice_affichage) {
		global $memo_notice;

		//On définit le tableau à utiliser
		switch ($type_links) {
			case 'parents':
				$direction = 'up';
				$links = $this->get_parents();
				$header_only = 1;
				break;
			case 'childs':
				$direction = 'down';
				$links = $this->get_childs();
				if($notice_affichage->seule) $header_only=0; else $header_only=1;
				break;
			case 'pairs':
				$direction = 'both';
				$links = $this->get_pairs();
				if($notice_affichage->seule) $header_only=0; else $header_only=1;
				break;

		}
		$display_links = array();
		foreach ($links as $relation_type=>$links_relations) {
			$relations_links = array();
			foreach ($links_relations as $i=>$link) {
			    if(!$notice_affichage->seule && isset($memo_notice[$link->get_linked_notice()]["niveau_biblio"]) && isset($memo_notice[$link->get_linked_notice()]["header_without_doclink"]) && $memo_notice[$link->get_linked_notice()]["niveau_biblio"]!='b' && $memo_notice[$link->get_linked_notice()]["header_without_doclink"]) {
					$relations_links[] = $link->get_display_link($notice_affichage, 1);
				} else if (!isset($notice_affichage->antiloop[$link->get_linked_notice()])) {
					if ($link->get_niveau_biblio()!='b' || ($link->get_niveau_biblio()=='b' && $link->get_niveau_biblio() != "s")) {
						$relations_links[] = $link->get_display_link($notice_affichage, $header_only);
					}
				}
			}
			$display_links[static::$liste_type_relation[$direction]->table[$relation_type]] = $relations_links;
		}
		// !$notice_affichage->seule : les dépouillements sont affichés dans la zone liens entre notices et non dans la zone dépouillement
		if(!$notice_affichage->seule && $type_links == 'childs' && $notice_affichage->notice->niveau_biblio == 'b') {
			$analysis = $this->get_analysis();
			foreach ($analysis as $link) {
				$display_links[static::$liste_type_relation['down']->table['d']][] = $link->get_display_link($notice_affichage, 1);
			}
		}
		return $display_links;
	}

	public static function insert($num_notice, $linked_notice, $relation_type, $ranking=0, $direction='up', $add_reverse_link=true) {
		$id_notices_relations = static::insert_link($num_notice, $linked_notice, $relation_type, $ranking, $direction, 0);

		if ($add_reverse_link) {
			static::parse();
			$reverse_relation_type = static::$liste_type_relation[$direction]->attributes[$relation_type]['REVERSE_CODE'];
			$reverse_direction = static::$liste_type_relation[$direction]->attributes[$relation_type]['REVERSE_DIRECTION'];
			$reverse_id_notices_relations = static::insert_link($linked_notice, $num_notice, $reverse_relation_type, $ranking, $reverse_direction, $id_notices_relations);

			pmb_mysql_query("update notices_relations
				set num_reverse_link=".$reverse_id_notices_relations."
				where id_notices_relations=".$id_notices_relations);
		}
	}

	public static function insert_link($num_notice, $linked_notice, $relation_type, $ranking=0, $direction='', $num_reverse_link=0) {
		$query = "insert into notices_relations set
			num_notice = '".$num_notice."',
			linked_notice = '".$linked_notice."',
			relation_type = '".addslashes($relation_type)."',
			ranking = '".$ranking."',
			direction = '".addslashes($direction)."',
			num_reverse_link = ".$num_reverse_link;
		pmb_mysql_query($query);

		return pmb_mysql_insert_id();
	}

	public static function replace($num_notice, $linked_notice, $relation_type, $ranking=0) {
		$query = "replace into notices_relations set
				num_notice = '".$num_notice."',
				linked_notice = '".$linked_notice."',
				relation_type = '".addslashes($relation_type)."',
				ranking = '".$ranking."',
				direction = 'up',
				num_reverse_link = 0";
		pmb_mysql_query($query);
	}

	public static function update_nomenclature_ranking($num_notice, $linked_notice, $relation_type, $ranking=0) {
		$query = "update notices_relations set
				ranking = '".$ranking."'
				where
				num_notice = '".$num_notice."' and
				linked_notice = '".$linked_notice."' and
				relation_type = '".addslashes($relation_type)."'";
		pmb_mysql_query($query);
	}

	public static function update_ranking($id, $ranking=0) {
		$query = "update notices_relations set
				ranking = '".$ranking."'
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

	public static function get_notice_links($num_notice=0, $niveau_biblio='m', $num_serial=0) {
		$notice_links = array();

		$notice_relations = new notice_relations($num_notice);
		/**
		 * @var notice_relation
		 */
		foreach ($notice_relations->links as $i=>$link) {
			$notice_links[$link->get_direction()][$i] = $link;
		}
		return $notice_links;
	}

	public function get_analysis() {
		$analysis = array();
		// notice de bulletins, les relations sont dans la table analysis
		$access_rights = static::get_access_rights();
		$query = "select analysis_notice as notice_id from analysis
			JOIN bulletins ON bulletin_id = analysis_bulletin, notices ".$access_rights['acces_j']." ".$access_rights['statut_j']."
			WHERE num_notice=".$this->notice_id." AND notice_id = analysis_notice ".$access_rights['statut_r']."
			ORDER BY analysis_notice ASC";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$i = 0;
			while ($row = pmb_mysql_fetch_object($result)) {
				$analysis[$i] = new notice_relation(0);
				$analysis[$i]->set_num_notice($this->notice_id);
				$analysis[$i]->set_linked_notice($row->notice_id);
				$i++;
			}
		}
		return $analysis;
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

	public function get_links() {
	    return $this->links;
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

	public static function get_access_rights() {
		global $gestion_acces_active,$gestion_acces_empr_notice;

		if(!isset(static::$access_rights)) {
			//droits d'acces emprunteur/notice
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
			} else {
				$dom_2=null;
			}
			if (is_null($dom_2)) {
				static::$access_rights["acces_j"] = '';
				static::$access_rights["statut_j"] = ',notice_statut';
				static::$access_rights["statut_r"] = "and notices.statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".(!empty($_SESSION["user_code"])?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			} else {
				static::$access_rights["acces_j"] = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notices.notice_id');
				static::$access_rights["statut_j"] = "";
				static::$access_rights["statut_r"] = "";
			}
		}
		return static::$access_rights;
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
					$reverse_id_notices_relations = static::insert_link($row->linked_notice, $row->num_notice, $reverse_relation_type, $row->ranking, $reverse_direction, $row->id_notices_relations);
				}

				pmb_mysql_query("update notices_relations
					set direction='".$direction."',
					num_reverse_link=".$reverse_id_notices_relations."
					where id_notices_relations=".$row->id_notices_relations);
				$affected++;
			}
		}

		//Mise à jour des liens bulletin -> notice mère
		static::upgrade_notices_relations_table_bulletins();

		return $affected;
	}

	public static function upgrade_notices_relations_table_bulletins() {
		static::parse();

		//Mise à jour des liens bulletin -> notice mère (régression Avril 2020 #84994)
		$query = "SELECT bulletins.num_notice, bulletins.bulletin_notice FROM bulletins
			LEFT JOIN notices_relations ON notices_relations.num_notice = bulletins.num_notice
			AND notices_relations.relation_type = 'b' AND notices_relations.direction='up' WHERE bulletins.num_notice <> 0 AND notices_relations.num_notice IS NULL";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if($row->num_notice && $row->bulletin_notice) {
					static::insert($row->num_notice, $row->bulletin_notice, 'b', 1, 'up', false);
				}
			}
		}
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