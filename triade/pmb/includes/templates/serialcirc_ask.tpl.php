<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask.tpl.php,v 1.6 2019-05-27 13:29:27 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $biblio_name, $serialcirc_asklist_filter_tpl, $msg, $charset, $current_module, $serialcirc_asklist_tpl, $serialcirc_asklist_tr, $serialcirc_inscription_accepted_mail, $serialcirc_inscription_no_mail, $serialcirc_inscription_end_mail;

if(!isset($biblio_name)) $biblio_name = '';

$serialcirc_asklist_filter_tpl = "		
	<h1>".htmlentities($msg["serialcirc_asklist_title"],ENT_QUOTES,$charset)."</h1>
	<form class='form-$current_module' name='ask_list_filter' method='post' action='./catalog.php?categ=serials&sub=circ_ask' >		
		<div class='form-contenu'>
			<div class='row'>		
				".htmlentities($msg["serialcirc_asklist_location_title"],ENT_QUOTES,$charset)." : !!localisation_filter!!						
				".htmlentities($msg["serialcirc_asklist_type_title"],ENT_QUOTES,$charset)." : !!type_filter!!			
				".htmlentities($msg["serialcirc_asklist_statut_title"],ENT_QUOTES,$charset)." : !!statut_filter!!
				<input type='submit' class='bouton' value='".htmlentities($msg["serialcirc_asklist_refresh_bt"],ENT_QUOTES,$charset)."'   />
			</div>
			
		</div>
	</form>	
	";
$serialcirc_asklist_tpl = "	
	<script type='text/javascript'>
		
	</script>

	<h3>".htmlentities($msg["serialcirc_asklist_title_form"],ENT_QUOTES,$charset)."</h3>		
	<form class='form-$current_module' name='saisie_cb_ex' method='post' action='./catalog.php?categ=serials&sub=circ_ask'  >
		<div class='form-contenu'>		
			<div class='row'>
				<table width='100%' class='sortable'>
					<tr>
						<th>
						</th>
						<th>
							".htmlentities($msg["serialcirc_asklist_date"],ENT_QUOTES,$charset)."
						</th>
						<th>
							".htmlentities($msg["serialcirc_asklist_empr"],ENT_QUOTES,$charset)."
						</th>
						<th>
							".htmlentities($msg["serialcirc_asklist_type"],ENT_QUOTES,$charset)."
						</th>
						<th>
							".htmlentities($msg["serialcirc_asklist_perio"],ENT_QUOTES,$charset)."
						</th>
						<th>
							".htmlentities($msg["serialcirc_asklist_statut"],ENT_QUOTES,$charset)."
						</th>
						<th>
							".htmlentities($msg["serialcirc_asklist_comment"],ENT_QUOTES,$charset)."
						</th>
					</tr>
					!!asklist!!
				</table>			
			</div>
			<input type='hidden' id='action' name='action' value='' />
			<div class='row'>				
				<input type='submit' class='bouton' value='".htmlentities($msg["serialcirc_asklist_accept_bt"],ENT_QUOTES,$charset)."'  
				onClick=\"document.getElementById('action').value='accept';\" />
				<input type='submit' class='bouton' value='".htmlentities($msg["serialcirc_asklist_refus_bt"],ENT_QUOTES,$charset)."'  
				onClick=\"document.getElementById('action').value='refus';\" />
				<input type='submit' class='bouton' value='".htmlentities($msg["serialcirc_asklist_delete_bt"],ENT_QUOTES,$charset)."' 
				onClick=\"document.getElementById('action').value='delete';\" />
			</div>
	
		</div>
	</form>
	<script type='text/javascript'>	
		
	</script>
	
";
$serialcirc_asklist_tr="
	<tr >					
		<td>
			<input type='checkbox' name='asklist_id[]'  value='!!id_ask!!' class='checkbox' />
		</td>				
		<td>
			!!date!!
		</td>			
		<td>
			!!destinataire!!
		</td>
		<td>
			!!type!!
		</td>
		
		<td>
			!!perio!!
		</td>		
		<td>
			!!statut!!
		</td>
		<td>
			!!comment!!
		</td>	
	</tr>
";	

$serialcirc_inscription_accepted_mail="
<p>Bonjour,</p>
<p>La demande d'inscription concernant le périodique !!issue!! a été acceptée.
</p>
<p>Cordialement,<br />
$biblio_name</p>";


$serialcirc_inscription_no_mail="
<p>Bonjour,</p>
<p>La demande d'inscription concernant le périodique !!issue!! a été refusée.
</p>
<p>Cordialement,<br />
$biblio_name</p>";


$serialcirc_inscription_end_mail="
<p>Bonjour,</p>
<p>La désinscription concernant le périodique !!issue!! a été acceptée.
</p>
<p>Cordialement,<br />
$biblio_name</p>";
