/**
 * WordPress dependencies.
 */
import { Component, Fragment } from "@wordpress/element";
import { withDispatch, withSelect } from "@wordpress/data"
import { compose } from "@wordpress/compose"
import { __ } from "@wordpress/i18n";
import { PluginSidebarMoreMenuItem, PluginSidebar } from "@wordpress/edit-post";

const cb = 'th-primary-category-sidebar';

class PrimaryCategoryPlugin extends Component {

    constructor() {
        super()
    }

    render() {

        const {
            meta: {
                _th_primary_category_id: currentPrimaryMetaValue,
            } = {},
            categories = [],
            updateMeta,
        } = this.props;

        console.log( categories );

        return (
            <Fragment>
                <PluginSidebarMoreMenuItem
                    name={cb}
                    target={cb}
                    type="sidebar"
                >
                    { __('Primary Category', 'primary-category') }
                </PluginSidebarMoreMenuItem>
                <PluginSidebar
                    name={cb}
                    title={ __('Primary Category', 'primary-category') }
                >
                    <div className={`${cb}`}>
                        <div className={`${cb}__intro`}>Select a Primary Category</div>
                        <select
                            className={`${cb}__select`}
                            onChange={(e) => {
                                updateMeta( { _th_primary_category_id: e.target.value || 0 } );
                            }}
                            value={currentPrimaryMetaValue}
                        >
                            <option value="0">None</option>
                            { categories && categories.length && categories.map( ( category ) => {
                                if ( category.name === "Uncategorized" ) return null
                                return (
                                    <option value={ category.id }>{ category.name }</option>
                                )
                            })}
                        </select>
                    </div>
                </PluginSidebar>

            </Fragment>
        )
    }
}

// Fetch the post meta.
const applyWithSelect = withSelect( ( select ) => {
    const { getEditedPostAttribute } = select( 'core/editor' );
    const { getEntityRecords } = select( 'core' );

    return {
        meta: getEditedPostAttribute( 'meta' ),
        categories: getEntityRecords( 'taxonomy', 'category' )
    };
} );

// Provide method to update post meta.
const applyWithDispatch = withDispatch( ( dispatch, { meta } ) => {
    const { editPost } = dispatch( 'core/editor' );

    return {
        updateMeta( newMeta ) {
            editPost( { meta: { ...meta, ...newMeta } } );
        },
    };
} );

const ConnectedPrimaryCategoryPlugin = compose( [
    applyWithSelect,
    applyWithDispatch
] )( PrimaryCategoryPlugin );

export default ConnectedPrimaryCategoryPlugin;
