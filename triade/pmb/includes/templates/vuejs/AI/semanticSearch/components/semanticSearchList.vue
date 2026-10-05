<template>
	<div id="searchSemanticList">
		<table>
			<thead>
				<tr>
					<th>{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_python_id") }}</th>
					<th>{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_name") }}</th>
					<th>{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_python_caddie_name") }}</th>
					<th>{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_active_semantic_search") }}</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(semantic, index) in semanticsearchlist" :key='index' style='cursor: pointer'>
					<td class="center" @click="editOrganisation(semantic.id_ai_setting)">
						{{ semantic.id_ai_setting }}
					</td>
					<td class="center" @click="editOrganisation(semantic.id_ai_setting)">
						{{ semantic.settings_ai_settings.name }}
					</td>
					<td class="center" @click="editOrganisation(semantic.id_ai_setting)">
						{{ semantic.settings_ai_settings.caddie_name }}
					</td>
					<td class="center">
						<input name="active_ai_settings" type="radio" v-model="semantic.active_ai_settings"
							value="1" @click="choiceSemanticSearch(semantic.id_ai_setting)">
					</td>
				</tr>
			</tbody>
		</table>
		<div class='row'>
			<input @click="newSemanticSearch" class="bouton" type="button"
				:value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_add')" />
		</div>
	</div>
</template>

<script>
export default {
	props: ["semanticsearchlist"],
	methods: {
		newSemanticSearch: function () {
			document.location = './admin.php?categ=ai&sub=semantic_search&action=add';
		},
		editOrganisation: function (id) {
			document.location = './admin.php?categ=ai&sub=semantic_search&action=edit&id=' + id;
		},
		choiceSemanticSearch: async function (id) {
			await fetch('./admin.php?categ=ai&sub=semantic_search&action=active_semantic_search&id=' + id, {});
			document.location = './admin.php?categ=ai&sub=semantic_search';
		}
	},
}
</script>