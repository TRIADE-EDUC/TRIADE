<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recouvr_reader.inc.php,v 1.15.8.1 2025/05/30 08:11:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $recouvr_id, $id_empr, $libelle, $montant;

//Affichage des recouvrements pour un lecteur

$libelle = (isset($libelle) ? stripslashes($libelle) : '');
$montant = (isset($montant) ? stripslashes($montant) : '');

print "<script src='./javascript/dynamic_element.js' type='text/javascript'></script>";

recouvr_reader_controller::set_id_empr($id_empr);
recouvr_reader_controller::proceed($recouvr_id);

print "
<script type='text/javascript'>parse_dynamic_elts();</script>
";
