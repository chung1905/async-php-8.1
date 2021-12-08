<?php

class Async
{
    public static array $tasks = [];

    public static function async(Fiber $fiber): mixed
    {
        self::$tasks[] = [Fiber::getCurrent(), $fiber];
        #echo "Async::await start\n";
        $fiber->start();
        #echo "Async::await start after\n";
        while (!$fiber->isTerminated()) {
            #echo "Async::await resume\n";
            $fiber->resume();
            #echo "Async::await resume after\n";

            if ($fiber->isTerminated()) {
                #echo "Async::await break\n";
                break;
            } else {
                #echo "Async::await suspend\n";
                Fiber::suspend();
            }
        }

        #echo "Async::await return\n";
        return $fiber->getReturn();
    }

    public static function wait(): void
    {
        while (count(self::$tasks) > 0) {
            $toRemove = [];
            /**
             * @var int $index
             * @var Fiber $fiber
             * @var Fiber $_
             */
            foreach (self::$tasks as $index => list($fiber, $_)) {
                if (!$fiber->isTerminated() && $fiber->isSuspended()) {
                    $fiber->resume();
                } elseif ($fiber->isTerminated()) {
                    $toRemove[] = $index;
                }
            }

            foreach ($toRemove as $indexToRemove) {
                unset(self::$tasks[$indexToRemove]);
            }

            self::$tasks = array_values(self::$tasks);
        }
    }
}
