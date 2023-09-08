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
 * Strings for component 'format_grid', language 'fr', version '4.1'.
 *
 * @package     format_grid
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addsection'] = 'Ajouter une section';
$string['cannotconvertuploadedimagetodisplayedimage'] = 'Impossible de convertir l\'image téléversée comme image à afficher - {$a}. Veuillez reporter les détails de l\'erreur et l\'information contenue dans le fichier php.log au développeur.';
$string['cannotgetmanagesectionimagelock'] = 'Impossible d\'obtenir le verrouillage de l\'image de la section de gestion. Cela peut arriver si deux personnes modifient les paramètres de la même section sur le même parcours en même temps.';
$string['centre'] = 'Centre';
$string['crop'] = 'Rognage';
$string['currentsection'] = 'Cette section';
$string['default'] = 'Défaut - {$a}';
$string['defaultdisplayedimagefiletype'] = 'Type d\'image affichée';
$string['defaultdisplayedimagefiletype_desc'] = 'Définir le type d\'image affichée.';
$string['defaultimagecontainerratio'] = 'Ratio par défaut du conteneur de l\'image relatif à la largeur';
$string['defaultimagecontainerratio_desc'] = 'Le ratio par défaut du conteneur de l\'image relatif à la largeur.';
$string['defaultimagecontainerwidth'] = 'Largeur par défaut du conteneur de l\'image';
$string['defaultimagecontainerwidth_desc'] = 'La largeur par défaut du conteneur de l\'image.';
$string['defaultimageresizemethod'] = 'Méthode par défaut de redimensionnement de l\'image';
$string['defaultimageresizemethod_desc'] = 'La méthode par défaut de redimensionnement de l\'image.';
$string['defaultpopup'] = 'Utiliser une fenêtre flottante';
$string['defaultpopup_desc'] = 'Affiche par défaut la section dans une fenêtre flottante au lieu de naviguer sur une seule page de section.';
$string['defaultshowcompletion'] = 'Afficher l\'achèvement';
$string['defaultshowcompletion_desc'] = 'Affiche par défaut l\'achèvement de la section sur la grille.';
$string['defaultsinglepagesummaryimage'] = 'Afficher l\'image de la grille dans le résumé de la section';
$string['defaultsinglepagesummaryimage_desc'] = 'Afficher l\'image de la grille pour cette section dans le résumé de la section lorsqu\'il y a un résumé dans la section.';
$string['deletesection'] = 'Supprimer la section';
$string['editsection'] = 'Modifier la section';
$string['editsectionname'] = 'Modifier le nom de section';
$string['formatnotsupported'] = 'Le format n\'est pas pris en charge sur ce serveur ; veuillez installer, sur le serveur, l\'extension PHP GD - {$a}.';
$string['functionfailed'] = 'La fonction a échoué sur l\'image - {$a}.';
$string['hidefromothers'] = 'Masquer la section';
$string['imagecontainerratio'] = 'Définir le rapport du conteneur d\'image par rapport à la largeur';
$string['imagecontainerratio_help'] = 'Définir le rapport du conteneur d\'image sur l\'une des valeurs : 3-2, 3-1, 3-3, 2-3, 1-3, 4-3 or 3-4.';
$string['imagecontainerwidth'] = 'Définir la largeur du conteneur d\'image';
$string['imagecontainerwidth_help'] = 'Définir la largeur du conteneur d\'image sur l\'une des valeurs : 128, 192, 210, 256, 320, 384, 448, 512, 576, 640, 704 or 768';
$string['imageresizemethod'] = 'Définir la méthode de redimensionnement de l\'image';
$string['imageresizemethod_help'] = 'Définissez la méthode de redimensionnement de l\'image sur : « Scale » ou « Crop » lors du redimensionnement de l\'image pour l\'adapter au conteneur.';
$string['information'] = 'Information';
$string['informationsettings'] = 'Paramètres d\'informations';
$string['informationsettingsdesc'] = 'Informations sur le format de la grille';
$string['left'] = 'Gauche';
$string['markedthissection'] = 'Cette section en surbrillance est la section courante';
$string['markthissection'] = 'Mettre en surbrillance cette section comme la section courante.';
$string['mimetypenotsupported'] = 'Le type MIME n\'est pas accepté comme type d\'image dans le format Vue en images - {$a}.';
$string['newsectionname'] = 'Nouveau nom pour la section {$a}';
$string['noimageinformation'] = 'Les informations sur l\'image sont vides - {$a}.';
$string['numbersections'] = 'Nombre de sections';
$string['off'] = 'Désactivé';
$string['original'] = 'Original';
$string['originalheightempty'] = 'La hauteur d\'origine est vide - {$a}.';
$string['originalwidthempty'] = 'La largeur d\'origine est vide - {$a}.';
$string['page-course-view-grid'] = 'Toutes les pages principales de cours au format vue en image.';
$string['page-course-view-grid-x'] = 'Toutes les pages de cours au format vue en image.';
$string['pluginname'] = 'Vue en image';
$string['popup'] = 'Utiliser une fenêtre flottante';
$string['popup_help'] = 'Afficher la section dans une fenêtre flottante au lieu de naviguer sur une seule page de section.';
$string['privacy:nop'] = 'Le format Vue en image stocke de nombreux paramètres relatifs à sa configuration. Aucun de ces paramètres n\'est lié à un utilisateur spécifique. Il est de votre responsabilité de vous assurer qu\'aucune donnée utilisateur n\'est entrée dans les champs de texte libre. Si vous définissez un paramètre, cette action sera consignée dans les journaux de Moodle par rapport à l\'utilisateur qui l\'a modifiée. Cela ne relève pas du contrôle des formats. Veuillez consulter le système de journaux principal pour en savoir plus sur la confidentialité. Lorsque vous déposez des images, évitez de les déposer avec des données de localisation intégrées (GPS EXIF) incluses ou toute autre donnée personnelle. Il serait possible d\'extraire n\'importe quelle localisation / donnée personnelle des images. Veuillez examiner attentivement le code pour vous assurer qu\'il est conforme à votre interprétation des lois sur la confidentialité. Je ne suis pas avocat et mon analyse repose sur mon interprétation. Si vous avez un doute, supprimez le format.';
$string['reporterror'] = 'Veuillez signaler les détails de l\'erreur et les informations contenues dans le fichier php.log au développeur';
$string['right'] = 'Droite';
$string['scale'] = 'Mise à l\'échelle';
$string['section0name'] = 'Général';
$string['sectionbreak'] = 'Saut de section';
$string['sectionbreak_help'] = 'Saut dans la grille à cette cette section.';
$string['sectionbreakheading'] = 'En-tête de saut de section';
$string['sectionbreakheading_help'] = 'Afficher cet en-tête à l\'endroit où le saut de section se trouve dans la grille. HTML peut être utilisé.';
$string['sectionchangecoursesettings'] = 'Changer le nombre de sections dans les paramètres du cours';
$string['sectionimage'] = 'Image de la section';
$string['sectionimage_help'] = 'L\'image de la section.';
$string['sectionimagealttext'] = 'Texte alternatif de l\'image';
$string['sectionimagealttext_help'] = 'Ce texte sera défini comme l\'attribut alternatif de l\'image.';
$string['sectionname'] = 'Section';
$string['settings'] = 'Paramètres';
$string['settingssettings'] = 'Paramètres des paramètres';
$string['settingssettingsdesc'] = 'Paramètres du format vue en images';
$string['showcompletion'] = 'Afficher l\'achèvement';
$string['showcompletion_help'] = 'Montrer l\'achèvement de la section sur la grille.';
$string['showfromothers'] = 'Afficher la section';
$string['singlepagesummaryimage'] = 'Afficher l\'image de la grille dans le résumé de la section';
$string['singlepagesummaryimage_help'] = 'Afficher l\'image de la grille pour cette section dans le résumé de la section lorsqu\'il existe dans la section.';
$string['topic'] = 'Section';
$string['topic0'] = 'Général';
$string['versionalpha'] = 'Version Alpha - Contient presque certainement des bogues. Il s\'agit d\'une version de développement pour les développeurs « uniquement » ! Ne pensez même pas à installer sur un serveur de production !';
$string['versionbeta'] = 'Version bêta - susceptible de contenir des bogues. Prêt à être testé par les administrateurs sur un serveur de test uniquement.';
$string['versioninfo'] = 'Release {$a->release}, version {$a->version} sur Moodle {$a->moodle}';
$string['versionrc'] = 'Version Release Candidate - Peut contenir des bogues. Vérifiez complètement sur un serveur de test avant d\'envisager sur un serveur de production.';
$string['versionstable'] = 'Version stable - Peut contenir des bogues. Vérifiez sur un serveur de test avant d\'installer sur votre serveur de production.';
$string['webp'] = 'WebP';
