<?php

class __Mustache_5cbfc4a2fb90983646044899179eddd1 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('prefix');
        $buffer .= $this->section7e518caad6d2508f3d4ab9b7a186f310($context, $indent, $value);
        $value = $this->resolveValue($context->find('subject'), $context);
        $buffer .= ($value === null ? '' : $value);
        $buffer .= '
';

        return $buffer;
    }

    private function section7e518caad6d2508f3d4ab9b7a186f310(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{{prefix}}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('prefix'), $context);
                $buffer .= $indent . ($value === null ? '' : $value);
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
