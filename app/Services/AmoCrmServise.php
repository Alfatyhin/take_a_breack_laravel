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
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\AccountModel;
use AmoCRM\Models\CatalogElementModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\DateTimeCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultiselectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\PriceCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\SelectCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\StreetAddressCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\DateCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultiselectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\PriceCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\SelectCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\StreetAddressCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\DateCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultiselectCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\PriceCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\SelectCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\StreetAdressCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteType\CommonNote;
use AmoCRM\Models\NoteType\ServiceMessageNote;
use AmoCRM\Models\TagModel;
use App\Models\AppErrors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AmoCrmServise
{
    private $tokenFile = 'data/amo-assets.json';
    private static $tokensFile = 'data/amo-assets.json';
    private $apiClient;

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
                'title'          => '???????????????????? ????????????????????',
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

        /** ?????????????? ???????????? ?????? ?????????????? */
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

        $subdomain = $data['referer']; //???????????????? ?????????????? ????????????????
        $link = 'https://' . $subdomain . '/oauth2/access_token'; //?????????????????? URL ?????? ??????????????
        $curl = curl_init(); //?????????????????? ???????????????????? ???????????? cURL
        /** ?????????????????????????? ?????????????????????? ?????????? ?????? ???????????? cURL  */
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl); //???????????????????? ???????????? ?? API ?? ?????????????????? ?????????? ?? ????????????????????
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        /** ???????????? ???? ?????????? ???????????????????? ??????????, ???????????????????? ???? ??????????????. ?????? ????????????. ???? ???????????? ???????????????????? ???????????? ?????????? ????????????????. */
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

        /** ???????? ?????? ???????????? ???? ???????????????? - ???????????????????? ?????????????????? ???? ????????????  */
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

    // ???????????????? ???????????? ???? ?????? ?????? ??????????
    public function NewOrder(array $amoData)
    {
        $apiClient = $this->getApiClient();
        ///////////////////////////////////////////////////////////////////////////

        // ?????????????????? ?????? ???????????????????? ????????
        $leadCustomFieldsValues = new CustomFieldsValuesCollection();

        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(352111);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($amoData['notes']))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);


        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(489653);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($amoData['ekwidId']))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);


        $textCustomFieldValueModel = new TextCustomFieldValuesModel();
        $textCustomFieldValueModel->setFieldId(338459);
        $textCustomFieldValueModel->setValues(
            (new TextCustomFieldValueCollection())
                ->add((new TextCustomFieldValueModel())->setValue($amoData['refer_URL']))
        );
        $leadCustomFieldsValues->add($textCustomFieldValueModel);


        if (!empty($amoData['to_presents'])) {
            $name = $amoData['to_presents']['presents_name'];
            $phone =  $amoData['to_presents']['presents_phone'];

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


        if (!empty($phone)) {
            $textCustomFieldValueModel = new TextCustomFieldValuesModel();
            $textCustomFieldValueModel->setFieldId(308401);
            $textCustomFieldValueModel->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($phone))
            );
            $leadCustomFieldsValues->add($textCustomFieldValueModel);
        }


        // ?????? ???????? ????????????
        $selectCustomFieldValueModel = new SelectCustomFieldValuesModel();
        $selectCustomFieldValueModel->setFieldId(308363);
        $selectCustomFieldValueModel->setValues(
            (new SelectCustomFieldValueCollection())
                ->add((new SelectCustomFieldValueModel())->setValue($amoData['payment']))
        );
        $leadCustomFieldsValues->add($selectCustomFieldValueModel);


        // ?????? ???????? ????????-??????????
        $dateCustomFieldValueModel = new DateTimeCustomFieldValuesModel();
        $dateCustomFieldValueModel->setFieldId(462257);
        $dateCustomFieldValueModel->setValues(
            (new DateCustomFieldValueCollection())
                ->add((new DateCustomFieldValueModel())->setValue($amoData['date']))
        );
        $leadCustomFieldsValues->add($dateCustomFieldValueModel);


        // ?????? ???????? ???????????? ?? ???????????? ??????????????
        $timeCustomFieldValueModel = new MultiselectCustomFieldValuesModel();
        $timeCustomFieldValueModel->setFieldId(462331);
        $timeCustomFieldValueModel->setValues(
            (new MultiselectCustomFieldValueCollection())
                ->add((new MultiselectCustomFieldValueModel())->setValue($amoData['time']))
        );
        $leadCustomFieldsValues->add($timeCustomFieldValueModel);


        // ?????? ???????? ??????????
        $adressCustomFeeldValueModel = new StreetAddressCustomFieldValuesModel();
        $adressCustomFeeldValueModel->setFieldId(308403);
        $adressCustomFeeldValueModel->setValues(
            (new StreetAddressCustomFieldValueCollection())
                ->add((new StreetAdressCustomFieldValueModel())->setValue($amoData['address']))
        );
        $leadCustomFieldsValues->add($adressCustomFeeldValueModel);

        //// ???????????????????? ???????????????????????? ???????????? ?? ????????
        $lead = new LeadModel();

        if (!empty($amoData['tags'])) {

            //???????????????? ??????
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

        // ?????????????????????? ???????????????? ????????????
        $lead->setName($amoData['order name'])
            ->setPrice($amoData['order price'])
            ->setCustomFieldsValues($leadCustomFieldsValues) // ???????????????????????? ?? ???????????? ???????????????? ???? ?????????? ????????
            ->setStatusId($amoData['statusId'])
            ->setRequestId($amoData['ekwidId']);

        if (empty($amoData['clientAmoId'])) {

            $contactsCollection = new ContactsCollection();
            $contact = new ContactModel();
            $contact->setFirstName($amoData['name'])
                ->setIsMain(true);

                $contact->setCustomFieldsValues(
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
                        ->add( // ?????????????????? ???????? ?????? ??????????
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
                );

                if (!empty($amoData['phone'])) {
                    $customFields = $contact->getCustomFieldsValues();
                    $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
                    $customFields->add($phoneField);
                    //?????????????????? ???????????????? ????????
                    $phoneField->setValues(
                        (new MultitextCustomFieldValueCollection())
                            ->add(
                                (new MultitextCustomFieldValueModel())
                                    ->setValue($amoData['phone'])
                            )
                    );
                    $contact->setCustomFieldsValues($customFields);
                    $contactsCollection->add($contact);
                }

            // ?????????????????? ?? ???????????? ?????????????? ?? ??????????????
            $lead->setContacts($contactsCollection);

        } else {

            $contactsCollection = new ContactsCollection();
            $contact = $apiClient->contacts()->getOne($amoData['clientAmoId']);
            $contactsCollection->add($contact);
            $lead->setContacts($contactsCollection);
        }

        /////////////////////////////////////////////////////////////////////////////////////////

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($lead);


        try {
            $addedLeadsCollection = $apiClient->leads()->addComplex($leadsCollection);
        } catch (AmoCRMMissedTokenException $e) {
            echo 'AmoCRMMissedTokenException <hr>';
            AppErrors::addError('AmoCRMMissedTokenException', $e);
            $this->printError($e);
            die;
        } catch (AmoCRMoAuthApiException $e) {
            echo 'AmoCRMoAuthApiException <hr>';
            AppErrors::addError('AmoCRMoAuthApiException', $e);
            $this->printError($e);
            die;
        } catch (AmoCRMApiException $e) {
            echo 'AmoCRMApiException <hr>';
            AppErrors::addError('AmoCRMApiException', $e);
            $this->printError($e);
            die;
        }



        /** @var LeadModel $addedLead */
        foreach ($addedLeadsCollection as $addedLead) {
            //?????????????????? ???? ?????????????????????? ?????????????? ?? ?????????????? ??????????????????
            $leadId = $addedLead->getId();
            $contactId = $addedLead->getContacts()->first()->getId();

            $res = [
                'amo_id'    => $leadId,
                'client_id' => $contactId
            ];
        }


        return $res;

    }

    public function getCatalogElementBuName(string $name)
    {
        // ?????????????????????????? ?????? ??????????????
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        $catalogsCollection = $apiClient->catalogs()->get();
        //?????????????? ?????????????? ???? ????????????????
        $catalog = $catalogsCollection->getBy('name', 'Ecwid ??????????????');

        //?????????????? ???????????????? ???? ?????????????? ?????? ????????????????, ?????? ?? ???????????????? ?????? ?????????? ???????? ?????????? ????????????????
        $catalogElementsService = $apiClient->catalogElements($catalog->getId());

        $catalogElementsCollection = $catalogElementsService->get();
        $catalogElement = $catalogElementsCollection->getBy('name', $name);

        if ($catalogElement) {
            return $catalogElement;
        } else {
            return false;
        }

    }

    public function setCatalogElement(array $data)
    {
        $apiClient = $this->getApiClient();
        /////////////////////////////////////////////////////////////////////////

        $catalogsCollection = $apiClient->catalogs()->get();
        //?????????????? ?????????????? ???? ????????????????
        $catalog = $catalogsCollection->getBy('name', 'Ecwid ??????????????');

        //?????????????? ?????????????? ?? ?????????????? (????????????)
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
                    ->add(
                        (new TextCustomFieldValuesModel())
                            ->setFieldId(488441)
                            ->setValues(
                                (new TextCustomFieldValueCollection())
                                    ->add(
                                        (new TextCustomFieldValueModel())
                                            ->setValue($data['description'])
                                    )
                            )
                    )
            );

        $catalogElementsCollection->add($catalogElement);

        $catalogElementsService = $apiClient->catalogElements($catalog->getId());

        $catalogElementsService->add($catalogElementsCollection);

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

        //???????????????????? ?????????????? ?? ????????????
        foreach ($amoProductsList as $item) {
            $name = $item['name'];
            // ?????????????????? ???????? ?????????? ?????????? ????????
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


    // ???????????????????? ???????????????????? ???????????????????? ?? ????????????
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

    // ?????????????????? ???????????? ?? ?????? ???? ?????? ????, (?????? ??????????????)
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



}
