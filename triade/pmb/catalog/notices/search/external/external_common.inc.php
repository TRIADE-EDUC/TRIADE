<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external_common.inc.php,v 1.29 2023/12/19 08:03:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $from_mode, $external_env, $external_type, $class_path, $include_path, $code, $search, $serialized_search;

if(!isset($from_mode)) $from_mode = '';
if(!isset($external_env)) $external_env = '';

//Enregistrement du type de recherche externe avant toute chose pour quye le message soit bon dans le formulaire template
if ($external_type) $_SESSION["ext_type"]=$external_type;
if (!$_SESSION["ext_type"]) $_SESSION["ext_type"]="simple";

require_once($class_path."/search.class.php");
require_once($class_path."/searcher.class.php");
require_once($class_path."/mono_display_unimarc.class.php");
require_once($include_path."/external.inc.php");
require_once($class_path."/z3950_notice.class.php");

function decale($var,$var1) {
	global ${$var};
	global ${$var1};
	${$var1}=${$var};
}

function get_sources() {
	global $msg;
	
	$sources = array();
	$sources_no_category = array();
	//Recherche des sources
	$query = "SELECT connectors_categ_sources.num_categ, connectors_sources.source_id, connectors_categ.connectors_categ_name as categ_name, connectors_sources.name, connectors_sources.comment, connectors_sources.repository, connectors_sources.opac_allowed, connectors_sources.gestion_selected, source_sync.cancel FROM connectors_sources LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_source = connectors_sources.source_id) LEFT JOIN connectors_categ ON (connectors_categ.connectors_categ_id = connectors_categ_sources.num_categ) LEFT JOIN source_sync ON (connectors_sources.source_id = source_sync.source_id AND connectors_sources.repository=2) ORDER BY connectors_categ.connectors_categ_name, connectors_sources.name ";
	$result = pmb_mysql_query($query);
	while ($srce=pmb_mysql_fetch_object($result)) {
		if ($srce->categ_name) {
			$sources[$srce->categ_name][] = $srce;
		} else {
			$sources_no_category[] = $srce;
		}
	}
	if(count($sources_no_category)) {
		$sources[$msg["source_no_category"]] =$sources_no_category;
	}
	return $sources;
}

function do_sources() {
	global $charset,$source,$msg,$first,$base_path;
	
	$r="<script type='text/javascript'>
		function debloque(source_id) {
			var ajax = new http_request();
			ajax.request('".$base_path."/ajax.php?module=catalog&categ=debloque_source&item='+source_id,true,'',true,debloque_callback);
		}
		
		function debloque_callback(response) {
			data = eval('('+response+')');
			source_id = data.source_id;
			if (source_id) {
				for(i=0; i<document.search_form.elements.length; i++){
					if(document.search_form.elements[i].name == 'source[]')	{
						if (document.search_form.elements[i].value == source_id) {
							document.search_form.elements[i].checked = true;
							document.getElementById('spandebloque_'+document.search_form.elements[i].id).style.display='none';
							document.getElementById('label_'+document.search_form.elements[i].id).className='';
						}
					}
				}
			}
		}
	</script>
	<!-- <script type='text/javascript' src='./javascript/tabform.js'></script> -->
	<script type=\"text/javascript\" src=\"./javascript/tablist.js\"></script>
	";
	
	$r.="<div class='row'>
	<a href=\"javascript:expandAll(); rec_expand_collapse('expand');\"><img src='".get_url_icon('expand_all.gif')."' style='border:0px' id=\"expandall\"></a>

	<a href=\"javascript:collapseAll(); rec_expand_collapse('collapse');\"><img src='".get_url_icon('collapse_all.gif')."' style='border:0px' id=\"collapseall\"></a>	<input type='hidden' name='b_level' value='m' />
	<input type='hidden' name='h_level' value='0' />
	<input type='hidden' name='first' value='1'/>
	</div>";

	if ($source) $_SESSION["checked_sources"]=$source;
	if (isset($_SESSION["checked_sources"])&&(!$source)) $source=$_SESSION["checked_sources"];
	if (!is_array($source)) $source=array();
	
	$r .= "<div>";
	$count=0;
	$debloque_form_outputed = array();
	
	//Recherche des sources
	$sources = get_sources();
    foreach ($sources as $category_name=>$category) {
    	$count++;
		$open="open_".$count;
		global ${$open};
		if(!isset($_SESSION["sources_open_".$count])) $_SESSION["sources_open_".$count] = 0;
		if ((!$first)&&($_SESSION["sources_open_".$count])) ${$open}=1; else if ($first) $_SESSION["sources_open_".$count]=${$open};
		$img_plus=${$open}?get_url_icon('minus.gif'):get_url_icon('plus.gif');
		$r .= '</div><div id="elconn'.$count.'Parent" class="parent" width="100%">
		<h3>
			<img src="'.$img_plus.'" class="img_plus" name="imEx" id="elconn'.$count.'Img" title="'.htmlentities($msg["connector_external_plus_detail"], ENT_QUOTES, $charset).'" alt="'.htmlentities($msg["connector_external_plus_detail"], ENT_QUOTES, $charset).'" onClick="expandBase(\'elconn'.$count.'\', true); if (document.getElementById(\'elconn'.$count.'Child\').style.display==\'none\') document.search_form.open_'.$count.'.value=0; else  document.search_form.open_'.$count.'.value=1; return false;" />&nbsp;
			'.$category_name.'
		</h3>
		</div><div id=\'elconn'.$count.'Child\' class=\'child\' '.(${$open}?"startOpen='Yes'":"").' style=\'display:none\'><input type="hidden" name="open_'.$count.'" id="open_'.$count.'" value="'.${$open}.'"/>';
    	foreach ($category as $srce) {
    		$debloque_source = "debloque_source_".$srce->source_id;
    		global ${$debloque_source};
    		$r.="<div style='width:33%; float:left'>
				<input type='checkbox' name='source[]' value='".$srce->source_id."' id='source_".$srce->source_id."_".$count."' onclick='change_source_checkbox(source_".$srce->source_id."_".$count.", ".$srce->source_id.");'";
    		if ((array_search($srce->source_id,$source)!==false)&&($srce->cancel!=2 || ${$debloque_source})) {
    			$r.=" checked";
    		} else if (!count($source) && $srce->gestion_selected) {
	   			$r.=" checked";
	   		}
    		$r.="/><label for='source_".$srce->source_id."_".$count."' ".($srce->cancel==2?"class='erreur'":"")." id='label_source_".$srce->source_id."_".$count."'><img src='images/".($srce->repository==1?"entrepot.png":"globe.gif")."'/>&nbsp;".htmlentities($srce->name.($srce->comment?" : ".$srce->comment:""),ENT_QUOTES,$charset);
    		$r .= "</label>";
    		if ($srce->cancel==2 && !${$debloque_source}) {
    			if (!isset($debloque_form_outputed[$srce->source_id])) {
    				$r.=" <input type='hidden' name='debloque_source_".$srce->source_id."' id='debloque_source_".$srce->source_id."' value='".(${$debloque_source} ? "1" : "0")."'/>";
    				$debloque_form_outputed[$srce->source_id] = true;
    			}
    			$r.=" <span id='spandebloque_source_".$srce->source_id."_".$count."'>(<a href='#' onClick='debloque(".$srce->source_id.");'>".$msg["connecteurs_debloque"]."</a>)</span>";
    		}
    		$r.="</div>";
    	}
    }
   	$r.="</div><div class='row'></div>";
   	if ($count) {
   		$r.="
   		<script type=\"text/javascript\">
			function rec_expand_collapse(type) {
				var i;
				for (i=1; i<=$count; i++) {
					if (type=='expand') {
						document.getElementById('open_'+i).value=1;
   					} else {
   						document.getElementById('open_'+i).value=0;
   					}
				}
			}
		</script>";
   	}
   	return $r;
}

//Instanciation de la classe de recherche
//Si c'est une multi
if ($_SESSION["ext_type"]=="multi") {
	$sc=new search(true,"search_fields_unimarc");
	$sc->remove_forbidden_fields();
} else {
    $sc=new search(true,"search_simple_fields_unimarc");
}

//Si c'est une simple 
if ($_SESSION["ext_type"]=="simple") {
	//Si ça vient d'une autre recherche, on transforme !
 	if ((string)$from_mode!="") {
 		//Récupération des variables
 		switch ($from_mode) {
 			case "0":
 				if ($code) {
 					$op_="STARTWITH";
					
					$search[0]="f_31";
					//opérateur
		    		$op="op_0_".$search[0];
		    		global ${$op};
		    		${$op}=$op_;
		    		    			
		    		//contenu de la recherche
		    		$field = "field_0_".$search[0];
		    		$field_array_ = array();
		    		$field_array_[0] = $code;
		    		global ${$field};
		    		${$field} = $field_array_;
		    	    	
		    		//opérateur inter-champ
		    		$inter="inter_0_".$search[0];
		    		global ${$inter};
		    		${$inter}="";
		    			    		
		    		//variables auxiliaires
		    		$fieldvar_="fieldvar_0_".$search[0];
		    		global ${$fieldvar_};
		    		${$fieldvar_}="";
		    		$fieldvar=${$fieldvar_};
 				} else searcher_title::convert_simple_multi_unimarc($_SESSION["CURRENT"]);
 				break;
			case "1":
				searcher_subject::convert_simple_multi_unimarc($_SESSION["CURRENT"]);
				break;
			case "3":
				searcher_publisher::convert_simple_multi_unimarc($_SESSION["CURRENT"]);
				break;
			case "7":
			    // recherche externe
			    $serialized_search = $_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["serialized_search"];
				break;
 		}
 	} else {
		if ($external_env) {
			$external_env=unserialize(stripslashes($external_env));
			foreach ($external_env as $varname=>$varvalue) {
				global ${$varname};
				${$varname}=$varvalue;
			}
		}
 	}
} else {
	if ($from_mode==6) {
		//Récupération de l'environnement
		$search=$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]["search"];
   		//Pour chaque champ
		if(empty($search)) {
			$search = array();
		}
   		for ($i=0; $i<count($search); $i++) {
	   	 	//Récupération de l'opérateur
	   	 	$op="op_".$i."_".$search[$i];
	   	 	global ${$op};
	   	 	${$op}=$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"][$op];
	   	 			    			
	    	//Récupération du contenu de la recherche
	    	$field_="field_".$i."_".$search[$i];
	    	global ${$field_};
	    	${$field_}=$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"][$field_];
	    	$field=${$field_};
	    	
	    	//Récupération de l'opérateur inter-champ
	    	$inter="inter_".$i."_".$search[$i];
	    	global ${$inter};
	    	${$inter}=$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"][$inter];
	    	    		
	    	//Récupération des variables auxiliaires
	    	$fieldvar_="fieldvar_".$i."_".$search[$i];
	    	global ${$fieldvar_};
	    	${$fieldvar_}=$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"][$fieldvar_];
	    	$fieldvar=${$fieldvar_};
	    }
	}
	if ($from_mode==7) {
	    // recherche externe
	    $serialized_search = $_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["serialized_search"];
	}
}

if (isset($serialized_search)) {
	$sc->unserialize_search(stripslashes($serialized_search));
}
?>
