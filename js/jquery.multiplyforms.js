(function($){
    $.multiplyForms = function(el, options){
        var self = this;

        self.$el = $(el);
        self.el = el;
        self.formCount = 100;

        self.$el.data("multiplyForms", self);

        self.init = function() {
            self.options = $.extend({},$.multiplyForms.defaultOptions, options);
			$(self.options.initialForm).find('input, textarea, select, button').attr('disabled', 'disabled');
			$(self.options.addLink).click(function(e) {
				e.preventDefault();
				var $newForm = self.$el.find(self.options.initialForm)
					.clone(false)
					.find('input, textarea, select, button')
						.removeAttr('disabled')
					.end()
				.appendTo(self.el).show();
				self._updateIndex($newForm);
				if (typeof(self.options.afterAdd) == 'function') {
				    self.options.afterAdd.call(this, $newForm);
				}
			});
        };

        self._updateIndex = function($form) {
        	var formId = $form.attr('id') + "_" + self.formCount;
        	$form.attr('id', formId);
        	$form.find('*[name*="{index}"]').each(function() {
				this.name = this.name.replace('{index}', self.formCount);
				this.id = this.id.replace('{index}', self.formCount);
			});
			$form.find('.delete').click(function(e) {
				e.preventDefault();
				$('#'+formId).remove();
			});
			self.formCount++;
        };

        // Sample Function, Uncomment to use
        // self.functionName = function(paramaters){
        //
        // };

        // Run initializer
        self.init();
    };

    $.multiplyForms.defaultOptions = {
    	addLink: ".add-link",
    	initialForm: "",
    	afterAdd: undefined
    };

    $.fn.multiplyForms = function(options) {
        return this.each(function(){
            (new $.multiplyForms(this, options));
        });
    };

})(jQuery);
