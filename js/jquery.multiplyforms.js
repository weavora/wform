(function($){
    $.multiplyForms = function(el, options){
        var self = this;
        self.$el = $(el);
        self.el = el;
        self.formCount = 100;

        self.$el.data("multiplyForms", self);

        self.init = function() {
            self.options = $.extend({},$.multiplyForms.defaultOptions, options);
			$(self.options.template).find('input, textarea, select, button').attr('disabled', 'disabled');
			$(self.options.addLink).click(function(e) {
				e.preventDefault();
				var $newElement = self.$el.find(self.options.template)
					.clone(false)
					.find('input, textarea, select, button')
						.removeAttr('disabled')
					.end()
				.appendTo(self.el).show();
				self._updateIndex($newElement);
				if (typeof(self.options.afterAdd) == 'function') {
				    self.options.afterAdd.call(this, $newElement);
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
			$form.find(self.options.deleteLink).click(function(e) {
				e.preventDefault();
				var $element = $('#'+formId);
				if (typeof(self.options.beforeDelete) == 'function') {
				    self.options.beforeDelete.call(this, $element);
				}
				$element.remove();
			});
			self.formCount++;
        };

        self.init();
    };

    $.multiplyForms.defaultOptions = {
    	addLink: ".add",
    	deleteLink: ".delete",
    	template: "",
    	afterAdd: undefined,
    	beforeDelete: undefined
    };

    $.fn.multiplyForms = function(options) {
        return this.each(function(){
            (new $.multiplyForms(this, options));
        });
    };

})(jQuery);
