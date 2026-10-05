<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationModel.php,v 1.9 2023/07/03 15:09:58 dbellamy Exp $

namespace Pmb\Authentication\Models;

use Pmb\Authentication\Orm\AuthenticationModelsOrm;
use Pmb\Common\Models\Model;
use Pmb\Authentication\Interfaces\CreateUserInterface;
use Pmb\Authentication\Interfaces\SearchEmprInterface;
use Pmb\Authentication\Interfaces\SearchUserInterface;
use Pmb\Authentication\Interfaces\TransfoInterface;
use Pmb\Authentication\Helpers\Empr;
use Pmb\Authentication\Helpers\User;
use Pmb\Authentication\Helpers\Transfo;
use Pmb\Authentication\Interfaces\CreateEmprInterface;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AuthenticationModel extends Model
{

    public $id = "";

    public $name = "";

    public $source_name = "";

    public $settings = "";

    public $context = 0;

    protected static $searchEmprClass = null;

    protected static $searchUserClass = null;

    protected static $transfoClass = null;

    protected static $createUserClass = null;

    protected static $createEmprClass = null;

    public const ALL_MODELS = 0;

    public const OPAC_MODEL = 1;

    public const GESTION_MODEL = 2;

    protected $ormName = "Pmb\Authentication\Orm\AuthenticationModelsOrm";

    public static function getModels($context = AuthenticationModel::ALL_MODELS)
    {
        $list = AuthenticationModelsOrm::findByContext($context);
        return self::toArray($list);
    }

    public static function getModel(int $id)
    {
        $model = AuthenticationModelsOrm::find("id", $id);
        return self::toArray($model);
    }

    public static function getEmprSearchClassList()
    {
        global $base_path;

        if (! is_null(static::$searchEmprClass)) {
            return static::$searchEmprClass;
        }

        static::$searchEmprClass = [];
        $path = $base_path ."/Pmb/Authentication/Helpers/Empr/*.php";
        $files = glob($path);
        foreach ($files as $file) {
            $class_name = '\\Pmb\\Authentication\\Helpers\\Empr\\'.basename($file, ".php");
            $obj = new $class_name();
            if ($obj instanceof SearchEmprInterface) {
                static::$searchEmprClass[] = basename($file, ".php");
            }
        }
        return static::$searchEmprClass;
    }

    public static function getEmprCreateClassList()
    {
        global $base_path;

        if (! is_null(static::$createEmprClass)) {
            return static::$createEmprClass;
        }

        static::$createEmprClass = [];
        $path = $base_path . "/Pmb/Authentication/Helpers/Empr/*.php";
        $files = glob($path);
        foreach ($files as $file) {
            $class_name = '\\Pmb\\Authentication\\Helpers\\Empr\\' . basename($file, ".php");
            $obj = new $class_name();
            if ($obj instanceof CreateEmprInterface) {
                static::$createEmprClass[] = basename($file, ".php");
            }
        }
        return static::$createEmprClass;
    }

    public static function getUserSearchClassList()
    {
        global $base_path;

        if (! is_null(static::$searchUserClass)) {
            return static::$searchUserClass;
        }

        static::$searchUserClass = [];
        $path = $base_path . "/Pmb/Authentication/Helpers/User/*.php";
        $files = glob($path);
        foreach ($files as $file) {
            $class_name = '\\Pmb\\Authentication\\Helpers\\User\\'.basename($file, ".php");
            $obj = new $class_name();
            if ($obj instanceof SearchUserInterface) {
                static::$searchUserClass[] = basename($file, ".php");
            }
        }

        return static::$searchUserClass;
    }

    public static function getUserCreateClassList()
    {
        global $base_path;

        if (! is_null(static::$createUserClass)) {
            return static::$createUserClass;
        }

        static::$createUserClass = [];
        $path = $base_path . "/Pmb/Authentication/Helpers/User/*.php";
        $files = glob($path);
        foreach ($files as $file) {
            $class_name = '\\Pmb\\Authentication\\Helpers\\User\\' . basename($file, ".php");
            $obj = new $class_name();
            if ($obj instanceof CreateUserInterface) {
                static::$createUserClass[] = basename($file, ".php");
            }
        }

        return static::$createUserClass;
    }

    public static function getTransfoClassList()
    {
        global $base_path;

        if (! is_null(static::$transfoClass)) {
            return static::$transfoClass;
        }

        static::$transfoClass = [];
        $path = $base_path . "/Pmb/Authentication/Helpers/Transfo/*.php";
        $files = glob($path);
        foreach ($files as $file) {
            $class_name = '\\Pmb\\Authentication\\Helpers\\Transfo\\'.basename($file, ".php");
            $obj = new $class_name();
            if ($obj instanceof TransfoInterface) {
                static::$transfoClass[] = basename($file, ".php");
            }
        }
        return static::$transfoClass;
    }

    public static function getViewData()
    {
        global $base_path, $pmb_url_base;

        $path = $base_path . 'Pmb/Authentication/Models/Sources';
        $manifests_list = AuthenticationParserDirectory::getInstance()->getManifests($path);

        $models_list = AuthenticationModel::getModels();

        $empr_search_class_list = AuthenticationModel::getEmprSearchClassList();
        $user_search_class_list = AuthenticationModel::getUserSearchClassList();

        $empr_create_class_list = AuthenticationModel::getEmprCreateClassList();
        $user_create_class_list = AuthenticationModel::getUserCreateClassList();

        $transfo_class_list = AuthenticationModel::getTransfoClassList();

        return [
            "url_webservice" => $pmb_url_base . "rest.php/authentication/",
            "manifests_list" => $manifests_list,
            "models_list" => $models_list,
            "empr_search_class_list" => $empr_search_class_list,
            "user_search_class_list" => $user_search_class_list,
            "empr_create_class_list" => $empr_create_class_list,
            "user_create_class_list" => $user_create_class_list,
            "transfo_class_list" => $transfo_class_list
        ];
    }

    public function saveModel($data)
    {
        $id = empty($data->modelParams->id) ? 0 : intval($data->modelParams->id);

        $authenticationModelsOrm = new AuthenticationModelsOrm($id);
        $authenticationModelsOrm->name = $data->modelParams->name;
        $authenticationModelsOrm->source_name = $data->sourceName;
        $authenticationModelsOrm->settings = \encoding_normalize::json_encode($data->modelParams->settings);
        $authenticationModelsOrm->context = intval($data->modelParams->context);
        $authenticationModelsOrm->save();

        return $authenticationModelsOrm->id;
    }
}

