<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agency;
use Tests\DatabaseTestCase;

final class AgencyWriteTest extends DatabaseTestCase
{
    public function testCreateAgencyPersistsExpectedRow(): void
    {
        $agencyModel = new Agency($this->pdo);

        $agencyId = $agencyModel->create('Grenoble');
        $agency = $this->fetchAgency($agencyId);

        $this->assertNotNull($agency);
        $this->assertSame('Grenoble', (string) $agency['nom']);
        $this->assertSame(6, $this->countRows('agences'));
    }

    public function testUpdateAgencyChangesName(): void
    {
        $agencyModel = new Agency($this->pdo);

        $updated = $agencyModel->update(1, 'Paris Centre');
        $agency = $this->fetchAgency(1);

        $this->assertTrue($updated);
        $this->assertNotNull($agency);
        $this->assertSame('Paris Centre', (string) $agency['nom']);
    }

    public function testDeleteAgencyRemovesUnusedAgency(): void
    {
        $agencyModel = new Agency($this->pdo);
        $agencyId = $agencyModel->create('Grenoble');

        $deleted = $agencyModel->delete($agencyId);

        $this->assertTrue($deleted);
        $this->assertNull($this->fetchAgency($agencyId));
        $this->assertSame(5, $this->countRows('agences'));
    }
}