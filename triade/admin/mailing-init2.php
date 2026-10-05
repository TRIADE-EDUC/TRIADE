<?php
session_start();
include_once("./librairie_php/lib_licence.php");
@unlink("../common/config-mailing.php");
header("Location:mailing.php?init");
?>
