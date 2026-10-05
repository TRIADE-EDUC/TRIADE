<template>
    <div v-if="dataLoaded">
        <label for="criteria" class="dsi-filters-labels">
            {{ formData.messages.criteria }}
        </label>
        <criteria-facet :group="group" :index="0" :list="formData.fieldList"></criteria-facet>
        <div id="bannette_facettes_options" class="row">
            <div class="row">
                <label :for="`order_asc_${uid}`">{{ formData.messages.order }}</label>
            </div>
            <div class="row" v-for="(order, value) in formData.orderList" :key="value">
                <input type="radio" :name="`order_${uid}`" :id="`order_${value}_${uid}`" :value="value" v-model.trim="group.settings.order">
                <label :for="`order_${value}_${uid}`">{{ order }}</label>
            </div>
            <div class="row">
                <label for="sort_alpha">{{ formData.messages.sort }}</label>
            </div>
            <div class="row" v-for="(sort, value) in formData.sortList" :key="value">
                <input type="radio" :name="`sort_${uid}`" :id="`sort_${value}_${uid}`" :value="value" v-model.number="group.settings.sort">
                <label :for="`sort_${value}_${uid}`">{{ sort }}</label>
            </div>
        </div>
    </div>
</template>

<script>
import CriteriaFacet from "./CriteriaFacet.vue";

let uidComponent = 0;
export default {
    name: "RecordFacets",
    props: ["group"],
    components: {
        CriteriaFacet
    },
    data: function () {
        return {
            uid: 0,
            dataLoaded: false,
            formData: {
                availableTypes: [],
                availableItems: [],
                messages: {},
                orderList: {},
                sortList: {},
            }
        }
    },
    computed: {
        currentCirteria: function () {
            for (const index in this.formData.fieldList) {
                if (this.formData.fieldList[index].value == this.group.settings.criteria) {
                    return this.formData.fieldList[index];
                }
            }
            return null;
        }
    },
    created : function () {
        uidComponent++;
        this.uid = uidComponent;

        this.getAdditionalData();

        // On ajoute les settings sur le groupe
        this.update({
            settings : {
                criteria: [],
                order: "asc",
                sort: 1,
            },
            ...this.group
        });
    },
    methods: {
        getAdditionalData: async function() {
            let response = await this.ws.get("group", `form/data/${this.group.id}`);
            if (response.error) {
                this.notif.error(response.messages);
            } else {
                this.dataLoaded = true;
                this.$set(this, "formData", response);
            }
        },
        update: function (group) {
            this.$emit("update", group);
        }
    }
}
</script>