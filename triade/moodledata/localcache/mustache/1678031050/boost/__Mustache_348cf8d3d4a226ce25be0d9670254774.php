<?php

class __Mustache_348cf8d3d4a226ce25be0d9670254774 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '
';
        $buffer .= $indent . '<div id="block-timeline-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '-';
        $value = $this->resolveValue($context->find('timelineinstanceid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" class="block-timeline" data-region="timeline">
';
        $buffer .= $indent . '    <div class="p-0 px-2">
';
        $buffer .= $indent . '        <div class="row no-gutters">
';
        $buffer .= $indent . '            <div class="mr-2 mb-1">
';
        if ($partial = $this->mustache->loadPartial('block_timeline/nav-day-filter')) {
            $buffer .= $partial->renderInternal($context, $indent . '                ');
        }
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '            <div class="mr-auto mb-1">
';
        if ($partial = $this->mustache->loadPartial('block_timeline/nav-view-selector')) {
            $buffer .= $partial->renderInternal($context, $indent . '                ');
        }
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '            <div class="col-md-6 col-sm-8 col-12 mb-1 d-flex justify-content-end nav-search">
';
        if ($partial = $this->mustache->loadPartial('block_timeline/nav-search')) {
            $buffer .= $partial->renderInternal($context, $indent . '                ');
        }
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '        <div class="pb-3 px-2 border-bottom"></div>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '    <div class="p-0">
';
        if ($partial = $this->mustache->loadPartial('block_timeline/view')) {
            $buffer .= $partial->renderInternal($context, $indent . '        ');
        }
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';
        $value = $context->find('js');
        $buffer .= $this->section90886d711e1fcb8c634cb0da1272feab($context, $indent, $value);

        return $buffer;
    }

    private function section90886d711e1fcb8c634cb0da1272feab(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
require(
[
    \'jquery\',
    \'block_timeline/main\',
],
function(
    $,
    Main
) {
    var root = $(\'#block-timeline-{{uniqid}}-{{timelineinstanceid}}\');
    Main.init(root);
});
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'require(
';
                $buffer .= $indent . '[
';
                $buffer .= $indent . '    \'jquery\',
';
                $buffer .= $indent . '    \'block_timeline/main\',
';
                $buffer .= $indent . '],
';
                $buffer .= $indent . 'function(
';
                $buffer .= $indent . '    $,
';
                $buffer .= $indent . '    Main
';
                $buffer .= $indent . ') {
';
                $buffer .= $indent . '    var root = $(\'#block-timeline-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-';
                $value = $this->resolveValue($context->find('timelineinstanceid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '\');
';
                $buffer .= $indent . '    Main.init(root);
';
                $buffer .= $indent . '});
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
