<?php
/**
 * Client detail view
 */
$this->setLayout('base');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Client Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-medium text-gray-900"><?php echo htmlspecialchars($client->name); ?></h3>
                            <div class="mt-1 text-sm text-gray-500">
                                <?php if ($client->email): ?>
                                    <div class="mb-1">
                                        <span class="font-medium">Email:</span> <?php echo htmlspecialchars($client->email); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($client->phone): ?>
                                    <div>
                                        <span class="font-medium">Phone:</span> <?php echo htmlspecialchars($client->phone); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="/clients/<?php echo $client->id; ?>/edit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                                Edit Client
                            </a>
                            <form action="/clients/<?php echo $client->id; ?>/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');" class="inline">
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">
                                    Delete Client
                                </button>
                            </form>
                        </div>
                    </div>

                    <?php if ($client->notes): ?>
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-900">Notes</h4>
                            <div class="mt-1 text-sm text-gray-600">
                                <?php echo nl2br(htmlspecialchars($client->notes)); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact History -->
            <div class="mt-8 bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Contact History</h3>
                    </div>
                    
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <?php 
                            $totalContacts = count($contacts);
                            foreach ($contacts as $index => $contact): 
                                $isLast = ($index === $totalContacts - 1);
                            ?>
                                <li class="relative pb-8">
                                    <?php if (!$isLast): ?>
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <?php endif; ?>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($contact['user_name']); ?></span>
                                                </div>
                                                <p class="mt-0.5 text-sm text-gray-500">
                                                    <?php echo date('F j, Y g:i a', strtotime($contact['contact_date'])); ?>
                                                </p>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700">
                                                <p><?php echo htmlspecialchars($contact['notes']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Contact Form -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add Contact</h3>
                    <form action="/clients/<?php echo $client->id; ?>/contacts" method="POST" class="space-y-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Contact Type</label>
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="phone">Phone Call</option>
                                <option value="email">Email</option>
                                <option value="meeting">Meeting</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="contact_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea id="contact_notes" name="notes" rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="What was discussed?"></textarea>
                        </div>

                        <div>
                            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                                Log Contact
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
