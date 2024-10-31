<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_flash_message_in_session_when_category_is_created()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(201);

        // Kiểm tra xem session có chứa thông báo thành công không
        $response->assertSessionHas('success', 'Category created successfully.');

        // Hoặc bạn có thể kiểm tra trực tiếp trong session bằng cách gọi session() helper
        $this->assertEquals('Category created successfully.', session('success'));
    }

    /** @test */
    public function it_stores_flash_error_message_if_category_already_exists()
    {
        // Tạo một category trước
        Category::create(['name' => 'Existing Category']);

        // Gửi yêu cầu tạo danh mục với tên đã tồn tại
        $response = $this->postJson('/api/categories', [
            'name' => 'Existing Category',
        ]);

        $response->assertStatus(409);

        // Kiểm tra xem session có chứa thông báo lỗi không
        $response->assertSessionHas('error', 'Category already exists.');
    }

    /** @test */
    public function it_stores_category_in_session_when_created()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Category in session',
        ]);

        $response->assertStatus(201);

        // Kiểm tra xem category vừa tạo có được lưu trong session không
        $category = Category::where('name', 'Category in session')->first();
        $this->assertNotNull($category);

        // Kiểm tra xem session có lưu category vừa tạo không
        $this->assertEquals($category->toArray(), session('last_created_category')->toArray());
    }

    /** @test */
    public function it_forgets_session_data_after_deleting_category()
    {
        $category = Category::create(['name' => 'Category to delete']);

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(200);

        // Kiểm tra xem session đã quên các key cần thiết sau khi xóa danh mục
        $this->assertFalse(session()->has('last_created_category'));
    }
}
