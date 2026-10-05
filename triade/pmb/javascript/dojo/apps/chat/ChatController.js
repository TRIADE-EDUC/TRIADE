// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ChatController.js,v 1.3 2020/12/17 15:05:02 arenou Exp $

/*
 * 
        'apps/chat/ChatUsersList',
        'apps/chat/Chat',
 */
define([
        'dojo/_base/declare',
        'dijit/_WidgetBase', 
        'dojo/request/xhr',
        'dojo/dom',
        'dojo/on',
        'dojo/ready',
        'dojo/_base/event',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/request',
        'dojo/json',
        'dojo/dom-construct',
        'apps/chat/ChatUsersList',
        'apps/chat/Chat',
        'dojo/dom-style',
], 
function(
		declare, 
		WidgetBase, 
		xhr,
		dom, 
		on,
		ready,
		dojoEvent, 
		lang, 
		topic, 
		request, 
		json, 
		domConstruct,
		ChatUsersList,
		Chat,
		domStyle
		) {
	return declare(null, {
		callback: null,
		lastPoll: null,
		chatUsersList: null,
		ChatsList: new Array(),
		firstAcess: 1,
		url: './ajax.php?module=ajax&categ=chat&action=',
		
		constructor: function() {
			this.init();
		},
		
		init: function() {		
			topic.subscribe('ChatUsersList', lang.hitch(this, this.handleEvents));
			topic.subscribe('Chat', lang.hitch(this, this.handleEvents));
			topic.subscribe('ChatGroup', lang.hitch(this, this.handleEvents));
				
			this.chatUsersList = new ChatUsersList();
			on(window,'blur',lang.hitch(this,this.disableCall))
			on(window,'focus',lang.hitch(this,this.initCall));
			this.initCall();
		},
		
		handleEvents: function(evtType,evtArgs) {

			//console.log('handleEvents', evtType, evtArgs);
			switch(evtType) {
				case 'chatUsersList_OnlineFilter':
				case 'chatUsersList_NotifFilter':
				case 'chatUsersList_expandCollspace':
					// no break;
				case 'chat_expandCollspace':
					this.saveState(evtArgs);
					break;
				case 'chat_sendMessage':
					this.sendMessage(evtArgs);
					break;
				case 'chat_deleteMessage':
					this.deleteMessage(evtArgs);
					break;
				case 'chat_setMessagesRead':
					this.setMessagesRead(evtArgs);
					break;
				case 'chatGroup_save':
					this.chatGroupSave(evtArgs);
					break;
				case 'chatGroup_delete':
					this.chatGroupDelete(evtArgs);
					break;
			}			
		},
		
		
		initCall : function(){
			this.disableCall();
			this.pool();
			if(window.document.hasFocus() && this.callback == null){
				this.callback = setInterval(lang.hitch(this, this.pool), 5000);
			}
		},
		  
		disableCall : function(){
			if(this.callback){
				clearInterval(this.callback);
			}
		},
		pool: function() {				
			var chatsList = '';
			if (this.firstAcess) {								
			} else {
				chatsList = this.chatUsersList.getChatsList();
			}	
			this.xhrPost({
				method: 'get_chat',
				params: {
					'id': '',
					'firstAcess': this.firstAcess,
					'chats': chatsList
				}
			}, this.wsSendResponse);
		},
		
		wsSendResponse: function(response) {
			if (this.firstAcess) {
				for (var i = 0; i < response.data.chats_state.length; i++) {
					var type = this.getChatType(response.data.chats_state[i].id);
					if (this.getChatType(response.data.chats_state[i].id) == 1) {
						// chat de goupe
						for (var j = 0; j < response.data.groups_list.users.length; j++) {							
							if (response.data.groups_list.users[j].user_type_id == response.data.chats_state[i].id) {
								this.chatUsersList.openChat(response.data.groups_list.users[j], 1);
								break;
							}
						}	
					} else {
						// chat de user
						for (var j = 0; j < response.data.users_list.users.length; j++) {
							if (response.data.users_list.users[j].user_type_id == response.data.chats_state[i].id) {
								this.chatUsersList.openChat(response.data.users_list.users[j], 1);
								break;
							}
						}	
					}	
				}
			}			
			topic.publish('ChatController', 'ChatController_SendResponses', response);
			this.firstAcess = 0;
		},
		
		getChatType: function(id) {
			if(!id) return 0;
			var type_id = id.split("_");
			return type_id[0];
		},		
		
		sendMessage: function(params) {
			this.xhrPost({
				method: 'send_message',
				params: params				
			}, this.sendMessageCallback);
		},
		
		sendMessageCallback: function(response) {
			this.pool();
		},

		deleteMessage: function(params) {
			this.xhrPost({
				method: 'delete_message',
				params: params				
			}, this.deleteMessageCallback);
		},
		
		deleteMessageCallback: function(response) {
		},

		setMessagesRead: function(params) {
			this.xhrPost({
				method: 'set_messages_read',
				params: params				
			}, this.setMessagesReadCallback);
		},
		
		setMessagesReadCallback: function(response) {
		},

		chatGroupSave: function(params) {
			this.xhrPost({
				method: 'chat_group_save',
				params: params				
			}, this.chatGroupSaveCallback);
		},
		
		chatGroupSaveCallback: function(response) {
		},

		chatGroupDelete: function(params) {
			this.xhrPost({
				method: 'chat_group_delete',
				params: params				
			}, this.chatGroupDeleteCallback);
		},
		
		chatGroupDeleteCallback: function(response) {
		},
		
		saveState: function(data) {			
			var chatsList = this.chatUsersList.getChatsList();
			var list = new Array();
			for (var i = 0; i < chatsList.length; i++) {
				if (domStyle.get('chatWindow_' + chatsList[i].id, "display") == 'block') {
					list[i] = {
							'id': chatsList[i].id,
							'pos': domStyle.get('chatWindow_' + chatsList[i].id, 'right'),
							'open': 1,
					}
				}				
			}			
			this.xhrPost({
				method: 'save_state',
				params: {
					'chats': list,
					'user_list': this.chatUsersList.getState(),
				}
			}, this.saveStateResponse);				
		},
		
		saveStateResponse: function() {			
		},

		xhrPost: function(params, callback) {
			xhr.post(this.url + 'exec', {
				handleAs: 'json',
				data: {
					chat_params: JSON.stringify(params)
				},
			}).then(lang.hitch(this, callback));
		},
		
	});
});