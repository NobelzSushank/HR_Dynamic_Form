<?php

namespace Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AppMacroServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function boot(): void
    {
        $this->macroQueryBuilderWhereLike();
        $this->macroCollectionPaginate();
        // $this->macroCollectionNullify();
        $this->extendValidator();
    }

    private function macroQueryBuilderWhereLike(): void
    {
        // Macro for whereLike
        Builder::macro("whereLike", function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        value: Str::contains($attribute, '.'),
                        callback: function (Builder $query) use ($attribute, $searchTerm) {
                            $explodedAttribute = explode('.', $attribute);
                            $relationAttribute = end($explodedAttribute);
                            array_pop($explodedAttribute);
                            $relationName = (count($explodedAttribute) > 1)
                                ? implode(".", $explodedAttribute)
                                : $explodedAttribute[0];
                            $query->orWhereHas(
                                relation: $relationName,
                                callback: function (Builder $query) use ($relationAttribute, $searchTerm) {
                                    $query->where($relationAttribute, "LIKE", "%{$searchTerm}%");
                                }
                            );
                        },
                        default: function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, "LIKE", "%{$searchTerm}%");
                        }
                    );
                }
            });
            return $this;
        });
    }

    private function macroCollectionPaginate(): void
    {
        if (!Collection::hasMacro("paginate")) {
            Collection::macro("paginate", function ($perPage, $total = null, $page = null, $pageName = "page") {
                $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
                $options = [
                    "path" => LengthAwarePaginator::resolveCurrentPath(),
                    "pageName" => $pageName,
                ];
                return new LengthAwarePaginator(
                    items: $this->forPage($page, $perPage),
                    total: $total ?: $this->count(),
                    perPage: $perPage,
                    currentPage: $page,
                    options: $options
                );
            });
        }
    }

    // public function macroCollectionNullify(): void
    // {
    //     if (!Collection::hasMacro("nullify")) {
    //         Collection::macro("nullify", function () {
    //             return $this->map(fn ($value) => Nullify::parse($value));
    //         });
    //     }
    // }

    public function extendValidator(): void
    {
        Validator::extend("phone_number", function ($attribute, $value) {
            return preg_match_all("/^([0-9\s\-\+\(\)]*)$/i", $value);
        }, "The :attribute must be a valid phone number.");
    }
}
