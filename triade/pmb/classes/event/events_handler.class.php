<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: events_handler.class.php,v 1.11.2.1 2025/01/20 13:43:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * Gestion du système d'événements. (Singleton)
 * @author arenou
 *
 */
class events_handler {
    
    protected $hooks = [];
    protected $listener = [];
    protected $requires = [];
    
    private static $_instance = null;
    private $discovered = false;
    
    /**
     * Empeche l'instantiation sans passer par get_instance
     */
    private function __construct()
    {}
    
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new events_handler();
        }
        return self::$_instance;
    }
    
    /**
     * Getter
     */
    public function get_hooks()
    {
        return $this->hooks;
    }
    
    /**
     * Setter
     *
     * @param array $hooks
     * @return events_handler
     */
    public function set_hooks($hooks)
    {
        $this->hooks = $hooks;
        return $this;
    }
    
    /**
     * Getter
     */
    public function get_listener()
    {
        return $this->listener;
    }
    
    /**
     * Setter
     *
     * @param array $listener
     * @return events_handler
     */
    public function set_listener($listener)
    {
        $this->listener = $listener;
        return $this;
    }
    
    /**
     * Getter
     */
    public function get_requires()
    {
        return $this->requires;
    }
    
    /**
     * Envoi d'un événement (Déclenche le séquence de callback associée)
     *
     * @param event $event
     */
    public function send(event $event)
    {
        if (isset($this->listener[$event->get_type()]) && isset($this->listener[$event->get_type()][$event->get_sub_type()]) && is_array($this->listener[$event->get_type()][$event->get_sub_type()])) {
            $listeners = $this->listener[$event->get_type()][$event->get_sub_type()];
            for ($i = 0; $i < count($listeners); $i ++) {
                if (is_callable($listeners[$i])) {
                    call_user_func_array($listeners[$i], array(
                        $event
                    ));
                }
            }
        }
    }
    
    /**
     * Recherche et enregistre les Hooks du répertoire hooks
     */
    public function discover()
    {
        global $class_path;
        global $base_path;
        
        if (false === $this->discovered) {
            if (file_exists($class_path . '/event/hooks')) {
                $this->_recurse_discovery($class_path . '/event/hooks');
            }
            if (file_exists($base_path . '/plugins')) {
                $this->_recurse_discovery($base_path . '/plugins');
            }
        }
        $this->discovered = true;
    }
    
    /**
     *
     * @param string $path
     */
    private function _recurse_discovery($path, $depth = 1)
    {
    	if ($depth > 3) {
            return;
        }
        $dh = opendir($path);
        if (false === $dh) {
            return;
        }
        while (($file = readdir($dh)) !== false) {
            // On perd pas de temps sur le courant...
            if ($file == "." || $file == ".." || $file == "CVS" || $file == 'vendor') {
                continue;
            }
            // C'est une classe PMB
            if ((".class.php" == substr($file, - 10)) && (basename(dirname($path . '/' . $file)) == 'hooks')) {
                $this->register_hook($path . '/' . $file);
            }
            // C'est un autre répertoire.
            if (is_dir($path . '/' . $file)) {
            	$this->_recurse_discovery($path . '/' . $file, $depth+1);
            }
        }
        closedir($dh);
    }
    
    protected function register_hook($path)
    {
        global $base_path, $include_path, $class_path, $javascript_path, $styles_path;
        require_once $path;
        
        $classname = basename($path, ".class.php");
        if (class_exists($classname) && in_array('hook_interface', class_implements($classname))) {
            $this->requires = array_merge_recursive($this->requires, $classname::requires());
            $this->listener = array_merge_recursive($this->listener, $classname::get_subcriptions());
        }
    }
}
