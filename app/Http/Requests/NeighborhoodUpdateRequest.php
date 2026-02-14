<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NeighborhoodUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'boundary_coordinates' => ['sometimes', 'required', 'array', 'min:3'],
            'boundary_coordinates.*' => ['required_with:boundary_coordinates', 'array', 'size:2'],
            'boundary_coordinates.*.0' => ['required_with:boundary_coordinates', 'numeric', 'between:-90,90'],
            'boundary_coordinates.*.1' => ['required_with:boundary_coordinates', 'numeric', 'between:-180,180'],
            'region' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:draft,published,archived'],
        ];
    }
}

