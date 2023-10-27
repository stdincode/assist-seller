<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;

class SchemaController extends Controller
{
    /**
     * Получить схему OpenAPI в формате JSON или YAML
     *
     * @param string $type
     * @return string
     */
    public function schema(string $type): string
    {
        return file_get_contents(resource_path('schema.client.' . $type));
    }
}
