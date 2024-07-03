<?php
/**
 * @file
 * Contains CsrfGuard
 */

namespace App\Form\Validator;

use Mezzio\Csrf\CsrfGuardInterface;
use Laminas\Validator\AbstractValidator;

class CsrfGuard extends AbstractValidator
{
    /**
     * Error codes
     * @const string
     */
    const NOT_SAME = 'notSame';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_SAME => "The form submitted did not originate from the expected site",
    ];

    /**
     * @var \Mezzio\Csrf\CsrfGuardInterface
     */
    private $guard;

    public function __construct(CsrfGuardInterface $guard, $options = null)
    {
        parent::__construct($options);
        $this->guard = $guard;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @return bool
     * @throws \Laminas\Validator\Exception If validation of $value is impossible
     */
    public function isValid($value)
    {
        if (!$this->guard->validateToken($value)) {
            $this->error(self::NOT_SAME);
            return false;
        }
        return true;
    }
}
