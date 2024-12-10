<?php

namespace Tests\Feature;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    private function createMenu($overrides = []): Menu
    {
        return Menu::create(array_merge([
            'nama' => 'Test Menu',
            'deskripsi' => 'Test Description',
            'ketersediaan' => 10,
            'harga' => 15000,
            'gambar_path' => 'menu-images/test.jpg'
        ], $overrides));
    }

    public function test_can_create_menu_without_image(): void
    {
        $response = $this->postJson('/api/menus', [
            'nama' => 'Nasi Goreng',
            'deskripsi' => 'Nasi goreng spesial',
            'ketersediaan' => 20,
            'harga' => 25000
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'menu' => [
                    'id',
                    'nama',
                    'deskripsi',
                    'ketersediaan',
                    'harga',
                    'gambar_url'
                ]
            ]);

        $this->assertDatabaseHas('menus', [
            'nama' => 'Nasi Goreng',
            'deskripsi' => 'Nasi goreng spesial',
            'ketersediaan' => 20,
            'harga' => 25000
        ]);
    }

    public function test_can_create_menu_with_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('menu.jpg');

        $response = $this->postJson('/api/menus', [
            'nama' => 'Nasi Goreng',
            'deskripsi' => 'Nasi goreng spesial',
            'ketersediaan' => 20,
            'harga' => 25000,
            'gambar' => $file
        ]);

        $response->assertStatus(201);

        // Assert the file was stored
        Storage::disk('public')->assertExists('menu-images/' . $file->hashName());
    }

    public function test_cannot_create_menu_with_invalid_data(): void
    {
        $response = $this->postJson('/api/menus', [
            'nama' => '', // required field
            'deskripsi' => str_repeat('a', 101), // exceeds max length
            'ketersediaan' => 'invalid', // should be integer
            'harga' => 'invalid' // should be integer
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'ref_code',
                'detail'
            ]);
    }

    public function test_can_get_all_menus(): void
    {
        $this->createMenu();
        $this->createMenu(['nama' => 'Second Menu']);

        $response = $this->getJson('/api/menus');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'menus')
            ->assertJsonStructure([
                'menus' => [
                    '*' => [
                        'id',
                        'nama',
                        'deskripsi',
                        'ketersediaan',
                        'harga',
                        'gambar_url'
                    ]
                ]
            ]);
    }

    public function test_can_get_menu_by_id(): void
    {
        $menu = $this->createMenu();

        $response = $this->getJson("/api/menus/{$menu->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'menu' => [
                    'id',
                    'nama',
                    'deskripsi',
                    'ketersediaan',
                    'harga',
                    'gambar_url'
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_menu(): void
    {
        $response = $this->getJson('/api/menus/999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Menu tidak ditemukaan',
                'ref_code' => 'RESOURCE_NOT_FOUND'
            ]);
    }

    public function test_can_update_menu_without_image(): void
    {
        $menu = $this->createMenu();

        $response = $this->patchJson("/api/menus/{$menu->id}", [
            'nama' => 'Updated Menu',
            'harga' => 30000
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('menu.nama', 'Updated Menu')
            ->assertJsonPath('menu.harga', 30000);

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'nama' => 'Updated Menu',
            'harga' => 30000
        ]);
    }

    public function test_can_update_menu_with_image(): void
    {
        Storage::fake('public');

        $menu = $this->createMenu();
        $file = UploadedFile::fake()->image('new-menu.jpg');

        $response = $this->patchJson("/api/menus/{$menu->id}", [
            'nama' => 'Updated Menu',
            'gambar' => $file
        ]);

        $response->assertStatus(200);

        // Assert the new file was stored
        Storage::disk('public')->assertExists('menu-images/' . $file->hashName());

        // Assert the old file would have been deleted (if it existed)
        Storage::disk('public')->assertMissing('menu-images/test.jpg');
    }

    public function test_cannot_update_non_existent_menu(): void
    {
        $response = $this->patchJson('/api/menus/999', [
            'nama' => 'Updated Menu'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Menu tidak ditemukaan',
                'ref_code' => 'RESOURCE_NOT_FOUND'
            ]);
    }

    public function test_cannot_update_menu_with_invalid_data(): void
    {
        $menu = $this->createMenu();

        $response = $this->patchJson("/api/menus/{$menu->id}", [
            'nama' => str_repeat('a', 51), // exceeds max length
            'harga' => 'invalid' // should be integer
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'ref_code',
                'detail'
            ]);
    }

    public function test_can_delete_menu(): void
    {
        Storage::fake('public');

        $menu = $this->createMenu();

        $response = $this->deleteJson("/api/menus/{$menu->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);

        // Assert the image would have been deleted (if it existed)
        Storage::disk('public')->assertMissing('menu-images/test.jpg');
    }

    public function test_cannot_delete_non_existent_menu(): void
    {
        $response = $this->deleteJson('/api/menus/999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Menu tidak ditemukaan',
                'ref_code' => 'RESOURCE_NOT_FOUND'
            ]);
    }
}
