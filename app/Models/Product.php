<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'company_id',
        'company_name',
        'price',
        'stock',
        'comment',
        'img_path',
    ];

    protected $attributes = [
        'comment' => ' ',
        'img_path' => ' '
    ];

    public function getCompanyNameById(){  
        $query = DB::table('products')
        ->join('companies', 'products.company_id', '=', 'companies.id')
        ->select('products.*', 'companies.company_name as company_name')
        ->get();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function registProduct($request, $img_path) {
        DB::table('products')->insert([
            'product_name' => $request->product_name,
            'company_id' => $request->company_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'comment' => $request->comment,
            'img_path' => $img_path,
            
        ]);
    }
}
