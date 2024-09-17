<?php

namespace App\DataObjects;

final class AppDataObject
{
    private bool $isDefault;

    public function __construct(
        public string $name,
        public ?string $alias
    ) {
        if (empty($alias)) {
            $this->alias = $name;
        }
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function toArray()
    {
        return [
            'name' =>  $this->isDefault ? "{$this->name} (default)" : $this->name,
            'alias' => $this->alias,
        ];
    }
}
