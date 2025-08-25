<?php

declare(strict_types=1);

namespace Elgentos\RemoveCityValidation\Model\Validator;

use Magento\Customer\Model\Customer;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Override the city validator to allow any value.
 */
class City extends AbstractValidator
{
    /**
     * Validate city fields.
     *
     * @param Customer $value
     * @return bool
     */
    public function isValid($value)
    {
        // Always return true as validation is removed
        return true;
    }
}
