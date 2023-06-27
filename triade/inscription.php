<?php
session_start();
if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {
	header('Location: ./acces_refuse.php');
	exit;
}

include_once("common/config.inc.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if (!verif_compte($_SESSION["nom"],$_SESSION["prenom"],$_SESSION["id_pers"],$_SESSION["membre"])) {
	header('Location: ./acces_depart.php');	
	PgClose();
	exit;
}
PgClose();
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
<!--
/************************************************************
Last updated: 12/12/2008    par Taesch  Eric

Last updated: 31/07/2006    par Pirio Mikaël
  - Correction du code pour la validation XHTML 1.0 - strict
*************************************************************/
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">	

<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<?php define("CHARSET","iso-8859-1"); ?>
		<meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
		<meta http-equiv="CacheControl" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="-1" />
		<meta name="Copyright" content="Triade©, 2001" />
		<link rel="SHORTCUT ICON" href="../favicon.ico" />
		<script language="JavaScript" src="./librairie_js/lib_type_navigateur.js"></script>
		<script language="JavaScript" src="./librairie_js/lib_type_debit.js"></script>
		<link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css2.css" />
		<title>Triade Inscription</title>
	</head>

	<body  >
	<?php 
	include_once("./librairie_php/lib_licence2.php");
	include_once("librairie_php/langue.php");
	include_once("./common/version.php") 
?>

		<!-- "text-align: center" à cause du bug centrage d'IE :( -->
		<div style="text-align: center; ">




			<div id="mainInst" style='box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);'>
<?php
		
		if ((LAN == "oui") && (AGENTWEB == "oui")) {
			$cnx=cnx();
			$data=visu_param();
			PgClose();
			$nometablissement=$data[0][0];
			$etablissement=urlencode(stripHTMLtags("$nometablissement"));
			$mess="<iframe width='120' height='350' src=\"./agentweb/agentpers.php?inc=6&mess=M1&etablissement=$etablissement&m=M4\"  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>";
			print $mess;
		}
		 
?>

				<img src="./image/logo_triade_licence.gif"
				     alt="logo_triade_licence" />

<?php
	include_once("./common/version.php");
	include_once("./common/productId.php");
	

?>

				<p>
				Version : <b><?php print VERSION; ?></b><br />
				Licence d'utilisation  : <?php print LICENCE; ?> <br />
				Product ID = <b> <?php print PRODUCTID; ?> </b><br />
				</p>
				<div style="text-align: center">
				<textarea cols="80" rows="10"
				          style="font-family: Arial;
				                 font-size:10px;
				                 color:#CC0000;
				                 background-color:#CCCCFF;
				                 font-weight:bold;
				                 margin-right: auto;
						 width:450px;
				                 margin-left: auto;"><?php print DROITUTILISATION."\n\n".DROITRIADE?>
				</textarea>
				</div>
				<p>T.R.I.A.D.E. © <?php print date("Y"); ?></p>
				<div style="text-align: right;
		  		          padding-right: 100px;
		  	  	        margin-bottom: 1em;" >
					<form name="inscripform" method='POST' action="inscription02.php">
<br>

<input type=checkbox name="accord" value="1" onclick="document.inscripform.val.disabled=false; document.inscripform.accord.disabled=true;" /> <?php print LANGCONDITION ?> &nbsp;&nbsp;&nbsp;&nbsp;
<input type=submit value='<?php print ACCEPTER ?>' class=BUTTON disabled='disabled' name="val" >
</form>
				</div>
			</div>
		</div>

<?php
	include_once("installation/librairie/pied_page.php");
?>

<!---------------->
	</body>
</html>

