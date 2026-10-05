<template>
	<form>
		<div class="dsi-form-group" v-show="item.settings.entityType || ! showEntityType">
			<label class="etiquette" for="viewTemplateDirectory">{{ messages.get('dsi', 'view_form_template_directory') }}</label>
			<div class="dsi-form-group-content">
				<select id="viewTemplateDirectory" name="viewTemplateDirectory" v-model="item.settings.templateDirectory">
					<option value="0" disabled>{{ messages.get('dsi', 'view_form_default_template_directory') }}</option>
					<option v-for="(dir, index) in templateDirectories" :value="dir" :key="index">{{dir}}</option>
				</select>
			</div>
		</div>
		
		<div class="dsi-form-group-content">
			<div id="django_tree" class="colonne3">
				<label v-show="hasTree">{{ messages.get('dsi', 'view_form_entity_tree') }}</label>
			</div>
			<textarea class="colonne-suite" name="view-editor" id="view-editor"></textarea>
		</div>
	</form>
</template>

<script>
	export default {
		name : "agnosticAceEditor",
        props : ["item", "entities", "showEntityType", "itemEntities"],
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
		created : function () {
	        this.getAdditionalData();

			this.$root.$on("updateEditor", this.updateEditor);
			this.$root.$on("updateCustomizableFieldsTree", (e) =>this.updateCustomizableFieldsTree(e));

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
				this.templates = await this.ws.get("views", 'getEntitiesDefaultTemplates/' + 0);
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

				promises.push(this.ws.get("views", `getEntityTree/0")`));

				if(this.customizableFields.length) {
					promises.push(this.ws.post("views", 'getCustomizableFieldTree', {fields: this.customizableFields}));
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