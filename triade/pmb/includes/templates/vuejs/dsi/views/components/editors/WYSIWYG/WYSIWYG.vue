<template>
    <div>
        <FormModal
            :title="messages.get('dsi', 'view_wysiwyg_view_wysiwyg')"
            formClass="wysiwyg-form-view" ref="modal">

            <div class="form-contenu">
                <h3>{{ messages.get('common', 'parameters') }}</h3>
                <div class="row">
                    <div class="colonne3">
                        <label class="etiquette" for="display_choice">
                            {{ messages.get('dsi', 'view_wysiwyg_display') }}
                        </label>
                    </div>
                    <div class="colonne_suite">
                        <select id="display_choice" name="display_choice" v-model.number="view.settings.displayChoice" required>
                            <option value="0">{{ messages.get('dsi', 'view_wysiwyg_display_default') }}</option>
                            <option value="1">{{ messages.get('dsi', 'view_wysiwyg_display_HTML5') }}</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <p class="warning" v-show="!view.settings.displayChoice">
                        {{ messages.get('dsi', 'view_wysiwyg_display_HTML3_compatibility') }}
                    </p>
                </div>
            </div>
        </FormModal>
	    <form class="wysiwyg-form-view" ref="wysiwyg_form">
	        <div v-if="! showPreview" class="wysiwyg-form-view-buttons">
	            <div class="wysiwyg-form-view-buttons-left">
	                <button type="button" class="wysiwyg-button" @click="getEditor" :disabled="!showTree && !showPrev" :title="messages.get('dsi', 'view_wysiwyg_build')">
	                    <i class="fa fa-align-justify" aria-hidden="true"></i>
	                </button>
	                <button type="button" class="wysiwyg-button" @click="getPreview" :disabled="showPrev" :title="messages.get('dsi', 'view_wysiwyg_preview')">
	                    <i class="fa fa-eye" aria-hidden="true"></i>
	                </button>
	                <button type="button" class="wysiwyg-button" @click="getTree" :disabled="showTree" :title="messages.get('dsi', 'view_wysiwyg_tree')">
	                    <i class="fa fa-sitemap" aria-hidden="true"></i>
	                </button>
	            </div>
	            <div class="wysiwyg-form-view-buttons-right">
	                <button type="button" class="wysiwyg-button" @click="showParameters" :title="messages.get('dsi', 'view_wysiwyg_properties')">
	                    <i class="fa fa-cog" aria-hidden="true"></i>
	                </button>
	                <button type="button" class="wysiwyg-button" @click="fullscreen" :title="messages.get('dsi', 'view_wysiwyg_fullscreen')">
	                    <i class="fa fa-arrows-alt" aria-hidden="true"></i>
	                </button>
	            </div>
	        </div>

	        <div v-if="showPrev" class="wysiwyg-preview">
	            <iframe
	                id="dsi-preview-frame"
	                :srcdoc="messages.get('dsi', 'dsi_preview_loading')"
	                :src="previewUrl"
	                width="100%"
	                sandbox="allow-same-origin allow-downloads allow-scripts"
	                @load="resizeFrame">
	            </iframe>
	        </div>
	        <div v-else-if="showTree" class="wysiwyg-tree">
	            <tree :tree="view.settings.layer.blocks[0]" :blockLabels="blockLabels" :view="view"></tree>
	        </div>
	        <div v-else class="wysiwyg-editor">
	            <component v-for="(block, index) in view.settings.layer.blocks"
	                :view="view"
	                :key="index"
	                :is="blockTypes[block.type]"
	                :block="block"
	                :blockTypes="blockTypes"
	                :blockLabels="blockLabels"
	                :root="true"
	                :parent="view">
	            </component>
	            <addBlock :blocks="view.settings.layer.blocks" :show="show" @close="show = false" :root="true"></addBlock>
	            <div class="wysiwyg-add-section" @click="show = true">
	                <div class="wysiwyg-add-section-element">
	                    <span>{{ messages.get('dsi', 'view_wysiwyg_add_element') }}</span>
	                </div>
	            </div>
	        </div>
	    </form>
    </div>
</template>

<script>
    import FormModal from '../../../../../common/components/FormModal.vue';
    import addBlock from './addBlock.vue';
    import tree from './WYSIWYGTree.vue';

    import block from './elements/block.vue';
    import textInput from './elements/textInput.vue';
    import imageInput from './elements/imageInput.vue';
    import videoInput from './elements/videoInput.vue';
    import listInput from './elements/listInput.vue';
    import textEditorInput from './elements/textEditorInput.vue';
    import viewInput from './elements/viewInput.vue';
    import viewImportInput from './elements/viewImportInput.vue';
	export default {
        name : "WYSIWYGEditor",
		props : ["view", "showPreview", "idDiffusion", "selectedAttachment"],
		components: {
            FormModal,
            addBlock,
            block,
            textInput,
            imageInput,
            videoInput,
            listInput,
            textEditorInput,
            viewInput,
            viewImportInput,
            tree
        },
		data: function () {
			return {
                blockTypes: {
                    1: "block",
                    2: "textInput",
                    3: "imageInput",
                    4: "videoInput",
                    5: "listInput",
                    6: "textEditorInput",
                    7: "viewInput",
                    8: "viewImportInput"
                },
                blockLabels: {
                    1: this.messages.get('dsi', 'view_wysiwyg_block'),
                    2: this.messages.get('dsi', 'view_wysiwyg_input_text'),
                    3: this.messages.get('dsi', 'view_wysiwyg_input_image'),
                    4: this.messages.get('dsi', 'view_wysiwyg_input_video'),
                    5: this.messages.get('dsi', 'view_wysiwyg_input_list'),
                    6: this.messages.get('dsi', 'view_wysiwyg_input_text_rich'),
                    7: this.messages.get('dsi', 'view_wysiwyg_views'),
                    8: this.messages.get('dsi', 'view_wysiwyg_view_wysiwyg')
                },
                show: false,
                showPrev: false,
                showTree: false,
            }
        },
        created: function() {
            if(!this.view.settings.layer) {
                this.$set(this.view.settings, "layer", {
                    blocks: [{type: 1, style: {"display": "flex", "flex-direction": "column"}, content: "", blocks: []}]
                });
            }

            if (!this.view.settings.displayChoice) {
                this.$set(this.view.settings, "displayChoice", 0);
            }

            this.$root.$on('removeBlock', (data) => {
                if (this.viewIsInView(data.parent.id, this.view)) {
                    this.removeBlock(data.parent.settings.layer.blocks, data.block);
                }
            });

            this.$root.$on('moveBlock', (data) => {
                if (this.viewIsInView(data.parent.id, this.view)) {
                    this.moveBlock(data.parent.settings.layer.blocks, data.block, data.direction);
                }
            });
        },
        mounted : function() {
            if(this.showPreview) {
                this.getPreview();
            }
        },
        computed: {
            previewUrl: function() {
                let previewUrl = "rest.php/dsi/";
                let selectedAttachment = this.selectedAttachment != undefined ? this.selectedAttachment : -1;

                if (this.idDiffusion != 0 && this.idDiffusion != undefined) {
                    previewUrl += "diffusions/preview/" + this.idDiffusion + "/" + selectedAttachment;
                } else {
                    previewUrl += "views/preview/" + this.view.id;
                }

                return previewUrl;
            },
            isAttachment: function() {
                return this.selectedAttachment != undefined ? true : false;
            }
        },
		methods: {
            addBlock: function(params) {
                let blocks = [];
                if(params.cols != 1) {
                    for(var i=0; params.cols > i; i++) {
                        blocks.push({type: params.type, style: {}, content: "", blocks: []})
                    }
                }

                let parentBlock = { type: params.type, style: {}, content: "", blocks: blocks};
                this.view.settings.layer.blocks.push(parentBlock);
                this.$nextTick(() => {
                    if (blocks.length == 0) {
                        this.$root.$emit('editBlock', parentBlock);
                    } else {
                        this.$root.$emit('editBlock', blocks[blocks.length - 1]);
                    }
                })
            },
            removeBlock: function(blocks, block) {
                let deleted = false;
                for (let i=0; i < blocks.length; i++) {
                    const element = blocks[i];

                    if (element.id == block.id) {
                        // On supprime la vue liée au bloc si c'est un bloc vue
                        if(block.viewSelected || block.content.viewId)  {
                            const blockID = block.viewSelected || block.content.viewId;
                            const index = this.view.childs.findIndex(viewChild => viewChild.id == blockID);
                            // On met a jour le tableau child
                            if (index != -1) {
                                this.ws.post("views", 'delete', this.view.childs[index]);
                                this.$delete(this.view.childs, index);
                            }

                            if (block.viewSelected) {
                                block.viewSelected = "";
                            }
                            if (block.content.viewId) {
                                block.content.viewId = "";
                            }
                            block.itemSelected = "";
                        }

                        deleted = true;
                        blocks.splice(i, 1);
                        break;
                    }

                    if (element.blocks) {
                        this.removeBlock(element.blocks, block);
                    }
                }

                if (deleted) {
                    this.ws.post("views", 'save', this.view);
                    this.$root.$emit('editBlock', {});
                }
            },
            moveBlock: function(blocks, blockMoved, direction, parent = "") {

                const index = blocks.findIndex(blockChild => blockChild.id == blockMoved.id);
                if (index >= 0) {
                    const element = blocks[index];
                    if (direction === "up") {
                        this.$set(parent.blocks, index, parent.blocks[index-1]);
                        this.$set(parent.blocks, index-1, element);
                    } else {
                        this.$set(parent.blocks, index, parent.blocks[index+1]);
                        this.$set(parent.blocks, index+1, element);
                    }

                    this.$root.$emit('editBlock', {});
                } else {
                    for (let blockChild of blocks) {
                        if (blockChild.blocks) {
                            this.moveBlock(blockChild.blocks, blockMoved, direction, blockChild);
                        }
                    }
                }
            },
            getPreview: function() {
                let rootNode = document.getElementById(this.view.settings.layer.blocks[0].id);
                if (rootNode) {
                    rootNode = rootNode.cloneNode(true);
                    this.cleanHTML(rootNode);
                    this.view.settings.layer.content = rootNode.outerHTML.replace(/(<!--.*?-->)|(<!--[\S\s]+?-->)|(<!--[\S\s]*?$)/g, "");
                }

                this.showPrev = !this.showPrev;
                this.showTree = false;
                this.$root.$emit('editBlock', {});
            },
            getTree: function() {
                this.$root.$emit('editBlock', {});
                this.showPrev = false;
                this.showTree = !this.showTree;
            },
            getEditor: function() {
                this.$root.$emit('editBlock', {});
                this.showPrev = false;
                this.showTree = false;
            },
            fullscreen: function() {
                this.showPrev = false;
                this.showTree = false;
                this.$root.$emit("fullscreen");
            },
            cleanHTML: function(node) {
                if(node.className == "wysiwyg-add-section-actions" || node.className == "wysiwyg-section-label") {
                    node.remove();
                    return;
                }

                node.removeAttribute("class");
                let children = [...node.children];
                children.forEach(element => {
                    this.cleanHTML(element);
                });
            },
            resizeFrame() {
                let frame = document.getElementById("dsi-preview-frame");
                if (frame) {
                    try {
                        const iframeDocument = (frame.contentDocument) ? frame.contentDocument : (frame.contentWindow?.document ?? null);

                        let height = iframeDocument.documentElement.scrollHeight;
                        if(height >= 800) {
                            height = 800;
                        }

                        frame.removeAttribute('srcdoc');
                        frame.style.height = height + 'px';
                    } catch(e) {
                        frame.style.height = '800px';
                    }
                }
            },
            showParameters: function() {
                this.$refs.modal.show();
            },
            viewIsInView: function(searchIdView, view) {
                if(searchIdView == view.id) {
                    return true;
                }

				for (let child of view.childs) {
					if (searchIdView == child.id) {
						return true;
					}

					if (this.viewIsInView(searchIdView, child)) {
					    return true;
					}
				}

				return false;
			},
		}
	}
</script>