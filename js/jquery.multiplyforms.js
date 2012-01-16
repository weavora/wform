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

			// find add links inside self.element
			var $addLinks = self.$el.find(self.options.addLink);
			// find add links in document
			$addLinks = $addLinks.length ? $addLinks : $(self.options.addLink);

			$addLinks.click(function(e) {
				e.preventDefault();
				var $newElement = self.$el.find(self.options.template)
					.clone(false)
					.find('input, textarea, select, button')
						.removeAttr('disabled')
					.end()
				.appendTo(self.el).show();
				// if options.template is className then remove class
				if (typeof self.options.template == "string" && self.options.template.indexOf('.') == 0) {
				    $newElement.removeClass(self.options.template.replace('.', ' '));
				}

				self._updateIndex($newElement);
				// afterAdd callback
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
				// beforeDelete callback
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
    	template: ".template",
    	afterAdd: undefined,
    	beforeDelete: undefined
    };

    $.fn.multiplyForms = function(options) {
        return this.each(function(){
            (new $.multiplyForms(this, options));
        });
    };

})(jQuery);
