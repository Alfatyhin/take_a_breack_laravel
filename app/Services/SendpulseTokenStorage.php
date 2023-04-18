<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;

class SendpulseTokenStorage
{
    public function __construct($storageFolder = '')
    {
        $this->storageFolder = $storageFolder;
    }

    /**
     * @param $key string
     * @param $token
     *
     * @return void
     */
    public function set($key, $token)
    {
        $filePath = $this->storageFolder . $key;
        Storage::put($filePath, $token);
    }

    /**
     * @param $key string
     *
     * @return mixed
     */
    public function get($key)
    {
        $filePath = $this->storageFolder . $key;
        if (Storage::exists($filePath)) {
            return Storage::get($filePath);
        }

        return null;
    }

    /**
     * @param  $key string
     *
     * @return bool
     */
    public function delete($key)
    {
        $filePath = $this->storageFolder . $key;
        Storage::delete($filePath);
        return false;
    }
}