jQuery(document).ready(function ($) {


let catId = $('select.categories_list').val();
SetProducts(catId);

$('select.categories_list').on('change', function () {
    let catId = $('select.categories_list').val();
    SetProducts(catId);
});

function SetProducts(catId) {
    $('.prodicts_list').html('');
    var option = '';
    for (key in products) {
        var product = products[key];
        var prod_cat_id = product['category_id'];
        if (prod_cat_id == catId) {
            console.log(product['name'])
            option = `${option} <option value="${product['id']}"> ${product['name']}</option>`;
        }



    }
    var box = `<select name="dey_offer_id">${option}</select>`;
    $('.prodicts_list').html(box);
    return true;
}

});
