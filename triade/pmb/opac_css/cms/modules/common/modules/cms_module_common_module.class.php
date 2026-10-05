<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_module.class.php,v 1.88.2.2 2025/01/21 09:58:09 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class cms_module_common_module extends cms_module_root
{

    protected $module_path = "";

    protected $manifest;

    public $informations = array();

    public $elements_used = array();

    public $name = "";

    public $styles;

    public $dom_parent;

    public $dom_after;

    protected $datasource = array();

    protected $filters = array();

    protected $view = array();

    protected $conditions = array();

    protected $managed_datas;

    public $fixed = false;

    protected $extension_datas = array();

    public $memo_url = "";

    protected $modcache = "get_post_view";

    protected $selected_datasource = "";

    protected $selected_view = "";

    public $css_class = "";

    public $classement = "";

    public $classement_list = array();

    public $cadre_url;

    public function __construct($id = 0)
    {
        $this->id = (int) $id;
        $infos = self::read_manifest();
        $this->informations = $infos['informations'];
        $this->elements_used = $infos['elements_used'];
        parent::__construct();
        // on va chercher le contenu de la boite noire...
        $this->fetch_managed_datas();
    }

    public static function get_informations()
    {
        $infos = self::read_manifest();
        return $infos['informations'];
    }

    public static function read_manifest()
    {
        global $base_path, $lang;
        $informations = array();
        $manifest = new domDocument();
        $module_path = realpath(dirname($base_path . "/cms/modules/" . str_replace("cms_module_", "", static::class) . "/" . static::class . ".class.php"));

        $manifest->load($module_path . "/manifest.xml");

        // on récupère la langue par défaut du module...
        $informations['informations']['default_language'] = self::get_module_default_language($manifest);

        // on récupère le nom
        $names = $manifest->getElementsByTagName("name");
        $name = array();
        for ($i = 0; $i < $names->length; $i ++) {
            if ($names->item($i)->parentNode->nodeName == "manifest") {
                if (isset($names->item($i)->attributes->getNamedItem('lang')->nodeValue) && ($names->item($i)->attributes->getNamedItem('lang')->nodeValue == $lang)) {
                    $name[$lang] = $names->item($i)->nodeValue;
                    break;
                } else if (! $names->item($i)->attributes->getNamedItem('lang') || ($names->item($i)->attributes->getNamedItem('lang')->nodeValue == $informations['informations']['default_language'])) {
                    $name['default'] = $names->item($i)->nodeValue;
                }
            }
        }
        $informations['informations']['name'] = cms_module_root::charset_normalize(isset($name[$lang]) ? $name[$lang] : $name['default'], "utf-8");

        // on récupère le(les) auteur(s)
        $informations['informations']['author'] = array();
        $authors = $manifest->getElementsByTagName("author");
        for ($i = 0; $i < $authors->length; $i ++) {
            $author = array();
            // on récupère son nom
            $author['name'] = cms_module_root::charset_normalize($authors->item($i)
                ->getElementsByTagName('name')
                ->item(0)->nodeValue, "utf-8");
            // on récupère son organisation
            $organisation = $authors->item($i)->getElementsByTagName("organisation");
            if ($organisation->length > 0) {
                $author['organisation'] = cms_module_root::charset_normalize($organisation->item(0)->nodeValue, "utf-8");
            }
            $informations['informations']['author'][] = $author;
        }

        // on récupère les dates
        $created_date = $manifest->getElementsByTagName("created_date")->item(0);
        $informations['informations']['created_date'] = cms_module_root::charset_normalize($created_date->nodeValue, "utf-8");
        $updated_date = $manifest->getElementsByTagName("updated_date");
        if ($updated_date->length > 0) {
            $informations['informations']['updated_date'] = cms_module_root::charset_normalize($updated_date->item(0)->nodeValue, "utf-8");
        }
        // on récupère la version
        $version = $manifest->getElementsByTagName("version")->item(0);
        $informations['informations']['version'] = cms_module_root::charset_normalize($version->nodeValue, "utf-8");

        // administrable?
        $informations['informations']['managed'] = ($manifest->getElementsByTagName("managed") && $manifest->getElementsByTagName("managed")->item(0)->nodeValue == "true" ? true : false);

        // fournisseur de liens?
        if (isset($manifest->getElementsByTagName("extension_form")->item(0)->nodeValue)) {
            $informations['informations']['extension_form'] = ($manifest->getElementsByTagName("extension_form")->item(0)->nodeValue == "true" ? true : false);
        } else {
            $informations['informations']['extension_form'] = '';
        }

        // on récupère la listes des éléments utilisés par le module...
        $use = $manifest->getElementsbyTagName("use")->item(0);
        $informations['elements_used'] = self::read_elements_used($use);
        return $informations;
    }

    protected function fetch_datas()
    {
        if ($this->id) {
            $this->classement_list = array();
            // on va cherches les infos du cadres...
            $query = "select * from cms_cadres where id_cadre = '" . $this->id . "'";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_object($result);
                $this->id = (int) $row->id_cadre;
                $this->hash = $row->cadre_hash;
                $this->name = $row->cadre_name;
                $this->fixed = $row->cadre_fixed;
                $this->styles = unserialize($row->cadre_styles);
                $this->dom_parent = $row->cadre_dom_parent;
                $this->dom_after = $row->cadre_dom_after;
                $this->memo_url = $row->cadre_memo_url;
                $this->cadre_url = $row->cadre_url;
                $this->classement = $row->cadre_classement;
                $this->modcache = $row->cadre_modcache;
                $this->css_class = $row->cadre_css_class;
                $query = "select id_cadre_content,cadre_content_object,cadre_content_type from cms_cadre_content where cadre_content_num_cadre = '" . $this->id . "' and cadre_content_num_cadre_content=0";
                $result = pmb_mysql_query($query);
                if ($result && pmb_mysql_num_rows($result)) {
                    while ($ligne = pmb_mysql_fetch_object($result)) {
                        switch ($ligne->cadre_content_type) {
                            case "datasource":
                                $this->datasource = array(
                                    'id' => (int) $ligne->id_cadre_content,
                                    'name' => $ligne->cadre_content_object
                                );
                                break;
                            case "filter":
                                $this->filters[] = array(
                                    'id' => (int) $ligne->id_cadre_content,
                                    'name' => $ligne->cadre_content_object
                                );
                                break;
                            case "view":
                                $this->view = array(
                                    'id' => (int) $ligne->id_cadre_content,
                                    'name' => $ligne->cadre_content_object
                                );
                                break;
                            case "condition":
                                $this->conditions[] = array(
                                    'id' => (int) $ligne->id_cadre_content,
                                    'name' => $ligne->cadre_content_object
                                );
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }
    }

    public static function read_elements_used($use_node)
    {
        $elements_used = array();
        $types = array(
            'condition',
            'view',
            'datasource',
            'filter'
        );
        foreach ($types as $type) {
            $elements = $use_node->getElementsByTagName($type);
            $elements_used[$type] = array();
            if ($elements->length > 0) {
                for ($i = 0; $i < $elements->length; $i ++) {
                    // On regarde si l'élément est conditionné sur un paramètre
                    if ($elements->item($i)->getAttribute('conditionParam')) {
                        global ${$elements->item($i)->getAttribute('conditionParam')};
                        if (! ${$elements->item($i)->getAttribute('conditionParam')}) {
                            // Le paramètre n'est pas vérifié, on s'arrête là
                            continue;
                        }
                    }
                    if (($elements->item($i)->nodeValue != "")) {
                        $elements_used[$type][] = $elements->item($i)->nodeValue;
                    }
                }
            }
        }
        // certaines conditions sont par défaut dans tous les modules...
        if (! in_array("cms_module_common_condition_authentificated", $elements_used['condition']))
            $elements_used['condition'][] = "cms_module_common_condition_authentificated";
        if (! in_array("cms_module_common_condition_page", $elements_used['condition']))
            $elements_used['condition'][] = "cms_module_common_condition_page";
        if (! in_array("cms_module_common_condition_lvl", $elements_used['condition']))
            $elements_used['condition'][] = "cms_module_common_condition_lvl";
        if (! in_array("cms_module_common_condition_global_var", $elements_used['condition']))
            $elements_used['condition'][] = "cms_module_common_condition_global_var";
        if (! in_array("cms_module_common_condition_global_var_value", $elements_used['condition']))
            $elements_used['condition'][] = "cms_module_common_condition_global_var_value";
        if (! in_array("cms_module_common_condition_view", $elements_used['condition']))
            $elements_used['condition'][] = "cms_module_common_condition_view";
        if (! in_array("cms_module_common_condition_lang", $elements_used['condition']))
            $elements_used['condition'][] = "cms_module_common_condition_lang";
        if (! in_array("cms_module_common_condition_readers_categories", $elements_used['condition']))
            $elements_used['condition'][] = "cms_module_common_condition_readers_categories";
        if (! in_array("cms_module_common_condition_readers_statuses", $elements_used['condition']))
            $elements_used['condition'][] = "cms_module_common_condition_readers_statuses";

        return $elements_used;
    }

    public static function get_elements_used($file = "")
    {
        // on récupère la partie intéressante du manifest...
        $dom = new domDocument();
        $dom->load($file);
        $use = $dom->getElementsbyTagName("use")->item(0);
        $elements_used = self::read_elements_used($use);
        return $elements_used;
    }

    protected function get_datasources_list_form()
    {
        if (is_countable($this->elements_used['datasource']) && count($this->elements_used['datasource']) > 1) {
            $form = "
			<div class='colonne3'>
				<label for='datasource_choice'>" . $this->format_text($this->msg['cms_module_datasource_choice']) . "</label>
			</div>
			<div class='colonne-suite'>
				<select name='datasource_choice' onchange='load_datasource_form(this.value)'>
					<option value=''>" . $this->format_text($this->msg['cms_module_datasource_choice']) . "</option>";
            foreach ($this->elements_used['datasource'] as $datasource) {

                $selected = "";
                if (isset($this->datasource['name']) && $datasource == $this->datasource['name']) {
                    $selected = "selected='selected'";
                }

                $form .= sprintf('<option value="%s" %s>%s</option>', $datasource, $selected, $this->format_text($this->msg[$datasource] ?? $datasource));
            }
            $form .= "
				</select>
				<script>
					function load_datasource_form(datasource){
						if(datasource != ''){
							cms_module_load_elem_form(datasource,'0','datasource_form');
						}
					}
				</script>
			</div>";
        } else {
            $datasource = !empty($this->elements_used['datasource']) ? $this->elements_used['datasource'][0] : "";
            $form = "
				<input type='hidden' name='datasource_choice' value='" . $datasource . "'/>";
        }
        return $form;
    }

    protected function get_views_list_form()
    {
        if (is_countable($this->elements_used['view']) && count($this->elements_used['view']) > 1) {
            $form = "
				<div class='colonne3'>
					<label for='view_choice'>" . $this->format_text($this->msg['cms_module_common_module_view_choice']) . "</label>
				</div>
				<div class='colonne-suite'>
					<select name='view_choice' onchange='load_view_form(this.value)'>
						<option value=''>" . $this->format_text($this->msg['cms_module_view_choice']) . "</option>";
            foreach ($this->elements_used['view'] as $view) {
                $form .= "
						<option value='" . $view . "'" . (isset($this->view['name']) && $view == $this->view['name'] ? " selected='selected'" : "") . ">" . $this->format_text($this->msg[$view]) . "</option>";
            }
            $form .= "
					</select>
				<script>
					function load_view_form(datasource){
						if(datasource != ''){
							cms_module_load_elem_form(datasource,'0','view_form');
						}
					}
				</script>
			</div>";
        } else {
            $view = !empty($this->elements_used['view']) ? $this->elements_used['view'][0] : "";
            $form = "
					<input type='hidden' name='view_choice' value='" . $view . "'/>";
        }

        return $form;
    }

    public function get_form($ajax = true, $callback = '', $cancel_callback = '', $delete_callback = '', $action = "?action=save")
    {
        global $charset, $cms_active;

        // en création ,on peut permettre certaines choses par défaut (pré-chargement de conditions,...)
        if (! $this->id) {
            $this->creation_init();
        }
        if ($ajax) {
            $action = "./ajax.php?module=cms&categ=module&elem=" . $this->class_name . "&action=save_form";
        }
        $forbidden_duplication = false;
        if (func_num_args() > 5 && ! empty(func_get_arg(5))) {
            $forbidden_duplication = true;
        }
        $form = "
		<script>
			function test_form_" . $this->class_name . "() {
				if(document.getElementById('cms_module_common_module_name').value=='' ) {
					alert(\"" . $this->msg['cms_module_common_module_name_empty'] . "\");
					document.getElementById('cms_module_common_module_name').focus();
					return false;
				}
				return true;
			}
		</script>
		<form name='" . $this->class_name . "_form' id='" . $this->class_name . "_form' method='POST' action='" . $action . "' style='width:800px'>
			<h3>" . $this->format_text(($this->id ? sprintf($this->msg['cms_module_common_module_alter_cadre'], $this->informations['name'] . " : " . $this->class_name . "_" . $this->id) : $this->msg['cms_module_common_module_new_cadre'] . " - " . $this->informations['name'])) . "</h3>
			<div class='form-contenu'>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_module_name'>" . $this->msg['cms_module_common_module_name'] . "</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' id='cms_module_common_module_name' name='cms_module_common_module_name' value = '" . addslashes($this->format_text($this->name)) . "' />
					</div>
				</div>";

                if($cms_active != 2) {
                    $form .= "
                        <div class='row'>
                            <div class='colonne3'>
                                <label for='cms_module_common_module_fixed'>" . $this->msg['cms_module_common_module_fixed'] . "</label>
                            </div>
                            <div class='colonne-suite'>
                                <input type='checkbox' id='cms_module_common_module_fixed' name='cms_module_common_module_fixed' value='1' " . ($this->fixed && is_countable($this->conditions) && count($this->conditions) == 0 ? "checked='checked'" : "") . (is_countable($this->conditions) && count($this->conditions) ? "disabled" : "") . "/>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='colonne3'>
                                <label for='cms_module_common_module_memo_url'>" . $this->msg['cms_module_common_module_memo_url'] . "</label>
                            </div>
                            <div class='colonne-suite'>
                                <input type='checkbox' name='cms_module_common_module_memo_url' value='1' " . ($this->memo_url ? "checked='checked'" : "") . "/>
                            </div>
                        </div>";
                }

                $form .= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_module_modcache'>" . $this->msg['cms_module_common_module_modcache'] . "</label>
					</div>
					<div class='colonne_suite'>
						<select name='cms_module_common_module_modcache'>";
        $modcache_choices = $this->get_modcache_choices();
        foreach ($modcache_choices as $choice) {
            $form .= "
							<option value='" . $choice['value'] . "'" . ($this->modcache == $choice['value'] ? " selected='selected'" : "") . ">" . $this->format_text($choice['name']) . "</option>";
        }
        $form .= "
						</select>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_module_css_class'>" . $this->msg['cms_module_common_module_css_class'] . "</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_module_css_class' value='" . addslashes($this->format_text($this->css_class)) . "'/>
					</div>
				</div>
				<div class='row'></div>
				<hr/>";

        $form .= $this->get_conditions_form();
        $form .= "
				<hr/>
				<div class='row'>";
        $form .= $this->get_datasources_list_form();
        if ((isset($this->datasource['id']) && $this->datasource['id']) || (is_countable($this->elements_used['datasource']) && count($this->elements_used['datasource']) == 1)) {
            if (isset($this->datasource['id']) && $this->datasource['id']) {
                $datasource_name = $this->datasource['name'];
                $datasource_id = $this->datasource['id'];
            } else if (is_countable($this->elements_used['datasource']) && count($this->elements_used['datasource']) == 1) {
                $datasource_name = $this->elements_used['datasource'][0];
                $datasource_id = 0;
            }
            $form .= "
				<script>
					cms_module_load_elem_form('" . $datasource_name . "','" . $datasource_id . "','datasource_form');
				</script>";
        }
        $form .= "
				<div id='datasource_form' dojoType='dojox.layout.ContentPane'>
				</div>
			</div>";
        $form .= $this->get_filters_form();
        $form .= "
			<hr/>
			<div class='row'>";
        $form .= $this->get_views_list_form();
        if ((isset($this->view['id']) && $this->view['id']) || (is_countable($this->elements_used['view']) && count($this->elements_used['view']) == 1)) {
            if (isset($this->view['id']) && $this->view['id']) {
                $view_name = $this->view['name'];
                $view_id = $this->view['id'];
            } else if (is_countable($this->elements_used['view']) && count($this->elements_used['view']) == 1) {
                $view_name = $this->elements_used['view'][0];
                $view_id = 0;
            }
            $form .= "
				<script>
					cms_module_load_elem_form('" . $view_name . "','" . $view_id . "','view_form');
				</script>";
        }
        $form .= "
				<div id='view_form' dojoType='dojox.layout.ContentPane'>
				</div>
				<div class='row'>&nbsp;</div>
			</div>
			<div class='row'><hr></div>
			<div class='row'>
				<input type='hidden' name='cms_build_info' id='cms_build_info' value='" . htmlentities(serialize($this->cms_build_env), ENT_QUOTES, $charset) . "' />
				<input type='hidden' name='cms_module_common_module_id' id='cms_module_common_module_id' value='" . $this->id . "' />
				<div class='left'>
					<input type='submit' id='cms_module_common_module_submit' class='bouton' value='" . $this->msg['cms_module_common_module_save'] . "' " . ($ajax ? "onclick=\"if(test_form_" . $this->class_name . "())cms_module_save();return false;\"" : "") . "/>
					&nbsp;
					<input type='button' class='bouton' value='" . $this->msg['cms_module_common_module_cancel'] . "' " . ($cancel_callback != '' ? "onclick='" . $cancel_callback . "();'" : "") . "/>
				</div>
				<div class='right'>" . (($this->id && $ajax && ! $forbidden_duplication) ? "<input type='submit' id='cms_module_common_module_submit' class='bouton' value='" . $this->msg['cms_module_common_module_duplicate'] . "' " . ($ajax ? "onclick=\"if(test_form_" . $this->class_name . "())cms_module_duplicate();return false;\"" : "") . "/>&nbsp;" : "") . "
					<input type='button' class='bouton' value='" . $this->msg['cms_module_common_module_delete'] . "' onclick=\"if(confirm('" . addslashes($this->msg['cms_module_common_module_confirm_delete']) . "')) {cms_module_confirm_delete()}\"/>
				</div>
			</div>
			<div class='row'></div>
		</form>
		<script>
			function cms_module_load_elem_form(elem,id,dom_id){
				dojo.xhrPost({
					url : './ajax.php?module=cms&categ=module&elem='+elem+'&action=get_form&id='+id,
					postData : 'cms_build_info=" . rawurlencode(serialize(pmb_base64_encode($this->cms_build_env))) . "&cms_module_class=" . rawurlencode($this->class_name) . "',
					handelAs : 'text/html',
					load : function(data){
						dijit.byId(dom_id).set('content',data);
					}
				});
			}
		</script>";
        if ($ajax) {
            $form .= "
		<script>
			function cms_module_save(){
				dojo.xhrPost({
					form: '" . $this->class_name . "_form',
					handleAs: 'json',
					load: function(data) {
						dojo.byId('cms_module_common_module_id').value = data;";
            if ($callback != '') {
                $form .= "
						if(typeof(" . $callback . ") == 'function'){
							" . $callback . "(data);
						}else{
							alert('" . addslashes($this->msg['cms_module_common_module_saved']) . "');
						}";
            } else {
                $form .= "
						alert('" . addslashes($this->msg['cms_module_common_module_saved']) . "');";
            }
            $form .= "
					},
					error: function(error) {
						alert('" . addslashes($this->msg['cms_module_common_module_save_error']) . "');
					}
				});
			}
            function cms_module_duplicate(){
                //On veut sauvegarder notre formulaire comme si c'était un nouveau
                document.forms['" . $this->class_name . "_form'].action = document.forms['" . $this->class_name . "_form'].action.replace('action=save_form','action=duplicate_form');
                //C'est une copie, donc on le dit
				document.forms['" . $this->class_name . "_form'].cms_module_common_module_name.value = 'Copie de '+document.forms['" . $this->class_name . "_form'].cms_module_common_module_name.value
				cms_module_save();
            }
            function cms_module_confirm_delete(){
                dojo.xhrGet({
					url: './ajax.php?module=cms&categ=module&elem=" . $this->class_name . "&action=confirm_delete&id=" . $this->id . "',
					handleAs: 'json',
					load: function(data) {
                        if (data.message) {
                            if (confirm(data.message)) {
                                cms_module_delete();
                            }
                        } else {
                            cms_module_delete();
                        }
                    },
                    error: function(error) {
						alert('" . addslashes($this->msg['cms_module_common_module_delete_error']) . "');
					}
				});
            }

			function cms_module_delete(){
				dojo.xhrGet({
					url: './ajax.php?module=cms&categ=module&elem=" . $this->class_name . "&action=delete&id=" . $this->id . "',
					handleAs: 'json',
					load: function(data) {";
            if ($delete_callback != '') {
                $form .= "
						if(typeof(" . $delete_callback . ") == 'function'){
							" . $delete_callback . "(data);
						}else{
							alert('" . addslashes($this->msg['cms_module_common_module_deleted']) . "');
						}";
            } else {
                $form .= "
						alert('" . addslashes($this->msg['cms_module_common_module_deleted']) . "');";
            }
            $form .= "
					},
					error: function(error) {
						alert('" . addslashes($this->msg['cms_module_common_module_delete_error']) . "');
					}
				});
			}
		</script>";
        }
        return $form;
    }

    protected function get_modcache_choices()
    {
        return array(
            array(
                'value' => "no_cache",
                'name' => $this->msg['cms_module_common_module_no_cache']
            ),
            array(
                'value' => "get_post",
                'name' => $this->msg['cms_module_common_module_cache_get_post']
            ),
            array(
                'value' => "get_post_view",
                'name' => $this->msg['cms_module_common_module_cache_get_post_view']
            ),
            array(
                'value' => "view",
                'name' => $this->msg['cms_module_common_module_cache_view']
            ),
            array(
                'value' => "all",
                'name' => $this->msg['cms_module_common_module_cache_all']
            )
        );
    }

    public static function get_hash_cache($obj_name, $id)
    {
        global $lang;
        $str_to_hash = "";
        $hash = "";
        $str_to_hash_more = "";
        // AR - 25/09/2018 : On revient en arrière, le calcul des conditions pour la génération du cache est trop gourmande en .
        // //$obj = cms_modules_parser::get_module_class_by_id($id*1);
        // $query = 'select id_cadre_content, cadre_content_object from cms_cadre_content where cadre_content_type ="condition" and cadre_content_num_cadre = '.$id;
        // $result = pmb_mysql_query($query);
        // $visible = true;
        // if(pmb_mysql_num_rows($result)){
        // while($row = pmb_mysql_fetch_object($result)){
        // $condition = cms_modules_parser::get_module_class_content($row->cadre_content_object, $row->id_cadre_content);
        // $visible = $condition->check_condition();
        // if(!$visible){
        // break;
        // }
        // }
        // }
        // $str_to_hash_more = "visible=".($visible);
        // AR - fin 25/09/2018
        if ($tmp = $_SERVER["REQUEST_URI"]) {
            $matches = array();
            if (preg_match("#/([^/]*?\.php)#i", $tmp, $matches)) {
                if ($tmp2 = trim($matches[1])) {
                    $str_to_hash_more .= $tmp2;
                }
            }
        }
        if (! empty($_SESSION["id_empr_session"])) { // utilisateur connecté
            $str_to_hash_more .= "_empr_is_logged";
        }

        if ($id) {
            $query = "select cadre_modcache from cms_cadres where id_cadre = '" . ($id * 1) . "'";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $mode = pmb_mysql_result($result, 0, 0);
            } else {
                $mode = "get_post";
            }

            switch ($mode) {
                case "no_cache":
                    $str_to_hash = "";
                    break;
                case "get_post_view":
                    $str_to_hash = $obj_name . "_" . serialize($_GET) . "_" . serialize($_POST) . "_" . (isset($_SESSION['opac_view']) ? $_SESSION['opac_view'] : '');
                    break;
                case "view":
                    $str_to_hash = $obj_name . "_" . $_SESSION['opac_view'];
                    break;
                case "all":
                    $str_to_hash = $obj_name;
                    break;
                case "get_post":
                    $str_to_hash = $obj_name . "_" . serialize($_GET) . "_" . serialize($_POST);
                    break;
            }
        }
        if ($str_to_hash) {
            $hash = md5($lang . "_" . $str_to_hash . $str_to_hash_more);
        }
        return $hash;
    }

    public function build_cadre_url()
    {
        global $cms_build_info;

        if (! is_array($cms_build_info))
            return "";
        $url = $cms_build_info['input'] . "?";

        foreach ($cms_build_info['get'] as $key => $val) {
            if ($key != "database" && $key != "cms_build_activate" && $key != "build_id_version")
                $url .= "&$key=$val";
        }
        foreach ($cms_build_info['post'] as $key => $val) {
            $url .= "&$key=$val";
        }
        return $url;
    }

    public function save_form()
    {
        global $datasource_choice;
        global $view_choice;
        global $cms_module_common_module_name;
        global $cms_module_common_module_fixed;
        global $cms_module_common_module_memo_url;
        global $cms_module_common_module_modcache;
        global $cms_module_common_module_css_class;

        $this->name = strip_tags(stripslashes($cms_module_common_module_name));
        // on calcule un hash...
        $this->get_hash();
        // on enregistre le cadre...
        if ($cms_module_common_module_memo_url) {
            $cadre_url = " cadre_url = '" . $this->build_cadre_url() . "', ";
        } else {
            $cadre_url = "";
        }
        if ($this->id) {
            $query = "update cms_cadres set ";
            $clause = " where id_cadre = " . $this->id;
        } else {
            $query = "insert into cms_cadres set ";
            $clause = "";
        }
        $query .= "
			cadre_hash = '" . $this->hash . "',
			cadre_object = '" . $this->class_name . "',
			cadre_name = '" . addslashes($this->name) . "',
			cadre_fixed = " . ($cms_module_common_module_fixed ? "1" : "0") . " ,
			$cadre_url
			cadre_memo_url = " . ($cms_module_common_module_memo_url ? "1" : "0") . ",
			cadre_modcache ='" . addslashes($cms_module_common_module_modcache) . "',
			cadre_css_class = '" . addslashes($cms_module_common_module_css_class) . "'" . $clause;

        $result = pmb_mysql_query($query);
        if ($result) {
            if (! $this->id) {
                $this->id = pmb_mysql_insert_id();
            }

            // les Conditions
            $result = $this->save_conditions();
            if ($result) {
                // source de donnée
                if (! empty($this->datasource['name']) && ($datasource_choice == $this->datasource['name'])) {
                    $datasource_id = $this->datasource['id'];
                } else {
                    $datasource_id = 0;
                }
                $datasource = new $datasource_choice($datasource_id);
                $datasource->set_cadre_parent($this->id);
                $result = $datasource->save_form();
                if ($result) {
                    $this->datasource = array(
                        'id' => $datasource->id,
                        'name' => $datasource_choice
                    );

                    // Filtres
                    $result = $this->save_filters();
                    if ($result) {
                        // Vue
                        if (! empty($this->view['name']) && ($view_choice == $this->view['name'])) {
                            $view_id = $this->view['id'];
                        } else {
                            $view_id = 0;
                        }
                        $view = new $view_choice($view_id);
                        $view->set_cadre_parent($this->id);
                        $result = $view->save_form();
                        if ($result) {
                            $this->view = array(
                                'id' => $view->id,
                                'name' => $view_choice
                            );

                            // reste à nettoyer la table de hash...
                            $this->clean_hash_table();
                            // tout est bon, on a fini
                            return encoding_normalize::utf8_normalize(array(
                                'id' => $this->id,
                                'name' => $this->name,
                                'object' => $this->class_name,
                                'dom_id' => $this->get_dom_id()
                            ));
                        } else {
                            // sauvegarde de la vue ratée, on supprime le cadre...
                            $this->delete();
                        }
                    } else {
                        // sauvegarde des filtres ratée, on supprime le cadre...
                        $this->delete();
                    }
                } else {
                    // sauvegarde de la source de donnée ratée, on supprime le cadre...
                    $this->delete();
                }
            } else {
                // sauvegarde des conditions ratée, on supprime le cadre...
                $this->delete();
            }
        } else {
            // création du cadre ratée, on supprime le hash de la table...
            $this->delete_hash();
        }

        return false;
    }

    public function get_conditions_form()
    {
        $form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_module_conditions_selector'>" . $this->format_text($this->msg['cms_module_common_module_conditions_selector']) . "</label>
				</div>
				<div class='colonne-suite'>
					<select name='cms_module_common_module_conditions_selector' onchange='load_condition_form(this.value);'>
						<option value='0'>" . $this->format_text($this->msg['cms_module_common_module_conditions_selector_choice']) . "</option>";
        for ($i = 0; $i < count($this->elements_used['condition']); $i ++) {
            $form .= "
						<option value='" . $this->elements_used['condition'][$i] . "'>" . $this->format_text($this->msg[$this->elements_used['condition'][$i]]) . "</option>";
        }
        $form .= "
					</select>
					<input type='hidden' name='cms_module_common_module_conditions[]' id='cms_module_common_module_conditions'/>
				</div>
				<div id='cms_module_common_module_conditions_form'>";
        for ($i = 0; $i < count($this->conditions); $i ++) {
            $form .= "
				<script>
					dojo.xhrPost({
						url : './ajax.php?module=cms&categ=module&elem=" . $this->conditions[$i]['name'] . "&action=get_form&id=" . $this->conditions[$i]['id'] . "',
						postData : 'cms_build_info=" . rawurlencode(serialize($this->cms_build_env)) . "&cms_module_class=" . rawurlencode($this->class_name) . "',
						handelAs : 'text/html',
						load : function(data){
							var form_content = dojo.create('div');
							var condition_form= new dojox.layout.ContentPane({
								content : data
							},form_content);
							dojo.place(form_content,'cms_module_common_module_conditions_form');
						}
					});
				</script>";
        }
        $form .= "
				</div>
			</div>
			<script>
				dojo.require('dojox.layout.ContentPane');
				function load_condition_form(condition){
					if(condition!=0){
						var form_content = dojo.create('div');
						dojo.xhrPost({
							url : './ajax.php?module=cms&categ=module&elem='+condition+'&action=get_form&id=0',
							postData : 'cms_build_info=" . rawurlencode(serialize($this->cms_build_env)) . "&cms_module_class=" . rawurlencode($this->class_name) . "',
							handleAs : 'text',
							load : function(data){
								var condition_form= new dojox.layout.ContentPane({
									content : data
								},form_content);
								dojo.place(form_content,'cms_module_common_module_conditions_form');
							}
						});
						document.getElementById('cms_module_common_module_fixed').checked = false;
						document.getElementById('cms_module_common_module_fixed').disabled = true;
					}
				}
			</script>";
        return $form;
    }

    public function save_conditions()
    {
        global $cms_module_common_module_conditions;
        $result = true;
        for ($i = 0; $i < count($cms_module_common_module_conditions); $i ++) {
            if ($cms_module_common_module_conditions[$i] != "") {
                $condition_id = 0;
                for ($j = 0; $j < count($this->conditions); $j ++) {
                    if ($cms_module_common_module_conditions[$i] == $this->conditions[$j]['name']) {
                        $condition_id = $this->conditions[$j]['id'];
                        break;
                    }
                }
                $condition = new $cms_module_common_module_conditions[$i]($condition_id);
                $condition->set_cadre_parent($this->id);
                $result = $condition->save_form();
                if ($result) {
                    if ($condition_id == 0) {
                        $this->conditions[] = array(
                            'id' => $condition->id,
                            'name' => $cms_module_common_module_conditions[$i]
                        );
                    }
                    continue;
                } else {
                    break;
                }
            }
        }
        return $result;
    }

    // Copie de la fonction save_conditions, il y a probablement moyen d'optimiser ça ! :)
    public function save_filters()
    {
        global $cms_module_common_module_filters;

        $result = true;
        if (! empty($cms_module_common_module_filters)) {
            foreach ($cms_module_common_module_filters as $cms_filter) {
                if (! empty($cms_filter)) {
                    $filter_id = 0;
                    foreach ($this->filters as $filter) {
                        if ($cms_filter == $filter['name']) {
                            $filter_id = $filter['id'];
                            break;
                        }
                    }
                }

                $filter_instance = new $cms_filter($filter_id);
                $filter_instance->set_cadre_parent($this->id);
                $result = $filter_instance->save_form();
                if (! empty($result)) {
                    if (empty($filter_id)) {
                        $this->filters[] = [
                            'id' => $filter_instance->id,
                            'name' => $cms_filter
                        ];
                    }
                    continue;
                } else {
                    break;
                }
            }
        }

        return $result;
    }

    public function delete()
    {
        $dom_id = $this->get_dom_id();
        // on commence par supprimer la définition dans le portail...
        $query = "delete from cms_build where build_obj = '" . $dom_id . "'";
        pmb_mysql_query($query);

        // on élimine tous les éléments associés directement au cadre...
        $query = "select id_cadre_content, cadre_content_object from cms_cadre_content where cadre_content_num_cadre = '" . $this->id . "' and cadre_content_num_cadre_content = 0";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            // pour éviter tout problème, on ne supprime pas directement les élements de la table, on appelle la méthode de suppression de l'objet...
            while ($row = pmb_mysql_fetch_object($result)) {
                $elem = new $row->cadre_content_object($row->id_cadre_content);
                $success = $elem->delete();
                if (! $success) {
                    // TODO verbose mode
                    return false;
                }
            }
        }
        // il ne peut en rester qu'un, et c'est perdu pour celui-ci...
        $query = "delete from cms_cadres where id_cadre = '" . $this->id . "'";
        $result = pmb_mysql_query($query);
        if ($result) {
            $this->delete_hash();
            return array(
                'dom_id' => $dom_id
            );
        } else {
            // TODO verbose mode
            return false;
        }
    }

    public function check_conditions()
    {
        if (is_countable($this->conditions)) {
            for ($i = 0; $i < count($this->conditions); $i ++) {
                // $condition = new $this->conditions[$i]['name']($this->conditions[$i]['id']);
                $condition = cms_modules_parser::get_module_class_content($this->conditions[$i]['name'], $this->conditions[$i]['id']);
                if (! $condition->check_condition()) {
                    return false;
                } else {
                    continue;
                }
            }
        }
        return true;
    }

    public function show_cadre()
    {
        if ($this->get_selected_datasource()) {
            if ($this->get_selected_view()) {
                $datasource_data = $this->selected_datasource->get_datasource_data();
                $uniqid = PHP_log::prepare_time($this->name, 'cms'); // Log du render si le traitement est long
                $render = $this->selected_view->render($datasource_data);
                PHP_log::register($uniqid);
                return "<div id='" . $this->get_dom_id() . "' class='" . $this->class_name . " cms_module" . ($this->css_class != '' ? " " . $this->css_class : "") . "'>" . $render . "</div>";
            }
        }
        return "";
    }

    public function get_dom_id()
    {
        return $this->class_name . "_" . $this->id;
    }

    protected function creation_init()
    {
        global $cms_active;

        if (0 == $this->id && 2 == $cms_active) {
            return;
        }

        if ($this->elements_used['condition']) {
            $this->conditions[] = array();
            return;
        }

        // on regarde si des conditions peuvent être pré-chargées...
        for ($i = 0; $i < count($this->elements_used['condition']); $i ++) {
            // appel statique du test de la conditions
            if (call_user_func(array(
                $this->elements_used['condition'][$i],
                'is_loadable_default'
            ))) {
                // si c'est positif, on ajoute la condition...
                $this->conditions[] = array(
                    'id' => 0,
                    'name' => $this->elements_used['condition'][$i]
                );
            }
        }
    }

    public function get_headers($datas = array())
    {
        $headers = array(
            'add' => array(),
            'replace' => array()
        );
        if ($this->get_selected_datasource()) {
            $datasource_headers = $this->selected_datasource->get_headers($datas);
            if (isset($datasource_headers['add']) || isset($datasource_headers['replace'])) {
                $headers['add'] = array_merge($headers['add'], $datasource_headers['add'] ?? []);
                $headers['replace'] = array_merge($headers['replace'], $datasource_headers['replace'] ?? []);
            } else {
                $headers['add'] = array_merge($headers['add'], $datasource_headers);
            }
            $datasource_headers = array();
            $headers['add'] = array_unique($headers['add']);
            $headers['replace'] = array_unique($headers['replace']);
        }
        if ($this->get_selected_view()) {
            $view_headers = $this->selected_view->get_headers($this->selected_datasource->get_datasource_data());
            if (isset($view_headers['add']) || isset($view_headers['replace'])) {
                $headers['add'] = array_merge($headers['add'], $view_headers['add'] ?? []);
                $headers['replace'] = array_merge($headers['replace'], $view_headers['replace'] ?? []);
            } else {
                $headers['add'] = array_merge($headers['add'], $view_headers);
            }
            $view_headers = array();
            $headers['add'] = array_unique($headers['add']);
            $headers['replace'] = array_unique($headers['replace']);
        }
        if (is_callable($this->conditions)) {
            for ($i = 0; $i < count($this->conditions); $i ++) {
                // $condition = new $this->conditions[$i]['name']($this->conditions[$i]['id']);
                $condition = cms_modules_parser::get_module_class_content($this->conditions[$i]['name'], $this->conditions[$i]['id']);
                $condition_headers = $condition->get_headers($datas);
                if (isset($condition_headers['add']) || isset($condition_headers['replace'])) {
                    $headers['add'] = array_merge($headers['add'], $condition_headers['add'] ?? []);
                    $headers['replace'] = array_merge($headers['replace'], $condition_headers['replace'] ?? []);
                } else {
                    $headers['add'] = array_merge($headers['add'], $condition_headers);
                }
                $condition_headers = array();
                $headers['add'] = array_unique($headers['add']);
                $headers['replace'] = array_unique($headers['replace']);
            }
        }
        return $headers;
    }

    protected function fetch_managed_datas($type = "")
    {
        $query = "select managed_module_box from cms_managed_modules where managed_module_name = '" . $this->class_name . "'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $this->managed_datas = @unserialize(pmb_mysql_result($result, 0, 0));
        }
    }

    public function get_manage_forms()
    {
        global $quoi;

        $form = '';
        switch ($quoi) {
            case "views":
            case "datasources":
            case "conditions":
                $form = $this->get_element_manage_form($quoi);
                break;
            case "module":
                $form = $this->get_manage_form();
                break;
        }
        return $form;
    }

    public function save_manage_forms()
    {
        global $quoi, $elem;

        // on sauvegarde les infos modifiées
        switch ($quoi) {
            case "views":
            case "datasources":
            case "conditions":
                $this->managed_datas[$quoi][$elem] = call_user_func(array(
                    new $elem(),
                    "save_manage_form"
                ), $this->managed_datas[$quoi][$elem] ?? []);
                break;
            case "module":
                $this->managed_datas[$quoi] = $this->save_manage_form();
                break;
        }
        $query = "replace into cms_managed_modules set managed_module_name = '" . $this->class_name . "', managed_module_box = '" . addslashes(serialize($this->managed_datas)) . "'";
        return pmb_mysql_query($query);
    }

    public function get_manage_menu()
    {
        global $javascript_path;
        $manage_menu = "
			<script src='" . $javascript_path . "/cms/cms_form.js'></script>
		";
        // on regarde si le module lui-même est administrable
        if (method_exists($this->class_name, "get_manage_form")) {
            $manage_menu .= "
			<span" . ongletSelect("categ=manage&sub=" . str_replace("cms_module_", "", $this->class_name) . "&quoi=module") . ">
				<a title='" . $this->format_text($this->msg["cms_manage_module_general"]) . "' href='./cms.php?categ=manage&sub=" . str_replace("cms_module_", "", $this->class_name) . "&quoi=module'>
					" . $this->format_text($this->msg["cms_manage_module_general"]) . "
				</a>
			</span>";
        }
        // on regarde aussi pour chaque type d'éléments
        $elements = array(
            "view",
            "datasource",
            "condition"
        );
        foreach ($elements as $element) {
            if ($this->check_managed_elem($element)) {
                $manage_menu .= "
		<span" . ongletSelect("categ=manage&sub=" . str_replace("cms_module_", "", $this->class_name) . "&quoi=" . $element . "s") . ">
			<a title='" . $this->format_text($this->msg["cms_manage_module_" . $element . "s"]) . "' href='./cms.php?categ=manage&sub=" . str_replace("cms_module_", "", $this->class_name) . "&quoi=" . $element . "s'>
				" . $this->format_text($this->msg["cms_manage_module_" . $element . "s"]) . "
			</a>
		</span>";
            }
        }
        return $manage_menu;
    }

    protected function check_managed_elem($elem)
    {
        foreach ($this->elements_used[$elem] as $element) {
            if (method_exists($element, "get_manage_form")) {
                return true;
            }
        }
        return false;
    }

    protected function get_element_manage_form($quoi)
    {
        global $base_path;
        global $elem;
        $type = substr($quoi, 0, strlen($quoi) - 1);
        $nb_managed_elems = 0;
        $elem_choice = "";
        if (is_countable($this->elements_used[$type])) {
            for ($i = 0; $i < count($this->elements_used[$type]); $i ++) {
                if (method_exists($this->elements_used[$type][$i], "get_manage_form")) {
                    if (! $elem)
                        $elem = $this->elements_used[$type][$i];
                    $nb_managed_elems ++;
                    if (array_key_exists($this->elements_used[$type][$i], $this->msg)) {
                        $elem_choice .= "<p><a href='" . $base_path . "/cms.php?categ=manage&sub=" . str_replace("cms_module_", "", $this->class_name) . "&quoi=" . $quoi . "&elem=" . $this->elements_used[$type][$i] . "&action=get_form'>" . $this->format_text($this->msg[$this->elements_used[$type][$i]]) . "</a></p>";
                    }
                }
            }
        }

        $form = "
		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>";
        if ($nb_managed_elems > 1) {
            $form .= "
			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:300px;' >
				" . $elem_choice . "
			</div>";
        }
        $form .= "
			<div dojoType='dijit.layout.ContentPane' region='center' >";
        $view = new $elem();
        $view->set_module_class_name($this->class_name);
        $form .= $view->get_manage_form();
        $form .= "
			</div>
		</div>";
        return $form;
    }

    protected function get_filters_form()
    {
        $form = "";
        if (is_countable($this->elements_used['filter']) && count($this->elements_used['filter'])) {
            $form .= "
 			<hr/>
 			<div class='row'>
 				<div class='colonne3'>
 					<label>" . $this->format_text($this->msg['cms_module_common_module_filter_label']) . "
 				</div>
 				<div class='colonne_suite'>
 					<select name='filter_choice' onchange='load_filter_form(this)'>
 						<option value=''>" . $this->format_text($this->msg['cms_module_common_module_filter_choice']) . "</option>";
            foreach ($this->elements_used['filter'] as $filter) {
                $form .= "
 						<option value='$filter'>" . $this->format_text($this->msg[$filter] ?? $filter) . "</option>";
            }
            $form .= "
 					</select>
  					<script>
 						function load_filter_form(select) {
                            let filter = select.value;
					 		if (filter != '') {
							 	var form_content = dojo.create('div');
        						dojo.xhrPost({
        							url : './ajax.php?module=cms&categ=module&elem=' + filter + '&action=get_form&id=0',
        							postData : 'cms_build_info=" . rawurlencode(serialize(pmb_base64_encode($this->cms_build_env))) . "&cms_module_class=" . rawurlencode($this->class_name) . "',
        							handleAs : 'text',
        							load : function(data) {
        								var condition_form = new dojox.layout.ContentPane({
        									content : data
        								}, form_content);
        								dojo.place(form_content, 'cms_module_common_module_filters_form');
        							}
        						});
						 	}
                            select.selectedIndex = 0;
					 	}
			 		</script>
 				</div>
 			</div>
 			<div class='row' id='cms_module_common_module_filters_form'>";

            if (! empty($this->filters)) {
                $form .= "
                <script>";
                foreach ($this->filters as $filter) {
                    if (! empty($filter['id'])) {
                        $form .= "
                    dojo.xhrPost({
    					url : './ajax.php?module=cms&categ=module&elem=" . $filter['name'] . "&action=get_form&id=" . $filter['id'] . "',
    					postData : 'cms_build_info=" . rawurlencode(serialize(pmb_base64_encode($this->cms_build_env))) . "&cms_module_class=" . rawurlencode($this->class_name) . "',
    					handelAs : 'text/html',
    					load : function(data) {
							var form_content = dojo.create('div');
							var filter_form = new dojox.layout.ContentPane({
								content : data
							}, form_content);
							dojo.place(form_content, 'cms_module_common_module_filters_form');
						}
    				});";
                    }
                }
                $form .= "
                </script>";
            }

            $form .= "
            </div>
 			<div class='row'>&nbsp;</div>";
        }

        return $form;
    }

    public function get_exported_datas()
    {
        $infos = parent::get_exported_datas();
        $infos['name'] = $this->name;
        $infos['fixed'] = $this->fixed;
        $infos['css_class'] = $this->css_class;
        $infos['modcache'] = $this->modcache;
        $infos['classement'] = $this->classement;
        $datasource = new $this->datasource['name']($this->datasource['id']);
        $infos['datasource'] = $datasource->get_exported_datas();
        $view = new $this->view['name']($this->view['id']);
        $infos['view'] = $view->get_exported_datas();
        $infos['conditions'] = array();
        if (is_countable($this->conditions)) {
            for ($i = 0; $i < count($this->conditions); $i ++) {
                $condition = new $this->conditions[$i]['name']($this->conditions[$i]['id']);
                $infos['conditions'][] = $condition->get_exported_datas();
            }
        }
        return $infos;
    }

    public function get_extension_form($type, $type_elem, $num_elem)
    {
        $query = "select extension_datas_datas from cms_modules_extensions_datas where extension_datas_module = '" . $this->class_name . "' and extension_datas_type = '" . $type . "' and extension_datas_type_element = '" . $type_elem . "' and extension_datas_num_element = '" . ($num_elem * 1) . "'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $this->extension_datas = unserialize(pmb_mysql_result($result, 0, 0));
        }
        // on var chercher les données pour l'élément courant
        return $this->get_hash_form();
    }

    protected function save_extension_form($type, $type_elem, $num_elem)
    {
        // on supprime ceux d'avant...
        $query = "delete from cms_modules_extensions_datas where extension_datas_module = '" . $this->class_name . "' and extension_datas_type = '" . $type . "' and extension_datas_type_element = '" . $type_elem . "' and extension_datas_num_element = '" . ($num_elem * 1) . "'";
        pmb_mysql_query($query);

        $query = "insert into cms_modules_extensions_datas set
			extension_datas_module = '" . $this->class_name . "',
			extension_datas_type_element = '" . $type_elem . "',
			extension_datas_num_element = '" . $num_elem . "',
			extension_datas_type = '" . $type . "',
			extension_datas_datas = '" . addslashes(serialize($this->extension_datas)) . "'";
        pmb_mysql_query($query);
    }

    // on parcours les conditions pour savoir si rien n'empeche la mise en cache du cadre!
    public function check_for_cache()
    {
        if (is_countable($this->conditions)) {
            for ($i = 0; $i < count($this->conditions); $i ++) {
                $condition = $this->conditions[$i]['name'];
                if (! $condition::use_cache()) {
                    return false;
                } else {
                    continue;
                }
            }
        }
        return true;
    }

    public function get_selected_datasource()
    {
        if (empty($this->selected_datasource)) {
            if (! empty($this->datasource['id'])) {
                $this->selected_datasource = new $this->datasource['name']($this->datasource['id']);
                foreach ($this->filters as $filter) {
                    if (! isset($filter['id'])) {
                        $filter['id'] = 0;
                    }
                    if (! empty($filter['id'])) {
                        $filter_instance = new $filter['name']($filter['id']);
                        $this->selected_datasource->set_filter($filter_instance);
                    }
                }
            } else {
                return false;
            }
        }
        return $this->selected_datasource;
    }

    public function get_selected_view()
    {
        if (! $this->selected_view) {
            if ($this->view['id'] != 0 && class_exists($this->view['name'])) {
                $this->selected_view = new $this->view['name']($this->view['id']);
                $this->selected_view->set_cadre_name($this->name);
            } else {
                return false;
            }
        }
        return $this->selected_view;
    }

    public function get_human_description()
    {
        $datasource = new $this->datasource['name']($this->datasource['id']);
        $view = new $this->view['name']($this->view['id']);

        $description = "<span class = 'cms_module_common_module_human_description'><strong>" . $this->name . "</strong> ( " . $this->id . " ) (" . $this->classement . ")</span><br/>";
        foreach ($this->get_modcache_choices() as $modcache) {
            if ($modcache['value'] == $this->modcache) {
                $description .= "<span class = 'cms_module_common_modcache_human_description'><em>- " . $this->format_text($this->msg['cms_module_common_module_modcache']) . "</em> : <span class = 'cms_module_common_modcache_name_human_description'>" . $modcache['name'] . "</span></span><br/>";
            }
        }
        if ($this->css_class) {
            $description .= "<span class = 'cms_module_common_css_class_human_description'><em>- " . $this->format_text($this->msg['cms_module_common_module_css_class']) . "</em> : <span class = 'cms_module_common_css_class_name_human_description'>" . $this->css_class . "</span></span><br/>";
        }
        if (is_countable($this->conditions) && count($this->conditions)) {
            $description .= "<span class = 'cms_module_common_condition_human_description'><em>- " . $this->format_text($this->msg['cms_manage_module_conditions']) . "</em> : ";

            for ($i = 0; $i < count($this->conditions); $i ++) {
                $condition = new $this->conditions[$i]['name']($this->conditions[$i]['id']);
                if ($i == 0) {
                    $description .= $condition->get_human_description($this->format_text($this->msg[$condition->class_name]));
                } else {
                    $description .= "; " . $condition->get_human_description($this->format_text($this->msg[$condition->class_name]));
                }
            }

            $description .= "</span><br/>";
        }
        $description .= "<span class = 'cms_module_common_datasource_human_description'><em>- " . $this->format_text($this->msg['cms_manage_module_datasources']) . "</em> : " . $datasource->get_human_description(isset($this->msg[$datasource->class_name]) ? $this->format_text($this->msg[$datasource->class_name]) : '') . "</span><br/>
						<span class = 'cms_module_common_view_human_description'><em>- " . $this->format_text($this->msg['cms_manage_module_views']) . "</em> : " . $view->get_human_description(isset($this->msg[$view->class_name]) ? $this->format_text($this->msg[$view->class_name]) : '') . "</span>";
        return $description;
    }

    public function clean_duplication()
    {
        $this->id = 0;
        $this->hash = '';
        $this->get_hash();
        $this->conditions = [];
        $this->filters = [];
        $this->datasource = [];
        $this->view = [];
    }

    public static function get_name_from_id($id)
    {
        $id = intval($id);
        $query = "select cadre_name from cms_cadres where id_cadre = " . $id;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            return pmb_mysql_result($result, 0, 'cadre_name');
        }
        return '';
    }

    public function get_managed_datas()
    {
        return $this->managed_datas;
    }

    public function confirm_delete()
    {
        return true;
    }
}