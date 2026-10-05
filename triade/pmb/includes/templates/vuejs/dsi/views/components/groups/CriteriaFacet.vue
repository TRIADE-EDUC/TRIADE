<template>
    <span>
        <select class="dsi-select" :id="`criteria_${index}_${uid}`" :name="`criteria_${index}_${uid}`" v-model="criteria" @change="clearCriteria">
            <option v-for="(field, index) in list" :value="field.value" :key="index">
                {{ field.label }}
            </option>
        </select>
        <criteria-facet
            v-if="currentCirteria && currentCirteria.subFields.length > 0"
            :group="group"
            :index="subIndex"
            :list="currentCirteria.subFields">
        </criteria-facet>
    </span>
</template>

<script>
let uidComponent = 0;
export default {
    name: "CriteriaFacet",
    props: ["group", "index", "list"],
    data: function () {
        return {
            uid: 0
        }
    },
    computed: {
        criteria: {
            get: function () {
                return this.group.settings.criteria[this.index];
            },
            set: function (value) {
                this.$set(this.group.settings.criteria, this.index, value);
            }
        },
        currentCirteria: function () {
            for (const index in this.list) {
                if (this.list[index].value == this.group.settings.criteria[this.index]) {
                    return this.list[index];
                }
            }
            return null;
        },
        subIndex: function () {
            return this.index + 1;
        }
    },
    created: function () {
        uidComponent++;
        this.uid = uidComponent;

        if (!this.group.settings.criteria[this.index]) {
            const entries = Object.values(this.list) || [];
            this.$set(this.group.settings.criteria, this.index, entries[0].value || 0);
        }
    },
    methods: {
        clearCriteria: function () {
            if (
                this.currentCirteria &&
                this.currentCirteria.subFields.length <= 0 &&
                this.group.settings.criteria.length > this.index
            ) {
                let criteria = this.group.settings.criteria;
                criteria.splice(this.index + 1, criteria.length);

                this.$set(this.group.settings, "criteria", criteria);
            }
        }
    }
}
</script>