# Wordpress Functionality Plugin
By Gifford Nowland <hi[at]giffordnowland.com>

Facilitates the addition of custom functionality to a WordPress website, including Custom Post Types, Meta Fields, Widgets, Taxonomies, Shortcodes, etc.

### Why a functionality plugin?
Instead of putting site architecture and management code inside a Theme, use a functionality plugin.
> _"We recommend that you always put custom post types in a plugin rather than a theme. This ensures that the user’s content is portable whenever they change their website’s design."_<br>
&mdash;_[Wordpress.org Plugin Handbook](https://developer.wordpress.org/plugins/custom-post-types-and-taxonomies/registering-custom-post-types/)_

See also: _[Why Custom Post Types Belong in Plugins](http://justintadlock.com/archives/2013/09/14/why-custom-post-types-belong-in-plugins)_ and _[How to Create Your Own WordPress Functionality Plugin](http://wpcandy.com/teaches/how-to-create-a-functionality-plugin)_ for more supporting evidence behind adding additional site functionality via a plugin instead of a theme's `functions.php` file.

PHP Namespacing for dummies: Use `__NAMESPACE__ . '\\` to refer to function names in hooks, etc.:
`add_action('hook_action_name', __NAMESPACE__ . '\\function_name');`

### Setup
Replace prefix '_sitename_' with domain of site.
