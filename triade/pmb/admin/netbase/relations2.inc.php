<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relations2.inc.php,v 1.20 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $start, $v_state, $spec;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_clean_relations_cat"]);

pmb_mysql_query("delete from notices_custom_values where notices_custom_champ not in (select idchamp from notices_custom)");
$affected = pmb_mysql_affected_rows();
pmb_mysql_query("delete from expl_custom_values where expl_custom_champ not in (select idchamp from expl_custom)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE empr_custom_values FROM empr_custom_values LEFT JOIN empr ON id_empr=empr_custom_origine WHERE id_empr IS NULL ");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("delete from empr_custom_values where empr_custom_champ not in (select idchamp from empr_custom)");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("delete from notices_custom_dates where notices_custom_champ not in (select idchamp from notices_custom)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("delete from expl_custom_dates where expl_custom_champ not in (select idchamp from expl_custom)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE empr_custom_dates FROM empr_custom_dates LEFT JOIN empr ON id_empr=empr_custom_origine WHERE id_empr IS NULL ");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("delete from empr_custom_dates where empr_custom_champ not in (select idchamp from empr_custom)");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("DELETE from tu_custom_values where tu_custom_origine not in (select tu_id from titres_uniformes)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from author_custom_values where author_custom_origine not in (select author_id from authors)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from categ_custom_values where categ_custom_origine not in (select id_noeud from noeuds)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from collection_custom_values where collection_custom_origine not in (select collection_id from collections)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from indexint_custom_values where indexint_custom_origine not in (select indexint_id from indexint)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from publisher_custom_values where publisher_custom_origine not in (select ed_id from publishers)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from serie_custom_values where serie_custom_origine not in (select serie_id from series)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from subcollection_custom_values where subcollection_custom_origine not in (select sub_coll_id from sub_collections)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from skos_custom_values where skos_custom_origine not in (select uri_id from onto_uri)");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("DELETE from tu_custom_dates where tu_custom_origine not in (select tu_id from titres_uniformes)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from author_custom_dates where author_custom_origine not in (select author_id from authors)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from categ_custom_dates where categ_custom_origine not in (select id_noeud from noeuds)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from collection_custom_dates where collection_custom_origine not in (select collection_id from collections)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from indexint_custom_dates where indexint_custom_origine not in (select indexint_id from indexint)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from publisher_custom_dates where publisher_custom_origine not in (select ed_id from publishers)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from serie_custom_dates where serie_custom_origine not in (select serie_id from series)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from subcollection_custom_dates where subcollection_custom_origine not in (select sub_coll_id from sub_collections)");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE from skos_custom_dates where skos_custom_origine not in (select uri_id from onto_uri)");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("delete cms_articles_descriptors from cms_articles_descriptors left join noeuds on num_noeud=id_noeud where id_noeud is null");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("delete cms_sections_descriptors from cms_sections_descriptors left join noeuds on num_noeud=id_noeud where id_noeud is null");
$affected += pmb_mysql_affected_rows();

$query = "SELECT N1.id_noeud AS id, N1.num_parent AS parent, N3.id_noeud AS top_thes
FROM noeuds N1
JOIN noeuds N2
ON N1.num_parent = N2.id_noeud
JOIN thesaurus T
ON T.id_thesaurus = N1.num_thesaurus
JOIN noeuds N3
ON N3.num_thesaurus = T.id_thesaurus
AND N3.autorite = 'TOP'
WHERE N2.autorite = 'TOP' AND N1.autorite != 'TOP'
AND N1.num_thesaurus != N2.num_thesaurus";
$result = pmb_mysql_query($query);
if (pmb_mysql_num_rows($result)) {
    while ($row = pmb_mysql_fetch_assoc($result)) {
        if ($row["parent"] != $row["top_thes"]) {
            pmb_mysql_query("UPDATE noeuds SET num_parent = ".$row["top_thes"]." WHERE id_noeud = ".$row["id"]);
            $affected++;
        }
    }
}

$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_relations"], $affected." ".$msg["nettoyage_res_suppr_relations_cat"]);
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '3');
