<?php


namespace App\Entities;


/**
 * Class Post
 * @package App\Entities
 */
class Post extends AbstractEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $views;

    /**
     * Unix date
     * @var int
     */
    private $date;

    /**
     * @var string[]
     */
    private $mediaPaths = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Post
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Post
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     * @return Post
     */
    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * @param int $date
     * @return Post
     */
    public function setDate(int $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return array
     */
    public function getMediaPaths(): array
    {
        return $this->mediaPaths;
    }

    /**
     * @param string $path
     * @return Post
     */
    public function addMediaPath(string $path): self
    {
        $this->mediaPaths[] = $path;

        return $this;
    }

}
