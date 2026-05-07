<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpatialDataRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama'                 => ['required', 'string', 'max:255'],
            'kategori'             => ['required', 'string', 'max:100'],
            'deskripsi'            => ['nullable', 'string', 'max:5000'],
            'properties'           => ['nullable', 'array'],
            'geometry'             => ['required', 'array'],
            'geometry.type'        => ['required', 'string', 'in:Point,LineString,Polygon,MultiPoint,MultiLineString,MultiPolygon'],
            'geometry.coordinates' => ['required', 'array'],
        ];
    }
}