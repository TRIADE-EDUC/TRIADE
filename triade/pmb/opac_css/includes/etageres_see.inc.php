<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etageres_see.inc.php,v 1.14 2023/08/02 06:21:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg, $showet, $liens_opac;
global $opac_etagere_nbnotices_accueil, $opac_etagere_notices_format, $opac_etagere_notices_depliables;

// affichage du contenu d'une étagère
if ($showet) {
	print pmb_bidi(affiche_etagere (0, "$showet", 1, $opac_etagere_nbnotices_accueil, $opac_etagere_notices_format, $opac_etagere_notices_depliables, "./index.php?lvl=etagere_see&id=!!id!!", $liens_opac )) ;
} else {
	print common::format_title($msg['accueil_etageres_virtuelles']);
	print pmb_bidi(affiche_etagere (0, "", 1, $opac_etagere_nbnotices_accueil, $opac_etagere_notices_format, $opac_etagere_notices_depliables, "./index.php?lvl=etagere_see&id=!!id!!", $liens_opac )) ; 
	print "\n";
}