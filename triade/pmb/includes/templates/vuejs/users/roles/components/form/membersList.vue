<template>
	<div class="role-members">
		<div class="row">
			<label>{{ messages.get("roles", "role_members") }}</label>
		</div>
		<div class="row">
		<input type="text" v-model="globalSearch" class="saisie-30em" :placeholder="messages.get('roles', 'role_members_filter_global_search')" />
		<input type="checkbox" id="checked_members" v-model="checkedMembers" /><label for="checked_members">{{ messages.get("roles", "role_members_filter_checked") }}</label>
		</div>
		<div class="row">
			<div class="rolesgroupcheckbox ui-clearfix ui-flex ui-flex-1-5 ui-flex-top">
				<span v-for="(member, index) in filteredMembersList" :key="index" class='checkbox'>
					<input :id="'role_member_'+member.type+'_'+member.id" type="checkbox" v-model="members[member.type]" :value="member.id" :readonly="member.type == 'user' && member.id == 1" :disabled="member.type == 'user' && member.id == 1" />
					<i v-if="member.type == 'group'" class="fa fa-users"></i>
					<label :for="'role_member_'+member.type+'_'+member.id">{{ member.label }}</label>
				</span>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		props : {
	        members : {
	            'type' : Object
	        }
		},
		data: function () {
			return {
				membersList: [],
				globalSearch : '',
				checkedMembers : 1
			}
		},
		created: function() {
            this.getMembersList();
        },
		computed: {
        	filteredMembersList: function() {
        		let filteredMembersList = [];
        		if(this.globalSearch) {
        			filteredMembersList = this.membersList.filter((element) => {
                        return element.label.toLowerCase().includes(this.globalSearch.toLowerCase());
                    });
        		} else {
        			filteredMembersList = this.membersList;
        		}
        		if(!this.checkedMembers) {
        			filteredMembersList = filteredMembersList.filter((element) => {
        				return this.members[element.type].find(numMember => numMember == element.id);
                    });
        		}
        		return filteredMembersList;
        	}
		},
		methods: {
			getMembersList: async function() {
                let response = await fetch(this.$root.url_webservice + "roles/list/members", {
                    method: "GET",
                    cache: 'no-cache'
                });
                let result = await response.json();
                if (result.error) {
                    this.notif.error(this.messages.get('roles', result.errorMessage));
                    return;
                }
                this.membersList = new Array();
                if (result.users.length) {
                	result.users.forEach((user) => {
                		let member = user;
                		member.id = user.object.userid;
                		member.type = 'user';
                		member.label = user.object.prenom+' '+user.object.nom+' ('+user.object.username+')';
                		this.membersList.push(member);
					});
                }
                if (result.groups.length) {
                	result.groups.forEach((group) => {
                		let member = group;
                		member.id = group.object.grp_id;
                		member.type = 'group';
                		member.label = group.object.grp_name;
                		this.membersList.push(member);
					});
                }
                this.initMembers();
            },
            sortedMembersList: function() {
				if (this.membersList.length) {
					return this.membersList.sort((a,b) => a.label > b.label);
				}
			},
            initMembers: function() {
				if(!this.members['user']) {
					this.members['user'] = [];
				}
				if(!this.members['group']) {
					this.members['group'] = [];
				}
            },
		}
	}
</script>