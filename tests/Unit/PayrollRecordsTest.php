<?php

use App\Models\PayrollRecords;

it('does not expose undertime minutes in payroll row output', function () {
    $record = new PayrollRecords;
    $record->forceFill(['undertime_minutes' => 60]);

    expect($record->toRow())->not->toHaveKey('undertime_minutes');
});
