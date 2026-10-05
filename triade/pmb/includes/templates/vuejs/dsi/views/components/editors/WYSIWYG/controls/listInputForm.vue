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
            <h3>{{ messages.get('dsi', 'view_wysiwyg_input_list') }}</h3>
            <div class="dsi-form-group dsi-form-wysiwyg-35">
                <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_list_elements') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line" v-for="(element, index) in block.list.elements" :key="index">
                        <input class="list-input-text-item" type="text" name="wysiwyg-input-list-text" v-model="block.list.elements[index]">
                        <button class="list-item-remove-button" type="button" @click="removeItem(index)"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                    <div class="dsi-form-group-line">
                        <button class="bouton list-item-add-button" type="button" @click="addItem()"><i class="fa fa-plus" aria-hidden="true"></i></button>
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
                        <select id="wysiwyg-input-text-align" name="wysiwyg-input-text-align" v-model="block.list.style.textAlign">
                            <option value="center">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_align_center') }}</option>
                            <option value="right">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_align_right') }}</option>
                            <option value="left">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_align_left') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-list-style">{{ messages.get('dsi', 'view_wysiwyg_input_list_style_bullete') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <select id="wysiwyg-input-list-style" name="wysiwyg-input-list-style" v-model="block.list.style.listStyleType">
                            <option value="disc">{{ messages.get('dsi', 'view_wysiwyg_input_list_style_bullete_disc') }}</option>
                            <option value="circle">{{ messages.get('dsi', 'view_wysiwyg_input_list_style_bullete_circle') }}</option>
                            <option value="square">{{ messages.get('dsi', 'view_wysiwyg_input_list_style_bullete_square') }}</option>
                            <option value="decimal">{{ messages.get('dsi', 'view_wysiwyg_input_list_style_bullete_decimal') }}</option>
                            <option value="none">{{ messages.get('dsi', 'view_wysiwyg_input_list_style_bullete_none') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-input-text-color">{{ messages.get('dsi', 'view_wysiwyg_input_text_style_color') }}</label>
                <div class="dsi-form-group-content">
                    <input type="color" id="wysiwyg-input-text-color" name="wysiwyg-input-text-color" v-model="block.list.style.color">
                    <button class="color-reset" v-if="block.list.style.color" @click="block.list.style.color = ''" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>
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
                        <select id="wysiwyg-input-text-font-weight" name="wysiwyg-input-text-font-weight" v-model="block.list.style.fontWeight">
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
                        <select id="wysiwyg-input-text-font-style" name="wysiwyg-input-text-font-style" v-model="block.list.style.fontStyle">
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
                        <select id="wysiwyg-input-text-font-decoration" name="wysiwyg-input-text-font-decoration" v-model="block.list.style.textDecoration">
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
        name: "listInputForm",
		props: ["block"],
        data: function() {
            return {
                //arrayUnit: ["px", "rem", "%", "vh", "vw"],
                fontSize: this.block.list.style.fontSize ? this.block.list.style.fontSize.replace("px", "") : 18,
                lineHeight: this.block.list.style.lineHeight ? this.block.list.style.lineHeight.replace("px", "") : 10,
                letterSpacing: this.block.list.style.letterSpacing ? this.block.list.style.letterSpacing.replace("px", "") : 0,
            }
        },
        mounted: function() {
            if (!this.block.name) {
                this.$set(this.block, "name", "");
            }

            if(!this.block.list.style.textAlign) {
                this.$set(this.block.list.style, "textAlign", "left");
            }

            if(!this.block.list.style.listStyleType) {
                this.$set(this.block.list.style, "listStyleType", "disc");
            }

            if(!this.block.list.style.fontSize) {
                this.$set(this.block.list.style, "fontSize", "18px");
            }

            if(!this.block.list.style.lineHeight) {
                this.$set(this.block.list.style, "lineHeight", "10px");
            }

            if(!this.block.list.style.letterSpacing) {
                this.$set(this.block.list.style, "letterSpacing", "0px");
            }

            if(!this.block.list.style.fontWeight) {
                this.$set(this.block.list.style, "fontWeight", "400");
            }

            if(!this.block.list.style.fontStyle) {
                this.$set(this.block.list.style, "fontStyle", "normal");
            }

            if(!this.block.list.style.textDecoration) {
                this.$set(this.block.list.style, "textDecoration", "none");
            }
        },
		methods: {
            addItem: function() {
                this.block.list.elements.push("");

                setTimeout(function() {
                    let inputs = document.getElementsByName("wysiwyg-input-list-text");
                    inputs[inputs.length-1].focus();
                }, 200);
            },
            removeItem: function(index) {
                this.block.list.elements.splice(index, 1);
            },
            changeFontSize: function(event) {
                this.fontSize = event.target.value;
                this.$set(this.block.list.style, "fontSize", + this.fontSize + "px");
            },
            changeLineHeight: function(event) {
                this.lineHeight = event.target.value;
                this.$set(this.block.list.style, "lineHeight", + this.lineHeight + "px");
            },
            changeLetterSpacing: function(event) {
                this.letterSpacing = event.target.value;
                this.$set(this.block.list.style, "letterSpacing", + this.letterSpacing + "px");
            }
		}
	}
</script>