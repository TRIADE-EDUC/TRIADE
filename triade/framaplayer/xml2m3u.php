<?php
include_once('xml.class.php');

if (isset($_GET['xmlloaded'])) {
	$xmlfile = $_GET['xmlloaded'];
	$xmlC = new XmlC();
	$xml_data = file_get_contents( $xmlfile );
	$xmlC->Set_XML_data( $xml_data );
	$playlist = new xml_2_m3u($xmlC);
	$m3u = $playlist->M3UGeneration($playlist);
	$xmlloaded=true;
} else {
	$xmlloaded=false;
}
header('Content-type: application/force-download');
header('Content-Disposition: inline; filename="playlist.m3u"');
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//echo $xml_data;
echo $m3u;
?>