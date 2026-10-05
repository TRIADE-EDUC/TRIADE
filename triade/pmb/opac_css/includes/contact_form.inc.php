<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form.inc.php,v 1.3 2020/08/11 09:29:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/contact_forms/contact_form.class.php");

if(empty($id)) $id = 1;
$contact_form = new contact_form($id);
print $contact_form->get_form();

