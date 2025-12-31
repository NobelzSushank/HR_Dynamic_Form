<?php

namespace Modules\Core\Services;

use Modules\Core\Traits\Cacheable;
use Modules\Core\Traits\ResponseMessage;

abstract class BaseService
{
    use Cacheable;
    use ResponseMessage;

    protected array $rules = [];
    protected array $messages = [];

    protected int $cacheTTl = 30 * 24 * 60 * 60;
    protected bool $isCached = true;

}