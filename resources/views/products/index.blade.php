@extends('layouts.app')

@section('content')

<div class="container">
    <h1 class="mb-4">商品情報一覧</h1>

    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">新規登録</a>

<div class="search mt-5">
    
    <h2>検索条件で絞り込み</h2>

    <form action="{{ route('products.index') }}" method="GET" class="row g-3" id="search-form">
        
        <div class="col-sm-12 col-md-3">
            <input type="text" name="search" class="form-control" placeholder="商品名" value="{{ request('search') }}">
        </div>
        
        <div class="col-sm-3">
            <select  name="company_name" class="form-select" placeholder="メーカー" id="company_name">
                
            <option value="">全てのメーカー</option>
            
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                    {{ $company->company_name }}
                </option>
            @endforeach

            </select>
        </div>

        <div class="col-sm-12 col-md-2">
            <input type="number" name="min_price" class="form-control" placeholder="最小価格" value="{{ request('min_price') }}">
        </div>

        <div class="col-sm-12 col-md-2">
            <input type="number" name="max_price" class="form-control" placeholder="最大価格" value="{{ request('max_price') }}">
        </div>

        <div class="col-sm-12 col-md-2">
            <input type="number" name="min_stock" class="form-control" placeholder="最小在庫" value="{{ request('min_stock') }}">
        </div>

        <div class="col-sm-12 col-md-2">
            <input type="number" name="max_stock" class="form-control" placeholder="最大在庫" value="{{ request('max_stock') }}">
        </div>

        <div class="col-sm-12 col-md-1">
            <button class="btn btn-success" type="submit">検索</button>
        </div>
    </form>
</div>


<a href="{{ route('products.index') }}" class="btn btn-outline-secondary mt-3">検索条件を元に戻す</a>


    <div class="products mt-5" id="product-list">
        <table class="table table-striped">
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
                    </td>
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細</a>
                        
                        <button class="btn btn-danger btn-sm mx-1 delete-button" data-id="{{ $product->id }}">削除</button>

                        <!-- <form method="POST" action="{{ route('products.destroy', $product) }}" class="delete-form d-inline"> -->
                            <!-- @csrf -->
                            <!-- @method('DELETE') -->
                            <!-- <button type="submit" class="btn btn-danger btn-sm mx-1 delete-button">削除</button> -->
                            <!-- <button type="submit" class="btn btn-danger btn-sm mx-1 delete-button" data-id="{{ $product->id }}">削除</button> -->
                        <!-- </form> --> 
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $products->appends(request()->query())->links() }}

</div>

<script>
    $(document).ready(function() {

        // function bindDeleteEvent() {
        //     $(document).on('click', '.delete-button', function(event) {
        //         event.preventDefault();

        //         let $button = $(this);
        //         let itemId = $button.data('id');
        //         let url = '/products/' + itemId;

        //         if (!confirm('本当に削除しますか？')) {
        //             return;
        //         }

        //         $.ajax({
        //             url: url,
        //             type: 'DELETE',
        //             dataType: 'json',
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function(response) {
        //                 if (response.success) {
        //                     $button.closest('.item-row').fadeOut(500, function() {
        //                         $(this).remove();
        //                     });
        //                 } else {
        //                     alert('削除に失敗しました。');
        //                 }
        //             },
        //             error: function(xhr) {
        //             console.error(xhr);
        //             alert('エラーが発生しました: ' + xhr.status + ' - ' + xhr.responseText);
        //             }
        //         });
        //     });
        // }

        function bindDeleteEvent() {
            $(document).on('click', '.delete-button', function(event) {
                event.preventDefault();
                if (!confirm('本当に削除しますか？')) return;

                const id = $(this).data('id');
                $.ajax({
                    url: `/products/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    
                    success: function(response) {
                        if (response.success) {
                        $('#search-form').submit();// 削除成功後にリストを再取得して描画する方が安全
                        } else {
                            alert('削除に失敗しました。');
                        }
                    }
                });
            });





            // $(document).on('click', '.delete-button', function(event) {
            //     event.preventDefault(); // フォーム送信を完全に防ぐ
            //     console.log('削除ボタンがクリックされました');


            //     const button = $(this);
            //     const id = button.data('id');

            //     if (!confirm('本当に削除しますか？')) return;

            //     $.ajax({
            //         url: `/products/${id}`,
            //         type: 'DELETE',
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         success: function(response) {
            //             if (response.success) {
            //                 button.closest('.item-row').fadeOut(300, function() {
            //                     $(this).remove();
            //                 });
            //             } else {
            //                 alert('削除に失敗しました。');
            //             }
            //         },
            //         error: function(xhr) {
            //             alert('エラーが発生しました: ' + xhr.status);
            //         }
            //     });
            // });
        }


        /** ページネーション機能のコード */
        function bindPaginationEvent() {
            $(document).off('click', '.pagination a');
            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                $.get($(this).attr('href'), function(response) {
                    $('#product-list').html(response); // responseは部分テンプレート
                    rebindAllEvents();
                });
            });

            // $(document).on('click', '.pagination a', function(event) {
            //     event.preventDefault(); // ページネーションリンクのデフォルト動作を防ぐ
            //     var url = $(this).attr('href'); // 次のページのURLを取得

            //     $.ajax({
            //         url: url,
            //         type: 'GET',
            //         success: function(response) {
            //         // レスポンスの中から#product-listの内容を更新//コメントアウトしないことで反応が上手くいく
            //         $('#product-list').html($(response));
            //             bindDeleteEvent();  // 削除イベントを再バインド
            //             bindPaginationEvent(); // ページネーションイベントを再バインド
            //         },
            //         error: function(xhr) {
            //             alert('Pagination failed: ' + xhr.status + ' - ' + xhr.responseText);
            //         }
            //     });
            // });
        }

        // 共通して再バインドする処理をまとめる
        function rebindAllEvents() {
            bindDeleteEvent();
            bindPaginationEvent();
            // bindSortEvent();
        }
        
        
        /** 検索機能のコード */
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: '{{ route("products.index") }}',
                type: 'GET',
                data: formData,
                success: function (response) {
                    $('#product-list').html(response); // 商品リストを更新
                    rebindAllEvents();
                },
                error: function (xhr) {
                    alert('検索に失敗しました: ' + xhr.status + ' - ' + xhr.responseText);
                }
            });
        });


        /** ソート機能のコード */
        $(document).on('click', '.column-sorting', function(event) { 
            event.preventDefault(); // デフォルトのリンク動作を防ぐ
            var url = $(this).attr('href'); // ソートリンクのURLを取得
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#product-list').html(response); // 部分更新
                    rebindAllEvents();
                },
                error: function(xhr) {
                    alert('ソートに失敗しました: ' + xhr.status + ' - ' + xhr.responseText);
                }
            });
        
        });

        rebindAllEvents();

        // console.log('js');

    });

</script>

@endsection