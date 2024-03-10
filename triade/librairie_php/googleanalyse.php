<!-- Matomo -->
<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="https://analytics.triade-educ.net/matomo/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '5']);
<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$idReference=recupcomptegoogleanalytic();
if (trim($idReference) != "") { ?>
    var websiteIdDuplicate = <?php print $idReference ?>;
    _paq.push(['addTracker', u+'matomo.php', websiteIdDuplicate]);
<?php } ?>
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->


