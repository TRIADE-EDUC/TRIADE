<?php
session_start();
error_reporting(0);
?>
<html>
<head>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script type="text/javascript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit2.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./tinymce/tinymce.min.js"></script>
</head>
<body>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

$idpers=$_SESSION['id_pers'];
$membre=$_SESSION['membre'];
$libelle="Sign_$idpers_$membre";

if (isset($_POST['create'])) {
	$valeur=$_POST['memo'];
	$info="";
	enr_parametrage($libelle,$valeur,$info);	
	$contenu=$valeur;
}else{
	$contenu=aff_valeur_parametrage($libelle);
}

$contenu=preg_replace('/\\\r\\\n/','',$contenu);


?>

<form method=post name="form" >
<script>
tinymce.init({
  selector: 'textarea#default-editor',
  plugins: 'image emoticons link table',
//  plugins: 'ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
  toolbar: 'undo redo | fontfamily fontsize | bold italic underline | forecolor backcolor',
  color_cols:5,
  menubar: 'file edit view insert format tools table help',
  height: 300,
  protect: [
        /\<\/?(if|endif)\>/g,
        /\<xsl\:[^>]+\>/g,
        /<\?php.*?\?>/g,
        /\<script/ig,
        /<\?.*\?>/g
        ]
});
</script>


<textarea id="default-editor" name='memo'  >
<?php print $contenu ?>
</textarea>
<br><br>
<table align='center' ><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","create");</script></td></tr></table>
<form>

</body>
</html>
