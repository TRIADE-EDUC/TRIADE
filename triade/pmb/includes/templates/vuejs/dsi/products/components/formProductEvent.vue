<template>
	<div v-if="eventActive" class="dsi-form-diffusion-event">
        <fieldset class="dsi-form-diffusion-event-list">
            <legend class="dsi-legend-setting">{{ messages.get('dsi', 'event_form_triggers_list') }}</legend>
            <ul>
                <li v-for="(event, index) in product.events" :key="index">
                    <span @click="changeSelectedEvent(index)" :class="eventActive.id == event.id ? 'active' : ''">{{ event.name }}</span>
                    <button type="button" :value="event.id" @click="removeEvent(event.id)"><i class="fa fa-trash"></i></button>
                </li>
                <li v-if="product.events.length === 0">
                    <p>{{ messages.get('dsi', 'event_form_triggers_empty') }}</p>
                </li>
            </ul>
            <button type="button" @click="fetchData(false)" :class="eventActive.id == 0 ? 'disabled bouton' : 'bouton'">
                <i class="fa fa-plus"></i>
            </button>
        </fieldset>
        <div class="dsi-form-diffusion-event-exchange">
            <i class="fa fa-exchange" aria-hidden="true"></i>
        </div>
        <fieldset class="dsi-form-diffusion-event-add">
            <legend class="dsi-legend-setting">{{ messages.get('dsi', 'event_form_triggers_add') }}</legend>
            <formEvent v-if="eventActive" :event="eventActive" :types="eventTypeList" :is_model="false" :is_product="true" @addEvent="addEvent"></formEvent>
        </fieldset>
	</div>
</template>

<script>
    import formEvent from "../../triggers/components/formEvent.vue";
	export default {
		props : ["product"],
        components: {
            formEvent,
        },
		data: function () {
			return {
			    eventTypeList: [],
			    eventActive: null
			}
		},
		created: function() {
			this.fetchData(true);
		},
		methods: {
			fetchData: async function(force = false) {
                if(force || this.eventActive.id != 0) {
                    const promises = [
                        this.ws.get('triggers', 'getTypeListAjax'),
                        this.ws.get('triggers', 'getEmptyInstance')
                    ];
                    const result = await Promise.all(promises);
                    this.eventTypeList = result[0];
                    this.$set(this, 'eventActive', {});
                    this.$set(this, 'eventActive', result[1]);
                }
			},
            addEvent: async function(event) {
                const index = this.product.events.findIndex((e) => e.id == event.id);
                if(index == -1) {
                    this.$set(this.product.events, this.product.events.length, event);
                }

                let response = await this.ws.post('products', 'save', this.product);
				if (response.error) {
                    this.notif.error(this.messages.get('dsi', response.errorMessage));
				}else {
					this.notif.info(this.messages.get('common', 'success_save'));
				}
            },
            removeEvent: async function(idEvent) {
                let response = await this.ws.post('triggers', 'deleteEventProduct', {"num_event": idEvent, "num_product": this.product.id});
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				}else {
                    this.product.events.splice(this.product.events.findIndex((event) => event.id == idEvent), 1);
                    if(this.eventActive.id == idEvent) {
                        this.eventActive = await this.ws.get('triggers', 'getEmptyInstance');
                    }
                    this.notif.info(this.messages.get('common', 'success_save'));
				}
            },
            changeSelectedEvent: function(index) {
                this.eventActive = this.product.events[index];
            }
		}
	}
</script>