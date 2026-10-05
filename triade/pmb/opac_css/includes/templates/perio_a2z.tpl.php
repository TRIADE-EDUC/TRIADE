<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perio_a2z.tpl.php,v 1.65.2.1.2.1 2025/06/04 07:05:11 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) {
    die("no access");
}

global $abt_actif, $page, $num_field_start, $numfield_end, $date_debut, $date_fin, $id, $msg, $include_path, $charset;

if (! isset($page)) {
    $page = '';
}
if (! isset($num_field_start)) {
    $num_field_start = '';
}
if (! isset($numfield_end)) {
    $numfield_end = '';
}
if (! isset($date_debut)) {
    $date_debut = '';
}
if (! isset($date_fin)) {
    $date_fin = '';
}
if (! isset($id)) {
    $id = 0;
}

global $onglet_a2z;
global $ongletSub_a2z;
global $ongletSubList_a2z;
global $a2z_perio;
global $a2z_tpl_ajax;
global $a2z_tpl;
global $a2z_bull_search;
global $opac_notice_enrichment;
global $opac_rgaa_active;

// ce fichier contient les templates utile à la navigation des pério A to Z


$ongletSub_a2z="
	<div id='ongletSub_!!onglet_num!!' style='display:!!ongletSub_display!!'>
		<ul class='onglets_isbd_public'>
		!!ongletSub_list!!
		</ul>
	</div>";

if($opac_rgaa_active){
    $onglet_a2z = "
		<li id='onglet_!!onglet_num!!' class='!!onglet_class!!'>
			<button type='button' class='onglet' onclick=\"show_onglet('!!onglet_num!!_1'); return false;\"  aria-controls='a2z_contens' aria-pressed='!!onglet_btn_pressed!!' >!!onglet_label!!</button>
		</li>";
    
    $ongletSubList_a2z = "
		<li id='ongletSub_!!onglet_num!!_!!ongletSub_num!!' class='isbd_public_inactive'>
			<button class='sub-onglet' type='button' onclick=\"show_onglet('!!onglet_num!!_!!ongletSub_num!!'); return false;\" aria-controls='a2z_contens' aria-pressed='false'>!!ongletSub_label!!</button>
		</li>";
    $a2z_perio ="
		<li class='!!perio_active!!'>
			<button class='a2z_perio_button perio-title' type='button' onclick=\"reset_fields();show_perio('!!id!!', this); return false;\" aria-controls='a2z_perio' aria-pressed='!!perio_btn_pressed!!'>
			!!abt_actif!!
			!!perio_title!!
			</button>
		</li>";
    $a2z_tpl_ajax ="
		<div class='a2z_contens'>
			<ul class='a2z_perio_list'>
				!!a2z_perio_list!!
			</ul>
			<div id='a2z_perio'>!!perio_display!!</div>
		</div>";
}else{
    $onglet_a2z = "
		<li id='onglet_!!onglet_num!!' class='!!onglet_class!!'>
			<a role='button' class='onglet' href='#' onclick=\"show_onglet('!!onglet_num!!_1'); return false;\" aria-controls='a2z_contens' aria-pressed='!!onglet_btn_pressed!!'>!!onglet_label!!</a>
		</li>";
    $ongletSubList_a2z = "
		<li id='ongletSub_!!onglet_num!!_!!ongletSub_num!!' class='isbd_public_inactive'>
			<a role='button' href='#' class='sub-onglet' onclick=\"show_onglet('!!onglet_num!!_!!ongletSub_num!!'); return false;\" aria-controls='a2z_contens' aria-pressed='false'>!!ongletSub_label!!</a>
		</li>";
    $a2z_perio ="
		<tr><td class='!!perio_active!!'>!!abt_actif!!</td><td><a role='button' class='a2z_perio_button perio-title' href='#' onclick=\"reset_fields();show_perio('!!id!!', this); return false;\" aria-controls='a2z_perio' aria-pressed='!!perio_btn_pressed!!'>!!perio_title!!</a></td></tr>
	";
    $a2z_tpl_ajax ="
		<table class='a2z_contens' role='presentation'>
			<tr>
				<td style='vertical-align:top'>
					<div class='a2z_perio_list'>
						<table class='a2z_perio_list' role='presentation'>!!a2z_perio_list!!</table>
					</div>
				</td>
				<td class='a2z_perio'><div id='a2z_perio'>!!perio_display!!</div></td>
			</tr>
		</table>";
}

$a2z_tpl="
<div id='perio_a2z-container'>
	<script type='text/javascript' src='" . $include_path . "/javascript/ajax.js'></script>
	<script type='text/javascript'>
	    
		var memo_onglet=new Array();
	    
		function reset_fields(){
			if(document.getElementById('bull_date_start')) document.getElementById('bull_date_start').value='';
			if(document.getElementById('bull_date_end'))document.getElementById('bull_date_end').value='';
			if(document.getElementById('bull_num_deb'))document.getElementById('bull_num_deb').value='';
			if(document.getElementById('bull_num_end'))document.getElementById('bull_num_end').value='';
			if(document.getElementById('bull_page')) {
                document.getElementById('bull_page').value=1;
            }
			if(document.getElementById('bull_nb_per_page')) {
                document.getElementById('bull_nb_per_page').value='12';
            }
			document.getElementById('perio_a2z_search').value='';//On efface le champ de saisie
		}
	    
		function show_perio(perio_id, element) {
			var bull_date_start='';
			var bull_date_end='';
			var bull_num_deb='';
			var bull_num_end='';
			var page='';
            var nb_per_page_custom=0;
			var location='';
	    
			if(document.getElementById('bull_date_start')){
				bull_date_start = document.getElementById('bull_date_start').value;
			}
			if(document.getElementById('bull_date_end')){
				bull_date_end = document.getElementById('bull_date_end').value;
			}
			if(document.getElementById('bull_num_deb')) {
                bull_num_deb = document.getElementById('bull_num_deb').value;
            }
			if(document.getElementById('bull_num_end')) {
                bull_num_end = document.getElementById('bull_num_end').value;
            }
			if(document.getElementById('bull_page')) {
                page = document.getElementById('bull_page').value;
            }
			if(document.getElementById('bull_nb_per_page')) {
                nb_per_page_custom = document.getElementById('bull_nb_per_page').value;
            }
			if(document.getElementById('location')) location = document.getElementById('location').value;
	    
			var url= './ajax.php?module=ajax&categ=perio_a2z&sub=get_perio&id=' + perio_id;
			url+='&bull_date_start='+bull_date_start;
			url+='&bull_date_end='+bull_date_end;
			url+='&bull_num_deb='+bull_num_deb;
			url+='&bull_num_end='+bull_num_end;
			url+='&page='+page;
            if (nb_per_page_custom) {
                url+='&nb_per_page_custom='+nb_per_page_custom;
            }
			url+='&location='+location;
	    
			if(typeof(dojo) == 'object' && dojo.byId('form_search_bull')){
				dojo.forEach(dijit.findWidgets(dojo.byId('form_search_bull')), function(w) {
					console.log('destroyRecursive');
					w.destroyRecursive(true);
				});
	    
			}
			var id = document.getElementById('a2z_perio');
			id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto; border:0px;\" src=\"".get_url_icon('patience.gif')."\" id=\"collapseall\"><\/div>' ;
			    
			// On initialise la classe:
			var req = new http_request();
			// Execution de la requette
			if(req.request(url)) return 0;
			    
			// contenu
			id.innerHTML = req.get_text();
			var tags = id.getElementsByTagName('script');
			if(!id.getElementsByTagName('script').length) return 1;
			    
       		for(var i=0;i<tags.length;i++){
                window.eval(tags[i].text);
        	}
			    
			" . ($opac_notice_enrichment ? "
				if( id.children[0] && id.children[0].getAttribute('id')){
					var record_id = id.children[0].getAttribute('id').replace(/[^0-9]*/ig,'');
					if(!record_id) {
						record_id = id.children[0].getAttribute('data-record-id');
					}
					getEnrichment(record_id);
				}" : "") . "
				    
			if(typeof(dojo) == 'object'){
				dojo.parser.parse(id);
			}
				    
			var perioButtons = document.querySelectorAll('.a2z_perio_list .a2z_perio_button');
			if(perioButtons){
				perioButtons.forEach(btn => {
						btn.setAttribute('aria-pressed', 'false');
						btn.parentNode.classList.add('perio_inactive');
						btn.parentNode.classList.remove('perio_active');
					}
				);
			}
			if(element){
				element.setAttribute('aria-pressed', 'true');
				element.parentNode.classList.add('perio_active');
				element.parentNode.classList.remove('perio_inactive');
			}
				    
				    
			return 1;
		}
				    
		function show_onglet(onglet) {
				    
			var myArray = onglet.split('_');
			var onglet_num = myArray[0];
			var ongletSub_num = myArray[1];
				    
			// contenu
			var id = document.getElementById('a2z_contens');
				    
			if(typeof(dojo) == 'object' && dojo.byId('form_search_bull')){
				dojo.forEach(dijit.findWidgets(dojo.byId('form_search_bull')), function(w) {
					console.log('destroyRecursive');
					w.destroyRecursive(true);
				});
			}
			if(!memo_onglet[onglet]){
				    
				var location = document.getElementById('location').value;
				var surlocation = document.getElementById('surloc').value;
				var filtre_select;
				if(document.getElementById('filtre_select')) filtre_select = document.getElementById('filtre_select').value;
				var abt_actif=0;
				if(document.getElementById('a2z_abt_actif').checked == true)abt_actif=1;
				id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto; border:0px;\" src=\"" . get_url_icon('patience.gif') . "\" id=\"collapseall\"><\/div>';
				var url= './ajax.php?module=ajax&categ=perio_a2z&sub=get_onglet&onglet_sel=' + onglet;
				url+='&location='+location;
				url+='&surloc='+surlocation;
				url+='&abt_actif='+abt_actif;
				url+='&filtre_select='+filtre_select;
				// On initialise la classe:
				var req = new http_request();
				// Execution de la requette
				if(req.request(url))return 0;
				memo_onglet[onglet]= req.get_text();
			}
				    
			id.innerHTML = memo_onglet[onglet];
			var div_tags = document.getElementById('a2z_perio').getElementsByTagName('div');
			if(div_tags[0] && div_tags[0].getAttribute('id')){
				" . ($opac_notice_enrichment ? "
					var record_id = div_tags[0].getAttribute('id').replace(/[^0-9]*/ig,'');
					if(!record_id) {
						record_id = div_tags[0].getAttribute('data-record-id');
					}
					getEnrichment(record_id);" : "") . "
			}
					    
			var i=1;
			while(myOnglet=document.getElementById('onglet_' + i)){
				myOnglet.setAttribute('class', 'isbd_public_inactive');
				myOnglet.querySelector(':scope > *').setAttribute('aria-pressed', 'false');
				i++;
			}
			if(document.getElementById('onglet_'+onglet_num)){
				document.getElementById('onglet_'+onglet_num).setAttribute('class', 'isbd_public_active');
				document.querySelector('#onglet_'+onglet_num+' > *[aria-pressed]').setAttribute('aria-pressed', 'true');
			}
					    
			i=1;
			while(myOngletSub=document.getElementById('ongletSub_' + i)){
				myOngletSub.setAttribute('style', 'display:none');
				i++;
			}
			if(document.getElementById('ongletSub_'+onglet_num)){
				document.getElementById('ongletSub_'+onglet_num).setAttribute('style', 'display:block');
			}
					    
			i=1;
			while(myOngletSubList=document.getElementById('ongletSub_' + onglet_num + '_' + i)){
				myOngletSubList.setAttribute('class', 'isbd_public_inactive');
				".($opac_rgaa_active ? "myOngletSubList.querySelector('*[aria-pressed]').setAttribute('aria-pressed', 'false');" : "").";
				i++;
			}
			//On est oblige de tester : certaines lettres n'ont pas de sous-liste
			var myList = document.getElementById('ongletSub_' + onglet_num + '_' + ongletSub_num);
			if(myList){
				myList.setAttribute('class', 'isbd_public_active');
				".($opac_rgaa_active ? "myList.querySelector('*[aria-pressed]').setAttribute('aria-pressed', 'true');" : "").";
			}
				    
			if(typeof(dojo) == 'object'){
				dojo.parser.parse(id);
			}
				    
			var perioButtons = document.querySelectorAll('.a2z_perio_list .a2z_perio_button');
			perioButtons.forEach(btn => {
					btn.setAttribute('aria-pressed', 'false');
					btn.parentNode.classList.add('perio_inactive');
					btn.parentNode.classList.remove('perio_active', '!!perio_active!!');
				}
			);
			perioButtons[0].setAttribute('aria-pressed', 'true');
			perioButtons[0].parentNode.classList.add('perio_active');
			perioButtons[0].parentNode.classList.remove('perio_inactive');
				    
			return 1;
		}
				    
		function search_change_onglet(id) {
			var elt=document.getElementById('perio_a2z_onglet').value;
			var onglperio=elt.split('.');
			show_onglet(onglperio[0]);
			reset_fields(); show_perio(onglperio[1]);
		}
				    
		function reload_all() {
			var location = document.getElementById('location').value;
			var surlocation = document.getElementById('surloc').value;
			var filtre_select;
			if(document.getElementById('filtre_select')) filtre_select = document.getElementById('filtre_select').value;
			var abt_actif = 0;
			if(document.getElementById('a2z_abt_actif').checked == true)abt_actif=1;
				    
			document.getElementById('perio_a2z_search').setAttribute('autexclude', abt_actif);
				    
			if(typeof(dojo) == 'object' && dojo.byId('form_search_bull')){
				dojo.forEach(dijit.findWidgets(dojo.byId('form_search_bull')), function(w) {
					console.log('destroyRecursive');
					w.destroyRecursive(true);
				});
			}
				    
			var id = document.getElementById('perio_a2z');
			id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto; border:0px;\" src=\"" . get_url_icon('patience.gif') . "\" id=\"collapseall\"><\/div>' ;
			    
			var url= './ajax.php?module=ajax&categ=perio_a2z&sub=reload'
			url+='&location='+location;
			url+='&surloc='+surlocation;
			url+='&abt_actif='+abt_actif;
			url+='&filtre_select='+filtre_select;
			    
			// On initialise la classe:
			var req = new http_request();
			// Execution de la requette
			if(req.request(url))return 0;
			    
			id.innerHTML = req.get_text();
			ajax_pack_element(document.getElementById('perio_a2z_search'));
			show_onglet('1_1');
		}
	</script>
	<label for='perio_a2z_search' class='visually-hidden'>" . htmlentities($msg["atoz_rgaa_perio"], ENT_QUOTES, $charset) . "</label>
    &nbsp;
    <input type='text' id='perio_a2z_search' name='perio_a2z_search' completion='perio_a2z' placeholder='" . htmlentities($msg["atoz_rechercher"], ENT_QUOTES, $charset) . "' autfield='perio_a2z_onglet' listfield='location,surloc' expand_mode='1' autexclude='" . (0 | $abt_actif) . "' callback='search_change_onglet'/>
	<input type='hidden' id='perio_a2z_onglet' value='' name='perio_a2z_onglet'/>
	<input type=\"hidden\" name=\"location\" id=\"location\" value=\"!!location!!\">\n
	<input type=\"hidden\" name=\"surloc\" id=\"surloc\" value=\"!!surlocation!!\">\n
	<div class='row'>";
if($opac_rgaa_active) {
    $a2z_tpl .= "<span id='span_a2z_abt_actif'><label for='a2z_abt_actif'>".$msg["a2z_abt_actif_filter"]."</label><input type='checkbox'  !!check_abt_actif!! name='a2z_abt_actif' id='a2z_abt_actif' onclick=\"memo_onglet=new Array(); reload_all(); \" ></span>";
} else {
    $a2z_tpl .= "<span id='span_a2z_abt_actif'>" . $msg["a2z_abt_actif_filter"] . "<input type='checkbox'  !!check_abt_actif!! name='a2z_abt_actif' id='a2z_abt_actif' onclick=\"memo_onglet=new Array(); reload_all(); \" ></span>";
    
}
$a2z_tpl .= "!!filtre!!
	</div>
	<div class='row'><ul class='onglets_isbd_public'>!!a2z_onglets_list!!</ul></div>
	<div class='row'>!!a2z_onglets_sublist!!</div>
	<div class='row'></div>
	<div id='a2z_contens'>
		$a2z_tpl_ajax
	</div>
	<script type='text/javascript'>ajax_parse_dom();</script>
</div>
<div class='row'></div>
";
		
		$a2z_bull_search="
	<script type='text/javascript' src='./includes/javascript/http_request.js'></script>
	<script type='text/javascript'>
		    
		function bull_search() {
			var url= './ajax.php?module=ajax&categ=perio_a2z&sub=get_perio&id=';
			var id = document.getElementById('a2z_perio');
			id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto; border:0px;\" src=\"" . get_url_icon('patience.gif') . "\" id=\"collapseall\"></div>' ;
			// On initialise la classe:
			var req = new http_request();
			// Execution de la requette
			if(req.request(url)){
				// Il y a une erreur. Afficher le message retourne
				alert ( req.get_text() );
			}else {
				// contenu
				id.innerHTML = req.get_text();
				return 1;
			}
		}
	</script>
	
	<a name='tab_bulletin'></a>
	<h3>$msg[perio_list_bulletins]</h3>
	<div id='form_search_bull'>
		<div class='row'></div>\n
			<script src='./includes/javascript/ajax.js'></script>
			<form name=\"form_values\" action=\"./index.php?lvl=notice_display&id=$id\" method=\"post\" onsubmit=\"if (document.getElementById('onglet_isbd$id').className=='isbd_public_active') document.form_values.premier.value='ISBD'; else document.form_values.premier.value='PUBLIC';document.form_values.page.value=1;\">\n
				<input type=\"hidden\" name=\"premier\" value=\"\">\n
				<input type=\"hidden\" name=\"page\" value=\"$page\">\n
				<table role='presentation'>
					<tr>
						<td class='align_left' rowspan='2'><strong>" . $msg["search_bull"] . "&nbsp;:&nbsp;</strong></td>
						<td class='align_right'><strong>" . $msg["search_per_bull_num"] . " : " . $msg["search_bull_start"] . "</strong></td>
						<td >$num_field_start</td>
						<td ><strong>" . $msg["search_bull_end"] . "</strong> $numfield_end</td>
					</tr>
					<tr>
						<td class='align_right'><strong>" . $msg["search_per_bull_date"] . " : " . $msg["search_bull_start"] . "</strong></td>
						<td>$date_debut</td>
						<td><strong>" . $msg["search_bull_end"] . "</strong> $date_fin</td>
						<td>&nbsp;&nbsp;<input type='button' class='boutonrechercher' value='" . $msg["142"] . "' onclick='submit();'></td>
					</tr>
				</table>
			</form>
		<div class='row'></div><br />
	</div>\n
";
		