<template>
    <editor
        :id="uniqueId"
        v-model="tempContent" 
        :init="configEditor" 
        :inline="true">
    </editor>
</template>

<script>
    import tinymce from 'tinymce';

    import 'tinymce/icons/default';
    import 'tinymce/themes/silver';
    import 'tinymce/skins/ui/oxide/skin.css';
    import 'tinymce/models/dom';

    import 'tinymce/plugins/link';
    import 'tinymce/plugins/image';

    import editor from "@tinymce/tinymce-vue";

    export default {
        props: ["content", "editable"],
        components: {
            editor
        },
        data: function() {
            return {
                uniqueId: this.helper.generateRandomString(10),
                tempContent: this.content,
                configEditor: {
                    setup: (editor) => {
                        editor.on('init', () => {
                            this.$emit("init");
                            this.showEditor();
                        });
                        editor.on('blur', () => {
                            this.$emit("blur");
                        });
                    },
                    height: "100%",
                    width: "500",
                    entity_encoding : 'raw',
                    convert_urls : false,
                    language: 'fr_FR',
                    language_url : './javascript/tinymce/fr_FR.js',
                    menubar: false,
                    statusbar: true,
                    resize: false,
                    plugins: ["image", "link"],
                    toolbar: 'blocks | bold italic backcolor forecolor | alignleft aligncenter ' +
                             'alignright alignjustify | bullist numlist outdent indent | removeformat | link image',
                    formats: {
                        p: { block: 'p', styles: { 'margin': '0' }, exact: true },
                    },
                    skin: false,
                    content_css: false,
                    promotion: false,
                }
            }
        },
        created: function() {
            this.showEditor();
        },
        watch: {
            editable: function() {
                this.showEditor();
            },
            tempContent: function() {
                this.$emit("changeContent", this.tempContent);
            }
        },
        methods: {
            showEditor: function() {
                const editor = this.getEditor();

                if(!editor || !editor.bodyElement) {
                    return;
                }

                editor.bodyElement.contentEditable = this.editable;
            },
            getEditor: function() {
                return tinymce.get(this.uniqueId) ?? null;
            }
        }
    }
</script>