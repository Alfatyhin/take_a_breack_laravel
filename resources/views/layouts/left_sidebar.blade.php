

<ul class="list">

    @if(Auth::user()->user_role == 'admin' )
        <li class="orders" data_name="orders">
            <a href="{{ route('shop_settings_orders') }}" >Заказы</a>
        </li>
    @endif

    @if(Auth::user()->user_role == 'admin' || Auth::user()->user_role == 'content manager' )
        <li class="categories" data_name="categories">
            <a href="{{ route('shop_settings_categories') }}" >Категории</a>
        </li>
        <li class="products" data_name="products">
            <a href="{{ route('shop_settings_products') }}" >Товары</a>
        </li>
    @endif

    @if(Auth::user()->user_role == 'admin' )
        <li class="dey_offer" data_name="dey_offer">
            <a href="{{ route('dey_offer') }}" >Предложение дня</a>
        </li>
        <li class="coupons_discount" data_name="coupons_discount">
            <a href="{{ route('coupons_discount') }}" >Купоны на скидку</a>
        </li>
        <li class="delivery" data_name="delivery">
            <a href="{{ route('delivery') }}" >Доставка</a>
        </li>
        <li class="invoice_setting" data_name="invoice_setting">
            <a href="{{ route('invoice_setting') }}" >чеки</a>
        </li>
        <li class="banner" data_name="banner">
            <a href="{{ route('banner') }}" >баннер</a>
        </li>
        <li class="users" data_name="users">
            <a href="{{ route('users') }}" >персонал</a>
        </li>
    @endif
</ul>
