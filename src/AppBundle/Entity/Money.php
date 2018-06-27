<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Base\MoneyInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Money
 * @ORM\Embeddable()
 */
class Money implements MoneyInterface
{
    /**
     * @var int
     * @JMS\Type("integer")
     *
     * @ORM\Column(name="amount", type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     * @JMS\Expose()
     */
    private $amount = 0;

    /**
     * @var int
     * @JMS\Type("integer")
     *
     * @ORM\Column(name="decimals", type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     * @JMS\Expose()
     */
    private $decimals = 0;

    public function setDecimals(int $decimals): MoneyInterface
    {
        $this->setAmount($this->getAmount() + $decimals / 100);
        $this->decimals = $decimals % 100;

        return $this;
    }

    /**
     * @return int
     */
    public function getDecimals(): int
    {
        return $this->decimals;
    }

    /**
     * @param int $amount
     * @return MoneyInterface
     */
    public function setAmount(int $amount): MoneyInterface
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Convert amount and decimals to full amount
     * @return int
     */
    public function getTotalAmount(): int
    {
        return $this->getAmount() * 100 + $this->getDecimals();
    }
}
