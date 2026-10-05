<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animation.tpl.php,v 1.1 2021/03/01 10:02:15 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) {
    die("no access");
}

global $base_path, $animation_inputs, $animation_editorial;

$animation_inputs['editorial_button'] = '<input class="bouton" type="button" value="'.plugins::get_message('animation', "animation_editorial_button").'" onClick="document.location=\''.$base_path.'/animations.php?categ=animations&action=editorial&event_action=create&id=!!animation_id!!\'"/>';
$animation_inputs['view_editorial_article'] = '<input class="bouton" type="button" value="'.plugins::get_message('animation', "animation_view_editorial_article").'" onClick="window.open(\''.$base_path.'/cms.php?categ=article&sub=edit&id=!!article_id!!\')"/>';
$animation_inputs['update_manuel_editorial_article'] = '<input class="bouton" type="button" value="'.plugins::get_message('animation', "animation_update_manuel_editorial_article").'" onClick="document.location=\''.$base_path.'/animations.php?categ=animations&action=editorial&event_action=update&id=!!animation_id!!\'"/>';

$animation_editorial = '
<div class="row">
	<div class="row">
		<b> !!title!! :</b>
		!!date!! !!hour!!
	</div>
</div>';