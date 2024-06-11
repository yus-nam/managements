<?php

namespace App\Models;

// 使うツールを取り込んでいます。
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

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
        'company_id',
        'product_name',
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
            'price' => $request->price,
            'stock' => $request->stock,
            'company_id' => $request->company_id,
            'comment' => $request->comment,
            'img_path' => $img_path,
        ]);
    }

}
