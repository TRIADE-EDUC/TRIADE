<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: filter_fields.class.php,v 1.5.4.1 2025/01/23 15:48:16 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once "$class_path/fields/fields.class.php";

class filter_fields extends fields {

	protected function get_field($i, $n, $field) {
		global $charset;

		//Champ
		$v=$this->get_global_value("field_".$i."_".$field);
		if ($v=="") $v=array('');
		$field = "
			<span class='field_value'>
				<input type='text' id='field_".$n."_".$field."' name='field_".$n."_".$field."[]' value='".htmlentities(stripslashes($v[0]),ENT_QUOTES,$charset)."' completion='fields_global_index' param1='".($this->type ? $this->type : 'notices')."' param2='".$field."' class='ext_field_txt'/>
			</span>";
		return $field;
	}

	public function get_already_selected() {
		global $msg, $charset;
		global $add_field;
		global $delete_field;
		global $fields;
		//Affichage des champs deja saisis
		$r="";
		$n=0;
		$r.="<table class='table-no-border'>\n";
		if(is_countable($fields)) {
			for ($i=0; $i<count($fields); $i++) {
				if ((string)$i!=$delete_field) {
					$f=explode("_",$fields[$i]);
					$r.="<tr>";
					$r.="<td>";
					$r.="<input type='hidden' name='fields[]' value='".$fields[$i]."'>";//Colonne 1
					$r.="</td>";
					$r.="<td class='field_first_column'>";//Colonne 2
					if ($n>0) {
						$inter = $this->get_global_value("inter_".$i."_".$fields[$i]);
						$r.="<span class='field_operator'><select name='inter_".$n."_".$fields[$i]."'>";
						$r.="<option value='and' ".($inter=="and" ? "selected='selected'" : "").">".$msg["search_and"]."</option>";
						$r.="<option value='or' ".($inter=="or" ? "selected='selected'" : "").">".$msg["search_or"]."</option>";
						$r.="<option value='ex' ".($inter=="ex" ? "selected='selected'" : "").">".$msg["search_exept"]."</option>";
						$r.="</select></span>";
					} else $r.="&nbsp;";
					$r.="</td>";
					$r.="<td><span class='field_critere'>";//Colonne 3
					if ($f[0]=="f") {
						if($f[2] && $this->type != 'skos') {
							$r.=htmlentities($msg[self::$fields[$this->type]["FIELD"][$f[1]]["TABLE"][0]["TABLEFIELD"][$f[2]]["NAME"]],ENT_QUOTES,$charset);
						} else {
							if(isset($msg[self::$fields[$this->type]["FIELD"][$f[1]]["NAME"]])) {
							  $r.=htmlentities($msg[self::$fields[$this->type]["FIELD"][$f[1]]["NAME"]],ENT_QUOTES,$charset);
							} else {
								$r.=htmlentities(self::$fields[$this->type]["FIELD"][$f[1]]["NAME"],ENT_QUOTES,$charset);
							}
						}
					} elseif(array_key_exists($f[0],static::$pp)) {
						$r.=htmlentities(static::$pp[$f[0]]->t_fields[$f[2]]["TITRE"],ENT_QUOTES,$charset);
					}

					$r.="</span></td>";
					//Recherche des operateurs possibles
					$r.="<td>";//Colonne 4
					$r.="<span class='field_sous_critere'><select name='op_".$n."_".$fields[$i]."' id='op_".$n."_".$fields[$i]."'";
					$op = $this->get_global_value("op_".$i."_".$fields[$i]);
					foreach (static::get_operators() as $name=>$label) {
						$r.="<option value='".$name."' ".($op == $name ? "selected='selected'" : "").">".htmlentities(get_msg_to_display($label),ENT_QUOTES,$charset)."</option>\n";
					}
					$r.="</select></span>";
					$r.="</td>";
					//Affichage du champ de saisie
					$r.="<td>";//Colonne 5
					$r.=$this->get_field($i,$n,$fields[$i]);
					$r.="</td>";
					$r.="<td><span class='field_cancel'><input id='delete_field_button_".$n."' type='button' class='bouton' value='".$msg["raz"]."' onClick=\"this.form.delete_field.value='".$n."'; this.form.action=''; this.form.target=''; this.form.submit();\"></td>";//Colonne 6
					$r.="</tr>\n";
					$n++;
				}
			}
		}
		$r.="</table>";
		return $r;
	}

	public function format_fields() {
		global $fields;

		$to_format=array();
		for ($i=0; $i<count($fields); $i++) {
			$to_format[$i]["NAME"]=$fields[$i];
			$to_format[$i]["OP"]=$this->get_global_value("op_".$i."_".$fields[$i]);
			$to_format[$i]["FIELD"]=$this->get_global_value("field_".$i."_".$fields[$i]);
			$to_format[$i]["INTER"]=$this->get_global_value("inter_".$i."_".$fields[$i]);
		}
		return $to_format;
	}

	public function unformat_fields($to_unformat) {
		global $fields;

		$fields=array();
		for ($i=0; $i<count($to_unformat); $i++) {
			$fields[$i] = $to_unformat[$i]["NAME"];
			$this->set_global_value("op_".$i."_".$to_unformat[$i]["NAME"], $to_unformat[$i]["OP"]);
			$this->set_global_value("field_".$i."_".$to_unformat[$i]["NAME"], $to_unformat[$i]["FIELD"]);
			$this->set_global_value("inter_".$i."_".$to_unformat[$i]["NAME"], $to_unformat[$i]["INTER"]);
		}
	}

	public function filter_datas($datas=array()) {
		global $fields;
		global $opac_multi_search_operator;

		$main = "";
		$last_table = "";
		$prefixe = "tempo_".str_replace([" ","."],"_",microtime());
		for ($i=0; $i<count($fields); $i++) {
			$f=explode("_",$fields[$i]);

			$op = $this->get_global_value("op_".$i."_".$fields[$i]);
			$field = $this->get_global_value("field_".$i."_".$fields[$i]);
			$inter = $this->get_global_value("inter_".$i."_".$fields[$i]);

			$last_main_table="";
// 			$prefixe="";

			//Pour chaque valeur du champ
			for ($j=0; $j<count($field); $j++) {
				$operator = ($opac_multi_search_operator?$opac_multi_search_operator:"or");
				$main = "select distinct ".$this->field_keyName." from ".$this->field_tableName." where code_champ = ".$f[1];
				if($f[2]) {
					$main .= " and code_ss_champ = ".$f[2];
				}
				switch ($op) {
					case "BOOLEAN" :
					    $main .= " and value like ' ".addslashes($field[$j])." '";
						break;
					case "STARTWITH" :
					    $main .= " and value like '".addslashes($field[$j])."%'";
						break;
					case "ENDWITH" :
					    $main .= " and value like '%".addslashes($field[$j])."'";
						break;
					case "EXACT" :
					    $main .= " and value like '".addslashes($field[$j])."'";
						break;
					case "ISEMPTY" :
						$main .= " and ".$this->field_keyName." NOT IN (select ".$this->field_keyName." from ".$this->field_tableName." where code_champ = ".$f[1];
						if($f[2]) {
							$main .= " and code_ss_champ = ".$f[2];
						}
						$main .= ")";
						break;
					case "ISNOTEMPTY" :
						$main .= " and ".$this->field_keyName." IN (select ".$this->field_keyName." from ".$this->field_tableName." where code_champ = ".$f[1];
						if($f[2]) {
							$main .= " and code_ss_champ = ".$f[2];
						}
						$main .= ")";
						break;
				}
				if (count($field)>1) {
					if($operator == "or"){
						//Ou logique si plusieurs valeurs
						if ($prefixe) {
							$this->gen_temporary_table($prefixe."mf_".$j, $main);
						} else {
							$this->gen_temporary_table("mf_".$j, $main);
						}

						if ($last_main_table) {
							if ($prefixe) {
								$requete="insert ignore into ".$prefixe."mf_".$j." select ".$last_main_table.".* from ".$last_main_table;
							} else {
								$requete="insert ignore into mf_".$j." select ".$last_main_table.".* from ".$last_main_table;
							}
							pmb_mysql_query($requete);
							pmb_mysql_query("drop table ".$last_main_table);
						}
						if ($prefixe) {
							$last_main_table=$prefixe."mf_".$j;
						} else {
							$last_main_table="mf_".$j;
						}
					} elseif($operator == "and"){
						//ET logique si plusieurs valeurs
						if ($prefixe) {
							$this->gen_temporary_table($prefixe."mf_".$j, $main);
						} else {
							$this->gen_temporary_table("mf_".$j, $main);
						}

						if ($last_main_table) {
							if($j>1){
								$search_table=$last_main_table;
							}else{
								$search_table=$last_tables;
							}
							if ($prefixe) {
								$requete="create temporary table ".$prefixe."and_result_".$j." ENGINE=".$this->current_engine." select ".$search_table.".* from ".$search_table." where exists ( select ".$prefixe."mf_".$j.".* from ".$prefixe."mf_".$j." where ".$search_table.".notice_id=".$prefixe."mf_".$j.".notice_id)";
							} else {
								$requete="create temporary table and_result_".$j." ENGINE=".$this->current_engine." select ".$search_table.".* from ".$search_table." where exists ( select mf_".$j.".* from mf_".$j." where ".$search_table.".notice_id=mf_".$j.".notice_id)";
							}
							pmb_mysql_query($requete);
							pmb_mysql_query("drop table ".$last_tables);
						}
						if ($prefixe) {
							$last_tables=$prefixe."mf_".$j;
						} else {
							$last_tables="mf_".$j;
						}
						if ($prefixe) {
							$last_main_table = $prefixe."and_result_".$j;
						} else {
							$last_main_table = "and_result_".$j;
						}
					}
				}
			}
			if ($last_main_table){
				$main="select * from ".$last_main_table;
			}
			if ($prefixe) {
				$table=$prefixe."t_".$i."_".$fields[$i];
				$this->gen_temporary_table($table, $main, true);
			} else {
				$table="t_".$i."_".$fields[$i];
				$this->gen_temporary_table($table, $main, true);
			}
			if ($last_main_table) {
				$requete="drop table ".$last_main_table;
				pmb_mysql_query($requete);
			}

			if ($prefixe) {
				$requete="create temporary table ".$prefixe."t".$i." ENGINE=".$this->current_engine." ";
			} else {
				$requete="create temporary table t".$i." ENGINE=".$this->current_engine." ";
			}
			$isfirst_criteria=false;
			switch ($inter) {
				case "and":
					$requete.="select ";
					$req_col="SHOW columns FROM ".$table;
					$res_col=pmb_mysql_query($req_col);
					while ($col = pmb_mysql_fetch_object($res_col)){
						if($col->Field == "pert"){
							$requete.="SUM(".$table.".pert + ".$last_table.".pert) AS pert,";
						}else{
							$requete.=$table.".".$col->Field.",";
						}
					}
					$requete=substr($requete,0,-1);
					$requete.=" from $last_table,$table where ".$table.".".$this->field_keyName."=".$last_table.".".$this->field_keyName." group by ".$this->field_keyName;
					pmb_mysql_query($requete);
					break;
				case "or":
					//Si la table précédente est vide, c'est comme au premier jour !
					$requete_c="select count(*) from ".$last_table;
					if (!pmb_mysql_result(pmb_mysql_query($requete_c),0,0)) {
						$isfirst_criteria=true;
					} else {
						$requete.="select * from ".$table;
						pmb_mysql_query($requete);
						if ($prefixe) {
							$this->upgrade_columns_temporary_table($prefixe."t".$i, $this->field_keyName, true);
						} else {
							$this->upgrade_columns_temporary_table("t".$i, $this->field_keyName, true);
						}
						if ($prefixe) {
							$requete="insert into ".$prefixe."t".$i." (".$this->field_keyName.",idiot,pert) select distinct ".$last_table.".".$this->field_keyName.",".$last_table.".idiot, ".$last_table.".pert AS pert from ".$last_table." left join ".$table." on ".$last_table.".".$this->field_keyName."=".$table.".".$this->field_keyName." where ".$table.".".$this->field_keyName." is null";
						} else {
							$requete="insert into t".$i." (".$this->field_keyName.",idiot,pert) select distinct ".$last_table.".".$this->field_keyName.",".$last_table.".idiot, ".$last_table.".pert AS pert from ".$last_table." left join ".$table." on ".$last_table.".".$this->field_keyName."=".$table.".".$this->field_keyName." where ".$table.".".$this->field_keyName." is null";
						}
						pmb_mysql_query($requete);
					}
					break;
				case "ex":
					$requete.="select ".$last_table.".* from $last_table left join ".$table." on ".$table.".".$this->field_keyName."=".$last_table.".".$this->field_keyName." where ".$table.".".$this->field_keyName." is null";
					pmb_mysql_query($requete);
					if ($prefixe) {
						$requete="alter table ".$prefixe."t".$i." add idiot int(1)";
						pmb_mysql_query($requete);
						$requete="alter table ".$prefixe."t".$i." add unique(".$this->field_keyName.")";
						pmb_mysql_query($requete);
						$requete="alter table ".$prefixe."t".$i." add pert decimal(16,1) default 1";
						pmb_mysql_query($requete);
					} else {
						$requete="alter table t".$i." add idiot int(1)";
						pmb_mysql_query($requete);
						$requete="alter table t".$i." add unique(".$this->field_keyName.")";
						pmb_mysql_query($requete);
						$requete="alter table ".$prefixe."t".$i." add pert decimal(16,1) default 1";
						pmb_mysql_query($requete);
					}
					break;
				default:
					$isfirst_criteria=true;
					$requete.="select * from ".$table;
					pmb_mysql_query($requete);

					$requete="alter table ".$prefixe."t".$i." add idiot int(1)";
					pmb_mysql_query($requete);
					$requete="alter table ".$prefixe."t".$i." add unique(".$this->field_keyName.")";
					pmb_mysql_query($requete);
					$requete="alter table ".$prefixe."t".$i." add pert decimal(16,1) default 1";
					pmb_mysql_query($requete);
					break;
			}
			if (!$isfirst_criteria) {
				if($last_table){
					pmb_mysql_query("drop table if exists ".$last_table);
				}
				if($table){
					pmb_mysql_query("drop table if exists ".$table);
				}
				if ($prefixe) {
					$last_table=$prefixe."t".$i;
				} else {
					$last_table="t".$i;
				}
			} else {
				if($last_table){
					pmb_mysql_query("drop table if exists ".$last_table);
				}
				$last_table=$table;
			}
		}
		switch ($this->type) {
			case 'authorities':
				$query="select ".$last_table.".id_authority, authorities.num_object from ".$last_table." JOIN authorities ON authorities.id_authority = ".$last_table.".id_authority";
				$result = pmb_mysql_query($query);
				$ids = array();
				while($row = pmb_mysql_fetch_object($result)) {
					$ids[] = $row->num_object;
				}
				break;
			default:
				$query="select ".$last_table.".id_notice from ".$last_table;
				$result = pmb_mysql_query($query);
				$ids = array();
				while($row = pmb_mysql_fetch_object($result)) {
					$ids[] = $row->id_notice;
				}
				break;
		}
		$datas = array_intersect($datas, $ids);
		return $datas;
	}

	public static function get_operators() {
		return array(
				"BOOLEAN" => "msg:expr_bool_query",
				"STARTWITH" => "msg:commence_par_query",
				"ENDWITH" => "msg:finit_par_query",
				"EXACT" => "msg:exactement_comme_query",
				"ISEMPTY" => "msg:est_vide_query",
				"ISNOTEMPTY" => "msg:pas_vide_query"
		);
	}
}
?>