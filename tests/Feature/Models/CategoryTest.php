<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
            , $categoryKeys
        );
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'test'
        ]);
        $category->refresh();
        $this->assertEquals('test', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'test',
            'description' => null
        ]);
        $category->refresh();
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test',
            'description' => 'description'
        ]);
        $category->refresh();
        $this->assertEquals('description', $category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'test',
            'is_active' => false
        ]);
        $category->refresh();
        $this->assertFalse($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'description',
            'is_active'   => false
        ]);

        $data = [
            'name' => 'new name',
            'description' => 'new description',
            'is_active' => true
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = factory(Category::class)->create();
        $category->delete();
        $this->assertNull(Category::find($category->id));

        $category->restore();
        $this->assertNotNull(Category::find($category->id));
    }
}
