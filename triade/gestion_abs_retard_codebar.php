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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/xorax_serialize.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<script  language="JavaScript">
function fonc1() {
	// document.formulaire.reset();
	document.formulaire.retard_aucun.checked=true;
	document.formulaire.rien.disabled=false;
	document.getElementById('inf').style.visibility='hidden';

}
function fonc2() {
	var op=document.formulaire.saisie_heure.options.selectedIndex;
	if (document.formulaire.saisie_heure.options[op].value == "null") {
		document.formulaire.rien.disabled=true;
		document.getElementById('inf').style.visibility='visible';
	}else{
		document.getElementById('inf').style.visibility='hidden';
		if (document.formulaire.valide.checked == true) {
			document.formulaire.rien.disabled=false;
		}else{
			document.formulaire.rien.disabled=true;
		}
	}
}

</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<script language="JavaScript" >var envoiform=true; </script>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<form name="formulaire"  method=post action='gestion_abs_retard_codebar2.php' id='formulaire' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS25 ?> via code barre</font></b></td></tr>
<tr  id='cadreCentral0' >
<td>
<BR>
<!-- // fin  -->
<UL>
<br>
<font class=T2> Horaire : 
<select name="saisie_heure" onChange="fonc2()" id="saisie_heure" >
<option STYLE='color:#000066;background-color:#FCE4BA' value="null" ><?php print LANGCHOIX ?></option>
<?php
$disabled="disabled";
$data3=recupCreneauDefault("creneau"); // libelle,text
if (count($data3) > 0) {
	$data3=recupInfoCreneau($data3[0][1]); // libelle,dep_h,fin_h
	print "<option  id='select0' value=\"".trim($data3[0][0])."#".$data3[0][1]."#".$data3[0][2]."\" selected='selected' >".trim($data3[0][0])." : ".timeForm($data3[0][1])." - ".timeForm($data3[0][2])."</option>\n";
	$disabled="";
}else{
?>
<option STYLE='color:#000066;background-color:#FCE4BA' value="null" ><?php print LANGCHOIX ?></option>
<?php
}
select_creneaux2();
?>
	</select> - <input type=text name="datedepart" value="<?php print dateDMY() ?>" size=12 readonly class="bouton2" id="datedepart" /> <?php
	include_once("librairie_php/calendar.php");
	calendar('id1','document.formulaire.datedepart',$_SESSION["langue"],"1");
	?>

</font> 
<br><br>

<font class=T2>Matière : </font><select name="idmatiere" id="saisie_matiere">

<?php 
	if ($_SESSION["membre"] == "menuprof") {
		if (isset($_GET["smat"])) print "<option value=".$_GET["smat"]." id='select1' >".chercheMatiereNom($_GET["smat"])."</option>";
		if (PROFPACCESABSRTD == "oui") {
			if (!isset($_GET["smat"])) {
				print "<option id='select0' value='' >".LANGCHOIX."</option>";
				select_matiere3("20");
			}

		}
	}else{	
		print "<option id='select0' value='' >".LANGCHOIX."</option>";
		select_matiere3("20");
	}
?>
</select>
<br><br>

<font class=T2>Enseignant : </font><select name="idprof" id="saisie_prof">
<?php
	if ($_SESSION["membre"] == "menuprof") {
		print "<option value=".$_SESSION["id_pers"]." id='select1' >".recherche_personne2($_SESSION["id_pers"])."</option>";
	}else{	
		print "<option id='select0' value='' >".LANGCHOIX."</option>";
		select_personne_nom_len_id('ENS',25);
	}	
?>
</select>

<br><br>


<font class=T2>Nature (abs/retard) : </font><select name="type_abs">
<option value="absent" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGABS?></option>
<option value="retard" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGRTD?></option>
</select>


<br><br>


<font class=T2>Lecture code barre : </font> 
	<textarea name='codebar' rows=1 cols=15 STYLE='color:#000066;background-color:#CCCCFF;height: 15px; width:70px ; overflow:hidden ' onBlur="clearTimeout(idtime);this.value='NON-ACTIF';document.formulaire.action.value='Activer Ecoute code barre'"  onclick="func_codebar();" >NON-ACTIF</textarea> <input type="button" value="Activer Ecoute code barre" class="bouton2" onclick="document.formulaire.codebar.focus();func_codebar();this.value='Lecture en cours...'"  name='action' />


<script language="JavaScript" >

var productsList = [];
var element = 7 ;

ajaxChercheEleve = function (id)
{
	var myAjax = new Ajax.Request(
		"ajaxChercheNomPrenomEleve.php",
		{	method: "post",
			parameters : "idcodebarre="+id,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText
		}
	)
}

ajaxChercheHoraire  = function (id)
{
	var myAjax = new Ajax.Request(
		"ajaxChercheHoraire.php",
		{       method: "post",
			parameters : "idcodebarre="+id,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText2 
		}
	)
}

function supp(idelement) {
//	var listing=document.getElementById("listing");
	//	alert(idelement);
	delete(productsList[idelement]);
	document.getElementById("a"+idelement).style.display='none';
	document.getElementById("img"+idelement).style.display='none';
	document.getElementById("C"+idelement).style.display='none'; 
	document.getElementById("V"+idelement).style.display='none'; 

}

displayText2 = function (request) {
	// date,heure_debut,heure_fin,idmatiere,matiere,idprof,prof

        // dateForm($date)."#".timeForm($heure)."#".timeForm($heure_fin)."#$idmatiere#$matiere#$idprof#$prof"

	var reponse=request.responseText;
//	var tab=unserialize(reponse);
	var tab = new Array();
	tab=reponse.split('#');


	var date=tab[0];
	var heure_debut=tab[1];
	var heure_fin=tab[2];
	var idmatiere=tab[3];
	var matiere=tab[4];
	var idprof=tab[5];
	var prof=tab[6];

	var idheure=heure_debut+"-"+heure_fin+"#"+heure_debut+"#"+heure_fin;
	document.getElementById("datedepart").value=date;
	var newOption = document.createElement("option");
	newOption.setAttribute("value",idheure);
	newOption.setAttribute("id","select1");
	newOption.setAttribute("selected","selected");
	newOption.innerHTML=heure_debut+" - "+heure_fin;
	document.getElementById('saisie_heure').appendChild(newOption);
	newOption = document.createElement("option");
	newOption.setAttribute("value",idmatiere);
	newOption.setAttribute("id","select1");
	newOption.setAttribute("selected","selected");
	newOption.innerHTML=matiere;
	document.getElementById('saisie_matiere').appendChild(newOption);
	newOption = document.createElement("option");
	newOption.setAttribute("value",idprof);
	newOption.setAttribute("id","select1");
	newOption.setAttribute("selected","selected");
	newOption.innerHTML=prof;
	document.getElementById('saisie_prof').appendChild(newOption);
}

displayText = function (request) {

	var reponse=request.responseText;
	var tableau=reponse.split(":");
	var nomprenomeleve=tableau[1];
	var ideleve=tableau[0];

	var formulaire=document.getElementById("formulaire");

	if (nomprenomeleve != "???"){
		if (productsList[ideleve] == undefined || productsList[ideleve] == null) {

			var listing=document.getElementById("listing");

			var newElementList = document.createElement("input");
			newElementList.setAttribute("type","text");
			newElementList.setAttribute("value",nomprenomeleve);
			newElementList.setAttribute("readonly","readonly");
			newElementList.setAttribute("size","40");
			newElementList.setAttribute("className","bouton2");
			newElementList.setAttribute("id","V"+ideleve);

			var objLink = document.createElement("a");
			objLink.setAttribute('href','JavaScript:supp("'+ideleve+'");');
			objLink.setAttribute('title','Supprimer');
			objLink.setAttribute('id',"a"+ideleve);
			
			if (ideleve == "ERROR") {
				var objError = document.createElement("img");
				objError.setAttribute('src','image/commun/important.png');
				objError.setAttribute('title','Carte Invalide');
				objError.setAttribute('border','0');
				objLink.appendChild(objError);
			}else{
				if (window.ActiveXObject) {
					var newElementList2 = document.createElement("input name='ideleve[]' ");	
				}else{
					var newElementList2 = document.createElement("input");
					newElementList2.setAttribute("name","ideleve[]");	
				}
				newElementList2.setAttribute("type","hidden");
				newElementList2.setAttribute("className","bouton2");
				newElementList2.setAttribute("class","bouton2");
				newElementList2.setAttribute("value",ideleve);
				newElementList2.setAttribute("readOnly","readOnly");
				newElementList2.setAttribute("id","C"+ideleve);
				newElementList2.setAttribute("size","5");
			}
			var newElementList3 = document.createElement('img');
			newElementList3.setAttribute('src','image/commun/trash.png');
			newElementList3.setAttribute('id',"img"+ideleve);
			newElementList3.setAttribute('align','center');
			newElementList3.setAttribute('border','0');
			objLink.appendChild(newElementList3);

			listing.appendChild(newElementList);
			listing.appendChild(newElementList2);

			document.getElementById('listing').innerHTML+=" ";
			listing.appendChild(objLink);


		
			document.getElementById('listing').innerHTML+="<br>";
	

			productsList[ideleve]=ideleve;

		}
	}
}


function func_codebar() {
	var valeur=document.formulaire.codebar.value;
	var exp=new RegExp("^EDT","g");
	if (exp.test(valeur)) {
		ajaxChercheHoraire(valeur);
	}else{
		ajaxChercheEleve(valeur);
	}
	document.formulaire.codebar.value="";
	idtime=window.setTimeout('func_codebar()','500');
}

</script>
<br><br>
<font class="T2"><b>Liste des élèves :</b> </font><br><br>
<div id='listing' width=10></div>

</UL>

<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR?>","rien","<?php print $disabled ?>"); //text,nomInput</script>
<?php if ($_SESSION["membre"] != "menuprof") { ?>
<script language=JavaScript>buttonMagicRetour2('gestion_abs_retard.php','_self','Retour menu')</script>
<?php } ?>
</td></tr>
<tr><td><input type='checkbox' onclick="fonc2()" name="valide" > Valider l'enregistrement </td></tr>
</table>
<br>
<div id="inf" style='color:red' ><center><i>Indiquer heure d'abs/rtd</i></center></div>
<?php if ($disabled == '') {
	print "<script>document.getElementById('inf').style.visibility='hidden';</script>";
}
?>
<br>
</form>

     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
     <SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>

   </BODY></HTML>
