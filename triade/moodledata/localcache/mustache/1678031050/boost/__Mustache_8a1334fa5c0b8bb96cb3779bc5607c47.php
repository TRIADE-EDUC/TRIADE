<?php

class __Mustache_8a1334fa5c0b8bb96cb3779bc5607c47 extends Mustache_Template
{
    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $buffer = '';

        $buffer .= $indent . '<div id="recentlyaccesseditems-view-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '     data-region="recentlyaccesseditems-view"
';
        $buffer .= $indent . '     data-noitemsimgurl="';
        $value = $this->resolveValue($context->find('noitemsimgurl'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        $buffer .= $indent . '    <div data-region="recentlyaccesseditems-view-content">
';
        $buffer .= $indent . '        <div data-region="recentlyaccesseditems-loading-placeholder">
';
        $buffer .= $indent . '            <div class="card-deck dashboard-card-deck one-row">
';
        if ($partial = $this->mustache->loadPartial('block_recentlyaccesseditems/placeholder-item')) {
            $buffer .= $partial->renderInternal($context, $indent . '                ');
        }
        if ($partial = $this->mustache->loadPartial('block_recentlyaccesseditems/placeholder-item')) {
            $buffer .= $partial->renderInternal($context, $indent . '                ');
        }
        if ($partial = $this->mustache->loadPartial('block_recentlyaccesseditems/placeholder-item')) {
            $buffer .= $partial->renderInternal($context, $indent . '                ');
        }
        if ($partial = $this->mustache->loadPartial('block_recentlyaccesseditems/placeholder-item')) {
            $buffer .= $partial->renderInternal($context, $indent . '                ');
        }
        if ($partial = $this->mustache->loadPartial('block_recentlyaccesseditems/placeholder-item')) {
            $buffer .= $partial->renderInternal($context, $indent . '                ');
        }
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '
';

        return $buffer;
    }
}
