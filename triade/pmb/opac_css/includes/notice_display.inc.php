<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_display.inc.php,v 1.91.2.1 2024/07/25 09:08:47 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $notice_display_header, $notice_display_footer, $ref, $show_bull, $opac_url_base, $opac_notices_format;

require_once($base_path.'/includes/templates/notice_display.tpl.php');
require_once($base_path.'/includes/explnum.inc.php');
require_once($base_path.'/classes/notice_affichage.class.php');
require_once($base_path.'/includes/bul_list_func.inc.php');
require_once($base_path.'/classes/upload_folder.class.php');
require_once($base_path.'/classes/record_display.class.php');
require_once($base_path.'/classes/notice_display.class.php');

print $notice_display_header;
if (!empty($ref)) {
	$EAN = '';
	$isbn = '';
	$code = '';

	if(isEAN($ref)) {
		// la saisie est un EAN -> on tente de le formater en ISBN
		$EAN=$ref;
		$isbn = EANtoISBN($ref);
		// si échec, on prend l'EAN comme il vient
		if(!$isbn) {
			$code = str_replace("*","%",$ref);
		}
		else {
			$code=$isbn;
			$code10=formatISBN($code,10);
		}
	} else {
		if(isISBN($ref)) {
			// si la saisie est un ISBN
			$isbn = formatISBN($ref);
			// si échec, ISBN erroné on le prend sous cette forme
			if(!$isbn)
				$code = str_replace("*","%",$ref);
			else {
				$code10=$isbn ;
				$code=formatISBN($code10,13);
			}
		} else {
			// ce n'est rien de tout ça, on prend la saisie telle quelle
			$code = str_replace("*","%",$ref);
		}
	}

	if ($EAN && $isbn) {
		// cas des EAN purs : constitution de la requête
		$requete = "SELECT notice_id FROM notices  where code in ('$code','$EAN'".($code10?",'$code10'":"").") limit 1";
	} elseif ($isbn) {
		// recherche d'un isbn
		$requete = "SELECT notice_id FROM notices where code in ('$code'".($code10?",'$code10'":"").") limit 1";
	} elseif ($code) {
		$requete = "SELECT notice_id FROM notices where code like '$code' limit 1";
	}
	$res = pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($res)) {
		$id=pmb_mysql_result($res,0,0);
	}
}

$id = intval($id);

if(isset($show_bull) && $show_bull && $id != 0){
	$query = "select bulletin_id from bulletins where num_notice = ".$id;
	$result = pmb_mysql_query($query);
	if(pmb_mysql_num_rows($result)>0){
		header("Location:".$opac_url_base."index.php?lvl=bulletin_display&id=".(pmb_mysql_result($result,0,0)*1));
	}
}

if($opac_notices_format != 9){
    // h1 a gerer dans les templates django
    print common::format_hidden_title(notice::get_field($id, 'tit1'));
}
$notice_display = new notice_display($id);
$notice_display->proceed('records');

print $notice_display_footer;