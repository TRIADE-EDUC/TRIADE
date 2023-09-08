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

  header("Content-Type: text/css");

  if (!isset($_GET) && isset($HTTP_GET_VARS)) {
    $_GET = $HTTP_GET_VARS;
  }

  if (!empty($_GET) && isset($_GET['id'])) {
    $skin = $_GET['id'];
  }
  if ($skin=="")
    $skin="Petrole";
  include("../skins/$skin.php");
?>
/* The main calendar widget.  DIV containing a table. */

div.calendar { position: relative; }

.calendar {
  border: 1px solid <?php echo $AgendaBordureTableau; ?>;
  font-size: 11px;
  color: <?php echo $AgendaTexte; ?>;
  cursor: default;
  background: <?php echo $CalFond; ?>;
  font-family: tahoma,verdana,sans-serif;
}

.calendar table {
  border: 0px;
  font-size: 10px;
  color: <?php echo $AgendaTexte; ?>;
  cursor: default;
  background: <?php echo $CalFond; ?>;
  font-family: verdana,tahoma,sans-serif;
  border-collapse: separate;
}

/* Header part -- contains navigation buttons and day names. */

.calendar .superbutton { /* "<<", "<", ">", ">>" buttons have this class */
  text-align: center;    /* They are the navigation buttons */
  padding: 2px;          /* Make the buttons seem like they're pressing */
  color:<?php echo $AujourdhuiLien; ?>;
}

.calendar .button { /* "<<", "<", ">", ">>" buttons have this class */
  text-align: center;    /* They are the navigation buttons */
  padding: 2px;          /* Make the buttons seem like they're pressing */
  color:<?php echo $AujourdhuiLien; ?>;
  background: <?php echo $AujourdhuiFond; ?>;
}

.calendar .nav {
  background: <?php echo $AujourdhuiFond; ?> url("../image/calendrier/<?php echo $CalFlechePopup; ?>") no-repeat 100% 100%;
}

/*Juin 2005*/
.calendar thead .title { /* This holds the current "month, year" */
  font-weight: bold;      /* Pressing it will take you to the current date */
  text-align: center;
  background: <?php echo $AgendaFondEnteteTableau; ?>;
  color: <?php echo $AgendaTexteEnteteTableau; ?>;
  padding: 2px;
}

.calendar thead .headrow { /* Row <TR> containing navigation buttons */
  background: <?php echo $AujourdhuiFond; ?>;
  color: <?php echo $AujourdhuiLien; ?>;
  font-weight: bold;
}

.calendar thead .name { /* Cells <TD> containing the day names */
  border-bottom: 1px solid <?php echo $AgendaBordureTableau; ?>;
  padding: 2px;
  text-align: center;
  color: <?php echo $AgendaTexte; ?>;
}

.calendar thead .weekend { /* How a weekend day name shows in header */
  color: #f00;
}

.calendar thead .hilite { /* How do the buttons in header appear when hover */
  background-color: <?php echo $bgColor[0]; ?>;
  color: <?php echo $AgendaTexte; ?>;
  border: 1px solid <?php echo $AgendaBordureTableau; ?>;
  padding: 1px;
}

.calendar thead .active { /* Active (pressed) buttons in header */
  background-color: <?php echo $bgColor[1]; ?>;
  padding: 2px 0px 0px 2px;
}

.calendar thead .daynames { /* Row <TR> containing the day names */
  background: <?php echo $CalTitreFond; ?>;
}

/* The body part -- contains all the days in month. */

.calendar tbody .day { /* Cells <TD> containing month days dates */
  width: 2em;
  text-align: right;
  padding: 2px 4px 2px 2px;
}
.calendar tbody .day.othermonth {
  font-size: 80%;
  color: <?php echo $CalJourMoisPrec; ?>;
}
.calendar tbody .day.othermonth.oweekend {
  color: #faa;
}

.calendar table .wn {
  padding: 2px 3px 2px 2px;
  border-right: 1px solid <?php echo $AgendaBordureTableau; ?>;
  background: <?php echo $CalTitreFond; ?>;
}

.calendar tbody .rowhilite td {
 background: <?php echo $bgColor[0]; ?>;

}

.calendar tbody .rowhilite td.wn {
  background: <?php echo $CalFond; ?>;
}

.calendar tbody td.hilite { /* Hovered cells <TD> */
  background: <?php echo $CalJourFerie; ?>;
  padding: 1px 3px 1px 1px;
  border: 1px solid <?php echo $AgendaBordureTableau; ?>;
}

.calendar tbody td.active { /* Active (pressed) cells <TD> */
  background: <?php echo $CalJourSelection; ?>;
  padding: 2px 2px 0px 2px;
}

.calendar tbody td.selected { /* Cell showing today date */
  font-weight: bold;
  border: 1px solid #000;
  padding: 1px 3px 1px 1px;
  background: <?php echo $CalJourSelection; ?>;
}

.calendar tbody td.weekend { /* Cells showing weekend days */
  color: #f00;
}

.calendar tbody td.today { font-weight: bold; }

.calendar tbody .disabled { color: #999; }

.calendar tbody .emptycell { /* Empty cells (the best is to hide them) */
  visibility: hidden;
}

.calendar tbody .emptyrow { /* Empty row (some months need less than 6 rows) */
  display: none;
}

/* The footer part -- status bar and "Close" button */

.calendar tfoot .footrow { /* The <TR> in footer (only one right now) */
  text-align: center;
  background: #988;
  color: #000;

}

.calendar tfoot .ttip { /* Tooltip (status bar) cell <TD> */
  border-top: 1px solid <?php echo $AgendaBordureTableau; ?>;
  background: <?php echo $AujourdhuiFond; ?>;
  color: <?php echo $AujourdhuiLien; ?>;
  font-weight: bold;
  padding: 1px;
}
.calendar tfoot .hilite { /* Hover style for buttons in footer */
  background: red;
  border: 1px solid #f40;
  padding: 1px;
}

.calendar tfoot .active { /* Active (pressed) style for buttons in footer */
  background: #c77;
  padding: 2px 0px 0px 2px;
}

/* Combo boxes (menus that display months/years for direct selection) */

.combo {
  position: absolute;
  display: none;
  top: 0px;
  left: 0px;
  width: 4em;
  cursor: default;
  border: 1px solid <?php echo $AgendaBordureTableau; ?>;
  background: <?php echo $CalFond; ?>;
  color: <?php echo $AgendaTexte; ?>;
  font-size: smaller;
  z-index: 100;
}

.combo .label,
.combo .label-IEfix {
  text-align: center;
  padding: 1px;
}

.combo .label-IEfix {
  width: 4em;
}

.combo .hilite {
  background: <?php echo $CalJourFerie; ?>;
}

.combo .active {
  border-top: 1px solid <?php echo $AgendaBordureTableau; ?>;
  border-bottom: 1px solid <?php echo $AgendaBordureTableau; ?>;
  background: <?php echo $CalJourSelection; ?>;
  font-weight: bold;
}

.calendar td.time {
  border-top: 1px solid #a88;
  padding: 1px 0px;
  text-align: center;
  background-color: #fed;
}

.calendar td.time .hour,
.calendar td.time .minute,
.calendar td.time .ampm {
  padding: 0px 3px 0px 4px;
  border: 1px solid #988;
  font-weight: bold;
  background-color: #fff;
}

.calendar td.time .ampm {
  text-align: center;
}

.calendar td.time .colon {
  padding: 0px 2px 0px 3px;
  font-weight: bold;
}

.calendar td.time span.hilite {
  border-color: #000;
  background-color: #866;
  color: #fff;
}

.calendar td.time span.active {
  border-color: #f00;
  background-color: #000;
  color: #0f0;
}
