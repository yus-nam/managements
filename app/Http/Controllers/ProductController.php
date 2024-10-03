<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request; 
// use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller 
{
    
    public function index(Request $request)
    {

        Log::info('Index method called', $request->all());
    
        $companies = Company::all();
    
        $query = Product::query();  // ここでクエリビルダを初期化します
    
        if ($search = $request->search) {
            $query->where('product_name', 'LIKE', "%{$search}%");
        }
    
        if ($company_id = $request->company_name) {
            $query->where('company_id', $company_id);
        }

        if ($min_price = $request->min_price) {
            $query->where('price', '>=', $min_price);
        }
    
        if ($max_price = $request->max_price) {
            $query->where('price', '<=', $max_price);
        }
    
        if ($min_stock = $request->min_stock) {
            $query->where('stock', '>=', $min_stock);
        }
    
        if ($max_stock = $request->max_stock) {
            $query->where('stock', '<=', $max_stock);
        }

        $products = $query->paginate(10)->appends($request->all());
    
        return view('products.index', [
            'products' => $products,
            'companies' => Company::all()
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
            
            $model->registProduct($request,$img_path);

            
            DB::commit();

        } catch(Exception $e) { /** 例外処理 **/

            DB::rollBack();
            Log::error($e);

        };

        //二重送信防止
        $request->session()->regenerateToken();

        // 商品一覧画面にリダイレクト
        return redirect('products/create');

    }



    public function show(Product $product)
    {
            return view('products.show', ['product' => $product]);
    }



    public function edit(Product $product)
    {
        $companies = Company::all();

        return view('products.edit', compact('product', 'companies'));
    }
    
    public function update(Request $request, Product $product)
    {
        // バリデーション設定
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'company_name' => 'required|exists:companies,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'comment' => 'nullable|string',
            'img_path' => 'nullable|image|max:2048', 
        ]);
    
        //トランザクション開始
        DB::beginTransaction();

        try{
            
            if($request->hasFile('img_path')) {

                $file = $request->file('img_path');

                $filename = $file->getClientOriginalName();

                $request->file('img_path')->storeAs('storage', $filename, 'public');

                $product->img_path = '/storage/'. $filename;

            }

                $product->product_name = $request->product_name;
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->comment = $request->comment;
        
            DB::commit();

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

        return redirect()->route('products.edit', $product)
            ->with('success', 'Product updated successfully');
    } 

    public function destroy(Product $product)
    {
        //トランザクション開始
        DB::beginTransaction();
        try{

            $product->delete();

            DB::commit();

        } catch(Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect('/products')
            ->with('error', 'Failed to deleted product');

        };

        return redirect('/products')
            ->with('success', 'Product deleted successfully');
    }

}
