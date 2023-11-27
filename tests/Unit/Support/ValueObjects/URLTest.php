<?php

namespace Tests\Unit\Support\ValueObjects;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Support\ValueObjects\URL;
use Tests\TestCase;

class URLTest extends TestCase
{
    use WithFaker;

    protected string $baseUrl = 'https://sint.com';

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test  */
    public function baseUrlMethodShouldReturnString()
    {
        $url = URL::make($this->baseUrl);

        $this->assertTrue(
            Str::isUrl($url->baseUrl())
        );

        $this->assertIsString($url->baseUrl());
    }

    /** @test  */
    public function queryParamsShouldReturnArray()
    {
        $url = URL::make($this->baseUrl, [
            'submitted_at' => now(),
            'vacancy_id' => 11,
        ]);

        $this->assertIsArray($url->queryParams());
    }

    /** @test */
    public function itShouldBuildFullUrlWhenUrlAndQueryParamsAreValid()
    {
        $url = URL::make($this->baseUrl, [
            'submitted_at' => now()->format('Y-m-d H:i'),
            'vacancy_id' => 11,
            'token' => Hash::make('123'),
        ]);

        $this->assertIsString($url->fullUrl());
    }

    /** @test  */
    public function itShouldReturnFullUrlWhenCallingAsString()
    {
        $url = URL::make($this->baseUrl, [
            'foo' => 'baa',
        ]);

        $this->assertIsString((string) $url);
    }

    /** @test  */
    public function itShouldThrowErrorWhenInvalidUrlPassed()
    {
        $invalidUrl = 'http:://invaildURL.foo';

        $this->expectException(\Exception::class);

        $this->expectExceptionMessage("invalid url: $invalidUrl");

        $url = URL::make($invalidUrl);
    }
}
