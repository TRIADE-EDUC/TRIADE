<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_view.class.php,v 1.26.4.1 2025/03/14 13:27:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des vues Opac

// inclusions principales
global $class_path, $include_path;
require_once("$include_path/templates/opac_view.tpl.php");
require_once("$class_path/param_subst.class.php");
require_once($class_path."/opac_filters.class.php");
require_once("$class_path/search.class.php");
require_once("$class_path/quotas.class.php");
require_once("$class_path/interface/admin/interface_admin_opac_form.class.php");

class opac_view {

	public $id=0;
	public $id_empr=0;
	public $name='';
	public $requete='';
	public $human_query='';
	public $comment='';
	public $visible=0;
	public $last_gen='';					//datetime derniere generation
	public $ttl=86400;						//duree de validite
	public $opac_view_wo_query = 0;			//pas de recherche mc associée
	public $view_list_empr = 0;
	public $view_list_empr_default;
	public $opac_views_list = array();
	public $selector = "";
	public $search_class = null;
	protected static $search_parameters = array();
	public $param_subst = null;
	public $opac_filters = null;
	
	// constructeur
	public function __construct($id=0,$id_empr=0) {
		// si id, allez chercher les infos dans la base
		if(!$id_empr) $this->search_class=new search(false);
		$this->id = intval($id);
		$this->id_empr = intval($id_empr);
		$this->fetch_data();
		return $this->id;
	}
	
	// récupération des infos en base
	public function fetch_data() {
		if($this->id){
			$myQuery = pmb_mysql_query("SELECT * FROM opac_views WHERE opac_view_id='".$this->id."' LIMIT 1");
			$myreq= pmb_mysql_fetch_object($myQuery);
			$this->name=$myreq->opac_view_name;
			$this->requete=$myreq->opac_view_query;
			if ($this->requete) {
				$this->opac_view_wo_query = 0;
				$this->human_query = $this->search_class->make_serialized_human_query($this->requete) ;
			} else {
				$this->opac_view_wo_query = 1;
				$this->human_query = '';
			}
			$this->comment=$myreq->opac_view_comment;
			$this->visible=$myreq->opac_view_visible;
			$this->last_gen=$myreq->opac_view_last_gen;
			$this->ttl=$myreq->opac_view_ttl;
	
			$this->param_subst=new param_subst("opac", "opac_view",$this->id);
			$this->opac_filters=new opac_filters($this->id);
		}
		$this->view_list_empr=array();
		$this->view_list_empr_default=0;
		if($this->id_empr){
			// vues selectionnées pour empr
			$myQuery = pmb_mysql_query("SELECT * FROM opac_views_empr WHERE emprview_empr_num='".$this->id_empr."' ");
			if(pmb_mysql_num_rows($myQuery)){
				while(($r=pmb_mysql_fetch_object($myQuery))) {
					if($r->emprview_default) $this->view_list_empr_default=$r->emprview_view_num;
					$this->view_list_empr[]=$r->emprview_view_num;
				}
			}
		}
		$this->get_list();
	}
	
	public function get_list($name='', $value_selected=0) {
		global $charset;
		$myQuery = pmb_mysql_query("SELECT * FROM opac_views order by opac_view_name ");
		$this->opac_views_list=array();
	
		$selector = "<select name='$name' id='$name'>";
		if(pmb_mysql_num_rows($myQuery)){
			$i=0;
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->opac_views_list[$i]=new stdClass();
				$this->opac_views_list[$i]->id=$r->opac_view_id;
				$this->opac_views_list[$i]->name=$r->opac_view_name;
				$this->opac_views_list[$i]->visible=$r->opac_view_visible;
				$this->opac_views_list[$i]->query=$r->opac_view_query;
				$this->opac_views_list[$i]->comment=$r->opac_view_comment;
				$selector .= "<option value='".$r->opac_view_id."'";
				$r->opac_view_id == $value_selected ? $selector .= ' selected=\'selected\'>' : $selector .= '>';
		 		$selector .= htmlentities($r->opac_view_name,ENT_QUOTES, $charset).'</option>';
				$i++;
			}
		}
		$selector .= '</select>';
		$this->selector=$selector;
		return true;
	
	}
	// fonction de mise à jour ou de création
	public function update($value) {
		global $msg;
		$fields="";
		foreach($value as $key => $val) {
			if($fields) $fields.=",";
			$fields.=" $key='$val' ";
		}
	
		if($this->id) {
			// modif
			$erreur=pmb_mysql_query("UPDATE opac_views SET $fields WHERE opac_view_id=".$this->id);
			if(!$erreur) {
				error_message($msg["opac_view_form_edit"], $msg["opac_view_form_add_error"],1);
				exit;
			}
		} else {
			// create
			$erreur=pmb_mysql_query("INSERT INTO opac_views SET $fields ");
			$this->id = pmb_mysql_insert_id();
			if(!$erreur) {
				error_message($msg["opac_view_form_edit"], $msg["opac_view_form_add_error"],1);
				exit;
			}
		}
	
		// Création/suppression table associée si besoin
		if ($this->opac_view_wo_query) {
			$q = "drop table if exists opac_view_notices_".$this->id;
			pmb_mysql_query($q);
		} else {
			$req_create="create table if not exists opac_view_notices_".$this->id." (
			opac_view_num_notice int(20) not null default 0,
			PRIMARY KEY (opac_view_num_notice)
			)";
			pmb_mysql_query($req_create);
		}
	
	
		// rafraichissement des données
		$this->fetch_data();
		return $this->id;
	}
	
	public function update_form() {
		global $name,$requete,$comment,$opac_view_form_visible,$ttl,$opac_view_wo_query;
	
		$value = new stdClass();
		$value->opac_view_name=$name;
		if ($opac_view_wo_query || !isset($requete)) {
			$this->opac_view_wo_query = 1;
			$value->opac_view_query='';
		} else {
			$this->opac_view_wo_query = 0;
			$value->opac_view_query=$requete;
		}
		if (isset($comment)) {
			$value->opac_view_comment=$comment;
		} else {
			$value->opac_view_comment='';
		}
		if(isset($opac_view_form_visible)) {
			$value->opac_view_visible=$opac_view_form_visible;
		} else {
			$value->opac_view_visible=$this->visible;
		}
		if(isset($ttl)) {
			$value->opac_view_ttl = $ttl;
		} else {
			$value->opac_view_ttl=$this->ttl;
		}
		$this->update($value);
		$this->opac_filters->save_all_form();
	}
	
	public function gen() {
		static::apply_opac_search_parameters();
		for($i=0;$i<count($this->opac_views_list);$i++) {
			$query="SHOW TABLES LIKE 'opac_view_notices_".$this->opac_views_list[$i]->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$req="TRUNCATE TABLE opac_view_notices_".$this->opac_views_list[$i]->id;
				pmb_mysql_query($req);
			}
			if($this->opac_views_list[$i]->query) {
				$this->search_class->unserialize_search($this->opac_views_list[$i]->query);
				$table=$this->search_class->make_search() ;
				$req="INSERT ignore INTO opac_view_notices_".$this->opac_views_list[$i]->id." ( opac_view_num_notice) select notice_id from $table ";
				pmb_mysql_query($req);
				pmb_mysql_query("drop table $table");
			}
		}
		$req="update opac_views set opac_view_last_gen=now()";
		pmb_mysql_query($req);
		static::restore_gestion_search_parameters();
	}
	
	// fonction générant le form de saisie
	public function get_form() {
		global $msg,$tpl_opac_view_content_form, $tpl_opac_view_create_content_form, $charset,$suite;
	
		$interface_form = new interface_admin_opac_form('opac_view_form');
		
		// titre formulaire
		if($this->id) {
			global $suite,$requete;
			if($suite== 'transform_equ') {
				$this->requete=stripslashes($requete);
				$this->human_query =$this->search_class->make_serialized_human_query($this->requete) ;
			}
			$interface_form->set_label($msg["opac_view_modifier"]);
			$button_modif_requete = "onClick=\"document.modif_requete_form.submit();\" ";
			$form_modif_requete = $this->make_hidden_search_form();
			$content_form=$tpl_opac_view_content_form;
		} else {
			$content_form=$tpl_opac_view_create_content_form;
			$interface_form->set_label($msg["opac_view_add"]);
			$button_modif_requete ="";
			$form_modif_requete ="";
		}
		// Champ
		$opac_visible_selected= "!!opac_visible_selected_".$this->visible."!!";
		$content_form = str_replace($opac_visible_selected, "selected=selected", $content_form);
		$content_form = str_replace('!!name!!', htmlentities($this->name,ENT_QUOTES,$charset), $content_form);
		$content_form = str_replace('!!comment!!',htmlentities($this->comment,ENT_QUOTES,$charset) , $content_form);
		$content_form = str_replace('!!last_gen!!',(($this->last_gen!=NULL)?htmlentities(formatdate($this->last_gen,ENT_QUOTES,$charset)):'') , $content_form);
		$content_form = str_replace('!!ttl!!',htmlentities($this->ttl,ENT_QUOTES,$charset) , $content_form);
	
		// recherche multicritères
		if (!$suite== 'transform_equ' && $this->opac_view_wo_query) {
			$content_form = str_replace('!!opac_view_w_query_checked!!','',$content_form);
			$content_form = str_replace('!!opac_view_wo_query_checked!!',"checked='checked'",$content_form);
		} else {
			$content_form = str_replace('!!opac_view_wo_query_checked!!','',$content_form);
			$content_form = str_replace('!!opac_view_w_query_checked!!',"checked='checked'",$content_form);
		}
	
		$content_form = str_replace('!!opac_view_id!!', $this->id,  $content_form);
		$content_form = str_replace('!!search_build!!', $button_modif_requete,  $content_form);
		$content_form = str_replace('!!requete_human!!', $this->human_query, $content_form);
		$content_form = str_replace('!!requete!!', htmlentities($this->requete,ENT_QUOTES, $charset), $content_form);
	
		// param subst
		if(isset($this->param_subst))$content_form = str_replace('!!parameters!!', $this->param_subst->get_form_list("./admin.php?categ=opac&sub=opac_view&section=list&opac_view_id=".$this->id."&action=param"), $content_form);
		else $content_form=str_replace('!!parameters!!',"", $content_form);
	
		// elements visibles: filtres
		if(isset($this->opac_filters))$content_form = str_replace('!!filters!!', $this->opac_filters->show_all_form(), $content_form);
		else $content_form=str_replace('!!filters!!',"", $content_form);
	
		$form = "
		<script type='text/javascript'>
			function check_link(id) {
				w=window.open(document.getElementById(id).value);
				w.focus();
			}
		</script>";
		$form .= $form_modif_requete;
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg["confirm_suppr"])
		->set_content_form($content_form)
		->set_table_name('opac_views')
		->set_field_focus('name');
		$form .= $interface_form->get_display();
		return $form;
	}
	
	public function get_form_param() {
		return $this->param_subst->exec_param_form("./admin.php?categ=opac&sub=opac_view&section=list&opac_view_id=".$this->id."&action=param");
	}
	
	public function do_sel_list() {
		global $tpl_opac_view_list_sel_tableau,$tpl_opac_view_list_sel_tableau_ligne;
		global $pmb_opac_view_class;
		global $include_path,$lang,$msg;
		global $charset;
	
		// on reprend...
		global $pmb_opac_view_activate;
		$j=0;
		$disabled = "";
		//on a une classe spécifique pour la gestion des vues...
		//on ne peut pas proposer le forcage, mais on peut peut etre afficher les valeurs
		if($pmb_opac_view_class){
			$tpl = "
			<div class='row'>".htmlentities($msg['opac_view_class_exists'],ENT_QUOTES,$charset)."</div>";
		}else{
			if($pmb_opac_view_activate==2){
				//définition en administration, on va chercher les valeurs...
				$qt = new quota("OPAC_VIEW",$include_path."/quotas/own/".$lang."/opac_views.xml");
				//Tableau de passage des paramètres
				if($this->id_empr){
					$struct=array();
					$struct["READER"] = $this->id_empr;
					$values = $qt->get_quota_value_with_id($struct);
				}else{
					$values = $qt->apply_conflict(array(""));
				}
				$allowed = array();
				if($values['VALUE'] && $values['VALUE'] != -1){
					$allowed = unserialize($values['VALUE']);
				}
				//on a peut etre déjà forcer
				if(count($this->view_list_empr)==0){
					$disabled = "disabled='disabled'";
				}
			}
			if (!isset($allowed['default'])) {
				$allowed['default'] = 0;
			}
			//OPAC Classique
			$line = str_replace('!!class!!',"even", $tpl_opac_view_list_sel_tableau_ligne);
			$line = str_replace('!!name!!', $msg['opac_view_classic_opac'], $line);
			$line = str_replace('!!comment!!', $msg['opac_view_classic_opac_comment'], $line);
			$line = str_replace('!!opac_view_id!!', 0, $line);
			$tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='even'\" ";
			$line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
			if($disabled == ""){
				if(in_array (0,$this->view_list_empr) || !count($this->view_list_empr)){
					$checked = "checked='checked'";
				}else $checked = "";
				$line = str_replace("!!checked!!",$checked,$line);
				if($this->view_list_empr_default == 0){
					$checked_default = "checked='checked'";
				}else{
					$checked_default = "";
				}
				$line = str_replace("!!radio_checked!!",$checked_default,$line);
				$line = str_replace("!!disabled!!",$disabled,$line);
			}else{
				if(isset($allowed['allowed']) && is_array($allowed['allowed']) && in_array(0,$allowed['allowed'])){
					$checked = "checked='checked'";
				}else $checked = "";
				$line = str_replace("!!checked!!",$checked,$line);
				if($allowed['default'] == 0){
					$checked_default = "checked='checked'";
				}else{
					$checked_default = "";
				}
				$line = str_replace("!!radio_checked!!",$checked_default,$line);
			}
			$line = str_replace("!!disabled!!",$disabled,$line);
			$liste = $line;
	
			//Pour les vues
			for($i=0;$i<count($this->opac_views_list);$i++) {
				if($this->opac_views_list[$i]->visible==0) continue;
				if($this->opac_views_list[$i]->visible>0) {
					$j++;
				}
				$line = str_replace('!!class!!',($j%2 ? "odd" : "even"), $tpl_opac_view_list_sel_tableau_ligne);
				$line = str_replace('!!name!!', $this->opac_views_list[$i]->name, $line);
				$line = str_replace('!!comment!!', $this->opac_views_list[$i]->comment, $line);
				$line = str_replace('!!opac_view_id!!', $this->opac_views_list[$i]->id, $line);
				$tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($j%2 ? "odd" : "even")."'\" ";
				$line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
				//gestion simple ou forçage pour l'utilisateur...
				if($disabled == ""){
					if(in_array($this->opac_views_list[$i]->id,$this->view_list_empr)){
						$checked = "checked='checked'";
					}else $checked = "";
					$line = str_replace("!!checked!!",$checked,$line);
					if($this->view_list_empr_default == $this->opac_views_list[$i]->id){
						$checked_default = "checked='checked'";
					}else{
						$checked_default = "";
					}
					$line = str_replace("!!radio_checked!!",$checked_default,$line);
				}else{
					if(isset($allowed['allowed']) && is_array($allowed['allowed']) && in_array($this->opac_views_list[$i]->id,$allowed['allowed'])){
						$checked = "checked='checked'";
					}else $checked = "";
					$line = str_replace("!!checked!!",$checked,$line);
					if($allowed['default'] == $this->opac_views_list[$i]->id){
						$checked_default = "checked='checked'";
					}else{
						$checked_default = "";
					}
					$line = str_replace("!!radio_checked!!",$checked_default,$line);
				}
				$line = str_replace("!!disabled!!",$disabled,$line);
				$liste.=$line;
			}
			$tpl = str_replace('!!lignes_tableau!!',$liste , $tpl_opac_view_list_sel_tableau);
	
			if($pmb_opac_view_activate == 2 && !$pmb_opac_view_class){
				$forcage = "
				<label for='force'>".$msg['opac_view_allow_force']."</label>&nbsp;
				&nbsp;".$msg['40']."<input type='radio' name='force_opac_view_choice' onclick='allow_forcage();' value='1' ".($disabled!= "" ? "" : "checked")."/>
				&nbsp;".$msg['39']."<input type='radio' name='force_opac_view_choice' onclick='disable_forcage();' value='0' ".($disabled== "" ? "" : "checked")."/>
				<script type='text/javascript'>
					function allow_forcage(){
						var checkboxes = document.forms.empr_form['form_empr_opac_view[]'];
						var radios = document.forms.empr_form['form_empr_opac_view_default'];
						for (var i=0 ; i<checkboxes.length ; i++){
							checkboxes[i].disabled= false;
						}
						for (var i=0 ; i<radios.length ; i++){
							radios[i].disabled= false;
						}
					}
	
					function disable_forcage(){
						var selected_views = new Array();";
				
				if (isset($allowed['allowed']) && is_array($allowed['allowed'])) {
					foreach($allowed['allowed'] as $view){
						$forcage.= "
							selected_views.push(".$view.")";
					}
				}
	
				$forcage.= "
						var checkboxes = document.forms.empr_form['form_empr_opac_view[]'];
						var radios = document.forms.empr_form['form_empr_opac_view_default'];
						for (var i=0 ; i<checkboxes.length ; i++){
							var selected = false;
							for(key in selected_views){
								if(checkboxes[i].value == selected_views[key]){
									selected = true;
									break;
								}
							}
							checkboxes[i].disabled= true;
							if(selected){
								checkboxes[i].checked = true;
							}else{
								checkboxes[i].checked = false;
							}
						}
						for (var i=0 ; i<radios.length ; i++){
							radios[i].disabled= true;
							if(radios[i].value == ".$allowed['default']."){
								radios[i].checked = true;
							}
						}
					}
	
				</script>";
			}else{
				$forcage = "";
			}
			$tpl = str_replace("!!forcage!!",$forcage,$tpl);
		}
		return $tpl;
	}
	
	public function update_sel_list() {
		global $form_empr_opac_view; // issu du formulaire
		global $form_empr_opac_view_default; // issu du formulaire
	
		if($this->id_empr) pmb_mysql_query("DELETE from opac_views_empr WHERE emprview_empr_num=".$this->id_empr);
	
		if(is_array($form_empr_opac_view) && $this->id_empr){
			foreach($form_empr_opac_view as $view_num){
				$found=0;
				for($i=0;$i<count($this->opac_views_list);$i++) {
					if( $this->opac_views_list[$i]->id == $view_num){$found=1;break;}
				}
				if($found || $view_num == 0){
					if($view_num==$form_empr_opac_view_default)$default=1; else $default=0;
					$req="INSERT INTO opac_views_empr SET emprview_view_num=$view_num, emprview_empr_num=".$this->id_empr.", emprview_default=$default ";
					pmb_mysql_query($req);
				}
			}
		}
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			// relation vues / empr
			pmb_mysql_query("DELETE from opac_views_empr WHERE emprview_view_num=".$id);
			// table de la liste des notices de la vue
			$req="DROP TABLE IF EXISTS opac_view_notices_".$id;
			pmb_mysql_query($req);
			// la vue
			pmb_mysql_query("DELETE from opac_views WHERE opac_view_id='".$id."' ");
		}
	}
	
	public function make_hidden_search_form() {
	    global $search;
	    global $charset;
	    
	    $url = "./catalog.php?categ=search&mode=6" ;
	    // remplir $search
	    $this->search_class->unserialize_search($this->requete);
	
	    $r="<form name='modif_requete_form' action='$url' style='display:none' method='post'>";
	    
	    if (!empty($search)) {
    	    for ($i=0; $i<count($search); $i++) {
    	    	$inter="inter_".$i."_".$search[$i];
    	    	global ${$inter};
    	    	if (!isset(${$inter})) ${$inter} = '';
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
	    }
	    // Champs à mémoriser
	    $r.="<input type='hidden' name='opac_view_id' value='".$this->id."'/>";
	    $r.="</form>";
	    return $r;
    }

    public static function apply_opac_search_parameters() {
    	global $pmb_allow_term_troncat_search, $opac_allow_term_troncat_search;
    	global $pmb_default_operator, $opac_default_operator;
    	global $pmb_multi_search_operator, $opac_multi_search_operator;
    	global $pmb_search_relevant_with_frequency, $opac_search_relevant_with_frequency;
    	
    	if(empty(static::$search_parameters)) {
    		static::$search_parameters = array(
    				'allow_term_troncat_search' => $pmb_allow_term_troncat_search,
    				'default_operator' => $pmb_default_operator,
    				'multi_search_operator' => $pmb_multi_search_operator,
    				'search_relevant_with_frequency' => $pmb_search_relevant_with_frequency
    		);
    	}
    	$pmb_allow_term_troncat_search = $opac_allow_term_troncat_search;
    	$pmb_default_operator = $opac_default_operator;
    	$pmb_multi_search_operator = $opac_multi_search_operator;
    	$pmb_search_relevant_with_frequency = $opac_search_relevant_with_frequency;
    }
    
    public static function restore_gestion_search_parameters() {
    	global $pmb_allow_term_troncat_search;
    	global $pmb_default_operator;
    	global $pmb_multi_search_operator;
    	global $pmb_search_relevant_with_frequency;
    	
    	$pmb_allow_term_troncat_search = static::$search_parameters['allow_term_troncat_search'];
    	$pmb_default_operator = static::$search_parameters['default_operator'];
    	$pmb_multi_search_operator = static::$search_parameters['multi_search_operator'];
    	$pmb_search_relevant_with_frequency = static::$search_parameters['search_relevant_with_frequency'];
    }
} // fin définition classe
