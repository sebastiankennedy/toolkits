<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\Components\PhpSpreadsheet;

use Luyiyuan\Toolkits\Components\PhpSpreadsheet\BaseImporter;
use Luyiyuan\Toolkits\Tests\TestCase;

use function Luyiyuan\Toolkits\Functions\fail_if_file_not_exists;

class BaseImporterTest extends TestCase
{
    public function testImport(): void
    {
        $file = __DIR__ . '/../../Data/Csv/scores.csv';
        fail_if_file_not_exists($file);
        $sheet = BaseImporter::load($file)->getActiveSheet();
        $importer = new class ($sheet) extends BaseImporter {
        };
        self::assertInstanceOf(BaseImporter::class, $importer);
    }
}
