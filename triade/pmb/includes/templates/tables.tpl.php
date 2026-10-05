<?php

// +-------------------------------------------------+
// | PMB                                                                      |
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tables.tpl.php,v 1.8 2019/05/27 14:04:23 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $container, $msg;

$container='
<h1>'.$msg["sauv_tables_titre"].'</h1>
<table class="nobrd"><tr>
<td style="vertical-align:top; width:30%">!!tables_tree!!</td>
<td>
!!tables_form!!
</td>
</tr></table>';
?>