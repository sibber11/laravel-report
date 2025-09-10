<?php

namespace BlinkerBoy\Report;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class Type implements \JsonSerializable, Arrayable
{
    protected array $select;

    protected array $hidden = [];

    protected string $name;

    protected string $className;

    protected bool $dateRange = true;

    protected bool $singleDate = false;

    protected bool $dateRequired = true;

    protected bool $include = true;
    private Reporter $reporter;

    public function __construct(string $name, string $className)
    {
        $this->name = $name;
        $this->className = $className;
    }

    public function select($label, $model = null, $placeholder = null, $required = false, $multiple = false, $options = null, $remote = null, $default = null): static
    {
        if (!$remote) {
            $remote = 'remote.' . Str::kebab($label) . 's';
        }
        $this->select[] = [
            'label' => $label,
            'model' => $model ?? $this->getModelIdName($label),
            'placeholder' => __($placeholder) ?? $label,
            'required' => $required,
            'multiple' => $multiple,
            'options' => $options,
            'remote' => $remote,
            'default' => $default,
        ];

        return $this;
    }

    public function hidden(string $name, $value = true): static
    {
        $this->hidden[] = [
            'name' => $name,
            'value' => $value
        ];

        return $this;
    }

    public function getModelIdName($label): string
    {
        return \Str::snake($label) . '_id';
    }

    public function activeable($required = false): static
    {
        $this->select[] = [
            'label' => 'is_active',
            'placeholder' => 'is_active',
            'required' => $required,
            'model' => 'is_active',
            'options' => [
                ['id' => true, 'value' => __('Active')],
                ['id' => false, 'value' => __('Inactive')],
            ],
        ];

        return $this;
    }

    public function singleDate(): static
    {
        $this->singleDate = true;

        return $this;
    }

    public function noDateRange(): static
    {
        $this->dateRange = false;

        return $this;
    }

    public function dateNotRequired(): static
    {
        $this->dateRequired = false;

        return $this;
    }

    public function exclude(): static
    {
        $this->include = false;

        return $this;
    }

    //    public function dateRequired(): static
    //    {
    //        $this->dateRequired = true;
    //        return $this;
    //    }

    public function included(): bool
    {
        return $this->include;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        if (!$this->include) {
            return [
                'name' => $this->name,
                'include' => false,
            ];
        }

        return [
            'name' => $this->name,
            'selects' => $this->select ?? null,
            'dateRange' => $this->dateRange,
            'singleDate' => $this->singleDate,
            'dateRequired' => $this->dateRequired,
            'include' => true,
            'hidden' => $this->hidden,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    public function setReporter(Reporter $param)
    {
        $this->reporter = $param;
    }
}
