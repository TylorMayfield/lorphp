<?php
/**
 * Dashboard page view - Auth required
 */
$this->setLayout('base');
?>    
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 dashboard-content">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <?php $this->partial('components/stats-card', [
                'label' => 'Total Projects',
                'value' => '12',
                'bgColor' => 'bg-indigo-500'
            ]); ?>

            <?php $this->partial('components/stats-card', [
                'label' => 'Active Tasks',
                'value' => '5',
                'bgColor' => 'bg-green-500'
            ]); ?>

            <?php $this->partial('components/stats-card', [
                'label' => 'Notifications',
                'value' => '3',
                'bgColor' => 'bg-yellow-500'
            ]); ?>
        </div>        <!-- Main Content -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Activity</h3>
                <div class="mt-5">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <?php $this->partial('components/activity-item', [
                                'title' => 'Project Update',
                                'description' => 'Updated the project status',
                                'time' => '2 hours ago',
                                'link' => '#'
                            ]); ?>
                            
                            <?php $this->partial('components/activity-item', [
                                'title' => 'New Task',
                                'description' => 'Created a new task in Project X',
                                'time' => '4 hours ago',
                                'link' => '#'
                            ]); ?>
                            
                            <?php $this->partial('components/activity-item', [
                                'title' => 'Code Review',
                                'description' => 'Completed code review for feature Y',
                                'time' => 'Yesterday',
                                'link' => '#'
                            ]); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
