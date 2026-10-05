<template>
    <div>
        <div class="dsi-form-group">
            <label>{{ messages.get('dsi', 'form_item_sort') }}</label>
            <div class="dsi-form-group-content">
                <select v-model="sort.namespace">
                    <option value="" disabled>{{ messages.get('dsi', 'form_item_sort_default_value') }}</option>
                    <option v-for="(sort, i) in sortTypes" :key='i' :value="sort.namespace">{{ sort.name }}</option>
                </select>
                <select v-model="sort.data.direction">
                    <option value="" disabled>{{ messages.get('dsi', 'form_item_order_default_value') }}</option>
                    <option value="ASC">{{ messages.get('dsi', 'form_item_sort_asc') }}</option>
                    <option value="DESC">{{ messages.get('dsi', 'form_item_sort_desc') }}</option>
                </select>
            </div>
        </div>
        <div class="dsi-form-group" v-for="(field, i) in currentSort.fields" :key="i">
            <label v-if="currentSort.messages && currentSort.messages[i + 'Label']">{{ currentSort.messages[i + 'Label'] }}</label>
            <div class="dsi-form-group-content">
                <select v-if="field.type == 'select'" :required="field.required" v-model="sort.data[i]">
                    <option v-for="option in field.options" :key="option.value" :value="option.value">{{ option.label }}</option>
                </select>
                <input v-else :type="field.type" :required="field.required" v-model="sort.data[i]" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name : "formSort",
    props : ["sort", "sortTypes"],
    computed : {
        currentSort : function() {
            let currentSort = this.sortTypes.find(s => s.namespace == this.sort.namespace);
            if(currentSort) {
                return currentSort;
            }
            return {
                fields : []
            };
        }
    }
}
</script>