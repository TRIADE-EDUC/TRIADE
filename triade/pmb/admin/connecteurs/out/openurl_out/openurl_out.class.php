<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: openurl_out.class.php,v 1.6.8.1 2025/03/04 10:45:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;

require_once $class_path . "/connecteurs_out.class.php";
require_once $class_path . "/openurl/transport/openurl_transport.class.php";

class openurl_out extends connecteur_out
{

    public function get_config_form()
    {
        $result = $this->msg["openurl_no_configuration_required"];
        return $result;
    }

    public function update_config_from_form()
    {
        return;
    }

    public function instantiate_source_class($source_id)
    {
        return new openurl_out_source($this, $source_id, $this->msg);
    }

    public function need_global_messages()
    {
        return false;
    }

    public function process($source_id, $pmb_user_id)
    {
        global $base_path;

        foreach ($this->sources as $source) {
            if ($source->id == $source_id) {
                if ($source->config['mode'] == "requeteur") {
                    $str = file_get_contents("php://input");
                    if (! $str) {
                        $str = str_replace('source_id=' . $source_id . '&', "", $_SERVER['QUERY_STRING']);
                    }
                    openurl_transport::staticUnserialize($str);
                } else {
                    foreach ($_POST as $key => $value) {
                        global ${$key};
                        ${$key} = $value;
                    }
                    foreach ($_GET as $key => $value) {
                        global ${$key};
                        ${$key} = $value;
                    }
                    require $base_path . "/admin/connecteurs/in/openurl/openurl.class.php";
                    $conn = new openurl("openurl");
                    header('Content-type: text/txt');
                    print $conn->getByRefContent($in_id, $notice_id, $uri, $entity);
                }
            }
        }
        return;
    }
}

class openurl_out_source extends connecteur_out_source
{

    public function __construct($connector, $id, $msg)
    {
        parent::__construct($connector, $id, $msg);
    }

    public function get_config_form()
    {
        global $pmb_url_base;
        $result = parent::get_config_form();

        // Adresse d'utilisation
        $result .= "
        <div class=row>
            <label class='etiquette'>" . $this->msg['openurl_service_endpoint'] . "</label><br />";
        if ($this->id) {
            $result .= "<a target='_blank' href='" . $pmb_url_base . "ws/connector_out.php?source_id=" . $this->id . "'>" . $pmb_url_base . "ws/connector_out.php?source_id=" . $this->id . "</a>";
        } else {
            $result .= $this->msg["openurl_service_endpoint_unrecorded"];
        }
        $result .= "
        </div>
        <div class='row'>&nbsp;</div>
        <div class='row'>
            <div class='colonne3'>
                <label for='mode'>" . $this->msg['openurl_mode'] . "</label>
            </div>
            <div class='colonne-suite'>
                <span>" . $this->msg['openurl_mode_requeteur'] . "&nbsp;<input type='radio' name='mode' value='requeteur' " . (isset($this->config["mode"]) && $this->config["mode"] == "requeteur" ? "checked='checked' " : "") . "style='vertical-align:bottom;' /></span>
                <span>" . $this->msg['openurl_mode_byref'] . "&nbsp;<input type='radio' name='mode' value='byref' " . (isset($this->config["mode"]) && $this->config["mode"] == "byref" ? "checked='checked' " : "") . "style='vertical-align:bottom;' /></span>
            </div>
            <div class='colonne3'>
                <label for='serialization'>" . $this->msg['openurl_serialization'] . "</label>
            </div>
            <div class='colonne-suite'>
                <span>" . $this->msg['openurl_serialization_kev'] . "&nbsp;<input type='radio' name='serialization' value='kev' " . (isset($this->config["serialization"]) && $this->config["serialization"] == "kev" ? "checked='checked' " : "") . "style='vertical-align:bottom;' /></span>
                <span>" . $this->msg['openurl_serialization_xml'] . "&nbsp;<input type='radio' name='serialization' value='xml' " . (isset($this->config["serialization"]) && $this->config["serialization"] == "xml" ? "checked='checked' " : "") . "style='vertical-align:bottom;' /></span>
            </div>
        </div>";

        return $result;
    }

    public function update_config_from_form()
    {
        parent::update_config_from_form();
        global $serialization;
        global $mode;
        $this->config = array();
        $this->config["serialization"] = $serialization;
        $this->config["mode"] = $mode;

        return;
	}

}
