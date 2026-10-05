<template>
    <div class="filter">
        <div class="filter-group">
            <label :for="filterId" class="etiquette">{{ label }}</label>
            <div class="filter-group-inputs">
                <select :id="filterId" :name="filterId" v-model="selected" required @change="change" ref="select" class="dsi-select">
                    <option value="">{{ messages.get('dsi', 'filter_empty_option') }}</option>
                    <option v-for="(option, index) in optionsFiltred" :key="index" :value="option.value">{{ option.label
                    }}</option>
                </select>
                <button v-if="multiple" type="button" class="bouton" @click="add" :disabled="!selected">
                    {{ messages.get('common', 'more') }}
                </button>
            </div>
        </div>
        <div class="filter-list dsi-cards" v-if="multiple && selectedList.length">
            <div class="dsi-card" v-for="(optionValue, index) in selectedList" :key="index">
                <p>
                    {{ getOptionLabel(optionValue) }}
                    <button type="button" class="bouton" @click="remove(index)">
                        {{ messages.get('common', 'remove_short') }}
                    </button>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
let uid = 0;
export default {
    props: {
        label: {
            type: String,
            required: true
        },
        options: {
            type: Array,
            required: true
        },
        multiple: {
            type: Boolean,
            required: false,
            default: () => { return false; }
        }
    },
    data: function () {
        return {
            filterId: 0,
            selected: "",
            selectedList: []
        };
    },
    created: function () {
        this.filterId = `select_filter_${uid}`;
        uid++;
    },
    mounted: function () {
        if (this.$refs.select && this.$refs.select.form) {
            this.$refs.select.form.addEventListener('reset', () => this.reset())
        }
    },
    computed: {
        hasValues: function () {
            return this.multiple ? (this.selectedList.length > 0) : (this.selected != "")
        },
        optionsFiltred: function () {
            return this.options.filter(option => !this.selectedList.includes(option.value))
        }
    },
    methods: {
        reset: function () {
            if (this.hasValues) {
                this.selected = "";
                this.selectedList = [];
                this.update();
            }
        },
        add: function () {
            this.selectedList.push(this.selected);
            this.removeSelected();
        },
        removeSelected: function () {
            this.selected = "";
            this.update();
        },
        remove: function (index) {
            this.selectedList.splice(index, 1);
            this.update();
        },
        change: function () {
            if (!this.multiple) {
                this.update();
            }
        },
        update: function () {
            if (this.multiple) {
                this.$emit('update', this.selectedList);
            } else {
                this.$emit('update', this.selected);
            }
        },
        getOptionLabel: function (optionValue) {
            const option = this.options.find(option => option.value == optionValue);
            return option ? option.label : '';
        }
    }
}
</script>