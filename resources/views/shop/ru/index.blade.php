



@include("shop.$lang.anchor-welcome")


@include("shop.$lang.anchor-select")

<section class="offer">
    <div class="container">
        <div class="offer__body">
            @include("shop.layouts.index_dey_offer")
        </div>
    </div>
</section>
<section class="size">
    <div class="size__bg"></div>
    <div class="container">
        <div class="size__body">
            <h2 class="size__title blockTitle">Выберите подходящий размер торта</h2>
            <div class="size__itemsWrapper">
                <div class="size__itemsBg"></div>
                <div class="size__items">
                    <div class="size_item">
                        <div class="size_itemImg"><img class="size_itemImg--main" src="/img/index/size/4_parts.webp" alt="image">
                            <img class="size_itemImg--hover" src="/img/index/size/4_parts_anim.webp" alt="image"></div>
                        <div class="size_itemTitle">Размер Мини</div>
                        <div class="size_itemWeight">Вес: 600г ⌀12см</div>
                        <div class="size_itemText blockText">Небольшой торт, подойдёт для маленькой компании из 4-5 человек</div>
                    </div>
                    <div class="size_item">
                        <div class="size_itemImg"><img class="size_itemImg--main" src="/img/index/size/8_parts.webp" alt="image">
                            <img class="size_itemImg--hover" src="/img/index/size/8_parts_anim.webp" alt="image"></div>
                        <div class="size_itemTitle">Размер Семейный</div>
                        <div class="size_itemWeight">Вес:1200г ⌀16см</div>
                        <div class="size_itemText blockText">Для тёплых встреч за чашкой чая в семейном кругу на 8-10 человек</div>
                    </div>
                    <div class="size_item">
                        <div class="size_itemImg"><img class="size_itemImg--main" src="/img/index/size/16_parts.webp" alt="image">
                            <img class="size_itemImg--hover" src="/img/index/size/16_parts_anim.webp" alt="image"></div>
                        <div class="size_itemTitle">Размер Торжество</div>
                        <div class="size_itemWeight">Вес: 2400г ⌀20см</div>
                        <div class="size_itemText blockText">Торт для торжественных случаев на большую компанию из 16-20 человек</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="about" id="anchor-about">
    <div class="container">
        <div class="about__body">
            <div class="about__content">
                <h2 class="about__title blockTitle">Take a Break –<br>это мы: Женя и Лиза</h2>
                <div class="about__text blockText"><p>Всегда хотелось что-то сладенькое и чтобы это не вредило здоровью. В один момент мы решили попробовать отказаться от сладкого из магазина и делать конфеты и тортики дома, чтобы исключить глютен, сахар, лактозу, вредные красители и консерванты...</p><p>Это переросло в целую студию здоровых десертов! Все наши друзья, а позже и покупатели, были в восторге от вкуса наших сладостей. Они говорят: "Это намного вкуснее, чем любой магазинный торт с сахаром". Есть секретный ингредиент - это любовь!</p><p>Присоединяйтесь к нам в Инстаграм и первыми узнавайте о новинках и скидках!</p></div><a class="about__btn blockBtn blockBtn--bgc" href="https://www.instagram.com/takeabreak_desserts/" target="_blank">перейти в наш инстаграм</a>
            </div>
            <div class="about__images"><img src="/img/index/about/1.webp" alt="image"><img src="/img/index/about/2.webp" alt="image"><img src="/img/index/about/3.webp" alt="image">
            </div>
        </div>
    </div>
</section>

@include("shop.$lang.anchor-advantage")

{{--<section class="mailing hidden" id="anchor-mailing">--}}
{{--    <div class="mailing__border">--}}
{{--        <div class="container">--}}
{{--            <div class="mailing__body">--}}
{{--                <div class="mailing__content">--}}
{{--                    <div class="mailing__title blockTitle"><h3>Крафтовый шоколад в подарок</h3><img class="mailing__img" src="/img/index/mailing/gift.png" alt="image"></div>--}}
{{--                    <div class="mailing__video">--}}
{{--                        <video src="/video/index/mailing/chocolate.mp4" autoplay loop muted poster="/img/index/mailing/video_bg.webp"></video>--}}
{{--                    </div>--}}
{{--                    <div class="mailing__subtitle">Узнавайте первыми о наших новинках, акциях и скидках! И получайте подарки!</div>--}}
{{--                    <div class="mailing__text blockText"><p>Оставьте адрес своей электронной почты в поле ниже и получите плитку крафтового шоколада из элитных ароматических сортов какао бобов в&nbsp;подарок от нашего магазина. Великолепный натуральный шоколад без глютена и сахара, изготовленный по уникальной технологии, доставт вам непередаваемое удовольствие. </p><p>Мы будем присылать вам только свежую и полезную информацию о нашем товаре, никакого спама. </p><p>А если вы скажете нам дату своего рождения, мы с радостью поздравим вас с праздником!</p></div>--}}
{{--                    <form class="mailing__form" action="{{ route('send_birthday') }}" method="POST">--}}
{{--                        @csrf--}}
{{--                        <input type="hidden" name="lang" value="{{ $lang }}">--}}
{{--                        <div class="mailing__formFields">--}}
{{--                            <div class="mailing__formField">--}}
{{--                                <label class="mailing__formFieldTitle">Введите адрес электронной почты</label>--}}
{{--                                <div class="mailing__formInputWrapper">--}}
{{--                                    <input class="blockTextField validateText" type="text" placeholder="Ваш email" name="mailingMail" data-text="Email">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="mailing__formField">--}}
{{--                                <label class="mailing__formFieldTitle">Введите или выберите дату рождения</label>--}}
{{--                                <div class="mailing__formInputWrapper">--}}
{{--                                    <input class="blockTextField inputDate" type="date" placeholder="__.__.____" name="mailingBirthday" data-text="Birthday">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <button class="mailing__formSend blockBtn blockBtn--bgc">отправить данные</button>--}}
{{--                        <div class="mailing__formNote">Нажимая кнопку ОТПРАВИТЬ, вы даёте согласие на получение рассылки</div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<section class="feedback" id="anchor-feedback">
    <div class="container">
        <div class="feedback__body">
            <h2 class="feedback__title blockTitle">Что говорят о наших десертах</h2>
            <div class="feedback__slider">
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img src="/img/index/feedback/1.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img src="/img/index/feedback/2.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img src="/img/index/feedback/3.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img src="/img/index/feedback/1.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img src="/img/index/feedback/2.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img src="/img/index/feedback/3.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img src="/img/index/feedback/1.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img src="/img/index/feedback/2.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img src="/img/index/feedback/3.webp" alt="image"></div>
                </div>

            </div>
            <div class="feedback__sliderPagWrapper">
                <div class="feedback__sliderPagination sliderPagination"></div>
            </div>
        </div>
    </div>
</section>
<section class="info" id="anchor-info">
    <div class="container">
        <div class="info__body">
            <h2 class="info__title blockTitle">Доставка и оплата</h2>
            <div class="info__content">
                <div class="info__map">
                    <iframe style="width: 100%; height: 100%;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3382.780969817665!2d34.786971815315084!3d32.02104628120692!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1502b4b0e0292707%3A0xb7476744837f4cd8!2zRW1hbnVlbCBSaW5nZWxibHVtIDMsIEhvbG9uLCDQmNC30YDQsNC40LvRjA!5e0!3m2!1sru!2spl!4v1628862790907!5m2!1sru!2spl">
                    </iframe>
                </div>
                <div class="info__items">
                    <div class="info__item">
                        <h3 class="info__itemTitle">Самовывоз</h3>
                        <div class="info__itemText blockText">
                            <p>Вы можете забрать нашу продукцию самостоятельно по адресу <span>Emanuel Ringelblum 3, Holon.</span> Пожалуйста, позвоните нам за 15 минут до вашего прибытия. Мы будем рады вас встретить.</p>
                        </div>
                    </div>
                    <div class="info__item">
                        <h3 class="info__itemTitle">Доставка курьером</h3>
                        <div class="info__itemText blockText">
                            <p>Мы осуществляем доставку от Ашкелона до Хайфы. Размер оплаты — от 35 шекелей. Уточнить точную стоимость доставки и минимальную сумму заказа можно <button class='areaAndPriceBtn blockText'>здесь</button>.</p>
                        </div>
                    </div>
                    <div class="info__item">
                        <h3 class="info__itemTitle">Оплата</h3>
                        <div class="info__itemText blockText">
                            <p>Оплатить свой заказ вы можете  с помощью PayPal, кредитной картой, переводом на бит или наличными.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="contact" id="anchor-contact">
    <div class="container">
        <div class="contact__body">
            <h2 class="contact__title blockTitle">Свяжитесь с нами</h2>
            <div class="contact__content">
                <div class="contact__text">
                    <div class="contact__subtitle blockText">Если у вас есть вопросы или вам нужна помощь с выбором, сообщите нам об этом, мы с радостью вам поможем</div>
                    <div class="contact__comText">
                        <div class="contact__comTextItem">
                            <p>WhatsApp <span class="hoverUnderline"><a href='https://wa.me/9720559475812' target='_blank'>0559475812</a></span></p>
                        </div>
                        <div class="contact__comTextItem">
                            <p>Email <span class="hoverUnderline"><a href='mailto:info@takeabreak.co.il'>info@takeabreak.co.il</a></span></p>
                        </div>
                        <div class="contact__comTextItem">
                            <p>Адрес <span><span>Emanuel Ringelblum 3, Holon</span></span></p>
                        </div>
                        <div class="contact__comTextItem">
                            <p>График работы <span><span>вс - чт с 10:00 до 20:00,<br>пт с 10 до 16, вс - выходной</span></span></p>
                        </div>
                    </div>
                    <div class="contact__comIcons"><a class="contact__comIcon hoverIcon" href="https://www.instagram.com/takeabreak_desserts/" target="_blank"><img src="/img/common/inst.png" alt="image"></a><a class="contact__comIcon hoverIcon" href="https://www.facebook.com/TABdesserts/" target="_blank"><img src="/img/common/facebook.png" alt="image"></a>
                    </div>
                </div>
                <form class="contact__form" action="{{ route('send_contact_form') }}" method="POST">
                    @csrf
                    <input type="hidden" name="lang" value="{{$lang}}">
                    <input class="blockTextField validateText" type="text" placeholder="Ваше имя" name="clientName" data-text="Имя">
                    <input class="blockTextField inputCall validatePhone" type="text" placeholder="+972 999-999-9999" name="phone" data-text="Телефон">
                    <textarea class="blockTextField validateText" placeholder="Напишите ваш вопрос" name="question" data-text="Вопрос"></textarea>
                    <div id="captcha_1"></div>
                    <button class="contact__formSend blockBtn blockBtn--bgc">отправить нам сообщение</button>
                </form>
            </div>
        </div>
    </div>
</section>
<section class="profitably" id="anchor-profitably">
    <div class="container">
        <div class="profitably__body">
            <div class="profitably__content">
                <h2 class="profitably__title blockTitle">Оптовое предложение</h2>
                <div class="profitably__text blockText"><p>Наш магазин приглашает к сотрудничеству оптовых и мелкооптовых покупателей.</p>
                    <p>Если вы планируете регулярное оформление заказов у нас на сумму более 1500₪ и хотите при этом сэкономить ваши время и деньги,
                        обратите внимание на наше предложение.</p>
                </div>
                <a class="profitably__btn blockBtn blockBtn--transparent" href="{{ route('wholesale_'.$lang) }}">узнать подробнее</a>
            </div>
            <div class="profitably__img"><img src="/img/common/wholesale.webp" alt="image"></div>
        </div>
    </div>
</section>


@include("shop.$lang.master_popap")


<div class="popupMessage {{ $popapp_message }}">
    <div class="container">
        <div class="popupMessage__body">
            <button class="popupMessage__closeBtn popupCloseBtn"></button>
            <div class="popupMessage__text">Спасибо, ваше сообщение получено. Мы ответим вам в ближайшее время.</div>
        </div>
    </div>
</div>
