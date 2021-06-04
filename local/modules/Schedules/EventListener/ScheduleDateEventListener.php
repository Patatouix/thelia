<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/


namespace Schedules\EventListener;

use DateInterval;
use DatePeriod;
use Schedules\Event\ScheduleDateEvent;
use Schedules\Event\ScheduleDateStockEvent;
use Schedules\Model\ScheduleDate;
use Schedules\Model\ScheduleDateQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\HttpFoundation\Request;

/**
 * Class ScheduleDateEventListener
 * @package Schedules\EventListener
 */
class ScheduleDateEventListener implements EventSubscriberInterface
{
    protected $request;
    protected $dispatcher;

    public function __construct(Request $request, EventDispatcherInterface $dispatcher)
    {
        $this->request = $request;
        $this->dispatcher = $dispatcher;
    }

    // create all dates associated to a schedule
    public function processScheduleDate(ScheduleDateEvent $event)
    {
        $schedule = $event->getSchedule();
        $id = $schedule->getId();
        $day = $schedule->getDay();
        $periodBegin = $schedule->getPeriodBegin();
        $periodEnd = $schedule->getPeriodEnd();
        $begin = $schedule->getBegin();
        $end = $schedule->getEnd();
        $closed = $schedule->getClosed();

        if (null !== $productSchedule = $schedule->getProductSchedule()) {
            $stock = $productSchedule->getStock();
        };

        // delete all dates of schedule before creating new ones
        ScheduleDateQuery::create()
            ->findByScheduleId($id)
            ->delete()
        ;

        if (null !== $day) {
            //setTime() is a trick to get last day of DatePeriod
            $period = new DatePeriod($periodBegin, new DateInterval('P1D'), $periodEnd->setTime(0,0,1));

            // iterate on all day of period
            foreach($period as $dayPeriod) {

                if ((int)$dayPeriod->format('w') === (int)($day + 1)) {

                    $date = new ScheduleDate();
                    $date->setDateBegin($dayPeriod)
                        ->setDateEnd($dayPeriod)
                        ->setStock($stock)
                        ->setClosed($closed)
                        ->setScheduleId($id)
                        ->setTimeBegin($begin)
                        ->setTimeEnd($end)
                        ->save()
                    ;
                }
            }
        } else {
            $date = new ScheduleDate();
            $date->setDateBegin($periodBegin)
                ->setDateEnd($periodEnd)
                ->setStock($stock)
                ->setClosed($closed)
                ->setScheduleId($id)
                ->setTimeBegin($begin)
                ->setTimeEnd($end)
                ->save()
            ;
        }
    }

    public function getRemainingStock(ScheduleDateStockEvent $event)
    {
        $scheduleDate = $event->getScheduleDate();
        $remainingStock = $scheduleDate->getRemainingStock();

        // check if cart has consumed stock of this schedule date
        foreach ($this->request->getSession()->getSessionCart($this->dispatcher)->getCartItems() as $cartItem) {
            if (null !== $cartItemScheduleDate = $cartItem->getCartItemScheduleDate()) {
                if ($cartItemScheduleDate->getScheduleDateId() === $scheduleDate->getId()) {
                    $remainingStock -= $cartItem->getQuantity();
                    //only one cart item is supposed to have this schedule date
                    break;
                }
            }
        }
        $event->setRemainingStock($remainingStock);
    }

    /**
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            ScheduleDateEvent::SCHEDULE_DATE_EVENT => ['processScheduleDate', 128],
            ScheduleDateStockEvent::SCHEDULE_DATE_STOCK_EVENT => ['getRemainingStock', 128],
        ];
    }
}