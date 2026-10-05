<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SemanticSearchModel.php,v 1.4 2024/02/19 12:35:49 gneveu Exp $

namespace Pmb\AI\Models;

use Pmb\AI\Orm\AISettingsOrm;
use Pmb\Common\Models\Model;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class SemanticSearchModel extends Model
{
    // On définit le type search_sémantique a 1

     /**
     * La fonction « getSemanticSearchList » récupère les paramètres AI d'un type spécifique.
     *
     * @return la liste des paramètres AI pour la recherche sémantique.
     */
    public static function getSemanticSearchList()
    {
        $aiSettingsOrm = new AISettingsOrm();
        return $aiSettingsOrm->getAiSettings();
    }
}
