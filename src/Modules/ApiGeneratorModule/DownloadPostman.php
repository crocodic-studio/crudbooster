<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use crocodicstudio\crudbooster\controllers\CBController;

class DownloadPostman extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_apicustom';
        $this->primaryKey = "id";
        $this->title_field = "nama";
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
        foreach ($this->table()->orderby('nama', 'asc')->get() as $api) {
            $parameters = unserialize($api->parameters);
            $formdata = [];
            $httpbuilder = [];
            if ($parameters) {
                foreach ($parameters as $p) {
                    $enabled = ($p['used'] == 0) ? false : true;
                    $name = $p['name'];
                    $httpbuilder[$name] = '';
                    if ($enabled) {
                        $formdata[] = ['key' => $name, 'value' => '', 'type' => 'text', 'enabled' => $enabled];
                    }
                }
            }

            if (strtolower($api->method_type) == 'get' && $httpbuilder) {
                $httpbuilder = "?".http_build_query($httpbuilder);
            } else {
                $httpbuilder = '';
            }

            $items[] = [
                'name' => $api->nama,
                'request' => [
                    'url' => url('api/'.$api->permalink).$httpbuilder,
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
}
