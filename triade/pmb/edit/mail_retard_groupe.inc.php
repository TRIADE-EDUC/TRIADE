<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_retard_groupe.inc.php,v 1.14 2019-05-24 15:47:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if ($id_groupe) {
	
	$req = "select libelle_groupe from groupe where id_groupe='".$id_groupe."'";
	$res = pmb_mysql_query($req,$dbh);
	if ($res && pmb_mysql_num_rows($res)) {
		$row = pmb_mysql_fetch_object($res);
		$group_name = $row->libelle_groupe;
	}
	
	if (empty($relance)) $relance = 1;
	
	require_once($class_path."/mail/reader/loans/mail_reader_loans_late_group.class.php");
	mail_reader_loans_late_group::set_niveau_relance($relance);
	$mail_reader_loans_late_group = new mail_reader_loans_late_group();
	$mail_reader_loans_late_group->send_mail(0, $id_groupe);
}