<?php

class __Mustache_72705532f7bb883d264c5554175f44c2 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="mb-1 mr-1 flex-grow-1">
';
        $buffer .= $indent . '    ';
        if ($parent = $this->mustache->loadPartial('core/search_input_auto')) {
            $context->pushBlockContext(array(
                'label' => array($this, 'block4ad3a13a1e13e15f7c2e6e666ab045d7'),
                'placeholder' => array($this, 'blockA0522b8194af85963120bd4979f0f5f0'),
            ));
            $buffer .= $parent->renderInternal($context, $indent);
            $context->popBlockContext();
        }
        $buffer .= '</div>
';

        return $buffer;
    }

    private function section0f8c6c9c1084976a5813ad5164199beb(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            searchcourses, block_myoverview
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '
';
                $buffer .= $indent . '            searchcourses, block_myoverview
';
                $buffer .= $indent . '        ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section36ae90f5e80d01daed79f59c00e6de4d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            search, core
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= '
';
                $buffer .= $indent . '            search, core
';
                $buffer .= $indent . '        ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    public function block4ad3a13a1e13e15f7c2e6e666ab045d7($context)
    {
        $indent = $buffer = '';
        $value = $context->find('str');
        $buffer .= $this->section0f8c6c9c1084976a5813ad5164199beb($context, $indent, $value);
    
        return $buffer;
    }

    public function blockA0522b8194af85963120bd4979f0f5f0($context)
    {
        $indent = $buffer = '';
        $value = $context->find('str');
        $buffer .= $this->section36ae90f5e80d01daed79f59c00e6de4d($context, $indent, $value);
    
        return $buffer;
    }
}
