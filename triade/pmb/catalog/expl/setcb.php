<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: setcb.php,v 1.21 2019/06/05 09:04:41 btafforeau Exp $
// popup de saisie d'un code barre

// YPR 22/11/2004 : on lui passe en paramètre le DOM du champ à modifier en retour $returnDOM

global $form, $field_name, $id, $base_path, $msg, $pmb_numero_exemplaire_auto, $checked, $pmb_numero_exemplaire_auto_script, $include_path;

$base_path      = "../../";
$base_title		= "";
require_once ($base_path."/includes/init.inc.php");

// $d = $_GET['returnDOM'];
if (! isset($form))  $form  = 'expl';
if (! isset($field_name)) $field_name = 'f_ex_cb';
if (! isset($id)) $id = 0;

print "
	<script type='text/javascript'>
		function updateParent() {
			if(document.forms['setcb'].elements['option_num_auto'] && document.forms['setcb'].elements['option_num_auto'].checked) {
				var ajax_request = new http_request();
				ajax_request.request('".$base_path."/ajax.php?module=catalog&categ=get_expl_cb&id=".$id."');
				window.opener.document.forms['".$form."'].elements['".$field_name."'].value = ajax_request.get_text();
			} else {
				window.opener.document.forms['".$form."'].elements['".$field_name."'].value = document.forms['setcb'].elements['cb'].value;
			}
			window.close();
		}
	</script>
	<div class='center'>
		<form class='form-catalog' name='setcb' onSubmit='updateParent();'>
			<small><?php echo $msg[4056]; ?></small><br />
			<input type='text' name='cb' value=''>";
if($pmb_numero_exemplaire_auto==1 || $pmb_numero_exemplaire_auto==2){
	$checked=true;
	if ($pmb_numero_exemplaire_auto_script) {
		if (file_exists($include_path."/$pmb_numero_exemplaire_auto_script")) {
			require_once($include_path."/$pmb_numero_exemplaire_auto_script");
			if (function_exists('is_checked_by_default')) {
				$checked=is_checked_by_default($id,0);
			}
		}
	}
	print " ".$msg['option_num_auto']." <INPUT type=checkbox name='option_num_auto' value='num_auto' ".($checked ? "checked='checked'" : "")." />";
}			
print "		<p>
				<input type='button' class='bouton' name='bouton' value='".$msg[76]."' onClick='window.close();'>
				<input type='submit' class='bouton' name='save' value='".$msg[77]."' />
			</p>
		</form>
		<script type='text/javascript'>
			self.focus();
				document.forms['setcb'].elements['cb'].focus();
		</script>
	</div>
</body>
</html>		
";

?>