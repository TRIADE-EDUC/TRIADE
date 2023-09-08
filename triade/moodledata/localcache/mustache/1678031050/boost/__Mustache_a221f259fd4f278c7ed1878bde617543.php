<?php

class __Mustache_a221f259fd4f278c7ed1878bde617543 extends Mustache_Template
{
    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $buffer = '';

        $buffer .= $indent . '<div data-region="timeline-view-dates">
';
        if ($partial = $this->mustache->loadPartial('block_timeline/event-list')) {
            $buffer .= $partial->renderInternal($context, $indent . '    ');
        }
        $buffer .= $indent . '</div>
';

        return $buffer;
    }
}
