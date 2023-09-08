<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if (file_exists("./common/config.centralStage.php")) {
        $productid=$_GET["productid"];
        $p=$_GET["p"];
        $cnx=cnx();
        verifAccesCentrale("$productid","$p");
        PgClose($cnx);
}
$cnx=cnx();
$data=periodeStageCentralDate();
PgClose($cnx);
// datedebut,datefin,id,nomstage
print "document.write(\"<option id='select0' value='' >Choix...</option>\");\n";
for($i=0;$i<count($data);$i++) {
        if (count($data) > 0) {
                for ($i=0;$i<count($data);$i++) {
                        $dateDebut=$data[$i][0];
                        $datefin=$data[$i][1];
                        $nomstage=$data[$i][3];
                        $num=$data[$i][2];
                        $value=$data[$i][2]."#||#$dateDebut#||#$datefin#||#$nomstage#||#$num";
                        print "document.write(\"<option id='select1' value='$value' >(".$data[$i][3].") ".dateForm($data[$i][0])." - ".dateForm($data[$i][1])."</option>\");\n";
                }
        }else{
                echo "";
        }
}
?>
