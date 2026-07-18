<?php

namespace Hexactyl\\Http\Requests\Admin\Settings;

use Hexactyl\\Http\Requests\Admin\AdminFormRequest;

class AdvancedSettingsFormRequest extends AdminFormRequest
{
    /**
     * Return all the rules to apply to this request's data.
     */
    public function rules(): array
    {
        return [
            'recaptcha:enabled' => 'required|in:true,false',
            'recaptcha:secret_key' => 'required|string|max:191',
            'recaptcha:website_key' => 'required|string|max:191',
            'Hexactyl:guzzle:timeout' => 'required|integer|between:1,60',
            'Hexactyl:guzzle:connect_timeout' => 'required|integer|between:1,60',
            'Hexactyl:client_features:allocations:enabled' => 'required|in:true,false',
            'Hexactyl:client_features:allocations:range_start' => [
                'nullable',
                'required_if:Hexactyl:client_features:allocations:enabled,true',
                'integer',
                'between:1024,65535',
            ],
            'Hexactyl:client_features:allocations:range_end' => [
                'nullable',
                'required_if:Hexactyl:client_features:allocations:enabled,true',
                'integer',
                'between:1024,65535',
                'gt:Hexactyl:client_features:allocations:range_start',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'recaptcha:enabled' => 'reCAPTCHA Enabled',
            'recaptcha:secret_key' => 'reCAPTCHA Secret Key',
            'recaptcha:website_key' => 'reCAPTCHA Website Key',
            'Hexactyl:guzzle:timeout' => 'HTTP Request Timeout',
            'Hexactyl:guzzle:connect_timeout' => 'HTTP Connection Timeout',
            'Hexactyl:client_features:allocations:enabled' => 'Auto Create Allocations Enabled',
            'Hexactyl:client_features:allocations:range_start' => 'Starting Port',
            'Hexactyl:client_features:allocations:range_end' => 'Ending Port',
        ];
    }
}
