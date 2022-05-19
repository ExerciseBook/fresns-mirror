<?php

namespace App\Exceptions;

use App\Utilities\ConfigUtility;
use App\Fresns\Api\Traits\ApiResponseTrait;

class FresnsApiException extends \Exception
{
    use ApiResponseTrait;

    public function __construct(int $code, ?string $unikey = null)
    {
        $message = $this->getCodeMessage($code, $unikey);

        parent::__construct($message, $code);
    }

    public function getCodeMessage(int $code, ?string $unikey = null)
    {
        return ConfigUtility::getCodeMessage($code, $unikey, \request()->header('langTag'));
    }

    public function render()
    {
        // if (!\request()->wantJsons()) {
        //     return view('error.30000', $this);
        // }

        return $this->failure($this->getCode(), $this->getMessage());
    }
}
