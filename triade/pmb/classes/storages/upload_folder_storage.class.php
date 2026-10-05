<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: upload_folder_storage.class.php,v 1.12.2.1 2024/09/30 13:12:06 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path, $include_path;
require_once ($class_path . "/storages/storage.class.php");
require_once ($class_path . "/upload_folder.class.php");
require_once ($include_path . "/explnum.inc.php");

class upload_folder_storage extends storage
{

    public $up_rep;

    public function __construct($id = 0)
    {
        $this->class_name = self::class;
        parent::__construct($id);
        if (! empty($this->parameters['id_rep'])) {
            $this->up_rep = new upload_folder($this->parameters['id_rep']);
        }
    }

    public function get_params_form()
    {
        global $charset, $msg;

        $req = "select repertoire_id, repertoire_nom from upload_repertoire order by repertoire_nom";
        $res = pmb_mysql_query($req);
        if (pmb_mysql_num_rows($res)) {
            $params_form = "
			<div class='colonne3'>
				<label for='storage_params_id_rep'>" . htmlentities($msg['upload_repertoire_server'], ENT_QUOTES, $charset) . "</label>
			</div>
			<div class='colonne_suite'>";
            $params_form .= "
			<select id='storage_params_id_rep' name='storage_params[id_rep]'>";
            while ($row = pmb_mysql_fetch_object($res)) {
                $params_form .= "
				<option value='" . $row->repertoire_id . "' " . (isset($this->parameters['id_rep']) && ($row->repertoire_id == $this->parameters['id_rep']) ? "selected='selected'" : "") . ">" . htmlentities($row->repertoire_nom, ENT_QUOTES, $charset) . "</option>";
            }
            $params_form .= "
			</select>";
        } else {
            $params_form = "
				<div class='colonne3'>
			<label>" . htmlentities($msg['upload_repertoire_undefined'], ENT_QUOTES, $charset) . "</label>";
        }
        $params_form .= "
		</div>";

        return $params_form;
    }

    public function add($file)
    {
        if ($this->parameters['id_rep']) {
            $filepath = $this->get_filepath($file);
            if (file_exists("./temp/" . $file)) {
                if (copy("./temp/".$file,$filepath)) {
                    unlink("./temp/".$file);
                    return $filepath;
                }
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

    public function get_uploaded_fileinfos($filepath)
    {
        $infos = array();
        if ($filepath) {
            $infos['title'] = "";
            $infos['description'] = "";
            $infos['filename'] = basename($filepath);
            $infos['mimetype'] = $this->get_mimetype($filepath);
            $infos['filesize'] = filesize($filepath);
            $infos['vignette'] = construire_vignette($filepath);
            $infos['url'] = "";
            $infos['path'] = str_replace($this->up_rep->repertoire_path, "", $filepath);
            $infos['path'] = str_replace(basename($filepath), "", $infos['path']);
            if (empty($infos['path'])) {
                $infos['path'] = "/";
            }
            $infos['create_date'] = date('Y-m-d H:i:s');
            $infos['num_storage'] = $this->id;
        }
        return $infos;
    }

    public function get_storage_infos()
    {
        global $msg;
        return $this->name . " (" . $msg['local_storage'] . ")";
    }

    public function get_infos()
    {
        return $this->up_rep->repertoire_path;
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