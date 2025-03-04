<?php

declare(strict_types=1);

namespace tests\models;

use Codeception\Test\Unit;
use Yii;
use app\models\BattleImage;

class BattleImageTest extends Unit
{
    public function testGenerateFilename(): void
    {
        $generated = [];
        for ($i = 0; $i < 5; ++$i) {
            $value = BattleImage::generateFilename(false);
            $this->assertIsString($value);
            $this->assertFalse(in_array($value, $generated));
            $this->assertEquals(1, preg_match(
                '#^[a-z2-7]{2}/[a-z2-7]{26}\.jpg$#',
                $value
            ));

            $parts = explode('/', $value);
            $this->assertEquals($parts[0], substr($parts[1], 0, 2));

            $generated[] = $value;
        }
    }
}
