<?php

class __Mustache_786d89934e249435b87ba613ccf0b260 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="form-textarea">
';
        $buffer .= $indent . '    <textarea ';
        $value = $context->find('readonly');
        $buffer .= $this->sectionC87198b06c7ce18b3aeed72afb34afb9($context, $indent, $value);
        $buffer .= ' rows="';
        $value = $this->resolveValue($context->find('rows'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" cols="';
        $value = $this->resolveValue($context->find('cols'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" id="';
        $value = $this->resolveValue($context->find('id'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" name="';
        $value = $this->resolveValue($context->find('name'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" spellcheck="true" class="form-control ';
        $value = $context->find('forceltr');
        $buffer .= $this->sectionE883efc355488afa28c6a1efa34031dd($context, $indent, $value);
        $buffer .= '">';
        $value = $this->resolveValue($context->find('value'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '</textarea>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function sectionC87198b06c7ce18b3aeed72afb34afb9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'disabled';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'disabled';
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

}
