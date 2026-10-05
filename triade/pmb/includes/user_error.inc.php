<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user_error.inc.php,v 1.31.2.1.2.2 2025/03/17 08:15:42 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions d'affichage des messages d'erreur

function error_form_message($error_message) {

	echo "<script>alert(\"$error_message\"); history.go(-1);</script>";
	exit();
}

/**
 * Affiche un message d'erreur
 *  Attention : Les balises HTML sont autorisees, penses a mettre des htmlentities
 *
 * @param string $error_title Titre de l'erreur (non utilise)
 * @param string $error_message Message d'erreur (Attention : les balises HTML sont autorisees)
 * @param boolean $back_button Afficher le bouton "retour"
 * @param string $ret_adr URL de retour sinon "history.go(-1)" est fait par defaut
 * @param boolean $return_html Retourne le code html
 * @return void|string
 */
function error_message($error_title, $error_message, $back_button=0, $ret_adr='', $return_html = false) {
	global $msg, $current_module, $charset;

	$html = "<br />
		<div class='erreur'>". htmlentities($msg[540], ENT_QUOTES, $charset) ."</div>
		<div class='row'>
			<div class='colonne10'>
				<img src='".get_url_icon('error.gif')."' class='align_left'>
			</div>
			<div class='colonne80'>
				<strong>". $error_message ."</strong>
			</div>
		</div>";

	if ($back_button) {
		if ($ret_adr) {
			$html .= "
			<div class='row'>
				<form class='form-". htmlentities($current_module, ENT_QUOTES, $charset) ."' name='dummy'>
					<input
						type='button'
						name='ok'
						class='bouton'
						value=' ". htmlentities($msg[89], ENT_QUOTES, $charset) ." '
						onClick='document.location=\"$ret_adr\"'
					/>
				</form>
				<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>
			</div>";
		} else {
			$html .= "
			<div class='row'>
				<form class='form-". htmlentities($current_module, ENT_QUOTES, $charset) ."' name='dummy'>
					<input
						type='button'
						name='ok'
						class='bouton'
						value=' ". htmlentities($msg[89], ENT_QUOTES, $charset) ." '
						onClick='history.go(-1);'
					/>
				</form>
				<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>
			</div>";
		}
	}

	if ($return_html) {
		return $html;
	} else {
		echo $html;
	}
}

function return_error_message($error_title, $error_message, $back_button=0, $ret_adr='', $ret_url='') {

	global $msg;
	global $current_module ;
	/*	la même que error_message mais return au lieu de print
		paramètres : -->
		$error_title	:	titre du message
		$error_message	: 	texte du message
		$back_button	:	flag pour affichage du bouton (TRUE=oui ; FALSE=non)
		$ret_adr		:	adresse de retour. si non précisé : $default_ret_adr est utilisé
	*/

	$default_ret_adr = './main.php';
	//affichage

	$retour = "
	<br /><div class='erreur'>$msg[540]</div>
	<div class='row'>
		<div class='colonne10'>
			<img src='".get_url_icon('error.gif')."' class='align_left'>
			</div>
		<div class='colonne80'>
			<strong>$error_message</strong>
			</div>
		</div>

		";

	if($back_button) {
		if(!$ret_adr) $ret_adr = $default_ret_adr;

		if (strpos($ret_url,"?")) {
			$extract_url = explode("?", $ret_url);
			$items=explode("&", $extract_url[1]);
			if (is_array($items)) {
				foreach($items as $i=>$item){
					$item=explode("=",$item);
					switch ($item[0]) {
						case "action" :
						case "act" :
							$items[$i] = $item[0]."=";
							break;
						default :
							$items[$i] = $item[0]."=".$item[1];
							break;
					}
				}
				$extract_url[1] = implode("&", $items);
			}
			$ret_url = implode("?", $extract_url);
		}
		if(strpos($ret_url, 'ajax.php') !== false) {
			$ret_url=$_SERVER['HTTP_REFERER'];
		}
		$retour .= "
			<div class='row'>
				<form class='form-$current_module' name='dummy' method=\"post\" action=\"".urldecode($ret_adr)."\">
				<input type=hidden name=ret_url value=\"".addslashes($ret_url)."\">
				<input type='submit' name='ok' class='bouton' value=' $msg[89] ' >
				</form>
				<script type='text/javascript'>
					document.forms['dummy'].elements['ok'].focus();
					if (typeof unload_off === 'function') {
						unload_off();
					}
				</script>
				</div>
				";
	}
	return $retour ;
}


function error_message_history($error_title, $error_message, $back_button=0) {

	global $msg;
	global $current_module ;
	/*
		paramètres : -->
		$error_title	:	titre du message
		$error_message	: 	texte du message
		$back_button	:	flag pour affichage du bouton (TRUE=oui ; FALSE=non)
	*/

	//affichage
	print "
	<br /><div class='erreur'>$msg[540]</div>
	<div class='row'>
		<div class='colonne10'>
			<img src='".get_url_icon('error.gif')."' class='align_left'>
			</div>
		<div class='colonne80'>
			<strong>$error_message</strong>
		</div>
	</div>
	";

	if($back_button) {
		print "
			<div class='row' id='error_message_history_button'>
				<form class='form-$current_module' name='dummy'>
				<input type='button' name='ok' class='bouton' value=' $msg[89] ' onClick='history.go(-1);'>
				</form>
				<script type='text/javascript'>
					document.forms['dummy'].elements['ok'].focus();
				</script>
				</div>
				";
	}

	print "";
}

function choice_message($error_title, $error_message, $back_button=0, $ret_adr='', $cancel_button=0, $cancel_adr='') {

	global $msg;
	global $current_module ;
	/*
		paramètres : -->
		$error_title	:	titre du message
		$error_message	: 	texte du message
		$back_button	:	flag pour affichage du bouton (TRUE=oui ; FALSE=non)
		$ret_adr	:	adresse de retour. si non précisé : $default_ret_adr est utilisé
		$cancel_button	:	flag pour affichage du bouton annulation (TRUE=oui ; FALSE=non)
		$cancel_adr	:	adresse d'annulation. si non précisé : $default_ret_adr est utilisé
	*/

	$default_ret_adr = './main.php';
	//affichage

	print "
		<br />
		<table style='border:0px; padding:0px' bgcolor='#e0e0e0' class='center fiche-lecteur' width='350' role='presentation'>
			<tr>
				<td class='error-header' colspan='2'>
					$msg[540] <!--$msg[1001] : $error_title-->
				</td>
			</tr>
			<tr>
				<td class='align_left'><br />
					<img src='".get_url_icon('error.gif')."' class='align_left'>
				</td>
				<td><br />
					<p class='error'>$error_message</p>
				</td>
			</tr>";

	if($back_button) {
		if(!$ret_adr) $ret_adr = $default_ret_adr;
		print "<tr>
			<td class='center' colspan='2'><br />
			<form class='form-$current_module' name='dummy'>
			<input type='button' name='ok' class='button' value=' $msg[89] ' onClick='document.location=\"$ret_adr\"'>
			</form>
			<script type='text/javascript'>
				document.forms['dummy'].elements['ok'].focus();
				</script>
			</td>
			</tr>";
		}
	if($cancel_button) {
		if(!$cancel_adr) $cancel_adr = $default_ret_adr;
		print "<tr>
			<td class='center' colspan='2'><br />
			<form class='form-$current_module' name='dummy2'>
			<input type='button' name='ok' class='button' value=' $msg[76] ' onClick='document.location=\"$cancel_adr\"'>
			</form>
			</td>
			</tr>";
		}

	print "</table>";
}

function form_error_message($error_title, $error_message, $libelle, $ret_adr='', $vars=array()) {

	global $msg;
	global $current_module,$charset ;
	/*	la même que error_message mais return au lieu de print
		paramètres : -->
		$error_title	:	titre du message
		$error_message	: 	texte du message
		$back_button	:	flag pour affichage du bouton (TRUE=oui ; FALSE=non)
		$ret_adr		:	adresse de retour. si non précisé : $default_ret_adr est utilisé
	*/

	$default_ret_adr = './main.php';
	//affichage

	$retour = "
	<br /><div class='erreur'>$msg[540]</div>
	<div class='row'>
		<div class='colonne10'>
			<img src='".get_url_icon('error.gif')."' class='align_left'>
			</div>
		<div class='colonne80'>
			<strong>$error_message</strong>
			</div>
		</div>

		";

	if(!$ret_adr) $ret_adr = $default_ret_adr;
	$retour .= "
		<div class='row'>
			<form class='form-$current_module' name='dummy' method=\"post\" action=\"".urldecode($ret_adr)."\">
	";
	foreach($vars as $key=>$values) {
		$retour.="<input type='hidden' name='".htmlentities($key,ENT_QUOTES,$charset)."' value='".htmlentities($values,ENT_QUOTES,$charset)."'/>\n";
	}
	$retour .="
			<input type='submit' name='ok' class='bouton' value='".$libelle."' >
			</form>
			<script type='text/javascript'>
				document.forms['dummy'].elements['ok'].focus();
			</script>
			</div>
	";

	return $retour ;
}

function information_message($error_title, $error_message, $back_button=0, $ret_adr='', $cancel_button=0, $cancel_adr='') {

	global $msg;
	global $current_module ;
	/*
		paramètres : -->
		$error_title	:	titre du message
		$error_message	: 	texte du message
		$back_button	:	flag pour affichage du bouton (TRUE=oui ; FALSE=non)
		$ret_adr	:	adresse de retour. si non précisé : $default_ret_adr est utilisé
		$cancel_button	:	flag pour affichage du bouton annulation (TRUE=oui ; FALSE=non)
		$cancel_adr	:	adresse d'annulation. si non précisé : $default_ret_adr est utilisé
	*/

	$default_ret_adr = './main.php';
	//affichage

	print "<br />
		<div class='row'>
		<div class='colonne10'>
			<img src='".get_url_icon('idea.gif')."' class='align_left'>
			</div>
		<div class='colonne80'>
			<strong>$error_message</strong>
			</div>
		</div>";

	if($back_button) {
		print "
		<div class='row'>
			<form class='form-$current_module' name='dummy'>";
		if($ret_adr) {
			print "<input type='button' name='ok' class='bouton' value=' $msg[89] ' onClick='document.location=\"$ret_adr\"' />";
		} else  {
			print "<input type='button' name='ok' class='bouton' value=' $msg[89] ' onClick='history.go(-1);' />";
		}
		print "
			</form>
			<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>
		</div>";
	}
// 	if($cancel_button) {
// 		if(!$cancel_adr) $cancel_adr = $default_ret_adr;
// 		print "<tr>
// 			<td class='center' colspan='2'><br />
// 			<form class='form-$current_module' name='dummy2'>
// 			<input type='button' name='ok' class='button' value=' $msg[76] ' onClick='document.location=\"$cancel_adr\"'>
// 			</form>
// 			</td>
// 			</tr>";
// 		}

// 	print "</table>";
}

function warning($error_title, $error_message)  {
	print "
	<table border='0' class='center warning' role='presentation'>
    	<tr>
    		<td style='vertical-align:top; width:33px'>
                <img src='".get_url_icon('error.gif')."'>
            </td>
    		<td style='vertical-align:top'>
                <strong>$error_title</strong><br />$error_message
            </td>
    	</tr>
	</table>";
}

function box_confirm_message($error_title, $error_message, $ret_adr='', $cancel_adr='', $confirm_texte_bouton='', $cancel_texte_bouton='') {
	print return_box_confirm_message($error_title, $error_message, $ret_adr, $cancel_adr, $confirm_texte_bouton, $cancel_texte_bouton);
}

function return_box_confirm_message($error_title, $error_message, $ret_adr='', $cancel_adr='', $confirm_texte_bouton='', $cancel_texte_bouton='') {

	global $msg;
 	global $current_module ;
	/*
		paramètres : -->
		$error_title	:	titre du message
		$error_message	: 	texte du message
		$ret_adr	:	adresse de confirmation
		$cancel_adr	:	adresse d'annulation
		$confirm_texte_bouton : texte du bouton de confirmation
		$cancel_texte_bouton : texte du bouton d'annulation
	*/

	if ($confirm_texte_bouton == '') $confirm_texte_bouton = $msg[89];
	if ($cancel_texte_bouton == '') $cancel_texte_bouton = $msg[76];
	//affichage

	//affichage
	$html = "<br />
	<div class='erreur'>$error_title</div>
	<div class='row'>
		<div class='colonne10'>
			<img src='".get_url_icon('error.gif')."' class='align_left'>
		</div>
		<div class='colonne80'>
			<strong>$error_message</strong>
		</div>
	</div>
	<div class='row'>
		<form class='form-$current_module' name='dummy'>
			<input type='button' name='ok' class='bouton' value=\" ".$confirm_texte_bouton." \" onClick='document.location=\"$ret_adr\"'>
			<input type='button' name='cancel' class='bouton' value=\" ".$cancel_texte_bouton." \" onClick='document.location=\"$cancel_adr\"'>
		</form>
	</div>";
	return $html;
}