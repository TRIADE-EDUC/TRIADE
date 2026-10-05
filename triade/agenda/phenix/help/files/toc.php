<?php
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  *                                                             MOD by dJuL  *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/


// ----------------------------------------------------------------------------
//Constante de gestion des droits
// ----------------------------------------------------------------------------
// profil
  define("_DROIT_PROFIL_RIEN",0);                 // Aucun Acces
  define("_DROIT_PROFIL_PARAM_BASE",10);          // Acces parametres de base
  define("_DROIT_PROFIL_PARAM_PARTAGE",20);       // Acces parametres de base et partages
  define("_DROIT_PROFIL_AUTRE_PARAM_BASE",30);    // Acces autres agendas parametres de base
  define("_DROIT_PROFIL_AUTRE_PARAM_PARTAGE",40); // Acces autres agendas parametres de base et partages
  define("_DROIT_PROFIL_COMPLET",50);             // Acces complet
// agenda
  define("_DROIT_AGENDA_SEUL",0);                 // Acces a son propre agenda
  define("_DROIT_AGENDA_PARTAGE",10);             // Acces standard
  define("_DROIT_AGENDA_TOUS",20);                // Acces a tous
// note
  define("_DROIT_NOTE_CONSULT_SEUL",0);           // Acces en consultation uniquement
  define("_DROIT_NOTE_CONSULT_RECHERCHE",5);      // Acces en consultation avec recherche
  define("_DROIT_NOTE_STANDARD_SANS_APPR",10);    // Acces standard sans appropriation
  define("_DROIT_NOTE_STANDARD",15);              // Acces standard
  define("_DROIT_NOTE_MODIF_STATUT",20);          // Acces standard avec modification du statut des notes
  define("_DROIT_NOTE_MODIF_CREATION",30);        // Acces en modification et creation (sans suppression)
  define("_DROIT_NOTE_COMPLET",40);               // Acces complet
// ----------------------------------------------------------------------------
//Lecture variable en GET
// ----------------------------------------------------------------------------
  if (!isset($drprf)) $drprf=$_GET['drprf'];
  if (!isset($dragd)) $dragd=$_GET['dragd'];
  if (!isset($drnt)) $drnt=$_GET['drnt'];
  if (!isset($dradm)) $dradm=$_GET['dradm'];

?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>

  <head>
    <meta name="generator" content="HelpNDoc Free">
    <link rel="StyleSheet" href="dtree.css" type="text/css" />
    <script type="text/javascript" src="dtree.js"></script>
  </head>

  <body>
  
    <script type="text/javascript"><!--
      d = new dTree('d');
      d.config.target = 'FrameMain';
      d.add(0,-1,'Phenix V. 5.00 - Aide Version 2.1.14','javascript:void(0);');
      d.add(1, 0,'Pr&eacute;sentation','{E4CF7E7D-35BE-4448-A6A5-92834672B4EF}.htm');
<?php
  if ($dradm == "O")
    {
?>
      d.add(2, 0,'Installation','{0377F596-8499-47CC-8DD9-5795A1A4377A}.htm');
      d.add(3, 2,'Premi&egrave;re Installation','{EDA7FE2C-9936-4ED3-B48F-6616CE3DC8DC}.htm');
      d.add(4, 2,'Mise &agrave; jour','{8C67B4EC-FB2C-449A-8579-C8D2DB3EFF5D}.htm');
<?php
    }
?>
      d.add(5, 0,'G&eacute;n&eacute;ralit&eacute;s','{A5D34344-5617-47DF-897A-F3CD543D827B}.htm');
      d.add(6, 5,'Menu de gauche','{0C52ACB6-A1D7-47F0-AB79-7493859F5CA1}.htm');
      d.add(7, 5,'Les Types de notes','{EF3DC494-7943-46E3-B280-1CDF714751C0}.htm');
      d.add(8, 5,'Les Symboles utilis&eacute;s','{C59921CB-F5C7-42D5-9FA9-DADB7C7555D2}.htm');
<?php
  if ($drnt > _DROIT_NOTE_CONSULT_RECHERCHE)
    {
?>
      d.add(9, 5,'Editeur de textes','{4759AE33-DDA4-4F70-9CE8-61E40C0F40C0}.htm');
<?php
    }
?>
      d.add(10, 5,'Note r&eacute;currente','{3F406184-A7D2-4565-9457-14350287D2A5}.htm');
      d.add(11, 0,'Utilisation','{4DDE6AC3-F1B0-417E-9BD0-7491C4E8CB6A}.htm');
      d.add(12, 11,'Connexion','{E7B010F2-00A9-450E-98BA-EB8F63B558C5}.htm');
      d.add(13, 11,'Planning','{6DD26C78-0204-4300-8FF1-4F6886816CAD}.htm');
      d.add(14, 13,'Quotidien','{B55ECA15-804B-4FB2-91EE-D487324B1C8C}.htm');
      d.add(15, 13,'Hebdomadaire','{88982C88-CD51-4213-9B29-9D04C388D46B}.htm');
      d.add(16, 13,'Mensuel','{E78236EE-3D7C-4A38-86DF-B9C6A8993542}.htm');
      d.add(17, 13,'Annuel','{8A62A5B1-62F7-453C-A92C-B6D344A7E9E8}.htm');
<?php
  if ($dragd > _DROIT_AGENDA_SEUL)
    {
?>
      d.add(18, 13,'Quotidien global','{7F6D83DC-E5DF-4B8B-8ABC-022A60F8D0B2}.htm');
      d.add(19, 13,'Hebdomadaire global','{85E65C25-4C1D-4019-B461-078B64F49562}.htm');
      d.add(20, 13,'Mensuel Global','{64302D5C-A631-4D23-9EF4-6FDAA525E58B}.htm');
<?php
    }
  if ($drnt > _DROIT_NOTE_CONSULT_RECHERCHE)
    {
?>
      d.add(21, 11,'Gestion des ...','{C69C30FD-88ED-433C-96F7-54368B35AD57}.htm');
      d.add(22, 21,'Notes','{36EC3C0B-85F2-4573-963F-4C4208A3A54C}.htm');
      d.add(23, 22,'Modifier une Note','{122B5E38-AB0F-482F-A58F-3C5B9782FCCA}.htm');
      d.add(24, 22,'Dupliquer une Note','{FED7C499-B685-460C-BD21-65BE9755130D}.htm');
      d.add(25, 21,'Anniversaires','{F2E5B6A9-A9AF-4AC9-A74A-82B31A27F429}.htm');
      d.add(26, 21,'Contacts','{3BA2E92E-D7A9-404C-A69E-9F20BED6D280}.htm');
      d.add(27, 21,'Ev&egrave;nements','{E8CA709C-9AFA-4BC2-BAFF-CEEFBCB3FD06}.htm');
      d.add(28, 21,'M&eacute;mos','{B66BF351-A5A0-4634-A2B6-228C1885D372}.htm');
      d.add(29, 21,'Libell&eacute;s','{85E07E51-421D-4EC2-BBFA-3808BBC52924}.htm');
      d.add(30, 21,'Favoris','{770FEB23-1593-43A7-8822-7313D62D86CC}.htm');
<?php
    }
  if ($drnt > _DROIT_NOTE_CONSULT_SEUL)
    {
?>
      d.add(31, 11,'Recherche','{310A54C1-0AA2-4E20-AB65-5BD7AEEF8506}.htm');
<?php
    }
  if ($dragd > _DROIT_AGENDA_SEUL && $drnt > _DROIT_NOTE_CONSULT_RECHERCHE)
    {
?>
      d.add(32, 11,'Disponibilit&eacute;s','{3C403BBF-5B44-4284-A17A-01E22D4B8F42}.htm');
<?php
    }
  if ($drnt > _DROIT_NOTE_CONSULT_RECHERCHE)
    {
?>
      d.add(33, 11,'Contacts','{762B871C-C2F0-406D-AB69-463A314CCBFA}.htm');
<?php
    }
  if ($drprf > _DROIT_PROFIL_RIEN || $drnt > _DROIT_NOTE_CONSULT_RECHERCHE)
    {
?>
      d.add(34, 11,'Outils','{CC1BD467-3098-49FB-BA34-652E1F77AEE5}.htm');
<?php
    }
  if ($drprf > _DROIT_PROFIL_RIEN)
    {
?>
      d.add(35, 34,'Options du Profil','{834C7C2C-9580-4D38-958D-3F077E3B1FEB}.htm');
      d.add(36, 35,'Profil - Informations personnelles','{77E12098-C143-4D6C-AB5E-660373B46053}.htm');
      d.add(37, 35,'Profil - Affichage','{52E3826F-0A74-4031-AE03-2EF75660F860}.htm');
      d.add(38, 35,'Profil - Param&egrave;tres','{F06EEE75-CA56-4A72-951A-1F8029E2FC77}.htm');
<?php
    if ($dradm == "O")
      {
?>
      d.add(39, 35,'Profil - Droits','{58782409-74AE-4D5D-9EE4-B21C1588D9B0}.htm');
<?php
      }
    }
  if ($drnt > _DROIT_NOTE_CONSULT_RECHERCHE)
    {
?>
      d.add(40, 34,'Importer des Notes','{F868D6BB-FC1B-4AF6-B027-B6B3893C4F35}.htm');
      d.add(41, 34,'Exporter des Notes','{4B71BC68-9EE3-426C-AD3B-9CD2F3323B78}.htm');
      d.add(42, 34,'Importer des Contacts','{BD884D6B-8728-4155-A68B-BAABEA73C8B0}.htm');
<?php
    }
  if ($dradm == "O")
    {
?>
      d.add(43, 0,'Administration','{9A2DB920-6EFD-4984-A08A-398B5B7BB54D}.htm');
      d.add(44, 43,'Principes','{BD068E50-F39F-42D3-937F-DF0589A619A2}.htm');
      d.add(45, 43,'Base de donn&eacute;es','{BB41BC1D-0F35-4185-AECA-D4B9A66200F4}.htm');
      d.add(46, 43,'Protection des programmes','{F65841B2-6538-4890-A3D7-FAE664D9D1DA}.htm');
      d.add(47, 43,'Le fichier conf.inc.php','{9B1B2D84-03F1-4D10-BC2B-D1A366C8E517}.htm');
      d.add(48, 43,'Configuration','{61FCC78C-D947-4443-8ACE-7476482020D5}.htm');
      d.add(49, 48,'Options Utilisateurs','{71ED7FA1-23A6-4A54-8AE9-684DAB73CBB5}.htm');
      d.add(50, 48,'Options G&eacute;n&eacute;rales','{1F1AF2DD-A8CE-48C6-8D0D-0BFEC6007107}.htm');
      d.add(51, 48,'Sauvegarder la base','{3FFC6673-5790-4F64-BBC5-3D964092A129}.htm');
      d.add(52, 48,'Optimiser la base','{B91BFA1B-ABBC-498C-BF09-8069DCA56A5F}.htm');
      d.add(53, 48,'Installer un Mod','{43298441-B736-4BF9-9911-746CAE97A62F}.htm');
      d.add(54, 43,'Utilisateurs','{5F1EB114-9186-4CC3-85FC-DB0300DAB91A}.htm');
      d.add(55, 54,'Cr&eacute;er un compte','{4D6F7DEE-6D66-47EA-AA0C-F26B56A4D48A}.htm');
      d.add(56, 54,'Modifier un compte','{46BA8B87-EF04-479F-B10B-8C3F4F12DE89}.htm');
      d.add(57, 54,'Supprimer un compte','{6CAD8269-13D9-47A5-B7DC-50D52E6CFE41}.htm');
      d.add(58, 54,'Groupe d\'utilisateurs','{711DB8F2-D226-41F9-BE1C-6AFF868C2855}.htm');
      d.add(59, 43,'Administrateurs','{3DFCED8D-1B1A-4C5A-95F9-B9BFB4293CEC}.htm');
      d.add(60, 59,'Cr&eacute;er un compte','{74645D27-673D-4152-87C1-E3E789893771}.htm');
      d.add(61, 59,'Modifier un compte','{27D09087-BE45-40EB-B1E0-349848A658B2}.htm');
      d.add(62, 59,'Suppression de compte','{4E93AD4F-8706-4BC1-8139-73BACAC40FA2}.htm');
      d.add(63, 43,'Ev&egrave;nements et Notes','{861AE930-3C5A-431F-AB84-BF171C84896A}.htm');
      d.add(64, 63,'Ajouter des &eacute;v&egrave;nements','{D9CD566C-FE8D-4298-96CE-97837E723D47}.htm');
      d.add(65, 63,'Suppression d\'&eacute;v&egrave;nements','{EDFA8BF2-A83C-4D6F-9977-D76EDD75E4E4}.htm');
      d.add(66, 63,'Suppression de notes','{B02634B8-B7E6-41EC-8E35-FCB9BF4D9F0E}.htm');
      d.add(67, 63,'Couleur des notes','{BCE67695-332A-478D-9E0A-15AB08D78659}.htm');
      d.add(68, 63,'Jours f&eacute;ri&eacute;s','{6BC3402F-ACA7-4FE3-951E-9BC7FB3263A6}.htm');
<?php
    }
?>
      d.add(69, 0,'Palm / Smartphone / Iphone','{DE8DF60B-1F68-4BB4-A4F6-C19A79AC325A}.htm');
      d.add(70, 69,'Ecran principal','{CF725E5B-B8F5-4CB3-B8FC-D91A4D385901}.htm');
      d.add(71, 69,'Ecran Note','{A815AA7E-DD82-4114-A29B-5A0B1DC4A780}.htm');
      d.add(72, 69,'Ecran Calepin','{0041C052-9DCE-4179-A145-102242E50754}.htm');
      d.add(73, 69,'Ecran Contact','{A3A5AD57-3535-4FC7-8BDE-C22ADD9D6DAA}.htm');
      d.add(74, 69,'Ecran Journ&eacute;e','{5114B9D7-743C-4386-A72D-98555F52B907}.htm');
      d.add(75, 0,'Echanges de donn&eacute;es','{422E5433-6B45-465C-B0A9-70C6B1087DE3}.htm');
      d.add(76, 75,'Sunbird','{7ED8BAE8-2A68-4E3B-8F3B-92653878365E}.htm');
      d.add(77, 75,'Flux Rss','{0632EC08-4CAB-46E0-B664-88C20829AD65}.htm');
<?php
  if ($dradm == "O")
    {
?>
      d.add(78, 0,'R&eacute;diger un Mod','{C935501A-4D12-4E2D-A3D5-5147E2BAB5FA}.htm');
      d.add(79, 78,'D&eacute;nomination','{86B5C28B-541F-4062-96D4-98B155B8D847}.htm');
      d.add(80, 78,'Structure du Mod','{7DB78690-0501-45EF-8B07-501E1AEAAAE9}.htm');
      d.add(81, 78,'Informations sur le Mod','{5E96D6D4-0562-4D1D-BF6E-8427A723D645}.htm');
      d.add(82, 78,'Instructions du Mod','{26D1E790-02FB-4D75-84C8-7AD3EAA86D8D}.htm');
      d.add(83, 78,'Exemple de Mod','{C443D777-8E64-429F-BB3C-F27537FDF03F}.htm');
<?php
    }
?>
      d.add(84, 0,'Toujours une question ?','{59C60C25-97CA-476B-AF88-81D87288AC7D}.htm');
      document.write(d);
    //--></script>
    
  </body>

</html>