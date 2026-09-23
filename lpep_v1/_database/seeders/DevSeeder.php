<?php

namespace Database\Seeders;

use App\Models\AppCustomer;
use App\Models\Brand;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createSupplier();
        $this->createCategory();
        $this->createBrand();
        $this->createProduct();
        $this->createAppCustomer();
    }

    private function createSupplier()
    {
        $contact = new Contact;
        $contact->type = 'supplier';
        $contact->name = 'Supplier 1';
        $contact->email = 'supplier@gmail.com';
        $contact->mobile = '01234567890';
        $contact->note = null;
        $contact->address = 'Uttara, Dhaka';
        $contact->save();
    }

    private function createCategory()
    {
        $category = new Category;
        $category->category_name = 'Category 1';
        $category->created_by = 1;
        $category->status = 1;
        $category->save();
    }

    private function createBrand()
    {
        $brand = new Brand();
        $brand->brand_name = 'Brand 1';
        $brand->created_by = 1;
        $brand->status = 1;
        $brand->save();
    }

    private function createProduct()
    {
        Product::query()
            ->insert([
                [
                    'product_name' => 'Cattle Feed (S)',
                    'barcode' => nextProductBarcode(),
                    'unit_id' => 4,
                    'category_id' => 1,
                    'brand_id' => 1,
                    'alert_quantity' => null,
                    'selling_price' => 70,
                    'other_price' => null,
                    'vat_group_id' => null,
                    'created_by' => 1,
                    'product_description' => null,
                    'status' => 1,
                    'image' => '',
                    'discount_selling_price' => 70,
                ],
                [
                    'product_name' => 'Cow Milk (C)',
                    'barcode' => nextProductBarcode() + 1,
                    'unit_id' => 4,
                    'category_id' => 1,
                    'brand_id' => 1,
                    'alert_quantity' => null,
                    'selling_price' => 80,
                    'other_price' => null,
                    'vat_group_id' => null,
                    'created_by' => 1,
                    'product_description' => null,
                    'status' => 1,
                    'image' => '',
                    'discount_selling_price' => 80,
                ],
            ]);
    }

    private function createAppCustomer()
    {
        $customer = AppCustomer::query()->create([
            'agent_id' => 2,
            'name' => 'App Customer 1',
            'mobile' => '01234567892',
            'email' => null,
            'password' => Hash::make(123456),
            'created_by' => 2
        ]);

        $customer->farms()->create([
            'name' => 'AC1 Farm1',
            'created_by' => 2
        ]);
    }
}
