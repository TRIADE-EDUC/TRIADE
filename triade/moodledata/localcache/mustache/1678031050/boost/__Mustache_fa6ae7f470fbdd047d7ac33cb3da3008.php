<?php

class __Mustache_fa6ae7f470fbdd047d7ac33cb3da3008 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '
';
        $value = $context->find('nopresets');
        $buffer .= $this->section347e83e07b5934815232ab85fb716bdb($context, $indent, $value);
        $value = $context->find('nopresets');
        if (empty($value)) {
            
            $buffer .= $indent . '    <table class="generaltable boxaligncenter mb-5">
';
            $buffer .= $indent . '        <caption class="accesshide">';
            $value = $context->find('str');
            $buffer .= $this->sectionA58793d130d3bfab89aea5956fa24e9d($context, $indent, $value);
            $buffer .= '</caption>
';
            $buffer .= $indent . '        <thead>
';
            $buffer .= $indent . '            <tr>
';
            $buffer .= $indent . '                <th scope="col">';
            $value = $context->find('str');
            $buffer .= $this->section8111cbf56118aa58f390da519737a980($context, $indent, $value);
            $buffer .= '</th>
';
            $buffer .= $indent . '                <th scope="col">';
            $value = $context->find('str');
            $buffer .= $this->section1dee8adaf405bfc6db6cbb048fef1653($context, $indent, $value);
            $buffer .= '</th>
';
            $value = $context->find('showactions');
            if (empty($value)) {
                
                $buffer .= $indent . '                    <th scope="col">';
                $value = $context->find('str');
                $buffer .= $this->section01c79a9329b86b1f1dd913405d73d1a2($context, $indent, $value);
                $buffer .= '</th>
';
                $buffer .= $indent . '                    <th scope="col">';
                $value = $context->find('str');
                $buffer .= $this->section4922acf57b8aafb350daad98d78415f9($context, $indent, $value);
                $buffer .= '</th>
';
                $buffer .= $indent . '                    <th scope="col">';
                $value = $context->find('str');
                $buffer .= $this->section102f5ac104c0f1abe5e6debf3c2437c8($context, $indent, $value);
                $buffer .= '</th>
';
                $buffer .= $indent . '                    <th scope="col">';
                $value = $context->find('str');
                $buffer .= $this->section79d3d09eefe1e6e6d4f4c8eb7464cc8a($context, $indent, $value);
                $buffer .= '</th>
';
                $buffer .= $indent . '                    <th scope="col">';
                $value = $context->find('str');
                $buffer .= $this->sectionA1224c14d1380ebaa6fc85aa74bb91d2($context, $indent, $value);
                $buffer .= '</th>
';
            }
            $value = $context->find('showactions');
            $buffer .= $this->sectionF22c6e4a20f5142b2f5c166f8d34b111($context, $indent, $value);
            $buffer .= $indent . '            </tr>
';
            $buffer .= $indent . '        </thead>
';
            $buffer .= $indent . '        <tbody>
';
            $value = $context->find('presets');
            $buffer .= $this->section60f70dbc75c88de543570d86752039c9($context, $indent, $value);
            $buffer .= $indent . '        </tbody>
';
            $buffer .= $indent . '    </table>
';
        }

        return $buffer;
    }

    private function sectionA80c485c6c59b176210aa301cb92c5d6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'nopresets, tool_admin_presets';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'nopresets, tool_admin_presets';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section347e83e07b5934815232ab85fb716bdb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <div id="id_nopresets" class="box py-3 generalbox">
        <p id="{{uniqid}}">{{#str}}nopresets, tool_admin_presets{{/str}}</p>
    </div>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <div id="id_nopresets" class="box py-3 generalbox">
';
                $buffer .= $indent . '        <p id="';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $context->find('str');
                $buffer .= $this->sectionA80c485c6c59b176210aa301cb92c5d6($context, $indent, $value);
                $buffer .= '</p>
';
                $buffer .= $indent . '    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA58793d130d3bfab89aea5956fa24e9d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'presetslisttable, tool_admin_presets';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'presetslisttable, tool_admin_presets';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8111cbf56118aa58f390da519737a980(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'name';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'name';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1dee8adaf405bfc6db6cbb048fef1653(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'description';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'description';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section01c79a9329b86b1f1dd913405d73d1a2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'presetmoodlerelease, tool_admin_presets';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'presetmoodlerelease, tool_admin_presets';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4922acf57b8aafb350daad98d78415f9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'author, tool_admin_presets';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'author, tool_admin_presets';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section102f5ac104c0f1abe5e6debf3c2437c8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'site, tool_admin_presets';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'site, tool_admin_presets';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section79d3d09eefe1e6e6d4f4c8eb7464cc8a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'created, tool_admin_presets';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'created, tool_admin_presets';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA1224c14d1380ebaa6fc85aa74bb91d2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'imported, tool_admin_presets';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'imported, tool_admin_presets';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section2ad2fdcc9b858451e82e60edfcdcf48d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'actions';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'actions';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF22c6e4a20f5142b2f5c166f8d34b111(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <th scope="col" aria-label="{{#str}}actions{{/str}}"></th>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <th scope="col" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->section2ad2fdcc9b858451e82e60edfcdcf48d($context, $indent, $value);
                $buffer .= '"></th>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA8de664bdbde1877bb3f2991e26edf72(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{> core/action_menu}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/action_menu')) {
                    $buffer .= $partial->renderInternal($context);
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD738d1775d27ec51036f45172b39a66b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                        <td>
                            {{#actions}}{{> core/action_menu}}{{/actions}}
                        </td>
                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                        <td>
';
                $buffer .= $indent . '                            ';
                $value = $context->find('actions');
                $buffer .= $this->sectionA8de664bdbde1877bb3f2991e26edf72($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                        </td>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section60f70dbc75c88de543570d86752039c9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <tr>
                    <td>{{{name}}}</td>
                    <td>{{{description}}}</td>
                    {{^showactions}}
                        <td>{{{release}}}</td>
                        <td>{{{author}}}</td>
                        <td>{{{site}}}</td>
                        <td>{{{timecreated}}}</td>
                        <td>{{{timeimported}}}</td>
                    {{/showactions}}
                    {{#showactions}}
                        <td>
                            {{#actions}}{{> core/action_menu}}{{/actions}}
                        </td>
                    {{/showactions}}
                </tr>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <tr>
';
                $buffer .= $indent . '                    <td>';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</td>
';
                $buffer .= $indent . '                    <td>';
                $value = $this->resolveValue($context->find('description'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '</td>
';
                $value = $context->find('showactions');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                        <td>';
                    $value = $this->resolveValue($context->find('release'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '</td>
';
                    $buffer .= $indent . '                        <td>';
                    $value = $this->resolveValue($context->find('author'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '</td>
';
                    $buffer .= $indent . '                        <td>';
                    $value = $this->resolveValue($context->find('site'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '</td>
';
                    $buffer .= $indent . '                        <td>';
                    $value = $this->resolveValue($context->find('timecreated'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '</td>
';
                    $buffer .= $indent . '                        <td>';
                    $value = $this->resolveValue($context->find('timeimported'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '</td>
';
                }
                $value = $context->find('showactions');
                $buffer .= $this->sectionD738d1775d27ec51036f45172b39a66b($context, $indent, $value);
                $buffer .= $indent . '                </tr>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
