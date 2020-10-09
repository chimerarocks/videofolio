<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    private $freshCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->freshCategory = new Category();
    }

    public function testFillable()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->freshCategory->getFillable());
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
        $this->assertEquals('string', $this->freshCategory->getKeyType());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->freshCategory->incrementing);
    }

    public function testDatesAttribute()
    {
        $dates         = ['deleted_at', 'created_at', 'updated_at'];
        $categoryDates = $this->freshCategory->getDates();
        $this->assertEqualsCanonicalizing($dates, $categoryDates);
    }

    public function testCastAttribute()
    {
        $casts         = ['is_active' => 'boolean'];
        $categoryCasts = $this->freshCategory->getCasts();
        $this->assertEqualsCanonicalizing($casts, $categoryCasts);
    }

}
