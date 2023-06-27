<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr.inc.php,v 1.20 2017-03-01 07:59:36 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// Fonction qui génère le formulaire de connexion emprunteur
function genere_form_connexion_empr(){
	global $opac_websubscribe_show,$opac_password_forgotten_show,$msg;
	
	$loginform="<form action='empr.php' method='post' name='myform'>
				<label>".$msg["common_tpl_cardnumber_default"]."</label><br />
				<input type='text' name='login' class='login' size='14' placeholder='".$msg["common_tpl_cardnumber"]."' ><br />
				<input type='password' name='password' class='password' size='8' placeholder='".$msg["common_tpl_empr_password"]."' value=''/>
				<input type='submit' name='ok' value='".$msg[11]."' class='bouton'></form>";
	if($opac_password_forgotten_show)	
		$loginform.="<a  class='mdp_forgotten' href='./askmdp.php'>".$msg["mdp_forgotten"]."</a>";
	if ($opac_websubscribe_show) 
		$loginform .= "<br /><a class='subs_not_yet_subscriber' href='./subscribe.php'>".$msg["subs_not_yet_subscriber"]."</a>";
	return $loginform ; 
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
		$loginform=str_replace('<!-- common_tpl_login_invite -->','<h3 class="login_invite">'.$msg['common_tpl_login_invite'].'</h3>',$loginform);
	} else {
		$loginform=str_replace('<!-- common_tpl_login_invite -->',$msg["empr_my_account"],$loginform);
		$info = genere_compte_empr() ;
	}
	return str_replace("!!login_form!!",$info,$loginform);
}





