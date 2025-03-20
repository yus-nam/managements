<!-- resources/views/products/partials/product_list.blade.php -->
<div class="products mt-5" id="product-list">
    <h2>商品情報</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th a class="column-sorting">ID</th>
                <th a class="column-sorting">商品名</th>
                <th a class="column-sorting">メーカー</th>
                <th a class="column-sorting">価格</th>
                <th a class="column-sorting">在庫数</th>
                <th>コメント</th>
                <th>商品画像</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->company->company_name ?? '未設定' }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->comment }}</td>
                <td><img src="{{ asset($product->img_path) }}" alt="商品画像" width="100"></td>
                <td>
                    <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細</a>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="delete-form d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm mx-1 delete-button">削除</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{ $products->appends(request()->query())->links() }}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!confirm('Delete OK？')) {
                event.preventDefault();
            }
        });
    });
});
</script>

