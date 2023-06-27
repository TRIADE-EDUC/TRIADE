<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre-resa_planning.inc.php,v 1.4 2015-04-24 14:20:58 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// popup d'impression PDF pour lettre de confirmation de résa
/* reçoit : liste d'id_resa séparés par des , */
// la formule de politesse du bas (le signataire)
eval("\$fdp=\"".$pdflettreresa_fdp."\";");

// le texte après la liste des ouvrages en résa
eval("\$after_list=\"".$pdflettreresa_after_list."\";");

// la position verticale limite du texte after_liste (si >, saut de page et impression)
$limite_after_list=$pdflettreresa_limite_after_list;

// le texte avant la liste des ouvrges en réservation
eval ("\$before_list=\"".$pdflettreresa_before_list."\";");

// le "Madame, Monsieur," ou tout autre truc du genre "Cher adhérent,"
eval("\$madame_monsieur=\"".$pdflettreresa_madame_monsieur."\";");

// le nombre de blocs notices à imprimer sur la première page
$nb_1ere_page = $pdflettreresa_nb_1ere_page;

// le nombre de blocs notices à imprimer sur les pages suivantes
$nb_par_page = $pdflettreresa_nb_par_page;

// la taille d'un bloc notices
$taille_bloc_expl = $pdflettreresa_taille_bloc_expl;

// la position verticale du premier bloc notice sur la première page
$debut_expl_1er_page = $pdflettreresa_debut_expl_1er_page;

// la position verticale du premier bloc notice sur les pages suivantes
$debut_expl_page = $pdflettreresa_debut_expl_page;

// la marge gauche des pages
$marge_page_gauche = $pdflettreresa_marge_page_gauche;

// la marge droite des pages
$marge_page_droite = $pdflettreresa_marge_page_droite;

// la largeur des pages
$largeur_page = $pdflettreresa_largeur_page;

// la hauteur des pages
$hauteur_page = $pdflettreresa_hauteur_page;

// le format des pages
$format_page = $pdflettreresa_format_page;

$taille_doc=array($largeur_page,$hauteur_page);

$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
$ourPDF->Open();

switch($pdfdoc) {
	case "lettre_resa_planning" :
	default :
		// chercher id_empr validé
		$q = "select distinct (resa_idempr) from resa_planning where id_resa in (".addslashes($id_resa).") and resa_validee=1 ";
		$r = pmb_mysql_query($q, $dbh) ;
		while($o=pmb_mysql_fetch_object($r)) {
			lettre_resa_planning_par_lecteur($o->resa_idempr) ;
		}
		$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
		break;
	}

if ($probleme) {
	echo "<script type='text/javascript'> self.close(); </script>" ;
}else {
	$ourPDF->OutPut();
}
