<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: c3rb.class.php,v 1.2.4.2 2025/03/25 10:21:09 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path,$base_path;
require_once $base_path."/admin/connecteurs/in/oai/oai.class.php" ;
require_once $class_path."/sessions_tokens.class.php";

class c3rb extends oai {

    protected const XSLT_PATH = __DIR__.'/xslt';

    /**
     * tokens sha256 deja calcules
     * @var array
     */
    static protected $tokens = [];

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "c3rb";
    }

    /**
     *
     * {@inheritdoc}
     * @see connector::get_messages($connector_path)
     */
    public function get_messages($connector_path)
    {
        global $lang;

        $oai_file_name = '';
        if (file_exists($connector_path . "/../oai/messages/" . $lang . ".xml")) {
            $oai_file_name = $connector_path . "/../oai/messages/" . $lang . ".xml";
        } else if (file_exists($connector_path . "/../oai/messages/fr_FR.xml")) {
            $oai_file_name = $connector_path . "/../oai/messages/fr_FR.xml";
        }

        $file_name = '';
        if (file_exists($connector_path . "/messages/" . $lang . ".xml")) {
            $file_name = $connector_path . "/messages/" . $lang . ".xml";
        } else if (file_exists($connector_path . "/messages/fr_FR.xml")) {
            $file_name = $connector_path . "/messages/fr_FR.xml";
        }

        if ($oai_file_name) {
            $xmllist = new XMLlist($oai_file_name);
            $xmllist->analyser();
            $this->msg = $xmllist->table;
        }
        if ($file_name) {
            $xmllist = new XMLlist($file_name);
            $xmllist->analyser();
            $this->msg += $xmllist->table;
        }
    }

    /**
     *
     * @param string $recid
     * @param array $params
     *
     * @return string
     */
    public static function get_resource_link($ref, $params=[]) {

        if(empty($params['link'])) {
            return '';
        }
        if(empty($ref) || empty($params) || empty($params['source_id']) || empty($params['empr_id']) ) {
            return $params['link'];
        }

        $conn = new static();
        $source_params = $conn->unserialize_source_params($params['source_id']);

        $c3rb_authentication_source_id = $source_params['PARAMETERS']['c3rb_authentication_source_id'] ?? 0;
        if(empty($c3rb_authentication_source_id)) {
            return $params['link'];
        }
        $c3rb_authentication_source = $conn::get_authentication_source_by_id($c3rb_authentication_source_id);
        $c3rb_sso_endpoint = $c3rb_authentication_source['c3rb_sso_endpoint'] ?? '';
        $c3rb_shared_key = $c3rb_authentication_source['c3rb_shared_key'] ?? '';
        if(empty($c3rb_sso_endpoint) || empty($c3rb_shared_key)) {
            return $params['link'];
        }

        $sessions_tokens = new sessions_tokens('c3rb');
        $sessions_tokens->set_SESSID($_COOKIE["PmbOpac-SESSID"]);
        $c3rb_sessionid = $sessions_tokens->get_token();

        if(!$c3rb_sessionid) {
            $c3rb_sessionid = $sessions_tokens->generate_token_from_arguments([uniqid('c3rb')]);
        }
        if( empty(static::$tokens[$c3rb_sessionid]) )  {
            static::$tokens[$c3rb_sessionid] = hash('sha256', $c3rb_shared_key.$params['empr_id'].$c3rb_sessionid);
        }

        $link = $c3rb_sso_endpoint . '&userid='.$params['empr_id'].'&sessionid='.$c3rb_sessionid.'&token='.static::$tokens[$c3rb_sessionid];
        $link.= '&backurl='.base64_encode($params['link']);

        return $link;
    }


}