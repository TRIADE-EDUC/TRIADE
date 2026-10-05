<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGABS26 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
$motif=strtolower(trim($_POST["saisie_nom_eleve"]));
$sql=<<<EOF
SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM {$prefixe}eleves e, {$prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom
EOF;
$res=execSql($sql);
$data=chargeMat($res);

if(countTriade($data) <= 0) {
	print("<br><center><b>".LANGDISP1."</b></center><br>");
}else{
for($i=0;$i<countTriade($data);$i++) {
?>
<FORM name="formulaire_<?php print $i ?>" method="post" action="gestion_abs_retard_planifier_suite.php">

<div class="na-card" style="margin:8px 0 4px;">
  <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
    <div><span class="na-lbl"><?php print LANGNA1 ?> :</span> <b><?php print ucwords(trim($data[$i][1])) ?></b> <?php infoBulleEleve($data[$i][3]); ?></div>
    <div><span class="na-lbl"><?php print LANGNA2 ?> :</span> <b><?php print ucwords(trim($data[$i][2])) ?></b></div>
  </div>
  <div class="na-row" style="margin-top:8px;">
    <span class="na-lbl"><?php print LANGABS12 ?> :</span>
    <select onChange="motifabsretad22('<?php print $i ?>',this.value); verifjustifier('<?php print $i ?>')" name="saisie_motifs_<?php print $i ?>" id="motif_<?php print $i ?>" class="cc-select">
      <option value="0"><?php print LANGINCONNU ?></option>
      <?php affSelecMotif() ?>
      <option value="autre">autre</option>
    </select>
    <input type="text" name="saisie_motif_<?php print $i ?>" class="cc-select" style="width:120px;display:none;" value="<?php print LANGINCONNU ?>" id="saisie_motif_<?php print $i ?>">
    <label style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
      <input type="checkbox" name="saisie_justifier_<?php print $i ?>" value="1"> <?php print LANGRTDJUS ?>
    </label>
  </div>
  <div class="na-row" style="flex-wrap:wrap;gap:8px;">
    <span class="na-lbl">Sera :</span>
    <?php $val="'".$i."','".dateHI()."','".dateDMY()."'"; ?>
    <select name="saisie_<?php print $i ?>" class="cc-select" onChange="absplanifier0(<?php print $val ?>);document.formulaire_<?php print $i ?>.create.disabled=false;">
      <option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN ?></option>
      <option value="absent" style='color:#000066;background-color:#CCCCFF'><?php print LANGABS ?></option>
      <option value="retard" style='color:#000066;background-color:#CCCCFF'><?php print LANGRTD ?></option>
    </select>
    <span style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">le</span>
    <input type="text" name="saisie_date_<?php print $i ?>" value="" class="cc-select" style="width:90px;" readonly="readonly">
    <?php
    include_once("librairie_php/calendar.php");
    $dateSupp=recupEntervaleAbs($data[$i][3]);
    calendarSupp("id1$i","document.formulaire_$i.saisie_date_$i",$_SESSION["langue"],"0",$dateSupp);
    unset($dateSupp);
    ?>
    <span style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">à</span>
    <select name="saisie_heure_<?php print $i ?>" class="cc-select" onChange="fonc2()">
    <?php
    $disabled="disabled";
    $data3=recupCreneauDefault("creneau");
    if (countTriade($data3) > 0) {
        $data3=recupInfoCreneau($data3[0][1]);
        print "<option value=\"".trim($data3[0][0])."#".$data3[0][1]."#".$data3[0][2]."\">".trim($data3[0][0])." : ".timeForm($data3[0][1])." - ".timeForm($data3[0][2])."</option>\n";
        $disabled="";
    }else{
        print "<option style='color:#000066;background-color:#FCE4BA' value=\"null\">".LANGCHOIX."</option>";
    }
    select_creneaux2();
    ?>
    </select>
    <span style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">pendant</span>
    <select name="saisie_duree_<?php print $i ?>" class="cc-select" onChange="absplanifier2(<?php print $i ?>)">
      <option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGRIEN ?></option>
      <?php for($o=0;$o<15;$o++) { print "<option style='color:#000066;background-color:#CCCCFF'></option>"; } ?>
    </select>
    <input type="hidden" onfocus="this.blur()" value="<?php print $i ?>" name="saisie_id_champ">
    <input type="hidden" onfocus="this.blur()" value="<?php print trim($data[$i][3]) ?>" name="saisie_pers">
    <input type="hidden" onfocus="this.blur()" name="saisie_duree_retourner_<?php print $i ?>">
  </div>
</div>

<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:6px 0;">
  <button type="submit" value="<?php print LANGABS27." ".ucwords(trim($data[$i][1]))." ".ucwords(trim($data[$i][2])) ?>" class="btn-enr" disabled="disabled" name="create"><?php print LANGABS27 ?> <?php print ucwords(trim($data[$i][1]))." ".ucwords(trim($data[$i][2])) ?></button>
  <button type="button" class="btn-retour" onclick="open('gestion_abs_retard.php','_self','')">Retour menu</button>
</div>
<br><br>
</FORM>
<br>
<?php
}
}
?>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
