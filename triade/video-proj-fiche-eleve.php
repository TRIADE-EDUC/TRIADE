<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<title>Triade Vidéo-Projecteur</title>
<style>
* { box-sizing: border-box; }
html, body { height: 100%; margin: 0; padding: 0; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; overflow: hidden; background: #fff; }
.fe-wrap {
  display: flex; align-items: center; height: 100%;
  background: linear-gradient(90deg,#f0f2fa 0%,#fff 60%);
  padding: 0 12px 0 10px; gap: 12px;
}
.fe-photo img {
  width: 80px; height: 80px; border-radius: 50%;
  object-fit: cover; border: 3px solid #fff;
  box-shadow: 0 3px 10px rgba(8,10,102,.25);
  display: block;
}
.fe-info { flex: 1; min-width: 0; }
.fe-name { font-size: 16px; font-weight: 700; color: #080A66; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fe-prenom { font-size: 13px; color: #444; margin-top: 2px; }
.fe-age { font-size: 11px; color: #666; margin-top: 4px; }
.fe-badges { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 7px; }
.fe-badge { font-size: 10px; padding: 2px 7px; border-radius: 10px; font-weight: 600; }
.fe-badge-oui { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
.fe-badge-non { background: #f5f5f5; color: #999; border: 1px solid #e0e0e0; }
.fe-empty { text-align: center; width: 100%; color: #888; font-size: 13px; padding: 20px; }
</style>
</head>
<body>
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
validerequete("7");
$cnx=cnx();
$ideleve=$_GET["saisie_eleve"];
$idclasse=$_GET["saisie_classe"];
?>
<div class="fe-wrap">
<?php
if ($ideleve == "") {
  print "<div class='fe-empty'><i class='bi bi-person-x'></i> Aucun élève dans cette classe</div>";
}else{
  $sql="SELECT elev_id,nom,prenom,c.libelle,lv1,lv2,`option`,regime,date_naissance,numero_eleve,boursier,cdi,bde FROM {$prefixe}eleves, {$prefixe}classes c WHERE elev_id='$ideleve' AND c.code_class='$idclasse'";
  $res=execSql($sql);
  $data=chargeMat($res);
  if (countTriade($data) <= 0) {
    print "<div class='fe-empty'>Données introuvables</div>";
  }else{
    $boursier=$data[0][10] ? LANGOUI : LANGNON;
    $cdi=$data[0][11] ? LANGOUI : LANGNON;
    $bde=$data[0][12] ? LANGOUI : LANGNON;
    $dateage=dateForm($data[0][8]);
    $age=($dateage=="00/00/0000") ? "??" : calculAge($dateage);
    ?>
    <div class="fe-photo">
      <img src="image_trombi.php?idE=<?php print $ideleve ?>" alt="">
    </div>
    <div class="fe-info">
      <div class="fe-name"><?php print strtoupper(trim($data[0][1])) ?></div>
      <div class="fe-prenom"><?php print ucwords(trim($data[0][2])) ?></div>
      <div class="fe-age"><i class="bi bi-calendar-event"></i> <?php print ($dateage=="00/00/0000")?"??/??/????" : $dateage ?> &nbsp;(<?php print $age ?> ans)</div>
      <div class="fe-badges">
        <span class="fe-badge <?php print $data[0][10]?'fe-badge-oui':'fe-badge-non' ?>"><i class="bi bi-award"></i> Boursier : <?php print $boursier ?></span>
        <span class="fe-badge <?php print $data[0][11]?'fe-badge-oui':'fe-badge-non' ?>"><i class="bi bi-book"></i> CDI : <?php print $cdi ?></span>
        <span class="fe-badge <?php print $data[0][12]?'fe-badge-oui':'fe-badge-non' ?>"><i class="bi bi-people"></i> BDE : <?php print $bde ?></span>
      </div>
    </div>
    <?php
  }
}
?>
</div>
<?php Pgclose(); ?>
</body>
</html>
