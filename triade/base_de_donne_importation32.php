<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGbasededoni91 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once("librairie_php/db_triade.php");
include_once("librairie_php/timezone.php");
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);

$nbi=$_POST["nbi"];

@unlink("./data/fic_pass.txt");

function eclair($x,$y){
	if (!is_array($x) || !is_array($y)){
		echo "<br><br><center>".LANGbasededoni92;
	}
	array_pad($x,countTriade($y),"");
	array_pad($y,countTriade($x),"");
	while(countTriade($x) > 0){
		$in=gep_classe(array_shift($x),array_shift($y));
		if ($in == 0) {
			alertJs(LANGbasededoni93);
			print "<script>history.go(-2);</script>";
			break;
		}
	}
}

$cnx=cnx();

vide_gep_classe();

for($i=0;$i<=$nbi;$i++) {
	$tabsaisie_classe[]=$_POST["saisie_classe_$i"];
	$tabsaisie_ref[]=$_POST["saisie_ref_$i"];
}

eclair($tabsaisie_classe,$tabsaisie_ref);

$nbelevetotal=0;
$nbelevedejaffecte=0;

$optionligne=1;
if ($_POST['optionligne'] == 1) { $optionligne=0; }

$noncontroledatenaissance=$_POST["noncontroledatenaissance"];

if ($_POST["typefichier"] == "excel" ) {
		$fic_xls=$_POST["fichier"];
		include_once('./librairie_php/reader.php');
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('UTF-8');
		$data->read($fic_xls);

		for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
			if ($i == $optionligne ) { continue; }

			$classe=recherche_gep_classe(trim($data->sheets[0]['cells'][$i][3]));

			$passwd=trim($data->sheets[0]['cells'][$i][34]);
			$passwd2=trim($data->sheets[0]['cells'][$i][47]);

			$passwd_eleve=trim($data->sheets[0]['cells'][$i][39]);
			$date_naissance=trim($data->sheets[0]['cells'][$i][5]);

			if ((trim($passwd) == "") || (! isset($passwd)))  {
					$passwd=passwd_random();
					$passwd_enr=$passwd;
			}else {
					$passwd_enr=$passwd;
			}

			if ((trim($passwd2) == "") || (! isset($passwd2)))  {
					$passwd2=passwd_random();
					$passwd_enr2=$passwd2;
			}else {
					$passwd_enr2=$passwd2;
			}


			if ((trim($passwd_eleve) == "") || (! isset($passwd_eleve)) ||  (trim($passwd_eleve) == "null") )  {
					$passwd_eleve=passwd_random();
					$passwd_eleve_enr=$passwd_eleve;
			}else {
					$passwd_eleve_enr=$passwd_eleve;
			}


			if ($date_naissance == "") {
					$date_naissance=dateDMY();
			}

			if ((strtoupper(trim($data->sheets[0]['cells'][$i][4])) == "EXTERN") ||  (strtoupper(trim($data->sheets[0]['cells'][$i][4])) == "EXT") || (strtolower(trim($data->sheets[0]['cells'][$i][4])) == "externe") ) {
				$regime="externe";
			}

			if ((strtoupper(trim($data->sheets[0]['cells'][$i][4])) == "INT")  || (strtolower(trim($data->sheets[0]['cells'][$i][4])) == "interne")) {
				$regime="interne";
			}

			if ((strtoupper(trim($data->sheets[0]['cells'][$i][4])) == "DP DAN") || (strtoupper(trim($data->sheets[0]['cells'][$i][4])) == "DP")  || (strtolower(trim($data->sheets[0]['cells'][$i][4])) == "demi pension") ){
				$regime="Demi Pension";
			}


			if (strlen(trim($classe))) {
					$nbelevetotal++;
					$params['ne']=            trim(addslashes($data->sheets[0]['cells'][$i][1]));
					$params['pe']=            trim(addslashes($data->sheets[0]['cells'][$i][2]));
					$params['ce']=            $classe;
					$params['lv1']=           trim(addslashes($data->sheets[0]['cells'][$i][30]));
					$params['lv2']=           trim(addslashes($data->sheets[0]['cells'][$i][31]));
					$params['option']=        trim(addslashes($data->sheets[0]['cells'][$i][32]));
					$params['regime']=        $regime;
					$params['naiss']=         $date_naissance;
					$params['lieunais']=	trim(addslashes($data->sheets[0]['cells'][$i][6]));
					$params['nat']=           trim(addslashes($data->sheets[0]['cells'][$i][7]));
					$params['mdp']=           $passwd_enr;
					$params['mdpeleve']=	$passwd_eleve_enr;
					$params['mdp2']=		$passwd_enr2;
					$params['civ_1']=         civ2($data->sheets[0]['cells'][$i][8]);
					$params['nt']=            trim(addslashes($data->sheets[0]['cells'][$i][9]));
					$params['pt']=		trim(addslashes($data->sheets[0]['cells'][$i][10]));
					$params['adr1']=        	trim(addslashes($data->sheets[0]['cells'][$i][11]));
					$params['cpadr1']=      	trim(addslashes($data->sheets[0]['cells'][$i][12]));
					$params['commadr1']=     	trim(addslashes($data->sheets[0]['cells'][$i][13]));
					$params['tel_port_1']=    trim(addslashes($data->sheets[0]['cells'][$i][14]));
					$params['civ_2']=         civ2($data->sheets[0]['cells'][$i][15]);
					$params['nom_resp2']=     trim(addslashes($data->sheets[0]['cells'][$i][16]));
					$params['prenom_resp2']=  trim(addslashes($data->sheets[0]['cells'][$i][17]));
					$params['adr2']=          trim(addslashes($data->sheets[0]['cells'][$i][18]));
					$params['cpadr2']=       	trim(addslashes($data->sheets[0]['cells'][$i][19]));
					$params['commadr2']=     	trim(addslashes($data->sheets[0]['cells'][$i][20]));
					$params['tel_port_2']=    trim(addslashes($data->sheets[0]['cells'][$i][21]));
					$params['tel']=          	trim(addslashes($data->sheets[0]['cells'][$i][22]));
					$params['tel_eleve']=	trim(addslashes($data->sheets[0]['cells'][$i][23]));
					$params['profp']=        	trim(addslashes($data->sheets[0]['cells'][$i][24]));
					$params['telprofp']=     	trim(addslashes($data->sheets[0]['cells'][$i][25]));
					$params['profm']=         trim(addslashes($data->sheets[0]['cells'][$i][26]));
					$params['telprofm']=     	trim(addslashes($data->sheets[0]['cells'][$i][27]));
					$params['nomet']=        	trim(addslashes($data->sheets[0]['cells'][$i][28]));
					$params['numet']=        	trim(addslashes($data->sheets[0]['cells'][$i][29]));
					$params['cpet']=         	"";
					$params['commet']=    	"";
					$params['numero_eleve']=  trim(addslashes($data->sheets[0]['cells'][$i][33]));
					$params['email']=  	trim(addslashes($data->sheets[0]['cells'][$i][35]));
					$params['mail_eleve']=  	trim(addslashes($data->sheets[0]['cells'][$i][36]));
					$params['classe_ant']=  	trim(addslashes($data->sheets[0]['cells'][$i][37]));
					$params['annee_ant']=  	trim(addslashes($data->sheets[0]['cells'][$i][38]));
					$params['adr_eleve']=  	trim(addslashes($data->sheets[0]['cells'][$i][40]));
					$params['commune_eleve']= trim(addslashes($data->sheets[0]['cells'][$i][41]));
					$params['ccp_eleve']=  	trim(addslashes($data->sheets[0]['cells'][$i][42]));
					$params['tel_fixe_eleve']=trim(addslashes($data->sheets[0]['cells'][$i][43]));
					$params['boursier']=	trim(addslashes($data->sheets[0]['cells'][$i][44]));
					$params['mailpro_eleve']	=trim(addslashes($data->sheets[0]['cells'][$i][45]));
					$params['sexe']=		trim(addslashes($data->sheets[0]['cells'][$i][46]));
					$params['annee_scolaire'] = $_POST["annee_scolaire"];
					$ascii=1;
					if ($_POST['update'] == 1) {
						$cr=create_update_eleve($params['ne'],$params['pe'],$params['naiss'],$params,$ascii,$_POST['updatevide'],$_POST['updatepasswd'],$noncontroledatenaissance);
					}else{
						$cr=@create_eleve($params,$ascii);
					}
					if ($cr == 1){
						$f_pass=fopen("./data/fic_pass.txt","a+");
					     	    	fwrite($f_pass,strtolower(trim($data->sheets[0]['cells'][$i][1])).";".strtolower(trim($data->sheets[0]['cells'][$i][2])).";".$passwd_enr.";".$passwd_eleve_enr.";".chercheClasse_nom($classe)."<br />");
			     			fclose($f_pass);
						$nbeleveaffecte++;
					}
					if ($cr == -3) {
						$nbelevedejaffecte++;
					}

		}else{
					$nbelevetotal++;
					$params['ne']=            trim(addslashes($data->sheets[0]['cells'][$i][1]));
					$params['pe']=            trim(addslashes($data->sheets[0]['cells'][$i][2]));
					$params['ce']=            $classe;
					$params['lv1']=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][30])));
					$params['lv2']=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][31])));
					$params['option']=        strtolower(trim(addslashes($data->sheets[0]['cells'][$i][32])));
					$params['regime']=        $regime;
					$params['naiss']=         $date_naissance;
					$params['lieunais']=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][6])));
					$params['nat']=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][7])));
					$params['mdp']=           cryptage($passwd_enr);
					$params['mdp2']=          cryptage($passwd_enr2);
					$params['mdpeleve']=	cryptage($passwd_eleve_enr);
					$params['civ_1']=         civ2($data->sheets[0]['cells'][$i][8]);
					$params['nt']=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][9])));
					$params['pt']=		strtolower(trim(addslashes($data->sheets[0]['cells'][$i][10])));
					$params['adr1']=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][11])));
					$params['cpadr1']=      	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][12])));
					$params['commadr1']=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][13])));
					$params['tel_port_1']=    trim(addslashes($data->sheets[0]['cells'][$i][14]));
					$params['civ_2']=         civ2($data->sheets[0]['cells'][$i][15]);
					$params['nom_resp2']=     strtolower(trim(addslashes($data->sheets[0]['cells'][$i][16])));
					$params['prenom_resp2']=  strtolower(trim(addslashes($data->sheets[0]['cells'][$i][17])));
					$params['adr2']=          strtolower(trim(addslashes($data->sheets[0]['cells'][$i][18])));
					$params['cpadr2']=       	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][19])));
					$params['commadr2']=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][20])));
					$params['tel_port_2']=    trim(addslashes($data->sheets[0]['cells'][$i][21]));
					$params['tel']=          	trim(addslashes($data->sheets[0]['cells'][$i][22]));
					$params['tel_eleve']=	trim(addslashes($data->sheets[0]['cells'][$i][23]));
					$params['profp']=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][24])));
					$params['telprofp']=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][25])));
					$params['profm']=         strtolower(trim(addslashes($data->sheets[0]['cells'][$i][26])));
					$params['telprofm']=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][27])));
					$params['nomet']=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][28])));
					$params['numet']=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][29])));
					$params['cpet']=         	"";
					$params['commet']=    	"";
					$params['numero_eleve']=  strtolower(trim(addslashes($data->sheets[0]['cells'][$i][33])));
					$params['email']=  	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][35])));
					$params['mail_eleve']=  	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][36])));
					$params['classe_ant']=  	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][37])));
					$params['annee_ant']=  	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][38])));
					$params['adr_eleve']=  	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][40])));
					$params['commune_eleve']= strtolower(trim(addslashes($data->sheets[0]['cells'][$i][41])));
					$params['ccp_eleve']=  	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][42])));
					$params['tel_fixe_eleve']=strtolower(trim(addslashes($data->sheets[0]['cells'][$i][43])));
					$params['boursier']=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][44])));
					$params['mailpro_eleve']=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][45])));
					$params['sexe']=		strtolower(trim(addslashes($data->sheets[0]['cells'][$i][46])));
					$params['annee_scolaire'] = $_POST["annee_scolaire"];
					$ascii=1;
					$cr=@create_eleve_sans_classe($params,$ascii);
					if ($cr == 1) {
						$f_pass=fopen("./data/fic_pass.txt","a+");
						fwrite($f_pass,strtolower(trim($data->sheets[0]['cells'][$i][1])).";".strtolower(trim($data->sheets[0]['cells'][$i][2])).";".$passwd_enr.";".$passwd_eleve_enr.";".chercheClasse_nom($classe)."<br />");
						fclose($f_pass);
						$nbeleverreur++;
					}
					if ($cr == -3) {
						$nbelevedejaffecte++;
					}

		}

}
			$today=dateDMY();
			$fichier_s=fopen("./".REPADMIN."/data/fic_opinion.txt","a+");
			$donnee=fwrite($fichier_s,"<BR>Message du : <FONT color=red>$today</font> De :<FONT color=red> $_SESSION[nom] $_SESSION[prenom]</FONT> <BR>Membre : <font color=red> $_SESSION[membre] </FONT><BR> <B>Message :</B> <font color=red> NOUVELLE BASE </font> - avec fichier EXCEL <BR>  Etablissement : <font color=red>".REPECOLE."</font> ");
			fclose($fichier_s);

			@unlink($fic_xls);


}

Pgclose();
?>

<div class="na-card">
  <p style="font-size:13px;color:#555;margin:0 0 4px;">— <?php print LANGBASE6bis ?> : <strong><?php print $nbelevetotal ?></strong></p>
  <p style="font-size:13px;color:#555;margin:0 0 4px;">— <?php print LANGBASE7 ?> : <strong><?php print $nbeleveaffecte ?></strong></p>
  <p style="font-size:13px;color:#555;margin:0 0 4px;">— <?php print LANGBASE7bis ?> : <strong><?php print $nbelevedejaffecte ?></strong></p>
  <p style="font-size:13px;color:#555;margin:0 0 4px;">— <?php print LANGBASE8 ?> : <strong><?php print $nbeleverreur ?></strong></p>
  <p style="font-size:12px;color:#888;margin-top:8px;"><?php print LANGBASE9 ?> (<?php print LANGBASE8bis ?>)</p>
  <p style="font-size:12px;color:#c62828;margin-top:4px;"><?php print LANGBASE17 ?></p>
</div>
<div class="na-foot">
  <?php if (file_exists("./data/fic_pass.txt")) : ?>
  <button type="button" class="btn-enr" onclick="open('recupepw.php','_blank','')"><?php print LANGBT40 ?></button>
  <?php endif; ?>
  <button type="button" class="btn-enr" onclick="location.href='acces2.php'"><?php print LANGBT41 ?></button>
</div>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
