<?php
$translite = [
    'header_title' => [
        'ru' => 'магазин авторских сладостей',
        'en' => "shop of author's sweets",
        'he' => "shop of author's sweets"
    ],
    'com_back' => [
        'ru' => 'Возвращайтесь к нам снова!',
        'en' => "Come back to us again!",
        'he' => "Come back to us again!"
    ],
    'goo_shop' => [
        'ru' => 'перейти в магазин',
        'en' => "go to the store",
        'he' => "go to the store"
    ]
];
$lang = $order->orderData['lang'];

?>
    <!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>HTML Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
            line-height: 100%;
        }

        [style*="Roboto"] {font-family: 'Roboto', arial, sans-serif !important;}

        img {
            outline: none;
            text-decoration: none;
            border:none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%!important;
            margin: 0;
            padding: 0;
            display: block;
        }

        table td {
            border-collapse: collapse;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
    </style>
</head>

<body style="margin: 0; padding: 0;">
<table cellpadding="0" cellspacing="0" width="100%">
    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; background-color: #ffffff;">

        <!--HEADER-->
        <tr>
            <td align="center">
                <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; background-color: #FEEDD6">
                    <tr>
                        <td align="center" height="7">
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px; background-color: #FEEDD6">
                                <tr>
                                    <td align="center" width="97">
                                        <img width="86" src="https://i.ibb.co/QrGvFb4/Ellipse-13.png" height="86" style="max-width: 86px; min-width: 86px" alt="" align="left">
                                    </td>
                                    <td align="center">
                                        <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 32px;line-height: 38px;color: #121212; text-align: left">Take a Break.</p>
                                        <p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 20px;line-height: 23px;color: #121212; text-align: left">{{ $translite['header_title'][$lang] }}</p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <td align="center" height="7">
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

        @section('content')

        @show

    <!--Возвращайтесь к нам снова! ---------------->
        <tr>
            <td align="center">
                <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; background-color: #FEEDD6">
                    <tr>
                        <td align="center" height="17">
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px; background-color: #FEEDD6">
                                <tr>
                                    <td align="center"><p style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 22px;line-height: 29px;text-align: center;text-transform: uppercase;color: #000000;">{{ $translite['com_back'][$lang] }}</p></td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <td align="center" height="17">
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

        <tr>
            <td align="center">
                <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px;">
                    <tr>
                        <td align="center" height="25">
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table align="center" cellpadding="0" cellspacing="0" width="95%" style="max-width: 600px; min-width: 300px;">
                                <tr>

                                    <td align="center" width="260">
                                        <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 96px; min-width: 96px;">
                                            <tr>
                                                <td align="left">
                                                    <a href="#" target="_blank" style="text-decoration: none">
                                                        <img width="33" height="33" src="https://i.ibb.co/SxvZx2m/inst.png" style="display: block" alt="">
                                                    </a>
                                                </td>
                                                <td align="right">
                                                    <a href="#" target="_blank" style="text-decoration: none">
                                                        <img width="33" height="33" src="https://i.ibb.co/6HYQmh8/facebook.png" style="display: block" alt="">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20"></td>
                                </tr>
                                <tr>
                                    <td align="center"><a href="tel:+9720559475812" style="margin: 0; padding: 0; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 600;font-size: 17px;line-height: 20px;letter-spacing: 0.05em;color: #121212; text-decoration: none; text-align: center">+972 055-947-5812</a></td>
                                </tr>
                                <tr>
                                    <td align="center"><p style="margin: 0; padding: 0; margin-top: 10px; font-family: 'Roboto',sans-serif;font-style: normal;font-weight: 400;font-size: 14px;line-height: 16px;text-align: center;color: #121212;">Israel, Emanuel Ringelblum 3, Holon</p></td>
                                </tr>
                                <tr>
                                    <td height="40"></td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <table align="center" cellpadding="0" cellspacing="0" width="300" border="0" style="background-color: #AD7D80; border-radius:10px;border-spacing:0;display:inline-block;border-collapse:separate; overflow: hidden; text-align: center;">
                                            <tr>
                                                <td align="center" height="60" width="300" valign="middle"><a href="#" target="_blank" style="text-decoration: none; color: #ffffff; background-color: #AD7D80;  display: block;font-family: 'Roboto', sans-serif; font-size: 20px; font-weight: 400; width: 100%; line-height: 60px; text-align: center; text-transform: uppercase">{{ $translite['goo_shop'][$lang] }}</a></td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <td align="center" height="30">
                        </td>
                    </tr>
                </table>

            </td>
        </tr>


    </table>
</table>
</body>

</html>
