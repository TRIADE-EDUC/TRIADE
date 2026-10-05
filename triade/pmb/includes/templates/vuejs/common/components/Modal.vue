<template>
    <Transition>
        <div :id="modalBackgroundId" class="modal-background cursor-pointer" v-show="display" @click.self="close">
            <div :id="modalId" class="modal cursor-default" aria-modal="true">
                <div class="modal-header">
                    <div class="row">
                        <h2 class="left modal-title" v-if="title">{{ title }}</h2>
                        <button type="button" class="bouton right close" @click="close">
                            <!-- {{ messages.get('common', 'remove_short') }} -->
                            <span class="visually-hidden">{{ messages.get('common', 'close') }}</span>
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-content">
                    <slot></slot>
                </div>
                <div class="modal-footer" v-if="hasFooterSlot">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script>
let uid = 0;
export default {
    props: {
        title: {
            type: String,
            default: () => ''
        }
    },
    data: function () {
        return {
            id: 0,
            display: false
        }
    },
    computed: {
        modalBackgroundId: function () {
            return `modal-background-${this.id}`;
        },
        modalId: function () {
            return `modal-${this.id}`;
        },
        hasFooterSlot() {
            return !!this.$slots.footer
        }
    },
    create: function () {
        this.id = uid;
        uid++;
    },
    methods: {
        show: function() {
            this.display = true;
            this.dispatchEvent('show');

            const modal = document.getElementById(this.modalId);
            if (modal) {
                modal.focus();
            }
        },
        close: function() {
            this.display = false;
            this.dispatchEvent('close');
        },
        dispatchEvent: function (event) {
            this.$emit(event);
        }
    }
}
</script>

<style>
.v-enter-active,
.v-leave-active {
    transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>