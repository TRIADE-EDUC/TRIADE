<?php
session_start();
$rowHeight = 60;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/langue.php");
include_once("./librairie_php/db_triade.php");
validerequete("6");
$cnx=cnx();

nettoyageEdt();
$fichiercsv="data/edt/edtgoogle_".$_SESSION["id_pers"].".csv";
if (!is_dir("data/edt")) { mkdir("data/edt/"); }

$hd_edt="7";
$hf_edt="21";
$mf_edt="00";
if (defined("HD_EDT")){ $hd_edt=HD_EDT; }
if (defined("HF_EDT")){ $hf_edt=HF_EDT; }
if (defined("MF_EDT")){ $mf_edt=MF_EDT; }
if (($mf_edt < 10) && (strlen($mf_edt) == 1)) { $mf_edt="0".$mf_edt; }
if (trim($hd_edt) == "") { $hd_edt="7"; $hf_edt="21"; $mf_edt="00"; }
$nb=$hf_edt-$hd_edt+1.2;

$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

$Seid=$mySession['Spid'];
if ($_SESSION["membre"] == "menututeur") { $Seid=""; }
if (isset($_GET["idelevetuteur"])) {
    $Seid=$_GET["idelevetuteur"];
    $_SESSION["idelevetuteur"]=$Seid;
    $Scid=chercheClasseEleve($Seid);
    $_SESSION["idClasse"]=$Scid;
}
if (isset($_SESSION["idelevetuteur"])) {
    $Seid=$_SESSION["idelevetuteur"];
}

$idclasse  = chercheIdClasseDunEleve($Seid);
$nomclasse = chercheClasse($idclasse);
?>
<title>Triade - EDT</title>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<style type="text/css">
html,body{margin:0;padding:0;height:100%;width:100%;font-family:arial;font-size:0.8em}
p,h2{margin:2px}
h1{font-size:1.4em;margin:2px}
h2{font-size:1.3em}
.weekButton{width:80px;font-size:0.8em;font-family:arial}

#weekScheduler_container{
    border:1px solid #9fa8da;
    width:986px;
    margin:8px 5px 5px;
    padding:0;
    box-shadow:0 2px 12px rgba(26,35,126,.15);
    border-radius:4px;
    overflow:hidden;
}
.weekScheduler_appointments_day{
    width:130px;float:left;
    background-color:#f8f9fd;
    border-right:1px solid #e8eaf6;
    position:relative;
}
#weekScheduler_top{
    background-color:#eef0f8;
    height:20px;
    border-bottom:1px solid #c5cae9;
}
.calendarContentTime,.spacer{
    background-color:#eef0f8;
    text-align:center;
    font-family:arial;
    font-size:28px;
    line-height:<?php echo $rowHeight; ?>px;
    height:<?php echo $rowHeight; ?>px;
    border-right:1px solid #c5cae9;
    width:50px;
}
.weekScheduler_appointmentHour{
    height:60px;
    border-bottom:1px solid #e8eaf6;
}
.spacer{height:20px;float:left}
#weekScheduler_hours{width:50px;float:left}
.calendarContentTime{border-bottom:1px solid #c5cae9}
#weekScheduler_appointments{width:917px;float:left}
.calendarContentTime .content_hour{
    font-size:10px;
    vertical-align:top;
    line-height:<?php echo $rowHeight; ?>px;
}
#weekScheduler_top{position:relative;clear:both}
#weekScheduler_content{
    clear:both;
    height:<?php echo $rowHeight*$nb ?>px;
    position:relative;
    overflow:auto;
}
#stime:hover{
    position:absolute;border:1px solid #9fa8da;
    right:-1px;top:-1px;width:80px;height:18px;
    z-index:100000;font-size:1.5em;padding:1px;
    background-color:#eef0f8;
}
.days div{
    width:130px;float:left;
    background-color:#eef0f8;
    text-align:center;font-family:arial;
    height:20px;font-size:0.9em;line-height:20px;
    border-right:1px solid #c5cae9;
    color:#080A66;font-weight:600;
}
.weekScheduler_anAppointment{
    position:absolute;background-color:#fff;
    border:1px solid #9fa8da;z-index:1000;overflow:hidden;
}
.weekScheduler_appointment_header{height:4px;background-color:#9fa8da}
.weekScheduler_appointment_headerActive{height:4px;background-color:#3949ab}
.weekScheduler_appointment_textarea{font-size:0.7em;font-family:arial}
.weekScheduler_appointment_txt{
    font-size:0.9em;font-family:arial;
    padding:2px;padding-top:5px;overflow:hidden;
}
.weekScheduler_appointment_footer{
    position:absolute;bottom:-1px;
    border-top:1px solid #9fa8da;
    height:4px;width:100%;font-size:0.8em;
    background-color:#3949ab;
}
.weekScheduler_appointment_time{
    position:absolute;border:1px solid #c5cae9;
    right:1px;top:5px;width:55px;height:12px;
    z-index:100000;font-size:0.6em;
    padding-left:3px;padding-top:2px;
    background-color:#eef0f8;
    border-radius:5px;
}
.eventIndicator{background-color:#3949ab;z-index:50;display:none;position:absolute}
</style>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/ajax.js"></script>
<script type="text/javascript" src="./librairie_js/function.js"></script>
<script type="text/javascript">
var itemRowHeight=<?php echo $rowHeight; ?>;
var initDateToShow="<?php echo date("Y-m-d"); ?>"
</script>
<script src="./librairie_js/dhtmlgoodies-week-planner-ro.js?random=<?php echo date("Ymd"); ?>" type="text/javascript"></script>
</head>
<body id='bodyfond2'>
<?php
$edtimage=0;
if (file_exists("./data/image_pers/".$idclasse."_edt.jpg")) {
    $edtimage=1;
    $id=$idclasse."#jpg";
    $tabClasse[$id]=$nomclasse[0][1];
}
if (file_exists("./data/image_pers/".$idclasse."_edt.pdf")) {
    $edtimage=1;
    $id=$idclasse."#pdf";
    $tabClasse[$id]=$nomclasse[0][1];
}

if ($edtimage == 1) {
    print "<form method='post'><br>&nbsp;&nbsp;<font class=T2>Classe : </font><select name='idclasse' onChange='this.form.submit();' >";
    print select_classe_search($donnee[0][6]);
    print "<option value='' STYLE='color:#000066;background-color:#FCE4BA'>Aucun</option>";
    foreach ($tabClasse as $id => $nom) {
        print "<option value='".$id."'>".strtoupper($nom)."</option>\n";
    }
    print "</select></form>";
    if (isset($_POST["idclasse"])) {
        list($idclasse,$type)=preg_split('/#/',$_POST["idclasse"]);
        if ($type == "jpg") {
            print "<br /><img src='image_trombi.php?edt&idclasse=".$idclasse."' />";
        } else {
            print "<iframe src='visu_pdf_edt.php?id=$idclasse' width='1' height='1'></iframe>";
        }
    }
} else {
?>
<script type="text/javascript">
initTopHour = <?php print $hf_edt ?>;
weekplannerStartHour = <?php print $hd_edt ?>;
</script>

<form name="form">
<input type="hidden" name="profID" value="">
<input type="hidden" name="classeID" value="<?php print $idclasse ?>">

<div class="edt-toolbar">

  <!-- Ligne 1 : navigation semaine + date -->
  <div class="edt-nav">
    <button class="edt-nav-btn" onclick="displayPreviousWeek();return false">&larr; <?php print LANGMESS53 ?></button>
    <button class="edt-nav-btn" onclick="displayNextWeek();return false"><?php print LANGMESS54 ?> &rarr;</button>
    <input type="text" name="saisiedate" readonly="readonly" size="10" class="edt-date-input">
    <?php include_once("librairie_php/calendar.php");
    calendarpopupCalend('id2','document.form.saisiedate',$_SESSION["langue"],"1","0"); ?>
    <button class="edt-ok-btn" onclick="displayPreviousWeek2(document.form.saisiedate.value);return false">OK</button>
    <button class="edt-print-btn" onclick="imprimer();return false">&#x1F5A8; <?php print LANGMESS53 != "" ? "Imprimer" : "Print" ?></button>
  </div>

  <!-- Ligne 2 : classe / sélecteur tuteur -->
  <div class="edt-row2">
    <?php if ($_SESSION["membre"] == "menututeur") { ?>
      <select name='idelevetuteur' class="edt-tutor-select" onchange="this.form.submit()">
        <?php
        if ($Seid != "") {
            $nom    = recherche_eleve_nom($Seid);
            $prenom = recherche_eleve_prenom($Seid);
            print "<option value='$Seid'>".trunchaine(strtoupper($nom)." ".$prenom,30)."</option>\n";
        } else {
            print "<option>".LANGCHOIX."</option>";
        }
        listEleveTuteur($_SESSION["id_pers"],30);
        ?>
      </select>
    <?php } else { ?>
      <span class="edt-class-lbl"><?php print LANGMESS55 ?></span>
      <span class="edt-class-name"><?php print htmlspecialchars($nomclasse[0][1]) ?></span>
    <?php } ?>
  </div>

  <!-- Ligne 3 : exports -->
  <div class="edt-actions">
    <button class="edt-export-btn" onclick="open('telecharger.php?fichier=<?php print $fichiercsv ?>&fichiername=Export_Google.csv','_blank','');return false">
      Export Google Agenda
    </button>
    <?php
    $keytriade=$recupkeytriade ?? recupkeytriade($_SESSION["membre"],$_SESSION["id_pers"],$_SESSION["idparent"]);
    if ($keytriade != "") {
        $index  = recupIndexViaMembre($_SESSION['membre']);
        $http   = protohttps();
        $urlical= "$http".URLSITE."ical-triade.php?ref=$keytriade&index=$index";
        echo "<span class='edt-ical-lbl'>Agenda en ligne :</span>";
        echo "<input type='text' class='edt-ical-input' value='".htmlspecialchars($urlical)."' size='45' readonly onclick='this.select()'>";
    }
    ?>
  </div>

</div>

<div id="weekScheduler_container">
  <div id="weekScheduler_top">
    <div class="spacer"><span></span></div>
    <div class="days" id="weekScheduler_dayRow">
      <div><?php print LANGLUNDI ?>   <span></span></div>
      <div><?php print LANGMARDI ?>   <span></span></div>
      <div><?php print LANGMERCREDI ?><span></span></div>
      <div><?php print LANGJEUDI ?>   <span></span></div>
      <div><?php print LANGVENDREDI ?><span></span></div>
      <div><?php print LANGSAMEDI ?>  <span></span></div>
      <div><?php print LANGDIMANCHE ?><span></span></div>
    </div>
  </div>
  <div id="weekScheduler_content">
    <div id="weekScheduler_hours">
      <?php
      $startHourOfWeekPlanner = $hd_edt;
      $endHourOfWeekPlanner   = $hf_edt;
      $month = dateM_duServeur();
      $day   = dateD_duServeur();
      $year  = dateY_duServeur();
      $date  = mktime($startHourOfWeekPlanner,0,0,$month,$day,$year);
      for ($no=$startHourOfWeekPlanner; $no<=$endHourOfWeekPlanner; $no++) {
          $time = $no."<span class=\"content_hour\">$mf_edt</span>";
          $date = $date + 3600;
          echo "<div class=\"calendarContentTime\">$time</div>\n";
      }
      ?>
    </div>
    <div id="weekScheduler_appointments">
      <?php for ($no=0; $no<7; $no++) { ?>
      <div class="weekScheduler_appointments_day">
        <?php for ($no2=$startHourOfWeekPlanner; $no2<=$endHourOfWeekPlanner; $no2++) {
            echo "<div id=\"weekScheduler_appointment_hour".$no."_".$no2."\" class=\"weekScheduler_appointmentHour\"></div>\n";
        } ?>
      </div>
      <?php } ?>
    </div>
  </div>
</div>

</form>
<script type="text/javascript">displayPreviousClasse3();</script>
<?php @Pgclose() ?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<?php } ?>
</body>
</html>
