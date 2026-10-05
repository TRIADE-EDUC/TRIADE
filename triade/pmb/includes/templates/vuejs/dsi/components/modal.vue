<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper" @click="close($event)">
                <div class="modal-container">
                    <div class="modal-header">
                        <slot name="header">default header</slot>
                    </div>
                    <div class="modal-body">
                        <slot name="body">default body</slot>
                    </div>
                    <div class="modal-footer">
                        <slot name="footer">default footer</slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
	export default {
		methods: {
            close: function(e) {
                if(e.target.className === "modal-wrapper") {
                    this.$emit("close");
                }
            }
		}
	}
</script>