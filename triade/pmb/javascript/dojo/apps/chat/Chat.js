// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Chat.js,v 1.6 2019/01/02 14:10:25 ngantier Exp $


define([
        'dojo/_base/declare',
        'dijit/_WidgetBase', 
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/request',
        'dojo/json',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dijit/layout/BorderContainer',
        'dijit/layout/ContentPane',
        'dijit/form/CheckBox',
        'dijit/form/Button',
        'dijit/form/Textarea', 
        'dojo/keys',
        
        
], function(
		declare, 
		WidgetBase, 
		dom, 
		on, 
		lang, 
		topic, 
		request, 
		json, 
		domConstruct,
		domStyle, 
		BorderContainer,
		ContentPane,
		CheckBox,
		Button,
		Textarea, 
		keys
		) {
	return declare(null, {
		id: null,
		container: null,
		domNode: null,
		topContainer: null,
		chatContainer: null,
		expandCollspace: 0,
		user: 0,
		lastMessage: 0,
		firstAcess: 1,
		
		constructor: function(id, user) {			
			this.id = id;			
			this.user = user;
			this.init();
		},
		
		init: function() {			
			topic.subscribe('ChatController', lang.hitch(this, this.handleEvents));
		},
		
		handleEvents: function(evtType,evtArgs) {
			switch(evtType) {
				case 'ChatController_SendResponses':
					this.refresh_chat(evtArgs.data);
					break;
			}			
		},

		refresh_chat: function(data) {	
			if(!data.chats[this.id]) return;
			if(!data.chats[this.id].messages) return;
			var messages = data.chats[this.id].messages;
			
			var content = domConstruct.create('div', {'class': ''});
			var lastMessage = 0;
			for (var i = 0; i < messages.length; i++) {
				if(this.lastMessage && (this.lastMessage >= messages[i].id_chat_message)) break;
				if(!i) lastMessage = messages[i].id_chat_message;
				
				var display;
				if (messages[i].my_message > 0) {
					display = this.displayMyMessage(messages[i].id_chat_message, messages[i].chat_message_text, messages[i].formated_date);	
				} else {
					display = this.displayContactMessage(messages[i].id_chat_message, messages[i].chat_message_text, messages[i].formated_date, messages[i].from_user_type_id, data);
				}
				domConstruct.place(display, content, 'first');
			}
			if (lastMessage) {
				if (domStyle.get(dom.byId('chatWindow_' + this.id), 'display') != 'none') {
					topic.publish('Chat','chat_setMessagesRead', {'user_type_id': this.id});
				}
				this.lastMessage = lastMessage;
			} else {
				return;
			}
			domConstruct.place(content, 'ChatWritePart_' + this.id, 'before');
			if (this.firstAcess) {
				dom.byId('chat_' + this.id).scrollTop = dom.byId('chat_' + this.id).scrollHeight;
				this.firstAcess = 0;
			}
		},

		displayMyMessage: function(id_chat_message, message, date) {
			var content = domConstruct.create('div', {'id': 'chatMyMessage_' + id_chat_message, 'class': 'note_gest'});
			var btn_note = domConstruct.create('div', {'id': 'chatButtonPart_' + id_chat_message, 'class': 'btn_note'}, content);
			
			var deleteButton = new Button({
				id: 'chatDeleteButton_' + id_chat_message,
		        label: '',	
		        iconClass: 'dijitEditorIcon dijitEditorIconDelete',	      
		        title: pmbDojo.messages.getMessage('chat', 'chat_delete_message'),
		    });
			on(deleteButton, 'click', lang.hitch(this, this.deleteMessage, id_chat_message));
			deleteButton.placeAt(btn_note);		
			
			domConstruct.create('p', {'class': 'entete_note', 'innerHTML': date}, content);
			domConstruct.create('p', {'innerHTML': message}, content);			
			return content;
		},

		deleteMessage: function(id_chat_message) {
			topic.publish('Chat','chat_deleteMessage', {'id_chat_message': id_chat_message});
			dom.byId('chatMyMessage_' + id_chat_message).remove();
		},
		
		displayContactMessage: function(id_chat_message, message, date, from_user_type_id, data) {
			var content = domConstruct.create('div', {'class': 'note_opac'});
			// affichage l'auteur du message
			var users = data.users_list.users;
			var authorDisplay = '';
			for (var i = 0; i < users.length; i++) {
				if (users[i].user_type_id == from_user_type_id) {
					authorDisplay = users[i].prenom + ' ' + users[i].nom + '<br/>';
				}
			}
			domConstruct.create('p', {'class': 'entete_note', 'innerHTML': authorDisplay + date}, content);
			domConstruct.create('p', {'innerHTML': message}, content);			
			return content;
		},
				
		openChat: function(right_pos) {
			if(!dijit.byId('chatBorderContainerId_' + this.id)) {
				this.domNode = domConstruct.create('div', {id: 'chatWindow_' + this.id, style: 'width: 300px; height: 300px; position: fixed; bottom:0; right:' + right_pos + 'px; z-index: 4;'}, 'att');
				this.container = new BorderContainer({id:'chatBorderContainerId_' + this.id, style: 'height: 100%; width: 100%;', design:'headline', gutters:true, liveSplitters:true});
	
				this.topContainer = new ContentPane({
					id: 'topContainer_' + this.id,
					region: 'top',
					content: this.getHeaderList(),
			        style: 'height: 16px;'
				});								
				this.chatContainer = new ContentPane({
					id: 'chat_' + this.id,
					region: 'center',
					splitter: 'true',
			        style: 'width: 100%; display:block;',
				});	
				this.container.addChild(this.topContainer);
				this.container.addChild(this.chatContainer);
				this.container.placeAt(this.domNode);	
				this.chatContainer.set('content', this.getChatPart());
				this.container.startup();
				this.container.resize();
			} else {
				domStyle.set(dom.byId('chatWindow_' + this.id), 'display', 'block');
				domStyle.set(dom.byId('chatWindow_' + this.id), 'right', right_pos + 'px');
			}			
		},

		getHeaderList: function() {			
			var content = domConstruct.create('div', {});	
			if(!this.user.prenom) this.user.prenom = '';
			domConstruct.create('span', { innerHTML: this.user.prenom + ' ' + this.user.nom}, content);	
			
			var img_src = './images/moins_msg.png';
			var closeButton = domConstruct.create('img', {
				className: 'chat_close',  
                id: 'closeButton_' + this.id,
                src: img_src,
		        style: "float:right;",
	            alt: pmbDojo.messages.getMessage('chat', 'chat_close'),
	            title: pmbDojo.messages.getMessage('chat', 'chat_close'),
                }
			);
			on(closeButton, 'click', lang.hitch(this, this.closeChat));
			domConstruct.place(closeButton, content);
			
			return content;
		},

		getChatPart: function() {			
			var content = domConstruct.create('div', {id: 'ChatWritePart_' + this.id});
			var textarea = new Textarea({
		        name: "sendMessage",
		        id: "sendMessage_" + this.id,
		        style: "width:225px;"
		    });
			on(textarea, 'keypress', lang.hitch(this, 
				function(event) {
					switch(event.keyCode) {
						case keys.ENTER:
							this.sendMessage();
							break;					
					}
				}
			));
			
			var sendButton = new Button({
		        label: '',
                className: 'chat_send_message',
		        iconClass: 'dijitEditorIcon dijitEditorIconRedo',	      
		        title: pmbDojo.messages.getMessage('chat', 'chat_send_message'),
		    });
			on(sendButton, 'click', lang.hitch(this, this.sendMessage));

			sendButton.placeAt(content, 'first');
			textarea.placeAt(content, 'first');			
			return content;
		},

		sendMessage: function() {
			var message = dijit.byId('sendMessage_' + this.id).get("value");
			if (message == '') return;
			topic.publish('Chat','chat_sendMessage', {'user_type_id': this.id, 'message': message});
			dijit.byId('sendMessage_' + this.id).set("value", '');
		},
		
		closeChat: function() {
			domStyle.set(dom.byId('chatWindow_' + this.id), 'display', 'none');
			topic.publish('Chat','chat_closeChat', this.id);			
		},
		
	});
});