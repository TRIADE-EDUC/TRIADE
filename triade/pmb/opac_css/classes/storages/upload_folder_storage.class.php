<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: upload_folder_storage.class.php,v 1.7 2023/09/22 15:04:57 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path, $include_path;
require_once ($class_path . "/storages/storage.class.php");
require_once ($class_path . "/upload_folder.class.php");
require_once ($include_path . "/explnum.inc.php");

class upload_folder_storage extends storage
{

    public $up_rep;

    public $filepath;

    public function __construct($id = 0)
    {
        $this->class_name = self::class;
        parent::__construct($id);
        if ($this->parameters['id_rep']) {
            $this->up_rep = new upload_folder($this->parameters['id_rep']);
        }
    }

    public function get_params_form()
    {
        global $charset, $msg;

        $params_form = "
		<div class='colonne3'>
			<label>" . htmlentities($msg['upload_repertoire_server'], ENT_QUOTES, $charset) . "</label>
		</div>
		<div class='colonne_suite'>";
        $req = "select repertoire_id, repertoire_nom from upload_repertoire order by repertoire_nom";
        $res = pmb_mysql_query($req);
        if (pmb_mysql_num_rows($res)) {
            $params_form .= "
			<select name='storage_params[id_rep]'>";
            while ($row = pmb_mysql_fetch_object($res)) {
                $params_form .= "
				<option value='" . $row->repertoire_id . "' " . ($row->repertoire_id == $this->config['upload_rep'] ? "selected='selected'" : "") . ">" . htmlentities($row->repertoire_nom, ENT_QUOTES, $charset) . "</option>";
            }
            $params_form .= "
			</select>";
        }
        $params_form .= "
		</div>";

        return $params_form;
    }

    public function add($file)
    {
        if ($this->parameters['id_rep']) {
            $this->filepath = $this->get_filepath($file);
            if (copy("./temp/".$file,$this->filepath)) {
                unlink("./temp/".$file);
                return true;
            }
        }
        return false;
    }

    public function get_filepath($file)
    {
        $chemin_hasher = "/";
        if ($this->up_rep->isHashing()) {
            $rep = $this->up_rep->hachage($file);
            @mkdir($rep);
            $chemin_hasher = $this->up_rep->formate_path_to_nom($rep);
            $file_name = $rep . $file;
            $chemin = $this->up_rep->formate_path_to_save($chemin_hasher);
        } else {
            $file_name = $this->up_rep->get_path($file) . $file;
            $chemin = $this->up_rep->formate_path_to_save("/");
        }
        $file_name = $this->up_rep->encoder_chaine($file_name);
        $i = 1;
        while (file_exists($file_name)) {
            if ($i == 1) {
                $file_name = substr($file_name, 0, strrpos($file_name, ".")) . "_" . $i . substr($file_name, strrpos($file_name, "."));
            } else {
                $file_name = substr($file_name, 0, strrpos($file_name, ($i - 1) . ".")) . $i . substr($file_name, strrpos($file_name, "."));
            }
            $i ++;
        }
        return $file_name;
    }

    public function delete($file)
    {
        if ($this->parameters['id_rep']) {
            if (! file_exists(str_replace("//", "/", $this->up_rep->repertoire_path . $file))) {
                return false;
            }
            return unlink(str_replace("//", "/", $this->up_rep->repertoire_path . $file));
        }
        return false;
    }

    public function get_uploaded_fileinfos()
    {
        $infos = array();
        if ($this->filepath) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $infos['title'] = "";
            $infos['description'] = "";
            $infos['filename'] = basename($this->filepath);
            $infos['mimetype'] = finfo_file($finfo, $this->filepath);
            $infos['filesize'] = filesize($this->filepath);
            $infos['vignette'] = construire_vignette($this->filepath);
            $infos['url'] = "";
            $infos['path'] = str_replace($this->up_rep->repertoire_path, "", $this->filepath);
            $infos['path'] = str_replace(basename($this->filepath), "", $infos['path']);
            if (! $infos['path'])
                $infos['path'] = "/";
            $infos['create_date'] = date('Y-m-d');
            $infos['num_storage'] = $this->id;
        }
        return $infos;
    }

    public function get_storage_infos()
    {
        global $msg;
        return $this->name . " (" . $msg['local_storage'] . ")";
    }

    public function duplicate($source_path, $dest_path)
    {
        return copy(str_replace("//", "/", $this->up_rep->repertoire_path . $source_path), $dest_path);
    }

    public function get_content($file_path)
    {
        if (file_exists(str_replace("//", "/", $this->up_rep->repertoire_path . $file_path))) {
            return file_get_contents(str_replace("//", "/", $this->up_rep->repertoire_path . $file_path));
        } else {
            return false;
        }
    }
}