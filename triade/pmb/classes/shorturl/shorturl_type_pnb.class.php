<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_pnb.class.php,v 1.3 2021/01/27 14:38:24 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;

require_once $class_path . "/shorturl/shorturl_type.class.php";

class shorturl_type_pnb extends shorturl_type
{

	/**
	 * Fonction de callback pour les retours anticipés de prêt
	 * Sert à la création du hash en gestion
	 * Appelée uniquement depuis l'OPAC
	 */
    protected function returnCallback()
    {
	     return;
    }

    
    /**
     * Fonction de callback pour les prolongations de prêt
     * Sert à la création du hash en gestion
     * Appelée uniquement depuis l'OPAC
     */
    protected function extendCallback()
    {
    	return;
    }
    
 
    public function generate_hash($action, $context = array())
    {
        if (method_exists(self::class, $action)) {
            $hash = self::create_hash('pnb', $action, $context);
        }
        return $hash;
    }
}