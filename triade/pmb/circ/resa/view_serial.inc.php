<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: view_serial.inc.php,v 1.15 2017-02-20 19:04:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page d'affichage des éléments bulletinés d'un périodique en recherche réservation

require_once("$class_path/serials.class.php");

$serial = new serial($serial_id);
echo "<h3>".$msg[1150]." : ".$serial->tit1."</h3>";

$requete = "select bulletin_id from bulletins join exemplaires on expl_bulletin=bulletin_id WHERE bulletin_notice=$serial_id group by bulletin_id ORDER BY bulletin_id DESC";
$res = pmb_mysql_query($requete, $dbh);

if(pmb_mysql_num_rows($res)) {

	print $begin_result_liste;
	while (($n=pmb_mysql_fetch_object($res))) {
		$link_bulletin = "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=".$n->bulletin_id;
		require_once ("$class_path/serials.class.php") ;
		require_once ("$include_path/bull_info.inc.php") ;
		$n->isbd = show_bulletinage_info_resa($n->bulletin_id, $link_bulletin);
		print $n->isbd ;
	}
	print $end_result_liste;

} else {
	error_message($msg[235], $msg['resa_no_expl'], 1, "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&mode=0");
}
