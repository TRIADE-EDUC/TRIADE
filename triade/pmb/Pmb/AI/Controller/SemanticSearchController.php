<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SemanticSearchController.php,v 1.6.2.3 2024/09/24 08:19:59 gneveu Exp $

namespace Pmb\AI\Controller;

use Pmb\AI\Models\AiModel;
use Pmb\AI\Models\SemanticSearchModel;
use Pmb\AI\Orm\AISettingsOrm;
use Pmb\AI\Views\SemanticSearchView;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Orm\SearchPersoOrm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class SemanticSearchController extends Controller
{
    /* La ligne `public  = "list";` déclare une propriété publique appelée `` et lui
    attribue la valeur "list". Cette propriété est utilisée pour déterminer l'action par défaut à
    effectuer par la méthode `proceed()` si aucun paramètre d'action n'est fourni. Dans ce cas,
    l'action par défaut est définie sur "liste". */
    public $action = "list";


    /**
     * La fonction "proceed" détermine l'action à entreprendre en fonction du paramètre fourni et
     * exécute la méthode correspondante.
     *
     * @param action Le paramètre « action » est utilisé pour déterminer l'action spécifique qui doit
     * être effectuée. Il peut avoir trois valeurs possibles : « modifier », « ajouter » ou «
     * enregistrer ».
     */

    public function proceed($action = "")
    {
        $this->action = $action;

        switch ($action) {
            case 'edit':
            case 'add':
                $this->getFormAction();
                break;
            case 'save':
                $this->saveAction();
                break;
            case 'delete':
                $this->deleteAction();
                break;
            case 'active_semantic_search':
                $this->activeSemanticSearchAction();
                break;
            default:
                $this->listAction();
        }
    }


    /**
     * La fonction setData définit la valeur de la propriété data dans une classe PHP.
     *
     * @param data Le paramètre "data" est une variable qui représente les données que vous souhaitez
     * définir pour l'objet. Il peut s'agir de n'importe quel type de données, tel qu'une chaîne, un
     * entier, un tableau ou un objet.
     */

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * La fonction « listAction » crée une nouvelle instance de la classe « SemanticSearchView », en
     * transmettant certaines données, puis restitue et imprime la vue.
     */

    public function listAction()
    {
        $semanticSearchView = new SemanticSearchView("AI/semanticSearch", [
            "action" => $this->action,
            "semanticsearchlist" => SemanticSearchModel::getSemanticSearchList(),
        ]);
        print $semanticSearchView->render();
    }

    /**
     * La fonction `getFormAction` récupère l'action du formulaire pour la recherche sémantique et
     * restitue la vue correspondante.
     */

    public function getFormAction()
    {
        global $pmb_url_base;

        $semanticSearch = [];
        if (isset($this->data) && is_int($this->data) && AISettingsOrm::exist($this->data)) {
            $aISettingsOrm = new AISettingsOrm($this->data);
            $aISettingsOrm->unsetStructure();
            $semanticSearch = $aISettingsOrm;
        }

        $semanticSearchView = new SemanticSearchView("AI/semanticSearch", [
            "action" => $this->action,
            "semanticsearch" => $semanticSearch,
            "caddieslist" => \caddie_root::get_caddies(["idcaddie", "name"]),
            "url_webservice" => $pmb_url_base . "rest.php/Ai/"
        ]);
        print $semanticSearchView->render();
    }

    /* La fonction `saveAction()` est responsable de la sauvegarde des données reçues lors de la
    soumission du formulaire. */

    public function saveAction()
    {
        try {
            if (isset($this->data->id) && !empty($this->data->id)) {
                $aiSettingsOrm = new AISettingsOrm($this->data->id);
            } else {
                $aiSettingsOrm = new AISettingsOrm();
            }

            $aiSettingsOrm->settings_ai_settings = json_encode($this->data->settings);
            $aiSettingsOrm->save();

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /* La fonction `deleteAction()` est responsable de la suppression d'un enregistrement de la base de
    données. Il crée une instance de la classe `AISettingsOrm` avec l'ID de l'enregistrement à
    supprimer, puis appelle la méthode `delete()` sur cette instance pour supprimer
    l'enregistrement. Si la suppression réussit, elle renvoie « true ». Si une exception se produit
    pendant le processus de suppression, il intercepte l'exception et renvoie le message d'erreur. */

    public function deleteAction()
    {
        $Id = intval($this->data);
        if (!AISettingsOrm::exist($id)) {
            http_response_code(404);
            return "Suppression Impossible";
        }

        try {
            $aISettingsOrm = new AISettingsOrm($id);
            $aISettingsOrm->delete();
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /* La fonction `activeSemanticSearchAction()` est une méthode de la classe
    `SemanticSearchController`. Il se charge d'activer une recherche sémantique en appelant la
    méthode `setActiveSemanticSearch()` depuis la classe `AiModel`. */

    public function activeSemanticSearchAction()
    {
        if (!isset($this->data) || !AISettingsOrm::exist(intval($this->data))) {
            http_response_code(404);
            return $this->listAction();
        }
        try {
            AiModel::setActiveSemanticSearch(intval($this->data));
            $this->listAction();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
