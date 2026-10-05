<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: groupexpl.inc.php,v 1.5 2022/01/10 10:57:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $msg, $charset;
global $sel_header, $sel_footer, $caller, $expl_list, $expl_list_id, $deb_rech, $user_input, $bt_ajouter, $nbr_lignes, $page, $jscript;
global $pmb_lecteurs_localises, $deflt_docs_location, $sel_search_form;
global $id_groupexpl, $name, $statut_principal, $statut_others, $comment;

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=groupexpl&caller=$caller&expl_list_id=$expl_list_id";

require_once($class_path."/groupexpl.class.php");
require_once($class_path."/session.class.php");
// contenu popup sélection collection
require_once('./selectors/templates/sel_groupexpl.tpl.php');

// affichage du header
print $sel_header;

// traitement en entrée des requêtes utilisateur
if ($deb_rech) $f_user_input = $deb_rech ;
$rech_regexp = 0 ;
if($f_user_input=="" && $user_input=="") {
	$user_input='';
} else {
	// traitement de la saisie utilisateur
	if ($user_input) $f_user_input=$user_input;
	if (($f_user_input)&&(!$user_input)) $user_input=$f_user_input;
}

// affichage des membres de la page

if($bt_ajouter == "no"){
	$bouton_ajouter="";
}else{
	$bouton_ajouter= "<input type='button' class='bouton_small' onclick=\"document.location='$base_url&action=add&deb_rech='+this.form.f_user_input.value\" value=\"".$msg["groupexpl_create_button"]."\" />";
}

switch($action){
	case 'add':
		$groupexpl_form = str_replace("!!deb_saisie!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $groupexpl_form);
		$groupexpl_form=str_replace('!!statut_principal!!',do_selector('docs_statut', 'statut_principal', ""),$groupexpl_form);
		$groupexpl_form=str_replace('!!statut_others!!',do_selector('docs_statut', 'statut_others', ""),$groupexpl_form);
		
		if($pmb_lecteurs_localises){
			$f_loc=$deflt_docs_location;
				
			$loc_select .= "
			<div class='row'>
				<label class='etiquette' for='name'>".$msg['groupexpl_form_location']."</label>
			</div>
			<div class='row'>
				<select name='f_loc' >";
			$res = pmb_mysql_query("SELECT idlocation, location_libelle FROM docs_location order by location_libelle");
			$loc_select .= "<option value='0'>".$msg["all_location"]."</option>";
			while ($value = pmb_mysql_fetch_array($res)) {
				$loc_select .= "<option value='".$value[0]."'";
				if ($value[0]==$f_loc)	$loc_select .= " selected ";
				$loc_select .= ">".htmlentities($value[1],ENT_QUOTES,$charset)."</option>";
			}
			$loc_select .= "
				</select>
			</div>";
		}
		$groupexpl_form=str_replace('!!location!!',$loc_select, $groupexpl_form);
		print $groupexpl_form;
		break;
	case 'update':
		require_once("$class_path/groupexpl.class.php");
		$groupexpl = new groupexpl();
		$value = array();
		$value['name'] = $name;
		$value['location'] = $f_loc;
		$value['statut_principal'] = $statut_principal;
		$value['statut_others'] = $statut_others;
		$value['comment'] = $comment;
		$groupexpl->save($value);
		$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($user_input, $nbr_lignes, $page);
		break;
	case "add_expl":
		if($id_groupexpl) {
			print $jscript;
			$informations = array();
			$groupexpl=new groupexpl($id_groupexpl);
			$exemplaires = explode(",",$expl_list);
			$flag_error = 0;
			if(count($exemplaires)) {
				foreach ($exemplaires as $cb) {
					$added = $groupexpl->add_expl($cb);
					if($added) {
						$informations[$cb] = $groupexpl->info_message;
						print "<script type='text/javascript'>
							set_parent('groupexpl_name_".$cb."','".$id_groupexpl."','".$groupexpl->info['name']."');
						</script>";
					} else {
						$id_group = $groupexpl->get_id_group_from_cb($cb);
						$error_html_message = "<a style='cursor:pointer' onclick=\"window.parent.document.location.href='./circ.php?categ=groupexpl&action=form&id=".$id_group."'\">".$groupexpl->get_name_group_from_id($id_group)."</a>";
						$informations[$cb] = $groupexpl->error_message." ".$error_html_message;
						$flag_error = 1;
					}
				}
			}
			if($flag_error) {
				foreach ($informations as $cb=>$information) {
					$query="SELECT expl_id FROM exemplaires WHERE expl_cb='".$cb."'";
					$result=pmb_mysql_query($query);
					if($result && pmb_mysql_num_rows($result)) {
						$id = pmb_mysql_result($result,0,0);
						$nt = new mono_display_expl('',$id, 0);
						print "<div class='row'>".$nt->result.$aff."<span class='erreur'>".$information."</span></div>";
					}
				}
				$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
				$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);
				print $sel_search_form;
				show_results($user_input, $nbr_lignes, $page);
			} else {
				print "<script type='text/javascript'>
						window.close();
					</script>";
			}
		}
		break;
		
	default:
		$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($user_input, $nbr_lignes, $page);
		break;
	}

function show_results($user_input, $nbr_lignes=0, $page=0, $id = 0) {
	global $nb_per_page;
	global $base_url;
 	global $charset;
	global $msg;
	global $expl_list_id ;
	
	// on récupére le nombre de lignes qui vont bien
	if (!$id) {
		if($user_input=="") {
			$requete = "SELECT COUNT(1) FROM groupexpl";	
		} else {
			$aq=new analyse_query(stripslashes($user_input));
			if ($aq->error) {
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
				exit;
			}
			$requete="select count(distinct id_groupexpl) from groupexpl where groupexpl_name like '%".$user_input."%'";
		}
		$res = pmb_mysql_query($requete);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
	} else $nbr_lignes=1;
	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;

	if($nbr_lignes) {
		$expl_list = explode(",",$expl_list_id);
		$expl_list_cb = array();
		foreach ($expl_list as $id_expl) {
			$query="SELECT expl_cb FROM exemplaires WHERE expl_id='".$id_expl."'";
			$result=pmb_mysql_query($query);
			if($result && pmb_mysql_num_rows($result)) {
				$cb = pmb_mysql_result($result,0,0);
				$expl_list_cb[] = $cb;
			}
		}
		
		$last_id_used = session::get_last_used("groupexpl");
		if($last_id_used) {
			print "<div class='selector_last_used'>
 				<div class='row'>
 					<b>".$msg["selector_last_groupexpl_used"]."</b>
 				</div>
 				<div class='row'>";
			$query = "select id_groupexpl, groupexpl_name from groupexpl where id_groupexpl=".$last_id_used;
			$result = pmb_mysql_query($query);
			while($group = pmb_mysql_fetch_object($result)) {
				print pmb_bidi("
						<a href=\"$base_url&action=add_expl&id_groupexpl=".$group->id_groupexpl."&expl_list=".implode(",", $expl_list_cb)."\">
						$group->groupexpl_name</a><br />");
			}
			print "</div></div>";
		}
		
		// on lance la vraie requête
		if (!$id) {
			if($user_input=="") {
				$requete = "SELECT groupexpl.* FROM groupexpl";
				$requete .= " ORDER BY groupexpl_name LIMIT $debut,$nb_per_page ";
			} else {
				$requete="select groupexpl.* from groupexpl where groupexpl_name like '%".$user_input."%' order by groupexpl_name LIMIT $debut,$nb_per_page";
			}
		} else $requete="select groupexpl.* FROM groupexpl where id_groupexpl='".$id."'";
		$res = pmb_mysql_query($requete);
		while(($group=pmb_mysql_fetch_object($res))) {
			print pmb_bidi("
 			<a href=\"$base_url&action=add_expl&id_groupexpl=".$group->id_groupexpl."&expl_list=".implode(",", $expl_list_cb)."\">
					$group->groupexpl_name</a><br />");
		}
		pmb_mysql_free_result($res);

		// affichage pagination
		print "<div class='row'>&nbsp;<hr /></div><div class='center'>";
		$url_base = $base_url."&user_input=".rawurlencode(stripslashes($user_input));
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		print $nav_bar;
		print "</div>";

	}
}

print $sel_footer;
