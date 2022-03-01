[![Build Status](https://travis-ci.com/DiscipleTools/disciple-tools-dashboard.svg?branch=master)](https://travis-ci.com/DiscipleTools/disciple-tools-dashboard)

![Plugin Banner](https://raw.githubusercontent.com/DiscipleTools/disciple-tools-dashboard/master/dashboard-banner.png)
# Disciple.Tools - Dashboard

This plugin adds a beautiful start page to assist disciple makers in knowing what are the most important actions to
take (New Contacts, Contacts Needing Update, etc).

## Purpose

In an access ministry, where you have a large amount of incoming contacts that need follow-up this starting dashboard
helps to clarify from the moment that the disciple make signs in to address the most urgent issues.

It quickly helps you answer:

1. Do I have any new contacts assigned to me?
2. Do I have any contacts that need follow-up?
3. What tasks do I have outstanding?
4. How is my pace and progress?

## Usage

#### Will Do

- Quick access to number of contacts, newly assigned contacts, and contacts needing updates.
- Quick access to availability for more contact assignments
- Quick access to tasks.
- Quick access to key metrics for faith milestones, personal benchmarks, and seeker progress.

#### Will Not Do

- Does not do direct editing. It only surfaces the key items for focus.

## Requirements

- Disciple.Tools Theme installed on a Wordpress Server

## Installing

- Install as a standard Disciple.Tools/Wordpress plugin in the system Admin/Plugins area.
- Requires the user role of Administrator.

## Custom cards

Cards can be registered by using the `dt_dashboard_register_card` function.

```php
dt_dashboard_register_card(
    'Your_Custom_Card',                     //handle
    __('Custom Card Label', 'your-plugin'), //label
    function() {                            //Register any assets the card needs or do anything else needed on registration.
        wp_enqueue_script( $this->handle, 'path-to-your-cards-script.js', [], null, true);
    },
    function() {                            //Render the card
        get_template_part( 'whatever-slug', 'whatever-file', [
            'handle' => $this->handle,
            'label' => $this->label,
            'card' => $this
        ]);
    }
);

```
More complex custom cards can be creating by extending `DT_Dashboard_Plugin_Card`.

Here's an example:

```php
/**
* Your custom card class
 */
class Your_Custom_Card extends DT_Dashboard_Card
{

    /**
     * Register any assets the card needs or do anything else needed on registration.
     * @return mixed
     */
    public function setup() {
        wp_enqueue_script( $this->handle, 'path-t0-your-cards-script.js', [], null, true);
    }

    /**
     * Render the card
     */
    public function render() {
        get_template_part( 'whatever-slug', 'whatever-file', [
            'handle' => $this->handle,
            'label' => $this->label,
            'card' => $this
        ]);
    }
}

/**
* Next, register our class. This can be done in the after_setup_theme hook.
*/
DT_Dashboard_Plugin_Cards::instance()->register(
    new Your_Custom_Card(
        'Your_Custom_Card',                     //handle
        __('Custom Card Label', 'your-plugin'), //label
         [
            'priority' => 1,
            'span' => 1
         ]
    ));
```

### Hooks

The `dt_dashboard_cards` filter can be used to deregister cards, or to add new cards without using `DT_Dashboard_Plugin_Cards::instance()->register`.

## Contribution

Contributions welcome. You can report issues and bugs in the
[Issues](https://github.com/DiscipleTools/disciple-tools-dashboard/issues) section of the repo. You can present ideas in
the [Discussions](https://github.com/DiscipleTools/disciple-tools-dashboard/discussions) section of the repo. And code
contributions are welcome using the [Pull Request](https://github.com/DiscipleTools/disciple-tools-dashboard/pulls)
system for git. For a more details on contribution see the
[contribution guidelines](https://github.com/DiscipleTools/disciple-tools-dashboard/blob/master/CONTRIBUTING.md).

## Screenshots

![screenshot.png](https://raw.githubusercontent.com/DiscipleTools/disciple-tools-dashboard/master/screenshot.png)
