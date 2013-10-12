jQuery.validator.setDefaults({
  highlight: function(element) {
    $(element).closest('.control-group').addClass('error'); // add the Bootstrap error class to the control group
  },
  success: function(element) {
    $(element).closest('.control-group').removeClass('error'); // remove the Boostrap error class from the control group
  }
});