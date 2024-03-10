<?php
session_start();
error_reporting(0);
if (trim($_POST["typebull"]) == "0") { header("Location:imprimer_trimestre.php?err"); exit ; }
setcookie("bulletinselection",$_POST["typebull"],time()+36000*24*30);
setcookie("bulletinannee",$_POST["anneeScolaire"],time()+36000*24*30);
setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
if (isset($_POST['saisie_trimestre'])) {
	setcookie("saisie_trimestre",$_POST["saisie_trimestre"],time()+36000*24*30);
}


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

$annescolaire=$_POST["anneeScolaire"];
$annescolaireRep=preg_replace('/ /','',$annescolaire);
if (!is_dir("./data/archive/bulletin/$annescolaireRep")) mkdir("./data/archive/bulletin/$annescolaireRep");

$anneeScolaire=$_POST["anneeScolaire"];

include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/recupnoteperiode.php");
include_once("./librairie_php/lib_get_init.php");
include_once('./librairie_pdf/php.arabe/Arabic.php');
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
?>

<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html;" />
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_bascule_select.js"></script>
<script language="JavaScript" src="./librairie_js/lib_ordre_liste.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<script language="javaScript">
var nbElems=0;
function notaffminmaxA() {
	if (document.getElementById('notaffmoyclass').checked == true) {
		document.getElementById('notaffminmax').checked=true; 
	}

	if (document.getElementById('notaffmoyclass').checked == false) {
		document.getElementById('notaffminmax').checked=false; 
	}
}


function notaffminmaxB() {
	if (document.getElementById('notaffminmax').checked == false) {
		 document.getElementById('notaffmoyclass').checked=false;
	}
	
}

function calcul(op) {
	// calcul le nombre d'élèment
	nbElems = eval(nbElems + op);
	if (nbElems < 0 ) { nbElems = 0; }
	document.formulaire.saisie_nb_recherche.value=nbElems;
}

function prepEnvoi() {
	var hid = new String();
	var tab = new Array();
	var data = window.document.formulaire.saisie_recherche.options;
	for (i=0;i<data.length;i++)
	{
		tab.push(data[i].value);
	}
	document.formulaire.saisie_recherche_final.value=tab.join(",");
}

</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();"  >
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL5?> </font></b></td></tr>
<tr  id='cadreCentral0' >
<td valign='top' >
<!-- // fin  --><br>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menuprof") {
	$data=aff_enr_parametrage("autorisebulletinprof"); 
	if ($data[0][1] == "oui") {
		validerequete("3");
	}else{
		verif_profp_class($_SESSION["id_pers"],$_POST["saisie_classe"]);
	}
}else{
	if ($_SESSION["membre"] == "menupersonnel") {
		if (!verifDroit($_SESSION["id_pers"],"imprbulletin")) {
			Pgclose();
			accesNonReserveFen();
			exit();
		}
	}else{
		validerequete("2");
	}
}
$idclasse=$_POST["saisie_classe"];
$nbEleve=nbEleve($idclasse,$_POST["anneeScolaire"]);
$typebull=trim($_POST["typebull"]);
if ($typebull == "bull01") { $bull="bulletin_construction01.php"; }
if ($typebull == "bull01UE") { $bull="bulletin_construction01UE.php"; }
if ($typebull == "bull05UE") { $bull="bulletin_construction05UE.php"; }
if ($typebull == "bull04UE") { $bull="bulletin_construction04UE.php"; }
if ($typebull == "bull04UE3") { $bull="bulletin_construction04UE3.php"; }
if ($typebull == "bull04UE2") { $bull="bulletin_construction04UE2.php"; }
if ($typebull == "bull01a") { $bull="bulletin_construction01a.php"; }
if ($typebull == "bull01b") { $bull="bulletin_construction01b.php"; }
if ($typebull == "bull02") { $bull="bulletin_construction02.php"; }
if ($typebull == "bull03") { $bull="bulletin_construction03.php"; }
if ($typebull == "bull04") { $bull="bulletin_construction04.php"; }
if ($typebull == "bull05") { $bull="bulletin_construction05.php"; }
if ($typebull == "bull06") { $bull="bulletin_construction06.php"; }
if ($typebull == "bull07") { $bull="bulletin_construction07.php"; }
if ($typebull == "bull08") { $bull="bulletin_construction08.php"; }
if ($typebull == "bull09") { $bull="bulletin_construction09.php"; }
if ($typebull == "bull099") { $bull="bulletin_construction099.php"; }
if ($typebull == "bull099a") { $bull="bulletin_construction099a.php"; }
if ($typebull == "bull10") { $bull="bulletin_construction10.php"; }
if ($typebull == "bull11") { $bull="bulletin_construction11.php"; }
if ($typebull == "bull12") { $bull="bulletin_construction12.php"; }
if ($typebull == "bull12bis") { $bull="bulletin_construction12bis.php"; }
if ($typebull == "bull12ter") { $bull="bulletin_construction129.php"; }
if ($typebull == "bull0101") { $bull="bulletin_construction0101.php"; }
if ($typebull == "bull0101a") { $bull="bulletin_construction0101a.php"; }
if ($typebull == "bull0101b") { $bull="bulletin_construction0101b.php"; }
if ($typebull == "bull01017") { $bull="bulletin_construction01017.php"; }
if ($typebull == "bull01018") { $bull="bulletin_construction01018.php"; }
if ($typebull == "bull01011") { $bull="bulletin_construction01011.php"; }
if ($typebull == "bull01012") { $bull="bulletin_construction01012.php"; }
if ($typebull == "bull01013") { $bull="bulletin_construction01013.php"; }
if ($typebull == "bull01014") { $bull="bulletin_construction01014.php"; }
if ($typebull == "bull01016") { $bull="bulletin_construction01016.php"; }
if ($typebull == "bull0102") { $bull="bulletin_construction0102.php"; }
if ($typebull == "bull0103") { $bull="bulletin_construction0103.php"; }
if ($typebull == "bull01022") { $bull="bulletin_construction01022.php"; }
if ($typebull == "bull0104") { $bull="bulletin_construction0104.php"; }
if ($typebull == "bull0105") { $bull="bulletin_construction0105.php"; }
if ($typebull == "bull0106") { $bull="bulletin_construction0106.php"; }
if ($typebull == "bull0107") { $bull="bulletin_construction0107.php"; }
if ($typebull == "bull0108") { $bull="bulletin_construction0108.php"; }
if ($typebull == "bull0108") { $bull="bulletin_construction0108.php"; }
if ($typebull == "bull0109") { $bull="bulletin_construction0109.php"; }
if ($typebull == "bull0109-2") { $bull="bulletin_construction0109-2.php"; }
if ($typebull == "bull0110") { $bull="bulletin_construction0110.php"; }
if ($typebull == "bull0111") { $bull="bulletin_construction0111.php"; }
if ($typebull == "bull0208") { $bull="bulletin_construction0208.php"; }
if ($typebull == "bull0209") { $bull="bulletin_construction0209.php"; }
if ($typebull == "bull0210") { $bull="bulletin_construction0210.php"; }
if ($typebull == "bull0210a") { $bull="bulletin_construction0210a.php"; }
if ($typebull == "bull0210b") { $bull="bulletin_construction0210b.php"; }
if ($typebull == "bull112") { $bull="bulletin_construction0112.php"; }
if ($typebull == "bull113") { $bull="bulletin_construction0113.php"; }
if ($typebull == "bull500") { $bull="bulletin_construction02premiersemestre.php"; } 
if ($typebull == "bull501") { $bull="bulletin_construction02secondsemestre.php"; } 
if ($typebull == "bull502") { $bull="bulletin_construction502.php"; }
if ($typebull == "bull503") { $bull="bulletin_construction503.php"; } 
if ($typebull == "bulllprb") { $bull="bulletin_construction01-lprb.php"; } 
if ($typebull == "bull01133") { $bull="bulletin_construction01133.php"; } 
if ($typebull == "bull01133a") { $bull="bulletin_construction01133a.php"; } 
if ($typebull == "bull01144") { $bull="bulletin_construction01144.php"; } 
if ($typebull == "bull01015") { $bull="bulletin_construction01015.php"; } 
if ($typebull == "bull01015-2") { $bull="bulletin_construction01015-2.php"; } 
if ($typebull == "bull401") { $bull="bulletin_construction400.php"; }  // BAC Blanc
if ($typebull == "bull402") { $bull="bulletin_construction400.php"; }  // BTS Blanc
if ($typebull == "bull403") { $bull="bulletin_construction400.php"; }  // Brevet Blanc
if ($typebull == "bull404") { $bull="bulletin_construction400.php"; }  // CAP Blanc
if ($typebull == "bull405") { $bull="bulletin_construction400.php"; }  // BEP Blanc
if ($typebull == "bull406") { $bull="bulletin_construction400.php"; }  // Partiel Blanc
if ($typebull == "bull409") { $bull="bulletin_construction400.php"; }  // Brevet Professionnel Blanc
if ($typebull == "bul9999") 	{ $bull="bulletin_construction_vatel.php"; }
if ($typebull == "bul9999en") 	{ $bull="bulletin_construction_vatel_en.php"; }
if ($typebull == "bul9999a") 	{ $bull="bulletin_construction_vatel_annuel.php"; }
if ($typebull == "bul9999aa") 	{ $bull="bulletin_construction_vatel_annuel_beta.php"; }
if ($typebull == "bul9999b") 	{ $bull="bulletin_construction_vatel_madrid_annuel.php"; }
if ($typebull == "bul9999c") 	{ $bull="bulletin_construction_vatel_2.php"; }
if ($typebull == "bul9999d") 	{ $bull="bulletin_construction_vatel_paris.php"; }
if ($typebull == "bul9999e") 	{ $bull="bulletin_construction_vatel_annuel_paris.php"; }
if ($typebull == "bul9999f") 	{ $bull="bulletin_construction_vatel_annuel_paris_beta.php"; }
if ($typebull == "bul0303") { $bull="bulletin_construction0303.php"; }  // Seminaire Lycee
if ($typebull == "bul0304") { $bull="bulletin_construction0304.php"; }  // Seminaire college
if ($typebull == "bull0305") { $bull="bulletin_construction0305.php"; }  // ISMAPP
if ($typebull == "bull0305a") { $bull="bulletin_construction0305a.php"; }  // ISMAPP annuel
if ($typebull == "bull0305b") { $bull="bulletin_construction0305b.php"; }  // ISMAPP annuel beta 2
if ($typebull == "bull0305b-nv") { $bull="bulletin_construction0305b-nv.php"; }  // ISMAPP annuel beta 2
if ($typebull == "bull0305b-2") { $bull="bulletin_construction0305b-2.php"; }  // ISMAPP 2019
if ($typebull == "bull0305c") { $bull="bulletin_construction0305c.php"; }  // ISMAPP annuel beta 2
if ($typebull == "bull0305d") { $bull="bulletin_construction0305d.php"; }  // ISMAPP UE
if ($typebull == "bull407") { $bull="bulletin_constructionBTSBlanc.php"; }  //  Cie. Formation BTS Blanc
if ($typebull == "bull408") { $bull="bulletin_constructionTAS.php";      }  // Cie. Formation TAS
if ($typebull == "bull410") { $bull="bulletin_constructionPB.php";      }  // Cie. Formation Partiel Blanc
if ($typebull == "bull600") { $bull="bulletin_construction600.php";      }  
if ($typebull == "bull700") { $bull="bulletin_construction700.php";      }  
if ($typebull == "bull0211") { $bull="bulletin_construction0211.php";      }  
if ($typebull == "bull0211a") { $bull="bulletin_construction0211a.php";      }  
if ($typebull == "bull800") { $bull="bulletin_construction800.php";      }  
if ($typebull == "bull01019") { $bull="bulletin_construction01019.php";      }  
if ($typebull == "bull01XL") { $bull="bulletin_construction01XL.php";      }  
if ($typebull == "bull02XL") { $bull="bulletin_construction02XL.php";      }  
if ($typebull == "bull02UE") { $bull="bulletin_construction02UE.php";      }  
if ($typebull == "bull04UE4") { $bull="bulletin_construction04UE4.php";      }  
if ($typebull == "bull03UE") { $bull="bulletin_construction03UE.php";      }  
if ($typebull == "bull09S") { $bull="bulletin_construction09S.php";      }  
if ($typebull == "bul9999t") { $bull="bulletin_construction_vatel_tunisie.php"; }
if ($typebull == "bul9999i") { $bull="bulletin_construction_vatel_ilemaurice.php"; }
if ($typebull == "bull01001") { $bull="bulletin_construction01001.php"; }
if ($typebull == "bullFr6eme") { $bull="bulletin_construction_bullFr6eme.php"; }
if ($typebull == "bullFrCycle") { $bull="bulletin_construction_bullFrCycle.php"; }
?>

<form method=post action="<?php print $bull ?>" onSubmit="document.formulaire5.rien.disabled=true;"  name="formulaire5" >
<input type='hidden' name='examen' value='<?php print $typebull ?>' />
<input type='hidden' name="type_pdf" value="pers" >
<center>

<?php if ($typebull == "bullFr6eme") { ?>
	<br /><br />
<?php
	$datap=config_param_visu("opt1livretscolaire");
        $opt1livretscolaire=$datap[0][0];
        if (trim($opt1livretscolaire) == "oui") {
        	$checkboxopt1livretscolaireOUI="checked='checked'";
        	$checkboxopt1livretscolaireNON="";
        }else{
        	$checkboxopt1livretscolaireOUI="";
        	$checkboxopt1livretscolaireNON="checked='checked'";
	}
        print "<font class='T2'>Afficher \"Elèments du programme travaillés durant la période\"  : </font> <input type='radio' name='opt1livretscolaire' value='oui' $checkboxopt1livretscolaireOUI  /> <i>(oui)</i>
	 <input type='radio' name='opt1livretscolaire' value='non' $checkboxopt1livretscolaireNON  /> <i>(non)</i><br><br>
	";


	$datap=config_param_visu("opt2livretscolaire");
	$opt2livretscolaire=$datap[0][0];
        if (trim($opt2livretscolaire) == "oui") {
                $checkboxopt2livretscolaireOUI="checked='checked'";
                $checkboxopt2livretscolaireNON="";
        }else{
                $checkboxopt2livretscolaireOUI="";
                $checkboxopt2livretscolaireNON="checked='checked'";
        }
        print "<font class='T2'>Afficher \"Enseignements pratiques interdisciplinaires (EPI) \"  : </font> <input type='radio' name='opt2livretscolaire' value='oui' $checkboxopt2livretscolaireOUI  /> <i>(oui)</i>
         <input type='radio' name='opt2livretscolaire' value='non' $checkboxopt2livretscolaireNON  /> <i>(non)</i>
        ";


}

 
if (($typebull == "bull0102") || ($typebull == "bull0109") || ($typebull == "bull0109-2") || ($typebull == "bull0103") || ($typebull == "bull01022") || ($typebull == "bull03") ||  ($typebull == "bull01") ||  ($typebull == "bull01b")  || ($typebull == "bull01019") || ($typebull == "bull800")  || ($typebull == "bull0101") || ($typebull == "bull01001") || ($typebull == "bull0101a") || ($typebull == "bull0101b") || ($typebull == "bull01017") || ($typebull == "bull0305") || ($typebull == "bull0305a") || ($typebull == "bull0305c") || ($typebull == "bull113") || ($typebull == "bull12") || ($typebull == "bull12bis") || ($typebull == "bull12ter") || ($typebull == "bull503") || ($typebull == "bulllprb") ||  ($typebull == "bull01011" ) ||  ($typebull == "bull01012" ) ||  ($typebull == "bull01013" ) ||  ($typebull == "bull01014" ) ||  ($typebull == "bull01015" ) || ($typebull == "bull01015-2") ||  ($typebull == "bull700" ) || ($typebull == "bull01018" ) ||  ($typebull == "bull01016" ) || ($typebull == "bull0305b") || ($typebull == "bull0305b-nv") || ($typebull == "bull0305b-2")||  ($typebull == "bull01UE") ||  ($typebull == "bull04UE") || ($typebull == "bull04UE3") || ($typebull == "bull04UE2") || ($typebull == "bull01XL") || ($typebull == "bull02XL")  || ($typebull == "bull02UE") || ($typebull == "bull03UE") || ($typebull == "bull05UE") || ($typebull == "bull0305d") || ($typebull == "bull099a")  ) {
?>
	<br /><br />
	<?php 
	$idclasse=$_POST["saisie_classe"];
	$nbEleve=nbEleve($idclasse,$anneeScolaire);
	?>
	<select name="plageEleve" onChange="selection()" >
	<option value="tous" id='select1'><?php print "Tous les &eacute;l&egrave;ves" ?></option>
	<?php if (($typebull == "bull0305") || ($typebull == "bull0305a") || ($typebull == "bull0305b") || ($typebull == "bull0305b-nv") || ($typebull == "bull0305b-2") || ($typebull == "bull0305c") || ($typebull == "bull0305d") || ($typebull == "bull099a") ) { ?>
		<option value="1" id='select1'><?php print "Le premier &eacute;l&egrave;ve" ?></option>
	<?php } ?>
	<option value="10" id='select1'><?php print "Les 10 premiers &eacute;l&egrave;ves" ?></option>
	<option value="20" id='select1'><?php print "Du 11 au 20ieme &eacute;l&egrave;ves" ?></option>
	<option value="30" id='select1'><?php print "Du 21 au 30ieme &eacute;l&egrave;ves" ?></option>
	<option value="40" id='select1'><?php print "Du 31 au 40ieme &eacute;l&egrave;ves" ?></option>
	<option value="50" id='select1'><?php print "Du 41 au 50ieme &eacute;l&egrave;ves" ?></option>
	<option value="60" id='select1'><?php print "Du 51 au 60ieme &eacute;l&egrave;ves" ?></option>
	<?php 
	if (($typebull == "bull0305") || ($typebull == "bull0305a") || ($typebull == "bull0305b") || ($typebull == "bull0305b-nv") || ($typebull == "bull0305b-2") || ($typebull == "bull0305c") || ($typebull == "bull0305d") ) { ?>
		<optgroup label='Elève/Etudiant'>
		<?php
		$sql="SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM ${prefixe}eleves a,${prefixe}classes b WHERE a.classe='$idclasse' AND b.code_class='$idclasse' ORDER BY nom";
		$res=execSql($sql);
		$data_eleve=chargeMat($res);
		for($o=0;$o<count($data_eleve);$o++) {
			print "<option value='E_".$data_eleve[$o][1]."' id='select1'>".$data_eleve[$o][2]." ".$data_eleve[$o][3]."</option>";
		}
		?>
	<?php 
		} 
		print "</select>";
	} 

	if ($typebull == "bull0109-2") {
                print  "<br><br><br><center><font class=T2>ajout information admission : </font> <input type='checkbox' name='affadmission' value='oui' /></center>";
        }


	if (($typebull == "bull04UE" ) || ($typebull == "bull04UE2" ) || ($typebull == "bull04UE3" ) ) {
                $datap=config_param_visu("affExam");
                $choixaffExam=$datap[0][0];
                if ($choixaffExam == "oui") $checkboxaffExam='checked="checked"';
                $datap=config_param_visu("affEtudeDeCas");
                $affEtudeDeCas=$datap[0][0];
                if ($affEtudeDeCas == "oui") $checkboxaffEtudeDeCas='checked="checked"';
                $datap=config_param_visu("hauteurMatiereUE4");
                $hauteurMatiere=$datap[0][0];
                if ($hauteurMatiere == "") $hauteurMatiere="10";
                if ($hauteurMatiere > 10) $hauteurMatiere="10";
		if (!is_numeric($hauteurMatiere)) $hauteurMatiere="10";

		if (($typebull == "bull04UE" ) || ($typebull == "bull04UE3" )) { 
			$datap=config_param_visu("bulletinProvisoire");
			$choixaffExam=$datap[0][0];
			$checkboxbullprovisoireOui="";
			$checkboxbullprovisoireNon='checked="checked"';
			if ($choixaffExam == "oui") { 
				$checkboxbullprovisoireOui='checked="checked"';
				$checkboxbullprovisoireNon="";
			}
			$datap=config_param_visu("rattrapageprovisoire");
                        $choixaffExam=$datap[0][0];
                        $checkboxRattrapageprovisoireOui="";
                        $checkboxRattrapageprovisoireNon='checked="checked"';
			$disabledRattrapage="";	
                        if ($choixaffExam != "oui") {
                                $checkboxRattrapageprovisoireOui='checked="checked"';
                                $checkboxRattrapageprovisoireNon="";
                        }else{
				$disabledRattrapage="disabled='disabled'";	
			}
		}

                print "<br><br><font class='T2'>Afficher la colonne Examen :</font> <input type='checkbox' name='affExam' $checkboxaffExam  value='oui' /> (<i>oui</i>) <br /><br />";
                print "<font class='T2'>Afficher l'étude de cas :</font> <input type='checkbox' name='affEtudeDeCas' $checkboxaffEtudeDeCas value='oui' /> (<i>oui</i>) <br /><br />";
                print "<font class='T2'>Hauteur des matières :</font> <input type='text' name='hauteurMatiere' value=\"$hauteurMatiere\" size=2 /> (Max : 10)<br /><br />";
		if (($typebull == "bull04UE" ) || ($typebull == "bull04UE3" )) {
		?>
			<script>
			function chan() { 
				document.getElementById('btaprattrapageN').checked=false ;
			 	document.getElementById('btaprattrapage').checked=true ; 
				document.getElementById('btaprattrapageN').disabled=true ;
			 	document.getElementById('btaprattrapage').disabled=true ; 
			}


			function chan2() {
				document.getElementById('btaprattrapageN').disabled=false ;
			 	document.getElementById('btaprattrapage').disabled=false ; 
			}
			</script>
			<?php
			print "<font class='T2'>Bulletin provisoire :</font> <input type='radio' onClick='chan2()' name='bulletinProvisoire' $checkboxbullprovisoireOui value='oui' /> (<i>oui</i>)<br />";
			print "<ul><font class='T2'>- Avant rattrapage :</font> <input type='radio' id='btaprattrapageN' name='rattrapageprovisoire' $checkboxRattrapageprovisoireOui  value='non' $disabledRattrapage /> (<i>oui</i>)<br />";
			print "<font class='T2'>- Après rattrapage :</font> <input type='radio' id='btaprattrapage'  name='rattrapageprovisoire' $checkboxRattrapageprovisoireNon  value='oui' $disabledRattrapage /> (<i>oui</i>)<br /></ul>";
			print "<font class='T2'>Bulletin définitif :</font> <input type='radio' onClick='chan()'  name='bulletinProvisoire' $checkboxbullprovisoireNon  value='non' /> (<i>oui</i>)<br /><br />";
			print "<hr width=50%><br>";
			print "<font class='T2'>Nbr de points en moins sur la moyenne pour absences injustifiées : " ;
			print "<br>";
			print "<br>";
			print "<table>";
			$eleveT=recupEleve($idclasse);
			for($p=0;$p<count($eleveT);$p++) {
				unset($pt);
				$ideleve=$eleveT[$p][4];
				$pt=recupPtAbsBulletin($ideleve,$_POST["saisie_trimestre"],$anneeScolaire,$idclasse);
				print "<tr><td align='right' >".$eleveT[$p][0]." ".$eleveT[$p][1]." : </td><td><input type='text' name='ptabs_$p' value=\"$pt\" size='3' />";
				print "<input type='hidden' name='ideleve_$p' value='$ideleve' />";

			}
			print "</table>";
			print "<input type='hidden' name='nbe' value='$p' />";
			print "<hr width=50%><br>";
			
				


		}
	} 	


	if ($typebull == "bull05UE")  {	
		print "<br><br>";
		print "<table>";
		$datap=config_param_visu("hauteurmatiere");
		$hauteurBull=$datap[0][0];
	        if (trim($hauteurBull) == "") {
	                $hauteurBull="8";
		}
	        $datap=config_param_visu("affphotoeleve");
	        $checkaffphotoeleve=$datap[0][0];
		if ($checkaffphotoeleve == "oui") { $checkaffphotoeleve='checked="checked"'; }
		print "<tr><td align='right'><font class=T2> Hauteur des matières : </font> </td><td>";
		print "<input type=text name='hauteurmatiere' size='3' value='$hauteurBull' /></td></tr>";
		print "<tr><td align='right'><font class=T2> Affiche les photos des ".INTITULEELEVES." : </font> </td><td>";
	        print "<input type=checkbox name='affphotoeleve' value='oui' $checkaffphotoeleve /> <i>(oui)</i></td></tr>";
	        $datap=config_param_visu("hauteurphoto");
	        $hauteurphoto=$datap[0][0];
	        $datap=config_param_visu("largeurphoto");
	        $largeurphoto=$datap[0][0];
		print "<tr><td align='right'><font class='T2'>Taille Photo des ".INTITULEELEVES." : </font></td><td>";
	        print "<input type=text name='largeurphoto' size='2' value='$largeurphoto' > largeur (mm) / ";
	        print "<input type=text name='hauteurphoto' size='2' value='$hauteurphoto'> hauteur (mm) </td></tr>";	
		print "</table>";
	}


	if ($typebull == "bull01UE")  {
		print "<br><br>";
		print "<table>";
		$datap=config_param_visu("affECTS");
		$affECTS=$datap[0][0];
		$checkboxaffECTS="";
		if ($affECTS =="oui") { $checkboxaffECTS='checked="checked"'; }

		$datap=config_param_visu("affnomenseignant");
                $affNomEnseignant=$datap[0][0];
                $checkboxNomEnseignant="";
                if ($affNomEnseignant =="oui") { $checkboxNomEnseignant='checked="checked"'; }

		print "<tr><td align='right'><font class='T2'>Affichage la colonne ECTS : </font></td><td><input type='checkbox' name='affECTS' $checkboxaffECTS value='oui'  /> <i>(oui)</i></td></tr>";

		print "<tr><td align='right'><font class='T2'>Affichage du cadre préparation examen : </font></td><td> <input type='checkbox' name='affprepaexam' value='oui' /> <i>(oui)</i></td></tr>";

		print "<tr><td align='right'><font class='T2'>Affichage du nom de l'enseignant : </font></td><td> <input type='checkbox'  $checkboxNomEnseignant name='affnomenseignant' value='oui' /> <i>(oui)</i></td></tr>";

		$datap=config_param_visu("choixcommentaire");
	        $choixcommentaire=$datap[0][0];
	        $checkboxchoix2='checked="checked"';
	        if ($choixcommentaire == "profp")       { $checkboxchoix1='checked="checked"'; $checkboxchoix2=''; }
	        if ($choixcommentaire == "direction")   { $checkboxchoix2='checked="checked"'; $checkboxchoix1=''; }
	        print "<tr><td align='right'><font class='T2'>Commentaire professeur Principal : </font></td><td> <input type='radio' name='choixcommentaire' value='profp' $checkboxchoix1  /> <i>(oui)</i></td></tr>";

	        print "<tr><td align='right'><font class='T2'>Commentaire direction : </font></td><td> <input type='radio' name='choixcommentaire' value='direction' $checkboxchoix2 /> <i>(oui)</i></td></tr>";
		print "<input type='hidden' value='".$_POST["anneeScolaire"]."' name='annee_scolaire' />";	

		$datap=config_param_visu("affcommentaire");
		$affcommentaire=$datap[0][0];
		$checkboxaffcommentaire="";
		if ($affcommentaire == "oui") { $checkboxaffcommentaire='checked="checked"'; }

		$datap=config_param_visu("affnbabsmat");
		$affnbabsmat=$datap[0][0];
		$checkboxaffnbabsmat="";
		if ($affnbabsmat == "oui") { $checkboxaffnbabsmat='checked="checked"'; }

		$datap=config_param_visu("affminmaxgeneral");
		$affminmaxgeneral=$datap[0][0];
		$checkaffminmaxgeneral="";
		if ($affminmaxgeneral == "oui") { $checkaffminmaxgeneral='checked="checked"'; }

		$datap=config_param_visu("affhautbasgeneral");
		$affhautbasgeneral=$datap[0][0];
		$checkaffhautbasgeneral="";
		if ($affhautbasgeneral == "oui") { $checkaffhautbasgeneral='checked="checked"'; }

		$datap=config_param_visu("hauteurmatiere");
		$hauteurBull=$datap[0][0];
    	    	if (trim($hauteurBull) == "") {
                	$hauteurBull="8";
		}

     	   	$datap=config_param_visu("affphotoeleve");
      	  	$checkaffphotoeleve=$datap[0][0];
		if ($checkaffphotoeleve == "oui") { $checkaffphotoeleve='checked="checked"'; }

		$datap=config_param_visu("calculmoyenbrute");
		$calculmoyenbrute=$datap[0][0];
	        if (trim($calculmoyenbrute) == "") {
        	        $checkboxcalculmoyenbrute="checked='checked'";
		}
		if ($calculmoyenbrute == "brute") $checkboxcalculmoyenbrute="checked='checked'";
		if ($calculmoyenbrute == "nonbrute") $checkboxcalculmoyennonbrute="checked='checked'";
		
		print "<tr><td align='right'><font class='T2'>Affichage la colonne des commentaires : </font></td><td> <input type='checkbox' name='affcommentaire' value='oui' $checkboxaffcommentaire /> <i>(oui)</i></td></tr>";
		print "<tr><td align='right'><font class='T2'>Affichage le nbr d'absence aux matières : </font></td><td> <input type='checkbox' name='affnbabsmat' value='oui' $checkboxaffnbabsmat /> <i>(oui)</i></td></tr>";
		print "<tr><td align='right'><font class='T2'>Moyenne générale  : </font></td><td>( Ensemble des notes <input type='radio' name='calculmoyenbrute' value='brute' $checkboxcalculmoyenbrute />) 
								      ( Moyennes des UE <input type='radio' name='calculmoyenbrute' value='nonbrute' $checkboxcalculmoyennonbrute />)</td></tr>";

		print "<tr><td align='right'><font class=T2> Hauteur des matières : </font> </td><td>";
		print "<input type=text name='hauteurmatiere' size='3' value='$hauteurBull' /></td></tr>";

		print "<tr><td align='right'><font class=T2> Afficher min,max,moy générale : </font> </td><td>";
	        print "<input type=checkbox name='affminmaxgeneral' value='oui' $checkaffminmaxgeneral /></td></tr>";

		print "<tr><td align='right'><font class=T2> La moyenne Général sur la plus haute et la plus basse : </font></td><td>";
	        print "<input type=checkbox name='affhautbasgeneral' value='oui' $checkaffhautbasgeneral /> <i>(oui)</i></td></tr>";

		print "<tr><td align='right'><font class=T2> Affiche les photos des ".INTITULEELEVES." : </font> </td><td>";
	        print "<input type=checkbox name='affphotoeleve' value='oui' $checkaffphotoeleve /> <i>(oui)</i></td></tr>";



      		$datap=config_param_visu("hauteurphoto");
        	$hauteurphoto=$datap[0][0];
        	$datap=config_param_visu("largeurphoto");
        	$largeurphoto=$datap[0][0];

		print "<tr><td align='right'><font class='T2'>Taille Photo des ".INTITULEELEVES." : </font></td><td>";
        	print "<input type=text name='largeurphoto' size='2' value='$largeurphoto' > largeur (mm) / ";
        	print "<input type=text name='hauteurphoto' size='2' value='$hauteurphoto'> hauteur (mm) </td></tr>";

		$datap=config_param_visu("affTextDirPedago");
        	$affTextDirPedago=$datap[0][0];
        	$checkboxaffTextDirPedago="";
        	if ($affTextDirPedago == "oui") { $checkboxaffTextDirPedago='checked="checked"'; }

		print "<tr><td align='right'><font class='T2'>Supprimer le comnentaire \"Responsable pédagogique\" : </font></td><td>";
		print "<input type='checkbox' name='affTextDirPedago' value='oui' $checkboxaffTextDirPedago /> <i>(oui)</i></td></tr>";
		print "</table>";
	}

if ($typebull == "bull03UE") {
	print "<br>";
	
	print "<br><br><font class='T2'>Affichage du cadre préparation examen : </font> <input type='checkbox' name='affprepaexam' value='oui' /> <i>(oui)</i>";

	$datap=config_param_visu("affcommentaire");
	$affcommentaire=$datap[0][0];
	$checkboxaffcommentaire="";
	if ($affcommentaire == "oui") { $checkboxaffcommentaire='checked="checked"'; }

	$datap=config_param_visu("affnbabsmat");
	$affnbabsmat=$datap[0][0];
	$checkboxaffnbabsmat="";
	if ($affnbabsmat == "oui") { $checkboxaffnbabsmat='checked="checked"'; }

	$datap=config_param_visu("hauteurmatiere");
	$hauteurBull=$datap[0][0];
        if (trim($hauteurBull) == "") {
                $hauteurBull="8";
	}

	$datap=config_param_visu("affabsrecup");
        $affabsrecup=$datap[0][0];
        $checkboxaffabsrecup="";
        if ($affabsrecup == "oui") { $checkboxaffabsrecup='checked="checked"'; }
	

	$datap=config_param_visu("calculmoyenbrute");
	$calculmoyenbrute=$datap[0][0];
        if (trim($calculmoyenbrute) == "") {
                $checkboxcalculmoyenbrute="checked='checked'";
	}
	if ($calculmoyenbrute == "brute") $checkboxcalculmoyenbrute="checked='checked'";
	if ($calculmoyenbrute == "nonbrute") $checkboxcalculmoyennonbrute="checked='checked'";
		
	print "<br><br><font class='T2'>Affichage la colonne des commentaires : </font> <input type='checkbox' name='affcommentaire' value='oui' $checkboxaffcommentaire /> <i>(oui)</i>";
	print "<br><br><font class='T2'>Affichage le nbr d'absence aux matières : </font> <input type='checkbox' name='affnbabsmat' value='oui' $checkboxaffnbabsmat /> <i>(oui)</i>";
	print "<br><br><font class='T2'>Affichage des informations d'absentéismes  : </font> <input type='checkbox' name='affabsrecup' value='oui' $checkboxaffabsrecup /> <i>(oui)</i>";
	print "<br><br><font class='T2'>Moyenne générale  : </font>( Ensemble des notes <input type='radio' name='calculmoyenbrute' value='brute' $checkboxcalculmoyenbrute />) 
								      ( Moyennes des UE <input type='radio' name='calculmoyenbrute' value='nonbrute' $checkboxcalculmoyennonbrute />) 
		";

	print "<br><br><br><font class=T2> Hauteur des matières : </font> ";
        print "<input type=text name='hauteurmatiere' size='3' value='$hauteurBull' />";
}


if ($typebull == "bull02UE") {	
	print "<br><br><br><font class='T2'>Document d'attestation : </font> <input type='checkbox' name='affdocattestation' value='oui' /> <i>(oui)</i>";
}

if (($typebull == "bull01XL") || ($typebull == "bull02XL")) {
	print "<br>";
	$datap=config_param_visu("affmatieresansnote");
	$affmatieresansnote=$datap[0][0];
	$checkboxaffmatieresansnote="";
	if ($affmatieresansnote == "oui") { $checkboxaffmatieresansnote='checked="checked"'; }
	print "<br><br><font class='T2'>Afficher les matières sans notation : </font> <input type='checkbox' name='affmatieresansnote' value='oui' $checkboxaffmatieresansnote /> <i>(oui)</i>";

	$datap=config_param_visu("affmoyengeneralexls");
	$affmoyengeneralexls=$datap[0][0];
	$checkboxaffmoyengeneralexls="";
	if ($affmoyengeneralexls == "oui") { $checkboxaffmoyengeneralexls='checked="checked"'; }
	print "<br><br><font class='T2'>Afficher la moyenne générale : </font> <input type='checkbox' name='affmoyengeneralexls' value='oui' $checkboxaffmoyengeneralexls /> <i>(oui)</i>";
}

?>


	<?php if ($typebull == "bull03") { ?>
		<br><br><font class="T2">Affichage des photos <?php print INTITULEELEVE ?> : </font> <input type='checkbox' name='photoaff' value='oui'  /> <i>(oui)</i>
	<?php
	}
	
	if ($typebull == "bull03") {
		$datap=config_param_visu("retenuarras");
		$affretenuarras=$datap[0][0];
	?>
		<br><br><font class="T2">Affichage des retenues : </font> <input type='checkbox' name='affretenu' value="oui" <?php print $affretenuarras?>  /> 	

		<?php
		$datap=config_param_visu("affichageMinMax");
		$affichageMinMax=$datap[0][0];
		if ($affichageMinMax == "oui") $affichageMinMax="checked='checked'"; 
	?>
		<br><br><font class="T2">Affichage moyenne max,min,classe : </font> <input type='checkbox' name='affichageMinMax' value="oui" <?php print $affichageMinMax ?>  /> <i>(oui)</i>

<?php
		if (($_POST["saisie_trimestre"] == "trimestre2") || ($_POST['saisie_trimestre'] == "trimestre3"))  {
			$datap=config_param_visu("affichageTM1");
			$affichageTM1=$datap[0][0];
			if ($affichageTM1 == "oui") $affichageTM1="checked='checked'"; 
		?>
			<br><br><font class="T2">Affichage trimestre précédent : </font> <input type='checkbox' name='affichageTM1' value="oui" <?php print $affichageTM1 ?>  /> <i>(oui)</i>
		<?php } ?>
		


<?php
}
?>

<?php if ($_SESSION["membre"] == "menupersonnel")  { ?> 
	<br><br><br>
	<table border=0 align=center width="250"><tr><td align="center">
        <script language=JavaScript>buttonMagicRetour("imprimer_trimestre.php<?php print $url ?>","_parent")</script>
        <?php if ($nbEleve > 0) { ?><script language=JavaScript>buttonMagicSubmit3("<?php print LANGBULL6 ?>","rien","onclick='this.value=\"<?php print LANGBT5 ?>\";AfficheAttente()'");</script>&nbsp;&nbsp;
	<?php }else{ print "<font id='color3' class='T2 shadow'>Aucun ".INTITULEELEVE." pour cette classe en $anneeScolaire</font>"; } ?>
	</td></tr></table>
	<br><br>
<?php } ?>

<?php if ($_SESSION["membre"] == "menuprof") { ?> 
	<br><br><br>
	<table border=0 align=center width="250"><tr><td align="center">
        <script language=JavaScript>buttonMagicRetour("imprimer_trimestre.php<?php print $url ?>","_parent")</script>
        <?php if ($nbEleve > 0) { ?> <script language=JavaScript>buttonMagicSubmit3("<?php print LANGBULL6 ?>","rien","onclick='this.value=\"<?php print LANGBT5 ?>\";AfficheAttente()'");</script>&nbsp;&nbsp;
	<?php }else{ print "<font id='color3' class='T2 shadow'>Aucun ".INTITULEELEVE." pour cette classe en $anneeScolaire</font>"; } ?>
	</td></tr></table>
	<br><br>
<?php } ?>

<?php if ($_SESSION["membre"] == "menuscolaire") { ?> 
	<br><br><br>
	<table border=0 align=center width="250"><tr><td align="center">
        <script language=JavaScript>buttonMagicRetour("imprimer_trimestre.php<?php print $url ?>","_parent")</script>
        <?php if ($nbEleve > 0) { ?> <script language=JavaScript>buttonMagicSubmit3("<?php print LANGBULL6 ?>","rien","onclick='this.value=\"<?php print LANGBT5 ?>\";AfficheAttente()'");</script>&nbsp;&nbsp;
	<?php }else{ print "<font id='color3' class='T2 shadow'>Aucun ".INTITULEELEVE." pour cette classe en $anneeScolaire</font>"; } ?>
	</td></tr></table>
	
	<br><br>
	<?php if (($typebull == "bull01") ||  ($typebull == "bull01b") || ("bull0101" == $typebull) || ($typebull == "bull01001") || ($typebull == "bull0101a") || ($typebull == "bull0101b") || ($typebull == "bull01019") )  { print "</td></tr></table>"; } ?>

<?php } ?>

<?php
print "</center>";
if (($_SESSION["membre"] != "menuadmin") && ($_SESSION["membre"] != "menupersonnel") && ($typebull != "bull01015") && ($typebull != "bull03") && ($typebull != "bull0209"))  { print "<div style='display:none' >"; }

if (($typebull == "bull01022") || ($typebull == "bull0102") ||  ($typebull == "bull0103")) {
?>
	<br><br><font class="T2">Président du conseil : </font> <input type='text' name='nomdivision' size='30' /> 	
<?php
}
?>

<?php
if (($typebull == "bull0305b") || ($typebull == "bull0305b-nv") || ($typebull == "bull0305b-2") || ($typebull == "bull0305c") || ($typebull == "bull0305d") )  {
	$datap=config_param_visu("ismappadmis");
	$ismappadmis=$datap[0][0];
	$checkboxadmis="";
	if ($ismappadmis == 1) { $checkboxadmis='checked="checked"'; }
?>
	<br><br><center><font class="T2">Afficher l'encadrer Admis-e / Ajourn&eacute;-e : </font> <input type='checkbox' name='ismappadmis' value='1' <?php print $checkboxadmis ?>  /></center>
<?php } ?>

<?php
if ($typebull == "bull0110")  {
?>
	<br><br><font class="T2">Affichage note vie scolaire : </font> <input type='checkbox' name='affnoteviescolaire' value='oui' checked='checked' /> <i>(oui)</i>	
<?php
}
?>

<?php
if (($typebull == "bull0305") || ($typebull == "bull0305a")) {
	$datap=config_param_visu("ptenmoinsabs");
	$ptenmoinsabs=$datap[0][0];
	if ($ptenmoinsabs == "") { $ptenmoinsabs=0; }
	$datap=config_param_visu("nbrtdaabs");
	$nbrtdaabs=$datap[0][0];
	if ($nbrtdaabs == "") { $nbrtdaabs=0; }
	$datap=config_param_visu("ptenmoinsabsjusti");
	$ptenmoinsabsjusti=$datap[0][0];
	if ($ptenmoinsabsjusti == "") { $ptenmoinsabsjusti=0; }
	$datap=config_param_visu("nbrabs");
	$nbrabs=$datap[0][0];
	if ($nbrabs == "") { $nbrabs=0; }
	$datap=config_param_visu("motif_polspe_ip");
	$motif_polspe_ip=$datap[0][0];
	if ($motif_polspe_ip == "") { $motif_polspe_ip="<option value='' id='select0' ></option>"; }
	$motif_polspe_ipVisu="<option value='$motif_polspe_ip' id='select0' >$motif_polspe_ip</option>";
	$datap=config_param_visu("ismappdatedebut");
	$ismappdatedebut=$datap[0][0];
	$datap=config_param_visu("ismappdatefin");
	$ismappdatefin=$datap[0][0];
	$datap=config_param_visu("ismappects");
	$ismappects=$datap[0][0];


?>
	<br><br><br><font class="T2">Nbr de point en moins par abs. non justifiée : </font> <input type='text' size=2 name='ptenmoinsabs' value='<?php print $ptenmoinsabs ?>'  />

	<br><br><br><font class="T2">Nbr de retards équivalant à une abs. non justifiée : </font> <input type='text' size=2 name='nbrtdaabs' value='<?php print $nbrtdaabs ?>'  />

	<br><br><br><font class="T2">Type d'abs. justifiée lié au POLSPE-IP : </font> <select name="motif_polspe_ip" >
<?php print $motif_polspe_ipVisu ?>
<?php affSelecMotif() ?>
</select>

	<br><br><br><font class="T2">Nbr d'abs justifiée autorisées au POLSPE-IP : </font> <input type='text' size=2 name='nbrabs' value='<?php print $nbrabs ?>'  />

	<br><br><br><font class="T2">Nbr de point en moins par dépassement d'abs. justifiée : </font> <input type='text' size=2 name='ptenmoinsabsjusti' value='<?php print $ptenmoinsabsjusti ?>'  />
<?php
$checkboxects="";
if ($ismappects == 1) { $checkboxects='checked="checked"'; }
?>
	<br><br><br><font class="T2">Afficher la colonne ECTS : </font> <input type='checkbox' name='ects' value='1' <?php print $checkboxects ?>  />
<?php
	if ($typebull == "bull0305a" ) {  ?>
		<br><br><br><font class="T2">Début de l'année : </font> 
		<input type="text" value="<?php print $ismappdatedebut ?>" name="saisie_date_debut" TYPE="text" size=13  class=bouton2 readonly>
		<?php
		include_once("librairie_php/calendar.php");
		calendar("id1","document.formulaire5.saisie_date_debut",$_SESSION["langue"],"0");
		?>
		<br><br><font class='T2'><?php print "Fin de l'année" ?> : </font>
		<input type="text" value="<?php print $ismappdatefin ?>" name="saisie_date_fin" TYPE="text" size=13 class=bouton2 readonly>
		<?php
	        calendar("id2","document.formulaire5.saisie_date_fin",$_SESSION["langue"],"0");
	}

}
if ( (($typebull == "bull0101") || ($typebull == "bull01001") || ($typebull == "bull0101a") || ($typebull == "bull0101b") || ($typebull == "bull01017") ||  ($typebull == "bull01018") ||  ($typebull == "bull01016" ) || ($typebull == "bull01011" ) ||  ($typebull == "bull01012" ) ||  ($typebull == "bull01013" ) || ($typebull == "bull0109") || ($typebull == "bull0109-2")  || ($typebull == "bull01") ||  ($typebull == "bull01b") || ($typebull == "bull01019")  || ($typebull == "bull800") || ($typebull == "bull700") || ($typebull == "bull03")  ||  ($typebull == "bull01015" )|| ($typebull == "bull01015-2")  ) && (file_exists("./data/image_pers/logo_signature.jpg"))) {
?>


<br><br><center><font class=T2>Insertion signature directeur : </font> <input type="checkbox" name="ajsignature" value="oui" /></center>
<?php } ?>

<?php if ($typebull == "bull01014") { ?>
		<br><center><font class=T2>Insertion signature directeur : </font> <input type="checkbox" name="ajsignature" value="oui" /></center>
<?php }  ?>



<br />
<?php
$choixtrimestre=$_POST["saisie_trimestre"];
?>
<input type=hidden name='saisie_classe' value="<?php print $_POST["saisie_classe"];?>" >
<input type=hidden name='saisie_trimestre' value="<?php print $choixtrimestre ;?>" >
<input type=hidden name='annee_scolaire' value="<?php print $_POST["anneeScolaire"];?>" >
<input type=hidden name='NoteUsa' value="<?php print $_POST["NoteUsa"];?>" >
<input type=hidden name='typetrisem' value="<?php print $_POST["typetrisem"];?>" >
<?php 
$url="";
if ($_SESSION["membre"] == "menuprof") {
	$url="?sClasseGrp=".$_POST["saisie_classe"];
}

if ($typebull == "bull10") {
	print "<table align=center ><tr>";
	print "<td><input type='radio' name='typetitre' value='primaire' checked='checked' /></td><td><font class=T2> Primaire </font></td> ";
	print "</tr><tr>";
	print "<td><input type='radio' name='typetitre' value='maternelle' /></td><td><font class=T2> Maternelle </font> </td>";
	print "</tr></table><br>";

}

if (($typebull == "bull0208") || ($typebull == "bull01011")  ||  ($typebull == "bull01016") ||  ($typebull == "bull01018")  ||  ($typebull == "bull01012" ) ||  ($typebull == "bull01013" ) ||  ($typebull == "bull01014" ) || ($typebull == "bull800") || ($typebull == "bull01015" ) || ($typebull == "bull05") ) {
	$hauteurBull="10";
	if ($typebull == "bull01011") { $hauteurBull="8"; }
	if ($typebull == "bull01016") { $hauteurBull="8"; }
	if ($typebull == "bull01018") { $hauteurBull="8"; }
	if ($typebull == "bull01012") { $hauteurBull="8"; }
	if ($typebull == "bull01013") { $hauteurBull="8"; }
	if ($typebull == "bull01015") { $hauteurBull="8"; }
	if ($typebull == "bull800")   { $hauteurBull="8"; }
	if ($typebull == "bull05")   { $hauteurBull="13"; }
	$hauteurorigine=$hauteurBull;
	$datap=config_param_visu("hauteurMatBull208");
	$hauteurBull=$datap[0][0];
	if (trim($hauteurBull) == "") { $hauteurBull=$hauteurorigine; }
	print "<table align=center ><tr>";
	print "<td><font class=T2> Hauteur des matières : </font></td> ";
	print "<td><input type=text name='hauteurmatiere' size='3' value='$hauteurBull' /> (valeur d'origine : $hauteurorigine)</td>";
	print "</tr></table><br>";
}

 if (($typebull == "bull0101") || ($typebull == "bull01001") || ($typebull == "bull0101a") || ($typebull == "bull0101b")) {
 	$datap=config_param_visu("hauteurMatiere");
	$hauteurBull=$datap[0][0];
	if (trim($hauteurBull) == "") {
		$hauteurBull="10";
		if (($typebull == "bull0101") || ($typebull == "bull01001") || ($typebull == "bull0101a") || ($typebull == "bull0101b")) { $hauteurBull="8"; }
	}
	$datap=config_param_visu("nbmaxMatierePageUne");
	$nbmaxMatierePageUne=$datap[0][0];

        $datap=config_param_visu("infoMatiere");
        $infoMatiere=$datap[0][0];
        if ($infoMatiere == "oui") $checkedinfoMatiere="checked='checked'";

	print "<table align=center >";
	print "<tr>";
	print "<td><font class=T2> Hauteur des matières : </font></td> ";
	print "<td><input type=text name='hauteurMatiere' size='3' value='$hauteurBull' /></td>";
	print "</tr>";
	print "<tr>";
	print "<td><font class=T2> Nombre de matière par page : </font></td> ";
	print "<td><input type=text name='nbmaxMatierePageUne' size='3' value='$nbmaxMatierePageUne' /></td>";
	print "</tr>";
        print "<tr>";
        print "<td><font class=T2> Afficher le descriptif des matières : </font></td> ";
        print "<td><input type='checkbox' name='infoMatiere' value='oui' $checkedinfoMatiere /> (oui) </td>";
        print "</tr>";	
	print "</table><br>";
}

if (($typebull == "bull01014") || ($typebull == "bull01011") || ($typebull == "bull800") || ($typebull == "bull01016") || ($typebull == "bull01018") ) {
	$datap=config_param_visu("hauteurConseilBull");
	$hauteurConseilBull=$datap[0][0];
	if (trim($hauteurConseilBull) == "") { 
		$hauteurConseilBull="15"; 
		if ($typebull == "bull800")   { $hauteurConseilBull="30"; }
	}
	print "<table align=center ><tr>";
	print "<td><font class=T2> Hauteur des avis de conseils  : </font></td> ";
	print "<td><input type=text name='hauteurConseilBull' size='3' value='$hauteurConseilBull' /></td>";
	print "</tr></table><br>";
}




if (($typebull == "bull0209") || ($typebull == "bull01133") || ($typebull == "bull01133a") || ($typebull == "bull0210a") || ($typebull == "bull0210b") || ($typebull == "bull0210") || ($typebull == "bull0211") || ($typebull == "bull0211a" ) ||  ($typebull == "bull01144")  ) {
	$datap=config_param_visu("hauteurMatBull209");
	$hauteurBull=$datap[0][0];
	if ((trim($hauteurBull) == "") && ($typebull == "bull0209")) { $hauteurBull="8"; }
	if ((trim($hauteurBull) == "") && ($typebull == "bull0210")) { $hauteurBull="8"; }
	if ((trim($hauteurBull) == "") && ($typebull == "bull0210a")) { $hauteurBull="8"; }
	if ((trim($hauteurBull) == "") && ($typebull == "bull0210b")) { $hauteurBull="8"; }
	if ((trim($hauteurBull) == "") && ($typebull == "bull0211")) { $hauteurBull="8"; }
	if ((trim($hauteurBull) == "") && ($typebull == "bull0211a")) { $hauteurBull="8"; }
	if ((trim($hauteurBull) == "") && ($typebull == "bull01133")) { $hauteurBull="9"; }
	if ((trim($hauteurBull) == "") && ($typebull == "bull01133a")) { $hauteurBull="9"; }
	if ((trim($hauteurBull) == "") && ($typebull == "bull01144")) { $hauteurBull="9"; }
	print "<table align=center ><tr>";
	print "<td><font class=T2> Hauteur des matières : </font></td> ";
	print "<td><input type=text name='hauteurmatiere' size='3' value='$hauteurBull' /></td>";
	print "</tr></table><br>";
}


if (($typebull == "bull01133") || ($typebull == "bull01133a")) {
	$datap=config_param_visu("PoliceMatBull01133");
	$policeBull=$datap[0][0];
	if ((trim($policeBull) == "") && ($typebull == "bull01133")) { $policeBull="9"; }
	if ((trim($policeBull) == "") && ($typebull == "bull01133a")) { $policeBull="9"; }
	print "<table align=center ><tr>";
	print "<td><font class=T2> Police de caractère : </font></td> ";
	print "<td><input type=text name='policecaractere' size='3' value='$policeBull' /></td>";
	print "</tr></table><br>";
}

if (($typebull == "bull01133") || ($typebull == "bull01014") || ($typebull == "bull01133a") ) {
	$datap=config_param_visu("photobulleleve");
	$photobulleleve=$datap[0][0];
	if (trim($photobulleleve) == "") { 
		$affichephotobulleleve=""; 
	}else{ 
		$affichephotobulleleve="checked='checked'"; 
	}
	print "<table align=center ><tr>";
	print "<td align='right'><font class=T2> Affichage des photos des ".INTITULEELEVES."  : </font></td> ";
	print "<td><input type='checkbox' name='photobulleleve' value='oui' $affichephotobulleleve /> <i>(oui)</i></td>";

	if ($typebull == "bull01133a") {
		$datap=config_param_visu("affrang");
	        $afficheRang=$datap[0][0];
	        if (trim($afficheRang) == "") {
	                $afficherang="";
        	}else{
                	$afficherang="checked='checked'";
	        }
		$intituleeleve=INTITULEELEVE;
		print "<tr><td height='20'></td></tr>";
		print "<tr>";
		print "<td align='right'><font class=T2> Affichage du rang de l'$intituleeleve : </font></td> ";
		print "<td><input type='checkbox' name='affrang' value='oui' $afficherang /> <i>(oui)</i></td>";
		print "</tr>";
	}

	print "</tr></table><br>";
}


if (($typebull == "bull01") ||  ($typebull == "bull01b") || ($typebull == "bull01019") || ($typebull == "bull800") || ($typebull == "bull0101") || ($typebull == "bull01001") || ($typebull == "bull0101a") || ($typebull == "bull0101b") || ($typebull == "bulllprb") || ($typebull == "bull01011") ||  ($typebull == "bull01012" ) ||  ($typebull == "bull01013" ) ||  ($typebull == "bull01014" ) ||  ($typebull == "bull01015" ) || ($typebull == "bull01015-2") || ($typebull == "bull700") || ($typebull == "bull01016") || ($typebull == "bull01018") ) {
	$datap=config_param_visu("hauteurphoto");
	$hauteurphoto=$datap[0][0];
	$datap=config_param_visu("largeurphoto");
	$largeurphoto=$datap[0][0];
	$datap=config_param_visu("hauteurlogo");
	$hauteurlogo=$datap[0][0];
	$datap=config_param_visu("largeurlogo");
	$largeurlogo=$datap[0][0];
	$datap=config_param_visu("avecexamenblanc");
	$avecexamenblanc=$datap[0][0];
	$datap=config_param_visu("affichemoyengeneral");
	$affichemoyengeneral=$datap[0][0];
	$datap=config_param_visu("affichematierecoefzero");
	$affichematierecoefzero=$datap[0][0];
	$datap=config_param_visu("affichesignatureprofp");
	$affichesignatureprofp=$datap[0][0];
	$datap=config_param_visu("affichecommentaireprofp");
	$affichecommentaireprofp=$datap[0][0];
	$datap=config_param_visu("affichenomprofp");
	$affichenomprofp=$datap[0][0];
	$datap=config_param_visu("suppmentionpedago");
	$suppmentionpedago=$datap[0][0];
	

	if ($suppmentionpedago == "oui") { $suppmentionpedago="checked='checked'"; }else { $suppmentionpedago=""; }
	if ($avecexamenblanc == "oui") { $avecexamenblanc="checked='checked'"; }else { $avecexamenblanc=""; }
	if ($affichemoyengeneral == "oui") { $affichemoyengeneral="checked='checked'"; }else { $affichemoyengeneral=""; }
	if ($affichematierecoefzero == "oui") { $affichematierecoefzero="checked='checked'"; }else { $affichematierecoefzero=""; }
	if (trim($hauteurphoto) == "") {
		$hauteurphoto=16.3;
		$largeurphoto=10.8;
	}
	if (trim($hauteurlogo) == "") {
		$largeurlogo=25;
		$hauteurlogo=25;
	}
	$datap=config_param_visu("moyensousmatiere");
	$moyensousmatiere=$datap[0][0];
	$moyensousmatiereoui="";$moyensousmatierenon="";
	if ($moyensousmatiere == "non") { $moyensousmatierenon="checked='checked'"; }else { $moyensousmatiereoui="checked='checked'"; }
	print "<center><table><tr><td align=right><font class='T2'>Taille Logo : </font></td>";
	print "<td><input type=text name='largeurlogo' size='2' value='$largeurlogo' > largeur (mm) /  ";
	print "<input type=text name='hauteurlogo' size='2' value='$hauteurlogo' > hauteur (mm) <td></tr>";
	print "<tr><td align=right><font class='T2'>Taille Photo ".INTITULEELEVES." : </font></td>";
	print "<td><input type=text name='largeurphoto' size='2' value='$largeurphoto' > largeur (mm) /  ";
	print "<input type=text name='hauteurphoto' size='2' value='$hauteurphoto'> hauteur (mm) </td></tr>";
	if (($typebull == "bull0101a") || ($typebull == "bull0101b")) {  $avecexamenblanc="checked='checked'"; }
	print "<td colspan='2' align=right > <font class='T2'>Prise en compte de note d'examen blanc :</font> <input type=checkbox name='avecexamenblanc' value='oui' $avecexamenblanc > (oui) </td></tr>";
	if ( ($typebull == "bull0101") || ($typebull == "bull01001") || ($typebull == "bull0101a") || ($typebull == "bull0101b") || ($typebull == "bull01017") || ($typebull == "bull01011") ||  ($typebull == "bull01012" ) ||  ($typebull == "bull01013" ) ||  ($typebull == "bull01014" ) ||  ($typebull == "bull01015" ) || ($typebull == "bull01015-2") || ($typebull == "bull01016") || ($typebull == "bull01018")  )  {
		print "<td colspan='2' align=right> <font class='T2'>Moyenne générales via les sous-matières  :</font> <input type=radio name='moyensousmatiere' value='oui' $moyensousmatiereoui > (oui) </td></tr>";
		print "<td colspan='2' align=right>  <font class='T2'>Moyenne générales via les matières  :</font> <input type=radio name='moyensousmatiere' value='non' $moyensousmatierenon > (oui) </td></tr>";
	}
	if (($typebull == "bull0101a") || ($typebull == "bull0101b"))  { $affichemoyengeneral="checked='checked'"; $disabledmoyengeneral="readonly='readonly'" ; $cache=" style='display:none' "; }
	print "<td $cache colspan='2' align='right' > <font class='T2'>Afficher la moyenne générale :</font> <input type=checkbox name='affichemoyengeneral' value='oui' $affichemoyengeneral $disabledmoyengeneral > (oui) </td></tr>";

	if ($typebull == "bull01") {
		print "<td colspan='2' align='right' ><font class=T2>Supprimer la mention \"Appréciation globale de l'équipe pédagogique\" :</font> <input type=checkbox name='suppmentionpedago' value='oui' $suppmentionpedago > (oui) </td></tr>";	
	} 
	
        if ($typebull == "bull01b") {
                print "<td colspan='2' align='right' ><font class=T2>Afficher le nom du professeur Principal : </font> <input type='checkbox' name='affichenomprofp'  $affichenomprofp value='oui' /> (oui) </></tr>";
        }


	if ($typebull == "bull01015") {
                print "<td colspan='2' align='right' ><font class=T2>Insertion signature Prof. Prin. : </font> <input type='checkbox' name='affichesignatureprofp'  $affichesignatureprofp value='oui' /> (oui) </></tr>";
                print "<td colspan='2' align='right' ><font class=T2>Commentaire du Prof. Prin. : </font> <input type='checkbox' name='affichecommentaireprofp'  $affichecommentaireprofp value='oui' /> (oui) </></tr>";
                $datap=config_param_visu("affichedistinction");
                $affichedistinction=($datap[0][0] == "oui") ? "checked='checked'" : "";
                $datap=config_param_visu("affichevisascolaire");
                $affichevisascolaire=($datap[0][0] == "oui") ? "checked='checked'" : "";
                print "<td colspan='2' align='right' ><font class=T2>Affichage des distinctions : </font> <input type='checkbox' name='affichedistinction'  $affichedistinction value='oui' /> (oui) </></tr>";
                print "<td colspan='2' align='right' ><font class=T2>Affichage du visa scolaire : </font> <input type='checkbox' name='affichevisascolaire'  $affichevisascolaire value='oui' /> (oui) </></tr>";
	} 
	

	if ( ($typebull == "bull0101") || ($typebull == "bull01001") || ($typebull == "bull0101a") || ($typebull == "bull0101b") || ($typebull == "bull01017") ||  ($typebull == "bull01") ||  ($typebull == "bull01b") || ($typebull == "bull01019")  || ($typebull == "bull800")  ||  ($typebull == "bull01015" ) || ($typebull == "bull01015-2") || ($typebull == "bull700")  ) {
		print "<td colspan='2' align=right > <font class='T2'>Afficher les matières coef à 0 (zéro) :</font> <input type=checkbox name='affichematierecoefzero' value='oui' $affichematierecoefzero > (oui) </td></tr>";
		$datap=config_param_visu("abssconet");
		$abssconet=$datap[0][0];
		if (trim($abssconet) == "oui") { $abssconet="checked='checked'"; }
		print "<tr><td  colspan='2' align=right ><font class=T2>Prise en charge abs/rtd SIECLE :</font>";
		print " <input type='checkbox' name='abssconet'  value='oui' $abssconet /> (oui) </td></tr>";
	
		if (($typebull == "bull01") ||  ($typebull == "bull01b"))  {
			$datap=config_param_visu("afficherang");
			$afficherang=$datap[0][0];
			if (trim($afficherang) == "oui") { $afficherang="checked='checked'"; }
			print "<tr><td  colspan='2' align=right ><font class=T2>Afficher la colonne rang :</font>";
			print " <input type='checkbox' name='afficherang'  value='oui' $afficherang /> (oui) </td></tr>";

			$datap=config_param_visu("npAfficheSousMatiere");
                        $npAfficheSousMatiere=$datap[0][0];
                        if (trim($npAfficheSousMatiere) == "oui") { $npAfficheSousMatiere="checked='checked'"; }
                        print "<tr><td  colspan='2' align=right ><font class=T2>Ne pas afficher pas les sous-matières :</font>";
                        print " <input type='checkbox' name='npAfficheSousMatiere'  value='oui' $npAfficheSousMatiere /> (oui) </td></tr>";

			$datap=config_param_visu("npAfficheCoef");
                        $npAfficheCoef=$datap[0][0];
                        if (trim($npAfficheCoef) == "oui") { $npAfficheCoef="checked='checked'"; }
                        print "<tr><td  colspan='2' align=right ><font class=T2>Ne pas afficher pas la colonne coef. :</font>";
                        print " <input type='checkbox' name='npAfficheCoef'  value='oui' $npAfficheCoef /> (oui) </td></tr>";

                        $datap=config_param_visu("hauteurMatiere");
                        $hauteurMatiere=$datap[0][0];
			if (trim($hauteurBull) == "") { $hauteurBull="8"; } 
                        print "<tr><td  colspan='2' align=right ><font class=T2>Hauteur des lignes \"matières\" :</font>";
                        print " &nbsp;<input type='text' size=2 name='hauteurMatiere'  value='$hauteurBull'  />&nbsp;&nbsp;&nbsp;</td></tr>";

			$datap=config_param_visu("coef100");
			$coef100=$datap[0][0];
			if (trim($coef100) == "oui") { $coef100="checked='checked'"; }
                        print "<tr><td  colspan='2' align=right ><font class=T2>Coefficient sur 100 :</font>";
			print " <input type='checkbox' name='coef100'  value='oui' $coef100 /> (oui) </td></tr>";

			$datap=config_param_visu("notescolairegeneral");
			$notescolairegeneral=$datap[0][0];
			if (trim($notescolairegeneral) == "oui") { $notescolairegeneral="checked='checked'"; }
                        print "<tr><td  colspan='2' align=right ><font class=T2>Moy. Vie scolaire inclus dans moy. générale :</font>";
                        print " <input type='checkbox' name='noteviescolairedansmoyennegeneral'  value='oui' $notescolairegeneral /> (oui) </td></tr>";


			if ($typebull == "bull01") {
				$datap=config_param_visu("adressebulletin");
        	                $adressebulletinetudiant=$datap[0][0];
	                        if (trim($adressebulletinetudiant) == "etudiant") { $adressebulletinetudiant="checked='checked'"; }
                	        print "<tr><td  colspan='2' align=right ><font class=T2>Indiquer l'adresse de l'étudiant :</font>";
                        	print " <input type='radio' name='adressebulletin'  value='etudiant' $adressebulletinetudiant /> (oui) </td></tr>";

				$datap=config_param_visu("adressebulletin");
        	                $adressebulletinetudiant=$datap[0][0];
	                        if (trim($adressebulletinetudiant) == "parent") { $adressebulletinetudiant="checked='checked'"; }
                	        print "<tr><td  colspan='2' align=right ><font class=T2>Indiquer l'adresse du tuteur :</font>";
                        	print " <input type='radio' name='adressebulletin'  value='parent' $adressebulletinetudiant /> (oui) </td></tr>";

				$datap=config_param_visu("adressebulletin");
                                $adressebulletinetudiant=$datap[0][0];
                                if (trim($adressebulletinetudiant) == "") { $adressebulletinetudiant="checked='checked'"; }
                                print "<tr><td  colspan='2' align=right ><font class=T2>Indiquer aucune adresse :</font>";
                                print " <input type='radio' name='adressebulletin'  value='' $adressebulletinetudiant /> (oui) </td></tr>";
			}


		}
		print "<tr><td height='20'></td></tr>";
	}

}
if (($typebull == "bull0101") || ($typebull == "bull01001") || ($typebull == "bull0101a") || ($typebull == "bull0101b"))  {
	$datap=config_param_visu("bullregime");
	$bullregime=$datap[0][0];
	if (trim($bullregime) == "oui") { $bullregime="checked='checked'"; }
	print "<tr><td  colspan='2' align=right ><font class=T2> Afficher le régime :</font>";
	print " <input type='checkbox' name='bullregime'  value='oui' $bullregime /> (oui) </td></tr>";	


	if (($typebull == "bull0101a") || ($typebull == "bull0101b")) { 
		$datap=config_param_visu("notaffmoyclass");
	        $notaffmoyclass=$datap[0][0];
	        if (trim($notaffmoyclass) == "oui") { $notaffmoyclass="checked='checked'"; }
	        print "<tr><td  colspan='2' align=right ><font class=T2> Afficher la moyenne générale :</font>";
	        print " <input type='checkbox' name='notaffmoyclass'  value='oui' $notaffmoyclass /> (oui) </td></tr>";
	}


	$datap=config_param_visu("bullnumele");
	$bullnumele=$datap[0][0];
	if (trim($abssconet) == "oui") { $bullnumele="checked='checked'"; }
	print "<tr><td  colspan='2' align=right ><font class=T2> Afficher le numéro ".INTITULEELEVE." :</font>";
	print " <input type='checkbox' name='bullnumele'  value='oui' $bullnumele /> (oui) </td></tr>";	

	$datap=config_param_visu("bullprofp");
	$bullprofp=$datap[0][0];
	if (trim($bullprofp) == "oui") { $bullprofp="checked='checked'"; }
	print "<tr><td  colspan='2' align=right ><font class=T2> Afficher le professeur principal et commentaire :</font>";
	print " <input type='checkbox' name='bullprofp'  value='oui' $bullprofp /> (oui) </td></tr>";	

	$datap=config_param_visu("moyensurdix");
	$moyensurdix=$datap[0][0];
	if (trim($moyensurdix) == "oui") { $moyensurdix="checked='checked'"; }
	print "<tr><td  colspan='2' align=right ><font class=T2> Afficher les moyennes sur dix (xx/10) :</font>";
	print " <input type='checkbox' name='moyensurdix'  value='oui' $moyensurdix /> (oui) </td></tr>";

	if (MODNAMUR0 == "oui") {
		$datap=config_param_visu("noteviescolaire");
		$noteviecmp=$datap[0][0];
		if (trim($noteviecmp) != "non") { $noteviecmp="checked='checked'"; }
		if (($typebull == "bull0101a") || ($typebull == "bull0101b"))  { $noteviecmp="checked='checked'"; $disablednoteviecmp="readonly='readonly'" ; $cache=" style='display:none' "; } 
		print "<tr $cache ><td  colspan='2' align=right ><font class=T2> Afficher la note de vie scolaire :</font>";
		print " <input type='checkbox' name='noteviecmp'  value='oui' $noteviecmp $disablednoteviecmp  /> (oui) </td></tr>";	
	}


	$datap=config_param_visu("notaffmoyclass");
	$notaffmoyclass=$datap[0][0];
	if (trim($notaffmoyclass) == "oui") { $notaffmoyclass="checked='checked'"; }else{ $notaffmoyclass=""; }
	if (($typebull != "bull0101a") && ($typebull != "bull0101b"))  { 
		print "<tr $cache ><td  colspan='2' align=right ><font class=T2> Ne pas afficher la moyenne la moyenne de classe :</font>";
		print " <input type='checkbox' name='notaffmoyclass'  value='oui' $notaffmoyclass onClick=\"notaffminmaxA()\"  id='notaffmoyclass'  /> (oui) </td></tr>";
	}

	$datap=config_param_visu("notaffminmax");
        $notaffmoyminmax=$datap[0][0];
        if (trim($notaffmoyminmax) == "oui") { $notaffmoyminmax="checked='checked'"; }
        if (($typebull != "bull0101a") && ($typebull != "bull0101b"))  {
                print "<tr $cache ><td  colspan='2' align=right ><font class=T2> Ne pas afficher la moyenne la moyenne mini et maxi :</font>";
                print " <input type='checkbox' name='notaffminmax'  value='oui' $notaffmoyminmax id='notaffminmax' onClick=\"notaffminmaxB()\" /> (oui) </td></tr>";
        }



	$datap=config_param_visu("afficheRemplacant");
	$afficheRemplacant=$datap[0][0];
	if (trim($afficheRemplacant) == "oui") { $afficheRemplacantCheck="checked='checked'"; }
	print "<tr>";
	print "<td colspan='2' align=right ><font class=T2> Affichage des remplaçants : </font> ";
	print "<input type='checkbox' name='afficheRemplacant'  value='oui' $afficheRemplacantCheck /> <i>(oui)</i></td>";
	print "</tr>";

	print "<tr><td height='20'></td></tr>";
}

if (($typebull == "bull0108") || ($typebull == "bull0208") || ($typebull == "bull0209") || ($typebull == "bull0210a") ||  ($typebull == "bull0210b") || ($typebull == "bull0210") || ($typebull == "bull0211") || ($typebull == "bull0211a")) {
	$datap=config_param_visu("afficheRemplacant");
	$afficheRemplacant=$datap[0][0];
	if (trim($afficheRemplacant) == "oui") { $afficheRemplacantCheck="checked='checked'"; }
	print "<table align=center ><tr>";
	print "<td align='right'><font class=T2> Affichage des remplaçants : </font></td> ";
	print "<td><input type='checkbox' name='afficheRemplacant'  value='oui' $afficheRemplacantCheck /> <i>(oui)</i></td>";
	print "</tr></table><br>";
}

if (($typebull == "bull0210a") ||  ($typebull == "bull0210b") )  {
        $datap=config_param_visu("affichemoyengenerale");
        $affichemoyengenerale=$datap[0][0];
        if (trim($affichemoyengenerale) == "oui") { $affichemoyengenerale="checked='checked'"; }
        $datap=config_param_visu("afficheappreciation");
        $afficheappreciation=$datap[0][0];
        if (trim($afficheappreciation) == "oui") { $afficheappreciation="checked='checked'"; }
        $datap=config_param_visu("recupAdresseEtudiant");
        $recupAdresseEtudiant=$datap[0][0];
        if (trim($recupAdresseEtudiant) == "oui") { $recupAdresseEtudiant="checked='checked'"; }
	print "<table align=center ><tr>";
	if ($typebull == "bull0210a") {
        	print "<td><font class=T2> Bulletin de type Examen Blanc : </font></td> ";
	        print "<td><input type='checkbox' name='affichemoyengenerale'  value='oui' $affichemoyengenerale $readonlyaffichemoyengenerale /> <i>(oui)</i></td>";
	        print "<tr><td height='20'></td></tr>";
	}

	if ($typebull == "bull0210b") {
        	$datap=config_param_visu("affichecommentairedirection");
	        $affichecommentairedirection=$datap[0][0];
	        if (trim($affichecommentairedirection) == "oui") { $affichecommentairedirection="checked='checked'"; }
                print "<td align='right'><font class=T2> Affiche moyenne générale : </font></td> ";
                print "<td><input type='checkbox' name='affichemoyengenerale'  value='oui' $affichemoyengenerale $readonlyaffichemoyengenerale /> <i>(oui)</i></td>";
                print "<tr><td height='20'></td></tr>";
                print "<td align='right'><font class=T2> Affiche commentaire direction : </font></td> ";
                print "<td><input type='checkbox' name='affichecommentairedirection'  value='oui' $affichecommentairedirection $readonlyaffichecommentairedirection /> <i>(oui)</i></td>";
                print "<tr><td height='20'></td></tr>";
        }


        print "<tr>";
        print "<td align='right'><font class=T2> Afficher les appréciations (enseignants) : </font></td> ";
        print "<td><input type='checkbox' name='afficheappreciation'  value='oui' $afficheappreciation /> <i>(oui)</i></td>";
        print "</tr>";
        print "<tr><td height='20'></td></tr>";
        print "<tr>";
        print "<td align='right'><font class=T2> Adresse de l'étudiant  : </font></td> ";
        print "<td><input type='checkbox' name='recupAdresseEtudiant'  value='oui' $recupAdresseEtudiant /> <i>(oui)</i></td>";
        print "</tr>";
        print "</table><br>";
}

if ($typebull == "bull0208") {
	$datap=config_param_visu("uneseuledecimale");
	$afficheuneseuledecimale=$datap[0][0];
	if (trim($afficheuneseuledecimale) == "oui") { $afficheuneseuledecimale="checked='checked'"; }
	print "<table align=center ><tr>";
	print "<td><font class=T2> Notation à 1 seule décimale : </font></td> ";
	print "<td><input type='checkbox' name='uneseuledecimale'  value='oui' $afficheuneseuledecimale /> <i>(oui)</i></td>";
	print "</tr></table><br>";
}
 
if (($typebull == "bull0209") || ($typebull == "bull0210a") || ($typebull == "bull0210b")  || ($typebull == "bull0210") || ($typebull == "bull0211") || ($typebull == "bull0211a") ) {
	$datap=config_param_visu("abssconet");
	$abssconet=$datap[0][0];
	if (trim($abssconet) == "oui") { $abssconet="checked='checked'"; }
	print "<table align=center ><tr>";
	print "<td><font class=T2> Prise en charge abs/rtd SIECLE : </font></td> ";
	print "<td><input type='checkbox' name='abssconet'  value='oui' $abssconet /> <i>(oui)</i></td>";
	print "</tr></table><br>";
}

?>
</i><br>
<?php 
// CIE FORMATION
// -------------------------------------------------------------------------------------------------
if ($typebull == "bull408" || $typebull == "bull410") { ?>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<font class="T2">Indiquez la date d'édition du relevé : </font> 
		<input type='text' name='date_edition' size='12' />
		<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <font class=T1>(mois année ou JJ/MM/AAAA)</font><br><br><br>
<?php 
}

if ($typebull == "bull410") {
?>
    	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	    <font class="T2">Indiquez le titre du relevé : </font><input type='text' name='titre_releve' size='30' /><br><br><br>
<?php
}
// -------------------------------------------------------------------------------------------------
?>



<?php
// VATEL
if (($typebull == "bul9999") ||  ($typebull == "bul9999en") || ($typebull == "bul9999a") ) {
        $datap=config_param_visu("appliPratique");
        $appliPratique=$datap[0][0];
        if (trim($appliPratique) == "oui") { $appliPratique="checked='checked'"; }
        print "<table align='center' ><tr><td align='right' ><font class='T2'>Appliquer le r&eacute;sultat de la mati&egrave;re \"Application Pratiques\" au partiel : </font></td>";
        print "<td valign='bottom'><input type='checkbox' $appliPratique name='appliPratique' value='oui' /> (<i>oui</i>)</td></tr>";
	print "<tr><td align='right' ><font class='T2'>Afficher l'information \"ECTS ACQUIS\" : </font></td>";
        print "<td valign='bottom'><input type='checkbox' name='afficheECTSACQUIS' value='oui' /> (<i>oui</i>)</td></tr>";
	
	if ($typebull != "bul9999a") { 
        	print "<tr><td align='right'><font class='T2'>Autoriser le saut de page au sein du bulletin : </font></td>";
        	print "<td valign='bottom'><input type='checkbox' name='sautPage' value='oui' /> (<i>oui</i>)</td></tr>";
	}

        print "</table><br><br>";
} 





?>

<?php if (($_SESSION["membre"] != "menuadmin") && ($typebull != "bull03")) { print "</div><br><br><br>"; } ?>



<?php if (($typebull == "bull01015") || ($typebull == "bull01015-2"))    { ?> 
	<table border=0 align=center width="250"><tr><td align="center">
	<script language=JavaScript>buttonMagicRetour("imprimer_trimestre.php<?php print $url ?>","_parent")</script>
	<?php if ($nbEleve > 0) { ?> <script language=JavaScript>buttonMagicSubmit3("<?php print LANGBULL6 ?>","rien","onclick='this.value=\"<?php print LANGBT5 ?>\";AfficheAttente()'");</script>&nbsp;&nbsp;
	<?php }else{ print "<font id='color3' class='T2 shadow'>Aucun ".INTITULEELEVE." pour cette classe en $anneeScolaire</font>"; } ?>
	</td></tr></table><br /><br />

<?php if ($_SESSION["membre"] != "menuadmin") { print "<div style='display:none' >"; } ?>

<?php
	print "<ul><font class=T2>Configuration groupement de matière : <input type=button onclick=\"open('bulletin_construction01015bis.php','','width=850,height=400')\" value='Configurer'  STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\" /></font></ul>";
	print "<br><br>";
	$idliste="";
	$data=aff_grp_bull_leap("bulletinLeap_1",$_POST["saisie_classe"]);
	$idliste=$data[0][1];
	$idliste=preg_replace("/[{}]/",'',$idliste);
	$nomdugroupe=$data[0][2];
	if ($nomdugroupe != "") {
		$tabens_1=explode(",",$idliste);
		print "<ul><font class=T2>Nom du groupe Matière : ".utf8_decode($nomdugroupe)."</font><br /><br />";
		foreach ($tabens_1 as $key=>$value) {
			print "- ".chercheMatiereNom($value)."<br />";
		}
	}
	print "<br><br>";
	$idliste="";
	$data=aff_grp_bull_leap("bulletinLeap_2",$_POST["saisie_classe"]);
	$idliste=$data[0][1];
	$idliste=preg_replace("/[{}]/",'',$idliste);
	$nomdugroupe=$data[0][2];
	if ($nomdugroupe != "") {
		$tabens_1=explode(",",$idliste);
		print "<font class=T2>Nom du groupe Matière : ".utf8_decode($nomdugroupe)."</font><br /><br />";
		foreach ($tabens_1 as $key=>$value) {
			print "- ".chercheMatiereNom($value)."<br />";
		}
	}
	print "<br><br>";
	$idliste="";
	$data=aff_grp_bull_leap("bulletinLeap_3",$_POST["saisie_classe"]);
	$idliste=$data[0][1];
	$idliste=preg_replace("/[{}]/",'',$idliste);
	$nomdugroupe=$data[0][2];
	if ($nomdugroupe != "") {
		$tabens_1=explode(",",$idliste);
		print "<font class=T2>Nom du groupe Matière : ".utf8_decode($nomdugroupe)."</font><br /><br />";
		foreach ($tabens_1 as $key=>$value) {
			print "- ".chercheMatiereNom($value)."<br />";
		}
	}
	print "</ul><br><br>";
} 

?>

<?php if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel")) { ?>

<table border=0 align=center width="250"><tr><td align="center">
<script language=JavaScript>buttonMagicRetour("imprimer_trimestre.php<?php print $url ?>","_parent")</script>
<?php if ($nbEleve > 0) { ?><script language=JavaScript>buttonMagicSubmit3("<?php print LANGBULL6 ?>","rien","onclick='this.value=\"<?php print LANGBT5 ?>\";AfficheAttente()'");</script>&nbsp;&nbsp; 
<?php }else{ print "<font id='color3' class='T2 shadow'>Aucun ".INTITULEELEVE." pour cette classe en $anneeScolaire</font>"; } ?>
</td></tr></table>
</form>

<?php } ?>

<?php 
if (($typebull == "bull06") || ($typebull == "bull12") || ($typebull == "bull12ter")  || ($typebull == "bull113") || ($typebull == "bull502") ) { 
	if (isset($_POST["enrg"])) {
		enr_bull_bonifacio($_POST["list_matiere"],$_POST["saisie_recherche_final"]);
	}
?>
<hr>
<!-- ________________________________________________________________ -->
<br>
<form method=post name="formulaire" >
<ul>
<b>Pour Littéraire :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("litteraire");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}

?>

<br><br>

<b>Pour Scientifique :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("scientifique");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<hr>
<br>

<b>Pour arabe :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("arabe");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<br><br>

<b>Pour Français :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("français");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<br><br>

<b>Pour Social :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("social");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<br><br>

<b>Pour Technique :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("technique");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<hr>



<br><br>
<b>Enseignements généraux :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("ens_generaux");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<br><br>
<b>Secteur Professionnel :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("sect_prof");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<br><br>
<b>Spécialité Professionnelle :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("spec_prof");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<hr>

<br><br>
<b>Module 1 :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("module1");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<br><br>
<b>Module 2 :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("module2");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<br><br>
<b>Module 3 :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("module3");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<br><br>
<b>Module 4 :</b>
<?php 
$idliste=aff_grp_bull_bonifacio("module4");
$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
$liste_matiere=preg_replace("/\}/","",$liste_matiere);
if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>

<br><br>

</ul>
<table border=0 width=100%>
<tr><td width=33% align=center>
<select size=10 name="saisie_depart"  style="width:180px">
<?php 

$data=affMatiere();
for($i=0;$i<count($data);$i++)  {
	if ($data[$i][1] != "") {
		print "<option  id='select1' value='".$data[$i][0]."' title=\"".$data[$a][1]." ".preg_replace("/0$/","",$data[$a][2])."\" >".$data[$i][1]." ".preg_replace("/0$/","",$data[$i][2])."</option>";
        }
}
?>
</select>
</td>
<td width=15% align=center>
<input type="button" value="<?php print LANGCHER5?> >>>" onClick="calcul('+1');Deplacer(this.form.saisie_depart,this.form.saisie_recherche,'Choisissez un élèment')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
<br><br><br>
<input type="button" value="&lt;&lt;&lt; <?php print LANGCHER6 ?>" onClick="calcul('-1');Deplacer(this.form.saisie_recherche,this.form.saisie_depart,'Choisissez un élèment')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
</td>
<td width=33% align=center>
<select size=10 name="saisie_recherche" style="width:180px" multiple="multiple">
<OPTION>-------------</OPTION>
</select>
<script language="javascript">
// suppression de la ligne  mais on la garde pour la largeur
document.formulaire.saisie_recherche.options.length=0;
</script>
</td></tr></table>
<input type=hidden name="saisie_recherche_final">
<input type=hidden name="saisie_nb_recherche">
<ul>

<input type="submit" name="enrg" value="<?php print LANGENR ?>" class="BUTTON" onclick="prepEnvoi()" ><br /><br>
Liste de matière pour le groupe  : 
<select name='list_matiere'  onChange="ChoixListe(this.value);" >
<option value="" id="select0" ><?php print LANGCHOIX ?></option>
<optgroup label="Bulletin Bonifacio" >
<option id="select1" value="litteraire" >Littéraire</option>
<option id="select1" value="scientifique" >Scientifique</option>
<optgroup label="Bulletin Laghmani" >
<option id="select1" value="arabe" >Arabe</option>
<option id="select1" value="français" >Français</option>
<option id="select1" value="social" >Social</option>
<option id="select1" value="technique" >Technique</option>
<optgroup label="Bulletin MFREO" >
<option id="select1" value="ens_generaux" >Ens. Généraux</option>
<option id="select1" value="sect_prof" >Secteur Professionnel</option>
<option id="select1" value="spec_prof" >Spécialité Professionnelle</option>
<optgroup label="Bulletin ESG" >
<option id="select1" value="module1" >Module 1</option>
<option id="select1" value="module2" >Module 2</option>
<option id="select1" value="module3" >Module 3</option>
<option id="select1" value="module4" >Module 4</option>
</select>
</ul>
<input type=hidden name='saisie_classe' 	value="<?php print $_POST["saisie_classe"];?>" >
<input type=hidden name='saisie_trimestre' 	value="<?php print $_POST["saisie_trimestre"];?>" >
<input type=hidden name='saisie_classe' 	value="<?php print $_POST["saisie_classe"];?>" >
<input type=hidden name='annee_scolaire' 	value="<?php print $_POST["anneeScolaire"];?>" >
<input type=hidden name='NoteUsa' 		value="<?php print $_POST["NoteUsa"];?>" >
<input type=hidden name='typetrisem'	 	value="<?php print $_POST["typetrisem"];?>" >
<input type=hidden name='typebull' 		value="<?php print $typebull;?>" >
</form>
<br><br>
<!-- ________________________________________________________________ -->

<?php  } ?>




<?php


if (isset($_POST["falcutatif"])) {
	enr_bull_matFalcutative("matfacultative",$_POST["saisie_recherche_final"]);
}

if (($typebull == "bull0106") || ($typebull == "bull0107")  || (isset($_POST["falcutatif"])) ) {
	print "<br><hr>";
?>
	<font class=T2><?php print LANGMESS51 ?> : </font> <i><?php print LANGMESS52 ?></i>
	<form method=post name="formulaire" >
	<table border=0 width=100%>
	<tr><td width=33% align=center>
	<i>Matières</i><br>
	<select size=10 name="saisie_depart"  style="width:180px">
	<?php 
	
	$data=affMatiere();
	for($i=0;$i<count($data);$i++)  {
		if ($data[$i][1] != "") {
			print "<option STYLE='color:#000066;background-color:#CCCCFF' value='".$data[$i][0]."' >".$data[$i][1]." ".preg_replace("/0$/","",$data[$i][2])."</option>";
	        }
	}
	?>
	</select>
	</td>
	<td width=15% align=center>
	<input type="button" value="<?php print LANGCHER5?> >>>" onClick="calcul('+1');Deplacer(this.form.saisie_depart,this.form.saisie_recherche,'Choisissez un élèment')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
	<br><br><br>
	<input type="button" value="&lt;&lt;&lt; <?php print LANGCHER6 ?>" onClick="calcul('-1');Deplacer(this.form.saisie_recherche,this.form.saisie_depart,'Choisissez un élèment')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
	</td>
	<td width=33% align=center>
	<i>Matières falcutatives</i><br>
	<select size=10 name="saisie_recherche" style="width:180px" multiple="multiple">
	<OPTION>-------------</OPTION>
	</select>
	<script language="javascript">
	// suppression de la ligne  mais on la garde pour la largeur
	document.formulaire.saisie_recherche.options.length=0;
	</script>
	</td></tr></table>
	<input type="hidden" name="saisie_recherche_final">
	<input type="hidden" name="saisie_nb_recherche">
	<br><br>
	<table align="center"><tr><td><input type="submit" name="falcutatif" value="<?php print LANGENR ?>" class="BUTTON" onclick="prepEnvoi()" ></td></tr></table>
	<input type=hidden name='saisie_classe' 	value="<?php print $_POST["saisie_classe"];?>" >
	<input type=hidden name='saisie_trimestre' 	value="<?php print $_POST["saisie_trimestre"];?>" >
	<input type=hidden name='saisie_classe' 	value="<?php print $_POST["saisie_classe"];?>" >
	<input type=hidden name='annee_scolaire' 	value="<?php print $_POST["anneeScolaire"];?>" >
	<input type=hidden name='NoteUsa' 		value="<?php print $_POST["NoteUsa"];?>" >
	<input type=hidden name='typetrisem'	 	value="<?php print $_POST["typetrisem"];?>" >
	<input type=hidden name='typebull' 		value="<?php print $typebull;?>" >
	</form>
	
	<br>
	<b>Matières Facultatives :</b>
	<?php 
	$idliste=aff_bull_matfacultative("matfacultative");
	$liste_matiere=preg_replace("/\{/","",$idliste[0][1]);
	$liste_matiere=preg_replace("/\}/","",$liste_matiere);
	if ($liste_matiere != "") {
$sql=<<<EOF
SELECT  code_mat,libelle,sous_matiere FROM ${prefixe}matieres WHERE  code_mat IN ($liste_matiere) ORDER BY libelle 
EOF;
	$res=execSql($sql);
	$data=chargeMat($res);
	for($i=0;$i<count($data);$i++) {
		print ucwords($data[$i][1]).",";
	}
}
?>
<br><br>

<?php 
}
?>

<?php
// -----------------------------------------------------------------------------------------------------------
// Gestion bulletin blanc

if ( ($typebull == "bull401") || ($typebull == "bull0101b") || ($typebull == "bull402") || ($typebull == "bull403") || ($typebull == "bull404") || ($typebull == "bull405") || ($typebull == "bull406") ||  (isset($_POST["coefenr"])) || ($typebull == "bull409") || ($typebull == "bull0210b")  ) {

	if (isset($_POST["coefenr"])) {
		delete_coef_bulletin($typebull,$_POST["saisie_classe"]);
		config_param_ajout($_POST['moyenadmis'],"${typebull}Mdmis");
		for($k=0;$k<$_POST["nbmatiere"];$k++) {
			enr_coef_bulletin($typebull,$_POST["saisie_classe"],$_POST["idmatiere_$k"],$_POST["coef_$k"],$_POST["ordre_$k"]);
		}
	}

	include_once('librairie_php/recupnoteperiode.php');

	print "<hr><br />";
	
	$datap=config_param_visu("${typebull}Mdmis");
	$moyenadmis=$datap[0][0];
	
	print "<form method='post' >";
	print "<font class='T2'>&nbsp;&nbsp;Indiqué la mention (ADMIS ou REFUSE) en fonction&nbsp;&nbsp;de la moyenne suivante : <input type='text' value='$moyenadmis' size=3 name='moyenadmis' /></font>";
	
	print "<br><font class='T1 shadow'>&nbsp;&nbsp;* Si  zéro (0), pas d'indication </font>";

        print "<br /><br /><br />";	
	print "<font class='T2'>&nbsp;&nbsp;Mise en place des coefficients : </font>";
	print "<br>";
	print "<font class='T1 shadow'>&nbsp;&nbsp;* Si le coefficient est à zéro (0), la matière ne sera pas sur le bulletin </font>";
	print "<br><br><br>";
	print "<center><table border='0' style='border-collapse: collapse;' >";
	if ($typebull == "bull0210b") {
		$data=ordre_matiere_visubull_btsblanc($_POST["saisie_classe"],$anneeScolaire);
	}elseif($typebull == "bull0101b") {
		$data=ordre_matiere_visubull_btsblanc($_POST["saisie_classe"],$anneeScolaire);
	}else{
		$data=ordre_matiere_visubull($_POST["saisie_classe"],$anneeScolaire); //code_mat,libelle,sous_matiere
	}
	$nbmatiere=count($data);
	for($i=0;$i<count($data);$i++) {
		$libelle=chercheMatiereNom($data[$i][0]);
		//$sousmatiere=$data[$i][2];
		$idmatiere=$data[$i][0];
		$ordre=$data[$i][2];
		if ($sousmatiere == "0") { $sousmatiere=""; }
		$coef=recup_coef_bulletin($typebull,$_POST["saisie_classe"],$idmatiere,$ordre);
		print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
		print "<td align='right' >&nbsp;&nbsp;$libelle $sousmatiere&nbsp;:&nbsp;</td><td><input type='text' name='coef_$i' size='4' value='$coef' />";
		print "<input type='hidden' name='idmatiere_$i' value='$idmatiere' />";
		print "<input type='hidden' name='ordre_$i' value='$ordre' />";
		print "</td>";
		print "</tr>";

	}
	print "</table>";
	print "<input type='hidden' name='nbmatiere' 		value='$nbmatiere'  />";
	print "<input type='hidden' name='saisie_classe' 	value='".$_POST["saisie_classe"]."'  />";
	print "<input type='hidden' name='typebull' 		value=\"$typebull\"  />";
	print "<input type='hidden' name='saisie_trimestre' 	value=\"".$_POST["saisie_trimestre"]."\" />";
	print "<input type='hidden' name='annee_scolaire' 	value=\"".$_POST["anneeScolaire"]."\" />";
	print "<input type='hidden' name='anneeScolaire' 	value=\"".$_POST["anneeScolaire"]."\" />";
	print "<input type='hidden' name='NoteUsa' 		value=\"".$_POST["NoteUsa"]."\" />";
	print "<input type='hidden' name='typetrisem'	 	value=\"".$_POST["typetrisem"]."\" />";

	print "<br /><input type='submit' value='Enregistrer'  name='coefenr' class='bouton2' /></center>";
	print "</form>";
}
 
if ($_SESSION["membre"] != "menuadmin") { print "</div>"; } 


if (($_POST["saisie_trimestre"] == "trimestre3" && $typebull == "bull02UE") || ($_POST["saisie_trimestre"] == "trimestre3" && $typebull == "bull04UE") || ($_POST["saisie_trimestre"] == "trimestre3" && $typebull == "bull04UE3") )  {
	// rien
}else{
	if ( ($_POST["saisie_trimestre"] != "cycle1") && ($_POST["saisie_trimestre"] != "cycle2") && ($_POST["saisie_trimestre"] != "cycle3") && ($_POST["saisie_trimestre"] != "cycle4")) { 
		$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
		if (count($dateRecup) == 0) {
			print "<center><font id='color2'><b>ATTENTION !! Aucune date trimestrielle n'est attribu&eacute;e pour la classe.</b></font></center>";
		}
	}
	if (($_POST["saisie_trimestre"] == "cycle1") || ($_POST["saisie_trimestre"] == "cycle2") || ($_POST["saisie_trimestre"] == "cycle3") || ($_POST["saisie_trimestre"] != "cycle4")) { 
		print "<script>document.formulaire5.rien.disabled=false</script>";
	}
}

?>
<?php  if ($_SESSION["membre"] == "menuprof") print "</table>"; ?>

<br /><br />
<!-- // fin  -->
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif ;
// deconnexion en fin de fichier

attente();
Pgclose();
?>
<?php include_once("./librairie_php/finbody.php"); ?>
</BODY></HTML>
