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


if (!defined(INTITULEDIRECTION)) { define("INTITULEDIRECTION","direction"); }
if (!defined(INTITULEELEVE)) { define("INTITULEELEVE","élève"); }
if (!defined(INTITULEELEVES)) { define("INTITULEELEVES","élèves"); }


// fichier pour langue cote admin.
// POUR TOUS -------------------
// brmozilla($_SESSION[navigateur]);
define("CLICKICI","Klikañ amañ");
define("VALIDER","Asantiñ");
define("LANGTP22"," DIWALLIT - Goulenn prouadoù - RANN");
define("LANGTP3"," deiziataer ar prouadoù ");
define("LANGCHOIX","Dibab ...");
define("LANGCHOIX2","klas ebet");
define("LANGCHOIX3","--- Dibab ---");
define("LANGOUI","ya");
define("LANGNON","nann");
define("LANGFERMERFEN","Serriñ ar prenestr");
define("LANGATT","DIWALLIT !");
define("LANGDONENR","Titour enrollet");
define("LANGPATIENT","Trugarez da c'hortoz");
define("LANGSTAGE1",'Merañ ar stajoù micherel');
define("LANGINCONNU",'dianavezet'); // doit etre identique que langinconnu cote javascript
define("LANGABS",'ezv');
define("LANGRTD",'dale');
define("LANGRIEN",'mann ebet');
define("LANGENR",'Enrollañ');
define("LANGRAS1",'Hiziv, d\'a_ ');
define("LANGDATEFORMAT",'dd/mm/bbbb');

//------------------------------
// titre
//-------------------------------
define("LANGTITRE1","Oberiantiz - Darvoud(où) ar miz");
define("LANGTITRE2","Oberiantiz - Keleier");
define("LANGTITRE3","Kemennadenn o tibunañ e krec'h ar bajenn");
define("LANGTITRE4","Kemennadenn o tibunañ er vandenn");
define("LANGTITRE5","Kemennadenn o vezañ resevet");
define("LANGTITRE6","Krouidigezh ur gont merour");
define("LANGTITRE7","Krouidigezh ur gont buhez skol");
define("LANGTITRE8","Krouidigezh ur gont kelenner");
define("LANGTITRE9","Krouidigezh ur gont elec'hier");
define("LANGTITRE10","Krouidigezh ur gont skoliad");
define("LANGTITRE11","Krouidigezh ur strollad"); //
define("LANGTITRE12","Krouidigezh ur c'hlasad"); //
define("LANGTITRE13","Krouidigezh un danvez"); //
define("LANGTITRE14","Krouidigezh un is-danvez"); //
define("LANGTITRE15","Envel ar benngelennerien");
define("LANGTITRE16","Krouiñ kefredioù");
define("LANGTITRE17","Krouiñ kefredioù evit ar c'hlasad");
define("LANGTITRE18","Sellet ouzh ar c'hefredioù");
define("LANGTITRE19","Kemm kefredioù");
define("LANGTITRE20","Kemm kefredioù evit ar c'hlasad");
define("LANGTITRE21","Diverkañ kefredioù");
define("LANGTITRE22","Ebarzhiañ ur fichenn ASCII (txt,csv) ");
define("LANGTITRE23","Listenn an daleoù diabeget ");
define("LANGTITRE24","Ouzhpennañ un diskarg");
define("LANGTITRE25","Embann / Kemm an diskargoù");
define("LANGTITRE26","Diverkañ un diskarg");
define("LANGTITRE27","Merañ diskargoù -  Steuñverezh");
define("LANGTITRE28","Skritellañ / Kemm diskargoù");
define("LANGTITRE29","Sellout ouzh ar c'hlasadoù");
define("LANGTITRE30","Klask ur skoliad");
define("LANGTITRE31","ebarzhiañ ar fichenn GEP");
define("LANGTITRE32","Poltredaoueg ar skolidi");
define("LANGTITRE33","Testeni skol");

//------------------------------
define("LANGTE1","Titl");
define("LANGTE2","Eus");
define("LANGTE3","D'");
define("LANGTE4","Niver a arouezioù");
define("LANGTE5","Pal");
define("LANGTE6","Da");
define("LANGTE6bis","Da gerent ar re ");
define("LANGTE7","Deiziad");
define("LANGTE8","Diverkañ ur gemennadenn kaset d'ur c'har");
define("LANGTE9","lennet");
define("LANGTE10","betek :");
define("LANGTE11","e");
define("LANGTE12","d'a_ ");
define("LANGTE13","da");
define("LANGTE14","D'ar strollad ");

//------------------------------
define("LANGFETE","Gouelioù an deiz ");
define("LANGFEN1","Darvoud an deiz");
define("LANGFEN2","Prouad an deiz");
//------------------------------
define("LANGLUNDI","Lun");
define("LANGMARDI","Meurzh");
define("LANGMERCREDI","Merc'her");
define("LANGJEUDI","Yaou");
define("LANGVENDREDI","Gwener");
define("LANGSAMEDI","Sadorn");
define("LANGDIMANCHE","Sul");
// ------------------------------
define("LANGMESS1","Kas ur gemennadenn - d'a_ ");
define("LANGMESS2","Kas ur gemennadenn d'ar velestradurezh : ");
define("LANGMESS3","Kas ur gemennadenn d'ar gasourien : ");
define("LANGMESS4","Kas ur gemennadenn d'ur c'helenner : ");
define("LANGMESS5","Kas ur gemennadenn d'ur c'har er c'hlasad : ");
define("LANGMESS6","Kemennadenn kaset");
define("LANGMESS7","Nevezenti enrollet");
define("LANGMESS8","Kemennadenn kaset");
define("LANGMESS9","Respont d'ar gemennadenn - d'a_ ");
define("LANGMESS10",'N\'eo ket enrollet deiziadoù an trimiziadoù.');
define("LANGMESS11",'Kelaouit ar velestradurezh marplij.');
define("LANGMESS12",'a-benn asantiñ deiziadoù an trimiziadoù.');
define("LANGMESS13",'Klikit <a href="Resisaat_trimiziad.php">amañ</a> marplij');
define("LANGMESS14",'N\'eo ket enrollet kefredioù ar c\'hlas-mañ.');
define("LANGMESS15",'Klikit <a href="Envel_krouiñ_key.php">amañ</a> marplij');
define("LANGMESS16",'a-benn asantiñ kefredioù ar c\'hlas-mañ ');
define("LANGMESS17","Neuziadur");
define("LANGMESS18","M");     // Lizherenn gentañ ar frazenn da heul !!!
define("LANGMESS18bis","a ez euz meur a bostel da zisklêriañ,<br> disrannañ ar postelioù gant ur virgulenn.");
define("LANGMESS19","Lañset");
define("LANGMESS20","Neuziadur nevesaet");
define("LANGMESS21","Bezañ kelaouet a-fed ur gemennadenn a zo en ho poest postelioù");
define("LANGMESS22","Kas ur gemmennadenn d'ur rummad postelioù : ");
define("LANGMESS23","Sevel ur rummad postelioù");
define("LANGMESS24","Menegiñ tud ar rummadoù");
define("LANGMESS25","Dibab pep den en ur zerc'hel pouez war an douchenn"); //
define("LANGMESS26","Kadarnaat ar  grouidigezh");
define("LANGMESS27","Strollad postelioù krouet");
define("LANGMESS28","Listenn ho rummadoù postelioù");
define("LANGMESS29","Strollad ");
define("LANGMESS30","Listenn an dud ");
define("LANGMESS31","Kemennadenn eus perzh ");
define("LANGMESS32","Evit ar poent hoc'h eus ");
define("LANGMESS33","(g,c'h)kemennadenn o c'hortoz ");

// -----------------------------
// bouton
// PAS DE -->' (cote) !!!!
define("LANGBTS","Heuliad >");
define("LANGBT1","Enrollañ ar gemennadenn o tibunañ");
define("LANGBT2","Enrollañ an ditour");
define("LANGBT3","Kuitaat hep kas");
define("LANGBT4","Kas kemennadenn");
define("LANGBT5","Gortozit, Marplij");
define("LANGBT6","Kemennadennoù meneget da skarzhañ");
define("LANGBT7","Enrollañ ar gont");
define("LANGBT8","Listenn ar velestrourien / Poltredaoueg");
define("LANGBT9","Listenn ar vuhez skol  / Poltredaoueg");
define("LANGBT10","Listenn ar gelennerien / Poltredaoueg");
define("LANGBT11","Listenn an elec'hierien");
define("LANGBT12","Listenn ar strolladoù");
define("LANGBT13","Kadarnaat klas(où)");
define("LANGBT14","Enrollañ ar grouidigezh");
define("LANGBT15","Listenn klasoù");
define("LANGBT16","Listenn an danvezioù");
define("LANGBT17","Enrollañ an is-danvez");
define("LANGBT18","Enrollañ ar statud"); //
define("LANGBT19","Kadarnaat"); //
define("LANGBT20","Kuitaat hep enrollañ"); //
define("LANGBT21","Enrollañ an dibab"); //
define("LANGBT22","Nullañ an dibab"); //
define("LANGBT23","Kas ar fichenn"); //
define("LANGBT24","Adkregiñ"); //
define("LANGBT25","Adneveziñ ar bajennad"); //
define("LANGBT26","Krouiñ klas"); //
define("LANGBT27","Steuñviñ ezv pe dale"); //
define("LANGBT28","Sellet"); //
define("LANGBT29","Nullañ ezv pe dale"); //
define("LANGBT30","Asantiñ an neveziñ"); //
define("LANGBT31","Mont er rummad");
define("LANGBT32","Nullañ an dispañsoù");
define("LANGBT33","Adsevel an dispañsoù");
define("LANGBT34","Ouzhpennañ an dispañsoù");
define("LANGBT35","Enrollañ titour ");
define("LANGBT36","Dispañs adsavet --  Skipailh Triade");
define("LANGBT37","Treuzkas an ditour");
define("LANGBT38","Kas");
define("LANGBT39","Lañs an enklask");
define("LANGBT40","Adimplijout");
define("LANGBT41","Echu");
define("LANGBT42","Asantiñ listenn skolajidi nann-enrollet");
define("LANGBT43","Moullañ ar roll");
define("LANGBT44","Rentañ-kont");
define("LANGBT45","Reiñ ur sell war an titouroù");
define("LANGBT46","Enrollañ ar poltred");
define("LANGBT47","Cheñchamant all");
define("LANGBT48","Kuitaat ar bloc'had");
define("LANGBT49","Klasad a-bezh da embann");
define("LANGBT50","Nullañ");
define("LANGBT51","Asantiñ goulenn prouad");
// -----------------------------
define("LANGCA1","K"); //
define("LANGCA1bis","emennadenn nann-lennet"); // hep al lizherenn gentañ
define("LANGCA2","K"); //
define("LANGCA2bis","emennadenn lennet"); // hep al lizherenn gentañ
define("LANGCA3","M"); //
define("LANGCA3bis","enegit DD/MM/BBBB  <BR> Betek goût ma vefe un deiziad nann <BR>anavezet, resisaat ar meneg <br>"); // hep al lizherenn gentañ
// -----------------------------
define("LANGNA1","Anv"); //
define("LANGNA2","Raganv"); //
define("LANGNA3","Ger kuzh"); //
define("LANGNA3bis","Ger kuzh ar gerent "); //
define("LANGNA3ter","Ger kuzh ar skoliad "); //
define("LANGNA4","Kont nevez krouet \\n\\n Skipailh Triade "); //
define("LANGNA5","Cheñchamant&nbsp;de&nbsp;"); //
// -----------------------------
define("LANGELE1","Titouroù diwar-benn ar skoliad"); //
define("LANGELE2","Anv"); //
define("LANGELE3","Raganv"); //
define("LANGELE4","Klas"); //
define("LANGELE5","Latin"); //
define("LANGELE6","Diab/Diav"); //
define("LANGELE7","Diabarzhad"); //
define("LANGELE8","Hanter-diabarzhad"); //
define("LANGELE9","Diavaezad"); //
define("LANGELE10","Deiziad ganidigezh"); //
define("LANGELE11","Broadelezh"); //
define("LANGELE12","Niverenn vroadel"); //
define("LANGELE13","Titouroù diwar-benn ar familh"); //
define("LANGELE14","Chomlec'h 1"); //
define("LANGELE15","Kod post"); //
define("LANGELE16","Kêr"); //
define("LANGELE17","Chomlec'h 2"); //
define("LANGELE18",""); //
define("LANGELE19",""); //
define("LANGELE20","Niverenn bellgomz"); //
define("LANGELE21","Micher an tad"); //
define("LANGELE22","Pellgomz an tad"); //
define("LANGELE23","Micher ar vamm"); //
define("LANGELE24","Pellgomz ar vamm"); //
define("LANGELE25","Skol orin"); //
define("LANGELE26","Anv ar skolaj"); //
define("LANGELE27","Niverenn ar skolaj"); //
define("LANGELE28","Skoliad krouet -- Skipailh Triade"); //
define("LANGELE29","Skoliad krouet dija -- Skipailh Triade"); //
//------------------------------------------------------------
define("LANGGRP1","Anv ar strollad"); //
define("LANGGRP2","Klasoù da vennegiñ a-benn sevel ar strollad"); //
define("LANGGRP3","Dibabit klasoù disheñvel en ur zerc'hel pouez war an douchenn"); //
define("LANGGRP4","Ctrl"); //
define("LANGGRP5","hag en ur bouezañ war touchenn gleiz al logodenn."); //
define("LANGGRP6","Anv ar rann"); //
define("LANGGRP7","Klas nevez krouet -- Skipailh Triade"); //
define("LANGGRP8","Danvez nevez krouet -- Skipailh Triade"); //
define("LANGGRP9","Anv an danvez"); //
define("LANGGRP10","Anv an is-danvez"); //
//------------------------------------------------------------
//------------------------------------------------------------
define("LANGAFF1","Kefridi evit ar c'hlasad"); //
define("LANGAFF2","!! Krouiñ kefridioù <u> a ziverk </u> holl notennoù ar c'hlasad !!</u>"); //
define("LANGAFF3","Kefridi ar c'hlasadoù"); //
//------------------------------------------------------------
define("LANGPER1","Moullañ ar maread"); //
define("LANGPER2","Derou ar maread"); //
define("LANGPER3","Fin ar maread"); //
define("LANGPER4","Rann"); //
define("LANGPER5","Adimplijout ar fichenn PDF"); //
define("LANGPER6","Kelenner(ez) "); //
define("LANGPER7","penngelenner(ez) e "); //
define("LANGPER8","e klas "); //
define("LANGPER9","Bloc'had kefridi ar c'hlasadoù."); //
define("LANGPER10","DIWALL, implijet e vez ar bloc'had-mañ e-pad ur gefridi nevez,<br> distrujañ a ra holl notennoù skolidi ar c'hlasoù meneget."); //
define("LANGPER11","DIWALL, diverket e vo notennoù ar c\\'hlasadoù dibabet. \\n Ha dalc\\'hit da vont ? \\n\\n Skipailh Triade"); //
define("LANGPER12","Menegit ar c'hod alc'hwez.");
define("LANGPER13","Gwiriañ ar c'hod");
define("LANGPER14","Niver a zanvezioù");
define("LANGPER15","Krouiñ kefridi evit ar c'hlasad");
define("LANGPER16","Niv");
define("LANGPER17","Danvez");
define("LANGPER18","Kelenner");
define("LANGPER19","Kenefeder");
define("LANGPER20","Strollad");
define("LANGPER21","Yezh");
define("LANGPER22","Moullañ ar bajennad");
define("LANGPER23","Kefridi");
define("LANGPER23bis","Degemeret");  // kefridi xxxx degemeret
define("LANGPER24","diskroget"); // kefridi xxxx diskroget
define("LANGPER25","Klas");
define("LANGPER26","Brassellet");
define("LANGPER27","Sellet");
define("LANGPER28","Sellet ouzh kefridioù ar c'hlasad");
define("LANGPER29","!! Kemm kefredioù <u>a ziverk</u> holl notennoù ar c'hlasad !!");
define("LANGPER30","Kemmañ");
define("LANGPER31","Kemmañ ar c'hefridi");
define("LANGPER32","Kemm kefridi");
define("LANGPER32bis","diskroget"); // Modification d'affectation xxxx interrompue
define("LANGPER33","Diverkañ ar c'hefridi evit ");
define("LANGPER34","!! Kemm kefredioù <u>a ziverk</u> holl notennoù ar c'hlasad !!</u>");
define("LANGPER35","Kefridioù ar c'hlas");
define("LANGPER35bis","diverket"); // Affectation de la classe  xxxx supprimée
//------------------------------------------------------------------------------
define("LANGIMP1","ebarzhiañ un diaz ");
define("LANGIMP2","Menegiñ ar fichenn da ebarzhiañ ");
define("LANGIMP3","Fichenn ASCII ");
define("LANGIMP4","Fichennaoueg GEP ");
define("LANGIMP5","Modulenn ebarzhiañ ur fichennaoueg ASCII.");
define("LANGIMP6","Ar fichennaoueg da gas <FONT color=RED><B>A RANK</B></FONT> kaout <FONT COLOR=red><B>45</B></FONT> bann <I>(goullo pe get)</I> dispartiet gant an arouez \"<FONT color=red><B>;</B></font>\" <I>Da lavaret eo 44 gwech an arouez \"<FONT color=red><B>;</B></font>\"</I>");
define("LANGIMP7","Setu urzh ar bannoù da lakaat : ");
define("LANGIMP8","anv");
define("LANGIMP9","raganv");
define("LANGIMP10","klas");
define("LANGIMP11","diav/diab");
define("LANGIMP12","deiz ganidigezh");
define("LANGIMP13","broadelezh");
define("LANGIMP14","anv gward");
define("LANGIMP15","raganv gward");
define("LANGIMP16","chomlec'h1");
define("LANGIMP17","chomlec'h2");
define("LANGIMP18","kod post");
define("LANGIMP19","ker");
define("LANGIMP20","pellgomz");
define("LANGIMP21","micher tad");
define("LANGIMP22","pellgomz micher tad");
define("LANGIMP23","micher mamm");
define("LANGIMP24","pellgomz micher mamm");
define("LANGIMP25","niverenn skolaj");
define("LANGIMP26","yezh1");
define("LANGIMP27","yezh2");
define("LANGIMP28","latin");
define("LANGIMP29","Niverenn skoliad");
define("LANGIMP30","DIWALL, distrujet e vo an diaz. \\n C'hoant hoc'h eus da genderc'hel ? \\n\\n Skipailh Triade");
define("LANGIMP31","DIWALL : ar vodulenn-mañ a zo da vezañ implijet ar wech kentañ,<br> diverkañ a ra an holl ditouroù diwar-benn ar skolidi (notennoù, follennoù trimiziad, buhez skol).<br /> * park ret");
define("LANGIMP39","Menegiñ ar fichennaoueg da gas ");
define("LANGIMP40","Fichennaoueg kaset -- Skipailh Triade ");
define("LANGIMP41","N'eo ket bet doujet ouzh niver a barkoù ");
define("LANGIMP42","Menegiñ ar c'hlas a glot evit pep dave ");
define("LANGIMP43","Fichennaoueg nann enrollet ");
// ------------------------------------------------------------------------------
define("LANGABS1","Merañ ezvezañsoù - daleoù an deiz");
define("LANGABS2","Skrivañ un ezvezañs pe un dale");
define("LANGABS3","Menegiñ anv ar skoliad");
define("LANGABS4","Renabliñ an ezvezañsoù pe an daleoù diabeget");
define("LANGABS5","Renabl an <b>ezvezañsoù</b> diabeget");
define("LANGABS6","Renabl an <b>daleoù</b> diabeget");
define("LANGABS7","Sellet ha/pe kemm un ezvezañs pe dale");
define("LANGABS8","Menegiñ anv ar skoliad");
define("LANGABS9","Skritelliñ ha/pe diverkañ un ezvezañs pe un dale");
define("LANGABS10","skoliad ebet en diaz");
define("LANGABS11","Ezv/Dale");
define("LANGABS12","Abeg");
define("LANGABS13","Dale d'a_");
define("LANGABS14","Dale");
define("LANGABS15","Ezv");
define("LANGABS16","Nullañ");
define("LANGABS17","Kemmañ ezv pe dale");
define("LANGABS18","Ezvezant eus a_ ");
define("LANGABS19","d'a_ ");
define("LANGABS20","Ezv/Dale");
define("LANGABS21","Padelezh");
define("LANGABS22","Abeg");
define("LANGABS23","Eur/Deiz");
define("LANGABS24","ebarzhiañ an ezvezañsoù pe an daleoù e klas ");
define("LANGABS25","Merañ Ezvezañs - Dale");
define("LANGABS26","Merañ Ezvezañs - Dale  Rakgwelet");
define("LANGABS27","Enrollañ titour ");
define("LANGABS28","Titour enrollet ");
define("LANGABS29","D"); //premiere lettre
define("LANGABS29bis","iskarget eus :"); //suite
define("LANGABS30","Disk");
define("LANGABS31","klas ");
define("LANGABS32","D"); //premiere lettre
define("LANGABS32bis","ale da "); //suite
define("LANGABS33","e");
define("LANGABS34","eus");
define("LANGABS35","Ezvezañs - Dale - Diskarg a_ ");
define("LANGABS36","Nevesadenn");
define("LANGABS37","Moullañ ezvezañsoù - diskargoù - daleoù an deiz ");
define("LANGABS38","Pgz.");
define("LANGABS39","Pgz. Mich Tad ");
define("LANGABS40","Pgz. Mich Mamm");
define("LANGABS41","Pgz. Ker ");
define("LANGABS42","Ezvezant a_ ");
define("LANGABS43","e-pad ");
define("LANGABS44","Devezh ");
define("LANGABS45","Enrollañ an nevesadenn ");
define("LANGABS46","adalek a_ ");

define("LANGDISP8","Diverkañ diskarg");
//----------------------------------------------------------------------------
define("LANGPROJ1","Dibab ar c'hlas");
define("LANGPROJ2","Dibab an trimiziad");
define("LANGPROJ3","Trimiziad 1");
define("LANGPROJ4","Trimiziad 2");
define("LANGPROJ5","Trimiziad 3");
define("LANGPROJ6","<font class=T2>Skoliad ebet er c'hlasad-mañ</font>");
define("LANGPROJ7","Niver a zaleoù");
define("LANGPROJ8"," Hollad");
define("LANGPROJ9","Emzalc'h");
define("LANGPROJ10","munutennoù");
define("LANGPROJ11","Niver a zalc'hoù");
define("LANGPROJ12","roet gant ");
define("LANGPROJ13","Listenn");
define("LANGPROJ14","Keid SKoliad");
define("LANGPROJ15","Keid Klasad");
define("LANGPROJ16","Keidenn Skoliad");
// ----------------------------------------------------------------------------
define("LANGDISP1","<font class=T2>skoliad ebet gant an anv-mañ</font>");
define("LANGDISP2","Abeg");
define("LANGDISP3","Paperenn digant ar mezeg");
define("LANGDISP4","Pranted&nbsp;a_&nbsp;");
define("LANGDISP5","en danvez ");
define("LANGDISP6","Eur an diskarg ");
define("LANGDISP7","<B><font color=red>M</font></B>enegit an DD/MM/BBBB  <BR> en daou bark");
define("LANGDISP9","Skritellaoueg <b>klok</B> an diskargoù");
define("LANGDISP10","E");
// ----------------------------------------------------------------------------
define("LANGASS1","TRIADE skoazell");
define("LANGASS2","A ginnig ur skoazell evit ho sikour da implijout Triade.<br /><br />Ur gudenn hoc'h eus gant unan eus servijoù Triade, leugnit ar furmskrid da heul gant an titouroù war ar servij. Hon ijinourien a wirieko ar servij-se.");
define("LANGASS3","Ezel dedennet");
define("LANGASS4","Melestradurezh");
define("LANGASS5","Kelenner");
define("LANGASS6","Buhez skol");
define("LANGASS6bis","Kar");
define("LANGASS7","Ober");
define("LANGASS8","Krouidigezh");
define("LANGASS9","Sell");
define("LANGASS10","Diverkadenn");
define("LANGASS11","Tra all");
define("LANGASS12","Servij");
define("LANGASS13","Kont implijer");
define("LANGASS14","Postel");
define("LANGASS15","Kefridi");
define("LANGASS16","Diaz titouroù");
define("LANGASS17","Klas");
define("LANGASS18","Danvez");
define("LANGASS19","Enklask");
define("LANGASS20","Prouad");
define("LANGASS21","Steuñv");
define("LANGASS22","Diskarg");
define("LANGASS23","Emzalc'h");
define("LANGASS24","Kelc'hlizher");
define("LANGASS25","Follenn drimiziad");
define("LANGASS26","Prantad");
define("LANGASS27","Evezhiadenn");
define("LANGASS28","TRIADE skoazell a drugareka ac'hanoc'h evit ho skoazell");
define("LANGASS29","Skipailh Triade.");
define("LANGASS30","Skipailh Triade evit ho servij");
define("LANGASS31","Un aozad dibar ha nevez savet eo TRIADE, setu perak e c'houlennomp ganeoc'h kas deomp hoc'h alioù ha kinnigoù a-benn derc'hel ul lec'hienn a rento servij da vat d'an implijerien ! Trugarez deoc'h :-)");
define("LANGASS32","Levr aour");
define("LANGASS33","Ho testeni war-eeun : skrivit hoc'h evezhiadennoù war hol levr aour.");
define("LANGASS34","Kaset eo bet ho kemennadenn, respontet e vo deoc'h.<br> <BR>Trugarez da implijout TRIADE, ken ar c'hentañ.<BR><BR><BR><UL><UL>Skipailh Triade.<BR>");
define("LANGASS35","Tra all");
define("LANGASS36","SMS");
define("LANGASS37","WAP");
define("LANGASS38","Poltredaoueg");
define("LANGASS39","Kod barenn");
define("LANGASS40","Staj Mich.");
// -----------------------------------------------------------------------------
define("LANGRECH1","<font class=T2>skoliad ebet er c'hlas</font>");
define("LANGRECH2","Klask ");
define("LANGRECH3","<font class=T2>skoliad ebet evit an enklask-mañ</font>");
define("LANGRECH4","Titouroù / Kemm");
// ---------------------------------------------------------------------------------
define("LANGBASE1","DIWALL : ar bloc'had-mañ a zo da vezañ implijet e-kerzh an implij kentañ,<br> diverkañ a ra an holl ditouroù diwar-benn ar skolidi  (notennoù, follennoù trimiziad, buhez skol).");
define("LANGBASE2"," Ar fichennaouegoù da enporzhiañ a rank bezañ dindan stumm dbf ");
define("LANGBASE3","Sed aze listennad ar fichennaouegoù ");
define("LANGBASE4","Modulenn ebarzhiañ fichennaouegoù GEP ");
define("LANGBASE5","Enporzhiañ un diaz GEP ");
define("LANGBASE6","Hollad ar skolidi en diaz DBF ");
define("LANGBASE7","Hollad ar skolidi gant ur c'hlas ");
define("LANGBASE8","Hollad ar skolidi hep klas ");
define("LANGBASE9","Adpakañ ar gerioù kuzh ");
define("LANGBASE10","Dibosupl da zigeriñ ar fichennaoueg F_ele.dbf");
define("LANGBASE11","Graet eo bet war dro an diaz -- Skipailh Triade");
define("LANGBASE12","N'eo ket reizh ar fichennaoueg dibabet !");
define("LANGBASE13","Setu listennad ar gerioù kuzh");
define("LANGBASE14","Adpakañ al listennad o tibab ha kopiañ an holl linennoù ha pegañ e-barzh ur fichennaoueg \"txt\".");
define("LANGBASE15","Ha gant Excel pe OpenOffice, adpakañ ar fichennaoueg \"txt\"  o tibab ar poent-skej da zispartiañ ar bannoù.");
define("LANGBASE17"," Diwall : n'haller kaout ar gerioù kuzh nemet war <br />ar bajenn-mañ !! Soñjit en adpakañ al listennad <b>A-RAOK</b> echuiñ ");
define("LANGBASE18","TITOUR N'HALLER KET KAOUT");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGBULL1","Moullañ ar follennoù trimiziad");
define("LANGBULL2","Menegit ar c'hlas");
define("LANGBULL3","Bloavezh Skol");
define("LANGBULL4","<b><FONT COLOR=red>DIWALL</FONT></B> Ezhomm a zo <B>Adobe Acrobat Reader</B>.  Digoust eo ar poellad <a href=\"#\" onclick=\"open('./accrobat.php','acro','width=500,height=300')\"><B>ICI</B></A>");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGPARENT1","kemenadenn ebet");
define("LANGPARENT2","Kannad ebet bet lakaet evit ar poent");
define("LANGPARENT3","Kannaded ar skolidi");
define("LANGPARENT4","Kannaded ar gerent");
define("LANGPARENT5","Listenn ar gannaded");
//----------------------------------------------------------------------//
define("LANGPURG1","Modulenn diverkañ an diaz");
define("LANGPUR2","Modulenn diverkañ an diaz");
define("LANGPUR3","DIWALL: da vezañ implijet eo ar vodulenn-mañ <br>pa oc'h eus c'hoant da ziverkañ titouroù an diaz.");
define("LANGPUR4","DIWALL, hollad an titouroù a vo diverket. \\n C'hoant oc'h eus da genderc'hel ? \\n\\n Skipailh Triade");
define("LANGPUR5","An titouroù a vo diverket");
define("LANGPUR6","Keloù : Selektiñ \"Skolidi\" a dalv e vo diverket an notennoù, ezvezañsoù, emzalc'hioù, diskargoù, daleoù");
define("LANGPUR7","Menegiñ an elfenn(où) da zistrujañ : ");
define("LANGPUR8","Da virout");
define("LANGPUR9","Da ziverkañ");
//----------------------------------------------------------------------//
define("LANGCHAN0","Modulenn evit cheñch klas d'ur skoliad pe da veur a skoliad");
define("LANGCHAN1","DIWALL: da vezañ implijet eo ar vodulenn-mañ <br>pa oc'h eus c'hoant da cheñch klas d'ur skoliad");
define("LANGCHAN3","DIWALL, an holl ditouroù diwar-benn ar skoliad pe ar skolidi dedennet gant ar cheñchamant klas a vo diverket");
//----------------------------------------------------------------------//
define("LANGGEP1",'ebarzhiañ ar fichennaoueg GEP');
define("LANGGEP2",'Menegiñ ar fichennaoueg');
//----------------------------------------------------------------------//
define("LANGCERT1"," pellgargañ an testeni-mañ ");
//----------------------------------------------------------------------//
define("LANGPROFR1",'Menegiñ ar skolidi gant dale');
define("LANGPROFR2",'Skrivañ an daleoù  ');
define("LANGKEY1",'<font class=T1>N\'eus ket a alc\'hwez enrollañ </font>');
define("LANGDISP20",'Ouzhpennañ diskargoù');
define("LANGPROFA",'<br><center><font size=2>N\'eus ket a alc\'hwez enrollañ </font><br><br>Kit en darempred gant ho merour Triade, <br>a-benn kadarnaat enrolladenn Triade. </center><br><br>');
define("LANGPROFB",'Ouzhpennañ un notenn e ');
define("LANGPROFC",'Kadarnaat enrolladenn an notennoù ');
define("LANGPROFD",'Enrollañ an notennoù');
define("LANGPROFE",'&nbsp;&nbsp;<i><u>Keloù</u>: Gant an douchenn KAS e c\'haller tremen eus an eil notenn d\'eben.</i>');
define("LANGPROFF",'Ouzhpennañ un notenn');
define("LANGPROFG",'Menegiñ ar c\'hlas');
//----------------------------------------------------------------------//
define("LANGMETEO1",'DEIZ');
define("LANGMETEO2",'NOZ');
//----------------------------------------------------------------------//
define("LANGPROFP1","Kemenadenn evit ar c'hlasad");
define("LANGPROFP2","Enrollañ ar gemenadenn");
define("LANGPROFP3","Kemenadenn a-berzh ar penngelenner");
//----------------------------------------------------------------------//
// Module Stage Pro
define("LANGSTAGE1","Steuñviñ ar stajoù ");
define("LANGSTAGE2","Sellet ouzh deiziadoù ar stajoù ");
define("LANGSTAGE3","Ouzhpennañ ");
define("LANGSTAGE4","Lakaat ");
define("LANGSTAGE5","Ouzhpennañ deiziad ur staj ");
define("LANGSTAGE6","Kemm deiziad ur staj ");
define("LANGSTAGE7","Diverkañ deiziad ur staj ");
define("LANGSTAGE8","Merañ an embregerezhioù ");
define("LANGSTAGE9","Sellout ouzh an embregerezhioù ");
define("LANGSTAGE10","Ouzhpennañ un embregerezh ");
define("LANGSTAGE11","Kemm titouroù un embregerezh ");
define("LANGSTAGE12","Diverkañ un embregerezh ");
define("LANGSTAGE13","Merañ ar skolidi ");
define("LANGSTAGE14","Sellout ouzh ar skolidi gant un embregerezh ");
define("LANGSTAGE15","Lakaat un embregerezh d'ur skoliad ");
define("LANGSTAGE16","Kemm titouroù ur skoliad ");
define("LANGSTAGE17","Diverkañ embregerezh ur skoliad ");
define("LANGSTAGE18","Sellout ouzh deiziadoù ar stajoù");
define("LANGSTAGE19","Staj");
define("LANGSTAGE20","Klask embregerezhioù");
define("LANGSTAGE21","Sellout ouzh an embregerezhioù diouzh oc'h oberiantiz");
define("LANGSTAGE22","Sellout ouzh an embregerezhioù");
//----------------------------------------------------------------------//
define("LANGGEN1","Melestradurezh");
define("LANGGEN2","Buhez skol");
define("LANGGEN3","Kelennerien");
//----------------------------------------------------------------------//
define("LANGDST1","Goulenn prouadoù");
define("LANGDST2","Demat, <br> <br> Ho koulenn prouad evit a_ ");
define("LANGDST3","<br><br><b>n\'eo ket bet asantet</b>, dibabit un deiz all pe kit en darempred ganeomp. <br><br> Trugarez");
define("LANGDST4","<br><br><b>a zo bet enrollet</b> kit en darempred ganeomp evit kaout titouroù ouzhpenn. <br><br> Trugarez");
define("LANGDST5","evit a_ ");
define("LANGDST6","Sujed / Danvez");
define("LANGDST7","Goulenn nac'het");
define("LANGDST8","Goulenn asantet");
//----------------------------------------------------------------------//
define("LANGCALEN1","Darvoud");
define("LANGCALEN2","Steuñv a_ ");
define("LANGCALEN3","Ouzhpennañ un darvoud");
define("LANGCALEN4","Diverkañ un darvoud");
define("LANGCALEN5","Nevesiñ ar bajenn");
define("LANGCALEN6","Deiziataer an darvoudoù");
define("LANGCALEN7","E klas ");
define("LANGCALEN8","Prouad ");
define("LANGCALEN9","Prouad(où) an deiz");
//----------------------------------------------------------------------//
//module reservation
define("LANGRESA1","Merañ an dafar");
define("LANGRESA2","Merañ ar salioù");
define("LANGRESA3","Listenn an dafar");
define("LANGRESA4","Listenn ar salioù");
define("LANGRESA5","Ouzhpennañ un dafar");
define("LANGRESA6","Kemm un dafar");
define("LANGRESA7","Diverkañ un dafar");
define("LANGRESA8","Ouzhpennañ ur sal");
define("LANGRESA9","Kemm ur sal");
define("LANGRESA10","Diverkañ ur sal");
define("LANGRESA11","Mirout dafar / sal");
define("LANGRESA12","Mirout dafar");
define("LANGRESA13","Mirout sal");
define("LANGRESA14","Mirout");
define("LANGRESA15","Krouiñ un dafar");
define("LANGRESA16","Anv an dafar");
define("LANGRESA17","Enrollañ ar grouidigezh");
define("LANGRESA18","Titouroù ouzhpenn");
define("LANGRESA19","Dafar enrollet");
define("LANGRESA20","Krouiñ ur sal");
define("LANGRESA21","Anv ar sal");
define("LANGRESA22","Sal enrollet");
define("LANGRESA23","Diverkañ sal");
define("LANGRESA24","Sal");
define("LANGRESA25","Diverkañ ar sal");
define("LANGRESA26","Sal diverket");
define("LANGRESA27","ur sal");
define("LANGRESA28","Dibosupl diverkañ ar sal-mañ. \\n\\n Sal implijet.  ");
define("LANGRESA29","Dafar diverket");
define("LANGRESA30","Dibosupl diverkañ an dafar-mañ. \\n\\n Dafar implijet.  ");
define("LANGRESA31","un dafar");
define("LANGRESA32","Diverkañ dafar");
define("LANGRESA33","Dafar");
define("LANGRESA34","Diverkañ un dafar");
define("LANGRESA35","Listenn an dafar");
define("LANGRESA36","DEIZIAD");
define("LANGRESA37","Eus");
define("LANGRESA38","DA");
define("LANGRESA39","Gant");
define("LANGRESA40","Titouroù");
define("LANGRESA41","Kadarnaat");
define("LANGRESA42","Kadarnaet");
define("LANGRESA43","Nann&nbsp;Kadarnaet");
define("LANGRESA44","Steuñv dafar");
define("LANGRESA45","Dafar");
define("LANGRESA46","Dafar miret dija d'an deiz-se");
define("LANGRESA47","Sellout ouzh steuñv mirout an dafar-mañ");
define("LANGRESA48","Mirout adalek ");
define("LANGRESA49","D'a_ ");
define("LANGRESA50","Dafar miret da c'hortoz bezañ kadarnaet");
define("LANGRESA51","Steuñv sal");
define("LANGRESA52","Sal");
define("LANGRESA53","Sal miret dija d'an deiz-se");
define("LANGRESA54","Sal miret da c'hortoz bezañ kadarnaet");
define("LANGRESA55","Sellout ouzh steuñv mirout ar sal-mañ");
define("LANGRESA56","Kadarnaat ar mirout");
define("LANGRESA57","Steuñv");
define("LANGRESA58","Kadarnaat");
//----------------------------------------------------------------------//
define("LANGTTITRE1","Mont e-barzh izili");
define("LANGTTITRE2","Ezel");
define("LANGTTITRE3","Digoradur ar gont");
define("LANGTTITRE4","Trugarez da c'hortoz");
//--------------
define("LANGTP1","Anv");
define("LANGTP2","Raganv");
define("LANGTP3","Ger kuzh");
define("LANGTCONNEXION","Lugañ");
define("LANGTERREURCONNECT","Fazi lugañ");
define("LANGTCONNECCOURS","O lugañ emañ ");
define("LANGTFERMCONNEC","Klikit amañ evit serriñ ho kont");
define("LANGTDECONNEC","Emañ o vezañ diluget");
// --- non corrige
define("LANGTBLAKLIST0",'<center><br><br><b><font color=red  class=T1>Serret eo ho kont !!<br> A-benn addigeriñ ho kont, kit en darempred gant ho kelenndi.</b><br><br></center>');

define("LANGMOIS1","Genver");
define("LANGMOIS2","C'hwevrer");
define("LANGMOIS3","Meurzh");
define("LANGMOIS4","Ebrel");
define("LANGMOIS5","Mae");
define("LANGMOIS6","Mezheven");
define("LANGMOIS7","Gouere");
define("LANGMOIS8","Eost");
define("LANGMOIS9","Gwengolo");
define("LANGMOIS10","Here");
define("LANGMOIS11","Du");
define("LANGMOIS12","Kerzu");


//______________________________________________________________________//
// non corrigé
// -----------

// non corrige non traduit en anglais
define("LANGDEPART1","ar skoliad");

define("LANGVALIDE","Kadarnaat");
define("LANGIMP45","Embann");

define("LANGMESS34","Kemenadenn diviet.");
define("LANGMESS35","Lakaat ar strollad-mañ da vezañ publik.");
define("LANGMESS36","Kemenadenn diverket");
define("LANGMESS37","N'eo ket bet asantet an ober-mañ gant merour Triade");

define("LANGRESA59","Anv ar sal");
define("LANGRESA60","Keloù");

define("LANGMAINT0","Un dalc'hidigezh a zo bet raktreset war ar poellad");
define("LANGMAINT1","Ne vo ket tu implijout Triade ");
define("LANGMAINT2","etre");
define("LANGMAINT3","ha");

define("LANCALED1","Bloavezh raok");
define("LANCALED2","Bloavezh da heul");


define("LANGTTITRE5","Kudenn mont e-barzh");
define("LANGTTITRE6","Goulennoù");
define("LANGTPROBL1","Bremañ emañ servij Triade o seveniñ.");
define("LANGTPROBL2","Ur goulenn am eus");
define("LANGTPROBL3","Enrollañ ar goulenn");
define("LANGTPROBL4","Kuitaat hep enrollañ");
define("LANGTPROBL5","Displegit ho kudenn deomp");
define("LANGTPROBL6","Skol*: ");
define("LANGTPROBL7","Postel : ");
define("LANGTPROBL8","Kemenadenn : ");
define("LANGTPROBL9","(* bannoù ret)");
define("LANGTPROBL10","Enrollañ ar gudenn");
define("LANGTPROBL11","Trugarez evit ho koulenn -- Skipailh Triade");
define("LANGTPROBL12","Klasket e vo renkañ ho kudenn an abretañ ar gwellañ --  Skipailh Triade ");

define("LANGELEV1","Notennoù skol");

define("LANGFORUM1","Forum - Listenn ar c'hemennadennoù");
define("LANGFORUM2","N'eus kemennadenn ebet bet postet war ar forum-mañ");
define("LANGFORUM3","Gallout a rit ");
define("LANGFORUM3bis"," postañ ");
define("LANGFORUM3ter"," ur gemennadenn gentañ m'oc'h eus c'hoant ");
define("LANGFORUM4","Postañ ur gemennadenn nevez");
define("LANGFORUM5","Forum - Postañ ur gemennadenn");
define("LANGFORUM6","Karta da vezañ doujet");
define("LANGFORUM7","Mankadenn : n'eus ket eus ar gemennadenn a-gevret-mañ.");
define("LANGFORUM8","Distro da listenn ar c'hemennadennoù postet");
define("LANGFORUM9","--- Kemennadenn orin ---");
define("LANGFORUM10","Hoc'h anv ");
define("LANGFORUM11","Ho postel ");
define("LANGFORUM12","Sujed ");
define("LANGFORUM13","Kas"); // --> bouton envoyer
define("LANGFORUM14","Distro da listenn ar c'hemennadennoù postet");
define("LANGFORUM15","Forum - kas ur gemennadenn");
define("LANGFORUM16","<b>Mankadenn</b> : ar bajennad-mañ n'hall ket bezañ goulet<br> war-bouez d'ar gemennadenn-mañ bezañ ");
define("LANGFORUM16bis"," kaset ");
define("LANGFORUM17","<b>Mankadenn</b> : testenn ebet en ho kemennadenn.<br>");
define("LANGFORUM18","<b>Mankadenn</b> : n'hoc'h eus ket lakaet hoc'h anv.<br>");
define("LANGFORUM19","Mankadenn ! N'eo ket bet postet ho kemennadenn. ");
define("LANGFORUM20","<b>Mankadenn</b> : dibosupl da nevesiñ ar fichennaoueg index. <br>");
define("LANGFORUM21","N'eo ket bet postet ho kemennadenn.");
define("LANGFORUM22","Postet mat eo bet ho kemennadenn.<br>Trugarez evit ho kemer perzh.");
define("LANGFORUM23","Distro da listenn ar c'hemennadennoù postet");
define("LANGFORUM24","Forum - lenn ur gemennadenn");
define("LANGFORUM25","N'eus kemenadenn ebet bet postet war ar forum-mañ.");
define("LANGFORUM26","Gallout a rit ");
define("LANGFORUM26bis","postañ");
define("LANGFORUM26ter","ur gemennadenn gentañ m'oc'h eus c'hoant.");
define("LANGFORUM27","N'eus ket eus ar gemenadenn-mañ pe diverket eo bet gant merour ar forum.<br>");
define("LANGFORUM28","Distro da listenn ar c'hemennadennoù postet");
define("LANGFORUM30","Aozer");
define("LANGFORUM31","Deiziad");
define("LANGFORUM32","Postañ ur respont");
define("LANGFORUM33","Kemennadennoù kent (da heul poell ar gaoz)");
define("LANGFORUM34","Kemennadennoù da heul (da heul poell ar gaoz)");

define("LANGPROFH","Labour skol d'ober en ");
define("LANGPROFI","Enrollañ al labour d'ober ");
define("LANGPROFJ","Labour d'ober ");
define("LANGPROFK","lakaet&nbsp;d'a_&nbsp;");
define("LANGPROFL","Kadarnaat an deiziad");
define("LANGPROFM","Evit a_ ");
define("LANGPROFN","Labour eus ");
define("LANGPROFO","Labour skol ");
define("LANGPROFP","Lakaat ar benngelennerien");
define("LANGPROFQ","A-benn arc'hoazh");
define("LANGPROFR","Evit dec'h");
define("LANGPROFS","Danvez pe Sujed");
define("LANGPROFT","Kadarnaat ar goulenn prouad");
define("LANGPROFU","Goulenn kaset -- Skipailh Triade");


define("LANGPROJ17","Niver a ezvezañsoù");
define("LANGPROJ18","devezh");

define("LANGCALEN10","Deiziataer ar prouadoù");

define("LANGPARENT6","Listenn an daleoù");
define("LANGPARENT7","Listenn an ezvezañsoù");
define("LANGPARENT8","Ezvezañt a_ ");
define("LANGPARENT9","Listenn an diskargoù");
define("LANGPARENT10","Prantad a_ ");
define("LANGPARENT11","Da"); // indique une date (heure)
define("LANGPARENT12","D'a_"); // indique une date jour
define("LANGPARENT13","Testeni");
define("LANGPARENT14","kastiz");
define("LANGPARENT15","kastiz");
define("LANGPARENT16","Dalc'het");
define("LANGPARENT17","da");  // indique une heure
define("LANGPARENT18","Dalc'h graet");
define("LANGPARENT19","Listenn ar c'helc'hlizherioù melestradur");
define("LANGPARENT20","Tizhout Fichennaoueg");
define("LANGPARENT21","Gweladus gant ");
define("LANGPARENT22","Deiziataer an darvoudoù ");
define("LANGPARENT23","Deiziataer ar prouadoù ");
define("LANGPARENT24","Goulenn ur prouad ");


define("LANGAUDIO1","Kemennadenn Audio");
define("LANGAUDIO2","D'a_ "); // indique une date
define("LANGAUDIO3","K"); // premiere lettre
define("LANGAUDIO3bis","emennadenn audio <br />stumm <b>mp3</b><br>Ment brasañ ar fichennaoueg : ");
define("LANGAUDIO4","Enrollañ ar gemennadenn");
define("LANGAUDIO5","Gortozit 2 pe 3 munutenn goude bezañ kaset ar fichennaoueg son.");
define("LANGAUDIO6","Diverkañ ar gemennadenn audio");


// non ajouté dans le fichier 06/09

define("LANGOK","Ok");
define("LANGCLICK","Klikit amañ");
define("LANGPRECE","Kent");
define("LANGERROR1","Titouroù digavadus");
define("LANGERROR2","titour ebet");


define("LANGPROF1","Menegiñ an danvez");
define("LANGPROF2","Niver a notennoù");
define("LANGPROF3","Sellout ouzh an notennoù");
define("LANGPROF4","strollad");
define("LANGPROF5","Dibab an trimiziad");
define("LANGPROF6","Sujed "); // sujet du devoir
define("LANGPROF7","Anv ar sujed "); // sujet du devoir
define("LANGPROF8","Notenn"); //note d'un devoir
define("LANGPROF9","Labour skol d'ober er ger");
define("LANGPROF10","Cheñch un notenn");
define("LANGPROF11","Diverkañ ur prouad"); // devoir --> interrogation
define("LANGPROF12","Penngelenner");
define("LANGPROF13","Fichenn Skoliad");
define("LANGPROF14","Ouzhpennañ notennoù en ");
define("LANGPROF15","Cheñch un notenn en");
define("LANGPROF16","Anv ar prouad");
define("LANGPROF17","Deiziad&nbsp;ar&nbsp;prouad"); // &nbsp; --> egal un blanc
define("LANGPROF18","Gortozit");
define("LANGPROF19","Kadarnaat ar cheñchamantoù");
define("LANGPROF20","Enrollañ ar cheñchamantoù");
define("LANGPROF21","Cheñch notenn en");
define("LANGPROF22","Sellout ouzh an notennoù en");
define("LANGPROF23","Diverkañ ur prouad en");
define("LANGPROF24","Prouad a_ "); // interrogation du
define("LANGPROF25","a zo bet diverket");
define("LANGPROF26","Titouroù diwar-benn ar skoliad");
define("LANGPROF27","Titouroù melestradurel");
define("LANGPROF28","Titouroù war ar vuhez skol");
define("LANGPROF29","Titouroù medisinerezh");
define("LANGPROF30","Titour a_");
define("LANGPROF31","Eus"); // indiquant une personne


define("LANGEL1","Anv");
define("LANGEL2","Raganv");
define("LANGEL3","Klas ");
define("LANGEL4","Yezh1");
define("LANGEL5","Yezh2");
define("LANGEL6","Latin");
define("LANGEL7","Bod ha boued");
define("LANGEL8","Deiz ganidigezh");
define("LANGEL9","Broadelezh");
define("LANGEL10","Ger kuzh");
define("LANGEL11","Anv Familh");
define("LANGEL12","Raganv");
define("LANGEL13","straed");
define("LANGEL14","Chomlec'h 1");
define("LANGEL15","Kod post");
define("LANGEL16","Kêr");
define("LANGEL17","straed");
define("LANGEL18","Chomlec'h 2");
define("LANGEL19","Kod post");
define("LANGEL20","Kêr");
define("LANGEL21","Pellgomz");
define("LANGEL22","Micher an tad");
define("LANGEL23","Pellgomz an tad");
define("LANGEL24","Micher ar vamm");
define("LANGEL25","Pellgomz ar vamm");
define("LANGEL26","Skol");
define("LANGEL27","Kod ar skol");
define("LANGEL28","Kod post");
define("LANGEL29","Kêr");
define("LANGEL30","Niverenn vroadel");


define("LANGPROF32","Titouroù skol");
define("LANGPROF33","Labour er ger");
define("LANGPROF34","Sellout e-krezh ar sizhun");
define("LANGPROF35","Sizhun dremenet");
define("LANGPROF36","Sizhun a zeu");
define("LANGTP23"," DIWALL - Goulenn mirout sal pe dafar  - RANN");
define("LANGRESA61","Anv an dafar");


// non transmis (4) 27/09/2005

define("LANGIMP46","Raganv");
define("LANGIMP47","Anvadenn (Ao. pe It. pe Dmz) ");
define("LANGIMP48","Anv");
define("LANGIMP49","* titour ret");
define("LANGIMP50","Ar fichennaoueg da gas <FONT color=RED><B>A RANK</B></FONT> kaout <FONT COLOR=red><B>3</B></FONT> fark <I>(nann goulo)</I> dispartiet gant \"<FONT color=red><B>;</B></font>\" <I>Da lavaret eo 2 wech \"<FONT color=red><B>;</B></font>\"</I>");
define("LANGIMP51","ger kuzh kerent");
define("LANGIMP52","ger kuzh skoliad");

define("LANGELE244","Postel");

define("LANGTP12","Kadarnit ho kont");

define("LANGacce_dep1","Fazi lugañ");
define("LANGacce_dep2","Gwiriekit hoc'h identelezh,<br> ma chom ar gudenn kelaouit <br>ho merour Triade gant <br>al liamm 'Kudenn mont e-barzh' er <br>roll kleiz");

define("LANGacce_ref1","Mankadenn a-rummad : Mont e-barzh diaotreet");
define("LANGacce_ref11","Gwelladennet d'a ");
define("LANGacce_ref12","gant ");
define("LANGacce_ref13","gant  ");
define("LANGacce_ref2","MONED DIAOTREET");
define("LANGacce_ref3","A-benn mont war ho kont e ranker lugañ.");
define("LANGacce1","Ar skoliad(ez) ");
define("LANGacce12","en (he) deus ur binijenn da rentañ, <br> diwar ar rann : ");
define("LANGacce13","evit an abeg ");
define("LANGacce14","An dever-mañ a zo d'ober : ");
define("LANGacce2","Diverkañ ar gemennadenn-mañ : ");
define("LANGacce21","Diverkañ");
define("LANGacce3","Ar skoliad(ez) ");
define("LANacce31","n'eo ket bet gwelet</b></font> gant ar penngasour(ez), <b>evit an dalc'h</b>,  diwar ar rann :");
define("LANacce32","evit an abeg : ");
define("LANGacce4","An dever-mañ a zo d'ober :");
define("LANGacce5","diverkañ");
define("LANGacce6","Merañ an emzalc'h");
define("LANGaccrob11","Pellgargañ ar poellad Adobe Acrobat Reader 8.1.0 fr");
define("LANGaccrob2","23,4 Mo  evit Windows 2000/XP/2003/Vista");
define("LANGaccrob3","Amzer pellgargañ :");
define("LANGaccrob4","e 56 K : 57 mun ha 53.4 s.");
define("LANGaccrob5","e 512 K : 6 mun ha 31.2 s.");
define("LANGaccrob6","e 5 M : 0 mun ha 37 s.");
define("LANGaccrob7","Pellgargañ ar poellad Adobe Acrobat Reader 6.O.1 fr");
define("LANGaccrob8","Pouez : ");
define("LANGaccrob9","0.40916 Mo evit NT/95/98/2000/ME/XP");
define("LANGaccrob10","e 56 K :0 mun ha 58.2 s");
define("LANGaccrob11bis","e 512 K : 0 mun ha 6.6 s ");
define("LANGaffec_cre21","Krouiñ kefridioù evit ar c'hlas ");
define("LANGaffec_cre22","Kefredioù war staliañ");
define("LANGaffec_cre23","Emañ ar poellad kefrediañ o tigeriñ<br>Klikañ ma ne weler ket ar bajennad ");
define("LANGaffec_cre24","Triade - Kont ");
define("LANGaffec_cre31","KROUIDIGEZH - KEFREDIADUR");
define("LANGaffec_cre41","Moullañ");
define("LANGaffec_mod_key1","Dasparzh ar c'hlasadoù");
define("LANGaffec_mod_key2","Bloc'had ar c'hemm kefridioù ar c'hlasadoù.");
define("LANGaffec_mod_key3","DIWALL ar vodulenn-mañ a zo da implijout pa vez kemmet kefredioù,<br> diverkañ a ra holl notennoù bugale ar c'hlasoù cheñchet. ");
define("LANGaffec_mod_key4","DIWALL, notennoù ar c'hlasadoù diuzet a vo diverket. \\n Ha kendalc'het 'vo ? \\n\\n Skipailh Triade");
define("LANGattente1","Gortozit - Triade");
define("LANGattente2","Gortozit marplij ....");
define("LANGattente3","Skipailh Triade.");
define("LANGatte_mess1","TRIADE - Gortoz - Posteloù");
define("LANGatte_mess2","Gortozit marplij ....");
define("LANGatte_mess3","servij TRIADE");
define("LANGbasededon20","Kas ar fichennaoueg");
define("LANGbasededon201","mann ebet");
define("LANGbasededon2011","ebarzhiañ fichennaouegoù GEP");
define("LANGbasededon202","Fichennaoueg kaset -- Skipailh Triade");
define("LANGbasededon203","Fichennaoueg nann enrollet");
define("LANGbasededon31","Menegiñ ar c'hlasad a zere gant kement dave 'zo");
define("LANGbasededon32","Dibab ...");
define("LANGbasededon33","hini ebet");
define("LANGbasededon34","Kas ar fichennaoueg a c'hall padout <b>2 pe 4 munutenn</b> diouzh niver ar skolidi.");
define("LANGbasededon35","Rankout a ra ar fichennaoueg bezañ dindan stumm <b>dbf</b> ha bezañ galvet <b>F_ele.dbf</b>");
define("LANGbasededon41","Mank gant an niver a glasoù !!! - Kelaouit skipailh Triade <br /><br /> maintenance@triade.ht.st</center>");
define("LANGbasededon42","Mankadenn bizskrivañ er c'hlasoù, ur c'hlas a zo bet bizskrivet meur a wech -- Skipailh Triade");
define("LANGbasededon43","Kemennadenn a_ : ");
define("LANGbasededon44","Eus");
define("LANGbasededon45","Ezel :");
define("LANGbasededon46","Kemennadenn :");
define("LANGbasededon47","DIAZ NEVEZ:");
define("LANGbasededon48","- gant GEP");
define("LANGbasededon49"," Skolaj :");
define("LANGbasededoni11","'Diwall','./image/commun/warning.jpg','<font face=Verdana size=1><font color=red>A</font>r bloc'had <b>dbase</b> n\'eo ket <br> karget !! <i>Ret evit ebarzhiañ <br> un diaz GEP.");
define("LANGbasededoni21","DIWALL, distrujet e vo an diaz kozh. \\n C'hoant hoc'h eus da genderc'hel ? \\n\\n Skipailh Triade");
define("LANGbasededoni31","Evit peseurt rann eo ar fichennaoueg ");
define("LANGbasededoni32","Sellout a ra ar fichennaoueg ouzh : ");
define("LANGbasededoni33","Enporzhiañ skolidi : ");
define("LANGbasededoni34","Enporzhiañ kelennerien :");
define("LANGbasededoni35","Enporzhiañ tud ar vuhez skol : ");
define("LANGbasededoni36","Enporzhiañ tud ar velestradurezh : ");
define("LANGbasededoni41","Klas kent");
define("LANGbasededoni42","Bloavezh kent");
define("LANGbasededoni51","Evit an anvadenn");
define("LANGbasededoni52","talvoudegezh : <b>0</b> evit Aot.<br>");
define("LANGbasededoni53","talvoudegezh : <b>1</b> evit Itr.<br>");
define("LANGbasededoni54","talvoudegezh : <b>2</b> evit Dmz.<br>");
define("LANGbasededoni61","mank");
define("LANGbasededoni71","Enporzhiañ ar fichennaoueg ASCII");
define("LANGbasededoni72","Kemennadenn a_ : ");
define("LANGbasededoni721","Eus");
define("LANGbasededoni722","Ezel :");
define("LANGbasededoni723","Kemennadenn :");
define("LANGbasededoni724","DIAZ NEVEZ:");
define("LANGbasededoni725","- gant ASCII");
define("LANGbasededoni726"," Skolaj :");
define("LANGbasededoni73","Hollad enrolladennoù en diaz ");
define("LANGbasededoni91","Enporzhiañ ar fichennaoueg ASCII");
define("LANGbasededoni92","Mank gant an niver a glasoù !!! - Kelaouit skipailh Triade <br />");
define("LANGbasededoni93","Mankadenn bizskrivañ er c'hlasoù, ur c'hlas a zo bet bizskrivet meur a wech -- Skipailh Triade");
define("LANGbasededoni94","Roadenn tretet eus an diaz -- Skipailh Triade<br />");
define("LANGbasededoni95","Hollad skolidi enrollet en diaz : ");
define("LANGPIEDPAGE","<p> La <b>T</b>ransparence et la <b>R</b>apidité de l'<b>I</b>nformatique <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br>A-benn gwelet al lec'hienn-mañ ar gwellañ ma c'haller : spister izelañ : 800x600 <br>  © 2000/2006 Triade - Holl wirioù miret");

define("LANGAPROPOS1","Neuz (Version)");
define("LANGAPROPOS2","Holl wirioù miret");
define("LANGAPROPOS3","Aotre implijout");
define("LANGAPROPOS4","Product ID");

define("LANGTELECHARGER","Pellgargañ");
define("LANGAJOUT1","Evit ar Bod ha Boued : dibaboù posupl (<b>INT</b> (Diabarzhad),<b>EXT</b> (Diavaezad), <b>DP</b> (Hanter diabarzhad)<br>
Evit al Latin  : dibaboù posupl (<b>LATIN</b> pe mann ebet)<br><br>");
define("LANGIMP44","Ar fichenn na glot ket.");
define("LANGBASE16"," Diskouezet eo ar bannoù dindan ar stumm : <b>anv lugañ ; raganv lugañ; ger-kuzh Kerent ; ger-kuzh Skoliad e sklaer</b>");


define("LANGSUPP0","Diverkañ ur gont Erlec'hier");
define("LANGSUPP1","Bloc'had Diverkañ");
define("LANGSUPP2","Diverkañ ar gont");
define("LANGSUPP3","C'hoant hoc'h eus diverkañ eus listenn an erlec'hierien");
define("LANGSUPP3bis","erlec'hier");
define("LANGSUPP4","Kadarnaat an diverkañ");
define("LANGSUPP5","Dibosupl diverkañ ar gont-mañ. \\n\\n Kont lakaet d'ur c'hlas.  \\n\\n  Skipailh Triade");
define("LANGSUPP6","Kont diverket - Skipailh Triade");
define("LANGSUPP7","Diverkañ ur strollad");
define("LANGSUPP8","Diverkañ ar strollad");
define("LANGSUPP9","Diverkañ ur gont ");
define("LANGSUPP10","Diverkañ ar gont");
define("LANGSUPP11","un ezel eus ar vuhez skol");
define("LANGSUPP12","ur merour");
define("LANGSUPP13","ar c'helenner");
define("LANGSUPP14","Diverkañ ur skoliad er c'hlas");
define("LANGSUPP15","Klikit war ar skolaid da dennañ");
define("LANGSUPP16","Diverkañ ur skoliad");
define("LANGSUPP17","a zo o vont da vezañ tennet eus an diaz");
define("LANGSUPP18","An holl ditouroù diwar-benn ar skoliad-mañ a zo o vont da vezañ diverket, da lavaret eo : <br> (notennoù, ezvezañsoù, daleoù, diskargoù, kastizoù, keleier, posteloù)");
define("LANGSUPP19","Nullañ an diverkañ");
define("LANGSUPP20","a zo tennet eus an diaz");
define("LANGSUPP21","Diverkañ ur c'hlas");
define("LANGSUPP22","Diverkañ ur c'hlas");
define("LANGSUPP23","Diverkañ un danvez pe un rann-danvez");
define("LANGSUPP24","Diverkañ an danvez");
define("LANGSUPP25","Klas diverket --  Servij Triade");
define("LANGSUPP26","Danvez diverket --  Servij Triade");
define("LANGSUPP27","Krouidigezh an danvez");
define("LANGSUPP28","Rann-danvez enrollet");

define("LANGADMIN","merour");
define("LANGPROF","Kelenner");
define("LANGSCOLAIRE","ar Vuhez Skol");
define("LANGCLASSE","ur c'hlas");


define("LANGGRP11","Anv ar strollad");
define("LANGGRP12","Klasad(où) orin");
define("LANGGRP13","Listenn ar Skolidi");
define("LANGGRP14","Listenn ar strolladoù");
define("LANGGRP15","Krouidigezh ur strollad");
define("LANGGRP16","Menegit skolidi ar strollad");
define("LANGGRP17","Dibab");
define("LANGGRP18","Enrollañ ar strollad");
define("LANGGRP19","Strollad krouet");
define("LANGGRP20","Strollad all");
define("LANGGRP21","Listenn ar strolladoù");
define("LANGGRP22","Menegiñ ur c'hlas evit krouiñ ar strollad marplij \\n\\n Skipailh Triade");
define("LANGGRP23","Listenn ar skolidi er strollad");
define("LANGGRP24","Listenn ar c'hlasoù");
define("LANGGRP25","Listenn an danvezioù");



//----------------//
define("LANGDONNEENR","<font class=T2>Titour(où) enrollet.</font>");

define("LANGABS47","Ouzhpennañ ur c'hastiz");
define("LANGABS48"," en (he) deus tizhet ");
define("LANGABS48bis","gwech ar rann");
define("LANGABS49","padelezh");
define("LANGABS50"," Dalc'h a_ ");
define("LANGABS51","Pgz Mich Tad ");
define("LANGABS52","Pgz Mich Mamm ");
define("LANGABS53","Dale pe ezvezañs ebet sinet");

define("LANGCALRET1","Deiziadur &nbsp; an &nbsp; Dalc'hoù");

define("LANGHISTO1","Istor an oberiadennoù");

define("LANGDST9","Ouzhpennañ un enmont");
define("LANGDST10","Diverkañ un enmont");
define("LANGDST11","e klas");

define("LANGDISP11","Skritelliñ <b>an holl</B> zispañsoù");

define("LANGEN","E");

define("LANGAFF4","Embann klasad");
define("LANGAFF5","An holl glasoù");
define("LANGAFF6","Sellout ouzh ar c'hlasad-mañ");

define("LANGCHER1","Enklask donn");
define("LANGCHER2","Menegiñ stumm ar fichenn da grouiñ");
define("LANGCHER3","Menegiñ an disranner maeziennoù");
define("LANGCHER4","Klask ur skoliad diwar an anv : <b>klikit amañ</b>");
define("LANGCHER5","Ouzhpennañ");
define("LANGCHER6","Tennañ");
define("LANGCHER7","Pignat");
define("LANGCHER8","Diskenn");
define("LANGCHER9","Heul");
define("LANGCHER10","Elfenn klasket");
define("LANGCHER11","Niver a zezverkoù enklask");
define("LANGCHER12","Adalek");

define("LANGCHER13","gant an dalvoudegezh");
define("LANGCHER14","Enklask wel-wazh");
define("LANGCHER15","Enklask resis");
define("LANGCHER16","Kregiñ an enklask");
define("LANGCHER17","Diwall ! chom a ra un elfenn nann dibabet !! -- Skipailh Triade ");

define("LANGCHER18","gant an dalvoudegezh");

define("LANGTITRE34","Kefluniañ ul lizher evit an daleoù");
define("LANGTITRE35","Kefluniañ ul lizher evit an ezvezañsoù");

define("LANGCONFIG1","Kefluniadur enrollet.");
define("LANGCONFIG2","Setu ho testenn ");

define("LANGCONFIG3","Menegiñ listenn ar gerent a resevo ul lizher");

define("LANGERROR01","Mankadenn moned d'an diaz");
define("LANGERROR02","DIWALL ! Dibosupl ! <br><br>Ar gudenn a c'hall dont eus an titouroù embarzhet <br>(Gwiriekaat ar maeziennoù disheñvel a-raok kadarnaat).<BR>  <BR>PE ez eo enrollet dija an titour PE n'hall ket bezañ tizhet.");
define("LANGERROR03","Dibosupl moned d'an diaz evit an oberiadenn-mañ. <BR><BR>Kaset ez eus bet ur postel war-eeun da emskoazell TRIADE.");

define("LANGABS54","a zo skrivet dija ezvezant d'an devezh-se");
define("LANGABS55","a zo skrivet dija diwezhat d'an devezh-se");
define("LANGPARAM4","Enrollet eo an testeni.");
define("LANGPARAM5","Testeni skol skolidi ar c'hlasad ");
define("LANGPARAM5bis","a zo prest, e stumm PDF");
define("LANGPARAM6","Arventennañ endalc'had ar follennoù trimiziad pe lazhiadek");

define("LANGPARAM7","Anv rener ar c'helenndi");
define("LANGPARAM8","Anv ar c'helenndi");
define("LANGPARAM9","Chomlec'h");
define("LANGPARAM10","Kod post");
define("LANGPARAM11","Ker");
define("LANGPARAM12","Pellgomz");
define("LANGPARAM13","Postel");
define("LANGPARAM14","Siell ar c'helenndi");
define("LANGPARAM15","Enrollañ an arventennoù");
define("LANGPARAM16","Enrolladenn graet -- Skipailh Triade");

define("LANGCERTIF1","Testeni skol ");
define("LANGCERTIF1bis","a zo prest, e stumm PDF");


define("LANGRECHE1","Titouroù diwar-benn ar skoliad");

define("LANGBT52","Kemm ar roadennoù");

define("LANGEDIT1","Roadennoù aet diwar-wel");

define("LANGMODIF1","Nevesadenn kont ur skoliad");
define("LANGMODIF2","Titouroù war ar skoliad");
define("LANGMODIF3","Titouroù war ar familh");

define("LANGALERT1","Roadennoù nevesaet -- Skipailh Triade");
define("LANGALERT2","Diwall, n'eo glot ket ar fichenn dre he stumm pe he ment");
define("LANGALERT3","Diwall, n'eo glot ket ar fichenn dre he stumm pe he ment");

define("LANGLOGO1","Siell da gas");
define("LANGLOGO2","Enrollañ ar siell");
define("LANGLOGO3","Ar siell <b>a rank bezañ e stumm jpg</b> ha gant ar ment 96px war 96px.");

define("LANGPARAM17","Bevenniñ an trimiziadoù pe ar c'hwec'hmiziadoù");
define("LANGPARAM18","Trimiziad pe C'hwec'hmiziad");
define("LANGPARAM19","Deiziad kregiñ");
define("LANGPARAM20","Deiziad echuiñ");
define("LANGPARAM21","Kentañ");
define("LANGPARAM22","Eil");
define("LANGPARAM23","Trede");
define("LANGPARAM24","Enrollañ deiziadoù an trimiziadoù");
define("LANGPARAM25","Roadenn dalc'het en kont, m'emañ an enrolladenn e stumm c'hwec'hmiziad");
define("LANGPARAM26","Deiziad direizh -- Skipailh Triade");
define("LANGPARAM27","Titouroù enrollet -- Skipailh Triade");
define("LANGPARAM28","trimiziad");
define("LANGPARAM29","c'hwec'hmiziad");
define("LANGPARAM30","Follenn");


define("LANGBULL5","Moullañ ar follenn");
define("LANGBULL6","Kenderc'hel an dreterezh");
define("LANGBULL7","Moullañ ar prantad");
define("LANGBULL8","Menegiñ penn-kentañ ar prantad");
define("LANGBULL9","Menegiñ fin ar prantad");
define("LANGBULL10","Menegiñ niverenn ar prantad");
define("LANGBULL11","Menegiñ ar rann");
define("LANGBULL12","Moullañ ar prantad");
define("LANGBULL13","Istorel");
define("LANGBULL14","<FONT COLOR='red'>DIWALL</FONT></B> Ezhomm a zo <B>Adobe Acrobat Reader</B>.  Poellad ha pellgargañ digoust ");
define("LANGBULL14bis","Pellgargañ");
define("LANGBULL15","Sellout / Diverkañ");
define("LANGBULL16","Anv ar skoliad");
define("LANGBULL17","Kelenner");
define("LANGBULL18","Munud an notennoù");
define("LANGBULL19","Evezhiadennoù ar penngelenner");
define("LANGBULL20","FOLLENN NOTENNOU");
define("LANGBULL21","prantad");

define("LANGBULL22","trimiziad kentañ");
define("LANGBULL23","eil trimiziad");
define("LANGBULL24","trede trimiziad");

define("LANGBULL25","kentañ c'hwec'hmiziad");
define("LANGBULL26","eil c'hwec'hmiziad");

define("LANGBULL27","Follenn an ");
define("LANGBULL28","Rann");
define("LANGBULL29","Bloavezh Skol");

define("LANGBULL30","FOLLENN");

define("LANGBULL31","Skoliad(ez)");
define("LANGBULL32","Danvezioù");
define("LANGBULL33","Klas");
define("LANGBULL34","Evezhiadennoù");

define("LANGBULL35","Kenef");
define("LANGBULL36","Keid");
define("LANGBULL37","Izel");
define("LANGBULL38","Uhel");
define("LANGBULL39","Aked hag emzalc'h : ");
define("LANGBULL40","Evezhiadenn hollek ar skipailh : ");
define("LANGBULL41","Eilenn ebet na vo roet");
define("LANGBULL42","Sinadur ar rener / Renerezh ar studioù");
define("LANGBULL43","BLOAVEZH SKOL");
define("LANGBULL44","Aot. & Itr.");
define("LANGOU","pe"); // le ou de ou bien


define("LANGPROJ19","C'hwec'hmiziad 1");
define("LANGPROJ20","C'hwec'hmiziad 2");

define("LANGDISC1","Dalc'h  a_ ");
define("LANGDISC2","Moullañ dalc'hoù an devezh");


define("LANGDISC3","Pgz Ker ");
define("LANGDISC4","Pgz Mich Tad ");
define("LANGDISC5","Pgz Mich Mamm ");
define("LANGDISC6","Lakaat ur c'hastiz e klas ");
define("LANGDISC7","Titl ar rann ");
define("LANGDISC8","Titl ar c'hastiz ");
define("LANGDISC9","Lakaet gant ");
define("LANGDISC10","Abeg, titouroù, labour d'ober ");
define("LANGDISC11","Dalc'h");
define("LANGDISC11bis","A_");  // Le pour indiquer une date
define("LANGDISC11Ter","Da");  // A pour indiquer une heure
define("LANGDISC12","padelezh");
define("LANGDISC13","<font color=red>G</font></B>evaskit al log ma \'z eo <br>dalc'het pe kastizet ar skoliad.");
define("LANGDISC14","Ouzhpennañ ur c'hastiz");
define("LANGDISC15","<B>*<I> D</B>: Pellgomz er ger, <B>P</B>: Pellgomz micherel an tad, <B>M</B>: Pellgomz micherel ar vamm</I>");
define("LANGDISC16","Seveniñ");
define("LANGDISC17","Pgz");
define("LANGDISC18","Gwelet ar c'hastizoù");
define("LANGDISC19","Gwelet ar <b>5</B> kastiz diwezhañ");
define("LANGDISC20","Rann");
define("LANGDISC21","Listenn glok ");
define("LANGDISC22","Gwelet dalc'hoù ");
define("LANGDISC23","Skritelliñ an dalc'hoù");
define("LANGDISC24","Diskwel  <b>klok</B> an dalc'hoù");
define("LANGDISC25","Dalc'het");
define("LANGDISC26","Dalc'h nann sevenet");
define("LANGDISC27","Sevel roll kastizoù ");
define("LANGDISC28","Diskwel ar c'hastizoù");
define("LANGDISC29","Diskwel <b>klok</B> ar c'hastizoù");
define("LANGDISC30","Lakaet&nbsp;d'a_");
define("LANGDISC31","Sevel roll kastizoù ");
define("LANGDISC32","Dalc'h nann deverket d'ur skoliad ");
define("LANGDISC33","DIWALL ! ar skoliad ");
define("LANGDISC33bis"," a zo dalc'het dija d'an deiz ha d'an eur meneget. ");
define("LANGDISC34","en (he) deus");
define("LANGDISC34bis","(g)wech ar rann");
define("LANGDISC35","Tennañ Kastiz");
define("LANGDISC36","Tennañ Dalc'h");

define("LANGattente222","Gortozit");



define("LANGSUPP","Diverk."); // abreviation de Diverkañ



define("LANGCIRCU1","Merañ ar C'helc'hlizheroù");
define("LANGCIRCU2","Ouzhpennañ ur c'helc'hlizher");
define("LANGCIRCU3","Renabliñ kelc'hlizheroù");
define("LANGCIRCU4","Diverkañ ur c'helc'hlizher");
define("LANGCIRCU5","Ouzhpennañ kelc'hlizheroù");
define("LANGCIRCU6","Pal");
define("LANGCIRCU7","Dave");
define("LANGCIRCU8","Kelc'hlizher");
define("LANGCIRCU9","Kelennerien");
define("LANGCIRCU10","Er c'hlas(où)");
define("LANGCIRCU11","<font face=Verdana size=1><B><font color=red>K</font></B>elc'hlizher <br />e stumm<b> doc</b>,<b>pdf</b>,<b>txt</b>.</FONT>");
define("LANGCIRCU12","<font face=Verdana size=1><B><font color=red>K</font></B>elc'hlizher gweladus gant ar<br> gelennerien.</FONT>");
define("LANGCIRCU13","Holl glasoù");
define("LANGCIRCU14","Distreiñ d'ar roll");
define("LANGCIRCU15","Enrollañ ar c'helc'hlizher");
define("LANGCIRCU16","Kelc'hlizher nann enrollet");
define("LANGCIRCU17","Rankout a ra ar fichenn bezañ e stumm <b>txt pe doc pe pdf</b> ha skañvoc'h eget 2 Mo ");
define("LANGCIRCU18","<font class=T2>Kelc'hlizher enrollet</font>");
define("LANGCIRCU19","Diverkañ Kelc'hlizheroù");
define("LANGCIRCU20","Moned fichenn");
define("LANGCIRCU21","<font color=red><b>D</b></font><font color=#000000>ave");

define("LANGCODEBAR1","Merañ ar c'hodoù-barr");
define("LANGCODEBAR2","N'ez a ket en-dro ar bloc'had-mañ gant ho servijer. <br> Ret eo deoc'h kaout PHP 5 pe muioc'h.");
define("LANGCODEBAR3","Setu roll kodoù-barr a c'haller kaout dre Triade");
define("LANGCODEBAR4","Ar c'hod-barr implijet dre ziouer a zo ");
define("LANGCODEBAR5","Listenn");


define("LANGPUB1","Ouzhpennañ ur banniel bruderezh");
define("LANGPUB2","C'hoant hoc'h eus da embann war lec'hienn TRIADE");
define("LANGPUB3","Ober ul lazhiad brudañ");
define("LANGPUB4","Evit se  ");
define("LANGPUB5","Kemenner oc'h dija war Triade ");

define("LANGPROFB1","Evezhiadennoù evit ar follennoù trimiziad");
define("LANGPROFB2","Arventennañ hoc'h evezhiadennoù");
define("LANGPROFB3","Arventennañ");
define("LANGPROFB4","Kefluniañ Evezhiadennoù Follennoù trimiziad");
define("LANGPROFB5","Enrollañ an evezhiadennoù");
define("LANGPROFB6","Evezhiadenn");
define("LANGPROFB7","Listenn");


define("LANGPROFC1","Deiziadur Implij Dafar");
define("LANGPROFC2","Deiziadur Implij Salioù");


define("LANGPARAM31","Gwelet e stumm U.S.A");
define("LANGPARAM32","Aked hag emzalc'h er c'helenndi : ");
define("LANGPARAM33","Adpakañ ar fichenn PDF");

define("LANGDISC37","Ouzhpennañ ur c'hastiz");

define("LANGPROFP4","<b>Penngelenner</b> e ");
define("LANGPROFP5","Titouroù war ar skoliad");
define("LANGPROFP6","Keleier a_ ");
define("LANGPROFP7","betek a_ ");

define("LANGPROFP8","Hollad an daleoù");
define("LANGPROFP9","Niver a zaleoù en trimiziad-mañ");
define("LANGPROFP10","Hollad an ezvezañsoù");
define("LANGPROFP11","Niver a ezvezañsoù en trimiziad-mañ");

define("LANGPROFP12","Merañ ar gannaded");
define("LANGPROFP13"," e klas ");
define("LANGPROFP14","Kannad tud ar vugale");
define("LANGPROFP15","Chomlec'h");
define("LANGPROFP16","Kannad skolidi");
define("LANGPROFP17","Kannad(ed) tud ar vugale");
define("LANGPROFP18","Kannad(ed) skolidi");
define("LANGPROFP19","Pgz"); // pour téléphone
define("LANGPROFP20","Postel");
define("LANGPROFP21","Titouroù yec'hed ouzhpenn ar skoliad");

define("LANGETUDE1","Merañ ar prantadoù-studi");
define("LANGETUDE2","Lec'hiañ ar skolidi er prantadoù-studi");
define("LANGETUDE3","Gwelet roll al lec'hiañ prantadoù-studi");
define("LANGETUDE4","Ouzhpennañ ur prantad-studi");
define("LANGETUDE5","Kemm ur prantad-studi");
define("LANGETUDE6","Diverkañ ur prantad-studi");
define("LANGETUDE7","Gwelet ur prantad-studi");
define("LANGETUDE8","Lec'hiañ ur skoliad en ur prantad-studi");
define("LANGETUDE9","Kemmañ ur skoliad en ur prantad-studi");
define("LANGETUDE10","Diverkañ ur skoliad en ur prantad-studi");
define("LANGETUDE11","Roll ar prantadoù-studi");

define("LANGETUDE12","Kasour");
define("LANGETUDE13","Studi");
define("LANGETUDE14","E sal");
define("LANGETUDE15","Sizhun");
define("LANGETUDE16","A_");  		// Le indique une date
define("LANGETUDE17","da");  		// à indique une heure
define("LANGETUDE18","e-pad");  	//indique une durée
define("LANGETUDE19","Krouiñ ur prantad-studi");
define("LANGETUDE20","Anv ar prantad-studi");
define("LANGETUDE21","Devezh ar sizhun");
define("LANGETUDE22","Eur ar prantad-studi");
define("LANGETUDE23","Padelezh ar prantad-studi");
define("LANGETUDE24","ee:mm");
define("LANGETUDE25","Sal studi");
define("LANGETUDE26","Kasour ar prantad-studi-mañ");
define("LANGETUDE27","Enrollet eo ar prantad-studi");
define("LANGETUDE28","Roll ar prantadoù-studi");
define("LANGETUDE29","Kemmañ ur prantad-studi");
define("LANGETUDE30","Skolidi a zo er prantad-studi-se. Diverkañ da gentañ roll ar skolidi a-raok diverkañ ar prantad-studi e-unan.");
define("LANGETUDE31","Roll skoliad");
define("LANGETUDE32","Roll ar skolidi");
define("LANGETUDE33","Lec'hiañ ur skoliad en ur prantad-studi");
define("LANGETUDE34","Dibab ar prantad-studi");
define("LANGETUDE35","Resisaat ar c'hlasadoù a-benn lec'hiañ ar skolidi er prantad-studi-mañ.");
define("LANGETUDE36","Anv ar prantad-studi");
define("LANGETUDE37","Resisaat ar skolidi er prantad-studi-mañ.");
define("LANGETUDE38","aotret da guitaat");
define("LANGETUDE39","Enrollañ ar prantad-studi");
define("LANGETUDE40","Prantad-studi all");
define("LANGETUDE41","Kemmañ prantad-studi ur skoliad");
define("LANGETUDE42","Skoliad war ur prantad-studi");
define("LANGETUDE43","Enrollañ ar c'hemmoù");
define("LANGETUDE44","Aotreet da mont e-maez");
define("LANGETUDE45","Diverkañ prantad-studi ur skoliad");

define("LANGLIST1","Embann ur c'hlas");
define("LANGLIST2","Listenn kelennerien ar c'hlas");
define("LANGLIST3","Penngelenner");
define("LANGLIST4","Deiziad");
define("LANGLIST5","Liste compléte au format PDF");
define("LANGLIST6","Penngelenner");


define("LANGPASS1","Ger-tremen nevez");

define("LANGTRONBI1","Gwelout ar boltredaoueg");
define("LANGTRONBI2","Kemmañ ar boltredaoueg");
define("LANGTRONBI3","Diwall, fichennaoueg direizh he ment");
define("LANGTRONBI4","Diwall, skeudenn direizh he ment");
define("LANGTRONBI5","Anv-familh ar skoliad");
define("LANGTRONBI6","Raganv ar skoliad");
define("LANGTRONBI7","ar skeudenn");
define("LANGTRONBI8","Ouzhpennañ ur skeudenn");


define("LANGBASE19","Le fichier sélectionné n'est pas valide");
define("LANGBASE20","Skoliad diglasad");
define("LANGBASE21","Niver a skolidi diglasad");
define("LANGBASE22","Skritellañ an 30 kentañ");
define("LANGBASE23","Kemm klasad evit ar skolidi");
define("LANGBASE24","Kemmadenn echuet");
define("LANGBASE25","A-RAOK KEMMAÑ TRA PE DRA LENN HOR SKOAZELL");
define("LANGBASE26","Kemm klasad evit skolidi ar c'hlasad");
define("LANGBASE27","Kelaouerezh diwar-benn kemm klasad ur skoliad");
define("LANGBASE28","<b>Kemm ebet.</b> <i>(Gant an dibab 'choix ...')</i>");
define("LANGBASE29","N'eus bet tennet tamm keloù ebet diwar-benn ar skoliad.");
define("LANGBASE30","<b>Kemmañ klasad.</b> <i>(o verkañ ur c'hlasad.)</i>");
define("LANGBASE31","Diverkañ notennoù, ezvezañsoù, daleoù, evezhiadennoù emzalc'h, diskargoù ar skoliad.");
define("LANGBASE32","<b>A ya kuit eus ar skol.</b>  <i>(Gant dibab 'a ya kuit eus ar skol')</i>");
define("LANGBASE33","Diverkañ ar skoliad diouzh an diaz.");
define("LANGBASE34","Diverkañ notennoù, ezvezañsoù, daleoù, evezhiadennoù emzalc'h, diskargoù ar skoliad.");
define("LANGBASE35","Diverkañ kemennadennoù diabarzh ar familh.");
define("LANGBASE36","A ya e... klas");
define("LANGBASE37","A guita ar skol");
define("LANGBASE38","Kadarnaat ar c'hemm");
define("LANGBASE39","Dibabit un elfenn");


// new
define("LANGBASE40","Choaz...");

define("LANGAGENDA1","Diwall !!! Mont a ra an notenn emaoc'h o paouez krouiñ pe kemmañ war-c'horre un notenn all evit an implijerien-mañ");
define("LANGAGENDA2","Ha c'hoant hoc'h eus da ziverkañ an notenn-se a zo bet lakaet deoc'h ?");
define("LANGAGENDA3","Diverkañ un notenn, degas da soñj :\\n\\n - An holl zegouezhioù stag ouzh an notenn-se a vo diverket ivez\\n - Evit diverkañ un degouezh hepken klikañ war ar skeudenn a zere a-zehou d'an notenn e-barzh an implijoù-amzer\\n\\nHa c'hoant hoc'h eus da ziverkañ an notenn-mañ ?");
define("LANGAGENDA4","Diverkañ un degouezh, degas da soñj :\\n\\n - Ne vo diverket nemet an degouezh-se\\n - Evit diverkañ un notenn a zeu meur a wech hag hec'h holl zegouezhioù klikañ war ar groaz a-zehou d'an notenn e-barzh an implijoù-amzer pe embannit an notenn ha klikit war ar bouton [Diverkañ]\\n\\nHa c'hoant hoc'h eus da ziverkañ an degouezh-mañ ?");
define("LANGAGENDA5","Notenn gant addegas da soñj");
define("LANGAGENDA6","Diverkañ un degouezh");
define("LANGAGENDA7","Diverkañ un notenn");
define("LANGAGENDA8","Perc'hennañ un notenn");
define("LANGAGENDA9","Lakaat war-wel ar munudoù");
define("LANGAGENDA10","Notenn bersonel");
define("LANGAGENDA11","Notenn lakaet");
define("LANGAGENDA12","Notenn Vev");
define("LANGAGENDA13","Notenn Echuet");
define("LANGAGENDA14","Deiz a hiziv");
define("LANGAGENDA15","Deizioù dilabour");
define("LANGAGENDA16","Krouiñ un notenn");
define("LANGAGENDA17","Klikañ evit kemmañ");
define("LANGAGENDA18","Enrollañ deiziad un deiz-ha-bloaz");
define("LANGAGENDA19","Kemmañ deiziad un deiz-ha-bloaz");
define("LANGAGENDA20","Skrivit anv an den");
define("LANGAGENDA21","Skrivit deiziad ganedigezh an den");
define("LANGAGENDA22","Deiz-ha-bloaz...");
define("LANGAGENDA23","Deiziad ganedigezh");
define("LANGAGENDA24","Ment jj/mm/aaaa");
define("LANGAGENDA25","Diverkañ an deiz-ha-bloaz-mañ ?");
define("LANGAGENDA26","Diverkañ");
define("LANGAGENDA27","Nullañ");
define("LANGAGENDA28","Enrollañ");
define("LANGAGENDA29","Sur oc'h da gaout c'hoant da ziverkañ an deiz-ha-bloaz-mañ ?");
define("LANGAGENDA30","Kemmañ");
define("LANGAGENDA31","Bloaz a-raok");
define("LANGAGENDA32","Miz a-raok");
define("LANGAGENDA33","Mont betek deiziad an deiz");
define("LANGAGENDA34","Derc'hel evit ar roll danvezioù");
define("LANGAGENDA35","Miz war-lerc'h");
define("LANGAGENDA36","Bloaz war-lerc'h");
define("LANGAGENDA37","Dibab un deiziad");
define("LANGAGENDA38","Dilec'hiañ");
define("LANGAGENDA39","Hiziv");
define("LANGAGENDA40","Diwar-benn an deiziataer");
define("LANGAGENDA41","Lakaat war-wel da gentañ");
define("LANGAGENDA42","Serriñ");
define("LANGAGENDA43","Klikañ pe lakaat riklañ evit kemmañ an dalvoudegezh");
define("LANGAGENDA44","Implijer dianav");
define("LANGAGENDA45","Echuet eo ho koulzad; !");
define("LANGAGENDA46","Ger-kuzh implijet dija");
define("LANGAGENDA47","Ger-tremen kozh faziek");
define("LANGAGENDA48","Roit hoc'h anvadur evit implijout Phenix");
define("LANGAGENDA49","N'eo ket deuet a-benn da gevreañ ouzh ar servijer");
define("LANGAGENDA50","Neuz kemmet");
define("LANGAGENDA51","Notenn enrollet");
define("LANGAGENDA52","Notenn nevezet");
define("LANGAGENDA53","Notenn diverket");
define("LANGAGENDA54","Degouezh an notenn diverket");
define("LANGAGENDA55","Deiz-ha-bloaz enrollet");
define("LANGAGENDA56","Deiz-ha-bloaz nevezet");
define("LANGAGENDA57","Deiz-ha-bloaz diverket");
define("LANGAGENDA58","Kont savet bez' e c'hellit kevreañ");
define("LANGAGENDA59","C'hwitet eo an enrolladenn");
define("LANGAGENDA60","Holl dachennoù");
define("LANGAGENDA61","Embregerezh");
define("LANGAGENDA62","Anv + raganv");
define("LANGAGENDA63","Chomlec'h");
define("LANGAGENDA64","Niverenn bellgomz");
define("LANGAGENDA65","Chomlec'h postel");
define("LANGAGENDA66","Evezhiadennoù");
define("LANGAGENDA67","Kregiñ gant an enklask");
define("LANGAGENDA68","Embregerezh");
define("LANGAGENDA69","Anv-familh");
define("LANGAGENDA70","Raganv");
define("LANGAGENDA71","Chomlec'h");
define("LANGAGENDA72","Kêr");
define("LANGAGENDA73","Bro");
define("LANGAGENDA74","Pgz er gêr");
define("LANGAGENDA75","Pgz labour");
define("LANGAGENDA76","Pgz hezoug");
define("LANGAGENDA77","Pelleiler");
define("LANGAGENDA78","Postel");
define("LANGAGENDA79","Postel micher");
define("LANGAGENDA80","Notenn / A bep seurt");
define("LANGAGENDA81","Strollad");
define("LANGAGENDA82","Dasparzh");
define("LANGAGENDA83","Kod post");
define("LANGAGENDA84","Deiziad ganedigezh");
define("LANGAGENDA85","Adkregiñ");
define("LANGAGENDA86","Enporzhiañ");
define("LANGAGENDA87","Enporzhiadur kaset da benn");
define("LANGAGENDA88","Darempredad ouzhpennet");
define("LANGAGENDA89","Tamm darempredad ebet !");
define("LANGAGENDA90","<LI>E-barzh Outlook, ober <I>Fichier</I>-&gt;<I>Ezporzhiañ</I>-&gt;<I>Karned chomlec'hioù all...</I></LI>");
define("LANGAGENDA91","<LI>Dibab <I>Fichennaoueg testenn (talvoudoù disrannet gant virgulennoù)</I> goude <I>ezporzhiañ</I></LI>");
define("LANGAGENDA92","<LI>Dibab al lec'h ma vo miret ar fichennaoueg; goude <I>Hini war-lerc'h</I></LI>");
define("LANGAGENDA93","<LI>E roll an tachennoù; ezporzhiañ; dibab :<BR>");
define("LANGAGENDA94","<I>Raganv, anv-familh, postel, Straed, Kêr, Kod Post, Bro/Rannvro, pellgomz er gêr, Pellgomz hezoug, Pellgomz labour, pelleiler labour, Embregerezh</I> neuze klikañ war <I>Echuiñ</I></LI>");
define("LANGAGENDA95","<LI>Adtapout ar fichennaoueg krouet er follenn a-is ha klikañ war <I>Enporzhiañ</I></LI>");
define("LANGAGENDA96","Skrivit anv un embregerezh evit an enklask");
define("LANGAGENDA97","Lakait un anv-familh pe ur raganv evit an enklask");
define("LANGAGENDA98","Lakait ur chomlec'h evit an enklask");
define("LANGAGENDA99","Lakait un niverenn bellgomz evit an enklask");
define("LANGAGENDA100","Lakait ur chomlec'h evit an enklask");
define("LANGAGENDA101","Lakait un darn eus un evezhiadenn evit an enklask");
define("LANGAGENDA102","Lakait da nebeutañ un dezverk evit an enklask");
define("LANGAGENDA103","Ha sur oc'h da gaout c'hoant da ziverkañ  an darempredad-se ?");
define("LANGAGENDA104","Bloavezh");
define("LANGAGENDA105","Tad ebet");
define("LANGAGENDA106","Roll an dud a c'hellit lakaat pep a notenn dezho");
define("LANGAGENDA107","Tud posupl");
define("LANGAGENDA108","Den pe tud dibabet");
define("LANGAGENDA109","Resisted ar skritellañ");
define("LANGAGENDA110","Darn 30 mn");
define("LANGAGENDA111","Darn 15 mn");
define("LANGAGENDA112","Eur an deroù");
define("LANGAGENDA113","Eur an dibenn");
define("LANGAGENDA114","Dalc'het");
define("LANGAGENDA115","Darnel");
define("LANGAGENDA116","Dieub");
define("LANGAGENDA117","Krouiñ un notenn etre ");
define("LANGAGENDA118","Munudoù an devezh dre implijer");
define("LANGAGENDA119","Skritellañ");
define("LANGAGENDA120","Dibabit un den");
define("LANGAGENDA121","Dibabit un eur dibenn diwezhatoc'h eget eur an deroù");
define("LANGAGENDA122","Sizhun eus ");
define("LANGAGENDA123","betek");
define("LANGAGENDA124","Sizhun war-lerc'h");
define("LANGAGENDA125","Tennañ");
define("LANGAGENDA126","Prantadoù vak ho tarempredidi evit ");
define("LANGAGENDA127","Ouzhpennañ");
define("LANGAGENDA128","Disneuz");
define("LANGAGENDA129","Dibabit un eur dibenn war-lerc'h eur an deroù");
define("LANGAGENDA130","Resisted skritellañ");
define("LANGAGENDA131","Skrivit un anv");
define("LANGAGENDA132","Skrivit un URL");
define("LANGAGENDA133","Ouzhpennit  ur postel implijetañ");
define("LANGAGENDA134","Aliet da lakaat moulañ a-blaen");
define("LANGAGENDA135","Sizhun a-raok");
define("LANGAGENDA136","Sizhun ");
define("LANGAGENDA137","a");
define("LANGAGENDA138","Deiz-ha-bloaz");
define("LANGAGENDA139","Addegas da soñj dre ziouer pa lakaer un notenn");
define("LANGAGENDA140","Hep addegas da soñj");
define("LANGAGENDA141","Addegas da soñj");
define("LANGAGENDA142","eilenn dre bostel");
define("LANGAGENDA143","munutenn(où)");
define("LANGAGENDA144","eur(ioù)");
define("LANGAGENDA145","deiz(ioù)");
define("LANGAGENDA146","devezh");
define("LANGAGENDA147","Echuet");
define("LANGAGENDA148","Pellgomz");
define("LANGAGENDA149","Etrefas");
define("LANGAGENDA150","Implij-amzer dre ziouer");
define("LANGAGENDA151","Pemdeziek");
define("LANGAGENDA152","Sizhuniek");
define("LANGAGENDA153","Miziek");
define("LANGAGENDA154","30 munutenn");
define("LANGAGENDA155","15 munutenn");
define("LANGAGENDA156","45 munutenn");
define("LANGAGENDA157","1 eurvezh");
define("LANGAGENDA158","Dibab aotomatikel eur dibenn un notenn");
define("LANGAGENDA159","Dasparzh an implij-amzer o vezañ lennet");
define("LANGAGENDA160","Tud aotreet da welout va implij-amzer");
define("LANGAGENDA161","N'eo ket rannet");
define("LANGAGENDA162","Au choix");
define("LANGAGENDA163","Tout an dud");
define("LANGAGENDA164","Implij amzer<BR>o vezañ kemmet");
define("LANGAGENDA165","Kelenner a chell lakaat un notenn");
define("LANGAGENDA166","Kelaouiñ achanon dre bostel pa vez lakaet un notenn din");
define("LANGAGENDA167","Diverkañ an notenn zo bet lakaet ganin");
define("LANGAGENDA168","Diverkañ an notenn zo bet lakaet din");
define("LANGAGENDA169","Asantiñ dan notenn zo bet lakaet din");
define("LANGAGENDA170","An devezh a-bezh");
define("LANGAGENDA171","Dibab an titl");
define("LANGAGENDA172","Titl nevez");
define("LANGAGENDA173","Anv");
define("LANGAGENDA174","Pad keitat");
define("LANGAGENDA175","Liv");
define("LANGAGENDA176","Neuz an notenn");
define("LANGAGENDA177","Diverkañ an titl-mañ ?");
define("LANGAGENDA178","Enrollañ ur memo");
define("LANGAGENDA179","Dibabit un titl");
define("LANGAGENDA180","Titl");
define("LANGAGENDA181","Danvez");
define("LANGAGENDA182","Ha fellout a ra deoch diverkañ ar memo-mañ ?");
define("LANGAGENDA183","Enrollañ un notenn");
define("LANGAGENDA184","An notenn ho peus choant diverkañ");
define("LANGAGENDA185","Fellout a ra deoch kemmañ ar rummad a-bezh pe an titour-mañ nemetken ?");
define("LANGAGENDA186","Ar rummad a-bezh");
define("LANGAGENDA187","An titour-mañ hepken");
define("LANGAGENDA188","Evezhiadenn war an devezh-pad");
define("LANGAGENDA189","Lakaat war-wel an deiziataer");
define("LANGAGENDA190","An devezh a-bezh");
define("LANGAGENDA191","Penn kentañ da");  // Début à
define("LANGAGENDA192","Evit<BR>");
define("LANGAGENDA193","Neuz an notenn");
define("LANGAGENDA194","Notenn bublik");
define("LANGAGENDA195","Notenn dre ar munud e rannadur an implij amzer");
define("LANGAGENDA196","menegad dalchet en implij amzer");
define("LANGAGENDA197","notenn brevez");
define("LANGAGENDA198","dalchet");
define("LANGAGENDA199","neo ket vak");
define("LANGAGENDA200","Vak");
define("LANGAGENDA201","lakaet da vak");
define("LANGAGENDA202","Liv");
define("LANGAGENDA203","Rannadur");
define("LANGAGENDA204","Vakter");
define("LANGAGENDA205","Adchalv");
define("LANGAGENDA206","Adchalv evet");
define("LANGAGENDA207","Eilskouerenn dre bostel");
define("LANGAGENDA208","En a-raok");  // à l'avance
define("LANGAGENDA209","Mareadegezh");
define("LANGAGENDA210","Hini ebet");
define("LANGAGENDA211","Pemdeziek");
define("LANGAGENDA212","Sizhuniek");
define("LANGAGENDA213","Miziek");
define("LANGAGENDA214","Bloaziek");
define("LANGAGENDA215","An holl");
define("LANGAGENDA215bis","devezhioù");
define("LANGAGENDA216","Tout an devezhioù skol");
define("LANGAGENDA217","holl zevezhioù ar sizhun skouer");
define("LANGAGENDA218","Ne vo ket enrollet an titouroù.Ha fellout a ra deoch kenderchel ?");
define("LANGAGENDA219","neuz");
define("LANGAGENDA220","An holl");
define("LANGAGENDA221","An holl");
define("LANGAGENDA221bis","Sizhunvezhioù");
define("LANGAGENDA222","Eus pep miz");
define("LANGAGENDA223","kentañ");
define("LANGAGENDA224","eil");
define("LANGAGENDA225","trede");
define("LANGAGENDA226","pevare");
define("LANGAGENDA227","diwezhañ");
define("LANGAGENDA228","eus ar miz");
define("LANGAGENDA229","A.");
define("LANGAGENDA230","Termeniñ deiziad ar fin");
define("LANGAGENDA231","Fin war-lerch"); // Fin après
define("LANGAGENDA232","Fin d'a.");
define("LANGAGENDA233","titour");
define("LANGAGENDA234","Lakait un titl");
define("LANGAGENDA235","Lakait un deiziad");
define("LANGAGENDA236","Dibabit eur ar fin war-lerch ar penn kentañ");  // \\n signifie un retour chariot
define("LANGAGENDA237","Dibabit un den");
define("LANGAGENDA238","Dibabit un niver a zevezhioù par da 1 pe brasoch eget unan");
define("LANGAGENDA239","Dibabit un niver a titouroù par da 1 pe brasoch eget unan");
define("LANGAGENDA240","adlavaradenn"); // répétition
define("LANGAGENDA241","Lakait hoch anv hag ho raganv da gentañ");
define("LANGAGENDA242","Lakait ho raganv");
define("LANGAGENDA243","Lakait ho login");
define("LANGAGENDA244","Lakait ho ker-tremen kozh");
define("LANGAGENDA245","Ger-tremen disheñvel");
define("LANGAGENDA246","Ret eo kaout ur ger-tremen");
define("LANGAGENDA247","Dibabit eur ar fin war-lerch eur ar penn kentañ");
define("LANGAGENDA248","Diverkañ an titour-mañ");
define("LANGAGENDA249","Notenn a zeu meur a wech");
define("LANGAGENDA250","Diverkañ an notenn-mañ zo bet lakaet ganin");
define("LANGAGENDA251","Asantiñ dan notenn zo bet lakaet din");
define("LANGAGENDA252","Silañ");
define("LANGAGENDA253","Moulañ an implij-amzer");
define("LANGAGENDA254","Moulañ a-blaen kuzuliet");
define("LANGAGENDA255","Notenn lakaet gant");
define("LANGAGENDA256","Kemmañ statud");
define("LANGAGENDA257","Diverkañ an titour-mañ");
define("LANGAGENDA258","Diverkañ an notenn-mañ a zo bet lakaet ganin");
define("LANGAGENDA259","Diverkañ an notenn-mañ a zo bet lakaet din");
define("LANGAGENDA260","un notenn");
define("LANGAGENDA261","un deiz-ha-bloaz");
define("LANGAGENDA262","un darempredad");
define("LANGAGENDA263","Dan implijer dibabet amañ dindan");
define("LANGAGENDA264","Ouzhpennañ un notenn");
define("LANGAGENDA265","Klask");
define("LANGAGENDA266","Vakter");
define("LANGAGENDA267","Darempred");
define("LANGAGENDA268","Memo");
define("LANGAGENDA269","Anv");
define("LANGAGENDA270","An implijetañ");
define("LANGAGENDA271","Neuz");
define("LANGAGENDA272","Ezporzhiañ chwitet");
define("LANGAGENDA273","Deiziadur eus ");


define("LANGL","L");  // L de lundi
define("LANGM","M");  // M de mardi
define("LANGME","M");  // M de mercredi
define("LANGJ","Y");  // J de jeudi
define("LANGV","G");  // V de vendredi
define("LANGS","S");  // S de samedi
define("LANGD","S");  // D de dimanche

define("LANGL1","Lun"); // Jours sur 3 lettres
define("LANGM1","Mzh");	// Jours sur 3 lettres
define("LANGME1","Mer"); // Jours sur 3 lettres
define("LANGJ1","Yao");	// Jours sur 3 lettres
define("LANGV1","Gwe");	// Jours sur 3 lettres
define("LANGS1","Sad");	// Jours sur 3 lettres
define("LANGD1","Sul");	// Jours sur 3 lettres

define("LANGMOIS21","Genv");			// mois abregé
define("LANGMOIS22","C'hw"); 		// mois abregé
define("LANGMOIS23","Mrzh");			// mois abregé
define("LANGMOIS24","Ebr");				// mois abregé
define("LANGMOIS25","Mae");				// mois abregé
define("LANGMOIS26","Mezh");			// mois abregé
define("LANGMOIS27","Goue");			// mois abregé
define("LANGMOIS28","Eost");		// mois abregé
define("LANGMOIS29","Gwen");			// mois abregé
define("LANGMOIS210","Here");			// mois abregé
define("LANGMOIS211","Du"); 			// mois abregé
define("LANGMOIS212","Krzu"); 	// mois abregé



define("LANGPROFP22","Ar chelenner-mañ zo dija penngelenner");



define("LANGSTAGE23","Anv an obererezh");
define("LANGSTAGE24","Enrollañ un embregerezh nevez");
define("LANGSTAGE25","Anv an embregerezh-mañ zo enrollet dija");
define("LANGSTAGE26","Anv an embregerezh");
define("LANGSTAGE27","Darempred");
define("LANGSTAGE28","Chomlec'h");
define("LANGSTAGE29","Kod post");
define("LANGSTAGE30","Kêr");
define("LANGSTAGE31","Gennad obererezh");
define("LANGSTAGE32","ouzhpennañ un obererezh");
define("LANGSTAGE33","Obererezh pennañ");
define("LANGSTAGE34","Pellgomz");
define("LANGSTAGE35","Pelleiler");
define("LANGSTAGE36","Postel");
define("LANGSTAGE37","Titouroù");
define("LANGSTAGE38","Sellout ouzh an embregerezhioù");
define("LANGSTAGE39","Kevredigezh");
define("LANGSTAGE40","Obererezh pennañ");
define("LANGSTAGE41","enklask all");
define("LANGSTAGE42","Pgz / Plr");
define("LANGSTAGE43","Embregerezh ebet gant an anv-se");
define("LANGSTAGE44","Implij amzer ar stajoù");
define("LANGSTAGE45","Deiziad penn kentañ ar staj");
define("LANGSTAGE46","Deiziad fin ar staj");
define("LANGSTAGE47","Enrollañ ar staj");
define("LANGSTAGE48","Niverenn ar staj");
define("LANGSTAGE49","Kemmañ deiziadoù ar staj");
define("LANGSTAGE50","Staj");
define("LANGSTAGE51","Deiziad ar staj");
define("LANGSTAGE52","Fazi skriverezañ");
define("LANGSTAGE53","Staj renevezet");
define("LANGSTAGE54","Staj a. ");
define("LANGSTAGE55","evit klasad");
define("LANGSTAGE56","zo enrollet");
define("LANGSTAGE57","Deiziad ar staj dilamet");
define("LANGSTAGE58","Embregerezh enrollet");
define("LANGSTAGE59","Kemmañ embregerezh");
define("LANGSTAGE60","Sellout ouzh an embregerezhioù dre rummadoù micher");
define("LANGSTAGE61","Klask un embregerezh");
define("LANGSTAGE62","Keleier");
define("LANGSTAGE63","Listenn glok");
define("LANGSTAGE64","Sellout ouzh deiziadoù ar staj");
define("LANGSTAGE65","Dilemel un embregerezh");
define("LANGSTAGE66","Embregerezh dilamet");
define("LANGSTAGE67","Sellout ouzh an embregerezhioù dre rummadoù micher");
define("LANGSTAGE68","Embregerezh ebet gant an anv-mañ");
define("LANGSTAGE69","Sellout ouzh ur skoliad da-geñver ur staj");
define("LANGSTAGE70","Moulañ ar staj niverenn");
define("LANGSTAGE71","Sellout ouzh ur skolajiad er stajoù");
define("LANGSTAGE72","Deiziad ar staj"); // respecter les &nbsp;
define("LANGSTAGE73","Distro");
define("LANGSTAGE74","Embregerezh");
define("LANGSTAGE75","Lakaat ur skolajiad en ur staj");
define("LANGSTAGE76","Lec'h ar staj");
define("LANGSTAGE77","Den e karg");
define("LANGSTAGE78","Kelenner gweladenner");
define("LANGSTAGE79","Bod");
define("LANGSTAGE80","Boued");
define("LANGSTAGE81","tremen dre meur a servij");
define("LANGSTAGE82","Abeg cheñchamant servij");
define("LANGSTAGE83","Titouroù ouzhpenn");
define("LANGSTAGE84","Enrollet \\n \\n L'Skipailh Triade");
define("LANGSTAGE85","Deiziad ar weladenn");
define("LANGSTAGE86","Kemmañ ur skoliad en ur staj");
define("LANGSTAGE87","Titouroù enrollet");
define("LANGSTAGE88","Dilemel ur skolajiad diouzh ur staj");


define("LANGRESA62","Anv");
define("LANGRESA63","Nac'hañ");
define("LANGRESA64","Ouzhpennañ ur goulenn");
define("LANGRESA65","Eus Da");
define("LANGRESA66","Miret");
define("LANGRESA66bis","gant");  // suite réservé par
define("LANGRESA67","Nann kadarnaet");
define("LANGRESA68","Kadarnaet");
define("LANGRESA69","Enrolladenn echuet");
define("LANGRESA70","mirout a-benn");






define("LANGNOTEUSA1","Lakaat notennoù mod USA");
define("LANGNOTEUSA2","Gallout a rit lechiañ al lizherennoù e-keñver an dregantad roet da pep notenn.");
define("LANGNOTEUSA3","Skouer : Eus 95 da 100 --> A+ , eus 87 da 94  --> A, etc...");
define("LANGNOTEUSA4","Eus");
define("LANGNOTEUSA4bis","da");
define("LANGNOTEUSA4ter","par da");   //  ex : De  10 à 20 équivaut à B
define("LANGNOTEUSA5","Etre an notenn");
define("LANGNOTEUSA5bis","hag an notenn");
define("LANGNOTEUSA5ter","par eo da");



define("LANGABS56","Listenn an ezvezañsoù diabeg");
define("LANGABS57","listenn ar skolajidi-mañ nevezet");




define("LANGSANC1","Kastiz krouet -- L'Skipailh Triade");
define("LANGSANC2","Rummad nann dilamet. Klotañ a ra ar rummad-mañ dija gant ur chastiz pe ur skoliad -- Skipailh Triade");
define("LANGSANC3","Aozadur an danvezioù");
define("LANGSANC4","Enrollañ ar rummadoù.");
define("LANGSANC5","Anv ar rummad");
define("LANGSANC6","Enrollañ anvioù ar chastizoù dre rummad.");
define("LANGSANC7","Anv ar chastiz");
define("LANGSANC8","Aozadur dibabet");
define("LANGSANC9","Kemennadenn war-wel pa zegouezh ar skoliad gant bevenn an aotreoù.");
define("LANGSANC10","Evit ar rummad");
define("LANGSANC11","Kemennadenn adalek");
define("LANGSANC12","Niver a wechoù");
define("LANGSANC13","Krouet gant");
define("LANGSANC14","Deiziad ebarzhiñ");

define("LANGMODIF4","Kemmañ ur gont");
define("LANGMODIF5","Keleier kevreañ");
define("LANGMODIF6","Skeudenn");
define("LANGMODIF7","Doareoù ar gont");
define("LANGMODIF8","Chomlec'h");
define("LANGMODIF9","Kod post");
define("LANGMODIF10","Kumun");
define("LANGMODIF11","Pgz");
define("LANGMODIF12","Postel");
define("LANGMODIF13","Kemmañ ar gont");
define("LANGMODIF14","Kont kemmet -- Skipailh Triade");
define("LANGMODIF15","Ger-tremen ");
define("LANGMODIF15bis"," zo bet kemmet.");
define("LANGMODIF16","Kemm ar ger-tremen");
define("LANGMODIF17","Neo ket reizh ment al luchskeudenn");
define("LANGMODIF18","Adnevesaat al luchskeudenn");
define("LANGMODIF19","Ouzhpennañ al luchskeudenn");
define("LANGMODIF20","Kemmañ al luchskeudenn");

define("LANGGRP25","Merañ ar strolladoù");
define("LANGGRP26","Listenn ar strolladoù");
define("LANGGRP27","Ouzhpennañ ur skoliad en ur strollad");
define("LANGGRP28","Diverkañ ur skoliad eus ur strollad");
define("LANGGRP29","Anv ar strollad");
define("LANGGRP30","klas");
define("LANGGRP31","Kemmañ al listenn");
define("LANGGRP32","Ouzhpennañ skolidi er strollad");
define("LANGGRP33","Ouzhpennañ ur skoliad er strollad-mañ");
define("LANGGRP34","Skoliad e ");
define("LANGGRP35","Skoliad er strollad");
define("LANGGRP36","Kadarnaat ar strollad");
define("LANGGRP37","Strollad kemmet -- Skipailh Triade ");
define("LANGGRP38","Listenn skolidi ar strollad ");
define("LANGGRP39","Skoliad ebet er strollad-mañ");

define("LANGCARNET1","Karned notennoù");
define("LANGCARNET2","Lakait klas ar skoliad");
define("LANGCARNET3","Klikit war anv ar skoliad");

define("LANGPASSG1","Rankout a ra ar ger-tremen bezañ 8 arouezenn dan nebeutañ gant lizherennoù bras ha bihan");
define("LANGPASSG2","N'eo ket mat ar ger-tremen. Rankout a ra ar ger-tremen bezañ 8 arouezenn dan nebeutañ gant lizherennoù bras ha bihan \\n\\n L\\'Skipailh Triade");
define("LANGPASSG3","C'hwitet");



define("LANGDISC38","Ouzhpennañ ur chastiz");
define("LANGDISC39","Merañ an danvezioù");
define("LANGDISC40","Dalch kastiz neo ket bet graet.");
define("LANGDISC41","Implij amzer an dalchioù-kastiz.");
define("LANGDISC42","An dalch-kastiz neo ket bet roet dur skoliad.");
define("LANGDISC43","Kenaozadur.");
define("LANGDISC44","Diverkañ an dalc'hioù-kastiz hag ar chastizoù");
define("LANGDISC45","Diverkañ ar chastizoù");
define("LANGDISC46","Listenn ezvezansoù ha daleoù ur chlasad");
define("LANGDISC47","Menegit penn kentañ ar mare");
define("LANGDISC48","Menegit fin ar mare");
define("LANGDISC49","Menegit ar rummad");
define("LANGDISC50","<br><ul>Dilemel an dalchioù-kastiz pe kastizoù diouzh an hed etre an deiziadoù.</ul>");
define("LANGDISC51","Tout ar chlasadoù");
define("LANGDISC52","Kastizoù dilamet");
define("LANGDISC53","Fazi ! neo ket  bet dilamet ar chastizoù nag an dalc'hioù-kastiz");

define("LANGIMP53","Fichennaoueg ASCII dre SQ");


// autre new

define("LANGSTAGE31bis","Eil gennad");
define("LANGSTAGE31ter","Trede gennad");
define("LANGMEDIC1","Teuliad mezegiezh ur skoliad");
define("LANGMEDIC2","Kas an enklask");
define("LANGMEDIC3","Keleier / Kemmañ");


define("LANGDISC54","Sellout ouzh danvezioù ur skoliad");
define("LANGDISC55","Diverkañ ur chastiz");
define("LANGDISC56","Diverkañ ur chastiz");

define("LANGBASE6bis","Hollad ar skolidi er fichennaoueg ");

define("LANGMODIF21","Rankout a ra ar ger-tremen bezañ 8 arouezenn bras ha bihan ennañ dan nebeutañ.\\n\\n Skipailh Triade");

define("LANGMODIF22","Ger-tremen: 8 arouezenn : niverennoù, lizherennoù bras ha bihan");
define("LANGPASS1bis","Kadarnaat ar ger-tremen");

define("LANGMODIF23","Gallout a rit kemmañ ho ker-tremen evit ho kont Triade");
define("LANGMODIF24","Ar gont ");
define("LANGMODIF24bis","zo o vezañ kadarnaet.");
define("LANGMODIF24ter","zo mat da vezañ implijet");
define("LANGMODIF25","Ger-tremen direizh \\n\\n Skipailh Triade");

define("LANGABS58","Sellout / diverkañ ezvezañs pe dale");
define("LANGABS59","Lakaat war-wel an holl zaleoù");
define("LANGABS60","E-pad");  	// une durée pendant temps de temps
define("LANGABS61","Sellout / kemmañ un ezvezañs pe un dale");
define("LANGABS62","lakaat war-wel an holl ezvezañsoù ha daleoù");
define("LANGABS63","Lakaet d'an");
define("LANGABS64","lakaat war-wel ar 5 ezvezañs ha dale diwezhañ");
define("LANGABS65","lakaat war-wel an holl ezvezañsoù");
define("LANGABS66","titouroù nevesaet evit al listenn skolidi-mañ");
define("LANGABS6bis","listenn an daleoù nann abeget");
define("LANGABS4bis","renabliñ an ezvezañsoù hag an daleoù");
define("LANGABS67","<font class=T2>skoliad ebet er chlas-mañ</font>");
define("LANGABS68","listenn ezv/ dal ur chlasad");
define("LANGABS69","hollad evz/dal ur skoliad");
define("LANGABS70","Kenaozadur an abegoù");
define("LANGABS71","Niver a ezvezañsoù");
define("LANGABS72","Niver a zaleoù");
define("LANGABS73","Ezvezañs  dale  ar chlasad ");
define("LANGABS74","Nevesaat");
define("LANGABS75","Ezvezañs na tamm dale ebet");
define("LANGABS76","meneget da ");

define("LANGDEPART3","Da heul ur gudenn deknikel.");
define("LANGDEPART4","Dibosupl kevreañ. Emañ skipailh Triade oc'h ober war-dro ar servijer.");

define("LANGBASE3_2","Setu listenn ar fichennaouegoù a chell bezañ enporzhiet.");
define("LANGbasededoni21_2","Ha choant ho peus da genderchel ? \\n\\n L\'Skipailh Triade");
define("LANGbasededon21","Kas ar fichennaoueg a chell padout un nebeud munutennoù diouzh an niver a ditouroù.");
define("LANGbasededon31_2","menegit an danvezioù ho peus choant enporzhiañ.");




// ----------------------------------------------- //
define("LANGBASE10_2","menegit ar gelennerien da ouzhpennañ.");

define("LANGBASE16_2"," ar bannoù zo dindan ar stumm : anv login ; raganv login ; ger-tremen");
define("LANGIMP25_2","anv ar skol");
// ----------------------------- //
define("LANGABS77","meneget da.");
define("LANGSTAGE89","ober ar c'henemglev staj");
define("LANGSTAGE90","Lakaat war wel ar c'henemglevioù staj");
define("LANGSTAFE91","listenn ar skolidi en embregerezhioù");
define("LANGSTAGE92","listenn ar skolidi en embregerezhioù");
define("LANGPASSG4","rankout a ra ar ger-tremen kaout 8 arouezenn.");
define("LANGPASSG5","rankout a ra ar ger-tremen kaout 4 arouezenn.");
define("LANGPASSG6","neo ket reizh ar ger-tremen. Rankout a ra ar ger-tremen kaout 8 arouezenn dan nebeutañ \\n\\n L\\'Equipe TRIADE");
define("LANGPASSG7","neo ket reizh ar ger-tremen. Rankout a ra ar ger-tremen kaout 4 arouezenn dan nebeutañ \\n\\n L\\'Equipe TRIADE");

define("LANGMODIF22_1","ger-tremen : 4 arouezenn");
define("LANGMODIF22_2","ger-tremen : 8 arouezenn ");
define("LANGMODIF22_3","ger-tremen : 8 arouezenn");
define("LANGDEPART2","<font color=red  class=T2>ATTENTION, pour utiliser TRIADE, la variable php '<strong>register_globals</strong>' doit être sur <u>Off</u>.</font><br />");


define("LANGacce15","amprouenn da vezañ rentet a-benn");
define("LANGacce16","amprouenn da vezañ rentet hiziv !");
define("LANGacce17","ouzhpennañ ur chastiz");

define("LANGBASE41","dilemel an holl skolidi a-raok enporzhiañ");
define("LANGBASE7bis","skoliad lakaet dija");
define("LANGBASE8bis","evit ar skolidi lakaet ha hep klasad");

define("LANGPER21bis","yezh");

define("LANGASS6ter","Skoliad");
define("LANGASS41","Dastum");
define("LANGASS42","arventennañ");

define("LANGIMP46bis","Ger-tremen");

define("LANGIMP54","niv. ar straed");
define("LANGIMP55","chomlec'h");
define("LANGIMP56","kod post");
define("LANGIMP57","pellgomz");
define("LANGIMP58","postel");
define("LANGIMP59","kumun");

define("LANGBULL1pp","Moulañ follenn drimiziad pe c'hwec'hmiziad");
define("LANGBT43pp","Moulañ taolenn");


define("LANGMESS38","Kemennadenn bet lennet.");
define("LANGMESS39","Kemennadenn n'eo ket bet lennet.");


define("LANGDISC57","Abeg kastiz");

define("CUMUL01","Sammad ezvezañsoù ha daleoù ur c'hlasad dre skoliad");
define("CUMUL02","Sammad kastizoù ur c'hlasad dre skoliad");
define("CUMUL03","Sammad kastizoù ur skoliad");
define("LANGPROJ18bis","Eur-ioù");
define("LANGCREAT1","Kont a zo anezhi.");
define("ERREUR1","Ne c'heller ket kaout ar rouedad internet evit ar vodulenn-se.");
define("ERREUR2","Mont da welout ar vodulenn kenneuziadur evit reiñ buhez d'ar rouedad.");


define("PASSG8","Kemmañ ar ger-tremen");
define("PASSG9","Ger-tremen ar skoliad");
define("PASSG9bis"," a zo bet kemmet.");


define("LANGPARAM34","Lec'hienn Kenrouedad an ensavadur");
define("LANGLOGO3bis","Ret d'al logo bezañ mentadet jpg");


define("LANGMAT1","Enrollañ danvez");
define("LANGMAT2","Roll/Kemmañ un danvez");
define("LANGMAT3","Tennañ kuit un danvez");
define("LANGMAT4","Kadarnaat ar c'hemm");
define("LANGMAT5","Danvez kemmet");
define("LANGMAT6","Danvez bet lakaet dija");
define("LANGCLAS1","Roll/Kemmañ klas");
define("LANGCLAS2","Klas kemmet");
define("LANGCLAS3","Klas lakaet dija");

define("LANGDEVOIR1","evit ar strollad");
define("LANGDEVOIR2","evit ar c'hlas");
define("LANGDEVOIR3","Enrollañ un dever skol");
define("LANGCIRCU111","<font face=Verdana size=1><B><font color=red>D</font></B>ocument au format : <b> doc</b>, <b>pdf</b>, <b>txt</b>.</FONT>");

define("LANGAFF7","Modulenn diverkañ ar c'hlasoù gouestlet.");
define("LANGAFF8","DIWALLIT Ober gant ar vodulenn-mañ evit diverkañ ur gouestladur.");
define("LANGAFF9","DIWALLIT, distrujañ a ra holl notennoù skolidi ar c'hlasoù diverket. \\n C'hoant hoc'h eus da genderc'hel ? \\n\\n Equipe TRIADE");
define("LANGCREAT2","Diverkañ ur gont");


define("LANGPROF37","kaier klas.");

// news

define("LANGPARAM35","Dibab ar follenn drimiziad");
define("LANGPROBLE1","Respont dre bostel");
define("LANGPROBLE2","Ret titouriñ an holl dachennoù");
define("LANGMESS37","N'eo ket bet kadarnaet ar vodulenn-mañ gant merour TRIADE.<br><br> Skipailh TRIADE");

define("LANGPROFP23","Notennoù skol a...");
define("LANGPROFP24","a viz...");
define("LANGPROFP25","Poltredaoueg");
define("LANGPROFP26","Heuliadenn ur skoliad");
define("LANGPROFP27","Titouroù diwar-benn an dileuridi");
define("LANGPROFP28","Kemennadenn evit ar c'hlasad");
define("LANGPROFP29","Kelc'hlizher evit ar c'hlasad");
define("LANGPROFP30","Merañ staj micher");
define("LANGPROFP31","Taolenn keidennoù ar skolidi");
define("LANGPROFP32","Follennoù grafikel ar skolidi");


define("LANGLETTRELUNDI","L");	  // Lundi
define("LANGLETTREMARDI","M");    // Mardi
define("LANGLETTREMERCREDI","M"); // Mercredi
define("LANGLETTREJEUDI","Y");    // Jeudi
define("LANGLETTREVENDREDI","G"); // Vendredi
define("LANGLETTRESAMEDI","S");   // Samedi
define("LANGLETTREDIMANCHE","S"); // Dimanche



define("LANGRESA71","Miret a-benn a...");
define("LANGRESA72","eus");
define("LANGRESA73","da");
define("LANGRESA74","titouroù ouzhpenn");

define("LANGbasededoni52","Talvoud asantet : <b>0</b> pe Ao.<br>");
define("LANGbasededoni53","Talvoud asantet : <b>1</b> pe It.<br>");
define("LANGbasededoni54","Talvoud asantet : <b>2</b> pe Dim.<br>");
define("LANGbasededoni54_2","Talvoud asantet : <b>3</b> pe Ao <br>");
define("LANGbasededoni54_3","Talvoud asantet : <b>4</b> pe Ao <br>");
define("LANGbasededoni54_4","Talvoud asantet : <b>5</b> pe Ao <br>");


define("LANGacce_dep2bis","<br><b>DIWALLIT !! Gwiriit ervat ho toare dont tre. Dibabit ho kont a zere.</b>");

define("LANGNA3bis","Ger-tremen kerent "); //
define("LANGNA3ter","Ger-tremen skoliad "); //

define("LANGELE244","Postel");

define("LANGTP12","Kadarnait ho kont, mar plij.");

define("LANGMESS40","Bez' hoc'h eus <strong> ");
define("LANGMESS40bis"," </strong> flux RSS enregistré(s).");  // ajouter "\" devant les quotes
define("LANGMESS41","Kont");  // Compte comme "kont implijer".
define("LANGMESS42","Eil kevreadur");
define("LANGMESS43","Kevreadur diwezhañ");

define("LANGALERT4","DIWALLIT, dibabit anvioù sujedoù disheñvel.");

define("LANGMODIF26","Kemmañ isdanvez");
define("LANGPROF38","Notennoù trimiziad");
define("LANGPROF39","Titourerezh ouzhpenn");

define("LANGCIRCU21","Prest a-b."); // abréviation de "prest a-benn" 

define("LANGTELECHARGE","Pellgargañ"); //  downloader

define("LANGPARENT15bis","Kastiz a...");
define("LANGDISC2bis","Moulañ kastizoù an deiz");

define("LANGRECH5","Skrivañ an elfenn pe an elfennoù da glask");
define("LANGRECH6","Dibab dre urzh");

define("LANGPROFP33","Leuniañ ar follennoù trimiziad");
define("LANGPROFP34","Gwiriañ follenn drimiziad");
define("LANGPROFP35","Lenn pe gemmañ evezhiadennoù ar follennoù trimiziad");


define("LANGPROFP36","Deiziad trimiziad ebet lakaet evit ar bloavezh-skol mañ");
define("LANGPROFP37","Enrollañ an evezhiadennoù");

define("LANGGRP40","Strollad savet");
define("LANGGRP41","Setu roll ar skolidi n'int ket enrollet");
define("LANGGRP42","Bez' ez eus dija eus ar strollad-se");
define("LANGGRP43","Fazi fichennaoueg");
define("LANGGRP44","Diverkañ ur strollad");
define("LANGGRP45","Enporzhiañ fichennaoueg");
define("LANGGRP46","Anv strollad a zo anezhañ -- Servij TRIADE");

define("LANGPARAM37","Akademiezh");


define("LANGAGENDA274","Gouel an deiz ");
define("LANGPARAM38","Deiz-ha-bloaz ");

define("LANGEDT1","F"); // première lettre
define("LANGEDT1bis","Fichennaoueg ment pe Ment vrasañ ar fichennaoueg : ");
define("ERREUR3","Mont e darempred gant merer TRIADE evit reiñ buhez d'ar rouedad.");

define("LANGELE30","Kemmañ ar ger-tremen");
define("LANGMESS44","Kas ur gemennadenn d'ur skoliad a ");
define("LANGMESS5","Kas ur gemennadenn d'un tad/ur vamm a : ");


define("LANGMESS45","Kas ur gemennadenn war-zu ur postel : ");
define("LANGMESS2","Kas ur gemennadenn d'ar renerezh : ");
define("LANGTRONBI9","ar skolidi");
define("LANGTRONBI10","ar skipailh");
define("LANGTRONBI11","Poltredaoueg an implijidi");
define("LANGTITRE15","Staliañ ar benngelennerien pe ar skolaerien");
define("LANGPER7","Anvet e ... klas "); //

define("LANGPROF40","Titouroù ouzhpenn");
define("LANGPROFP38","Leuniañ pe lenn karned an heuliañ");

define("LANGEDIT2","Pgz hezoug 1");
define("LANGEDIT3","Sevended ");
define("LANGEDIT4","Anv ateb. 2");
define("LANGEDIT5","Raganv ateb. 2");
define("LANGEDIT6","Lec'h ganedigezh");
define("LANGEDIT7","Sevended ");
define("LANGEDIT8","Anv ateb. 1");
define("LANGEDIT9","Pgz hezoug 2");
define("LANGEDIT10"," Den kar");
define("LANGEDIT11","Postel skoliad");
define("LANGEDIT12","Pgz skoliad");
define("LANGEDIT13","Postel tutor");

define("LANGEDIT14","a hiziv");
define("LANGEDIT15","Abaoe un devezh");
define("LANGEDIT16","Abaoe daou zevezh");
define("LANGEDIT17","Abaoe tri devezh");
define("LANGEDIT18","Abaoe pevar devezh");
define("LANGEDIT19","Daleoù hep abegoù reizh");
define("LANGEDIT20","Pgz hezoug ");

define("LANGSMS1","Kas SMS evit daleoù abaoe ");
define("LANGSMS2","N'eo ket merket");

define("LANGSUPPLE","Roll an erlec'hierien");
define("LANGSUPPLE1","o kemer lec'h ");
define("LANGTITRE2","Keleier an ensavadur");
define("LANGTITRE1","Darvoudoù");


define("LANGDISC58","Lakaat un danvez ouzhpenn d'ur skoliad");
define("LANGDISC59","ebarzhiñ doare U.S.A.");
define("LANGDISC60","Arnodenn ");

define("LANGBT8","Roll / Kemm ar renerien");
define("LANGBT9","Roll / Kemm ar vuhez skol");
define("LANGBT10","Roll / Kemm ar gelennerien");
define("LANGDIRECTION","Renerezh");

define("LANGTITRE36","Merañ izili ar renerezh");
define("LANGTITRE37","Merañ izili ar vuhez-skol");
define("LANGTITRE38","Merañ ar gelennerien");
define("LANGTITRE39","Merañ an erlec'hierien");
define("LANGTITRE40","skoliad");
define("LANGTITRE41","ateb."); // pour l'abréviation de "responsable"
define("LANGTITRE42","tutor"); // dans le cadre familial
define("LANGTITRE43","Merañ ur skoliad");
define("LANGTITRE44","Enporzhiañ ur roll skolidi");
define("LANGTITRE45","Klask ur skoliad");
define("LANGCHERCH1","Diouzh an dezverk enklask");
define("LANGCHERCH2","Dibenn ar c'hlask");
define("LANGCHERCH3","Niver a elfennoù kavet");
define("LANGPROF3bis","Gwelout deverioù, priziadennoù, arnodennoù");
define("LANGTROMBI","Ezporzhiañ listennoù ar skolajidi war-zu WellPhoto");
define("LANGPURG1","Diverkañ an titouroù");
define("LANGPUR2","Diverkañ an titouroù");
define("LANGPROFP39","Taolenn ar cheidennoù bloaz :");
define("LANGBLK1","Diluget eo ho kont. Klasket ho peus mont war ur bajenn neo ket aotreet deoch. Evit lañsiñ en-dro ho kont, it e darempred gant merour ho skolaj. Skipailh Triade.");
define("LANGCARNET4","mont");
define("LANGFORUM10bis","Ho raganv ");
define("LANGTPROBL11","Respontet e vo deoch an abretañ ar gwellañ. Skipailh Triade ");
define("LANGTRAD1","Listenn an obererezhioù graet");
define("LANGPARAM39","Testeni enrollet");
define("LANGPARAM40","Testeni nann enrollet");
define("LANGPARAM41","Rankout a ra ar fichennaoueg bezañ e stumm rtf ha skañvoch evit 2Mo");
define("LANGBASE42","Enporzhiañ ar fichennaoueg");
define("ACCEPTER","Asantiñ");
define("LANGCONDITION","Asantiñ a ran an divizoù lakaet");
define("LANGPARAM42","Listenn an daleoù pe ezvezañsoù");
define("LANGCARNET5","Sellout ouzh ar charned heuliañ");
define("LANGCARNET6","Leuniañ ar charned heuliañ");
define("LANGCARNET7","Leuniañ");
define("LANGCARNET8","Karned heuliañ");
define("LANGCARNET9","Krouiñ ur charned heuliañ");
define("LANGCARNET10","Kemmañ ur charned heuliañ");
define("LANGCARNET11","Lemel ur charned heuliañ");
define("LANGCARNET12","Sellout ouzh ur charned heuliañ");
define("LANGCARNET13","Ezporzhiañ ur charned heuliañ");
define("LANGCARNET14","Enporzhiañ ur charned heuliañ");
define("LANGCARNET15","Enporzhiañ");
define("LANGCARNET16","Ezporzhiañ");
define("LANGCARNET17","Roll ar charned heuliañ");
define("LANGCARNET18","Anv ar charned heuliañ");
define("LANGCONTINUER","Kenderc'hel --->");
define("LANGCARNET19","Krouiñ ur charned heuliañ");
define("LANGCARNET20","Kod evezhiadennoù a chell bezañ dibabet gant ar gelennerien");
define("LANGCARNET21","Lizhiri");
define("LANGCARNET22","Sifroù");
define("LANGCARNET23","Livioù");
define("LANGCARNET24","Notennoù");
define("LANGCARNET25","(0 da 10 pe 0 da 20)");
define("LANGCARNET26","Kenskrivañ");
define("LANGCARNET27","tapet");
define("LANGCARNET28","Da vezañ kadarnaet");
define("LANGCARNET29","Neo ket tapet");
define("LANGCARNET30","o vezañ paket");
define("LANGCARNET31","Neo ket bet prizet");
define("LANGCARNET32","Gwer");
define("LANGCARNET33","Glas");
define("LANGCARNET34","Orañjez");
define("LANGCARNET35","Ruz");
define("LANGCARNET36","mare");
define("LANGCARNET37","mareoù");
define("LANGCARNET38","merañ ar c'harned heuliañ");
define("LANGCARNET39","niver a vareoù ma ranker kaout sinadurioù ar gerent, ar c'helenner hag ar Renerezh ");
define("LANGCARNET40","Niver-où ");
define("LANGCARNET41","Lodennoù stag ouzh ar c'harned heuliañ");
define("LANGCARNET42","Lodennoù");
define("LANGCARNET43","D'ar muiañ e c'heller ober 4 dibab (miret e vo ar 4 choaz kentañ)");
define("LANGCARNET44","Savet eo ar c'harned heuliañ. Bez' e c'hellit ouzhpennañ bremañ ar barregezhioù stag ouzh ar c'harned-se.");
define("LANGCARNET45","Ouzhpennañ un dachenn varregezh ");
define("LANGCARNET46","Anv an dachenn varregezh ");
define("LANGCARNET47","Ha klotañ a ra an anvadur-mañ gant ur rummad barregezhioù ?  ");
define("LANGCARNET48","Anvadur");
define("LANGCARNET49","Ouzhpennañ ur varregezh ");
define("LANGCARNET50","Kemmañ doareoù pennañ ar c'harned ");
define("LANGCARNET51","Ouzhpennañ un dachenn barregezhioù ");
define("LANGCARNET52","Kemmañ un dachenn barregezhioù ");
define("LANGCARNET53","Merkit ar c'harned heuliañ");
define("LANGCARNET54","N'eus karned heuliañ ebet.");
define("LANGCARNET55","Sellout ouzh ur c'harned heuliañ");
define("LANGCARNET56","Ur c'harned heuliañ");
define("LANGCARNET57","Adtapout ar c'harned heuliañ e stumm PDF");
define("LANGCARNET58","Ezporzhiañ ur c'harned heuliañ");
define("LANGCARNET59","Evit adtapout ar c'harned heuliañ");
define("LANGCARNET60","Kemmañ ur c'harned heuliañ");
define("LANGCARNET61","Diverkañ ur c'harned heuliañ");
define("LANGCARNET63","Enporzhiañ ur charned heuliañ");
define("LANGCARNET64","fichennaoueg da enporzhiañ");
define("LANGCARNET65","Dilemel an implij amzer a-bezh a-raok enporzhiañ ?");
define("LANGCARNET66","Enporzhiañ nullet. Anv ar charned-mañ zo roet dija. Dilamit ar charned-mañ a-raok enporzhiañ.");
define("LANGCARNET62","Diwallit !!! Diverket vo an holl notennoù stag ouzh ar charned heuliañ");
define("LANGEDT2","Enporzhiañ implij amzer Visual Timetabling");
define("LANGEDT3","Enporzhiañ implij amzer Visual Timetabling echuet");
define("LANGEDT4","Skritellañ / merañ an implij amzer");
define("LANGEDT5","Enporzhiañ implij amzer Visual Timetabling");
define("LANGEDT6","Ezporzhiañ Triade war-zu Visual Timetabling");
define("LANGEDT7","Skritellañ / merañ an implij amzer");
define("LANGEDT8","Merañ");
define("LANGEDT9","Lakaat e pleustr an implij amzer");
define("LANGEDT10","Modulenn SQLite neo ket asantet. Kadarnait ho servijer evit ma vo asantet SQLite.");
define("LANGGRP47","Klask ar strolladoù");
define("LANGGRP48","Listenn strolladoù ur skoliad");
define("LANGGRP49","Listenn ar strolladoù");
define("LANGDISP21","Kenaozadur abeg evz / dale");
define("LANGDISP22","Enrollañ an abegoù ");
define("LANGDISP23","Anv an abeg ");
define("LANGDISP24","Listenn an abegoù ");
define("LANGDISP25","Niver a skolidi nevesaet");
define("LANGDISP26","Rankout a ra ar fichennaoueg bezañ e stumm xls");
define("LANGCARNET63","Karned heuliañ enporzhiet betek penn");
define("LANGCARNET64","Listenn ar chastizoù");
// News 2
define("LANGCARNET67","ouzhpennañ ur chastiz");
define("LANGCARNET68","Eur");
define("LANGVIES1","Anv an den stag ouzh ar rentañ-kont");
define("LANGVIES2","feur an notenn buhez skol war ar rentañ-kont");
define("LANGVIES3","feur kelenner");
define("LANGVIES4","feur buhez skol");
define("LANGVIES5","Listenn ar gelennerien");
define("LANGVIES6","titouroù skol ouzhpenn");


define("LANGVIES7","enrollañ an notennoù hag an evezhiadennoù");
define("LANGVIES8","moulañ ezvezañsoù ur chlasad");
define("LANGVIES9","Menegit ar miz");
define("LANGVIES10","menegit ur chlasad ");

define("LANGPDF1","ur fichennaoueg PDF evit an holl");
define("LANGPDF2","ur fichennaoueg PDF dre skoliad");

define("LANGEDIT5bis","raganv den e karg 1");

define("LANGGRP50","Kemmañ anv ur strollad");
define("LANGGRP51","Anv ar strollad");
define("LANGGRP52","Modulenn gemmañ");
define("LANGGRP53","Anv strollad nevez");
define("LANGGRP54","pe follenn notennoù");
define("LANGGRP55","Arnodenn");

define("LANG1ER","1añ");
define("LANG2EME","2l");
define("LANG3EME","3de");
define("LANG4EME","4re");
define("LANG5EME","5vet");
define("LANG6EME","6vet");
define("LANG7EME","7vet");
define("LANG8EME","8vet");
define("LANG9EME","9vet");


define("LANGGRP56","Notenn war");
define("LANGGRP57","Derc'hel");
define("LANGGRP58","Diwall ! Diverket vo notennoù ar skolidi dibabet en holl glasadoù !!!");
define("LANGGRP59","Diaskit ar skoliad nemañ ket ken er strollad");
define("LANGGRP60","Kemmañ al listenn");


define("LANGEDIT20bis","Dvk");  // abréviation de Supprimer  sur 3 lettres seulement

define("LANGGRP61","Distro d'an nevesadur");
define("LANGRTDJUS","Reizhabeget"); // pour un retard
define("LANGABSJUS","Reizhabeget"); // pour une abs
define("LANGPARAM2","<font class=T1>Composez votre texte pour le contenu du message de retard à envoyer aux parents. Vous pouvez préciser les informations suivantes : Nom de l'élève : <b>NomEleve</b> - Prénom de l'élève : <b>PrenomEleve</b> - Adresse : <b>AdresseEleve</b> - Code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'élève : <b>ClasseEleve</b> - Date du retard : <b>RTDDATE</b> - Heure du retard : <b>RTDHEURE</b> - Durée : <b>RTDDUREE</b>  - Cumul absence : <b>CumulABS</b> </font><br><br>");

define("LANGPARAM1","<font class=T1>Composez votre texte pour le contenu du message de l'absence à envoyer aux parents. Vous pouvez préciser les informations suivantes : Nom de l'élève : <b>NomEleve</b> - Prénom de l'élève : <b>PrenomEleve</b> - Adresse : <b>AdresseEleve</b> - Code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'élève : <b>ClasseEleve</b> - Date de début d'absence :  <b>ABSDEBUT</b> - Date de fin d'absence : <b>ABSFIN</b> - Durée : <b>ABSDUREE</b> - Nom du responsable 1 : <b>NomResponsable1</b> - Adresse responsable 1 : <b>AdresseResponsable1</b> - Ville responsable 1 : <b>VilleResponsable1</b> - Cumul absence : <b>CumulABS</b> - Date du jour : <b>DATEDUJOUR</b> </font><br><br>");

define("LANGGRP62","studi");
define("LANGGRP63","Lizhiri");
// FAUTES CORRIGEES ---> 01/09/2007

define("LANGDELEGUE1","Kannaded");
define("LANGEDT10bis","Modulenn simpl XML neo ket asantet. Kemmit ar servijer evit gallout degemer ar stumm simpl XML.");


define("LANGBULL45","Kas ur gemennadenn dan holl gelennerien asket evit goulenn diganto leuniañ ar rentaoù-kont.");
define("LANGBULL46","Niver a rentaoù-kont leuniet er chlas");

define("LANGMESS46","Sellout e");
define("LANGMESS47","Dilemel un dalc'h-kastiz pe ur chastiz");


define("LANGCOUR","Lizhiri echuet");
define("LANGCOUR1","Listenn ar chastizoù n'int ket bet graet");
define("LANGCOUR2","Kenaozadur lizhiri ar chastizoù");


define("RESA75","Titouroù ouzhpenn");
define("LANGCOM","Enrollañ ho holl evezhiadennoù en ho tiaz-titouroù.");
define("LANGCOM1","An niver uhelañ a rank bezañ brasoc'h eget an niver bihanañ.");
define("LANGCOM2","Rankout a ra an holl dachennoù bezañ meneget en un doare reizh.");
define("LANGCOM3","Niver a skolidi : ");
define("LANGSTAGE91","Anv an den e karg");
define("LANGSTAGE93","Kefridi an den e karg");
define("LANGSTAGE94","eus an embregerezh");
define("LANGSTAGE95","embregerezh");
define("LANGSTAGE96","Niver a ditouroù kavet");
define("LANGSTAGE97","Lakait un niver, mar plij");
define("LANGSTAGE98","Lakait deiziad deroù ar staj");
define("LANGSTAGE99","Lakait deiziad dibenn ar staj");

define("LANGPATIENTE","Gortozit, mar plij");
define("LANGSMS3","Niverenn bellgomz hezoug");
define("LANGSMS4","150 arouezenn dar muiañ");
define("LANGSMS5","Kemennadenn");
define("LANGSMS6","Kasadenn an SMS zo miret gant ar renerezh hag a c'hell bezañ gwelet");
define("LANGSMS7","Kasadenn kemennadenn SMS");
define("LANGSMS8","Kas kemennadenn SMS");
define("LANGSMS9","Pellgomz kerent");
define("LANGSMS10","Kas un SMS dur chlasad a-bezh");
define("LANGSMS11","Kas un SMS da dud ur skoliad");
define("LANGSMS12","Kas un SMS dun den dre e anv");
define("LANGSMS13","Kas un SMS dun den dre e niverenn");
define("LANGSMS14","Niverenn");
define("LANGbasededoni54_5","niverenn asantet : <b>7</b> ou P <br>");
define("LANGbasededoni54_6","niverenn asantet : <b>8</b> ou Sr <br>");

define("LANGGRP27bis","Ouzhpennañ ur skoliad e meur a strollad");
define("LANGGRP28bis","Ouzhpennañ ur skoliad en ur strollad");
define("LANGGRP29bis","Ebarzhiañ&nbsp;/&nbsp;Kemmañ");
define("LANGNOTEUSA6","Lakaat da glotañ an notennoù er mod USA");
define("LANGNOTE1","Anv an arnodenn");
define("LANGPARAM44","Kaout ur gemennadenn pa resever ur c'heloù eus doare");
define("LANGMESS17bis","Kenaozadur");
define("LANGNNOTE2","Dibab dre glasad");
define("LANGNNOTE3","Dibab dre anv");
define("LANGNNOTE4","Lakait titl an diell");
define("LANGBULL47","Rentañ-kont hep isdanvez");
define("LANGBULL48","Rentañ-kont gant isdanvezioù");
define("LANGBULL49","Rentañ-kont arnodenn wenn");

define("LANGMESS48","Boest dilemel");
define("LANGMESS49","Neus embregerezh ebet lakaet da skoliad ebet.");
define("LANGMESS50","Steuñv ar chlasad");
define("LANGPARAM43","<font class=T1>Composez votre texte pour le contenu du message de la retenu pour l'envoi du courrier aux parents d'élève. Vous pouvez préciser les informations suivantes : Nom de l'élève : <b>NomEleve</b> - Prénom de l'élève : <b>PrenomEleve</b> - adresse : <b>AdresseEleve</b> - code postal : <b>CodePostalEleve</b> - Ville : <b>VilleEleve</b> - Classe de l'élève : <b>ClasseEleve</b> - la date de la retenu : <b>DATERETENU</b> - l'heure de la retenu : <b>HEURERETENU</b> - la durée : <b>RETENUDUREE</b> - le motif : <b>RETENUMOTIF</b> -  la catégorie : <b>RETENUCATEGORY</b> - Attribué par : <b>ATTRIBUEPAR</b> - Devoir à faire : <b>DEVOIRAFAIRE</b> - Les faits : <b>FAITS</b>  - Civilité tuteur 1 : <b>CIVILITETUTEUR1</b> - Nom du responsable 1 : <b>NOMRESP1</b> Prénom du responsable 1 : <b>PRENOMRESP1</b> - Date du jour : <b>DATEDUJOUR</b> </font><br><br>");
define("LANGMESS51","Menegit an danvezioù diret");
define("LANGMESS52","(Notenn lakaet er geidenn hollek ma 'z eo dreist da 10/20)");
define("LANGMESS53","Sizhun a-raok");
define("LANGMESS54","Sizhun a zeu");
define("LANGMESS55","Implij amzer ar chlasad");
define("LANGMESS56","skoliad ebet");

define("LANGMESS57","Anavezer");
define("LANGMESS58","Neus niverenn ebet gant ar gont-mañ.");
define("LANGMESS59","Kemmañ ivez an ezv/dal abeget");
define("LANGMESS60","E");
define("LANGMESS60bis","ezvezant");
define("LANGMESS61","kelennerien");
define("LANGMESS62","Tud");
define("LANGMESS63","hiziv");  // mettre une \' 
define("LANGBT27bis","Enrollañ ezv/dal"); 
define("LANGDEPART3bis","Mont-tre trochet ! ");
define("LANGDEPART4bis","Trochet eo mont tre Triade, it e darempred gant ho skolaj evit gouzout hiroc'h.");
define("LANGAIDE","Sikour en-linenn");
define("LANGAIDE1","Menegit al liamm etre ho tanvezioù enrollet ha danvezioù ar breved. Grit un drag-drop etre an danvezioù a gleiz da zehou");
define("LANGAIDE2","Composer votre texte pour le contenu de la convention de stage. Pour une prise en compte d'élément tel que le nom, prénom, adresse etc... veuillez présiser la chaîne suivante en fonction de vos besoins :");

define("LANGBREVET1","Mont tre");

define("LANGPARAM3","<font class=T1>Composez votre texte pour le contenu du certificat de scolarité.  Pour une prise en compte du nom, du prénom et de l'adresse de l'élève automatiquement dans chaque document, veuillez présiser la chaîne <b>NomEleve</b>, <b>PrenomEleve</b>, <b>AdresseEleve</b>, <b>CodePostalEleve</b> et <b>VilleEleve</b> à l'emplacement désiré. De même, possibilité d'indiquer la classe avec le mot clef <b>ClasseEleve</b> ou <b>ClasseEleveLong</b>, la date de naissance avec <b>DateNaissanceEleve</b>, lieu de naissance via <b>LieuDeNaissance</b>, la date du jour via <b>DateDuJour</b>, l'année scolaire via <b>AnneeScolaire</b>.</font><br><br>");
define("LANGCONFIG4","Bezañ kelaouet eus ur gemennadenn pa");
define("LANGCONFIG5","niver a ezvezañsoù nann abeget aet en tu-hont da");
define("LANGCONFIG6","niver a zaleoù nann abeget aet en tu-hont da");
define("LANGCONFIG7","gwech");
define("LANGCONFIG8","Listenn an implijerien gelaouet");

define("LANGMESS64","Tud o deus bet ar gemennadenn-mañ");
define("LANGMESS65","Listenn ar reolennoù diabarzh");
define("LANGMESS66","Ar rener");
define("LANGMESS67","Sellet am eus ouzh an dielloù a-us");
define("LANGMESS68","Asantiñ a ran ar reolennadur diabarzh");
define("LANGMESS69","Asantiñ a ran aozioù kelenn hollek");
define("LANGMESS70","Reolennoù a c'heller kaout dre ar gelennerien");
define("LANGMESS71","Sellout ouzh fichennoù stad ar reolennoù");
define("LANGMESS72","Moulañ fichennoù stad ar reolennoù");
define("LANGMESS73","Listenn an dleoù hag ar paeamantoù diglok");
define("LANGMESS74","Fichenn stad ar reolennoù");
define("LANGacce_dep2ter","<br><b>Diwallit ! Gwiriekait ho toare mont tre, dibabit ar gont a glot ganeoch.</b>");

define("LANGMESS75","Distro d?ar roll-meuzioù pennañ");
define("LANGMESS76","Kenskrivañ");
define("LANGMESS77","(priziadenn, arnodenn");
define("LANGMESS78","Dibab dre ");
define("LANGMESS79","Notenn embannet d'ar skolajidi  d'a...");
define("LANGMESS80","buhez skol");
define("LANGMESS81","O kennaskañ emañ");
define("LANGMESS82","Keidenn");
define("LANGMESS83","Keidenn ar c?hlasad");
define("LANGMESS84","Uhel");
define("LANGMESS85","Izel");
define("LANGMESS86","deiziad trimiziad ebet");
define("LANGMESS86bis","a-benn");
define("LANGMESS86ter","ar bloavezh skol-mañ");
define("LANGMESS87","Notenn an arnodennoù");

define("LANGMESS88","Kaier-klas enrollet  -- Servijoù Triade");
define("LANGMESS89","Kaier-klas e ");
define("LANGMESS90","Enrollit a-raok cheñch ivinell");
define("LANGMESS91","Danvez ar sizhun");
define("LANGMESS92","Danvez ar gentel");
define("LANGMESS93","Fichennaoueg stag");
define("LANGMESS94","Fichennaoueg stag");
define("LANGMESS95","Pal ar gentel");
define("LANGMESS96","Labour-noz d'ober a-benn");
define("LANGMESS97","Titour ebet");
define("LANGMESS98","Labour-noz d'ober");
define("LANGMESS99","Notennoù");
define("LANGMESS100","Sell hollek");
define("LANGMESS101","Kadarnaat");
define("LANGMESS102","Sellout");
define("LANGMESS103","Amzer brasjedet d'ober al labour-mañ");
define("LANGMESS104","Amzer brasjedet: ");
define("LANGMESS105","Fichennaoueg ");
define("LANGMESS106","Kemmañ ");
define("LANGMESS107","Skarzhañ ar fichennaoueg-mañ");
define("LANGMESS108","Amzer brasjedet");
define("LANGMESS109","eus"); // notion de date du xxxx au xxxx
define("LANGMESS110","betek"); // notion de date du xxxx au xxxx
define("LANGMESS111","Stumm PDF");

define("LANGBT288","Sellout / Kemmañ");
define("LANGSITU1","Dimezet"); 
define("LANGSITU2","Torrdimezet"); 
define("LANGSITU3","Intañv");
define("LANGSITU4","Intañvez"); 
define("LANGSITU5","Concubin"); 
define("LANGSITU6","PACS"); 
define("LANGSITU7","Dizimez");
define("LANGFIN002","Dareviadur");
define("LANGFIN003","Dareviadur");
define("LANGFIN004","Deiziad ebet dibabet");
define("LANGCONFIG","Arventennañ");

define("LANGMESS112","Evezhiadenn rentañ-kont trimiziad / C'hwec'hmiziad");
define("LANGMESS113","Dibab an evezhiadenn");
define("LANGMESS114","Evezhiadenn evit ar breved");
define("LANGMESS115","Sellout ouzh ar rentañ-kont");
define("LANGMESS116","digeriñ");
define("LANGMESS117","rummad");
define("LANGMESS118","Tremen er mod astennet");
define("LANGMESS119","Evezhiadennoù, alioù evit mont war-raok");
define("LANGMESS120","Perzhioù mat,gwellaennoù , strivoù");
define("LANGMESS121","kemm e-keñver ar palioù gortozet");
define("LANGMESS122","Alioù evit mont war-raok");
define("LANGMESS123","Keidenn ar c'hlasad");
define("LANGMESS124","Evezhiadenn gent");
define("LANGMESS125","ouzhpennañ el listenn"); // Gwiriañ (') 
define("LANGMESS126","Enrollañ an evezhiadenn"); // Gwiriañ (') 
define("LANGMESS127","Distreiñ ha klikañ war"); // Gwiriañ (') 
define("LANGMESS128","Enrollañ");  // Gwiriañ (') 
define("LANGMESS129","Lenn");
define("LANGMESS130","Keidenn gent");
define("LANGMESS131","Enrollañ an evezhiadennoù");
define("LANGMESS132","Gortozit mar plij");
define("LANGMESS133","Evezhiadenn ebet");
define("LANGMESS134","Evezhiadenn n'eo ket bet enrollet");
define("LANGMESS135","Evezhiadenn evit ar c'hlasad");
define("LANGMESS136","Klikit amañ");
define("LANGMESS137","Titouroù skol ouzhpenn");
define("LANGMESS138","Lakaat evezhiadennoù ouzhpenn er rentañ-kont");


define("LANGMESS139","Messagerie brouillon");
define("LANGMESS140","Préparer un brouillon ");
define("LANGMESS141","Accès");
define("LANGMESS142","Valider un brouillon");
define("LANGMESS143","Les messages brouillons sont visibles par tous les membres de la direction");
define("LANGMESS144","Signature du directeur");
define("LANGMESS145","Année scolaire");
define("LANGMESS156","Pays");
define("LANGMESS159","Choix du site");
define("LANGMESS160","Nouveau site");
define("LANGMESS177","Département ");
define("LANGMESS146","Enregistrement au format semestriel.");
define("LANGMESS147","Toutes les classes");
define("LANGMESS148","Liste des périodes trimestrielles ou semestrielles ");
define("LANGMESS149","Modifier");
define("LANGMESS150","Supprimer");
define("LANGMESS157","Trimestre");
define("LANGMESS158","Classe");
define("LANGMESS151","Identifiez votre compte");
define("LANGMESS152","Veuillez d'abord identifier votre compte pour réinitialiser votre mot de passe.");
define("LANGMESS153","Demande de mot de passe");
define("LANGMESS154","Création de groupe");
define("LANGMESS155","Liste des groupes des enseignants");
define("LANGMESS161","Gestion de votre compte");
define("LANGMESS162","Gestion de votre compte");
define("LANGBT53","Entrée"); 
define("LANGMESS163","Vérification des groupes");
define("LANGMESS164","Boite de suppression");
define("LANGMESS165","Archiver dans");
define("LANGMESS166","Boite de reception");
define("LANGMESS167","Paramétrage de votre compte");
define("LANGMESS168","Actualités");
define("LANGMESS169","Réservation Salle / Equipement");
define("LANGMESS170","Messagerie Triade");
define("LANGMESS171","(Indiquer votre  email)");
define("LANGMESS172","(Numéro de portable)");
define("LANGMESS173","Message à un groupe ");
define("LANGMESS174","Message aux délégués :");
define("LANGMESS175","Message à un membre du personnel : ");
define("LANGMESS176","Message à un tuteur de stage : ");
define("LANGMESS178","Civ.");
define("LANGMESS179","Indice&nbsp;salaire");
define("LANGMESS180","Création d'un compte tuteur de stage");
define("LANGMESS181","Liste / Modification d'un tuteur de stage");
define("LANGMESS182","Gestion des membres Tuteur de stage");
define("LANGMESS183","Entreprise liée");
define("LANGMESS184","En qualité de ");
define("LANGMESS185","Gestion des membres du Personnel");
define("LANGMESS186","Création d'un compte personnel"); // "Cr&eacute;ation d'un compte personnel"
define("LANGMESS187","Rechercher");
define("LANGMESS188","Importer");
define("LANGMESS189","Supprimer");
define("LANGMESS190","Lv1/Spé :");
define("LANGMESS191","Lv2/Spé :");
define("LANGMESS192","Boursier");
define("LANGMESS193","Inscription au BDE");
define("LANGMESS194","Inscription à la bibliothèque");
define("LANGMESS195","Montant Bourse");
define("LANGMESS196","Indemnité Stage");
define("LANGMESS197","Code comptabilité ");
define("LANGMESS198","Adresse");
define("LANGMESS199","Téléphone");
define("LANGMESS200","Tél. Portable");
define("LANGMESS201","E-mail Etudiant");
define("LANGMESS202","E-mail universitaire");
define("LANGMESS203","Situation Familiale");
define("LANGMESS204","Copier adresse");
define("LANGMESS205","Classe antérieure");
define("LANGMESS206","Intitulé de la classe");
define("LANGMESS207","Ecole");
define("LANGMESS208","Format court");
define("LANGMESS209","Format long");
define("LANGMESS210","Code matière");
define("LANGMESS211","Réglement intérieur");
define("LANGMESS212","Ajouter un règlement");
define("LANGMESS213","lister le/les règlements");
define("LANGMESS214","Supprimer un règlement");
define("LANGMESS215","Gestion des SMS");
define("LANGMESS216","Membre");
define("LANGMESS217","Direction");
define("LANGMESS218","Enseignant");
define("LANGMESS219","Vie Scolaire");
define("LANGMESS220","Personnel");
define("LANGMESS221","Code barre :");
define("LANGMESS222","Gestion des Unités d'enseignements");
define("LANGMESS223","Création d'une unité d'enseignement");
define("LANGMESS224","Lister/Modifier");
define("LANGMESS225","Fichier Excel");
define("LANGMESS226","Fichier XML");
define("LANGMESS227","Code barre");
define("LANGMESS228","Suppression d'une période ");
define("LANGMESS229","Ajustement des horaires ");
define("LANGMESS230","Période visible sur l'EDT");
define("LANGMESS231","Importer image ou pdf : ");
define("LANGMESS232","(format  de l'image : jpg et moins de 2Mo )");
define("LANGMESS233","EDT de la classe : ");
define("LANGMESS234","Exportation des données");
define("LANGMESS235","Informations à exporter");
define("LANGMESS236","Personnel");
define("LANGMESS237","Choix de l'extraction : ");
define("LANGMESS238","Nom de l'enseignant ");
define("LANGMESS239","Exportation au format PDF : ");
define("LANGMESS240","Exporter");
define("LANGMESS241","Sujet : ");
define("LANGMESS242","Fichier audio : ");
define("LANGMESS243","Impression ");
define("LANGMESS365","&nbsp;Demi&nbsp;Pension&nbsp;");
define("LANGMESS366","&nbsp;Interne&nbsp;");
define("LANGMESS367","&nbsp;Externe&nbsp;");
define("LANGMESS368","&nbsp;Inconnu&nbsp;");
define("LANGMESS244","Réserver via E.D.T.");
define("LANGMESS245","Type membre : ");
define("LANGMESS246","Parents");
define("LANGMESS247","Etudiants");
define("LANGMESS248","Type adresse :");
define("LANGMESS249","Tuteur");
define("LANGMESS327","Publipostage");
define("LANGMESS328","Afficher la civilit&eacute; des &eacute;tudiants : ");
define("LANGMESS329","Afficher matricule : ");
define("LANGMESS330","Afficher Classe : ");
define("LANGMESS331","Afficher Adresse : ");
define("LANGMESS250","Listing Classe");
define("LANGMESS251","Envoyer un SMS");
define("LANGMESS252","Modifier Fiche");
define("LANGMESS253","Affecter &agrave; un stage");
define("LANGMESS254","Bloquer ce compte");
define("LANGMESS255","Débloquer ce compte");
define("LANGMESS259","Renseignements");
define("LANGMESS260","Carnet de notes");
define("LANGMESS261","Vie Scolaire");
define("LANGMESS262","Disciplines");
define("LANGMESS263","Opérations effectuées");
define("LANGMESS264","Info. Tuteur 1");
define("LANGMESS265","Info. Tuteur 2");
define("LANGMESS266","Info. Etudiant");
define("LANGMESS267","Archives");
define("LANGMESS268","Info. médicales");
define("LANGMESS269","info. compl.");
define("LANGMESS270","Nom :");
define("LANGMESS271","Prénom :");
define("LANGMESS272","Classe :");
define("LANGMESS273","Date&nbsp;de&nbsp;nais.&nbsp;:");
define("LANGMESS274","Nationalité&nbsp;:");
define("LANGMESS275","Lieu&nbsp;naissance&nbsp;:");
define("LANGMESS276","Boursier :");
define("LANGMESS277","Numéro&nbsp;Etudiant&nbsp;:");
define("LANGMESS278","Lv1/Spé :");
define("LANGMESS279","Lv2/Spé :");
define("LANGMESS280","Option :");
define("LANGMESS281","Régime :");
define("LANGMESS282","N°&nbsp;Rangement&nbsp;:");
define("LANGMESS283","Contact&nbsp;:");
define("LANGMESS284","Situation&nbsp;familiale&nbsp;:");
define("LANGMESS285","Adresse&nbsp;:");
define("LANGMESS287","Code&nbsp;Postal&nbsp;:");
define("LANGMESS288","Ville&nbsp;:");
define("LANGMESS289","Email&nbsp;:");
define("LANGMESS290","Téléphone&nbsp;:");
define("LANGMESS291","Profession&nbsp;:");
define("LANGMESS292","Tél.&nbsp;Prof.&nbsp;:");
define("LANGMESS293","Sexe&nbsp;:");
define("LANGMESS294","Classe&nbsp;ant.&nbsp;:");
define("LANGMESS295","Année&nbsp;Scolaire");
define("LANGMESS296","Trim&nbsp;/&nbsp;Sem");
define("LANGMESS297","Bulletin");
define("LANGMESS298","Effectué&nbsp;le");
define("LANGMESS308","Permission non accordées");
define("LANGMESS309","Ajouter une information");
define("LANGMESS310","Entretien individuel");
define("LANGMESS311","Planifier abs/rtd");
define("LANGMESS312","Modifier abs/rtd");
define("LANGMESS313","Supprimer abs/rtd");
define("LANGMESS320","$email_eleve / $emailpro_eleve");
define("LANGMESS321","$tel_eleve / $tel_fixe_eleve");
define("LANGMESS256","Save");
define("LANGMESS257","All classes.");
define("LANGMESS258","Search");
define("LANGMESS299","    Titre : ");
define("LANGMESS300","Votre TRIADE n'est pas configuré en accès Internet, veuillez consulter votre compte administrateur Triade pour valider l'option de la connexion Internet.");
define("LANGMESS365","Actualités  de la 1er page");
define("LANGMESS301","Lien de la video : ");
define("LANGMESS302","ou Lien Youtube : ");
define("LANGMESS303","Gestion des émargements ");
define("LANGMESS304","Au niveau de la classe");
define("LANGMESS305","Emargement vierge");
define("LANGMESS306","Emargement vierge d'examen");
define("LANGMESS306","Emargement vierge d'examen");
define("LANGMESS307","Au niveau du groupe");
define("LANGMESS314","Emargement du jour ");
define("LANGMESS315","Emargement&nbsp;du&nbsp;");
define("LANGMESS316","Pour la classe : ");
define("LANGMESS317","Enseignant : ");
define("LANGMESS318","Tous les enseignants : ");
define("LANGMESS319","Hauteur des cellules des élèves");
define("LANGMESS322","Imprimer au format PDF");
define("LANGMESS323","Importer les photos au format ZIP");
define("LANGMESS324",": notes, absences, retards, dispences, sanctions, retenues, Brevets, Commentaires bulletin de l'élève, droits de scolarité, plan de classe, Brevets, Affectation stage");
define("LANGMESS325","Paramétrage  manuel : ");
define("LANGMESS326","Paramétrage  import : ");
define("LANGMESS332","Type du bulletin : ");
// VALIDER CHANGER PAR ENTER-->LANGMESS116
define("LANGMESS333","Valider");
define("LANGMESS334","Annuel"); /// voir si posible de mettre une variable
///////////////////////
//--------------------list_classe.php----- Voir comment changer le bouton Modifier
//--------------------list_matiere.php---- Voir comment changer le bouton Modifier
//--------------------listepreinscription.php
define("LANGMESS335","Liste des pré-inscriptions");
//--------------------reglement_ajout.php
define("LANGMESS336","Règlement intérieur");
define("LANGMESS337","règlement");
define("LANGMESS338","la ou les classe(s)");
define("LANGMESS339","la ou les classe(s)");
//--------------------affectation_visu.php
define("LANGMESS340","Année/Trimestre/Semestre");
define("LANGMESS341","Toute l'année");
define("LANGMESS342","Trimestre 1 / Semestre 1");
define("LANGMESS343","Trimestre 2 / Semestre 2");
define("LANGMESS344","Trimestre 3");
//--------------------affectation_modif_key.php
//----Modidifier le bouton suivant par next
//--------------------reglement_ajout.php
//--------------------reglement_liste.php
// comment modifier le lien Reglement interieur
//----------------/reglement_supp.php
define("LANGMESS345","Visualiser");
//-----------------vatel_list_ue.php
define("LANGMESS346","Gestion des Unités d'Enseignements");
define("LANGMESS347","Filtre : ");
define("LANGMESS348","Modifier");
define("LANGMESS349","Supprimer");
define("LANGMESS350","Nom UE");
define("LANGMESS351","Sem.");
define("LANGMESS352","Création d'une UE");
define("LANGMESS353","Fichier excel");
define("LANGMESS354","Contenu du fichier excel");
define("LANGMESS355","Commentaire des enseignants");
define("LANGMESS356","Visa direction");
define("LANGMESS357","Impression tableau de notes trimestriel ou semestriel");
define("LANGMESS358","Afficher le classement ");
define("LANGMESS359","Afficher les colonnes vides ");
define("LANGMESS360","Regroupement par module ");
define("LANGMESS361","Afficher les matières ");
define("LANGMESS362","Tableau des différentes moyennes au format excel");
define("LANGMESS374","Jusqu'au :");
define("LANGMESS375","Fichier Excel");
define("LANGMESS363","Visu<i>*</i>");
define("LANGMESS364","Unité Ens.");
define("LANGMESS369","Journal d'entretiens individuels");
define("LANGMESS370","Journal d'entretiens groupés ");
define("LANGMESS371","Tableau récapitulatif");
define("LANGMESS372","&nbsp;Enseignants&nbsp;");
define("LANGMESS373","&nbsp;Nombre&nbsp;d'heures&nbsp;");
define("LANGMESS376","Pour modifier / changer votre code d'accès, merci de consulter votre compte ");
define("LANGMESS377","administrateur Triade");
define("LANGMESS378","puis le module 'code d'accès'");
define("LANGMESS379","pas d'année");
define("LANGMESS380","Choix de la classe");
define("LANGMESS381","Choix des classes :");
define("LANGMESS383","Changement de classe pour les élèves en ");
define("LANGMESS384","Passage pour l'année scolaire");
define("LANGMESS385","Sans classe");
define("LANGMESS382","Liste des messages brouillons");
define("LANGMESS386","Bulletin&nbsp;personnalisé");
define("LANGMESS387","Bulletin définit pour les enseignants (et parents  prochainement)");
define("LANGMESS388","Visible pour la classe");
define("LANGMESS389","Autoriser l'accès aux bulletins pour les enseignants");

define("LANGMESST390","Merci de renseigner les informations nécessaires à Triade pour le site numéro 1 !!<br>Merci de confirmer en validant ou revalidant le formulaire suivant.");
define("LANGMESST391","Supprimer site");
define("LANGMESST392","Carnet de suivi");
define("LANGMESST393","COMPTE BLOQUE");
define("LANGMESST394","COMPTE EN PERIODE PROBATOIRE");
define("LANGMESST395","Supprimer la période probatoire");
define("LANGMESST396","Mise en période probatoire");
define("LANGMESST397","Saisie&nbsp;par");
define("LANGMESST398","Enregistrer cette liste");
define("LANGMESST399","Effectuer une recherche complexe");
define("LANGMESST700","Supprimer message en cours");
define("LANGMESST701","Actualités  de la 1er page");
define("LANGMESST702","Titre de la vidéo");
define("LANGMESST703","Copier/coller le lien ");
define("LANGMESST704","Indiquer le destinateur du message à transmettre.");
define("LANGMESST705","Message non envoyé ! \\n \\n Vous n'avez pas l'autorisation d'envoyer un message à cette personne.\\n\\n L'Equipe TRIADE. ");
define("LANGTMESS400","Votre demande a bien été pris en compte,");
define("LANGTMESS401","Veuillez consulter votre adresse email");
define("LANGTMESS402","Aucun compte pour cet email !!");
define("LANGTMESS403","merci de contacter votre administrateur en cliquant ");
define("LANGTMESS404","sur ce lien ");
define("LANGTMESS405","Contacter l'administrateur TRIADE ");
define("LANGTMESS406","Vérifier");
define("LANGTMESS407","Vérification / Check groupes");
define("LANGTMESS408","Email non valide !!");
define("LANGTMESS409","Merci d'indiquer un email valide.");
define("LANGTMESS410","Les emails <b>hotmail</b> ne sont pas reconnues par nos serveurs.");
define("LANGTMESS411","Merci d'indiquer une autre adresse email.");
define("LANGTMESS412","Nouveau Répertoire");
define("LANGTMESS413","Message déjà imprimé");
define("LANGTMESS414","Pièce jointe");
define("LANGTMESS415","Archiver dans");
define("LANGTMESS416","Boite de ");
define("LANGTMESS417","Boite de Réception");
define("LANGTMESS418","Mode Classique");
define("LANGTMESS419","Messages envoyées ");
define("LANGTMESS420","Vos répertoires ");
define("LANGTMESS421","via le mail ");
define("LANGTMESS422","via SMS ");
define("LANGTMESS423","via RSS ");
define("LANGTMESS424","Module lors de votre connexion");
define("LANGTMESS425","Module d'absenteisme");
define("LANGTMESS426","Liste d'une UE ( Modif / Suppr )");
define("LANGTMESS427","PDF EDT Enregistré");
define("LANGTMESS428","L'Equipe Triade");
define("LANGTMESS429","Image EDT Enregistrée");
define("LANGTMESS430","EDT Supprimé");
define("LANGTMESS431","Nom de structure déjà utilisé");
define("LANGTMESS432","Exportation format");
define("LANGTMESS433","&nbsp;Total&nbsp;");
define("LANGTMESS434","colonnes");
define("LANGTMESS435","Tuteur de stage");
define("LANGTMESS436","Afficher Adresse");
define("LANGTMESS437","Tous les parents");
define("LANGTMESS438","Tous les ");
define("LANGTMESS439","Lister / Modification");
define("LANGTMESS440","ajouter");
define("LANGTMESS441","Rangement / Info.");
define("LANGTMESS442","par mois");
define("LANGTMESS443","Nb mois");
define("LANGTMESS444","Code comptabilité");
define("LANGTMESS445","Universitaire");
define("LANGTMESS446","Editer le RIB");
define("LANGTMESS447","Donnée déjà enregistrée");
define("LANGTMESS448","Site rattaché");
define("LANGTMESS449","Définition compléte");
define("LANGCIV0","M.");
define("LANGCIV1","Mme");
define("LANGCIV2","Mlle");
define("LANGCIV3","Ms");
define("LANGCIV4","Mr");
define("LANGCIV5","Mrs");
define("LANGCIV6","M. ou Mme");
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
define("LANGCIV24","Dr");
define("LANGMESS391","Mode Classique");
define("LANGMESS392","Liste des destinataires");
define("LANGMESS393","Effacer liste"); // lg 262
define("LANGMESS394","Sélectionnez un fichier");
define("LANGMESS395","Liste des membres de la direction");
define("LANGMESS396","Visualiser / Modifier");
define("LANGMESS397","Liste de la Vie Scolaire");
define("LANGMESS398","Désactiver compte");
define("LANGMESS399","Activer compte");
define("LANGMESS400","Permission");
define("LANGMESS401","Liste des comptes personnels ");
define("LANGMESS403","Liste Tuteur de stage");
define("LANGMESS404","Liste / Modifier");
define("LANGMESS405","M.");
define("LANGMESS406","Mme");
define("LANGTMESS450","Traduction autre langue");
define("LANGTMESS451","Actuellement le fichier import sert de référence à la création du certificat.");
define("LANGTMESS452","Récupérer");
define("LANGTMESS453","Certificat numéro :");
define("LANGTMESS454","Ajouter une inscription :");
define("LANGTMESS455","Nouveau");
define("LANGTOUS","Tous");
define("LANGTMESS456","En attente");
define("LANGTMESS457","Accepté");
define("LANGTMESS458","Réfusé");
define("LANGTMESS459","Décision");
define("LANGTMESS460","Transferer liste en classe");
define("LANGTMESS461","Destruction fiche(s)");
define("LANGTMESS462","Attention !, le règlement doit être au format pdf et ne pas dépasser deux méga octé.");
define("LANGTMESS463","Cette option permet aux enseignants, de valider le réglement au moment de leur premiere connexion.");
define("LANGTMESS464","Elève(s) au total.");
define("LANGTMESS465","Commentaire pour le");
define("LANGTMESS466","Afficher les sous-matières");
define("LANGTMESS467","Prise en compte note examen");
define("LANGTMESS468","Prise en compte coef à zéro");
define("LANGTMESS469","Si le coefficient est à zéro, les points supérieurs à 10 seront pris en compte.");
define("LANGTMESS470","Spécif");
define("LANGTMESS471","Etude de cas");
define("LANGTMESS472","Visu : Visualisation dans le bulletin");
define("LANGTMESS473","pour l'année :");
define("LANGTMESS474","changer");
define("LANGTMESS475","Fichier Taille Max");
define("LANGTMESS476","Liste / Modifier un compte personnel");
define("LANGTMESS477","Liste / Modifier un tuteur de stage");

//--------------------list_classe.php
//--------------------modif_classe.php
define("LANGMESS407","Modification d'une classe");
define("LANGMESS408","Activer la classe");
define("LANGMESS409","Désactiver la classe");
define("LANGMESS410","Définition complète");
define("LANGMESS411","Site rattaché");
//--------------------affectation_creation.php
//-------------------publipostage.php
define("LANGMESS412","Type de vignette");
define("LANGMESS413","Type de membre");
//-------------------list_matiere.php
//-------------------modif_matiere.php
define("LANGMESS414","Type de membre");
define("LANGMESS415","Code matière");
define("LANGMESS416","Nom de la sous-matière");
define("LANGMESS417","Supprimer sous matière");
define("LANGMESS418","Désactiver matière");
define("LANGMESS419","Activer matière");
//-------------------triadev1/circulaire_liste.php
define("LANGMESS420","Référence");
//-------------------visu_retard_parent.php
//-------------------messagerie_envoi.php
define("LANGMESS421","Vous n'avez pas l'autorisation d'envoyer un message à cette personne.");
//-------------------information.php
define("LANGMESS422","Informations scolaires");
//-------------------parametrage.php
define("LANGMESS423","Module lors de votre connexion ");
define("LANGMESS424","Actualités");
define("LANGMESS425","Module d'absenteisme");
//-------------------retardprof.php
define("LANGMESS426","Indiquez des élèves en retard ou absent");
//-------------------retardprof2.php
define("LANGMESS427","Indiquer heure d'abs/rtd");
define("LANGMESS428","En ");
define("LANGMESS429","Horaire : ");


define("LANGTMESS478","Via code barre");
define("LANGTMESS479","Valider les présents");
define("LANGTMESS480","Visa direction");
define("LANGTMESS481","Commentaires pour les ".INTITULEELEVES);

define("LANGTMESS482","ACTUALITES - TRIADE");
define("LANGTMESS483","non disponible");
define("LANGTMESS484","Vos répertoires");
define("LANGTMESS485","Messages aux délégués");
define("LANGTMESS486","Modifier des circulaires");

define("LANGMESS430","L'année complète");
define("LANGMESS431","Avec notes partiel Vatel ");
define("LANGMESS432","Type du bulletin");
define("LANGMESS433","Enregistrement par code barre");
define("LANGMESS434","Valider les présents");
define("LANGMESS435","Courrier");
define("LANGMESS436","Relevés sans abs, ni rtd");
define("LANGMESS437","Listing des absences");
define("LANGMESS438","Absences par semaine");
define("LANGMESS439","Imprimer absences / retards");
define("LANGMESS440","Liste des présents");
define("LANGMESS441","Gestion abs/rtd via sconet");
define("LANGMESS442","Statistiques Abs / Rtd ");
define("LANGMESS443","Gestion des absences et retards d'un ".INTITULEELEVE);
define("LANGMESS444","Planifier&nbsp;");
define("LANGMESS445","&nbsp;Consulter&nbsp;/&nbsp;Modifier&nbsp;");
define("LANGMESS446","&nbsp;Supprimer&nbsp;");
define("LANGMESS447","Accéder");
define("LANGMESS448","&nbsp;Convertir&nbsp;abs.&nbsp;");
define("LANGMESS449","Configuration");
define("LANGMESS450","Gestion alertes");
define("LANGMESS451","Configuration créneau horaire ");
define("LANGMESS452","Configuration  SMS ");
define("LANGMESS453","Créditer des SMS");

define("LANGTMESS487","Avec notes vie scolaire");
define("LANGTMESS488","Rattrapage non validés");

define("LANGTRONBI30","Visualisation Trombinoscope du personnel");
define("LANGTRONBI20","Modifier Trombinoscope du personnel");

define("LANGSEXEF","F");
define("LANGSEXEH","H");
define("LANGHOM","Homme");
define("LANGFEM","Femme");

define("LANGTMESS489","Dupliquer l'EDT");
define("LANGTMESS490","Dupliquer l'EDT d'une classe vers une autre");
define("LANGTMESS491","Période à copier");
define("LANGTMESS492","Import du personnel de direction : ");
define("LANGTMESS493","Import des comptes du personnel : ");
define("LANGTMESS494","Import des entreprises : ");
define("LANGTMESS495","Import Spécif. IPAC : ");
define("LANGTMESS496","Import des matières : ");
define("LANGTMESS497","Module d'importation de fichier : ");
define("LANGTMESS498","Module d'importation de fichier Excel ");
define("LANGTMESS499","Le fichier excel à transmettre DOIT contenir 4 champs");
define("LANGTMESS500","Exemple fichier xls");
define("LANGTMESS501","Nombre de matière ajoutée : ");
define("LANGTMESS502","Dates Trimestrielles");
define("LANGTMESS503","Votre accès est actuellement désactivé.");
define("LANGTMESS504","Envoyer mot de passe par mail");
define("TITREACC1","parents");      // Info au niveau de la page d'accueil "Accès Parents"  
define("TITREACC2","Enseignants");  // Info au niveau de la page d'accueil "Accès Enseignants"  
define("TITREACC3","Vie scolaire"); // Info au niveau de la page d'accueil "Accès Vie scolaire"  
define("TITREACC4","Tuteur Stage"); // Info au niveau de la page d'accueil "Accès Tuteur Stage"  
define("TITREACC5","Personnels");   // Info au niveau de la page d'accueil "Accès Personnels"  
define("LANGTMESS505","Classe antérieures");
define("LANGTMESS506","Spécialisation");
define("LANGTMESS507","Sortie supplément au titre");
define("LANGTMESS508","Configuration supplément au titre");
define("LANGTMESS509","Gestion d'examen");
define("LANGTMESS510","Choix du document :");
define("LANGTMESS511","Récupérer le fichier ZIP Suppléments Titre");
define("LANGTMESS512","Niveau scolaire");
define("LANGTMESS513","Publipostage des sociétés ");
define("LANGTMESS514","Import des entreprises");
define("LANGTMESS515","Indemnité de stage");
define("LANGTMESS516","Suivi des demandes de convention");
define("LANGTMESS517","Gestion supplément au titre");
define("LANGTMESS518","Libellé :");
define("LANGTMESS519","Fichier");

define("LANGTMESS520","Nom du stage");
define("LANGTMESS521","En Entreprise le : ");
define("LANGTMESS522","Pays");
define("LANGTMESS523","Groupe hôtelier");
define("LANGTMESS524","Nombre d'étoiles");
define("LANGTMESS525","Nombre de chambres");
define("LANGTMESS526","Site web");
define("LANGTMESS527","Affectation de plusieurs étudiants à un stage");
define("LANGSTAGE100","Nom");
define("LANGSTAGE101","N° Stage");
define("LANGSTAGE102","Entreprise");
define("LANGSTAGE103","Service");
define("LANGSTAGE104","Indemnité");
define("LANGSTAGE105","Logé");
define("LANGSTAGE106","Nourri");
define("LANGSTAGE107","Valider");
define("LANGSTAGE108","Stage personnalisé");
define("LANGSTAGE109","Pays");
define("LANGSTAGE110","Tuteur de stage");
define("LANGSTAGE111","Langue parlé durant le stage");
define("LANGSTAGE112","Intitulé du service");
define("LANGSTAGE113","Indemnités de stage");
define("LANGSTAGE114","Horaires journaliers");
define("LANGSTAGE115","Les conventions de stage");
define("LANGSTAGE116","Sortie des conventions groupées");

define("LANGTMESS528","Langue de la classe");
define("LANGTMESS529","Retour classe");
define("LANGTMESS530","Récuperation des conventions de stage");


define("LANGVATEL1","D&eacute;connexion");
define("LANGVATEL2","Me connecter");
define("LANGVATEL3","Mot de passe oubli&eacute;");
define("LANGVATEL4","Ecris ton email");
define("LANGVATEL5","Ecris ton mot de passe");
define("LANGVATEL6","Semestre");
define("LANGVATEL7","Abs/Rtd/Sanction");
define("LANGVATEL8","Absences / Retards / Sanctions");
define("LANGVATEL9","Absences");
define("LANGVATEL10","Retards");
define("LANGVATEL11","Sanctions");
define("LANGVATEL12","Description des faits");
define("LANGVATEL13","<");
define("LANGVATEL14",">");
define("LANGVATEL15","Mois");
define("LANGVATEL16","R&eacute;initialiser votre mot de passe");
define("LANGVATEL17","Mot de passe oubli&eacute; ?");

define("LANGVATEL18","Acc&egrave;s Etudiant");
define("LANGVATEL19","Acc&egrave;s Enseignant");
define("LANGVATEL20","Acc&egrave;s Personnel");

define("LANGVATEL21","Ajouter");
define("LANGVATEL22","Modifier");
define("LANGVATEL23","Supprimer");
define("LANGVATEL24","Visualiser");
define("LANGVATEL25","Quoi de neuf ?");
define("LANGVATEL26","Notes");
define("LANGVATEL27","Statistiques de ce devoir");
define("LANGVATEL28","IMPOSSIBLE");
define("LANGVATEL29","Semestre déjà passé.");
define("LANGVATEL30","Ajouter élève");
define("LANGVATEL31","Ajouter une note à un élève pour ce devoir.");
define("LANGVATEL32","Retour sur la liste des devoirs");
define("LANGVATEL33","Emploi du temps");
define("LANGVATEL34","Absentéisme");
define("LANGVATEL35","absent(s) signé(s)");
define("LANGVATEL36","Calendrier");
define("LANGVATEL37","Problème d'enregistrement");
define("LANGVATEL38","Indiquer la date");


define("LANGMESSE01","Accès à votre compte SMS");
define("LANGMESSE02","Gestion des SMS"); 

define("LANGNEW100","Sanction(s)");
define("LANGNEW101","Prévision sur ");

?>
