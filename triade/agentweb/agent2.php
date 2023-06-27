<html>
<?php
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
$coloragent=couleurDeFond3($_GET["inc"]);
$ECOLE=ECOLE;
?>
<body bgcolor='#<?php print $coloragent ?>' >
<?php
$height=150; $width=130;
$fichierswf="/$ECOLE/agentweb/swf/offline.swf";
if ($_GET["message"] == "apropos")  { 
	if (($coloragent == "FFFFFF") || ($coloragent == "FFE664"))  {
		$fichierswf="/$ECOLE/agentweb/swf/apropos.swf"; $height=149; $width=128; 
	}
	if (($coloragent == "cccccc") || ($coloragent == "cba3c6")) {
		$fichierswf="/$ECOLE/agentweb/swf/apropos_2.swf"; $height=150; $width=130;
	}
}
if (($_GET["message"] == "offline") && ($coloragent == "FFFFFF")) {
	$fichierswf="/$ECOLE/agentweb/swf/offline.swf"; $height=150; $width=130;
}
if (($_GET["message"] == "offline") && ($coloragent == "cba3c6")) {
	$fichierswf="/$ECOLE/agentweb/swf/offlinegris.swf"; $height=150; $width=130;
}
if (($_GET["message"] == "offline") && ($coloragent == "cccccc")) {
	$fichierswf="/$ECOLE/agentweb/swf/offlinegris.swf"; $height=150; $width=130;
}
if (($_GET["message"] == "offline") && ($coloragent == "FFE664") ) {
	$fichierswf="/$ECOLE/agentweb/swf/offline.swf"; $height=150; $width=130;
}
	
if ($_GET["message"] == "accueil") {
	if (($coloragent == "FFFFFF") || ($coloragent == "FFE664") ){
		$fichierswf="/$ECOLE/agentweb/swf/accueilnewsBlanc1.swf"; $height=150; $width=130;
	}
	if (($coloragent == "cccccc") || ($coloragent == "cba3c6")) {
		$fichierswf="/$ECOLE/agentweb/swf/accueilnewsGris1.swf"; $height=150; $width=130;
	}
}

if ($_GET["message"] == "problemeacces") {
	if (($coloragent == "FFFFFF") || ($coloragent == "FFE664")) {
		$fichierswf="/$ECOLE/agentweb/swf/melproblemeaccesblanc.swf"; $height=150; $width=130;
	}
	if (($coloragent == "cccccc") || ($coloragent == "cba3c6")) {
		$fichierswf="/$ECOLE/agentweb/swf/melproblemeaccesgris3.swf"; $height=150; $width=130;
	}
}

if ($_GET["message"] == "questionacces") {
	if (($coloragent == "FFFFFF") || ($coloragent == "FFE664")) {
		$fichierswf="/$ECOLE/agentweb/swf/melquestionblanc.swf"; $height=150; $width=130;
	}
	if (($coloragent == "cccccc") || ($coloragent == "cba3c6")) {
		$fichierswf="/$ECOLE/agentweb/swf/melquestiongris2.swf"; $height=150; $width=130;
	}
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
