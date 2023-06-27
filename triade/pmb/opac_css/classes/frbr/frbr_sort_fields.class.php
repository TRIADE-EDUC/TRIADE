<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_sort_fields.class.php,v 1.6 2019-06-10 15:17:16 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/frbr/frbr_fields.class.php");

class frbr_sort_fields extends frbr_fields {
	
	protected function gen_selector_type($name='') {
		global $msg, $charset;
		
		$selected = $this->get_global_value($name);
		$selector = "<select id='".$name."' name='".$name."'>";
		foreach (static::get_types() as $key=>$type) {
			$selector .= "<option value = '".$key."' ".($selected == $key ? "selected='selected'" : "").">".htmlentities(get_msg_to_display($type), ENT_QUOTES, $charset)."</option>";
		}	
		$selector .= "</select>";
		return $selector;
	}
	
	protected function gen_selector_asc_desc($name='') {
		global $msg, $charset;
		
		$selected = $this->get_global_value($name);
		$selector = "<select id='".$name."' name='".$name."'>";
		foreach (static::get_directions() as $key=>$direction) {
			$selector .= "<option value = '".$key."' ".($selected == $key ? "selected='selected'" : "").">".htmlentities(get_msg_to_display($direction), ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
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
		for ($i=0; $i<count($fields); $i++) {
			if ((string)$i!=$delete_field) {
				$f=explode("_",$fields[$i]);
				$r.="<tr>";
				$r.="<td>";
				$r.="<input type='hidden' name='fields[]' value='".$fields[$i]."'>";//Colonne 1
				$r.="</td>";
				$r.="<td><span class='field_critere'>";//Colonne 2
				if ($f[0]=="f") {
					if($f[2]) {
						$r.=htmlentities($msg[self::$fields[$this->type]["FIELD"][$f[1]]["TABLE"][0]["TABLEFIELD"][$f[2]]["NAME"]],ENT_QUOTES,$charset);
					} else {
						$r.=htmlentities($msg[self::$fields[$this->type]["FIELD"][$f[1]]["NAME"]],ENT_QUOTES,$charset);
					}
				} elseif(array_key_exists($f[0],static::$pp)) {
					$r.=htmlentities(static::$pp[$f[0]]->t_fields[$f[2]]["TITRE"],ENT_QUOTES,$charset);
				}
				$r.="</span></td>";
				$r.="<td class='field_sort_asc_desc'>";//Colonne 3
				$r.=$this->gen_selector_asc_desc("asc_desc_".$n."_".$fields[$i]);
				$r.="</td>";
				$r.="<td class='field_sort_type'>";//Colonne 4
				$r.=$this->gen_selector_type("type_".$n."_".$fields[$i]);
				$r.="</td>";
				$r.="<td><span class='field_cancel'><input id='delete_field_button_".$n."' type='button' class='bouton' value='".$msg["raz"]."' onClick=\"this.form.delete_field.value='".$n."'; this.form.action=''; this.form.target=''; this.form.submit();\"></td>";//Colonne 6
				$r.="</tr>\n";
				$n++;
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
			$to_format[$i]["ASC_DESC"]=$this->get_global_value("asc_desc_".$i."_".$fields[$i]);
			$to_format[$i]["TYPE"]=$this->get_global_value("type_".$i."_".$fields[$i]);
		}
		return $to_format;
	}
	
	public function unformat_fields($to_unformat) {
		global $fields;
		$fields=array();
		for ($i=0; $i<count($to_unformat); $i++) {
			$fields[$i] = $to_unformat[$i]["NAME"];
			$this->set_global_value("asc_desc_".$i."_".$fields[$i], $to_unformat[$i]["ASC_DESC"]);
			$this->set_global_value("type_".$i."_".$fields[$i], $to_unformat[$i]["TYPE"]);
		}
	}
	
	/**
	 * Ajoute une colonne à la table temporaire du nom et du type précisé
	 */
	protected function add_colum_temporary_table($nomTable, $nomCol,$type) {
	
		//d'abord on ajoute la colonne
		$cmd_table = "ALTER TABLE " . $nomTable . " ADD " . $nomCol . " ";
	
		//en fonction du type on met le type mysql
		switch($type) {
			case "num":
				$cmd_table .= "integer";
				break;
			case "date":
				$cmd_table .= "date";
				break;
			case "text":
			default:
				$cmd_table .= "text";
				break;
		}
	
		//execution de l'ajout de la colonne
		pmb_mysql_query($cmd_table);
	}
	
	protected function add_values_temporary_table($temporary_table_name, $field, $type, $datas=array()) {
		$f=explode("_",$field);
		$query = "select distinct ".$this->field_keyName.", value from ".$this->field_tableName." where code_champ = ".$f[1];
		if($f[2]) {
			$query .= " and code_ss_champ = ".$f[2];
		}
		$query .= " and ".$this->field_keyName." IN (".implode(",",$datas).") order by value";
		
		//cas particulier des autorites indexees avec l'id d'autorite
		if ($this->field_tableName == "authorities_fields_global_index") {
		    $query = "
                SELECT DISTINCT num_object AS '".$this->field_keyName."', value 
                FROM ".$this->field_tableName." 
                JOIN authorities ON authorities.id_authority = ".$this->field_tableName.".".$this->field_keyName."
                WHERE code_champ = ".$f[1];
		    if($f[2]) {
		        $query .= " AND code_ss_champ = ".$f[2];
		    }
		    $query .= " AND num_object IN (".implode(",",$datas).")";
		    if ($this->sub_type) {
		        $query .= " AND type_object = ".$this->sub_type;
		    }
		    $query .= " ORDER BY value";
		}
		//
		//On met le tout dans une table temporaire
		//
		pmb_mysql_query("DROP TEMPORARY TABLE IF EXISTS ".$temporary_table_name."_update");
		pmb_mysql_query("CREATE TEMPORARY TABLE ".$temporary_table_name."_update ENGINE=MyISAM (".$query.")");
		pmb_mysql_query("alter table ".$temporary_table_name."_update add index(".$this->field_keyName.")");
			
		//
		//Et on rempli la table tri_tempo avec les éléments de la table temporaire
		//
		$requete = "UPDATE ".$temporary_table_name.", ".$temporary_table_name."_update";
		$requete .= " SET " . $temporary_table_name.".".$field." = " . $temporary_table_name."_update.value";
			
		//le lien vers la table de tri temporaire
		$requete .= " WHERE " . $temporary_table_name.".".$this->field_keyName;
		$requete .= "=" . $temporary_table_name."_update.".$this->field_keyName;
// 		$requete .= " AND ".$this->params["REFERENCE"].".".$this->params["REFERENCEKEY"]."=".$temporary_table_name.".".$this->params["REFERENCEKEY"];
		$requete .= " AND ".$temporary_table_name."_update.value IS NOT NULL";
		$requete .= " AND ".$temporary_table_name."_update.value != ''";
			
		pmb_mysql_query($requete);
	}
	
	public function sort_datas($datas=array()) {
		global $fields;
		
		$sub_query="";
		if (count($datas)) {
		    switch ($this->type) {
		        case "authorities":
		            $sub_query ="SELECT DISTINCT num_object AS ".$this->field_keyName." FROM authorities WHERE num_object in (".implode(",",$datas).") AND type_object = ".$this->sub_type;
		            break;
		        default:
		            $sub_query = "SELECT DISTINCT ".$this->field_keyName." FROM ".$this->field_tableName." WHERE ".$this->field_keyName." IN (".implode(",",$datas).")";
		            break;
		    }
		}
		
		$temporary_table_name = "tempo_".str_replace([" ","."],"_",microtime());
		$query = "CREATE TEMPORARY TABLE $temporary_table_name ENGINE=MyISAM ($sub_query)";
		pmb_mysql_query($query);
		$query = "ALTER TABLE $temporary_table_name ADD PRIMARY KEY (" . $this->field_keyName.")";
		pmb_mysql_query($query);
		$orderby = '';
		for ($i=0; $i<count($fields); $i++) {
			$asc_desc = $this->get_global_value("asc_desc_".$i."_".$fields[$i]);
			$type = $this->get_global_value("type_".$i."_".$fields[$i]);
				
			//on ajoute la colonne au orderby
			$orderby .= $fields[$i]." ".$asc_desc.",";
			
			//on ajoute la colonne à la table temporaire
			$this->add_colum_temporary_table($temporary_table_name, $fields[$i], $type);
			
			$this->add_values_temporary_table($temporary_table_name, $fields[$i], $type, $datas);
			
		}
		if ($orderby!="") {
			//on enleve la derniere virgule
			$orderby = substr($orderby, 0, strlen($orderby) - 1);
			
			$query = "SELECT " . $this->field_keyName . " FROM " . $temporary_table_name;
			$query .= " ORDER BY " . $orderby;
			$result = pmb_mysql_query($query);
			if($result) {
				if(pmb_mysql_num_rows($result)) {
					$datas = array();
					while ($row = pmb_mysql_fetch_object($result)) {
						$datas[] = $row->{$this->field_keyName};
					}
				}
			}
		}
		
		return $datas;	
	}
	
	public static function get_types() {
		return array(
				"alpha" => "msg:frbr_sort_field_alpha",
				"num" => "msg:frbr_sort_field_num",
				"date" => "msg:frbr_sort_field_date",
		);
	}
	
	public static function get_directions() {
		return array(
				"asc" => "msg:tri_croissant",
				"desc" => "msg:tri_decroissant"
		);
	}
}
?>