<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Rule;



class StoreUserRequest extends FormRequest
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
            'UserId' => 'required|string|max:191',
            // 'email' => 'required|email|max:191',
            'email' => 'required|email |unique:member',
            'password' => 'required|string|max:191',
            'sname' => 'required|string|max:191',
            'fname' => 'required|string|max:191',
            'mname' => 'required|string|max:191',
            'Gender' => 'required|string|max:191',
            'dob' => 'required|string|max:191',
            'mobile' => 'required|string|max:191',
            'Altmobile' => 'required|string|max:191',
            'Residence' => 'required|string|max:191',
            'Country' => 'required|string|max:191',
            'State' => 'required|string|max:191',
            'City' => 'required|string|max:191',
            'Title' => 'required|string|max:191',
            'dot' => 'required|string|max:191',
            'MStatus' => 'required|string|max:191',
            'ministry' => 'required|string|max:191',
            'Status' => 'required|string|max:191',
            // 'thumbnail' => 'required|string |max:191',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }
}
