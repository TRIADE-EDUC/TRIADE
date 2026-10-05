<template>
	<div id="rmc-form">
        <div class="dsi-form-group-item">
            <input type='hidden' name='explicit_search' value='1'/>
            <input type='hidden' name='search_xml_file' value='search_fields'/>
            <input type='hidden' name='search_xml_file_full_path' value=''/>

            <input type="hidden" :id="'idRMCSelector_data_' + uniqueId" :name="'idRMCSelector_data_' + uniqueId" v-model="search">
            <input type="hidden" :id="'idRMCSelector_search_serialize_' + uniqueId" :name="'idRMCSelector_data_' + uniqueId">

            <span :id="'idRMCSelector_human_' + uniqueId" :name="'idRMCSelector_human_' + uniqueId" v-html="human"></span>

            <div id="rmc-form-action">
                <span role="button" type="button"
                    :title="messages.get('dsi', 'rmc_form_edit')"
                    :data-pmb-evt='setEvent("loadSetDialog")'
                    style="cursor: pointer">
                    <i class="fa fa-pencil dsi-rmc-button" aria-hidden="true" id='edit_dsi_set'></i>
                </span>
                <span role="button" type="button"
                    :title="messages.get('dsi', 'rmc_form_del')"
                    style="cursor: pointer"
                    @click="clearSearch">
                    <i class="fa fa-times dsi-rmc-button" aria-hidden="true" id='delete_dsi_set'></i>
                </span>
            </div>
        </div>
        <div v-show="human">
            <slot name="trySearch"></slot>
        </div>
	</div>
</template>

<script>
    export default {
		props : {
		    allowImport: {
		        type: Boolean,
                default: function() {
                    return false;
                }
		    },
		    rmc_type: {
		        type: String
		    },
		    entity_type: {
		        type: String
		    },
		    data: {
                type: Object,
                default: function() {
                    return { search : "", human_query : ""};
                }
		    }
		},
		data: function () {
            return {
                list: {},
                uniqueId: Date.now().toString(36) + Math.random().toString(36).substring(2)
            }
		},
        computed: {
            search: {
                get: function() {
                    return this.data ? this.data.search : ""
                },
                set: function(value) {
                    this.data.search = value;
                }
            },
            human: {
                get: function() {
                    return this.data ? this.data.human_query : ""
                },
                set: function(value) {
                    this.data.human_query = value;
                }
            }
        },
        mounted: function() {
            let event = new CustomEvent('RMCLoaded');
            window.dispatchEvent(event);

            window.addEventListener("changeRMCData_" + this.uniqueId, (event) => {
                this.$emit("updateRMC", event.detail.data);
                if (this.allowImport) {
                    this.$emit('startImport', true);
                }
            });
        },
		methods: {
            setEvent: function(method = "loadSetDialog") {
                return JSON.stringify({
                    class: "DsiForm",
                    type: "click",
                    method: method,
                    parameters: {
                        module: "selectors",
                        what: this.rmc_type,
                        entity_type: this.rmc_type,
                        entity_id: 0,
                        action: "advanced_search",
                        uniqueId: this.uniqueId
                    }
                })
            },
            clearSearch: function() {
                document.getElementById("idRMCSelector_data_" + this.uniqueId).value = ""
                document.getElementById("idRMCSelector_search_serialize_" + this.uniqueId).value = ""
                document.getElementById("idRMCSelector_human_" + this.uniqueId).innerHTML = ""

                this.list = {};
                this.$emit("updateRMC", {})
            }
		}
	}
</script>