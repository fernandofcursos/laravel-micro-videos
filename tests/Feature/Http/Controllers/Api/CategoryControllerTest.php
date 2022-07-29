<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;


class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::factory()->create();
    }

    public function testIndex()
    {

        $response = $this->get(route('categories.index'));

        $response
        ->assertStatus(200)
        ->assertJson([$this->category->toArray()]);
    }


    public function testShow()
    {
        $category = Category::factory()->create();
       
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));
      
        $response
        ->assertStatus(200)
        ->assertJson([$this->category->toArray()]);

    }

    public function testInvalidationData()
    {
        
        $response = $this->json('POST', route('categories.store', []));

        $this->assertInvalidationRequired($response);
        $this->assertInvalidationBoolean($response); 

        $response = $this->json('POST', route(
            'categories.store',
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        ));

        $this->assertInvalidationMax($response);

   
        $response = $this->json('PUT', route('categories.update', ['category' => $this->category->id]));

        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $this->category->id]),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]

        );

        $this->assertInvalidationMax($response); 
        $this->assertInvalidationBoolean($response); 
                    
    }

    public function testStore()
    {
        $response = $this->json('POST', route('categories.store', 
        [
            'name'=> 'test'
        ]));

        $id= $response->json('id');
        $category = $this->category::find('id');

       $response
       ->assertStatus(201)
       ->assertJson($category->toArray());
       $this->assertTrue($response->json('is_active'));
       $this->assertNull($response->json('description'));
     
       $response = $this->json('POST', route('categories.store', 
       [
           'name'=> 'test',
           'description' => 'descrição',
           'is_active' => false
       ]));

       $response

       ->assertJsonFragment(
           [
            'description' => 'descrição',
            'is_active' => false
           ]
           );
     

    }

    public function testUpdate()
    {
        $response = $this->json('PUT', route('categories.store', 
        [
            'name'=> 'test',
            'description' => 'descrição',
            'is_active' => true
        ]));

        $id= $response->json('id');
        $category = $this->category::find('id');

       $response
       ->assertStatus(200)
       ->assertJson($category->toArray())
       ->assertJsonFragment(
        [
            'description' => 'descrição',
            'is_active' => true
        ]);

        $response = $this->json('PUT', route('categories.store', 
        [
            'name'=> 'test',
            'description' => ''
   
        ]));
     
        $response
        ->assertJsonFragment(['description' => null]);

     

    }
    
    protected function assertInvalidationRequired(TestResponse $response)
    {

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrorFor(['name'])
        ->assertJsonMissingValidationErrors(['is_active'])
        ->assertJsonFragment(
            [
                Lang::trans('validation.required', ['attributte'=> 'name'])
            ]
            );

            $response = $this->json('POST',route('categories.store', 
            [
                'name' => str_repeat('a', 256),
                'is_acitve' => 'a'
            ]));
    }

    public function testDelete()
    {
        $category = Category::factory()->create();
        $response = $this->json('DELETE', route('categories.destroy', ['category' => "{$category->id}"]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        $this->assertNotNull(Category::withTrashed()->find($category->id));
    }
    protected function assertInvalidationMax(TestResponse $response)
    {

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrorFor(['name'])
            ->assertJsonFragment(
                [
                    Lang::trans('validation.max.string', ['attributte' => 'name', 'max' => 255])
                ]
            );
 
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrorFor(['is_active'])
            ->assertJsonFragment(
                [
                    Lang::trans('validation.boolean', ['attributte' => 'is active'])
                ]
            );  
    }
}
