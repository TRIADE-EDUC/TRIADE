<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ConditionModel.php,v 1.4 2022/03/17 15:49:52 qvarin Exp $
namespace Pmb\CMS\Models;

class ConditionModel extends PortalRootModel implements ConditionInterfaceModel
{

    public static $nbInstance = 0;

    /**
     * 
     * @var ConditionEnvModel|ConditionFRBRModel[]
     */
    public static $instances = array();

    protected $data = null;

    /**
     *
     * @return bool
     */
    public function check(): bool
    {
        return true;
    }
    
    public static function getConditionList()
    {
        global $include_path, $msg;
        $filename = $include_path."/portal/conditions.xml";
        $filename_subst = $include_path."/portal/conditions_subst.xml";
        if (is_readable($filename_subst)) {
            $filename = $filename_subst;
        }
        
        $xml = json_decode(json_encode(simplexml_load_file($filename, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT)), TRUE);
        
        $conditions = array();
        if (!empty($xml['condition'][0])) {            
            $conditions = $xml['condition'];
        } else {
            $conditions[] = $xml['condition'];
        }
        
        $index = count($conditions);
        for ($i = 0; $i < $index; $i++) {
            $className = "Pmb\\CMS\\Models\\{$conditions[$i]['class_name']}";
            if (!class_exists($className)) {
                throw new \Exception("Condition doesn't exist ({$className})");
            }

            if (substr($conditions[$i]['label'], 0, 4) == "msg:") {
                $code = substr($conditions[$i]['label'], 4, strlen($conditions[$i]['label']));
                if (isset($msg[$code])) {                    
                    $conditions[$i]['label'] = $msg[$code];
                }
            }
            
            $conditions[$i]['namespace'] = $className;
        }
        return $conditions;
    }
}
