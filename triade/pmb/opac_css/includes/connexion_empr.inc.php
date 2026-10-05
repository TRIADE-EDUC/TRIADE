<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connexion_empr.inc.php,v 1.26.2.3 2024/09/24 09:59:33 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path;

require_once $include_path."/empr.inc.php";

function do_formulaire_connexion() 
{
	global $msg,$charset ;
	global $lvl;
	global $id_notice, $id_bulletin ;
	global $todo, $noticeid;
	global $bannette_abon;
	global $opac_websubscribe_show, $opac_password_forgotten_show;

	//Validation des entrees
	$id_notice = (empty($id_notice)) ? 0 : intval($id_notice);
	$id_bulletin = (empty($id_bulletin)) ? 0 : intval($id_bulletin);
	$noticeid = (empty($noticeid)) ? 0 : intval($noticeid);
	$todo = (empty($todo) || ('liste' != $todo)) ? '' : $todo ;
	$bannette_abon = (empty($bannette_abon) || !is_array($bannette_abon) ) ? [] : $bannette_abon;
	if(!empty($bannette_abon)) {
	    $tmp = [];
	    foreach($bannette_abon as $k => $v) {
	        if(intval($k)) {
	            $tmp[$k] = $v;
	        }
	    }
	    $bannette_abon = $tmp;
	    unset($tmp);
	}
	
	$loginform_forgotten = '';
	if($opac_password_forgotten_show) {
		$loginform_forgotten = "&nbsp;<a class='ask-password' href='askmdp.php' onclick=\"document.forms['loginform'].action='askmdp.php';document.forms['loginform'].submit();\">" . htmlentities($msg['mdp_forgotten'],ENT_QUOTES,$charset) . "</a>";
	}

	switch ($lvl) {
		case ('resa_planning') :
			$loginform ="
				<div class='popup_connexion_empr'>
					<h3>".$msg['resa_doit_etre_abon']."</h3>
						<blockquote role='presentation'>
							<form action='do_resa.php' method='post' name='loginform' data-csrf='true'>
							<label for='empr_login'>".$msg['resa_empr_login']."</label>
							<input id='empr_login' type='text' name='login' autocomplete='username' size='20' border='0' placeholder=\"".$msg['common_tpl_cardnumber']."\"/>
							<label for='empr_pwd'>".$msg['resa_empr_password']."</label>
							<div class='password-text-visually'>
            				    <input id='empr_pwd' type='password' name='password' autocomplete='current-password' size='20' border='0' value='' />
								<button type='button' role='switch' aria-checked='false' class='fa fa-eye' id='password_visually' onclick='toggle_password(this, \"empr_pwd\");' title='".htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset)."'>
									<span class='visually-hidden'>" . htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset) . "</span>
								</button>
                            </div>
							<input type='hidden' name='id_notice' value='$id_notice' />
							<input type='hidden' name='id_bulletin' value='$id_bulletin' />
							<input type='hidden' name='lvl' value='resa_planning' />
							<input type='hidden' name='connectmode' value='popup' />
							<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton' /> $loginform_forgotten ";

			if($opac_websubscribe_show==2){
				$loginform.="&nbsp;<input type='button' class='bouton' onclick=\"document.forms['loginform'].action='subscribe.php';document.forms['loginform'].submit();\" value='".htmlentities($msg['websubscribe_label'],ENT_QUOTES,$charset)."' />";
			}
			$loginform.="
				</form>
				</blockquote>
				</div>";
			break;

		case ('avis_add') :
		case ('avis_liste') :
		case ('avis_save') :
		case ('avis_') :
			$loginform ="
				<div class='popup_connexion_empr'>
					<h3>".$msg['avis_doit_etre_abon']."</h3>
						<blockquote role='presentation'>
						<form action='avis.php' method='post' name='loginform' data-csrf='true'>
							<label for='empr_login'>".$msg['sugg_empr_login']."</label>
							<input id='empr_login' type='text' name='login' autocomplete='username' size='20' border='0' placeholder=\"".$msg['common_tpl_cardnumber']."\" >
							<label for='empr_pwd'>".$msg['sugg_empr_password']."</label>
							<div class='password-text-visually'>
            				    <input id='empr_pwd' type='password' name='password' autocomplete='current-password' size='20' border='0' value='' />
								<button type='button' role='switch' aria-checked='false' class='fa fa-eye' id='password_visually' onclick='toggle_password(this, \"empr_pwd\");' title='".htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset)."'>
									<span class='visually-hidden'>" . htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset) . "</span>
								</button>
                            </div>
							<input type='hidden' name='lvl' value='$lvl' >
							<input type='hidden' name='todo' value='$todo' >
							<input type='hidden' name='noticeid' value='$noticeid' >
							<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton'>
						</form>
					</blockquote>
				</div>";
			break;

		case ('tags') :
		    $loginform ="
				<div class='popup_connexion_empr'>
					<h3>".$msg['tag_doit_etre_abon']."</h3>
					<blockquote role='presentation'>
						<form action='addtags.php' method='post' name='loginform' data-csrf='true'>
							<label for='empr_login'>".$msg['sugg_empr_login']."</label>
							<input id='empr_login' type='text' name='login' size='20' autocomplete='username' border='0' placeholder=\"".$msg['common_tpl_cardnumber']."\" >
							<label for='empr_pwd'>".$msg['sugg_empr_password']."</label>
							<div class='password-text-visually'>
            				    <input id='empr_pwd' type='password' name='password' autocomplete='current-password' size='20' border='0' value='' />
								<button type='button' role='switch' aria-checked='false' class='fa fa-eye' id='password_visually' onclick='toggle_password(this, \"empr_pwd\");' title='".htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset)."'>
									<span class='visually-hidden'>" . htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset) . "</span>
								</button>
                            </div>
							<input type='hidden' name='lvl' value='$lvl' >
							<input type='hidden' name='noticeid' value='$noticeid' >
							<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton'>
						</form>
					</blockquote>
				</div>";
			break;

		case ('make_sugg') :
			$loginform ="
				<div class='popup_connexion_empr'>
				<h3>".$msg['sugg_doit_etre_abon']."</h3>
					<blockquote role='presentation'>
						<form action='do_resa.php' method='post' name='loginform' data-csrf='true'>
							<label for='empr_login'>".$msg['sugg_empr_login']."</label>
							<input id='empr_login' type='text' name='login' size='20' autocomplete='username' border='0' placeholder=\"".$msg['common_tpl_cardnumber']."\" >
							<label for='empr_pwd'>".$msg['sugg_empr_password']."</label>
                            <div class='password-text-visually'>
            				    <input id='empr_pwd' type='password' name='password' autocomplete='current-password' size='20' border='0' value=''  />
								<button type='button' role='switch' aria-checked='false' class='fa fa-eye' id='password_visually' onclick='toggle_password(this, \"empr_pwd\");' title='".htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset)."'>
									<span class='visually-hidden'>" . htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset) . "</span>
								</button>
                            </div>
							<input type='hidden' name='lvl' value='make_sugg' >
							<input type='hidden' name='connectmode' value='popup' >
							<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton'>
						</form>
					</blockquote>
				</div>";
			break;
			
		//abonnement à une bannette
		case "bannette_gerer":
			$loginform ="
				<div class='popup_connexion_empr'>
					<h3>".$msg['bannette_doit_etre_abon']."</h3>
					<blockquote role='presentation'>
						<form action='./empr.php?tab=dsi&lvl=bannette_gerer' method='post' name='bannette_gerer' data-csrf='true'>
							<label for='empr_login'>".$msg['resa_empr_login']."</label>
							<input id='empr_login' type='text' name='login' autocomplete='username' size='20' border='0' placeholder=\"".$msg['common_tpl_cardnumber']."\" >
							<label for='empr_pwd'>".$msg['resa_empr_password']."</label>
                            <div class='password-text-visually'>
            				    <input id='empr_pwd' type='password' name='password' autocomplete='current-password' size='20' border='0' value='' />
								<button type='button' role='switch' aria-checked='false' class='fa fa-eye' id='password_visually' onclick='toggle_password(this, \"empr_pwd\");' title='".htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset)."'>
									<span class='visually-hidden'>" . htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset) . "</span>
								</button>
                            </div>
							<input type='hidden' name='enregistrer' value='PUB' >
							<input type='hidden' name='tab' value='dsi' >
							<input type='hidden' name='lvl' value='bannette_gerer' >
							<input type='hidden' name='new_connexion' value='1' >
							<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton'>";
			foreach($bannette_abon as $id => $v){
				$loginform.="
							<input type='hidden' name='bannette_abon[".$id."]' value='1' >";
			}
			if($opac_websubscribe_show==2){
				$loginform.="
				 &nbsp;<input type='button' class='bouton' onclick=\"document.forms['bannette_gerer'].action='subscribe.php';document.forms['bannette_gerer'].submit();\" value='".htmlentities($msg['websubscribe_label'],ENT_QUOTES,$charset)."'/>";
			}
			$loginform.="
						</form>
					</blockquote>
				</div>";
			break;
		default :
		case ('resa') :
			$loginform ="
				<div class='popup_connexion_empr'>
				<h3>".$msg['resa_doit_etre_abon']."</h3>
				<blockquote role='presentation'>
				<form action='do_resa.php' method='post' name='loginform' data-csrf='true'>
				<label for='empr_login'>".$msg['resa_empr_login']."</label>
				<input id='empr_login' type='text' name='login' autocomplete='username' size='20' border='0' placeholder=\"".$msg['common_tpl_cardnumber']."\" >
				<label for='empr_pwd'>".$msg['resa_empr_password']."</label>
                <div class='password-text-visually'>
				    <input id='empr_pwd' type='password' name='password' autocomplete='current-password' size='20' border='0' value='' />
					<button type='button' role='switch' aria-checked='false' class='fa fa-eye' id='password_visually' onclick='toggle_password(this, \"empr_pwd\");' title='".htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset)."'>
						<span class='visually-hidden'>" . htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset) . "</span>
					</button>
                </div>
				<input type='hidden' name='id_notice' value='$id_notice' >
				<input type='hidden' name='id_bulletin' value='$id_bulletin' >
				<input type='hidden' name='lvl' value='resa' >
				<input type='hidden' name='connectmode' value='popup' >
				<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton'> $loginform_forgotten ";

			if($opac_websubscribe_show==2){
				$loginform.="&nbsp;<input type='button' class='bouton' onclick=\"document.forms['loginform'].action='subscribe.php';document.forms['loginform'].submit();\" value='".htmlentities($msg['websubscribe_label'],ENT_QUOTES,$charset)."'/>";
			}
			$loginform.="
				</form>
                <!-- external_authentication -->
				</blockquote>
				</div>";
			break;
	}
	$external_authentication_form = generate_external_authentication_form();
	$loginform = str_replace('<!-- external_authentication -->', '<br />'.$external_authentication_form ,$loginform);

	return $loginform ;

}

function get_default_connexion_form() {
	global $msg,$charset ;
	global $opac_websubscribe_show,$opac_password_forgotten_show;

	$loginform_forgotten='';
	if($opac_password_forgotten_show) {
		$loginform_forgotten = "<a class='ask-password' href='askmdp.php' onclick=\"document.forms['loginform'].action='askmdp.php';document.forms['loginform'].submit();\">" . htmlentities($msg['mdp_forgotten'],ENT_QUOTES,$charset) . "</a>";
	}
	//Pose des soucis si il y a un proxy et variable SCRIPT_URI non fiable
	//$url_redirect = (!empty($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : 'empr.php').(!empty($_SERVER['QUERY_STRING']) ? "?".$_SERVER['QUERY_STRING'] : "");
	$url_redirect = "./empr.php";
	$loginform = "
	<div class='popup_connexion_empr'>
		<h3>".$msg['authentification_page_mandatory']."</h3>
		<blockquote role='presentation'>
		<form action='".$url_redirect."' method='post' name='loginform' data-csrf='true'>
			<label for='empr_login'>".$msg['resa_empr_login']."</label>
			<input id='empr_login' type='text' name='login' autocomplete='username' size='20' border='0' placeholder=\"".$msg['common_tpl_cardnumber']."\" >
			<label for='empr_pwd'>".$msg['resa_empr_password']."</label>
			<div class='password-text-visually'>
                <input id='empr_pwd' type='password' name='password' autocomplete='current-password' size='20' border='0' value='' />
                <button type='button' role='switch' aria-checked='false' class='fa fa-eye' id='password_visually' onclick='toggle_password(this, \"empr_pwd\");' title='".htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset)."'>
					<span class='visually-hidden'>" . htmlentities($msg['rgaa_password_field_desc'], ENT_QUOTES, $charset) . "</span>
				</button>
            </div>
            <input type='hidden' id='direct_access' name='direct_access' value='".(!empty($_SERVER['HTTP_REFERER']) ? 0 : 1)."' />
			<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton'> $loginform_forgotten ";

	if($opac_websubscribe_show){
		$loginform.="<input type='button' class='bouton' onclick=\"document.forms['loginform'].action='subscribe.php';document.forms['loginform'].submit();\" value='".htmlentities($msg['websubscribe_label'],ENT_QUOTES,$charset)."'/>";
	}
	$loginform.="
		</form>
		</blockquote>
	</div>";
	return $loginform;
}
