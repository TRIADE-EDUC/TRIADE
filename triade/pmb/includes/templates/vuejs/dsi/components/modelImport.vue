<template>
    <div>
        <input type="button" class="bouton" :value="messages.get('dsi', 'model_import')" @click="$refs.file_input_import.click()">
		<input type="file" accept=".dsi" id="file-input-import" ref="file_input_import" @change="loadFileImport" style="display: none;">
    </div>
</template>

<script>
    export default {
		props: {},
		data: function () {
            return {
                importedFile: ""
            }
		},
        watch: {
			"importedFile": function() {
                if(this.importedFile) {
                    this.import();
                }
			}
		},
		methods: {
            import: async function() {
				let response = await this.ws.post(this.$root.categ, 'import', { file: this.importedFile });
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
					return;
				}
                
                this.notif.info(this.messages.get('common', 'success_save'));
				
                this.$emit("importModel", response);
                this.$set(this, "importedFile", "");
			},
			loadFileImport: function(e) {
				let files = e.target.files || e.dataTransfer.files;

				if(files[0]) {
					let read = new FileReader();
					read.readAsText(files[0], 'UTF-8');
					read.onloadend = () => {
						this.$set(this, "importedFile", read.result);
					}
				}
			}
		}
	}
</script>