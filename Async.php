<?php

class Async
{
    public static array $tasks = [];

    public static function async(Fiber $fiber): mixed
    {
        self::$tasks[] = [Fiber::getCurrent(), $fiber];
        $fiber->start();
        while (!$fiber->isTerminated()) {
            $fiber->resume();

            if ($fiber->isTerminated()) {
                break;
            } else {
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
