/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import edit from './edit';

import './asset/css/style.scss';

const { name } = json;

registerBlockType( name, {
	edit,
} );
