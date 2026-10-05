<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publication_states.inc.php,v 1.2 2021/03/03 07:57:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;
require_once($class_path."/cms/cms_editorial_publications_state.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");
require_once($class_path."/list/configuration/cms_editorial/list_configuration_cms_editorial_publication_state_ui.class.php");

configuration_controller::set_model_class_name('cms_editorial_publications_state');
configuration_controller::set_list_ui_class_name('list_configuration_cms_editorial_publication_state_ui');
configuration_controller::proceed($id);
