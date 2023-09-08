<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_param.tpl.php,v 1.9 2019-05-27 09:59:36 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $pnb_param_form, $current_module, $msg, $pnb_param_form_drm_parameters;

$pnb_param_form = "
<form class='form-$current_module' name='formulaire' action='admin.php?categ=pnb&sub=param&action=save' method='post'>
	<h3>".$msg['admin_pnb_param_title']."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='login'>".$msg['admin_pnb_param_login']."</label>
			</div>
			<div class='colonne_suite'>	
				<input class='saisie-30em' id='login' type='text' name='login' value='!!login!!'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='password'>".$msg['admin_pnb_param_password']."</label>	
			</div>
			<div class='colonne_suite'>	
				<input class='saisie-30em' id='password' type='text' name='password' value='!!password!!'/>
			</div>
		</div>
		<hr />
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='ftp_login'>".$msg['admin_pnb_param_ftp_login']."</label>
			</div>
			<div class='colonne_suite'>	
				<input class='saisie-30em' id='ftp_login' type='text' name='ftp_login' value='!!ftp_login!!'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='ftp_password'>".$msg['admin_pnb_param_ftp_password']."</label>
			</div>
			<div class='colonne_suite'>	
				<input class='saisie-30em' id='ftp_password' type='password' name='ftp_password' value='!!ftp_password!!' class='password' placeholder='" . $msg["admin_pnb_param_ftp_password"] . "'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='ftp_server'>".$msg['admin_pnb_param_ftp_server']."</label>
			</div>
			<div class='colonne_suite'>	
				<input class='saisie-30em' id='ftp_server' type='text' name='ftp_server' value='!!ftp_server!!'/>
			</div>
		</div>
		<div class='row'>
		</div>
	</div>
	<h3>".$msg['admin_contribution_area_param_title']."</h3>
	<div class='form-contenu'>	
	    <div class='row'>
    	    <div class='colonne3'>
    			<label class='etiquette' for='user_name'>".$msg['es_user_username']."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='text' class='saisie-30em' name='user_name' id='user_name' value='!!user_name!!' />
    		</div>
    	</div>
    	<div class='row'>
    		<div class='colonne3'>
    			<label class='etiquette' for='user_password'>".$msg['es_user_password']."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='text' class='saisie-30em' name='user_password' id='user_password' value='!!user_password!!'/>
    		</div>		    	
		</div>		    	
        <div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='dilicom_url'>".$msg['admin_pnb_param_dilicom_url']."</label>
			</div>
			<div class='colonne_suite'>	
				<input class='saisie-30em' id='dilicom_url' type='text' name='dilicom_url' value='!!dilicom_url!!'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='webservice_url'>".$msg['admin_pnb_param_webservice_url']."</label>
			</div>
			<div class='colonne_suite'>	
				<input class='saisie-30em' id='webservice_url' type='text' name='webservice_url' value='!!webservice_url!!'/>
			</div>
		</div>
		<div class='row'>
		</div>
	</div>	
	<h3>".$msg['admin_pnb_alert']."</h3>
	<div class='form-contenu'>	
	    <div class='row'>
    	    <div class='colonne3'>
    			<label class='etiquette' for='alert_end_offers'>".$msg['pnb_alert_end_offers']."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='text' class='saisie-30em' name='alert_end_offers' id='alert_end_offers' value='!!alert_end_offers!!' />
    		</div>
    	</div>
    	<div class='row'>
    		<div class='colonne3'>
    			<label class='etiquette' for='alert_staturation_offers'>".$msg['pnb_alert_staturation_offers']."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='text' class='saisie-30em' name='alert_staturation_offers' id='alert_staturation_offers' value='!!alert_staturation_offers!!'/>
    		</div>		    	
		</div>  
		<div class='row'>
		</div>     
	</div>
	<div class='row'>
		<div class='left'>
			<input type='submit' class='bouton' value='".$msg[77]."' />
		</div>
		<div class='right'>	
		</div>
	</div>
</form>
";


$pnb_param_form_drm_parameters = "
<form class='form-$current_module' name='formulaire' action='admin.php?categ=pnb&sub=drm_parameters&action=save' method='post'>
<h3>".$msg['admin_pnb_drm_parameters_title']."</h3>
	<div class='form-contenu'>
		<table>
			<tr>
				<th>".$msg['admin_pnb_drm_parameters_name']."</th>
				<th>".$msg['admin_pnb_drm_parameters_loan_duration']."</th>
				<th>".$msg['admin_pnb_drm_parameters_prolonge']."</th>
			</tr>
    		<tr>	
				<td>
					".$msg['admin_pnb_drm_acs']."
				</td>
				<td>
					<input type='text' name='loan_durations_ACS' value='!!loan_duration_ACS!!'/>
				</td>
				<td>
					<input type='checkbox' name='prolongation_ACS' value='1' !!prolongation_ACS_checked!!/>
				</td>
			</tr>    	
    		<tr>	
				<td>
					".$msg['admin_pnb_drm_lcp']."
				</td>
				<td>
					<input type='text' name='loan_durations_LCP' value='!!loan_duration_LCP!!'/>
				</td>
				<td>
					<input type='checkbox' name='prolongation_LCP' value='1' !!prolongation_LCP_checked!!/>
				</td>
			</tr>    	   	
    		<tr>	
				<td>
					".$msg['admin_pnb_drm_sony']."
				</td>
				<td>
					<input type='text' name='loan_durations_SONY' value='!!loan_duration_SONY!!'/>
				</td>
				<td>
					<input type='checkbox' name='prolongation_SONY' value='1' !!prolongation_SONY_checked!!/>
				</td>
			</tr>    	
	   </table>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='submit' class='bouton' value='".$msg[77]."' />
		</div>
		<div class='right'>
		</div>
	</div>
</form>
";