<?php
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  header("Content-type: text/javascript;");

  if (!isset($_GET) && isset($HTTP_GET_VARS)) {
    $_GET = $HTTP_GET_VARS;
  }

  $skin = "Petrole";
  if (!empty($_GET) && isset($_GET['id'])) {
    $skin = $_GET['id'];
    $formatHeure = urldecode($_GET['frmHrs']);
  }
  include("../skins/$skin.php");
?>
///////////////////////////////////////////////////////////
// "Live Clock" script (1.0)
// By Mark Plachetta (astroboy@zip.com.au)
// http://www.zip.com.au/~astroboy/liveclock/
///////////////////////////////////////////////////////////

  var myfont_face = "Verdana, Arial";
  var myfont_size = "10";
  var myfont_color = "<?php echo $CalClockTexte; ?>";
  var mywidth = 129;
  var myformat = 1; // 1=date+heure ; 2=date ; 3=heure

  var old = false;

  if (document.all || document.getElementById || document.layers) {
    document.write('<TR><TD height="1" bgcolor="<?php echo $CalGaucheFond; ?>"><IMG src="image/trans.gif" alt="" width="1" height="1" border="0"></TD></TR><TR><TD height="24" bgcolor="<?php echo $CalClockFond; ?>" align="center" valign="middle">');
    if (document.all || document.getElementById)
      document.write('<span id="LiveClockIE" style="width:'+mywidth+'px; text-align:center; font-weight:bold;"></span>');
    else
      document.write('<ilayer id="ClockPosNS"><layer width="'+mywidth+'" id="LiveClockNS"></layer></ilayer>');
    document.write('</TD></TR>');
  }
  else { old = true; show_clock(); }

  function show_clock() {

    if (old) { return; }

    //show clock in NS 4
    if (document.layers) { document.ClockPosNS.visibility="show"; }

    var Digital = new Date();
    var day=Digital.getDate();
    var mnth=Digital.getMonth()+1;
    var yr=Digital.getFullYear();
    var hrs = Digital.getHours();
    var mins = Digital.getMinutes();
    var secs = Digital.getSeconds();

    var ampm = "";
<?php
  if ($formatHeure!="H:i")
    echo ("
    if (hrs>=12) {
      hrs -= 12;
      ampm = \" pm\";
    } else {
      ampm = \" am\";
    }
    if (hrs==0) hrs=12;
    ");
?>
    if (day <= 9)  { day = "0"+day; }
    if (mnth <= 9) { mnth = "0"+mnth; }
    if (hrs <= 9)  { hrs = "0"+hrs; }
    if (mins <= 9) { mins = "0"+mins; }
    if (secs <= 9) { secs = "0"+secs; }

    myclock = '';
    myclock += '<A style="color:'+myfont_color+'; font-family:'+myfont_face+'; font-size:'+myfont_size+'px;">';
    if (myformat == 1)
      myclock += day+'/'+mnth+'/'+yr+'<BR>'+hrs+':'+mins+':'+secs+ampm;
    else if (myformat == 2)
      myclock += day+'/'+mnth+'/'+yr;
    else
      myclock += hrs+':'+mins+':'+secs+ampm;
    myclock += '</A>';

    if (document.layers) {
      clockpos = document.ClockPosNS;
      liveclock = clockpos.document.LiveClockNS;
      liveclock.document.write(myclock);
      liveclock.document.close();
    } else if (document.all) {
      LiveClockIE.innerHTML = myclock;
    } else if (document.getElementById) {
      document.getElementById("LiveClockIE").innerHTML = myclock;
    }

    window.status='';

    setTimeout("show_clock()",1000);
}
