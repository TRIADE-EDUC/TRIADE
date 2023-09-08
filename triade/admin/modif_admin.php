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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="../librairie_js/verif_creat.js"></script>
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<form method=post onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMODIF4?> <?php print LANGADMIN ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
	include_once("../librairie_php/db_triade.php");
	// connexion P
	$cnx=cnx();
	error($cnx);


if(isset($_POST["create"])){
	// requete ? prenom2 ?
	$cr=modif_personnel($_POST["id_pers"],$_POST["saisie_creat_nom"],$_POST["saisie_creat_prenom"],$_POST["saisie_intitule"],$_POST["saisie_creat_adr"],$_POST["saisie_creat_code"],$_POST["saisie_creat_tel"],$_POST["saisie_creat_mail"],$_POST["saisie_creat_commune"],$_POST["saisie_creat_tel_port"],'','','');
	if($cr == 1){
		alertJs(LANGMODIF14);
		history_cmd($_SESSION["nom"],"MODIFICATION"," de $_POST[saisie_creat_nom]");
	}
	$saisie_id=$_POST["id_pers"];
}else{
	$saisie_id=$_GET["saisie_id"];
}
	  $passage_argument="oui"; // pour le JavaScript
          // soit 0 ou 1 ou 2 PAS DE M. ni Mme ni Mme
          $data=recherche_personne_modif($saisie_id);
          // pers_id,nom,prenom,mdp,civ,email,num,adr,code_post,commune,tel
          $nom_admin=trim($data[0][1]);
          $prenom_admin=trim($data[0][2]);
          $passwd_admin=trim($data[0][3]);
          $intitule_admin=$data[0][4];
          $mail=trim($data[0][5]);
          $adr=trim($data[0][6]);
          $code_post=trim($data[0][7]);
          $commune=trim($data[0][8]);
	  $tel=trim($data[0][9]);
	  $telPort=trim($data[0][10]);
?>
     <!-- // fin  -->
     <blockquote><BR>
<fieldset><legend><?php print LANGMODIF5 ?></legend>
<table width=80% border=0 cellpadding="2" cellspacing="2" >
<tr><td align=right ><font class="T2">Civ : </font></td><td>
<select name="saisie_intitule" > 
<option value='0' id='select1' >M</option>
<option value='1' id='select1'>Mme</option>
<option value='2' id='select1'>Mlle</option>
<option value='3' id='select1'>Ms</option>
<option value='4' id='select1'>Mr</option>
<option value='5' id='select1' >Mrs</option>
<option value='7' id='select1' >P.</option>
<option value='8' id='select1' >Sr</option>
</select>
</td></tr>
<tr><td align=right width=40%><font class="T2"><?php print LANGNA1?> : </font></td><td><input type=text name="saisie_creat_nom" value="<?php print "$nom_admin" ?>" size=33 maxlength=30></td></tr>
<tr><td align=right width=40%><font class="T2"><?php print LANGNA2?> : </font></td><td><input type=text name="saisie_creat_prenom"  value="<?php print "$prenom_admin" ?>" size=33 maxlength=30></td></tr>
<tr><td align=right width=40%><font class="T2"><?php print nb2space(LANGNA3) ?> : </font></td><td><input type=button onclick="open('./modif_pers_pass.php?id=<?php print $saisie_id;?>&type=ADM','pass','width=450,height=300')" value='<?php print LANGPER30 ?>'  class="bouton2" > </td></tr>
</table>
</fieldset>
<br><br><br>

<fieldset><legend><?php print LANGMODIF7 ?></legend>
<TABLE width=80% border=0 cellpadding="2" cellspacing="2">
<tr><td align=right><font class="T2"><?php print LANGMODIF8 ?> :  </font></td><td><input type=text name="saisie_creat_adr" size=33 maxlength=100 value="<?php print $adr ?>"></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF9 ?> :  </font></td><td><input type=text name="saisie_creat_code" size=33 maxlength=15 value="<?php print $code_post ?>"></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF10 ?> : </font></td><td><input type=text name="saisie_creat_commune" size=33 maxlength=40 value="<?php print $commune ?>"></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF11 ?> : </font></td><td><input type=text name="saisie_creat_tel" size=33 maxlength=18 value="<?php print $tel ?>"></td></tr>
<tr><td align=right><font class="T2"><?php print LANGAGENDA76 ?> : </font></td><td><input type=text name="saisie_creat_tel_port" size=33 maxlength=18 value="<?php print $telPort ?>" ></td></tr>
<tr><td align=right><font class="T2"><?php print LANGMODIF12 ?> : </font></td><td><input type=text name="saisie_creat_mail" size=33 maxlength=150 value="<?php print $mail ?>"></td></tr>
</TABLE>
</fieldset>
<br><br>
<BR><BR>
<center>
<input type=hidden name=id_pers value="<?php print $saisie_id?>" >
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMODIF13 ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("<?php print LANGBT8?>","compte-admin.php","_parent","","");</script>
<BR><br>
</center>
     </blockquote>
     <!-- // fin  -->
     </td></tr></table>
     </form>
	  <?php
	  //procedure javascript pour l'intitule

	   if ($passage_argument == "oui" ) {
         	print "<script language=JavaScript>";
	      switch($intitule_admin) {
	        case 0 :
			print "document.formulaire.saisie_intitule.options[0].selected=true ";
        	break;
        	case 1:
			print "document.formulaire.saisie_intitule.options[1].selected=true ";
        	break;
        	case 2:
			print "document.formulaire.saisie_intitule.options[2].selected=true";
		break;
		case 3:
			print "document.formulaire.saisie_intitule.options[3].selected=true";
		break;
		case 4:
			print "document.formulaire.saisie_intitule.options[4].selected=true";
		break;
		case 5:
			print "document.formulaire.saisie_intitule.options[5].selected=true";
        	break;
		case 7:
			print "document.formulaire.saisie_intitule.options[6].selected=true";
		break;
		case 8:
			print "document.formulaire.saisie_intitule.options[7].selected=true";
        	break;
        	default:
        	return 0;
        	break;
              }
		print "</SCRIPT>";
	}
		?>
 <SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>


<?php 	Pgclose(); ?>

</BODY></HTML>
