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
<title>Administration calend.</title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if (DSTPROF == "oui") {
        validerequete("3");
}else{
        validerequete("2");
}
$cnx=cnx();
$saisiejour=$_GET["saisiejour"];
$saisiemois=$_GET["saisiemois"];
$saisieannee=$_GET["saisieannee"];

?>
             <BR>
<center>
<a href="calendrier_config_dst2.php?saisiejour=<?php print $saisiejour?>&saisiemois=<?php print $saisiemois?>&saisieannee=<?php print $saisieannee?>"><?php print LANGDST9?></A>
-
<a href="calendrier_config_dst3.php?saisiejour=<?php print $saisiejour?>&saisiemois=<?php print $saisiemois?>&saisieannee=<?php print $saisieannee?>"><?php print LANGDST10?></A>
</center>

<BR><UL>
             <form method='post' onsubmit="return valid_calendrier()" name='formulaire' >
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
	     <font class=T2><?php print LANGCALEN2 ?> </font><input type=text name=saisiedate value='<?php print "$date" ?>' size="10" onfocus="this.blur()"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><BR><BR>

	<?php 
	     for($a=1;$a<10;$a++) {
	?>
             D.S.T  <input type=text name="saisie_dst<?php print $a ?>" size="30" maxlength="30" > &nbsp;&nbsp;<?php print LANGDST11?> &nbsp;&nbsp;
                     <select name="dst<?php print $a ?>" >
                     <option value="choix" id="select0"><?php print LANGCHOIX?></option>
<?php select_classe_nom_2() ?>
		     </select>
			à <input type=text name="heure<?php print $a ?>" size=4 value="hh:mm" onclick="this.value=''" onKeyPress="onlyChar2(event)" > 
			durant  <select name="duree<?php print $a ?>">
					<option value="choix" id="select0" ><?php print LANGCHOIX?></option>
					<option value="0.30" id="select1" >30 minutes</option>
					<option value="1" id="select1" >1 heure</option>
					<option value="1.30" id="select1" >1 heure 30</option>
					<option value="2" id="select1" >2 heures</option>
					<option value="2.30" id="select1" >2 heure 30</option>
					<option value="3" id="select1" >3 heures</option>
					<option value="3.30" id="select1" >3 heure 30</option>
					<option value="4" id="select1" >4 heures</option>
					<option value="4.30" id="select1" >4 heure 30</option>
					<option value="5" id="select1" >5 heures</option>
					<option value="5.30" id="select1" >5 heure 30</option>
					<option value="6" id="select1" >6 heures</option>
					<option value="6.30" id="select1" >6 heure 30</option>
					<option value="7" id="select1" >7 heures</option>
					<option value="7.30" id="select1" >7 heure 30</option>
					<option value="8" id="select1" >8 heures</option>
					<option value="8.30" id="select1" >8 heure 30</option>
				</select>
			 en salle <select name="idsalle<?php print $a ?>" >
					<option value="0" id="select0" ><?php print LANGCHOIX?></option>
					<?php
					$data=list_salle();
					// id,libelle,info,type
					for($i=0;$i<count($data);$i++) {
						print "<option value='".$data[$i][0]."' id='select1' >".$data[$i][1]."</option>";
					} 
					?>
				  </select>
			<BR>
			
	<?php } ?>

             <BR>&nbsp;&nbsp;
	     <script language=JavaScript>buttonMagicSubmit("Enregistrer","creat"); //text,nomInput</script>
	     <script language=JavaScript>buttonMagicFermeture()</script>
             </FORM>
<?php
if (isset($_POST["creat"])) {
	$nb=1;
	while ($nb < 10 ) {
		$valeur="saisie_dst".$nb;
		$valeur_classe="dst".$nb;
		$valeur_heure="heure".$nb;
		$valeur_duree="duree".$nb;
		$valeur_idsalle="idsalle".$nb;
		if ( (strlen($_POST[$valeur]) >= 2) && ($_POST[$valeur_classe] != "choix" ) ) {
			$date_form=dateFormBase($_POST["saisiedate"]);
	 		$cr=calend_dst($date_form,$_POST[$valeur],$_POST[$valeur_classe],$_POST[$valeur_heure],$_POST[$valeur_duree],$_POST[$valeur_idsalle]);
        		if($cr == 1){
                		// alertJs("DST Enregistrée -- Service Triade");
        			history_cmd($_SESSION["nom"],"AJOUT","D.S.T.");
				if ($_POST[$valeur_idsalle] > 0) {
					$heure1=conv_en_seconde($_POST[$valeur_heure]);
					if (preg_match('/\./',$_POST[$valeur_duree])) {
						list($heure,$minute)= preg_split('/\./',$_POST[$valeur_duree]);
					}else{
						$heure=$_POST[$valeur_duree];
					}
					$heure2=$heure1+($minute*60)+($heure*3600);
					$heure2=calcul_hours($heure2);
					$info="D.S.T. : ".$_POST[$valeur];
					$confirm=0; $periode=''; $nbperiode='';
					create_resa($_POST[$valeur_idsalle],$_POST["saisiedate"],$_SESSION["id_pers"],$_POST[$valeur_heure],$heure2,$info,$confirm,$periode,$nbperiode);
				}
			}
		}
		$nb=$nb+1;
	}
//	print "<script>parent.window.close();</script>"	;

}

Pgclose();
?>
</BODY></HTML>
