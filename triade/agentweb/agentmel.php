<html>
<body bgcolor="#FBFFD9" TOPMARGIN=0 LEFTMARGIN=0 MARGINHEIGHT=0 MARGINWIDTH=0 >
<?php
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
$coloragent=couleurDeFond4($_GET["inc"]);
$ECOLE=ECOLE;

$height=100; $width=100;
if ($_GET["mess"] == "M11") { 
       	$fichierswf="/$ECOLE/agentweb/swf/erroracces.swf"; $height=103; $width=97;
}elseif ($_GET["mess"] == "M1") { 
       	$fichierswf="/$ECOLE/agentweb/swf/melmessagelu.swf"; $height=99; $width=99;
}elseif ($_GET["m"] == "M2") {
	$fichierswf="/$ECOLE/agentweb/swf/melcirculaire1.swf"; $height=100; $width=100;
}elseif ($_GET["m"] == "M3") {
	$fichierswf="/$ECOLE/agentweb/swf/melcirculaire2.swf"; $height=100; $width=100;
}elseif ($_GET["m"] == "M5") {
	$fichierswf="/$ECOLE/agentweb/swf/melcomptaconfig1.swf"; $height=100; $width=100;
}elseif ($_GET["m"] == "M6") {
	$fichierswf="/$ECOLE/agentweb/swf/melcomptaconfig2.swf"; $height=100; $width=100;
}elseif ($_GET["m"] == "M7") {
	$fichierswf="/$ECOLE/agentweb/swf/melcomptaconfig3.swf"; $height=100; $width=100;
}elseif ($_GET["mess"] == "M8") {
	$fichierswf="/$ECOLE/agentweb/swf/melsanction.swf"; $height=99; $width=99;
}elseif ($_GET["mess"] == "M9") {
	$fichierswf="/$ECOLE/agentweb/swf/melprofsanction.swf"; $height=100; $width=100;
}elseif ($_GET["m"] == "M12") { 
       	$fichierswf="/$ECOLE/agentweb/swf/piecejointe.swf"; $height=100; $width=100;
}elseif ($_GET["m"] == "M13") { 
       	$fichierswf="/$ECOLE/agentweb/swf/meldocpiecejointe.swf"; $height=100; $width=100;
}else{
        $fichierswf="/$ECOLE/agentweb/swf/miniaideoffline.swf";
}
	
?>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="<?php print $width ?>" height="<?php print $height ?>" id="Scene1" align="middle" name="Scene1">
<param name="movie" value="<?php print $fichierswf ?>" />
<param name="bgcolor" value="#<?php print $coloragent ?>" />
<param name="allowScriptAccess" value="sameDomain" />
<param name="quality" value="high" />
<embed src="<?php print $fichierswf ?>" quality="high" bgcolor="#<?php print $coloragent ?>" width="<?php print $width ?>" height="<?php print $height ?>" name="Scene1" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" id="Scene1" />
</object>
</body>
</html>
