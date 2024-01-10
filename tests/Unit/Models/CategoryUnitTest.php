<?php

namespace Tests\Unit\Models;

use App\Models\Genero;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

# Classe especifica               - vendor/bin/phpunit tests/Unit/GeneroTest.php
# Método especifico em um arquivo - vendor/bin/phpunit --filter testIfUseTraits tests/Unit/GeneroTest.php
# Método especifico em uma classe - vendor/bin/phpunit --filter GeneroTest::testIfUseTraits

class GeneroUnitTest extends TestCase
{
    private $genero;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genero = new Genre();
    }


    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $generoTraits = array_keys(class_uses(Genero::class));
        $this->assertEquals($traits, $generoTraits);
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'is_active'];
        $this->assertEquals($fillable, $this->genero->getFillable());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->genero->getDates());
        }
        $this->assertCount(count($dates), $this->genero->getDates());
    }

    public function testCatsAttribute()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts, $this->genero->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->genero->incrementing);
    }


}
