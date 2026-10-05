<template>
    <div>
        <div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-placement-direction">
                    {{ messages.get('dsi', 'diffusion_name') }}
                </label>
                <div class="dsi-form-group-content">
                    <input type="text" name="name" v-model="block.name" />
                </div>
            </div>
        </div>
        <div>
            <h3>{{ messages.get('dsi', 'view-wysiwyg-placement') }}</h3>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-placement-direction">
                    {{ messages.get('dsi', 'view-wysiwyg-placement-direction') }}
                </label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <select id="wysiwyg-placement-direction"
                            name="wysiwyg-placement-direction"
                            v-model="block.style.flexDirection">
                            <option value="row">{{ messages.get('dsi', 'view-wysiwyg-placement-row') }}</option>
                            <option value="column">{{ messages.get('dsi', 'view-wysiwyg-placement-column') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-placement-width-enabled">
                    <b>{{ messages.get('dsi', 'view_wysiwyg_placement_width_enabled') }}</b>
                </label>
                <div class="dsi-form-group-content">
                    <input 
                        type="checkbox" 
                        id="wysiwyg-placement-width-enabled" 
                        name="wysiwyg-placement-width-enabled"
                        v-model="block.widthEnabled" 
                        @change="resetWidth">
                </div>
            </div>

            <!-- Largeur fixe -->
            <div class="dsi-form-group dsi-form-wysiwyg dsi-form-wysiwyg-width" v-if="block.widthEnabled">

                <label class="etiquette" for="wysiwyg-input-width">
                    {{ messages.get('dsi', 'view_wysiwyg_placement_width') }}
                </label>
                <div class="dsi-form-group-content">
                    <input 
                        type="number" 
                        min="0" 
                        id="wysiwyg-input-width" 
                        name="wysiwyg-input-width" 
                        :value="getWidth()"
                        class="wysiwyg-width-input"
                        @input="changeWidth($event)">
                    <select v-model="widthUnit" @change="changeWidth($event, true)">
                        <option v-for="unit of arrayUnit" :value="unit">
                            {{ unit }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Largeur maximale -->
            <div class="dsi-form-group dsi-form-wysiwyg dsi-form-wysiwyg-width" v-if="block.widthEnabled">
                
                <label class="etiquette" for="wysiwyg-input-max-width">
                    {{ messages.get('dsi', 'view_wysiwyg_placement_max_width') }}
                </label>
                <div class="dsi-form-group-content">
                    <input 
                        type="number" 
                        min="0" 
                        id="wysiwyg-input-max-width" 
                        name="wysiwyg-input-max-width" 
                        :value="getMaxWidth()"
                        class="wysiwyg-width-input"
                        @input="changeMaxWidth($event)">
                    <select v-model="maxWidthUnit" @change="changeMaxWidth($event, true)">
                        <option v-for="unit of arrayUnit" :value="unit">
                            {{ unit }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="wysiwyg-block-conditions" v-if="$root.diffusion">
            <h3>{{ messages.get('dsi', 'view-wysiwyg-condition-display') }}</h3>
            <conditions :settings="block" :context="'wysiwyg'"></conditions>
        </div>
        <div>
            <h3>{{ messages.get('dsi', 'view-wysiwyg-bg') }}</h3>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-bg-color">
                    {{ messages.get('dsi', 'view-wysiwyg-bg-color') }}
                </label>
                <div class="dsi-form-group-content">
                    <input type="color"
                        id="wysiwyg-bg-color"
                        name="wysiwyg-bg-color"
                        ref="wysiwyg_bg_color"
                        v-model="bgColor"
                        @change="transformBgColor($event)">
                    <button class="color-reset"
                        v-if="block.style.backgroundColor"
                        @click="block.style.backgroundColor = ''; bgColor = '#ffffff'; bgOpacity = 1"
                        type="button">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div v-show="view.settings.displayChoice == 1" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-bg-opacity">
                    {{ messages.get('dsi', 'view_wysiwyg_bg_opacity') }}
                </label>
                <div class="dsi-form-group-content">
                    <input type="range"
                        id="wysiwyg-bg-opacity"
                        name="wysiwyg-bg-opacity"
                        ref="wysiwyg_bg_opacity"
                        max="1"
                        step="0.01"
                        v-model="bgOpacity"
                        @input="transformBgColor($event, true)">
                </div>
            </div>    
            <hr>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-bg-image">{{ messages.get('dsi', 'view-wysiwyg-bg-img') }}</label>
                <div class="dsi-form-group-content">
                    <input v-if="!block.style.backgroundImage"
                        type="file" id="wysiwyg-bg-image"
                        name="wysiwyg-bg-image" @change="changeBgImage">
                    <div v-else class="wysiwyg-bg-image-preview">
                        <img width="48" height="48" :src="dataURLToData(block.style.backgroundImage)" alt="">
                        <button class="bg-reset"
                            v-if="block.style.backgroundImage"
                            @click="block.style.backgroundImage = ''"
                            type="button">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-bg-img-repeat">
                    {{ messages.get('dsi', 'view-wysiwyg-bg-img-repeat') }}
                </label>
                <div class="dsi-form-group-content">
                    <select v-model="block.style.backgroundRepeat" id="wysiwyg-bg-img-repeat">
                        <option value="no-repeat">
                            {{ messages.get('dsi', 'view-wysiwyg-bg-img-repeat-norepeat') }}
                        </option>
                        <option value="repeat">{{ messages.get('dsi', 'view-wysiwyg-bg-img-repeat-repeat') }}</option>
                    </select>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-bg-img-size">
                    {{ messages.get('dsi', 'view-wysiwyg-bg-img-size') }}
                </label>
                <div class="dsi-form-group-content">
                    <select v-model="bgSize" id="wysiwyg-bg-img-size">
                        <option value="auto">{{ messages.get('dsi', 'view-wysiwyg-bg-img-size-auto') }}</option>
                        <option value="cover">{{ messages.get('dsi', 'view-wysiwyg-bg-img-size-cover') }}</option>
                        <option value="contain">{{ messages.get('dsi', 'view-wysiwyg-bg-img-size-contain') }}</option>
                        <option value="custom">{{ messages.get('dsi', 'view-wysiwyg-bg-img-size-custom') }}</option>
                    </select>
                    <input type="hidden" v-model="block.style.backgroundSize">
                    <div v-show="bgSize === 'custom'">
                        <input type="number" id="wysiwyg-bg-width" name="wysiwyg-bg-width" @input="changeBgSize">
                        <select v-model="bgSizeUnit" @change="changeBgSize($event, true)">
                            <option v-for="(unit, index) of arrayUnit" :value="unit" :key="index">{{ unit }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="dsi-form-wysiwyg-mg">
            <h3>{{ messages.get('dsi', 'view-wysiwyg-mg') }}</h3>
            <!-- marge automatique avec checkbox-->
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-mg-auto">
                    {{ messages.get('dsi', 'view_wysiwyg_mg_auto') }}
                </label>
                <div class="dsi-form-group-content">
                    <input 
                        type="checkbox" 
                        id="wysiwyg-mg-auto" 
                        name="wysiwyg-mg-auto" 
                        ref="wysiwyg_mg_auto"
                        v-model="marginAuto"
                        @change="changeMarginAuto">
                </div>
            </div>

            <div v-if="!marginAuto" v-for="direction of arrayDirections" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" :for="'wysiwyg-mg-' + direction">
                    {{ messages.get('dsi', 'view-wysiwyg-mg-' + direction) }}
                </label>
                <div class="dsi-form-group-content">
                    <input min="0" type="number" :id="'wysiwyg-mg-' + direction" :name="'wysiwyg-mg-' + direction"
                        class="wysiwyg-mg-input" :value="getMargin(direction)" @input="changeMargin($event, direction)">

                    <select v-model="marginsUnit[direction]" @change="changeMargin($event, direction, true)">
                        <option v-for="unit of arrayUnit" :value="unit">{{ unit }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="dsi-form-wysiwyg-pd">
            <h3>{{ messages.get('dsi', 'view-wysiwyg-pd') }}</h3>
            <div v-for="direction of arrayDirections" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" :for="'wysiwyg-pd-' + direction">
                    {{ messages.get('dsi', 'view-wysiwyg-mg-' + direction) }}
                </label>
                <div class="dsi-form-group-content">
                    <input min="0" type="number" :id="'wysiwyg-pd-' + direction" :name="'wysiwyg-pd-' + direction"
                        class="wysiwyg-pd-input" :value="getPadding(direction)"
                        @input="changePadding($event, direction)">

                    <select v-model="paddingsUnit[direction]" @change="changePadding($event, direction, true)">
                        <option v-for="unit of arrayUnit" :value="unit">{{ unit }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="dsi-form-wysiwyg-border">
            <h3>{{ messages.get('dsi', 'view_wysiwyg_border') }}</h3>
            
            <!-- Border type -->
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-border-type">
                    <b>{{ messages.get('dsi', 'view_wysiwyg_border_type') }}</b>
                </label>
                <div class="dsi-form-group-content">
                    <select 
                        id="wysiwyg-border-type" 
                        name="wysiwyg-border-type" 
                        v-model="borders.type" 
                        @change="changeBorder()">

                        <option v-for="type in borders.types" :value="type" :key="type">
                            {{ messages.get('dsi', 'view_wysiwyg_border_type_' + type) }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Border weight unit -->
            <div v-if="borders.type != 'none'" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-border-weight">
                    {{ messages.get('dsi', 'view_wysiwyg_border_weight') }}
                </label>
                <div class="dsi-form-group-content">
                    <input 
                        min="0" 
                        type="number" 
                        id="wysiwyg-border-weight" 
                        name="wysiwyg-border-weight"
                        class="wysiwyg-border-weight-input" 
                        v-model="borders.weight"
                        @input="changeBorder()">

                    <select v-model="borders.weightUnit" @change="changeBorder()">
                        <option v-for="unit of borders.arrayWeightUnit" :value="unit">
                            {{ unit }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Border color -->
            <div v-if="borders.type != 'none'" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-border-color">
                    {{ messages.get('dsi', 'view_wysiwyg_border_color') }}
                </label>
                <div class="dsi-form-group-content">
                    <input 
                        type="color"
                        id="wysiwyg-border-color"
                        name="wysiwyg-border-color"
                        ref="wysiwyg_border_color"
                        v-model="borders.color"
                        @change="changeBorder()">

                    <button 
                        v-if="borders.color"
                        class="color-reset"
                        @click="resetBorderColor()"
                        type="button">

                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- Arrondi -->
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-border-around">
                    <b>{{ messages.get('dsi', 'view_wysiwyg_border_around') }}</b>
                </label>
                <div class="dsi-form-group-content">
                    <input 
                        type="checkbox"
                        id="wysiwyg-border-around"
                        name="wysiwyg-border-around"
                        ref="wysiwyg_border_around"
                        v-model="borders.around"
                        @change="changeBorder()">
                </div>
            </div>

            <div v-if="borders.around" v-for="direction of borders.radiusDirections" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" :for="'wysiwyg-border-radius-' + direction">
                    {{ messages.get('dsi', 'view_wysiwyg_border_radius_' + direction) }}
                </label>
                <div class="dsi-form-group-content">
                    <input 
                        min="0" 
                        type="number" 
                        :id="'wysiwyg-border-radius-' + direction" 
                        :name="'wysiwyg-border-radius-' + direction"
                        class="wysiwyg-border-radius-input" 
                        v-model="borders.radius[direction]"
                        @input="changeBorder()">

                    <select v-model="borders.radiusUnit[direction]" @change="changeBorder()">
                        <option v-for="unit of arrayUnit" :value="unit">
                            {{ unit }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Ombre -->
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-border-shadow">
                    <b>{{ messages.get('dsi', 'view_wysiwyg_border_shadow') }}</b>
                </label>
                <div class="dsi-form-group-content">
                    <input 
                        type="checkbox"
                        id="wysiwyg-border-shadow"
                        name="wysiwyg-border-shadow"
                        ref="wysiwyg_border_shadow"
                        v-model="borders.shadow"
                        @change="changeBorder()">
                </div>
            </div>

            <div v-if="borders.shadow">

                <!-- Color -->
                <div class="dsi-form-group dsi-form-wysiwyg">
                    <label class="etiquette" for="wysiwyg-border-shadow-color">
                        {{ messages.get('dsi', 'view_wysiwyg_border_shadow_color') }}
                    </label>
                    <div class="dsi-form-group-content">
                        <input type="color"
                            id="wysiwyg-border-shadow-color"
                            name="wysiwyg-border-shadow-color"
                            ref="wysiwyg_border_shadow_color"
                            v-model="borders.boxShadow.color"
                            @change="changeBorder()">

                        <button 
                            v-if="borders.boxShadow.color"
                            class="color-reset"
                            @click="resetBorderShadowColor()"
                            type="button">

                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <!-- Opacity -->
                <div class="dsi-form-group dsi-form-wysiwyg">
                    <label class="etiquette" for="wysiwyg-border-shadow-opacity">
                        {{ messages.get('dsi', 'view_wysiwyg_border_shadow_opacity') }}
                    </label>
                    <div class="dsi-form-group-content">
                        <input 
                            type="range"
                            id="wysiwyg-border-shadow-opacity"
                            name="wysiwyg-border-shadow-opacity"
                            ref="wysiwyg_border_shadow_opacity"
                            max="1"
                            step="0.01"
                            v-model="borders.boxShadow.opacity"
                            @input="changeBorder">
                    </div>
                </div>

                <div v-for="(prop, key) of borders.boxShadowUnit" :key="key" class="dsi-form-group dsi-form-wysiwyg">
                    <label class="etiquette" :for="'wysiwyg-border-shadow-' + key">
                        {{ messages.get('dsi', 'view_wysiwyg_border_shadow_' + key) }}
                    </label>
                    <div class="dsi-form-group-content">
                        <input 
                            min="0" 
                            type="number" 
                            :id="'wysiwyg-border-shadow-' + key" 
                            :name="'wysiwyg-border-shadow-' + key"
                            class="wysiwyg-border-shadow-horizontal-input" 
                            v-model="borders.boxShadow[key]"
                            @input="changeBorder()">

                        <select v-model="borders.boxShadowUnit[key]" @change="changeBorder()">
                            <option v-for="unit of borders.arrayBoxShadowUnit" :value="unit">
                                {{ unit }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import conditions from '@dsi/components/Conditions/conditions.vue';

export default {
    name: "blockForm",
    props: ["block", "view", "item"],
    components: {
        conditions
    },
    data: function () {
        return {
            marginsUnit: { right: "px", left: "px", top: "px", bottom: "px" },
            paddingsUnit: { right: "px", left: "px", top: "px", bottom: "px" },
            arrayUnit: ["px", "rem", "%", "vh", "vw"],
            arrayDirections: ["right", "left", "top", "bottom"],
            bgSize: "auto",
            bgSizeUnit: "px",
            bgSizeList: ["auto", "cover", "contain"],
            // heightUnit: "px",
            widthUnit: "px",
            maxWidthUnit: "px",
            blockLabels: {
                1: this.messages.get('dsi', 'view_wysiwyg_block'),
                2: this.messages.get('dsi', 'view_wysiwyg_input_text'),
                3: this.messages.get('dsi', 'view_wysiwyg_input_image'),
                4: this.messages.get('dsi', 'view_wysiwyg_input_video'),
                5: this.messages.get('dsi', 'view_wysiwyg_input_list'),
                6: this.messages.get('dsi', 'view_wysiwyg_input_text_rich'),
                7: this.messages.get('dsi', 'view_wysiwyg_views'),
                8: this.messages.get('dsi', 'view_wysiwyg_view_wysiwyg')
            },
            condition: "",
            bgColor: "",
            bgOpacity: 1,
            marginAuto: false,
            borders: {
                type: "none",
                weight: 0,
                weightUnit: "px",
                arrayWeightUnit: ["px", "rem", "vh", "vw"],
                color: "",
                around: false,
                radius: { 
                    top_left: 0,
                    top_right: 0,
                    bottom_right: 0,
                    bottom_left: 0 
                },
                radiusUnit: { 
                    top_left: "px",
                    top_right: "px",
                    bottom_right: "px",
                    bottom_left: "px" 
                },
                types: ["none", "solid", "dotted", "dashed"],
                radiusDirections: ["top_left", "top_right", "bottom_right", "bottom_left"],
                shadow: false,
                boxShadow: {
                    horizontal: 0,
                    vertical: 0,
                    blur: 0,
                    spread: 0,
                    color: "",
                    opacity: 1
                },
                boxShadowUnit: {
                    horizontal: "px",
                    vertical: "px",
                    blur: "px",
                    spread: "px",
                },
                arrayBoxShadowUnit: ["px", "rem", "vh", "vw"]
            }
        }
    },
    updated: function() {
        if (typeof domUpdated	=== "function") {
            domUpdated();
        }
    },
    mounted: function() {
        if (typeof domUpdated	=== "function") {
            domUpdated();
        }
    },
    watch: {
        bgSize: function (newVal) {
            if (this.bgSize === "custom") {
                if (!this.block.style.backgroundSize) {
                    this.$set(this.block.style, "backgroundSize", "");
                }

                const formSize = document.getElementById("wysiwyg-bg-width");
                if (formSize && formSize.value != "") {
                    this.$set(this.block.style, "backgroundSize", formSize.value + this.bgSizeUnit);
                }
                return;
            }
            this.$set(this.block.style, "backgroundSize", newVal);
        },
        "view.settings.displayChoice": function(newVal) {
            let color = "";
            
            if(newVal == 1) {
                color = this.convertHexToRGBA(this.block.style.backgroundColor);
                this.bgOpacity = 1;

            } else {
                color = this.convertRGBToHex(this.block.style.backgroundColor);
                this.bgOpacity = this.RGBAToArray(this.block.style.backgroundColor)[3];
            }

            this.$set(this.block.style, "backgroundColor", color);
        }
    },
    created: function () {
        if (!this.block.name) {
            this.$set(this.block, "name", "");
        }

        if (!this.block.style.flexDirection) {
            this.$set(this.block.style, "flexDirection", "column");
        }

        if (!this.block.widthEnabled) {
            this.$set(this.block, "widthEnabled", false);
        }

        if (!this.block.style.backgroundRepeat) {
            this.$set(this.block.style, "backgroundRepeat", "no-repeat");
        }

        if (this.block.style.backgroundImage) {
            if (document.getElementById("wysiwyg-bg-image")) {
                let file = this.dataURLtoFile(this.block.style.backgroundImage);
                let container = new DataTransfer();
                container.items.add(file);
                document.getElementById("wysiwyg-bg-image").files = container.files
            }
        }

        if (this.block.style.backgroundSize) {
            this.bgSize = "custom"
            for (const size of this.bgSizeList) {
                if (this.block.style.backgroundSize == size) {
                    this.bgSize = size;
                    break;
                }
            }

            if (this.bgSize == "custom") {
                for (const unit of this.arrayUnit) {
                    if (this.block.style.backgroundSize.includes(unit)) {
                        this.bgSizeUnit = unit;
                        let node = document.getElementById("wysiwyg-bg-width");
                        if (node) {
                            node.value = this.block.style.backgroundSize.replace(unit, "");
                        }
                        break;
                    }
                }
            }
        }

        if(this.block.style.backgroundColor) {
            if(this.block.style.backgroundColor.includes("rgba")) {
                this.bgColor = this.convertRGBToHex(this.block.style.backgroundColor);
                this.bgOpacity = this.RGBAToArray(this.block.style.backgroundColor)[3];
                
                if(this.view.settings.displayChoice == 0) {
                    this.$set(this.block.style, "backgroundColor", this.bgColor);
                }
            } else {
                this.bgColor = this.block.style.backgroundColor;
            }
        }

        if (this.block.style.width) {
                for (const unit of this.arrayUnit) {
                    if (this.block.style.width.includes(unit)) {
                        this.widthUnit = unit;
                        let node = document.getElementById("wysiwyg-input-width");
                        if (node) {
                            node.value = this.block.style.width.replace(unit, "");
                        }
                        break;
                    }
                }
            }


        for (const direction of this.arrayDirections) {
            if (this.block.style["margin-" + direction]) {
                for (const unit of this.arrayUnit) {
                    if (this.block.style["margin-" + direction].includes(unit)) {
                        this.marginsUnit[direction] = unit;
                        let node = document.getElementById("wysiwyg-mg-" + direction);
                        if (node) {
                            node.value = this.block.style["margin-" + direction].replace(unit, "");
                        }
                        break;
                    }
                }
            }

            if (this.block.style["padding-" + direction]) {
                for (const unit of this.arrayUnit) {
                    if (this.block.style["padding-" + direction].includes(unit)) {
                        this.paddingsUnit[direction] = unit;
                        let node = document.getElementById("wysiwyg-pd-" + direction);
                        if (node) {
                            node.value = this.block.style["padding-" + direction].replace(unit, "");
                        }
                        break;
                    }
                }
            }
        }

        if(this.block.style.border || this.block.style.borderRadius || this.block.style["box-shadow"]) {
            this.setBorder();
        }
    },
    methods: {
        getWidth: function () {
            this.block.style["flex"] = "none";

            if (this.block.style["width"]) {
                return parseInt(this.block.style["width"], 10);
            }
            return 800;
        },
        changeWidth: function (event, reload = false) {
            this.block.style["flex"] = "none";

            if (reload) {
                this.$set(this.block.style, "width", event.target.previousElementSibling.value + this.widthUnit);
                return;
            }

            this.$set(this.block.style, "width", event.target.value + this.widthUnit);
        },
        getMaxWidth: function () {
            if (this.block.style["max-width"]) {
                return parseInt(this.block.style["max-width"], 10);
            }

            return 800;
        },
        changeMaxWidth: function (event, reload = false) {
            if (reload) {
                this.$set(this.block.style, "max-width", event.target.previousElementSibling.value + this.maxWidthUnit);
                return;
            }

            this.$set(this.block.style, "max-width", event.target.value + this.maxWidthUnit);
        },
        getMargin: function (direction) {
            if(this.block.style.margin && this.block.style.margin == "auto") {
                this.$set(this, "marginAuto", true);
                return;
            }

            if (this.block.style["margin-" + direction]) {
                return parseInt(this.block.style["margin-" + direction], 10);
            }
            return 0;
        },
        changeMargin: function (event, direction, reload = false) {
            if (reload) {
                this.$set(this.block.style, "margin-" + direction, event.target.previousElementSibling.value + this.marginsUnit[direction]);
                return;
            }
            this.$set(this.block.style, "margin-" + direction, event.target.value + this.marginsUnit[direction]);
        },
        changeMarginAuto: function (event) {
            const checked = this.$refs["wysiwyg_mg_auto"].checked;

            if(checked) {
                for(const direction of this.arrayDirections) {
                    this.$delete(this.block.style, "margin-" + direction);
                }

                this.$set(this.block.style, "margin", "auto");

                return;
            }

            this.$delete(this.block.style, "margin");

            this.$nextTick(() => {
                for(const direction of this.arrayDirections) {
                    const value = document.getElementById("wysiwyg-mg-" + direction).value;
                    
                    if(value) {
                        this.$set(this.block.style, "margin-" + direction, value + this.marginsUnit[direction]);
                    }
                }
            });
        },
        getPadding: function (direction) {
            if (this.block.style["padding-" + direction]) {
                return parseInt(this.block.style["padding-" + direction], 10);
            }
            return 0;
        },
        changePadding: function (event, direction, reload = false) {
            if (reload) {
                this.$set(this.block.style, "padding-" + direction, event.target.previousElementSibling.value + this.paddingsUnit[direction]);
                return;
            }
            this.$set(this.block.style, "padding-" + direction, event.target.value + this.paddingsUnit[direction]);
        },
        changeBgSize: function (event, reload = false) {
            if (reload) {
                const node = document.getElementById('wysiwyg-bg-width');
                this.$set(this.block.style, "backgroundSize", (node?.value ?? 0) + this.bgSizeUnit);
                return;
            }
            this.$set(this.block.style, "backgroundSize", event.target.value + this.bgSizeUnit);
        },
        changeBgImage(event) {
            let files = event.target.files || event.dataTransfer.files;
            if (!files.length) return;

            const maxKo = this.Const.views.blockBgImgMaxSize; // 200 Ko
            const maxAllowedSize = maxKo * 1024;

            if (files[0].size > maxAllowedSize) {
                event.target.value = ''
                alert(`${this.messages.get('dsi', 'view_form_image_max_size')} (${maxKo}Ko maximum)`);

                return;
            }

            this.createImage(files[0]);
        },
        createImage(file) {
            let image = new Image();
            let reader = new FileReader();

            reader.onload = (e) => {
                image = e.target.result;
                this.$set(this.block.style, "backgroundImage", "url(" + image + ")");
            };
            reader.readAsDataURL(file);
        },
        dataURLToData: function (dataURL) {
            return dataURL.match(/\((.*?)\)/)[1].replace(/('|")/g, '');
        },
        dataURLtoFile: function (dataURL) {
            let url = this.dataURLToData(dataURL);

            let arr = url.split(','),
                mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]),
                n = bstr.length,
                u8arr = new Uint8Array(n);

            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }

            return new File([u8arr], "myfile", { type: mime });
        },
        transformBgColor: function(e, isOpacity = false) {
            const opacity = !isOpacity ? this.$refs.wysiwyg_bg_opacity.value : e.target.value;
            const color = !isOpacity ? e.target.value : this.$refs.wysiwyg_bg_color.value;
            
            let colorHex = color;
            // Si on est en mode HTML5 (defaul) on transforme la couleur en RGBA
            if(this.view.settings.displayChoice == 1) {
                colorHex = this.convertHexToRGBA(color, opacity);
            }

            this.$set(this.block.style, "backgroundColor", colorHex);
        },
        convertHexToRGBA: function(color, opacity = 1) {
            return `rgba(${parseInt(color.slice(-6, -4), 16)}, ${parseInt(color.slice(-4, -2), 16)}, ${parseInt(color.slice(-2), 16)}, ${opacity})`;
        },
        convertRGBToHex: function(rgbString) {
            let rgbArray = this.RGBAToArray(rgbString);

            function componentToHex(c) {
                let hex = c.toString(16);
                return hex.length == 1 ? "0" + hex : hex;
            }

            return "#" + componentToHex(parseInt(rgbArray[0])) + componentToHex(parseInt(rgbArray[1])) + componentToHex(parseInt(rgbArray[2]));
        },
        RGBAToArray: function(rgbaString) {
            return rgbaString.substring(rgbaString.indexOf('(') + 1, rgbaString.length - 1).split(', ');
        },
        resetWidth: function () {
            if(!this.block.widthEnabled) {
                this.$set(this.block.style, "width", "");
                this.$set(this.block.style, "max-width", "");

                this.$set(this.block.style, "flex", "1");
                this.$set(this.block.style, "flex-grow", "1");

                return;
            }

            this.block.style["flex"] = "none";

            this.$set(this.block.style, "width", this.getWidth() + this.widthUnit);
            this.$set(this.block.style, "max-width", this.getMaxWidth() + this.maxWidthUnit);
        },
        changeBorder: function() {
            if(this.borders.type === "none") {
                this.$delete(this.block.style, "border");
            } else {
                this.$set(
                    this.block.style,
                    "border",
                    `${this.borders.weight}${this.borders.weightUnit} ${this.borders.type} ${this.borders.color}`
                );
            }

            if(this.borders.around) {
                this.$set(
                    this.block.style, 
                    "borderRadius",
                    `${this.borders.radius.top_left}${this.borders.radiusUnit.top_left} ${this.borders.radius.top_right}${this.borders.radiusUnit.top_right} ${this.borders.radius.bottom_right}${this.borders.radiusUnit.bottom_right} ${this.borders.radius.bottom_left}${this.borders.radiusUnit.bottom_left}`
                );
            } else {
                this.$delete(this.block.style, "borderRadius");
            }

            if(this.borders.shadow) {
                let boxShadowStyle = this.borders.boxShadow.horizontal + this.borders.boxShadowUnit.horizontal + " ";
                boxShadowStyle += this.borders.boxShadow.vertical + this.borders.boxShadowUnit.vertical + " ";
                boxShadowStyle += this.borders.boxShadow.blur + this.borders.boxShadowUnit.blur + " ";
                boxShadowStyle += this.borders.boxShadow.spread + this.borders.boxShadowUnit.spread + " ";
                boxShadowStyle += this.convertHexToRGBA(
                    this.borders.boxShadow.color ? this.borders.boxShadow.color : "#000000",
                    this.borders.boxShadow.opacity
                );
                
                this.$set(this.block.style, "box-shadow", boxShadowStyle);
            } else {
                this.$delete(this.block.style, "box-shadow");
            }
        },
        setBorder: function() {
            const border = this.block.style["border"];
            if (border) {
                let params = border.split(" ");

                // weight
                if(params[0]) {
                    this.$set(this.borders, "weight", parseInt(params[0], 10));
                    this.$set(this.borders, "weightUnit", params[0].replace(/\d+/g, ''));
                }

                // type
                if(params[1]) {
                    this.$set(this.borders, "type", params[1]);
                }

                // color
                if(params[2]) {
                    this.$set(this.borders, "color", params[2]);
                }
            }

            const borderRadius = this.block.style["borderRadius"];
            if (borderRadius) {
                this.$set(this.borders, "around", true);

                let params = borderRadius.split(" ");

                // top left
                if(params[0]) {
                    this.$set(this.borders.radius, "top_left", parseInt(params[0], 10));
                    this.$set(this.borders.radiusUnit, "top_left", params[0].replace(/\d+/g, ''));
                }

                // top right
                if(params[1]) {
                    this.$set(this.borders.radius, "top_right", parseInt(params[1], 10));
                    this.$set(this.borders.radiusUnit, "top_right", params[1].replace(/\d+/g, ''));
                }

                // bottom right
                if(params[2]) {
                    this.$set(this.borders.radius, "bottom_right", parseInt(params[2], 10));
                    this.$set(this.borders.radiusUnit, "bottom_right", params[2].replace(/\d+/g, ''));
                }

                // bottom left
                if(params[3]) {
                    this.$set(this.borders.radius, "bottom_left", parseInt(params[3], 10));
                    this.$set(this.borders.radiusUnit, "bottom_left", params[3].replace(/\d+/g, ''));
                }
            }

            const boxShadow = this.block.style["box-shadow"];
            if (boxShadow) {
                this.$set(this.borders, "shadow", true);

                const regexParams = /(-?\d*\.?\d+(px|rem|vh|vw)?)\s*(?![^()]*\))/g;
                const regexRgba = /rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,?\s*([\d.]+)?\s*\)/g;

                let paramMatches = [];
                let rgbaMatches = [];

                let match;

                // Récupération des autres paramètres
                while ((match = regexParams.exec(boxShadow)) !== null) {
                    paramMatches.push(match[1]);
                }

                // Récupération de la valeur RGBA
                while ((match = regexRgba.exec(boxShadow)) !== null) {
                    rgbaMatches.push(match[0]);
                }

                this.$set(this.borders.boxShadow, "horizontal", parseInt(paramMatches[0]));
                this.$set(this.borders.boxShadowUnit, "horizontal", paramMatches[0].replace(/\d+/g, ''));

                this.$set(this.borders.boxShadow, "vertical", parseInt(paramMatches[1]));
                this.$set(this.borders.boxShadowUnit, "vertical", paramMatches[1].replace(/\d+/g, ''));

                this.$set(this.borders.boxShadow, "blur", parseInt(paramMatches[2]));
                this.$set(this.borders.boxShadowUnit, "blur", paramMatches[2].replace(/\d+/g, ''));

                this.$set(this.borders.boxShadow, "spread", parseInt(paramMatches[3]));
                this.$set(this.borders.boxShadowUnit, "spread", paramMatches[3].replace(/\d+/g, ''));

                this.$set(this.borders.boxShadow, "color", this.convertRGBToHex(rgbaMatches[0]));
                this.$set(this.borders.boxShadow, "opacity", this.RGBAToArray(rgbaMatches[0])[3]);
            }
        },
        resetBorderColor: function () {
            this.$set(this.borders, "color", "");
            this.changeBorder();
        },
        resetBorderShadowColor: function () {
            this.$set(this.borders.boxShadow, "color", "");
            this.changeBorder();
        }
    }
}
</script>