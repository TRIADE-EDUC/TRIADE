<?php
session_start();
//error_reporting(0);
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
	<?php 
	include_once("./librairie_php/lib_rss.php"); // mettre à jour
	include_once("./librairie_php/lib_licence.php");
	include_once("./librairie_php/db_triade.php");
  	$cnx=cnx();
	if (isset($_POST["info"])) {
		$val=recup_lien_rss($_POST["info"]);
		$val=preg_replace('/{MEMBRE}/',$_SESSION["membre"],$val);
		$val=preg_replace('/{IDPERS}/',$_SESSION["id_pers"],$val);
	}
	?>
	<script type="text/javascript">
	function frss(lrss) {
			if (document.forms[0].rss.checked == false) {
				document.forms[0].lienrss.value="";
			}else{
				if (lrss == "resa") { document.forms[0].lienrss.value="<?php print $val  ?>"; }	
				if (lrss == "actu") { document.forms[0].lienrss.value="<?php print $val  ?>"; }	
			}
	}

	</script>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center">
	<div align='center'><?php top_h(); ?>
	<SCRIPT type="text/javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS167 ?></font></b></td></tr>
	<tr id='cadreCentral0'><td >
	<!-- // fin  -->
		<?php

	
      
		if ($_SESSION["membre"] == "menuprof"){
			$idpers=$_SESSION["id_suppleant"];
		}else{
			$idpers=$_SESSION["id_pers"];
		}

		$mail=mess_mail_forward($_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["membre"]);

		if (isset($_POST["create"])) {
			if ($_POST["info"] == "mess") {
				$valid=0;
				if (ValideMail($_POST["mail"])) $valid=1;
				$cr=mess_forward(trim($_POST["mail"]),$valid,$_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["membre"]);
			}else{
				$cr=enreg_param($_POST["mail"],$_POST["sms"],$_POST["rss"],$idpers,$_SESSION["membre"],$_POST["info"],$_POST["numero"]);
				if ($_SESSION["membre"] == "menuprof") { config_param_ajout($_POST["connexion"],"pagecnx".$_SESSION["id_pers"]); }
			}

			if($cr == 1){
				history_cmd($_SESSION["nom"],"MODIFIER","parametrage");
                		alertJs("Enregistrement effectué");
        		}	
		}

		$data=recherche_param($idpers,$_POST["info"],$_SESSION["membre"]);

		// mettre à jour
		if ($_POST["info"] == "actu") { $choixoption0="selected='selected'"; }
		if ($_POST["info"] == "resa") { $choixoption1="selected='selected'"; }
		if ($_POST["info"] == "mess") { $choixoption2="selected='selected'"; }
		// parametrage,valeur
		for ($i=0;$i<count($data);$i++) {
			if (preg_match('/^sms/',$data[$i][1])) { 
				$checkedsms="checked='checked'"; 
				$numero=preg_replace('/sms\//','',$data[$i][1]);
			}	
			if ($data[$i][1] == "rss") { 
				$checkedrss="checked='checked'"; 
				// mettre à jour
				if ($data[$i][0] == "resa") { $lienrss=$val; }
				if ($data[$i][0] == "actu") { $lienrss=$val; }
			}	
			if (preg_match('/@/',$data[$i][1])) { $mail=$data[$i][1]; }	

		}

		if (SMS != "oui") {
			$disabled="disabled='disabled'";
			$numero=LANGTMESS483;
		}

		if (FORWARDMAIL != "oui") {
			$mail=LANGTMESS483;
			$disabledmail="disabled='disabled'";
		}

		if ($_POST["info"] == "mess") {
			$disabledrss="disabled='disabled'";
		}
	?>
		<form method="post" >
		<ul>
			<br /><br />
			<?php print LANGPARAM44 ?> : 
			<br /><br /><br />
			<img src="image/commun/ico_conf.gif" alt="conf" align="center" />	
				<select name="info" onChange="document.forms[0].submit();" >
				<option value="rien" id='select0'><?php print LANGCHOIX ?></option>
				<?php
				
				print "<option value='actu' $choixoption0 id='select1' >".LANGMESS168."</option>";
		
				if ($_SESSION["membre"] == "menuadmin") {
					print "<option value='resa' $choixoption1 id='select1' >".LANGMESS169."</option>";
				}
				if ($_SESSION["membre"] == "menuscolaire") {
					print "<option value='resa' $choixoption1 id='select1' >".LANGMESS169."</option>";
				}

			
		
				print "<option value='mess' $choixoption2 id='select1' >".LANGMESS170."</option>";
			
				
				?>
				</select>	

			<br /><br /><br />
			<?php if ((EMAILCHANGEELEVE == "non") && ($_SESSION["membre"] == "menueleve")) { 
					$readonly="readonly='readonly'";
				}else{
					$readonly="";
				}
			?>
				<img src="image/commun/email.gif" alt="mail" align="center" /><?php print LANGTMESS421 ?> <input type="text" name="mail"  onblur='verifEmail(this)' size=40 value="<?php print $mail?>" <?php print $disabledmail ?> <?php print  $readonly ?> />
			<i><?php print LANGMESS171 ?></i>
				
			<br /><br /><br />

			<img src="image/l_port.gif" alt="Tel" align="center" /> <?php print LANGTMESS422 ?> <input type=checkbox name="sms" value="sms" <?php print $checkedsms ?>  <?php print $disabled ?> onclick="document.forms[0].numero.value=''" />
			<input type="text" <?php print $disabled ?> name="numero"  maxlength="27" value='<?php print $numero ?>' /> <i> <?php print LANGMESS172 ?></i>

			<br /><br /><br />

			<img src="image/commun/rss-icon.gif" alt="rss" align="center" />&nbsp;&nbsp; <?php print LANGTMESS423 ?> <input type=checkbox name="rss" value="rss" onclick="frss(document.forms[0].info.options[document.forms[0].info.options.selectedIndex].value)" <?php print $checkedrss." ".$disabledrss ?>  /> <input type=text <?php print $disabledrss ?> name='lienrss' size="50" readonly="readonly" value="<?php print $lienrss ?>" />

			<br /><br /><br />
<?php if ($_SESSION["membre"] == "menuprof") { ?>
			<img src="image/commun/actif.gif" alt="rss" align="center" />&nbsp;&nbsp; <?php print LANGTMESS424 ?>
				<select name="connexion"  >
				<?php 
				$datap=config_param_visu("pagecnx".$_SESSION["id_pers"]);
				$pageconnexion=$datap[0][0];
				if ($pageconnexion == "abs") { $selected2="selected='selected'"; }  
				if ($pageconnexion == "messagerie") { $selected3="selected='selected'"; }  
				?>
				<option value="rien" id='select0'><?php print LANGMESS168 ?></option>
				<option value="abs" id='select1' <?php print $selected2 ?>  ><?php print LANGTMESS425 ?></option>
				<option value="messagerie" id='select1' <?php print $selected3 ?>  ><?php print LANGASS14 ?></option>
				</select>
			<br /><br /><br />
<?php } ?>
			<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","create"); //text,nomInput</script>

			<br /><br /><br />
		</ul>	
		</form>


	<?php
	print "</td></tr></table>";
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     		print "<SCRIPT type='text/javascript' ";
	       	print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       		print "</SCRIPT>";
	}else{
       		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      		print "</SCRIPT>";
	      	top_d();
      		print "<SCRIPT type='text/javascript' ";
	      	print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
		print "</SCRIPT>";
	}
	Pgclose();
?>
</BODY></HTML>
