<?php

class __Mustache_eb485913a06756125f475be07b3a29f8 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('displaygroupingselector');
        $buffer .= $this->sectionD726ef070087e4da1eb0c12366d343ca($context, $indent, $value);
        $value = $context->find('displaygroupingselector');
        if (empty($value)) {
            
            $buffer .= $indent . '<div class="mb-1 mr-auto">
';
            $buffer .= $indent . '    <span class="filler">&nbsp;</span>
';
            $buffer .= $indent . '</div>
';
        }

        return $buffer;
    }

    private function sectionCdca03c966985271d933771f38d17201(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' aria:groupingdropdown, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' aria:groupingdropdown, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF8249148f760da3e109caa3663f633ad(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' allincludinghidden, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' allincludinghidden, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDd89a06415c3a9738588538d22ed5719(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}} allincludinghidden, block_myoverview {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->sectionF8249148f760da3e109caa3663f633ad($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7aa86992f913c684aa982299bc5b0237(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' all, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' all, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3842a12aa13716271b547dd2b63a83f0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}} all, block_myoverview {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->section7aa86992f913c684aa982299bc5b0237($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section943672e65d749500a0e39a5c5732165b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' inprogress, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' inprogress, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section423ab0cbddfc75d44c8e8c6d7354a909(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}} inprogress, block_myoverview {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->section943672e65d749500a0e39a5c5732165b($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section73e2d31fbcff71568286bba0816b9728(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' future, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' future, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA95d1da52da1e6b0fb8c273a6af61e45(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}} future, block_myoverview {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->section73e2d31fbcff71568286bba0816b9728($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1e8dea46004e4fe5964bd9187fa70241(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' past, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' past, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC723f5724128554b7185f98be27ffdca(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}} past, block_myoverview {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->section1e8dea46004e4fe5964bd9187fa70241($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6a8fa4f1d83488a4bff00ad9c6b4e87c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' favourites, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' favourites, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section33d18a9baa544480c9874cbbfef7bfd0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}} favourites, block_myoverview {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->section6a8fa4f1d83488a4bff00ad9c6b4e87c($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section62c24258cc369df41565ccf8e4d02cf8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' hiddencourses, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' hiddencourses, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section351479d60e926483df14f8612bc07800(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}} hiddencourses, block_myoverview {{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('str');
                $buffer .= $this->section62c24258cc369df41565ccf8e4d02cf8($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section2baefc80a6c574d8f63074ae198cd792(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' aria:allcoursesincludinghidden, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' aria:allcoursesincludinghidden, block_myoverview ';
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

    private function sectionA8d7f2948d337ed8382ec87fd9040693(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="allincludinghidden" data-pref="allincludinghidden" aria-label="{{#str}} aria:allcoursesincludinghidden, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#allincludinghidden}}aria-current="true"{{/allincludinghidden}}>
                {{#str}} allincludinghidden, block_myoverview {{/str}}
            </a>
        </li>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <li>
';
                $buffer .= $indent . '            <a class="dropdown-item" href="#" data-filter="grouping" data-value="allincludinghidden" data-pref="allincludinghidden" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->section2baefc80a6c574d8f63074ae198cd792($context, $indent, $value);
                $buffer .= '" aria-controls="courses-view-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menuitem" ';
                $value = $context->find('allincludinghidden');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->sectionF8249148f760da3e109caa3663f633ad($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            </a>
';
                $buffer .= $indent . '        </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionBfccddfcef129c993aebf4ea499b00f4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' aria:allcourses, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' aria:allcourses, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD4e51cc446cca6db12110f7d2dd6dc30(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <li class="dropdown-divider" role="presentation">
            <span class="filler">&nbsp;</span>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="all" data-pref="all" aria-label="{{#str}} aria:allcourses, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#all}}aria-current="true"{{/all}}>
                {{#str}} all, block_myoverview {{/str}}
            </a>
        </li>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <li class="dropdown-divider" role="presentation">
';
                $buffer .= $indent . '            <span class="filler">&nbsp;</span>
';
                $buffer .= $indent . '        </li>
';
                $buffer .= $indent . '        <li>
';
                $buffer .= $indent . '            <a class="dropdown-item" href="#" data-filter="grouping" data-value="all" data-pref="all" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionBfccddfcef129c993aebf4ea499b00f4($context, $indent, $value);
                $buffer .= '" aria-controls="courses-view-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menuitem" ';
                $value = $context->find('all');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->section7aa86992f913c684aa982299bc5b0237($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            </a>
';
                $buffer .= $indent . '        </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6a2e4a8597805bb70bcf8a5cd54793e1(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' aria:inprogress, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' aria:inprogress, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFffd4a6c3b34db8d4d5b5de5a54652a3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <li class="dropdown-divider" role="presentation">
            <span class="filler">&nbsp;</span>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="inprogress" data-pref="inprogress" aria-label="{{#str}} aria:inprogress, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#inprogress}}aria-current="true"{{/inprogress}}>
                {{#str}} inprogress, block_myoverview {{/str}}
            </a>
        </li>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <li class="dropdown-divider" role="presentation">
';
                $buffer .= $indent . '            <span class="filler">&nbsp;</span>
';
                $buffer .= $indent . '        </li>
';
                $buffer .= $indent . '        <li>
';
                $buffer .= $indent . '            <a class="dropdown-item" href="#" data-filter="grouping" data-value="inprogress" data-pref="inprogress" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->section6a2e4a8597805bb70bcf8a5cd54793e1($context, $indent, $value);
                $buffer .= '" aria-controls="courses-view-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menuitem" ';
                $value = $context->find('inprogress');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->section943672e65d749500a0e39a5c5732165b($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            </a>
';
                $buffer .= $indent . '        </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8035b57ae92326df403c45b19619cf49(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' aria:future, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' aria:future, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7222f7e2a44170ca5df3f701b371a5f2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{^displaygroupinginprogress}}
            <li class="dropdown-divider" role="presentation">
                <span class="filler">&nbsp;</span>
            </li>
            {{/displaygroupinginprogress}}
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="future" data-pref="future" aria-label="{{#str}} aria:future, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#future}}aria-current="true"{{/future}}>
                {{#str}} future, block_myoverview {{/str}}
            </a>
        </li>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('displaygroupinginprogress');
                if (empty($value)) {
                    
                    $buffer .= $indent . '            <li class="dropdown-divider" role="presentation">
';
                    $buffer .= $indent . '                <span class="filler">&nbsp;</span>
';
                    $buffer .= $indent . '            </li>
';
                }
                $buffer .= $indent . '        <li>
';
                $buffer .= $indent . '            <a class="dropdown-item" href="#" data-filter="grouping" data-value="future" data-pref="future" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->section8035b57ae92326df403c45b19619cf49($context, $indent, $value);
                $buffer .= '" aria-controls="courses-view-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menuitem" ';
                $value = $context->find('future');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->section73e2d31fbcff71568286bba0816b9728($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            </a>
';
                $buffer .= $indent . '        </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section60cb1bc6b8bef86f0ebcc4100b812ad9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' aria:past, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' aria:past, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB302a1e85c352759ac46ea1e16cf22df(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{^displaygroupinginprogress}}
                {{^displaygroupingfuture}}
                <li class="dropdown-divider" role="presentation">
                    <span class="filler">&nbsp;</span>
                </li>
                {{/displaygroupingfuture}}
            {{/displaygroupinginprogress}}
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="past" data-pref="past" aria-label="{{#str}} aria:past, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#past}}aria-current="true"{{/past}}>
                {{#str}} past, block_myoverview {{/str}}
            </a>
        </li>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('displaygroupinginprogress');
                if (empty($value)) {
                    
                    $value = $context->find('displaygroupingfuture');
                    if (empty($value)) {
                        
                        $buffer .= $indent . '                <li class="dropdown-divider" role="presentation">
';
                        $buffer .= $indent . '                    <span class="filler">&nbsp;</span>
';
                        $buffer .= $indent . '                </li>
';
                    }
                }
                $buffer .= $indent . '        <li>
';
                $buffer .= $indent . '            <a class="dropdown-item" href="#" data-filter="grouping" data-value="past" data-pref="past" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->section60cb1bc6b8bef86f0ebcc4100b812ad9($context, $indent, $value);
                $buffer .= '" aria-controls="courses-view-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menuitem" ';
                $value = $context->find('past');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->section1e8dea46004e4fe5964bd9187fa70241($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            </a>
';
                $buffer .= $indent . '        </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEf3b21f97bc8fe4506d69d5331b9f4b9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'aria:customfield, block_myoverview, {{name}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'aria:customfield, block_myoverview, ';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section27318474f4eb38fa1db0ebfdc96688e8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <li>
                    <a class="dropdown-item" href="#" data-filter="grouping"
                       data-value="customfield" data-pref="customfield" data-customfieldvalue="{{value}}"
                       aria-label="{{#str}}aria:customfield, block_myoverview, {{name}}{{/str}}"
                       aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#active}}aria-current="true"{{/active}}>
                        {{name}}
                    </a>
                </li>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <li>
';
                $buffer .= $indent . '                    <a class="dropdown-item" href="#" data-filter="grouping"
';
                $buffer .= $indent . '                       data-value="customfield" data-pref="customfield" data-customfieldvalue="';
                $value = $this->resolveValue($context->find('value'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"
';
                $buffer .= $indent . '                       aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionEf3b21f97bc8fe4506d69d5331b9f4b9($context, $indent, $value);
                $buffer .= '"
';
                $buffer .= $indent . '                       aria-controls="courses-view-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menuitem" ';
                $value = $context->find('active');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                        ';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '
';
                $buffer .= $indent . '                    </a>
';
                $buffer .= $indent . '                </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section68a63c73f4736b6ecd346553e078ef61(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <li class="dropdown-divider" role="presentation">
                <span class="filler">&nbsp;</span>
            </li>
            {{#customfieldvalues}}
                <li>
                    <a class="dropdown-item" href="#" data-filter="grouping"
                       data-value="customfield" data-pref="customfield" data-customfieldvalue="{{value}}"
                       aria-label="{{#str}}aria:customfield, block_myoverview, {{name}}{{/str}}"
                       aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#active}}aria-current="true"{{/active}}>
                        {{name}}
                    </a>
                </li>
            {{/customfieldvalues}}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <li class="dropdown-divider" role="presentation">
';
                $buffer .= $indent . '                <span class="filler">&nbsp;</span>
';
                $buffer .= $indent . '            </li>
';
                $value = $context->find('customfieldvalues');
                $buffer .= $this->section27318474f4eb38fa1db0ebfdc96688e8($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section153539089d04b17ab672bafd0b927502(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' aria:favourites, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' aria:favourites, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA6b4de8817268f50849413abb85ceafc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <li class="dropdown-divider" role="presentation">
            <span class="filler">&nbsp;</span>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="favourites"  data-pref="favourites" aria-label="{{#str}} aria:favourites, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#favourites}}aria-current="true"{{/favourites}}>
                {{#str}} favourites, block_myoverview {{/str}}
            </a>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <li class="dropdown-divider" role="presentation">
';
                $buffer .= $indent . '            <span class="filler">&nbsp;</span>
';
                $buffer .= $indent . '        </li>
';
                $buffer .= $indent . '        <li>
';
                $buffer .= $indent . '            <a class="dropdown-item" href="#" data-filter="grouping" data-value="favourites"  data-pref="favourites" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->section153539089d04b17ab672bafd0b927502($context, $indent, $value);
                $buffer .= '" aria-controls="courses-view-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menuitem" ';
                $value = $context->find('favourites');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->section6a8fa4f1d83488a4bff00ad9c6b4e87c($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            </a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA0d6bc2dd5f49bbd3f2040aaf69a67b9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' aria:hiddencourses, block_myoverview ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' aria:hiddencourses, block_myoverview ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section356026611c829ff09fb2897482093eff(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <li class="dropdown-divider" role="presentation">
            <span class="filler">&nbsp;</span>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="hidden"  data-pref="hidden" aria-label="{{#str}} aria:hiddencourses, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#hidden}}aria-current="true"{{/hidden}}>
                {{#str}} hiddencourses, block_myoverview {{/str}}
            </a>
        </li>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <li class="dropdown-divider" role="presentation">
';
                $buffer .= $indent . '            <span class="filler">&nbsp;</span>
';
                $buffer .= $indent . '        </li>
';
                $buffer .= $indent . '        <li>
';
                $buffer .= $indent . '            <a class="dropdown-item" href="#" data-filter="grouping" data-value="hidden"  data-pref="hidden" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionA0d6bc2dd5f49bbd3f2040aaf69a67b9($context, $indent, $value);
                $buffer .= '" aria-controls="courses-view-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" role="menuitem" ';
                $value = $context->find('hidden');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                ';
                $value = $context->find('str');
                $buffer .= $this->section62c24258cc369df41565ccf8e4d02cf8($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            </a>
';
                $buffer .= $indent . '        </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD726ef070087e4da1eb0c12366d343ca(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
<div class="dropdown mb-1 mr-1">
    <button id="groupingdropdown" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="{{#str}} aria:groupingdropdown, block_myoverview {{/str}}">
        <span data-active-item-text>
            {{#allincludinghidden}}{{#str}} allincludinghidden, block_myoverview {{/str}}{{/allincludinghidden}}
            {{#all}}{{#str}} all, block_myoverview {{/str}}{{/all}}
            {{#inprogress}}{{#str}} inprogress, block_myoverview {{/str}}{{/inprogress}}
            {{#future}}{{#str}} future, block_myoverview {{/str}}{{/future}}
            {{#past}}{{#str}} past, block_myoverview {{/str}}{{/past}}
            {{#favourites}}{{#str}} favourites, block_myoverview {{/str}}{{/favourites}}
            {{#hidden}}{{#str}} hiddencourses, block_myoverview {{/str}}{{/hidden}}
            {{selectedcustomfield}}
        </span>
    </button>
    <ul class="dropdown-menu" role="menu" data-show-active-item data-skip-active-class="true" data-active-item-text aria-labelledby="groupingdropdown">
        {{#displaygroupingallincludinghidden}}
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="allincludinghidden" data-pref="allincludinghidden" aria-label="{{#str}} aria:allcoursesincludinghidden, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#allincludinghidden}}aria-current="true"{{/allincludinghidden}}>
                {{#str}} allincludinghidden, block_myoverview {{/str}}
            </a>
        </li>
        {{/displaygroupingallincludinghidden}}
        {{#displaygroupingall}}
        <li class="dropdown-divider" role="presentation">
            <span class="filler">&nbsp;</span>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="all" data-pref="all" aria-label="{{#str}} aria:allcourses, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#all}}aria-current="true"{{/all}}>
                {{#str}} all, block_myoverview {{/str}}
            </a>
        </li>
        {{/displaygroupingall}}
        {{#displaygroupinginprogress}}
        <li class="dropdown-divider" role="presentation">
            <span class="filler">&nbsp;</span>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="inprogress" data-pref="inprogress" aria-label="{{#str}} aria:inprogress, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#inprogress}}aria-current="true"{{/inprogress}}>
                {{#str}} inprogress, block_myoverview {{/str}}
            </a>
        </li>
        {{/displaygroupinginprogress}}
        {{#displaygroupingfuture}}
            {{^displaygroupinginprogress}}
            <li class="dropdown-divider" role="presentation">
                <span class="filler">&nbsp;</span>
            </li>
            {{/displaygroupinginprogress}}
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="future" data-pref="future" aria-label="{{#str}} aria:future, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#future}}aria-current="true"{{/future}}>
                {{#str}} future, block_myoverview {{/str}}
            </a>
        </li>
        {{/displaygroupingfuture}}
        {{#displaygroupingpast}}
            {{^displaygroupinginprogress}}
                {{^displaygroupingfuture}}
                <li class="dropdown-divider" role="presentation">
                    <span class="filler">&nbsp;</span>
                </li>
                {{/displaygroupingfuture}}
            {{/displaygroupinginprogress}}
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="past" data-pref="past" aria-label="{{#str}} aria:past, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#past}}aria-current="true"{{/past}}>
                {{#str}} past, block_myoverview {{/str}}
            </a>
        </li>
        {{/displaygroupingpast}}
        {{#displaygroupingcustomfield}}
            <li class="dropdown-divider" role="presentation">
                <span class="filler">&nbsp;</span>
            </li>
            {{#customfieldvalues}}
                <li>
                    <a class="dropdown-item" href="#" data-filter="grouping"
                       data-value="customfield" data-pref="customfield" data-customfieldvalue="{{value}}"
                       aria-label="{{#str}}aria:customfield, block_myoverview, {{name}}{{/str}}"
                       aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#active}}aria-current="true"{{/active}}>
                        {{name}}
                    </a>
                </li>
            {{/customfieldvalues}}
        {{/displaygroupingcustomfield}}
        {{#displaygroupingfavourites}}
        <li class="dropdown-divider" role="presentation">
            <span class="filler">&nbsp;</span>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="favourites"  data-pref="favourites" aria-label="{{#str}} aria:favourites, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#favourites}}aria-current="true"{{/favourites}}>
                {{#str}} favourites, block_myoverview {{/str}}
            </a>
        {{/displaygroupingfavourites}}
        {{#displaygroupinghidden}}
        <li class="dropdown-divider" role="presentation">
            <span class="filler">&nbsp;</span>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-filter="grouping" data-value="hidden"  data-pref="hidden" aria-label="{{#str}} aria:hiddencourses, block_myoverview {{/str}}" aria-controls="courses-view-{{uniqid}}" role="menuitem" {{#hidden}}aria-current="true"{{/hidden}}>
                {{#str}} hiddencourses, block_myoverview {{/str}}
            </a>
        </li>
        {{/displaygroupinghidden}}
    </ul>
</div>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '<div class="dropdown mb-1 mr-1">
';
                $buffer .= $indent . '    <button id="groupingdropdown" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="';
                $value = $context->find('str');
                $buffer .= $this->sectionCdca03c966985271d933771f38d17201($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '        <span data-active-item-text>
';
                $buffer .= $indent . '            ';
                $value = $context->find('allincludinghidden');
                $buffer .= $this->sectionDd89a06415c3a9738588538d22ed5719($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $context->find('all');
                $buffer .= $this->section3842a12aa13716271b547dd2b63a83f0($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $context->find('inprogress');
                $buffer .= $this->section423ab0cbddfc75d44c8e8c6d7354a909($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $context->find('future');
                $buffer .= $this->sectionA95d1da52da1e6b0fb8c273a6af61e45($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $context->find('past');
                $buffer .= $this->sectionC723f5724128554b7185f98be27ffdca($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $context->find('favourites');
                $buffer .= $this->section33d18a9baa544480c9874cbbfef7bfd0($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $context->find('hidden');
                $buffer .= $this->section351479d60e926483df14f8612bc07800($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('selectedcustomfield'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '
';
                $buffer .= $indent . '        </span>
';
                $buffer .= $indent . '    </button>
';
                $buffer .= $indent . '    <ul class="dropdown-menu" role="menu" data-show-active-item data-skip-active-class="true" data-active-item-text aria-labelledby="groupingdropdown">
';
                $value = $context->find('displaygroupingallincludinghidden');
                $buffer .= $this->sectionA8d7f2948d337ed8382ec87fd9040693($context, $indent, $value);
                $value = $context->find('displaygroupingall');
                $buffer .= $this->sectionD4e51cc446cca6db12110f7d2dd6dc30($context, $indent, $value);
                $value = $context->find('displaygroupinginprogress');
                $buffer .= $this->sectionFffd4a6c3b34db8d4d5b5de5a54652a3($context, $indent, $value);
                $value = $context->find('displaygroupingfuture');
                $buffer .= $this->section7222f7e2a44170ca5df3f701b371a5f2($context, $indent, $value);
                $value = $context->find('displaygroupingpast');
                $buffer .= $this->sectionB302a1e85c352759ac46ea1e16cf22df($context, $indent, $value);
                $value = $context->find('displaygroupingcustomfield');
                $buffer .= $this->section68a63c73f4736b6ecd346553e078ef61($context, $indent, $value);
                $value = $context->find('displaygroupingfavourites');
                $buffer .= $this->sectionA6b4de8817268f50849413abb85ceafc($context, $indent, $value);
                $value = $context->find('displaygroupinghidden');
                $buffer .= $this->section356026611c829ff09fb2897482093eff($context, $indent, $value);
                $buffer .= $indent . '    </ul>
';
                $buffer .= $indent . '</div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
