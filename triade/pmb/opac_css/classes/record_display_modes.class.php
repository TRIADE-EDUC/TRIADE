<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_display_modes.class.php,v 1.13.4.2 2025/03/20 10:04:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class record_display_modes {
	private $filename;
	private $modes;
	private static $instance;
	
	public function __construct(){
		$this->get_modes_from_description_file();
		$this->analyse();
	}
	
	private function get_modes_from_description_file(){
		global $opac_notices_display_modes;
		
		$this->filename = $opac_notices_display_modes;
		$this->filename=str_replace(array(".xml",".XML"), "", $this->filename);
	}
	
	/**
	 * On parse le fichier xml des modes d'affichage.
	 */
	private function analyse(){
		global $include_path;
		
		if(file_exists($include_path."/records/".$this->filename."_subst.xml")){
			$filepath = $include_path."/records/".$this->filename."_subst.xml";
		}else if (file_exists($include_path."/records/".$this->filename.".xml")){
			$filepath = $include_path."/records/".$this->filename.".xml";
		}else{
			$filepath = $include_path."/records/display_modes.xml";
		}
		
		$fp = fopen($filepath,"r");
		$xml=fread($fp,filesize($filepath));
		fclose($fp);
		$this->modes =_parser_text_no_function_($xml, "MODES");
		if (!isset($this->modes['MODE'])) {
			$this->modes['MODE'] = [];
		}
	}
	
	/**
	 * Retourne un mode en fonction de son ID
	 * 
	 * @param int $mode_id l'identifiant d'un mode
	 * @return array le tableau correspondant au mode recherché
	 */
	public function get_mode($mode_id){
		if(is_countable($this->modes['MODE']) && sizeof($this->modes['MODE'])){
			foreach($this->modes['MODE'] as $mode_offset=>$mode){
				if($mode['ID']==$mode_id){
					return $this->modes['MODE'][$mode_offset];
				}
			}
		}
		return false;
	}
	
	/**
	 * Compare les types de doc autorisés dans le mode avec les types de doc dans le résultat.
	 * 
	 * @param int $mode_id l'identifiant du mode à comparer
	 * @return boolean comparaison vrai ou fausse
	 */
	private function compare_typdoc($mode_id){
		global $l_typdoc;
		
		$tab_typdoc_result=explode(",",$l_typdoc);
		
		$mode=$this->get_mode($mode_id);
		
		$return=false;
		if(isset($mode['DOCTYPES'][0]['value']) && $mode['DOCTYPES'][0]['value'] && $tab_typdoc_mode=explode(",",$mode['DOCTYPES'][0]['value'])){
			$return=true;
			foreach($tab_typdoc_result as $typdoc_result){
				if(!in_array($typdoc_result, $tab_typdoc_mode)){
					$return=false;
				}
			}
		}elseif(empty($mode['DOCTYPES'])){
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Retourne le mode courrant à utiliser pour un resultat de recherche
	 * en fonction de la sesson, et du paramètrage dans le fichier xml
	 * 
	 * @return int $mode_id l'identifiant du mode à utiliser
	 */
	public function get_current_mode(){
		//On rafraichit (si jamais on est sur une vue avec un paramètre substitué....)
		$this->get_modes_from_description_file();
		$this->analyse();
		
		$mode_id=0;
		
		$mode_id_selected=0;
		$mode_id_auto=0;
		$mode_id_default=0;
		
		if((!empty($_SESSION['user_current_mode']) && $this->compare_typdoc($_SESSION['user_current_mode'])) ||  (isset($_SESSION['user_current_mode']) && $_SESSION['user_current_mode']==="0")){
			$mode_id_selected=$_SESSION['user_current_mode'];
		}
		
		$available_modes = array();
		if(!empty($this->modes['NOMODE'])){
			$available_modes[] = 0;
		}
		if(!empty($this->modes['MODE']) && is_countable($this->modes['MODE']) && sizeof($this->modes['MODE'])){
			foreach($this->modes['MODE'] as $mode){
				if(isset($mode['DOCTYPES'][0]['AUTO']) && $mode['DOCTYPES'][0]['AUTO']=='yes' && $this->compare_typdoc($mode['ID'])){
					//Mode auto
					$mode_id_auto= $mode['ID'];
				}
				if($mode['DEFAULT']=='yes'){
					//mode par défaut
					$mode_id_default= $mode['ID'];
				}
				$available_modes[] = $mode['ID'];
			}
		}
		
		if($mode_id_selected || $mode_id_selected==="0"){
			//on vérifie que le mode est disponible
			if (!in_array($mode_id_selected,$available_modes)) {
				if($mode_id_auto){
					$mode_id=$mode_id_auto;
				}elseif($mode_id_default){
					$mode_id=$mode_id_default;
				}
				$this->set_user_current_mode($mode_id);
			} else {
				$mode_id=$mode_id_selected;
			}
		}elseif($mode_id_auto){
			$mode_id=$mode_id_auto;
		}elseif($mode_id_default){
			$mode_id=$mode_id_default;
		}
		
		return $mode_id;
	}
	
	/**
	 * Retourne la bonne fonction en fonction du mode $mode_id
	 * 
	 * @param int $mode_id le mode courrant
	 * @return unknown|boolean
	 */
	public function get_aff_function($mode_id){
		global $class_path,$include_path;
		$mode=$this->get_mode($mode_id);
		if($aff_notice_fonction=$mode['FUNCTION'][0]['SRC']){
			if(file_exists($include_path."/".$aff_notice_fonction.".inc.php")){
				require_once($include_path."/".$aff_notice_fonction.".inc.php");
				return $aff_notice_fonction;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * Retourne l'identifiant du template pour l'objet mode passé en param
	 * 
	 * @param unknown $mode
	 * @return unknown|number
	 */
	public function get_template_id($mode_id){
		$mode=$this->get_mode($mode_id);
		$template_id=$mode['TEMPLATE'][0]['TEMPLATE_ID'];
		if($template_id){
			return $template_id;
		}else {
			return 0;
		}
	}
	
	/**
	 * Retourne le code du template si il est saisi dans le fichier xml
	 * 
	 * @param unknown $mode
	 * @return unknown|number
	 */
	public function get_template_code($mode_id){
		$mode=$this->get_mode($mode_id);
		$code=$mode['TEMPLATE'][0];
		if(is_countable($code) && sizeof($code)){
			return $code;
		}else {
			return 0;
		}
	}
	
	/**
	 * Retourne le répertoire de template à utiliser dans le cas du type django
	 * @param int $mode_id
	 * @return string
	 */
	public function get_template_directory($mode_id){
		$mode = $this->get_mode($mode_id);
		$template_mode = $mode['TEMPLATE'][0]['DIRECTORY'];
		if ($template_mode) {
			return $template_mode;
		} else {
			return "";
		}
	}
	
	/**
	 * Retourne les informations du layout
	 * 
	 * @param unknown $mode_id
	 * @return Ambigous <multitype:, boolean>|number
	 */
	public function get_layout($mode_id){
		$mode=$this->get_mode($mode_id);
		$layout=$mode['LAYOUT'][0];
		if($layout){
			return $layout;
		}else {
			return 0;
		}
	}
	
	/**
	 * Enregistre le mode choisi par l'utilisateur en session
	 * 
	 * @param int $user_current_mode le mode choisi par l'utilisateur
	 */
	public function set_user_current_mode($user_current_mode){
		$_SESSION['user_current_mode']=$user_current_mode;
	}
	
	/**
	 * On affiche le menu d'affichage
	 * 
	 * @return string
	 */
	public function show_mode_selector() {
		//On rafraichit (si jamais on est sur une vue avec un paramètre substitué....)
		$this->get_modes_from_description_file();
		$this->analyse();
		
		$current_mode = $this->get_current_mode();
		$nb_modes = 0;
		
		$html = "<ul class='mode_selector_list'>";
		
		// Le mode par défaut 
		if (!empty($this->modes['NOMODE'])) {
			$selected = '';
			if ($current_mode == 0) {
				$selected = '_selected';
			}
			$html .= "<li class='mode_selector$selected' onclick='switch_mode(0)' title='".$this->modes['NOMODE'][0]['NAME']."'>
                          <img src='".$this->get_icon_url($this->modes['NOMODE'][0]['ICON'])."' alt='".$this->modes['NOMODE'][0]['NAME']."'/>
                      </li>";
			$nb_modes++;
		}
		
		foreach ($this->modes['MODE'] as $mode) {
			if ($this->compare_typdoc($mode['ID']) || !$mode['DOCTYPES']) {
				$selected = '';
				if ($current_mode == $mode['ID']) {
					$selected = '_selected';
				}
				$html .= "<li class='mode_selector$selected' onclick='switch_mode(".$mode['ID'].")' title='".$mode['NAME']."' $selected>
                              <img src='".$this->get_icon_url($mode['ICON'])."' alt='".$mode['NAME']."'/>
                          </li>";
				$nb_modes++;
			}
		}
		
		// Si on n'a pas ou qu'un seul mode disponible, ça ne sert à rien d'aller plus loin
		if ($nb_modes <= 1) {
			return '';
		}
		
		$html .= "</ul>";
		
		$html .= "
		<script>
			function switch_mode(id_mode){
				
				var formName='';
				
				for(var iForm in document.forms){
					
					if(document.forms[iForm].nodeName=='FORM'){
						var replace = false;
						for(var iInput in document.forms[iForm].children){
							if(document.forms[iForm].children[iInput].name=='user_current_mode'){
								document.forms[iForm].children[iInput].value=id_mode;
								replace=true;
							}
						}
						
						if(!replace){
							var user_current_mode='';
						
							user_current_mode=document.createElement('input');
							user_current_mode.setAttribute('name','user_current_mode');
							user_current_mode.setAttribute('value',id_mode);
							user_current_mode.setAttribute('type','hidden');
											
							try{
							 	document.forms[iForm].appendChild(user_current_mode);
							}catch(e){
								
							}
						}
						
						if(document.forms[iForm].name=='form_values'){
							formName='form_values';
						}
						
						if(!formName && document.forms[iForm].name=='form'){
							formName='form';
						}
					}
				}
				
				document.getElementsByName(formName)[0].submit();
				
			}
		</script>
		";
		
		return $html;
	}
	
	/**
	 * retourne l'url de l'icone à afficher dans la liste de choix
	 *
	 * @param string $name le nom de l'icone
	 * @return string le path de l'icone
	 */
	private function get_icon_url($name){
		global $css;
		global $base_path;
	
		$src='';
		if(file_exists($base_path.'/styles/'.$css.'/images/'.$name)){
			$src=$base_path.'/styles/'.$css.'/images/'.$name;
		}elseif(file_exists($base_path.'/styles/common/images/'.$name)){
			$src=$base_path.'/styles/common/images/'.$name;
		}elseif(file_exists($base_path.'/images/'.$name)){
			$src=$base_path.'/images/'.$name;
		}
		return $src;
	}
	
	/**
	 * renseigne l'affichage ou non de la navigation
	 * @param int $mode_id
	 */
	public function is_nav_displayed($mode_id) {
		$mode=$this->get_mode($mode_id);
		$layout=(isset($mode['LAYOUT'][0]) ? $mode['LAYOUT'][0] : '');
		if(isset($layout['NAV']) && $layout['NAV'] == 'no'){
			return false;
		}
		return true;
	}
	
	public static function get_instance() {
		global $opac_notices_display_modes;
		global $lvl;
		global $user_current_mode;
		
		if(!isset(static::$instance)) {
			static::$instance = '';
			//on utilise le système de choix des modes d'affichage
			if($opac_notices_display_modes && $lvl != "notice_display" && $lvl != "bulletin_display" && $lvl != "show_cart"){
				//le selecteur de mode d'affichage
				static::$instance = new record_display_modes();
				if((isset($user_current_mode) && $user_current_mode) || $user_current_mode==='0'){
					//Si on a dans le post la variable $user_current_mode qui determine un choix utilisateur (envoyé par les formulaires)
					static::$instance->set_user_current_mode($user_current_mode);
				}
			}
		}
		return static::$instance;
	}
}