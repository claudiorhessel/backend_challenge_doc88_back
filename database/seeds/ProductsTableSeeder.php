<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            'name' => 'Carne',
            'price' => '7.00',
            'photo_original_name' => 'carne.jpg',
            'photo_name' => 'carne.jpg',
            'photo_destination_path' => 'public/images',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Frango',
            'price' => '7.00',
            'photo_original_name' => 'frango.jpg',
            'photo_name' => 'frango.jpg',
            'photo_destination_path' => 'public/images',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Calabresa',
            'price' => '7.00',
            'photo_original_name' => 'calabresa.jpg',
            'photo_name' => 'calabresa.jpg',
            'photo_destination_path' => 'public/images',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Queijo',
            'price' => '7.00',
            'photo_original_name' => 'queijo.jpg',
            'photo_name' => 'queijo.jpg',
            'photo_destination_path' => 'public/images',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Camarão',
            'price' => '10.00',
            'photo_original_name' => 'camarao.jpg',
            'photo_name' => 'camarao.jpg',
            'photo_destination_path' => 'public/images',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Costela',
            'price' => '10.00',
            'photo_original_name' => 'costela.jpg',
            'photo_name' => 'costela.jpg',
            'photo_destination_path' => 'public/images',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Especial',
            'price' => '14.00',
            'photo_original_name' => 'especial.jpg',
            'photo_name' => 'especial.jpg',
            'photo_destination_path' => 'public/images',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
    }
}
