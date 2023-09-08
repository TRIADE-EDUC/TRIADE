<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account.php,v 1.76 2019-06-07 08:31:33 btafforeau Exp $

global $base_path, $base_auth, $base_title, $base_use_dojo, $include_path, $class_path, $menu_bar, $extra2, $account_layout, $use_shortcuts;
global $modified, $user_params, $PMBuserid, $param_default, $account_form, $stylesheet, $names, $values, $user_lang, $form_pwd, $pmb_url_base;
global $form_nb_per_page_search, $form_nb_per_page_select, $form_nb_per_page_gestion, $form_style, $form_deflt_thesaurus, $field_values, $dummy;
global $form_deflt_docs_location, $cle, $valeur, $n_values, $loc, $msg, $extra, $extra_info, $footer;

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "PREF_AUTH|ADMINISTRATION_AUTH";  
$base_title = "\$msg[933]";
$base_use_dojo=1;
require_once ("$base_path/includes/init.inc.php");  

// modules propres à account.php ou à ses sous-modules
include($include_path."/account.inc.php");
include($include_path."/templates/account.tpl.php");
require_once($include_path."/user_error.inc.php");
require_once($base_path."/admin/users/users_func.inc.php");

require_once($class_path.'/user.class.php');

print "<div id='att' style='z-Index:1000'></div>";
print $menu_bar;
print $extra2;
print $account_layout;

if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}

if(empty($modified)) {
	$user_params = get_account_info(SESSlogin);
	
	$param_default = user::get_form($PMBuserid, 'account_form');
	
	$account_form = str_replace('!!all_user_param!!', $param_default, $account_form);
	// fin gestion des paramètres personalisés du user
	
	$account_form = str_replace('!!combo_user_style!!', make_user_style_combo($stylesheet), $account_form);
	$account_form = str_replace('!!combo_user_lang!!', make_user_lang_combo($user_params->user_lang), $account_form);
	$account_form = str_replace('!!nb_per_page_search!!', $user_params->nb_per_page_search, $account_form);
	$account_form = str_replace('!!nb_per_page_select!!', $user_params->nb_per_page_select, $account_form);
	$account_form = str_replace('!!nb_per_page_gestion!!', $user_params->nb_per_page_gestion, $account_form);
	print $account_form;

} else {
		
	// code de mise à jour
	// constitution des variables MySQL
	// mise à jour de la date d'update 
	
	$names[] = 'last_updated_dt';
	$values[] = "'".today()."'";
	
	$names[] = 'user_lang';
	$values[] = "'$user_lang'";
	
	if ($form_pwd) {
		$names[] = 'pwd';
		$values[] = "password('$form_pwd')";
		$names[] = 'user_digest';
		$values[]= "'".md5(SESSlogin.":".md5($pmb_url_base).":".$form_pwd)."'";
	}
	
	if($form_nb_per_page_search >= 1) {
		$names[] = 'nb_per_page_search';
		$values[] = "'$form_nb_per_page_search'";
	}
	
	if($form_nb_per_page_select >= 1) {
		$names[] = 'nb_per_page_select';
		$values[] = "'$form_nb_per_page_select'";
	}
	
	if($form_nb_per_page_gestion >= 1) {
		$names[] = 'nb_per_page_gestion';
		$values[] = "'$form_nb_per_page_gestion'";
	}
	
	if(strcmp($form_style, $stylesheet)) {
		$names[] .= 'deflt_styles';
		$values[] .= "'$form_style'";
	}
	
			
	/* insérer ici la maj des param et deflt */
	
	//maj thesaurus par defaut en session
	if ($form_deflt_thesaurus) thesaurus::setSessionThesaurusId($form_deflt_thesaurus);
			
	$requete_param = "SELECT * FROM users WHERE userid='$PMBuserid' LIMIT 1 ";
	$res_param = pmb_mysql_query($requete_param);
	$field_values = pmb_mysql_fetch_row($res_param);
	$i = 0;
	while ($i < pmb_mysql_num_fields($res_param)) {
		$field = pmb_mysql_field_name($res_param, $i) ;
		$field_deb = substr($field,0,6);
		switch ($field_deb) {
			case "deflt_" :
				if ($field == "deflt_styles") {
					$dummy[$i+8]=$field."='".$form_style."'";
				} elseif ($field == "deflt_docs_section") {
					$formlocid="f_ex_section".$form_deflt_docs_location ;
					$dummy[$i+8]=$field."='".${$formlocid}."'";
				} else {
					$var_form = "form_".$field;
					global ${$var_form};
					$dummy[$i+8]=$field."='".${$var_form}."'";
				}
				break;
			case "deflt2" :
				$var_form = "form_".$field;
				global ${$var_form};
				$dummy[$i+8]=$field."='".${$var_form}."'";
				break ;
			case "param_" :
				$var_form = "form_".$field;
				global ${$var_form};
				$dummy[$i+8]=$field."='".${$var_form}."'";
				break ;
			case "value_" :
				$var_form = "form_".$field;
				global ${$var_form};
				$dummy[$i+8]=$field."='".${$var_form}."'";
				break ;
			case "xmlta_" :
				$var_form = "form_".$field;
				global ${$var_form};
				$dummy[$i+8]=$field."='".${$var_form}."'";
				break ;
			case "deflt3" :
				$var_form = "form_".$field;
				global ${$var_form};
				$dummy[$i+8]=$field."='".${$var_form}."'";
				break ;
			case "speci_" :
				$speci_func = substr($field, 6);
				eval('$dummy[$i+8].= set_'.$speci_func.'();');
				break;
			default :
				break ;
		}
		$i++;
	}

	if(!empty($dummy)) {
		$set = join($dummy, ", ");
		$set = " , ".$set ;
	} else $set = "" ;
	if(sizeof($names) == sizeof($values)) {
	    $test = 0;
	    foreach($names as $cle => $valeur) {
			$n_values ? $n_values .= ", $valeur=${values[$cle]}" : $n_values = "$valeur=${values[$cle]}";
			$test++;
	    }
		$requete = "UPDATE users SET $n_values $set , last_updated_dt=curdate() WHERE username='".SESSlogin."' ";
		$result = @pmb_mysql_query($requete);
		if($result) {
			$loc = "index.php" ;
			if (SESSrights & ADMINISTRATION_AUTH) 
				$loc="admin.php";
			if (SESSrights & EDIT_AUTH) 
				$loc="edit.php";
			if (SESSrights & AUTORITES_AUTH) 
				$loc="autorites.php";
			if (SESSrights & CATALOGAGE_AUTH) 
				$loc="catalog.php";
			if (SESSrights & CIRCULATION_AUTH) 
				$loc="circ.php";
			print $msg["937"]." <!-- back to main page --> <script type=\"text/javascript\"> document.location=\"./".$loc."\"; </script>";
		} else {
			// c'est parti en vrac : erreur MySQL
			warning($msg["281"], $msg["936"]);
		}
	}
}
	
print "</div></div>";
print $extra;
print $extra_info;
print $footer;

pmb_mysql_close();
