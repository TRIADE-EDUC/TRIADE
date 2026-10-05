<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_result.tpl.php,v 1.24 2023/12/07 15:02:47 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $search_result_affiliate_lvl1;
global $search_extented_result_affiliate_lvl1;
global $msg, $opac_rgaa_active;

// template for PMB OPAC
/*
$search_result_header= "<div><span>";

$search_result_footer ="</span></div>";

*/

if ($opac_rgaa_active) {
    $search_result_affiliate_lvl1_btn = "
    	let button = document.createElement('button');
    	button.setAttribute('type', 'button');
    	button.classList.add('search_result');
    	button.innerHTML = \"".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' />\";

    	button.addEventListener('click', function() {
    		document.!!form_name!!.action='./index.php?lvl=more_results&tab=affiliate';
    		document.!!form_name!!.submit();
    		return false;
    	}, true);

    	div.appendChild(button);
    ";
    $search_extented_result_affiliate_lvl1_btn = "
    	let button = document.createElement('button');
		button.setAttribute('type', 'button');
		button.classList.add('search_extented_result');
		button.innerHTML = \"".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' />\";

		button.addEventListener('click',function(){
			document.!!form_name!!.action='./index.php?lvl=more_results&mode=extended&tab=affiliate';
			document.!!form_name!!.submit();
			return false;
		},true);

		div.appendChild(button);
    ";
} else {
    $search_result_affiliate_lvl1_btn = "
        var a = document.createElement('a');
    	a.setAttribute('href','#');
    	a.innerHTML = \"".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' />\";

		a.addEventListener('click',function(){
			document.!!form_name!!.action='./index.php?lvl=more_results&tab=affiliate';
			document.!!form_name!!.submit();
			return false;
		},true);

    	div.appendChild(a);
    ";

    $search_extented_result_affiliate_lvl1_btn = "
        var a = document.createElement('a');
		a.setAttribute('href','#');
		a.innerHTML = \"".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' />\";

		a.addEventListener('click',function(){
			document.!!form_name!!.action='./index.php?lvl=more_results&mode=extended&tab=affiliate';
			document.!!form_name!!.submit();
			return false;
		},true);

		div.appendChild(a);
    ";
}




/*
 * template search lvl1 recherche affiliée
 *  !!mode!! : mode de recherche
 *  !!search_type!! : type de recherche
 *  !!label!! : libellé du mode de recherche
 * 	!!style!! : style du block (utile pour masquer ou non le block...)
 *  !!link!! lien en fonction du nombre de résultat affiliés
 *  !!user_query!! : query cherché
 *  !!nb_results!! : nombre de résultats dans le catalogue
 *  !!form_name!! : nom du formulaire à soumettre...
 *  !!form!! : le formulaire à soumettre...
 */
$search_result_affiliate_lvl1 = "
<div id='!!mode!!_result' !!style!!>
	<strong>!!label!!</strong>
	<blockquote role='presentation' id='!!mode!!_result_blockquote'>
		<div id='!!mode!!_results_in_catalog'>
			<strong>".$msg['in_catalog']."</strong> !!nb_result!! ".$msg['results']."
			!!link!!
		</div>
		<div id='!!mode!!_results_affiliate'>
			<strong>".$msg['in_affiliate_source']."</strong><img src='".get_url_icon('patience.gif')."' />
		</div>
		<script>
			var !!mode!!_search = new http_request();
			!!mode!!_search.request('./ajax.php?module=ajax&categ=search',true,'&type=!!mode!!&search_type=!!search_type!!&user_query=!!user_query!!',true,!!mode!!Results);
			function !!mode!!Results(response){
				var rep = eval('('+response+')');
				var div = document.getElementById('!!mode!!_results_affiliate');
				div.innerHTML='';
				var strong = document.createElement('strong');
				strong.innerHTML = \"".$msg['in_affiliate_source']."\";
				div.appendChild(strong);
				var text_node = document.createTextNode(' '+((rep.nb_results.total || rep.nb_results.total === 0) ? rep.nb_results.total : rep.nb_results) + ' '+pmbDojo.messages.getMessage('search', 'results')+' ');
				div.appendChild(text_node);
				if (rep.nb_results>0 || rep.nb_results.total > 0) {
					". $search_result_affiliate_lvl1_btn ."
					document.getElementById('!!mode!!_result').style.display = 'block';
				}
			}
		</script>
	</blockquote>
	<div class='search_result'>
		!!form!!
	</div>
</div>";

$search_extented_result_affiliate_lvl1 = "
<div id='!!mode!!_result' !!style!!>
	<strong>!!label!!</strong>
	<blockquote role='presentation' id='!!mode!!_result_blockquote'>
		<div id='!!mode!!_results_in_catalog'>
			<strong>".$msg['in_catalog']."</strong> !!nb_result!! ".$msg['results']."
			!!link!!
		</div>
		<div id='!!mode!!_results_affiliate'>
			<strong>".$msg['in_affiliate_source']."</strong><img src='".get_url_icon('patience.gif')."' />
		</div>
		<script>
			var !!mode!!_search = new http_request();
			!!mode!!_search.request('./ajax.php?module=ajax&categ=search',true,'&type=!!mode!!&search_type=!!search_type!!&user_query=!!user_query!!',true,!!mode!!Results);
			function !!mode!!Results(response){
				var rep = eval('('+response+')');
				var div = document.getElementById('!!mode!!_results_affiliate');
				div.innerHTML='';
				var strong = document.createElement('strong');
				strong.innerHTML = \"".$msg['in_affiliate_source']."\";
				div.appendChild(strong);
				var text_node = document.createTextNode(' '+(rep.nb_results.total ? rep.nb_results.total : rep.nb_results) + ' '+pmbDojo.messages.getMessage('search', 'results')+' ');
				div.appendChild(text_node);
				if (rep.nb_results > 0 || rep.nb_results.total > 0) {
				    ". $search_extented_result_affiliate_lvl1_btn ."
					document.getElementById('!!mode!!_result').style.display = 'block';
				}
			}
		</script>
	</blockquote>
	<div class='search_result'>
		!!form!!
	</div>
</div>";