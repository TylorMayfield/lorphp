<?php
namespace LorPHP\Components\Layout;

use LorPHP\Core\Component;

class AuthContainer extends Component {
    protected function template(): void {
        $title = $this->attr('title');
        $subtitle = $this->attr('subtitle');
        $linkText = $this->attr('linkText');
        $linkUrl = $this->attr('linkUrl');
        ?>
        <div class="min-h-screen flex items-center justify-center">
            <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-xl shadow-lg auth-container">
                <div>
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900"><?php echo $title; ?></h2>
                    <?php if ($subtitle): ?>
                        <p class="mt-2 text-center text-sm text-gray-600">
                            <?php echo $subtitle; ?>
                            <?php if ($linkText && $linkUrl): ?>
                                <a href="<?php echo $linkUrl; ?>" class="font-medium text-indigo-600 hover:text-indigo-500">
                                    <?php echo $linkText; ?>
                                </a>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php echo $this->slot('default'); ?>
            </div>
        </div>
        <?php
    }
}
