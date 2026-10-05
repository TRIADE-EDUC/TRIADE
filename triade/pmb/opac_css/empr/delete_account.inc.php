<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: delete_account.inc.php,v 1.3 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id_empr, $empr_opac_account_deleted_status;

print "<span class='alerte'>" . htmlentities($msg['delete_account_running'], ENT_QUOTES, $charset) . "</span>";
$empr = new emprunteur($id_empr);
$empr->update_empr_status($empr_opac_account_deleted_status);
print '<script>window.location = "./index.php?logout=1";</script>';