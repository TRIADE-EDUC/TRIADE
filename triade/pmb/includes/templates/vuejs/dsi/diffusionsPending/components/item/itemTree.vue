<template>
    <div>
        <div class="wysiwyg-tree-item" @click.stop="$emit('displayItem', parent)" style="cursor: pointer;">
            <i :class="displayCarret()" aria-hidden="true"></i>
            <b>{{ displayLabel() }}</b>
        </div>
        <ul v-if="childs.length > 0">
            <li v-for="(child, index) in childs" :key="index" style="cursor: pointer;">
                <itemTree :items="items" :parent="child" @displayItem="displayItem" :entities="entities"></itemTree>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "itemTree",
        props: ["items", "parent", "entities"],
        computed: {
            childs: function() {
                return this.items.filter(item => item.numParent == this.parent.id);
            }
        },
        methods: {
            displayItem: function(item) {
                if(item.type != 0) {
                    this.$emit("displayItem", item);
                }
            },
            displayLabel: function() {
                const entityLabel = typeof this.entities[this.parent.type] === "undefined" ? "" : `(${this.entities[this.parent.type]})`;
                return `${this.parent.name} ${entityLabel}`;
            },
            displayCarret: function() {
                if(this.childs.length) {
                    return "fa fa-caret-down";
                }

                return "fa fa-caret-right";
            }
        }
    };
</script>