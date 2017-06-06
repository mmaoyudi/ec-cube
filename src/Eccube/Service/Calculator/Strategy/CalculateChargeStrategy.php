<?php
namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Order;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Service\Calculator\ShipmentItemCollection;

/**
 * 手数料の合計を集計して Order にセットする.
 */
class CalculateChargeStrategy implements CalculateStrategyInterface
{
    /* @var Application $app */
    protected $app;

    /* @var Order $Order */
    protected $Order;

    /** @var OrderItemTypeRepository */
    protected $OrderItemTypeRepository;

    public function execute(ShipmentItemCollection $ShipmentItems)
    {
        $charge = array_reduce(
            $ShipmentItems->getCharges()->toArray(),
            function($total, $ShipmentItem) {
                return $total + $ShipmentItem->getPrice() * $ShipmentItem->getQuantity();
            }, 0);
        $this->Order->setCharge($charge);
    }

    public function setApplication(Application $app)
    {
        $this->app = $app;
        return $this;
    }

    public function setOrder(Order $Order)
    {
        $this->Order = $Order;
        return $this;
    }
}
