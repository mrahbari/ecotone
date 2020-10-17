<?php


namespace Test\Ecotone\Messaging\Fixture\Behat\Calculating;

use Ecotone\Messaging\Annotation\Scheduled;
use Ecotone\Messaging\Annotation\ServiceActivator;

class InboundCalculation
{
    /**
     * @return int
     * @Scheduled(
     *     endpointId="inboundCalculator",
     *     requestChannelName="calculateForInbound"
     * )
     * @BeforeMultiplyCalculation(amount=3)
     * @AroundSumCalculation(amount=2)
     * @AfterMultiplyCalculation(amount=10)
     */
    public function calculateFor() : int
    {
        return 5;
    }

    /**
     * The result will be published to channel after this method
     *
     * @param int $number
     * @return int
     * @ServiceActivator(
     *     inputChannelName="calculateForInbound",
     *     outputChannelName="resultChannel"
     * )
     */
    public function calculate(int $number) : int
    {
        return $number;
    }
}