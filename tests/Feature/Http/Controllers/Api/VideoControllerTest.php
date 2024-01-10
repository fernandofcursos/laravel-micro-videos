<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Video;
use Carbon\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;


class VideoControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;
    private $sendData;
        

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = Video::factory()->create();
        $this->sendData = [
            'title' => 'titulo',
            'description' => 'descrição',
            'year_launched' => '2012',
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ];
    }

    public function testIndex()
    {

        $response = $this->get(route('videos.index'));

        $response
        ->assertStatus(200)
        ->assertJson([$this->video->toArray()]);
    }

    public function testInvalidationRequired()
    {
        $data =[
            'title' => '',
            'description'=>'',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => '' 
        ];

        $this->assertInvalidationStoreAction($data, 'required');
        $this->assertInvalidationUpdateAction($data , 'required');
    }

    public function testInvalidationMax()
    {
        $data=[
            'title' => str_repeat('a', 256)
        ];

        $this->assertInvalidationStoreAction($data,'max_string', ['max'=>255]);
        $this->assertInvalidationUpdateAction($data,'max_string', ['max'=>255]);

    }

    public function testInvalidationInteger()
    {
        $data=[
            'duration' => 's'
        ];

        $this->assertInvalidationStoreAction($data,'integer');
        $this->assertInvalidationUpdateAction($data,'integer');  
    }

    public function testInvalidationYearLaunchedField()
    {
        $data=[
            'year_launched' => 'a'
        ];

        $this->assertInvalidationStoreAction($data,'date_format', ['format' => 'Y']);
        $this->assertInvalidationUpdateAction($data,'date_format', ['format' => 'Y']); 
    }

    public function testInvalidationOpenedField()
    {
        $data=[
            'opened' => 's'
        ];

        $this->assertInvalidationStoreAction($data,'boolean');
        $this->assertInvalidationUpdateAction($data,'boolean'); 
    }

    public function testInvalidationRatingField()
    {
        $data=[
            'rating' => 0
        ];

        $this->assertInvalidationStoreAction($data,'in');
        $this->assertInvalidationUpdateAction($data,'in'); 
    } 
    public function testShow()
    {
       
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));
      
        $response
        ->assertStatus(200)
        ->assertJson([$this->video->toArray()]);

    }

    public function testSave()
    {
        $data =[
           [
            'sendData'=> $this->sendData,
            'sendData'=> $this->sendData + ['opened'=> true]
           ],
           [
            'sendData'=> $this->sendData + ['opened' =>true],
            'sendData'=> $this->sendData + ['opened'=> true]
           ],
           [
            'sendData'=> $this->sendData + ['rating' =>::Video=>RATING_LIST[1]],
            'sendData'=> $this->sendData + ['rating' =>::Video=>RATING_LIST[1]]
           ]
           ];

        foreach ($data as $key => $value) {
         $reponse = $this->assertStore(
            $valeu['sendData'],
            $valeu['test_data'] + ['deleted_at'=> null]
        );
         $reponse= assertJsonStructure(
            [
                'created_at',
                'updated_at'
            ]
            );
            $reponse = $this->assertUpdate(
                $value['sendData'],
                $value['test_data'] + ['deleted_at'=> null]);
            $reponse= assertJsonStructure(
               [
                   'created_at',
                   'updated_at'
               ]
               );
        }   
      

    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id'=> 'a'
        ];

        $this->assertInvalidationStoreAction($data, 'array');
        $this->assertInvalidationUpdateAction($data, 'array');

        $data = [
            'categories_id'=> [100]
        ];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exits');

    }
   
    public function testInvalidationGenresIdField()
    {
        $data = [
            'categories_id'=> 'a'
        ];

        $this->assertInvalidationStoreAction($data, 'array');
        $this->assertInvalidationUpdateAction($data, 'array');

        $data = [
            'categories_id'=> [100]
        ];

        $this->assertInvalidationStoreAction($data, 'exists');
        $this->assertInvalidationUpdateAction($data, 'exits');

    }
    public function testDelete()
    {
        $video = video::factory()->create();
        $response = $this->json('DELETE', route('categories.destroy', ['video' => "{$video->id}"]));
        $response->assertStatus(204);
        $this->assertNull(video::find($video->id));
        $this->assertNotNull(video::withTrashed()->find($video->id));
    }

  

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('categories.destroy', ['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNull(video::find($this->video->id));
        $this->assertNotNull(video::withTrashed()->find($this->video->id));

    }
    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        
        return route('videos.update', ['video' => $this->video->id]);

    }

    protected function model()
    {
        return video::class;
    }

}
  