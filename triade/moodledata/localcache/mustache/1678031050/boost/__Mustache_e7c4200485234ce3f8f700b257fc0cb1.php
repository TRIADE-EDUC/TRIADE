<?php

class __Mustache_e7c4200485234ce3f8f700b257fc0cb1 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div data-region="timeline-view">
';
        $buffer .= $indent . '    <div class="tab-content">
';
        $buffer .= $indent . '        <div class="tab-pane ';
        $value = $context->find('sorttimelinedates');
        $buffer .= $this->section394210065fc120f40f05534a4cb50829($context, $indent, $value);
        $buffer .= ' fade" data-limit="';
        $value = $this->resolveValue($context->find('limit'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" data-region="view-dates" id="view_dates_';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '-';
        $value = $this->resolveValue($context->find('timelineinstanceid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        if ($partial = $this->mustache->loadPartial('block_timeline/view-dates')) {
            $buffer .= $partial->renderInternal($context, $indent . '            ');
        }
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '        <div
';
        $buffer .= $indent . '            class="tab-pane ';
        $value = $context->find('sorttimelinecourses');
        $buffer .= $this->section394210065fc120f40f05534a4cb50829($context, $indent, $value);
        $buffer .= ' fade"
';
        $buffer .= $indent . '            data-region="view-courses"
';
        $buffer .= $indent . '            data-midnight="';
        $value = $this->resolveValue($context->find('midnight'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '            data-limit="2"
';
        $buffer .= $indent . '            data-offset="0"
';
        $buffer .= $indent . '            data-days-limit="';
        $value = $this->resolveValue($context->find('dayslimit'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '            data-days-offset="';
        $value = $this->resolveValue($context->find('daysoffset'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '            data-no-events-url="';
        $value = $this->resolveValue($context->findDot('urls.noevents'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '            ';
        $value = $context->find('overdue');
        $buffer .= $this->sectionCd1d6ff3a18ac8b2210e2e5bf3a62251($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            id="view_courses_';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '-';
        $value = $this->resolveValue($context->find('timelineinstanceid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '        >
';
        if ($partial = $this->mustache->loadPartial('block_timeline/view-courses')) {
            $buffer .= $partial->renderInternal($context, $indent . '            ');
        }
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>';

        return $buffer;
    }

    private function section394210065fc120f40f05534a4cb50829(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'active show';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'active show';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCd1d6ff3a18ac8b2210e2e5bf3a62251(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'data-filter-overdue="{{overdue}}"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'data-filter-overdue="';
                $value = $this->resolveValue($context->find('overdue'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
