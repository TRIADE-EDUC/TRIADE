<?php

class __Mustache_a4c60843c2015c14bf24b315f9ba870a extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div';
        $buffer .= ' class="calendarwrapper"';
        $value = $context->find('courseid');
        $buffer .= $this->section4f3e547ac50707b30ffc2cec5e028045($context, $indent, $value);
        $value = $context->find('categoryid');
        $buffer .= $this->sectionA00eaaaf78214a10bae0a4f10432dae0($context, $indent, $value);
        $buffer .= ' data-context-id="';
        $value = $this->resolveValue($context->find('defaulteventcontext'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= ' data-month="';
        $value = $this->resolveValue($context->findDot('date.mon'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= ' data-year="';
        $value = $this->resolveValue($context->findDot('date.year'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= ' data-view="';
        $value = $this->resolveValue($context->find('view'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"';
        $buffer .= '>
';
        if ($partial = $this->mustache->loadPartial('core_calendar/month_navigation')) {
            $buffer .= $partial->renderInternal($context, $indent . '    ');
        }
        if ($partial = $this->mustache->loadPartial('core/overlay_loading')) {
            $buffer .= $partial->renderInternal($context, $indent . '    ');
        }
        $buffer .= $indent . '    <table id="month-detailed-';
        $value = $this->resolveValue($context->find('uniqid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '-';
        $value = $this->resolveValue($context->find('calendarinstanceid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" class="calendarmonth calendartable mb-0">
';
        $buffer .= $indent . '        <thead>
';
        $buffer .= $indent . '            <tr>
';
        $value = $context->find('daynames');
        $buffer .= $this->section7b7c62a3b4a44264ae1437e113a8c3bc($context, $indent, $value);
        $buffer .= $indent . '            </tr>
';
        $buffer .= $indent . '        </thead>
';
        $buffer .= $indent . '        <tbody>
';
        $value = $context->find('weeks');
        $buffer .= $this->sectionB3e10f42ada76b9c7d5cd4d51cb4b3df($context, $indent, $value);
        $buffer .= $indent . '        </tbody>
';
        $buffer .= $indent . '    </table>
';
        $buffer .= $indent . '</div>
';
        $value = $context->find('js');
        $buffer .= $this->sectionB56e1972cadb534bbb2d637a549766db($context, $indent, $value);

        return $buffer;
    }

    private function section4f3e547ac50707b30ffc2cec5e028045(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' data-courseid="{{courseid}}"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' data-courseid="';
                $value = $this->resolveValue($context->find('courseid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA00eaaaf78214a10bae0a4f10432dae0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' data-categoryid="{{categoryid}}"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' data-categoryid="';
                $value = $this->resolveValue($context->find('categoryid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7b7c62a3b4a44264ae1437e113a8c3bc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <th class="header text-xs-center">
                    <span class="sr-only">{{fullname}}</span>
                    <span aria-hidden="true">{{shortname}}</span>
                </th>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <th class="header text-xs-center">
';
                $buffer .= $indent . '                    <span class="sr-only">';
                $value = $this->resolveValue($context->find('fullname'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span>
';
                $buffer .= $indent . '                    <span aria-hidden="true">';
                $value = $this->resolveValue($context->find('shortname'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span>
';
                $buffer .= $indent . '                </th>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section30ad4cd62bbcce6b32c5d2ea63ba3dca(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <td class="dayblank">&nbsp;</td>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <td class="dayblank">&nbsp;</td>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCce61e3fee851e8b66112494c048a58a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' today';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' today';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section84f949a8191b4daaac38d5ac1997cd7a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' weekend';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' weekend';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8aa9feca8669cbbfdc6a37548f193ebe(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' duration';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' duration';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC090550ce065bf0ee1844d93a2f5c5bc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' duration_{{.}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' duration_';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA7cafb8496be54e32a81ab964df17ff2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' clickable';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' clickable';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE28f621eb042f18284779ecc71990758(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' hasevent';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' hasevent';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA4b17465b30f8e7b0573e098b81f0e55(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' today, core_calendar ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' today, core_calendar ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0376e0cfa88d71c021de2af759f6b3da(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' title="{{#str}} today, core_calendar {{/str}}"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' title="';
                $value = $context->find('str');
                $buffer .= $this->sectionA4b17465b30f8e7b0573e098b81f0e55($context, $indent, $value);
                $buffer .= '"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section319181b8b4154e3dfe673ffd2a6e2de2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' data-title="{{viewdaylinktitle}}"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' data-title="';
                $value = $this->resolveValue($context->find('viewdaylinktitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6d8762f9bad5b0242d71000030af73f6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <a data-action="view-day-link" href="#" class="aalink day" aria-label="{{viewdaylinktitle}}"
                                    data-year="{{date.year}}" data-month="{{date.mon}}" data-day="{{mday}}"
                                    data-courseid="{{courseid}}" data-categoryid="{{categoryid}}"
                                    data-timestamp="{{timestamp}}"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></a>
                            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <a data-action="view-day-link" href="#" class="aalink day" aria-label="';
                $value = $this->resolveValue($context->find('viewdaylinktitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                                    data-year="';
                $value = $this->resolveValue($context->findDot('date.year'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-month="';
                $value = $this->resolveValue($context->findDot('date.mon'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-day="';
                $value = $this->resolveValue($context->find('mday'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                                    data-courseid="';
                $value = $this->resolveValue($context->find('courseid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-categoryid="';
                $value = $this->resolveValue($context->find('categoryid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                                    data-timestamp="';
                $value = $this->resolveValue($context->find('timestamp'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"><span class="day-number-circle"><span class="day-number">';
                $value = $this->resolveValue($context->find('mday'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span></span></a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5fbeee9f6499251ea8344b9dfcf5798d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                            <li class="events-underway">[{{{name}}}]</li>
                                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                            <li class="events-underway">[';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= ']</li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section50364c4284f19fd772672c1b38d7b5be(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                                        data-min-day-timestamp="{{.}}"
                                                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                                        data-min-day-timestamp="';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section28335d6b4cbbabf1beb666c83b45ead6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                                        data-min-day-error="{{.}}"
                                                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                                        data-min-day-error="';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE95f9f64e0794353d8c18c2b16a3cae5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                                        data-max-day-timestamp="{{.}}"
                                                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                                        data-max-day-timestamp="';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCb2bf7bee155864e3d1b641ba49040c8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                                        data-max-day-error="{{.}}"
                                                    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                                        data-max-day-error="';
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFb869f17130f2d782b4c7cad05208853(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                                    draggable="true"
                                                    data-drag-type="move"
                                                    {{#mindaytimestamp}}
                                                        data-min-day-timestamp="{{.}}"
                                                    {{/mindaytimestamp}}
                                                    {{#mindayerror}}
                                                        data-min-day-error="{{.}}"
                                                    {{/mindayerror}}
                                                    {{#maxdaytimestamp}}
                                                        data-max-day-timestamp="{{.}}"
                                                    {{/maxdaytimestamp}}
                                                    {{#maxdayerror}}
                                                        data-max-day-error="{{.}}"
                                                    {{/maxdayerror}}
                                                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                                    draggable="true"
';
                $buffer .= $indent . '                                                    data-drag-type="move"
';
                $value = $context->find('mindaytimestamp');
                $buffer .= $this->section50364c4284f19fd772672c1b38d7b5be($context, $indent, $value);
                $value = $context->find('mindayerror');
                $buffer .= $this->section28335d6b4cbbabf1beb666c83b45ead6($context, $indent, $value);
                $value = $context->find('maxdaytimestamp');
                $buffer .= $this->sectionE95f9f64e0794353d8c18c2b16a3cae5($context, $indent, $value);
                $value = $context->find('maxdayerror');
                $buffer .= $this->sectionCb2bf7bee155864e3d1b641ba49040c8($context, $indent, $value);
                $buffer .= $indent . '                                                ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE840a517716fe28e6c24699413117467(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                        {{#underway}}
                                            <li class="events-underway">[{{{name}}}]</li>
                                        {{/underway}}
                                        {{^underway}}
                                            <li data-region="event-item"
                                                data-event-component="{{component}}"
                                                data-event-eventtype="{{eventtype}}"
                                                data-event-folded="false"
                                                data-eventtype-{{normalisedeventtype}}="1"
                                                {{#draggable}}
                                                    draggable="true"
                                                    data-drag-type="move"
                                                    {{#mindaytimestamp}}
                                                        data-min-day-timestamp="{{.}}"
                                                    {{/mindaytimestamp}}
                                                    {{#mindayerror}}
                                                        data-min-day-error="{{.}}"
                                                    {{/mindayerror}}
                                                    {{#maxdaytimestamp}}
                                                        data-max-day-timestamp="{{.}}"
                                                    {{/maxdaytimestamp}}
                                                    {{#maxdayerror}}
                                                        data-max-day-error="{{.}}"
                                                    {{/maxdayerror}}
                                                {{/draggable}}>

                                                <a data-action="view-event" data-event-id="{{id}}" href="{{url}}" title="{{name}}">
                                                    <span class="calendar-circle calendar_event_{{normalisedeventtype}}">
                                                        &nbsp;
                                                    </span>
                                                    <span class="eventname">{{{name}}}</span>
                                                </a>
                                            </li>
                                        {{/underway}}
                                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('underway');
                $buffer .= $this->section5fbeee9f6499251ea8344b9dfcf5798d($context, $indent, $value);
                $value = $context->find('underway');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                                            <li data-region="event-item"
';
                    $buffer .= $indent . '                                                data-event-component="';
                    $value = $this->resolveValue($context->find('component'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '"
';
                    $buffer .= $indent . '                                                data-event-eventtype="';
                    $value = $this->resolveValue($context->find('eventtype'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '"
';
                    $buffer .= $indent . '                                                data-event-folded="false"
';
                    $buffer .= $indent . '                                                data-eventtype-';
                    $value = $this->resolveValue($context->find('normalisedeventtype'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '="1"
';
                    $value = $context->find('draggable');
                    $buffer .= $this->sectionFb869f17130f2d782b4c7cad05208853($context, $indent, $value);
                    $buffer .= '>
';
                    $buffer .= $indent . '
';
                    $buffer .= $indent . '                                                <a data-action="view-event" data-event-id="';
                    $value = $this->resolveValue($context->find('id'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '" href="';
                    $value = $this->resolveValue($context->find('url'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '" title="';
                    $value = $this->resolveValue($context->find('name'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '">
';
                    $buffer .= $indent . '                                                    <span class="calendar-circle calendar_event_';
                    $value = $this->resolveValue($context->find('normalisedeventtype'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '">
';
                    $buffer .= $indent . '                                                        &nbsp;
';
                    $buffer .= $indent . '                                                    </span>
';
                    $buffer .= $indent . '                                                    <span class="eventname">';
                    $value = $this->resolveValue($context->find('name'), $context);
                    $buffer .= ($value === null ? '' : $value);
                    $buffer .= '</span>
';
                    $buffer .= $indent . '                                                </a>
';
                    $buffer .= $indent . '                                            </li>
';
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section52bc13cb08d8b11281e5355488a15bad(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' moreevents, calendar, {{hasmoreevents}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' moreevents, calendar, ';
                $value = $this->resolveValue($context->find('hasmoreevents'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6d305541516b4b8b076461474b174727(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                            <div data-popover-eventtype-{{normalisedeventtype}}="1">
                                                {{> core_calendar/event_icon}}
                                                {{{name}}}
                                            </div>
                                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                            <div data-popover-eventtype-';
                $value = $this->resolveValue($context->find('normalisedeventtype'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '="1">
';
                if ($partial = $this->mustache->loadPartial('core_calendar/event_icon')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                                                ');
                }
                $buffer .= $indent . '                                                ';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '                                            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB3fa2ccb77b79eb31a810786cbc57d86(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <div data-region="day-content">
                                    <ul>
                                        {{#events}}
                                        {{#underway}}
                                            <li class="events-underway">[{{{name}}}]</li>
                                        {{/underway}}
                                        {{^underway}}
                                            <li data-region="event-item"
                                                data-event-component="{{component}}"
                                                data-event-eventtype="{{eventtype}}"
                                                data-event-folded="false"
                                                data-eventtype-{{normalisedeventtype}}="1"
                                                {{#draggable}}
                                                    draggable="true"
                                                    data-drag-type="move"
                                                    {{#mindaytimestamp}}
                                                        data-min-day-timestamp="{{.}}"
                                                    {{/mindaytimestamp}}
                                                    {{#mindayerror}}
                                                        data-min-day-error="{{.}}"
                                                    {{/mindayerror}}
                                                    {{#maxdaytimestamp}}
                                                        data-max-day-timestamp="{{.}}"
                                                    {{/maxdaytimestamp}}
                                                    {{#maxdayerror}}
                                                        data-max-day-error="{{.}}"
                                                    {{/maxdayerror}}
                                                {{/draggable}}>

                                                <a data-action="view-event" data-event-id="{{id}}" href="{{url}}" title="{{name}}">
                                                    <span class="calendar-circle calendar_event_{{normalisedeventtype}}">
                                                        &nbsp;
                                                    </span>
                                                    <span class="eventname">{{{name}}}</span>
                                                </a>
                                            </li>
                                        {{/underway}}
                                        {{/events}}
                                        <li data-action="view-more-events" style="display: none">
                                            <strong>
                                                <a data-action="view-day-link" href="#" class="aalink day" aria-label="{{viewdaylinktitle}}"
                                                   data-year="{{date.year}}" data-month="{{date.mon}}" data-day="{{mday}}"
                                                   data-courseid="{{courseid}}" data-categoryid="{{categoryid}}"
                                                   data-timestamp="{{timestamp}}">
                                                   {{#str}} moreevents, calendar, {{hasmoreevents}} {{/str}}
                                                </a>
                                            </strong>
                                        </li>
                                    </ul>
                                    <div class="hidden">
                                        {{#events}}
                                            <div data-popover-eventtype-{{normalisedeventtype}}="1">
                                                {{> core_calendar/event_icon}}
                                                {{{name}}}
                                            </div>
                                        {{/events}}
                                    </div>
                                </div>
                            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <div data-region="day-content">
';
                $buffer .= $indent . '                                    <ul>
';
                $value = $context->find('events');
                $buffer .= $this->sectionE840a517716fe28e6c24699413117467($context, $indent, $value);
                $buffer .= $indent . '                                        <li data-action="view-more-events" style="display: none">
';
                $buffer .= $indent . '                                            <strong>
';
                $buffer .= $indent . '                                                <a data-action="view-day-link" href="#" class="aalink day" aria-label="';
                $value = $this->resolveValue($context->find('viewdaylinktitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                                                   data-year="';
                $value = $this->resolveValue($context->findDot('date.year'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-month="';
                $value = $this->resolveValue($context->findDot('date.mon'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-day="';
                $value = $this->resolveValue($context->find('mday'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                                                   data-courseid="';
                $value = $this->resolveValue($context->find('courseid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-categoryid="';
                $value = $this->resolveValue($context->find('categoryid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                                                   data-timestamp="';
                $value = $this->resolveValue($context->find('timestamp'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                                                   ';
                $value = $context->find('str');
                $buffer .= $this->section52bc13cb08d8b11281e5355488a15bad($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                                                </a>
';
                $buffer .= $indent . '                                            </strong>
';
                $buffer .= $indent . '                                        </li>
';
                $buffer .= $indent . '                                    </ul>
';
                $buffer .= $indent . '                                    <div class="hidden">
';
                $value = $context->find('events');
                $buffer .= $this->section6d305541516b4b8b076461474b174727($context, $indent, $value);
                $buffer .= $indent . '                                    </div>
';
                $buffer .= $indent . '                                </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1e541283a0533623efd22d0b0b04baa5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                                <a data-action="view-day-link" href="#" class="day aalink" aria-label="{{viewdaylinktitle}}"
                                    data-year="{{date.year}}" data-month="{{date.mon}}" data-day="{{mday}}"
                                    data-courseid="{{courseid}}" data-categoryid="{{categoryid}}"
                                    data-timestamp="{{timestamp}}"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></a>
                            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                                <a data-action="view-day-link" href="#" class="day aalink" aria-label="';
                $value = $this->resolveValue($context->find('viewdaylinktitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                                    data-year="';
                $value = $this->resolveValue($context->findDot('date.year'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-month="';
                $value = $this->resolveValue($context->findDot('date.mon'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-day="';
                $value = $this->resolveValue($context->find('mday'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                                    data-courseid="';
                $value = $this->resolveValue($context->find('courseid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-categoryid="';
                $value = $this->resolveValue($context->find('categoryid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                                    data-timestamp="';
                $value = $this->resolveValue($context->find('timestamp'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"><span class="day-number-circle"><span class="day-number">';
                $value = $this->resolveValue($context->find('mday'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span></span></a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1060f1febc78cd3837104d5a70b9e874(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <td class="day text-sm-center text-md-left{{!
                            }}{{#istoday}} today{{/istoday}}{{!
                            }}{{#isweekend}} weekend{{/isweekend}}{{!
                            }}{{#durationevents.0}} duration{{/durationevents.0}}{{!
                            }}{{#durationevents}} duration_{{.}}{{/durationevents}}{{!
                            }}{{#defaulteventcontext}} clickable{{/defaulteventcontext}}{{!
                            }}{{#hasevents}} hasevent{{/hasevents}}{{!
                        }}"
                        data-day="{{mday}}"
                        data-day-timestamp="{{timestamp}}"
                        data-drop-zone="month-view-day"
                        data-region="day"
                        data-new-event-timestamp="{{neweventtimestamp}}"{{#istoday}} title="{{#str}} today, core_calendar {{/str}}"{{/istoday}}
                        {{#hasevents}} data-title="{{viewdaylinktitle}}"{{/hasevents}}>
                        <div class="d-none d-md-block hidden-phone text-xs-center">
                            <span class="sr-only">{{daytitle}}</span>
                            {{#hasevents}}
                                <a data-action="view-day-link" href="#" class="aalink day" aria-label="{{viewdaylinktitle}}"
                                    data-year="{{date.year}}" data-month="{{date.mon}}" data-day="{{mday}}"
                                    data-courseid="{{courseid}}" data-categoryid="{{categoryid}}"
                                    data-timestamp="{{timestamp}}"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></a>
                            {{/hasevents}}
                            {{^hasevents}}
                                <span aria-hidden="true"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></span>
                            {{/hasevents}}
                            {{#hasevents}}
                                <div data-region="day-content">
                                    <ul>
                                        {{#events}}
                                        {{#underway}}
                                            <li class="events-underway">[{{{name}}}]</li>
                                        {{/underway}}
                                        {{^underway}}
                                            <li data-region="event-item"
                                                data-event-component="{{component}}"
                                                data-event-eventtype="{{eventtype}}"
                                                data-event-folded="false"
                                                data-eventtype-{{normalisedeventtype}}="1"
                                                {{#draggable}}
                                                    draggable="true"
                                                    data-drag-type="move"
                                                    {{#mindaytimestamp}}
                                                        data-min-day-timestamp="{{.}}"
                                                    {{/mindaytimestamp}}
                                                    {{#mindayerror}}
                                                        data-min-day-error="{{.}}"
                                                    {{/mindayerror}}
                                                    {{#maxdaytimestamp}}
                                                        data-max-day-timestamp="{{.}}"
                                                    {{/maxdaytimestamp}}
                                                    {{#maxdayerror}}
                                                        data-max-day-error="{{.}}"
                                                    {{/maxdayerror}}
                                                {{/draggable}}>

                                                <a data-action="view-event" data-event-id="{{id}}" href="{{url}}" title="{{name}}">
                                                    <span class="calendar-circle calendar_event_{{normalisedeventtype}}">
                                                        &nbsp;
                                                    </span>
                                                    <span class="eventname">{{{name}}}</span>
                                                </a>
                                            </li>
                                        {{/underway}}
                                        {{/events}}
                                        <li data-action="view-more-events" style="display: none">
                                            <strong>
                                                <a data-action="view-day-link" href="#" class="aalink day" aria-label="{{viewdaylinktitle}}"
                                                   data-year="{{date.year}}" data-month="{{date.mon}}" data-day="{{mday}}"
                                                   data-courseid="{{courseid}}" data-categoryid="{{categoryid}}"
                                                   data-timestamp="{{timestamp}}">
                                                   {{#str}} moreevents, calendar, {{hasmoreevents}} {{/str}}
                                                </a>
                                            </strong>
                                        </li>
                                    </ul>
                                    <div class="hidden">
                                        {{#events}}
                                            <div data-popover-eventtype-{{normalisedeventtype}}="1">
                                                {{> core_calendar/event_icon}}
                                                {{{name}}}
                                            </div>
                                        {{/events}}
                                    </div>
                                </div>
                            {{/hasevents}}
                        </div>
                        <div class="d-md-none hidden-desktop hidden-tablet">
                            <span class="sr-only">{{daytitle}}</span>
                            {{#hasevents}}
                                <a data-action="view-day-link" href="#" class="day aalink" aria-label="{{viewdaylinktitle}}"
                                    data-year="{{date.year}}" data-month="{{date.mon}}" data-day="{{mday}}"
                                    data-courseid="{{courseid}}" data-categoryid="{{categoryid}}"
                                    data-timestamp="{{timestamp}}"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></a>
                            {{/hasevents}}
                            {{^hasevents}}
                                <span aria-hidden="true"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></span>
                            {{/hasevents}}
                        </div>
                    </td>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <td class="day text-sm-center text-md-left';
                $value = $context->find('istoday');
                $buffer .= $this->sectionCce61e3fee851e8b66112494c048a58a($context, $indent, $value);
                $value = $context->find('isweekend');
                $buffer .= $this->section84f949a8191b4daaac38d5ac1997cd7a($context, $indent, $value);
                $value = $context->findDot('durationevents.0');
                $buffer .= $this->section8aa9feca8669cbbfdc6a37548f193ebe($context, $indent, $value);
                $value = $context->find('durationevents');
                $buffer .= $this->sectionC090550ce065bf0ee1844d93a2f5c5bc($context, $indent, $value);
                $value = $context->find('defaulteventcontext');
                $buffer .= $this->sectionA7cafb8496be54e32a81ab964df17ff2($context, $indent, $value);
                $value = $context->find('hasevents');
                $buffer .= $this->sectionE28f621eb042f18284779ecc71990758($context, $indent, $value);
                $buffer .= '"
';
                $buffer .= $indent . '                        data-day="';
                $value = $this->resolveValue($context->find('mday'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                        data-day-timestamp="';
                $value = $this->resolveValue($context->find('timestamp'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                        data-drop-zone="month-view-day"
';
                $buffer .= $indent . '                        data-region="day"
';
                $buffer .= $indent . '                        data-new-event-timestamp="';
                $value = $this->resolveValue($context->find('neweventtimestamp'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $value = $context->find('istoday');
                $buffer .= $this->section0376e0cfa88d71c021de2af759f6b3da($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                        ';
                $value = $context->find('hasevents');
                $buffer .= $this->section319181b8b4154e3dfe673ffd2a6e2de2($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                        <div class="d-none d-md-block hidden-phone text-xs-center">
';
                $buffer .= $indent . '                            <span class="sr-only">';
                $value = $this->resolveValue($context->find('daytitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span>
';
                $value = $context->find('hasevents');
                $buffer .= $this->section6d8762f9bad5b0242d71000030af73f6($context, $indent, $value);
                $value = $context->find('hasevents');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                                <span aria-hidden="true"><span class="day-number-circle"><span class="day-number">';
                    $value = $this->resolveValue($context->find('mday'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '</span></span></span>
';
                }
                $value = $context->find('hasevents');
                $buffer .= $this->sectionB3fa2ccb77b79eb31a810786cbc57d86($context, $indent, $value);
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                        <div class="d-md-none hidden-desktop hidden-tablet">
';
                $buffer .= $indent . '                            <span class="sr-only">';
                $value = $this->resolveValue($context->find('daytitle'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span>
';
                $value = $context->find('hasevents');
                $buffer .= $this->section1e541283a0533623efd22d0b0b04baa5($context, $indent, $value);
                $value = $context->find('hasevents');
                if (empty($value)) {
                    
                    $buffer .= $indent . '                                <span aria-hidden="true"><span class="day-number-circle"><span class="day-number">';
                    $value = $this->resolveValue($context->find('mday'), $context);
                    $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                    $buffer .= '</span></span></span>
';
                }
                $buffer .= $indent . '                        </div>
';
                $buffer .= $indent . '                    </td>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB3e10f42ada76b9c7d5cd4d51cb4b3df(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <tr data-region="month-view-week">
                {{#prepadding}}
                    <td class="dayblank">&nbsp;</td>
                {{/prepadding}}
                {{#days}}
                    <td class="day text-sm-center text-md-left{{!
                            }}{{#istoday}} today{{/istoday}}{{!
                            }}{{#isweekend}} weekend{{/isweekend}}{{!
                            }}{{#durationevents.0}} duration{{/durationevents.0}}{{!
                            }}{{#durationevents}} duration_{{.}}{{/durationevents}}{{!
                            }}{{#defaulteventcontext}} clickable{{/defaulteventcontext}}{{!
                            }}{{#hasevents}} hasevent{{/hasevents}}{{!
                        }}"
                        data-day="{{mday}}"
                        data-day-timestamp="{{timestamp}}"
                        data-drop-zone="month-view-day"
                        data-region="day"
                        data-new-event-timestamp="{{neweventtimestamp}}"{{#istoday}} title="{{#str}} today, core_calendar {{/str}}"{{/istoday}}
                        {{#hasevents}} data-title="{{viewdaylinktitle}}"{{/hasevents}}>
                        <div class="d-none d-md-block hidden-phone text-xs-center">
                            <span class="sr-only">{{daytitle}}</span>
                            {{#hasevents}}
                                <a data-action="view-day-link" href="#" class="aalink day" aria-label="{{viewdaylinktitle}}"
                                    data-year="{{date.year}}" data-month="{{date.mon}}" data-day="{{mday}}"
                                    data-courseid="{{courseid}}" data-categoryid="{{categoryid}}"
                                    data-timestamp="{{timestamp}}"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></a>
                            {{/hasevents}}
                            {{^hasevents}}
                                <span aria-hidden="true"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></span>
                            {{/hasevents}}
                            {{#hasevents}}
                                <div data-region="day-content">
                                    <ul>
                                        {{#events}}
                                        {{#underway}}
                                            <li class="events-underway">[{{{name}}}]</li>
                                        {{/underway}}
                                        {{^underway}}
                                            <li data-region="event-item"
                                                data-event-component="{{component}}"
                                                data-event-eventtype="{{eventtype}}"
                                                data-event-folded="false"
                                                data-eventtype-{{normalisedeventtype}}="1"
                                                {{#draggable}}
                                                    draggable="true"
                                                    data-drag-type="move"
                                                    {{#mindaytimestamp}}
                                                        data-min-day-timestamp="{{.}}"
                                                    {{/mindaytimestamp}}
                                                    {{#mindayerror}}
                                                        data-min-day-error="{{.}}"
                                                    {{/mindayerror}}
                                                    {{#maxdaytimestamp}}
                                                        data-max-day-timestamp="{{.}}"
                                                    {{/maxdaytimestamp}}
                                                    {{#maxdayerror}}
                                                        data-max-day-error="{{.}}"
                                                    {{/maxdayerror}}
                                                {{/draggable}}>

                                                <a data-action="view-event" data-event-id="{{id}}" href="{{url}}" title="{{name}}">
                                                    <span class="calendar-circle calendar_event_{{normalisedeventtype}}">
                                                        &nbsp;
                                                    </span>
                                                    <span class="eventname">{{{name}}}</span>
                                                </a>
                                            </li>
                                        {{/underway}}
                                        {{/events}}
                                        <li data-action="view-more-events" style="display: none">
                                            <strong>
                                                <a data-action="view-day-link" href="#" class="aalink day" aria-label="{{viewdaylinktitle}}"
                                                   data-year="{{date.year}}" data-month="{{date.mon}}" data-day="{{mday}}"
                                                   data-courseid="{{courseid}}" data-categoryid="{{categoryid}}"
                                                   data-timestamp="{{timestamp}}">
                                                   {{#str}} moreevents, calendar, {{hasmoreevents}} {{/str}}
                                                </a>
                                            </strong>
                                        </li>
                                    </ul>
                                    <div class="hidden">
                                        {{#events}}
                                            <div data-popover-eventtype-{{normalisedeventtype}}="1">
                                                {{> core_calendar/event_icon}}
                                                {{{name}}}
                                            </div>
                                        {{/events}}
                                    </div>
                                </div>
                            {{/hasevents}}
                        </div>
                        <div class="d-md-none hidden-desktop hidden-tablet">
                            <span class="sr-only">{{daytitle}}</span>
                            {{#hasevents}}
                                <a data-action="view-day-link" href="#" class="day aalink" aria-label="{{viewdaylinktitle}}"
                                    data-year="{{date.year}}" data-month="{{date.mon}}" data-day="{{mday}}"
                                    data-courseid="{{courseid}}" data-categoryid="{{categoryid}}"
                                    data-timestamp="{{timestamp}}"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></a>
                            {{/hasevents}}
                            {{^hasevents}}
                                <span aria-hidden="true"><span class="day-number-circle"><span class="day-number">{{mday}}</span></span></span>
                            {{/hasevents}}
                        </div>
                    </td>
                {{/days}}
                {{#postpadding}}
                    <td class="dayblank">&nbsp;</td>
                {{/postpadding}}
            </tr>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <tr data-region="month-view-week">
';
                $value = $context->find('prepadding');
                $buffer .= $this->section30ad4cd62bbcce6b32c5d2ea63ba3dca($context, $indent, $value);
                $value = $context->find('days');
                $buffer .= $this->section1060f1febc78cd3837104d5a70b9e874($context, $indent, $value);
                $value = $context->find('postpadding');
                $buffer .= $this->section30ad4cd62bbcce6b32c5d2ea63ba3dca($context, $indent, $value);
                $buffer .= $indent . '            </tr>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB56e1972cadb534bbb2d637a549766db(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
require([
    \'jquery\',
    \'core_calendar/month_view_drag_drop\'
], function(
    $,
    DragDrop
) {
    var root = $(\'#month-detailed-{{uniqid}}-{{calendarinstanceid}}\');
    DragDrop.init(root);
});
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . 'require([
';
                $buffer .= $indent . '    \'jquery\',
';
                $buffer .= $indent . '    \'core_calendar/month_view_drag_drop\'
';
                $buffer .= $indent . '], function(
';
                $buffer .= $indent . '    $,
';
                $buffer .= $indent . '    DragDrop
';
                $buffer .= $indent . ') {
';
                $buffer .= $indent . '    var root = $(\'#month-detailed-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '-';
                $value = $this->resolveValue($context->find('calendarinstanceid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '\');
';
                $buffer .= $indent . '    DragDrop.init(root);
';
                $buffer .= $indent . '});
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
