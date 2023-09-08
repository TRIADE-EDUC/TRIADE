<?php
session_start();
//error_reporting(0);

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


	<br>
	<ul>
	Questionnaire de : <select name='type' id='type' STYLE="color:#000066;background-color:#FCE4BA" />
			<option value='' STYLE="color:#000066;background-color:#FCE4BA" >Type</option>
			<option value='quizz10' STYLE='color:#000066;background-color:#CCCCFF' >Quizz 10 questions</option>
			<option value='quizz20' STYLE='color:#000066;background-color:#CCCCFF' >Quizz 20 questions</option>
			<option value='sujet' STYLE='color:#000066;background-color:#CCCCFF' >Dissertation</option>
			</select> &nbsp;&nbsp; <b><?php print $_GET["matiere"] ?> / <?php print $_GET['classe'] ?></b>
	<br><br>
	Th&eacute;matique du questionnaire : <input type='text' placeholder='Indiquer quelques mots cl&eacute;s ' size='30' id='question' />
	<input type='button' value='Envoyer' class="button" onClick="ajaxDevoirEns(document.getElementById('question').value,'<?php print $productID ?>','<?php print $iakey ?>','reponse',document.getElementById('type').options[document.getElementById('type').selectedIndex].value,'<?php print $_GET["matiere"] ?>','<?php print $_GET['classe'] ?>')" />
	<br><br>
	<div id='reponse' style="border-radius:30px;background-color:#F3F6FC;border:solid;border-width:1px;height:70%;width:90%;padding:13px;overflow-x:hidden;overflow-y:auto;"  ></div>	


	</ul>
	</body>
</html>
