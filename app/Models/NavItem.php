<?php

declare(strict_types=1);

namespace Kickflip\Models;

class NavItem implements NavItemInterface
{
    public function __construct(
        public string $title,
        public string $url = '',
        /**
         * @var ?array<NavItem>
         */
        public ?array $children = null,
    ) {}

    public static function make(string $title, ?string $url = null): self
    {
        return new self($title, $url);
    }

    /**
     * @param array<self> $children
     * @return $this
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function hasUrl(): bool
    {
        return !empty($this->url);
    }

    public function hasChildren(): bool
    {
        return null !== $this->children && count($this->children) > 0;
    }
}
