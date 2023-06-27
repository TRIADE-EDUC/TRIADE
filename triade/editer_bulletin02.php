<?php
session_start();
setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
setcookie("saisie_trimestre",$_POST["saisie_trimestre"],time()+36000*24*30);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
$trimes=$_POST['saisie_trimestre'];
$idclasse=$_POST["saisie_classe"];
$typecom=$_POST["typecom"];
header("Location:editer_bulletin2.php?apres=0&saisie_trimestre=$trimes&saisie_classe=$idclasse&typecom=$typecom");
exit();
?>
