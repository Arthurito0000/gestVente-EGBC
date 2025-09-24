<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            // 'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'client_location' => 'nullable|string|max:255',
            'vendor_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',

            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.quantity' => 'required|numeric|min:1',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.total_price' => 'required|numeric|min:0',
        ];
    }

     public function messages(): array
    {
        return [
            // 'invoice_number.required' => 'Le numéro de facture est obligatoire.',
            // 'invoice_number.unique' => 'Ce numéro de facture existe déjà.',
            'invoice_date.required' => 'La date de la facture est obligatoire.',
            'client_name.required' => 'Le nom du client est obligatoire.',
            'lines.required' => 'Vous devez ajouter au moins une ligne de produit.',
            'lines.*.product_id.required' => 'Sélectionnez un produit.',
            'lines.*.product_id.exists' => 'Le produit sélectionné est invalide.',
            'lines.*.quantity.min' => 'La quantité doit être d\'au moins 1.',
        ];
    }
}
