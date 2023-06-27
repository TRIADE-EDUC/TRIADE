<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_history.inc.php,v 1.32 2019-06-06 13:20:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($include_path."/rec_history.inc.php");

print "<div id='search_history_container' class='search_history_container'>";
if ($_SESSION["nb_queries"]) {
	print "<script>
		var history_all_checked = false;
		
		function check_uncheck_all_history() {
			if (history_all_checked) {
				setCheckboxes('cases_a_cocher', 'cases_suppr', false);
				history_all_checked = false;
				document.getElementById('show_history_checked_all').value = pmbDojo.messages.getMessage('history', 'show_history_check_all');
				document.getElementById('show_history_checked_all').title = pmbDojo.messages.getMessage('history', 'show_history_check_all');
				if (document.getElementById('show_history_checked_all_1')) {
					document.getElementById('show_history_checked_all_1').value = pmbDojo.messages.getMessage('history', 'show_history_check_all');
					document.getElementById('show_history_checked_all_1').title = pmbDojo.messages.getMessage('history', 'show_history_check_all');
				}
			} else {
				setCheckboxes('cases_a_cocher', 'cases_suppr', true);
				history_all_checked = true;
				document.getElementById('show_history_checked_all').value = pmbDojo.messages.getMessage('history', 'show_history_uncheck_all');
				document.getElementById('show_history_checked_all').title = pmbDojo.messages.getMessage('history', 'show_history_uncheck_all');
				if (document.getElementById('show_history_checked_all_1')) {
					document.getElementById('show_history_checked_all_1').value = pmbDojo.messages.getMessage('history', 'show_history_uncheck_all');
					document.getElementById('show_history_checked_all_1').title = pmbDojo.messages.getMessage('history', 'show_history_uncheck_all');
				}
			}
			return false;
		}
		
		function setCheckboxes(the_form, the_objet, do_check) {
			 var elts = document.forms[the_form].elements[the_objet+'[]'] ;
			 var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
			 if (elts_cnt) {
				for (var i = 0; i < elts_cnt; i++) {
			 		elts[i].checked = do_check;
			 	} // end for
			 } else {
			 	elts.checked = do_check;
			 } 
			 return true;
		}
						
		function verifCheckboxes(the_form, the_objet) {
			var bool=false;
			var elts = document.forms[the_form].elements[the_objet+'[]'] ;
			var elts_cnt  = (typeof(elts.length) != 'undefined')
	                  ? elts.length
	                  : 0;
	
			if (elts_cnt) {
					
				for (var i = 0; i < elts_cnt; i++) { 		
					if (elts[i].checked)
					{
						bool = true;
					}
				}
			} else {
					if (elts.checked)
					{
						bool = true;
					}
			}
			return bool;
		} 
	</script>";
	print "<h3 class='title_history'><span>".$msg["history_title"]."</span></h3>";
}

print "<form name='cases_a_cocher' class='search_history_form' method='post' action='./index.php?lvl=search_history&raz_history=1'>";

if ($_SESSION["nb_queries"]!=0) {
	print "<div id='history_action'>";
	print "<input type='button' class='bouton' id='show_history_checked_all' value=\"".$msg["show_history_check_all"]."\" onClick=\"check_uncheck_all_history();\" /><span class='espaceResultSearch'>&nbsp;</span>";
	print "<input type='button' class='bouton' value=\"".$msg["suppr_elts_coch"]."\" onClick=\"if (verifCheckboxes('cases_a_cocher','cases_suppr')){ document.cases_a_cocher.submit(); return false;}\" /><span class='espaceResultSearch'>&nbsp;</span>";
	print "<input type='button' id='search_history' class='bouton search_history_combine_button' value=\"".$msg["search_history_combine"]."\" />";
	print "<select id='search_history_combine_op' name='search_history_combine_op' class='search_history_combine_op'>
				<option value='and'>".$msg['search_and']."</option>
				<option value='or'>".$msg['search_or']."</option>
			</select>";
	print "</div>";
	print "<ul class='search_history_ul'>";
	for ($i=$_SESSION["nb_queries"]; $i>=1; $i--) {
		if ($_SESSION["search_type".$i]!="module") {
		    print get_history_row($i);
		}
	}
	print "</ul>";
	if ($_SESSION["nb_queries"] > 20) {
		print "<div id='history_action_1'>";
		print "<input type='button' class='bouton' id='show_history_checked_all_1' value=\"".$msg["show_history_check_all"]."\" onClick=\"check_uncheck_all_history();\" /><span class='espaceResultSearch'>&nbsp;</span>";
		print "<input type='button' class='bouton' value=\"".$msg["suppr_elts_coch"]."\" onClick=\"if (verifCheckboxes('cases_a_cocher','cases_suppr')){ document.cases_a_cocher.submit(); return false;}\" /><span class='espaceResultSearch'>&nbsp;</span>";
		print "<input type='button' id='search_history_1' class='bouton search_history_combine_button' value=\"".$msg["search_history_combine"]."\" /><span class='espaceResultSearch'>&nbsp;</span>";
		print "<select id='search_history_combine_op_1' name='search_history_combine_op' class='search_history_combine_op'>
					<option value='and'>".$msg['search_and']."</option>
					<option value='or'>".$msg['search_or']."</option>
				</select>";
		print "</div>";
	}
	
	print "<script type='text/javascript'>
			require(['dojo/dom',
					'dojo/on',
					'dojo/_base/lang',
					'dojo/dom-construct',
					'dojo/query',
                    'dojo/ready'],
			function(dom, on, lang, domConstruct, query, ready){
				var search_history_combine = function(){
					var checkboxes = query('input[type=\"checkbox\"][data-search-id]');
					var checkedBoxes = [];
					checkboxes.forEach(function(box){
						if(box.checked){
							checkedBoxes.push(box);
						}
					});
					if(checkedBoxes.length == 1){
						document.forms['search_'+checkedBoxes[0].value].submit();
					}else{
						var form = domConstruct.create('form', {
							action: './index.php?lvl=more_results&mode=extended',
							name: 'search',
							method: 'post',
						}, document.body);
						for(var i=0 ; i<checkedBoxes.length ; i++){
							domConstruct.create('input', {
								type: 'hidden',
								name: 'search[]',
								value: 's_1',
							}, form);
							domConstruct.create('input', {
								type: 'hidden',
								name: 'op_'+i+'_s_1',
								value: 'EQ',
							}, form);
							domConstruct.create('input', {
								type: 'hidden',
								name: 'field_'+i+'_s_1[]',
								value: checkedBoxes[i].value,
							}, form);
							if(i!=0){
								domConstruct.create('input', {
									type: 'hidden',
									name: 'inter_'+i+'_s_1',
									value: dom.byId('search_history_combine_op').value,
								}, form);
							}
						}
						domConstruct.create('input', {
							type: 'hidden',
							name: 'explicit_search',
							value: 1,
						}, form);
	
						domConstruct.create('input', {
							type: 'hidden',
							name: 'search_xml_file',
							value: 'search_fields',
						}, form);
						domConstruct.create('input', {
							type: 'hidden',
							name: 'launch_search',
							value: 1,
						}, form);
						domConstruct.create('input', {
							type: 'hidden',
							name: 'search_type_asked',
							value: 'extended_search',
						}, form);
						form.submit();
					}
				}
                ready(function(){
                    on(dom.byId('search_history'), 'click', search_history_combine);
                    if (dom.byId('search_history_1')) {
                    	on(dom.byId('search_history_1'), 'click', search_history_combine);
                    }
                    query('.search_history_combine_op').forEach(function(op, i, ops) {
                    	on(op, 'change', function(e) {
                    		ops.forEach(function(node) {
                    			node.value = e.target.value;
                    		});
                    	});
                    });
                });                
			});
			</script>
			";
	
} else {
	print "<span class='etiq_champ'>".$msg["histo_empty"]."</span>";	
}

print "</form>";

print "</div>";
//Si autolevel2=2, on re-soumet immédiatement sans passer par le lvl1
if (($opac_autolevel2==2) && ($_SESSION["nb_queries"]!=0)) {
	for ($i=$_SESSION["nb_queries"]; $i>=1; $i--) {
		if ($_SESSION["search_type".$i]!="module") {
			get_history($i);
			if ($_SESSION["search_type".$i]=="simple_search") {
				print "<form method='post' style='display:none' name='search_".$i."' action='".$base_path."/index.php?lvl=more_results&autolevel1=1'>";
				if (function_exists("search_other_function_post_values")){
					print search_other_function_post_values();
				}
				if(count($map_emprise_query)){
					foreach($map_emprises_query as $map_emprise_query){
						print " <input type='hidden' name='map_emprises_query[]' value='".$map_emprise_query."'>";
					}
				}
				print "
		  		<input type='hidden' name='mode' value='tous'>
		  		<input type='hidden' name='typdoc' value='".$typdoc."'>
		  		<input type='hidden' name='user_query' value='".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."'>";				
				if ($look_TITLE) {
					print "<input type='hidden' name='look_TITLE' value='1' />";
				}
				if ($look_AUTHOR) {
					print "<input type='hidden' name='look_AUTHOR' value='1' />";
				}
				if ($look_PUBLISHER) {
					print "<input type='hidden' name='look_PUBLISHER' value='1' />";
				}
				if ($look_TITRE_UNIFORME) {
					print "<input type='hidden' name='look_TITRE_UNIFORME' value='1' />";
				}
				if ($look_COLLECTION) {
					print "<input type='hidden' name='look_COLLECTION' value='1' />";
				}
				if ($look_SUBCOLLECTION) {
					print "<input type='hidden' name='look_SUBCOLLECTION' value='1' />";
				}
				if ($look_CATEGORY) {
					print "<input type='hidden' name='look_CATEGORY' value='1' />";
				}
				if ($look_INDEXINT) {
					print "<input type='hidden' name='look_INDEXINT' value='1' />";
				}
				if ($look_KEYWORDS) {
					print "<input type='hidden' name='look_KEYWORDS' value='1' />";
				}
				if ($look_ABSTRACT) {
					print "<input type='hidden' name='look_ABSTRACT' value='1' />";
				}
				if ($look_ALL) {
					print "<input type='hidden' name='look_ALL' value='1' />";
				}
				if ($look_DOCNUM) {
					print "<input type='hidden' name='look_DOCNUM' value='1' />";
				}
				if ($look_CONCEPT) {
					print "<input type='hidden' name='look_CONCEPT' value='1' />";
				}
				print "</form>";
			} elseif ($_SESSION["search_type".$i] == 'extended_search_authorities') {
			    $action=$base_path."/index.php?lvl=index&search_type_asked=".$_SESSION["search_type".$i];
			    $sc=new search();
			    print $sc->make_hidden_search_form("./index.php?lvl=more_results&mode=extended_authorities","search_".$i,"",true);
			}else {
				$action=$base_path."/index.php?lvl=index&search_type_asked=extended_search";
				$sc=new search();
				print $sc->make_hidden_search_form("./index.php?lvl=more_results&mode=extended","search_".$i,"",true);
			}					
		}
	}
}
?>