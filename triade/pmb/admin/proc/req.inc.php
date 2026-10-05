<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: req.inc.php,v 1.5 2022/03/31 14:17:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $action, $msg;
global $req_name, $req_code, $user_aut, $req_comm, $form_classement;

require_once ($class_path.'/parameters.class.php');
require_once ($class_path.'/request.class.php');  
require_once ($class_path.'/requester.class.php');
require_once ($include_path.'/templates/requests.tpl.php');

$rqt = new requester();

//traitement des actions
switch($action) {
	case 'add':
		print "
		<script type='text/javascript' src='./javascript/select.js'></script>
		<script type='text/javascript' src='./javascript/requests.js'></script>
		<script type='text/javascript'>
		function test_form(form) {
			if(form.req_name.value.length == 0) {
				alert('".addslashes($msg[702])."');
				form.req_name.focus();
				return false;
			}
			if(form.req_code.value.length == 0) {
				alert('".addslashes($msg[703])."');
				form.req_code.focus();
				return false;
			}
			return true;
		}
		</script>";
		print $rqt->getForm();
		break;
	case 'modif':
		break;
	case 'update':
		if($req_name && $req_code) {
			$requete = "SELECT count(1) FROM procs WHERE name='".$req_name."' ";
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			if(!$nbr_lignes) {
				if (is_array($user_aut)) { 
					$autorisations=implode(" ",$user_aut);
				} else {
					$autorisations='';
				}
				$param_name=parameters::check_param($req_code);
				if ($param_name!==true) {
					error_message_history($param_name, sprintf($msg['proc_param_check_field_name'],$param_name), 1);
					exit();
				}
				$requete = "INSERT INTO procs (idproc,name,requete,comment,autorisations,num_classement) VALUES ('', '$req_name', '$req_code', '$req_comm', '$autorisations', '$form_classement'  ) ";
				$res = pmb_mysql_query($requete);
			} else {
				print "<script language='Javascript'>alert(\"".addslashes($msg[709])."\");</script>";
			}
			print "<script type='text/javascript'> document.location='./admin.php?categ=proc&sub=proc&action='</script>";
		}
		break;
	case 'del':
		break;
	case 'list':
	default:
		break;
}

?>