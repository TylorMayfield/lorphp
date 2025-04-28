<?php
/**
 * Dashboard page view - Auth required
 */
$this->setLayout('base');
?>    
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 dashboard-content">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php 
            $totalClients = count($user->getOrganizationClients());
            $activeClients = count($user->getOrganizationClients(['status' => 'active']));
            $recentContacts = count($user->getOrganizationClients(['last_contact_date' => date('Y-m-d', strtotime('-7 days'))]));
            ?>
            
            <?php $this->partial('components/stats-card', [
                'label' => 'Total Clients',
                'value' => $totalClients,
                'bgColor' => 'bg-indigo-500'
            ]); ?>

            <?php $this->partial('components/stats-card', [
                'label' => 'Active Clients',
                'value' => $activeClients,
                'bgColor' => 'bg-green-500'
            ]); ?>

            <?php $this->partial('components/stats-card', [
                'label' => 'Recent Contacts',
                'value' => $recentContacts,
                'bgColor' => 'bg-blue-500'
            ]); ?>

            <?php $this->partial('components/stats-card', [
                'label' => 'Organization Users',
                'value' => count($user->getOrganization()->getUsers()),
                'bgColor' => 'bg-purple-500'
            ]); ?>
        </div>        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Clients -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Clients</h3>
                        <a href="/clients/create" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">Add Client</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($user->getOrganizationClients(['limit' => 5]) as $client): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($client->name); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($client->email); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $client->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo ucfirst($client->status); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $client->last_contact_date ? date('M j, Y', strtotime($client->last_contact_date)) : 'Never'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="/clients/<?php echo $client->id; ?>" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <?php 
                            $recentContacts = [];
                            foreach ($user->getOrganizationClients() as $client) {
                                $contacts = $client->getContacts();
                                foreach ($contacts as $contact) {
                                    $contact['client_name'] = $client->name;
                                    $recentContacts[] = $contact;
                                }
                            }
                            
                            // Sort by contact date
                            usort($recentContacts, function($a, $b) {
                                return strtotime($b['contact_date']) - strtotime($a['contact_date']);
                            });
                            
                            // Show only last 5 contacts
                            $recentContacts = array_slice($recentContacts, 0, 5);
                            
                            foreach ($recentContacts as $contact):
                            ?>
                                <?php $this->partial('components/activity-item', [
                                    'title' => $contact['user_name'],
                                    'description' => "Contacted {$contact['client_name']} - {$contact['type']}",
                                    'time' => date('M j, Y', strtotime($contact['contact_date'])),
                                    'link' => "/clients/{$contact['client_id']}"
                                ]); ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
