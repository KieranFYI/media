<?php

namespace KieranFYI\Media\Listeners;

use KieranFYI\Admin\Facades\Admin;
use KieranFYI\Media\Core\Models\Media;

class RegisterAdminNavigationListener
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(): void
    {
        Admin::header()
            ->menu('Media Library')
            ->can('viewAny')
            ->model(Media::class)
            ->route('admin.media.index')
            ->icon('');
    }

}