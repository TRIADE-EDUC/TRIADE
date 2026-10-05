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
<title>404 — Page introuvable</title>
<style>
body { min-height: 100vh; background: #eef0f5; display: flex; flex-direction: column; align-items: center; justify-content: center; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; margin: 0; }
.err-card { background: #fff; border-radius: 12px; width: 380px; max-width: 94vw; box-shadow: 0 4px 24px rgba(0,0,0,0.10); overflow: hidden; text-align: center; }
.err-top  { height: 6px; background: linear-gradient(90deg, #e65100, #ff7043); }
.err-inner { padding: 36px 32px 28px; }
.err-icon  { font-size: 48px; color: #ff7043; margin-bottom: 12px; }
.err-code  { font-size: 56px; font-weight: 900; color: #e65100; line-height: 1; margin-bottom: 6px; }
.err-title { font-size: 17px; font-weight: 700; color: #333; margin-bottom: 8px; }
.err-msg   { font-size: 13px; color: #888; margin-bottom: 24px; line-height: 1.5; }
.err-footer { margin-top: 28px; padding-top: 16px; border-top: 1px solid #eef0f5; font-size: 11px; color: #bbb; }
</style>
</head>
<body>
<div class="err-card">
  <div class="err-top"></div>
  <div class="err-inner">
    <div class="err-icon"><i class="bi bi-search"></i></div>
    <div class="err-code">404</div>
    <div class="err-title">Page introuvable</div>
    <div class="err-msg">La page que vous cherchez n'existe pas ou a été déplacée.</div>
    <button class="btn btn-secondary" onclick="history.go(-1)">
      <i class="bi bi-arrow-left"></i>&nbsp; Retour
    </button>
    <div class="err-footer">
      T.R.I.A.D.E. &copy; 2000/<?php echo date('Y'); ?> &mdash; Tous droits réservés
    </div>
  </div>
</div>
</body>
</html>
