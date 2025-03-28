<?php

namespace Tests\Unit;

use App\View\Components\AdminLayout;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    /**
     * Test czy komponent AdminLayout renderuje poprawny widok.
     *
     * @return void
     */
    public function test_admin_layout_renders_correct_view()
    {
        $component = new AdminLayout();
        $view = $component->render();
        
        $this->assertEquals('layouts.admin-livewire', $view->getName());
    }
} 