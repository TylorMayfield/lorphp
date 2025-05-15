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
            <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl relative">
                <!-- Gradient background effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
                
                <div class="px-4 py-5 sm:p-6 relative">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-medium text-[#fafafa]"><?php echo htmlspecialchars($client->name); ?></h3>
                            <div class="mt-1 text-sm text-[#a1a1aa]">
                                <?php if ($client->email): ?>
                                    <div class="mb-1">
                                        <span class="font-medium text-[#fafafa]">Email:</span> <?php echo htmlspecialchars($client->email); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($client->phone): ?>
                                    <div>
                                        <span class="font-medium text-[#fafafa]">Phone:</span> <?php echo htmlspecialchars($client->phone); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="/clients/<?php echo $client->id; ?>/edit" 
                               class="bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl">
                                Edit Client
                            </a>
                            <form action="/clients/<?php echo $client->id; ?>/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');" class="inline">
                                <button type="submit" 
                                        class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-2 rounded-xl text-sm hover:bg-red-500/20 transition-colors duration-200">
                                    Delete Client
                                </button>
                            </form>
                        </div>
                    </div>

                    <?php if ($client->notes): ?>
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-[#fafafa]">Notes</h4>
                            <div class="mt-1 text-sm text-[#a1a1aa]">
                                <?php echo nl2br(htmlspecialchars($client->notes)); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact History -->
            <div class="mt-8 bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl relative">
                <!-- Gradient background effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
                
                <div class="px-4 py-5 sm:p-6 relative">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-[#fafafa]">Contact History</h3>
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
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-[#3f3f46]" aria-hidden="true"></span>
                                    <?php endif; ?>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-400 via-purple-400 to-blue-400 flex items-center justify-center ring-8 ring-[#18181b] shadow-md">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <span class="font-medium text-[#fafafa]"><?php echo htmlspecialchars($contact['user_name']); ?></span>
                                                </div>
                                                <p class="mt-0.5 text-sm text-[#71717a]">
                                                    <?php echo date('F j, Y g:i a', strtotime($contact['contact_date'])); ?>
                                                </p>
                                            </div>
                                            <div class="mt-2 text-sm text-[#a1a1aa]">
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
            <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl relative">
                <!-- Gradient background effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
                
                <div class="px-4 py-5 sm:p-6 relative">
                    <h3 class="text-lg font-medium text-[#fafafa] mb-4">Add Contact</h3>
                    <form action="/clients/<?php echo $client->id; ?>/contacts" method="POST" class="space-y-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-[#a1a1aa]">Contact Type</label>
                            <select id="type" name="type" 
                                class="mt-1 block w-full rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-[#fafafa]">
                                <option value="phone" class="bg-[#18181b]">Phone Call</option>
                                <option value="email" class="bg-[#18181b]">Email</option>
                                <option value="meeting" class="bg-[#18181b]">Meeting</option>
                                <option value="other" class="bg-[#18181b]">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="contact_notes" class="block text-sm font-medium text-[#a1a1aa]">Notes</label>
                            <textarea id="contact_notes" name="notes" rows="4" 
                                class="mt-1 block w-full rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-[#fafafa] placeholder-[#71717a]"
                                placeholder="What was discussed?"></textarea>
                        </div>

                        <div>
                            <button type="submit" 
                                class="w-full bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl">
                                Log Contact
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
