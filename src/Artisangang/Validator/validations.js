$(function () {
		jQuery.validator.addMethod("accepted", function(value, element) {

		  return value == 'yes' || value == 'on' || value == 'true' || value == '1';
		}, 'This field must be accepted.');


		jQuery.validator.addMethod("alpha", function(value, element) {
		  return this.optional(element) || /^[a-z]+$/i.test(value);
		}, "The field may only contain letters."); 

		jQuery.validator.addMethod("alpha_dash", function(value, element) {
		  return this.optional(element) || /^[a-z0-9\-\s]+$/i.test(value);
		}, "The field may only contain letters, numbers, and dashes."); 


		jQuery.validator.addMethod("alpha_num", function(value, element) {
		  return this.optional(element) || /^[a-z0-9]+$/i.test(value);
		}, "The field may only contain letters and numbers."); 

		jQuery.validator.addMethod("array", function(value, element) {
		  return this.optional(element) || $.isArray(value);
		}, "The field must be an array."); 

		jQuery.validator.addMethod("between", function(value, element, params) {

			if ($(element).attr('type') == 'file') {
				size = (element.files[0].size/1024).toFixed(2);
				return this.optional(element) ||  (size >= params[0] && size <= params[1]); 
			}

			if (/^[0-9]+$/i.test(value)) {
				return this.optional(element) || (value >= params[0] && value <= params[1]);
			}


			if ($.isArray(value) || typeof value == 'string') {

				return this.optional(element) || value.length >= params[0] && value.length <= params[1];
			}

		}, jQuery.validator.format("The field must be between {0} and {1}"));

        jQuery.validator.addMethod("max", function(value, element, params) {

            if ($(element).attr('type') == 'file') {
                size = (element.files[0].size/1024).toFixed(2);
                return this.optional(element) ||  (size <= params[0]);
            }

            if (/^[0-9]+$/i.test(value)) {
                return this.optional(element) || (value <= parseInt(params[0]));
            }


            if ($.isArray(value) || typeof value == 'string') {
                return this.optional(element) || value.length <= params[0];
            }

        }, jQuery.validator.format("The field may not be greater than {0}"));

        jQuery.validator.addMethod("min", function(value, element, params) {


            if ($(element).attr('type') == 'file') {
                size = (element.files[0].size/1024).toFixed(2);
                return this.optional(element) ||  (size >= params[0]);
            }

            if (/^[0-9]+$/i.test(value)) {
                return this.optional(element) || (value >= parseInt(params[0]));
            }


            if ($.isArray(value) || typeof value == 'string') {
                return this.optional(element) || value.length >= params[0];
            }

        }, jQuery.validator.format("The field may be at least {0}"));

        jQuery.validator.addMethod("size", function(value, element, params) {

			if ($(element).attr('type') == 'file') {
				size = (element.files[0].size/1024).toFixed(2);
				return this.optional(element) ||  size == params[0]; 
			}

			if (/^[0-9]+$/i.test(value)) {
				return this.optional(element) || value == parseInt(params[0]);
			}


			if ($.isArray(value) || typeof value == 'String') {
				return this.optional(element) || value.length == params[0];
			}

		}, "The field must be an array."); 

		jQuery.validator.addMethod("boolean", function(value, element) {

		  return this.optional(element) || value == 1 || value == 0 || value == '1' || value == '0' || value == 'true' || value == 'false' || value == true || value == false;
		}, 'This field must be boolean.');

		jQuery.validator.addMethod("in", function(value, element, params) {

		  return this.optional(element) || jQuery.inArray( value, params );
		}, 'This field must be boolean.');

		jQuery.validator.addMethod("required_if", function(value, element, params) {


			if ($(element).attr('type') == 'checkbox') {
				return this.optional(element) || $(params[0]).is(':checked');
			}

			if ($(element).attr('type') == 'select' || $(element).attr('type') == 'radio') {
				return this.optional(element) || $(params[0]).is(':selected');
			}

		  return this.optional(element) || $(params[0]).val() == params[1];
		}, "The field is required."); 
		
	});