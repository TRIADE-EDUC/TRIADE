<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.tpl.php,v 1.12 2023/12/21 11:11:56 dgoron Exp $

// templates pour gestion des autorités collections

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $serialcirc_circ_list_tpl;
global $serialcirc_copy_resume;
global $ask_transmission_mail;
global $opac_biblio_name;
global $report_late_mail;
global $transmission_accepted_mail;
global $ret_accepted_mail;
global $serialcirc_hold_mail;
global $msg, $charset;

$serialcirc_circ_list_tpl ="
		<table>
			<tr>
				<th role='columnheader'></th>
				<th role='columnheader'>".htmlentities($msg['serialcirc_serial_name'],ENT_QUOTES,$charset)."</th>
				<th role='columnheader'>".htmlentities($msg['serialcirc_circ_mode'],ENT_QUOTES,$charset)."</th>
				<th role='columnheader'>".htmlentities($msg['bulletin_retard_libelle_numero'],ENT_QUOTES,$charset)."</th>
				<th role='columnheader'>".htmlentities($msg['serialcirc_start_date'],ENT_QUOTES,$charset)."</th>
				<th role='columnheader'>".htmlentities($msg['codebarre_sort'],ENT_QUOTES,$charset)."</th>
				<th role='columnheader'>".htmlentities($msg['serialcirc_nb'],ENT_QUOTES,$charset)."</th>
				<th role='columnheader'>".htmlentities($msg['serialcirc_expected_date'],ENT_QUOTES,$charset)."</th>
				<th role='columnheader'>".htmlentities($msg['serialcirc_transmission_date'],ENT_QUOTES,$charset)."</th>
				<th role='columnheader'>".htmlentities($msg['serialcirc_actions'],ENT_QUOTES,$charset)."</th>
			</tr>
			!!rows!!
		</table>
		<form method='post' action='empr.php?tab=serialcirc&lvl=list_abo&action=unsubscribe' name='unsubscribe'>
			<input type='hidden' value='' name='unsubscribe_list[]' id='unsubscribe_list'/>
			<input type='submit' class='bouton' onclick='get_checkboxes();' value='".htmlentities($msg['serialcirc_unsubscribe_checked'],ENT_QUOTES,$charset)."'/>
		</form>
		<script>
			function get_checkboxes(){
				var unsubscribe_list = document.getElementById('unsubscribe_list');
				var inputs = document.getElementsByName('unsubscribe');
				var values = new Array();
				for(var i=0 ; i<inputs.length ; i++){
					if(inputs[i].checked && !inputs[i].disabled ){
						values.push(inputs[i].value);
					}
				}
				unsubscribe_list.value = values;
			}
		</script>";

$serialcirc_copy_resume ="
		<div class='row'>
			<form action='empr.php?tab=serialcirc&lvl=copy&action=ask_copy' method='post'>
				<input type='text' name='expl_cb' value='' placeholder='".htmlentities($msg['serialcirc_codebarre'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['serialcirc_codebarre'],ENT_QUOTES,$charset)."'/>
				&nbsp;<input type='submit' class='bouton' value='".htmlentities($msg['serialcirc_ask_copy'],ENT_QUOTES,$charset)."' />
			</form> 
		</div>
		<div class='row'>
			<hr>
		</div>
		<div class='row'>
			!!ask_copy_list!!
		</div>";

$ask_transmission_mail="
<p>Bonjour,</p>

<p>Vous êtes actuellement en possession du bulletin suivant : !!issue!!.<br />
Le prochain destinataire vous remercie de bien vouloir lui transmettre.</p>

<p>Cordialement,<br />
$opac_biblio_name</p>";

$report_late_mail="
<p>Bonjour,</p>
<p>Je vous signale que le bulletin !!issue!! ne m'a toujours pas été transmis.</p>
<p>Cordialement,<br />
!!empr!!</p>";

$transmission_accepted_mail="
<p>Bonjour,</p>
<p>La demande de transmission concernant le bulletin !!issue!! a été acceptée.<br />
Il devrait vous parvenir rapidement.</p>
<p>Cordialement,<br />
$opac_biblio_name</p>";

$ret_accepted_mail="
<p>Bonjour,</p>
<p>J'ai pris connaissance de votre demande de retour concernant le bulletin !!issue!!.<br />
Je vous le ferai parvenir dans les meilleurs délais.</p>
<p>Cordialement,<br />
!!empr!!</p>";

$serialcirc_hold_mail ="
<p>Bonjour,</p>
<p>Je souhaiterais réserver le document suivant à l'issue de sa circulation : <br />
!!issue!!</p>
<p>Cordialement,<br />
!!empr!!</p>
";
