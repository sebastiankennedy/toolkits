<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Components\PhpSpreadsheet;

use Luyiyuan\Toolkits\Components\Exceptions\ValidationException;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BaseImporter extends Parser
{
    public function __construct(Worksheet $sheet)
    {
        parent::__construct($sheet);
        $this->requiredColumnMessage = '找不到「{name}」必填列。';
    }

    /**
     * @var int
     */
    public int $maxRows = 20000;

    protected function getLabelRegex(string $label): string
    {
        return sprintf("/^%s$/u", preg_quote($label, '/'));
    }

    /**
     * @param  Cell  $cell
     * @return mixed
     * @throws \Exception
     */
    protected function getValue(Cell $cell)
    {
        $value = $cell->getValue();

        if (Date::isDateTime($cell)) {
            // 业务端自行处理具体的日期/时间格式
            return Date::excelToTimestamp($value, 'Asia/Shanghai');
        }

        return $value;
    }

    /**
     * @param  string  $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateInteger(string $field, $value, array $params): bool
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->addError(
                'validation',
                $field,
                $params['message'] ?? sprintf('单元格 %s 必须为整数', $field)
            );
        }

        return true;
    }

    /**
     * @param  mixed  $field
     * @param  mixed  $value
     * @param  mixed  $params
     * @return bool
     */
    protected function validateNumber($field, $value, $params): bool
    {
        if ($this->isEmpty($value)) {
            return true;
        }

        if (! is_numeric($value)) {
            $this->addError(
                'validation',
                $field,
                $params['message'] ?? sprintf('单元格 %s 必须为数字', $field)
            );
        }

        return true;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws ValidationException
     */
    public function parse(): array
    {
        try {
            return parent::parse();
        } catch (Exception $exception) {
            $coordinate = $this->getErrorCoordinate($exception->getMessage());
            throw ValidationException::fromArgs('sheet', sprintf('表格 %s 里含有不支持的公式', $coordinate));
        }
    }

    /**
     * @param  bool  $clearErrors
     * @return bool
     * @throws ValidationException|\PhpOffice\PhpSpreadsheet\Exception
     */
    public function validate($clearErrors = true): bool
    {
        try {
            return parent::validate($clearErrors);
        } catch (Exception $exception) {
            $coordinate = $this->getErrorCoordinate($exception->getMessage());
            throw ValidationException::fromArgs('sheet', sprintf('表格 %s 里含有不支持的公式', $coordinate));
        }
    }

    public function getErrorCoordinate(string $errorMsg): string
    {
        $position = '';
        if (preg_match("/!(\w+\d+) ->/", $errorMsg, $matches)) {
            $position = $matches[1] ?? '';
        }

        return $position;
    }
}
