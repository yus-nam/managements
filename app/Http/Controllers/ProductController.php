<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request; 
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller 
{
    
    public function index(Request $request)
    {

        Log::info('Index method called', $request->all());
    
        // 会社の情報を取得
        $companies = Company::all();
    
        // クエリビルダの初期化
        $query = Product::query();  // ここでクエリビルダを初期化します
    
        // 商品名での検索
        if ($search = $request->search) {
            $query->where('product_name', 'LIKE', "%{$search}%");
        }
    
        // メーカーでの検索
        if ($company_id = $request->company_name) {
            $query->where('company_id', $company_id);
        }
    
        // 最小価格
        if ($min_price = $request->min_price) {
            $query->where('price', '>=', $min_price);
        }
    
        // 最大価格
        if ($max_price = $request->max_price) {
            $query->where('price', '<=', $max_price);
        }
    
        // 最小在庫数
        if ($min_stock = $request->min_stock) {
            $query->where('stock', '>=', $min_stock);
        }
    
        // 最大在庫数
        if ($max_stock = $request->max_stock) {
            $query->where('stock', '<=', $max_stock);
        }
    
        // ページネーションと検索条件を保持したリンクの生成
        $products = $query->paginate(10)->appends($request->all()); // ここでページネーションと検索条件保持を追加
    
        // ビューへのデータを返す
        return view('products.index', [
            'products' => $products,
            'companies' => Company::all() // 会社のデータもビューに渡す
        ]);
    }
    


    public function create()
    {
        // 商品作成画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品作成画面を表示します。その際に、先ほど取得した全ての会社情報を画面に渡します。
        return view('products.create', compact('companies'));
    }



    // 送られたデータをデータベースに保存するメソッド
    public function store(Request $request) 
    {

        Log::info('Store method called', $request->all());

        // バリデーション
        $request->validate([
            'company_id' => 'required', //requiredは必須入力
            'product_name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'comment' => 'nullable', //'nullable'はそのフィールドが未入力でもOKという意味です
            'img_path' => 'nullable|image|max:2048', // 画像ファイル、最大サイズ2048kb
        ]);

        // 必須項目が一部未入力の場合、フォームの画面を再表示かつ、警告メッセージを表示
        Log::info('Validation passed', $request->all());


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
            return view('products.show', ['product' => $product]);
        //　ビューへproductという変数が使えるように値を渡している
        // ['product' => $product]でビューでproductを使えるようにしている
        // compact('products')と行うことは同じであるためどちらでも良い
    }



    public function edit(Product $product)
    {
        // 商品編集画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品編集画面の表示
        return view('products.edit', compact('product', 'companies'));
    }

    // 登録機能もちゃんと機能している→反応しなくなった

   
    
    public function update(Request $request, Product $product)
    {
        // バリデーション設定
        $validatedData = $request->validate([
            // 'product_name' => 'required',
            'product_name' => 'required|string|max:255',

            //追加項目
            'company_name' => 'required|exists:companies,id', // 'company_name'が'companies'テーブルの'id'に存在するか確認

            // 'price' => 'required',
            'price' => 'required|numeric',
            // 'stock' => 'required',
            'stock' => 'required|integer',
            // 'comment' => 'nullable', //'nullable'はnone許可
            'comment' => 'nullable|string',

            'img_path' => 'nullable|image|max:2048', // 画像ファイル、最大サイズ2048kb
        ]);
    
        //トランザクション開始
        DB::beginTransaction();

        try{
        //画像変更の確認
            if(!empty($request->img_path)) {
                // $file = $request->file('path');
                $file = $request->file('img_path');

                // $filename = $file->getClientOriginalName();
                $filename = $file->getClientOriginalName();

                // $request->file('path')->storeAs('public',$filename);
                $request->file('img_path')->storeAs('storage', $filename, 'public');

                
                // $post->path = '/storage/' . $filename;
                $product->img_path = '/storage/'. $filename; 
                
                // $img_path = $filePath;

            }

            // 商品情報の更新、既存の情報を新しい情報に書き換える
                $product->product_name = $request->product_name;
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->comment = $request->comment;
        
            DB::commit();
            
            // }

        } catch(Exception $e) { 
            DB::rollBack();
            Log::error($e);
        
        };
       
        $request->session()->regenerateToken();

        
        //追加項目
        $product->update([
            'product_name' => $validatedData['product_name'],
            'company_id' => $validatedData['company_name'],
            'price' => $validatedData['price'],
            'stock' => $validatedData['stock'],
            'comment' => $validatedData['comment'],
        ]);



        // 全ての処理が終わったら、商品一覧画面にリダイレクト
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
        // ビュー画面にメッセージを代入した変数(success)を送ります
    } 



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
            return redirect('/products')
            ->with('error', 'Failed to deleted product');

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


}
