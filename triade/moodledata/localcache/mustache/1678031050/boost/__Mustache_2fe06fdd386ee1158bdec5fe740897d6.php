<?php

class __Mustache_2fe06fdd386ee1158bdec5fe740897d6 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('hascourses');
        if (empty($value)) {
            
            $buffer .= $indent . '    <div class="text-xs-center text-center mt-3" data-region="no-courses-empty-message">
';
            $buffer .= $indent . '        <img
';
            $buffer .= $indent . '            src="';
            $value = $this->resolveValue($context->findDot('urls.noevents'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
            $buffer .= '"
';
            $buffer .= $indent . '            alt=""
';
            $buffer .= $indent . '            style="height: 70px; width: 70px"
';
            $buffer .= $indent . '        >
';
            $buffer .= $indent . '        <p class="text-muted mt-1">';
            $value = $context->find('str');
            $buffer .= $this->section8ac1faa3b604bd216bd6406aef5a4809($context, $indent, $value);
            $buffer .= '</p>
';
            $buffer .= $indent . '    </div>
';
        }

        return $buffer;
    }

    private function section8ac1faa3b604bd216bd6406aef5a4809(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' nocoursesinprogress, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' nocoursesinprogress, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
