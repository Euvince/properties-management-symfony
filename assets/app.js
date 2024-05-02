import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

/* import select2 from 'select2';
$("select[multiple]").select2(); */

let contactButton = $('#contactButton');
contactButton.click(e => {
    e.preventDefault();
    console.log("HELLO WORLD");
    $('#contactForm').slideDown();
    contactButton.slideUp();
})

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
console.log('Hello');
