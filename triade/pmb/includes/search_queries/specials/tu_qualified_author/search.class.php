<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.10 2018/08/01 12:56:11 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion de la recherche spécial "tu_qualified_author"
global $class_path;
require_once($class_path."/search.class.php");

class tu_qualified_author {
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
    	global $msg;
    	$operators = array(
    		'AUTHORITY' => $msg['authority_query']	
    	);
    	return $operators;
    }
    
    protected function get_variable($type, $inc) {
    	global $msg;
    	
    	switch ($type) {
    		case 'function' :
    			$label = $msg['245'];
    			$ajax = 'fonction';
    			$selector = 'function';
    			$p1 = 'p1';
    			$p2 = 'p2';
    			$linkfield = '';
    			break;
    		case 'qualification' :
    			$label = $msg['notice_vedette_composee_author'];
    			$ajax = 'vedette';
    			$selector = 'vedette';
    			$p1 = 'p1';
    			$p2 = 'p2';
    			$linkfield = "fieldvar_".$this->n_ligne."_s_".$this->id."[".$inc."][".$type."][grammars]";
    			break;
			case 'author' :
			default :
				$label = $msg['tu_authors_list'];
				$ajax = 'authors';
				$selector = 'auteur';
				$p1 = 'param1';
				$p2 = 'param2';
				$linkfield = '';
				break;
    	}
    	
    	return array(
    		"label" => $label,
    		"fnamesans" => "field_".$this->n_ligne."_s_".$this->id."_".$type,
			"fname" => "field_".$this->n_ligne."_s_".$this->id."[".$inc."][".$type."]",
			"fname_id" => "field_".$this->n_ligne."_s_".$this->id."_".$type."_id",
			"fnamesanslib" => "field_".$this->n_ligne."_s_".$this->id."_".$type."_lib",
			"fnamelib" => "field_".$this->n_ligne."_s_".$this->id."_lib[".$inc."][".$type."]",
			"fname_name_aut_id" => "fieldvar_".$this->n_ligne."_s_".$this->id."[".$inc."][".$type."][authority_id]",
			"fname_aut_id" => "fieldvar_".$this->n_ligne."_s_".$this->id."_".$type."_authority_id",
    		"fnamevar_id" => "",
    		"fnamevar_id_js" => "",
    		"ajax" => $ajax,
    		"selector" => $selector,
    		"p1" => $p1,
    		"p2" => $p2,
    		"linkfield" => $linkfield
		);
    }
    
    //fonction de récupération de l'affichage de la saisie du critère
    public function get_input_box() {
    	global $msg;
    	global $charset;
    	global $fonction_auteur;
    	global $pmb_authors_qualification;
    	
    	// récupération des codes de fonction
    	if (!count($fonction_auteur)) {
    		$fonction_auteur = new marc_list('function');
    		$fonction_auteur = $fonction_auteur->table;
    	}
    	
    	$display = '';
    	
     	//Récupération des valeurs saisies
    	$values_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$values_};
    	$values=${$values_};
    	
    	//Recuperation des variables auxiliaires
    	$fieldvar_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$fieldvar_};
    	$fieldvar=${$fieldvar_};
    	
    	$nb_values=count($values);
    	if(!$nb_values){
    		//Création de la ligne
    		$nb_values=1;
    	}
    	$nb_max_aut=$nb_values-1;
    	
    	$display = "<input type='hidden' id='field_".$this->n_ligne."_s_".$this->id."_max_aut' value='".$nb_max_aut."'>";
    	$display .= "<input class='bouton' type='button' value='+' onclick='qualifiedAuthorAddLine(".$this->id.", ".$this->n_ligne.")'>";
    	$display .= "<div id='elfield_".$this->n_ligne."_s_".$this->id."'>";
    	
    	for($inc=0;$inc<$nb_values;$inc++){
    		$fields = array();
    		$fields['author'] = $this->get_variable('author', $inc);
    		$fields['function'] = $this->get_variable('function', $inc);
    		if($pmb_authors_qualification) {
    			$fields['qualification'] = $this->get_variable('qualification', $inc);
    		}
    		$display .="<div class='row'>";
    		foreach ($fields as $authority_type=>$field) {
    			$display .="<div class='colonne3'>";
    			if($inc == 0) $display .=$field['label']."<br />";
    			$display .="<input id='".$field['fnamesans']."_".$inc."' name='".$field['fname']."' value='".htmlentities($values[$inc][$authority_type],ENT_QUOTES,$charset)."' type='hidden' />";
    			 
//     			if (${$op} == "AUTHORITY"){
    				$libelle = "";
    				if($values[$inc][$authority_type]!= 0){
    					switch ($authority_type) {
    						case 'author' :
    							$libelle = search::get_authoritie_display($values[$inc][$authority_type], 'auteur');
    							break;
    						case 'function' :
    							$libelle = $fonction_auteur[$values[$inc][$authority_type]];
    							break;
							case 'qualification' :
								$libelle = search::get_authoritie_display($values[$inc][$authority_type], 'vedette');
								break;
    					}
    					
    				}
//     				onkeyup='fieldQualifiedAuthorChanged(\"".$field['fnamesans']."\",".$inc.",this.value,event)'
//     				callback='authoritySelected'
    				$display .="<input autfield='".$field['fname_id']."_".$inc."' completion='".$field['ajax']."' ".$field['fnamevar_id']." id='".$field['fnamesanslib']."_".$inc."' name='".$field['fnamelib']."' value='".htmlentities($libelle,ENT_QUOTES,$charset)."' type='text' class='saisie-15emr' callback='qualifiedAuthorAuthoritySelected' ".($field['linkfield'] ? "linkfield='".$field['linkfield']."'" : "")." />";
//     			}else{
//     				$display .="<input autfield='".$field['fname_id']."_".$inc."' onkeyup='fieldQualifiedAuthorChanged(\"".$field['fnamesans']."\",".$inc.",this.value,event)' callback='authoritySelected' completion='".$field['ajax']."' ".$field['fnamevar_id']." id='".$field['fnamesanslib']."_".$inc."' name='".$field['fnamelib']."' value='".htmlentities($v[$inc],ENT_QUOTES,$charset)."' type='text' />";
//     			}
    			$display .= "<input class='bouton' value='...' id='".$field['fnamesans']."_authority_selector' title='".htmlentities($msg['title_select_from_list'],ENT_QUOTES,$charset)."' onclick=\"openPopUp('./select.php?what=".$field['selector']."&grammars=tu_authors&caller=search_form&mode=un&".$field['p1']."=".$field['fname_id']."_0&".$field['p2']."=".$field['fnamesanslib']."_0&deb_rech='+".pmb_escape()."(document.getElementById('".$field['fnamesanslib']."_".$inc."').value)+'&callback=qualifiedAuthorAuthoritySelected&infield=".$field['fnamesans']."_".$inc."', 'selector')\" type=\"button\">";
    			$display .= "<input class='bouton' type='button' onclick='this.form.".$field['fnamesanslib']."_".$inc.".value=\"\";this.form.".$field['fname_id']."_".$inc.".value=\"0\";this.form.".$field['fnamesans']."_".$inc.".value=\"0\";' value='".$msg['raz']."'>";
//     			$display .= "<input type='hidden' value='".($fieldvar[$inc][$authority_type]['authority_id'] ?$fieldvar[$inc][$authority_type]['authority_id'] : "")."' id='".$field['fname_aut_id']."_".$inc."' name='".$field['fname_name_aut_id']."' />";
    			$display .= "<input name='".$field['fname_id']."' id='".$field['fname_id']."_".$inc."' value='".htmlentities($values[$inc][$authority_type],ENT_QUOTES,$charset)."' type='hidden'>";
    			if($authority_type == 'qualification') {
    				$display .= "<input id='".$field['linkfield']."' name='".$field['linkfield']."' type='hidden' value='tu_authors' />";
    			}
    			$display .="</div>";
    		}
    		$display .="</div>";
    	}
    	$display .= "</div>";
    	$display .= "
    		<script type='text/javascript'>
    			
    			//callback du selecteur AJAX pour les autorités
				function qualifiedAuthorAuthoritySelected(infield){
					//on enlève le dernier _X
					var tmp_infield = infield.split('_');
					var tmp_infield_length = tmp_infield.length;
					//var inc = tmp_infield[tmp_infield_length-1];
					tmp_infield.pop();
					infield = tmp_infield.join('_');
					//pour assurer la compatibilité avec le selecteur AJAX
					infield=infield.replace('_lib','');
					infield=infield.replace('_authority_label','');
					for(i=0;i<=document.getElementById('field_'+tmp_infield[1]+'_s_'+tmp_infield[3]+'_max_aut').value;i++){
						var searchField = document.getElementById(infield+'_'+i);
						var f_lib = document.getElementById(infield+'_lib'+'_'+i);
						var f_id = document.getElementById(infield+'_id'+'_'+i);
						//var authority_id = document.getElementById(infield.replace('field','fieldvar')+'_authority_id'+'_'+i);
						
						if(f_id.value==''){
							f_id.value=0;
						}
						searchField.value=f_id.value;
						//authority_id.value= f_id.value;
					}
				}
    			
    			function qualifiedAuthorAddElement(line, fnamesans, inc, type){

    				switch (type) {
    					case 'function' :
    						var label = '".$msg['245']."';
    						var ajax = 'fonction';
    						var selector = 'function';
    						var p1 = 'p1';
    						var p2 = 'p2';
    						var linkfield = '';
    						var css_class = 'saisie-15emr';
    						break;
    					case 'qualification' :
    						var label = '".$msg['notice_vedette_composee_author']."';
    						var ajax = 'vedette';
    						var selector = 'vedette';
    						var p1 = 'p1';
    						var p2 = 'p2';
    						var linkfield = fnamesans.replace('field_', 'fieldvar_')+'['+inc+']['+type+'][grammars]';
    						var css_class = 'saisie-15emr';
    						break;
    					case 'author' :
    					default :
    						var label = '".$msg['tu_authors_list']."';
    						var ajax = 'authors';
    						var selector = 'auteur';
							var p1 = 'param1';
							var p2 = 'param2';
							var linkfield = '';
    						var css_class = 'saisie-30emr';
    						break;
					}
						
					var fname=fnamesans+'['+inc+']['+type+']';
					var fname_id=fnamesans+'_'+type+'_id';
					var fnamesanslib=fnamesans+'_'+type+'_lib';
					var fnamelib=fnamesans+'_lib['+inc+']['+type+']';
// 					var fname_name_aut_id=fnamesans+'['+inc+']['+type+'][authority_id]';
// 					var fname_name_aut_id=fname_name_aut_id.replace('field','fieldvar');
// 					var fname_aut_id=fnamesans+'_'+type+'_authority_id';
// 					var fname_aut_id=fname_aut_id.replace('field','fieldvar');
					
    				var element=document.createElement('div');
			  		element.setAttribute('class', 'colonne3');
    			
					var f_id = document.createElement('input');
					f_id.setAttribute('id',fnamesans+'_'+type+'_'+inc);
					f_id.setAttribute('name',fname);
					f_id.setAttribute('value','');
					f_id.setAttribute('type','hidden');
							
					var f_lib = document.createElement('input');
					f_lib.setAttribute('autfield',fname_id+'_'+inc);
					f_lib.setAttribute('callback','qualifiedAuthorAuthoritySelected');
					if(document.getElementById(fnamesanslib+'_0').getAttribute('completion')){
						f_lib.setAttribute('completion',document.getElementById(fnamesanslib+'_0').getAttribute('completion'));
					}
					f_lib.setAttribute('id',fnamesanslib+'_'+inc);
					f_lib.setAttribute('name',fnamelib);
					f_lib.setAttribute('value','');
					f_lib.setAttribute('type','text');
    				f_lib.setAttribute('class','saisie-15emr');
					if(document.getElementById(fnamesanslib+'_0').getAttribute('linkfield')){
						f_lib.setAttribute('linkfield',document.getElementById(fnamesanslib+'_0').getAttribute('linkfield'));
					}
    				if(linkfield != '') {
    					f_lib.setAttribute('linkfield',linkfield);
    				}				
					
    				var f_parcourir = document.createElement('input');
					f_parcourir.setAttribute('class','bouton');
					f_parcourir.setAttribute('type','button');
					f_parcourir.setAttribute('onclick','openPopUp(\'./select.php?what='+selector+'&grammars=tu_authors&caller=search_form&mode=un&'+p1+'='+fname_id+'_'+inc+'&'+p2+'='+fnamesanslib+'_'+inc+'&deb_rech='+".pmb_escape()."(document.getElementById(fnamesanslib+'_0').value)+'&callback=qualifiedAuthorAuthoritySelected&infield='+fnamesans+'_'+type+'_'+inc+'\', \'selector\');');
					f_parcourir.setAttribute('value','".$msg['parcourir']."');
    			
					var f_del = document.createElement('input');
					f_del.setAttribute('class','bouton');
					f_del.setAttribute('type','button');
					f_del.setAttribute('onclick','document.getElementById(\''+fnamesanslib+'_'+inc+'\').value=\'\';document.getElementById(\''+fname_id+'_'+inc+'\').value=\'0\';document.getElementById(\''+fnamesans+'_'+inc+'\').value=\'0\';');
					f_del.setAttribute('value','".$msg['raz']."');
					
// 					var f_aut = document.createElement('input');
// 					f_aut.setAttribute('type','hidden');
// 					f_aut.setAttribute('value','');
// 					f_aut.setAttribute('id',fname_aut_id+'_'+inc);
// 					f_aut.setAttribute('name',fname_name_aut_id);
					
					var f_id2 = document.createElement('input');
					f_id2.setAttribute('type','hidden');
					f_id2.setAttribute('value','');
					f_id2.setAttribute('id',fname_id+'_'+inc);
					f_id2.setAttribute('name',fname_id);
							
			        element.appendChild(f_id);
			        element.appendChild(f_lib);
			        element.appendChild(f_parcourir);
					element.appendChild(f_del);
// 			        element.appendChild(f_aut);
			        element.appendChild(f_id2);
					if(type == 'qualification') {
						var f_grammars = document.createElement('input');
						f_grammars.setAttribute('type','hidden');
						f_grammars.setAttribute('value','tu_authors');
						f_grammars.setAttribute('id',linkfield);
						f_grammars.setAttribute('name',linkfield);
						element.appendChild(f_grammars);
					}
			        
			        line.appendChild(element);
							
					ajax_pack_element(f_lib);
			
				}
						
				function qualifiedAuthorAddLine(id, n_ligne) {
					var inc=document.getElementById('field_'+n_ligne+'_s_'+id+'_max_aut').value;
					inc++;
					
					var template = document.getElementById('elfield_'+n_ligne+'_s_'+id);
					
			        var line=document.createElement('div');
					line.setAttribute('class', 'row');
					template.appendChild(line);		
							
							
					qualifiedAuthorAddElement(line, 'field_'+n_ligne+'_s_'+id, inc, 'author');
					qualifiedAuthorAddElement(line, 'field_'+n_ligne+'_s_'+id, inc, 'function');
					".($pmb_authors_qualification ? "qualifiedAuthorAddElement(line, 'field_'+n_ligne+'_s_'+id, inc, 'qualification');" : "")."
							
					document.getElementById('field_'+n_ligne+'_s_'+id+'_max_aut').value=inc;
					
					//Plus d'un champ : on bloque
					var selector = document.getElementById('op_'+n_ligne+'_s_'+id);
					selector.disabled=true;
					operators_to_enable.push('op_'+n_ligne+'_s_'+id);
				}
			</script>";
    	$display .="<div class='row'>";
    	$display .= htmlentities($msg["operator_between_multiple_authorities"],ENT_QUOTES,$charset);
 		$display .= "&nbsp;<input type='radio' ".(((!$fieldvar['operator_between_multiple_authorities'][0])||($fieldvar['operator_between_multiple_authorities'][0]=='or'))?"checked=''":"")." value='or' name='fieldvar_".$this->n_ligne."_s_".$this->id."[operator_between_multiple_authorities][]'>&nbsp;".htmlentities($msg["operator_between_multiple_authorities_or"],ENT_QUOTES,$charset);
		$display .= "&nbsp;<input type='radio' ".($fieldvar['operator_between_multiple_authorities'][0]=='and'?"checked=''":"")." value='and' name='fieldvar_".$this->n_ligne."_s_".$this->id."[operator_between_multiple_authorities][]'>&nbsp;".htmlentities($msg["operator_between_multiple_authorities_and"],ENT_QUOTES,$charset);
		$display .="</div>";
    	if($nb_values>1){
    		$display .="
    		<script type='text/javascript'>
				document.getElementById('op_".$this->n_ligne."_s_".$this->id."').disabled=true;
				operators_to_enable.push('op_".$this->n_ligne."_s_".$this->id."');
			</script>";
    	}
    	return $display;
    }
    
    //fonction de conversion de la saisie en quelque chose de compatible avec l'environnement
    public function transform_input() {
    }
    
    //fonction de création de la requête (retourne une table temporaire)
    public function make_search() {
        
		//Récupération des valeurs saisies
    	$values_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$values_};
    	$values=${$values_};
    	
    	//Recuperation des variables auxiliaires
    	$fieldvar_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$fieldvar_};
    	$fieldvar=${$fieldvar_};
    	
    	if (!$this->is_empty($values)) {
    		$notices = array();
	    	
	    	$query = "select distinct id_authority from authorities 
	    			join responsability_tu on responsability_tu.responsability_tu_num=authorities.num_object and authorities.type_object = 7 ";
	    	if($this->params['TYPE'] == 'tu_qualified_interpreter') {
	    		$query .= "and responsability_tu.responsability_tu_type=1";
	    	} else {
	    		$query .= "and responsability_tu.responsability_tu_type=0";
	    	}
	    	$restricts = array();
	    	foreach ($values as $value) {
	    		$restrict = array();
    			if(isset($value['author']) && $value['author']*1) {
    				$restrict[] = "responsability_tu.responsability_tu_author_num=".$value['author'];
    			}
    			if(isset($value['function']) && $value['function']) {
    				$restrict[] = "responsability_tu.responsability_tu_fonction='".$value['function']."'";
    			}
    			if(isset($value['qualification']) && $value['qualification']) {
    				$restrict[] = "id_responsability_tu in (select vedette_link.num_object from vedette_link where vedette_link.num_vedette = ".$value['qualification'].")";
    			}
    			if(count($restrict)) {
    				$restricts[] = "(".implode(' and ', $restrict).")";
    			}
	    	}
	    	if(count($restricts)) {
	    		$query .= " where (".implode(') '.$fieldvar['operator_between_multiple_authorities'][0].' (', $restricts).")";
	    	}
	    	if($this->search->tableName == "notices"){
	    	    $query = "select ntu_num_notice as notice_id from notices_titres_uniformes where ntu_num_tu in (
                            select authorities.num_object as ntu_num_tu from authorities
                                join (".$query.") as t1 on t1.id_authority = authorities.id_authority and authorities.type_object = 7)";
	    	}
	    	
	    	pmb_mysql_query("create temporary table t_s_tu_qualified_author (".$this->search->keyName." integer unsigned not null) as ".$query);
	 		pmb_mysql_query("alter table t_s_tu_qualified_author add primary key(".$this->search->keyName.")");
    	}
		return "t_s_tu_qualified_author"; 
    }
    
    //fonction de traduction littérale de la requête effectuée (renvoie un tableau des termes saisis)
    public function make_human_query() {
    	global $msg;
    	global $include_path;
    	global $fonction_auteur;
    	// récupération des codes de fonction
    	if (!count($fonction_auteur)) {
    		$fonction_auteur = new marc_list('function');
    		$fonction_auteur = $fonction_auteur->table;
    	}
    	
    	//Récupération des valeurs saisies
    	$values_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$values_};
    	$values=${$values_};
    	
    	//Recuperation des variables auxiliaires
    	$fieldvar_="fieldvar_".$this->n_ligne."_s_".$this->id;
    	global ${$fieldvar_};
    	$fieldvar=${$fieldvar_};
    	
    	$human_query = array();
    	if (!$this->is_empty($values)) {
    		$humans=array();
    		foreach ($values as $value) {
    			$human = array();
    			if(isset($value['author']) && $value['author']*1) {
    				$human[] = $msg['tu_authors_list'].' : '.search::get_authoritie_display($value['author'], 'auteur');
    			}
    			if(isset($value['function']) && $value['function']) {
    				$human[] = $msg['245'].' : '.$fonction_auteur[$value['function']];
    			}
    			if(isset($value['qualification']) && $value['qualification']) {
					$human[] = $msg['notice_vedette_composee_author'].' : '.search::get_authoritie_display($value['qualification'], 'vedette');;
    			}
    			if(count($human)) {
    				$humans[] = ' [ '.implode(', ', $human).' ] ';
    			}
    		}
    		switch($fieldvar['operator_between_multiple_authorities'][0]) {
    			case 'and' :
    				$human_query[] = implode(' '.$msg["operator_between_multiple_authorities_and"].' ',$humans);
    				break;
    			case 'or' :
    			default :
    				$human_query[] = implode(' '.$msg["operator_between_multiple_authorities_or"].' ',$humans);
    				break;
    		}
    	} 
		return $human_query;    
    }
    
    public function make_unimarc_query() {
    	//Récupération des valeurs saisies
    	$values_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$values_};
    	$values=${$values_};
    	return "";
    }    
    
	//fonction de vérification du champ saisi ou sélectionné
    public function is_empty($values) {
    	
    	//Récupération des valeurs saisies
    	$values_="field_".$this->n_ligne."_s_".$this->id;
    	global ${$values_};
    	$values=${$values_};
    	
    	if (count($values)) {
    		if (($values[0]['author']=="") && ($values[0]['function']=="") && ($values[0]['qualification']=="")) return true;
    			else return false;
    	} else {
    		return true;
    	}	
    }
}