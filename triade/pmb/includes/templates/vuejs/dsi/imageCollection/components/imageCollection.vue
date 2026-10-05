<template>
    <div>
		<div v-if="parameters.configurationError != ''">
			<span>Error : {{ parameters.configurationError }}</span>
		</div>
		<div v-else>
			<input 
				type="file" 
				ref="formFile" 
				:accept="allowedExtensions" 
				@change="handleFileUpload" 
				style="display: none" 
				required 
				multiple 
			/>
			<button type="button" class="bouton" @click="openFileInput">
				{{ messages.get("dsi", "dsi_image_collection_add") }}
			</button>

			<div class="dsi-image-collection-container" ref="imageCollectionContainer">
				<pagination-list :list="parameters.images" :perPage="20" :startPage="1" :nbPage="6" :nbResultDisplay="false">
					<template #content="{ list }">
						<div 
							v-for="(image, index) in list" 
							:key="index" 
							:class="selectedImage != null && selectedImage == index ? 'dsi-image-collection-image-box selected' : 'dsi-image-collection-image-box'"
							@click="selectImage(image.name)">

							<button 
								type="button" 
								class="bouton dsi-image-collection-button-link" 
								@click.stop="copyLinkToClipboard($event, image.name)" 
								:title="messages.get('dsi', 'dsi_image_collection_insert_copy_link')">

								<i class="fa fa-link" aria-hidden="true"></i>
							</button>
							<button 
								type="button" 
								class="bouton dsi-image-collection-button-delete" 
								@click.stop="removeImage(image.name)" 
								:title="messages.get('common', 'remove')">

								<i class="fa fa-trash" aria-hidden="true"></i>
							</button>
							<img :src="image.url" alt="" lazy="loading">
						</div>
					</template>
				</pagination-list>

				<div v-if="images.length == 0" class="dsi-image-collection-no-image">
					{{ messages.get("dsi", "dsi_image_collection_no_image") }}
				</div>
			</div>
			<fieldset v-if="insert && selectedImage != null" class="dsi-image-collection-insert-form dsi-fieldset-image-collection">
				<legend class="dsi-legend-image-collection">
					{{ parameters.images[selectedImage].name }}
				</legend>
				<div class="dsi-form-group">
					<label class="etiquette" for="dsi-image-collection-alt">
						{{ messages.get("dsi", "dsi_image_collection_alt_title") }}
					</label>
					<div class="dsi-form-group-content">
						<input name="dsi-image-collection-alt" id="dsi-image-collection-alt" type="text" v-model="selectedAltTilte">
					</div>
				</div>
				<div class="dsi-form-group">
					<label class="etiquette" for="dsi-image-collection-width">
						{{ messages.get("dsi", "dsi_image_collection_width") }}
					</label>
					<div class="dsi-form-group-content">
						<input name="dsi-image-collection-width" id="dsi-image-collection-width" type="number" v-model="selectedWidth">
					</div>
				</div>
				<div class="dsi-form-group">
					<label class="etiquette" for="dsi-image-collection-height">
						{{ messages.get("dsi", "dsi_image_collection_height") }}
					</label>
					<div class="dsi-form-group-content">
						<input name="dsi-image-collection-height" id="dsi-image-collection-height" type="number" v-model="selectedHeight">
					</div>
				</div>
				<button type="button" class="bouton" @click="insertImage">
					{{ messages.get("dsi", "dsi_image_collection_insert") }}
				</button>
			</fieldset>
		</div>
    </div>
</template>

<script>
	import paginationList from "@dsi/components/paginationList.vue";

    export default {
		props: {
			insert: {
				'type': Boolean,
				default: () => {
					return false;
				}
			}
		},
		components: {
			paginationList
		},
		data: function() {
			return {
				parameters: {
					configurationError: '',
					images: [],
					maxFileSize: 0,
					allowedExtensions: [],
					allowedMimetypes: []
				},
				selectedImage: null,
				selectedAltTilte: '',
				selectedWidth: '',
				selectedHeight: '',
				files: []
			}
		},
		created: async function() {
			await this.initParameters();
		},
		computed: {
			/**
			 * Retourne les extensions autorisees (avec un . en prefixe) 
			 * 
			 * @return {Array<String>}
			 */
			allowedExtensions: function() {
				return this.parameters.allowedExtensions.map(ext => {
					if (ext[0] !== '.') {
						return `.${ext}`;
					}

					return ext;
				});
			}
		},
		methods: {
			initParameters: async function() {
				this.$set(this, "parameters", await this.ws.get("ImageCollection", 'getParameters'));
			},

			/**
			 * Upload les images.
			 * 
			 * @return {void}
			 */
			uploadImage: async function() {
				if (this.files.length === 0) {
					return;
				}

				let response = await this.ws.post("ImageCollection", 'upload', this.files, true);
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else {
					this.parameters.images.push(...response);
					this.notif.info(this.messages.get('common', 'success_save'));
				}

				this.files = [];
				this.$refs.formFile.value = '';
			},
			
			/**
			 * Gere les fichiers selectionnes par l'utilisateur.
			 * 
			 * @param event
			 * @return {void}
			 */
			handleFileUpload: async function(event) {
				const maxAllowedSize = this.parameters.maxFileSize * 1024;

				for (let file of event.target.files) {
					if (file) {
						if (file.size > maxAllowedSize) {
							event.target.value = '';
							alert(`${this.messages.get('dsi', 'view_form_image_max_size')} (${this.parameters.maxFileSize}Ko maximum)`);
							return;
						}

						if (!this.parameters.allowedMimetypes.includes(file.type)) {
							event.target.value = '';
							alert(this.messages.get('dsi', 'dsi_image_collection_invalid_file_type'));
							return;
						}

						this.files.push(file);
					}
				}

				await this.uploadImage();
        	},

			/**
			 * Ouvre le champs de selection des fichiers.
			 * 
			 * @return {void}
			 */
			openFileInput: function() {
				this.$refs.formFile.click();
			},

			/**
			 * Supprime une image de la collection.
			 * 
			 * Demande confirmation a l'utilisateur avant de supprimer l'image.
			 * 
			 * @param {number} name
			 * @return {void}
			 */
			removeImage: async function (name) {
				if(confirm(this.messages.get('dsi', 'confirm_del'))) {
					const index = this.getImageIndexByName(name);
					let response = await this.ws.post("ImageCollection", 'delete', this.parameters.images[index]);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
						return;
					}
	
					this.parameters.images.splice(index, 1);
				}
			},

			/**
			 * Copie le lien de l'image selectionnee dans le presse-papier.
			 * 
			 * @param {Event} event
			 * @param {string} name
			 * 
			 * @return {void}
			 */
			copyLinkToClipboard: function(event, name) {
				const index = this.getImageIndexByName(name);
				const image = this.parameters.images[index];
				navigator.clipboard.writeText(image.url);

				// Change l'icône en fa-check
				let iconElement = event.target;
				if(iconElement.tagName != 'I') {
					iconElement = iconElement.querySelector('i');
				}

				iconElement.classList.remove('fa-link');
				iconElement.classList.add('fa-check');

				// Après 2 secondes, rétablit l'icône fa-link
				setTimeout(() => {
					iconElement.classList.remove('fa-check');
					iconElement.classList.add('fa-link');
				}, 1500);
			},

			/**
			 * Selectionne une image dans la collection.
			 * 
			 * @param {number} name
			 * @return {void}
			 */
			selectImage: function(name) {
				if(!this.insert) {
					return;
				}

				const index = this.getImageIndexByName(name);

				if(this.selectedImage == index) {
					this.$set(this, "selectedImage", null);
					return;
				}
				
				this.$set(this, "selectedImage", index);
			},

			/**
			 * Genere le code HTML de l'image selectionnee.
			 * 
			 * @return {string}
			 */
			 generateSelectedHtmlImage: function() {
				if (this.selectedImage !== null) {
					const image = this.parameters.images[this.selectedImage];
					
					// Initialisation de la balise <img>
					let imgTag = `<img src="${image.url}" alt="${this.selectedAltTilte}">`;

					// Ajouter width si défini
					if (this.selectedWidth) {
						imgTag = imgTag.replace('>', ` width="${this.selectedWidth}">`);
					}

					// Ajouter height si défini
					if (this.selectedHeight) {
						imgTag = imgTag.replace('>', ` height="${this.selectedHeight}">`);
					}

					return imgTag;
				}
			},

			/**
			 * Insert l'image selectionnee.
			 * Cette methode emet l'event 'insert' avec le code HTML de l'image selectionnee.
			 * 
			 * @return {void}
			 */
			insertImage: function() {
				this.$emit('insert', {
					tag: this.generateSelectedHtmlImage(),
					name: this.parameters.images[this.selectedImage].name,
					url: this.parameters.images[this.selectedImage].url,
					alt: this.selectedAltTilte,
					width: this.selectedWidth,
					height: this.selectedHeight
				});
			},

			/**
			 * Retourne l'index d'une image dans la collection.
			 * 
			 * @param {string} name
			 * @return {number}
			 */
			getImageIndexByName: function(name) {
				return this.parameters.images.findIndex(image => image.name == name);
			}
		}
    }
</script>