<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Components\Exceptions;

/**
 * Interface Validatable
 *
 * @package phpapi\support\validation
 */
interface Validatable
{
    /**
     * Returns the row number.
     *
     * @return int|null
     */
    public function getRowNumber(): ?int;

    /**
     * Returns the validation rules.
     *
     * @return array<mixed>
     */
    public function rules(): array;

    /**
     * Performs the validation.
     *
     * @return bool
     * @throws ValidationException
     */
    public function validate(): bool;

    /**
     * Create new instance with given attributes and number.
     *
     * @param  array<mixed>  $attributes
     * @param  int  $number
     * @return static
     */
    public static function make(array $attributes, int $number): Validatable;
}
