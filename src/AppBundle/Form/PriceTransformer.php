<?php

namespace AppBundle\Form;

use AppBundle\Entity\Money;
use Symfony\Component\Form\DataTransformerInterface;

class PriceTransformer implements DataTransformerInterface
{
    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        return [
            'amount' => $value->getAmount(),
            'decimals' => $value->getDecimals(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        $money = new Money();
        $money->setAmount($value['amount']);
        $money->setDecimals($value['decimals']);

        return $money;
    }
}
