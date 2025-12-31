<?php

namespace Modules\Core\Facades\Services;

use Carbon\Carbon;
use Modules\Core\Traits\Cacheable;

class ParseTimeService
{
    use Cacheable;

    protected ?string $timeZone = null;
    protected string $dateFormat;

    public function __construct()
    {
        $this->dateFormat = config("core.date_format");
        $this->setTimeZone();
    }

    public function setTimeZone(?string $timeZone = "Asia/Kathmandu"): void
    {
        if (!$timeZone) {
            if (auth("user")->check()) {
                $user = auth("user")->user();
                $user->load(["userTimeZone"]);
                $timeZone = optional($user->userTimeZone)->value;
            } else {
                $this->timeZone = config("timezone", "Asia/Kathmandu");
            }
        }
        $this->timeZone = $timeZone;
    }

    public function parse(string $time, string $timeZone = null): string
    {
        $parsedTime = Carbon::parse($time)
            ->timezone($timeZone ?? $this->timeZone)
            ->format($this->dateFormat);
        return $parsedTime;
    }

    public function diffForHumans(string $time, string $timeZone = null): string
    {
        $time = $this->parse($time, $timeZone);
        $diffForHuman = Carbon::parse($time)->diffForHumans();
        return $diffForHuman;
    }
}