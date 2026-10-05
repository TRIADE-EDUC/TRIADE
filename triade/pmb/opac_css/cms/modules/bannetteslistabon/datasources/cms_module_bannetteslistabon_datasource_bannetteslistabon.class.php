<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannetteslistabon_datasource_bannetteslistabon.class.php,v 1.6 2024/03/07 08:26:23 jparis Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $base_path, $class_path;
require_once $class_path . "/bannette.class.php";

//Pour gerer la difference gestion/opac
if(file_exists($base_path.'/includes/empr_func.inc.php')){
    require_once($base_path.'/includes/empr_func.inc.php');
}
if(file_exists($base_path.'/includes/websubscribe.inc.php')){
    require_once($base_path.'/includes/websubscribe.inc.php');
}

class cms_module_bannetteslistabon_datasource_bannetteslistabon extends cms_module_common_datasource_list
{
    
    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->sortable = true;
        $this->limitable = false;
    }
    
    /*
     * Definition des selecteurs utilisables pour cette source de donnees
     */
    public function get_available_selectors()
    {
        return array("cms_module_common_selector_bannettes_generic");
    }
    
    /*
     * Definition des criteres de tri utilisable pour cette source de donnees
     */
    protected function get_sort_criterias()
    {
        return array(
            "id_bannette",
            "nom_bannette",
            "comment_public",
            "date_last_remplissage",
            "date_last_envoi",
            
        );
    }
    
    /*
     * Recuperation des donnees de la source...
     */
    public function get_datas()
    {
        $selector = $this->get_selected_selector();
        if ($selector) {
            $return = array();
            if (is_countable($selector->get_value()) && count($selector->get_value()) > 0) {
                foreach ($selector->get_value() as $value) {
                    $return[] = $value;
                }
            }
            $categories = array();
            if (count($return)) {
                $query = "SELECT id_bannette, nom_bannette, comment_public, id_classement, classement_opac_name, classement_order
                          FROM bannettes LEFT JOIN classements ON classements.id_classement = bannettes.num_classement
                          WHERE id_bannette in (" . implode(",", $return) . ")";
                
                if (!empty($this->parameters["sort_by"])) {
                    $query .= " ORDER BY " . addslashes($this->parameters["sort_by"]);
                    if (!empty($this->parameters["sort_order"])) $query .= " " . addslashes($this->parameters["sort_order"]);
                }
                $result = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($result)) {
                    $return = array();
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $flux_rss = array();
                        $i = 0;
                        $query2 = "select num_rss_flux from  rss_flux_content where type_contenant='BAN' and num_contenant=" . $row->id_bannette;
                        $result2 = pmb_mysql_query($query2);
                        if (pmb_mysql_num_rows($result2)) {
                            while ($row2 = pmb_mysql_fetch_object($result2)) {
                                $flux_rss[$i]['id'] = $row2->num_rss_flux;
                                $flux_rss[$i]['name'] = $row2->nom_rss_flux;
                                $flux_rss[$i]['opac_link'] = "./rss.php?id=" . $row2->num_rss_flux;
                                $flux_rss[$i]['link'] = $row2->link_rss_flux;
                                $flux_rss[$i]['lang'] = $row2->lang_rss_flux;
                                $flux_rss[$i]['copy'] = $row2->copy_rss_flux;
                                $flux_rss[$i]['editor_mail'] = $row2->editor_rss_flux;
                                $flux_rss[$i]['webmaster_mail'] = $row2->webmaster_rss_flux;
                                $flux_rss[$i]['ttl'] = $row2->ttl_rss_flux;
                                $flux_rss[$i]['img_url'] = $row2->img_url_rss_flux;
                                $flux_rss[$i]['img_title'] = $row2->img_title_rss_flux;
                                $flux_rss[$i]['img_link'] = $row2->img_link_rss_flux;
                                $flux_rss[$i]['format'] = $row2->format_flux;
                                $flux_rss[$i]['content'] = $row2->rss_flux_content;
                                $flux_rss[$i]['date_last'] = $row2->rss_flux_last;
                                $flux_rss[$i]['export_court'] = $row2->export_court_flux;
                                $flux_rss[$i]['link'] = $row2->link_rss_flux;
                                $flux_rss[$i]['template '] = $row2->tpl_rss_flux;
                                
                                $i++;
                            }
                        }
                        $bannette = bannette::get_instance($row->id_bannette);
                        $return[] = array("id" => $row->id_bannette, "name" => $row->nom_bannette, "comment" => $bannette->get_render_comment_public(), "flux_rss" => $flux_rss);
                        
                        if(!isset($categories[$row->classement_order]) || !is_array($categories[$row->classement_order])) {
                            $categories[$row->classement_order] = array("id" => $row->id_classement, "name" => $row->classement_opac_name);
                        }
                        $categories[$row->classement_order]['bannettes'][] = array("id" => $row->id_bannette, "name" => $row->nom_bannette, "comment" => $bannette->get_render_comment_public(), "flux_rss" => $flux_rss);
                    }
                }
            }
            return [
                'bannettes' => $return,
                'categories' => $categories
            ];
        }
        return false;
    }
}
