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
 * Strings for component 'block_quickmail', language 'fr', version '4.1'.
 *
 * @package     block_quickmail
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Actions';
$string['additional_emails'] = 'Adresses électroniques secondaires';
$string['additional_emails_help'] = 'Autres adresses électroniques vers lesquelles vous souhaiteriez envoyer le message (séparez ces adresses par une virgule ou un point virgule). Exemple:

email1@exemple.com, email2@exemple.com';
$string['additionalemail'] = 'Autoriser l\'envoi de courriels à des adresses de courriel externes';
$string['additionalemail_desc'] = 'Si cette option est activée, l\'expéditeur aura la possibilité d\'envoyer des messages à des courriels alternatifs en dehors de Moodle';
$string['all_in_course'] = 'Tout le monde dans le cours';
$string['allow_mentor_copy'] = 'Permettre aux expéditeurs d\'envoyer automatiquement un message aux tuteurs d\'un destinataire lors de l\'envoi';
$string['allow_mentor_copy_help'] = 'Dans le cas contraire, cette option ne sera pas visible pour les expéditeurs et les mentors ne seront jamais copiés. Si oui, l\'expéditeur aura la possibilité de choisir par message. S\'il est forcé, cette option sera forcée sans possibilité de désélection.';
$string['allowstudents'] = 'Autoriser les étudiants à utiliser Courriel';
$string['allowstudents_desc'] = 'Permettre aux étudiants d\'utiliser Quickmail. Si vous choisissez "Jamais", le bloc ne peut pas être configuré pour autoriser les étudiants à accéder au niveau du cours.';
$string['alternate'] = 'Adresses secondaires';
$string['alternate_activated'] = 'Le courriel alternatif {$a} peut maintenant être utilisé !';
$string['alternate_already_confirmed'] = 'Ce courriel a déjà été confirmé.';
$string['alternate_availability'] = 'Qui peut envoyer à partir de ce courriel ?';
$string['alternate_availability_course'] = 'Tous les rôles autorisés dans {$a->courseshortname}';
$string['alternate_availability_only'] = 'Seulement moi, dans {$a->courseshortname} seulement';
$string['alternate_availability_user'] = 'Seulement moi, dans tous les cours.';
$string['alternate_body'] = '<p>
{$a->fullname} a ajouté {$a->address} comme adresse secondaire pour le cours {$a->course}.
</p>

<p>
Ce message a pour but de vérifier la validité de cette adresse, et si le destinataire a les droits nécessaires sur la plateforme.
</p>

<p>
Si vous souhaitez terminer le processus de validation, merci de cliquer sur le lien suivant :<br> {$a->url}.
</p>

<p>
Si le contenu de ce message n\'a aucun sens pour vous, c\'est qu\'il a été envoyé par erreur. Merci d\'ignorer simplement ce message.
</p>

Cordialement.';
$string['alternate_confirmation_email_resent'] = 'Le courriel de confirmation a été renvoyé !';
$string['alternate_confirmed'] = 'Confirmé';
$string['alternate_created'] = 'Le courriel d\'envoi alternatif a été créé avec succès !';
$string['alternate_delete'] = 'Supprimer l\'adresse de remplacement';
$string['alternate_delete_confirm'] = 'Voulez-vous vraiment supprimer définitivement votre courriel alternatif ?';
$string['alternate_deleted'] = 'Votre courriel d\'envoi alternatif a été supprimé.';
$string['alternate_email_not_found'] = 'Impossible de trouver ce courriel alternatif.';
$string['alternate_invalid_token'] = 'Jeton de confirmation non valable.';
$string['alternate_new'] = 'Ajouter des adresses secondaires';
$string['alternate_owner_must_confirm'] = 'Vous devez être le propriétaire du courriel pour confirmer.';
$string['alternate_owner_must_delete'] = 'Vous devez être le propriétaire du courriel à supprimer.';
$string['alternate_resend_confirmation'] = 'Renvoyer le courriel de confirmation';
$string['alternate_subject'] = 'Vérification de l\'adresse secondaire';
$string['alternate_waiting'] = 'En attente';
$string['attached_files'] = 'Fichiers joints ({$a})';
$string['attachments'] = 'Pièces jointes';
$string['back_to_course'] = 'Retour au cours';
$string['back_to_mypage'] = 'Retour à mon Tableau de bord';
$string['backup_block_configuration'] = 'Sauvegarde des paramètres de configuration au niveau du bloc Quickmail (tels que [Autoriser les étudiants à utiliser Quickmail])';
$string['backup_history'] = 'Inclure l\'historique Courriel';
$string['body'] = 'Corps';
$string['broadcast'] = 'Composer un message d\'administration';
$string['cachedef_qm_controller_store'] = 'Cache pour les instances de contrôle.';
$string['cachedef_qm_event_notif_last_fired_at'] = 'Cache pour l\'horodatage de la dernière notification d\'événement.';
$string['cachedef_qm_msg_addl_email_count'] = 'Les messages en cache comptent les courriels supplémentaires.';
$string['cachedef_qm_msg_attach_count'] = 'Les pièces jointes aux messages mis en cache comptent.';
$string['cachedef_qm_msg_deliv_count'] = 'La livraison de messages en cache compte.';
$string['cachedef_qm_msg_recip_count'] = 'Le destinataire du message en cache compte.';
$string['compose'] = 'Composer un message de cours';
$string['condition_grade_greater_than'] = 'Note supérieure à';
$string['condition_grade_less_than'] = 'Note inférieure à';
$string['condition_summary_reminder_course_grade_range'] = 'Tous ceux qui ont une note comprise entre {$a->grade_greater_than} et {$a->grade_less_than}';
$string['condition_summary_reminder_course_non_participation'] = 'Tous ceux qui n\'ont pas accédé au cours depuis {$a->time_amount} {$a->time_unit}';
$string['could_not_duplicate'] = 'Il n\'a pas été possible de reproduire ce brouillon. Veuillez réessayer.';
$string['course_required'] = 'Un cours est nécessaire.';
$string['coursealternate_not_allowed'] = 'Vous n\'êtes pas en mesure de partager ce courriel alternatif dans ce cours.';
$string['courseferpa'] = 'Respecte le mode du cours';
$string['courseneveraccessed'] = 'Jamais accédé';
$string['create_new'] = 'Créer un nouveau';
$string['create_notification'] = 'Créer une notification';
$string['create_notification_message'] = 'Créer {$a->model} {$a->type} Message de notification';
$string['create_notification_message_description'] = 'Créez maintenant le message que vous souhaitez recevoir lors de l\'envoi de cette notification.';
$string['created'] = 'Créé';
$string['critical_error'] = 'Erreur critique';
$string['default_message_type'] = 'Méthode préférée d\'envoi des messages';
$string['default_message_type_configuration'] = 'Ce paramètre sera la méthode d\'envoi par défaut pour toute personne composant un message dans le cadre de ce cours. L\'expéditeur pourra passer outre lors de la composition du message.';
$string['default_message_type_configuration_help'] = 'Ce paramètre sera la méthode d\'envoi par défaut pour toute personne composant un message dans le cadre de ce cours. L\'expéditeur pourra passer outre lors de la composition du message.';
$string['default_message_type_desc'] = 'Envoyez vos messages sous forme de messages Moodle ou de courriel traditionnel.';
$string['delete_draft_confirm_message'] = 'Voulez-vous vraiment supprimer définitivement votre projet de message ?';
$string['delete_draft_modal_title'] = 'Supprimer le brouillon';
$string['delete_signature'] = 'Supprimer la signature';
$string['delete_signature_confirm_message'] = 'Voulez-vous vraiment effacer définitivement votre signature ?';
$string['delete_signature_modal_title'] = 'Supprimer la signature';
$string['deleted'] = 'Supprimé';
$string['download_file_content'] = 'Télécharger le contenu du fichier';
$string['downloads'] = 'Vous devez être connecté pour accéder aux pièces jointes';
$string['downloads_desc'] = 'Ce paramètre détermine si les pièces jointes sont disponibles uniquement pour les utilisateurs Moodle connectés';
$string['draft_no_record'] = 'Je n\'ai pas pu trouver ce projet de message.';
$string['drafted'] = 'Copié en brouillon';
$string['drafts'] = 'Voir les brouillons';
$string['duplicate'] = 'Dupliquer';
$string['duplicate_draft_confirm_message'] = 'Voulez-vous vraiment faire une copie du projet ?';
$string['duplicate_draft_modal_title'] = 'Dupliquer le projet de message';
$string['edit_conditions'] = 'Conditions d\'édition';
$string['edit_event_details'] = 'Modifier les détails de l\'événement';
$string['edit_message'] = 'Modifier le message';
$string['edit_notification'] = 'Notification de modification';
$string['edit_schedule'] = 'Programme d\'édition';
$string['email_profile_fields'] = 'Champs de profil du courriel';
$string['email_profile_fields_desc'] = 'Ces champs de profil seront automatiquement envoyés par courriel s\'ils sont définis pour l\'utilisateur destinataire.';
$string['enable_notification'] = 'Activer la notification';
$string['eventalternateemailadded'] = 'Ajout d\'un courriel alternatif';
$string['eventalternateemailadded_desc'] = 'L\'utilisateur avec l\'identifiant {$a->user_id} a ajouté un autre courriel : {$a->email}';
$string['excluded_ids_label'] = 'Exclure';
$string['excluded_recipients_desc'] = 'Qui ne doit PAS recevoir ce message ?';
$string['failed_recipients'] = 'Bénéficiaires défaillants ou en attente';
$string['failed_recipients_promise'] = 'Nous continuerons à essayer d\'envoyer à ces destinataires jusqu\'à ce que le message ait été envoyé avec succès.';
$string['ferpa'] = 'Mode FERPA';
$string['ferpa_desc'] = 'Permet au système de prendre en compte soit le réglage des groupes du cours, soit en ignorant les réglage des groupes mais en faisant des groupes séparés, soit en ignorant la notion de groupe du cours.
<br>FERPA : Family Educational Rights and Privacy Act';
$string['found_filtered_users'] = 'Utilisateur(s) trouvé(s) {$a}';
$string['from_email'] = 'Adresse de courriel de l\'expéditeur';
$string['from_email_help'] = 'L\'adresse de courriel à partir de laquelle ce message sera envoyé. Vous pouvez ajouter d\'autres adresses par le biais du menu du bloc de la page du cours.';
$string['here'] = 'ici';
$string['included_ids_label'] = 'Vers';
$string['included_recipients_desc'] = 'Qui doit recevoir ce message ?';
$string['invalid_additional_email'] = 'Le courriel supplémentaire "{$a}" que vous avez saisi n\'est pas valide';
$string['invalid_additional_emails_validation'] = 'Certains des courriels supplémentaires que vous avez saisis n\'étaient pas valides.';
$string['invalid_availability'] = 'Valeur de disponibilité non valable.';
$string['invalid_condition_grade_greater_than'] = 'Note invalide supérieure à la valeur.';
$string['invalid_condition_grade_less_than'] = 'Note invalide inférieure à la valeur.';
$string['invalid_condition_time_amount'] = 'Durée de la condition non valable.';
$string['invalid_condition_time_unit'] = 'Unité de temps non valable en l\'état.';
$string['invalid_custom_data_delimiters'] = 'Les codes de substitution du corps du message ne sont pas correctement formatés.';
$string['invalid_custom_data_key'] = 'La clé de données personnalisée "{$a}" n\'est pas autorisée.';
$string['invalid_custom_data_not_allowed'] = 'Les codes de substitution personnalisés ne sont pas autorisés pour ce message.';
$string['invalid_email'] = 'Adresse courriel invalide';
$string['invalid_notification_model'] = 'Sélection d\'un modèle de notification non valable.';
$string['invalid_notification_type'] = 'Le type de notification doit être "Rappel" ou "Événement".';
$string['invalid_schedule_time_amount'] = 'Délai non valable pour l\'horaire.';
$string['invalid_schedule_time_unit'] = 'Unité de temps non valable pour l\'horaire.';
$string['invalid_send_method'] = 'Cette méthode d\'envoi n\'est pas autorisée.';
$string['invalid_time_amount'] = 'Durée non valable.';
$string['invalid_time_relation'] = 'Doit être "avant" ou "après".';
$string['invalid_time_unit'] = 'Unité de temps non valable.';
$string['is_enabled'] = 'Activé';
$string['last_run_at'] = 'Prochain démarrage';
$string['last_updated'] = 'Dernière mise à jour';
$string['manage_alternates'] = 'Courriels alternatifs';
$string['manage_drafts'] = 'Voir les brouillons';
$string['manage_signatures'] = 'Mes signatures';
$string['mentor_copy'] = 'Envoyer des copies aux tuteurs des bénéficiaires ?';
$string['mentor_copy_help'] = 'Si vous êtes sélectionné, les tuteurs de vos destinataires recevront une copie du message.';
$string['mentor_copy_message_prefix'] = '<p><strong>Vous recevez ce message parce que vous êtes désigné comme tuteur de</strong> : {$a}. Vous trouverez ci-dessous une copie du message qui a également été envoyé à votre élève.</p>';
$string['mentor_copy_subject_prefix'] = '[Copie pour le tuteur]';
$string['mentors_copied'] = 'Envoyer des copies aux tuteurs';
$string['message_deleted'] = 'Message effacé';
$string['message_details'] = 'Détails du message';
$string['message_no_record'] = 'Impossible de trouver ce message';
$string['message_not_found'] = 'Impossible de trouver ce message';
$string['message_preview'] = 'Prévisualisation du message';
$string['message_queued'] = 'Message dont l\'envoi est prévu';
$string['message_sent_asap'] = 'Le message sera envoyé prochainement';
$string['message_sent_now'] = 'Message envoyé';
$string['message_type'] = 'Envoyer des messages courriel en tant que';
$string['message_type_available_all'] = 'L\'expéditeur ne fixe aucune restriction.';
$string['message_type_available_email'] = 'Restreindre au courrier électronique traditionnel uniquement';
$string['message_type_available_message'] = 'Restreindre aux seuls messages Moodle';
$string['message_type_desc'] = 'Permet d\'envoyer des messages Quickmail sous forme de message Moodle, de courrier électronique traditionnel ou selon les préférences de l\'expéditeur.';
$string['message_type_email'] = 'Courriel';
$string['message_types_available'] = 'Restrictions message type';
$string['message_types_available_desc'] = 'Restreindre les messages Quickmail à envoyer sous forme de messages Moodle, de courriels traditionnels ou selon les préférences de l\'expéditeur.';
$string['message_unqueued'] = 'Message non planifié';
$string['messageprovider:quickmessage'] = 'Message de Quickmail';
$string['migrate'] = 'Migrer les données';
$string['migrate_legacy_data_task'] = 'Migrer l\'historique des données de Quickmail v1 vers v2';
$string['migration_chunk_size'] = 'Taille des fragments de migration';
$string['migration_chunk_size_desc'] = 'Nombre d\'enregistrements qui devraient être traités par la tâche de migration des données héritées à chaque fois qu\'elle est exécutée, si elle est activée.';
$string['missing_body'] = 'Corps du message manquant !';
$string['missing_email'] = 'Adresse courriel manquante !';
$string['missing_firstname'] = 'Prénom manquant !';
$string['missing_lastname'] = 'Nom manquant !';
$string['missing_notification_name'] = 'Nom de la notification manquante. !';
$string['missing_subject'] = 'Il manque le sujet !';
$string['ms_alternate'] = 'Courriels supplémentaires';
$string['ms_compose'] = 'Écrire';
$string['ms_config'] = 'Configurer';
$string['ms_create_notification'] = 'Créer une notification';
$string['ms_drafts'] = 'Brouillons';
$string['ms_queued'] = 'Prévu';
$string['ms_sent'] = 'Courriels envoyés';
$string['ms_signatures'] = 'Mes signatures';
$string['must_be_draft_to_duplicate'] = 'Le message doit être un brouillon à dupliquer.';
$string['must_be_owner_to_duplicate'] = 'Désolé, ce brouillon ne vous appartient pas et ne peut être reproduit.';
$string['mute_time_summary'] = 'Temps mort';
$string['mute_time_unit'] = 'Temps mort';
$string['mute_time_unit_help'] = 'Un délai facultatif avant l\'envoi automatique de cette notification.';
$string['never'] = 'Jamais';
$string['next_run_at'] = 'Prochaine exécution';
$string['no_alternates'] = 'Pas d\'adresse secondaire de trouvée pour {$a->fullname}. Poursuivre la création.';
$string['no_drafts'] = 'Vous n\'avez pas de brouillon.';
$string['no_excluded_recipients'] = 'Aucun destinataire exclu';
$string['no_included_recipients'] = 'Aucun destinataire inclus';
$string['no_included_recipients_validation'] = 'Vous devez sélectionner au moins un destinataire.';
$string['no_notifications'] = 'Vous n\'avez pas créé de notifications.';
$string['no_queued'] = 'Vous n\'avez pas de messages programmés.';
$string['no_sents'] = 'Vous n\'avez pas d\'historique de messages envoyés.';
$string['no_signatures_create'] = 'Vous n\'avez pas de signature. {$a}.';
$string['noferpa'] = 'Pas de respect de groupe';
$string['notification_already_sent'] = 'Cette notification a déjà été envoyée au moins une fois.';
$string['notification_conditions_description'] = 'Précisez les conditions de cette notification. Tout destinataire qui remplit ces conditions sera notifié.';
$string['notification_created'] = 'Notification créée';
$string['notification_deleted'] = 'Notification supprimée';
$string['notification_is_enabled'] = 'Notification activée';
$string['notification_is_enabled_help'] = 'Si elle est activée, cette notification sera active, sinon, la notification sera désactivée jusqu\'à ce que vous l\'activiez.';
$string['notification_model'] = 'Modèle de notification';
$string['notification_model_event_course_entered'] = 'Cours suivis';
$string['notification_model_event_course_entered_description'] = 'Avertir un participant au cours lorsqu\'il accède au cours pour la première fois.';
$string['notification_model_reminder_course_grade_range'] = 'Gamme de notes des cours';
$string['notification_model_reminder_course_grade_range_condition_description'] = 'Précisez la fourchette de notes. Si un participant à un cours se situe actuellement dans cette fourchette, il en sera informé.';
$string['notification_model_reminder_course_grade_range_description'] = 'Aviser les participants aux cours qui ont actuellement une note dans la fourchette donnée.';
$string['notification_model_reminder_course_non_participation'] = 'Non-participation au cours';
$string['notification_model_reminder_course_non_participation_condition_description'] = 'Précisez le temps écoulé depuis le dernier accès au cours. Si un participant au cours n\'a pas accédé au cours dans ce laps de temps, il en sera informé.';
$string['notification_model_reminder_course_non_participation_description'] = 'Avertir les participants au cours qui n\'ont pas accédé au cours dans un laps de temps donné.';
$string['notification_name'] = 'Titre';
$string['notification_name_help'] = 'Une brève description de cette notification pour vous aider à l\'identifier facilement parmi les autres notifications que vous avez pu mettre en place.';
$string['notification_name_too_long'] = 'Le nom de la notification doit comporter 40 caractères maximum.';
$string['notification_not_found'] = 'Impossible de trouver cette notification.';
$string['notification_not_updated'] = 'Notification mise à jour.';
$string['notification_review'] = 'Révisez votre notification';
$string['notification_schedule'] = 'Calendrier';
$string['notification_type'] = 'Type de notification';
$string['notification_type_event'] = 'Événement';
$string['notification_type_event_description'] = 'Un message automatisé peut-être  envoyé suite à un événement spécifique. Les événements peuvent être utilisés pour avertir les destinataires que des activités ont été réalisées, que des devoirs ont été soumis ou notés, et plus encore.';
$string['notification_type_reminder'] = 'Rappel';
$string['notification_type_reminder_description'] = 'Un message récurrent qui sera automatiquement envoyé selon un calendrier précis. Les rappels peuvent être utilisés pour informer les destinataires des activités à venir, des dates d\'échéance, de la participation en classe, etc.';
$string['notification_updated'] = 'Notification mise à jour.';
$string['notifications_enabled'] = 'Activer les notifications';
$string['notifications_enabled_desc'] = 'Permettre aux cours de créer des notifications automatisées, y compris des rappels et des réponses aux événements.';
$string['notified_by'] = 'Notifié par';
$string['open'] = 'Ouvert';
$string['open_broadcast'] = 'Composer un message';
$string['open_compose'] = 'Composer un message';
$string['overwrite_history'] = 'Écraser l\'historique Courriel';
$string['pending_recipients'] = 'Bénéficiaires en attente';
$string['pending_recipients_promise'] = 'Ces destinataires sont actuellement en train de recevoir des messages, revenez plus tard pour voir les éventuelles défaillances.';
$string['picker_style_autocomplete'] = 'Autocomplétion';
$string['picker_style_multiselect'] = 'Multisélection';
$string['picker_style_option_title'] = 'Mon style préféré de sélection des bénéficiaires';
$string['picker_style_option_title_help'] = 'Votre interface personnelle préférée pour sélectionner les destinataires lors de la composition d\'un message.';
$string['pluginname'] = 'Courriel';
$string['prepend_class'] = 'Nom du cours dans l\'objet';
$string['prepend_class_configuration'] = 'Préfixez les informations d\'identification du cours à l\'objet du message.';
$string['prepend_class_configuration_help'] = 'Préfixez les informations d\'identification du cours à l\'objet du message.';
$string['prepend_class_desc'] = 'Ajoute l\'identification du cours dans l\'objet du message.';
$string['preview_no_body'] = '(Pas de contenu)';
$string['preview_no_subject'] = '(Pas de sujet)';
$string['profile_mentor_copy_message_prefix'] = '<p><strong>Vous recevez ce message parce que vous êtes assigné comme {$a} du destinataire prévu</strong>. Voici une copie de ce qui a été envoyé au destinataire.</p>';
$string['queue_scheduled_notifications_task'] = 'Mettre en file d\'attente les notifications Quickmail qui doivent être envoyées';
$string['queued'] = 'Prévu';
$string['queued_no_record'] = 'Impossible de trouver ce message en file d\'attente.';
$string['quickmail:addinstance'] = 'Ajouter un nouveau bloc Courriel';
$string['quickmail:allowalternate'] = 'Autoriser les utilisateurs à ajouter une adresse secondaire pour les cours.';
$string['quickmail:allowcoursealternate'] = 'Créer d\'autres adresses électroniques à partager avec d\'autres enseignants d\'autres cours';
$string['quickmail:canconfig'] = 'Autorise les utilisateurs à configurer l\'instance de Courriel.';
$string['quickmail:cansend'] = 'Autorise les utilisateurs à envoyer des messages par le biais de Courriel.';
$string['quickmail:createnotifications'] = 'Créer des notifications automatiques dans Quickmail';
$string['quickmail:myaddinstance'] = 'Ajouter un nouveau bloc Courriel à ma page';
$string['quickmail:viewgroupusers'] = 'Voir tous les utilisateurs de chaque groupe';
$string['receipt'] = 'Recevoir une copie';
$string['receipt_configuration'] = 'Envoyez un courriel de confirmation à l\'expéditeur du message lorsque celui-ci a été envoyé. Ce paramètre sera la préférence par défaut de l\'expéditeur.';
$string['receipt_configuration_help'] = 'Envoyez un courriel de confirmation à l\'expéditeur du message lorsque celui-ci a été envoyé. Ce paramètre sera la préférence par défaut de l\'expéditeur.';
$string['receipt_email_body'] = '<p>Votre message a été envoyé ! Vous pouvez consulter les détails de ce message envoyé {$a->sent_message_link}.</p> <p> <strong>Résumé des détails du message :</strong><br><br> <strong>Cours :</strong> {$a->course_name}<br><br> <strong>Sujet du message :</strong> {$a->subject}<br><br> <strong>Destinataires :</strong> {$a->recipient_count}<br><br> <strong>Destinataires additionnels :</strong> {$a->addition_emails_string}<br><br> <strong>Envoyé aux tuteurs :</strong> {$a->sent_to_mentors}<br><br> <strong>Fichiers joints :</strong> {$a->attachment_count}<br><br> <strong>Corps du message :</strong> </p> {$a->message_body}';
$string['receipt_help'] = 'Recevoir une copie du message envoyé';
$string['recipients'] = 'Destinataires';
$string['redirect_back_from_message_detail_message_deleted'] = 'Le message que vous tentez de consulter a été supprimé.';
$string['redirect_back_from_message_detail_no_access'] = 'Vous ne pouvez pas voir ce message.';
$string['redirect_back_to_course_from_message_after_duplicate'] = 'Votre message a été dupliqué avec succès.';
$string['redirect_back_to_course_from_message_after_save'] = 'Votre brouillon a bien été enregistré';
$string['redirect_back_to_course_from_notifications_not_enabled'] = 'Les notifications Quickmail sont désactivées pour votre site.';
$string['reset_success_message'] = 'Les paramètres par défaut de Quickmail ont été restaurés !';
$string['restore_default_confirm_message'] = 'Voulez-vous vraiment rétablir les paramètres Quickmail par défaut pour ce cours ?';
$string['restore_default_modal_title'] = 'Restaurer la configuration par défaut';
$string['restore_history'] = 'Restaurer l\'historique Courriel';
$string['save'] = 'Enregistrement';
$string['save_draft'] = 'Enregistrer le brouillon';
$string['save_signature'] = 'Enregistrer la signature';
$string['schedule_begin_at'] = 'Date de début';
$string['schedule_end_at'] = 'Date de fin';
$string['scheduled_time'] = 'Heure programmée';
$string['select_allowed_user_fields'] = 'Champs de données utilisateur pris en charge';
$string['select_allowed_user_fields_desc'] = 'Les expéditeurs pourront faire référence aux champs sélectionnés pour rendre le contenu du message dynamique et spécifique au destinataire. Ex : "[:firstname:]"';
$string['select_message_type'] = 'Envoyer le message en tant que';
$string['select_notification_model'] = 'Sélectionnez {$a} Modèle de notification';
$string['select_signature_for_edit'] = 'Sélectionnez la signature à modifier';
$string['selectable_roles'] = 'Sélectionnez les rôles';
$string['selectable_roles_configuration'] = 'Ces rôles pourront être sélectionnés lors de la composition d\'un message.';
$string['selectable_roles_configuration_help'] = 'Ces rôles pourront être sélectionnés lors de la composition d\'un message.';
$string['selectable_roles_desc'] = 'Ces rôles pourront être sélectionnés lors de la composition d\'un message. Ce paramètre peut être modifié par la configuration du cours.';
$string['send_all_ready_messages_task'] = 'Envoyer tous les messages Quickmail programmés';
$string['send_as_tasks'] = 'Envoyer tous les messages en tâche de fond.';
$string['send_as_tasks_help'] = 'Si cette option est sélectionnée, tous les messages seront envoyés de manière asynchrone via une tâche cron. Sinon, l\'envoi sera immédiat.';
$string['send_at'] = 'Envoyer à';
$string['send_message'] = 'Envoyer le message';
$string['send_now'] = 'Envoyer maintenant';
$string['send_now_scheduled_confirm_message'] = 'Voulez-vous vraiment ignorer le calendrier et envoyer le message immédiatement ?';
$string['send_now_threshold'] = 'Envoyer un message maintenant';
$string['send_now_threshold_desc'] = 'Forcer l\'envoi immédiat d\'un message non programmé si le nombre de destinataires est égal ou inférieur à ce nombre, même si le bloc est configuré pour envoyer des messages en tâche de fond. La valeur 0 ignore ce paramètre.';
$string['send_receipt_subject_addendage'] = 'Message envoyé';
$string['send_schedule'] = 'Calendrier d\'envoi';
$string['sending'] = 'Envoi';
$string['sent'] = 'Envoyé';
$string['sent_at'] = 'Envoyé à';
$string['sent_messages'] = 'Historique des messages envoyés';
$string['set_event_details'] = 'Définir {$a->model} Détails des notifications d\'événements';
$string['set_event_details_description'] = 'Précisez les options supplémentaires concernant l\'événement.';
$string['set_notification_conditions'] = 'Définir {$a->model} {$a->type} Conditions de notification';
$string['set_notification_schedule'] = 'Définir {$a->model} {$a->type} Conditions de planification';
$string['set_notification_schedule_description'] = 'Précisez la fréquence d\'envoi de cette notification, la date à laquelle elle doit commencer et la date à laquelle elle doit se terminer. Si aucune date de fin n\'est spécifiée, cette notification continuera d\'être envoyée jusqu\'à ce qu\'elle soit désactivée ou supprimée.';
$string['signature'] = 'Signature';
$string['signature_signature_required'] = 'Une signature est requise.';
$string['signature_title_must_be_unique'] = 'Le titre de la signature doit être unique.';
$string['signature_title_required'] = 'Un titre de signature est requis.';
$string['status'] = 'état';
$string['strictferpa'] = 'Groupes séparés permanent';
$string['subject'] = 'Objet';
$string['success_recipients_promise'] = 'Ces destinataires ont tous reçu un message du système.';
$string['time_amount'] = 'Durée de la procédure';
$string['time_beginning'] = 'Début';
$string['time_delay_summary'] = 'Délai';
$string['time_delay_unit'] = 'Délai';
$string['time_delay_unit_help'] = 'Un délai d\'attente facultatif avant l\'envoi de la notification.';
$string['time_ending'] = 'La procédure se termine';
$string['time_every'] = 'Tous les';
$string['time_once_a'] = 'Une fois qu\'un';
$string['time_relation'] = 'Avant ou après';
$string['time_unit'] = 'Unité de temps';
$string['time_unit_day'] = 'Jour';
$string['time_unit_days'] = 'Jours';
$string['time_unit_month'] = 'Mois';
$string['time_unit_months'] = 'Mois';
$string['time_unit_week'] = 'Semaine';
$string['time_unit_weeks'] = 'Semaines';
$string['title'] = 'Titre';
$string['unqueue'] = 'Pas dans la file d\'attente';
$string['unqueue_scheduled_confirm_message'] = 'Voulez-vous vraiment décaler l\'envoi de ce message et de le sauvegarder comme un brouillon ?';
$string['unqueue_scheduled_modal_title'] = 'Message programmé non mis en file d\'attente';
$string['user_signature_deleted'] = 'Votre signature a été effacée.';
$string['validation_exception_message'] = 'Exception de validation !';
$string['view'] = 'Voir';
$string['view_message_detail'] = 'Voir les détails du message';
$string['view_queued'] = 'Voir la programmation';
$string['view_sent'] = 'Voir les messages envoyés';
