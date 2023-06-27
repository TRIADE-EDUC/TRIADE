<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
<!-- /**************************************
Last updated: 29.12.2003    par Taesch  Eric
*******************************************/ -->
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript">
</script>
<title>Validation d'Inscription</title>
</head>
<?php 
include("./librairie_php/lib_licence2.php");
include_once("librairie_php/langue.php");
include("./common/version.php") ?>
<body background="./image/commun/fond_inscrip.jpg" >
<?php
	$idRss=$_GET["id"];
	$titleOr=stripslashes(urldecode($_GET["titre"]));
	include_once("librairie_php/db_rss.php");
	$cnx=cnx();
	include_once("./magpierss/rss_fetch.inc");
	print "<table width='100%' >";
		
	miseAjour(stripslashes($titleOr),$idRss);

	$url=recupUrl($idRss);
	$rss=fetch_rss($url);
	print "<tr>";
	foreach ($rss->items as $item) {
		$href 	= $item['link'];
		$title 	= $item['title'];
		$date 	= $item['date'];
		$link	= $item['link'];
		if ($title == $titleOr) {
			$description = $item['description'];
			print "<td>";
			print "Information disponible --> <a href='$link' target='_blank' >$link</a><br><hr>";
			print $description;
			print "</td></tr>";
		}
	}
	print "</tr>";
	Pgclose();
?>
</body>
</html>
