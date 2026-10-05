<template>
    <form v-if="entities && formData.messages">
        <div class="dsi-form-group">
            <label class="etiquette" for="viewEntityTypeList">{{ messages.get('dsi', 'view_form_entity_type') }}</label>
            <div class="dsi-form-group-content">
                <select id="viewEntityTypeList" name="viewEntityTypeList"
                    @focus="currentType = item.settings.entityType" v-model="item.settings.entityType">
                    <option value="0" disabled>{{ messages.get('dsi', 'view_form_default_entity_type') }}</option>
                    <option v-for="(entityType, index) in availableEntities" :value="entityType.value" :key="index"
                        :disabled="entityType.disabled">
                        {{ entityType.label }}
                    </option>
                </select>
            </div>
        </div>

        <div class="dsi-form-group">
            <label class="etiquette" for="linked-grouping-view">{{ formData.messages['linked-grouping-view'] }}</label>
            <div class="dsi-form-group-content">
                <select id="linked-grouping-view" name="linked-grouping-view" v-model.number="item.settings.linkedView">
                    <option value="0" disabled>{{ formData.messages['linked-grouping-view-select'] }}</option>
                    <option v-for="(view, index) in formData.views" :value="view.value" :key="index">
                        {{ view.label }}
                    </option>
                </select>
            </div>
        </div>

        <div class="dsi-form-group">
            <h3>{{ formData.messages['content'] }}</h3>
		</div>

        <div class="dsi-form-group">
            <label class="etiquette" for="title">{{ formData.messages['title'] }}</label>
            <div class="dsi-form-group-content">
                <input type="text" name="title" id="title" v-model.trim="item.settings.title"/>
            </div>
        </div>

        <div class="dsi-form-group">
			<label class="etiquette" for="default-list">{{ formData.messages['type-List'] }}</label>
			<div class="dsi-form-group-content">
                <div class="colonne">
                    <div class="dsi-form-group-content">
                        <input type="radio" value="0" name="listType" id="default-list" v-model.number="item.settings.listType">
                        <label class="etiquette" for="default-list" :title="formData.messages['default-list']">
                            <default-list></default-list>
                        </label>
                    </div>
                </div>
                <div class="colonne">
                    <div class="dsi-form-group-content">
                        <input type="radio" value="1" name="listType" id="bullet-list" v-model.number="item.settings.listType">
                        <label class="etiquette" for="bullet-list" :title="formData.messages['bullet-list']">
                            <bullet-list></bullet-list>
                        </label>
                    </div>
                </div>
                <div class="colonne">
                    <div class="dsi-form-group-content">
                        <input type="radio" value="2"  name="listType" id="lowercase-alpha" v-model.number="item.settings.listType">
                        <label class="etiquette" for="lowercase-alpha" :title="formData.messages['lowercase-alpha']">
                            <lowercase-alpha></lowercase-alpha>
                        </label>
                    </div>
                </div>

                <div class="colonne">
                    <div class="dsi-form-group-content">
                        <input type="radio" value="3" name="listType" id="uppercase-alpha" v-model.number="item.settings.listType">
                        <label class="etiquette" for="uppercase-alpha" :title="formData.messages['uppercase-alpha']">
                            <uppercase-alpha></uppercase-alpha>
                        </label>
                    </div>
                </div>

                <div class="colonne">
                    <div class="dsi-form-group-content">
                        <input type="radio" value="4" name="listType" id="lower-roman-numeral" v-model.number="item.settings.listType">
                        <label class="etiquette" for="lower-roman-numeral" :title="formData.messages['lower-roman-numeral']">
                            <lower-roman-numeral></lower-roman-numeral>
                        </label>
                    </div>
                </div>

                <div class="colonne">
                    <div class="dsi-form-group-content">
                        <input type="radio" value="5" name="listType" id="upper-roman-numeral" v-model.number="item.settings.listType">
                        <label class="etiquette" for="upper-roman-numeral" :title="formData.messages['upper-roman-numeral']">
                            <upper-roman-numeral></upper-roman-numeral>
                        </label>
                    </div>
                </div>
			</div>
		</div>
    </form>
</template>

<script>
import defaultList from "./summary-list/default.vue";
import bulletList from "./summary-list/bullet-list.vue";
import lowercaseAlpha from "./summary-list/lowercase-alpha.vue";
import uppercaseAlpha from "./summary-list/uppercase-alpha.vue";
import lowerRomanNumeral from "./summary-list/lower-roman-numeral.vue";
import UpperRomanNumeral from "./summary-list/upper-roman-numeral.vue";

export default {
    name: "summaryView",
    props: ["item", "entities"],
    components: {
        defaultList,
        bulletList,
        lowercaseAlpha,
        uppercaseAlpha,
        lowerRomanNumeral,
        UpperRomanNumeral
    },
    data: function () {
        return {
            formData: {
                availableTypes: [],
                availableItems: [],
            }
        }
    },
    computed: {
        availableEntities: function () {
            let availableEntities = [];
            for (const entityType in this.entities) {
                const find = this.formData.availableItems.find(availableItem => availableItem == entityType);
                availableEntities.push({
                    value: entityType,
                    disabled: find != undefined ? false : true,
                    label: this.entities[entityType]
                });
            }

            if (availableEntities.length == 0) {
                this.$set(this.item.settings, "entityType", 0);
            }

            if (availableEntities.length == 1) {
                this.$set(this.item.settings, "entityType", availableEntities[0].value);
            }

            return availableEntities;
        }
    },
    created: async function () {
        if (this.item.settings && !this.item.settings.entityType) {
            this.$set(this.item.settings, "entityType", 0);
        }
        if (!this.item.settings.listType) {
            this.$set(this.item.settings, "listType", 0);
        }
        if (!this.item.settings.linkedView) {
            this.$set(this.item.settings, "linkedView", 0);
        }

        await this.getAdditionalData();
        if (this.item.settings.title === undefined) {
            this.$set(this.item.settings, "title", this.formData.messages['default-title']);
        }
    },
    methods: {
        getAdditionalData: async function () {
            let response = await this.ws.get("views", `form/data/${this.item.type}/${this.item.id}`);
            if (response.error) {
                this.notif.error(response.messages);
            } else {
                this.$set(this, "formData", response);
            }

            return true;
        }
    }
}
</script>