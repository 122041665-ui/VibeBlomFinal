<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class FastApiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('FASTAPI_URL', 'http://127.0.0.1:8010'), '/');
    }

    protected function client(?string $token = null): PendingRequest
    {
        $request = Http::acceptJson();

        if ($token) {
            $request = $request->withToken($token);
        }

        return $request;
    }

    public function get(string $path, ?string $token = null, array $query = []): Response
    {
        return $this->client($token)->get($this->baseUrl . $path, $query);
    }

    public function post(string $path, array $data = [], ?string $token = null): Response
    {
        return $this->client($token)->post($this->baseUrl . $path, $data);
    }

    public function put(string $path, array $data = [], ?string $token = null): Response
    {
        return $this->client($token)->put($this->baseUrl . $path, $data);
    }

    public function patch(string $path, array $data = [], ?string $token = null): Response
    {
        return $this->client($token)->patch($this->baseUrl . $path, $data);
    }

    public function delete(string $path, ?string $token = null): Response
    {
        return $this->client($token)->delete($this->baseUrl . $path);
    }

    public function postMultipart(
        string $path,
        array $data = [],
        array|UploadedFile $files = [],
        ?string $token = null,
        string $fileField = 'photos'
    ): Response {
        $request = $this->client($token);

        $files = $files instanceof UploadedFile ? [$files] : $files;

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $request = $request->attach(
                    $fileField,
                    fopen($file->getRealPath(), 'r'),
                    $file->getClientOriginalName()
                );
            }
        }

        return $request->post($this->baseUrl . $path, $data);
    }

    public function putMultipart(
    string $path,
    array $data = [],
    array|UploadedFile $files = [],
    ?string $token = null,
    string $fileField = 'photos'
): Response {
    $request = $this->client($token);

    $files = $files instanceof UploadedFile ? [$files] : $files;

    $multipart = [];

    foreach ($data as $key => $value) {
        $multipart[] = [
            'name' => $key,
            'contents' => is_null($value) ? '' : (string) $value,
        ];
    }

    foreach ($files as $file) {
        if ($file instanceof UploadedFile) {
            $multipart[] = [
                'name' => $fileField,
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }
    }

    return $request->send('PUT', $this->baseUrl . $path, [
        'multipart' => $multipart,
    ]);
}
}