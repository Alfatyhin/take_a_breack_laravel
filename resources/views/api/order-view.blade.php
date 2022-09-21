
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
        .print-button {cursor: pointer; ...}

        /* Стили для печати */
        @media print {
            .print-button {display: none;}
        }
    </style>
</head>

<body style="margin: 0; padding: 0;">
<table cellpadding="0" cellspacing="0" width="100%">
    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="max-width: 660px; min-width: 320px; background-color: #ffffff;">


        @section('content')

        @show



    </table>
</table>
</body>

</html>
