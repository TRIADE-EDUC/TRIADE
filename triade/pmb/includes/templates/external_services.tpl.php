<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external_services.tpl.php,v 1.4 2021/03/12 13:21:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $es_admin_general, $es_admin_peruser;

//Administration générale
$es_admin_general="
<input type='hidden' name='is_not_first' value='1'/>
!!table_rights!!";

//Par utilisateur
$es_admin_peruser="
<input type='hidden' name='is_not_first' value='1'/>
!!table_rights!!";
?>