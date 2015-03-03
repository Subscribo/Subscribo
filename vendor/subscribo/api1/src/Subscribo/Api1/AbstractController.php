<?php namespace Subscribo\Api1;

use Subscribo\Api1\Interfaces\SelfRegisteringControllerInterface;
use Illuminate\Routing\Controller;
use Subscribo\Api1\Context;
use App;

use Subscribo\Api1\Traits\SelfRegisteringControllerTrait;
use Subscribo\Api1\Traits\ContextRequestValidationTrait;
use Subscribo\Api1\Traits\QuestionAskingControllerTrait;

/**
 * Class AbstractController
 * Base class for API v1 controllers
 *
 *
 * @package Subscribo\Api1
 */
abstract class AbstractController extends Controller implements SelfRegisteringControllerInterface
{
    use SelfRegisteringControllerTrait;
    use ContextRequestValidationTrait;
    use QuestionAskingControllerTrait;

    /**
     * @var Context
     */
    protected $context;


    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $what
     * @return mixed
     */
    protected function applicationMake($what)
    {
        return App::make($what);
    }

}
