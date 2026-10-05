<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_pnb.class.php,v 1.4 2021/03/26 11:46:49 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) 
	die ("no access");

global $class_path;

require_once $class_path . "/shorturl/shorturl_type.class.php";
require_once $class_path . "/pnb/pnb.class.php";


class shorturl_type_pnb extends shorturl_type
{

	/**
	 * Fonction de callback pour les retours anticipés de prêt
	 */
    protected function returnCallback()
    {
        $context = unserialize($this->context);
        $pnb = new pnb();
        $result = $pnb->return_book($context['empr_id'],$context['expl_id']);
        $response = encoding_normalize::json_decode($result, true);
        if($response['status'] == '1'){
            pmb_mysql_query('delete from shorturls where shorturl_hash = "'.addslashes($this->hash).'"');
            pmb_mysql_query('delete from shorturls where shorturl_hash = "'.addslashes($context['extend_hash']).'"');
        }
        print $result;
    }
    
    /**
     * Fonction de callback pour les prolongations de prêt
     */
    protected function extendCallback()
    {
        $context = unserialize($this->context);
        $pnb = new pnb();
        $result = $pnb->extend_loan($context['empr_id'],$context['expl_id']);
        encoding_normalize::json_decode($result);

        print $result;
    }

 
    public function generate_hash($action, $context = array())
    {
        if (method_exists($this, $action)) {
            $hash = self::create_hash('pnb', $action, $context);
        }
        return $hash;
    }
}