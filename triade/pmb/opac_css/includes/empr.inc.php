<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr.inc.php,v 1.34 2023/10/17 09:01:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

use Pmb\Authentication\Models\AuthenticationHandler;

// Fonction qui génère le formulaire de connexion emprunteur
function genere_form_connexion_empr(){
    global $charset;
    global $opac_websubscribe_show, $opac_password_forgotten_show, $msg, $opac_rgaa_active;
    if (! empty($_SERVER['REQUEST_URI'])) {
        $action = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
        if (strpos($action, 'index.php?logout=1') !== false) {
            $action = 'index.php';
        } elseif (strpos($action, 'subscribe.php') !== false) {
            $action = 'empr.php';
        }
    } else {
        $action = "empr.php";
    }
    $loginform = "<form action='" . $action . "' method='post' name='myform' data-csrf='true'>";
    $loginform .= get_hidden_global_var('POST');
    if($opac_rgaa_active) {
        $loginform .= "<label>".$msg["common_tpl_cardnumber_default"]."</label><br />
        <input type='text' id='myform-login' name='login' class='login' size='14' placeholder='" . htmlentities($msg["common_tpl_cardnumber"], ENT_QUOTES, $charset) . "' title='" . htmlentities($msg["common_tpl_cardnumber"], ENT_QUOTES, $charset) . "' />
        <label for='myform-login' class='visually-hidden'>".$msg["common_tpl_cardnumber"]."</label>
        <br />
        <div class='myform-password-text-visually'>
            <input type='password' id='myform-password' name='password' class='password' size='8' placeholder='".htmlentities($msg["common_tpl_empr_password"], ENT_QUOTES, $charset)."' value='' title='" . htmlentities($msg["common_tpl_empr_password"], ENT_QUOTES, $charset) . "' />
            <label for='myform-password' class='visually-hidden'>".$msg["common_tpl_empr_password"]."</label>
            <button type='button' class='fa fa-eye' id='myform-password-visually' onclick='toggle_password(this, \"myform-password\");' title='".htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset)."'></button>
        </div>
        <input type='hidden' name='force_login' value='1'/>
        <input type='submit' name='ok' value='".$msg[11]."' class='bouton' />
    </form>";
    } else {
        $loginform .= "<label>".$msg["common_tpl_cardnumber_default"]."</label><br />
        <input type='text' name='login' class='login' size='14' placeholder='" . $msg["common_tpl_cardnumber"] . "' /><br />
        <div class='myform-password-text-visually'>
            <input type='password' id='myform-password' name='password' class='password' size='8' placeholder='".htmlentities($msg["common_tpl_empr_password"], ENT_QUOTES, $charset)."' value=''/>
            <button type='button' class='fa fa-eye' id='myform-password-visually' onclick='toggle_password(this, \"myform-password\");' title='".htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset)."'></button>
        </div>
        <input type='hidden' name='force_login' value='1'/>
        <input type='submit' name='ok' value='".$msg[11]."' class='bouton' />
    </form>";
    }

    if ($opac_password_forgotten_show) {
        $loginform .= "<a  class='mdp_forgotten' href='./askmdp.php'>" . $msg["mdp_forgotten"] . "</a>";
    }
    if ($opac_websubscribe_show) {
        $loginform .= "<br /><a class='subs_not_yet_subscriber' href='./subscribe.php'>" . $msg["subs_not_yet_subscriber"] . "</a>";
    }

    return $loginform ;
}

//Fonction qui génère les liens d'authentifications externes
function generate_external_authentication_form() {
    $ext_auth_form = '';
    $ext_auth_configs = AuthenticationHandler::getConfigs('opac');
    if(empty($ext_auth_configs)) {
        return $ext_auth_form;
    }
    $ext_auth_form = "<div id='external_authentication' >";
    foreach($ext_auth_configs as $config) {
        $ext_auth_form.= $config['params']['template'];
    }
    $ext_auth_form.= "</div>";
    return $ext_auth_form;
}


function genere_compte_empr(){
	global $msg, $empr_prenom, $empr_nom;
	$loginform ="<b class='logged_user_name'>".$empr_prenom." ".$empr_nom."</b><br />
				<a href=\"empr.php\" id=\"empr_my_account\">".$msg["empr_my_account"]."</a><br />
				<a href=\"index.php?logout=1\" id=\"empr_logout_lnk\">".$msg["empr_logout"]."</a>";
	return $loginform ;
}

function affichage_onglet_compte_empr(){
	global $msg;
	global $loginform ;
	if (!$_SESSION["user_code"]) {
		$info = genere_form_connexion_empr() ;
		$loginform = str_replace('<!-- common_tpl_login_invite -->', '<h3 class="login_invite">'.$msg['common_tpl_login_invite'].'</h3>', $loginform);
	} else {
		$loginform=str_replace('<!-- common_tpl_login_invite -->',$msg["empr_my_account"],$loginform);
		$info = genere_compte_empr() ;
	}
	return str_replace("!!login_form!!",$info,$loginform);
}





