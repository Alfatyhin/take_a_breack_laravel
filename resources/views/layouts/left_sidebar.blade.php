
@php($rout_name = Route::currentRouteName())

<style>
   .left_sidebar ul.list .{{ $rout_name }} {
        border-bottom: 2px solid #fff;
    }
</style>

<ul class="list">

    @if(Auth::user()->user_role == 'admin' )
        <li class="orders shop_settings_orders" data_name="orders">
            <a href="{{ route('shop_settings_orders') }}" >Заказы</a>
        </li>
    @endif

    @if(Auth::user()->user_role == 'admin' || Auth::user()->user_role == 'content manager' )
        <li class="categories shop_settings_categories" data_name="categories">
            <a href="{{ route('shop_settings_categories') }}" >Категории</a>
        </li>
        <li class="products shop_settings_products" data_name="products">
            <a href="{{ route('shop_settings_products') }}" >Товары</a>
        </li>
    @endif

    @if(Auth::user()->user_role == 'admin' )
        <li class="dey_offer dey_offer" data_name="dey_offer">
            <a href="{{ route('dey_offer') }}" >Предложение дня</a>
        </li>
        <li class="coupons_discount coupons_discount" data_name="coupons_discount">
            <a href="{{ route('coupons_discount') }}" >Купоны на скидку</a>
        </li>
        <li class="delivery crm_delivery" data_name="delivery">
            <a href="{{ route('crm_delivery') }}" >Доставка</a>
        </li>
        <li class="invoice_setting invoice_setting" data_name="invoice_setting">
            <a href="{{ route('invoice_setting') }}" >чеки</a>
        </li>
        <li class="users users" data_name="users">
            <a href="{{ route('users') }}" >персонал</a>
        </li>
        <li class="products_options shop_settings_products_options" data_name="products_options">
            <a href="{{ route('shop_settings_products_options') }}" >Product Options</a>
        </li>
        <li class="orders_log orders_log" data_name="orders_log">
            <a href="{{ route('orders_log') }}" >Orders Log</a>
        </li>

    @endif



    @if(Auth::user()->user_role == 'admin' || Auth::user()->user_role == 'content manager' || Auth::user()->user_role == 'marketer' )
        <li class="banner" data_name="banner">
            <a href="{{ route('banner') }}" >баннер</a>
        </li>
    @endif

    @if(Auth::user()->user_role == 'admin' || Auth::user()->user_role == 'content manager' )
        <li class="translations" data_name="translations">
            <a href="{{ route('translations') }}" >Переводы</a>
        </li>
        <li class="components" data_name="components">
            <a href="{{ route('components') }}" >site components</a>
        </li>
    @endif


    @if(Auth::user()->user_role == 'admin' || Auth::user()->user_role == 'marketer' )
        <li class="db utm" data_name="utm">
            <a href="{{ route('db', ['tb_name' => 'utm']) }}" >utm</a>
        </li>
    @endif
</ul>
