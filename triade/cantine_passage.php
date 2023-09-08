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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_cantine.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<script language="JavaScript" src="./librairie_js/xorax_serialize.js"></script>
<script>
ajaxCherchePlateau = function (id) {
	var myAjax = new Ajax.Request("ajaxCherchePlateauCantine.php",{method:"post",parameters:"id="+id,asynchronous:true,timeout: 5000,onComplete: displayText});
}

displayText = function (request) {
	var tab=unserialize(request.responseText);
	var listing=document.getElementById("menucantine");
	var chaine=tab[0][2];
	var reg1=new RegExp(" ", "g");
	var tableau=chaine.split(reg1);
	var prix=tableau[0];
	var unite=tableau[1];
	var reg=new RegExp(",", "g");
	prix.replace(reg,".");
	prix=parseFloat(prix);
	prix=(Math.round(prix*100))/100;
	listing.innerHTML+="&nbsp;&nbsp;&nbsp;<img src='image/lu.gif' >&nbsp;<font class='T2'>"+unescape(tab[0][1])+" : "+prix+" "+unite+"</font><br>";
	var somme=document.getElementById("somme");
	if (window.ActiveXObject){
		var newElementList = document.createElement("<input name='plat[]' >");	
	}else{
		var newElementList = document.createElement("input");
		newElementList.setAttribute("name","plat[]");	
	}
	newElementList.setAttribute("type","hidden");
	newElementList.setAttribute("value",tab[0][4]+"#||#"+tab[0][1]);
	newElementList.setAttribute("readonly","readonly");
	somme.appendChild(newElementList);
	document.formulaire.platok.value='1';

	var total=parseFloat(getInnerText(document.getElementById("total")));
	var nb=parseFloat(tab[0][4]);
	total=total+nb;
	document.getElementById("total").innerHTML=total;
}


function ajoutPlateau(val) {
	ajaxCherchePlateau(val);
}

</script>
<script type="text/javascript">
function valideFormulaire() {
	if (document.formulaire.platok.value == "0") {
		new Effect.Grow('validationnon', 1); 
		return false;
	}else{
		document.formulaire.valide.value=1;
		document.formulaire.submit();
	}
}

function listenKey(code) {
	//	 alert("vous avez frapper la touche:"+code);
	if (code == "113") {
		document.formulaire.codebar.focus();
		document.getElementById('action').value='Lecture en cours...'; 
		document.getElementById('enreff').style.display='none'; 
	}

	if (code == "119") { valideFormulaire(); }
}
if (navigator.appName=="Microsoft Internet Explorer") {
 function toucheA() {listenKey(event.keyCode)};
 document.onkeydown = toucheA;
}
else {
 function toucheB(evnt) {listenKey(evnt.keyCode)};
 document.onkeydown = toucheB;
}


function verifScrool() {
	element = document.getElementById('menucantine');
        element.scrollTop = element.scrollHeight;
	window.setTimeout('verifScrool()','300');
}
</script>

</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="verifScrool();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="100%">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Passage cantine" ?></font></b> - 
<?php 
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-size:11px;"); 
?>

</td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php
include_once('./librairie_php/db_triade.php');
$cnx=cnx();
$image="idP=''";
if ((verifDroit($_SESSION["id_pers"],"cantine")) || ($_SESSION["membre"] == "menuadmin" )) { 
/*
	if (isset($_GET["idsupp"])) {
		$idpers2=$_GET["idpers2"];
		$membre2=$_GET["membre"];
		if ($membre2 == "menueleve") {
			$nom2=recherche_eleve_nom($idpers2);
			$prenom2=recherche_eleve_prenom($idpers2); 
			$nomprenom2="$nom2 $prenom2";
		}else{
			$membre2=renvoiTypePersonneMembre(recherche_type_personne($idpers2)); 
			$nomprenom2=recherche_personne2($idpers2);
		}
		$cr=suppOperationCantine($_GET["idsupp"]);
		if ($cr) { history_cmd($_SESSION["nom"],"CANTINE","Suppression Opération sur ($nomprenom2) ");}
	}
 */

	$alerteButton=1;
	$ideleve="";
	$membre="";
	$valide="";

	$colorB="735770";
	$disabled2="disabled='disabled'";

	if (isset($_POST["codebar"])) {
		

		$data=rechercheIdPersViaCodeBarre(trim($_POST["codebar"])); // id,valide,membre
		if (count($data) > 0) {
			$ideleve=$data[0][0];
			$valide=$data[0][1];
			$membre=$data[0][2];
			$membre2='NONELE';
		}

		if ($membre == "menueleve") { $fonction="Elève en ".chercheClasse_nom(chercheClasseEleve($ideleve)); $membre2='ELE'; $attribue="menueleve"; $image="idE=$ideleve"; } 
		if ($membre == "menuprof")  { $fonction="Enseignant"; $attribue="menuautre"; $indiceSalairePers=recupIndiceSalairePers($ideleve);$image="idP=$ideleve";  }
		if ($membre == "menuadmin") { $fonction="Direction"; $attribue="menuautre";$indiceSalairePers=recupIndiceSalairePers($ideleve); $image="idP=$ideleve"; }
		if ($membre == "menuscolaire") { $fonction="Vie Scolaire"; $attribue="menuautre";$indiceSalairePers=recupIndiceSalairePers($ideleve); $image="idP=$ideleve"; }
		if ($membre == "menupersonnel") { $fonction="Personnel"; $attribue="menuautre";$indiceSalairePers=recupIndiceSalairePers($ideleve); $image="idP=$ideleve"; }
		

		if ($valide == 1) {
			$nom=strtoupper(recherche_personne_nom($ideleve,$membre2));
			$prenom=ucwords(recherche_personne_prenom($ideleve,$membre2));
			$nomprenom="$nom $prenom";
			$alerteButton=0;
			$colorB="C60417";
			$disabled2="";
			$verif=verifCompteDejaPasse($ideleve,$membre);
			if ($verif > 0) {
				$ALERTE2="<font class='T2' color='red'>BADGE DEJA UTILISE !!!</font>";
			}

		}elseif ($valide == 0) {
			$fonction="";
			$ALERTE="<font class='T2' color='red'>BADGE NON VALIDE !!!</font>";
		}else{
			$prenom="";
			$ALERTE="<font class='T2' color='red'>BADGE NON LU !!!</font>";
		}
	}

	if (isset($_POST["reset"])) { $ALERTE=""; }

	if ((isset($_POST["valide"])) && ($_POST["valide"] == 1)) { 
		$ALERTE=""; 
		$idpers=$_POST["idpers"];
		$plat=$_POST["plat"];
		$membre=$_POST["membre"];
		$cr=enrPlateauCompte($idpers,$plat,$membre);
		$ALERTCONFIRM="<span id='enreff'>&nbsp;&nbsp;<b><font color=red class=T2 >Enregistrement effectué</font></b></span>";
	}
?>
<div id="validationnon" style="position:absolute;top:140;left:330;display:none;width:350px;height:130px;padding:1px;border:1px #666 solid;background-color:yellow;z-index:1000">
	<center>
	<br><br>
	<b><font class=T2 color=red>AUCUN PLAT D'ENREGISTRE</font></b><br><br>
	<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="new Effect.Shrink('validationnon', 1)" />
	</center>
</div>
<div id="viscredit" style="position:absolute;top:140;left:330;display:none;width:450px;height:290px;padding:1px;border:1px #666 solid;background-color:#ddd;z-index:1000">
		<?php   ?>
		<form><br /><br />
		&nbsp;&nbsp;&nbsp;<font class=T2><b>Créditer le compte de <?php print $nomprenom ?></b></font>
		<br /><br />
		<ul><table border='0' >	
		<tr><td align='right'><font class='T2'>&nbsp;Date :</font></td><td><input type='text' name='date' size='10' value='<?php print dateDMY() ?>' /></td></tr>
		<tr><td height=20></td></tr>
		<tr><td align='right'><font class='T2'>&nbsp;Crédit :</font></td><td><input type='text' name='credit' size='10' value='0' onclick="this.value=''" /></td></tr>
		<tr><td height=20></td></tr>	
		<tr><td align='right'><font class='T2'>&nbsp;Détail :</font></td><td><input type='text' name='detail' size='50' maxlength='250' /></td></tr>
		<tr><td height=20></td></tr>
		<tr><td align='right' colspan='2' ><input type='button' onclick="enrCreditCantine('<?php print $ideleve ?>','<?php print $membre ?>',this.form.date.value,this.form.credit.value,this.form.detail.value,'retourenr0')" value="<?php print LANGENR ?>" class='bouton2' />
		
		&nbsp;&nbsp;<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="new Effect.Shrink('viscredit', 1)" /><br><br>
		<span id='retourenr0' style='color:red; '></span>		
		</table></ul>
		</form>
	</div>
<?php

	print "<form name='formulaire' method='post' action='cantine_passage.php'>";
	print "<table width='100%' border='1' style='padding:10px;border-radius: 16px;-moz-border-radius: 16px;-webkit-border-radius: 16px;' >";
	print "<tr><td width='50%' bgcolor='#FFFFFF' valign='top' >";
	print "$ALERTCONFIRM <br> <font class=T2>&nbsp;N° Badge : </font>";
	print "<input type=text name='codebar' STYLE='color:#000066;background-color:#CCCCFF;height: 15px; width:70px ; overflow:hidden'>&nbsp;<input type='button' value='Activer Lecture Badge [F2] ' STYLE='border-radius: 16px;-moz-border-radius: 16px;-webkit-border-radius: 16px;color:#000066;background-color:#CCCCFF;height: 35px;box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-moz-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-webkit-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);' onclick=\"document.formulaire.codebar.focus();this.value='Lecture en cours...'; document.getElementById('enreff').style.display='none'; \"  name='action' id='action' /><br /><br /><hr><br />";
	$data=recupConfigCantine(); // id,libelle,prix,attribue,indice_salaire,platdefault
	for($i=0;$i<count($data);$i++) {
		$platdefault=$data[$i][5];
		$color="BFB580";
		$disabled="disabled='disabled'";
		$indiceSalaire=$data[$i][4];
		if ($data[$i][3] == "tous") { $disabled="";  $color="FFA54A";  }
		if ( (($data[$i][3] == $attribue ) && ($indiceSalairePers == $indiceSalaire)) || (($data[$i][3] == $attribue ) && ($indiceSalairePers == 0)) )  { $disabled="";  $color="FFA54A";  }
		if (trim($data[$i][1]) == "") continue ;
		print "<input type='button' value='".$data[$i][1]."' class='shadow' STYLE='box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-moz-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-webkit-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);border-radius: 16px;-moz-border-radius: 16px;-webkit-border-radius: 16px;color:#800000;background-color:#$color;font-size:larger;font-weight: bold ; height:45px;margin:15px;' $disabled onclick=\"ajoutPlateau('".$data[$i][0]."')\" /> ";
		if (($platdefault == 1) && ($disabled != "disabled='disabled'")) {
			print "<script>ajoutPlateau('".$data[$i][0]."')</script>";
		}
	}
	print "</td>";

	print "<td bgcolor='#FFFFFF'  valign='top' >";
	print "&nbsp;<img src='image_trombi.php?$image' id='photopersonne' align='left' style=\"box-shadow: 5px 5px 5px #656565;border-radius: 20px;\" >";
	print "&nbsp;&nbsp;<table border=0>";
	print "<tr><td align='right' >&nbsp;&nbsp;<font class='T2'>Nom : </font></td><td><font class='T2 shadow'>$ALERTE$nom</font></td></tr>";
	print "<tr><td height='7' ></td></tr>";
	print "<tr><td align='right' >&nbsp;&nbsp;<font class='T2'>Prénom : </font></td><td><font class='T2 shadow'>$prenom</font></td></tr>";
	print "<tr><td height='7' ></td></tr>";
	print "<tr><td align='right' >&nbsp;&nbsp;<font class='T2'>Fonction : </font></td><td><font class='T2 shadow'>$fonction</font></td></tr>";
	print "</table>";
	print "<br><br>";
	print "&nbsp;&nbsp;&nbsp;<font class='T2 shadow' ><b>Contenu du plateau</b></font><br><br>";
	print "<div id='menucantine' style='height:100px;overflow:auto' ></div>";
	print "<br>&nbsp;&nbsp;&nbsp;<font class='T2 shadow' ><b> Total : <span id='total' >0</span> ".unitemonnaie()." </b></font>";
	if ($ideleve > 0) {
		$sommeComptePers=sommeComptaPers($ideleve,$membre);
	}else{
		$sommeComptePers=0;
	}
	print "<font class='T2'>&nbsp;&nbsp;&nbsp;(Compte restant : ".$sommeComptePers." ".unitemonnaie().")</font>";
	print "<br>";
	print "&nbsp;&nbsp;&nbsp;<input type=submit name='reset' value='Annuler' class='shadow' STYLE='box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-moz-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-webkit-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);border-radius: 16px;-moz-border-radius: 16px;-webkit-border-radius: 16px;color:#FFFFFF;background-color:#C60417;font-size:larger;font-weight: bold ; height:45px;margin:15px;'>";
	print "&nbsp;&nbsp;&nbsp;<input type=button  onclick='valideFormulaire();' class='shadow'  value='Confirmer [F8] ' STYLE='box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-moz-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-webkit-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);border-radius: 16px;-moz-border-radius: 16px;-webkit-border-radius: 16px; color:#FFFFFF;background-color:#$colorB;font-size:larger;font-weight: bold ; height:45px;margin:15px;' $disabled2 >";
	print "&nbsp;&nbsp;&nbsp;<input type=button value='Créditer' class='shadow' STYLE='box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-moz-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);-webkit-box-shadow: 5px 6px 5px 0px rgba(119, 119, 119, 0.71);border-radius: 16px;-moz-border-radius: 16px;-webkit-border-radius: 16px; color:#FFFFFF;background-color:#$colorB;font-size:larger;font-weight: bold ; height:45px;margin:15px;' $disabled2 onclick=\"new Effect.Grow('viscredit', 1); return false;\" >";
	print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color='red' class='T2'><b>$ALERTE2</b></font><br>";
	print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>Nombre de passage aujourd'hui : <b>".nbpassagecantine()."</b>&nbsp;&nbsp;&nbsp;(".dateDMY().") </i><br><br>" ;
	print "</td>";
	print "</tr>";
	print "</table>";
	print "<input type='hidden' name='idpers' value='$ideleve' >";
	print "<input type='hidden' name='membre' value='$membre' >";
	print "<input type='hidden' name='valide' value='0' >";
	print "<input type='hidden' name='platok' value='0' >";
	print "<p id='somme' ></p>";
	print "</form>";
?>
<table border='1' width=100% bgcolor='#FFFFFF' style="padding:5px;border: 1px solid #bbbbbb;border-radius: 16px;-moz-border-radius: 16px;-webkit-border-radius: 16px;border-collapse: collapse;" >
<tr>
<td bgcolor='yellow' width=5%><font class='T2'><b>&nbsp;Date&nbsp;</b></font></td>
<td bgcolor='yellow'><font class='T2'><b>&nbsp;Détail&nbsp;</b><i><font class=T1>(<i>les 10 dernières enregistrements</i>)</font></i></font></td>
<td bgcolor='yellow' width=5% align='right' colspan=2 ><font class='T2'><b>&nbsp;Montant&nbsp;<?php print unitemonnaie() ?>&nbsp;</b></font></td>
</tr>

<?php 
unset($data);
if ($ideleve > 0) {
	$data=recupComptaPers($ideleve,$membre); //date,prix,plateau,id
}
for($i=0;$i<count($data);$i++) {
	if ($i < 10) {
		$bgcolor=($data[$i][1] < 0) ? "bgcolor='#FF6262'" : "bgcolor='#B7FFB7'" ;
		print "<tr $bgcolor>";
		print "<td ><font class='T2'>&nbsp;".dateForm($data[$i][0])."</font></td>";
		print "<td ><font class='T2'>&nbsp;".urldecode($data[$i][2])."</font></td>";
		print "<td align='right' $bgcolor ><font class='T2'>".affichageFormatMonnaie($data[$i][1])."</font></td>";
		print "</tr>";
	}
	$total+=$data[$i][1];
}
	print "<tr bgcolor='#CCCCCC' >";
	print "<td colspan=2 align='right' id='bordure' ><font class='T2'>Totaux : </font></td>";
	print "<td align='right'  id='bordure'  ><font class='T2'><b>".affichageFormatMonnaie($total)."</b></font></td>";
	
	print "</tr>";
?>

</table>
<br>


<?php }else{ ?>
<br><font class="T2" id="color3"><center><img src="image/commun/img_ssl.gif" align='center' /> Accès réservé</center></font>
<br><br>
<?php } ?>
</td></tr></table>
</BODY></HTML>
