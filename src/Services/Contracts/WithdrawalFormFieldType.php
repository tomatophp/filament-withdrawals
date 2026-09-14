<?php

namespace TomatoPHP\FilamentWithdrawals\Services\Contracts;

use Filament\Forms\Components\TextInput;

class WithdrawalFormFieldType
{
    public string $name;

    public string $label;

    public string $color = 'primary';

    public string $icon = 'heroicon-s-bars-3-center-left';

    public string $className = TextInput::class;

    /**
     * @return Author
     */
    public static function make(string $name): self
    {
        return (new self)->name($name);
    }

    /**
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return $this
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return $this
     */
    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return $this
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return $this
     */
    public function className(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'color' => $this->color,
            'icon' => $this->icon,
            'className' => $this->className,
        ];
    }
}
