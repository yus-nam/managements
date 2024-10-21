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

        $companies = Company::all();
        
        $query = Product::query();
        
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

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');

        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(10)->appends($request->all());

        // Ajaxリクエストの際も全体のHTMLを返す
        if ($request->ajax()) {
            return view('products.index', [
                'products' => $products,
                'companies' => $companies,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ]);
        }

        return view('products.index', [
            'products' => $products,
            'companies' => $companies,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }


    
    public function create()
    {
        $companies = Company::all();

        return view('products.create', compact('companies'));
    }

    
    public function store(Request $request) 
    {

        Log::info('Store method called', $request->all());

        $request->validate([
            'product_name' => 'required',
            'company_id' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'comment' => 'nullable',
            'img_path' => 'nullable|image|max:2048',
        ]);

        Log::info('Validation passed', $request->all());

        $model = new Product();

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

        } catch(Exception $e) {

            DB::rollBack();
            Log::error($e);

        };

        $request->session()->regenerateToken();

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

        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'company_name' => 'required|exists:companies,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'comment' => 'nullable|string',
            'img_path' => 'nullable|image|max:2048', 
        ]);

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

        DB::beginTransaction();
        try{

            $product->delete();

            DB::commit();

            // 削除成功のレスポンスをJSONで返す
            return response()->json(['success' => 'Product deleted successfully']);

        } catch(Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect('/products')
            ->with('error', 'Failed to deleted product');

        };

        // return redirect('/products')
        //     ->with('success', 'Product deleted successfully');
    }

}
