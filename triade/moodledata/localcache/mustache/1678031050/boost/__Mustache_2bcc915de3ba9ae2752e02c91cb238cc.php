<?php

class __Mustache_2bcc915de3ba9ae2752e02c91cb238cc extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div data-region="view-selector" class="btn-group mb-1">
';
        $buffer .= $indent . '    <button type="button" class="btn btn-outline-secondary dropdown-toggle icon-no-margin" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section0fa205000ef28b6095fc51405a3bcb1e($context, $indent, $value);
        $buffer .= '" aria-controls="menusortby"
';
        $buffer .= $indent . '            title="';
        $value = $context->find('str');
        $buffer .= $this->section0fa205000ef28b6095fc51405a3bcb1e($context, $indent, $value);
        $buffer .= '" aria-describedby="timeline-view-selector-current-selection">
';
        $buffer .= $indent . '        <span id="timeline-view-selector-current-selection" data-active-item-text>
';
        $buffer .= $indent . '            ';
        $value = $context->find('sorttimelinecourses');
        $buffer .= $this->sectionDfa06877fd09c2cddf0a4f1dd0fc532b($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            ';
        $value = $context->find('sorttimelinedates');
        $buffer .= $this->sectionB00ffd9dad0df485052a361e3aca95d3($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '        </span>
';
        $buffer .= $indent . '    </button>
';
        $buffer .= $indent . '    <div id="menusortby" role="menu" class="dropdown-menu dropdown-menu-right list-group hidden" data-show-active-item data-skip-active-class="true">
';
        $buffer .= $indent . '        <a
';
        $buffer .= $indent . '            class="dropdown-item"
';
        $buffer .= $indent . '            href="#view_dates_';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '-';
        $value = $this->resolveValue($context->find('timelineinstanceid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '            data-toggle="tab"
';
        $buffer .= $indent . '            data-filtername="sortbydates"
';
        $buffer .= $indent . '            ';
        $value = $context->find('sorttimelinedates');
        $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->sectionB059cbc52d578d75eb17a5d7ee98c57e($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '            role="menuitem"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '            ';
        $value = $context->find('str');
        $buffer .= $this->section0e371d172f96b805892a6421f8a90d73($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '        <a
';
        $buffer .= $indent . '            class="dropdown-item"
';
        $buffer .= $indent . '            href="#view_courses_';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '-';
        $value = $this->resolveValue($context->find('timelineinstanceid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '            data-toggle="tab"
';
        $buffer .= $indent . '            data-filtername="sortbycourses"
';
        $buffer .= $indent . '            ';
        $value = $context->find('sorttimelinecourses');
        $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section03cebf862b1d4876fc56e14d75f8c3dc($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '            role="menuitem"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '            ';
        $value = $context->find('str');
        $buffer .= $this->sectionBfaaee379c81d3133b94a4d029246ae1($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section0fa205000ef28b6095fc51405a3bcb1e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariaviewselector, block_timeline';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariaviewselector, block_timeline';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5e6898f76f5e70b1d03b979cbea41cbf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' sortbycourses, block_timeline';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' sortbycourses, block_timeline';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDfa06877fd09c2cddf0a4f1dd0fc532b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}} sortbycourses, block_timeline{{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->section5e6898f76f5e70b1d03b979cbea41cbf($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0e371d172f96b805892a6421f8a90d73(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' sortbydates, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' sortbydates, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB00ffd9dad0df485052a361e3aca95d3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}} sortbydates, block_timeline {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->section0e371d172f96b805892a6421f8a90d73($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFc0c0b051caebb6243b5c2bd6d728967(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'aria-current="true"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'aria-current="true"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB059cbc52d578d75eb17a5d7ee98c57e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariaviewselectoroption, block_timeline, {{#str}} sortbydates, block_timeline {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariaviewselectoroption, block_timeline, ';
                $value = $context->find('str');
                $buffer .= $this->section0e371d172f96b805892a6421f8a90d73($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBfaaee379c81d3133b94a4d029246ae1(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' sortbycourses, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' sortbycourses, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section03cebf862b1d4876fc56e14d75f8c3dc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariaviewselectoroption, block_timeline, {{#str}} sortbycourses, block_timeline {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariaviewselectoroption, block_timeline, ';
                $value = $context->find('str');
                $buffer .= $this->sectionBfaaee379c81d3133b94a4d029246ae1($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
