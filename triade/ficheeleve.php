<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
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
error_reporting(0);
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
validerequete("7");
// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
// données DB utiles pour cette page
$Spid=$mySession["Spid"];
$sql="
SELECT
	a.code_classe,
	trim(c.libelle),
	a.code_matiere,
";
if(DBTYPE=='pgsql')
{
	$sql .=" trim(m.libelle)||' '||trim(m.sous_matiere)||' '||trim(langue), ";
}
elseif(DBTYPE=='mysql')
{
	$sql .=" CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(langue) ), ";
}
$sql .= "
	a.code_groupe,
	trim(g.libelle)
FROM
	${prefixe}affectations a,
	${prefixe}matieres m,
	${prefixe}classes c,
	${prefixe}groupes g
WHERE
	code_prof='$Spid'
AND a.code_classe = c.code_class
AND a.code_matiere = m.code_mat
AND a.code_groupe = group_id
ORDER BY
	c.libelle
";
$curs=execSql($sql);
$data=chargeMat($curs);
@array_unshift($data,array()); // nécessaire pour compatibilité
// patch pour problème sous-matière à 0
for($i=0;$i<count($data);$i++){
	$tmp=explode(" 0 ",$data[$i][3]);
	$data[$i][3]=$tmp[0].' '.$tmp[1];
}
// fin patch
freeResult($curs);
unset($curs);
//htmlTableMat($data);
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/jquery-min.js" ></script>
<style>
ul { style-type:none;list-style: none; cursor:pointer;margin-left: 3px;padding-left: 0; }
li { padding:7x; }
</style>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGPROF13 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td>
<!-- // fin  -->
<form method="POST" onsubmit="return verifAccesFiche()" name="formulaire" action="ficheeleve2.php">
<br />
<blockquote>
         <ul>
                <font class="T2"><?php print LANGBULL3 ?> :</font>
                 <select name='anneeScolaire'  >
                 <?php
		 $anneeScolaire=$_COOKIE["anneeScolaire"];
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
                 ?>
                 </select>
		<br><br>

             <font class=T2><?php print LANGPROFG ?> :</font>
				<?php if ($_SESSION["membre"] == "menuprof") { ?>
					 <select name="sClasseGrp" size="1" >
					 <option value="0" STYLE="color:#000066;background-color:#FCE4BA"> <?php print LANGCHOIX3 ?> </option>
					 <?php
					 for($i=1;$i<count($data);$i++){
					 	if( $i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp) ){
							continue;
						}else {
							// utilisation de l'opérateur ternaire expr1?expr2:expr3;
							$libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
							if (isset($verif[$libelle])) continue;
		                                        $verif[$libelle]=$libelle;
							print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0]."\">".$libelle."</option>\n";
						}
						$gtmp=$data[$i][4];
						$ctmp=$data[$i][0];
					 }
					 unset($gtmp);
					 unset($ctmp);
					 unset($libelle);
					 unset($verif);
					 ?>
					 </select>
				<?php }else{ ?>
					 <select name="sClasseGrp" size="1" >
					 <option id='select0' ><?php print LANGCHOIX?></option>
					 <?php select_classe(); // creation des options ?>
					 </select>
				<?php } ?>
				 <br /><br />
                <!--
				 Indiquer la matière :

				<select name="sMat" size="1">
                <option value="0" STYLE="color:#000066;background-color:#FCE4BA">Choix ...</option>
               	</select>
			<br><br>
                 -->
				<br>
				 <UL><UL><UL><UL>
		 <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31 ?>","rien"); //text,nomInput</script>
				                                 <br><br>

				 </UL></UL></UL></UL></UL>
                 </form>
</blockquote>
<hr />

<form method=post onsubmit="return valide_recherche_eleve_1()" name="formulaire_1">
<blockquote><BR>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap>
<font class="T2"><?php print LANGABS3?> : </font></td><td> 
<input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" style="width:15em;"  />
</td></tr>
<tr><td></td><td style="padding-top:0px;"><div id="userList" style="width:13.5em;border-style:none; background-color:#EEEEEE;"></div></td></tr>
</table><div style="position:relative">
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT31?>","create"); //text,nomInput</script></UL></UL></UL>
</div>
</blockquote>
<?php brmozilla($_SESSION["navigateur"]);?>
<?php brmozilla($_SESSION["navigateur"]);?>
</form>
<br><br><br>

<?php
//alertJs(empty($create));
// affichage de la liste d élèves trouvés
if(isset($_POST["saisie_nom_eleve"]))
{
$saisie_nom_eleve=trim($_POST["saisie_nom_eleve"]);
$motif=strtolower($saisie_nom_eleve);
$sql=<<<EOF

SELECT c.libelle,e.nom,e.prenom,e.elev_id,e.classe
FROM ${prefixe}eleves e, ${prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom

EOF;
$res=execSql($sql);
$data=chargeMat($res);

?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#CCCCCC" >
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'>
		<?php print LANGRECH2?> : <font id="color2"><B><?php print ucwords(stripslashes($motif))?></font>
	</font></td>
</tr>
<?php

if( count($data) <= 0 )
	{
	print("<tr><td align=center valign=center>".LANGRECH3."</td></tr>");
	}
else {
?>
<tr bgcolor="#FFFFFF"><td><b><?php print ucwords(LANGIMP10)?></b></td><td><B><?php print LANGIMP8?> <?php print LANGIMP9?></B></td><td><B> </B></td></tr>
<?php
for($i=0;$i<count($data);$i++)
{
	$idEleve=$data[$i][3];
	?>
	<tr>
	<td bgcolor="#FFFFFF"><?php print ucfirst($data[$i][0])?></td>
	<td bgcolor="#FFFFFF"><?php infoBulleEleveSansLoupe($idEleve,strtoupper($data[$i][1])." ".ucwords($data[$i][2])) ; ?> </td>
	<td bgcolor="#FFFFFF" width='5%'><input type='button' class="BUTTON" value='Consulter' onclick="open('ficheeleve3.php?eid=<?php print $data[$i][3]?>&idclasse=<?php print $data[$i][4] ?>','_self','')"; /></td>
	</tr>
	<?php
	}
}

?>
</table>
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<?php
}
?>
<hr>
<br>
<form method=post action="recherche_complexe.php" >
<table align='center'><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESST399 ?>","create"); //text,nomInput</script>
</td></tr></table>
<br><br>
</form>


     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ):
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
   </BODY>
   </HTML>
<script>
    $(document).ready(function(){
        $('#search').keyup(function(){
            var query = $(this).val();
            if(query != '')
            {
                $.ajax({
                    url:"librairie_php/search_eleve.php",
                    method:"POST",
                    data:{query:query}, success:function(data)
                    {                        $('#userList').fadeIn();
                        $('#userList').html(data);
                    }
                });
            }
        });
        $(document).on('click', 'li', function(){
            $('#search').val($(this).text());
            $('#userList').fadeOut();
        });
    });
</script>
   <?php @Pgclose() ?>
