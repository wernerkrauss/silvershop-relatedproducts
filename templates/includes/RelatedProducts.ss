<% if $RelatedProducts %>
    <div id="RelatedProducts" class="typography">
    	<h3>Related Products</h3>
    	<ul class="related-products">
    		<% loop $RelatedProducts %>
    			<% include SilverShop\Includes\ProductGroupItem %>
    		<% end_loop %>
    	</ul>
    </div>
<% end_if %>
