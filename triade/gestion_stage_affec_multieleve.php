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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxStage.js"></script>
<script language="JavaScript" src="./librairie_js/lib_stage.js"></script>
<script language="JavaScript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/xorax_serialize.js"></script>
<title>Affectation de plusieurs étudiants à un stage</title>
<script>
function verifSiAutre(idfen,value) {
	document.getElementById("service_"+idfen).value=value;
	document.getElementById("aff_"+idfen).style.display='block';
}

</script>
</head>
<body  id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
?>


<script>
function enrg(i) {

	var ident=document.getElementById("ident_"+i).value;
	var ideleve=document.getElementById("ideleve_"+i).value;
	var contact=document.getElementById("contact_"+i).value;
	var adressesiege=document.getElementById("adressesiege_"+i).value;
	var activite=document.getElementById("activite_"+i).value;
	var activiteprin=document.getElementById("activiteprin_"+i).value;
	var email=document.getElementById("email_"+i).value;
	var information=document.getElementById("information_"+i).value;
	var activite2=document.getElementById("activite2_"+i).value;
	var activite3=document.getElementById("activite3_"+i).value;
	var fonction=document.getElementById("fonction_"+i).value;
	var nbchambre=document.getElementById("nbchambre_"+i).value;
	var siteweb=document.getElementById("siteweb_"+i).value;
	var grphotelier=document.getElementById("grphotelier_"+i).value;
	var nbetoile=document.getElementById("nbetoile_"+i).value;
	var registrecommerce=document.getElementById("registrecommerce_"+i).value;
	var siren=document.getElementById("siren_"+i).value;
	var siret=document.getElementById("siret_"+i).value;
	var formejuridique=document.getElementById("formejuridique_"+i).value;
	var secteureconomique=document.getElementById("secteureconomique_"+i).value;
	var INSEE=document.getElementById("INSEE_"+i).value;
	var NAFAPE=document.getElementById("NAFAPE_"+i).value;
	var NACE=document.getElementById("NACE_"+i).value;
	var typeorganisation=document.getElementById("typeorganisation_"+i).value;
	var lieu=document.getElementById("lieu_"+i).value;
	var postal=document.getElementById("postal_"+i).value;
	var ville=document.getElementById("ville_"+i).value;
	var pays=document.getElementById("pays_"+i).value;
	var tel=document.getElementById("tel_"+i).value;
	var fax=document.getElementById("fax_"+i).value;
	var loge=(document.getElementById("loge_"+i).checked) ? "1" : "0" ;
	var nourri=(document.getElementById("nourri_"+i).checked) ? "1" : "0" ;
	var postal=document.getElementById("postal_"+i).value;
	var responsable=document.getElementById("responsable_"+i).value;
	var nom_entreprise_via_central=document.getElementById("nom_entreprise_via_central_"+i).value;

//	var alternance=document.getElementById("alternance_"+i).value;
//	var dateDebutAlternance=document.getElementById("dateDebutAlternance_"+i).value;
//	var dateFinAlternance=document.getElementById("dateFinAlternance_"+i).value;
//	var jourstage=document.getElementById("jourstage_"+i).value;
//	var idtuteur=document.getElementById("idtuteur_"+i).value;
//	var horairedebutjournalier=document.getElementById("horairedebutjournalier_"+i).value;
//	var horairefinjournalier=document.getElementById("horairefinjournalier_"+i).value;
//	var date2=document.getElementById("date2_"+i).value;
//	var idprof2=document.getElementById("idprof2_"+i).value;
//	var idprof=document.getElementById("idprof_"+i).value;
//	var date1=document.getElementById("date_"+i).value;
//	var raison=document.getElementById("raison_"+i).value;
//	var info=document.getElementById("info_"+i).value;
//	var xservice=document.getElementById("xservice_"+i).value;

	var idstage=document.getElementById("idstage_"+i).value;


	var service=document.getElementById("service_"+i).value;

	var indemnitestage=document.getElementById("indemnitestage_"+i).value;
	var saisie_classe=document.getElementById("saisie_classe_"+i).value;
	var divid="affrep_"+i;

	indemnitestage=encodeURIComponent(indemnitestage);
	indemnitestage=indemnitestage.replace("%E2%82%AC","EURO");

    //alert(service);
	var myAjax = new Ajax.Request(
		"ajaxEnrgStageMultiEleve.php",
		{	method: "post",
			parameters : "i="+i+"&ident="+ident+"&ideleve="+ideleve+"&contact="+encodeURIComponent(contact)+"&adressesiege="+encodeURIComponent(adressesiege)+"&activite="+encodeURIComponent(activite)+"&activiteprin="+encodeURIComponent(activiteprin)+"&email="+email+"&information="+encodeURIComponent(information)+"&activite2="+encodeURIComponent(activite2)+"&activite3="+encodeURIComponent(activite3)+"&fonction="+encodeURIComponent(fonction)+"&nbchambre="+nbchambre+"&siteweb="+siteweb+"&grphotelier="+grphotelier+"&nbetoile="+nbetoile+"&registrecommerce="+registrecommerce+"&siren="+siren+"&siret="+siret+"&formejuridique="+formejuridique+"&secteureconomique="+secteureconomique+"&INSEE="+INSEE+"&NAFAPE="+NAFAPE+"&NACE="+NACE+"&typeorganisation="+typeorganisation+"&lieu="+encodeURIComponent(lieu)+"&postal="+postal+"&ville="+encodeURIComponent(ville)+"&pays="+encodeURIComponent(pays)+"&tel="+tel+"&fax="+fax+"&loge="+loge+"&nourri="+nourri+"&postal="+postal+"&responsable="+encodeURIComponent(responsable)+"&tel="+tel+"&service="+encodeURIComponent(service)+"&indemnitestage="+indemnitestage+"&saisie_classe="+saisie_classe+"&idstage="+idstage+"&createstage=1&nom_entreprise_via_central="+encodeURIComponent(nom_entreprise_via_central),
			asynchronous: true,
			timeout: 5000,
			onComplete: function(transport) {  
				if (200 == transport.status)  {
					document.getElementById(divid).innerHTML =transport.responseText;
				}
			} 
		}
	);
}

</script>


<table border="0" align="center" width="100%" >
<tr><td valign='top'>
	<table width='100%' border='1' cellspacing='0' bordercolor='#CCCCCC' >
	<tr>
		<td bgcolor='yellow' width='15%' >&nbsp;<font class='T2'><?php print LANGSTAGE100 ?></font></td>
		<td bgcolor='yellow' width='15%' >&nbsp;<font class='T2'><?php print LANGSTAGE101 ?></font></td>
		<td bgcolor='yellow' width='5%' >&nbsp;<font class='T2'><?php print LANGSTAGE102 ?></font></td>
		<td bgcolor='yellow' width='5%' >&nbsp;<font class='T2'><?php print LANGSTAGE103 ?></font></td>
		<td bgcolor='yellow' width='5%' >&nbsp;<font class='T2'><?php print LANGSTAGE104 ?></font></td>
		<td bgcolor='yellow' width='5%' >&nbsp;<font class='T2'><?php print LANGSTAGE105 ?></font></td>
		<td bgcolor='yellow' width='5%' >&nbsp;<font class='T2'><?php print LANGSTAGE106 ?></font></td>
		<td bgcolor='yellow' width='9%' >&nbsp;<font class='T2'><?php print LANGSTAGE107 ?></font></td>
	</tr>
<?php
$saisie_classe=$_POST["saisie_classe"];
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);
$cl=$data[0][0];

$p=''; $urlcentrale=''; 
if (file_exists("./common/config.centralStageClient.php")) {
	include_once("./common/config.centralStageClient.php");
	$urlcentrale=URLCENTRALSTAGE;
	$p=PASSCENTRALSTAGE;
}

for($i=0;$i<count($data);$i++) {
		print "<tr id=\"tr$i\" class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
		$nomprenom="<font class='T2'>".ucwords($data[$i][2])." ".ucfirst($data[$i][3])."</font>";
		print "<td valign='top' >&nbsp;<input type='hidden' name='ideleve_$i' value='".$data[$i][1]."' id='ideleve_$i'  />";
		infoBulleEleveSansLoupe($data[$i][1],$nomprenom);
		print "</td>";
		print "<td valign='top' >&nbsp;<font class='T1'>";
		$num_stage="";
		$id_entreprise="";
		$service="";
		$loger="";
		$nourri="";
		$indemnitestage="";
		$datastageeleve=affiche_stage_multiple($data[$i][1]);
		for($j=0;$j<count($datastageeleve);$j++) {
			$num_stage=$datastageeleve[$j][0];
			$id_entreprise=$datastageeleve[$j][1];
			$service=$datastageeleve[$j][2];
			$loger=$datastageeleve[$j][3];
			$nourri=$datastageeleve[$j][4];
			$indemnitestage=$datastageeleve[$j][5];
		}
		// id_eleve,id_entreprise,lieu_stage,ville_stage,id_prof_visite,date_visite_prof,loger,nourri,passage_x_service,raison,info_plus,num_stage,code_p,tuteur_stage,tel,compte_tuteur_stage,alternance,jour_alternance,dateDebutAlternance,dateFinAlternance,horairedebutjournalier,horairefinjournalier,date_visite_prof2,id_prof_visite2,service,indemnitestage,pays_stage,fax,autre_responsable
		select_stage_multi2($saisie_classe,$i,$num_stage);
		print "</font></td>";
		print "<td valign='top' >&nbsp;<font class='T2'>";
		print "<select name='ident_$i' id='ident_$i' onChange=\"checkListMulti(this.value,'$urlcentrale','$p','".PRODUCTID."','$i');\"  >";
		select_recherche_entreprise("45",$id_entreprise);
		print "<option id='select0'>".LANGCHOIX."</option>";	
		print "<optgroup label='Entreprise Interne'>";
		select_entreprise_limit("25");
	        if (file_exists("./common/config.centralStageClient.php")) {
			print "<optgroup label='Central de Stage'>";
			print "<script src='$urlcentrale/ajaxEntrepriseCentraleStage.php?productid=".PRODUCTID."&p=$p' ></script>";
		}	
		print "</select><br /><span id='patient_$i'></span>";	
		print "</font>";
		print "<div id='info_$i' ></div>";
		print "<input type='hidden' name='lieu_$i' 		id='lieu_$i' />";
		print "<input type='hidden' name='postal_$i' 		id='postal_$i' />";
		print "<input type='hidden' name='ville_$i' 		id='ville_$i' />";
		print "<input type='hidden' name='pays_$i' 		id='pays_$i' />";
		print "<input type='hidden' name='responsable_$i' 	id='responsable_$i' />";
		print "<input type='hidden' name='tel_$i' 		id='tel_$i' />";
		print "<input type='hidden' name='fax_$i' 		id='fax_$i' />";
		print "<input type='hidden' name='nom_entreprise_via_central_$i' id='nom_entreprise_via_central_$i' />";
		print "<input type='hidden' name='registrecommerce_$i' 	id='registrecommerce_$i' >";
		print "<input type='hidden' name='siren_$i' 		id='siren_$i'>";
		print "<input type='hidden' name='siret_$i' 		id='siret_$i' >";
		print "<input type='hidden' name='formejuridique_$i' 	id='formejuridique_$i' >";
		print "<input type='hidden' name='secteureconomique_$i'	id='secteureconomique_$i' >";
		print "<input type='hidden' name='INSEE_$i' 		id='INSEE_$i' >";
		print "<input type='hidden' name='NAFAPE_$i'		id='NAFAPE_$i' >";
		print "<input type='hidden' name='NACE_$i'		id='NACE_$i'  > ";
		print "<input type='hidden' name='typeorganisation_$i' 	id='typeorganisation_$i' >";
		print "<input type='hidden' name='contact_$i' 		id='contact_$i'  >";
		print "<input type='hidden' name='fonction_$i' 		id='fonction_$i'  >";
		print "<input type='hidden' name='adressesiege_$i' 	id='adressesiege_$i'  >";
		print "<input type='hidden' name='activite_$i' 		id='activite_$i' >";
		print "<input type='hidden' name='activite2_$i' 	id='activite2_$i' >";
		print "<input type='hidden' name='activite3_$i' 	id='activite3_$i' >";
		print "<input type='hidden' name='activiteprin_$i' 	id='activiteprin_$i' >";
		print "<input type='hidden' name='grphotelier_$i'	id='grphotelier_$i'  >";
		print "<input type='hidden' name='nbetoile_$i'  	id='nbetoile_$i' >";
		print "<input type='hidden' name='nbchambre_$i' 	id='nbchambre_$i'  >";
		print "<input type='hidden' name='email_$i'		id='email_$i'  >";
		print "<input type='hidden' name='siteweb_$i' 		id='siteweb_$i'  >";
		print "<input type='hidden' name='information_$i' 	id='information_$i' >";
		print "</td>";
		print "<td valign='top'  >";
		print "<select id='select_$i' onChange=\"verifSiAutre('$i',this.value)\" >";
		print "<option value='' 		id='select0' >".LANGCHOIX."</option>";
		$selected="";
		if ($service == 'autre') $selected="selected='selected'";
		print "<option value='autre' 		id='select1' $selected >Autres</option>";
		$selected="";
		if ($service == 'Réception') $selected="selected='selected'";
     		print "<option value='Réception' 	id='select1'  $selected >Réception</option>";
		$selected="";
		if ($service == 'Restaurant/Réception') $selected="selected='selected'";
      		print "<option value='Restaurant/Réception' id='select1'  $selected >Restaurant/Réception</option>";
		$selected="";
		if ($service == 'Housekeeping') $selected="selected='selected'";
       		print "<option value='Housekeeping' 	id='select1'  $selected >Housekeeping</option>";
		$selected="";
		if ($service == 'Réservation') $selected="selected='selected'";
       		print "<option value='Réservation' 	id='select1'  $selected >Réservation</option>";
		$selected="";
		if ($service == 'Rotation F&B') $selected="selected='selected'";
       		print "<option value='Rotation F&B' 	id='select1'  $selected >Rotation F&B</option>";
		print "</select>";
		print "<input type='text' id='service_$i' name='service_$i' size='22' style='display:none' />";
		print "</td>";
		print "<td valign='top'  >&nbsp;<font class='T2'><input type='text' size='30' id='indemnitestage_$i' name='indemnitestage_$i' value=\"$indemnitestage\"  maxlength='200' ></font></td>";
		$indemnitestage="";
		$checked="";
		if ($loger == 1) $checked="checked='checked'" ;
		print "<td valign='top'  >&nbsp;<font class='T2'><input type='checkbox' name='loge_$i' id='loge_$i' value='1' class='btradio1' $checked > (oui)</font></td>";
		$checked="";
		if ($nourri == 1) $checked="checked='checked'" ;
		print "<td valign='top'  >&nbsp;<font class='T2'><input type='checkbox' name='nourri_$i' id='nourri_$i' value='1' class='btradio1' $checked  > (oui)</font></td>";
		print "<td valign='top' ><input type='hidden' name='saisie_classe_$i' value='$saisie_classe' id='saisie_classe_$i' >";
		print "&nbsp;&nbsp;<span id='aff_$i' style='display:none'><script language=JavaScript>buttonMagicSubmit4('".LANGENR."','','enrg($i)')</script></span>&nbsp;&nbsp;";
		print "<span id='affrep_$i'></span></td>";
		print "</tr>";

}
?>
</table>
<br><br>
<table align=center border=0 ><tr><td><script language=JavaScript>buttonMagicFermeture();</script>&nbsp;&nbsp;</td></tr></table>

</tr></td></table>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY></HTML>
<?php
Pgclose();
?>
