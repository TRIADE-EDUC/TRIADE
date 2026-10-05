<template>
    <div class="docnumsList">
        <modal
            :showModal="showModal"
            @closeModal="closeModal"
            @validModal="validModal"
            :label="messages.get('ai', 'docnums_rename')"
            class="shared_docnums_list_modal shared_docnums_list_rename"
        >
            <fieldset>
                <legend>{{ messages.get('ai', 'docnum_name') }}</legend>
                <input type="text" name="docnum_name" v-model="editDocnum.name" />
            </fieldset>
        </modal>

        <h2>{{ messages.get('ai', 'docnums_list_title') }}</h2>
        <ul class="list" v-if="docnums.length > 0">
            <li class="docnum" v-for="(docnum, index) in docnums" :key="index">
                <span class="name">{{ docnum.name }}</span>
                <button
                    v-if="visionneuse_allow"
                    type="button"
                    class="bouton button-see"
                    @click="seeDocnum(index)"
                    :title="messages.get('ai', 'docnums_list_see')"
                >
                    <span class="fa fa-eye" aria-hidden="true"></span>
                </button>
                <a v-else :href="docnum.url" target="_blank" :title="messages.get('ai', 'docnums_list_see')">
                    <span class="fa fa-eye" aria-hidden="true"></span>
                </a>
                <button
                    type="button"
                    class="bouton button-rename"
                    @click="renameDocnum(index)"
                    :title="messages.get('ai', 'docnums_list_rename')"
                >
                    <img :src="images.get('b_edit.png')" :alt="messages.get('ai', 'docnums_list_rename')">
                </button>
                <button
                    type="button"
                    class="bouton button-delete"
                    @click="removeDocnum(index)"
                    :title="messages.get('ai', 'docnums_list_remove')"
                >
                    <img :src="images.get('empty-001.svg')" :alt="messages.get('ai', 'docnums_list_remove')">
                </button>
            </li>
        </ul>
        <p v-else>{{ messages.get('ai', 'docnums_list_empty') }}</p>
    </div>
</template>

<script>
import modal from "../../../common/components/modal.vue";

export default {
    name: "DocnumsList",
    props: {
        shared_list_id: {
            type: Number,
            required: true
        },
        visionneuse_allow: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            docnums: [],
            showModal: false,
            editDocnum: {
                index: 0,
                id: 0,
                name: ''
            }
        }
    },
    components: {
        modal
    },
    mounted() {
        window.addEventListener('DocumentUploaded', this.fetchDocnumsList.bind(this));
        this.fetchDocnumsList()
    },
    methods: {
        sendToVisionneuse(id) {
            document.getElementById('visionneuseIframe').src = './visionneuse.php?driver=pmb_document&lvl=visionneuse&cms_type=shared_list&id=' + id;
        },
        seeDocnum(index) {
            open_visionneuse(
                this.sendToVisionneuse.bind(this),
                this.docnums[index].id
            );
        },
        closeModal() {
            this.showModal = false;
            this.editDocnum = { index: 0, id: 0, name: '' };
        },
        validModal() {
            if (
                this.editDocnum.name == '' ||
                this.editDocnum.name == this.docnums[this.editDocnum.index].name
            ) {
                return false;
            }

            this.ws.post('docnums', 'rename', {
                'id': this.editDocnum.id,
                'name': this.editDocnum.name
            }).then((response) => {
                this.docnums[this.editDocnum.index].name = this.editDocnum.name;
                this.showModal = false;
                this.editDocnum = { index: 0, id: 0, name: '' };
            }).catch((error) => {
                console.error(error);
                this.showModal = false;
                this.editDocnum = { index: 0, id: 0, name: '' };
            });
        },
        renameDocnum(index) {
            this.editDocnum = {
                index: index,
                id: this.docnums[index].id,
                name: this.docnums[index].name
            };
            this.showModal = true;
        },
        fetchDocnumsList() {
            this.ws.post('docnums', 'list', {
                'id': this.shared_list_id
            }).then((response) => {
                this.docnums = response.list || [];
            }).catch((error) => {
                this.docnums = [];
                console.error(error);
            });
        },
        removeDocnum(index) {
            const docnum = this.docnums[index] || null;
            if (!docnum) {
                return false;
            }

            const msgConfirm = this.messages.get('ai', 'docnums_list_remove_confirm').replace('%s', docnum.name);
            if (confirm(msgConfirm)) {
                this.ws.post('docnums', 'remove', {
                    'id': docnum.id
                }).then(() => {
                    this.docnums.splice(index, 1);
                    window.dispatchEvent(new CustomEvent("DocumentRemoved"));
                }).catch((error) => {
                    console.error(error);
                })
            }
            return true;
        }
    }
};
</script>