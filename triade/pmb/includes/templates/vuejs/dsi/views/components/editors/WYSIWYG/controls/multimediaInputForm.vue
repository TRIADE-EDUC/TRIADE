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
            <div v-if="block.content != '' && elementType != 'video'" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_' + this.elementType+'_keep_ratio') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input :checked="block.keepRatio == '1'" type="checkbox" @input="updateElementRatio" />
                    </div>
                </div>
            </div>
            <div v-if="block.content != '' && block.keepRatio == '0'" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_' + this.elementType + '_width') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input :id="'wysiwyg_' + this.elementType + '_width_input'" type="number" min="1" :value="widthValue" @input="updateElementWidth" />
                        <select v-model="widthUnit" @change="updateElementWidth($event, true)">
                            <option v-for="unit of arrayUnit" :value="unit">{{ unit }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div v-if="block.content != '' && block.keepRatio == '0'" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_' + this.elementType + '_height') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input :id="'wysiwyg_' + this.elementType + '_height_input'" type="number" min="1" :value="heightValue" @input="updateElementHeight" />
                        <select v-model="heightUnit" @change="updateElementHeight($event, true)">
                            <option v-for="unit of arrayUnit" :value="unit">{{ unit }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div v-if="block.content != '' && block.keepRatio == '1'" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_' + this.elementType + '_size') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input :id="'wysiwyg_' + this.elementType + '_width_input'"  type="number" min="1" :value="widthValue" @input="updateElementWidth" />
                        <select v-model="widthUnit" @change="updateElementWidth($event, true)">
                            <option v-for="unit of arrayUnit" :value="unit">{{ unit }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="block.content != ''">
            <h3>{{ messages.get('dsi', 'view_wysiwyg_input_' + this.elementType + '_position') }}</h3>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" :for="elementType">{{ messages.get('dsi', 'view_wysiwyg_input_' + this.elementType + '_position_x') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <select :value="block.style.block['justify-content']" @change="updatePositionX">
                            <option v-for="(position, index) in elementPositionsX" :value="position.value" :key="index">{{ position.label }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" :for="elementType">{{ messages.get('dsi', 'view_wysiwyg_input_' + this.elementType + '_position_y') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <select :value="block.style.block['align-items']" @change="updatePositionY">
                            <option v-for="(position, index) in elementPositionsY" :value="position.value" :key="index">{{ position.label }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "multimediaInputForm",
    props: ['block', 'elementType'],
    data: function() {
        return {
            elementPositionsX: [
                {
                    value : "start",
                    label : this.messages.get('dsi', 'view_wysiwyg_position_left')
                },
                {
                    value : "center",
                    label : this.messages.get('dsi', 'view_wysiwyg_position_centered')
                },
                {
                    value : "end",
                    label : this.messages.get('dsi', 'view_wysiwyg_position_right')
                }
            ],
            elementPositionsY: [
                {
                    value : "start",
                    label : this.messages.get('dsi', 'view_wysiwyg_position_top')
                },
                {
                    value : "center",
                    label : this.messages.get('dsi', 'view_wysiwyg_position_centered')
                },
                {
                    value : "end",
                    label : this.messages.get('dsi', 'view_wysiwyg_position_bottom')
                }
            ],
            arrayUnit: ["px", "%"],
            widthUnit: "px",
            heightUnit: "px",
        }
    },
    mounted: function() {
        if (!this.block.name) {
            this.$set(this.block, "name", "");
        }
    },
    created: function() {
        if (this.block.style[this.elementType] && this.block.style[this.elementType].width) {
            for (const unit of this.arrayUnit) {
                if (this.block.style[this.elementType].width.includes(unit)) {
                    this.widthUnit = unit;
                    break;
                }
            }
        }

        if (this.block.style[this.elementType] && this.block.style[this.elementType].height) {
            for (const unit of this.arrayUnit) {
                if (this.block.style[this.elementType].height.includes(unit)) {
                    this.heightUnit = unit;
                    break;
                }
            }
        }
    },
    watch: {
        widthUnit: function() {
            let width = this.block.imgWidth;
            if(this.widthUnit == "%") {
                width = 100;

                if(this.widthValue && this.widthValue <= 100) {
                    width = this.widthValue;
                }
            }

            this.$set(this.block.style[this.elementType], "width", width + this.widthUnit);
        },
        heightUnit: function() {
            let height = this.block.imgHeight;
            if(this.heightUnit == "%") {
                height = 100;

                if(this.heightValue && this.heightValue <= 100) {
                    height = this.heightValue;
                }
            }

            this.$set(this.block.style[this.elementType], "height", height + this.heightUnit);
        }
    },
    computed: {
        heightValue: function() {
            if(this.block.style[this.elementType].height) {
                return this.block.style[this.elementType].height.replace(this.heightUnit, "");
            }
        },
        widthValue: function() {
            if(this.block.style[this.elementType].width) {
                return this.block.style[this.elementType].width.replace(this.widthUnit, "");
            }
        }
    },
    methods: {
        updateElementHeight: function(e, reload = false) {
            if(reload) {
                this.$set(this.block.style[this.elementType], "height", e.target.previousElementSibling.value + this.heightUnit);
                return;
            }
            this.$set(this.block.style[this.elementType], 'height', e.target.value + this.heightUnit);
        },
        updateElementWidth: function(e, reload = false) {
            if(reload) {
                this.$set(this.block.style[this.elementType], "width", e.target.previousElementSibling.value + this.widthUnit);
                return;
            }
            this.$set(this.block.style[this.elementType], 'width', e.target.value + this.widthUnit);
        },
        updateElementRatio: function(e) {
            if(e.target.checked) {
                this.$delete(this.block.style[this.elementType], 'height');
                this.$set(this.block, 'keepRatio', "1");
            } else {    
                this.$set(this.block.style[this.elementType], 'height', this.block.style[this.elementType].width);
                this.heightUnit = this.widthUnit;
                this.$set(this.block, 'keepRatio', "0");
            }
        },
        updatePositionX: function(e) {
            this.$set(this.block.style.block, "justify-content", e.target.value);
        },
        updatePositionY: function(e) {
            this.$set(this.block.style.block, "align-items", e.target.value);
        }
    }
}
</script>