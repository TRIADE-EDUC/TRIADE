<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_reviewslist_selector_articles_by_empr_cp.class.php,v 1.3 2022/12/23 10:19:56 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//require_once($base_path."/cms/modules/common/selectors/cms_module_selector.class.php");
class cms_module_reviewslist_selector_articles_by_empr_cp extends cms_module_common_selector{
	
    public function __construct($id=0){
        parent::__construct($id);
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
			<select name='".$this->get_form_value_name("cp")."' onchange='load_cp_val_".$this->get_form_value_name("cp")."(this.value)'>
				<option value ='0'>".$this->format_text($this->msg['cms_module_reviewslist_selector_articles_by_empr_cp_no_cp'])."</option>";
		    while($row = pmb_mysql_fetch_object($result)){
		        $select.="
				<option value='".$row->idchamp."' ".(!empty($this->parameters['cp']) && $row->idchamp == $this->parameters['cp'] ? "selected='selected'" : "").">".$this->format_text($row->titre)."</option>";
		    }
		    $select.="
			<select>
			<script type='text/javascript'>
				function load_cp_val_".$this->get_form_value_name("cp")."(id_cp){
					dojo.xhrGet({
						url : '".$this->get_ajax_link(array($this->class_name."_hash[]" => $this->hash))."&id_cp='+id_cp,
						handelAs : 'text/html',
						load : function(data){
							dojo.byId('".$this->get_form_value_name("cp")."_values').innerHTML = data;
						}
					});
				}
			</script>
			<div id='".$this->get_form_value_name("cp")."_values'></div>";
		    if(!empty($this->parameters['cp'])){
		        $select.="
			<script type='text/javascript'>
				load_cp_val_".$this->get_form_value_name("cp")."(".$this->parameters['cp'].");
			</script>";
		    }
		}
		return $select;
	}
	
		
	
	public function save_form(){
		$this->parameters['cp'] = $this->get_value_from_form("cp");
		$this->parameters['cp_val'] = (is_array($this->get_value_from_form("cp_val")) ? $this->get_value_from_form("cp_val")[0] : $this->get_value_from_form("cp_val"));
		return parent ::save_form();
	}
	
	/*
	 * Retourne la valeur s�lectionn�
	 */
	public function get_value(){
		if(!$this->value){
			$this->value = $this->parameters;
		}
		return $this->value;
	}
	
	public function execute_ajax(){
		global $id_cp;
		$id_cp = intval($id_cp);
		$response['content'] = "";
		if($id_cp){
			$query = "SELECT type, num_type FROM cms_editorial_custom WHERE idchamp = $id_cp ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
    			$row = pmb_mysql_fetch_assoc($result);
    			$response['content'].="
    			<div class='colonne3'>
    			<label>".$this->format_text($this->msg['cms_module_reviewslist_selector_articles_by_empr_cp_label'])."</label>
    			</div>
    			<div class='colonne_suite'>";
    			//on regarde la nature du CP...
    			$cms_pp = new cms_editorial_parametres_perso($row['num_type']);
    			//un peu de manipulation du champ pour que l'affichage sous forme d'autorite fonctionne
    			$cms_pp->t_fields[$id_cp]["VALUES"] = [$this->parameters['cp_val']];
    			$cms_pp->t_fields[$id_cp]["ID"] = $id_cp;
    			$cms_pp->t_fields[$id_cp]["NAME"] = $this->get_form_value_name("cp_val");
    			$check_scripts = "";
    			$response['content'].= aff_query_list_empr($cms_pp->t_fields[$id_cp], $check_scripts, "", "cms_module_reviewslist_form");
			}
		}
		$response['content-type'] = "text/html";
		return $response;
	}
}