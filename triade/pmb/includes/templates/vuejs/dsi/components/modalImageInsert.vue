<template>
	<div class="dsi-modal-image-insert">
		<modal 
			ref="modal" 
			:title="messages.get('dsi', 'dsi_image_collection_insert_title')" 
			@close="dispatchEvent('close', $event)" 
			:modal-style="{ width: '75%' }">
			
            <slot>
				<imageCollection :insert="true" @insert="$emit('insert', $event)"></imageCollection>
			</slot>
        </modal>
	</div>
</template>

<script>
	import modal from "@/common/components/Modal.vue";
	import imageCollection from "@dsi/imageCollection/components/imageCollection.vue";

	export default {
		props: ["showModal"],
		components: {
			modal,
			imageCollection
		},
		methods: {

			/**
			 * Affiche la modal en déclenchant l'événement 'show'.
			 * 
			 * @return {void}
			 */
			show: function () {
				this.$refs.modal.show();
				this.dispatchEvent('show');
			},
			
			/**
			 * Cache la modal en déclenchant l'événement 'close'.
			 * 
			 * @return {void}
			 */
			close: function () {
				this.$refs.modal.close();
				this.dispatchEvent('close');
			},

			/**
			 * Envoie un événement en direction du parent en émulant un événement natif.
			 * @param {string} event
			 * @param {mixed} data
			 * @return {void}
			 */
			dispatchEvent: function (event, data) {
				this.$emit(event, data || undefined);
			}
		}
	}
</script>