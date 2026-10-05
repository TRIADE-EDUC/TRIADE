<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Controller.php,v 1.7 2024/01/31 12:47:17 qvarin Exp $

namespace Pmb\Common\Controller;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
	die("no access");
}

class Controller
{

    public $data;

    public function __construct(object $data = null)
    {
        if (empty($data)) {
            $this->data = new \stdClass();
        } else {
            $this->data = $data;
        }
    }

    /**
     * Retourne une erreur et on stop PHP
     *
     * @param string $message
     */
    protected function ajaxError(string $message, $code = null)
    {
        if (null !== $code) {
            http_response_code($code);
        }
    	ajax_http_send_response(\encoding_normalize::utf8_normalize([
    		"error" => true,
    		"errorMessage" => $message,
    	]));
    	exit;
    }

    /**
     * Retourne un JSON et on stop PHP
     *
     * @param mixed $data
     */
    protected function ajaxJsonResponse($data, $code = null)
    {
    	if (!is_array($data) && !is_object($data)) {
    		$data = [$data];
    	}
        if (null !== $code) {
            http_response_code($code);
        }
    	ajax_http_send_response(\encoding_normalize::utf8_normalize($data));
    	exit;
    }

    /**
     * Retourne une reponse et on stop PHP
     *
     * @param mixed $data
     * @param string $type
     */
    protected function ajaxResponse($data, string $type = 'text/html')
    {
    	ajax_http_send_response(\encoding_normalize::utf8_normalize($data), $type);
    	exit;
    }
}
