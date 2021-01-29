<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();

    }
}
