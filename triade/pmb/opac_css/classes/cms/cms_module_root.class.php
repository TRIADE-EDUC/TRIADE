<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_root.class.php,v 1.72 2024/04/26 15:58:45 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

// définition de constantes pour le débug
if (! defined("CMS_DEBUG_MODE_OFF")) {
    define("CMS_DEBUG_MODE_OFF", 0);
}
if (! defined("CMS_DEBUG_MODE_PHP")) {
    define("CMS_DEBUG_MODE_PHP", 1);
}
if (! defined("CMS_DEBUG_MODE_CONSOLE")) {
    define("CMS_DEBUG_MODE_CONSOLE", 2);
}
if (! defined("CMS_DEBUG_MODE_FILE")) {
    define("CMS_DEBUG_MODE_FILE", 3);
}
if (! defined("CMS_DEBUG_MODE_DUMP")) {
    define("CMS_DEBUG_MODE_DUMP", 4);
}
// l'autoload a quelques suprises
global $cms_debug_mode;
$cms_debug_mode = CMS_DEBUG_MODE_CONSOLE;

class cms_module_root
{

    protected $parameters;

    protected $msg = array();

    protected $class_name;

    protected $hash;

    public $id = 0;

    protected $cms_build_env;

    protected $module_folder;

    protected $managed_datas = array();

    protected $module_class_name = '';

    protected $manifest;

    protected $informations;

    protected $cadre_parent;

    protected $selectors;

    public function __construct()
    {
        // on va chercher les messages...
        $this->class_name = get_class($this);
        $this->load_msg();

        $this->fetch_datas_cache();
        if (! $this->hash) {
            $this->get_hash_from_form();
        } else {
            $var_name = $this->class_name . "_hash";
            global ${$var_name};
            if (isset(${$var_name}) && is_array(${$var_name})) {
                array_shift(${$var_name});
            }
        }
    }

    protected function fetch_datas_cache()
    {
        if ($this->id && ($tmp = cms_cache::get_at_cms_cache($this))) {
            $this->restore($tmp);
        } else {
            $this->fetch_datas();
            cms_cache::set_at_cms_cache($this);
        }
    }

    protected function fetch_datas()
    {
        // A surcharger pour avoir les datas
    }

    protected function restore($cms_object)
    {
        if (is_object($cms_object)) {
            foreach (get_object_vars($cms_object) as $propertieName => $propertieValue) {
                $this->{$propertieName} = $propertieValue;
            }
        }
    }

    public function serialize()
    {
        return serialize($this->parameters);
    }

    public function unserialize($parameters)
    {
        $this->parameters = unserialize($parameters);
    }

    protected function load_msg()
    {
        if (! count($this->msg)) {
            global $lang;
            global $base_path;

            // on regarde si on doit intégrer des fichiers de messages de parents
            $parents = $this->get_parent_classes();
            for ($i = count($parents) - 1; $i >= 0; $i --) {
                if ($parents[$i] != "cms_module_root") {
                    $parent = str_replace("cms_module_", "", $parents[$i]);
                    if (strpos($parent, "_") !== false) {
                        $module_rep = substr($parent, 0, strpos($parent, "_"));
                    } else {
                        $module_rep = $parent;
                    }
                    // on regarde la langue par défaut du module
                    $default_language = $this->get_default_language($module_rep);
                    // si elle est différente de celle de l'interface, on l'intègre
                    // la langue par défaut donne l'assurance d'avoir tous les messages...
                    if ($default_language != $lang) {
                        $file = $base_path . "/cms/modules/" . $module_rep . "/messages/" . $default_language . "/" . $parents[$i] . ".xml";
                        $this->load_msg_file($file);
                    }
                    // on commence par charger les messages de la langue par défaut du module...
                    $file = $base_path . "/cms/modules/" . $module_rep . "/messages/" . $lang . "/" . $parents[$i] . ".xml";
                    $this->load_msg_file($file);
                } else {
                    $file = $base_path . "/cms/modules/common/messages/" . $lang . "/cms_module_root.xml";
                    $this->load_msg_file($file);
                }
            }
            $var = str_replace("cms_module_", "", $this->class_name);
            if (strpos($var, "_") !== false) {
                $module_rep = substr($var, 0, strpos($var, "_"));
            } else {
                $module_rep = $var;
            }
            // on regarde la langue par défaut du module
            $default_language = $this->get_default_language($module_rep);
            // si elle est différente de celle de l'interface, on l'intègre
            // la langue par défaut donne l'assurance d'avoir tous les messages...
            if ($default_language != $lang) {
                $file = $base_path . "/cms/modules/" . $module_rep . "/messages/" . $default_language . "/" . $this->class_name . ".xml";
                $this->load_msg_file($file);
            }
            $file = $base_path . "/cms/modules/" . $module_rep . "/messages/" . $lang . "/" . $this->class_name . ".xml";
            $this->load_msg_file($file);
        }
    }

    protected function get_parent_classes()
    {
        $parents = array();
        $parent = get_parent_class($this->class_name);
        if ($parent) {
            $parents[] = $parent;
            while ($parent = get_parent_class($parent)) {
                $parents[] = $parent;
            }
        }
        return $parents;
    }

    protected function get_recurse_classes_parent($name, $parents = array())
    {
        $parent = get_parent_class($name);
        $parents[] = $parent;
        if (get_parent_class($parent) != "") {
            $parents = $this->get_recurse_classes_parent($parent, $parents);
        }
        return $parents;
    }

    protected function load_msg_file($file)
    {
        global $cache_msg_file, $base_path;

        if (! $cache_msg_file || ! is_array($cache_msg_file)) {
            $cache_msg_file = array();
        }

        if (isset($cache_msg_file[$file])) {

            $this->msg = $cache_msg_file[$file];
        } elseif (file_exists($file)) {

            $messages = new XMLlist($file);
            $messages->analyser();
            if (is_array($messages->table)) {
                $this->msg = array_merge($this->msg, $messages->table);
            }

            // Gestion des substs
            $tokens = explode('/', $file);
            $file_name = array_pop($tokens);
            $file_lang = array_pop($tokens);
            $subst_file = $base_path . "/cms/modules/common/substs/" . $file_lang . "/" . $file_name;
            if (file_exists($subst_file)) {
                $subst_messages = new XMLlist($subst_file);
                $subst_messages->analyser();
                if (is_array($subst_messages->table)) {
                    $this->msg = array_merge($this->msg, $subst_messages->table);
                }
            }

            $cache_msg_file[$file] = $this->msg;
            return true;
        } else {
            return false;
        }
    }

    protected function format_text($text)
    {
        if (is_numeric($text)) {
            return $text;
        }

        if (! is_string($text)) {
        	// Array, Object etc.
            return "";
        }

        global $charset;
        return htmlentities($text, ENT_QUOTES, $charset);
    }

    public function get_hash()
    {
        if (! $this->hash) {
            $this->hash = $this->generate_hash($this->class_name);
            $query = "insert into cms_hash set hash = '" . $this->hash . "'";
            pmb_mysql_query($query);
        }
        return $this->hash;
    }

    public function delete_hash()
    {
        $query = "delete from cms_hash where hash = '" . $this->hash . "'";
        $result = pmb_mysql_query($query);
        if ($result) {
            $this->hash = "";
        }
    }

    protected function generate_hash($phrase = "")
    {
        $hash = md5($phrase . time());
        $query = "select hash from cms_hash where hash = '" . $hash . "'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $hash = $this->generate_hash($hash);
        }
        return $hash;
    }

    protected static function charset_normalize($elem, $input_charset)
    {
        global $charset;
        if (is_array($elem)) {
            foreach ($elem as $key => $value) {
                $elem[$key] = self::charset_normalize($value, $input_charset);
            }
        } else {
            // PMB dans un autre charset, on converti la chaine...
            $elem = self::clean_cp1252($elem, $input_charset);
            if ($charset != $input_charset) {
                $elem = iconv($input_charset, $charset, $elem);
            }
        }
        return $elem;
    }

    public static function addslashes($elem)
    {
        if (is_array($elem) || is_object($elem)) {
            foreach ($elem as $key => $value) {
                $elem[$key] = self::addslashes($value);
            }
        } else {
            $elem = addslashes($elem);
        }
        return $elem;
    }

    public static function stripslashes($elem)
    {
        if (is_array($elem) || is_object($elem)) {
            foreach ($elem as $key => $value) {
                $elem[$key] = self::stripslashes($value);
            }
        } else {
            $elem = stripslashes($elem);
        }
        return $elem;
    }

    public static function debug($elem, $mode = "")
    {
        global $cms_debug_mode;
        if (! $mode) {
            global $cms_debug_mode;
            $mode = $cms_debug_mode;
        }
        switch ($mode) {
            case CMS_DEBUG_MODE_DUMP:
                var_dump($elem);
                break;
            // impression à l'écran
            case CMS_DEBUG_MODE_PHP:
                highlight_string(print_r($elem, true));
                break;
            // renvoi dans la console
            case CMS_DEBUG_MODE_CONSOLE:
                print "
				<!-- Debug/Verbose mode -->
				<script>
					if(typeof console != 'undefined') {
						console.log(" . encoding_normalize::json_encode($elem) . ");
					}
				</script>";
                break;
            case CMS_DEBUG_MODE_FILE:
                global $base_path;
                if (is_string($elem)) {
                    file_put_contents($base_path . "/temp/debug_portail.txt", date("r") . " : " . $elem . "\n", FILE_APPEND);
                } else {
                    file_put_contents($base_path . "/temp/debug_portail.txt", date("r") . " : " . print_r($elem, true) . "\n", FILE_APPEND);
                }
                break;
            case CMS_DEBUG_MODE_OFF:
            default:
                // rien à faire...
                break;
        }
    }

    public function get_default_language($module)
    {
        global $base_path;
        // si c'est un module, on a déjà lu le manifest...
        if (isset($this->manifest)) {
            $default_language = $this->informations['default_language'];
        } else {
            // sinon, le cas des common est à part, on sait que c'est en français...
            if ($module == "common") {
                $default_language = "fr_FR";
            } else {
                // sinon, on va chercher l'info dans le manifest du module...
                $default_language = self::get_module_default_language($base_path . "/cms/modules/" . $module . "/manifest.xml");
            }
        }
        return $default_language;
    }

    public static function get_module_default_language($xml)
    {
        if (! is_object($xml)) {
            $dom = new domDocument();
            $dom->load($xml);
            $xml = $dom;
        }
        $default_language = $xml->getElementsByTagName("default_language")->item(0)->nodeValue;
        return $default_language;
    }

    public function set_cms_build_env($env)
    {
        $this->cms_build_env = $env;
    }

    public function get_module_folder()
    {
        if (! $this->module_folder) {
            global $base_path;
            $var = str_replace("cms_module_", "", $this->class_name);
            if (strpos($var, "_") !== false) {
                $module_rep = substr($var, 0, strpos($var, "_"));
            } else {
                $module_rep = $var;
            }
            $this->module_folder = $base_path . "/cms/modules/" . $module_rep . "/";
        }
        return $this->module_folder;
    }

    public function convert_utf8($str)
    {
        global $charset;
        if ($charset != "utf-8") {
            return iconv($charset, "utf-8", $str);
        }
    }

    public function get_headers($datas = array())
    {
        return array();
    }

    public function get_ajax_link($args = array(), $action = 'ajax')
    {
        global $base_path;
        $request = "";
        foreach ($args as $key => $val) {
            $request .= "&" . $key . "=" . $val;
        }
        return $base_path . "/ajax.php?module=cms&categ=module&elem=" . $this->class_name . "&id=" . $this->id . "&action=" . $action . $request;
    }

    public function execute_ajax()
    {
        global $do;

        $response = array();
        switch ($do) {
            case "get_infopages":
                $query = "select id_infopage,title_infopage from infopages where valid_infopage = 1 order by title_infopage";
                $result = pmb_mysql_query($query);
                $infopages = array();
                if (pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $infopages[$row->id_infopage] = $row->title_infopage;
                    }
                }
                $response['content'] = json_encode(encoding_normalize::utf8_normalize($infopages));
                $response['content-type'] = "application/json";
                break;
            case "get_pages":
                $query = "select id_page,page_name from cms_pages order by page_name asc";
                $result = pmb_mysql_query($query);
                $pages = array();
                $pages[0] = $this->msg["cms_module_menu_menu_entry_page_choice"];
                if (pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $pages[$row->id_page] = $row->page_name;
                    }
                }
                $response['content'] = json_encode(encoding_normalize::utf8_normalize($pages));
                $response['content-type'] = "application/json";
                break;
            case 'get_page_vars':
            	global $page;
            	$page = intval($page);
                $query = "select var_name,var_comment from cms_vars where var_num_page = " . $page;
                $result = pmb_mysql_query($query);
                $vars = array();
                if (pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $vars[] = array(
                            'name' => $row->var_name,
                            'comment' => $row->var_comment
                        );
                    }
                }
                $response['content'] = json_encode(encoding_normalize::utf8_normalize($vars));
                $response['content-type'] = "application/json";
                break;

            default:
                $response = array(
                    'content' => "",
                    'content-type' => "text/html"
                );
                break;
        }
        return $response;
    }

    public function get_module_dom_id()
    {
        if (method_exists($this, "get_dom_id")) {
            return $this->get_dom_id();
        } else {
            $query = "select cadre_object from cms_cadres where id_cadre = '" . $this->cadre_parent . "'";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $obj = pmb_mysql_result($result, 0, 0);
                return $obj . "_" . $this->cadre_parent;
            }
        }
    }

    protected function fetch_managed_datas($type)
    {
        switch ($type) {
            case "conditions":
            case "datasources":
            case "views":
                if ($this->module_class_name) {
                    $query = "select managed_module_box from cms_managed_modules where managed_module_name = '" . $this->module_class_name . "'";
                    $result = pmb_mysql_query($query);
                    if (pmb_mysql_num_rows($result)) {
                        $datas = unserialize(pmb_mysql_result($result, 0, 0));
                        $this->managed_datas = $datas[$type][$this->class_name];
                    }
                }
                break;
            default:
                $query = "select managed_module_box from cms_managed_modules where managed_module_name = '" . $this->class_name . "'";
                $result = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($result)) {
                    $this->managed_datas = unserialize(pmb_mysql_result($result, 0, 0));
                }
                break;
        }
    }

    protected function get_managed_form_start($pvars = "")
    {
        global $base_path;
        $vars = "";
        $params = array(
            'categ' => "manage"
        );
        if ($this->module_class_name) {
            $params['sub'] = str_replace("cms_module_", "", $this->module_class_name);
            $var = explode("_", $this->class_name);
            $params['quoi'] = $var[3] . "s";
            $params['elem'] = $this->class_name;
        } else {
            $params['sub'] = str_replace("cms_module_", "", $this->class_name);
            $params['quoi'] = "module";
        }
        $params['action'] = "save_form";
        foreach ($params as $key => $val) {
            if ($vars != "")
                $vars .= "&";
            $vars .= $key . "=" . $val;
        }
        if ($pvars) {
            foreach ($pvars as $key => $val) {
                if ($vars != "")
                    $vars .= "&";
                $vars .= $key . "=" . $val;
            }
        }
        return "
		<form name='" . $this->class_name . "_manage_form' method='POST' action='" . $base_path . "/cms.php?" . $vars . "'>
			<div class='form-contenu'>";
    }

    protected function get_managed_form_end()
    {
        return "
			</div>
			<div class='row'>
				<hr/>
				<input type='submit' class='bouton' value='" . $this->format_text($this->msg['cms_manage_module_save']) . "'/>
			</div>
		</form>";
    }

    // offrons un peu de bonheur...
    // quelques méthodes génériques pour construire du lien...
    public function get_constructor_link_form($type, $name = "")
    {
        if (! $name)
            $name = $this->class_name . "_link_" . $type;

        $form = "
				<select id='" . $name . "' name='" . $name . "' onChange='" . $this->class_name . "_load_" . $type . "_page_env();'>
					<option value='0'>" . $this->format_text($this->msg['cms_module_common_link_constructor_page']) . "</option>";

        $query = "select id_page,page_name from cms_pages order by 2";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {

            while ($row = pmb_mysql_fetch_object($result)) {
                $form .= "
					<option value='" . $row->id_page . "' " . (isset($this->parameters['links'][$type]['page']) && $row->id_page == $this->parameters['links'][$type]['page'] ? "selected='selected'" : "") . ">" . $this->format_text($row->page_name) . "</option>";
            }
        }
        $form .= "
				</select>
				<script>
					function " . $this->class_name . "_load_" . $type . "_page_env(){
						dijit.byId('" . $name . "_env').href = './ajax.php?module=cms&elem=" . $this->class_name . "&categ=module&action=get_env&name=" . $this->class_name . "_page_" . $type . "_var" . "&pageid='+dojo.byId('" . $name . "').value;
						dijit.byId('" . $name . "_env').refresh();
					}
				</script>";
        $href = "";
        if (isset($this->parameters['links'][$type]['page']) && $this->parameters['links'][$type]['page']) {
            $href = "./ajax.php?module=cms&elem=" . $this->class_name . "&categ=module&action=get_env&name=" . $this->class_name . "_page_" . $type . "_var" . "&pageid=" . $this->parameters['links'][$type]['page'] . "&var=" . $this->parameters['links'][$type]['var'];
        }
        $form .= "
				<div id='" . $name . "_env' dojoType='dojox.layout.ContentPane'" . ($href != "" ? " preload='true' href='" . $href . "'" : "") . "></div>";
        return $form;
    }

    public function get_page_env_select($pageid, $name, $var = "")
    {
        $pageid = intval($pageid);
        $page = new cms_page($pageid);
        $form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='" . $name . "'>" . $this->format_text($this->msg['cms_module_common_link_constructor_page_var']) . "</label>
			</div>
			<div class='colonne-suite'>
				<select name='" . $name . "' id='" . $name . "'>";
        foreach ($page->vars as $page_var) {
            $form .= "
					<option value='" . $this->format_text($page_var['name']) . "' " . ($page_var['name'] == $var ? "selected='selected'" : "") . ">" . $this->format_text(($page_var['comment'] != "" ? $page_var['comment'] : $page_var['name'])) . "</option>";
        }
        $form .= "
				</select>
			</div>
		</div>";
        return $form;
    }

    protected function save_constructor_link_form($type)
    {
        $page = $this->class_name . "_link_" . $type;
        $var = $this->class_name . "_page_" . $type . "_var";

        global ${$page};
        global ${$var};
        $this->parameters['links'][$type] = array(
            'page' => intval(${$page}),
            'var' => ${$var}
        );
    }

    protected function get_constructed_link($type, $value, $is_bulletin = false)
    {
        $link = "";
        $page_id = (! empty($this->parameters['links'][$type]['page']) ? $this->parameters['links'][$type]['page'] : 0);
        $var = (! empty($this->parameters['links'][$type]['var']) ? $this->parameters['links'][$type]['var'] : "");
        switch ($type) {
            case "notice":
                if ($page_id) {
                    $link = "./index.php?lvl=cmspage&pageid=$page_id&$var=$value";
                } else {
                    if (! $is_bulletin) {
                        $link = "./index.php?lvl=notice_display&id=$value";
                    } else {
                        $link = "./index.php?lvl=bulletin_display&id=$value";
                    }
                }
                break;
            case "animation":
                $link = "./index.php?lvl=animation_see&id=$value";
                if ($page_id) {
                    $link = "./index.php?lvl=cmspage&pageid=$page_id&$var=$value";
                }
                break;
            case "shelve":
                if ($page_id) {
                    $link = "./index.php?lvl=cmspage&pageid=$page_id&$var=$value";
                } else {
                    $link = "./index.php?lvl=etagere_see&id=$value";
                }
                break;
            case "shelve_to_cart":
                $link = "cart_info.php?lvl=etagere_see&id=$value";
                break;
            case "article":
            case "section":
            case "animationslist":
            default:
                $link = "./index.php?lvl=cmspage&pageid=$page_id&$var=$value";
                break;
        }
        return $link;
    }

    protected static function obj2array($obj)
    {
        $array = array();
        if (is_object($obj)) {
            foreach ($obj as $key => $value) {
                if (is_object($value)) {
                    $value = cms_module_root::obj2array($value);
                }
                $array[$key] = $value;
            }
        } else {
            $array = $obj;
        }
        return $array;
    }

    protected function get_form_value_name($name)
    {
        // calcule le hash si pas encore fait...
        return $this->get_hash() . "_" . $name;
    }

    protected function get_value_from_form($name)
    {
        $var_name = $this->get_form_value_name($name);
        global ${$var_name};
        return ${$var_name};
    }

    protected function get_hash_form()
    {
        return "
			<input type='hidden' name='" . $this->class_name . "_hash[]' value='" . $this->get_hash() . "'/>";
    }

    public function get_hash_from_form()
    {
        if (! $this->hash) {
            $var_name = $this->class_name . "_hash";
            global ${$var_name};
            if (is_array(${$var_name})) {
                $this->hash = array_shift(${$var_name});
            }
        }
    }

    protected function clean_hash_table()
    {
        // on commence par créer une table tempo de tous les hash utilisés !
        $query = "create temporary table used_hash (hash varchar(255))";
        pmb_mysql_query($query);
        // on ajoute les hash des pages...
        $query = "insert into used_hash select page_hash as hash from cms_pages";
        pmb_mysql_query($query);
        // on ajoute les hash des modules...
        $query = "insert into used_hash select cadre_hash as hash from cms_cadres";
        pmb_mysql_query($query);
        // on ajoute les hash des éléments des modules...
        $query = "insert into used_hash select cadre_content_hash as hash from cms_cadre_content";
        pmb_mysql_query($query);
        // on nettoie !
        $query = "delete cms_hash from cms_hash left join used_hash on cms_hash.hash = used_hash.hash where cms_hash.hash is null";
        pmb_mysql_query($query);
    }

    protected function prefix_var_tree($tree, $prefix)
    {
        for ($i = 0; $i < count($tree); $i ++) {
            $tree[$i]['var'] = $prefix . "." . $tree[$i]['var'];
            if (isset($tree[$i]['children']) && $tree[$i]['children']) {
                $tree[$i]['children'] = $this->prefix_var_tree($tree[$i]['children'], $prefix);
            }
        }
        return $tree;
    }

    protected static function clean_cp1252($str, $charset)
    {
        $cp1252_map = array();
        switch ($charset) {
            case "utf-8":
                $cp1252_map = array(
                    "\xe2\x82\xac" => "EUR", /* EURO SIGN */
				"\xe2\x80\x9a" => "\xc2\xab", /* SINGLE LOW-9 QUOTATION MARK */
				"\xc6\x92" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
				"\xe2\x80\9e" => "\xc2\xab", /* DOUBLE LOW-9 QUOTATION MARK */
				"\xe2\x80\xa6" => "...", /* HORIZONTAL ELLIPSIS */
				"\xe2\x80\xa0" => "?", /* DAGGER */
				"\xe2\x80\xa1" => "?", /* DOUBLE DAGGER */
				"\xcb\x86" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
				"\xe2\x80\xb0" => "?", /* PER MILLE SIGN */
				"\xc5\xa0" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
				"\xe2\x80\xb9" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
				"\xc5\x92" => "OE",   /* LATIN CAPITAL LIGATURE OE */
				"\xc5\xbd" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
				"\xe2\x80\x98" => "\x27", /* LEFT SINGLE QUOTATION MARK */
				"\xe2\x80\x99" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
				"\xe2\x80\x9c" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
				"\xe2\x80\x9d" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
				"\xe2\x80\xa2" => "\b7", /* BULLET */
				"\xe2\x80\x93" => "\x20", /* EN DASH */
				"\xe2\x80\x94" => "\x20\x20", /* EM DASH */
				"\xcb\x9c" => "\x7e",   /* SMALL TILDE */
				"\xe2\x84\xa2" => "?", /* TRADE MARK SIGN */
				"\xc5\xa1" => "s",   /* LATIN SMALL LETTER S WITH CARON */
				"\xe2\x80\xba" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
				"\xc5\x93" => "oe",   /* LATIN SMALL LIGATURE OE */
				"\xc5\xbe" => "z",   /* LATIN SMALL LETTER Z WITH CARON */
				"\xc5\xb8" => "Y" /* LATIN CAPITAL LETTER Y WITH DIAERESIS */
                );
                break;
            case "iso8859-1":
            case "iso-8859-1":
                $cp1252_map = array(
                    "\x80" => "EUR", /* EURO SIGN */
				"\x82" => "\xab", /* SINGLE LOW-9 QUOTATION MARK */
				"\x83" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
				"\x84" => "\xab", /* DOUBLE LOW-9 QUOTATION MARK */
				"\x85" => "...", /* HORIZONTAL ELLIPSIS */
				"\x86" => "?", /* DAGGER */
				"\x87" => "?", /* DOUBLE DAGGER */
				"\x88" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
				"\x89" => "?", /* PER MILLE SIGN */
				"\x8a" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
				"\x8b" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
				"\x8c" => "OE",   /* LATIN CAPITAL LIGATURE OE */
				"\x8e" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
				"\x91" => "\x27", /* LEFT SINGLE QUOTATION MARK */
				"\x92" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
				"\x93" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
				"\x94" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
				"\x95" => "\b7", /* BULLET */
				"\x96" => "\x20", /* EN DASH */
				"\x97" => "\x20\x20", /* EM DASH */
				"\x98" => "\x7e",   /* SMALL TILDE */
				"\x99" => "?", /* TRADE MARK SIGN */
				"\x9a" => "S",   /* LATIN SMALL LETTER S WITH CARON */
				"\x9b" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
				"\x9c" => "oe",   /* LATIN SMALL LIGATURE OE */
				"\x9e" => "Z",   /* LATIN SMALL LETTER Z WITH CARON */
				"\x9f" => "Y" /* LATIN CAPITAL LETTER Y WITH DIAERESIS */
                );
                break;
        }
        return strtr($str, $cp1252_map);
    }

    public static function get_platform()
    {
        $user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');

        if ((strpos($user_agent, "iPhone") !== FALSE) || (strpos($user_agent, "iPad") !== FALSE)) {
            $os = "iOS";
        } elseif (strpos($user_agent, "Windows Phone") !== FALSE) {
            $os = "Windows Phone";
        } elseif (strpos($user_agent, "Windows") !== FALSE) {
            $os = "Windows";
        } elseif ((strpos($user_agent, "Mac") !== FALSE) || (strpos($user_agent, "PPC") !== FALSE)) {
            $os = "Mac";
        } elseif (strpos($user_agent, "Android") !== FALSE) {
            $os = "Android";
        } elseif (strpos($user_agent, "Linux") !== FALSE) {
            $os = "Linux";
        } elseif (strpos($user_agent, "BlackBerry") !== FALSE) {
            $os = "BlackBerry";
        } elseif (strpos($user_agent, "FreeBSD") !== FALSE) {
            $os = "FreeBSD";
        } elseif (strpos($user_agent, "SunOS") !== FALSE) {
            $os = "SunOS";
        } elseif (strpos($user_agent, "IRIX") !== FALSE) {
            $os = "IRIX";
        } elseif (strpos($user_agent, "BeOS") !== FALSE) {
            $os = "BeOS";
        } elseif (strpos($user_agent, "OS/2") !== FALSE) {
            $os = "OS/2";
        } elseif (strpos($user_agent, "AIX") !== FALSE) {
            $os = "AIX";
        } else {
            $os = "Autre";
        }

        return $os;
    }

    public static function get_browser()
    {
        $user_agent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');

        if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) {
            $browser = 'Opera';
        } elseif (strpos($user_agent, 'Edge')) {
            $browser = 'Edge';
        } elseif (strpos($user_agent, 'Chrome')) {
            $browser = 'Chrome';
        } elseif (strpos($user_agent, 'Safari')) {
            $browser = 'Safari';
        } elseif (strpos($user_agent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) {
            $browser = 'Internet Explorer';
        } elseif (strpos($user_agent, 'SamsungBrowser')) {
            $browser = 'Samsung Browser';
        } else {
            $browser = 'Other';
        }

        return $browser;
    }

    protected function get_exported_datas()
    {
        $infos = array(
            "id" => $this->id,
            "class" => $this->class_name,
            "hash" => $this->hash,
            "managed_datas" => $this->managed_datas,
            "parameters" => $this->parameters,
            "module_folder" => $this->module_folder
        );
        return $infos;
    }

    public static function int_caster(&$item)
    {
        return intval($item);
    }

    public function get_id()
    {
        return $this->id;
    }

    public function clean_duplication()
    {
        $this->id = 0;
        $this->hash = '';
        $this->get_hash();
    }

    public function array_int_caster($tab)
    {
        $values = [];
        for ($i = 0; $i < count($tab); $i ++) {
            if (is_numeric($tab[$i])) {
                $values[] = intval($tab[$i]);
            }
        }
        return $values;
    }

    protected function make_tmp_file(string $content = '', string $extension = '.css', bool $with_time = false)
    {
        if ('' === $content) {
            return '';
        }
        if (('' !== $extension) && ('.' != substr($extension, 0, 1))) {
            $extension = '.' . $extension;
        }

        global $base_path;
        $tmp_dir = $base_path . "/temp/";
        $tmp_file = $tmp_dir . $this->get_module_dom_id() . $extension;

        if (! is_readable($tmp_file) || file_get_contents($tmp_file) != $content) {

            if (false === file_put_contents($tmp_file, $content)) {
                return '';
            }
        }

        if (true == $with_time) {
            $time = @filemtime($tmp_file);
            $tmp_file .= "?$time";
        }
        return $tmp_file;
    }

    public function get_parameters()
    {
        return $this->parameters;
    }
}
