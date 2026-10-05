<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_tpl.class.php,v 1.25 2023/05/04 09:22:14 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/template.class.php");
require_once("$include_path/templates/bannette_tpl.tpl.php");
require_once($include_path."/h2o/pmb_h2o.inc.php");

class bannette_tpl extends template {
	
	protected static $table_name = 'bannette_tpl';
	protected static $field_name = 'bannettetpl_id';
	
	protected static $base_url;
	
	protected static function get_data_query($id) {
		$id = intval($id);
		return "SELECT 'bannette' as template_type, bannettetpl_name as template_name, bannettetpl_comment as template_comment, bannettetpl_tpl as template_content  FROM bannette_tpl WHERE bannettetpl_id='".$id."'";
	}
	
	public function get_fields_options() {
		global $msg, $charset;
		
		$fields_options="<optgroup label='".$msg["bannette_tpl_insert_group_bannette"]."'>";
		
		$fields_options.="<option value='{{info.id}}'>".$msg["bannette_tpl_insert_id"]."</option>";
		$fields_options.="<option value='{{info.name}}'>".$msg["bannette_tpl_insert_name"]."</option>";
		$fields_options.="<option value='{{info.opac_name}}'>".$msg["bannette_tpl_insert_opac_name"]."</option>";
		$fields_options.="<option value='{{info.header}}'>".$msg["bannette_tpl_insert_header"]."</option>";
		$fields_options.="<option value='{{info.footer}}'>".$msg["bannette_tpl_insert_footer"]."</option>";
		$fields_options.="<option value='
		{% for sommaire in sommaires %}
			{{sommaire.level}} - {{sommaire.title}}
		{% endfor %}
				'>".$msg["bannette_tpl_insert_chapters"]."</option>";
		$fields_options.="<option value='
		{% for sommaire in sommaires %}
			{% for record in sommaire.records %}
				{{record.render}}
			{% endfor %}
		{% endfor %}
				'>".$msg["bannette_tpl_insert_records_by_chapters"]."</option>";
		$fields_options.="<option value='{{sommaires.1.title}}'>".$msg["bannette_tpl_insert_title_chapter"]."</option>";
		$fields_options.="<option value='{{sommaires.1.level}}'>".$msg["bannette_tpl_insert_level_chapter"]."</option>";
		$fields_options.="<option value='
		{% for record in records %}
			{{record.render}}
		{% endfor %}
				'>".$msg["bannette_tpl_insert_records_render"]."</option>";
		$fields_options.="<option value='{{records.length}}'>".$msg["bannette_tpl_insert_records_length"]."</option>";
		$fields_options.="<option value='{{records.length_total}}'>".$msg["bannette_tpl_insert_records_length_total"]."</option>";
		$fields_options.="<option value='{{info.date_diff}}'>".$msg["bannette_tpl_insert_date_diff"]."</option>";
		$fields_options.="<option value='{{info.equation}}'>".$msg["bannette_tpl_insert_equation"]."</option>";
		$fields_options.="<option value='{{info.nb_abonnes}}'>".$msg["bannette_tpl_insert_nb_abonnes"]."</option>";
		$fields_options.="</optgroup>";
		
		$fields_options.="<optgroup label='".$msg["bannette_tpl_insert_group_empr"]."'>";
		$fields_options.="<option value='{{empr.name}}'>".$msg["bannette_tpl_insert_empr_name"]."</option>";
		$fields_options.="<option value='{{empr.first_name}}'>".$msg["bannette_tpl_insert_empr_first_name"]."</option>";
		$fields_options.="<option value='{{empr.civ}}'>".$msg["bannette_tpl_insert_empr_civ"]."</option>";
		$fields_options.="<option value='{{empr.cb}}'>".$msg["bannette_tpl_insert_empr_cb"]."</option>";
		$fields_options.="<option value='{{empr.login}}'>".$msg["bannette_tpl_insert_empr_login"]."</option>";
		$fields_options.="<option value='{{empr.mail}}'>".$msg["bannette_tpl_insert_empr_mail"]."</option>";
		$fields_options.="<option value='{{empr.name_and_adress}}'>".$msg["bannette_tpl_insert_empr_name_and_adress"]."</option>";
		//$fields_options.="<option value='{{empr.statut_id}}'>".$msg["bannette_tpl_insert_empr_statut_id"]."</option>";
		$fields_options.="<option value='{{empr.statut_lib}}'>".$msg["bannette_tpl_insert_empr_statut_lib"]."</option>";
		//$fields_options.="<option value='{{empr.categ_id}}'>".$msg["bannette_tpl_insert_empr_categ_id"]."</option>";
		$fields_options.="<option value='{{empr.categ_lib}}'>".$msg["bannette_tpl_insert_empr_categ_lib"]."</option>";
		//$fields_options.="<option value='{{empr.codestat_id}}'>".$msg["bannette_tpl_insert_empr_codestat_id"]."</option>";
		$fields_options.="<option value='{{empr.codestat_lib}}'>".$msg["bannette_tpl_insert_empr_codestat_lib"]."</option>";
		//$fields_options.="<option value='{{empr.langopac_code}}'>".$msg["bannette_tpl_insert_empr_langopac_code"]."</option>";
		//$fields_options.="<option value='{{empr.langopac_lib}}'>".$msg["bannette_tpl_insert_empr_langopac_lib"]."</option>";
		$fields_options.="<option value='{{empr.all_information}}'>".$msg["bannette_tpl_insert_empr_tout"]."</option>";
		$fields_options.="<option value='".htmlentities("<a href='{{global.opac_url_base}}empr.php?code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["bannette_tpl_insert_empr_connect"]."</a>",ENT_QUOTES,$charset)."'>".$msg["bannette_tpl_insert_empr_connect"]."</option>";
		$fields_options.="<option value='".htmlentities("<a href='{{global.opac_url_base}}empr.php?tab=dsi&lvl=bannette_gerer&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["bannette_tpl_gerer_vos_alertes"]."</a>",ENT_QUOTES,$charset)."'>".$msg["bannette_tpl_gerer_vos_alertes"]."</option>";
		$fields_options.="<option value='".htmlentities("<a href='{{global.opac_url_base}}empr.php?tab=dsi&lvl=bannette&id_bannette={{info.id}}&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>{{info.name}}</a>",ENT_QUOTES,$charset)."'>".$msg["bannette_tpl_lien_vers_bannette"]."</option>";
		$fields_options.="<option value='".htmlentities("<a href='{{global.opac_url_base}}empr.php?tab=dsi&lvl=bannette_unsubscribe&id_bannette={{info.id}}&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["bannette_tpl_unsubscribe"]."</a>",ENT_QUOTES,$charset)."'>".$msg["bannette_tpl_unsubscribe"]."</option>";
		$fields_options.="</optgroup>";
		
		$fields_options.="<optgroup label='".htmlentities($msg["bannette_tpl_insert_group_loc"],ENT_QUOTES, $charset)."'>";
		$fields_options.="<option value='{{loc.name}}'>".$msg["bannette_tpl_insert_loc_name"]."</option>";
		$fields_options.="<option value='{{loc.adr1}}'>".$msg["bannette_tpl_insert_loc_adr1"]."</option>";
		$fields_options.="<option value='{{loc.adr2}}'>".$msg["bannette_tpl_insert_loc_adr2"]."</option>";
		$fields_options.="<option value='{{loc.cp}}'>".$msg["bannette_tpl_insert_loc_cp"]."</option>";
		$fields_options.="<option value='{{loc.town}}'>".$msg["bannette_tpl_insert_loc_town"]."</option>";
		$fields_options.="<option value='{{loc.phone}}'>".$msg["bannette_tpl_insert_loc_phone"]."</option>";
		$fields_options.="<option value='{{loc.email}}'>".$msg["bannette_tpl_insert_loc_email"]."</option>";
		$fields_options.="<option value='{{loc.website}}'>".$msg["bannette_tpl_insert_loc_website"]."</option>";
		$fields_options.="</optgroup>";
		return $fields_options;
	}
	
	protected function get_form_name() {
		return "bannette_tpl_form";
	}
	
	protected function get_content_form() {
		global $charset;
		global $bannette_tpl_content_form;
	
		$content_form=$bannette_tpl_content_form;
		
		$fields_options="<select id='fields_options' name='fields_options'>";
		$fields_options.= $this->get_fields_options();
		$fields_options.="</select>";
		$content_form=str_replace('!!fields_options!!', $fields_options ?? "", $content_form);
		$content_form=str_replace('!!content!!', htmlentities($this->content ?? "",ENT_QUOTES, $charset), $content_form);
		return $content_form;
	}
	
	public function save() {
		global $msg;
		global $include_path;
			
		if(!$this->name) return false;
	
		$query  = "SET  ";
		$query .= "bannettetpl_name='".addslashes($this->name)."', ";
		$query .= "bannettetpl_comment='".addslashes($this->comment)."', ";
		$query .= "bannettetpl_tpl='".addslashes($this->content)."' ";
	
		if($this->id) {
			// update
			$query = "UPDATE ".static::$table_name." $query WHERE ".static::$field_name."=".$this->id." ";
			if(!pmb_mysql_query($query)) {
				require_once("$include_path/user_error.inc.php");
				warning($msg["template_modifier"], $msg["template_modifier_erreur"]);
				return false;
			}
		} else {
			// creation
			$query = "INSERT INTO ".static::$table_name." ".$query;
			if(pmb_mysql_query($query)) {
				$this->id=pmb_mysql_insert_id();
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg["template_ajouter"], $msg["template_ajouter_erreur"]);
				return false;
			}
		}
			
		return true;
	}

	public static function get_base_url() {
		global $base_path;
		if(!isset(static::$base_url)) {
			static::$base_url = $base_path.'/edit.php?categ=tpl&sub=bannette';
		}
		return static::$base_url;
	}
	
	public static function gen_tpl_select($select_name="form_bannette_tpl", $selected_id=0, $onchange="", $invisible_default=0) {		
		global $msg;
		
		$requete = "SELECT bannettetpl_id, concat(bannettetpl_name,'. ',bannettetpl_comment) as nom  FROM bannette_tpl ORDER BY bannettetpl_name ";
		if($invisible_default) {
			return gen_liste ($requete, "bannettetpl_id", "nom", $select_name, $onchange, $selected_id, 0, $msg["bannette_tpl_list_default"], "","", 0) ;
		} else {
			return gen_liste ($requete, "bannettetpl_id", "nom", $select_name, $onchange, $selected_id, 0, $msg["bannette_tpl_list_default"], 0,$msg["bannette_tpl_list_default"], 0) ;
		}	
	}
} // fin class 
