<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_external_sources.class.php,v 1.5 2021/08/10 09:53:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class docwatch_selector_external_sources
 * 
 */
class docwatch_selector_external_sources extends docwatch_selector {
	
	/*
	 * On récupère via le formulaire un tableau d'entrepôt
	 * $this->parameters['external_sources']
	 */
	
	public function get_value(){
	    if(empty($this->value) && is_array($this->parameters['external_sources']) && count($this->parameters['external_sources'])){
		    $this->value = array();
			//vérifions que ce soit toujours des external_sources...
			$query ="select distinct source_id from connectors_sources where source_id in (".implode(",",$this->parameters['external_sources']).") and repository=1";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row=pmb_mysql_fetch_object($result)){
					$requete = "select distinct recid from entrepot_source_".$row->source_id." order by date_import DESC";
					$resultat = pmb_mysql_query($requete);
					$i = 0;
					while($ligne=pmb_mysql_fetch_object($resultat)){
						if($this->parameters['nb_max_elements']==0 || $i < $this->parameters['nb_max_elements']){
							$this->value[] = $ligne->recid;
						}
						$i++;
					}
				}			
			}				
		}	
		return $this->value;
	}
	
	public function get_form(){
		global $msg,$charset;
		
		if(!isset($this->parameters['nb_max_elements'])) $this->parameters['nb_max_elements'] = '';
				
		$form ="
		<div class='row'>
			<div class='colonne3'>
				<label>".htmlentities($msg['dsi_docwatch_selector_external_sources_select'],ENT_QUOTES,$charset)."</label>
			</div> 
			<div class='colonne_suite'>".$this->gen_select()."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label>".htmlentities($msg['dsi_docwatch_selector_external_sources_limit'],ENT_QUOTES,$charset)."</label>
			</div> 
			<div class='colonne_suite'>
				<input type='text' name='docwatch_selector_external_sources_limit' value='".$this->parameters['nb_max_elements']."'/>		
			</div>
		</div>	
		";
		return $form;
	}
	
	public function set_from_form(){
		global $docwatch_selector_external_sources_select;
		global $docwatch_selector_external_sources_limit;
		$this->parameters['external_sources'] = $docwatch_selector_external_sources_select;
		$this->parameters['nb_max_elements'] = (int) $docwatch_selector_external_sources_limit;
	}
	
	
	protected function gen_select(){
	    global $msg, $charset;
		
		if(!isset($this->parameters['external_sources']) || !$this->parameters['external_sources']){
			$this->parameters['external_sources']= array();
		}
		$query ="select source_id, name from connectors_sources where repository='1' order by name";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$select ="
				<select name='docwatch_selector_external_sources_select[]' multiple='yes'>";
			while($row = pmb_mysql_fetch_object($result)){
				$select.="
					<option value='".htmlentities($row->source_id,ENT_QUOTES,$charset)."' ".(in_array($row->source_id,$this->parameters['external_sources']) ? "selected='selected'" : "").">".htmlentities($row->name,ENT_QUOTES,$charset)."</option>";
			}
			$select.="
				</select>";
		}else{
			$select = $msg["dsi_docwatch_selector_external_sources_select_no_repository"];
		}
		
		return $select;
	}
	
} // end of docwatch_selector_external_sources
