<?php

namespace Kenshoo;

use GuzzleHttp\Client;

class Report
{

    public $id;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function execute($start_date, $end_date)
    {
        $client = new Client();
        $response = $client->request('POST',
            Kenshoo::getEndpoint() . 'reports/' . $this->id . '/runs',
            [
                'query' => [
                    'ks' => Kenshoo::getKenshooId(),
                ],
                'auth' => [
                    Kenshoo::getUsername(),
                    Kenshoo::getPassword(),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'dateRange' => [
                        'from' => $start_date,
                        'to' => $end_date,
                    ]
                ]
            ]
        );

        $location_header = $response->getHeader('Location', true)[0];
        $run_token = preg_replace('/\A.*?(?=rpx)/', '', $location_header);
        $run_token = preg_replace('/\?(.*)/', '', $run_token);

        return $run_token;
    }

    public static function all()
    {
        $client = new Client();
        $response = $client->request('GET',
            Kenshoo::getEndpoint() . 'reports',
            [
                'query' => [
                    'ks' => Kenshoo::getKenshooId(),
                ],
                'auth' => [
                    Kenshoo::getUsername(),
                    Kenshoo::getPassword(),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]
        );

        return json_decode($response->getBody(), true);
    }

    public static function find($id)
    {
        $client = new Client();
        $response = $client->request('GET',
            Kenshoo::getEndpoint() . 'reports/' . $id,
            [
                'query' => [
                    'ks' => Kenshoo::getKenshooId(),
                ],
                'auth' => [
                    Kenshoo::getUsername(),
                    Kenshoo::getPassword(),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]
        );

        $output = json_decode($response->getBody(), true);
        return new Report(
          $output['reportId'],
          $output['reportName']
        );
    }

    public static function getStatus($run_token)
    {
        $client = new Client();
        $response = $client->request('GET',
            Kenshoo::getEndpoint() . 'reports/runs/' . $run_token . '/status',
            [
                'query' => [
                    'ks' => Kenshoo::getKenshooId(),
                ],
                'auth' => [
                    Kenshoo::getUsername(),
                    Kenshoo::getPassword(),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]
        );

        $output = json_decode($response->getBody(), true);
        return $output['status'];
    }

    public static function download($run_token)
    {
        $client = new Client();
        $response = $client->request('GET',
            Kenshoo::getEndpoint() . 'reports/runs/' . $run_token . '/data',
            [
                'query' => [
                    'ks' => Kenshoo::getKenshooId(),
                ],
                'auth' => [
                    Kenshoo::getUsername(),
                    Kenshoo::getPassword(),
                ],
                'headers' => [
                    'Content-Type' => 'application/octet-stream',
                ],
            ]
        );

        $filename = $run_token . '.zip';
        $contents = fopen($filename, 'w');
        fwrite($contents, $response->getBody()->getContents());
        fclose($contents);

        header('Content-Disposition: attachment; filename="' . $filename);
        readfile($filename);
        unlink($filename);
    }
}