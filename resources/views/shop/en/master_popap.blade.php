
<div class="popupAreasInfo">
    <div class="container">
        <div class="popupAreasInfo__body">
            <button class="popupAreasInfo__closeBtn popupCloseBtn"></button>
            <div class="popupAreasInfo__title blockTitle">Delivery zones and prices</div>
            <table class="popupAreasInfo__table">
                <thead>
                <tr>
                    <th>Delivery zone</th>
                    <th>Min. order price</th>
                    <th>Cost of delivery</th>
                </tr>
                </thead>
                <tbody>
                @include("shop.layouts.delivery")
                </tbody>
            </table>
            <div class="popupAreasInfo__note">
                <div class="popupAreasInfo__noteItem popupAreasInfo__noteItem--location"><img src="./img/common/location.png" alt="location">
                    <div class="popupAreasInfo__noteItemText">
                        <p><span>Pickup free of charge at:</span> Holon, Emanuel Ringelblum 3. Please notify or call us 15-20 minutes prior to arrival. At self-delivery <span>2% discount</span> from the cost of the order.</p>
                    </div>
                </div>
                <div class="popupAreasInfo__noteItem popupAreasInfo__noteItem--location--whatsapp">
                    <a href="https://wa.me/9720559475812">
                        <img src="/img/common/whatsapp.png" alt="whatsapp">
                    </a>
                    <div class="popupAreasInfo__noteItemText">
                        <p>Write to us to clarify the cost of delivery to other cities.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
