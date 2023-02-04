<?php

namespace KieranFYI\Tests\Media\Unit\Http\Requests;

use KieranFYI\Media\Http\Requests\SearchRequest;
use KieranFYI\Tests\Media\TestCase;

class SearchRequestTest extends TestCase
{
    public function testRules()
    {
        $this->assertEquals([
            'search' => ['nullable', 'string'],
            'page' => ['nullable', 'integer'],
        ], (new SearchRequest())->rules());
    }
}