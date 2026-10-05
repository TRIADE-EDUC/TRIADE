<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_question.tpl.php,v 1.7 2023/11/17 14:27:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $faq_question_js_form, $msg, $javascript_path, $faq_question_first_desc, $faq_question_other_desc;

$faq_question_js_form ="
<script type='text/javascript' src='".$javascript_path."/ajax.js'></script>
<script type='text/javascript'>
	ajax_parse_dom();
	function add_categ() {
		templates.add_completion_field('f_categ', 'f_categ_id', 'categories_mul');
    }
    function fonction_selecteur_categ() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,7)+'_id'+name.substr(7);
        openPopUp('./select.php?what=categorie&caller=!!cms_editorial_form_name!!&p1='+name_id+'&p2='+name+'&dyn=1', 'selector_category');
    }
</script>";



$faq_question_first_desc = "
<div class='row'>
<input type='hidden' id='max_categ' name='max_categ' value=\"!!max_categ!!\" />
<input type='text' class='saisie-80emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />

<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=categorie&caller='+this.form.name+'&p1=f_categ_id!!icateg!!&p2=f_categ!!icateg!!&dyn=1&parent=0&deb_rech=', 'selector_category')\" />
<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
<input type='button' class='bouton' value='+' onClick=\"add_categ();\"/>
</div>";
$faq_question_other_desc = "
<div class='row'>
<input type='text' class='saisie-80emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />

<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
</div>";
