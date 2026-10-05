<template>
    <modal
      :showModal="showModal"
      @closeModal="closeModal"
      @validModal="validModal"
      :label="messages.get('ai', 'session_rename')"
      class="ai_sharedlist_upload_modal">

	  	<div class="ai_sharedlist_upload_title" >
				<h1>
					{{ messages.get('ai', 'sharedlist_upload_title') }}
				</h1>
			</div>
			<form @submit.prevent="uploadFile">

				<div class="ai_sharedlist_upload_field">
					<label for="doc_file_upload">
						{{ messages.get('ai', 'sharedlist_upload_label') }}
					</label>

					<div class="ai_sharedlist_upload_input">
						<input
							type="file"
							id="doc_file_upload"
							name="doc_file_upload"
							ref="fileInput"
							@change="onFileChange"
							accept=".pdf">
					</div>
				</div>

				<div class="ai_sharedlist_upload_drag_area" @drop.prevent="onDrop" @dragover.prevent>
					<p v-if="selectedFile">{{ selectedFile.name }}</p>
					<p v-else>Drag & Drop</p>
				</div>

			</form>
    </modal>
</template>

<script>
import modal from "../../../common/components/modal.vue";
export default {
	name: "sharedUploadModal",
	props: {
		showModal: {
			type: Boolean,
			default: false
		},
		shared_list_id: {
			type: Number,
			default: 0
		},
		upload_max_size: {
			type: Number,
			default: 100
		}
	},
	components: {
		modal
	},
	data: () => {
		return {
			selectedFile: null,
			authorizedTypes: ["application/pdf"]
		}
	},
	methods: {

		/**
		 * Ferme la modal
		 *
		 * @return {void}
		 */
		closeModal() {
			this.$emit("closeModal");
		},

		/**
		 * Declenche le processus d'upload de fichier.
		 *
		 * @return {void}
		 */
		validModal() {
			this.uploadFile();
		},

		/**
		 * Met � jour le fichier s�lectionn�
		 *
		 * @param {Event} event
		 * @return {void}
		 */
		onFileChange(event) {
			this.selectedFile = event.target.files[0];
			this.checkFile();
		},

		/**
		 * G�re l'�v�nement de drop et met � jour le fichier s�lectionn�.
		 *
		 * @param {DragEvent} event
		 * @return {void}
		 */
		onDrop(event) {
			this.selectedFile = event.dataTransfer.files[0];

			this.updateFileInput(this.selectedFile);
			this.checkFile();
		},

		/**
		 * Met � jour l'input file avec le fichier
		 *
		 * @param {File} file - Le fichier � mettre � jour
		 * @return {void}
		 */
		updateFileInput(file) {
			const dataTransfer = new DataTransfer();
			dataTransfer.items.add(file);

			this.$refs.fileInput.files = dataTransfer.files;
		},

		/**
		 * V�rifie si le fichier s�lectionn� est dans la taille autoris�e et de type autoris�.
		 *
		 * @return {void}
		 */
		checkFile() {
			if(this.selectedFile.size > (this.upload_max_size * 1024 * 1024)) {
				alert(this.messages.get('ai', 'sharedlist_upload_to_large_file_error').replace('%s', this.upload_max_size));
				this.resetFile();

				return;
			}

			if(!this.authorizedTypes.includes(this.selectedFile.type)) {
				alert(this.messages.get('ai', 'sharedlist_upload_type_file_error'));
				this.resetFile();

				return;
			}
		},

		/**
		 * Reset le fichier s�lectionn�.
		 *
		 * @return {void}
		 */
		resetFile() {
			this.selectedFile = null;
			this.$refs.fileInput.value = "";
		},

		/**
		 * Upload le fichier s�lectionn�.
		 *
		 * @return {void}
		 */
		uploadFile() {
			if (!this.selectedFile) {
				alert(this.messages.get('ai', 'sharedlist_upload_empty_file'));
				return;
			}

			this.showLoader();

			const formData = new FormData();
			formData.append("file", this.selectedFile);
			formData.append("idList", this.shared_list_id)

			fetch(this.$root.webservice_url + "AiApiSharedList/uploadFile", {
				method: "POST",
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if(data.error) {
					alert(data.errorMessage);
					this.hiddenLoader();
					return;
				}

				this.hiddenLoader();
				this.resetFile();
				window.dispatchEvent(new CustomEvent("DocumentUploaded"));
				this.closeModal();
			})
			.catch(error => {
				console.error("Error uploading file:", error);
				this.hiddenLoader();
			});
		}
	}
}
</script>