<template>
	<div>
		<form id="roleForm" @submit.prevent="submit($event)">
			<div class="row">
				<label for="role_name">{{ messages.get("roles", "role_name") }}</label>
			</div>
			<div class="row">
				<input type="text" id="role_name" class="saisie-50em" v-model="role.name" required />
			</div>
			<div class="row">
				<label for="role_comment">{{ messages.get("roles", "role_comment") }}</label>
			</div>
			<div class="row">
				<textarea id="role_comment" v-model="role.comment" cols="62" rows="5" ></textarea>
			</div>
			<members-list :members="role.members">
			</members-list>
			<modules-list :modules="role.modules" :tabs="role.tabs" :sub-tabs="role.subTabs">
			</modules-list>
			<div class="row">
				<div class='left'>
					<input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click='cancel'>
					<input type="submit" class="bouton" :value="messages.get('common', 'submit')">
				</div>
				<div class='right' v-if='role.id'>
					<input type="button" class="bouton" :value="messages.get('common', 'remove')" @click='remove'>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
	import membersList from "./form/membersList.vue";
	import modulesList from "./form/modulesList.vue";
	
	export default {
		props : {
	        role : {
	            'type' : Object
	        }
		},
		data: function () {
			return {
			}
		},
		components : {
			membersList,
			modulesList,
		},
		computed: {
		    
		},
		methods: {
			cancel: function() {
				document.location = './admin.php?categ=users&sub=roles';
			},
			submit: async function() {
				let response = await this.ws.post('roles', 'save', this.role);
				if(! response.error) {
					document.location = './admin.php?categ=users&sub=roles';
				}
			},
			remove: async function() {
				let response = await this.ws.post('roles', 'delete', {id : this.role.id });
				if(!response.error) {
					document.location = './admin.php?categ=users&sub=roles';
				} else {
					this.notif.error(this.messages.get('common', 'failed_delete'));
				}
			}
		}
	}
</script>