<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Novembre 2016
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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

// fichièr per lenga cote admin.
// POUR TOUS -------------------
// brmozilla($_SESSION[navigateur]);
//

function TextNoAccentLicence2($Text){
	 Return (strtr($Text, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"));
}

if (!defined(INTITULEDIRECTION)) { define("INTITULEDIRECTION","direccion"); }
if (!defined(INTITULEELEVE)) { define("INTITULEELEVE","escolan"); }
if (!defined(INTITULEELEVES)) { define("INTITULEELEVES","escolans"); }



define("CLICKICI","Clicatz aicí");
define("VALIDER","Validar");
define("LANGTP22","INFORMACION - Demanda de D.S.T. de confimar !");
define("LANGTP3"," calendièr DST ");
define("LANGCHOIX","Causida ...");
define("LANGCHOIX2","pas cap de classa");
define("LANGCHOIX3","--- Causida ---");
define("LANGOUI","òc");
define("LANGNON","non");
define("LANGFERMERFEN","Tampar la fenèstra");
define("LANGATT","ATENCION !");
define("LANGDONENR","Donada(s) enregistrada(s)");
define("LANGPATIENT","Mercé de pacientar");
define("LANGSTAGE1",'Gestion dels estagis professionals');
define("LANGINCONNU",'desconegut'); // deu èstre identique que langdesconegut cote javascript
define("LANGABS",'abs');
define("LANGRTD",'rtd');
define("LANGRIEN",'pas res');
define("LANGENR",'Enregistrar');
define("LANGRAS1",'Uèi, le ');
define("LANGDATEFORMAT",'jj/mm/aaaa');

//------------------------------
// títol
//-------------------------------

define("LANGTITRE3","Messatge desfilant dins le naut de la pagina");
define("LANGTITRE4","Messatge desfilant dins le bendèl ");
define("LANGTITRE5","Réception messatge");
define("LANGTITRE6","Creacion d'un compte ".INTITULEDIRECTION);
define("LANGTITRE7","Creacion d'un compte vida escolara");
define("LANGTITRE8","Creacion d'un compte ensenhant");
define("LANGTITRE9","Creacion d'un compte suplent");
define("LANGTITRE10","Creacion d'un compte ".INTITULEELEVE);
define("LANGTITRE11","Creacion d'un grop"); //
define("LANGTITRE12","Creacion d'una classa"); //
define("LANGTITRE13","Creacion d'una matèria"); //
define("LANGTITRE14","Creacion d'una sosmatèria"); //
define("LANGTITRE16","Creacion d'afectacion");
define("LANGTITRE17","Creacion d'afectacion per la classa");
define("LANGTITRE18","Visualizacion d'afectacion");
define("LANGTITRE19","Modificacion d'afectacion");
define("LANGTITRE20","Modificacion de l'afectacion per la classa");
define("LANGTITRE21","Supression d'afectacion");
define("LANGTITRE22","Importacion d'un fichièr ASCII (txt,csv) ");
define("LANGTITRE23","Lista dels retards pas justificats ");
define("LANGTITRE24","Apondre una dispensa");
define("LANGTITRE25","Listar / Modificar les  dispensas");
define("LANGTITRE26","Suprimir una dispensa");
define("LANGTITRE27","Gestion dispensas -  Planificacion");
define("LANGTITRE28","Afichatge / Modificacion des dispensas");
define("LANGTITRE29","Consultacion de las classas");
define("LANGTITRE30","Recèrca d'un ".INTITULEELEVE);
define("LANGTITRE31","Importacion del fichièr GEP");
define("LANGTITRE32","Trombinoscòpi dels ".INTITULEELEVE."s");
define("LANGTITRE33","Certificat d'escolaritat");

//------------------------------
define("LANGTE1","Títol");
define("LANGTE2","del");
define("LANGTE3","de");
define("LANGTE4","Nombre de caractèrs");
define("LANGTE5","Objècte");
define("LANGTE6","A");
define("LANGTE6bis","Als parents de ");
define("LANGTE7","Data");
define("LANGTE8","Supression messatges");
define("LANGTE9","legit");
define("LANGTE10","fins al :");
define("LANGTE11","al ");
define("LANGTE12","le ");
define("LANGTE13","a");
define("LANGTE14","Al grop ");

//------------------------------
define("LANGFETE","Bona Fèsta als ");
define("LANGFEN1","Eveniment(s) del jorn");
define("LANGFEN2","D.S.T. del jorn");
//------------------------------
define("LANGLUNDI","Diluns");
define("LANGMARDI","Dimars");
define("LANGMERCREDI","Dimècres");
define("LANGJEUDI","Dijòus");
define("LANGVENDREDI","Divendres");
define("LANGSAMEDI","Dissabte");
define("LANGDIMANCHE","Dimenge");
// ------------------------------
define("LANGMESS1","Mandadís d'un messatge - le ");
define("LANGMESS3","Messatge a la vida escolara : ");
define("LANGMESS4","Messatge a un ensenhant : ");
define("LANGMESS6","Messatge(s) mandat(s)");
define("LANGMESS7","Actualitat enregistrada");
define("LANGMESS8","Messatge(s) mandat(s)");
define("LANGMESS9","Respondre al messatge - le ");
define("LANGMESS10",'Las datas trimestralas son pas enregistradas.');
define("LANGMESS11",'Prevenètz la '.INTITULEDIRECTION.'.');
define("LANGMESS12",'per fin de validar las datas trimestralas.');
define("LANGMESS13",'Clicatz <a href="definir_trimestre.php">aicí</a>');
define("LANGMESS14",'Las afectacions d\'aquesta classa  son pas enregistradas.');
define("LANGMESS15",'Clicatz <a href="affectation_creation_key.php">aicí</a>');
define("LANGMESS16",'per fin de validar las afectacions d\'aquesta classa ');
define("LANGMESS17","Configuracion");
define("LANGMESS18","S");     // primièra letra de la phrase seguenta !!!
define("LANGMESS18bis","i a mantun emails a declarar,<br> separar les emails per una virgula.");
define("LANGMESS19","Activé");
define("LANGMESS20","Configuracion mesa a jorn");
define("LANGMESS21","Èsser avertit d'un messatge recebut sus la vòstra messatjariá ");
define("LANGMESS22","Mandar messatge a un grop <font class=T1>(Ens,Vs,Dir)</font> : ");
define("LANGMESS23","Creacion d'un grop mail ");
define("LANGMESS24","Indicar las personas del grop ");
define("LANGMESS25","Seleccionar las diferentas personas en mantenent la tòca"); //
define("LANGMESS26","Validar la creacion");
define("LANGMESS27","Grop de mail creat");
define("LANGMESS28","Lista dels vòstres gropes mail ");
define("LANGMESS29","Grop ");
define("LANGMESS30","Lista de las personas ");
define("LANGMESS31","Messatge de ");
define("LANGMESS32","Avètz actualament ");
define("LANGMESS33","messatge(s) en espèra ");

// -----------------------------
// boton
// PAS DE -->' (cote) !!!!
define("LANGBTS","Seguent >");
define("LANGBT1","Enregistrar le messatge desfilant");
define("LANGBT2","Enregistrar informacion");
define("LANGBT3","Quitar sens mandar");
define("LANGBT4","Mandar messatge");
define("LANGBT5","Pacientatz, S.V.P.");
define("LANGBT6","Suprimir les messatges marcats");
define("LANGBT7","Enregistrar le compte");
define("LANGBT11","Lista dels suplents");
define("LANGBT12","Lista dels gropes");
define("LANGBT13","Validar la o las classa(s)");
define("LANGBT14","Enregistrar la creacion");
define("LANGBT15","Lista de las classas");
define("LANGBT16","Lista dels matèrias");
define("LANGBT17","Enregistrar la sosmatèria");
define("LANGBT18","Enregistrar l'estatut"); //
define("LANGBT19","Validar"); //
define("LANGBT20","Quitar sens enregistrar"); //
define("LANGBT21","Enregistrar afectacion"); //
define("LANGBT22","Suprimir afectacion"); //
define("LANGBT23","Mandar le fichièr"); //
define("LANGBT24","Recomençar"); //
define("LANGBT25","Reactualizar la pagina"); //
define("LANGBT26","Crear una classa"); //
define("LANGBT27","Planificar abs o retard"); //
define("LANGBT28","Consultar"); //
define("LANGBT29","Suprimir abs o retard"); //
define("LANGBT30","Validar la mesa a jorn"); //
define("LANGBT31","Validar");
define("LANGBT32","Suprimir dispensas");
define("LANGBT33","Modificar dispensas");
define("LANGBT34","Apondre dispensas");
define("LANGBT35","Enregistrar la donada de ");
define("LANGBT36","Dispensa  modificada --  L'equipa TRIADE");
define("LANGBT37","Transmetre informacion");
define("LANGBT38","Mandar");
define("LANGBT39","Aviar la recèrca");
define("LANGBT40","Recuperacion");
define("LANGBT41","Acabat");
define("LANGBT42","Validar les ".INTITULEELEVE."s pas enregistrats");
define("LANGBT43","Imprimir le bulletin");
define("LANGBT44","Istoric");
define("LANGBT45","Consultar la documentacion");
define("LANGBT46","Enregistrar la fòto");
define("LANGBT47","Autre cambiament");
define("LANGBT48","Quitar aqueste modul");
define("LANGBT49","Editar tota la classa");
define("LANGBT50","Suprimir");
define("LANGBT51","Validar demanda D.S.T");
// -----------------------------
define("LANGCA1","M"); //
define("LANGCA1bis","essatge pas encara legit"); // sens la primièra letra
define("LANGCA2","M"); //
define("LANGCA2bis","essatge ja legit"); // sens la primière letra
define("LANGCA3","I"); //
define("LANGCA3bis","ndicatz le JJ/MM/AAAA  <BR> Dins le cas d\'una data pas <BR>convenguda, precisatz la mencion <br>"); // sens la primière letra
// -----------------------------
define("LANGNA1","Nom"); //
define("LANGNA2","Petit nom"); //
define("LANGNA3","Senhal"); //
define("LANGNA4","Novèl compte creat \\n\\n L'equipa TRIADE "); //
define("LANGNA5","Remplaçament&nbsp;de&nbsp;"); //
// -----------------------------
define("LANGELE1","Rensenhaments sus l'".INTITULEELEVE); //
define("LANGELE2","Nom"); //
define("LANGELE3","Petit nom"); //
define("LANGELE4","Classa"); //
define("LANGELE5","Opcion"); //
define("LANGELE6","Regim"); //
define("LANGELE7","Intèrne"); //
define("LANGELE8","Semipensionari"); //
define("LANGELE9","Extèrne"); //
define("LANGELE10","Data de naissença"); //
define("LANGELE11","Nacionalitat"); //
define("LANGELE12","Numèro estudiant"); //
// define("LANGELE12","Numèro national"); //
define("LANGELE13","Rensenhaments sus la familha"); //
define("LANGELE14","Adreça 1"); //
define("LANGELE15","Còdi postal"); //
define("LANGELE16","Comuna"); //
define("LANGELE17","Adreça 2"); //
define("LANGELE18",""); //
define("LANGELE19",""); //
define("LANGELE20","Numèro de telefòn"); //
define("LANGELE21","Profession del paire"); //
define("LANGELE22","Telefòn del paire"); //
define("LANGELE23","Profession de la maire"); //
define("LANGELE24","Telefòn de la maire"); //
define("LANGELE25","Ecole anteriora"); //
define("LANGELE26","Nom de l'establiment"); //
define("LANGELE27","Numèro establiment"); //
define("LANGELE28","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." creat -- L'equipa TRIADE"); //
define("LANGELE29","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." ja existant  -- L'equipa TRIADE"); //
//------------------------------------------------------------
define("LANGGRP1","Intitulat del grop"); //
define("LANGGRP2","Indicatz las classas per la creacion del grop"); //
define("LANGGRP3","Seleccionatz las diferentas classas en mantenent la tòca"); //
define("LANGGRP4","Ctrl"); //
define("LANGGRP5","et en appuyant sul boton gauche de la souris."); //
define("LANGGRP6","Intitulat de la seccion"); //
define("LANGGRP7","Novèla classa creada -- L'equipa TRIADE"); //
define("LANGGRP8","Novèla matèria creada -- L'equipa TRIADE"); //
define("LANGGRP9","Intitulat de la matèria"); //
define("LANGGRP10","Nom de la sosmatèria"); //
//------------------------------------------------------------
//------------------------------------------------------------
define("LANGAFF1","Afectacion per la classa"); //
define("LANGAFF2","!! La creacion d'afectacion <u>suprimís</u> totas las nòtas de la classa !!</u>"); //
define("LANGAFF3","Afectacion de las classas"); //
//------------------------------------------------------------
define("LANGPER1","Impression de periòde"); //
define("LANGPER2","Començament del periòde"); //
define("LANGPER3","Fin del periòde"); //
define("LANGPER4","Seccion"); //
define("LANGPER5","Recuperar le fichièr PDF"); //
define("LANGPER6","Ensenhant "); //
define("LANGPER8","en classa de "); //
define("LANGPER9","Modul d'afectacion de las classas."); //
define("LANGPER10","ATENCION aqueste modul es a utilizar al moment d'una novèla afectacion,<br> destrutz totas las nòtas dels ".INTITULEELEVE."s  de las classas afectadas."); //
define("LANGPER11","ATENCION, las nòtas de las classas seleccionadas  seràn suprimidas. \\n Volètz contunhar ? \\n\\n Equipa TRIADE"); //
define("LANGPER12","Indicatz le còdi d'accès.");
define("LANGPER13","Verification del còdi");
define("LANGPER14","Nombre de matèrias");
define("LANGPER15","Creacion d'afectacion per la classa");
define("LANGPER16","Nb");
define("LANGPER17","Matèria");
define("LANGPER18","Ensenhant");
define("LANGPER19","Coef");
define("LANGPER20","Grop");
define("LANGPER21","Lenga");
define("LANGPER22","Imprimir aquesta pagina");
define("LANGPER23","afectacion");
define("LANGPER23bis","reüssida");  // afectacion xxxx reüssida
define("LANGPER24","interrompuda"); // afectacion xxxx interrompue
define("LANGPER25","Classa");
define("LANGPER26","Visualizacion");
define("LANGPER27","Visualizar");
define("LANGPER28","Visualizacion d'afectacion per la classa");
define("LANGPER29","!! La modificacion d'afectacion <u>suprimís</u> totas las nòtas de la classa !!");
define("LANGPER30","Modificar");
define("LANGPER31","Modificar l'afectacion");
define("LANGPER32","Modificacion d'afectacion");
define("LANGPER32bis","interrompuda"); // Modificacion d'afectacion xxxx interrompue
define("LANGPER33","Supression de l'afectacion  per la ");
define("LANGPER34","!! La supression d'afectacion <u>suprimís</u> totas las nòtas de la classa !!</u>");
define("LANGPER35","Afectacion de la classa");
define("LANGPER35bis","suprimida"); // Afectacion de la classa  xxxx suprimida
//------------------------------------------------------------------------------
define("LANGIMP1","Importacion d'una basa existenta ");
define("LANGIMP2","Indicar le type del fichièr a importar ");
define("LANGIMP3","Fichièr ASCII ");
define("LANGIMP4","Fichièr GEP ");
define("LANGIMP5","Modul d'importacion de fichièr ASCII.");
define("LANGIMP6","Le fichièr de transmetre <FONT color=RED><B>DEU</B></FONT> contenir <FONT COLOR=red><B>43</B></FONT> camps <I>(voids o pas voids)</I> separats per un meteis separador le \"<FONT color=red><B>;</B></font>\" <I>Soit la preséncia de 42 còps le caractèr \"<FONT color=red><B>;</B></font>\"</I>");
define("LANGIMP7","Aquí l'òrdre dels camps d'indicar : ");
define("LANGIMP8","nom");
define("LANGIMP9","petit nom");
define("LANGIMP10","classa");
define("LANGIMP11","regim");
define("LANGIMP12","data naissença");
define("LANGIMP13","nacionalitat");
define("LANGIMP14","nom tutor");
define("LANGIMP15","petit nom tutor");

define("LANGIMP16","adreça&nbsp;1");
define("LANGIMP18","còdi postal&nbsp;1");
define("LANGIMP19","comuna&nbsp;1");

define("LANGIMP17","adreça&nbsp;2");
define("LANGIMP18_2","còdi postal&nbsp;2");
define("LANGIMP19_2","comuna&nbsp;2");


define("LANGIMP20","telefòn");
define("LANGIMP21","profession paire");
define("LANGIMP22","telefòn profession paire");
define("LANGIMP23","profession maire");
define("LANGIMP24","telefòn profession maire");
define("LANGIMP25","numèro establiment");

define("LANGIMP26","lv1");
define("LANGIMP27","lv2");
define("LANGIMP28","option");
define("LANGIMP29","Numèro ".INTITULEELEVE);
define("LANGIMP30","ATENCION, la destruccion de la basa serà automatica. \\n Volètz contunhar ? \\n\\n L\'Equipa TRIADE");
define("LANGIMP31","ATENCION : aqueste modul es a utilizar al moment de la primièra utilizacion,<br> destrutz totas las informacions dels ".INTITULEELEVE."s (nòtas, bulletins, vida escolara).<br /> * camp obligatòri");
define("LANGIMP39","Indicar le fichièr de transmetre ");
define("LANGIMP40","Fichièr transmis -- L'equipa TRIADE ");
define("LANGIMP41","Le nombre de camps es pas respectat ");
define("LANGIMP42","Indicar per cada referéncia la classa correspondenta ");
define("LANGIMP43","Fichièr pas enregistrat ");
// ------------------------------------------------------------------------------
define("LANGABS1","Gestion abséncias - retards del jorn");
define("LANGABS2","Planificar una abséncia o retard");
define("LANGABS3","Indicar le nom de l'".INTITULEELEVE);
define("LANGABS4","Listar las abséncias o retards pas justificats");
define("LANGABS5","Abséncias pas justificadas");
define("LANGABS6","Retards pas justificats");
define("LANGABS7","Visualizar e/o modificar una abséncia o retard");
define("LANGABS8","Indicar le nom de l'".INTITULEELEVE);
define("LANGABS9","Afichar e/o suprimir una abséncia o retard");
define("LANGABS10","pas cap d'escolan dins la basa de donadas");
define("LANGABS11","Abs/Rtd");
define("LANGABS12","Motiu");
define("LANGABS13","En retard le");
define("LANGABS14","Rtd");
define("LANGABS15","Abs");
define("LANGABS16","Anullar");
define("LANGABS17","Modificar abs o retard");
define("LANGABS18","Absent&nbsp;del&nbsp;");
define("LANGABS19","au&nbsp;");
define("LANGABS20","Abs/Rtd");
define("LANGABS21","Durada");
define("LANGABS22","Motiu");
define("LANGABS23","Ora / Data");
define("LANGABS24","Mesa en plaça de las abséncias o retards en Classa de ");
define("LANGABS25","Gestion Abséncia - Retard");
define("LANGABS26","Gestion Abséncia - Retard  Planificacion");
define("LANGABS27","Enregistrar la donada de ");
define("LANGABS28","Donada(s) Enregistrada(s) ");
define("LANGABS29","D"); //primière letra
define("LANGABS29bis","ispensat(-ada) de :"); //seguida
define("LANGABS30","Disp");
define("LANGABS31","classa de ");
define("LANGABS32","R"); //primière letra
define("LANGABS32bis","etard "); //seguida
define("LANGABS33","en");
define("LANGABS34","de");
define("LANGABS35","Abséncia - Retard - dispensa  del ");
define("LANGABS36","Mesa a jorn");
define("LANGABS37","Imprimir las abséncias, dispensas, retards, del jorn ");
define("LANGABS38","T&eacute;l.");
define("LANGABS39","Tel. Prof Paire ");
define("LANGABS40","Tel. Prof Maire");
define("LANGABS41","Tel. Dom ");
define("LANGABS42","Absent(e)  del ");
define("LANGABS43","pendent ");
define("LANGABS44","Jorn(s) ");
define("LANGABS45","Enregistrar la mesa a jorn ");
define("LANGABS46","a partir del ");

define("LANGDISP8","Supression dispensa");
//----------------------------------------------------------------------------
define("LANGPROJ1","Causida de la classa");
define("LANGPROJ2","Causida del trimèstre");
define("LANGPROJ3","Trimèstre 1");
define("LANGPROJ4","Trimèstre 2");
define("LANGPROJ5","Trimèstre 3");
define("LANGPROJ6","<font class=T2>Aucun ".INTITULEELEVE." dins aquesta classa</font>");
define("LANGPROJ7","Nombre de retards");
define("LANGPROJ8"," Cumul");
define("LANGPROJ9","Disciplina");
define("LANGPROJ10","minutas");
define("LANGPROJ11","Nbr de retengudas");
define("LANGPROJ12","atr.&nbsp;per&nbsp;");
define("LANGPROJ13","Lista");
define("LANGPROJ14","Mej ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGPROJ15","Mej Classa");
define("LANGPROJ16","Mejana ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
// ----------------------------------------------------------------------------
define("LANGDISP1","<font class=T2>pas cap de ".INTITULEELEVE." a aqueste nom</font>");
define("LANGDISP2","Motiu");
define("LANGDISP3","Certificat medical");
define("LANGDISP4","Periòde&nbsp;del&nbsp;");
define("LANGDISP5","en matèria ");
define("LANGDISP6","Ora de dispensa ");
define("LANGDISP7","<B><font color=red>I</font></B>ndicatz le JJ/MM/AAAA  <BR> dins les 2 camps");
define("LANGDISP9","Afichatge <b>complet</B> de las dispensas");
define("LANGDISP10","En");
// ----------------------------------------------------------------------------
define("LANGASS1","TRIADE assisténcia");
define("LANGASS2","Vous propose un  servici per vos depanar, vos ajudar dins la vòstra utilizacion  de TRIADE.<br /><br />Avètz un problèma sus un des servicis de TRIADE, esitetz pas a nos transmetre pel formulari que seguís, las informacions sul servici en question. Les nòstres ingenhiaires se cargaràn de verificar aqueste servici.");
define("LANGASS3","Membre concernit");
define("LANGASS4","Administracion");
define("LANGASS5","Ensenhant");
define("LANGASS6","Vida Escolara");
define("LANGASS6bis","Parent");
define("LANGASS7","Action");
define("LANGASS8","Creacion");
define("LANGASS9","Visualizacion");
define("LANGASS10","Supression");
define("LANGASS11","Autre");
define("LANGASS12","Servici");
define("LANGASS13","Compte utilizaire");
define("LANGASS14","Messatjariá");
define("LANGASS15","Afectacion");
define("LANGASS16","Basa de donadas");
define("LANGASS17","Classa");
define("LANGASS18","Matèria");
define("LANGASS19","Recèrca");
define("LANGASS20","D.S.T.");
define("LANGASS21","Planning");
define("LANGASS22","Dispensa");
define("LANGASS23","Disciplina");
define("LANGASS24","Circulara");
define("LANGASS25","Bulletin");
define("LANGASS26","Periòde");
define("LANGASS27","Comentari");
define("LANGASS28","TRIADE assisténcia vous remercie per la vòstra aide.");
define("LANGASS29","Equipa TRIADE.");
define("LANGASS30","L'equipa TRIADE a le vòstre servici");
define("LANGASS31","TRIADE es un produit unic e inedit, tanben, esitetz pas a nos transmetre le les vòstres conselhs e suggestions per fin que le site responde a las espèras vertadièras dels utilizaires ! Mercé a vos :-)");
define("LANGASS32","Livre d'or");
define("LANGASS33","Le vòstre testimoniatge en direct : inscrivètz las vòstras remarcas sul nòstre libre d'aur.");
define("LANGASS34","Le vòstre messatge nos es estat mandat, mancarem pas de vos respondre.<br> <BR>Mercé d'utilizar TRIADE e a lèu.<BR><BR><BR><UL><UL>L'equipa TRIADE.<BR>");
define("LANGASS35","Autre");
define("LANGASS36","SMS");
define("LANGASS37","WAP");
define("LANGASS38","Trombinoscòpi");
define("LANGASS39","Còdi barras");
define("LANGASS40","Estagi Pro.");
// -----------------------------------------------------------------------------
define("LANGRECH1","<font class=T2>pas cap de ".INTITULEELEVE." dins la classa</font>");
define("LANGRECH2","Recèrca de ");
define("LANGRECH3","<font class=T2>pas cap de ".INTITULEELEVE." per aquesta recèrca</font>");
define("LANGRECH4","Informacion / Modificacion");
// ---------------------------------------------------------------------------------
define("LANGBASE1","ATENCION : aqueste modul es a utilizar al moment de la primièra utilizacion,<br> destrutz totas las informacions dels ".INTITULEELEVE."s  (nòtas, bulletins, vida escolara).");
define("LANGBASE2"," Los fichièrs d'importar DEVON èstre al format dbf ");
define("LANGBASE3","Aquí la lista dels fichièrs ");
define("LANGBASE4","Modul d'importacion des fichièrs GEP ");
define("LANGBASE5","Importacion d'una basa GEP ");
define("LANGBASE6","Total d'".INTITULEELEVE."s dins le fichièr DBF ");
define("LANGBASE7","Total d'".INTITULEELEVE."s en classa ");
define("LANGBASE8","Total d'".INTITULEELEVE."s sens classa ");
define("LANGBASE9","Recuperacion dels senhals  ");
define("LANGBASE10","Impossible de dobrir le fichièr F_ele.dbf");
define("LANGBASE11","Basa de donadas traitée -- L'equipa TRIADE");
define("LANGBASE12","Le fichièr seleccionat es pas valide !");
define("LANGBASE13","Aquí la lista dels senhals");
define("LANGBASE14","Recuperar la lista en seleccionnant l'ensemble de las linhas e efectuatz un copiar/pegar dins un fichièr \"txt\".");
define("LANGBASE15","Puèi via excel o OpenOffice, recuperar le fichièr \"txt\"  en precisant le punt virgula coma separador de camps.");
define("LANGBASE17"," Atencion : les senhals son pas accessibles que sus <br />aquesta pagina !! Pensatz a recuperar la lista <b>ABANS</b> d'Acabar ");
define("LANGBASE18","INFORMACION PAS DISPONIBLA");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGBULL1","Impression bulletin trimestral");
define("LANGBULL2","Indicatz la classa");
define("LANGBULL3","Annada scolaire");
define("LANGBULL4","<a href=\"#\" onclick=\"open('./accrobat.php','acro','width=500,height=350')\"><b><FONT COLOR=red>ATENCION</FONT></B> Besonh de l'aisina <B>Adobe Acrobat Reader</B>.  Logicial e telecargament gratuits  clicatz <B>ICI</B></A>");
// -----------------------------------------------------------------------------------------------------------------------
define("LANGPARENT1","aucun messatge");
define("LANGPARENT2","Aucun delegat afectat pel moment");
define("LANGPARENT3","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."(s) delegat(s)");
define("LANGPARENT4","Parent(s) delegat(s)");
define("LANGPARENT5","Lista dels delegats");
//----------------------------------------------------------------------//
define("LANGPUR3","ATENCION: aqueste modul es a utilizar <br>quand volètz escafar de donadas TRIADE.");
define("LANGPUR4","ATENCION, Dintratz dins un modul que per la seguida suprimirà de donadas qu'auretz causidas. \\n Volètz contunhar ? \\n\\n L\'equipa TRIADE");
define("LANGPUR5","Las donadas son suprimidas");
define("LANGPUR6","Informacion : La seleccion \"".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."\" implique automaticament la supression de las nòtas, abséncias, disciplinas, dispensas, retards, entretiens");
define("LANGPUR7","Indicar l'element o les elements  a  détruire : ");
define("LANGPUR8","A conservar");
define("LANGPUR9","A Suprimir");
//----------------------------------------------------------------------//
define("LANGCHAN0","Modul pel cambiament de classa d'un o de mantun ".INTITULEELEVE."s");
define("LANGCHAN1","ATENCION: aqueste modul es a utilizar <br>quand volètz efectuar <br> un cambiament de classa per les ".INTITULEELEVE."s");
define("LANGCHAN3","ATENCION, l\'ensemble de las donadas de l\'".INTITULEELEVE." \\n o dels ".INTITULEELEVE."s concernit(s) pel cambiament de classa serà suprimit");
//----------------------------------------------------------------------//
define("LANGGEP1",'Importacion del fichièr GEP');
define("LANGGEP2",'Indicatz le fichièr');
//----------------------------------------------------------------------//
define("LANGCERT1"," telecargar aqueste certificat ");
//----------------------------------------------------------------------//
define("LANGPROFR1",'Indicatz de '.INTITULEELEVE.'s en retard');
define("LANGPROFR2",'Mesa en plaça dels retards  ');
define("LANGKEY1",'<font class=T1>Pas de clau d\'enregistrament </font>');
define("LANGDISP20",'Apondre dispensas');
define("LANGPROFA",'<br><center><font size=2>Pas de clau d\'enregistrament </font><br><br>Contactatz le vòstre administrator TRIADE, <br>per fin de validar la demanda d\'enregistrament de TRIADE. </center><br><br>');
define("LANGPROFB",'Apondon d\'una nòta en ');
define("LANGPROFC",'Confirmatz l\'enregistrament de las nòtas ');
define("LANGPROFD",'Validatz l\'enregistrament de las nòtas');
define("LANGPROFE",'&nbsp;&nbsp;<i><u>Info</u>: La tòca Entrada vos permet de passar automaticament a la nòta seguenta.</i>');
define("LANGPROFF",'Apondon d\'una nòta');
define("LANGPROFG",'Indicar la classa');
//----------------------------------------------------------------------//
define("LANGMETEO1",'JORN');
define("LANGMETEO2",'NUÈIT');
//----------------------------------------------------------------------//
define("LANGPROFP1","Messatge per la classa");
define("LANGPROFP2","Enregistrar le messatge");
define("LANGPROFP3","Messatge del Professor Principal");
//----------------------------------------------------------------------//
// Modul Estagi Pro
define("LANGSTAGE1","Planificacion dels estagis ");
define("LANGSTAGE2","Visualizar las datas dels estagis ");
define("LANGSTAGE3","Apondre ");
define("LANGSTAGE4","Afectar ");
define("LANGSTAGE5","Insercion d'una data d'estagi ");
define("LANGSTAGE6","Modificacion  d'una data d'estagi ");
define("LANGSTAGE7","Suprimir una data d'estagi ");
define("LANGSTAGE8","Gestion de las entrepresas ");
define("LANGSTAGE9","Visualizar las diferentas entrepresas ");
define("LANGSTAGE10","Apondre una entrepresa ");
define("LANGSTAGE11","Modificar una entrepresa ");
define("LANGSTAGE12","Suprimir una entrepresa ");
define("LANGSTAGE13","Gestion dels ".INTITULEELEVE."s ");
define("LANGSTAGE14","Visualizar les ".INTITULEELEVE."s en entrepresa ");
define("LANGSTAGE15","Afectar un ".INTITULEELEVE." a una entrepresa ");
define("LANGSTAGE16","Modificar las caracteristicas d'un ".INTITULEELEVE." ");
define("LANGSTAGE17","Suprimir l'atribucion d'un ".INTITULEELEVE." ");
define("LANGSTAGE18","Visualizacion des datas d'estagi");
define("LANGSTAGE19","Estagi");
define("LANGSTAGE20","Recèrca d'entrepresas");
define("LANGSTAGE21","Consultar las entrepresas per activitat");
define("LANGSTAGE22","Consultacion des entrepresas");
//----------------------------------------------------------------------//
define("LANGGEN1","Administracion");
define("LANGGEN2","Vida Escolara");
define("LANGGEN3","Ensenhants");
//----------------------------------------------------------------------//
define("LANGDST1","Demanda de D.S.T");
define("LANGDST2","Bonjorn, <br> <br> La vòstra demanda de Dever sus Taula pel ");
define("LANGDST3","<br><br><b>es pas possible</b>, causissètz una autra data o nos contactar dirèctament. <br><br> Mercé");
define("LANGDST4","<br><br><b>es enregistrada</b> per tota informacion suplementària, nos contactar. <br><br> Mercé");
define("LANGDST5","per le ");
define("LANGDST6","Subjècte / Matèria");
define("LANGDST7","Demanda refusada");
define("LANGDST8","Demanda accordée");
//----------------------------------------------------------------------//
define("LANGCALEN1","Eveniment");
define("LANGCALEN2","Planning del ");
define("LANGCALEN3","Apondre una entrada");
define("LANGCALEN4","Suprimir una entrada");
define("LANGCALEN5","Réactualizar la pagina");
define("LANGCALEN6","Calendièr dels eveniments");
define("LANGCALEN7","En classa de ");
define("LANGCALEN8","Dever de ");
define("LANGCALEN9","Dever(s) Sus Taula del jorn");
//----------------------------------------------------------------------//
//modul reservation
define("LANGRESA1","Gestion de l'equipament");
define("LANGRESA2","Gestion de las salas");
define("LANGRESA3","Lista de l'equipament");
define("LANGRESA4","Lista de las salas");
define("LANGRESA5","Apondre un equipament");
define("LANGRESA6","Modificar un equipament");
define("LANGRESA7","Suprimir un equipament");
define("LANGRESA8","Apondre sala");
define("LANGRESA9","Suprimir sala");
define("LANGRESA10","Suprimir una sala");
define("LANGRESA11","Reservacion equipament / sala");
define("LANGRESA12","Reservacion equipament");
define("LANGRESA13","Reservacion sala");
define("LANGRESA14","Reservar");
define("LANGRESA15","Creacion d'un equipament");
define("LANGRESA16","Intitulat de l'equipament");
define("LANGRESA17","Enregistrar la creacion");
define("LANGRESA18","Informacions complementàrias");
define("LANGRESA19","Equipament enregistrat");
define("LANGRESA20","Creacion d'una sala");
define("LANGRESA21","Intitulat de la sala");
define("LANGRESA22","Sala enregistrada");
define("LANGRESA23","Suprimir sala");
define("LANGRESA24","Sala");
define("LANGRESA25","Suprimir la sala");
define("LANGRESA26","Sala suprimida");
define("LANGRESA27","una sala");
define("LANGRESA28","Impossible de suprimir aquesta sala. \\n\\n Sala afectada.  ");
define("LANGRESA29","Equipament suprimit");
define("LANGRESA30","Impossible de suprimir aqueste equipament. \\n\\n Equipament afectat.  ");
define("LANGRESA31","un equipament");
define("LANGRESA32","Suprimir equipament");
define("LANGRESA33","Equipament");
define("LANGRESA34","Suprimir un equipament");
define("LANGRESA35","Lista dels equipaments");
define("LANGRESA36","DATA");
define("LANGRESA37","De");
define("LANGRESA38","A");
define("LANGRESA39","Per qual");
define("LANGRESA40","Informacion");
define("LANGRESA41","Confirmar");
define("LANGRESA42","Confirmat");
define("LANGRESA43","Pas&nbsp;Confirmat");
define("LANGRESA44","Planning Equipament");
define("LANGRESA45","Equipament");
define("LANGRESA46","Equipament ja reservat a aquesta data");
define("LANGRESA47","Consultar le planning de reservacion d'aqueste equipament");
define("LANGRESA48","Reservacion a partir del ");
define("LANGRESA49","En data del ");
define("LANGRESA50","Equipament reservat en espèra de confirmacion");
define("LANGRESA51","Planning Sala");
define("LANGRESA52","Sala");
define("LANGRESA53","Sala ja reservada a aquesta data");
define("LANGRESA54","Sala reservada en espèra de confirmacion");
define("LANGRESA55","Consultar le planning de reservacion per aquesta sala");
define("LANGRESA56","Confirmar Reservacion");
define("LANGRESA57","Planning");
define("LANGRESA58","Confirmar");
//----------------------------------------------------------------------//
define("LANGTTITRE1","Accès Membre");
define("LANGTTITRE2","Membre");
define("LANGTTITRE3","Activacion del compte");
define("LANGTTITRE4","Mercé de plan voler pacientar");
//--------------
define("LANGTP1","Nom");
define("LANGTP2","Petit nom");
define("LANGTP3","Senhal");
define("LANGTCONNEXION","Connexion");
define("LANGTERREURCONNECT","Error de connexion");
define("LANGTCONNECCOURS","Connexion en cors ");
define("LANGTFERMCONNEC","Clicatz aicí per la tampadura de le vòstre compte");
define("LANGTDECONNEC","Déconnexion en cors");

define("LANGTBLAKLIST0",'<b><font color=red  class=T2>Le vòstre compte es desactivat !!</b><br> Per revalidar le vòstre compte, contactar le vòstre establiment escolar.</font>');

define("LANGMOIS1","Genièr");
define("LANGMOIS2","Febrièr");
define("LANGMOIS3","Març");
define("LANGMOIS4","Abril");
define("LANGMOIS5","Mai");
define("LANGMOIS6","Junh");
define("LANGMOIS7","Juljet");
define("LANGMOIS8","Agost");
define("LANGMOIS9","Setembre");
define("LANGMOIS10","Octobre");
define("LANGMOIS11","Novembre");
define("LANGMOIS12","Decembre");

define("LANGDEPART1","de l'".INTITULEELEVE);

define("LANGVALIDE","Validar");
define("LANGIMP45","Editar");

define("LANGMESS34","Messatge plus disponible.");
define("LANGMESS35","Rendre public aqueste grop.");
define("LANGMESS36","Messatge suprimit");


define("LANGRESA59","Nom de la sala");
define("LANGRESA60","Informacion");

define("LANGMAINT0","Una intervencion es prevista sul logicial");
define("LANGMAINT1","Le servici TRIADE serà inaccessible le ");
define("LANGMAINT2","entre");
define("LANGMAINT3","e");

define("LANCALED1","Annada Precedenta");
define("LANCALED2","Annada Seguenta");


define("LANGTTITRE5","Problèma d'accès");
define("LANGTTITRE6","Questions");
define("LANGTPROBL1","Actualament, le servici TRIADE  es en  servici.");
define("LANGTPROBL2","Ai una Question");
define("LANGTPROBL3","Enregistrar la question");
define("LANGTPROBL4","Quitar sens enregistrar");
define("LANGTPROBL5","Explicatz-nos le vòstre problèma");
define("LANGTPROBL6","Establiment escolar*: ");
define("LANGTPROBL7","Email : ");
define("LANGTPROBL8","Messatge : ");
define("LANGTPROBL9","(* camp obligatòri)");
define("LANGTPROBL10","Enregistrar le problèma");
define("LANGTPROBL12","Nos encargam de reglar le vòstre problèma tant viste coma possible. \\n\\n  L'Equipa TRIADE ");

define("LANGELEV1","Nòtas escolaras de");

define("LANGFORUM1","- Lista dels messatges");
define("LANGFORUM2","Aucun messatge es pas estat postat dins aqueste forum de discussion");
define("LANGFORUM3","Podètz ");
define("LANGFORUM3bis"," postar ");
define("LANGFORUM3ter"," un primièr messatge se o volètz ");
define("LANGFORUM4","Postar un novèl messatge");
define("LANGFORUM5","Forum - Postar un messatge");
define("LANGFORUM6","Charta de respectar");
define("LANGFORUM7","Error : le messatge referent existís pas.");
define("LANGFORUM8","Retorn a la lista dels messatges postats");
define("LANGFORUM9","--- Messatge d'origina ---");
define("LANGFORUM10","Le vòstre nom ");
define("LANGFORUM11","Le vòstre email ");
define("LANGFORUM12","Subjècte ");
define("LANGFORUM13","Mandar"); // --> boton mandar
define("LANGFORUM14","Retorn a la lista dels messatges postats");
define("LANGFORUM15","Forum - mandadís d'un messatge");
define("LANGFORUM16","<b>Error</b> : aquesta pagina pòt pas èstre apelada<br> que se un messatge es estat prealablament ");
define("LANGFORUM16bis"," postat ");
define("LANGFORUM17","<b>Error</b> : le vòstre messatge compòrta pas cap de tèxte.<br>");
define("LANGFORUM18","<b>Error</b> : avètz doblidat d'indicar le vòstre nom.<br>");
define("LANGFORUM19","Error ! Le vòstre messatge a pas pogut èstre postat. ");
define("LANGFORUM20","<b>Error</b> : impossible de metre a jorn le fichièr indèx. <br>");
define("LANGFORUM21","Le vòstre messatge a pas pogut èstre postat.");
define("LANGFORUM22","Le vòstre messatge es estat postat corrèctament.<br>Mercé de la vòstra contribucion.");
define("LANGFORUM23","Retorn a la lista dels messatges postats");
define("LANGFORUM24","Forum - lecture d'un messatge");
define("LANGFORUM25","Cap de messatge es pas estat postat dins aqueste forum de discussion.");
define("LANGFORUM26","Podètz ");
define("LANGFORUM26bis","poster");
define("LANGFORUM26ter","un primièr messatge se o volètz.");
define("LANGFORUM27","Aqueste messatge existís pas o es estat suprimit per l'administrator del forum de discussion.<br>");
define("LANGFORUM28","Retorn a la lista dels messatges postats");
define("LANGFORUM30","Autor");
define("LANGFORUM31","Data");
define("LANGFORUM32","Postar una responsa");
define("LANGFORUM33","Messatge precedent (dins le fial de discussion)");
define("LANGFORUM34","Messatges seguents (dins le fial de discussion)");

define("LANGPROFH","Dever Escolar  de  far en ");
define("LANGPROFI","Enregistrar le dever a far ");
define("LANGPROFJ","Dever a far ");
define("LANGPROFK","sasida&nbsp;le&nbsp;");
define("LANGPROFL","Confirmar la data");
define("LANGPROFM","Per le ");
define("LANGPROFN","Dever del ");
define("LANGPROFO","Dever Escolar ");
define("LANGPROFP","Mesa en plaça dels professors principals");
define("LANGPROFQ","Per deman");
define("LANGPROFR","Per ièr");
define("LANGPROFS","Matèria o subjècte");
define("LANGPROFT","Validar la demanda de D.S.T");
define("LANGPROFU","Demanda Mandada -- L'equipa TRIADE");


define("LANGPROJ17","Nombre d'abséncias");
define("LANGPROJ18","jorns");

define("LANGCALEN10","Calendièr dels devers sus table");

define("LANGPARENT6","Lista dels Retards");
define("LANGPARENT7","Lista dels Abséncias");
define("LANGPARENT8","Absent le ");
define("LANGPARENT9","Lista dels dispensas");
define("LANGPARENT10","Periòde&nbsp;du&nbsp;");
define("LANGPARENT11","A"); // indica una data (ora)
define("LANGPARENT12","Le"); // indica una data jorn
define("LANGPARENT13","Certificat");
define("LANGPARENT14","Sanccion disciplinària");
define("LANGPARENT15","Sanccion");
define("LANGPARENT16","En&nbsp;retenguda");
define("LANGPARENT17","à");  // indica una ora
define("LANGPARENT18","Retenguda efectuada");
define("LANGPARENT19","Lista de las circularas administrativas");
define("LANGPARENT20","Accès Fichièr");
define("LANGPARENT21","Visible per ");
define("LANGPARENT22","Calendièr dels eveniments ");
define("LANGPARENT23","Calendièr dels devers sus taula ");
define("LANGPARENT24","Demanda de D.S.T ");


define("LANGAUDIO1","Comunicat Àudio");
define("LANGAUDIO2","Le "); // indica una data
define("LANGAUDIO3","C"); // primièra letra
define("LANGAUDIO3bis","omunicat àudio al format <b>mp3</b><br>Talha maximum del fichièr : ");
define("LANGAUDIO4","Enregistratz le comunicat");
define("LANGAUDIO5","Pacientatz 2 a 3 minutas aprèp le mandadís del fichièr àudio.");
define("LANGAUDIO6","Suprimir le comunicat àudio");


define("LANGOK","D'acòrdi");
define("LANGCLICK","Clicatz aicí");
define("LANGPRECE","Precedent");
define("LANGERROR1","Donadas introbablas");
define("LANGERROR2","pas cap de donada");


define("LANGPROF1","Indicar la matèria");
define("LANGPROF2","Nombre de nòtas");
define("LANGPROF3","Visualizacion de las nòtas");
define("LANGPROF4","grop");
define("LANGPROF5","Causida del Trimèstre");
define("LANGPROF6","Subjècte"); // subjècte del dever
define("LANGPROF7","Intitulat del subjècte "); // subjècte del dever
define("LANGPROF8","Note"); //nòta d'un dever
define("LANGPROF9","Dever Escolar a far a l'ostal");
define("LANGPROF10","Modificacion d'una nòta");
define("LANGPROF11","Supression d'un dever"); // dever --> interrogation
define("LANGPROF12","Professor Principal");
define("LANGPROF13","Ficha ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGPROF14","Apondon de Nòta en ");
define("LANGPROF15","Modificar una nòta en");
define("LANGPROF16","Nom del dever");
define("LANGPROF17","Data&nbsp;du&nbsp;dever"); // &nbsp; --> egal un blanc
define("LANGPROF18","Pacientatz");
define("LANGPROF19","Confirmar la modificacion de las nòtas");
define("LANGPROF20","Validar la modificacion  de las nòtas");
define("LANGPROF21","Modificacion de Nòtas en");
define("LANGPROF22","Visualizacion de las nòtas en");
define("LANGPROF23","Supression d'un dever en");
define("LANGPROF24","Dever de "); // interrogation du
define("LANGPROF25","es suprimit");
define("LANGPROF26","Informacions sus l'".INTITULEELEVE);
define("LANGPROF27","Rensenhaments administratius");
define("LANGPROF28","Informacions sus la vida escolara");
define("LANGPROF29","Informacions medicalas");
define("LANGPROF30","Informacion del");
define("LANGPROF31","De"); // indiquant una persona


define("LANGEL1","Nom");
define("LANGEL2","Petit nom");
define("LANGEL3","Classa ");
define("LANGEL4","Lv1");
define("LANGEL5","Lv2");
define("LANGEL6","Opcion");
define("LANGEL7","Regim");
define("LANGEL8","Data de naissença");
define("LANGEL9","Nacionalitat");
define("LANGEL10","Senhal");
define("LANGEL11","Nom de Familha");
define("LANGEL12","Petit nom");
define("LANGEL13","carrièra");
define("LANGEL14","Adreça 1");
define("LANGEL15","Còdi postal");
define("LANGEL16","Comuna");
define("LANGEL17","carrièra");
define("LANGEL18","Adreça 2");
define("LANGEL19","Còdi Postal");
define("LANGEL20","Comuna");
define("LANGEL21","Telefòn");
define("LANGEL22","Profession del paire");
define("LANGEL23","Telefòn del paire");
define("LANGEL24","Profession de la maire");
define("LANGEL25","Telefòn de la maire");
define("LANGEL26","Establiment");
define("LANGEL27","Còdi establiment");
define("LANGEL28","Còdi postal");
define("LANGEL29","Comuna");
define("LANGEL30","Numèro Estudiant");
// define("LANGEL30","Numèro National");


define("LANGPROF32","Informacions scolaires");
define("LANGPROF33","Dever a l'ostal");
define("LANGPROF34","Consultacion en setmana");
define("LANGPROF35","Setmana darrièra");
define("LANGPROF36","Setmana que ven");
define("LANGTP23"," INFORMACION - Demanda de reservacion !");
define("LANGRESA61","Nom de l'equipament");


define("LANGIMP46","Petit nom");
define("LANGIMP47","Intitulat (Sr. o Dna o Dmsla) ");
define("LANGIMP48","Nom");
define("LANGIMP49","* camp obligatòri");
define("LANGIMP50","Le fichièr de transmetre <FONT color=RED><B>DOIT</B></FONT> contenir <FONT COLOR=red><B>9</B></FONT> camps <I>(pas voids)</I> separats per un meteis separador le \"<FONT color=red><B>;</B></font>\" <I>Soit la preséncia de 8 còps le caractèr \"<FONT color=red><B>;</B></font>\"</I>");
define("LANGIMP51","senhal parent");
define("LANGIMP52","senhal ".INTITULEELEVE);



define("LANGacce_dep1","Error de connexion");
define("LANGacce_dep2","Verificar le les vòstres identifiants de connexion, se le problèma persiste, <br />  avertissez le vòstre administrator TRIADE via le ligam <br /> 'Problèma d'accès' dins le menú d'esquèrra");

define("LANGacce_ref1","Error Tipe : Accès pas autorizat");
define("LANGacce_ref11","Visitat le ");
define("LANGacce_ref12","per ");
define("LANGacce_ref13","amb  ");
define("LANGacce_ref2","ACCÈS PAS AUTORIZAT");
define("LANGacce_ref3","Per accéder a le vòstre compte, vos cal vos connectar.");
define("LANGacce1","L'".INTITULEELEVE." ");
define("LANGacce12","a una punicion a rendre, <br> seguida a la categoria : ");
define("LANGacce13","per le motiu ");
define("LANGacce14","Le dever a far es le seguent : ");
define("LANGacce2","Suprimir aqueste messatge : ");
define("LANGacce21","Suprimir");
define("LANGacce3","L'".INTITULEELEVE." ");
define("LANacce31","s'es pas presentat</b></font> a la vida escolara (CPE), <b>per la retenguda</b>,  seguida a la categoria :");
define("LANacce32","pel motiu : ");
define("LANGacce4","Le dever a far es le seguent :");
define("LANGacce5","Suprimir");
define("LANGacce6","Gestion disciplinària");
define("LANGaccrob11","Telecargament del Logicial Adobe Acrobat Reader 8.1.0 fr");
define("LANGaccrob2","23,4 Mo  per Windows 2000/XP/2003/Vista");
define("LANGaccrob3","Temps del telecargament :");
define("LANGaccrob4","en 56 K : 57 min e 3 s");
define("LANGaccrob5","en 512 K : 6 min e 14 s");
define("LANGaccrob6","en 5 M : 37 segondas");
define("LANGaccrob7","Telecargament del Logicial Adobe Acrobat Reader 6.O.1 fr");
define("LANGaccrob8","Talha : ");
define("LANGaccrob9","0.40916 Mo per NT/95/98/2000/ME/XP");
define("LANGaccrob10","en 56 K : 0 min e 58.2 s");
define("LANGaccrob11bis","en 512 K : 0 min e 6.6 s ");
define("LANGaffec_cre21","Creacion d'afectacion per la classa ");
define("LANGaffec_cre22","Mesa en plaça d'afectacion en cors ");
define("LANGaffec_cre23","L'aviada del logicial d'afectacion va se faire automaticament<br>Se la novèla pagina apareis pas, clicatz ");
define("LANGaffec_cre24","TRIADE - Compte de ");
define("LANGaffec_cre31","CREACION - AFECTACION");
define("LANGaffec_cre41","Imprimir");
define("LANGaffec_mod_key1","Afectacion de las classas");
define("LANGaffec_mod_key2","Modul de modificacion d'afectacion de las classas.");
define("LANGaffec_mod_key3","ATENCION aqueste modul es a utilizar al moment de modificacion d'afectacion,<br> destrutz totas las nòtas dels ".INTITULEELEVE."s  de las classas modificadas. ");
define("LANGaffec_mod_key4","ATENCION, la destruccion de las nòtas de las classas seleccionadas seràn suprimidas. \\n Volètz contunhar ? \\n\\n L\'equipa TRIADE");
define("LANGespèra1","Attente - TRIADE");
define("LANGespèra2","Pacientatz, S.V.P.");
define("LANGespèra3","L'Equipa TRIADE.");
define("LANGatte_mess1","TRIADE - Attente - Messatjariá");
define("LANGatte_mess2","Pacientatz, S.V.P.");
define("LANGatte_mess3","servici TRIADE");
define("LANGbasededon20","Mandar le fichièr");
define("LANGbasededon201","pas res");
define("LANGbasededon2011","Importacion de fichièr GEP");
define("LANGbasededon202","Fichièr Transmés -- L'equipa TRIADE");
define("LANGbasededon203","Fichièr pas enregistrat");
define("LANGbasededon31","Indicar per cada referéncia la classa correspondenta");
define("LANGbasededon32","Causida ...");
define("LANGbasededon33","pas cap");
define("LANGbasededon34","Le mandadís del fichièr pòt durer de <b>2 a 4 minutas</b> en foncion del nombre d'".INTITULEELEVE."s.");
define("LANGbasededon35","Le fichièr deu èstre al format <b>dbf</b> e deu èstre <b>F_ele.dbf</b>");
define("LANGbasededon41","Error sul nombre de classas !!! - Contactar l'equipa TRIADE <br /><br /> support@triade-educ.org</center>");
define("LANGbasededon42","Error sus la sasida de las classas, una classa es repetida mantun còp -- L'equipa TRIADE");
define("LANGbasededon43","Messatge del : ");
define("LANGbasededon44","De");
define("LANGbasededon45","Membre :");
define("LANGbasededon46","Messatge :");
define("LANGbasededon47","NOUVELLE BASE:");
define("LANGbasededon48","- amb GEP");
define("LANGbasededon49"," Establiment :");
define("LANGbasededoni11","'Atencion','./imatge/commun/warning.jpg','<font face=Verdana size=1><font color=red>L</font>e modul <b>dbase</b> es pas <br> chargé !! <i>Necessari per importar <br> una basa GEP.");
define("LANGbasededoni21","ATENCION, la destruccion de l\'ancienne basa serà automatique. \\n Volètz contunhar ? \\n\\n L\'Equipa TRIADE");
define("LANGbasededoni31","Indicar per quina categoria le fichièr es artribuit ");
define("LANGbasededoni32","L'impòrt del fichièr concerne : ");
define("LANGbasededoni33","Impòrt dels ".INTITULEELEVE."s : ");
define("LANGbasededoni34","Impòrt dels ensenhants :");
define("LANGbasededoni35","Impòrt del personal vida escolara : ");
define("LANGbasededoni36","Impòrt del personal administratiu : ");
define("LANGbasededoni41","Classa anteriora");
define("LANGbasededoni42","Annada anteriora");
define("LANGbasededoni51","Per l'intitulat");



define("LANGbasededoni61","error");
define("LANGbasededoni71","Importacion del fichièr ASCII");
define("LANGbasededoni72","Messatge del : ");
define("LANGbasededoni721","De");
define("LANGbasededoni722","Membre :");
define("LANGbasededoni723","Messatge :");
define("LANGbasededoni724","NOVÈLA BASA:");
define("LANGbasededoni725","- amb ASCII");
define("LANGbasededoni726"," Establiment :");
define("LANGbasededoni73","Total d'enregistraments dins la basa ");
define("LANGbasededoni91","Importacion del fichièr ASCII");
define("LANGbasededoni92","Error sul nombre de classas !!! - Contactar le l'equipa TRIADE <br />");
define("LANGbasededoni93","Error sus la sasida de las classas, una classa es repetida mantun còp -- L'equipa TRIADE");
define("LANGbasededoni94","Donada de la basa tractada -- L'equipa TRIADE<br />");
define("LANGbasededoni95","Total d'".INTITULEELEVE."  enregistrat dins la basa : ");
define("LANGPIEDPAGE","<p> La <b>T</b>ransparence e la <b>R</b>apidité de l'<b>I</b>nformatique <b>A</b>u servici <b>D</b>e l'<b>E</b>nsenhament<br>Per visualizar aqueste site de faiçon optimala :  resolucion minimala : 800x600 <br>  © 2000 - ".date("Y")." TRIADE - Tous droits reservats");

define("LANGAPROPOS1","Version");
define("LANGAPROPOS2","Tous droits reservats");
define("LANGAPROPOS3","Licence d'utilizacion");
define("LANGAPROPOS4","Product ID");

define("LANGTELECHARGER","Telecargar");
define("LANGAJOUT1","Per le Régime : causida possibla (<b>INT</b> (Interne),<b>EXT</b> (Extèrne), <b>DP</b> (Demi Pension)<br><br>");
define("LANGIMP44","Le fichièr es pas confòrme.");
define("LANGBASE16"," Les colomnas son représentées sous la forme : <b>nom de login ; petit nom de login ; senhal Parent ; senhal ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." en clar ; classa de l'".INTITULEELEVE." </b>");


define("LANGSUPP0","Supression d'un compte Suplent");
define("LANGSUPP1","Modul Supression");
define("LANGSUPP2","Suprimir le compte");
define("LANGSUPP3","Volètz suprimir de la lista dels suplents");
define("LANGSUPP3bis","remplaçant de");
define("LANGSUPP4","Confirmar la supression");
define("LANGSUPP5","Impossible de suprimir aqueste compte. \\n\\n Compte afectat a una classa.  \\n\\n  L'equipa TRIADE");
define("LANGSUPP6","Compte suprimit - L'equipa TRIADE");
define("LANGSUPP7","Supression d'un grop");
define("LANGSUPP8","Suprimir le grop");
define("LANGSUPP9","Supression d'un compte ");
define("LANGSUPP10","Suprimir le compte");
define("LANGSUPP11","un membre de la vida escolara");
define("LANGSUPP12","un administrator");
define("LANGSUPP13","un ensenhant");
define("LANGSUPP14","Supression d'un ".INTITULEELEVE." dins la  classa");
define("LANGSUPP15","Clicar sus l'".INTITULEELEVE." a suprimir");
define("LANGSUPP16","Supression d'un ".INTITULEELEVE."");
define("LANGSUPP17","va èstre suprimit de la basa");
define("LANGSUPP18","Totas las informacions sus aqueste ".INTITULEELEVE." van èstre suprimidas, velent a dire : <br> (nòtas, abséncias, retards, dispensas, sanccions, informacions, messatjariás, ...)");
define("LANGSUPP19","Anullar la supression");
define("LANGSUPP20","es suprimit de la basa");
define("LANGSUPP21","Suprimir una classa");
define("LANGSUPP22","Supression d'una classa");
define("LANGSUPP23","Supression d'una matèria o sosmatèria");
define("LANGSUPP24","Suprimir la matèria");
define("LANGSUPP25","Classa suprimida --  Servici TRIADE");
define("LANGSUPP26","Matèria suprimida --  Servici TRIADE");
define("LANGSUPP27","Creacion de la matèria");
define("LANGSUPP28","Sosmatèria enregistrada");

define("LANGADMIN","Administracion");
define("LANGPROF","Ensenhant");
define("LANGSCOLAIRE","de la Vida Escolara");
define("LANGCLASSE","une classa");

define("LANGGRP11","Nom del Groupe");
define("LANGGRP12","Classa(s) concernida(s)");
define("LANGGRP13","Lista ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."s");
define("LANGGRP14","Lista dels gropes");
define("LANGGRP15","Creacion d'un grop");
define("LANGGRP16","Indicatz les ".INTITULEELEVE."s dins le grop");
define("LANGGRP17","Seleccionar");
define("LANGGRP18","Enregistrar le grop");
define("LANGGRP19","Creacion del grop efectuada");
define("LANGGRP20","Autre grop");
define("LANGGRP21","Lista dels gropes");
define("LANGGRP22","Indicar una classa per la creacion del grop S.V.P. \\n\\n L'equipa TRIADE");
define("LANGGRP23","Lista dels ".INTITULEELEVE."s del grop");
define("LANGGRP24","Lista de las classas");
define("LANGGRP25","Lista dels matèrias");



//----------------//
define("LANGDONNEENR","<font class=T2>Donada(s) Enregistrada(s).</font>");

define("LANGABS47","Apondon d'una sanccion disciplinària");
define("LANGABS48"," a atent ");
define("LANGABS48bis","còps la categoria");
define("LANGABS49","durada");
define("LANGABS50"," Retenguda  del ");
define("LANGABS51","Tel. Prof Paire ");
define("LANGABS52","Tel. Prof Maire ");
define("LANGABS53","Cap de retard o d'abséncia pas senhalat");

define("LANGCALRET1","Calendièr &nbsp; de las &nbsp; Retengudas");

define("LANGHISTO1","Istoric de las operacions");

define("LANGDST9","Apondre una entrada");
define("LANGDST10","Suprimir una entrada");
define("LANGDST11","en classa de");

define("LANGDISP11","Afichatge <b>complet</B> de las dispensas");

define("LANGEN","En");

define("LANGAFF4","Edicion d'una classa");
define("LANGAFF5","Totas las classas");
define("LANGAFF6","Consultar aquesta classa");

define("LANGCHER1","Recèrca Complexe");
define("LANGCHER2","Indicar le format de fichièr a generar");
define("LANGCHER3","Indicar le separador de camps");
define("LANGCHER4","Efectuar la recèrca d'un ".INTITULEELEVE." a partir del nom : <b>clicatz aicí</b>");
define("LANGCHER5","Apondre");
define("LANGCHER6","Levar");
define("LANGCHER7","Montar");
define("LANGCHER8","Davalar");
define("LANGCHER9","Seguent");
define("LANGCHER10","Element recercat");
define("LANGCHER11","Nombre de critèris de recèrca");
define("LANGCHER12","A partir de");

define("LANGCHER13","amb la valor");
define("LANGCHER14","Recèrca aproximativa");
define("LANGCHER15","Recèrca precisa");
define("LANGCHER16","Aviar la recèrca");
define("LANGCHER17","Atencion : demòra un element pas causit !! -- L'equipa TRIADE ");

define("LANGCHER18","amb coma valor");

define("LANGTITRE34","Configuracion del corrièr retard");
define("LANGTITRE35","Configuracion del corrièr abséncia");

define("LANGCONFIG1","Configuracion enregistrada.");
define("LANGCONFIG2","Aquí le vòstre tèxte ");

define("LANGCONFIG3","Indicar la lista dels parents d'".INTITULEELEVE."s que recebràn un corrièr");

define("LANGERROR01","Error d'accès a la basa");
define("LANGERROR02","ATENCION Impossible <br><br>Le problèma pòt venir de las informacions sasidas <br>(Verificatz les diferents camps abans de validar).<BR>  <BR>O l'informacion es ja enregistrada O pas accessibla.");
define("LANGERROR03","Accès impossible a la basa per aquesta accion . <BR>");

define("LANGABS54","es ja notat absent.");
define("LANGABS55","es ja notat en retard.");


define("LANGPARAM4","Le certificat es plan enregistrat.");
define("LANGPARAM5","Le certificat d'escolaritat dels ".INTITULEELEVE."s de la classa ");
define("LANGPARAM5bis","es disponible, al format PDF");
define("LANGPARAM6","Parametratge pel contengut dels bulletins e periòdes");

define("LANGPARAM7","Nom  del director");
define("LANGPARAM8","Nom  de l'establiment");
define("LANGPARAM9","adreça");
define("LANGPARAM10","Còdi Postal");
define("LANGPARAM11","Vila");
define("LANGPARAM12","Telefòn");
define("LANGPARAM13","E-mail");
define("LANGPARAM14","Lògo establiment");
define("LANGPARAM15","Enregistrar les paramètres");
define("LANGPARAM16","Enregistrament efectuat. -- L'Equipa TRIADE");

define("LANGCERTIF1","Le certificat d'escolaritat de ");
define("LANGCERTIF1bis","es disponible, al format PDF");


define("LANGRECHE1","Informacions sus l'".INTITULEELEVE."");

define("LANGBT52","Modificar las donadas");

define("LANGEDIT1","Donadas introbablas");

define("LANGMODIF1","Mesa a jorn d'un compte ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGMODIF2","Rensenhaments sus l'".INTITULEELEVE);
define("LANGMODIF3","Rensenhaments sus la familha");

define("LANGALERT1","Donadas mesas a jorn -- Equipa TRIADE");
define("LANGALERT2","Atencion format del fichièr pas confòrme o talha pas respectada");
define("LANGALERT3","Atencion format del fichièr pas confòrme o talha pas respectada");

define("LANGLOGO1","Lògo de transmetre");
define("LANGLOGO2","Enregistrar le logo");
define("LANGLOGO3","Le logo <b>deu èstre al format jpg</b> e de talha 96px sus 96px.");

define("LANGPARAM17","Définition des periòdes trimestralas o semestralas");
define("LANGPARAM18","Trimèstre o Semèstre");
define("LANGPARAM19","Data de començament");
define("LANGPARAM20","Data de fin");
define("LANGPARAM21","Primièr");
define("LANGPARAM22","Segond");
define("LANGPARAM23","Tresen");
define("LANGPARAM24","Enregistrar las datas trimestralas");
define("LANGPARAM25","Donada presa en compte, se l'enregistrament es al format Trimestral");
define("LANGPARAM26","Data pas valida -- Equipa TRIADE");
define("LANGPARAM27","Informacions Enregistradas -- Equipa TRIADE");
define("LANGPARAM28","trimèstre");
define("LANGPARAM29","semèstre");
define("LANGPARAM30","Bulletin");


define("LANGBULL5","Impression de bulletin");
define("LANGBULL6","Contunhar le tractament");
define("LANGBULL7","Impression de periòde");
define("LANGBULL8","Indicatz le començament del periòde");
define("LANGBULL9","Indicatz la fin del periòde");
define("LANGBULL10","Indicatz le periòde");
define("LANGBULL11","Indicatz la seccion");
define("LANGBULL12","Imprimir le periòde");
define("LANGBULL13","Istoric");
define("LANGBULL14","<FONT COLOR='red'>ATENCION</FONT></B> Besonh de l'aisina <B>Adobe Acrobat Reader</B>.  Logicial e telecargament Gratuits ");
define("LANGBULL14bis","Telecargament");
define("LANGBULL15","Visualizar / Suprimir");
define("LANGBULL16","Nom de l'".INTITULEELEVE);
define("LANGBULL17","Professor");
define("LANGBULL18","Detalh de las nòtas");
define("LANGBULL19","Apreciacion del Professor Principal");
define("LANGBULL20","RELEVAT DE NÒTAS");
define("LANGBULL21","periòde");

define("LANGBULL22","primièr trimèstre");
define("LANGBULL23","segond trimèstre");
define("LANGBULL24","tresen trimèstre");

define("LANGBULL25","primièr semèstre");
define("LANGBULL26","segond semèstre");

define("LANGBULL27","Bulletin del ");
define("LANGBULL28","Seccion");
define("LANGBULL29","Annada Escolara");

define("LANGBULL30","BULLETIN");

define("LANGBULL31","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGBULL32","Matèrias");
define("LANGBULL33","Classa");
define("LANGBULL34","Apreciacions, progrèsses, conselhs per progressar");

define("LANGBULL35","Coef");
define("LANGBULL36","Mej");
define("LANGBULL37","Mini");
define("LANGBULL38","Maxi");
define("LANGBULL39","Assiduitat e comportament al dintre de l'establiment : ");
define("LANGBULL40","Apreciacion globala de l'equipa pedagogica : ");
define("LANGBULL41","Bulletin de conservar preciosament");
define("LANGBULL42","Visa del cap d'establiment o de son delegat");
define("LANGBULL43","ANNADA ESCOLARZ");
define("LANGBULL44","Sr. & Dna");
define("LANGOU","ou"); // le o de o bien


define("LANGPROJ19","Semèstre 1");
define("LANGPROJ20","Semèstre 2");

define("LANGDISC1","Retenguda  del ");
define("LANGDISC2","Imprimir las retengudas del jorn");


define("LANGDISC3","Tel. Dom. ");
define("LANGDISC4","Tel. Prof. Paire ");
define("LANGDISC5","Tel. Prof. Maire ");
define("LANGDISC6","Mesa en plaça d'una sanccion en  Classa de ");
define("LANGDISC7","Intitulat de la categoria ");
define("LANGDISC8","Intitulat de la sanccion ");
define("LANGDISC9","Artribuit per ");
define("LANGDISC10","Motiu, informacions, dever a far ");
define("LANGDISC11","Retenguda");
define("LANGDISC11bis","Le");  // Le per indicar una data
define("LANGDISC11Ter","A");  // A per indicar una ora
define("LANGDISC12","durada");
define("LANGDISC13","<font color=red>M</font></B>arcatz la casa se l\'".INTITULEELEVE." es siá en retenguda siá sanccionat.");
define("LANGDISC14","Apondon d'una sanccion disciplinària");
define("LANGDISC15","<B>*<I> D</B>: Telefòn Domicili, <B>P</B>: Telefòn Profession Paire, <B>M</B>: Telefòn Profession Maire</I>");
define("LANGDISC16","Efectuar");
define("LANGDISC17","Tel.");
define("LANGDISC18","Afichatge  de las Sanccions");
define("LANGDISC19","Afichatge de las <b>5</B> darrièras sanccions");
define("LANGDISC20","Categoria");
define("LANGDISC21","Lista completa de ");
define("LANGDISC22","Visualizar las retengudas de ");
define("LANGDISC23","Afichatge de las retengudas");
define("LANGDISC24","Afichatge  <b>complet</B> de las retengudas");
define("LANGDISC25","En&nbsp;retenguda");
define("LANGDISC26","Retenguda pas efectuada");
define("LANGDISC27","Listar las sanccions de ");
define("LANGDISC28","Afichatge   de las Sanccions");
define("LANGDISC29","Afichatge  <b>complet</B> de las sanccions");
define("LANGDISC30","Sasida&nbsp;lo");
define("LANGDISC31","Listar las sanccions de ");
define("LANGDISC32","Retenguda pas afectada a un escolan ");
define("LANGDISC33","ATENCION l'".INTITULEELEVE." ");
define("LANGDISC33bis"," es ja en retenguda per la data e l'ora indicada. ");
define("LANGDISC34","a atent");
define("LANGDISC34bis","còps la categoria");
define("LANGDISC35","Supression Sanccion");
define("LANGDISC36","Supression Retenguda");

define("LANGespèra222","Pacientatz");



define("LANGSUPP","Sup"); // abréviation de Suprimir



define("LANGCIRCU1","Gestion de las Circularas administrativas");
define("LANGCIRCU2","Apondre una circulara");
define("LANGCIRCU3","Listar de circularas");
define("LANGCIRCU4","Suprimir una circulara");
define("LANGCIRCU5","Apondon de circularas administrativas");
define("LANGCIRCU6","Subjècte");
define("LANGCIRCU7","Referéncia");
define("LANGCIRCU8","Circulara");
define("LANGCIRCU9","Còs Ensenhant");
define("LANGCIRCU10","Dins la o las classa(s)");
define("LANGCIRCU11","<font face=Verdana size=1><B><font color=red>C</font></B>irculara al format : <b>doc</b>, <b>pdf</b>, <b>txt</b>, <b>Office</b>.</FONT>");
define("LANGCIRCU12","<font face=Verdana size=1><B><font color=red>C</font></B>irculara visible pels ensenhants.</FONT>");
define("LANGCIRCU13","Totas las classas");
define("LANGCIRCU14","Retorn al Menú");
define("LANGCIRCU15","Enregistrar la circulara");
define("LANGCIRCU16","Circulara pas enregistrada");
define("LANGCIRCU17","Le fichièr deu èstre al format <b>txt o doc o pdf</b> e inferior a 2Mo ");
define("LANGCIRCU18","<font class=T2>Circulara enregistrada</font>");
define("LANGCIRCU19","Suprimir de las Circularas administrativas");
define("LANGCIRCU20","Accès Fichièr");
define("LANGCIRCU21","<font color=red>R</b></font><font color=#000000>eferéncia");

define("LANGCODEBAR1","Gestion dels còdis barras");
define("LANGCODEBAR2","Aqueste modul fonciona pas amb le vòstre servidor. <br> Vos cal aver PHP 5 o sup per utilizar aqueste modul.");
define("LANGCODEBAR3","Aquí la lista dels còdis barras accessible per TRIADE");
define("LANGCODEBAR4","Le còdi barra utilizat per defaut es le ");
define("LANGCODEBAR5","Lista");


define("LANGPUB1","Apondon d'una bandièra de publicité");
define("LANGPUB2","Vous désirez publier sul site de TRIADE");
define("LANGPUB3","Efectuar una campagne publicitaire");
define("LANGPUB4","Per aquò  ");
define("LANGPUB5","Sètz ja annonceur sus TRIADE ");

define("LANGPROFB1","Apreciacion pels bulletins trimestrals");
define("LANGPROFB2","Parametratge de le les vòstres comentaris automatizats");
define("LANGPROFB3","Parametratge");
define("LANGPROFB4","Configuracion Comentaris Bulletins");
define("LANGPROFB5","Enregistrament dels comentaris");
define("LANGPROFB6","Comentari");
define("LANGPROFB7","Lista");


define("LANGPROFC1","Calendièr del planning d'equipament");
define("LANGPROFC2","Calendièr del planning de las salas");


define("LANGPARAM31","Visualizacion en mòde U.S.A.");
define("LANGPARAM32","Assiduitat e comportament al dintre de l'establiment : ");
define("LANGPARAM33","Recuperar le fichièr PDF");

define("LANGDISC37","Apondon d'una sanccion disciplinària");

define("LANGPROFP4","<b>Professor Principal</b> en ");
define("LANGPROFP5","Informacions sus l'".INTITULEELEVE);
define("LANGPROFP6","Informacions del ");
define("LANGPROFP7","fins al ");

define("LANGPROFP8","Nombre total de retards");
define("LANGPROFP9","Nombre de retards aqueste trimèstre");
define("LANGPROFP10","Nombre total d'abséncias");
define("LANGPROFP11","Nombre d'abséncias aqueste trimèstre");

define("LANGPROFP12","Gestion dels delegats");
define("LANGPROFP13"," en classa de ");
define("LANGPROFP14","Parent delegat");
define("LANGPROFP15","Coordonadas");
define("LANGPROFP16","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." delegat");
define("LANGPROFP17","Parent(s) delegat(s)");
define("LANGPROFP18","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."(s) delegat(s)");
define("LANGPROFP19","Tel."); // per telefòn
define("LANGPROFP20","Mail");
define("LANGPROFP21","Complement d'informacions medicalas sus l'".INTITULEELEVE);

define("LANGETUDE1","Gestion dels estudis");
define("LANGETUDE2","Afectacion dels ".INTITULEELEVE."s a l'estudi");
define("LANGETUDE3","Consultar la lista dels estudis afectadas");
define("LANGETUDE4","Apondre un estudi");
define("LANGETUDE5","Modificar un estudi");
define("LANGETUDE6","Suprimir un estudi");
define("LANGETUDE7","Consultacion d'una estudi");
define("LANGETUDE8","Afectar un ".INTITULEELEVE." a un estudi");
define("LANGETUDE9","Modificar un ".INTITULEELEVE." a un estudi");
define("LANGETUDE10","Suprimir un ".INTITULEELEVE." d'un estudi");
define("LANGETUDE11","Lista dels estudis");

define("LANGETUDE12","Susvelhant");
define("LANGETUDE13","Estudi");
define("LANGETUDE14","En sala");
define("LANGETUDE15","Setmana");
define("LANGETUDE16","Lo");  		// Le indica una data
define("LANGETUDE17","a");  		// a indica una ora
define("LANGETUDE18","pendznt");  	//indica una durada
define("LANGETUDE19","Creacion d'un estudi");
define("LANGETUDE20","Nom de l'estudi");
define("LANGETUDE21","Jorn de la setmana");
define("LANGETUDE22","L'ora d'estudi");
define("LANGETUDE23","Durada de l'estudi");
define("LANGETUDE24","hh:mm");
define("LANGETUDE25","Sala d'estudi");
define("LANGETUDE26","Susvelhant d'aquesta estudi");
define("LANGETUDE27","L'estudi es enregistrada");
define("LANGETUDE28","Lista dels estudis");
define("LANGETUDE29","Modificacion d'un estudi");
define("LANGETUDE30","L'estudi possedís dels ".INTITULEELEVE."s. Suprimir la lista dels ".INTITULEELEVE."s de l'estudi abans de suprimir l'estudi");
define("LANGETUDE31","Lista ".INTITULEELEVE);
define("LANGETUDE32","Lista dels ".INTITULEELEVE."s");
define("LANGETUDE33","Afectacion d'un ".INTITULEELEVE." a un estudi");
define("LANGETUDE34","Causida de l'estudi");
define("LANGETUDE35","Indicar las classas per l'afectacion dels ".INTITULEELEVE."s a aquesta estudi");
define("LANGETUDE36","Intitulat de l'estudi");
define("LANGETUDE37","Indicatz les ".INTITULEELEVE."s dins aquesta estudi");
define("LANGETUDE38","autorizat a sortir");
define("LANGETUDE39","Enregistrar l'estudi");
define("LANGETUDE40","Autre estudi");
define("LANGETUDE41","Modificar l'estudi d'un ".INTITULEELEVE);
define("LANGETUDE42","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." en estudi");
define("LANGETUDE43","Enregistrar las modificacions");
define("LANGETUDE44","Sortida autorizada");
define("LANGETUDE45","Suprimir l'estudi d'un ".INTITULEELEVE);

define("LANGLIST1","Edicion d'una classa");
define("LANGLIST2","Lista dels ensenhants de la classa");
define("LANGLIST3","Professor Principal");
define("LANGLIST4","Data");
define("LANGLIST5","Lista completa al format PDF");
define("LANGLIST6","Professor Principal");


define("LANGPASS1","Novèl senhal");

define("LANGTRONBI1","Visualizacion Trombinoscòpi");
define("LANGTRONBI2","Modificar Trombinoscòpi");
define("LANGTRONBI3","Atencion format del fichièr pas confòrme");
define("LANGTRONBI4","Impossible, fòto de talha pas confòrme");
define("LANGTRONBI5","Nom ".INTITULEELEVE);
define("LANGTRONBI6","Petit nom ".INTITULEELEVE);
define("LANGTRONBI7","la fòto");
define("LANGTRONBI8","apondre fòto");


define("LANGBASE19","Le fichièr seleccionat es pas valid");
define("LANGBASE20","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." sens classa");
define("LANGBASE21","Nombre d'".INTITULEELEVE."s sens classa");
define("LANGBASE22","Afichatge dels 30 primièrs");
define("LANGBASE23","Cambiament de classa pels ".INTITULEELEVE."s");
define("LANGBASE24","Cambiament Acabat");
define("LANGBASE25","ABANS TOTAS ModificacionS CONSULTAR NÒSTRA AJUDA");
define("LANGBASE26","Cambiament de classa pels ".INTITULEELEVE."s de la classa");
define("LANGBASE27","Informacion sul cambiament de classa d'un ".INTITULEELEVE);
define("LANGBASE28","<b>Pas de cambiament.</b> <i>(Amb l'opcion 'causida ...')</i>");
define("LANGBASE29","Cap de supression d'informacion de l'".INTITULEELEVE." es pas realizada.");
define("LANGBASE30","<b>Le cambiament de classa.</b> <i>(Amb indicacion d'una classa)</i>");
define("LANGBASE31","Supression nòtas, abs, retards, disciplinas, dispensas  de l'".INTITULEELEVE.".");
define("LANGBASE32","<b>Quita l'escòla.</b>  <i>(Amb l'opcion 'Quita l'escòla')</i>");
define("LANGBASE33","Supression de l'".INTITULEELEVE." dins la basa.");
define("LANGBASE34","Supression nòtas, abs, retards, disciplinas, dispensas de l'".INTITULEELEVE.".");
define("LANGBASE35","Supression messatges intèrnes de la familha.");
define("LANGBASE36","Va en classa de");
define("LANGBASE37","Quita l'escòla");
define("LANGBASE38","Validar lo(s) cambiament(s)");
define("LANGBASE39","Causissètz un element");


define("LANGBASE40","Causida del ");



// MODULE AGENDA 
define("LANGAGENDA1","Atencion!!!\nLa nòta que venètz de crear o de modificar se superpose\namb una autra nòta pels utilizaires seguents");
define("LANGAGENDA2","Volètz suprimir aquesta nòta que vos es estat afectada ?");
define("LANGAGENDA3","Supression d'una nòta, rapèl :\\n\\n - Totas las ocuréncias que resultan d'aquesta nòta seràn tanbens escafadas\\n - Per suprimir juste una ocuréncia, clicatz sus l'imatge correspondent a dreita de la nòta dins les planning\\n\\nVolètz suprimir aquesta nòta ?");
define("LANGAGENDA4","Supression d'una ocuréncia, rapèl :\\n\\n - Sola aquesta ocuréncia serà suprimida\\n - Per suprimir una nòta recurrenta e totas sas ocuréncias, clicatz sus la crotz a dreita de la nòta dins les plannings o editatz la nòta e clicatz sul boton [Suprimir]\\n\\nVolètz suprimir aquesta ocuréncia ?");
define("LANGAGENDA5","Nòta amb rapèl");
define("LANGAGENDA6","Suprimir una ocuréncia");
define("LANGAGENDA7","Suprimir una nòta");
define("LANGAGENDA8","S'apropriar una nòta");
define("LANGAGENDA9","Afichar le detalh");
define("LANGAGENDA10","Nòta personala");
define("LANGAGENDA11","Nòta afectada");
define("LANGAGENDA12","Nòta Activa");
define("LANGAGENDA13","Nòta Acabada");
define("LANGAGENDA14","Jorn corrent");
define("LANGAGENDA15","Jorn feriat");
define("LANGAGENDA16","Crear una nòta");
define("LANGAGENDA17","clicar per cambiar");
define("LANGAGENDA18","Enregistrar una data d'anniversari");
define("LANGAGENDA19","Modificacion d'una data d'anniversari");
define("LANGAGENDA20","Sasissètz le nom de la persona");
define("LANGAGENDA21","Sasissètz la data de naissença de la persona");
define("LANGAGENDA22","Anniversari de");
define("LANGAGENDA23","Data de naissença");
define("LANGAGENDA24","Format jj/mm/aaaa");
define("LANGAGENDA25","Suprimir aqueste anniversari ?");
define("LANGAGENDA26","Suprimir");
define("LANGAGENDA27","Anullar");
define("LANGAGENDA28","Enregistrar");
define("LANGAGENDA29","Sètz segur que volètz escafar aqueste anniversari ?");
define("LANGAGENDA30","Modificar");
define("LANGAGENDA31","Annada prec.");
define("LANGAGENDA32","Mois prec.");
define("LANGAGENDA33","Atteindre la data del jorn");
define("LANGAGENDA34","maintenir per menú");
define("LANGAGENDA35","Mes seg.");
define("LANGAGENDA36","Annada seg.");
define("LANGAGENDA37","Seleccionar una data");
define("LANGAGENDA38","Desplaçar");
define("LANGAGENDA39","Uèi");
define("LANGAGENDA40","A prepaus del calendièr");
define("LANGAGENDA41","Afichar %s en primièr");
define("LANGAGENDA42","Tampar");
define("LANGAGENDA43","Clicar o lisar per modificar la valor");
define("LANGAGENDA44","Utilizaire desconegut");
define("LANGAGENDA45","La vòstra session a expirat !");
define("LANGAGENDA46","Aqueste login es ja utilizat");
define("LANGAGENDA47","Ancian senhal erronèu");
define("LANGAGENDA48","Identificatz-vos per utilizar Phenix");
define("LANGAGENDA49","La connexion al servidor SQL a fracassat");
define("LANGAGENDA50","Perfil modificat");
define("LANGAGENDA51","Nòta enregistrada");
define("LANGAGENDA52","Nòta mesa a jorn");
define("LANGAGENDA53","Nòta suprimida");
define("LANGAGENDA54","Ocuréncia de la nòta suprimida");
define("LANGAGENDA55","Anniversari enregistrat");
define("LANGAGENDA56","Anniversari mes a jorn");
define("LANGAGENDA57","Anniversari suprimit");
define("LANGAGENDA58","Compte creat, vos podètz connectar");
define("LANGAGENDA59","L'enregistrament a fracassat");
define("LANGAGENDA60","Totis les camps");
define("LANGAGENDA61","Societat");
define("LANGAGENDA62","Nom + Petit nom");
define("LANGAGENDA63","Adreça");
define("LANGAGENDA64","Numèro de telefòn");
define("LANGAGENDA65","Adreça Email");
define("LANGAGENDA66","Comentaris");
define("LANGAGENDA67","Aviar la recèrca");
define("LANGAGENDA68","Societat");
define("LANGAGENDA69","Nom");
define("LANGAGENDA70","Petit nom");
define("LANGAGENDA71","Adreça");
define("LANGAGENDA72","Vila");
define("LANGAGENDA73","País");
define("LANGAGENDA74","Tel. Domicili");
define("LANGAGENDA75","Tel. Trabalh");
define("LANGAGENDA76","Tel.&nbsp;Portable");
define("LANGAGENDA77","Fax");
define("LANGAGENDA78","Email");
define("LANGAGENDA79","Email Pro");
define("LANGAGENDA80","Nòta / Divèrs");
define("LANGAGENDA81","Grop");
define("LANGAGENDA82","Partiment");
define("LANGAGENDA83","CP");
define("LANGAGENDA84","Data de naissença");
define("LANGAGENDA85","Recomençar");
define("LANGAGENDA86","Importar");
define("LANGAGENDA87","Impòrt acabat");
define("LANGAGENDA88","contacte(s) apondut(s)");
define("LANGAGENDA89","Pas de contacte disponible !");
define("LANGAGENDA90","<LI>Dins Outlook, far <I>Fichièr</I>-&gt;<I>Exportar</I>-&gt;<I>Autre quasernet d'adreças...</I></LI>");
define("LANGAGENDA91","<LI>Causir <I>Fichièr tèxte (valors separadas per de virgulas)</I> puèi <I>Exportar</I></LI>");
define("LANGAGENDA92","<LI>Causir l'endreit ont le fichièr serà salvat puèi <I>Seguent</I></LI>");
define("LANGAGENDA93","<LI>Dins la lista dels camps a exportar, seleccionar :<BR>");
define("LANGAGENDA94","<I>Petit nom, Nom, Adreça de messatjariá, Carrièra (domicili), Vila (domicili), Còdi Postal (domicili), País/region (domicili), Telefòn personal, Telefòn mobil, Telefòn professional, Fax professionala, Societat</I> puèi clicar sus <I>Acabar</I></LI>");
define("LANGAGENDA95","<LI>Recuperar le fichièr atal creat dins le formulari çaijós e clicar sus <I>Importar</I></LI>");
define("LANGAGENDA96","Entratz una societat per la recèrca");
define("LANGAGENDA97","Entratz un nom o un petit nom per la recèrca");
define("LANGAGENDA98","Entratz una adreça per la recèrca");
define("LANGAGENDA99","Entratz un numèro de telefòn per la recèrca");
define("LANGAGENDA100","Entratz una adreça Email per la recèrca");
define("LANGAGENDA101","Sasissètz una briga de comentari per la recèrca");
define("LANGAGENDA102","Entratz al mens un critèri per la recèrca");
define("LANGAGENDA103","Sètz segur que volètz escafar aqueste contacte ?");
define("LANGAGENDA104","Annada");
define("LANGAGENDA105","Pas de paire");
define("LANGAGENDA106","Lista de las personas<BR>a las qualas podètz<BR>afectar una nòta");
define("LANGAGENDA107","Persona(s) possibla(s)");
define("LANGAGENDA108","Persona(s) seleccionada(s)");
define("LANGAGENDA109","Precision d'afichatge");
define("LANGAGENDA110","Escalon de 30mn");
define("LANGAGENDA111","Escalon de 15mn");
define("LANGAGENDA112","Ora de començament");
define("LANGAGENDA113","Ora de fin");
define("LANGAGENDA114","Ocupat");
define("LANGAGENDA115","Parcial");
define("LANGAGENDA116","Liure");
define("LANGAGENDA117","Crear una nòta entre ");
define("LANGAGENDA118","Detalh per utilizaire d'aquesta jornada");
define("LANGAGENDA119","Afichar");
define("LANGAGENDA120","Seleccionatz una persona");
define("LANGAGENDA121","Seleccionatz una ora de fin posteriora a l'ora de començament");
define("LANGAGENDA122","Setmana del ");
define("LANGAGENDA123","al");
define("LANGAGENDA124","Setmana seguenta");
define("LANGAGENDA125","Levar");
define("LANGAGENDA126","Disponibilitats de le les vòstres contactes pel ");
define("LANGAGENDA127","Apondre");
define("LANGAGENDA128","Fòra Perfil");
define("LANGAGENDA129","Seleccionatz una ora de fin posteriora a l'ora de començament");
define("LANGAGENDA130","Precision d'afichatge");
define("LANGAGENDA131","Sasissètz un nom");
define("LANGAGENDA132","Sasissètz una URL");
define("LANGAGENDA133","Apondre un favorit");
define("LANGAGENDA134","Impression en mòde païsatge conselhada");
define("LANGAGENDA135","Setmana precedenta ");
define("LANGAGENDA136","Setmana ");
define("LANGAGENDA137","del");
define("LANGAGENDA138","Anniversari");
define("LANGAGENDA139","Rapèl per defaut a la creacion d'una nòta");
define("LANGAGENDA140","Pas de rapèl");
define("LANGAGENDA141","Rapèl");
define("LANGAGENDA142","còpia per mail");
define("LANGAGENDA143","minuta(s)");
define("LANGAGENDA144","ora(s)");
define("LANGAGENDA145","jorn(s)");
define("LANGAGENDA146","Jornada tipe");
define("LANGAGENDA147","Acaba a");
define("LANGAGENDA148","Telefòn VF");
define("LANGAGENDA149","Interfàcia");
define("LANGAGENDA150","Planning per defaut");
define("LANGAGENDA151","Quotidian");
define("LANGAGENDA152","Setmanièr");
define("LANGAGENDA153","Mesadièr");
define("LANGAGENDA154","30 minutas");
define("LANGAGENDA155","15 minutas");
define("LANGAGENDA156","45 minutas");
define("LANGAGENDA157","1 ora");
define("LANGAGENDA158","Seleccion automatica de l'ora de fin d'una nòta");
define("LANGAGENDA159","Partiment del planning<BR>en consultacion");
define("LANGAGENDA160","Personas autorizadas a consultar mon planning");
define("LANGAGENDA161","Pas partejat");
define("LANGAGENDA162","A la tria");
define("LANGAGENDA163","Tot le monde");
define("LANGAGENDA164","Partimebt del planning<BR>en modificacion");
define("LANGAGENDA165","Persona(s) que me pòt/pòdon afectar una nòta");
define("LANGAGENDA166","M'informar per mail quand una nòta m'es afectada");
define("LANGAGENDA167","Suprimir aquesta nòta qu'ai creada");
define("LANGAGENDA168","Suprimir aquesta nòta que m'es estada afectada");
define("LANGAGENDA169","M'apropriar aquesta nòta que m'es estada afectada");
define("LANGAGENDA170","Tota la jornada");
define("LANGAGENDA171","Causida del libellat");
define("LANGAGENDA172","Novèl libellat");
define("LANGAGENDA173","Intitulat");
define("LANGAGENDA174","Durada mejana");
define("LANGAGENDA175","Color");
define("LANGAGENDA176","Aparéncia de la nòta");
define("LANGAGENDA177","Suprimir aqueste libellat ?");
define("LANGAGENDA178","Enregistrar un memo");
define("LANGAGENDA179","Sasissètz un títol");
define("LANGAGENDA180","Títol");
define("LANGAGENDA181","Contengut");
define("LANGAGENDA182","Sètz segur que volètz escafar aqueste memo ?");
define("LANGAGENDA183","Enregistrar una nòta");
define("LANGAGENDA184","La nòta que volètz modificar aparten a una seria recurrenta");
define("LANGAGENDA185","Volètz modificar tota la seria o unicament aquesta ocuréncia ?");
define("LANGAGENDA186","Tota la seria");
define("LANGAGENDA187","Unicament aquesta ocuréncia");
define("LANGAGENDA188","Nòta que cobrís tota la jornada");
define("LANGAGENDA189","Afichar le calendièr");
define("LANGAGENDA190","Tota la jornada");
define("LANGAGENDA191","Comença a");  // Començament à
define("LANGAGENDA192","Persona<BR>concernida");
define("LANGAGENDA193","Aparéncia de la nòta");
define("LANGAGENDA194","Nòta publica");
define("LANGAGENDA195","nòta detalhada dins le partiment de planning");
define("LANGAGENDA196","mencion \"Ocupat\" dins le partiment de planning");
define("LANGAGENDA197","Nòta privada");
define("LANGAGENDA198","Ocupat(-ada)");
define("LANGAGENDA199","considerar coma <B>pas disponible</B> dins le modul de las disponibilitats");
define("LANGAGENDA200","Liure");
define("LANGAGENDA201","considerar coma <B>liure</B> dins le modul de las disponibilitats");
define("LANGAGENDA202","Color");
define("LANGAGENDA203","Partiment");
define("LANGAGENDA204","Disponibilitat");
define("LANGAGENDA205","Rapèl");
define("LANGAGENDA206","Pas de rapèl");
define("LANGAGENDA207","còpia per mail");
define("LANGAGENDA208","a l'avança");  // a l'avance
define("LANGAGENDA209","Periodicitat");
define("LANGAGENDA210","Pas cap");
define("LANGAGENDA211","Quotidiana");
define("LANGAGENDA212","Setmanièra");
define("LANGAGENDA213","Mesadièra");
define("LANGAGENDA214","Annadièra");
define("LANGAGENDA215","Totis les ");
define("LANGAGENDA215bis","jorns");
define("LANGAGENDA216","Totis les jorns dobrants (Diluns al Divendres)");
define("LANGAGENDA217","Totis les jorns de ma setmana tipe");
define("LANGAGENDA218","Las informacions sasidas o modificadas seràn pas enregistradas\\nSètz segur que volètz contunhar ?");
define("LANGAGENDA219","perfil");
define("LANGAGENDA220","Totis les ");
define("LANGAGENDA221","Totas las ");
define("LANGAGENDA221bis","setmanas");
define("LANGAGENDA222","de cada mes");
define("LANGAGENDA223","primièr");
define("LANGAGENDA224","segond");
define("LANGAGENDA225","tresen");
define("LANGAGENDA226","quatren");
define("LANGAGENDA227","darrièr");
define("LANGAGENDA228","del mes");
define("LANGAGENDA229","Le ");
define("LANGAGENDA230","Definir la data de fin");
define("LANGAGENDA231","Fin aprèp"); // Fin après
define("LANGAGENDA232","Fin le");
define("LANGAGENDA233","ocuréncia(s)");
define("LANGAGENDA234","Sasissètz un libellat");
define("LANGAGENDA235","Sasissètz una data");
define("LANGAGENDA236","Seleccionatz una ora de fin\\nposteriora a l'ora de començament");  // \\n signifie un retour chariot
define("LANGAGENDA237","Seleccionatz una persona");
define("LANGAGENDA238","Sasissètz un nombre de jorns\\nsuperior o egal a 1");
define("LANGAGENDA239","Sasissètz un nombre d'ocuréncias\\nsuperior o egal a 1");
define("LANGAGENDA240","Repeticion"); // repeticion
define("LANGAGENDA241","Sasissètz le vòstre nom e le vòstre petit nom al préalable");
define("LANGAGENDA242","Sasissètz le vòstre Petit nom");
define("LANGAGENDA243","Vos cal sasir le vòstre login");
define("LANGAGENDA244","Sasissètz le vòstre ancian senhal");
define("LANGAGENDA245","Senhals diferents");
define("LANGAGENDA246","Un senhal es obligatòri");
define("LANGAGENDA247","Seleccionatz una ora de fin\\nposteriora a l'ora de començament");
define("LANGAGENDA248","Suprimir aquesta ocuréncia");
define("LANGAGENDA249","Nòta recurrenta");
define("LANGAGENDA250","Suprimir aquesta nòta qu'ai creada");
define("LANGAGENDA251","M'apropriar aquesta nòta que m'es estada afectada");
define("LANGAGENDA252","Filtrar");
define("LANGAGENDA253","Imprimir aqueste planning");
define("LANGAGENDA254","Impression en mòde païsatge conselhada");
define("LANGAGENDA255","Nòta creada per ");
define("LANGAGENDA256","Cambiar l'estatut");
define("LANGAGENDA257","Suprimir aquesta ocuréncia");
define("LANGAGENDA258","Suprimir aquesta nòta qu'ai creada");
define("LANGAGENDA259","Suprimir aquesta nòta que m'es estada afectada");
define("LANGAGENDA260","una nòta");
define("LANGAGENDA261","un anniversari");
define("LANGAGENDA262","un contacte");
define("LANGAGENDA263","A l'utilizaire seleccionat çaijós");
define("LANGAGENDA264","Apondre una nòta");
define("LANGAGENDA265","Recèrca");
define("LANGAGENDA266","Disponibilitats");
define("LANGAGENDA267","Contactes");
define("LANGAGENDA268","Memo");
define("LANGAGENDA269","Libellats");
define("LANGAGENDA270","Favorits");
define("LANGAGENDA271","Perfil");
define("LANGAGENDA272","Fracàs creacion expòrt");
define("LANGAGENDA273","Agenda de ");
// FIN AGENDA

define("LANGL","L");  // L de diluns
define("LANGM","M");  // M de dimars
define("LANGME","M");  // M de dimècres
define("LANGJ","J");  // J de dijòus
define("LANGV","V");  // V de divendres
define("LANGS","S");  // S de dissabte
define("LANGD","D");  // D de dimenge

define("LANGL1","Lun"); // Jorns sus 3 letras
define("LANGM1","Mar");	// Jorns sus 3 letras
define("LANGME1","Mèr"); // Jorns sus 3 letras
define("LANGJ1","Jòu");	// Jorns sus 3 letras
define("LANGV1","Ven");	// Jorns sus 3 letras
define("LANGS1","Sab");	// Jorns sus 3 letras
define("LANGD1","Dim");	// Jorns sus 3 letras

define("LANGMOIS21","Gen");			// mes abreujat
define("LANGMOIS22","Feb"); 		// mes abreujat
define("LANGMOIS23","Març");			// mes abreujat
define("LANGMOIS24","Abr");				// mes abreujat
define("LANGMOIS25","Mai");				// mes abreujat
define("LANGMOIS26","Junh");			// mes abreujat
define("LANGMOIS27","Julh");			// mes abreujat
define("LANGMOIS28","Agos");		// mes abreujat
define("LANGMOIS29","Set");			// mes abreujat
define("LANGMOIS210","Oct");			// mes abreujat
define("LANGMOIS211","Nov"); 			// mes abreujat
define("LANGMOIS212","Dec"); 	// mes abreujat



define("LANGPROFP22","Aqueste ensenhant es ja assignat coma professor principal. \\n\\n L'Equipa TRIADE");



define("LANGSTAGE23","Nom de l'activitat");
define("LANGSTAGE24","Enregistrar una novèla entrepresa");
define("LANGSTAGE25","Le nom d'aquesta entrepresa es ja enregistrat");
define("LANGSTAGE26","Nom de l'entrepresa");
define("LANGSTAGE27","Contacte");
define("LANGSTAGE28","Adreça");
define("LANGSTAGE29","Còdi Postal");
define("LANGSTAGE30","Vila");
define("LANGSTAGE31","Sector Activitat");
define("LANGSTAGE32","apondre activitat");
define("LANGSTAGE33","Activitat principala");
define("LANGSTAGE34","Telefòn");
define("LANGSTAGE35","Fax");
define("LANGSTAGE36","Email");
define("LANGSTAGE37","Informacions");
define("LANGSTAGE38","Consultacion de las entrepresas");
define("LANGSTAGE39","Societat");
define("LANGSTAGE40","Activitat principala");
define("LANGSTAGE41","Autra recèrca");
define("LANGSTAGE42","Tel. / Fax");
define("LANGSTAGE43","Pas cap d'entrepresa per aqueste nom");
define("LANGSTAGE44","Planificacion dels estagis");
define("LANGSTAGE45","Data de començament d'estagi");
define("LANGSTAGE46","Data de fin d'estagi");
define("LANGSTAGE47","Enregistrar l'estagi");
define("LANGSTAGE48","Numèro de l'estagi");
define("LANGSTAGE49","Modificacion de las datas d'estagi");
define("LANGSTAGE50","Estagi");
define("LANGSTAGE51","Data de l'estagi");
define("LANGSTAGE52","Error de sasida");
define("LANGSTAGE53","Estagi mesa a jorn");
define("LANGSTAGE54","L'estagi del ");
define("LANGSTAGE55","per la classa de");
define("LANGSTAGE56","est enregistrat");
define("LANGSTAGE57","Data d'estagi, suprimida \\n\\n L'Equipa TRIADE");
define("LANGSTAGE58","Entrepresa enregistrada \\n\\n L'Equipa TRIADE");
define("LANGSTAGE59","Modificacion d'entrepresa");
define("LANGSTAGE60","Entrepresas per activitat");
define("LANGSTAGE61","Recèrca d'entrepresas");
define("LANGSTAGE62","Info");
define("LANGSTAGE63","Lista completa");
define("LANGSTAGE64","Visualizacion de las datas d'estagi");
define("LANGSTAGE65","Supression d'entrepresa");
define("LANGSTAGE66","Entrepresa Suprimida \\n\\n L'Equipa TRIADE");
define("LANGSTAGE67","Consultar las entrepresas per activitat");
define("LANGSTAGE68","Aucune entrepresa per aqueste nom");
define("LANGSTAGE69","Visualizacion d'un ".INTITULEELEVE." a un estagi");
define("LANGSTAGE70","Imprimir l'estagi numèro");
define("LANGSTAGE71","Visualizacion d'un ".INTITULEELEVE." als estagis");
define("LANGSTAGE72","&nbsp;Data&nbsp;de&nbsp;l'Estagi&nbsp;"); // respecter les &nbsp;
define("LANGSTAGE73","Retorn");
define("LANGSTAGE74","Entrepresa");
define("LANGSTAGE75","Afectacion d'un ".INTITULEELEVE." a un estagi");
define("LANGSTAGE76","Luòc de l'estagi");
define("LANGSTAGE77","Responsable");
define("LANGSTAGE78","Ensenhant Visitor");
define("LANGSTAGE79","Albergat");
define("LANGSTAGE80","Noirit");
define("LANGSTAGE81","Passatge dins n servicis");
define("LANGSTAGE82","Rason cmbment de servici");
define("LANGSTAGE83","Info. complementàrias");
define("LANGSTAGE84","Creacion enregistrada \\n \\n L'Equipa TRIADE");
define("LANGSTAGE85","Data de la visita");
define("LANGSTAGE86","Modificacion d'un ".INTITULEELEVE." a un estagi");
define("LANGSTAGE87","Informacions enregistradas");
define("LANGSTAGE88","Supression d'un ".INTITULEELEVE." a un estagi");


define("LANGRESA62","Libellat");
define("LANGRESA63","Refusar");
define("LANGRESA64","Apondre una demanda");
define("LANGRESA65","&nbsp;De&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;a");
define("LANGRESA66","Reservat");
define("LANGRESA66bis","pzr");  // seguida reservat par
define("LANGRESA67","Pas confirmat");
define("LANGRESA68","Confirmat");
define("LANGRESA69","Enregistrament acabat");
define("LANGRESA70","reservacion pel ");






define("LANGNOTEUSA1","Configuracion de las atribucions de las nòtas pel mòde USA");
define("LANGNOTEUSA2","Aqueste modul vos permet de posicionar las letras en foncion del percentatge d'atribuir a cada nòta (letra).");
define("LANGNOTEUSA3","Exemple : de 95 a 100 --> A+ , de 87 a 94  --> A, etc...");
define("LANGNOTEUSA4","De");
define("LANGNOTEUSA4bis","a");
define("LANGNOTEUSA4ter","equival a");   //  ex : De  10 a 20 équivaut a B
define("LANGNOTEUSA5","Entre la nòta");
define("LANGNOTEUSA5bis","e la nòta");
define("LANGNOTEUSA5ter","aquò equival a");



define("LANGABS56","Lista de las abséncias pas justificadas");
define("LANGABS57","Mesa a jorn realizada per aquesta lista d'".INTITULEELEVE."s");




define("LANGSANC1","Sanccion creada -- L'Equipa TRIADE");
define("LANGSANC2","Categoria pas suprimida. Aquesta categoria es ja afectada a una sanccion o un ".INTITULEELEVE." -- Equipa TRIADE");
define("LANGSANC3","Configuracion Disciplina");
define("LANGSANC4","Enregistrament de las categorias.");
define("LANGSANC5","Intitulat de la categoria");
define("LANGSANC6","Enregistrament dels noms de las sanccions per categoria.");
define("LANGSANC7","Intitulat de la sanccion");
define("LANGSANC8","Configuracion retenguda");
define("LANGSANC9","Avertiment d'un messatge  quand l'".INTITULEELEVE."  a atent le limit autorizat.");
define("LANGSANC10","Per  la categoria");
define("LANGSANC11","Avertiment d'un messatge al cap de");
define("LANGSANC12","Nb de còps");
define("LANGSANC13","Creat per");
define("LANGSANC14","Data de sasida");

// Modificacion de ces 2 phrases a traduire
// define("LANGPARAM1","<font class=T1>Compausatz le vòstre tèxte pel contengut del messatge de l'abséncia pel mandadís del corrièr aux parents d'".INTITULEELEVE.". Per una presa en compte del nom e del petit nom de l'".INTITULEELEVE." automaticament dins cada document, precisatz la cadena <b>NomEscolan</b> e <b>Petit nomEscolan</b> a l'emplaçament désiré. De même possibilitat d'indicar la classa amb le mot clau <b>ClassaEscolan</b>, o la data de l'abséncia ABSDEBUT o ABSFIN atal que la durada ABSDUREE </font><br><br>");
// define("LANGPARAM2","<font class=T1>Compausatz le vòstre tèxte pel contengut del messatge de retard pel mandadís del corrièr aux parents. Per una presa en compte del nom e del petit nom de l'".INTITULEELEVE." automaticament dins cada document, precisatz la cadena <b>NomEscolan</b> e <b>Petit nomEscolan</b> a l'emplaçament désiré. De même possibilitat d'indicar la classa amb le mot clau <b>ClassaEscolan</b>, o la data del retard RTDDATE , l'ora RTDHEURE atal que le durada RTDDUREE </font><br><br>");


define("LANGMODIF4","Modificacion d'un compte");
define("LANGMODIF5","Informacions de connexion");
define("LANGMODIF6","Fòto d'identitat");
define("LANGMODIF7","Coordenadas del compte");
define("LANGMODIF8","Adreça");
define("LANGMODIF9","Còdi Postal");
define("LANGMODIF10","Comuna");
define("LANGMODIF11","Tel.");
define("LANGMODIF12","Email");
define("LANGMODIF13","Modificar le compte");
define("LANGMODIF14","Compte modificat -- Equipa TRIADE");
define("LANGMODIF15","Le senhal de ");
define("LANGMODIF15bis"," es estat modificat.");
define("LANGMODIF16","Modificacion del senhal");
define("LANGMODIF17","Impossible fòto de talha pas confòrma");
define("LANGMODIF18","Reactualizar aquesta fòto");
define("LANGMODIF19","Apondre la fòto");
define("LANGMODIF20","Modificar la fòto");

define("LANGGRP25bis","Gestion dels gropes");
define("LANGGRP26","Lista dels gropes");
define("LANGGRP27","Apondre un ".INTITULEELEVE." dins un grop");
define("LANGGRP28","Suprimir un ".INTITULEELEVE." d'un grop");
define("LANGGRP29","Nom del Grop");
define("LANGGRP30","Classa(s) concernida(s)");
define("LANGGRP31","Modificar lista");
define("LANGGRP32","Apondre de ".INTITULEELEVE."s dins le grop");
define("LANGGRP33","Apondre un ".INTITULEELEVE." dins aqueste grop");
define("LANGGRP34","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." en classa de ");
define("LANGGRP35","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." dins le grop");
define("LANGGRP36","Validar le grop");
define("LANGGRP37","Grop modificat -- Equipa TRIADE ");
define("LANGGRP38","Lista dels ".INTITULEELEVE."s del grop ");
define("LANGGRP39","Pas cap ".INTITULEELEVE." dins aqueste grop");

define("LANGCARNET1","Quasernet de nòtas");
define("LANGCARNET2","Classa de l'".INTITULEELEVE);
define("LANGCARNET3","Clicatz sul <b>nom</b> de l'".INTITULEELEVE);

define("LANGPASSG1","Le senhal deu èstre de <b>8 caractèrs</b> minimum,<br /> <b>alfanumeric</b> e utilizant <b>majuscula e minuscula</b>.");
define("LANGPASSG2","Le senhal es pas corrècte. \\n Le senhal deu comportar : \\n\\n -> 8 caractèrs minimum, \\n -> alfanumeric, \\n -> majuscula e minuscula \\n\\n L\\'Equipa TRIADE");
define("LANGPASSG3","Fracàs de la creacion");



define("LANGDISC38","Apondre Sanccion");
define("LANGDISC39","Gestion de las disciplinas");
define("LANGDISC40","Retenguda pas efectuada.");
define("LANGDISC41","Planning Retenguda.");
define("LANGDISC42","Retenguda pas afectada a un escolan.");
define("LANGDISC43","Configuracion.");
define("LANGDISC44","Suprimir Retengudas e sanccions");
define("LANGDISC45","Suprimir Retengudas e sanccions");
define("LANGDISC46","Lista de las abséncias e dels retards d'una classa");
define("LANGDISC47","Indicatz le començament del periòde");
define("LANGDISC48","Indicatz la fin del periòde");
define("LANGDISC49","Indicatz la seccion");
define("LANGDISC50","<br><ul>Supression de las retengudas e de las sanccions en <br>foncion de l'interval de data.</ul>");
define("LANGDISC51","Totas las classas");
define("LANGDISC52","Retengudas e sanccions suprimidas");
define("LANGDISC53","Error ! Retengudas e sanccions pas suprimidas");

define("LANGIMP53","Fichièr ASCII via SQL ");


// autre new

define("LANGSTAGE31bis","2nd Sector Activitat");
define("LANGSTAGE31ter","3en Sector Activitat");
define("LANGMEDIC1","Dorsièr medical d'un ".INTITULEELEVE);
define("LANGMEDIC2","Mandar la recèrca");
define("LANGMEDIC3","Informacion / Modificacion");


define("LANGDISC54","Visualizar las disciplinas d'un escolan");
define("LANGDISC55","Suprimir una Sanccion");
define("LANGDISC56","Suprimir Sanccion");

define("LANGBASE6bis","Total d'".INTITULEELEVE."s dins le fichièr ");

define("LANGMODIF21","Le senhal deu aver : \\n\\n - 8 caractèrs minimum \\n - Alfanumeric \\n - MAJUSCULA e minuscula.\\n\\n Equipa TRIADE");

define("LANGMODIF22","Senhal : 8 caractèrs - Alfanumeric - Majusculas e minusculas");
define("LANGPASS1bis","Confirmar senhal");

define("LANGMODIF23","Podètz cambiar le vòstre senhal per le vòstre compte TRIADE");
define("LANGMODIF24","Le compte ");
define("LANGMODIF24bis","es en cors de validacion..");
define("LANGMODIF24ter","es ara operacional");
define("LANGMODIF25","Senhal pas identic. \\n\\n Equipa TRIADE");

define("LANGABS58","Visualizacion / Supression  Abséncia - Retard");
define("LANGABS59","Afichatge complet dels retards");
define("LANGABS60","Pendent");  	// una durada pendant tant de temps
define("LANGABS61","Visualizacion / Modificacion d'una  Abséncia - Retard");
define("LANGABS62","Afichatge <b>complet</B> dels rtds e abs");
define("LANGABS63","Sasida le");
define("LANGABS64","Afichatge dels <b>5</B> darrièrs rtd e abs");
define("LANGABS65","Afichatge complet de las abséncias");
define("LANGABS66","Mesa a jorn efectuada per aquesta lista d'".INTITULEELEVE."s");
define("LANGABS6bis","Lista dels retards pas justificats");
define("LANGABS4bis","Listar las abséncias o retards");
define("LANGABS67","<font class=T2>Pas cap d'escolan dins aquesta classa</font>");
define("LANGABS68","Abs / Rtds d'una classa");
define("LANGABS69","Cumul abs/rtds dels ".INTITULEELEVE."s");
define("LANGABS70","Configuracion dels motius");
define("LANGABS71","Nombre d'abséncias / Cumul");
define("LANGABS72","Nombre de Retards / Cumul");
define("LANGABS73","Abséncias - Retards -  de la classa ");
define("LANGABS74","Efectuar la mesa a jorn");
define("LANGABS75","Pas cap d'absent o retard");
define("LANGABS76","relevat a ");

define("LANGDEPART3","En seguida a un problèma tecnic,");
define("LANGDEPART4","l'accès al servidor es indisponible. L'equipa TRIADE interven actualament sul servidor.");

define("LANGBASE3_2","Aquí la lista dels fichièrs que pòdon èstre importats.");
define("LANGbasededoni21_2","Volètz contunhar ? \\n\\n L\'Equipa TRIADE");
define("LANGbasededon21","Le mandadís del fichièr pòt durar de <b>2 a 4 mn</b> en foncion del nombre d'elements.");
define("LANGbasededon31_2","Indicatz las matèrias que volètz importar.");
define("LANGBASE10_2","Indicatz les ensenhants d'apondre.");

define("LANGBASE16_2"," Las colomnas son representadas jos la forma : <b>nom de login ; petit nom de login ; senhal en clar</b>");
define("LANGIMP25_2","nom establiment");
// ----------------------------- //
define("LANGABS77","Signalé le");
define("LANGSTAGE89","Etablir la convencion d'estagi");
define("LANGSTAGE90","Sortir las convencions d'estagi");
define("LANGSTAFE91","Lista dels ".INTITULEELEVE."s en entrepresa actualament");
define("LANGSTAGE92","Lista dels ".INTITULEELEVE."s en entrepresa actualament");
define("LANGPASSG4","Le senhal deu èstre de <b>8 caractèrs</b> minimum <br /><b>alfanumeric</b>.");
define("LANGPASSG5","Le senhal deu èstre de <b>4 caractèrs</b> minimum.");
define("LANGPASSG6","Le senhal es pas corrècte. \\n Le senhal deu comportar : \\n\\n -> 8 caractèrs minimum, \\n -> alfanumeric \\n\\n L\\'Equipa TRIADE");
define("LANGPASSG7","Le senhal es pas corrècte. \\n Le senhal deu comportar : \\n\\n -> 4 caractèrs minimum. \\n\\n L\\'Equipa TRIADE");

define("LANGMODIF22_1","Senhal : 4 caractèrs");
define("LANGMODIF22_2","Senhal : 8 caractèrs - Alfanumeric ");
define("LANGMODIF22_3","Senhal : 8 caractèrs - Alfanumeric - Majusculas e minusculas");
define("LANGDEPART2","<font color=red  class=T2>ATENCION, per utilizar TRIADE, la variable php '<strong>register_globals</strong>' deu èstre sus <u>Off</u>.</font><br />");


define("LANGacce15","Dever a remetre pel ");
define("LANGacce16","Dever de tornar uèi !");
define("LANGacce17","Apondon d'una sanccion disciplinària");

define("LANGBASE41","Suprimir totis les ".INTITULEELEVE."s abans l'impòrt");
define("LANGBASE7bis","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))." ja afectat");
define("LANGBASE8bis","pels ".INTITULEELEVE."s <u>afectats</u> e <u>sens classa</u>");

define("LANGPER21bis","Lenga&nbsp;/&nbsp;opcion");

define("LANGASS6ter","".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGASS41","Emmagazinatge");
define("LANGASS42","Parametratge");

define("LANGIMP46bis","Senhal");

define("LANGIMP54","N° carrièra");
define("LANGIMP55","adreça");
define("LANGIMP56","còdi postal");
define("LANGIMP57","telefòn");
define("LANGIMP58","email");
define("LANGIMP59","comuna");

define("LANGBULL1pp","Impression bulletin trimestral o semestral");
define("LANGBT43pp","Imprimir Tablèu");


define("LANGMESS38","Messatge legit.");
define("LANGMESS39","Messatge pas legit.");


define("LANGDISC57","Motiu&nbsp;/&nbsp;Sanccion");

define("CUMUL01","Cumul de las abséncias e retards d'una classa per ".INTITULEELEVE);
define("CUMUL02","Cumul de las sanccions d'una classa per ".INTITULEELEVE);
define("CUMUL03","Cumul de las sanccions d'un ".INTITULEELEVE);
define("LANGPROJ18bis","ora(s)");
define("LANGCREAT1","Compte ja existent.");
define("ERREUR1","Ret Internet pas disponibla per aqueste modul.");
define("ERREUR2","Consultar le modul Configuracion per activar la ret.");


define("PASSG8","Modificacion del senhal");
define("PASSG9","Le senhal de l'".INTITULEELEVE." ");
define("PASSG9bis"," es estat modificat.");


define("LANGPARAM34","Site Web de l'establiment");
define("LANGLOGO3bis","Le lògo <b>deu èstre al format jpg</b>");


define("LANGMAT1","Enregistrar matèria");
define("LANGMAT2","Lista / Modificacion d'una matèria");
define("LANGMAT3","Suprimir matèria");
define("LANGMAT4","Validar la modificacion");
define("LANGMAT5","Matèria modificada");
define("LANGMAT6","Matèria ja afectada");
define("LANGCLAS1","Lista / Modificar classa");
define("LANGCLAS2","Classa modificada");
define("LANGCLAS3","Classa ja afectada");

define("LANGDEVOIR1","pel grop");
define("LANGDEVOIR2","per la classa");
define("LANGDEVOIR3","Enregistrar un dever escolar");
define("LANGCIRCU111","<font face=Verdana size=1><B><font color=red>D</font></B>ocument al format : <b> doc</b>, <b>pdf</b>, <b>txt</b>.</FONT>");

define("LANGAFF7","Modul de supression d'afectacion de las classas.");
define("LANGAFF8","ATENCION aqueste modul es a utilizar al moment de la supression d'afectacion,<br> destrutz totas las nòtas dels ".INTITULEELEVE."s  de las classas suprimidas.");
define("LANGAFF9","ATENCION, las nòtas de las classas seleccionadas seràn suprimidas. \\n Volètz contunhar ? \\n\\n Equipa TRIADE");
define("LANGCREAT2","Suprimir compte");


define("LANGPROF37","Quasèrn de tèxtes.");

// news

define("LANGPARAM35","Causida del bulletin");
define("LANGPROBLE1","responsa per email");
define("LANGPROBLE2","Totis les camps devon èstre rensenhats");
define("LANGMESS37","Aqueste modul es pas estat validat per l'administrator TRIADE.<br><br> L'Equipa TRIADE");

define("LANGPROFP23","Nòtas escolaras de ");
define("LANGPROFP24","del  mes de");
define("LANGPROFP25","Trombinoscòpi");
define("LANGPROFP26","Seguiment d'un ".INTITULEELEVE);
define("LANGPROFP27","Informacions suls delegats");
define("LANGPROFP28","Messatge per la classa");
define("LANGPROFP29","Circulara per la classa");
define("LANGPROFP30","Gestion d'estagi professional");
define("LANGPROFP31","Tablèu de las mejanas dels ".INTITULEELEVE."s");
define("LANGPROFP32","Bulletins grafics dels ".INTITULEELEVE."s");


define("LANGLETTRELUNDI","L");	  // Diluns
define("LANGLETTREMARDI","M");    // Dimars
define("LANGLETTREMERCREDI","M"); // Dimècres
define("LANGLETTREJEUDI","J");    // Dijòus
define("LANGLETTREVENDREDI","V"); // Divendres
define("LANGLETTRESAMEDI","S");   // Dissabte
define("LANGLETTREDIMANCHE","D"); // Dimenge



define("LANGRESA71","reservacion per le");
define("LANGRESA72","de");
define("LANGRESA73","a");
define("LANGRESA74","Informacions complementàrias");

define("LANGbasededoni52","valor acceptada : <b>0</b> o Sr.<br>");
define("LANGbasededoni53","valor acceptada : <b>1</b> o Dna.<br>");
define("LANGbasededoni54","valor acceptada : <b>2</b> o Dsla.<br>");
define("LANGbasededoni54_2","valor acceptada : <b>3</b> o Sr <br>");
define("LANGbasededoni54_3","valor acceptada : <b>4</b> o Mr <br>");
define("LANGbasededoni54_4","valor acceptada : <b>5</b> o Mma <br>");


define("LANGacce_dep2bis","<br><b>ATENCION !!  Verificatz plan le vòstre mòde d'accès,<br> causissètz le vòstre compte correspondent.</b>");

define("LANGNA3bis","Senhal parent "); //
define("LANGNA3ter","Senhal ".INTITULEELEVE." "); //

define("LANGELE244","Email");

define("LANGTP12","Validatz le vòstre compte");

define("LANGMESS40","Avètz <strong> ");
define("LANGMESS40bis"," </strong> flux(es) RSS enregistrat(s).");  // apondre "\" devant les quotes
define("LANGMESS41","Compte ");  // Compte coma "compte utilizaire".
define("LANGMESS42","Segonda connexion");
define("LANGMESS43","Darrièra connexion le");

define("LANGALERT4","ATENCION, causissètz des noms de subjècte diferent.");

define("LANGMODIF26","Modificar sosmatèria");
define("LANGPROF38","Nòtas Trimestralas");
define("LANGPROF39","Complement d'informacion");

define("LANGCIRCU21","Disp. per"); // abréviation de "Disponible pour" 

define("LANGTELECHARGE","Telecargar"); //  downloader

define("LANGPARENT15bis","Sanccion del");
define("LANGDISC2bis","Imprimir las sanccions del jorn");

define("LANGRECH5","Indicar l'element o les elements de recercar");
define("LANGRECH6","Triar per òrdre");

define("LANGPROFP33","Emplenar les bulletins");
define("LANGPROFP34","Verificar bulletin");
define("LANGPROFP35","Consultar o modificar les comentaris dels bulletins");


define("LANGPROFP36","Pas cap de data trimestrala <br /><br /> afectada per <u>aquesta annada escolara</u>");
define("LANGPROFP37","Enregistrar les comentaris");

define("LANGGRP40","Grop creat");
define("LANGGRP41","Aquí la lista dels ".INTITULEELEVE."s pas enregistrats");
define("LANGGRP42","Aqueste grop existís ja");
define("LANGGRP43","Error de fichièr");
define("LANGGRP44","Suprimir un grop");
define("LANGGRP45","Importar fichièr");
define("LANGGRP46","Nom de grop existent -- Servici TRIADE");

define("LANGPARAM37","Acadèmia");
define("LANGAGENDA274","Fèsta del jorn ");
define("LANGPARAM38","Urós Anniversari a ");
define("LANGEDT1","F"); // primièra letra
define("LANGEDT1bis","ichièr al format <b>xml</b> o <b>zip</b> <br>Talha maximum del fichièr : ");
define("ERREUR3","Contactar l'administrator TRIADE per activar le ret.");
define("LANGELE30","Cambiar le senhal");
define("LANGMESS44","Messatge a un ".INTITULEELEVE." en ");
define("LANGMESS5","Messatge a un parent en : ");
define("LANGMESS45","Messatge cap a un email : ");
define("LANGMESS2","Messatge per ".INTITULEDIRECTION." : ");
define("LANGTRONBI9","dels ".INTITULEELEVE."s");
define("LANGTRONBI10","del personal");
define("LANGTRONBI11","Trombinoscòpi del personal");
define("LANGTITRE15","Mesa en plaça dels professors principals o dels institutors");
define("LANGPER7","afectat en classa "); //
define("LANGPROF40","Rensenhaments complementaris");
define("LANGPROFP38","Emplenar o consultar le Quasernet de Seguiment");
define("LANGEDIT2","Tel. Portable 1");
define("LANGEDIT3","Civilitat ");
define("LANGEDIT4","Nom Resp. 2");
define("LANGEDIT5","Petit nom Resp. 2");
define("LANGEDIT6","Luòc de naissença");
define("LANGEDIT7","Civilitat ");
define("LANGEDIT8","Nom Resp. 1");
define("LANGEDIT9","Tel. Portable 2");
define("LANGEDIT10"," Parent");
define("LANGEDIT11","E-mail ".ucfirst(TextNoAccentLicence2(INTITULEELEVE))."");
define("LANGEDIT12","Tel. ".INTITULEELEVE);
define("LANGEDIT13","E-mail Tutor 2");
define("LANGEDIT14","d'uèi");
define("LANGEDIT15","Dempuèi 1 jorn");
define("LANGEDIT16","Dempuèi 2 jorns");
define("LANGEDIT17","Dempuèi 3 jorns");
define("LANGEDIT18","Dempuèi 4 jorns");
define("LANGEDIT19","Retard(s) pas justificat(s)");
define("LANGEDIT20","Tel. Portable ");
define("LANGSMS1","Mandadís SMS pels retards dempuèi ");
define("LANGSMS2","Non indicat");
define("LANGSUPPLE","Lista dels suplents");
define("LANGSUPPLE1","En remplaçament de ");
define("LANGTITRE2","Actualitats de l'establiment");
define("LANGTITRE1","Eveniments");

define("LANGDISC58","Apondre una disciplina a un ".INTITULEELEVE);
define("LANGDISC59","Sasida en mòde U.S.A.");
define("LANGDISC60","Examèn ");

define("LANGBT8","Listar / Modificacion");
define("LANGBT9","Listar / Modificacion");
define("LANGBT10","Listar / Modificacion");
define("LANGDIRECTION","Administracion");

define("LANGTITRE36","Gestion dels membres ".INTITULEDIRECTION);
define("LANGTITRE37","Gestion dels membres Vida Escolara");
define("LANGTITRE38","Gestion Ensenhants");
define("LANGTITRE39","Gestion Suplents");
define("LANGTITRE40",INTITULEELEVE);
define("LANGTITRE41","resp."); // per l'abréviation de "responsable"
define("LANGTITRE42","tutor"); // dins le cadre familial
define("LANGTITRE43","Gestion d'un ".INTITULEELEVE);
define("LANGTITRE44","Importar una lista d'".INTITULEELEVE."s");
define("LANGTITRE45","Recèrca ".INTITULEELEVE);
define("LANGCHERCH1","En foncion del critèri de recèrca");
define("LANGCHERCH2","Fin de la recèrca");
define("LANGCHERCH3","Nombre d'elements trobats");
define("LANGPROF3bis","Visualizar les devers, interrogacions e contraròtles");
define("LANGTROMBI","Exportar las listas d'".INTITULEELEVE."s vers WellPhoto");
define("LANGPURG1","Supression de las informacions");
define("LANGPUR2","Supression de las informacions");
define("LANGPROFP39","Tablèu de las mejanas annuelles :");
define("LANGBLK1","Le vòstre compte es desactivat.<br /><br />Avètz temptat un accès sus una pagina pas autorizada.<br /><br />Per reactivar le vòstre compte, contactatz le vòstre establiment escolar.<br /><br />L'Equipa TRIADE.");
define("LANGCARNET4","accedir");
define("LANGFORUM10bis","Le vòstre petit nom ");
define("LANGTPROBL11","Nos encargam de vos respondre tant viste coma possible. \\n\\n  L'Equipa TRIADE ");
define("LANGTRAD1","Lista de las operacions efectuadas");
define("LANGPARAM39","Certificat enregistrat");
define("LANGPARAM40","Certificat pas enregistrat");
define("LANGPARAM41","Le fichièr deu èstre al format <b>rtf</b> e inferior a 2Mo");
define("LANGBASE42","Importacion del fichièr");
define("ACCEPTER","Accepter");
define("LANGCONDITION","Accèpti las Condicions");
define("LANGPARAM42","Lista dels retards o abséncias");
define("LANGCARNET5","Consultar le Quasernet de Seguiment");
define("LANGCARNET6","Emplenar le Quasernet de Seguiment");
define("LANGCARNET7","Emplenar");
define("LANGCARNET8","Quasernet de Seguiment");
define("LANGCARNET9","Crear un Quasernet de Seguiment");
define("LANGCARNET10","Modificar un Quasernet de Seguiment");
define("LANGCARNET11","Suprimir un Quasernet de Seguiment");
define("LANGCARNET12","Consultar un Quasernet de Seguiment");
define("LANGCARNET13","Exportar un Quasernet de Seguiment");
define("LANGCARNET14","Importar un Quasernet de Seguiment");
define("LANGCARNET15","Importar");
define("LANGCARNET16","Exportar");
define("LANGCARNET17","Menú Quasernet de Seguiment");
define("LANGCARNET18","Nom del Quasernet de Seguiment");
define("LANGCONTINUER","Contunhar --->");
define("LANGCARNET19","Creacion d'un Quasernet de Seguiment");
define("LANGCARNET20","Còdis d'apreciacion que pòdon èstre causits pels ensenhants");
define("LANGCARNET21","Letras");
define("LANGCARNET22","Chifras");
define("LANGCARNET23","Colors");
define("LANGCARNET24","Nòtas");
define("LANGCARNET25","(0 a 10 o 0 a 20)");
define("LANGCARNET26","Correspondéncia");
define("LANGCARNET27","aquesit");
define("LANGCARNET28","de&nbsp;confirmar");
define("LANGCARNET29","pas&nbsp;acquesit");
define("LANGCARNET30","en&nbsp;cors&nbsp;d'aquisicion");
define("LANGCARNET31","pas&nbsp;evalorat");
define("LANGCARNET32","Verd");
define("LANGCARNET33","Blau");
define("LANGCARNET34","Irange");
define("LANGCARNET35","Roge");
define("LANGCARNET36","periòde");
define("LANGCARNET37","periòdes");
define("LANGCARNET38","Gestion del Quasernet de Seguiment");
define("LANGCARNET39","Nombre(s) de periòde(s) qu'impausan la signatura dels parents, de l'ensenhant e de la Direccion ");
define("LANGCARNET40","Nombre(s) ");
define("LANGCARNET41","Seccions associadas a aqueste Quasernet de Seguiment");
define("LANGCARNET42","Seccions");
define("LANGCARNET43","Maximum 4 causidas possiblas (les 4 primièrs seràn conservats)");
define("LANGCARNET44","Quasernet de Seguiment creat. Podètz ara apondre las competéncias associadas a aqueste Quasernet.");
define("LANGCARNET45","Apondon d'un domeni de competéncias ");
define("LANGCARNET46","Intitulat del domeni de competéncias ");
define("LANGCARNET47","Aqueste intitulat correspond a una rubrica de competéncias ?  ");
define("LANGCARNET48","Intitulat");
define("LANGCARNET49","Apondon d'una competéncia ");
define("LANGCARNET50","Modificar las caracteristicas generalas del Quasernet ");
define("LANGCARNET51","Apondre un domeni de competéncias ");
define("LANGCARNET52","Modificar un domeni de competéncias ");
define("LANGCARNET53","Indicatz le Quasernet de Seguiment");
define("LANGCARNET54","Quasernet de Seguiment pas existant ");
define("LANGCARNET55","Consultacion d'un Quasernet de Seguiment");
define("LANGCARNET56","Un Quasernet de Seguiment");
define("LANGCARNET57","Recuperacion del Quasernet de Seguiment al format PDF");
define("LANGCARNET58","Exportacion d'un Quasernet de Seguiment");
define("LANGCARNET59","Per recuperar aqueste Quasernet de Seguiment");
define("LANGCARNET60","Modificacion d'un Quasernet de Seguiment");
define("LANGCARNET61","Supression d'un Quasernet de Seguiment");
define("LANGCARNET63","Importacion d'un Quasernet de Seguiment");
define("LANGCARNET64","Fichièr d'importar");
define("LANGCARNET65","Suprimir tot l'emplec del temps abans l'impòrt ?");
define("LANGCARNET66","Importacion anullada. <br><br>Aqueste nom de Quasernet existís ja ! <br />Suprimissètz aqueste Quasernet abans d'efectuar l'importacion.");
define("LANGCARNET62","ATENCION !!! Totas las nòtas assubjectidas al Quasernet de Seguiment seràn escafadas!");
define("LANGEDT2","Impòrt Emplec del temps Visual Timetabling");
define("LANGEDT3","Impòrt Visual Timetabling acabat");
define("LANGEDT4","Afichatge / Gestion de l'Emplec del Temps");
define("LANGEDT5","Importar Emplec del temps Visual Timetabling");
define("LANGEDT6","Exportar Triade vers Visual Timetabling");
define("LANGEDT7","Afichatge / Gestion de l'Emplec del Temps");
define("LANGEDT8","Administrar");
define("LANGEDT9","Mesa en plaça de l'Emplec del Temps");
define("LANGEDT10","Modul SQLite pas suportat. Validatz le vòstre servidor per la presa en carga del support SQLite.");
define("LANGGRP47","Rechercher les gropes");
define("LANGGRP48","Lista dels gropes d'un ".INTITULEELEVE);
define("LANGGRP49","Lista dels gropes");
define("LANGDISP21","Configuracion Motiu abs / rtds");
define("LANGDISP22","Enregistrament dels motius ");
define("LANGDISP23","Intitulat del motiu ");
define("LANGDISP24","Lista dels motius ");
define("LANGDISP25","Nombre d'".INTITULEELEVE."s mis a jorn");
define("LANGDISP26","Le fichièr deu èstre al format xls");
define("LANGCARNET63","Impòrt Quasernet de Seguiment acabat");
define("LANGCARNET64","Lista de las sanccions");
// News 2
define("LANGCARNET67","Apondon d'una sanccion disciplinària");
define("LANGCARNET68","Orari");
define("LANGVIES1","Nom de la persona restacadas al bulletin");
define("LANGVIES2","Coeficient de la nòta Vida Escolara sul bulletin");
define("LANGVIES3","Coeficient Ensenhant");
define("LANGVIES4","Coeficient Vida escolara");
define("LANGVIES5","Lista dels Ensenhants");
define("LANGVIES6","Informacions Escolaras Complementàrias");


define("LANGVIES7","Enregistrar las nòtas e comentaris");
define("LANGVIES8","Impression de las abséncias d'una classa");
define("LANGVIES9","Indicatz le mes");
define("LANGVIES10","Indicatz una classa ");
define("LANGPDF1","Un fichièr PDF per l'ensemble");
define("LANGPDF2","Un fichièr PDF per ".INTITULEELEVE);
define("LANGEDIT5bis","Petit nom Resp. 1");
define("LANGGRP50","Modificar le nom d'un grop");
define("LANGGRP51","Nom del grop");
define("LANGGRP52","Modul Modificacion");
define("LANGGRP53","Novèl nom de grop");
define("LANGGRP54","o relevat de nòtas");
define("LANGGRP55","examèn");
define("LANG1ER","1èra");
define("LANG2EME","2nda");
define("LANG3EME","3ena");
define("LANG4EME","4ena");
define("LANG5EME","5ena");
define("LANG6EME","6ena");
define("LANG7EME","7ena");
define("LANG8EME","8ena");
define("LANG9EME","9ena");
define("LANGGRP56","Notation sur");
define("LANGGRP57","Garder");
define("LANGGRP58","Atencion, las nòtas dels ".INTITULEELEVE."s seleccionats a la supression <br /> seràn suprimidas dins totas las classas qu'utilizan aqueste grop !!!");
define("LANGGRP59","Décocher le(s) ".INTITULEELEVE."(s) n'appartenant plus al grop");
define("LANGGRP60","Modificar la lista");
define("LANGPARAM3","<font class=T1>Compausatz le vòstre tèxte pel contengut del certificat d'escolaritat.  Per una presa en compte del nom, del petit nom e de l'adreça de l'".INTITULEELEVE." automaticament dins cada document, precisatz la cadena <b>NomEscolan</b>, <b>Petit nomEscolan</b>, <b>AdreçaEscolan</b>, <b>CòdiPostalEscolan</b> e <b>VilaEscolan</b> a l'emplaçament desirat. Amai, possibilitat d'indicar la classa amb le mot clau <b>ClassaEscolan</b> o <b>ClassaEscolanLong</b>, la data de naissença amb <b>DataNaissençaEscolan</b>, luòc de naissença via <b>LuòcDeNaissença</b>, la data del jorn via <b>DataDelJorn</b>, l'annada escolara via <b>AnneeScolaire</b>, nacionalitat via <b>Nationalite</b>.</font><br><br>");
define("LANGEDIT20bis","Supp");  // abréviation de Suprimir  sus 3 letras solament
define("LANGGRP61","Retorn a la mesa a jorn");
define("LANGRTDJUS","Justificat"); // per un retard
define("LANGABSJUS","Justificada"); // per una abs
define("LANGPARAM2","<font class=T1>Compausatz le vòstre tèxte pel contengut del messatge de retard a mandar aux parents. Podètz precisar las informacions seguentas : Nom de l'".INTITULEELEVE." : <b>NomEscolan</b> - Petit nom de l'".INTITULEELEVE." : <b>Petit nomEscolan</b> - Adreça : <b>AdreçaEscolan</b> - Còdi postal : <b>CòdiPostalEscolan</b> - Vila : <b>VilaEscolan</b> - Classa de l'".INTITULEELEVE." : <b>ClassaEscolan</b> - Data del retard : <b>RTDDATE</b> - Ora del retard : <b>RTDHEURE</b> - Durada : <b>RTDDUREE</b>  - Cumul abséncia : <b>CumulABS</b> </font><br><br>");
define("LANGPARAM1","<font class=T1>Compausatz le vòstre tèxte pel contengut del messatge de l'abséncia a mandar als parents. Podètz precisar las informacions seguentas : Nom de l'".INTITULEELEVE." : <b>NomEscolan</b> - Petit nom de l'".INTITULEELEVE." : <b>Petit nomEscolan</b> - Adreça : <b>AdreçaEscolan</b> - Còdi postal : <b>CòdiPostalEscolan</b> - Vila : <b>VilaEscolan</b> - Classa de l'".INTITULEELEVE." : <b>ClassaEscolan</b> - Data de començament d'abséncia :  <b>ABSDEBUT</b> - Data de fin d'abséncia : <b>ABSFIN</b> - Durada : <b>ABSDUREE</b> - Nom del responsable 1 : <b>NomResponsable1</b> - Adreça responsable 1 : <b>AdreçaResponsable1</b> - Vila responsable 1 : <b>VilaResponsable1</b> - Cumul abséncia : <b>CumulABS</b> - Data del jorn : <b>DATEDUJOUR</b> </font><br><br>");
define("LANGGRP62","estudi");
define("LANGGRP63","Corrièr");
define("LANGDELEGUE1","delegat");
define("LANGEDT10bis","Modul SimpleXML pas suportat. Validatz le vòstre servidor per la presa en carga de l'extension SimpleXML.");
define("LANGBULL45","Mandar un messatge a totis les ensenhants marcats per les prevenir d'emplenar lors bulletins.");
define("LANGBULL46","Nombre de bulletins emplenats dins la classa");
define("LANGMESS46","Visualizar dins");
define("LANGMESS47","Suprimir una retenguda o una sanccion");
define("LANGCOUR","Corrièr acabat");
define("LANGCOUR1","Lista dels retengudas pas efectuadas");
define("LANGCOUR2","Configuracion del corrièr de retengudas");
define("LANGPARAM43","<font class=T1>Compausatz le vòstre tèxte pel contengut del messatge de retenguda a mandar aux parents. Podètz precisar las informacions seguentas : Nom de l'".INTITULEELEVE." : <b>NomEscolan</b> - Petit nom de l'".INTITULEELEVE." : <b>Petit nomEscolan</b> - Adreça : <b>AdreçaEscolan</b> - Còdi postal : <b>CòdiPostalEscolan</b> - Vila : <b>VilaEscolan</b> - Classa de l'".INTITULEELEVE." : <b>ClassaEscolan</b> - Data de la retenguda : <b>DATERETENU</b> - Ora de la retenguda : <b>HEURERETENU</b> - Durada : <b>RETENUDUREE</b> - Motiu : <b>RETENUMOTIF</b> -  Categoria : <b>RETENUCATEGORY</b> - Atribuida per : <b>ATTRIBUEPAR</b> - Dever a far : <b>DEVOIRAFAIRE</b> - Los faits : <b>FAITS</b> - Civilitat tutor 1 : <b>CIVILITETUTEUR1</b> - Nom del responsable 1 : <b>NOMRESP1</b> Petit nom del responsable 1 : <b>PRENOMRESP1</b> - Data del jorn : <b>DATEDUJOUR</b> </font><br><br>");
define("RESA75","Informacions complementàrias");
define("LANGCOM","Enregistrar totis le les vòstres comentaris dins la vòstra bibliotèca.");
define("LANGCOM1","La valor max deu èstre mai granda que la valor min.");
define("LANGCOM2","Totis les camps devon èstre indicats corrèctament.");
define("LANGCOM3","Nombre d'".INTITULEELEVE."s : ");
define("LANGSTAGE91","Nom del responsable");
define("LANGSTAGE93","Foncion del resp.");
define("LANGSTAGE94","de l'entrepresa");
define("LANGSTAGE95","Entrepresa");
define("LANGSTAGE96","Nombre d'elements trobats");
define("LANGSTAGE97","Indicar una valor numerica, svp");
define("LANGSTAGE98","Indicatz la data del començament d'estagi, svp");
define("LANGSTAGE99","Indicatz la data de fin d'estagi, svp");
define("LANGPATIENTE","Pacientatz");
define("LANGSMS3","Numèro de telefòn portable");
define("LANGSMS4","150 caractèrs maximum");
define("LANGSMS5","Messatge");
define("LANGSMS6","Le mandadís del messatge SMS es conservat e accessible per ".INTITULEDIRECTION);
define("LANGSMS7","Mandadís messatge SMS");
define("LANGSMS8","Mandar un messatge SMS");
define("LANGSMS9","Lista dels numèros de telefòns dels parents <br> de ");
define("LANGSMS10","Mandar un sms a tota una classa");
define("LANGSMS11","Mandar un sms a un parent d'".INTITULEELEVE." via son nom");
define("LANGSMS12","Mandar un sms a una persona via son nom");
define("LANGSMS13","Mandar un sms a una persona via son numèro");
define("LANGSMS14","Numèro");
define("LANGbasededoni54_5","valor acceptada : <b>7</b> o P <br>");
define("LANGbasededoni54_6","valor acceptada : <b>8</b> o Sr <br>");
define("LANGGRP27bis","Apondre un ".INTITULEELEVE." dins mantun grop");
define("LANGGRP28bis","Apondon ".INTITULEELEVE." dins grop");
define("LANGGRP29bis","Sasida&nbsp;/&nbsp;Modif");
define("LANGNOTEUSA6","Correspondéncia de las nòtas per la notacion en mòde USA");
define("LANGNOTE1","Intitulat de l'examèn");
define("LANGPARAM44","Recebre un messatge quand recebètz una informacion de tipe");
define("LANGMESS17bis","Config.");
define("LANGNNOTE2","Triar per classa");
define("LANGNNOTE3","Triar per nom");
define("LANGNNOTE4","Indicar le títol del document");
define("LANGBULL47","Bulletin sens sosmatèrias");
define("LANGBULL48","Bulletin amb sosmatèrias");
define("LANGBULL49","Bulletin examèn blanc");
define("LANGMESS48","Bóstia de supression");
define("LANGMESS49","Cap de ".INTITULEELEVE." a pas d'entrepresa afectada.");
define("LANGMESS50","Plan de la classa");
define("LANGMESS51","Indicar las matèrias facultativas");
define("LANGMESS52","(Nòtas comptabilizadas dins la mejana generala, se son superioras a 10/20)");
define("LANGMESS53","Setmana precedenta");
define("LANGMESS54","Setmana seguenta");
define("LANGMESS55","Emplec del temps de la classa ");
define("LANGMESS56","Pas cap de ".INTITULEELEVE."");
define("LANGMESS57","Identificant");
define("LANGMESS58","Aqueste compte possedís pas cap de numèro.");
define("LANGMESS59","Modificar tanbens las abs/rtd justificats");
define("LANGMESS60","A");
define("LANGMESS60bis","bsent");
define("LANGMESS61","dels ensenhants");
define("LANGMESS62","Parent de ");
define("LANGMESS63","uèi");  // metre una ' 
define("LANGBT27bis","Enregistrar abs/rtd"); //
define("LANGDEPART3bis","Accès interromput ! ");
define("LANGDEPART4bis","L'accès a le vòstre TRIADE es actualament interromput, mercé de contactar le vòstre establiment escolar per de mai amplas informacions.");
define("LANGAIDE","Ajuda en linha");
define("LANGAIDE1","Indicar las correspondéncias entre las vòstras matèrias enregistradas dins TRIADE e las matèrias ensenhadas pel brevet dels collègis. Per aquò, efectuar un drag&drop (lisar&relacher) entre las matèrias d'esquèrra a dreita.");
define("LANGAIDE2","Compausar le vòstre tèxte pel contengut de la convencion d'estagi. Per una presa en compte d'elements tals coma le nom, petit nom, adreça, etc..., precisatz la cadena seguenta en foncion de le les vòstres besonhs :");
define("LANGBREVET1","Accedir");
define("LANGCONFIG4","Èsser avertit d'un messatge quand");
define("LANGCONFIG5","Nbr d'abséncias pas justificadas d'un ".INTITULEELEVE." a depassat ");
define("LANGCONFIG6","Nbr de retards pas justificats d'un ".INTITULEELEVE." a depassat ");
define("LANGCONFIG7","fois");
define("LANGCONFIG8","Lista dels utilizaires avertits");

define("LANGMESS64","Personas qu'an recebut aqueste messatge");
define("LANGMESS65","Lista dels règlaments interiors");
define("LANGMESS66","Le Director");
define("LANGMESS67","Ai pres coneissença dels diferents documents çaisús");
define("LANGMESS68","Accèpti le o lo(s) règlament(s) interior(s)");
define("LANGMESS69","Accèpti las condicions generalas d'ensenhament");
define("LANGMESS70","Règlament accessible pels ensenhants");
define("LANGMESS71","Consultar Ficha d'estat dels règlaments");
define("LANGMESS72","Imprimir Ficha d'estat dels règlaments");
define("LANGMESS73","Lista dels impagats o pagament(s) incomplet(s)");
define("LANGMESS74","Ficha d'estat dels règlaments");
define("LANGacce_dep2ter","<br><b>ATENCION !  Verificatz bien le vòstre mòde d'accès, causissètz le vòstre compte correspondant.</b>");
//NEW NON CORRIGE

define("LANGMESS75","Retorn menú principal");
define("LANGMESS76","Correspondéncia");
define("LANGMESS77","(dever, contraròtle, examèn)");
define("LANGMESS78","Triar per ");
define("LANGMESS79","Nòtas visiblas pels escolans le ");
define("LANGMESS80","vida escolara");
define("LANGMESS81","Connexion en cors");
define("LANGMESS82","Mejana");
define("LANGMESS83","Mejana de classa");
define("LANGMESS84","Max");
define("LANGMESS85","Min");
define("LANGMESS86","Cap de data trimestrala pas afectada");
define("LANGMESS86bis","per");
define("LANGMESS86ter","aquesta annada escolara");
define("LANGMESS87","Nòta dels devers de");




define("LANGMESS88","Quasèrn de tèxte enregistrat  -- Servici Triade");
define("LANGMESS89","Quasèrn de tèxte en ");
define("LANGMESS90","Pensar a enregistrar le vòstre contengut abans de cambiar d'onglet.");
define("LANGMESS91","Consultacion de la setmana");
define("LANGMESS92","Contengut del cors");
define("LANGMESS93","Fichièr junt");
define("LANGMESS94","Pèça Junta");
define("LANGMESS95","Objectiu del cors");
define("LANGMESS96","Dever a far pel ");
define("LANGMESS97","pas indicat");
define("LANGMESS98","Dever a far");
define("LANGMESS99","Blòt-Nòtas");
define("LANGMESS100","Consultacion completa");
define("LANGMESS101","Validacion");
define("LANGMESS102","Consultacion");
define("LANGMESS103","Temps estimat per aqueste trabalh ");
define("LANGMESS104","Temps de trabalh estimat a ");
define("LANGMESS105","Fichièr ");
define("LANGMESS106","Modificacion ");
define("LANGMESS107","Suprimir aquesta ficha ");
define("LANGMESS108","Temps de trabalh total estimat ");
define("LANGMESS109","del"); // notion de data del xxxx al xxxx
define("LANGMESS110","al"); // notion de data del xxxx al xxxx
define("LANGMESS111","Format PDF"); 
define("LANGBT288","Consultar / Modificar"); //
define("LANGSITU1","Maridat(-ada)"); //
define("LANGSITU2","Divorciat(-ada)"); //
define("LANGSITU3","Veuse"); //
define("LANGSITU4","Veusa"); //
define("LANGSITU5","Concubin"); //
define("LANGSITU6","PACS"); //
define("LANGSITU7","Celibatari(-ària)"); //
define("LANGFIN002","Escasencièr");//
define("LANGFIN003","Escasencièr");//
define("LANGFIN004","Pas cap de data de configurada");//
define("LANGCONFIG","Configurar");//

define("LANGMESS112","Comentari bulletin trimèstre/semèstre");
define("LANGMESS113","Causida del comentari");
define("LANGMESS114","Comentari brevet dels collègis");
define("LANGMESS115","Visualizacion del bulletin de classa");
define("LANGMESS116","Accedir");
define("LANGMESS117","Seria");
define("LANGMESS118","Passar en mòde espandit");
define("LANGMESS119","Apreciacions, Conselhs per progressar");
define("LANGMESS120","Punts d'appui. Progrès. Esfòrces");
define("LANGMESS121","Escarts per rapòrt als objectius esperats");
define("LANGMESS122","Conselhs per progressar");
define("LANGMESS123","Mejana de la classa");
define("LANGMESS124","Comentari precedent");
define("LANGMESS125","Apondon dins lista"); // verif. pas de quote (') 
define("LANGMESS126","Enregistrar le comentari"); // verif. pas de quote (') 
define("LANGMESS127","Tornar e clicar sus"); // verif. pas de quote (') 
define("LANGMESS128","Enregistrament");  // verif. pas de quote (') 
define("LANGMESS129","Consultar");
define("LANGMESS130","Mej. Precedenta");
define("LANGMESS131","Enregistrar les comentaris");
define("LANGMESS132","Pacientatz S.V.P.");
define("LANGMESS133","Comentari void");
define("LANGMESS134","comentari pas enregistrat");
define("LANGMESS135","Apreciacion per le bulletin trimestral classa");
define("LANGMESS136","clicatz aicí");
define("LANGMESS137","Informacion Escolara Complementària");
define("LANGMESS138","Sasir autres comentaris pels bulletins");

//-----------------Traduction Sam le 06/06/2014
//-----------------messagerie_brouillon.php
define("LANGMESS139","Messagerie brouillon");
define("LANGMESS140","Préparer un brouillon ");
define("LANGMESS141","Accès");
define("LANGMESS142","Valider un brouillon");
define("LANGMESS143","Les messages brouillons sont visibles par tous les membres de la direction");

//------------------param.php
define("LANGMESS144","Signature du directeur");
define("LANGMESS145","Année scolaire");
define("LANGMESS156","Pays");
define("LANGMESS159","Choix du site");
define("LANGMESS160","Nouveau site");
define("LANGMESS177","Département ");
//------------------definir_trimestre.php
define("LANGMESS146","Enregistrement au format semestriel.");
define("LANGMESS147","Toutes les classes");
define("LANGMESS148","Liste des périodes trimestrielles ou semestrielles ");
define("LANGMESS149","Modifier");
define("LANGMESS150","Supprimer");
define("LANGMESS157","Trimestre");
define("LANGMESS158","Classe");
//-----------------probleme_acces_2.php
define("LANGMESS151","Identifiez votre compte");
define("LANGMESS152","Veuillez dabord identifier votre compte pour réinitialiser votre mot de passe.");
define("LANGMESS153","Demande de mot de passe");
//-----------------geston_groupe.php
define("LANGMESS154","Création de groupe");
define("LANGMESS155","Liste des groupes des enseignants");
//-----------------gestcompte.php
define("LANGMESS161","Gestion de votre compte");
//-----------------messagerie_reception.php
define("LANGMESS162","Gestion de votre compte");
//------------------gestion_groupe.php
define("LANGBT53","Entrée"); // traduit par sam le 09/06/2014
define("LANGMESS163","Vérification des groupes");
//-------------------messagerie_suppression.php
define("LANGMESS164","Boite de suppression");
define("LANGMESS165","Archiver dans");
//-------------------messagerie_reception.php
define("LANGMESS166","Boite de reception");
//-------------------parametrage.php
define("LANGMESS167","Paramétrage de votre compte");
define("LANGMESS168","Actualités");
define("LANGMESS169","Réservation Salle / Equipement");
define("LANGMESS170","Messagerie Triade");
define("LANGMESS171","(Indiquer votre  email)");
define("LANGMESS172","(Numéro de portable)");
//-------------------messagerie_envoi.php
define("LANGMESS173","Message à un groupe ");
define("LANGMESS174","Message aux délégués :");
define("LANGMESS175","Message à un membre du personnel : ");
define("LANGMESS176","Message à un tuteur de stage : ");
//-------------------creat_admin.php
define("LANGMESS178","Civ.");
define("LANGMESS179","Indice&nbsp;salaire");
//-------------------creat_tuteur.php
define("LANGMESS180","Création d'un compte tuteur de stage");
define("LANGMESS181","Liste / Modification d'un tuteur de stage");
define("LANGMESS182","Gestion des membres Tuteur de stage");
define("LANGMESS183","Entreprise liée");
define("LANGMESS184","En qualité de ");
//--------------------creat_personnel.php
define("LANGMESS185","Gestion des membres du Personnel");
define("LANGMESS186","Création d'un compte personnel"); // "Cr&eacute;ation d'un compte personnel"
//--------------------creat_eleve.php
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
//--------------------creat_class.php
define("LANGMESS206","Intitulé de la classe");
define("LANGMESS207","Ecole");
//--------------------creat_matiere.php
define("LANGMESS208","Format court");
define("LANGMESS209","Format long");
define("LANGMESS210","Code matière");
//--------------------reglement.php
define("LANGMESS211","Réglement intérieur");
define("LANGMESS212","Ajouter un règlement");
define("LANGMESS213","lister le/les règlements");
define("LANGMESS214","Supprimer un règlement");
//--------------------sms.php
define("LANGMESS215","Gestion des SMS");
define("LANGMESS216","Membre");
define("LANGMESS217","Direction");
define("LANGMESS218","Enseignant");
define("LANGMESS219","Vie Scolaire");
define("LANGMESS220","Personnel");
//--------------------Codebar0.php
define("LANGMESS221","Code barre :");
//--------------------vatel_gestion_ue.php
define("LANGMESS222","Gestion des Unités d'enseignements");
define("LANGMESS223","Création d'une unité d'enseignement");
define("LANGMESS224","Lister/Modifier");
//--------------------base_de_donne_importation.php
define("LANGMESS225","Fichier Excel");
define("LANGMESS226","Fichier XML");
define("LANGMESS227","Code barre");
//--------------------edt.php
define("LANGMESS228","Suppression d'une période ");
define("LANGMESS229","Ajustement des horaires ");
define("LANGMESS230","Période visible sur l'EDT");
define("LANGMESS231","Importer image ou pdf : ");
define("LANGMESS232","(format  de l'image : jpg et moins de 2Mo )");
define("LANGMESS233","EDT de la classe : ");
//--------------------export.php
define("LANGMESS234","Exportation des données");
define("LANGMESS235","Informations à exporter");
define("LANGMESS236","Personnel");
define("LANGMESS237","Choix de l'extraction : ");
//--------------------export.php
define("LANGMESS238","Nom de l'enseignant ");
define("LANGMESS239","Exportation au format PDF : ");
define("LANGMESS240","Exporter");
//--------------------commaudio.php
define("LANGMESS241","Sujet : ");
define("LANGMESS242","Fichier audio : ");
//--------------------consult_classe.php
define("LANGMESS243","Impression ");
define("LANGMESS365","&nbsp;Demi&nbsp;Pension&nbsp;");
define("LANGMESS366","&nbsp;Interne&nbsp;");
define("LANGMESS367","&nbsp;Externe&nbsp;");
define("LANGMESS368","&nbsp;Inconnu&nbsp;");
//--------------------resr_admin.php
define("LANGMESS244","Réserver via E.D.T.");
//--------------------carnetnote.php
//------------modif nom de l'enseignant---LANGMESS238
//--------------------publipostage.php
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
//--------------------ficheeleve3.php
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

//--------------------elevesansclasse.php
define("LANGMESS256","Save");
//--------------------consult_classe.php
define("LANGMESS257","All classes.");
//--------------------ficheeleve.php
define("LANGMESS258","Search");
//--------------------newsactualite.php
define("LANGMESS299","    Titre : ");
define("LANGMESS300","Votre TRIADE n'est pas configuré en accès Internet, veuillez consulter votre compte administrateur Triade pour valider l'option de la connexion Internet.");
define("LANGMESS365","Actualités  de la 1er page");
//--------------------actualiteetablissement.php
//--------------------newsdefil.php
//--------------------commaudio.php // Bouton Parcourir
//--------------------commvideo.php
define("LANGMESS301","Lien de la video : ");
define("LANGMESS302","ou Lien Youtube : ");
//--------------------emmargement.php
define("LANGMESS303","Gestion des émargements ");
define("LANGMESS304","Au niveau de la classe");
define("LANGMESS305","Emargement vierge");
define("LANGMESS306","Emargement vierge d'examen");
define("LANGMESS307","Au niveau du groupe");
define("LANGMESS314","Emargement du jour ");
define("LANGMESS315","Emargement&nbsp;du&nbsp;");
define("LANGMESS316","Pour la classe : ");
define("LANGMESS317","Enseignant : ");
define("LANGMESS318","Tous les enseignants : ");
define("LANGMESS319","Hauteur des cellules des élèves");
//--------------------trombinoscope0.php
define("LANGMESS322","Imprimer au format PDF des ".INTITULEELEVE);
define("LANGMESS323","Importer les photos au format ZIP");
//--------------------chgmentclas.php
define("LANGMESS324",": notes, absences, retards, dispences, sanctions, retenues, Brevets, Commentaires bulletin de l'élève, droits de scolarité, plan de classe, Brevets, Affectation stage");
//------LANGASS10-- Variable pour suppression
//--------------------certificat.php
define("LANGMESS325","Paramétrage  manuel : ");
define("LANGMESS326","Paramétrage  import : ");
//define("LANGMESS331","Publipostage");
//--------------------visa_direction.php
define("LANGMESS332","Type du bulletin : ");
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

//----------------creat_groupe.php
define("LANGMESS353","Fichier excel");
define("LANGMESS354","Contenu du fichier excel");
//----------------visa_direction2.php
define("LANGMESS355","Commentaire des enseignants");
define("LANGMESS356","Visa direction");
//----------------imprimer_tableaupp.php
define("LANGMESS357","Impression tableau de notes trimestriel ou semestriel");
define("LANGMESS358","Afficher le classement ");
define("LANGMESS359","Afficher les colonnes vides ");
define("LANGMESS360","Regroupement par module ");
define("LANGMESS361","Afficher les matières ");
define("LANGMESS362","Tableau des différentes moyennes au format excel");
define("LANGMESS374","Jusqu'au :");
define("LANGMESS375","Fichier Excel");
//------------------affectation_creation_key.php
//------------------affectation_visu2.php
define("LANGMESS363","Visu");
define("LANGMESS364","Unité Ens.");
//------------------entretien.php
define("LANGMESS369","Journal d'entretiens individuels");
define("LANGMESS370","Journal d'entretiens groupés ");
define("LANGMESS371","Tableau récapitulatif");
define("LANGMESS372","&nbsp;Enseignants&nbsp;");
define("LANGMESS373","&nbsp;Nombre&nbsp;d'heures&nbsp;");
//------------------base_de_donne_key.php
define("LANGMESS376","Pour modifier / changer votre code d'accès, merci de consulter votre compte ");
define("LANGMESS377","administrateur Triade");
define("LANGMESS378","puis le module \"code d'accès\"");
//------------------chgmentClas0.php
// année = Year
define("LANGMESS379","pas d'année");
define("LANGMESS380","Choix de la classe");
//------------------chgmentClas00.php
// année et pas d'année 
define("LANGMESS381","Choix des classes :");
define("LANGMESS383","Changement de classe pour les élèves en ");
define("LANGMESS384","Passage pour l'année scolaire");
define("LANGMESS385","Sans classe");
//------------------bro3uillon_reception.php
define("LANGMESS382","Liste des messages brouillons");
//------------------imprimer_trimestre.php
define("LANGMESS386","Bulletin&nbsp;personnalisé");
define("LANGMESS387","Bulletin définit pour les enseignants (et parents  prochainement)");
define("LANGMESS388","Visible pour la classe");
define("LANGMESS389","Autoriser l'accès aux bulletins pour les enseignants");




// --- NEW ERIC --- // 
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
define("LANGVATEL20","Acc&egrave;s Direction");

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


define("LANGVATEL39","Accueil");
define("LANGVATEL40","Choix de l'enseignant");
define("LANGVATEL41","pour l'enseignant");
define("LANGVATEL42","Enseignant affecté à ce devoir");
define("LANGVATEL43","Absences ou retards en classe de");
define("LANGVATEL44","Autres absences");
define("LANGVATEL45","Autres absences pour la même classe");
define("LANGVATEL46","Signalé le ");
define("LANGVATEL47","Gestion Absences / Retards");
define("LANGVATEL48","Avertir par messagerie ");
define("LANGVATEL49","Mise à jour des tables");
define("LANGVATEL50","Impossible de supprimer cette classe");
define("LANGVATEL51","Classe non supprimable");
define("LANGVATEL52","Classe affectée");
define("LANGVATEL53","Supprimer cette classe");
define("LANGVATEL54","Supprimer cette matière");
define("LANGVATEL55","Impossible de supprimer cette matière");
define("LANGVATEL56","Matière affectée à une classe");
define("LANGVATEL57","Si pas de prénom, indiquer 'inconnu' ");
define("LANGVATEL58","Création d'un compte administratif");

define("LANGVATEL59","Liste des Tuteurs de stage");
define("LANGVATEL60","Liste des enseignants");
define("LANGVATEL61","Liste du personnel administratif");
define("LANGVATEL62","Liste des membres de la vie scolaire");
define("LANGVATEL63","Règlement intérieur");
define("LANGVATEL64","Classes");
define("LANGVATEL65","Personnel administratif");
define("LANGVATEL66","Tuteur de stage");
define("LANGVATEL67","Règlement interieur non enregistré");
define("LANGVATEL68","Le fichier doit être au format pdf et inférieur à 2Mo");
define("LANGVATEL69","Menu");
define("LANGVATEL70","Accès au PDF de la classe");
define("LANGVATEL71","Accès au PDF du régime");
define("LANGVATEL72","Imprimer au format PDF");
define("LANGVATEL73","Modifier la photo de ");
define("LANGVATEL74","Groupes");
define("LANGVATEL75","Création d'un groupe");
define("LANGVATEL76","Voir cette liste");
define("LANGVATEL77","aucun étudiant");
define("LANGVATEL78","Modifier ce groupe");
define("LANGVATEL79","Gestion des groupes");
define("LANGVATEL80","Groupe NON supprimé");
define("LANGVATEL81","Groupe supprimé");
define("LANGVATEL82","Le groupe est actuellement affecté.\\n\\n Impossible de le supprimer.\\n\\n Modifier l\\'affectation avant de supprimer ce groupe.");
define("LANGVATEL83","Groupe déjà créé.");


define("LANGVATEL84","Paramétrage Bulletin");
define("LANGVATEL85","Paramétrage Ecole");
define("LANGVATEL86","Mise en place des affectations");
define("LANGVATEL87","Modification des affectations");
define("LANGVATEL88","Suppression des affectations");
define("LANGVATEL89","Unité d'enseignement");
define("LANGVATEL90","Paramétrage des absences");
define("LANGVATEL91","Paramétrage des certificats de scolarité");
define("LANGVATEL92","Paramétrage du supplément");
define("LANGVATEL93","Indiquer le jour et le mois du début de votre année scolaire : ");
define("LANGVATEL94","Indiquer le jour et le mois de la fin de votre année scolaire : ");
define("LANGVATEL95","Erreur de saisie sur vos jours ou mois indiqués");
define("LANGVATEL96","Indiquer l'ann&eacute;e scolaire");
define("LANGVATEL97","IMPORTANT, LA CREATION D'AFFECTATION SUPPRIME TOUTES LES INFORMATIONS DE NOTATION DE LA NOUVELLE CLASSE CONCERNEE !!");
define("LANGVATEL98","Copier Affectation");
define("LANGVATEL99","ERREUR DE COPIE");
define("LANGVATEL100","de l'année scolaire");
define("LANGVATEL101","IMPORTANT, LA COPIE D'AFFECTATION SUPPRIME TOUTES LES INFORMATIONS DE NOTATION DE LA NOUVELLE CLASSE CONCERNEE !!");
define("LANGVATEL102","Copier l'affectation de la classe ");
define("LANGVATEL103","Etude de cas");
define("LANGVATEL104","Supprimer les notes scolaires de cette classe.");
define("LANGVATEL105","* Visu. : Visualiser au sein du bulletin / ** Nombre d'heure annuelle / *** Visu. : Visualiser au sein du bulletin AFTEC BTS BLANC");
define("LANGVATEL106","Indiquer un enseignant");
define("LANGVATEL107","Indiquer le coef de la matière");
define("LANGVATEL108","Indiquer une valeur Numérique");
define("LANGVATEL109","Déplacer la ligne en effectuant un drag&drop");
define("LANGVATEL110","cliquer/deplacer");
define("LANGVATEL111","sur le N° correspondant");
define("LANGVATEL112","Copier unité enseignement");
define("LANGVATEL113","Liste des unités d'enseignements");
define("LANGVATEL114","ATTENTION !! METTRE A JOUR LES AFFECTATIONS DE LA CLASSE ");
define("LANGVATEL115"," SUR LES DONNEES UNITE D'ENSEIGNEMENT ");
define("LANGVATEL116"," au sein du bulletin de zéro à n ");
define("LANGVATEL117","Valider la suppression");
define("LANGVATEL118","Etes vous sur de vouloir supprimer l'unité d'enseignement suivante ?");
define("LANGVATEL119","Suppresion effectuée");
define("LANGVATEL120","Config. créneaux horaires");
define("LANGVATEL121","Config. des motifs");
define("LANGVATEL122","Nom du créneau");
define("LANGVATEL123","Heure de départ");
define("LANGVATEL124","Heure de fin");
define("LANGVATEL125","Intitulé du créneau");
define("LANGVATEL126","Enregistrer les créneaux horaires");
define("LANGVATEL127","Créneaux par défaut");
define("LANGVATEL128","Certificat numéro");
define("LANGVATEL129","Paramétrage certificats");
define("LANGVATEL130","Importer un certificat");
define("LANGVATEL131","Erreur d'enregistrement");
define("LANGVATEL132","Certificat en cours");
define("LANGVATEL133","Configuration des mots clefs");

define("LANGVATEL134","Erreur d'enregistrement");
define("LANGVATEL135","Erreur : Fichier non reconnu ");
define("LANGVATEL136","Erreur : Fichier suppérieur à 8 MO");
define("LANGVATEL137","Fichier NON enregistré");
define("LANGVATEL138","Editions / Listes");
define("LANGVATEL139","Editions par classe");
define("LANGVATEL140","Liste des étudiants");
define("LANGVATEL150","Tableau de bord de toutes les classes.");
define("LANGVATEL151"," Elève(s) au total. Année Scolaire : ");
define("LANGVATEL152"," Elève(s) au total ");
define("LANGVATEL153","Liste d'émargements");
define("LANGVATEL154","Aucun cours définis sur l'emploi du temps.");
define("LANGVATEL155","Horaire début");
define("LANGVATEL156","Horaire fin");
define("LANGVATEL157","Intitulé du cours");
define("LANGVATEL158","Liste des enseignants");
define("LANGVATEL159","Liste des matières");
define("LANGVATEL160","Editions des certificats de scolarité");
define("LANGVATEL161","Documents de certificats de scolarité");
define("LANGVATEL162","Récupération des certificats au format ZIP");
define("LANGVATEL163","Liste des entretiens");
define("LANGVATEL164","Edition étiquettes Etudiants");
define("LANGVATEL165","Edition étiquettes Parents");
define("LANGVATEL166","Récupération du document Publipostage");
define("LANGVATEL167","Import / Export");
define("LANGVATEL168","Importer des étudiants");
define("LANGVATEL169","Importer des enseignants");
define("LANGVATEL170","Importer du personnel direction");
define("LANGVATEL171","Importer des entreprises");
define("LANGVATEL172","Exporter des étudiants");
define("LANGVATEL173","Exporter des enseignants");
define("LANGVATEL174","Exporter du personnel direction");

define("LANGVATEL175","Adresse élève");
define("LANGVATEL176","Commune élève");
define("LANGVATEL177","CCP élève");
define("LANGVATEL178","Tél. fixe élève");
define("LANGVATEL179","Boursier");
define("LANGVATEL180","Email Universitaire");
define("LANGVATEL181","Sexe élève");
define("LANGVATEL182","Mot de passe tuteur 2");
define("LANGVATEL183","Régime possible");
define("LANGVATEL184","Civilité possible");
define("LANGVATEL185","Le fichier à transmettre DOIT contenir 47 champs");
define("LANGVATEL186","Exemple fichier xls");
define("LANGVATEL187","Prendre la première ligne du fichier ");
define("LANGVATEL188","Effectuer une mise à jour ");
define("LANGVATEL189","Prendre en compte les champs vides du fichier");
define("LANGVATEL190","Affecter un nouveau mot de passe pour les élèves déjà inscrits");
define("LANGVATEL191","Pas d'archivage possible");
define("LANGVATEL192","Attention la suppression des l'élèves, supprimera toutes les archives !!");
define("LANGVATEL193","Import pour l'année scolaire suivante : ");
define("LANGVATEL194","ERREUR CLASSE NON CREEE -- Service Triade");
define("LANGVATEL195","Le fichier à transmettre DOIT contenir 9 champs");
define("LANGVATEL196","ERREUR sur le mot de passe de la personne");
define("LANGVATEL197","Ajouter d'autres colonnes");
define("LANGVATEL198","nbr de colonne(s) supplémentaire(s)");
define("LANGVATEL199","Indiquer les données à exporter");
define("LANGVATEL200","Sauvegarder la structure");
define("LANGVATEL201","Si vous souhaitez sauvegarder la structure de l'exporation, récupérez d'abord votre fichier excel, puis cliquez sur le bouton \"Sauvegarder la structure\"");
define("LANGVATEL202","Nom de la structure");
define("LANGVATEL203","Récupération de l'exportation");
define("LANGVATEL204","Indiquer l'ordre des colonnes dans votre fichier excel");
define("LANGVATEL205","Bulletin scolaire");

define("LANGVATEL206","Bulletin / Supplément au diplôme");
define("LANGVATEL207","Appréciations de la direction");
define("LANGVATEL208","Appréciations de la classe");
define("LANGVATEL209","Editions des notes");
define("LANGVATEL210","Edition des bulletins scolaires");
define("LANGVATEL211","Edition supplément Bachelor / Master");
define("LANGVATEL212","Commentaires enregistrés");
define("LANGVATEL213","Vérifier vos affectations pour cette classe");
define("LANGVATEL214","Gestion des dates de stage");
define("LANGVATEL215","Gestion des entreprises");
define("LANGVATEL216","Affectation des étudiants aux stages");
define("LANGVATEL217","Liste des étudiants en entreprise");
define("LANGVATEL218","Edition des conventions");
define("LANGVATEL219","Ajouter une période");
define("LANGVATEL220","Liste des périodes");
define("LANGVATEL221","La date de fin de stage ne peut être avant la date de début");
define("LANGVATEL222","Modifier une période");
define("LANGVATEL223","Supprimer une période");
define("LANGVATEL224","Suppression de toutes les dates non affectées à un étudiant");
define("LANGVATEL225","Lister");
define("LANGVATEL226","Gestion Stage");
define("LANGVATEL227","Imprimer la liste des entreprises");
define("LANGVATEL228","Nbre d'élèves ayant effectué un stage");
define("LANGVATEL229","Plan");
define("LANGVATEL230","Historique des élèves");
define("LANGVATEL231","Récupération du fichier PDF");
define("LANGVATEL232","Adresse / CCP / Ville");
define("LANGVATEL233","Listing des entreprises en date du ");
define("LANGVATEL234","Affectation de plusieurs étudiants à un stage");
define("LANGVATEL235","Affectation d'un étudiant à un stage");
define("LANGVATEL236","Début");
define("LANGVATEL237","Fin");
define("LANGVATEL238","Pour la période : Semestre / Trimestre");
define("LANGVATEL239","Période désirée");
define("LANGVATEL240","Indiquer le numéro de stage ou du stage personnalisé");
define("LANGVATEL241","Imprimer la liste complète");

define("LANGVATEL242","Edition des affectations");
define("LANGVATEL243","Autre classe");
define("LANGVATEL244","Mise en place de l'EDT");


define("LANGVATEL245","Veuillez choisir le type de compte souhait&eacute;");
define("LANGVATEL246","Mise &agrave; jour des tables");
define("LANGVATEL247","Gestion Absences / Retards");
define("LANGVATEL248","Ajouter une absence ou un retard");
define("LANGVATEL249","Pour le personnel");
define("LANGVATEL250","Pour la vie scolaire");
define("LANGVATEL251","Pour tuteurs de stage");
define("LANGVATEL252","Pour la direction");
define("LANGVATEL253","la ou les classe(s)");
define("LANGVATEL254","Param&eacute;trage");
define("LANGVATEL255","Mise en place de l'EDT");

define("LANGVATEL256","Indiquer la liste des parents d'étudiants qui recevront un email");
define("LANGVATEL257","Aucun numéro");
define("LANGVATEL258","Confirmer envoi SMS");
define("LANGVATEL259","Port. Elève ");
define("LANGVATEL260","Portable ");
define("LANGVATEL261","Tel Prof. Mère ");
define("LANGVATEL262","Tel Prof. Père ");
define("LANGVATEL263","Vidéo Projecteur ");
define("LANGVATEL264","Détail ABS/Rtd ");
define("LANGVATEL265","Créneaux ");
define("LANGVATEL266","non précisé ");
define("LANGVATEL267","Lister / Modifier des enseignants ");
define("LANGVATEL268","Supprimer un compte ");
define("LANGVATEL269","Abs/Rtd Etudiant ");
define("LANGVATEL270","Absences et Retards d'un étudiant");
define("LANGVATEL271","Création impossible, année scolaire non indiquée.");
define("LANGVATEL272","Nouvelle unité d'enseignement créée.");
define("LANGVATEL273","Trombinoscope");


?>
