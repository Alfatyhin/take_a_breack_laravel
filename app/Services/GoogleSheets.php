<?php


namespace App\Services;


use Google\Service\Sheets;

class GoogleSheets extends GClientService
{

    private $service;

    public function __construct()
    {
        parent::__construct();

        $client = $this->getClient();
        $client->addScope(Sheets::DRIVE);
        $client->setSubject('laravelshop@laravelshop-390704.iam.gserviceaccount.com');

        $service = new Sheets($client);
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service): void
    {
        $this->service = $service;
    }


}
