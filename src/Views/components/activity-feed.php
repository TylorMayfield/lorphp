<?php
/**
 * Activity Feed Component
 * Displays a timeline of recent activity
 */
?>
<div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl p-4 sm:p-6 lg:p-8 relative h-full">
    <!-- Gradient background effect -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50 rounded-xl"></div>
    
    <div class="relative">
        <h3 class="text-lg font-medium text-[#fafafa] mb-6">Recent Activity</h3>
        <div class="flow-root">
            <ul class="-mb-8">                <?php foreach ($contacts as $contact): ?>
                    <?php echo $this->ui()->activityItem([
                        'title' => 'Contact Update',
                        'description' => "Contact with {$contact['client_name']} - {$contact['notes']}",
                        'time' => date('M j, Y', strtotime($contact['contact_date'])),
                        'link' => "/clients/{$contact['id']}"
                    ])->withSlot('icon', '<svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>'); ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
