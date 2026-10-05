<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_reset_mfa.inc.php,v 1.1 2023/07/18 08:51:41 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id;

$query = "UPDATE empr SET mfa_secret_code = NULL, mfa_favorite = NULL WHERE id_empr = " . intval($id);
pmb_mysql_query($query);

header('Location: ./circ.php?categ=pret&id_empr=' . $id);
exit();


