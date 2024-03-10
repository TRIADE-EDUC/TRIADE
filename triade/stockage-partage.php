<?php
session_start();
error_reporting(0);

if (empty($_SESSION["nom"]))  {
    header('Location: acces_refuse.php');
    exit;
}

include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

function mimetype($fichier,$quoi) {
	global $mess,$HTTP_USER_AGENT;
	if(!preg_match("/MSIE/i",$HTTP_USER_AGENT)) {$client="netscape.gif";} else {$client="html.gif";}
	if(is_dir($fichier)){$image="dossier.gif";$nom_type=$mess[8];}
	else if(preg_match('/\.mid$/i',$fichier)){$image="mid.gif";$nom_type=$mess[9];}
	else if(preg_match('/\.txt$/i',$fichier)){$image="txt.gif";$nom_type=$mess[10];}
	else if(preg_match('/\.sql$/i',$fichier)){$image="txt.gif";$nom_type=$mess[10];}
	else if(preg_match('/\.js$/i',$fichier)){$image="js.gif";$nom_type=$mess[11];}
	else if(preg_match('/\.gif$/i',$fichier)){$image="gif.gif";$nom_type=$mess[12];}
	else if(preg_match('/\.jpg$/i',$fichier)){$image="jpg.gif";$nom_type=$mess[13];}
	else if(preg_match('/\.html$/i',$fichier)){$image=$client;$nom_type=$mess[14];}
	else if(preg_match('/\.htm$/i',$fichier)){$image=$client;$nom_type=$mess[15];}
	else if(preg_match('/\.rar$/i',$fichier)){$image="rar.gif";$nom_type=$mess[60];}
	else if(preg_match('/\.gz$/i',$fichier)){$image="zip.gif";$nom_type=$mess[61];}
	else if(preg_match('/\.tgz$/i',$fichier)){$image="zip.gif";$nom_type=$mess[61];}
	else if(preg_match('/\.z$/i',$fichier)){$image="zip.gif";$nom_type=$mess[61];}
	else if(preg_match('/\.ra$/i',$fichier)){$image="ram.gif";$nom_type=$mess[16];}
	else if(preg_match('/\.ram$/i',$fichier)){$image="ram.gif";$nom_type=$mess[17];}
	else if(preg_match('/\.rm$/i',$fichier)){$image="ram.gif";$nom_type=$mess[17];}
	else if(preg_match('/\.pl$/i',$fichier)){$image="pl.gif";$nom_type=$mess[18];}
	else if(preg_match('/\.zip$/i',$fichier)){$image="zip.gif";$nom_type=$mess[19];}
	else if(preg_match('/\.wav$/i',$fichier)){$image="wav.gif";$nom_type=$mess[20];}
	else if(preg_match('/\.php$/i',$fichier)){$image="php.gif";$nom_type=$mess[21];}
	else if(preg_match('/\.php$/i',$fichier)){$image="php.gif";$nom_type=$mess[22];}
	else if(preg_match('/\.phtml$/i',$fichier)){$image="php.gif";$nom_type=$mess[22];}
	else if(preg_match('/\.exe$/i',$fichier)){$image="exe.gif";$nom_type=$mess[50];}
	else if(preg_match('/\.bmp$/i',$fichier)){$image="bmp.gif";$nom_type=$mess[56];}
	else if(preg_match('/\.png$/i',$fichier)){$image="gif.gif";$nom_type=$mess[57];}
	else if(preg_match('/\.css$/i',$fichier)){$image="css.gif";$nom_type=$mess[58];}
	else if(preg_match('/\.mp3$/i',$fichier)){$image="mp3.gif";$nom_type=$mess[59];}
	else if(preg_match('/\.xls$/i',$fichier)){$image="xls.gif";$nom_type=$mess[64];}
	else if(preg_match('/\.doc$/i',$fichier)){$image="doc.gif";$nom_type=$mess[65];}
	else if(preg_match('/\.pdf$/i',$fichier)){$image="pdf.gif";$nom_type=$mess[79];}
	else if(preg_match('/\.mov$/i',$fichier)){$image="mov.gif";$nom_type=$mess[80];}
	else if(preg_match('/\.avi$/i',$fichier)){$image="avi.gif";$nom_type=$mess[81];}
	else if(preg_match('/\.mpg$/i',$fichier)){$image="mpg.gif";$nom_type=$mess[82];}
	else if(preg_match('/\.mpeg$/i',$fichier)){$image="mpeg.gif";$nom_type=$mess[83];}
	else if(preg_match('/\.swf$/i',$fichier)){$image="flash.gif";$nom_type=$mess[91];}
	else {$image="defaut.gif";$nom_type=$mess[23];}
	if($quoi=="image"){return $image;} else {return $nom_type;}
}



function taille($fichier) {
	global $size_unit;
	$taille=filesize($fichier);
	if ($taille >= 1073741824) {$taille = round($taille / 1073741824 * 100) / 100 . " G".$size_unit;}
	elseif ($taille >= 1048576) {$taille = round($taille / 1048576 * 100) / 100 . " M".$size_unit;}
	elseif ($taille >= 1024) {$taille = round($taille / 1024 * 100) / 100 . " K".$size_unit;}
	else {$taille = $taille . " ".$size_unit;} 
	if($taille==0) {$taille="-";}
	return $taille;
}


$idpers=$_SESSION["id_pers"];
$membre=$_SESSION["membre"];

echo "<html>\n";
echo "<head><title>$mess[23] : ".$nomdufichier."</title>";
echo "<LINK TITLE='style' TYPE='text/CSS' rel='stylesheet' HREF='./librairie_css/css.css'>";
?>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<?php
echo "</head>\n";
echo "<body id='coulfond1' >";

echo "<table width='100%'>";
echo "<tr><td align='right' >";
echo "<a href=\"stockage-partage.php\" title=\"Réseau / Partage\"><img src=\"image/stockage/reseau.png\" alt=\"Réseau Partage\" border=\"0\"></a>&nbsp;&nbsp;\n";
echo "<a href=\"stockage.php\"><img src=\"image/stockage/hdd.gif\" alt=\"Local\" border=\"0\"></a>&nbsp;&nbsp;\n";
echo "</td></tr></table>";

print "<div style='position:absolute; left:30px; top:45px; border: 2px solid #CCCCCC; background-color: #FFFFFF; border-radius: 10px 10px 10px 10px; width:90%; height:360px; overflow:auto ; box-shadow: 5px 5px 5px grey;'  >";
print "<br>";
print "<font class='T2'>&nbsp;&nbsp;<b>Liste des fichiers partagés : </b><br>";
print "&nbsp;&nbsp;<table width='100%' >";
print "<tr><td height='5' colspan='4' ></td></tr>";
print "<tr><td width='40%' >Fichier</td><td>Taille</td><td>Modifié le</td><td>Télécharger</td></tr>";
print "<tr><td height='5' colspan='4' ></td></tr>";
$data=recupListFichierPartager($idpers,$membre);
// fichier,chemin,membreIdProprio,membreIdAutorise,idclasse,membresource,idsource,id
for($i=0;$i<count($data);$i++) {
	$fichier=$data[$i][0];
	$chemin=$data[$i][1];
	$membresource=$data[$i][5];
	$idsource=$data[$i][6];
	$id=$data[$i][7];

	if (!file_exists("./data/stockage/$membresource/$idsource/$chemin")) {
		suppFichierPartager($id);
		continue;
	}

	print "<tr>";
	print "<td width='5' ><img src='image/stockage/".mimetype("./data/stockage/$membreS/$idpersS/$chemin","image")."' align='bottom' />";
	print "$fichier</td>";
	print "<td>".taille("./data/stockage/$membresource/$idsource/$chemin")."</td>";
	$tmp=filemtime("./data/stockage/$membresource/$idsource/$chemin");
	print "<td>".date("d/m/Y H:i",$tmp)."</td>";
	print "<td><a href='stockage-download.php?id=$id' target='_blank' ><img src='./image/stockage/download.gif' /></a></td>";
	print "</tr>";

}
print "</table>";
print "</font></div>";
?>

<?php Pgclose(); ?>
</body>
</html>
