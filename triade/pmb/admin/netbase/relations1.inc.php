<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relations1.inc.php,v 1.23 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg;
global $start, $v_state, $spec;

// initialisation de la borne de départ
if(!isset($start)) $start=0;

$v_state=urldecode($v_state);

print netbase::get_display_progress_title($msg["nettoyage_clean_relations_ban"]);

$affected = 0;
pmb_mysql_query("DELETE bannettes FROM bannettes LEFT JOIN empr ON proprio_bannette = id_empr WHERE id_empr IS NULL AND proprio_bannette !=0");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE equations FROM equations LEFT JOIN empr ON proprio_equation = id_empr WHERE id_empr IS NULL AND proprio_equation !=0 ");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE bannette_equation FROM bannette_equation LEFT JOIN bannettes ON num_bannette = id_bannette WHERE id_bannette IS NULL ");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE bannette_equation FROM bannette_equation LEFT JOIN equations on num_equation=id_equation WHERE id_equation is null");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE bannette_abon FROM bannette_abon LEFT JOIN empr on num_empr=id_empr WHERE id_empr is null");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("DELETE bannette_abon FROM bannette_abon LEFT JOIN bannettes ON num_bannette=id_bannette WHERE id_bannette IS NULL ");
$affected += pmb_mysql_affected_rows();
pmb_mysql_query("delete caddie_content from caddie join caddie_content on (idcaddie=caddie_id and type='NOTI') left join notices on object_id=notice_id where notice_id is null");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("delete bannette_contenu FROM bannette_contenu left join notices on num_notice=notice_id where notice_id is null ");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("delete bannette_contenu FROM bannette_contenu left join bannettes on num_bannette=id_bannette where id_bannette is null ");
$affected += pmb_mysql_affected_rows();

pmb_mysql_query("DELETE avis FROM avis LEFT JOIN notices ON num_notice=notice_id WHERE type_object = 1 AND notice_id IS NULL ");
pmb_mysql_query("DELETE avis FROM avis LEFT JOIN cms_articles ON num_notice=id_article WHERE type_object = 2 AND id_article IS NULL ");

pmb_mysql_query("DELETE FROM categories WHERE libelle_categorie='' ");

$v_state .= netbase::get_display_progress_v_state($msg["nettoyage_suppr_relations"], $affected." ".$msg["nettoyage_res_suppr_relations_ban"]);
pmb_mysql_query('OPTIMIZE TABLE bannette_contenu');
// mise à jour de l'affichage de la jauge
print netbase::get_display_final_progress();

print netbase::get_process_state_form($v_state, $spec, '', '2');
