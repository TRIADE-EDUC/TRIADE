<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_unsubscribe.inc.php,v 1.2 2021/11/19 09:20:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg;
global $id_bannette, $id_empr;
global $confirmed;

$id_bannette = intval($id_bannette);

require_once($class_path."/search.class.php");
require_once($class_path."/bannette.class.php");
require_once($class_path."/equation.class.php");
require_once($base_path."/includes/bannette_func.inc.php");

if (!$id_bannette) die ("Acc&egrave;s interdit");

// afin de résoudre un pb d'effacement de la variable $id_empr par empr_included, bug à trouver
if (!$id_empr) $id_empr=$_SESSION["id_empr_session"] ;

print "<div id='aut_details' class='aut_details_bannette'>\n";

$query = "select * from bannette_abon where num_empr = '" . $id_empr . "' and num_bannette = '".$id_bannette."'";
$result = pmb_mysql_query($query);
if(pmb_mysql_num_rows($result)) {
	if(isset($confirmed) && $confirmed) {
		pmb_mysql_query("delete from bannette_abon where num_empr = '" . $id_empr . "' and num_bannette = '".$id_bannette."' ");
		print "<span class='bannette_unsubscribe_confirmed'>".$msg['bannette_unsubscribe_confirmed']."</span>";
	} else {
		print "<div class='row'>
				<span class='bannette_unsubscribe_link'>
					<a href='".$base_path."/empr.php?tab=dsi&lvl=bannette_unsubscribe&id_bannette=".$id_bannette."&confirmed=1'>
						".$msg['bannette_unsubscribe_confirm']." 
					</a>
				</span>
			</div>";
	}
}
print "</div><!-- fermeture #aut_details -->\n";	
?>