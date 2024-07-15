/**
 * WordPress dependencies
 */

import { useBlockProps , BlockControls } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import { Disabled, ToolbarGroup } from '@wordpress/components'
import ServerSideRender from '@wordpress/server-side-render';
import { edit } from '@wordpress/icons';
import './asset/css/editor.scss';

const Edit = () => {

	const blockProps = useBlockProps();
	const toolbarControls = [];

	return (
		<div { ...blockProps }>
			<BlockControls>
				<ToolbarGroup controls={ toolbarControls } />
			</BlockControls>
			<ServerSideRender
				block = "qr-reader/qr"
				className = "wrapper_offers"
			/>
		</div>
	);
};

export default Edit;
