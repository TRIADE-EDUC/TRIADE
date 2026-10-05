<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: zattr.inc.php,v 1.16 2021/03/12 15:15:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $action;
global $bib_id, $form_attr_bib_id;

require_once($class_path.'/z_attr.class.php');

// gestion des attributs de recherche z3950

?>
<script type="text/javascript">
function test_form(form)
{
	if( (form.form_attr_bib_id.value.length == 0) || (form.form_attr_libelle.value.length == 0) || (form.form_attr_attr.value.length == 0) ) {
		alert("<?php echo $msg['zattr_renseign_lib_et_attr'] ?>");
		return false;
		}
	return true;
}
</script>

<?php
function show_zattr($bib_id) {
	print list_configuration_z3950_zattr_ui::get_instance(array('attr_bib_id' => $bib_id))->get_display_list();
}


$requete = "SELECT bib_nom, base, search_type FROM z_bib where bib_id ='$bib_id' or bib_id='$form_attr_bib_id' ";
$res = pmb_mysql_query($requete);
$row=pmb_mysql_fetch_object($res);
echo "<hr /><strong>$row->bib_nom - $row->base - $row->search_type</strong><hr />";
switch($action) {
	case 'update':
		global $form_attr_libelle;
		if(z_attr::check_data_from_form()) {
			$z_attr = new z_attr($bib_id, stripslashes($form_attr_libelle));
			$z_attr->set_properties_from_form();
			$z_attr->save();
		}
		show_zattr($bib_id);
		break;
	case 'add':
		global $form_attr_libelle, $form_attr_attr;
		if(empty($form_attr_bib_id) || empty($form_attr_libelle) || empty($form_attr_attr)) {
			$z_attr = new z_attr($bib_id);
			$z_attr->libelle = stripslashes($form_attr_libelle);
			$z_attr->attr = stripslashes($form_attr_attr);
			print $z_attr->get_form();
		} else {
			show_zattr($bib_id);
		}
		break;
	case 'modif':
		if($bib_id){
			global $attr_libelle;
			$z_attr = new z_attr($bib_id, stripslashes($attr_libelle));
			if(pmb_error::get_instance('z_attr')->has_error()) {
				pmb_error::get_instance('z_attr')->display(1, static::get_url_base());
			} else {
				print $z_attr->get_form();
			}
		} else {
			show_zattr($bib_id);
		}
		break;
	case 'del':
		global $attr_libelle;
		if (($bib_id) && ($attr_libelle)) {
			z_attr::delete($bib_id);
		}
		show_zattr($bib_id);
		break;
	default:
		show_zattr($bib_id);
		break;
	}
