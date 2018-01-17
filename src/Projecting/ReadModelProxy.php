<?php
declare(strict_types=1);

namespace Prooph\EventMachine\Projecting;

use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\Persistence\Stream;
use Prooph\EventStore\Projection\AbstractReadModel;

final class ReadModelProxy extends AbstractReadModel
{
    /**
     * @var array
     */
    private $projectionDescriptions;

    /**
     * @var EventMachine
     */
    private $eventMachine;

    /**
     * @var ReadModel[]
     */
    private $readModels;

    public function __construct(
        array $projectionDescriptions,
        EventMachine $eventMachine)
    {
        $this->projectionDescriptions = $projectionDescriptions;
        $this->eventMachine = $eventMachine;
    }

    public function handle(string $streamName, Message $event): void
    {
        foreach ($this->readModels as $readModel) {
            if($readModel->isInterestedIn($streamName, $event)) {
                $readModel->handle($event);
            }
        }
    }

    public function init(): void
    {
        $this->readModels = [];

        foreach ($this->projectionDescriptions as $prjName => $desc) {
            $stream = Stream::fromArray($desc[ProjectionDescription::SOURCE_STREAM]);

            if($stream->isLocalService()) {
                $readModel = ReadModel::fromProjectionDescription($desc, $this->eventMachine);
                $readModel->prepareForRun();
                $this->readModels[] = $readModel;
            }
        }
    }

    public function isInitialized(): bool
    {
        return null !== $this->readModels;
    }

    public function reset(): void
    {
        $this->delete();
    }

    public function delete(): void
    {
        foreach ($this->readModels as $readModel) $readModel->delete();
    }
}