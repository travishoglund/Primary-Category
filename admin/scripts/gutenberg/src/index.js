/**
 * WordPress dependencies.
 */
import { registerPlugin } from "@wordpress/plugins";

/**
 * Local dependencies.
 */
import PrimaryCategoryPlugin from './components/PrimaryCategoryPlugin.js';

/**
 * Register the Primary Category Gutenberg plugin.
 */
registerPlugin( 'th-primary-category-sidebar', {
    icon: 'star-filled',
    render: PrimaryCategoryPlugin
});
