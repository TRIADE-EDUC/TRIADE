<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.10.8.2 2025/04/18 08:28:01 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

switch($categ) {
    case "document" :
        //Mise en cache des images
        //on ajoute des entêtes qui autorisent le navigateur à faire du cache...
        $headers = getallheaders();
        //une journée
        $offset = 60 * 60 * 24 ;
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) <= time())) {
            header('Last-Modified: '.$headers['If-Modified-Since'], true, 304);
            return;
        } else {
            header('Expired: '.gmdate("D, d M Y H:i:s", time() + $offset).' GMT', true);
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);
        }
        $doc = new cms_document($id);
        switch($action) {
            case "thumbnail":
                $doc->render_thumbnail();
                break;

            case "render":
                global $mode;

                if ($doc->get_num_storage()) {
                    generate_log('ajax_cms_document_render', [], true);
                    session_write_close();
                    $doc->render_doc($mode);
                }
                break;

            default:
                http_response_code(404);
                break;
        }
        break;
    case "module" :
        switch($action) {
            case "ajax":
                $element = new $elem($id);
                $response = $element->execute_ajax();
                ajax_http_send_response($response['content'], $response['content-type']);
                break;

            case "css":
            case "js":
                session_write_close();
                $element = new $elem($id);
                $response = $element->execute_ajax();
                ajax_http_send_response($response['content'], $response['content-type']);
                break;

            default:
                http_response_code(404);
                break;
        }
        break;
    case "build" :
        switch($action) {

            case "set_version":
                $_SESSION["build_id_version"] = $value;
                ajax_http_send_response("ok ". htmlentities($_SESSION["build_id_version"], ENT_QUOTES));
                break;

            default:
                http_response_code(404);
                break;
        }
        break;

    default:
        http_response_code(404);
        break;

}
