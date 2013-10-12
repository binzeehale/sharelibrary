jQuery.validator.setDefaults({
	ignore: [],
	onkeyup: false,
	highlight: function(element) {
		$(element).closest('.control-group').removeClass('success').addClass('error');
	},
	success: function(element) {
		element
			.text('OK!').addClass('valid')
			.closest('.control-group').removeClass('error').addClass('success');
	}
});

jQuery.validator.addMethod("legalString", function(value, element) {
	return this.optional(element) || /^\w+$/.test(value);
}, "请输入字母或数字或下划线");

jQuery.validator.addMethod("legalStringCN", function(value, element) {
	return this.optional(element) || /^([\u4E00-\u9FFF]|\w)+$/.test(value);
}, "请输入字母或数字或下划线或汉字");