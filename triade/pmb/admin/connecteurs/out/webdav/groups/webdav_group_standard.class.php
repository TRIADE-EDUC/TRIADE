<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: webdav_group_standard.class.php,v 1.2 2016-03-30 15:31:14 apetithomme Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path.'/admin/connecteurs/out/webdav/groups/webdav_group.class.php');
require_once($class_path."/thesaurus.class.php");

class webdav_group_standard extends webdav_group {
	
	public function get_config_form(){
		global $charset;
		global $thesaurus_default;
		
		
		if(!$this->config['used_thesaurus']){
			$this->config['used_thesaurus'] = $thesaurus_default;
		}
		
		$result.= "
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='used_thesaurus'>".htmlentities($this->msg['webdav_user_thesaurus'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<select name='used_thesaurus'>";
		$liste_thesaurus = thesaurus::getThesaurusList();
		foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
			$result.= "
					<option value='".$id_thesaurus."' ".($id_thesaurus == $this->config['used_thesaurus'] ? "selected='selected'" : "").">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";
		}
		$result.= "
				</select>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='only_with_notices'>".htmlentities($this->msg['webdav_only_with_notices'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				".$this->msg['webdav_yes']."&nbsp;<input type='radio' value='1' name='only_with_notices' ".($this->config['only_with_notices'] ? "checked='checked'" : "")."/>
				".$this->msg['webdav_no']."&nbsp;<input type='radio' value='0' name='only_with_notices' ".($this->config['only_with_notices'] ? "" : "checked='checked'")."/>
			</div>";
		
		$result.= $this->get_collections_tree();
		return $result;
	}
	
	public function get_config_form_script() {
		return $this->get_collections_tree_script();
	}
	
	public static function update_config_from_form(){
		global $used_thesaurus;
		global $only_with_notices;
		
		return array_merge(parent::update_config_from_form(), array(
				'used_thesaurus' => $used_thesaurus,
				'only_with_notices' => $only_with_notices
		));
	}
}