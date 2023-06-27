<?php

class __Mustache_d9666aabc26710ef39bf8759e43382b3 extends Mustache_Template
{
    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $buffer = '';

        $buffer .= $indent . '<span class="overlay-icon-container ';
        $blockFunction = $context->findInBlock('hiddenclass');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $value = $context->find('visible');
            if (empty($value)) {
                
                $buffer .= 'hidden';
            }
        }
        $buffer .= '" data-region="overlay-icon-container">
';
        if ($partial = $this->mustache->loadPartial('core/loading')) {
            $buffer .= $partial->renderInternal($context, $indent . '    ');
        }
        $buffer .= $indent . '</span>
';

        return $buffer;
    }
}
