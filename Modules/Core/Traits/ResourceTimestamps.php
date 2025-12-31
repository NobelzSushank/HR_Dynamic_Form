<?php

namespace Modules\Core\Traits;

use Modules\Core\Facades\ParseTime;

trait ResourceTimestamps
{
    /**
     * @return array
     */
    public function timestamps(): array
    {
        return [
            "created_at" => $this->created_at->format(config("core.date_format")),
            "created_at_local" => ParseTime::parse($this->created_at),
            "created_at_human" => optional($this->created_at)->diffForHumans(),
            "created_at_human_local" => ParseTime::diffForHumans($this->created_at),
        ];
    }

    /**
     * @param array $resource
     *
     * @return array
     */
    public function withTimeStamp(array $resource): array
    {
        return array_merge($resource, $this->timestamps());
    }
}