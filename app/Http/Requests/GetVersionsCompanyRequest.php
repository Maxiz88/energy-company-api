<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetVersionsCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['edrpou'] = $this->route('edrpou');
        return $data;
    }

    public function rules(): array
    {
        return [
            'edrpou' => 'required|string|max:10',
        ];
    }

}
