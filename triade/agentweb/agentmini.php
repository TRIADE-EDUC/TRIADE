<html>
<?php
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
$coloragent=couleurDeFond4($_GET["inc"]);
$ECOLE=ECOLE;
?>
<body bgcolor="#<?php print $coloragent?>">
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="100" height="100" id="Scene1" align="middle" name="Scene1">
<param name="movie" value="/<?php print $ECOLE ?>/agentweb/swf/messageoffline<?php print $_GET["inc"] ?>.swf" />
<param name="bgcolor" value="#<?php print $coloragent ?>" />
<param name="allowScriptAccess" value="sameDomain" />
<param name="quality" value="high" />
<embed src="/<?php print $ECOLE ?>/agentweb/swf/messageoffline<?php print $_GET["inc"]?>.swf" quality="high" bgcolor="#<?php print $coloragent ?>" width="100" height="100" name="Scene1" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" id="Scene1" />
</object>
</body>
</html>
