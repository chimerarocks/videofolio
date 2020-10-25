<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSave;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSave;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)
            ->assertJson([$this->genre->toArray()])
        ;
    }

    public function testShow()
    {
        $response = $this->get(route('genres.index', [
            'genre' => $this->genre->id
        ]));

        $response->assertStatus(200)
            ->assertJson([$this->genre->toArray()])
        ;
    }

    public function testInvalidationData()
    {
        /**
         * Validating if is_active is optional and if name is required
         */
        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        /**
         * Validating if is_active is a boolean and if name respects max limit
         */
        $data = [
            'name'      => str_repeat('a', 256)
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];
        $response = $this->assertStore($data, $data + [
            'is_active'   => true,
            'deleted_at'  => null
        ]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);

        $data = [
            'name' => 'test',
            'is_active' => false
        ];
        $this->assertStore($data, $data);
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'test',
            'is_active' => true
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
    }

    public function testDestroy() {
        $response = $this->json('DELETE', route('genres.destroy', [
            'genre' => $this->genre->id
        ]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    protected function model()
    {
        return Genre::class;
    }
}
