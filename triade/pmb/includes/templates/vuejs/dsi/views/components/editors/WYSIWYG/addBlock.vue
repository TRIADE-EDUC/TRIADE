<template>
    <addElementModal v-if="show" @close="$emit('close')">
        <h3 slot="header">{{ messages.get('dsi', 'view_wysiwyg_modal_choice') }}</h3>
        <div slot="body">
            <h4>{{ messages.get('dsi', 'view_wysiwyg_modal_sections') }}</h4>
            <ul class="wysiwyg-add-section-list">
                <li>
                    <button @click.prevent="addBlock({type: 1, cols: 1, orientation: 'column'})" 
                            :title="messages.get('dsi', 'view_wysiwyg_choice_alt') + '1' + messages.get('dsi', 'view_wysiwyg_choice_row_alt')">
                        <img src="images/dsi/full.svg" :alt="messages.get('dsi', 'view_wysiwyg_choice_alt') + '1' + messages.get('dsi', 'view_wysiwyg_choice_row_alt')">
                    </button>
                </li>
                <li v-for="i in 5">
                    <button @click.prevent="addBlock({type: 1, cols: i+1, orientation: 'column'})" 
                            :title="messages.get('dsi', 'view_wysiwyg_choice_alt') + (i+1) + messages.get('dsi', 'view_wysiwyg_choice_row_alt')">
                        <img :src="'images/dsi/line_' + (i+1) + '.svg'" :alt="messages.get('dsi', 'view_wysiwyg_choice_alt') + (i+1) + messages.get('dsi', 'view_wysiwyg_choice_row_alt')">
                    </button>
                </li>
                <li>
                    <button @click.prevent="addBlock({type: 1, cols: 1, orientation: 'row'})" 
                            :title="messages.get('dsi', 'view_wysiwyg_choice_alt') + '1' + messages.get('dsi', 'view_wysiwyg_choice_column_alt')">
                        <img src="images/dsi/full.svg" :alt="messages.get('dsi', 'view_wysiwyg_choice_alt') + '1' + messages.get('dsi', 'view_wysiwyg_choice_column_alt')">
                    </button>
                </li>
                <li v-for="i in 5">
                    <button @click.prevent="addBlock({type: 1, cols: i+1, orientation: 'row'})" 
                            :title="messages.get('dsi', 'view_wysiwyg_choice_alt') + (i+1) + messages.get('dsi', 'view_wysiwyg_choice_column_alt')">
                        <img :src="'images/dsi/col_' + (i+1) + '.svg'" :alt="messages.get('dsi', 'view_wysiwyg_choice_alt') + (i+1) + messages.get('dsi', 'view_wysiwyg_choice_column_alt')">
                    </button>
                </li>
            </ul>
        </div>
        <div slot="footer">
            <div>
                <h4>{{ messages.get('dsi', 'view_wysiwyg_modal_elements') }}</h4>
                <div class="dsi-cards dsi-cards-wysiwyg">
                    <div style="cursor: pointer;" role="button" class="dsi-card">
                        <p type="button" @click="addBlock({type: 2, cols: 1})">{{ messages.get('dsi', 'view_wysiwyg_input_text') }}</p>
                    </div>
                    <div style="cursor: pointer;" role="button" class="dsi-card">
                        <p type="button" @click="addBlock({type: 6, cols: 1})">{{ messages.get('dsi', 'view_wysiwyg_input_text_rich') }}</p>
                    </div>
                    <div style="cursor: pointer;" role="button" class="dsi-card">
                        <p type="button" @click="addBlock({type: 5, cols: 1})">{{ messages.get('dsi', 'view_wysiwyg_input_list') }}</p>
                    </div>
                    <div style="cursor: pointer;" role="button" class="dsi-card" @click="addBlock({type: 3, cols: 1})">
                        <p>{{ messages.get('dsi', 'view_wysiwyg_input_image') }}</p>
                    </div>
                    <div style="cursor: pointer;" role="button" class="dsi-card" @click="addBlock({type: 4, cols: 1})">
                        <p>{{ messages.get('dsi', 'view_wysiwyg_input_video') }}</p>
                    </div>
                    <div style="cursor: pointer;" role="button" class="dsi-card" @click="addBlock({type: 7, cols: 1})">
                        <p>{{ messages.get('dsi', 'view_wysiwyg_view') }}</p>
                    </div>
                </div>
            </div>
            <div style="margin-top: 1rem;">
                <h4>{{ messages.get('dsi', 'view_wysiwyg_import_view') }}</h4>
                <div class="dsi-cards dsi-cards-wysiwyg">
                    <div style="cursor: pointer;" role="button" class="dsi-card" @click="addBlock({type: 8, cols: 1})">
                        <p>{{ messages.get('dsi', 'view_wysiwyg_view_wysiwyg') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </addElementModal>
</template>

<script>
    import addElementModal from '../../../../components/modal.vue';
	export default {
		props : ["blocks", "show", "root", 'view'],
		components: {
            addElementModal
        },
		data: function () {
			return {
                showModal: false,
                id: 0
			}
		},
        created: function() {
            this.showModal = this.show;
            this.$root.$on("showModal", function(id){
                this.showModal = true;
                this.id = id;
            });
        },
		methods: {
            addBlock: function(params) {
                params.id = this.id;
                let props = this.root ? this.blocks[0].blocks : this.blocks;

                let blocks = [];
                if(params.cols != 1) {
                    for(var i=0; params.cols > i; i++) {
                        blocks.push({type: params.type, style: {flexDirection: params.orientation}, content: "", blocks: []})
                    }
                }

                let parentBlock = { type: params.type, style: {flexDirection: params.orientation}, content: "", blocks: blocks};
                props.push(parentBlock);
                this.$nextTick(() => {
                    if (blocks.length == 0) {
                        this.$root.$emit('editBlock', parentBlock);
                    } else {
                        this.$root.$emit('editBlock', blocks[blocks.length - 1]);
                    }
                })
                this.$emit('close');
            }
		}
	}
</script>