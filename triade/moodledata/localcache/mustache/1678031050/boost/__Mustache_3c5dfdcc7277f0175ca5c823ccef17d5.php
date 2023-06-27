<?php

class __Mustache_3c5dfdcc7277f0175ca5c823ccef17d5 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="bottom">
';
        $value = $context->find('footerlinks');
        $buffer .= $this->section86d9b454c9b29b6707f1b9f6016e3aa8($context, $indent, $value);
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section86d9b454c9b29b6707f1b9f6016e3aa8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <span class="footer-link">
            <a href="{{url}}">{{linkname}}</a>
        </span>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <span class="footer-link">
';
                $buffer .= $indent . '            <a href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('linkname'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $buffer .= $indent . '        </span>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
