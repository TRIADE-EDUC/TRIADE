<?php
print "<script language='javascript' src='./messagerie/mc.js'></script>";
print "<script language='javascript' src='./messagerie/messagerie.js'></script>";
print "<div ID='null' style='position:relative;top=0px;left=0px;height:250' width=50%>";
print "<textarea name='Body' style='visibility:hidden;position:absolute;top:0px;left:0px'></textarea>";
print "<script language='javascript'>";
print "        var idGenerator = new IDGenerator(0);";
print "        var editor = new Editor(idGenerator);";
print "        editor.Instantiate();";
print "</script></div>";
?>
