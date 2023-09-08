<?php

class __Mustache_94ad30c9713b00520b7f50a0470d78e7 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div data-region="day-filter" class="dropdown mb-1">
';
        $buffer .= $indent . '    <button type="button" class="btn btn-outline-secondary dropdown-toggle icon-no-margin" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section2bfd6fe43b06b97ea356d4fcab591c00($context, $indent, $value);
        $buffer .= '" aria-controls="menudayfilter"
';
        $buffer .= $indent . '            title="';
        $value = $context->find('str');
        $buffer .= $this->sectionF15412641d113a5b82ef6d99781ad384($context, $indent, $value);
        $buffer .= '" aria-describedby="timeline-day-filter-current-selection">
';
        $buffer .= $indent . '        <span id="timeline-day-filter-current-selection" data-active-item-text>
';
        $buffer .= $indent . '            ';
        $value = $context->find('all');
        $buffer .= $this->section07e7ad586188f7567e4f0282449d998a($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            ';
        $value = $context->find('overdue');
        $buffer .= $this->sectionCe5fbcbc4bde8b805db62d1648be5798($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            ';
        $value = $context->find('next7days');
        $buffer .= $this->sectionCc7f7061e3f062969cbadd2dc0e1358f($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            ';
        $value = $context->find('next30days');
        $buffer .= $this->sectionBad1e52226fe5895ae1a7061e3e9246c($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            ';
        $value = $context->find('next3months');
        $buffer .= $this->sectionAc6f7408d9e2a00c37e161e5d06371e7($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            ';
        $value = $context->find('next6months');
        $buffer .= $this->sectionA656f9c9f0e96debaeaca2d014fdde65($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '        </span>
';
        $buffer .= $indent . '    </button>
';
        $buffer .= $indent . '    <div id="menudayfilter" role="menu" class="dropdown-menu" data-show-active-item data-skip-active-class="true">
';
        $buffer .= $indent . '        <a
';
        $buffer .= $indent . '            class="dropdown-item"
';
        $buffer .= $indent . '            href="#"
';
        $buffer .= $indent . '            data-from="-14"
';
        $buffer .= $indent . '            data-filtername="all"
';
        $buffer .= $indent . '            ';
        $value = $context->find('all');
        $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->sectionA176852b616f342104b33141731cf7e3($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '            role="menuitem"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '            ';
        $value = $context->find('str');
        $buffer .= $this->section796b182c855d7b48f08d0295b8450703($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '        <a
';
        $buffer .= $indent . '            class="dropdown-item"
';
        $buffer .= $indent . '            href="#"
';
        $buffer .= $indent . '            data-from="-14"
';
        $buffer .= $indent . '            data-to="1"
';
        $buffer .= $indent . '            data-filtername="overdue"
';
        $buffer .= $indent . '            ';
        $value = $context->find('overdue');
        $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->sectionA4af987a17a121006b1261664f42f160($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '            role="menuitem"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '            ';
        $value = $context->find('str');
        $buffer .= $this->section2b53b81f2e413c7f3305585402637ce5($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '        <div class="dropdown-divider" role="separator"></div>
';
        $buffer .= $indent . '        <h6 class="dropdown-header">';
        $value = $context->find('str');
        $buffer .= $this->sectionF87960ec9a019cff09c35a4337264efd($context, $indent, $value);
        $buffer .= '</h6>
';
        $buffer .= $indent . '        <a
';
        $buffer .= $indent . '            class="dropdown-item"
';
        $buffer .= $indent . '            href="#"
';
        $buffer .= $indent . '            data-from="0"
';
        $buffer .= $indent . '            data-to="7"
';
        $buffer .= $indent . '            data-filtername="next7days"
';
        $buffer .= $indent . '            ';
        $value = $context->find('next7days');
        $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section30ba648f69c5b8bc898da8dfa83799aa($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '            role="menuitem"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '            ';
        $value = $context->find('str');
        $buffer .= $this->section924731da35c118fc230304b129d1ee39($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '        <a
';
        $buffer .= $indent . '            class="dropdown-item"
';
        $buffer .= $indent . '            href="#"
';
        $buffer .= $indent . '            data-from="0"
';
        $buffer .= $indent . '            data-to="30"
';
        $buffer .= $indent . '            data-filtername="next30days"
';
        $buffer .= $indent . '            ';
        $value = $context->find('next30days');
        $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->sectionE5baa477f3a25999f054a0b58620e924($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '            role="menuitem"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '            ';
        $value = $context->find('str');
        $buffer .= $this->sectionE563c52f6c2570f63e63ab28b0341de8($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '        <a
';
        $buffer .= $indent . '            class="dropdown-item"
';
        $buffer .= $indent . '            href="#"
';
        $buffer .= $indent . '            data-from="0"
';
        $buffer .= $indent . '            data-to="90"
';
        $buffer .= $indent . '            data-filtername="next3months"
';
        $buffer .= $indent . '            ';
        $value = $context->find('next3months');
        $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->sectionF997284219d5db169619c75faca471c3($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '            role="menuitem"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '            ';
        $value = $context->find('str');
        $buffer .= $this->sectionB11d45d10da0071260fb95fc0b3e4472($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '        <a
';
        $buffer .= $indent . '            class="dropdown-item"
';
        $buffer .= $indent . '            href="#"
';
        $buffer .= $indent . '            data-from="0"
';
        $buffer .= $indent . '            data-to="180"
';
        $buffer .= $indent . '            data-filtername="next6months"
';
        $buffer .= $indent . '            ';
        $value = $context->find('next6months');
        $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section5f5aa5c86e6f620a5289462d7e7a53b9($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '            role="menuitem"
';
        $buffer .= $indent . '        >
';
        $buffer .= $indent . '            ';
        $value = $context->find('str');
        $buffer .= $this->section2b2acf2ac0842b77ca0c679d6d5c1611($context, $indent, $value);
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

    private function section2bfd6fe43b06b97ea356d4fcab591c00(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariadayfilter, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariadayfilter, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF15412641d113a5b82ef6d99781ad384(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariadayfilter, block_timeline';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariadayfilter, block_timeline';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section796b182c855d7b48f08d0295b8450703(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' all, core ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' all, core ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section07e7ad586188f7567e4f0282449d998a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' {{#str}} all, core {{/str}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ';
                $value = $context->find('str');
                $buffer .= $this->section796b182c855d7b48f08d0295b8450703($context, $indent, $value);
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section2b53b81f2e413c7f3305585402637ce5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' overdue, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' overdue, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCe5fbcbc4bde8b805db62d1648be5798(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' {{#str}} overdue, block_timeline {{/str}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ';
                $value = $context->find('str');
                $buffer .= $this->section2b53b81f2e413c7f3305585402637ce5($context, $indent, $value);
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4ea118d81b6b550a666436f44cbfe6f8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'next7days, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'next7days, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCc7f7061e3f062969cbadd2dc0e1358f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' {{#str}}next7days, block_timeline {{/str}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ';
                $value = $context->find('str');
                $buffer .= $this->section4ea118d81b6b550a666436f44cbfe6f8($context, $indent, $value);
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section05a8d4b7a3829e9336bee618ed110b39(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'next30days, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'next30days, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBad1e52226fe5895ae1a7061e3e9246c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' {{#str}}next30days, block_timeline {{/str}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ';
                $value = $context->find('str');
                $buffer .= $this->section05a8d4b7a3829e9336bee618ed110b39($context, $indent, $value);
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section39b4ab160929ecaa6ccaa0e864cb92a2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'next3months, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'next3months, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionAc6f7408d9e2a00c37e161e5d06371e7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' {{#str}}next3months, block_timeline {{/str}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ';
                $value = $context->find('str');
                $buffer .= $this->section39b4ab160929ecaa6ccaa0e864cb92a2($context, $indent, $value);
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section665941133eb8ee8a420a40a68928c4a5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'next6months, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'next6months, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA656f9c9f0e96debaeaca2d014fdde65(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' {{#str}}next6months, block_timeline {{/str}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ';
                $value = $context->find('str');
                $buffer .= $this->section665941133eb8ee8a420a40a68928c4a5($context, $indent, $value);
                $buffer .= ' ';
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

    private function sectionA176852b616f342104b33141731cf7e3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariadayfilteroption, block_timeline, {{#str}} all, core {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariadayfilteroption, block_timeline, ';
                $value = $context->find('str');
                $buffer .= $this->section796b182c855d7b48f08d0295b8450703($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA4af987a17a121006b1261664f42f160(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariadayfilteroption, block_timeline, {{#str}} overdue, block_timeline {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariadayfilteroption, block_timeline, ';
                $value = $context->find('str');
                $buffer .= $this->section2b53b81f2e413c7f3305585402637ce5($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF87960ec9a019cff09c35a4337264efd(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' duedate, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' duedate, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section924731da35c118fc230304b129d1ee39(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' next7days, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' next7days, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section30ba648f69c5b8bc898da8dfa83799aa(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariadayfilteroption, block_timeline, {{#str}} next7days, block_timeline {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariadayfilteroption, block_timeline, ';
                $value = $context->find('str');
                $buffer .= $this->section924731da35c118fc230304b129d1ee39($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE563c52f6c2570f63e63ab28b0341de8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' next30days, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' next30days, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE5baa477f3a25999f054a0b58620e924(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariadayfilteroption, block_timeline, {{#str}} next30days, block_timeline {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariadayfilteroption, block_timeline, ';
                $value = $context->find('str');
                $buffer .= $this->sectionE563c52f6c2570f63e63ab28b0341de8($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB11d45d10da0071260fb95fc0b3e4472(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' next3months, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' next3months, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF997284219d5db169619c75faca471c3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariadayfilteroption, block_timeline, {{#str}} next3months, block_timeline {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariadayfilteroption, block_timeline, ';
                $value = $context->find('str');
                $buffer .= $this->sectionB11d45d10da0071260fb95fc0b3e4472($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section2b2acf2ac0842b77ca0c679d6d5c1611(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' next6months, block_timeline ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' next6months, block_timeline ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5f5aa5c86e6f620a5289462d7e7a53b9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' ariadayfilteroption, block_timeline, {{#str}} next6months, block_timeline {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' ariadayfilteroption, block_timeline, ';
                $value = $context->find('str');
                $buffer .= $this->section2b2acf2ac0842b77ca0c679d6d5c1611($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
