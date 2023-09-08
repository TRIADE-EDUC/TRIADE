<?php

class __Mustache_f8ea3b5aa56530c43bc37958d1f98eb4 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="fp-def-search form-group">
';
        $buffer .= $indent . '    <label class="sr-only" for="reposearch">';
        $value = $context->find('str');
        $buffer .= $this->section838c131eb303fc7142fc88ee7e5ab99d($context, $indent, $value);
        $buffer .= '</label>
';
        $buffer .= $indent . '    <input type="search" class="form-control" id="reposearch" name="s" placeholder="';
        $value = $context->find('str');
        $buffer .= $this->section367f09614bfd8b218f547713d89473bf($context, $indent, $value);
        $buffer .= '"/>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section838c131eb303fc7142fc88ee7e5ab99d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'searchrepo, repository';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'searchrepo, repository';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section367f09614bfd8b218f547713d89473bf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'search, repository';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'search, repository';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
