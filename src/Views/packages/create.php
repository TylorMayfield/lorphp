<?php
/**
 * Create package view
 */
$this->setLayout('base');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">New Package</h3>
            
            <form action="/packages" method="POST" class="space-y-6">
                <?php $this->partial('forms/input', [
                    'id' => 'name',
                    'type' => 'text',
                    'label' => 'Package Name',
                    'required' => true
                ]); ?>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>

                <?php $this->partial('forms/input', [
                    'id' => 'price',
                    'type' => 'number',
                    'label' => 'Price',
                    'required' => true,
                    'step' => '0.01',
                    'min' => '0'
                ]); ?>

                <div class="flex justify-end space-x-3">
                    <a href="/packages" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md text-sm hover:bg-gray-200">
                        Cancel
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                        Create Package
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
