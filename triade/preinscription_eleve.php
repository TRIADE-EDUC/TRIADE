<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="CacheControl" content = "no-cache" />
   <meta http-equiv="pragma" content = "no-cache" />
   <meta http-equiv="expires" content = -1 />
   <meta name="Copyright" content="Triade©, 2001" />
   <meta http-equiv="imagetoolbar" content="no" />
     <link rel="stylesheet" type="text/CSS" href="./librairie_css/css.css" media="screen" />
     <link rel="stylesheet" href="./librairie_css/css-v4.css">
     <link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
     <style>
     .pe-date-input{padding:5px 7px;border:1px solid #dde0f0;border-radius:6px;font-size:12px;color:#333;width:110px;background:#f8f9ff;text-align:center}
     #anchor18id1{display:inline-flex;align-items:center;vertical-align:middle;margin-left:6px}
     </style>
     <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <title>Envoi des candidatures</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
	<noscript><meta http-equiv="Refresh" content="0; URL=noscript.php"></noscript>
	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	<script type="text/javascript" src="./librairie_js/logo.js"></script>
	<script type="text/javascript" src="./librairie_js/function.js"></script>
	<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
  <script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
  <script language="JavaScript" src="./librairie_js/lib_css.js"></script>
  <script src="./alertifyjs/alertify.min.js"></script>
	<?php
	include_once("./librairie_php/lib_netscape.php");
	include_once("./librairie_php/lib_licence2.php");
	include_once("./common/lib_ecole.php");
	include_once("./common/config2.inc.php");
	include_once("./common/config.inc.php");
	include_once("./librairie_php/db_triade.php");
	include_once("./common/version.php");
	if ($_COOKIE["langue-triade"] == "fr") {
        	include_once("./librairie_php/langue-text-fr.php");
	        print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
        	print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
	}elseif ($_COOKIE["langue-triade"] == "en") {
        	print "<script type=text/javascript src='librairie_js/langueenmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueenfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-en.php");
	}elseif ($_COOKIE["langue-triade"] == "es") {
        	print "<script type=text/javascript src='librairie_js/langueesmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/langueesfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-es.php");
	}elseif ($_COOKIE["langue-triade"] == "bret") {
        	print "<script type=text/javascript src='librairie_js/languebretmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languebretfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-bret.php");
	}elseif ($_COOKIE["langue-triade"] == "arabe") {
        	print "<script type=text/javascript src='librairie_js/languearabemenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languearabefunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-arabe.php");
	}else {
        	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-fr.php");
	}
	if ((defined("HTTPS")) && (HTTPS == "non")) {
		print "<script type='text/javascript'>var http='http://';</script>\n";
	}else{
		print "<script type='text/javascript'>var http='https://';</script>\n";
	}
	if ((defined("POPUP")) && (POPUP == "non")) {
		print "<script type='text/javascript'>var popup='non';</script>\n";
	}else {
		print "<script type='text/javascript'>var popup='oui';</script>\n";
	}
	print "<script type='text/javascript'>var vocalmess='offline';</script>\n";
	print "<script type='text/javascript'>var inc='".GRAPH."';</script>\n";

	?>
	<script type="text/javascript" >var mailcontact="<?php
		if ((MAILCONTACT != "") && (defined("MAILCONTACT")) ) {
			print MAILCONTACT;
		}else{
			print "";
		} ?>";</script>
	<script type="text/javascript" >var urlcontact="<?php
		if ((URLCONTACT != "") && (defined("URLCONTACT"))) {
			print URLCONTACT;
		}else{
			print "";
		}  ?>"; </script>
	<script type="text/javascript" >var urlnomcontact="<?php
		if ((URLNOMCONTACT != "") && (defined("URLNOMCONTACT"))) {
			$urlnomcontact=preg_replace('/ /',"&nbsp;",URLNOMCONTACT);
			print URLNOMCONTACT;
		}else{
			print "";
		} ?>"; </script>

<script type="text/javascript" >var urlcontact2="<?php if (URLCONTACT2 != "") { print URLCONTACT2; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact2="<?php if (URLNOMCONTACT2 != "") { print URLNOMCONTACT2; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact3="<?php if (URLCONTACT3 != "") { print URLCONTACT3; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact3="<?php if (URLNOMCONTACT3 != "") { print URLNOMCONTACT3; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact4="<?php if (URLCONTACT4 != "") { print URLCONTACT4; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact4="<?php if (URLNOMCONTACT4 != "") { print URLNOMCONTACT4; }else{ print ""; } ?>"; </script>

	<script type="text/javascript" src="./librairie_js/menudepart.js"></script>
	<?php include("./librairie_php/lib_defilement.php"); ?>
	</TD><td width="472" valign="middle" rowspan="3" align="center" >
	<div align='center'><?php top_h(); ?>
	<script type="text/javascript" src="./librairie_js/menudepart1.js"></script>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Candidature Élève</font></B></td></tr>
	<tr id='cadreCentral0'>
	<td >
	<!-- // fin  -->
<br>
<?php
$cnx=cnx();
?>
<script>
alertify.set('notifier', 'position', 'bottom-right');

function valide_creat_eleve3() {
    var ok = true;
    var firstEl = null;
    var f = document.formulaire;

    function checkText(name, label, minLen) {
        var el = f[name];
        if (!el) return;
        var val = el.value.trim();
        if (val.length < (minLen || 1)) {
            el.style.border = '2px solid #c62828';
            if (!firstEl) { firstEl = el; alertify.error('Champ obligatoire : ' + label); }
            ok = false;
        } else {
            el.style.border = '';
        }
    }

    function checkSelect(name, label) {
        var el = f[name];
        if (!el) return;
        if (el.selectedIndex <= 0) {
            el.style.border = '2px solid #c62828';
            if (!firstEl) { firstEl = el; alertify.error('Champ obligatoire : ' + label); }
            ok = false;
        } else {
            el.style.border = '';
        }
    }

    checkText('saisie_nom',            'Nom',                2);
    checkText('saisie_prenom',         'Prénom',             2);
    checkSelect('saisie_classe',       'Classe');
    checkSelect('annee_scolaire',      'Année scolaire');
    checkText('saisie_date_naissance', 'Date de naissance', 10);
    checkText('saisie_passwd_eleve',   'Mot de passe élève', 2);
    checkText('saisie_email_eleve',    'Email élève',        4);

    if (!ok && firstEl) { firstEl.focus(); }

    if (ok) {
        f.saisie_nom.value    = f.saisie_nom.value.toLowerCase();
        f.saisie_prenom.value = f.saisie_prenom.value.toLowerCase();
    }

    return ok;
}
</script>
<form method=post action="add_eleve.php" onsubmit="document.getElementById('pe-error-msg').style.display='none'; return valide_creat_eleve3()" name="formulaire">

<?php if (isset($_GET["error"])): ?>
<div style="background:#fce4ec;border:1px solid #f48fb1;border-radius:7px;padding:10px 14px;font-size:12px;color:#c62828;font-weight:600;margin-bottom:12px">&#9888;&nbsp;<?php $errMsg = trim($_GET["error"]); print htmlspecialchars($errMsg !== '' ? $errMsg : "Erreur de transmission. Veuillez vérifier l'ensemble des données avant de valider.") ?></div>
<?php endif; ?>

<!-- Section Élève -->
<table border='0' width='100%' style="background:#f8f9ff;border:2px solid #c5cae9;border-radius:8px;box-shadow:0 2px 8px rgba(8,10,102,.10);margin-bottom:14px">
<tr><td style="padding:10px 16px;font-size:13px;font-weight:700;color:#080A66;border-bottom:1px solid #dde0f0;background:#eef0f8;border-radius:6px 6px 0 0">Renseignements Élève</td></tr>
<tr><td style="padding:14px 16px">
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right;width:200px">Nom :</td>
<td style="padding:5px 0"><input type="text" name="saisie_nom" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"> <span style="color:#c62828;font-weight:700">*</span></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Prénom :</td>
<td style="padding:5px 0"><input type="text" name="saisie_prenom" maxlength=50 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"> <span style="color:#c62828;font-weight:700">*</span></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Classe :</td>
<td style="padding:5px 0">
<select name="saisie_classe" style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px">
<option><?php print LANGCHOIX ?></option>
<?php select_classe2(20); ?>
</select> <span style="color:#c62828;font-weight:700">*</span>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Pour l'année scolaire :</td>
<td style="padding:5px 0">
<select name='annee_scolaire' style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px">
<?php filtreAnneeScolaireSelectFutur(); ?>
</select> <span style="color:#c62828;font-weight:700">*</span>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">LV1 :</td>
<td style="padding:5px 0">
<select name="saisie_lv1" style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px">
<option selected value=''> </option>
<?php select_matiere2(); ?>
</select>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">LV2 :</td>
<td style="padding:5px 0">
<select name="saisie_lv2" style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px">
<option selected value=''> </option>
<?php select_matiere2(); ?>
</select>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Option :</td>
<td style="padding:5px 0">
<select name="saisie_option2" style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px">
<option selected value=''> </option>
<?php select_matiere2(); ?>
</select>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right;vertical-align:top">Régime :</td>
<td style="padding:8px 0">
<label style="font-size:12px;color:#333;display:block;margin-bottom:4px"><input type="radio" name="saisie_regime" value="Interne" id='.btradio1'> Interne</label>
<label style="font-size:12px;color:#333;display:block;margin-bottom:4px"><input type="radio" name="saisie_regime" value="Demi-pension" id='.btradio1'> Demi-pensionnaire</label>
<label style="font-size:12px;color:#333;display:block"><input type="radio" name="saisie_regime" value="Externe" id='.btradio1'> Externe</label>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Boursier :</td>
<td style="padding:5px 0">
<label style="font-size:12px;color:#333;margin-right:14px"><input type="radio" name="saisie_boursier" value="1" id='.btradio1'> Oui</label>
<label style="font-size:12px;color:#333"><input type="radio" name="saisie_boursier" value="0" id='.btradio1'> Non</label>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Date de naissance :</td>
<td style="padding:5px 0">
<input type="text" name="saisie_date_naissance" readonly="readonly" class="pe-date-input">
<?php include_once("librairie_php/calendar.php"); calendarDim('id1','document.formulaire.saisie_date_naissance',$_COOKIE["langue-triade"],"0","0"); ?>
<script>cal18.showYearNavigation(); cal18.showYearNavigationInput(25);</script>
<span style="color:#c62828;font-weight:700">&nbsp;*</span>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Sexe :</td>
<td style="padding:5px 0">
<label style="font-size:12px;color:#333;margin-right:14px"><input type="radio" name="saisie_sexe" value="m"> M</label>
<label style="font-size:12px;color:#333"><input type="radio" name="saisie_sexe" value="f"> F</label>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Nationalité :</td>
<td style="padding:5px 0"><input type="text" name="saisie_nationalite" maxlength=20 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Lieu de naissance :</td>
<td style="padding:5px 0"><input type="text" name="saisie_lieu_naissance" maxlength='40' style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Mot de passe élève :</td>
<td style="padding:5px 0"><input type="passwd" name="saisie_passwd_eleve" maxlength=50 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"> <span style="color:#c62828;font-weight:700">*</span></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Numéro étudiant :</td>
<td style="padding:5px 0"><input type="text" name="saisie_numero_eleve" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Tél. portable élève :</td>
<td style="padding:5px 0"><input type="text" name="saisie_tel_eleve" maxlength=18 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Email élève :</td>
<td style="padding:5px 0"><input type="text" name="saisie_email_eleve" maxlength=48 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"> <span style="color:#c62828;font-weight:700">*</span></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Adresse élève :</td>
<td style="padding:5px 0"><input type="text" name="saisie_adr_eleve" maxlength=100 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right"><?php print LANGELE15 ?> élève :</td>
<td style="padding:5px 0"><input type="text" name="saisie_code_post_adr_eleve" maxlength=6 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:100px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right"><?php print LANGELE16 ?> élève :</td>
<td style="padding:5px 0"><input type="text" name="saisie_commune_adr_eleve" maxlength=40 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Pays élève :</td>
<td style="padding:5px 0"><input type="text" name="saisie_pays_eleve" maxlength=50 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Téléphone fixe élève :</td>
<td style="padding:5px 0"><input type="text" name="saisie_tel_fixe_eleve" maxlength=25 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
</table>
</td></tr>
</table>

<!-- Section Famille -->
<table border='0' width='100%' style="background:#f8f9ff;border:2px solid #c5cae9;border-radius:8px;box-shadow:0 2px 8px rgba(8,10,102,.10);margin-bottom:14px">
<tr><td style="padding:10px 16px;font-size:13px;font-weight:700;color:#080A66;border-bottom:1px solid #dde0f0;background:#eef0f8;border-radius:6px 6px 0 0">Renseignements Famille</td></tr>
<tr><td style="padding:14px 16px">
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right;width:200px">Civ 1 :</td>
<td style="padding:5px 0">
<select name="saisie_civ_1" style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:180px">
<option value='6'>M. ou Mme</option>
<option value='0'>M.</option>
<option value='1'>Mme</option>
<option value='2'>Mlle</option>
<option value='3'>Ms</option>
<option value='4'>Mr</option>
<option value='5'>Mrs</option>
</select>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Nom resp. 1 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_nomtuteur" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Prénom resp. 1 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_prenomtuteur" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Adresse 1 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_adr1" maxlength=100 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Code postal 1 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_code_post_adr1" maxlength=6 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:100px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Commune 1 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_commune_adr1" maxlength=40 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Tél. portable 1 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_tel_port_1" maxlength=25 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Email tuteur 1 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_email" maxlength=150 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Civ 2 :</td>
<td style="padding:5px 0">
<select name="saisie_civ_2" style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:180px">
<option value='6'>M. ou Mme</option>
<option value='0'>M.</option>
<option value='1'>Mme</option>
<option value='2'>Mlle</option>
<option value='3'>Ms</option>
<option value='4'>Mr</option>
<option value='5'>Mrs</option>
</select>
</td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Nom resp. 2 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_nom_resp_2" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Prénom resp. 2 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_prenom_resp_2" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Adresse 2 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_adr2" maxlength=100 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Code postal 2 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_code_post_adr2" maxlength=6 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:100px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Commune 2 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_commune_adr2" maxlength=40 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Tél. portable 2 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_tel_port_2" maxlength=25 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Email tuteur 2 :</td>
<td style="padding:5px 0"><input type="text" name="saisie_email_resp_2" maxlength=150 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Numéro de téléphone :</td>
<td style="padding:5px 0"><input type="text" name="saisie_telephone" maxlength=18 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Profession du père :</td>
<td style="padding:5px 0"><input type="text" name="saisie_profession_pere" maxlength=20 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Téléphone du père :</td>
<td style="padding:5px 0"><input type="text" name="saisie_tel_prof_pere" maxlength=18 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Profession de la mère :</td>
<td style="padding:5px 0"><input type="text" name="saisie_profession_mere" maxlength=20 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Téléphone de la mère :</td>
<td style="padding:5px 0"><input type="text" name="saisie_tel_prof_mere" maxlength=18 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Mot de passe parent :</td>
<td style="padding:5px 0"><input type="passwd" name="saisie_passwd" maxlength=50 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
</table>
</td></tr>
</table>

<!-- Section École antérieure -->
<table border='0' width='100%' style="background:#f8f9ff;border:2px solid #c5cae9;border-radius:8px;box-shadow:0 2px 8px rgba(8,10,102,.10);margin-bottom:14px">
<tr><td style="padding:10px 16px;font-size:13px;font-weight:700;color:#080A66;border-bottom:1px solid #dde0f0;background:#eef0f8;border-radius:6px 6px 0 0">École antérieure</td></tr>
<tr><td style="padding:14px 16px">
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right;width:200px">Nom de l'établissement :</td>
<td style="padding:5px 0"><input type="text" name="saisie_nom_etablissement" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Numéro établissement :</td>
<td style="padding:5px 0"><input type="text" name="saisie_numero_etablissement" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Classe antérieure :</td>
<td style="padding:5px 0"><input type="text" name="saisie_classe_ant" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Année antérieure :</td>
<td style="padding:5px 0"><input type="text" name="saisie_date_ant" style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Code postal :</td>
<td style="padding:5px 0"><input type="text" name="saisie_code_postal_etablissement" maxlength=6 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:100px"></td>
</tr>
<tr>
<td style="padding:5px 10px 5px 0;font-size:12px;font-weight:600;color:#444;white-space:nowrap;text-align:right">Commune :</td>
<td style="padding:5px 0"><input type="text" name="saisie_commune_etablissement" maxlength=30 style="padding:6px 9px;border:1px solid #c5cae9;border-radius:6px;font-size:12px;background:#fff;color:#333;width:220px"></td>
</tr>
</table>
</td></tr>
</table>

<div id="pe-error-msg" style="display:none;background:#fce4ec;border:1px solid #f48fb1;border-radius:7px;padding:10px 14px;font-size:12px;color:#c62828;font-weight:600;margin-bottom:12px"></div>
<table border="0" width="100%" align="center"><tr><td height="53"><table align=center><td><td><script language=JavaScript>buttonMagicSubmit('Envoyer ma candidature','create');</script></td></tr></table>
</td></tr></table>

</form>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
