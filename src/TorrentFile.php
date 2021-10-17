<?php

declare(strict_types=1);

namespace SandFox\Torrent;

use ArrayObject;
use League\Uri\QueryString;
use Psr\EventDispatcher\EventDispatcherInterface;
use SandFox\Bencode\Bencode\BigInt;
use SandFox\Bencode\Decoder;
use SandFox\Bencode\Encoder;
use SandFox\Bencode\Types\BencodeSerializable;
use SandFox\Torrent\DataTypes\DateTimeWrapper;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\FileSystem\FileData;

final class TorrentFile implements BencodeSerializable
{
    public const CREATED_BY = 'PHP Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html';

    private array $data;

    // info hash cache
    private ?string $infoHash = null;
    private ?DateTimeWrapper $creationDate = null;

    /**
     * @param array $data
     */
    private function __construct(array $data = [])
    {
        $this->data = $data;
    }

    private static function decoder(): Decoder
    {
        return new Decoder(['bigInt' => BigInt::INTERNAL]);
    }

    /**
     * Load data from torrent file
     *
     * @param string $fileName
     * @return TorrentFile
     */
    public static function load(string $fileName): self
    {
        return new self(self::decoder()->load($fileName));
    }

    /**
     * Load data from bencoded string
     *
     * @param string $string
     * @return TorrentFile
     */
    public static function loadFromString(string $string): self
    {
        return new self(self::decoder()->decode($string));
    }

    /**
     * Load data from bencoded stream
     *
     * @param resource $stream
     * @return TorrentFile
     */
    public static function loadFromStream($stream): self
    {
        return new self(self::decoder()->decodeStream($stream));
    }

    /**
     * Create torrent file for specified path
     *
     * @param string $path to file or directory
     * @param array $options
     * @param EventDispatcherInterface|null $eventDispatcher Event Dispatcher to monitor hashing progress
     * @return TorrentFile
     */
    public static function fromPath(
        string $path,
        array $options = [],
        ?EventDispatcherInterface $eventDispatcher = null
    ): self {
        // generate data for files

        $dataGenerator = FileData::forPath($path, $options);

        $dataGenerator->generateData($eventDispatcher);

        $torrent = new self([
            'info' => $dataGenerator->getData(),
        ]);

        // set some defaults

        $torrent->setCreatedBy(self::CREATED_BY);
        $torrent->setCreationDate(new \DateTimeImmutable('now'));

        return $torrent;
    }

    /**
     * Save torrent to file
     *
     * @param string $fileName
     * @return bool
     */
    public function store(string $fileName): bool
    {
        return (new Encoder())->dump($this, $fileName);
    }

    /**
     * Return torrent file as a string
     *
     * @return string
     */
    public function storeToString(): string
    {
        return (new Encoder())->encode($this);
    }

    /**
     * Save torrent to a stream
     *
     * @param resource|null $stream
     * @return resource
     */
    public function storeToStream($stream = null)
    {
        return (new Encoder())->encodeToStream($this, $stream);
    }

    public function getRawData(): array
    {
        $stream = fopen('php://temp', 'r+');
        $this->storeToStream($stream);
        rewind($stream);
        $rawData = (new Decoder())->decodeStream($stream);
        fclose($stream);

        return $rawData;
    }

    public function bencodeSerialize(): array
    {
        // clean data from empty arrays
        $filter = function ($value): bool {
            return $value !== [];
        };

        $data = $this->data;
        $data['info'] = array_filter($data['info'] ?? [], $filter);
        $data = array_filter($data, $filter);

        return $data;
    }

    /* Torrent file fields */

    // announce

    public function setAnnounce(string $announce): self
    {
        $this->data['announce'] = $announce;
        return $this;
    }

    public function getAnnounce(): ?string
    {
        return $this->data['announce'] ?? null;
    }

    // announce list BEP-0012

    /**
     * @param string[]|string[][] $announceList
     * @return $this
     */
    public function setAnnounceList(array $announceList): self
    {
        foreach ($announceList as &$group) {
            if (\is_string($group)) {
                $group = [$group];
                continue;
            }

            if (!\is_array($group)) {
                throw new InvalidArgumentException(
                    'announce-list should be an array of strings or an array of arrays of strings'
                );
            }

            $group = array_values(array_unique($group));

            foreach ($group as $announce) {
                if (!\is_string($announce)) {
                    throw new InvalidArgumentException(
                        'announce-list should be an array of strings or an array of arrays of strings'
                    );
                }
            }
        }

        /** @var string[][] $announceList - string[] is converted to string[][] by now */

        $this->data['announce-list'] = array_values(
            array_unique(
                array_filter($announceList, 'count'),
                SORT_REGULAR
            )
        );

        return $this;
    }

    /**
     * @return string[][]
     */
    public function getAnnounceList(): array
    {
        return $this->data['announce-list'] ?? [];
    }

    // creation date

    /**
     * @param int|\DateTimeInterface|null $timestamp
     * @return $this
     */
    public function setCreationDate($timestamp): self
    {
        $this->creationDate = DateTimeWrapper::fromExternalValue($timestamp);
        $this->data['creation date'] = $this->creationDate;
        return $this;
    }

    /**
     * @deprecated Alias of getCreationDateAsTimestamp(). In 3.0 it will be an alias of getCreationDateAsDateTime()
     * @return int|null
     */
    public function getCreationDate(): ?int
    {
        return $this->getCreationDateAsTimestamp();
    }

    private function getCreationDateWrapper(): DateTimeWrapper
    {
        return $this->creationDate ??= DateTimeWrapper::fromDataValue($this->data['creation date'] ?? null);
    }

    public function getCreationDateAsDateTime(): ?\DateTimeImmutable
    {
        return $this->getCreationDateWrapper()->getDateTime();
    }

    public function getCreationDateAsTimestamp(): ?int
    {
        return $this->getCreationDateWrapper()->getTimestamp();
    }

    // comment

    public function setComment(?string $comment): self
    {
        $this->data['comment'] = $comment;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->data['comment'] ?? null;
    }

    // created by

    public function setCreatedBy(string $comment): self
    {
        $this->data['created by'] = $comment;
        return $this;
    }

    public function getCreatedBy(): string
    {
        return $this->data['created by'];
    }

    // private flag BEP-0027

    public function setPrivate(bool $isPrivate): self
    {
        $this->infoHash = null;

        if ($isPrivate) {
            $this->data['info']['private'] = 1;
        } else {
            unset($this->data['info']['private']);
        }

        return $this;
    }

    public function isPrivate(): bool
    {
        return isset($this->data['info']['private']) && $this->data['info']['private'] === 1;
    }

    /* service functions */

    public function getInfoHash(): string
    {
        return $this->infoHash ??= sha1((new Encoder())->encode(new ArrayObject($this->data['info'] ?? [])));
    }

    public function getDisplayName(): ?string
    {
        $infoName = $this->data['info']['name'] ?? '';

        return $infoName === '' ? $this->getInfoHash() : $infoName;
    }

    public function getFileName(): string
    {
        return $this->getDisplayName() . '.torrent';
    }

    public function getMagnetLink(): string
    {
        $pairs = [['xt', 'urn:btih:' . strtoupper($this->getInfoHash())]];

        $dn = $this->data['info']['name'] ?? '';
        if ($dn !== '') {
            $pairs[] = ['dn', $this->getDisplayName()];
        }

        $trackers = [];

        $rootTracker = $this->getAnnounce();

        if ($rootTracker) {
            $trackers[] = $rootTracker;
        }

        foreach ($this->getAnnounceList() as $trGroup) {
            foreach ($trGroup as $tracker) {
                $trackers[] = $tracker;
            }
        }

        foreach (array_unique($trackers) as $tr) {
            $pairs[] = ['tr', $tr];
        }

        $query = QueryString::build($pairs);

        return 'magnet:?' . $query;
    }
}
