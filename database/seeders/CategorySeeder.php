<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Cá', 'slug' => 'ca'],
            ['name' => 'Giáp xác', 'slug' => 'giap-xac'],
            ['name' => 'Thân mềm', 'slug' => 'than-mem'],
            ['name' => 'Thú biển', 'slug' => 'thu-bien'],
            ['name' => 'San hô và sứa', 'slug' => 'san-ho-va-sua'],
            ['name' => 'Khác', 'slug' => 'khac'],
        ];

        foreach ($categories as $item) {
            Category::updateOrCreate(
                ['slug' => $item['slug']],
                ['name' => $item['name']]
            );
        }
    }
}