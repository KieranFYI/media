<?php

namespace KieranFYI\Tests\Media\Unit\Http\Requests;

use KieranFYI\Media\Http\Requests\StoreRequest;
use KieranFYI\Tests\Media\TestCase;

class StoreRequestTest extends TestCase
{
    public function testRules()
    {
        $this->assertEquals([
            'files' => ['required', 'array'],
            'files.*' => ['nullable', 'file'],
        ], (new StoreRequest())->rules());
    }
}