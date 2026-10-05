<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere.class.php,v 1.10 2024/01/16 09:17:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'auteurs'

if ( ! defined( 'ETAGERE_CLASS' ) ) {
  define( 'ETAGERE_CLASS', 1 );
  
global $class_path;
require_once($class_path."/sort.class.php");
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/translation.class.php");

class etagere {
	// propriétés
	public $idetagere ;
	public $name = ''			;	// nom de référence
	public $comment = ""		;	// description du contenu du panier
	public $comment_gestion = "";	// Commentaire de gestion
	public $validite = 1		;	// validite de l'étagère permanente ?
	public $validite_date_deb = ''	;	// 	si non permanente date de début
	public $validite_date_fin = ''	;	// 	                  date de fin
	public $validite_date_deb_f = ''	;	// 	si non permanente date de début formatée
	public $validite_date_fin_f = ''	;	// 	                  date de fin formatée
	public $visible_accueil = 1	;	// visible en page d'accueil ?
	public $id_tri = 0;
	public $thumbnail_url = '';
	public $autorisations = ""		;	// autorisations accordées sur ce panier
	public $classementGen = ""		;	// classement

	// constructeur
	public function __construct($etagere_id=0) {
		$this->idetagere = intval($etagere_id);
		$this->getData();
	}
	
	// récupération infos etagere
	public function getData() {
		global $msg ;
		
		$this->name	= '';
		$this->comment	= '';
		$this->comment_gestion	= '';
		$this->autorisations	= "";
		$this->validite = "";
		$this->validite_date_deb = "";
		$this->validite_date_fin = "";
		$this->validite_date_deb_f = "";
		$this->validite_date_fin_f = "";
		$this->visible_accueil = "";
		$this->id_tri = 0;
		$this->thumbnail_url = '';
		$this->classementGen = '';
		if($this->idetagere) {
			$requete = "SELECT idetagere, name, comment, comment_gestion, validite, ";
			$requete .= "validite_date_deb, date_format(validite_date_deb, '".$msg["format_date"]."') as validite_date_deb_f,  ";
			$requete .= "validite_date_fin, date_format(validite_date_fin, '".$msg["format_date"]."') as validite_date_fin_f,  ";
			$requete .= "visible_accueil, autorisations, id_tri, thumbnail_url, etagere_classement FROM etagere WHERE idetagere='$this->idetagere' ";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				$this->idetagere = $temp->idetagere;
				$this->name = $temp->name;
				$this->comment = $temp->comment;
				$this->comment_gestion = $temp->comment_gestion;
				$this->validite = $temp->validite;
				$this->validite_date_deb = $temp->validite_date_deb;
				$this->validite_date_deb_f = $temp->validite_date_deb_f;
				$this->validite_date_fin = $temp->validite_date_fin;
				$this->validite_date_fin_f = $temp->validite_date_fin_f;
				$this->visible_accueil = $temp->visible_accueil;
				$this->autorisations = $temp->autorisations;
				$this->id_tri = $temp->id_tri;
				$this->thumbnail_url = $temp->thumbnail_url;
				$this->classementGen = $temp->etagere_classement;
			}
		}
	}
	
	public function get_content_form() {
	    global $msg, $charset;
	    global $pmb_notice_img_folder_id;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('form_etagere_name', 'etagere_name')
	    ->add_input_node('text', $this->name)
	    ->set_attributes(array('data-translation-fieldname' => 'name'));
	    
	    if ($this->validite || !$this->idetagere) {
	        $checkbox_all = "checked";
	        $form_visible_deb = "";
	        $form_visible_fin = "";
	    } else {
	        $checkbox_all = "";
	        $form_visible_deb = $this->validite_date_deb_f;
	        $form_visible_fin = $this->validite_date_fin_f;
	    }
	    $content = $msg['etagere_visible_date_all']."&nbsp;<input type='checkbox' name='form_visible_all' value='1' ".$checkbox_all." class='checkbox' onClick=\"vadidite_check(this.form)\" />
            &nbsp;&nbsp;<label for='form_visible_deb' style='all:unset'>".$msg['etagere_visible_date_deb']."</label><input type='text' class='saisie-10em' id='form_visible_deb' name='form_visible_deb' value='".$form_visible_deb."' />
            &nbsp;<label for='form_visible_fin' style='all:unset'>".$msg['etagere_visible_date_fin']."</label>&nbsp;<input type='text' class='saisie-10em' id='form_visible_fin' name='form_visible_fin' value='".$form_visible_fin."' />
            &nbsp;<label for='form_visible_accueil' style='all:unset'>".$msg['etagere_visible_accueil']."</label>&nbsp;<input type='checkbox' id='form_visible_accueil' name='form_visible_accueil' value='1' ".($this->visible_accueil ? "checked='checked'" : "")." class='checkbox'  />";
	    $interface_content_form->add_element('form_etagere_date', 'etagere_visible_date')
	    ->add_html_node($content);
	    
	    $interface_content_form->add_element('form_etagere_comment', 'etagere_comment')
	    ->add_textarea_node($this->comment, 62, 5)
	    ->set_attributes(array('wrap' => 'virtual', 'data-translation-fieldname' => 'comment'));
	    $interface_content_form->add_element('form_etagere_comment_gestion', 'etagere_comment_gestion')
	    ->add_textarea_node($this->comment_gestion, 62, 5)
	    ->set_attributes(array('wrap' => 'virtual', 'data-translation-fieldname' => 'comment_gestion'));
	    
	    $content = "
        <input type='text' class='saisie-80em' id='f_thumbnail_url' name='f_thumbnail_url' value=\"".htmlentities($this->thumbnail_url, ENT_QUOTES, $charset)."\" />
        <input type='button' class='bouton' value='".$msg['raz']."' onClick=\"try{document.getElementById('f_thumbnail_url').value='';document.getElementById('f_img_load').value='';} catch(e) {}; \"/>";
	    $interface_content_form->add_element('f_thumbnail_url', 'etagere_thumbnail_url')
	    ->add_html_node($content);
	    
	    if($pmb_notice_img_folder_id) {
	        $interface_content_form->add_element('f_img_load', 'etagere_img_load')
	        ->add_input_node('file');
	    }
	    
	    $interface_content_form->add_inherited_element('permissions_users', 'autorisations', 'etagere_autorisations')
	    ->set_autorisations($this->autorisations);

	    return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		global $base_path;
		global $PMBuserid;
		global $pmb_javascript_office_editor;
		global $etagere_content_form;
		
		$content_form = $this->get_content_form();
		$content_form .= $etagere_content_form;
		$content_form = str_replace('!!idetagere!!', $this->idetagere, $content_form);
		
		$interface_form = new interface_catalog_form('etagere_form');
		$interface_form->set_enctype('multipart/form-data');
		if(!$this->idetagere){
			$interface_form->set_label($msg['etagere_new_etagere']);
		}else{
			$interface_form->set_label($msg['etagere_edit_etagere']);
		}
		if($this->id_tri>0){
			$sort = new sort("notices","base");
			$content_form = str_replace('!!tri!!', $this->id_tri, $content_form);
			$content_form = str_replace('!!tri_name!!', $sort->descriptionTriParId($this->id_tri), $content_form);
		}else{
			$content_form = str_replace('!!tri!!', "", $content_form);
			$content_form = str_replace('!!tri_name!!', $msg['etagere_form_no_active_tri'], $content_form);
		}
			
		$message_folder = static::validate_img_folder();
		$content_form = str_replace('!!message_folder!!', $message_folder, $content_form);
		$classementGen = new classementGen('etagere', $this->idetagere);
		$content_form = str_replace("!!object_type!!",$classementGen->object_type,$content_form);
		$content_form = str_replace("!!classements_liste!!",$classementGen->getClassementsSelectorContent($PMBuserid,$classementGen->libelle),$content_form);
		
		$js_script = "";
		if($pmb_javascript_office_editor){
			$js_script .= $pmb_javascript_office_editor;
			$js_script .= "<script type='text/javascript'>
                pmb_include('$base_path/javascript/tinyMCE_interface.js');
            </script>";
		}
		$interface_form->set_object_id($this->idetagere)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($content_form)
		->set_table_name('etagere')
		->set_field_focus('form_etagere_name')
		->set_duplicable(true);
		$form = $interface_form->get_display();
		$form .= "
		<script type=\"text/javascript\">
			function vadidite_check(form) {
				if (form.form_visible_all.checked==true) {
					form.form_visible_deb.disabled='disabled' ;
					form.form_visible_deb.value='' ;
					form.form_visible_fin.disabled='disabled' ;
					form.form_visible_fin.value='' ;
				} else {
					form.form_visible_deb.disabled='';
					form.form_visible_fin.disabled='';
				}
			}
			vadidite_check(document.forms['etagere_form']);
		</script>
		";
		return $js_script.$form;
	}
	
	public function set_properties_from_form() {
		global $form_etagere_name, $form_etagere_comment, $form_etagere_comment_gestion;
		global $form_visible_all, $form_visible_deb, $form_visible_fin;
		global $form_visible_accueil;
		global $tri, $f_thumbnail_url, $classementGen_etagere;
		global $autorisations;
		
		$this->name = stripslashes($form_etagere_name);
		$this->comment = stripslashes($form_etagere_comment);
		$this->comment_gestion = stripslashes($form_etagere_comment_gestion);
		$this->validite = $form_visible_all;
		$this->validite_date_deb_f = $form_visible_deb;
		$this->validite_date_fin_f = $form_visible_fin;
		$this->validite_date_deb = extraitdate($form_visible_deb);
		$this->validite_date_fin = extraitdate($form_visible_fin);
		$this->visible_accueil = $form_visible_accueil;
		$this->tri = $tri;
		$this->thumbnail_url = stripslashes($f_thumbnail_url);
		$this->classementGen = stripslashes($classementGen_etagere);
		if (is_array($autorisations)) {
			$this->autorisations=implode(" ",$autorisations);
		}
		else {
			$this->autorisations="1";
		}
	}
	
	// liste des étagères disponibles
	public static function get_etagere_list() {
		global $msg ;
		$etagere_list=array();
		$requete = "SELECT idetagere, name, comment, comment_gestion, validite, ";
		$requete .= "validite_date_deb, date_format(validite_date_deb, '".$msg["format_date"]."') as validite_date_deb_f,  ";
		$requete .= "validite_date_fin, date_format(validite_date_fin, '".$msg["format_date"]."') as validite_date_fin_f,  ";
		$requete .= "visible_accueil, autorisations, etagere_classement FROM etagere order by name ";
		$result = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($result)) {
			while ($temp = pmb_mysql_fetch_object($result)) {
					$sql = "SELECT COUNT(*) FROM etagere_caddie WHERE etagere_id = ".$temp->idetagere;
					$res = pmb_mysql_query($sql);
					$nbr_paniers = pmb_mysql_result($res, 0, 0);
									
					$etagere_list[] = array( 
						'idetagere' => $temp->idetagere,
						'name' => $temp->name,
						'comment' => $temp->comment,
						'comment_gestion' => $temp->comment_gestion,
						'validite' => $temp->validite,
						'validite_date_deb' => $temp->validite_date_deb,
						'validite_date_fin' => $temp->validite_date_fin,
						'validite_date_deb_f' => $temp->validite_date_deb_f,
						'validite_date_fin_f' => $temp->validite_date_fin_f,
						'visible_accueil' => $temp->visible_accueil,
						'autorisations' => $temp->autorisations,
						'etagere_classement' => $temp->etagere_classement,
						'nb_paniers' => $nbr_paniers
						);
				}
			} 
		return $etagere_list;
	}
	
	// création d'une etagere vide
	public function create_etagere() {
		$requete = "insert into etagere set name='".addslashes($this->name)."', comment='".addslashes($this->comment)."', comment_gestion='".addslashes($this->comment_gestion)."', validite='".$this->validite."', validite_date_deb='".$this->validite_date_deb."', validite_date_fin='".$this->validite_date_fin."', visible_accueil='".$this->visible_accueil."', autorisations='".$this->autorisations."'";
		pmb_mysql_query($requete);
		$this->idetagere = pmb_mysql_insert_id();
		$this->save_translations();
	}
	
	// suppression d'une etagere
	public function delete() {
		$requete = "delete FROM etagere_caddie where etagere_id='".$this->idetagere."' ";
		pmb_mysql_query($requete);
		$this->delete_vignette();
		translation::delete($this->idetagere, "etagere");
		$requete = "delete FROM etagere where idetagere='".$this->idetagere."' ";
		pmb_mysql_query($requete);
			
	}
	
	public function delete_vignette() {
		//Suppression de la vignette d'etagere
	    thumbnail::delete($this->idetagere, 'shelve');
	}
	
	public function create_vignette() {
		$thumbnail_url=$this->thumbnail_url;
		
		// vignette de l'etagere
		$uploaded_thumbnail_url = thumbnail::create($this->idetagere, 'shelve');
		if($uploaded_thumbnail_url) {
			$thumbnail_url = $uploaded_thumbnail_url;
		}
		
		return $thumbnail_url;
	}
	
	// sauvegarde de l'etagere
	public function save_etagere() {
		$this->thumbnail_url = $this->create_vignette();
		if(!$this->thumbnail_url) {
			$this->delete_vignette();
		}
		$requete = "update etagere set name='".addslashes($this->name)."', comment='".addslashes($this->comment)."', comment_gestion='".addslashes($this->comment_gestion)."', validite='".$this->validite."', validite_date_deb='".$this->validite_date_deb."', validite_date_fin='".$this->validite_date_fin."', visible_accueil='".$this->visible_accueil."', autorisations='".$this->autorisations."',id_tri='".$this->tri."',thumbnail_url='".addslashes($this->thumbnail_url)."',etagere_classement='".addslashes($this->classementGen)."' where idetagere='".$this->idetagere."'";
		pmb_mysql_query($requete);
		$this->save_translations();
	}

	public function save_translations() {
		$translation = new translation($this->idetagere, "etagere");
		$translation->update("name", "form_etagere_name");
		$translation->update_text("comment", "form_etagere_comment");
		$translation->update_text("comment_gestion", "form_etagere_comment_gestion");
	}
	
	public static function validate_img_folder () {
		return thumbnail::get_message_folder('shelve');
	}	
	
	public static function check_rights($id) {
		global $PMBuserid;
	
		if ($id) {
			$query = "SELECT autorisations FROM etagere WHERE idetagere='$id' ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				$rqt_autorisation=explode(" ",$temp->autorisations);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid == 1) return $id ;
			}
		}
		return 0 ;
	}
	
	public function get_translated_name() {
		return translation::get_translated_text($this->idetagere, 'etagere', 'name',  $this->name);
	}
	
	public function get_translated_comment() {
		return translation::get_translated_text($this->idetagere, 'etagere', 'comment',  $this->comment);
	}
	
	public function get_translated_comment_gestion() {
		return translation::get_translated_text($this->idetagere, 'etagere', 'comment_gestion',  $this->comment_gestion);
	}
	
	public function get_classement_label() {
		if(!trim($this->classementGen)) {
			return classementGen::getDefaultLibelle();
		}
		return $this->classementGen;
	}
	
	public function get_classement_selector() {
		global $base_path, $PMBuserid;
		$classementGen = new classementGen('etagere', $this->idetagere);
		return $classementGen->show_selector($base_path.'/catalog.php?categ=etagere',$PMBuserid);
	}
} // fin de déclaration de la classe cart
  
} # fin de déclaration du fichier caddie.class
