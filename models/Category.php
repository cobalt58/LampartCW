<?php

namespace models;

class Category{
    private int $id;
    private ?int $parent;
    private ?string $title;

    public function __construct($id, $title = null, $parent = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->parent = $parent;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function parent()
    {
        return $this->parent;
    }

    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function title()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
}