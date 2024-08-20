<?php

namespace App\Models;

// 使うツールを取り込んでいます。
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\support\Facades\DB;

// Productという名前のツール（クラス）を作っています。
class Product extends Model
{
    // ダミーレコードを代入する機能を使うことを宣言しています。
    use HasFactory;

    // モデルに関連づけるテーブル
    // protected $table = 'products';

    // 以下の情報（属性）を一度に保存したり変更したりできるように設定しています。
    // $fillable を設定しないと、Laravelはセキュリティリスクを避けるために、この一括代入をブロックします。
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
        //デフォルトのコメントと画像ファイルの空設定
        'comment' => ' ',
        'img_path' => ' '
    ];


    //内部結合を行うためのメソッドです
    public function getCompanyNameById(){
        // return DB::table('products')
        //     ->join('companies', 'products.company_id', '=', 'companies.id')
        //     ->get();

        return DB::table('products')
            ->join('companies', function($join) {
              $join->on('products.company_id', 'companies.id');
            })
            ->get()/**->query**/;
        }


    // Productモデルがsalesテーブルとリレーション関係を結ぶためのメソッドです
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Productモデルがcompaniesテーブルとリレーション関係を結ぶ為のメソッドです
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function registProduct($request, $img_path) {
        // 登録処理
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
