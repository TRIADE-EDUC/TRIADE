<template>
	<form v-if="entities">
		<div v-if="showEntityType" class="dsi-form-group">
			<label class="etiquette" for="viewEntityTypeList">{{ messages.get('dsi', 'view_form_entity_type') }}</label>
			<div class="dsi-form-group-content">
				<select id="viewEntityTypeList" name="viewEntityTypeList" @focus="currentType = item.settings.entityType" v-model="item.settings.entityType" @change="updateSelector($event)">
					<option value="0" disabled>{{ messages.get('dsi', 'view_form_default_entity_type') }}</option>
					<option v-for="(entityType, index) in availableEntities"
                        :value="entityType.value"
						:key="index" :disabled="entityType.disabled">
							{{ entityType.label }}
					</option>
				</select>
			</div>
		</div>
		<div v-if="! noHtml" class="dsi-form-group" v-show="item.settings.entityType || ! showEntityType">
			<label class="etiquette" for="viewTemplateDirectory">{{ messages.get('dsi', 'view_form_template_directory') }}</label>
			<div class="dsi-form-group-content">
				<select id="viewTemplateDirectory" name="viewTemplateDirectory" v-model="item.settings.templateDirectory">
					<option value="0" disabled>{{ messages.get('dsi', 'view_form_default_template_directory') }}</option>
					<option v-for="(dir, index) in templateDirectories" :value="dir" :key="index">{{dir}}</option>
				</select>
			</div>
		</div>
		
		<div v-if="noHtml"><i>{{ messages.get('dsi', 'view_no_html') }}</i></div>
		<div v-if="! showEntityType && ! itemEntities.length">
			<i>{{ messages.get('dsi', 'dsi_django_view_aggregated') }}</i>
		</div>
		<div class="dsi-form-group-content" v-show="item.settings.entityType || ! showEntityType">
			<div id="django_tree" class="colonne3">
				<label v-show="hasTree">{{ messages.get('dsi', 'view_form_entity_tree') }}</label>
			</div>
			<textarea class="colonne-suite" name="view-editor" id="view-editor"></textarea>
		</div>
	</form>
</template>

<script>
	export default {
		name : "ace_editor",
        props : ["item", "entities", "showEntityType", "itemEntities", "idItem"],
        data : function() {
        	return {
	        	templates : "",
	        	editor : {},
	        	hasTree : false,
	        	templateDirectories : [],
				formData: {
					availableTypes: [],
					availableItems: [],
				},
				customizableFields: []
        	}
        },
		computed : {
			noHtml : function() {
				return this.item.type == 4 || this.item.type == 6;
			},
			availableEntities: function() {
				let availableEntities = [];
				for (const entityType in this.entities) {
					const find = this.formData.availableItems.find(availableItem => availableItem == entityType);
					availableEntities.push({
						value: entityType,
						disabled: find != undefined ? false : true,
						label: this.entities[entityType]
					});
				}

				if (availableEntities.length == 0) {
					this.$set(this.item.settings, "entityType", 0);
				}

				if (availableEntities.length == 1) {
					this.$set(this.item.settings, "entityType", availableEntities[0].value);
				}

				return availableEntities;
			}
		},
		created : function () {
        	if(this.item.settings && ! this.item.settings.entityType) {
        		this.$set(this.item.settings, "entityType", 0);
            }
        	if(this.item.settings && ! this.item.settings.templateDirectory) {
        		this.$set(this.item.settings, "templateDirectory", 0);
            }

	        this.getAdditionalData();
            this.getTemplateDirectories();

			this.$root.$on("updateEditor", this.updateEditor);
			this.$root.$on("updateCustomizableFieldsTree", this.updateCustomizableFieldsTree);

			window.addEventListener("updateEditor", (e) => {
				this.updateItem();
			});		
        },
        mounted: async function() {
			pmbDojo.aceManager.initEditor("view-editor");
            this.getTemplates()
				.then(_ => {
					this.initEditor();
					this.updateTree();
				})
				.catch(e => console.error(e.errorMessage || e));
        },
        watch : {
        	"item.settings.entityType" : function() {
        		//On met a jour le dossier de templates selon l'entite
        		this.getTemplateDirectories();
				if(this.editor) {
					if(typeof this.editor.setValue == "function") this.editor.setValue("");
					if(typeof this.editor.insert == "function") this.editor.insert(this.templates[this.item.settings.entityType] ?? "");
				}
        	},
        	"item.type" : function() {
				this.getTemplates();
        	},
			"itemEntities" : function() {
				this.updateTree();
			}
        },
		methods: {
            initEditor: async function() {
                this.editor = pmbDojo.aceManager.getEditor("view-editor");

				if (this.item.settings.html && this.item.settings.html != "") {
					this.editor.insert(this.item.settings.html);
				} else if (this.item.settings.entityType) {
					this.editor.insert(this.templates[this.item.settings.entityType] || "");
				}

				this.editor.textInput.getElement().addEventListener("keyup", this.updateItem);
				this.editor.textInput.getElement().addEventListener("cut", this.updateItem);
				this.editor.textInput.getElement().addEventListener("undo", this.updateItem);
				this.editor.textInput.getElement().addEventListener("paste", this.updateItem);
            },
            getTemplates: async function() {
				if(this.noHtml) {
					this.templates = await this.ws.get("views", 'getEntitiesDefaultTemplates/' + 1);
				} else {
					this.templates = await this.ws.get("views", 'getEntitiesDefaultTemplates/' + 0);
				}
				return this.templates;
            },
            updateItem: function() {
            	this.item.settings.html = this.editor.getValue();
            },
            updateSelector: function(e) {
           		this.editor.setValue("");
				this.editor.insert(this.templates[e.target.value] || "");
				this.updateItem();
				this.updateTree();
            },
            updateTree: async function() {
				let tree = new Set();
				let promises = [];
				if(this.customizableFields.length) {
					promises.push(this.ws.post("views", 'getCustomizableFieldTree', {fields: this.customizableFields}));
				}
				if(this.idItem) {
					promises.push(this.ws.get("items", `getItemEntityTree/${this.idItem}`));
				} else if(this.itemEntities.length){
					promises.push(...this.itemEntities.map(entity => this.ws.get("views", `getEntityTree/${entity}`)));
				}
				const results = await Promise.all(promises);
				const flattenedResults = results.flat();

				flattenedResults.forEach(treeItem => tree.add(JSON.stringify(treeItem)));

				// convert Set back to array of objects
				tree = [...tree].map(str => JSON.parse(str));

				let event = new CustomEvent('startTree', {
					detail: {
						data : tree
					}
				});

				window.dispatchEvent(event);
				this.hasTree = tree.length ? true : false;
            },
			updateCustomizableFieldsTree: async function(fields) {
				if(fields && fields.length) {
					this.$set(this, "customizableFields", fields);
					this.updateTree();
				}
			},
            getTemplateDirectories: async function() {
            	this.templateDirectories = await this.ws.get("views", `getTemplateDirectories/${this.item.type}/${this.item.settings.entityType}`);
            	//On reset le champ si on ne trouve plus l'element dans le nouveau tableau
        		if(! this.templateDirectories.includes(this.item.settings.templateDirectory)) {
        			this.item.settings.templateDirectory = 0;
        		}
				return this.templateDirectories;
            },
			updateEditor: function() {
				this.editor.setValue("");
				this.editor.insert(this.item.settings.html);
			},
			getAdditionalData: async function() {
				let response = await this.ws.get("views", `form/data/${this.item.type}/${this.item.id}`);
				if (response.error) {
					this.notif.error(response.messages);
				} else {
					this.$set(this, "formData", response);
				}
			}
		}
	}
</script>