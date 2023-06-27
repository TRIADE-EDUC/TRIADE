<?php

class __Mustache_b4ee8a1e6508e4cd215c4f0c77ca8ca8 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="form-item row" id="';
        $value = $this->resolveValue($context->find('id'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        $buffer .= $indent . '    <div class="form-label col-sm-3 text-sm-right">
';
        $value = $context->find('customcontrol');
        $buffer .= $this->section02eed9586865ec182b35f966ca78dc26($context, $indent, $value);
        $value = $context->find('customcontrol');
        if (empty($value)) {
            
            $buffer .= $indent . '            <label ';
            $value = $context->find('labelfor');
            $buffer .= $this->section3d8f2ee95af1360bf9488b09a602ca85($context, $indent, $value);
            $buffer .= '>
';
            $buffer .= $indent . '                ';
            $value = $this->resolveValue($context->find('title'), $context);
            $buffer .= ($value === null ? '' : $value);
            $buffer .= '
';
            $value = $context->find('override');
            $buffer .= $this->sectionDdacf7fb733fa0ef79e908dd9d6b1566($context, $indent, $value);
            $value = $context->find('warning');
            $buffer .= $this->sectionEd8f8e664dc8c642b488c4edf7f112e4($context, $indent, $value);
            $buffer .= $indent . '            </label>
';
        }
        $buffer .= $indent . '        <span class="form-shortname d-block small text-muted">';
        $value = $this->resolveValue($context->find('name'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '</span>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '    <div class="form-setting col-sm-9">
';
        $value = $context->find('error');
        $buffer .= $this->sectionBea80677ad0885a721d04660401556e5($context, $indent, $value);
        $buffer .= $indent . '        ';
        $value = $this->resolveValue($context->find('element'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';
        $value = $context->find('default');
        $buffer .= $this->sectionB74383387a20b43f64f472563e1ccc7e($context, $indent, $value);
        $buffer .= $indent . '        <div class="form-description mt-3">';
        $value = $this->resolveValue($context->find('description'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '</div>
';
        $buffer .= $indent . '        ';
        $value = $context->find('dependenton');
        $buffer .= $this->section8fa2fb993cb3b341b0b7fef1d81c2802($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';
        $value = $context->find('customcontrol');
        $buffer .= $this->section037c6a65b5f87aede11b7d8b37ec21a2($context, $indent, $value);

        return $buffer;
    }

    private function sectionFab12aa97bf6c114741b60cef513b51c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'id="{{labelfor}}_label"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'id="';
                $value = $this->resolveValue($context->find('labelfor'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '_label"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDdacf7fb733fa0ef79e908dd9d6b1566(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div class="alert alert-info">{{override}}</div>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div class="alert alert-info">';
                $value = $this->resolveValue($context->find('override'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEd8f8e664dc8c642b488c4edf7f112e4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <div class="alert alert-warning">{{warning}}</div>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <div class="alert alert-warning">';
                $value = $this->resolveValue($context->find('warning'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section02eed9586865ec182b35f966ca78dc26(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <p {{#labelfor}}id="{{labelfor}}_label"{{/labelfor}}>
                {{{title}}}
                {{#override}}
                    <div class="alert alert-info">{{override}}</div>
                {{/override}}
                {{#warning}}
                    <div class="alert alert-warning">{{warning}}</div>
                {{/warning}}
            </p>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <p ';
                $value = $context->find('labelfor');
                $buffer .= $this->sectionFab12aa97bf6c114741b60cef513b51c($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                ';
                $value = $this->resolveValue($context->find('title'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $value = $context->find('override');
                $buffer .= $this->sectionDdacf7fb733fa0ef79e908dd9d6b1566($context, $indent, $value);
                $value = $context->find('warning');
                $buffer .= $this->sectionEd8f8e664dc8c642b488c4edf7f112e4($context, $indent, $value);
                $buffer .= $indent . '            </p>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3d8f2ee95af1360bf9488b09a602ca85(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'for="{{labelfor}}"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'for="';
                $value = $this->resolveValue($context->find('labelfor'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBea80677ad0885a721d04660401556e5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div><span class="error">{{error}}</span></div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div><span class="error">';
                $value = $this->resolveValue($context->find('error'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE883efc355488afa28c6a1efa34031dd(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'text-ltr';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'text-ltr';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB74383387a20b43f64f472563e1ccc7e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div class="form-defaultinfo text-muted {{#forceltr}}text-ltr{{/forceltr}}">{{{default}}}</div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div class="form-defaultinfo text-muted ';
                $value = $context->find('forceltr');
                $buffer .= $this->sectionE883efc355488afa28c6a1efa34031dd($context, $indent, $value);
                $buffer .= '">';
                $value = $this->resolveValue($context->find('default'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8fa2fb993cb3b341b0b7fef1d81c2802(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '<div class="form-dependenton mb-4 text-muted">{{{.}}}</div>';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '<div class="form-dependenton mb-4 text-muted">';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</div>';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section553eaf7d423bfec4db0ee7f668120567(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        require([\'jquery\'], function($) {
            $(\'#{{id}}_label\').css(\'cursor\', \'default\');
            $(\'#{{id}}_label\').click(function() {
                $(\'#{{id}}\')
                    .find(\'button, a, input:not([type="hidden"]), select, textarea, [tabindex]\')
                    .filter(\':not([disabled]):not([tabindex="0"]):not([tabindex="-1"])\')
                    .first().focus();
            });
        });
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        require([\'jquery\'], function($) {
';
                $buffer .= $indent . '            $(\'#';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '_label\').css(\'cursor\', \'default\');
';
                $buffer .= $indent . '            $(\'#';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '_label\').click(function() {
';
                $buffer .= $indent . '                $(\'#';
                $value = $this->resolveValue($context->find('id'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '\')
';
                $buffer .= $indent . '                    .find(\'button, a, input:not([type="hidden"]), select, textarea, [tabindex]\')
';
                $buffer .= $indent . '                    .filter(\':not([disabled]):not([tabindex="0"]):not([tabindex="-1"])\')
';
                $buffer .= $indent . '                    .first().focus();
';
                $buffer .= $indent . '            });
';
                $buffer .= $indent . '        });
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section037c6a65b5f87aede11b7d8b37ec21a2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{#js}}
        require([\'jquery\'], function($) {
            $(\'#{{id}}_label\').css(\'cursor\', \'default\');
            $(\'#{{id}}_label\').click(function() {
                $(\'#{{id}}\')
                    .find(\'button, a, input:not([type="hidden"]), select, textarea, [tabindex]\')
                    .filter(\':not([disabled]):not([tabindex="0"]):not([tabindex="-1"])\')
                    .first().focus();
            });
        });
    {{/js}}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('js');
                $buffer .= $this->section553eaf7d423bfec4db0ee7f668120567($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
