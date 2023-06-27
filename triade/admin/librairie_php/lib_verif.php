<?php
//----------------------------------------------------------------------------
// section pour un acces non autorise
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");

if (empty($_SESSION["admin1"])) {
    print "<script language='javascript'>";
    print "location.href='/".REPECOLE."/".REPADMIN."/acces_refuse.php'";
    print "</script>";
    exit;
}
?>
