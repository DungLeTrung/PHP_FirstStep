<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class OrderSessionTest extends TestCase
{
    use RefreshDatabase;

    public function testOrderSessionDataIsStored()
    {
        // Giả lập một user
        $user = User::factory()->create();

        // Giả lập đăng nhập
        $this->actingAs($user);

        // Gửi request để tạo đơn hàng và lưu dữ liệu vào session
        $response = $this->post('/api/orders', [
            'products' => [
                ['id' => 1, 'quantity' => 2],
                ['id' => 2, 'quantity' => 1],
            ]
        ]);

        // Kiểm tra xem session có chứa dữ liệu 'order_products' không
        $response->assertSessionHas('order_products');

        // Kiểm tra nội dung trong session
        $this->assertEquals(
            session('order_products'),
            json_encode([
                ['id' => 1, 'quantity' => 2],
                ['id' => 2, 'quantity' => 1],
            ])
        );
    }
}
