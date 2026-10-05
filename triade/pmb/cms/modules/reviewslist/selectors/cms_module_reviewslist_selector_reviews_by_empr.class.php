<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_reviewslist_selector_reviews_by_empr.class.php,v 1.2 2022/12/23 10:19:56 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_reviewslist_selector_reviews_by_empr extends cms_module_common_selector{
	
    public function __construct($id=0){
        parent::__construct($id);
        $this->once_sub_selector=true;
    }
    
    protected function get_sub_selectors(){
        return array(
            "cms_module_reviewslist_selector_empr_by_article_cp",
            "cms_module_common_selector_env_var",
            "cms_module_common_selector_global_var",
        );
    }
	
    /*
     * Retourne la valeur s�lectionn�
     */
    public function get_value(){
        $reviews = [];
        if($this->parameters['sub_selector']){
            $sub_selector = new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
            $value = intval($sub_selector->get_value());
            if ($value) {
                $query="SELECT id_avis FROM avis WHERE num_empr ='".$value."'";
                $result = pmb_mysql_query($query);
                if ($result) {
                    if (pmb_mysql_num_rows($result)) {
                        while ($row = pmb_mysql_fetch_assoc($result)) {
                            $reviews[] = $row["id_avis"];
                        }
                    }
                }
            }
        }
        return $reviews;
    }
}