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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Trombinoscope</title>
<style>
body { padding: 16px; }
.photo-card { max-width: 480px; margin: 0 auto; }
.photo-identity { display: flex; gap: 16px; align-items: flex-start; margin-bottom: 16px; }
.photo-identity img { box-shadow: 0 4px 12px rgba(0,0,0,0.3); border-radius: 8px; flex-shrink: 0; }
.photo-identity-info { font-size: 12px; line-height: 2; }
.photo-identity-info strong { color: #080A66; }
</style>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
if (($_SESSION["membre"] == "menueleve") && (MODIFTROMBIELEVE == "oui" )) {
	$_GET['idelevesupp']=$_SESSION["id_pers"];
}else{
	validerequete("3");
}
$cnx=cnx();

if (isset($_GET["idelevesupp"])) {
	$photoLocal=recherche_photo_eleve($_GET["idelevesupp"]);
	@unlink("./data/image_eleve/$photoLocal");
	$nomeleve=recherche_eleve_nom($_GET["idelevesupp"]);
	$prenomeleve=recherche_eleve_prenom($_GET["idelevesupp"]);
	history_cmd($_SESSION["nom"],"PHOTO","SUPPRESSION $nomeleve $prenomeleve");
}

if (isset($_POST["create"])) {
	$photo=$_FILES['photo']['name'];
	$type=$_FILES['photo']['type'];
	$tmp_name=$_FILES['photo']['tmp_name'];
	$size=$_FILES['photo']['size'];

	$taille = getimagesize($tmp_name);
	if ((!empty($photo)) &&  ($size <= 2000000)) {
		$type=str_replace("image/","",$type);
		$type=str_replace("pjpeg","jpg",$type);
		$type=str_replace("x-png","png",$type);
		$type=str_replace("jpeg","jpg",$type);
		if (verifImageJpg($type))  {
			$nomphoto="$_POST[ideleve].$type";
			move_uploaded_file($tmp_name,"data/image_eleve/$nomphoto");
			history_cmd($_SESSION["nom"],"PHOTO","AJOUT $nomphoto");
			modif_photo($nomphoto,$_POST["ideleve"]);
			print "<script>alertify.success('Photo enregistrée'); setTimeout(function(){parent.window.close();},1500);</script>";
		 }else{
			print "<div class='alert alert-danger' style='margin-bottom:8px'>".LANGTRONBI3."</div>";
		 }
	} else {
		print "<div class='alert alert-danger' style='margin-bottom:8px'>".LANGTRONBI4."</div>";
	}
}

if (isset($_GET["idelevesupp"]))  { $ideleve=$_GET["idelevesupp"]; }
if (isset($_GET["ideleve"]))  { $ideleve=$_GET["ideleve"]; }
if (isset($_POST["ideleve"])) { $ideleve=$_POST["ideleve"]; }
?>

<div class="photo-card">
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="bi bi-camera-fill"></i> Photo élève</span>
    </div>
    <div style="padding:16px">
      <div class="photo-identity">
        <div style="position:relative">
          <img src="image_trombi.php?idE=<?php print $ideleve?>" width='96' height='96' />
        </div>
        <div class="photo-identity-info">
          <?php print LANGTRONBI5 ?> : <strong><?php print recherche_eleve_nom($ideleve); ?></strong><br>
          <?php print LANGTRONBI6?> : <strong><?php print recherche_eleve_prenom($ideleve); ?></strong><br>
          <?php print LANGELE4 ?> : <strong><?php $idclasse=chercheIdClasseDunEleve($ideleve); print chercheClasse_nom($idclasse); ?></strong>
        </div>
      </div>
      <form method='post' ENCTYPE="multipart/form-data" action="photoajouteleve.php">
        <div class="form-row" style="margin-bottom:8px">
          <label style="font-size:12px;margin-bottom:4px;display:block"><?php print LANGTRONBI7 ?> <span style="color:#888;font-size:11px">(JPG, max 2 Mo, recommandé 96×96 px)</span></label>
          <input type="file" name="photo" class="form-control" style="font-size:12px">
        </div>
        <div class="toolbar" style="margin-top:12px">
          <script language=JavaScript>buttonMagicSubmit('<?php print LANGBT46?>','create');</script>
          <script language=JavaScript>buttonMagicFermeture();</script>
        </div>
        <input type='hidden' name='ideleve' value="<?php print $ideleve ?>" />
      </form>
    </div>
  </div>
</div>
<BR>
<?php Pgclose(); ?>
</BODY></HTML>
