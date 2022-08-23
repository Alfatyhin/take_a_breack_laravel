
<section class="description">
    <div class="description__imagesBlock">
        @include("shop.layouts.product_cart.galery")
    </div>
    <div class="description__contentBlock">
        @include("shop.layouts.product_cart.description")

        @if(isset($product->data['attributes']) && (isset($product->data['attributes']['composition'][$lang]) || $product->data['attributes']['calories'] || $category_data['attributes']['keeping']))
            <div class="description__tabs">
                <div class="description__tabsTitle">
                    <div class="description__tabNameUnderline"></div>
                    <button class="description__tabName description__tabName--components showBlock">
                        <img src="/img/product_card/components.png" alt="icon"> Состав
                    </button>
                    <button class="description__tabName description__tabName--calories">
                        <img src="/img/product_card/calories.png" alt="icon"> Калорийность
                    </button>

                    <button class="description__tabName description__tabName--storing">
                        <img src="/img/product_card/storing.png" alt="icon"> Хранение
                    </button>
                </div>
                <div class="description__tabsSeparate"></div>
                <div class="description__tabsBody">
                        <div class="description__tabComponents description__tab showBlock">

                            @isset($product->data['attributes']['composition'][$lang])
                                <div class="description__tabComponentsTitle description__tabTitle">
                                    <p>В составе десерта следующие ингридиенты:</p>
                                </div>
                                <div class="description__tabComponentsText description__tabText">
                                    <p>{{ $product->data['attributes']['composition'][$lang] }}</p>
                                </div>
                                <div class="description__tabComponentsList description__tabText">

                                </div>

                            @endisset
                        </div>
                    <div class="description__tabColories description__tab">
                        @isset($product->data['attributes']['calories'])
                            <div class="description__tabColoriesTitle description__tabTitle">
                                <p>Энергетическая ценность на 100 грамм продукта:</p>
                            </div>
                            <div class="description__tabColoriesList">
                                <div class="description__tabColoriesListItem">
                                    <div class="description__tabColoriesListItemIcon">К</div>
                                    <div class="description__tabColoriesListItemName description__tabText">Калорийность</div>
                                    <div class="description__tabColoriesListItemNumber description__tabText">{{ $product->data['attributes']['calories']['calories'] }} ккал</div>
                                </div>
                                <div class="description__tabColoriesListItem">
                                    <div class="description__tabColoriesListItemIcon">Б</div>
                                    <div class="description__tabColoriesListItemName description__tabText">Белки</div>
                                    <div class="description__tabColoriesListItemNumber description__tabText">{{ $product->data['attributes']['calories']['protein'] }} г</div>
                                </div>
                                <div class="description__tabColoriesListItem">
                                    <div class="description__tabColoriesListItemIcon">Ж</div>
                                    <div class="description__tabColoriesListItemName description__tabText">Жиры</div>
                                    <div class="description__tabColoriesListItemNumber description__tabText">{{ $product->data['attributes']['calories']['fat'] }} г</div>
                                </div>
                                <div class="description__tabColoriesListItem">
                                    <div class="description__tabColoriesListItemIcon">У</div>
                                    <div class="description__tabColoriesListItemName description__tabText">Углеводы</div>
                                    <div class="description__tabColoriesListItemNumber description__tabText">{{ $product->data['attributes']['calories']['carbohydrate'] }} г</div>
                                </div>
                            </div>
                        @endisset
                    </div>
                    <div class="description__tabStoring description__tab">
                        @isset($category_data['attributes']['keeping'][$lang])
                            <div class="description__tabStoringTitle description__tabTitle">
                                <p>Сроки и условия хранения:</p>
                            </div>
                            <div class="description__tabStoringText description__tabText">
                                {!! $category_data['attributes']['keeping'][$lang] !!}
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<section class="additional">
    <h2 class="additional__title">Добавить к заказу:</h2>
    <div class="additional__slider">
        @include("shop.layouts.slider")
    </div>
    <div class="additional__sliderPagWrapper">
        <div class="additional__sliderPagination sliderPagination"></div>
    </div>
</section>

