<?php


namespace Test\Ecotone\Modelling\Fixture\MultipleHandlersAtSameMethod;

use Ecotone\Modelling\Annotation\Aggregate;
use Ecotone\Modelling\Annotation\AggregateIdentifier;
use Ecotone\Modelling\Annotation\CommandHandler;
use Ecotone\Modelling\Annotation\QueryHandler;

class Basket
{
    private array $items;

    #[CommandHandler("basket.add")]
    #[CommandHandler("basket.removeLast")]
    public function addToBasket(array $command) : void
    {
        if (!isset($command["item"])) {
            array_pop($this->items);
            return;
        }

        $this->items[] = $command["item"];
    }

    #[QueryHandler("basket.get")]
    #[QueryHandler("basket.getAll")]
    public function getBasket() : array
    {
        return $this->items;
    }
}