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
        @include('products.partials.product_list')
    </div>

    <div id="pagination-area">
        {{ $products->appends(request()->query())->links() }}
    </div>

    <!-- {{ $products->appends(request()->query())->links() }} -->

</div>

<script>
    $(document).ready(function() {


        function bindDeleteEvent() {
            $(document).off('click', '.delete-button');
            $(document).on('click', '.delete-button', function(event) {
                console.log('削除ボタンがクリックされました'); // ←確認用
                event.preventDefault();// これでフォーム送信を止める

                if (!confirm('本当に削除しますか？')) return;

                // const id = $(this).data('url');
                const url = $(this).data('url'); // ここでURLを取得
                const token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    // url: `/products/${id}`,
                    url: url,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token },
                    // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    
                    success: function(response) {
                        if (response.success) {
                        // 削除後リスト再描画
                        $.get($('#search-form').attr('action'), $('#search-form').serialize(), function(resp) {
                            $('#product-list').html(resp.list);
                            $('#pagination-area').html(resp.pagination);
                            rebindAllEvents();
                        });

                        } else {
                            alert('削除に失敗しました。');
                        }
                    },
                    error: function(xhr) {
                        alert('削除に失敗しました: ' + xhr.status);
                    }
                });
            });


            
        }

        function bindPaginationEvent() {
            $(document).off('click', '.pagination a');
            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                let url = $(this).attr('href');
                $.get(url, function(response) {
                    $('#product-list').html(response.list);
                    $('#pagination-area').html(response.pagination);
                    rebindAllEvents();
                });
            });
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
                    // $('#product-list').html(response); // 商品リストを更新
                    $('#product-list').html(response.list);
                    $('#pagination-area').html(response.pagination);
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
                    // $('#product-list').html(response); // 部分更新
                    $('#product-list').html(response.list);
                    $('#pagination-area').html(response.pagination);
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

