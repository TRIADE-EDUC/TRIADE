<template>
	<div id="listCertificate">
		<table>
			<thead>
				<tr>
					<th>Nom</th>
					<th>Certificat</th>
				</tr>
			</thead>
			<tbody>
				<tr class="req_row_handler" v-for="(certificat, index) in list" :key='index' @click="editCertificate(certificat.id)">
					<td>{{ certificat.name }}</td>
					<td>{{ certificat.cert }}</td>
				</tr>
			</tbody>
		</table>
		<input @click="addCertificate" class="bouton" type="button" :value="pmb.getMessage('digital_signature', 'digital_signature_add_certificate')"/>
	</div>
</template>

<script>
	export default {
		props : ["pmb", "action", "list", "img"],
		
		data: function () {
			return {
			}
		},
		created :function(){
		},			
		methods: {
			addCertificate : function() {
				document.location = './admin.php?categ=digital_signature&sub=certificate&action=edit&id=0';
			},
			editCertificate : function(id) {
				document.location = './admin.php?categ=digital_signature&sub=certificate&action=edit&id=' + id;
			},
		}
	}
</script>