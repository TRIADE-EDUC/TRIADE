// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_tpl_diff.js,v 1.1 2014/10/14 10:13:43 dgoron Exp $

function serialcirc_tpl_print_add_button(){
	var id_tpl = document.getElementById('id_tpl').value;
	var url= './edit.php?&categ=tpl&sub=serialcirc&action=add_field';
	url+='&id='+id_tpl;
	document.serialcirc_tpl_form.action=url; 
	document.serialcirc_tpl_form.submit();
}
function serialcirc_print_del_button(index){
	var id_tpl = document.getElementById('id_tpl').value;
	var url= './edit.php?&categ=tpl&sub=serialcirc&action=del_field';
	url+='&id='+id_tpl;
	url+='&index='+index;
	document.serialcirc_tpl_form.action=url; 
	document.serialcirc_tpl_form.submit();
}