<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ai_indexation.inc.php,v 1.12.2.1 2024/06/24 12:44:50 qvarin Exp $

use Pmb\AI\Orm\AISettingsOrm;
use Pmb\AI\Library\Api;
use Pmb\AI\Models\AiModel;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $class_path, $msg, $charset;
global $start, $current_module, $v_state;
global $pmb_set_time_limit;

// initialisation de la borne de départ
if (!isset($start)) {
    $start = 0;
}

$v_state = urldecode($v_state ?? '');

print netbase::get_display_progress_title($msg["admin_netbase_ai_indexation"]);
if ($start) {
    showProgressAI();
} else {
    startIndexationAI();
}

function startIndexationAI()
{
    global $charset, $msg, $spec, $start, $v_state;

    $index_quoi = [];

    $config = AISettingsOrm::getAiSettingActive();
    if (empty($config)) {
        global $spec;
        $spec = $spec - AI_INDEXATION;
        print netbase::get_current_state_form($v_state, $spec);
        return false;
    }

    $index_quoi[] = $config->id_ai_setting;
    // Je laisse cela au cas ou on change  d'avis
    // $api = new Api($config);
    // $api->cleanContainer();
    // AiModel::unFlagAllElementInCaddie($config->formatSettingsAiSettings()->caddie_id);

    $display = sprintf(
        $msg['admin_netbase_ai_indexation_start_for'],
        $config->settings_ai_settings->name
    );
    print "<h3>" . htmlentities($display, ENT_QUOTES, $charset) . "</h3>";
    print netbase::build_display_progress(0);

    print netbase::get_current_state_form($v_state, $spec, implode(',', $index_quoi), 1);
}


function showProgressAI()
{
    global $index_quoi, $charset, $v_state, $spec, $msg;

    $index_quoi = explode(',', $index_quoi);
    $index_quoi = array_map('intval', $index_quoi);

    $finish = true;

    foreach ($index_quoi as $id_ai_setting) {
        if (!AISettingsOrm::exist($id_ai_setting)) {
            continue;
        }

        // get instance
        $config = new AISettingsOrm($id_ai_setting);

        // On recupere le nombre d'elements dans le caddie
        $totalEntries = intval(AiModel::getCountEntriesInCaddie($config->formatSettingsAiSettings()->caddie_id));

        // On lance une premiere indexation
        global $ai_index_nb_elements;
        $api = new Api($config);
        $api->indexation($ai_index_nb_elements);

        // On recupere le nombre d'elements non indexés dans le caddie
        $totalEntriesUnFlag = intval(AiModel::GetNbEntriesCaddieContent($config->formatSettingsAiSettings()->caddie_id));

        if (empty($totalEntries)) {
            // On a fini l'indexation
            $progress = 100;
        } else {
            // calcul de la progress
            $countIndexed = $totalEntries - $totalEntriesUnFlag;
            $progress = ($countIndexed * 100) / $totalEntries;
            $progress = round($progress, 2);

            if ($progress < 0) {
                $progress = 0;
            } elseif ($progress >= 100) {
                $progress = 100;
            }

            if ($progress < 100) {
                // On indique qu'on a pas fini une configuration
                $finish = false;
            }
        }


        $display = sprintf(
            $msg['admin_netbase_ai_indexation_start_for'],
            $config->settings_ai_settings->name
        );
        print "<h3>" . htmlentities($display, ENT_QUOTES, $charset) . "</h3>";
        print netbase::build_display_progress($progress);
    }

    if ($finish) {
        $spec = $spec - AI_INDEXATION;
        $v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace='3'>".htmlentities($msg['netbase_ai_indexation'], ENT_QUOTES, $charset);
        print netbase::get_current_state_form($v_state, $spec);
    } else {
        print netbase::get_current_state_form($v_state, $spec, implode(',', $index_quoi), 1);
    }
}
