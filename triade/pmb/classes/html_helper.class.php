<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: html_helper.class.php,v 1.7.4.1 2025/05/12 15:05:28 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");


class HtmlHelper {


	protected static $instance = null;
	protected $is_initialized = false;

	protected $styles_path = '';
	protected $show_rtl = 0;
	protected $css_content = null;
	protected $common_disabled_files = [];


	/**
	 * Constructeur prive
	 *
	 */
	final private function __construct()
	{
	}


	/**
	 * Retourne une instance
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		if(is_null(static::$instance)) {
			static::$instance = new static();
		}

		static::$instance->getDependencies();
		return static::$instance;
	}


	/**
	 * Recupere le contexte d'execution
	 *
	 * @return void
	 */
	protected function getDependencies()
	{
		if(true === $this->is_initialized) {
			return;
		}

		global $styles_path;
		$this->styles_path = trim($styles_path);
		global $pmb_show_rtl;
		$this->show_rtl = $pmb_show_rtl;

		$this->is_initialized = true;
	}


	/**
	 * Lit les fichiers d'un repertoire en fonction de leur extension et les trie par ordre alpha
	 *
	 * @param string $dir : repertoire a lire avec / final
	 * @param string $extension : extension du fichier
	 *
	 * @return array|string[] : tableau de fichiers
	 */
	protected function readAndSortFiles(string $dir, string $extension)
	{
	    if (!is_dir($dir)) {
	        return [];
	    }

		$handle = @opendir($dir);
		$files = [];
		if(!$handle) {
			return [];
		}

		$l = strlen($extension);
		if($handle) {
			while( false !== ($item = readdir($handle)) ) {
				if( !in_array($dir.$item, $this->common_disabled_files) && is_file($dir.$item) && substr($item, -$l) == $extension ) {
					$files[] = $dir.$item;
				}
			}
			closedir($handle);
		}
		sort($files);
		return $files;
	}


	/**
	 * Retourne les feuilles de style et fichiers js associes a inclure
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	public function getStyle(string $style)
	{
		if( !is_null($this->css_content) ) {
			return $this->css_content;
		}

		$dir = __DIR__.'/../styles';
		if($this->styles_path) {
			$dir = $this->styles_path;
		}

		$this->css_content = "";

		// Lecture fichier disable dans le repertoire common
		if(is_file($dir.'/common/disable') && is_readable($dir.'/common/disable')) {
			$s = file_get_contents($dir.'/common/disable');
			$t = explode(PHP_EOL, $s);
			if(is_array($t)) {
				foreach($t as $v) {
					if($v) {
						$this->common_disabled_files[] = $dir.'/common/'.trim($v);
					}
				}
			}
		}

		// inclusion des feuilles de style communes
		$css_files = static::readAndSortFiles($dir.'/common/', '.css');
		foreach($css_files as $css_file){
			$time = @filemtime($css_file);
			$this->css_content.= PHP_EOL."<link rel='stylesheet' type='text/css' href='".$css_file."?".$time."' title='lefttoright' />";
		}

		// inclusion des fichiers js communs
		$js_files = static::readAndSortFiles($dir.'/common/javascript', '.js');
		foreach($js_files as $js_file) {
			$time = @filemtime($js_file);
			$this->css_content.= PHP_EOL."<script src='".$js_file."?".$time."' ></script>";
		}

		// inclusion des feuilles de style issues du style demande
		$css_files = static::readAndSortFiles($dir.'/'.$style.'/', '.css');
		foreach($css_files as $css_file){
			$time = @filemtime($css_file);
			$this->css_content.= PHP_EOL."<link rel='stylesheet' type='text/css' href='".$css_file."?".$time."' title='lefttoright' />";
		}

		// inclusion des fichiers js issus du style demande
		$js_files = static::readAndSortFiles($dir.'/'.$style.'/javascript/', '.js');
		foreach($js_files as $js_file) {
			$time = @filemtime($js_file);
			$this->css_content.= PHP_EOL."<script src='".$js_file."?".$time."' ></script>";
		}

		// Inclusion des feuilles de style RTL / LTR
		if ($this->show_rtl) {
			$css_files = static::readAndSortFiles($dir.'/'.$style.'/rtl', '.css');
			foreach($css_files as $css_file){
				$time = @filemtime($css_file);
				$this->css_content.= PHP_EOL."<link rel='stylesheet' type='text/css' href='".$css_file."?".$time."' title='lefttoright' />";
			}
		}

		return $this->css_content;

	}

    /**
     * Retourne les feuilles de style et fichiers js associes de l'opac pour l'envoi d'une dsi
     *
     * @param string $style
     *
     * @return string
     */
    public function getStyleOpac(string $style)
    {
        global $base_path, $opac_url_base;

        $opac_url_styles = $opac_url_base. "styles";
        $dir = $base_path."/opac_css/styles";
        $content = "";

        // Lecture fichier disable dans le repertoire common
        if(is_file($dir.'/common/disable') && is_readable($dir.'/common/disable')) {
            $s = file_get_contents($dir.'/common/disable');
            $t = explode(PHP_EOL, $s);
            if(is_array($t)) {
                foreach($t as $v) {
                    if($v) {
                        $this->common_disabled_files[] = $dir.'/common/'.trim($v);
                    }
                }
            }
        }

        // inclusion des feuilles de style communes
        $css_files = static::readAndSortFiles($dir.'/common/', '.css');
        foreach($css_files as $css_file){
            $time = @filemtime($css_file);
            $content.= PHP_EOL.'<link rel="stylesheet" type="text/css" href="' . $opac_url_styles . str_replace($dir, "", $css_file) . '?' . $time . '" />';
        }

        // inclusion des fichiers js communs
        $js_files = static::readAndSortFiles($dir.'/common/javascript', '.js');
        foreach($js_files as $js_file) {
            $time = @filemtime($js_file);
            $content.= PHP_EOL."<script src='" . $opac_url_styles . str_replace($dir, "", $js_file) . "?".$time."' ></script>";
        }

        // inclusion des feuilles de style issues du style demande
        $css_files = static::readAndSortFiles($dir.'/'.$style.'/', '.css');
        foreach($css_files as $css_file){
            $time = @filemtime($css_file);
            $content.= PHP_EOL.'<link rel="stylesheet" type="text/css" href="' . $opac_url_styles . str_replace($dir, "", $css_file) . '?'.$time.'" />';
        }

        // inclusion des fichiers js issus du style demande
        $js_files = static::readAndSortFiles($dir.'/'.$style.'/javascript/', '.js');
        foreach($js_files as $js_file) {
            $time = @filemtime($js_file);
            $content.= PHP_EOL."<script src='" . $opac_url_styles . str_replace($dir, "", $js_file) . "?".$time."' ></script>";
        }

        return $content;
    }
}

