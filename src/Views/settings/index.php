<?php
$this->layout = 'base';
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Account Settings</h2>
        
        <!-- Display IDs -->
        <div class="mb-8 p-4 bg-gray-50 rounded-md">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">User ID</p>
                    <p class="font-mono"><?php echo htmlspecialchars($user->id); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Organization ID</p>
                    <p class="font-mono"><?php echo htmlspecialchars($user->organization_id); ?></p>
                </div>
            </div>
        </div>

        <form action="/settings/update" method="POST" class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" 
                    value="<?php echo htmlspecialchars($old['name'] ?? $user->name); ?>"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['name']); ?></p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" 
                    value="<?php echo htmlspecialchars($old['email'] ?? $user->email); ?>"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['email']); ?></p>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="password" id="password" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Leave blank to keep current password">
                <?php if (isset($errors['password'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['password']); ?></p>
                <?php endif; ?>
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="password_confirm" id="password_confirm" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
