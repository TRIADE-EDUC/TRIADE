<?php
// +--------------------------------------------------------------------------+
// | PMB est sous licence GPL, la réutilisation du code est cadrée            |
// +--------------------------------------------------------------------------+
// $Id: print_dsi.php,v 1.7 2021/12/01 13:09:43 dgoron Exp $

//Impression DSI

$base_path = ".";
$base_auth = "DSI_AUTH";
$base_title = "\$msg[dsi_menu_title]";
$base_nobody=1;
$base_noheader=1;

require($base_path."/includes/init.inc.php");

global $class_path, $include_path, $id_bannette, $charset;

require_once($class_path."/mono_display.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($class_path."/author.class.php");
require_once($class_path."/editor.class.php");
require_once($include_path."/isbn.inc.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/serie.class.php");
require_once($include_path."/explnum.inc.php");
require_once($class_path."/category.class.php");
require_once($class_path."/indexint.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/serial_display.class.php");

include_once("$class_path/bannette.class.php");
include_once("$class_path/equation.class.php");
include_once("$class_path/classements.class.php");
require_once("$class_path/docs_location.class.php");
require_once("./dsi/func_pro.inc.php");
require_once("./dsi/func_common.inc.php");

if (!$id_bannette) die( "<script>self.close();</script>" );

header ("Content-Type: text/html; charset=$charset");

$bannette = new bannette($id_bannette) ;
print pmb_bidi($bannette->get_display_export());

