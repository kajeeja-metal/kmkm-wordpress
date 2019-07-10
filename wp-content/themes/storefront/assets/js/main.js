// var user_click = document.getElementById("button-view");
// var user_view = document.getElementById("user_member");
// jQuery(document).ready(function ($) {
// 	var user_view = document.getElementById("user_member");
// 	$( "#button-view" ).click(function() {
// 	    var ourRequest = new XMLHttpRequest();
// 		ourRequest.open('GET', 'http://localhost/kmkm-wordpress/wp-json/wp/v2/users');
// 		ourRequest.onload = function(){
// 			if(ourRequest.status >= 200 && ourRequest.status < 400){
// 				var data = JSON.parse(ourRequest.responseText);
// 				createHTML(data);
// 			} else {
// 				console.log("We connect to the Server But Error");
// 			}
// 		}
// 		ourRequest.onerror = function(){
// 			console.log("Connection error");
// 		}
// 		ourRequest.send();
// 		function createHTML (PostsData) {
// 		var ourHTMLString = '';
// 		for (var i = 0; i < PostsData.length; i++) {
// 			ourHTMLString += '<h2>' + PostsData[i].name + '</h2>';
// 			// ourHTMLString += PostsData[i].content.rendered;
// 			console.log(ourHTMLString);
// 		}
// 		user_view.innerHTML = ourHTMLString;
// 	}
// 	});
// });


// jQuery.ajax({
//     url: 'http://localhost/kmkm-wordpress/wp-json/wp/v2/users',
//     method: 'GET',
//     beforeSend: function ( xhr ) {
//         xhr.setRequestHeader( 'Authorization', 'Basic ' + Base64.encode( 'admin:123456' ) );
//     },
//     success: function( data, txtStatus, xhr ) {
//         console.log( data );
//         console.log( xhr.status );
//     }
// });
// if(user_click){
// 	user_click.addEventListener("click", function() {
		
// 	});
// 	function createHTML (PostsData) {
// 		var ourHTMLString = '';
// 		console.log('asd');
// 		for (var i = 0; i < PostsData.length; i++) {
// 			ourHTMLString += '<h2>' + PostsData[i].title.rendered + '</h2>';
// 			ourHTMLString += PostsData[i].content.rendered;
// 			console.log(ourHTMLString);
// 		}
// 		user_view.innerHTML = ourHTMLString;
// 	}
// }

// jQuery.ajax( {
//  url: Slug_API_Settings.root + 'wp/v2/users/',
//  method: 'POST',
//  beforeSend: function ( xhr ) {
//  xhr.setRequestHeader( 'X-WP-Nonce', Slug_API_Settings.nonce );
//  },
//  data:{
// 	 email: 'someone@somewhere.net',
// 	 username: 'someone',
// 	 password: Math.random().toString(36).substring(7)
//  }
// } ).done( function ( response ) {
//  console.log( response );
// } )



// var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}};

jQuery(document).ready(function($) {
// ---------------------------------ใช้ตัวนี้
 //    var ajax_url = ajax_params.ajax_url;
 //    jQuery.ajax( {
	//  url: Slug_API_Settings.root + 'wp/v2/users/',
	//  method: 'GET',
	//  beforeSend: function ( xhr ) {
	//  xhr.setRequestHeader( 'X-WP-Nonce', Slug_API_Settings.nonce );

	//  },
	// success: function( data, txtStatus, xhr ) {
	// 	createHTML(data);
	// 	function createHTML (PostsData) {
	// 	var ourHTMLString = '';
	// 	for (var i = 0; i < PostsData.length; i++) {
	// 		ourHTMLString = PostsData[i].id ;
	// 		// ourHTMLString += PostsData[i].content.rendered;
	// 		// console.log(ourHTMLString);
	// 		var data = {
	// 	        'action': 'my_action',
	// 	        'user_id': PostsData[i].id
	// 	    };
	// 		$.post(ajax_url, data, function(response) {
	// 			// var data_address = response;
		  		
	// 	  //       var results = JSON.parse(data_address);
	// 	  //       console.log(results);
	// 	        var cur_ques_details = response;
	// 			// console.log(cur_ques_details);
	// 	    });
	// 	}
	//     // console.log( data );
	//     // console.log( xhr.status );

	// }
	// }});

// ----------------------------------------------ปิด

    //  jQuery.ajax( {
	//  url: Slug_API_Settings.root + 'wc/v2/products/',
	//  method: 'GET',
	//  beforeSend: function ( xhr ) {
	//  xhr.setRequestHeader( 'X-WP-Nonce', Slug_API_Settings.nonce );

	//  },
	// success: function( data, txtStatus, xhr ) {
	// 	createHTML(data);
	// 	function createHTML (PostsData) {
	// 	var ourHTMLString = '';
	// 	for (var i = 0; i < PostsData.length; i++) {
	// 		ourHTMLString = PostsData[i].name ;
	// 		// ourHTMLString += PostsData[i].content.rendered;
	// 		console.log(ourHTMLString);
			
	// 	}

	// }
	// }});
	// setTimeout(function(){ 
	// 	$('.woocommerce-notices-wrapper').fadeTo("slow", 0.001, function(){ //fade
 //             $(this).slideUp("slow", function() { //slide up
 //                 $(this).remove(); //then remove from the DOM
 //             });
 //         });
	// }, 3000);
	
	$('.site-search').click(function (e) {
		$('#woocommerce-product-search-field-0').toggleClass('active');
	});

	


	$('.selectScents').click(function( event, variation) {
		$('.overlay-select-scents').addClass('active');
		$('body').css({'overflow':'hidden'});
		$(".woocommerce-product-gallery").flexslider(0);
		$('.product_title').addClass('blur');
		$('.tagged_as').addClass('blur');
		$('.size-main').addClass('blur');
		$('.woocommerce-product-details__short-description').addClass('blur');
		$('.btn-select-scent').addClass('blur');
		$( '.variations_form' ).each( function() {
		    $(this).on( 'found_variation', function( event, variation ) {
		        var price = variation.display_price;//selectedprice
		        $('.price-main').html("<div style='color: #6d6d6d;fonr-size:16px;    letter-spacing: 1px;'>Price :<span style='letter-spacing: 1px; font-size: 20px; color:#000;font-family: HKGrotesk-Bold !important; '> "+price + ' THB</span></div>');
		    });
		});
	});
	$('.swatch-image').click(function(e) {
		$('.woocommerce-variation-add-to-cart .screen-reader-text').html('<span style="padding-right:15px;letter-spacing: 1px;">Quantity : </span>');
		$('.price-main').css({'margin':'30px 0px 0px'});
		$('.overlay-select-scents').removeClass('active');
		$('.product_title').removeClass('blur');
		$('.tagged_as').removeClass('blur');
		$('.size-main').removeClass('blur');
		// $('.header-detail-name').css({'display':'block'});
		$('.woocommerce-product-details__short-description').removeClass('blur');
		$('.btn-select-scent').removeClass('blur');
		$('body').css({'overflow':'auto'});
		$('.product_title.entry-title + .price').html('');
		// setTimeout(function(){ $('.woocommerce-variation-price').clone().appendTo(".product_title.entry-title + .price"); }, 800);
		
		$('.product_meta').css({'display':'block'});
		var img = $(this).find('img').attr('src');
		var details_name = $(this).find('.name-scents').text();
		var details_detailname = $(this).find('.description-scents').text();
		$('.img-scents img').attr("src", img);
		$('.detail-scents-select .name-scents').text(details_name);
		$('.detail-scents-select .description-scents').text(details_detailname);
		$('#selectScents').css({'display':'none'});
		$('.overlay-edit-scents').css({'display':'block'});
		$('.woocommerce-variation-add-to-cart.variations_button').css({'display':'block'});
	});
	
	

    $('.columns-3 > .type-product:first-child').parent().addClass('columns-4');
    $('.columns-3 > .type-product:first-child').parent().prev().css({'display':'none'});
    $('.type-product').parent().next().css({'display':'none'});
    $('.product-category').parent().next().css({'display':'block'});
	var footerHeight = $('footer').outerHeight(true) + $('.related').outerHeight(true);
	$('.woocommerce-product-gallery').affix({
    offset: {
    	top:100,
        bottom: footerHeight
	    }
	});
});
jQuery(window).on('mousewheel', function(event, delta) {
    //console.log(event.deltaX, event.deltaY, event.deltaFactor);
    if(delta > 0) {
    console.log('scroll up');
    } 
    else {
    console.log('scroll down');
    }
});
// var scrolled = jQuery(window).scrollTop();
// jQuery(window).scroll(function(e) { // เมื่อมีการ scroll
//     var scrolled = jQuery(window).scrollTop();
//     if (scrolled > 100) {

//     } else {
//     }
// });
/* See related post at
https://codepen.io/Javarome/post/full-page-sliding
*/
function ScrollHandler(pageId) { 
  var page = jQuery('#' + pageId);
  var pageStart = page.offset().top;
  var pageJump = false;
  function scrollToPage() {
    pageJump = true;
      jQuery('html, body').animate({ 
      scrollTop: pageStart
    }, {
      duration: 1000,
      complete: function() {
        pageJump = false;
      }
    });  
  }
  window.addEventListener('wheel', function(event) {
   var viewStart = jQuery(window).scrollTop();
   if (!pageJump) { 
      var pageHeight = page.height();
      var pageStopPortion = pageHeight / 2;
      var viewHeight = jQuery(window).height();

      var viewEnd = viewStart + viewHeight;
      var pageStartPart = viewEnd - pageStart;
      var pageEndPart = (pageStart + pageHeight) - viewStart;
      
      var canJumpDown = pageStartPart >= 0; 
      var stopJumpDown = pageStartPart > pageStopPortion; 
      
      var canJumpUp = pageEndPart >= 0; 
      var stopJumpUp = pageEndPart > pageStopPortion; 

      var scrollingForward = event.deltaY > 0;
      if (  ( scrollingForward && canJumpDown && !stopJumpDown) 
         || (!scrollingForward && canJumpUp   && !stopJumpUp)) {
        event.preventDefault();
        scrollToPage();
      } 
   } else {
     event.preventDefault();
   }    
  },{passive: false});
}
var idproduct = jQuery('.single-product .site-main').children().attr('id');
new ScrollHandler(idproduct); 
new ScrollHandler('two');
new ScrollHandler('three');
new ScrollHandler('four');


// jQuery(document).mouseleave(function () {
//     console.log('out');
// });


