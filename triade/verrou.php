<?php
session_start();
error_reporting(0);
$nom=$_SESSION["nom"];
$prenom=$_SESSION["prenom"];
$membre=$_SESSION["membre"];
$langue=$_SESSION["langue"];


session_set_cookie_params(0);
$_SESSION=array();
session_unset();
session_destroy();
session_start();
$csrf_verrou = bin2hex(random_bytes(32));
$_SESSION['csrf_login'] = $csrf_verrou;
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
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
	}else {
        	print "<script type=text/javascript src='librairie_js/languefrmenu-depart.js'></script>\n";
	        print "<script type=text/javascript src='librairie_js/languefrfunction-depart.js'></script>\n";
        	include_once("./librairie_php/langue-text-fr.php");
	}

?>

<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css-v4-2.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_type_navigateur.js"></script>
<title>Triade</title>
<style>
.lock-wrap { display:flex; justify-content:center; padding:48px 10px; }
.lock-card { max-width:700px; width:100%; box-shadow:0 6px 20px rgba(0,0,0,.18) !important; }
.lock-header { font-size:13px; color:#333; font-family:Electrolize,Trebuchet MS,Arial,sans-serif; display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:14px; }
.lock-header b { color:#080A66; }
.lock-persist { margin-top:14px; font-size:11px; text-align:center; }
</style>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence2.php");
?>

<div class="lock-wrap">
<form method=post name="inscripform" action="acces.php">
<div class="na-card lock-card">
  <div class="lock-header">
    <img src="image/commun/img_ssl.gif" align="center">
    Compte de <b><?php print strtoupper($nom) ?> <?php print ucfirst($prenom) ?></b> verrouillé.
  </div>
  <br>
  <div class="na-row">
    <span class="na-lbl">Mot de passe :</span>
    <input type="password" name="saisiepasswd" class="bouton2">
  </div>
  <br>
  <div class="na-foot">
    <button type="submit" name="rien" value="connexion" class="btn-enr">connexion</button>
    <button type="button" onclick="open('index.html','_parent','')" class="btn-retour">Quitter</button>
  </div>
  <br>
  <div class="lock-persist">
    <?php
    include_once("./librairie_php/lib_conexpersistant.php");
    connexpersistance("color:black;font-weight:bold;font-size:11px;text-align:center;");
    ?>
  </div>
</div>

<?php
if ($membre == "menuadmin") { $membre="administrateur";}
if ($membre == "menuparent") { $membre="parent";}
if ($membre == "menuprof") { $membre="enseignant";}
if ($membre == "menueleve") { $membre="eleve";}
if ($membre == "menuscolaire") { $membre="vie scolaire";}
if ($membre == "menututeur") { $membre="tuteur";}
?>
<input type="hidden" name="saisienom" value="<?php print strtolower($nom) ?>" />
<input type="hidden" name="saisieprenom" value="<?php print strtolower($prenom) ?>" />
<input type="hidden" name="saisie_membre" value="<?php print $membre ?>" />
<input type="hidden" name="saisielangue" value="<?php print $langue ?>" />
<input type="hidden" name="csrf_token" value="<?php print $csrf_verrou ?>" />
<input type="hidden" name=info_nav>
<script language=JavaScript>document.inscripform.info_nav.value=nom;</script>
</form>
</div>

<div class="msg-wrapper">
  <div class="msg-footer">
    <div class="msg-pub-label">TRIADE-ACTU</div>
    <div class="msg-pub-wrap"><?php top_p(); ?></div>
  </div>
</div>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</BODY>
</HTML>
