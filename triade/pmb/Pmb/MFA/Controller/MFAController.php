<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFAController.php,v 1.4 2022/10/18 13:45:59 jparis

namespace Pmb\MFA\Controller;

use Pmb\Common\Controller\Controller;

class MFAController extends Controller
{
    protected $action;
    public function proceed($action = "")
    {
        $this->action = $action;
        switch ($action) {
            case "edit":
            default:
                return $this->defaultAction();
        }
    }
    
    protected function getDataList()
    {
        global $pmb_url_base;
        return [
            "url_webservice" => $pmb_url_base . "rest.php/mfa/",
            "action" => $this->action,
            "data" => [
                "gestion" => $this->getData("GESTION"),
                "opac" => $this->getData("OPAC"),
            ]
        ];
    }
    
    public function getData($context)
    {
        $instance = new static::$modelNamespace();
        $data = $instance->getList(["context" => $context]);
        if(!empty($data)) {
            return $data[0];
        }
        
        return $this->getDefaultData($instance, $context);
    }
    
    private function getDefaultData($instance, $context)
    {
        $instance->context = $context;
        return $instance;
    }
    
    public function save()
    {
        foreach ($this->data->objects as $object) {
            $object->id = intval($object->id);
            
            $model = new static::$modelNamespace($object->id);
            $model->setFromForm($object);
            
            if (!$object->id) {
                $model->create();
            } else {
                $model->update();
            }

            $this->saveTranslations($model->id, $model->context);
        }
        
        $this->ajaxJsonResponse($this->data);
    }

    public function saveTranslations(int $id, string $context) {}
    
}