<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Modules\Core\Traits\ApiResponse;
use Modules\Core\Traits\ExceptionHandler;
use Modules\Core\Traits\ResponseMessage;

abstract class BaseController extends Controller
{
    use ApiResponse;
    use ExceptionHandler;
    use ResponseMessage;

    /**
     * model
     *
     * @deprecated This variable will be deprecated in next version
     *
     * @var [object]
     */
    public $model;

    /**
     * Service class
     *
     * @deprecated This variable will be deprecated in next version
     *
     * @var [object]
     */
    protected $service;

    protected array $responseMessages;
    protected array $exceptionMessages;
    protected array $exceptionStatusCodes;

    /**
     * Resource transformer
     *
     * @deprecated This variable will be deprecated in next version
     *
     * @var [object]
     */
    protected array $policies = [];

    public function __construct()
    {
        $this->responseMessages = array();
        $this->exceptionStatusCodes = array();
        $this->exceptionMessages = [
            ModelNotFoundException::class => $this->lang("not-found"),
        ];
    }

    /**
     * handleException handles the exception and formats exception code and messages.
     *
     * @param  object $exception
     * @return JsonResponse
     */
    public function handleException(object $exception): JsonResponse
    {
        // Set exception logs.
        $this->setFatalExceptionLog($exception);

        // returns exception in proper API format.
        return $this->errorResponse(
            message: $this->getExceptionMessage($exception),
            responseCode: $this->getExceptionStatus($exception)
        );
    }
}
