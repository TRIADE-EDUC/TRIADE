<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_logo.class.php,v 1.31.2.3.2.2 2025/05/15 09:53:52 jparis Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $include_path;
require_once($include_path . "/templates/cms/cms_logo.tpl.php");

class cms_logo
{
    public const CACHE_FILE_EXTENSION = ['png', 'jpg', 'jpeg', 'gif'];

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

    // infos image (dimensions,mimetype,...)
    public function __construct($id = "", $type = "section")
    {
        $this->id = $id * 1;
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
        foreach (get_object_vars($cms_object) as $propertieName => $propertieValue) {
            $this->{$propertieName} = $propertieValue;
        }
    }

    protected function fetch_data()
    {
        $table = $this->get_sql_table();
        if (!$table) {
            return false;
        }

        $rqt = "select " . $this->type . "_logo from " . $table . " where id_" . $this->type . " = '" . $this->id . "'";
        $res = pmb_mysql_query($rqt);
        if (pmb_mysql_num_rows($res)) {
            $this->data = pmb_mysql_result($res, 0, 0);
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

            switch ($this->img_infos['mimetype']) {
                case 'image/png':
                    $this->img_infos['type'] = 'png';
                    $this->img_infos['render_fct'] = 'imagepng';
                    if (defined('PNG_ALL_FILTERS')) {
                        $this->img_infos['render_params'] = [
                            9,
                            PNG_ALL_FILTERS,
                        ];
                    } else {
                        $this->img_infos['render_params'] = [
                            9,
                        ];
                    }
                    break;
                case 'image/jpeg':
                    $this->img_infos['type'] = 'jpeg';
                    $this->img_infos['render_fct'] = 'imagejpeg';
                    if (strlen($this->data) < 102400) {
                        // Si image < 100ko, on ne réduit pas la qualité, sinon on laisse le réglage par défaut de imagejpeg
                        $this->img_infos['render_params'] = [
                            100,
                        ];
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
        if ($this->id) {
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
        global $cms_logo_field_tpl, $cms_logo_delete;

        $field = str_replace("!!type!!", $this->type, $cms_logo_field_tpl);

        // si $_FILES n'est pas vide, on a du matos...
        if ($cms_logo_delete) {
            $result = $this->delete();

            if ($result === true) {
                $js = $this->get_js_form();
            } else {
                $js = "alert(\"" . $result . "\");";
            }

        } else {

            if (count($_FILES)) {
                $result = $this->save();

                if ($result === true) {
                    $js = $this->get_js_form();
                } else {
                    $js = "alert(\"" . $result . "\");";
                }

            } else {
                $js = $this->get_js_form();
            }
        }

        $field = str_replace("!!js!!", $js, $field);
        return $field;
    }

    public function get_js_form()
    {
        global $cms_img_pics_max_size, $msg;

        return "
            let div_vign = window.parent.document.getElementById('cms_logo_vign');
            let old_img = window.parent.document.getElementById('cms_logo_vign_img');

            let date = new Date();

            div_vign.removeChild(old_img);

            let img = document.createElement('img');
            img.setAttribute('id', 'cms_logo_vign_img');
            img.setAttribute('class', 'cms_logo_vign');
            img.setAttribute('src', './cms_vign.php?type=" . $this->type . "&id=" . $this->id . "&mode=vign'+'&' + date.getTime());

            div_vign.appendChild(img);

            let cms_logo_form = document.getElementById('cms_logo_form');
            cms_logo_form.addEventListener('submit', function(e) {
                e.preventDefault();

                let imageInput = document.querySelector(\"input[name='cms_logo_file']\");
                let file = imageInput.files[0];

                if (file) {
                    let reader = new FileReader();

                    reader.onload = function(event) {
                        let img = new Image();
                        img.onload = function() {
                            if (img.width > " . $cms_img_pics_max_size . " || img.height > " . $cms_img_pics_max_size . ") {
                                if(confirm('" . sprintf($msg['cms_editorial_form_logo_size'], strval($cms_img_pics_max_size)) . "')) {
                                    cms_logo_form.submit();
                                }
                                return;
                            }
                            cms_logo_form.submit();
                        };

                        img.src = event.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        ";
    }

    /**
     * Permet de supprimer le cache d'un logo en parcourant le dossier recursivement
     *
     * @param string $path Chemin du dossier
     * @param integer $id Identifiant du logo
     * @throws InvalidArgumentException
     * @return void
     */
    private function clean_cache_directory(string $path, int $id)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException("$path is not a valid directory");
        }

        $resources = glob($path . "/*", GLOB_NOSORT);
        foreach ($resources as $resource) {
            if (in_array(basename($resource), ["CVS", ".", ".."])) {
                continue;
            }

            if (is_dir($resource)) {
                $this->clean_cache_directory($resource, $id);
            } else {
                $filename = basename($resource);
                $parsed_file = explode(".", $filename);
                if (
                    is_file($resource) &&
                    $parsed_file[0] == $this->type . $id &&
                    in_array($parsed_file[1], cms_logo::CACHE_FILE_EXTENSION)
                ) {
                    unlink($resource);
                }
            }
        }
    }

    /**
     * Permet de supprimer le cache d'un logo
     *
     * @param integer $id
     * @return void
     */
    public function clean_cache($id = 0)
    {
        global $base_path;

        if (!$id) {
            $id = $this->id;
        }

        $gestion_path = join(DIRECTORY_SEPARATOR, [$base_path, "temp", "cms_vign"]);
        if (is_dir($gestion_path)) {
            $this->clean_cache_directory($gestion_path, $id);
        }

        $opac_path = join(DIRECTORY_SEPARATOR, [$base_path, "opac_css", "temp", "cms_vign"]);
        if (is_dir($opac_path)) {
            $this->clean_cache_directory($opac_path, $id);
        }
    }

    public function delete()
    {
        global $msg;

        $table = $this->get_sql_table();
        if (!$table) {
            return $msg['cms_editorial_form_logo_cant_delete'];
        }
        $rqt = "update " . $table . " set " . $this->type . "_logo='' where id_" . $this->type . " = '" . $this->id . "'";
        $res = pmb_mysql_query($rqt);
        if ($res) {
            $this->clean_cache($this->id);
            return true;
        } else {
            return $msg['cms_editorial_form_logo_cant_delete'];
        }
    }

    public function save()
    {
        global $msg, $cms_img_pics_max_size;
        // on commence par regarder ce qu'on nous a donné...
        $mimetype = $_FILES['cms_logo_file']['type'];
        // on ne veut que les images
        if (substr($mimetype, 0, 5) != "image") {
            return $msg['cms_editorial_form_logo_unsupported_file'];
        } else {
            $data = file_get_contents($_FILES['cms_logo_file']['tmp_name']);
            $img = imagecreatefromstring($data);
            
            $img_x = imagesx($img);
            $img_y = imagesy($img);
            
            $cms_img_pics_max_size = intval($cms_img_pics_max_size);
            if($img_x > $cms_img_pics_max_size || $img_y > $cms_img_pics_max_size) {
                $data = get_resized_img($data, $cms_img_pics_max_size, $cms_img_pics_max_size);
                if(!$data) {
                    return $msg['cms_editorial_form_logo_cant_save'];
                }
            }
        }
        $table = $this->get_sql_table();
        if (!$table) {
            return $msg['cms_editorial_form_logo_cant_save'];
        }
        $rqt = "update " . $table . " set " . $this->type . "_logo=\"" . addslashes($data) . "\" where id_" . $this->type . " = '" . $this->id . "'";
        $res = pmb_mysql_query($rqt);
        if ($res) {
            $this->clean_cache($this->id);
            return true;
        } else {
            return $msg['cms_editorial_form_logo_cant_save'];
        }
    }

    public function save_from_content($content)
    {
        $table = $this->get_sql_table();
        if (!$table) {
            return false;
        }
        $rqt = "update " . $table . " set " . $this->type . "_logo=\"" . addslashes($content) . "\" where id_" . $this->type . " = '" . $this->id . "'";
        $res = pmb_mysql_query($rqt);
        if ($res) {
            $this->clean_cache($this->id);
            return true;
        } else {
            return false;
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
        imagepng($dst_img, $tmp_path . "/tmp_cms_logo");
        $data = file_get_contents($picture);
        unlink($tmp_path . "/tmp_cms_logo");
        return $data;
    }

    public function show_picture($mode = '')
    {
        global $cms_active_image_cache;

        $cache_path = $this->get_img_cache_path($mode);
        if ($cms_active_image_cache && is_file($cache_path)) {
            header('Content-Type: ' . $this->img_infos['mimetype']);
            print file_get_contents($cache_path);
            return;
        }

        if ($this->data) {
            if (!count($this->img_infos)) {
                $this->get_img_infos();
            }

            if ($this->img_infos['render_fct']) {
                $img_data = $this->data;
            } else {
                $img_data = file_get_contents(get_url_icon('vide.png'));
            }
        } else {
            $img_data = file_get_contents(get_url_icon('vide.png'));
        }

        if (strpos($mode, 'custom_') !== false) {
            $elems = explode('_', $mode);
            $size = $elems[1] * 1;
            if ($size > 0) {
                $resized_img = get_resized_img($img_data, $size, $size);
            } else {
                $resized_img = get_resized_img($img_data, 500, 500);
            }
        } else {
            switch ($mode) {
                case 'small_vign':
                    $resized_img = get_resized_img($img_data, 16, 16);
                    break;
                case 'vign':
                    $resized_img = get_resized_img($img_data, 100, 100);
                    break;
                case 'small':
                    $resized_img = get_resized_img($img_data, 140, 140);
                    break;
                case 'medium':
                    $resized_img = get_resized_img($img_data, 300, 300);
                    break;
                case 'big':
                    $resized_img = get_resized_img($img_data, 600, 600);
                    break;
                case 'large':
                default:
                    $resized_img = $img_data;
                    break;
            }
        }

        $resized_img = imagecreatefromstring($resized_img);
        if (isset($this->img_infos['type']) && $this->img_infos['type'] == 'png') {
            // On active la transparence pour les png
            imageSaveAlpha($resized_img, true);
        }

        if (function_exists($this->img_infos['render_fct'])) {
            header('Content-Type: ' . $this->img_infos['mimetype']);
            $render_params = array_merge(
                [
                    $resized_img,
                    null,
                ],
                $this->img_infos['render_params']
            );
            call_user_func_array($this->img_infos['render_fct'], $render_params);

            if ($cms_active_image_cache) {
                $this->init_cache_path($mode);
                $render_params[1] = $this->get_img_cache_path($mode);
                call_user_func_array($this->img_infos['render_fct'], $render_params);
            }
        } else {
            header('Content-Type: image/png');
            print file_get_contents(get_url_icon('vide.png'));
        }
    }

    /**
     * Retourne le chemin du cache
     *
     * @param string $mode
     * @return string
     */
    private function get_img_cache_path(string $mode = "")
    {
        global $base_path, $database;
        $cache_path = [$base_path, "temp", "cms_vign", $database];
        if (!empty($mode)) {
            $cache_path[] = $mode;
        }

        $cache_path[] = $this->type.$this->id . "." . $this->img_infos['type'];
        return join(DIRECTORY_SEPARATOR, $cache_path);
    }

    /**
     * Initialise le chemin du cache
     *
     * @param string $mode
     * @return void
     */
    private function init_cache_path(string $mode = "")
    {
        global $base_path, $database;

        $cache_path = $base_path . "/temp/cms_vign";
        if (!is_dir($cache_path)) {
            mkdir($cache_path);
        }
        if (!is_dir($cache_path . "/" . $database)) {
            mkdir($cache_path . "/" . $database);
        }
        if (!is_dir($cache_path . "/" . $database . "/" . $mode)) {
            mkdir($cache_path . "/" . $database . "/" . $mode);
        }
        return true;
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

    /**
     * Redimensionne l'image et l'affiche
     *
     * @param integer $size_x
     * @param integer $size_y
     * @return resource|\GdImage|false|void
     */
    protected function resize($size_x = 0, $size_y = 0)
    {
        if ($this->data) {
            if (!count($this->img_infos)) {
                $this->get_img_infos();
            }
            if (!$this->img_infos['render_fct']) {
                header('Content-Type: image/png');
                print file_get_contents(get_url_icon('vide.png'));
                return;
            }

            if (!$size_x && !$size_y) {
                header('Content-Type: ' . $this->img_infos['mimetype']);
                print $this->data;
                return;
            }

            $src_img = imagecreatefromstring($this->data);

            if (!$src_img) {
                header('Content-Type: image/png');
                print file_get_contents(get_url_icon('vide.png'));
                return;
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
                    // Si x plus petit que y et taille finale portrait
                    // Si le format final est plus large en proportion
                    if ($rs > $r) {
                        $new_h = $maxY;
                        $new_w = $new_h * $r;
                    } else {
                        $new_w = $maxX;
                        $new_h = $new_w / $r;
                    }
                } elseif (($r < 1) && ($rs >= 1)) {
                    // Si x plus petit que y et taille finale paysage
                    $new_h = $maxY;
                    $new_w = $new_h * $r;
                } elseif (($r > 1) && ($rs < 1)) {
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
            if (function_exists($this->img_infos['render_fct'])) {
                $render_params = array_merge([
                    $dst_img,
                    null,
                ], $this->img_infos['render_params']);
                header('Content-Type: ' . $this->img_infos['mimetype']);
                call_user_func_array($this->img_infos['render_fct'], $render_params);
            } else {
                header('Content-Type: image/png');
                print file_get_contents(get_url_icon('vide.png'));
            }
        } else {
            header('Content-Type: image/png');
            print file_get_contents(get_url_icon('vide.png'));
        }
    }

    public function get_vign_url($mode = "")
    {
        global $opac_url_base, $database;
        return $opac_url_base . "cms_vign.php?type=" . $this->type . "&id=" . $this->id . "&database=" . $database . "&mode=" . $mode;
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

    public function get_id()
    {
        return $this->id;
    }

    public function get_type()
    {
        return $this->type;
    }
}
