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
            <h3>{{ messages.get('dsi', 'view_wysiwyg_input_text') }}</h3>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-patterns">{{ messages.get('dsi', 'label_pattern') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <select name="wysiwyg-patterns" id="wysiwyg-patterns" class="dsi-patterns" v-model="pattern">
                            <option value="">{{ messages.get('common', 'common_default_select') }}</option>
                            <optgroup :label="group" v-for="(patterns, group) in patterns" :key="group">
                                <option v-for="(label, pattern) in patterns" :value="pattern" :key="pattern">
                                    {{ label }}
                                </option>
                            </optgroup>
                        </select>
                        <input type="button" class="bouton" :value="messages.get('common', 'common_insert_pattern')" @click="addPattner"/>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_text') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <textarea v-model="block.content"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <h3>{{ messages.get('dsi', 'view_wysiwyg_input_text_style') }}</h3>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-text-align">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_align') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <select id="wysiwyg-input-text-align" name="wysiwyg-input-text-align" v-model="block.text.style.textAlign">
                            <option value="center">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_align_center') }}</option>
                            <option value="right">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_align_right') }}</option>
                            <option value="left">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_align_left') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-text-color">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_color') }}</label>
                <div class="dsi-form-group-content">
                    <input type="color" id="wysiwyg-input-text-color" name="wysiwyg-input-text-color" v-model="block.text.style.color">
                    <button class="color-reset" v-if="block.text.style.color" @click="block.text.style.color = ''" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-text-size">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_size') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input min="0" max="100" :value="fontSize" type="range" id="wysiwyg-input-text-size" name="wysiwyg-input-text-size" @input="changeFontSize($event)">
                        <input min="0" max="100" type="number" id="wysiwyg-input-text-size-input" :value="fontSize" name="wysiwyg-input-text-size-input" @input="changeFontSize($event)">
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-text-line-height">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_line_height') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input min="0" max="75" :value="lineHeight" type="range" id="wysiwyg-input-text-line-height" name="wysiwyg-input-text-line-height" @input="changeLineHeight($event)">
                        <input min="0" max="75" type="number" id="wysiwyg-input-text-line-height-input" :value="lineHeight" name="wysiwyg-input-text-line-height-input" @input="changeLineHeight($event)">
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-text-letter-space">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_letter_space') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input min="0" max="100" :value="letterSpacing" type="range" id="wysiwyg-input-text-letter-space" name="wysiwyg-input-text-letter-space" @input="changeLetterSpacing($event)">
                        <input min="0" max="100" type="number" id="wysiwyg-input-text-letter-space-input" :value="letterSpacing" name="wysiwyg-input-text-letter-space-input" @input="changeLetterSpacing($event)">
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-text-font-weight">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_weight') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <select id="wysiwyg-input-text-font-weight" name="wysiwyg-input-text-font-weight" v-model="block.text.style.fontWeight">
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="300">300</option>
                            <option value="400">400</option>
                            <option value="500">500</option>
                            <option value="600">600</option>
                            <option value="700">700</option>
                            <option value="800">800</option>
                            <option value="900">900</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-text-font-style">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_font') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <select id="wysiwyg-input-text-font-style" name="wysiwyg-input-text-font-style" v-model="block.text.style.fontStyle">
                            <option value="normal">Normal</option>
                            <option value="italic">Italique</option>
                            <option value="oblique 23deg">Oblique</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-text-font-decoration">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_font_decoration') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <select id="wysiwyg-input-text-font-decoration" name="wysiwyg-input-text-font-decoration" v-model="block.text.style.textDecoration">
                            <option value="none">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_font_decoration_default') }}</option>
                            <option value="underline">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_font_decoration_underline') }}</option>
                            <option value="wavy underline">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_font_decoration_wavy') }}</option>
                            <option value="overline">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_font_decoration_overline') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

	export default {
        name: "textInputForm",
		props: ["block"],
        data: function() {
            return {
                pattern: "",
                patterns: {},
                //arrayUnit: ["px", "rem", "%", "vh", "vw"],
                fontSize: this.block.text.style.fontSize ? this.block.text.style.fontSize.replace("px", "") : 18,
                lineHeight: this.block.text.style.fontSize ? this.block.text.style.lineHeight.replace("px", "") : 20,
                letterSpacing: this.block.text.style.fontSize ? this.block.text.style.letterSpacing.replace("px", "") : 0,
            }
        },
        created: function() {
            this.fetchPatterns();
        },
        mounted: function() {
            if (!this.block.name) {
                this.$set(this.block, "name", "");
            }
            
            if(!this.block.text.style.textAlign) {
                this.$set(this.block.text.style, "textAlign", "left");
            }

            if(!this.block.text.style.fontSize) {
                this.$set(this.block.text.style, "fontSize", "18px");
            }

            if(!this.block.text.style.lineHeight) {
                this.$set(this.block.text.style, "lineHeight", "20px");
            }

            if(!this.block.text.style.letterSpacing) {
                this.$set(this.block.text.style, "letterSpacing", "0px");
            }

            if(!this.block.text.style.fontWeight) {
                this.$set(this.block.text.style, "fontWeight", "400");
            }

            if(!this.block.text.style.fontStyle) {
                this.$set(this.block.text.style, "fontStyle", "normal");
            }

            if(!this.block.text.style.textDecoration) {
                this.$set(this.block.text.style, "textDecoration", "none");
            }
        },
		methods: {
            changeFontSize: function(event) {
                this.fontSize = event.target.value;
                this.$set(this.block.text.style, "fontSize", + this.fontSize + "px");
            },
            changeLineHeight: function(event) {
                this.lineHeight = event.target.value;
                this.$set(this.block.text.style, "lineHeight", + this.lineHeight + "px");
            },
            changeLetterSpacing: function(event) {
                this.letterSpacing = event.target.value;
                this.$set(this.block.text.style, "letterSpacing", + this.letterSpacing + "px");
            },
            fetchPatterns: function() {
                this.ws.get('input', 'patterns').then(result => {
                    if (result.error) {
                        this.notif.error(result.errorMessage);
                    } else {
                        this.patterns = result
                    }
                });
            },
            addPattner: function() {
                this.block.content += this.pattern;
                this.pattern = "";
            }
		}
	}
</script>