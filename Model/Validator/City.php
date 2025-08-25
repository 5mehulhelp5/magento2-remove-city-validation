<?php

declare(strict_types=1);

namespace Elgentos\RemoveCityValidation\Model\Validator;

use Magento\Customer\Model\Validator\City as OriginalCityValidator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class City extends OriginalCityValidator
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * @param $customer
     *
     * @return bool
     */
    public function isValid($customer)
    {
        if (!$this->scopeConfig->isSetFlag('customer/address/enable_city_validation', ScopeInterface::SCOPE_STORE)) {
            return true;
        }

        if ($this->scopeConfig->isSetFlag('customer/address/use_builtin_city_regex', ScopeInterface::SCOPE_STORE)) {
            return parent::isValid($customer);
        }

        if (!$this->isValidCity($customer->getCity())) {
            parent::_addMessages([[
                'city' => "Invalid City"
            ]]);
        }

        return count($this->_messages) == 0;
    }

    /**
     * @param $cityValue
     *
     * @return bool
     */
    private function isValidCity($cityValue)
    {
        if ($cityValue != null) {
            $pattern = $this->scopeConfig->getValue('customer/address/city_validation_regex', ScopeInterface::SCOPE_STORE);
            if (preg_match($pattern, $cityValue, $matches)) {
                return $matches[0] == $cityValue;
            }
        }

        return true;
    }
}
