<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)
            ->assertJson([$category->toArray()])
        ;
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index', [
            'category' => $category->id
        ]));

        $response->assertStatus(200)
            ->assertJson([$category->toArray()])
        ;
    }

    public function testInvalidationData()
    {
        /**
         * Validating if is_active is a optiomal and if name is required
         */
        $response = $this->json('POST', route('categories.store'), []);
        $this->assertInvalidationRequired($response);

        /**
         * Validating if is_active is a boolean and if name respects max limit
         */
        $response = $this->json('POST', route('categories.store'), [
            'name'      => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $category = factory(Category::class)->create();
        $response = $this->json('PUT',
            route('categories.update', [
                'category' => $category->id
            ]),
            []
        );
        $this->assertInvalidationRequired($response);

        $response = $this->json('PUT',
            route('categories.update', [
                'category' => $category->id
            ]), [
            'name'      => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

    }

    public function assertInvalidationRequired(TestResponse $response) {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])
            ])
        ;
    }

    public function assertInvalidationMax(TestResponse $response) {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', [
                    'attribute' => 'name',
                    'max' => 255
                ])
            ])
        ;
    }

    public function assertInvalidationBoolean(TestResponse $response) {
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.boolean', [
                    'attribute' => 'is active'
                ])
            ])
        ;
    }
}
