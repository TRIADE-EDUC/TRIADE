<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SharedListsController.php,v 1.1.2.1 2024/06/17 12:04:37 jparis Exp $

namespace Pmb\AI\Controller;

use encoding_normalize;
use Pmb\AI\Models\AiModel;
use Pmb\AI\Models\SemanticSearchModel;
use Pmb\AI\Models\SharedListModel;
use Pmb\AI\Orm\AISettingsOrm;
use Pmb\AI\Orm\AiSharedListOrm;
use Pmb\AI\Views\SemanticSearchView;
use Pmb\AI\Views\SharedListView;
use Pmb\Common\Controller\Controller;
use Pmb\Common\Orm\EmprCategOrm;
use Pmb\Common\Orm\SearchPersoOrm;
use Pmb\Common\Orm\UploadFolderOrm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class SharedListsController extends Controller
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
            case "save":
                $this->saveAction();
            default:
                $this->formAction();
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

    public function formAction()
    {
        global $pmb_url_base;

        $emprCategOrm = new EmprCategOrm();
        $uploadFolder = new UploadFolderOrm();

        $sharedListView = new SharedListView("AI/sharedList", [
            "sharedlistsdata" => SharedListModel::getSharedListData(),
            "emprcategory" => $emprCategOrm->getEmprCategList(),
            "uploadfolder" => $uploadFolder->getUploadForlderList(),
            "url_webservice" => $pmb_url_base . "rest.php/Ai/"
        ]);
        print $sharedListView->render();
    }


    /* La fonction `saveAction()` est responsable de la sauvegarde des données reçues lors de la
    soumission du formulaire. */

    public function saveAction()
    {
        try {
            $aiSharedListOrm = new AiSharedListOrm();
            $aiSharedListOrm->id_ai_shared_list = SharedListModel::ID_CONFIG_SHARED_LIST;
            $aiSharedListOrm->settings_ai_shared_list = encoding_normalize::json_encode($this->data);
            $aiSharedListOrm->save();
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
