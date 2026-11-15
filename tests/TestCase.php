<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Les vues utilisent @vite / @fonts : inutile de compiler les assets pour les tests.
        $this->withoutVite();
    }
}
