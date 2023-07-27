<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\TestCase;

class GenreTest extends TestCase
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
        CastMember::factory(1)->create();
        $genre = CastMember::all();
        $this->assertCount(1, $genre);

        $GenreKey = array_keys($genre->first()->getAttributes());

        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
               'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $GenreKey
        );
    }

    public function test_create()
    {
        $Genre = CastMember::create(
            [
                'name' => 'teste'
            ]
        );

        $Genre->refresh();

        $this->assertEquals('teste', $Genre->name);
        $this->assertTrue($Genre->is_active);
        $this->assertTrue(Uuid::isValid($Genre->id));



        $Genre = CastMember::create([
            'name' => 'Genre1',
            'is_active' => false,
        ]);
        $this->assertFalse($Genre->is_active);

        $Genre = CastMember::create([
            'name' => 'Genre1',
            'is_active' => true,
        ]);
        $this->assertTrue($Genre->is_active);
    }

    public function test_update()
    {
        $Genre = CastMember::factory()->create(
            [
                'name' => 'test_genre',
                'is_active'  => false
            ]
        )->first();

        $data =
            [
                'name' => 'test_name_updated',
                'is_active'  => true
            ];
        $Genre->update($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $Genre->{$key});
        }
    }

    public function testDelete()
    {
        $Genre = CastMember::factory()->create()->first();
        $Genre->delete();
        $this->assertNotNull($Genre->deleted_at);
    }

    function isValidUuid( $uuid ) {
    
        if (!is_string($uuid) || (preg_match($this->uuid_validation_regex,$uuid) !== 1)) {
            return false;
        }
    
        return true;
    }

    public function testValidatedUuid()
    {

        $categoria = CastMember::factory()->create(
            [
                'name' => 'genero Teste UUID'
            ])->first();
        $uuid = $categoria->id;
        $uuidValid = $this->isValidUuid($uuid);
       $this->assertTrue($uuidValid);

    }
}
