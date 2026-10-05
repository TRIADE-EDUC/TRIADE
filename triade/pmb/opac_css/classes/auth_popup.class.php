<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: auth_popup.class.php,v 1.16.2.2.2.1 2025/02/27 14:05:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
// authentification via "popup" à l'OPAC

global $base_path, $include_path, $msg, $charset;
global $empty_pwd, $ext_auth;
global $action;
global $callback_func;
global $callback_url, $new_tab;
global $popup_header;
global $opac_websubscribe_show, $opac_password_forgotten_show;

use Pmb\Authentication\Models\AuthenticationHandler;
require_once $include_path . "/empr.inc.php";
require_once $include_path . "/empr_func.inc.php";
require_once $include_path . "/h2o/pmb_h2o.inc.php";

class auth_popup
{

	public const MODE_ONLY_LOGIN = 1;

	protected $callback_func = "";
	protected $callback_url = "";
	protected $new_tab = false;
	protected $handle_ext_auth = false;
	private $mobile_app = 0;
	private $sess_id = 0;


	public function __construct()
	{
		global $base_path;
		if (file_exists($base_path . '/includes/ext_auth.inc.php')) {
			$this->handle_ext_auth = true;
		}
	}


	public function process()
	{
		global $base_path, $msg;
		global $empty_pwd, $ext_auth;

		global $action;
		global $callback_func;
		global $callback_url, $new_tab, $mobile_app;

		global $popup_header;

		$this->callback_func = $callback_func;
		$this->callback_url = $callback_url;
		$this->new_tab = $new_tab;
		$this->mobile_app = intval($mobile_app);

		switch ($action) {
			case 'check_auth':
				//On tente la connexion
				// si paramétrage authentification particulière
				$empty_pwd = true;
				$ext_auth = false;
				if ($this->handle_ext_auth) {
					require_once $base_path . '/includes/ext_auth.inc.php';
				}
				$log_ok = connexion_empr();
				print $popup_header;
				if ($log_ok) {
				    if ($this->mobile_app) {
				        $this->sess_id = generate_ws_sess_id();
				    }
					//réussie, on poursuit le tout...
					$this->success_callback();
				} else {
					print $this->get_form('', $msg['auth_failed']);
				}
				break;
			case 'get_form':
			default:
				print $popup_header;
				if (empty($_SESSION['user_code'])) {
					print $this->get_form();
				} else {
					$this->success_callback();
				}
				break;
		}
	}


	protected function success_callback()
	{
	    if ($this->mobile_app) {
	        if (empty($this->sess_id)) {
	            $this->sess_id = $_SESSION["ws_sess_id"];
	        }
	        print "<script>
                        const canal = new BroadcastChannel('mobile_app');
                        let sessId = '$this->sess_id';
                        console.log(canal);
                        console.log({'sessId' : sessId, 'login' : '".$_SESSION['user_code']."'});
                        canal.postMessage({'sessId' : sessId, 'login' : '".$_SESSION['user_code']."'});
                        if (sessId) {
                            window.close();
                        }
                </script>";
	        return;
	    }
	    
		if ($this->new_tab) {
	        print "
            <script>
                let idEmprSession = '".$_SESSION['id_empr_session']."';
                window.opener.postMessage({'idEmprSession' : idEmprSession}, '*');
                window.close();
            </script>";
	        return;
		}
		
		$html = "<script>";
		if ($this->callback_func) {
	        $html .= "window.parent.".$this->callback_func . "('" . $_SESSION['id_empr_session'] . "');";
		} else if ($this->callback_url) {
			$html .= "window.parent.document.location='" . $this->callback_url . "';";
		}
		
		$html .= "
			var frame = window.parent.document.getElementById('auth_popup');
            if (frame) {
    			frame.parentNode.removeChild(frame);
            }
		</script>";
		print $html;
	}


	protected function get_form($message = "", $error_message = "")
	{
		global $base_path, $include_path, $charset;
		global $opac_websubscribe_show, $opac_password_forgotten_show, $msg;
		global $popup_mode;

		if (!$message) {
			$message = $msg["need_auth"];
		}

		if(! isset($popup_mode)) {
			$popup_mode = 0;
		}

		$template_path = $include_path . '/templates/auth_popup.tpl.html';
		if (file_exists($include_path . '/templates/auth_popup_subst.tpl.html')) {
			$template_path = $include_path . '/templates/auth_popup_subst.tpl.html';
		}
		try {
			$H2o = H2o_collection::get_instance($template_path);
			$form = $H2o->render([
				'message' => $message,
			    'error_message' => $error_message,
				'callback_func' => $this->callback_func,
				'callback_url' => $this->callback_url,
				'new_tab' => $this->new_tab,
			    'mobile_app' => $this->mobile_app,
				'popup_mode' => $popup_mode,
			]);
		} catch (Exception $e) {
		    $form = '<blockquote id="askmdp">
		    <!-- ' . $e->getMessage() . ' -->
		    <div class="error_on_template" title="' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '">'
				. $msg["error_template"] .
				'</div>
            </blockquote>';
		}

		return $form;
	}
}
