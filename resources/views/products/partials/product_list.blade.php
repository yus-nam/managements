<!-- resources/views/products/partials/product_list.blade.php -->

    <h2>商品情報</h2>
    <table class="table table-striped">
        <!-- <thead>
            <tr>
                <th><a class="column-sorting">ID</th>
                <th><a class="column-sorting">商品名</th>
                <th><a class="column-sorting">メーカー</th>
                <th><a class="column-sorting">価格</th>
                <th><a class="column-sorting">在庫数</th>
                <th>コメント</th>
                <th>商品画像</th>
                <th>操作</th>
            </tr>
        </thead> -->

        <thead>
            <tr>
                <th style="width: 45px; padding: 6px">
                    <a class="column-sorting" href="{{ route('products.index', array_merge(request()->all(), ['sort_by' => 'id', 'sort_order' => request('sort_order') === 'asc' && request('sort_by') === 'id' ? 'desc' : 'asc'])) }}">
                    ID
                    @if(request('sort_by') === 'id')
                        @if(request('sort_order') === 'asc')
                            ▲
                        @else
                            ▼
                        @endif
                    @endif
                    </a>
                </th>

                <th style="width: 94px; padding: 6px">
                    <!-- <span style="cursor: pointer;" onclick="location.href='{{ route('products.index', array_merge(request()->all(), ['sort_by' => 'product_name', 'sort_order' => request('sort_order') === 'asc' && request('sort_by') === 'product_name' ? 'desc' : 'asc'])) }}'"> -->
                    <a class="column-sorting" href="{{ route('products.index', array_merge(request()->all(), ['sort_by' => 'product_name', 'sort_order' => request('sort_order') === 'asc' && request('sort_by') === 'product_name' ? 'desc' : 'asc'])) }}">
                    商品名
                    @if(request('sort_by') === 'product_name')
                        @if(request('sort_order') === 'asc')
                            ▲
                        @else
                            ▼
                        @endif
                    @endif
                    </a>
                    <!-- </span> -->
                </th>
                
                <th style="width: 140px; padding: 6px">
                    <a class="column-sorting" href="{{ route('products.index', array_merge(request()->all(), ['sort_by' => 'company_id', 'sort_order' => request('sort_order') === 'asc' && request('sort_by') === 'company_id' ? 'desc' : 'asc'])) }}">
                    メーカー
                    @if(request('sort_by') === 'company_id')
                        @if(request('sort_order') === 'asc')
                            ▲
                        @else
                            ▼
                        @endif
                    @endif
                    </a>
                </th>

                <th style="width: 80px; padding: 6px">
                    <a class="column-sorting" href="{{ route('products.index', array_merge(request()->all(), ['sort_by' => 'price', 'sort_order' => request('sort_order') === 'asc' && request('sort_by') === 'price' ? 'desc' : 'asc'])) }}">
                    価格
                    @if(request('sort_by') === 'price')
                        @if(request('sort_order') === 'asc')
                            ▲
                        @else
                            ▼
                        @endif
                    @endif
                    </a>
                </th>

                <th style="width: 96px; padding: 6px">
                    <a class="column-sorting" href="{{ route('products.index', array_merge(request()->all(), ['sort_by' => 'stock', 'sort_order' => request('sort_order') === 'asc' && request('sort_by') === 'stock' ? 'desc' : 'asc'])) }}">
                    在庫数
                    @if(request('sort_by') === 'stock')
                        @if(request('sort_order') === 'asc')
                            ▲
                        @else
                            ▼
                        @endif
                    @endif
                    </a>
                </th>

                <th style="width: 280px; padding: 6px">コメント</th>

                <th style="width: 150px; padding: 6px">商品画像</th>

                <th style="width: 120px; padding: 6px">操作</th>
            </tr>
        </thead>

        <tbody>
        @foreach ($products as $product)
            <tr class="item-row">
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
                        <!-- <button type="submit" class="btn btn-danger btn-sm mx-1 delete-button">削除</button> -->
                        <button type="button" class="btn btn-danger btn-sm mx-1 delete-button" data-id="{{ $product->id }}">削除</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <!-- {{ $products->appends(request()->query())->links() }} -->


