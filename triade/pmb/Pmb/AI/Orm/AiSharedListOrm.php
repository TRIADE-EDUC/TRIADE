<?php

// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiSharedListOrm.php,v 1.1.2.5 2024/06/26 13:03:02 qvarin Exp $

namespace Pmb\AI\Orm;

use encoding_normalize;
use Pmb\AI\Models\SharedListModel;
use Pmb\Common\Orm\EmprOrm;
use Pmb\Common\Orm\Orm;
use Pmb\Common\Orm\UploadFolderOrm;

class AiSharedListOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "ai_shared_list";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_ai_shared_list";

    /**
     *
     * @var integer
     */
    public $id_ai_shared_list = 0;

    /**
     *
     * @var string|object
     */
    public $settings_ai_shared_list = "";

    /**
     * La fonction « getAiSettingActive » renvoie l'objet des paramètres AI actuels.
     *
     * @return AiSharedListOrm|null
     */
    public static function getAiSettingActive()
    {
        try {
            $setting = self::findById(SharedListModel::ID_CONFIG_SHARED_LIST);
            if (!empty($setting)) {
                $setting->settings_ai_shared_list = json_decode($setting->settings_ai_shared_list);
                $setting->unsetStructure();

                return $setting;
            }
        } catch (\Exception $e) {
            // No record found !
        }

        return null;
    }

    /**
     * La fonction `formatSettingsAiSettings()` est une méthode au sein de la classe `AiSharedListOrm`.
     * Cette fonction est responsable du formatage de la propriété `settings_ai_shared_list` d'un objet.
     *
     * @return \stdClass
     */
    public function formatSettingsAiSettings()
    {
        global $id_empr;

        $settings_ai_shared_list = is_string($this->settings_ai_shared_list) ? encoding_normalize::json_decode($this->settings_ai_shared_list) : $this->settings_ai_shared_list;

        $id_empr = intval($id_empr);
        if (!empty($id_empr)) {

            $emprOrm = EmprOrm::findById($id_empr);
            $emprOrm->empr_categ;

            if (
                !empty($settings_ai_shared_list->prompt->{$emprOrm->empr_categ}) &&
                !empty($settings_ai_shared_list->prompt->{$emprOrm->empr_categ}->prompt_system)
            ) {
                $settings_ai_shared_list->prompt_system = $settings_ai_shared_list->prompt->{$emprOrm->empr_categ}->prompt_system;
            } else {
                $settings_ai_shared_list->prompt_system = $settings_ai_shared_list->prompt->{0}->prompt_system;
            }

            if (
                !empty($settings_ai_shared_list->prompt->{$emprOrm->empr_categ}) &&
                !empty($settings_ai_shared_list->prompt->{$emprOrm->empr_categ}->prompt_user)
            ) {
                $settings_ai_shared_list->prompt_user = $settings_ai_shared_list->prompt->{$emprOrm->empr_categ}->prompt_user;
            } else {
                $settings_ai_shared_list->prompt_user = $settings_ai_shared_list->prompt->{0}->prompt_user;
            }
            unset($settings_ai_shared_list->prompt);
        }

        if (!is_object($settings_ai_shared_list)) {
            $settings_ai_shared_list = new \stdClass();
            $settings_ai_shared_list->prompt_system = "Ne répond pas à la question. Dit simplement qu'il y a une erreur.";
            $settings_ai_shared_list->prompt_user = "";
            $settings_ai_shared_list->min_score = 100;
            $settings_ai_shared_list->url_server_python = null;
            $settings_ai_shared_list->token = null;
            $settings_ai_shared_list->indexation_choice = new \stdClass();
        }

        $settings_ai_shared_list->isSharedList = true;
        return $settings_ai_shared_list;
    }

    public function getAiSharedList()
    {
        return encoding_normalize::json_decode($this->settings_ai_shared_list);
    }

    /**
     * Retourne le repertoire d'upload
     *
     * @return UploadFolderOrm
     * @throws \Exception
     */
    public function getUploadFolder()
    {
        $settings = $this->getAiSettingActive();
        if (empty($settings)) {
            throw new \Exception('No active AI settings');
        }

        $uploadFolderId = intval($settings->settings_ai_shared_list->upload_folder);
        if (empty($uploadFolderId)) {
            throw new \Exception('No upload folder');
        }

        return UploadFolderOrm::findById($uploadFolderId);
    }
}
