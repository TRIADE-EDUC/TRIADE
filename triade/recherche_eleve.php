<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/rechercheV4.js"></script>
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
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx = cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE30; ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<form method="post" onsubmit="return valide_recherche_eleve()" name="formulaire" id="formulaire">
<div style="margin:12px 8px 6px;display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap;">
    <div>
        <label style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;">
            <?php echo LANGABS3; ?> :
        </label><br>
        <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
            <input type="text" name="saisie_nom_eleve" id="search" size="20" autocomplete="off"
                   onkeyup="searchRequestV4('search','eleve','resultats','formulaire','saisie_nom_eleve')"
                   style="font-size:12px;border:1px solid #c5cae9;border-radius:4px;padding:4px 8px;width:180px;">
            <input type="submit" name="create" value="<?php echo LANGBT39; ?>" class="btn-enr">
        </div>
        <div id="resultats" style="width:195px;background:#f5f5f5;border:1px solid #c5cae9;border-top:none;border-radius:0 0 4px 4px;font-size:12px;"></div>
    </div>
</div>
</form>

</td></tr></table>

<?php if (isset($_POST["saisie_nom_eleve"])): ?>
<?php
$saisie_nom_eleve = trim($_POST["saisie_nom_eleve"]);
$motif = strtolower($saisie_nom_eleve);
$sql = "SELECT c.libelle, e.nom, e.prenom, e.elev_id
        FROM {$prefixe}eleves e, {$prefixe}classes c
        WHERE lower(e.nom) LIKE '%$motif%'
        AND c.code_class = e.classe
        ORDER BY c.libelle, e.nom, e.prenom";
$res  = execSql($sql);
$data = chargeMat($res);
?>

<div style="margin:14px 8px 0;">
    <div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:7px 14px;font-size:12px;font-weight:700;font-family:Electrolize,Trebuchet MS,Arial;">
        <?php echo LANGRECH2; ?> : <em style="font-style:normal;color:#c5cae9;"><?php echo htmlspecialchars(ucwords(stripslashes($motif))); ?></em>
    </div>
    <div style="border:1px solid #c5cae9;border-top:none;border-radius:0 0 8px 8px;background:#fff;">
    <?php if (countTriade($data) <= 0): ?>
        <div style="padding:14px;font-size:12px;color:#666;font-family:Electrolize,Trebuchet MS,Arial;text-align:center;">
            <?php echo LANGRECH3; ?>
        </div>
    <?php else: ?>
        <table class="cc-data-table">
            <thead>
                <tr class="cc-thead-row">
                    <th class="cc-th"><?php echo ucwords(LANGIMP10); ?></th>
                    <th class="cc-th"><?php echo LANGIMP8; ?></th>
                    <th class="cc-th"><?php echo LANGIMP9; ?></th>
                </tr>
            </thead>
            <tbody>
            <?php for ($i = 0; $i < countTriade($data); $i++): ?>
                <tr class="cc-tr-data">
                    <td class="cc-td"><?php echo htmlspecialchars($data[$i][0]); ?></td>
                    <td class="cc-td">
                        <a href="edit_eleve.php?eid=<?php echo $data[$i][3]; ?>"
                           class="cc-link-classe"><?php echo strtoupper(htmlspecialchars($data[$i][1])); ?></a>
                        <span class="htip-wrap">
                            <span style="font-size:10px;color:#080A66;cursor:pointer;">&#9432;</span>
                            <span class="htip"><?php echo LANGRECH4; ?></span>
                        </span>
                    </td>
                    <td class="cc-td"><?php echo htmlspecialchars(ucwords($data[$i][2])); ?></td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </div>
</div>

<?php endif; ?>

<?php Pgclose(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."2.js'"; ?>></SCRIPT>
</body></html>
