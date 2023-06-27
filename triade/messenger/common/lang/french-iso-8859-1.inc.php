<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2019 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **  Licence :       GPL (GNU Public License)         **
 **  http://opensource.org/licenses/gpl-license.php   **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger-server  **
 **                                                   **
 **  IntraMessenger is a free software.               **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/
#
#
# [EN] 
# Thanks for posts your update or new translation on the official forum :
# http://www.intramessenger.com/forum/viewforum.php?f=10
# and post it on the forum or send it by email : im-translate@theuds.com
#
#
# [FR]
# Merci de poster vos corrections ou nouvelles traductions sur le forum officiel :
# http://www.intramessenger.com/forum/viewforum.php?f=11
# et la poster sur le forum ou l'envoyer par email : im-translate@theuds.com
#
#
#
$charset = 'iso-8859-1';
$l_lang_name = "Français-";
//$left_font_family = 'verdana, arial, helvetica, geneva, sans-serif';
//$right_font_family = 'arial, helvetica, geneva, sans-serif';

# commun
$l_legende = "Légende";
$l_order_by = "Trier par :";
$l_date_format_display = "d/m/Y";
$l_time_format_display = "H:i:s";
$l_time_short_format_display = "H:i";
$l_admin_bt_delete = "Supprimer";
$l_admin_bt_erase = "Effacer";
$l_admin_bt_update = "Modifier";
$l_admin_bt_add = "Ajouter";
$l_admin_bt_create = "Créer";
$l_admin_bt_allow = "Autoriser";
$l_admin_bt_invalidate = "Désactiver";
$l_admin_bt_search = "Chercher";
$l_admin_bt_empty = "Vider";
$l_language = "Langue";
$l_country = "Pays";
$l_time_zone = "Fuseau horaire";
$l_server = "Serveur";
$l_clic_for_message = "Cliquer ici pour lui envoyer un message en tant qu&#146;administrateur";
$l_clic_on_user = "Cliquer ici pour voir la fiche détaillée de l&#146;utilisateur";
$l_man = "Homme";
$l_woman = "Femme";
$l_gender = "Genre";
$l_user_informations = "Informations utilisateur";
$l_email = "Email"; // courriel
$l_phone = "Téléphone";
$l_display_col = "Afficher les colonnes";
$l_display = "Afficher";
$l_hide = "Masquer";
$l_configure = "Configurer";
$l_captcha = "Copier ce code de sécurité";
$l_rows_per_page = "Lignes par page";
$l_KB = "Ko"; // Kilo Octets
$l_relation_option = "En relation avec l'option : ";
$l_days = "jours";
$l_day_0 = "Lundi";
$l_day_1 = "Mardi";
$l_day_2 = "Mercredi";
$l_day_3 = "Jeudi";
$l_day_4 = "Vendredi";
$l_day_5 = "Samedi";
$l_day_6 = "Dimanche";

# Languages
$l_lng['FR'] = "français";
$l_lng['GB'] = "anglais"; // EN
$l_lng['UK'] = "anglais"; // EN
$l_lng['DE'] = "allemand"; // GE
$l_lng['BR'] = "portugais brésilien";
$l_lng['PT'] = "portugais";
$l_lng['ES'] = "espagnol";
$l_lng['IT'] = "italien";
$l_lng['FI'] = "finnois";
$l_lng['RO'] = "roumain";
$l_lng['TR'] = "turkish";
$l_lng['RS'] = "serbe";
$l_lng['RU'] = "russe";
$l_lng['NL'] = "hollandais";
//$l_lng[''] = "";

# level
$c_nb_level = 5;
$c_level[0] = "Administrateur";
$c_level[1] = "Directeur";
$c_level[2] = "Chef de service";
$c_level[3] = "Employé";
$c_level[4] = "Invité";
// PDG, Directeur, Directeur de filiale, Administrateur, Superviseur, Chef de centre, Chef de service, Chef de projet, Chef de secteur, Chef, Employé, Invité.

#start
$l_start_cannot_authenticate = "Authentification impossible";
$l_start_unknow_user = "nom d&#146;utilisateur inconnu";
$l_start_wait_valid = "compte verrouillé (en attente), veuillez patienter que l`administrateur le débloque.";
$l_start_contact_admin_check = "contactez votre administrateur pour qu`il confirme votre changement de configuration.";
$l_start_password = "mot de passe incorrect.";
$l_start_max_users = "Démarrage impossible : ajout nouvel utilisateur impossible : nombre maximum atteint.";
$l_start_no_find_iduser = "identifiant utilisateur non trouvé.";
$l_start_short_username = "Authentification impossible : utilisateur non indiqué.";
$l_start_username_forbid = "Authentification refusée : nom d'utilisateur reservé (interdit).";
$l_start_username_forbid_by_admin = "Authentification refusée : nom d'utilisateur interdit par l'administrateur.";
$l_start_version_missing = "Authentification refusée : version trop ancienne, vous devez mettre à jour le logiciel.";
$l_start_waiting_valid = "Utilisateur(s) en attente de validation de l'administrateur...";

# menu
$l_menu_list = "Liste";
$l_menu_index = "Sommaire";
$l_menu_dash_board = "Tableau de bord";
$l_menu_currently = "Actuellement"; // En cours
$l_menu_list_sessions = "Sessions";
$l_menu_conference = "Conférences";
$l_menu_list_users = "Utilisateurs";
$l_menu_list_users_ip = "Même adresse IP";
$l_menu_list_users_double = "Même poste";
$l_menu_users_by_country = "Utilisateurs par pays";
$l_menu_list_contact = "Contacts utilisateurs";
$l_menu_list_conference_list = "Liste des conférences";
$l_menu_list_group = "Groupes";
$l_menu_list_group_list = "Liste des groupes";
$l_menu_group_add_member = "Ajouter un membre";
$l_menu_ban = "Bannissement";
$l_menu_ban_user = "Utilisateurs";
$l_menu_ban_ip = "Adresses IP";
$l_menu_ban_pc = "Ordinateurs";
$l_menu_options = "Options";
$l_menu_avatars = "Avatars";
$l_menu_messagerie = "Messagerie";
$l_menu_statistics = "Statistiques";
$l_menu_log = "Journaux serveur";
$l_menu_backup = "Sauvegarde";
$l_menu_donate = "Faire un don";
$l_menu_donate_info = "Tous les dons sont les bienvenus";
$l_menu_need_change_admin_dir = "Attention, il est impératif de renommer le répertoire <B>/admin/</B> sous un autre nom (pour que les utilisateurs puissent se connecter) !";
$l_menu_need_delete_install_dir = "Attention, il est recommandé de supprimer le répertoire <B>/install/</B> (sauf pour les mises à jour)";
$l_menu_maintenance_mode_on = "Attention, le mode maintenance est actif, les utilisateurs ne peuvent actuellement pas communiquer (ni se connecter).";
//$l_menu_need_htaccess = "Attention, il est vivement recommandé d'ajouter une protection htaccess dans le répertoire d'administration !";
$l_menu_need_htaccess = "Attention, l'authentification des administrateurs est désactivée (_ACP_PROTECT_BY_HTACCESS) <BR/> il est donc indispensable d'ajouter une protection htaccess dans le répertoire d'administration !";
$l_menu_pass_root_empty = "Votre fichier de configuration fait référence à l'utilisateur root sans mot de passe, ce qui correspond à la valeur par défaut de MySQL.  Votre serveur MySQL est donc ouvert aux intrusions, vous devriez corriger ce problème de sécurité.";
$l_menu_need_reg = "Il est recommandé d'ajouter le fichier im_setup.reg";
$l_menu_no_javascript = "JavaScript non activé, cliquer ici pour afficher le menu correctement";
$l_menu_no_javascript_info = "JavaScript non activé, impossible d'afficher le menu en haut...";
$l_menu_customize = "Personnalisation";
$l_menu_customize_info = "Demander une version personnalisée du client IntraMessenger";
$l_menu_bookmarks = "Marques pages";
$l_menu_list_roles_list = "Liste des rôles";
$l_menu_messagerie_instant = "Directe";
$l_menu_messagerie_emails = "Par emails";
$l_menu_logout = "Déconnexion";
$l_menu_acp_auth = "Administrateurs";
$l_menu_manage = "Gestion";

#
$l_pg_result = "Résultats";
$l_pg_show_result = "Affichage des résultats";
$l_pg_prev_page = "Page précédente";
$l_pg_next_page = "Page suivante";
$l_pg_first = "Première";
$l_pg_first_page = "Première page";
$l_pg_last = "Dernière";
$l_pg_last_page = "Dernière page";
$l_pg_all = "Toutes les pages";
$l_pg_to = "à";
$l_pg_of = "sur";

# index
$l_index_welcome = "Bienvenue dans l'interface d'administration (<acronym title='Admin Control Panel'>ACP</acronym>)";
$l_index_can_cfg = "Vous pouvez configurer les options dans le fichier";
$l_index_can_lng = "comme le langage";
$l_index_actualy = "actuellement";
$l_index_chg = "en changeant";
$l_index_find_doc = "Vous trouverez la documentation d'installation dans";
$l_index_chk_opt = "Vous pouvez vérifier les options et la configuration sur";
$l_index_after_upd_chk = "Après chaque mise à jour, vérifier impérativement la configuration via le menu ";
$l_index_waiting_valid = "En attente de validation"; // Utilisateurs en attente de validation par l'administrateur
$l_index_ready_users = "Utilisateurs prêts";
$l_index_today_creat_users = "Utilisateurs créés aujourd'hui";
$l_index_today_sessions = "Sessions simultanées aujourd'hui";
$l_index_last_valid_username = "Dernier utilisateur créé :";
$l_index_pending_avatars = "Avatars en attente de validation";
$l_index_records = "Records";
$l_index_full_list = "Liste complète";
$l_index_users_per_day = "Utilisateurs par jours";
$l_index_created_users_per_day = "Utilisateurs créés par jours";
$l_index_messages_per_day = "Messages par jours";
$l_index_leave_users = "Utilisateurs ayant quitté le serveur";
$l_index_soon_dashboard_here = "Le tableau de bord s'affichera ici, une fois l'installation complètement terminée, et le mode maintenance désactivé.";
$l_old_files_to_delete = "Des anciens fichiers existent encore et sont à supprimer";
$l_index_shoutbox_pending = "Message(s) dans la shoutbox en attente de validation";
$l_index_shoutbox_nb_msg = "Nombre de messages"; // (publiés)
$l_index_shoutbox_nb_msg_wait = "Messages en attente d'approbation";
$l_index_shoutbox_nb_msg_rejects = "Messages rejetés";
$l_index_shoutbox_nb_user_lock_rejects = "Utilisateurs verrouillés (suite refus)";
$l_index_shoutbox_nb_user_lock_votes = "Utilisateurs verrouillés (suite votes)";
$l_index_shoutbox_nb_votes = "Nombre de votes";
$l_index_shoutbox_best_author = "Meilleur auteur";
$l_index_users_pending_group = "Utilisateurs en cours d'inscription groupes";
$l_index_trend_7_days = "Tendance 7 derniers jours (en comparaison sur les 60 derniers jours)";
$l_index_users_recent_activity = "Utilisateurs avec activité récente <SMALL>(30 jours<SMALL>)</SMALL>";
$l_index_checking_version = "Vérification version";
$l_index_server_up_to_date = "La version du serveur est à jour";
$l_index_new_server_version_available = "Une nouvelle version (serveur) est disponible !";
$l_index_cannot_check_version = "Impossible de vérifier (sur internet) la disponibilité d'une nouvelle version...";
$l_index_dashboard_empty = "Après quelques jours d'utilisation, le tableau de bord affichera plus d'informations...";
$l_index_bookmarks_pending = "Marques pages en attente de validation";
$l_index_most_connected = "Le plus connecté";
$l_index_share_file_pending = "Fichiers en attente de validation";
$l_index_share_file_trash = "Fichiers en corbeille"; // $nb_file_share_trash
$l_index_share_file_alert = "Fichiers signalés (à traiter)";
$l_index_share_file_download = "Téléchargements"; // Fichiers téléchargés
$l_index_backup_file = "Sauvegardes";
$l_index_backup_file_users = "Utilisateurs avec sauvegarde";
$l_index_files_workspace = "Espace de stockage utilisé (en Mo)";

# admin options screen
$l_admin_options_title = "Liste des options";
$l_admin_options_title_2 = "Conseil";
$l_admin_options_update = "Modifier les options";
$l_admin_options_bt_update = "Enregistrer les options";
$l_admin_options_more = "Afficher plus d&#146;options";
$l_admin_options_title_table_2 = "Splashscreen optionnel au démarrage (pour internet principalement)";
$l_admin_options_col_option = "Option";
$l_admin_options_col_value = "Valeur";
$l_admin_options_col_comment = "Commentaire";
$l_admin_options_col_description = "Description";
$l_admin_options_general_options = "Options générales";
$l_admin_options_general_options_short = "Générales";
$l_admin_options_maintenance_mode = "Mode maintenance : les utilisateurs ne peuvent pas communiquer, ni se connecter";
$l_admin_options_is_usernamePC = "Pseudo forcé au nom de session Windows. Si non activée : choix du pseudo par l'utilisateur";
$l_admin_options_auto_add_user = "Les nouveaux utilisateurs sont automatiquement ajoutés";
$l_admin_options_quick_register = "Inscription rapide nécessaire avant ajout automatique de nouveaux utilisateurs";
$l_admin_options_need_admin_after_add = "Les utilisateurs automatiquement ajoutés sont à valider par l'administrateur";
$l_admin_options_need_admin_if_chang_check = "Les utilisateurs qui changent de PC sont à valider par l'administrateur";
$l_admin_options_log_session_open = "Archiver dans le journal d'évènements (log) les ouvertures de sessions";
$l_admin_options_password_user = "Forcer l'utilisation d'un mot de passe pour chaque utilisateur";
$l_admin_options_password_for_private_server = "Si vide, le serveur est publique, sinon, c'est le mot de passe d'authentification des postes.";
$l_admin_options_nb_max_user = "Nombre maxi d'utilisateurs inscrits (0 : illimité)";
$l_admin_options_nb_max_session = "Nombre maxi de sessions (utilisateurs connectés simultanément) (0 : illimité)";
$l_admin_options_nb_max_contact_by_user = "Nombre maxi de contacts par utilisateurs (0 : illimité)";
$l_admin_options_del_user_after_x_days_not_use = "Comptes périmés (pour suppression par l'admin) si non utilisés pendant x jours";
$l_admin_options_force_away = "Forcer l'état 'absent' lorsque l'économiseur d'écran est actif";
$l_admin_options_col_name_hide = "Masquer la colonne nom/fonction";
$l_admin_options_col_name_default_active = "Si non masquée, afficher par défaut la colonne nom/fonction";
$l_admin_options_allow_invisible = "Autoriser d'être invisible (caché si en ligne) pour certains contacts";
$l_admin_options_can_change_contact_nickname = "Autoriser le renommage de ses contacts";
$l_admin_options_allow_change_contact_list = "Autoriser la gestion de sa liste de contacts et l'accès à l'alarme";
$l_admin_options_allow_change_options = "Autoriser la gestion de ses options et l'accès à l'alarme";
$l_admin_options_allow_change_profile = "Autoriser la gestion de son profil";
$l_admin_options_crypt_msg = "Chiffrage (de niveau élevé) des messages échangés";
$l_admin_options_censor_messages = "Censurer les messages échangés (si chiffrage non activé) : <I>/common/config/censure.txt</I>";
$l_admin_options_log_messages = "Archiver les messages échangés (si chiffrage non activé)  : pour écoles.";
$l_admin_options_site_url = "URL (adresse) du site";
$l_admin_options_site_title = "Titre du site";
$l_admin_options_missing_option = "Option(s) manquante(s) dans";
$l_admin_options_conf_file = "le fichier de configuration";
$l_admin_options_flag_country = "Afficher le drapeau du pays de l'adresse IP (internet uniquement)";
$l_admin_options_legende_empty = "option non activée"; //  (vide)
$l_admin_options_legende_not_empty = "option activée"; //  (non vide)
$l_admin_options_legende_up2u = "Votre choix";
$l_admin_options_special_options = "Options spéciales";
$l_admin_options_special_modes = "Modes spécifiques";
$l_admin_options_normal_mode = "Chacun ne voit que ses contacts (validés)";
$l_admin_options_opencommunity = "Tout le monde voit tout le monde, sans s'ajouter à la liste des contacts (ex: écoles, cyber café...)";
$l_admin_options_groupcommunity = "Tout le monde peut voir (uniquement) les personnes de son (ses mêmes) groupe(s)";
$l_admin_options_opengroupcommunity = "Tout le monde voit tout le monde, affichage par groupes";
$l_admin_options_statistics = "Enregistrer et afficher (dans l'interface d'admin) les statistiques";
$l_admin_options_info_1 = "Si vides, pas de splashscreen au démarrage des clients";
$l_admin_options_info_2 = "Vous devriez activer (au moins) une de ces deux options";
$l_admin_options_info_2b = "Vous devriez activer une de ces options";
$l_admin_options_info_3 = "Impossible d'archiver et crypter les messages (choisir seulement une option) !";
$l_admin_options_info_4 = "Impossible d'utiliser simultanément les 2 modes : group et open community !";
$l_admin_options_info_5 = "Activation inutile pour cette option";
$l_admin_options_info_6 = "Gestion de la liste des pseudos (logins) interdits dans le fichier"; // inutile
$l_admin_options_info_7 = "Votre configuration vous permet de vous inscrire à";
$l_admin_options_info_8 = "Votre configuration NE vous permet PAS de vous inscrire à";
$l_admin_options_info_9 = "activée : vérifiez la configuration des options :";
$l_admin_options_info_book = "l&#146;annuaire des serveurs publics sur internet";
$l_admin_options_info_10 = "Authentification externe";
$l_admin_options_info_11 = "une seule à la fois ! Veuillez corriger la configuration !";
$l_admin_options_info_12 = "N'activer qu'une seule de ces deux options à la fois";
$l_admin_options_info_13 = "Les options affichées en rouge ne sont modifiables que manuellement dans le fichier de configuration (plus d'autres encore).";
$l_admin_options_check_new_msg_every = "Interval de vérification de l'arrivée d'un premier nouveau message (10 à 60 secondes)";
$l_admin_options_full_check = "Ne vérifier si des contacts sont en attente que toutes les 3 minutes";
$l_admin_options_minimum_length_of_username = "Longueur minimale du pseudo";
$l_admin_options_minimum_length_of_password = "Longueur minimale du mot de passe des utilisateurs";
$l_admin_options_max_pwd_error_lock = "Maximum d'erreurs consécutives du mot de passe avant verrouillage du compte utilisateur";
$l_admin_options_user_history_messages = "Autoriser l'archivage des messages échangés";
$l_admin_options_user_history_messages_export = "Autoriser l'exportation des messages archivés";
$l_admin_option_allow_conference = "Autoriser la création de conférences à plusieurs";
$l_admin_option_send_offline = "Autoriser l'envoi de messages à des contacts non connectés";
$l_admin_options_allow_smiley = "Autoriser l'envoi de smileys (émoticones, affichés en images)";
$l_admin_options_allow_change_email_phone = "Autoriser le changement de son numéro de téléphone ainsi que son adresse email";
$l_admin_options_allow_change_function_name = "Autoriser le changement de son nom/fonction (affiché après le login/pseudo)";
$l_admin_options_allow_change_avatar = "Autoriser le changement de son avatar (photo)";
$l_admin_options_allow_use_proxy = "Autoriser l'utilisation de serveur proxy";
$l_admin_extern_url_to_register = "URL (adresse) pour s'inscrire (forum, CMS...) via l'authentification externe";
$l_admin_extern_url_password_forget = "URL (adresse) pour récupérer son mot de passe (via l'authentification externe)";
$l_admin_extern_url_change_password = "URL (adresse) pour changer son mot de passe (via l'authentification externe)";
$l_admin_options_autentification = "Authentification";
$l_admin_options_security_options = "Options de sécurité";
$l_admin_options_security = "Sécurité";
$l_admin_options_admin_options = "Options admin";
$l_admin_options_force_update_by_server = "Forcer les mises à jour des postes clients depuis le serveur";
$l_admin_options_force_update_by_internet = "Forcer les mises à jour des postes clients depuis le site internet officiel";
$l_admin_options_user_restrictions_options = "Options de restrictions utilisateurs";
$l_admin_options_user_restrictions_options_short = "Restrictions"; // utilisateurs
$l_admin_options_hierachic_management = "Activer la gestion hiérarchique, et afficher la colonne niveau (section admin)";
$l_admin_authentication_extern = "Authentification externe via ";
$l_admin_options_public_see_options = "Les options sont consultables par tout le monde";
$l_admin_options_public_see_users = "La liste des utilisateurs est consultable par tout le monde";
$l_admin_options_public_upload_avatar = "Proposition de nouveaux avatars possible pour tout le monde";
$l_admin_options_admin_email = "Email de l'administrateur";
$l_admin_options_admin_phone = "Téléphone de l'administrateur";
$l_admin_options_public_folder = "Répertoire de consultation publique";
$l_admin_options_scroll_text = "Texte (temporaire) d'information défilant";
$l_admin_options_uppercase_space_nickname = "Autoriser les majuscules et espaces dans le pseudo";
$l_admin_options_allow_email_notifier = "Autoriser l'utilisation du notifieur d'email intégré";
$l_admin_options_force_email_server = "Définir/forcer l'adresse du serveur de courrier entrant (pour le notifieur)";
$l_admin_options_enterprise_server = "Mode entreprise : remonté versions des logiciels, possibilité d'arrêt des PC à distance";
$l_admin_options_allow_rating = "Autoriser la notation de ses contacts (et les consulter si 'PUBLIC')";
$l_admin_options_proxy_address = "Définir/forcer l'adresse du serveur proxy";
$l_admin_options_proxy_port_number = "Définir/forcer le numéro de port du serveur proxy";
$l_admin_options_max_simultaneous_ip_addresses = "Nombre maxi d'adresses IP identiques simultanées (0 : illimité)";
#$l_admin_options_group_for_admin_messages = "Permet l'utilisation des groupes (gestion par l'admin) uniquement pour envoyer des messages administrateurs";
$l_admin_options_group_for_sbx_and_admin_messages = "Permet l'utilisation des groupes (gestion par l'admin) pour la ShoutBox et envoyer des messages administrateurs";
$l_admin_options_group_for_admin_messages_2 = "à utiliser uniquement si _SPECIAL_MODE_GROUP_COMMUNITY est non activée (vide)";
$l_admin_options_cannot_access_to = "mais impossible d'accéder à";
$l_admin_options_auth_if_not_same = "Si l'un des quatre est différent d'IntraMessenger, renseigner les tous";
$l_admin_options_pass_register_book = "Mot de passe pour s'inscrire à";
$l_admin_options_auto_corrected = "option(s) ont été automatiquement corrigées.";
$l_admin_options_pass_need_digit_and_letter = "Mot de passe devant contenir : chiffres ET lettres (au moins un de chaque)";                ///////
$l_admin_options_pass_need_upper_and_lower = "Mot de passe devant contenir : lettres majuscules ET minuscules (au moins un de chaque)";    ///////
$l_admin_options_pass_need_special_character = "Mot de passe devant contenir : caractères spéciaux (au moins un)";                         ///////
$l_admin_options_group_for_shoutbox = "Permet l'utilisation des groupes (gestion par l'admin) uniquement pour les ShoutBoxs";
$l_admin_options_shoutbox_title_short = "ShoutBox";                                                                                         ///////
$l_admin_options_shoutbox_title_long = "ShoutBox (journal de bord ou chat)";
$l_admin_options_shoutbox_refresh_delay = "Délai de rafraichissement (10 à 180 secondes)";
$l_admin_options_shoutbox_store_days = "Durée (jours) de stockage des messages";
$l_admin_options_shoutbox_store_max = "Nombre maxi de messages stockés (ne conserver que les plus récents)";
$l_admin_options_shoutbox_day_user_quota = "Quota quotidien de messages par utilisateur (0 : illimité)";
$l_admin_options_shoutbox_week_user_quota = "Quota hebdomadaire de messages par utilisateur (0 : illimité). Nécessite MySQL 5";
$l_admin_options_shoutbox_need_approval = "Nécessite l&#146;approbation des messages avant publication";
$l_admin_options_shoutbox_approval_queue = "Limite de file d'attente d'approbation"; // "Temporise l'envoi si déjà X messages en attente d'approbation.
$l_admin_options_shoutbox_approval_queue_user = "Limite de file d'attente d'approbation par utilisateur";  // Temporise l'envoi si l'utilisateur a déjà X messages en attente d'approbation.
$l_admin_options_shoutbox_lock_user_approval = "Nombre de rejets d'approbation empêchant l'utilisateur d'envoyer d'autres messages (0 : illimité)";
$l_admin_options_shoutbox_can_vote = "Possibilité de voter";
$l_admin_options_shoutbox_day_votes_quota = "Quota quotidien de votes par utilisateur (0 : illimité)";
$l_admin_options_shoutbox_week_votes_quota = "Quota hebdomadaire de votes par utilisateur (0 : illimité)";
$l_admin_options_shoutbox_remove_msg_votes = "Nombre de votes négatifs activant la suppression automatique du message (0 : illimité)";
$l_admin_options_shoutbox_lock_user_votes = "Nombre de votes négatifs empêchant l'utilisateur d'envoyer d'autres messages (0 : illimité)";
$l_admin_options_shoutbox_public = "Contenu consultable par tout le monde";
$l_admin_options_other_options = "Autres";
$l_admin_options_other_options_options = "Autres options";
$l_admin_options_group_user_can_join = "Les utilisateurs peuvent rejoindre les groupes publics (demander pour les groupes officiels)";
$l_admin_options_may_change_option = "Vous devriez changer les options suivantes";
$l_admin_options_servers_status = "Liste des services/serveurs et leurs états respectifs";
$l_admin_options_check_version_internet = "Vérifier sur internet la disponibilité d'une nouvelle version (serveur)"; // Check for updates automatically
$l_admin_options_show_option_name = "Afficher le nom des options";
$l_admin_options_new = "Nouvelle option";
$l_admin_options_check_now = "Vérifier maintenant";
$l_admin_options_book_password = "Après avoir indiqué ce mot de passe, les autres options ci-dessous seront automatiquement corrigées";
$l_admin_options_time_zones = "Afficher les fuseaux/décalages horaires";
$l_admin_options_bookmarks = "Partage de marques pages (favoris)";
$l_admin_options_bookmarks_can_vote = "Marques pages : possibilité de voter";
$l_admin_options_bookmarks_public = "Les marques pages sont consultables par tout le monde";
$l_admin_options_bookmarks_need_approval = "Nécessite l&#146;approbation des marques pages avant publication";
$l_admin_options_unread_message_validity = "Durée (en jours) de validité des messages non lus (0 : illimité)";
$l_admin_options_lock_after_no_activity_duration = "Définir après combien de jours d'inactivité un compte (fantôme) est automatiquement verrouillé (0 : illimité)";
$l_admin_options_lock_duration = "Durée de verrouillage du compte (en minutes, 0 : illimité)";
$l_admin_options_profile_first_register = "Inviter à renseigner le profil à la première utilisation";
$l_admin_options_roles_to_override_permissions = "Rôles permettant de modifier les permissions";
$l_admin_options_wait_startup_if_server_hs = "Si serveur indisponible au démarrage le client reste en attente (sans rien signaler)";
$l_admin_options_restore_options = "Restaurer la précédente configuration";
$l_admin_options_doc_title = "Documentation";
$l_admin_options_doc_list = "Liste des options serveur";
$l_admin_options_doc_view = "Impact visuel sur les postes clients";
$l_admin_options_allow_skin = "Autoriser le changement de skin";
$l_admin_options_allow_close_im = "Autoriser la fermeture du client";
$l_admin_options_allow_sound_usage = "Autoriser l'usage des sons";
$l_admin_options_allow_reduce_main_screen = "Autoriser la réduction de la fenêtre principale";
$l_admin_options_allow_reduce_message_screen = "Autoriser la réduction de la fenêtre de messages";
$l_admin_options_send_admin_alert_by_email = "Envoyer par email les messages d'alerte (administrateur)";
$l_admin_options_password_validity = "Durée (en jours) de validité des mots de passe avant expiration (0 : illimité)";
$l_admin_options_allow_postit = "Autoriser l'utilisation de Post-It";
$l_admin_options_enable_options = "Option principale qui permet d&#146;en activer d&#146;autres secondaires";
$l_admin_options_status_reasons_list = "Liste des motifs pour chaque état";
$l_admin_options_status_reason = "Motifs pour l&#146;état :";
$l_admin_options_status_reasons_separated = "jusqu'à 10 motifs séparés par des point-virgules";
$l_admin_options_force_status_list = "Force la liste des 4 états depuis le fichier langue (serveur)";
$l_admin_options_share_files = "Partage de fichiers";
$l_admin_options_share_files_title = "Partage et échange de fichiers";
$l_admin_options_share_files_allow = "Permettre le partage (publication) de fichiers";
$l_admin_options_share_files_exchange = "Permettre les échanges de fichiers entre utilisateurs";
$l_admin_options_share_files_options_to_active = "Pour activer les partages de fichiers, le paramètrage des options FTP est indispensable";
$l_admin_options_share_files_ftp_address = "Adresse du serveur FTP ( ex: <I>ftp.votreserveur</I> )"; // /intramessenger/share/files/
$l_admin_options_share_files_ftp_login = "Login d'accès au serveur FTP";
$l_admin_options_share_files_ftp_password = "<b>Si FTP sur autre serveur</b> : mot de passe (en clair) d'accès au serveur FTP"; // (_SHARE_FILES_FOLDER vide)
$l_admin_options_share_files_ftp_password_crypt = "Mot de passe (<U>chiffré</U> par IM_Skin) d'accès au serveur FTP";
$l_admin_options_share_files_ftp_port_number = "Numéro de port du serveur FTP";
$l_admin_options_share_files_max_file_size = "Taille maxi par fichier en Ko {*} (0 : illimité)";
$l_admin_options_share_files_max_nb_files_total = "Nombre maxi de fichiers stockés (0 : illimité)";
$l_admin_options_share_files_max_nb_files_user = "Nombre maxi de fichiers stockés par utilisateur (0 : illimité)";
$l_admin_options_share_files_max_space_size_total = "Taille maxi de l'espace de stockage en Mo {*} (0 : illimité)";
$l_admin_options_share_files_max_space_size_user = "Taille maxi de l'espace de stockage par utilisateur en Mo {*} (0 : illimité)";
$l_admin_options_share_files_need_approval = "La publication de fichiers nécessite l'approbation administrateur";
$l_admin_options_share_files_exchange_need_approval = "L'échange de fichiers entre utilisateurs nécessite l'approbation administrateur";
$l_admin_options_share_files_approval_queue =  "Nombre maxi de fichiers stockés en attente d'approbation administrateur (10 à 99)";
$l_admin_options_share_files_quota_files_user_week = "Quota hedomadaire de fichiers par utilisateur (0 : illimité)";
$l_admin_options_share_files_trash = "La suppression de fichiers publiés met à la corbeille";
$l_admin_options_share_files_exchange_trash = "La suppression de fichiers échangés entre utilisateurs met à la corbeille";
$l_admin_options_share_files_exchange_unread_validity = "Durée (en jours) de validité des fichiers échangés non lus";
$l_admin_options_share_files_info = "{*} 1 Mo = 1024 Ko  &nbsp; - &nbsp;  1 Go = 1024 Mo  &nbsp;  (une disquette = 1,44 Mo  -   un CDR = 700 Mo)";
$l_admin_options_share_files_read_only = "Fichiers en lecture seule uniquement";
$l_admin_options_share_files_can_vote = "Possibilité de noter (voter)";
$l_admin_options_share_files_folder = "<b>Si FTP sur ce serveur web</b> : chemin relatif de stockage des fichiers  (ex : '../share/files/')"; //  pour accès administrateur
$l_admin_options_share_files_compress = "Compresser les fichiers avant expédition. Fichiers alors inaccessibles depuis l'<acronym title='Admin Control Panel'>ACP</acronym>";
$l_admin_options_share_files_protect = "Protéger les fichiers (chiffrement). Fichiers alors inaccessibles depuis l'<acronym title='Admin Control Panel'>ACP</acronym>";
$l_admin_options_share_files_download_quota_day = "Quota quotidien du nombre de téléchargements de fichiers publics (0 : illimité)";
$l_admin_options_share_files_download_quota_week = "Quota hebdomadaire du nombre de téléchargements de fichiers publics (0 : illimité)";
$l_admin_options_share_files_download_quota_month = "Quota mensuel du nombre de téléchargements de fichiers publics (0 : illimité)";
$l_admin_options_share_files_download_quota_mb_day = "Quota quotidien de taille (en Mo) de téléchargements de fichiers publics (0 : illimité)";
$l_admin_options_share_files_download_quota_mb_week = "Quota hebdomadaire de taille (en Mo) de téléchargements de fichiers publics (0 : illimité)";
$l_admin_options_share_files_download_quota_mb_month = "Quota mensuel de taille (en Mo) de téléchargements de fichiers publics (0 : illimité)";
$l_admin_options_share_files_screenshot = "Permettre la publication de captures écrans"; // (fichiers publics) 
$l_admin_options_share_files_screenshot_exchange = "Permettre l'échange de captures écrans entre utilisateurs"; // (fichiers privés) 
$l_admin_options_share_files_webcam = "Permettre la publication de photos via webcam"; // (fichiers publics) 
$l_admin_options_share_files_webcam_exchange = "Permettre l'échange de photos via webcam entre utilisateurs"; // (fichiers privés) 
$l_admin_options_hidden_status = "Autoriser l'état 'hors connexion' (masqué de tous)";
$l_admin_options_backup_files = "Sauvegarde des fichiers";
$l_admin_options_backup_files_title = "Sauvegarde des fichiers utilisateurs";
$l_admin_options_backup_files_allow = "Permettre la sauvegarde (compactée et chiffrée) des fichiers utilisateurs";
$l_admin_options_backup_files_options_to_active = "Pour activer la sauvegarde des fichiers, le paramètrage des options FTP est indispensable";
$l_admin_options_backup_files_max_file_size = "Taille maxi par sauvegarde en Mo {*} (0 : illimité)";
$l_admin_options_backup_files_max_space_size_user = "Taille maxi de l'espace de stockage par utilisateur en Mo {*} (0 : illimité)";
$l_admin_options_backup_files_max_nb_backup_user = "Nombre maxi de sauvegardes stockées par utilisateur (1 à 9)";
$l_admin_options_backup_files_this_local_folder = "Sauvegarde uniquement ce répertoire local (vide : choix par l'utilisateur)"; //"Force to save this folder only (empty for user choose)";
$l_admin_options_backup_files_multi_folders = "Autoriser la sauvegarde de plusieurs répertoires (sinon, un seul uniquement)";
$l_admin_options_backup_files_sub_folders = "Autoriser la sauvegarde (récursive) des sous-dossiers";

#admin users list screen
$l_admin_users_title = "Liste des utilisateurs";
$l_admin_users_col_user = "Utilisateur";
$l_admin_users_col_function = "Nom/fonction";
$l_admin_users_col_level = "Niveau";
$l_admin_users_col_etat = "Etat";
$l_admin_users_col_etat_wait = "'Etat' en attente";
$l_admin_users_col_creat = "Création";
$l_admin_users_col_last = "Dernier";
$l_admin_users_col_action = "Action";
$l_admin_users_col_password = "Mot de passe";
$l_admin_users_col_activity = "Activité";
$l_admin_users_col_version = "Version";
$l_admin_users_col_pc = "Poste";
$l_admin_users_col_mac_adr = "Adresse MAC";
$l_admin_users_col_screen = "Résolution";
$l_admin_users_col_emailclient = "Client de messagerie";
$l_admin_users_col_browser = "Navigateur web";
$l_admin_users_col_ooo = "OOo";
$l_admin_users_col_backup = "Sauvegarde"; // Dernière sauvegarde
$l_admin_users_info_wait_valid = "En attente de validation";
$l_admin_users_info_change_ok = "Changement de configuration validé";
$l_admin_users_info_locked = "Verrouillé";
$l_admin_users_info_valid = "Validé";
$l_admin_users_info_leave = "A quitté ce serveur";
$l_admin_users_order_login = "login";
$l_admin_users_order_function = "nom/fonction";
$l_admin_users_order_state = "état";
$l_admin_users_order_creat = "date création";
$l_admin_users_order_last = "date dernier";
$l_admin_users_order_last_activity = "dernière activité";
$l_admin_users_order_level = "niveau";
$l_admin_users_order_role = "rôle";
$l_admin_users_add_new = "Ajout nouvel utilisateur";
$l_admin_users_cannot_add = "Impossible : nombre maximum d'utilisateurs atteint.";
$l_admin_users_to_add_more_1 = "Pour en ajouter encore, modifiez l'option : <I>_MAX_NB_USER</I>.";
$l_admin_users_to_add_more_2 = "Pour en ajouter manuellement, désactivez l'option.";
$l_admin_users_no_add_1 = "Ajout inutile";
$l_admin_users_no_add_2 = "l'option (<I>_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER</I>) d'ajout automatique<BR/> des nouveaux utilisateurs est déjà activée.";
$l_admin_users_out_of_date = "Comptes périmés";
$l_admin_users_no_out_of_date = "Aucun compte périmé actuellement";
$l_admin_users_for_out_of_date_1 = "Les comptes sont périmés si non utilisés depuis plus de";
$l_admin_users_for_out_of_date_2 = "jours (défini dans les options)";
$l_admin_users_info_level = "Attention : chaque utilisateur ne peut demander l'ajout de contact qu'à ceux de niveau inférieur ou égal !";
$l_admin_users_info_nm_function = "A noter : même si la colonne nom/fonction est masquée, le nom/fonction reste visible dans la gestion des contacts";
$l_admin_users_searching = "Rechercher un utilisateur";
$l_admin_users_no_found = "Aucun utilisateur trouvé";
$l_admin_users_send_admin_message = "Envoyer un message administrateur";
$l_admin_users_nb_connect = "Connexions (jours)";
$l_admin_users_admin = "Administrateur";
$l_admin_users_admin_alert = "Reçoit les alertes";
$l_admin_users_not_admin = "N'est pas admin";
$l_admin_users_hide_from_other = "Caché des autres";
$l_admin_users_auto_add_user_for_ext_auth = "Ne pas désactiver cette option (indispensable pour l'authentification externe) !";
$l_admin_users_ban_user = "Bannir ce nom d&#146;utilisateur";
$l_admin_users_ban_ip = "Bannir cette adresse IP";
$l_admin_users_ban_pc = "Bannir ce poste";
$l_admin_users_pc_banned = "Poste banni";
$l_admin_users_user_banned = "Nom d&#146;utilisateur banni";
$l_admin_users_ip_banned = "Adresse IP bannie";
$l_admin_users_pc_title = "Liste des postes";
$l_admin_users_how_to_ban_pc = "Utiliser ce bouton pour bannir un poste<BR/>(dans la fiche détaillée)";
$l_admin_users_participation = "Taux de participation/présence";
$l_admin_users_reputation = "Réputation"; // popularité
$l_admin_users_state_on = "Allumé";
$l_admin_users_state_off = "Eteint";
$l_admin_users_state_sleep = "En veille (ou en erreur)";
$l_admin_users_rating = "Meilleur score";
$l_admin_users_empty = "Aucun utilisateur pour le moment...";
$l_admin_received = "reçus";
$l_admin_sent = "envoyés";

#admin contacts list screen
$l_admin_contact_title = "Liste des contacts";
$l_admin_contact_col_contact = "Contacts";
$l_admin_contact_col_state = "Etat";
$l_admin_contact_col_action = "Action";
$l_admin_contact_bt_forbid = "Interdire";
$l_admin_contact_info_wait_valid = "Non validé";
$l_admin_contact_info_ok = "Validé";
$l_admin_contact_info_vip = "Privilégié";
$l_admin_contact_info_hidden = "Etat en ligne masqué (invisible)";
$l_admin_contact_info_refused = "Refusé définitivement";
$l_admin_contact_add_contact = "Ajout de contacts";
$l_admin_contact_auto_add = "(validés automatiquement)";
$l_admin_contact_no_add_1 = "Ajout inutile";
$l_admin_contact_no_add_2b = "l'option (<I>_ALLOW_MANAGE_CONTACT_LIST</I>) autorisant l'ajout de contacts par les utilisateurs est activée.";
$l_admin_contact_no_add_3b = "Pour en ajouter manuellement, désactivez l'option.";
$l_admin_contact_cannot_use = "Utilisation impossible car l'option _SPECIAL_MODE_GROUP_COMMUNITY est active.";
$l_admin_contact_average_1 = "Moyenne";
$l_admin_contact_average_2 = "contacts actifs par utilisateur";
$l_admin_contact_total = "total";
$l_admin_contact_bt_avatar = "Choisir un avatar";
$l_admin_contacts = "Contact(s)";
$l_admin_contact_empty = "Aucun contact pour le moment...";

#admin sessions list screen
$l_admin_session_title = "Liste des sessions";
$l_admin_session_title_2 = "Sessions en cours";
$l_admin_session_at = "à";
$l_admin_session_col_state = "Etat";
$l_admin_session_col_user = "Utilisateur";
$l_admin_session_col_function = "Nom/fonction";
$l_admin_session_col_ip = "Adresse IP";
$l_admin_session_col_begin = "Début";
$l_admin_session_col_last = "Dernier";
$l_admin_session_col_version = "Version";
$l_admin_session_info_not_connect = "Non connecté";
$l_admin_session_info_online = "Disponible"; // Connecté
$l_admin_session_info_away = "Absent";
$l_admin_session_info_busy = "Occupé";
$l_admin_session_info_do_not_disturb = "Ne pas déranger";
$l_admin_session_order_user = "utilisateur";
$l_admin_session_order_state = "état";
$l_admin_session_no_session = "Aucune session actuellement";
$l_admin_session_col_time = "Heure";
$l_admin_session_col_state_reason = "Motif";

#admin messenger screen
$l_admin_mess_title = "Messagerie administrateur";
$l_admin_mess_title_2 = "Envoyer un message d'information (en tant qu'administrateur)";
$l_admin_mess_title_3 = "Messages admin en attente de lecture";
$l_admin_mess_title_4 = "Choisir une image à envoyer (png jpg gif)";
$l_admin_mess_message = "Message";
$l_admin_mess_to = "Destinataire(s)";
$l_admin_mess_only = "Uniquement";
$l_admin_mess_all_connected = "Tous les utilisateurs connectés";
$l_admin_mess_all = "Tous les utilisateurs (même ceux non connectés)";
$l_admin_mess_group = "Tous les membres du groupe";
$l_admin_mess_group_connected = "Tous les membres connectés du groupe";
$l_admin_mess_bt_send = "Envoyer";
$l_admin_mess_nb_send = "message(s) envoyé(s)";
$l_admin_mess_bt_refresh = "Actualiser";
$l_admin_mess_time = "Heure";
$l_admin_mess_no_wait = "Actuellement aucun message en attente de lecture";
$l_admin_mess_dir = "Images du répertoire";
$l_admin_mess_select = "Sélectionner";
$l_admin_mess_title_5 = "Envoyer un ordre";
$l_admin_mess_order = "Ordre";
$l_admin_mess_stop_pc = "Extinction du PC";
$l_admin_mess_boot_pc = "Redémarrage du PC";
$l_admin_mess_boot_im = "Redémarrage IM";
$l_admin_mess_cannot_order = "Utilisation impossible car l'option _ENTERPRISE_SERVER n'est pas activée";
$l_admin_mess_image_only = "Images uniquement (.gif .jpg .jpeg .png) sans espace dans le nom de fichier";

#admin message email screen
$l_admin_mess_email_title = "Envoyer un email d'information";

#admin group manage
$l_admin_group_title = "Groupes d'utilisateurs";
$l_admin_group_title_2 = "Administration des groupes d'utilisateurs";
$l_admin_group_no_group = "Aucun groupe actuellement";
$l_admin_group_no_user_group = "Aucun membre de groupe actuellement";
$l_admin_group_col_group = "Groupe";
$l_admin_group_creat_group = "Création nouveau groupe";
$l_admin_group_rename_group = "Renommer groupe";
$l_admin_group_title_add_to_group = "Ajout de membres aux groupes";
$l_admin_group_new_name = "Nouveau nom";
$l_admin_group_add_to_group = "Ajouter au groupe";
$l_admin_group_order_group = "groupe";
$l_admin_group_cannot_use_1 = "Utilisation impossible car l'option _SPECIAL_MODE_GROUP_COMMUNITY n'est pas activée (attention, elle désactive la gestion des contacts).";
$l_admin_group_cannot_use_2 = "Les utilisateurs peuvent néanmoins définir des groupes dans leur liste de contacts.";
$l_admin_group_members = "Membres";
$l_admin_group_public = "Public";
$l_admin_group_official = "Officiel";
$l_admin_group_private = "Privé";
$l_admin_group_public_legende = "les utilisateurs peuvent s'y inscrire directement.";
$l_admin_group_official_legende = "les utilisateurs peuvent demander à s'y inscrire (validation admin).";
$l_admin_group_private_legende = "les utilisateurs ne voit pas ce groupe, et ne peuvent pas s'y inscrire.";

#admin statistics screen
$l_admin_stats_title = "Statistiques";
$l_admin_stats_col_date = "Date";
$l_admin_stats_col_nb_msg = "Messages";
$l_admin_stats_col_nb_creat = "Comptes créés";
$l_admin_stats_col_nb_session = "Sessions simultanées";
$l_admin_stats_col_nb_users = "Utilisateurs";
$l_admin_stats_col_nb_msg_sbx = "Messages en ShoutBox"; // $l_admin_stats_col_nb_sharefile = "Fichiers publiés"; $l_admin_stats_col_nb_sharefile_exchange = "Fichiers échangés"; $l_admin_stats_col_nb_sharefile_download = "Fichiers téléchargés";
$l_admin_stats_no_stats = "Pas de statistiques actuellement";
$l_admin_stats_option_not = "Option non activée";
$l_admin_stats_rate = "de la valeur maximale";
$l_admin_stats_by_day = "Vue par jour";
$l_admin_stats_by_week = "Vue par semaine";
$l_admin_stats_by_month = "Vue par mois";
$l_admin_stats_by_year = "Vue par année";
$l_admin_stats_average = "moyenne";
$l_admin_stats_day_of_week = "Par jour de la semaine";
$l_admin_stats_latest = "Derniers";
$l_admin_stats_click_drag_to_zoom = "Pour zoomer : cliquer et faire glisser dans la zone de traçage";
$l_admin_stats_click_to_show_hide = "Cliquer sur la légende pour afficher/masquer";
$l_admin_stats_empty = "Nécessite d'être utilisés plus de jours pour obtenir des statistiques...";

#admin conference screen
$l_admin_conference_title = "Conférences";
$l_admin_conference_cannot_use_1 = "Utilisation impossible car l'option _ALLOW_CONFERENCE n'est pas activée.";
$l_admin_conference_col_creator = "Créateur";
$l_admin_conference_col_partaker = "Participants";
$l_admin_conference_no_conference = "Aucune conférence actuellement";

#admin change avatar screen
$l_admin_avatar_title = "Changement avatar/photo";
$l_admin_avatar_title_2 = "Sélectionner un nouvel avatar (ou photo)";
$l_admin_avatar_title_3 = "Ajouter un nouvel avatar (ou photo) à la liste";
$l_admin_avatar_title_4 = "Sélectionner l'avatar (ou photo) à supprimer";
$l_admin_avatar_title_5 = "Liste des avatars (ou photos) en attente de validation";
$l_admin_avatar_title_6 = "Liste des avatars inacceptables (ex: dimensions)";
$l_admin_avatar_bt_download = "Télécharger d'autres avatars";
$l_admin_avatar_info_1 = "Placez les photos (ou des avatars) dans le répertoire";
$l_admin_avatar_images_filter = "Filtrer aux fichiers de type images uniquement";

#admin htaccess create
$l_admin_htaccess_1 = "Les fichiers <I>.htaccess</I> et <I>.htpasswd</I> permettent de sécuriser votre répertoire d'administration";
$l_admin_htaccess_2 = "(le premier contient la politique de sécurité, le deuxième les utilisateurs et leur mot de passe).";
$l_admin_htaccess_3 = "Utilisez le bouton ci-dessous pour créer un compte par défaut (à modifier impérativement ensuite)";
$l_admin_htaccess_4 = "Pour (essayer de) les supprimer, cliquer sur";
$l_admin_htaccess_create_files = "Créer les fichiers <I>.htaccess</I> et <I>.htpasswd</I>";
$l_admin_htaccess_warning = "ATTENTION : supprimer ces deux fichiers avant de modifier l'adresse (url) du serveur (ou d'accès à l'ACP).";
$l_admin_htaccess_cannot = "Utilisation impossible car l'option _ACP_PROTECT_BY_HTACCESS n'est pas activée";

#admin log screen
$l_admin_log_title = "Consultation des journaux d&#146;évènements du serveur";
$l_admin_log_title_admin = "Consultation des journaux d&#146;évènements des activités d&#146;administration";
$l_admin_log_select = "Sélectionner le journal d'évènements à consulter";
$l_admin_log_hack = "Tentative de piratage";
$l_admin_log_error_log = "Journal d'erreurs";
$l_admin_log_error_log_connection = "Journal d'erreurs de connexions";
$l_admin_log_type_error = "Erreur";
$l_admin_log_type_warning = "Attention/interdit";
$l_admin_log_type_info = "Information";
$l_admin_log_type_monitor = "Surveiller";
$l_admin_log_session_open = "Ouverture de session";
$l_admin_log_password_errors = "Erreur de mot de passe";
$l_admin_log_lock_user_password = "Verrouillage utilisateur (erreurs de mot de passe)";
$l_admin_log_check_change = "Changement de poste";
$l_admin_log_change_nickname = "Utilisateur changeant de pseudo";
$l_admin_log_upload_avatar = "Proposition d'avatar";
$l_admin_log_username_unknown = "Utilisateurs inconnus";
$l_admin_log_reject_username = "Rejet utilisateurs : pseudos interdits";
$l_admin_log_reject_ip = "Rejet adresses IP interdites";
$l_admin_log_reject_pc = "Rejet postes interdits";
$l_admin_log_reject_max_same_ip = "Limite d'adresses IP identiques simultanées";
$l_admin_log_reject_max_same_pc = "Limite d'utilisations simultanées d'un seul PC";
$l_admin_log_reject_max_users = "Rejet pour nombre maxi d'utilisateurs inscrits atteint";
$l_admin_log_server_full = "Rejet pour serveur complet";
$l_admin_log_no_ip_address = "Adresse IP manquante";
$l_admin_log_version_to_old = "Version trop ancienne";
$l_admin_log_private_password = "Erreur de mot passe pour serveur privé";
$l_admin_log_user_create = "Création nouveau compte";
$l_admin_log_user_allow = "Validation compte en attente";
$l_admin_log_user_disallow = "Désactivation compte";
$l_admin_log_user_delete = "Suppression de compte";
$l_admin_log_user_avatar_valid = "Validation avatar proposé";
$l_admin_log_send_order = "Envoi d'ordre";
$l_admin_log_send_message = "Envoi de message admin";
$l_admin_log_ban_ip_address = "Bannissement adresse IP";
$l_admin_log_unban_ip_address = "Débannissement adresse IP";
$l_admin_log_ban_username = "Bannissement nom d'utilisateur";
$l_admin_log_unban_username = "Débannissement nom d'utilisateur";
$l_admin_log_ban_computer = "Bannissement d'ordinateur";
$l_admin_log_unban_computer = "Débannissement d'ordinateur";
$l_admin_log_user_admin_alert_get = "Activation de la réception des messages d'alerte";
$l_admin_log_user_admin_alert_not_get = "Désactivation de la réception des messages d'alerte";
$l_admin_log_one_user_two_pc = "Un utilisateur sur deux PC simultanément";
$l_admin_log_shoutbox_delete_message = "Suppression message de la Shoutbox";
$l_admin_log_server_status = "Changement de status d'un serveur";
$l_admin_log_bookmark_delete = "Suppression de marque page";
$l_admin_log_empty = "Aucun journal d&#146;évènements pour le moment";
$l_admin_log_options_update = "Modification des options";
$l_admin_log_password_out_of_date = "Mot de passe expiré";
$l_admin_log_files_exchange_sended = "Echange de fichiers : fichier envoyé";
$l_admin_log_files_exchange_proposed = "Echange de fichiers : proposer d'échange de fichier";
$l_admin_log_files_share_sended = "Partage de fichiers : fichier envoyé";
$l_admin_log_files_share_proposed = "Partage de fichiers : fichier proposé";
$l_admin_log_files_exchange_deleted = "Echange de fichiers : fichier supprimé";
$l_admin_log_files_exchange_trashed = "Echange de fichiers : fichier mis à la corbeille";
$l_admin_log_files_share_deleted = "Partage/échange de fichiers : fichier supprimé";
$l_admin_log_files_share_trashed = "Partage/échange de fichiers : fichier mis à la corbeille";
$l_admin_log_files_pendind_delete = "Partage/échange de fichiers : suppression fichier en attente";
//$l_admin_log_files_delete = "Partage/échange de fichiers : suppression fichier";
$l_admin_log_files_alert = "Partage/échange de fichiers : fichier signalé abusif";
$l_admin_log_acp_connect = "Connexion administreur";
$l_admin_log_acp_login_error = "Connexion administreur : login inconnu";
$l_admin_log_acp_password_error = "Connexion administreur : erreur mot de passe";
$l_admin_log_files_backup_sended = "Sauvegarde de fichiers : fichier envoyé";
$l_admin_log_files_backup_deleted = "Sauvegarde de fichiers : fichier supprimé";
$l_admin_log_files_backup_error = "Echec sauvegarde de fichiers : ";
$l_admin_log_files_share_error = "Echec partage/échange de fichiers : ";
$l_admin_log_files_error_max_file_size = "taille du fichier trop grande";
$l_admin_log_files_error_max_space_size_user = "espace de stockage par utilisateur atteint";
$l_admin_log_files_error_max_space_size_total = "espace de stockage total atteint";
$l_admin_log_files_error_too_much_pending = "trop de fichiers en attente de validation";
$l_admin_log_files_error_max_nb_files_user = "quota utilisateur atteint";
$l_admin_log_files_error_max_nb_files_user_total = "quota total atteint";
$l_admin_log_files_error_quota_user_week = "quota de fichiers hebdomadaire atteint";
$l_admin_log_files_error_unknow_media = "médias (extension) inconnu";

#admin check config
$l_admin_check_title = "Vérification de la configuration (après chaque mise à jour)";
$l_admin_check_conf_file = "Fichier de configuration";
$l_admin_check_not_found = "absent !";
$l_admin_check_found = "présent";
$l_admin_check_on = "activé";
$l_admin_check_off = "désactivé";
$l_admin_check_before_upgrade = "Avant chaque mise à jour";
$l_admin_check_read_last = "lire la dernière version de";
$l_admin_check_last_options = "Vérification des dernières options ajoutées/modifiées";
$l_admin_check_new_options_are = "Toutes les dernières options sont";
$l_admin_check_in_conf_file = "dans le fichier de configuration";
$l_admin_check_mysql = "Vérification de la connexion au serveur MySQL";
$l_admin_check_connect_server = "Connexion au serveur";
$l_admin_check_failed = "echec";
$l_admin_check_cannot_continue = "Impossible de continuer sans";
$l_admin_check_language_file = "le fichier langage";
$l_admin_check_connect_to_server = "la connexion au serveur";
$l_admin_check_connect_to_database  = "la connexion à la base de données";
$l_admin_check_missing_option = "l'option manquante";
$l_admin_check_all_tables = "toutes les tables dans la base de données";
$l_admin_check_version = "Version MySQL";
$l_admin_check_connect_database = "Connexion à la base de données";
$l_admin_check_option_missing = "Option manquante dans le fichier";
$l_admin_check_tables_list = "Vérification de la liste des tables";
$l_admin_check_table = "Table";
$l_admin_check_tables_ok = "Toutes les tables existent";
$l_admin_check_use = "Utiliser";
$l_admin_check_in_admin = "dans PHPMyAdmin (admin MySQL)";
$l_admin_check_to_create_table = "pour créer les tables";
$l_admin_check_tables_structure = "Vérification de la structure des tables";
$l_admin_check_tables_structure_are = "La structure des tables existantes est";
$l_admin_check_col = "colonne";
$l_admin_check_for_structure = "pour corriger la structure de la table";
$l_admin_check_update_now = "Corriger maintenant";
$l_admin_check_conf_not_ok = "La configuration est INCORRECTE : vous devez la corriger !";
$l_admin_check_folders = "Vérification des répertoires";
$l_admin_check_folder = "Répertoire";
$l_admin_check_not_writeable = "est en lecture seule";
$l_admin_check_history = "Veuillez consulter l'historique des versions dans le fichier";
$l_admin_check_conf_ok = "La configuration/mise à jour est correcte";
$l_admin_check_can_go = "vous pouvez maintenant vous rendre à";
$l_admin_check_admin_panel = "l'interface d'administration";
$l_admin_check_optimize_tables = "Optimisation des tables";
$l_admin_check_tables_are_optimized = "Toutes les tables viennent d'être optimisées";
$l_admin_check_system_info = "Informations système";
$l_admin_check_incomplete = "incomplet";
$l_admin_check_fix_missing_option = "Pour corriger, enregistrer simplement les options";

#admin save database
$l_admin_save_title = "Sauvegarder la base de données";
$l_admin_save_bt_now = "Sauvegarder maintenant";
$l_admin_save_selet_to_restore = "Sélectionner la sauvegarde à restaurer";
$l_admin_save_bt_restore = "Restaurer";
$l_admin_save_list = "Liste des sauvegardes";
$l_admin_save_not_in_maintenance = "Restauration impossible : mode maintenance non activé.";
$l_admin_save_cannot_use = "Impossible d'utiliser";
$l_admin_save_do_not_use = "Ne pas utiliser";

#admin ban control
$l_admin_ban_users = "Bannissement d'utilisateurs";
$l_admin_ban_ip = "Bannissement d'adresses IP";
$l_admin_ban_pc = "Bannissement de postes";
$l_admin_ban_add_user = "Ajouter un nom d'utilisateur à bannir";
$l_admin_ban_add_ip = "Ajouter une adresse IP à bannir";
$l_admin_ban_add_pc = "Ajouter un code de PC à bannir";
$l_admin_ban_dont_need_file = "Attention, le fichier zzz n'est plus nécessaire, car remplacé ici, pensez à le supprimer."; // ne pas remplacer "zzz" !!!
$l_admin_ban_import_delete = "Importer et supprimer le fichier";

#install
$l_install_check_files = "Vérification fichiers";
$l_install_file = "Fichier";
$l_install_bt_next = "Continuer";
$l_install_step = "Etape";
$l_install_check_cannot_continue = "Impossible de continuer l'installation sans";
$l_install_not_in_maintenance_mode = "Votre serveur n'est pas en mode maintenance <SMALL>(<I>_MAINTENANCE_MODE</I> dans le fichier de configuration)</SMALL>";
$l_install_warning = "Il peut être risqué d'effectuer la mise à jour <B>maintenant<B>.";

#home
$l_home_not_configured = "La configuration du serveur de messagerie instantanée n'est pas encore terminée...";
$l_home_welcome = "Bienvenue sur votre serveur de messagerie instantanée";
$l_home_thanks_to_first = "Merci de bien vouloir vous";
$l_home_here_register = "Cliquer ici pour s&#146;inscrire";
$l_home_register = "inscrire";
$l_home_download_execute = "Téléchargez puis lancez/exécutez";
$l_home_before_install = "<B>AVANT</B> l'installation<BR/>afin de faciliter la configuration et éviter la dernière étape (ci-dessous)";
$l_home_download_install = "Télécharger l&#146;installation d&#146;IntraMessenger";
$l_home_or = "ou";
$l_home_download_zip = "Télécharger IntraMessenger (version zip)";
$l_home_on_startup_config_url = "Au lancement d&#146;IntraMessenger configurer l'adresse (<I>URL</I>) suivante";
$l_home_replace = "Remplacer";
$l_home_by_ip_address = "par l'adresse IP, si vous souhaitez vous y connecter <blink>depuis un AUTRE poste</blink>";

#admin display
$l_admin_display_title = "Affichage";
$l_admin_display_options = "Options d'affichage";
$l_admin_display_menu = "Menu";
$l_menu_top = "Afficher le menu en haut";
$l_menu_left = "Afficher le menu à gauche";
$l_menu_right = "Afficher le menu à droite";
$l_menu_full = "Afficher le menu complet";
$l_menu_not_full = "Afficher le menu nécessaire";
$l_admin_display_style = "Styles";
$l_admin_display_style_select = "Choix du style";
$l_admin_display_background_color = "Couleur de fond";
$l_admin_display_color_select = "Choix de la couleur";
$l_color_blue = "Bleu";
$l_color_green = "Vert";
$l_color_pink = "Rose";
$l_color_red = "Rouge";
$l_color_yellow = "Jaune";
$l_admin_display_character_sets = "Jeux de caractères"; // Character Sets
$l_admin_display_charset = "Charset";
$l_admin_display_default_charset = "Par défaut (suivant langue sélectionnée)";

#ShoutBox
$l_admin_shoutbox_empty = "La Shoutbox est actuellement vide";
$l_admin_shoutbox_cannot = "L'accès à la Shoutbox n'est actuellement pas activé";
$l_admin_shoutbox_valid_messages = "Valider tous les messages en attente";
$l_admin_shoutbox_average = "<acronym title='Moyenne des votes'>Moy.</acronym>";

#Servers status
$l_admin_servers_title = "Etat des serveurs";
$l_admin_servers_list = "Liste des serveurs/services/fonctionnalités";
$l_admin_servers_col_server = "Serveur";
$l_admin_servers_creat = "Création nouveau serveur/service";
$l_admin_servers_list_empty = "Aucun serveur défini";
$l_admin_servers_status_0 = "Hors Service";
$l_admin_servers_status_1 = "Fonctionnement partiel"; // Mode dégradé 
$l_admin_servers_status_2 = "Disponible";
$l_admin_servers_cannot = "Utilisation impossible car l'option _SERVERS_STATUS n'est pas activée.";

#Bookmarks
$l_admin_bookmarks_title = "Marques pages (favoris)";
$l_admin_bookmarks_cannot = "Utilisation impossible car l'option _BOOKMARKS n'est pas activée.";
$l_admin_bookmarks_url_address = "Adresse";
$l_admin_bookmarks_url_title = "Titre";
$l_admin_bookmarks_list_empty = "Aucun marque page défini";
$l_admin_bookmarks_creat = "Création nouveau marque page";
$l_admin_bookmarks_valid_all = "Valider tous les marques pages en attente";
$l_admin_bookmarks_category = "Catégorie";
$l_admin_bookmarks_all_category = "Toutes les catégories";

#Roles
$l_admin_role = "Rôle";
$l_admin_roles_title = "Rôles";
$l_admin_roles_creat_role = "Création nouveau rôle";
$l_admin_roles_title_add_to_role = "Affectation de membres aux rôles";
$l_admin_roles_cannot_use = "Utilisation impossible car l'option _ROLES_TO_OVERRIDE_PERMISSIONS n'est pas activée.";
$l_admin_roles_info = "Les rôles permettent d'attribuer des permissions <u>en plus ou en moins</u> par rapport aux options définies.";
$l_admin_roles_rename_role = "Renommer rôle";
$l_admin_roles_list_empty = "Aucun rôle actuellement";
$l_admin_roles_add_to_role = "Ajouter au rôle";
$l_admin_roles_default = "Rôle par défaut (pour les utilisateurs n&#146;en ayant pas)";
$l_admin_roles_default_explain = "Rôle par défaut sert uniquement à désactiver une <small>(ou plusieurs)</small> option globale qui a été activée juste pour certains rôles.";
$l_admin_roles_permissions = "Définir les permissions";
$l_admin_roles_permissions_of = "Permissions sélectionnées pour le rôle :";
$l_admin_roles_permissions_add = "Ajouter permission";
$l_admin_roles_permissions_empty = "Aucune permission définie pour ce rôle";
$l_admin_roles_need_active_option = "Certaines permissions définies ci-dessus ne peuvent être prises en compte.";
$l_admin_roles_unactivated_options = "Option(s) non activée(s)";
$l_admin_roles_activated_options = "Option(s) activée(s)";
$l_admin_roles_permissions_only_role = "Nota : pour activer une option uniquement à certains rôles, il est nécessaire de la désactiver aux autres (ou juste au rôle par défaut)... <br/>Les permissions du rôle par défaut ne concernent que ces options globales.";
$l_admin_roles_members = "Membres du rôle :";
$l_admin_role_no_member = "Aucun membre du rôle actuellement";
$l_admin_role_permission_on = "Permission activée";
$l_admin_role_permission_off = "Permission désactivée";
$l_admin_role_dashboard = "Tableau de bord des permissions attribuées";
$l_admin_role_useless_permission = "Permission inutile (valeur identique à l&#146;option)";
$l_admin_role_get_admin_alert = "Recevoir les alertes (administrateur)";
$l_admin_role_send_alert_to_admin = "Pouvoir envoyer des alertes aux administrateurs";
$l_admin_role_broadcast_alert_to_group = "Pouvoir envoyer des alertes à tous ceux du groupe";
$l_admin_role_broadcast_alert = "Pouvoir envoyer des alertes à tous";
$l_admin_role_offline_mode = "Force le compte en mode déconnecté";
$l_admin_role_change_server_status = "Pouvoir changer l'état des serveurs";
$l_admin_role_cannot_option = "Activation de ce rôle impossible : l'option respective n'est pas activée.";
$l_admin_role_cannot_option_see_default_role = "voir aussi le <i>rôle par défaut</i>";

#Share files
$l_admin_share_files_title = "Fichiers partagés (publiés)";
$l_admin_share_files_col_name = "Nom fichier";
$l_admin_share_files_col_size = "Taille";
$l_admin_share_files_col_create = "Création";
$l_admin_share_files_col_add = "Ajout";
$l_admin_share_files_col_nb_download = "Nombre de téléchargements";
$l_admin_share_files_col_author = "Auteur";
$l_admin_share_files_col_recipient = "Destinataire";
$l_admin_share_files_col_removal = "Suppression";
$l_admin_share_files_col_projet = "Projet";
$l_admin_share_files_col_hash = "Signature (hash MD5)";
$l_admin_share_files_cannot = "L'accès au partage de fichiers n'est actuellement pas activé";
$l_admin_share_files_empty = "Aucun fichier actuellement";
$l_admin_share_files_exchange = "Fichiers échangés";
$l_admin_share_files_trash = "Corbeille des fichiers partagés supprimés";
$l_admin_share_files_trash_exchange = "Corbeille des fichiers échangés supprimés";
$l_admin_share_file_pending = "Fichiers partagés en attente de validation";
$l_admin_share_file_pending_exchange = "Fichiers échangés en attente de validation";
$l_admin_share_file_valid_pending_files = "Valider tous les fichiers en attente";
$l_admin_share_file_clean_deleted = "Contrôle et nettoyage des fichiers supprimés"; // Clear deleted files 
$l_admin_share_file_only_shared_files = "Sans modération ni corbeille, seuls les fichiers partagés (publiés) sont affichés";
$l_admin_share_file_project_files_only = "Fichiers de ce projet uniquement";
$l_admin_share_file_project_list = "Liste des projets";
$l_admin_share_file_project_subfolder = "Sous-dossier";
$l_admin_share_file_project_col_end = "Fin";
$l_admin_share_file_project_col_closing = "Clôture";
$l_admin_share_file_project_empty = "Aucun projet actuellement";
$l_admin_share_file_project_add_new = "Ajout nouveau projet";
$l_admin_share_file_project_close_empty = "Les utilisateurs ne peuvent ajouter des fichiers aux projets sans nom (vides) ou clôturés";
$l_admin_share_file_project_subfolder_must_exist = "Veuillez vérifier que les sous-dossiers existent";
$l_admin_share_file_media = "Média";
$l_admin_share_file_compressed_file = "Fichier compressé";
$l_admin_share_file_protected_file = "Fichier protégé";
$l_admin_share_file_cannot_display = "affichage impossible";
$l_admin_share_file_cannot_protect = "Pour protéger les fichiers voir";

#Files Backup
$l_admin_backup_files_cannot = "L'accès aux sauvegarde de fichiers n'est actuellement pas activé";

#ACP Authentication
$l_admin_acp_auth_title = "Authentification (ACP)";
$l_admin_acp_auth_error = "Erreur d'authentification...";
$l_admin_acp_auth_username = "Nom d'utilisateur";
$l_admin_acp_auth_password = "Mot de passe";
$l_admin_acp_auth_login = "Connexion";
$l_admin_remember_me = "Se souvenir de moi";

#ACP Change password
$l_admin_acp_pass_changing = "Changement de mot de passe";
$l_admin_acp_pass_1 = "Actuel";
$l_admin_acp_pass_2 = "Nouveau";
$l_admin_acp_pass_3 = "Confirmation";

#ACP Administrators
$l_admin_acp_admin_title = "Gestion des administrateurs";
$l_admin_acp_admin_warning_1 = "Attention, l'option _ACP_PROTECT_BY_HTACCESS est activée.";
$l_admin_acp_admin_warning_2 = "L'authentification des administrateurs ne sera prise en compte qu'une fois l'option désactivée.";
$l_admin_acp_admin_list = "Liste des administrateurs";
$l_admin_acp_admin_list_empty = "La liste est vide";
$l_admin_acp_admin_create = "Création nouveau compte administrateur";
$l_admin_acp_admin_at_least = "Au moins 6 caractères alphanumériques";
$l_admin_acp_admin_right_on = "Droit activé";
$l_admin_acp_admin_right_off = "Droit non activé";
$l_admin_acp_admin_right_see_role = "Option serveur non activée : voir rôles si droit effectif";
$l_admin_acp_admin_right_no_option = "Droit non activable (voir options)";
$l_admin_acp_admin_rights = "Droits d&#146;accès";
$l_admin_acp_admin_right[1] = "Gestion des administrateurs";
$l_admin_acp_admin_right[2] = "Gestion des options";
$l_admin_acp_admin_right[4] = "Gestion des utilisateurs : débloquage";
#$l_admin_acp_admin_right[4] = "Gestion des utilisateurs : profil";
$l_admin_acp_admin_right[8] = "Gestion des utilisateurs : accès complet";
$l_admin_acp_admin_right[16] = "Gestion des contacts utilisateurs";
$l_admin_acp_admin_right[32] = "Gestion des avatars";
$l_admin_acp_admin_right[64] = "Gestion des groupes";
$l_admin_acp_admin_right[128] = "Gestion des rôles";
$l_admin_acp_admin_right[256] = "Gestion de la ShoutBox";
$l_admin_acp_admin_right[512] = "Gestion des fichiers publiés";
$l_admin_acp_admin_right[1024] = "Gestion des marques pages";
$l_admin_acp_admin_right[2048] = "Gestion des bannissements";
$l_admin_acp_admin_right[4096] = "Gestion état des serveurs";
$l_admin_acp_admin_right[8192] = "Messagerie admin";
$l_admin_acp_admin_right[16384] = "Messagerie admin : ordres";
$l_admin_acp_admin_right[32768] = "Messagerie admin : emails";
$l_admin_acp_admin_right[65536] = "Journaux d'événements : consultation";
$l_admin_acp_admin_right[131072] = "Journaux d'événements : purge";
$l_admin_acp_admin_right[262144] = "";
$l_admin_acp_admin_right[524288] = "";
$l_admin_acp_admin_right[1048576] = "";

?>