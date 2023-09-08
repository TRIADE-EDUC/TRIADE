<?php

class __Mustache_1e8e4e124098f012ce35c8eb1d7c6582 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->findDot('urls.noevents');
        $buffer .= $this->section324d850bcbc000d654bf9ba5ab609100($context, $indent, $value);

        return $buffer;
    }

    private function section0da079e8ecded1d3dc97887fb486b7eb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' noevents, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' noevents, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section324d850bcbc000d654bf9ba5ab609100(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
<div class="hidden text-xs-center text-center mt-3" data-region="no-events-empty-message">
    <img
        src="{{urls.noevents}}"
        alt=""
        style="height: 70px; width: 70px"
    >
    <p class="text-muted mt-1">{{#str}} noevents, block_timeline {{/str}}</p>
</div>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '<div class="hidden text-xs-center text-center mt-3" data-region="no-events-empty-message">
';
                $buffer .= $indent . '    <img
';
                $buffer .= $indent . '        src="';
                $value = $this->resolveValue($context->findDot('urls.noevents'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '        alt=""
';
                $buffer .= $indent . '        style="height: 70px; width: 70px"
';
                $buffer .= $indent . '    >
';
                $buffer .= $indent . '    <p class="text-muted mt-1">';
                $value = $context->find('str');
                $buffer .= $this->section0da079e8ecded1d3dc97887fb486b7eb($context, $indent, $value);
                $buffer .= '</p>
';
                $buffer .= $indent . '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
