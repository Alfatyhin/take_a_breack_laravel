

@include("shop.$lang.anchor-welcome")

@include("shop.$lang.anchor-select")

<section class="offer">
    <div class="container">
        <div class="offer__body">
            @include("shop.layouts.index_dey_offer")
        </div>
    </div>
</section>
{{--<section class="size">--}}
{{--    <div class="size__bg"></div>--}}
{{--    <div class="container">--}}
{{--        <div class="size__body">--}}
{{--            <h2 class="size__title blockTitle">Choose the right cake size</h2>--}}
{{--            <div class="size__itemsWrapper">--}}
{{--                <div class="size__itemsBg"></div>--}}
{{--                <div class="size__items">--}}
{{--                    <div class="size_item">--}}
{{--                        <div class="size_itemImg"><img class="size_itemImg--main" src="./img/index/size/4_parts.webp" alt="image">--}}
{{--                            <img class="size_itemImg--hover" src="./img/index/size/4_parts_anim.webp" alt="image"></div>--}}
{{--                        <div class="size_itemTitle">Size Mini</div>--}}
{{--                        <div class="size_itemWeight">Weight: 600g ⌀12cm</div>--}}
{{--                        <div class="size_itemText blockText">A small cake, suitable for a small company of 4-5 people</div>--}}
{{--                    </div>--}}
{{--                    <div class="size_item">--}}
{{--                        <div class="size_itemImg"><img class="size_itemImg--main" src="./img/index/size/8_parts.webp" alt="image">--}}
{{--                            <img class="size_itemImg--hover" src="./img/index/size/8_parts_anim.webp" alt="image"></div>--}}
{{--                        <div class="size_itemTitle">Size Family</div>--}}
{{--                        <div class="size_itemWeight">Weight:1200g ⌀16cm</div>--}}
{{--                        <div class="size_itemText blockText">For warm meetings over a cup of tea in a family circle for 8-10 people</div>--}}
{{--                    </div>--}}
{{--                    <div class="size_item">--}}
{{--                        <div class="size_itemImg"><img class="size_itemImg--main" src="./img/index/size/16_parts.webp" alt="image">--}}
{{--                            <img class="size_itemImg--hover" src="./img/index/size/16_parts_anim.webp" alt="image"></div>--}}
{{--                        <div class="size_itemTitle">Size Celebration</div>--}}
{{--                        <div class="size_itemWeight">Weight: 2400g ⌀20cm</div>--}}
{{--                        <div class="size_itemText blockText">Cake for special occasions for a large company of 16-20 people</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<section class="about" id="anchor-about">
    <div class="container">
        <div class="about__body">
            <div class="about__content">
                <h2 class="about__title blockTitle">Take a Break –<br>this is us: Zhenya and Liza</h2>
                <div class="about__text blockText"><p>We always wanted something sweet and that it was not harmful to health. At one point, we decided to try to give up sweets from the store and make sweets and cakes at home to eliminate gluten, sugar, lactose, harmful dyes and preservatives...</p><p>It has grown into a whole studio of healthy desserts! All our friends, and later customers, were delighted with the taste of our sweets. They say: "This is much tastier than any store-bought cake with sugar." The secret ingredient is love!</p><p>Follow us on Instagram and be the first to know about new products and discounts!</p></div><a class="about__btn blockBtn blockBtn--bgc" href="https://www.instagram.com/takeabreak_desserts/" target="_blank">go to our instagram</a>
            </div>
            <div class="about__images"><img data-src="./img/index/about/1.webp" alt="image"><img data-src="./img/index/about/2.webp" alt="image"><img data-src="./img/index/about/3.webp" alt="image">
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
{{--                    <div class="mailing__title blockTitle"><h3>Craft chocolate as a gift</h3><img class="mailing__img" src="/img/index/mailing/gift.png" alt="image"></div>--}}
{{--                    <div class="mailing__video">--}}
{{--                        <video src="/video/index/mailing/chocolate.mp4" autoplay loop muted poster="/img/index/mailing/video_bg.webp"></video>--}}
{{--                    </div>--}}
{{--                    <div class="mailing__subtitle">Be the first to know about our new products, promotions and discounts! And get gifts!</div>--}}
{{--                    <div class="mailing__text blockText">--}}
{{--                        <p>Leave your email address in the field below and--}}
{{--                            Get a bar of craft chocolate from elite aromatic varieties of cocoa beans as a gift from our store.--}}
{{--                            Excellent natural chocolate without gluten and sugar, made using a unique technology,--}}
{{--                            will give you inexpressible pleasure. </p>--}}
{{--                        <p>We will send you only fresh and useful information about our product, no spam.</p>--}}
{{--                        <p>And if you tell us your date of birth, we will be happy to congratulate you on the holiday!</p>--}}
{{--                    </div>--}}
{{--                    <form class="mailing__form" action="{{ route('send_birthday') }}" method="POST">--}}
{{--                        @csrf--}}
{{--                        <input type="hidden" name="lang" value="{{ $lang }}">--}}
{{--                        <div class="mailing__formFields">--}}
{{--                            <div class="mailing__formField">--}}
{{--                                <label class="mailing__formFieldTitle">Enter your email address</label>--}}
{{--                                <div class="mailing__formInputWrapper">--}}
{{--                                    <input class="blockTextField validateText" type="text" placeholder="Your email" name="mailingMail" data-text="Email">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="mailing__formField">--}}
{{--                                <label class="mailing__formFieldTitle">Enter or select date of birth</label>--}}
{{--                                <div class="mailing__formInputWrapper">--}}
{{--                                    <input class="blockTextField inputDate" type="date" name="mailingBirthday" data-text="Birthday">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <button class="mailing__formSend blockBtn blockBtn--bgc">send data</button>--}}
{{--                        <div class="mailing__formNote">By clicking the SEND button, you agree to receive the newsletter</div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<section class="feedback" id="anchor-feedback">
    <div class="container">
        <div class="feedback__body">
            <h2 class="feedback__title blockTitle">What they say about our desserts</h2>
            <div class="feedback__slider">
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img data-src="./img/index/feedback/1.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img data-src="./img/index/feedback/2.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img data-src="./img/index/feedback/3.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img data-src="./img/index/feedback/1.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img data-src="./img/index/feedback/2.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img data-src="./img/index/feedback/3.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img data-src="./img/index/feedback/1.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img data-src="./img/index/feedback/2.webp" alt="image"></div>
                </div>
                <div class="feedback__sliderItem">
                    <div class="feedback__sliderItemContainer"><img data-src="./img/index/feedback/3.webp" alt="image"></div>
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
            <h2 class="info__title blockTitle">Delivery and payment</h2>
            <div class="info__content">
                <div class="info__map">
                    <!--<iframe loading="lazy" style="width: 100%; height: 100%;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3382.780969817665!2d34.786971815315084!3d32.02104628120692!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1502b4b0e0292707%3A0xb7476744837f4cd8!2zRW1hbnVlbCBSaW5nZWxibHVtIDMsIEhvbG9uLCDQmNC30YDQsNC40LvRjA!5e0!3m2!1sru!2spl!4v1628862790907!5m2!1sru!2spl">
                    </iframe>-->
					
	<iframe frameborder="0"
	class="lazyload"
    loading="lazy"
    allowfullscreen=""
    width="100%"
    height="100%"
    data-src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3382.780969817665!2d34.786971815315084!3d32.02104628120692!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1502b4b0e0292707%3A0xb7476744837f4cd8!2zRW1hbnVlbCBSaW5nZWxibHVtIDMsIEhvbG9uLCDQmNC30YDQsNC40LvRjA!5e0!3m2!1sru!2spl!4v1628862790907!5m2!1sru!2spl">
</iframe>

<script>
  if ('loading' in HTMLIFrameElement.prototype) {
    const iframes = document.querySelectorAll('iframe[loading="lazy"]');

    iframes.forEach(iframe => {
      iframe.src = iframe.dataset.src;
    });

  } else {
    // Динамический импорт библиотеки LazySizes
    const script = document.createElement('script');
    script.src =
      'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.2.2/lazysizes.min.js';
    document.body.appendChild(script);
  }

</script>
                </div>
                <div class="info__items">
                    <div class="info__item">
                        <h3 class="info__itemTitle">Pickup</h3>
                        <div class="info__itemText blockText">
                            <p>You can pick up our products yourself at <span>Emanuel Ringelblum 3, Holon.</span> Please call us 15 minutes before your arrival. We will be glad to meet you.</p>
                        </div>
                    </div>
                    <div class="info__item">
                        <h3 class="info__itemTitle">Courier delivery</h3>
                        <div class="info__itemText blockText">
                            <p>We deliver from Ashkelon to Haifa. The amount of payment is from 30 shekels. You can specify the exact cost of delivery and the minimum order amount <button class='areaAndPriceBtn blockText'>here</button>.</p>
                        </div>
                    </div>
                    <div class="info__item">
                        <h3 class="info__itemTitle">Payment</h3>
                        <div class="info__itemText blockText">
                            <p>You can pay for your order using PayPal, credit card, bit transfer or cash.</p>
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
            <h2 class="contact__title blockTitle">Contact us</h2>
            <div class="contact__content">
                <div class="contact__text">
                    <div class="contact__subtitle blockText">If you have questions or need help choosing, let us know, we will be happy to help you.</div>
                    <div class="contact__comText">
                        <div class="contact__comTextItem">
                            <p>WhatsApp <span class="hoverUnderline"><a href='https://wa.me/9720559475812' target='_blank'>0559475812</a></span></p>
                        </div>
                        <div class="contact__comTextItem">
                            <p>Email <span class="hoverUnderline"><a href='mailto:info@takeabreak.co.il'>info@takeabreak.co.il</a></span></p>
                        </div>
                        <div class="contact__comTextItem">
                            <p>The address <span><span>Emanuel Ringelblum 3, Holon</span></span></p>
                        </div>
                        <div class="contact__comTextItem">
                            <p>Schedule <span><span>Sun - Thu from 10:00 to 20:00,<br>Friday from 10:00 to 16:00, Sat - day off</span></span></p>
                        </div>
                    </div>
                    <div class="contact__comIcons"><a class="contact__comIcon hoverIcon" href="https://www.instagram.com/takeabreak_desserts/" target="_blank">
                            <img data-src="/img/common/inst.png" alt="image"></a><a class="contact__comIcon hoverIcon" href="https://www.facebook.com/TABdesserts/" target="_blank"><img data-src="./img/common/facebook.png" alt="image"></a>
                    </div>
                </div>
                <form class="contact__form" action="{{ route('send_contact_form') }}" method="POST">
                    @csrf
                    <input type="hidden" name="lang" value="{{$lang}}">
                    <input class="blockTextField validateText" type="text" placeholder="Your name" name="clientName" data-text="Имя">
                    <input class="blockTextField inputCall validatePhone" type="text" placeholder="+972 999-999-9999" name="phone" data-text="Телефон">
                    <textarea class="blockTextField validateText" placeholder="Write your question" name="question" data-text="Вопрос"></textarea>
                    <button class="contact__formSend blockBtn blockBtn--bgc">send us a message</button>
                </form>
            </div>
        </div>
    </div>
</section>
<section class="profitably" id="anchor-profitably">
    <div class="container">
        <div class="profitably__body">
            <div class="profitably__content">
                <h2 class="profitably__title blockTitle">Wholesale offer</h2>
                <div class="profitably__text blockText"><p>Our store invites wholesale and small wholesale buyers to cooperation.</p>
                    <p>If you plan to regularly place orders with us for more than 1500₪ and want to save your time and money at the same time, pay attention to our offer.</p>
                </div><a class="profitably__btn blockBtn blockBtn--transparent" href="{{ route('wholesale_'.$lang) }}">learn more</a>
            </div>
            <div class="profitably__img"><img data-src="./img/common/wholesale.webp" alt="image"></div>
        </div>
    </div>
</section>

@include("shop.$lang.master_popap")

<div class="popupMessage {{ $popapp_message }}">
    <div class="container">
        <div class="popupMessage__body">
            <button class="popupMessage__closeBtn popupCloseBtn"></button>
            <div class="popupMessage__text">Thank you, your message has been received. We will reply to you as soon as possible.</div>
        </div>
    </div>
</div>

