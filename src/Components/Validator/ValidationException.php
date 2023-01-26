<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Components\Validator;

use Exception;
use Illuminate\Validation\Validator;
use Throwable;

/**
 * Class ValidationException
 *
 * @phpstan-consistent-constructor
 */
class ValidationException extends Exception
{
    /**
     * @var array<mixed>
     */
    public array $errors;

    /**
     * @param  array<mixed>  $errors
     * @param  int  $code
     * @param  Throwable|null  $previous
     */
    public function __construct(array $errors, int $code = 0, ?Throwable $previous = null)
    {
        $this->errors = $errors;

        parent::__construct(
            'Validation Error: ' .
            json_encode($errors, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            $code,
            $previous
        );
    }

    /**
     * Create a ValidationException from a Validator.
     *
     * @param Validator $validator
     * @param Validatable|null $owner
     * @return static
     */
    public static function fromValidator(Validator $validator, ?Validatable $owner = null): ValidationException
    {
        $results = [];

        foreach ($validator->errors()->getMessages() as $filed => $errors) {
            foreach ($errors as $error) {
                $tmp = [
                    'field' => $filed,
                    'message' => $error,
                ] + static::resolveRowNumber($owner);

                $results[] = $tmp;
            }
        }

        return new static($results);
    }

    /**
     * @param Validatable|null $owner
     * @return array<mixed>
     */
    protected static function resolveRowNumber(?Validatable $owner = null): array
    {
        if (! $owner || $owner->getRowNumber() === null) {
            return [];
        }

        return [
            'number' => $owner->getRowNumber(),
        ];
    }

    /**
     * Create a ValidationException from the arguments list.
     *
     * @param  string  $field
     * @param string|array<string> $error
     * @param string|array<string>|Validatable ...$args
     * @return static
     */
    public static function fromArgs(string $field, $error, ...$args): ValidationException
    {
        $args = func_get_args();

        if (count($args) % 2 !== 0) {
            /** @var Validatable $owner */
            $owner = array_pop($args);
        } else {
            $owner = null;
        }

        $errors = [];

        foreach (array_chunk($args, 2) as list($key, $error)) {
            $error = static::normalizeError($error);
            $error['field'] = $key;

            $errors[] = $error + static::resolveRowNumber($owner);
        }

        return new static($errors);
    }

    /**
     * @param mixed $error
     * @return array<mixed>
     */
    protected static function normalizeError($error): array
    {
        if (! is_array($error)) {
            $error = ['message' => (string) $error];
        }

        return $error;
    }

    /**
     * Create a ValidationException from raw array.
     *
     * @param array<mixed> $errors
     * @return static
     */
    public static function fromArray(array $errors): ValidationException
    {
        return new static($errors);
    }
}
