<?php
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
include_once("./common/config.inc.php");
include_once("./common/productId.php");
include_once("./librairie_php/db_triade.php");
$productid=$_POST["productid"];
$p=$_POST["p"];
if ($productid != PRODUCTID) {
	$cnx=cnx();
	verifAccesCentrale("$productid","$p");
	PgClose($cnx);
}
$_SESSION["productidstage"]=$productid;
if (trim($productid) == "") { print "Session cloturée"; exit; }
$CENTRAL=0;
if ($productid == PRODUCTID) $CENTRAL=1;
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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" >
function valideStageEleve(productid,id,retourAffiche,idcentralestage,value) { 
	if (value == true) value=1;
	if (value == false) value=0;
	var divid="div"+retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxConfirmEtudiantCentral.php",
		{	method: "post",
			asynchronous: true,
			parameters: "productid="+productid+"&id="+id+"&idcentralestage="+idcentralestage+"&value="+value,
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					document.getElementById(divid).disabled=true;
				}
			}
		}
	);
	if (value == 1) document.getElementById('etA'+retourAffiche).style.display='block';
	if (value == 0) document.getElementById('etA'+retourAffiche).style.display='none';
}


function envoiMail(productid,id,retourAffiche,idcentralestage,email,nomprenometudiant,numbermail) {
	if (email == "") {
		alert("Email de l'entreprise non valide.");
	}else{
		var infocontenu=document.getElementById("infoA"+retourAffiche).value;
		document.getElementById("etA"+retourAffiche).innerHTML="<strong>en cours... </strong> <img src='image/commun/indicator.gif' />";	
		var myAjax = new Ajax.Request(
			"ajaxEnvoiMailCentral.php",
			{	method: "post",
				asynchronous: true,
				parameters: "numbermail="+numbermail+"&productid="+productid+"&id="+id+"&idcentralestage="+idcentralestage+"&email="+email+"&nomprenometudiant="+nomprenometudiant+"&infocontenu="+infocontenu,
				timeout: 5000,
				onComplete: function (request) {
					if ("ok" == request.responseText)  {
						//alert("mail"+retourAffiche);
						document.getElementById("mail"+retourAffiche).style.display='block';
						document.getElementById("etA"+retourAffiche).innerHTML="Envoy&eacute; ";
					}
				}
			}
		);
	} 
}

function modifEtatCentral(i,etat) {
	if (etat) {
		document.getElementById('modicentral'+i).value='1';
	}else{
		document.getElementById('modicentral'+i).value='0';
	}
}


</script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='cadreCentral0' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/langue.php");
include_once("./librairie_php/db_triade.php"); 
include_once("common/productId.php");
$cnx=cnx();
if (isset($_POST["attribe"])) { 
	enrcentralSouhait($_POST["idcentralstagesouhait"],$_POST["attribution"],$_POST["nb"],$_POST["productid"],$_POST["modifcentrale"]); 
	alertJs("Donnée enregistrée -- Service Triade");
}
if (isset($_POST["periode"])) {
	$idSouhait=$_POST["periode"];
}
$data=recupPeriodeStageCentralSouhait($idSouhait);
if (count($data)) {
	$periode1=dateForm($data[0][0]);
	$periode2=dateForm($data[0][1]);
	$nomstage=$data[0][2];
}
$fichier="./data/fichier_ASCII/CentralSouhait_".dateFormBase($periode1)."_".dateFormBase($periode2)."_".$_SESSION["idpers"].".pdf";
@unlink($fichier);
$fichierpdf="_".dateFormBase($periode1)."_".dateFormBase($periode2)."_".$_SESSION["idpers"].".pdf";

$titre="Souhaits pour la période : $periode1 au $periode2  ($nomstage) ";
?>
<font class=T2><?php print  $titre ?></font>
<br><br>
<table><tr><td><font class=T2>Document au format pdf : <input type=button onclick="open('visu_document_central.php?fichier=<?php print $fichierpdf ?>','_blank','');" value="<?php print "Récupération" ?>"  class="bouton2"> </font></td></tr></table>
<br>
<table width="100%" border="1" align="center">
<tr>
<td bgcolor='yellow' align='center' width=3% ><font class=T2>&nbsp;Détail&nbsp;</font></td>
<td bgcolor='yellow' align='center' ><font class=T2>Services</font></td>
<td bgcolor='yellow' align='center' width=1% ><font class=T2>&nbsp;Nbr&nbsp;</font></td>
<td bgcolor='yellow' align='center' ><font class=T2>Attribution</font></td>
</tr>
<?php
$afficheNbPage="oui";
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
//include_once('./librairie_pdf/lib.php');
$pdf=new PDF('L','mm','A4');  // declaration du constructeur
$pdf->AddPage();

$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(255);

$X=0;
$Y=0;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',16);
$pdf->MultiCell(290,10,"$titre",0,'C',0);	
$Y+=12;
$X=3;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(40,10,"Entreprise",1,'C',0);
$pdf->SetXY($X+=40,$Y);
$pdf->MultiCell(35,10,"Nb demandes",1,'C',0);
$pdf->SetXY($X+=35,$Y);
$pdf->MultiCell(27,10,"Sexe",1,'C',0);
$pdf->SetXY($X+=27,$Y);
$pdf->MultiCell(60,10,"Service",1,'C',0);
$pdf->SetXY($X+=60,$Y);
$pdf->MultiCell(70,10,"Observations",1,'C',0);
$pdf->SetXY($X+=70,$Y);
$pdf->MultiCell(60,10,"Atrributions",1,'C',0);

$etape=0;
$data=rechercheStageCentralSouhait($periode1,$periode2,$idSouhait); 
// id,datedemande,identreprise,sexe,service,observation,nbdemande,nomentreprisen,s.adresse,s.ville,s.code_p,s.contact,s.tel,s.fax,s.email,s.info_plu,idproductreserv,null,salaire,logement,pays,contact_fonction,web,grphotelier,nbetoile,nbchambre,qualite

$Y+=10;
for($i=0;$i<count($data);$i++) {
	$adresse=$data[$i][8];
	$id=$data[$i][0];
	$idproductresa=$data[$i][16];
	$ville=$data[$i][9];
	$ccp=$data[$i][10];
	$contact=$data[$i][11];
	$tel=$data[$i][12];
	$fax=$data[$i][13];
	$email=$data[$i][14];
	$infoplus=$data[$i][15];
	$sexe=$data[$i][3];
	$salaire=$data[$i][18];
	$logement=($data[$i][19] == 1) ? "oui" : "non" ;
	$pays=$data[$i][20];
	$contact_fonction=trim($data[$i][21]);
	$siteweb=$data[$i][22];
	$grphotelier=$data[$i][23];
	$nbetoile=$data[$i][24];
	$nbchambre=$data[$i][25];
	$qualite=$data[$i][26];
	$info="<font class=T1><font color=blue>Date de la demande :</font> ".dateForm($data[$i][1])."&nbsp;&nbsp;" ;
	$info.="<br><font color=blue>Observations : </font>".html_quotes($data[$i][5]);
	$info.="<br><font color=blue>Groupe Hôtelier : </font>".html_quotes($grphotelier);
	$info.="<br><font color=blue>Nombre Etoiles : </font>".html_quotes($nbetoile);
	$info.="<br><font color=blue>Nombre Chambres : </font>".html_quotes($nbchambre);
	$info.="<br><font color=blue>Qualit&eacute; : </font>".html_quotes($qualite);
	$info.="</font>";
	$nom_ent=$data[$i][7];


	$infoEntreprise="Société: $nom_ent      / adresse: $adresse / CCP: $ccp / Ville: $ville / Pays : $pays \n\nTel: $tel /  Fax: $fax  /    Email: $contact ($email) - $contact_fonction\nSexe: $sexe - Logement : $logement - Salaire: $salaire / Groupe Hôtelier: $grphotelier  - Nb Etoiles: $nbetoile - Nb Chambres: $nbchambre";


	$pdf->SetXY($X=3,$Y);
	$pdf->SetFont('Arial','',12);
	$pdf->SetFillColor(220);
	$pdf->MultiCell(292,30,"",1,'L',1);
	$pdf->SetXY($X,$Y+2);
	$pdf->MultiCell(287,4.3,"$infoEntreprise",0,'L',0);
	$pdf->SetFillColor(255);
	$Y+=30;

	$pdf->SetXY($X=3,$Y);
	$pdf->SetFont('Arial','',10);
	$pdf->MultiCell(40,20,"",1,'L',1);
	$pdf->SetXY($X,$Y+1);
	$pdf->MultiCell(40,3,"$nom_ent",0,'L',0);
	$pdf->SetXY($X+=40,$Y);
	$pdf->MultiCell(35,20,'',1,'L',1);
	$pdf->SetXY($X,$Y+1);
	$pdf->MultiCell(35,3,$data[$i][6],0,'L',0);
	$pdf->SetXY($X+=35,$Y);
	$pdf->MultiCell(27,20,'',1,'L',1);
	$pdf->SetXY($X,$Y+1);
	$pdf->MultiCell(27,3,$data[$i][3],0,'L',0);
	$pdf->SetXY($X+=27,$Y);
	$pdf->MultiCell(60,20,'',1,'L',1);
	$pdf->SetXY($X,$Y+1);
	$pdf->MultiCell(60,3,$data[$i][4],0,'L',0);
	$pdf->SetXY($X+=60,$Y);
	$pdf->MultiCell(70,20,'',1,'L',1);
	$pdf->SetXY($X,$Y+1);
	$pdf->MultiCell(70,3,$data[$i][5],0,'L',0);
	$pdf->SetXY($X+=70,$Y);
	$pdf->MultiCell(60,20,'',1,'C',1);
	$ahref="";
	$fahref="";
	if (trim($siteweb) != "") {
		$ahref="<a href='http://$siteweb' title='$siteweb' target='_blank' >";
		$fahref='</a>';
	}
	$infoEntreprise="<font class='T2'>$ahref$nom_ent$fahref  - Contact : ".ucwords($contact)." - Email : $email &nbsp;-&nbsp;$contact_fonction<br>adresse: $adresse  CCP: $ccp   Ville: $ville   Pays: <font color=red>$pays</font>  &nbsp;Tel: $tel  -  Fax: $fax  - <br>Informations : ".stripslashes($infoplus)." &nbsp;Sexe : $sexe / Logement : $logement / Salaire : $salaire</font>";
	print "<tr >";
	print "<td bgcolor='#CCCCCC' colspan=4 valign='top' >&nbsp;".$infoEntreprise."</td>";
	print "</tr>";
	print "<tr  >";
	print "<td align='center' valign='top' >";
	if (PRODUCTID == $productid) {
		print "<a href='gestiondemandesouhaitcentral.php?idstage=$id&periode=$idSouhait&p=$p&productid=$productid' ><img src='image/commun/editer.gif' border='0'></a>&nbsp;&nbsp;";
	}
	print "<a href='#' onMouseOver=\"AffBulle('$info');\" onMouseOut='HideBulle()'><img src='image/commun/affichage.gif' border='0'></a></td>";
	print "<td valign='top' ><font class='T2'>&nbsp;".$data[$i][4]."</font></td>";
	$departement=$data[$i][4];
	print "<td align='center' valign='top' ><font class='T2'>".$data[$i][6]."</font></td>";
	print "<td valign='top'><table border=0>";
	print "<tr><td><i>Nom et prénom de l'étudiant</i></td></tr>";
	for($u=1;$u<=$data[$i][6];$u++) {
		print "<tr><a name='ancre$i$u' /><form method='post' action='gestion_central_stage_visu2.php#ancre$i$u' >";
		print "<td class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		print "<input type='hidden' name='periode' value='$idSouhait' />";
		print "<input type='hidden' name='productid' value='$productid' />";
		print "<input type='hidden' name='p' value='$p' />";
		print "<input type='hidden' name='nb' value='$u' />";
		$dataattribution=rechercheAttribution($u,$id); // attribution,productid,id,confirmer,emailenvoye,idcentralestage
		$attributionText=$dataattribution[0][0];
		$productidattribution=$dataattribution[0][1];
		$idattribution=$dataattribution[0][2];
		$confirmeattribution=$dataattribution[0][3];
		$confirmemailenvoyer=$dataattribution[0][4];
		$idcentralestage=$dataattribution[0][5];
		$viacentrale=$dataattribution[0][6];
		$disabled="";
		if (trim($attributionText) != "") { $attributionTextTotal.=$attributionText."\n"; }
		if (($productidattribution != $productid) && ($attributionText != "")) {
			$disabled="disabled='disabled'";
		}
		print "<input type='hidden' size=4  name='idcentralstagesouhait' value='$id' > ";
		print "<input type='hidden' id='modicentral$i$u'  name='modifcentrale' value='0' > ";
		print "<input type='text' size=30 maxlength='250' name='attribution' id='attribution$i$u' value=\"$attributionText\" $disabled >&nbsp;";
		print "<input type='submit' value='ok' name='attribe' $disabled id='ok$i$u' /> ";
		if (($CENTRAL == 1) && ($disabled != "")) {
			print "&nbsp;<img src='image/commun/editer.gif' title='Editer en tant que Central Stage' valign='top' onclick=\"document.getElementById('attribution$i$u').disabled=false;document.getElementById('ok$i$u').disabled=false;document.getElementById('modicentral$i$u').value='1';\" />&nbsp;";
		}
		if (($CENTRAL == 1) && ($disabled == "")) {
			print "&nbsp;<input type='checkbox'  title='Editer en tant que Central Stage' onclick=\"modifEtatCentral('$i$u',this.checked)\" />&nbsp;";
		}
		if (trim($attributionText) != "") { 
			if ($productidattribution == $productid) {
				print "<input type='submit' name='attribe' style='background:url(image/commun/trash.png) top right no-repeat;padding: 0 0 0 0px ; width:20px; height:17px ; border:0;cursor:pointer;' value='' onclick='this.form.attribution.value=\"\"' >"; 
			}
		}
		// recherche info etablissement en fonction de l'etudiant
		print "</td>";
		$infoetablissement="";
		$disabled1="";
		if ($productidattribution != "") {
			if ($productidattribution == PRODUCTID) {
				$dataE=visu_param();
				// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,$anneeScolaire
				for($y=0;$y<count($dataE);$y++) {
					$nometablissement=trim($dataE[$y][0]);
					$villeetablissement=trim($dataE[$y][3]);
					$paysetablissement=trim($dataE[$y][9]);
				}	
			}else{
				$infoentreprise=infoDemandeAffiliation($productidattribution); 
				//datedemande,nom,email,etablissement,ville,pays,productid,autorise,password
				$nometablissement=$infoentreprise[0][3];
				$villeetablissement=$infoentreprise[0][4];
				$paysetablissement=$infoentreprise[0][5];
			}
			if ($viacentrale == 1) {
				if ($attributionText != "") $infoetablissement="Modifié par DIRECTION CENTRALE DES STAGES";
			}else{
				if (trim($nometablissement) != "") {
					$infoetablissement=" pour : <i>$nometablissement - $villeetablissement $paysetablissement</i>";
				}
			}
		}
		$checked1="";
		if ($confirmeattribution == 1) { 
			$checked1="checked='checked'";
		}
		if ($CENTRAL == 1) { 
			$disabled="";
		}
		print "<td><font class=T2>$infoetablissement</font></td>";
		if (($productidattribution != "") && ($attributionText != "")) { 
			print "<td>&nbsp;(Confirmer:<input $checked1 $disabled type='checkbox' id='div$i$u' onclick=\"valideStageEleve('$productidattribution','$idattribution','$i$u','$idcentralestage',this.checked)\" />&nbsp;oui)</td>";
			if ($confirmeattribution == 1)	{ $style=''; }else{ $style="style='display:none'"; }
			if (trim($disabled) == "") {
				if (aff_valeur_parametrage("mailentrmess") == "") {
					print "<td  id='etA$i$u' $style >(<b>Mail non configur&eacute;</b>)</td>";
				}else{
					// $nom_ent
					// $contact
					// $nometablissement 
					// $villeetablissement 
					// $paysetablissement
					// $periode1 au $periode2
					if ($confirmemailenvoyer != 1) {
						print "<td  id='etA$i$u' $style ><table><tr><td>Config&nbsp;mail&nbsp;:&nbsp;</i>
							<select id='numbermail$i$u'>
							  <option value=''  ></option>
							  <option value='1_' >1</option>
							  <option value='2_' >2</option>
							  <option value='3_' >3</option>
							  <option value='4_' >4</option>
							  <option value='5_' >5</option>
							  </select></td><td>&nbsp;<script>buttonMagicSubmit4(\"Mail Ent.\",'',\"envoiMail('$productidattribution','$idattribution','$i$u','$idcentralestage','$email',document.getElementById('attribution$i$u').value,document.getElementById('numbermail$i$u').options[document.getElementById('numbermail$i$u').selectedIndex].value);\")</script>&nbsp;&nbsp;</td></tr></table>";
						$infocontenu=addslashes($nom_ent)."||".addslashes($contact)."||".addslashes($nometablissement)."||".addslashes($villeetablissement)."||".addslashes($paysetablissement)."||$periode1 au $periode2||$departement";
						print "<input type='hidden' value=\"$infocontenu\"  id='infoA$i$u'/>";
						print "</td>";
					}
				}
			}
			if ($confirmemailenvoyer == 1)	{ $style=''; }else{ $style="style='display:none'"; }
			print "<td $style id='mail$i$u' ><img src='image/commun/stat1.gif' ></td>";
		}
			
		print "</tr>";	
		print "</form>";
	}
	print "</table></td>";
	
	$pdf->SetXY($X,$Y+1);
	$pdf->MultiCell(60,4,"$attributionTextTotal",0,'L',0);
	$attributionTextTotal="";
	$attributionText="";
	$Y+=20;
	print "</tr>";
	print "</td></tr>";
	if ($Y >= 150) { 
		$pdf->AddPage(); 
		$Y=3;
	}
}
$pdf->output('F',$fichier);
$pdf->close();
$url="";
if ($_POST["action"] == 1) {
	$url="?m=1";
}
?>
</tr>
</table>
<br>
<br>  
<?php
Pgclose();
include_once("librairie_php/lib_conexpersistant.php");
connexpersistance("");
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
