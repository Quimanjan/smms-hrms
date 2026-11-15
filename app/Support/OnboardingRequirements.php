<?php

namespace App\Support;

/**
 * The fixed onboarding requirements from St. Mary Magdalene School
 * (client answers, November 2026). Order and wording follow the school's list.
 * "optional" items only apply to some employees (e.g. "if married").
 */
final class OnboardingRequirements
{
    /** @return array<int, array{name: string, optional: bool}> */
    public static function all(): array
    {
        return [
            ['name' => 'Duly Accomplished SMMS Employee Information Sheet', 'optional' => false],
            ['name' => 'Latest Vaccination ID, Certificate, or Card (photocopy)', 'optional' => false],
            ['name' => 'PSA or NSO Birth Certificate (photocopy)', 'optional' => false],
            ['name' => 'Baptismal Certificate (photocopy)', 'optional' => false],
            ['name' => 'Confirmation Certificate (photocopy)', 'optional' => false],
            ['name' => 'Cedula (photocopy)', 'optional' => false],
            ['name' => 'NBI or Police Clearance', 'optional' => false],
            ['name' => 'Application Letter (if applicable)', 'optional' => true],
            ['name' => 'Comprehensive Resume with recent and colored picture', 'optional' => false],
            ['name' => 'Diploma (photocopy)', 'optional' => false],
            ['name' => 'Transcript of Record with Seal (photocopy)', 'optional' => false],
            ['name' => 'SSS ID / E-1 Form / E-4 Form (photocopy)', 'optional' => false],
            ['name' => "PhilHealth ID, PMRF, or Member's Data Form (photocopy)", 'optional' => false],
            ['name' => "PAG-IBIG ID or Member's Data Form (photocopy)", 'optional' => false],
            ['name' => 'TIN ID / BIR 1902 Form (photocopy)', 'optional' => false],
            ['name' => 'Latest PRC License, if already obtained (photocopy)', 'optional' => true],
            ['name' => 'BIR 2316 Form, if previously employed (photocopy)', 'optional' => true],
            ['name' => 'Certificate of Employment, if previously employed (photocopy)', 'optional' => true],
            ['name' => 'Service Record, if previously employed and applicable (photocopy)', 'optional' => true],
            ['name' => 'Marriage Certificate, if married (photocopy)', 'optional' => true],
            ['name' => 'Certificate of Completion, Certificate of Grades, MA/MS TOR, and/or MA/MS Diploma', 'optional' => true],
            ['name' => 'Seminar and Training Certificates (photocopy)', 'optional' => false],
            ['name' => 'Acknowledgment of Receipts and Reviews of Guidelines, Policies, and Forms', 'optional' => false],
            ['name' => 'Data Privacy Policy', 'optional' => false],
            ['name' => 'Learning Material Policy', 'optional' => false],
            ['name' => 'Social Media Policy', 'optional' => false],
            ['name' => 'Code of Conduct', 'optional' => false],
            ['name' => 'ATM Information Sheet (Google Form)', 'optional' => false],
            ['name' => 'CEAP Retirement Form 01', 'optional' => false],
        ];
    }
}
