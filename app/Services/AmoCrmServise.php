<?php


namespace App\Services;

use AmoCRM\Client\AmoCRMApiClientFactory;
use AmoCRM\Collections\CatalogElementsCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Collections\TagsCollection;
use AmoCRM\Enum\InvoicesCustomFieldsEnums;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\CatalogElementsFilter;
use AmoCRM\Filters\CatalogsFilter;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\AccountModel;
use AmoCRM\Models\CatalogElementModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFields\TextareaCustomFieldModel;
use AmoCRM\Models\CustomFieldsValues\BirthdayCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\DateTimeCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ItemsCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\LinkedEntityCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultiselectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\NumericCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\PriceCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\SelectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\StreetAddressCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\BirthdayCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\DateCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\ItemsCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\LinkedEntityCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultiselectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\NumericCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\PriceCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\SelectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\StreetAddressCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\BaseCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\DateCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\ItemsCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\LinkedEntityCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultiselectCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\NumericCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\PriceCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\SelectCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\StreetAdressCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteType\CommonNote;
use AmoCRM\Models\NoteType\ServiceMessageNote;
use AmoCRM\Models\TagModel;
use App\Models\AppErrors;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Mockery\Exception;

class AmoCrmServise
{
    private $tokenFile = 'data/amo-assets.json';
    private static $tokensFile = 'data/amo-assets.json';
    private $apiClient;
    private $url = 'https://www.amocrm.com';
    private $open_stages = ['42684658' => 1, '42684652' => 1];

    public function __construct()
    {
        $oAuthConfig = new OAuthConfig();
        $oAuthService = new OAuthService();
        $apiClientFactory = new AmoCRMApiClientFactory($oAuthConfig, $oAuthService);
        $this->apiClient = $apiClientFactory->make();
    }

    public function getButton()
    {
        $state = bin2hex(random_bytes(16));
        Storage::disk('local')->put('data/amo-state.txt', $state);

        $apiClient = $this->apiClient;

        return $apiClient->getOAuthClient()->getOAuthButton(
            [
                'title'          => 'Установить интеграцию',
                'compact'        => true,
                'class_name'     => 'className',
                'color'          => 'default',
                'error_callback' => 'handleOauthError',
                'state'          => $state,
            ]
        );
    }

    public static function getTokens()
    {
        $accessTokens = Storage::disk('local')->get(self::$tokensFile);
        $accessToken = json_decode($accessTokens, true);

        if (
            isset($accessToken)
            && isset($accessToken['access_token'])
            && isset($accessToken['refresh_token'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new AccessToken([
                'access_token' => $accessToken['access_token'],
                'refresh_token' => $accessToken['refresh_token'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            exit('Invalid access token ');
        }

    }


    public function saveToken($accessToken)
    {
        if (
            isset($accessToken)
            && isset($accessToken['expires_in'])
            && isset($accessToken['access_token'])
            && isset($accessToken['refresh_token'])
            && isset($accessToken['baseDomain'])
        ) {

            $data = new AccessToken ($accessToken);

            Storage::disk('local')->put(self::getTokenFile(), json_encode($data));

            return true;
        } else {
            return AppErrors::addError('amo error-assets token', $accessToken);
        }
    }

    private function getTokenFile()
    {
        return $this->tokenFile;
    }

    public function getAccessTokenByCods($amoData)
    {

        /** Соберем данные для запроса */
        $data = [
            'client_id' => $_ENV['AMO_CLIENT_ID'],
            'client_secret' => $_ENV['AMO_CLIENT_SECRET'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => $_ENV['AMO_CLIENT_REDIRECT_URI'],
            'code' => $amoData['code'],
            'referer' => $amoData['referer'],
        ];

        $res = $this->getTokensToAmo($data);

        $tokenData = [
            'expires_in'    => $res['expires_in'],
            'access_token'  => $res['access_token'],
            'refresh_token' => $res['refresh_token'],
            'baseDomain'    => $amoData['referer'],
        ];
        return $tokenData;
    }

    private function getTokensToAmo($data)
    {

        $subdomain = $data['referer']; //Поддомен нужного аккаунта
        $link = 'https://' . $subdomain . '/oauth2/access_token'; //Формируем URL для запроса
        $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
        /** Устанавливаем необходимые опции для сеанса cURL  */
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];

        /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
        if ($code < 200 || $code > 204) {

            if (!empty($errors[$code])) {
                $err[$code]["error text"] = $errors[$code];
            }
            $err[$code]["request"] = json_decode($out, true);
            AppErrors::addError('amo-get tokens error', json_encode($err));

        } else {
            return json_decode($out, true);
        }
    }

    private function getApiClient()
    {
        $oAuthConfig = new OAuthConfig();
        $oAuthService = new OAuthService();
        $apiClientFactory = new AmoCRMApiClientFactory($oAuthConfig, $oAuthService);
        $apiClient = $apiClientFactory->make();
        $accessToken = $this->getTokens();
        $apiClient->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['baseDomain']);

        return $apiClient;
    }



    // создание сделки на амо для прода
    public function NewOrder(array $amoData)
    {
        $apiClient = $this->getApiClient();
        ///////////////////////////////////////////////////////////////////////////

        // текстовый тип кастомного поля
        $leadCustomFieldsValues = new CustomFieldsValuesCollection();

        if (!empty($amoData['ekwidId'])) {
            $notes = $amoData['ekwidId'] . ': ' .  $amoData['notes'];
            var_dump($notes);
            if (strlen($notes) < 255) {
                $textCustomFieldValueModel = new TextCustomFieldValuesModel();
                $textCustomFieldValueModel->setFieldId(352111);
                $textCustomFieldValueModel->setValues(
                    (new TextCustomFieldValueCollection())
                        ->add((new TextCustomFieldValueModel())->setValue($notes))
                );
                $leadCustomFieldsValues->add($textCustomFieldValueModel);
            }


            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(489653);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($amoData['ekwidId']))
            );
            $leadCustomFieldsValues->add($textCustomFieldValueModel);

            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(511579);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue('Ecwid'))
            );
            $leadCustomFieldsValues->add($textCustomFieldValueModel);

            $order_id = $amoData['ekwidId'];

        } else {

            $notes = $amoData['notes'];
            if (strlen($notes) < 255) {
                $textCustomFieldValueModel = new TextCustomFieldValuesModel();
                $textCustomFieldValueModel->setFieldId(352111);
                $textCustomFieldValueModel->setValues(
                    (new TextCustomFieldValueCollection())
                        ->add((new TextCustomFieldValueModel())->setValue($notes))
                );
                $leadCustomFieldsValues->add($textCustomFieldValueModel);
            }

            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(489653);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($amoData['order_id']))
            );
            $leadCustomFieldsValues->add($textCustomFieldValueModel);

            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(511579);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($amoData['api_mode']))
            );

            $leadCustomFieldsValues->add($textCustomFieldValueModel);

            if (!empty($amoData['room_number'])) {
                $textCustomFieldValueModel = new TextCustomFieldValuesModel();
                $textCustomFieldValueModel->setFieldId(511647);
                $textCustomFieldValueModel->setValues(
                    (new TextCustomFieldValueCollection())
                        ->add((new TextCustomFieldValueModel())->setValue($amoData['room_number']))
                );

                $leadCustomFieldsValues->add($textCustomFieldValueModel);
            }


            $order_id = $amoData['order_id'];
        }



//        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
//        $textCustomFieldValueModel->setFieldId(338459);
//        $textCustomFieldValueModel->setValues(
//            (new TextCustomFieldValueCollection())
//                ->add((new TextCustomFieldValueModel())->setValue($amoData['refer_URL']))
//        );
//        $leadCustomFieldsValues->add($textCustomFieldValueModel);

        if (!empty($amoData['utmData'])) {


            foreach ($amoData['utmData'] as $key => $item) {
                if ($key == 'source') {
                    $textCustomFieldValueModel = new TextCustomFieldValuesModel();
                    $textCustomFieldValueModel->setFieldId(492029);
                    $textCustomFieldValueModel->setValues(
                        (new TextCustomFieldValueCollection())
                            ->add((new TextCustomFieldValueModel())->setValue($item))
                    );
                    $leadCustomFieldsValues->add($textCustomFieldValueModel);
                }
                if ($key == 'campaign') {
                    $textCustomFieldValueModel = new TextCustomFieldValuesModel();
                    $textCustomFieldValueModel->setFieldId(492033);
                    $textCustomFieldValueModel->setValues(
                        (new TextCustomFieldValueCollection())
                            ->add((new TextCustomFieldValueModel())->setValue($item))
                    );
                    $leadCustomFieldsValues->add($textCustomFieldValueModel);
                }
                if ($key == 'medium') {
                    $textCustomFieldValueModel = new TextCustomFieldValuesModel();
                    $textCustomFieldValueModel->setFieldId(492031);
                    $textCustomFieldValueModel->setValues(
                        (new TextCustomFieldValueCollection())
                            ->add((new TextCustomFieldValueModel())->setValue($item))
                    );
                    $leadCustomFieldsValues->add($textCustomFieldValueModel);
                }

            }
        }


        if (!empty($amoData['to_presents'])) {

            if (!empty($amoData['to_presents']['presents_name'])) {
                $name = $amoData['to_presents']['presents_name'];
            } else {
                $name = 'noname';
            }
            if (!empty($amoData['to_presents']['presents_phone'])) {
                $phone =  $amoData['to_presents']['presents_phone'];
            } else {
                $phone = 000000000;
            }

            $checkboxCustomFieldValueModel = new CheckboxCustomFieldValuesModel();
            $checkboxCustomFieldValueModel->setFieldId(352113);
            $checkboxCustomFieldValueModel->setValues(
                (new CheckboxCustomFieldValueCollection())
                    ->add((new CheckboxCustomFieldValueModel())->setValue(true))
            );
            $leadCustomFieldsValues->add($checkboxCustomFieldValueModel);

        } else {
            $name = $amoData['name'];
            if (!empty($amoData['phone'])) {
                $phone =  $amoData['phone'];
            }
        }

        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(308397);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($name))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);

        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(512455);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($amoData['text_note']))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);

        if (!empty($phone)) {
            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(308401);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($phone))
            );
            $leadCustomFieldsValues->add($textCustomFieldValueModel);
        }


        // тип поля список
        $selectCustomFieldValueModel = new SelectCustomFieldValuesModel();
        $selectCustomFieldValueModel->setFieldId(308363);
        $selectCustomFieldValueModel->setValues(
            (new SelectCustomFieldValueCollection())
                ->add((new SelectCustomFieldValueModel())->setValue($amoData['payment']))
        );
        $leadCustomFieldsValues->add($selectCustomFieldValueModel);


        // тип поля дата-время
        $dateCustomFieldValueModel = new DateTimeCustomFieldValuesModel();
        $dateCustomFieldValueModel->setFieldId(462257);
        $dateCustomFieldValueModel->setValues(
            (new DateCustomFieldValueCollection())
                ->add((new DateCustomFieldValueModel())->setValue($amoData['date']))
        );
        $leadCustomFieldsValues->add($dateCustomFieldValueModel);
        // тип поля дата-время
        $dateCustomFieldValueModel = new DateTimeCustomFieldValuesModel();
        $dateCustomFieldValueModel->setFieldId(508997);
        $dateCustomFieldValueModel->setValues(
            (new DateCustomFieldValueCollection())
                ->add((new DateCustomFieldValueModel())->setValue($amoData['date']))
        );
        $leadCustomFieldsValues->add($dateCustomFieldValueModel);


        // тип поля список с мульти выбором
        $timeCustomFieldValueModel = new MultiselectCustomFieldValuesModel();
        $timeCustomFieldValueModel->setFieldId(462331);
        $timeCustomFieldValueModel->setValues(
            (new MultiselectCustomFieldValueCollection())
                ->add((new MultiselectCustomFieldValueModel())->setValue($amoData['time']))
        );
        $leadCustomFieldsValues->add($timeCustomFieldValueModel);


        // тип поля адрес
//        $adressCustomFeeldValueModel = new StreetAddressCustomFieldValuesModel();
//        $adressCustomFeeldValueModel->setFieldId(308403);
//        $adressCustomFeeldValueModel->setValues(
//            (new StreetAddressCustomFieldValueCollection())
//                ->add((new StreetAdressCustomFieldValueModel())->setValue($amoData['address']))
//        );
//        $leadCustomFieldsValues->add($adressCustomFeeldValueModel);
        // new
        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(509001);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($amoData['address']))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);

        //// добавление наименований заказа в теги
        $lead = new LeadModel();

        if (!empty($amoData['tags'])) {

            //Создадим тег
            $tagsCollection = new TagsCollection();
            foreach ($amoData['tags'] as $item) {
                if ($item) {
                    $tag = new TagModel();
                    $tag->setName($item);
                    $tagsCollection->add($tag);

                }

            }
            $lead->setTags($tagsCollection);
        }
        ///

        // комнплексное создание сделки
        $lead->setName($amoData['order name'])
            ->setPrice($amoData['order price'])
            ->setCustomFieldsValues($leadCustomFieldsValues) // прикрепление к сделке значений из полей выше
            ->setPipelineId($amoData['pipelineId'])
            ->setStatusId($amoData['statusId'])
            ->setRequestId($order_id);

        if (empty($amoData['clientAmoId'])) {

            $lead->setContacts(
                (new ContactsCollection())
                    ->add(
                        (new ContactModel())
                            ->setFirstName($amoData['name'])
                            ->setCustomFieldsValues(
                                (new CustomFieldsValuesCollection())
                                    ->add(
                                        (new MultitextCustomFieldValuesModel())
                                            ->setFieldCode('EMAIL')
                                            ->setValues(
                                                (new MultitextCustomFieldValueCollection())
                                                    ->add(
                                                        (new MultitextCustomFieldValueModel())
                                                            ->setValue($amoData['email'])
                                                    )
                                            )
                                    )
                                    ->add( // кастомное поле для языка
                                        (new SelectCustomFieldValuesModel())
                                            ->setFieldId(490441)
                                            ->setValues(
                                                (new SelectCustomFieldValueCollection())
                                                    ->add(
                                                        (new SelectCustomFieldValueModel())
                                                            ->setValue($amoData['lang'])
                                                    )
                                            )
                                    )
//                                    ->add(
//                                        (new TextCustomFieldValuesModel())
//                                            ->setFieldId(226599)
//                                            ->setFieldName('Адрес клиента')
//                                            ->setValues(
//                                                (new TextCustomFieldValueCollection())
//                                                    ->add(
//                                                        (new TextCustomFieldValueModel())
//                                                            ->setValue($amoData['address'])
//                                                    )
//                                            )
//                                    )
                            )
                    )
            );

                if (!empty($amoData['phone'])) {
                    $lead->setContacts(
                        (new ContactsCollection())
                            ->add(
                                (new ContactModel())
                                    ->setFirstName($amoData['name'])
                                    ->setCustomFieldsValues(
                                        (new CustomFieldsValuesCollection())
                                            ->add(
                                                (new MultitextCustomFieldValuesModel())
                                                    ->setFieldCode('PHONE')
                                                    ->setValues(
                                                        (new MultitextCustomFieldValueCollection())
                                                            ->add(
                                                                (new MultitextCustomFieldValueModel())
                                                                    ->setValue($amoData['phone'])
                                                            )
                                                    )
                                            )
                                            ->add(
                                                (new MultitextCustomFieldValuesModel())
                                                    ->setFieldCode('EMAIL')
                                                    ->setValues(
                                                        (new MultitextCustomFieldValueCollection())
                                                            ->add(
                                                                (new MultitextCustomFieldValueModel())
                                                                    ->setValue($amoData['email'])
                                                            )
                                                    )
                                            )
                                            ->add( // кастомное поле для языка
                                                (new SelectCustomFieldValuesModel())
                                                    ->setFieldId(490441)
                                                    ->setValues(
                                                        (new SelectCustomFieldValueCollection())
                                                            ->add(
                                                                (new SelectCustomFieldValueModel())
                                                                    ->setValue($amoData['lang'])
                                                            )
                                                    )
                                            )
//                                            ->add(
//                                                (new TextCustomFieldValuesModel())
//                                                    ->setFieldId(226599)
//                                                    ->setFieldName('Адрес клиента')
//                                                    ->setValues(
//                                                        (new TextCustomFieldValueCollection())
//                                                            ->add(
//                                                                (new TextCustomFieldValueModel())
//                                                                    ->setValue($amoData['address'])
//                                                            )
//                                                    )
//                                            )
                                    )
                            )
                    );

                } else {
                    $lead->setContacts(
                        (new ContactsCollection())
                            ->add(
                                (new ContactModel())
                                    ->setFirstName($amoData['name'])
                                    ->setCustomFieldsValues(
                                        (new CustomFieldsValuesCollection())
                                            ->add(
                                                (new MultitextCustomFieldValuesModel())
                                                    ->setFieldCode('EMAIL')
                                                    ->setValues(
                                                        (new MultitextCustomFieldValueCollection())
                                                            ->add(
                                                                (new MultitextCustomFieldValueModel())
                                                                    ->setValue($amoData['email'])
                                                            )
                                                    )
                                            )
                                            ->add(
                                                (new MultitextCustomFieldValuesModel())
                                                    ->setFieldCode('PHONE')
                                                    ->setValues(
                                                        (new MultitextCustomFieldValueCollection())
                                                            ->add(
                                                                (new MultitextCustomFieldValueModel())
                                                                    ->setValue($amoData['phone'])
                                                            )
                                                    )
                                            )
                                            ->add( // кастомное поле для языка
                                                (new SelectCustomFieldValuesModel())
                                                    ->setFieldId(490441)
                                                    ->setValues(
                                                        (new SelectCustomFieldValueCollection())
                                                            ->add(
                                                                (new SelectCustomFieldValueModel())
                                                                    ->setValue($amoData['lang'])
                                                            )
                                                    )
                                            )
//                                            ->add(
//                                                (new TextCustomFieldValuesModel())
//                                                    ->setFieldId(226599)
//                                                    ->setFieldName('Адрес клиента')
//                                                    ->setValues(
//                                                        (new TextCustomFieldValueCollection())
//                                                            ->add(
//                                                                (new TextCustomFieldValueModel())
//                                                                    ->setValue($amoData['address'])
//                                                            )
//                                                    )
//                                            )
                                    )
                            )
                    );
                }



        } else {

            var_dump('test 2');
            var_dump($amoData['clientAmoId']);
            $contactsCollection = new ContactsCollection();
            try {
//                $contact = $apiClient->contacts()->getOne(17689361);
//                dd($contact);
                var_dump('test 3');
                $contact = $apiClient->contacts()->getOne($amoData['clientAmoId']);
                $customFields = $contact->getCustomFieldsValues();
//                $addressField = $customFields->getBy('fieldId', '226599');
//                if (empty($addressField)) {
//
//                    $customFields->add(
//                        (new TextCustomFieldValuesModel())
//                            ->setFieldId(226599)
//                            ->setFieldName('Адрес клиента')
//                            ->setValues(
//                                (new TextCustomFieldValueCollection())
//                                    ->add(
//                                        (new TextCustomFieldValueModel())
//                                            ->setValue($amoData['address'])
//                                    )
//                            )
//                    );
//
////                    dd($customFields);
//                    $apiClient->contacts()->updateOne($contact);
//                }
                $contactsCollection->add($contact);
                $lead->setContacts($contactsCollection);

            } catch (AmoCRMApiException $e) {

                $code = $e->getCode();
                if ($code == 204) {

                    $lead->setContacts(
                        (new ContactsCollection())
                            ->add(
                                (new ContactModel())
                                    ->setFirstName($amoData['name'])
                                    ->setCustomFieldsValues(
                                        (new CustomFieldsValuesCollection())
                                            ->add(
                                                (new MultitextCustomFieldValuesModel())
                                                    ->setFieldCode('EMAIL')
                                                    ->setValues(
                                                        (new MultitextCustomFieldValueCollection())
                                                            ->add(
                                                                (new MultitextCustomFieldValueModel())
                                                                    ->setValue($amoData['email'])
                                                            )
                                                    )
                                            )
                                            ->add( // кастомное поле для языка
                                                (new SelectCustomFieldValuesModel())
                                                    ->setFieldId(490441)
                                                    ->setValues(
                                                        (new SelectCustomFieldValueCollection())
                                                            ->add(
                                                                (new SelectCustomFieldValueModel())
                                                                    ->setValue($amoData['lang'])
                                                            )
                                                    )
                                            )
//                                            ->add(
//                                                (new TextCustomFieldValuesModel())
//                                                    ->setFieldId(226599)
//                                                    ->setFieldName('Адрес клиента')
//                                                    ->setValues(
//                                                        (new TextCustomFieldValueCollection())
//                                                            ->add(
//                                                                (new TextCustomFieldValueModel())
//                                                                    ->setValue($amoData['address'])
//                                                            )
//                                                    )
//                                            )
                                    )
                            )
                    );

                } else {
                    $this->printError($e);
                    die;
                }


            }


        }

        /////////////////////////////////////////////////////////////////////////////////////////

        if (isset($amoData['test'])) {
            var_dump('test lead');
            dd($lead);
        }
        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($lead);


        try {
            $addedLeadsCollection = $apiClient->leads()->addComplex($leadsCollection);
        } catch (AmoCRMMissedTokenException $e) {
            echo 'AmoCRMMissedTokenException <hr>';
            AppErrors::addError('AmoCRMMissedTokenException ' . $amoData['order_id'], $e);
            $this->printError($e);
            die;
        } catch (AmoCRMoAuthApiException $e) {
            echo 'AmoCRMoAuthApiException <hr>';
            AppErrors::addError('AmoCRMoAuthApiException ' . $amoData['order_id'], $e);
            $this->printError($e);
            die;
        } catch (AmoCRMApiException $e) {
            echo 'AmoCRMApiException <hr>';
            AppErrors::addError('AmoCRMApiException  ' . $amoData['order_id'], $e);
            $this->printError($e);
            die;
        }



        /** @var LeadModel $addedLead */
        foreach ($addedLeadsCollection as $addedLead) {
            //Пройдемся по добавленным сделкам и выведем результат
            $leadId = $addedLead->getId();
            $contactId = $addedLead->getContacts()->first()->getId();

            $res = [
                'amo_id'    => $leadId,
                'client_id' => $contactId
            ];
        }


        return $res;

    }

    public function addContactToLead($contact, $lead)
    {
        $apiClient = $this->getApiClient();
        $leadsService = $apiClient->leads();
        ///////////////////////////////////////////////////////////////////////////

        $links = new LinksCollection();
        $links->add($contact);
        try {
            $leadsService->link($lead, $links);
        } catch (AmoCRMApiException $e) {
            $this->printError($e);
            die;
        }

        return $lead;
    }

    public function createNewLead($amoData)
    {
        $apiClient = $this->getApiClient();
        $leadsService = $apiClient->leads();
        ///////////////////////////////////////////////////////////////////////////
        $lead = new LeadModel();
        $lead = $this->setLeadData($lead, $amoData);

        try {
            $leadsService->addOne($lead);
        } catch (AmoCRMApiException $e) {
            $this->printError($e);
            die;
        }

        return $lead;
    }

    public function updateLead($lead, $amoData)
    {;
        $apiClient = $this->getApiClient();
        $leadsService = $apiClient->leads();
        ///////////////////////////////////////////////////////////////////////////
        $lead = $this->setLeadData($lead, $amoData);

        try {
            $leadsService->updateOne($lead);
        } catch (AmoCRMApiException $e) {
//            $this->printError($e);
//            die;
        }

        return $lead;
    }

    //
    public function setLeadData($lead, $amoData)
    {

        // текстовый тип кастомного поля
        $leadCustomFieldsValues = new CustomFieldsValuesCollection();

        if (!empty($amoData['notes'])) {
            $notes = $amoData['notes'];
            if (strlen($notes) < 255) {
                $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 352111, $notes);
            }
        }

        if (!empty($amoData['order_id'])) {
            $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 489653, $amoData['order_id']);
        }

        if (!empty($amoData['api_mode'])) {
            $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 511579, $amoData['api_mode']);
        }

        if (!empty($amoData['room_number'])) {
            $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 511647, $amoData['room_number']);
        }

        if (!empty($amoData['floor'])) {
            $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 514141, $amoData['floor']);
        }

        if (!empty($amoData['refer_URL'])) {
            $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 338459, $amoData['refer_URL']);
        }


        if (!empty($amoData['utmData'])) {

            foreach ($amoData['utmData'] as $key => $item) {

                if ($key == 'utm_referrer') {
                    $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 514567, $item);
                }

                if ($key == 'utm_content') {
                    $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 514569, $item);
                }

                if ($key == 'utm_source') {
                    $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 514571, $item);
                }

                if ($key == 'utm_medium') {
                    $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 514573, $item);
                }

                if ($key == 'utm_campaign') {
                    $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 514575, $item);
                }

            }
        }


        if (!empty($amoData['to_presents'])) {

            if (!empty($amoData['to_presents']['presents_name'])) {
                $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 308397, $amoData['to_presents']['presents_name']);
            }

            if (!empty($amoData['to_presents']['presents_phone'])) {
                $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 308401, $amoData['to_presents']['presents_phone']);
            }
//            $leadCustomFieldsValues = $this->addCheckboxCustomFieldValuesModel($leadCustomFieldsValues, 352113, true);

        }

        if (!empty($amoData['name'])) {
            $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 514563, $amoData['name']);
        }

        if (!empty($amoData['phone'])) {
            $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 514565, $amoData['phone']);
        }

        if (!empty($amoData['text_note'])) {
            $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 512455, $amoData['text_note']);
        }

        if (!empty($amoData['address'])) {
            $leadCustomFieldsValues = $this->addTextCustomFieldValuesModel($leadCustomFieldsValues, 509001, $amoData['address']);
        }

        if (!empty($amoData['lang'])) {
            $leadCustomFieldsValues = $this->addSelectCustomFieldValuesModel($leadCustomFieldsValues, 516743, $amoData['lang']);
        }

        if (!empty($amoData['payment'])) {
            $leadCustomFieldsValues = $this->addSelectCustomFieldValuesModel($leadCustomFieldsValues, 308363, $amoData['payment']);
        }

        if (!empty($amoData['date'])) {
            $leadCustomFieldsValues = $this->addDateTimeCustomFieldValuesModel($leadCustomFieldsValues, 508997, $amoData['date']);
        }

        if (!empty($amoData['time'])) {
            $leadCustomFieldsValues = $this->addMultiselectCustomFieldValuesModel($leadCustomFieldsValues, 462331, $amoData['time']);
        }

        if (!empty($amoData['tags'])) {
            $lead->getTags($amoData['tags']);
        }

        // комнплексное создание сделки
        $lead->setName($amoData['order name'])
            ->setPrice($amoData['order price'])
            ->setCustomFieldsValues($leadCustomFieldsValues) // прикрепление к сделке значений из полей выше
            ->setPipelineId($amoData['pipelineId'])
            ->setStatusId($amoData['statusId'])
            ->setRequestId($amoData['order_id']);

        /////////////////////////////////////////////////////////////////////////////////////////

        return $lead;
    }

    public function addTextCustomFieldValuesModel($leadCustomFieldsValues, $field_id, $value)
    {
        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId($field_id);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($value))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);

        return $leadCustomFieldsValues;
    }

    public function addCheckboxCustomFieldValuesModel($leadCustomFieldsValues, $field_id, $value)
    {
        $checkboxCustomFieldValueModel = new CheckboxCustomFieldValuesModel();
        $checkboxCustomFieldValueModel->setFieldId($field_id);
        $checkboxCustomFieldValueModel->setValues(
            (new CheckboxCustomFieldValueCollection())
                ->add((new CheckboxCustomFieldValueModel())->setValue($value))
        );
        $leadCustomFieldsValues->add($checkboxCustomFieldValueModel);

        return $leadCustomFieldsValues;
    }

    public function addSelectCustomFieldValuesModel($leadCustomFieldsValues, $field_id, $value)
    {
        // тип поля список
        $selectCustomFieldValueModel = new SelectCustomFieldValuesModel();
        $selectCustomFieldValueModel->setFieldId($field_id);
        $selectCustomFieldValueModel->setValues(
            (new SelectCustomFieldValueCollection())
                ->add((new SelectCustomFieldValueModel())->setValue($value))
        );
        $leadCustomFieldsValues->add($selectCustomFieldValueModel);

        return $leadCustomFieldsValues;
    }

    public function addDateTimeCustomFieldValuesModel($leadCustomFieldsValues, $field_id, $value)
    {
        // тип поля дата-время
        $dateCustomFieldValueModel = new DateTimeCustomFieldValuesModel();
        $dateCustomFieldValueModel->setFieldId($field_id);
        $dateCustomFieldValueModel->setValues(
            (new DateCustomFieldValueCollection())
                ->add((new DateCustomFieldValueModel())->setValue($value))
        );
        $leadCustomFieldsValues->add($dateCustomFieldValueModel);

        return $leadCustomFieldsValues;
    }

    public function addMultiselectCustomFieldValuesModel($leadCustomFieldsValues, $field_id, $value)
    {
        // тип поля список с мульти выбором
        $timeCustomFieldValueModel = new MultiselectCustomFieldValuesModel();
        $timeCustomFieldValueModel->setFieldId($field_id);
        $timeCustomFieldValueModel->setValues(
            (new MultiselectCustomFieldValueCollection())
                ->add((new MultiselectCustomFieldValueModel())->setValue($value))
        );
        $leadCustomFieldsValues->add($timeCustomFieldValueModel);

        return $leadCustomFieldsValues;
    }

    public function getTagsCollection($tags)
    {
        //Создадим тег
        $tagsCollection = new TagsCollection();
        foreach ($tags as $item) {
            if ($item) {
                $tag = new TagModel();
                $tag->setName($item);
                $tagsCollection->add($tag);

            }

        }

        return $tagsCollection;
    }

    public function newLeadBuContactForm($amoData)
    {
        $pipelineId = '4651807'; // воронка
        $statusId = '43924885'; // статус
        $apiClient = $this->getApiClient();
        ///////////////////////////////////////////////////////////////////////////


        $leadCustomFieldsValues = new CustomFieldsValuesCollection();

        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(308397);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($amoData['clientName']))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);


        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(308401);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($amoData['phone']))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);


        $lead = new LeadModel();
        // комнплексное создание сделки
        $lead->setName('contact form')
            ->setCustomFieldsValues($leadCustomFieldsValues) // прикрепление к сделке значений из полей выше
//            ->setStatusId($statusId)
            ->setPipelineId($pipelineId);

        $lead->setContacts(
            (new ContactsCollection())
                ->add(
                    (new ContactModel())
                        ->setFirstName($amoData['clientName'])
                        ->setCustomFieldsValues(
                            (new CustomFieldsValuesCollection())
                                ->add(
                                    (new MultitextCustomFieldValuesModel())
                                        ->setFieldCode('PHONE')
                                        ->setValues(
                                            (new MultitextCustomFieldValueCollection())
                                                ->add(
                                                    (new MultitextCustomFieldValueModel())
                                                        ->setValue($amoData['phone'])
                                                )
                                        )
                                )
                        )
                )
        );

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($lead);


        try {
            $addedLeadsCollection = $apiClient->leads()->addComplex($leadsCollection);
        } catch (AmoCRMMissedTokenException $e) {
            echo 'AmoCRMMissedTokenException <hr>';
            $this->printError($e);
            die;
        } catch (AmoCRMoAuthApiException $e) {
            echo 'AmoCRMoAuthApiException <hr>';
            $this->printError($e);
            die;
        } catch (AmoCRMApiException $e) {
            echo 'AmoCRMApiException <hr>';
            $this->printError($e);
            die;
        }



        /** @var LeadModel $addedLead */
        foreach ($addedLeadsCollection as $addedLead) {
            //Пройдемся по добавленным сделкам и выведем результат
            $leadId = $addedLead->getId();
            $contactId = $addedLead->getContacts()->first()->getId();

            $res = [
                'amo_id'    => $leadId,
                'client_id' => $contactId
            ];
        }


        return $res;

    }


    //
    public function searchContactFilter($filter_value)
    {

        $contacts = $this->getContactDoubles($filter_value);

        if ($contacts) {
            $amo_clones_rev = array_reverse($contacts);
            $amoId = $amo_clones_rev[0]['id'];

            $contact = $this->getContactBuId($amoId);
        } else {
            return false;
        }

        return $contact;
    }

    public function getContactDoubles($filter_value)
    {
        // инициализация апи клиента
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        $filter = new ContactsFilter();
        $filter->setQuery($filter_value);

        try {
            $contacts = $apiClient->contacts()->get($filter);
        } catch (AmoCRMApiException $e) {
            return false;
        }

        $x = 0;
        foreach ($contacts as $contact) {

            $data[$x]['id'] = $contact->id;
            $data[$x]['name'] = $contact->name;
            $data[$x]['firstName'] = $contact->firstName;
            $data[$x]['lastName'] = $contact->name;
            $customFields = $contact->getCustomFieldsValues();
            foreach ($customFields as $field)
            {
                $field_data = $field->values;
                foreach ($field_data as $k => $v) {
                    $data[$x]['fields'][$field->fieldName][$k] = $v->value;
                }
            }

            $x++;
        }

        return $data;
    }

    public function getCustomers()
    {
        // инициализация апи клиента
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////
        ///
        $customersService = $apiClient->customers();
    }


    public function getCatalogElementBuName(string $name, $catalog_name)
    {
        // инициализация апи клиента
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        $catalogsCollection = $apiClient->catalogs()->get();
        //Получим каталог по названию
        $catalog = $catalogsCollection->getBy('name', $catalog_name);

        try {

            //Получим элементы из нужного нам катагола по названию
            $catalogElementsService = $apiClient->catalogElements($catalog->getId());
            $catalogElementsCollection = $catalogElementsService->get();
            $catalogElement = $catalogElementsCollection->getBy('name', $name);
        } catch (AmoCRMApiException $e) {

            return false;
        }

        return $catalogElement;
    }

    public function getCatalogElementBuSku(string $sku, $catalog_name)
    {
        // инициализация апи клиента
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        $catalogsCollection = $apiClient->catalogs()->get();
        //Получим каталог по названию
        $catalog = $catalogsCollection->getBy('name', $catalog_name);
        $catalogElementsService = $apiClient->catalogElements($catalog->getId());
        $catalogElementsFilter = new CatalogElementsFilter();
        $catalogElementsFilter->setQuery($sku);
        try {
            $catalogElement = $catalogElementsService->get($catalogElementsFilter)->first();
        } catch (AmoCRMApiException $e) {
            return false;
        }

        return $catalogElement;
    }

    public function setCatalogElement(array $data, $catalog_name)
    {
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        $catalogsCollection = $apiClient->catalogs()->get();
        //Получим каталог по названию
        $catalog = $catalogsCollection->getBy('name', $catalog_name);

        //Добавим элемент в каталог (Список)
        $catalogElementsCollection = new CatalogElementsCollection();
        $catalogElement = new CatalogElementModel();
        $catalogElement->setName($data['name'])
            ->setCustomFieldsValues(
                (new CustomFieldsValuesCollection())
                    ->add(
                        (new TextCustomFieldValuesModel())
                            ->setFieldCode('SKU')
                            ->setValues(
                                (new TextCustomFieldValueCollection())
                                    ->add(
                                        (new TextCustomFieldValueModel())
                                            ->setValue($data['sku'])
                                    )
                            )
                    )
                    ->add(
                        (new PriceCustomFieldValuesModel())
                            ->setFieldCode('PRICE')
                            ->setValues(
                                (new PriceCustomFieldValueCollection())
                                    ->add(
                                        (new PriceCustomFieldValueModel())
                                            ->setValue($data['price'])
                                    )
                            )
                    )
//                    ->add(
//                        (new TextCustomFieldValuesModel())
//                            ->setFieldId(488441)
//                            ->setValues(
//                                (new TextCustomFieldValueCollection())
//                                    ->add(
//                                        (new TextCustomFieldValueModel())
//                                            ->setValue($data['description'])
//                                    )
//                            )
//                    )
            );

        $catalogElementsCollection->add($catalogElement);
        $catalogElementsService = $apiClient->catalogElements($catalog->getId());
        $catalogElementsService->add($catalogElementsCollection);

        return $catalogElement;
    }

    public function updateCatalogElement($catalogElement, array $data, $catalog_name)
    {
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        $catalogsCollection = $apiClient->catalogs()->get();
        //Получим каталог по названию
        $catalog = $catalogsCollection->getBy('name', $catalog_name);

        $catalogElement->setName($data['name'])
            ->setCustomFieldsValues(
                (new CustomFieldsValuesCollection())
                    ->add(
                        (new PriceCustomFieldValuesModel())
                            ->setFieldCode('PRICE')
                            ->setValues(
                                (new PriceCustomFieldValueCollection())
                                    ->add(
                                        (new PriceCustomFieldValueModel())
                                            ->setValue($data['price'])
                                    )
                            )
                    )
            );


        $catalogElementsService = $apiClient->catalogElements($catalog->getId());
        $catalogElementsService->updateOne($catalogElement);

        return $catalogElement;
    }



    public function setCatalogElementByOrderId(CatalogElementModel $element, $orderId, $quantity)
    {
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        $element->setQuantity($quantity);
        $lead = $apiClient->leads()->getOne($orderId);

        $links = new LinksCollection();
        $links->add($element);

        try {
            $apiClient->leads()->link($lead, $links);
            return true;
        } catch (AmoCRMApiException $e) {
            return false;
        }

    }

    public function addProductsToLead(array $amoProductsList, $amoId)
    {

        //добавление товаров в сделку
        foreach ($amoProductsList as $item) {
            $name = $item['name'];
            // проверить если такой товар есть
            $catalogElement = $this->getCatalogElementBuName($name);

            if (empty($catalogElement)) { //create catalog element
                $this->setCatalogElement($item);
                $catalogElement = $this->getCatalogElementBuName($name);
            }

            if (!empty($catalogElement)) {
                $res = $this->setCatalogElementByOrderId($catalogElement, $amoId, $item['quantity']);

                if (!$res) {
                    AppErrors::addError('error add product to amo lead - ' . $amoId, json_decode($item));
                }
            }
        }
    }


    // добавление текстового примечания в сделку
    public function addTextNotesToLead($id, $notes)
    {
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        $notesCollection = new NotesCollection();
        $commonNote = new CommonNote();
        $commonNote->setEntityId($id)
            ->setCreatedBy(0)
            ->setText($notes);
        $notesCollection->add($commonNote);

        try {
            $leadNotesService = $apiClient->notes(EntityTypesInterface::LEADS);
            $notesCollection = $leadNotesService->add($notesCollection);
        } catch (AmoCRMApiException $e) {
            printError($e);
            die;
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////

    // получение заказа с амо по его ид, (для отладки)
    public function getOrderById($id)
    {
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        try {
            $lead = $apiClient->leads()->getOne($id, [LeadModel::CONTACTS, LeadModel::CATALOG_ELEMENTS]);
        } catch (AmoCRMMissedTokenException $e) {
            echo '<hr>';
            $this->printError($e);
            die;
        } catch (AmoCRMoAuthApiException $e) {
            echo '<hr>';
            $this->printError($e);
            die;
        } catch (AmoCRMApiException $e) {
            $this->printError($e);
            die;
        }

        return $lead;
    }

    public function getAccount()
    {
        $apiClient = $this->getApiClient();

        try {
            $ownerDetails = $apiClient->account()->getCurrent(AccountModel::getAvailableWith());
        } catch (AmoCRMMissedTokenException $e) {
            echo 'AmoCRMMissedTokenException <hr>';
            $this->printError($e);
        } catch (AmoCRMoAuthApiException $e) {
            echo 'AmoCRMoAuthApiException <hr>';
            $this->printError($e);
            return false;
        } catch (AmoCRMApiException $e) {
            echo 'AmoCRMApiException <hr>';
            $this->printError($e);
        }

        return $ownerDetails;
    }

    public function getConacts()
    {
        $apiClient = $this->getApiClient();
        $contacts = $apiClient->contacts();

        return $contacts;
    }

    public function getContactBuId($contact_id)
    {
        $apiClient = $this->getApiClient();
        try {
            $contact = $apiClient->contacts()->getOne($contact_id);
        } catch (AmoCRMApiException $e) {
            $contact = false;
        }


        return $contact;
    }

    public function createContact($contactData)
    {
        $apiClient = $this->getApiClient();
        //Создадим контакт
        $contact = new ContactModel();


        $contact->setFirstName($contactData['name'])
            ->setCustomFieldsValues((new CustomFieldsValuesCollection()));

        try {
            $contactModel = $apiClient->contacts()->addOne($contact);
            $contactModel = $this->syncContactData($contactModel, $contactData);
        } catch (AmoCRMApiException $e) {
            $this->printError($e);
            die;
        }

        return $contactModel;

    }

    public function searchOpenLeadByContactId($contact_id)
    {
        $apiClient = $this->getApiClient();
        $open_lead = false;

        $contact = $apiClient->contacts()->getOne($contact_id, ['leads']); //Получаете контакт, при этом указав параметр with
        $rawLeads = $contact->getLeads(); //Тут у нас будут коллекция сделок либо null. Но при этом следки будут без информации, только id

        if($rawLeads) { //проверим, что сделки все таки есть у контакта
            $leadIds = $rawLeads->pluck('id'); //Получим массив id сделок из коллекции
            $filter = (new LeadsFilter())->setIds($leadIds)->setLimit(250); //Создадим фильтр
            $leads = $apiClient->leads()->get($filter); //Получим уже полноценные сделки со всем содержимым

            foreach ($leads as $lead) {
                if (!$lead->closedAt && !$open_lead && isset($this->open_stages[$lead->statusId])) {
                    $open_lead = $lead;
                }
            }
        }

        return $open_lead;
    }

    public function syncContactData($contact, $clientData)
    {
        $apiClient = $this->getApiClient();
        $contact_update = false;

        $customFields = $contact->getCustomFieldsValues();

        if (isset($clientData['email'])) {
            //эмейл
            $emailField = $customFields->getBy('fieldCode', 'EMAIL');
            //Если значения нет, то создадим новый объект поля и добавим его в коллекцию значений
            if (empty($emailField)) {
                $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
                $customFields->add($emailField);

                //Установим значение поля
                $emailField->setValues(
                    (new MultitextCustomFieldValueCollection())
                        ->add(
                            (new MultitextCustomFieldValueModel())
                                ->setEnum('WORK')
                                ->setValue($clientData['email'])
                        )
                );
                $contact_update = true;
            }
        }

        if (isset($clientData['phone'])) {
            //телефон
            $phoneField = $customFields->getBy('fieldCode', 'PHONE');
            //Если значения нет, то создадим новый объект поля и добавим его в коллекцию значений
            if (empty($phoneField)) {
                $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
                $customFields->add($phoneField);

                //Установим значение поля
                $phoneField->setValues(
                    (new MultitextCustomFieldValueCollection())
                        ->add(
                            (new MultitextCustomFieldValueModel())
                                ->setEnum('WORK')
                                ->setValue($clientData['phone'])
                        )
                );
                $contact_update = true;
            }
        }

        if (isset($clientData['lang'])) {
            //язык
            $langField = $customFields->getBy('fieldId', '490441');
            //Если значения нет, то создадим новый объект поля и добавим его в коллекцию значений
            if (empty($langField)) {
                $langField = (new SelectCustomFieldValuesModel())->setFieldId('490441');
                $customFields->add($langField);

                //Установим значение поля
                $langField->setValues(
                    (new SelectCustomFieldValueCollection())
                        ->add(
                            (new SelectCustomFieldValueModel())
                                ->setValue($clientData['lang'])
                        )
                );
                $contact_update = true;
            }
        }

        if (isset($clientData['birthday'])) {
            //birthday
            $birthdayField = $customFields->getBy('fieldId', '230445');
            //Если значения нет, то создадим новый объект поля и добавим его в коллекцию значений
            if (empty($birthdayField)) {
                $birthdayField = (new BirthdayCustomFieldValuesModel())->setFieldId('230445');
                $customFields->add($birthdayField);

                //Установим значение поля
                $birthdayField->setValues(
                    (new BirthdayCustomFieldValueCollection())
                        ->add(
                            (new BaseCustomFieldValueModel())
                                ->setValue($clientData['birthday'])
                        )
                );
                $contact_update = true;
            }
        }

        if ($contact_update) {
            try {
                $apiClient->contacts()->updateOne($contact);
            } catch (AmoCRMApiException $e) {
                $this->printError($e);
                die;
            }
        }

        return $contact;
    }

    public function createLeadBirthday($clientData)
    {
//        $clientData['email'] = 'test_1@mail.ru';
        $email = $clientData['email'];

        $contact = $this->searchContactFilter($email);

        if ($contact) {
            $contact = $this->syncContactData($contact, $clientData);
        } else {
            $clientData['name'] = 'new client';
            $contact = $this->createContact($clientData);
        }

        dd('test', $contact->toArray());

    }


    public function getLeads()
    {
        $apiClient = $this->getApiClient();
        $leadsService = $apiClient->leads();

        //Получим сделки и следующую страницу сделок
        try {
            $leadsCollection = $leadsService->get();
        //$leadsCollection = $leadsService->nextPage($leadsCollection);
        } catch (AmoCRMApiException $e) {
            return false;
        }
        return $leadsCollection;
    }

    public function addSopProductsToLead($amoId, $amoProducts)
    {
        foreach ($amoProducts as $item) {
            $this->setCatalogElementByOrderId($item['amo_model'], $amoId, (int) $item['count']);
        }
    }


    public function addInvoiceToLead($contact_id, $order_id, $lead_id, $order_price, $payment_status)
    {

        $apiClient = $this->getApiClient();

        $catalogsFilter = new CatalogsFilter();
        $catalogsFilter->setType(EntityTypesInterface::INVOICES_CATALOG_TYPE_STRING);
        $invoicesCatalog = $apiClient->catalogs()->get($catalogsFilter)->first();

        //Создадим новый счет
        //Обязательно должно быть название и заполнено поле статус
        $newInvoice = new CatalogElementModel();
        //Зададим Имя
        $newInvoice->setName('Заказ #' . $order_id);
        //Зададим дату создания
        $creationDate = new DateTime();
        $newInvoice->setCreatedAt($creationDate->getTimestamp());

        $invoiceCustomFieldsValues = new CustomFieldsValuesCollection();
        //Зададим статус
        if ($payment_status == 4) {
            $status = 'Оплачен';
        } else {
            $status = 'Создан';
        }
        $statusCustomFieldValueModel = new SelectCustomFieldValuesModel();
        $statusCustomFieldValueModel->setFieldCode(InvoicesCustomFieldsEnums::STATUS);
        $statusCustomFieldValueModel->setValues(
            (new SelectCustomFieldValueCollection())
                ->add((new SelectCustomFieldValueModel())->setValue($status)) //Текст должен совпадать с одним из значений поля статус
        );
        $invoiceCustomFieldsValues->add($statusCustomFieldValueModel);

        //Зададим комментарий
        $commentCustomFieldValueModel = new TextCustomFieldValuesModel();
        $commentCustomFieldValueModel->setFieldCode(InvoicesCustomFieldsEnums::COMMENT);
        $commentCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue('Оплата заказа'))
        );
        $invoiceCustomFieldsValues->add($commentCustomFieldValueModel);
        //Зададим плательщика (до поле связанная сущность, может хранить в себе связь с сущностью (контакт или компания))
        $payerCustomFieldValueModel = new LinkedEntityCustomFieldValuesModel();
        $payerCustomFieldValueModel->setFieldCode(InvoicesCustomFieldsEnums::PAYER);
        $payerCustomFieldValueModel->setValues(
            (new LinkedEntityCustomFieldValueCollection())
                ->add(
                    (new LinkedEntityCustomFieldValueModel())
//                        ->setName($contact_id) //Можно передать или название сущности, или ID сущности, чтобы заполнить это поле
                        ->setEntityId($contact_id)
                        ->setEntityType(EntityTypesInterface::CONTACTS)
                )
        );

        //Зададим товары в счете
        $itemsCustomFieldValueModel = new ItemsCustomFieldValuesModel();
        $itemsCustomFieldValueModel->setFieldCode(InvoicesCustomFieldsEnums::ITEMS);
        $itemsCustomFieldValueModel->setValues(
            (new ItemsCustomFieldValueCollection())
                ->add(
                    (new ItemsCustomFieldValueModel())
                        ->setDescription('Оплата заказа')
                        ->setExternalUid($order_id)
                        //->setProductId('ID товара в списке товаров в amoCRM') //Необзятальное поле
                        ->setQuantity(1) //количество
//                        ->setSku('Артикул товара')
                        ->setUnitPrice($order_price) //цена за единицу товара
                        ->setUnitType('шт') //единица измерения товвара
//                        ->setVatRateValue(20) //НДС 20%
//                        ->setDiscount([
//                            'type' => ItemsCustomFieldValueModel::FIELD_DISCOUNT_TYPE_AMOUNT, //amount - скидка абсолютная, percentage - скидка в процентах от стоимости товара
//                            'value' => 15.15 //15 рублей 15 копеек
//                        ])
//                        ->setBonusPointsPerPurchase(20) //Сколько бонусных баллов будет начислено за покупку
                )
        );
        $invoiceCustomFieldsValues->add($itemsCustomFieldValueModel);


        //Зададим значение поля Итоговая сумма к оплате
        //Отображается в списке счетов,
        //при заходе в карточку счета, стоимость счета будет рассчитана с учетом товаров, ндс и отображена в карточке счета
        //Если передать некорректную сумму, то до редактирования в интерфейсе, через API будет возвращаться некорректная сумма
        $priceCustomFieldValueModel = new NumericCustomFieldValuesModel();
        $priceCustomFieldValueModel->setFieldCode(InvoicesCustomFieldsEnums::PRICE);
        $priceCustomFieldValueModel->setValues(
            (new NumericCustomFieldValueCollection())
                ->add(
                    (new NumericCustomFieldValueModel())
                        ->setValue($order_price)
                )
        );
        $invoiceCustomFieldsValues->add($priceCustomFieldValueModel);

        //Установим значения в модель и сохраним
        $newInvoice->setCustomFieldsValues($invoiceCustomFieldsValues);
        $catalogElementsService = $apiClient->catalogElements($invoicesCatalog->getId());
        try {
            $newInvoice = $catalogElementsService->addOne($newInvoice);
        } catch (AmoCRMApiException $e) {
            $this->printError($e);
            die;
        }
        //Свяжем счет со сделкой
        $leadsService = $apiClient->leads();
        $lead = (new LeadModel())
            ->setId($lead_id);
        try {
            $leadsService->link($lead, (new LinksCollection())->add($newInvoice));
        } catch (AmoCRMApiException $e) {
            $this->printError($e);
            die;
        }

        return $newInvoice->getId();
    }





    public function printError(AmoCRMApiException $e): void
    {
        $errorTitle = $e->getTitle();
        $code = $e->getCode();
        $debugInfo = var_export($e->getLastRequestInfo(), true);

        $error = <<<EOF
Error: $errorTitle
Code: $code
Debug: $debugInfo
EOF;

        echo '<pre>' . $error . '</pre>';
    }

    public static function testTimeToken(AccessTokenInterface $accessToken)
    {
        $timeToken = $accessToken->getExpires();
        $time = time();

    }


    public function getPipelines()
    {
        $apiClient = $this->getApiClient();
        $pipelinesService = $apiClient->pipelines();
        $pipelinesCollection = $pipelinesService->get();

        return $pipelinesCollection;
    }

}
