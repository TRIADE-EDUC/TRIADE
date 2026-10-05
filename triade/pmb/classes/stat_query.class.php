<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: stat_query.class.php,v 1.13.6.1.2.1 2025/03/18 13:48:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/interface/admin/interface_admin_opac_form.class.php');
require_once ($class_path . "/parameters.class.php");
require_once ($class_path."/procs.class.php");
require_once("$include_path/templates/stat_opac.tpl.php");

class stat_query {
	
	public $id_query;
	public $action;
	public $id_vue_liee;
	
	public function __construct($id,$action,$idvue=0){
		$this->id_query=intval($id);
		$this->action=$action;
		$this->id_vue_liee = intval($idvue);
	}
	
	public function proceed(){
		switch($this->action){
			case 'configure':
				$hp=new parameters($this->id_query,"statopac_request");
				$hp->show_config_screen(static::format_url("&section=view_list&act=update_config&id_req=$this->id_query"),static::format_url("&section=view_list"));
				break;
			case 'update_config':
				$hp=new parameters($this->id_query,"statopac_request");
				$hp->update_config(static::format_url("&section=view_list"));
				break;
			case 'update_request':
				//Ajout/Modification d'une requete
				if(!$this->id_vue_liee){
					$this->id_vue_liee = static::get_vue_associee($this->id_query);
				}
				print $this->do_form_request($this->id_query,$this->id_vue_liee);
				break;
			case 'save_request':
				if(!$this->id_vue_liee){
					$this->id_vue_liee = static::get_vue_associee($this->id_query);
				}
				$this->save_request($this->id_query,$this->id_vue_liee);
				break;
			case 'suppr_request':
				//Suppression d'une vue
				$this->delete_request($this->id_query);
				break;
			case 'final':
				$this->final();
				break;
			case 'exec_req':
				// form pour params et validation
				$this->run_form($this->id_query);
				break;					
			default:
				break;
		}
	}
		
	//Supprime une requete
	public function delete_request($id_req){
		$id_req = intval($id_req);
		if($id_req){
			$req="DELETE FROM statopac_request where idproc='".$id_req."'";
			pmb_mysql_query($req);
		}
	}
	
	public function final(){
		global $PMBuserid,$msg,$charset;
		global $force_exec, $pmb_procs_force_execution;
		
		$hp=new parameters($this->id_query,"statopac_request");
		$query_parameters=array();
		if (preg_match_all("|!!(.*)!!|U",$hp->proc->requete,$query_parameters)) {
			$hp->get_final_query();
			$code=$hp->final_query;
			$this->id_query=intval($this->id_query);
		} else {
			$code='';
		}
		// include d'exécution d'une procédure
		$requete = "SELECT * FROM statopac_request WHERE idproc=".$this->id_query;
		$res = pmb_mysql_query($requete);
		
		$nbr_lignes = pmb_mysql_num_rows($res);
		if($nbr_lignes) {
			// récupération du résultat
			$row = pmb_mysql_fetch_row($res);
			$idp = $row[0];
			$name = $row[1];
			if (!isset($code) || !$code)
				$code = $row[2];
				$commentaire = $row[3];
				
				//on remplace VUE par el nom de la table dynamique associée
				$num_vue = static::get_vue_associee($this->id_query);
				$code = str_replace('VUE()','statopac_vue_'.$num_vue,$code);
				print "<br>
					<h3>".htmlentities($msg["procs_execute"]." ".$name, ENT_QUOTES, $charset)."</h3>
					<br/>".htmlentities($commentaire, ENT_QUOTES, $charset)."<hr/>
					<input type='button' class='bouton' value='$msg[62]'  onClick='document.location=\"".static::format_url("&section=query&act=update_request&id_req=".$this->id_query)."\"' />";
				if (($pmb_procs_force_execution && $force_exec) || (($PMBuserid == 1) && $force_exec)) {
					print "<input type='button' id='procs_button_exec' class='bouton' value='".htmlentities($msg["procs_force_exec"], ENT_QUOTES, $charset)."' onClick='document.location=\"".static::format_url("&section=view_list&act=exec_req&id_req=".$this->id_query."&force_exec=1")."\"' />";
				} else {
					print "<input type='button' id='procs_button_exec' class='bouton' value='$msg[708]' onClick='document.location=\"".static::format_url("&section=view_list&act=exec_req&id_req=".$this->id_query)."\"' />";
				}
				print "<br />";
				list_query_statopac_admin_ui::set_id_proc($this->id_query);
				procs::$table = 'statopac_request';
				$report = procs::run_query($code);
				if($report['state'] == false && $report['message'] == 'explain_failed') {
					if ($pmb_procs_force_execution || ($PMBuserid == 1)) {
						print "
						<script type='text/javascript'>
							if (document.getElementById('procs_button_exec')) {
								var button_procs_exec = document.getElementById('procs_button_exec');
								button_procs_exec.setAttribute('value','".addslashes($msg["procs_force_exec"])."');
								button_procs_exec.setAttribute('onClick','document.location=\"".static::format_url("&section=view_list&act=exec_req&id_req=".$this->id_query."&force_exec=1")."\"');
							}
						</script>
						";
					}
				}
		} else {
			print $msg["proc_param_query_failed"];
		}
	}
	
	public function get_cols_content_form_request($vue_id=0, $code='') {
	    global $msg;
	    global $stat_view_request_cols_content_form;
	    
	    $content_form = $stat_view_request_cols_content_form;
	    $content_form = str_replace('!!code!!', $code, $content_form);
	    $rqt_colnom="select nom_col from statopac_vues_col where num_vue='".$vue_id."'";
	    $res=pmb_mysql_query($rqt_colnom);
	    if(pmb_mysql_num_rows($res) == 0){
	        $content_form = str_replace('!!liste_cols!!',$msg['stat_no_col_associate'],$content_form);
	    } else {
	        $liste = "<select style='width:100%; height:140px' multiple='yes' ondblclick='right_to_left()' id='associate_col' name='nom_col[]'>";
	        $i=0;
	        while(($col_nom = pmb_mysql_fetch_object($res))){
	            $liste.= "<option value=$i>$col_nom->nom_col</option>";
	            $i++;
	        }
	        $liste.="</select>";
	        $content_form = str_replace('!!liste_cols!!',$liste,$content_form);
	    }
	    return $content_form;
	}
	
	/**
	 * Contenu du formulaire d'ajout d'une requete
	 */
	public function get_content_form_request($request_id=0,$vue_id=0) {
	    $request_id = intval($request_id);
	    $vue_id = intval($vue_id);
	    $name = '';
	    $code = '';
	    $comment = '';
	    $autorisations_all = 1;
	    $autorisations = '';
	    if ($request_id) {
    	    $query = "select name , requete , comment, autorisations, autorisations_all from statopac_request where idproc='".$request_id."'";
    	    $result = pmb_mysql_query($query);
    	    while(($row = pmb_mysql_fetch_object($result))){
    	        $name = $row->name;
    	        $code = $row->requete;
    	        $comment = $row->comment;
    	        $autorisations_all = $row->autorisations_all;
    	        $autorisations = $row->autorisations;
    	    }
	    }
	    $interface_content_form = new interface_content_form(static::class);
	    //Nom
	    $interface_content_form->add_element('f_request_name', '705')
	    ->add_input_node('text', $name)
	    ->set_maxlength(255);
	    //Colonnes
	    $interface_content_form->add_element('f_request_code')
	    ->add_html_node($this->get_cols_content_form_request($vue_id, $code));
	    //Commentaire
	    $interface_content_form->add_element('f_request_comment', '707')
	    ->add_input_node('text', $comment)
	    ->set_maxlength(255);
	    //Visible pour tous ?
	    $interface_content_form->add_element('autorisations_all', 'procs_autorisations_all', 'flat')
	    ->add_input_node('boolean', $autorisations_all);
	    //Autorisations
	    $interface_content_form->add_inherited_element('permissions_users', 'autorisations', 'etagere_autorisations')
	    ->set_autorisations($autorisations)
	    ->set_on_create(($request_id ? 0 : 1));
	    return $interface_content_form->get_display();
	}
	
	//Affiche le formulaire de saisie d'une requete
	public function do_form_request($request_id=0,$vue_id=0){
		global $msg;
		
		$request_id = intval($request_id);
		$vue_id = intval($vue_id);
		$content_form = $this->get_content_form_request($request_id,$vue_id);
		
		$interface_form = new interface_admin_opac_form('request_form');
		if(!$request_id){
			$interface_form->set_label($msg['stat_create_query']);
		}else{
			$interface_form->set_label($msg['stat_alter_query']);
		}
		$interface_form->set_object_id($request_id)
		->set_id_view($vue_id)
		->set_confirm_delete_msg($msg['confirm_suppr'])
		->set_content_form($content_form)
		->set_table_name('statopac_request')
		->set_field_focus('f_request_name');
		$interface_form->add_action_extension('execute_button', $msg['708'], static::format_url("&section=view_list&act=exec_req&id_view=".$vue_id."&id_req=".$request_id));
		return $interface_form->get_display();
	}

	//Insere ou enregistre une requete
	public function save_request($request_id='', $vue_id=''){
		global $f_request_name, $f_request_code, $f_request_comment, $msg;
		global $autorisations, $autorisations_all;
		
		$chaine = strpos($f_request_code,'VUE()');
		if($chaine !==false){
			if (is_array($autorisations)) {
				$autorisations=implode(" ",$autorisations);
			} else {
				$autorisations='';
			}
			$autorisations_all = intval($autorisations_all);
			if((!$request_id) && $vue_id){
					$req = "INSERT INTO statopac_request(name,requete,comment,num_vue,autorisations,autorisations_all) VALUES ('".$f_request_name."', '".$f_request_code."','".$f_request_comment."','".$vue_id."', '$autorisations', '".$autorisations_all."')";
					pmb_mysql_query($req);
			} else {
					$req = "UPDATE statopac_request SET name='".$f_request_name."', requete='".$f_request_code."', num_vue='".$vue_id."', comment='".$f_request_comment."', autorisations='".$autorisations."', autorisations_all='".$autorisations_all."' WHERE idproc='".$request_id."'";
					pmb_mysql_query($req);
			}
		} else{
			error_form_message($msg["stat_wrong_query_format"]);
		}
	}
	
	//Formulaire d'execution
	public function run_form($id) {
		global $force_exec;

		$hp=new parameters($id,"statopac_request");
		$query_parameters=array();
		if (preg_match_all("|!!(.*)!!|U",$hp->proc->requete,$query_parameters))
			$hp->gen_form(static::format_url("&section=view_list&act=final&id=$id".($force_exec ? "&force_exec=$force_exec" : "")));
			else echo "<script>document.location='".static::format_url("&section=view_list&act=final&id=".$id.($force_exec ? "&force_exec=$force_exec" : ""))."'</script>";
	}
	
	public static function get_vue_associee($id_req){
		$id_req = intval($id_req);
		$rqt="select num_vue from statopac_request where idproc='".addslashes($id_req)."'";
		$res = pmb_mysql_query($rqt);
		
		return pmb_mysql_result($res,0,0);
	}
	
	protected static function format_url($url='') {
		global $base_path;
		
		return $base_path.'/admin.php?categ=opac&sub=stat'.$url;
	}
}