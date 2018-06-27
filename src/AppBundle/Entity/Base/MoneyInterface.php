<?php

namespace AppBundle\Entity\Base;

interface MoneyInterface
{
    /**
     * @param int $decimals
     *
     * @return MoneyInterface
     */
    public function setDecimals(int $decimals): self;

    /**
     * @return int
     */
    public function getDecimals(): int;

    /**
     * @param int $amount
     *
     * @return MoneyInterface
     */
    public function setAmount(int $amount): self;

    /**
     * @return int
     */
    public function getAmount(): int;

    /**
     * @return int
     */
    public function getTotalAmount(): int;
}
