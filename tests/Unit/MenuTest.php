<?php

namespace Tests\Unit;

use App\Models\Menu;
use PHPUnit\Framework\TestCase;

class MenuTest extends TestCase
{
    private Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menu = new Menu([
            'nama' => 'Test Menu',
            'deskripsi' => 'Test Description',
            'ketersediaan' => '10', // String to test casting
            'harga' => '15000',     // String to test casting
            'gambar_path' => 'menu-images/test.jpg'
        ]);
    }

    public function test_menu_can_be_instantiated(): void
    {
        $this->assertInstanceOf(Menu::class, $this->menu);
    }

    public function test_menu_has_correct_fillable_attributes(): void
    {
        $fillable = $this->menu->getFillable();

        $this->assertEquals([
            'nama',
            'deskripsi',
            'ketersediaan',
            'harga',
            'gambar_path'
        ], $fillable);
    }

    public function test_menu_attributes_are_cast_correctly(): void
    {
        $this->assertIsInt($this->menu->ketersediaan);
        $this->assertIsInt($this->menu->harga);
        $this->assertEquals(10, $this->menu->ketersediaan);
        $this->assertEquals(15000, $this->menu->harga);
    }

    public function test_menu_table_name_is_correct(): void
    {
        $this->assertEquals('menus', $this->menu->getTable());
    }

    public function test_menu_primary_key_is_correct(): void
    {
        $this->assertEquals('id', $this->menu->getKeyName());
    }

    public function test_menu_primary_key_type_is_correct(): void
    {
        $this->assertEquals('int', $this->menu->getKeyType());
    }

    public function test_menu_timestamps_are_disabled(): void
    {
        $this->assertFalse($this->menu->timestamps);
    }

    public function test_menu_incrementing_is_enabled(): void
    {
        $this->assertTrue($this->menu->incrementing);
    }

    public function test_menu_attributes_can_be_accessed(): void
    {
        $this->assertEquals('Test Menu', $this->menu->nama);
        $this->assertEquals('Test Description', $this->menu->deskripsi);
        $this->assertEquals(10, $this->menu->ketersediaan);
        $this->assertEquals(15000, $this->menu->harga);
        $this->assertEquals('menu-images/test.jpg', $this->menu->gambar_path);
    }

    public function test_menu_attributes_can_be_set(): void
    {
        $this->menu->nama = 'New Name';
        $this->menu->harga = 20000;

        $this->assertEquals('New Name', $this->menu->nama);
        $this->assertEquals(20000, $this->menu->harga);
    }
}
