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
$anneeScolaire=$_COOKIE["anneeScolaire"];
setcookie("anneeScolaire",$anneeScolaire,time()+3600*24*30);


include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/recupnoteperiode.php");
$cnx=cnx();


if(isset($_POST["create"])) {
	$cgrp=$_POST["sClasseGrp"];
	$sClasseGrp=$_POST["sClasseGrp"];
	$cgrp=explode(":",$cgrp);
	$cid=$cgrp[0];
	$gid=$cgrp[1];
	$mid=$_POST["sMat"];
	$choix_tri=$_POST["choix_trimestre"];
	$typecom=$_POST["typecom"];

}else {
	$cgrp=$_GET["sClasseGrp"];
	$sClasseGrp=$_GET["sClasseGrp"];
	$cgrp=explode(":",$cgrp);
	$cid=$cgrp[0];
	$gid=$cgrp[1];
	$mid=$_GET[sMat];
	$choix_tri=recherche_trimestre_en_cours_via_classe($cid,$anneeScolaire);
	$typecom=$_GET["typecom"];
}


if ($typecom == "") { $typecom="0"; }

$nomClasse=chercheClasse($cid);
$nomClasse=$nomClasse[0][1];
$nomMat=chercheMatiereNom($mid);
$nomGrp=chercheGroupeNom($gid);
$libel=$nomClasse." ".$nomGrp." ".$nomMat;

if ($nomGrp != "") {
	$groupe=1;
}else{
	$groupe=0;
}


// recherche de l'intervalle de date
// creation de la requete
if ($choix_tri != "") {
	$data=recherche_intervalle_trimestre_via_classe($choix_tri,$cid,$anneeScolaire);
	for($i=0;$i<count($data);$i++){
		$date_debut=$data[$i][0];
		$date_fin=$data[$i][1];
		$sql2="date >= '$date_debut' AND date <= '$date_fin' ";
	}

	if ($choix_tri == "trimestre2") $data=recherche_intervalle_trimestre_via_classe("trimestre1",$cid,$anneeScolaire);
	for($i=0;$i<count($data);$i++){
		$date_debutP=$data[$i][0];
		$date_finP=$data[$i][1];
	}


	if ($choix_tri == "trimestre3") $data=recherche_intervalle_trimestre_via_classe("trimestre2",$cid,$anneeScolaire);
	for($i=0;$i<count($data);$i++){
		$date_debutP=$data[$i][0];
		$date_finP=$data[$i][1];
	}
}

// fin de la creation

$listTmp=explode(":",$_GET["sClasseGrp"]);
unset($HPV[cgrp]);
$HPV[cid]=$cid;
$HPV[gid]=$gid;
unset($listTmp);
//print_r($HPV);
if($HPV[gid]):
        $who="<font color=\"#FFFFFF\">- ".LANGPROF4." : </font> ".chercheGroupeNom($HPV[gid]);
else:
        $cl=chercheClasse($HPV[cid]);
        $who="<font color=\"#FFFFFF\">- ".strtolower(LANGELE4)." : </font>".$cl[0][1];
        unset($cl);
endif;

?>
<HTML>
<HEAD>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script language="JavaScript" src="./librairie_js/lib_pulldown.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>

<script type="text/JavaScript">

<?php
include_once("common/productId.php");
include_once("common/config-ia.php");
$productID=PRODUCTID;
$iakey=IAKEY;
?>

verifToken('<php print $productID ?>','<?php print $iakey ?>','afficheToken');


<!--
function GP_AdvOpenWindow(theURL,winName,ft,pw,ph,wa,il,aoT,acT,bl,tr,trT,slT,pu) { //v3.08
  // Copyright(c) George Petrov, www.dmxzone.com member of www.DynamicZones.com
  var rph=ph,rpw=pw,nlp,ntp,lp=0,tp=0,acH,otH,slH,w=480,h=340,d=document,OP=(navigator.userAgent.indexOf("Opera")!=-1),IE=d.all&&!OP,IE5=IE&&window.print,NS4=d.layers,NS6=d.getElementById&&!IE&&!OP,NS7=NS6&&(navigator.userAgent.indexOf("Netscape/7")!=-1),b4p=IE||NS4||NS6||OP,bdyn=IE||NS4||NS6,olf="",sRes="";
  imgs=theURL.split('|'),isSL=imgs.length>1;aoT=aoT&&aoT!=""?true:false;
  var tSWF='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" ##size##><param name=movie value="##file##"><param name=quality value=high><embed src="##file##" quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" ##size##></embed></object>'
  var tQT='<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" ##size##><param name="src" value="##file##"><param name="autoplay" value="true"><param name="controller" value="true"><embed src="##file##" ##size## autoplay="true" controller="true" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>'
  var tIMG=(!IE?'<a href="javascript:'+(isSL?'nImg()':'window.close()')+'">':'')+'<img id=oImg name=oImg '+((NS4||NS6||NS7)?'onload="if(isImg){nW=pImg.width;nH=pImg.height}window.onload();" ':'')+'src="##file##" border="0" '+(IE?(isSL?'onClick="nImg()"':'onClick="window.close()"'):'')+(IE&&isSL?' style="cursor:pointer"':'')+(!NS4&&isSL?' onload="show(\\\'##file##\\\',true)"':'')+'>'+(!IE?'</a>':'')
  var tMPG='<OBJECT classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,0,02,902" ##size## type="application/x-oleobject"><PARAM NAME="FileName" VALUE="##file##"><PARAM NAME="animationatStart" VALUE="true"><PARAM NAME="transparentatStart" VALUE="true"><PARAM NAME="autoStart" VALUE="true"><PARAM NAME="showControls" VALUE="true"><EMBED type="application/x-mplayer2" pluginspage = "http://www.microsoft.com/Windows/MediaPlayer/" SRC="##file##" ##size## AutoStart=true></EMBED></OBJECT>'
  omw=aoT&&IE5;bl=bl&&bl!=""?true:false;tr=IE&&tr&&isSL?tr:0;trT=trT?trT:1;ph=ph>0?ph:100;pw=pw>0?pw:100;
  re=/\.(swf)/i;isSwf=re.test(theURL);re=/\.(gif|jpg|png|bmp|jpeg)/i;isImg=re.test(theURL);re=/\.(avi|mov|rm|rma|wav|asf|asx|mpg|mpeg)/i;isMov=re.test(theURL);isEmb=isImg||isMov||isSwf;
  if(isImg&&NS4)ft=ft.replace(/resizable=no/i,'resizable=yes');if(b4p){w=screen.availWidth;h=screen.availHeight;}
  if(wa&&wa!=""){if(wa.indexOf("center")!=-1){tp=(h-ph)/2;lp=(w-pw)/2;ntp='('+h+'-nWh)/2';nlp='('+w+'-nWw)/2'}if(wa.indexOf("bottom")!=-1){tp=h-ph;ntp=h+'-nWh'} if(wa.indexOf("right")!=-1){lp=w-pw;nlp=w+'-nWw'}
    if(wa.indexOf("left")!=-1){lp=0;nlp=0} if(wa.indexOf("top")!=-1){tp=0;ntp=0}if(wa.indexOf("fitscreen")!=-1){lp=0;tp=0;ntp=0;nlp=0;pw=w;ph=h}
    ft+=(ft.length>0?',':'')+'width='+pw;ft+=(ft.length>0?',':'')+'height='+ph;ft+=(ft.length>0?',':'')+'top='+tp+',left='+lp;
  } if(IE&&bl&&ft.indexOf("fullscreen")!=-1&&!aoT)ft+=",fullscreen=1";
  if(omw){ft='center:no;'+ft.replace(/lbars=/i,'l=').replace(/(top|width|left|height)=(\d+)/gi,'dialog$1=$2px').replace(/=/gi,':').replace(/,/gi,';')}
  if (window["pWin"]==null) window["pWin"]= new Array();var wp=pWin.length;pWin[wp]=(omw)?window.showModelessDialog(imgs[0],window,ft):window.open('',winName,ft);
  if(pWin[wp].opener==null)pWin[wp].opener=self;window.focus();
  if(b4p){ if(bl||wa.indexOf("fitscreen")!=-1){pWin[wp].resizeTo(pw,ph);pWin[wp].moveTo(lp,tp);}
    if(aoT&&!IE5){otH=pWin[wp].setInterval("window.focus();",50);olf='window.setInterval("window.focus();",50);'}
  } sRes='\nvar nWw,nWh,d=document,w=window;'+(bdyn?';dw=parseInt(nW);dh=parseInt(nH);':'if(d.images.length == 1){var di=d.images[0];dw=di.width;dh=di.height;\n')+
    'if(dw>0&&dh>0){nWw=dw+'+(IE?12:NS7?15:NS6?14:0)+';nWh=dh+'+(IE?32:NS7?50:NS6?1:0)+';'+(OP?'w.resizeTo(nWw,nWh);w.moveTo('+nlp+','+ntp+')':(NS4||NS6?'w.innerWidth=nWw;w.innerHeight=nWh;'+(NS6?'w.outerWidth-=14;':''):(!omw?'w.resizeTo(nWw,nWh)':'w.dialogWidth=nWw+"px";w.dialogHeight=nWh+"px"')+';eh=dh-d.body.clientHeight;ew=dw-d.body.clientWidth;if(eh!=0||ew!=0)\n'+
  	(!omw?'w.resizeTo(nWw+ew,nWh+eh);':'{\nw.dialogWidth=(nWw+ew)+"px";\nw.dialogHeight=(nWh+eh)+"px"}'))+(!omw?'w.moveTo('+nlp+','+ntp+')'+(!(bdyn)?'}':''):'\nw.dialogLeft='+nlp+'+"px";w.dialogTop='+ntp+'+"px"\n'))+'}';
  var iwh="",dwh="",sscr="",sChgImg="";tRep=".replace(/##file##/gi,cf).replace(/##size##/gi,(nW>0&&nH>0?'width=\\''+nW+'\\' height=\\''+nH+'\\'':''))";
  var chkType='re=/\\.(swf)$/i;isSwf=re.test(cf);re=/\\.(mov)$/i;isQT=re.test(cf);re=/\\.(gif|jpg|png|bmp|jpeg)$/i;isImg=re.test(cf);re=/\.(avi|rm|rma|wav|asf|asx|mpg|mpeg)/i;isMov=re.test(cf);';
  var sSize='tSWF=\''+tSWF+'\';\ntQT=\''+tQT+'\';tIMG=\''+tIMG+'\';tMPG=\''+tMPG+'\'\n'+"if (cf.substr(cf.length-1,1)==']'){var bd=cf.lastIndexOf('[');if(bd>0){var di=cf.substring(bd+1,cf.length-1);var da=di.split('x');nW=da[0];nH=da[1];cf=cf.substring(0,bd)}}"+chkType;
  if(isEmb){if(isSL) { 
      sChgImg=(NS4?'var l = document.layers[\'slide\'];ld=l.document;ld.open();ld.write(nHtml);ld.close();':IE?'document.all[\'slide\'].innerHTML = nHtml;':NS6?'var l=document.getElementById(\'slide\');while (l.hasChildNodes()) l.removeChild(l.lastChild);var range=document.createRange();range.setStartAfter(l);var docFrag=range.createContextualFragment(nHtml);l.appendChild(docFrag);':'');
      sscr='var pImg=new Image(),slH,ci=0,simg="'+theURL+'".split("|");'+
      'function show(cf,same){if(same){di=document.images[0];nW=di.width;nH=di.height}'+sRes+'}\n'+
      'function nImg(){if(slH)window.clearInterval(slH);nW=0;nH=0;cf=simg[ci];'+sSize+'document.title=cf;'+
      (tr!=0?';var fi=IElem.filters[0];fi.Apply();IElem.style.visibility="visible";fi.transition='+(tr-1)+';fi.Play();':'')+
      'if (nW==0&&nH==0){if(isImg){nW=pImg.width;nH=pImg.height}else{nW='+pw+';nH='+ph+'}}'+
      (bdyn?'nHtml=(isSwf?tSWF'+tRep+':isQT?tQT'+tRep+':isImg?tIMG'+tRep+':isMov?tMPG'+tRep+':\'\');'+sChgImg+';':'if(document.images)document["oImg"].src=simg[ci];')+
      sRes+';ci=ci==simg.length-1?0:ci+1;cf=simg[ci];re=/\\.(gif|jpg|png|bmp|jpeg)$/i;isImg=re.test(cf);if(isImg)pImg.src=cf;'+
      (isSL?(!NS4?'if(ci>1)':'')+'slH=window.setTimeout("nImg()",'+slT*1000+')}':'');
    } else {sscr='var re,pImg=new Image(),nW=0,nH=0,nHtml="",cf="'+theURL+'";'+chkType+'if(isImg)pImg.src=cf;\n'+
      'function show(){'+sSize+';if (nW==0&&nH==0){if(isImg){;nW=pImg.width;nH=pImg.height;if (nW==0&&nH==0){nW='+pw+';nH='+ph+'}}else{nW='+pw+';nH='+ph+
      '}};nHtml=(isSwf?tSWF'+tRep+':isQT?tQT'+tRep+':isImg?tIMG'+tRep+':isMov?tMPG'+tRep+':\'\');document.write(nHtml)};'}
    pd = pWin[wp].document;pd.open();pd.write('<html><'+'head><title>'+imgs[0]+'</title><'+'script'+'>'+sscr+'</'+'script>'+(!NS4?'<STYLE TYPE="text/css">BODY {margin:0;border:none;padding:0;}</STYLE>':'')+'</head><body '+(NS4&&isSL?'onresize=\'ci--;nImg()\' ':'')+'onload=\''+olf+(isSL?';nImg()':sRes)+'\' bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginheight="0" marginwidth="0">'); 
    if(rpw>0){iwh='width="'+rpw+'" ';dwh='width:'+rpw} if(rph>0){iwh+='height="'+rph+'"';dwh+='height:'+rph}
    if(tr!=0) pd.write('<span id=IElem Style="Visibility:hidden;Filter:revealTrans(duration='+trT+');width:100%;height=100%">');
    if(isSL&&bdyn) {pd.write(NS4?'<layer id=slide></layer>':'<span id=slide></span>')} else {pd.write('<'+'script>show()'+'</'+'script>')}   
    if(tr!=0) pd.write('</span>');pd.write('</body></html>');pd.close();
  }else {if(!omw)pWin[wp].location.href=imgs[0];}
  if((acT&&acT>0)||(slT&&slT>0&&isSL)){if(pWin[wp].document.body)pWin[wp].document.body.onunload=function(){if(acH)window.clearInterval(acH);if(slH)window.clearInterval(slH)}}
  if(acT&&acT>0)acH=window.setTimeout("pWin["+wp+"].close()",acT*1000);if(slT&&slT>0&&isSL)slH=window.setTimeout("if(pWin["+wp+"].nImg)pWin["+wp+"].nImg()",slT*1000);  
  if(pu&&pu!=""){pWin[wp].blur();window.focus()} else pWin[wp].focus();document.MM_returnValue=(il&&il!="")?false:true;
}
//-->
</script>

</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="setTimeout('timer("formulaire")',100)" >
<?php include("./librairie_php/lib_licence.php"); ?>
<br>
<?php
if ($typecom == "0") { $titrecom=LANGMESS119; }
if ($typecom == "1") { $titrecom=LANGMESS120; }
if ($typecom == "2") { $titrecom=LANGMESS121; }
if ($typecom == "3") { $titrecom=LANGMESS122; }
print "<center><font class=T2><b>$titrecom / ".LANGBULL3." : $anneeScolaire </b></font></center>";
?>
<ul>
<table border=0>
<tr><td>
<form method='post' >
<?php print LANGPROF5 ?> :
<?php
$choix_tri_text=$choix_tri;
//$tri=recherche_intervalle_trimestre($choix_tri_text);

if ($choix_tri_text == "trimestre1") {
	$choix_tri_text=LANGPROJ3. " ou ".LANGPROJ19;
}
if ($choix_tri_text == "trimestre2") {
    $choix_tri_text=LANGPROJ4. " ou ".LANGPROJ20;
}
if ($choix_tri_text == "trimestre3") {
    $choix_tri_text=LANGPROJ5;
}

$dateDebut=$date_debut;
$dateFin=$date_fin;
$dateDebutP=$date_debutP;
$dateFinP=$date_finP;
$idgroupe=$gid;
$idMatiere=$mid;
$idclasse=$cid;

if ($typecom == 4) {
	if ($groupe == 1) {
		$moyClass=moyeMatGenGroupeExamen($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe,$_SESSION["id_pers"],"Partiel Blanc");
                $notetype=recherchetypenotegroupe($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe);
        }else {
		$moyClass=moyeMatGenExamen($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse,$_SESSION["id_pers"],"Partiel Blanc");
                $notetype=recherchetypenote($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse);
        }
}else{
	if ($groupe == 1) {
		$moyClass=moyeMatGenGroupe($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe,$_SESSION["id_pers"]);
		$notetype=recherchetypenotegroupe($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe);
	}else {
		$moyClass=moyeMatGen($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse,$_SESSION["id_pers"]);
		$notetype=recherchetypenote($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse);
	}
}
if ($notetype == "en") {
	$moyClass=recherche_note_en($moyClass);
}
?>

<select name="choix_trimestre">
	<option value='<?php print $choix_tri?>' STYLE="color:#000066;background-color:#FCE4BA"><?php print ucfirst($choix_tri_text)?></option>
	<option value='trimestre1' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3. " ou ".LANGPROJ19?></option>
	<option value='trimestre2' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4. " ou ".LANGPROJ20?></option>
	<option value='trimestre3' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?></option>
</select>
<input type=hidden name="sMat" value='<?php print $_GET["sMat"];?>'>
<input type=hidden name="sClasseGrp" value='<?php print $_GET["sClasseGrp"];?>'>
<input type='hidden' name="typecom" value="<?php print $typecom ?>" >
</td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGOK ?>","create"); </script>
<?php
$fichier="./data/pdf_bull/edition_".$_SESSION["id_pers"].".pdf";
?>
&nbsp;&nbsp;&nbsp;
<a href="visu_pdf_prof.php?id=<?php print $fichier?>" target="_blank"><img src="image/commun/print.gif" border=0 align=center></a>

</td></tr></table>
</form>
<?php if ($choix_tri == "") { ?>
	<br><center><font class="T2" id='color2'><b>INDIQUER UN TRIMESTRE OU SEMESTRE !!</b></font></center>
<?php } ?>
<br>
<script>buttonMagic2("<?php print LANGMESS118 ?>",'bulletincomprof22.php?sClasseGrp=<?php print $sClasseGrp ?>&sMat=<?php print $mid ?>&typecom=<?php print $typecom ?>','bulletincom','width=800,height=600,resizable=yes,scrollbars=yes','')</script>
<br>
</ul>
<br>
<?php
// creation PDF
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$xcoor=20;
$ycoor=5;
$nomclasse=chercheClasse_nom($cid);
$texte="Appréciation pour le bulletin trimestriel classe : $nomclasse ";
$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor,$ycoor);
$pdf->Write(5,$texte);
?>

&nbsp;&nbsp;Du <b><?php print dateForm($dateDebut) ?></b> au <b><?php print dateForm($dateFin) ?></b>
/ <?php print LANGMESS123 ?> : <font color=blue><b><?php print $moyClass ?></b></font>
<br /><br />
<?php 
if ( (defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) { 
?>
<table><tr><td>Information Scolaire Complémentaire :</td><td><script language=JavaScript>buttonMagic("cliquez-ici","profpprojo.php?fiche=1&idClasse=<?php print $cid?>","video","width=800,height=700,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes","");</script>
</td></tr></table>
<?php } ?>


<?php
$ycoor+=5;
$pdf->SetFont('Arial','',11);
$pdf->SetXY($xcoor,$ycoor);
$pdf->Write(5,"Du ".dateForm($dateDebut)." au ".dateForm($dateFin)."  -  ".ucfirst($choix_tri_text)." / Année Scolaire : ".$anneeScolaire);
$pdf->SetXY($xcoor,$ycoor+5);
$nomMatiere=chercheMatiereNom($idMatiere);
$pdf->Write(5,LANGPER17." : $nomMatiere");
$pdf->SetXY($xcoor,$ycoor+10);
$pdf->SetTextColor(0,0,255);  // blue
$pdf->WriteHTML(LANGMESS123." : <b>$moyClass</b>");
$pdf->SetTextColor(0,0,0);  // noire
$ycoor+=15;
?>

<script language=Javascript>
var deja=0;
function envoi() {
	document.formulaire.valide.disabled=true;
	return true;
}
</script>
<form method='post' name='formulaire' action="bulletincomprof4.php"  onsubmit="return envoi();">


<script language=Javascript>
<?php
	print "var tab=new Array();\n";

	$data=liste_com_bulletin($_SESSION["id_pers"]);
	for($i=0;$i<count($data);$i++) {
		$text=$data[$i][1];
		$nb=$data[$i][0];
		$text=preg_replace("/\r\n/"," ",$text);
		$text=preg_replace("/\"/","\\\"",$text);
		print "tab[$nb]=\"".$text."\";\n";
	}
		
?>
</script>
<table  border='0' bordercolor="#000000" width='100%' >
<?php
if($HPV[gid]){
        $gid=$HPV[gid];
        $sqlIn=<<<SQL
        SELECT
        	liste_elev
        FROM
        	${prefixe}groupes
        WHERE
        	group_id='$gid'
SQL;
    $curs=execSql($sqlIn);
    $in=chargeMat($curs);
    freeResult($curs);
    $in=$in[0][0];
	$in=substr($in,1);
	$in=substr($in,0,-1);
	$sql="
        SELECT
        	elev_id,
	";
        if(DBTYPE=='pgsql')
	{
		$sql .= " upper(trim(nom))||' '||initcap(trim(prenom)) ";
	}
	elseif(DBTYPE=='mysql')
	{
		$sql .= " CONCAT( UPPER(TRIM(nom)) , ' ' , TRIM(prenom) ) ";
	}
	$sql .= "
        FROM
        	${prefixe}eleves
        WHERE
        	elev_id IN ($in)
		AND annee_scolaire='$anneeScolaire'
        ORDER BY
        	2
	";
		unset($in);
} else {
        $cid=$HPV[cid];
	$sql="
        SELECT
        	elev_id,
	";
	if(DBTYPE=='pgsql')
	{
        	$sql .= " upper(trim(nom))||' '||initcap(trim(prenom)) ";
        }
	elseif(DBTYPE=='mysql')
	{
        	$sql .= " CONCAT( UPPER(TRIM(nom)) , ' ' , TRIM(prenom) ) ";
	}
	$sql .= "
	FROM
        	${prefixe}eleves
        WHERE
        	classe='$cid'
		AND annee_scolaire='$anneeScolaire'
        ORDER BY
        	2
	";
        unset($cid);
}
        $curs=execSql($sql);
        unset($sql);
        $mat=chargeMat($curs);
        freeResult($curs);
        unset($curs);
	
	   for($i=0;$i<count($mat);$i++){

				if ($ii == 24) {
	                		$pdf->AddPage();
					$ii=0;
					$xcoor=20;
					$ycoor=20;
				}
				$ii++;  


				$idEleve=$mat[$i][0];
				
				$photoeleve="image_trombi.php?idE=".$mat[$i][0];


				if ($typecom == 4) {
		                        if ($idgroupe == "0") {
                		                $noteaff=moyenneEleveMatiereExamen($idEleve,$idMatiere,dateForm($dateDebut),dateForm($dateFin),$_SESSION["id_pers"],"Partiel Blanc");
                                		$notetype=recherchetypenote($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idClasse);
		                        }else{
                		                $noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe,$_SESSION["id_pers"],"Partiel Blanc");
                                		$notetype=recherchetypenotegroupe($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe);
		                        }
                		}else{
					if ($groupe == 1) {
						$moyEleve=moyenneEleveMatiereGroupe($idEleve,$idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe,$_SESSION["id_pers"]);
						$moyElevePrecedent=moyenneEleveMatiereGroupe($idEleve,$idMatiere,dateForm($dateDebutP),dateForm($dateFinP),$idgroupe,$_SESSION["id_pers"]);
						$notetype=recherchetypenotegroupe($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe);
					}else {
						$moyEleve=moyenneEleveMatiere($idEleve,$idMatiere,dateForm($dateDebut),dateForm($dateFin),$_SESSION["id_pers"]);
						$moyElevePrecedent=moyenneEleveMatiere($idEleve,$idMatiere,dateForm($dateDebutP),dateForm($dateFinP),$_SESSION["id_pers"]);
						$notetype=recherchetypenote($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse);
					}
				}
				
				if ($notetype == "en") { 
					$moyEleve=recherche_note_en($moyEleve); 
					$color="blue";
				}else{

					if ($moyEleve < 10) {
						$color="red";
						$rb1=255;$rb2=0;$rb3=0;	
					}else{
						$rb1=0;$rb2=0;$rb3=0;
						$color="orange";
					}
				}
				$commentaireeleve="";
				$commentaireeleve=cherche_com_eleve2($idEleve,$idMatiere,$idclasse,$choix_tri,$_SESSION["id_pers"],$idgroupe,$typecom);

				print "<tr>\n";
				print "<td id='bordure' valign=top title=\"".$mat[$i][1]."\" >&nbsp;<b>".trunchaine($mat[$i][1],15)."</b><br><img src='$photoeleve' >";?>
				<?php $commentaireeleve = addslashes($commentaireeleve); ?>
				<?php
				print "<br> &nbsp<u>".LANGMESS82."</u> : <font color='$color'><b>$moyEleve</b></font>";
						
				print "</td>\n";
			?>
			<?php $commentaireeleve = stripslashes($commentaireeleve); 
			if (defined("NBCARBULL")) { $nbcar=NBCARBULL; }else{ $nbcar=400; }
			if ($typecom > 0) { $nbcar=400; }
			?>
			<td id='bordure'>
			<?php 
			if (trim($commentaireeleve) != "") { ?>
			<button onMouseOver="AffBulle('<u><strong> <?php print LANGMESS125 ?> :</strong> </u><br> 1º: <?php print LANGMESS126 ?>. <br> 2º: <?php print LANGMESS127 ?> +.')" onMouseOut="HideBulle()" onClick="GP_AdvOpenWindow('bulletin_com_bdd_ajout.php?com=<?php print addslashes(urlencode($commentaireeleve)) ?>','<?php print LANGMESS128 ?>','fullscreen=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,channelmode=no,directories=no',200,100,'center','ignoreLink','alwaysOnTop',3,'',0,1,5,'');return document.MM_returnValue" title="Sauvegarder ce commentaire" >+</button>&nbsp;
			<?php } ?>
			<select onChange="motifbulletin('<?php print $i?>',this.value)">
			<option value=0>.........................................</option>
			<?php select_com_bulletin($_SESSION["id_pers"],35); ?>
			</select>
			<input type='text' name='CharRestant_<?php print $i?>' size='2' disabled='disabled'>

			
			<?php
			if(file_exists("./common/config-ia.php")) {
				include_once("common/productId.php");
				include_once("common/config-ia.php");
				$productID=PRODUCTID;
				$iakey=IAKEY;
				$prenom=recherche_eleve_prenom($idEleve);
				$lienIA="ajaxIABulletinCom(document.getElementById('saisie_text_$i').value,'$moyEleve','$productID','$iakey','saisie_text_$i','$prenom',document.getElementById('tonia_$i').value)";
			}else{
				$lienIA="alert('Votre Triade n\'est pas configur&eacute; pour utiliser l\'IA. Contacter votre administrateur Triade')";
			}
			?>
			<input type='button' value='TRIADE-COPILOT' class='BUTTON' onClick="<?php print $lienIA ?>" >
			<select name='tonia' id='tonia_<?php print $i?>' >
				<option value='IA' STYLE="color:#000066;background-color:#FCE4BA" >Comportement IA : Par défaut</option>
				<option value='Neutre' STYLE='color:#000066;background-color:#CCCCFF' >Neutre</option>
				<option value='Positif' STYLE='color:#000066;background-color:#CCCCFF' >Positif</option>
				<option value='Encourageant' STYLE='color:#000066;background-color:#CCCCFF' >Encourageant</option>
				<option value='Inquiétant' STYLE='color:#000066;background-color:#CCCCFF'  >Inquiétant</option>
				<option value='Motivant' STYLE='color:#000066;background-color:#CCCCFF' >Motivant</option>
			</select>
			<span id="afficheToken" style="position:relative;top:-70px;left:-40px"  /></span>


			<br>
			<input type=hidden name="saisie_eleve_<?php print $i?>" value="<?php print $idEleve?>" >
			<textarea onkeypress="compter(this,'<?php print $nbcar ?>', this.form.CharRestant_<?php print $i?>)" cols="78" rows="5" name="saisie_text_<?php print $i?>" id="saisie_text_<?php print $i?>"  ><?php print $commentaireeleve?></textarea><br>
<?php
			$choix_triprecedent="";
			if ($choix_tri == "trimestre2") { $choix_triprecedent="trimestre1"; }
			if ($choix_tri == "semestre2") { $choix_triprecedent="semestre1"; }
			if ($choix_tri == "trimestre3") { $choix_triprecedent="trimestre2"; }
			if ($choix_triprecedent != "") { 
			?>
				<?php print LANGMESS124 ?> : [<a href="#" onclick="slidedown_showHide('box<?php print $i?>');return false;"><?php print LANGMESS129 ?></a>]
<div  style="background-color:#CCCCCC;padding:0px;margin-top:0px"  >
	<div id="dhtmlgoodies_control" style="background-color:#CCCCCC;padding:0px;margin-top:0px" ></div>
	<div style="width:300px;background-color:#CCCCCC;padding:0px;margin-top:0px" class="dhtmlgoodies_contentBox" id="box<?php print $i?>" >
		<div class="dhtmlgoodies_content" id="subBox<?php print $i ?>" style="background-color:#CCCCCC;padding:0px;margin-top:0px" >
		<font class=T1>
<?php 
		$commentaireprecedent="";
		$commentaireprecedent=cherche_com_eleve2($idEleve,$idMatiere,$idclasse,$choix_triprecedent,$_SESSION["id_pers"],$idgroupe,$typecom);
		print LANGMESS130." :<b>$moyElevePrecedent</b><br>$commentaireprecedent";
		$moyElevePrecedent="";
?>
<br /><br />	
	</font>
		</div>
	</div>
</div>
<?php } ?>
			</td>
<?php

		$pdf->SetFont('Arial','',9);
	
		$pdf->SetXY($xcoor,$ycoor); // placement du cadre du nom de l eleve
		$nomprenom=trunchaine($mat[$i][1],30);
		$hauteur=16;

		$pdf->MultiCell(60,$hauteur,"$nomprenom",'1','','',0);
		
		$pdf->SetXY($xcoor+60,$ycoor);
		$pdf->SetFont('Arial','',8);
		$pdf->SetTextColor($rb1,$rb2,$rb3); 
		$pdf->MultiCell(10,$hauteur,"$moyEleve",'1','','',0);
		$pdf->SetTextColor(0,0,0);  // noire

		$pdf->SetXY($xcoor+70,$ycoor);
		$pdf->SetFont('Arial','',7);
		$pdf->MultiCell(80,$hauteur,"",'1','','',0);
		$pdf->SetXY($xcoor+70,$ycoor+0.5);

		$commentaireeleve=trunchaine($commentaireeleve,405);
		$pdf->MultiCell(80,3,"$commentaireeleve",'0','','L',0);
	 
		$xcoor=20;
		$ycoor+=$hauteur;
		if ($ycoor >= 240) { $ycoor=10; $pdf->AddPage(); }

		print "</tr><tr><td colspan=2 id='bordure'><hr></td></tr>\n";
	   }

@unlink($fichier);
$pdf->output('F',$fichier);
?>
<!----------------------------------------------------->
</table><br>
<input type='hidden' name='nb' value="<?php print count($mat) ?>" >
<input type='hidden' name="saisie_classe" value="<?php print $idclasse ?>" >
<input type='hidden' name="saisie_matiere" value="<?php print $idMatiere ?>" >
<input type='hidden' name="choix_trimestre" value="<?php print $choix_tri ?>" >
<input type='hidden' name="saisie_groupe" value="<?php print $idgroupe ?>" >
<input type='hidden' name="typecom" value="<?php print $typecom ?>" >
<input type='hidden' name="anneeScolaire" value="<?php print $anneeScolaire ?>" >
&nbsp;&nbsp;<?php print LANGCOM ?> <input type=checkbox name="bibli" value="oui" /><br /><br />
<?php if ($choix_tri != "") { ?>
<script language=JavaScript>buttonMagicSubmit2("<?php print LANGMESS131 ?>","valide","<?php print LANGMESS132 ?>"); //text,nomInput</script>
<?php }else{ ?>
	<font class="T2" id='color2'><b>Aucun choix de trimestre/semestre indiqué !!</b></font>
<?php } ?>
</form>
<br /><br /><br />
<?php
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
?>
<br />


<?php Pgclose() ?>
</BODY>
</html>
