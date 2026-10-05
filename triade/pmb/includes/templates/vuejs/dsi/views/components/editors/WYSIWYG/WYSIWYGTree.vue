<template>
    <div>
        <div class="wysiwyg-tree-item">
            <b>{{ tree.name ? tree.name : blockLabels[tree.type] }}</b>
        </div>
        <ul v-if="childView && childView.settings">
            <li v-for="(child, index) in childView.settings.layer.blocks" :key="'child_' + index">
                <tree :tree="child" :blockLabels="blockLabels" :view="view"></tree>
            </li>

        </ul>

        <ul v-else>
            <li v-for="(child, index) in tree.blocks" :key="index">
                <tree :tree="child" :blockLabels="blockLabels" :view="view"></tree>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "tree",
        props: ["tree", "blockLabels", "view"],
        computed: {
            childView: function () {
                if (this.tree.content.viewId == 0) {
                    return {}
                }
                let view = this.view.childs.find((v) => v.id == this.tree.content.viewId);
                return typeof view === 'undefined' ? {} : view;
            }
        }
    };
</script>