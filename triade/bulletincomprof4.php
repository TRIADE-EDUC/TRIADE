<?php
session_start();
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");

if(isset($_POST["nb"])) {
	$cnx=cnx();
	$nb=$_POST["nb"];
	$tri=$_POST["choix_trimestre"];
	$idclasse=$_POST["saisie_classe"];
	$idmatiere=$_POST["saisie_matiere"];
	$idgroupe=$_POST["saisie_groupe"];
	$typecom=$_POST["typecom"];
	$biblio=$_POST["bibli"];
	$anneeScolaire=$_POST["anneeScolaire"];
	if (defined("NBCARBULL")) { $nbcar=NBCARBULL; }else{ $nbcar=400; }
	if ($typecom > 0) { $nbcar=150; }
	for($i=0;$i<$nb;$i++) {
		$saisie_eleve="saisie_eleve_".$i;
		$saisie_com="saisie_text_".$i;
		$idEleve=$_POST[$saisie_eleve];
		$commentaire=trim($_POST[$saisie_com]);
		$commentaire=preg_replace('/^, /','',$commentaire);
		if ($biblio == "oui") {
			if (trim($commentaire) != "") {
				create_com_bulletin($commentaire,$_SESSION["id_pers"]);
			}
		}
		$commentaire=trunchaine($commentaire,$nbcar);
		enregistrement_com_bulletin($idmatiere,$idclasse,$tri,$idEleve,$commentaire,$_SESSION["id_pers"],$idgroupe,$typecom,$anneeScolaire);
	}
	$nomclasse=chercheClasse_nom($idclasse);
	$nommatiere=chercheMatiereNom($idmatiere);
	history_cmd($_SESSION["nom"],"CREATION","Commentaire Bulletin $nomclasse $nommatiere");
	Pgclose();
}
?>
<HTML>
<HEAD>
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script src="./librairie_js/function.js"></script>
<script src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/clickdroit2.js"></script>
<style>
.bc4-wrap {
  display:flex;
  align-items:flex-start;
  justify-content:center;
  padding-top:60px;
  min-height:100vh;
  box-sizing:border-box;
}
.bc4-card {
  background:#fff;
  border-radius:10px;
  box-shadow:0 4px 24px rgba(8,10,102,.13);
  padding:40px 48px 32px;
  text-align:center;
  max-width:420px;
  width:90%;
}
.bc4-icon {
  font-size:48px;
  color:#2e7d32;
  margin-bottom:16px;
  display:block;
}
.bc4-msg {
  font-family:Electrolize,Arial,sans-serif;
  font-size:15px;
  color:#080A66;
  font-weight:600;
  margin-bottom:28px;
  background:#fff;
  padding:10px 16px;
  border-radius:6px;
}
</style>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" style="background:#CACCEF">
<?php include("./librairie_php/lib_licence.php"); ?>
<div class="bc4-wrap">
  <div class="bc4-card">
    <i class="bi bi-check-circle-fill bc4-icon"></i>
    <div class="bc4-msg"><?php print LANGRESA69 ?>.</div>
    <script>buttonMagic("<?php print LANGMESS138 ?>","bulletincomprof.php","_parent","","");</script>
  </div>
</div>
</body>
</html>
