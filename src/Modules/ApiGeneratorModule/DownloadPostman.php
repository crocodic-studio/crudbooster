<?php

namespace Crocodicstudio\Crudbooster\Modules\ApiGeneratorModule;

use Crocodicstudio\Crudbooster\Controllers\CBController;

class DownloadPostman extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_apicustom';
        $this->primaryKey = "id";
        $this->titleField = "name";
    }

    public function getDownloadPostman()
    {
        $this->cbLoader();
        $data = [];
        $data['variables'] = [];
        $data['info'] = [
            'name' => cbGetsetting('appname').' - API',
            '_postman_id' => "1765dd11-73d1-2978-ae11-36921dc6263d",
            'description' => '',
            'schema' => 'https://schema.getpostman.com/json/collection/v2.0.0/collection.json',
        ];

        $data['item'] = $this->makeItems();

        $json = json_encode($data);

        return \Response::make($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename='.cbGetsetting('appname').' - API For POSTMAN.json',
        ]);
    }

    /**
     * @return array
     */
    private function makeItems()
    {
        $items = [];
        foreach ($this->table()->orderby('name', 'asc')->get() as $api) {
            list($httpbuilder, $formdata) = $this->processParams($api->parameters);

            $items[] = [
                'name' => $api->name,
                'request' => [
                    'url' => url('api/'.$api->permalink).$this->httpBuilder($api, $httpbuilder),
                    'method' => $api->method_type ?: 'GET',
                    'header' => [],
                    'body' => [
                        'mode' => 'formdata',
                        'formdata' => $formdata,
                    ],
                    'description' => $api->keterangan,
                ],
            ];
        }

        return $items;
    }

    /**
     * @param $api
     * @param $httpbuilder
     * @return string
     */
    private function httpBuilder($api, $httpbuilder)
    {
        if (strtolower($api->method_type) == 'get' && $httpbuilder) {
            return "?".http_build_query($httpbuilder);
        }
        return '';
    }

    /**
     * @param $parameters
     * @return array
     */
    private function processParams($parameters)
    {
        $parameters = unserialize($parameters);
        $formdata = [];
        $httpbuilder = [];
        if (! $parameters) {
            return [$httpbuilder, $formdata];
        }
        foreach ($parameters as $p) {
            $enabled = ($p['used'] == 0) ? false : true;
            $name = $p['name'];
            $httpbuilder[$name] = '';
            if ($enabled) {
                $formdata[] = ['key' => $name, 'value' => '', 'type' => 'text', 'enabled' => $enabled];
            }
        }

        return [$httpbuilder, $formdata];
    }
}
