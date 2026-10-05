<?php
namespace Pmb\Common\Helper;
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: H2oAutocompletion.php,v 1.1 2021/05/04 13:13:36 arenou Exp $

global $base_path;

require_once $base_path . "/includes/h2o/pmb_h2o.inc.php";

/**
 *
 * @author arenou
 *        
 */
class H2oAutocompletion
{

    private $tags = [];

    private $filters = [];

    // TODO - Insert your code here

    /**
     */
    public function __construct()
    {
        // TODO - Insert your code here
        $this->initTags();
        $this->initFilters();
    }

    private function initTags()
    {
        foreach (\H2o::$tags as $key => $object) {
            // TODO un peu d'annotation pour aider les webs !
            $infos = [];
            $reflectedObject = new \ReflectionClass($object);
            $infos = Annotations::get($reflectedObject);
            if (empty($infos)) {
                $infos = [
                    'classname' => $object
                ];
            }
            $this->tags[$key] = $infos;
        }
    }

    private function initFilters()
    {
        foreach (\H2o::$filters as $key => $value) {
            // TODO un peu d'annotation pour aider les webs !
            $tag = "DefaultFilters";
            $infos = [];
            if (is_array($value)) {
                $tag = $value[0];
                $reflectedMethod = new \ReflectionMethod($value[0],$value[1]);
            }else{
                $reflectedMethod = new \ReflectionFunction($value);
            }
            $infos = Annotations::get($reflectedMethod);
            $infos['tag'] = $tag;
            $this->filters[$key] = $infos;
        }
    }

    public function search($word)
    {
        $response = [];
        foreach ($this->tags as $key => $infos) {
            if ($word === '' || strpos($key, $word) !== false) {
                $response[] = [
                    'caption' => $infos['Description'] ?? $key,
                    'value' => $infos['Format'] ?? $key,
                    'meta' => 'Tag'
                ];
            }
        }
        foreach ($this->filters as $key => $infos) {
            if ($word === '' || strpos($key, $word) !== false) {
                $response[] = [
                    'caption' => $infos['Description'] ?? $key,
                    'value' => $infos['Format'] ?? $key,
                    'meta' => $infos['tag']
                ];
            }
        }

        return $response;
    }
}

