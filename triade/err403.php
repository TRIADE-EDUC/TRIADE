<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<!DOCTYPE html>
<html lang="fr">
<?php
error_reporting(0);
include_once("./common/lib_admin.php");
include_once("./common/lib_ecole.php");
?>
<head>
<meta charset="UTF-8">
<meta http-equiv="CacheControl" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="/<?php print REPECOLE?>/librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="/<?php print REPECOLE?>/librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="/<?php print REPECOLE?>/librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<title>403 — Accès interdit</title>
<style>
body { min-height: 100vh; background: #eef0f5; display: flex; flex-direction: column; align-items: center; justify-content: center; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; margin: 0; }
.err-card  { background: #fff; border-radius: 12px; width: 380px; max-width: 94vw; box-shadow: 0 4px 24px rgba(0,0,0,0.10); overflow: hidden; text-align: center; }
.err-top   { height: 6px; background: linear-gradient(90deg, #b71c1c, #ef5350); }
.err-inner { padding: 36px 32px 28px; }
.err-icon  { font-size: 48px; color: #ef5350; margin-bottom: 12px; }
.err-code  { font-size: 56px; font-weight: 900; color: #c62828; line-height: 1; margin-bottom: 6px; }
.err-title { font-size: 17px; font-weight: 700; color: #333; margin-bottom: 8px; }
.err-msg   { font-size: 13px; color: #888; margin-bottom: 8px; line-height: 1.5; }
.err-sub   { font-size: 12px; color: #bbb; margin-bottom: 24px; font-style: italic; }
.err-footer { margin-top: 28px; padding-top: 16px; border-top: 1px solid #eef0f5; font-size: 11px; color: #bbb; }
</style>
</head>
<body>
<div class="err-card">
  <div class="err-top"></div>
  <div class="err-inner">
    <div class="err-icon"><i class="bi bi-slash-circle"></i></div>
    <div class="err-code">403</div>
    <div class="err-title">Accès interdit</div>
    <div class="err-msg">Vous n'avez pas les droits nécessaires pour accéder à cette page.</div>
    <div class="err-sub">Un rapport a été envoyé à l'administration.</div>
    <button class="btn btn-secondary" onclick="history.go(-1)">
      <i class="bi bi-arrow-left"></i>&nbsp; Retour
    </button>
    <div class="err-footer">
      T.R.I.A.D.E. &copy; 2000/<?php echo date('Y'); ?> &mdash; Tous droits réservés
    </div>
  </div>
</div>
<?php
$date = date("d/m/Y \à G:i");
$fp = fopen("./data/error.log", "a+");
fwrite($fp, "<font color=red>Erreur Type : 403 acces Interdit </font>- <br> Visité le $date par ".$_SERVER["REMOTE_ADDR"]." <BR>avec ".$_SERVER["HTTP_USER_AGENT"]." <BR> ".$_SERVER["REDIRECT_URL"]."</font><BR><hr><br>\n");
fclose($fp);
?>
</body>
</html>
