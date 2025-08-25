<?php

declare(strict_types=1);

namespace Elgentos\RemoveCityValidation\Model\Validator;

use Magento\Customer\Model\Validator\Street as OriginalStreetValidator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Street extends OriginalStreetValidator
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * Validate street fields.
     *
     * @param $customer
     *
     * @return bool
     */
    public function isValid($customer)
    {
        if (!$this->scopeConfig->isSetFlag('customer/address/enable_street_validation', ScopeInterface::SCOPE_STORE)) {
            return true;
        }

        if ($this->scopeConfig->isSetFlag('customer/address/use_builtin_street_regex', ScopeInterface::SCOPE_STORE)) {
            return parent::isValid($customer);
        }

        foreach ($customer->getStreet() as $street) {
            if (!$this->isValidStreet($street)) {
                parent::_addMessages([[
                    'street' => "Invalid Street Address"
                ]]);
            }
        }

        return count($this->_messages) == 0;
    }

    /**
     * @param $streetValue
     * @param $pattern
     *
     * @return bool
     */
    private function isValidStreet($streetValue)
    {
        if ($streetValue != null) {
            $pattern = $this->scopeConfig->getValue('customer/address/street_validation_regex', ScopeInterface::SCOPE_STORE);
            if (preg_match($pattern, $streetValue, $matches)) {
                return $matches[0] == $streetValue;
            }
        }

        return true;
    }
}
