<template>
    <span v-if="dataLoaded">
        <label for="criteria" class="dsi-filters-labels">
            {{ formData.messages.criteria }}
        </label>
        <select class="dsi-select" id="criteria" name="criteria" v-model="group.settings.criteria">
            <option v-for="(field, index) in formData.customFieldList" :value="field.value" :key="index">
                {{ field.label }}
            </option>
        </select>
    </span>
</template>

<script>
export default {
    name: "RecordCustomFields",
    props: ["group"],
    data: function () {
        return {
            dataLoaded: false,
            formData: {
                messages: {},
                availableTypes: [],
                availableItems: [],
                customFieldList: {},
            }
        }
    },
    created : function () {
        this.getAdditionalData();

        // On ajoute les settings sur le groupe
        this.update({
            settings : {
                criteria: 0,
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