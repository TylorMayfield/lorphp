<?php
/**
 * Edit client view
 */
$this->setLayout('base');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl relative">
        <!-- Gradient background effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
        
        <div class="px-4 py-5 sm:p-6 relative">
            <h3 class="text-lg font-medium leading-6 text-[#fafafa] mb-4">Edit Client</h3>
            
            <form action="/clients/<?php echo $client->id; ?>/update" method="POST" class="space-y-6">
                <?php $this->partial('forms/input', [
                    'id' => 'name',
                    'type' => 'text',
                    'label' => 'Client Name',
                    'required' => true,
                    'value' => $client->name
                ]); ?>

                <?php $this->partial('forms/input', [
                    'id' => 'email',
                    'type' => 'email',
                    'label' => 'Email Address',
                    'value' => $client->email
                ]); ?>

                <?php $this->partial('forms/input', [
                    'id' => 'phone',
                    'type' => 'tel',
                    'label' => 'Phone Number',
                    'value' => $client->phone
                ]); ?>

                <div>
                    <label for="notes" class="block text-sm font-medium text-[#a1a1aa]">Notes</label>
                    <textarea id="notes" name="notes" rows="3" 
                        class="mt-1 block w-full rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-[#fafafa] placeholder-[#71717a]"><?php echo htmlspecialchars($client->notes ?? ''); ?></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="/clients/<?php echo $client->id; ?>" 
                       class="bg-[#27272a]/50 text-[#a1a1aa] px-4 py-2 rounded-xl text-sm border border-[#3f3f46] hover:bg-[#3f3f46]/50 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 text-white px-4 py-2 rounded-xl text-sm transition-all duration-200 hover:scale-[1.02] shadow-xl hover:shadow-2xl">
                        Update Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
