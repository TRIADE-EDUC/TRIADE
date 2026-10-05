<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest_profil_import.tpl.php,v 1.5 2021/01/29 09:34:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $harvest_content_form_tpl, $harvest_form_elt_tpl;

$harvest_content_form_tpl="	
<div class='row'>
	<label class='etiquette' for='name'>".$msg['admin_harvest_profil_form_name']."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
</div>		
<div class='row'>
	<table border='0' width='100%'>	
		<th>".$msg['admin_harvest_profil_form_field_title']."
		</th>
		<th>".$msg['admin_harvest_profil_form_field_no']."
		</th>
		<th>".$msg['admin_harvest_profil_form_field_replace']."
		</th>
		<th>".$msg['admin_harvest_profil_form_field_add']."
		</th>
		!!elt_list!!
	</table>
</div>	
";

$harvest_form_elt_tpl="		
<tr>
	<td>
		<label class='etiquette' >!!pmb_field_msg!!</label>
	</td>
	<td>	
		<input type='radio'  name='flagtodo_!!id!!'  !!flagtodo_checked_0!! value='0' /> 
	</td>
	<td>
		<input type='radio'  name='flagtodo_!!id!!'  !!flagtodo_checked_1!! value='1' /> 
	</td>
	<td>
		<input type='radio'  name='flagtodo_!!id!!'  !!flagtodo_checked_2!! value='2' /> 
	</td>
</tr>

";
