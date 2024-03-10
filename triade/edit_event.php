<?php
session_start();
error_reporting(0);

include_once("./librairie_php/timezone.php");
// Input to this file:	$_GET['id'] which is the id of the node that you are about to edit
?>
<html>
<head>
<title>EDT Editer</title>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajaxEDT.js"></script>
<script type="text/javascript" src="./librairie_js/xorax_serialize.js" ></script>
<script>
function active(i) {
	if (i == 1) {
/*
		document.formulaire.periode.value="";
		document.formulaire.elements[7].checked=false;
		document.formulaire.elements[8].checked=false;
		document.formulaire.elements[9].checked=false;
		document.formulaire.elements[10].checked=false;
		document.formulaire.elements[11].checked=false;
		document.formulaire.elements[12].checked=false;
		document.formulaire.elements[13].checked=false;
*/
		document.formulaire.periode.disabled=false;
		document.formulaire.elements[17].disabled=false;
		document.formulaire.elements[16].disabled=false;
		document.formulaire.elements[15].disabled=false;
		document.formulaire.elements[14].disabled=false;
		document.formulaire.elements[11].disabled=false;
		document.formulaire.elements[12].disabled=false;
		document.formulaire.elements[13].disabled=false;

	}else{

		document.formulaire.periode.disabled=true;
		document.formulaire.elements[17].disabled=true;
		document.formulaire.elements[16].disabled=true;
		document.formulaire.elements[15].disabled=true;
		document.formulaire.elements[14].disabled=true;
		document.formulaire.elements[11].disabled=true;
		document.formulaire.elements[12].disabled=true;
		document.formulaire.elements[13].disabled=true;

	}

}
</script>
<style type="text/css">
.colorDiv{
	width:15px;
	height:15px;
	margin:1px;
	float:left;
	border:1px solid #000;
}
.colorDivSelected{
	width:13px;
	height:13px;
	margin:1px;
	float:left;
	border:2px solid #000;

}

.aButton{
	width:75px;
}

.buttonDiv{
	float:right;
	margin-top:5px;
	padding-right:15px;
}
</style>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

// Saving event
if(isset($_POST['save'])){
	$descrip=$_POST['eventDescription'];
	$descrip=preg_replace('/\n/',"<br />",$descrip);
	$descrip=addslashes($descrip);
	miseAJourEdt($_POST['id'],$descrip,$_POST['color'],$_POST['classeID'],$_POST['profID'],$_POST['periode'],$_POST['prestation'],$_POST['jours'],$_POST['recursive'],$_POST['matiereID'],$_POST["coursannule"],$_POST["docdst"],$_POST["reportle"],$_POST["reporta"],$_POST["validecreation"],$_POST["emargement"],$_POST['ressourceID'],$_POST["dureehoraire"],$_POST["debuthoraire"],$_POST["emargementeval"],$_POST["emargementpedago"],$_POST["groupeID"],$_POST["semainesurdeux"]);
	if ($_POST['ressourceID'] != "rien") {
		if (RESERV == "oui") { $confirm=1; }
		create_resa2($_POST['ressourceID'],$_POST['periode'],$_SESSION["id_pers"],$_POST["debuthoraire"],$_POST["dureehoraire"],$descrip,$confirm,$_POST['periode'],$_POST['jours'],$_POST['id']);
	}
	?>
	<script type="text/javascript">self.close();</script>
	<?php
}

if (isset($_POST['recursivesupp'])){
	deleteEdtId($_POST['id'],$_POST["recursivesupp"]);
?>
	<script type="text/javascript">self.close();</script>
	<?php
	exit;
}
?>

<script type="text/javascript">

function ferm() {
	self.close();
}

function confirmSupp() {
	document.formulaire.submit();
}

function confirmSave() {
	document.formulaire.submit();
	if(confirm('RAPPEL : La classe doit être indiqué. \nDans le cas contraire cette fiche sera détruite. \n\n Confirmer l\'enregistrement ?')){
		if(window.opener){
			var id = "<?php echo $_GET['id']; ?>" ;
			var formObj = document.forms[0];			
			opener.setElement_txt(id,formObj.eventDescription.value);	// Calling function in week planner - update content
			if(formObj.color.value.length>0)opener.setElement_color(id,formObj.color.value);	// Calling function in week planner - updating color
		}
		return true;
	}	
	return false;
}

// This function doesn't do anything with the week planner - it only updates color on this page. The confirmSave() function sends the color value back
// to the week planner
var activeColorObj = false;
function selectColor(inputObj,color) {
	if(activeColorObj)activeColorObj.className='colorDiv';
	inputObj.className='colorDivSelected';
	activeColorObj = inputObj;
	document.forms[0].color.value = color; 	
}

</script>
</head>
<body id='bodyfond2'>

<?php
/* This is the place where you will get data from your database for this event */
if(isset($_GET['id'])){
	$donnee=rechercheDescriptionEdt($_GET['id']);
	// code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation,idmatiere,coursannule,docdst,reportle,reporta,emargement,idressource,emargementeval,emargementpedago,idgroupe
}else{
	die("Erreur ! Pas de référence.");
}



/* Values for this example only 
You will typically pull these variables from a database(look above) instead of setting them fixed as I have done in this example.
*/


$date=rechercheFinRecurrence($donnee[0][0]);
$inf = array();
$inf["eventDescription"] = $donnee[0][1] ;
$inf["profID"] = $donnee[0][7] ;
$inf["colorCode"] = $donnee[0][5]; 
$inf["classeID"] = $donnee[0][6] ; 

$colors = array("#CCCCCC","#FFFFFF","#E2EBED","#FF0000","#00CC00","#9999FF","#FF6600","#330066","#FF00FF","#CCFF00","#33FF00","#993300","#333300","#6699FF","#9900FF",'#00FF00','#CC99FF','#808080','#008000','#BFDAA3');	// array of colors the user could choose from
$c=0;
foreach($colors as $key=>$value) {
	if ($donnee[0][5] == $value) { break; }
	$c++;
}

$checkedrecursivenon="checked='checked'";
$checkedrecursiveoui="";
if ($donnee[0][10] == 1){
	$checkedrecursivenon="checked='checked'";
	$checkedrecursiveoui="";
	$disabledrecursive="disabled='disabled'";
}

if (($donnee[0][6] > 0) || ($donnee[0][15] > 0)) {
	if ($donnee[0][6] > 0) {
		$action2="( Modifier les récurrences : oui <input type='radio' name='recursive' id='recursiveoui' value='oui' $disabledrecursive  $checkedrecursiveoui onclick='active(0)' /> non <input type='radio' name='recursive' $checkedrecursivenon id='recursivenon' value='non' onclick='active(1)'  /> )&nbsp;";
	$action="<input type='submit' value='Modifier' name='save' class='BUTTON'  style='width:75px;' >";
	$disabledDate="readonly='readonly'";
	//$disabledJour="disabled='disabled'";
	$choixColor="<script>selectColor(document.getElementById('col$c'),'".$colors[$c]."')</script>";
	}
}else{
	$action2="";
	$disabledDate="";
	$choixColor="";
	$disabledJour="";
	$action="<input type='submit' value='Enregistrer' name='save' class='BUTTON'  style='width:75px;'  >";
}

$heureDebut=$donnee[0][3];
$sec=conv_en_seconde($heureDebut)+(TIMEZONE*3600);
$heureDebut=calcul_hours($sec);

?>

<!-- Example of a form --->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="Post" name="formulaire" >
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
<input type="hidden" name="color" value="<?php print $donnee[0][5] ?>">

	<table border="0" cellpadding="2" cellpadding="0">
		<tr>
		<td align='right'><font class='T2'>Horaire : </font></td><td><input type='text' size='5' name="debuthoraire"  value="<?php print timeForm($heureDebut) ?>" /> <font class='T2'>Durée :</font> <input type='text' size='5' name="dureehoraire"  value="<?php print timeForm($donnee[0][4]) ?>" /> </td>
		
		</tr>
		<tr>
			<td align='right'>
				<label for="eventDescription"><font class='T2'>Informations :</font></label>
			</td>
			<td>
				<?php 
				$desc=$inf["eventDescription"];
				$desc=preg_replace("/\<br \/\>/i","\n",$desc);
				$desc=preg_replace("/\<br\>/i","\n",$desc);
				$desc=preg_replace("/\<br\/\>/i","\n",$desc);
				?>
				<textarea id="eventDescription" name="eventDescription" cols=40 rows=5><?php echo $desc ?></textarea>
			</td>
		</tr>	
		<tr>
			<td align='right'><font class='T2'>Enseignant :</font></td>
			<td><select name="profID">
			  	 <?php if ($_SESSION["membre"] == "menuprof") { ?>
					 <?php print select_personne_uniq($_SESSION["id_pers"]); ?>
				 <?php }else{ ?>
					 <?php print select_personne_uniq($donnee[0][7]); ?>
					 <option value="rien" id='select0'><?php print LANGCHOIX?></option>
					<?php
					if (EDTENSUNIQ == "non") { 
						select_personne('ENS');
					}else{
						select_personne_edt('ENS',"$heureDebut","$date"); // creation des options	
					}
					?>
				<?php } ?>
			</select>
			</td>
		</tr>
		<?php if ($_SESSION["membre"] != "menuprof") { ?>
		<tr>
			<td align='right'><font class='T2'>Matière :</font></td>
			<td><select name="matiereID"  >
				<?php print select_matiere_search($donnee[0][9],23); ?>
			        <option  value="rien" id='select0'><?php print LANGCHOIX?></option>
				<?php
				select_matiere3(23); // creation des options
				?>
			</select>
			</td>
		</tr>
		<tr>
			<td align='right'><font class='T2'>Classe :</font></td>
			<td><select id='classeID' name="classeID" onchange="visugroupe(document.getElementById('classeID').options[selectedIndex].value)" >
				 <?php print select_classe_search($donnee[0][6]); ?>
			          <option  value="rien" id='select0'><?php print LANGCHOIX?></option>
				<?php
				select_classe(); // creation des options
				?>
			</select>
			</td>
		</tr>

		<tr>
			<td align='right'><font class='T2'>Groupe :</font></td>
			<td><select name="groupeID" id='groupeID'>
				 <?php  print select_groupe_search($donnee[0][18]); ?>
			          <option  value="rien" id='select0'><?php print LANGCHOIX?></option>
				<?php
				
				?>
			</select>
			</td>
		</tr>


		<?php } ?>
		<tr>
			<td align='right'><font class='T2'>Ressource :</font></td>
			<td><select name="ressourceID">
				  <?php print select_ressource_search($donnee[0][15]); ?>
			          <option  value="rien" id='select0'><?php print LANGCHOIX?></option>
				  <optgroup label='Equipement' >
				  <?php
					select_equip(); // creation des options
					print "<optgroup label='Salle' >";
					select_salle(); // creation des options
				  ?>
			</select>
			</td>
		</tr>
		<tr>
	
			<td align='right'><font class='T2'>Couleur :</font></td>
			<td><?php
			for($no=0;$no<count($colors);$no++){
				echo "<div class=\"colorDiv\" id='col$no' onclick=\"selectColor(this,'".$colors[$no]."');\" style=\"background-color:".$colors[$no]."\"><span></span></div>\n";
				
			}
				print $choixColor;
			?>
			</td>
		</tr>


		<tr>
			<td align='right'><font class='T2'>Récurrence :</font></td>
			<td> <font class='T2'>jusqu'au </font><input type="text" name="periode" readonly="readonly" size=10 value="<?php print $date ?>" <?php print $disabledDate ?> > 
<?php 
				if ($disabledDate == "") {
				     include_once("librairie_php/calendar.php");
				     calendarDim('id2','document.formulaire.periode',$_SESSION["langue"],"1","0");
				}
				?> </td>
				     
		</tr>

		<tr>
			<td align='right'><font class='T2'>Pour les jours :</font></td>
<?php
		if ($date != "") {
			$jour=date_jour2($date); // "di","lu","ma","me","je","ve","sa"
			switch($jour) {
				case "di" :  $checkDi="checked='checked'" ; break;
				case "lu" :  $checkLu="checked='checked'" ; break;
				case "ma" :  $checkMa="checked='checked'" ; break;
				case "me" :  $checkMe="checked='checked'" ; break;
				case "je" :  $checkJe="checked='checked'" ; break;
				case "ve" :  $checkVe="checked='checked'" ; break;
				case "sa" :  $checkSa="checked='checked'" ; break;
			}
		}
?>
			<td> 
			<table border=0>
<tr>
<td width=5>&nbsp;<?php print LANGL ?></td>
<td>&nbsp;<?php print LANGM ?></td>
<td>&nbsp;<?php print LANGME ?></td>
<td>&nbsp;<?php print LANGJ ?></td>
<td>&nbsp;<?php print LANGV ?></td>
<td>&nbsp;<?php print LANGS ?></td>
<td>&nbsp;<?php print LANGD ?></td>
</tr>
<tr>
<td><input type="checkbox" name="jours[]" value="1" <?php print $checkLu." ".$disabledJour ?>  ></td>
<td><input type="checkbox" name="jours[]" value="2" <?php print $checkMa." ".$disabledJour ?> ></td>
<td><input type="checkbox" name="jours[]" value="3" <?php print $checkMe." ".$disabledJour ?> ></td>
<td><input type="checkbox" name="jours[]" value="4" <?php print $checkJe." ".$disabledJour ?> ></td>
<td><input type="checkbox" name="jours[]" value="5" <?php print $checkVe." ".$disabledJour  ?> ></td>
<td><input type="checkbox" name="jours[]" value="6" <?php print $checkSa." ".$disabledJour ?> ></td>
<td><input type="checkbox" name="jours[]" value="7" <?php print $checkDi." ".$disabledJour ?> ></td>
</tr>
</table>		
</td>
	</tr>

<tr>
<td align='right'><font class='T2'>Semaine sur deux : </font></td>
<td><input type=checkbox value="oui" name="semainesurdeux" /> <i>(oui)</i></td>
</tr>

<?php if ($_SESSION["membre"] != "menuprof") { ?>
		<tr>
			<td align='right'><font class='T2'>Prestation :</font></td>
			<td><select name="prestation">
			<?php 
				print select_EvalHoraire_search($donnee[0][8]); 
		
			?>
			          <option  value="rien" STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
				<?php
				select_EvalHoraire(); // creation des options
				?>
			</select>
			</td>
		</tr>
		<?php 
				if ($donnee[0][10] == 1) { 
					$selectedcoursannuleoui="checked='checked'";$selectedcoursannulenon="";
					$displayreport="inline";
				}else{
					$selectedcoursannulenon="checked='checked'";$selectedcoursannuleoui="";
					$displayreport="none";
				}	
				

		?>
		<tr>
			<td align='right'><font class='T2'>Cours annulé :</font></td>
			<td>	<input type="radio" value='1' name="coursannule" <?php print $selectedcoursannuleoui ?> onclick="document.getElementById('report').style.display='inline'; document.getElementById('recursiveoui').disabled=true; document.getElementById('recursivenon').checked=true " /> (<i>oui</i>) 
				<input type="radio" value='0' name="coursannule" <?php print $selectedcoursannulenon ?> onclick="document.getElementById('report').style.display='none' ; document.getElementById('recursiveoui').disabled=false;  " /> (<i>non</i>)

		<?php 
				if ($donnee[0][12] == "0000-00-00") {
					$reportle="jj/mm/aaaa";
				}else{
					$reportle=dateForm($donnee[0][12]);
				}
				$reporta=$donnee[0][13];
		?>
				<span id='report' style="display:<?php print $displayreport?>" >&nbsp;&nbsp;
				Report le <input type="text" name="reportle"  size=10 value="<?php print $reportle ?>" onclick="this.value=''" > 
			     	<?php 
				//	calendarDim('id3','document.formulaire.reportle',$_SESSION["langue"],"1","0");
				?>  
				à&nbsp;&nbsp;<input type="text" name="reporta" size=5 value="<?php print $reporta ?>" onclick="this.value=''">
				<input type='checkbox'	name='validecreation' value='1' title="Création automatique dans l'EDT" />
				</span>
			</td>
		</tr>
		<?php
				if ($donnee[0][11] == 1) { 
					$selecteddocdstoui="checked='checked'"; $selecteddocdstnon="";
				}else{
					$selecteddocdstnon="checked='checked'"; $selecteddocdstoui="";
				}
		?>

		<tr>
			<td align='right'><font class='T2'>Document D.S.T. :</font></td>
				<td>	<input type="radio" value='1' name="docdst" <?php print $selecteddocdstoui ?> /> (<i>oui</i>) 
					<input type="radio" value='0' name="docdst" <?php print $selecteddocdstnon ?> /> (<i>non</i>)
			</td>
		</tr>

		<?php
				if ($donnee[0][14] == 1) { 
					$selectedemargementoui="checked='checked'"; $selectedemargementnon="";
				}else{
					$selectedemargementnon="checked='checked'"; $selectedemargementoui="";
				}
		?>

		<tr>
			<td align='right'><font class='T2'>Feuille d'emargement (classique) :</font></td>
				<td>	<input type="radio" value='1' name="emargement" <?php print $selectedemargementoui ?> /> (<i>oui</i>) 
					<input type="radio" value='0' name="emargement" <?php print $selectedemargementnon ?> /> (<i>non</i>)
			</td>
		</tr>
		<?php
				if ($donnee[0][16] == 1) { 
					$selectedemargementevaloui="checked='checked'"; $selectedemargementevalnon="";
				}else{
					$selectedemargementevalnon="checked='checked'"; $selectedemargementevaloui="";
				}
		?>

		<tr>
			<td align='right'><font class='T2'>Feuille d'emargement (évaluation) :</font></td>
				<td>	<input type="radio" value='1' name="emargementeval" <?php print $selectedemargementevaloui ?> /> (<i>oui</i>) 
					<input type="radio" value='0' name="emargementeval" <?php print $selectedemargementevalnon ?> /> (<i>non</i>)
			</td>
		</tr>
		<?php
				if ($donnee[0][17] == 1) { 
					$selectedemargementpedagooui="checked='checked'"; $selectedemargementpedagonon="";
				}else{
					$selectedemargementpedagonon="checked='checked'"; $selectedemargementpedagooui="";
				}

				if ($donnee[0][9] == "") {
					$selectedemargementpedagooui="checked='checked'"; $selectedemargementpedagonon="";
				}
		?>

		<tr>
			<td align='right'><font class='T2'>Fiche vacation pédagogique :</font></td>
				<td>	<input type="radio" value='1' name="emargementpedago" <?php print $selectedemargementpedagooui ?> /> (<i>oui</i>) 
					<input type="radio" value='0' name="emargementpedago" <?php print $selectedemargementpedagonon ?> /> (<i>non</i>)
			</td>
		</tr>
<?php } ?>

	</table>	
<br><br>
<div align="right"><?php print $action2 ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
<div class="buttonDiv">
	<input type="button" value="Supprimer cette fiche" name="supp" class="BUTTON"  style="width:120px;"  onclick="document.getElementById('suppA').style.visibility='visible'" >

<?php print $action ?>

	<input type="button" value="Annuler" class="BUTTON" style="width:75px;" onclick="ferm();">
<br>
<div style="visibility:hidden" id='suppA' >(Suppression des récurrences : oui <input type='radio'  name='recursivesupp' value='oui' onclick="confirmSupp()" /> 
									  non <input type='radio'  name='recursivesupp' value='non' onclick="confirmSupp()" /> )</div>
</div>
		
		
</form>

<?php Pgclose();  ?>
</body>
</html>

