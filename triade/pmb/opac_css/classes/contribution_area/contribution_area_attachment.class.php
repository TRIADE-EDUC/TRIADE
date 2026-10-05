<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_attachment.class.php,v 1.7 2022/05/17 07:31:52 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($include_path . '/h2o/pmb_h2o.inc.php');
require_once ($class_path . '/contribution_area/contribution_area_store.class.php');

class contribution_area_attachment
{

    /**
     * Id de l'attchment
     *
     * @var float
     */
    protected $id;

    /**
     * URI de l'attchment
     *
     * @var string
     */
    protected $uri = "";

    /**
     * Espace de contribution
     *
     * @var contribution_area
     */
    protected $area;

    /**
     * Nom de l'attchment
     *
     * @var string
     */
    protected $name = "";

    /**
     * Commentaire
     *
     * @var string
     */
    protected $comment = "";

    /**
     * Question de l'attchment
     *
     * @var string
     */
    protected $question = "";

    /**
     * Type d'entité de l'attchment
     *
     * @var string
     */
    protected $entity_type;

    /**
     * scénario liés à l'attchment
     */
    protected $scenarios = array();

    public function __construct($id, $area_id)
    {
        $this->id = $id;
        if (intval($area_id)) {
            $this->area = new contribution_area(intval($area_id));
        }
        $this->get_infos();
    }

    protected function get_infos()
    {
        $contribution_area_store = new contribution_area_store();
        $this->uri = $contribution_area_store->get_uri_from_id($this->id);
        $infos = $contribution_area_store->get_infos($this->uri);
        $this->name = $infos['name'] ?? "";
        $this->entity_type = $infos['entityType'] ?? "";
        $this->question = $infos['question'] ?? "";
        $this->comment = $infos['comment'] ?? "";
        //$this->get_scenarios();
    }

    public function get_scenarios()
    {
        if (!empty($this->scenarios)) {
            return $this->scenarios;
        }
        
        $this->scenarios = array();
        $contribution_area_store = new contribution_area_store();
        $graphstore = $contribution_area_store->get_graphstore();

        $query = "select ?uri where {
            <" . $this->uri . "> ca:attachmentDest ?uri .
        }";

        $succes = $graphstore->query($query);
        if ($succes) {
            $results = $graphstore->get_result();
            
            //gestion des droits
            global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
            if (($gestion_acces_active == 1) && ($gestion_acces_empr_contribution_scenario == 1)) {
                $ac = new acces();
                $dom_5 = $ac->setDomain(5);
            }
            
            
            $length = count($results);
            for ($i = 0; $i < $length; $i ++) {
                $infos = $contribution_area_store->get_infos($results[$i]->uri);
                if (!empty($infos["entityType"]) && !empty($this->entity_type) && ($infos["entityType"] != $this->entity_type)) {
                    continue;
                }
                $scenario = new contribution_area_scenario($infos["id"], $this->area->get_id());
                $scenario->get_name();
                $scenario->get_ajax_link();
                
                $access_granted = true;
                if (isset($dom_5)) {
                    if (!$dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id('http://www.pmbservices.fr/ca/Scenario#'.$infos["id"]), 4)) {
                        $access_granted = false;
                    }
                }
                
                if ($access_granted) {
                    $this->scenarios[] = $scenario;
                }
            }
        }

        return $this->scenarios;
    }

    public function render()
    {
        global $include_path;
        
        if (count($this->scenarios) == 1) {
            return $this->scenarios[0]->sub_render();
        } else {
            usort($this->scenarios,  function($a,$b){
                if ($a->get_orderResponse() == $b->get_orderResponse()) {
                    return 0;
                }
                return (intval($a->get_orderResponse()) < intval($b->get_orderResponse()) ? -1 : 1);
            });
            $h2o = H2o_collection::get_instance($include_path . '/templates/contribution_area/contribution_area_attachment.tpl.html');
            return $h2o->render(array(
                'attachment' => $this
            ));
        }
    }

    public function get_uri()
    {
        if (! isset($this->uri)) {
            $this->get_infos();
        }
        return $this->uri;
    }

    public function get_name()
    {
        if (! isset($this->name)) {
            $this->get_infos();
        }
        return $this->name;
    }

    public function get_question()
    {
        if (! isset($this->question)) {
            $this->get_infos();
        }
        return $this->question;
    }

    public function get_comment()
    {
        if (! isset($this->comment)) {
            $this->get_infos();
        }
        return $this->comment;
    }

    public function get_area_uri()
    {
        if (isset($this->area)) {
            return $this->area->get_area_uri();
        }
        return '';
    }
    
    public function set_entity_type($entity_type) {
        $this->entity_type = $entity_type;
    }
}