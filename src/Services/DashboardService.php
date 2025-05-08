<?php
namespace LorPHP\Services;

use LorPHP\Models\User;

class DashboardService {
    public function getStats(User $user): array {
        return [
            'totalClients' => count($user->getOrganizationClients()),
            'activeClients' => count($user->getOrganizationClients(['status' => 'active'])),
            'recentContacts' => count($user->getOrganizationClients([
                'last_contact_date' => date('Y-m-d', strtotime('-7 days'))
            ])),
            'organizationUsers' => $user->getOrganization() ? 
                count($user->getOrganization()->getUsers()) : 0,
            'totalPackages' => count($user->getOrganizationPackages())
        ];
    }

    public function getRecentContacts(User $user, int $limit = 5): array {
        $recentContacts = [];
        foreach ($user->getOrganizationClients() as $client) {
            $contacts = $client->getContacts();
            foreach ($contacts as $contact) {
                $contact['client_name'] = $client->name;
                $recentContacts[] = $contact;
            }
        }
        
        usort($recentContacts, function($a, $b) {
            return strtotime($b['contact_date']) - strtotime($a['contact_date']);
        });
        
        return array_slice($recentContacts, 0, $limit);
    }

    public function getRecentClients(User $user, int $limit = 5): array {
        return $user->getOrganizationClients(['limit' => $limit]);
    }
}
