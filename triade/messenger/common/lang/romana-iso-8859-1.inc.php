<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2013 THeUDS           **
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
# [EN]
# Thanks for posts your update or new translation on the official forum :
# http://www.intramessenger.com/forum/viewforum.php?f=10
# and post it on the forum or send it by email : im-translate@theuds.com
#
# [FR]
# Merci de poster vos corrections ou nouvelles traductions sur le forum officiel :
# http://www.intramessenger.com/forum/viewforum.php?f=11
# et la poster sur le forum ou l'envoyer par email : im-translate@theuds.com
#
# [RO]
# Tranlated by Cosmin Negut cosmin@runasoft.eu
# 
#
$charset = 'iso-8859-1';
$l_lang_name = "Romana";
//$left_font_family = 'verdana, arial, helvetica, geneva, sans-serif';
//$right_font_family = 'arial, helvetica, geneva, sans-serif';

# commun
$l_legende = "Legenda";
$l_order_by = "Comandat de :";
$l_date_format_display = "m-d-Y";
$l_time_format_display = "g:i:s A";
$l_time_short_format_display = "g:i A";
$l_admin_bt_delete = "Sterge";
$l_admin_bt_erase = "Sterge";
$l_admin_bt_update = "Actualizeaza";
$l_admin_bt_add = "Adauga";
$l_admin_bt_create = "Creaza";
$l_admin_bt_allow = "Permite";
$l_admin_bt_invalidate = "Nu permite";
$l_admin_bt_search = "Cauta";
$l_admin_bt_empty = "Gol";
$l_language = "Limba";
$l_country = "Tara";
$l_time_zone = "Ora locala";
$l_server = "Server";
$l_clic_for_message = "Clic aici pentru a trimite un mesaj al administratorului catre utilizator";
$l_clic_on_user = "Clic aici pentru a vedea detalii despre utilizator";
$l_man = "Barbat";
$l_woman = "Femeie";
$l_gender = "Sexul";
$l_user_informations = "Informatii utilizator";
$l_email = "Email";
$l_phone = "Telefon";
$l_display_col = "Afiseaza coloane";
$l_display = "Afiseaza";
$l_hide = "Hide";
$l_configure = "Configurarea";
$l_captcha = "Copiaza acest cod de securitate";
$l_rows_per_page = "Randuri pe pagina";
$l_KB = "KB"; // Kilo Bytes
$l_relation_option = "Relationship with option : ";
$l_days = "zile";
$l_day_0 = "Luni";
$l_day_1 = "Marti";
$l_day_2 = "Miercuri";
$l_day_3 = "Joi";
$l_day_4 = "Vineri";
$l_day_5 = "Sâmbãtã";
$l_day_6 = "Duminicã";

# Languages
$l_lng['FR'] = "franceza";
$l_lng['GB'] = "engleza"; // EN
$l_lng['UK'] = "engleza"; // EN
$l_lng['DE'] = "germana"; // GE
$l_lng['BR'] = "portugheza braziliana";
$l_lng['PT'] = "portugheza";
$l_lng['ES'] = "spaniola";
$l_lng['IT'] = "italiana";
$l_lng['FI'] = "finlandeza";
$l_lng['RO'] = "romana";
$l_lng['TR'] = "turca";
$l_lng['RS'] = "serbian";
$l_lng['RU'] = "russian";
$l_lng['NL'] = "Dutch";
//$l_lng[''] = "";

# level
$c_nb_level = 5;
$c_level[0] = "Administrator";
$c_level[1] = "Director";
$c_level[2] = "Manager";
$c_level[3] = "Angajat";
$c_level[4] = "Vizitator";
// CEO, Director, Branch manager, Administrator, Supervisor, Center manager, Service manager, Project manager, Sector manager, Manager, Employee, Guest.

#start
$l_start_cannot_authenticate = "Nu poate fi facuta autentificarea"; 
$l_start_unknow_user = "utilizator necunoscut";
$l_start_wait_valid = "cont blocat, te rog asteapta administratorul pentru a debloca...";
$l_start_contact_admin_check = "contacteaza administratorul pentru a confirma schimbarea PC-ului.";
$l_start_password = "parola incorecta.";
$l_start_max_users = "Nu pot porni : nu mai pot adauga utilizatori : s-a atins numarul maxim de utilizatori.";
$l_start_no_find_iduser = "identificarea utilizatorului nu s-a putut face.";
$l_start_short_username = "Autentificare imposibila : nu exista utilizator (pseudonim).";
$l_start_username_forbid = "Autentificare refuzata : utilizator (pseudonim) rezervat (interzis).";
$l_start_username_forbid_by_admin = "Autentificare refuzata : utilizator (pseudonim) interzis de administrator.";
$l_start_version_missing = "Autentificare refuzata : versiune client veche : trebuie sa actualizati.";
$l_start_waiting_valid = "Utilizator blocat utilizator(i) in asteptare...";

# menu
$l_menu_list = "Listeaza";
$l_menu_index = "Index";
$l_menu_dash_board = "Tabloul de bord";
$l_menu_currently = "In prezent";
$l_menu_list_sessions = "Sesiuni curente";
$l_menu_conference = "Conferinte";
$l_menu_list_users = "Utilizatori";
$l_menu_list_users_ip = "Aceeasi adresa IP";
$l_menu_list_users_double = "Acelasi calculator";
$l_menu_users_by_country = "Utilizatori dupa tara";
$l_menu_list_contact = "Contacte utilizatori";
$l_menu_list_conference_list = "Lista conferinte";
$l_menu_list_group = "Grupuri";
$l_menu_list_group_list = "Lista Grupuri";
$l_menu_group_add_member = "Adauga membru";
$l_menu_ban = "Control ban";
$l_menu_ban_user = "Utilizatori";
$l_menu_ban_ip = "IP address";
$l_menu_ban_pc = "Calculatoare";
$l_menu_options = "Optiuni";
$l_menu_avatars = "Avataruri";
$l_menu_messagerie = "Administrare Messenger";
$l_menu_statistics = "Statistici";
$l_menu_log = "Jurnal server";
$l_menu_backup = "Backup baza de date";
$l_menu_donate = "Dona";
$l_menu_donate_info = "All donate are welcome";
$l_menu_need_change_admin_dir = "Fi atent ! Trebuie sa redenumesti folderul <B>/admin/</B> (inainte ca utilizatorul sa se poata conecta) !";
$l_menu_need_delete_install_dir = "Este bine sa stergi folderul <B>/install/</B> (pastreaza-l numai pentru actualizarea versiunii)";
$l_menu_maintenance_mode_on = "Fi atent, maintenance mode este activat, utilizatorii nu pot comunica (si utilizatorii offline nu se pot conecta).";
//$l_menu_need_htaccess = "Fi atent, trebuie sa protejezi sectiunea de administrare cu fisierele de securitate htaccess !";
$l_menu_need_htaccess = "NOTICE, administrator authentication is disabled (_ACP_PROTECT_BY_HTACCESS) <BR/> you should protect the <acronym title='Admin Control Panel'>ACP</acronym> by using htaccess security files!";
$l_menu_pass_root_empty = "Fisierele tale de configurare contin setari (root fara parola) care corespund cu setarile de baza ale contului MySql. Serverul tau MySql ruleaza cu aceste setari de baza, este deschis la atacuri si trebuie in mod real sa fixezi aceste probleme de securitate.";
$l_menu_need_reg = "Este bine sa adaugi fisierul im_setup.reg (mai usor pentru utilizatorii finali sa faca setarile)";
$l_menu_no_javascript = "JavaScript nu este activ, click aici pentru a afisa meniul corect";
$l_menu_no_javascript_info = "JavaScript not active, cannot display menu on top...";
$l_menu_customize = "Customize";
$l_menu_customize_info = "Request an IntraMessenger client customized version";
$l_menu_bookmarks = "Bookmarks";
$l_menu_list_roles_list = "Roluri lista";
$l_menu_messagerie_instant = "Direct";
$l_menu_messagerie_emails = "De email";
$l_menu_logout = "Log out";
$l_menu_acp_auth = "Administrators";
$l_menu_manage = "Manage";

#
$l_pg_result = "Rezultate";
$l_pg_show_result = "Arata rezultate";
$l_pg_prev_page = "Pagina precedenta";
$l_pg_next_page = "Pagina urmatoare";
$l_pg_first = "Prima";
$l_pg_first_page = "Prima pagina";
$l_pg_last = "Ultima";
$l_pg_last_page = "Ultima pagina";
$l_pg_all = "Toate paginile";
$l_pg_to = "catre";
$l_pg_of = "al";

# index
$l_index_welcome = "Bine ai venit in sectiunea de administrare (<acronym title='Admin Control Panel'>ACP</acronym>)";
$l_index_can_cfg = "Poti configura optiunile in fisier";
$l_index_can_lng = "ca limba";
$l_index_actualy = "actual";
$l_index_chg = "prin schimbare";
$l_index_find_doc = "Poti gasi documentatia pentru instalare si configurare in";
$l_index_chk_opt = "Poti alege optiuni si configurarea in";
$l_index_after_upd_chk = "Dupa toate actualizarile, te rog sa alegi configuratia in ultimul meniu : ";
$l_index_waiting_valid = "In asteptare utilizatori valizi admin";
$l_index_ready_users = "Utilizatori gata";
$l_index_today_creat_users = "Utilizatori creati azi";
$l_index_today_sessions = "Sesiuni simultane azi";
$l_index_last_valid_username = "Last registered user:";
$l_index_pending_avatars = "Avataruri in asteptare";
$l_index_records = "Inregistrari";
$l_index_full_list = "Lista completa";
$l_index_users_per_day = "Utilizatori pe zi";
$l_index_created_users_per_day = "Utilizatori creati pe zi";
$l_index_messages_per_day = "Mesaje pe zi";
$l_index_leave_users = "Users that left the server";
$l_index_soon_dashboard_here = "After installation full completed, and maintenance mode disabled, dashboard will be displayed here.";
$l_old_files_to_delete = "Old files still exist and should be deleted";
$l_index_shoutbox_pending = "Shoutbox message(s) waiting approbation";
$l_index_shoutbox_nb_msg = "Messages number"; //  (published)
$l_index_shoutbox_nb_msg_wait = "Posts awaiting approval";
$l_index_shoutbox_nb_msg_rejects = "Rejected posts";
$l_index_shoutbox_nb_user_lock_rejects = "Locked users (unapproved)";
$l_index_shoutbox_nb_user_lock_votes = "Locked users (votes)";
$l_index_shoutbox_nb_votes = "Votes number";
$l_index_shoutbox_best_author = "Best author";
$l_index_users_pending_group = "Pending group join users";
$l_index_trend_7_days = "Last seven days trend (compared to 60 last days)";
$l_index_users_recent_activity = "Users with recent activity <SMALL>(30 days)</SMALL>";
$l_index_checking_version = "Checking for updates";
$l_index_server_up_to_date = "Server version is up to date";
$l_index_new_server_version_available = "New (server) version available !";
$l_index_cannot_check_version = "Cannot check (on internet) for upgrade (server)...";
$l_index_dashboard_empty = "After several days of use, the dashboard will display more information...";
$l_index_bookmarks_pending = "Bookmark(s) waiting approbation";
$l_index_most_connected = "Most connected";
$l_index_share_file_pending = "File(s) waiting Approval";
$l_index_share_file_trash = "File(s) in trash";
$l_index_share_file_alert = "Reported files (pending process)";
$l_index_share_file_download = "Downloads";
$l_index_backup_file = "Backups";
$l_index_backup_file_users = "Users with Backup";
$l_index_files_workspace = "Storage space used (MB)";

# admin options screen
$l_admin_options_title = "Lista optiuni";
$l_admin_options_title_2 = "Sfat";
$l_admin_options_update = "Actualizeaza optiuni";
$l_admin_options_bt_update = "Save options";
$l_admin_options_more = "Display more options";
$l_admin_options_title_table_2 = "Pornire optionala splashscreen (utilizare internet)";
$l_admin_options_col_option = "Optiune";
$l_admin_options_col_value = "Valoare";
$l_admin_options_col_comment = "Comentariu";
$l_admin_options_col_description = "Descrierea";
$l_admin_options_general_options = "Optiuni generale";
$l_admin_options_general_options_short = "Generale";
$l_admin_options_maintenance_mode = "Maintenance mode : users cannot communicate (and offline users cannot connect)";
$l_admin_options_is_usernamePC = "Daca este activat : pseudonim fortat la 'utilizator' , sau, daca nu este activat : utilizatorul nu poate alege pseudonim.";
$l_admin_options_auto_add_user = "Utilizatorii noi sunt adaugati automat.";
$l_admin_options_quick_register = "Quick registration required before automatically adding new users.";
$l_admin_options_need_admin_after_add = "Adaugarea automata a utilizatorilor trebuie validata de administrator.";
$l_admin_options_need_admin_if_chang_check = "Utilizatorii care isi schimba PC-urile trebuie validati de administrator";
$l_admin_options_log_session_open = "Arhiva in jurnalul de evenimente (log) sesiuni deschise.";
$l_admin_options_password_user = "Forteaza utilizarea unei parole.";
$l_admin_options_password_for_private_server = "Daca este gol, serverul este public, daca nu, este parola pentru autentificarea PC-ului.";
$l_admin_options_nb_max_user = "Numar maxim de utilizatori inregistrati (0 : nelimitat).";
$l_admin_options_nb_max_session = "Numar maxim de sesiuni (utilizatori conectati simultan) (0 : nelimitat).";
$l_admin_options_nb_max_contact_by_user = "Numar maxim de contacte pe utilizator (0 : nelimitat).";
$l_admin_options_del_user_after_x_days_not_use = "Conturi neactualizate (pentru a fi sterse de administrator) daca nu sunt folosite timp de x zile.";
$l_admin_options_force_away = "Forteaza status 'plecat' (inlocuieste 'online') cand ruleasza screensaverul.";
$l_admin_options_col_name_hide = "Ascunde coloana : nume/functie";
$l_admin_options_col_name_default_active = "Daca nu este ascuns, setarea de baza afiseaza coloana nume/functie.";
$l_admin_options_allow_invisible = "Permite utilizatorilor sa fie invizibili (online ascuns) pentru anumite contacte.";
$l_admin_options_can_change_contact_nickname = "Permite utilizatorilor sa schimbe contactele.";
$l_admin_options_allow_change_contact_list = "Allow users to manage their contacts and alarms";
$l_admin_options_allow_change_options = "Allow users to manage their options and alarms";
$l_admin_options_allow_change_profile = "Allow users to manage their profile";
$l_admin_options_crypt_msg = "Mesaje criptate";
$l_admin_options_log_messages = "Salveaza (log) pe server toate mesajele (daca nu sunt criptate) : pentru scoala.";
$l_admin_options_censor_messages = "Censoring messages (if uncrypted) : <I>/common/config/censure.txt</I>";
$l_admin_options_site_url = "Adresa URL Web site";
$l_admin_options_site_title = "Titlu Web site";
$l_admin_options_missing_option = "Lipsesc optiuni in";
$l_admin_options_conf_file = "configurare fisier";
$l_admin_options_flag_country = "Afiseaza steagul tarii corespunzator adresei IP (utilizare internet).";
$l_admin_options_legende_empty = "optiune ne-activata"; // (gol)
$l_admin_options_legende_not_empty = "optiune activata"; // (nu este gol)
$l_admin_options_legende_up2u = "La alegere";
$l_admin_options_special_options = "Optiuni speciale";
$l_admin_options_special_modes = "Special modes";
$l_admin_options_normal_mode = "Everyone sees only his (validated) contacts.";
$l_admin_options_opencommunity = "Toata lumea poate vedea pe toata lumea, fara sa poata sa adauge in lista de contacte (ex: scoala, internet cafe...).";
$l_admin_options_groupcommunity = "Toata lumea poate vedea pe toata lumea (numai) din ACELASI GRUP.";
$l_admin_options_opengroupcommunity = "Everybody can see everybody, display by groups.";
$l_admin_options_statistics = "Pentru a pastra si afisa (in sectiunea de administrare) statistici.";
$l_admin_options_info_1 = "Daca ambele sunt goale, fara splashscreen la pornire client";
$l_admin_options_info_2 = "Poti activa una (sau mai multe) din aceste optiuni";
$l_admin_options_info_2b = "Poti activa una din aceste optiuni";
$l_admin_options_info_3 = "Nu pot face arhivarea si criptarea acestui mesaj (alege o singura optiune) !";
$l_admin_options_info_4 = "Nu pot folosi simultan 2 moduri : grupuri si comunitate deschisa !";
$l_admin_options_info_5 = "Nu este nevoie sa activezi aceasta optiune";
$l_admin_options_info_6 = "Administreaza lista de pseudonime banate in fisier";
$l_admin_options_info_7 = "Optiunile tale permit sa fi inregistrat pe";
$l_admin_options_info_8 = "Optiunile tale NU permit sa fi inregistrat pe";
$l_admin_options_info_9 = "activat : verifica configuratia optiunilor :";
$l_admin_options_info_book = "director servere publice internet IntraMessenger";
$l_admin_options_info_10 = "Autentificare externa";
$l_admin_options_info_11 = "numai unul ! Multumesc pentru stergere/actualizare configuratie !";
$l_admin_options_info_12 = "Nu activa ambele optiuni";
$l_admin_options_info_13 = "Optiunile afisate in rosu pot fi actualizate manual numai in fisierul de configurare";
$l_admin_options_check_new_msg_every = "Interval intre verificari pentru primul mesaj nou sosite (10 pana la 60 secunde).";
$l_admin_options_full_check = "Verifica contactele in asteptare la fiecare 3 minute";
$l_admin_options_minimum_length_of_username = "Lungimea minima a utilizatorului (pseudonim).";
$l_admin_options_minimum_length_of_password = "Lungimea minima a parolelor utilizatorilor.";
$l_admin_options_max_pwd_error_lock = "Numarul maxim de introduceri eronate a parolelor, dupa care serverul blocheaza utilizatorul";
$l_admin_options_user_history_messages = "Utilizatorii pot inregistra/salva mesajele in istoric.";
$l_admin_options_user_history_messages_export = "Allow export archived messages.";
$l_admin_option_allow_conference = "Permite utilizatorilor sa creeze conferinte multi-utilizator.";
$l_admin_option_send_offline = "Permite utilizatorilor sa trimita mesaje catre contacte offline.";
$l_admin_options_allow_smiley = "Permite trimiterea de smileys (afisate prin pictograme).";
$l_admin_options_allow_change_email_phone = "Permite utilizatorilor sa isi schimbe numarul de telefon si adresa email.";
$l_admin_options_allow_change_function_name = "Permite utilizatorilor sa isi schimbe numele/functia (afisare dupa utilizator).";
$l_admin_options_allow_change_avatar = "Permite utilizatorilor sa isi schimbe avatarul (fotografia).";
$l_admin_options_allow_use_proxy = "Permite utilizatorilor sa foloseasca proxy server.";
$l_admin_extern_url_to_register = "URL (adresa) pentru a face inregistrarea (forum, CMS...) pentru autentificare externa";
$l_admin_extern_url_password_forget = "URL (adresa) pentru a aduce parola pierduta (prin autentificare externa)";
$l_admin_extern_url_change_password = "URL (adresa) pentru a schimba parola (prin autentificare externa)";
$l_admin_options_autentification = "Autentificare";
$l_admin_options_security_options = "Optiuni de securitate";
$l_admin_options_security = "Securitate";
$l_admin_options_admin_options = "Optiuni administrator";
$l_admin_options_force_update_by_server = "Forteaza clientii sa faca actualizare de pe server.";
$l_admin_options_force_update_by_internet = "Forteaza clientii sa faca actualizare dupa serverul de internet oficial.";
$l_admin_options_user_restrictions_options = "Restrictii utilizatori";
$l_admin_options_user_restrictions_options_short = "Restrictii"; //  utilizatori
$l_admin_options_hierachic_management = "Permite management ierarhic, afiseaza coloana nivel (in sectiunea de administrare).";
$l_admin_authentication_extern = "Autentificare externa (autentificare + parola) prin";
$l_admin_options_public_see_options = "Lista de optiuni este publica";
$l_admin_options_public_see_users = "Lista de utilizatori este publica";
$l_admin_options_public_upload_avatar = "Oricine poate propune avataruri";
$l_admin_options_admin_email = "Adresa email a administratorului";
$l_admin_options_admin_phone = "Numarul de telefon al administratorului";
$l_admin_options_public_folder = "Folder public (afiseaza optiuni, utilizatori...)";
$l_admin_options_scroll_text = "Deruleaza mesaj informatii (temporar)";
$l_admin_options_uppercase_space_nickname = "Allow uppercase and space in nickname.";
$l_admin_options_allow_email_notifier = "Permite utilizatorilor sa foloseasca notificare prin email";
$l_admin_options_force_email_server = "Adresa serverului de mail de primire (pentru notificare).";
$l_admin_options_enterprise_server = "Mod Enterprise : Preia versiunile instalate de software si poate opri/face reboot calculatoarelor.";
$l_admin_options_allow_rating = "Allow users to rate their contacts (and see average if 'PUBLIC'). Permite utilizatorilor sa faca rating contactelor (se poate vedea media in varianta 'PUBLIC')";
$l_admin_options_proxy_address = "Forteaza adresa proxy server.";
$l_admin_options_proxy_port_number = "Forteaza numarul portului proxy server.";
$l_admin_options_max_simultaneous_ip_addresses = "Numarul maxim de adrese IP simultane (0 : nelimitat)";
#$l_admin_options_group_for_admin_messages = "Permite managementul grupurilor, numai pentru a trimite mesaje admin.";
$l_admin_options_group_for_sbx_and_admin_messages = "Allow (admin) manage groups, for ShoutBox and send admin messages";
$l_admin_options_group_for_admin_messages_2 = "se foloseste numai daca _SPECIAL_MODE_GROUP_COMMUNITY este gol";
$l_admin_options_cannot_access_to = "dar nu se poate accesa la";
$l_admin_options_auth_if_not_same = "If one of the four is different from IntraMessenger, complete everyone";
$l_admin_options_pass_register_book = "Password to register on";
$l_admin_options_auto_corrected = "option(s) were automatically corrected.";
$l_admin_options_pass_need_digit_and_letter = "Password must contain : letters AND numbers (at least one of each).";
$l_admin_options_pass_need_upper_and_lower = "Password must contain : uppercase and lowercase letters (at least one of each).";
$l_admin_options_pass_need_special_character = "Password must contain : special characters (at least one).";
$l_admin_options_group_for_shoutbox = "Allow (admin) manage groups, only for shoutboxs";
$l_admin_options_shoutbox_title_short = "Shoutbox";
$l_admin_options_shoutbox_title_long = "Shoutbox (logbook or chat)";
$l_admin_options_shoutbox_refresh_delay = "Refresh delay (10 to 180 seconds)";
$l_admin_options_shoutbox_store_days = "Duration (days) for storing messages (before expiration).";
$l_admin_options_shoutbox_store_max = "Maximum number of messages stored (keeping only the most recent).";
$l_admin_options_shoutbox_day_user_quota = "Daily messages quota per user (0: unlimited).";
$l_admin_options_shoutbox_week_user_quota = "Weekly messages quota per user (0: unlimited). Need MySQL 5.";
$l_admin_options_shoutbox_need_approval = "Messages require approval before publication.";
$l_admin_options_shoutbox_approval_queue = "Limit queue approval.";
$l_admin_options_shoutbox_approval_queue_user = "Limit queue approval by user.";
$l_admin_options_shoutbox_lock_user_approval = "Number approval rejects to prevent user from sending other messages (0: unlimited).";
$l_admin_options_shoutbox_can_vote = "Possibility to vote";
$l_admin_options_shoutbox_day_votes_quota = "Daily votes quota per user (0: unlimited).";
$l_admin_options_shoutbox_week_votes_quota = "Weekly votes quota per user (0: unlimited).";
$l_admin_options_shoutbox_remove_msg_votes = "Number of negative votes activating the automatic message deletion (0: unlimited).";
$l_admin_options_shoutbox_lock_user_votes = "Number of votes to prevent user from sending other messages (0: unlimited).";
$l_admin_options_shoutbox_public = "Shoutbox content is public";
$l_admin_options_other_options = "Alte";
$l_admin_options_other_options_options = "Alte optiuni";
$l_admin_options_group_user_can_join = "Users can join public groups (request for officials groups).";
$l_admin_options_may_change_option = "You may change following options";
$l_admin_options_servers_status = "Servers/services list and their respective status";
$l_admin_options_check_version_internet = "Check on internet for new (server) version";
$l_admin_options_show_option_name = "Display options name";
$l_admin_options_new = "New option";
$l_admin_options_check_now = "Check now";
$l_admin_options_book_password = "After save this password, other following options will be automatically updated";
$l_admin_options_time_zones = "Display time zones differences";
$l_admin_options_bookmarks = "Share bookmarks";
$l_admin_options_bookmarks_can_vote = "Allowed to vote for bookmarks";
$l_admin_options_bookmarks_public = "Bookmarks public";
$l_admin_options_bookmarks_need_approval = "Bookmarks require approval before publication";
$l_admin_options_unread_message_validity = "Unread message validity (days) (0: unlimited)";
$l_admin_options_lock_after_no_activity_duration = "Set after how many days of inactivity a (ghost) account is automatically locked (0: unlimited)";
$l_admin_options_lock_duration = "Account lockout duration (minutes, 0: unlimited)";
$l_admin_options_profile_first_register = "Fill out profile information on first login";
$l_admin_options_roles_to_override_permissions = "Roles to override permissions";
$l_admin_options_wait_startup_if_server_hs = "If server unavailable on startup, client still wait (without warn)";
$l_admin_options_restore_options = "Restore previous configuration";
$l_admin_options_doc_title = "Documentation";
$l_admin_options_doc_list = "Server options list";
$l_admin_options_doc_view = "Visual impact on the client"; 
$l_admin_options_allow_skin = "Allow change skin";
$l_admin_options_allow_close_im = "Allow close IM";
$l_admin_options_allow_sound_usage = "Allow use sounds";
$l_admin_options_allow_reduce_main_screen = "Allow reduce main screen";
$l_admin_options_allow_reduce_message_screen = "Allow reduce messages screen";
$l_admin_options_send_admin_alert_by_email = "Send by email admin alert messages";
$l_admin_options_password_validity = "Password validity (days) before expiration (0: unlimited)";
$l_admin_options_allow_postit = "Allow use Post-It";
$l_admin_options_enable_options = "Main option enable secondary options";
$l_admin_options_status_reasons_list = "Status reasons list";
$l_admin_options_status_reason = "Reason for status:";
$l_admin_options_status_reasons_separated = "up to 10 reasons separated by semicolons";
$l_admin_options_force_status_list = "Force 4 status list from language file (server)";
$l_admin_options_share_files = "Share files";
$l_admin_options_share_files_title = "Share and exchange files";
$l_admin_options_share_files_allow = "Allow share (publish) files";
$l_admin_options_share_files_exchange = "Allow exchange files between users";
$l_admin_options_share_files_options_to_active = "To active file sharing, must setup FTP options";
$l_admin_options_share_files_ftp_address = "FTP server address ( example: <I>ftp.yourserver</I> )";
$l_admin_options_share_files_ftp_login = "FTP server login";
$l_admin_options_share_files_ftp_password = "<b>Only if FTP on another server</b>: (clear) FTP password";
$l_admin_options_share_files_ftp_password_crypt = "Password (<U>encrypted</U> by IM_Skin) for FTP server";
$l_admin_options_share_files_ftp_port_number = "FTP server port number";
$l_admin_options_share_files_max_file_size = "Max file size in KB {*} (0: unlimited)";
$l_admin_options_share_files_max_nb_files_total = "Max stored files number (0: unlimited)";
$l_admin_options_share_files_max_nb_files_user = "Max stored files number per user (0: unlimited)";
$l_admin_options_share_files_max_space_size_total = "Max files storage space (MB) {*} (0: unlimited)";
$l_admin_options_share_files_max_space_size_user = "Max files storage space (MB) {*} per user (0: unlimited)";
$l_admin_options_share_files_need_approval = "Published files require admin approval";
$l_admin_options_share_files_exchange_need_approval = "Users files exchange require admin approval";
$l_admin_options_share_files_approval_queue =  "Limit queue approval (10 to 99)";
$l_admin_options_share_files_quota_files_user_week = "Weekly files quota per user (0: unlimited)";
$l_admin_options_share_files_trash = "On deleting, put published files to trash (not deleted)";
$l_admin_options_share_files_exchange_trash = "On deleting, put exchanged files in trash (not deleted)";
$l_admin_options_share_files_exchange_unread_validity = "Unread exchanged files validity (days)";
$l_admin_options_share_files_info = "{*} 1 MB = 1024 KB  &nbsp; - &nbsp;  1 GB = 1024 MB  &nbsp;  (one floppy = 1,44 MB  -   one CDR = 700 MB)";
$l_admin_options_share_files_read_only = "Files are read only";
$l_admin_options_share_files_can_vote = "Allow rating published files";
$l_admin_options_share_files_folder = "<b>Only if FTP on this web server</b>: relative path to files storage folder (eg: '../share/files/')"; // for admin access
$l_admin_options_share_files_compress = "Compress files before uploading: remove file display from <acronym title='Admin Control Panel'>ACP</acronym>";
$l_admin_options_share_files_protect = "Protect (encrypt) files: remove file display from <acronym title='Admin Control Panel'>ACP</acronym>";
$l_admin_options_share_files_download_quota_day = "Daily download number publics files quota (0: unlimited)";
$l_admin_options_share_files_download_quota_week = "Weekly download number publics files quota (0: unlimited)";
$l_admin_options_share_files_download_quota_month = "Monthly download number publics files quota (0: unlimited)";
$l_admin_options_share_files_download_quota_mb_day = "Daily download publics files size (MB) quota (0: unlimited)";
$l_admin_options_share_files_download_quota_mb_week = "Weekly download publics files size (MB) quota (0: unlimited)";
$l_admin_options_share_files_download_quota_mb_month = "Monthly download publics files size (MB) quota (0: unlimited)";
$l_admin_options_share_files_screenshot = "Allow publish screenshot"; // (public files)
$l_admin_options_share_files_screenshot_exchange = "Allow exchange screenshot between users"; // (private files) 
$l_admin_options_share_files_webcam = "Allow publish photo from webcam"; // (public files)
$l_admin_options_share_files_webcam_exchange = "Allow exchange photo from webcam between users"; // (private files) 
$l_admin_options_hidden_status = "Allow status offline (hidden for all users)";
$l_admin_options_backup_files = "Files backup";
$l_admin_options_backup_files_title = "Users files backup";
$l_admin_options_backup_files_allow = "Allow users files backup (archive compacted and encrypted)";
$l_admin_options_backup_files_options_to_active = "To active file backup, must setup FTP options";
$l_admin_options_backup_files_max_file_size = "Max archive size in MB {*} (0: unlimited)";
$l_admin_options_backup_files_max_space_size_user = "Max archive storage space (MB) {*} per user (0: unlimited)";
$l_admin_options_backup_files_max_nb_backup_user = "Max stored archive number (1 to 9)";
$l_admin_options_backup_files_this_local_folder = "Backup this local folder only (empty: user choose)";
$l_admin_options_backup_files_multi_folders = "Allow backup multi folders (otherwise, only one)";
$l_admin_options_backup_files_sub_folders = "Allow recursive backup (include sub-folders)";

#admin users list screen
$l_admin_users_title = "Lista utilizatori";
$l_admin_users_col_user = "Utilizator";
$l_admin_users_col_function = "Nume/functie";
$l_admin_users_col_level = "Nivel";
$l_admin_users_col_etat = "Status";
$l_admin_users_col_etat_wait = "Status in asteptare";
$l_admin_users_col_creat = "Adaugat";
$l_admin_users_col_last = "Ultimul";
$l_admin_users_col_action = "Actiune";
$l_admin_users_col_password = "Parola";
$l_admin_users_col_activity = "Activity";
$l_admin_users_col_version = "Versiune";
$l_admin_users_col_pc = "Computer";
$l_admin_users_col_mac_adr = "MAC address";
$l_admin_users_col_screen = "Resolutie";
$l_admin_users_col_emailclient = "E-mail client";
$l_admin_users_col_browser = "Web browser";
$l_admin_users_col_ooo = "OOo";
$l_admin_users_col_backup = "Backup";
$l_admin_users_info_wait_valid = "Asteapta validare administrator"; // Standby of validation
$l_admin_users_info_change_ok = "Schimbarea calculatorului validata";
$l_admin_users_info_locked = "Locked";
$l_admin_users_info_valid = "Validat";
$l_admin_users_info_leave = "Paraseste serverul";
$l_admin_users_order_login = "login";
$l_admin_users_order_function = "nume/functie";
$l_admin_users_order_state = "status";
$l_admin_users_order_creat = "data adaugare";
$l_admin_users_order_last = "data ultimei utilizari";
$l_admin_users_order_last_activity = "last activity";
$l_admin_users_order_level = "nivel";
$l_admin_users_order_role = "role";
$l_admin_users_add_new = "Adauga utilizator nou";
$l_admin_users_cannot_add = "Nu se poate : s-a atins numarul maxim de utilizatori.";
$l_admin_users_to_add_more_1 = "Pentru a adauga mai multe, modifica optiunea : <I>_MAX_NB_USER</I>.";
$l_admin_users_to_add_more_2 = "Pentru a adauga manual, trebuie sa dezactivezi aceasta optiune.";
$l_admin_users_no_add_1 = "Adaugare inutila";
$l_admin_users_no_add_2 = "Optiunea (<I>_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER</I>) pentru a adauga in mod automat<BR/> noi utilizatori este deja activata.";
$l_admin_users_out_of_date = "Conturi expirate";
$l_admin_users_no_out_of_date = "Conturi ne-expirate";
$l_admin_users_for_out_of_date_1 = "Conturile vor expira daca nu vor fi folosite pentru";
$l_admin_users_for_out_of_date_2 = "zile (in lista de optiuni)";
$l_admin_users_info_level = "Atentie : utilizatorii pot adauga contacte numai de nivel mai mic sau egal cu al lor !";
$l_admin_users_info_nm_function = "Nota : chiar daca col nume/functie este ascuns, campul nume/functie ramane totusi afisat in sectiunea de administrare a contactelor";
$l_admin_users_searching = "Cautare utilizator";
$l_admin_users_no_found = "Nu a fost gasit nici un utilizator";
$l_admin_users_send_admin_message = "Trimite un mesaj al administratorului";
$l_admin_users_nb_connect = "Conexiuni (zile)";
$l_admin_users_admin = "Administrator";
$l_admin_users_admin_alert = "Primesti mesaje de alerta";
$l_admin_users_not_admin = "Nu este un administrator";
$l_admin_users_hide_from_other = "Hidden from others";
$l_admin_users_auto_add_user_for_ext_auth = "Nu goliti aceasta optiune (este esential pentru autentificarea externa) !";
$l_admin_users_ban_user = "Baneaza acest utilizator";
$l_admin_users_ban_ip = "Baneaza aceasta adresa ip";
$l_admin_users_ban_pc = "Baneaza acest calculator";
$l_admin_users_pc_banned = "Calculator banat";
$l_admin_users_user_banned = "Utilizator banat";
$l_admin_users_ip_banned = "Adresa IP Banata";
$l_admin_users_pc_title = "Lista Calculatoare";
$l_admin_users_how_to_ban_pc = "Foloseste acest buton pentru a bana un calculator <BR/>(in detalii)";
$l_admin_users_participation = "Participare/rata prezenta";
$l_admin_users_reputation = "Reputatie"; // popularity 
$l_admin_users_state_on = "Pornit";
$l_admin_users_state_off = "Oprit";
$l_admin_users_state_sleep = "In asteptare (sau bug)";
$l_admin_users_rating = "Highest score";
$l_admin_users_empty = "Actually no user...";
$l_admin_received = "received";
$l_admin_sent = "sent";

#admin contacts list screen
$l_admin_contact_title = "Lista contacte";
$l_admin_contact_col_contact = "Contacte";
$l_admin_contact_col_state = "Status";
$l_admin_contact_col_action = "Actiune";
$l_admin_contact_bt_forbid = "Interzice";
$l_admin_contact_info_wait_valid = "Nu este validat";
$l_admin_contact_info_ok = "Validat";
$l_admin_contact_info_vip = "Privilegiat";
$l_admin_contact_info_hidden = "Status ascuns (invizibil)";
$l_admin_contact_info_refused = "Refuzat in mod definitiv ";
$l_admin_contact_add_contact = "Adauga noi contacte";
$l_admin_contact_auto_add = "(adaugat in mod automat)";
$l_admin_contact_no_add_1 = "Adaugare inutila";
$l_admin_contact_no_add_2b = "The option (<I>_ALLOW_MANAGE_CONTACT_LIST</I>) to allow users to add contacts is enabled.";
$l_admin_contact_no_add_3b = "To manually add, you need to disable this option.";
$l_admin_contact_cannot_use = "Nu poate fi folosita lista de contacte : optiunea _SPECIAL_MODE_GROUP_COMMUNITY este activata.";
$l_admin_contact_average_1 = "Media";
$l_admin_contact_average_2 = "contacte active pe utilizator";
$l_admin_contact_total = "total";
$l_admin_contact_bt_avatar = "Alege un avatar";
$l_admin_contacts = "Contact(e)";
$l_admin_contact_empty = "Actually no contact...";

#admin sessions list screen
$l_admin_session_title = "Lista sesiuni";
$l_admin_session_title_2 = "Sesiuni curente";
$l_admin_session_at = "la";
$l_admin_session_col_state = "Status";
$l_admin_session_col_user = "Utilizator";
$l_admin_session_col_function = "Nume/functie";
$l_admin_session_col_ip = "Adresa IP";
$l_admin_session_col_begin = "Incepe";
$l_admin_session_col_last = "Ultima";
$l_admin_session_col_version = "Versiune";
$l_admin_session_info_not_connect = "Neconectat";
$l_admin_session_info_online = "Disponibil"; // Online
$l_admin_session_info_away = "Plecat";
$l_admin_session_info_busy = "Ocupat";
$l_admin_session_info_do_not_disturb = "Nu deranjati";
$l_admin_session_order_user = "utilizator";
$l_admin_session_order_state = "status";
$l_admin_session_no_session = "Nici o sesiune";
$l_admin_session_col_time = "Acum";
$l_admin_session_col_state_reason = "Reason";

#admin messenger screen
$l_admin_mess_title = "Administrator messenger";
$l_admin_mess_title_2 = "Trimite un mesaj informativ (ca administrator)";
$l_admin_mess_title_3 = "Mesajul administratorului nu a fost inca citit";
$l_admin_mess_title_4 = "Alege o imagine pentru a fi trimisa (png jpg gif)";
$l_admin_mess_message = "Mesaj";
$l_admin_mess_to = "Destinatar(i)";
$l_admin_mess_only = "Numai";
$l_admin_mess_all_connected = "Toti utilizatorii conectati";
$l_admin_mess_all = "Toti utilizatorii (inclusiv utilizatorii offline)";
$l_admin_mess_group = "Toti mambrii grupului";
$l_admin_mess_group_connected = "Toti membrii conectati ai grupului";
$l_admin_mess_bt_send = "Trimite";
$l_admin_mess_nb_send = "mesajul a fost trimis";
$l_admin_mess_bt_refresh = "Refresh";
$l_admin_mess_time = "Acum";
$l_admin_mess_no_wait = "Nici un mesaj nu asteapta sa fie citit";
$l_admin_mess_dir = "Imagini de foldere";
$l_admin_mess_select = "Selecteaza";
$l_admin_mess_title_5 = "Trimite o comanda";
$l_admin_mess_order = "Comanda";
$l_admin_mess_stop_pc = "Opreste calculatorul";
$l_admin_mess_boot_pc = "Reboot calculator";
$l_admin_mess_boot_im = "Reboot IM";
$l_admin_mess_cannot_order = "Nu poate fi folosit : optiunea _ENTERPRISE_SERVER nu este activata";
$l_admin_mess_image_only = "Pictures only (.gif .jpg .jpeg .png) without space in filename";

#admin message email screen
$l_admin_mess_email_title = "Send an information email";

#admin group manage
$l_admin_group_title = "Grupuri utilizatori";
$l_admin_group_title_2 = "Administreaza grupuri utilizatori";
$l_admin_group_no_group = "Acum nu exista nici un grup";
$l_admin_group_no_user_group = "Acum nu exista nici un membru al grupului";
$l_admin_group_col_group = "Grup";
$l_admin_group_creat_group = "Creaza grup nou";
$l_admin_group_rename_group = "Redenumeste grup";
$l_admin_group_title_add_to_group = "Adauga membri la grupuri";
$l_admin_group_new_name = "Nume nou";
$l_admin_group_add_to_group = "Adauga la grup";
$l_admin_group_order_group = "grup";
$l_admin_group_cannot_use_1 = "Nu poti folosi grupuri : optiunea _SPECIAL_MODE_GROUP_COMMUNITY nu este activata (fi atent, lista de contacte va fi dezactivata).";
$l_admin_group_cannot_use_2 = "Cu toate acestea, utilizatorii pot adauga singuri grupuri in lista lor de contacte.";
$l_admin_group_members = "Membri";
$l_admin_group_public = "Public";
$l_admin_group_official = "Official";
$l_admin_group_private = "Private";
$l_admin_group_public_legende = "users can directly join group.";
$l_admin_group_official_legende = "users can request to join group (validated by admin).";
$l_admin_group_private_legende = "users cannot see group and cannot join.";

#admin statistics screen
$l_admin_stats_title = "Statistici";
$l_admin_stats_col_date = "Data";
$l_admin_stats_col_nb_msg = "Mesaje";
$l_admin_stats_col_nb_creat = "Utilizatori creati";
$l_admin_stats_col_nb_session = "Sesiuni simultane";
$l_admin_stats_col_nb_users = "Utilizatori";
$l_admin_stats_col_nb_msg_sbx = "ShoutBox Messages";
$l_admin_stats_no_stats = "Acum nu exista statistici";
$l_admin_stats_option_not = "Optiune neactivata";
$l_admin_stats_rate = "a valorii maxime";
$l_admin_stats_by_day = "Afiseaza pe zile";
$l_admin_stats_by_week = "Afiseaza pe saptamani";
$l_admin_stats_by_month = "Afiseaza pe luni";
$l_admin_stats_by_year = "Afiseaza pe an";
$l_admin_stats_average = "media";
$l_admin_stats_day_of_week = "By day of week";
$l_admin_stats_latest = "Latest";
$l_admin_stats_click_drag_to_zoom = "Click and drag in the plot area to zoom in";
$l_admin_stats_click_to_show_hide = "Click on legend to display/hide";
$l_admin_stats_empty = "Need to use more days to get statistics...";

#admin conference screen
$l_admin_conference_title = "Conferinte";
$l_admin_conference_cannot_use_1 = "Nu pot folosi : optiunea _ALLOW_CONFERENCE nu este activata.";
$l_admin_conference_col_creator = "Creator";
$l_admin_conference_col_partaker = "Participanti";
$l_admin_conference_no_conference = "Acum nu exista nici o conferinta";

#admin change avatar screen
$l_admin_avatar_title = "Schimba avatar/fotografie";
$l_admin_avatar_title_2 = "Selecteaza un alt avatar (sau fotografie)";
$l_admin_avatar_title_3 = "Adauga un alt avatar (sau fotografie) la aceasta lista";
$l_admin_avatar_title_4 = "Selecteaza avatar (sau fotografie) pentru a sterge";
$l_admin_avatar_title_5 = "Avataruri in asteptare (asteapta pentru validarea administratorului)";
$l_admin_avatar_title_6 = "Unacceptable avatar list (e.g. dimensions)";
$l_admin_avatar_bt_download = "Descarca mai multe avataruri";
$l_admin_avatar_info_1 = "Pune fotografiile (sau avatarurile) in folder";
$l_admin_avatar_images_filter = "Filter files such as images only";

#admin htaccess create
$l_admin_htaccess_1 = "Fisierele <I>.htaccess</I> si <I>.htpasswd</I> iti permite sa iti protejezi folderul de administrare";
$l_admin_htaccess_2 = "(primul contine politici de securitate, cel de-al doilea utilizatori si parole).";
$l_admin_htaccess_3 = "Folositi butonul de dedesubt pentru a crea un utilizator implicit (trebuie sa actualizati dupa !)";
$l_admin_htaccess_4 = "Pentru a (incercati sa) ii sterge, apasati pe ";
$l_admin_htaccess_create_files = "Creati fisierele <I>.htaccess</I> si <I>.htpasswd</I>";
$l_admin_htaccess_warning = "WARNING, delete this two files before updating the server address/url (or ACP folder).";
$l_admin_htaccess_cannot = "Cannot use: option _ACP_PROTECT_BY_HTACCESS is not activated.";

#admin log screen
$l_admin_log_title = "Jurnal evenimente server";
$l_admin_log_title_admin = "Jurnal evenimente - administrare";
$l_admin_log_select = "Selectati jurnal de evenimente server de afisat";
$l_admin_log_hack = "Hack attempt";
$l_admin_log_error_log = "Jurnal erori";
$l_admin_log_error_log_connection = "Connections error log";
$l_admin_log_type_error = "Error";
$l_admin_log_type_warning = "Warning/forbidden";
$l_admin_log_type_info = "Information";
$l_admin_log_type_monitor = "Monitor";
$l_admin_log_session_open = "Sesiuni deschise";
$l_admin_log_password_errors = "Erori parola";
$l_admin_log_lock_user_password = "utilizatori blocati pentru erori parola";
$l_admin_log_check_change = "Configurare schimbare";
$l_admin_log_change_nickname = "Schimbare nickname utilizator";
$l_admin_log_upload_avatar = "Actualizare avataruri";
$l_admin_log_username_unknown = "Utilizatori necunoscuti";
$l_admin_log_reject_username = "Nickname-uri pentru utilizatori prohibite";
$l_admin_log_reject_ip = "Adrese IP cu IP-ul banat";
$l_admin_log_reject_pc = "Calculator banat";
$l_admin_log_reject_max_same_ip = "Numar maxim de adrese IP identice care pot fi folosite";
$l_admin_log_reject_max_same_pc = "Max simultaneous of a single PC usage";
$l_admin_log_reject_max_users = "Reject : maximum number of registered users reached";
$l_admin_log_server_full = "Refuzat : server plin";
$l_admin_log_no_ip_address = "Nu exista adresa IP";
$l_admin_log_version_to_old = "Versiune prea veche";
$l_admin_log_private_password = "Eroare parola privata";
$l_admin_log_user_create = "Created users";
$l_admin_log_user_allow = "Utilizatori acceptati";
$l_admin_log_user_disallow = "Users neacceptati";
$l_admin_log_user_delete = "Utilizatori stersi";
$l_admin_log_user_avatar_valid = "Avataruri valide in asteptare";
$l_admin_log_send_order = "Comenzi trimise";
$l_admin_log_send_message = "Mesaje admin trimise";
$l_admin_log_ban_ip_address = "Adrese ip banate";
$l_admin_log_unban_ip_address = "Adrese ip nebanate";
$l_admin_log_ban_username = "Utilizatori banati";
$l_admin_log_unban_username = "Utilizatori nebanati";
$l_admin_log_ban_computer = "Calculatoare banate";
$l_admin_log_unban_computer = "Calculatoare nebanate";
$l_admin_log_user_admin_alert_get = "Utilizatorul primeste acum alerte admin";
$l_admin_log_user_admin_alert_not_get = "Utilizatorul nu mai primeste alerte admin";
$l_admin_log_one_user_two_pc = "A user simultaneously on two computers";
$l_admin_log_shoutbox_delete_message = "Deleting message from shoutbox";
$l_admin_log_server_status = "Server status updated";
$l_admin_log_bookmark_delete = "Deleting bookmark";
$l_admin_log_empty = "Actually no server logs";
$l_admin_log_options_update = "Options updated";
$l_admin_log_password_out_of_date = "Password expiration";
$l_admin_log_files_exchange_sended = "Exchange files: file sended";
$l_admin_log_files_exchange_proposed = "Exchange files: file proposal for exchange";
$l_admin_log_files_share_sended = "Share files: file sended";
$l_admin_log_files_share_proposed = "Share files: file proposed";
$l_admin_log_files_exchange_deleted = "Exchange files: file deleted";
$l_admin_log_files_exchange_trashed = "Exchange files: file into trash";
$l_admin_log_files_share_deleted = "Share/exchange files: file deleted";
$l_admin_log_files_share_trashed = "Share/exchange files: file into trash";
$l_admin_log_files_pendind_delete = "Share/exchange files: delete pendind file";
//$l_admin_log_files_delete = "Share/exchange files: delete file";
$l_admin_log_files_alert = "Share/exchange files: file reported";
$l_admin_log_acp_connect = "Administrator connection";
$l_admin_log_acp_login_error = "Administrator connection: unknow login";
$l_admin_log_acp_password_error = "Administrator connection: incorrect password";
$l_admin_log_files_backup_sended = "Files backup: file sended";
$l_admin_log_files_backup_deleted = "Files backup: file deleted";
$l_admin_log_files_backup_error = "Backup Files  failure: ";
$l_admin_log_files_share_error = "Share/exchange files failure: ";
$l_admin_log_files_error_max_file_size = "file size over quota";
$l_admin_log_files_error_max_space_size_user = "user storage over quota";
$l_admin_log_files_error_max_space_size_total = "total storage over quota";
$l_admin_log_files_error_too_much_pending = "too much pending";
$l_admin_log_files_error_max_nb_files_user = "user files number over quota";
$l_admin_log_files_error_max_nb_files_user_total = "total files number over quota";
$l_admin_log_files_error_quota_user_week = "Weekly files quota per user";
$l_admin_log_files_error_unknow_media = "unknow media (extension)";

#admin check config
$l_admin_check_title = "Verifica configurarea (dupa toate actualizarile)";
$l_admin_check_conf_file = "Configurare fisier";
$l_admin_check_not_found = "Nu a fost gasit !";
$l_admin_check_found = "gasit";
$l_admin_check_on = "pornit";
$l_admin_check_off = "oprit";
$l_admin_check_before_upgrade = "Dupa actualizare";
$l_admin_check_read_last = "citeste ultima versiune";
$l_admin_check_last_options = "verifica ultimele optiuni";
$l_admin_check_new_options_are = "Toate noile optiuni sunt";
$l_admin_check_in_conf_file = "in fisierul de configurare";
$l_admin_check_mysql = "Verifica conexiunea la serverul MySQL";
$l_admin_check_connect_server = "Conectare la server";
$l_admin_check_failed = "esuat";
$l_admin_check_cannot_continue = "Nu pot continua testele fara";
$l_admin_check_language_file = "fisier limba";
$l_admin_check_connect_to_server = "conectare la server";
$l_admin_check_connect_to_database  = "conectare la baza de date";
$l_admin_check_missing_option = "optiune lipsa";
$l_admin_check_all_tables = "toate tabelele in baza de date";
$l_admin_check_version = "versiune MySQL";
$l_admin_check_connect_database = "Conexiune la baza de date";
$l_admin_check_option_missing = "Optiune lipsa in fisier";
$l_admin_check_tables_list = "Verifica lista de tabele";
$l_admin_check_table = "Tabel";
$l_admin_check_tables_ok = "Toate tabelele exista";
$l_admin_check_use = "Foloseste";
$l_admin_check_in_admin = "in administrare MySQL (ex. PHPMyAdmin)";
$l_admin_check_to_create_table = "pentru a crea tabele";
$l_admin_check_tables_structure = "Verifica structura tabele";
$l_admin_check_tables_structure_are = "Toate structurile tabelelor existente sunt";
$l_admin_check_col = "col"; // column
$l_admin_check_for_structure = "pentru a actualiza structura tabelului";
$l_admin_check_update_now = "Actualizeaza acum";
$l_admin_check_conf_not_ok = "Configurare : NU este corect : trebuie sa actualizezi configurarea !";
$l_admin_check_folders = "Verifica foldere";
$l_admin_check_folder = "Folder";
$l_admin_check_not_writeable = "nu poate fi scris";
$l_admin_check_history = "Te rog consulta istoricul versiunilor in fisier";
$l_admin_check_conf_ok = "Configurarea/actualizarea este OK";
$l_admin_check_can_go = "poti trece acum la";
$l_admin_check_admin_panel = "interfata de administrare";
$l_admin_check_optimize_tables = "Optimizare tabele";
$l_admin_check_tables_are_optimized = "Toate tabelele sunt optimizate";
$l_admin_check_system_info = "Informatii sistem";
$l_admin_check_incomplete = "incomplete";
$l_admin_check_fix_missing_option = "To fix it, just save options";

#admin save database
$l_admin_save_title = "Backup la baza de date";
$l_admin_save_bt_now = "Backup acum";
$l_admin_save_selet_to_restore = "Selecteaza backup-ul pentru a restaura";
$l_admin_save_bt_restore = "Restaurare";
$l_admin_save_list = "Lista backup-uri";
$l_admin_save_not_in_maintenance = "Nu pot restaura : maintenance mode este activat.";
$l_admin_save_cannot_use = "Cannot use";
$l_admin_save_do_not_use = "Do not use";

#admin ban control
$l_admin_ban_users = "Banare utilizatori";
$l_admin_ban_ip = "Banare adrese ip";
$l_admin_ban_pc = "Banare calculatoare";
$l_admin_ban_add_user = "Adauga un utilizator in lista ban";
$l_admin_ban_add_ip = "Adauga o adresa ip in lista ban";
$l_admin_ban_add_pc = "Add a computers to ban ";
$l_admin_ban_dont_need_file = "Fi atent, fisierul zzz nu mai este necesar, inlocuieste-l aici, nu uita sa il stergi.";  // do not change "zzz" !!!
$l_admin_ban_import_delete = "Importa si (incearca sa) stergi fisierul";

#install
$l_install_check_files = "Verifica fisiere";
$l_install_file = "Fisier";
$l_install_bt_next = "Continua";
$l_install_step = "Pas";
$l_install_check_cannot_continue = "Nu pot continua instalarea fara";
$l_install_not_in_maintenance_mode = "Your server is not in maintenance mode <SMALL>(<I>_MAINTENANCE_MODE</I> in configuration file)</SMALL>";
$l_install_warning = "Can be dangerous to apply upgrade <B>now</B>.";

#home
$l_home_not_configured = "Serverul de instant messenger nu este inca configurat...";
$l_home_welcome = "Bine ai venit pe serverul tau de instant messenger";
$l_home_thanks_to_first = "Multumesc pentru prima data";
$l_home_here_register = "Apasa aici pentru a te inregistra";
$l_home_register = "inregistrare";
$l_home_download_execute = "Descarcare si executare";
$l_home_before_install = "<B>INAINTE</B> de instalare<BR/> pentru o setare mai usoara si pentru a evita ultimul pas";
$l_home_download_install = "Descarca IntraMessenger (setup/install)";
$l_home_or = "sau";
$l_home_download_zip = "Descarca IntraMessenger (versiune zip)";
$l_home_on_startup_config_url = "La pornirea IntraMessenger, configureaza adresa (<I>URL</I>)";
$l_home_replace = "Inlocuieste";
$l_home_by_ip_address = " cu adresa IP a serverului pentru a te conecta<blink>de la un ALT calculator</blink>";

#admin display
$l_admin_display_title = "Afiseaza";
$l_admin_display_options = "Afiseaza optiuni";
$l_admin_display_menu = "Meniul";
$l_menu_top = "Afiseaza meniul sus";
$l_menu_left = "Afiseaza meniul in stanga";
$l_menu_right = "Afiseaza meniul in dreapta";
$l_menu_full = "Afiseaza meniul complet";
$l_menu_not_full = "Afiseaza meniul dorit";
$l_admin_display_style = "Stil";
$l_admin_display_style_select = "Selectati stil";
$l_admin_display_background_color = "Culoare fundal";
$l_admin_display_color_select = "Selectati culoare";
$l_color_blue = "Albastru";
$l_color_green = "Verde";
$l_color_pink = "Roz";
$l_color_red = "Rosu";
$l_color_yellow = "Galben";
$l_admin_display_character_sets = "Character Sets";
$l_admin_display_charset = "Charset";
$l_admin_display_default_charset = "Default (language charset)";

#ShoutBox
$l_admin_shoutbox_empty = "The shoutbox is currently empty";
$l_admin_shoutbox_cannot = "Access to shoutbox is currently not activated";
$l_admin_shoutbox_valid_messages = "Validate all pending messages";
$l_admin_shoutbox_average = "Rated";

#Servers status
$l_admin_servers_title = "Servers status";
$l_admin_servers_list = "Servers/services/features list";
$l_admin_servers_col_server = "Server";
$l_admin_servers_creat = "Add new server/feature";
$l_admin_servers_list_empty = "No Server";
$l_admin_servers_status_0 = "Out of service";
$l_admin_servers_status_1 = "Not Fully Functional";
$l_admin_servers_status_2 = "Available";
$l_admin_servers_cannot = "Cannot use: option _SERVERS_STATUS is not activated.";

#Bookmarks
$l_admin_bookmarks_title = "Bookmarks";
$l_admin_bookmarks_cannot = "Cannot use : option _BOOKMARKS is not activated.";
$l_admin_bookmarks_url_address = "Address";
$l_admin_bookmarks_url_title = "Title";
$l_admin_bookmarks_list_empty = "No bookmark";
$l_admin_bookmarks_creat = "Add new bookmark";
$l_admin_bookmarks_valid_all = "Validate all pending bookmarks";
$l_admin_bookmarks_category = "Category";
$l_admin_bookmarks_all_category = "All categories";

#Roles
$l_admin_role = "Role";
$l_admin_roles_title = "Roles";
$l_admin_roles_creat_role = "Create new role";
$l_admin_roles_title_add_to_role = "Assigning roles to members";
$l_admin_roles_cannot_use = "Cannot use option : _ROLES_TO_OVERRIDE_PERMISSIONS is not activated.";
$l_admin_roles_info = "The roles can assign permissions <u>more or less</u> compared to the options.";
$l_admin_roles_rename_role = "Rename role";
$l_admin_roles_list_empty = "Actually no role";
$l_admin_roles_add_to_role = "Add to role";
$l_admin_roles_default = "Default role (for users without role)";
$l_admin_roles_default_explain = "Default role only serves to disable a global option which has just been activated for certain roles.";
$l_admin_roles_permissions = "Set permissions";
$l_admin_roles_permissions_of = "Permission selected for role:";
$l_admin_roles_permissions_add = "Add permission";
$l_admin_roles_permissions_empty = "No permission assigned for this role";
$l_admin_roles_need_active_option = "Some permissions defined above can not be taken into account.";
$l_admin_roles_unactivated_options = "Disabled option(s)";
$l_admin_roles_activated_options = "Enabled option(s)";
$l_admin_roles_permissions_only_role = "Note: To activate an option for only certain roles, it is necessary to disable it to other (or just to default role)... <br/>Default role permissions concern only this global options.";
$l_admin_roles_members = "Role members:";
$l_admin_role_no_member = "Actually no role member";
$l_admin_role_permission_on = "Permission activated";
$l_admin_role_permission_off = "Permission disabled";
$l_admin_role_dashboard = "Dashboard permissions";
$l_admin_role_useless_permission = "Useless permission (value identical to option)";
$l_admin_role_get_admin_alert = "Get administrator alert messages";
$l_admin_role_send_alert_to_admin = "Can send alert to administrators";
$l_admin_role_broadcast_alert_to_group = "Can send alert to all from (same) group(s)";
$l_admin_role_broadcast_alert = "Can send alert to everybody";
$l_admin_role_offline_mode = "Force user to offline mode";
$l_admin_role_change_server_status = "Can change server status";
$l_admin_role_cannot_option = "Cannot active this role: the respective option is not enabled.";
$l_admin_role_cannot_option_see_default_role = "see also <i>default role</i>";

#Share files
$l_admin_share_files_title = "Shared files";
$l_admin_share_files_col_name = "File name";
$l_admin_share_files_col_size = "Size";
$l_admin_share_files_col_create = "Created";
$l_admin_share_files_col_add = "Added";
$l_admin_share_files_col_nb_download = "Number of downloads";
$l_admin_share_files_col_author = "Author";
$l_admin_share_files_col_recipient = "Recipient";
$l_admin_share_files_col_removal = "Removal";
$l_admin_share_files_col_projet = "Project";
$l_admin_share_files_col_hash = "File hash (MD5)";
$l_admin_share_files_cannot = "Access to share files is currently not activated";
$l_admin_share_files_empty = "Actually no file";
$l_admin_share_files_exchange = "Exchanged files";
$l_admin_share_files_trash = "Deleted sharing files trash";
$l_admin_share_files_trash_exchange = "Deleted exchanged files trash";
$l_admin_share_file_pending = "Sharing file(s) waiting Approval";
$l_admin_share_file_pending_exchange = "Exchanged file(s) waiting Approval";
$l_admin_share_file_valid_pending_files = "Validate all pending files";
$l_admin_share_file_clean_deleted = "Check and clear deleted files";
$l_admin_share_file_only_shared_files = "Without moderation or trash, only shared (published) files are displayed";
$l_admin_share_file_project_files_only = "This project files only";
$l_admin_share_file_project_list = "Projects list";
$l_admin_share_file_project_subfolder = "Subfolder";
$l_admin_share_file_project_col_end = "End";
$l_admin_share_file_project_col_closing = "Closing";
$l_admin_share_file_project_empty = "Actually no project...";
$l_admin_share_file_project_add_new = "Adding new project";
$l_admin_share_file_project_close_empty = "Users cannot add files to closed or empty projects name";
$l_admin_share_file_project_subfolder_must_exist = "Please check subfolders exist";
$l_admin_share_file_media = "Media";
$l_admin_share_file_compressed_file = "Compressed file";
$l_admin_share_file_protected_file = "Protected file";
$l_admin_share_file_cannot_display = "cannot display";
$l_admin_share_file_cannot_protect = "To protect files see";

#Files Backup
$l_admin_backup_files_cannot = "Files backup is currently not activated";

#ACP Authentication
$l_admin_acp_auth_title = "Authentication (ACP)";
$l_admin_acp_auth_error = "Authentication error...";
$l_admin_acp_auth_username = "Username";
$l_admin_acp_auth_password = "Password";
$l_admin_acp_auth_login = "Login";
$l_admin_remember_me = "Remember me";

#ACP Change password
$l_admin_acp_pass_changing = "Change Password";
$l_admin_acp_pass_1 = "Old password";
$l_admin_acp_pass_2 = "New password";
$l_admin_acp_pass_3 = "Confirm new password";

#ACP Administrators
$l_admin_acp_admin_title = "Manage administrators";
$l_admin_acp_admin_warning_1 = "NOTICE, option _ACP_PROTECT_BY_HTACCESS enabled.";
$l_admin_acp_admin_warning_2 = "Administrators authentication will be effective once the option disabled.";
$l_admin_acp_admin_list = "Administrators list";
$l_admin_acp_admin_list_empty = "Empty list";
$l_admin_acp_admin_create = "Create new administrators account";
$l_admin_acp_admin_at_least = "At least 6 characters alphanumeric";
$l_admin_acp_admin_right_on = "Activated right";
$l_admin_acp_admin_right_off = "Not activated right";
$l_admin_acp_admin_right_see_role = "Server option disabled: see roles for use this right";
$l_admin_acp_admin_right_no_option = "Cannot active right (see options)";
$l_admin_acp_admin_rights = "Rights";
$l_admin_acp_admin_right[1] = "Manage administrators";
$l_admin_acp_admin_right[2] = "Manage options";
$l_admin_acp_admin_right[4] = "Manage users: unlock";
$l_admin_acp_admin_right[8] = "Manage users: full access";
$l_admin_acp_admin_right[16] = "Manage user's contacts";
$l_admin_acp_admin_right[32] = "Manage avatars";
$l_admin_acp_admin_right[64] = "Manage groups";
$l_admin_acp_admin_right[128] = "Manage roles";
$l_admin_acp_admin_right[256] = "Manage ShoutBox";
$l_admin_acp_admin_right[512] = "Manage published files";
$l_admin_acp_admin_right[1024] = "Manage bookmars";
$l_admin_acp_admin_right[2048] = "Manage banned";
$l_admin_acp_admin_right[4096] = "Manage servers status";
$l_admin_acp_admin_right[8192] = "Admin messages";
$l_admin_acp_admin_right[16384] = "Admin messages: orders";
$l_admin_acp_admin_right[32768] = "Admin messages: emails";
$l_admin_acp_admin_right[65536] = "Server log: read";
$l_admin_acp_admin_right[131072] = "Server log: purge";
$l_admin_acp_admin_right[262144] = "";
$l_admin_acp_admin_right[524288] = "";
$l_admin_acp_admin_right[1048576] = "";

?>