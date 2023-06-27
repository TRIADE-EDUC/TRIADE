<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_question.tpl.php,v 1.6 2019-05-27 15:09:40 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $faq_question_form, $current_module, $msg, $javascript_path, $faq_question_first_desc, $faq_question_other_desc;

$faq_question_form ="
<form method='post' class='form-$current_module' name='faq_question_form' action='!!action!!&action=save'>
	<h3>!!form_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label for='faq_question_type_label'>".$msg['faq_question_type_label']."</label><br/>
				!!type_selector!!
			</div>
			<div class='colonne3'>
				<label for='faq_question_theme_label'>".$msg['faq_question_theme_label']."</label><br/>
				!!theme_selector!!
			</div>
			<div class='colonne-suite'>
				<label for='faq_question_statut'>".$msg['faq_question_statut_label']."</label><br/>
				!!statut_selector!!
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='faq_question_question'>".$msg['faq_question_question']."</label>
		</div>
		<div class='row'>
			<textarea name='faq_question_question' rows='5' >!!question!!</textarea>
		</div>
		<div class='row'>
			<label for='faq_question_question_date'>".$msg['faq_question_question_date']."</label>
		</div>
		<div class='row'>
			<input type='text' name='faq_question_question_date' value='!!question_date!!' placeholder='".$msg['format_date_input_text_placeholder']."'/>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='faq_question_answer'>".$msg['faq_question_answer']."</label>
		</div>
		<div class='row'>
			<textarea name='faq_question_answer' rows='5' >!!answer!!</textarea>
		</div>
		<div class='row'>
				<label for='faq_question_answer_date'>".$msg['faq_question_answer_date']."</label>
			</div>
		<div class='row'>
			<input type='text' name='faq_question_answer_date' value='!!answer_date!!' placeholder='".$msg['format_date_input_text_placeholder']."'/>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
	    	<label for='faq_question_desc'>".$msg['faq_question_desc']."</label>
	    </div>
	    <div class='row'>
	    	!!faq_question_categs!!
	    	<div id='addcateg'/></div>
		</div>
		<div class='row'>&nbsp;</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='hidden' name='faq_question_id' value='!!id!!'/>
			<input type='hidden' name='faq_question_num_demande' value='!!num_demande!!'/>
			<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='!!action!!'\">&nbsp;
			<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
		<div class='right'>
			!!bouton_supprimer!!
		</div>
	</div>
	<div class='row'>&nbsp;</div>
</form>
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
