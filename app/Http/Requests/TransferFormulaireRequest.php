<?php

namespace App\Http\Requests;

use App\Models\Formulaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferFormulaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        $formulaire = $this->route('formulaire');

        return $formulaire !== null && $this->user()->can('transfer', $formulaire);
    }

    public function rules(): array
    {
        $codes = array_keys(config('administration.services', []));

        return [
            'target_service_code' => ['required', 'string', 'max:10', Rule::in($codes)],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Formulaire|null $formulaire */
            $formulaire = $this->route('formulaire');
            $target = (string) $this->input('target_service_code', '');

            if (! $formulaire || $target === '') {
                return;
            }

            if ($target === $formulaire->service_code) {
                $validator->errors()->add(
                    'target_service_code',
                    'Choisissez un service différent du service actuel du dossier.'
                );

                return;
            }

            if (! $formulaire->isAllowedTransferTarget($target)) {
                $validator->errors()->add(
                    'target_service_code',
                    'Ce dossier ne peut pas être transféré vers ce service dans l’état actuel (statut ou règles métier).'
                );
            }
        });
    }
}
