<?php
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


/* TRADUCTION PAR FABRICE RAUD */
// --------------------------- //

if (!defined(INTITULEDIRECTION)) { define("INTITULEDIRECTION","direction"); }
if (!defined(INTITULEELEVE)) { define("INTITULEELEVE","student"); }
if (!defined(INTITULEELEVES)) { define("INTITULEELEVES","students"); }


// fichier pour langue cote admin.
// POUR TOUS -------------------
// brmozilla($_SESSION[navigateur]);
// ($nbretard late(s) / $nbabs half day(s) missed)
//define("CLICKICI","Click here");
define("CLICKICI","Enter");
define("VALIDATE","Enter");
define("VALIDER","Enter");
define("LANGTP22"," WARNING - class test request - class test request - RUBRIQUE");
define("LANGTP3"," Class test calendar ");
define("LANGCHOIX","Select ...");
define("LANGCHOIX2","no class");
define("LANGCHOIX3","--- Select ---");
define("LANGOUI","yes");
define("LANGNON","no");
define("LANGFERMERFEN","Close Window");
define("LANGATT","WARNING !");
define("LANGDONENR","data has been saved");
define("LANGPATIENT","Please wait");
define("LANGSTAGE1",'Professional developpement management');
define("LANGINCONNU",'unknown'); // doit etre identique que langinconnu cote javascript
define("LANGABS",'abs');
define("LANGRTD",'trd');
define("LANGRIEN",'nothing');
define("LANGENR",'save');
define("LANGRAS1",'Today ');
define("LANGMETEO1",'DAY');
define("LANGMETEO2",'NIGHT');
define("LANGDATEFORMAT",'dd/mm/yyyy');


//------------------------------
// titre
//-------------------------------
define("LANGTITRE3","sliding text message on the top");
define("LANGTITRE4","sliding text message in the header ");
define("LANGTITRE5","receiving message");
// define("LANGTITRE6","Create direction account"); le 16/06/2014
define("LANGTITRE6","Create management account");
define("LANGTITRE7","Create School life account");
// define("LANGTITRE8","Create teacher's account"); le 16/06/2014
define("LANGTITRE8","Create professor's account");
define("LANGTITRE9","Create subtitute's account");
define("LANGTITRE10","Create  student's account");
define("LANGTITRE11","Create group"); //
define("LANGTITRE12","Create class"); //
define("LANGTITRE13","Create course"); //
define("LANGTITRE14","Create course option"); //
define("LANGTITRE16","create teacher's assignment to class");
define("LANGTITRE17","Create class assignment");
define("LANGTITRE18","Show class assignment");
define("LANGTITRE19","Change teacher's assignement");
define("LANGTITRE20","change class assignment");
define("LANGTITRE21","delete assignment");
define("LANGTITRE22","Import ASCII file (txt,csv) ");
define("LANGTITRE23","Unjustify tardiness list ");
define("LANGTITRE24","Add an exemption");
define("LANGTITRE25","list / change exemptions");
define("LANGTITRE26","Delete exemption");
define("LANGTITRE27","Manage exemption - planning");
define("LANGTITRE28","View  / Change exemption");
define("LANGTITRE29","Consult class");
define("LANGTITRE30","Search a student");
define("LANGTITRE31","Import fGEP file");
//define("LANGTITRE32","student's information"); sam 03/09/2014
define("LANGTITRE32","Picture ID");
define("LANGTITRE33","School certificate");

//------------------------------
define("LANGTE1","Title");
define("LANGTE2","du");
define("LANGTE3","from");
define("LANGTE4","Number of character");
define("LANGTE5","Subject");
define("LANGTE6","To");
define("LANGTE6bis","To parents of ");
define("LANGTE7","Date");
define("LANGTE8","Delete messages");
define("LANGTE9","read");
define("LANGTE10","until:");
define("LANGTE11","at ");
define("LANGTE12","date");
define("LANGTE13","date");
define("LANGTE14","to group ");

//------------------------------
define("LANGFETE","Happy Birthday ");
define("LANGFEN1","Today's event");
define("LANGFEN2","Today's class tests");
//------------------------------
define("LANGLUNDI","Monday");
define("LANGMARDI","Tuesday");
define("LANGMERCREDI","Wednesday");
define("LANGJEUDI","Thursday");
define("LANGVENDREDI","Friday");
define("LANGSAMEDI","Saturday");
define("LANGDIMANCHE","Sunday");
// ------------------------------
define("LANGMESS1","Send a message - date ");
define("LANGMESS3","Send a message to School Life :");
define("LANGMESS4","Send a message to a professor :");
define("LANGMESS6","Message Sent");
define("LANGMESS7","News saved");
define("LANGMESS8","Message Sent");
define("LANGMESS9","Reply to message - on ");
define("LANGMESS10",'Trimester dates not yet entered');
define("LANGMESS11",'Please inform the administration');
define("LANGMESS12",'To validate trimester dates');
define("LANGMESS13",'Please go <a href="definir_trimestre.php">Here</a>');
define("LANGMESS14",'These class assignments have not been saved yet');
define("LANGMESS15",'Please go <a href="affectation_creation_key.php">Here</a>');
define("LANGMESS16",'To validate this class assignment ');
define("LANGMESS17","Setting ");
define("LANGMESS18","I"); // premiere lettre de la phrase suivante !!!
define("LANGMESS18bis","f more than one email address to <br> enter, separate each of them with<br> a comma.");
define("LANGMESS19","Activated");
define("LANGMESS20","Update Configuration");
define("LANGMESS21","Transfer all received messages to following email");
define("LANGMESS22","Send message to email group : ");
define("LANGMESS23","Create email group ");
define("LANGMESS24","Enter group's members");
define("LANGMESS25","Select different members by hold key"); //
define("LANGMESS26","Validate creation");
define("LANGMESS27","Email Group created");
define("LANGMESS28","Email group list");
define("LANGMESS29","Group ");
define("LANGMESS30","Members list");
define("LANGMESS31","Message from ");
define("LANGMESS32","You currently have");
define("LANGMESS33","message(s) on hold ");

// -----------------------------
// bouton
// PAS DE -->' (quote) !!!!
define("LANGBTS","next >");
define("LANGBT1","save passing text");
define("LANGBT2","save information");
define("LANGBT3","Quit without sending");
define("LANGBT4","Send message");
define("LANGBT5","Please wait");
define("LANGBT6","Delete checked messages");
define("LANGBT7","Save account");
define("LANGBT11","subs list");
define("LANGBT12","Groups list");
define("LANGBT13","Save class");
//define("LANGBT14","Save creation");
define("LANGBT14","Save");
define("LANGBT15","class list");
define("LANGBT16","Course list");
define("LANGBT17","course Options list");
define("LANGBT18","Save status"); //
define("LANGBT19","Save"); //
define("LANGBT20","Quit without saving"); //
define("LANGBT21","Save assignment"); //
define("LANGBT22","Delete assignment"); //
define("LANGBT23","Save file"); //
define("LANGBT24","Try again"); //
define("LANGBT25","Refresh page"); //
define("LANGBT26","Create class"); //
define("LANGBT27","Attendance planning"); //
//define("LANGBT28","Consult"); // le 18/06/2014
define("LANGBT28","Enter");
define("LANGBT29","Delete absence or tardy"); //
define("LANGBT30","Save update"); //
define("LANGBT31","Enter");
define("LANGBT32","Delete planned absence");
define("LANGBT33","change planned absence");
define("LANGBT34","Add planned absence");
define("LANGBT35","Save data from ");
define("LANGBT36","planned absence changed --  Service Triade");
define("LANGBT37","Forward information");
define("LANGBT38","Send");
define("LANGBT39","Begin search");
define("LANGBT40","retrieving information");
define("LANGBT41","Finished");
define("LANGBT42","Save unregistered students");
// define("LANGBT43","Print report card"); sam le 12/09/2014
define("LANGBT43","Print transcript");
define("LANGBT44","History");
define("LANGBT45","Consult documentation");
define("LANGBT46","Save picture");
define("LANGBT47","Other changes");
define("LANGBT48","End module");
define("LANGBT49","Edit whole class");
define("LANGBT50","Delete");
define("LANGBT51","Validate Class test submission");


// -----------------------------
define("LANGCA1","M"); //
define("LANGCA1bis","essage unread"); // sans la premiere lettre
define("LANGCA2","M"); //
define("LANGCA2bis","essage read"); // sans la premiere lettre
define("LANGCA3","I"); //
define("LANGCA3bis","ndiquez le JJ/MM/AAAA  <BR> Dans le cas d'une date non convenue,<BR> précisez la mention "); // sans la premiere lettre
// -----------------------------
define("LANGNA1","Last Name"); //
define("LANGNA2","First name"); //
define("LANGNA3","Password"); //
define("LANGNA4","New account created \\n\\n Service Triade "); //
define("LANGNA5","Substitute for "); //
// -----------------------------
define("LANGELE1","student's information"); //
define("LANGELE2","First Name"); //
define("LANGELE3","Last Name"); //
define("LANGELE4","Class"); //
define("LANGELE5","Option"); //
define("LANGELE6","Status"); //
define("LANGELE7","Bording schhol"); //
define("LANGELE8","Cafeteria"); //
define("LANGELE9","no meal"); //
define("LANGELE10","Date of birth"); //
define("LANGELE11","Nationality"); //
define("LANGELE12","Social Security"); //
define("LANGELE13","Family information"); //
define("LANGELE14","Address 1"); //
define("LANGELE15","Zip code"); //
define("LANGELE16","City"); //
define("LANGELE17","Address 2"); //
define("LANGELE18",""); //
define("LANGELE19",""); //
define("LANGELE20","Phone Number"); //
define("LANGELE21","Father's job"); //
define("LANGELE22","Father's phone"); //
define("LANGELE23","Mother's job"); //
define("LANGELE24","Mother's phone"); //
define("LANGELE25","Former school"); //
define("LANGELE26","School Name"); //
define("LANGELE27","School Number"); //
define("LANGELE28","Student created -- Service Triade"); //
define("LANGELE29","Student already registered  -- Service Triade"); //
//------------------------------------------------------------
define("LANGGRP1","Group Name"); //
define("LANGGRP2","Enter classs to create group"); //
define("LANGGRP3","Hold key to select class"); //
define("LANGGRP4","Ctrl"); //
define("LANGGRP5","and by left cliking with the mouse"); //
// define("LANGGRP6","Section Name"); //
define("LANGGRP6","Class");
define("LANGGRP7","New class created -- Service Triade"); //
define("LANGGRP8","New course created -- Service Triade"); //
define("LANGGRP9","Course Name"); //
define("LANGGRP10","Course Option Name"); //
//------------------------------------------------------------
define("LANGAFF1","Class assignment"); //
define("LANGAFF2","!! Assignment creation<u>deletes</u> all class grades!!</u>"); //
define("LANGAFF3","class Assignment"); //
//------------------------------------------------------------
define("LANGPER1","Print periods"); //
define("LANGPER2","Begining of period"); //
define("LANGPER3","End of period"); //
define("LANGPER4","Section"); //
define("LANGPER5","Get PDF file"); //
define("LANGPER6","Teacher "); //
define("LANGPER8","in class of "); //
define("LANGPER9","Class Assignment Module"); //
define("LANGPER10","WARNING this module to be used during new assignment,<br> detroys all students grades in selected classes."); //
define("LANGPER11","WARNING, All Students grades will be deleted. \\n Are you sure you want to do that ? \\n\\n Service Triade"); //
define("LANGPER12","Enter Access code");
define("LANGPER13","checking access code");
define("LANGPER14","Number of courses");
define("LANGPER15","Create class assignment");
define("LANGPER16","nota");
define("LANGPER17","course");
define("LANGPER18","Teacher");
define("LANGPER19","Coef");
define("LANGPER20","Group");
define("LANGPER21","Language");
define("LANGPER22","Print this page");
define("LANGPER23","assignment");
define("LANGPER23bis","succesful");  // assignment xxxx succeful
define("LANGPER24","interrupted"); // assignment xxxx interrupted
define("LANGPER25","Class");
define("LANGPER26","Show");
define("LANGPER27","Show");
define("LANGPER28","Showing class assignment");
define("LANGPER29","!! Changing class assignment <u>deletes</u> all class grades !!");
define("LANGPER30","Change");
define("LANGPER31","change assignment");
define("LANGPER32","Changing assignment");
define("LANGPER32bis","interrupted"); // Changing assigment xxxx interrupted
define("LANGPER33","Deleting assignment for  ");
define("LANGPER34","!! Deleting assignment <u>deletes</u> all class grades !!</u>");
define("LANGPER35","Class assignment");
define("LANGPER35bis","deleted"); // class assignment  xxxx deleted
//------------------------------------------------------------------------------
define("LANGIMP1","Import existing database ");
define("LANGIMP2","Enter file type to import ");
define("LANGIMP3","ASCII File");
define("LANGIMP4","GEP File");
define("LANGIMP5","ASCII file importation module");
define("LANGIMP6","the file to be added <FONT color=RED><B>MUST</B></FONT> contain <FONT COLOR=red><B>39</B></FONT> fields <I>(empty  or not)</I> separated by the same gap \"<FONT color=red><B>;</B></font>\" <I> or 38 time the same character within\"<FONT color=red><B>;</B></font>\"</I>");
define("LANGIMP7","Here the order of fields to be entered : ");
define("LANGIMP8","Last Name");
define("LANGIMP9","First Name");
define("LANGIMP10","class");
define("LANGIMP11","meal status");
define("LANGIMP12","Date of birth");
define("LANGIMP13","nationality");
define("LANGIMP14","tutor's Last name");
define("LANGIMP15","tutor's First Name ");

define("LANGIMP16","address&nbsp;1");
define("LANGIMP18","Zip&nbsp;code");
define("LANGIMP19","City");


define("LANGIMP17","address&nbsp;2");
define("LANGIMP18_2","Zip code");
define("LANGIMP19_2","City");


define("LANGIMP20","telephone");
define("LANGIMP21","Father's job");
define("LANGIMP22","Father's job telephone ");
define("LANGIMP23","mother's job");
define("LANGIMP24","mother's job telephone ");
define("LANGIMP25","school number");
define("LANGIMP26","lv1");
define("LANGIMP27","lv2");
define("LANGIMP28","latin");
define("LANGIMP29","Students reference number");
define("LANGIMP30","WARNING, former database will be automatically destroyed. \\n Would you like to continue ? \\n\\n Service Triade");
define("LANGIMP31","WARNING : Use this module for first install only,<br> It will delete all students datas (grades, report cards, school life).<br /> * require field");
define("LANGIMP39","Enter file to be forwarded  ");
define("LANGIMP40","File forwarded -- Service Triade ");
define("LANGIMP41","Invalid number of fields ");
define("LANGIMP42","Enter reference for each corresponding class ");
define("LANGIMP43","File not saved ");
// ------------------------------------------------------------------------------
define("LANGABS1","Attendance management");
define("LANGABS2","Plan an absence or tardiness");
define("LANGABS3","Enter student's name");
define("LANGABS4","Enter unexcused absences or tardiness");
define("LANGABS5","Unexcused absences list");
define("LANGABS6","Unexcused tardiness list");
define("LANGABS7","View or edit absence or tardiness");
define("LANGABS8","Enter student's name");
define("LANGABS9","Show or delete absence or tardiness");
define("LANGABS10","No student in database");
define("LANGABS11","Absence-tardiness");
define("LANGABS12","Excuse");
//define("LANGABS13","Tardy on ");
define("LANGABS13","Date of delay ");
define("LANGABS14","Tardy");
define("LANGABS15","Absence");
define("LANGABS16","Cancel");
define("LANGABS17","Change attendance");
define("LANGABS18","Absence from ");
define("LANGABS19","to ");
define("LANGABS20","Abs/Delay");
define("LANGABS21","lenght");
define("LANGABS22","Apology");
define("LANGABS23","Time/date");
define("LANGABS24","Setup attendance for Class ");
define("LANGABS25","Manage Attendance");
define("LANGABS26","Manage Atte  schedule");
define("LANGABS27","Save data for ");
define("LANGABS28","Data saved ");
define("LANGABS29","E"); //premiere lettre
define("LANGABS29bis","xempted from :"); //suite
define("LANGABS30","Expt");
define("LANGABS31","Class ");
define("LANGABS32","T"); //premiere lettre
define("LANGABS32bis","ardy lenght "); //suite
define("LANGABS33","in");
define("LANGABS34","period from");
define("LANGABS35","Attendance on ");
define("LANGABS36","update");
define("LANGABS37","Print today's absences, exemption, lateness  ");
define("LANGABS38","Phone");
define("LANGABS39","Phone Work Father ");
define("LANGABS40","Phone Work Mother");
define("LANGABS41","Phone Home ");
define("LANGABS42","Absent on ");
define("LANGABS43","for ");
define("LANGABS44","Day(s) ");
define("LANGABS45","Save update ");
define("LANGABS46","From ");
//----------------------------------------------------------------------------
define("LANGPROJ1","Choose a class");
define("LANGPROJ2","Choose a trimester");
define("LANGPROJ3","Trimester 1");
define("LANGPROJ4","Trimester 2");
define("LANGPROJ5","Trimester 3");
define("LANGPROJ6","<font class=T1>NO student in this class</font>");
define("LANGPROJ7","Number of tardiness");
define("LANGPROJ8"," Cumulative results");
define("LANGPROJ9","Discipline");
define("LANGPROJ10","minutes");
define("LANGPROJ11","Number of detention ");
define("LANGPROJ12","Assigned by ");
define("LANGPROJ13","List");
define("LANGPROJ14","student's average");
define("LANGPROJ15","Class average");
define("LANGPROJ16","student's average");
// ----------------------------------------------------------------------------
define("LANGDISP1","<font class=T1>No student with this name</font>");
define("LANGDISP2","reason");
define("LANGDISP3","physical");
define("LANGDISP4","Périod ");
define("LANGDISP5","in class ");
define("LANGDISP6","planned absence time ");
define("LANGDISP7","<B><font color=red>Enter</font></B>dd/mm/yyyy  <BR> in both fields");
define("LANGDISP8","Delete exemption");
define("LANGDISP9","Show <b>complete</B> exemption list");
define("LANGDISP10","in");
// ----------------------------------------------------------------------------
define("LANGASS1","TRIADE assistance");
define("LANGASS2","is a tech support service to help you with TRIADE<br /><br />If you are ahving a problem with one of TRIADE's features, please fill out this form. Our specialists will look at it promptly.");
define("LANGASS3","Current Member");
define("LANGASS4","Administration");
define("LANGASS5","Teacher");
define("LANGASS6","School life");
define("LANGASS6bis","Parent");
define("LANGASS7","Action");
define("LANGASS8","Create");
define("LANGASS9","View");
define("LANGASS10","Delete");
define("LANGASS11","Other");
define("LANGASS12","Duty");
define("LANGASS13","User's account");
define("LANGASS14","Message center");
define("LANGASS15","Assignemt");
define("LANGASS16","Database");
define("LANGASS17","class");
define("LANGASS18","Course");
define("LANGASS19","Search");
define("LANGASS20","Class test");
define("LANGASS21","Planning");
define("LANGASS22","planned absence");
define("LANGASS23","Discipline");
define("LANGASS24","Memo");
//define("LANGASS25","Report Card"); sam le 12/09/2014
define("LANGASS25","Transcript");
define("LANGASS26","Period");
define("LANGASS27","Comment");
define("LANGASS28","TRIADE assistance thanks you for your help");
define("LANGASS29","Service Triade.");
define("LANGASS30","Service Triade at your service");
define("LANGASS31","TRIADE is a unique service. Do not hesitate to contact us for help and suggestion. We would appreciate your input:-)");
define("LANGASS32","Guestbook");
define("LANGASS33","Your experience with TRIADE : write your comments here.");
define("LANGASS34","Your message has been send, we will get back to you shortly <br> <BR>Thanks for using TRIADE<BR><BR><BR><UL><UL>Technical Department<BR>");
define("LANGASS35","Other");
define("LANGASS36","SMS");
define("LANGASS37","WAP");
define("LANGASS38","Student's ID");
define("LANGASS39","barcode");
define("LANGASS40","prof dev");
// -----------------------------------------------------------------------------
define("LANGRECH1","<font class=T1>No student in this class</font>");
define("LANGRECH2","Search for ");
define("LANGRECH3","<font class=T1>No student in this search</font>");
define("LANGRECH4","Information / Modify");
// ---------------------------------------------------------------------------------
define("LANGBASE1","WARNING : Module to be used in first install only,<br> it will destroy  all students data (grade, report card, school life).");
define("LANGBASE2"," Files to be imported in  dbf format only");
define("LANGBASE3","File list ");
define("LANGBASE4","GEP file importation module");
define("LANGBASE5","GEP database importation ");
define("LANGBASE6","Number of students in DBF file ");
define("LANGBASE7","Number of registered students in database ");
define("LANGBASE8","Number of unregistered students in database ");
define("LANGBASE9","retrieve access code");
define("LANGBASE10","Unable to open file F_ele.dbf");
define("LANGBASE11","database processed -- Service Triade");
define("LANGBASE12","The selected file is not valid!");
define("LANGBASE13","Access code list");
define("LANGBASE14","Select all lines to get the list and copy/paste it in a \"txt|' file\" .");
define("LANGBASE15","with excel or OpenOffice, open \"txt\" file  en précisant le point virgule comme séparateur de champs.");
define("LANGBASE17"," Warning, Paswords only accessible on <br />this page !! Do not forget to get the list <b>BEFORE</b> ending ");
define("LANGBASE18","INFORMATION NONT AVAILABLE");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGBULL1","Print Trimester Report Card");
define("LANGBULL2","Enter class");
define("LANGBULL3","School year");
define("LANGBULL4","<a href=\"#\" onclick=\"open('https://www.adobe.com','_blank','')\"><b><FONT COLOR=red></FONT></B> Need <B>Adobe Acrobat Reader</B>.  FOr free Software download click <B>HERE</B></A>");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGPARENT1","no message");
define("LANGPARENT2","No delegates assigned yet");
define("LANGPARENT3","Student delegate(s)");
define("LANGPARENT4","Parent delegate(s)");
define("LANGPARENT5","Delegates list");
//----------------------------------------------------------------------//
define("LANGPUR3","WARNING: module to be used <br> if you want to empty the database");
define("LANGPUR5","all data have been erased");
define("LANGPUR6","Information : By selecting \"Students\" all grades, attendance, discipline and planned absences will be deleted ");
define("LANGPUR7","Selected items to be deleted: ");
define("LANGPUR8","Keep");
define("LANGPUR9","Delete");
//----------------------------------------------------------------------//
define("LANGCHAN0","students class modification modules");
define("LANGCHAN1","WARNING: Module to be used only <br>for students class modification");
define("LANGCHAN3","WARNING, All data for selected students will be erased");
//----------------------------------------------------------------------//
define("LANGGEP1",'Import GEP file');
define("LANGGEP2",'enter file');
//----------------------------------------------------------------------//
define("LANGCERT1"," download this certificate ");
//----------------------------------------------------------------------//
define("LANGPROFR1",'Enter tardy students');
define("LANGPROFR2",'lateness setup ');
define("LANGKEY1",'<font class=T1>No access code </font>');
define("LANGDISP20",'Add planned absences');
define("LANGPROFA","<br><center><font size=2>No access code </font><br><br>Please contact TRIADE administrator, <br>to validate your TRIADE's registration submission. </center><br><br>");
define("LANGPROFB",'Add a grade in ');
define("LANGPROFC",'Confirm grades to be saved ');
define("LANGPROFD",'Validate grades saving');
define("LANGPROFE",'&nbsp;&nbsp;<i><u>Info</u>: Type ENTER key to go to the next grade.</i>');
define("LANGPROFF",'Add a grade');
define("LANGPROFG",'Enter class');
//----------------------------------------------------------------------//
// Module Stage Pro
define("LANGSTAGE1","Internship management ");
define("LANGSTAGE2","View Internship dates ");
define("LANGSTAGE3","add ");
define("LANGSTAGE4","Assign ");
define("LANGSTAGE5","Insert Internship date ");
define("LANGSTAGE6","Modify Internship date ");
define("LANGSTAGE7","Delete Internship date ");
define("LANGSTAGE8","Companies management ");
define("LANGSTAGE9","View other companies ");
define("LANGSTAGE10","Add company ");
define("LANGSTAGE11","Modify company ");
define("LANGSTAGE12","Delete company ");
define("LANGSTAGE13","Students management");
define("LANGSTAGE14","View students in companies");
define("LANGSTAGE15","Assign Student to company ");
define("LANGSTAGE16","change student's setting ");
define("LANGSTAGE17","delete student's assignement' ");
define("LANGSTAGE18","View intership's dates");
define("LANGSTAGE19","Internship");
define("LANGSTAGE20","Serach company");
define("LANGSTAGE21","view companies by activites");
define("LANGSTAGE22","Consult companies");
//----------------------------------------------------------------------//
define("LANGPROFP1","Message for the class");
define("LANGPROFP2","Save message");
define("LANGPROFP3","Homeroom teacher's Message");
//----------------------------------------------------------------------//
define("LANGGEN1","Administration");
define("LANGGEN2","School life");
define("LANGGEN3","Teachers");
//----------------------------------------------------------------------//
define("LANGCALEN1","Event");
define("LANGCALEN2","schedule of ");
define("LANGCALEN3","Add an event");
define("LANGCALEN4","Delete an event");
define("LANGCALEN5","Refresh page");
define("LANGCALEN6","Event calendar");
define("LANGCALEN7","For class of ");
define("LANGCALEN8","Test in ");
define("LANGCALEN9","class test today:");

//----------------------------------------------------------------------//
//module reservation
define("LANGRESA1","Equipment management");
define("LANGRESA2","Room management ");
//---correction du mot equipment
define("LANGRESA3","Equipment list");
define("LANGRESA4","Room list");
define("LANGRESA5","add an equipment");
define("LANGRESA6","change an equipment");
define("LANGRESA7","delete an equipment");
define("LANGRESA8","add room");
define("LANGRESA9","delete room");
define("LANGRESA10","delete une room");
define("LANGRESA11","Equipment / room reservation ");
define("LANGRESA12","Equipment reservation ");
define("LANGRESA13","room reservation");
//----define("LANGRESA14","Reserve"); sam le 03/07/2014
define("LANGRESA14","Book");
define("LANGRESA15","equipment creation");
define("LANGRESA16","equipment name");
define("LANGRESA17","save creation");
define("LANGRESA18","more information");
define("LANGRESA19","Equipment created");
define("LANGRESA20","Create a room");
define("LANGRESA21","room name");
define("LANGRESA22","room created");
define("LANGRESA23","delete room");
define("LANGRESA24","room");
define("LANGRESA25","delete room");
define("LANGRESA26","room deleted");
define("LANGRESA27","one room");
define("LANGRESA28","Impossible to delete this room. \\n\\n room assigned.  ");
define("LANGRESA29","Equipment deleted");
define("LANGRESA30","Impossible to delete this equipment. \\n\\n Equipment assigned.  ");
define("LANGRESA31","one equipment");
define("LANGRESA32","delete equipment");
define("LANGRESA33","Equipment");
define("LANGRESA34","delete an equipment");
define("LANGRESA35","List of equipments");
define("LANGRESA36","DATE");
define("LANGRESA37","from");
define("LANGRESA38","to");
define("LANGRESA39","by");
define("LANGRESA40","Information");
define("LANGRESA41","Confirm");
define("LANGRESA42","Confirmed");
define("LANGRESA43","Not confirmed");
define("LANGRESA44","schedule equipment");
define("LANGRESA45","Equipment");
define("LANGRESA46","Equipment already reserved at this date");
define("LANGRESA47","view reservation schedule for this equipment");
define("LANGRESA48","reservation from");
define("LANGRESA49","on date of");
define("LANGRESA50","Equipment reserved, waiting for confirmation");
define("LANGRESA51","schedule room");
define("LANGRESA52","room");
define("LANGRESA53","room already reserved at this date");
define("LANGRESA54","room reserved, waiting for confirmation");
define("LANGRESA55","view reservation schedule for this room");
define("LANGRESA56","Confirm reservation");
define("LANGRESA57","schedule");
define("LANGRESA58","Confirm");
//----------------------------------------------------------------------//
define("LANGDST1","Class test submission");
define("LANGDST2","Hello, <br> <br> Your class test submission for the");
define("LANGDST3","<br><br><b>is impossible</b>, please choose another date or contact us. <br><br> Thanks");
define("LANGDST4","<br><br><b>is saved</b> COntact us for all other information. <br><br> Thanks");
define("LANGDST5","for the ");
define("LANGDST6","Subject / Course");
define("LANGDST7","Submission denied");
define("LANGDST8","Submission accepted");
//----------------------------------------------------------------------//
define("LANGTTITRE1","Members access");
define("LANGTTITRE2","Member");
define("LANGTTITRE3","Account Activation");
define("LANGTTITRE4","Please wait");
//--------------
define("LANGTP1","Last Name");
define("LANGTP2","First Name");
define("LANGTP3","Password");
define("LANGTCONNEXION","Connection");
define("LANGTERREURCONNECT","Connection error");
define("LANGTCONNECCOURS","Waiting for connection ");
define("LANGTFERMCONNEC","Log out");
define("LANGTDECONNEC","Waiting for log out");
define("LANGTBLAKLIST0",'<b><font color=red  class=T2>Your account has benn desactivited !!, To reactivate your acount, you must contact your school.</font></b>');

define("LANGMOIS1","January");
define("LANGMOIS2","February");
define("LANGMOIS3","March");
define("LANGMOIS4","April");
define("LANGMOIS5","May");
define("LANGMOIS6","June");
define("LANGMOIS7","July");
define("LANGMOIS8","August");
define("LANGMOIS9","September");
define("LANGMOIS10","October");
define("LANGMOIS11","November");
define("LANGMOIS12","December");


define("LANGDEPART1","of the pupil");

define("LANGVALIDE","Validate");
define("LANGIMP45","Edit");

define("LANGMESS34","Message not available.");
define("LANGMESS35","Make this group public .");
define("LANGMESS36","Remove message");

define("LANGRESA59","Name of the room");
define("LANGRESA60","Information");

define("LANGMAINT0","An intervention is envisaged on the software");
define("LANGMAINT1","The service triade will be inaccessible the ");
define("LANGMAINT2","between ");
define("LANGMAINT3","and");

define("LANCALED1","Previous year");
define("LANCALED2","Following year");

define("LANGTTITRE5","Access Problem");
define("LANGTTITRE6","Questions");
define("LANGTPROBL1","Currently, the Triade service is in service.");
define("LANGTPROBL2","I have a question");
define("LANGTPROBL3","Save the question");
define("LANGTPROBL4","Quit without saving");
define("LANGTPROBL5","Explain us your problem");
define("LANGTPROBL6","School establishment*: ");
define("LANGTPROBL7","Email : ");
define("LANGTPROBL8","Message : ");
define("LANGTPROBL9","(* Mandatory information)");
define("LANGTPROBL10","Save the problem");
define("LANGTPROBL12","We will respond to  your problem within the shortest time  --  Triade Service ");


define("LANGELEV1","School grades for");

define("LANGDEPART1","student");

define("LANGVALIDE","");
define("LANGIMP45","Edit");

define("LANGMESS34","Unrecoverable Message.");
define("LANGMESS35","Make this group public.");
define("LANGMESS36","Message deleted");
define("LANGMESS37","This function has not be validated by the Triade administrator");

define("LANGRESA59","Room name");
define("LANGRESA60","Information");

define("LANGMAINT0","An update is scheduled on this software");
define("LANGMAINT1","Triade will not be accessible on the  ");
define("LANGMAINT2","between");
define("LANGMAINT3","and");

define("LANCALED1","Last year");
define("LANCALED2","Next year");

define("LANGTTITRE5","Access problem");
define("LANGTTITRE6","Questions");
define("LANGTPROBL1","Triade is currently on.");
define("LANGTPROBL2","I have a question");
define("LANGTPROBL3","Save the question");
define("LANGTPROBL4","Quit without saving?");
define("LANGTPROBL5","describe your problem");
define("LANGTPROBL6","School*: ");
define("LANGTPROBL7","Email : ");
define("LANGTPROBL8","Message : ");
define("LANGTPROBL9","(* Mandatory fields)");
define("LANGTPROBL10","Save the problem");
define("LANGTPROBL11","Thank for your enquiry -- Triade Service");
define("LANGTPROBL12","We will respond to  your problem within the shortest time  --  Triade Service ");


define("LANGFORUM1","- Message list");
define("LANGFORUM2","No message has been posted on this forum");
define("LANGFORUM3","You can ");
define("LANGFORUM3bis"," post ");
define("LANGFORUM3ter"," a first message if you want");
define("LANGFORUM4","Post a new message");
define("LANGFORUM5","Forum - Post a message");
define("LANGFORUM6","User's agreement");
define("LANGFORUM7","Error: The requested message does not exist anymore.");
define("LANGFORUM8","Back to the posted messages list");
define("LANGFORUM9","--- original message ---");
define("LANGFORUM10","Your name ");
define("LANGFORUM11","Your email ");
define("LANGFORUM12","Subject ");
define("LANGFORUM13","Send"); // --> bouton envoyer
define("LANGFORUM14","Back to the posted messages list");
define("LANGFORUM15","Forum - send a message");
define("LANGFORUM16","<b>Error</b> : This page can only be retrieve<br> if a message has been previously ");
define("LANGFORUM16bis"," posted ");
define("LANGFORUM17","<b>Error</b> : no text in this message.<br>");
define("LANGFORUM18","<b>Error</b> : You forgot to enter your name.<br>");
define("LANGFORUM19","Error ! Your message could not be posted. ");
define("LANGFORUM20","<b>Error</b> : Cannot update the index file. <br>");
define("LANGFORUM21","Your message could not be posted.");
define("LANGFORUM22","Your message has benn correctly posted.<br>Thanks for your contribution.");
define("LANGFORUM23","Back to the posted messages list");
define("LANGFORUM24","Forum - read message");
define("LANGFORUM25","No message posted in this discussion forum.");
define("LANGFORUM26","You can ");
define("LANGFORUM26bis","post");
define("LANGFORUM26ter","a first message if you want.");
define("LANGFORUM27","This message does not exist or has been deleted by the forum administrator.<br>");
define("LANGFORUM28","Back to the posted messages list");
define("LANGFORUM30","Author");
define("LANGFORUM31","Date");
define("LANGFORUM32","Reply");
define("LANGFORUM33","Previous messages (in this topic)");
define("LANGFORUM34","Next messages (in this topic)");

define("LANGPROFH","In Class test for ");
define("LANGPROFI","Save in class test");
define("LANGPROFJ","In class Test ");
define("LANGPROFK","posted on   ");
define("LANGPROFL","Confirm date");
define("LANGPROFM","for ");
define("LANGPROFN","In class test of the ");
define("LANGPROFO","In class test ");
define("LANGPROFP","HOmeroom teachers setup");
define("LANGPROFQ","For tomorrow");
define("LANGPROFR","For yesterday");
define("LANGPROFS","Course or subject");
define("LANGPROFT","In class Test request validation");
define("LANGPROFU","Request send -- Triade Service ");


define("LANGPROJ17","Number of absences");
define("LANGPROJ18","day(s)");

define("LANGCALEN10","In class Test calendar");

define("LANGPARENT6","List delays"); // sam le 15/09/2014
define("LANGPARENT7","Absence list");
define("LANGPARENT8","Absent on ");
define("LANGPARENT9","Exemption list");
define("LANGPARENT10","Périod from  ");
define("LANGPARENT11","on"); // indique une date (heure)
define("LANGPARENT12","at"); // indique une date jour
define("LANGPARENT13","Certificate");
define("LANGPARENT14","Disciplinary sanction");
define("LANGPARENT15","Sanction");
define("LANGPARENT16","In detention");
define("LANGPARENT17","à");  // indique une heure
define("LANGPARENT18","detention done");
define("LANGPARENT19","Staff memos list");
define("LANGPARENT20","Access files");
define("LANGPARENT21","Visible by");
define("LANGPARENT22","Events calendar ");
define("LANGPARENT23","In class Test calendar ");
define("LANGPARENT24","In class Test request ");

define("LANGAUDIO1","Audio message");
define("LANGAUDIO2","on "); // indique une date
define("LANGAUDIO3","A"); // premiere lettre
define("LANGAUDIO3bis","udio message <br />in <b>mp3 format</b><br>maximum size file: ");
define("LANGAUDIO4","Save audio message");
define("LANGAUDIO5","Please, wait 2 to 3 minutes after sending the audio file.");
define("LANGAUDIO6","Delete audio file");


// non ajouté dans le fichier 06/09

define("LANGOK","Ok");
define("LANGCLICK","Click here");
define("LANGPRECE","Previous");
define("LANGERROR1","Lost data");
define("LANGERROR2","No data available");


define("LANGPROF1","Enter the course");
define("LANGPROF2","Number of grades");
define("LANGPROF3","Show grades");
define("LANGPROF4","group");
define("LANGPROF5","choose Trimester");
define("LANGPROF6","Subject "); // sujet du devoir
define("LANGPROF7","Name of subject "); // sujet du devoir
define("LANGPROF8","Grade"); //note d'un devoir
define("LANGPROF9","homework assignement");
define("LANGPROF10","Modify a grade");
define("LANGPROF11","Delete in class test"); // devoir --> interrogation
define("LANGPROF12","Homeroom teacher");
define("LANGPROF13","Student's file");
define("LANGPROF14","Add grade in ");
define("LANGPROF15","Modify grade in");
define("LANGPROF16","test's name");
define("LANGPROF17","test&nbsp;date"); // &nbsp; --> egal un blanc
define("LANGPROF18","Please wait");
define("LANGPROF19","Confirm grade modification");
define("LANGPROF20","Validate grade modification");
define("LANGPROF21","Modify grade in");
define("LANGPROF22","Show grades in");
define("LANGPROF23","Delete test in");
define("LANGPROF24","Test in "); // interrogation du
define("LANGPROF25","is deleted");
define("LANGPROF26","Student information");
define("LANGPROF27","Administration info");
define("LANGPROF28","School life informations");
define("LANGPROF29","Medical informations");
define("LANGPROF30","Information from");
define("LANGPROF31","about"); // indiquant une personne


define("LANGEL1","Last name");
define("LANGEL2","First name");
define("LANGEL3","Class ");
define("LANGEL4","Lv1");
define("LANGEL5","Lv2");
define("LANGEL6","Option latin");
define("LANGEL7","Status");
define("LANGEL8","DOB");
define("LANGEL9","Nationality");
define("LANGEL10","Password");
define("LANGEL11","Family name");
define("LANGEL12","First name");
define("LANGEL13","street");
define("LANGEL14","Address 1");
define("LANGEL15","city");
define("LANGEL16","Zipcode");
define("LANGEL17","street");
define("LANGEL18","Address 2");
define("LANGEL19","City");
define("LANGEL20","Zipcode");
define("LANGEL21","Telephone");
define("LANGEL22","Father's job");
define("LANGEL23","Father's phone");
define("LANGEL24","Mother's job");
define("LANGEL25","Mother's phone");
define("LANGEL26","School");
define("LANGEL27","School's pin number");
define("LANGEL28","City");
define("LANGEL29","Zip code");
define("LANGEL30","National number");

define("LANGPROF32","school information");
define("LANGPROF33","Homework assignement");
define("LANGPROF33","Homework assignement");
define("LANGPROF34","view by week");
define("LANGPROF35","Previous week");
define("LANGPROF36","Next week");

define("LANGTP23"," ATTENTION - room and equipment reservation  - CATEGORY");

define("LANGRESA61","equipment's name");





define("LANGacce_dep1","Login error");
define("LANGacce_dep2","Please check your login ID if the problem happens again <br />contact your Triade administrator via <br>the link 'Acces problem' in the left side menu bar");

define("LANGacce_ref1","Error code: unautorized access");
define("LANGacce_ref11","Visited on ");
define("LANGacce_ref12","by ");
define("LANGacce_ref13","with ");
define("LANGacce_ref2","UNAUTORIZED ACCES");
define("LANGacce_ref3","You need to login to access your account");
define("LANGacce1","the student ");
define("LANGacce12","has an work assignment due <br> following category ");
define("LANGacce13","for reason ");
define("LANGacce14","the work assignment is ");
define("LANGacce2","Delete this message : ");
define("LANGacce21","Delete");
define("LANGacce3","The student ");
define("LANacce31","did not come to</b></font> School life staff (CPE), <b>regarding his retention</b>,  following category :");
define("LANacce32","for reason: ");
define("LANGacce4","Work assignment is :");
define("LANGacce5","delete");
define("LANGacce6","Manage discipline");
define("LANGaccrob11","Downloading Adobe Acrobat Reader 8.1.0 fr");
define("LANGaccrob2","23,4 Mo  for Windows 2000/XP/2003/Vista");
define("LANGaccrob3","Downloading time :");
define("LANGaccrob4","in 56 K :  57 min and 3 s");
define("LANGaccrob5","in 512 K : 6 min and 14 s");
define("LANGaccrob6","in 5 M : 37 s");
define("LANGaccrob7","Downloading Adobe Acrobat Reader 6.O.1 fr");
define("LANGaccrob8","Size : ");
define("LANGaccrob9","0.40916 kb for NT/95/98/2000/ME/XP");
define("LANGaccrob10","in 56 K :0 min and 58.2 s");
define("LANGaccrob11bis","in 512K : 0 min and 6.6 s ");
define("LANGaffec_cre21","Assignment creation to class ");
define("LANGaffec_cre22","Setting up assignement in progress ");
define("LANGaffec_cre23","The assignment software will load automatically<br> Click if the new page does not appear");
define("LANGaffec_cre24","Triade - Account: ");
define("LANGaffec_cre31","CREATION - Assignment");
define("LANGaffec_cre41","Print");
define("LANGaffec_mod_key1","Class assignment");
define("LANGaffec_mod_key2","Class assignment modifying module");
define("LANGaffec_mod_key3","WARNING, this module is to be used only for modifications,<br> It destroys all students grdes from modified classes. ");
define("LANGaffec-mod_key4","WARNING, all claases grades ans notes will be deleted \\n Are you sure you want to continue ? \\n\\n Triade Service ");
define("LANGattente1","Wait - Triade");
define("LANGattente2","Please wait ...");
define("LANGattente3","Triade.Team");
define("LANGatte_mess1","TRIADE - Wait - Email");
define("LANGatte_mess2","Please wait ..");
define("LANGatte_mess3","TRIADE SERVICE");
define("LANGbasededon20","Send file");
define("LANGbasededon201","nothing");
define("LANGbasededon2011","Import GEP file");
define("LANGbasededon202","File transmitted -- Triade Service");
define("LANGbasededon203","File not saved");
define("LANGbasededon31","Indicate the class for each reference");
define("LANGbasededon32","Choice ...");
define("LANGbasededon33","none");
define("LANGbasededon34","Sending a file may last up to  <b>2 to 4 minutes</b> depending on the number of students.");
define("LANGbasededon35","The file's format must be<b>dbf</b> and  <b>F_ele.dbf</b>");
define("LANGbasededon41","Error on the number of classes !!! - Contact Triade Services <br /><br /> support@triade-educ.org</center>");
define("LANGbasededon42","Error entering a class, One class is repeated several times --Triade Service ");
define("LANGbasededon43","Message date: ");
define("LANGbasededon44","from");
define("LANGbasededon45","Member :");
define("LANGbasededon46","Message :");
define("LANGbasededon47","NEWS BASE:");
define("LANGbasededon48","- with GEP");
define("LANGbasededon49"," School :");
define("LANGbasededoni11","'Attention','./image/commun/warning.jpg','<font face=Verdana size=1><font color=red>T</font>he <b>dbase</b>module  has not been <br> activated !! <i>Needed for base GEP <br> importation .");
define("LANGbasededoni21","WARNING, the former database will automatically be destroyed. \n Are you sure you want to continue? \n\n Triade Service");
define("LANGbasededoni31","Indicate the category this file is linked with ");
define("LANGbasededoni32","Import related file ");
define("LANGbasededoni33","Import student(s) : ");
define("LANGbasededoni34","Import teacher(s):");
define("LANGbasededoni35","Import school life staff: ");
define("LANGbasededoni36","Import administrative staff: ");
define("LANGbasededoni41","Previous class");
define("LANGbasededoni42","Previous year");
define("LANGbasededoni51","For the title");
define("LANGbasededoni61","error");
define("LANGbasededoni71","ASCII file importation ");
define("LANGbasededoni72","Message date : ");
define("LANGbasededoni721","From");
define("LANGbasededoni722","Member :");
define("LANGbasededoni723","Message :");
define("LANGbasededoni724","NEW BASE:");
define("LANGbasededoni725","- with ASCII");
define("LANGbasededoni726"," School :");
define("LANGbasededoni73","Total saved in the database");
define("LANGbasededoni91","ASCII file importation ");
define("LANGbasededoni92","Error on the number of classes !!! - ContactTriade  Service <br />");
define("LANGbasededoni93","Error when entering the classes, one class has been repreated several times --Triade Service ");
define("LANGbasededoni94","Data from base has been processed -- Service Triade<br />");
define("LANGbasededoni95","Total number of students saved in base ");

define("LANGAPROPOS1","Version");
define("LANGAPROPOS2"," All Right reserved ");
define("LANGAPROPOS3","User's Licence ");
define("LANGAPROPOS4","Product ID");

define("LANGTELECHARGER","Download");
define("LANGPIEDPAGE","<p> La <b>T</b>ransparence et la <b>R</b>apidité de l'<b>I</b>nformatique <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br>Pour visualiser ce site de façon optimale : Internet Explorer 5 et Mozilla ----- résolution minimale : 800x600 <br>  © 2000/".date("Y")." Triade - Tous droits réservés");
define("LANGAJOUT1","For status : possible choice(<b>FTR</b>(Full Time Resident),<b>DR</b> (Day Resident), <b>HR</b> (Half resident)<br>");
define("LANGIMP44","This file does not have a valid format.");
define("LANGBASE16"," Colums are represented as follow :<b>login last name; login first name; Parent password; Student assword </b>");


define("LANGSUPP0","Delete substitute account");
define("LANGSUPP1","Deletion Module");
define("LANGSUPP2","Delete Account");
define("LANGSUPP3","Do you want to delete substitute's list");
define("LANGSUPP3bis","substitute for");
define("LANGSUPP4","Confirm deleting");
define("LANGSUPP5","Unable to delete this account\\n\\n Account affected to a class. \\n\\n   Triade Team");
define("LANGSUPP6","Account deleted - Triade Team");
define("LANGSUPP7","Delete a group");
define("LANGSUPP8","Delete the group");
define("LANGSUPP9","Delete a group ");
define("LANGSUPP10","Delete the group");
define("LANGSUPP11","a school life staff member");
define("LANGSUPP12","an administrator");
define("LANGSUPP13","a teacher");
define("LANGSUPP14","delete a student in a class");
define("LANGSUPP15","Click on student to delete");
define("LANGSUPP16","delete the student");
define("LANGSUPP17","is going to be erased from database");
define("LANGSUPP18","All information about this student are going to be deleted such as <br> (notes, attendance, exemption,discipline, informations, messages)");
define("LANGSUPP19","Cancel delete");
define("LANGSUPP20","has been erased from database");
//define("LANGSUPP21","Delete a class");
define("LANGSUPP21","Delete");
define("LANGSUPP22","Delete the class");
define("LANGSUPP23","Delete a course or a sub-course");
define("LANGSUPP24","Delete the course");
define("LANGSUPP25","Class deleted -- Triade Service ");
define("LANGSUPP26","Class deleted -- Triade Service");
define("LANGSUPP27","Create the course");
define("LANGSUPP28","Sub-course created");

define("LANGADMIN","Administrator");
define("LANGPROF","Teacher");
define("LANGSCOLAIRE","from school life");
define("LANGCLASSE","a class");

define("LANGGRP11","Group name");
define("LANGGRP12","Selected class(es)");
define("LANGGRP13","Students list");
define("LANGGRP14","Groups list");
define("LANGGRP15","Group creation");
define("LANGGRP16","Seletect student in this group");
define("LANGGRP17","Select");
define("LANGGRP18","Save group");
define("LANGGRP19","Group has been created");
define("LANGGRP20","Other group");
define("LANGGRP21","Group list");
define("LANGGRP22","Select the classes for group creation \\n\\n Triade Service");
define("LANGGRP23","Students group list");
define("LANGGRP24","Class list");
define("LANGGRP25","Course list");

define("LANGDONNEENR","<font class=T2>data saved.</font>");

define("LANGABS47","add a sanction to discipline");
define("LANGABS48"," has reached this category");
define("LANGABS48bis","times");
define("LANGABS49","duration");
define("LANGABS50"," Retention of");
define("LANGABS51","Father\'s Work phone number ");
define("LANGABS52","Mother\'s Work phone number  ");
define("LANGABS53","No Absence nor Lateness has been signed");

define("LANGCALRET1","Retention's calendar");

define("LANGHISTO1","History");

define("LANGDST9","Add an item");
define("LANGDST10","Delete an item");
define("LANGDST11","in class : ");

define("LANGDISP11","Show exemption<b>entire list </B>");

define("LANGEN","in");

define("LANGAFF4","Edit a class");
define("LANGAFF5","All classes");
define("LANGAFF6","Consult this class");

define("LANGCHER1","Advanced search");
define("LANGCHER2","Indicate in which format to generate file");
define("LANGCHER3","Indicate field gap");
define("LANGCHER4","Search student by name : <b>click here</b>");
define("LANGCHER5","Add");
define("LANGCHER6","Erase");
define("LANGCHER7","Up");
define("LANGCHER8","Down");
define("LANGCHER9","Nextt");
define("LANGCHER10","searched item");
define("LANGCHER11","Keywords number");
define("LANGCHER12","from");

define("LANGCHER13","with value");
define("LANGCHER14","extended search");
define("LANGCHER15","accurate search");
define("LANGCHER16","start search");
define("LANGCHER17","Warning, no item has been chosen!! -- Triade Team");

define("LANGCHER18","with value");

define("LANGTITRE34","Late mail configuration");
define("LANGTITRE35","Absence mail configuration");

define("LANGCONFIG1","Configuration saved");
define("LANGCONFIG2","here is your text ");

define("LANGCONFIG3","enter the list of parents who will received this message");

define("LANGERROR01","database error message");
define("LANGERROR02","WARNING Impossible <br><br>the problem may come from recently saved datas<br>(Verifiy all fields before submitting).<BR>  <BR>Or the information has already been saved or is not accessible.");
define("LANGERROR03","Access to the database has been denied for this action.<br>");

define("LANGABS54","has already been entered as absent for this date");
define("LANGABS55","has already been entered as tardy for this date");


define("LANGPARAM4","the School certificate has been saved.");
define("LANGPARAM5","the class students school certificate  ");
define("LANGPARAM5bis","is available, in PDF format ");
define("LANGPARAM6","Report card and period content configuration");

define("LANGPARAM7","Head of studies");
define("LANGPARAM8","School's Name");
define("LANGPARAM9","Address");
define("LANGPARAM10","Zip Code");
define("LANGPARAM11","City");
define("LANGPARAM12","Phone");
define("LANGPARAM13","E-mail");
define("LANGPARAM14","School logo ");
define("LANGPARAM15","Save");
define("LANGPARAM16","Configuration saved -- Triade Team");

define("LANGCERTIF1","The school certificate for ");
define("LANGCERTIF1bis","is available in PDF format");


define("LANGRECHE1","Student's information");

define("LANGBT52","Modify datas");

define("LANGEDIT1","data not found");

define("LANGMODIF1","Update student's account");
define("LANGMODIF2","Student info");
define("LANGMODIF3","Family info");

define("LANGALERT1","Data modified-- Triade Team");
define("LANGALERT2","Warning, file format or size not valid");
define("LANGALERT3","Warning, file format or size not valid");

define("LANGLOGO1","Logo to be forwarded");
define("LANGLOGO2","Save logo");
define("LANGLOGO3","Logo <b>must be in jpg format</b> and size 96px sur 96px.");

define("LANGPARAM17","Define trimester or semester periods");
define("LANGPARAM18","Trimester or Semester");
//define("LANGPARAM19","Begining date"); sam le 08/06/2014
define("LANGPARAM19","Start date");
define("LANGPARAM20","End date");
define("LANGPARAM21","First");
define("LANGPARAM22","Second");
define("LANGPARAM23","Third");
//define("LANGPARAM24","Save trimester dates"); sam le 08/06/2014
define("LANGPARAM24","Save");
//define("LANGPARAM25","Date valid, if saved in format Trimester "); sam le 08/06/2014
define("LANGPARAM25","Date valid if saved as quarter format ");
define("LANGPARAM26","Invalid date-- Triade Team");
define("LANGPARAM27","Informations saved -- Triade Team");
//define("LANGPARAM28","trimester"); sam le 08/06/2014
define("LANGPARAM28","Quarter");
define("LANGPARAM29","semester");
define("LANGPARAM30","Report card");


define("LANGBULL5","Print Report Card");
define("LANGBULL6","Continue activity");
define("LANGBULL7","Print period");
define("LANGBULL8","Enter begining date");
define("LANGBULL9","Enter end date");
define("LANGBULL10","Enter period's number");
define("LANGBULL11","Enter section");
define("LANGBULL12","Print period");
define("LANGBULL13","History");
define("LANGBULL14","<FONT COLOR='red'>Warning</FONT></B> You need <B>Adobe Acrobat Reader</B>.  Free software to download  ");
define("LANGBULL14bis","Download");
define("LANGBULL15","Show / Delete");
define("LANGBULL16","Student first name");
define("LANGBULL17","Teacher");
define("LANGBULL18","grades detail");
define("LANGBULL19","homeroom teacher's comment");
define("LANGBULL20","GRADE REPORT");
define("LANGBULL21","Period");

define("LANGBULL22","first trimester");
define("LANGBULL23","second trimester");
define("LANGBULL24","third trimester");

define("LANGBULL25","first semester");
define("LANGBULL26","second semester");

define("LANGBULL27","report card of ");
define("LANGBULL28","Section");
define("LANGBULL29","School year");

define("LANGBULL30","REPORT CARD");

define("LANGBULL31","Student");
define("LANGBULL32","Subject");
define("LANGBULL33","Class");
define("LANGBULL34","Comment, progress report, improvment advices");

define("LANGBULL35","Weight");
define("LANGBULL36","AVRG");
define("LANGBULL37","Mini");
define("LANGBULL38","Maxi");
define("LANGBULL39","Assiduity and behavior within the School : ");
define("LANGBULL40","Overall Comment from teaching team: ");
define("LANGBULL41","NO copy will be delivered");
define("LANGBULL42","Principal'signature");
define("LANGBULL43","ASCHOOL YEAR");
define("LANGBULL44","Mr. & Mrs");
define("LANGOU","ou"); // the or from or else


define("LANGPROJ19","Semester 1");
define("LANGPROJ20","Semester 2");

define("LANGDISC1","Retention from");
define("LANGDISC2","Print today's retention");


define("LANGDISC3","Phone Home ");
define("LANGDISC4","Father's Work Phone ");
define("LANGDISC5","Mother's Work Phone ");
define("LANGDISC6","Configure sanction in class of ");
define("LANGDISC7","Category name ");
define("LANGDISC8","Sanction's name ");
define("LANGDISC9","Entered by");
define("LANGDISC10","Reason, informations, WOrk assignement");
define("LANGDISC11","Retention");
define("LANGDISC11bis","Le");  // the to indicate the date
define("LANGDISC11Ter","A");  // At to indicate the time
define("LANGDISC12","length");
define("LANGDISC13","<font color=red>C</font></B>heck box if student is either in retention or sanctioned.");
define("LANGDISC14","Add a disciplinary sanction");
define("LANGDISC15","<B>*<I> D</B>: HOoe phone, <B>P</B>: Father's Work Phone, <B>M</B>: Mother's Work Phone</I>");
define("LANGDISC16","Done");
define("LANGDISC17","Phone");
define("LANGDISC18","Show Sanctions");
define("LANGDISC19","Show last <b>5</B> sanctions");
define("LANGDISC20","Category");
define("LANGDISC21","Complete list of ");
define("LANGDISC22","Show retention for ");
define("LANGDISC23","Show retention");
define("LANGDISC24","Show  <b>call</B> retention");
define("LANGDISC25","In&nbsp;retention");
define("LANGDISC26","Retention not done");
define("LANGDISC27","List sanction for");
define("LANGDISC28","Show Sanctions");
define("LANGDISC29","Show <b>all</B> sanctions");
define("LANGDISC30","Entered&nbsp;on");
define("LANGDISC31","List sanction for ");
define("LANGDISC32","Retention not assigned to a student");
define("LANGDISC33","WARNING student");
define("LANGDISC33bis"," is already in retention for this date and time. ");
define("LANGDISC34","has reached");
define("LANGDISC34bis","time this category");
define("LANGDISC35","Delete Sanction");
define("LANGDISC36","Delete Retention");

define("LANGattente222","Wait");



define("LANGSUPP","del"); // abreviation for delete



define("LANGCIRCU1","Manage administrative Memos");
define("LANGCIRCU2","Add a Memo");
define("LANGCIRCU3","Memo's list");
define("LANGCIRCU4","Delete a Memo");
define("LANGCIRCU5","Add administrative Memo");
define("LANGCIRCU6","Topic");
define("LANGCIRCU7","Reference");
define("LANGCIRCU8","Memo");
define("LANGCIRCU9","Teaching staff");
define("LANGCIRCU10","In class(es)");
define("LANGCIRCU11","<font face=Verdana size=1><B><font color=red>M</font></B>emo in format<b> doc</b>, <b>pdf</b>, <b>txt</b>.</FONT>");
define("LANGCIRCU12","<font face=Verdana size=1><B><font color=red>M</font></B>emos accessible to teachers.</FONT>");
define("LANGCIRCU13","All classes");
define("LANGCIRCU14","Back to menu");
define("LANGCIRCU15","Save memo");
define("LANGCIRCU16","Memo not saved");
define("LANGCIRCU17","The memo must be in format <b>txt or doc or pdf</b> and less than 2Mgb ");
define("LANGCIRCU18","<font class=T2>Saved Memos</font>");
define("LANGCIRCU19","Delete administrative Memos");
define("LANGCIRCU20","Access file");
define("LANGCIRCU21","<font color=red>R</b></font><font color=#000000>eference");

define("LANGCODEBAR1","Manage bar codes");
define("LANGCODEBAR2","This modules does not work with your server. <br> You must have PHP 5 or more to you this module.");
define("LANGCODEBAR3","Here is the list of bar codes accessible by Triade");
define("LANGCODEBAR4","The default bar code use is ");
define("LANGCODEBAR5","List");


define("LANGPUB1","Add an advertising banner");
define("LANGPUB2","You want to publish on TRIADE site");
define("LANGPUB3","Start an advertising campaign");
define("LANGPUB4","For this");
define("LANGPUB5","YO:U already are an advertiser on TRIADE ");

define("LANGPROFB1","Comments for trimester report card");
define("LANGPROFB2","Automatic comments configuration");
//define("LANGPROFB3","Configuration");
define("LANGPROFB3","Enter");
define("LANGPROFB4","Configuration report Card comments");
define("LANGPROFB5","Save Comment");
define("LANGPROFB6","Comment");
define("LANGPROFB7","List");


define("LANGPROFC1","Equipment Schedule Calendar");
define("LANGPROFC2","Room reservation Calendar");


define("LANGPARAM31","Show in USA mode");
define("LANGPARAM32","Assiduity and behavior within the school : ");
define("LANGPARAM33","get PDF file");

define("LANGDISC37","Add a disciplinary sanction");

define("LANGPROFP4","<b>Homeroom Teacher</b> in ");
define("LANGPROFP5","Student Information");
define("LANGPROFP6","Information from ");
define("LANGPROFP7","until ");

define("LANGPROFP8","lateness number total");
define("LANGPROFP9","lateness number this trimester");
define("LANGPROFP10","Absence number total");
define("LANGPROFP11","Absence number this trimester");

define("LANGPROFP12","Manage room parents");
define("LANGPROFP13"," in class ");
define("LANGPROFP14","Room Parents");
define("LANGPROFP15","Info");
define("LANGPROFP16","Class representative");
define("LANGPROFP17","Room Parent(s)");
define("LANGPROFP18","Class representative(s)");
define("LANGPROFP19","Tel"); // pour téléphone
define("LANGPROFP20","Email");
define("LANGPROFP21","Additional Medical Information for student");

define("LANGETUDE1","Manage Study Hall ");
define("LANGETUDE2","Assign student to study hall period");
define("LANGETUDE3","show study hall period list");
define("LANGETUDE4","Add study hall period");
define("LANGETUDE5","Modify study hall period");
define("LANGETUDE6","Delete study hall period");
define("LANGETUDE7","Show a study hall period");
define("LANGETUDE8","Assign a student to study hall period");
define("LANGETUDE9","Modify a student in study hall period");
define("LANGETUDE10","Delete a student from study hall period");
define("LANGETUDE11","Study hall period list");

define("LANGETUDE12","Student supervisor");
define("LANGETUDE13","Study hall");
define("LANGETUDE14","In room");
define("LANGETUDE15","Week");
define("LANGETUDE16","On");          // Le indique une date
define("LANGETUDE17","at");          // à indique une heure
define("LANGETUDE18","during");      //indique une durée
define("LANGETUDE19","Create a study hall period");
define("LANGETUDE20","Name of study hall");
define("LANGETUDE21","Day of the week");
define("LANGETUDE22","Study hall time");
define("LANGETUDE23","Durée de l'étude");
define("LANGETUDE24","hh:mm");
define("LANGETUDE25","Study hall room");
define("LANGETUDE26","This study hall supervisor");
define("LANGETUDE27","this study hall fas benn saved");
define("LANGETUDE28","Study Hall list");
define("LANGETUDE29","Modify study hall");
define("LANGETUDE30","This study hall contains student(s). Delete the list of student for this study hall proir to deleting the study hall");
define("LANGETUDE31","Student List");
define("LANGETUDE32","Students list");
define("LANGETUDE33","Assign a study hall to a student");
define("LANGETUDE34","Choose study hall");
define("LANGETUDE35","Indiqate class to assign student to this study hall");
define("LANGETUDE36","Mane of study hall");
define("LANGETUDE37","Indicate students for this study hall");
define("LANGETUDE38","Allowed to leave");
define("LANGETUDE39","Save study hall");
define("LANGETUDE40","other study hall");
define("LANGETUDE41","Modify a student's study hall");
define("LANGETUDE42","Student in study hall");
define("LANGETUDE43","Save change");
define("LANGETUDE44","Allowed to leave");
define("LANGETUDE45","Delete a student's study hall");

define("LANGLIST1","Edit a class");
define("LANGLIST2","Teachers for this class list");
define("LANGLIST3","Homeroom teacher");
define("LANGLIST4","Date");
define("LANGLIST5","Complete list in PDF format");
define("LANGLIST6","Homeroom teacher");


define("LANGPASS1","New password");

define("LANGTRONBI1","View students ID picture");
define("LANGTRONBI2","Modify students ID picture");
define("LANGTRONBI3","WARNING invalid file format ");
define("LANGTRONBI4","Impossible invalid picture size");
define("LANGTRONBI5","Student First Name");
define("LANGTRONBI6","Student Last Name");
define("LANGTRONBI7","the picture");
define("LANGTRONBI8","Add picture");


define("LANGBASE19","the selected file is invalid");
define("LANGBASE20","Student without class");
define("LANGBASE21","Number of student without class");
define("LANGBASE22","Show first 30th");
define("LANGBASE23","Change class for students");
define("LANGBASE24","Change saved");
define("LANGBASE25","GET OUR HELP BEFORE ANY CHANGE");
define("LANGBASE26","Change class for students in class");
define("LANGBASE27","Information about a student class change");
define("LANGBASE28","<b>No change.</b> <i>(with option 'choice...')</i>");
define("LANGBASE29","Information about this student has not been deleted.");
define("LANGBASE30","<b>The class change.</b> <i>(with class notice)</i>");
define("LANGBASE31","Delete student's grades, abs, lateness, disciplines, exemption.");
define("LANGBASE32","<b>Leaves school.</b>  <i>(With notice 'Leaves school')</i>");
define("LANGBASE33","Delete this student from Database.");
define("LANGBASE34","Delete student's grades, abs, lateness, disciplines, exemption.");
define("LANGBASE35","Delete internal messages from this family.");
define("LANGBASE36","Is going to class of");
define("LANGBASE37","Leaves school");
define("LANGBASE38","Save Change");
define("LANGBASE39","Choose an item");


define("LANGBASE40","Choice of ");
define("LANGAGENDA1","Warning!!!\n The note you have just created or modified is overlaping \nwith another note for the following users");
define("LANGAGENDA2","Do you want to delete this note that has been assigned to you ?");
define("LANGAGENDA3","Deleting a note, reminder :\\n\\n - All occurences related to this notes will also be deleted\\n - To delete only one occurence, click on the relating icon right to the note in the schedule\\n\\nDe you want to delete this note ?");
define("LANGAGENDA4","Deleting an occurence, reminder :\\n\\n - Only this occurence will be deleted\\n - To delete a recurring note and all its occurences, clickz the cross sign right to the note in the schedule or edit the note and click the [delete]button \\n\\nDo you want to delete this occurence ?");
define("LANGAGENDA5","Note with reminder");
define("LANGAGENDA6","Delete an occurence");
define("LANGAGENDA7","delete a note");
define("LANGAGENDA8","Get a note");
define("LANGAGENDA9","Show details");
define("LANGAGENDA10","Personal note");
define("LANGAGENDA11","Asigned note");
define("LANGAGENDA12","Active note");
define("LANGAGENDA13","Discontinued note");
define("LANGAGENDA14","This day");
define("LANGAGENDA15","Holiday;");
define("LANGAGENDA16","Create a note");
define("LANGAGENDA17","click to modify");
define("LANGAGENDA18","Save birthday date");
define("LANGAGENDA19","Modify birthday date");
define("LANGAGENDA20","Enter person's name");
define("LANGAGENDA21","Enter person's birthday date ");
define("LANGAGENDA22","Birthday of");
define("LANGAGENDA23","Birthday date");
define("LANGAGENDA24","Format dd/mm/yyyy");
define("LANGAGENDA25","Delete this birthday date ?");
define("LANGAGENDA26","Delete");
define("LANGAGENDA27","Cancel");
define("LANGAGENDA28","Save");
define("LANGAGENDA29","Are you sure you want to delete this birthday date?");
define("LANGAGENDA30","Modify");
define("LANGAGENDA31","Prev. Year");
define("LANGAGENDA32","Prev. Month");
define("LANGAGENDA33","Go to today's date");
define("LANGAGENDA34","kepp for menu");
define("LANGAGENDA35","Next Month");
define("LANGAGENDA36","Next Year");
define("LANGAGENDA37","Select a date");
define("LANGAGENDA38","Move");
define("LANGAGENDA39","Today");
define("LANGAGENDA40","Regarding calendar");
define("LANGAGENDA41","Show %s first");
define("LANGAGENDA42","Close");
define("LANGAGENDA43","Click or drag to modify value");
define("LANGAGENDA44","Unknown user");
define("LANGAGENDA45","Your session has expired!");
define("LANGAGENDA46","This login is already in use");
define("LANGAGENDA47","The previous password is invalid");
define("LANGAGENDA48","login in to use Phenix");
define("LANGAGENDA49","The connection to the SQL server has failed");
define("LANGAGENDA50","Profile modified");
define("LANGAGENDA51","Note saved");
define("LANGAGENDA52","Note updated");
define("LANGAGENDA53","Note deleted");
define("LANGAGENDA54","This note's occurence deleted");
define("LANGAGENDA55","Birthday saved");
define("LANGAGENDA56","Birthday updated");
define("LANGAGENDA57","Birthday deleted");
define("LANGAGENDA58","Account created, You can now login");
define("LANGAGENDA59","The registration has failed");
define("LANGAGENDA60","All fields");
define("LANGAGENDA61","Company");
define("LANGAGENDA62","Last Name + First Name");
define("LANGAGENDA63","Address");
define("LANGAGENDA64","Phone number");
define("LANGAGENDA65","Email");
define("LANGAGENDA66","Comments");
define("LANGAGENDA67","Start search");
define("LANGAGENDA68","Company");
define("LANGAGENDA69","Last name");
define("LANGAGENDA70","First name");
define("LANGAGENDA71","Address");
define("LANGAGENDA72","City");
define("LANGAGENDA73","Country");
define("LANGAGENDA74","Home phone");
define("LANGAGENDA75","Work phone");
define("LANGAGENDA76","Cell phone");
define("LANGAGENDA77","Fax");
define("LANGAGENDA78","Email");
define("LANGAGENDA79","Email work");
define("LANGAGENDA80","Note / Misc");
define("LANGAGENDA81","Group");
define("LANGAGENDA82","Share");
define("LANGAGENDA83","Zipcode");
define("LANGAGENDA84","Birthday date");
define("LANGAGENDA85","Start again");
define("LANGAGENDA86","Import");
define("LANGAGENDA87","Import finished");
define("LANGAGENDA88","contact(s) added");
define("LANGAGENDA89","No contact available!");
define("LANGAGENDA90","<LI>In Outlook, go to  <I>File</I>-&gt;<I>Export</I>-&gt;<I>other address book...</I></LI>");
define("LANGAGENDA91","<LI>Choose <I>Text file (comma separated value(s))</I> then<I>Export</I></LI>");
define("LANGAGENDA92","<LI>Browse for destination folder then <I>Next</I></LI>");
define("LANGAGENDA93","<LI>In the list of fields to be exported select<BR>");
define("LANGAGENDA94","<I>First Name, Last Name, Email Address, Street (home), City (home), Zip code (home), Country/Region (home), Home phone, Cell phone, Work phone, Fax work, Company</I> then click on <I>Finish</I></LI>");
define("LANGAGENDA95","<LI>Get the generated file in the form below and click on  <I>Import</I></LI>");
define("LANGAGENDA96","Enter a compagny for search");
define("LANGAGENDA97","Enter a Last or First name for search");
define("LANGAGENDA98","Enter an address for search");
define("LANGAGENDA99","Enter a phoe number for search");
define("LANGAGENDA100","Enter an email address for search");
define("LANGAGENDA101","Enter comment sample for search");
define("LANGAGENDA102","Enter a criteria for search");
define("LANGAGENDA103","Are you sure you want to delete this contact ?");
define("LANGAGENDA104","Year");
define("LANGAGENDA105","No father");
define("LANGAGENDA106","List of person you can assign a note to ");
define("LANGAGENDA107","Available person(s)");
define("LANGAGENDA108","Selected person");
define("LANGAGENDA109","Display settings");
define("LANGAGENDA110","by 30mn");
define("LANGAGENDA111","by 15mn");
define("LANGAGENDA112","Begining time");
define("LANGAGENDA113","Ending time");
define("LANGAGENDA114","Buzy");
define("LANGAGENDA115","Partial");
define("LANGAGENDA116","Free");
define("LANGAGENDA117","Create a note between");
define("LANGAGENDA118","Details for this day's user(s)");
define("LANGAGENDA119","Show");
define("LANGAGENDA120","Select a person");
define("LANGAGENDA121","Select an ending date from after the beginig date");
define("LANGAGENDA122","Week of");
define("LANGAGENDA123","to");
define("LANGAGENDA124","Next week");
define("LANGAGENDA125","Remove");
define("LANGAGENDA126","Your contacts availaibility for ");
define("LANGAGENDA127","add");
define("LANGAGENDA128","Off profile");
define("LANGAGENDA129","Seletc a beginig date prior to the ending date ");
define("LANGAGENDA130","Display settings");
define("LANGAGENDA131","Enter a name");
define("LANGAGENDA132","Enter a URL");
define("LANGAGENDA133","Ajdd a favorite");
define("LANGAGENDA134","Landscape printing");
define("LANGAGENDA135","Previous week");
define("LANGAGENDA136","Week ");
define("LANGAGENDA137","from");
define("LANGAGENDA138","Birthday");
define("LANGAGENDA139","Default reminder of a note's creation");
define("LANGAGENDA140","No reminder");
define("LANGAGENDA141","Reminder");
define("LANGAGENDA142","copy by email");
define("LANGAGENDA143","minute(s)");
define("LANGAGENDA144","hour(s)");
define("LANGAGENDA145","day(s)");
define("LANGAGENDA146","Default day");
define("LANGAGENDA147","Finished");
define("LANGAGENDA148","Phone");
define("LANGAGENDA149","Interface");
define("LANGAGENDA150","Default agenda");
define("LANGAGENDA151","Daily");
define("LANGAGENDA152","Weekly");
define("LANGAGENDA153","Monthly");
define("LANGAGENDA154","30 minutes");
define("LANGAGENDA155","15 minutes");
define("LANGAGENDA156","45 minutes");
define("LANGAGENDA157","1 hour");
define("LANGAGENDA158","Automatic selection of ending time for a note");
define("LANGAGENDA159","Shared agenda<BR>in consultation");
define("LANGAGENDA160","Persons autorised to view my agenda");
define("LANGAGENDA161","Not shared");
define("LANGAGENDA162","by choice");
define("LANGAGENDA163","Everyone");
define("LANGAGENDA164","Shared agenda<BR>in modification");
define("LANGAGENDA165","Person(s) who can assign a note to me");
define("LANGAGENDA166","Notify me by email when a note has been assigned to me");
define("LANGAGENDA167","Delete this note that I created");
define("LANGAGENDA168","Delete this note that has been assigned to me");
define("LANGAGENDA169","Acquire this note that has been assigned to me");
define("LANGAGENDA170","All day");
define("LANGAGENDA171","Choice of labelling");
define("LANGAGENDA172","New labelling");
define("LANGAGENDA173","title");
define("LANGAGENDA174","Default period");
define("LANGAGENDA175","Colorr");
define("LANGAGENDA176","Note's configuration");
define("LANGAGENDA177","Delete this labelling ?");
define("LANGAGENDA178","Save a memo");
define("LANGAGENDA179","Enter a title");
define("LANGAGENDA180","Title");
define("LANGAGENDA181","Content");
define("LANGAGENDA182","Are you sure you want to delete this memo?");
define("LANGAGENDA183","ASave a note");
define("LANGAGENDA184","The note you want to modify belong to a recursive serie");
define("LANGAGENDA185","Do you want to modify the entire Serie or thie occurence only ?");
define("LANGAGENDA186","The entire serie");
define("LANGAGENDA187","This occurence only");
define("LANGAGENDA188","All day note");
define("LANGAGENDA189","View Calendar");
define("LANGAGENDA190","All day");
define("LANGAGENDA191","Start at");  // start at
define("LANGAGENDA192","affected<BR>Person");
define("LANGAGENDA193","note configuration");
define("LANGAGENDA194","Public note");
define("LANGAGENDA195","Note details in the shared agenda");
define("LANGAGENDA196","mention \"Busy\" in the shared agenda");
define("LANGAGENDA197","Private note");
define("LANGAGENDA198","Busy");
define("LANGAGENDA199","considered as <B>unavailable</B> in the availability module");
define("LANGAGENDA200","free");
define("LANGAGENDA201","Considered as <B>free</B> in the shared agenda");
define("LANGAGENDA202","Color");
define("LANGAGENDA203","share");
define("LANGAGENDA204","availability");
define("LANGAGENDA205","Reminder");
define("LANGAGENDA206","No reminder");
define("LANGAGENDA207","Email copy");
define("LANGAGENDA208","in advance");  // à l'avance
define("LANGAGENDA209","Periodicity");
define("LANGAGENDA210","None");
define("LANGAGENDA211","Daily");
define("LANGAGENDA212","Weekly");
define("LANGAGENDA213","Monthly");
define("LANGAGENDA214","Yearly");
define("LANGAGENDA215","Every ");
define("LANGAGENDA215bis","day");
define("LANGAGENDA216","Every open days (Monday thru Friday)");
define("LANGAGENDA217","Every default week days");
define("LANGAGENDA218","Entered or modified information will not be saved\\nAre you sure you want to continue ?");
define("LANGAGENDA219","profile");
define("LANGAGENDA220","Every ");
define("LANGAGENDA221","Every ");
define("LANGAGENDA221bis","week");
define("LANGAGENDA222","of every month");
define("LANGAGENDA223","first");
define("LANGAGENDA224","second");
define("LANGAGENDA225","third");
define("LANGAGENDA226","fourth");
define("LANGAGENDA227","last");
define("LANGAGENDA228","of the month");
define("LANGAGENDA229","The ");
define("LANGAGENDA230","Select ending date");
define("LANGAGENDA231","End after"); // Fin après
define("LANGAGENDA232","End on");
define("LANGAGENDA233","occurence(s)");
define("LANGAGENDA234","Enter labelling");
define("LANGAGENDA235","Enter a date");
define("LANGAGENDA236","Select an ending hour\\nposterior to the begining one");  // \\n signifie un retour chariot
define("LANGAGENDA237","select a person");
define("LANGAGENDA238","Select a number of days\\ngreater or equal to 1");
define("LANGAGENDA239","Enter a number of occurences\\ngreater or equal to 1");
define("LANGAGENDA240","Repetition"); // répétition
define("LANGAGENDA241","Enter your last and first name first");
define("LANGAGENDA242","Enter your first name");
define("LANGAGENDA243","Your must enter your login");
define("LANGAGENDA244","Enter your old password");
define("LANGAGENDA245","Passwords are different");
define("LANGAGENDA246","Password is mandatory");
define("LANGAGENDA247","Select an ending hour\\nposterior to the beginig one");
define("LANGAGENDA248","Delete this occurence");
define("LANGAGENDA249","Recurrent note");
define("LANGAGENDA250","Delete this note that I created");
define("LANGAGENDA251","Acquire this note that was affected to me ");
define("LANGAGENDA252","Filter");
define("LANGAGENDA253","Print this agenda");
define("LANGAGENDA254","Landscape printing mode strongly adviced");
define("LANGAGENDA255","Note created by");
define("LANGAGENDA256","Change status");
define("LANGAGENDA257","Delete this occurence");
define("LANGAGENDA258","Delete this  note that I have created");
define("LANGAGENDA259","Delete this note that was affected to me");
define("LANGAGENDA260","a note");
define("LANGAGENDA261","a birthday");
define("LANGAGENDA262","a contact");
define("LANGAGENDA263","To the user selected below");
define("LANGAGENDA264","Add a note");
define("LANGAGENDA265","Search");
define("LANGAGENDA266","Availibility");
define("LANGAGENDA267","Contacts");
define("LANGAGENDA268","Memo");
define("LANGAGENDA269","Labeling");
define("LANGAGENDA270","Favorites");
define("LANGAGENDA271","Profile");
define("LANGAGENDA272","Export creation failed");
define("LANGAGENDA273","Agenda of ");


define("LANGL","M");  // M of Monday
define("LANGM","T");  // T of Tuesday
define("LANGME","W");  // w of Wednesday
define("LANGJ","T");  // T of Thursday
define("LANGV","F");  // F of Friday
define("LANGS","S");  // S de Saturday
define("LANGD","S");  // s of Sunday

define("LANGL1","Mon"); // Days on three letters
define("LANGM1","Tue");    // Days on three letters
define("LANGME1","Wed"); // Days on three letters
define("LANGJ1","Thu");    // Days on three letters
define("LANGV1","Fri");    // Days on three letters
define("LANGS1","Sat");    // Days on three letters
define("LANGD1","Sun");    // Days on three letters

define("LANGMOIS21","Jan");            // abreviate month
define("LANGMOIS22","Feb");         // mois abregé
define("LANGMOIS23","Mar");            // mois abregé
define("LANGMOIS24","Apr");                // mois abregé
define("LANGMOIS25","May");                // mois abregé
define("LANGMOIS26","Jun");            // mois abregé
define("LANGMOIS27","Jul");            // mois abregé
define("LANGMOIS28","Aug");        // mois abregé
define("LANGMOIS29","Sep");            // mois abregé
define("LANGMOIS210","Oct");            // mois abregé
define("LANGMOIS211","Nov");             // mois abregé
define("LANGMOIS212","Dec");     // mois abregé



define("LANGPROFP22","This teacher has already been assigned as homeroom teacher\\n\\n Triade Team");



define("LANGSTAGE23","Activity Name");
define("LANGSTAGE24","Enter a new Company");
define("LANGSTAGE25","This company name has already been saved");
define("LANGSTAGE26","Company name");
define("LANGSTAGE27","Contact");
define("LANGSTAGE28","Address");
define("LANGSTAGE29","Zip code");
define("LANGSTAGE30","City");
define("LANGSTAGE31","Field of activity");
define("LANGSTAGE32","Add activity");
define("LANGSTAGE33","Main activity");
define("LANGSTAGE34","Phone");
define("LANGSTAGE35","Fax");
define("LANGSTAGE36","Email");
define("LANGSTAGE37","Information");
define("LANGSTAGE38","Consult Companies");
define("LANGSTAGE39","Company");
define("LANGSTAGE40","Main Activity");
define("LANGSTAGE41","other search");
define("LANGSTAGE42","Tel / Fax");
define("LANGSTAGE43","No company under this name");
define("LANGSTAGE44","Schedule internship");
define("LANGSTAGE45","Internship starting date");
define("LANGSTAGE46","Internship ending date");
define("LANGSTAGE47","Save internship");
define("LANGSTAGE48","Internship number");
define("LANGSTAGE49","Modify internship dates");
define("LANGSTAGE50","Internship");
define("LANGSTAGE51","Internship dates");
define("LANGSTAGE52","Error entering datas");
define("LANGSTAGE53","UPdate internship");
define("LANGSTAGE54","Internship of ");
define("LANGSTAGE55","for class of");
define("LANGSTAGE56","is saved");
define("LANGSTAGE57","Internship date deleted \\n\\n Triade Team");
define("LANGSTAGE58","Company saved\\n\\n Triade Team");
define("LANGSTAGE59","Modify conpany");
define("LANGSTAGE60","Consult company by activity");
define("LANGSTAGE61","Rsearch company");
define("LANGSTAGE62","Info");
define("LANGSTAGE63","Entire list");
define("LANGSTAGE64","View internship dates");
define("LANGSTAGE65","Delete company");
define("LANGSTAGE66","Company deleted \\n\\n Triade team");
define("LANGSTAGE67","Consult company by activity");
define("LANGSTAGE68","No company under this name");
define("LANGSTAGE69","View intership for a student");
define("LANGSTAGE70","Print internship number");
define("LANGSTAGE71","Viinternships for a student");
define("LANGSTAGE72","&nbsp;Internship&nbsp;dates&nbsp;"); // respecter les &nbsp;
define("LANGSTAGE73","Back");
define("LANGSTAGE74","Company");
define("LANGSTAGE75","Assign a student to an internship");
define("LANGSTAGE76","Internship location");
define("LANGSTAGE77","Person in charge");
define("LANGSTAGE78","Mentor teacher");
define("LANGSTAGE79","Overnight accomodation");
define("LANGSTAGE80","Meals provided");
define("LANGSTAGE81","going through different services");
define("LANGSTAGE82","Reason for changing service");
define("LANGSTAGE83","Other information");
define("LANGSTAGE84","Creation saved \\n \\n Triade Team");
define("LANGSTAGE85","Visit date");
define("LANGSTAGE86","Modify a student to an internship");
define("LANGSTAGE87","Information saved");
define("LANGSTAGE88","delete a student to an internship");


define("LANGRESA62","Labelling");
define("LANGRESA63","Denied");
define("LANGRESA64","Add a request");
define("LANGRESA65","&nbsp;De&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to");
define("LANGRESA66","already booked");
define("LANGRESA66bis","by");  //  booked by
define("LANGRESA67","not confirmed");
define("LANGRESA68","Confirmed");
define("LANGRESA69","Saving finished");
define("LANGRESA70","booked for");


define("LANGNOTEUSA1","Assigned notes for USA mode configuration");
define("LANGNOTEUSA2","This module enables you to generate a letter based grade according to a percentile grade");
define("LANGNOTEUSA3","Example : de 95 to 100 --> A+ , de 87 to 94  --> A, etc...");
define("LANGNOTEUSA4","From");
define("LANGNOTEUSA4bis","to");
define("LANGNOTEUSA4ter","equals");   //  ex : De  10 à 20 équivaut à B
define("LANGNOTEUSA5","Between note");
define("LANGNOTEUSA5bis","and note");
define("LANGNOTEUSA5ter","equals to");


define("LANGABS56","List of unexcused absences");
define("LANGABS57","Update done ofr this list of students");

define("LANGSANC1","Sanction create -- Triade Team");
define("LANGSANC2","Category not deleted. This category is already assigned to a sanction or a student -- Equipe Triade");
define("LANGSANC3","Discipline Configuration ");
define("LANGSANC4","Save categories.");
define("LANGSANC5","Categories title");
define("LANGSANC6","Saved sanctions name by categories.");
define("LANGSANC7","Sanctions title");
define("LANGSANC8","Retention Configuration ");
define("LANGSANC9","Warning message when a student has reached the authorized limit.");
define("LANGSANC10","For this category");
define("LANGSANC11","Warning message after");
define("LANGSANC12","Number of time");
define("LANGSANC13","Created by");
define("LANGSANC14","Date entered");



define("LANGMODIF4","Modify account");
// define("LANGMODIF5","Connection Information"); le 16/06/2014
define("LANGMODIF5","Connection Identification");
define("LANGMODIF6","Picture ID");
define("LANGMODIF7","Account Specs");
define("LANGMODIF8","Address");
define("LANGMODIF9","Zip code");
define("LANGMODIF10","City");
define("LANGMODIF11","Phone");
define("LANGMODIF12","Email");
define("LANGMODIF13","Modify account");
define("LANGMODIF14","Account modified -- Triade  Team");
define("LANGMODIF15","The account for");
define("LANGMODIF15bis"," has been changed.");
define("LANGMODIF16","Modify password");
define("LANGMODIF17","Invalid picture size");
define("LANGMODIF18","Refresh picture");
define("LANGMODIF19","Add picture");
define("LANGMODIF20","Modify picture");

define("LANGGRP25bis","Manage groups");
define("LANGGRP26","Groups list");
define("LANGGRP27","Add a student to a group");
define("LANGGRP28","Delete student from a group");
define("LANGGRP29","Group name");
define("LANGGRP30","Affected Classe(s)");
define("LANGGRP31","Modifylist");
define("LANGGRP32","Add students to this group");
define("LANGGRP33","Add a student to this group");
define("LANGGRP34","Student in class of ");
define("LANGGRP35","Student in group");
define("LANGGRP36","Save group");
define("LANGGRP37","Groupe modified -- Triade Team");
define("LANGGRP38","Group students list ");
define("LANGGRP39","No student in this group");

define("LANGCARNET1","Grade book");
define("LANGCARNET3","Click on student/'<b>name</b>");

define("LANGPASSG1","Password must be of <br><b>8 characters</b> minimum,<br> <b>alphanumerical</b> and <br> using <b>capital and<br> small letters</b>.");
define("LANGPASSG2","Invalid password. \\n The password must contain : \\n\\n -> 8 characters minimum, \\n --> alphanumerical, \\n --> capital and small letters \\n\\n'Triade Team");
define("LANGPASSG3","Creation failed");



define("LANGDISC38","Add sanction");
define("LANGDISC39","Manage discipline");
define("LANGDISC40","Retention not done.");
define("LANGDISC41","Retention agenda.");
define("LANGDISC42","Retention not affected to a student.");
define("LANGDISC43","Configuration.");
define("LANGDISC44","Delete Retentions and sanctions");
define("LANGDISC45","Delete Retentions and sanctions");
define("LANGDISC46","List abesences and lateness for a class");
define("LANGDISC47","Enter periods begining");
define("LANGDISC48","Enter periods end");
define("LANGDISC49","Enter section");
define("LANGDISC50","<br><ul>Delete Retentions and sanctions according to <br>dates bracket.</ul>");
define("LANGDISC51","All classes");
define("LANGDISC52","Retentions and sanctions Deleted ");
define("LANGDISC53","Error ! Retentions and sanctions NOT deleted");

define("LANGIMP53","ASCII file via SQL ");

define("LANGSTAGE31bis","2nd activity section");
define("LANGSTAGE31ter","3rd activity section");
define("LANGMEDIC1","Student physical info");
define("LANGMEDIC2","start search");
define("LANGMEDIC3","Information / Modification");
define("LANGDISC54","show student's disciplne");
define("LANGDISC55","delete Sanction");
define("LANGDISC56","delete Sanction");
define("LANGBASE6bis","total number of students in file ");
define("LANGMODIF21","password must have : \\n\\n - 8 characters minimum \\n - Alphanumeric \\n - uppercase and minorcase.\\n\\n Triade Team");
define("LANGMODIF22","Password : 8 charactèrs - Alphanumérical - uppercase and minorcase");
define("LANGPASS1bis","Confirm password");
define("LANGMODIF23","On can change your passird from your Triade Account");
define("LANGMODIF24","The account ");
define("LANGMODIF24bis","is being validated..");
define("LANGMODIF24ter","is now fully operational");
define("LANGMODIF25","Password notidentical. \\n\\n Triade Team");
define("LANGABS58","view / lete  Absence - tardy");
define("LANGABS59","display all tardy");
define("LANGABS60","during");  // the period from ..to
define("LANGABS61","View / Modify  Absence - tardy");
define("LANGABS62","display <b>complete</B> list of tardy and abs");
define("LANGABS63","entered on");
define("LANGABS64","diplay of <b>5</B> last tardy and abs");
define("LANGABS65","Complete display of absence");
define("LANGABS66","update done for this students list");
define("LANGABS6bis","show  list of unjustified tardy");
define("LANGABS4bis","View list of unjustified absences");
define("LANGABS67","<font class=T2>no student in this class</font>");
define("LANGABS68","class list of abs/tardy");
define("LANGABS69","student's summary of abs/tardy");
define("LANGABS70","Settings");
define("LANGABS71","Number of absences / Total");
define("LANGABS72","Number of tardy / total");
define("LANGABS73","Absence - Tardy -  for class ");
define("LANGABS74","update");
define("LANGABS75","no absences/lateness");
define("LANGABS76","last update at ");
define("LANGDEPART3","due to a technical problem.");
define("LANGDEPART4","access to server is currently impossible. Triade Team is currently working on it.");
define("LANGIMP25_2","school name");
define("LANGABS77","notified on");
// define("LANGSTAGE89","Etablish internship contracts"); Sam le 01092015
define("LANGSTAGE89","Set up an internship agreement");
// define("LANGSTAGE90","Sort internship contract");
define("LANGSTAGE90","Edit internship agreement");
define("LANGSTAFE91","List of students in company");
define("LANGSTAGE92","List of students in company");
define("LANGPASSG4","Password must be <b>8 characters long</b> minimum <br /><b>alphanumérical</b>.");
define("LANGPASSG5","Password must be  <b>4 characters long</b> minimum.");
define("LANGPASSG6","Incorrect password. \\n Password must have: \\n\\n -> 8 characters minimum, \\n -> alphanumeric \\n\\n Triade Team");
define("LANGPASSG7","Incorrect password. \\n passwrod must be: \\n\\n -> 4 characters long minimum. \\n\\n Triade Team");
define("LANGMODIF22_1","Password : 4 characters");
define("LANGMODIF22_2","Password : 8 characters - Alphanuméric ");
define("LANGMODIF22_3","Mot de passe : 8 caractères - Alphanuméric - upper and minor case ");
define("LANGDEPART2","<font color=red  class=T2>WRANING, to use TRIADe, the php variable '<strong>register_globals</strong>' must be at <u>Off</u>.</font><br />");
define("LANGacce15","Homework assignment due on ");
define("LANGacce16","Homework assignment due today !");
define("LANGacce17","Add a disciplinary sanction");
define("LANGBASE41","delete all student before import");
define("LANGBASE7bis","student already assgined");
define("LANGBASE8bis","for the students <u>assigned</u> and <u>without class</u>");
define("LANGPER21bis","Language / option");
define("LANGASS6ter","student");
define("LANGASS41","Storage");
define("LANGASS42","configuration");
define("LANGIMP46bis","password");
define("LANGIMP54","N° street");
define("LANGIMP55","address");
define("LANGIMP56","zip code");
define("LANGIMP57","phone");
define("LANGIMP58","email");
define("LANGIMP59","contry");
define("LANGBULL1pp","print tremestrial ro semetrial report card");
define("LANGBT43pp","Print table");
define("LANGMESS38","Message read.");
define("LANGMESS39","Message not read.");
define("LANGDISC57","Justification&nbsp;/&nbsp;Sanction");

define("CUMUL01","Compile  absences and tardiness for a class by student");
define("CUMUL02","Compile  of sanctions for a class bystudent");
define("CUMUL03","Compile of sanctions for a student");
define("LANGPROJ18bis","hour(s)");
define("LANGCREAT1","Account already exists.");
define("ERREUR1","Internet network is not available for this module."); // sam
define("ERREUR2","Refer to the module configuration to activate the network.");


define("PASSG8","Change password");
define("PASSG9","Student's password");
define("PASSG9bis"," has been changed.");


define("LANGPARAM34","School's website");
define("LANGLOGO3bis","The logo <b> format must in jpg</b>");


//define("LANGMAT1","Save course");
define("LANGMAT1","Save");
//define("LANGMAT2","List / Modify course");
define("LANGMAT2","List / Modify");
//define("LANGMAT3","Delete course");
define("LANGMAT3","Delete");
define("LANGMAT4","Save changes");
define("LANGMAT5","Course modified");
define("LANGMAT6","Course already affected");
//define("LANGCLAS1","List / Modify class");
define("LANGCLAS1","List / Modify");
define("LANGCLAS2","Class modified");
define("LANGCLAS3","Class already modified");

define("LANGDEVOIR1","for group");
define("LANGDEVOIR2","for class");
define("LANGDEVOIR3","Save class test");
define("LANGCIRCU111","<font face=Verdana size=1><B><font color=red>D</font></B>ocument in format : <b> doc</b>, <b>pdf</b>, <b>txt</b>.</FONT>");

define("LANGAFF7","Module to erase class assignment.");
define("LANGAFF8","WARNING, this module is to be used to delete class assignment. ,<br> it erases all students grades in deleted classes.");
define("LANGAFF9","WARNING,selected class grades will be erased. \\n Are you sure you want to do that ? \\n\\nTRIADE team");
define("LANGCREAT2","Delete an account");


define("LANGPROF37","Homework agenda."); 


// -------------------------------------------
define("LANGLETTRELUNDI","M");	  // Lundi
define("LANGLETTREMARDI","T");    // Mardi
define("LANGLETTREMERCREDI","W"); // Mercredi
define("LANGLETTREJEUDI","T");    // Jeudi
define("LANGLETTREVENDREDI","F"); // vendredi
define("LANGLETTRESAMEDI","S");   // samedi
define("LANGLETTREDIMANCHE","S"); // Dimanche
// --------------------------------------------

define("LANGPARAM35","Choose report card type");
define("LANGPROBLE1","respond by email");
define("LANGPROBLE2","all fields must be filled");
define("LANGMESS37","This module has not been validated by the TRIADE administrator.<br><br>Triade Team");

define("LANGPROFP23","Grades for");
define("LANGPROFP24","for month of ");
define("LANGPROFP25","Picture ID");
define("LANGPROFP26","Student's followup");
define("LANGPROFP27","Delegates info");
define("LANGPROFP28","Message for class");
define("LANGPROFP29","Memo for class");
define("LANGPROFP30","Internship managment");
define("LANGPROFP31","Students average table");
define("LANGPROFP32","Stuedents graphic chart");

define("LANGRESA71","reservation for");
define("LANGRESA72","from");
define("LANGRESA73","to");
define("LANGRESA74","More information");

define("LANGbasededoni52","value accepted : <b>0</b> ou M.<br>");
define("LANGbasededoni53","value accepted : <b>1</b> ou Mme.<br>");
define("LANGbasededoni54","value accepted : <b>2</b> ou Mlle.<br>");
define("LANGbasededoni54_2","value accepted : <b>3</b> ou Ms <br>");
define("LANGbasededoni54_3","value accepted : <b>4</b> ou Mr <br>");
define("LANGbasededoni54_4","value accepted : <b>5</b> ou Mrs <br>");

define("LANGacce_dep2bis","<br><b>WARNING !!  Check your access mode,<br> choose the appropriate account type.</b>");


define("LANGNA3bis","Parent password"); 
define("LANGNA3ter","Student password "); 

define("LANGIMP46","First name");
define("LANGIMP47","Prefix (Mr. or Mrs or Ms) ");
define("LANGIMP48","Last name");
define("LANGIMP49","* requiered field");
define("LANGIMP50","The file to be transmitted<FONT color=RED><B>MUST</B></FONT> contain <FONT COLOR=red><B>9</B></FONT>fields <I>(non vides)</I> separated by the same \"<FONT color=red><B>;</B></font>\" <I>or 8 occcurences of the\"<FONT color=red><B>;</B></font>\"</I>");

define("LANGIMP51","parent password");

define("LANGIMP52","student password");

define("LANGELE244","E-mail");

define("LANGTP12","please validate account");

define("LANGMESS40","You have <strong> ");
define("LANGMESS40bis"," </strong>registered RSS feed(s).");  
define("LANGMESS41","Account ");  // Accountlike "cuser account".
define("LANGMESS42","second connection");
define("LANGMESS43","Last connection on");

define("LANGALERT4","WARNING, choose different names for you topics.");

define("LANGMODIF26","Modify sub-course");
//define("LANGPROF38","Trimester grade"); le 02/09/2014
define("LANGPROF38","Grades/Marks");
define("LANGPROF39","More information");
define("LANGCIRCU21","Availbl for"); // abréviation for "Available for"

define("LANGTELECHARGE","download"); //  downloader

define("LANGPARENT15bis","Sanction for");
define("LANGDISC2bis","print sanctions for");

define("LANGRECH5","enter item(s) to be searched");
define("LANGRECH6","sort by order");

define("LANGPROFP33","fill out report cards");
define("LANGPROFP34","check report cards");
define("LANGPROFP35","consult or modify report cards comments"); 


//---------------------------------------------------------------------------
define("LANGPROFP36","no trimester date assigned to  <u>this school year</u>");
define("LANGPROFP37","save commments");
define("LANGGRP40","Group  created");
define("LANGGRP41","list of unregistered students");
define("LANGGRP42","This group already exists");
define("LANGGRP43","file error");
define("LANGGRP44","delete a group");
define("LANGGRP45","Import file");
define("LANGGRP46","existing group name -- Service Triade");

define("LANGPARAM37","School District");
define("LANGAGENDA274","Today's calendar's name ");
define("LANGPARAM38","Happy Birthday to  ");

define("LANGEDT1","F"); // first letter
define("LANGEDT1bis","ile's format <b>xml</b> or <b>zip</b> <br>maximum file's size : ");
define("ERREUR3","Contact Triade's administrator to activate network.");

define("LANGELE30","Change password");
define("LANGMESS44","Send a message to a student in : ");
define("LANGMESS5","Send a message to a parent in : ");
define("LANGMESS45","Send a message to an email : ");
define("LANGMESS2","Send a message to the school MGT : ");
define("LANGCARNET2","Student's class");
define("LANGTITRE15","Homeroom teacher or teacher's configutation");
define("LANGPER7","assigned to class ");
define("LANGPROF40","Additional Information");
define("LANGPROFP38","Edit student's report");

define("LANGEDIT2","Cellphone1");
define("LANGEDIT3","Civility ");
define("LANGEDIT4","Name guard1");
define("LANGEDIT5","First Name guard2");
define("LANGEDIT6","Birth place");
define("LANGEDIT7","Civility ");
define("LANGEDIT8","Name guard1");
define("LANGEDIT9","Cellphone2");
define("LANGEDIT10","Parent");
define("LANGEDIT11","Student E-mail");
define("LANGEDIT12","Phone Student");
define("LANGEDIT13","Guardian's E-mail");

define("LANGEDIT14","from today");
define("LANGEDIT15","for 1 day");
define("LANGEDIT16","for 2 days");
define("LANGEDIT17","for 3 days");
define("LANGEDIT18","for 4 days");
define("LANGEDIT19","Unexcused absences");
define("LANGEDIT20","Cell phone");

define("LANGSMS1","Send SMS for tardyness since ");
define("LANGSMS2","Not indicated");

define("LANGSUPPLE","Substitutes list");
define("LANGSUPPLE1","Substituting for ");
define("LANGTITRE2","School news");
define("LANGTITRE1","Events");

define("LANGEDIT20","Del");  // abreviation for delete !! 3 letters only

define("LANGDISC58","Add a class to a student");
define("LANGDISC59","USA grade system");
define("LANGDISC60","Test ");

define("LANGBT8","List / Modify Administrators");
define("LANGBT9","List / Modify Councelor (school life)");
//define("LANGBT10","List / Modify Teachers");
define("LANGBT10","List / Modify Professors");
define("LANGDIRECTION","Direction");

// define("LANGTITRE36","Manage Administrators"); le 16/06/2014
define("LANGTITRE36","Management Board");
define("LANGTITRE37","Manage Councelor (School Life)");
// define("LANGTITRE38","Manage Teachers"); le 16/06/2014
define("LANGTITRE38","Professors Management");
define("LANGTITRE39","Manage Substitutes");
define("LANGTITRE40","Student");
define("LANGTITRE41","mngr."); // for manager abreviation"
define("LANGTITRE42","guardian"); // as in family guardian
//define("LANGTITRE43","Manage a student"); le 17/06/2014
define("LANGTITRE43","Student management");
define("LANGTITRE44","Import students list");
define("LANGTITRE45","search student");
define("LANGCHERCH1","According to search criteria");
define("LANGCHERCH2","end of search");
define("LANGCHERCH3","Number of items found");

define("LANGPROF3bis","View Homework, and tests");

define("LANGTROMBI","Export students list to WellPhoto");


define("LANGPURG1","Delete informations");
define("LANGPUR2","Delete informationss");

define("LANGPROFP39","Yearly average chart :");


define("LANGBLK1","Your account has been activated.<br /><br />You have tried to access an unauthorized page.<br /><br />Contact your school administrator to reactivate your account.<br /><br />TRIADE team.");

define("LANGCARNET4","access");

define("LANGFORUM10bis","Your first name ");
define("LANGTPROBL11","We will contact you as soon as possible. \\n\\n  TRIADE Team");

define("LANGTRAD1","List of recent actions");



define("LANGPARAM39","Certificate registered");
define("LANGPARAM40","Certificate not registered");
define("LANGPARAM41","File must be in format <b>rtf</b> and be less than 2Mo");

define("LANGBASE42","Import file");
define("ACCEPTER","Accept");
define("LANGCONDITION","I accept the terms");

define("LANGPARAM42","List of non tardyness or absences");


define("LANGCARNET5","View Student report");
define("LANGCARNET6","fill out Student Report");
define("LANGCARNET7","fill out");
define("LANGCARNET8","Student Report");

define("LANGCARNET9","Create a Student Report");
define("LANGCARNET10","Modify a Student Report");
define("LANGCARNET11","Delete a Student Report");
define("LANGCARNET12","View a Student Report");
define("LANGCARNET13","Export a Student Report");
define("LANGCARNET14","Import a Student Report");
define("LANGCARNET15","Import");
define("LANGCARNET16","Export");
define("LANGCARNET17","Menu Student Report");
define("LANGCARNET18","Name of Student Report");
define("LANGCONTINUER","Continue --->");
define("LANGCARNET19","Creation of a Student Report");
define("LANGCARNET20","Grading scale to be chosen by Teachers");
define("LANGCARNET21","Letters");
define("LANGCARNET22","Numeric");
define("LANGCARNET23","Colors");
define("LANGCARNET24","Grades");
define("LANGCARNET25","(0 to 10 or 0 to 20)");
define("LANGCARNET26","Compare to ");
define("LANGCARNET27","acquired");
define("LANGCARNET28","needs improvement");
define("LANGCARNET29","not acquired");
define("LANGCARNET30","in developpement");
define("LANGCARNET31","N/A");
define("LANGCARNET32","Green");
define("LANGCARNET33","Blue");
define("LANGCARNET34","Orange");
define("LANGCARNET35","Red");
define("LANGCARNET36","period");
define("LANGCARNET37","periods");
define("LANGCARNET38","Manage student report");
define("LANGCARNET39","Number(s) of period(s) requiring Principal, parents and teachers signature ");
define("LANGCARNET40","Number(s) ");
define("LANGCARNET41","Sections associated to this students Report");
define("LANGCARNET42","Sections");
define("LANGCARNET43","Maximum 4 possible choices (the 4 first will be saved)");
define("LANGCARNET44","Student report saved. You can now add skills to this report.");
define("LANGCARNET45","Add a skill group ");
define("LANGCARNET46","skill group name ");
define("LANGCARNET47","Is this name relevant to a skill section ?  ");
define("LANGCARNET48","Name");
define("LANGCARNET49","Add a skill ");
define("LANGCARNET50","Edit the students report general configuration  ");
define("LANGCARNET51","Add a skill group ");
define("LANGCARNET52","Edit skill group ");
define("LANGCARNET53","choose Report ");
define("LANGCARNET54","Student Report does not exist ");
define("LANGCARNET55","View Student Report");
define("LANGCARNET56","a student Report");
define("LANGCARNET57","Get Student Report in PDF format ");
define("LANGCARNET58","Export Student Report");
define("LANGCARNET59","To get Student Report");
define("LANGCARNET60","Edit a Student Report");
define("LANGCARNET61","Delete a Student Report");
define("LANGCARNET63","Import a Student Report");
define("LANGCARNET64","File to import");
define("LANGCARNET65","Delete all schedule before import ?");
define("LANGCARNET66","Import canceled. <br><br>This Student report already exists ! <br />Please delete this report before importation.");
define("LANGCARNET62","WARNING !! All grades assigned to this Report will be deleted.");
define("LANGEDT2","Import Visual Timetabling schedules");
define("LANGEDT3","Import Visual Timetabling done");
define("LANGEDT4","View / Manage Schedule");
define("LANGEDT5","Import Visual Timetabling Schedule");
define("LANGEDT6","Export Triade to Visual Timetabling");
// define("LANGEDT7","View / Manage Schedule"); sam le 02/07/2014
define("LANGEDT7","Set up schedule");
define("LANGEDT8","Manage");
define("LANGEDT9","Set up schedule");
define("LANGEDT10","Module SQLite not supported, please update your server to authorize SQlite support .");

define("LANGGRP47","Search Groups");
define("LANGGRP48","List a student's groups");
define("LANGGRP49","List of groups");

define("LANGDISP21","Configure Change abs / trdy");
define("LANGDISP22","Save excuses ");
define("LANGDISP23","Excuse's name ");
define("LANGDISP24","Liste of excuses ");
define("LANGDISP25","Number of updated students");
define("LANGDISP26","File must be in xls format");


// --------- FIN Traduction //------------
// ----Traduit le 18/05/2014 par sam
define("LANGCARNET67","disciplinary penalty");
define("LANGCARNET68","Schedule");

define("LANGVIES1","Name of student");
define("LANGVIES2","Coefficient de la vie scolaire sur la bulletin");
define("LANGVIES3","Coefficient Enseignant");
define("LANGVIES4","Coefficient Vie scolaire");
define("LANGVIES5","Academic staff");
define("LANGVIES6","Additional information");
define("LANGVIES7","Save grades and comments");
define("LANGVIES8","Print class absences");
define("LANGVIES9","Provide the month");
define("LANGVIES10","Provide the class ");

define("LANGPDF1","a pdf file");
define("LANGPDF2","a pdf file by student");
define("LANGEDIT5bis","Prénom Resp. 1");

define("LANGGRP50","Change group name");
define("LANGGRP51","Group name");
define("LANGGRP52","Module Modification");
define("LANGGRP53","New group name");
define("LANGGRP54","or transcript");
define("LANGGRP55","exam");

define("LANG1ER","1st");
define("LANG2EME","2nd");
define("LANG3EME","3rd");
define("LANG4EME","4th");
define("LANG5EME","5th");
define("LANG6EME","6th");
define("LANG7EME","7th");
define("LANG8EME","8th");
define("LANG9EME","9th");

define("LANGGRP56","Scoring on");
define("LANGGRP57","Save");
define("LANGGRP58","Be careful ! Grades of the selected<br />students will be removed in all classes !!!");
define("LANGGRP59","Uncheck  student (s) not belonging to the group");
define("LANGGRP60","Amend the list");

define("LANGPARAM3","<font class=T1>Enter your text to the contents of the certificate of attendance. For automatic consideration of the name, surname and address of the student in each document, please specify expected chain <b>NomEleve</b>, <b>PrenomEleve</b>, <b>AdresseEleve</b>, <b>CodePostalEleve</b> et <b>VilleEleve</b> to the desired location. Similarly, ability to specify the class with the keyword <b>ClasseEleve</b> or <b>ClasseEleveLong</b>, date of birth with <b>DateNaissanceEleve</b>, birthplace via <b>LieuDeNaissance</b>, the current date via <b>DateDuJour</b>, the school year via <b>AnneeScolaire</b>.</font><br><br>");

define("LANGEDIT20bis","Del");  // abréviation de Supprimer  sur 3 lettres seulement

define("LANGGRP61","Back to update");
define("LANGRTDJUS","Justified"); // pour un retard
define("LANGABSJUS","Justified"); // pour une abs
define("LANGPARAM2","<font class=T1>Enter your text to the message in order  to send to parents. You can specify the following information: Nom de l'élève : <b>NomEleve</b> - Prénom de l'élève : <b>PrenomEleve</b> - Adresse : <b>AdresseEleve</b> - Code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'élève : <b>ClasseEleve</b> - Date du retard : <b>RTDDATE</b> - Heure du retard : <b>RTDHEURE</b> - Durée : <b>RTDDUREE</b>  - Cumul absence : <b>CumulABS</b> - Date du jour : <b>DATEDUJOUR</b> </font><br><br>");

define("LANGPARAM1","<font class=T1>Enter your text to the message in order to send to parents. You can specify the following information : Nom de l'élève : <b>NomEleve</b> - Prénom de l'élève : <b>PrenomEleve</b> - Adresse : <b>AdresseEleve</b> - Code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'élève : <b>ClasseEleve</b> - Date de début d'absence :  <b>ABSDEBUT</b> - Date de fin d'absence : <b>ABSFIN</b> - Durée : <b>ABSDUREE</b> - Nom du responsable 1 : <b>NomResponsable1</b> - Adresse responsable 1 : <b>AdresseResponsable1</b> - Ville responsable 1 : <b>VilleResponsable1</b> - Cumul absence : <b>CumulABS</b> </font><br><br>");

define("LANGGRP62","Studies");
define("LANGGRP63","Mail");

define("LANGDELEGUE1","délégué");
define("LANGEDT10bis","Module SimpleXML non supporté. Veuillez valider votre serveur pour la prise en charge de l'extension SimpleXML.");


define("LANGBULL45","Send a message to teachers to ask them to fill the transcripts.");
define("LANGBULL46","Number of transcripts filled-in");

define("LANGMESS46","View in");
define("LANGMESS47","Delete a penalty");


define("LANGCOUR","Mail completed");
define("LANGCOUR1","Liste des retenues non effectuées");
define("LANGCOUR2","Configuration du courrier de retenu");


define("RESA75","Additional information");
define("LANGCOM","Save your comments in your library.");
define("LANGCOM1","The maximum value must be greater than the minimum value.");
define("LANGCOM2","All fields must be properly identified.");
define("LANGCOM3","Number of students : ");
define("LANGSTAGE91","Name of supervisor");
//define("LANGSTAGE93","Function."); sam le 16 09 2015
define("LANGSTAGE93","Position.");
define("LANGSTAGE94","of the company");
define("LANGSTAGE95","Company");
define("LANGSTAGE96","Number of items found");
define("LANGSTAGE97","Please, enter a numeric value, S.V.P.");
define("LANGSTAGE98","Please enter the startdate of the training, S.V.P.");
define("LANGSTAGE99","Please enter the training  completion date, S.V.P.");

define("LANGPATIENTE","Please wait");
define("LANGSMS3","Mobile phone");
define("LANGSMS4","Maximum 150 characters");
define("LANGSMS5","Message");
define("LANGSMS6","Sending an SMS message is stored and available for management");
define("LANGSMS7","Sending an SMS message");
define("LANGSMS8","Send a SMS");
define("LANGSMS9","Parents phone number <br> de ");
define("LANGSMS10","Send an SMS to a class");
define("LANGSMS11","Send an SMS to a students parent via his/her name");
define("LANGSMS12","Send an SMS to a person via his/her name");
define("LANGSMS13","Send an SMS to person via his / her number");
define("LANGSMS14","Number");

define("LANGbasededoni54_5","valeur acceptée : <b>7</b> ou P <br>");
define("LANGbasededoni54_6","valeur acceptée : <b>8</b> ou Sr <br>");

define("LANGGRP27bis","Add a student in different groups");
define("LANGGRP28bis","Add a student in a group");
define("LANGGRP29bis","Saisie&nbsp;/&nbsp;Modif");
define("LANGNOTEUSA6","Grading system concordance table");
define("LANGNOTE1","Exam subject");
define("LANGPARAM44","Receive a message when you receive such an information ");
define("LANGMESS17bis","Config.");
define("LANGNNOTE2","Sort by class");
define("LANGNNOTE3","Sort by name");
define("LANGNNOTE4","Enter document title");
define("LANGBULL47","Bulletin sans sous-matière");
define("LANGBULL48","Bulletin avec sous-matière");
define("LANGBULL49","Bulletin examen blanc");

define("LANGMESS48","Deleted items");
define("LANGMESS49","No student has been affected to a company.");
define("LANGMESS50","Plan de la classe");
define("LANGPARAM43","<font class=T1>Enter your text to the selected message for sending mail to student parents. You can specify the following information : Nom de l'élève : <b>NomEleve</b> - Prénom de l'élève : <b>PrenomEleve</b> - adresse : <b>AdresseEleve</b> - code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'élève : <b>ClasseEleve</b> - la date de la retenu : <b>DATERETENU</b> - l'heure de la retenu : <b>HEURERETENU</b> - la durée : <b>RETENUDUREE</b> - le motif : <b>RETENUMOTIF</b> -  la catégorie : <b>RETENUCATEGORY</b> - Attribué par : <b>ATTRIBUEPAR</b> - Devoir à faire : <b>DEVOIRAFAIRE</b> - Les faits : <b>FAITS</b>  - Civilité tuteur 1 : <b>CIVILITETUTEUR1</b> - Nom du responsable 1 : <b>NOMRESP1</b> Prénom du responsable 1 : <b>PRENOMRESP1</b> - Date du jour : <b>DATEDUJOUR</b>  </font><br><br>");
define("LANGMESS51","Enter optionnal subjets");
define("LANGMESS52","(Grades recorded in the overall average, if it is above 10/20)");
define("LANGMESS53","Previous week");
define("LANGMESS54","Next week");
define("LANGMESS55","Class schedule ");
define("LANGMESS56","No student");

define("LANGMESS57","Login");
define("LANGMESS58","This account has no number.");
define("LANGMESS59","Amend justified abs and delays");
define("LANGMESS60","A");
define("LANGMESS60bis","bsent");
define("LANGMESS61","of academic staff");
define("LANGMESS62","Parent of ");
define("LANGMESS63","Today");  // mettre une \' 
define("LANGBT27bis","Save");
define("LANGDEPART3bis","Interrupted access ! ");
define("LANGDEPART4bis","Access to  Triade is currently interrupted. please contact your school for more information.");
define("LANGAIDE","Online helpdesk");
define("LANGAIDE1","Indiquer les correspondances entre vos matiéres enregistrées dans TRIADE et les matières renseignées pour le brevet des collèges. Pour cela effectuer un drag&drop (glisser&relacher) entre les matières de gauche à droite.");
define("LANGAIDE2","Type your text for the content of the internship agreement. For a consideration of element such as name, address etc ... please specifyi the following chain to suit your needs");

define("LANGBREVET1","Enter");
define("LANGCONFIG4","Be notified by a message when ");
define("LANGCONFIG5","The number of unexcused absences of a student has exceeded ");
define("LANGCONFIG6","The number  of unjustified delays of a student has exceeded ");
define("LANGCONFIG7","Times");
define("LANGCONFIG8","Liste des utilisateurs avertis");

define("LANGMESS64","People who have received this message");
define("LANGMESS65","Liste des règlements interne");
define("LANGMESS66","The director");
define("LANGMESS67","I have read the documents above mentionned");
define("LANGMESS68","I accept the school rules and regulations");
define("LANGMESS69","J'accepte les conditions générales d'enseignement");
define("LANGMESS70","rules ans regulations for academic staff");
define("LANGMESS71","Consulter Fiche d'état des réglements");
define("LANGMESS72","Imprimer Fiche d'état des réglements");
define("LANGMESS73","List of outstanding or incomplete payment");
define("LANGMESS74","Fiche d'état des réglements");
define("LANGacce_dep2ter","<br><b>ATTENTION !!  Vérifiez bien votre mode d'accès, choisissez votre compte correspondant.</b>");



define("LANGMESS75","Back to menu");
define("LANGMESS76","Correspondance");
define("LANGMESS77","(Tests, mid-term and final exams)");
define("LANGMESS78","Sort by ");
define("LANGMESS79","Visible grades for students on ");
define("LANGMESS80","vie scolaire");
define("LANGMESS81","Connecting");
define("LANGMESS82","Average");
define("LANGMESS83","Class average");
define("LANGMESS84","Max");
define("LANGMESS85","Min");
define("LANGMESS86","Aucune date trimestrielle affectée");
define("LANGMESS86bis","For");
define("LANGMESS86ter","cette année scolaire");
define("LANGMESS87","Note des devoirs de");

define("LANGMESS88","Course diary saved -- Triade");
define("LANGMESS89","Course diary ");
define("LANGMESS90","Save your content before changing tab.");
define("LANGMESS91","Consultation of the week");
define("LANGMESS92","Course content");
define("LANGMESS93","Attached file");
define("LANGMESS94","Attached file");
define("LANGMESS95","Course objective");
define("LANGMESS96","Homework due to ");
define("LANGMESS97","N/A");
define("LANGMESS98","To do list");
define("LANGMESS99","Note pad");
define("LANGMESS100","Consultation completed");
define("LANGMESS101","Validation");
define("LANGMESS102","Consultation");
define("LANGMESS103","Estimated time ");
define("LANGMESS104","Estimated time ");
define("LANGMESS105","File ");
define("LANGMESS106","Modification ");
define("LANGMESS107","Delete ");
define("LANGMESS108","Total estimated time ");
define("LANGMESS109","from"); // notion de date du xxxx au xxxx
define("LANGMESS110","to"); // notion de date du xxxx au xxxx
define("LANGMESS111","PDF"); 
define("LANGBT288","View / Edit");
define("LANGSITU1","Married"); //
define("LANGSITU2","Divorced"); //
define("LANGSITU3","Widower"); //
define("LANGSITU4","Widow"); //
define("LANGSITU5","Life partner"); //
define("LANGSITU6","PACS"); //
define("LANGSITU7","Single");
define("LANGFIN002","Schedule");//
define("LANGFIN003","Schedule");//
define("LANGFIN004","No date has been set up");//
define("LANGCONFIG","Set up");//

define("LANGMESS112","Transcript comment");
define("LANGMESS113","Choice of comment");
define("LANGMESS114","Commentaire brevet des collèges");
define("LANGMESS115","Visualisation du bulletin de classe");
define("LANGMESS116","Enter");
define("LANGMESS117","Série");
define("LANGMESS118","Passer en mode étendu");
define("LANGMESS119","Assessments, recommendations");
define("LANGMESS120","Improvements");
define("LANGMESS121","Gaps  from expected goals");
define("LANGMESS122","Recommendations to improve");
define("LANGMESS123","Class average");
define("LANGMESS124","Previous comment");
define("LANGMESS125","Add"); // vérif. pas de quote (') 
define("LANGMESS126","Save comment"); // vérif. pas de quote (') 
define("LANGMESS127","back and click on"); // vérif. pas de quote (') 
define("LANGMESS128","Save");  // vérif. pas de quote (') 
define("LANGMESS129","View");
define("LANGMESS130","Moy. Précédente");
define("LANGMESS131","Save comments");
define("LANGMESS132","Please wait");
define("LANGMESS133","Empty comment");
define("LANGMESS134","Unsaved comment");
define("LANGMESS135","Appréciation pour le bulletin trimestriel classe");
define("LANGMESS136","Click here");
define("LANGMESS137","Additionnal information");
define("LANGMESS138","Enter other comments on transcripts");
//-----------------Traduction Sam le 06/06/2014
//-----------------messagerie_brouillon.php
define("LANGMESS139","Drafts folder");
define("LANGMESS140","Write a draft");
define("LANGMESS141","Enter");
define("LANGMESS142","Approve a draft");
define("LANGMESS143","Draft messages are visible to all school staff");

//------------------param.php
define("LANGMESS144","Head of studies signature");
define("LANGMESS145","School year");
define("LANGMESS156","Country");
define("LANGMESS159","Choose school");
define("LANGMESS160","New school");
define("LANGMESS177","County ");
//------------------definir_trimestre.php
define("LANGMESS146","Save as quarter format.");
define("LANGMESS147","All Classes");
define("LANGMESS148","List of quarter or semester period ");
define("LANGMESS149","Change");
define("LANGMESS150","Delete");
define("LANGMESS157","Quarter");
define("LANGMESS158","Class");
//-----------------probleme_acces_2.php
define("LANGMESS151","log on to your account");
define("LANGMESS152","Please enter you login (E-mail address) to create new password.");
define("LANGMESS153","Create new password");
//-----------------geston_groupe.php
define("LANGMESS154","Create a group");
define("LANGMESS155","List of academic staff");
//-----------------gestcompte.php
define("LANGMESS161","My account");
//-----------------messagerie_reception.php
define("LANGMESS162","Gestion de votre compte");
//------------------gestion_groupe.php
define("LANGBT53","Enter"); // traduit par sam le 09/06/2014
define("LANGMESS163","Check group");
//-------------------messagerie_suppression.php
define("LANGMESS164","Delete Items");
define("LANGMESS165","Archive in");
//-------------------messagerie_reception.php
define("LANGMESS166","Inbox");
//-------------------parametrage.php
define("LANGMESS167","Settings");
define("LANGMESS168","News");
define("LANGMESS169","Book a room / facilities");
define("LANGMESS170","Messages");
define("LANGMESS171","(Enter your e-mail adress)");
define("LANGMESS172","(Enter your cellphone number)");
//-------------------messagerie_envoi.php
define("LANGMESS173","Send a message to a student group : ");
define("LANGMESS174","Send a message to a class representative : ");
define("LANGMESS175","Message to staff member : "); // le 03/09/2014
define("LANGMESS176","Message to internship mentor : ");
//-------------------creat_admin.php
define("LANGMESS178","Gender :  ");
// define("LANGMESS179","Indice&nbsp;salaire");
define("LANGMESS179","wage index");
//-------------------creat_tuteur.php
define("LANGMESS180","Create an account placement tutor");
define("LANGMESS181","List / Modify");
define("LANGMESS182","Membership Management Tutor Internship");
define("LANGMESS183","Related company");
define("LANGMESS184","Fonction ");
//--------------------creat_personnel.php
define("LANGMESS185","Management of the Personnel");
define("LANGMESS186","Creating a Personal Account");
//--------------------creat_eleve.php
define("LANGMESS187","Search");
define("LANGMESS188","Import");
define("LANGMESS189","Delete");
define("LANGMESS190","Foreign language I : ");
define("LANGMESS191","Foreign language II : ");
define("LANGMESS192","Scholarship");
define("LANGMESS193","Student association registration");
define("LANGMESS194","Library registration");
define("LANGMESS195","Scholarship amount");
define("LANGMESS196","Stipend");
define("LANGMESS197","Accountancy code");
define("LANGMESS198","Adress");
define("LANGMESS199","Phone");
define("LANGMESS200","Cell Phone");
define("LANGMESS201","Student personal E-mail");
define("LANGMESS202","Student School E-mail");
define("LANGMESS203","Family Situation");
define("LANGMESS204","Copy the adress");
define("LANGMESS205","Previous Class");
//--------------------creat_class.php
define("LANGMESS206","Name of the Class");
define("LANGMESS207","School");
//--------------------creat_matiere.php
define("LANGMESS208","Short format");
define("LANGMESS209","Long format");
define("LANGMESS210","Course code");
//--------------------reglement.php
define("LANGMESS211","Internal school rules and regulation");
define("LANGMESS212","Add a rule");
define("LANGMESS213","View rules and regulations");
define("LANGMESS214","Delete a rule");
//--------------------sms.php
define("LANGMESS215","Management SMS");
define("LANGMESS216","Member");
define("LANGMESS217","Management Board");
define("LANGMESS218","Professor");
define("LANGMESS219","School life");
define("LANGMESS220","Personnal");
//--------------------Codebar0.php
define("LANGMESS221","Bar code :");
//--------------------vatel_gestion_ue.php
define("LANGMESS222","Course units");
define("LANGMESS223","Create");
define("LANGMESS224","Change/Delete");
//--------------------base_de_donne_importation.php
define("LANGMESS225","Excel file");
define("LANGMESS226","XML file");
define("LANGMESS227","Bar cod");
//--------------------edt.php
define("LANGMESS228","Delete a time period ");
define("LANGMESS229","Adjustment of schedules ");
define("LANGMESS230","Schedule ");
define("LANGMESS231","Download picture or pdf file : ");
define("LANGMESS232","(Jpeg-less than 2 Mo)");
define("LANGMESS233","Class schedule : ");
//--------------------export.php
define("LANGMESS234","Export data");
define("LANGMESS235","Upload");
define("LANGMESS236","Staff");
define("LANGMESS237","Choice of extraction : ");
//--------------------export.php
define("LANGMESS238","Teacher's name");
define("LANGMESS239","Upload as pdf file : ");
define("LANGMESS240","Upload");
//--------------------commaudio.php
define("LANGMESS241","Subject : ");
define("LANGMESS242","Audio file : ");
//--------------------consult_classe.php
define("LANGMESS243","Print ");
define("LANGMESS365","Half-board");
define("LANGMESS366","Resident");
define("LANGMESS367","Non-resident");
define("LANGMESS368","unknown");

//--------------------resr_admin.php
define("LANGMESS244","Book via schedule.");
//--------------------carnetnote.php
//------------modif nom de l'enseignant---LANGMESS238
//--------------------publipostage.php
define("LANGMESS245","Member type : ");
define("LANGMESS246","Parents");
define("LANGMESS247","Students");
define("LANGMESS248","Adress type :");
define("LANGMESS249","Tutor");
define("LANGMESS327","Mailing");

define("LANGMESS328","Student Gender : ");
define("LANGMESS329","Registration number : ");
define("LANGMESS330","Class : ");
define("LANGMESS331","Adress : ");
//--------------------ficheeleve3.php
define("LANGMESS250","Class");
define("LANGMESS251","Send an SMS");
define("LANGMESS252","Edit Profil");
define("LANGMESS253","Assign an internship");
define("LANGMESS254","Block the account");
define("LANGMESS255","Deblock the account");
define("LANGMESS259","Information");
define("LANGMESS260","Transcipt");
define("LANGMESS261","School life");
define("LANGMESS262","Disciplines");
define("LANGMESS263","Actions");
define("LANGMESS264","Info. Tuteur 1");
define("LANGMESS265","Info. Tuteur 2");
define("LANGMESS266","Info. Etudiant");
define("LANGMESS267","Archives");
define("LANGMESS268","Info. médicales");
define("LANGMESS269","info. compl.");
define("LANGMESS270","Name :");
define("LANGMESS271","First name :");
define("LANGMESS272","Class :");
define("LANGMESS273","Date of birth :");
define("LANGMESS274","Nationality :");
define("LANGMESS275","Place of birth :");
define("LANGMESS276","Boursier :");
define("LANGMESS277","Student number :");
define("LANGMESS278","Lv1/Spé :");
define("LANGMESS279","Lv2/Spé :");
define("LANGMESS280","Option :");
define("LANGMESS281","Régime :");
define("LANGMESS282","N°&nbsp;Rangement&nbsp; :");
define("LANGMESS283","Contact&nbsp;:");
define("LANGMESS284","Situation&nbsp;familiale&nbsp;:");
define("LANGMESS285","Adress :");
define("LANGMESS287","Zip code :");
define("LANGMESS288","City :");
define("LANGMESS289","Email&nbsp;:");
define("LANGMESS290","Phone :");
define("LANGMESS291","Profession&nbsp;:");
define("LANGMESS292","Professional phone :");
define("LANGMESS293","Gender :");
define("LANGMESS294","Previous class:");
define("LANGMESS295","Année&nbsp;Scolaire");
define("LANGMESS296","Trim&nbsp;/&nbsp;Sem");
define("LANGMESS297","Trancript");
define("LANGMESS298","Effectué&nbsp;le");
define("LANGMESS308","Permission non accordées");
define("LANGMESS309","Ajouter une information");
define("LANGMESS310","individual interview");
define("LANGMESS311","Plan abs/rtd");
define("LANGMESS312","Modify abs/rtd");
define("LANGMESS313","Delete abs/rtd");
define("LANGMESS320","Student Mail / Professionnal mail");
define("LANGMESS321","Student Phone / Cell phone");


//--------------------elevesansclasse.php
define("LANGMESS256","Save");
//--------------------consult_classe.php
define("LANGMESS257","All classes.");
//--------------------ficheeleve.php
define("LANGMESS258","Search");
//--------------------newsactualite.php
define("LANGMESS299","    Title : ");
define("LANGMESS300","Votre TRIADE n'est pas configuré en accès Internet, veuillez consulter votre compte administrateur Triade pour valider l'option de la connexion Internet.");
define("LANGMESS390","News first page");
//--------------------actualiteetablissement.php
//--------------------newsdefil.php
//--------------------commaudio.php // Bouton Parcourir
//--------------------commvideo.php
define("LANGMESS301","Link of the video : ");
define("LANGMESS302","Youtube link : ");
//--------------------emmargement.php
define("LANGMESS303","Attendance sheet management/roll call book");
define("LANGMESS304","In a class");
define("LANGMESS305","Attendance");
define("LANGMESS306","Exam Attendance");
define("LANGMESS307","In a group");
define("LANGMESS314","Today's attendance");
define("LANGMESS315","Attendance from : ");
define("LANGMESS316","Class : ");
define("LANGMESS317","Teacher : ");
define("LANGMESS318","All the teacher : ");
define("LANGMESS319","Cell height of students");
//--------------------trombinoscope0.php
define("LANGMESS322","Print as pdf file students ID picture");
define("LANGMESS323","Download Picture as zip file");
//--------------------chgmentclas.php
define("LANGMESS324",": grading, absences, penalties, comments ,transcript ,internships");
//------LANGASS10-- Variable pour suppression
//--------------------certificat.php
define("LANGMESS325","Settings : ");
define("LANGMESS326","Downloading settings : ");
//--------------------visa_direction.php
define("LANGMESS332","Type of transcript : ");
// VALIDER CHANGER PAR ENTER-->LANGMESS116
define("LANGMESS333","Enter");
define("LANGMESS334","Year");
//--------------------list_classe.php----- Voir comment changer le bouton Modifier
//--------------------list_matiere.php---- Voir comment changer le bouton Modifier
//--------------------listepreinscription.php
define("LANGMESS335","Liste des pré-inscriptions");
//--------------------reglement_ajout.php
define("LANGMESS336","School regulations");
define("LANGMESS337","Rules and regulations");
define("LANGMESS338","Classes");
define("LANGMESS339","Classes");
//--------------------affectation_visu.php
define("LANGMESS340","Year / semester / quarter");
define("LANGMESS341","All year");
define("LANGMESS342","Quarter 1 / Semester 1");
define("LANGMESS343","Quarter 2 / Semester 2");
define("LANGMESS344","Quarter 3");
//--------------------affectation_modif_key.php
//----Modidifier le bouton suivant par next
//--------------------reglement_ajout.php
//--------------------reglement_liste.php
//----------------/reglement_supp.php
define("LANGMESS345","View");
//-----------------vatel_list_ue.php
define("LANGMESS346","Course units settings");
define("LANGMESS347","Filter : ");
define("LANGMESS348","Change");
define("LANGMESS349","Delete");
define("LANGMESS350","Course unit");
define("LANGMESS351","Sem.");
define("LANGMESS352","Create course unit");
//----------------creat_groupe.php
define("LANGMESS353","Excel File");
define("LANGMESS354","Excel file content");
//----------------visa_direction2.php
define("LANGMESS355","Comments");
define("LANGMESS356","School management comments");
//----------------imprimer_tableaupp.php
define("LANGMESS357","Print grading report");
define("LANGMESS358","Show Ranking ");
define("LANGMESS359","Show empty columns");
define("LANGMESS360","Merge course units");
define("LANGMESS361","Show topics ");
define("LANGMESS362","Average in Excel file");
define("LANGMESS374","Until :");
define("LANGMESS375","Excel file");
//------------------affectation_creation_key.php
//------------------affectation_visu2.php
define("LANGMESS363","View");
define("LANGMESS364","Course Unit");
//------------------entretien.php
define("LANGMESS369","Personal interview");
define("LANGMESS370","Group interview");
define("LANGMESS371","Summary table");
define("LANGMESS372","Professors");
define("LANGMESS373","Number hours");

//------------------base_de_donne_key.php
define("LANGMESS376","To change your acces code, please contact the Triade administrator");
define("LANGMESS377"," ");
define("LANGMESS378"," ");
//------------------chgmentClas0.php
// année = Year
define("LANGMESS379","No Year");
define("LANGMESS380","Choose a class");
//------------------chgmentClas00.php
// année et pas d'année 
define("LANGMESS381","Choose classes");
define("LANGMESS383","Change the class for students in ");
define("LANGMESS384","Move forward a class to year");
define("LANGMESS385","No class");
// define("LANGBASE38","Save Change"); suppression du (s)
//------------------brouillon_reception.php
define("LANGMESS382","Draft");
//------------------imprimer_trimestre.php
define("LANGMESS386","Specific transcript");
define("LANGMESS387","Transcript for the professors");
define("LANGMESS388","Class preview");
define("LANGMESS389","Allow access to transcript for professors");
// le 14/09/2014
//----------------------messagerie_reception.php
define("LANGMESS391","Classic mode");
//----------------------messagerie_envoi_suite2.php
define("LANGMESS392","Recipients");
define("LANGMESS393","Delete list");
// traduire Sélectionnez un fichier
define("LANGMESS394","Sélectionnez un fichier");
//---------------------list_admin.php
define("LANGMESS395","School management");
define("LANGMESS396","View / Modify");
//---------------------list_scolaire.php
// Visualiser / Modifier
define("LANGMESS397","School life");
//---------------------modif_admin.php
define("LANGMESS398","Block the account");
define("LANGMESS399","Deblock the account");
//---------------------modif_scolaire.php
//define("LANGMESS398","Désactiver compte");
//define("LANGMESS399","Activer compte");
//---------------------list_enseignant.php
//---------------------list_personnel.php
define("LANGMESS400","Permission");
define("LANGMESS401","Staff members");
//---------------------list_tuteur.php
//define("LANGMESS402","Liste Tuteur de stage ");
define("LANGMESS403","Placement tutor");
//---------------------modif_personnel.php
define("LANGMESS404","List / Modify");
//-------------Genre
define("LANGMESS405","Mr");
define("LANGMESS406","Mrs");
//--------------------list_classe.php
//--------------------modif_classe.php
define("LANGMESS407","Modify class");
define("LANGMESS408","Activer la classe");
define("LANGMESS409","Désactiver la classe");
define("LANGMESS410","Complete definition");
define("LANGMESS411","Site rattaché");
//--------------------affectation_creation.php
//-------------------publipostage.php
define("LANGMESS412","Tag / label format");
define("LANGMESS413","Number Type");
//-------------------list_matiere.php
//-------------------modif_matiere.php
define("LANGMESS414","Type de membre");
define("LANGMESS415","Code matière");
define("LANGMESS416","Nom de la sous-matière");
define("LANGMESS417","Supprimer sous matière");
define("LANGMESS418","Désactiver matière");
define("LANGMESS419","Activer matière");
//-------------------triadev1/circulaire_liste.php
define("LANGMESS420","Reference");
//-------------------visu_retard_parent.php
//-------------------messagerie_envoi.php
define("LANGMESS421","Vous n'avez pas l'autorisation d'envoyer un message à cette personne.");
//-------------------information.php
define("LANGMESS422","Educational Institutes");
//-------------------parametrage.php
define("LANGMESS423","Module lors de votre connexion ");
define("LANGMESS424","News");
define("LANGMESS425","Of absenteeism Module");
//-------------------retardprof.php
define("LANGMESS426","Indiquez des élèves en retard ou absent");
//-------------------retardprof2.php
define("LANGMESS427","Indiquer heure d'abs/rtd");
define("LANGMESS428","In ");
define("LANGMESS429","Schedule : ");
//-------------------consult_classe_prof.php


define("LANGTMESS450","Translate into another language");
define("LANGTMESS451","Actuellement le fichier import sert de référence à la création du certificat.");
define("LANGTMESS452","Recover");
define("LANGTMESS453","Certificat numéro :");
define("LANGTMESS454","Add an application");
define("LANGTMESS455","New");
define("LANGTOUS","All");
define("LANGTMESS456","En attente");
define("LANGTMESS457","Accepted");
define("LANGTMESS458","Denied");
define("LANGTMESS459","Decision ");
define("LANGTMESS460","Transfer class list");
define("LANGTMESS461","Delete files");
define("LANGTMESS462","Attention!, The regulation must be in pdf format and not exceed 2MB");
define("LANGTMESS463","This option allows teachers to settle the regulation at the time of the first connection. ");
define("LANGTMESS464","Students total number ");
define("LANGTMESS465","Comment for");
define("LANGTMESS466","Show sub-topics");
define("LANGTMESS467","Consideration of exams grades");
define("LANGTMESS468","Consideration of coefficent 0");
define("LANGTMESS469","If the coefficient is zero, grades above 10 will be taken into account");
define("LANGTMESS470","Spécif");
define("LANGTMESS471","Case studies");
define("LANGTMESS472","View transcript");
define("LANGTMESS473","for year ");
define("LANGTMESS474","Change");
define("LANGTMESS475","File - maximum size");
define("LANGTMESS476","Edit personal account");
define("LANGTMESS477","Edit internship mentor");
define("LANGCIV0","Mr.");
define("LANGCIV1","Mrs.");
define("LANGCIV2","Ms.");
define("LANGCIV3","Ms");
define("LANGCIV4","Mr");
define("LANGCIV5","Mrs");
define("LANGCIV6","Mr. or Mrs.");
define("LANGCIV7","Sr"); 
define("LANGCIV8","Général");
define("LANGCIV9","Colonel");
define("LANGCIV10","Lieutenant-Colonel");
define("LANGCIV11","Commandant");
define("LANGCIV12","Capitaine");
define("LANGCIV13","Lieutenant");
define("LANGCIV14","Sous-Lieutenant");
define("LANGCIV15","Aspirant");
define("LANGCIV16","Major");
define("LANGCIV17","Adjudant-Chef");
define("LANGCIV18","Adjudant");
define("LANGCIV19","Sergent-Chef");
define("LANGCIV20","Sergent");
define("LANGCIV21","Caporal-Chef");
define("LANGCIV22","Caporal"); 
define("LANGCIV23","Aviateur");
define("LANGCIV24","Dr."); 

define("LANGMESST390","Thank you for filling in the information needed for the Triade number 1 website !! <br> Please confirm validating or revalidating the form below. ");
define("LANGMESST391","Delete website");
define("LANGMESST392","Carnet de suivi");
define("LANGMESST393","Your account is locked out");
define("LANGMESST394","Your account is on probationary period");
define("LANGMESST395","Delete probationary period");
define("LANGMESST396","Probationary period");
define("LANGMESST397","by");
define("LANGMESST398","Save the list");
define("LANGMESST399","Complex search");
define("LANGMESST700","Delete current message");
define("LANGMESST701","News first page");
define("LANGMESST702","Title of the video clip");
define("LANGMESST703","Copy / paste");
define("LANGMESST704","Enter recipient");
define("LANGMESST705","The message has not been sent: You do not have permission to send a message to this person. TRIADE team");
define("LANGTMESS400","Your request has been taken into account");
define("LANGTMESS401","Please check your e-mail address");
define("LANGTMESS402","No account matches with the email address");
define("LANGTMESS403","Please click here to contact TRIADE administrator");
define("LANGTMESS404","on the link");
define("LANGTMESS405","Please contact TRIADE administrator");
define("LANGTMESS406","Check");
define("LANGTMESS407","Check Groups");
define("LANGTMESS408","non-valid email adress");
define("LANGTMESS409","Please enter valid email address");
define("LANGTMESS410","hotmail email addresses are not accepted");
define("LANGTMESS411","Please enter another email address");
define("LANGTMESS412","New directory");
define("LANGTMESS413","Message already printed");
define("LANGTMESS414","Attached file");
define("LANGTMESS415","Save (as an archive) in");
define("LANGTMESS416","Boite de "); 
define("LANGTMESS417","Inbox");
define("LANGTMESS418","Classic mode");
define("LANGTMESS419","Sent items");
define("LANGTMESS420","My directory");
define("LANGTMESS421","Via email");
define("LANGTMESS422","Via SMS");
define("LANGTMESS423","Via RSS");
define("LANGTMESS424","Module when logging");
define("LANGTMESS425","Truancy ");
define("LANGTMESS426","List of course units (Edit / Delete)");
define("LANGTMESS427","Schedule saved as pdf file");
define("LANGTMESS428","The TRIADE team");
define("LANGTMESS429","Schedule saved");
define("LANGTMESS430","Schedule deleted");
define("LANGTMESS431","Name already used");
define("LANGTMESS432","Export format");
define("LANGTMESS433","&nbsp;Total&nbsp;");
define("LANGTMESS434","Columns");
define("LANGTMESS435","Internship mentor");
define("LANGTMESS436","View address");
define("LANGTMESS437","All parents");
define("LANGTMESS438","Every");
define("LANGTMESS439","View / Change");
define("LANGTMESS440","Add");
define("LANGTMESS441","Information");
define("LANGTMESS442","per month");
define("LANGTMESS443","Months number");
define("LANGTMESS444","Accountings code");
define("LANGTMESS445","University");
define("LANGTMESS446","Edit bank information");
define("LANGTMESS447","Data already saved");
define("LANGTMESS448","Site rattaché");
define("LANGTMESS449","Definition completed");


define("LANGTRONBI9","the students");
define("LANGTRONBI10","of personnel");
define("LANGTRONBI11","Picture ID of personnel");

define("LANGTMESS480","Visa management");
define("LANGTMESS481","Comments for students");

define("LANGTMESS482","NEWS - TRIADE");
define("LANGTMESS483","not available");
define("LANGTMESS484","Your directorues");
define("LANGTMESS485","Send a message to a class representative");
define("LANGTMESS486","Tag / label format");

define("LANGMESS430","L'année complète");
define("LANGMESS431","Including mid-term grades ");
define("LANGMESS432","Transcript");
define("LANGMESS433","Save as barcode");
define("LANGMESS434","Endorse attendance");
define("LANGMESS435","Mail");
define("LANGMESS436","Transcript without absences");
define("LANGMESS437","List of absences");
define("LANGMESS438","Absences per week");
define("LANGMESS439","Print absences and delays");
define("LANGMESS440","Attendance sheet");
define("LANGMESS441","Absences and delays management via sconet");
define("LANGMESS442","Statistics");
define("LANGMESS443","Absences and delays for 1 student");
define("LANGMESS444","Manage");
define("LANGMESS445","View / Change");
define("LANGMESS446","Delete");
define("LANGMESS447","Enter");
define("LANGMESS448","Convert abs ");
define("LANGMESS449","Settings");
define("LANGMESS450","Select an alert");
define("LANGMESS451","Configuration créneau horaire ");
define("LANGMESS452","SMS settings");
define("LANGMESS453","SMS credit");

define("LANGTMESS487","Avec notes vie scolaire");
define("LANGTMESS488","Rattrapage non validés");

define("LANGTRONBI30","View staff ID picture");
define("LANGTRONBI20","Modify staff ID picture");

define("LANGSEXEF","W");
define("LANGSEXEH","M");
define("LANGHOM","Man");
define("LANGFEM","Woman");

define("LANGTMESS489","Copy time table");
define("LANGTMESS490","Copy time table from one class to another");
define("LANGTMESS491","time period to copy");
define("LANGTMESS492","Upload management list");
define("LANGTMESS493","Upload staff accounts");
define("LANGTMESS494","Upload companies");
define("LANGTMESS495","Upload IPAC : ");
define("LANGTMESS496","Upload subjects ");
define("LANGTMESS497","Module d'importation de fichier : ");
define("LANGTMESS498","Module d'importation de fichier Excel ");
define("LANGTMESS499","Excel file should include 4 fields");
define("LANGTMESS500","Exemple fichier xls");
define("LANGTMESS501","Number of added subjects : ");
define("LANGTMESS502","Dates Trimestrielles");
define("LANGTMESS503","Access currently disabled ");
define("LANGTMESS504","Send password by email ");
define("TITREACC1","parents");
define("TITREACC2","Teachers");
define("TITREACC3","School life");
define("TITREACC4","Internship mentor");
define("TITREACC5","Staff");
define("LANGTMESS505","Previous classes");
define("LANGTMESS506","Specialization");
define("LANGTMESS507","Edit diploma supplement");
define("LANGTMESS508","Settings");
define("LANGTMESS509","Diploma supplement");
define("LANGTMESS510","Select document :");
define("LANGTMESS511","Recover zip files");
define("LANGTMESS512","Level");
define("LANGTMESS513","Mail companies ");
define("LANGTMESS514","Upload companies");
define("LANGTMESS515","Allowance");
define("LANGTMESS516","Followup");
define("LANGTMESS517","Settings diploma supplement");
define("LANGTMESS518","Wording :");
define("LANGTMESS519","File");
define("LANGTMESS520","Name of the internship");
define("LANGTMESS521","Working days : ");
define("LANGTMESS522","Country");
define("LANGTMESS523","Hotel group");
define("LANGTMESS524","Stars");
define("LANGTMESS525","Number of rooms");
define("LANGTMESS526","Website");
define("LANGTMESS527","assign several students to an internship");
define("LANGSTAGE100","Name");
define("LANGSTAGE101","Internship number");
define("LANGSTAGE102","Company");
define("LANGSTAGE103","Department");
define("LANGSTAGE104","Allowance ");
define("LANGSTAGE105","Accomodation - Yes");
define("LANGSTAGE106","Meals  Yes");
define("LANGSTAGE107","Assign");
define("LANGSTAGE108","Internship dates");
define("LANGSTAGE109","Country");
define("LANGSTAGE110","Mentor");
define("LANGSTAGE111","Language");
define("LANGSTAGE112","Department");
define("LANGSTAGE113","Allowance");
define("LANGSTAGE114","Daily schedule");
define("LANGSTAGE115","Internship agreement");
define("LANGSTAGE116","Print all internship agreement");

define("LANGTMESS528","Language of the class");
define("LANGTMESS529","Back to class");
define("LANGTMESS530","Retrieve internship agreement");

define("LANGVATEL1","Log Out");
define("LANGVATEL2","My connect");
define("LANGVATEL3","Lost password");
define("LANGVATEL4","Write your email");
define("LANGVATEL5","Write your password");
define("LANGVATEL6","Semester");
define("LANGVATEL7","Abs/Delay/Sanction");
define("LANGVATEL8","Absences / Delays / Sanctions");
define("LANGVATEL9","Absences");
define("LANGVATEL10","Delays");
define("LANGVATEL11","Sanctions");
define("LANGVATEL12","Description des faits");
define("LANGVATEL13","<");
define("LANGVATEL14",">");
define("LANGVATEL15","Monthy");
define("LANGVATEL16","Reinitialise your password");
define("LANGVATEL17","Mot de passe oublié ?");

define("LANGVATEL18","Student access");
define("LANGVATEL19","Teacher access");
define("LANGVATEL20","Personal access");

define("LANGVATEL21","Add");
define("LANGVATEL22","Modify");
define("LANGVATEL23","Remove");
define("LANGVATEL24","View");
define("LANGVATEL25","News ?");
define("LANGVATEL26","Grades");
define("LANGVATEL27","Statistics of this duty");
define("LANGVATEL28","IMPOSSIBLE");
define("LANGVATEL29","Already half past.");
define("LANGVATEL30","Add student");
define("LANGVATEL31","Add a note to a student for this duty.");
define("LANGVATEL32","Back on the list of duties");
define("LANGVATEL33","Timetable");
define("LANGVATEL34","Attendance");
define("LANGVATEL35","missing sign");
define("LANGVATEL36","Calendar");
define("LANGVATEL37","Error Save");
define("LANGVATEL38","Indicate the date");



define("LANGVATEL39","Home page");
define("LANGVATEL40","Choose a teacher");
define("LANGVATEL41","for teacher");
define("LANGVATEL42","Assign a teacher");
define("LANGVATEL43","Non-attendance or delays in class");
define("LANGVATEL44","Other absences");
define("LANGVATEL45","Others absences in the same class");
define("LANGVATEL46","Reported on");
define("LANGVATEL47","Attendance / Delays management");
define("LANGVATEL48","Email alert");
define("LANGVATEL49","Tables update");
define("LANGVATEL50","Unable to delete a class");
define("LANGVATEL51","You are not allowed to delete this class");
define("LANGVATEL52","Class assigned");
define("LANGVATEL53","Delete this class");
define("LANGVATEL54","Delete this subject");
define("LANGVATEL55","Unable to delete this subject");
define("LANGVATEL56","Subject assigned to a class");
define("LANGVATEL57","If no first name, please type \"unknown\" ");
define("LANGVATEL58","Create a adminstration account");
define("LANGVATEL59","List of Internships mentors");
define("LANGVATEL60","List of teachers");
define("LANGVATEL61","List of administration staff");
define("LANGVATEL62","List of school life staff");
define("LANGVATEL63","School regulations");
define("LANGVATEL64","Classes");
define("LANGVATEL65","Administration staff");
define("LANGVATEL66","Intersnhip mentor");
define("LANGVATEL67","Unregistered school regulations ");
define("LANGVATEL68","File size should be in a pdf fomat and less than 2 MB");
define("LANGVATEL69","Menu");
define("LANGVATEL70","Access to pdf class file");
define("LANGVATEL71","Accès au PDF du régime ");
define("LANGVATEL72","Print to pdf fomat");
define("LANGVATEL73","Edit picture");
define("LANGVATEL74","Groups");
define("LANGVATEL75","Create a group");
define("LANGVATEL76","View list");
define("LANGVATEL77","No student");
define("LANGVATEL78","Change group");
define("LANGVATEL79","Edit groups");
define("LANGVATEL80","Group NOT removed");
define("LANGVATEL81","Group removed");
define("LANGVATEL82","Group is currently assigned.\\n\\n unable to delete.\\n\\n assign before removing");
define("LANGVATEL83","Group already created");
define("LANGVATEL84","Transcript settings");
define("LANGVATEL85","School settings");
define("LANGVATEL86","Set up assignments");
define("LANGVATEL87","Edit assignments");
define("LANGVATEL88","Delete assignments");
define("LANGVATEL89","Educational unit");
define("LANGVATEL90","Set up attendance ");
define("LANGVATEL91","Set up school certificate");
define("LANGVATEL92","Set up diploma supplement");
define("LANGVATEL93","Please enter day and month when the school year starts");
define("LANGVATEL94","Please enter day and month when the school year ends");
define("LANGVATEL95","Day or month is not correct");
define("LANGVATEL96","Please enter school year");
define("LANGVATEL97","IMPORTANT ! CREATE ASSIGMENTS SHALL DELETE ALL GRADES INFORMATIONS CONCERNING THE NEW DESIGNATED CLASS");
define("LANGVATEL98","Copy Assignments");
define("LANGVATEL99","COPY ERROR");
define("LANGVATEL100","of school year");
define("LANGVATEL101","IMPORTANT ! CREATE ASSIGMENTS SHALL DELETE ALL GRADES INFORMATIONS CONCERNING THE NEW DESIGNATED CLASS");
define("LANGVATEL102","Copy class assignment");
define("LANGVATEL103","Case study");
define("LANGVATEL104","Delete all class grades and marks");
define("LANGVATEL105","* Visu. : Visualiser au sein du bulletin / ** Nombre d'heure annuelle");
define("LANGVATEL106","Enter teacher");
define("LANGVATEL107","Enter subject weight");
define("LANGVATEL108","Enter number");
define("LANGVATEL109","Drag and drop a line");
define("LANGVATEL110","Click and move");
define("LANGVATEL111","sur le N° correspondant ");
define("LANGVATEL112","Copy unit");
define("LANGVATEL113","List of units");
define("LANGVATEL114","CAUTION ! UPDATE CLASS ASSIGMNENT");
define("LANGVATEL115"," ON EDUCATIONAL UNIT DATA");
define("LANGVATEL116"," within transcript from 0 to ");
define("LANGVATEL117","Confirm ");
define("LANGVATEL118","Are you sure that you want to delete the next educational unit ?");
define("LANGVATEL119","Deleted");
define("LANGVATEL120","Set up hour slot");
define("LANGVATEL121","Config. des motifs");
define("LANGVATEL122","Name hour slot");
define("LANGVATEL123","Start hour");
define("LANGVATEL124","End hour");
define("LANGVATEL125","Intitulé du créneau");
define("LANGVATEL126","Save hour slots");
define("LANGVATEL127","Créneaux par défaut");
define("LANGVATEL128","Certificate number");
define("LANGVATEL129","Set up certificates");
define("LANGVATEL130","Donwload certificate");
define("LANGVATEL131","Registration error");
define("LANGVATEL132","In progress");
define("LANGVATEL133","Set up keywords");
define("LANGVATEL134","Registration error");
define("LANGVATEL135","Error : unknown file");
define("LANGVATEL136","Error : file should be less than 8 MB");
define("LANGVATEL137","File NOT saved");
define("LANGVATEL138","Edit list");
define("LANGVATEL139","Edit class");
define("LANGVATEL140","Students list");
define("LANGVATEL150","Classes dashboard");
define("LANGVATEL151","Total number of students. School year");
define("LANGVATEL152","Total number of students");
define("LANGVATEL153","Attendance sheet");
define("LANGVATEL154","No defined courses in schedule");
define("LANGVATEL155","Start hour");
define("LANGVATEL156","End hour");
define("LANGVATEL157","Subject");
define("LANGVATEL158","Teachers list");
define("LANGVATEL159","Subjects list");
define("LANGVATEL160","Edit school certificates");
define("LANGVATEL161","Documents related to school certificates");
define("LANGVATEL162","Recover certificates in ZIP format");
define("LANGVATEL163","List of interviews");
define("LANGVATEL164","Edit Students tags");
define("LANGVATEL165","Edits parents tags");
define("LANGVATEL166","Recover mailings documents");
define("LANGVATEL167","Download / Upload");
define("LANGVATEL168","Download students");
define("LANGVATEL169","Download teachers");
define("LANGVATEL170","Download administration staff");
define("LANGVATEL171","Download companies");
define("LANGVATEL172","Upload students");
define("LANGVATEL173","Upload teachers");
define("LANGVATEL174","Upload adminstration staff");
define("LANGVATEL175","Student¿s address");
define("LANGVATEL176","Student¿s city");
define("LANGVATEL177","Student¿s ZIP Code");
define("LANGVATEL178","Student¿s phone number");
define("LANGVATEL179","Scholarship holder");
define("LANGVATEL180","School email address");
define("LANGVATEL181","Male / Female");
define("LANGVATEL182","Mot de passe tuteur 2");
define("LANGVATEL183","Régime possible");
define("LANGVATEL184","Civilité possible");
define("LANGVATEL185","The file to be transmitted should contain 47 fields");
define("LANGVATEL186","Excel file ");
define("LANGVATEL187","Prendre la première ligne du fichier");
define("LANGVATEL188","Update");
define("LANGVATEL189","Prendre en compte les champs vides du fichier");
define("LANGVATEL190","Affecter un nouveau mot de passe pour les élèves déjà inscrits");
define("LANGVATEL191","Pas d'archivage possible");
define("LANGVATEL192","Attention la suppression des l'élèves, supprimera toutes les archives !!");
define("LANGVATEL193","Download next school year");
define("LANGVATEL194","ERROR : CLASS HAS NOT BEEN CREATED");
define("LANGVATEL195","The file to be transmitted should contain 9 fields");
define("LANGVATEL196","PASSWORD ERROR");
define("LANGVATEL197","Add columns");
define("LANGVATEL198","Number of columns to add");
define("LANGVATEL199","Enter data to upload");
define("LANGVATEL200","Save structure");
define("LANGVATEL201","If you want to save the structure uploading , first get your excel file, then click \" Save  structure \"");
define("LANGVATEL202","Structure¿s name");
define("LANGVATEL203","Recover uploading");
define("LANGVATEL204","Enter the order of columns in your Excel file");
define("LANGVATEL205","Transcript");
define("LANGVATEL206","Transcript / Dilpoma supplement");
define("LANGVATEL207","Comments from the Head");
define("LANGVATEL208","Comments related to the class");
define("LANGVATEL209","Edit marks and grades");
define("LANGVATEL210","Edit transcripts");
define("LANGVATEL211","Edit Bachelor / MBA supplement ");
define("LANGVATEL212","Saved comments");
define("LANGVATEL213","Check assigments related to this class");
define("LANGVATEL214","Edit internship dates");
define("LANGVATEL215","Edit companies data");
define("LANGVATEL216","Assign students to internships");
define("LANGVATEL217","List of students in companies");
define("LANGVATEL218","Edit internship agreements");
define("LANGVATEL219","Add time period");
define("LANGVATEL220","List of time periods");
define("LANGVATEL221","End date shall occur before the start date");
define("LANGVATEL222","Edit time period");
define("LANGVATEL223","Delete time period");
define("LANGVATEL224","Delete all non assigned dates");
define("LANGVATEL225","List");
define("LANGVATEL226","Edit internship");
define("LANGVATEL227","Print list of campanies");
define("LANGVATEL228","Number of students who have done an internship");
define("LANGVATEL229","Plan");
define("LANGVATEL230","Historique des élèves");
define("LANGVATEL231","Recover pdf file");
define("LANGVATEL232","Address / ZIP / City");
define("LANGVATEL233","List of companies from");
define("LANGVATEL234","Assign several students to an internship");
define("LANGVATEL235","Assign a student to an internship");
define("LANGVATEL236","Start");
define("LANGVATEL237","End");
define("LANGVATEL238","Period of time : Semester / Quarter");
define("LANGVATEL239","Requested time period");
define("LANGVATEL240","Please enter internship number ");
define("LANGVATEL241","Print full list");


define("LANGVATEL242","Edit assignments");  
define("LANGVATEL243","Other class");  
define("LANGVATEL244","Implement a schedule");  
define("LANGVATEL245","Please choose a type of account");  
define("LANGVATEL246","Update tables");  
define("LANGVATEL247","Manage absences and delays");  
define("LANGVATEL248","Add an absence or a delay");  
define("LANGVATEL249","Staff"); 
define("LANGVATEL250","School life");  
define("LANGVATEL251","Internship mentor");  
define("LANGVATEL252","School management staff");  
define("LANGVATEL253","Classes");  
define("LANGVATEL254","Set up");
define("LANGVATEL255","Implement schedule"); 

define("LANGVATEL256","Please enter the list of parents who will receive an email");
define("LANGVATEL257","no number"); 
define("LANGVATEL258","Please confirm sending"); 
define("LANGVATEL259","Student's cellphone number"); 
define("LANGVATEL260","Cellphone "); 
define("LANGVATEL261","Professional phone number (Mother)"); 
define("LANGVATEL262","Professionnal phone number (Father)"); 
define("LANGVATEL263","Projector"); 
define("LANGVATEL264","Absence / delays details "); 
define("LANGVATEL265","Time slot "); 
define("LANGVATEL266","undefined "); 
define("LANGVATEL267","Change teachers list "); 
define("LANGVATEL268","Delete an account "); 
define("LANGVATEL269","Absences and delays "); 
define("LANGVATEL270","Absences and delays"); 
define("LANGVATEL271","Impossible to proceed. Please define the school year."); 
define("LANGVATEL272","The new educational unit has been successfully created."); 
define("LANGVATEL273","Who's who"); 

define("LANGVATEL274","Send SMS regarding absences from ");
define("LANGVATEL275","NBETUDIANTS => Number of students<br />
HISTOETUDIANT => Student's path<br />
NOMETUDIANT => Student's name<br> 
PREETUDIANT => Student's first name<br>
DATENAISETUDIANT => Student's birth date<br>
IDENTETUDIANT => Student's ID number<br>
NOMETABLISSEMENT => School<br>
DATEDUJOUR => Date of the day<br>
LANGUEETUDIANT => Teaching language<br>
NBRETUDIANTPA1 => Number of students for Titre I<br>
NBRETUDIANTPA2 => Number of students for  Titre II<br>
NBRETUDIANTPREPA => Number of students in foundation program<br>
NBRETUDIANTM4 => Number of 4th year students fo Titre I<br>
SPECIALISATION => Specialization<br>
NOMDIRECTEUR => Name of school director<br>
NOMCLASSELONG => Name of the class in a long format <br>");

define("LANGVATEL276","Listes of past comments ");
define("LANGVATEL277","View comments for each subject ");
define("LANGVATEL278","Nombre effectué");
define("LANGVATEL279","Completed rate ");
define("LANGVATEL280","Flagged ");
define("LANGVATEL281","Send message ");
define("LANGVATEL282","Comments saved in ");
define("LANGVATEL283","Age"); 
define("LANGVATEL284","Years old ");
define("LANGVATEL285","List of absences for current time period ");
define("LANGVATEL286","Number&nbsp;of&nbsp;absences");
define("LANGVATEL287","List of delays for the current time period ");
define("LANGVATEL288","Graphs ");
define("LANGVATEL289","Comments ");
define("LANGVATEL290","Commentaire du professeur principal.");


define("LANGVATEL291","Teachers comments are available.");
define("LANGVATEL292","Archive transcript ");
define("LANGVATEL293","Access control ");
define("LANGVATEL294","Change class ");
define("LANGVATEL295","Please enter current school year ");
define("LANGVATEL296","Please enter next school years ");
define("LANGVATEL297","Please enter current school year ");
define("LANGVATEL298","Please enter new school year ");
define("LANGVATEL299","Born on ");
define("LANGVATEL300","Students information ");
define("LANGVATEL301","Absence alert");
define("LANGVATEL302","SMS alert");
define("LANGVATEL303","SMS alert");


define("LANGPUR4","WARNING : You are running a module that will delete selected data.\\n Do you want to continue ? \\n\\nTRIADE team");

define("LANGNEW100","Punishment(s)");
define("LANGNEW101","Forecast");


?>
