<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.10.4.1 2025/03/05 15:06:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path.'/connecteurs.class.php');
//Classe de gestion de la recherche spécial "combine"


class remote_serials_list{
	public $id;
	public $n_ligne;
	public $params;
	public $search;

	//Constructeur
    public function __construct($id,$n_ligne,$params,&$search) {
    	$this->id=$id;
    	$this->n_ligne=$n_ligne;
    	$this->params=$params;
    	$this->search=&$search;
    }
    
    //fonction de récupération des opérateurs disponibles pour ce champ spécial (renvoie un tableau d'opérateurs)
    public function get_op() {
    	$operators = array();
    	$operators["EQ"]="=";
    	return $operators;
    }
    
    //fonction de récupération de l'affichage de la saisie du critère
    public function get_input_box() {
    	global $base_path;

    	//$this->s = new search(false,"search_simple_fields.xml");
    	
    	//Récupération de la valeur de saisie
    	$valeur_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$valeur_};
    	$valeur=${$valeur_};

    	$r ="
   			<script type='text/javascript'>
   				if(typeof(seriallist_fields)=='undefined'){
   					var seriallist_fields = new Array();
   				}
   				seriallist_fields.push('serial_".$valeur_."');
   				
   				if(typeof(listen_sources_change)=='undefined'){
   					function listen_sources_change(field){
   						var inputs = document.getElementsByName('source[]');
						if(inputs && (inputs.length > 0)){
							for(var i=0 ; i<inputs.length ; i++){
								if(typeof(inputs[i].onchange) != 'function'){
									inputs[i].onchange = function(){
						   				var ajax_call = new http_request();
						    			ajax_call.request('".$base_path."/ajax.php?module=catalog&categ=search_params&field_form=serial_'+field+'&onchange=1', true ,get_remote_serials_elem_params(), true, got_remote_serials_elem_params);
	    							}
    							}
    						}
						}else{
							var select = document.forms.search_form['field_0_s_2[]'];
							if(typeof(select.onchange) != 'function'){
								select.onchange = function(){
						   			var ajax_call = new http_request();
						    		ajax_call.request('".$base_path."/ajax.php?module=catalog&categ=search_params&field_form=serial_'+field+'&onchange=1', true ,get_remote_serials_elem_params(), true, got_remote_serials_elem_params);
	    						}
    						}
						}
					}
   				}
   			
    			if(typeof(get_remote_serials_elem_params)=='undefined'){
					function get_remote_serials_elem_params(){
						var values = '';
						var inputs = document.getElementsByName('source[]');
						if(inputs && (inputs.length > 0)){
							//recherche simple
							for(var i=0 ; i<inputs.length ; i++){
								if(inputs[i].checked){
									values+= '&selected_sources[]='+inputs[i].value;
								}
							}
						}else{
							//multi
							var select = document.forms.search_form['field_0_s_2[]'];
							for (var i=0 ; i<select.options.length ; i++){
								if(select.options[i].selected == true){
									values+= '&selected_sources[]='+select.options[i].value;
    							}
    						}
   						}
						return values;
					}
				}
				
				if(typeof(get_remote_issues_elem_params)=='undefined'){
					function get_remote_issues_elem_params(field){
						var values = get_remote_serials_elem_params();
						values+= '&serial='+encodeURIComponent(document.getElementById(field).options[document.getElementById(field).selectedIndex].value);
						return values;
					}
				}
   			
   				if(typeof(got_remote_serials_elem_params)=='undefined'){
					function got_remote_serials_elem_params(response){
						var data = eval('('+response+')');
						if(data.onchange){
							for(var i=0 ; i<seriallist_fields.length ; i++){
								var select = document.getElementById(seriallist_fields[i]);
								var issue_select = document.getElementById(seriallist_fields[i].replace('serial','issues'));
								while(select.hasChildNodes()) {
									select.removeChild(select.lastChild);
								}
								while(issue_select.hasChildNodes()) {
									issue_select.removeChild(issue_select.lastChild);
								}
								for(var j=0 ; j<data.list.length ; j++){
									var option = document.createElement('option');
									option.setAttribute('value',data.list[j].id);
									var text = document.createTextNode(data.list[j].title);
									option.appendChild(text);
									select.appendChild(option);
								}	
    						}
						}else{
							var select = document.getElementById(data.field);
							var issue_select = document.getElementById(data.field.replace('serial','issues'));
							while(select.hasChildNodes()) {
								select.removeChild(select.lastChild);
							}
							while(issue_select.hasChildNodes()) {
								issue_select.removeChild(issue_select.lastChild);
							}
							for(var i=0 ; i<data.list.length ; i++){
								var option = document.createElement('option');
								option.setAttribute('value',data.list[i].id);
								var text = document.createTextNode(data.list[i].title);
								option.appendChild(text);
								select.appendChild(option);
							}	
							select.onchange = function(){
								for(var i=0 ; i<seriallist_fields.length ; i++){
									var issue_select = document.getElementById(seriallist_fields[i].replace('serial','issues'));
									while(issue_select.hasChildNodes()) {
										issue_select.removeChild(issue_select.lastChild);
									}
	    						}
								var ajax_call = new http_request();
								ajax_call.request('".$base_path."/ajax.php?module=catalog&categ=search_params&field_form='+data.field.replace('serial','issues'), true ,get_remote_issues_elem_params(data.field), true, got_remote_issues_list);
							}
						}				
					}
				}
				
	   			if(typeof(got_remote_issues_list)=='undefined'){
					function got_remote_issues_list(response){				
						var data = eval('('+response+')');
						var select = document.getElementById(data.field);
						while(select.hasChildNodes()) {
							select.removeChild(select.lastChild);
						}
						for(var i=0 ; i<data.list.length ; i++){
							var option = document.createElement('option');
							option.setAttribute('value',data.list[i].id);
							var text = document.createTextNode(data.list[i].title);
							option.appendChild(text);
							select.appendChild(option);
						}				
					}
				}
   				var ajax_call = new http_request();
    			ajax_call.request('".$base_path."/ajax.php?module=catalog&categ=search_params&field_form=serial_".$valeur_."', true ,get_remote_serials_elem_params(), true, got_remote_serials_elem_params);
    			listen_sources_change('".$valeur_."');
    		</script>
    	<select id='serial_".$valeur_."' name='".$valeur_."[]' size='5' style='width:40em;'>
		</select>
		<br />
		<select id='issues_".$valeur_."' name='".str_replace('field','fieldvar',$valeur_)."[][]' size='5' multiple style='width:40em;'></select>";
    	return $r;
    }

    public function get_ajax_params(){
    	global $selected_sources;
    	global $field_form;
    	global $serial;
    	global $onchange;
		global $base_path;
		
    	$response = array();
    	$queries = array();
    	$response['field'] = $field_form;
    	$response['onchange'] = ($onchange ? true : false);
 		$response_list = array();
 		
		$elem = explode("_",$field_form);
		switch ($elem[0]) {
			case 'issues' :
				if ($serial) {
					$t = explode('_',$serial);
					$source_id = $t[0];
					$serial_id = $t[1];
					
					$contrs=new connecteurs();
						
					$connector_id=0;
					foreach ($contrs->catalog as $k=> $contr) {
						if ($contr['NAME']=='pmb') {
							$connector_id=$k;
							break;
						}
					}
					$connector_name = connecteurs::get_class_name($source_id);
					$conn=false;
					if ($connector_name=='pmb') {
						require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$connector_id]["PATH"]."/".$contrs->catalog[$connector_id]["NAME"].".class.php");
						eval("\$conn=new ".$contrs->catalog[$connector_id]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$connector_id]["PATH"]."\");");
					}
					if ($conn) {
						$issue_list = $conn->fetch_notice_list_full($source_id,array(0=>$serial_id));
						if (count($issue_list[0]['noticeBulletins'])) {
							foreach ($issue_list[0]['noticeBulletins'] as $k=>$v) {
								$response_list[]=array('id'=>$source_id.'_'.$serial_id.'_'.$v['bulletin_id'],'title'=>$v['bulletin_numero'].' - '.$v['bulletin_date_caption']);
							}
						}
					}
				}
		 		break;	
		 		
			default :
		    	if($selected_sources){
					$contrs=new connecteurs();
					
					$connector_id=0;
					foreach ($contrs->catalog as $k=> $contr) {
						if ($contr['NAME']=='pmb') {
							$connector_id=$k;
							break;
						}
					}
		    		foreach($selected_sources as $source_id){
						$connector_name = connecteurs::get_class_name($source_id);
						$conn=false;
						if ($connector_name=='pmb') {
							require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$connector_id]["PATH"]."/".$contrs->catalog[$connector_id]["NAME"].".class.php");
							eval("\$conn=new ".$contrs->catalog[$connector_id]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$connector_id]["PATH"]."\");");
						}
						if ($conn) {
							$serial_list = $conn->fetch_serial_list($source_id);
							if (count($serial_list)) {
								foreach ($serial_list as $k=>$v) {
									$response_list[]=array('id'=>$source_id.'_'.$v['serial_id'],'title'=>$v['serial_title']);
								}
							}
						}
					}	  	
		    	}    	
		       	break;
		}		
		if (count($response_list)) {
			$response['list'] = $response_list;
    	}else{
    		$response['list'] = array();
       	}
		ajax_http_send_response($response,'application/json');
    }
    
    //fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
    public function transform_input() {
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
    	global $search;
    	global $source;
    	global $base_path;
    	//Récupération de la valeur de saisie
    	$serial_="field_".$this->n_ligne."_s_".$this->id;
    	$issues_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$serial_};
    	global ${$issues_};
    	$serial=${$serial_};
    	$issues = ${$issues_};
		
    	if(!$this->is_empty($serial)){
    		$source_id=0;
    		$issue_id=array();
    		foreach ( $issues as $value ) {//Tous les bulletins viennent forcément du périodique de la même source
       			$t = explode('_',$value[0]);
       			$source_id = $t[0];
       			$issue_id[]=$t[2];
			}
	    	
	    	$contrs=new connecteurs();
							
			$connector_id=0;
			foreach ($contrs->catalog as $k=> $contr) {
				if ($contr['NAME']=='pmb') {
					$connector_id=$k;
					break;
				}
			}
			$connector_name = connecteurs::get_class_name($source_id);
			$conn=false;
			if ($connector_name=='pmb') {
				require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$connector_id]["PATH"]."/".$contrs->catalog[$connector_id]["NAME"].".class.php");
				eval("\$conn=new ".$contrs->catalog[$connector_id]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$connector_id]["PATH"]."\");");
			}
			$analysis_list_id=array();
			if ($conn) {
				$bull_list = $conn->fetch_bulletin_list_full($source_id,$issue_id);
				if(count($bull_list)){
					foreach ( $bull_list as $value ) {
       					if (count($value['bulletin_analysis_notice_ids'])) {
       						$analysis_list_id[]=implode(',',$value['bulletin_analysis_notice_ids']);
							$conn->fetch_notice_list_full($source_id,$value['bulletin_analysis_notice_ids'],'pmb_xml_unimarc','utf-8',true,true);
						}
					}
				}
			}
			if (count($analysis_list_id)) {
				$query= "select distinct analysis.recid as notice_id from entrepot_source_".$source_id." as analysis 
						 where 
						 analysis.ref in(".implode(',',$analysis_list_id).")";
				$queries[]=$query;
				$t_table= "table_".$this->n_ligne."_s_".$this->id;
				$query = "create temporary table ".$t_table." select * from(".implode(" union ",$queries).") as uni";
				pmb_mysql_query($query);
				return $t_table;
			}else{
				//Aucun article
				$t_table= "table_".$this->n_ligne."_s_".$this->id;
				$query = "create temporary table ".$t_table." (notice_id integer unsigned not null)";
				pmb_mysql_query($query);
				return $t_table;
			}
    	}
    }

    
    public function make_unimarc_query() {
		return array();
    }
    	    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {
		global $search;
    	global $source;
    	global $base_path,$charset;    	    	
    	//Récupération de la valeur de saisie
    	$serial_="field_".$this->n_ligne."_s_".$this->id;
    	$issues_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$serial_};
    	global ${$issues_};
    	$serial=${$serial_};
    	$issues = ${$issues_};

    	if(!$this->is_empty($serial)){
    		$source_id=0;
    		$issue_id=array();
    		foreach ( $issues as $value ) {
       			$t = explode('_',$value[0]);
       			$source_id = $t[0];
       			$issue_id[]=$t[2];
			}
    		
    		$contrs=new connecteurs();
						
			$connector_id=0;
			foreach ($contrs->catalog as $k=> $contr) {
				if ($contr['NAME']=='pmb') {
					$connector_id=$k;
					break;
				}
			}
			$connector_name = connecteurs::get_class_name($source_id);
			$conn=false;
			if ($connector_name=='pmb') {
				require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$connector_id]["PATH"]."/".$contrs->catalog[$connector_id]["NAME"].".class.php");
				eval("\$conn=new ".$contrs->catalog[$connector_id]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$connector_id]["PATH"]."\");");
			}
			$issues_infos = "";
			if ($conn) {
				$issue_list = $conn->fetch_bulletin_list_full($source_id,$issue_id);
				if (count($issue_list)) {
					foreach ( $issue_list as $value ) {
       					$elem=array();
						$elem[0] = $value['bulletin_bulletin']['bulletin_numero'];
						$elem[1] = $value['bulletin_bulletin']['bulletin_date'];
						$elem[2] = $value['bulletin_bulletin']['serial_title'];
						if ($charset!='utf-8') {
							foreach($elem as $k=>$v) {
								$elem[$k]=encoding_normalize::utf8_decode($v);
							}
						}
						if($issues_infos == ""){
							
							$issues_infos = $elem[2]." (".$elem[0].($elem[0] && $elem[1] ? " - ":"").format_date($elem[1]).")";
						}else{
							$issues_infos .=" / (".$elem[0].($elem[0] && $elem[1] ? " - ":"").format_date($elem[1]).")";
						}
					}
				}
			}
			$litteral=array($issues_infos);
	    	return $litteral;   
	    } 
    }
    
    //fonction de vérification du champ saisi ou sélectionné
    public function is_empty($valeur) {
    	if($valeur[0]!= ""){
    		$issues_="fieldvar_".$this->n_ligne."_s_".$this->id;
    		global ${$issues_};
    		$issues = ${$issues_};
    		if(is_countable($issues[0]) && count($issues[0])>0){
    			return false;
    		}
    	}
    	return true;
    }
    
     //fonction de découpage d'une chaine trop longue
    public function cutlongwords($valeur,$size=50) {
    	if (strlen($valeur)>=$size) {
    		$pos=strrpos(substr($valeur,0,$size)," ");
    		if ($pos) {
    			$valeur=substr($valeur,0,$pos+1)."...";
    		} 
    	}
    	return $valeur;		
    }
}
?>