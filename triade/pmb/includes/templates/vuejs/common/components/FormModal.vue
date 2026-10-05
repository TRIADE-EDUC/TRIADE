<template>
    <form :id="formModalId" action="#" :class="formClass" method="POST" @submit.prevent="submit">
        <Modal ref="modal" :title="title" @close="dispatchEvent('close', $event)">
            <slot></slot>
            <template v-if="haveFooter()" v-slot:footer>
                <div class="row">
                    <input v-if="showCancel"
                        type="button" 
                        class="bouton" 
                        @click="close" 
                        :value="messages.get('common', 'cancel')">

                    <input v-if="showSave" 
                        type="submit" 
                        class="bouton" 
                        :value="messages.get('common', 'submit')">

                    <input v-if="showDuplicate" 
                        type="button" 
                        class="bouton" 
                        @click="duplicate"
                        :value="messages.get('common', 'common_duplicate')">

                    <input v-if="showDelete" 
                        type="button" 
                        class="bouton right" 
                        @click="remove"
                        :value="messages.get('common', 'remove')">
                </div>
            </template>
        </Modal>
    </form>
</template>

<script>
import Modal from './Modal.vue';

let uid = 0;
export default {
    components: {
        Modal
    },
    props: {
        formClass: {
            default: () => ''
        },
        showCancel: {
            type: Boolean,
            default: () => false
        },
        showSave: {
            type: Boolean,
            default: () => false
        },
        showDuplicate: {
            type: Boolean,
            default: () => false
        },
        showDelete: {
            type: Boolean,
            default: () => false
        },
        title: {
            type: String,
            default: () => ''
        }
    },
    data: function () {
        return {
            id: 0
        }
    },
    computed: {
        formModalId: function () {
            return `form-modal-${this.id}`;
        }
    },
    create: function () {
        this.id = uid;
        uid++;
    },
    methods: {
        show: function () {
            this.$refs.modal.show();
            this.dispatchEvent('show');
        },
        close: function () {
            this.$refs.modal.close();
            this.dispatchEvent('close');
        },
        dispatchEvent: function (event, data) {
            this.$emit(event, data || undefined);
        },
        submit: function () {
            const form = document.getElementById(this.formModalId);
            if (form) {
                const formData = new FormData(form);
                const formDataObj = {};
                formData.forEach((value, key) => (formDataObj[key] = value));
                this.dispatchEvent("submit", formDataObj);
            }
        },
        duplicate: function () {
            this.dispatchEvent("duplicate");
        },
        remove: function () {
            this.dispatchEvent("remove");
        },
        haveFooter: function () {
            return this.showCancel || this.showSave || this.showDuplicate || this.showDelete;
        }
    }
}
</script>