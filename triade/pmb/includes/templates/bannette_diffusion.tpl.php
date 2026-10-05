<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_diffusion.tpl.php,v 1.3 2023/09/12 12:25:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $charset;
global $bannette_diffusion_view_tpl;

$bannette_diffusion_view_tpl = "
<script>
    function bannette_diffusion_see_records(indice) {
        if(document.getElementById('bannette_diffusion_equation_'+indice+'_records')) {
            var equations_records = document.getElementById('bannette_diffusion_equation_'+indice+'_records');
            if(equations_records.style.display == 'block') {
                equations_records.style.display = 'none';
            } else {
                equations_records.style.display = 'block';
            }
        }
    }
</script>
<div class='row'>
	<div class='bannette_diffusion_view_box'>
		<span class='bannette_diffusion_view_box_label bannette_diffusion_view_label_date'>
			".htmlentities($msg['bannette_diffusion_date'], ENT_QUOTES, $charset)."
		</span>
		<span class='bannette_diffusion_view_box_content bannette_diffusion_view_content_date'>
			!!date!!
		</span>
	</div>
	<div class='bannette_diffusion_view_box'>
		<span class='bannette_diffusion_view_box_label bannette_diffusion_view_label_number_records'>
			".htmlentities($msg['bannette_diffusion_number_records'], ENT_QUOTES, $charset)."
		</span>
		<span class='bannette_diffusion_view_box_content bannette_diffusion_view_content_number_records'>
			!!number_records!!
		</span>
	</div>
	<div class='bannette_diffusion_view_box'>
		<span class='bannette_diffusion_view_box_label bannette_diffusion_view_label_number_sent_mail'>
			".htmlentities($msg['bannette_diffusion_number_sent_mail'], ENT_QUOTES, $charset)."
		</span>
		<span class='bannette_diffusion_view_box_content bannette_diffusion_view_content_number_sent_mail'>
			!!number_sent_mail!!
		</span>
	</div>
	<div class='bannette_diffusion_view_box'>
		<span class='bannette_diffusion_view_box_label bannette_diffusion_view_label_number_deleted_records'>
			".htmlentities($msg['bannette_diffusion_number_deleted_records'], ENT_QUOTES, $charset)."
		</span>
		<span class='bannette_diffusion_view_box_content bannette_diffusion_view_content_number_deleted_records'>
			!!number_deleted_records!!
		</span>
	</div>
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='bannette_diffusion_view_equations'>
		<span class='bannette_diffusion_view_equations_title'>
			<h2>".htmlentities($msg['bannette_diffusion_equations'], ENT_QUOTES, $charset)."</h2>
		</span>
		<span class='bannette_diffusion_view_equations_text'>
			!!equations!!
		</span>
	</div>
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='bannette_diffusion_view_mail_object'>
		<span class='bannette_diffusion_view_mail_object_title'>
			<h2>".htmlentities($msg['bannette_diffusion_mail_object'], ENT_QUOTES, $charset)."</h2>
		</span>
		<span class='bannette_diffusion_view_mail_object_text'>
			!!mail_object!!
		</span>
	</div>
	<div class='bannette_diffusion_view_mail_content'>
		<span class='bannette_diffusion_view_mail_content_title'>
			<h2>".htmlentities($msg['bannette_diffusion_mail_content'], ENT_QUOTES, $charset)."</h2>
		</span>
		<span class='bannette_diffusion_view_mail_content_text'>
			!!mail_content!!
		</span>
	</div>
</div>
<div class='row'>
	<span class='bannette_diffusion_view_recipients'>
		!!recipients!!
	</span>
</div>
!!deleted_records!!";
