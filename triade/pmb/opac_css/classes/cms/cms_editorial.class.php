<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial.class.php,v 1.56.2.1 2024/12/17 14:36:16 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path, $include_path;
require_once ($class_path . "/cms/cms_root.class.php");
require_once ($class_path . "/cms/cms_logo.class.php");
require_once ($class_path . "/cms/cms_editorial_publications_states.class.php");

require_once ($class_path . "/categories.class.php");
require_once ($include_path . "/templates/cms/cms_editorial.tpl.php");
require_once ($class_path . "/double_metaphone.class.php");
require_once ($class_path . "/stemming.class.php");
require_once ($class_path . "/cms/cms_collections.class.php");
require_once ($class_path . "/index_concept.class.php");
require_once ($class_path . "/cms/cms_concept.class.php");

class cms_editorial extends cms_root
{

    public $id;

    // identifiant du contenu
    public $num_parent;

    // id du parent
    public $title;

    // le titre du contenu
    public $contenu;

    // contenu
    public $resume;

    // résumé du contenu
    public $logo;

    // objet gérant le logo
    public $publication_state;

    // statut de publication
    public $start_date;

    // date de début de publication
    public $end_date;

    // date de fin de publication
    protected $descriptors;

    // descripteurs
    protected $type;

    // le type de l'objet
    public $num_type;

    // id du type de contenu
    public $type_content = "";

    // libellé du type de contenu
    protected $fields_type;

    protected $opt_elements;

    // les éléments optionnels constituants l'objet
    public $create_date;

    //
    protected $documents_linked;

    // tableau des docs liés
    public $formated_datas = null;

    public $last_update_date = "";

    // date de dernière modification
    protected $num_page;

    // Id de la page sur laquelle seras affiché l'élément (défini par le type en administration)
    protected $var_name;

    // Nom de la variable d'environnement utilisé sur la page pour afficher l'élément (défini par le type également)
    protected static $generic_classes;

    /**
     * Concepts associés
     *
     * @var index_concept
     */
    protected $index_concept = null;

    public function __construct($id = 0, $type = "section", $num_parent = 0)
    {
        $this->type = $type;
        if ($id) {
            $this->id = intval($id);
            $this->fetch_data();
            $this->logo = new cms_logo($this->id, $this->type);
        } else {
            $this->id = 0;
            $this->title = "";
            $this->resume = "";
            $this->logo = new cms_logo(0, $this->type);
            $this->publication_state = "";
            $this->start_date = "";
            $this->end_date = "";
            $this->num_parent = $num_parent;
            $this->descriptors = array();
            $this->num_type;
            $this->create_date = "";
            $this->documents_linked = array();
            $this->last_update_date = "";
            $this->num_page = 0;
            $this->var_name = '';
        }
        $this->get_index_concept();
    }

    protected function fetch_data_cache()
    {
        if ($tmp = cms_cache::get_at_cms_cache($this)) {
            $this->restore($tmp);
        } else {
            $this->fetch_data();
            cms_cache::set_at_cms_cache($this);
        }
    }

    protected function restore($cms_object)
    {
        if (is_object($cms_object)) {
            foreach (get_object_vars($cms_object) as $propertieName => $propertieValue) {
                $this->{$propertieName} = $propertieValue;
            }
        }
    }

    public function get_descriptors()
    {
        global $lang;
        if (! isset($this->descriptors)) {
            $this->descriptors = array();
            // les descripteurs...
            $rqt = "select num_noeud from cms_" . $this->type . "s_descriptors where num_" . $this->type . " = '" . $this->id . "' order by " . $this->type . "_descriptor_order";
            $res = pmb_mysql_query($rqt);
            if (pmb_mysql_num_rows($res)) {
                while ($row = pmb_mysql_fetch_object($res)) {
                    $descriptors = array();
                    $categ = new categories($row->num_noeud, $lang);
                    $descriptors["id"] = $categ->num_noeud;
                    $descriptors["lang"] = $categ->langue;
                    $descriptors["name"] = $categ->libelle_categorie;
                    $descriptors["comment"] = $categ->comment_public;
                    $this->descriptors[] = $descriptors;
                }
            }
        }
        return $this->descriptors;
    }

    public function get_fields_type()
    {
        if (! isset($this->fields_type)) {
            $this->fields_type = array();
            $generic = $this->get_generic_fields_class();
            $this->fields_type = $generic->get_out_values($this->id);
            if ($this->num_type) {
                $query = "select editorial_type_label, editorial_type_permalink_num_page, editorial_type_permalink_var_name from cms_editorial_types where id_editorial_type = " . $this->num_type;
                $result = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($result)) {
                    $row = pmb_mysql_fetch_object($result);
                    $this->num_page = $row->editorial_type_permalink_num_page;
                    $this->var_name = $row->editorial_type_permalink_var_name;
                    if (! $this->num_page || ! $this->var_name) { // Récupération des éléments composants le permalien
                        $cms_editorial_types = new cms_editorial_types($this->type);
                        if ($cms_editorial_types->get_generic_type()) {
                            $generic_type = $cms_editorial_types->get_generic_type();
                            if ($generic_type['var_name'] && $generic_type['num_page']) {
                                $this->var_name = $generic_type['var_name'];
                                $this->num_page = $generic_type['num_page'];
                            }
                        }
                    }

                    $this->type_content = $row->editorial_type_label;
                    $fields_type = new cms_editorial_parametres_perso($this->num_type);
                    $this->fields_type = array_merge($this->fields_type, $fields_type->get_out_values($this->id));
                }
            }
        }
        return $this->fields_type;
    }

    public function delete()
    {
        $result = $this->is_deletable();
        if ($result === true) {
            // documents du portfolio
            $query = "delete from cms_documents_links where document_link_type_object = '" . $this->type . "' and document_link_num_object = '" . $this->id . "'";
            pmb_mysql_query($query);
            // l'elément
            $del = "delete from cms_" . $this->type . "s where id_" . $this->type . "='" . $this->id . "'";
            pmb_mysql_query($del);
            // ses descripteurs
            $del_desc = "delete from cms_" . $this->type . "s_descriptors where num_" . $this->type . " = '" . $this->id . "'";
            pmb_mysql_query($del_desc);
            // ses champs persos
            $fields_type = new cms_editorial_parametres_perso($this->num_type);
            $fields_type->delete_values($this->id, $this->type);
            // indexation
            $query = "delete from cms_editorial_fields_global_index where num_obj = '" . $this->id . "' and type='" . $this->type . "'";
            pmb_mysql_query($query);
            $query = "delete from cms_editorial_words_global_index where num_obj = '" . $this->id . "' and type='" . $this->type . "'";
            pmb_mysql_query($query);
            // ses extensions
            $query = "delete from cms_modules_extensions_datas where extension_datas_type_element ='" . $this->type . "' and extension_datas_num_element = '" . $this->id . "'";
            pmb_mysql_query($query);
            // les veilles
            $query = "update docwatch_items set item_num_" . $this->type . " = 0 where item_num_" . $this->type . " = '" . $this->id . "'";
            pmb_mysql_query($query);
            return true;
        } else {
            return $result;
        }
    }

    public function get_form($name = "cms_form_editorial", $id = "cms_form_editorial", $attr = "", $close = true)
    {
        // on récupère le template
        global $cms_editorial_form_tpl;
        global $cms_editorial_form_del_button_tpl;
        global $cms_editorial_form_dupli_button_tpl;
        global $msg;
        global $base_path;

        $fields_form = "";
        $fields_form .= $this->get_id_field();
        $fields_form .= $this->get_permalink_field();
        $fields_form .= $this->get_type_field();
        $fields_form .= $this->get_parent_field();
        $fields_form .= $this->get_title_field();
        $fields_form .= $this->get_resume_field();
        $fields_form .= $this->get_contenu_field();
        $fields_form .= $this->get_logo_field();
        $fields_form .= $this->get_desc_field();
        $fields_form .= $this->get_concepts_field($name);
        $fields_form .= $this->get_publication_state_field();
        $fields_form .= $this->get_dates_field();
        $fields_form .= $this->get_documents_form();

        $form = str_replace("!!fields!!", $fields_form, $cms_editorial_form_tpl);

        if ($this->id) {
            $del_button = $cms_editorial_form_del_button_tpl;
            $dupli_button = $cms_editorial_form_dupli_button_tpl;
            $type_href = $base_path . "/ajax.php?module=cms&categ=get_type_form&elem=" . $this->type . "&type_id=" . $this->num_type . "&id=" . $this->id;
        } else {
            $del_button = "";
            $dupli_button = "";
            $type_href = $base_path . "/ajax.php?module=cms&categ=get_type_form&elem=" . $this->type . "&type_id=&id=" . $this->id;
            ;
        }
        $form = str_replace("!!cms_editorial_form_suppr!!", $del_button, $form);
        $form = str_replace("!!cms_editorial_form_dupli!!", $dupli_button, $form);
        $form = str_replace("!!type_href!!", $type_href, $form);

        $form = str_replace("!!type!!", $this->type, $form);
        $form = str_replace("!!cms_editorial_form_name!!", $name, $form);
        $form = str_replace("!!cms_editorial_form_id!!", $id, $form);
        $form = str_replace("!!cms_editorial_form_obj_id!!", $this->id, $form);

        if (! $this->id) {
            $attr = "enctype='multipart/form-data' " . $attr;
        }
        $form = str_replace("!!cms_editorial_form_attr!!", $attr, $form);

        $form = str_replace("!!form_title!!", $msg['cms_' . ($this->id ? "" : "new_") . $this->type . "_form_title"], $form);

        if ($close) {
            $form = str_replace("!!cms_editorial_suite!!", "", $form);
        }
        return $form;
    }

    public function get_ajax_form($name = "cms_form_editable", $id = "cms_form_editable")
    {
        global $msg;

        $form = $this->get_form($name, $id, "onsubmit='cms_ajax_submit();return false;'", false);
        $suite = "
		<script>
			function cms_ajax_submit(){
				var values = '';

				if(typeof(check_form) == 'function' && !check_form()){
					return false;
				}
				if(document.forms['$name'].cms_editorial_form_delete.value == 1){
					if(confirm(\"" . $msg['cms_editorial_form_' . $this->type . '_delete_confirm'] . "\")){
						cms_" . $this->type . "_delete();
					}
				} else if(document.forms['$name'].cms_editorial_form_duplicate.value == 1){
					if ('" . $this->type . "' == 'section') {
						if (confirm('" . $msg['cms_editorial_form_duplicate_branch_confirm'] . "')) {
							cms_" . $this->type . "_duplicate(1);
						} else {
							cms_" . $this->type . "_duplicate(0);
						}
					} else {
						cms_" . $this->type . "_duplicate(0);
					}
				}else{
					for(var i=0 ; i<document.forms['$name'].elements.length ; i++){
						var element = document.forms['$name'].elements[i];
						if(element.name){
							if(element.type == 'select-multiple'){
								for(var j=0; j< element.options.length; j++){
									if(element.options[j].selected == true){
										values+='&'+element.name+'='+encodeURIComponent(element.options[j].value);
									}
								}
							}else if(element.type == 'checkbox'){
								if(element.checked == true){
									values+='&'+element.name+'='+encodeURIComponent(element.value);
								}
							}else if(element.type == 'radio'){
								if(element.checked == true){
									values+='&'+element.name+'='+encodeURIComponent(element.value);
								}
							}else{
								values+='&'+element.name+'='+encodeURIComponent(element.value);
							}
						}
					}
					var post = new http_request();
					post.request('./ajax.php?module=cms&categ=save_" . $this->type . "',true,values,true,cms_" . $this->type . "_saved);
				}
			}

			function cms_" . $this->type . "_delete(){
				var post = new http_request();
				post.request('./ajax.php?module=cms&categ=delete_" . $this->type . "',true,'&id='+document.forms['$name'].cms_editorial_form_obj_id.value,true,cms_" . $this->type . "_deleted);
			}

			function cms_" . $this->type . "_duplicate(recursive){
				var post = new http_request();
				post.request('./ajax.php?module=cms&categ=duplicate_" . $this->type . "',true,'&id='+document.forms['$name'].cms_editorial_form_obj_id.value+'&recursive='+recursive,true,cms_" . $this->type . "_duplicated);
			}

			function cms_" . $this->type . "_deleted(response){
				var result = eval('('+response+')');
				if(result.status == 'ok'){
					dijit.byId('editorial_tree_container').refresh();
					dijit.byId('content_infos').destroyDescendants();
				}else{
					alert(result.error_message);
				}
			}

			function cms_" . $this->type . "_duplicated(){
					dijit.byId('editorial_tree_container').refresh();
					dijit.byId('content_infos').destroyDescendants();
			}

			function cms_" . $this->type . "_saved(response){
				dijit.byId('editorial_tree_container').refresh();
				dijit.byId('content_infos').refresh();
				dijit.byId('content_infos').domNode.scrollIntoView();
				setTimeout(function(){ document.getElementById('cms_editorial_content_saved').innerHTML='<span class=\'erreur\'>" . $msg["cms_editorial_content_saved"] . "</span>'; }, 1000);
				setTimeout(function(){ document.getElementById('cms_editorial_content_saved').innerHTML=''; }, 4000);
			}
		</script>";
        $form = str_replace("!!cms_editorial_suite!!", $suite, $form);
        return $form;
    }

    public function get_parent_selector()
    {
        // à surcharger...
    }

    protected function get_parent_field()
    {
        global $cms_editorial_parent_field;
        return str_replace("!!cms_editorial_form_parent_options!!", $this->get_parent_selector(), $cms_editorial_parent_field);
    }

    protected function get_title_field()
    {
        global $charset;
        global $cms_editorial_title_field;
        return str_replace("!!cms_editorial_form_title!!", htmlentities($this->title, ENT_QUOTES, $charset), $cms_editorial_title_field);
    }

    protected function get_resume_field()
    {
        global $cms_editorial_resume_field, $cms_editorial_resume_field_no_dojo, $pmb_editorial_dojo_editor, $cms_editorial_field_javascript;

        if ($pmb_editorial_dojo_editor) {
            $template = $cms_editorial_resume_field;
        } else {
            $template = $cms_editorial_resume_field_no_dojo;
        }

        $this->resume = str_replace('!!cms_editorial_form_resume!!', $this->resume, $template);
        $this->resume = str_replace('!!cms_editorial_resume_field_javascript!!', $cms_editorial_field_javascript, $this->resume);
        $this->resume = str_replace('!!field_id!!', 'cms_editorial_form_resume', $this->resume);
        return $this->resume;
    }

    protected function get_contenu_field()
    {
        global $cms_editorial_contenu_field;
        if ($this->opt_elements['contenu'] == true) {
            return str_replace("!!cms_editorial_form_contenu!!", $this->contenu, $cms_editorial_contenu_field);
        } else {
            return "";
        }
    }

    protected function get_logo_field()
    {
        return $this->logo->get_form();
    }

    protected function get_desc_field()
    {
        global $lang;
        global $cms_editorial_desc_field;
        global $cms_editorial_first_desc, $cms_editorial_other_desc;

        $categs = "";
        $this->get_descriptors();
        if (count($this->descriptors)) {
            for ($i = 0; $i < count($this->descriptors); $i ++) {
                if ($i == 0)
                    $categ = $cms_editorial_first_desc;
                else
                    $categ = $cms_editorial_other_desc;
                // on y va
                $categ = str_replace('!!icateg!!', $i, $categ);
                $categ = str_replace('!!categ_id!!', $this->descriptors[$i], $categ);
                $categorie = new categories($this->descriptors[$i], $lang);
                $categ = str_replace('!!categ_libelle!!', $categorie->libelle_categorie, $categ);
                $categs .= $categ;
            }
            $categs = str_replace("!!max_categ!!", count($this->descriptors), $categs);
        } else {
            $categs = $cms_editorial_first_desc;
            $categs = str_replace('!!icateg!!', 0, $categs);
            $categs = str_replace('!!categ_id!!', "", $categs);
            $categs = str_replace('!!categ_libelle!!', "", $categs);
            $categs = str_replace('!!max_categ!!', 1, $categs);
        }
        return str_replace("!!cms_categs!!", $categs, $cms_editorial_desc_field);
    }

    protected function get_publication_state_field()
    {
        global $cms_editorial_publication_state_field;
        $publications_states = new cms_editorial_publications_states();
        return str_replace("!!cms_editorial_form_publications_states_options!!", $publications_states->get_selector_options($this->publication_state), $cms_editorial_publication_state_field);
    }

    protected function get_dates_field()
    {
        global $cms_editorial_dates_field;
        global $msg;
        $day = date("Ymd");
        $form = str_replace("!!day!!", $day, $cms_editorial_dates_field);

        $start_date = formatDate($this->start_date);
        if (! $start_date)
            $start_date = $msg['no_date'];
        $form = str_replace("!!cms_editorial_form_start_date_value!!", substr($this->start_date, 0, 10), $form);
        $form = str_replace("!!cms_editorial_form_start_date!!", $start_date, $form);

        $end_date = formatDate($this->end_date);
        if (! $end_date)
            $end_date = $msg['no_date'];
        $form = str_replace("!!cms_editorial_form_end_date_value!!", substr($this->end_date, 0, 10), $form);
        $form = str_replace("!!cms_editorial_form_end_date!!", $end_date, $form);
        return $form;
    }

    protected function get_type_field()
    {
        global $cms_editorial_type_field;
        $types = new cms_editorial_types($this->type);
        $types->get_types();
        if (count($types->types)) {
            $cms_editorial_type_field = str_replace("!!type!!", $this->type, $cms_editorial_type_field);
            return str_replace("!!cms_editorial_form_type_options!!", $types->get_selector_options($this->num_type), $cms_editorial_type_field);
        } else {
            return "";
        }
    }

    protected function get_id_field()
    {
        global $cms_editorial_obj_id_field;
        if ($this->id) {
            return str_replace("!!cms_editorial_form_obj_id!!", $this->id, $cms_editorial_obj_id_field);
        } else {
            return "";
        }
    }

    public function get_from_form()
    {
        global $cms_editorial_form_obj_id;
        global $cms_editorial_form_type;
        global $cms_editorial_form_parent;
        global $cms_editorial_form_title;
        global $cms_editorial_form_resume;
        global $cms_editorial_form_contenu;
        global $max_categ;
        global $cms_editorial_form_publication_state;
        global $cms_editorial_form_start_date_value;
        global $cms_editorial_form_end_date_value;
        global $cms_documents_linked;
        global $thesaurus_concepts_active;

        $this->descriptors = array();
        for ($i = 0; $i < $max_categ; $i ++) {
            $categ_id = 'f_categ_id' . $i;
            global ${$categ_id};
            if (${$categ_id} > 0) {
                $this->descriptors[] = ${$categ_id};
            }
        }
        $this->id = $cms_editorial_form_obj_id;
        $this->num_type = $cms_editorial_form_type;
        $this->num_parent = stripslashes($cms_editorial_form_parent);
        $this->title = stripslashes($cms_editorial_form_title);
        $this->resume = stripslashes($cms_editorial_form_resume);
        if ($this->resume == '<br _moz_editor_bogus_node="TRUE" />') {
            $this->resume = "";
        }
        $this->start_date = stripslashes($cms_editorial_form_start_date_value);
        $this->end_date = stripslashes($cms_editorial_form_end_date_value);
        $this->publication_state = stripslashes($cms_editorial_form_publication_state);
        if ($this->opt_elements['contenu']) {
            $this->contenu = stripslashes($cms_editorial_form_contenu);
            if ($this->contenu == '<br _moz_editor_bogus_node="TRUE" />') {
                $this->contenu = "";
            }
        }
        $this->logo->id = $this->id;
        $this->documents_linked = $cms_documents_linked;

        // Indexation concepts
        if ($thesaurus_concepts_active == 1) {
            $this->index_concept->get_from_form();
        }
    }

    protected function save_logo()
    {
        // on agit que si un fichier a été soumis...
        if (! empty($_FILES) && count($_FILES)) {
            $this->logo->id = $this->id;
            $this->logo->save();
        }
    }

    public function maj_indexation($datatype = 'all')
    {
        global $include_path;
        global $base_path;
        global $champ_base;
        // recuperation du fichier xml de configuration
        if (! count($champ_base)) {
            $file = $include_path . "/indexation/editorial_content/" . $this->type . "_subst.xml";
            if (! file_exists($file)) {
                $file = $include_path . "/indexation/editorial_content/" . $this->type . ".xml";
            }
            $fp = fopen($file, "r");
            if ($fp) {
                $xml = fread($fp, filesize($file));
            }
            fclose($fp);
            $champ_base = _parser_text_no_function_($xml, "INDEXATION", $file);
        }
        $tableau = $champ_base;

        // analyse des donnees des tables
        $temp_not = array();
        $temp_not['t'][0][0] = $tableau['REFERENCE'][0]['value'];
        $temp_ext = array();
        $temp_marc = array();
        $temp_callable = array();
        $champ_trouve = false;
        $tab_code_champ = array();
        $tab_languages = array();
        $tab_keep_empty = array();
        $tab_pp = array();
        for ($i = 0; $i < count($tableau['FIELD']); $i ++) { // pour chacun des champs decrits
                                                             // recuperation de la liste des informations a mettre a jour
            if ($datatype == 'all' || ($datatype == $tableau['FIELD'][$i]['DATATYPE'])) {
                // conservation des mots vides
                if (isset($tableau['FIELD'][$i]['KEEPEMPTYWORD']) && $tableau['FIELD'][$i]['KEEPEMPTYWORD'] == "yes") {
                    $tab_keep_empty[] = $tableau['FIELD'][$i]['ID'];
                }
                // champ perso
                if (isset($tableau['FIELD'][$i]['DATATYPE']) && $tableau['FIELD'][$i]['DATATYPE'] == "custom_field") {
                    $tab_pp[$tableau['FIELD'][$i]['ID']] = $tableau['FIELD'][$i]['TABLE'][0]['value'];
                } else if (isset($tableau['FIELD'][$i]['EXTERNAL']) && $tableau['FIELD'][$i]['EXTERNAL'] == "yes") {
                    // champ externe à la table notice
                    // Stockage de la structure pour un accès plus facile
                    $temp_ext[$tableau['FIELD'][$i]['ID']] = $tableau['FIELD'][$i];
                } else if (isset($tableau['FIELD'][$i]['CALLABLE'])) {
                    // Callables
                    $temp_callable[$tableau['FIELD'][$i]['ID']] = array();
                    for ($j = 0; $j < count($tableau['FIELD'][$i]['CALLABLE']); $j ++) {
                        $temp_callable[$tableau['FIELD'][$i]['ID']][] = array(
                            'champ' => $tableau['FIELD'][$i]['ID'],
                            'ss_champ' => $tableau['FIELD'][$i]['CALLABLE'][$j]['ID'],
                            'pond' => $tableau['FIELD'][$i]['CALLABLE'][$j]['POND'],
                            'class_path' => $tableau['FIELD'][$i]['CALLABLE'][$j]['CLASS_PATH'],
                            'class_name' => $tableau['FIELD'][$i]['CALLABLE'][$j]['CLASS_NAME'],
                            'method' => $tableau['FIELD'][$i]['CALLABLE'][$j]['METHOD'],
                            'parameters' => $tableau['FIELD'][$i]['CALLABLE'][$j]['PARAMETERS']
                        );
                    }
                } else {
                    // champ de la table notice
                    $temp_not['f'][0][$tableau['FIELD'][$i]['ID']] = $tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value'];
                    $tab_code_champ[0][$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']] = array(
                        'champ' => $tableau['FIELD'][$i]['ID'],
                        'ss_champ' => 0,
                        'pond' => $tableau['FIELD'][$i]['POND'],
                        'no_words' => (isset($tableau['FIELD'][$i]['DATATYPE']) && $tableau['FIELD'][$i]['DATATYPE'] == "marclist" ? true : false)
                    );
                    if (! empty($tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'])) {
                        $tab_code_champ[0][$tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']]['marctype'] = $tableau['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
                    }
                }
                $champ_trouve = true;
            }
        }
        if ($champ_trouve) {
            $tab_req = array();
            // Recherche des champs directs
            if ($datatype == 'all') {
                $tab_req[0]["rqt"] = "select " . implode(',', $temp_not['f'][0]) . " from " . $temp_not['t'][0][0];
                $tab_req[0]["rqt"] .= " where " . $tableau['REFERENCEKEY'][0]['value'] . "='" . $this->id . "'";
                $tab_req[0]["table"] = $temp_not['t'][0][0];
            }
            foreach ($temp_ext as $k => $v) {
                // Construction de la requete
                // Champs pour le select
                $select = array();

                // on harmonise les fichiers XML décrivant des requetes...
                for ($i = 0; $i < count($v["TABLE"]); $i ++) {
                    $table = $v['TABLE'][$i];
                    $select = array();
                    for ($j = 0; $j < count($table['TABLEFIELD']); $j ++) {
                        $select[] = (! empty($table['ALIAS']) ? $table['ALIAS'] . "." : "") . $table['TABLEFIELD'][$j]["value"];
                        if (! empty($table['LANGUAGE'])) {
                            $select[] = $table['LANGUAGE'][0]['value'];
                            $tab_languages[$k] = $table['LANGUAGE'][0]['value'];
                        }
                        $field_name = $table['TABLEFIELD'][$j]["value"];
                        if (strpos($table['TABLEFIELD'][$j]["value"], ".") !== false) {
                            $field_name = substr($table['TABLEFIELD'][$j]["value"], strpos($table['TABLEFIELD'][$j]["value"], ".") + 1);
                        }
                        $tab_code_champ[$v['ID']][$field_name] = array(
                            'champ' => $v['ID'],
                            'ss_champ' => $table['TABLEFIELD'][$j]["ID"],
                            'pond' => $table['TABLEFIELD'][$j]['POND'],
                            'no_words' => (isset($tableau['FIELD'][$i]['DATATYPE']) && $tableau['FIELD'][$i]['DATATYPE'] == "marclist" ? true : false)
                        );
                        if (! empty($v['TABLEFIELD'][$j]['marclist'])) {
                            $tab_code_champ[$v['ID']][$v['TABLEFIELD'][$j]["value"]]['marctype'] = $v['TABLEFIELD'][$j]['marctype'];
                        }
                    }
                    $query = "select " . implode(",", $select) . " from " . $tableau['REFERENCE'][0]['value'];
                    $jointure = "";
                    for ($j = 0; $j < count($table['LINK']); $j ++) {
                        $link = $table['LINK'][$j];
                        if (! empty($link["TABLE"][0]['ALIAS'])) {
                            $alias = $link["TABLE"][0]['ALIAS'];
                        } else {
                            $alias = (! empty($link["TABLE"][0]['value']) ? $link["TABLE"][0]['value'] : "");
                        }
                        switch ($link["TYPE"]) {
                            case "n1":
                                if ($link["TABLEKEY"][0]['value']) {
                                    $jointure .= " JOIN " . $link["TABLE"][0]['value'] . ($link["TABLE"][0]['value'] != $alias ? " AS " . $alias : "");
                                    if ($link["EXTERNALTABLE"][0]['value']) {
                                        $jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
                                    } else {
                                        $jointure .= " ON " . (! empty($table['ALIAS']) ? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
                                    }
                                    $jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value'];
                                } else {
                                    $jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS'] ? " as " . $table['ALIAS'] : "");
                                    $jointure .= " ON " . $tableau['REFERENCE'][0]['value'] . "." . $tableau['REFERENCEKEY'][0]['value'];
                                    $jointure .= "=" . (! empty($table['ALIAS']) ? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
                                }
                                break;
                            case "1n":
                                $jointure .= " JOIN " . $table['NAME'] . (! empty($table['ALIAS']) ? " as " . $table['ALIAS'] : "");
                                $jointure .= " ON (" . (! empty($table['ALIAS']) ? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'];
                                $jointure .= "=" . $tableau['REFERENCE'][0]['value'] . "." . $link["REFERENCEFIELD"][0]['value'] . ") ";
                                break;
                            case "nn":
                                $jointure .= " JOIN " . $link["TABLE"][0]['value'] . ($link["TABLE"][0]['value'] != $alias ? " AS " . $alias : "");
                                $jointure .= " ON (" . $tableau['REFERENCE'][0]['value'] . "." . $tableau['REFERENCEKEY'][0]['value'];
                                $jointure .= "=" . $alias . "." . $link["REFERENCEFIELD"][0]['value'] . ") ";
                                if (! empty($link["TABLEKEY"][0]['value'])) {
                                    $jointure .= " JOIN " . $table['NAME'] . ($table['ALIAS'] ? " as " . $table['ALIAS'] : "");
                                    $jointure .= " ON (" . $alias . "." . $link["TABLEKEY"][0]['value'];
                                    $jointure .= "=" . ($table['ALIAS'] ? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'] . " " . $link["LINKRESTRICT"][0]['value'] . ") ";
                                } else {
                                    $jointure .= " JOIN " . $table['NAME'] . (! empty($table['ALIAS']) ? " as " . $table['ALIAS'] : "");
                                    $jointure .= " ON (" . $alias . "." . $link["EXTERNALFIELD"][0]['value'];
                                    $jointure .= "=" . (! empty($table['ALIAS']) ? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'] . " " . (! empty($link["LINKRESTRICT"][0]['value']) ? $link["LINKRESTRICT"][0]['value'] : "") . ") ";
                                }
                                break;
                        }
                    }
                    if (! empty($table['LANGUAGE'])) {
                        $tab_req_lang[$k] = "select " . $table['LANGUAGE'][0]['value'] . " from ";
                    }
                    $query .= $jointure . " where " . $temp_not['t'][0][0] . "." . $tableau['REFERENCEKEY'][0]['value'] . "=" . $this->id;
                    if (! empty($table['LANGUAGE'])) {
                        $tab_req_lang[$k] .= $jointure . " where " . $temp_not['t'][0][0] . "." . $tableau['REFERENCEKEY'][0]['value'] . "=" . $this->id;
                    }
                    $tab_req[$k]["new_rqt"]['rqt'][] = $query;
                }
                $tab_req[$k]["rqt"] = implode(" union ", $tab_req[$k]["new_rqt"]['rqt']);
            }
            // qu'est-ce qu'on efface?
            if ($datatype == "all") {
                $req_del = "delete from cms_editorial_words_global_index where num_obj='" . $this->id . "' and type = '" . $this->type . "'";
                pmb_mysql_query($req_del);
                // la table pour les recherche exacte
                $req_del = "delete from cms_editorial_fields_global_index where num_obj='" . $this->id . "' and type = '" . $this->type . "'";
                pmb_mysql_query($req_del);
            } else {
                foreach ($tab_code_champ as $subfields) {
                    foreach ($subfields as $subfield) {
                        $req_del = "delete from cms_editorial_words_global_index where num_obj='" . $this->id . "' and type = '" . $this->type . "' and code_champ='" . $subfield['champ'] . "'";
                        pmb_mysql_query($req_del);
                        // la table pour les recherche exacte
                        $req_del = "delete from cms_editorial_fields_global_index where num_obj='" . $this->id . "' and type = '" . $this->type . "' and code_champ='" . $subfield['champ'] . "'";
                        pmb_mysql_query($req_del);
                        break;
                    }
                }

                // Les champs perso
                if (count($tab_pp)) {
                    foreach ($tab_pp as $id) {
                        $req_del = "delete from cms_editorial_words_global_index where num_obj='" . $this->id . "' and type = '" . $this->type . "' and code_champ='" . $id . "' ";
                        pmb_mysql_query($req_del);
                        // la table pour les recherche exacte
                        $req_del = "delete from cms_editorial_fields_global_index where num_obj='" . $this->id . "' and type = '" . $this->type . "' and code_champ='" . $id . "' ";
                        pmb_mysql_query($req_del);
                    }
                }
            }

            // qu'est-ce qu'on met a jour ?
            $tab_insert = array();
            $tab_field_insert = array();
            foreach ($tab_req as $k => $v) {
                $r = pmb_mysql_query($v["rqt"]);
                $tab_mots = array();
                $tab_fields = array();
                if (pmb_mysql_num_rows($r)) {
                    while (($tab_row = pmb_mysql_fetch_array($r, PMB_MYSQL_ASSOC))) {
                        if (isset($tab_languages[$k]) && ! empty($tab_row[$tab_languages[$k]])) {
                            $lang = $tab_row[$tab_languages[$k]];
                            unset($tab_row[$tab_languages[$k]]);
                        } else {
                            $lang = "";
                        }
                        foreach ($tab_row as $nom_champ => $liste_mots) {
                            if (! empty($tab_code_champ[$k][$nom_champ]['marctype'])) {
                                $marclist = marc_list_collection::get_instance($tab_code_champ[$k][$nom_champ]['marctype']);
                                $liste_mots = $marclist->table[$liste_mots];
                            }
                            if ($liste_mots != '') {
                                $liste_mots = str_replace('<', ' <', $liste_mots);
                                $liste_mots = strip_tags($liste_mots);
                                $tab_tmp = array();
                                if (! in_array($k, $tab_keep_empty)) {
                                    $tab_tmp = explode(' ', strip_empty_words($liste_mots));
                                } else {
                                    $tab_tmp = explode(' ', strip_empty_chars(clean_string($liste_mots)));
                                }
                                // if($lang!="") $tab_tmp[]=$lang;
                                // la table pour les recherche exacte
                                if (! isset($tab_fields[$nom_champ])) {
                                    $tab_fields[$nom_champ] = array();
                                }
                                $tab_fields[$nom_champ][] = array(
                                    'value' => trim($liste_mots),
                                    'lang' => $lang
                                );
                                if (! $tab_code_champ[$k][$nom_champ]['no_words']) {
                                    foreach ($tab_tmp as $mot) {
                                        if (trim($mot)) {
                                            $tab_mots[$nom_champ][$mot] = $lang;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                foreach ($tab_mots as $nom_champ => $tab) {
                    $pos = 1;
                    foreach ($tab as $mot => $lang) {
                        $num_word = indexation::add_word($mot, $lang);
                        if ($num_word != 0) {
                            $tab_insert[] = "(" . $this->id . ",'" . $this->type . "'," . $tab_code_champ[$k][$nom_champ]['champ'] . "," . $tab_code_champ[$k][$nom_champ]['ss_champ'] . "," . $num_word . "," . $tab_code_champ[$k][$nom_champ]['pond'] . ",$pos)";
                            $pos ++;
                        }
                    }
                }
                // la table pour les recherche exacte
                foreach ($tab_fields as $nom_champ => $tab) {
                    foreach ($tab as $order => $values) {
                        // $tab_field_insert[]="(".$this->id.",".$tab_code_champ[$v["table"]][$nom_champ][0].",".$tab_code_champ[$v["table"]][$nom_champ][1].",".$order.",'".addslashes($values['value'])."','".addslashes($values['lang'])."',".$tab_code_champ[$v["table"]][$nom_champ][2].")";
                        $tab_field_insert[] = "(" . $this->id . ",'" . $this->type . "'," . $tab_code_champ[$k][$nom_champ]['champ'] . "," . $tab_code_champ[$k][$nom_champ]['ss_champ'] . "," . $order . ",'" . addslashes($values['value']) . "','" . addslashes($values['lang']) . "'," . $tab_code_champ[$k][$nom_champ]['pond'] . ")";
                    }
                }
            }
            // callables
            if (count($temp_callable)) {
                foreach ($temp_callable as $k => $callable_data) {
                    for ($i = 0; $i < count($callable_data); $i ++) {
                        if (! file_exists($base_path . '/' . $callable_data[$i]['class_path'])) {
                            continue;
                        }
                        require_once ($base_path . '/' . $callable_data[$i]['class_path']);
                        $callback_parameters = array(
                            $this->id
                        );
                        if (! empty($callable_data[$i]['parameters'])) {
                            $callback_parameters = array_merge($callback_parameters, explode(',', $callable_data[$i]['parameters']));
                        }
                        $callback_return = call_user_func_array(array(
                            $callable_data[$i]['class_name'],
                            $callable_data[$i]['method']
                        ), $callback_parameters);

                        $order_fields = 1;
                        for ($j = 0; $j < count($callback_return); $j ++) {
                            $tab_mots = array();
                            $tab_field_insert[] = $this->get_tab_field_insert($this->id, $callable_data[$i], $order_fields, $callback_return[$j]);

                            $tab_tmp = explode(' ', strip_empty_words($callback_return[$j]));
                            foreach ($tab_tmp as $mot) {
                                if (trim($mot)) {
                                    $tab_mots[$mot] = "";
                                }
                            }
                            $pos = 1;
                            foreach ($tab_mots as $mot => $langage) {
                                $num_word = indexation::add_word($mot, $langage);
                                if ($num_word != 0) {
                                    $tab_insert[] = $this->get_tab_insert($this->id, $callable_data[$i], $num_word, $pos);
                                    $pos ++;
                                }
                            }
                            $order_fields ++;
                        }
                    }
                }
            }
            // Les champs perso
            if (count($tab_pp)) {
                foreach ($tab_pp as $code_champ => $table) {
                    $p_perso = new cms_editorial_parametres_perso($this->num_type);
                    $data = $p_perso->get_fields_recherche_mot($this->id);
                    $j = 0;
                    foreach ($data as $code_ss_champ => $value) {
                        $tab_mots = array();
                        $tab_tmp = explode(' ', strip_empty_words($value));
                        // la table pour les recherche exacte
                        $tab_field_insert[] = "(" . $this->id . ",'" . $this->type . "'," . $code_champ . "," . $code_ss_champ . "," . $j . ",'" . addslashes(trim($value)) . "',''," . $p_perso->get_pond($code_ss_champ) . ")";
                        $j ++;
                        foreach ($tab_tmp as $mot) {
                            if (trim($mot)) {
                                $tab_mots[$mot] = "";
                            }
                        }
                        $pos = 1;
                        foreach ($tab_mots as $mot => $lang) {
                            $num_word = indexation::add_word($mot, $lang);
                            $tab_insert[] = "(" . $this->id . ",'" . $this->type . "'," . $code_champ . "," . $code_ss_champ . "," . $num_word . "," . $p_perso->get_pond($code_ss_champ) . ",$pos)";
                            $pos ++;
                        }
                    }
                }
            }
            $req_insert = "insert ignore into cms_editorial_words_global_index(num_obj,type,code_champ,code_ss_champ,num_word,pond,position) values " . implode(',', $tab_insert);
            pmb_mysql_query($req_insert);
            // la table pour les recherche exacte
            $req_insert = "insert ignore into cms_editorial_fields_global_index(num_obj,type,code_champ,code_ss_champ,ordre,value,lang,pond) values " . implode(',', $tab_field_insert);
            pmb_mysql_query($req_insert);
        }
    }

    public static function get_format_data_structure($type, $full = true)
    {
        global $msg;
        global $thesaurus_concepts_active;
        global $opac_avis_allow;
        $main_fields = array();
        $main_fields[] = array(
            'var' => "id",
            'desc' => $msg['cms_module_common_datasource_desc_id_' . $type]
        );
        if ($type == "section") {
            $main_fields[] = array(
                'var' => "num_parent",
                'desc' => $msg['cms_module_common_datasource_desc_num_parent']
            );
        } else {
            $main_fields[] = array(
                'var' => "parent",
                'desc' => $msg['cms_module_common_datasource_desc_parent'],
                'children' => self::prefix_var_tree(cms_section::get_format_data_structure(false, false), "parent")
            );
        }
        $main_fields[] = array(
            'var' => "title",
            'desc' => $msg['cms_module_common_datasource_desc_title']
        );
        $main_fields[] = array(
            'var' => "resume",
            'desc' => $msg['cms_module_common_datasource_desc_resume']
        );
        if ($type == "article") {
            $main_fields[] = array(
                'var' => "content",
                'desc' => $msg['cms_module_common_datasource_desc_content']
            );
        }
        $main_fields[] = array(
            'var' => "logo",
            'children' => self::prefix_var_tree(cms_logo::get_format_data_structure(false, false), "logo"),
            'desc' => $msg['cms_module_common_datasource_desc_logo']
        );
        $main_fields[] = array(
            'var' => "publication_state",
            'desc' => $msg['cms_module_common_datasource_desc_publication_state']
        );
        $main_fields[] = array(
            'var' => "start_date",
            'desc' => $msg['cms_module_common_datasource_desc_start_date']
        );
        $main_fields[] = array(
            'var' => "end_date",
            'desc' => $msg['cms_module_common_datasource_desc_end_date']
        );
        $main_fields[] = array(
            'var' => "descriptors",
            'desc' => $msg['cms_module_common_datasource_desc_descriptors'],
            'children' => array(
                array(
                    'var' => "descriptors[i].id",
                    'desc' => $msg['cms_module_common_datasource_desc_descriptors_id']
                ),
                array(
                    'var' => "descriptors[i].name",
                    'desc' => $msg['cms_module_common_datasource_desc_descriptors_name']
                ),
                array(
                    'var' => "descriptors[i].comment",
                    'desc' => $msg['cms_module_common_datasource_desc_descriptors_comment']
                ),
                array(
                    'var' => "descriptors[i].lang",
                    'desc' => $msg['cms_module_common_datasource_desc_descriptors_lang']
                )
            )
        );
        // Concepts
        if ($thesaurus_concepts_active) {
            $main_fields[] = array(
                'var' => "concepts",
                'desc' => $msg['cms_module_common_datasource_desc_concepts'],
                'children' => self::prefix_var_tree(cms_concept::get_format_data_structure(), "concepts[i]")
            );
        }

        $main_fields[] = array(
            'var' => "num_type",
            'desc' => $msg['cms_module_common_datasource_desc_num_type_' . $type]
        );
        $main_fields[] = array(
            'var' => "type",
            'desc' => $msg['cms_module_common_datasource_desc_type_' . $type]
        );
        $main_fields[] = array(
            'var' => "fields_type",
            'desc' => $msg['cms_module_common_datasource_desc_fields_type_' . $type]
        );
        $main_fields[] = array(
            'var' => "create_date",
            'desc' => $msg['cms_module_common_datasource_desc_create_date']
        );
        $main_fields[] = array(
            'var' => "last_update_date",
            'desc' => $msg['cms_module_common_datasource_desc_last_update_date']
        );
        $main_fields[] = array(
            'var' => "last_update_sql_date",
            'desc' => $msg['cms_module_common_datasource_desc_last_update_sql_date']
        );

        $main_fields[] = array(
            'var' => "permalink",
            'desc' => $msg['cms_editorial_form_permalink']
        );

        $main_fields[] = array(
            'var' => "social_media_sharing",
            'desc' => $msg['cms_editorial_form_social_media_sharing']
        );

        if ($opac_avis_allow) {
            $main_fields[] = array(
                'var' => "avis_display",
                'desc' => $msg['cms_module_common_datasource_desc_avis_display']
            );
        }

        // pour les types de contenu
        $fields_type = array();
        $types = new cms_editorial_types($type);
        $fields_type = $types->get_format_data_structure($full);
        return array(
            array(
                'var' => $msg['cms_module_common_datasource_main_fields'],
                "children" => $main_fields
            ),
            array(
                'var' => "nb_documents",
                'desc' => $msg['cms_module_common_datasource_desc_nb_documents']
            ),
            array(
                'var' => "documents",
                'desc' => $msg['cms_module_common_datasource_desc_documents'],
                'children' => self::prefix_var_tree(cms_document::get_format_data_structure(), "documents[i]")
            ),
            array(
                'var' => $msg['cms_module_common_datasource_types'],
                'desc' => $msg['cms_module_common_datasource_desc_types'],
                "children" => $fields_type
            )
        );
    }

    public static function prefix_var_tree($tree, $prefix)
    {
        for ($i = 0; $i < count($tree); $i ++) {
            $tree[$i]['var'] = $prefix . "." . $tree[$i]['var'];
            if (isset($tree[$i]['children']) && $tree[$i]['children']) {
                $tree[$i]['children'] = self::prefix_var_tree($tree[$i]['children'], $prefix);
            }
        }
        return $tree;
    }

    public function get_documents_form()
    {
        $collections = new cms_collections();
        return $collections->get_documents_form($this->get_documents());
    }

    public function get_documents()
    {
        if (! isset($this->documents_linked)) {
            $this->documents_linked = array();
            $query = "select document_link_num_document from cms_documents_links join cms_documents on document_link_num_document = id_document where document_link_type_object = '" . $this->type . "' and document_link_num_object = " . $this->id . " order by document_create_date desc";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    $this->documents_linked[] = $row->document_link_num_document;
                }
            }
        }
        return $this->documents_linked;
    }

    public function save_documents()
    {
        // on commence par tout virer
        $query = "delete from cms_documents_links where document_link_type_object = '" . $this->type . "' and document_link_num_object = " . $this->id;
        pmb_mysql_query($query);

        if (count($this->get_documents())) {
            $query = "insert into cms_documents_links (document_link_type_object,document_link_num_object,document_link_num_document) values";
            $documents = "";
            foreach ($this->documents_linked as $doc) {
                if ($documents)
                    $documents .= ",";
                $documents .= "('" . $this->type . "'," . $this->id . ",'" . $doc . "')";
            }
            pmb_mysql_query($query . $documents);
        }
    }

    public function get_index_concept_type_constant()
    {
        $type_constant = 0;
        switch ($this->type) {
            case 'section':
                $type_constant = TYPE_CMS_SECTION;
                break;
            case 'article':
                $type_constant = TYPE_CMS_ARTICLE;
                break;
        }
        return $type_constant;
    }

    public function get_concepts_field($name)
    {
        global $thesaurus_concepts_active;
        // Indexation concept
        if ($thesaurus_concepts_active == 1) {
            return $this->index_concept->get_form($name);
        }
        return '';
    }

    public function save_concepts()
    {
        global $thesaurus_concepts_active;
        // Indexation concepts
        if ($thesaurus_concepts_active == 1) {
            if (! $this->index_concept->get_object_id()) {
                $this->index_concept->set_object_id($this->id);
            }
            $this->index_concept->save(false);
        }
    }

    protected function get_index_concept()
    {
        global $thesaurus_concepts_active;
        // Indexation concepts
        if ($thesaurus_concepts_active == 1) {
            $type_constant = $this->get_index_concept_type_constant();
            if ($type_constant) {
                $this->index_concept = new index_concept($this->id, $type_constant);
            }
        }
    }

    public function get_permalink()
    {
        if ($this->num_page && $this->var_name) { // Le type d'élément sur lequel on se trouve a une page et une variable d'environnement renseignés
            return "./index.php?lvl=cmspage&pageid=" . $this->num_page . "&" . $this->var_name . "=" . $this->id;
        }
        return '';
    }

    protected function get_permalink_field()
    {
        global $cms_editorial_permalink_field;
        global $opac_url_base;
        if ($this->get_permalink()) {
            $link = '<a href="' . $opac_url_base . $this->get_permalink() . '" target="_blank">' . $opac_url_base . $this->get_permalink() . '</a>';
            return str_replace("!!cms_editorial_form_permalink!!", $link, $cms_editorial_permalink_field);
        } else {
            return "";
        }
    }

    public function get_social_media_block()
    {
        global $opac_url_base;
        return "
			<div id='el" . $this->type . $this->id . "addthis' class='addthis_toolbox addthis_default_style '
				addthis:url='" . $opac_url_base . $this->get_permalink() . "'>
			</div>
			<script>
				if(param_social_network){
					creeAddthis('el" . $this->type . $this->id . "');
				}else{
					waitingAddthisLoaded('el" . $this->type . $this->id . "');
				}
			</script>";
    }

    public function get_num_page()
    {
        return $this->num_page;
    }

    public function set_num_page($num_page)
    {
        $this->num_page = $num_page;
    }

    public function get_var_name()
    {
        return $this->var_name;
    }

    public function set_var_name($var_name)
    {
        $this->var_name = $var_name;
    }

    public function get_id()
    {
        return $this->id;
    }

    /**
     * Retourne l'autorisation des avis
     *
     * @return boolean
     */
    public function get_avis_allowed()
    {
        global $opac_avis_allow;
        global $allow_avis;
        if (($opac_avis_allow && $opac_avis_allow != 2) || ($_SESSION["user_code"] && $opac_avis_allow == 2 && $allow_avis)) {
            return true;
        } else {
            return false;
        }
    }

    protected function get_generic_fields_class()
    {
        if (isset(self::$generic_classes[$this->type])) {
            return self::$generic_classes[$this->type];
        }
        $query = "select id_editorial_type from cms_editorial_types where editorial_type_element = '" . $this->type . "_generic'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            self::$generic_classes[$this->type] = new cms_editorial_parametres_perso(pmb_mysql_result($result, 0, 0));
        }
        return self::$generic_classes[$this->type];
    }

    public function format_datas($links_patterns = [])
    {
        $cms_editorial_data = new cms_editorial_data($this->id, $this->type, $links_patterns);
        return $cms_editorial_data;
    }
}