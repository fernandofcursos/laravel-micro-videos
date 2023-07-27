<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\CastMember;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CastmemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $castmember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castmember = CastMember::factory()->create();
    }

    public function testIndex()
    {

        $response = $this->get(route('genres.index'));

        $response
        ->assertStatus(200)
        ->assertJson([$this->castmember->toArray()]);
    }


    public function testShow()
    {
       $response = $this->get(route('genres.show', ['genre' => $this->castmember->id]));
      
        $response
        ->assertStatus(200)
        ->assertJson([$this->castmember->toArray()]);

    }

    public function testStore()
    {
        $data =[
            "name" => 'test'
        ];
        $this->assertStore($data,$data + ['is_active'=> true, 'deleted_at' => null]);

        $data =[
           'name'=> 'test',
           'description' => 'descrição',
           'is_active' => false
        ];         
        $this->assertStore($data,$data + [ 'is_active'=> false]);



    }

    public function testUpdate()
    {
        // $this->castmember = CastMember::factory()->create([
        //     'is_active'  => false
        // ]);  

        $data =[
            'name' => 'teste',
            'is_active' => true
        ];

        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
        
        $data =[
            'name' => 'teste',
            'description' => ''
        ];

        $response = $this->assertUpdate($data, array_merge($data + ['description' => null]));  
        
        $data['description'] = 'teste';
        $response = $this->assertUpdate($data, array_merge($data + ['description' => 'teste']));  
       
        $data['description'] = null;
        $response = $this->assertUpdate($data, array_merge($data + ['description' => null]));  

     

    }

    public function testDelete()
    {
        $genre = CastMember::factory()->create();
        $response = $this->json('DELETE', route('categories.destroy', ['category' => "{$genre->id}"]));
        $response->assertStatus(204);
        $this->assertNull(CastMember::find($genre->id));
        $this->assertNotNull(CastMember::withTrashed()->find($genre->id));
    }

    public function testInvalidationData()
    {
        
        $data =[
            'name' =>';'
           ];
           $this->assertInvalidationStoreAction($data,'required');
           $this->assertInvalidationUpdateAction($data,'required');
    
           $data=[
            'name' => str_repeat('a', 256)
           ] ;
    
           $this->assertInvalidationStoreAction($data,'max.string',['max'=>255]);
           $this->assertInvalidationUpdateAction($data,'max.string',['max'=>255]);
           $data=[
            'is_active' => 'a'
           ] ;
           $this->assertInvalidationStoreAction($data,'boolean');
           $this->assertInvalidationUpdateAction($data,'boolean');
                    
    }
    
    protected function assertInvalidationRequired(TestResponse $response)
    {

        $this->assertInvalidationFields(
            $response, ['name'],'required'
        );
        $response->assertJsonMissingValidationErrors(['is_active']);   

    }

 

    protected function assertInvalidationMax(TestResponse $response)
    {

        $this
            ->assertInvalidationFields(
                $response, ['name'],'max.string',['max' => 255]
            );

 
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {

        $this
        ->assertInvalidationFields(
            $response, ['is_active'],'boolean'
        );
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $this->castmember->id]));
        $response->assertStatus(204);
        $this->assertNull(CastMember::find($this->castmember->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->castmember->id));

    }
    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        
        return route('categories.update', ['category' => $this->castmember->id]);

    }

    protected function model()
    {
        return CastMember::class;
    }
}
