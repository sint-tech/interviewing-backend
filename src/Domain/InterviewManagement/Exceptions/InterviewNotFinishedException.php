<?php

namespace Domain\InterviewManagement\Exceptions;

class InterviewNotFinishedException extends \Exception
{
    protected $message = 'coudn\'t process this request, the current interview still running';
}
