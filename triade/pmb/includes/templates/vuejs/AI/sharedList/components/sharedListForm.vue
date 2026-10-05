<template>
    <div id="sharedListForm" class="sharedListForm">
        <form class="form-admin" action="" method="POST">
            <div class='form-contenu'>
                <!-- Repertoire d'upload -->
                <div class='row'>
                    <div class="colonne4">
                        <div class="row">
                            <label for="upload_folder">{{ messages.get("ai_shared_list", "admin_ai_shared_list_upload_folder") }}* :</label>
                        </div>
                    </div>
                    <div class="colonne_suite">
                        <select id="upload_folder" name="upload_folder" v-model="data.upload_folder" required>
                            <option value="0" selected>{{ messages.get("ai_shared_list", "upload_repertoire") }}</option>
                            <option v-for="(folder, index) in uploadfolder" :key="index" :value="index">{{ folder }}</option>">
                        </select>
                    </div>
                </div>
                <!-- Serveur Python URL -->
                <div class='row'>
                    <div class="colonne4">
                        <div class="row">
                            <label for="ws_server_python">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_python_server") }}* :</label>
                        </div>
                        <div class="row">
                            <span>{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_python_server_comment") }}</span>
                        </div>
                    </div>
                    <div class="colonne_suite">
                        <input class="saisie-80em" id="ws_server_python" type="text" name="ws_server_python" v-model.trim="data.url_server_python" required />
                    </div>
                </div>
                <!-- Serveur Python Token -->
                <div class='row'>
                    <div class="colonne4">
                        <label for="token">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_python_token") }}* :</label>
                    </div>
                    <div class="colonne_suite">
                        <input class="saisie-80em" id="token" type="text" name="token" v-model.trim="data.token" required />
                    </div>
                    <div class="colonne_suite">
                        <input @click="checkToken" class="bouton" type="button"
                            :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_check_token')" />
                    </div>
                </div>

                <!-- Prompt pour les catégories de lecteur -->
                <div class="row">
                    <div class="colonne4">
                        <div class="row">
                            <label for="empr_categ">{{ messages.get("ai_shared_list", "empr_categ_prompt") }} :</label>
                        </div>
                    </div>
                </div>

                <!-- Categ Default -->
                <div class="row">
                    <div class="colonne4">
                        <h3>
                            <img class="img_plus" id="categ_0" :src="visibleCategories[0] ? 'images/minus.gif' : 'images/plus.gif'" @click="toggle(0)">
                            {{ messages.get("ai_shared_list", "empr_categ_prompt_default") }}
                        </h3>
                    </div>
                    <div v-show="visibleCategories[0]">
                        <div class='row'>
                            <div class="colonne4">
                                <label for="prompt_system_0">
                                {{ messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_system_python_server") }} :
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="colonne4" id="django_tree_system_0"></div>
                            <textarea id="prompt_system_0" name="prompt_system_0" v-model="data.prompt[0].prompt_system" required></textarea>
                        </div>
                        <div class='row'>
                            <div class="colonne4">
                                <label for="prompt_user_0">
                                {{ messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_user_python_server") }} :
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="colonne4" id="django_tree_user_0"></div>
                            <textarea id="prompt_user_0" name="prompt_user_0" v-model="data.prompt[0].prompt_user" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Categ on boucle dessus what's else -->
                <div class="row" v-for="(categ, index) in emprcategory" :key="index">
                    <div class="colonne4">
                        <h3>
                            <img class="img_plus" :id="'categ_' + index" :src="visibleCategories[index] ? 'images/minus.gif' : 'images/plus.gif'" @click="toggle(index)">
                            {{ categ }}
                        </h3>
                    </div>
                    <div v-show="visibleCategories[index]">
                        <div class='row'>
                            <div class="colonne4">
                                <label :for="'prompt_system_' + index">
                                {{ messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_system_python_server") }} :
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="colonne4" :id="'django_tree_system_' + index"></div>
                            <textarea :id="'prompt_system_' + index" :name="'prompt_system_' + index" v-model="data.prompt[index].prompt_system"></textarea>
                        </div>
                        <div class='row'>
                            <div class="colonne4">
                                <label :for="'prompt_user_' + index">
                                {{ messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_user_python_server") }} :
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="colonne4" :id="'django_tree_user_' + index"></div>
                            <textarea :id="'prompt_user_' + index" :name="'prompt_user_' + index" v-model="data.prompt[index].prompt_user"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Score -->
                <div class='row'>
                    <div class="colonne4">
                        <label for="min_score">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_slider") }} * :</label>
                    </div>
                    <div class="colonne_suite">
                        <input type="range" id="min_score" name="min_score" min="1" max="100" v-model.number="data.min_score" class="ai_slider" required>
                    </div>
                    <div class="colonne_suite">
                        <span class="right">{{ data.min_score }} %</span>
                    </div>
                </div>

                <!-- Indexation -->
                <div class='row'>
                    <div class="colonne4">
                        <label>{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_choice_indexation") }} * :</label>
                    </div>
                    <div class="colonne_suite">
                        <label for="indexation_choice_docnum">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_choice_indexation_label_docnum") }}</label>
                        <input type="checkbox" id="indexation_choice_docnum" name="indexation_choice_docnum" v-model="data.indexation_choice.docnum" value="1" />
                        <label for="indexation_choice_summary">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_choice_indexation_label_notice") }}</label>
                        <input type="checkbox" id="indexation_choice_summary" name="indexation_choice_summary" v-model="data.indexation_choice.summary" value="1" />
                    </div>
                </div>

                <!-- Boutons -->
                <div class='row'>
                    <br />
                    <div class="left">
                        <input @click="cancel" class="bouton" type="button"
                            :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_cancel')" />
                        <input @click="submit" class="bouton" type="button"
                            :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_save')" />
                        </div>
                        <div class="right" v-if="data.id">
                            <input @click="emptyContainer" class="bouton" type="button"
                                :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_empty_container')" />
                            <input @click="deleteSemanticSearch" class="bouton" type="button"
                                :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_delete')" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: [ "sharedlistsdata", "emprcategory", "uploadfolder"],
    data: function () {
        return {
            visibleCategories: [],
            data: {
                min_score: 50,
                url_server_python: "",
                token: "",
                upload_folder: 0,
                prompt: {
                    0: {
                        prompt_system: `À partir de la liste de notice, répond en {{ user.language }} à la question de l'utilisateur.
N'utilise que les informations des notices pour répondre en les citant seulement à la fin de ta réponse (Sous la forme : #n, n étant le numéro de la notice. Exemple: #42).
S'il n'y a pas l'information explicitement dans les notices, n'essaie pas d'inventer une réponse, dis simplement que tu ne peux pas répondre.

Liste des notices :
{% for document in documents %}
Notice n°{{ forloop.counter }}
{{ document.content }}
{% endfor %}`,
                        prompt_user:`{{ user.query }}`,
                    },
                },
                indexation_choice: {
                    docnum: false,
                    summary: false,
                },
            },
        };
    },
    created() {
        if(Object.keys(this.sharedlistsdata).length !== 0) {
            this.data = {...this.data, ...this.sharedlistsdata};
        } else {
            this.initializePrompts();
        }
    },
    mounted() {
        this.$nextTick(function () {
            // boucle sur les prompts
            pmbDojo.aceManager.initEditor("prompt_system_0");
            pmbDojo.aceManager.initEditor("prompt_user_0");

            const systemEditor = pmbDojo.aceManager.getEditor("prompt_system_0");
            if (systemEditor) {
                systemEditor.session.on('change', (_, systemEditor) => {
                    this.$set(this.data.prompt[0], "prompt_system", systemEditor.getValue());
                });
            }

            const userEditor = pmbDojo.aceManager.getEditor("prompt_user_0");
            if (userEditor) {
                userEditor.session.on('change', (_, userEditor) => {
                    this.$set(this.data.prompt[0], "prompt_user", userEditor.getValue());
                });
            }

            window.dispatchEvent(new CustomEvent("startTree", {
                detail: {
                    index: 0,
                },
            }));

            for (const key in this.emprcategory) {
                pmbDojo.aceManager.initEditor("prompt_system_" + key);
                pmbDojo.aceManager.initEditor("prompt_user_" + key);

                const systemEditor = pmbDojo.aceManager.getEditor("prompt_system_" + key);
                if (systemEditor) {
                    systemEditor.session.on('change', (_, systemEditor) => {
                        this.$set(this.data.prompt[key], "prompt_system", systemEditor.getValue());
                    });
                }

                const userEditor = pmbDojo.aceManager.getEditor("prompt_user_" + key);
                if (userEditor) {
                    userEditor.session.on('change', (_, userEditor) => {
                        this.$set(this.data.prompt[key], "prompt_user", userEditor.getValue());
                    });
                }

                window.dispatchEvent(new CustomEvent("startTree", {
                    detail: {
                        index: key,
                    },
                }));
            }

            this.toggle(0)
        });
    },
    methods: {
        initializePrompts() {
            const keys = Object.keys(this.emprcategory);
            keys.forEach((key, index) => {
                if (!this.data.prompt[key]) {
                    this.$set(this.data.prompt, key, {
                        prompt_system: '',
                        prompt_user: ''
                    });
                }
                // Initialisez également les valeurs par défaut de visibleCategories
                if (this.visibleCategories[index] === undefined) {
                    this.$set(this.visibleCategories, index, false);
                }
            });
        },
        toggle(index) {
            this.$set(this.visibleCategories, index, !this.visibleCategories[index]);
        },
        cancel: function () {
            document.location = './admin.php?categ=ai&sub=shared_lists';
        },

        emptyContainer: async function () {
            if (!this.checkForm("emptyContainer") || !confirm(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_empty_store_confirm"))) {
                return false;
            }

            const response = await this.ws.post('container', 'clean', {
                id: this.data.id,
            });
            if (response.success) {
                alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_empty_store_success"));
            }
        },

        checkToken: async function () {
            if (!this.checkForm("url_server_python")) {
                return false;
            }

            if (!this.checkForm("checkToken")) {
                return false;
            }

            const response = await this.ws.post('check_token', '', {
                token: this.data.token,
                url_server_python: this.data.url_server_python
            });
            if (response.is_valid) {
                alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_check_token_success"));
            } else {
                alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_check_token_failed"));
            }
        },

        submit: async function () {
            if (!this.checkForm()) {
                return false;
            }

            let formData = new FormData();
            formData.append('data', JSON.stringify(this.data));

            let response = await fetch('./admin.php?categ=ai&sub=shared_lists&action=save', {
                method: 'POST',
                body: formData,
            });

            if (response) {
                document.location = './admin.php?categ=ai&sub=shared_lists';
            } else {
                console.log("Error");
            }
        },

        checkForm: function (type = "form") {
            switch (true) {
                case !this.data.id && type == "emptyContainer":
                    return false;
                case !this.data.url_server_python:
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_python_server_error"));
                    return false;
                case !this.data.token:
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_token_error"));
                    return false;
                case !this.data.prompt[0].prompt_system && type == "form":
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_system_python_server_error"));
                    return false;
                case !this.data.prompt[0].prompt_user && type == "form":
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_user_python_server_error"));
                    return false;
                case !this.data.upload_folder && type == "form":
                    alert(this.messages.get("ai_shared_list", "admin_ai_shared_list_upload_folder_error"));
                    return false;
            }
            return true;
        },
    },
};
</script>
