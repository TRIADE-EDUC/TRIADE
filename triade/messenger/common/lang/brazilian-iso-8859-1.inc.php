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
# [BR]
# Tranlated by Maicon Fernando Abreu
# maicon@mfaconsult.com
#
$charset = 'iso-8859-1';
$l_lang_name = "Portuguese";
//$left_font_family = 'verdana, arial, helvetica, geneva, sans-serif';
//$right_font_family = 'arial, helvetica, geneva, sans-serif';

# commun
$l_legende = "Legenda";
$l_order_by = "Ordenar por :";
$l_date_format_display = "d-m-Y";
$l_time_format_display = "g:i:s A";
$l_time_short_format_display = "g:i A";
$l_admin_bt_delete = "Excluir";
$l_admin_bt_erase = "Apagar";
$l_admin_bt_update = "Atualizar";
$l_admin_bt_add = "Adicionar";
$l_admin_bt_create = "Criar";
$l_admin_bt_allow = "Permitir";
$l_admin_bt_invalidate = "Bloquear/Negar";
$l_admin_bt_search = "Procurar";
$l_admin_bt_empty = "Empty";
$l_language = "Idioma";
$l_country = "País";
$l_time_zone = "Fuso horário";
$l_server = "Servidor";
$l_clic_for_message = "Clique aqui para enviar mensagem adminitrativa para este(s) usuario(s)";
$l_clic_on_user = "Clic here to see user`s details";
$l_man = "Homem";
$l_woman = "Mulher";
$l_gender = "Gender";
$l_user_informations = "User informations";
$l_email = "Email";
$l_phone = "Telefone";
$l_display_col = "Display columns";
$l_display = "Display";
$l_hide = "Hide";
$l_configure = "Configurar";
$l_captcha = "Copy this security code";
$l_rows_per_page = "Rows per page";
$l_KB = "KB"; // Kilo Bytes
$l_relation_option = "Relationship with option : ";
$l_days = "dias";
$l_day_0 = "Segunda-feira";
$l_day_1 = "Terça-feira";
$l_day_2 = "Quarta-feira";
$l_day_3 = "Quinta-feira";
$l_day_4 = "Sexta-feira";
$l_day_5 = "Sábado";
$l_day_6 = "Domingo";

# Languages
$l_lng['FR'] = "francês";
$l_lng['GB'] = "inglês"; // EN
$l_lng['UK'] = "inglês"; // EN
$l_lng['DE'] = "alemão"; // GE
$l_lng['BR'] = "portugues";
$l_lng['PT'] = "portugues";
$l_lng['ES'] = "espanhol";
$l_lng['IT'] = "italiano";
$l_lng['FI'] = "finnish";
$l_lng['RO'] = "romanian";
$l_lng['TR'] = "turkish";
$l_lng['RS'] = "serbian";
$l_lng['RU'] = "russian";
$l_lng['NL'] = "Dutch";
//$l_lng[''] = "";

# level
$c_nb_level = 5;
$c_level[0] = "Administrador";
$c_level[1] = "Diretor";
$c_level[2] = "Gerente";
$c_level[3] = "Empregado";
$c_level[4] = "Convidado";
// CEO, Director, Branch manager, Administrator, Supervisor, Center manager, Service manager, Project manager, Sector manager, Manager, Employee, Guest.

#start
$l_start_cannot_authenticate = "Não foi possível autenticar"; 
$l_start_unknow_user = "usuario desconhecido";
$l_start_wait_valid = "usuario ainda não foi validado pelo administrador.";
$l_start_contact_admin_check = "entre em contato com seu administrador para confirmar sua alteração.";
$l_start_password = "senha incorreta.";
$l_start_max_users = "Impossível inicializar: não é possível adicionar mais usuarios: numero máximo excedido.";
$l_start_no_find_iduser = "identificação de usuario não encontrada.";
$l_start_short_username = "Impossível autenticar: sem login (nickname).";
$l_start_username_forbid = "Autenticação recusada: login (nickname) reservado (proibido).";
$l_start_username_forbid_by_admin = "Autenticação recusada: username (nickname) negado pelo administrador.";
$l_start_version_missing = "Autenticação recusada : versão do cliente antiga : necessita de atualização.";
$l_start_waiting_valid = "Locked user(s) waiting...";

# menu
$l_menu_list = "Lista";
$l_menu_index = "Index";
$l_menu_dash_board = "Dashboard";
$l_menu_currently = "Atuais";
$l_menu_list_sessions = "Sessoes";
$l_menu_conference = "Conferencias";
$l_menu_list_users = "Usuarios";
$l_menu_list_users_ip = "Mesmo IP endereço";
$l_menu_list_users_double = "Mesmo computador";
$l_menu_users_by_country = "Users by country";
$l_menu_list_contact = "Contatos dos usuarios";
$l_menu_list_conference_list = "Lista de conferencias";
$l_menu_list_group = "Grupos";
$l_menu_list_group_list = "Lista de grupos";
$l_menu_group_add_member = "Adicionar membro";
$l_menu_ban = "Ban control";
$l_menu_ban_user = "Users";
$l_menu_ban_ip = "IP address";
$l_menu_ban_pc = "Computers";
$l_menu_options = "Opções";
$l_menu_avatars = "Avatars";
$l_menu_messagerie = "Mensagem Aminstrativa";
$l_menu_statistics = "Estatisticas";
$l_menu_log = "Server log";
$l_menu_backup = "Backup database";
$l_menu_donate = "Doações";
$l_menu_donate_info = "All donate are welcome";
$l_menu_need_change_admin_dir = "TENHA CUIDADO ! Voce precisa renomear <B>/admin/</B> folder (before user can connect) !";
$l_menu_need_delete_install_dir = "Melhor apagar <B>/install/</B> folder (manter somete quando atualizar versao)";
$l_menu_maintenance_mode_on = "Be careful, maintenance mode is activated, users cannot communicate (and offline users cannot connect).";
$l_menu_need_htaccess = "NOTICE, administrator authentication is disabled (_ACP_PROTECT_BY_HTACCESS) <BR/> you should protect the <acronym title='Admin Control Panel'>ACP</acronym> by using htaccess security files!";
$l_menu_pass_root_empty = "O seu arquivo de configuração contém configurações (root sem senha) que correspondem à conta privilegiada padrão do MySQL. O servidor MySQL rodando com esse padrão estará aberto a invasões, você realmente deveria corrigir este furo de segurança."; 
$l_menu_need_reg = "Better to add the file im_setup.reg (more easy for end users to setup)";
$l_menu_no_javascript = "JavaScript not active, click here to display the menu correctly";
$l_menu_no_javascript_info = "JavaScript not active, cannot display menu on top...";
$l_menu_customize = "Customize";
$l_menu_customize_info = "Request an IntraMessenger client customized version";
$l_menu_bookmarks = "Bookmarks";
$l_menu_list_roles_list = "Lista de funções";
$l_menu_messagerie_instant = "Dirigir";
$l_menu_messagerie_emails = "Por e-mails";
$l_menu_logout = "Log out";
$l_menu_acp_auth = "Administrators";
$l_menu_manage = "Manage";

#
$l_pg_result = "Resultados";
$l_pg_show_result = "Mostrando resultados";
$l_pg_prev_page = "Pagina anterior";
$l_pg_next_page = "Próxima pagina";
$l_pg_first = "Primeiro";
$l_pg_first_page = "Primeira pagina";
$l_pg_last = "Ultima";
$l_pg_last_page = "Ultima pagina";
$l_pg_all = "Todas as paginas";
$l_pg_to = "para";
$l_pg_of = "de";

# index
$l_index_welcome = "Bem vindo a interface de administracao (<acronym title='Admin Control Panel'>ACP</acronym>)";
$l_index_can_cfg = "Voce pode configurar o arquivo de configuracao";
$l_index_can_lng = "como o idioma";
$l_index_actualy = "recentemente/atualmente";
$l_index_chg = "por alteracao";
$l_index_find_doc = "Voce pode achar documentacao de como instalar e configurar em";
$l_index_chk_opt = "Voce pode verificar as opcoes e configuracoes em";
$l_index_after_upd_chk = "Apos todas as atualizacoes, por favor verifque as configuracoes : ";
$l_index_waiting_valid = "Waiting admin valid users";
$l_index_ready_users = "Ready users";
$l_index_today_creat_users = "Today created users";
$l_index_today_sessions = "Today simultaneously sessions";
$l_index_last_valid_username = "Last registered user:";
$l_index_pending_avatars = "Pending avatars";
$l_index_records = "Records";
$l_index_full_list = "Complete list";
$l_index_users_per_day = "Users per day";
$l_index_created_users_per_day = "Created users per day";
$l_index_messages_per_day = "Messages per day";
$l_index_leave_users = "Users leave server";
$l_index_soon_dashboard_here = "After installation full completed, and maintenance mode disabled, dashboard will be displayed here.";
$l_old_files_to_delete = "Old files still exist and should be deleted";
$l_index_shoutbox_pending = "Shoutbox message(s) waiting approbation";
$l_index_shoutbox_nb_msg = "Messages number"; //  published
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
$l_admin_options_title = "Lista de opcoes";
$l_admin_options_title_2 = "Conselho";
$l_admin_options_update = "Atualização opcoes";
$l_admin_options_bt_update = "Salvar opcoes";
$l_admin_options_more = "Exibir mais opcoes";
$l_admin_options_title_table_2 = "Tela opcional de inicializacao (necessida da internet)";
$l_admin_options_col_option = "Opcao";
$l_admin_options_col_value = "Valor";
$l_admin_options_col_comment = "Comentario";
$l_admin_options_col_description = "Descrição";
$l_admin_options_general_options = "Opções gerais";
$l_admin_options_general_options_short = "Gerais";
$l_admin_options_maintenance_mode = "Maintenance mode : users cannot communicate (and offline users cannot connect)";
$l_admin_options_is_usernamePC = "Se ativo: nickname forcado para 'username' , ou, se nao ativo : o usuario pode escolher o nickname.";
$l_admin_options_auto_add_user = "Novos usuarios serao automaticamente adicionados.";
$l_admin_options_quick_register = "Quick registration required before automatically adding new users.";
$l_admin_options_need_admin_after_add = "Usuarios adicionados automaticamente precisam ser validados pelo admnistrador.";
$l_admin_options_need_admin_if_chang_check = "Usuarios que alterarem os nomes nos computadores precisam ser validados pelo administrador.";
$l_admin_options_log_session_open = "Sessao de LOG aberta.";
$l_admin_options_password_user = "Forca os usuarios a usar senha.";
$l_admin_options_password_for_private_server = "Se vazio, o servidor fica PUBLICO, se nao, fica sendo a senha para autenticacao no servidor.";
$l_admin_options_nb_max_user = "Maximo de usuarios registrados (0 : ilimitado).";
$l_admin_options_nb_max_session = "Maximo de sessoes (usuarios simultaneamente conectados) (0 : ilimitado).";
$l_admin_options_nb_max_contact_by_user = "Maximo de contatos por usuario (0 : ilimitado).";
$l_admin_options_del_user_after_x_days_not_use = "Contas ultrapassadas (para excluir pelo admin) se nao especificado existira x dias.";
$l_admin_options_force_away = "Forcar status 'away' (recolocar 'online') quando a protecao de tela estiver ativa.";
$l_admin_options_col_name_hide = "Esconder o col : nome/funcao";
$l_admin_options_col_name_default_active = "Se nao for escondida, padrao sera mostrar col nome/funcao.";
$l_admin_options_allow_invisible = "Permite os usuarios ficarem invisiveis (online escondido) para alguns contatos.";
$l_admin_options_can_change_contact_nickname = "Permite usuarios renomear seus contatos.";
$l_admin_options_allow_change_contact_list = "Allow users to manage their contacts and alarms";
$l_admin_options_allow_change_options = "Allow users to manage their options and alarms";
$l_admin_options_allow_change_profile = "Allow users to manage their profile";
$l_admin_options_crypt_msg = "Criptografar mensagens";
$l_admin_options_log_messages = "Salva (log) no servidor - todas as mesagens (se nao criptografadas).";
$l_admin_options_censor_messages = "Censurando mensagens (se nao criptografadas) : <I>/common/config/censure.txt</I>";
$l_admin_options_site_url = "Website URL (endereco)";
$l_admin_options_site_title = "Titulo Website";
$l_admin_options_missing_option = "Opcao(es) faltando em";
$l_admin_options_conf_file = "arquivo de configuracao";
$l_admin_options_flag_country = "Mostra a bandeira do pais e o IP (usa internet).";
$l_admin_options_legende_empty = "opcao nao ativada"; // (vazio)
$l_admin_options_legende_not_empty = "opcao ativada"; // (preenchido)
$l_admin_options_legende_up2u = "Up para voce";
$l_admin_options_special_options = "Opcoes Especiais";
$l_admin_options_special_modes = "Special modes";
$l_admin_options_normal_mode = "Everyone sees only his (validated) contacts.";
$l_admin_options_opencommunity = "Todo mundo pode ver todo mundo, sem adicionar a lista de contatos.";
$l_admin_options_groupcommunity = "Todo mundo pode ver todo mundo (somente) do mesmo grupo(s).";
$l_admin_options_opengroupcommunity = "Everybody can see everybody, display by groups.";
$l_admin_options_statistics = "Para mostrar estatistica (na area admin).";
$l_admin_options_info_1 = "Se ambos vazio, sem tela de abertura no cliente";
$l_admin_options_info_2 = "Voce pode ativar um (ou mais) desta opcao";
$l_admin_options_info_2b = "Voce pode ativar um desta opcao";
$l_admin_options_info_3 = "Nao pode gerar LOG e criptograr a mensagem (escolha somente uma opcao) !";
$l_admin_options_info_4 = "Nao se pode usar simultaneamente 2 modulos: grupo e comunidade aberta!";
$l_admin_options_info_5 = "Não é necessario ativar esta opcao";
$l_admin_options_info_6 = "Manipular lista de nickname banido no arquivo";
$l_admin_options_info_7 = "Sua opcao habilita voce para registro em";
$l_admin_options_info_8 = "Sua opcao NAO habilita voce para registro em";
$l_admin_options_info_9 = "ativado : checando configuracoes das opcoes :";
$l_admin_options_info_book = "internet IntraMessenger servidor de diretorio publico";
$l_admin_options_info_10 = "Autenticacao externa";
$l_admin_options_info_11 = "somente um! Obrigado por limpar/atualizar a configuracao !";
$l_admin_options_info_12 = "Do not activate both of these options";
$l_admin_options_info_13 = "Red displayed options, manualy updatable only in configuration file.";
$l_admin_options_check_new_msg_every = "Intervalo entre checagem por nova mensagem primeira (10 a 60 segundos).";
$l_admin_options_minimum_length_of_username = "Minimo de caracterres do usuario (nickname).";
$l_admin_options_full_check = "Check for waiting contacts every 3 minutes only.";
$l_admin_options_minimum_length_of_password = "Minimo de caracteres da senha do usuario.";
$l_admin_options_max_pwd_error_lock = "Maximum password consecutive errors, before server lock user";
$l_admin_options_user_history_messages = "Permite aos usuarios para o historico de LOG (log/save) das mensagens.";
$l_admin_options_user_history_messages_export = "Allow export archived messages.";
$l_admin_option_allow_conference = "Permite aos usuarios criar conferencias.";
$l_admin_option_send_offline = "Permite aos usuarios enviar mensagens para contatos offline.";
$l_admin_options_allow_smiley = "Allow send smileys (display by pictures).";
$l_admin_options_allow_change_email_phone = "Allow users to change their phone number and email address.";
$l_admin_options_allow_change_function_name = "Allow users to change their name/function (display behind username).";
$l_admin_options_allow_change_avatar = "Allow users to change their avatar (picture).";
$l_admin_options_allow_use_proxy = "Allow users to use proxy server.";
$l_admin_extern_url_to_register = "URL (endereco) para registro (forum, CMS...) para autenticacao";
$l_admin_extern_url_password_forget = "URL (endereco) to getback forgotten password (by extern authentication)";
$l_admin_extern_url_change_password = "URL (endereco) to change password (by extern authentication)";
$l_admin_options_autentification = "Autenticacao";
$l_admin_options_security_options = "Opcoes de Seguranca";
$l_admin_options_security = "Seguranca";
$l_admin_options_admin_options = "Opcoes Administrativas";
$l_admin_options_force_update_by_server = "Forca o cliente a ser atualizado pelo servidor.";
$l_admin_options_force_update_by_internet = "Force clients to update from official internet server.";
$l_admin_options_user_restrictions_options = "User restrictions options";
$l_admin_options_user_restrictions_options_short = "User restrictions";
$l_admin_options_hierachic_management = "Enable hierarquia, display nivel col (area de admin).";
$l_admin_authentication_extern = "Autenticacao (login + senha) externo pela";
$l_admin_options_public_see_options = "Options list is public";
$l_admin_options_public_see_users = "Users list is public";
$l_admin_options_public_upload_avatar = "Everybody can propose avatars";
$l_admin_options_admin_email = "Email do administrador";
$l_admin_options_admin_phone = "Número de telefone do administrador";
$l_admin_options_public_folder = "Public folder (display options, users...)";
$l_admin_options_scroll_text = "Scrolling information message (temporary)";
$l_admin_options_uppercase_space_nickname = "Allow uppercase and space in nickname.";
$l_admin_options_allow_email_notifier = "Allow users to use email notifier.";
$l_admin_options_force_email_server = "Incoming mail server address (for notifier)";
$l_admin_options_enterprise_server = "Enterprise mode : get installed software versions and can stop/reboot computers";
$l_admin_options_allow_rating = "Allow users to rate their contacts (and see average if 'PUBLIC').";
$l_admin_options_proxy_address = "Force proxy server address.";
$l_admin_options_proxy_port_number = "Force proxy server port number.";
$l_admin_options_max_simultaneous_ip_addresses = "Maximum simultaneous IP addresses (0 : unlimited)";
#$l_admin_options_group_for_admin_messages = "Allow to manage groups, only to send admin messages";
$l_admin_options_group_for_sbx_and_admin_messages = "Allow (admin) manage groups, for ShoutBox and send admin messages";
$l_admin_options_group_for_admin_messages_2 = "use only if _SPECIAL_MODE_GROUP_COMMUNITY empty";
$l_admin_options_cannot_access_to = "but cannot access to";
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
$l_admin_options_other_options = "Other";
$l_admin_options_other_options_options = "Other options";
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
$l_admin_users_title = "Lista de Usuarios";
$l_admin_users_col_user = "Usuario";
$l_admin_users_col_function = "Nome/funcao";
$l_admin_users_col_level = "Nivel";
$l_admin_users_col_etat = "Status";
$l_admin_users_col_etat_wait = "Aguardando 'status'";
$l_admin_users_col_creat = "Adicionado";
$l_admin_users_col_last = "Ultimo";
$l_admin_users_col_action = "Acao";
$l_admin_users_col_password = "Senha";
$l_admin_users_col_activity = "Activity";
$l_admin_users_col_version = "Versao";
$l_admin_users_col_pc = "Computer";
$l_admin_users_col_mac_adr = "MAC address";
$l_admin_users_col_screen = "Resolution";
$l_admin_users_col_emailclient = "E-mail client";
$l_admin_users_col_browser = "Web browser";
$l_admin_users_col_ooo = "OOo";
$l_admin_users_col_backup = "Backup";
$l_admin_users_info_wait_valid = "Esperando por validacao";
$l_admin_users_info_change_ok = "Alterando a validacao do PC";
$l_admin_users_info_locked = "Locked";
$l_admin_users_info_valid = "Validado";
$l_admin_users_info_leave = "Leave server";
$l_admin_users_order_login = "login";
$l_admin_users_order_function = "nome/funcao";
$l_admin_users_order_state = "status";
$l_admin_users_order_creat = "data de adicao";
$l_admin_users_order_last = "ultima data de uso";
$l_admin_users_order_last_activity = "last activity";
$l_admin_users_order_level = "nivel";
$l_admin_users_order_role = "role";
$l_admin_users_add_new = "Adicionar novo usuario";
$l_admin_users_cannot_add = "Nao é possivel: numero maximo de usuarios atingido.";
$l_admin_users_to_add_more_1 = "Para adicionar mais, altere a opcao: <I>_MAX_NB_USER</I>.";
$l_admin_users_to_add_more_2 = "Para adicionar manualmente, voce precisa remover esta opcao.";
$l_admin_users_no_add_1 = "Adicionados sem uso";
$l_admin_users_no_add_2 = "A opcao (<I>_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER</I>) para adicao automatica <BR/> de novos usarios esta ativada.";
$l_admin_users_out_of_date = "Contas fora da validade";
$l_admin_users_no_out_of_date = "Sem contas fora da validade";
$l_admin_users_for_out_of_date_1 = "As contas ficaram sem validade se NAO usadas durante";
$l_admin_users_for_out_of_date_2 = "dias (na lista de opcao)";
$l_admin_users_info_level = "Atencao : usuarios podem requisitar adicao de contatos somente para usuarios de niveis menores ou iguais!";
$l_admin_users_info_nm_function = "Anotar : mesma col nome/funcao oculto, nome/funcao continua mostrada na lista de contatos";
$l_admin_users_searching = "Procurar usuario";
$l_admin_users_no_found = "Usuario NAO encontrado";
$l_admin_users_send_admin_message = "Send an admin message";
$l_admin_users_nb_connect = "Conexões (dias)";
$l_admin_users_admin = "Administrator";
$l_admin_users_admin_alert = "Get alert messages";
$l_admin_users_not_admin = "Not an administrator";
$l_admin_users_hide_from_other = "Hidden from others";
$l_admin_users_auto_add_user_for_ext_auth = "Do not empty this option (essential for extern authentication) !";
$l_admin_users_ban_user = "Ban this username";
$l_admin_users_ban_ip = "Ban this IP address";
$l_admin_users_ban_pc = "Ban this computer";
$l_admin_users_pc_banned = "Banned computer";
$l_admin_users_user_banned = "Banned username";
$l_admin_users_ip_banned = "Banned IP address";
$l_admin_users_pc_title = "Computer list";
$l_admin_users_how_to_ban_pc = "Use this button to ban a computer<BR/>(in details)";
$l_admin_users_participation = "Participation/presence rate";
$l_admin_users_reputation = "Reputation"; // popularity 
$l_admin_users_state_on = "On";
$l_admin_users_state_off = "Off";
$l_admin_users_state_sleep = "Sleeping (or bug)";
$l_admin_users_rating = "Highest score";
$l_admin_users_empty = "Actually no user...";
$l_admin_received = "received";
$l_admin_sent = "sent";

#admin contacts list screen
$l_admin_contact_title = "Lista de contatos";
$l_admin_contact_col_contact = "Contatos";
$l_admin_contact_col_state = "Status";
$l_admin_contact_col_action = "Acao";
$l_admin_contact_bt_forbid = "Proibido";
$l_admin_contact_info_wait_valid = "Nao validado";
$l_admin_contact_info_ok = "Validado";
$l_admin_contact_info_vip = "Privilegiado";
$l_admin_contact_info_hidden = "Esconder online status (invisivel)";
$l_admin_contact_info_refused = "Definitivamente recusado";
$l_admin_contact_add_contact = "Adicionar novos contatos";
$l_admin_contact_auto_add = "(automaticamente adicionados)";
$l_admin_contact_no_add_1 = "Adicionados sem uso";
$l_admin_contact_no_add_2b = "The option (<I>_ALLOW_MANAGE_CONTACT_LIST</I>) to allow users to add contacts is enabled.";
$l_admin_contact_no_add_3b = "To manually add, you need to disable this option.";
$l_admin_contact_cannot_use = "Nao foi possivel usar a lista de contatos : opcao _SPECIAL_MODE_GROUP_COMMUNITY esta ativada.";
$l_admin_contact_average_1 = "Media";
$l_admin_contact_average_2 = "Ativar contatos por usuario";
$l_admin_contact_total = "total";
$l_admin_contact_bt_avatar = "Choose an avatar";
$l_admin_contacts = "Contato(s)";
$l_admin_contact_empty = "Actually no contact...";

#admin sessions list screen
$l_admin_session_title = "Lista de Sessoes";
$l_admin_session_title_2 = "Sessoes corrente";
$l_admin_session_at = "em";
$l_admin_session_col_state = "Status";
$l_admin_session_col_user = "Usuario";
$l_admin_session_col_function = "Nome/funcao";
$l_admin_session_col_ip = "endereco IP";
$l_admin_session_col_begin = "Começo";
$l_admin_session_col_last = "Ultimo";
$l_admin_session_col_version = "Versao";
$l_admin_session_info_not_connect = "NAO conectado";
$l_admin_session_info_online = "Ativo"; // Available
$l_admin_session_info_away = "Ausente";
$l_admin_session_info_busy = "Ocupado";
$l_admin_session_info_do_not_disturb = "Nao perturbe";
$l_admin_session_order_user = "usuario";
$l_admin_session_order_state = "status";
$l_admin_session_no_session = "Atualmente sem secao";
$l_admin_session_col_time = "Tempo";
$l_admin_session_col_state_reason = "Reason";

#admin messenger screen
$l_admin_mess_title = "Mensagem adminitrativa";
$l_admin_mess_title_2 = "Enviar uma mensagem de informacao (como administrador)";
$l_admin_mess_title_3 = "Mensagens nao lidas ainda";
$l_admin_mess_title_4 = "Escolha uma imagem para enviar (png jpg gif)";
$l_admin_mess_message = "Mensagem";
$l_admin_mess_to = "Destinatario(s)";
$l_admin_mess_only = "Somente";
$l_admin_mess_all_connected = "Todos os usuarios conectados";
$l_admin_mess_all = "Todos usuarios ( incluindo usuarios desconetados)";
$l_admin_mess_group = "All members of the group";
$l_admin_mess_group_connected = "All connected members of group";
$l_admin_mess_bt_send = "Enviar";
$l_admin_mess_nb_send = "mensagem(s) acabou de ser enviada";
$l_admin_mess_bt_refresh = "Recarregar";
$l_admin_mess_time = "Tempo";
$l_admin_mess_no_wait = "Atualmente sem mensagens na espera";
$l_admin_mess_dir = "Imagens das pastas";
$l_admin_mess_select = "Selecionar";
$l_admin_mess_title_5 = "Send an order";
$l_admin_mess_order = "Order";
$l_admin_mess_stop_pc = "Shutdown computer";
$l_admin_mess_boot_pc = "Reboot computer";
$l_admin_mess_boot_im = "Reboot IM";
$l_admin_mess_cannot_order = "Cannot use : option _ENTERPRISE_SERVER is not activated";
$l_admin_mess_image_only = "Pictures only (.gif .jpg .jpeg .png) without space in filename";

#admin message email screen
$l_admin_mess_email_title = "Send an information email";

#admin group manage
$l_admin_group_title = "Grupo de usuarios";
$l_admin_group_title_2 = "Manusear grupo de usuarios";
$l_admin_group_no_group = "Atualmente sem grupos";
$l_admin_group_no_user_group = "Atualmente sem membro grupos";
$l_admin_group_col_group = "Grupo";
$l_admin_group_creat_group = "Criar novo grupo";
$l_admin_group_rename_group = "Renomear grupo";
$l_admin_group_title_add_to_group = "Adicionar membros para grupo";
$l_admin_group_new_name = "Novo nome";
$l_admin_group_add_to_group = "Adicionar para grupo";
$l_admin_group_order_group = "grupo";
$l_admin_group_cannot_use_1 = "NAO e possivel usar os grupos: opcao _SPECIAL_MODE_GROUP_COMMUNITY NAO esta ativada (tenha cuidado, a lista de contatos sera desabilitada).";
$l_admin_group_cannot_use_2 = "Nao obstante, usuarios podem adicionar grupos eles mesmo em suas listas de contatos.";
$l_admin_group_members = "Membros";
$l_admin_group_public = "Public";
$l_admin_group_official = "Official";
$l_admin_group_private = "Private";
$l_admin_group_public_legende = "users can directly join group.";
$l_admin_group_official_legende = "users can request to join group (validated by admin).";
$l_admin_group_private_legende = "users cannot see group and cannot join.";

#admin statistics screen
$l_admin_stats_title = "Estatisticas";
$l_admin_stats_col_date = "Data";
$l_admin_stats_col_nb_msg = "Mensagem";
$l_admin_stats_col_nb_creat = "Usuarios criados";
$l_admin_stats_col_nb_session = "Sessoes simultaneas";
$l_admin_stats_col_nb_users = "Ususarios";
$l_admin_stats_col_nb_msg_sbx = "ShoutBox Messages";
$l_admin_stats_no_stats = "Atualmente sem estatisticas";
$l_admin_stats_option_not = "Opcao nao ativada";
$l_admin_stats_rate = "maximo valor de";
$l_admin_stats_by_day = "Vista pelo dia";
$l_admin_stats_by_week = "Vista pela semana";
$l_admin_stats_by_month = "Vista pelo mês";
$l_admin_stats_by_year = "Vista pelo ano";
$l_admin_stats_average = "média";
$l_admin_stats_day_of_week = "By day of week";
$l_admin_stats_latest = "Latest";
$l_admin_stats_click_drag_to_zoom = "Click and drag in the plot area to zoom in";
$l_admin_stats_click_to_show_hide = "Click on legend to display/hide";
$l_admin_stats_empty = "Need to use more days to get statistics...";

#admin conference screen
$l_admin_conference_title = "Conferencias";
$l_admin_conference_cannot_use_1 = "NAO foi possivel usar: opcao _ALLOW_CONFERENCE NAO esta ativada.";
$l_admin_conference_col_creator = "Criador";
$l_admin_conference_col_partaker = "Compartilhados";
$l_admin_conference_no_conference = "Atualmente sem conferencias";

#admin change avatar screen
$l_admin_avatar_title = "Change avatar/picture";
$l_admin_avatar_title_2 = "Select another avatar (or picture)";
$l_admin_avatar_title_3 = "Add another avatar (or picture) to the list";
$l_admin_avatar_title_4 = "Select avatar (or picture) to delete";
$l_admin_avatar_title_5 = "Waiting admin valid avatars";
$l_admin_avatar_title_6 = "Unacceptable avatar list (e.g. dimensions)";
$l_admin_avatar_bt_download = "Download more avatars";
$l_admin_avatar_info_1 = "Put pictures (or avatars) in folder";
$l_admin_avatar_images_filter = "Filter files such as images only";

#admin htaccess create
$l_admin_htaccess_1 = "Files <I>.htaccess</I> and <I>.htpasswd</I> allow you to protect you admin folder";
$l_admin_htaccess_2 = "(first one contain security policy, second one users and password).";
$l_admin_htaccess_3 = "Use the create button to create a default user (must update after !)";
$l_admin_htaccess_4 = "To (try to) delete them, clic on ";
$l_admin_htaccess_create_files = "Create files <I>.htaccess</I> and <I>.htpasswd</I>";
$l_admin_htaccess_warning = "WARNING, delete this two files before updating the server address/url (or ACP folder).";
$l_admin_htaccess_cannot = "Cannot use: option _ACP_PROTECT_BY_HTACCESS is not activated.";

#admin log screen
$l_admin_log_title = "Display server logs";
$l_admin_log_title_admin = "Display admin server logs";
$l_admin_log_select = "Select log server to display";
$l_admin_log_hack = "Hack attempt";
$l_admin_log_error_log = "Error log";
$l_admin_log_error_log_connection = "Connections error log";
$l_admin_log_type_error = "Error";
$l_admin_log_type_warning = "Warning/forbidden";
$l_admin_log_type_info = "Information";
$l_admin_log_type_monitor = "Monitor";
$l_admin_log_session_open = "Sessions open";
$l_admin_log_password_errors = "Password errors";
$l_admin_log_lock_user_password = "Locked users for password errors";
$l_admin_log_check_change = "Config change";
$l_admin_log_change_nickname = "User change nickname";
$l_admin_log_upload_avatar = "Avatars upload";
$l_admin_log_username_unknown = "Unknown users";
$l_admin_log_reject_username = "Users nickname prohibited";
$l_admin_log_reject_ip = "IP addresses IP banned";
$l_admin_log_reject_pc = "Computer banned";
$l_admin_log_reject_max_same_ip = "Max same IP address usage";
$l_admin_log_reject_max_same_pc = "Max simultaneous of a single PC usage";
$l_admin_log_reject_max_users = "Reject : maximum number of registered users reached";
$l_admin_log_server_full = "Reject : server full";
$l_admin_log_no_ip_address = "No IP address";
$l_admin_log_version_to_old = "Version to old";
$l_admin_log_private_password = "Private password error";
$l_admin_log_user_create = "Created users";
$l_admin_log_user_allow = "Users allowed";
$l_admin_log_user_disallow = "Users disallowed";
$l_admin_log_user_delete = "Users deleted";
$l_admin_log_user_avatar_valid = "Valid pending avatars";
$l_admin_log_send_order = "Orders sended";
$l_admin_log_send_message = "Admin messages sended";
$l_admin_log_ban_ip_address = "Banned ip addresses";
$l_admin_log_unban_ip_address = "Unbanned ip addresses";
$l_admin_log_ban_username = "Banned usernames";
$l_admin_log_unban_username = "Ubanned usernames";
$l_admin_log_ban_computer = "Banned computers";
$l_admin_log_unban_computer = "Unbanned computers";
$l_admin_log_user_admin_alert_get = "User get now admin alerts";
$l_admin_log_user_admin_alert_not_get = "User stop get admin alerts";
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
$l_admin_check_title = "Check configuration (after all updates)";
$l_admin_check_conf_file = "Configuration file";
$l_admin_check_not_found = "Not found !";
$l_admin_check_found = "found";
$l_admin_check_on = "on";
$l_admin_check_off = "off";
$l_admin_check_before_upgrade = "Before upgrade";
$l_admin_check_read_last = "read last";
$l_admin_check_last_options = "Check last options";
$l_admin_check_new_options_are = "All new options are";
$l_admin_check_in_conf_file = "in configuration file";
$l_admin_check_mysql = "Check MySQL server connection";
$l_admin_check_connect_server = "Connection to server";
$l_admin_check_failed = "failed";
$l_admin_check_cannot_continue = "Cannot continue tests without";
$l_admin_check_language_file = "language file";
$l_admin_check_connect_to_server = "connection to server";
$l_admin_check_connect_to_database  = "connection to database";
$l_admin_check_missing_option = "missing option";
$l_admin_check_all_tables = "all tables in database";
$l_admin_check_version = "MySQL version";
$l_admin_check_connect_database = "Connection to database";
$l_admin_check_option_missing = "Option missing in file";
$l_admin_check_tables_list = "Check tables list";
$l_admin_check_table = "Table";
$l_admin_check_tables_ok = "All tables exist";
$l_admin_check_use = "Use";
$l_admin_check_in_admin = "in MySQL admin (e.g. PHPMyAdmin)";
$l_admin_check_to_create_table = "to create tables";
$l_admin_check_tables_structure = "Check tables structure";
$l_admin_check_tables_structure_are = "All existing tables structures are";
$l_admin_check_col = "col"; // column
$l_admin_check_for_structure = "to update table structure";
$l_admin_check_update_now = "Update now";
$l_admin_check_conf_not_ok = "Configuration : NOT correct : you must update configuration !";
$l_admin_check_folders = "Check folders";
$l_admin_check_folder = "Folder";
$l_admin_check_not_writeable = "not writeable";
$l_admin_check_history = "Please read version history in file";
$l_admin_check_conf_ok = "Configuration/update is OK";
$l_admin_check_can_go = "you can go now to";
$l_admin_check_admin_panel = "admin panel";
$l_admin_check_optimize_tables = "Optimize tables";
$l_admin_check_tables_are_optimized = "All tables are just optimized";
$l_admin_check_system_info = "System informations";
$l_admin_check_incomplete = "incomplete";
$l_admin_check_fix_missing_option = "To fix it, just save options";

#admin save database
$l_admin_save_title = "Backup database";
$l_admin_save_bt_now = "Backup now";
$l_admin_save_selet_to_restore = "Select backup to restore";
$l_admin_save_bt_restore = "Restore";
$l_admin_save_list = "Backups list";
$l_admin_save_not_in_maintenance = "Cannot restore : maintenance mode enabled.";
$l_admin_save_cannot_use = "Cannot use";
$l_admin_save_do_not_use = "Do not use";

#admin ban control
$l_admin_ban_users = "Usernames ban";
$l_admin_ban_ip = "IP address ban";
$l_admin_ban_pc = "Computers ban";
$l_admin_ban_add_user = "Add a username to ban";
$l_admin_ban_add_ip = "Add a IP address to ban";
$l_admin_ban_add_pc = "Add a computers to ban ";
$l_admin_ban_dont_need_file = "NOTICE, the file zzz is no longer necessary, replaced here, dont forget to delete it."; // do not change "zzz" !!!
$l_admin_ban_import_delete = "Import and (try to) delete the file";

#install
$l_install_check_files = "Check files";
$l_install_file = "File";
$l_install_bt_next = "Continue";
$l_install_step = "Step";
$l_install_check_cannot_continue = "Cannot continue install without";
$l_install_not_in_maintenance_mode = "Your server is not in maintenance mode <SMALL>(<I>_MAINTENANCE_MODE</I> in configuration file)</SMALL>";
$l_install_warning = "Can be dangerous to apply upgrade <B>now</B>.";

#home
$l_home_not_configured = "Instant messenger server not configured yet...";
$l_home_welcome = "Welcome on your instant messenger server";
$l_home_thanks_to_first = "Thanks to first";
$l_home_here_register = "Clic here to register";
$l_home_register = "register";
$l_home_download_execute = "Download and run";
$l_home_before_install = "<B>BEFORE</B> install<BR/> for easy setup and avoid last step";
$l_home_download_install = "Download IntraMessenger (setup/install)";
$l_home_or = "or";
$l_home_download_zip = "Download IntraMessenger (zip version)";
$l_home_on_startup_config_url = "On IntraMessenger startup, configure the address (<I>URL</I>)";
$l_home_replace = "Replace";
$l_home_by_ip_address = " by server IP address to connect <blink>from ANOTHER computer</blink>";

#admin display
$l_admin_display_title = "Mostrar";
$l_admin_display_options = "Mostrar Opções";
$l_admin_display_menu = "Menu";
$l_menu_top = "Mostrar menu para o topo";
$l_menu_left = "Mostrar menu para a esquerda";
$l_menu_right = "Mostrar menu para a direita";
$l_menu_full = "Mostrar Menu Completo";
$l_menu_not_full = "Necessário mostrar menu";
$l_admin_display_style = "Estilo";
$l_admin_display_style_select = "Selecione o estilo";
$l_admin_display_background_color = "Cor de fundo";
$l_admin_display_color_select = "Selecione o cor";
$l_color_blue = "Azul";
$l_color_green = "Verde";
$l_color_pink = "Rosa";
$l_color_red = "Vermelho";
$l_color_yellow = "Amarelo";
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