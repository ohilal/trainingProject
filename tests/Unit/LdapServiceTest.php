<?php

namespace Tests\Unit;

use App\Services\LdapService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class LdapServiceTest extends TestCase
{
    public function test_it_extracts_organizational_units_from_the_user_distinguished_name()
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

    public function test_it_decodes_escaped_arabic_in_organizational_units()
    {
        if (!function_exists('ldap_explode_dn')) {
            $this->markTestSkipped('The PHP LDAP extension is not installed.');
        }

        $service = new LdapService();
        $method = new ReflectionMethod($service, 'getOrganizationalUnit');
        $method->setAccessible(true);

        $organizationalUnit = $method->invoke(
            $service,
            'CN=Example User,OU=\\D8\\A7\\D9\\84\\D8\\A5\\D8\\AF\\D8\\A7\\D8\\B1\\D8\\A9,OU=IDA Main Building,DC=example,DC=local'
        );

        $this->assertSame('IDA Main Building / الإدارة', $organizationalUnit);
    }

    public function test_it_returns_null_when_user_distinguished_name_has_no_organizational_unit()
    {
        if (!function_exists('ldap_explode_dn')) {
            $this->markTestSkipped('The PHP LDAP extension is not installed.');
        }

        $service = new LdapService();
        $method = new ReflectionMethod($service, 'getOrganizationalUnit');
        $method->setAccessible(true);

        $this->assertNull($method->invoke(
            $service,
            'CN=Example User,DC=example,DC=local'
        ));
    }
}
