<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Carbon\Traits\Cast;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSave;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSave;

    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));

        $response->assertStatus(200)
            ->assertJson([$this->castMember->toArray()])
        ;
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.index', [
            'cast_member' => $this->castMember->id
        ]));

        $response->assertStatus(200)
            ->assertJson([$this->castMember->toArray()])
        ;
    }

    public function testInvalidationData()
    {
        /**
         * Validating if is_active is optional and if name is required
         */
        $data = [
            'name' => '',
            'type' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'type' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testStore()
    {
        $data = [
            [
                'name' => 'test',
                'type' => CastMember::TYPE_DIRECTOR
            ],
            [
                'name' => 'test',
                'type' => CastMember::TYPE_ACTOR
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore($value, $value + ['deleted_at' => null]);
            $response->assertJsonStructure([
                'created_at', 'updated_at'
            ]);
        };
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
    }

    public function testDestroy() {
        $response = $this->json('DELETE', route('cast_members.destroy', [
            'cast_member' => $this->castMember->id
        ]));
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
