<?php
/**
 * Client index view
 */
$this->setLayout('base');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Search and filters -->
    <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl p-4 mb-6 relative">
        <!-- Gradient background effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 relative">
            <div>
                <label for="search" class="block text-sm font-medium text-[#a1a1aa]">Search</label>
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                    class="mt-1 block w-full rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-[#fafafa] placeholder-[#71717a]">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-[#a1a1aa]">Status</label>
                <select name="status" id="status" 
                    class="mt-1 block w-full rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-[#fafafa]">
                    <option value="" class="bg-[#18181b]">All</option>
                    <option value="active" <?php echo ($status ?? '') === 'active' ? 'selected' : ''; ?> class="bg-[#18181b]">Active</option>
                    <option value="inactive" <?php echo ($status ?? '') === 'inactive' ? 'selected' : ''; ?> class="bg-[#18181b]">Inactive</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" 
                    class="bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl">
                    Filter Results
                </button>
            </div>
        </form>
    </div>

    <!-- Client List -->
    <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl relative">
        <!-- Gradient background effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
        
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center relative">
            <h3 class="text-lg font-medium leading-6 text-[#fafafa]">Clients</h3>
            <a href="/clients/create" 
               class="bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl">
                Add New Client
            </a>
        </div>
        <div class="overflow-x-auto relative">
            <table class="min-w-full divide-y divide-[#3f3f46]">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-[#27272a]/50 text-left text-xs font-medium text-[#a1a1aa] uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 bg-[#27272a]/50 text-left text-xs font-medium text-[#a1a1aa] uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 bg-[#27272a]/50 text-left text-xs font-medium text-[#a1a1aa] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 bg-[#27272a]/50 text-left text-xs font-medium text-[#a1a1aa] uppercase tracking-wider">Last Contact</th>
                        <th class="px-6 py-3 bg-[#27272a]/50 text-right text-xs font-medium text-[#a1a1aa] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#3f3f46]">
                    <?php foreach ($clients as $client): ?>
                    <tr class="bg-[#27272a]/20 hover:bg-[#27272a]/40 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-[#fafafa]"><?php echo htmlspecialchars($client->name); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-[#fafafa]"><?php echo htmlspecialchars($client->email); ?></div>
                            <div class="text-sm text-[#71717a]"><?php echo htmlspecialchars($client->phone); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $client->status === 'active' ? 'bg-green-400/10 text-green-400 border border-green-400/20' : 'bg-[#3f3f46]/50 text-[#a1a1aa] border border-[#52525b]'; ?>">
                                <?php echo ucfirst($client->status); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#71717a]">
                            <?php echo $client->last_contact_date ? date('M j, Y', strtotime($client->last_contact_date)) : 'Never'; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/clients/<?php echo $client->id; ?>" class="text-indigo-400 hover:text-indigo-300 transition-colors mr-3">View</a>
                            <a href="/clients/<?php echo $client->id; ?>/edit" class="text-indigo-400 hover:text-indigo-300 transition-colors">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
