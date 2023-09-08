<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'tool_tcpdffonts', language 'fr', version '4.1'.
 *
 * @package     tool_tcpdffonts
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['action:font:add'] = 'Ajouter une police (TTF/OTF)';
$string['action:font:addzippedfont'] = 'Ajouter une police à partir de ZIP';
$string['action:font:delete'] = 'Supprimer la police';
$string['action:font:download'] = 'Télécharger la police';
$string['action:font:preview'] = 'Prévisualiser la police';
$string['action:initcustomfonts'] = 'Initialiser le dossier des polices personnalisées';
$string['action:resetcorefonts'] = 'Réinitialiser le dossier des polices TCPDF principales';
$string['addfont:explain'] = 'Les paramètres ci-dessous vous permettent de personnaliser la façon dont la police sera générée.<br/>
Si vous n\'avez aucune idée de la nature de ces paramètres, veuillez conserver les valeurs par défaut ( cela couvre la plupart des cas standard ).<br/>
Ce n\'est que lorsque vous savez exactement ce que signifient les paramètres ou que vous avez été informé des paramètres que votre police True Type/Open Type doit être convertie que vous devez ajuster ces paramètres.';
$string['backtolist'] = 'Retour à l\'aperçu des polices';
$string['bold'] = 'Appliquer une épaisseur de police en gras';
$string['defaultpreviewtext'] = 'Le renard brun rapide saute par-dessus le porc paresseux et quel saut c\'était.';
$string['err:font:otf-otto'] = 'TCPDF ne peut pas traiter/ajouter des polices de type OTF ( OTTO )';
$string['err:font:zip'] = 'Le fichier téléchargé ne semble pas être une archive ZIP valide';
$string['err:zipopen'] = 'Une erreur s\'est produite lors de la tentative de création de l\'archive téléchargeable.<br/>
Vous pouvez essayer de rafraîchir cette page. Si l\'erreur persiste, veuillez contacter l\'administrateur système.';
$string['family'] = 'Famille de polices';
$string['folderstate:custom'] = 'Le dossier des polices TCPDF est personnalisé et prêt à être fourni avec des polices personnalisées.';
$string['folderstate:default'] = 'Le dossier Polices TCPDF n\'est pas encore personnalisé. Vous ne pouvez pas utiliser les actions spécialisées fournies par cet outil ( telles que l\'ajout de polices personnalisées ) <i>avant</i> d\'avoir initialisé le dossier des polices à l\'aide de l\'action d\'initialisation ci-dessous.';
$string['font:add'] = 'Ajouter une police';
$string['font:addfont:fail'] = 'Impossible d\'ajouter la police {$a}';
$string['font:addfont:success'] = 'Police {$a} ajoutée avec succès';
$string['font:delete'] = 'Supprimer la police';
$string['font:delete:body'] = 'Vous êtes sur le point de supprimer la police personnalisée {$a}.<br/>
Il s\'agit d\'un processus irréversible qui ne peut être annulé. Toutes les données de police seront supprimées.<br/>
Voulez-vous vraiment continuer ?';
$string['font:delete:confirmation'] = 'Oui, je comprends et souhaite supprimer cette police.';
$string['font:delete:fail'] = 'Impossible de supprimer la police.';
$string['font:delete:header'] = 'Supprimer la police';
$string['font:delete:success'] = 'Police supprimée avec succès.';
$string['font:family:delete:body'] = 'Vous êtes sur le point de supprimer la famille de polices personnalisée {$a}.<br/>
Il s\'agit d\'un processus irréversible qui ne peut être annulé. Toutes les données de police seront supprimées.<br/>
Voulez-vous vraiment continuer ?';
$string['font:family:delete:confirmation'] = 'Oui, je comprends et souhaite supprimer cette famille de polices.';
$string['font:family:delete:fail'] = 'Impossible de supprimer la famille de polices.';
$string['font:family:delete:header'] = 'Supprimer la famille de polices {$a}';
$string['font:family:delete:success'] = 'Famille de polices supprimée avec succès.';
$string['font:initcustom:body'] = 'Vous êtes sur le point d\'initialiser la personnalisation de la police TCPDF.<br/>
Cela signifie que nous allons :<ul>
<li>créez le dossier personnalisé dans votre dossier de données Moodle</li>
<li>copier toutes les polices principales ( ce qui est un mandat de Moodle, sinon Moodle <i>retournera</i> au dossier principal )</li>
</ul>
Vous pouvez le faire en toute sécurité et supprimer la personnalisation à tout moment pour revenir à l\'implémentation principale de Moodle.<br/>
Voulez-vous vraiment continuer ?';
$string['font:initcustom:confirmation'] = 'Oui, je comprends et souhaite initialiser les personnalisations des polices TCPDF.';
$string['font:initcustom:fail'] = 'Échec de l\'initialisation des polices personnalisées TCPDF. Erreur : {$a}';
$string['font:initcustom:header'] = 'Initialiser les polices personnalisées TCPDF';
$string['font:initcustom:success'] = 'Polices personnalisées TCPDF initialisées avec succès.';
$string['font:resetcore:body'] = 'Vous êtes sur le point de réinitialiser la personnalisation de la police TCPDF sur le cœur.<br/>
Cela signifie que nous revenons au cœur TCPDF interne de Moodle avec les polices standard fournies avec l\'installation.<br/>
<b>Avertissement</b> : Toutes vos polices personnalisées seront supprimées ; il n\'y a aucun moyen de revenir en arrière une fois cette action terminée.<br/>
Voulez-vous vraiment continuer ?';
$string['font:resetcore:confirmation'] = 'Oui, je comprends et souhaite réinitialiser toutes les personnalisations de police.';
$string['font:resetcore:fail'] = 'Échec de la réinitialisation des polices TCPDF de base. Erreur : {$a}';
$string['font:resetcore:header'] = 'Réinitialiser les polices TCPDF de base';
$string['font:resetcore:success'] = 'Réinitialiser avec succès les polices de base TCPDF.';
$string['fontaddcbbox'] = 'Ajouter des informations sur le cadre de délimitation ?';
$string['fontaddcbbox_help'] = 'Lorsqu\'il est sélectionné, cela ajoutera des informations de cadre de délimitation aux fichiers de police générés.';
$string['fontenc'] = 'Tableau de codage';
$string['fontenc_help'] = 'Nom de la table d\'encodage à utiliser. Laisser vide pour le mode par défaut. Omettre ce paramètre pour TrueType Unicode et les polices symboliques comme Symbol ou ZapfDingBats.';
$string['fontencid'] = 'Identifiant d\'encodage';
$string['fontencid_help'] = 'Identifiant d\'encodage pour la table CMAP à extraire ( lors de la création d\'une police Unicode pour Windows, cette valeur doit être égale à 1, pour Macintosh doit être de 0 ). Lorsque l\'ID de plate-forme est 3, les valeurs légales pour l\'identifiant de codage sont : 0 = Symbole, 1 = Unicode, 2 = ShiftJIS, 3 = PRC, 4 = Big5, 5 = Wansung, 6 = Johab, 7 = Réservé, 8 = Réservé, 9 = Réservé, 10 = UCS-4.';
$string['fontfile'] = 'Fichier de police';
$string['fontflags'] = 'Drapeaux';
$string['fontflags_help'] = 'Entier 32 bits non signé contenant des drapeaux spécifiant diverses caractéristiques de la police ( PDF32000:2008 - 9.8.2 Drapeaux de descripteur de police) : +1 pour la police fixe ; +4 pour le symbole ou +32 pour le non-symbole ; +64 pour l\'italique. Les modes Fixe et Italique sont généralement détectés automatiquement, vous devez donc le définir sur 32 = police non symbolique ( par défaut ) ou 4 = police symbolique.';
$string['fontforceoverwrite'] = 'Écraser la police si elle existe déjà (par opposition à la sortie anticipée).';
$string['fontplatid'] = 'Identifiant de la plate-forme';
$string['fontplatid_help'] = 'Identifiant de plate-forme pour la table CMAP à extraire (lors de la création d\'une police Unicode pour Windows, cette valeur doit être 3, pour Macintosh doit être 1).';
$string['fonttype'] = 'Type de police';
$string['fonttype_help'] = 'Sélectionner ici le type de police ou laisser la détection automatique.';
$string['ft:autodetect'] = 'Détection automatique';
$string['ft:cid0cs'] = 'CID-0 chinois simplifié';
$string['ft:cid0ct'] = 'CID-0 chinois traditionnel';
$string['ft:cid0jp'] = 'CID-0 japonais';
$string['ft:cid0kr'] = 'CID-0 coréen';
$string['ft:truetype'] = 'True Type';
$string['ft:truetypeunicode'] = 'True Type Unicode';
$string['ft:type1'] = 'Type-1';
$string['italic'] = 'Appliquer le style de police en italique';
$string['missing:ctg-file'] = 'Version compressée manquante de CIDToGIDMap pour {$a}';
$string['missing:z-file'] = 'Version compressée manquante de la source de police pour {$a}';
$string['nonsymbolfont'] = 'Police sans symbole';
$string['pluginname'] = 'Polices TCPDF';
$string['previewfor'] = 'Aperçu de la police pour {$a}';
$string['previewtext'] = 'Aperçu du texte';
$string['settings'] = 'Paramètres de police TCPDF';
$string['size'] = 'Taille de police';
$string['styles'] = 'Styles';
$string['symbolfont'] = 'Police de symboles';
$string['tcpdffonts:managefonts'] = 'Gérer les polices PDF';
$string['tcpdffonts:viewfonts'] = 'Afficher la liste des polices PDF';
$string['toggle:familyview:off'] = 'Regrouper par famille : Désactivé';
$string['toggle:familyview:on'] = 'Regrouper par famille : Activé';
$string['toggle:subset'] = 'Appliquer un sous-ensemble de polices';
$string['type'] = 'Type';
$string['updatepreview'] = 'Actualiser la prévisualisation';
$string['upload:font:exists'] = 'La police {$a} a déjà été traitée et ne sera pas écrasée. Si vous souhaitez écraser la police, veuillez d\'abord supprimer la police existante ou cochez l\'option pour forcer l\'écrasement de la police.';
$string['uploadfontfile:description'] = 'Vous pouvez télécharger de nouvelles polices à partir d\'un fichier TTF ou OTF et indiquer les paramètres corrects pour générer le ou les fichiers de polices TCPDF';
$string['uploadfontfile:header'] = 'Télécharger le fichier de police';
$string['uploadzipfile:description'] = 'Vous pouvez télécharger un fichier compressé contenant le(s) fichier(s) de police TCPDF composé d\'un fichier PHP qui définit la police, un fichier .z qui est essentiellement la source de police compressée par gz et éventuellement le fichier ctg.z qui contient tout mappage CID à GID. <br/>
<div class="alert alert-danger"><strong>Avertissement :</strong> vous ne devez jamais faire confiance à des fichiers provenant d\'une source externe dans Moodle, en particulier lorsqu\'ils contiennent des fichiers PHP, car cela pose un risque de sécurité majeur.< br/>
Vous devez absolument vous assurer que le contenu du fichier zip que vous téléchargez ici est vérifié et validé ! Bien que certaines vérifications rudimentaires soient effectuées pour déterminer la validité du fichier de définition de police basé sur PHP pour TCPDF,
cela ne garantit rien du tout. En raison de l\'absence d\'un mécanisme de validation à part entière ( ou littéralement de tout autre fichier de définition autre qu\'un fichier PHP ; une définition codée en JSON, par exemple, aurait été préférable ), vous êtes entièrement responsable de vous assurer que le fichier zip téléchargé ne contient aucun code malicieux.
</div>';
$string['uploadzipfile:header'] = 'Télécharger le fichier zip de la police';
