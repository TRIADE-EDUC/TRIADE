<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: view_log.inc.php,v 1.14 2023/11/30 10:43:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $logfile, $del;

function parseentry($str) {
    $tab= array();
    $regs= array();
	preg_match('/<datetime>(.+?)<\/datetime>/msi', $str, $regs);
	$tab['datetime']= $regs[1];
	preg_match('/<errornum>(.+?)<\/errornum>/msi', $str, $regs);
	$tab['errornum']= $regs[1];
	preg_match('/<errortype>(.+?)<\/errortype>/msi', $str, $regs);
	$tab['errortype']=$regs[1];
	preg_match('/<errormsg>(.+?)<\/errormsg>/msi', $str, $regs);
	$tab['errormsg']=$regs[1];
	preg_match('/<scriptname>(.+?)<\/scriptname>/msi', $str, $regs);
	$tab['scriptname']=$regs[1];
	preg_match('/<scriptlinenum>(.+?)<\/scriptlinenum>/msi', $str, $regs);
	$tab['scriptlinenum']=$regs[1];
	return $tab;
}

function parselog($str) {

	$regs = array();
  if(!strlen($str)) {
  	$array = array();
    $array[] = "";
    return $array;
  }

// premier niveau du parser : récupération des pattern <

  while(preg_match("/<errorentry>(.+?)<\/errorentry>/msi", $str, $regs)) {

    # on check s'il y a un pattern flml, si oui -> $tab[0]

	$pattern=$regs[0];
	$tab = array(parseentry($pattern));
	$del = preg_replace("/\//", "\/", quotemeta($pattern));
    $str = preg_replace("/$del/msi", "", $str);
  }

  return $tab;
}

print "<table >";
print "<tr><td class=\"formtitle\">";
print "Journal des événements";
print "</td></tr><tr><td>";

if(!$del) {
if($fp=fopen($logfile, 'r')) {
	$str=fread($fp, filesize($logfile));
	fclose($fp);

	// récupération du tableau des entrées
	$entry = parselog($str);

	if (!empty($entry) && filesize($logfile) > 0) {
		$entry = array_reverse($entry);
		print "<a href='./admin.php?categ=log&del=1'>vider le journal</a>";
		print "<table style='border:0px; border-spacing:1px'>";
		foreach ($entry as $valeur) {

			switch($valeur['errornum']) {
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
				case E_ERROR;
					$evt_icon="<img src='".get_url_icon('alert.gif')."' class='align_left'>";
					$bgcolor='#ffffff';
					break;
				case E_PARSE:
				case E_COMPILE_WARNING:
				case E_CORE_WARNING:
				case E_USER_WARNING:
				case E_WARNING:
					$evt_icon="<img src='".get_url_icon('warning.gif')."' class='align_left'>";
					$bgcolor='#ffffff';
					break;
				case E_USER_NOTICE:
				case E_NOTICE:
					$evt_icon="<img src='".get_url_icon('info.gif')."' class='align_left'>";
					$bgcolor='#e0e0e0';
					break;
			}

			print "<tr><td style='vertical-align:top' bgcolor='$bgcolor'>$evt_icon";
				print '<b>'.$valeur['errortype'].'</b>&nbsp;';
			print $valeur['datetime'];
			print '<br />'.$valeur['errormsg'].'<br />';
			print '<b>script</b>&nbsp;:&nbsp;'.$valeur['scriptname'].'&nbsp;ligne&nbsp;';
			print $valeur['scriptlinenum'];
			print '</td><tr>';
		}
		print '</table>';
	} else {
		print "le fichier journal est vide";
	}
} else {
	print "impossible d'ouvrir le fichier journal";
}
} else {

	if($fp=fopen($logfile, 'w')) {
		fwrite($fp, '');
		fclose($fp);
		print "le fichier journal a été purgé";
	} else {
		print "impossible de purger le fichier journal";
	}
}
print "</td></tr></table>";
