<?php

class __Mustache_c237dddfcc0017ca2c618ca0be18743e extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="container-fluid langimport">
';
        $buffer .= $indent . '    <div class="row rtl-compatible">
';
        $buffer .= $indent . '        <div class="col-md-';
        $value = $context->find('caninstall');
        $buffer .= $this->section1281316342ba7c72fa44fcbc12b59cb2($context, $indent, $value);
        $value = $context->find('caninstall');
        if (empty($value)) {
            
            $buffer .= '12';
        }
        $buffer .= ' span';
        $value = $context->find('caninstall');
        $buffer .= $this->section1281316342ba7c72fa44fcbc12b59cb2($context, $indent, $value);
        $value = $context->find('caninstall');
        if (empty($value)) {
            
            $buffer .= '12';
        }
        $buffer .= ' mb-1">
';
        $buffer .= $indent . '            <form id="uninstallform" action="';
        $value = $this->resolveValue($context->find('uninstallurl'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" method="post">
';
        $buffer .= $indent . '                <fieldset>
';
        $buffer .= $indent . '                    <div class="form-group">
';
        $buffer .= $indent . '                        <label for="menuuninstalllang">';
        $value = $context->find('str');
        $buffer .= $this->section9b50f9e0ecc34ece08d59347100dd12d($context, $indent, $value);
        $buffer .= '</label>
';
        $buffer .= $indent . '                        <select size="15" multiple="multiple" id="menuuninstalllang" class="form-control input-block-level" name="uninstalllang[]">
';
        $value = $context->find('installedoptions');
        $buffer .= $this->sectionEdd5ce8c6679e284d601457c1ad171bd($context, $indent, $value);
        $buffer .= $indent . '                        </select>
';
        $buffer .= $indent . '                    </div>
';
        $buffer .= $indent . '                    <div class="form-group">
';
        $buffer .= $indent . '                        <input type="hidden" name="sesskey" value="';
        $value = $this->resolveValue($context->find('sesskey'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        $buffer .= $indent . '                        <input id="languninstallbutton" type="submit" value="';
        $value = $context->find('str');
        $buffer .= $this->section9747e6d81332a186bcc6beef8799cab4($context, $indent, $value);
        $buffer .= '" class="btn btn-secondary">
';
        $buffer .= $indent . '                    </div>
';
        $buffer .= $indent . '                </fieldset>
';
        $buffer .= $indent . '            </form>
';
        $value = $context->find('updateurl');
        $buffer .= $this->sectionF2c09f61ae68a11366567194bd4fafae($context, $indent, $value);
        $buffer .= $indent . '        </div>
';
        $value = $context->find('caninstall');
        $buffer .= $this->section00d4e6cde2ffeb415f52b4991224a51b($context, $indent, $value);
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';
        $value = $context->find('js');
        $buffer .= $this->section1f82cc0d74b37d18446d267990722697($context, $indent, $value);

        return $buffer;
    }

    private function section1281316342ba7c72fa44fcbc12b59cb2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '6';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '6';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9b50f9e0ecc34ece08d59347100dd12d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'installedlangs, tool_langimport';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'installedlangs, tool_langimport';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC877874b20aed109ed5be9bdc0ef9c49(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'selected="selected"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'selected="selected"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEdd5ce8c6679e284d601457c1ad171bd(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <option value="{{value}}" {{#selected}}selected="selected"{{/selected}}>{{{text}}}‎</option>
                            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <option value="';
                $value = $this->resolveValue($context->find('value'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" ';
                $value = $context->find('selected');
                $buffer .= $this->sectionC877874b20aed109ed5be9bdc0ef9c49($context, $indent, $value);
                $buffer .= '>';
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '‎</option>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9747e6d81332a186bcc6beef8799cab4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'uninstall, tool_langimport';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'uninstall, tool_langimport';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3fdad3b251296edb8d8998a815a63900(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'updatelangs, tool_langimport';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'updatelangs, tool_langimport';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section2222913e2d5e9035655d97f5bc71ba27(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{updatelangstaskname}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('updatelangstaskname'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE0ad72b22c8e994f62ac48837db18e98(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' updatelangsnote, tool_langimport, {"taskname": {{#quote}}{{updatelangstaskname}}{{/quote}} }';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' updatelangsnote, tool_langimport, {"taskname": ';
                $value = $context->find('quote');
                $buffer .= $this->section2222913e2d5e9035655d97f5bc71ba27($context, $indent, $value);
                $buffer .= ' }';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0a4f1dc9b80a28ca7163fb4de6a1975c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <p class="mt-2 small">{{#str}} updatelangsnote, tool_langimport, {"taskname": {{#quote}}{{updatelangstaskname}}{{/quote}} }{{/str}}</p>
                            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <p class="mt-2 small">';
                $value = $context->find('str');
                $buffer .= $this->sectionE0ad72b22c8e994f62ac48837db18e98($context, $indent, $value);
                $buffer .= '</p>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF2c09f61ae68a11366567194bd4fafae(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <div>
                    <form id="updateform" action="{{updateurl}}" method="post">
                        <fieldset>
                            <input type="submit" value="{{#str}}updatelangs, tool_langimport{{/str}}" class="btn btn-secondary">
                            {{#hasmanyinstalledlanguages}}
                                <p class="mt-2 small">{{#str}} updatelangsnote, tool_langimport, {"taskname": {{#quote}}{{updatelangstaskname}}{{/quote}} }{{/str}}</p>
                            {{/hasmanyinstalledlanguages}}
                        </fieldset>
                    </form>
                </div>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <div>
';
                $buffer .= $indent . '                    <form id="updateform" action="';
                $value = $this->resolveValue($context->find('updateurl'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" method="post">
';
                $buffer .= $indent . '                        <fieldset>
';
                $buffer .= $indent . '                            <input type="submit" value="';
                $value = $context->find('str');
                $buffer .= $this->section3fdad3b251296edb8d8998a815a63900($context, $indent, $value);
                $buffer .= '" class="btn btn-secondary">
';
                $value = $context->find('hasmanyinstalledlanguages');
                $buffer .= $this->section0a4f1dc9b80a28ca7163fb4de6a1975c($context, $indent, $value);
                $buffer .= $indent . '                        </fieldset>
';
                $buffer .= $indent . '                    </form>
';
                $buffer .= $indent . '                </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDf671d9178cc9b6249e194329a2d4165(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'availablelangs, install';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'availablelangs, install';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section296ff257a82440bc7dbc8277228b096c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                    <option value="{{value}}" {{#selected}}selected="selected"{{/selected}}>{{{text}}}‎</option>
                                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                    <option value="';
                $value = $this->resolveValue($context->find('value'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" ';
                $value = $context->find('selected');
                $buffer .= $this->sectionC877874b20aed109ed5be9bdc0ef9c49($context, $indent, $value);
                $buffer .= '>';
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '‎</option>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9e18d214c735de75313ec8cae71810cf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' search, tool_langimport ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' search, tool_langimport ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7523292bf79cd2ac3a986294349485f5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'install, tool_langimport';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'install, tool_langimport';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section00d4e6cde2ffeb415f52b4991224a51b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div class="col-md-6 mb-1">
                <form id="installform" action="{{installurl}}" method="post">
                    <fieldset>
                        <div class="form-group">
                            <label for="menupack">{{#str}}availablelangs, install{{/str}}</label>
                            <select size="15" multiple="multiple" class="form-control input-block-level" id="menupack" name="pack[]">
                                {{#toinstalloptions}}
                                    <option value="{{value}}" {{#selected}}selected="selected"{{/selected}}>{{{text}}}‎</option>
                                {{/toinstalloptions}}
                            </select>
                        </div>
                        <div class="mb-3">
                            {{< core/search_input_auto }}
                                {{$label}}
                                    {{#str}} search, tool_langimport {{/str}}
                                {{/label}}
                            {{/ core/search_input_auto }}
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="sesskey" value="{{sesskey}}">
                            <input type="submit" value="{{#str}}install, tool_langimport{{/str}}" class="btn btn-secondary">
                        </div>
                    </fieldset>
                </form>
            </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div class="col-md-6 mb-1">
';
                $buffer .= $indent . '                <form id="installform" action="';
                $value = $this->resolveValue($context->find('installurl'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" method="post">
';
                $buffer .= $indent . '                    <fieldset>
';
                $buffer .= $indent . '                        <div class="form-group">
';
                $buffer .= $indent . '                            <label for="menupack">';
                $value = $context->find('str');
                $buffer .= $this->sectionDf671d9178cc9b6249e194329a2d4165($context, $indent, $value);
                $buffer .= '</label>
';
                $buffer .= $indent . '                            <select size="15" multiple="multiple" class="form-control input-block-level" id="menupack" name="pack[]">
';
                $value = $context->find('toinstalloptions');
                $buffer .= $this->section296ff257a82440bc7dbc8277228b096c($context, $indent, $value);
                $buffer .= $indent . '                            </select>
';
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                        <div class="mb-3">
';
                $buffer .= $indent . '                            ';
                if ($parent = $this->mustache->loadPartial('core/search_input_auto')) {
                    $context->pushBlockContext(array(
                        'label' => array($this, 'blockF2d0e22a2aff6aff8da6b5f9949bbd19'),
                    ));
                    $buffer .= $parent->renderInternal($context, $indent);
                    $context->popBlockContext();
                }
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                        <div class="form-group">
';
                $buffer .= $indent . '                            <input type="hidden" name="sesskey" value="';
                $value = $this->resolveValue($context->find('sesskey'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                            <input type="submit" value="';
                $value = $context->find('str');
                $buffer .= $this->section7523292bf79cd2ac3a986294349485f5($context, $indent, $value);
                $buffer .= '" class="btn btn-secondary">
';
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                    </fieldset>
';
                $buffer .= $indent . '                </form>
';
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1f82cc0d74b37d18446d267990722697(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    require([\'tool_langimport/search\'], function(search) {
        search.init(document.querySelector(\'#installform\'));
    });
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    require([\'tool_langimport/search\'], function(search) {
';
                $buffer .= $indent . '        search.init(document.querySelector(\'#installform\'));
';
                $buffer .= $indent . '    });
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    public function blockF2d0e22a2aff6aff8da6b5f9949bbd19($context)
    {
        $indent = $buffer = '';
        $buffer .= '                                    ';
        $value = $context->find('str');
        $buffer .= $this->section9e18d214c735de75313ec8cae71810cf($context, $indent, $value);
        $buffer .= '
';
    
        return $buffer;
    }
}
