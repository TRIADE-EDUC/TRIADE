<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_searchhub_view_django.class.php,v 1.1.2.8.2.1 2025/02/17 15:30:21 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_searchhub_view_django extends cms_module_common_view_django
{
    /**
     * Template par defaut
     *
     * @var string
     */
    public $default_template = <<<HTML
<div class="search-hub">

    {% if 1 < profile.searches | length %}
    <div class="search-hub-tab" role="tablist" aria-labelledby="search-hub-title">
        {% for index, search in profile.searches %}
            <button
                id="search-hub-tab-{{ index }}"
                class="search-hub-tab-button"
                type="button"
                role="tab"
                aria-selected="true"
                aria-controls="search-hub-tabpanel-{{ index }}">
                <span class="search-hub-tab-label">{{ search.name }}</span>
            </button>
        {% endfor %}
    </div>
    {% endif %}

    {% for index, search in profile.searches %}
    <div
        id="search-hub-tabpanel-{{ index }}"
        class="search-hub-tabpanel"

        {% if 1 < profile.searches | length %}
            role="tabpanel"
            aria-labelledby="search-hub-tab-{{ index }}"
            {% if index > 0 %} style="display:none;" {% endif %}
        {% endif %}
    >

        <form action="{{ search.settings.form.action }}" target="{{ search.settings.form.target }}" method="{{ search.settings.form.method }}">

            {% if search.type != 'external' %}
            <input type="hidden" name="search_hub_tab_index" value="{{ index }}">
            {% endif %}

            {%  if search.description %}
            <p class="search-description">{{ search.description }}</p>
            {%  endif %}

            <div class="inputs">
                <input class="text_query" name="{{ search.settings.form.input_name }}" type="text" placeholder="{{ search.settings.placeholder }}" value="{{ search.settings.form.input_value }}">
                <input class="bouton button_search_submit" type="submit" value="{{ msg.10 }}">
            </div>

            {% if search.type == 'simple' or search.type == 'cms_editorial' %}
                <input type="hidden" name="look_ALL" value="1">
            {% endif %}

            {% if search.type == 'universe' %}
                <div class="segments">
                {% for index, segment in search.settings.segments %}
                    <div class="segment">
                        {% if 1 == search.settings.segments | length %}
                            <input type="hidden" name="id" value="{{ segment.id }}">
                        {% else %}
                            <input type="radio" name="id" value="{{ segment.id }}" id="segment-{{ index }}" {% if index == 0 %}checked{% endif %}>
                            <label for="segment-{{ index }}">{{ segment.label }}</label>
                        {% endif %}
                    </div>
                    {% endfor %}
                </div>
            {% endif %}

            {% if search.settings.otherLinks %}
                <div class="other_links">
                {% for index, otherLink in search.settings.otherLinks %}
                    <div class="other_link">
                        <a href="{{ otherLink.link }}" target="{% if otherLink.target_blank %}_blank{% endif %}" title="{{ otherLink.title }}">{{ otherLink.label }}</a>
                    </div>
                    {% endfor %}
                </div>
            {% endif %}
        </form>
    </div>
    {% endfor %}

    {% if 1 < profile.searches | length %}
        <script>
            window.addEventListener('load', () => {
                const tab = new TabsManual("{{ id }}");
                const searchHubTabIndex = parseInt("{{ global.search_hub_tab_index }}");
                if (searchHubTabIndex && tab.tabs[searchHubTabIndex]) {
                    tab.setSelectedTab(tab.tabs[searchHubTabIndex])
                }
            });
        </script>
    {% endif %}
</div>
HTML;

    /**
     * Retourne le formulaire de parametrage de la vue
     *
     * @return string
     */
    public function get_form()
    {
        return parent::get_form();
    }

    /**
     * Enregistre le formulaire de parametrage de la vue
     *
     * @return boolean
     */
    public function save_form()
    {
        return parent::save_form();
    }

    /**
     * Calcule le rendu de la vue
     *
     * @param array|false $data
     * @return string
     */
    public function render($data)
    {
        if (false === $data) {
            return '';
        }

        foreach ($data['profile']['searches'] as $key => $search) {
            switch ($search['type']) {
                case 'external':
                    $data['profile']['searches'][$key]['settings']['form']['target'] = '_blank';
                    break;

                case 'simple':
                    $data['profile']['searches'][$key]['settings']['form']['method'] = 'POST';
                    $data['profile']['searches'][$key]['settings']['form']['target'] = '_self';
                    $data['profile']['searches'][$key]['settings']['form']['input_name'] = 'user_query';
                    $data['profile']['searches'][$key]['settings']['form']['action'] = './index.php?lvl=more_results&autolevel1=1';
                    break;

                case 'cms_editorial':
                    $data['profile']['searches'][$key]['settings']['form']['method'] = 'POST';
                    $data['profile']['searches'][$key]['settings']['form']['target'] = '_self';
                    $data['profile']['searches'][$key]['settings']['form']['input_name'] = 'user_query';
                    $data['profile']['searches'][$key]['settings']['form']['action'] = './index.php?lvl=cmspage&pageid=' . $search['settings']['page'];
                    break;

                case 'universe':
                    $data['profile']['searches'][$key]['settings']['form']['action'] = './index.php?lvl=search_segment&action=segment_results&id=' . $search['settings']['universe'];
                    $data['profile']['searches'][$key]['settings']['form']['input_name'] = 'user_query';
                    $data['profile']['searches'][$key]['settings']['form']['method'] = 'POST';
                    $data['profile']['searches'][$key]['settings']['form']['target'] = '_self';
                    $data['profile']['searches'][$key]['settings']['segments'] = $this->loadSegmentsLabels($search['settings']['segments']);
                    break;

                default:
                    throw new Exception('Unknown search type ' . $search['type']);
            }

            global $search_hub_tab_index;
            if ($key == $search_hub_tab_index) {
                $global_name =  $data['profile']['searches'][$key]['settings']['form']['input_name'];
                global ${$global_name};

                $data['profile']['searches'][$key]['settings']['form']['input_value'] = ${$global_name} ?? '';
            } else {
                $data['profile']['searches'][$key]['settings']['form']['input_value'] = '';
            }
        }

        return parent::render($data);
    }

    /**
     * Retourne la structure des donnees utilisable dans le template
     *
     * @param integer[] $segments
     * @return array
     */
    public function loadSegmentsLabels($segments)
    {
        $segmentsWithLabel = [];
        foreach ($segments as $segment_id) {
            $query = "SELECT search_segment_label FROM search_segments WHERE id_search_segment = '" . intval($segment_id) . "'";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $segment = pmb_mysql_fetch_assoc($result);
                $segmentsWithLabel[] = [
                    'id' => $segment_id,
                    'label' => $segment['search_segment_label']
                ];
            } else {
                $segmentsWithLabel[] = [ 'id' => $segment_id, 'label' => 'Unknown segment ' . $segment_id ];
            }
        }

        return $segmentsWithLabel;
    }

    /**
     * Permet d'ajouter des meta dans la page en OPAC
     *
     * @param array $data
     * @return array
     */
    public function get_headers($data = [])
    {
        global $opac_url_base;
        return [
            "add" => [
                '<script src="'. $opac_url_base .'includes/javascript/rgaa/Tabs.js"></script>',
            ],
        ];
    }

    /**
     * Retourne la structure des donnees utilisable dans le template
     *
     * @return array
     */
    public function get_format_data_structure()
    {
        $format_datas = [
            [
                'var' => 'profile',
                'desc' => $this->msg['cms_module_searchhub_view_profile_desc'],
                'children' => [
                    [
                        'var' => 'profile.id',
                        'desc' => $this->msg['cms_module_searchhub_view_profile_id_desc'],
                    ],
                    [
                        'var' => 'profile.name',
                        'desc' => $this->msg['cms_module_searchhub_view_profile_name_desc'],
                    ],
                    [
                        'var' => 'profile.searches',
                        'desc' => $this->msg['cms_module_searchhub_view_profile_searches_desc'],
                        'children' => [
                            [
                                'var' => 'profile.searches.[i].name',
                                'desc' => $this->msg['cms_module_searchhub_view_profile_searches_name_desc'],
                            ],
                            [
                                'var' => 'profile.searches.[i].description',
                                'desc' => $this->msg['cms_module_searchhub_view_profile_searches_description_desc'],
                            ],
                            [
                                'var' => 'profile.searches.[i].type',
                                'desc' => $this->msg['cms_module_searchhub_view_profile_searches_type_desc'],
                            ],
                            [
                                'var' => 'profile.searches.[i].settings.<key>',
                                'desc' => $this->msg['cms_module_searchhub_view_profile_searches_settings_desc'],
                            ],
                            [
                                'var' => 'profile.searches.[i].translation.<key>',
                                'desc' => $this->msg['cms_module_searchhub_view_profile_searches_translations_desc'],
                            ],
                        ]
                    ]
                ]
            ]
        ];

        return array_merge_recursive(
            $format_datas,
            parent::get_format_data_structure()
        );
    }
}
