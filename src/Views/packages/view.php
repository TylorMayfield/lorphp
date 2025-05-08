<?php
/**
 * Package detail view
 */
$this->setLayout('base');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Package Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-medium text-gray-900"><?php echo htmlspecialchars($package->name); ?></h3>
                            <div class="mt-1 text-sm text-gray-500">
                                <div class="mb-2">
                                    <span class="font-medium">Price:</span> $<?php echo number_format($package->price, 2); ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="/packages/<?php echo $package->id; ?>/edit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                                Edit Package
                            </a>
                            <form action="/packages/<?php echo $package->id; ?>/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this package?');" class="inline">
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">
                                    Delete Package
                                </button>
                            </form>
                        </div>
                    </div>

                    <?php if ($package->description): ?>
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900">Description</h4>
                            <div class="mt-1 text-sm text-gray-600">
                                <?php echo nl2br(htmlspecialchars($package->description)); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Assigned Clients -->
            <div class="mt-8 bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Assigned Clients</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client Name</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($client->name); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($client->email): ?>
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($client->email); ?></div>
                                        <?php endif; ?>
                                        <?php if ($client->phone): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($client->phone); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="/clients/<?php echo $client->id; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <form action="/packages/<?php echo $package->id; ?>/clients/<?php echo $client->id; ?>/remove" method="POST" class="inline">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Client Form -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Assign to Client</h3>
                    <form action="/packages/<?php echo $package->id; ?>/assign" method="POST" class="space-y-4">
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700">Select Client</label>
                            <select id="client_id" name="client_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select a client...</option>
                                <?php 
                                $organization = $this->user->getOrganization();
                                $allClients = $organization->getClients();
                                foreach ($allClients as $client):
                                    // Skip if client already has this package
                                    if ($client->hasPackage($package->id)) continue;
                                ?>
                                    <option value="<?php echo $client->id; ?>"><?php echo htmlspecialchars($client->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                                Assign Package
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
