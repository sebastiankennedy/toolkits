<?php
/**
 * @link http://www.seiue.com
 * @license Copyright (c) 2015 Seiue Inc.
 */

namespace Luyiyuan\Toolkits\Components\PhpSpreadsheet;

use BadMethodCallException;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * 解析器
 */
class Parser
{
    private Worksheet $_sheet;

    public string $requiredColumnMessage = "The required column '{name}' is missing";
    public int $skipRows = 0;
    /**
     * @var int The max rows will be parsed, zero means unlimited
     */
    public int $maxRows = 0;

    private ?int $_highestRow = null;

    /**
     * Parser constructor.
     *
     * @param  Worksheet  $sheet
     */
    public function __construct(Worksheet $sheet)
    {
        $this->_sheet = $sheet;
    }

    /**
     * Declare rules for excel cell validation.
     *
     * @return array
     *
     * [
     *   [
     *     'A', 1,
     *     ['required'],
     *     ['compare', 'compareValue' => 'foo']
     *   ],
     *   [
     *     'A-', 1,
     *     ['required'],
     *     ['compare', 'compareValue' => 'foo']
     *   ],
     *   ...
     * ]
     *
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * Declare rules to extract meta information from header row.
     *
     * @return array
     * [
     *  'name' => 'DisplayName',
     *  'name' => ['DisplayName', 'Alias1', 'Alias2'],
     *  'name' => function (rowData) {return 'name'}
     * ]
     */
    protected function fields(): array
    {
        return [];
    }

    /**
     * @throws Exception
     */
    public function validate($clearErrors = true): bool
    {
        if ($clearErrors) {
            $this->clearErrors();
        }

        if ($this->getColumns() === false) {
            return false;
        }

        $rules = $this->rules();
        foreach ($rules as $_rule) {
            list($columnDef, $rowDef) = $_rule;
            $valueRules = array_slice($_rule, 2);
            list($columns, $rows) = $this->parseColumnsAndRows($columnDef, $rowDef);

            foreach ($columns as $column) {
                foreach ($rows as $row) {
                    $p = $column . $row;
                    $value = trim($this->getValue($this->_sheet->getCell($p)));
                    $this->validateRules($p, $value, $valueRules);
                }
            }
        }

        return ! $this->hasErrors();
    }

    /**
     * Validate a value against multiple rules.
     *
     * @param $field
     * @param $value
     * @param  array  $rules
     * [
     *   ['name', 'param1' => 1, 'param2' => 2],
     *   ...
     * ]
     */
    public function validateRules($field, $value, array $rules = [])
    {
        foreach ($rules as $rule) {
            $params = array_slice($rule, 1);

            $validator = 'validate' . $rule[0];
            if (! method_exists($this, $validator)) {
                throw new BadMethodCallException("The validator '$rule[0]' does not exists");
            }

            if (($params['skipOnEmpty'] ?? false) && $this->isEmpty($value)) {
                continue;
            }

            $result = call_user_func([$this, $validator], $field, $value, $params);

            if (! $result) {
                break;
            }
        }
    }

    protected function isEmpty($value): bool
    {
        return $value === null || $value === [] || $value === '';
    }

    protected function formatMessage($message, $params): string
    {
        $placeholders = [];
        foreach ((array) $params as $name => $value) {
            $placeholders['{' . $name . '}'] = $value;
        }

        return strtr($message, $placeholders);
    }

    protected function validateRequired($field, $value, $params = []): bool
    {
        if ($isEmpty = $this->isEmpty($value)) {
            $message = $this->formatMessage($params['message'] ?? "The '{field}' field is required",
                [
                    'field' => $field, 'value' => $value,
                ]);
            $this->addError('validation', $field, $message);
        }

        return ! $isEmpty;
    }

    protected function validateIn($field, $value, $params = []): bool
    {
        if ($this->isEmpty($value)) {
            return true;
        }
        $in = $params['range'] ?? [];

        if (! ($inArray = in_array($value, $in))) {
            $message = $this->formatMessage($params['message'] ?? "The '{field}' field is invalid",
                [
                    'field' => $field, 'value' => $value,
                ]);
            $this->addError('validation', $field, $message);
        }

        return $inArray;
    }

    protected function validateRegexp($field, $value, $params = [])
    {
        $pattern = $params['pattern'];

        if ($this->isEmpty($value) && ($params['allowEmpty'] ?? true)) {
            return true;
        }

        if (! ($matched = preg_match($pattern, $value))) {
            $message = $this->formatMessage($params['message'] ?? "The '{field}' field is invalid",
                [
                    'field' => $field, 'value' => $value,
                ]);
            $this->addError('validation', $field, $message);
        }

        return $matched;
    }

    protected function validateCompare($field, $value, $params = []): bool
    {
        $type = $params['type'] ?? 'string';
        $operator = $params['operator'] ?? '==';
        $compareValue = $params['compareValue'];

        if ($type === 'number') {
            $value = (float) $value;
            $compareValue = (float) $compareValue;
        } else {
            $value = (string) $value;
            $compareValue = (string) $compareValue;
        }

        $result = false;
        switch ($operator) {
            case '==':
                $result = $value == $compareValue;
                break;
            case '===':
                $result = $value === $compareValue;
                break;
            case '!=':
                $result = $value != $compareValue;
                break;
            case '!==':
                $result = $value !== $compareValue;
                break;
            case '>':
                $result = $value > $compareValue;
                break;
            case '>=':
                $result = $value >= $compareValue;
                break;
            case '<':
                $result = $value < $compareValue;
                break;
            case '<=':
                $result = $value <= $compareValue;
                break;
        }

        if (! $result) {
            $message = $this->formatMessage($params['message'] ?? "The '{field}' field is invalid",
                [
                    'field' => $field, 'value' => $value,
                ]);
            $this->addError('validation', $field, $message);
        }

        return $result;
    }

    private $_columns;

    /**
     * Get the column definitions.
     *
     * @param  bool  $refresh
     *
     * @return array|false Array of the definition, indexed by column names.
     * @throws Exception
     */
    public function getColumns(bool $refresh = false)
    {
        if ($this->_columns !== null && ! $refresh) {
            return $this->_columns;
        }

        $fields = $this->fields();
        $columns = [];

        foreach ($fields as $field => $definition) {
            if (! is_callable($definition) && is_string($definition)) {
                $definition = [
                    'regexp' => '/^' . $definition . '$/u',
                    'label' => $definition,
                ];
            }

            $defBackup = $definition;

            if (! is_callable($definition)) {
                $definition = function ($rawData) use ($definition, $field) {
                    if (preg_match($definition['regexp'], $rawData)) {
                        return [
                            'name' => $field,
                            'label' => $definition['label'] ?? $rawData,
                        ];
                    }
                    return false;
                };
            }

            $hasDefinition = false;
            foreach ($this->_sheet->getRowIterator(1 + $this->skipRows)->current()->getCellIterator() as $cell) {
                $rawData = trim($cell->getValue());
                $result = $definition($rawData, $this);
                if ($result === false) {
                    continue;
                }

                if (is_string($result)) {
                    $result = ['name' => $result, 'label' => $rawData];
                }
                if (! isset($result['name'])) {
                    throw new BadMethodCallException("The callback in columns() should returns string or array contains 'name' attribute");
                }
                if (! isset($result['label'])) {
                    $result['label'] = $result['name'];
                }
                $columns[$cell->getColumn()] = $result;
                $hasDefinition = true;
            }

            if (is_array($defBackup) && isset($defBackup['required']) && $defBackup['required'] && ! $hasDefinition) {
                $this->addError('definition', $field,
                    strtr($this->requiredColumnMessage, ['{name}' => $defBackup['label']]));
            }
        }

        if ($this->hasErrors()) {
            return false;
        }

        return $this->_columns = $columns;
    }

    /**
     * @throws Exception
     */
    public function parse(): array
    {
        $data = [];
        $columns = $this->getColumns();
        if ($this->_sheet->getHighestRow() < 2 + $this->skipRows) {
            $this->addError('validation', '', '导入模板未填写任何信息，请检查修改后上传');
            return $data;
        }
        $rows = $this->_sheet->getRowIterator(2 + $this->skipRows);
        foreach ($rows as $row) {
            if ($row->getRowIndex() > $this->getHighestRow()) {
                break;
            }

            $dataRow = [];
            foreach ($row->getCellIterator() as $cell) {
                $column = $cell->getColumn();
                if (! isset($columns[$column])) {
                    continue;
                }
                $columnDef = $columns[$column];
                $dataRow[$columnDef['name']] = trim($this->getValue($cell));
            }
            $data[] = $dataRow;
        }
        return $data;
    }

    protected function getValue(Cell $cell)
    {
        $value = $cell->getValue();

        if (Date::isDateTime($cell)) {
            $value = date('Y-m-d', Date::excelToTimestamp($value));
        }

        return $value;
    }

    protected array $_errors = [];

    /**
     * Returns parsing & validation errors as array.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }

    public function hasErrors(): bool
    {
        return ! empty($this->_errors);
    }

    /**
     * @param  string  $type definition, validation
     * @param  string  $field
     * @param $message
     */
    public function addError(string $type, string $field, $message)
    {
        if (! is_array($message)) {
            $message = ['message' => $message];
        }

        $this->_errors[] = ['type' => $type, 'field' => $field] + $message;
    }

    public function clearErrors()
    {
        $this->_errors = [];
    }

    /**
     *
     * 获取指定规则的excel表格总列数
     *
     * @param string  $column
     * @return array
     * @throws Exception
     *@example [A-Z] [A-AR]
     */
    public function getColumnsInRange(string $column = 'A-'): array
    {
        $columns = [];
        if (preg_match('/^[A-Z]+-[A-Z]*$/', $column)) {
            if (strpos($column, '-') === false) {
                $columns[] = $column;
            } else {
                list($start, $end) = explode('-', $column);
                if (! $end) {
                    $end = $this->_sheet->getHighestColumn();
                }

                $start = Coordinate::columnIndexFromString($start);
                $end = Coordinate::columnIndexFromString($end);

                for ($i = $start; $i <= $end; $i++) {
                    $columns[] = Coordinate::stringFromColumnIndex($i);
                }
            }
        }

        return $columns;
    }

    /**
     * @param  string  $column
     * @param  string  $row
     * @return array
     * [
     *   [A, B, C],
     *   [1, 2, 3]
     * ]
     * @throws Exception
     */
    protected function parseColumnsAndRows(string $column, string $row): array
    {
        $columns = [];
        if (preg_match('/^[A-Z]+-?\d*$/', $column)) {
            if (strpos($column, '-') === false) {
                $columns[] = $column;
            } else {
                list($start, $end) = explode('-', $column);
                if (! $end) {
                    $end = $this->_sheet->getHighestColumn();
                }

                $start = Coordinate::columnIndexFromString($start);
                $end = Coordinate::columnIndexFromString($end);

                for ($i = $start; $i < $end; $i++) {
                    $columns[] = Coordinate::stringFromColumnIndex($i);
                }
            }
        } else {
            foreach ($this->_sheet->getRowIterator(1 + $this->skipRows)->current()->getCellIterator() as $cell) {
                if (preg_match($column, trim($cell->getValue()))) {
                    $columns[] = $cell->getColumn();
                }
            }
        }

        $rows = [];
        if (strpos($row, '-') === false) {
            $rows[] = $row;
        } else {
            list($start, $end) = explode('-', $row);
            if (! $end) {
                $end = $this->getHighestRow();
            }
            for ($i = $start; $i <= $end; $i++) {
                $rows[] = $i;
            }
        }

        return [$columns, $rows];
    }

    protected function getHighestRow(): int
    {
        if ($this->_highestRow !== null) {
            return $this->_highestRow;
        }

        $highestRow = $this->_sheet->getHighestRow();

        if ($this->maxRows && $highestRow > $this->maxRows) {
            $highestRow = $this->maxRows;
        }

        $rows = $this->_sheet->rangeToArray('A1:' . $this->_sheet->getHighestColumn() . $highestRow);
        $emptyRows = 0;

        foreach ($rows as $index => $row) {
            $filtered = array_filter($row, function ($value) {
                return $value !== null && trim($value) !== '';
            });

            if ($filtered === []) {
                if ($emptyRows == 20) {
                    break;
                }
                $emptyRows++;
            } else {
                $emptyRows = 0;
            }

            $this->_highestRow = $index + 1;
        }

        return $this->_highestRow -= $emptyRows;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function load($filename)
    {
        $reader = IOFactory::createReaderForFile($filename);
        //读取带格式cell会有问题，暂时注释
        //$reader->setReadDataOnly(true);
        return $reader->load($filename);
    }
}
