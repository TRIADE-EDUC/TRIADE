<?php

class __Mustache_a5c341d581b0160862369f797be736ca extends Mustache_Template
{
    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $buffer = '';

        $buffer .= $indent . '<div class="card dashboard-card mb-1">
';
        $buffer .= $indent . '    <div class="card-body p-2 m-1">
';
        $buffer .= $indent . '        <div class="d-flex flex-row mw-100 align-items-center">
';
        $buffer .= $indent . '            <div class="bg-pulse-grey rounded-circle" style="height: 40px; width: 40px;"></div>
';
        $buffer .= $indent . '            <div class="pl-2" style="flex: 1;">
';
        $buffer .= $indent . '                <div class="bg-pulse-grey w-100" style="height: 1rem;"></div>
';
        $buffer .= $indent . '                <div class="bg-pulse-grey w-75 mt-1" style="height: 0.8rem;"></div>
';
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>';

        return $buffer;
    }
}
