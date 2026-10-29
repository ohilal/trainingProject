<?php

namespace Tests\Unit;

use App\Services\LdapService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class LdapServiceTest extends TestCase
{
    public function test_it_extracts_organizational_units_from_the_distinguished_name()
    {
        if (!function_exists('ldap_explode_dn')) {
            $this->markTestSkipped('The PHP LDAP extension is not installed.');
        }

        $service = new LdapService();
        $method = new ReflectionMethod($service, 'getOrganizationalUnit');
        $method->setAccessible(true);

        $organizationalUnit = $method->invoke(
            $service,
            'CN=Example User,OU=Learners,OU=Training,DC=example,DC=local'
        );

        $this->assertSame('Training / Learners', $organizationalUnit);
    }
}
