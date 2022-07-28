<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    private $uuid_validation_regex = "/^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/"; 
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        // $response = $this->get('/');

        // $response->assertStatus(200);
        Category::factory(1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);

        $categoryKey = array_keys($categories->first()->getAttributes());

        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $categoryKey
        );
    }

    public function test_create()
    {
        $category = Category::create(
            [
                'name' => 'teste'
            ]
        );

        $category->refresh();

        $this->assertEquals('teste', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        $this->assertTrue(Uuid::isValid($category->id));

        $category = Category::create([
            'name' => 'Category1',
            'description' => null,
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'Category1',
            'description' => 'Test Decription',
        ]);
        $this->assertEquals('Test Decription', $category->description);

        $category = Category::create([
            'name' => 'Category1',
            'is_active' => false,
        ]);
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'Category1',
            'is_active' => true,
        ]);
        $this->assertTrue($category->is_active);
    }

    public function test_update()
    {
        $category = Category::factory()->create(
            [
                'description' => 'test_description',
                'is_active'  => false
            ]
        )->first();

        $data =
            [
                'name' => 'test_name_updated',
                'description' => 'test_description_updated',
                'is_active'  => true
            ];
        $category->update($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = Category::factory()->create()->first();
        $category->delete();
        $this->assertNotNull($category->deleted_at);
    }

    function isValidUuid( $uuid ) {
    
        if (!is_string($uuid) || (preg_match($this->uuid_validation_regex,$uuid) !== 1)) {
            return false;
        }
    
        return true;
    }

    public function testValidatedUuid()
    {

        $categoria = Category::factory()->create(
            [
                'name' => 'categoria Teste UUID'
            ])->first();
        $uuid = $categoria->id;
        $uuidValid = $this->isValidUuid($uuid);
       $this->assertTrue($uuidValid);

    }



}
