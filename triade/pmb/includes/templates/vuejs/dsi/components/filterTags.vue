<template>
    <select multiple id="dsi-select-tag" v-model="value" @change="sendFilter()">
        <option value="" disabled>{{ messages.get('dsi', 'tag_filter_default_value') }}</option>
        <option v-for="(tag, i) in tags" :key="i" :value="tag.id">{{ tag.name }}</option>
    </select>
</template>

<script>

export default {
    props : ["diffusions"],

    data : function() {
        return {
            tags : [],
            value : []
        }
    },
    created : async function() {
        this.tags = await this.ws.get("tags", "tags");
    },
    methods : {
        sendFilter : function() {
            let filtered = this.diffusions.filter((element) => {
                let elementsTags = element.tags.map(t => t.id);
                return this.value.includes(...elementsTags);
            })
            this.$emit("filter", filtered);
        },
        reset : function() {
            this.value = [];
        }
    }
}
</script>