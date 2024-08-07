<?php

namespace App\Models;

// 使うツールを取り込んでいます。
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Companyという名前のツール（クラス）を作っています。
class Company extends Model
{
    use HasFactory;

    // public function getCompany()
    // {
    //     $companies = Company::pluck('company_id', 'company_name');
    //     return $companies;
    
    // }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}