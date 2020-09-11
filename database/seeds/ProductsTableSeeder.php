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
            'photo' => 'carne.jpg',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Frango',
            'price' => '7.00',
            'photo' => 'frango.jpg',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Calabresa',
            'price' => '7.00',
            'photo' => 'calabresa.jpg',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Queijo',
            'price' => '7.00',
            'photo' => 'queijo.jpg',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Camarão',
            'price' => '10.00',
            'photo' => 'camarao.jpg',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Costela',
            'price' => '10.00',
            'photo' => 'costela.jpg',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
        DB::table('products')->insert([
            'name' => 'Especial',
            'price' => '14.00',
            'photo' => 'especial.jpg',
            'type_id' => 1,
            'created_at' => Carbon::now()
        ]);
    }
}
