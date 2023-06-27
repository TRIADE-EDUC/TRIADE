<?php

class __Mustache_7898a9a95724959cc1fd9c6f9f5f4e34 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div id="searchform-auto-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" class="form-inline simplesearchform">
';
        $buffer .= $indent . '    <div class="input-group searchbar w-100" role="search">
';
        $buffer .= $indent . '        <label for="searchinput-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        $buffer .= $indent . '            <span class="sr-only">';
        $blockFunction = $context->findInBlock('label');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $value = $context->find('str');
            $buffer .= $this->section9dbf37a0f544ff636ac78cdadec0a0c6($context, $indent, $value);
        }
        $buffer .= '</span>
';
        $buffer .= $indent . '        </label>
';
        $buffer .= $indent . '        <input
';
        $buffer .= $indent . '           type="text"
';
        $buffer .= $indent . '           role="searchbox"
';
        $buffer .= $indent . '           data-region="input"
';
        $buffer .= $indent . '           data-action="search"
';
        $buffer .= $indent . '           id="searchinput-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '           class="form-control withclear rounded"
';
        $buffer .= $indent . '           placeholder="';
        $blockFunction = $context->findInBlock('placeholder');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $value = $context->find('str');
            $buffer .= $this->section9dbf37a0f544ff636ac78cdadec0a0c6($context, $indent, $value);
        }
        $buffer .= '"
';
        $buffer .= $indent . '           name="search"
';
        $buffer .= $indent . '           value="';
        $blockFunction = $context->findInBlock('value');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        }
        $buffer .= '"
';
        $buffer .= $indent . '           autocomplete="off"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '        <button
';
        $buffer .= $indent . '            class="btn btn-clear d-none"
';
        $buffer .= $indent . '            data-action="clearsearch"
';
        $buffer .= $indent . '            type="button"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '           ';
        $value = $context->find('pix');
        $buffer .= $this->section01a1cb688132d57fc41f0070a1ef998d($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            <span class="sr-only">';
        $value = $context->find('str');
        $buffer .= $this->section40e9cf3a64788ffede70afd9139160c8($context, $indent, $value);
        $buffer .= '</span>
';
        $buffer .= $indent . '        </button>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section9dbf37a0f544ff636ac78cdadec0a0c6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' search, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' search, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section01a1cb688132d57fc41f0070a1ef998d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' e/cancel, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' e/cancel, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section40e9cf3a64788ffede70afd9139160c8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' clearsearch, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' clearsearch, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
