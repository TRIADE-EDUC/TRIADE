<?php
session_start();
error_reporting(0);
// Input to this file:	$_GET['id'] which is the id of the node that you are about to edit
?>
<html>
<head>
<title>EDT Editer</title>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script>
function active(i) {
	if (i == 1) {
		document.formulaire.periode.value="";
		document.formulaire.elements[7].checked=false;
		document.formulaire.elements[8].checked=false;
		document.formulaire.elements[9].checked=false;
		document.formulaire.elements[10].checked=false;
		document.formulaire.elements[11].checked=false;
		document.formulaire.elements[12].checked=false;
		document.formulaire.elements[13].checked=false;

		document.formulaire.periode.disabled=false;
		document.formulaire.elements[7].disabled=false;
		document.formulaire.elements[8].disabled=false;
		document.formulaire.elements[9].disabled=false;
		document.formulaire.elements[10].disabled=false;
		document.formulaire.elements[11].disabled=false;
		document.formulaire.elements[12].disabled=false;
		document.formulaire.elements[13].disabled=false;
	}else{
		document.formulaire.periode.disabled=true;
		document.formulaire.elements[7].disabled=true;
		document.formulaire.elements[8].disabled=true;
		document.formulaire.elements[9].disabled=true;
		document.formulaire.elements[10].disabled=true;
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
	CreateEdt($descrip,$_POST['color'],$_POST['classeID'],$_POST['profID'],$_POST['periode'],$_POST['prestation'],$_POST['jours'],$_POST['recursive'],$_POST['matiereID'],$_POST["coursannule"],$_POST["docdst"],$_POST["emargement"],$_POST['ressourceID'],$_POST["dureehoraire"],$_POST["debuthoraire"],$_POST["debutdate"],$_POST["horaire"],$_POST['multiseance']);

	?>
	<script type="text/javascript">self.close();</script>
	<?php
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
			var id = "<? echo $_GET['id']; ?>" ;
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


$action2="";
$disabledDate="";
$choixColor="";
$disabledJour="";
$action="<input type='submit' value='Enregistrer' name='save' class='BUTTON'  style='width:75px;'  >";




?>
<script>
// affiche un message d'alerte
function error2(text) {
// abandon si erreur déjà signalée
   if (errfound) return;
   window.alert(text);
   errfound = true;
}


// validation d'un champ de select
function Validselect(item){
 if (item == 0) {
        return (false) ;
 }else {
        return (true) ;
        }
}

function valideEnvoiEdt() {
	errfound=false;
	if (!Validselect(document.formulaire.classeID.options.selectedIndex)) {
        error2("Indiquer une classe pour valider l'enregistrement."); }	
	return !errfound; /* vrai si il ya pas d'erreur */

}
</script>
<!-- Example of a form --->
<form  method="post" name="formulaire" onSubmit="return valideEnvoiEdt();">
<input type="hidden" name="color" value="">

	<table border="0" cellpadding="2" cellpadding="0">
		<tr>
		<td align='right'><font class='T2'>Du :</font></td><td><input type='text' size='12' name="debutdate"  value="" onBlur="document.getElementById('dateFin').value=this.value" /><?php 
				     include_once("librairie_php/calendar.php");
calendarDim('id22','document.formulaire.debutdate',$_SESSION["langue"],"1","0");?>
 <font class='T2'>au :</font> <input type='text' size='12' name="periode"  value="" id='dateFin' /><?php 
				     include_once("librairie_php/calendar.php");
calendarDim('id32','document.formulaire.periode',$_SESSION["langue"],"1","0");?>
 </td>
		
		</tr>

		<tr>
		<td align='right'><font class='T2'>Horaire :</font></td><td><input type='text' size='4' name="debuthoraire"  value="hh:mm" onclick="this.value=''" /> <font class='T2'>Durée :</font> <input type='text' size='4' name="dureehoraire"  value="hh:mm" onclick="this.value=''" /> </td>
		
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
				<textarea id="eventDescription" name="eventDescription" cols=80 rows=5><?php echo $desc ?></textarea>
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
					select_personne('ENS'); // creation des options	
					?>
				<?php } ?>
			</select>
			</td>
		</tr>
		<?php if ($_SESSION["membre"] != "menuprof") { ?>
		<tr>
			<td align='right'><font class='T2'>Matière :</font></td>
			<td><select name="matiereID">
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
			<td><select name="classeID">
				 <?php print select_classe_search($donnee[0][6]); ?>
			          <option  value="rien" id='select0'><?php print LANGCHOIX?></option>
				  <option  value="tous" id='select1'><?php print "Tous les classes"?></option>
				<?php
				select_classe(); // creation des options
				?>
			</select>
			</td>
		</tr>
		<?php } ?>
<!--
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
-->
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
<td><input type="checkbox" name="jours[]" value="1"  ></td>
<td><input type="checkbox" name="jours[]" value="2"  ></td>
<td><input type="checkbox" name="jours[]" value="3"  ></td>
<td><input type="checkbox" name="jours[]" value="4"  ></td>
<td><input type="checkbox" name="jours[]" value="5"  ></td>
<td><input type="checkbox" name="jours[]" value="6"  ></td>
<td><input type="checkbox" name="jours[]" value="7"  ></td>
</tr>
</table>		
</td>
</tr>

<tr>
<td align='right'><font class='T2'>Autorise chevauchement : </font></td>
<td><input type="radio" value='1' name="multiseance" /> (<i>oui</i>)
    <input type="radio" value='0' name="multiseance" checked='checked' /> (<i>non</i>)
</td>
</tr>



<?php if ($_SESSION["membre"] != "menuprof") { ?>
		<tr>
			<td align='right'><font class='T2'>Prestation :</font></td>
			<td><select name="prestation">
			    <option  value="rien" STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
				<?php
				select_EvalHoraire(); // creation des options
				?>
			</select>
			</td>
		</tr>
		<tr>
			<td align='right'><font class='T2'>Document D.S.T. :</font></td>
				<td>	<input type="radio" value='1' name="docdst" /> (<i>oui</i>) 
					<input type="radio" value='0' name="docdst" checked='checked' /> (<i>non</i>)
			</td>
		</tr>
		<tr>
			<td align='right'><font class='T2'>Feuille d'emargement :</font></td>
				<td>	<input type="radio" value='1' name="emargement"  /> (<i>oui</i>) 
					<input type="radio" value='0' name="emargement" checked='checked' /> (<i>non</i>)
			</td>
		</tr>
		<tr>
			<td align='right'><font class='T2'>Affichage horaire :</font></td>
				<td>	<input type="radio" value='1' name="horaire" checked='checked'  /> (<i>oui</i>) 
					<input type="radio" value='0' name="horaire" /> (<i>non</i>)
			</td>
		</tr>
<?php } ?>

	</table>	
<br><br>
<div class="buttonDiv">

<?php print $action ?>

	<input type="button" value="Annuler" class="BUTTON" style="width:75px;" onclick="ferm();">
<br>
</div>
		
		
</form>
<?php Pgclose();  ?>
</body>
</html>

