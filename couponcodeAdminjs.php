<?php //prepare details here  ?>

// Code for WP Quick Selector

var CouponCode_Admin = function() {}

CouponCode_Admin.prototype = {
   options             : {},
   generateShortCode   : function() {
      var attrs = '';
      jQuery.each(this['options'], function(name,value) {
         if (value != '') {
            attrs += ' ' + name + '="' + value + '"';
         }
      });
      return '[coupon' + attrs + ' /]';
   },
   sendToEditor        : function(f) {
      var collection = jQuery(f).find("input[id^=ccode]:not(input:checkbox),input[id^=ccode]:checkbox:checked,select[id^=ccode]");
      var $this = this;
      collection.each(function () {
         var name = this.name.substring(12, this.name.length - 1 );
         $this['options'][name] = this.value;
      });
      send_to_editor(this.generateShortCode());
      return false;
   }
}

var CouponCode_Setup = new CouponCode_Admin();