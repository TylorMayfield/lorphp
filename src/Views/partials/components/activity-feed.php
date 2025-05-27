<?php
/**
 * Activity feed component for dashboard
 */
?>
<div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl relative">
    <!-- Gradient background effect -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
    
    <div class="px-4 py-5 sm:p-6 relative">
        <h3 class="text-lg font-medium text-[#fafafa] mb-4">Recent Activity</h3>
        
        <?php if (empty($contacts)): ?>
            <p class="text-[#a1a1aa]">No recent activity to display.</p>
        <?php else: ?>
            <div class="flow-root">
                <ul class="-mb-8">
                    <?php foreach ($contacts as $index => $contact): ?>
                        <li>
                            <div class="relative pb-8">
                                <?php if ($index !== count($contacts) - 1): ?>
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-[#3f3f46]" aria-hidden="true"></span>
                                <?php endif; ?>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-400 via-purple-400 to-blue-400 flex items-center justify-center ring-8 ring-[#18181b]">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="text-sm text-[#fafafa]">
                                                <a href="/clients/<?php echo $contact->client_id; ?>" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                                                    <?php echo htmlspecialchars($contact->client_name); ?>
                                                </a>
                                            </div>
                                            <p class="mt-1 text-sm text-[#a1a1aa]">
                                                <?php echo htmlspecialchars($contact->description); ?>
                                            </p>
                                            <div class="mt-2 text-sm text-[#71717a]">
                                                <?php echo $contact->created_at ? date('M j, Y g:i A', strtotime($contact->created_at)) : 'N/A'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
