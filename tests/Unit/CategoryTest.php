<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    public function testFillable()
    {
        $fillable = ['name', 'description', 'is_active'];
        $category = new Category();
        $this->assertEquals($fillable, $category->getFillable());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }

    public function testKeyTypeAttribute()
    {
        $category = new Category();
        $this->assertEquals('string', $category->getKeyType());
    }

    public function testIncrementingAttribute()
    {
        $category = new Category();
        $this->assertFalse($category->incrementing);
    }

    public function testDatesAttribute()
    {
    $category      = new Category();
        $dates         = ['deleted_at', 'created_at', 'updated_at'];
        $categoryDates = $category->getDates();
        sort($dates);
        sort($categoryDates);
        $this->assertEquals($dates, $categoryDates);
    }

}
