
<div class="popupAreasInfo">
    <div class="container">
        <div class="popupAreasInfo__body">
            <button class="popupAreasInfo__closeBtn popupCloseBtn"></button>
            <div class="popupAreasInfo__title blockTitle">Зоны доставки и цены</div>
            <table class="popupAreasInfo__table">
                <thead>
                <tr>
                    <th>Зона доставки</th>
                    <th>Мин. сумма заказа</th>
                    <th>Стоимость доставки</th>
                </tr>
                </thead>
                <tbody>
                @include("shop.layouts.delivery")
                </tbody>
            </table>
            <div class="popupAreasInfo__note">
                <div class="popupAreasInfo__noteItem popupAreasInfo__noteItem--location"><img src="/img/common/location.png" alt="location">
                    <div class="popupAreasInfo__noteItemText">
                        <p><span>Самовывоз бесплатно по адресу:</span> Holon, Emanuel Ringelblum 3. Пожалуйста, напишите или позвоните нам за 15-20 минут до прибытия. При самовывозе <span>скидка 2%</span> от стоимости заказа.</p>
                    </div>
                </div>
                <div class="popupAreasInfo__noteItem popupAreasInfo__noteItem--location--whatsapp">
                    <a href="https://wa.me/9720559475812">
                        <img src="/img/common/whatsapp.png" alt="whatsapp">
                    </a>
                    <div class="popupAreasInfo__noteItemText">
                        <p>Напишите нам, чтобы уточнить стоимость доставки в другие города.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

