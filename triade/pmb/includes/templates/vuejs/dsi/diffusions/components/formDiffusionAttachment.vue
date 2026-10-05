<template>
	<div id="dsi-form-diffusion-attachment" @click="">
        <div>
            <button class="bouton" name="add-attachment-button" @click="addNewAttachment">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
            </button>
            <label for="add-attachment-button">{{ messages.get('dsi', 'channel_form_add_attachment') }}</label>
        </div>
        <div class="dsi-cards">
            <div :class="selectedAttachment == index ? 'dsi-card active' : 'dsi-card'"
                 v-for="(attachment, index) in diffusion.attachments" @click="selectedAttachment = index"
                 style="cursor: pointer">
                <span v-if="renameIndex != index && diffusion.settings.attachments[index]">{{ diffusion.settings.attachments[index].name }}</span>
                <input v-if="renameIndex == index"
                    type="text"
                    id="view-name" 
                    name="view-name" 
                    v-model="diffusion.settings.attachments[index].name" 
                    @keyup.enter="rename(index)"
                    required>&nbsp;
                <i v-if="renameIndex != index"
                    class="fa fa-pencil" 
                    aria-hidden="true"
                    :title="messages.get('dsi', 'channel_form_edit_name_attachment')" 
                    @click.stop="rename(index)">
                </i>
                <i v-else class="fa fa-check"
                    aria-hidden="true"
                    :title="messages.get('dsi', 'channel_form_save_name_attachment')"
                    @click.stop="rename(index)">
                </i>&nbsp;
                <i class="fa fa-trash"
                    aria-hidden="true"
                    :title="messages.get('dsi', 'channel_form_del_attachment')"
                    @click.stop="removeAttachment(index)">
                </i>
            </div>
        </div>
        <formDiffusionView v-if="attachmentsLoaded && selectedAttachment != -1"
            :diffusion="diffusion" 
            :channelCompatibility="channelCompatibility" 
            :attachment="diffusion.attachments[selectedAttachment]">
        </formDiffusionView>
	</div>
</template>

<script>
	import formDiffusionView from "./formDiffusionView.vue";
	export default {
		props : ["diffusion", "channelCompatibility"],
        components: {
            formDiffusionView
        },
		data: function () {
			return {
                viewTypes: [],
                attachmentsLoaded: false,
                selectedAttachment: -1,
                renameIndex: -1
			}
		},
		created: function() {
            if(!this.diffusion.settings.attachments) {
                this.$set(this.diffusion.settings, "attachments", []);
            }
            this.fetchData();
		},
        computed: {
            compatibilities: function() {
                if(this.channelCompatibility.compatibility.attachments) {
                    if(this.channelCompatibility.compatibility.attachments.view) {
                        return this.channelCompatibility.compatibility.attachments.view
                    }
                }
                return [];
            }
        },
		methods: {
            fetchData: async function() {
				const promises = [];

				// Vérifie si les types de vues ne sont pas déjà chargés
				const shouldFetchViews = !this.$root.types?.views;
				if (shouldFetchViews) {
					promises.push(this.ws.get('views', 'getTypeListAjax'));
				}

				const results = await Promise.all(promises);
				this.viewTypes = shouldFetchViews ? results[0] : JSON.parse(JSON.stringify(this.$root.types.views));

                await this.loadAttachments();
			},
            addNewAttachment: async function() {
                this.attachmentsLoaded = false;
                
                this.$set(this.diffusion.settings.attachments, this.diffusion.settings.attachments.length, 
                { name: this.getDefaultName(), view: 0, item: 0 }
                );
                
                await this.loadAttachments();
                this.selectedAttachment = this.diffusion.attachments.length == 0 ? 0 : this.diffusion.attachments.length-1;
            },
            loadAttachments: async function() {
                const promises = [];

                if(!this.diffusion.attachments) {
                    this.$set(this.diffusion, "attachments", []);
                }

                for(let i = 0; i < this.diffusion.settings.attachments.length; i++) {
                    const attachment = this.diffusion.settings.attachments[i];
                    if(!this.diffusion.attachments[i]) {
                        if(attachment.view == 0) {
                            promises.push(this.ws.get('views', 'getEmptyInstance'));
                        } else {
                            promises.push(this.ws.get('views', 'getInstance/' + attachment.view));
                        }

                        if(attachment.item == 0) {
                            promises.push(this.ws.get('items', 'getEmptyInstance'));
                        } else {
                            promises.push(this.ws.get('items', 'getInstance/' + attachment.item));
                        }
                    }
                }

                if(promises.length) {
                    const result = await Promise.all(promises);
                    let index = 0;

                    for(let i = 0; i < this.diffusion.settings.attachments.length; i++) {
                        if(!this.diffusion.attachments[i]) {
                            this.$set(this.diffusion.attachments, this.diffusion.attachments.length,
                                { view: result[index], item: result[index+1] }
                            );
                            index = index + 2;
                        }
                    }
                }

                this.attachmentsLoaded = true;
            },
            removeAttachment: async function(index) {
                if (confirm(this.messages.get('dsi', 'confirm_del'))) {
                    let promises = [];

                    if(this.diffusion.attachments[index].view.id && this.diffusion.attachments[index].view.id != 0) {
                        promises.push(this.ws.post("views", 'delete', this.diffusion.attachments[index].view));
                    }
                    
                    if(this.diffusion.attachments[index].item.id && this.diffusion.attachments[index].item.id != 0) {
                        promises.push(this.ws.post("items", 'delete', this.diffusion.attachments[index].item));
                    }

                    if(promises.length) {
                        const response = await Promise.all(promises);
                        if ((response[0] && response[0].error) || (response[1] && response[1].error)) {
                            this.notif.error(this.messages.get('dsi', response.errorMessage));
                            return;
                        }
                    }

                    this.diffusion.settings.attachments.splice(index, 1);
                    this.diffusion.attachments.splice(index, 1);

                    if(this.selectedAttachment == index) {
                        this.selectedAttachment = -1;
                    }

                    await this.saveDiffusion();
                }
            },
            saveDiffusion: async function() {
				let response = await this.ws.post('diffusions', 'save', this.diffusion);
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				}else {
					this.notif.info(this.messages.get('common', 'success_save'));
				}
			},
            getDefaultName: function() {
                return this.messages.get('dsi', 'channel_form_label_attachment') + '(' + (this.diffusion.settings.attachments.length+1) + ')';
            },
            rename: async function(index) {
                if(this.renameIndex == index) {
                    this.renameIndex = -1
                    await this.saveDiffusion();
                    return;
                }

                this.renameIndex = index
            }
		},
	}
</script>