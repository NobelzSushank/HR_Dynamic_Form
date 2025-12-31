<?php

namespace Modules\Core\Traits;

trait HasResourceAdapter
{
    use ResourceTimestamps;

    /**
     * Convert given resource data with override classes and add time stamp
     *
     * @param object $request
     * @param array $resource
     * @param boolean $withTimeStamp
     *
     * @return array
     */
    public function convert(
        object $request,
        array $resource,
        bool $withTimeStamp = true
    ): array {
        $class = $this::class;
        // merge resource with timestamp
        if ($withTimeStamp) {
            $resource = $this->withTimeStamp($resource);
        }
        $overrideResources = collect(config("api_resources") ?? [])->where("resource", $class);
        if ($overrideResources->count() > 0) {
            $mappedResources = $overrideResources->sortBy("override.priority", descending: false)
                ->mapWithKeys(function ($resource) use ($request) {
                    $additionalResource = resolve($resource["override"]["resource"], [
                        "resource" => $this,
                        "request" => $request,
                    ]);

                    return $additionalResource->toArray($request);
                });
            $resource = $this->withTimeStamp($mappedResources->toArray());
        }

        return $resource;
    }
}