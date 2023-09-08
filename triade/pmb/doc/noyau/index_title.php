<html>
<?php

include 'db_doc.php';

$doc=new db_doc();
$doc->parsage();
print $doc->getTitre();
?>
<span class='center'>
<a href="#" onClick="w=open('scheme.gif','scheme','resizable=yes,scrollbars=yes,width=700,height=500'); w.focus(); return false;">Voir le sch&eacute;ma graphique</a>
&nbsp;
<a href="#" onClick="w=open('export_txt_form.php','scheme','resizable=yes,scrollbars=yes,width=700,height=500'); w.focus(); return false;">Exporter en texte mis en forme</a>
&nbsp;
<a href="#" onClick="w=open('export_txt.php','scheme','resizable=yes,scrollbars=yes,width=700,height=500'); w.focus(); return false;">Exporter en texte</a>
</span>
</html>