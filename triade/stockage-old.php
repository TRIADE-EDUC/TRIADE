<?php
exit;
session_start();
error_reporting(0);

if (empty($_SESSION["nom"]))  {
    header('Location: acces_refuse.php');
    exit;
}

$disabled2="";
include_once("./common/config2.inc.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

if (ACCESSTOCKAGE == "non") {
	$access="non"; 
	$disabled2="disabled='disabled'";
}


if (ACCESSTOCKAGE != "oui") {
	if (ACCESSTOCKAGEPROF == "non") {
   		$access="non"; 
		$disabled2="disabled='disabled'";
	}
}

if ($_SESSION["membre"] == "menuprof") {
	if (ACCESSTOCKAGEPROF == "non") {
   		$access="non"; 
		$disabled2="disabled='disabled'";
	}
}

if ($_SESSION["membre"] == "menuscolaire") {
	if (ACCESSTOCKAGECPE == "non") {
   		$access="non"; 
		$disabled2="disabled='disabled'";
	}
}

if ($_SESSION["membre"] == "menuparent") {
	if (ACCESSTOCKAGEPARENT == "non") {
   		$access="non"; 
		$disabled2="disabled='disabled'";
	}
}

if ($_SESSION["membre"] == "menueleve") {
	if (ACCESSTOCKAGEELEVE == "non") {
   		$access="non"; 
		$disabled2="disabled='disabled'";
	}
}


// verif repertoire //
if (!is_dir("./data/stockage")) {
	mkdir("./data/stockage");	
}

//---------------------------------------------------------------------------------------------------
//							
//	WebJeff - FileManager v1.6
//	
//	Jean-François GAZET
//	http://www.webjeff.org
//	webmaster@webjeff.org	
//
//	Modification pour TRIADE 3.0
//
//---------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------------------------------------
//	FONCTIONS
//-----------------------------------------------------------------------------------------------------------------------------------------

function connecte($id)
	{
	global $installurl,$users,$HTTP_REFERER;
	$retour=0;
	if($users==0) {$retour=1;}
	else if($id!="")
		{
		if(file_exists("data/$id.php")) {$retour=1;}
		if(!preg_match("/$installurl/i",$HTTP_REFERER)) {$retour=0;}		
		}
	
	return $retour;
	}

function is_editable($fichier)
	{
	$retour=0;
	if(preg_match("/\.txt$/i",$fichier)) {$retour=1;}
	return $retour;
	}

function is_image($fichier)
	{
	$retour=0;
	if(preg_match("/\.png$|\.bmp$|\.jpg$|\.jpeg$|\.gif$/i",$fichier)) {$retour=1;}
	return $retour;
	}

function creer_id($chemin,$url,$user)
	{
	global $id;
	$taille = 20;
	$lettres = "abcdefghijklmnopqrstuvwxyz0123456789";
	srand(time());
	for ($i=0;$i<$taille;$i++)
		{
		$id .= substr($lettres,(rand()%(strlen($lettres))),1);
	        	}		
	$fp=fopen("data/${id}.php","w");
	if($fp) 
		{
		fputs($fp,"<?php \$racine=\"$chemin\"; \$url_racine=\"$url\"; \$user=\"$user\"; ?>");
		fclose($fp);
		}
	else {exit;}
	}

function taille($fichier)
	{
	global $size_unit;
	$taille=filesize($fichier);
	if ($taille >= 1073741824) {$taille = round($taille / 1073741824 * 100) / 100 . " G".$size_unit;}
	elseif ($taille >= 1048576) {$taille = round($taille / 1048576 * 100) / 100 . " M".$size_unit;}
	elseif ($taille >= 1024) {$taille = round($taille / 1024 * 100) / 100 . " K".$size_unit;}
	else {$taille = $taille . " ".$size_unit;} 
	if($taille==0) {$taille="-";}
	return $taille;
	}

function date_modif($fichier)
	{
	$tmp = filemtime($fichier);
	return date("d/m/Y H:i",$tmp);
	}

function mimetype($fichier,$quoi)
	{
	global $mess,$HTTP_USER_AGENT;
	if(!preg_match("/MSIE/i",$HTTP_USER_AGENT)) {$client="netscape.gif";} else {$client="html.gif";}
	if(is_dir($fichier)){$image="dossier.gif";$nom_type=$mess[8];}
	else if(preg_match("/\.mid$/i",$fichier)){$image="mid.gif";$nom_type=$mess[9];}
	else if(preg_match("/\.txt$/i",$fichier)){$image="txt.gif";$nom_type=$mess[10];}
	else if(preg_match("/\.sql$/i",$fichier)){$image="txt.gif";$nom_type=$mess[10];}
	else if(preg_match("/\.js$/i",$fichier)){$image="js.gif";$nom_type=$mess[11];}
	else if(preg_match("/\.gif$/i",$fichier)){$image="gif.gif";$nom_type=$mess[12];}
	else if(preg_match("/\.jpg$/i",$fichier)){$image="jpg.gif";$nom_type=$mess[13];}
	else if(preg_match("/\.html$/i",$fichier)){$image=$client;$nom_type=$mess[14];}
	else if(preg_match("/\.htm$/i",$fichier)){$image=$client;$nom_type=$mess[15];}
	else if(preg_match("/\.rar$/i",$fichier)){$image="rar.gif";$nom_type=$mess[60];}
	else if(preg_match("/\.gz$/i",$fichier)){$image="zip.gif";$nom_type=$mess[61];}
	else if(preg_match("/\.tgz$/i",$fichier)){$image="zip.gif";$nom_type=$mess[61];}
	else if(preg_match("/\.z$/i",$fichier)){$image="zip.gif";$nom_type=$mess[61];}
	else if(preg_match("/\.ra$/i",$fichier)){$image="ram.gif";$nom_type=$mess[16];}
	else if(preg_match("/\.ram$/i",$fichier)){$image="ram.gif";$nom_type=$mess[17];}
	else if(preg_match("/\.rm$/i",$fichier)){$image="ram.gif";$nom_type=$mess[17];}
	else if(preg_match("/\.pl$/i",$fichier)){$image="pl.gif";$nom_type=$mess[18];}
	else if(preg_match("/\.zip$/i",$fichier)){$image="zip.gif";$nom_type=$mess[19];}
	else if(preg_match("/\.wav$/i",$fichier)){$image="wav.gif";$nom_type=$mess[20];}
	else if(preg_match("/\.php$/i",$fichier)){$image="php.gif";$nom_type=$mess[21];}
	else if(preg_match("/\.php$/i",$fichier)){$image="php.gif";$nom_type=$mess[22];}
	else if(preg_match("/\.phtml$/i",$fichier)){$image="php.gif";$nom_type=$mess[22];}
	else if(preg_match("/\.exe$/i",$fichier)){$image="exe.gif";$nom_type=$mess[50];}
	else if(preg_match("/\.bmp$/i",$fichier)){$image="bmp.gif";$nom_type=$mess[56];}
	else if(preg_match("/\.png$/i",$fichier)){$image="gif.gif";$nom_type=$mess[57];}
	else if(preg_match("/\.css$/i",$fichier)){$image="css.gif";$nom_type=$mess[58];}
	else if(preg_match("/\.mp3$/i",$fichier)){$image="mp3.gif";$nom_type=$mess[59];}
	else if(preg_match("/\.xls$",$fichier)){$image="xls.gif";$nom_type=$mess[64];}
	else if(preg_match("/\.doc$/i",$fichier)){$image="doc.gif";$nom_type=$mess[65];}
	else if(preg_match("/\.pdf$/i",$fichier)){$image="pdf.gif";$nom_type=$mess[79];}
	else if(preg_match("/\.mov$/i",$fichier)){$image="mov.gif";$nom_type=$mess[80];}
	else if(preg_match("/\.avi$/i",$fichier)){$image="avi.gif";$nom_type=$mess[81];}
	else if(preg_match("/\.mpg$/i",$fichier)){$image="mpg.gif";$nom_type=$mess[82];}
	else if(preg_match("/\.mpeg$/i",$fichier)){$image="mpeg.gif";$nom_type=$mess[83];}
	else if(preg_match("/\.swf$/i",$fichier)){$image="flash.gif";$nom_type=$mess[91];}
	else {$image="defaut.gif";$nom_type=$mess[23];}
	if($quoi=="image"){return $image;} else {return $nom_type;}
	}

function init($rep)
	{
	global $racine,$sens,$mess,$font;
	if($rep==""){$nom_rep=$racine;}
	if($sens==""){$sens=1;}
	else
		{
		if($sens==1){$sens=0;}else{$sens=1;}	
		}	
	if($rep!=""){$nom_rep="$racine/$rep";}
	if(!file_exists($racine)) {echo "<font face=\"$font\" size=\"2\">$mess[72]<br><br><a href=\"stockage.php\">$mess[32]</a></font>\n";exit;}
	if(!is_dir($nom_rep)) {echo "<font face=\"$font\" size=\"2\">$mess[76]<br><br><a href=\"javascript:window.history.back()\">$mess[32]</a></font>\n";exit;}
	return $nom_rep;
	}

function assemble_tableaux($t1,$t2)
	{
	global $sens;
	if($sens==0) {$tab1=$t1; $tab2=$t2;} else {$tab1=$t2; $tab2=$t1;}
	if(is_array($tab1)) {while (list($cle,$val) = each($tab1)) {$liste[$cle]=$val;}}
	if(is_array($tab2)) {while (list($cle,$val) = each($tab2)) {$liste[$cle]=$val;}}
	return $liste;
	}


function show_hidden_files($fichier)
	{
	global $showhidden;
	$retour=1;
	if(substr($fichier,0,1)=="." && $showhidden==0) {$retour=0;}
	return $retour;
	}





function inodeTotal($nom_rep) {
	$handle=opendir($nom_rep);
	while ($fichier = readdir($handle)) {
		if  ($fichier!="." && $fichier!=".." && is_dir("$nom_rep/$fichier"))	{
			$inodetotal+=inodeTotal("$nom_rep/$fichier");		
			$inodetotal++;
		}else{
			if($fichier!="." && $fichier!=".." && is_file("$nom_rep/$fichier")) 	{
				$inodetotal++;
			}
		}
		
	}
	closedir($handle);
	return $inodetotal;
}

function VerifInodeTotal($nom_rep) {
	include_once("./common/config2.inc.php");
	$inodetotal=inodeTotal("$nom_rep");		
	if ($inodetotal >= INODESTOCKAGE) {
		return true ;
	}else {
		return false ;
	}
}


function VerifPoidsTotal($nom_rep) {
	$poidstotal=poidstotal("$nom_rep");		
	include_once("./common/config2.inc.php");
	if ($poidstotal > TAILLESTOCKAGE) {
		return true ;
	}else {
		return false ;
	}
}


function poidstotal($nom_rep) {
	global $poidstotal ;
	$handle=opendir($nom_rep);
	while ($fichier = readdir($handle)) {
		if  ($fichier!="." && $fichier!=".." && is_dir("$nom_rep/$fichier"))	{
			$poidstotal+=poidstotal("$nom_rep/$fichier");		
		}else{
			if($fichier!="." && $fichier!="..") 	{
				$poidsfic=filesize("$nom_rep/$fichier");
				$poidstotal+=$poidsfic;
			}
		}
		
	}
	return $poidstotal;
}





function listing($nom_rep)
	{
	global $sens,$ordre,$size_unit;
	$poidstotal=0;
	$handle=opendir($nom_rep);
	while ($fichier = readdir($handle))
		{
		if($fichier!="." && $fichier!=".." && show_hidden_files($fichier)==1) 
			{
			$poidsfic=filesize("$nom_rep/$fichier");
			$poidstotal+=$poidsfic;
			if(is_dir("$nom_rep/$fichier")) 
				{
				if($ordre=="mod") {$liste_rep[$fichier]=filemtime("$nom_rep/$fichier");}
				else {$liste_rep[$fichier]=$fichier;}
				}
			else
				{
				if($ordre=="nom") {$liste_fic[$fichier]=mimetype("$nom_rep/$fichier","image");}
				else if($ordre=="taille") {$liste_fic[$fichier]=$poidsfic;}
				else if($ordre=="mod") {$liste_fic[$fichier]=filemtime("$nom_rep/$fichier");}
				else if($ordre=="type") {$liste_fic[$fichier]=mimetype("$nom_rep/$fichier","type");}
				else {$liste_fic[$fichier]=mimetype("$nom_rep/$fichier","image");}
				}
			}
		}
	closedir($handle);
	
	if(is_array($liste_fic)) 
		{
		if($ordre=="nom") {if($sens==0){ksort($liste_fic);}else{krsort($liste_fic);}}
		else if($ordre=="mod") {if($sens==0){arsort($liste_fic);}else{asort($liste_fic);}}
		else if($ordre=="taille"||$ordre=="type") {if($sens==0){asort($liste_fic);}else{arsort($liste_fic);}}
		else {if($sens==0){ksort($liste_fic);}else{krsort($liste_fic);}}
		}
	if(is_array($liste_rep)) 
		{
		if($ordre=="mod") {if($sens==0){arsort($liste_rep);}else{asort($liste_rep);}}
		else {if($sens==0){ksort($liste_rep);}else{krsort($liste_rep);}}
		}
	
	$liste=assemble_tableaux($liste_rep,$liste_fic);
	if ($poidstotal >= 1073741824) {$poidstotal = round($poidstotal / 1073741824 * 100) / 100 . " G".$size_unit;}
	elseif ($poidstotal >= 1048576) {$poidstotal = round($poidstotal / 1048576 * 100) / 100 . " M".$size_unit;}
	elseif ($poidstotal >= 1024) {$poidstotal = round($poidstotal / 1024 * 100) / 100 . " K".$size_unit;}
	else {$poidstotal = $poidstotal . " ".$size_unit;} 	

	return array($liste,$poidstotal);	
	}

function barre_outil($revenir)
	{
	global $font,$id,$ordre,$sens,$user,$users,$mess,$rep,$allow_change_lang;
	echo "<table width=\"830\" border=0 ><tr><td><b><font face=\"$font\" size=\"2\">\n";
	if($revenir==0) {echo "<img src=\"image/stockage/dossier.gif\" width=\"20\" height=\"20\" align=\"ABSMIDDLE\">\n";}
	echo "<a href=\"";
	if($revenir==1) {echo "stockage.php?id=$id&ordre=$ordre&sens=$sens&rep=$rep";}
	else {echo "stockage.php?id=$id&ordre=$ordre&sens=$sens";}
	echo "\">";
	if($revenir==1) {echo "$mess[32]</a>";}
	else 
		{
		echo "$user</a>";
		$array_chemin=preg_split('/\//',$rep);
		while (list($cle,$val) = each($array_chemin))
			{
			if($val!="") 
				{
				if($addchemin!="") {$addchemin=$addchemin."/".$val;}
				else {$addchemin=$val;}
				echo "/<a href=\"stockage.php?id=$id&ordre=$ordre&sens=$sens&rep=$addchemin\">$val</a>";
				}
			}
		}
	echo "</font></b></td>";
	echo "<td align=\"right\">\n";
	//if($allow_change_lang==1) {echo "<a href=\"stockage.php?action=langue&id=$id&ordre=$ordre&sens=$sens&rep=$rep\"><img src=\"image/stockage/lang.gif\" alt=\"$mess[92]\" border=\"0\"></a>&nbsp;&nbsp;\n";}
	//echo "<a href=\"https://support.triade-educ.org/stockage/index.php?inc=".GRAPH."&URL=".URLSITE."\"><img src=\"image/stockage/hdd.gif\" alt=\"Stockage +\" border=\"0\"></a>&nbsp;&nbsp;\n";
	echo "<a href=\"stockage-partage.php\" title=\"Réseau / Partage\"><img src=\"image/stockage/reseau.png\" alt=\"Réseau Partage\" border=\"0\"></a>&nbsp;&nbsp;\n";
	$http=protohttps(); // return http:// ou https://
	echo "<a href=\"stockage.php\"><img src=\"image/stockage/hdd.gif\" alt=\"Local\" border=\"0\"></a>&nbsp;&nbsp;\n";
	echo "<a href=\"javascript:location.reload()\"><img src=\"image/stockage/refresh.gif\" alt=\"$mess[85]\" border=\"0\"></a>&nbsp;&nbsp;\n";
	echo "<a href=\"stockage.php?action=aide&id=$id&ordre=$ordre&sens=$sens&rep=$rep\"><img src=\"image/stockage/help.gif\" alt=\"$mess[84]\" border=\"0\"></a>&nbsp;&nbsp;\n";
	if($users==1) {echo "<a href=\"stockage.php?action=deconnexion&id=$id\"><img src=\"image/stockage/disconnect.gif\" alt=\"$mess[63]\" border=\"0\"></a>";}
	echo "</td></tr></table><br>\n";
	}

function contenu_dir($nom_rep)
	{
	global $font,$id,$sens,$ordre,$rep,$poidstotal;
	
	// LECTURE DU REPERTOIRE ET CLASSEMENT DES FICHIERS
	list($liste,$poidstotal)=listing($nom_rep);
		
	// AFFICHAGE
	if(is_array($liste))
		{
		while (list($fichier,$mime) = each($liste))
			{
			// DEFINITION DU LIEN SUR LE FICHIER
			if(is_dir("$nom_rep/$fichier"))
				{
				$lien="stockage.php?id=$id&sens=$sens&ordre=$ordre&rep=";
				if($rep!=""){$lien.="$rep/";}
				$lien.=$fichier;
				$affiche_copier="non";
				}
			else 
				{
				$lien="";
				if($rep!=""){$lien.="$rep/";}
				$lien.=$fichier;
				$lien="javascript:popup('$lien')";
				$affiche_copier="oui";
				}
			
			// AFFICHAGE DE LA LIGNE
			echo "<tr>\n";
			echo "<td><font face=\"$font\" size=\"2\">\n";
			if(is_editable($fichier) || is_image($fichier) || is_dir("$nom_rep/$fichier")) {echo "<a href=\"$lien\">";}
			echo "<img src=\"image/stockage/".mimetype("$nom_rep/$fichier","image")."\" width=\"20\" height=\"20\" align=\"ABSMIDDLE\" border=\"0\">";
			echo "<a title=\"$fichier\">".trunchaine($fichier,25)."</a>";
			if(is_editable($fichier) || is_image($fichier) || is_dir("$nom_rep/$fichier")) {echo "</a>\n";}
			echo "</font></td>\n";
			echo "<td width=\"11%\"><font face=\"$font\" size=\"1\">";
			echo taille("$nom_rep/$fichier");
			echo "</font></td>\n";
			echo "<td width=\"15%\"><font face=\"$font\" size=\"1\">";
			echo mimetype("$nom_rep/$fichier","type");
			echo "</font></td>\n";			
			echo "<td width=\"17%\"><font face=\"$font\" size=\"1\">";
			echo date_modif("$nom_rep/$fichier");
			echo "</font></td>\n";
			echo "<td width=\"25%\">";
			
			// IMAGE COPIER
			if($affiche_copier=="oui")
				{
				echo "<a href=\"stockage.php?id=$id&action=copier&sens=$sens&ordre=$ordre&rep=";if($rep!=""){echo "$rep&fic=$rep/";}else{echo "&fic=";}
				echo "$fichier\"><img src=\"image/stockage/copier.gif\" alt=\"$mess[66]\" width=\"20\" height=\"20\" border=\"0\"></a>\n";
				}
			else
				{
				echo "<img src=\"image/stockage/pixel.gif\" width=\"20\" height=\"20\">\n";
				}

			// IMAGE DEPLACER
			if($affiche_copier=="oui")
				{
				echo "<a href=\"stockage.php?id=$id&action=deplacer&ordre=$ordre&sens=$sens&rep=";if($rep!=""){echo "$rep&fic=$rep/";}else{echo "&fic=";}
				echo "$fichier\"><img src=\"image/stockage/deplacer.gif\" alt=\"$mess[70]\" width=\"20\" height=\"20\" border=\"0\"></a>\n";
				 }
			else {echo "<img src=\"image/stockage/pixel.gif\" width=\"20\" height=\"20\">\n";}

			// IMAGE RENOMMER
			echo "<a href=\"stockage.php?id=$id&ordre=$ordre&sens=$sens&action=rename&rep=";if($rep!=""){echo "$rep&fic=$rep/";}else{echo "&fic=";}
			echo "$fichier\"><img src=\"image/stockage/renommer.gif\" alt=\"$mess[6]\" width=\"20\" height=\"20\" border=\"0\"></a>\n";
			
			// IMAGE SUPPRIMER
			echo "<a href=\"stockage.php?id=$id&action=supprimer&ordre=$ordre&sens=$sens&rep=";if($rep!=""){echo "$rep&fic=$rep/";}else{echo "&fic=";}
			echo "$fichier\"><img src=\"image/stockage/supprimer.gif\" alt=\"$mess[7]\" width=\"20\" height=\"20\" border=\"0\"></a>\n";

			// IMAGE EDITER
			if(is_editable($fichier) && !is_dir("$racine/$fichier"))
				{
				echo "<a href=\"stockage.php?id=$id&ordre=$ordre&sens=$sens&action=editer&rep=";if($rep!=""){echo "$rep&fic=$rep/";}else{echo "&fic=";}
				echo "$fichier\"><img src=\"image/stockage/editer.gif\" alt=\"$mess[51]\" width=\"20\" height=\"20\" border=\"0\"></a>\n";
				}
			else { //echo "<img src=\"image/stockage/pixel.gif\" width=\"20\" height=\"20\">\n";
			}
			
			// IMAGE TELECHARGER
			if($affiche_copier=="oui")
				{
				echo "<a href=\"stockage.php?id=$id&action=telecharger&fichier=";
				if($rep!=""){echo "$rep/";}
				echo "$fichier\">";
				echo "<img src=\"image/stockage/download.gif\" alt=\"$mess[88]\" width=\"20\" height=\"20\" border=\"0\"></a>&nbsp;";
					if ( ($_SESSION["membre"] == "menuprof") || ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ) {
						echo "<a href='stockage-partage-fichier.php?fic=$rep/$fichier&fichier=$fichier' title='Partage'><img src=\"image/stockage/partage.png\" /></a>";
					}
				}
							
			echo "</td>\n";				
			echo "</tr>\n";
			}
		}	
	}

function lister_rep($nom_rep)
	{
	// $rep,$sens passes dans l'url
	global $rep,$url_racine,$racine,$mess,$sens,$user,$users,$id,$font,$tablecolor,$ordre,$poidstotal;
	if(preg_match("/\.\./i",$rep)) {$rep="";}
	$nom_rep=init($rep);
	//$base_nom_rep=str_replace($racine,"",$nom_rep);
	//if($base_nom_rep==""){$base_nom_rep="/";}
		
	// AFFICHAGE BARRE DU HAUT (REPERTOIRE COURANT, AIDE, DECONNEXION...)
	if($sens==1){$sens=0;}else{$sens=1;}
	barre_outil(0);
	if($sens==1){$sens=0;}else{$sens=1;}
	
	echo "<script language=\"javascript\">\n";
	echo "function popup(lien) {\n";
	echo "var fen=window.open('stockage.php?id=$id&action=voir&fichier='+lien,'filemanager','status=yes,scrollbars=yes,resizable=yes,width=500,height=400');\n";
	echo "}\n";
	echo "</script>\n";
	echo "<TABLE  width=\"830\" cellspacing=\"0\">\n";
	echo "<tr><td>\n";
	echo "<tr id='coulBar0' >\n";

	// PREMIERE LIGNE DU TABLEAU : Nom du fichier / Taille / Type / Modifié le / Actions
	if($rep!=""){$lien="&rep=".$rep;}
	
	echo "<td><b><a href=\"stockage.php?id=$id&ordre=nom&sens=$sens".$lien."\"><font face=\"$font\" size=\"2\" id='titreStockage' >$mess[1]</font></a>";
	if($ordre=="nom"||$ordre=="") {echo "&nbsp;&nbsp;<img src=\"image/stockage/fleche${sens}.gif\" width=\"10\" height=\"10\">";}
	echo "</b></td>\n";
	echo"<td><b><a href=\"stockage.php?id=$id&ordre=taille&sens=$sens".$lien."\"><font face=\"$font\" size=\"2\" id='titreStockage' >$mess[2]</font></a>";
	if($ordre=="taille") {echo "&nbsp;&nbsp;<img src=\"image/stockage/fleche${sens}.gif\" width=\"10\" height=\"10\">";}
	echo "</b></td>\n";
	echo "<td><b><a href=\"stockage.php?id=$id&ordre=type&sens=$sens".$lien."\"><font face=\"$font\" size=\"2\" id='titreStockage' >$mess[3]</font></a>";
	if($ordre=="type") {echo "&nbsp;&nbsp;<img src=\"image/stockage/fleche${sens}.gif\" width=\"10\" height=\"10\">";}
	echo "</b></td>\n";	
	echo "<td><b><a href=\"stockage.php?id=$id&ordre=mod&sens=$sens".$lien."\"><font face=\"$font\" size=\"2\" id='titreStockage' >$mess[4]</font></a>\n";
	if($ordre=="mod") {echo "&nbsp;&nbsp;<img src=\"image/stockage/fleche${sens}.gif\" width=\"10\" height=\"10\">";}
	echo "</b></td>\n";	
	echo "<td align=\"center\"  ><b><font face=\"$font\" size=\"2\"  id='titreStockage' >$mess[5]</font></b></td>\n";	
	echo "</tr>\n";

	if($sens==1){$sens=0;}else{$sens=1;}
	
	// LIEN DOSSIER PARENT
	if($rep!="")
		{
		$nom=dirname($rep);
		echo "<tr ><td><a href=\"stockage.php?id=$id&sens=$sens&ordre=$ordre";
		if($rep!=$nom && $nom!="."){echo "&rep=$nom";}
		echo "\"><img src=\"image/stockage/parent.gif\" width=\"20\" height=\"20\" align=\"ABSMIDDLE\" border=\"0\"><font face=\"$font\" size=\"2\">$mess[24]</font></a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
		}
		
	contenu_dir($nom_rep);
	
	include_once("./common/config2.inc.php");
	$tailleMax=TAILLESTOCKAGE;

	echo "<tr><td colspan=\"5\"><hr width=\"830\" align=\"center\"></td></tr>\n";
	echo "<tr>\n";
	echo "<td >&nbsp;</td>\n";
	echo "<td width=\"11%\"><font face=\"$font\" size=\"1\">$poidstotal</font></td>\n";
	echo "<td width=\"17%\">&nbsp;</td>\n";
	echo "<td width=\"25%\" colspan=2 >$mess[97] : ".poidstotal($racine)." /  $tailleMax  </td>\n";
	echo "</tr>\n";
	echo "</table>\n<br>";		
	}

function deldir($location) 
	{ 
	if(is_dir($location))
		{
		$all=opendir($location); 
		while ($file=readdir($all)) 
			{ 
			if (is_dir("$location/$file") && $file !=".." && $file!=".") 
				{ 
				deldir("$location/$file"); 
				if(file_exists("$location/$file")){rmdir("$location/$file"); }
				unset($file); 
				}
			elseif (!is_dir("$location/$file"))
				{ 
				if(file_exists("$location/$file")){unlink("$location/$file"); }
				unset($file); 
				} 
			} 
		closedir($all);
		rmdir($location);
		}
	else 
		{
		if(file_exists("$location")) {unlink("$location");}
		}
	}

function enlever_controlM($fichier)
	{
	$fic=file($fichier);
	$fp=fopen($fichier,"w");
	while (list ($cle, $val) = each ($fic)) 
		{
		$val=str_replace(CHR(10),"",$val);
		$val=str_replace(CHR(13),"",$val);
		fputs($fp,"$val\n");
		}
	fclose($fp);
	}

function traite_nom_fichier($nom)
	{
	$nom=stripslashes($nom);
	$nom=str_replace("'","",$nom);
	$nom=str_replace("\"","",$nom);
	$nom=str_replace("\"","",$nom);
	$nom=str_replace("&","",$nom);
	$nom=str_replace(",","",$nom);
	$nom=str_replace(";","",$nom);
	$nom=str_replace("/","",$nom);
	$nom=str_replace("\\","",$nom);
	$nom=str_replace("`","",$nom);
	$nom=str_replace("<","",$nom);
	$nom=str_replace(">","",$nom);
	$nom=str_replace(" ","_",$nom);
	$nom=str_replace(":","",$nom);
	$nom=str_replace("*","",$nom);
	$nom=str_replace("|","",$nom);
	$nom=str_replace("?","",$nom);
	$nom=str_replace("é","",$nom);
	$nom=str_replace("è","",$nom);
	$nom=str_replace("ç","",$nom);
	$nom=str_replace("@","",$nom);
	$nom=str_replace("â","",$nom);
	$nom=str_replace("ê","",$nom);
	$nom=str_replace("î","",$nom);
	$nom=str_replace("ô","",$nom);
	$nom=str_replace("û","",$nom);
	$nom=str_replace("ù","",$nom);
	$nom=str_replace("à","",$nom);
	$nom=str_replace("!","",$nom);
	$nom=str_replace("§","",$nom);
	$nom=str_replace("+","",$nom);
	$nom=str_replace("^","",$nom);
	$nom=str_replace("(","",$nom);
	$nom=str_replace(")","",$nom);
	$nom=str_replace("#","",$nom);
	$nom=str_replace("=","",$nom);
	$nom=str_replace("$","",$nom);	
	$nom=str_replace("%","",$nom);
	$nom = substr ($nom,0,250);
 	return $nom;
 	}


//-----------------------------------------------------------------------------------------------------------------------------------------
//	MAIN
//-----------------------------------------------------------------------------------------------------------------------------------------

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
include_once("./common/lib_stockage.php");
include_once("./common/config.inc.php");

$id="";
$ordre="";
$sens="";
$rep="";
$langue="";
$action="";
$nom_rep="";


if (isset($_POST["id"])) {$id=$_POST["id"]; }
if (isset($_POST["ordre"])) {$ordre=$_POST["ordre"]; }
if (isset($_POST["sens"])) {$sens=$_POST["sens"]; }
if (isset($_POST["rep"])) {$rep=$_POST["rep"]; }
if (isset($_POST["langue"])) {$langue=$_POST["langue"]; }


if (isset($_GET["id"]))     {$id=$_GET["id"]; }
if (isset($_GET["ordre"]))  {$ordre=$_GET["ordre"]; }
if (isset($_GET["sens"]))   {$sens=$_GET["sens"]; }
if (isset($_GET["rep"]))    {$rep=$_GET["rep"]; }
if (isset($_GET["langue"])) {$langue=$_GET["langue"]; }


$racine="./data/stockage/".$_SESSION["membre"]."/";
if (!is_dir($racine)) {
	mkdir($racine,0755);
}

if ($_SESSION["membre"] == "menuprof") {
	$id_pers=$_SESSION["id_suppleant"];
	$cnx=cnx();
	if (!verif_si_compte_suppleant($id_pers)) {
	        $id_pers=$_SESSION["id_pers"];
	}
	pgClose();
}else{
	$id_pers=$_SESSION["id_pers"];
}


$racine="./data/stockage/".$_SESSION["membre"]."/".$id_pers."/";
if (!is_dir($racine)) {
	mkdir($racine,0755);
}

$http=protohttps(); // return http:// ou https://
$url_racine="$http".$_SERVER["SERVER_NAME"]."/".ECOLE."/data/stockage/".$_SESSION["membre"]."/".$id_pers;
$langueSESSION=$_SESSION["langue"];
if ($langueSESSION == "")   { 
	$langue="fr" ; 
}elseif($langueSESSION == "es") { 
	$langue="es" ; 
}elseif($langueSESSION == "fr") { 
	$langue="fr" ; 
}elseif($langueSESSION == "en") { 
	$langue="en" ; 
}else{
	$langue="fr" ; 
}

include_once("include/${langue}.php");
if(file_exists("data/$id.php")) {include_once("data/$id.php");}

if (isset($_POST["action"])) {	$action=$_POST["action"]; }
if (isset($_GET["action"])) { $action=$_GET["action"]; }

switch($action) {


//-----------------------------------------------------------------------------------------------------------------------------------------
//	AIDE / HELP
//-----------------------------------------------------------------------------------------------------------------------------------------


case "aide";
include($hautpage);
barre_outil(1);
include("include/${langue}_help.html");
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	LANGUE / LANGUAGE
//-----------------------------------------------------------------------------------------------------------------------------------------

case "langue";
setcookie("cookie_test","ok",time()+3600);  // 1 an
include($hautpage);
barre_outil(1);
echo "<center><font face=\"$font\" size=\"2\">$mess[95]</font></center><br>\n";
echo "<table width=\"500\" border=\"0\" cellspacing=\"20\" cellpadding=\"0\" align=\"center\">\n";
echo "<tr align=\"center\">\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=fr\"><img src=\"image/stockage/lang_fr.gif\" width=\"45\" height=\"30\" alt=\"Fran&ccedil;ais\" border=\"0\"><br>Fran&ccedil;ais</a></font>"; if($langue=="fr") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=en\"><img src=\"image/stockage/lang_en.gif\" width=\"60\" height=\"30\" alt=\"English\" border=\"0\"><br>English</a></font>"; if($langue=="en") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=de\"><img src=\"image/stockage/lang_de.gif\" width=\"50\" height=\"30\" alt=\"Deutch\" border=\"0\"><br>Deutch</a></font>"; if($langue=="de") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "</tr>\n";
echo "<tr align=\"center\">\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=ee\"><img src=\"image/stockage/lang_ee.gif\" width=\"47\" height=\"30\" alt=\"Estonian\" border=\"0\"><br>Estonian</a></font>"; if($langue=="ee") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=it\"><img src=\"image/stockage/lang_it.gif\" width=\"45\" height=\"30\" alt=\"Italian\" border=\"0\"><br>Italian</a></font>"; if($langue=="it") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=hun\"><img src=\"image/stockage/lang_hun.gif\" width=\"45\" height=\"30\" alt=\"Hungary\" border=\"0\"><br>Hungary</a></font>"; if($langue=="hun") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "</tr>\n";
echo "<tr align=\"center\">\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=rs\"><img src=\"image/stockage/lang_rs.gif\" width=\"45\" height=\"30\" alt=\"Russian\" border=\"0\"><br>Russian</a></font>"; if($langue=="rs") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=sk\"><img src=\"image/stockage/lang_sk.gif\" width=\"45\" height=\"30\" alt=\"Slovak\" border=\"0\"><br>Slovak</a></font>"; if($langue=="sk") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=pl\"><img src=\"image/stockage/lang_pl.gif\" width=\"48\" height=\"30\" alt=\"Poland\" border=\"0\"><br>Poland</a></font>"; if($langue=="pl") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "</tr>\n";
echo "<tr align=\"center\">\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=lt\"><img src=\"image/stockage/lang_lt.gif\" width=\"45\" height=\"30\" alt=\"Lithuanian\" border=\"0\"><br>Lithuanian</a></font>"; if($langue=="lt") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "<td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?action=savelangue&id=$id&ordre=$ordre&sens=$sens&rep=$rep&langue=cn\"><img src=\"image/stockage/lang_cn.gif\" width=\"45\" height=\"30\" alt=\"Chinese\" border=\"0\"><br>Chinese</a></font>"; if($langue=="cn") {echo "<img src=\"image/stockage/check.gif\" align=\"ABSMIDDLE\">";} echo "</td>\n";
echo "<td>&nbsp;</td>\n";
echo "</tr>\n";
echo "</table>\n";
break;

case "savelangue";
if($cookie_test!="ok")
	{
	include($hautpage);
	echo "<center><font face=\"$font\" size=\"2\">$mess[93]<br><br><a href=\"stockage.php?action=langue&id=$id&ordre=$ordre&sens=$sens&rep=$rep\">$mess[32]</a></font></center>\n";
	}
else
	{
	$langue=$_GET["langue"];
	setcookie("langue",$langue,time()+31536000);  // 1 an
	header("Location:stockage.php?action=langue&id=$id&ordre=$ordre&sens=$sens&rep=$rep");
	exit;
	}	
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	TELECHARGER / DOWNLOAD
//-----------------------------------------------------------------------------------------------------------------------------------------

case "telecharger";
$fichier=$_GET["fichier"];
$NomFichier = basename($fichier);
$taille=filesize("$racine/$fichier");
include_once("./common/config2.inc.php");
header("Content-Type: application/force-download; name=\"$NomFichier\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $taille");
header("Content-Disposition: attachment; filename=\"$NomFichier\"");
if (HTTPS == "oui") {
	header("Cache-Control: public"); 
	header("Pragma:"); 
	header("Expires: 0");
}else{
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
}
readfile("$racine/$fichier");
exit(); 
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	EDITER / EDIT
//-----------------------------------------------------------------------------------------------------------------------------------------

case "editer";
if(!connecte($id)) {header("Location:stockage.php");exit;}
include($hautpage);
$fic=$_GET["fic"];
$code=$_POST["code"];
//$code=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#',' ',$code);
$code=preg_replace('/\s\s+/', ' ', $code);
$code=str_replace("\\r\\n","\n",$code);
$code=stripslashes($code);
$save=$_POST["save"];
if($save==1)
	{
	$fic=$_POST["fic"];
	$code=str_replace("&lt;","<",$code);
	$fp=fopen("$racine/$fic","w");
	fputs ($fp,$code);
	fclose($fp);
	enlever_controlM("$racine/$fic");
	}
echo "<center>\n";
echo "<font face=\"$font\" size=\"2\">$mess[52] <b>$fic</b></font><br>";
echo "<form action=\"stockage.php\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
echo "<input type=\"hidden\" name=\"fic\" value=\"$fic\">\n";
echo "<input type=\"hidden\" name=\"rep\" value=\"$rep\">\n";
echo "<input type=\"hidden\" name=\"save\" value=\"1\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"editer\">\n";
echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">\n";
echo "<input type=\"hidden\" name=\"sens\" value=\"$sens\">\n";
echo "<TEXTAREA NAME=\"code\" rows=\"25\" cols=\"130\" wrap=\"OFF\">\n";
$fp=fopen("$racine/$fic","r");
while (!feof($fp)) 
	{ 
	$tmp=fgets($fp,4096);
	$tmp=str_replace("<","&lt;",$tmp);
	$tmp=str_replace("\\r\\n","\n",$tmp);
	echo "$tmp";
	}
fclose($fp);
echo "$fichier";
echo "</TEXTAREA>\n";
echo "<br><br>\n";
if (!VerifPoidsTotal($racine)) {
	echo "<input type=\"image\" src=\"image/stockage/enregistrer.gif\" alt=\"$mess[53]\" border=\"0\">\n";
}
echo "<a href=\"stockage.php?id=$id&rep=$rep&ordre=$ordre&sens=$sens\"><img src=\"image/stockage/fermer.gif\" alt=\"$mess[86]\" border=\"0\"></a>\n";
echo "</form>\n";
echo "</center>\n";
break;



//-----------------------------------------------------------------------------------------------------------------------------------------
//	COPIER / COPY
//-----------------------------------------------------------------------------------------------------------------------------------------

case "copier";
if(!connecte($id)) {header("Location:stockage.php");exit;}
include($hautpage);
$dest=trim($_GET["dest"]);
$fic=trim($_GET["fic"]);
echo "<center>\n";
echo "<table>\n";
echo "<tr><td><font face=\"$font\" size=\"2\"><img src=\"image/stockage/copier.gif\" width=\"20\" height=\"20\" align=\"ABSMIDDLE\"> $mess[67] : </font></td><td><font face=\"$font\" size=\"2\"><b>$fic</b></font></td></tr>\n";
echo "<tr><td><font face=\"$font\" size=\"2\"><img src=\"image/stockage/coller.gif\" width=\"20\" height=\"20\" align=\"ABSMIDDLE\"> $mess[68] : </font></td><td><font face=\"$font\" size=\"2\">";
if($dest=="") {echo "/";} else {echo "$dest";}
echo "</font></td></tr>\n";
echo "</table>\n";

echo "<br><font face=\"$font\" size=\"2\">$mess[69] :</font><br>\n";

echo "<table>";
$handle=opendir("$racine/$dest");
while ($fichier = readdir($handle))
	{
	if($fichier=="..")
		{
		$up=dirname($dest);
		if($up==$dest || $up==".") {$up="";}
		if($up!=$dest) 
			{
			echo "<td><img src=\"image/stockage/parent.gif\"></td><td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?id=$id&action=copier&ordre=$ordre&sens=$sens&dest=$up&fic=$fic&rep=$rep\">$mess[24]</font></td>";
			}
		}	
	else if($fichier!=".." && $fichier!="." && is_dir("$racine/$dest/$fichier")) {$liste_dir[]=$fichier;}
	}
closedir($handle);
if(is_array($liste_dir)) 
	{
	asort($liste_dir);
	while (list($cle,$val) = each($liste_dir))
		{
		echo "<tr><td><img src=\"image/stockage/dossier.gif\"></td><td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?id=$id&action=copier&dest=";
		if($dest!="") {echo "$dest/";}
		echo "$val&rep=$rep&ordre=$ordre&sens=$sens&fic=$fic\">$val</a></font></tr>\n";		
		}	
	}
echo "</table><br>";

echo "<table>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<form action=\"stockage.php\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"copier_suite\">\n";
echo "<input type=\"hidden\" name=\"fic\" value=\"$fic\">\n";
echo "<input type=\"hidden\" name=\"dest\" value=\"$dest\">\n";
echo "<input type=\"hidden\" name=\"rep\" value=\"$rep\">\n";
echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
echo "<input type=\"hidden\" name=\"sens\" value=\"$sens\">\n";
echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">\n";
echo "<input type=\"submit\" class='bouton2' value=\"&nbsp;&nbsp;&nbsp;ok&nbsp;&nbsp;\">&nbsp;\n";
echo "</form>\n";
echo "</td>\n";
echo "<td>\n";
echo "<form action=\"stockage.php\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
echo "<input type=\"hidden\" name=\"rep\" value=\"$rep\">\n";
echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">\n";
echo "<input type=\"hidden\" name=\"sens\" value=\"$sens\">\n";
echo "<input type=\"submit\" class='bouton2' value=\"$mess[54]\">\n";
echo "</form>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</center>\n";	
break;

case "copier_suite";
if(!connecte($id)) {header("Location:stockage.php");exit;}
$fic=$_POST["fic"];
$dest=$_POST["dest"];
$destination="$racine/";
if($dest!="") {$destination.="$dest/";}
$destination.=basename($fic);
if(file_exists("$racine/$fic") && "$racine/$fic"!=$destination) {copy("$racine/$fic",$destination);}
header("Location:stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens");
exit;
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	VOIR UN FICHIER
//-----------------------------------------------------------------------------------------------------------------------------------------

case "voir";
$fichier=$_GET["fichier"];
$nomdufichier=basename($fichier);
echo "<html>\n";
echo "<head><title>$mess[23] : ".$nomdufichier."</title>";
echo "<LINK TITLE='style' TYPE='text/CSS' rel='stylesheet' HREF='./librairie_css/css.css'>";
echo "</head>\n";

$fp=@fopen("$hautpage","r");
if($fp)
	{
	while(!feof($fp))
		{
		$buffer=fgets($fp,4096);
		if(preg_match("/<body/i",$buffer)) 
			{
			$tmp=preg_split("/</",$buffer);
			while (list($cle,$val) = each($tmp))
				{
				if(preg_match("/body/i",$val)) 
					{
					$val=str_replace(">","",$val);
					$val=str_replace(CHR(10),"",$val);
					$val=str_replace(CHR(13),"",$val);					
					echo "<$val onload=\"self.focus()\">\n";
					}
				}
			break;
			}
		}
	fclose($fp);
	}
echo "<center><font face=\"$font\" size=\"2\">$mess[23] : ";
echo "<img src=\"image/stockage/".mimetype("$racine/$fichier","image")."\" align=\"ABSMIDDLE\">\n";
echo "<b>".$nomdufichier."</b></font><br><br><hr>\n";
echo "<a href=\"javascript:window.print()\"><img src=\"image/stockage/imprimer.gif\" alt=\"$mess[90]\" border=\"0\"></a>\n";
echo "<a href=\"javascript:window.close()\"><img src=\"image/stockage/fermer.gif\" alt=\"$mess[86]\" border=\"0\"></a>\n";
echo "<br>\n";
echo "<hr><br>";
if(!is_image($fichier)) 
	{
	echo "</center>\n";
	$fp=@fopen("$racine/$fichier","r");
	if($fp)
		{
		echo "<font face=\"$font\" size=\"1\">\n";
		while(!feof($fp))
			{
			$buffer=fgets($fp,4096);
			$buffer=txt_vers_html($buffer);
			$buffer=str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$buffer);
			echo $buffer."<br>";
			}
		fclose($fp);
		echo "</font>\n";
		}
	else
		{
		echo "<font face=\"$font\" size=\"2\">$mess[89] : $racine/$fichier</font>";
		}
	echo "<center>\n";
	}
else
	{
	echo "<img src=\"visu_image.php?fic=$racine$fichier\">\n";
	}
echo "<hr>\n";
echo "<a href=\"javascript:window.print()\"><img src=\"image/stockage/imprimer.gif\" alt=\"$mess[90]\" border=\"0\"></a>\n";
echo "<a href=\"javascript:window.close()\"><img src=\"image/stockage/fermer.gif\" alt=\"$mess[86]\" border=\"0\"></a>\n";
echo "<hr></center>\n";
echo "</body>\n";
echo "</html>\n";
exit;
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	DEPLACER / MOVE
//-----------------------------------------------------------------------------------------------------------------------------------------

case "deplacer";
if(!connecte($id)) {header("Location:stockage.php");exit;}
include($hautpage);
echo "<center>\n";
echo "<table>\n";
echo "<tr><td><font face=\"$font\" size=\"2\"><img src=\"image/stockage/couper.gif\" width=\"20\" height=\"20\" align=\"ABSMIDDLE\"> $mess[67] : </font></td><td><font face=\"$font\" size=\"2\"><b>$fic</b></font></td></tr>\n";
echo "<tr><td><font face=\"$font\" size=\"2\"><img src=\"image/stockage/coller.gif\" width=\"20\" height=\"20\" align=\"ABSMIDDLE\"> $mess[68] : </font></td><td><font face=\"$font\" size=\"2\">";
if($dest=="") {echo "/";} else {echo "$dest";}
echo "</font></td></tr>\n";
echo "</table>\n";
echo "<br><font face=\"$font\" size=\"2\">$mess[69] :</font><br>\n";
echo "<table>";
$handle=opendir("$racine/$dest");
while ($fichier = readdir($handle))
	{
	if($fichier=="..")
		{
		$up=dirname($dest);
		if($up==$dest || $up==".") {$up="";}
		if($up!=$dest) 
			{
			echo "<td><img src=\"image/stockage/parent.gif\"></td><td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?id=$id&ordre=$ordre&sens=$sens&action=deplacer&dest=$up&fic=$fic&rep=$rep\">$mess[24]</font>";
			}
		}	
	else if($fichier!=".." && $fichier!="." && is_dir("$racine/$dest/$fichier")) {$liste_dir[]=$fichier;}
	}
closedir($handle);
if(is_array($liste_dir)) 
	{
	asort($liste_dir);
	while (list($cle,$val) = each($liste_dir))
		{
		echo "<tr><td><img src=\"image/stockage/dossier.gif\"></td><td><font face=\"$font\" size=\"2\"><a href=\"stockage.php?id=$id&action=deplacer&dest=";
		if($dest!="") {echo "$dest/";}
		echo "$val&rep=$rep&ordre=$ordre&sens=$sens&fic=$fic\">$val</a></font></tr>\n";		
		}	
	}
echo "</table><br>";
echo "<table>\n";
echo "<tr>\n";
echo "<td>\n";
echo "<form action=\"stockage.php\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"deplacer_suite\">\n";
echo "<input type=\"hidden\" name=\"fic\" value=\"$fic\">\n";
echo "<input type=\"hidden\" name=\"dest\" value=\"$dest\">\n";
echo "<input type=\"hidden\" name=\"rep\" value=\"$rep\">\n";
echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">\n";
echo "<input type=\"hidden\" name=\"sens\" value=\"$sens\">\n";
echo "<input type=\"submit\" class='bouton2' value=\"&nbsp;&nbsp;&nbsp;ok&nbsp;&nbsp;\">&nbsp;\n";
echo "</form>\n";
echo "</td>\n";
echo "<td>\n";
echo "<form action=\"stockage.php\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
echo "<input type=\"hidden\" name=\"rep\" value=\"$rep\">\n";
echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">\n";
echo "<input type=\"hidden\" name=\"sens\" value=\"$sens\">\n";
echo "<input type=\"submit\" class='bouton2' value=\"Annuler\">\n";
echo "</form>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</center>\n";
break;

case "deplacer_suite";
if(!connecte($id)) {header("Location:stockage.php");exit;}
$destination="$racine/";
if($dest!="") {$destination.="$dest/";}
$destination.=basename($fic);
if(file_exists("$racine/$fic") && "$racine/$fic"!=$destination) {copy("$racine/$fic",$destination);}
if("$racine/$fic"!=$destination) {if(file_exists("$racine/$fic")) {unlink("$racine/$fic");}}
header("Location:stockage.php?rep=$rep&ordre=$ordre&sens=$sens&id=$id");
exit;
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	SUPPRIMER / DELETE
//-----------------------------------------------------------------------------------------------------------------------------------------

case "supprimer";
if(!connecte($id)) {header("Location:stockage.php");exit;}
include($hautpage);
echo "<center>\n";
$fic=$_GET["fic"];
if(is_dir("$racine/$fic")){$mime=$mess[45];}else{$mime=$mess[46];}
echo "<font face=\"$font\" size=\"2\">$mess[47] $mime <b>$fic</b> ?";
echo "<br><br>";
echo "<a href=\"stockage.php?action=supprimer_suite&rep=$rep&fic=$fic&id=$id&ordre=$ordre&sens=$sens\">$mess[48]</a>&nbsp;&nbsp;&nbsp;\n";
echo "<a href=\"stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens\">$mess[49]</a>\n";
echo "</font><br>";
echo "</center>\n";
break;

case "supprimer_suite";
if (!connecte($id)) { header("Location:stockage.php");exit;}
if (!isset($_GET["fic"])) { header("Location:stockage.php");exit;} 
$_GET["fic"]=preg_replace('/\.\./',"x",$_GET["fic"]);
if ($_GET["fic"] == "..") { header("Location:stockage.php");exit;} 
$messtmp="<font face=\"$font\" size=\"2\">";
$fic=$_GET["fic"];
$a_effacer="$racine/$fic";

if(file_exists($a_effacer)) {
	if(is_dir($a_effacer)){ deldir($a_effacer); $messtmp.="$mess[38] <b>$fic</b> $mess[44].";}
	else {unlink($a_effacer); $messtmp.="$mess[34] <b>$fic</b> $mess[44].";}
	}
else { $messtmp.=$mess[76];}
$messtmp.="<br><br><a href=\"stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens\">$mess[32]</a>";
$messtmp.="</font>";
header("Location:stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens");
exit;
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	RENOMMER / RENAME
//-----------------------------------------------------------------------------------------------------------------------------------------

case "rename";
if(!connecte($id)) {header("Location:stockage.php");exit;}
include($hautpage);
echo "<br><br><center>\n";
$fic=$_GET["fic"];
$nom_fic=basename($fic);
echo "<font face=\"$font\" size=\"2\">";
echo "<form action=\"stockage.php\" method=\"post\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"rename_suite\">\n";
echo "<input type=\"hidden\" name=\"rep\" value=\"$rep\">\n";
echo "<input type=\"hidden\" name=\"fic\" value=\"$fic\">\n";
echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">\n";
echo "<input type=\"hidden\" name=\"sens\" value=\"$sens\">\n";
echo "$mess[6] <b>$nom_fic</b> $mess[42] ";
echo "<input type=\"text\" name=\"fic_new\" value=\"$nom_fic\">\n";
echo "<input type=\"submit\" class='bouton2' value=\"$mess[6]\">\n";
echo "</form>";
echo "<a href=\"stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens\">$mess[32]</a>";	
echo "</font><br>";
echo "</center>\n";
break;

case "rename_suite";
if(!connecte($id)) {header("Location:stockage.php");exit;}
$err="";
$fic_new=trim($_POST["fic_new"]);
$fic=$_POST["fic"];
$nom_fic=basename($fic);
$messtmp="<font face=\"$font\" size=\"2\">";
$fic_new=traite_nom_fichier($fic_new);
$old="$racine/$fic";
$new=dirname($old)."/".$fic_new;
if (!VerifInodeTotal($racine)) {
	if($fic_new=="") {
		$messtmp.="$mess[37]"; $err=1;
	}else if(file_exists($new)) {
		$messtmp.="<b>$fic_new</b> $mess[43]"; $err=1;
	}else{
		if(file_exists($old)) { 
			if (!preg_match('/\.txt$/',$new)) $new=$new.".txt";	
			rename($old,$new); 
		}
		$messtmp.="<b>$fic</b> $mess[41] <b>$fic_new</b>";
	}
	
	$messtmp.="<br><br><a href=\"stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens\">$mess[32]</a>";	
	$messtmp.="</font>";
}
if($err=="") {header("Location:stockage.php?rep=$rep&ordre=$ordre&sens=$sens&id=$id");exit;}
include($hautpage);
echo "<center>\n";
echo "$messtmp";
echo "</center>\n";
break;

	
//-----------------------------------------------------------------------------------------------------------------------------------------
//	CREER UN REPERTOIRE / CREATE DIR
//-----------------------------------------------------------------------------------------------------------------------------------------

case "mkdir";
if(!connecte($id)) {header("Location:stockage.php");exit;}
$err="";
$messtmp="<font face=\"$font\" size=\"2\">";
$nomdir=trim($_POST["nomdir"]);
$nomdir=preg_replace('/\./',"x",$nomdir);
$nomdir=traite_nom_fichier($nomdir);
if (!VerifInodeTotal($racine)) {
if($nomdir=="")
	{
	$messtmp.="$mess[37]"; $err=1;
	}
else if(file_exists("$racine/$rep/$nomdir"))
	{
	$messtmp.="$mess[40]"; $err=1;
	}
else
	{
	mkdir("$racine/$rep/$nomdir",0775);
	$messtmp.="$mess[38] <b>$nomdir</b> $mess[39] <b>";
	if($rep=="") {$messtmp.="/";} else {$messtmp.="$rep";}
	$messtmp.="</b>";
	}
$messtmp.="<br><br><a href=\"stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens\">$mess[32]</a>";
$messtmp.="</font>";
}
if($err=="") {header("Location:stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens");exit;}
include($hautpage);
echo "<center>\n";
echo "$messtmp";
echo "</center>\n";
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	CREER UN FICHIER / CREATE FILE
//-----------------------------------------------------------------------------------------------------------------------------------------

case "creer_fichier";
if(!connecte($id)) {header("Location:stockage.php");exit;}
$err="";
$messtmp="<font face=\"$font\" size=\"2\">";
$nomfic=$_POST["nomfic"];
$nomfic=traite_nom_fichier($nomfic);
if (!VerifInodeTotal($racine)) {

	if($nomfic=="") {
		$messtmp.="$mess[37]"; $err=1;
	}else if(file_exists("$racine/$rep/$nomfic")){
		$messtmp.="$mess[71]"; $err=1;
	}else{
		$nomfic=preg_replace('/\.txt/i',"",$nomfic);
		$fp=fopen("$racine/$rep/$nomfic.txt","w");
		if(preg_match("/\.html$/i",$nomfic)||preg_match("/\.html$/i",$nomfic))	{
			fputs($fp,"<html>\n<head>\n<title>Document sans titre</title>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n</head>\n<body bgcolor=\"#FFFFFF\" text=\"#000000\">\n\n</body>\n</html>\n");
		}
		fclose($fp);	
		$messtmp.="$mess[34] <b>$nomfic</b> $mess[39] <b>";
		if($rep=="") {$messtmp.="/";} else {$messtmp.="$rep";}
		$messtmp.="</b>";
	}

	$messtmp.="<br><br><a href=\"stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens\">$mess[32]</a>";
	$messtmp.="</font>";
}

	if($err=="") {header("Location:stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens");exit;}
	
	include($hautpage);
	echo "<center>\n";
	echo "$messtmp";
	echo "</center>\n";
	break;

	
//-----------------------------------------------------------------------------------------------------------------------------------------
//	UPLOAD
//-----------------------------------------------------------------------------------------------------------------------------------------	

case "upload";
if(!connecte($id)) {header("Location:stockage.php");exit;}
$messtmp="<font face=\"$font\" size=\"2\">";
if($rep!=""){$rep_source="/$rep";}
$destination=$racine.$rep_source;

$userfile_size=$_FILES["userfile"]["size"];
$userfile=$_FILES["userfile"]["tmp_name"];
$userfile_name=$_FILES["userfile"]["name"];
$userfile_type=$_FILES["userfile"]["type"];
//print $userfile_type; exit;

$total=poidstotal($racine);
$total+=$userfile_size;

include_once("./common/config2.inc.php");

if ($total <= TAILLESTOCKAGE ){

if ($userfile_size!=0) {$taille_ko=$userfile_size/1024;} else {$taille_ko=0;}
if ($userfile=="none") {$message=$mess[31];}
if ($userfile!="none" && $userfile_size!=0)
	{
	$userfile_name=traite_nom_fichier($userfile_name);
	if (!move_uploaded_file($userfile, "$destination/$userfile_name"))
//	if (!copy($userfile, "$destination/$userfile_name"))
		{
        		$message="<br>$mess[33]<br>$userfile_name";
	        	}
       	else
		{
        		if(is_editable($userfile_name))
        			{
        			enlever_controlM("$destination/$userfile_name");
        			}		
		$message="$mess[34] <b>$userfile_name</b> $mess[35] <b>$rep</b>";
		}
	}
}else{
	$message="Taille du fichier trop grand.";
}
$messtmp.="$message<br>";	
$messtmp.="<br><br><a href=\"stockage.php?rep=$rep&id=$id&ordre=$ordre&sens=$sens\">$mess[32]</a>";	
$messtmp.="</font>";
header("Location:stockage.php?rep=$rep&ordre=$ordre&sens=$sens&id=$id");


exit;
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	VERIFICATION LOGIN/PASSE
//-----------------------------------------------------------------------------------------------------------------------------------------

case "verif";
$fp=@fopen("prive/users.txt","r");
if($fp)
	{
	while(!feof($fp))
		{
		$buf=fgets($fp,4096);
		if(!preg_match("/^[#]/",$buf))
			{
			$buf=str_replace(CHR(10),"",$buf);
			$buf=str_replace(CHR(13),"",$buf);
			$buf=preg_split("/;/",$buf);
			$l=$buf[2];$p=$buf[3];
			if($login==$l && $passe==$p && $login!="" && $passe!="") {creer_id($buf[0],$buf[1],$l);$ok=1;}
			}
		}
	}
else 
	{
	include($hautpage);
	echo "<center>\n";
	echo "<font face=\"$font\" size=\"2\">$mess[75]</font>";
	echo "</center>\n";
	include($baspage);
	exit;
	}
if($fp) {fclose($fp);}
if($ok==1) {header("Location:stockage.php?id=$id");} 
else {header("Location:stockage.php?err=1");}
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	DECONNEXION
//-----------------------------------------------------------------------------------------------------------------------------------------

case "deconnexion";
if(!connecte($id)) {header("Location:stockage.php");exit;}
// EFFACE LE LOG DU USER
if(file_exists("data/$id.php")) {unlink("data/$id.php");}
	
//EFFACE LES LOGS DE PLUS DE 24H
$now=time();
$eff=$now-(24*3600);
$handle=opendir("data");
while ($fichier = readdir($handle))
	{
	if($fichier!="." && $fichier!="..") 
		{		
		$tmp = filemtime("data/$fichier");
		if($tmp<$eff) {unlink("data/$fichier");}
		}
	}
closedir($handle);
header("Location:stockage.php");
break;


//-----------------------------------------------------------------------------------------------------------------------------------------
//	DEFAUT
//-----------------------------------------------------------------------------------------------------------------------------------------

default;
include($hautpage);
echo "<center>\n";
if(!connecte($id))
	{
	echo "<br>\n";
	echo "<form method=\"post\" action=\"stockage.php\">\n";
	echo "<font face=\"$font\" size=\"2\"";
	if($err==1) {echo "color=\"#FF0033\"";}
	echo "><b>$mess[94]</b>\n";
	echo "<input type=\"text\" name=\"login\">\n";
	echo "<br><b>$mess[78]</b>\n";
	echo "<input type=\"password\" name=\"passe\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"verif\">\n";
	echo "<br><br>\n";
	echo "<input type=\"submit\" $disabled2 name=\"Submit\" value=\"$mess[77]\" class='bouton2' >\n";
	echo "</font></form>\n";
	}  
else 
	{ 
	lister_rep($nom_rep);
	echo "<table height='85' bgcolor='#0B3A0C' width=\"830\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n";
	echo "<tr id='coulBar0' >\n";
	echo "<td id='menumodule1' colspan=\"2\"><img src=\"image/stockage/upload.gif\" align=\"ABSMIDDLE\"> \n";
	echo "<font face=\"$font\" size=2 id='titreStockage' >$mess[25]<b>";
	if($rep==""){echo "/";}else{echo "$rep";}
	echo "</b></font>\n";
	echo "</td></tr>\n";
	echo "<tr id='cadreCentral0' ><td colspan=\"2\"><br>\n";
	echo "<form enctype=\"multipart/form-data\" action=\"stockage.php\" method=\"post\">\n";
	echo "&nbsp;&nbsp;\n";
	echo "<input type=\"file\" name=\"userfile\" size=\"30\">\n";
	echo "<INPUT TYPE=\"hidden\" name=\"action\" value=\"upload\">\n";
	echo "<INPUT TYPE=\"hidden\" name=\"id\" value=\"$id\">\n";
	echo "<input type=\"hidden\" name=\"rep\" value=\"$rep\">\n";
	echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">\n";
	echo "<input type=\"hidden\" name=\"sens\" value=\"$sens\">\n";
	
	if ((VerifPoidsTotal($racine)) && (VerifInodeTotal($racine))) {
		echo "<input type=\"submit\" disabled='disabled' name=\"Submit\" value=\"$mess[27]\" class='bouton2' >\n";
	}else{
		echo "<input type=\"submit\" $disabled2 name=\"Submit\" value=\"$mess[27]\" class='bouton2' >\n";
	}
	echo  inodeTotal($racine)." $mess[96](s) /  ".INODESTOCKAGE."</form></td></tr>\n";
	echo "<tr id='coulBar0' ><td colspan=\"2\">\n";
	echo "<img src=\"image/stockage/dossier.gif\" align=\"ABSMIDDLE\">\n";
	echo "<font face=\"$font\" size=2 id='titreStockage' >$mess[26]<b>";
	if($rep==""){echo "/";}else{echo "$rep";}
	echo "</b></font></td></tr>\n";
	echo "<tr id='cadreCentral0' ><td colspan=\"2\"><br>\n";
	echo "<form method=\"post\" action=\"stockage.php\">\n";
	echo "&nbsp;&nbsp;\n";
	echo "<input type=\"text\" name=\"nomdir\" size=\"30\" maxlength='30' >\n";
	echo "<input type=\"hidden\" name=\"rep\" value=\"$rep\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"mkdir\">\n";
	echo "<INPUT TYPE=\"hidden\" name=\"id\" value=\"$id\">\n";
	echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">\n";
	echo "<input type=\"hidden\" name=\"sens\" value=\"$sens\">\n";
	if (VerifInodeTotal($racine)) {
		echo "<input type=\"submit\" disabled='disabled' name=\"Submit\" value=\"$mess[29]\" class='bouton2'>\n";
	}else{
		echo "<input type=\"submit\" $disabled2 name=\"Submit\" value=\"$mess[29]\" class='bouton2'>\n";
	}
	echo  inodeTotal($racine)." $mess[96](s) /  ".INODESTOCKAGE."</form></td></tr>\n";
	echo "<tr id='coulBar0' ><td colspan=\"2\">\n";
	echo "<img src=\"image/stockage/defaut.gif\" align=\"ABSMIDDLE\">\n";
	echo "<font face=\"$font\" size=2 id='titreStockage' >$mess[28]<b>";
	if($rep==""){echo "/";}else{echo "$rep";}
	echo "</b></font></td></tr>\n";
	echo "<tr id='cadreCentral0' ><td colspan=\"2\"><br>\n";
	echo "<form method=\"post\" action=\"stockage.php\">\n";
	echo "&nbsp;&nbsp;\n";
	echo "<input type=\"text\" name=\"nomfic\" size=\"30\" maxlength='30' >\n";
	echo "<input type=\"hidden\" name=\"rep\" value=\"$rep\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"creer_fichier\">\n";
	echo "<INPUT TYPE=\"hidden\" name=\"id\" value=\"$id\">\n";
	echo "<input type=\"hidden\" name=\"ordre\" value=\"$ordre\">\n";
	echo "<input type=\"hidden\" name=\"sens\" value=\"$sens\">\n";
	if (VerifInodeTotal($racine)) {
		echo "<input type=\"submit\" disabled='disabled' name=\"Submit\" value=\"$mess[29]\" class='bouton2'>\n";
	}else{
		echo "<input type=\"submit\" $disabled2  name=\"Submit\" value=\"$mess[29]\" class='bouton2'>\n";
	}
	echo  inodeTotal($racine)." $mess[96](s) /  ".INODESTOCKAGE."</form></td></tr>\n";
	echo "</table>";
	}
echo "</center>\n";
break;
}
include($baspage);
?>
