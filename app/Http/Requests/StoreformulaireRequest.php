<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreformulaireRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_code' => ['required','string','max:10'],
            'annee' => ['required','integer','min:2000','max:2100'],
            'numero_ordre' => ['required','integer','min:1'],
            'expediteur' => ['required','string','max:255'],
            'objet' => ['required','string','max:1000'],
            'type_document' => ['required','string','max:255'],
            'autre_type_document' => ['nullable', 'string', 'max:255'], 
            'date_reception' => ['nullable','date'],
            'date_echeance' => ['nullable','date'],
            'fichier' => ['nullable','file','max:5120'],
            'status' => ['nullable','integer','min:0','max:4'],
        ];
    }
}
