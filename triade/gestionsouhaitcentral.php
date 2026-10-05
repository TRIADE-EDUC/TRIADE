<?php
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='cadreCentral0' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include_once("./librairie_php/db_triade.php"); ?>

<div class="dest-wrap" style="margin:10px auto;max-width:460px">
<div class="dest-list">

  <form action='gestion_central_stage_visu.php' method='post' target="_top" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <i class="bi bi-eye" style="color:#080A66;margin-right:5px"></i>
        Souhaits en cours
      </span>
      <script language=JavaScript>buttonMagicSubmit("Accès","rien");</script>
    </div>
  </form>

<?php if (file_exists("./common/config.centralStage.php")) { ?>

  <form action='gestion_central_stage_ajout.php' method='post' target="_top" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <i class="bi bi-plus-circle" style="color:#080A66;margin-right:5px"></i>
        Ajouter un souhait
      </span>
      <script language=JavaScript>buttonMagicSubmit("Accès","rien");</script>
    </div>
  </form>

  <form action='gestion_central_stage_config_mail.php' method='post' target="_top" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <i class="bi bi-envelope-gear" style="color:#080A66;margin-right:5px"></i>
        Config. envoi d'email aux entreprises
      </span>
      <script language=JavaScript>buttonMagicSubmit("Accès","rien");</script>
    </div>
  </form>

<?php } ?>

</div>
</div>

</BODY></HTML>
