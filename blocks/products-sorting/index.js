/**
 * DigiCommerce Products Sorting Block
 */

import DigiCommerceProductsSortingEdit from './edit';
import DigiCommerceProductsSortingSave from './save';

const { registerBlockType } = wp.blocks;

registerBlockType('digicommerce/products-sorting', {
    icon: {
        src: <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="24" height="24" aria-hidden="true" focusable="false"><path d="M267.3 443.3L171.3 539.3C165.1 545.5 154.9 545.5 148.7 539.3L52.7 443.3C46.5 437.1 46.5 426.9 52.7 420.7C58.9 414.5 69.1 414.5 75.3 420.7L144 489.4L144 112C144 103.2 151.2 96 160 96C168.8 96 176 103.2 176 112L176 489.4L244.7 420.7C250.9 414.5 261.1 414.5 267.3 420.7C273.5 426.9 273.5 437.1 267.3 443.3zM336 96L400 96C408.8 96 416 103.2 416 112C416 120.8 408.8 128 400 128L336 128C327.2 128 320 120.8 320 112C320 103.2 327.2 96 336 96zM336 232L464 232C472.8 232 480 239.2 480 248C480 256.8 472.8 264 464 264L336 264C327.2 264 320 256.8 320 248C320 239.2 327.2 232 336 232zM336 376L528 376C536.8 376 544 383.2 544 392C544 400.8 536.8 408 528 408L336 408C327.2 408 320 400.8 320 392C320 383.2 327.2 376 336 376zM336 512L592 512C600.8 512 608 519.2 608 528C608 536.8 600.8 544 592 544L336 544C327.2 544 320 536.8 320 528C320 519.2 327.2 512 336 512z"></path></svg>,
        foreground: '#ccb161'
    },
    edit: DigiCommerceProductsSortingEdit,
    save: DigiCommerceProductsSortingSave
});