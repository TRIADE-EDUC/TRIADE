<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: history_functions.inc.php,v 1.9 2024/04/19 12:32:09 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// ----------------------------------------------------------------------------
//	fonctions de suppression en cascade de l'historique de recherches
// ----------------------------------------------------------------------------


function suppr_histo($id_suppr, $tableau_suppr)
{
	if (!isset($tableau_suppr[$id_suppr]) || !$tableau_suppr[$id_suppr]) {
		for ($i = 1; $i <= $_SESSION["nb_queries"]; $i++) {
			if ($_SESSION["search_type" . $i] == "extended_search") {
				get_history($i);
				global $search;
				$bool = false;
				for ($j = 0; $j < count($search); $j++) {
					if ($search[$j] == "s_1") {
						$field_ = "field_0_" . $search[$j];
						global ${$field_};
						$field = ${$field_};
						if ($field[0] == $id_suppr) {
							$tableau_suppr[$i] = 0;
							suppr_histo($i, $tableau_suppr);
							$bool = true;
						}
					}
				}
				if ($bool == false) {
					$tableau_suppr[$i] = 1;
				}
			} else {
				$tableau_suppr[$i] = 1;
			}
		}
		$tableau_suppr[$id_suppr] = 0;
	}
	return $tableau_suppr;
}

function reorg_tableau_suppr($tableau_suppr)
{
	$t = array();
	$j = 1;
	for ($i = 1; $i <= count($tableau_suppr); $i++) {
		if ($tableau_suppr[$i] != 0) {
			$t[$i] = $j;
			$j++;
		} else {
			switch ($_SESSION["search_type" . (string)$i]) {
				case "search_universes":
					unset($_SESSION["search_universes" . (string)$i]);
					break;
				case "ai_search":
					unset($_SESSION["ai_search_history_" . (string)$i]);
					break;
				default:
					unset($_SESSION["human_query" . (string)$i]);
					unset($_SESSION["notice_view" . (string)$i]);
					unset($_SESSION["search_type" . (string)$i]);
					unset($_SESSION["user_query" . (string)$i]);
					unset($_SESSION["map_emprises_query" . (string)$i]);
					unset($_SESSION["typdoc" . (string)$i]);
					unset($_SESSION["look_TITLE" . (string)$i]);
					unset($_SESSION["look_AUTHOR" . (string)$i]);
					unset($_SESSION["look_PUBLISHER" . (string)$i]);
					unset($_SESSION["look_TITRE_UNIFORME" . (string)$i]);
					unset($_SESSION["look_COLLECTION" . (string)$i]);
					unset($_SESSION["look_SUBCOLLECTION" . (string)$i]);
					unset($_SESSION["look_CATEGORY" . (string)$i]);
					unset($_SESSION["look_INDEXINT" . (string)$i]);
					unset($_SESSION["look_KEYWORDS" . (string)$i]);
					unset($_SESSION["look_ABSTRACT" . (string)$i]);
					unset($_SESSION["look_CONCEPT" . (string)$i]);
					unset($_SESSION["look_CONTENT" . (string)$i]);
					unset($_SESSION["look_ALL" . (string)$i]);
					unset($_SESSION["l_typdoc" . (string)$i]);
					break;
			}
		}
	}
	return $t;
}
