<?php
$dstJ.="\" \"";
$dstT.="\" \"";
$date_deja="00/0000";
$txt_fini=0;
$passe=0;

$data=affPlanEquip("salle","rien");
// id.n,idmatos.n,idqui.n,quand.n,heure_depart.n,heure_fin.n,info.n,valider.n,type.m,id,m

$tab_j=array();
// $data : tab bidim - soustab 3 champs
for($i=0;$i<count($data);$i++)
{
	$date_recup=dateMoisAnnee($data[$i][3]);
        $date_recup_jour_mois=dateJourMois($data[$i][3]);
        $date_recup_annee=dateAnnee($data[$i][3]);
        $annee=$saisie_annee_choix;
        if ($annee == $date_recup_annee) {
        		$equip=recherche_equip($data[$i][1]);
                $equip=preg_replace('/"/',"&quot;",$equip);
                $equip=preg_replace('/\'/','\\\\\'',$equip);
                $equip=preg_replace('/"/',"&quot;",$equip);
                $equip=preg_replace('/\'/','\\\\\'',$equip);
                $passe=1;
 		if ($date_deja == $date_recup_jour_mois ) {
                        $dstT.="<HR>";
                        $dstT.="Salle : ".trim($equip)."<BR>de  ".trim(timeForm($data[$i][4]))." à ".trim(timeForm($data[$i][5]));
                }else{
                        if ($txt_fini == 1) {
                                $dstT.="\"";
                        }
                        $date_deja=$date_recup_jour_mois;
                        $dstJ.=",\"".$date_recup_jour_mois."\"";
                        $dstT.=",\" Salle : ".trim($equip)."<BR>de ".trim(timeForm($data[$i][4]))." à ".trim(timeForm($data[$i][5]));
                        $txt_fini=1;
                }
       }
}
if ($passe == 1 ) {
$dstT.="\"";
}

$LLUNDI=LANGLETTRELUNDI;
$LMARDI=LANGLETTREMARDI;
$LMERCREDI=LANGLETTREMERCREDI;
$LJEUDI=LANGLETTREJEUDI;
$LVENDREDI=LANGLETTREVENDREDI;
$LSAMEDI=LANGLETTRESAMEDI;
$LDIMANCHE=LANGLETTREDIMANCHE;

$FerieSamedi="";
$FerieMercredi="";
if ((CALMERCREDIAP == "oui") && (CALMERCREDIMATIN == "oui"))  { $FerieMercredi=" || (j==2)"; }
if ((CALMERCREDIAP == "oui") && (CALMERCREDIMATIN == "non"))  { $Ferieap="background='image/commun/ma.jpg'"; $FerieMatin=""; }
if ((CALMERCREDIAP == "non") && (CALMERCREDIMATIN == "oui"))  { $FerieMatin="background='image/commun/ap.jpg'"; $Ferieap=""; }

if ((CALSAMEDIAP == "oui") && (CALSAMEDIMATIN == "oui"))  { $FerieSamedi=" || (j==5)"; }
if ((CALSAMEDIAP == "oui") && (CALSAMEDIMATIN == "non"))  { $FerieSaap="background='image/commun/ma.jpg'"; $FerieSaMatin=""; }
if ((CALSAMEDIAP == "non") && (CALSAMEDIMATIN == "oui"))  { $FerieSaMatin="background='image/commun/ap.jpg'"; $FerieSaap=""; }


print "<script language=JavaScript>";
print "\nvar dstJ=new Array(".$dstJ.");";
print "\nvar dstT=new Array(".$dstT.");";

// Ici on déclare les dates des jours fériés fixes
print "var ferie=new Array(".FERIE.");";


print <<<EOF
var mois=new Array(langfuncmois1,langfuncmois2,langfuncmois3,langfuncmois4,langfuncmois5,langfuncmois6,langfuncmois7,langfuncmois8,langfuncmois9,langfuncmois10,langfuncmois11,langfuncmois12);

function disp(txt) { document.write(txt) }
function estFerie(j,m) {
        var nb=ferie.length;
        var test=false;
        for(var i=0;i<nb;i++) {
                if ((ferie[i].substring(0,2)==j)&&(ferie[i].substring(3,5)==m)) return true;
        }
        return false;
}

function estConseil(j,m) {
        var nb=dstJ.length;
        var test=false;
        for(var i=0;i<nb;i++) {
           if ((dstJ[i].substring(0,2)==j)&&(dstJ[i].substring(3,5)==m)) return true;
        }
        return false;
}

var ii=1;


function calendar(m, a) {
EOF;
	$newDATE=datecalendrier();
	print "var d_jour=$newDATE";
print <<<EOF
	  var d_jour=new Date();
        var d=new Date(a,m-1,1);
        var dfin=new Date(a,m-1,1);
        var nb_jour=31;
        var aff_j="";
        for(var k=32;k>27;k--) {
                dfin.setMonth(m-1);
                dfin.setDate(k);
                if (dfin.getMonth()!=m-1) {nb_jour=k-1;}
        }

        var j1=d.getDay(); if (j1==0) j1=7;
        var jour=0;
        disp("<FONT   color='#000000' size=1><CENTER><B>"+mois[d.getMonth()]+" "+a+"</B></CENTER></FONT>");
        disp("<TABLE border=0 bgcolor='#000099' cellspacing=0 cellpadding='2'>");
        disp("<TR align='center' bgcolor='#CCCCCC'><TD width='10'>$LLUNDI</TD><TD width='10'>$LMARDI</TD><TD width='10'>$LMERCREDI</TD><TD width='10'>$LJEUDI</TD><TD width='10'>$LVENDREDI</TD><TD width='10'>$LSAMEDI</TD><TD width='10'>$LDIMANCHE</TD></TR>");
        for(var i=0;i<6;i++) {
                disp("<TR>");
                for (j=0;j<7;j++) {
                        jour=7*i+j-j1+2;
			aff_j=jour;
			aff_jj=aff_j;
                        if ((jour==d_jour.getDate())&&(m==d_jour.getMonth()+1)) {aff_jj="<b><font color='#CC0000' >"+jour+"</font></b>";}
                        if ((7*i+j>=j1-1)&&(jour<=nb_jour)) {
                                if ((j==6)||(estFerie(jour,m)) || (estConseil(jour,m))  $FerieSamedi $FerieMercredi ) {
					if (estConseil(jour,m)) {

                                     disp("<TD width='10' bgcolor='pink'  align='center'><FONT face='Arial' size='-1' color='#0000CC'><A href='#' onMouseOver=\"AffBulle('<font face=Georgia, Times New Roman, Times, serif>"+dstT[ii]+" </FONT>');\"  onMouseOut='HideBulle()' onclick=\"PopupCentrer('calendrier_config_salle.php?saisiejour="+aff_j+"&saisiemois="+mois[d.getMonth()]+"&saisieannee="+a+"','600','400','scrollbars=yes','calendrier');\">"+aff_jj+"</A></FONT></TD>");
					ii++;
                                        }else {
                                                disp("<TD width='10' bgcolor='#CCCCff' align='center'><FONT face='Arial' size='-1' color='#0000CC'>"+aff_j+"</FONT></TD>")
                                        }

                                }  else {
				    if(j==2) {
					disp("<TD $Ferieap $FerieMatin width='10' bgcolor='#FFFFFF' align='center'><FONT face='Arial' size='-1' color='#0000CC'><A href='#' onclick=\"PopupCentrer('calendrier_config_salle.php?saisiejour="+aff_j+"&saisiemois="+mois[d.getMonth()]+"&saisieannee="+a+"','600','400','','calendrier')\";>"+aff_j+"</A></FONT></TD>");
				    }else{
					   if (j == 5) 
						disp("<TD $FerieSaap $FerieSaMatin width='10' bgcolor='#FFFFFF' align='center'><FONT face='Arial' size='-1' color='#0000CC'><A href='#' onclick=\"PopupCentrer('calendrier_config_salle.php?saisiejour="+aff_j+"&saisiemois="+mois[d.getMonth()]+"&saisieannee="+a+"','600','400','','calendrier')\";>"+aff_j+"</A></FONT></TD>");
					   else
	                                    disp("<TD width='10' bgcolor='#FFFFFF' align='center'><FONT face='Arial' size='-1' color='#0000CC'><A href='#' onclick=\"PopupCentrer('calendrier_config_salle.php?saisiejour="+aff_j+"&saisiemois="+mois[d.getMonth()]+"&saisieannee="+a+"','600','400','','calendrier')\";>"+aff_j+"</A></FONT></TD>");
				    }
				}
                        }
                        else
                            disp("<TD width='10' bgcolor='#FFFFFF'><FONT size=-1>&nbsp; </TD>");

                }
                disp("</TR>");
        }
        disp("</TABLE>");
}
function annee(an) {
        disp("<center><TABLE cellspacing=15>");
        for (var i=0;i<4;i++) {
                disp("<TR>");
                for (var j=0;j<3;j++) {
                        disp("<TD align='center'>");
                        calendar(i*3+j+1,an);
                        disp("</TD>");
                }
        }
        disp("</TABLE></center>");
}
</script>
EOF;

?>
