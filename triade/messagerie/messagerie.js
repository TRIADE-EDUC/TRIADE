/************************************************************
Procedure de mise en place de la messagerie
Last updated: 08.07.2002
*************************************************************/

    function UtilBeginScript()
    {
        return String.fromCharCode(60, 115, 99, 114, 105, 112, 116, 62);
    }

    function UtilEndScript()
    {
        return String.fromCharCode(60, 47, 115, 99, 114, 105, 112, 116, 62);
    }

        function IDGenerator(nextID)
        {
                this.nextID = nextID;
                this.GenerateID = IDGeneratorGenerateID;
        }

        function IDGeneratorGenerateID()
        {
                return this.nextID++;
        }

        var BUTTON_IMAGE_PREFIX = "buttonImage";
        var BUTTON_DIV_PREFIX = "buttonDiv";
        var BUTTON_PAD1_PREFIX = "buttonPad1";
        var BUTTON_PAD2_PREFIX = "buttonPad2";
        var buttonMap = new Object();

        function Button
        (
                idGenerator,
                caption,
                action,
                image
        )
        {
                this.idGenerator = idGenerator;
                this.caption = caption;
                this.action = action;
                this.image = image;
                this.enabled = true;
                this.Instantiate = ButtonInstantiate;
                this.Enable = ButtonEnable;
        }

        function ButtonInstantiate()
        {
                this.id = this.idGenerator.GenerateID();
                buttonMap[this.id] = this;
                var html = "";
                html += '<div id="';
                html += BUTTON_DIV_PREFIX;
                html += this.id;
                html += '" class="ButtonNormal"';
                html += ' onselectstart="ButtonOnSelectStart()"';
                html += ' ondragstart="ButtonOnDragStart()"';
                html += ' onmousedown="ButtonOnMouseDown(this)"';
                html += ' onmouseup="ButtonOnMouseUp(this)"';
                html += ' onmouseout="ButtonOnMouseOut(this)"';
                html += ' onmouseover="ButtonOnMouseOver(this)"';
                html += ' onclick="ButtonOnClick(this)"';
                html += ' ondblclick="ButtonOnDblClick(this)"';
                html += '>';
                html += '<table cellpadding=0 cellspacing=0 border=0><tr><td><img id="';
                html += BUTTON_PAD1_PREFIX;
                html += this.id;
                html += '" width=2 height=2></td><td></td><td></td></tr><tr><td></td><td>';
                html += '<img id="';
                html += BUTTON_IMAGE_PREFIX;
                html += this.id;
                html += '" src="';
                html += this.image;
                html += '" title="';
                html += this.caption;
                html += '" class="Image"';
                html += '>';
                html += '</td><td></td></tr><tr><td></td><td></td><td><img id="';
                html += BUTTON_PAD2_PREFIX;
                html += this.id;
                html += '" width=2 height=2></td></tr></table>';
                html += '</div>';
                document.write(html);
        }

        function ButtonEnable(enabled)
        {
                this.enabled = enabled;
                if (this.enabled)
                {
                        document.all[BUTTON_DIV_PREFIX + this.id].className = "ButtonNormal";
                }
                else
                {
                        document.all[BUTTON_DIV_PREFIX + this.id].className = "ButtonDisabled";
                }
        }

        function ButtonOnSelectStart()
        {
                window.event.returnValue = false;
        }

        function ButtonOnDragStart()
        {
                window.event.returnValue = false;
        }

        function ButtonOnMouseDown(element)
        {
                if (event.button == 1)
                {
                        var id = element.id.substring(BUTTON_DIV_PREFIX.length, element.id.length);
                        var button = buttonMap[id];
                        if (button.enabled)
                        {
                                ButtonPushButton(id);
                        }
                }
        }

        function ButtonOnMouseUp(element)
        {
                if (event.button == 1)
                {
                        var id = element.id.substring(BUTTON_DIV_PREFIX.length, element.id.length);
                        var button = buttonMap[id];
                        if (button.enabled)
                        {
                                ButtonReleaseButton(id);
                        }
                }
        }

        function ButtonOnMouseOut(element)
        {
                var id = element.id.substring(BUTTON_DIV_PREFIX.length, element.id.length);
                var button = buttonMap[id];
                if (button.enabled)
                {
                        ButtonReleaseButton(id);
                }
        }

        function ButtonOnMouseOver(element)
        {
                var id = element.id.substring(BUTTON_DIV_PREFIX.length, element.id.length);
                var button = buttonMap[id];
                if (button.enabled)
                {
                        ButtonReleaseButton(id);
                        document.all[BUTTON_DIV_PREFIX + id].className = "ButtonMouseOver";
                }
        }

        function ButtonOnClick(element)
        {
                var id = element.id.substring(BUTTON_DIV_PREFIX.length, element.id.length);
                var button = buttonMap[id];
                if (button.enabled)
                {
                        eval(button.action);
                }
        }

        function ButtonOnDblClick(element)
        {
                ButtonOnClick(element);
        }

        function ButtonPushButton(id)
        {
                document.all[BUTTON_PAD1_PREFIX + id].width = 3;
                document.all[BUTTON_PAD1_PREFIX + id].height = 3;
                document.all[BUTTON_PAD2_PREFIX + id].width = 1;
                document.all[BUTTON_PAD2_PREFIX + id].height = 1;
                document.all[BUTTON_DIV_PREFIX + id].className = "ButtonPressed";
        }

        function ButtonReleaseButton(id)
        {
                document.all[BUTTON_PAD1_PREFIX + id].width = 2;
                document.all[BUTTON_PAD1_PREFIX + id].height = 2;
                document.all[BUTTON_PAD2_PREFIX + id].width = 2;
                document.all[BUTTON_PAD2_PREFIX + id].height = 2;
                document.all[BUTTON_DIV_PREFIX + id].className = "ButtonNormal";
        }

    var IMAGE_CHOOSER_DIV_PREFIX = "imageChooserDiv";
    var IMAGE_CHOOSER_IMG_PREFIX = "imageChooserImg";
    var IMAGE_CHOOSER_ICON_PREFIX = "imageChooserIcon";
    var imageChooserMap = new Object();

    function ImageChooser
    (
            idGenerator,
            numRows,
            numCols,
            images,
            callback
    )
    {
            this.idGenerator = idGenerator;
            this.numRows = numRows;
            this.numCols = numCols;
            this.images = images;
            this.callback = callback;
            this.Instantiate = ImageChooserInstantiate;
            this.Show = ImageChooserShow;
            this.Hide = ImageChooserHide;
            this.IsShowing = ImageChooserIsShowing;
            this.SetUserData = ImageChooserSetUserData;
    }

    function ImageChooserInstantiate()
    {
            this.id = this.idGenerator.GenerateID();
            imageChooserMap[this.id] = this;
            var html = '';
            html += '<div id="' + IMAGE_CHOOSER_DIV_PREFIX + this.id + '" style="display:none;position:absolute;background-color:buttonface;border-left:buttonhighlight solid 1px;border-top:buttonhighlight solid 1px;border-right:buttonshadow solid 1px;border-bottom:buttonshadow solid 1px">';
            html += '<table>';
            for (var i = 0; i < this.numRows; i++) {
                    html += '<tr>';
                    for (var j = 0; j < this.numCols; j++) {
                            html += '<td>';
                            var k = i * this.numCols + j;
                            html += '<div id="' + IMAGE_CHOOSER_ICON_PREFIX + this.id + '_' + k + '" style="border:buttonface solid 1px">';
                            html += '<img src="' + this.images[k] + '" id="' + IMAGE_CHOOSER_IMG_PREFIX + this.id + '_' + k + '" onmouseover="ImageChooserOnMouseOver()" onmouseout="ImageChooserOnMouseOut()" onclick="ImageChooserOnClick()">';
                            html += '</div>';
                            html += '</td>';
                    }
                    html += '</tr>';
            }
            html += '</table>';
            html += '</div>';
            document.write(html);
    }

    function ImageChooserShow(x, y)
    {
            eval(IMAGE_CHOOSER_DIV_PREFIX + this.id).style.left = x;
            eval(IMAGE_CHOOSER_DIV_PREFIX + this.id).style.top = y;
            eval(IMAGE_CHOOSER_DIV_PREFIX + this.id).style.display = "block";
    }

    function ImageChooserHide()
    {
            eval(IMAGE_CHOOSER_DIV_PREFIX + this.id).style.display = "none";
    }

    function ImageChooserIsShowing()
    {
            return eval(IMAGE_CHOOSER_DIV_PREFIX + this.id).style.display == "block";
    }

    function ImageChooserSetUserData(userData)
    {
        this.userData = userData;
    }

    function ImageChooserOnMouseOver()
    {
            if (event.srcElement.tagName == "IMG") {
                    var underscore = event.srcElement.id.indexOf("_");
                    if (underscore != -1) {
                            var id = event.srcElement.id.substring(IMAGE_CHOOSER_IMG_PREFIX.length, underscore);
                            var index = event.srcElement.id.substring(underscore + 1);
                            eval(IMAGE_CHOOSER_ICON_PREFIX + id + "_" + index).style.borderColor = "black";
                    }
            }
    }

    function ImageChooserOnMouseOut()
    {
            if (event.srcElement.tagName == "IMG") {
                    var underscore = event.srcElement.id.indexOf("_");
                    if (underscore != -1) {
                            var id = event.srcElement.id.substring(IMAGE_CHOOSER_IMG_PREFIX.length, underscore);
                            var index = event.srcElement.id.substring(underscore + 1);
                            eval(IMAGE_CHOOSER_ICON_PREFIX + id + "_" + index).style.borderColor = "buttonface";
                    }
            }
    }

    function ImageChooserOnClick()
    {
            if (event.srcElement.tagName == "IMG") {
                    var underscore = event.srcElement.id.indexOf("_");
                    if (underscore != -1) {
                            var id = event.srcElement.id.substring(IMAGE_CHOOSER_IMG_PREFIX.length, underscore);
                            var imageChooser = imageChooserMap[id];
                            imageChooser.Hide();
                            var index = event.srcElement.id.substring(underscore + 1);
                            if (imageChooser.callback) {
                                    imageChooser.callback(imageChooser.images[index], imageChooser.userData);
                            }
                    }
            }
    }

        var EDITOR_COMPOSITION_PREFIX = "editorComposition";
        var EDITOR_PARAGRAPH_PREFIX = "editorParagraph";
        var EDITOR_LIST_AND_INDENT_PREFIX = "editorListAndIndent";
        var EDITOR_TOP_TOOLBAR_PREFIX = "editorTopToolbar";
        var EDITOR_BOTTOM_TOOLBAR_PREFIX = "editorBottomToolbar";
        var EDITOR_SMILEY_BUTTON_PREFIX = "editorSmileyButton";
        var EDITOR_IMAGE_CHOOSER_PREFIX = "editorImageChooser";
        var editorMap = new Object();
        var editorIDGenerator = null;

        function Editor(idGenerator)
        {
                this.idGenerator = idGenerator;
                this.textMode = false;
                this.brief = false;
                this.instantiated = false;
                this.Instantiate = EditorInstantiate;
                this.GetText = EditorGetText;
                this.SetText = EditorSetText;
                this.GetHTML = EditorGetHTML;
                this.SetHTML = EditorSetHTML;
                this.GetBrief = EditorGetBrief;
                this.SetBrief = EditorSetBrief;
        }

        function EditorInstantiate()
        {
                if (this.instantiated) {
                        return;
                }
                this.id = this.idGenerator.GenerateID();
                editorMap[this.id] = this;
                editorIDGenerator = this.idGenerator;

                var html = "";
                html += "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">";
                html += "<tr>";
                html += "<td id=\"" + EDITOR_TOP_TOOLBAR_PREFIX + this.id + "\" class=\"Toolbar\">";
                html += "<table cellpaddin=\"0\" cellspacing=\"0\" border=\"0\">";
                html += "<tr>";
                html += "<td>";
                html += "<div class=\"Space\"></div>";
                html += "</td>";
                html += "<td>";
                html += "<div class=\"Swatch\"></div>";
                html += "</td>";
                html += "<td>";
                html += "<select class=\"List\" onchange=\"EditorOnFont(" + this.id + ", this)\">";
                html += "<option class=\"Heading\">Police</option>";
                html += "<option value=\"Arial\">Arial</option>";
                html += "<option value=\"Arial Black\">Arial Black</option>";
                html += "<option value=\"Arial Narrow\">Arial Narrow</option>";
                html += "<option value=\"Comic Sans MS\">Comic Sans MS</option>";
                html += "<option value=\"Courier New\">Courier New</option>";
                html += "<option value=\"System\">System</option>";
                html += "<option value=\"Times New Roman\">Times New Roman</option>";
                html += "<option value=\"Verdana\">Verdana</option>";
                html += "<option value=\"Wingdings\">Wingdings</option>";
                                                                html += "</select>";
                html += "</td>";
                html += "<td>";
                html += "<select class=\"List\" onchange=\"EditorOnSize(" + this.id + ", this)\">";
                html += "<option class=\"Heading\">Taille</option>";
                html += "<option value=\"1\">1</option>";
                html += "<option value=\"2\">2</option>";
                html += "<option value=\"3\">3</option>";
                html += "<option value=\"4\">4</option>";
                html += "<option value=\"5\">5</option>";
                html += "<option value=\"6\">6</option>";
                html += "<option value=\"7\">7</option>";
                html += "</select>";
                html += "</td>";
                html += "<td>";
                html += "<div class=\"Divider\"></div>";
                html += "</td>";
                html += "<td class=\"Text\">";
                html += "</td>";
                html += "</tr>";
                html += "</table>";
                html += "</td>";
                html += "</tr>";
                html += "<tr>";
                html += "<td id=\"" + EDITOR_BOTTOM_TOOLBAR_PREFIX + this.id + "\" class=\"Toolbar\">";
                html += "<table cellpaddin=\"0\" cellspacing=\"0\" border=\"0\">";
                html += "<tr>";
                html += "<td>";
                html += "<div class=\"Space\"></div>";
                html += "</td>";
                html += "<td>";
                html += "<div class=\"Swatch\"></div>";
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var cutButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Couper\",";
                html += "\"EditorOnCut(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/cut.gif\"";
                html += ");";
                html += "cutButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var copyButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Copier\",";
                html += "\"EditorOnCopy(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/copy.gif\"";
                html += ");";
                html += "copyButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var pasteButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Coller\",";
                html += "\"EditorOnPaste(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/paste.gif\"";
                html += ");";
                html += "pasteButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += "<div class=\"Divider\"></div>";
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var boldButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Gras\",";
                html += "\"EditorOnBold(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/bold.gif\"";
                html += ");";
                html += "boldButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var italicButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Italique\",";
                html += "\"EditorOnItalic(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/italic.gif\"";
                html += ");";
                html += "italicButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var underlineButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Souligné\",";
                html += "\"EditorOnUnderline(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/uline.gif\"";
                html += ");";
                html += "underlineButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += "<div class=\"Divider\"></div>";
                html += "</td>";
                html += "<td>";
                html += "<div class=\"Divider\"></div>";
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var alignLeftButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Aligner à gauche\",";
                html += "\"EditorOnAlignLeft(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/aleft.gif\"";
                html += ");";
                html += "alignLeftButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var centerButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Centrer\",";
                html += "\"EditorOnCenter(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/center.gif\"";
                html += ");";
                html += "centerButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var alignRightButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Aligner à droite\",";
                html += "\"EditorOnAlignRight(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/aright.gif\"";
                html += ");";
                html += "alignRightButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += "<div class=\"Divider\"></div>";
                html += "</td>";
                html += "<td id=\"" + EDITOR_LIST_AND_INDENT_PREFIX + this.id + "\" style=\"display:" + (this.brief ? "none" : "inline") + "\">";
                html += "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
                html += "<tr>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var numberedListButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Numérotation\",";
                html += "\"EditorOnNumberedList(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/nlist.gif\"";
                html += ");";
                html += "numberedListButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var bullettedListButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Puces\",";
                html += "\"EditorOnBullettedList(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/blist.gif\"";
                html += ");";
                html += "bullettedListButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += "<div class=\"Divider\"></div>";
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var decreaseIndentButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Diminuer le retrait\",";
                html += "\"EditorOnDecreaseIndent(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/ileft.gif\"";
                html += ");";
                html += "decreaseIndentButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var increaseIndentButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Augmenter le retrait\",";
                html += "\"EditorOnIncreaseIndent(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/iright.gif\"";
                html += ");";
                html += "increaseIndentButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td>";
                html += "<div class=\"Divider\"></div>";
                html += "</td>";
                html += "</tr>";
                html += "</table>";
                html += "</td>";
                html += "<td>";
                html += UtilBeginScript();
                html += "var createHyperlinkButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Insérer un lien hypertexte\",";
                html += "\"EditorOnCreateHyperlink(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/wlink.gif\"";
                html += ");";
                html += "createHyperlinkButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "<td id=\"" + EDITOR_SMILEY_BUTTON_PREFIX + this.id + "\">";
                html += UtilBeginScript();
                html += "var insertSmileyButton = new Button(";
                html += "editorIDGenerator,";
                html += "\"Insérer un smiley\",";
                html += "\"EditorOnStartInsertSmiley(" + this.id + ")\",";
                html += "\"./messagerie/messagerie/smiley.gif\"";
                html += ");";
                html += "insertSmileyButton.Instantiate();";
                html += UtilEndScript();
                html += "</td>";
                html += "</tr>";
                html += "</table>";
                html += "</td>";
                html += "</tr>";
                html += "<tr>";
                html += "<td>";
                html += "<iframe id=\"" + EDITOR_COMPOSITION_PREFIX + this.id + "\" width=\"100%\" height=\"190\">";
                html += "</iframe>";
                html += "</td>";
                html += "</tr>";
                html += "</table>";
                html += UtilBeginScript();
                html += "var " + EDITOR_IMAGE_CHOOSER_PREFIX + this.id + " = new ImageChooser(";
                html += "editorIDGenerator,";
                html += "3, 5,";
                html += "[";
                html += "\"./messagerie/messagerie/smi1.gif\",";
                html += "\"./messagerie/messagerie/smi2.gif\",";
                html += "\"./messagerie/messagerie/smi3.gif\",";
                html += "\"./messagerie/messagerie/smi4.gif\",";
                html += "\"./messagerie/messagerie/smi5.gif\",";
                html += "\"./messagerie/messagerie/smi7.gif\",";
                html += "\"./messagerie/messagerie/smi8.gif\",";
                html += "\"./messagerie/messagerie/smi9.gif\",";
                html += "\"./messagerie/messagerie/smi10.gif\",";
                html += "\"./messagerie/messagerie/smi11.gif\",";
                html += "\"./messagerie/messagerie/smi12.gif\",";
                html += "\"./messagerie/messagerie/smi13.gif\",";
                html += "\"./messagerie/messagerie/smi14.gif\",";
                html += "\"./messagerie/messagerie/smi15.gif\",";
                html += "\"./messagerie/messagerie/smi16.gif\",";
                html += "],";
                html += "EditorOnEndInsertSmiley";
                html += ");";
                html += EDITOR_IMAGE_CHOOSER_PREFIX + this.id + ".SetUserData(" + this.id + ");";
                html += EDITOR_IMAGE_CHOOSER_PREFIX + this.id + ".Instantiate();";
                html += UtilEndScript();
                document.write(html);

                html = '';
                html += '<body style="font:10pt arial">';
                html += '</body>';
                eval(EDITOR_COMPOSITION_PREFIX + this.id).document.open();
                eval(EDITOR_COMPOSITION_PREFIX + this.id).document.write(html);
                eval(EDITOR_COMPOSITION_PREFIX + this.id).document.close();
                eval(EDITOR_COMPOSITION_PREFIX + this.id).document.designMode = "on";
                eval(EDITOR_COMPOSITION_PREFIX + this.id).document.onclick = new Function("EditorOnClick(" + this.id + ")");

                editorIDGenerator = null;
                this.instantiated = true;
        }

        function  EditorGetText()
        {
                return eval(EDITOR_COMPOSITION_PREFIX + this.id).document.body.innerText;
        }

        function  EditorSetText(text)
        {
                text = text.replace(/\n/g, "<br>");
                eval(EDITOR_COMPOSITION_PREFIX + this.id).document.body.innerHTML = text;
        }

        function  EditorGetHTML()
        {
                if (this.textMode) {
                        return eval(EDITOR_COMPOSITION_PREFIX + this.id).document.body.innerText;
                }
                EditorCleanHTML(this.id);
                EditorCleanHTML(this.id);
                return eval(EDITOR_COMPOSITION_PREFIX + this.id).document.body.innerHTML;
        }

        function  EditorSetHTML(html)
        {
                if (this.textMode) {
                        eval(EDITOR_COMPOSITION_PREFIX + this.id).document.body.innerText = html;
                }
                else {
                        eval(EDITOR_COMPOSITION_PREFIX + this.id).document.body.innerHTML = html;
                }
        }

        function EditorGetBrief()
        {
                return this.brief;
        }

        function EditorSetBrief(brief)
        {
                this.brief = brief;
                var display = this.brief ? "none" : "inline";
                if (this.instantiated) {
                        eval(EDITOR_PARAGRAPH_PREFIX + this.id).style.display = display;
                        eval(EDITOR_LIST_AND_INDENT_PREFIX + this.id).style.display = display;
                }
        }

        function EditorOnCut(id)
        {
                EditorFormat(id, "cut");
        }

        function EditorOnCopy(id)
        {
                EditorFormat(id, "copy");
        }

        function EditorOnPaste(id)
        {
                EditorFormat(id, "paste");
        }

        function EditorOnBold(id)
        {
                EditorFormat(id, "bold");
        }

        function EditorOnItalic(id)
        {
                EditorFormat(id, "italic");
        }

        function EditorOnUnderline(id)
        {
                EditorFormat(id, "underline");
        }

        function EditorOnForegroundColor(id)
        {
                if (!EditorValidateMode(id)) {
                        return;
                }
                var color = showModalDialog("/ym/ColorSelect", "", "font-family:Verdana;font-size:12;dialogWidth:30em;dialogHeight:35em");
                if (color) {
                        EditorFormat(id, "forecolor", color);
                }
                else {

                        eval(EDITOR_COMPOSITION_PREFIX + id).focus();
                }
        }

        function EditorOnBackgroundColor(id)
        {
                if (!EditorValidateMode(id)) {
                        return;
                }
                var color = showModalDialog("ColorSelect", "", "font-family:Verdana;font-size:12;dialogWidth:30em;dialogHeight:35em");
                if (color) {
                        EditorFormat(id, "backcolor", color);
                }
                else {
                        eval(EDITOR_COMPOSITION_PREFIX + id).focus();
                }
        }

        function EditorOnAlignLeft(id)
        {
                EditorFormat(id, "justifyleft");
        }

        function EditorOnCenter(id)
        {
                EditorFormat(id, "justifycenter");
        }

        function EditorOnAlignRight(id)
        {
                EditorFormat(id, "justifyright");
        }

        function EditorOnNumberedList(id)
        {
                EditorFormat(id, "insertOrderedList");
        }

        function EditorOnBullettedList(id)
        {
                EditorFormat(id, "insertUnorderedList");
        }

        function EditorOnDecreaseIndent(id)
        {
                EditorFormat(id, "outdent");
        }

        function EditorOnIncreaseIndent(id)
        {
                EditorFormat(id, "indent");
        }

        function EditorOnCreateHyperlink(id)
        {
                if (!EditorValidateMode(id)) {
                        return;
                }
                var anchor = EditorGetElement("A", eval(EDITOR_COMPOSITION_PREFIX + id).document.selection.createRange().parentElement());
                var link = prompt("Sélectionner d'abord votre texte \net saisissez le lien (exemple : http://www.triade-educ.com) :", anchor ? anchor.href : "http://");
                if (link && link != "http://") {
                        if (eval(EDITOR_COMPOSITION_PREFIX + id).document.selection.type == "None") {
                                var range = eval(EDITOR_COMPOSITION_PREFIX + id).document.selection.createRange();
                                range.pasteHTML('<A HREF="' + link + '"></A>');
                                range.select();
                        }
                        else {
                                EditorFormat(id, "CreateLink", link);
                        }
                }
        }

        function EditorOnStartInsertSmiley(id)
        {
                if (eval(EDITOR_IMAGE_CHOOSER_PREFIX + id).IsShowing()) {
                        eval(EDITOR_IMAGE_CHOOSER_PREFIX + id).Hide();
                }
                else {
                        var editor = editorMap[id];
                        editor.selectionRange = eval(EDITOR_COMPOSITION_PREFIX + id).document.selection.createRange();
                        eval(EDITOR_IMAGE_CHOOSER_PREFIX + id).Show(eval(EDITOR_SMILEY_BUTTON_PREFIX + id).offsetLeft - 124, eval(EDITOR_BOTTOM_TOOLBAR_PREFIX + id).offsetTop + eval(EDITOR_BOTTOM_TOOLBAR_PREFIX + id).offsetHeight - 124);
                }
        }

        function EditorOnEndInsertSmiley(image, id)
        {
            if (!EditorValidateMode(id)) {
                return;
            }
            var imgTag = '<img src="' + image + '">';
            var editor = editorMap[id];
            var bodyRange = eval(EDITOR_COMPOSITION_PREFIX + id).document.body.createTextRange();
            if (bodyRange.inRange(editor.selectionRange)) {
                editor.selectionRange.pasteHTML(imgTag);
                eval(EDITOR_COMPOSITION_PREFIX + id).focus();
            }
            else {
                eval(EDITOR_COMPOSITION_PREFIX + id).document.body.innerHTML += imgTag;
                editor.selectionRange.collapse(false);
                editor.selectionRange.select();
            }
        }

        function EditorOnParagraph(id, select)
        {
                EditorFormat(id, "formatBlock", select[select.selectedIndex].value);
                select.selectedIndex = 0;
        }

        function EditorOnFont(id, select)
        {
                EditorFormat(id, "fontname", select[select.selectedIndex].value);
                select.selectedIndex = 0;
        }

        function EditorOnSize(id, select)
        {
                EditorFormat(id, "fontsize", select[select.selectedIndex].value);
                select.selectedIndex = 0;
        }

        function EditorOnViewHTMLSource(id, textMode)
        {
                var editor = editorMap[id];
                editor.textMode = textMode;
                if (editor.textMode) {
                        EditorCleanHTML(id);
                        EditorCleanHTML(id);
                        eval(EDITOR_COMPOSITION_PREFIX + id).document.body.innerText = eval(EDITOR_COMPOSITION_PREFIX + id).document.body.innerHTML;
                }
                else {
                        eval(EDITOR_COMPOSITION_PREFIX + id).document.body.innerHTML = eval(EDITOR_COMPOSITION_PREFIX + id).document.body.innerText;
                }
                eval(EDITOR_COMPOSITION_PREFIX + id).focus();
        }

        function EditorOnClick(id)
        {
                eval(EDITOR_IMAGE_CHOOSER_PREFIX + id).Hide();
        }

        function EditorValidateMode(id)
        {
                var editor = editorMap[id];
                if (!editor.textMode) {
                        return true;
                }
                eval(EDITOR_COMPOSITION_PREFIX + id).focus();
                return false;
        }

        function EditorFormat(id, what, opt)
        {
                if (!EditorValidateMode(id)) {
                        return;
                }
                if (opt == "removeFormat") {
                        what = opt;
                        opt = null;
                }
                if (opt == null) {
                        eval(EDITOR_COMPOSITION_PREFIX + id).document.execCommand(what);
                }
                else {
                        eval(EDITOR_COMPOSITION_PREFIX + id).document.execCommand(what, "", opt);
                }
        }

        function EditorCleanHTML(id)
        {
                var fonts = eval(EDITOR_COMPOSITION_PREFIX + id).document.body.all.tags("FONT");
                for (var i = fonts.length - 1; i >= 0; i--) {
                        var font = fonts[i];
                        if (font.style.backgroundColor == "#ffffff") {
                                font.outerHTML = font.innerHTML;
                        }
                }
        }

        function EditorGetElement(tagName, start)
        {
                while (start && start.tagName != tagName) {
                        start = start.parentElement;
                }
                return start;
        }


function Switch() {
  //document.Compose.Body.value=editor.GetHTML());
  //document.Compose.Body.value = editor.GetText();
  //document.Compose.action = document.Compose.action + "&SWITCH=1";
  document.formulaire.resultat.value+=editor.GetHTML();
  //document.Compose.submit();
}
