<?php

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: storage.class.php,v 1.15.6.1 2024/09/30 13:12:06 qvarin Exp $

use Pmb\Common\Library\Upload\UploadFileValidator;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;
require_once $class_path.'/file_uploader.class.php';

class storage
{
    public const UPLOAD_DIR = "./temp/";
    public const ERROR_INVALIDE_EXT = 1;
    public const ERROR_INVALIDE_SIZE = 2;

    /**
     * Error code
     *
     * @var integer
     */
    public $codeError = 0;

    public $id = 0;
    public $class_name = "";
    public $parameters = [];
    public $name = "";

    public $numWrittenBytes;
    public $fileName;

    /**
     * Constructor
     *
     * @param integer $id
     */
    public function __construct($id = 0)
    {
        $this->id = intval($id);
        $this->fetch_datas();
    }

    /**
     * Get form
     *
     * @param string $class
     * @return string
     */
    public function get_form($class)
    {
        global $class_path;
        if ($class == $this->class_name) {
            //on a la classe déjà déclaré
            $obj = storages::get_storage_class($this->id);
        } else {
            require_once $class_path."/storages/".$class.".class.php";
            $obj = new $class($this->id);
        }
        return $obj->get_params_form();
    }

    /**
     * Fetch datas
     *
     * @return void
     */
    protected function fetch_datas()
    {
        $query = "select * from storages where id_storage = ".$this->id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $this->name = $row->storage_name;
            $this->class_name = $row->storage_class;
            $this->parameters = unserialize($row->storage_params);
        }
    }

    /**
     * Set properties from form
     *
     * @return void
     */
    public function set_properties_from_form()
    {
        // A surcharger
    }

    /**
     * Upload
     *
     * @param boolean $ajax (optional, default true)
     * @param string $field_name (optional)
     * @return array|void
     */
    public function upload_process($ajax = true, $field_name = "")
    {
        if ($ajax) {
            return $this->ajax_upload_process($field_name);
        }
        return $this->post_upload_process($field_name);
    }

    /**
     * Post upload
     *
     * @param [type] $field_name
     * @return void
     */
    public function post_upload_process($field_name)
    {
        if (is_dir(static::UPLOAD_DIR)) {
            if (is_writable(static::UPLOAD_DIR)) {
                return $this->get_file_post($field_name);
            } else {
                // $protocol = $_SERVER["SERVER_PROTOCOL"];
                // header($protocol.' 405 Method Not Allowed');
                // exit('Upload directory is not writable.');
            }
        }
    }

    /**
     * Ajax upload
     *
     * @param string $field_name
     * @return array|void
     */
    public function ajax_upload_process($field_name = "")
    {
        global $fnc;

        switch ($fnc) {
            case 'upl':
                if ($this->isUploadDirValid()) {
                    $filename = $this->get_file();
                    if ($filename) {
                        return [$filename];
                    }
                    return [];
                } else {
                    http_response_code(404);
                    exit('Upload directory does not exist or is not writable.');
                }
                break;
            case 'del':
                http_response_code(500);
                exit('Not implemented.');
                // $fileName = isset($_GET['fileName']) ? $_GET['fileName'] : null;
                // if ($fileName) {
                //     $this->delete($fileName, static::UPLOAD_DIR);
                // }else {
                //     http_response_code(404);
                //     exit('No file name provided.');
                // }
                break;
            case 'resume':
                http_response_code(500);
                exit('Not implemented.');
                // $this->save(static::UPLOAD_DIR, true);
                break;
            case 'getNumWrittenBytes':
                http_response_code(500);
                exit('Not implemented.');
                // $fileName = isset($_GET['fileName']) ? $_GET['fileName'] : null;
                // if($fileName){
                //     if (file_exists(static::UPLOAD_DIR.$fileName)) {
                //         echo json_encode(array('numWritten' => filesize(static::UPLOAD_DIR.$fileName)));
                //     }else {
                //         http_response_code(404);
                //         exit('Previous upload not found. Resume not possible.');
                //     }
                // }else{
                //     http_response_code(404);
                //     exit('No file name provided.');
                // }
                break;
            default:
                http_response_code(404);
                exit('Function not found.');
        }
    }

    /**
     * Get num written bytes
     *
     * @return int
     */
    public function getNumWrittenBytes()
    {
        return $this->numWrittenBytes;
    }

    /**
     * Get file
     *
     * @return boolean
     */
    protected function get_file()
    {
        $protocol = $_SERVER["SERVER_PROTOCOL"];

        $file = file_uploader::get_file();
        if(is_object($file)) {
            $this->fileName = $file->name;
            $file->content = file_get_contents("php://input");

            if (
                !UploadFileValidator::isExtensionAllowed(UploadFileValidator::findExtensionWithContent($file->content)) ||
                !UploadFileValidator::isExtensionAllowed(UploadFileValidator::findExtensionWithFile($file->name))
            ) {
                http_response_code(403);
                $this->codeError = static::ERROR_INVALIDE_EXT;
                return false;
            }

            // Since I don't know if the header content-length can be spoofed/is reliable, I check the file size again after it is uploaded
            $limit = file_uploader::get_limit();
            if (mb_strlen($file->content) > $limit) {
                http_response_code(403);
                $this->codeError = static::ERROR_INVALIDE_SIZE;
                return false;
            }

            $this->numWrittenBytes = file_put_contents(static::UPLOAD_DIR.$file->name, $file->content);
            if ($this->numWrittenBytes !== false) {
                header($protocol.' 201 Created');
                $success = $this->add($file->name);
                if(!$success) {
                    unlink(static::UPLOAD_DIR.$file->name);
                }
                return $success;
            } else {
                header($protocol.' 505 Internal Server Error');
                return false;
            }
        }
    }

    /**
     * Fonction permettant de récupérer les fichiers postés
     *
     * @param string $field_name
     * @return array
     */
    protected function get_file_post($field_name = "")
    {
        /**
         * TODO: test mimetype
         */
        $file_names = [];
        if(isset($_FILES)) {
            foreach($_FILES[$field_name]['name'] as $key => $name) {
                $i = 1;
                $file_name = $name['value'];
                while(file_exists(static::UPLOAD_DIR.$file_name)) {
                    if($i == 1) {
                        $file_name = substr($file_name, 0, strrpos($file_name, "."))."_".$i.substr($file_name, strrpos($file_name, "."));
                    } else {
                        $file_name = substr($file_name, 0, strrpos($file_name, ($i - 1).".")).$i.substr($file_name, strrpos($file_name, "."));
                    }
                    $i++;
                }
                move_uploaded_file($_FILES[$field_name]['tmp_name'][$key]['value'], './temp/'.$file_name);
                $file_names[] = $this->add($file_name);
            }
        }
        return $file_names;
    }

    /**
     * Get params form
     *
     * @return string
     */
    public function get_params_form()
    {
        // A surcharger
        return "";
    }

    /**
     * Get params to save
     *
     * @return void
     */
    public function get_params_to_save()
    {
        // A surcharger
    }

    /**
     * Add
     *
     * @param string $file
     * @return boolean
     */
    public function add($file)
    {
        // A surcharger
        return false;
    }

    /**
     * Update
     *
     * @param string $new_file
     * @return void
     */
    public function update($new_file)
    {
        // A surcharger
    }

    /**
     * Delete
     *
     * @param string $file
     * @return boolean
     */
    public function delete($file)
    {
        // A surcharger
        return false;
    }

    /**
     * Move
     *
     * @param string $file
     * @param string $dest
     * @return void
     */
    public function move($file, $dest)
    {
        // A surcharger
    }

    /**
     * Get uploaded fileinfos
     *
     * @param string $filepath
     * @return array
     */
    public function get_uploaded_fileinfos($filepath)
    {
        // A surcharger
        return [];
    }

    /**
     * Get infos
     *
     * @return string
     */
    public function get_infos()
    {
        // A surcharger
        return "";
    }

    /**
     * Get mimetype
     *
     * @param string $filepath
     * @return string
     */
    public function get_mimetype($filepath)
    {
        $finfo = new finfo(FILEINFO_MIME);
        //petit hack pour les formats exotiques(type BNF)
        $arrayMimetypess = ["application/bnf+zip"];
        $arrayExtensions = [".bnf"];
        $original_extension = (false === $pos = strrpos($filepath, '.')) ? '' : substr($filepath, $pos);
        if(in_array($original_extension, $arrayExtensions)) {
            for($i = 0 ; $i < count($arrayExtensions) ; $i++) {
                if($arrayExtensions[$i] == $original_extension) {
                    return $arrayMimetypess[$i];
                }
            }
        } else {
            $infos = $finfo->file($filepath);
            return substr($infos, 0, strpos($infos, ";"));
        }
    }

    /**
     * Is upload dir valid
     *
     * @return boolean
     */
    protected function isUploadDirValid()
    {
        return is_dir(static::UPLOAD_DIR) && is_writable(static::UPLOAD_DIR);
    }
}
