<?php
namespace LorPHP\Core;

use LorPHP\Components\UI\StatsCard;
use LorPHP\Components\UI\ActivityItem;
use LorPHP\Components\UI\Alert;
use LorPHP\Components\UI\Card;
use LorPHP\Components\UI\FeatureCard;
use LorPHP\Components\UI\Toast;
use LorPHP\Components\UI\Table;
use LorPHP\Components\UI\Badge;
use LorPHP\Components\Form\Input;
use LorPHP\Components\Layout\AuthContainer;
use LorPHP\Components\Layout\Container;
use LorPHP\Components\Navigation\NavLink;

/**
 * UI Component Facade
 * Provides a clean interface for creating UI components
 */
class UI {
    /**
     * Create a stats card component
     */
    public static function statsCard(): StatsCard {
        return StatsCard::make();
    }

    /**
     * Create an activity item component
     */
    public static function activityItem(array $attributes = []): ActivityItem {
        return ActivityItem::make($attributes);
    }

    /**
     * Create an alert component
     */
    public static function alert(array $attributes = []): Alert {
        return Alert::make($attributes);
    }

    /**
     * Create a card component
     */
    public static function card(array $attributes = []): Card {
        return Card::make($attributes);
    }

    /**
     * Create a feature card component
     */
    public static function featureCard(array $attributes = []): FeatureCard {
        return FeatureCard::make($attributes);
    }

    /**
     * Create a toast component
     */
    public static function toast(array $attributes = []): Toast {
        return Toast::make($attributes);
    }

    /**
     * Create an input component
     */
    public static function input(array $attributes = []): Input {
        return Input::make($attributes);
    }

    /**
     * Create an auth container component
     */
    public static function authContainer(array $attributes = []): AuthContainer {
        return AuthContainer::make($attributes);
    }

    /**
     * Create a container component
     */
    public static function container(array $attributes = []): Container {
        return Container::make($attributes);
    }

    /**
     * Create a nav link component
     */
    public static function navLink(array $attributes = []): NavLink {
        return NavLink::make($attributes);
    }

    /**
     * Create a table component
     */
    public static function table(array $attributes = []): Table {
        return Table::make($attributes);
    }

    /**
     * Create a badge component
     */
    public static function badge(array $attributes = []): Badge {
        return Badge::make($attributes);
    }
}
