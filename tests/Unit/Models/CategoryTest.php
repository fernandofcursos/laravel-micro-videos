<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryTest extends TestCase
{
    private $category;

    // this method is shared between tests and is executed once, before a test run
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    // this method is shared between tests and is executed once, after all tests run
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }

    // execute every test executes

    /**
     * @test
     * @return void
     */
    public function testFillableAttribute()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    // execute after a test finished executing

    /**
     * @test
     * @return void
     */
    public function testUseTraits()
    {
        $traits = [
            HasFactory::class,
            SoftDeletes::class, 
            Uuid::class]
            ;

        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }

    /**
     * @test
     * @return void
     */
    public function testCastsAttribute()
    {
        $cast = ['is_active' => 'boolean', 'deleted_at'=>'datetime'];
        $this->assertEquals($cast, $this->category->getCasts());
    }

    /**
     * @test
     * @return void
     */
    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->category->incrementing);
    }

    /**
     * @test
     * @return void
     */
    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $modelDates = array_values($this->category->getDates());
        $this->assertEquals($dates, $modelDates);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
