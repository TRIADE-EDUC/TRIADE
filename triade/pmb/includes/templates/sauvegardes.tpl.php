<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sauvegardes.tpl.php,v 1.8 2019/05/27 10:24:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $container, $msg;

$container='
<h1>'.$msg["sauv_sauvegardes_titre"].'</h1>
<table class="nobrd"><tr>
<td style="vertical-align:top">!!sauvegardes_tree!!</td>
<td>
!!sauvegardes_form!!
</td>
</tr></table>';
?>