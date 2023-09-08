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
 * Strings for component 'board', language 'fr', version '4.1'.
 *
 * @package     board
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['Cancel'] = 'Annuler';
$string['Ok'] = 'OK';
$string['addcomment'] = 'Ajouter un commentaire';
$string['addrating'] = 'Évaluer les messages';
$string['addrating_all'] = 'Par tout le monde';
$string['addrating_none'] = 'Désactivé';
$string['addrating_students'] = 'Par les étudiants';
$string['addrating_teachers'] = 'Par les enseignants';
$string['allowyoutube'] = 'Autoriser YouTube';
$string['allowyoutube_desc'] = 'Si ce réglage est activé, un bouton pour ajouter une vidéo YouTube intégrée est pris en charge.';
$string['aria_addmedia'] = 'Ajouter {type} pour le message {post} de la colonne {column}';
$string['aria_addmedianew'] = 'Ajouter {type} pour le nouveau message de la colonne {column}';
$string['aria_canceledit'] = 'Annuler l\'édition du message {post} de la colonne {column}';
$string['aria_cancelnew'] = 'Annuler le nouveau message pour la colonne {column}';
$string['aria_choosefileedit'] = 'Sélectionner un fichier pour le message {post} de la colonne {column}';
$string['aria_choosefilenew'] = 'Sélectionner un fichier pour le nouveau message de la colonne {column}';
$string['aria_deleteattachment'] = 'Supprimer la pièce jointe du message {post} de la colonne {column}';
$string['aria_deletecolumn'] = 'Supprimer la colonne {column}';
$string['aria_deletepost'] = 'Supprimer le message {post} de la colonne {column}';
$string['aria_editpost'] = 'Modifier la publication {post}';
$string['aria_lockcolumn'] = 'Verrouiller la colonne {column}';
$string['aria_movecolumn'] = 'Déplacer la colonne {column}';
$string['aria_movepost'] = 'Déplacer la publication {post}';
$string['aria_newcolumn'] = 'Ajouter une nouvelle colonne';
$string['aria_newpost'] = 'Ajouter un nouveau message à la colonne {column}';
$string['aria_postedit'] = 'Enregistrer les modifications du message {post} de la colonne {column}';
$string['aria_postnew'] = 'Enregistrer le nouveau message de la colonne {column}';
$string['aria_ratepost'] = 'Évaluer le message {post} de la colonne {column}';
$string['background_color'] = 'Couleur d\'arrière-plan';
$string['background_color_help'] = 'Doit être une couleur au format hexadécimal valide, comme #00cc99';
$string['background_image'] = 'Image d\'arrière plan';
$string['blanktargetenabled'] = 'Ce tableau a été configuré pour lancer tous ses liens URL / web dans une nouvelle fenêtre ou un nouvel onglet.';
$string['board:addinstance'] = 'Ajouter une nouvelle ressource «tableau»';
$string['board:deleteallcomments'] = 'Afficher et supprimer tous les commentaires sur les publications';
$string['board:manageboard'] = 'Gérer les colonnes et gérer tous les messages.';
$string['board:postcomment'] = 'Créer et afficher des commentaires sur les publications';
$string['board:view'] = 'Consulter le contenu du tableau et gérer vos propres messages.';
$string['board_column_locked'] = 'Cette colonne est verrouillée et ne peut pas être modifiée.';
$string['boardhasnotes'] = 'Ce tableau a déjà des messages, le changement de mode utilisateur n\'est pas autorisé';
$string['boardsettings'] = 'Réglages du tableau';
$string['brickfieldlogo'] = 'Logo propulsé par Brickfield';
$string['cancel_button_text'] = 'Annuler';
$string['choose_file'] = 'Choisir un fichier image';
$string['column_colours'] = 'Couleur des colonnes';
$string['column_colours_desc'] = 'Couleurs utilisées en haut de chaque colonne. Il s\'agit de couleurs au format hexadécimal : une couleur par ligne sous forme de 3 ou 6 caractères. Si l\'une de ces valeurs ne correspond pas à une couleur, les valeurs par défaut seront utilisées.';
$string['comment'] = 'Commentaire';
$string['comments'] = 'Commentaires {$a}';
$string['completiondetail:notes'] = 'Ajouter des annotations : {$a}';
$string['completionnotes'] = 'Demander aux élèves ce nombre d\'annotations pour terminer l\'activité';
$string['completionnotesgroup'] = 'Nécessite des annotations';
$string['default_column_heading'] = 'Titre';
$string['delete'] = 'Supprimer';
$string['deletecomment'] = 'Supprimer le commentaire';
$string['embed_height'] = 'Hauteur d\'intégration';
$string['embed_height_desc'] = 'Hauteur à utiliser pour l\'iframe lors de l\'intégration du tableau dans le cours. Il doit s\'agir d\'une valeur CSS valide, par ex. px, rem, %, etc.';
$string['embed_width'] = 'Largeur d\'intégration';
$string['embed_width_desc'] = 'Largeur à utiliser pour l\'iframe lors de l\'intégration du tableau dans le cours. Il doit s\'agir d\'une valeur CSS valide, par ex. px, rem, %, etc.';
$string['embedboard'] = 'Intégrer le tableau dans la page du cours';
$string['enableblanktarget'] = 'Activer nouvelle fenêtre';
$string['enableblanktarget_help'] = 'Si ce réglage est activé, tous les liens s\'ouvriront dans un nouvel onglet/fenêtre.';
$string['event_add_column'] = 'Colonne ajoutée';
$string['event_add_column_desc'] = 'L\'utilisateur avec l\'identifiant \'{$a->userid}\' a créé une colonne dans le tableau  \'{$a->objectid}\' et son nom est \'{$a->name}\'.';
$string['event_add_comment'] = 'Commentaire ajouté';
$string['event_add_comment_desc'] = 'L\'utilisateur d\'ID « {$a->userid} » a ajouté le commentaire « {$a->content} » d\'ID « {$a->objectid} » sur l\'annotation d\'ID « {$a->noteid} ».';
$string['event_add_note'] = 'Message ajouté';
$string['event_add_note_desc'] = 'L\'utilisateur avec l\'identifiant \'{$a->userid}\' a créé un message dans le tableau \'{$a->objectid}\', intitulé \'{$a->heading}\', contenant \'{$a->content}\', media \'{$a->media}\' dans la colonne \'{$a->columnid}\', et le groupe \'{$a->groupid}\'.';
$string['event_delete_column'] = 'Colonne supprimée';
$string['event_delete_column_desc'] = 'L\'utilisateur avec l\'identifiant \'{$a->userid}\' a supprimé la colonne du tableau \'{$a->objectid}\'.';
$string['event_delete_note'] = 'Message supprimé';
$string['event_delete_note_desc'] = 'L\'utilisateur avec l\'identifiant \'{$a->userid}\' a supprimé le message du tableau \'{$a->objectid}\' dans la colonne \'{$a->columnid}\'.';
$string['event_move_note'] = 'Message déplacé';
$string['event_move_note_desc'] = 'L\'utilisateur avec l\'identifiant \'{$a->userid}\' a déplacé le message du tableau \'{$a->objectid}\' vers la colonne \'{$a->columnid}\'.';
$string['event_rate_note'] = 'Message évalué';
$string['event_rate_note_desc'] = 'L\'utilisateur avec l\'identifiant \'{$a->userid}\' a évalué le message du tableau \'{$a->objectid}\' et lui a donné \'{$a->rating}\'.';
$string['event_update_column'] = 'Colonne mise à jour';
$string['event_update_column_desc'] = 'L\'utilisateur avec l\'identifiant \'{$a->userid}\' a mis à jour la colonne du tableau \'{$a->objectid}\' vers \'{$a->name}\'.';
$string['event_update_note'] = 'Message mis à jour';
$string['event_update_note_desc'] = 'L\'utilisateur avec l\'identifiant \'{$a->userid}\' a mis à jour un message dans le tableau \'{$a->objectid}\', vers l\'intitulé \'{$a->heading}\', contenant \'{$a->content}\', media \'{$a->media}\' dans la colonne \'{$a->columnid}\'.';
$string['export_board'] = 'Export au format CSV';
$string['export_content'] = 'Texte du message';
$string['export_email'] = 'Adresse de courriel';
$string['export_firstname'] = 'Prénom';
$string['export_heading'] = 'Intitulé du message';
$string['export_info'] = 'Titre du messsage';
$string['export_lastname'] = 'Nom de famille';
$string['export_submissions'] = 'Soumissions d\'exportation';
$string['export_timecreated'] = 'Date de création';
$string['export_url'] = 'Lien URL du message';
$string['form_body'] = 'Contenu';
$string['form_image_file'] = 'Fichier image';
$string['form_mediatype'] = 'Media';
$string['form_title'] = 'Titre du message';
$string['groupingid_required'] = 'Un regroupement de cours doit être sélectionné pour ce mode de groupe.';
$string['hideheaders'] = 'Cacher le titre des colonnes pour les étudiants';
$string['history'] = 'Historique du tableau';
$string['history_refresh'] = 'Délai de rafraîchissement du tableau';
$string['history_refresh_desc'] = 'Délai en secondes entre les rafraîchissements automatiques du tableau. Si la valeur est 0 ou vide, le tableau ne sera rafraîchi que pendant les actions du tableau (ajout/mise à jour/etc).';
$string['historyinfo'] = 'La table d\'historique du tableau n\'est utilisée que pour enregistrer des éléments temporaires, qui sont utilisés par les processus javascript pour actualiser les vues du tableau, et sont ensuite supprimés immédiatement.';
$string['invalid_file_extension'] = 'L\'extension du fichier n\'est pas acceptée pour le téléchargement.';
$string['invalid_file_size_max'] = 'La taille du fichier est trop importante pour être acceptée.';
$string['invalid_file_size_min'] = 'La taille du fichier est trop petite pour être acceptée.';
$string['invalid_youtube_url'] = 'Lien URL YouTube invalide';
$string['media_selection'] = 'Sélection des média';
$string['media_selection_buttons'] = 'Boutons';
$string['media_selection_desc'] = 'Configurer l\'affichage de la sélection de médias pour les messages.';
$string['media_selection_dropdown'] = 'Menu déroulant';
$string['modal_title_edit'] = 'Editer un message de la colonne {column}';
$string['modal_title_new'] = 'Nouveau message pour la  {column}';
$string['modulename'] = 'Tableau';
$string['modulename_help'] = 'Il s\'agit d\'une nouvelle activité pour Moodle qui permet à un enseignant de créer un nouveau tableau type «postit».';
$string['modulenameplural'] = 'Tableaux';
$string['move_column_to_aftercolumn'] = 'Déplacer la colonne après la colonne {$a}';
$string['move_column_to_firstplace'] = 'Déplacer la colonne en première position';
$string['move_to_afterpost'] = 'Déplacer après la publication {$a}';
$string['move_to_firstitemcolumn'] = 'Déplacer à la colonne {$a}';
$string['new_column_icon'] = 'Icône de nouvelle colonne';
$string['new_column_icon_desc'] = 'Icône affichée pour le bouton d\'ajout de colonnes';
$string['new_note_icon'] = 'Icône de nouveau message';
$string['new_note_icon_desc'] = 'Icône affichée sur le bouton d\'ajout de messages.';
$string['nopermission'] = 'Vous n\'avez pas les permissions nécessaires pour afficher ce tableau.';
$string['note_changed_text'] = 'La publication que vous êtes en train de modifier a changé.';
$string['note_changed_title'] = 'Confirmer';
$string['note_deleted_text'] = 'Le message que vous étiez en train de modifier a été supprimé.';
$string['nousers'] = 'Cette activité Tableau n\'a pas d\'utilisateurs inscrits';
$string['opensinnewwindow'] = 'S\'ouvre dans une nouvelle fenêtre';
$string['option_empty'] = 'Aucun';
$string['option_image'] = 'Image';
$string['option_image_info'] = 'Titre de l\'image';
$string['option_image_url'] = 'Lien URL de l\'image';
$string['option_link_info'] = 'Titre du lien';
$string['option_link_url'] = 'Lien URL';
$string['option_youtube'] = 'Vidéo (YouTube)';
$string['option_youtube_info'] = 'Titre de la vidéo';
$string['option_youtube_url'] = 'Lien URL YouTube';
$string['pluginadministration'] = 'Administration des modules du tableau';
$string['pluginname'] = 'Tableau';
$string['post_button_text'] = 'Message';
$string['post_max_length'] = 'Longueur maximale du message';
$string['post_max_length_desc'] = 'La longueur maximale autorisée du contenu. Tout ce qui dépasse cette longueur sera coupé.';
$string['postbydate'] = 'Message par date';
$string['postbyenabled'] = 'Définir une date limite pour que les étudiants puissent poster';
$string['posts'] = 'Publications';
$string['privacy:metadata:board_comments'] = 'Commentaires pour chaque publication du tableau.';
$string['privacy:metadata:board_comments:content'] = 'Le contenu du commentaire sur la publication';
$string['privacy:metadata:board_comments:noteid'] = 'L\'ID de la publication associée';
$string['privacy:metadata:board_comments:timecreated'] = 'L\'horodatage de la création du commentaire de publication';
$string['privacy:metadata:board_comments:userid'] = 'L\'ID de l\'utilisateur qui a ajouté le commentaire sur la publication';
$string['privacy:metadata:board_history'] = 'L\'historique temporaire du tableau enregistre des informations, utilisées par les processus javascript pour actualiser les vues du tableau, puis sont supprimées immédiatement.';
$string['privacy:metadata:board_history:action'] = 'L\'action effectuée';
$string['privacy:metadata:board_history:boardid'] = 'L\'ID du tableau';
$string['privacy:metadata:board_history:content'] = 'Les données JSON de l\'action effectuée';
$string['privacy:metadata:board_history:timecreated'] = 'L\'horodatage de l\'action';
$string['privacy:metadata:board_history:userid'] = 'L\'ID de l\'utilisateur qui a effectué l\'action';
$string['privacy:metadata:board_note_ratings'] = 'Informations sur les notes individuelles de chaque publication du tableau.';
$string['privacy:metadata:board_note_ratings:noteid'] = 'L\'ID de la publication associée';
$string['privacy:metadata:board_note_ratings:timecreated'] = 'L\'horodatage de l\'évaluation de la publication';
$string['privacy:metadata:board_note_ratings:userid'] = 'L\'ID de l\'utilisateur qui a créé l\'évaluation du message.';
$string['privacy:metadata:board_notes'] = 'Information sur les publications individuelles pour chaque tableau.';
$string['privacy:metadata:board_notes:columnid'] = 'L\'emplacement de la colonne de la publication';
$string['privacy:metadata:board_notes:content'] = 'Le contenu de la publication';
$string['privacy:metadata:board_notes:heading'] = 'Le titre de la publication';
$string['privacy:metadata:board_notes:info'] = 'Information concernant le média de la publication';
$string['privacy:metadata:board_notes:timecreated'] = 'L\'horodatage de la création de la publication';
$string['privacy:metadata:board_notes:url'] = 'L\'URL du média de la publication';
$string['privacy:metadata:board_notes:userid'] = 'L\'ID de l\'utilisateur qui a créé la publication';
$string['rate_note_text'] = 'Voulez-vous vraiment évaluer ce message ?';
$string['rate_note_title'] = 'Confirmer';
$string['ratings'] = 'Notes';
$string['remove_column_text'] = 'Voulez-vous vraiment supprimer la colonne "{$a}" et tous les messages qu\'elle contient ?';
$string['remove_column_title'] = 'Confirmer';
$string['remove_note_text'] = 'Voulez-vous vraiment supprimer ce message et toutes les données qu\'il contient, sachant que cela affectera également tous les autres utilisateurs ?';
$string['remove_note_title'] = 'Confirmer';
$string['selectuser'] = 'Sélectionner l\'utilisateur';
$string['selectuserplease'] = 'Veuillez sélectionner un utilisateur';
$string['singleusermode'] = 'Mode mono-utilisateur';
$string['singleusermode_desc'] = 'En mode mono-utilisateur, les utilisateurs ne peuvent ajouter des publications que sur leur propre tableau, si les utilisateurs privés ne peuvent pas voir les tableaux d\'autres utilisateurs, si les tableaux d\'utilisateurs publics sont disponibles via une liste déroulante.';
$string['singleusermodenone'] = 'Désactivé';
$string['singleusermodenotembed'] = 'Le tableau ne permet pas l\'intégration d\'un tableau mono-utilisateur. Veuillez modifier vos paramètres.';
$string['singleusermodeprivate'] = 'Mode mono-utilisateur (privé)';
$string['singleusermodepublic'] = 'Mode mono-utilisateur (public)';
$string['sortby'] = 'Trier par';
$string['sortbydate'] = 'Date de création';
$string['sortbynone'] = 'Aucun';
$string['sortbyrating'] = 'Evaluation';
$string['userscanedit'] = 'Autoriser tous les utilisateurs à modifier l\'emplacement de leurs propres publications.';
$string['warning'] = 'Notification';
