<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_liste.inc.php,v 1.13 2023/12/28 08:50:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $iddemande;

$iddemande = intval($iddemande);

demandes_controller::proceed($iddemande);