// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ChatUsersList.js,v 1.9 2020/09/25 07:50:13 moble Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/request',
        'dojo/json', 
        'dijit/_WidgetBase',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dijit/layout/BorderContainer',
        'dijit/layout/ContentPane',
        'dijit/form/CheckBox',
        'dojo/_base/event',
        'dijit/form/Button',
        'apps/chat/Chat',
        'dojo/dom-attr',
        'apps/chat/ChatGroup',
        'dijit/form/ToggleButton'
], function(
		declare, 
		dom, 
		on, 
		lang, 
		topic, 
		request, 
		json, 
		WidgetBase,
		domConstruct, 
		domStyle, 
		BorderContainer, 
		ContentPane,
		CheckBox,
		event,
		Button,
		Chat,
		domAttr,
		ChatGroup,
		ToggleButton) {
	return declare([WidgetBase], {
		
		readFilter: null,
		connectedFilter: null,
		container: null,
		domNode: null,
		topContainer: null,
		searchContainer: null,
		userListContainer: null,
		expandCollspace: 0,
		ChatsList: new Array(),
		ChatGroup: null,
		data: null,
		constructor: function() {			
		},	

		postCreate: function() {
			this.inherited(arguments);	
			this.own(
				topic.subscribe('ChatController', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Chat', lang.hitch(this, this.handleEvents))
			);
			this.chatGroup = new ChatGroup();
			//impossible d'utiliser startup pour appeler la transfomration en element dragable
			//on doit donc attendre que l'élément soit créé dans le DOM
			setTimeout(function(){ 
				dragElement();
			}, 4000);
		},
		
		startup: function(){
			
		},
		
		handleEvents: function(evtType,evtArgs) {
			switch(evtType) {
				case 'ChatController_SendResponses':					
					this.genList(evtArgs.data);
					break;
				case 'chat_closeChat':					
					this.closeChat(evtArgs);
					break;
			}			
		},

		genList: function(data) {
			if(JSON.stringify(this.data) === JSON.stringify(data)) return '';
			this.data = data;
			users_list = data.users_list;			
			if (!dom.byId('chatUsersListWindow')) {			
				this.domNode = domConstruct.create('div', {id: 'chatUsersListWindow', style: 'width: 300px; height: 50px; position: fixed; bottom:0; right:0; z-index: 4;'}, 'att');
				this.container = new BorderContainer({id:'chatBorderContainerId', style: 'height: 100%; width: 100%;', design:'headline', gutters:true, liveSplitters:true});
				
				this.topContainer = new ContentPane({
					id: 'headerUsersList',
					region: 'top',
					content: this.getHeaderList(data),
			        style: 'height: 16px;'
				});								
				this.userListContainer = new ContentPane({
					id: 'chatUsersList',
					region: 'center',
					splitter: 'true',
			        style: 'width: 100%; background-color: #f1f1f1;'
				});
				this.container.addChild(this.topContainer);
				this.container.addChild(this.userListContainer);				
				this.container.placeAt(this.domNode);

				if(!data.users_list_state.expandCollspace) data.users_list_state.expandCollspace = 0;
				this.expandCollspace = data.users_list_state.expandCollspace;				
				this.setExpandCollspace(users_list, 1);
			}
			if (dom.byId('chatUsersList')) {				
				var scrollTop = dom.byId('chatUsersList').scrollTop;
			}			
			this.userListContainer.set('content', this.getTabList(users_list));
			var groupList = this.getGroupsList(data);
			domConstruct.place(groupList, dom.byId('chatUsersListContent'), 'before');
			this.userListContainer.resize();	
			this.container.startup();
			this.container.resize();		
			dom.byId('chatUsersList').scrollTop = scrollTop;	
		},

		getGroupsList: function(data) {						
			var content = domConstruct.create('div', {});
			var table = domConstruct.create('table', {}, content);
			for (var i = 0; i < data.groups_list.users.length; i++) {
				var tr = domConstruct.create('tr', {}, table);				
				
				var td = domConstruct.create('td', {innerHTML: data.groups_list.users[i].chat_group_name, style:'cursor: pointer', title: pmbDojo.messages.getMessage('chat', 'chat_open_discussion')}, tr);
				on(td, 'click', lang.hitch(this, this.openChat, data.groups_list.users[i]));
				
				var editGroup = domConstruct.create('img', {	
						className: 'chat_edit_group',                
		                src: "./images/group_msg.png",
			            title: pmbDojo.messages.getMessage('chat', 'chat_edit_group'),
	                }
				);
				on(editGroup, 'click', lang.hitch(this, this.addNewGroup, data.users_list, data.groups_list.users[i]));
				
				domConstruct.place(editGroup, domConstruct.create('td', {}, tr));

				var notification = '';
				if (data.users_list.notifications.groups[data.groups_list.users[i].chat_user_group_num]) {
					notification+= " <img src='./images/msg_user.png' title='" + pmbDojo.messages.getMessage('chat', 'chat_notification') + "'>(" + data.users_list.notifications.groups[data.groups_list.users[i].chat_user_group_num] + ")";
				}				
				var td = domConstruct.create('td', {innerHTML: notification, style:'cursor: pointer', title: pmbDojo.messages.getMessage('chat', 'chat_open_discussion')}, tr);
				on(td, 'click', lang.hitch(this, this.openChat, data.groups_list.users[i]));
			}
			return content;
		},
		
		setExpandCollspace: function(users_list, noPublish) {
			if(this.expandCollspace != 1) {
				domStyle.set(dom.byId('chatUsersList'), 'display', 'none');
				domStyle.set(dom.byId('chatUsersListWindow'), 'height', '50px');
				var img_src = './images/plus_msg.png';
			} else {
				domStyle.set(dom.byId('chatUsersList'), 'display', 'block');
				domStyle.set(dom.byId('chatUsersListWindow'), 'height', '150px');
				var img_src = './images/moins_msg.png';
			}
			domAttr.set('expandCollspaceButton', 'data-checked', this.expandCollspace);
			domAttr.set('expandCollspaceButton', 'src', img_src);
			
			this.container.resize();		
			if (noPublish != 1) {
				topic.publish('ChatUsersList', 'chatUsersList_expandCollspace', {
						id: 0, 
						expandCollspace: this.expandCollspace,
					}
				);
			}
			this.expandCollspace = 1 - this.expandCollspace;		
		},
		
		getState: function() {
			var expandCollspace = 0;
			if (domStyle.get('chatUsersList', 'display') == 'block') {
				expandCollspace = 1;
			}
			return {
				id: 0, 
				expandCollspace: expandCollspace,
				chatOnlineFilter: domAttr.get('chatOnlineFilter', 'data-checked'),					
				chatNotifFilter: domAttr.get('chatNotifFilter', 'data-checked'),				
			};
		},
		
		getHeaderList: function(data) {
			users_list = data.users_list;			
			var content = domConstruct.create('div', {});
		
			var expandCollspace = domConstruct.create('img', {
                id: 'expandCollspaceButton',	
				className: 'expandCollspaceButton',        
	            alt: pmbDojo.messages.getMessage('chat', 'chat_users_list_expand'),
	            title: pmbDojo.messages.getMessage('chat', 'chat_users_list_expand'),
		        style: "float:right;",
                }
			);
			on(expandCollspace, 'click',  lang.hitch(this, function() {
				this.setExpandCollspace(users_list, 0);				
	        }));
			domConstruct.place(expandCollspace, content);
			
			var img_src = './images/move.png';	
			var dragButton = domConstruct.create('img', {
                id: 'dragButton',	
				className: 'dragButton',
				src: img_src,
	            alt: pmbDojo.messages.getMessage('chat', 'chat_dragable'),
	            title: pmbDojo.messages.getMessage('chat', 'chat_dragable'),
		        style: "float:right; height:20px; margin-right:20px;",
                }
			);
			
			domConstruct.place(dragButton, content);
			
			var value = 0;
			var img_src = './images/new_msg_tgl.png';
			if (data.users_list_state.chatNotifFilter == 1) {
				value = 1;
				img_src = './images/new_msg.png';
			}
			var checkBoxNotifFilter = domConstruct.create('img', {
                id: 'chatNotifFilter',
				className: 'chatNotifFilter',    
                src: img_src,
                'data-checked': value,
	            alt: pmbDojo.messages.getMessage('chat', 'chat_notification_filter'),
	            title: pmbDojo.messages.getMessage('chat', 'chat_notification_filter'),
                }
			);
			on(checkBoxNotifFilter, 'click',  lang.hitch(this, function() {	
				var value = domAttr.get('chatNotifFilter', 'data-checked');
				value = 1-value;
				var img_src = './images/new_msg_tgl.png';
				if (value == 1) { 
					img_src = './images/new_msg.png';
				} 
				domAttr.set('chatNotifFilter', 'src', img_src);
				domAttr.set('chatNotifFilter', 'data-checked', value);
				this.data.users_list_state.chatNotifFilter = value;
				topic.publish('ChatUsersList', 'chatUsersList_NotifFilter', value);
				
	        }));
			domConstruct.place(checkBoxNotifFilter, content);
		
			var value = 0;
			var img_src = './images/user_online_tgl.png';
			if (data.users_list_state.chatOnlineFilter == 1) { 
				value = 1;
				img_src = './images/user_online.png';
			}
			var checkBoxOnlineFilter = domConstruct.create('img', {
                id: 'chatOnlineFilter',
				className: 'chatOnlineFilter', 
                src: img_src,
                'data-checked': value,
	            alt: pmbDojo.messages.getMessage('chat', 'chat_connected_filter'),
	            title: pmbDojo.messages.getMessage('chat', 'chat_connected_filter'),
                }
			);
			on(checkBoxOnlineFilter, 'click',  lang.hitch(this, function() {	
				var value = domAttr.get('chatOnlineFilter', 'data-checked');
				value = 1-value;
				var img_src = './images/user_online_tgl.png';
				if (value == 1) { 
					img_src = './images/user_online.png';
				} 
				domAttr.set('chatOnlineFilter', 'src', img_src);
				domAttr.set('chatOnlineFilter', 'data-checked', value);
				this.data.users_list_state.chatOnlineFilter = value;
				topic.publish('ChatUsersList', 'chatUsersList_OnlineFilter', value);
				
	        }));
			domConstruct.place(checkBoxOnlineFilter, content);
			
			var newGroup = domConstruct.create('img', {
                id: 'chatNewGroup',
                className: 'chatNewGroup',
                src: "./images/group_msg.png",
	            alt: pmbDojo.messages.getMessage('chat', 'chat_add_group'),
	            title: pmbDojo.messages.getMessage('chat', 'chat_add_group'),
                }
			);
			on(newGroup, 'click', lang.hitch(this, this.addNewGroup, users_list, 0));
			domConstruct.place(newGroup, content);
		
			var notification = '';
			if (users_list.notifications.number) {
				notification = "<img src='./images/msg_user.png' alt='" + pmbDojo.messages.getMessage('chat', 'chat_notification') + "' title='" + pmbDojo.messages.getMessage('chat', 'chat_notification') + "'>(" + users_list.notifications.number + ")";
			}			
			domConstruct.create('span', { 
				id: 'chatNotification',
				innerHTML: notification,
			}, content);			
			return content;
		},

		addNewGroup: function(users_list, group) {
			this.chatGroup.createNewGroup(users_list, group);
		},
		
		getTabList: function(users_list) {
			var content = domConstruct.create('div', {id: 'chatUsersListContent'});
			var table = domConstruct.create('table', {}, content);
			for (var i = 0; i < users_list.users.length; i++) {
				if (domAttr.get('chatOnlineFilter', 'data-checked') == 1) {
					if (!users_list.users[i].login) continue;
				}
				if (domAttr.get('chatNotifFilter', 'data-checked') == 1) {
					if (!users_list.notifications.users[users_list.users[i].userid])  continue;
				}
				var tr = domConstruct.create('tr', {id: users_list.users[i].user_type_id, style:'cursor: pointer', title: pmbDojo.messages.getMessage('chat', 'chat_open_discussion')}, table);				
				on(tr, 'click', lang.hitch(this, this.openChat, users_list.users[i]));
				domConstruct.create('td', {innerHTML: users_list.users[i].prenom + ' ' + users_list.users[i].nom}, tr);
				domConstruct.create('td', {innerHTML: users_list.users[i].username}, tr);
				
				var notification = '';
				if (users_list.users[i].login) {
					notification = "<img class='user_connecte' src='./images/user_connecte.png' alt='" + pmbDojo.messages.getMessage('chat', 'chat_connected') + "' title='" + pmbDojo.messages.getMessage('chat', 'chat_connected') + "'>";
				}
				if (users_list.notifications.users[users_list.users[i].userid]) {
					notification+= " <img class='msg_user' src='./images/msg_user.png' alt'" + pmbDojo.messages.getMessage('chat', 'chat_notification') + "' title='" + pmbDojo.messages.getMessage('chat', 'chat_notification') + "'>(" + users_list.notifications.users[users_list.users[i].userid] + ")";
				}				
				domConstruct.create('td', {innerHTML: notification}, tr);
			}
			var notification = '';
			if (users_list.notifications.number > 0) {
				notification = "<img class='msg_users' src='./images/msg_user.png' alt='" + pmbDojo.messages.getMessage('chat', 'chat_notification') + "'>(" + users_list.notifications.number + ")";
			}
			dom.byId('chatNotification').innerHTML = notification;			
			return content;			
		},

		getChatsList:  function() {
			var ChatsList = new Array();
			for (var i = 0; i < this.ChatsList.length; i++) {
				ChatsList[i] = {
						'id': this.ChatsList[i].id,
				}				
			}
			return ChatsList;
		},
		
		openChat: function(user, noPublish) {		
			var found = false;
			var right_pos = 300;
			var index_to_open = 0;

			for (var i = 0; i < this.ChatsList.length; i++) {				
				if (domStyle.get('chatWindow_' + this.ChatsList[i].id, "display") == 'block') {
					right_pos+= 300;
				}
				if (this.ChatsList[i].id == user.user_type_id) {
					index_to_open = i;
					found = true;					
				}
			}
			if (!found) {
				var chat_to_open = new Chat(user.user_type_id, user);				
				this.ChatsList.push(chat_to_open);	
				chat_to_open.openChat(right_pos);
			} else {
				if (domStyle.get('chatWindow_' + this.ChatsList[index_to_open].id, "display") != 'block') {
					domStyle.set('chatWindow_' + this.ChatsList[index_to_open].id, 'right', right_pos + 'px');
				}
				domStyle.set('chatWindow_' + this.ChatsList[index_to_open].id, 'display', 'block');				
			}
			if (noPublish != 1) {
				topic.publish('Chat', 'chat_expandCollspace', {
						id: user.user_type_id, 
						open: 1,
						expandCollspace: 1,
						pos: right_pos,
					}
				);
			}
		},

		closeChat: function(id) {
			var close_pos = domStyle.get('chatWindow_' + id, "right");
			close_pos = close_pos.substring(0, close_pos.length - 2); // sans px
			for (var i = 0; i < this.ChatsList.length; i++) {
				if (domStyle.get('chatWindow_' + this.ChatsList[i].id, "display") == 'block') {					
					var pos = domStyle.get('chatWindow_' + this.ChatsList[i].id, "right");
					pos = pos.substring(0, pos.length - 2); 
					if (pos*1 >= close_pos*1) {
						domStyle.set(dom.byId('chatWindow_' + this.ChatsList[i].id), 'right', (pos - 300) + 'px');
					}
				}
			}
			topic.publish('Chat', 'chat_expandCollspace', {
				id: id,
				open: 0,
			});
		},
	});
});