<template>
	<div id="listSignature">
		<table>
			<thead>
				<tr>
					<th>Nom</th>
					<th>Certificat</th>
				</tr>
			</thead>
			<tbody>
				<tr class="req_row_handler" v-for="(signature, index) in list" :key='index' @click="editSignature(signature.id)">
					<td>{{ signature.name }}</td>
					<td>{{ getLabelCertificate(signature.num_cert) }}</td>
				</tr>
			</tbody>
		</table>
		<input @click="addSignature" class="bouton" type="button" :value="pmb.getMessage('digital_signature', 'digital_signature_add_certificate')"/>
	</div>
</template>

<script>
	export default {
		props : ["pmb", "certificates", "action", "list", "img"],
		
		data: function () {
			return {
			}
		},
		methods: {
			addSignature : function() {
				document.location = './admin.php?categ=digital_signature&sub=signature&action=edit&id=0';
			},
			editSignature : function(id) {
				document.location = './admin.php?categ=digital_signature&sub=signature&action=edit&id=' + id;
			},
			getLabelCertificate :function(id){
				var certificate = this.certificates.find(elem => id == elem.id);
				if(certificate){
					return certificate.name;
				}
				return "";
			}
		}
	}
</script>