<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_artecampus_view_artecampus.class.php,v 1.1.2.3.2.1 2025/02/27 14:05:54 dgoron Exp $

use Pmb\Common\Orm\EmprOrm;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_artecampus_view_artecampus extends cms_module_common_view_django
{
    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->default_template = '
<div class="artecampus">
    <img
        src="./images/connecteurs/artecampus.svg"
        alt="{{ msg.artecampus }}"
    />
    {% if !session_vars.id_empr %}
        {{ connector.button_connection }}
    {% else %}
        {{ connector.access_link }}
    {% endif %}
</div>';
    }

    /**
     * Renvoie les structures de données du module
     *
     * @return array
     */
    public function get_format_data_structure()
    {
        return array_merge(
            parent::get_format_data_structure(),
            [
                [
                    'var' => 'connector',
                    'desc' => $this->msg['connector_vars_view_desc'],
                    'children' => [
                        [
                            'var' => 'connector.url',
                            'desc' => $this->msg['connector_url_vars_view_desc'],
                        ],
                        [
                            'var' => 'connector.empr_data',
                            'desc' => $this->msg['connector_empr_data_vars_view_desc'],
                        ],
                        [
                            'var' => 'connector.button_connection',
                            'desc' => $this->msg['connector_button_connection_vars_view_desc'],
                        ],
                        [
                            'var' => 'connector.access_link',
                            'desc' => $this->msg['connector_access_link_vars_view_desc'],
                        ]
                    ]
                ]
            ]
        );
    }

    protected function get_button_connection()
    {
        global $msg, $charset;
        
        return '
        <input
            class="bouton" type="button"
            onclick="auth_popup(\'./ajax.php?module=ajax&categ=auth&callback_func=artecampus_callback_auth\', false, \''.htmlentities($msg['artecampus_access'], ENT_QUOTES, $charset).'\')"
            value="'.htmlentities($msg['artecampus_empr_login'], ENT_QUOTES, $charset).'"
            aria-label="'.htmlentities($msg['artecampus_empr_login_aria_label'], ENT_QUOTES, $charset).'"
        />
        <script>
            if (typeof artecampus_callback_auth === "undefined") {
                function artecampus_callback_auth(id_empr) {
                    window.location.reload();
                }
            }
        </script>';
    }
    
    protected function get_access_link($url, $empr_data)
    {
        global $msg, $charset;
        
        return '
        <input
            class="bouton" type="submit"
            form="'.$this->id.'_artecampus_form"
            value="'.htmlentities($msg['artecampus_see'], ENT_QUOTES, $charset).'"
            aria-label="'.htmlentities($msg['artecampus_see'], ENT_QUOTES, $charset).'"
        />
        <form
            id="'.$this->id.'_artecampus_form"
            action="'.$url.'"
            target="_blank"
            method="post"
            aria-describedby="'.htmlentities($msg['artecampus_see_more']." ".$msg['newtab'], ENT_QUOTES, $charset).'"
        >
            <input type="hidden" name="data" value="'.$empr_data.'" />
        </form>';
    }
    
    /**
     * Rendu du module
     *
     * @param false|array{connector: int} $data
     * @return string
     */
	public function render($data)
    {
        global $charset;
        
        if (false === $data || empty($data['connector'])) {
            return '';
        }

        try {
            if (!empty($_SESSION['id_empr_session'])) {
                $empr = new EmprOrm($_SESSION['id_empr_session']);
                $emails = explode(';', $empr->empr_mail);
                $email = $emails[0] ?? '';

                if (empty($email)) {
                    throw new Exception('Email not found');
                }

                $connector = new artecampus();
                $hmac = $connector->generate_hmac($email, $data['connector']);
                $payload = $connector->generate_data($empr, $data['connector']);
            } else {
                $hmac = '';
                $payload = [];
            }
        } catch (Exception $e) {
		    $html = '<!-- '.$e->getMessage().' -->';
		    $html .= '<div class="error_on_template" title="' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '">';
		    $html .= $this->msg["cms_module_common_view_error_template"];
		    $html .= '</div>';
            return $html;
        }
        $url = artecampus::LOGIN_URL . '?' . http_build_query(['hmac' => $hmac]);
        $empr_data = htmlentities(encoding_normalize::json_encode($payload), ENT_QUOTES, $charset);
	    return parent::render([
            'connector' => [
                'url' => $url,
                'empr_data' => $empr_data,
                'button_connection' => $this->get_button_connection(),
                'access_link' => $this->get_access_link($url, $empr_data)
            ]
        ]);
    }
}
