<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
$anneeScolaire=$_COOKIE["anneeScolaire"];
setcookie("anneeScolaire",$anneeScolaire,time()+3600*24*30);

include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
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
} else {
	$cgrp=$_GET["sClasseGrp"];
	$sClasseGrp=$_GET["sClasseGrp"];
	$cgrp=explode(":",$cgrp);
	$cid=$cgrp[0];
	$gid=$cgrp[1];
	$mid=$_GET['sMat'];
	$choix_tri=recherche_trimestre_en_cours_via_classe($cid,$anneeScolaire);
	$typecom=$_GET["typecom"];
}

if ($typecom == "") { $typecom="0"; }

$nomClasse=chercheClasse($cid);
$nomClasse=$nomClasse[0][1];
$nomMat=chercheMatiereNom($mid);
$nomGrp=chercheGroupeNom($gid);
$libel=$nomClasse." ".$nomGrp." ".$nomMat;

if ($nomGrp != "") { $groupe=1; } else { $groupe=0; }

if ($choix_tri != "") {
	$data=recherche_intervalle_trimestre_via_classe($choix_tri,$cid,$anneeScolaire);
	for($i=0;$i<countTriade($data);$i++){
		$date_debut=$data[$i][0];
		$date_fin=$data[$i][1];
		$sql2="date >= '$date_debut' AND date <= '$date_fin' ";
	}
	if ($choix_tri == "trimestre2") $data=recherche_intervalle_trimestre_via_classe("trimestre1",$cid,$anneeScolaire);
	for($i=0;$i<countTriade($data);$i++){
		$date_debutP=$data[$i][0];
		$date_finP=$data[$i][1];
	}
	if ($choix_tri == "trimestre3") $data=recherche_intervalle_trimestre_via_classe("trimestre2",$cid,$anneeScolaire);
	for($i=0;$i<countTriade($data);$i++){
		$date_debutP=$data[$i][0];
		$date_finP=$data[$i][1];
	}
}

$listTmp=explode(":",$_GET["sClasseGrp"]);
unset($HPV['cgrp']);
$HPV['cid']=$cid;
$HPV['gid']=$gid;
unset($listTmp);
if($HPV['gid']):
	$who="<font color=\"#FFFFFF\">- ".LANGPROF4." : </font> ".chercheGroupeNom($HPV['gid']);
else:
	$cl=chercheClasse($HPV['cid']);
	include_once('./librairie_php/langue.php');
	$who="<font color=\"#FFFFFF\">- ".strtolower(LANGELE4)." : </font>".$cl[0][1];
	unset($cl);
endif;
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script>
function slidedown_showHide(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
}
</script>
<title>Triade — Appréciations bulletin</title>
<style>
/* ── En-tête info ────────────────────────────────────────────────────────── */
.bcp-meta{display:flex;gap:12px;flex-wrap:wrap;align-items:center;padding:6px 0;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;color:#555}
.bcp-meta strong{color:#080A66}
.bcp-badge-moy{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;font-family:Electrolize,'Trebuchet MS',Arial}
.bcp-badge-moy.ok {background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7}
.bcp-badge-moy.bad{background:#fce4e4;color:#c62828;border:1px solid #ef9a9a}

/* ── Carte élève ─────────────────────────────────────────────────────────── */
.bcp-card{display:flex;gap:10px;align-items:flex-start;background:#fff;border:1px solid #dde0f0;border-radius:8px;padding:10px;margin-bottom:8px}
.bcp-left{display:flex;flex-direction:column;align-items:center;gap:5px;min-width:90px;max-width:90px}
.bcp-photo{width:70px;height:80px;object-fit:cover;border-radius:5px;border:2px solid #c5cae9}
.bcp-name{font-size:10px;font-weight:700;color:#080A66;font-family:Electrolize,'Trebuchet MS',Arial;text-align:center;word-break:break-word}
.bcp-right{flex:1;min-width:0}

/* ── Outils commentaire ─────────────────────────────────────────────────── */
.bcp-tools{display:flex;gap:6px;flex-wrap:wrap;align-items:center;margin-bottom:6px}
.bcp-select{padding:3px 6px;border:1px solid #c8cfe8;border-radius:4px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;background:#f5f7ff;color:#333;max-width:220px}
.bcp-counter{width:32px;padding:2px 4px;border:1px solid #c8cfe8;border-radius:4px;font-size:10px;font-family:Electrolize;text-align:center;background:#f5f7ff;color:#555}
.bcp-btn-ia{padding:3px 10px;border:1px solid #080A66;border-radius:4px;background:#080A66;color:#fff;font-size:10px;font-weight:700;font-family:Electrolize,'Trebuchet MS',Arial;cursor:pointer;white-space:nowrap}
.bcp-btn-ia:hover{background:#1a237e}
.bcp-btn-save{padding:3px 8px;border:1px solid #c5cae9;border-radius:4px;background:#e8eaf6;color:#080A66;font-size:10px;font-weight:700;font-family:Electrolize;cursor:pointer}
.bcp-textarea{width:100%;box-sizing:border-box;padding:6px 8px;border:1px solid #c8cfe8;border-radius:5px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;resize:vertical;min-height:80px;background:#fafbff;color:#222}
.bcp-textarea:focus{outline:none;border-color:#080A66;background:#fff}

/* ── Précédent ───────────────────────────────────────────────────────────── */
.bcp-prev-toggle{font-size:10px;color:#080A66;cursor:pointer;text-decoration:underline;font-family:Electrolize}
.bcp-prev-box{background:#f5f7ff;border-left:3px solid #c5cae9;border-radius:4px;padding:6px 10px;margin-top:4px;font-size:10px;font-family:Electrolize,'Trebuchet MS',Arial;color:#555}

/* ── Info scol compl ─────────────────────────────────────────────────────── */
.bcp-info-scol{display:flex;align-items:center;gap:8px;padding:6px 10px;background:#e8eaf6;border-radius:6px;margin-bottom:10px;font-size:11px;font-family:Electrolize}

/* ── Séparateur ─────────────────────────────────────────────────────────── */
.bcp-sep{border:none;border-top:1px solid #eef0f8;margin:4px 0 8px}
</style>
<script type="text/JavaScript">
<?php
include_once("common/productId.php");
$iakey="";
if (file_exists("common/config-ia.php")) {
	include_once("common/config-ia.php");
	$iakey=IAKEY;
}
$productID=PRODUCTID;
?>
verifToken('<?php print $productID ?>','<?php print $iakey ?>','afficheToken');

<!--
function GP_AdvOpenWindow(theURL,winName,ft,pw,ph,wa,il,aoT,acT,bl,tr,trT,slT,pu) { //v3.08
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
  } else {if(!omw)pWin[wp].location.href=imgs[0];}
  if((acT&&acT>0)||(slT&&slT>0&&isSL)){if(pWin[wp].document.body)pWin[wp].document.body.onunload=function(){if(acH)window.clearInterval(acH);if(slH)window.clearInterval(slH)}}
  if(acT&&acT>0)acH=window.setTimeout("pWin["+wp+"].close()",acT*1000);if(slT&&slT>0&&isSL)slH=window.setTimeout("if(pWin["+wp+"].nImg)pWin["+wp+"].nImg()",slT*1000);
  if(pu&&pu!=""){pWin[wp].blur();window.focus()} else pWin[wp].focus();document.MM_returnValue=(il&&il!="")?false:true;
}
//-->
</script>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" style="background:#CACCEF" onload="setTimeout('timer(&quot;formulaire&quot;)',100)">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
if ($typecom == "0") { $titrecom=LANGMESS119; }
if ($typecom == "1") { $titrecom=LANGMESS120; }
if ($typecom == "2") { $titrecom=LANGMESS121; }
if ($typecom == "3") { $titrecom=LANGMESS122; }
?>

<div style="padding:10px">

<!-- ── Filtre période ────────────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:10px">
<div class="card-body" style="padding:8px 12px">
<form method="post">
  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">

    <div class="form-row" style="min-width:180px;flex:1">
      <label class="form-label">
        <i class="bi bi-calendar3" style="color:#080A66"></i> <?php print LANGPROF5 ?>
      </label>
      <?php
      $choix_tri_text=$choix_tri;
      if ($choix_tri_text == "trimestre1") $choix_tri_text=LANGPROJ3. " ou ".LANGPROJ19;
      if ($choix_tri_text == "trimestre2") $choix_tri_text=LANGPROJ4. " ou ".LANGPROJ20;
      if ($choix_tri_text == "trimestre3") $choix_tri_text=LANGPROJ5;
      ?>
      <select name="choix_trimestre" class="cc-select">
        <option value='<?php print $choix_tri?>'><?php print ucfirst($choix_tri_text)?></option>
        <option value='trimestre1'><?php print LANGPROJ3. " ou ".LANGPROJ19?></option>
        <option value='trimestre2'><?php print LANGPROJ4. " ou ".LANGPROJ20?></option>
        <option value='trimestre3'><?php print LANGPROJ5?></option>
      </select>
      <input type="hidden" name="sMat"        value="<?php print $_GET['sMat'];?>">
      <input type="hidden" name="sClasseGrp"  value="<?php print $_GET['sClasseGrp'];?>">
      <input type="hidden" name="typecom"      value="<?php print $typecom?>">
    </div>

    <div style="display:flex;gap:6px;align-items:flex-end;padding-bottom:1px">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGOK ?>","create");</script>
      <?php
      $fichier="./data/pdf_bull/edition_".$_SESSION["id_pers"].".pdf";
      ?>
      <a href="visu_pdf_prof.php?id=<?php print $fichier?>" target="_blank"
         class="btn" style="font-size:11px;padding:3px 9px;background:#e8eaf6;color:#080A66;border:1px solid #c5cae9;display:inline-flex;align-items:center;gap:4px">
        <i class="bi bi-file-pdf-fill" style="color:#c62828"></i> PDF
      </a>
    </div>

  </div>
</form>
</div>
</div>

<?php if ($choix_tri == ""): ?>
<div class="alert" style="margin-bottom:10px;font-size:12px;font-weight:700">
  <i class="bi bi-exclamation-triangle-fill"></i> Indiquer un trimestre ou semestre !
</div>
<?php endif; ?>

<!-- ── Infos classe + bouton Historique ──────────────────────────────────── -->
<?php
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
	} else {
		$moyClass=moyeMatGenExamen($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse,$_SESSION["id_pers"],"Partiel Blanc");
		$notetype=recherchetypenote($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse);
	}
} else {
	if ($groupe == 1) {
		$moyClass=moyeMatGenGroupe($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe,$_SESSION["id_pers"]);
		$notetype=recherchetypenotegroupe($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe);
	} else {
		$moyClass=moyeMatGen($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse,$_SESSION["id_pers"]);
		$notetype=recherchetypenote($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse);
	}
}
if ($notetype == "en") { $moyClass=recherche_note_en($moyClass); }
?>

<div class="card" style="margin-bottom:10px">
<div class="card-body" style="padding:8px 12px">
  <div class="bcp-meta">
    <span><i class="bi bi-mortarboard-fill" style="color:#c5cae9"></i> <strong><?php print $nomClasse?></strong><?php if ($nomGrp) print ' — '.$nomGrp; ?></span>
    <span><i class="bi bi-book" style="color:#c5cae9"></i> <strong><?php print $nomMat?></strong></span>
    <span><i class="bi bi-calendar2-range" style="color:#c5cae9"></i>
      Du <strong><?php print dateForm($dateDebut)?></strong> au <strong><?php print dateForm($dateFin)?></strong>
    </span>
    <?php
    $moyBadge = (is_numeric($moyClass) && $moyClass < 10) ? 'bad' : 'ok';
    ?>
    <span class="bcp-badge-moy <?php print $moyBadge?>">
      <i class="bi bi-bar-chart-fill"></i> <?php print LANGMESS123 ?> : <?php print $moyClass ?>
    </span>
    <script language=JavaScript>buttonMagic2("<?php print LANGMESS118 ?>",'bulletincomprof22.php?sClasseGrp=<?php print $sClasseGrp ?>&sMat=<?php print $mid ?>&typecom=<?php print $typecom ?>','bulletincom','width=800,height=600,resizable=yes,scrollbars=yes','')</script>
  </div>
</div>
</div>

<!-- ── Info scolaire complémentaire ─────────────────────────────────────── -->
<?php if ((defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")): ?>
<div class="bcp-info-scol">
  <i class="bi bi-info-circle-fill" style="color:#080A66"></i>
  <span>Information Scolaire Complémentaire :</span>
  <script language=JavaScript>buttonMagic("cliquez-ici","profpprojo.php?fiche=1&idClasse=<?php print $cid?>","video","width=800,height=700,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes","");</script>
</div>
<?php endif; ?>

<!-- ── Token IA ──────────────────────────────────────────────────────────── -->
<span id="afficheToken" style="position:relative"></span>

<!-- ── Formulaire de saisie ─────────────────────────────────────────────── -->
<script language=Javascript>
var deja=0;
function envoi() {
	document.formulaire.valide.disabled=true;
	return true;
}
</script>
<script language=Javascript>
<?php
print "var tab=new Array();\n";
$data=liste_com_bulletin($_SESSION["id_pers"]);
for($i=0;$i<countTriade($data);$i++) {
	$text=$data[$i][1];
	$nb=$data[$i][0];
	$text=preg_replace("/\r\n/"," ",$text);
	$text=preg_replace("/\"/","\\\"",$text);
	print "tab[$nb]=\"".$text."\";\n";
}
?>
</script>

<form method='post' name='formulaire' action="bulletincomprof4.php" onsubmit="return envoi();">

<?php
// ── Construction de la liste élèves ───────────────────────────────────────
if ($HPV['gid']) {
	$gid=$HPV['gid'];
	$sqlIn=<<<SQL
	SELECT liste_elev FROM {$prefixe}groupes WHERE group_id='$gid'
SQL;
	$curs=execSql($sqlIn);
	$in=chargeMat($curs);
	freeResult($curs);
	$in=$in[0][0];
	$in=substr($in,1);
	$in=substr($in,0,-1);
	$sql="SELECT elev_id,";
	if (DBTYPE=='pgsql') $sql .= " upper(trim(nom))||' '||initcap(trim(prenom)) ";
	elseif (DBTYPE=='mysql') $sql .= " CONCAT( UPPER(TRIM(nom)) , ' ' , TRIM(prenom) ) ";
	$sql .= " FROM {$prefixe}eleves WHERE elev_id IN ($in) AND annee_scolaire='$anneeScolaire' ORDER BY 2";
	unset($in);
} else {
	$cid=$HPV['cid'];
	$sql="SELECT elev_id,";
	if (DBTYPE=='pgsql') $sql .= " upper(trim(nom))||' '||initcap(trim(prenom)) ";
	elseif (DBTYPE=='mysql') $sql .= " CONCAT( UPPER(TRIM(nom)) , ' ' , TRIM(prenom) ) ";
	$sql .= " FROM {$prefixe}eleves WHERE classe='$cid' AND annee_scolaire='$anneeScolaire' ORDER BY 2";
	unset($cid);
}
$curs=execSql($sql);
unset($sql);
$mat=chargeMat($curs);
freeResult($curs);
unset($curs);

// ── PDF init ───────────────────────────────────────────────────────────────
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
$pdf=new PDF();
$pdf->AddPage();
$xcoor=20; $ycoor=5;
$nomclasse=chercheClasse_nom($cid);
$texte="Appréciation pour le bulletin trimestriel classe : $nomclasse ";
$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor,$ycoor);
$pdf->Write(5,$texte);
$ycoor+=5;
$pdf->SetFont('Arial','',11);
$pdf->SetXY($xcoor,$ycoor);
$pdf->Write(5,"Du ".dateForm($dateDebut)." au ".dateForm($dateFin)."  -  ".ucfirst($choix_tri_text)." / Année Scolaire : ".$anneeScolaire);
$pdf->SetXY($xcoor,$ycoor+5);
$nomMatiere=chercheMatiereNom($idMatiere);
$pdf->Write(5,"Matière : ".$nomMatiere);
$pdf->SetXY($xcoor,$ycoor+10);
$pdf->SetTextColor(0,0,255);
$pdf->WriteHTML(LANGMESS123." : <b>$moyClass</b>");
$pdf->SetTextColor(0,0,0);
$ycoor+=15;
?>

<!-- ── Cartes élèves ─────────────────────────────────────────────────────── -->
<?php for ($i=0; $i<countTriade($mat); $i++):
	if ($ii == 24) { $pdf->AddPage(); $ii=0; $xcoor=20; $ycoor=20; }
	$ii++;

	$idEleve=$mat[$i][0];
	$photoeleve="image_trombi.php?idE=".$mat[$i][0];

	if ($typecom == 4) {
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiereExamen($idEleve,$idMatiere,dateForm($dateDebut),dateForm($dateFin),$_SESSION["id_pers"],"Partiel Blanc");
			$notetype=recherchetypenote($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idClasse);
		} else {
			$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe,$_SESSION["id_pers"],"Partiel Blanc");
			$notetype=recherchetypenotegroupe($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe);
		}
	} else {
		if ($groupe == 1) {
			$moyEleve=moyenneEleveMatiereGroupe($idEleve,$idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe,$_SESSION["id_pers"]);
			$moyElevePrecedent=moyenneEleveMatiereGroupe($idEleve,$idMatiere,dateForm($dateDebutP),dateForm($dateFinP),$idgroupe,$_SESSION["id_pers"]);
			$notetype=recherchetypenotegroupe($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idgroupe);
		} else {
			$moyEleve=moyenneEleveMatiere($idEleve,$idMatiere,dateForm($dateDebut),dateForm($dateFin),$_SESSION["id_pers"]);
			$moyElevePrecedent=moyenneEleveMatiere($idEleve,$idMatiere,dateForm($dateDebutP),dateForm($dateFinP),$_SESSION["id_pers"]);
			$notetype=recherchetypenote($idMatiere,dateForm($dateDebut),dateForm($dateFin),$idclasse);
		}
	}

	if ($notetype == "en") {
		$moyEleve=recherche_note_en($moyEleve);
		$color="blue"; $rb1=0;$rb2=0;$rb3=255;
	} else {
		if ($moyEleve < 10) { $color="red"; $rb1=255;$rb2=0;$rb3=0; }
		else { $color="orange"; $rb1=0;$rb2=0;$rb3=0; }
	}

	$commentaireeleve="";
	$commentaireeleve=cherche_com_eleve2($idEleve,$idMatiere,$idclasse,$choix_tri,$_SESSION["id_pers"],$idgroupe,$typecom);

	if (defined("NBCARBULL")) { $nbcar=NBCARBULL; } else { $nbcar=400; }
	if ($typecom > 0) { $nbcar=400; }

	if (file_exists("./common/config-ia.php")) {
		include_once("common/productId.php");
		include_once("common/config-ia.php");
		$productID=PRODUCTID; $iakey=IAKEY;
		$prenom=recherche_eleve_prenom($idEleve);
		$lienIA="ajaxIABulletinCom(document.getElementById('saisie_text_$i').value,'$moyEleve','$productID','$iakey','saisie_text_$i','$prenom',document.getElementById('tonia_$i').value)";
	} else {
		$lienIA="alert('Votre Triade n\'est pas configur&eacute; pour utiliser l\'IA. Contacter votre administrateur Triade')";
	}

	$moyBadgeEleve = ($notetype != "en" && is_numeric($moyEleve) && $moyEleve < 10) ? 'bad' : 'ok';

	// PDF
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($xcoor,$ycoor);
	$nomprenom=trunchaine($mat[$i][1],30);
	$hauteur=16;
	$pdf->MultiCell(60,$hauteur,"$nomprenom",'1','','',0);
	$pdf->SetXY($xcoor+60,$ycoor);
	$pdf->SetFont('Arial','',8);
	$pdf->SetTextColor($rb1,$rb2,$rb3);
	$pdf->MultiCell(10,$hauteur,"$moyEleve",'1','','',0);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetXY($xcoor+70,$ycoor);
	$pdf->SetFont('Arial','',7);
	$pdf->MultiCell(80,$hauteur,"",'1','','',0);
	$pdf->SetXY($xcoor+70,$ycoor+0.5);
	$commentaireeleve_pdf=trunchaine($commentaireeleve,405);
	$pdf->MultiCell(80,3,"$commentaireeleve_pdf",'0','','L',0);
	$xcoor=20; $ycoor+=$hauteur;
	if ($ycoor >= 240) { $ycoor=10; $pdf->AddPage(); }
?>

<div class="bcp-card">

  <!-- ── Colonne gauche : photo + nom + moy ────────────────────────────── -->
  <div class="bcp-left">
    <img src="<?php print $photoeleve?>" class="bcp-photo" alt="<?php print $mat[$i][1]?>">
    <div class="bcp-name"><?php print trunchaine($mat[$i][1],18)?></div>
    <span class="bcp-badge-moy <?php print $moyBadgeEleve?>" style="font-size:11px">
      <?php print $moyEleve?>
    </span>
  </div>

  <!-- ── Colonne droite : outils + textarea ────────────────────────────── -->
  <div class="bcp-right">

    <div class="bcp-tools">
      <!-- Historique commentaires -->
      <?php $commentaireeleve_js = addslashes($commentaireeleve); ?>
      <?php if (trim($commentaireeleve) != ""): ?>
      <button type="button" class="bcp-btn-save"
              onMouseOver="AffBulle('<u><strong><?php print LANGMESS125 ?> :</strong></u><br>1º: <?php print LANGMESS126 ?>.<br>2º: <?php print LANGMESS127 ?> +.')"
              onMouseOut="HideBulle()"
              onClick="GP_AdvOpenWindow('bulletin_com_bdd_ajout.php?com=<?php print addslashes(urlencode($commentaireeleve))?>','<?php print LANGMESS128 ?>','fullscreen=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,channelmode=no,directories=no',200,100,'center','ignoreLink','alwaysOnTop',3,'',0,1,5,'');return document.MM_returnValue"
              title="<?php print LANGMESS125 ?>">
        <i class="bi bi-bookmark-plus"></i>
      </button>
      <?php endif; ?>

      <!-- Pulldown commentaires enregistrés -->
      <select onChange="motifbulletin('<?php print $i?>',this.value)" class="bcp-select">
        <option value=0>— Modèles de commentaires —</option>
        <?php select_com_bulletin($_SESSION["id_pers"],35); ?>
      </select>

      <!-- Compteur chars -->
      <input type='text' name='CharRestant_<?php print $i?>' class="bcp-counter" disabled="disabled" title="Caractères restants">

      <!-- COPILOT IA -->
      <button type="button" class="bcp-btn-ia" onClick="<?php print $lienIA?>">
        <i class="bi bi-stars"></i> COPILOT
      </button>

      <!-- Ton IA -->
      <select name='tonia' id='tonia_<?php print $i?>' class="bcp-select" style="max-width:160px">
        <option value='IA'>Comportement IA : Par défaut</option>
        <option value='Neutre'>Neutre</option>
        <option value='Positif'>Positif</option>
        <option value='Encourageant'>Encourageant</option>
        <option value='Inquiétant'>Inquiétant</option>
        <option value='Motivant'>Motivant</option>
      </select>
    </div>

    <!-- Textarea commentaire -->
    <input type="hidden" name="saisie_eleve_<?php print $i?>" value="<?php print $idEleve?>">
    <textarea
      onkeypress="compter(this,'<?php print $nbcar ?>', this.form.CharRestant_<?php print $i?>)"
      cols="78" rows="5"
      name="saisie_text_<?php print $i?>"
      id="saisie_text_<?php print $i?>"
      class="bcp-textarea"><?php $commentaireeleve=stripslashes($commentaireeleve); print $commentaireeleve?></textarea>

    <!-- Période précédente collapsible -->
    <?php
    $choix_triprecedent="";
    if ($choix_tri == "trimestre2") $choix_triprecedent="trimestre1";
    if ($choix_tri == "semestre2")  $choix_triprecedent="semestre1";
    if ($choix_tri == "trimestre3") $choix_triprecedent="trimestre2";
    if ($choix_triprecedent != ""):
    ?>
    <div style="margin-top:4px">
      <span class="bcp-prev-toggle" onclick="slidedown_showHide('box<?php print $i?>')">
        <i class="bi bi-clock-history"></i> <?php print LANGMESS124 ?> — <?php print LANGMESS129 ?>
      </span>
      <div style="background-color:#CCCCCC;padding:0px;margin-top:4px">
        <div id="dhtmlgoodies_control" style="background-color:#CCCCCC;padding:0px;margin-top:0px"></div>
        <div style="width:100%;background-color:#CCCCCC;padding:0px;margin-top:0px" class="dhtmlgoodies_contentBox" id="box<?php print $i?>">
          <div class="dhtmlgoodies_content" id="subBox<?php print $i?>" style="background-color:#CCCCCC;padding:0px;margin-top:0px">
            <div class="bcp-prev-box">
              <?php
              $commentaireprecedent="";
              $commentaireprecedent=cherche_com_eleve2($idEleve,$idMatiere,$idclasse,$choix_triprecedent,$_SESSION["id_pers"],$idgroupe,$typecom);
              print LANGMESS130." : <strong>$moyElevePrecedent</strong><br>$commentaireprecedent";
              $moyElevePrecedent="";
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- .bcp-right -->
</div><!-- .bcp-card -->

<?php endfor; ?>

<!-- ── Champs cachés + bouton valider ────────────────────────────────────── -->
<input type='hidden' name='nb'               value="<?php print countTriade($mat)?>">
<input type='hidden' name='saisie_classe'    value="<?php print $idclasse?>">
<input type='hidden' name='saisie_matiere'   value="<?php print $idMatiere?>">
<input type='hidden' name='choix_trimestre'  value="<?php print $choix_tri?>">
<input type='hidden' name='saisie_groupe'    value="<?php print $idgroupe?>">
<input type='hidden' name='typecom'          value="<?php print $typecom?>">
<input type='hidden' name='anneeScolaire'    value="<?php print $anneeScolaire?>">

<div style="display:flex;align-items:center;gap:10px;margin-top:10px;padding:8px 0;border-top:1px solid #dde0f0">
  <label style="font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;display:flex;align-items:center;gap:5px;color:#444">
    <input type="checkbox" name="bibli" value="oui" style="accent-color:#080A66">
    <?php print LANGCOM ?>
  </label>

  <?php if ($choix_tri != ""): ?>
  <script language=JavaScript>buttonMagicSubmit2("<?php print LANGMESS131 ?>","valide","<?php print LANGMESS132 ?>");</script>
  <?php else: ?>
  <span style="font-size:11px;font-weight:700;color:#c62828;font-family:Electrolize">
    <i class="bi bi-exclamation-triangle-fill"></i> Aucun choix de trimestre/semestre indiqué !
  </span>
  <?php endif; ?>
</div>

</form>

<?php
@unlink($fichier);
$pdf->output('F',$fichier);
include_once("./librairie_php/lib_conexpersistant.php");
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align:center;");
Pgclose();
?>

</div>
</BODY></HTML>
