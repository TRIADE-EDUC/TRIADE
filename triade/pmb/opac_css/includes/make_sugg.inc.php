<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: make_sugg.inc.php,v 1.28 2023/12/20 10:57:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $msg, $opac_show_help, $form_action, $id_notice;
global $opac_rgaa_active;

require_once($base_path.'/classes/suggestions_categ.class.php');
require_once($base_path.'/classes/docs_location.class.php');

$tooltip = str_replace("\\n","<br />",$msg["empr_sugg_ko"]);
$sug_form= "<div id='make_sugg'>
".common::format_title($msg['empr_make_sugg']);
if($opac_show_help) {
    $sug_form .= "
    <div class='row'>
    	$tooltip
    </div>";
}
if(!isset($form_action) || !$form_action) $form_action = 'do_resa.php';
	
$sugg = new suggestions();
	
$id_notice = intval($id_notice);
if($id_notice){
    //Pré-remplissage avec les informations de la notice
    $requete = "SELECT tit1 as titre, ed_name as editeur, CONCAT(author_name,' ',author_rejete) as auteur, prix, code
	FROM notices LEFT JOIN responsability ON responsability_notice=notice_id
	LEFT JOIN authors ON responsability_author=author_id LEFT JOIN publishers ON ed1_id=ed_id
	WHERE notice_id=".$id_notice;
    $result = pmb_mysql_query($requete);
    while($row=pmb_mysql_fetch_object($result)){
        $sugg->titre = $row->titre;
        $sugg->editeur = $row->editeur;
        $sugg->auteur = $row->auteur;
        $sugg->code = $row->code;
        if($row->prix) {
            $sugg->prix = $row->prix;
        }
    }
}
	
$sug_form .= "
<div id='make_sugg-container'>
<form action='".$form_action."' method=\"post\" name=\"empr_sugg\" enctype='multipart/form-data'>
	<input type='hidden' name='id_notice' value='!!id_notice!!' />
    <input type=\"hidden\" name=\"lvl\" value=\"valid_sugg\"/>";
	
$btn_valid = "<input type='button' class='bouton' name='ok' value='&nbsp;".addslashes($msg['empr_bt_valid_sugg'])."&nbsp;' onClick='this.form.submit()'/>";
if ($opac_rgaa_active) {
    $sug_form .= "<div class='make_sugg-form-container'>";
    $sug_form .= $sugg->get_content_form();
    $sug_form .= "</div>";
    $sug_form.= "
        <div class='make_sugg-form-buttons align_right'>
            $btn_valid
        </div>
        ";
} else {
    $sug_form .= "
    <table style='width:60%; padding:5px' role='presentation'>
    ".$sugg->get_content_form();
    $sug_form.= "
		<tr>
			<td colspan='2' class='align_right'>
				$btn_valid
			</td>
		</tr>
	</table>";
}
$sug_form.= "
</form>
</div></div>";
	
$sug_form = str_replace('!!id_notice!!',$id_notice,$sug_form);
print $sug_form;
	