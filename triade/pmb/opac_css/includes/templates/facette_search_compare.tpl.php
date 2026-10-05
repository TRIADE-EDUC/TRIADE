<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette_search_compare.tpl.php,v 1.7 2023/08/17 09:47:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $base_path,$charset,$msg;
global $facette_search_compare_wrapper;
global $facette_search_compare_header;
global $facette_search_compare_line;
global $facette_search_compare_element;
global $facette_search_compare_hidden_line;
global $facette_search_compare_hidden_element;

$facette_search_compare_wrapper="
<style>
	#compare_wrapper {
		width:100%;
		height:100%;
	}

	.first_collumn {
		width:!!first_collumn_size!!%;
	}

	.compare_hearder{
		width:!!cullumn_size!!%;
		max-width:!!cullumn_size!!%;
	}

	.compare_element{
		width:!!cullumn_size!!%;
		max-width:!!cullumn_size!!%;
	}

	.compare_hidden_element{
		width:!!cullumn_size!!%;
		max-width:!!cullumn_size!!%;
	}

	.compare_hidden_element a:hover {
		cursor:pointer;
	}

</style>

<script src='$base_path/includes/javascript/select.js' ></script>

<script>

	function expandAll_compare(){
		var tmpColl=document.getElementsByClassName('compare_line');

		for(var i=0;i<tmpColl.length;i++){
			if(tmpColl[i].nextElementSibling.style.display=='none'){
				toggle_hidden_line(tmpColl[i],tmpColl[i].nextElementSibling.getAttribute('id'));
			}
		}
	}

	function collapseAll_compare(){
		var tmpColl=document.getElementsByClassName('compare_line');

		for(var i=0;i<tmpColl.length;i++){
			if(tmpColl[i].nextElementSibling.style.display!='none'){
				toggle_hidden_line(tmpColl[i],tmpColl[i].nextElementSibling.getAttribute('id'));
			}
		}
	}

	function toggle_hidden_line(current_line,hidden_line_id){
		var line=document.getElementById(hidden_line_id);

		if(line.style.display=='none'){
			line.style.display='';
			line.previousElementSibling.setAttribute('class',line.previousElementSibling.getAttribute('class')+' compare_line_toggled');
			if(current_line.firstElementChild.lastElementChild && current_line.firstElementChild.lastElementChild.nodeName=='IMG'){
				current_line.firstElementChild.lastElementChild.setAttribute('src','".get_url_icon("minus.gif")."');
				current_line.firstElementChild.lastElementChild.setAttribute('class','img_plus');
			}

		}else{
			line.style.display='none';
			var line_classes=line.previousElementSibling.getAttribute('class');
			line.previousElementSibling.setAttribute('class',line_classes.replace(' compare_line_toggled',''));

			if(current_line.firstElementChild.lastElementChild && current_line.firstElementChild.lastElementChild.nodeName=='IMG'){
				current_line.firstElementChild.lastElementChild.setAttribute('src','".get_url_icon("plus.gif")."');
				current_line.firstElementChild.lastElementChild.setAttribute('class','img_plus');
			}
		}
	}

	function open_notice_popup(notice_id,notice_affichage_cmd,notice_enrichment){
		var req = new http_request();

		req.request(\"./ajax.php?module=expand_notice&categ=expand\",true,'notice_affichage_cmd='+notice_affichage_cmd,true,function(data){
			var text=data;

			var el='el'+notice_id;
			var whichEl = document.getElementById('notice');

			open_popup(whichEl,text);

			if (notice_enrichment){
				getEnrichment(notice_id);
			}
			if (whichEl.getAttribute('simili_search')){
				show_simili_search(notice_id);
				show_expl_voisin_search(notice_id);
			}
			var whichAddthis = document.getElementById(el + 'addthis');
			if (whichAddthis && !whichAddthis.getAttribute('added')){
				creeAddthis(el);
			}
			if(document.getElementsByName('surligne')) {
				var surligne = document.getElementsByName('surligne');
				if (surligne[0].value == 1) rechercher(1);
			}
			ReinitializeAddThis();

		});
	}




	function compare_see_more(hidden_element,notices_ids){
		var req = new http_request();
		var sended_datas={'json_notices_ids':notices_ids};
		req.request(\"./ajax.php?module=ajax&categ=!!categ!!&sub=compare_see_more\",true,'sended_datas='+encodeURIComponent(JSON.stringify(sended_datas)),true,function(data){
			var jsonArray = JSON.parse(data);

			var parent=hidden_element.parentElement;
			hidden_element.parentNode.removeChild(hidden_element);
			parent.firstElementChild.innerHTML+=jsonArray['notices'];
			if(jsonArray['see_more']){
				parent.innerHTML+=jsonArray['see_more'];
			}
		});
	}

	!!compare_wrapper_script!!
</script>
<div id='notice'></div>
<table id='compare_wrapper'>
	<tr id='compare_hearders'>
		<th class='first_collumn'>&nbsp;</th>
		!!compare_header!!
	</tr>
	!!compare_body!!
</table>
";

$facette_search_compare_header="
	<th class='compare_hearder'>
		!!compare_hearder_libelle!!
	</th>
";

$facette_search_compare_line="
	<tr class='compare_line !!even_odd!!' onclick='!!compare_line_onclick!!'>
		<td class='first_collumn'>
			<img src='".get_url_icon("plus.gif")."' class='img_plus'/>
			!!groupedby_libelle!!
		</td>
		!!compare_line_elements!!
	</tr>
";

$facette_search_compare_element="
	<td class='compare_element'>!!compare_element_libelle!!</td>
";

$facette_search_compare_hidden_line="
	<tr class='compare_hidden_line' id='!!compare_hidden_line_id!!' style='display:none'>
		<td class='first_collumn'>&nbsp;</td>
		!!compare_hidden_line_elements!!
	</tr>
";

$facette_search_compare_hidden_element="
	<td class='compare_hidden_element'>
		<ul>
			!!compare_hidden_element_libelle!!
		</ul>
		!!compare_hidden_line_see_more!!
	</td>
";











