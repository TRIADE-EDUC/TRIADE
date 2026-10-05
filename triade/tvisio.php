<?php
session_start();
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();

if (empty($_SESSION['membre'])) { header('Location: ./index1.php'); exit; }

$membre    = $_SESSION['membre'];
$peutCreer = in_array($membre, ['menuprof', 'menuadmin', 'menuscolaire', 'menupersonnel']);

$page = isset($_GET['p']) ? $_GET['p'] : 'rooms';
if ($page === 'create' && !$peutCreer) $page = 'rooms';

$iframeMap = [
    'create'    => './visio/index.php',
    'dashboard' => './visio/dashboard.php',
];
$iframeUrl = $iframeMap[$page] ?? './visio/rooms.php';

@Pgclose();
?>
<HTML>
<HEAD>
<title>Triade — Visioconférence</title>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<style>
#visio-frame {
    width: 100%;
    border: none;
    min-height: 600px;
    display: block;
}
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php echo $membre; ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php echo $membre; ?>1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>&#128249; Visioconférence</font></b></td></tr>
<tr id='cadreCentral0'><td>

<iframe id="visio-frame"
        src="<?php echo htmlspecialchars($iframeUrl); ?>"
        scrolling="auto"
        onload="autoResize(this)">
</iframe>

</td></tr></table>

<?php
if ($membre == "menuadmin") {
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$membre."2.js'></SCRIPT>";
} else {
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$membre."22.js'></SCRIPT>";
    top_d();
    echo "<SCRIPT language='JavaScript' src='./librairie_js/".$membre."33.js'></SCRIPT>";
}
?>

<script>
function autoResize(iframe) {
    try {
        iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
    } catch(e) {}
}
window.addEventListener('message', function(e) {
    if (e.data && e.data.type === 'visio-resize') {
        document.getElementById('visio-frame').style.height = e.data.h + 'px';
    }
    if (e.data && e.data.type === 'visio-nav') {
        document.getElementById('visio-frame').src = e.data.url;
    }
});
</script>
</BODY>
</HTML>
