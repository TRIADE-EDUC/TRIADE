<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connexion_empr.inc.php,v 1.15 2018-11-20 10:23:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function do_formulaire_connexion() {
	global $dbh,$msg,$charset ;
	global $id_notice ;
	global $id_bulletin ;
	global $lvl;
	global $enregistrer, $new_connexion,$tab,$bannette_abon;
	global $opac_websubscribe_show,$opac_password_forgotten_show;

	$loginform_forgotten='';
	if($opac_password_forgotten_show) {
		$loginform_forgotten="&nbsp;<input type='button' class='bouton' onclick=\"document.forms['loginform'].action='askmdp.php';document.forms['loginform'].submit();\" value='".htmlentities($msg['mdp_forgotten'],ENT_QUOTES,$charset)."'/>";
	}

	switch ($lvl) {
		case ('resa_planning') :
			$loginform ="
				<div class='popup_connexion_empr'>
					<h3>".$msg['resa_doit_etre_abon']."</h3>
						<blockquote>
							<form action='do_resa.php' method='post' name='loginform'>
							<label>".$msg['resa_empr_login']."</label><br />
							<input type='text' name='login' size='20' border='0' value=\"".$msg['common_tpl_cardnumber']."\" onFocus=\"this.value='';\" /><br />
							<label>".$msg['resa_empr_password']."</label><br />
							<input type='password' name='password' size='20' border='0' value='' onFocus=\"this.value='';\" /><br />
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
			global $todo, $noticeid ;
			$loginform ="
				<div class='popup_connexion_empr'>
					<h3>".$msg['avis_doit_etre_abon']."</h3>
						<blockquote>
						<form action='avis.php' method='post' name='loginform'>
							<label>$msg[sugg_empr_login]</label><br />
							<input type='text' name='login' size='20' border='0' value=\"".$msg['common_tpl_cardnumber']."\" onFocus=\"this.value='';\"><br />
							<label>$msg[sugg_empr_password]</label><br />
							<input type='password' name='password' size='20' border='0' value='' onFocus=\"this.value='';\"><br />
							<input type='hidden' name='lvl' value='$lvl' >
							<input type='hidden' name='todo' value='$todo' >
							<input type='hidden' name='noticeid' value='$noticeid' >
							<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton'>
						</form>
					</blockquote>
				</div>";
			break;

		case ('tags') :
			global $noticeid ;
			$loginform ="
				<div class='popup_connexion_empr'>
					<h3>".$msg['tag_doit_etre_abon']."</h3>
					<blockquote>
						<form action='addtags.php' method='post' name='loginform'>
							<label>$msg[sugg_empr_login]</label><br />
							<input type='text' name='login' size='20' border='0' value=\"".$msg['common_tpl_cardnumber']."\" onFocus=\"this.value='';\"><br />
							<label>$msg[sugg_empr_password]</label><br />
							<input type='password' name='password' size='20' border='0' value='' onFocus=\"this.value='';\"><br />
							<input type='hidden' name='lvl' value='$lvl' >
							<input type='hidden' name='noticeid' value='$noticeid' >
							<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton'>
						</form>
					</blockquote>
				</div>";
			break;

		case ('make_sugg') :
			$loginform ="<br />
				<div class='popup_connexion_empr'>
				<h3>".$msg['sugg_doit_etre_abon']."</h3>
					<blockquote>
						<form action='do_resa.php' method='post' name='loginform'>
							<label>$msg[sugg_empr_login]</label><br />
							<input type='text' name='login' size='20' border='0' value=\"".$msg['common_tpl_cardnumber']."\" onFocus=\"this.value='';\"><br />
							<label>$msg[sugg_empr_password]</label><br />
							<input type='password' name='password' size='20' border='0' value='' onFocus=\"this.value='';\"><br />
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
					<blockquote>
						<form action='./empr.php?tab=dsi&lvl=bannette_gerer' method='post' name='bannette_gerer'>
							<label>$msg[resa_empr_login]</label><br />
							<input type='text' name='login' size='20' border='0' value=\"".$msg['common_tpl_cardnumber']."\" onFocus=\"this.value='';\"><br />
							<label>$msg[resa_empr_password]</label><br />
							<input type='password' name='password' size='20' border='0' value='' onFocus=\"this.value='';\"><br />
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
		default;
		case ('resa') :
			$loginform ="
				<div class='popup_connexion_empr'>
				<h3>".$msg['resa_doit_etre_abon']."</h3>
				<blockquote>
				<form action='do_resa.php' method='post' name='loginform'>
				<label>".$msg['resa_empr_login']."</label><br />
				<input type='text' name='login' size='20' border='0' value=\"".$msg['common_tpl_cardnumber']."\" onFocus=\"this.value='';\"><br />
				<label>".$msg['resa_empr_password']."</label><br />
				<input type='password' name='password' size='20' border='0' value='' onFocus=\"this.value='';\"><br />
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
				</blockquote>
				</div>";
			break;
	}
	return $loginform ;

}

function get_default_connexion_form() {
	global $msg,$charset ;
	global $opac_websubscribe_show,$opac_password_forgotten_show;
	
	$loginform_forgotten='';
	if($opac_password_forgotten_show) {
		$loginform_forgotten="&nbsp;<input type='button' class='bouton' onclick=\"document.forms['loginform'].action='askmdp.php';document.forms['loginform'].submit();\" value='".htmlentities($msg['mdp_forgotten'],ENT_QUOTES,$charset)."'/>";
	}
	$url_redirect = (!empty($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : 'empr.php').(!empty($_SERVER['QUERY_STRING']) ? "?".$_SERVER['QUERY_STRING'] : "");
	$loginform = "
	<div class='popup_connexion_empr'>
		<h3>".$msg['authentification_page_mandatory']."</h3>
		<blockquote>
		<form action='".$url_redirect."' method='post' name='loginform'>
			<label>".$msg['resa_empr_login']."</label><br />
			<input type='text' name='login' size='20' border='0' value=\"".$msg['common_tpl_cardnumber']."\" onFocus=\"this.value='';\"><br />
			<label>".$msg['resa_empr_password']."</label><br />
			<input type='password' name='password' size='20' border='0' value='' onFocus=\"this.value='';\"><br />
			<input type='hidden' id='direct_access' name='direct_access' value='".(!empty($_SERVER['HTTP_REFERER']) ? 0 : 1)."' />
			<input type='submit' name='ok' value=\"".$msg[11]."\" class='bouton'> $loginform_forgotten ";
		
	if($opac_websubscribe_show==2){
		$loginform.="&nbsp;<input type='button' class='bouton' onclick=\"document.forms['loginform'].action='subscribe.php';document.forms['loginform'].submit();\" value='".htmlentities($msg['websubscribe_label'],ENT_QUOTES,$charset)."'/>";
	}
	$loginform.="
		</form>
		</blockquote>
	</div>";
	return $loginform;
}
