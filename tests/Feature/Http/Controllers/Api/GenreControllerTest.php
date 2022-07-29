<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = Genre::factory()->create();
    }

    public function testIndex()
    {

        $response = $this->get(route('genres.index'));

        $response
        ->assertStatus(200)
        ->assertJson([$this->genre->toArray()]);
    }


    public function testShow()
    {
        $genre = Genre::factory()->create();
       
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));
      
        $response
        ->assertStatus(200)
        ->assertJson([$this->genre->toArray()]);

    }

    public function testInvalidationData()
    {
        
        $response = $this->json('POST', route('genres.store', []));

        $this->assertInvalidationRequired($response);
        $this->assertInvalidationBoolean($response); 

        $response = $this->json('POST', route(
            'genres.store',
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        ));

        $this->assertInvalidationMax($response);

   
        $response = $this->json('PUT', route('genres.update', ['genre' => $this->genre->id]));

        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $this->genre->id]),
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
        $response = $this->json('POST', route('genres.store', 
        [
            'name'=> 'test'
        ]));

        $id= $response->json('id');
        $genre = $this->genre::find('id');

       $response
       ->assertStatus(201)
       ->assertJson($genre->toArray());
       $this->assertTrue($response->json('is_active'));
       $this->assertNull($response->json('name'));
     
       $response = $this->json('POST', route('genres.store', 
       [
           'name'=> 'test',
           'is_active' => false
       ]));

       $response

       ->assertJsonFragment(
           [
            'name' => 'test',
            'is_active' => false
           ]
           );
     

    }

    public function testUpdate()
    {
        $response = $this->json('PUT', route('genres.store', 
        [
            'name'=> 'test',
            'is_active' => true
        ]));

        $id= $response->json('id');
        $genre = $this->genre::find('id');

       $response
       ->assertStatus(200)
       ->assertJson($genre->toArray())
       ->assertJsonFragment(
        [
            'name' => 'teste_nome',
            'is_active' => true
        ]);

        $response = $this->json('PUT', route('genres.store', 
        [
            'name'=> ''
   
        ]));
     
        $response
        ->assertJsonFragment(['name' => null]);

     

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

            $response = $this->json('POST',route('genres.store', 
            [
                'name' => str_repeat('a', 256),
                'is_acitve' => 'a'
            ]));
    }

    public function testDelete()
    {
        $genre = Genre::factory()->create();
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => "{$genre->id}"]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($genre->id));
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
