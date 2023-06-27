<?php

class __Mustache_bdfd3381644595a138b36d41f1e0d235 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="w-100">
';
        $buffer .= $indent . '    ';
        if ($parent = $this->mustache->loadPartial('core/search_input_auto')) {
            $context->pushBlockContext(array(
                'label' => array($this, 'block79987dac42c3976ca6539c3fdce65ece'),
                'placeholder' => array($this, 'block79987dac42c3976ca6539c3fdce65ece'),
            ));
            $buffer .= $parent->renderInternal($context, $indent);
            $context->popBlockContext();
        }
        $buffer .= '</div>
';

        return $buffer;
    }

    private function section3758675655fa466b507460be07eca5cb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            searchevents, block_timeline
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '
';
                $buffer .= $indent . '            searchevents, block_timeline
';
                $buffer .= $indent . '        ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    public function block79987dac42c3976ca6539c3fdce65ece($context)
    {
        $indent = $buffer = '';
        $value = $context->find('str');
        $buffer .= $this->section3758675655fa466b507460be07eca5cb($context, $indent, $value);
    
        return $buffer;
    }
}
