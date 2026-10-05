<?php

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storage_document.class.php,v 1.10.2.1.2.1 2025/03/21 10:28:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path, $include_path;
require_once($include_path . "/explnum.inc.php");
require_once($class_path . "/storages/storages.class.php");
create_tableau_mimetype();

class storage_document {

    protected $id = 0;
    protected $num_ontology;
    protected $title = "";
    protected $description = "";
    protected $filename = "";
    protected $mimetype = "";
    protected $filesize = "";
    protected $vignette = "";
    protected $url = "";
    protected $path = "";
    protected $create_date = "";
    protected $num_storage = 0;
    protected $type_object = "";
    protected $human_size = 0;
    protected $storage;
    protected static $table;
    protected static $prefix;
    protected $used = array();

    public $num_object;

    public function __construct($id = 0) {
        $this->id = $id * 1;
        $this->fetch_datas_cache();
    }

    protected function fetch_datas_cache() {
        $this->fetch_datas();
    }

    protected function fetch_datas() {
        if ($this->id) {
            $query = "select 
					" . static::$prefix . "_title as title,
					" . static::$prefix . "_description as description,
					" . static::$prefix . "_filename as filename,
					" . static::$prefix . "_mimetype as mimetype,
					" . static::$prefix . "_filesize as filesize,
					" . static::$prefix . "_vignette as vignette,
					" . static::$prefix . "_url as url,
					" . static::$prefix . "_path as path,
					" . static::$prefix . "_create_date as create_date,
					" . static::$prefix . "_num_storage as num_storage,
					" . static::$prefix . "_type_object as type_object,
					" . static::$prefix . "_num_object as num_object from " . static::$table . " where id_" . static::$prefix . " = " . $this->id;
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_object($result);
                $this->title = $row->title;
                $this->description = $row->description;
                $this->filename = $row->filename;
                $this->mimetype = $row->mimetype;
                $this->filesize = $row->filesize;
                $this->vignette = $row->vignette;
                $this->url = $row->url;
                $this->path = $row->path;
                $this->create_date = $row->create_date;
                $this->num_storage = $row->num_storage;
                $this->type_object = $row->type_object;
                $this->num_object = $row->num_object;
            }
            if ($this->num_storage) {
                $this->storage = storages::get_storage_class($this->num_storage);
            }
        }
    }

    public function get_item_render($edit_js_function = "openEditDialog") {
        global $msg, $charset;
        $item = "
		<div class='document_item' id='document_" . $this->id . "'>
			<div class='document_item_content'>
			<img src='" . $this->get_vignette_url() . "'/>
			<br/>
			<p> <a href='#' onclick='" . $edit_js_function . "(" . $this->id . ");return false;' title='" . htmlentities($msg['cms_document_edit_link']) . "'>" . htmlentities(($this->title ? $this->title : $this->filename), ENT_QUOTES, $charset) . "</a><br />
			<span style='font-size:.8em;'>" . htmlentities($this->mimetype, ENT_QUOTES, $charset) . ($this->filesize ? " - (" . $this->get_human_size() . ")" : "") . "</span></p>
			</div>
		</div>";
        return $item;
    }

    public function get_item_form($selected = false, $edit_js_function = "openEditDialog") {
        global $msg, $charset;
        $item = "
		<div class='document_item" . ($selected ? " document_item_selected" : "") . "' id='document_" . $this->id . "'>
			<div class='document_item_content'>
				<img src='" . $this->get_vignette_url() . "'/>
				<br/>
				<p> <a href='#' onclick='" . $edit_js_function . "(" . $this->id . ");return false;' title='" . htmlentities($msg['cms_document_edit_link']) . "'>" . htmlentities(($this->title ? $this->title : $this->filename), ENT_QUOTES, $charset) . "</a><br />
				<span style='font-size:.8em;'>" . htmlentities($this->mimetype, ENT_QUOTES, $charset) . ($this->filesize ? " - (" . $this->get_human_size() . ")" : "") . "</span></p>
			</div>
			<div class='document_checkbox'>
				<input name='cms_documents_linked[]' onchange='document_change_background(" . $this->id . ");' type='checkbox'" . ($selected ? "checked='checked'" : "") . " value='" . htmlentities($this->id, ENT_QUOTES, $charset) . "'/>
			</div>
		</div>";
        return $item;
    }

    public function get_vignette_url() {
        global $pmb_url_base;
       	$vign_url =  "./ajax.php?module=cms&categ=document&action=thumbnail&id=" . $this->id;
		//On prend l'URL absolu pour avoir un hash de l'image correcte
		$img = getimage_cache(0,0,0,$pmb_url_base.$vign_url);
        if($img['location']){
        	return $img['location'];
        }
        return $vign_url;
    }

    public function get_document_url() {
        return "./ajax.php?module=cms&categ=document&action=render&id=" . $this->id;
    }

    public function get_human_size() {
        $units = array("o", "Ko", "Mo", "Go");
        $i = 0;
        do {
            if (!$this->human_size)
                $this->human_size = $this->filesize;
            $this->human_size = $this->human_size / 1024;
            $i++;
        }while ($this->human_size >= 1024);
        return round($this->human_size, 1) . " " . $units[$i];
    }

    public function delete() {
        global $msg, $force_delete;
        //suppression physique
        //On ajoute un moyen de forcer la suppression en base, dans les cas de bases polluées
        if ((!empty($this->storage) && $this->storage->delete($this->path . $this->filename)) || $force_delete) {
            //il ne reste plus que la base
            if (pmb_mysql_query("delete from " . static::$table . " where id_" . static::$prefix . " = " . $this->id)) {
                return true;
            }
        } else {
            return $msg['cms_document_delete_physical_error'];
        }
        return false;
    }

    public function calculate_vignette() {
        error_reporting(null);
        global $base_path, $include_path, $class_path;
        $path = $this->get_document_in_tmp();
        if ($path) {
            switch ($this->mimetype) {
                case "application/bnf+zip" :
                    require_once($class_path . "/docbnf_zip.class.php");
                    $doc = new docbnf_zip($path);
                    $this->vignette = construire_vignette($doc->getCover());
                    break;
                case "application/epub+zip" :
                    require_once($class_path . "/epubData.class.php");
                    $doc = new epub_Data($path);
                    file_put_contents($path, $doc->getCoverContent());
                    $this->vignette = construire_vignette($path);
                    break;
                default :
                    $this->vignette = construire_vignette($path);
                    break;
            }
            unlink($path);
        }
    }

    public function regen_vign() {
        $this->calculate_vignette();
        pmb_mysql_query("update " . static::$table . " set " . static::$prefix . "_vignette = '" . addslashes($this->vignette) . "' where id_" . static::$prefix . " = " . $this->id);
    }

    public function get_document_in_tmp() {
        $this->clean_tmp();
        global $base_path;
        $path = tempnam($base_path . "/temp/", static::$table . '_');
        if ($this->storage->duplicate($this->path . $this->filename, $path)) {
            return $path;
        }
        return false;
    }

    protected function clean_tmp() {
        global $base_path;
        $dh = opendir($base_path . "/temp/");
        if (!$dh)
            return;
        $files = array();
        while (($file = readdir($dh)) !== false) {
            if ($file != "." && $file != ".." && substr($file, 0, strlen(static::$table . '_')) == static::$table . '_') {
                $stat = stat($base_path . "/temp/" . $file);
                $files[$file] = array("mtime" => $stat['mtime']);
            }
        }
        closedir($dh);
        foreach ($files as $file => $stat) {
            //si le dernier accès au fichier est de plus de 3h, on vide...
            if (time() - $stat["mtime"] > (3600 * 3)) {
                if (is_dir($base_path . "/temp/" . $file)) {
                    $this->rrmdir($base_path . "/temp/" . $file);
                } else {
                    unlink($base_path . "/temp/" . $file);
                }
            }
        }
    }

    public function rrmdir($dir) {
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

    public function format_datas() {
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
            'create_date' => $this->create_date,
            'thumbnails_url' => $this->get_vignette_url()
        );
        return $datas;
    }

    public function render_thumbnail() {
		global $pmb_url_base;
        header('Content-Type: image/png');
 		      
	    $vignette_url=$this->get_vignette_url();
		if(strpos($vignette_url,'http') === '0'){
			 $img = getimage_cache(0,0,0,$vignette_url);
		}else{//On prend l'URL absolu pour avoir un hash de l'image correcte
			 $img = getimage_cache(0,0,0,$pmb_url_base.$vignette_url);
		}
        
        if ($this->vignette) {
            $vign = $this->vignette;
        } else {
            global $prefix_url_image;
            if ($prefix_url_image) {
            	$tmpprefix_url_image = $prefix_url_image;
            } else {
            	$tmpprefix_url_image = "./";
            }
            $vign = file_get_contents($tmpprefix_url_image . "images/mimetype/" . icone_mimetype($this->mimetype, extension_fichier($this->filename)));
        }        
        if ($img['hash_location']) {
        	file_put_contents($img['hash_location'], $vign);
        }
        print $vign;
    }

    public function render_doc() {
        if (is_object($this->storage)) {
            $content = $this->storage->get_content($this->path . $this->filename);
            if ($content) {
                header('Content-Type: ' . $this->mimetype);
                header('Content-Disposition: inline; filename="' . $this->filename . '"');
                if ($this->filesize)
                    header("Content-Length: " . $this->filesize);
                print $content;
            }
        }
    }

    public function delete_use() {
        global $used;

        $elem = array();
        if (!empty($used) && is_countable($used)) {
            for ($i = 0; $i < count($used); $i++) {
                $tmp = explode("_", $used[$i]);
                $elem[$tmp[0]][] = $tmp[1];
            }
        }
        foreach ($elem as $type => $elem) {
            //TODO, vérifier utilisation du document dans l'association
            $query = "delete from cms_documents_links where document_link_type_object = '" . $type . "' and document_link_num_object in (" . implode(",", $elem) . ") and document_link_num_document = " . $this->id;
            $result = pmb_mysql_query($query);
            if (!$result)
                return false;
        }
        return true;
    }

    public function get_id() {
        return $this->id;
    }

    public function set_title($title) {
        $this->title = $title;
        return $this;
    }

    public function set_filename($filename) {
        $this->filename = $filename;
        return $this;
    }

    public function set_mimetype($mimetype) {
        $this->mimetype = $mimetype;
        return $this;
    }

    public function set_filesize($filesize) {
        $this->filesize = $filesize;
        return $this;
    }

    public function set_num_ontology($num_ontology) {
        $this->num_ontology = $num_ontology;
        return $this;
    }

    public function set_description($description) {
        $this->description = $description;
        return $this;
    }

    public function set_vignette($vignette) {
        $this->vignette = $vignette;
        return $this;
    }

    public function set_url($url) {
        $this->url = $url;
        return $this;
    }

    public function set_path($path) {
        $this->path = $path;
        return $this;
    }

    public function set_create_date($create_date) {
        $this->create_date = $create_date;
        return $this;
    }

    public function set_num_storage($num_storage) {
        $this->num_storage = $num_storage;
        return $this;
    }

    public function set_type_object($type_object) {
        $this->type_object = $type_object;
        return $this;
    }

    public function set_num_object($num_object) {
    	$this->num_object = $num_object;
    	return $this;
    }
    
    public function save() {
        $query = "insert into " . static::$table . " set
			" . static::$prefix . "_title = '" . addslashes($this->title) . "',
			" . static::$prefix . "_filename = '" . addslashes($this->filename) . "',
			" . static::$prefix . "_mimetype = '" . addslashes($this->mimetype) . "',
			" . static::$prefix . "_filesize = '" . addslashes($this->filesize) . "',
			" . static::$prefix . "_vignette = '" . addslashes($this->vignette) . "',
			" . static::$prefix . "_url = '" . addslashes($this->url) . "',
			" . static::$prefix . "_path = '" . addslashes($this->path) . "',
			" . static::$prefix . "_create_date = '" . addslashes($this->create_date) . "',
			" . static::$prefix . "_num_storage = " . ($this->num_storage) . ",
			" . static::$prefix . "_type_object = '" . addslashes($this->type_object) . "',
			" . static::$prefix . "_num_object = " . $this->num_object . "
		";
        if (pmb_mysql_query($query)) {
            $this->id = pmb_mysql_insert_id();
            $this->storage = storages::get_storage_class($this->num_storage);
            $this->regen_vign();
            return true;
        }
        return false;
    }
    
    public function get_filename(){
    	return $this->filename;
    }
    
    public static function get_existing_documents_from_object($object_type, $object_id) {
    	$existing_documents = array();
    	
    	$query = 'select id_'.static::$prefix.' as id from '.static::$table.' where '.static::$prefix.'_type_object = "'.$object_type.'" and '.static::$prefix.'_num_object = '.$object_id;
    	$result = pmb_mysql_query($query);
    	if (pmb_mysql_num_rows($result)) {
    		while ($row = pmb_mysql_fetch_object($result)) {
    			$existing_documents[] = $row->id;
    		}
    	}
    	return $existing_documents;
    }
}
