<?php

namespace Tests\Unit;

use App\Services\EligibilityService;
use PHPUnit\Framework\TestCase;

class EligibilityServiceTest extends TestCase
{
    private EligibilityService $service;

    protected function setUp(): void
    {
        $this->service = new EligibilityService();
    }

    public function testItPassesWhenAllRequiredSubjectsHaveCredit(): void
    {
        $results = [
            ['subject' => 'Mathematics', 'grade' => 'A1'],
            ['subject' => 'English Language', 'grade' => 'B3'],
            ['subject' => 'Physics', 'grade' => 'C4'],
            ['subject' => 'Chemistry', 'grade' => 'B2'],
            ['subject' => 'Biology', 'grade' => 'C6'],
        ];
        $required = ['Mathematics', 'English Language', 'Physics', 'Chemistry', 'Biology'];

        $check = $this->service->check($results, $required, 5);

        $this->assertTrue($check['passed']);
        $this->assertEquals(5, $check['credits_obtained']);
        $this->assertEmpty($check['missing_subjects']);
    }

    public function testItFailsWhenAMandatorySubjectIsMissing(): void
    {
        $results = [
            ['subject' => 'Mathematics', 'grade' => 'A1'],
            ['subject' => 'English Language', 'grade' => 'B3'],
            ['subject' => 'Chemistry', 'grade' => 'B2'],
            ['subject' => 'Biology', 'grade' => 'C6'],
            ['subject' => 'Geography', 'grade' => 'A1'], // Geography is not Physics
        ];
        $required = ['Mathematics', 'English Language', 'Physics', 'Chemistry', 'Biology'];

        $check = $this->service->check($results, $required, 5);

        $this->assertFalse($check['passed']);
        $this->assertEquals(4, $check['credits_obtained']);
        $this->assertContains('Physics', $check['missing_subjects']);
    }

    public function testItFailsWhenAMandatorySubjectHasPoorGrade(): void
    {
        $results = [
            ['subject' => 'Mathematics', 'grade' => 'A1'],
            ['subject' => 'English Language', 'grade' => 'B3'],
            ['subject' => 'Physics', 'grade' => 'D7'], // D7 is not a credit
            ['subject' => 'Chemistry', 'grade' => 'B2'],
            ['subject' => 'Biology', 'grade' => 'C6'],
        ];
        $required = ['Mathematics', 'English Language', 'Physics', 'Chemistry', 'Biology'];

        $check = $this->service->check($results, $required, 5);

        $this->assertFalse($check['passed']);
        $this->assertContains('Physics', $check['missing_subjects']);
    }

    public function testItMatchesSubjectAliasesCorrectly(): void
    {
        $results = [
            ['subject' => 'Maths', 'grade' => 'A1'], // Alias for Mathematics
            ['subject' => 'Use of English', 'grade' => 'B3'], // Alias for English Language
            ['subject' => 'Phy', 'grade' => 'C4'], // Alias for Physics
            ['subject' => 'Chem', 'grade' => 'C5'], // Alias for Chemistry
            ['subject' => 'Bio', 'grade' => 'C6'], // Alias for Biology
        ];
        $required = ['Mathematics', 'English Language', 'Physics', 'Chemistry', 'Biology'];

        $check = $this->service->check($results, $required, 5);

        $this->assertTrue($check['passed']);
        $this->assertEmpty($check['missing_subjects']);
    }

    public function testItFailsWhenTotalCreditsAreBelowMinimum(): void
    {
        $results = [
            ['subject' => 'Mathematics', 'grade' => 'A1'],
            ['subject' => 'English Language', 'grade' => 'A1'],
            ['subject' => 'Physics', 'grade' => 'A1'],
            ['subject' => 'Chemistry', 'grade' => 'A1'],
        ];
        $required = ['Mathematics', 'English Language', 'Physics', 'Chemistry', 'Biology'];

        $check = $this->service->check($results, $required, 5);

        $this->assertFalse($check['passed']);
        $this->assertEquals(4, $check['credits_obtained']);
    }

    public function testItReturnsCorrectMissingSubjectsList(): void
    {
        $results = [
            ['subject' => 'Mathematics', 'grade' => 'F9'],
            ['subject' => 'English Language', 'grade' => 'E8'],
        ];
        $required = ['Mathematics', 'English Language', 'Physics'];

        $check = $this->service->check($results, $required, 3);

        $this->assertFalse($check['passed']);
        $this->assertCount(3, $check['missing_subjects']);
        $this->assertContains('Physics', $check['missing_subjects']);
        $this->assertContains('Mathematics', $check['missing_subjects']);
        $this->assertContains('English Language', $check['missing_subjects']);
    }

    public function testItIsCaseInsensitive(): void
    {
        $results = [
            ['subject' => 'mAtHeMaTiCs', 'grade' => 'a1'], // Lowercase grade and mixed case subject
            ['subject' => 'ENGLISH LANGUAGE', 'grade' => 'b3'],
        ];
        $required = ['mathematics', 'english language'];

        $check = $this->service->check($results, $required, 2);

        $this->assertTrue($check['passed']);
    }

    public function testItHandlesWhitespaceInSubjectNames(): void
    {
        $results = [
            ['subject' => '  Mathematics  ', 'grade' => 'A1 '],
            ['subject' => ' English Language', 'grade' => ' B3'],
        ];
        $required = ['Mathematics', 'English Language'];

        $check = $this->service->check($results, $required, 2);

        $this->assertTrue($check['passed']);
    }

    public function testItPassesWithExactMinimumCredits(): void
    {
        $results = [
            ['subject' => 'Mathematics', 'grade' => 'C6'],
            ['subject' => 'English Language', 'grade' => 'C6'],
        ];
        $required = ['Mathematics', 'English Language'];

        $check = $this->service->check($results, $required, 2);

        $this->assertTrue($check['passed']);
        $this->assertEquals(2, $check['credits_obtained']);
    }

    public function testItHandlesEmptyOLevelResults(): void
    {
        $results = [];
        $required = ['Mathematics', 'English Language'];

        $check = $this->service->check($results, $required, 2);

        $this->assertFalse($check['passed']);
        $this->assertEquals(0, $check['credits_obtained']);
        $this->assertCount(2, $check['missing_subjects']);
    }

    public function testItHandlesEmptyRequiredSubjects(): void
    {
        $results = [
            ['subject' => 'Mathematics', 'grade' => 'A1'],
        ];
        $required = [];

        $check = $this->service->check($results, $required, 0);

        $this->assertTrue($check['passed']);
        $this->assertEquals(0, $check['credits_obtained']);
        $this->assertEmpty($check['missing_subjects']);
    }

    public function testItDetectsMultipleMissingSubjects(): void
    {
        $results = [
            ['subject' => 'Mathematics', 'grade' => 'A1'],
        ];
        $required = ['Mathematics', 'English Language', 'Physics', 'Chemistry'];

        $check = $this->service->check($results, $required, 4);

        $this->assertFalse($check['passed']);
        $this->assertCount(3, $check['missing_subjects']);
    }
}
