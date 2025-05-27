<?php
namespace LorPHP\Services;

use LorPHP\Models\User;
use LorPHP\Core\Database;

class DashboardService {
    public function getStats(User $user): array {
        $organization = $user->getOrganization();
        if (!$organization) {
            return [
                'totalClients' => 0,
                'activeClients' => 0,
                'recentContacts' => 0,
                'organizationUsers' => 0,
                'totalPackages' => 0
            ];
        }

        $clients = $organization->clients();
        $activeClients = array_filter($clients, fn($client) => $client->is_active);
        $recentContacts = array_filter($clients, function($client) {
            $lastContact = $client->last_contact_date ?? null;
            if (!$lastContact) return false;
            return strtotime($lastContact) >= strtotime('-7 days');
        });

        return [
            'totalClients' => count($clients),
            'activeClients' => count($activeClients),
            'recentContacts' => count($recentContacts),
            'organizationUsers' => count($organization->users() ?? []),
            'totalPackages' => count($organization->packages() ?? [])
        ];
    }

    public function getRecentContacts(User $user, int $limit = 5): array {
        $recentContacts = [];
        $organization = $user->getOrganization();
        if (!$organization) {
            return [];
        }

        foreach ($organization->clients() as $client) {
            foreach ($client->contacts() as $contact) {
                $recentContacts[] = [
                    'id' => $contact->getId(),
                    'contact_date' => $contact->created_at,
                    'client_name' => $client->getName(),
                    'notes' => $contact->notes ?? ''
                ];
            }
        }
        
        usort($recentContacts, function($a, $b) {
            return strtotime($b['contact_date']) - strtotime($a['contact_date']);
        });
        
        return array_slice($recentContacts, 0, $limit);
    }

    public function getRecentClients(User $user, int $limit = 5): array {
        $organization = $user->getOrganization();
        if (!$organization) {
            return [];
        }
        
        $clients = $organization->clients();
        usort($clients, function($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });
        
        return array_slice($clients, 0, $limit);
    }

    public function getRecentPackages(User $user, int $limit = 5): array {
        $organization = $user->getOrganization();
        if (!$organization) {
            return [];
        }

        $db = Database::getInstance();
        return $db->query(
            "SELECT * FROM packages WHERE organization_id = ? ORDER BY created_at DESC LIMIT ?",
            [$organization->id, $limit]
        )->fetchAll(\PDO::FETCH_CLASS, 'LorPHP\Models\Package');
    }
}
