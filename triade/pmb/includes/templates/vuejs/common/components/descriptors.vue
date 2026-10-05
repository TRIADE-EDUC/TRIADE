<template>
    <div>
        <component is="script" src="./javascript/ajax.js"></component>
        <div class="row dsi-descriptor" v-for="(descriptor, index) in descriptors" :key="index">
            <input :id="`categories${index}`"
                v-model="descriptor.displayLabel"
                class="saisie-30emr" type="text"
                completion="categories_mul"
                :callback="callbackName"
                :autfield="`categoriesId${index}`"
                autocomplete="off" />

            <input :id="`categoriesId${index}`" type="hidden" @change="changeMultipleField(index, $event)" />
            
            <button type="button" class="bouton" @click="deleteMultipleField(index)" :title="messages.get('common', 'remove')">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>

            <button v-if="index == descriptors.length - 1" @click="addMultipleField(index, $event)"
                type="button" class="bouton" :title="messages.get('common', 'more_label')">

                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</template>

<script>
let uid = 0;
export default {
    props: {
        descriptors: {
            type: Array,
            default: () => {
                return [
                    {
                        id: 0,
                        displayLabel: ''
                    }
                ];
            }
        }
    },
    created: function () {
        uid++;

        window[this.callbackName] = this.emitUpdate.bind(this);
        window.addEventListener("load", function (event) {
            if (typeof ajax_parse_elements == "function") {
                ajax_parse_elements();
            } else {
                throw new Error("ajax_parse_elements is not a function");
            }
        });
    },
    mounted: function() {
        this.checkData();
    },
    beforeUpdate: function() {
        this.checkData();
    },
    computed: {
        callbackName: function () {
            return `descriptorsUpdated${uid}`;
        }
    },
    methods: {
        checkData: function () {
            if (this.descriptors.length === 0) {
                this.descriptors.push({
                    id: 0,
                    displayLabel: ''
                });
            }
        },
        changeMultipleField: function (index, event) {
            let libelle = document.getElementById(`categories${index}`).value;
            let id = parseInt(event.target.value, 10);

            if (typeof this.descriptors[index] !== 'undefined' || this.descriptors[index] == 0) {
                this.descriptors[index].id = id;
                this.descriptors[index].displayLabel = libelle;
            } else {
                this.descriptors.push({ id: id, displayLabel: libelle });
            }
        },
        deleteMultipleField: function (index) {
            if (this.descriptors.length > 1) {
                this.descriptors.splice(index, 1);
            } else {
                this.descriptors[index].id = 0;
                this.descriptors[index].displayLabel = '';
            }
            document.activeElement.blur();
            this.emitUpdate();
        },
        addMultipleField: function (index, event) {
            index = parseInt(index, 10) + 1;
            this.descriptors.push({
                id: 0,
                displayLabel: ''
            });

            this.$nextTick(() => {
                let elt = document.getElementById(`categories${index}`);
                ajax_pack_element(elt, event);
            });
        },
        emitUpdate: function () {
            this.$emit("update", this.descriptors);
        }
    }
}
</script>