<?php
/**
 * Register form partial
 */

if (!isset($form)) {
    $form = \LorPHP\Core\FormBuilder::createRegistrationForm();
}

echo $form->render();
