<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pclass.tpl.php,v 1.12 2023/09/02 13:50:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// templates pour la gestion des plans de classements 

global $browser_pclassement;
global $msg;

//Template du browser
$browser_pclassement = "
<div class='row'>
	<h3>&nbsp;".$msg['pclassement_liste']."</h3>
</div>
<br />
<br />
<div class='row'>
	!!browser_content!!
</div>
";
		