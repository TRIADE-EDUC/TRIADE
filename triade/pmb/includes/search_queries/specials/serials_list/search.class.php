<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.10 2023/08/28 14:01:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion de la recherche spécial "combine"

class serials_list{
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
						    			ajax_call.request('".$base_path."/ajax.php?module=catalog&categ=search_params&field_form=serial_'+field+'&onchange=1', true ,get_serials_elem_params(), true, got_serials_elem_params);
	    							}
    							}
    						}
						}else{
							var select = document.forms.search_form['field_0_s_2[]'];
							if(typeof(select.onchange) != 'function'){
								select.onchange = function(){
						   			var ajax_call = new http_request();
						    		ajax_call.request('".$base_path."/ajax.php?module=catalog&categ=search_params&field_form=serial_'+field+'&onchange=1', true ,get_serials_elem_params(), true, got_serials_elem_params);
	    						}
    						}
						}
					}
   				}
   			
    			if(typeof(get_serials_elem_params)=='undefined'){
					function get_serials_elem_params(){
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
				
				if(typeof(get_issues_elem_params)=='undefined'){
					function get_issues_elem_params(field){
						var values = get_serials_elem_params();
						values+= '&serial_title='+encodeURIComponent(document.getElementById(field).options[document.getElementById(field).selectedIndex].value);
						return values;
					}
				}
   			
   				if(typeof(got_serials_elem_params)=='undefined'){
					function got_serials_elem_params(response){
						var data = eval('('+response+')');
						
						if(data.onchange){
							for(var i=0 ; i<seriallist_fields.length ; i++){
								var select = document.getElementById(seriallist_fields[i]);
								var issue_select = document.getElementById(seriallist_fields[i].replace('serial','issues'));
								for (var j=0 ; j<select.options.length ; j++){
									select.removeChild(select.options[j]);
									j--;
		    					}
		    					for (var j=0 ; j<issue_select.options.length ; j++){
									issue_select.removeChild(issue_select.options[j]);
									j--;
		    					}
								for(var j=0 ; j<data.list.length ; j++){
									var option = document.createElement('option');
									option.setAttribute('value',data.list[j]);
									var text = document.createTextNode(data.list[j]);
									option.appendChild(text);
									select.appendChild(option);
								}	
    						}
						}else{
							var select = document.getElementById(data.field);
							var issue_select = document.getElementById(data.field.replace('serial','issues'));
							for (var i=0 ; i<select.options.length ; i++){
								select.removeChild(select.options[i]);
								i--;
	    					}
	    					for (var i=0 ; i<issue_select.options.length ; i++){
								issue_select.removeChild(issue_select.options[i]);
								i--;
	    					}
							for(var i=0 ; i<data.list.length ; i++){
								var option = document.createElement('option');
								option.setAttribute('value',data.list[i]);
								var text = document.createTextNode(data.list[i]);
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
								ajax_call.request('".$base_path."/ajax.php?module=catalog&categ=search_params&field_form='+data.field.replace('serial','issues'), true ,get_issues_elem_params(data.field), true, got_issues_list);
							}
						}				
					}
				}
				
	   			if(typeof(got_issues_list)=='undefined'){
					function got_issues_list(response){
						var data = eval('('+response+')');
						var select = document.getElementById(data.field);
						for (var i=0 ; i<select.options.length ; i++){
							select.removeChild(select.options[i]);
							i--;
    					}
						for(var i=0 ; i<data.list.length ; i++){
							var option = document.createElement('option');
							option.setAttribute('value',data.list[i].value);
							var text = document.createTextNode(data.list[i].text);
							option.appendChild(text);
							select.appendChild(option);
						}				
					}
				}
   				var ajax_call = new http_request();
    			ajax_call.request('".$base_path."/ajax.php?module=catalog&categ=search_params&field_form=serial_".$valeur_."', true ,get_serials_elem_params(), true, got_serials_elem_params);
    			listen_sources_change('".$valeur_."');
    		</script>
    	<select id='serial_".$valeur_."' name='".$valeur_."[]' size='5' style='width:40em;'>
		</select><br />
		<select id='issues_".$valeur_."' name='".str_replace('field','fieldvar',$valeur_)."[][]' size='5' multiple style='width:40em;'></select>";
    	return $r;
    }

    public function get_ajax_params(){
    	global $selected_sources;
    	global $field_form;
    	global $charset;
    	global $serial_title;
    	global $onchange;
    	global $msg;

    	$response = array();
    	$queries = array();
    	$response['field'] = $field_form;
    	$response['onchange'] = ($onchange ? true : false);

    	$elem = explode("_",$field_form);
    	switch($elem[0]){
    		case "issues" :
    			if($serial_title){
	    			foreach($selected_sources as $source){
				    	$queries[] = "select distinct concat(num_issue.value,if(num_issue.value = '','',if(date_issue.value='','',' - ')),date_format(date_issue.value,'".$msg['format_date']."')) as val,date_issue.value as date, num_issue.value as num from entrepot_source_".$source." as serial join entrepot_source_".$source." as num_issue on serial.recid = num_issue.recid join entrepot_source_".$source." as date_issue on serial.recid = date_issue.recid where serial.ufield='461' and serial.usubfield='t' and serial.value='".$serial_title."' and num_issue.ufield = '463' and num_issue.usubfield='v' and date_issue.ufield = '463' and date_issue.usubfield='d' ";
		    		}
    			}
    	 		if(count($queries)>1){
		    		$query = "select * from (".implode(" union ",$queries).") as uni order by date,num,val";
		    	}else if(count($queries)==1){
		    		$query = $queries[0]. "order by date,num,val";
		    	} else {
		    		$query = "";
		    	}
   				if($query){
		    		$result = pmb_mysql_query($query);
					$list=array();
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							if($charset!="utf-8"){
								$list[] = array(
									'value' => encoding_normalize::utf8_normalize($row->num."|||".$row->date),
									'text' =>  encoding_normalize::utf8_normalize($row->val)
								);
							}else $list[] = array(
								'value' => $row->num."|||".$row->date,
								'text' =>  $row->val
							);
						}
					}
					$response['list'] = $list;
		    	}else{
		    		$response['list'] = array();
		       	}
    			break;
    		default :
		    	if($selected_sources){
	    			foreach($selected_sources as $source){
			    		$queries[] = "select distinct entrepot.value as val from entrepot_source_".$source." as entrepot join entrepot_source_".$source." on entrepot.recid = entrepot_source_".$source.".recid where entrepot_source_".$source.".ufield = 'bl' and entrepot_source_".$source.".value!='m' and entrepot.ufield='461' and entrepot.usubfield='t'";
				   	}
		    	}
		    	if(count($queries)>1){
		    		$query = "select * from (".implode(" union ",$queries).") as uni order by val";
		    	}else if(count($queries)==1){
		    		$query = $queries[0]. "order by val";
		    	} else {
		    		$query = "";
		    	}
		    	if($query){
		    		$result = pmb_mysql_query($query);
					$list=array();
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							if($charset!="utf-8"){
								$list[] = encoding_normalize::utf8_normalize($row->val);
							}else $list[] = $row->val;
						}
					}
					$response['list'] = $list;
		    	}else{
		    		$response['list'] = array();
		       	}
    			break;
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
    	    	    	
    	//Récupération de la valeur de saisie
    	$serial_="field_".$this->n_ligne."_s_".$this->id;
    	$issues_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$serial_};
    	global ${$issues_};
    	$serial=${$serial_};
    	$issues = ${$issues_};
    	//$issues=$issues[0];
    	
    	global $field_0_s_2;
    	if (count($field_0_s_2) && !$source){
    		$selected_sources =$field_0_s_2;
    	}else $selected_sources = $source;
    	if(!$this->is_empty($serial)){
    		$issues_infos = array();
	    	foreach($issues as $issue){
	    		$issues_infos[] = explode('|||',$issue[0]);
	    	}
	    	if($selected_sources){
		    	foreach($selected_sources as $s){
		       		$query= "select distinct analysis.recid as notice_id from entrepot_source_".$s." as serial join entrepot_source_".$s." as num_issue on serial.recid = num_issue.recid join entrepot_source_".$s." as date_issue on serial.recid = date_issue.recid join entrepot_source_".$s." as analysis on serial.recid = analysis.recid
					 where 
					 analysis.ufield ='bl' and analysis.value!='m' and
					 serial.ufield='461' and serial.usubfield='t' and serial.value='".addslashes($serial[0])."' ";
		       		$restricted_issues = array();
					foreach($issues_infos as $issue){
						$restricted_issues[] = "(num_issue.value='".$issue[0]."' and date_issue.value='".$issue[1]."')";
					}
					$query.=" and (".implode(" or ",$restricted_issues).")";
					$queries[] = $query;
				}  
				$t_table= "table_".$this->n_ligne."_s_".$this->id;
				$query = "create temporary table ".$t_table." select * from(".implode(" union ",$queries).") as uni";
				pmb_mysql_query($query);
				return $t_table;
	    	}
    	}
    }
    
    public function make_unimarc_query() {
    	global $search;
    	global $source;
    	    	    	
    	//Récupération de la valeur de saisie
    	$serial_="field_".$this->n_ligne."_s_".$this->id;
    	$issues_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$serial_};
    	global ${$issues_};
    	$serial=${$serial_};
    	$issues = ${$issues_};
    	//$issues=$issues[0];
    	
    	global $field_0_s_2;
    	if (count($field_0_s_2) && !$source){
    		$selected_sources =$field_0_s_2;
    	}else $selected_sources = $source;
    	
    	if(!$this->is_empty($serial)){
	    	$issues_infos = array();
	    	foreach($issues as $issue){
	    		$issues_infos[] = explode('|||',$issue[0]);
	    	}
	    	if($selected_sources){
		    	foreach($selected_sources as $s){
		       		$query= "select distinct analysis.recid as notice_id from entrepot_source_".$s." as serial join entrepot_source_".$s." as num_issue on serial.recid = num_issue.recid join entrepot_source_".$s." as date_issue on serial.recid = date_issue.recid join entrepot_source_".$s." as analysis on serial.recid = analysis.recid
					 where 
					 analysis.ufield ='bl' and analysis.value!='m' and
					 serial.ufield='461' and serial.usubfield='t' and serial.value='".addslashes($serial[0])."' ";
		       		$restricted_issues = array();
					foreach($issues_infos as $issue){
						$restricted_issues[] = "(num_issue.value='".$issue[0]."' and date_issue.value='".$issue[1]."')";
					}
					$query.=" and (".implode(" or ",$restricted_issues).")";
					$queries[] = $query;
				}  
				$t_table= "table_".$this->n_ligne."_s_".$this->id;
				$query = "create temporary table ".$t_table." select * from(".implode(" union ",$queries).") as uni";
				pmb_mysql_query($query);
				return $t_table;
	    	}
    	}
    }
    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {
		global $search;
    	global $source;
    	    	    	
    	//Récupération de la valeur de saisie
    	$serial_="field_".$this->n_ligne."_s_".$this->id;
    	$issues_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$serial_};
    	global ${$issues_};
    	$serial=${$serial_};
    	$issues = ${$issues_};
		//$issues=$issues[0];
		
    	if(!$this->is_empty($serial)){	
	  		$issues_infos = array();
	    	foreach($issues as $issue){
	    		$elem  = explode('|||',$issue[0]);
	    		$issues_infos[] = $elem[0].($elem[0] && $elem[1] ? " - ":"").format_date($elem[1]);
	    	} 
	    	
	    	$label = $serial[0]." (".implode(" / ",$issues_infos).")";
			$litteral=array($label);
	    	return $litteral;
	    } 
    }
    
    //fonction de vérification du champ saisi ou sélectionné
    public function is_empty($valeur) {
    	if($valeur[0]!= ""){
    		$issues_="fieldvar_".$this->n_ligne."_s_".$this->id;
    		global ${$issues_};
    		if(count(${$issues_})>0){
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