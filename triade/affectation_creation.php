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
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<title>Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./affectation_creation_key.php'</script>";
}
include_once('librairie_php/db_triade.php');
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE16; ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<div style="margin:12px 8px;">

<!-- ── Section 1 : Création ── -->
<div style="background:#f5f7ff;border:1px solid #c5cae9;border-radius:8px;padding:14px 18px;margin-bottom:14px;">

    <div style="padding:10px 14px;background:#fff3f3;border:1px solid #f5c6c6;border-radius:6px;font-size:11px;font-weight:700;font-family:Electrolize,Trebuchet MS,Arial;color:#c0392b;margin-bottom:14px;">
        &#9888; IMPORTANT : la création d'affectation supprime toutes les informations de notation de la nouvelle classe concernée.
    </div>

    <form method="post" onsubmit="return affectation_classe2()" name="formulaire" action="affectation_creation2.php">

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            <span style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;min-width:160px;"><?php echo LANGAFF1; ?> :</span>
            <select name="saisie_classe_envoi" class="cc-select">
                <option value="0"><?php echo LANGCHOIX; ?></option>
                <optgroup label="Classe">
                <?php select_classe(); ?>
                </optgroup>
            </select>
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            <span style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;min-width:160px;"><?php echo LANGPER14; ?> :</span>
            <input type="text" name="saisie_nb_matiere" size="3"
                   style="font-size:12px;border:1px solid #c5cae9;border-radius:4px;padding:3px 8px;width:50px;">
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            <span style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;min-width:160px;">Année / Trimestre / Semestre :</span>
            <select name="saisie_tri" class="cc-select">
                <option value="tous" selected>Toute l'année</option>
                <option value="trimestre1">Trimestre 1 / Semestre 1</option>
                <option value="trimestre2">Trimestre 2 / Semestre 2</option>
                <option value="trimestre3">Trimestre 3</option>
            </select>
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
            <span style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;min-width:160px;"><?php echo LANGMESS145; ?> :</span>
            <select name="anneeScolaire" class="cc-select">
                <option value=""><?php echo LANGCHOIX; ?></option>
                <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
            </select>
        </div>

        <input type="submit" name="rien" value="<?php echo LANGBT19; ?>" class="btn-enr">

    </form>
</div>

<hr style="border:none;border-top:1px solid #c5cae9;margin:0 0 14px;">

<!-- ── Section 2 : Copie ── -->
<div style="background:#f5f7ff;border:1px solid #c5cae9;border-radius:8px;padding:14px 18px;">

    <div style="padding:10px 14px;background:#fff3f3;border:1px solid #f5c6c6;border-radius:6px;font-size:11px;font-weight:700;font-family:Electrolize,Trebuchet MS,Arial;color:#c0392b;margin-bottom:14px;">
        &#9888; IMPORTANT : la copie d'affectation supprime toutes les informations de notation de la nouvelle classe concernée.
    </div>

    <form method="post" action="affectation_creation_copy.php">

        <div style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#333;margin-bottom:10px;font-weight:700;">
            Copier l'affectation de :
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            <span style="font-size:12px;color:#444;font-family:Electrolize,Trebuchet MS,Arial;min-width:80px;">Classe source :</span>
            <select name="saisie_classe_source" class="cc-select">
                <option value="0"><?php echo LANGCHOIX; ?></option>
                <optgroup label="Classe">
                <?php select_classe(); ?>
                </optgroup>
            </select>
            <span style="font-size:12px;color:#444;font-family:Electrolize,Trebuchet MS,Arial;">Année :</span>
            <select name="anneeScolaireSource" class="cc-select">
                <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
            </select>
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
            <span style="font-size:12px;color:#444;font-family:Electrolize,Trebuchet MS,Arial;min-width:80px;">Classe dest. :</span>
            <select name="saisie_classe_destination" class="cc-select">
                <option value="0"><?php echo LANGCHOIX; ?></option>
                <optgroup label="Classe">
                <?php select_classe(); ?>
                </optgroup>
            </select>
            <span style="font-size:12px;color:#444;font-family:Electrolize,Trebuchet MS,Arial;">Année :</span>
            <select name="anneeScolaireDest" class="cc-select">
                <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
            </select>
        </div>

        <input type="submit" name="rien" value="Copier l'affectation" class="btn-enr">

    </form>

    <?php if (isset($_GET["errorcopy"])): ?>
    <div style="margin-top:12px;padding:10px 14px;background:#fff3f3;border:1px solid #f5c6c6;border-radius:6px;font-size:12px;font-weight:700;font-family:Electrolize,Trebuchet MS,Arial;color:#c0392b;">
        &#9888; Erreur lors de la copie.
    </div>
    <?php endif; ?>

</div>

</div><!-- /margin -->

</td></tr></table>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."2.js'"; ?>></SCRIPT>
</body></html>
