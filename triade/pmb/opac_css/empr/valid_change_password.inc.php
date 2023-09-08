<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: valid_change_password.inc.php,v 1.15 2018-07-24 13:23:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/emprunteur.class.php");

if (!$allow_pwd) die();
print "
<div id='change-password'>
<div id='change-password-container'>";
// contrôle de l'ancien mot de passe ok
if ($new_password==$confirm_new_password) {
	emprunteur::hash_password($empr_login,$new_password);
	// contrôle du nouveau mot de passe par double ok
	// donc tout baigne, on lance la màj
	print $msg["empr_password_changed"]."<br /><br />";
} else {
	// contrôle du nouveau mot de passe par double non validé
	print $msg["empr_password_does_not_match"]."<br /><br />";
}
print "
</div>
</div>";