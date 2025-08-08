/**
 * DigiCommerce Products Filters Block
 */

import DigiCommerceProductsFiltersEdit from './edit';
import DigiCommerceProductsFiltersSave from './save';

const { registerBlockType } = wp.blocks;

registerBlockType('digicommerce/products-filters', {
    icon: {
        src: <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="24" height="24" aria-hidden="true" focusable="false"><path d="M66.4 147.8C71.4 135.8 83.1 128 96 128L544 128C556.9 128 568.6 135.8 573.6 147.8C578.6 159.8 575.8 173.5 566.7 182.7L384 365.3L384 544C384 556.9 376.2 568.6 364.2 573.6C352.2 578.6 338.5 575.8 329.3 566.7L265.3 502.7C259.3 496.7 255.9 488.6 255.9 480.1L256 365.3L73.4 182.6C64.2 173.5 61.5 159.7 66.4 147.8zM544 160L96 160L283.3 347.3C286.3 350.3 288 354.4 288 358.6L288 480L352 544L352 358.6C352 354.4 353.7 350.3 356.7 347.3L544 160z"></path></svg>,
        foreground: '#ccb161'
    },
    edit: DigiCommerceProductsFiltersEdit,
    save: DigiCommerceProductsFiltersSave
});