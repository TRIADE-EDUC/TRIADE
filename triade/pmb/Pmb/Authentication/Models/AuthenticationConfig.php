<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationConfig.php,v 1.4 2023/08/29 15:31:35 dbellamy Exp $
namespace Pmb\Authentication\Models;

use Pmb\Common\Models\Model;
use Pmb\Authentication\Orm\AuthenticationConfigsOrm;
if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AuthenticationConfig extends Model
{

    public $id = "";

    public $name = "";

    public $source_name = "";

    public $settings = "";

    public $context = 0;

    public $ranking = 0;

    public const ALL_MODELS = 0;

    public const OPAC_MODEL = 1;

    public const GESTION_MODEL = 2;

    protected $ormName = "Pmb\Authentication\Orm\AuthenticationConfigsOrm";

    public static function getConfigs($context = AuthenticationConfig::ALL_MODELS)
    {
        $list = AuthenticationConfigsOrm::findByContext($context, "ranking");
        return self::toArray($list);
    }

    public static function getConfig(int $id)
    {
        $config = AuthenticationConfigsOrm::find("id", $id);
        return self::toArray($config);
    }

    public function saveConfig($data)
    {
        $id = empty($data->modelParams->id) ? 0 : intval($data->modelParams->id);

        $authenticationConfigsOrm = new AuthenticationConfigsOrm($id);
        $authenticationConfigsOrm->name = $data->modelParams->name;
        $authenticationConfigsOrm->source_name = $data->sourceName;
        $authenticationConfigsOrm->settings = \encoding_normalize::json_encode($data->modelParams->settings);
        $authenticationConfigsOrm->context = intval($data->modelParams->context);
        $authenticationConfigsOrm->ranking = 0;
        $authenticationConfigsOrm->save();

        return $authenticationConfigsOrm->id;
    }

    public static function updateAllowInternalGestion($state)
    {
        $query = "UPDATE parametres SET valeur_param = " . ((1 != intval($state)) ? 1 : 0) . " WHERE sstype_param = 'allow_internal_gestion_authentication'";
        pmb_mysql_query($query);
    }

    public static function updateAllowInternalOpac($state)
    {
        $query = "UPDATE parametres SET valeur_param = " . ((1 != intval($state)) ? 1 : 0) . " WHERE sstype_param = 'allow_internal_opac_authentication'";
        pmb_mysql_query($query);
    }
}

