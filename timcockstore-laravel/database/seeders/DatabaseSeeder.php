<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создание тестовых пользователей
        User::create([
            'name' => 'Admin Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'Support User',
            'email' => 'support@example.com',
            'password' => Hash::make('password'),
            'role' => 'support',
        ]);

        User::create([
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);

        // Создание категорий
        $categories = [
            ['name' => 'Смартфоны', 'description' => 'Мобильные телефоны и смартфоны'],
            ['name' => 'Ноутбуки', 'description' => 'Портативные компьютеры'],
            ['name' => 'Планшеты', 'description' => 'Планшетные компьютеры'],
            ['name' => 'Аксессуары', 'description' => 'Аксессуары для гаджетов'],
            ['name' => 'ТВ', 'description' => 'Телевизоры'],
        ];

        $categoryModels = [];
        foreach ($categories as $category) {
            $categoryModels[] = Category::create($category);
        }

        // Создание товаров (используя существующие изображения)
        $products = [
            ['name' => 'iPhone 14', 'price' => 79999, 'image' => 'iphone14.jpg', 'description' => 'Новый iPhone 14 с улучшенной камерой', 'category_id' => $categoryModels[0]->id],
            ['name' => 'Samsung Galaxy S23', 'price' => 69999, 'image' => 'galaxy_s23.jpg', 'description' => 'Флагманский смартфон Samsung', 'category_id' => $categoryModels[0]->id],
            ['name' => 'Xiaomi Redmi Note 12', 'price' => 19999, 'image' => 'redmi_note12.jpg', 'description' => 'Бюджетный смартфон с хорошей камерой', 'category_id' => $categoryModels[0]->id],
            
            ['name' => 'MacBook Air M1', 'price' => 99999, 'image' => 'macbook_air_m1.jpg', 'description' => 'Ультратонкий ноутбук Apple', 'category_id' => $categoryModels[1]->id],
            ['name' => 'ASUS VivoBook', 'price' => 59999, 'image' => 'asus_vivobook.jpg', 'description' => 'Легкий и производительный ноутбук', 'category_id' => $categoryModels[1]->id],
            ['name' => 'Lenovo IdeaPad 3', 'price' => 34999, 'image' => 'lenovo_ideapad3.jpg', 'description' => 'Надежный ноутбук для работы', 'category_id' => $categoryModels[1]->id],
            
            ['name' => 'iPad 10.2', 'price' => 34999, 'image' => 'ipad_102.jpg', 'description' => 'Универсальный планшет Apple', 'category_id' => $categoryModels[2]->id],
            ['name' => 'Samsung Galaxy Tab S8', 'price' => 54999, 'image' => 'tab_s8.jpg', 'description' => 'Мощный планшет для мультимедиа', 'category_id' => $categoryModels[2]->id],
            
            ['name' => 'AirPods Pro 3', 'price' => 24999, 'image' => 'airpods3.jpg', 'description' => 'Беспроводные наушники премиум класса', 'category_id' => $categoryModels[3]->id],
            ['name' => 'Чехол для телефона', 'price' => 1999, 'image' => 'case.jpg', 'description' => 'Защитный чехол для смартфона', 'category_id' => $categoryModels[3]->id],
            ['name' => 'Power Bank Xiaomi', 'price' => 3999, 'image' => 'powerbank_xiaomi.jpg', 'description' => 'Портативное зарядное устройство', 'category_id' => $categoryModels[3]->id],
            
            ['name' => 'LG OLED 55"', 'price' => 89999, 'image' => 'lg_oled55.jpg', 'description' => 'Премиум OLED телевизор', 'category_id' => $categoryModels[4]->id],
            ['name' => 'Samsung QLED 65"', 'price' => 79999, 'image' => 'samsung_qled.jpg', 'description' => 'Яркий QLED телевизор высокого разрешения', 'category_id' => $categoryModels[4]->id],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
