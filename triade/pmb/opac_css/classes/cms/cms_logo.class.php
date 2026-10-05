<?php

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_logo.class.php,v 1.26.2.1 2025/01/08 14:32:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $include_path;
require_once($include_path."/templates/cms/cms_logo.tpl.php");

class cms_logo
{
    /**
     * Identifiant de l'objet
     *
     * @var int
     */
    public $id;

    /**
     * Type de l'objet
     *
     * @var string
     */
    public $type;

    /**
     * Donnees binaire du logo
     *
     * @var string
     */
    public $data = "";

    /**
     * Infos sur l'image (dimensions,mimetype,...)
     *
     * @var array
     */
    public $img_infos = [];

    public function __construct($id = "", $type = "section")
    {
        $this->id = intval($id);
        $this->type = $type;
        if ($this->id) {
            $this->fetch_data_cache();
        }
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
        foreach(get_object_vars($cms_object) as $propertieName => $propertieValue) {
            $this->{$propertieName} = $propertieValue;
        }
    }

    protected function fetch_data()
    {
        $table = $this->get_sql_table();
        if (!$table) {
            return false;
        }

        $rqt = "select ".$this->type."_logo from ".$table." where id_".$this->type." = '".$this->id."'";
        $res = pmb_mysql_query($rqt);
        if (pmb_mysql_num_rows($res)) {
            $this->data = pmb_mysql_result($res,0,0);
            pmb_mysql_free_result($res);

            if ($this->data) {
                $this->get_img_infos();
            }
        }
    }

    protected function get_img_infos()
    {
        $img_infos = getimagesizefromstring($this->data);
        if ($img_infos) {
            $this->img_infos['width'] = $img_infos[0];
            $this->img_infos['height'] = $img_infos[1];
            $this->img_infos['mimetype'] = $img_infos['mime'];

            $this->img_infos['render_fct'] = false;
            $this->img_infos['render_params'] = [];

            switch($this->img_infos['mimetype']) {
                case 'image/png':
                    $this->img_infos['type'] = 'png';
                    $this->img_infos['render_fct'] = 'imagepng';
                    if(defined('PNG_ALL_FILTERS')) {
                        $this->img_infos['render_params'] = [9, PNG_ALL_FILTERS];
                    } else {
                        $this->img_infos['render_params'] = [9];
                    }
                    break;
                case 'image/jpeg':
                    $this->img_infos['type'] = 'jpeg';
                    $this->img_infos['render_fct'] = 'imagejpeg';
                    if (strlen($this->data) < 102400) {
                        // Si image < 100ko, on ne réduit pas la qualité, sinon on laisse le réglage par défaut de imagejpeg
                        $this->img_infos['render_params'] = [100];
                    }
                    break;
                case 'image/gif':
                    $this->img_infos['type'] = 'gif';
                    $this->img_infos['render_fct'] = 'imagegif';
                    break;
            }
        }
    }

    public function get_form()
    {
        global $cms_logo_form_tpl;
        global $cms_logo_form_exist_obj_tpl;
        global $cms_logo_form_new_obj_tpl;

        $form = $cms_logo_form_tpl;
        if($this->id) {
            $form = str_replace("!!field!!", $cms_logo_form_exist_obj_tpl, $form);
        } else {
            $form = str_replace("!!field!!", $cms_logo_form_new_obj_tpl, $form);
            $form = str_replace("!!js!!", "", $form);
        }
        $form = str_replace("!!id!!", $this->id, $form);
        $form = str_replace("!!type!!", $this->type, $form);
        return $form;
    }

    public function get_field()
    {
        global $cms_logo_field_tpl, $database;

        $field = str_replace("!!type!!", $this->type, $cms_logo_field_tpl);

        //si $_FILES n'est pas vide, on a du matos...
        if(count($_FILES)) {
            $result = $this->save();
            if($result === true) {
                $js = "
				var div_vign = window.parent.document.getElementById('cms_logo_vign');
				var old_img = window.parent.document.getElementById('cms_logo_vign_img');
				div_vign.removeChild(old_img);
				var img = document.createElement('img');
				img.setAttribute('id','cms_logo_vign_img');
				img.setAttribute('class','cms_logo_vign');
				img.setAttribute('src','./cms_vign.php?type=".$this->type."&id=".$this->id."&mode=vign&database=".$database."');
				div_vign.appendChild(img);";
            } else {
                $js = "
					alert(\"".$result."\");";
            }
        } else {
            $js = "";
        }
        $field = str_replace("!!js!!", $js, $field);
        return $field;
    }

    public function clean_cache($id = 0)
    {
        //TODO
    }

	public function save()
    {
        global $msg;
        //on commence par regarder ce qu'on nous a donné...
        $mimetype = $_FILES['cms_logo_file']['type'];
        //on ne veut que les images
        if (substr($mimetype, 0, 5) != "image") {
            return $msg['cms_editorial_form_logo_unsupported_file'];
        } else {
            if(substr($mimetype, 6, 3) == "png") {
                $data = file_get_contents($_FILES['cms_logo_file']['tmp_name']);
            } else {
                //et que du png...
                $data = $this->convert_to_png($_FILES['cms_logo_file']['tmp_name']);
            }
        }

        $table = $this->get_sql_table();
        if (!$table) {
            return $msg['cms_editorial_form_logo_cant_save'];
        }

		$rqt = "update ".$table." set ".$this->type."_logo=\"".addslashes($data)."\" where id_".$this->type." = '".$this->id."'";
        $res = pmb_mysql_query($rqt);
        if ($res) {
            $this->clean_cache($this->id);
            return true;
        } else {
            return $msg['cms_editorial_form_logo_cant_save'];
        }
    }

    protected function get_sql_table()
    {
        switch ($this->type) {
            case "section":
                $table = "cms_sections";
                break;
            case "article":
                $table = "cms_articles";
                break;
            default:
                $table = "";
                break;
        }
        return $table;
    }

    protected function convert_to_png($picture)
    {
        $data = file_get_contents($picture);
        $src_img = imagecreatefromstring($data);
        $src_x = imagesx($src_img);
        $src_y = imagesy($src_img);
        $dst_img = imagecreatetruecolor($src_x, $src_y);
        ImageSaveAlpha($dst_img, true);
        ImageAlphaBlending($dst_img, false);
        imagefilledrectangle($dst_img, 0, 0, $src_x, $src_y, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $src_x, $src_y, $src_x, $src_y);
        $tmp_path = realpath("./temp");
        imagepng($dst_img, $tmp_path."/tmp_cms_logo");
        $data = file_get_contents($picture);
        unlink($tmp_path."/tmp_cms_logo");
        return $data;
    }

    public function show_picture($mode = '')
    {
        global $cms_active_image_cache,$base_path, $database;

        if (!count($this->img_infos)) {
            $this->get_img_infos();
        }

        if ($cms_active_image_cache && file_exists($base_path.'/temp/cms_vign/'.$database.'/'.$mode.'/'.$this->type.$this->id.'.'.$this->img_infos['type'])) {
            header('Content-Type: '.$this->img_infos['mimetype']);
            print file_get_contents($base_path.'/temp/cms_vign/'.$database.'/'.$mode.'/'.$this->type.$this->id.'.'.$this->img_infos['type']);
        } else {
            if (strpos($mode, 'custom_') !== false) {
                $elems = explode('_', $mode);
                if (!is_numeric($elems[1])) {
                    header("HTTP/1.0 404 Not Found");
                    return;
                }
                $size = $elems[1] * 1;
                if($size > 0) {
                    $dst_img = $this->resize($size, $size);
                } else {
                    $dst_img = $this->resize(500, 500);
                }
            } else {
                switch($mode) {
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
                        if($this->img_infos['type'] == 'png') {
                            //Pour les images non redimensionnées
                            imageSaveAlpha($dst_img, true);
                        }
                        break;
                    default:
                        header("HTTP/1.0 404 Not Found");
                        return;
                        break;
                }
            }

            if ($dst_img) {
                if(function_exists($this->img_infos['render_fct'])) {
                    header('Content-Type: '.$this->img_infos['mimetype']);
                    $render_params = array_merge([$dst_img, null], $this->img_infos['render_params']);
                    call_user_func_array($this->img_infos['render_fct'], $render_params);
                    if($cms_active_image_cache) {
                        $this->init_cache_path($mode);
                        $render_params = array_merge([$dst_img, $base_path.'/temp/cms_vign/'.$database.'/'.$mode.'/'.$this->type.$this->id.'.'.$this->img_infos['type']], $this->img_infos['render_params']);
                        call_user_func_array($this->img_infos['render_fct'], $render_params);
                    }
                }
            }
        }
    }

    private function init_cache_path($mode)
    {
        global $base_path, $database;
        if(!file_exists($base_path."/temp/cms_vign")) {
            mkdir($base_path."/temp/cms_vign");
        }
        if(!file_exists($base_path."/temp/cms_vign/".$database)) {
            mkdir($base_path."/temp/cms_vign/".$database);
        }
        if(!file_exists($base_path."/temp/cms_vign/".$database."/".$mode)) {
            mkdir($base_path."/temp/cms_vign/".$database."/".$mode);
        }
    }

    public function get_vign()
    {
        $this->resize(100, 100);
    }

    public function get_small_vign()
    {
        $this->resize(16, 16);
    }

    public function get_large()
    {
        $this->resize(0, 0);
    }

    protected function resize($size_x = 0, $size_y = 0)
    {

        if ($this->data) {
            if(!$this->img_infos['render_fct']) {
                header('Content-Type: image/png');
                print file_get_contents(get_url_icon('vide.png'));
                return;
            }

            $src_img = imagecreatefromstring($this->data);

            if (!$src_img) {
                header('Content-Type: image/png');
                print file_get_contents(get_url_icon('vide.png'));
                return;
            }

            if(!$size_x && !$size_y) {
                return $src_img;
            }

            $maxX = $size_x;
            $maxY = $size_y;

            $rs = $maxX / $maxY;
            $taillex = $this->img_infos['width'];
            $tailley = $this->img_infos['height'];
            if (!$taillex || !$tailley) {
                header('Content-Type: image/png');
                print file_get_contents(get_url_icon('vide.png'));
                return;
            }
            if (($taillex > $maxX) || ($tailley > $maxY)) {
                $r = $taillex / $tailley;
                if (($r < 1) && ($rs < 1)) {
                    //Si x plus petit que y et taille finale portrait
                    //Si le format final est plus large en proportion
                    if ($rs > $r) {
                        $new_h = $maxY;
                        $new_w = $new_h * $r;
                    } else {
                        $new_w = $maxX;
                        $new_h = $new_w / $r;
                    }
                } elseif (($r < 1) && ($rs >= 1)) {
                    //Si x plus petit que y et taille finale paysage
                    $new_h = $maxY;
                    $new_w = $new_h * $r;
                } elseif (($r > 1) && ($rs < 1)) {
                    //Si x plus grand que y et taille finale portrait
                    $new_w = $maxX;
                    $new_h = $new_w / $r;
                } else {
                    //Si x plus grand que y et taille finale paysage
                    if ($rs < $r) {
                        $new_w = $maxX;
                        $new_h = $new_w / $r;
                    } else {
                        $new_h = $maxY;
                        $new_w = $new_h * $r;
                    }
                }
            } else {
                $new_h = $tailley ;
                $new_w = $taillex ;
            }

            $dst_img = imagecreatetruecolor($new_w, $new_h);
            if($this->img_infos['type'] == 'png') {
                imageSaveAlpha($dst_img, true);
                imageAlphaBlending($dst_img, false);
            }
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_w, $new_h, $this->img_infos['width'], $this->img_infos['height']);

            return $dst_img;
        } else {
            header('Content-Type: image/png');
            print file_get_contents(get_url_icon('vide.png'));
            return;
        }
    }

    public function get_vign_url($mode = '')
    {
        global $opac_url_base, $base_path, $cms_active_image_cache, $database;

        if ($cms_active_image_cache && isset($this->img_infos['type']) && file_exists($base_path.'/temp/cms_vign/'.$database.'/'.$mode.'/'.$this->type.$this->id.'.'.$this->img_infos['type'])) {
            return $opac_url_base.'temp/cms_vign/'.$database.'/'.$mode.'/'.$this->type.$this->id.'.'.$this->img_infos['type'];
        } else {
            return $opac_url_base.'cms_vign.php?type='.$this->type.'&id='.$this->id.'&database='.$database.'&mode='.$mode;
        }
    }

    public function format_datas()
    {
        return [
            'small_vign' => $this->data ? $this->get_vign_url("small_vign") : false,
            'vign' => $this->data ? $this->get_vign_url("vign") : false,
            'small' => $this->data ? $this->get_vign_url("small") : false,
            'medium' => $this->data ? $this->get_vign_url("medium") : false,
            'big' => $this->data ? $this->get_vign_url("big") : false,
            'large' => $this->data ? $this->get_vign_url("large") : false,
            'custom' => $this->data ? $this->get_vign_url("custom_") : false,
            'exists' => $this->data ? true : false,
        ];
    }


    public static function get_format_data_structure()
    {
        global $msg;
        return [
            [
                'var' => "small_vign",
                'desc' => $msg['cms_module_common_datasource_desc_small_vign'],
            ],
            [
                'var' => "vign",
                'desc' => $msg['cms_module_common_datasource_desc_vign'],
            ],
            [
                'var' => "small",
                'desc' => $msg['cms_module_common_datasource_desc_small'],
            ],
            [
                'var' => "medium",
                'desc' => $msg['cms_module_common_datasource_desc_medium'],
            ],
            [
                'var' => "big",
                'desc' => $msg['cms_module_common_datasource_desc_big'],
            ],
            [
                'var' => "large",
                'desc' => $msg['cms_module_common_datasource_desc_large'],
            ],
            [
                'var' => "custom",
                'desc' => $msg['cms_module_common_datasource_desc_custom'],
            ],
            [
                'var' => "exists",
                'desc' => $msg['cms_module_common_datasource_desc_logo_exists'],
            ],
        ];
    }
}
