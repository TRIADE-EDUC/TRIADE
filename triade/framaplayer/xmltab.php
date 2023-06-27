<?php
include_once('xml.class.php');
//$xmlfile = "http://pyg.keonox.com/flashmp3player/framaplayer/exemple/test3.xml";
if (isset($_GET['xmlloaded'])) {
	$xmlfile = $_GET['xmlloaded'];
	$xmlC = new XmlC();
	$xml_data = file_get_contents( $xmlfile );
	$xmlC->Set_XML_data( $xml_data );
	$playlist = new DisplayPlaylistInfo($xmlC);
	$xmlloaded=true;
} else {
	$xmlloaded=false;
}
?>
<html>
<head>
<link href="framaplayer.css" rel="stylesheet" type="text/css">
<script language="JavaScript"  type="text/javascript" src="framaplayer.js"></script>
</head>
<body>
<?php 
if ($xmlloaded==false) {
	echo "Pas de playlist";
} else {
	echo '<script language="JavaScript" type="text/javascript">'; echo "\n";
	echo "  setInterval('Intervalle()', 300);\n";
	echo '</script>'; echo "\n";

	echo '<div class="small"><a href="xml2m3u.php?xmlloaded='.$_GET['xmlloaded'].'">Génère le .m3u</a></div>';
	echo "<table width=\"99%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bordercolor=\"#000000\">\n";
	echo "  <tr class=\"tabtitle\">\n";
	echo "    <td width=\"4%\" class=\"celltitle\">N&deg;</td>\n";
	echo "    <td width=\"40%\" class=\"celltitle\">Titre</td>\n";
	echo "    <td width=\"28%\" class=\"celltitlec\">Artiste</td>\n";
	echo "    <td width=\"10%\" class=\"celltitlec\">Download</td>\n";
	echo "    <td width=\"15%\" class=\"celltitlec\">Licence</td>\n";
	echo "  </tr>\n";
	echo $playlist->displaySound(); 
	echo "</table>";
}
?>
<div class="small" id="debug">&nbsp;</div>
</body>
</html>