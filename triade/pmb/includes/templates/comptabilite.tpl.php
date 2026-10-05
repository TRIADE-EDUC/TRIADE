<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: comptabilite.tpl.php,v 1.11 2023/09/02 07:08:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $charset, $ptab;

$ptab[2] = "&nbsp;&nbsp;<input type='checkbox' id='def' name='def' value='1' />".htmlentities($msg['acquisition_statut_ca_def'], ENT_QUOTES, $charset);

