<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$prefixe=PREFIXE;
if (file_exists("./common/config.centralStage.php")) {
        $productid=$_GET["productid"];
        $p=$_GET["p"];
        $cnx=cnx();
        verifAccesCentrale("$productid","$p");
        PgClose($cnx);
}
$cnx=cnx();
$sql="SELECT id_serial,nom FROM ${prefixe}stage_entreprise ORDER BY nom ";
$data=ChargeMat(execSql($sql));
PgClose($cnx);
for($i=0;$i<count($data);$i++) {
        $nom=$data[$i][1];
        $value=$data[$i][0];
        $nomcourt=trunchaine($nom,25);
        print "document.write(\"<option id='select1' value='CS:$value' title=\\\"$nom\\\" >$nomcourt</option>\");\n";
}
?>
