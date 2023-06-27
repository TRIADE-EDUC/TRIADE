<?php
function googleanalyse($idReference) {
?>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php print $idReference ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?php print $idReference ?>');
</script>

<?php 
}
?>
