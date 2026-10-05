// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ChatGroup.js,v 1.2 2018/11/07 07:51:55 ngantier Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/request',
        'dojo/json',
        "dijit/registry", 
        "dojo/parser", 
        "dijit/Dialog", 
        "dijit/form/Button", 
        "dojo/domReady!",
        'dojo/dom-construct',
        'dijit/layout/BorderContainer',
        'dijit/layout/ContentPane',
        'dijit/form/CheckBox',
        'dojo/query',
        
], function(declare, dom, on, lang, topic, request, json,registry, parser, Dialog, Button, domReady, domConstruct, BorderContainer, ContentPane, CheckBox, query) {
	return declare(null, {
		id: 0,
		dialog: null,
		userListContainer: null,
		container: null,
		constructor: function(id) {
			if (!id) id = 0;
			this.id = id;
			this.init();
		},
		
		init: function() {
		
		},
				
		createNewGroup: function(users_list, group) {
			if(!dom.byId('groupDialog')) {
				var content = domConstruct.create('div', {});

				this.dialog = new Dialog({
		            id: "groupDialog",
		            title: pmbDojo.messages.getMessage('chat', 'chat_goup_edition'),
					width: '400px',
					onHide: function() {
						registry.byId("groupDialog").destroyDescendants();
			            registry.byId("groupDialog").destroy();
					}
		        });	
				domConstruct.place('<label for="chatGroupName">' + pmbDojo.messages.getMessage('chat', 'chat_goup_name') + '</label></br><input type="text" id="chatGroupName" name="chatGroupName" required value="' + (group.chat_group_name ? group.chat_group_name : "") + '">' , content);
				
				var buttonSaveGroup = new Button({
					id: 'buttonSaveGroup',
			        label: pmbDojo.messages.getMessage('chat', 'chat_goup_save'),				        
			    });
				on(buttonSaveGroup, 'click', lang.hitch(this, this.saveGroup, group));

				domConstruct.place(this.getTabList(users_list, group), content);
				buttonSaveGroup.placeAt(content);
				
				if (group.chat_user_group_num > 0) {
					var buttonDeleteGroup = new Button({
						id: 'buttonDeleteGroup',
				        label: pmbDojo.messages.getMessage('chat', 'chat_goup_delete'),				        
				    });
					on(buttonDeleteGroup, 'click', lang.hitch(this, this.deleteGroup, group));
					buttonDeleteGroup.placeAt(content);
				}					
				this.dialog.set('content', content);
			}
			this.dialog.show();		
		},
		
		deleteGroup: function(group) {	
			var r = confirm(pmbDojo.messages.getMessage('chat', 'chat_goup_confirm_delete'));
			if (r != true) {
			    return;
			}
			topic.publish('ChatGroup', 'chatGroup_delete', {
					id: group.chat_user_group_num,
				}
			);
			registry.byId("groupDialog").destroyDescendants();
            registry.byId("groupDialog").destroy();
			return 0;
        },
        
		isInGroup: function(user_type_id, group) {
			if(!group.users) return 0;
			for (var i = 0; i < group.users.length; i++) {
				if(group.users[i].user_type_id == user_type_id) return 1;
			}
			return 0;
        },
        
		getTabList: function(users_list, group) {
			var content = domConstruct.create('div', {});
			var table = domConstruct.create('table', {id: 'chatTableUsersGroup'}, content);
			for (var i = 0; i < users_list.users.length; i++) {
				var tr = domConstruct.create('tr', {}, table);	
				domConstruct.create('td', {innerHTML: users_list.users[i].prenom + ' ' + users_list.users[i].nom}, tr);
				domConstruct.create('td', {innerHTML: users_list.users[i].username}, tr);
				var td = domConstruct.create('td', {}, tr);
			
				var checkBoxNotifFilter = new CheckBox({
			        id: 'chatUserGroupCheck_' + users_list.users[i].user_type_id,
			        name: 'chatUserGroupCheck[]',
			        value: users_list.users[i].user_type_id,
			        checked: this.isInGroup(users_list.users[i].user_type_id, group),			        
			    });				
				checkBoxNotifFilter.placeAt(td);
			}		
			return content;			
		},

		saveGroup: function(group) {

			if (!dom.byId('chatGroupName').value) {
				alert(pmbDojo.messages.getMessage('chat', 'chat_edit_group_empty_name'));
			    return;
			}
			
			var checkboxes = query("input[type=checkbox]:checked", "chatTableUsersGroup");
			var usersList = new Array();
			checkboxes.forEach(function (checkbox) {
			    usersList.push(checkbox.value);
			});		    
			topic.publish('ChatGroup', 'chatGroup_save', {
					id: group.chat_user_group_num, 
					name: dom.byId('chatGroupName').value,
					users: usersList
				}
			);
			registry.byId("groupDialog").destroyDescendants();
            registry.byId("groupDialog").destroy();
        },
        
		addRemove: function(user) {
			
		},
		
	});
});