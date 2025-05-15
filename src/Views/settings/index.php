<?php
$this->layout = 'base';
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] rounded-xl shadow-xl p-6 relative">
        <!-- Gradient background effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
        
        <div class="relative">
            <h2 class="text-2xl font-bold mb-6 text-[#fafafa]">Account Settings</h2>
            
            <!-- Display IDs -->
            <div class="mb-8 p-4 bg-[#27272a]/50 rounded-xl border border-[#3f3f46]">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-[#a1a1aa]">User ID</p>
                        <p class="font-mono text-[#fafafa]"><?php echo htmlspecialchars($user->id); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-[#a1a1aa]">Organization ID</p>
                        <p class="font-mono text-[#fafafa]"><?php echo htmlspecialchars($user->organization_id); ?></p>
                    </div>
                </div>
            </div>

            <form action="/settings/update" method="POST" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#a1a1aa]">Name</label>
                    <input type="text" name="name" id="name" 
                        value="<?php echo htmlspecialchars($old['name'] ?? $user->name); ?>"
                        class="mt-1 block w-full rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-[#fafafa] placeholder-[#71717a]">
                    <?php if (isset($errors['name'])): ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo htmlspecialchars($errors['name']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#a1a1aa]">Email</label>
                    <input type="email" name="email" id="email" 
                        value="<?php echo htmlspecialchars($old['email'] ?? $user->email); ?>"
                        class="mt-1 block w-full rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-[#fafafa] placeholder-[#71717a]">
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo htmlspecialchars($errors['email']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-[#a1a1aa]">New Password</label>
                    <input type="password" name="password" id="password" 
                        class="mt-1 block w-full rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-[#fafafa] placeholder-[#71717a]"
                        placeholder="Leave blank to keep current password">
                    <?php if (isset($errors['password'])): ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo htmlspecialchars($errors['password']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-[#a1a1aa]">Confirm New Password</label>
                    <input type="password" name="password_confirm" id="password_confirm" 
                        class="mt-1 block w-full rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-sm focus:border-indigo-400 focus:ring-indigo-400 text-[#fafafa] placeholder-[#71717a]">
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-xl text-sm font-medium text-white bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 hover:from-indigo-500 hover:via-purple-500 hover:to-blue-500 transform transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#18181b] focus:ring-indigo-400">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
