# Installation & Configuration of SilverShop Related Products

## Installation
* In a terminal:
`composer require antonythorpe/silvershop-relatedproducts`
* dev/build

A new tab `Related` will appear within each product.

## Templates
In your `{theme}/templates/SilverShop/Page/Layout/Product.ss` add `<% include RelatedProducts %>` under the content.

To customise, copy `vendor/antonythorpe/silvershop-relatedproducts/templates/includes/RelatedProducts.ss` to your `{theme}/templates/includes` folder and adjust as needed.

Optional: in your `{theme}/templates/SilverShop/includes/ProductGroupItem.ss` add
```html
<% if $RelatedTitle %>
    <h3 class="related-title">$RelatedTitle</h3>
<% end_if %>
```
to capture the Related Title against each listed related product.
