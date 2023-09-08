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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS26 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >

     <!-- // fin  -->
<?php
// affichage de la liste d'élèves trouvées
$motif=strtolower(trim($_POST["saisie_nom_eleve"]));
$sql=<<<EOF

SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM ${prefixe}eleves e, ${prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom

EOF;
$res=execSql($sql);
$data=chargeMat($res);

?>
<?php
if( count($data) <= 0 )
        {
        print("<BR><center><font size=3><?php print LANGDISP1?></font><BR><BR></center>");
        }
else {
for($i=0;$i<count($data);$i++)
        {
        ?>
<FORM name="formulaire_<?php print $i?>" method=post action='gestion_abs_retard_planifier_suite.php'>
<table border="1" bordercolor="#000000" width="100%" bgcolor="#FFFFFF">
<tr bordercolor="#FFFFFF">
<td bgcolor="#FFFFFF"><font class="T2"><?php print LANGNA1?> : </font><B><?php print ucwords(trim($data[$i][1]))?> <?php infoBulleEleve($data[$i][3]); ?> </b></td></tr>
<tr bordercolor="#FFFFFF">
<td bgcolor="#FFFFFF"><font class="T2"><?php print LANGNA2?>: </font><b><?php print ucwords(trim($data[$i][2]))?></b></td>
</tr>
<tr bordercolor="#FFFFFF">
<td bgcolor="#FFFFFF"><font class="T2"><?php print LANGABS12?> : </font>

<select onChange="motifabsretad22('<?php print $i ?>',this.value); verifjustifier('<?php print $i ?>')" name="saisie_motifs_<?php print $i ?>" id="motif_<?php print $i?>" >
<option value="0"  id='select0' ><?php print LANGINCONNU ?></option>
<?php affSelecMotif() ?>
<option value="autre"  id='select1' ><?php print "autre" ?></option>
</select>
<input type="text" name="saisie_motif_<?php print $i?>" size="19" value="<?php print LANGINCONNU ?>" id="saisie_motif_<?php print $i?>"  style="display:none" />
( <input type=checkbox name="saisie_justifier_<?php print $i?>" value="1" > <?php print LANGRTDJUS ?>)
</td>
</tr>
</table>
<table border="1" bordercolor="#000000" width="100%" bgcolor="#FFFFFF">
<tr bordercolor="#FFFFFF">
<td align=center  bgcolor="#FFFFFF"  > Sera :
<?php $val="'".$i."','".dateHI()."','".dateDMY()."'"; ?>
<select name="saisie_<?php print $i?>" onChange="absplanifier0(<?php print $val?>);document.formulaire_<?php print $i?>.create.disabled=false;">
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN?></option>
<option value=absent STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGABS?></option>
<option value=retard STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGRTD?></option>
</select></td>
<td bgcolor="#FFFFFF" align=center> le
<input type=text size=12 name="saisie_date_<?php print $i?>" value=''  readonly="readonly">
<?php
include_once("librairie_php/calendar.php");
$dateSupp=recupEntervaleAbs($data[$i][3]);
calendarSupp("id1$i","document.formulaire_$i.saisie_date_$i",$_SESSION["langue"],"0",$dateSupp);
unset($dateSupp);
?>
 à
<select name="saisie_heure_<?php print $i?>" onChange="fonc2()">
<?php
$disabled="disabled";
$data3=recupCreneauDefault("creneau"); // libelle,text
if (count($data3) > 0) {
	$data3=recupInfoCreneau($data3[0][1]);
	print "<option  id='select1' value=\"".trim($data3[0][0])."#".$data3[0][1]."#".$data3[0][2]."\" >".trim($data3[0][0])." : ".timeForm($data3[0][1])." - ".timeForm($data3[0][2])."</option>\n";
	$disabled="";
}else{
?>
<option STYLE='color:#000066;background-color:#FCE4BA' value="null" ><?php print LANGCHOIX ?></option>
<?php
}
select_creneaux2();
?>
	</select>
</td>
<td  bgcolor="#FFFFFF" align=center>pendant
<select name="saisie_duree_<?php print $i?>" onChange="absplanifier2(<?php print $i?>)" >
<option value=0 STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN?></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
<option STYLE='color:#000066;background-color:#CCCCFF'></option>
</select>
<input type=hidden onfocus=this.blur() value="<?php print $i?>" name="saisie_id_champ">
<input type=hidden onfocus=this.blur() value="<?php print trim($data[$i][3])?>" name="saisie_pers">
<input type=hidden onfocus=this.blur() name="saisie_duree_retourner_<?php print $i?>"></td>
</tr>
</table>
<BR>
<center><input type='submit' value="<?php print LANGABS27?> <?php print ucwords(trim($data[$i][1]))." ".ucwords(trim($data[$i][2]))?>" class="bouton2" disabled='disabled' name="create" />&nbsp;&nbsp;<script language='JavaScript'>buttonMagicRetour2('gestion_abs_retard.php','_self','Retour menu')</script>
</center><BR>
</form>
<BR><BR><BR>
        <?php
        }
      }
?>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
       print "</SCRIPT>";
   else :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
      print "</SCRIPT>";

      top_d();

      print "<SCRIPT language='JavaScript' ";
     print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
     print "</SCRIPT>";

       endif ;
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
   </BODY></HTML>
