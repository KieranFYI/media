<?php

namespace KieranFYI\Tests\Media\Unit\Listeners;

use KieranFYI\Admin\Facades\Admin;
use KieranFYI\Media\Listeners\RegisterAdminNavigationListener;
use KieranFYI\Tests\Media\TestCase;

class RegisterAdminNavigationListenerTest extends TestCase
{
    public function testHandle()
    {
        $listener = new RegisterAdminNavigationListener();
        $listener->handle();
        $menus = Admin::menus();
        $this->assertIsArray($menus);
    }
}