<?php

namespace App\Services;

use Exception;

class LdapService
{
    protected $connection;
    protected $host;
    protected $port;
    protected $baseDn;
    protected $adminUser;
    protected $adminPass;

    public function __construct()
    {
        $this->host = env('LDAP_HOST', '10.0.0.2');
        $this->port = env('LDAP_PORT', 389);
        $this->baseDn = env('LDAP_BASE_DN', 'dc=ida,dc=local');
        $this->adminUser = env('LDAP_USERNAME');
        $this->adminPass = env('LDAP_PASSWORD');
    }

    /**
     * Authenticate a user against LDAP.
     * Returns user details array if successful, null otherwise.
     */
    public function authenticate(string $username, string $password): ?array
    {
        // Connect to LDAP server
        $this->connection = @ldap_connect($this->host, $this->port);

        if (!$this->connection) {
            throw new Exception("Could not connect to LDAP server.");
        }

        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);

        try {
            // Bind with admin credentials to search for the user
            if (!@ldap_bind($this->connection, $this->adminUser, $this->adminPass)) {
                throw new Exception("Could not bind with admin credentials.");
            }

            $escapedUsername = ldap_escape($username, '', LDAP_ESCAPE_FILTER);
            $filter = sprintf(
                '(|(sAMAccountName=%1$s)(userPrincipalName=%1$s)(mail=%1$s)(uid=%1$s))',
                $escapedUsername
            );
            
            $result = ldap_search($this->connection, $this->baseDn, $filter);
            
            if ($result === false) {
                return null; // User not found
            }

            $entries = ldap_get_entries($this->connection, $result);

            if ($entries['count'] === 0) {
                return null; // User not found
            }

            $userEntry = $entries[0];
            $userDn = $userEntry['dn'];

            if (!@ldap_bind($this->connection, $userDn, $password)) {
                return null; // Invalid password
            }

            // Success! Return relevant data
            return [
                'username' => $username,
                'ldap_id' => $userEntry['objectguid'][0] ?? $userEntry['entryuuid'][0] ?? $userEntry['dn'],
                'guid' => $userEntry['objectguid'][0] ?? null,
                'email' => $userEntry['mail'][0] ?? null,
                'full_name' => $userEntry['displayname'][0] ?? $userEntry['cn'][0] ?? $username,
            ];

        } catch (Exception $e) {
            // Log error if needed
            \Log::error('LDAP Error: ' . $e->getMessage());
            return null;
        } finally {
            if ($this->connection) {
                @ldap_unbind($this->connection);
            }
        }
    }
}