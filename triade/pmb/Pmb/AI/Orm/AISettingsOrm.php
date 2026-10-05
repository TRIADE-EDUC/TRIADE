<?php

// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AISettingsOrm.php,v 1.9 2024/03/12 14:06:22 qvarin Exp $

namespace Pmb\AI\Orm;

use encoding_normalize;
use Pmb\Common\Orm\Orm;

class AISettingsOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "ai_settings";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_ai_setting";

    /**
     *
     * @var integer
     */
    public $id_ai_setting = 0;

    /**
     *
     * @var string|object
     */
    public $settings_ai_settings = "";

    /**
     *
     * @var integer
     */
    public $active_ai_settings = 0;


    /**
     * La fonction « getAiSettings » récupère les paramètres de l'IA, les traite et les renvoie dans un
     * tableau.
     *
     * @return array tableau d'objets de paramètres IA avec des propriétés supplémentaires telles que
     * `settings_ai_settings`, `active_ai_settings` et `caddie_name`. La propriété
     * `settings_ai_settings` est décodée à partir du format JSON, la propriété `active_ai_settings`
     * est définie directement et la propriété `caddie_name` est récupérée à l'aide d'une fonction
     * d'assistance `caddie::get_data_from_id()` basée sur
     */

    public function getAiSettings()
    {
        $return = [];
        $settings = $this->findAll();

        foreach($settings as $setting) {
            $setting->settings_ai_settings = json_decode($setting->settings_ai_settings);
            $setting->active_ai_settings = $setting->active_ai_settings;
            // Petit hack pour récupere le nom du caddie... Car dans la pratique on ne le sauvegarde pas
            $setting->settings_ai_settings->caddie_name = \caddie::get_data_from_id($setting->settings_ai_settings->caddie_id)["name"];
            $setting->unsetStructure();
            $return[] = $setting;
        }
        return $return;
    }


    /**
     * La fonction « getAiSettingsById » récupère les paramètres AI par leur ID, convertit les
     * paramètres JSON en objet, supprime la structure et renvoie les paramètres.
     *
     * @param id Le paramètre "id" est l'identifiant des paramètres AI que vous souhaitez récupérer. Il
     * est utilisé pour rechercher les paramètres AI dans la base de données.
     *
     * @return array tableau d?objets de paramètres AI.
     */
    public function getAiSettingsById($id)
    {
        $return = [];
        $settings = $this->findById($id);

        foreach($settings as $setting) {
            $setting->settings_ai_settings = json_decode($setting->settings_ai_settings);
            $setting->unsetStructure();
            $return[] = $setting;
        }
        return $return;
    }


    /**
     * La fonction « getAiSettingsFindAll » récupère tous les paramètres AI, convertit la chaîne JSON
     * en objet, supprime la propriété de structure et renvoie les paramètres modifiés.
     *
     * @return array tableau d?objets de paramètres IA.
     */
    public static function getAiSettingsFindAll()
    {
        $return = [];
        $settings = self::findAll();

        foreach($settings as $setting) {
            $setting->settings_ai_settings = json_decode($setting->settings_ai_settings);
            $setting->unsetStructure();
            $return[] = $setting;
        }
        return $return;
    }

    /**
     * La fonction « getAiSettingActive » renvoie l'objet des paramètres AI actuels.
     *
     * @return AiSettingsOrm|null
     */
    public static function getAiSettingActive()
    {
        $setting = self::find("active_ai_settings", "1");
        if (!empty($setting)) {
            $setting[0]->settings_ai_settings = json_decode($setting[0]->settings_ai_settings);
            $setting[0]->unsetStructure();
            return $setting[0];
        }
        return null;
    }


    /**
     * La fonction `formatSettingsAiSettings()` est une méthode au sein de la classe `AISettingsOrm`.
     * Cette fonction est responsable du formatage de la propriété `settings_ai_settings` d'un objet.
     *
     * @return \stdClass
     */
    public function formatSettingsAiSettings()
    {
        $settings_ai_settings = is_string($this->settings_ai_settings) ? encoding_normalize::json_decode($this->settings_ai_settings) : $this->settings_ai_settings;
        if (!is_object($settings_ai_settings)) {
            $settings_ai_settings = new \stdClass();
        }
        return $settings_ai_settings;
    }

}
