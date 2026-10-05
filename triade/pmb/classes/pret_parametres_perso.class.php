<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pret_parametres_perso.class.php,v 1.28.10.1 2025/03/12 17:27:47 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/templates/pret_parametres_perso.tpl.php");

class pret_parametres_perso extends parametres_perso {

	//Créateur : passer dans $prefix le type de champs persos et dans $base_url l'url a appeller pour les formulaires de gestion
	public function __construct($prefix,$base_url="",$option_visibilite=array()) {
		global $_custom_prefixe_;
		global $charset;

		$this->option_visibilite=$option_visibilite;

		$this->prefix=$prefix;
		$this->base_url=$base_url;
		$_custom_prefixe_=$prefix;

		//Lecture des champs
		$this->no_special_fields=0;
		$this->t_fields=array();
		if(!isset(self::$st_fields[$this->prefix])){
			$requete="select idchamp, name, titre, type, datatype, obligatoire, options, multiple, search, export, filters, exclusion_obligatoire, pond, opac_sort, comment from ".$this->prefix."_custom order by ordre";
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)==0){
				self::$st_fields[$this->prefix] = false;
			}else {
				while ($r=pmb_mysql_fetch_object($resultat)) {
					self::$st_fields[$this->prefix][$r->idchamp]["DATATYPE"]=$r->datatype;
					self::$st_fields[$this->prefix][$r->idchamp]["NAME"]=$r->name;
					self::$st_fields[$this->prefix][$r->idchamp]["COMMENT"]=$r->comment;
					self::$st_fields[$this->prefix][$r->idchamp]["TITRE"]=$r->titre;
					self::$st_fields[$this->prefix][$r->idchamp]["TYPE"]=$r->type;
					self::$st_fields[$this->prefix][$r->idchamp]["OPTIONS"][0] =_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS");
					self::$st_fields[$this->prefix][$r->idchamp]["MANDATORY"]=$r->obligatoire;
					self::$st_fields[$this->prefix][$r->idchamp]["FILTERS"]=$r->filters;
					self::$st_fields[$this->prefix][$r->idchamp]["POND"]=$r->pond;
				}
			}
		}
		if(self::$st_fields[$this->prefix] == false){
			$this->no_special_fields=1;
		}else{
			$this->t_fields=self::$st_fields[$this->prefix];
		}
	}

	//Affichage de l'écran de gestion des paramètres perso (la liste de tous les champs définis)
	public function show_field_list() {
		list_custom_fields_loans_ui::set_prefix($this->prefix);
		list_custom_fields_loans_ui::set_option_visibilite($this->option_visibilite);
		$list_custom_fields_loans_ui = new list_custom_fields_loans_ui();
		return $list_custom_fields_loans_ui->get_display_list();
	}

	//Affichage du formulaire d'édition d'un champ perso
	public function show_edit_form($idchamp=0) {
		global $charset;
		global $type_list_empr;
		global $datatype_list;
		global $form_loan_edit;
		global $include_path;
		global $msg;

		if ($idchamp!=0 and $idchamp!="") {
			$requete="select idchamp, name, titre, type, datatype, options, multiple, obligatoire, ordre, search, export, filters, exclusion_obligatoire, pond, comment, opac_sort from ".$this->prefix."_custom where idchamp=$idchamp";
			$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error());
			$r=pmb_mysql_fetch_object($resultat);

			$name=$r->name;
			$titre=htmlentities($r->titre,ENT_QUOTES,$charset);
			$type=$r->type;
			$datatype=$r->datatype;
			$options=htmlentities($r->options,ENT_QUOTES,$charset);
			$obligatoire=$r->obligatoire;
			$ordre=$r->ordre;
			$filters=$r->filters;
			$pond=$r->pond;
			$comment=$r->comment;
			$opac_sort=$r->opac_sort;
			$form_loan_edit=str_replace("!!form_titre!!",sprintf($msg["parperso_field_edition"],$name),$form_loan_edit);
			$form_loan_edit=str_replace("!!action!!","update",$form_loan_edit);

			if ($r->options!="") {
				$param=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS");
				$form_loan_edit=str_replace("!!for!!",$param["FOR"],$form_loan_edit);
			} else {
				$form_loan_edit=str_replace("!!for!!","",$form_loan_edit);
			}
			$form_loan_edit=str_replace("!!supprimer!!","&nbsp;<input type='button' class='bouton' value='".$msg["63"]."' onClick=\"if (confirm('".$msg["parperso_delete_field"]."')) { this.form.action.value='delete'; this.form.submit();} else return false;\">",$form_loan_edit);
		} else {
			$name='';
			$titre='';
			$type='';
			$datatype='';
			$options='';
			$obligatoire='';
			$ordre='';
			$filters='';
			$pond='';
			$comment='';
			$opac_sort='';
			$form_loan_edit=str_replace("!!form_titre!!",$msg["parperso_create_new_field"],$form_loan_edit);
			$form_loan_edit=str_replace("!!action!!","create",$form_loan_edit);
			$form_loan_edit=str_replace("!!for!!","",$form_loan_edit);
			$form_loan_edit=str_replace("!!supprimer!!","",$form_loan_edit);
		}

		$onclick="openPopUp('".$include_path."/options_empr/options.php?name=&type='+this.form.type.options[this.form.type.selectedIndex].value+'&_custom_prefixe_=".$this->prefix."','options');";
		$form_loan_edit=str_replace("!!onclick!!",$onclick,$form_loan_edit);

		$form_loan_edit=str_replace("!!idchamp!!",$idchamp,$form_loan_edit);
		$form_loan_edit=str_replace("!!name!!",$name,$form_loan_edit);
		$form_loan_edit=str_replace("!!titre!!",htmlentities($titre, ENT_QUOTES, $charset),$form_loan_edit);
		$form_loan_edit=str_replace("!!pond!!",$pond,$form_loan_edit);
		$form_loan_edit=str_replace("!!comment!!",$comment,$form_loan_edit);

		//Liste des types
		$t_list="<select name='type'>\n";
		reset($type_list_empr);
		foreach ($type_list_empr as $key => $val) {
			$t_list.="<option value='".$key."'";
			if ($type==$key) $t_list.=" selected";
			$t_list.=">".htmlentities($val,ENT_QUOTES, $charset)."</option>\n";
		}
		$t_list.="</select>\n";
		$form_loan_edit=str_replace("!!type_list!!",$t_list,$form_loan_edit);

		//Liste des types de données
		$t_list="<select name='datatype'>\n";
		reset($datatype_list);
		foreach ($datatype_list as $key => $val) {
			$t_list.="<option value='".$key."'";
			if ($datatype==$key) $t_list.=" selected";
			$t_list.=">".htmlentities($val,ENT_QUOTES, $charset)."</option>\n";
		}
		$t_list.="</select>\n";
		$form_loan_edit=str_replace("!!datatype_list!!",$t_list,$form_loan_edit);

		$form_loan_edit=str_replace("!!options!!",$options,$form_loan_edit);

		if ($obligatoire==1) $f_obligatoire="checked"; else $f_obligatoire="";
		$form_loan_edit=str_replace("!!obligatoire_checked!!",$f_obligatoire,$form_loan_edit);

		if ($filters==1) $f_filters="checked"; else $f_filters="";
		$form_loan_edit=str_replace("!!filters_checked!!",$f_filters,$form_loan_edit);

		foreach ( $this->option_visibilite as $key => $value ) {
       		$form_loan_edit=str_replace("!!".$key."_visible!!",$value,$form_loan_edit);
		}

		$form_loan_edit=str_replace("!!ordre!!",$ordre,$form_loan_edit);
		$form_loan_edit=str_replace("!!base_url!!",$this->base_url,$form_loan_edit);

		echo $form_loan_edit;
	}

	//Validation du formulaire de création
	public function check_form() {
		global $action,$idchamp;
		global $name,$titre,$type,$_for,$multiple,$obligatoire,$exclusion,$msg,$search,$export,$filters,$pond,$opac_sort;
		//Vérification conformité du champ name
		if (!preg_match("/^[A-Za-z][A-Za-z0-9_]*$/",$name)) $this->make_error(sprintf($msg["parperso_check_field_name"],$name));
		//On vérifie que le champ name ne soit pas déjà existant
		if ($action == "update") $requete="select idchamp from ".$this->prefix."_custom where name='$name' and idchamp<>$idchamp";
		else $requete="select idchamp from ".$this->prefix."_custom where name='$name'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat) > 0) $this->make_error(sprintf($msg["parperso_check_field_name_already_used"],$name));
		if ($titre=="") $titre=$name;
		if ($_for!=$type) $this->make_error($msg["parperso_check_type"]);
		if ($multiple=="") $multiple=0;
		if ($obligatoire=="") $obligatoire=0;
		if($search=="") $search=0;
		if($export=="") $export=0;
		if($filters=="") $filters=0;
		if($exclusion=="") $exclusion=0;
		if($pond=="") $pond=1;
		if($opac_sort=="") $opac_sort=0;
	}

	//Validation des valeurs des champs soumis lors de la saisie d'une fichie emprunteur ou autre...
	public function check_submited_fields() {
		global $chk_list_empr,$charset;

		$nberrors=0;
		$this->error_message="";

		if (!$this->no_special_fields) {
			reset($this->t_fields);
			foreach ($this->t_fields as $key => $val) {
				$check_message="";
				$field=array();
				$field["ID"]=$key;
				$field["NAME"]=$this->t_fields[$key]["NAME"];
				$field["COMMENT"]=$this->t_fields[$key]["COMMENT"];
				$field["MANDATORY"]=$this->t_fields[$key]["MANDATORY"];
				$field["ALIAS"]=$this->t_fields[$key]["TITRE"];
				$field["OPTIONS"]=$this->t_fields[$key]["OPTIONS"];
				$field["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
				$field["PREFIX"]=$this->prefix;
				$field["FILTERS"]=$this->t_fields[$key]["FILTERS"];
				global ${$val["NAME"]};
				$field['VALUES'] = ${$val["NAME"]};
				eval($chk_list_empr[$this->t_fields[$key]["TYPE"]]."(\$field,\$check_message);");
				if ($check_message!="") {
					$nberrors++;
					$this->error_message.="<p>".$check_message."</p>";
				}
			}
		}
		return $nberrors;
	}

	//Affichage des champs à saisir dans le formulaire de modification/création d'un emprunteur ou autre
	public function show_editable_fields($id,$from_z3950=false) {
		global $aff_list_empr,$charset;
		$perso=array();

		if (!$this->no_special_fields) {
			if(!$from_z3950){
				$this->get_values($id);
			}
			$check_scripts="";
			reset($this->t_fields);
			foreach ($this->t_fields as $key => $val) {
				$t=array();
				$t["ID"]=$key;
				$t["NAME"]=$val["NAME"];
				$t["TITRE"]=$val["TITRE"];
				$t["COMMENT"]=$val["COMMENT"];
				if($t["COMMENT"]){
					$t["COMMENT_DISPLAY"]="&nbsp;<span class='pperso_comment' title='".htmlentities($t["COMMENT"],ENT_QUOTES, $charset)."' >".nl2br(htmlentities($t["COMMENT"],ENT_QUOTES, $charset))."</span>";
				} else {
					$t["COMMENT_DISPLAY"]="";
				}
				$field=array();
				$field["ID"]=$key;
				$field["NAME"]=$this->t_fields[$key]["NAME"];
				$field["COMMENT"]=$this->t_fields[$key]["COMMENT"];
				$field["MANDATORY"]=$this->t_fields[$key]["MANDATORY"];
				$field["FILTERS"]=$this->t_fields[$key]["FILTERS"];
				$field["ALIAS"]=$this->t_fields[$key]["TITRE"];
				$field["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
				$field["OPTIONS"]=$this->t_fields[$key]["OPTIONS"];
				if(!isset($this->values[$key])) $this->values[$key] = '';
				$field["VALUES"]=$this->values[$key];
				$field["PREFIX"]=$this->prefix;
				$field["ID_ORIGINE"]=$id;
				eval("\$aff=".$aff_list_empr[$this->t_fields[$key]['TYPE']]."(\$field,\$check_scripts);");
				$t["AFF"]=$aff;
				$t["NAME"]=$field["NAME"];
				$t["MANDATORY"]=$field["MANDATORY"];
				$perso["FIELDS"][]=$t;
			}

			//Compilation des javascripts de validité renvoyés par les fonctions d'affichage
			$check_scripts="<script>function cancel_submit(message) { alert(message); return false;}\nfunction check_form() {\n".$check_scripts."\nreturn true;\n}\n</script>";
			$perso["CHECK_SCRIPTS"]=$check_scripts;
		} else
			$perso["CHECK_SCRIPTS"]="<script>function check_form() { return true; }</script>";
		return $perso;
	}

	//Affichage des champs en lecture seule pour visualisation d'un fiche emprunteur ou autre...
	public function show_fields($id) {
		global $val_list_empr;
		global $charset;
		$perso=array();
		//Récupération des valeurs stockées pour l'emprunteur
		$this->get_values($id);
		if (!$this->no_special_fields) {
			//Affichage champs persos
			$c=0;
			reset($this->t_fields);
			foreach ($this->t_fields as $key => $val) {
				$t=array();
				$t["TITRE"]="<b>".htmlentities($val["TITRE"],ENT_QUOTES,$charset)." : </b>";
				if(!isset($val["OPAC_SHOW"])) $val["OPAC_SHOW"] = '';
				$t["OPAC_SHOW"]=$val["OPAC_SHOW"];
				if(!isset($this->values[$key])) $this->values[$key] = array();
				if(!isset(static::$fields[$this->prefix][$key])){
					static::$fields[$this->prefix][$key]=array();
					static::$fields[$this->prefix][$key]["ID"]=$key;
					static::$fields[$this->prefix][$key]["NAME"]=$this->t_fields[$key]["NAME"];
					static::$fields[$this->prefix][$key]["COMMENT"]=$this->t_fields[$key]["COMMENT"];
					static::$fields[$this->prefix][$key]["MANDATORY"]=$this->t_fields[$key]["MANDATORY"];
					static::$fields[$this->prefix][$key]["FILTERS"]=$this->t_fields[$key]["FILTERS"];
					static::$fields[$this->prefix][$key]["ALIAS"]=$this->t_fields[$key]["TITRE"];
					static::$fields[$this->prefix][$key]["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
					static::$fields[$this->prefix][$key]["OPTIONS"]=$this->t_fields[$key]["OPTIONS"];
					static::$fields[$this->prefix][$key]["VALUES"]=$this->values[$key];
					static::$fields[$this->prefix][$key]["PREFIX"]=$this->prefix;
				}
				$aff=$val_list_empr[$this->t_fields[$key]["TYPE"]](static::$fields[$this->prefix][$key],$this->values[$key]);

				if (is_array($aff) && $aff['ishtml'] == true)$t["AFF"] = $aff["value"];
				else $t["AFF"]=htmlentities($aff,ENT_QUOTES,$charset);
				$t["NAME"]=$this->t_fields[$key]["NAME"];
				$t["COMMENT"]=$this->t_fields[$key]["COMMENT"];
				$t["ID"]=$key;
				$perso["FIELDS"][]=$t;
			}
		}
		return $perso;
	}

	public function get_formatted_output($values,$field_id) {
		global $val_list_empr,$charset;

		if(!isset(static::$fields[$this->prefix][$field_id])){
		    if(!empty($this->t_fields[$field_id])){
    			static::$fields[$this->prefix][$field_id]=array();
    			static::$fields[$this->prefix][$field_id]["ID"]=$field_id;
    			static::$fields[$this->prefix][$field_id]["NAME"]=$this->t_fields[$field_id]["NAME"];
    			static::$fields[$this->prefix][$field_id]["COMMENT"]=$this->t_fields[$field_id]["COMMENT"];
    			static::$fields[$this->prefix][$field_id]["MANDATORY"]=$this->t_fields[$field_id]["MANDATORY"];
    			static::$fields[$this->prefix][$field_id]["FILTERS"]=$this->t_fields[$field_id]["FILTERS"];
    			static::$fields[$this->prefix][$field_id]["ALIAS"]=$this->t_fields[$field_id]["TITRE"];
    			static::$fields[$this->prefix][$field_id]["DATATYPE"]=$this->t_fields[$field_id]["DATATYPE"];
    			static::$fields[$this->prefix][$field_id]["OPTIONS"]=$this->t_fields[$field_id]["OPTIONS"];
    			static::$fields[$this->prefix][$field_id]["VALUES"]=$values;
    			static::$fields[$this->prefix][$field_id]["PREFIX"]=$this->prefix;
		    }
	    }
	    if (!empty($this->t_fields[$field_id])) {
    		$aff = $val_list_empr[$this->t_fields[$field_id]["TYPE"]](static::$fields[$this->prefix][$field_id],$values);
	    }
	    if (isset($aff)) {
    	    if (is_array($aff)) {
    	        return $aff['withoutHTML'];
    	    }
    		return $aff;
	    }
	    return '';
	}

	//Gestion des actions en administration
	public function proceed() {
		global $action;
		global $name,$titre,$type,$datatype,$_options,$multiple,$obligatoire,$search,$export,$filters,$exclusion,$ordre,$idchamp,$id,$pond,$opac_sort, $comment;

		switch ($action) {
			case "nouv":
				$this->show_edit_form();
				break;
			case "edit":
				$this->show_edit_form($id);
				break;
			case "create":
				$this->check_form();
				$requete="select max(ordre) from ".$this->prefix."_custom";
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)!=0)
					$ordre=pmb_mysql_result($resultat,0,0)+1;
				else
					$ordre=1;

				$requete="insert into ".$this->prefix."_custom set name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=".intval($ordre).", search=$search, export=$export, filters=$filters, exclusion_obligatoire=$exclusion, opac_sort=$opac_sort, comment='".$comment."' ";
				pmb_mysql_query($requete);
				echo $this->show_field_list();
				break;
			case "update":
				$this->check_form();
				$requete="update ".$this->prefix."_custom set name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=".intval($ordre).", search=$search, export=$export, filters=$filters, exclusion_obligatoire=$exclusion, pond=$pond, opac_sort=$opac_sort, comment='".$comment."' where idchamp=$idchamp";
				pmb_mysql_query($requete);
				echo $this->show_field_list();
				break;
			case "up":
				$requete="select ordre from ".$this->prefix."_custom where idchamp=$id";
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select max(ordre) as ordre from ".$this->prefix."_custom where ordre<$ordre";
				$resultat=pmb_mysql_query($requete);
				$ordre_max=@pmb_mysql_result($resultat,0,0);
				if ($ordre_max) {
					$requete="select idchamp from ".$this->prefix."_custom where ordre=$ordre_max limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_max=pmb_mysql_result($resultat,0,0);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre_max."' where idchamp=$id";
					pmb_mysql_query($requete);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre."' where idchamp=".$idchamp_max;
					pmb_mysql_query($requete);
				}
				echo $this->show_field_list();
				break;
			case "down":
				$requete="select ordre from ".$this->prefix."_custom where idchamp=$id";
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select min(ordre) as ordre from ".$this->prefix."_custom where ordre>$ordre";
				$resultat=pmb_mysql_query($requete);
				$ordre_min=@pmb_mysql_result($resultat,0,0);
				if ($ordre_min) {
					$requete="select idchamp from ".$this->prefix."_custom where ordre=$ordre_min limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_min=pmb_mysql_result($resultat,0,0);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre_min."' where idchamp=$id";
					pmb_mysql_query($requete);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre."' where idchamp=".$idchamp_min;
					pmb_mysql_query($requete);
				}
				echo $this->show_field_list();
				break;
			case "delete":
				$requete="delete from ".$this->prefix."_custom where idchamp=$idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_values where ".$this->prefix."_custom_champ=$idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_lists where ".$this->prefix."_custom_champ=$idchamp";
				pmb_mysql_query($requete);
				echo $this->show_field_list();
				break;
			default:
				echo $this->show_field_list();
		}
	}
}