<?php
session_start();
$fichier="./data/parametrage/audio.txt";
if (file_exists($fichier)) {
    $f=fopen($fichier,"r");
    $donnee=fread($f,90000);
	$tab=explode("#||#",$donnee);
    fclose($f);
}
?>
<html>
<head>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./framaplayer/framaplayer.js"></script>
</head>
<body id='cadreCentral0' >
<center>
<!-- <input type="button" value="Stop" id="btnPlayStop" onclick="Playa.doPlayStop();" /> -->
<a href='#'  onMouseOver="AffBulle('<?php print $tab[0]; ?>');"  onMouseOut="HideBulle()";><img src="./image/commun/son.gif" border=0 align=center></a> : <font class=T1 color="#FFFFF"><strong><?php print LANGAUDIO7 ?></strong></font>
<br><br>
<script language="JavaScript" type="text/javascript">
fpa = new Array();
fpa['FlashVars'] = new Array();
fpa['type']='tiny';
fpa['defaultfile']='./data/audio/actu.mp3';
fpa['FlashVars'][0] = 'autolaunch=wait';
Framaplayer(fpa);
</script>
<br><br>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</center>
</body>
</html>
