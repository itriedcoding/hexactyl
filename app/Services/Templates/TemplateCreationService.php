<?php

namespace Hexactyl\Services\Templates;

use Hexactyl\Models\ServerTemplate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TemplateCreationService
{
    /**
     * Create a new server template.
     *
     * @throws ValidationException
     */
    public function create(array $data): ServerTemplate
    {
        $validated = $this->validate($data);

        return ServerTemplate::create($validated);
    }

    /**
     * Validate template data.
     *
     * @throws ValidationException
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'author' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:50',
            'is_public' => 'boolean',
            'is_default' => 'boolean',
            'egg_id' => 'required|exists:eggs,id',
            'nest_id' => 'required|exists:nests,id',
            'location_id' => 'required|exists:locations,id',
            'docker_image' => 'nullable|string|max:255',
            'startup' => 'nullable|array',
            'environment' => 'nullable|array',
            'limits' => 'nullable|array',
            'feature_limits' => 'nullable|array',
            'allocations' => 'nullable|array',
            'startup_commands' => 'nullable|array',
            'post_install_commands' => 'nullable|array',
            'tags' => 'nullable|array',
            'icon' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $validator->validated();
    }

    /**
     * Duplicate an existing template.
     *
     * @throws ValidationException
     */
    public function duplicate(int $templateId): ServerTemplate
    {
        $template = ServerTemplate::findOrFail($templateId);

        $data = $template->toArray();
        unset($data['id'], $data['uuid'], $data['created_at'], $data['updated_at'], $data['usage_count'], $data['last_used_at']);

        $data['name'] = $data['name'] . ' (Copy)';
        $data['is_public'] = false;
        $data['is_default'] = false;

        return $this->create($data);
    }
}