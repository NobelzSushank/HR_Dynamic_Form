<?php

namespace Modules\Core\Traits;

trait ResponseMessage
{
    protected ?string $modelName = null;
    protected ?string $transModulePath = null;
    protected array $lang = [
        "fetch-all-success" => "response.fetch-list-success",
        "fetch-success" => "response.fetch-success",
        "create-success" => "response.create-success",
        "update-success" => "response.update-success",
        "delete-success" => "response.delete-success",
        "permanent-delete-success" => "response.permanent-delete-success",
        "delete-error" => "response.deleted-error",
        "last-delete-error" => "response.last-delete-error",
        "not-found" => "response.not-found",
        "not-matched" => "response.not-matched",
        "status-updated" => "response.status-updated",
        "restore-success" => "response.restore-success",
    ];

    /**
     *
     * Map translation
     *
     * @param string $key
     * @param array|null $parameters
     * @param string $module
     *
     * @return string
     */
    public function lang(string $key, ?array $parameters = [], string $module = "core::app"): string
    {
        $parameters = empty($parameters) ? ["name" => $this->modelName] : $parameters;
        $translation_key = $this->lang[$key] ?? $key;
        $module = $this->transModulePath ? $this->transModulePath : $module;
        return trans("{$module}.{$translation_key}", $parameters);
    }
}