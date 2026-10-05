<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acquisition.tpl.php,v 1.38 2021/04/28 06:52:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $current_module, $user_query;

// $user_query : form de recherche
$user_query = "
<script type='text/javascript'>
<!--
	function test_form(form)
	{
		if(form.user_input.value.length == 0)
			{
				alert(\"$msg[141]\");
				return false;
			}
		return true;
	}
-->
</script>
<form class='form-$current_module' name='search' method='post' action='!!action!!'>
<h3><span>!!user_query_title!!</span></h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne'>
			<input type='text' class='saisie-50em' name='user_input' />
		</div>
		<div class='right'></div>
		<div class='row'></div>
	</div>
</div>
";


$user_query.="	
<div class='row'>
	<div class='left'>
		<input type='submit' class='bouton' value='$msg[142]' onClick=\"return test_form(this.form)\" />
		<input class='bouton' type='button' value='!!add_auth_msg!!' onClick=\"document.location='!!add_auth_act!!'\" />
	</div>
	<div class='right'>
		<!-- lien_derniers -->
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['search'].elements['user_input'].focus();
</script>
<div class='row'></div>
";
?>