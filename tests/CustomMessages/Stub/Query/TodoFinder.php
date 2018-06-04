<?php
/**
 * This file is part of the proophsoftware/event-machine.
 * (c) 2017-2018 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventMachineTest\CustomMessages\Stub\Query;

use React\Promise\Deferred;

final class TodoFinder
{
    private $lastQuery;

    public function __invoke($query, Deferred $deferred)
    {
        $this->lastQuery = $query;
    }

    public function getLastReceivedQuery()
    {
        return $this->lastQuery;
    }
}
