@extends('layouts.main')

@section('title', $product->name ?? 'Chi tiết sản phẩm') {{-- Cập nhật tiêu đề trang --}}

@section('content')
@php
$wishlistIds = [];
if(auth()->check()) {
$wishlistIds = auth()->user()->wishlist()->pluck('product_id')->toArray();
}
$configurations = is_string($product->configurations) ? json_decode($product->configurations, true) :
($product->configurations ?? []);
$colors = is_string($product->colors) ? json_decode($product->colors, true) : ($product->colors ?? []);
$sizes = is_string($product->sizes) ? json_decode($product->sizes, true) : ($product->sizes ?? []);
$storages = is_string($product->storages) ? json_decode($product->storages, true) : ($product->storages ?? []);
@endphp
<div id="breadcrumb" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="breadcrumb-tree">
                    <li><a href="{{ route('home') }}">Trang chủ</a></li>
                    {{-- Breadcrumb danh mục sản phẩm --}}
                    @if($product->category)
                    <li><a
                            href="{{ route('store.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                    </li>
                    @else
                    <li><a href="{{ route('store.index') }}">Tất cả sản phẩm</a></li>
                    {{-- Hoặc link đến trang danh mục chung nếu không có danh mục --}}
                    @endif
                    <li class="active">{{ $product->name ?? 'Tên sản phẩm' }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-md-push-2">
                <div id="product-main-img">
                    <div class="product-preview">
                        @php
                        $imagePath = $product->image_path && file_exists(public_path('storage/' . $product->image_path))
                        ? asset('storage/' . $product->image_path)
                        : asset('img/default-product.png');
                        @endphp
                        <img src="{{ $imagePath }}" alt="{{ $product->name }}"
                            style="max-width:100%;max-height:260px;border-radius:1.5rem;box-shadow:0 4px 24px #e3e3e3;object-fit:contain;background:#f8fafc;">
                    </div>
                    {{-- Nếu bạn có nhiều hình ảnh cho sản phẩm (liên kết với Product::images) --}}
                    {{-- @foreach($product->images as $image)
                    <div class="product-preview">
                        <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->name }}">
                </div>
                @endforeach --}}
            </div>
        </div>
        <div class="col-md-2  col-md-pull-5">
            <div id="product-imgs">
                <div class="product-preview">
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                </div>
                {{-- Nếu có nhiều ảnh thumb thì dùng vòng lặp --}}
                {{-- @foreach($product->images as $image)
                    <div class="product-preview">
                        <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->name }}">
            </div>
            @endforeach --}}
        </div>
    </div>
    <div class="col-md-5">
        <div class="product-details">
            <h2 class="product-name">{{ $product->name }}</h2>
            <div>
                <div class="product-rating">
                    @php
                    $avgRating = $product->averageRating(); // Sử dụng method mới
                    $totalReviews = $product->totalReviewsCount(); // Tổng số đánh giá
                    $reviewsCount = $product->reviews()->count(); // Số reviews
                    $ratingsCount = $product->ratings()->count(); // Số ratings
                    @endphp
                    {{-- <<<<<<< HEAD @for($i=1; $i <=5; $i++) <i class="fa fa-star{{ $i <= $avgRating ? '' : '-o' }}"></i>
                    @endfor
                    ======= --}}
                    @for($i = 1; $i <= 5; $i++) <i class="fa fa-star{{ $i <= $avgRating ? '' : '-o' }}"></i>
                        @endfor
                        {{-- >>>>>>> 0b9b44fc00061049fe4284b4321dfe2d5c01a9a4 --}}
                </div>
                <a class="review-link" href="#tab3">
                    {{ $totalReviews }} Đánh giá
                </a>
            </div>
            <div>
                <h3 class="product-price">{{ number_format($product->price) }} VNĐ <del
                        class="product-old-price">{{ number_format($product->old_price) }} VNĐ</del></h3>
                <span class="product-available">{{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
            </div>
            <p>{{ $product->description }}</p>

            {{-- XOÁ PHẦN HIỂN THỊ OPTION NÀY (ngoài form) --}}

            {{-- Chỉ giữ lại 1 bộ option duy nhất bên trong form "Thêm vào giỏ" (ẩn option trong form "Mua ngay") --}}
            {{-- Thêm lại phần hiển thị option động, chỉ 1 bộ duy nhất, phía trên nút Thêm vào giỏ/Mua ngay --}}
            <div class="product-options-modern" style="margin-bottom:28px;">
                <form class="add-to-cart-form" action="{{ route('cart.add') }}" method="POST"
                    style="display:flex;flex-direction:column;gap:18px;max-width:420px;background:#fff;border-radius:18px;box-shadow:0 4px 24px #e3e3e3;padding:28px 32px 18px 32px;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-end;">
                        <div style="flex:1;min-width:120px;">
                            <label style="font-weight:700;color:#222;margin-bottom:6px;display:block;">
                                <i class="fa fa-cogs" style="color:#d10024;margin-right:6px;"></i> Cấu hình
                            </label>
                            <select class="input-select" name="configuration"
                                style="width:100%;border-radius:10px;padding:8px 12px;border:1.5px solid #e3e3e3;font-size:15px;">
                                <option value="">Chọn cấu hình</option>
                                @foreach($configurations as $config)
                                <option value="{{ $config }}">{{ $config }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:1;min-width:120px;">
                            <label style="font-weight:700;color:#222;margin-bottom:6px;display:block;">
                                <i class="fa fa-paint-brush" style="color:#1976d2;margin-right:6px;"></i> Màu sắc
                            </label>
                            <select class="input-select" name="color"
                                style="width:100%;border-radius:10px;padding:8px 12px;border:1.5px solid #e3e3e3;font-size:15px;">
                                <option value="">Chọn màu sắc</option>
                                @foreach($colors as $color)
                                <option value="{{ $color }}">{{ $color }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-end;">
                        <div style="flex:1;min-width:120px;">
                            <label style="font-weight:700;color:#222;margin-bottom:6px;display:block;">
                                <i class="fa fa-arrows-h" style="color:#0097a7;margin-right:6px;"></i> Kích thước
                            </label>
                            <select class="input-select" name="size"
                                style="width:100%;border-radius:10px;padding:8px 12px;border:1.5px solid #e3e3e3;font-size:15px;">
                                <option value="">Chọn kích thước</option>
                                @foreach($sizes as $size)
                                <option value="{{ $size }}">{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:1;min-width:120px;">
                            <label style="font-weight:700;color:#222;margin-bottom:6px;display:block;">
                                <i class="fa fa-hdd-o" style="color:#f57c00;margin-right:6px;"></i> Dung lượng
                            </label>
                            <select class="input-select" name="storage"
                                style="width:100%;border-radius:10px;padding:8px 12px;border:1.5px solid #e3e3e3;font-size:15px;">
                                <option value="">Chọn dung lượng</option>
                                @foreach($storages as $storage)
                                <option value="{{ $storage }}">{{ $storage }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:16px;margin-top:8px;">
                        <div style="flex:0 0 90px;">
                            <label style="font-weight:700;color:#222;margin-bottom:6px;display:block;">Số lượng</label>
                            <input type="number" name="quantity" value="1" min="1"
                                style="width:80px;border-radius:10px;border:1.5px solid #e3e3e3;padding:8px 12px;font-size:15px;">
                        </div>
                        <button type="submit" class="add-to-cart-btn"
                            style="flex:1;background:linear-gradient(90deg,#d10024 60%,#1976d2 100%);color:#fff;border-radius:24px;padding:14px 0;font-size:18px;font-weight:700;box-shadow:0 4px 16px #f8bbd0;transition:background 0.2s;display:flex;align-items:center;justify-content:center;gap:10px;">
                            <i class="fa fa-shopping-cart" style="font-size:22px;"></i> Thêm vào giỏ hàng
                        </button>
                    </div>
                </form>
            </div>
            <div class="add-to-cart" style="display:flex;align-items:center;gap:12px;">
                {{-- ĐÃ ĐƯA TOÀN BỘ FORM THÊM VÀO GIỎ HÀNG LÊN TRÊN, CHỈ GIỮ LẠI FORM MUA NGAY --}}
                <form class="buy-now-form" action="{{ route('checkout') }}" method="GET"
                    style="display:flex;align-items:center;gap:12px;">
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="configuration" id="buy-now-configuration" value="">
                    <input type="hidden" name="color" id="buy-now-color" value="">
                    <input type="hidden" name="size" id="buy-now-size" value="">
                    <input type="hidden" name="storage" id="buy-now-storage" value="">
                    <input type="hidden" name="quantity" id="buy-now-quantity" value="1">
                    <button type="submit" class="primary-btn"
                        style="background:#222;color:#fff;border-radius:24px;padding:10px 28px;font-size:16px;font-weight:600;box-shadow:0 2px 8px rgba(0,0,0,0.08);transition:background 0.2s;">Mua
                        ngay</button>
                </form>
            </div>

            <ul class="product-btns">
                <li><a href="#"
                        class="add-to-wishlist wishlist-btn{{ in_array($product->id, $wishlistIds) ? ' added' : '' }}"
                        data-product-id="{{ $product->id }}"
                        style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;transition:box-shadow 0.2s,background 0.2s;box-shadow:0 2px 8px #f8bbd0;">
                        <i class="fa {{ in_array($product->id, $wishlistIds) ? 'fa-heart' : 'fa-heart-o' }} wishlist-icon"
                            style="color:#d10024;font-size:1.5rem;transition:color 0.2s;"></i>
                    </a> thêm vào danh sách yêu thích</li>
                <li><a href="#"><i class="fa fa-exchange"></i> thêm vào so sánh</a></li>
            </ul>

            <ul class="product-links">
                <li>Danh mục:</li>
                <li><a
                        href="{{ route('store.index', ['category' => $product->category->slug ?? '']) }}">{{ $product->category->name ?? 'Không có danh mục' }}</a>
                </li>
                <li>{{ $product->category->name ?? 'Không có danh mục' }}</li>
                {{-- Loại bỏ dòng này nếu nó là trùng lặp --}}
            </ul>

            <ul class="product-share">
                <li>Chia sẻ:</li>
                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                <li><a href="#"><i class="fa fa-envelope"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="col-md-12">
        <div id="product-tab">
            <ul class="tab-nav">
                <li class="active"><a data-toggle="tab" href="#tab1">Mô tả</a></li>
                <li><a data-toggle="tab" href="#tab2">Chi tiết</a></li>
                <li><a data-toggle="tab" href="#tab3">Đánh giá ({{ $totalReviews  }})</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab1" class="tab-pane fade in active">
                    <div class="row">
                        <div class="col-md-12">
                            <p>{{ $product->long_description }}</p>
                        </div>
                    </div>
                </div>
                <div id="tab2" class="tab-pane fade in">
                    <div class="row">
                        <div class="col-md-12">
                            <p>{{ $product->details }}</p>
                        </div>
                    </div>
                </div>
                <div id="tab3" class="tab-pane fade in">
                    <div class="row">
                        <div class="col-md-3">
                            <div id="rating">
                                <div class="rating-avg">
                                    <span>{{ number_format($product->averageRating(), 1) }}</span>
                                    <div class="rating-stars">
                                        @php
                                        $avgRating = $product->averageRating(); // Dùng method thống nhất
                                        @endphp
                                        @for($i = 1; $i <= 5; $i++) <i
                                            class="fa fa-star{{ $i <= $avgRating ? '' : '-o' }}"></i>
                                            @endfor
                                    </div>
                                </div>
                                <ul class="rating">
                                    @for($i = 5; $i >= 1; $i--)
                                    <li>
                                        <div class="rating-stars">
                                            @for($j = 1; $j <= 5; $j++) <i class="fa fa-star{{ $j <= $i ? '' : '-o' }}">
                                                </i>
                                                @endfor
                                        </div>
                                        <div class="rating-progress">
                                            @php
                                            // Tính từ cả ratings và reviews
                                            $ratingsCount = $product->ratings->where('rating', $i)->count();
                                            $reviewsCount = $product->reviews->where('rating', $i)->count();
                                            $count = $ratingsCount + $reviewsCount;
                                            $total = $product->totalReviewsCount();
                                            $percent = $total > 0 ? ($count / $total) * 100 : 0;
                                            @endphp
                                            <div style="width: {{ number_format($percent, 2) }}%;"></div>
                                        </div>
                                        <span class="sum">{{ $count }}</span>
                                    </li>
                                    @endfor
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="reviews">
                                <ul class="reviews">
                                    {{-- Hiển thị 5 reviews ngẫu nhiên từ dữ liệu mẫu --}}
                                    @if($randomReviews && $randomReviews->count() > 0)
                                    @foreach($randomReviews as $review)
                                    <li>
                                        <div class="review-heading">
                                            <h5 class="name">{{ $review->user_name ?? 'Người dùng ẩn danh' }}</h5>
                                            <p class="date">
                                                {{ \Carbon\Carbon::parse($review->created_at)->format('d M Y, g:i A') }}
                                            </p>
                                            <div class="review-rating">
                                                @for($i = 1; $i <= 5; $i++) <i
                                                    class="fa fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                                    @endfor
                                            </div>
                                        </div>
                                        <div class="review-body">
                                            <p>{{ $review->comment }}</p>
                                        </div>
                                    </li>
                                    @endforeach
                                    @endif

                                    {{-- Hiển thị ratings thực từ user (nếu có) --}}
                                    @forelse($product->ratings as $rating)
                                    <li>
                                        <div class="review-heading">
                                            <h5 class="name">{{ $rating->user->name }}</h5>
                                            <p class="date">{{ $rating->created_at->format('d M Y, g:i A') }}</p>
                                            <div class="review-rating">
                                                <<<<<<< HEAD @for($i=1; $i <=5; $i++) <i
                                                    class="fa fa-star{{ $i <= $rating->rating ? '' : '-o' }}"></i>
                                                    @endfor
                                                    =======
                                                    @for($i = 1; $i <= 5; $i++) <i
                                                        class="fa fa-star{{ $i <= $rating->rating ? '' : '-o' }}"></i>
                                                        @endfor
                                                        >>>>>>> 0b9b44fc00061049fe4284b4321dfe2d5c01a9a4
                                            </div>
                                        </div>
                                        <div class="review-body">
                                            <p>{{ $rating->comment ?? 'Không có nhận xét' }}</p>
                                        </div>
                                    </li>
                                    @empty
                                    @if(!$randomReviews || $randomReviews->count() == 0)
                                    <li>Chưa có đánh giá nào cho sản phẩm này</li>
                                    @endif
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div id="review-form">
                                @auth
                                <form action="{{ route('products.rating', $product->id) }}" method="POST"
                                    class="review-form">
                                    @csrf
                                    @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                    @endif

                                    <div class="input-rating">
                                        <span>Xếp hạng của bạn: </span>
                                        <div class="stars">
                                            <input id="star5" name="rating" value="5" type="radio" required><label
                                                for="star5"></label>
                                            <input id="star4" name="rating" value="4" type="radio"><label
                                                for="star4"></label>
                                            <input id="star3" name="rating" value="3" type="radio"><label
                                                for="star3"></label>
                                            <input id="star2" name="rating" value="2" type="radio"><label
                                                for="star2"></label>
                                            <input id="star1" name="rating" value="1" type="radio"><label
                                                for="star1"></label>
                                        </div>
                                    </div>

                                    <textarea name="review" class="input" placeholder="Nhận xét của bạn"></textarea>

                                    <button type="submit" class="primary-btn">Gửi đánh giá</button>
                                </form>
                                @else
                                <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đánh giá sản phẩm</p>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-title text-center">
                    <h3 class="title">Sản phẩm liên quan</h3>
                </div>
            </div>
            @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            @foreach ($relatedProducts as $relatedProduct)
            <div class="col-md-3 col-xs-6 mb-4">
                <a href="{{ route('products.show', $relatedProduct->slug) }}"
                    style="display:block;height:100%;text-decoration:none;color:inherit">
                    <div class="product"
                        style="cursor:pointer;border-radius:18px;box-shadow:0 2px 16px #e3e3e3;transition:box-shadow .2s,transform .2s;background:#fff;overflow:hidden;position:relative;min-height:420px;display:flex;flex-direction:column;justify-content:space-between;height:50%;">
                        <div class="product-img" style="padding:24px 24px 0 24px;text-align:center;">
                            @php
                            $imagePath = $relatedProduct->image_path && file_exists(public_path('storage/' .
                            $relatedProduct->image_path))
                            ? asset('storage/' . $relatedProduct->image_path)
                            : asset('img/default-product.png');
                            @endphp
                            <img src="{{ $imagePath }}" alt="{{ $relatedProduct->name }}"
                                style="max-width:100%;max-height:180px;border-radius:12px;box-shadow:0 2px 8px #f0f0f0;object-fit:contain;background:#f8fafc;">
                            @if($relatedProduct->is_new)
                            <div class="product-label"
                                style="position:absolute;top:18px;left:18px;background:#43a047;color:#fff;font-weight:700;border-radius:8px 0 8px 0;padding:3px 14px;font-size:13px;">
                                MỚI</div>
                            @endif
                            @if($relatedProduct->discount_percentage > 0)
                            <div class="product-label"
                                style="position:absolute;top:18px;right:18px;background:#d10024;color:#fff;font-weight:700;border-radius:0 8px 0 8px;padding:3px 14px;font-size:13px;">
                                -{{ $relatedProduct->discount_percentage }}%</div>
                            @endif
                        </div>
                        <div class="product-body" style="padding:18px 24px 0 24px;">
                            <p class="product-category"
                                style="font-size:13px;color:#888;font-weight:500;margin-bottom:4px;">
                                {{ $relatedProduct->category->name ?? 'N/A' }}
                            </p>
                            <h3 class="product-name"
                                style="font-size:1.1rem;font-weight:700;line-height:1.3;margin-bottom:8px;">
                                {{ $relatedProduct->name }}
                            </h3>
                            <h4 class="product-price"
                                style="font-size:1.2rem;color:#d10024;font-weight:800;margin-bottom:6px;">
                                {{ number_format($relatedProduct->price) }} VNĐ @if($relatedProduct->old_price) <del
                                    class="product-old-price"
                                    style="color:#aaa;font-size:1rem;font-weight:400;">{{ number_format($relatedProduct->old_price) }}
                                    VNĐ</del>@endif
                            </h4>
                            <div class="product-rating" style="margin-bottom:8px;">
                                @php $avgRating = $product->averageRating(); @endphp
                                @for($i = 1; $i <= 5; $i++) <i class="fa fa-star{{ $i <= $avgRating ? '' : '-o' }}"
                                    style="color:#ffc107;">
                                    </i>
                                    @endfor
                                    <span
                                        style="color:#888;font-size:13px;margin-left:4px;">({{ number_format($product->averageRating(), 1) }})</span>
                            </div>
                            <div class="product-btns mb-2" style="display:flex;gap:8px;">
                                <button
                                    class="add-to-wishlist wishlist-btn{{ in_array($relatedProduct->id, $wishlistIds) ? ' added' : '' }}"
                                    data-product-id="{{ $relatedProduct->id }}"
                                    style="background:transparent;border:none;outline:none;cursor:pointer;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;transition:box-shadow 0.2s,background 0.2s;box-shadow:0 2px 8px #f8bbd0;"
                                    tabindex="0">
                                    <i class="fa {{ in_array($relatedProduct->id, $wishlistIds) ? 'fa-heart' : 'fa-heart-o' }} wishlist-icon"
                                        style="color:#d10024;font-size:1.5rem;transition:color 0.2s;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="add-to-cart" style="padding:0 24px 18px 24px;">
                            <form class="add-to-cart-form" action="{{ route('cart.add') }}" method="POST"
                                style="display:flex;align-items:center;gap:10px;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="add-to-cart-btn"
                                    style="background:#d10024;color:#fff;border-radius:24px;padding:10px 24px;font-size:15px;font-weight:600;box-shadow:0 2px 8px #f8bbd0;transition:background 0.2s;">
                                    <i class="fa fa-shopping-cart"></i> Thêm vào giỏ hàng
                                </button>
                            </form>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
            @else
            <div class="col-md-12 text-center">
                <p>Không có sản phẩm liên quan.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div id="newsletter" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="newsletter">
                    <p>Đăng ký nhận <strong>BẢN TIN</strong></p>
                    <form>
                        <input class="input" type="email" placeholder="Nhập Email của bạn">
                        <button class="newsletter-btn"><i class="fa fa-envelope"></i> Đăng ký</button>
                    </form>
                    <ul class="newsletter-follow">
                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div id="success-modal"
    style="position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;">
    <div
        style="background:#222;padding:32px 40px;border-radius:16px;text-align:center;color:#fff;min-width:320px;box-shadow:0 8px 32px #0008;">
        <div style="font-size:48px;color:#4caf50;margin-bottom:12px;">
            <i class="fa fa-check" style="color:#4caf50;"></i>
        </div>
        <div style="font-size:20px;font-weight:500;margin-bottom:16px;">{{ session('success') }}</div>
        <button onclick="closeModal()"
            style="background:#fff;color:#222;padding:8px 24px;border-radius:24px;font-weight:600;border:none;cursor:pointer;">Đóng</button>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('success-modal').style.display = 'none';
    }

    setTimeout(function() {
        document.getElementById('success-modal').style.display = 'none';
    }, 2000);
</script>
@endif
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Khi chọn option, đồng bộ vào input hidden của cả 2 form
        $('.product-options-modern select.input-select').on('change', function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            // Cập nhật cho form thêm vào giỏ
            $(this).closest('form').find('input[name="' + name + '"]').val(value);
            // Cập nhật cho form mua ngay
            $('#buy-now-' + name).val(value);
        });
        // Khi load trang, đồng bộ giá trị mặc định của select vào input hidden
        $('.product-options-modern select.input-select').each(function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            // Cập nhật cho form thêm vào giỏ
            $(this).closest('form').find('input[name="' + name + '"]').val(value);
            // Cập nhật cho form mua ngay
            $('#buy-now-' + name).val(value);
        });
        // Đồng bộ số lượng khi thay đổi
        $('.add-to-cart-form input[name="quantity"]').on('input', function() {
            $('#buy-now-quantity').val($(this).val());
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Kiểm tra bắt buộc chọn đầy đủ option trước khi submit
        function mustSelectAllOptions() {
            var valid = true;
            // Chỉ kiểm tra các option thực sự có tồn tại (không kiểm tra nếu không có option nào)
            $('.product-options-modern select.input-select').each(function() {
                if ($(this).find('option').length > 1 && $(this).val() === '') {
                    valid = false;
                    $(this).css('border', '1.5px solid #d10024');
                } else {
                    $(this).css('border', '1px solid #eee');
                }
            });
            return valid;
        }
        $('.add-to-cart-form, .buy-now-form').on('submit', function(e) {
            if (!mustSelectAllOptions()) {
                alert(
                    'Vui lòng chọn đầy đủ tất cả các tuỳ chọn của sản phẩm trước khi mua hoặc thêm vào giỏ!'
                );
                e.preventDefault();
                return false;
            }
        });
    });

    function showCartSuccessModal(msg) {
        if ($('#cart-success-modal').length) $('#cart-success-modal').remove();
        var html = `<div id="cart-success-modal" style="position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;">
        <div style="background:#222;padding:32px 40px;border-radius:16px;text-align:center;color:#fff;min-width:320px;box-shadow:0 8px 32px #0008;">
            <div style="font-size:48px;color:#4caf50;margin-bottom:12px;">
                <i class='fa fa-shopping-cart'></i> <i class='fa fa-check' style='color:#4caf50;'></i>
            </div>
            <div style="font-size:20px;font-weight:500;margin-bottom:16px;">${msg}</div>
            <a href="/cart" style="background:#fff;color:#222;padding:8px 24px;border-radius:24px;font-weight:600;text-decoration:none;">Xem giỏ hàng</a>
        </div>
    </div>`;
        $('body').append(html);
        setTimeout(function() {
            $('#cart-success-modal').fadeOut(300, function() {
                $(this).remove();
            });
        }, 2000);
    }
</script>
@endpush

<style>
    .wishlist-btn:hover {
        background: #ffe4ec !important;
        box-shadow: 0 4px 16px #f8bbd0;
    }

    .wishlist-btn:active {
        background: #ffd6e3 !important;
    }

    .wishlist-icon:hover {
        color: #ff4081 !important;
        transform: scale(1.2);
    }
</style>