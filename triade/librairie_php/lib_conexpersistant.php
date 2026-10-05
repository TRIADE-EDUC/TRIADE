<?php
function connexpersistance($style='') {
    print "<script type='text/javascript' src='./librairie_js/ajax-time.js'></script>";
    $css = $style ? $style : "display:inline-flex;align-items:center;gap:4px;font-size:10px;color:#6c757d;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;padding:2px 7px;border-radius:10px;background:#f0f2fa;border:1px solid #e8eaf6;vertical-align:middle";
    print "<span name='info-time' id='info-time' style='$css'></span>";
    print "<script type='text/javascript'>";
    print "function boucle(){";
    print "  ConnexPersistant();";
    print "  setTimeout('boucle()',300000);";
    print "}";
    print "boucle();";
    print "</script>";
}
?>
