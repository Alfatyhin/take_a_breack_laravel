
console.log('test 1');
$(function() {


    var url = "https://privateqa.invoice4u.co.il/Services/MeshulamService.svc?singleWsdl";
    var token = "e0ccd9d2-d63e-494f-83c5-f058362ab23f";
    var data = {
        "Description": "a description of the order",
        "Email": "carin@invoice4u.co.il",
        "FullName": "carin test",
        "Invoice4UUserApiKey": token,
        "PaymentsNum": 1,
        "Phone": "0500000000",
        "ReturnUrl": "https://www.google.co.il",
        "Sum": 1,
        "Type": 1,
        "OrderIdClientUsage": "111222",
        "DocumentType": 3,
        "IsDocCreate": true,
        "IsManualDocCreationsWithParams": true,
        "DocItemName": "test",
        "DocItemQuantity": "1",
        "DocItemPrice": "1",
        "DocItemTaxRate": "17",
        "IsQaMode": true
    };


    $.ajax({
        'url': url,
        'type': "GET",
        'data': data,
        'success': function (result) {
            console.log(result);
        }
    });

});
