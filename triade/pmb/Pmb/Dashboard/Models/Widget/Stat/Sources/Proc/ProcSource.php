<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ProcSource.php,v 1.5 2024/03/01 16:12:54 dbellamy Exp $

namespace Pmb\Dashboard\Models\Widget\Stat\Sources\Proc;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Dashboard\Models\Widget\Common\AbstractSource;

global $include_path;
require_once("$include_path/fields.inc.php");

class ProcSource extends AbstractSource
{

    protected static $configuration_filename = "ProcSource";

    public function __construct()
    {
        static::$configuration_filename = __DIR__ . DIRECTORY_SEPARATOR . static::$configuration_filename;
    }

    public function getData($params = null)
    {
        $data = [];

        if(empty($params->id)) {
            return $data;
        }

        $query = $this->getFinalQuery($params);
        if(empty($query)) {
            return $data;
        }

        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result) == 0) {
            return $data;
        }
        $firstRow = pmb_mysql_fetch_assoc($result);

        // si moins de 3 colonnes, affichage en graphe possible
        if(count($firstRow) < 3 ) {
        
            $dataset_label = array_keys($firstRow);
            $firstKey = array_shift($dataset_label);

            $data["dataset_label"] = $dataset_label;
            $data["labels"][] = $firstRow[$firstKey];

            foreach($dataset_label as $key) {
                $data["dataset"][] = $firstRow[$key];
            }

            while ($row = pmb_mysql_fetch_assoc($result)) {
                $i = 0;
                foreach($row as $key => $value) {
                    if($i == 0) {
                        $data["labels"][] = $value;
                    } else {
                        $data["dataset"][] = $value;
                    }
                    
                    $i++;
                }
            }
            $data['graphEnabled'] = 1;
        // sinon affichage en table
        } else {                                                                    

            $data['labels'] = array_keys($firstRow); 
            $data["dataset_label"] = "";
            $i = 0;
            $j = 0;
            foreach($firstRow as $value) {
                $data["dataset"][$i][$j] = $value;
                $j++;
            }
            $i++;
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $j = 0;
                foreach($row as $value) {
                    $data["dataset"][$i][$j] = $value;
                    $j++;
                }
                $i++;
            }
            $data['graphEnabled'] = 0;
        }

        return $data;
    }

    private function getFinalQuery($params)
    {
        global $chk_list, $val_list;

        $parameters = new \parameters($params->id, "procs");
        foreach($parameters->query_parameters as $parameter) {
            $name = $parameter;

            global ${$name};
            ${$name} = $params->conditions->$parameter ?? "";

            $fieldType = $parameters->get_field_type($parameters->parameters_description[$parameter]);
            
            $checkFunction = $chk_list[$fieldType];
            $parameterDescription = $parameters->parameters_description[$parameter];

            $checkMessage = "";
            $chk = $checkFunction($parameterDescription, $checkMessage);
            if(!$chk) {
                return "";
            }
        }

        $request = $parameters->proc->requete;
        foreach($parameters->query_parameters as $parameter) {
            $fieldType = $parameters->get_field_type($parameters->parameters_description[$parameter]);

            $valFunction = $val_list[$fieldType];
            $parameterDescription = $parameters->parameters_description[$parameter];

            $val = $valFunction($parameterDescription);
            $request = str_replace("!!" . $parameter . "!!", $val, $request);
        }

        return $request;
    }

    public function getConfiguration($full = false)
    {
        global $msg;

        if (!is_null(static::$configuration)) {
            return static::$configuration['conditions'];
        }

        static::$configuration = [];

        // recuperation des procedures 
        $procs = [];
        $used_classements = [];
        $q = "select idproc as id, name as label, requete as requete, comment as comment, num_classement as id_classement
            from procs 
            where 1";
        // $q .= " and idproc=386";
        // $q .= " and idproc=461 ";
        // $q .= " and num_classement=5";
        $q .= " and convert(requete using latin1) like 'select%'";
        $q .= " and parameters not like ('%<TYPE>file_box</TYPE>%')";
        $q .= " and parameters not like ('%<TYPE>selector</TYPE>%')";
        $q .= " order by num_classement, label";
        $r = pmb_mysql_query($q);
       
        if(pmb_mysql_num_rows($r)) {
            while ($row = pmb_mysql_fetch_array($r, PMB_MYSQL_ASSOC)) {
                $procs[$row['id_classement']][] = [
                    'id' => $row['id'],
                    'label' => $row['label'],
                    'comment' => $row['comment'],
                ];

                $used_classements[] =  $row['id_classement'];
            }
        };

        // recuperation des classements de procedures
        $used_classements = array_unique($used_classements);
        $procs_classements = [];

        $procs_classements[] = [
            'id' => 0,
            'label' => $msg["proc_clas_aucun"],
            'procs' => $procs[0],
        ];

        $q = "select idproc_classement as id, libproc_classement as label from procs_classements";
        $q.= " where idproc_classement in (".implode(',', $used_classements).")";
        $q.= " order by label";
        $r = pmb_mysql_query($q);
        if(pmb_mysql_num_rows($r)) {
            while ($row = pmb_mysql_fetch_array($r, PMB_MYSQL_ASSOC)) {
                $procs_classements[] = [
                    'id' => $row['id'],
                    'label' => $row['label'],
                    'procs' => $procs[$row['id']],
                ];
            }
        };
        static::$configuration['methods'] = $procs_classements;
        return static::$configuration;
    }

    public function getConditions($params) 
    {
        $id = intval($params->id ?? 0);
        if(!$id) {  
            return [];
        }

        $parameters = new \parameters($id, 'procs');

        $conditions = ($parameters->parameters_description) ?? [];
        
        $valid_conditions = [];
        $k_condition = 0;
        foreach($conditions as $condition) {

            $type = $condition['TYPE'][0]['value'] ?? 'text';
            
            $valid_conditions[$k_condition] = [
                'name' => $condition['NAME'],
                'mandatory' => ($condition['MANDATORY'] == 'yes') ? 1 : 0 ,             
                'label' => ($condition['ALIAS'][0]['value']) ?? '' ,
                'type' => $type,
            ];
            switch($type)  {

                case  'text' :
                    $valid_conditions[$k_condition]['options'] = [
                        'size' => $condition['OPTIONS'][0]['SIZE'][0]['value'] ?? '20',
                        'maxsize' => $condition['OPTIONS'][0]['MAXSIZE'][0]['value'] ?? '255',
                    ];
                    break;

                case 'list' :
                    $valid_conditions[$k_condition]['options'] = [
                        'multiple' => ($condition['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes') ? 1 : 0,
                    ];
                    if($condition['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE']) {
                        $valid_conditions[$k_condition]['options']['default'] = [
                            $condition['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'],
                            $condition['OPTIONS'][0]['UNSELECT_ITEM'][0]['value'],
                        ];
                    }
                    if( isset($condition['OPTIONS'][0]['ITEMS']) )  {
                        foreach($condition['OPTIONS'][0]['ITEMS'][0]['ITEM'] as $item) {

                            $valid_conditions[$k_condition]['options']['values'][] = [
                                $item['VALUE'],
                                $item['value'],
                            ];
                        }
                    }
                    break;

                case 'query_list' :
                    $valid_conditions[$k_condition]['options'] = [
                        'multiple' => ($condition['OPTIONS'][0]['MULTIPLE'][0]['value'] == 'yes') ? 1 : 0,
                    ];
                    if($condition['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE']) {
                        $valid_conditions[$k_condition]['options']['default'] = [
                            $condition['OPTIONS'][0]['UNSELECT_ITEM'][0]['VALUE'],
                            $condition['OPTIONS'][0]['UNSELECT_ITEM'][0]['value'],
                        ];
                    }
                    $q = ($condition['OPTIONS'][0]['QUERY'][0]['value']) ?? '';
                    $values = [];
                    if($q) {
                        $r = pmb_mysql_query($q);
                        if (pmb_mysql_num_rows($r)) {
                            $values = pmb_mysql_fetch_all($r);
                        }

                    }
                    if(!empty($values)) {
                        $valid_conditions[$k_condition]['options']['values'] = $values;
                    }
                    break;

                case 'date_box' :
                        // Rien de plus a faire
                    break;

                case 'file_box' :
                        // non gere pour l'instant
                    break;

                case 'selector' :
                        // non gere pour l'instant
                    break;

                default :
                    break;
            }
            $k_condition++;
        }
        return $valid_conditions;
    }

}

