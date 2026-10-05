<?php
session_start();
error_reporting(0);

if (empty($_SESSION["nom"]))  {
    header('Location: acces_refuse.php');
    exit;
}



?>
<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <?php include_once("./common/config5.inc.php") ?>
                <meta http-equiv="Content-type" content="text/html; charset=<?php print CHARSET; ?>" />
                <meta http-equiv="CacheControl" content="no-cache" />
                <meta http-equiv="pragma" content="no-cache" />
                <meta http-equiv="expires" content="-1" />
                <meta name="Copyright" content="Triade©, 223" />
                <link rel="SHORTCUT ICON" href="./favicon.ico" />
                <link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css.css" />
                <link title="style" type="text/css" rel="stylesheet" href="./librairie_css/css-v4.css" />
                <style>
                .ia-wrap { max-width:720px; margin:14px auto; padding:0 10px; font-family:Electrolize,Trebuchet MS,Arial,sans-serif; }
                .ia-card { background:#fff; border:1px solid #dde0f0; border-radius:10px; padding:10px 14px; margin-bottom:12px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
                .ia-lbl  { font-size:13px; font-weight:700; color:#080A66; white-space:nowrap; }
                .ia-subject { font-size:13px; font-weight:700; color:#283593; background:#eef0fb; border:1px solid #c5cae9; border-radius:5px; padding:4px 10px; white-space:nowrap; }
                .btn-ia { background:#2e7d32; color:#fff; border:none; border-radius:6px; padding:7px 16px; font-size:12px; font-weight:700; cursor:pointer; white-space:nowrap; transition:background .12s; font-family:Electrolize,Trebuchet MS,Arial,sans-serif; }
                .btn-ia:hover { background:#388e3c; }
                .ia-response { border-radius:10px; background:#f3f6fc; border:1px solid #dde0f0; min-height:120px; padding:13px 16px; overflow-x:hidden; overflow-y:auto; font-size:13px; line-height:1.55; }
                </style>
                <title>Triade - Compte de <?php print stripslashes("$_SESSION[nom] $_SESSION[prenom] ") ?></title>
        	<script type="text/javascript" src="./librairie_js/ajaxIA.js"></script>
        	<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
	        <script type="text/javascript" src="./librairie_js/function.js"></script>
	        <script type="text/javascript" src="./librairie_js/lib_css.js"></script>
        </head>

        <body  id='bodyfond'  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >

<?php
if(file_exists("./common/config-ia.php")) {
        include_once("common/productId.php");
        include_once("common/config-ia.php");
        $productID=PRODUCTID;
        $iakey=IAKEY;
}
?>


	<div class="ia-wrap">
	  <div class="ia-card">
	    <span class="ia-lbl">Type :</span>
	    <select id='type' class='cc-select' style="flex:1;max-width:230px;">
	      <option value='' style="color:#000066;background-color:#FCE4BA">Type</option>
	      <option value='quizz10' style="color:#000066;background-color:#CCCCFF">Quizz 10 questions</option>
	      <option value='quizz20' style="color:#000066;background-color:#CCCCFF">Quizz 20 questions</option>
	      <option value='sujet' style="color:#000066;background-color:#CCCCFF">Dissertation</option>
	      <option value='trou' style="color:#000066;background-color:#CCCCFF">Texte à trous</option>
	    </select>
	    <span class="ia-subject"><?php print $_GET["matiere"] ?> / <?php print $_GET['classe'] ?></span>
	  </div>
	  <div class="ia-card">
	    <span class="ia-lbl">Thématique :</span>
	    <input type='text' placeholder='Indiquer quelques mots clés' id='question' class='bouton2' style="flex:1;min-width:200px;" />
	    <button type='button' class='btn-ia' onClick="ajaxDevoirEns(document.getElementById('question').value,'<?php print $productID ?>','<?php print $iakey ?>','reponse',document.getElementById('type').options[document.getElementById('type').selectedIndex].value,'<?php print $_GET["matiere"] ?>','<?php print $_GET['classe'] ?>')">Générer</button>
	  </div>
	  <div id='reponse' class='ia-response'></div>
	</div>
	</body>
</html>
