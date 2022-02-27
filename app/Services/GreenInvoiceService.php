<?php


namespace App\Services;


use App\Models\Orders;
use Illuminate\Support\Facades\Storage;

class GreenInvoiceService
{

    private $appId;
    private $secret;
    private $appUrl;

    private $token;
    private $timeOut;

    public function __construct(Orders $order)
    {
        // настройки аккаунта для инвойса
        $dataJson = Storage::disk('local')->get('data/app-setting.json');
        $invoiceSettingData = json_decode($dataJson, true);
        // для PayPal
        if ($order->paymentMethod == 3) {
            $this->setMode($invoiceSettingData['invoice_mode_paypal']);
        } elseif ($order->paymentMethod == 2) {
            $this->setMode($invoiceSettingData['invoice_mode_cache']);

        } else {
            $this->setMode(1);
        }


        $this->appUrl = $_ENV['GREENINVOICE_APP_URL'];
    }

    public function setMode($mode) : self
    {

        if ($mode == 2) {
            $this->secret = $_ENV['GREENINVOICE_APP_SECRET_2'];
            $this->appId  = $_ENV['GREENINVOICE_APP_ID_2'];
        } else {
            $this->secret = $_ENV['GREENINVOICE_APP_SECRET'];
            $this->appId  = $_ENV['GREENINVOICE_APP_ID'];
        }

        return $this;
    }

    private static $errors = [
        '401'  =>' В доступе отказано, подключитесь снова.',
        '403'  => 'Доступ запрещен.',
        '404'  => 'Товар не найден.',
        '405'  => 'Метод не разрешен.',
        '1002' => 'Поле там пустое.',
        '1003' => 'В аккаунте не найдено активных компаний.',
        '1004' => 'Пустое тело запроса или недопустимый JSON.',
        '1005' => 'Ошибка при сохранении.',
        '1006' => 'Пожалуйста, продлите встречу.',
        '1007' => 'Правильное разрешение отсутствует.',
        '1008' => 'Элемент не обновлен, потому что не было внесено никаких изменений.',
        '1009' => 'Необходимо выбрать адреса электронной почты. ',
        '1010' => 'Приложение с такими данными уже существует.  ',
        '1011' => 'Неподдерживаемый язык.',
        '1012' => 'Это подтверждается более высоким назначением.',
        '1013' => 'Пустой или недействительный файл.',
        '1014' => 'Неподдерживаемый тип файла.',
        '1015' => 'Для проведения операции необходимо погасить задолженность по счету.',
        '1016' => 'Ошибка обновления базы данных.',
        '1017' => 'Общая ошибка.',
        '1018' => 'Ошибка отправки электронной почты. Адрес электронной почты может быть заблокирован или неверен.',
        '1019' => 'Электронный адрес получателя заблокирован для отправки.                  ',
        '1020' => 'Ошибка шифрования.',
        '1100' => 'Недействительное поле {id}',
        '1101' => 'На обновление не было отправлено ни одного поля.',
        '1102' => 'Неверный адрес электронной почты.',
        '1103' => 'Неверный URL.',
        '1104' => 'Недействительный код страны.',
        '1105' => 'Неверное значение условий оплаты.',
        '1106' => 'Недействительное значение кода валюты.',
        '1107' => 'Недействительный коэффициент конверсии, значение должно быть больше нуля.',
        '1108' => 'Неверная ставка НДС. Должна быть от 0 до 1.',
        '1109' => 'В документе есть строка с НДС. ',
        '1110' => 'Неправильная цена.',
        '2000' => 'Аккаунт по-прежнему неактивен.',
        '2001' => 'заблокированный пользователь. ',
        '2002' => 'Неверный адрес электронной почты или пароль.',
        '2003' => 'Пользователь не существует. ',
        '2004' => 'Пожалуйста, введите имя.    ',
        '2005' => 'Пожалуйста, укажите фамилию.',
        '2006' => 'Неправильный пароль. Должно быть 8-16 цифр или символов.',
        '2007' => 'Занятая почта.',
        '2008' => 'Неверный текущий пароль.',
        '2009' => 'Идентификационный номер. Инвалид.',
        '2010' => 'Аккаунт уже активирован.',
        '2012' => 'Недействительное значение идентификатора.',
        '2013' => 'Неверное значение типа.',
        '2014' => 'Неверные ключевые данные. ',
        '1111' => 'Номер дилера / CP неверен.',
        '1112' => 'Документы добавить не удалось. В этом месяце по счету было представлено максимальное количество документов.',
        '1113' => 'Поле квитанции не отправлено.',
        '1114' => 'Недействительная дата начала.',
        '1115' => 'Недействительная дата окончания.',
        '1116' => 'Неверная сумма НДС.',
        '1117' => 'Недействительная дата.',
        '1118' => 'Приобрести дополнительные услуги без активного маршрута за плату невозможно.',
        '1119' => 'Размер страницы должен быть не более 100.',
        '1120' => 'Адрес домена электронной почты недействителен. ',
        '2100' => 'Неправильный тип дилера. ',
        '2101' => 'Название компании не может быть пустым.',
        '2102' => 'Во время встречи нельзя добавить больше компаний. ',
        '2103' => 'Пожалуйста, заполните юридический адрес.',
        '2104' => 'Укажите город.                          ',
        '2105' => 'Неверный файл или его размер превышает 5 МБ.',
        '2106' => 'Неподдерживаемый тип файла.    ',
        '2107' => 'Неверный номер шаблона дизайна.',
        '2125' => 'Номер скина в неправильном формате форматирования.',
        '2126' => 'Неверный процент аванса. Нормальное значение - это число от 0 до 35.',
        '2127' => 'Неправильное значение поля.',
        '2128' => 'Неправильная профессиональная ценность.',
        '2129' => 'Недопустимое значение порядка форматирования полей документа. ',
        '2108' => 'Ничего не найдено.',
        '2109' => 'Номер отделения банка может содержать до 5 символов.',
        '2110' => 'В аккаунте уже зарегистрирован бизнес с таким названием.',
        '2111' => 'Номер дилера / CP уже существует в аккаунте.',
        '2112' => 'Номер дилера / HF уже существует в системе под другой учетной записью.',
        '2113' => 'Номер дилера / CP уже прописан в аккаунте дважды. Возможно создание до 2 предприятий с одинаковым количеством дилеров. ',
        '2114' => 'Неверный номер файла вычета.',
        '2115' => 'Исходный процент удержания неверен. Нормальное значение - это число от 0 до 35. ',
        '2116' => 'Недействительное значение метода отчетности.',
        '2117' => 'Вы можете добавить до 3-х адресов электронной почты для учета.',
        '2118' => 'Некоторые поля не обновлены. Есть другой бизнес с тем же номером дилера, и поэтому между ними должна поддерживаться разница в диапазоне не менее 8000. ',
        '2119' => 'Неправильная нумерация документов. Нормальные значения от 1 до 9999999999.                                                                             ',
        '2120' => 'Поле {типы} не отправлено.',
        '2121' => 'Эту нумерацию нельзя было обновить, поскольку в этом году уже были созданы документы.',
        '2122' => 'Эту операцию нельзя выполнить в замороженном бизнесе.',
        '2123' => 'Недействительная ценность компании {id}.',
        '2124' => 'Содержимое в нижней части документа ограничено 2000 символами.',
        '2200' => 'Вы можете добавить клиенту до 3-х адресов электронной почты.',
        '2201' => 'Пожалуйста, введите имя клиента.',
        '2202' => 'Невозможно объединить активного клиента.',
        '2203' => 'Созданный для него клиент не может быть удален.',
        '2204' => 'Поле {id} клиента цели не отправлено. ',
        '2205' => 'Новых клиентов не найдено или импортированный файл недействителен.',
        '2206' => 'Вы можете добавить к заказчику до 3-х тегов.',
        '2207' => 'Неверный файл или его размер превышает 5 МБ.',
        '2208' => 'Неправильный баланс.',
        '2209' => 'Неправильный доход.',
        '2210' => 'Невозможно удалить клиента с зарезервированной кредитной картой.',
        '2250' => 'Вы можете добавить к провайдеру до 3-х адресов электронной почты.',
        '2251' => 'Пожалуйста, укажите здесь сомнение.',
        '2252' => 'Невозможно объединить активного провайдера.',
        '2253' => 'Невозможно удалить поставщика, который был связан с расходами.',
        '2254' => 'Поле {id} целевого провайдера не отправлено.',
        '2255' => 'В файле импорта не обнаружено новых и действительных поставщиков.',
        '2256' => 'Вы можете добавить к заказчику до 3-х тегов.',
        '2257' => 'Неверный файл или его размер превышает 5 МБ.',
        '2258' => 'Неправильный баланс.',
        '2259' => 'Неправильный доход. ',
        '2300' => 'Пожалуйста, введите название предмета.',
        '2301' => 'Пожалуйста, заполните описание товара.',
        '2302' => 'В файле импорта не обнаружено новых подходящих элементов.',
        '2305' => 'Неверный файл или его размер превышает 5 МБ.',
        '2400' => 'Документ, который не открыт, не может быть закрыт.',
        '2401' => 'Документ, который не был закрыт вручную, открыть невозможно',
        '2402' => 'Поле {ids} не отправлено или недействительно.',
        '2403' => 'Тип документа не отправлен или не поддерживается для этого типа бизнеса.                     ',
        '2404' => 'Поле {linkType} недействительно или отсутствует. Может быть только (копия | отмена | ссылка).',
        '2405' => 'Выбрана будущая дата или слишком ранняя для этого типа документа.    ',
        '2406' => 'Недействительное языковое значение.',
        '2407' => 'Недействительное значение кода валюты.',
        '2408' => 'Дата из-за недействительной.',
        '2409' => 'Неверный вид НДС.',
        '2410' => 'Отсутствует клиентское поле.',
        '2411' => 'Заказчика не существует.',
        '2412' => 'Счет-фактура может быть выставлен только с типом налоговой накладной.',
        '2413' => 'Пожалуйста, заполните хотя бы одну услугу или предмет.',
        '2414' => 'Неверная сумма платежа. Должно быть десятичное число.',
        '2415' => 'Описание строки не может быть пустым.',
        '2416' => 'Неверная сумма документа. Должно быть отличным от нуля.',
        '2417' => 'Неверная сумма документа. Должно быть больше нуля.     ',
        '2418' => 'Неверная сумма документа. Должно быть больше или равно нулю.',
        '2419' => 'Пожалуйста, заполните хотя бы одну строку квитанции.',
        '2420' => 'Неверный тип скидки. Может быть «суммой» или «процентом».',
        '2421' => 'Неверный код типа платежа.',
        '2422' => 'Несоответствие суммы поступлений и выплат.',
        '2423' => 'Неверный тип кредитной карты.',
        '2424' => 'Неверный код типа транзакции.',
        '2425' => 'Количество платежей может быть всего 1-36.',
        '2426' => 'Пустая, будущая или недействительная дата получения.',
        '2427' => 'Документ с цифровой подписью нельзя отправить по электронной почте.  ',
        '2428' => 'Пожалуйста, заполните хотя бы одну строку квитанции в дополнение к первоначальному вычету.',
        '2429' => 'Связанный документ не найден.',
        '2430' => 'Количественное значение должно быть больше 1.',
        '2431' => 'Максимально допустимая сумма строк дохода / выплат - 200 на документ.',
        '2432' => 'Неподдерживаемая копия типа документа.',
        '2433' => 'Неподдерживаемый тип статуса рассылки почты.',
        '2434' => 'Значение суммы чека должно отличаться от 0.',
        '2435' => 'Недействительный текст с аннотациями.',
        '2436' => 'Недопустимое значение нижнего колонтитула.',
        '2437' => 'Недействительное значение отправки документа.',
        '2500' => 'Неверный адрес.',
        '2501' => 'Отсутствует категория.',
        '2502' => 'Отсутствует заголовок.',
        '2503' => 'Адрес отсутствует.',
        '2504' => 'Пустой список сортировки.',
        '2600' => 'Активного клирингового терминала не найдено. ',
        '2601' => 'Возникла проблема с кредитным дебетом, обратитесь в службу поддержки.',
        '2602' => 'Ошибка подключения плагина.',
        '2603' => 'Ошибка дебетовой карты кредитной карты. ',
        '2604' => 'Неверное значение расхода.',
        '2605' => 'Содержание документа не отправлено.',
        '2606' => 'Ошибка при изменении статуса терминала. ',
        '2607' => 'Неверный тип токена. ',
        '2608' => '4 цифры кредитной карты недействительны.',
        '2609' => 'Неверный тип идентификатора транзакции. ',
        '2610' => 'Неверная сумма платежа. Должно быть больше нуля.',
        '2611' => 'Кредитная ошибка.',
        '2612' => 'Неверный идентификатор транзакции.',
        '2613' => 'Неверный тип статуса транзакции.',
        '2614' => 'Неверное настраиваемое поле.',
        '2615' => 'Отмененная транзакция не может быть отменена',
        '2616' => 'Отменить транзакцию невозможно, пожалуйста, сделайте кредит.',
        '2617' => 'Никакую сделку нельзя выиграть.',
        '2618' => 'Отмененная транзакция не может быть отменена. ',
        '2619' => 'Неверное поле типа биллинга',
        '2620' => 'Запрос выполнен в прошлом.',
        '2621' => 'Транзакцию нельзя отменить или выиграть.',
        '2622' => 'Неверное имя держателя карты.',
        '2623' => 'Недействительный срок действия карты.',
        '2624' => 'Невозможно выиграть транзакцию на сумму, превышающую первоначальную.',
        '2625' => 'У выбранного расширения нет разрешения на сохранение кредитных карт.',
        '2626' => 'Неверное имя плательщика.',
        '2627' => 'Сохраненная кредитная карта не существует или неактивна. ',
        '2700' => 'Неверный ключ API.',
        '2701' => 'Неверный статус ключа API.',
        '2702' => 'Тип ключа требует дополнительной идентификации. ',
        '2800' => 'Пустой учетный код.',
        '2801' => 'Несинхронизируемый тип интерфейса.',
        '2802' => 'Неверный тип внешнего интерфейса. ',
        '2803' => 'Ошибка подключения к внешнему интерфейсу.',
        '2804' => 'Ошибка плагина.   ',
        '2805' => 'Неактивный плагин.',
        '2900' => 'Недействительная дата начала.',
        '2901' => 'Недействительная дата окончания или не позволяет создать хотя бы один документ.',
        '2902' => 'Неправильный период возврата.',
        '2903' => 'Недействительный статус фиксатора.',
        '2904' => 'Неверная дата удержания.',
        '3000' => 'Общая ошибка.',
        '3001' => 'В аккаунте нет кредитной карты.',
        '3002' => 'Тип отправленной подписки не существует.',
        '4000' => 'Неверный тип данных.',
        '3003' => 'Неподдерживаемый тип действия.',
        '3004' => 'Одно или несколько полей отсутствуют.',
        '3005' => 'Такая же встреча существует в аккаунте.',
        '3006' => 'Суточная квота подписок закончилась. Повторите попытку завтра или обратитесь к менеджеру портфеля клиентов, чтобы увеличить дневную квоту.       ',
        '3007' => 'Суточная квота на подключение закончилась. Повторите попытку завтра или обратитесь к менеджеру портфеля клиентов, чтобы увеличить дневную квоту. ',
        '3008' => 'Не удалось активировать соединение.',
        '3009' => 'Неподдерживаемый тип транзакции.',
        '3010' => 'На счету есть задолженность. Пожалуйста, погасите задолженность перед покупкой новой подписки. ',
        '3100' => 'Пустой тег имени.',
        '3101' => 'Максимальная длина тега может составлять до 30 символов.',
        '3200' => 'Неподдерживаемый тип задачи.',
        '3300' => 'Неподдерживаемый тип статуса расходов.',
        '3301' => 'Неверная сумма расходов.',
        '3302' => 'Сумма НДС в неверной статье расходов.',
        '3303' => 'Приложите файл для публикации.',
        '3304' => 'Неподдерживаемый тип черновика.',
        '3305' => 'Связанной проблемы не обнаружено.',
        '3306' => 'Неверный номер расходного документа.',
        '3307' => 'Недействительная дата документа о выдаче. ',
        '3308' => 'Неверный тип расходного документа.',
        '3309' => 'Документы о доходах от бизнеса не могут быть классифицированы как расходы.',
        '3310' => 'Неправильный отчетный месяц.',
        '3311' => 'Пожалуйста, заполните данные поставщика.',
        '3312' => 'Пожалуйста, заполните информацию о типе расходов.',
        '3313' => 'Недопустимое поле затрат.',
        '3314' => 'Расходы не могут быть обновлены после отчета.',
        '3315' => 'Незарегистрированный расход открыть нельзя.',
        '3316' => 'Неоткрытые траты не могут быть закрыты.',
        '3317' => 'Неверный вид расхода.',
        '3318' => 'Неправильное расположение офиса.',
        '3319' => 'Неправильное количество комнат.',
        '3320' => 'Неверный номер автомобиля.',
        '3400' => 'Пожалуйста, введите название учетной записи.',
        '3401' => 'Неправильно признанная ставка расходов.',
        '3402' => 'Неправильно признанная ставка НДС.',
        '3403' => 'Неверный тип учетной записи.',
        '3404' => 'Код 6111 недействителен.',
        '3405' => 'Тип расходов, связанный с расходами, удалить нельзя.',
    ];


    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function createToken()
    {

        $url = $this->appUrl . 'account/token';

        $curl = curl_init();

        $data = [
            "id" => $this->appId,
            "secret" => $this->secret
        ];


        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);


        $response =json_decode($response, true);

        //var_dump('getToken', $response);

        if (isset($response['errorCode'])) {
            return "errorCode: " . $response['errorCode'];
        } elseif ($response['token']) {
            $this->token = $response['token'];
            $this->timeOut = $response['expires'];

            return $response;
        } else {
            return "Unknown error";
        }


    }

    public function newDoc(array $orderData)
    {
        $this->createToken();

        $email = $orderData['email'];
        $url = $this->appUrl . 'documents';

        $remarks = $orderData['remarks'];
        if (!empty($orderData['delivery'])) {
            $remarks .= $orderData['delivery'];
        }
        if (!empty($orderData['tips'])) {
            $remarks .= $orderData['tips'];
        }

        $lang = $orderData['lang'];

        if ($lang != 'he' && $lang != 'en') {
            $lang = 'en';
        }

        if (!empty($orderData['phone'])) {
            $phone = $orderData['phone'];
        } else {
            $phone = '';
        }
        $name = AppServise::TransLit($orderData['name']);
        $name = AppServise::TranslitIvrit($name);

        $data = [
            "description"  => $orderData['orderNames'],
            "remarks"      => $remarks,
            "footer"       => "",
            "emailContent" => "Email content",
            "type"         => 400,
            "date"         => $orderData['payDate'],
            "lang"         => $lang,
            "currency"     => "ILS",
            "vatType"      => 0,
            "rounding"     => true,
            "signed"       => true,
            "attachment"   => true,
            "maxPayments"  => 1,
            "client"       => [
                "name"     => $name,
                "phone"    => $phone,
                "city"     => $orderData['city'],
                "address"  => $orderData['address'],
                "emails"   => [$email],
                "add"      => true,
                'self'     => false
            ],
            "income"  => [$orderData['items']],
            "payment" => [
                [
                    "date"          => $orderData['payDate'],
                    "type"          => $orderData['type'], // 3 для кредитной карты 1 для наличных
                    "price"         => $orderData['total'],
                    "currency"      => "ILS",
                    "currencyRate"  => 1,
                    "bankName"      => $orderData['bankName'],
                    "transactionId" => $orderData['payId'],
                    "appType"       => 1,
                    "subType"       => 1,
                    "dealType"      => 2,
                    "numPayments"   => 1,
                    "firstPayment"  => 10
                ]
            ]

        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '  . $this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }

    public static function getError($err)
    {
        return "<br> ошибка создания документа $err - " . self::$errors[$err] . '<br>';
    }

}
