<template>
    <div class="dsi-form-selector">
        <div class="dsi-form-group">
            <label class="etiquette" for="selectorList"></label>
            <div class="dsi-form-group-content">
                <select id="selectorList" name="selectorList" v-model="selector.namespace" @change="$emit('updateSettings')" required>
                    <option value="" disabled>{{ messages.get('dsi', 'source_form_default_selector') }}</option>
                    <option v-for="(selectorItem, index) in selectorList" :key="index" :value="selectorItem.namespace">
                    	 {{ selectorItem.name }}
                    </option>
                </select>
            </div>
        </div>
        <selector v-if="haveSubSelector" :selector="selector.selector" :child="selector.namespace"></selector>
		<component v-if="selectorType && selector.data && currentSelector"
            :is="selectorType"
            :data="selector.data"
            :rmc_type="rmc_type"
            entity_type="record"
            :module-msg="moduleMsg"
            :msg="currentSelector.messages"
            @updateRMC="updateRMC">
            <template v-slot:trySearch>
                <trySearch :selector="selector"></trySearch>
            </template>
        </component>
        <form-sort v-if="sorts.length" :sort="selector.sort" :sort-types="sorts"></form-sort>
        <trySearch v-if="!selectorType && selector.namespace" :selector="selector"></trySearch>
    </div>
</template>

<script>
    import trySearch from "../../../components/trySearch.vue";
	import RecordRMCSelector from "../../../components/RMCForm.vue";
	import ArticleRMCSelector from "../../../components/RMCForm.vue";
	import SectionRMCSelector from "../../../components/RMCForm.vue";
	import ParentSectionSelector from "./selectors/ArticleParentSection.vue";
    import AllDiffusionSelector from './selectors/AllDiffusionSelector.vue';
	import WatchSelector from "./selectors/ItemWatchByWatch.vue";
    import ArticleByIdSelector from './selectors/ArticleByIdSelector.vue';
    import formSort from '../../../diffusions/components/formSort.vue';
    import RecordCaddieSelector from './selectors/RecordCaddieSelector.vue';
    import SectionByIdSelector from "./selectors/SectionByIdSelector.vue";
    import AnimationByDateSelector from "./selectors/AnimationByDateSelector.vue";
    import AnimationAllFieldsSelector from "./selectors/AnimationAllFieldsSelector.vue";

	export default {
		name: "selector",
		props : ["selector", "child"],
		components: {
            trySearch,
			RecordRMCSelector,
            ParentSectionSelector,
            WatchSelector,
            AllDiffusionSelector,
            ArticleByIdSelector,
            formSort,
            RecordCaddieSelector,
            ArticleRMCSelector,
            SectionByIdSelector,
            SectionRMCSelector,
            AnimationByDateSelector,
            AnimationAllFieldsSelector
		},
		data: function () {
			return {
				selectorList: [],
                haveSubSelector: false,
                moduleMsg : {},
                sorts : []
			}
		},
        watch: {
            "selector.namespace": function() {
                this.$set(this.selector, "data", {})
                this.$set(this.selector, "selector", {})

                this.getHaveSubSelector();
                this.getSelectorSort();
            }
        },
		created: async function() {
			await this.getSelectorList();

			if(this.selector.namespace === undefined) {
				this.$set(this.selector, "namespace", "")
			}

			if(this.selector.selector === undefined) {
				this.$set(this.selector, "selector", {})
			}

			if(this.selector.data === undefined) {
				this.$set(this.selector, "data", {})
			}

			if(this.selector.sort === undefined) {
				this.$set(this.selector, "sort", {
                    "namespace" : "",
                    "data" : {
                        "direction" : ""
                    }
                })
			}


            await this.getHaveSubSelector();
            await this.getSelectorSort();
		},
        beforeCreate : async function() {
            this.moduleMsg = await this.dsiMessages.getModuleMessages("selector");
        },
		computed: {
			selectorType : function() {
                if (this.selector.namespace) {
                    let explodedName = this.selector.namespace.split("\\");
                    let className = explodedName[explodedName.length-1];
                    if (this.$options.components[className]) {
                        return className;
                    }
                }
                return "";
            },
            currentSelector : function() {
                if(this.selector.namespace) {
                    return this.selectorList.find(s => s.namespace == this.selector.namespace);
                }
                return null;
            },
            rmc_type : function() {
                switch(this.selectorType) {
                    case "RecordRMCSelector":
                        return "notice";
                    case "ArticleRMCSelector":
                    case "SectionRMCSelector":
                        return "cms_editorial";
                    default:
                        return "";
                }
            }
		},
		methods: {
			getSelectorList: async function() {
				let response = await this.ws.get('items', 'getSelectorList/' + encodeURI(this.child.replaceAll("\\", "-")));
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else {
					this.selectorList = response;
				}
			},
            getHaveSubSelector: async function() {
                if(this.selector.namespace !== "") {
                    let response = await this.ws.get('items', 'haveSubSelector/' + encodeURI(this.selector.namespace.replaceAll("\\", "-")));
                    if (response.error) {
                        this.notif.error(this.messages.get('dsi', response.errorMessage));
                    } else {
                        this.haveSubSelector = response[0];
                    }
                }
            },
            getSubSelectorList: async function() {
                if(this.selector.namespace !== "") {
                    let response = await this.ws.get('items', 'getSelectorList/' + encodeURI(this.selector.namespace.replaceAll("\\", "-")));
                    if (response.error) {
                        this.notif.error(this.messages.get('dsi', response.errorMessage));
                    } else {
                        this.subSelectorList = response;
                    }
                }
            },
			updateRMC: function(data) {
				this.$set(this.selector, "data", data);
            },
            getSelectorSort : async function() {
                if(this.selector.namespace !== "") {
                    this.sorts = await this.ws.get("items", "getSelectorSorts/" + encodeURI(this.selector.namespace.replaceAll("\\", "-")));
                }
            }
		}
	}
</script>