<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
?>
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php include_once("./common/config5.inc.php") ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="./favicon.ico" />
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
		<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
		
	</head>

	<body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >

	<script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
	<script type="text/javascript" src="./librairie_js/messagerie_fenetre.js"></script>
	<?php 
	include_once("./librairie_php/lib_licence.php"); 
	?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Consultation de vos flux RSS" ?></font></b></td></tr>
	<tr id='cadreCentral0'><td >
	<!-- // fin  -->
	<?php
if (LAN == "oui") {
		
		include_once("./librairie_php/db_rss.php");
		
		include_once("./magpierss/rss_fetch.inc");

        	$cnx=cnx();
		$idpers=$_SESSION["id_pers"];
		if ($_SESSION["id_suppleant"] > 0) {
			$idpers=$_SESSION["id_suppleant"];
		}
		if (isset($_POST["create"])) {
			$url=$_POST["lienrss"];
			$rss=fetch_rss($url);
			$description=$rss->channel['title'];
	
			if (trim($description) != "") {
				ajoutRss($idpers,$url,$_SESSION["membre"]);
			}else{
				print "<center><br><font color=red class=T2>Cette adresse ($url) est incorrect. </font></center>" ;
			}
		}
		if (isset($_GET["idsupp"])) {
			suppRss($_GET["idsupp"],$idpers,$_SESSION["membre"]);
		}

?>
		<script>CreerFenetreBe();</script>
		<br /><ul>
		<form method="post" >
		<strong>Ajouter un flux RSS :</strong> <br /><br />
		<input type=text name='lienrss' size="70" value="http://" />
		<img src="image/commun/rss-icon.gif" alt="rss" align="center"  />
		<br />
		<br />
		<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","create"); //text,nomInput</script>
		<br /><br /><br />
		</form>
		</ul>	
		<hr width="80%">
		<ul>
		<img src="./image/news2.gif" align="center"> <strong>Vos Flux.</strong>
		<br /><br />
		<?php
		print "<table >";
		$dataR=consultRss($idpers,$_SESSION["membre"]);
		//id,idpers,membre,url
		for($i=0;$i<count($dataR);$i++) {
			$url=$dataR[$i][3];
			$tab[$url]=1;
		}
		krsort($tab);
		foreach($tab as $key => $value) {
			if (preg_match("/http/",$key)) {
				$url=$key;
				$rss=fetch_rss($key);
				$datemodif=$rss->channel['pubdate'];
				print "<tr>";
				print "<td colspan=2 ><img src='./image/commun/on1.gif' height='8' width='8' align='center' > <font class='T2' color='blue' >".trunchaine($rss->channel['title'],'33')." </font> -  <font class=T1>[<a href='flux.php?idsupp=".urlencode($url)."'>supprimer</a>]</font> </td>";
				print "</tr>";
				print "<tr>";
				$jj=0;
				foreach ($rss->items as $item) {
					$href 	= $item['link'];
					$title 	= $item['title'];
					$date 	= $item['pubdate'];
					$date = preg_replace('/\+\.\.\.\./',"",$date);
					$date = preg_replace('/GMT/',"",$date);
					if (trim($date) == "") {  $date=$datemodif; }
					$conx=RssDejaLu($title,$url,$_SESSION["id_pers"],$_SESSION["membre"]);
					print "<tr><td>";
					print "&nbsp;&nbsp;";
					if ($conx == "non") { print "<strong>"; }
					$idrss=idRss($url,$_SESSION["id_pers"],$_SESSION["membre"]);
					$title2=urlencode($title);

					print " - <a href='#' onClick=\"return apercu('lect_rss.php?id=$idrss&titre=$title2');\" >".trunchaine($title,35)."</a> ";
					print "</td><td><font class='T1'>$date</font></td> ";
					if ($conx == "non") { print "</strong>"; }
					print "</td></tr>";
					$j++;
				}
				print "</tr>";
			}
		}
		print "</table>";
		?>
		</ul>
	<?php
		Pgclose();
	}else{
		print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR1."</i></center><br><br>";
	}
	print "</td></tr></table>";
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     		print "<SCRIPT type='text/javascript' ";
	       	print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
       		print "</SCRIPT>";
	}else{
       		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
      		print "</SCRIPT>";
	      	top_d();
      		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
		print "</SCRIPT>";
	}
?>
</BODY></HTML>
