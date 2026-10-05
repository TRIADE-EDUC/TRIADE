<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard.tpl.php,v 1.2 2019/05/27 13:06:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $dashboard_menu, $dashboard_layout, $dashboard_layout_end;

$dashboard_menu = "";

$dashboard_layout = "
<div id='conteneur' class='dashboard'>
<script type='text/javascript'>dojo.require('dojox.layout.ContentPane');</script>
$dashboard_menu
";

$dashboard_layout_end = '
</div>
';