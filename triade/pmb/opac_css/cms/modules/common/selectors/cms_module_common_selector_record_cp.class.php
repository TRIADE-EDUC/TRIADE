<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_record_cp.class.php,v 1.2.16.1 2025/05/07 14:49:15 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_common_selector_record_cp extends cms_module_common_selector{

    public function __construct($id=0){
        parent::__construct($id);
    }

    public function get_form(){
        // $form.=parent::get_form();
        $form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='".$this->get_form_value_name("cp")."'>".$this->format_text($this->msg['cms_module_common_selector_record_cp_val_cp_label'])."</label>
				</div>
				<div class='colonne_suite'>
                    ".$this->gen_select()."
				</div>
			</div>";
        return $form;
    }

    public function gen_select(){
        $query = "select idchamp,titre from notices_custom";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $select= "<select name='".$this->get_form_value_name("cp")."'>";
            while($row = pmb_mysql_fetch_object($result)){
                $select.="
				        <option value='".$row->idchamp."' ".($row->idchamp == $this->parameters['cp'] ? "selected='selected'" : "").">".$this->format_text($row->titre)."</option>";
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
     * Retourne la valeur sélectionné
     */
    public function get_value(){
        if(!$this->value){
            $this->value = $this->parameters['cp'];
        }
        return $this->value;
    }
}