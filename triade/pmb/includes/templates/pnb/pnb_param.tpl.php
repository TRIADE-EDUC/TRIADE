<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_param.tpl.php,v 1.15 2021/02/02 10:00:36 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $pnb_param_form, $current_module, $msg;

$pnb_param_form = "
<form class='form-$current_module' name='formulaire' action='admin.php?categ=pnb&sub=param&action=save' method='post'>
	<h3>".$msg['admin_pnb_param_title']."</h3>
	<div class='form-contenu'>
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
				<input class='saisie-30em' id='password' type='password' name='password' value='!!password!!'/>
				<span class='fa fa-eye' onclick='toggle_password(this, \"password\");' ></span>
			</div>
		</div>
		<div class='row'></div>
		<hr />
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='ftp_server'>".$msg['admin_pnb_param_ftp_server']."</label>
			</div>
			<div class='colonne_suite'>	
				<input class='saisie-30em' id='ftp_server' type='text' name='ftp_server' value='!!ftp_server!!'/>
			</div>
		</div>
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
				<span class='fa fa-eye' onclick='toggle_password(this, \"ftp_password\");' ></span>
			</div>
		</div>
		<div class='row'></div>
		<hr />
	</div>
	<h3>".$msg['admin_contribution_area_param_title']."</h3>
	<div class='form-contenu'>	
	    <div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='webservice_url'>".$msg['admin_pnb_param_webservice_url']."</label>
			</div>
			<div class='colonne_suite'>	
				<input class='saisie-30em' id='webservice_url' type='text' name='webservice_url' value='!!webservice_url!!'/>
			</div>
		</div>
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
    			<input type='password' class='saisie-30em' name='user_password' id='user_password' value='!!user_password!!'/>
				<span class='fa fa-eye' onclick='toggle_password(this, \"user_password\");' ></span>
    		</div>		    	
		</div>		    	
		<div class='row'></div>
		<hr />
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
    		<div class='colonne3'>
    			<label class='etiquette' for='alert_threshold_tokens'>".$msg['pnb_alert_threshold_tokens']."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='text' class='saisie-30em' name='alert_threshold_tokens' id='alert_threshold_tokens' value='!!alert_threshold_tokens!!'/>
    		</div>		    	
		</div>  
		<div class='row'></div>  
		<hr />   
	</div>
	<h3>".$msg['pnb_admin_import_expl']."</h3>
	<div class='form-contenu'> 
    	<div class='row'>
    		<div class='colonne3'>
    			<label class='etiquette' >".$msg['pnb_admin_import_expl_typedoc']."</label>
    		</div>
    		<div class='colonne_suite'>
                !!typedoc!!
    		</div>		    	
		</div>  	
		<div class='row'>
    		<div class='colonne3'>
    			<label class='etiquette' >".$msg['pnb_admin_import_expl_location']."</label>
    		</div>
    		<div class='colonne_suite'>
                !!location!!
    		</div>		    	
		</div>  	
    	<div class='row'>
    		<div class='colonne3'>
    			<label class='etiquette' >".$msg['pnb_admin_import_expl_section']."</label>
    		</div>
    		<div class='colonne_suite'>
                !!section!!
    		</div>		    	
		</div>  
		<div class='row'>
    		<div class='colonne3'>
    			<label class='etiquette' >".$msg['pnb_admin_import_expl_statut']."</label>
    		</div>
    		<div class='colonne_suite'>
                !!statut!!
    		</div>		    	
		</div> 
	    <div class='row'>
    	    <div class='colonne3'>
    			<label class='etiquette' >".$msg['pnb_admin_import_expl_codestat']."</label>
    		</div>
    		<div class='colonne_suite'>
                !!codestat!!
    		</div>
    	</div>
		<div class='row'>
    	    <div class='colonne3'>
    			<label class='etiquette' >".$msg['pnb_admin_import_expl_owner']."</label>
    		</div>
    		<div class='colonne_suite'>
                !!owner!!
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

	<script>

		try {
			document.getElementById('location_selector').addEventListener('change', calculate_section);
			calculate_section();
		} catch(err) {};


		function calculate_section() {

			let location = document.getElementById('location_selector');
			let section = document.getElementById('section_selector');
			let sections = document.getElementById('section_selector').options;
			let locations = [];
			let visible_sections = [];

			for(let i=0; i < sections.length; i++) {
				sections[i].style.display = 'none';
				locations = JSON.parse(sections[i].dataset.locations);
				if ( -1 != locations.indexOf(location.value)) {
					sections[i].style.display = 'block';
					visible_sections.push(i);
				}
			}
			if( -1 == visible_sections.indexOf(section.selectedIndex) ) {
				section.selectedIndex = visible_sections[0];
			}
		}

	</script>
</form>
";

