<?php

namespace Tests\Feature\Http\Controllers\Api;

use factory;
use Tests\TestCase;
use App\Models\CastMember;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = CastMember::factory()->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    public function testIndex()
    {

        $response = $this->get(route('cast_members.index'));

        $response
        ->assertStatus(200)
        ->assertJson([$this->castMember->toArray()]);
    }

    public function testInvalidationData()
    {
        
        $data =[
            'name' =>'',
            'type' => ''
           ];
           $this->assertInvalidationStoreAction($data,'required');
           $this->assertInvalidationUpdateAction($data,'required');
    
           $data=[
            'type' => 's'
           ] ;
    
           $this->assertInvalidationStoreAction($data,'in');
           $this->assertInvalidationUpdateAction($data,'in');
    
    }

    public function testShow()
    {
       $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMember->id]));
      
        $response
        ->assertStatus(200)
        ->assertJson([$this->castMember->toArray()]);

    }

    public function testStore()
    {
        $data =[
            [
                'name' => 'test',
                'type' => CastMember::TYPE_DIRECTOR
            ],
            [
                'name' => 'test',
                'type' => CastMember::TYPE_ACTOR
            ]
         ];
        
         foreach ($data as $key =>$value)
         {
            $response = $this->assertStore($value, $value + ['deleted_at'=>null]);
            $response->assertJsonStructure([
                'created_at', 'updated_at'
            ]);
         }




    }

    public function testUpdate()
    {
        // $this->castmember = CastMember::factory()->create([
        //     'type'  => CastMember::TYPE_DIRECTOR
        // ]);  

       $data = [
        'name' => 'test',
        'type' => CastMember::TYPE_ACTOR
       ];

       $response = $this->assertUpdate($data, $data+['deleted_at'=>null]);
       $response->assertJsonStructure([
        'created_at', 'updated_at'
       ]);


    }

    public function testDelete()
    {
        $genre = CastMember::factory()->create();
        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(204);
        $this->assertNull(CastMember::find($this->castMember->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->castmember->id));
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
        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(204);
        $this->assertNull(CastMember::find($this->castMember->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));

    }
    protected function routeStore()
    {
        return route('cast_members.store');
    }

    protected function routeUpdate()
    {
        
        return route('cast_members.update', ['cast_member' => $this->castMember->id]);

    }

    protected function model()
    {
        return CastMember::class;
    }
}
