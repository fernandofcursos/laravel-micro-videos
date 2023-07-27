<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Carbon\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;


class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

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
       
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));
      
        $response
        ->assertStatus(200)
        ->assertJson([$this->category->toArray()]);

    }

    

    public function testStore()
    {
        $data =[
            "name" => 'test'
        ];
        $this->assertStore($data,$data + ['description' => null, 'is_active'=> true, 'deleted_at' => null]);

        $data =[
           'name'=> 'test',
           'description' => 'descrição',
           'is_active' => false
        ];         
        $this->assertStore($data,$data + ['description' => 'descrição', 'is_active'=> false]);
    }

    public function testUpdate()
    {

        // $this->category = Category::factory()->create([
        //     'description' => 'descrição',
        //     'is_active'  => false
        // ]);  

        $data =[
            'name' => 'teste',
            'description' => 'descrição',
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
        $category = Category::factory()->create();
        $response = $this->json('DELETE', route('categories.destroy', ['category' => "{$category->id}"]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        $this->assertNotNull(Category::withTrashed()->find($category->id));
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
        $response = $this->json('DELETE', route('categories.destroy', ['category' => $this->category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));

    }
    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        
        return route('categories.update', ['category' => $this->category->id]);

    }

    protected function model()
    {
        return Category::class;
    }

}
