<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: description.inc.php,v 1.3 2016/11/21 15:51:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$submenu.= "		
			".htmlspecialchars($class_param->descriptions[$quoi],ENT_QUOTES,$charset)."<br />
			<img src='$visionneuse_path/".$class_param->screenshoots[$quoi]."' title='$quoi' alt='$quoi' width='500px'/><br />
			mimetypes support&eacute;s :<br />
			<ul>";

foreach($class_param->classMimetypes[$quoi] as $mimetype){
$submenu.="
				<li>".htmlspecialchars($mimetype,ENT_QUOTES,$charset)."</li>
";	
}
$submenu.="				
			</ul>";
?>
