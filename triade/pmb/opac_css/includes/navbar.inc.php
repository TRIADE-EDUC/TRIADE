<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: navbar.inc.php,v 1.26 2019-04-17 08:31:52 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path.'/includes/javascript/form.inc.php');

function printnavbar($page, $nbr_lignes, $nb_per_page, $url, $nb_per_page_custom_url='', $action='') {
	global $script_test_form;
	global $msg;
	global $opac_items_pagination_custom;
	global $cms_active;
	
	$page+= 0;
	$nbrpages = ceil($nbr_lignes/$nb_per_page);
	$precedente = $page-1;
	$suivante = $page+1;
	
	//distance avant et après page courante
	$distance = 5;
	$start = $page - $distance;
	if ($start<1) $start=1;
	$end = $page + $distance ;
	if($end>$nbrpages)$end=$nbrpages;
	
	// crée les tests de formulaire
	$script = $script_test_form;
	$script = str_replace("!!tests!!",
	                      test_field_value_comp('form', 'page', GREATER, $nbrpages, $msg["page_too_high"]) ."\n".
	                      test_field_value_comp('form', 'page', LESSER, 1, $msg["page_too_low"]),
	                      $script);
	$print = $script;
	// affichage de la barre de navigation
	$print .= "<div class=\"navbar\">\n";

	// on fait suivre les variables d'environnement du portail
	if($cms_active && strpos($url, 'javascript:') === false) {
		$query = "select distinct var_name from cms_vars";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$var_name = $row->var_name;
				global ${$var_name};
				if(!empty(${$var_name})) {
					$url .= "&".$var_name."=".${$var_name};
				}
			}
		}
	}
	
	$printurl = $url;
	$printurl = str_replace("&page=!!page!!", "", $printurl);
	$printurl = str_replace("page=!!page!!&", "", $printurl);
	$printurl = str_replace("page=!!page!!", "", $printurl);
	if($action) $printurl=$action;
	
	$print .= "<form name='form' action='$printurl' method='post' onsubmit='return test_form(form)'>\n";
	// first
	if ($page != 1)	{
		$printurl = str_replace("!!page!!", "1", $url);
		$print .= "<a class='navbar_first' href='$printurl'><img src='".get_url_icon('first.png')."' alt='".$msg["first_page"]."' style='border:0px' title='".$msg["first_page"]."'></a>\n";
	}else {
		$print .= "<img src='".get_url_icon('first-grey.png')."' alt='".$msg["first_page"]."'>\n";
	}
	// prev
	if ($precedente >= 1) {
		$printurl = str_replace("!!page!!", "$precedente", $url);
		$print .= "<a class='navbar_prev' href='$printurl'><img src='".get_url_icon('prev.png')."' alt='".$msg["prec_page"]."' style='border:0px' title='".$msg["prec_page"]."'></a>\n";
	}else {
		$print .= "<img src='".get_url_icon('prev-grey.png')."' alt='".$msg["prec_page"]."'>\n";
	}
	
	for ($i = $start; ($i <= $nbrpages) && ($i<=$page+$distance) ; $i++) {
		if($i==$page) {
			$print .= "<strong>".$i."</strong>";
		} else {
			$printurl = str_replace("!!page!!", "$i", $url);
			$print .= "<a class='navbar_page' href='".$printurl."' >".$i."</a>";
		}
		if($i<$nbrpages) $print .= " ";
	}
	
	// next
	if ($suivante <= $nbrpages) {
		$printurl = str_replace("!!page!!", "$suivante", $url);
		$print .= "<a class='navbar_next' href='$printurl'><img src='".get_url_icon('next.png')."' alt='".$msg["next_page"]."' style='border:0px' title='".$msg["next_page"]."'></a>\n";
	} else {
		$print .= "<img src='".get_url_icon('next-grey.png')."' alt='".$msg["next_page"]."'>\n";
	}
	// last
	if ($page != $nbrpages)	{
		$printurl = str_replace("!!page!!", "$nbrpages", $url);
		$print .= "<a class='navbar_last' href='$printurl'><img src='".get_url_icon('last.png')."' alt='".$msg["last_page"]."' style='border:0px' title='".$msg["last_page"]."'></a>\n";
	}else {
		$print .= "<img src='".get_url_icon('last-grey.png')."' alt='".$msg["last_page"]."'>\n";
	}
	
	$start_in_page = ((($page-1)*$nb_per_page)+1);
	if(($start_in_page + $nb_per_page) > $nbr_lignes) {
		$end_in_page = $nbr_lignes;
	} else {
		$end_in_page = ((($page-1)*$nb_per_page)+$nb_per_page);
	}
	$print .= " (".$start_in_page." - ".$end_in_page." / ".$nbr_lignes.")";
	
	if($opac_items_pagination_custom) {
		$pagination_custom = explode(',', $opac_items_pagination_custom);
		if(count($pagination_custom)) {
			$pagination_nav_bar = "";
			$max_nb_elements = 0;
			$printurl = str_replace("!!page!!", "1", $url);
			if(!$nb_per_page_custom_url) {
				$nb_per_page_custom_url = "&nb_per_page_custom=!!nb_per_page_custom!!";
			}
			foreach ($pagination_custom as $nb_elements) {
				$nb_elements = trim($nb_elements)+0;
				if($nb_elements < $nbr_lignes) {
					if($nb_elements == $nb_per_page) $pagination_nav_bar .= "<b>";
					if(strpos($printurl, 'javascript:') !== false) {
						$pagination_nav_bar .= " <a class='navbar_custom' href='".str_replace('!!nb_per_page_custom!!', $nb_elements, $nb_per_page_custom_url).";".$printurl."'>".$nb_elements."</a> ";
					} else {
						$pagination_nav_bar .= " <a class='navbar_custom' href='".$printurl.str_replace('!!nb_per_page_custom!!', $nb_elements, $nb_per_page_custom_url)."'>".$nb_elements."</a> ";
					}
					if($nb_elements == $nb_per_page) $pagination_nav_bar .= "</b>";
				}
				if($nb_elements > $max_nb_elements) {
					$max_nb_elements = $nb_elements;
				}
			}
			if(($max_nb_elements > $nbr_lignes) && ($nb_per_page < $nbr_lignes)) {
				if(strpos($printurl, 'javascript:') !== false) {
					$pagination_nav_bar .= " <a class='navbar_custom' href='".str_replace('!!nb_per_page_custom!!', $nbr_lignes, $nb_per_page_custom_url).";".$printurl."'>".$msg['display_all']."</a> ";
				} else {
					$pagination_nav_bar .= " <a class='navbar_custom' href='".$printurl.str_replace('!!nb_per_page_custom!!', $nbr_lignes, $nb_per_page_custom_url)."'>".$msg['display_all']."</a> ";
				}
			}
			if($pagination_nav_bar) {
				$pagination_nav_bar = "<span style='float:right;'> ".$msg['per_page']." ".$pagination_nav_bar."</span>";
			}
			$print .= $pagination_nav_bar;
		}
	}
	$print .= "</form>\n";
	$print .= "</div>\n";
	return $print;
}


function printnavbar_onclick($page, $nbrpages, $url,$action='') {
	global $script_test_form;
	global $msg;
	
	$page+= 0;
	$precedente = $page-1;
	$suivante = $page+1;


	// affichage de la barre de navigation
	$print = "<div class=\"navbar\">\n";
	
	$print .= "<form name='form' onsubmit=\"document.getElementById('page').value=document.getElementById('navbar_input_page').value;$action\">\n";
	// first
	if ($page != 1)	{
		$print .= "<img src='".get_url_icon('first.png')."' alt='".$msg["first_page"]."' style='border:0px' title='".$msg["first_page"]."' onclick=\"document.getElementById('page').value=1;$action\">\n";
	}else {
		$print .= "<img src='".get_url_icon('first-grey.png')."' alt='".$msg["first_page"]."'>\n";
	}
	// prev
	if ($precedente >= 1) {
		$printurl = str_replace("!!page!!", "$precedente", $url);
		$print .= "<img src='".get_url_icon('prev.png')."' alt='".$msg["prec_page"]."' style='border:0px' title='".$msg["prec_page"]."'  onclick=\"document.getElementById('page').value=$precedente;$action\">\n";
	}else {
		$print .= "<img src='".get_url_icon('prev-grey.png')."' alt='".$msg["prec_page"]."'>\n";
	}
	// page courante
	if ($nbrpages > 1) {
		$print .= "page <input type='text' class='numero_page' name='page' id='navbar_input_page' value='$page' size='".strlen("$nbrpages")."'>/$nbrpages\n";
	}else {
		$print .= "page $page/$nbrpages\n";
	}
	// next
	if ($suivante <= $nbrpages) {
	
		$print .= "<img src='".get_url_icon('next.png')."' alt='".$msg["next_page"]."' style='border:0px' title='".$msg["next_page"]."' onclick=\"document.getElementById('page').value=$suivante;$action\">\n";
	} else {
		$print .= "<img src='".get_url_icon('next-grey.png')."' alt='".$msg["next_page"]."'>\n";
	}
	// last
	if ($page != $nbrpages)	{
		$print .= "<img src='".get_url_icon('last.png')."' alt='".$msg["last_page"]."' style='border:0px' title='".$msg["last_page"]."' onclick=\"document.getElementById('page').value=$nbrpages;$action\">\n";
	}else {
		$print .= "<img src='".get_url_icon('last-grey.png')."' alt='".$msg["last_page"]."'>\n";
	}
	$print .= "</form>\n";
	$print .= "</div>\n";
	return $print;
}
