<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autorites.tpl.php,v 1.58 2021/04/28 06:52:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg;
global $current_module, $user_query, $categ, $autorites_forcing_form, $autorites_unlocking_request;

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
<h3>!!user_query_title!!</h3>
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne'>
			<!-- sel_pclassement -->
			<!-- sel_thesaurus -->
			<!-- sel_autorites -->
			<!-- sel_authority_statuts -->
			<input type='text' class='saisie-50em' name='user_input' value='!!user_input!!'/>
		</div>
		<div class='right'></div>
		<div class='row'></div>
	</div>
</div>
<!-- sel_langue -->
";

if ($categ=="indexint") $user_query.="
	<div class='row'>
		<input type='radio' name='exact' id='exact1' value='1' !!checked_index!!/>
		<label class='etiquette' for='exact1'>&nbsp;".$msg["indexint_search_index"]."</label>&nbsp;
		<input type='radio' name='exact' id='exact0' value='0' !!checked_comment!!/>
		<label for='exact0' class='etiquette'>&nbsp;".$msg["indexint_search_comment"]."</label>
	</div>";
$user_query.="	
<div class='row'>
	<div class='left'>
		<input type='submit' class='bouton' value='$msg[142]' onClick=\"return test_form(this.form)\" />
		<input class='bouton' type='button' value='!!add_auth_msg!!' onClick=\"document.location='!!add_auth_act!!'\" />
	</div>
	<div class='right'>
		<!-- lien_classement --><!-- lien_derniers --><!-- lien_thesaurus --><!-- imprimer_thesaurus -->
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['search'].elements['user_input'].focus();
</script>
<div class='row'></div>
";


$autorites_forcing_form = "
<form class='form-$current_module' name='search' method='post' action='!!action!!'>
<h3>".$msg['entity_currently_locked']."</h3>
<div class='form-contenu'>
	<div class='row'>
		<p>!!entity_is_locked_by!!</p>
        <p>!!entity_force_edition!!</p>
	</div>
</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value='".$msg['654']."'  />
	</div>
	<div class='right'>
		<input type='submit' class='bouton' value='".$msg['142']."' />
	</div>
<div class='row'></div>
</form>
";

$autorites_unlocking_request= "
<script type='text/javascript'>
    require(['dojo/ready', 'dojo/xhr', 'dojo/on'], function(ready){
        ready(function(){
            var oldBefore = window.onbeforeunload;
            window.addEventListener('beforeunload', function(e){
                /* DO SOME MAGIC MY LITTLE FELLOW */
                oldBefore(e);
            });
        });
    });
</script>
";


?>