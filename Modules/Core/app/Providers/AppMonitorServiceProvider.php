<?php

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AppMonitorServiceProvider extends ServiceProvider
{
     public function boot(): void
    {
        if (!app()->isProduction()) {
            //DOC Prevent n+1 query and mass assignment protection
            $this->setPreventions();

            //DOC Logging queries
            $this->setQueryLogs();

            //DOC Log slow request and command
            $this->setRequestLogs();
        }
    }

    private function setQueryLogs(): void
    {
        // Log every single query on query.log file.
        DB::listen(function ($query) {
            //Write requested query on file
            File::append(
                path: storage_path('/logs/query.log'),
                data: $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
            );
        });
    }

    private function setRequestLogs(): void
    {
        $setRequestCycleTTl = 5000; // 5 second
        $second = $setRequestCycleTTl / 1000;
        // Log slow requests.
        $this->app[HttpKernel::class]->whenRequestLifecycleIsLongerThan(
            $setRequestCycleTTl,
            function ($startedAt, $request, $response) use ($second) {
                Log::warning("Request: A request took longer than {$second} seconds.", [
                    "started" => $startedAt,
                    "request" => $request,
                    "response" => $response,
                ]);
            }
        );

        if ($this->app->runningInConsole()) {
            // Log slow commands.
            $this->app[ConsoleKernel::class]->whenCommandLifecycleIsLongerThan(
                $setRequestCycleTTl,
                function ($startedAt, $input, $status) use ($second) {
                    Log::warning("Console: A command took longer than {$second} seconds.", [
                        "started" => $startedAt,
                        "input" => $input,
                        "status" => $status,
                    ]);
                }
            );
        }
    }

    private function setPreventions(): void
    {
        Model::preventLazyLoading();
        // Model::preventSilentlyDiscardingAttributes();
    }
}
