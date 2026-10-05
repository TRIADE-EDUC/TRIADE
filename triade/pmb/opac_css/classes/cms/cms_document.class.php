<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_document.class.php,v 1.15.2.1 2024/08/22 08:59:32 rtigero Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($include_path . "/explnum.inc.php");
require_once ($class_path . "/cms/cms_collections.class.php");
create_tableau_mimetype();

class cms_document
{

    public $id = 0;

    public $title = "";

    public $description = "";

    public $filename = "";

    public $mimetype = "";

    public $filesize = "";

    public $vignette = "";

    public $url = "";

    public $path = "";

    public $create_date = "";

    public $num_storage = 0;

    public $type_object = "";

    public $num_object = 0;

    protected $human_size = 0;

    protected $storage;

    protected $data;

    protected $img_infos;

    public function __construct($id = 0)
    {
        $this->id = intval($id);
        $this->fetch_datas_cache();
    }

    protected function fetch_datas_cache()
    {
        if ($tmp = cms_cache::get_at_cms_cache($this)) {
            $this->restore($tmp);
        } else {
            $this->fetch_datas();
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

    protected function fetch_datas()
    {
        if ($this->id) {
            $query = "select document_title,document_description,document_filename,document_mimetype,document_filesize,document_vignette,document_url,document_path,document_create_date,document_num_storage,document_type_object,document_num_object from cms_documents where id_document = '" . $this->id . "'";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_object($result);
                $this->title = $row->document_title;
                $this->description = $row->document_description;
                $this->filename = $row->document_filename;
                $this->mimetype = $row->document_mimetype;
                $this->filesize = $row->document_filesize;
                $this->vignette = $row->document_vignette;
                $this->url = $row->document_url;
                $this->path = $row->document_path;
                $this->create_date = $row->document_create_date;
                $this->num_storage = $row->document_num_storage;
                $this->type_object = $row->document_type_object;
                $this->num_object = $row->document_num_object;
            }
            if ($this->num_storage) {
                $this->storage = storages::get_storage_class($this->num_storage);
            }
        }
    }

    public function get_item_render($edit_js_function = "openEditDialog")
    {
        global $msg, $charset;
        $item = "
		<div class='document_item' id='document_" . $this->id . "'>
			<div class='document_item_content'>
			<img src='" . $this->get_vignette_url() . "' alt='" . $msg["opac_notice_vignette_alt"] . "'/>
			<br/>
			<p> <a href='#' onclick='" . $edit_js_function . "(" . $this->id . ");return false;' title='" . htmlentities($msg['cms_document_edit_link']) . "'>" . htmlentities(($this->title ? $this->title : $this->filename), ENT_QUOTES, $charset) . "</a><br />
			<span style='font-size:.8em;'>" . htmlentities($this->mimetype, ENT_QUOTES, $charset) . ($this->filesize ? " - (" . $this->get_human_size() . ")" : "") . "</span></p>
			</div>
		</div>";
        return $item;
    }

    public function get_item_form($selected = false)
    {
        global $msg, $charset;
        $item = "
		<div class='document_item" . ($selected ? " document_item_selected" : "") . "' id='document_" . $this->id . "'>
			<div class='document_checkbox'>
				<input name='cms_documents_linked[]' onchange='document_change_background(" . $this->id . ");' type='checkbox'" . ($selected ? "checked='checked'" : "") . " value='" . htmlentities($this->id, ENT_QUOTES, $charset) . "'/>
			</div>
			<div class='document_item_content'>
				<img src='" . $this->get_vignette_url() . "' alt='" . $msg["opac_notice_vignette_alt"] . "'/>
				<br/>
				<p>" . htmlentities(($this->title ? $this->title : $this->filename), ENT_QUOTES, $charset) . "<br />
				<span style='font-size:.8em;'>" . htmlentities($this->mimetype, ENT_QUOTES, $charset) . ($this->filesize ? " - (" . $this->get_human_size() . ")" : "") . "</span></p>
			</div>
		</div>";
        return $item;
    }

    public function get_vignette_url()
    {
        global $opac_url_base;
        return "./ajax.php?module=cms&categ=document&action=thumbnail&id=" . $this->id;
    }

    public function get_document_url($mode = '')
    {
        global $base_path, $opac_url_base, $database;
        if (! empty($mode) && is_object($this->storage)) {
            $this->data = $this->storage->get_content($this->path . $this->filename);
            $this->get_img_infos();

            if(!empty($this->img_infos['type']) && file_exists($base_path . '/temp/cms_document/'.$database.'/'.$mode.'/'.$this->id.'.'.$this->img_infos['type'])){
                return $opac_url_base . 'temp/cms_document/'.$database.'/'.$mode.'/'.$this->id.'.'.$this->img_infos['type'];
            }

            return "./ajax.php?module=cms&categ=document&action=render&id=" . $this->id . "&mode=" . $mode;
        }
        return "./ajax.php?module=cms&categ=document&action=render&id=" . $this->id;
    }

    public function get_human_size()
    {
        $units = array(
            "o",
            "Ko",
            "Mo",
            "Go"
        );
        $i = 0;
        do {
            if (! $this->human_size)
                $this->human_size = $this->filesize;
            $this->human_size = $this->human_size / 1024;
            $i ++;
        } while ($this->human_size >= 1024);
        return round($this->human_size, 1) . " " . $units[$i];
    }

    public function get_form($action = "./ajax.php?module=cms&categ=documents&action=save_form&id=")
    {
        global $msg, $charset;
        $form = "
		<form name='cms_document_form' id='cms_document_form' method='POST' action='" . $action . $this->id . "' style='width:500px;'>
			<div class='form-contenu'>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_title'>" . htmlentities($msg['cms_document_title'], ENT_QUOTES, $charset) . "</label>
					</div>
					<div class='colonne_suite'>
						<input type='text' name='cms_document_title' value='" . htmlentities($this->title, ENT_QUOTES, $charset) . "'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_description'>" . htmlentities($msg['cms_document_description'], ENT_QUOTES, $charset) . "</label>
					</div>
					<div class='colonne_suite'>
						<textarea name='cms_document_description' >" . htmlentities($this->description, ENT_QUOTES, $charset) . "</textarea>
					</div>
				</div>";
        if ($this->url) {
            $form .= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_url'>" . htmlentities($msg['cms_document_url'], ENT_QUOTES, $charset) . "</label>
					</div>
					<div class='colonne_suite'>
						<input type='text' name='cms_document_url' value='" . htmlentities($this->url, ENT_QUOTES, $charset) . "'/>
					</div>
				</div>";
        }
        if ($this->id) {
            $form .= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_vign'>" . htmlentities($msg['cms_document_vign'], ENT_QUOTES, $charset) . "</label>
					</div>
					<div class='colonne_suite'>
						<input type='checkbox' name='cms_document_vign' value='1'/>
					</div>
				</div>";
        }
        $form .= "
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<div class='colonne3'>
						<label>" . htmlentities($msg['cms_document_filename'], ENT_QUOTES, $charset) . "</label>
						<br />
						<label>" . htmlentities($msg['cms_document_mimetype'], ENT_QUOTES, $charset) . "</label>
						<br />
						<label>" . htmlentities($msg['cms_document_filesize'], ENT_QUOTES, $charset) . "</label>
						<br />
						<label>" . htmlentities($msg['cms_document_date'], ENT_QUOTES, $charset) . "</label>
					</div>
					<div class='colonne_suite'>
						<span>" . htmlentities($this->filename, ENT_QUOTES, $charset) . "</span>
						<br />
						<span>" . htmlentities($this->mimetype, ENT_QUOTES, $charset) . "</span>
						<br />
						<span>" . htmlentities($this->get_human_size(), ENT_QUOTES, $charset) . "</span>
						<br />
						<span>" . htmlentities(format_date($this->create_date), ENT_QUOTES, $charset) . "</span>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label>" . htmlentities($msg['cms_document_storage'], ENT_QUOTES, $charset) . "</label>
					</div>
					<div class='colonne_suite'>
						" . $this->storage->get_storage_infos() . "
					</div>
				</div>
				<div class='row'>&nbsp;</div>
				<hr />
				<div class='row'>
					<div class='left'>
						<input type='submit' class='bouton'  value='" . htmlentities($msg['cms_document_save'], ENT_QUOTES, $charset) . "'/>
					</div>
					<div class='right'>
						<input type='button' class='bouton' id='doc_del_button' value='" . htmlentities($msg['cms_document_delete'], ENT_QUOTES, $charset) . "'/>
					</div>
				</div>
				<div class='row'></div>
			</div>
		</form>
		<script>
			require(['dojo/dom-construct'],function(domConstruct){
				var form = dojo.byId('cms_document_form');
				dojo.connect(form, 'onsubmit', function(event){
					dojo.stopEvent(event);
					var xhrArgs = {
						form: dojo.byId('cms_document_form'),
						handleAs: 'text',
						load: function(data){
							domConstruct.place(data,'document_" . $this->id . "','replace');
							dijit.byId('dialog_document').hide();
						}
					};
					var deferred = dojo.xhrPost(xhrArgs);
				});	
				dojo.connect(dojo.byId('doc_del_button'),'onclick',function(event){
					if(confirm('" . addslashes($msg['cms_document_confirm_delete']) . "')){
						var xhrArgs = {
							url : '" . str_replace("action=save_form", "action=delete", $action) . $this->id . "',
							handleAs: 'text',
							load: function(data){
								if(data == 1){
									dojo.byId('document_" . $this->id . "').parentNode.removeChild(dojo.byId('document_" . $this->id . "'));
								}else{
									if(confirm(data)) {
										var forceXhrArgs = {
											url : '".str_replace("action=save_form","action=delete",$action).$this->id."&force_delete=1" . "',
											handleAs: 'text',
											load: function(data){
												if(data == 1){
													dojo.byId('document_".$this->id."').parentNode.removeChild(dojo.byId('document_".$this->id."'));
												}
												dijit.byId('dialog_document').hide();
											}
										};
										dojo.xhrGet(forceXhrArgs);
									}
								}
								dijit.byId('dialog_document').hide();
							}
						};
						dojo.xhrGet(xhrArgs);
					}
				});
			});
		</script>";
        return $form;
    }

    public function save_form()
    {
        global $msg, $charset;
        global $cms_document_title, $cms_document_description, $cms_document_url, $cms_document_vign;

        $this->title = $cms_document_title;
        $this->description = $cms_document_description;
        $this->url = $cms_document_url;

        if ($cms_document_vign) {
            $this->calculate_vignette();
        }

        if ($this->id) {
            $query = "update cms_documents set ";
            $clause = " where id_document = '" . $this->id . "'";
        } else {
            $query = "insert into cms_documents set ";
            $clause = "";
        }

        $query .= "
			document_title = '" . addslashes($this->title) . "',
			document_description = '" . addslashes($this->description) . "',
			document_url = '" . addslashes($this->url) . "'";
        if ($cms_document_vign) {
            $query .= ",
			document_vignette = '" . addslashes($this->vignette) . "'";
        }
        if (pmb_mysql_query($query . $clause)) {
            return $this->get_item_render("openEditDialog");
        }
    }

    public function delete()
    {
        // TODO vérification avant suppression dans le contenu éditorial
        if (! is_object($this->storage)) {
            return $msg['cms_document_delete_physical_error'];
            ;
        }
        // suppression physique
        if ($this->storage->delete($this->path . $this->filename)) {
            // il ne reste plus que la base
            if (pmb_mysql_query("delete from cms_documents where id_document = '" . $this->id . "'")) {
                return true;
            }
        } else {
            return $msg['cms_document_delete_physical_error'];
        }
        return false;
    }

    public function calculate_vignette()
    {
        error_reporting(null);
        global $base_path, $include_path, $class_path;
        $path = $this->get_document_in_tmp();
        if ($path) {
            switch ($this->mimetype) {
                case "application/epub+zip":
                    require_once ($class_path . "/epubData.class.php");
                    $doc = new epubData($path);
                    file_put_contents($path, $doc->getCoverContent());
                default:
                    $this->vignette = construire_vignette($path);
                    break;
            }
            unlink($path);
        }
    }

    public function get_document_in_tmp()
    {
        $this->clean_tmp();
        global $base_path;
        $path = $base_path . "/temp/cms_document_" . $this->id;
        if (file_exists($path)) {
            return $path;
        } else if (is_object($this->storage) && $this->storage->duplicate($this->path . $this->filename, $path)) {
            return $path;
        }
        return false;
    }

    protected function clean_tmp()
    {
        global $base_path;
        $dh = opendir($base_path . "/temp/");
        if (! $dh)
            return;
        $files = array();
        while (($file = readdir($dh)) !== false) {
            if ($file != "." && $file != ".." && substr($file, 0, strlen("cms_document_")) == "cms_document_") {
                $stat = stat($base_path . "/temp/" . $file);
                $files[$file] = array(
                    "mtime" => $stat['mtime']
                );
            }
        }
        closedir($dh);
        $deleteList = array();
        foreach ($files as $file => $stat) {
            // si le dernier accès au fichier est de plus de 3h, on vide...
            if (time() - $stat["mtime"] > (3600 * 3)) {
                if (is_dir($base_path . "/temp/" . $file)) {
                    $this->rrmdir($base_path . "/temp/" . $file);
                } else {
                    unlink($base_path . "/temp/" . $file);
                }
            }
        }
    }

    public function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public function format_datas()
    {
        $collection = new cms_collection($this->num_object);

        $datas = array(
            'id' => $this->id,
            'name' => $this->title,
            'description' => $this->description,
            'filename' => $this->filename,
            'mimetype' => $this->mimetype,
            'filesize' => array(
                'human' => $this->get_human_size(),
                'value' => $this->filesize
            ),
            'url' => $this->get_document_url(),
            'urls' => $this->format_datamode(),
            'create_date' => $this->create_date,
            'thumbnails_url' => $this->get_vignette_url()
        );
        $datas['collection'] = $collection->get_infos();
        return $datas;
    }

    public function format_datamode()
    {
        return array(
            'small_vign' => $this->get_document_url("small_vign"),
            'vign' => $this->get_document_url("vign"),
            'small' => $this->get_document_url("small"),
            'medium' => $this->get_document_url("medium"),
            'big' => $this->get_document_url("big"),
            'large' => $this->get_document_url("large"),
            'custom' => $this->get_document_url("custom_"),
            'exists' => ($this->data ? true : false)
        );
    }

    public static function get_format_data_structure()
    {
        global $msg;
        $format_datas = array();
        $format_datas[] = array(
            'var' => "id",
            'desc' => $msg['cms_document_format_data_id']
        );
        $format_datas[] = array(
            'var' => "name",
            'desc' => $msg['cms_document_format_data_name']
        );
        $format_datas[] = array(
            'var' => "description",
            'desc' => $msg['cms_document_format_data_description']
        );
        $format_datas[] = array(
            'var' => "filename",
            'desc' => $msg['cms_document_format_data_filename']
        );
        $format_datas[] = array(
            'var' => "mimetype",
            'desc' => $msg['cms_document_format_data_mimetype']
        );
        $format_datas[] = array(
            'var' => "filesize",
            'desc' => $msg['cms_document_format_data_filesize'],
            'children' => array(
                array(
                    'var' => "filesize.human",
                    'desc' => $msg['cms_document_format_data_filesize_human']
                ),
                array(
                    'var' => "filesize.value",
                    'desc' => $msg['cms_document_format_data_filesize_value']
                )
            )
        );
        $format_datas[] = array(
            'var' => "url",
            'desc' => $msg['cms_document_format_data_url']
        );
        $format_datas[] = array(
            'var' => "create_date",
            'desc' => $msg['cms_document_format_data_create_date']
        );
        $format_datas[] = array(
            'var' => "thumbnails_url",
            'desc' => $msg['cms_document_format_data_thumbnails_url']
        );
        $format_datas[] = array(
            'var' => "collection",
            'desc' => $msg['cms_document_format_data_collection'],
            'children' => array(
                array(
                    'var' => "collection.id",
                    'desc' => $msg['cms_document_format_data_collection_id']
                ),
                array(
                    'var' => "collection.name",
                    'desc' => $msg['cms_document_format_data_collection_name']
                ),
                array(
                    'var' => "collection.description",
                    'desc' => $msg['cms_document_format_data_collection_description']
                )
            )
        );
        return $format_datas;
    }

    public function render_thumbnail()
    {
        header('Content-Type: image/png');
        if ($this->vignette) {
            print $this->vignette;
        } else {
            global $prefix_url_image;
            if ($prefix_url_image)
                $tmpprefix_url_image = $prefix_url_image;
            else
                $tmpprefix_url_image = "./";
            print file_get_contents($tmpprefix_url_image . "images/mimetype/" . icone_mimetype($this->mimetype, substr($this->filename, strrpos($this->filename, ".") + 1)));
        }
    }

    public function render_doc($mode = "")
    {
        if ($this->num_storage && is_object($this->storage)) {
            $this->data = $this->storage->get_content($this->path . $this->filename);
            if ($this->data) {
                header('Content-Type: ' . $this->mimetype);
                header('Content-Disposition: inline; filename="' . $this->filename . '"');
                if (! empty($mode)) {
                    $this->get_img_infos();
                    print $this->get_resized_document($mode);
                    return;
                }
                // TODO récup le filesize quand on resize l'image
                if ($this->filesize)
                    header("Content-Length: " . $this->filesize);
                print $this->data;
            }
        }
    }

    public function get_num_storage()
    {
        return $this->num_storage;
    }

    protected function get_resized_document($mode)
    {
        global $cms_active_image_cache, $base_path, $database;

        if ($cms_active_image_cache && file_exists($base_path . '/temp/cms_document/' . $database . '/' . $mode . '/' . $this->id . '.' . $this->img_infos['type'])) {
            header('Content-Type: ' . $this->img_infos['mimetype']);
            print file_get_contents($base_path . '/temp/cms_document/' . $database . '/' . $mode . '/' . $this->id . '.' . $this->img_infos['type']);
        }
        if (strpos($mode, 'custom_') !== false) {
            $elems = explode('_', $mode);
            if (! is_numeric($elems[1])) {
                header("HTTP/1.0 404 Not Found");
                return;
            }
            $size = $elems[1] * 1;
            if ($size > 0) {
                $dst_img = $this->resize($size, $size);
            } else {
                $dst_img = $this->resize(500, 500);
            }
        } else {
            switch ($mode) {
                case 'small_vign':
                    $dst_img = $this->resize(16, 16);
                    break;
                case 'vign':
                    $dst_img = $this->resize(100, 100);
                    break;
                case 'small':
                    $dst_img = $this->resize(140, 140);
                    break;
                case 'medium':
                    $dst_img = $this->resize(300, 300);
                    break;
                case 'big':
                    $dst_img = $this->resize(600, 600);
                    break;
                case 'large':
                    $dst_img = $this->resize(0, 0);
                    if ($this->img_infos['type'] == 'png') {
                        // Pour les images non redimensionnées
                        imageSaveAlpha($dst_img, true);
                    }
                    break;
                default:
                    header("HTTP/1.0 404 Not Found");
                    return;
            }
        }
        if ($dst_img) {
            if (function_exists($this->img_infos['render_fct'])) {
                if ($cms_active_image_cache) {
                    $this->init_cache_path($mode);
                    $render_params = array_merge(array(
                        $dst_img,
                        $base_path . '/temp/cms_document/' . $database . '/' . $mode . '/' . $this->id . '.' . $this->img_infos['type']
                    ), $this->img_infos['render_params']);
                    call_user_func_array($this->img_infos['render_fct'], $render_params);
                }
                $render_params = array_merge(array(
                    $dst_img,
                    null
                ), $this->img_infos['render_params']);
                call_user_func_array($this->img_infos['render_fct'], $render_params);
            }
        }
    }

    private function init_cache_path($mode)
    {
        global $base_path, $database;
        if (! file_exists($base_path . "/temp/cms_document")) {
            mkdir($base_path . "/temp/cms_document");
        }
        if (! file_exists($base_path . "/temp/cms_document/" . $database)) {
            mkdir($base_path . "/temp/cms_document/" . $database);
        }
        if (! file_exists($base_path . "/temp/cms_document/" . $database . "/" . $mode)) {
            mkdir($base_path . "/temp/cms_document/" . $database . "/" . $mode);
        }
    }

    protected function resize($size_x = 0, $size_y = 0)
    {
        $src_img = imagecreatefromstring($this->data);
        if (! $src_img) {
            header('Content-Type: image/png');
            print file_get_contents(get_url_icon('vide.png'));
            return;
        }

        if (! $size_x && ! $size_y) {
            return $src_img;
        }

        $maxX = $size_x;
        $maxY = $size_y;

        $rs = $maxX / $maxY;
        $taillex = $this->img_infos['width'];
        $tailley = $this->img_infos['height'];
        if (! $taillex || ! $tailley) {
            header('Content-Type: image/png');
            print file_get_contents(get_url_icon('vide.png'));
            return;
        }
        if (($taillex > $maxX) || ($tailley > $maxY)) {
            $r = $taillex / $tailley;
            if (($r < 1) && ($rs < 1)) {
                // Si x plus petit que y et taille finale portrait
                // Si le format final est plus large en proportion
                if ($rs > $r) {
                    $new_h = $maxY;
                    $new_w = $new_h * $r;
                } else {
                    $new_w = $maxX;
                    $new_h = $new_w / $r;
                }
            } else if (($r < 1) && ($rs >= 1)) {
                // Si x plus petit que y et taille finale paysage
                $new_h = $maxY;
                $new_w = $new_h * $r;
            } else if (($r > 1) && ($rs < 1)) {
                // Si x plus grand que y et taille finale portrait
                $new_w = $maxX;
                $new_h = $new_w / $r;
            } else {
                // Si x plus grand que y et taille finale paysage
                if ($rs < $r) {
                    $new_w = $maxX;
                    $new_h = $new_w / $r;
                } else {
                    $new_h = $maxY;
                    $new_w = $new_h * $r;
                }
            }
        } else {
            $new_h = $tailley;
            $new_w = $taillex;
        }
        $dst_img = imagecreatetruecolor($new_w, $new_h);
        if ($this->img_infos['type'] == 'png') {
            imageSaveAlpha($dst_img, true);
            imageAlphaBlending($dst_img, false);
        }
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_w, $new_h, $this->img_infos['width'], $this->img_infos['height']);

        return $dst_img;
    }

    protected function get_img_infos()
    {
        $img_infos = getimagesizefromstring($this->data);
        if ($img_infos) {
            $this->img_infos['width'] = $img_infos[0];
            $this->img_infos['height'] = $img_infos[1];
            $this->img_infos['mimetype'] = $img_infos['mime'];

            $this->img_infos['render_fct'] = false;
            $this->img_infos['render_params'] = array();

            switch ($this->img_infos['mimetype']) {
                case 'image/png':
                    $this->img_infos['type'] = 'png';
                    $this->img_infos['render_fct'] = 'imagepng';
                    if (defined('PNG_ALL_FILTERS')) {
                        $this->img_infos['render_params'] = array(
                            9,
                            PNG_ALL_FILTERS
                        );
                    } else {
                        $this->img_infos['render_params'] = array(
                            9
                        );
                    }
                    break;
                case 'image/jpeg':
                    $this->img_infos['type'] = 'jpeg';
                    $this->img_infos['render_fct'] = 'imagejpeg';
                    if (strlen($this->data) < 102400) {
                        // Si image < 100ko, on ne réduit pas la qualité, sinon on laisse le réglage par défaut de imagejpeg
                        $this->img_infos['render_params'] = array(
                            100
                        );
                    }
                    break;
                case 'image/gif':
                    $this->img_infos['type'] = 'gif';
                    $this->img_infos['render_fct'] = 'imagegif';
                    break;
            }
        }
    }
}