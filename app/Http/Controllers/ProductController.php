<?php

// まずは必要なモジュールを読み込んでいます。今回はProductとCompanyの情報と、リクエストの情報が必要です。
namespace App\Http\Controllers;

use App\Models\Product; // Productモデルを現在のファイルで使用できるようにするための宣言です。
use App\Models\Company; // Companyモデルを現在のファイルで使用できるようにするための宣言です。
use Illuminate\Http\Request; // Requestクラスという機能を使えるように宣言します
// Requestクラスはブラウザに表示させるフォームから送信されたデータをコントローラのメソッドで引数として受け取ることができます。
use DB;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller 
{
    
    public function index(Request $request)
    {
        // Productモデルに基づいてクエリビルダを初期化
        $query = Product::query();
        // この行の後にクエリを逐次構築していきます。
        // そして、最終的にそのクエリを実行するためのメソッド（例：get(), first(), paginate() など）を呼び出すことで、データベースに対してクエリを実行します。
    
        // 商品名の検索キーワードがある場合、そのキーワードを含む商品をクエリに追加
        if($search = $request->search){
            $query->where('product_name', 'LIKE', "%{$search}%");
        }
    
        // 最小価格が指定されている場合、その価格以上の商品をクエリに追加
        if($min_price = $request->min_price){
            $query->where('price', '>=', $min_price);
        }
    
        // 最大価格が指定されている場合、その価格以下の商品をクエリに追加
        if($max_price = $request->max_price){
            $query->where('price', '<=', $max_price);
        }
    
        // 最小在庫数が指定されている場合、その在庫数以上の商品をクエリに追加
        if($min_stock = $request->min_stock){
            $query->where('stock', '>=', $min_stock);
        }
    
        // 最大在庫数が指定されている場合、その在庫数以下の商品をクエリに追加
        if($max_stock = $request->max_stock){
            $query->where('stock', '<=', $max_stock);
        }
    
        // ソートのパラメータが指定されている場合、そのカラムでソートを行う
        if($sort = $request->sort){
            $direction = $request->direction == 'desc' ? 'desc' : 'asc'; 
    // もし $request->direction の値が 'desc' であれば、'desc' を返す。
    // そうでなければ'asc' を返す
            $query->orderBy($sort, $direction);
    // orderBy('カラム名', '並び順')
        }
    
        // 上記の条件(クエリ）に基づいて商品を取得し、10件ごとのページネーションを適用
        $products = $query->paginate(10)->appends($request->all());
    
        // 商品一覧ビューを表示し、取得した商品情報をビューに渡す
        return view('products.index', ['products' => $products]);
    }

    public function create()
    {
        // 商品作成画面における会社情報の取得
        $companies = Company::all();

        // 商品登録画面の表示
        return view('products.create', compact('companies'));
    }

    // 送られたデータをデータベースに保存するメソッド
    public function store(Request $request) 
    {
        // バリデーション
        $request->validate([
            'company_id' => 'required', //requiredは必須入力
            'product_name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'comment' => 'nullable', //'nullable'はnone許可
            'img_path' => 'nullable|image|max:2048', // 画像ファイル、最大サイズ2048kb
        ]);
        // 必須項目が一部未入力の場合、フォームの画面を再表示かつ、警告メッセージを表示

        //新規のプロダクトインスタンスを作成
        $model = new Product();

        //トランザクション開始
        DB::beginTransaction();

        try{

            $image = $request->file('img_path');

            if($image){
            
                $filename = $image->getClientOriginalName();
                $filePath = $image->storeAs('storage', $filename, 'public');
                $img_path = $filePath;
            
            } else {
            
                $img_path = null;
            
            }
            // dd($img_path); /*img_pathには画像がちゃんと引き渡されている*/
            
            $model->registProduct($request,$img_path);

            // dd($request);
            DB::commit();

        } catch(Exception $e) { /** 例外処理 **/

            DB::rollBack();
            Log::error($e);

        };

        //二重送信防止
        $request->session()->regenerateToken();

        // 商品一覧画面にリダイレクト
        return redirect('products');
    }


    public function show(Product $product)
    //(Product $product) 指定されたIDで商品をデータベースから自動的に検索し、その結果を $product に割り当てます。
    {
        // 商品詳細画面を表示します。その際に、商品の詳細情報を画面に渡します。
    //　ビューへproductという変数が使えるように値を渡している
    // ['product' => $product]でビューでproductを使えるようにしている
    // compact('products')と行うことは同じであるためどちらでも良い
    }


    public function edit(Product $product)
    {
        // 商品編集画面における会社情報の取得します。
        $companies = Company::all();

        // 商品編集画面の表示
        return view('products.edit', compact('product', 'companies'));
    }



    
    public function update(Request $request, Product $product)
    {
        // バリデーション設定
        $request->validate([
            'product_name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'comment' => 'nullable', //'nullable'はnone許可
            'img_path' => 'nullable|image|max:2048', // 画像ファイル、最大サイズ2048kb
        ]);
    
        //トランザクション開始
        DB::beginTransaction();

        // 画像ファイルインスタンス取得
        // $image = $request->file('img_path');
        // $image = $request->file('img_path');

        // // 現在の画像へのパスをセット
        // $filePath = $product->image;

        try{

        // //     $image = '';           // 選択された画像
        // //     $filePath  = $product->img_path; // 現在の画像ファイルパス

        // //     //画像更新処理 
        // //     if ($request->hasFile('img_path')) {
        // //         // 現在の画像ファイル削除
        // //         if ($filePath !== '' && !is_null($filePath)) {
        // //             Storage::disk('public')->delete($filePath);
        // //         }
        // //         // 選択画像ファイルを保存してパスをセット
        // //         $image = $request->file('img_path')->store('img_path', 'public');
        // //         $data['img_path'] = $image;
        // //     }
        // //     // 更新
        // //     $product->update($data);
        // //     return redirect()->route('products.index')->with('message', '投稿の更新が完了しました。');

                    
            // 商品情報の更新、既存の情報を新しい情報に書き換える
                $product->product_name = $request->product_name;
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->comment = $request->comment;
        
            DB::commit();
            
            // }

        } catch(Exception $e) { /** 例外処理 **/

            DB::rollBack();
            Log::error($e);
        
        };
       
        //二重送信防止
        $request->session()->regenerateToken();

        // 更新した商品の保存
        $product->update();
        // モデルインスタンスである$productに対して行われた変更をデータベースに保存するためのメソッド(機能)です。

        // 全ての処理が終わったら、商品一覧画面にリダイレクト
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
        // ビュー画面にメッセージを代入した変数(success)を送ります
    } 

/** 更新機能でやりたいこと 
 * ① 商品一覧→編集機能ページからの項目編集と更新
 * ② 既に更新済み、あるいは更新中の項目が操作されたときにエラーが生じるようにしたい
 * 　あるいは同時に更新ボタンを押したときに先に操作された機能のみ実行されるようにしたい
 * **/



    public function destroy(Product $product)
    //(Product $product) 指定されたIDで商品をデータベースから自動的に検索し、その結果を $product に割り当てます。
    {

        //トランザクション開始
        DB::beginTransaction();

        try{

            // 商品の削除
            $product->delete();

            DB::commit();

        } catch(Exception $e) {

            DB::rollBack();
            Log::error($e);

        };

            // 商品の削除
            // $product->delete();

    //     // 全ての処理が終わったら、商品一覧画面にリダイレクト
        return redirect('/products')
            ->with('success', 'Product deleted successfully');
    //     //URLの/productsを検索します
    //     //products/がなくても検索できます
    // }
    }

/** 削除機能でやりたいこと 
 * ① 商品一覧からの項目削除
 * ② 既に削除済みの項目、削除中の項目が操作されたときにエラーが生じるようにしたい
 * 　あるいは同時に削除ボタンを押したときに先に操作された機能のみ実行されるようにしたい
 * **/
}