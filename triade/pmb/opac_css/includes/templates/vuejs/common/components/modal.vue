<template>
    <div class="modal-container" v-show="showModal" style="display:none;"
        ref="modal-container"
        @keydown.escape.prevent="closeModal"
        @click.self="closeModal"
    >
        <div class="modal" role="dialog" :aria-label="label" aria-modal="true">
            <div class="modal-header">
                <button class="modal-close" type='button' v-on:click.prevent="closeModal">
                    {{ messages.get('common', 'modal_close') }} <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-content">
                <slot></slot>
            </div>
            <div class="modal-footer">
                <button v-on:click.prevent="closeModal" type="button" class="bouton modal-cancel" >
                    {{ messages.get('common', 'modal_cancel') }}
                </button>
                <button v-on:click.prevent="validModal" type="button" class="bouton modal-valid" >
                    {{ messages.get('common', 'modal_valid') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "modal",
  props: {
    showModal: {
      type: Boolean,
      default: false
    },
    label: {
      type: String,
      required: true
    }
  },
  methods: {
    closeModal() {
      this.$emit("closeModal");
    },
    validModal() {
      this.$emit("validModal");
    },
    focusContainer() {
      this.$refs["modal-container"].focus();
    }
  },
  watch: {
    showModal(newValue) {
      if (newValue) {
        this.$nextTick(this.focusContainer);
      }
    }
  }
}
</script>