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
	$('.site-search').click(function (e) {
		$('#woocommerce-product-search-field-0').toggleClass('active');
	});
	$('#selectScents').click(function( event, variation) {
		$('.overlay-select-scents').addClass('active');
		$('body').css({'overflow':'hidden'});
		$(".woocommerce-product-gallery").flexslider(0);
		$('.product_title').addClass('blur');
		$('.woocommerce-product-details__short-description').addClass('blur');
		$('.btn-select-scent').addClass('blur');
		$( '.variations_form' ).each( function() {
		    $(this).on( 'found_variation', function( event, variation ) {
		        console.log(variation);//all details here
		        var price = variation.display_price;//selectedprice
		        $('.sku_wrapper + .price').html(price);
		        console.log(price);
		    });
		});
	});
	$('.swatch-image').click(function(e) {
		$('.overlay-select-scents').removeClass('active');
		$('.product_title').removeClass('blur');
		$('.woocommerce-product-details__short-description').removeClass('blur');
		$('.btn-select-scent').removeClass('blur');
		$('body').css({'overflow':'scroll'});
		$('.product_title.entry-title + .price').html('');
		// setTimeout(function(){ $('.woocommerce-variation-price').clone().appendTo(".product_title.entry-title + .price"); }, 800);
		
		$('.tagged_as').css({'display':'block'});
		$('.sku_wrapper').css({'display':'block'});
		$('.type-product .price').css({'display':'block'});
		$('.tagged_as').css({'display':'block'});
		
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
// var scrolled = jQuery(window).scrollTop();
// jQuery(window).scroll(function(e) { // เมื่อมีการ scroll
//     var scrolled = jQuery(window).scrollTop();
//     if (scrolled > 100) {

//     } else {
//     }
// });
// window.addEventListener('wheel', function(e) {
//   if (e.deltaY < 0) {
//   	event.preventDefault();
//   	jQuery('.description-single').addClass('active');
//   	jQuery('html, body').stop().animate({
//             scrollTop: 0
//         }, 1000);

//   }
//   if (e.deltaY > 0) {
//     console.log(e.deltaY);
    
//     if(jQuery(".description-single").hasClass( "active" )){
//     	jQuery('html, body').stop().animate({
//             scrollTop: jQuery(".description-single").offset().top
//         }, 1000);
//         jQuery('.description-single').removeClass('active');
//     }else{
    	
//     }
//   }
// });


