<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classLoader.class.php,v 1.4.2.2.2.2 2025/03/18 14:14:28 jparis Exp $

if ( isset($_SERVER['REQUEST_URI']) && stristr($_SERVER['REQUEST_URI'], ".class.php") ) {
    die("no access");
}

global $base_path;
global $class_path;
global $include_path;
global $javascript_path;
global $styles_path;
global $msg, $charset;
global $current_module;

class classLoader
{
    protected static $instance = null;

    protected $paths = [];
    protected $duplicates = [];

    protected $base_dir = __DIR__ . "/../..";
    protected $tmp_paths_file = __DIR__ . "/../../temp/classLoader_paths.php";
    protected $tmp_duplicates_file = __DIR__ . "/../../temp/classLoader_duplicates.php";

    protected $excluded_dirs = [
        ".",
        "..",
        "CVS",
        "temp",
        "vendor",
        "images",
        "marc_tables",
        "messages",
        "mime_types",
        "opac_css",
        "Pmb",
        "styles",
        "sounds",
        "zserver",
        "escpos-php-development",
        "devel",
        "h2o",
        "doc",
    ];

    protected $excluded_classes = [
        'es_proxy',
        'serialcirc_subst',
    ];

    protected $log = false;
    protected $log_file = __DIR__."/../../temp/classLoader.log";
    protected $start_time = 0;
    protected $end_time = 0;

    protected $lock_file = __DIR__."/../../temp/classLoader.lock";
    protected $lock_ttl = 10;
    protected $rebuild_on_failure = 1;

    /**
     * Retourne une instance de classLoader
     *
     * @param bool $log : Enregistrement journal
     *
     * @return classLoader
     */
    public static function getInstance($log = false)
    {
        $log = boolval($log);

        if( is_null(static::$instance) ) {
            static::$instance = new static($log);
        }
        return static::$instance;
    }

    /**
     * Constructeur
     *
     * @param bool $log : Enregistre un journal
     *
     * @return void
     */
    final protected function __construct($log = false)
    {
        $paths = [];
        $this->log = $log;
        $this->log_start(__METHOD__);
        $this->base_dir = realpath($this->base_dir);
        $this->log('base_dir = '.$this->base_dir);

        //Si le fichier est en cours de construction il y a un verrou
        //on attend un peu et on le supprime
        if( file_exists($this->lock_file) ) {
            sleep($this->lock_ttl);
            @unlink($this->lock_file);
        }

        //Chargement du fichier contenant le tableau classe => chemin
        if (is_readable($this->tmp_paths_file)) {
            require_once $this->tmp_paths_file;
        }

        if ( !empty($paths) && is_array($paths) ) {
            $this->paths = $paths;
            $this->log_end();
            return;
        }

        if (empty($this->paths)) {
            $this->build();
        }
        $this->log_end();
    }

    /**
     * Construction / Reconstruction du fichier contenant le tableau classe => chemin
     *
     * @return void
     */
    public function build()
    {
        touch($this->lock_file);
        $this->buildPaths();
        $this->savePaths();
        @unlink($this->lock_file);
    }

    /**
     * Construit un tableau classe => chemin
     *
     * @return void
     */
    protected function buildPaths()
    {
        $this->log_start(__METHOD__);
        $this->paths = [];
        $this->buildSubPaths($this->base_dir);
    }

    protected function buildSubPaths(string $dir)
    {
        $this->log_start(__METHOD__);
        $fdir = opendir($dir);
        if (false === $fdir) {
            return;
        }

        $rel_dir = str_replace($this->base_dir, '', $dir);
        $this->log("dir = ".$rel_dir);

        while (false !== ($file = readdir($fdir))) {

            if( '.class.php' == substr($file, -10) ) {

                $this->log("file = ".$rel_dir."/".$file);

                $tmp = file_get_contents($dir."/".$file);

                //namespace
                $matches = [];
                $namespace = '';
                preg_match_all('/^\s*namespace\s+([a-z|A-Z|0-9|\\\\]+)\s*;/im', $tmp, $matches, PREG_SET_ORDER, 0);
                if(count($matches)) {
                    $namespace = $matches[0][1];
                    $this->log("namespace = ".$namespace. " >> ignored.");
                }
                //S'il y a un namespace, c'est dans le composer. pas besoin d'autoload ici.
                if(!$namespace) {
                    //classes
                    $matches = [];
                    $classes = [];
                    preg_match_all('/^\s*(?:abstract\s+){0,1}(?:final\s+){0,1}class\s+(\w+)(?:\s+extends\s+\w+){0,1}(?:\s+implements.+){0,1}\s*\{/im', $tmp, $matches, PREG_SET_ORDER, 0);

                    //var_dump($dir."/".$file);
                    if(count($matches)) {
                        foreach($matches as $match) {
                            $classes[] = $match[1];
                        }
                    }

                    //interfaces
                    $matches_interface = [];
                    preg_match_all('/^\s*interface\s+(\w+)\s*\{/im', $tmp, $matches_interface, PREG_SET_ORDER, 0);
                    if(count($matches_interface)) {
                        foreach($matches_interface as $match) {
                            $classes[] = $match[1];
                        }
                    }

                    //var_dump($classes);
                    if(count($classes)) {
                        foreach($classes as $class) {

                            $this->log("class = ".$class);
                            //La classe n'est pas repertoriee
                            if(!isset($this->paths[$class]) ) {
                                $this->paths[$class] = $rel_dir."/".$file;
                                //La classe est deja repertoriee
                            } else {
                                $this->log("ERROR >> class with same name already exists.");
                                if(!isset($this->duplicates[$class])) {
                                    $this->duplicates[$class]['nb'] = 2;
                                    $this->duplicates[$class]['path'][] = $this->paths[$class];
                                } else {
                                    $this->duplicates[$class]['nb'] +=1;
                                }
                                $this->duplicates[$class]['path'][] = $rel_dir."/".$file;
                            }
                        }
                    }
                }
            }

            if ( ('.' != substr($file,0,1)) && is_dir($dir."/".$file) && !in_array($file, $this->excluded_dirs) )  {
                $this->buildSubPaths($dir . "/" . $file);
            }
        }

        closedir($fdir);
    }

    /**
     * Formate et enregistre le tableau classe => chemin
     *
     * @return void
     */
    protected function savePaths()
    {
        $this->log_start(__METHOD__);

        //Suppression des doublons des classes autoloadables
        if( count($this->duplicates) ) {
            foreach( $this->duplicates as $k=>$v) {
                unset($this->paths[$k]);
            }
        }
        //Suppression des classes generees logiciellement
        if( count($this->excluded_classes) ) {
            foreach( $this->excluded_classes as $v) {
                unset($this->paths[$v]);
            }
        }
        asort($this->paths);
        $content = '<?php' . PHP_EOL . '$paths = [' . PHP_EOL;
        foreach ($this->paths as $k=>$v) {
            $content .= "'".$k."' => '".$v."'," . PHP_EOL;
        }
        $content .= '];';
        file_put_contents($this->tmp_paths_file, $content);

        $content = '<?php' . PHP_EOL . '$paths = [' . PHP_EOL;
        foreach ($this->duplicates as $k=>$v) {
            $content .= "\t'".$k."' => [". PHP_EOL;
            $content.= "\t\t'nb' => '".$v['nb']."'," . PHP_EOL;
            $content.= "\t\t'paths' => [" . PHP_EOL;
            foreach($v['path'] as $path) {
                $content.= "\t\t\t'".$path."',".PHP_EOL;
            }
            $content.= "\t\t]," . PHP_EOL;
            $content.= "\t],".PHP_EOL;
        }
        $content .= '];';
        file_put_contents($this->tmp_duplicates_file, $content);
    }

    /**
     * Ajoute l'autoloader a la liste des autoloaders enregistres
     *
     * @param bool $throws : Transmet les exceptions
     * @param bool $prepend : Ajoute en debut de liste
     *
     * @return void
     */
    public function register(bool $throws = true, bool $prepend = false)
    {
        spl_autoload_register([
            $this,
            'load'
        ],$throws, $prepend);
    }

    /**
     * Retire l'autoloader de la liste des autoloaders enregistres
     *
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister([$this, 'load']);
    }

    /**
     * Cherche et charge la classe demandee
     *
     * @param string $class
     *
     * @return void
     */
    protected function load(string $class = '')
    {
        $this->log_start(__METHOD__);
        $this->log('class = '.$class);

        if(!$class || substr($class, 0, 4) == "Pmb\\") {
            $this->log_end();
            return;
        }

        if(empty($this->paths[$class])) {

            //Tentative de reconstruction sauf sur appel depuis class_exists
            $trace = debug_backtrace();

            // Attention la trace change en PHP 8
            if(PHP_MAJOR_VERSION >= 8) {
                $caller = ($trace[1]['function']) ?? '';
            } else {
                $caller = ($trace[2]['function']) ?? '';
            }

            if('class_exists' == $caller || empty($caller)) {
                $this->log_end();
                return;
            }

            if($this->rebuild_on_failure) {
                $this->log("INFO >> Rebuild");
                $this->build();
                $this->rebuild_on_failure --;
                if(empty($this->paths[$class])) {
                    if(class_exists('PHP_log', false)) {
                        PHP_log::register(PHP_log::prepare_error('classLoader >> Rebuild'), "No file for class : ".$class);
                    }
                }
            }
            if(empty($this->paths[$class])) {
                $this->log("ERROR >> No file for class : ".$class);
                $this->log_end();
                return;
            }
        }
        $this->requireFile($this->base_dir.$this->paths[$class]);

        //Chargement template associe a une classe
        //N'a rien a faire ici, a externaliser dans la classe correspondante
        $tokens = explode("/", $this->paths[$class]);
        if($tokens[0] == 'classes') {
            $tpl_file = str_replace('.class.php', '.tpl.php', $this->paths[$class]);
            $tpl_file = str_replace('/classes/', '/includes/templates/', $this->paths[$class]);
            $this->requireFile($tpl_file);
        }
        $this->log_end();
        return;
    }

    /**
     * Charge un fichier s'il existe
     *
     * @param string $file : Le fichier a charger.
     * @return bool : true si le fichier existe, false sinon.
     */
    protected function requireFile(string $file): bool
    {
        global $base_path;
        global $class_path;
        global $include_path;
        global $javascript_path;
        global $styles_path;
        global $msg, $charset;
        global $current_module;

        if (is_readable($file)) {
            $this->log('file = '.$file);
            require_once $file;
            return true;
        } else {
            $this->log("ERROR >> file '".$file."' is not readable");
        }
        return false;
    }

    /**
     * Journalisation
     */
    protected function log(string $data)
    {
        if(!$this->log) {
            return;
        }
        if('' === $data) {
            return;
        }
        file_put_contents($this->log_file, $data.PHP_EOL, FILE_APPEND);
    }

    /**
     * Debut de journalisation
     *
     * @param string $method : methode
     */
    protected function log_start(string $method)
    {
        if(!$this->log) {
            return;
        }
        $this->start_time = hrtime(true);
        $this->log(">> ".date('Y-m-d H:i:s')."  ".microtime(true));
        $this->log($method);

    }

    /**
     * Fin de journalisation
     */
    protected function log_end()
    {
        if(!$this->log) {
            return;
        }
        $this->end_time = hrtime(true);
        $this->log((($this->end_time - $this->start_time)/1000000)." ms".PHP_EOL);
    }

}

