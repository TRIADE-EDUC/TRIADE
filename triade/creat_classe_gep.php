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
 include_once("common/config.inc.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<?php if (VATEL != 1) { ?>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<?php }else{ ?>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800" rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
	<link href="vatel/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/css/vatel.css" rel="stylesheet" type="text/css" media="screen"> 
	<link href="vatel/assets/css/font-awesome.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/plugins/owl-carousel/owl.carousel.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/plugins/owl-carousel/owl.theme.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/plugins/owl-carousel/owl.transitions.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/plugins/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/css/animate.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/css/superslides.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/plugins/revolution-slider/css/settings.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/css/pikaday.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/css/essentials.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/css/masonry.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/css/layout.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/css/layout-responsive.css" rel="stylesheet" type="text/css" media="screen">
    <link href="vatel/assets/css/color_scheme/darkblue.css" rel="stylesheet" type="text/css" media="screen">
    
<?php } ?>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<?php
$vatel="";
$size=15;
if (VATEL == 1) {
	$vatel="VATEL";
	$size=15;
}
?>
<body id='bodyfond2'>
<?php include_once("./librairie_php/lib_licence.php"); ?>
<form  method=post onsubmit="return verifcreatclasse()" name="formulaire">
<!-- // fin  -->
<br>
<font class='T2'>&nbsp;&nbsp;&nbsp;<?php print LANGGRP6 ?>&nbsp;:&nbsp;</font> <input type=text name="saisie_creat_classe" size='<?php print $size ?>'  maxlength='29' ><BR>
<BR><bR>

&nbsp;&nbsp;<script language=JavaScript>buttonMagicSubmit<?php print $vatel ?>("<?php print LANGBT14 ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicFermeture<?php print $vatel ?>(); //bouton de fermeture</script>
<br><br>
<!-- // fin  -->
</form>
<?php
if(isset($_POST['create'])):
        include_once("librairie_php/db_triade.php");
        $cnx=cnx();
        $cr=create_classe($_POST['saisie_creat_classe']);
        if($cr):
                alertJs(LANGGRP7);
		print "<script>parent.window.close();</script";
        else:
                alertJs(LANGVATEL194);
		print "<script>parent.window.close();</script";
        endif;
        Pgclose();
endif;
?>
</BODY></HTML>
