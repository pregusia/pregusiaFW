
(function($){
	
	$.fn.dataIntMin = function(name, initial){
		if (!initial) initial = 10000;
		var res = { val: initial };
		
		$(this).each(function(){
			var val = parseInt($(this).data(name));
			if (val < res.val) res.val = val;
		});
		
		return parseInt(res.val);
	};

	$.fn.dataIntMax = function(name, initial){
		if (!initial) initial = 0;
		var res = { val: initial };
		
		$(this).each(function(){
			var val = parseInt($(this).data(name));
			if (val > res.val) res.val = val;
		});
		
		return parseInt(res.val);
	};
	
	$.fn.tagName = function() {
		if (this.get(0)) {
			return this.get(0).tagName.toLowerCase();
		} else {
			return '';
		}
	}

	
})(jQuery);
