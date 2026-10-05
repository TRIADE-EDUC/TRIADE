<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perio_a2z.inc.php,v 1.10.14.1.2.1 2025/02/07 13:49:04 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

require_once($base_path."/classes/perio_a2z.class.php");

global $nb_per_page_custom, $opac_perio_a2z_max_per_onglet;

$max_per_onglet = !empty($nb_per_page_custom) ? $nb_per_page_custom : $opac_perio_a2z_max_per_onglet;

switch($sub) {

    case 'get_onglet':
        $a2z = new perio_a2z(0, $opac_perio_a2z_abc_search, $max_per_onglet);
        ajax_http_send_response($a2z->get_onglet($onglet_sel));
        break;

    case 'get_perio':
        $a2z = new perio_a2z($id, $opac_perio_a2z_abc_search, $max_per_onglet);
        $html = $a2z->get_perio($id);

        if ($pmb_logs_activate) {

            // On met l'id par defaut si la notice n'existe pas, ou que c'est une notice externe
            $docs = $id;
            if (0 < intval($id)) {
                $rqt = "SELECT notice_id, typdoc, niveau_biblio, index_l, libelle_categorie, name_pclass, indexint_name
                    FROM notices n
                    LEFT JOIN notices_categories nc ON nc.notcateg_notice=n.notice_id
                    LEFT JOIN categories c ON nc.num_noeud=c.num_noeud
                    LEFT JOIN indexint i ON n.indexint=i.indexint_id
                    LEFT JOIN pclassement pc ON i.num_pclass=pc.id_pclass
                    WHERE notice_id='" . intval($id) . "'";
                $res_noti = pmb_mysql_query($rqt);
                if (pmb_mysql_num_rows($res_noti)) {
                    $docs = pmb_mysql_fetch_array($res_noti);
                }
            }

            // Enregistrement du log
            $record_log =  generate_log('ajax_get_perio', ['docs' => $docs]);
            if ($record_log) {
                $html .= $record_log->validation_script();
            }
        }
        ajax_http_send_response($html);
        break;

    case 'reload':
        $a2z = new perio_a2z(0, $opac_perio_a2z_abc_search, $max_per_onglet);
        ajax_http_send_response($a2z->get_form(0, 0, 1));
        break;

    default:
        http_response_code(404);
        break;
}
