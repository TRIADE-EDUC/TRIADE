<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autoindex_record.class.php,v 1.22 2023/08/28 14:04:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/autoindex/autoindex_document.class.php");

require_once("$class_path/marc_table.class.php");
require_once("$class_path/category.class.php");
require_once("$class_path/thesaurus.class.php");
require_once("$class_path/notice_doublon.class.php");

class autoindex_record extends autoindex_document {
	
	
	/**
	 * Identifiant de la notice
	 * @var integer
	 * @access protected
	 */
	protected $record_id=0;
	
	/**
	 * Liste des champs, sous-champs utiles
	 * $this->fields_list =array(
	 *   array(
	 *     'field'=>1,
	 *     'subfields' => array(
	 *       2,3
	 *     )
	 *   )
	 * @var array
	 * @access protected
	 */
	protected $fields_list = array(
								array(
										'field'=>1,
										'subfields'=>array(0),
									),
								array(
										'field'=>14,
										'subfields'=>array(0),
									)
								);
	
	
	public function __construct() {
		
		$this->collection = new autoindex_documents_collection($this->fields_list);
	
	}
	
	
	/**
	 * fonction de test
	 */
	public function test($raw_text='', $lang='fr_FR', $id_thesaurus=0) {
	
		$this->raw_text=$raw_text;
		$this->lang=$lang;
		$this->id_thesaurus=$id_thesaurus;
	}
	
	public function process() {	
		$this->get_raw_text();
		$this->get_lang();	
		$this->get_thesaurus();
		$this->find_revelants_words(); 
		$this->get_relevants_terms();
		$this->calc_total_terms_relevancy();
		$this->calc_document_terms_distances();
		$this->sort_terms();
	}
		
	/**
	 * Récupère le contenu des champs de la notice à indexer
	 *
	 * @global autoindex  
	 * json array(
	 * 		array (
	 * 			[name]=nom de la zone,
	 * 			[field]=nom du champ dans le formulaire,
	 * 			[pond]=ponderation,
	 * 			[value]=contenu de la zone
	 * 		)
	 * )
	 * 
	 * @return array(string)
	 * @access public
	 */
	public function get_raw_text() {
		
		global $autoindex_txt,$charset;
		
		$this->raw_text = json_decode(stripslashes($autoindex_txt),true);
		if(is_array($this->raw_text) && count($this->raw_text)) {
			foreach($this->raw_text as $k=>$v) {
				if(!is_null($v['value']) && $v['value']!=='') {
					$this->raw_text[$k]['value']=rawurldecode($v['value']);
					if($charset!='utf-8') {
						$this->raw_text[$k]['value'] = encoding_normalize::utf8_decode($this->raw_text[$k]['value']);
					}
				} else {
					unset($this->raw_text[$k]);
				}
			}
		}		
//TODO
// echo "Eléments postés =<br />";
// print $autoindex_txt."<br />";
// highlight_string(print_r($this->raw_text,true));
// echo "<br />";

		return $this->raw_text;
	}
	
	
	/**
	 * Récupère la langue de l'interface
	 *
	 * @return string
	 * @access public
	 */
	public function get_lang() {
		global $user_lang, $lang;
		
		if(!$user_lang){
			$user_lang=$lang;
		}
		if(!$user_lang) {
			$user_lang="fr_FR";
		}
		
		$this->lang=$user_lang;

//TODO
// echo "Langue de la notice = ".$user_lang."<br />";
// echo "Langue utilisateur = ".$lang."<br />";
// echo "Langue indexation = ".$this->lang."<br />";
// echo "<br />";

		return $this->lang;
	}
	
	
	/**
	 * Récupère l'identifiant du thésaurus à utiliser pour la recherche de termes.
	 *
	 * @return integer
	 * @access public
	 */
	public function get_thesaurus() {
		global $id_thes;
		$this->id_thesaurus=$id_thes;
		if($this->id_thesaurus < 0 ) {
			$this->id_thesaurus=0;
		}
		return $id_thes;
	}	
	
	public function get_form() {
				
		global $charset;
		global $msg;
		global $caller,$thesaurus_auto_index_notice_fields,$lang,$include_path,$search_type,$user_lang,$xmlta_indexation_lang;
		global $htmlfieldstype;
		
		if(!$htmlfieldstype) {
			$htmlfieldstype="html";
		}
		
		$tpl_index_auto="";
		if ($caller=='notice' && $thesaurus_auto_index_notice_fields) {
			$auto_index_notice_fields = str_replace(array("\\n","\\r","\n","\r"), "", $thesaurus_auto_index_notice_fields);
			$fields=explode(';',$auto_index_notice_fields);
		
			$notice_fields=new notice_doublon();
			$tpl_field = array();
			$tpl_selector_field='<table><tbody><tr>';
			$j=0;
			$i=0;
			
			foreach($fields as $k=>$field){
				$pos = stripos($field,'=');
				$pos_selected = stripos($field, '|');
				$field_default_selected = 0;
				if($pos!==false) {
					$field_name = trim(substr($field,0,$pos));
					if($pos_selected!==false) {
						$field_pond = trim(substr($field,$pos+1, $pos_selected));
						$field_default_selected = trim(substr($field,$pos_selected+1));
					} else {
						$field_pond = trim(substr($field,$pos+1));
					}
					$field_pond = (float) str_replace(',','.',$field_pond);
					if($field_pond > 1 && $field_pond <=100) {
						$field_pond = $field_pond / 100;
						$field_pond = round($field_pond,2);
					}
				} else {
					if($pos_selected!==false) {
						$field_name = trim(substr($field,0,$pos_selected));
						$field_default_selected = trim(substr($field,$pos_selected+1));
					} else {
						$field_name = trim($field);
					}
					$field_pond = 1;
				}			
				
				if ($field_name) {
					
					if($notice_fields::$fields[$field_name][$htmlfieldstype]){
						$tpl_field[$i]['name'] = $field_name;
						$tpl_field[$i]['field'] = $notice_fields::$fields[$field_name][$htmlfieldstype];
						$tpl_field[$i]['pond'] = $field_pond;
						
						if($field_name=="tit1"){ // cas du formulaire de bulletin ou le titre = bul_titre au lieu de tit1
							$i++;
							$tpl_field[$i]['name'] = $field_name;
							$tpl_field[$i]['field'] = 'bul_titre';
							$tpl_field[$i]['pond'] = $field_pond;
						}
					}else{
						// champ perso
						$tpl_field[$i]['name'] = $field_name;
						$tpl_field[$i]['field'] = $field_name;
						$tpl_field[$i]['pond'] = $field_pond;
					}	
					
					$checked = '';
					if( ($search_type!='autoindex') || (isset($_POST['chk_'.$tpl_field[$i]['name']])) 
							|| (!isset($_POST['chk'.$tpl_field[$i]['name']]) && !isset($_POST['chk_hidden'.$tpl_field[$i]['name']]) && $field_default_selected) ) {
						$checked="checked='checked'";
					}
					
					if($j%3==0) {
						$tpl_selector_field.= '</tr><tr>';
					}
					$tpl_selector_field.= "<td><input type='checkbox' id='chk_".$tpl_field[$i]['name']."' name='chk_".$tpl_field[$i]['name']."' value='1' ".$checked." />";
					$tpl_selector_field.= "<input type='hidden' id='chk_hidden_".$tpl_field[$i]['name']."' name='chk_hidden_".$tpl_field[$i]['name']."' value='1' />&nbsp;";
					$tpl_selector_field.= "<label for='chk_".$tpl_field[$i]['name']."'>".htmlentities($notice_fields::$fields[$field_name]['label'],ENT_QUOTES,$charset)."</label></td>";
					$j++;
					$i++;
				}
			}	
			while($j%3) {
				$tpl_selector_field.="<td></td>";
				$j++;
			}
			$tpl_selector_field.='</tr></tbody></table>';
			
			$langues = new XMLlist("$include_path/messages/languages.xml");
			$langues->analyser();
			$clang = $langues->table;
			$display='';
			if($search_type!='autoindex') {
				$display="style='display:none'";
			}
			$combo = "
				<div id='autoindex_selectors' $display >
				<div id='autoindex_selector_lang'>".$msg["autoindex_selector_lang"].
				"<select name='user_lang' id='user_lang' class='saisie-20em' \">";
			//Langue par défaut d'indexation de notice
			if(empty($user_lang)) {
				$user_lang = $xmlta_indexation_lang;
			}
			if(!$user_lang) {
				$combo .= "<option value='' selected='selected' >--</option>";
			} else {
				$combo .= "<option value='' >--</option>";
			}
			foreach ($clang as $cle => $value) {
				// arabe seulement si on est en utf-8
				if (($charset != 'utf-8' and $user_lang != 'ar') or ($charset == 'utf-8')) {
					if(strcmp($cle, $user_lang) != 0) {
						$combo .= "<option value='$cle'>$value ($cle)</option>";
					} else {
						$combo .= "<option value='$cle' selected='selected' >$value ($cle)</option>";
					}
				}
			}
			$combo .= "</select></div>";
			$combo.= "<div id='autoindex_selector_field'>$tpl_selector_field</div>";
			$combo.= "<input type='button' class='bouton_small' id='refresh_autoindex_search_button' value='".$msg['autoindex_do']."' onClick=\"autoindex_get_index();\" />";
			$combo.= "</div>";
					
			$tpl_index_auto="
			<script type='text/javascript'>
				var fields_index_auto = ".json_encode($tpl_field).";
				
				
				function autoindex_get_index(){
					
					if(!parent.window.parent.document.forms['$caller']) return false;
												
					//lecture des champs de la notice
					var something_checked=false;
					for(var i=0; i<fields_index_auto.length; i++){	
						fields_index_auto[i]['value']='';
						if(document.getElementById('chk_'+fields_index_auto[i]['name']).checked) {
							something_checked=true;
							if( parent.window.parent.document.forms['$caller'].elements[fields_index_auto[i]['field']]) {
								fields_index_auto[i]['value'] = encodeURIComponent(parent.window.parent.document.forms['$caller'].elements[fields_index_auto[i]['field']].value);
							}
						}
						
					}
					
					// lecture de la langue d'indexation de la notice
					document.getElementById('user_lang').value=parent.window.parent.document.forms['$caller'].elements['indexation_lang'].value;

					document.getElementById('autoindex_txt').value=JSON.stringify(fields_index_auto);
					if (something_checked) {
						document.forms['search_form'].submit();
					}
					return false;
				}
				
			</script>
			&nbsp;
			
			<input type='hidden' value='' name='autoindex_txt' id='autoindex_txt'/>
			<input type='hidden' value='$htmlfieldstype' name='htmlfieldstype' />
			<input type='hidden' value='' name='autoindex_lang' id='autoindex_lang'/>
			<br />
			$combo
			";			
		
		}
		return $tpl_index_auto;
	}
		
	public function index_list(){
		global $charset,$base_path,$base_url;
		global $categ_browser_autoindex;
		global $thesaurus_mode_pmb;
		global $include_path,$caller,$callback;	
		global $msg;

		$this->process();
		
		$libelle_partiel=0;
		if($caller == 'search_form') {
			$libelle_partiel=1;
		}
		$tpl_insert_all_index = '';
		$tpl_insert_all_index_name = '';
		
		$browser_content="<h3>".$msg["autoindex_selector_title"]."</h3>";	
		
		foreach($this->terms as $categ_obj){
			if($categ_obj->see)$categ_id=$categ_obj->see;
			else $categ_id=$categ_obj->id;
			$tcateg =  new category($categ_id);
			$browser_content .= "<tr><td>";
			if($this->get_thesaurus() == -1 && $thesaurus_mode_pmb){
				$display = '['.htmlentities($tcateg->thes->libelle_thesaurus,ENT_QUOTES, $charset).']';
			} else {
				$display = '';
			}
			if($tcateg->voir_id) {
				$tcateg_voir = new category($tcateg->voir_id);
				$display .= "$tcateg->libelle -&gt;<i>".$tcateg_voir->catalog_form."@</i>";
				$id_=$tcateg->voir_id;
				if($libelle_partiel){
					$libelle_=$tcateg_voir->libelle;
				}else{
					$libelle_=$tcateg_voir->catalog_form;
				}
			} else {
				$id_=$tcateg->id;
				if($libelle_partiel){
					$libelle_=$tcateg->libelle;
				}else{
					$libelle_=$tcateg->catalog_form;
				}
				$display .= $tcateg->libelle;
			}
			if($tcateg->has_child) {
				//$browser_content .= "<a href='$base_url".$tcateg->id."&id2=".$tcateg->id.'&id_thes='.$tcateg->thes->id_thesaurus."'>";//On mets le bon identifiant de thésaurus
				$browser_content .= "<img src='".get_url_icon('folderclosed.gif')."' style='border:0px; margin:3px 3px'/>";
			} else {
				$browser_content .= "<img src='".get_url_icon('doc.gif')."' style='border:0px; margin:3px 3px'/>";
			}
			if ($tcateg->commentaire) {
				$zoom_comment = "<div id='zoom_comment".$tcateg->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>".htmlentities($tcateg->commentaire,ENT_QUOTES, $charset)."</div>" ;
				$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display='none'; \"" ;
			} else {
				$zoom_comment = "" ;
				$java_comment = "" ;
			}
			if ($thesaurus_mode_pmb ) $nom_thesaurus='['.$tcateg->thes->getLibelle().'] ' ;
			else $nom_thesaurus='' ;
			
			if($tcateg->not_use_in_indexation){
				$browser_content .= "<img src='".get_url_icon('interdit.gif')."' style='border:0px; margin:3px 3px'/>&nbsp;";
				$browser_content .= $display;
			} else {
				$browser_content .= "<a href='#' $java_comment onclick=\"set_parent('$caller', '$id_', '".htmlentities(addslashes($nom_thesaurus.$libelle_),ENT_QUOTES, $charset)."','$callback','".$tcateg->thes->id_thesaurus."')\">";
				$browser_content .= $display;
				$browser_content .= "</a>";
			}
			$browser_content .= $zoom_comment."\n";
			$browser_content .= "</td></tr>";
			if(!$tcateg->not_use_in_indexation){
				if($tpl_insert_all_index){
					$tpl_insert_all_index.=",";
					$tpl_insert_all_index_name.=",";
				}
				$tpl_insert_all_index.=$id_;
				$tpl_insert_all_index_name.="'".htmlentities(addslashes($nom_thesaurus.$libelle_),ENT_QUOTES, $charset)."'";
			}
					
		}
		$categ_browser_autoindex = str_replace('!!browser_content!!', $browser_content, $categ_browser_autoindex);
		$categ_browser_autoindex = str_replace('!!base_url!!', $base_url, $categ_browser_autoindex);
		
		if(count($this->terms))
			$categ_browser_autoindex.="
			<script type='text/javascript'>
				function insert_all_index(){
					var categs=new Array($tpl_insert_all_index);
					var categs_name=new Array($tpl_insert_all_index_name);
					for(var i=0; i<categs.length; i++){
						set_parent('$caller', categs[i], categs_name[i],'','1');
					}
				}
			</script>
			<input type='button' class='bouton_small' value='".$msg["autoindex_selector_add_all"]."' onclick='insert_all_index()' />
			";
		
		return $categ_browser_autoindex;
	}
	
	
}

