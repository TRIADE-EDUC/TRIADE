<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_common.tpl.php,v 1.8 2023/04/07 14:38:40 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates communs
global $jscript_common_selector_simple;
global $jscript_common_selector;
global $what;

$jscript_common_selector_simple = "
	<div id='indexation_infos' style='display:none;'></div>
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value, callback){
		var w = window;
		if(w.parent.document.getElementById('!!param1!!')) {
			w.parent.document.getElementById('!!param1!!').value = id_value;
			w.parent.document.getElementById('!!param2!!').value = reverse_html_entities(libelle_value);
		} else {
			set_parent_value(f_caller,'!!param1!!', id_value);
			set_parent_value(f_caller,'!!param2!!', reverse_html_entities(libelle_value));
		}
		if(callback) {
			w.parent[callback]('!!infield!!');
		}
		closeCurrentEnv('$what');
	}
	-->
	</script>";

$jscript_common_selector = "
	<div id='indexation_infos' style='display:none;'></div>
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value, callback){
		var w = window;
		var p1 = '!!param1!!';
		var p2 = '!!param2!!';
		
		var max_element = w.parent.document.getElementById('max_'+p2);
		if(max_element && max_element.value){
			var trouve=false;
			var trouve_id=false;
			for(i_element=0;i_element<=max_element.value;i_element++){
				if(w.parent.document.getElementById(p1+'_'+i_element)) {
					if(w.parent.document.getElementById(p1+'_'+i_element).value==0){
						w.parent.document.getElementById(p1+'_'+i_element).value=id_value;
						w.parent.document.getElementById(p2+'_'+i_element).value=reverse_html_entities(libelle_value);
						trouve=true;
						break;
					}else if(w.parent.document.getElementById(p1+'_'+i_element).value==id_value){
						trouve_id=true;
					}
				}
			}
			if(!trouve && !trouve_id){
				w.parent.add_!!param2!!();
				w.parent.document.getElementById(p1+'_'+(max_element.value-1)).value=id_value;
				w.parent.document.getElementById(p2+'_'+(max_element.value-1)).value=reverse_html_entities(libelle_value);
			}
			if(callback)
				w.parent[callback](p1.replace('_id','')+'_'+i_element);
		} else {
			//on enlève le dernier _X
			var tmp_p1 = p1.split('_');
			var tmp_p1_length = tmp_p1.length;
			tmp_p1.pop();
			var p1bis = tmp_p1.join('_');
			
			var tmp_p2 = p2.split('_');
			var tmp_p2_length = tmp_p2.length;
			tmp_p2.pop();
			var p2bis = tmp_p2.join('_');
			
			var max_aut = w.parent.document.getElementById(p1bis.replace('id','max_aut'));
			if(max_aut && (p1bis.replace('id','max_aut').substr(-7)=='max_aut')){
				var trouve=false;
				var trouve_id=false;
				for(i_aut=0;i_aut<=max_aut.value;i_aut++){
					if(w.parent.document.getElementById(p1bis+'_'+i_aut).value==0){
						w.parent.document.getElementById(p1bis+'_'+i_aut).value=id_value;
						w.parent.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
						trouve=true;
						break;
					}else if(w.parent.document.getElementById(p1bis+'_'+i_aut).value==id_value){
						trouve_id=true;
					}
				}
				if(!trouve && !trouve_id){
					w.parent.add_line(p1bis.replace('_id',''));
					w.parent.document.getElementById(p1bis+'_'+i_aut).value=id_value;
					w.parent.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
				}
				if(callback)
					w.parent[callback](p1bis.replace('_id','')+'_'+i_aut);
			}else{
				set_parent_value(f_caller,'!!param1!!', id_value);
				set_parent_value(f_caller,'!!param2!!', reverse_html_entities(libelle_value));
				if(callback)
					w.parent[callback]('$infield');
				closeCurrentEnv('$what');
			}
		}
	}
	-->
	</script>
";
