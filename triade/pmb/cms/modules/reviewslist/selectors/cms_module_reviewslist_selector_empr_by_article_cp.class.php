<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_reviewslist_selector_empr_by_article_cp.class.php,v 1.3 2022/12/23 10:42:43 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_reviewslist_selector_empr_by_article_cp extends cms_module_common_selector {
	
    public function __construct($id=0){
        parent::__construct($id);
        $this->once_sub_selector=true;
    }
    
    protected function get_sub_selectors(){
        return array(
            "cms_module_common_selector_generic_article",
        );
    }
    
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_reviewslist_selector_articles_by_empr_cp_filter'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.=$this->gen_select();
		$form.="
				</div>
			</div>";
		$form.=parent::get_form();
		return $form;
	}
	
	protected function gen_select(){
		//pour le moment, on ne regarde pas le statut de publication
		$query= "SELECT idchamp, titre 
            FROM cms_editorial_custom 
            JOIN cms_editorial_types ON num_type = id_editorial_type AND editorial_type_element IN ('article_generic', 'article')
            WHERE type = 'query_list'
            AND options LIKE '%empr_nom%'
            ORDER BY titre";// where article_publication_state = 1 ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
		    $select= "
			<select name='".$this->get_form_value_name("cp")."'>
				<option value ='0'>".$this->format_text($this->msg['cms_module_reviewslist_selector_articles_by_empr_cp_no_cp'])."</option>";
		    while($row = pmb_mysql_fetch_object($result)){
		        $select.="
				<option value='".$row->idchamp."' ".(!empty($this->parameters['cp']) && $row->idchamp == $this->parameters['cp'] ? "selected='selected'" : "").">".$this->format_text($row->titre)."</option>";
		    }
		    $select.="
			<select>";
		}
		return $select;
	}
	
		
	
	public function save_form(){
		$this->parameters['cp'] = $this->get_value_from_form("cp");
		return parent ::save_form();
	}
	
	/*
	 * Retourne la valeur s�lectionn�
	 */
	public function get_value(){
	    if($this->parameters['sub_selector'] && !empty($this->parameters["cp"])){
	        $sub_selector = new $this->parameters['sub_selector']($this->get_sub_selector_id($this->parameters['sub_selector']));
	        $id_article = intval($sub_selector->get_value());
	        if ($id_article) {
	            
	            $query="SELECT cms_editorial_custom_integer
                    FROM cms_editorial_custom_values
                    WHERE cms_editorial_custom_origine ='".$id_article."'
                    AND cms_editorial_custom_champ = '".$this->parameters["cp"]."'";
	            $result = pmb_mysql_query($query);
	            if ($result) {
	                if (pmb_mysql_num_rows($result)) {
	                    $row = pmb_mysql_fetch_assoc($result);
	                    return $row["cms_editorial_custom_integer"];
	                }
	            }
	        }
	    }
	    return 0;
	}
}