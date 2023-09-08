<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_sort_fields.class.php,v 1.3 2017-06-01 09:28:16 dgoron Exp $

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