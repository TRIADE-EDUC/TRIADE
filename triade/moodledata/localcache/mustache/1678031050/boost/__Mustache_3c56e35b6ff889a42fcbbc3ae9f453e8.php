<?php

class __Mustache_3c56e35b6ff889a42fcbbc3ae9f453e8 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('defaulteventcontext');
        $buffer .= $this->section3fb989a492dcebe67cf2696e44c9c59b($context, $indent, $value);

        return $buffer;
    }

    private function section17f3d1ab55887b273f62f9f7fe44e71b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'newevent, core_calendar';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'newevent, core_calendar';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3fb989a492dcebe67cf2696e44c9c59b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
<button{{!
    }} class="btn btn-primary float-sm-right float-right"{{!
    }} data-context-id="{{defaulteventcontext}}"{{!
    }} data-action="new-event-button"{{!
    }}>
    {{#str}}newevent, core_calendar{{/str}}
</button>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '<button';
                $buffer .= ' class="btn btn-primary float-sm-right float-right"';
                $buffer .= ' data-context-id="';
                $value = $this->resolveValue($context->find('defaulteventcontext'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $buffer .= ' data-action="new-event-button"';
                $buffer .= '>
';
                $buffer .= $indent . '    ';
                $value = $context->find('str');
                $buffer .= $this->section17f3d1ab55887b273f62f9f7fe44e71b($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '</button>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
