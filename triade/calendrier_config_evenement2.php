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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Administration du calendrier</title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
if (CALPROF == "oui") {
        validerequete("3");
}else{
        validerequete("2");
}
$saisiejour=$_GET["saisiejour"];
$saisiemois=$_GET["saisiemois"];
$saisieannee=$_GET["saisieannee"];
?>
<BR>
<center>
<a href="calendrier_config_evenement2.php?saisiejour=<?php print $saisiejour?>&saisiemois=<?php print $saisiemois?>&saisieannee=<?php print $saisieannee?>"><?php print LANGCALEN3?></A>
-
<a href="calendrier_config_evenement3.php?saisiejour=<?php print $saisiejour?>&saisiemois=<?php print $saisiemois?>&saisieannee=<?php print $saisieannee?>"><?php print LANGCALEN4?></A>
</center>
        <BR><BR><UL>
<form method=post name=formulaire onSubmit="return valid_cal_evnt()" >
             <?php
                   $jour="$saisiejour";
                   if ($saisiejour < 10) :
                        $jour="0$saisiejour";
                   endif ;
		   $saisiemois=stripslashes($saisiemois);

             if ($saisiemois == LANGMOIS1 ) : $date="$jour/01/$saisieannee"; endif ;
             if (($saisiemois == "C hwevrer")  ||   ($saisiemois == LANGMOIS2 )) : $date="$jour/02/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS3 ) : $date="$jour/03/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS4 ) : $date="$jour/04/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS5 ) : $date="$jour/05/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS6 ) : $date="$jour/06/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS7 ) : $date="$jour/07/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS8 ) : $date="$jour/08/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS9 ) : $date="$jour/09/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS10 ) : $date="$jour/10/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS11 ) : $date="$jour/11/$saisieannee"; endif ;
             if ($saisiemois == LANGMOIS12 ) : $date="$jour/12/$saisieannee"; endif ;

             ?>
             <?php print LANGCALEN2?> <input type=text name=saisiedate value='<?php print "$date" ?>' size="10" onfocus="this.blur()"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><BR><BR>
             <?php print LANGCALEN1?> 1 <input type=text name=saisieevenement1 size=60><BR>
             <?php print LANGCALEN1?> 2 <input type=text name=saisieevenement2 size=60><BR>
             <?php print LANGCALEN1?> 3 <input type=text name=saisieevenement3 size=60><BR>
             <?php print LANGCALEN1?> 4 <input type=text name=saisieevenement4 size=60><BR>
             <?php print LANGCALEN1?> 5 <input type=text name=saisieevenement5 size=60><BR>
             <?php print LANGCALEN1?> 6 <input type=text name=saisieevenement6 size=60><BR>
             <?php print LANGCALEN1?> 7 <input type=text name=saisieevenement7 size=60><BR>
             <?php print LANGCALEN1?> 8 <input type=text name=saisieevenement8 size=60><BR>
             <?php print LANGCALEN1?> 9 <input type=text name=saisieevenement9 size=60><BR>

 <BR>&nbsp;&nbsp;
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","creat"); //text,nomInput</script>
<script language=JavaScript>buttonMagicFermeture()</script>

             </FORM>
<?php
$cnx=cnx();
if (isset($_POST[creat])) {
        $nb=1;
        while ($nb < 10 ) {
                $valeur="saisieevenement".$nb;
		$valeur=$_POST[$valeur];
                $valeur=preg_replace('/script/i','',$valeur);
                $valeur=preg_replace('/SCRIPT/i','',$valeur);
                if  (strlen($valeur) >= 2) {
                        $date_form=dateFormBase($_POST[saisiedate]);
                        $cr=calend_evenement($date_form,$valeur);
                        if($cr == 1){
                            //     alertJs("Evenement Enregistré -- Service Triade");
				history_cmd($_SESSION[nom],"AJOUT","EVENEMENT");
                        }
                        else {
                                error(0);
                        }
                }
                $nb=$nb+1;
        }
        print "<script>parent.window.close();</script>" ;

}

Pgclose();
?>
        </BODY></HTML>
