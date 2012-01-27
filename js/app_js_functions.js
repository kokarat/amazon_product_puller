

jQuery(function($) {
  
  var total;
  var current = 1;

  $('.large_image').live('click', function() {
    $.colorbox({
      href: $(this).attr('href'),
      transition: 'elastic'
    });
    return false;
  });

  // user clicks form submit
  $('#amazonpp_submit').click(function() {
    current = 1;
    ajax_amazon($('#category :selected').text(), $('#searchbox').val(), null, 1);
    return false;
  });

  // previous link
  $('.amazonpp_prev').click(function() {
    if(current == 1)
      return;
    else {
      current -= 1;
      ajax_amazon($('#category :selected').text(), $('#searchbox').val(), current);
    }
  });
  
  // next link
  $('.amazonpp_next').click(function() {
    if(current == (total))
      return;
    else {
      current += 1;
      ajax_amazon($('#category :selected').text(), $('#searchbox').val(), current);
    }
  });
  
  // set current to 1 when selectbox value changed
  $('#category').change(function() {
    current = 1;
  });


  // ajax function used from 'submit button' & 'click events'
  function ajax_amazon(cat, search, item, submit) {
    
    if(submit) {
      $('#app_ajax_wheel').css('display', 'block');
    }
    else {
      $('.amazonpp_ajax_loader').css('display', 'block');
    }
    
    
    $.ajax({
      url: amazon_lib.ajaxurl,
      data: {
        action : 'amazon-product-puller',
        amazonNonce : amazon_lib.amazonNonce,
        amazon: 'query',
        category: cat,
        searchterm: search,
        itempage: item != null ? item : '1'
      },
      dataType: 'json',
      success: function(data) {

        total = data.Items.TotalPages;
        totalresults = data.Items.TotalResults;
        container = [];
        if(total > 0 && totalresults > 1) {
          container = $.map(data.Items.Item, function(obj, index) {
            
            // fill necessary object from response
            return {
              link: obj.DetailPageURL,
              title: obj.ItemAttributes ? obj.ItemAttributes.Title : '',
              smallimg: obj.SmallImage ? obj.SmallImage.URL : amazon_lib.no_img,
              largeimg: obj.LargeImage ? obj.LargeImage.URL : '',
              feature: obj.ItemAttributes ? obj.ItemAttributes.Feature : '',
              price: getPrice(obj),
              genre: obj.ItemAttributes ? obj.ItemAttributes.Genre : '',
              product_group: obj.ItemAttributes ? obj.ItemAttributes.ProductGroup : '',
              binding: obj.ItemAttributes ? obj.ItemAttributes.Binding : '',
            }
            
          });
          ul_c = $('<ul id="ul_container">');
          $('#template').tmpl(container).appendTo(ul_c);
          $('.amazonpp_current').text(current);
          $('.amazonpp_total').text(total);
          $('#amazon_content').html(ul_c);
          $('.amazonpp_pager').css('display', 'block');
        }
        else {
          $('.amazonpp_current').text('0');
          $('.amazonpp_total').text('0');
          $('#amazon_content').html('');
          $('.amazonpp_pager').css('display', 'none');
        }
        $('.amazonpp_ajax_loader').css('display', 'none');
        $('#app_ajax_wheel').css('display', 'none');
        $('html, body').animate({scrollTop:0}, 'fast');
        
      }
      
    });
    
  }
  
  function getPrice(ob) {
    
    if('object' == typeof(ob) && null !== ob) {

      if(typeof ob.ItemAttributes == 'object') {
        if("ListPrice" in ob.ItemAttributes)
          return ob.ItemAttributes.ListPrice.FormattedPrice;
      }
        
      else if("OfferSummary" in ob) {
        
        if("LowestNewPrice" in ob.OfferSummary)
          return ob.OfferSummary.LowestNewPrice.FormattedPrice
          
      }
      else if(typeof ob.ItemAttributes == 'object') {
        if ("TradeInValue" in ob.ItemAttributes)
          return ob.ItemAttributes.TradeInValue.FormattedPrice;
      }
        
      else if(typeof ob.ItemAttributes == 'object') {
        if("RunningTime" in ob.ItemAttributes)
          return "Auction in progress";
      }
        
    }
        
    return;
        
  }

});
